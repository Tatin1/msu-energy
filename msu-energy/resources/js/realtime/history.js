const setEmptyRow = (tbody, message, colspan) => {
  if (!tbody) {
    return;
  }

  tbody.innerHTML = `<tr><td colspan="${colspan}" class="border px-3 py-4 text-center text-gray-500">${message}</td></tr>`;
};

const escapeHtml = (value) => String(value ?? '')
  .replace(/&/g, '&amp;')
  .replace(/</g, '&lt;')
  .replace(/>/g, '&gt;')
  .replace(/"/g, '&quot;')
  .replace(/'/g, '&#039;');

const formatCell = (value) => {
  if (value === null || value === undefined || String(value).trim() === '') {
    return '—';
  }

  return escapeHtml(value);
};

const applyBuildingFilters = () => {
  const select = document.getElementById('building-select');
  const dateInput = document.getElementById('building-date');
  const rows = document.querySelectorAll('#building-table tbody tr');

  if (!select || !dateInput || !rows.length) {
    return;
  }

  const buildingFilter = select.value;
  const dateFilter = dateInput.value;

  rows.forEach((row) => {
    const matchesBuilding = !buildingFilter || row.getAttribute('data-building') === buildingFilter;
    const matchesDate = !dateFilter || row.getAttribute('data-date') === dateFilter;
    row.style.display = matchesBuilding && matchesDate ? '' : 'none';
  });
};

const applySystemFilters = () => {
  const select = document.getElementById('system-building-select');
  const dateInput = document.getElementById('system-date');
  const rows = document.querySelectorAll('#system-table tbody tr');

  if (!select || !dateInput || !rows.length) {
    return;
  }

  const buildingFilter = select.value;
  const dateFilter = dateInput.value;

  rows.forEach((row) => {
    const matchesBuilding = !buildingFilter || row.getAttribute('data-building') === buildingFilter;
    const matchesDate = !dateFilter || row.getAttribute('data-date') === dateFilter;
    row.style.display = matchesBuilding && matchesDate ? '' : 'none';
  });
};

const renderBuildingHistory = (tbody, rows = []) => {
  if (!tbody) {
    return;
  }

  if (!rows.length) {
    setEmptyRow(tbody, 'No building logs match the current filters.', 16);
    return;
  }

  tbody.innerHTML = rows.map((row) => {
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
  applyBuildingFilters();
};

const renderSystemHistory = (tbody, rows = []) => {
  if (!tbody) {
    return;
  }

  if (!rows.length) {
    setEmptyRow(tbody, 'No system logs match the current filters.', 9);
    return;
  }

  tbody.innerHTML = rows.map((row) => {
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
  applySystemFilters();
};

export const mountHistoryRealtime = () => {
  const buildingBody = document.querySelector('#building-table tbody');
  const systemBody = document.querySelector('#system-table tbody');

  if (!buildingBody && !systemBody) {
    return;
  }

  if (!window.Echo) {
    return;
  }

  if (buildingBody) {
    window.Echo.channel('building.logs')
      .listen('BuildingLogRecorded', ({ payload }) => renderBuildingHistory(buildingBody, payload ?? []));
  }

  if (systemBody) {
    window.Echo.channel('system.logs')
      .listen('SystemLogRecorded', ({ payload }) => renderSystemHistory(systemBody, payload ?? []));
  }
};
