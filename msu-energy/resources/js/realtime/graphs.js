let isMounted = false;
let refreshTimer = null;

const queueRefresh = () => {
  if (refreshTimer) {
    clearTimeout(refreshTimer);
  }

  refreshTimer = setTimeout(() => {
    window.graphsPage?.refresh?.();
  }, 600);
};

export const mountGraphsRealtime = () => {
  if (isMounted) {
    return;
  }

  if (typeof window === 'undefined') {
    return;
  }

  const graphsRoot = document.getElementById('graphs');
  if (!graphsRoot || !window.Echo) {
    return;
  }

  window.Echo.channel('dashboard.metrics').listen('.ReadingIngested', queueRefresh);
  window.Echo.channel('transformers.overview').listen('.TransformerLogRecorded', queueRefresh);

  isMounted = true;
};
