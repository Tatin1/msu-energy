import './bootstrap';
import 'chart.js/auto';
import imageMapResize from 'image-map-resizer';
import { initRealtimeConsumers } from './realtime';

// app.js — simplified, performs fetch to backend API and populates UI
const fmtPeso = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', maximumFractionDigits: 2 });
const fmtInt = new Intl.NumberFormat('en-US');

const clockEl = document.getElementById('clock');
setInterval(()=> clockEl.textContent = new Date().toLocaleString(undefined,{weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit',second:'2-digit'}),1000);

async function fetchDashboard() {
  try {
    const res = await fetch('/api/dashboard');
    if(!res.ok) throw new Error('Network');
    const data = await res.json();
    document.getElementById('kpiTotalKW').textContent = Math.round(data.totalKW);
    document.getElementById('kpiPF').textContent = Number(data.avgPF).toFixed(4);
    document.getElementById('kpiLastMonth').textContent = fmtInt.format(data.lastMonth || 0);
    document.getElementById('kpiThisMonth').textContent = fmtInt.format(data.thisMonth || 0);

    // Bar chart
    const labels = data.buildings.map(b => b.code);
    const values = data.buildings.map(b => {
        // compute placeholder: use each building's billing.this_month_kwh if available else 0
        return (b.billing?.this_month_kwh ?? 0) / 1;
    });
    if(window.barChart) window.barChart.destroy();
    const ctx = document.getElementById('barBuildings');
    window.barChart = new Chart(ctx, {
      type:'bar',
      data:{ labels, datasets:[{ label:'kWh', data: values, backgroundColor: labels.map(()=> '#a11d1d') }] },
      options:{
        onClick:(_,elements)=>{ if(!elements?.length) return; const idx=elements[0].index; const code=labels[idx]; document.getElementById('paramBuilding').value = code; document.getElementById('billBuilding').value = code; openParameters(code); },
        plugins:{legend:{display:false}},
        scales:{ y:{ beginAtZero:true, title:{display:true, text:'Kilowatt-Hour (kWh)'}}}
      }
    });

  } catch(e){ console.error('Dashboard load failed',e); }
}

async function openParameters(buildingCode){
  // populate meter select and render parameters for first meter
  const res = await fetch(`/api/buildings/${buildingCode}/parameters`);
  if(!res.ok) return;
  const b = await res.json();
  const selB = document.getElementById('paramBuilding');
  const selM = document.getElementById('paramMeter');
  selB.innerHTML = `<option value="${b.code}">${b.name}</option>`;
  selM.innerHTML = b.meters.map(m => `<option value="${m.id}">${m.label}</option>`).join('');
  renderParameters();
}

async function renderParameters(){
  const bcode = document.getElementById('paramBuilding').value;
  const meterId = document.getElementById('paramMeter').value;
  if(!meterId) return;
  const res = await fetch(`/api/meters/${meterId}/readings`);
  const rows = await res.json();
  if(!rows.length) return;
  const latest = rows[0];
  // table rows
  const tbody = document.querySelector('#paramTable tbody');
  const S3 = ((latest.voltage1||230) * (latest.current1||28) + (latest.voltage2||231)*(latest.current2||29) + (latest.voltage3||229)*(latest.current3||30))/1000;
  const P3 = (latest.active_power || S3 * (latest.power_factor || 0.95));
  const Q3 = Math.sqrt(Math.max(0, S3*S3 - P3*P3));
  const rowsData = [
    ['Frequency', (latest.frequency ?? 60).toFixed(2), 'Hz'],
    ['Phase Voltages (V1, V2, V3)', `${(latest.voltage1||230).toFixed(1)}, ${(latest.voltage2||231).toFixed(1)}, ${(latest.voltage3||229).toFixed(1)}`, 'V'],
    ['Line Currents (A1, A2, A3)', `${(latest.current1||28).toFixed(1)}, ${(latest.current2||29).toFixed(1)}, ${(latest.current3||30).toFixed(1)}`, 'A'],
    ['Power Factor', (latest.power_factor||0.95).toFixed(3), '—'],
    ['Transformer 3φ Active Power', P3.toFixed(2), 'kW'],
    ['Transformer 3φ Reactive Power', Q3.toFixed(2), 'kVAr'],
    ['Transformer 3φ Apparent Power', S3.toFixed(2), 'kVA'],
    ['THD (Voltage)', ((latest.thd_voltage)||2.5).toFixed(2), '%'],
    ['THD (Current)', ((latest.thd_current)||3.4).toFixed(2), '%']
  ];
  tbody.innerHTML = rowsData.map(r=>`<tr><td>${r[0]}</td><td><strong>${r[1]}</strong></td><td>${r[2]}</td></tr>`).join('');
  // chart of recent kWh values
  const data = rows.slice(0,12).reverse().map(r=>Number(r.kwh || 0));
  const ctx = document.getElementById('paramEnergyChart');
  if(window.paramEnergyChart) window.paramEnergyChart.destroy();
  window.paramEnergyChart = new Chart(ctx,{ type:'line', data:{ labels:data.map((_,i)=>i+1), datasets:[{ label: `Meter ${meterId}`, data, fill:true, tension:.3, backgroundColor:'#a11d1d22', borderColor:'#a11d1d' }]}, options:{scales:{y:{beginAtZero:true}}}});
  // set KPIs
  document.getElementById('paramLastMonth').textContent = fmtInt.format(latest.kwh || 0);
  document.getElementById('paramThisMonth').textContent = fmtInt.format(latest.kwh || 0);
}

document.getElementById('btnGetData')?.addEventListener('click', renderParameters);

// Billing
async function recalcBills(){
  const res = await fetch('/api/billing');
  const data = await res.json();
  document.getElementById('pesoPerKwh').value = data.rate;
  const building = document.getElementById('billBuilding').value;
  const found = data.bills.find(b => b.code === building) ?? data.bills[0];
  if(found){
      document.getElementById('billPrev').textContent = fmtPeso.format(found.lastMonth * data.rate);
      document.getElementById('billThis').textContent = fmtPeso.format(found.thisMonth * data.rate);
  }
  const total = data.bills.reduce((s,b)=>s + (b.thisMonth * data.rate),0);
  document.getElementById('billTotal').textContent = fmtPeso.format(total);
  // chart
  const ctx = document.getElementById('billChart');
  if(window.billChart) window.billChart.destroy();
  window.billChart = new Chart(ctx,{ type:'bar', data:{ labels: data.bills.map(b=>b.code), datasets:[{ label:'This Month Bill', data: data.bills.map(b=>b.thisMonth*data.rate), backgroundColor:'#7a0e0e' }]}, options:{scales:{y:{beginAtZero:true}}}});
}
document.getElementById('btnRecalc')?.addEventListener('click', recalcBills);
// Auto-resize campus map
window.addEventListener('load', () => {
  imageMapResize();
});
// fill initial selects and run
async function populateSelects(){
  const res = await fetch('/api/buildings');
  const list = await res.json();
  const billSel = document.getElementById('billBuilding');
  const paramSel = document.getElementById('paramBuilding');
  const histSel = document.getElementById('histMeterBldg');
  billSel.innerHTML = list.map(b=>`<option value="${b.code}">${b.name}</option>`).join('');
  paramSel.innerHTML = billSel.innerHTML;
  histSel.innerHTML = billSel.innerHTML;
  // set defaults
  if(list[0]) { document.getElementById('billBuilding').value = list[0].code; document.getElementById('paramBuilding').value = list[0].code; openParameters(list[0].code); }
  await fetchDashboard();
  await recalcBills();
}
populateSelects();

document.addEventListener('DOMContentLoaded', () => {
  initRealtimeConsumers();
});
