@extends('layouts.app')

@section('content')
<section id="graphs" class="space-y-8">
  <h1 class="text-3xl font-bold text-maroon">Graphs</h1>

  {{-- Filter Controls --}}
  <div class="card flex flex-wrap gap-4 items-end">
    <label>Date
      <input type="date" class="input" value="{{ date('Y-m-d') }}">
    </label>

    <label>Parameter
      <select id="paramSelect" class="input">
        <option>Total Active Power</option>
        <option>Total Reactive Power</option>
        <option>Total Apparent Power</option>
        <option>Frequency</option>
        <option>THD Voltage</option>
        <option>THD Current</option>
      </select>
    </label>

    <label>College / Meter
      <select id="meterSelect" class="input">
        <option>COE</option>
        <option>CCS</option>
        <option>CSM</option>
        <option>CBAA</option>
        <option>CED</option>
        <option>CON</option>
      </select>
    </label>

    <button id="enterBtn" class="btn btn-primary">Enter</button>
    <button class="btn btn-grey no-print" onclick="window.print()">Print</button>
  </div>

  {{-- Chart Display --}}
  <div class="card">
    <h3 class="font-bold mb-2">Parameter Trends</h3>
    <canvas id="graphCanvas" height="180"></canvas>
  </div>

  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("graphCanvas");

    // 🧩 Dummy datasets per college and parameter
    const dummyData = {
      "COE": {
        "Total Active Power": [140, 145, 160, 180, 170, 165, 160],
        "Total Reactive Power": [50, 55, 52, 56, 60, 57, 54],
        "Total Apparent Power": [160, 165, 170, 180, 190, 185, 175],
        "Frequency": [59.8, 59.9, 60.1, 60.0, 60.2, 60.0, 60.1],
        "THD Voltage": [2.5, 2.7, 2.8, 3.0, 2.9, 3.1, 2.8],
        "THD Current": [4.0, 4.2, 4.3, 4.4, 4.5, 4.6, 4.7],
      },
      "CCS": {
        "Total Active Power": [120, 130, 135, 150, 145, 140, 138],
        "Total Reactive Power": [30, 32, 35, 34, 33, 32, 31],
        "Total Apparent Power": [130, 135, 140, 145, 150, 148, 143],
        "Frequency": [59.9, 60.0, 60.1, 59.9, 60.0, 59.8, 59.9],
        "THD Voltage": [2.2, 2.4, 2.3, 2.5, 2.4, 2.6, 2.5],
        "THD Current": [4.1, 4.3, 4.2, 4.4, 4.3, 4.5, 4.4],
      },
      "CSM": {
        "Total Active Power": [110, 115, 118, 125, 120, 118, 122],
        "Total Reactive Power": [25, 28, 27, 29, 30, 31, 30],
        "Total Apparent Power": [115, 120, 125, 130, 128, 126, 124],
        "Frequency": [60.0, 60.1, 59.9, 60.2, 59.8, 60.0, 60.1],
        "THD Voltage": [2.8, 2.9, 3.0, 3.1, 3.0, 3.2, 3.1],
        "THD Current": [3.9, 4.0, 4.1, 4.0, 4.2, 4.1, 4.3],
      },
      "CBAA": {
        "Total Active Power": [100, 105, 110, 120, 115, 110, 108],
        "Total Reactive Power": [28, 30, 32, 34, 33, 31, 30],
        "Total Apparent Power": [110, 115, 120, 125, 123, 122, 120],
        "Frequency": [60.0, 60.0, 59.9, 60.1, 60.2, 60.0, 60.0],
        "THD Voltage": [2.6, 2.7, 2.8, 2.9, 3.0, 2.9, 2.8],
        "THD Current": [4.0, 4.1, 4.2, 4.1, 4.3, 4.4, 4.2],
      },
      "CED": {
        "Total Active Power": [90, 95, 98, 105, 100, 98, 102],
        "Total Reactive Power": [22, 24, 23, 25, 26, 24, 25],
        "Total Apparent Power": [100, 105, 110, 112, 114, 113, 110],
        "Frequency": [59.9, 60.1, 60.2, 59.8, 60.0, 59.9, 60.1],
        "THD Voltage": [3.0, 3.1, 3.2, 3.1, 3.0, 3.2, 3.1],
        "THD Current": [4.3, 4.2, 4.4, 4.3, 4.5, 4.6, 4.5],
      },
      "CON": {
        "Total Active Power": [80, 82, 84, 90, 88, 86, 85],
        "Total Reactive Power": [20, 22, 23, 24, 25, 23, 22],
        "Total Apparent Power": [90, 92, 95, 98, 97, 96, 94],
        "Frequency": [60.0, 59.9, 59.8, 60.1, 60.0, 60.2, 59.9],
        "THD Voltage": [2.7, 2.8, 2.9, 2.8, 2.9, 3.0, 3.0],
        "THD Current": [4.0, 4.1, 4.0, 4.2, 4.1, 4.3, 4.2],
      },
    };

    // Initial chart setup
    const chart = new Chart(ctx, {
      type: "line",
      data: {
        labels: ["8 AM", "9 AM", "10 AM", "11 AM", "12 PM", "1 PM", "2 PM"],
        datasets: [{
          label: "Total Active Power (COE)",
          data: dummyData["COE"]["Total Active Power"],
          borderColor: "#7a0e0e",
          backgroundColor: "#7a0e0e33",
          fill: true,
          tension: 0.3,
        }],
      },
      options: { responsive: true, scales: { y: { beginAtZero: true } } },
    });

    // 🎛 Dropdown listeners
    const paramSelect = document.getElementById("paramSelect");
    const meterSelect = document.getElementById("meterSelect");
    const enterBtn = document.getElementById("enterBtn");

    function updateChart() {
      const parameter = paramSelect.value;
      const meter = meterSelect.value;
      chart.data.datasets[0].label = `${parameter} (${meter})`;
      chart.data.datasets[0].data = dummyData[meter][parameter];
      chart.update();
    }

    paramSelect.addEventListener("change", updateChart);
    meterSelect.addEventListener("change", updateChart);
    enterBtn.addEventListener("click", updateChart);
  });
  </script>
</section>
@endsection
