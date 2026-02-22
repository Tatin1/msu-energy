@extends('layouts.app')

@section('content')
<section id="tables" class="space-y-10">
  <h1 class="text-3xl font-bold text-maroon mb-6">System Tables</h1>
  

  {{-- Legacy transformer summary table
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
          @forelse(($transformerRows ?? collect()) as $index => $row)
          @php
            $status = $row['status'] ?? 'Unknown';
            $statusColors = [
              'Normal' => 'bg-green-100 text-green-800',
              'Warning' => 'bg-yellow-100 text-yellow-800',
              'Critical' => 'bg-red-100 text-red-700',
              'Unknown' => 'bg-gray-200 text-gray-700',
            ];
            $statusClass = $statusColors[$status] ?? 'bg-gray-200 text-gray-700';
          @endphp
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 font-medium">{{ $index + 1 }}</td>
            <td class="px-4 py-2">{{ $row['label'] }}</td>
            <td class="px-4 py-2">{{ isset($row['voltage']) ? number_format($row['voltage'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['load_kw']) ? number_format($row['load_kw'], 3) : '—' }}</td>
            <td class="px-4 py-2">
              <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">{{ $status }}</span>
            </td>
            <td class="px-4 py-2 text-gray-600">{{ $row['timestamp'] ?? '—' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="px-4 py-4 text-center text-gray-500">No transformer logs available yet.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
  --}}

  {{-- Transformer Log Table (raw schema fields) --}}
  <div class="card relative">
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
            {{-- <th class="px-4 py-2 text-left">Log ID</th> --}}
            <th class="px-4 py-2 text-left">Recorded At</th>
            <th class="px-4 py-2 text-left">Frequency (Hz)</th>
            <th class="px-4 py-2 text-left">V1 (V)</th>
            <th class="px-4 py-2 text-left">V2 (V)</th>
            <th class="px-4 py-2 text-left">V3 (V)</th>
            <th class="px-4 py-2 text-left">A1 (A)</th>
            <th class="px-4 py-2 text-left">A2 (A)</th>
            <th class="px-4 py-2 text-left">A3 (A)</th>
            <th class="px-4 py-2 text-left">PF</th>
            <th class="px-4 py-2 text-left">kWh</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          @forelse(($transformerRows ?? collect()) as $index => $row)
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 font-medium">{{ $index + 1 }}</td>
            {{-- <td class="px-4 py-2">{{ $row['id'] ?? '—' }}</td> --}}
            <td class="px-4 py-2">{{ $row['recorded_at'] ?? '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['frequency']) ? number_format($row['frequency'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['v1']) ? number_format($row['v1'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['v2']) ? number_format($row['v2'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['v3']) ? number_format($row['v3'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['a1']) ? number_format($row['a1'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['a2']) ? number_format($row['a2'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['a3']) ? number_format($row['a3'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['pf']) ? number_format($row['pf'], 3) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['kwh']) ? number_format($row['kwh'], 3) : '—' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="12" class="px-4 py-4 text-center text-gray-500">No transformer logs available yet.</td>
          </tr>
          @endforelse
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
          {{-- <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">Event</th>
            <th class="px-4 py-2 text-left">Source</th>
            <th class="px-4 py-2 text-left">Severity</th>
            <th class="px-4 py-2 text-left">Timestamp</th>
          </tr> --}}
          <tr>
            <th class="px-4 py-2 text-left">#</th>
            <th class="px-4 py-2 text-left">Date</th>
            <th class="px-4 py-2 text-left">Time Start</th>
            <th class="px-4 py-2 text-left">Time End</th>
            <th class="px-4 py-2 text-left">Total kW</th>
            <th class="px-4 py-2 text-left">Total kVAR</th>
            <th class="px-4 py-2 text-left">Total kVA</th>
            <th class="px-4 py-2 text-left">Total PF</th>
          </tr>
        </thead>
        <tbody class="divide-y">
          {{-- @foreach ([
            ['System boot completed', 'Server 1', 'Info', '2025-10-31 09:00 AM'],
            ['Connection lost to COE Meter', 'Meter COE-1', 'Warning', '2025-10-31 09:45 AM'],
            ['High voltage detected', 'Transformer T2', 'Critical', '2025-10-31 10:00 AM'],
            ['Data sync restored', 'Server 1', 'Info', '2025-10-31 10:15 AM'],
          ] as $index => $row) --}}
          @forelse(($systemLogRows ?? collect()) as $index => $row)
          @php
            $pf = $row['total_pf'];
            $pfClass = 'bg-gray-200 text-gray-700';
            if (is_numeric($pf)) {
              if ($pf < 0.8) {
                $pfClass = 'bg-red-100 text-red-700';
              } elseif ($pf < 0.9) {
                $pfClass = 'bg-yellow-100 text-yellow-800';
              } else {
                $pfClass = 'bg-green-100 text-green-800';
              }
            }
          @endphp
          {{-- <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 font-medium">{{ $index + 1 }}</td>
            <td class="px-4 py-2">{{ $row[0] }}</td>
            <td class="px-4 py-2">{{ $row[1] }}</td>
            <td class="px-4 py-2">{{ $row[2] }}</td>
            <td class="px-4 py-2 text-gray-600">{{ $row[3] }}</td>
          </tr> --}}
          <tr class="hover:bg-gray-100">
            <td class="px-4 py-2 font-medium">{{ $index + 1 }}</td>
            <td class="px-4 py-2">{{ $row['date'] ?? '—' }}</td>
            <td class="px-4 py-2">{{ $row['time'] ?? '—' }}</td>
            <td class="px-4 py-2">{{ $row['time_ed'] ?? '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['total_kw']) ? number_format($row['total_kw'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['total_kvar']) ? number_format($row['total_kvar'], 2) : '—' }}</td>
            <td class="px-4 py-2">{{ isset($row['total_kva']) ? number_format($row['total_kva'], 2) : '—' }}</td>
            <td class="px-4 py-2">
              <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $pfClass }}">
                {{ isset($row['total_pf']) ? number_format($row['total_pf'], 3) : '—' }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="px-4 py-4 text-center text-gray-500">No system logs available yet.</td>
          </tr>
          @endforelse
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
