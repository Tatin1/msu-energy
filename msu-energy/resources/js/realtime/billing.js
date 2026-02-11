let isMounted = false;
let refreshTimer = null;

const queueRefresh = () => {
  if (refreshTimer) {
    clearTimeout(refreshTimer);
  }

  refreshTimer = setTimeout(() => {
    window.billingPage?.refresh?.();
  }, 500);
};

export const mountBillingRealtime = () => {
  if (isMounted) {
    return;
  }

  if (typeof window === 'undefined') {
    return;
  }

  const billingRoot = document.getElementById('billing');
  if (!billingRoot || !window.Echo) {
    return;
  }

  window.Echo.channel('dashboard.metrics').listen('.ReadingIngested', queueRefresh);
  window.Echo.channel('system.logs').listen('.SystemLogRecorded', queueRefresh);

  isMounted = true;
};
