@extends('layouts.admin')

@section('title', 'Peminjaman Saya')
@section('heading', 'Peminjaman Saya')

@section('content')
    <div class="mb-3 d-flex flex-wrap gap-2 justify-content-between">
        <a href="{{ route('peminjam.loans.create') }}" class="btn btn-primary">Ajukan Peminjaman</a>

        <form method="GET" action="{{ route('peminjam.loans.index') }}" class="d-flex gap-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">Semua status</option>
                @foreach ($statuses as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                <tr><th>Kode</th><th>Periode</th><th class="text-center">Barang</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse ($loans as $loan)
                    <tr>
                        <td>{{ $loan->loan_code }}</td>
                        <td>{{ $loan->loan_date->format('d/m/Y') }} - {{ $loan->due_date->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $loan->details->count() }}</td>
                        <td>
                            <span class="badge bg-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span>
                            @if ($loan->isOverdue())
                                <span class="badge bg-danger">Terlambat</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('peminjam.loans.show', $loan) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Belum ada peminjaman.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $loans->links('pagination::bootstrap-5') }}</div>
@endsection
