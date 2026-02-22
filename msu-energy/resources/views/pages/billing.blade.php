@extends('layouts.app')

@section('content')
@php
  $today = now()->timezone(config('app.timezone'));
  $defaultStart = $today->copy()->startOfMonth()->toDateString();
  $defaultEnd = $today->toDateString();
@endphp
<section id="billing">
  <h1 class="text-3xl font-bold text-maroon mb-6">Billing Summary</h1>

  {{-- Summary KPIs --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="card text-center">
      <div class="kpi-label">This Month</div>
      <div id="thisMonthkW" class="kpi">{{ number_format($summary['this_month_kwh'] ?? 0, 0) }} kWh</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Previous Month</div>
      <div id="previousMonthkW" class="kpi">{{ number_format($summary['last_month_kwh'] ?? 0, 0) }} kWh</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Total Cost</div>
      <div id="totalCost" class="kpi">₱{{ number_format($summary['total_cost'] ?? 0, 2) }}</div>
    </div>

    <div class="card text-center">
      <div class="kpi-label">Average PF</div>
      <div id="avgPF" class="kpi">{{ number_format($summary['avg_pf'] ?? 0, 3) }}</div>
    </div>
    <p id="billingStatus" class="text-xs text-gray-500 mt-3">
      Showing aggregate billing for the current month.
    </p>
  </div>

  {{-- Filters --}}
  <div class="bg-panel p-5 rounded-2xl shadow mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

      {{-- Building Selector --}}
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Building</label>
        <select id="billingBuilding" class="input w-full">
          {{-- <option value="COE">COE</option>
          <option value="SET">SET</option>
          <option value="CSM">CSM</option>
          <option value="CCS">CCS</option>
          <option value="PRISM">PRISM</option>
          <option value="CED">CED</option>
          <option value="CHR">CHR</option>
          <option value="HOSTEL">HOSTEL</option> --}}
          <option value="">All Buildings</option>
          @foreach($buildings as $building)
            <option value="{{ $building['id'] }}">{{ $building['code'] }} – {{ $building['name'] }}</option>
          @endforeach
        </select>
      </div>

      {{-- Start Date --}}
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Start Date</label>
        <input type="date" id="billingStart" class="input w-full" value="{{ $defaultStart }}">
      </div>

      {{-- End Date --}}
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">End Date</label>
        <input type="date" id="billingEnd" class="input w-full" value="{{ $defaultEnd }}">
      </div>

      <div class="flex items-end gap-2">
        <button id="btnBilling"
          class="btn bg-maroon text-white w-full py-2 hover:bg-maroon-700 font-bold rounded-xl">
          Generate
        </button>
        <button id="btnBillingSave"
          class="btn bg-gray-800 text-white w-full py-2 hover:bg-gray-900 font-bold rounded-xl">
          Save Snapshot
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

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const summary = @json($summary);
      const config = @json($chartConfig);

      const buildingSelect = document.getElementById('billingBuilding');
      const startInput = document.getElementById('billingStart');
      const endInput = document.getElementById('billingEnd');
      const generateBtn = document.getElementById('btnBilling');
      const saveBtn = document.getElementById('btnBillingSave');
      const statusElement = document.getElementById('billingStatus');

      const ctxEnergy = document.getElementById('billingEnergyChart').getContext('2d');
      const ctxPrev = document.getElementById('previousMonthChart').getContext('2d');
      const ctxTrend = document.getElementById('totalKwhTrend').getContext('2d');

      let energyChart = null;
      let previousChart = null;
      let trendChart = null;
      let refreshTimer = null;

      const state = {
        summary,
        buildings: config.buildings ?? [],
        trend: config.trend ?? [],
      };

      const buildBuildingDataset = (buildings = []) => {
        return (buildings ?? []).reduce((carry, item) => {
          carry[String(item.id)] = item;
          return carry;
        }, {});
      };

      let buildingDataset = buildBuildingDataset(state.buildings);

      const getSelectedRange = () => ({
        start: startInput.value,
        end: endInput.value,
      });

      const findBuildingPayload = (id) => {
        if (!id) return null;
        return buildingDataset[id] ?? null;
      };

      async function fetchSnapshot(buildingId = null, selectedRange = null) {
        const params = new URLSearchParams();
        if (buildingId) params.append('building_id', buildingId);
        if (selectedRange?.start) params.append('start', selectedRange.start);
        if (selectedRange?.end) params.append('end', selectedRange.end);

        try {
          if (statusElement) {
            statusElement.textContent = 'Refreshing live data…';
          }

          const response = await fetch(`/api/billing?${params.toString()}`, {
            headers: { Accept: 'application/json' },
          });

          if (!response.ok) {
            throw new Error(await response.text());
          }

          const payload = await response.json();
          applySnapshot(payload);

          const active = findBuildingPayload(buildingId);
          const range = selectedRange ?? getSelectedRange();

          updateKpis(active ?? null);
          renderBuildingCharts(active ?? null);
          renderTrendChart();
          updateStatus(active ?? null, range);

          if (statusElement) {
            statusElement.textContent = 'Live billing snapshot updated.';
          }
        } catch (error) {
          console.error(error);
          if (statusElement) {
            statusElement.textContent = 'Live refresh failed. Showing cached data.';
          }
        }
      }

      const formatNumber = (value, options = {}) => {
        return new Intl.NumberFormat('en-PH', options).format(value ?? 0);
      };

      function updateKpis(payload = null) {
        const source = payload ?? state.summary;
        const thisMonth = source?.this_month_kwh ?? 0;
        const lastMonth = source?.last_month_kwh ?? 0;
        const cost = source?.cost ?? source?.total_cost ?? 0;
        const avgPf = payload && payload.avg_pf !== null
          ? payload.avg_pf
          : (state.summary?.avg_pf ?? null);

        document.getElementById('thisMonthkW').textContent = `${formatNumber(thisMonth, { maximumFractionDigits: 0 })} kWh`;
        document.getElementById('previousMonthkW').textContent = `${formatNumber(lastMonth, { maximumFractionDigits: 0 })} kWh`;
        document.getElementById('totalCost').textContent = `₱${formatNumber(cost, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        document.getElementById('avgPF').textContent = avgPf !== null
          ? formatNumber(avgPf, { minimumFractionDigits: 3, maximumFractionDigits: 3 })
          : '—';
      }

      function renderTrendChart() {
        const labels = state.trend.map(point => point.label);
        const values = state.trend.map(point => point.kwh);

        if (trendChart) trendChart.destroy();

        trendChart = new Chart(ctxTrend, {
          type: 'line',
          data: {
            labels,
            datasets: [{
              label: 'Total kWh',
              data: values,
              borderColor: '#a11d1d',
              backgroundColor: '#a11d1d33',
              tension: 0.35,
              fill: true,
              pointRadius: 3,
            }],
          },
          options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } },
          },
        });
      }

      function renderBuildingCharts(payload = null) {
        const label = payload ? `${payload.code} – ${payload.name}` : 'All Buildings';
        const fallbackSummary = state.summary ?? {};
        const thisMonth = payload ? payload.this_month_kwh : fallbackSummary.this_month_kwh;
        const lastMonth = payload ? payload.last_month_kwh : fallbackSummary.last_month_kwh;

        if (energyChart) energyChart.destroy();
        if (previousChart) previousChart.destroy();

        energyChart = new Chart(ctxEnergy, {
          type: 'bar',
          data: {
            labels: [label],
            datasets: [{
              label: 'This Month (kWh)',
              data: [thisMonth],
              backgroundColor: '#a11d1d99',
              borderColor: '#a11d1d',
              borderRadius: 8,
            }],
          },
          options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } },
          },
        });

        previousChart = new Chart(ctxPrev, {
          type: 'bar',
          data: {
            labels: [label],
            datasets: [{
              label: 'Previous Month (kWh)',
              data: [lastMonth],
              backgroundColor: '#caa15a99',
              borderColor: '#caa15a',
              borderRadius: 8,
            }],
          },
          options: {
            responsive: true,
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } },
          },
        });
      }

      function updateStatus(payload = null, range = null) {
        if (!statusElement) {
          return;
        }

        const target = payload ? `${payload.code} – ${payload.name}` : 'all buildings';
        if (range && range.start && range.end) {
          statusElement.textContent = `Showing ${target} from ${range.start} to ${range.end}.`;
          return;
        }

        statusElement.textContent = `Showing ${target} for the current month.`;
      }

      function handleGenerate() {
        const buildingId = buildingSelect.value || null;
        const selectedRange = getSelectedRange();
        fetchSnapshot(buildingId, selectedRange);
      }

      async function handleSave() {
        const buildingId = buildingSelect.value || null;
        const selectedRange = getSelectedRange();
        const params = new URLSearchParams();
        if (buildingId) params.append('building_id', buildingId);
        if (selectedRange?.start) params.append('start', selectedRange.start);
        if (selectedRange?.end) params.append('end', selectedRange.end);

        try {
          if (statusElement) {
            statusElement.textContent = 'Saving snapshot…';
          }

          const response = await fetch(`/api/billing/save?${params.toString()}`, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
          });

          if (!response.ok) {
            throw new Error(await response.text());
          }

          const payload = await response.json();
          if (statusElement) {
            statusElement.textContent = 'Snapshot saved to billing records.';
          }

          await fetchSnapshot(buildingId, selectedRange);
        } catch (error) {
          console.error(error);
          if (statusElement) {
            statusElement.textContent = 'Save failed.';
          }
        }
      }

      generateBtn.addEventListener('click', handleGenerate);
      saveBtn.addEventListener('click', handleSave);

      updateKpis();
      renderBuildingCharts();
      renderTrendChart();
      updateStatus();

      const applySnapshot = (snapshot) => {
        if (!snapshot) {
          return;
        }

        if (snapshot.summary) {
          state.summary = snapshot.summary;
        }

        if (snapshot.buildings) {
          state.buildings = snapshot.buildings;
          buildingDataset = buildBuildingDataset(state.buildings);
        }

        if (snapshot.trend) {
          state.trend = snapshot.trend;
        }

        updateKpis();
        renderBuildingCharts();
        renderTrendChart();
        updateStatus();
      };

      const refreshFromApi = async () => {
        const buildingId = buildingSelect.value || null;
        const selectedRange = getSelectedRange();
        await fetchSnapshot(buildingId, selectedRange);
      };

      const queueRefresh = () => {
        clearTimeout(refreshTimer);
        refreshTimer = setTimeout(refreshFromApi, 750);
      };

      window.billingPage = window.billingPage || {};
      window.billingPage.refresh = () => {
        queueRefresh();
      };
    });
  </script>

{{-- <script>
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
  </script> --}}

</section>
@endsection
