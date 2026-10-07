<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use App\Services\LoanWorkflow;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class LoanController extends Controller
{
    public function __construct(private LoanWorkflow $workflow)
    {
    }

    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = $request->input('q');

        $loans = Loan::with(['user', 'details'])
            ->when($status === 'terlambat', function ($query) {
                $query->where('status', 'dipinjam')->whereDate('due_date', '<', now()->toDateString());
            })
            ->when($status && $status !== 'terlambat', fn ($query) => $query->where('status', $status))
            ->when($search, function ($query, $value) {
                $query->where(function ($w) use ($value) {
                    $w->where('loan_code', 'like', "%{$value}%")
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$value}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.loans.index', [
            'loans' => $loans,
            'statuses' => Loan::STATUS_LABELS,
        ]);
    }

    public function show(Loan $loan): View
    {
        $loan->load(['details.item', 'user', 'processor', 'handedOverBy', 'returnedTo']);

        // Ketersediaan per periode ditampilkan saat petugas memutuskan persetujuan.
        $availability = [];
        if ($loan->status === 'menunggu') {
            foreach ($loan->details as $detail) {
                $availability[$detail->id] = $detail->item->availableBetween(
                    $loan->loan_date->toDateString(),
                    $loan->due_date->toDateString(),
                    $loan->id
                );
            }
        }

        return view('admin.loans.show', [
            'loan' => $loan,
            'availability' => $availability,
            'conditions' => Item::CONDITIONS,
        ]);
    }

    public function approve(Loan $loan): RedirectResponse
    {
        return $this->run(fn () => $this->workflow->approve($loan, auth()->user()), 'Pengajuan disetujui.');
    }

    public function reject(Request $request, Loan $loan): RedirectResponse
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:500'],
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
            'rejection_reason.min' => 'Alasan penolakan terlalu singkat.',
        ]);

        return $this->run(
            fn () => $this->workflow->reject($loan, auth()->user(), $data['rejection_reason']),
            'Pengajuan ditolak.'
        );
    }

    public function handover(Loan $loan): RedirectResponse
    {
        return $this->run(fn () => $this->workflow->handover($loan, auth()->user()), 'Serah terima barang dicatat.');
    }

    public function returnItems(Request $request, Loan $loan): RedirectResponse
    {
        $data = $request->validate([
            'returns' => ['required', 'array'],
            'returns.*.condition' => ['required', Rule::in(Item::CONDITIONS)],
            'returns.*.note' => ['nullable', 'string', 'max:255'],
            'return_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'returns.*.condition.required' => 'Kondisi setiap barang wajib dipilih.',
        ]);

        return $this->run(
            fn () => $this->workflow->receiveReturn($loan, auth()->user(), $data['returns'], $data['return_notes'] ?? null),
            'Pengembalian barang dicatat.'
        );
    }

    private function run(callable $action, string $successMessage): RedirectResponse
    {
        try {
            $action();
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', $successMessage);
    }
}
