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
                <span class="font-medium">Building:</span>
                <select id="building-select" class="border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-maroon">
                    <option value="">All</option>
                    {{--
                    @php
                        $buildings = [
                            'CCS', 'CPI', 'OMDH', 'OBA', 'GYMNASIUM', 'CSM', 'KTTO', 'ADMINISTRATIVE',
                            'REGISTRAR', 'IDS', 'MAIN LIBRARY', 'SID', 'COE OLD BUILDING COMPLEX', 'KASAMA',
                            'CASS', 'CEBA', 'SET', 'COE', 'CED', 'GRADUATE DORM', 'PRISM', 'IPDM', 'BAHAY ALUMNI'
                        ];
                    @endphp
                    @foreach ($buildings as $building)
                        <option value="{{ $building }}">{{ $building }}</option>
                    @endforeach
                    --}}
                    @foreach(($historyBuildings ?? collect()) as $building)
                        <option value="{{ $building['code'] }}">{{ $building['code'] }}</option>
                    @endforeach
                </select>
            </label>

            <button id="exportBuildingBtn" class="ml-auto bg-maroon text-white px-4 py-2 rounded-lg font-semibold hover:bg-maroon-700">
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table id="building-table" class="min-w-full border border-gray-300 text-sm text-center">
                <thead class="bg-gray-100 font-semibold">
                    <tr>
                        <th class="border px-3 py-2">ID</th>
                        <th class="border px-3 py-2">Building</th>
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
                    {{-- Dummy Data --}}
                    @forelse(($historyBuildingLogs ?? collect()) as $log)
                        <tr class="hover:bg-gray-50" data-building="{{ $log->building ?? $log->id }}" data-date="{{ optional($log->recorded_at)->toDateString() ?? '—' }}">
                            <td class="border px-3 py-2">{{ $log->id }}</td>
                            <td class="border px-3 py-2">{{ $log->building ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ optional($log->recorded_at)->toDateString() ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ optional($log->recorded_at)->format('H:i') ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ optional($log->recorded_at)->copy()->addMinutes(15)->format('H:i') ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->frequency ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->v1 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->v2 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->v3 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->a1 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->a2 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->a3 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->pf1 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->pf2 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->pf3 ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->kwh ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="border px-3 py-4 text-center text-gray-500">No building logs available yet. Seed data or wait for IoT ingestion.</td>
                        </tr>
                    @endforelse
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

            <label class="flex items-center gap-2">
                <span class="font-medium">Building:</span>
                <select id="system-building-select" class="border rounded-lg px-3 py-1.5 focus:ring-2 focus:ring-maroon">
                    <option value="">All</option>
                    @foreach(($historyBuildings ?? collect()) as $building)
                        <option value="{{ $building['code'] }}">{{ $building['code'] }}</option>
                    @endforeach
                </select>
            </label>

            <button id="exportSystemBtn" class="ml-auto bg-maroon text-white px-4 py-2 rounded-lg font-semibold hover:bg-maroon-700">
                Export
            </button>
        </div>

        <div class="overflow-x-auto">
            <table id="system-table" class="min-w-full border border-gray-300 text-sm text-center">
                <thead class="bg-gray-100 font-semibold">
                    <tr>
                        <th class="border px-3 py-2">ID</th>
                        <th class="border px-3 py-2">Building</th>
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
                    {{-- Dummy Data --}}
                    @forelse(($historySystemLogs ?? collect()) as $log)
                        <tr class="hover:bg-gray-50" data-building="{{ $log->building ?? 'SYSTEM' }}" data-date="{{ $log->date ?? '—' }}">
                            <td class="border px-3 py-2">{{ $log->id }}</td>
                            <td class="border px-3 py-2">{{ $log->building ?? 'System' }}</td>
                            <td class="border px-3 py-2">{{ $log->date ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->time ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->time_ed ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->total_kw ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->total_kvar ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->total_kva ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->total_pf ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="border px-3 py-4 text-center text-gray-500">No system logs available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Export & Filter Scripts --}}
    <script>
        // Export buttons
        document.getElementById('exportBuildingBtn').addEventListener('click', () => {
            window.location.href = "{{ route('export.building') }}";
        });
        document.getElementById('exportSystemBtn').addEventListener('click', () => {
            window.location.href = "{{ route('export.system') }}";
        });

        // Building Data Filter
        const buildingSelect = document.getElementById('building-select');
        const buildingDateInput = document.getElementById('building-date');
        const buildingTable = document.getElementById('building-table').getElementsByTagName('tbody')[0];

        function filterBuildingTable() {
            const selectedBuilding = buildingSelect.value;
            const selectedDate = buildingDateInput.value;
            const rows = buildingTable.getElementsByTagName('tr');

            for (let row of rows) {
                const rowBuilding = row.getAttribute('data-building');
                const rowDate = row.getAttribute('data-date');

                let show = true;
                if (selectedBuilding && rowBuilding !== selectedBuilding) show = false;
                if (selectedDate && rowDate !== selectedDate) show = false;

                row.style.display = show ? '' : 'none';
            }
        }

        buildingSelect.addEventListener('change', filterBuildingTable);
        buildingDateInput.addEventListener('change', filterBuildingTable);
        filterBuildingTable();

        // System Data Filter
        const systemBuildingSelect = document.getElementById('system-building-select');
        const systemDateInput = document.getElementById('system-date');
        const systemTable = document.getElementById('system-table').getElementsByTagName('tbody')[0];

        function filterSystemTable() {
            const selectedBuilding = systemBuildingSelect.value;
            const selectedDate = systemDateInput.value;
            const rows = systemTable.getElementsByTagName('tr');

            for (let row of rows) {
                const rowBuilding = row.getAttribute('data-building');
                const rowDate = row.getAttribute('data-date');

                let show = true;
                if (selectedBuilding && rowBuilding !== selectedBuilding) show = false;
                if (selectedDate && rowDate !== selectedDate) show = false;

                row.style.display = show ? '' : 'none';
            }
        }

        systemBuildingSelect.addEventListener('change', filterSystemTable);
        systemDateInput.addEventListener('change', filterSystemTable);
        filterSystemTable();
    </script>
</section>
@endsection
