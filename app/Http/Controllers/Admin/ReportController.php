<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.reports.index', $this->data($request) + [
            'loans' => $this->query($request)->paginate(15)->withQueryString(),
            'statuses' => Loan::STATUS_LABELS,
        ]);
    }

    public function print(Request $request): View
    {
        return view('admin.reports.print', $this->data($request) + [
            'loans' => $this->query($request)->get(),
            'statuses' => Loan::STATUS_LABELS,
        ]);
    }

    public function csv(Request $request): StreamedResponse
    {
        $loans = $this->query($request)->get();

        return response()->streamDownload(function () use ($loans) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['Kode', 'Peminjam', 'NIM/NIP', 'Tanggal Pinjam', 'Tanggal Kembali', 'Status', 'Barang', 'Dikembalikan Pada']);

            foreach ($loans as $loan) {
                fputcsv($out, [
                    $loan->loan_code,
                    $loan->user->name,
                    $loan->user->identity_number,
                    $loan->loan_date->format('d/m/Y'),
                    $loan->due_date->format('d/m/Y'),
                    $loan->statusLabel(),
                    $loan->details->map(fn ($d) => $d->item->name.' x'.$d->quantity)->implode('; '),
                    $loan->returned_at?->format('d/m/Y H:i'),
                ]);
            }

            fclose($out);
        }, 'laporan-peminjaman-'.now()->format('Ymd').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function query(Request $request)
    {
        $status = $request->input('status');
        $from = $request->input('from');
        $to = $request->input('to');

        return Loan::with(['user', 'details.item'])
            ->when($status, fn ($q, $v) => $q->where('status', $v))
            ->when($from, fn ($q, $v) => $q->whereDate('loan_date', '>=', $v))
            ->when($to, fn ($q, $v) => $q->whereDate('loan_date', '<=', $v))
            ->orderBy('loan_date', 'desc');
    }

    private function data(Request $request): array
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', 'string'],
        ]);

        $summary = (clone $this->query($request))
            ->reorder()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'summary' => $summary,
            'from' => $request->input('from'),
            'to' => $request->input('to'),
        ];
    }
}
