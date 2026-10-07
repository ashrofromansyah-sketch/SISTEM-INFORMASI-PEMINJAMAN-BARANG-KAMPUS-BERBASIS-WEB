@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <p class="text-muted">Halo, {{ auth()->user()->name }}. Anda masuk sebagai peminjam.</p>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Menunggu persetujuan</div>
                <div class="display-6">{{ $waiting }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Disetujui (siap diambil)</div>
                <div class="display-6">{{ $approved }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Sedang dipinjam</div>
                <div class="display-6">{{ $borrowed }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Selesai</div>
                <div class="display-6">{{ $finished }}</div>
            </div></div>
        </div>
    </div>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('peminjam.loans.create') }}" class="btn btn-primary">Ajukan Peminjaman</a>
        <a href="{{ route('peminjam.catalog') }}" class="btn btn-outline-secondary">Lihat Katalog Barang</a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Peminjaman terbaru</span>
            <a href="{{ route('peminjam.loans.index') }}" class="btn btn-sm btn-outline-secondary">Lihat semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead>
                <tr><th>Kode</th><th>Periode</th><th class="text-center">Barang</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse ($latestLoans as $loan)
                    <tr>
                        <td><a href="{{ route('peminjam.loans.show', $loan) }}">{{ $loan->loan_code }}</a></td>
                        <td>{{ $loan->loan_date->format('d/m/Y') }} - {{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $loan->details->count() }}</td>
                        <td>
                            <span class="badge bg-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
                            @if ($loan->isOverdue())
                                <span class="badge bg-danger">Terlambat</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada peminjaman.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
