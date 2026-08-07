<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Chair;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Sell;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'appointmentsToday' => Appointment::whereDate('startHour', today())->count(),
            'appointmentsPending' => Appointment::where('status', 'pending')->count(),
            'clients' => Client::count(),
            'employees' => Employee::count(),
            'products' => Product::count(),
            'lowStockProducts' => Product::where('stock', '<=', 5)->count(),
            'chairs' => Chair::count(),
            'services' => Service::count(),
            'categories' => Category::count(),
            'salesThisMonth' => Sell::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total'),
            'salesCountThisMonth' => Sell::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];

        $upcomingAppointments = Appointment::with(['client.person', 'employee.person', 'chair'])
            ->where('startHour', '>=', now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('startHour')
            ->limit(6)
            ->get();

        $recentSells = Sell::with('client.person')
            ->latest()
            ->limit(6)
            ->get();

        $lowStock = Product::where('stock', '<=', 5)
            ->orderBy('stock')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact('stats', 'upcomingAppointments', 'recentSells', 'lowStock'));
    }
}
