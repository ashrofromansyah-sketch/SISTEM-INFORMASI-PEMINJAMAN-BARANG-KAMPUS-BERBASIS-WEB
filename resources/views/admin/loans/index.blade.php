@extends('layouts.admin')

@section('title', 'Peminjaman')
@section('heading', 'Daftar Peminjaman')

@section('content')
    <form method="GET" action="{{ route('admin.loans.index') }}" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Cari kode peminjaman atau nama peminjam">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                    <option value="terlambat" @selected(request('status') === 'terlambat')>Terlambat dikembalikan</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">Cari</button>
                <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                <tr><th>Kode</th><th>Peminjam</th><th>Periode</th><th class="text-center">Barang</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse ($loans as $loan)
                    <tr>
                        <td>{{ $loan->loan_code }}</td>
                        <td>{{ $loan->user->name }}</td>
                        <td>{{ $loan->loan_date->format('d/m/Y') }} - {{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $loan->details->count() }}</td>
                        <td>
                            <span class="badge bg-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
                            @if ($loan->isOverdue())
                                <span class="badge bg-danger">Terlambat</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">
                                {{ in_array($loan->status, ['menunggu', 'disetujui', 'dipinjam'], true) ? 'Proses' : 'Detail' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data peminjaman.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $loans->links('pagination::bootstrap-5') }}</div>
@endsection
