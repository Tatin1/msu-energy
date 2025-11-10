@props(['title', 'value', 'color' => 'bg-maroon'])

<div class="card text-center p-6 shadow bg-gray-50 border rounded-2xl">
  <div class="kpi-label text-maroon font-semibold mb-2 uppercase">{{ $title }}</div>
  <div class="text-3xl font-bold text-gray-800">{{ $value }}</div>
</div>
