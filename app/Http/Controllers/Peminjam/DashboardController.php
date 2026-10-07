<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $loans = Loan::where('user_id', $user->id);

        return view('peminjam.dashboard', [
            'waiting' => (clone $loans)->where('status', 'menunggu')->count(),
            'approved' => (clone $loans)->where('status', 'disetujui')->count(),
            'borrowed' => (clone $loans)->where('status', 'dipinjam')->count(),
            'finished' => (clone $loans)->where('status', 'dikembalikan')->count(),
            'latestLoans' => Loan::with('details')
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
