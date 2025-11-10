<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function home()
    {
        // Dummy Data for GUI representation
        $labels = ['COE', 'SET', 'CSM', 'CCS', 'PRISM'];

        // Corresponding bar heights (kWh)
        $values = [80, 60, 50, 70, 100];

        // Right-side summary KPIs
        $totalPower = 300000;      // Total Power (kW)
        $avgPF = 0.9423;           // Power Factor (PF)
        $lastMonthKwh = 350160;    // Last Month’s energy
        $thisMonthKwh = 352512;    // This Month’s energy

        // Return data to the view
        return view('pages.home', compact(
            'labels',
            'values',
            'totalPower',
            'avgPF',
            'lastMonthKwh',
            'thisMonthKwh'
        ));
    }

    // Other existing pages (map, graphs, etc.)
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
