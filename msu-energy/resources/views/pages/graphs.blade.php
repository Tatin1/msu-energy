@extends('layouts.app')

@section('content')
@php
  $graphConfig = [
    'buildings' => ($buildings ?? collect())->map(function ($building) {
      return [
        'id' => $building->id,
        'code' => $building->code,
        'name' => $building->name,
        'meters' => $building->meters->map(function ($meter) {
          return [
            'id' => $meter->id,
            'label' => $meter->label,
            'code' => $meter->meter_code,
          ];
        })->values(),
      ];
    })->values(),
    'parameters' => $parameters ?? [],
    'defaultDate' => now()->toDateString(),
  ];
@endphp
<section id="graphs" class="space-y-8">
  <h1 class="text-3xl font-bold text-maroon">Graphs</h1>

  {{-- Filter Controls --}}
  <div class="card flex flex-wrap gap-4 items-end">
    <label>Date
      <input type="date" class="input" value="{{ date('Y-m-d') }}">
      <input type="date" id="dateInput" class="input" value="{{ $graphConfig['defaultDate'] }}">
    </label>

    <label>Parameter
      <select id="paramSelect" class="input">
        {{-- <option>Total Active Power</option>
        <option>Total Reactive Power</option>
        <option>Total Apparent Power</option>
        <option>Frequency</option>
        <option>THD Voltage</option>
        <option>THD Current</option> --}}
        @foreach($graphConfig['parameters'] as $parameter)
          <option value="{{ $parameter['key'] }}">{{ $parameter['label'] }}</option>
        @endforeach
      </select>
    </label>

    <label>Building
      <select id="buildingSelect" class="input">
        {{-- <option>COE</option>
        <option>CCS</option>
        <option>CSM</option>
        <option>CBAA</option>
        <option>CED</option>
        <option>CON</option> --}}
        @foreach($graphConfig['buildings'] as $building)
          <option value="{{ $building['id'] }}">{{ $building['code'] }} – {{ $building['name'] }}</option>
        @endforeach
      </select>
    </label>

    <label>Meter
      <select id="meterSelect" class="input">
        <option value="" disabled selected>Select a building first</option>
      </select>
    </label>

    <button id="enterBtn" class="btn btn-primary">Enter</button>
    <button class="btn btn-grey no-print" onclick="window.print()">Print</button>
  </div>

  {{-- Chart Display --}}
  <div class="card">
    <div class="flex items-center justify-between mb-2">
      <h3 class="font-bold">Parameter Trends</h3>
      <span id="chartStatus" class="text-xs uppercase tracking-wide text-gray-500"></span>
    </div>
    <canvas id="graphCanvas" height="220"></canvas>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const ctx = document.getElementById("graphCanvas");
    const dateInput = document.getElementById("dateInput");
    const paramSelect = document.getElementById("paramSelect");
    const buildingSelect = document.getElementById("buildingSelect");
    const meterSelect = document.getElementById("meterSelect");
    const enterBtn = document.getElementById("enterBtn");
    const chartStatus = document.getElementById("chartStatus");

    const config = @json($graphConfig);
    const palette = ["#7a0e0e", "#f28c38", "#1f6feb", "#0f915a"];

    const chart = new Chart(ctx, {
      type: "line",
      data: {
        labels: [],
        datasets: [{
          label: "",
          data: [],
          borderColor: palette[0],
          backgroundColor: `${palette[0]}33`,
          fill: true,
          tension: 0.35,
          pointRadius: 3,
        }],
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true },
        },
        plugins: {
          legend: { display: false },
        },
      },
    });

    const state = {
      building: buildingSelect.value,
      meter: null,
      parameter: paramSelect.value,
      date: dateInput.value,
    };

    function updateStatus(message, tone = 'default') {
      const tones = {
        default: 'text-gray-500',
        success: 'text-emerald-600',
        warn: 'text-amber-600',
        error: 'text-red-600',
      };
      chartStatus.className = `text-xs uppercase tracking-wide ${tones[tone] || tones.default}`;
      chartStatus.textContent = message;
    }

    function populateMeters() {
      const building = config.buildings.find((item) => String(item.id) === String(state.building));
      meterSelect.innerHTML = '';

      if (!building || building.meters.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.disabled = true;
        option.selected = true;
        option.textContent = 'No meters linked';
        meterSelect.appendChild(option);
        state.meter = null;
        return;
      }

      building.meters.forEach((meter, index) => {
        const option = document.createElement('option');
        option.value = meter.id;
        option.textContent = meter.label ? `${meter.label} (${meter.code})` : meter.code;
        if (index === 0 || String(meter.id) === String(state.meter)) {
          option.selected = true;
          state.meter = meter.id;
        }
        meterSelect.appendChild(option);
      });
    }

    async function fetchDataset() {
      if (!state.meter) {
        updateStatus('Select a meter to load data', 'warn');
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.update();
        return;
      }

      updateStatus('Loading…');
      const endpoint = `/api/meters/${state.meter}/daily/${state.parameter}/${state.date}`;

      try {
        const response = await fetch(endpoint);
        if (!response.ok) {
          throw new Error(await response.text());
        }
        const payload = await response.json();

        const labels = payload.labels ?? [];
        const values = payload.values ?? [];

        chart.data.labels = labels;
        chart.data.datasets[0].data = values;
        chart.data.datasets[0].label = payload.parameterLabel || 'Parameter Trend';
        chart.update();

        if (!values.length) {
          updateStatus('No readings for the selected date', 'warn');
        } else {
          updateStatus(`Loaded ${values.length} points`, 'success');
        }
      } catch (error) {
        console.error(error);
        chart.data.labels = [];
        chart.data.datasets[0].data = [];
        chart.update();
        updateStatus('Failed to load data', 'error');
      }
    }

    function handleBuildingChange(evt) {
      state.building = evt.target.value;
      populateMeters();
    }

    function handleMeterChange(evt) {
      state.meter = evt.target.value;
    }

    function handleParamChange(evt) {
      state.parameter = evt.target.value;
    }

    function handleDateChange(evt) {
      state.date = evt.target.value;
    }

    buildingSelect.addEventListener('change', handleBuildingChange);
    meterSelect.addEventListener('change', handleMeterChange);
    paramSelect.addEventListener('change', handleParamChange);
    dateInput.addEventListener('change', handleDateChange);
    enterBtn.addEventListener('click', fetchDataset);

    populateMeters();
    fetchDataset();

    window.graphsPage = window.graphsPage || {};
    window.graphsPage.refresh = () => {
      fetchDataset();
    };
  });
  </script>
</section>
@endsection

{{-- <script>
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
  </script> --}}