let isMounted = false;
let debounceHandle = null;

const queueRefresh = () => {
  if (debounceHandle) {
    clearTimeout(debounceHandle);
  }

  debounceHandle = setTimeout(() => {
    window.parametersPage?.refresh?.();
  }, 600);
};

export const mountParametersRealtime = () => {
  if (isMounted) {
    return;
  }

  if (typeof window === 'undefined') {
    return;
  }

  const parametersRoot = document.getElementById('parameters');
  if (!parametersRoot || !window.Echo) {
    return;
  }

  window.Echo.channel('building.logs').listen('BuildingLogRecorded', queueRefresh);
  window.Echo.channel('transformers.overview').listen('TransformerLogRecorded', queueRefresh);

  isMounted = true;
};
