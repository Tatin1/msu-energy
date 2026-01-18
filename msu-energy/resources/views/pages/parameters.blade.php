@extends('layouts.app')

@section('content')
@php
  $buildingOptions = ($parameterBuildings ?? collect())->map(fn ($building) => [
    'code' => $building['code'] ?? $building['id'],
    'label' => ($building['code'] ?? 'UNKNOWN').(
      isset($building['name']) ? ' – '.$building['name'] : ''
    ),
  ]);
  $defaultBuilding = $buildingOptions->first();
@endphp
<section id="parameters">
  <h1 class="text-3xl font-bold text-maroon mb-6">Electrical Parameters</h1>

  {{-- Controls --}}
  <div class="bg-panel p-4 rounded-2xl shadow mb-6">
    <div class="flex flex-wrap gap-4 items-end">
      <div>
        <label class="block text-sm font-bold mb-1 text-gray-800">Building</label>
        <select id="paramBuilding" class="input border-gray-400 rounded-lg px-3 py-2">
          {{--
          <option value="CCS">CCS</option>
          <option value="CPI">CPI</option>
          <option value="OMDH">OMDH</option>
          <option value="OBA">OBA</option>
          <option value="GYMNASIUM">Gymnasium</option>
          <option value="CSM">CSM</option>
          <option value="KTTO">KTTO</option>
          <option value="ADMINISTRATIVE">Administrative</option>
          <option value="REGISTRAR">Registrar</option>
          <option value="IDS">IDS</option>
          <option value="MAIN LIBRARY">Main Library</option>
          <option value="SID">SID</option>
          <option value="COE OLD BUILDING COMPLEX">COE Old Building Complex</option>
          <option value="KASAMA">KASAMA</option>
          <option value="CASS">CASS</option>
          <option value="CEBA">CEBA</option>
          <option value="SET">SET</option>
          <option value="COE">COE</option>
          <option value="CED">CED</option>
          <option value="GRADUATE DORM">Graduate Dorm</option>
          <option value="PRISM">PRISM</option>
          <option value="IPDM">IPDM</option>
          <option value="BAHAY ALUMNI">Bahay Alumni</option>
          --}}
          @forelse($buildingOptions as $option)
            <option value="{{ $option['code'] }}">{{ $option['label'] }}</option>
          @empty
            <option disabled>No buildings available</option>
          @endforelse
        </select>
      </div>

      <button id="btnGetData"
        class="btn bg-maroon text-white font-bold px-5 py-2 rounded-xl hover:bg-maroon-700">
        Refresh
      </button>
    </div>
  </div>

  {{-- Layout Grid --}}
  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Parameters Table --}}
    <div class="card bg-panel p-5 rounded-2xl shadow">
      <h2 class="text-xl font-semibold mb-4">Transformer 3φ Readings</h2>
      <table id="paramTable" class="w-full border-collapse text-sm">
        <thead class="bg-gray-100 font-semibold">
          <tr>
            <th>Metric</th>
            <th>Value</th>
            <th>Unit</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>

    {{-- Energy Consumption Chart --}}
    <div class="card bg-panel p-5 rounded-2xl shadow">
      <h2 class="text-xl font-semibold mb-4">Energy Consumption (kW)</h2>
      <canvas id="paramEnergyChart" height="180"></canvas>

      <div class="grid grid-cols-2 gap-4 mt-6">
  <div>
    <span class="text-sm">Last Month (kW)</span>
    <div id="paramLastMonth" class="font-bold text-lg">0</div>
  </div>
  <div>
    <span class="text-sm">This Month (kW)</span>
    <div id="paramThisMonth" class="font-bold text-lg">0</div>
  </div>
