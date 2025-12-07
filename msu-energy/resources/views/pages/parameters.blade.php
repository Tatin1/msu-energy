@extends('layouts.app')

@section('content')
<section id="parameters">
  <h1 class="text-3xl font-bold text-maroon mb-6">Electrical Parameters</h1>

  {{-- Controls --}}
  <div class="bg-panel p-4 rounded-2xl shadow mb-6">
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Building</label>
        <select id="paramBuilding" class="input border-gray-400 rounded-lg px-3 py-2">
          <option value="CCS">CCS</option>
          <option value="CPI">CPI</option>
          <option value="OMDH">OMDH</option>
          <option value="OBA">OBA</option>
          <option value="GYMNASIUM">Gymnasium</option>
          <option value="CSM">CSM</option>
          <option value="KTTO">KTTO</option>
          <option value="ADMINISTRATIVE">Administrative</option>
          <option value="REGISTRAR">Registrar</option>
          <option value="IDS">IDS</option>
          <option value="MAIN LIBRARY">Main Library</option>
          <option value="SID">SID</option>
          <option value="COE OLD BUILDING COMPLEX">COE Old Building Complex</option>
          <option value="KASAMA">KASAMA</option>
          <option value="CASS">CASS</option>
          <option value="CEBA">CEBA</option>
          <option value="SET">SET</option>
          <option value="COE">COE</option>
          <option value="CED">CED</option>
          <option value="GRADUATE DORM">Graduate Dorm</option>
          <option value="PRISM">PRISM</option>
          <option value="IPDM">IPDM</option>
          <option value="BAHAY ALUMNI">Bahay Alumni</option>
        </select>
      </div>

      <button id="btnGetData"
        class="btn bg-maroon text-white font-bold px-5 py-2 rounded-xl hover:bg-maroon-700">
        Refresh
      </button>
    </div>
  </div>

  {{-- Layout Grid --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Parameters Table --}}
    <div class="card bg-panel p-5 rounded-2xl shadow">
      <h2 class="text-xl font-semibold mb-4">Transformer 3φ Readings</h2>
      <table id="paramTable" class="w-full border-collapse text-sm">
        <thead class="bg-gray-100 font-semibold">
          <tr>
            <th>Metric</th>
            <th>Value</th>
            <th>Unit</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    {{-- Energy Consumption Chart --}}
    <div class="card bg-panel p-5 rounded-2xl shadow">
      <h2 class="text-xl font-semibold mb-4">Energy Consumption (kW)</h2>
      <canvas id="paramEnergyChart" height="180"></canvas>

      <div class="grid grid-cols-2 gap-4 mt-6">
  <div>
    <span class="text-sm">Last Month (kW)</span>
    <div id="paramLastMonth" class="font-bold text-lg">0</div>
  </div>
  <div>
    <span class="text-sm">This Month (kW)</span>
    <div id="paramThisMonth" class="font-bold text-lg">0</div>
  </div>
</div>

    </div>
  </div>

  {{-- Script --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const paramTableBody = document.querySelector("#paramTable tbody");
      const ctx = document.getElementById("paramEnergyChart").getContext("2d");
      let chart;

      function renderParameters() {
        // Simulated transformer readings
        const volts = [230 + Math.random() * 5, 231 + Math.random() * 5, 229 + Math.random() * 5];
        const amps = [30 + Math.random() * 5, 28 + Math.random() * 5, 29 + Math.random() * 5];
        const pfL = [0.95 + Math.random() * 0.03, 0.93 + Math.random() * 0.04, 0.94 + Math.random() * 0.03];
        const THDv = (2 + Math.random() * 1.5).toFixed(2);
        const THDi = (3 + Math.random() * 2).toFixed(2);
        const freq = (59.8 + Math.random() * 0.5).toFixed(2);
        const pf = (0.86 + Math.random() * 0.12).toFixed(3);

        const S3 = volts.reduce((s, v, i) => s + v * amps[i], 0) / 1000;
        const P3 = S3 * pf;
        const Q3 = Math.sqrt(Math.max(0, S3 * S3 - P3 * P3));

        const rows = [
          ["Frequency", freq, "Hz"],
          ["Phase Voltages (V1,V2,V3)", volts.map(v => v.toFixed(1)).join(", "), "V"],
          ["Line Currents (A1,A2,A3)", amps.map(a => a.toFixed(1)).join(", "), "A"],
          ["Line PFs (PF1,PF2,PF3)", pfL.map(x => x.toFixed(3)).join(", "), "—"],
          ["3φ Active Power", P3.toFixed(2), "kW"],
          ["3φ Reactive Power", Q3.toFixed(2), "kVAr"],
          ["3φ Apparent Power", S3.toFixed(2), "kVA"],
          ["3φ Power Factor", pf, "—"],
          ["THD (Voltage)", THDv, "%"],
          ["THD (Current)", THDi, "%"]
        ];

        paramTableBody.innerHTML = rows.map(r =>
          `<tr><td>${r[0]}</td><td class='font-bold'>${r[1]}</td><td>${r[2]}</td></tr>`
        ).join('');

        // Dummy energy consumption data
        const hours = Array.from({ length: 24 }, (_, i) => i + 1 + " hr");
        const lastMonthData = Array.from({ length: 24 }, () => Math.floor(50 + Math.random() * 20));
        const thisMonthData = Array.from({ length: 24 }, () => Math.floor(40 + Math.random() * 25));

        // Update KPI values
        document.getElementById("paramLastMonth").textContent = lastMonthData.reduce((a,b)=>a+b,0);
        document.getElementById("paramThisMonth").textContent = thisMonthData.reduce((a,b)=>a+b,0);

        // Destroy previous chart if exists
        if (chart) chart.destroy();

        chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: hours,
            datasets: [
              {
                label: 'Last Month',
                data: lastMonthData,
                fill: true,
                backgroundColor: 'rgba(161, 29, 29, 0.4)',
                borderColor: '#a11d1d',
                tension: 0.3,
                stack: 'Stack 0'
              },
              {
                label: 'This Month',
                data: thisMonthData,
                fill: true,
                backgroundColor: 'rgba(29, 161, 29, 0.4)',
                borderColor: '#1da11d',
                tension: 0.3,
                stack: 'Stack 0'
              }
            ]
          },
          options: {
            responsive: true,
            scales: {
              x: { title: { display: true, text: 'Time (hr)' } },
              y: {
                beginAtZero: true,
                stacked: true,
                title: { display: true, text: 'kW' }
              }
            },
            plugins: {
              tooltip: { mode: 'index', intersect: false },
              legend: { position: 'top' }
            }
          }
        });
      }

      document.getElementById("btnGetData").addEventListener("click", renderParameters);
      renderParameters();
    });
  </script>
</section>
@endsection
