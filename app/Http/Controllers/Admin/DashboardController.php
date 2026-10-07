<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $aktif = Item::where('is_active', true);

        return view('admin.dashboard', [
            'totalItems' => (clone $aktif)->count(),
            'totalUnits' => (int) (clone $aktif)->sum('total_quantity'),
            'availableUnits' => (int) (clone $aktif)->sum('available_quantity'),
            'totalCategories' => Category::count(),
            'inactiveItems' => Item::where('is_active', false)->count(),
            'latestItems' => Item::with('category')->latest()->take(5)->get(),

            'waitingLoans' => Loan::where('status', 'menunggu')->count(),
            'approvedLoans' => Loan::where('status', 'disetujui')->count(),
            'borrowedLoans' => Loan::where('status', 'dipinjam')->count(),
            'overdueLoans' => Loan::where('status', 'dipinjam')->whereDate('due_date', '<', now()->toDateString())->count(),
            'pendingLoans' => Loan::with(['user', 'details'])
                ->where('status', 'menunggu')
                ->oldest()
                ->take(5)
                ->get(),
        ]);
    }
}
