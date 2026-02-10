const STATUS_BADGES = {
  Normal: 'bg-green-100 text-green-800',
  Warning: 'bg-yellow-100 text-yellow-800',
  Critical: 'bg-red-100 text-red-700',
  Unknown: 'bg-gray-200 text-gray-700',
};

const setEmptyRow = (tbody, message, colspan) => {
  if (!tbody) {
    return;
  }

  tbody.innerHTML = `<tr><td colspan="${colspan}" class="px-4 py-4 text-center text-gray-500">${message}</td></tr>`;
};

const renderTransformerRows = (tbody, rows = []) => {
  if (!tbody) {
    return;
  }

  if (!rows.length) {
    setEmptyRow(tbody, 'No transformer logs available yet.', 6);
    return;
  }

  tbody.innerHTML = rows.map((row, index) => {
    const status = row.status ?? 'Unknown';
    const badgeClass = STATUS_BADGES[status] ?? STATUS_BADGES.Unknown;
    const voltage = row.voltage !== null && row.voltage !== undefined
      ? Number(row.voltage).toFixed(2)
      : '—';
    const loadKw = row.load_kw !== null && row.load_kw !== undefined
      ? Number(row.load_kw).toFixed(3)
      : '—';

    return `
      <tr class="hover:bg-gray-100">
        <td class="px-4 py-2 font-medium">${index + 1}</td>
        <td class="px-4 py-2">${row.label ?? '—'}</td>
        <td class="px-4 py-2">${voltage}</td>
        <td class="px-4 py-2">${loadKw}</td>
        <td class="px-4 py-2"><span class="px-2 py-1 rounded-full text-xs font-semibold ${badgeClass}">${status}</span></td>
        <td class="px-4 py-2 text-gray-600">${row.timestamp ?? '—'}</td>
      </tr>
    `;
  }).join('');
};

const renderSystemRows = (tbody, rows = []) => {
  if (!tbody) {
    return;
  }

  if (!rows.length) {
    setEmptyRow(tbody, 'No system logs available yet.', 8);
    return;
  }

  tbody.innerHTML = rows.map((row, index) => {
    const pf = row.total_pf;
    let pfClass = STATUS_BADGES.Unknown;
    if (typeof pf === 'number') {
      if (pf < 0.8) {
        pfClass = STATUS_BADGES.Critical;
      } else if (pf < 0.9) {
        pfClass = STATUS_BADGES.Warning;
      } else {
        pfClass = STATUS_BADGES.Normal;
      }
    }

    const format = (value, digits = 2) => (value !== null && value !== undefined ? Number(value).toFixed(digits) : '—');

    return `
      <tr class="hover:bg-gray-100">
        <td class="px-4 py-2 font-medium">${index + 1}</td>
        <td class="px-4 py-2">${row.date ?? '—'}</td>
        <td class="px-4 py-2">${row.time ?? '—'}</td>
        <td class="px-4 py-2">${row.time_ed ?? '—'}</td>
        <td class="px-4 py-2">${format(row.total_kw)}</td>
        <td class="px-4 py-2">${format(row.total_kvar)}</td>
        <td class="px-4 py-2">${format(row.total_kva)}</td>
        <td class="px-4 py-2"><span class="px-2 py-1 rounded-full text-xs font-semibold ${pfClass}">${pf !== null && pf !== undefined ? Number(pf).toFixed(3) : '—'}</span></td>
      </tr>
    `;
  }).join('');
};

export const mountTablesRealtime = () => {
  const transformerBody = document.querySelector('#transformerTable tbody');
  const systemBody = document.querySelector('#systemTable tbody');

  if (!transformerBody && !systemBody) {
    return;
  }

  if (!window.Echo) {
    return;
  }

  if (transformerBody) {
    window.Echo.channel('transformers.overview')
      .listen('TransformerLogRecorded', ({ payload }) => renderTransformerRows(transformerBody, payload ?? []));
  }

  if (systemBody) {
    window.Echo.channel('system.logs')
      .listen('SystemLogRecorded', ({ payload }) => renderSystemRows(systemBody, payload ?? []));
  }
};
