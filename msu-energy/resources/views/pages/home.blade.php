@extends('layouts.app')

@section('content')
@php
  $colorPalette = ['#581313', '#f28c38', '#e03a3a', '#3fa4ff', '#ff7a7a'];
@endphp
<section id="dashboard" class="w-full max-w-[1400px] mx-auto">

  <h1 class="text-4xl font-extrabold text-center text-maroon tracking-wide mb-6">
    MSU-IIT BUILDINGS ENERGY DASHBOARD
  </h1>

  {{-- Charts + KPIs Container --}}
  <div class="flex gap-4 justify-center items-end">

    {{-- Previous Month Chart --}}
    <div class="bg-white border rounded-2xl shadow p-2 flex-shrink-0"
         style="width:300px; height:360px; display:flex; flex-direction:column;">
      <h3 class="text-maroon font-semibold mb-1 text-center text-sm">Previous Month Energy (kW)</h3>
      <canvas id="prevMonthChart" style="height:280px;"></canvas>
    </div>

    {{-- Main Chart + KPI + Fullscreen --}}
    <div class="flex gap-4 items-start">

      {{-- Main Chart --}}
      <div class="bg-white border rounded-2xl shadow p-2 flex flex-col justify-between"
          style="width:600px; height:480px;">
        <canvas id="buildingChart" style="flex-grow:1;"></canvas>

        {{-- Legend --}}
        <div id="dashboardLegend" class="flex justify-center mt-3 space-x-4 text-xs font-semibold flex-wrap">
          {{-- <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#581313] inline-block"></span> BLDG1: COE</div>
          <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#f28c38] inline-block"></span> BLDG2: SET</div>
          <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#e03a3a] inline-block"></span> BLDG3: CSM</div>
          <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#3fa4ff] inline-block"></span> BLDG4: CCS</div>
          <div class="flex items-center gap-1"><span class="w-4 h-4 bg-[#ff7a7a] inline-block"></span> BLDG5: PRISM</div> --}}
          @forelse($labels as $index => $code)
            @php $color = $colorPalette[$index % count($colorPalette)]; @endphp
            <div class="flex items-center gap-1"><span class="w-4 h-4 inline-block" style="background-color: {{ $color }}"></span> {{ $code }}</div>
          @empty
            <span class="text-gray-500">No readings available yet</span>
          @endforelse
        </div>
      </div>

      {{-- KPI Boxes + Fullscreen --}}
      <div class="flex flex-col" style="height:480px;">
        
        {{-- KPI Column --}}
        <div class="flex flex-col gap-3 flex-1">
          <div class="bg-gray-300 rounded-xl text-center p-3 border-2 border-black h-full flex flex-col justify-center">
            <h3 class="font-bold text-sm mb-1">TOTAL POWER (kW)</h3>
            <p id="dashboardTotalPower" class="text-2xl font-extrabold">{{ number_format($totalPower ?? 0, 0) }}</p>
          </div>

          <div class="bg-gray-300 rounded-xl text-center p-3 border-2 border-black h-full flex flex-col justify-center">
            <h3 class="font-bold text-sm mb-1">POWER FACTOR (PF)</h3>
            <p id="dashboardAvgPf" class="text-2xl font-extrabold">{{ number_format($avgPF ?? 0, 4) }}</p>
          </div>

          <div class="bg-gray-300 rounded-xl text-center p-3 border-2 border-black h-full flex flex-col justify-center">
            <h3 class="font-bold text-sm mb-1">LAST MONTH (kW)</h3>
            <p id="dashboardLastMonth" class="text-2xl font-extrabold">{{ number_format($lastMonthkW ?? 0, 0) }}</p>
          </div>

          <div class="bg-gray-300 rounded-xl text-center p-3 border-2 border-black h-full flex flex-col justify-center">
            <h3 class="font-bold text-sm mb-1">THIS MONTH (kW)</h3>
            <p id="dashboardThisMonth" class="text-2xl font-extrabold">{{ number_format($thisMonthkW ?? 0, 0) }}</p>
          </div>
        </div>

        <p id="dashboardUpdatedAt" class="text-center text-xs text-gray-500 mt-2">Last updated {{ now()->format('M d, Y H:i') }}</p>

      </div>

    </div>
  </div>

  {{-- Chart Scripts --}}
  {{--
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const labels = {!! json_encode($labels ?? []) !!};
      const values = {!! json_encode($values ?? []) !!};
      const prevValues = {!! json_encode($prevValues ?? []) !!};
      const palette = {!! json_encode($colorPalette) !!};
      const colors = labels.map((_, idx) => palette[idx % palette.length] ?? '#581313');
      const ctx = document.getElementById("buildingChart").getContext("2d");
      new Chart(ctx, {
        type: "bar",
        data: { labels, datasets: [{ label: "kW", data: values, backgroundColor: colors, borderRadius: 8 }] },
        options: { responsive: true, scales: { y: { beginAtZero: true, title: { display: true, text: "Kilowatt-Hour (kW)" } } }, plugins: { legend: { display: false } } }
      });
      const ctxPrev = document.getElementById("prevMonthChart").getContext("2d");
      new Chart(ctxPrev, {
        type: "bar",
        data: { labels, datasets: [{ label: "kW", data: prevValues, backgroundColor: colors, borderRadius: 8 }] },
        options: { responsive: true, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
      });
    });
  </script>
  --}}

  <script>
    window.dashboardBootstrap = {
      chart: {
        labels: {!! json_encode($labels ?? []) !!},
        current: {!! json_encode($values ?? []) !!},
        previous: {!! json_encode($prevValues ?? []) !!},
      },
      totals: {
        total_power: {{ round($totalPower ?? 0, 3) }},
        avg_pf: {{ round($avgPF ?? 0, 4) }},
        last_month_kwh: {{ round($lastMonthkW ?? 0, 3) }},
        this_month_kwh: {{ round($thisMonthkW ?? 0, 3) }},
      },
      generated_at: '{{ now()->toIso8601String() }}',
    };
  </script>

</section>
@endsection