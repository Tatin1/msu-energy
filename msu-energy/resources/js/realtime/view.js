let isMounted = false;
let refreshTimer = null;

const queueRefresh = () => {
  if (refreshTimer) {
    clearTimeout(refreshTimer);
  }

  refreshTimer = setTimeout(() => {
    window.viewPage?.refresh?.();
  }, 600);
};

export const mountViewRealtime = () => {
  if (isMounted) {
    return;
  }

  if (typeof window === 'undefined') {
    return;
  }

  const viewRoot = document.getElementById('view');
  if (!viewRoot || !window.Echo) {
    return;
  }

  window.Echo.channel('dashboard.metrics').listen('.ReadingIngested', queueRefresh);
  window.Echo.channel('building.logs').listen('.BuildingLogRecorded', queueRefresh);

  isMounted = true;
};
