@extends('layouts.app')

@section('content')
<section id="tables" class="space-y-10">
  <h1 class="text-3xl font-bold text-maroon mb-6">System Tables</h1>

  {{-- Transformer Log Table --}}
  <div class="card">
    <h2 class="text-xl font-semibold text-maroon mb-3">Transformer Log</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 rounded-xl text-sm">
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
            <td class="px-4 py-2">
              @if ($row[3] === 'Normal')
                <span class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-semibold">Normal</span>
              @elseif ($row[3] === 'Warning')
                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-semibold">Warning</span>
              @else
                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">Critical</span>
              @endif
            </td>
            <td class="px-4 py-2 text-gray-600">{{ $row[4] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- System Log Table --}}
  <div class="card">
    <h2 class="text-xl font-semibold text-maroon mb-3">System Log</h2>
    <div class="overflow-x-auto">
      <table class="min-w-full border border-gray-300 rounded-xl text-sm">
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
            <td class="px-4 py-2">
              @if ($row[2] === 'Info')
                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-semibold">Info</span>
              @elseif ($row[2] === 'Warning')
                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full text-xs font-semibold">Warning</span>
              @else
                <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-semibold">Critical</span>
              @endif
            </td>
            <td class="px-4 py-2 text-gray-600">{{ $row[3] }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</section>
@endsection
