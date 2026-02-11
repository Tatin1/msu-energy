import { mountDashboardRealtime } from './dashboard';
import { mountTablesRealtime } from './tables';
import { mountHistoryRealtime } from './history';
import { mountMapRealtime } from './map';
import { mountParametersRealtime } from './parameters';
import { mountBillingRealtime } from './billing';
import { mountGraphsRealtime } from './graphs';
import { mountViewRealtime } from './view';

export const initRealtimeConsumers = () => {
  if (typeof window === 'undefined') {
    return;
  }

  mountDashboardRealtime();
  mountTablesRealtime();
  mountHistoryRealtime();
  mountMapRealtime();
  mountParametersRealtime();
  mountBillingRealtime();
  mountGraphsRealtime();
  mountViewRealtime();
};
