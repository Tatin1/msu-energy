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
    <h3 class="font-semibold text-lg text-gray-700">Select a view to display data</h3>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    const viewDisplay = document.getElementById("viewDisplay");
    const viewButtons = document.querySelectorAll("[data-view]");
    let chartInstance = null;

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
        wrapper.innerHTML = `
          <h3 class="font-semibold mb-3">Real-Time Usage Graphs</h3>
          <canvas id="realtimeChart" height="180"></canvas>`;
        viewDisplay.appendChild(wrapper);

        const ctx = document.getElementById("realtimeChart");
        chartInstance = new Chart(ctx, {
          type: "line",
          data: {
            labels: ["8 AM","9 AM","10 AM","11 AM","12 PM","1 PM","2 PM"],
            datasets: [{
              label: "Active Power (kW)",
              data: [120,135,140,160,170,165,175],
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
        wrapper.innerHTML = `
          <h3 class="font-semibold mb-3">Billing Summary</h3>
          <canvas id="billingChart" height="180"></canvas>`;
        viewDisplay.appendChild(wrapper);

        const ctx = document.getElementById("billingChart");
        chartInstance = new Chart(ctx, {
          type: "bar",
          data: {
            labels: ["COE","CCS","CSM","CED","CBAA","CON"],
            datasets: [{
              label: "This Month's Bill (₱)",
              data: [12000,9500,8800,10200,9700,8000],
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
        wrapper.innerHTML = `
          <h3 class="font-semibold mb-3">Aggregated Load Monitor</h3>
          <canvas id="loadChart" height="180"></canvas>`;
        viewDisplay.appendChild(wrapper);

        const ctx = document.getElementById("loadChart");
        chartInstance = new Chart(ctx, {
          type: "doughnut",
          data: {
            labels: ["COE","CCS","CSM","CED","CBAA","CON"],
            datasets: [{
              label: "Load Share (%)",
              data: [30,25,15,10,12,8],
              backgroundColor: [
                "#7a0e0e","#9d1b1b","#b34141","#d28c8c","#a35a5a","#6b3b3b"
              ]
            }]
          },
          options: {
            plugins: { legend: { position: "bottom" } }
          }
        });
      }
    }
  });
  </script>

  <style>
    .active-view {
      background-color: #7a0e0e !important;
      color: white !important;
      border: 1px solid #7a0e0e;
    }
  </style>
</section>
@endsection
