@extends('layouts.app')

@section('content')
<section id="options" class="space-y-6">
  <h1 class="text-3xl font-bold text-maroon mb-6">System Options</h1>

  {{-- General Settings --}}
  <div class="bg-gray-100 rounded-lg shadow p-6 flex items-center justify-between max-w-xl">
    <div>
      <label class="block text-sm font-semibold mb-1">Tariff Profile</label>
      <input type="text" value="Default" class="border border-gray-300 rounded-md px-3 py-1 w-48">
    </div>
    <button class="bg-white border border-maroon text-maroon px-4 py-2 rounded-md hover:bg-maroon-50">
      Edit Tariffs
    </button>
  </div>

  {{-- Device Management --}}
  <div class="bg-gray-100 rounded-lg shadow p-6 max-w-xl space-y-2">
    <div class="flex gap-2 mb-2">
      <button class="bg-maroon text-white px-4 py-2 rounded-md hover:bg-maroon-700">Add Device</button>
      <button class="bg-white border border-maroon text-maroon px-4 py-2 rounded-md hover:bg-maroon-50">Remove Device</button>
      <button class="bg-white border border-maroon text-maroon px-4 py-2 rounded-md hover:bg-maroon-50">Test Connection</button>
    </div>
    <p class="text-sm text-gray-600">Designed to scale—add ADE7753 + CVM-C10 nodes without redesign.</p>
  </div>

  {{-- Notifications --}}
  <div class="bg-gray-100 rounded-lg shadow p-6 max-w-xl space-y-2">
    <h2 class="font-semibold text-maroon">Notifications</h2>
    <div class="flex items-center gap-2">
      <input type="checkbox" id="enableAlerts" class="border-gray-300 rounded">
      <label for="enableAlerts" class="text-sm">Enable Email Alerts</label>
      <input type="text" placeholder="Recipient Email" class="border border-gray-300 rounded-md px-2 py-1 w-48">
      <label class="text-sm">Alert Threshold kW</label>
      <input type="number" value="100" class="border border-gray-300 rounded-md px-2 py-1 w-20">
    </div>
  </div>

  {{-- Data Export --}}
  <div class="bg-gray-100 rounded-lg shadow p-6 max-w-xl space-y-2">
    <h2 class="font-semibold text-maroon">Data Export</h2>
    <div class="flex items-center gap-4">
      <label class="flex items-center gap-1"><input type="radio" name="exportFormat" checked> CSV</label>
      <label class="flex items-center gap-1"><input type="radio" name="exportFormat"> Excel</label>
      <label class="flex items-center gap-1"><input type="radio" name="exportFormat"> PDF</label>
      <label class="flex items-center gap-1">
        <input type="checkbox"> Enable Auto-export (daily/monthly)
      </label>
    </div>
  </div>
</section>
@endsection
