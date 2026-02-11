let isMounted = false;

export const mountMapRealtime = () => {
  if (isMounted) {
    return;
  }

  if (typeof window === 'undefined') {
    return;
  }

  const mapRoot = document.getElementById('map');
  if (!mapRoot || !window.Echo) {
    return;
  }

  window.Echo.channel('dashboard.metrics')
    .listen('.ReadingIngested', ({ payload }) => {
      const buildings = Array.isArray(payload?.building_status) ? payload.building_status : null;
      if (buildings) {
        window.mapPage?.applyBuildingStatus?.(buildings);
      }
    });

  isMounted = true;
};
