@extends('layouts.app')

@section('content')
<section id="history" class="space-y-10">
  <h1 class="text-3xl font-bold text-maroon mb-4">Historical Logs</h1>

  {{-- Building Data Section --}}
  <div class="bg-white border rounded-2xl shadow p-6">
    <h2 class="text-2xl font-semibold text-maroon mb-4">Building Data</h2>

    <div class="flex flex-wrap items-center gap-4 mb-4">
      <label class="flex items-center gap-2">
        <span class="font-medium">Date:</span>
        <input id="building-date" type="date" class="border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-maroon">
      </label>

      <label class="flex items-center gap-2">
        <span class="font-medium">Meter:</span>
        <input id="building-meter" type="number" min="1" class="border rounded-lg px-3 py-1.5 w-20 focus:ring-2 focus:ring-maroon">
      </label>

      <button id="exportBuildingBtn" class="ml-auto bg-maroon text-white px-4 py-2 rounded-lg font-semibold hover:bg-maroon-700">
        Export
      </button>
    </div>

    {{-- Building Data Table --}}
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 text-sm text-center">
        <thead class="bg-gray-100 font-semibold">
          <tr>
            <th class="border px-3 py-2">ID</th>
            <th class="border px-3 py-2">DATE</th>
            <th class="border px-3 py-2">TIME</th>
            <th class="border px-3 py-2">TIMEₑd</th>
            <th class="border px-3 py-2">F</th>
            <th class="border px-3 py-2">V1</th>
            <th class="border px-3 py-2">V2</th>
            <th class="border px-3 py-2">V3</th>
            <th class="border px-3 py-2">A1</th>
            <th class="border px-3 py-2">A2</th>
            <th class="border px-3 py-2">A3</th>
            <th class="border px-3 py-2">PF1</th>
            <th class="border px-3 py-2">PF2</th>
            <th class="border px-3 py-2">PF3</th>
            <th class="border px-3 py-2">kWh</th>
          </tr>
        </thead>
        <tbody>
          <tr class="hover:bg-gray-50">
            <td class="border px-3 py-2">1</td>
            <td class="border px-3 py-2">2025-10-25</td>
            <td class="border px-3 py-2">08:15</td>
            <td class="border px-3 py-2">08:30</td>
            <td class="border px-3 py-2">60</td>
            <td class="border px-3 py-2">230</td>
            <td class="border px-3 py-2">228</td>
            <td class="border px-3 py-2">231</td>
            <td class="border px-3 py-2">12.4</td>
            <td class="border px-3 py-2">11.8</td>
            <td class="border px-3 py-2">13.0</td>
            <td class="border px-3 py-2">0.92</td>
            <td class="border px-3 py-2">0.94</td>
            <td class="border px-3 py-2">0.91</td>
            <td class="border px-3 py-2">128.3</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- System Data Section --}}
  <div class="bg-white border rounded-2xl shadow p-6">
    <h2 class="text-2xl font-semibold text-maroon mb-4">System Data</h2>

    <div class="flex flex-wrap items-center gap-4 mb-4">
      <label class="flex items-center gap-2">
        <span class="font-medium">Date:</span>
        <input id="system-date" type="date" class="border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-maroon">
      </label>

      <button id="exportSystemBtn" class="ml-auto bg-maroon text-white px-4 py-2 rounded-lg font-semibold hover:bg-maroon-700">
        Export
      </button>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 text-sm text-center">
        <thead class="bg-gray-100 font-semibold">
          <tr>
            <th class="border px-3 py-2">ID</th>
            <th class="border px-3 py-2">DATE</th>
            <th class="border px-3 py-2">TIME</th>
            <th class="border px-3 py-2">TIMEₑd</th>
            <th class="border px-3 py-2">TOTAL KW</th>
            <th class="border px-3 py-2">TOTAL KVAR</th>
            <th class="border px-3 py-2">TOTAL KVA</th>
            <th class="border px-3 py-2">TOTAL PF</th>
          </tr>
        </thead>
        <tbody>
          <tr class="hover:bg-gray-50">
            <td class="border px-3 py-2">1</td>
            <td class="border px-3 py-2">2025-10-25</td>
            <td class="border px-3 py-2">08:15</td>
            <td class="border px-3 py-2">08:30</td>
            <td class="border px-3 py-2">420</td>
            <td class="border px-3 py-2">180</td>
            <td class="border px-3 py-2">460</td>
            <td class="border px-3 py-2">0.92</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- Export Functionality --}}
  <script>
    document.getElementById('exportBuildingBtn').addEventListener('click', () => {
      window.location.href = "{{ route('export.building') }}";
    });

    document.getElementById('exportSystemBtn').addEventListener('click', () => {
      window.location.href = "{{ route('export.system') }}";
    });
  </script>
</section>
@endsection
