@extends('layouts.app')

@section('content')
@php
      $statusSeries = ($buildingStatus ?? collect())->values();
      $initialBuilding = $statusSeries->first();
      $buildingBootstrap = ($buildingBootstrap ?? collect())->values();
@endphp
<section id="map" class="space-y-6">
  <h1 class="text-3xl font-bold text-maroon">Campus Building Map</h1>

  <div class="grid md:grid-cols-3 gap-6">
    {{-- Map Display --}}
    <div class="md:col-span-2 bg-gray-50 border rounded-2xl shadow p-4 relative overflow-hidden">
      <div class="relative">
        <img src="{{ asset('images/msu-iit-map.png') }}" alt="MSU-IIT Campus Map" usemap="#msuMap"
          id="campusMap" class="rounded-xl w-full shadow-md">

        <!-- Accurate Clickable Map -->
        <map name="msuMap">

          <area target="_self" alt="CCS" title="CCS" 
                href="#" 
                coords="590,732,587,788,739,813,753,742,604,721" 
                shape="poly" data-building="CCS">

          <area target="_self" alt="CPI" title="CPI" 
                href="#" 
                coords="435,759,457,707,565,732,571,778" 
                shape="poly" data-building="CPI">

          <area target="_self" alt="OMDH" title="OMDH" 
                href="#" 
                coords="381,699,356,737,409,760,431,713" 
                shape="poly" data-building="OMDH">

          <area target="_self" alt="OBA" title="OBA" 
                href="#" 
                coords="331,696,325,721,343,733,365,700" 
                shape="poly" data-building="OBA">

          <area target="_self" alt="Gymnasium" title="Gymnasium" 
                href="#" 
                coords="921,569,857,713,1020,771,1073,601,926,563" 
                shape="poly" data-building="GYMNASIUM">

          <area target="_self" alt="CSM" title="CSM" 
                href="#" 
                coords="601,611,545,680,694,715,746,637" 
                shape="poly" data-building="CSM">

          <area target="_self" alt="KTTO" title="KTTO" 
                href="#" 
                coords="299,702,259,740,279,756,321,719,321,734" 
                shape="poly" data-building="KTTO">

          <area target="_self" alt="Administrative" title="Administrative" 
                href="#" 
                coords="93,634,51,664,89,688,135,661" 
                shape="poly" data-building="ADMIN">

          <area target="_self" alt="Registrar" title="Registrar" 
                href="#" 
                coords="137,611,116,631,150,653,161,628" 
                shape="poly" data-building="REGISTRAR">

          <area target="_self" alt="IDS" title="IDS" 
                href="#" 
                coords="207,524,119,596,267,604,313,588,297,599,321,582" 
                shape="poly" data-building="IDS">

          <area target="_self" alt="Main Library" title="Main Library" 
                href="#" 
                coords="194,607,168,660,252,713,275,645" 
                shape="poly" data-building="MAIN LIBRARY">

          <area target="_self" alt="SID" title="SID" 
                href="#" 
                coords="323,596,275,623,309,665,357,608" 
                shape="poly" data-building="SID">

          <area target="_self" alt="COE Old Building Complex" 
                title="COE Old Building Complex"
                href="#" 
                coords="428,583,392,656,514,686,561,618" 
                shape="poly" data-building="COE OLD BUILDING COMPLEX">

          <area target="_self" alt="KASAMA" title="KASAMA" 
                href="#" 
                coords="397,488,352,541,381,549,413,498" 
                shape="poly" data-building="KASAMA">

          <area target="_self" alt="CASS" title="CASS" 
                href="#" 
                coords="362,320,337,435,455,480,502,339" 
                shape="poly" data-building="CASS">

          <area target="_self" alt="CEBA" title="CEBA" 
                href="#" 
                coords="533,381,439,564,579,587,622,502" 
                shape="poly" data-building="CEBA">

          <area target="_self" alt="SET" title="SET" 
                href="#" 
                coords="631,262,573,356,781,378,787,295" 
                shape="poly" data-building="SET">

          <area target="_self" alt="COE" title="COE" 
                href="#" 
                coords="711,380,616,595,774,629,825,395" 
                shape="poly" data-building="COE">

          <area target="_self" alt="CED" title="CED" 
                href="#" 
                coords="883,342,831,469,956,486,1013,385" 
                shape="poly" data-building="CED">

          <area target="_self" alt="Graduate Dorm" title="Graduate Dorm" 
                href="#" 
                coords="991,197,970,274,1052,281,1061,216" 
                shape="poly" data-building="GRADUATE DORM">

          <area target="_self" alt="PRISM" title="PRISM" 
                href="#" 
                coords="1120,246,1105,320,1236,362,1252,293" 
                shape="poly" data-building="PRISM">

          <area target="_self" alt="IPDM" title="IPDM" 
                href="#" 
                coords="1361,341,1325,378,1399,411,1415,376" 
                shape="poly" data-building="IPDM">

          <area target="_self" alt="Bahay Alumni" title="Bahay Alumni" 
                href="#" 
                coords="1325,424,1302,455,1361,479,1384,444" 
                shape="poly" data-building="BAHAY ALUMNI">

        </map>

        <!-- Tooltip -->
        <div id="tooltip" class="hidden absolute bg-maroon text-white text-xs px-2 py-1 rounded shadow-lg"></div>
      </div>

      <p class="text-sm italic text-gray-600 mt-2">Click a building to view details.</p>
    </div>

    {{-- Building Status --}}
    <div class="bg-white border rounded-2xl shadow p-4 overflow-y-auto max-h-[600px]">
      <h2 class="text-lg font-semibold text-maroon mb-2">Building Status</h2>
                  <ul id="buildingStatus" class="space-y-1 text-sm">
                        {{--
                        <li><strong>COE</strong> (Engineering) — <span class="status online">Online</span></li>
                        <li><strong>CCS</strong> (Computer Studies) — <span class="status idle">Idle</span></li>
                        <li><strong>CED</strong> (Education) — <span class="status offline">Offline</span></li>
                        <li><strong>CBAA</strong> (Business) — <span class="status idle">Idle</span></li>
                        <li><strong>CSM</strong> (Science & Math) — <span class="status online">Online</span></li>
                        <li><strong>CASS</strong> (Arts & Social Sciences) — <span class="status online">Online</span></li>
                        <li><strong>MICEL</strong> — <span class="status online">Online</span></li>
                        <li><strong>GYMNASIUM</strong> — <span class="status online">Online</span></li>
                        --}}
                        @forelse($statusSeries as $building)
                              @php
                                    $statusLabel = strtoupper($building['status'] ?? 'unknown');
                                    $statusReason = $building['status_reason'] ?? '';
                                    $statusClass = $building['status'] ?? 'idle';
                              @endphp
                              <li data-building="{{ $building['code'] }}" class="flex flex-col gap-0.5">
                                    <div>
                                          <strong>{{ $building['code'] }}</strong>
                                          <span class="text-gray-600">({{ $building['name'] }})</span>
                                          — <span class="status {{ $statusClass }}">{{ $statusLabel }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $statusReason }}</span>
                              </li>
                        @empty
                              <li class="text-gray-500">No building telemetry available yet.</li>
                        @endforelse
                  </ul>

        <hr class="my-3">
        <div id="building-info" class="text-sm text-gray-700"></div>
    </div>
  </div>

  {{-- Interactivity --}}
      <script>
            window.appConfig = window.appConfig || {};
            window.appConfig.buildings = {!! json_encode($buildingBootstrap->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!};
      </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/image-map-resizer/1.0.10/js/imageMapResizer.min.js"></script>
  <script>
  document.addEventListener("DOMContentLoaded", () => {
    imageMapResize(); // keeps map responsive

            const tooltip = document.getElementById("tooltip");
            const info = document.getElementById("building-info");
            const mapImg = document.getElementById("campusMap");
            const statusList = document.getElementById("buildingStatus");

            const state = {
                  buildings: Array.isArray(window.appConfig?.buildings) ? window.appConfig.buildings : [],
                  selectedCode: Array.isArray(window.appConfig?.buildings) && window.appConfig.buildings.length
                        ? window.appConfig.buildings[0].code
                        : null,
            };

            const normalizeCode = (value) => (value || '').toString().trim().toUpperCase();

            const getBuildingByCode = (code) => {
                  if (!code) return null;
                  const target = normalizeCode(code);
                  return state.buildings.find((building) => normalizeCode(building.code) === target) ?? null;
            };

            const renderBuildingInfo = (building) => {
                  if (!info) return;

                  if (!building) {
                        return;
                  }

                  state.selectedCode = building.code;

                  const updatedAt = building.latest_reading_at
                        ? new Date(building.latest_reading_at).toLocaleString()
                        : 'N/A';

                  const statusNote = building.status_reason ?? '';

                  info.innerHTML = '';
                  const title = document.createElement('strong');
                  title.textContent = building.code;
                  const lineBreak = document.createElement('br');
                  const nameNode = document.createTextNode(building.name ?? 'Unknown building');
                  const lineBreak2 = document.createElement('br');
                  const detail = document.createElement('span');
                  detail.className = 'text-xs text-gray-500';
                  detail.textContent = `${statusNote} (${updatedAt})`;

                  info.append(title, lineBreak, nameNode, lineBreak2, detail);
            };

            const statusClass = (status) => ({
                  online: 'online',
                  idle: 'idle',
                  offline: 'offline',
            })[status] ?? 'idle';

            const renderStatusList = (buildings) => {
                  if (!statusList) {
                        return;
                  }

                  statusList.innerHTML = '';

                  if (!buildings?.length) {
                        const emptyRow = document.createElement('li');
                        emptyRow.className = 'text-gray-500';
                        emptyRow.textContent = 'No building telemetry available yet.';
                        statusList.appendChild(emptyRow);
                        return;
                  }

                  buildings.forEach((building) => {
                        const li = document.createElement('li');
                        li.dataset.building = building.code;
                        li.className = 'flex flex-col gap-0.5 rounded-lg px-2 py-1';

                        const header = document.createElement('div');
                        const codeNode = document.createElement('strong');
                        codeNode.textContent = building.code;
                        const nameNode = document.createElement('span');
                        nameNode.className = 'text-gray-600';
                        nameNode.textContent = ` (${building.name})`;

                        const badge = document.createElement('span');
                        badge.className = `status ${statusClass(building.status)}`;
                        badge.textContent = (building.status ?? 'unknown').toUpperCase();

                        header.append(codeNode);
                        header.appendChild(nameNode);
                        header.append(' — ');
                        header.appendChild(badge);

                        const reason = document.createElement('span');
                        reason.className = 'text-xs text-gray-500';
                        reason.textContent = building.status_reason ?? '';

                        li.append(header, reason);

                        if (state.selectedCode && normalizeCode(state.selectedCode) === normalizeCode(building.code)) {
                              li.classList.add('status-row-active');
                        }

                        li.addEventListener('click', () => {
                              renderBuildingInfo(building);
                              renderStatusList(state.buildings);
                        });

                        statusList.appendChild(li);
                  });
            };

            const refreshBuildingStatus = async () => {
                  try {
                        const response = await fetch('/api/buildings', {
                              headers: { 'Accept': 'application/json' },
                        });

                        if (!response.ok) {
                              throw new Error(await response.text());
                        }

                        const payload = await response.json();
                        if (Array.isArray(payload)) {
                              state.buildings = payload;
                              renderStatusList(state.buildings);
                              const selected = getBuildingByCode(state.selectedCode) ?? state.buildings[0] ?? null;
                              renderBuildingInfo(selected);
                        }
                  } catch (error) {
                        console.warn('Unable to refresh building status', error);
                  }
            };

            renderStatusList(state.buildings);
            renderBuildingInfo(getBuildingByCode(state.selectedCode) ?? state.buildings[0] ?? null);
            refreshBuildingStatus();
            setInterval(refreshBuildingStatus, 60000);

            const escapeSelector = (value) => {
                  if (window.CSS && typeof window.CSS.escape === 'function') {
                        return window.CSS.escape(value);
                  }
                  return (value || '').toString().replace(/([^a-zA-Z0-9_-])/g, '\\$1');
            };

            const highlightStatusRow = (code) => {
                  if (!statusList) return;
                  const target = statusList.querySelector(`[data-building="${escapeSelector(code)}"]`);
                  if (!target) return;
                  target.classList.add('status-row-active');
                  target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                  setTimeout(() => target.classList.remove('status-row-active'), 1200);
            };

            document.querySelectorAll('area').forEach((area) => {
                  area.addEventListener('mousemove', (e) => {
                        tooltip.textContent = area.alt;
                        const rect = mapImg.getBoundingClientRect();
                        tooltip.style.left = `${e.pageX - rect.left + 15 - window.scrollX}px`;
                        tooltip.style.top = `${e.pageY - rect.top + 15 - window.scrollY}px`;
                        tooltip.classList.remove('hidden');
                  });

                  area.addEventListener('mouseleave', () => tooltip.classList.add('hidden'));

                  area.addEventListener('click', (e) => {
                        e.preventDefault();
                        const building = getBuildingByCode(area.dataset.building);
                        renderBuildingInfo(building ?? { code: area.dataset.building, name: area.title });
                        renderStatusList(state.buildings);
                        highlightStatusRow(area.dataset.building);
                  });
            });
  });
  </script>

  <style>
    area:hover { cursor: pointer; outline: 2px solid #7a0e0e; }
    .status {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 9999px;
      font-size: 0.7rem;
      font-weight: bold;
    }
    .status.online { background: #d1fae5; color: #047857; }
    .status.offline { background: #fee2e2; color: #b91c1c; }
    .status.idle { background: #fef9c3; color: #a16207; }
            .status-row-active {
                  background: rgba(161, 29, 29, 0.08);
                  border: 1px solid #a11d1d;
                  font-weight: 600;
            }
  </style>
</section>
@endsection
