const palette = ['#581313', '#f28c38', '#e03a3a', '#3fa4ff', '#ff7a7a'];

const formatters = {
  integer: new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }),
  decimal: new Intl.NumberFormat('en-US', { minimumFractionDigits: 4, maximumFractionDigits: 4 }),
};

const state = {
  initialized: false,
  mainChart: null,
  prevChart: null,
};

const hasWindowChart = () => typeof window !== 'undefined' && typeof window.Chart !== 'undefined';

const setText = (id, textContent) => {
  const node = document.getElementById(id);
  if (!node) {
    return;
  }

  node.textContent = textContent;
};

const formatNumber = (value, formatter = formatters.integer) => formatter.format(Number(value ?? 0));

const buildColors = (labels = []) => labels.map((_, idx) => palette[idx % palette.length]);

const escapeHtml = (value) => String(value ?? '')
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;')
  .replace(/'/g, '&#039;');

const updateLegend = (labels = []) => {
  const legendEl = document.getElementById('dashboardLegend');
  if (!legendEl) {
    return;
  }

  if (!labels.length) {
    legendEl.innerHTML = '<span class="text-gray-500">No readings available yet</span>';
    return;
  }

  const colors = buildColors(labels);
  const markup = labels.map((label, index) => (
    `<div class="flex items-center gap-1"><span class="w-4 h-4 inline-block rounded-sm" style="background-color:${colors[index]}"></span> ${escapeHtml(label)}</div>`
  )).join('');

  legendEl.innerHTML = markup;
};

const refreshCharts = (chartPayload) => {
  if (!chartPayload || !hasWindowChart()) {
    return;
  }

  const Chart = window.Chart;
  const mainCanvas = document.getElementById('buildingChart');
  const prevCanvas = document.getElementById('prevMonthChart');

  if (mainCanvas) {
    if (!state.mainChart) {
      state.mainChart = new Chart(mainCanvas.getContext('2d'), {
        type: 'bar',
        data: { labels: chartPayload.labels ?? [], datasets: [{ label: 'kW', data: chartPayload.current ?? [], backgroundColor: buildColors(chartPayload.labels ?? []), borderRadius: 8 }] },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true, title: { display: true, text: 'Kilowatt-Hour (kW)' } } },
          plugins: { legend: { display: false } },
        },
      });
    } else {
      state.mainChart.data.labels = chartPayload.labels ?? [];
      state.mainChart.data.datasets[0].data = chartPayload.current ?? [];
      state.mainChart.data.datasets[0].backgroundColor = buildColors(chartPayload.labels ?? []);
      state.mainChart.update('none');
    }
  }

  if (prevCanvas) {
    if (!state.prevChart) {
      state.prevChart = new Chart(prevCanvas.getContext('2d'), {
        type: 'bar',
        data: { labels: chartPayload.labels ?? [], datasets: [{ label: 'kW', data: chartPayload.previous ?? [], backgroundColor: buildColors(chartPayload.labels ?? []), borderRadius: 8 }] },
        options: {
          responsive: true,
          scales: { y: { beginAtZero: true } },
          plugins: { legend: { display: false } },
        },
      });
    } else {
      state.prevChart.data.labels = chartPayload.labels ?? [];
      state.prevChart.data.datasets[0].data = chartPayload.previous ?? [];
      state.prevChart.data.datasets[0].backgroundColor = buildColors(chartPayload.labels ?? []);
      state.prevChart.update('none');
    }
  }

  updateLegend(chartPayload.labels ?? []);
};

const updateTotals = (totals = {}, generatedAt = null) => {
  setText('dashboardTotalPower', formatNumber(totals.total_power));
  setText('dashboardAvgPf', formatNumber(totals.avg_pf, formatters.decimal));
  setText('dashboardLastMonth', formatNumber(totals.last_month_kwh));
  setText('dashboardThisMonth', formatNumber(totals.this_month_kwh));

  if (generatedAt) {
    const timestamp = new Date(generatedAt);
    if (!Number.isNaN(timestamp.getTime())) {
      setText('dashboardUpdatedAt', `Last updated ${timestamp.toLocaleString()}`);
    }
  }
};

const renderDashboard = (payload = {}) => {
  if (!payload) {
    return;
  }

  refreshCharts(payload.chart ?? {});
  updateTotals(payload.totals ?? {}, payload.generated_at ?? null);
};

export const mountDashboardRealtime = () => {
  if (state.initialized) {
    return;
  }

  const dashboardEl = document.getElementById('dashboard');
  if (!dashboardEl) {
    return;
  }

  const bootstrapPayload = window.dashboardBootstrap ?? null;
  if (bootstrapPayload) {
    renderDashboard(bootstrapPayload);
  }

  if (window.Echo) {
    window.Echo.channel('dashboard.metrics')
      .listen('.ReadingIngested', ({ payload }) => renderDashboard(payload));
  }

  state.initialized = true;
};