</div>

    </div>
  </div>

  {{-- Script --}}
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const paramTableBody = document.querySelector("#paramTable tbody");
      const ctx = document.getElementById("paramEnergyChart").getContext("2d");
      const buildingSelect = document.getElementById("paramBuilding");
      const refreshBtn = document.getElementById("btnGetData");
      const lastMonthNode = document.getElementById("paramLastMonth");
      const thisMonthNode = document.getElementById("paramThisMonth");
      const statusNode = document.createElement('p');
      statusNode.className = 'text-xs text-gray-500 mt-2';
      refreshBtn.parentElement?.appendChild(statusNode);

      let chart;
      let currentDataset = null;
      let isLoading = false;

      const notify = (message, tone = 'default') => {
        const tones = {
          default: 'text-gray-500',
          warn: 'text-amber-600',
          error: 'text-red-600',
          success: 'text-emerald-600',
        };
        statusNode.className = `text-xs mt-2 ${tones[tone] ?? tones.default}`;
        statusNode.textContent = message;
      };

      const formatNumber = (value, options = {}) => new Intl.NumberFormat('en-PH', options).format(value ?? 0);

      const renderParameters = (payload) => {
        const meters = payload?.meters ?? [];
        const readingSource = meters.flatMap((meter) => meter.readings ?? []);
        const latestReading = readingSource.sort((a, b) => new Date(b.recorded_at) - new Date(a.recorded_at))[0] ?? null;

        if (!latestReading) {
          paramTableBody.innerHTML = '<tr><td colspan="3" class="text-center text-gray-500">No readings found for this building. Seed dummy data or wait for IoT ingestion.</td></tr>';
          lastMonthNode.textContent = '0';
          thisMonthNode.textContent = '0';
          if (chart) chart.destroy();
          chart = null;
          return;
        }

        const rows = [
          ['Frequency', latestReading.frequency ?? '—', 'Hz'],
          ['Phase Voltages (V1,V2,V3)', [latestReading.voltage1, latestReading.voltage2, latestReading.voltage3].filter((v) => v !== null && v !== undefined).map((v) => Number(v).toFixed(1)).join(', '), 'V'],
          ['Line Currents (A1,A2,A3)', [latestReading.current1, latestReading.current2, latestReading.current3].filter((v) => v !== null && v !== undefined).map((v) => Number(v).toFixed(1)).join(', '), 'A'],
          ['Line PFs (PF1,PF2,PF3)', [latestReading.pf1, latestReading.pf2, latestReading.pf3].filter((v) => v !== null && v !== undefined).map((v) => Number(v).toFixed(3)).join(', '), '—'],
          ['3φ Active Power', latestReading.active_power ?? '—', 'kW'],
          ['3φ Reactive Power', latestReading.reactive_power ?? '—', 'kVAr'],
          ['3φ Apparent Power', latestReading.apparent_power ?? '—', 'kVA'],
          ['3φ Power Factor', latestReading.power_factor ?? '—', '—'],
          ['THD (Voltage)', latestReading.thd_voltage ?? '—', '%'],
          ['THD (Current)', latestReading.thd_current ?? '—', '%'],
        ];

        paramTableBody.innerHTML = rows.map(([metric, value, unit]) => `<tr><td>${metric}</td><td class="font-bold">${value ?? '—'}</td><td>${unit}</td></tr>`).join('');

        const dailySeries = buildDailySeries(readingSource);
        lastMonthNode.textContent = formatNumber(dailySeries.lastMonthTotal, { maximumFractionDigits: 0 });
        thisMonthNode.textContent = formatNumber(dailySeries.thisMonthTotal, { maximumFractionDigits: 0 });

        if (chart) chart.destroy();
        chart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: dailySeries.labels,
            datasets: [
              {
                label: 'Last Month',
                data: dailySeries.lastMonth,
                fill: true,
                backgroundColor: 'rgba(161, 29, 29, 0.4)',
                borderColor: '#a11d1d',
                tension: 0.3,
                stack: 'stack',
              },
              {
                label: 'This Month',
                data: dailySeries.thisMonth,
                fill: true,
                backgroundColor: 'rgba(29, 161, 29, 0.4)',
                borderColor: '#1da11d',
                tension: 0.3,
                stack: 'stack',
              },
            ],
          },
          options: {
            responsive: true,
            scales: {
              x: { title: { display: true, text: 'Day of Month' } },
              y: {
                beginAtZero: true,
                stacked: true,
                title: { display: true, text: 'kWh' },
              },
            },
            plugins: {
              tooltip: { mode: 'index', intersect: false },
              legend: { position: 'top' },
            },
          },
        });
      };

      const buildDailySeries = (readings = []) => {
        const now = new Date();
        const currentMonth = now.getMonth();
        const previousMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1).getMonth();

        const buckets = readings.reduce((carry, reading) => {
          if (!reading?.kwh || !reading?.recorded_at) {
            return carry;
          }
          const recordedAt = new Date(reading.recorded_at);
          const month = recordedAt.getMonth();
          const day = recordedAt.getDate();
          if (!carry[month]) {
            carry[month] = {};
          }
          carry[month][day] = (carry[month][day] ?? 0) + Number(reading.kwh);
          return carry;
        }, {});

        const labels = Array.from({ length: 31 }, (_, i) => i + 1);
        const thisMonth = labels.map((day) => buckets[currentMonth]?.[day] ?? 0);
        const lastMonth = labels.map((day) => buckets[previousMonth]?.[day] ?? 0);

        return {
          labels,
          thisMonth,
          lastMonth,
          thisMonthTotal: thisMonth.reduce((sum, value) => sum + value, 0),
          lastMonthTotal: lastMonth.reduce((sum, value) => sum + value, 0),
        };
      };

      const fetchParameters = async () => {
        const buildingCode = buildingSelect.value;
        if (!buildingCode) {
          notify('Select a building to load parameters', 'warn');
          return;
        }

        if (isLoading) {
          return;
        }

        isLoading = true;
        notify(`Loading data for ${buildingCode}…`);

        try {
          const response = await fetch(`/api/buildings/${encodeURIComponent(buildingCode)}/parameters`, {
            headers: { 'Accept': 'application/json' },
          });

          if (!response.ok) {
            throw new Error(await response.text());
          }

          currentDataset = await response.json();
          renderParameters(currentDataset);
          notify(`Loaded ${buildingCode} parameters`, 'success');
        } catch (error) {
          console.error(error);
          notify('Failed to load building parameters. Check the console or seed data.', 'error');
        } finally {
          isLoading = false;
        }
      };

      refreshBtn.addEventListener('click', fetchParameters);
      fetchParameters();
    });
  </script>
</section>
@endsection
