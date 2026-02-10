import { mountDashboardRealtime } from './dashboard';
import { mountTablesRealtime } from './tables';
import { mountHistoryRealtime } from './history';

export const initRealtimeConsumers = () => {
  if (typeof window === 'undefined') {
    return;
  }

  mountDashboardRealtime();
  mountTablesRealtime();
  mountHistoryRealtime();
};
