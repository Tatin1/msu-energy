<?php

namespace App\Http\Controllers;

use App\Models\Reading;
use App\Models\Billing;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function home()
    {
        // KPIs
        $totalPower = Reading::sum('active_power');
        $avgPF = Reading::avg('power_factor');
        $lastMonthKwh = Billing::sum('last_month_kwh');
        $thisMonthKwh = Billing::sum('this_month_kwh');

        // Power trend for last 7 days
        $trendData = Reading::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(active_power) as total_power')
        )
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->take(7)
        ->get();

        $labels = $trendData->pluck('date');
        $values = $trendData->pluck('total_power');

        return view('pages.home', compact(
            'totalPower', 'avgPF', 'lastMonthKwh', 'thisMonthKwh', 'labels', 'values'
        ));
    }

    public function map() { return view('pages.map'); }
    public function parameters() { return view('pages.parameters'); }
    public function billing() { return view('pages.billing'); }
    public function tables() { return view('pages.tables'); }
    public function graphs() { return view('pages.graphs'); }
    public function history() { return view('pages.history'); }
    public function options() { return view('pages.options'); }
    public function view() { return view('pages.view'); }
    public function help() { return view('pages.help'); }
    public function about() { return view('pages.about'); }
}
