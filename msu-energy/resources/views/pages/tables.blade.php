@extends('layouts.app')

@section('content')
<section id="tables" class="space-y-10">
  <h1 class="text-3xl font-bold text-maroon mb-6">System Tables</h1>
  

  {{-- Transformer Log Table --}}
  <div class="card relative">

    <!-- Title + Export Button on the same level -->
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold text-maroon">Transformer Log</h2>

        <button onclick="exportTableToCSV('transformer-log.csv', 'transformerTable')"
            class="bg-maroon text-white px-3 py-1 rounded-lg text-sm hover:bg-maroon-700">
            Export CSV
        </button>
    </div>

    <div class="overflow-x-auto">
      <table id="transformerTable" class="min-w-full border border-gray-300 rounded-xl text-sm">
        <thead class="bg-maroon text-white">
          <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">Transformer</th>
            <th class="px-4 py-2 text-left">Voltage (V)</th>
            <th class="px-4 py-2 text-left">Load (kW)</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Last Updated</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @foreach ([
            ['T1', '230', '50.2', 'Normal', '2025-10-31 10:15 AM'],
            ['T2', '228', '48.5', 'Warning', '2025-10-31 10:17 AM'],
            ['T3', '231', '52.0', 'Normal', '2025-10-31 10:20 AM'],
          ] as $index => $row)
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 font-medium">{{ $index + 1 }}</td>
            <td class="px-4 py-2">{{ $row[0] }}</td>
            <td class="px-4 py-2">{{ $row[1] }}</td>
            <td class="px-4 py-2">{{ $row[2] }}</td>
            <td class="px-4 py-2">{{ $row[3] }}</td>
            <td class="px-4 py-2 text-gray-600">{{ $row[4] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- System Log Table --}}
  <div class="card relative">

    <!-- Title + Export Button on the same level -->
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-xl font-semibold text-maroon">System Log</h2>

        <button onclick="exportTableToCSV('system-log.csv', 'systemTable')"
            class="bg-maroon text-white px-3 py-1 rounded-lg text-sm hover:bg-maroon-700">
            Export CSV
        </button>
    </div>

    <div class="overflow-x-auto">
      <table id="systemTable" class="min-w-full border border-gray-300 rounded-xl text-sm">
        <thead class="bg-maroon text-white">
          <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">Event</th>
            <th class="px-4 py-2 text-left">Source</th>
            <th class="px-4 py-2 text-left">Severity</th>
            <th class="px-4 py-2 text-left">Timestamp</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @foreach ([
            ['System boot completed', 'Server 1', 'Info', '2025-10-31 09:00 AM'],
            ['Connection lost to COE Meter', 'Meter COE-1', 'Warning', '2025-10-31 09:45 AM'],
            ['High voltage detected', 'Transformer T2', 'Critical', '2025-10-31 10:00 AM'],
            ['Data sync restored', 'Server 1', 'Info', '2025-10-31 10:15 AM'],
          ] as $index => $row)
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 font-medium">{{ $index + 1 }}</td>
            <td class="px-4 py-2">{{ $row[0] }}</td>
            <td class="px-4 py-2">{{ $row[1] }}</td>
            <td class="px-4 py-2">{{ $row[2] }}</td>
            <td class="px-4 py-2 text-gray-600">{{ $row[3] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

</section>

{{-- EXPORT SCRIPT --}}
<script>
function exportTableToCSV(filename, tableId) {
    let csv = [];
    let rows = document.querySelectorAll(`#${tableId} tr`);

    for (let row of rows) {
        let cols = row.querySelectorAll("th, td");
        let rowData = [];
        cols.forEach(col => rowData.push(col.innerText.replace(/,/g, "")));
        csv.push(rowData.join(","));
    }

    let csvFile = new Blob([csv.join("\n")], { type: "text/csv" });

    let downloadLink = document.createElement("a");
    downloadLink.download = filename;
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = "none";

    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

@endsection
