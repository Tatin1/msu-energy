@extends('layouts.app')

@section('content')
<section id="billing">
  <h1 class="text-3xl font-bold text-maroon mb-6">Billing Summary</h1>

  {{-- Summary KPIs --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="card text-center">
      <div class="kpi-label">This Month</div>
      <div id="thisMonthkW" class="kpi">0 kW</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Previous Month</div>
      <div id="previousMonthkW" class="kpi">0 kW</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Total Cost</div>
      <div id="totalCost" class="kpi">₱0.00</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Average PF</div>
      <div id="avgPF" class="kpi">0.00</div>
    </div>
  </div>

  {{-- Filters --}}
  <div class="bg-panel p-5 rounded-2xl shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

      {{-- Building Selector --}}
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Building</label>
        <select id="billingBuilding" class="input w-full">
          <option value="COE">COE</option>
          <option value="SET">SET</option>
          <option value="CSM">CSM</option>
          <option value="CCS">CCS</option>
          <option value="PRISM">PRISM</option>
          <option value="CED">CED</option>
          <option value="CHR">CHR</option>
          <option value="HOSTEL">HOSTEL</option>
        </select>
      </div>

      {{-- Start Date --}}
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Start Date</label>
        <input type="date" id="billingStart" class="input w-full">
      </div>

      {{-- End Date --}}
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">End Date</label>
        <input type="date" id="billingEnd" class="input w-full">
      </div>

      <div class="flex items-end">
        <button id="btnBilling"
          class="btn bg-maroon text-white w-full py-2 hover:bg-maroon-700 font-bold rounded-xl">
          Generate
        </button>
      </div>
    </div>
  </div>

  {{-- Charts --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Energy Consumption Chart --}}
    <div class="card">
      <h2 class="text-lg font-semibold mb-3 text-center text-maroon">Energy Consumption (kWh)</h2>
      <canvas id="billingEnergyChart" height="150"></canvas>
    </div>

    {{-- Previous Month KW Chart --}}
    <div class="card">
      <h2 class="text-lg font-semibold mb-3 text-center text-maroon">Previous Month Consumption (kWh)</h2>
      <canvas id="previousMonthChart" height="150"></canvas>
    </div>

  </div>

  {{-- ALL BUILDINGS TOTAL KWH LINE GRAPH --}}
  <div class="card mt-6">
    <h2 class="text-lg font-semibold mb-3 text-center text-maroon">Total KWh Consumption Trend (All Buildings)</h2>
    <canvas id="totalKwhTrend" height="150"></canvas>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {

      const ctxEnergy = document.getElementById("billingEnergyChart").getContext("2d");
      const ctxPrev = document.getElementById("previousMonthChart").getContext("2d");
      const ctxTrend = document.getElementById("totalKwhTrend").getContext("2d");

      let energyChart, previousChart, trendChart;

      function generateBilling() {

        const buildings = ["COE", "SET", "CSM", "CCS", "PRISM", "CED", "CHR", "HOSTEL"];

        const randomKwh = () => Math.floor(Math.random() * 2000) + 500;

        const selectedBuilding = document.getElementById("billingBuilding").value;

        // Random values for charts
        const thisMonth = randomKwh();
        const prevMonth = Math.floor(thisMonth * 0.92);
        const buildingTotals = buildings.map(() => randomKwh());

        // KPI Updates
        document.getElementById("thisMonthkW").textContent = thisMonth + " kWh";
        document.getElementById("previousMonthkW").textContent = prevMonth + " kWh";
        document.getElementById("totalCost").textContent = "₱" + (thisMonth * 7.5).toFixed(2);
        document.getElementById("avgPF").textContent = (0.90 + Math.random() * 0.10).toFixed(2);

        // Destroy existing charts
        if (energyChart) energyChart.destroy();
        if (previousChart) previousChart.destroy();
        if (trendChart) trendChart.destroy();

        // Energy Chart (for building)
        energyChart = new Chart(ctxEnergy, {
          type: "bar",
          data: {
            labels: ["Energy Consumption"],
            datasets: [{
              label: selectedBuilding + " (kWh)",
              data: [thisMonth],
              backgroundColor: "#a11d1d99",
              borderColor: "#a11d1d",
              borderWidth: 1,
              borderRadius: 8
            }]
          }
        });

        // Previous Month Chart
        previousChart = new Chart(ctxPrev, {
          type: "bar",
          data: {
            labels: ["Previous Month"],
            datasets: [{
              label: "kWh",
              data: [prevMonth],
              backgroundColor: "#caa15a99",
              borderColor: "#caa15a",
              borderRadius: 8
            }]
          }
        });

        // LINE GRAPH FOR ALL BUILDINGS
        trendChart = new Chart(ctxTrend, {
          type: "line",
          data: {
            labels: buildings,
            datasets: [{
              label: "Total kWh",
              data: buildingTotals,
              borderColor: "#a11d1d",
              backgroundColor: "#a11d1d44",
              tension: 0.4,
              fill: true
            }]
          }
        });

      }

      document.getElementById("btnBilling").addEventListener("click", generateBilling);
      generateBilling();

    });
  </script>

</section>
@endsection
