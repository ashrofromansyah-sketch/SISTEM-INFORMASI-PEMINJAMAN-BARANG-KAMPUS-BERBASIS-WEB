@extends('layouts.admin')

@section('title', 'Laporan')
@section('heading', 'Laporan Peminjaman')

@section('content')
    @php $query = request()->only(['from', 'to', 'status']); @endphp

    <form method="GET" action="{{ route('admin.reports.index') }}" class="card card-body mb-3">
        @if ($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Tanggal pinjam dari</label>
                <input type="date" name="from" value="{{ $from }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Sampai</label>
                <input type="date" name="to" value="{{ $to }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label small mb-1">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">Tampilkan</button>
                <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="d-flex flex-wrap gap-2 mb-3">
        @foreach ($statuses as $value => $label)
            <span class="badge bg-{{ \App\Models\Loan::STATUS_COLORS[$value] }} fs-6 fw-normal">
                {{ $label }}: {{ $summary[$value] ?? 0 }}
            </span>
        @endforeach
    </div>

    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('admin.reports.print', $query) }}" target="_blank" class="btn btn-outline-primary">Cetak / PDF</a>
        <a href="{{ route('admin.reports.csv', $query) }}" class="btn btn-outline-success">Unduh CSV</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-sm table-hover mb-0 align-middle">
                <thead>
                <tr><th>Kode</th><th>Peminjam</th><th>Periode</th><th>Barang</th><th>Status</th></tr>
                </thead>
                <tbody>
                @forelse ($loans as $loan)
                    <tr>
                        <td><a href="{{ route('admin.loans.show', $loan) }}">{{ $loan->loan_code }}</a></td>
                        <td>{{ $loan->user->name }}</td>
                        <td class="text-nowrap">{{ $loan->loan_date->format('d/m/Y') }} - {{ $loan->due_date->format('d/m/Y') }}</td>
                        <td>{{ $loan->details->map(fn ($d) => $d->item->name.' x'.$d->quantity)->implode(', ') }}</td>
                        <td><span class="badge bg-{{ $loan->statusColor() }}">{{ $loan->statusLabel() }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada data pada filter ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $loans->links('pagination::bootstrap-5') }}</div>
@endsection
