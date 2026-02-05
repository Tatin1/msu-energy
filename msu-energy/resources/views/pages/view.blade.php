@extends('layouts.app')

@section('content')
<section id="view" class="space-y-8">
  <h1 class="text-3xl font-bold text-maroon">Dashboard Views</h1>

  {{-- Quick Access Section --}}
  <div class="card">
    <h3 class="font-semibold mb-3">Quick Access Views</h3>
    <div class="flex flex-wrap gap-3">
      <button class="btn btn-ghost active-view" data-view="realtime">Real-Time Usage Graphs</button>
      <button class="btn btn-ghost" data-view="billing">Billing Summary</button>
      <button class="btn btn-ghost" data-view="load">Aggregated Load Monitor</button>
    </div>
  </div>

  {{-- Display Area --}}
  <div id="viewDisplay" class="card text-center py-6">
    {{-- <h3 class="font-semibold text-lg text-gray-700">Select a view to display data</h3> --}}
    <p class="text-sm text-gray-500">Loading latest datasets...</p>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const viewDisplay = document.getElementById("viewDisplay");
    const viewButtons = document.querySelectorAll("[data-view]");
    let chartInstance = null;
    const realtimeSeries = @json($realtimeSeries ?? []);
    const billingSeries = @json($billingSeries ?? []);
    const loadSeries = @json($loadSeries ?? []);
    const viewSummary = @json($viewSummary ?? []);
    const palette = [
      "#7a0e0e",
      "#9d1b1b",
      "#b34141",
      "#d28c8c",
      "#a35a5a",
      "#6b3b3b",
      "#4b2626",
      "#d9a7a7",
      "#c25656",
      "#8c3c3c"
    ];
    const numberFormatter = new Intl.NumberFormat(undefined, { maximumFractionDigits: 2 });
    const currencyFormatter = new Intl.NumberFormat(undefined, { style: "currency", currency: "PHP", maximumFractionDigits: 0 });

    // Load default view
    renderView("realtime");

    // Handle view switching
    viewButtons.forEach(btn => {
      btn.addEventListener("click", () => {
        viewButtons.forEach(b => b.classList.remove("active-view"));
        btn.classList.add("active-view");
        renderView(btn.dataset.view);
      });
    });

    function renderView(type) {
      // Clear previous chart if any
      if (chartInstance) chartInstance.destroy();

      // Reset content
      viewDisplay.innerHTML = "";
      const wrapper = document.createElement("div");

      // === REAL-TIME USAGE VIEW ===
      if (type === "realtime") {
        if (!realtimeSeries.length) {
          handleEmptyState("No meter readings recorded in the past 24 hours.");
          return;
        }

        const totals = realtimeSeries.map(point => point.total_kw);
        const latestKw = totals.length ? totals[totals.length - 1] : 0;
        const peakKw = Math.max(...totals);
        const minKw = Math.min(...totals);
        const realtimeWindow = viewSummary.realtime_window || {};
        const updatedAt = realtimeWindow.end
          ? new Date(realtimeWindow.end).toLocaleString()
          : "n/a";
        const realtimeMetrics = [
          renderMetric("Latest kW", numberFormatter.format(latestKw)),
          renderMetric("Peak kW", numberFormatter.format(peakKw)),
          renderMetric("Min kW", numberFormatter.format(minKw))
        ].join("");

        wrapper.innerHTML = `
          <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <h3 class="font-semibold text-lg">Real-Time Usage (past 24 hours)</h3>
              <p class="text-sm text-gray-500">Last updated ${escapeHtml(updatedAt)}</p>
            </div>
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 metric-grid">
              ${realtimeMetrics}
            </div>
          </div>
          <canvas id="realtimeChart" height="200"></canvas>`;
        viewDisplay.appendChild(wrapper);

        const ctx = document.getElementById("realtimeChart");
        chartInstance = new Chart(ctx, {
          type: "line",
          data: {
            // labels: ["8 AM","9 AM","10 AM","11 AM","12 PM","1 PM","2 PM"],
            labels: realtimeSeries.map(point => point.label),
            datasets: [{
              label: "Active Power (kW)",
              // data: [120,135,140,160,170,165,175],
              data: realtimeSeries.map(point => point.total_kw),
              borderColor: "#7a0e0e",
              backgroundColor: "#7a0e0e33",
              fill: true,
              tension: 0.3
            }]
          },
          options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { position: "bottom" } }
          }
        });
      }

      // === BILLING SUMMARY VIEW ===
      else if (type === "billing") {
        if (!billingSeries.length) {
          handleEmptyState("No billing records found for the current period.");
          return;
        }

        const totalKwh = billingSeries.reduce((sum, row) => sum + row.this_month_kwh, 0);
        const totalCost = billingSeries.reduce((sum, row) => sum + row.cost, 0);
        const billingMetrics = [
          renderMetric("Total kWh", numberFormatter.format(totalKwh)),
          renderMetric("Estimated Cost", currencyFormatter.format(totalCost)),
          renderMetric(
            "Average kWh",
            numberFormatter.format(billingSeries.length ? totalKwh / billingSeries.length : 0)
          )
        ].join("");
        const billingRows = billingSeries.map(row => `
          <tr class="border-t border-gray-100">
            <td class="py-2 pr-4 font-semibold text-gray-900">${escapeHtml(row.label)}</td>
            <td class="py-2 pr-4 text-gray-600">${escapeHtml(row.name)}</td>
            <td class="py-2 pr-4">${numberFormatter.format(row.this_month_kwh)}</td>
            <td class="py-2 pr-4">${currencyFormatter.format(row.cost)}</td>
          </tr>
        `).join("");

        const billingPeriod = viewSummary.billing_period ? viewSummary.billing_period : 'Current';

        wrapper.innerHTML = `
          <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <h3 class="font-semibold text-lg">Billing Summary (${escapeHtml(billingPeriod)})</h3>
              <p class="text-sm text-gray-500">${billingSeries.length} building${billingSeries.length === 1 ? '' : 's'} with usage</p>
            </div>
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 metric-grid">
              ${billingMetrics}
            </div>
          </div>
          <canvas id="billingChart" height="200"></canvas>
          <div class="overflow-x-auto mt-6">
            <table class="min-w-full text-left text-sm">
              <thead>
                <tr>
                  <th class="py-2 pr-4 text-gray-500 font-medium">Code</th>
                  <th class="py-2 pr-4 text-gray-500 font-medium">Building</th>
                  <th class="py-2 pr-4 text-gray-500 font-medium">This Month (kWh)</th>
                  <th class="py-2 pr-4 text-gray-500 font-medium">Est. Cost</th>
                </tr>
              </thead>
              <tbody>
                ${billingRows}
              </tbody>
            </table>
          </div>`;
        viewDisplay.appendChild(wrapper);

        const ctx = document.getElementById("billingChart");
        chartInstance = new Chart(ctx, {
          type: "bar",
          data: {
            // labels: ["COE","CCS","CSM","CED","CBAA","CON"],
            labels: billingSeries.map(item => item.label),
            datasets: [{
              label: "This Month's Bill (₱)",
              // data: [12000,9500,8800,10200,9700,8000],
              data: billingSeries.map(item => item.cost),
              backgroundColor: "#7a0e0e"
            }]
          },
          options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
          }
        });
      }

      // === AGGREGATED LOAD MONITOR VIEW ===
      else if (type === "load") {
        if (!loadSeries.length) {
          handleEmptyState("No load profile available for the selected window.");
          return;
        }

        const loadWindow = viewSummary.load_window || {};
        const windowEnd = loadWindow.end ? new Date(loadWindow.end).toLocaleString() : "n/a";
        const topContributor = loadSeries.length ? loadSeries[0].label : 'n/a';
        const loadMetrics = [
          renderMetric(
            "Total kW",
            numberFormatter.format(loadSeries.reduce((sum, row) => sum + row.total_kw, 0))
          ),
          renderMetric("Top Contributor", topContributor),
          renderMetric("Buildings", loadSeries.length)
        ].join("");
        const loadRows = loadSeries.map(row => `
          <li class="flex items-center justify-between border border-gray-100 rounded p-3">
            <div>
              <p class="font-semibold text-gray-900">${escapeHtml(row.label)}</p>
              <p class="text-sm text-gray-500">${numberFormatter.format(row.total_kw)} kW</p>
            </div>
            <span class="text-base font-semibold text-gray-900">${row.percentage}%</span>
          </li>
        `).join("");
        const loadColors = loadSeries.map((_, index) => palette[index % palette.length]);

        wrapper.innerHTML = `
          <div class="flex flex-col gap-4 mb-6 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <h3 class="font-semibold text-lg">Aggregated Load Monitor</h3>
              <p class="text-sm text-gray-500">Window ending ${escapeHtml(windowEnd)}</p>
            </div>
            <div class="grid grid-cols-2 gap-3 lg:grid-cols-3 metric-grid">
              ${loadMetrics}
            </div>
          </div>
          <div class="flex flex-col gap-6 lg:flex-row lg:gap-10">
            <div class="lg:w-1/2">
              <canvas id="loadChart" height="220"></canvas>
            </div>
            <div class="lg:flex-1">
              <ul class="space-y-3">
                ${loadRows}
              </ul>
            </div>
          </div>`;
        viewDisplay.appendChild(wrapper);

        const ctx = document.getElementById("loadChart");
        chartInstance = new Chart(ctx, {
          type: "doughnut",
          data: {
            // labels: ["COE","CCS","CSM","CED","CBAA","CON"],
            labels: loadSeries.map(item => item.label),
            datasets: [{
              label: "Load Share (%)",
              // data: [30,25,15,10,12,8],
              data: loadSeries.map(item => item.percentage),
              // backgroundColor: [
              //   "#7a0e0e","#9d1b1b","#b34141","#d28c8c","#a35a5a","#6b3b3b"
              // ]
              backgroundColor: loadColors
            }]
          },
          options: {
            plugins: { legend: { position: "bottom" } }
          }
        });
      }
    }
    function handleEmptyState(message) {
      viewDisplay.innerHTML = `
        <div class="py-12 text-gray-500">
          <p>${message}</p>
        </div>`;
    }

    function renderMetric(label, value) {
      return `
        <div class="metric-card p-3 bg-gray-50 rounded border border-gray-100">
          <p class="text-xs uppercase tracking-wide text-gray-500">${escapeHtml(label)}</p>
          <p class="text-lg font-semibold text-gray-900">${escapeHtml(value)}</p>
        </div>`;
    }

    function escapeHtml(value) {
      const div = document.createElement("div");
      div.textContent = value === undefined || value === null ? "" : value;
      return div.innerHTML;
    }
  });
  </script>

  <style>
    .active-view {
      background-color: #7a0e0e !important;
      color: white !important;
      border: 1px solid #7a0e0e;
    }

    .metric-card {
      min-width: 120px;
    }
  </style>
</section>
@endsection
