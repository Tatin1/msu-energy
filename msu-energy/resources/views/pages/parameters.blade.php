@extends('layouts.app')

@section('content')
<section id="parameters">
  <h1 class="text-3xl font-bold text-maroon mb-6">Electrical Parameters</h1>

  {{-- Controls --}}
  <div class="bg-panel p-4 rounded-2xl shadow mb-6">
    <div class="flex flex-wrap gap-4">
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Building</label>
        <select id="paramBuilding" class="input border-gray-400 rounded-lg px-3 py-2">
          <option value="COE">COE</option>
          <option value="SET">SET</option>
          <option value="CSM">CSM</option>
          <option value="CCS">CCS</option>
          <option value="PRISM">PRISM</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Meter #</label>
        <select id="paramMeter" class="input border-gray-400 rounded-lg px-3 py-2">
          <option value="1">Meter 1</option>
          <option value="2">Meter 2</option>
        </select>
      </div>
      <button id="btnGetData"
        class="btn bg-maroon text-white font-bold px-5 py-2 rounded-xl hover:bg-maroon-700">
        Get Data
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
      <h2 class="text-xl font-semibold mb-4">Energy Consumption (kWh)</h2>
      <canvas id="paramEnergyChart" height="180"></canvas>

      <div class="grid grid-cols-2 gap-4 mt-6">
        <x-kpi title="Last Month (kWh)" value="0" id="paramLastMonth" />
        <x-kpi title="This Month (kWh)" value="0" id="paramThisMonth" />
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
        // Simulated demo values (replace with real data if available)
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

        // Simulate Energy Chart
        const data = Array.from({ length: 12 }, () => Math.round(100 + Math.random() * 30));
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: Array.from({ length: data.length }, (_, i) => i + 1),
            datasets: [{
              label: "kWh",
              data,
              fill: true,
              tension: .3,
              borderColor: "#a11d1d",
              backgroundColor: "#a11d1d22"
            }]
          },
          options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        document.getElementById("paramLastMonth").textContent = Math.round(30000 + Math.random() * 5000).toLocaleString();
        document.getElementById("paramThisMonth").textContent = Math.round(25000 + Math.random() * 5000).toLocaleString();
      }

      document.getElementById("btnGetData").addEventListener("click", renderParameters);
      renderParameters();
    });
  </script>
</section>
@endsection
