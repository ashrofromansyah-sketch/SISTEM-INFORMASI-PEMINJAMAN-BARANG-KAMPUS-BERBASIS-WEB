@extends('layouts.admin')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
    <p class="text-muted">Halo, {{ auth()->user()->name }}. Anda masuk sebagai {{ auth()->user()->role }}.</p>

    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.loans.index', ['status' => 'menunggu']) }}" class="text-decoration-none">
                <div class="card h-100 border-warning"><div class="card-body">
                    <div class="text-muted small">Menunggu persetujuan</div>
                    <div class="display-6 text-body">{{ $waitingLoans }}</div>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.loans.index', ['status' => 'disetujui']) }}" class="text-decoration-none">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">Siap diserahkan</div>
                    <div class="display-6 text-body">{{ $approvedLoans }}</div>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.loans.index', ['status' => 'dipinjam']) }}" class="text-decoration-none">
                <div class="card h-100"><div class="card-body">
                    <div class="text-muted small">Sedang dipinjam</div>
                    <div class="display-6 text-body">{{ $borrowedLoans }}</div>
                </div></div>
            </a>
        </div>
        <div class="col-6 col-lg-3">
            <a href="{{ route('admin.loans.index', ['status' => 'terlambat']) }}" class="text-decoration-none">
                <div class="card h-100 {{ $overdueLoans > 0 ? 'border-danger' : '' }}"><div class="card-body">
                    <div class="text-muted small">Terlambat dikembalikan</div>
                    <div class="display-6 text-body">{{ $overdueLoans }}</div>
                </div></div>
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Jenis barang aktif</div>
                <div class="display-6">{{ $totalItems }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Total unit</div>
                <div class="display-6">{{ $totalUnits }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Unit di tempat</div>
                <div class="display-6">{{ $availableUnits }}</div>
            </div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100"><div class="card-body">
                <div class="text-muted small">Kategori</div>
                <div class="display-6">{{ $totalCategories }}</div>
                @if ($inactiveItems > 0)
                    <div class="small text-muted">{{ $inactiveItems }} barang nonaktif</div>
                @endif
            </div></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Pengajuan yang menunggu</span>
            <a href="{{ route('admin.loans.index') }}" class="btn btn-sm btn-outline-secondary">Semua peminjaman</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead>
                <tr><th>Kode</th><th>Peminjam</th><th>Periode</th><th class="text-center">Barang</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse ($pendingLoans as $loan)
                    <tr>
                        <td>{{ $loan->loan_code }}</td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->loan_date->format('d/m/Y') }} - {{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $loan->details->count() }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-primary">Proses</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Tidak ada pengajuan yang menunggu.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>Barang terbaru</span>
            <a href="{{ route('admin.items.index') }}" class="btn btn-sm btn-outline-secondary">Lihat semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0 align-middle">
                <thead>
                <tr><th>Kode</th><th>Nama</th><th>Kategori</th><th class="text-end">Di tempat</th></tr>
                </thead>
                <tbody>
                @forelse ($latestItems as $item)
                    <tr>
                        <td>{{ $item->item_code }}</td>
                        <td><a href="{{ route('admin.items.show', $item) }}">{{ $item->name }}</a></td>
                        <td>{{ $item->category->name }}</td>
                        <td class="text-end">{{ $item->available_quantity }} / {{ $item->total_quantity }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-3">Belum ada data barang.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
