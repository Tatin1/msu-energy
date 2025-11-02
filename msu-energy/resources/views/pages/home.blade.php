@extends('layouts.app')

@section('content')
<section id="dashboard">
  <h1 class="text-3xl font-bold text-maroon mb-6">Campus Energy Dashboard</h1>

  {{-- KPI Grid --}}
  <div class="grid md:grid-cols-4 gap-6 mb-8">
    <x-kpi title="Total Power (W)" :value="number_format($totalPower, 2)" color="bg-maroon" />
    <x-kpi title="Avg Power Factor" :value="number_format($avgPF, 2)" color="bg-gold" />
    <x-kpi title="Last Month kW" :value="number_format($lastMonthKwh, 2)" color="bg-scarlet" />
    <x-kpi title="This Month kW" :value="number_format($thisMonthKwh, 2)" color="bg-grey-700" />
  </div>

  {{-- Power Chart --}}
  <div class="bg-white border rounded-2xl shadow p-4">
    <canvas id="powerChart" height="100"></canvas>
  </div>
</section>

<script>
const ctx = document.getElementById('powerChart');

new Chart(ctx, {
  type: 'bar',
  data: {
    labels: '{!! json_encode($labels) !!}',
    datasets: [{
      label: 'Active Power (W)',
      data: '{!! json_encode($values) !!}',
      backgroundColor: '#8c1b1b'
    }]
  },
  options: {
    responsive: true,
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});
</script>
@endsection
