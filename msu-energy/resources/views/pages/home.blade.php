@extends('layouts.app')

@section('content')
<section id="dashboard" class="space-y-10">
  <h1 class="text-4xl font-extrabold text-center text-maroon tracking-wide mb-6">
    MSU-IIT BUILDINGS ENERGY DASHBOARD
  </h1>

  <div class="grid md:grid-cols-3 gap-6 items-start">

    {{-- Left: Bar Chart --}}
    <div class="md:col-span-2 bg-white border rounded-2xl shadow p-4">
      <canvas id="buildingChart" height="160"></canvas>

      <p class="text-center text-sm italic text-gray-600 mt-3">
        Click on each building’s bar to view detailed parameters.
      </p>

      {{-- Legend --}}
      <div class="flex justify-center mt-3 space-x-6 text-xs font-semibold">
        <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#581313] inline-block"></span> BLDG1: COE</div>
        <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#f28c38] inline-block"></span> BLDG2: SET</div>
        <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#e03a3a] inline-block"></span> BLDG3: CSM</div>
        <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#3fa4ff] inline-block"></span> BLDG4: CCS</div>
        <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#ff7a7a] inline-block"></span> BLDG5: PRISM</div>
      </div>
    </div>

    {{-- Right: KPI Boxes --}}
    <div class="space-y-4">
      <div class="bg-gray-300 rounded-xl text-center p-4 border-2 border-black">
        <h3 class="font-bold text-sm mb-1">TOTAL POWER (kW)</h3>
        <p class="text-4xl font-extrabold text-black">{{ number_format($totalPower ?? 300000, 0) }}</p>
      </div>

      <div class="bg-gray-300 rounded-xl text-center p-4 border-2 border-black">
        <h3 class="font-bold text-sm mb-1">POWER FACTOR (PF)</h3>
        <p class="text-4xl font-extrabold text-black">{{ number_format($avgPF ?? 0.9423, 4) }}</p>
      </div>

      <div class="bg-gray-300 rounded-xl text-center p-4 border-2 border-black">
        <h3 class="font-bold text-sm mb-1">LAST MONTH (kWh)</h3>
        <p class="text-4xl font-extrabold text-black">{{ number_format($lastMonthKwh ?? 350160, 0) }}</p>
      </div>

      <div class="bg-gray-300 rounded-xl text-center p-4 border-2 border-black">
        <h3 class="font-bold text-sm mb-1">THIS MONTH (kWh)</h3>
        <p class="text-4xl font-extrabold text-black">{{ number_format($thisMonthKwh ?? 352512, 0) }}</p>
      </div>
    </div>

  </div>

  {{-- Chart Script --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const ctx = document.getElementById("buildingChart").getContext("2d");

      // Use Blade-safe JSON encoding
      const labels = {!! json_encode($labels ?? ['COE', 'SET', 'CSM', 'CCS', 'PRISM']) !!};
      const values = {!! json_encode($values ?? [80, 60, 50, 70, 100]) !!};

      const myChart = new Chart(ctx, {
        type: "bar",
        data: {
          labels: labels,
          datasets: [{
            label: "Kilowatt-Hour (kWh)",
            data: values,
            backgroundColor: ["#581313","#f28c38","#e03a3a","#3fa4ff","#ff7a7a"],
            borderRadius: 8
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: "Kilowatt-Hour (kWh)" }
            }
          },
          plugins: { legend: { display: false } },
          onClick: (e, elements) => {
            if(elements.length > 0){
              const building = myChart.data.labels[elements[0].index];
              alert(`Viewing detailed parameters for ${building}`);
            }
          }
        }
      });
    });
  </script>
</section>
@endsection
