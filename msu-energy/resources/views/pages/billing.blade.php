@extends('layouts.app')

@section('content')
<section id="billing">
  <h1 class="text-3xl font-bold text-maroon mb-6">Billing Summary</h1>

  {{-- Summary KPIs --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="card text-center">
      <div class="kpi-label">This Month</div>
      <div id="thisMonthKwh" class="kpi">1,245 kWh</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Last Month</div>
      <div id="lastMonthKwh" class="kpi">1,180 kWh</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Total Cost</div>
      <div id="totalCost" class="kpi">₱8,950.00</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Average PF</div>
      <div id="avgPF" class="kpi">0.95</div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="bg-panel p-5 rounded-2xl shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Building</label>
        <select id="billingBuilding" class="input w-full">
          <option value="COE">COE</option>
          <option value="SET">SET</option>
          <option value="CSM">CSM</option>
          <option value="CCS">CCS</option>
          <option value="PRISM">PRISM</option>
        </select>
      </div>

      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Billing Period</label>
        <input type="month" id="billingMonth" class="input w-full">
      </div>

      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Compare To</label>
        <input type="month" id="billingCompare" class="input w-full">
      </div>

      <div class="flex items-end">
        <button id="btnBilling" class="btn bg-maroon text-white w-full py-2 hover:bg-maroon-700 font-bold rounded-xl">
          Generate
        </button>
      </div>
    </div>
  </div>

  {{-- Charts --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card">
      <h2 class="text-lg font-semibold mb-3 text-center text-maroon">Energy Consumption (kWh)</h2>
      <canvas id="billingEnergyChart" height="150"></canvas>
    </div>

    <div class="card">
      <h2 class="text-lg font-semibold mb-3 text-center text-maroon">Cost Comparison (₱)</h2>
      <canvas id="billingCostChart" height="150"></canvas>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      const ctx1 = document.getElementById("billingEnergyChart").getContext("2d");
      const ctx2 = document.getElementById("billingCostChart").getContext("2d");
      let energyChart, costChart;

      function generateBillingCharts() {
        const buildings = ["COE", "SET", "CSM", "CCS", "PRISM"];
        const energy = buildings.map(() => Math.floor(Math.random() * 2000) + 800);
        const cost = buildings.map(kwh => (kwh * 7.5).toFixed(2)); // e.g. ₱7.5/kWh

        // Update KPI cards
        document.getElementById("thisMonthKwh").textContent = energy[0] + " kWh";
        document.getElementById("lastMonthKwh").textContent = Math.round(energy[0] * 0.95) + " kWh";
        document.getElementById("totalCost").textContent = "₱" + cost.reduce((a, b) => parseFloat(a) + parseFloat(b), 0).toFixed(2);
        document.getElementById("avgPF").textContent = (0.9 + Math.random() * 0.1).toFixed(2);

        // Destroy existing charts
        if (energyChart) energyChart.destroy();
        if (costChart) costChart.destroy();

        // Energy chart
        energyChart = new Chart(ctx1, {
          type: "bar",
          data: {
            labels: buildings,
            datasets: [{
              label: "Energy (kWh)",
              data: energy,
              backgroundColor: "#a11d1d99",
              borderColor: "#a11d1d",
              borderWidth: 1,
              borderRadius: 8
            }]
          },
          options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });

        // Cost chart
        costChart = new Chart(ctx2, {
          type: "line",
          data: {
            labels: buildings,
            datasets: [{
              label: "Cost (₱)",
              data: cost,
              borderColor: "#caa15a",
              backgroundColor: "#caa15a44",
              fill: true,
              tension: 0.4
            }]
          },
          options: { responsive: true, scales: { y: { beginAtZero: true } } }
        });
      }

      document.getElementById("btnBilling").addEventListener("click", generateBillingCharts);
      generateBillingCharts(); // initial render
    });
  </script>
</section>
@endsection
