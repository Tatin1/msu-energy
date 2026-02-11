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
                    @forelse(($historyBuildingLogs ?? collect()) as $log)
                        <tr class="hover:bg-gray-50" data-building="{{ $log->building ?? $log->id }}" data-date="{{ optional($log->date)->toDateString() ?? '—' }}">
                            <td class="border px-3 py-2">{{ $log->id }}</td>
                            <td class="border px-3 py-2">{{ $log->building ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->date ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->time ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->time_ed ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $log->f ?? '—' }}</td>
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
                            <td colspan="16" class="border px-3 py-4 text-center text-gray-500">No building logs available yet.</td>
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
    <script>
        const historyConfig = {
            buildingEndpoint: "{{ url('/api/history/building-logs') }}",
            systemEndpoint: "{{ url('/api/history/system-logs') }}",
            exportBuildingRoute: "{{ route('export.building') }}",
            exportSystemRoute: "{{ route('export.system') }}",
            buildingColumns: 16,
            systemColumns: 9,
            perPage: 50,
        };

        // const buildingSelect = document.getElementById('building-select');
        // const buildingDateInput = document.getElementById('building-date');
        const buildingTableBody = document.querySelector('#building-table tbody');
        const exportBuildingBtn = document.getElementById('exportBuildingBtn');

        // const systemBuildingSelect = document.getElementById('system-building-select');
        // const systemDateInput = document.getElementById('system-date');
        const systemTableBody = document.querySelector('#system-table tbody');
        const exportSystemBtn = document.getElementById('exportSystemBtn');

        const defaultRequestOptions = {
            headers: {
                'Accept': 'application/json',
            },
        };

        function escapeHtml(value) {
            if (value === null || value === undefined) {
                return '';
            }

            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatCell(value) {
            if (value === null || value === undefined || String(value).trim() === '') {
                return '—';
            }

            return escapeHtml(value);
        }

        function buildQueryString(filters, extras = {}) {
            const params = new URLSearchParams();
            const payload = { ...filters, ...extras };

            Object.entries(payload).forEach(([key, value]) => {
                if (value !== undefined && value !== null && String(value).trim() !== '') {
                    params.set(key, value);
                }
            });

            return params.toString();
        }

        function setTableMessage(tbody, message, colspan) {
            tbody.innerHTML = `<tr><td colspan="${colspan}" class="border px-3 py-4 text-center text-gray-500">${message}</td></tr>`;
        }

        function renderBuildingRows(rows) {
            if (!rows.length) {
                setTableMessage(buildingTableBody, 'No building logs match the current filters.', historyConfig.buildingColumns);
                return;
            }

            buildingTableBody.innerHTML = rows.map((row) => {
                const buildingValue = row.building ?? '—';
                const dateValue = row.date ?? '—';

                return `
                    <tr class="hover:bg-gray-50" data-building="${escapeHtml(buildingValue)}" data-date="${escapeHtml(dateValue)}">
                        <td class="border px-3 py-2">${formatCell(row.id)}</td>
                        <td class="border px-3 py-2">${formatCell(buildingValue)}</td>
                        <td class="border px-3 py-2">${formatCell(dateValue)}</td>
                        <td class="border px-3 py-2">${formatCell(row.time)}</td>
                        <td class="border px-3 py-2">${formatCell(row.time_ed)}</td>
                        <td class="border px-3 py-2">${formatCell(row.f)}</td>
                        <td class="border px-3 py-2">${formatCell(row.v1)}</td>
                        <td class="border px-3 py-2">${formatCell(row.v2)}</td>
                        <td class="border px-3 py-2">${formatCell(row.v3)}</td>
                        <td class="border px-3 py-2">${formatCell(row.a1)}</td>
                        <td class="border px-3 py-2">${formatCell(row.a2)}</td>
                        <td class="border px-3 py-2">${formatCell(row.a3)}</td>
                        <td class="border px-3 py-2">${formatCell(row.pf1)}</td>
                        <td class="border px-3 py-2">${formatCell(row.pf2)}</td>
                        <td class="border px-3 py-2">${formatCell(row.pf3)}</td>
                        <td class="border px-3 py-2">${formatCell(row.kwh)}</td>
                    </tr>
                `;
            }).join('');
        }

        function renderSystemRows(rows) {
            if (!rows.length) {
                setTableMessage(systemTableBody, 'No system logs match the current filters.', historyConfig.systemColumns);
                return;
            }

            systemTableBody.innerHTML = rows.map((row) => {
                const buildingValue = row.building ?? 'System';
                const dateValue = row.date ?? '—';

                return `
                    <tr class="hover:bg-gray-50" data-building="${escapeHtml(buildingValue)}" data-date="${escapeHtml(dateValue)}">
                        <td class="border px-3 py-2">${formatCell(row.id)}</td>
                        <td class="border px-3 py-2">${formatCell(buildingValue)}</td>
                        <td class="border px-3 py-2">${formatCell(dateValue)}</td>
                        <td class="border px-3 py-2">${formatCell(row.time)}</td>
                        <td class="border px-3 py-2">${formatCell(row.time_ed)}</td>
                        <td class="border px-3 py-2">${formatCell(row.total_kw)}</td>
                        <td class="border px-3 py-2">${formatCell(row.total_kvar)}</td>
                        <td class="border px-3 py-2">${formatCell(row.total_kva)}</td>
                        <td class="border px-3 py-2">${formatCell(row.total_pf)}</td>
                    </tr>
                `;
            }).join('');
        }

        async function loadBuildingLogs() {
            setTableMessage(buildingTableBody, 'Loading building logs…', historyConfig.buildingColumns);

            const query = buildQueryString({
                building: buildingSelect.value,
                date: buildingDateInput.value,
            }, { per_page: historyConfig.perPage });

            const url = query
                ? `${historyConfig.buildingEndpoint}?${query}`
                : `${historyConfig.buildingEndpoint}?per_page=${historyConfig.perPage}`;

            try {
                const response = await fetch(url, defaultRequestOptions);
                if (!response.ok) {
                    throw new Error('Unable to fetch building logs');
                }

                const payload = await response.json();
                renderBuildingRows(payload.data ?? []);
            } catch (error) {
                console.error(error);
                setTableMessage(buildingTableBody, 'Unable to load building logs. Please try again.', historyConfig.buildingColumns);
            }
        }

        async function loadSystemLogs() {
            setTableMessage(systemTableBody, 'Loading system logs…', historyConfig.systemColumns);

            const query = buildQueryString({
                building: systemBuildingSelect.value,
                date: systemDateInput.value,
            }, { per_page: historyConfig.perPage });

            const url = query
                ? `${historyConfig.systemEndpoint}?${query}`
                : `${historyConfig.systemEndpoint}?per_page=${historyConfig.perPage}`;

            try {
                const response = await fetch(url, defaultRequestOptions);
                if (!response.ok) {
                    throw new Error('Unable to fetch system logs');
                }

                const payload = await response.json();
                renderSystemRows(payload.data ?? []);
            } catch (error) {
                console.error(error);
                setTableMessage(systemTableBody, 'Unable to load system logs. Please try again.', historyConfig.systemColumns);
            }
        }

        function exportWithFilters(baseUrl, filters) {
            const query = buildQueryString(filters);
            const target = query ? `${baseUrl}?${query}` : baseUrl;
            window.location.href = target;
        }

        buildingSelect.addEventListener('change', loadBuildingLogs);
        buildingDateInput.addEventListener('change', loadBuildingLogs);
        exportBuildingBtn.addEventListener('click', () => {
            exportWithFilters(historyConfig.exportBuildingRoute, {
                building: buildingSelect.value,
                date: buildingDateInput.value,
            });
        });

        systemBuildingSelect.addEventListener('change', loadSystemLogs);
        systemDateInput.addEventListener('change', loadSystemLogs);
        exportSystemBtn.addEventListener('click', () => {
            exportWithFilters(historyConfig.exportSystemRoute, {
                building: systemBuildingSelect.value,
                date: systemDateInput.value,
            });
        });

        loadBuildingLogs();
        loadSystemLogs();
    </script>
</section>
@endsection
