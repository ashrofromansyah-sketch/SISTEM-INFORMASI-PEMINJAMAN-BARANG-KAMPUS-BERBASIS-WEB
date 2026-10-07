@extends('layouts.admin')

@section('title', 'Katalog Barang')
@section('heading', 'Katalog Barang')

@section('content')
    <form method="GET" action="{{ route('peminjam.catalog') }}" class="card card-body mb-3">
        @if ($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
        @endif
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small mb-1">Cari barang</label>
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nama atau kode">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Kategori</label>
                <select name="category_id" class="form-select">
                    <option value="">Semua</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Tanggal pinjam</label>
                <input type="date" name="loan_date" value="{{ request('loan_date') }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label small mb-1">Tanggal kembali</label>
                <input type="date" name="due_date" value="{{ request('due_date') }}" class="form-control">
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">Cari</button>
                <a href="{{ route('peminjam.catalog') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
        <div class="form-text mt-2">
            @if ($hasPeriod)
                Jumlah tersedia dihitung untuk periode yang Anda pilih.
            @else
                Isi kedua tanggal untuk melihat ketersediaan pada periode tertentu. Tanpa tanggal, yang tampil adalah stok yang ada di tempat sekarang.
            @endif
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                <tr>
                    <th>Kode</th><th>Nama</th><th>Kategori</th><th>Kondisi</th><th>Lokasi</th>
                    <th class="text-center">Tersedia</th><th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($items as $item)
                    <tr>
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name }}</td>
                        <td>{{ ucfirst($item->condition) }}</td>
                        <td>{{ $item->location }}</td>
                        <td class="text-center">
                            <span class="badge {{ $item->shown_available > 0 ? 'bg-success' : 'bg-secondary' }}">
                                {{ $item->shown_available }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if ($item->shown_available > 0)
                                <a href="{{ route('peminjam.loans.create', array_filter([
                                    'item' => $item->id,
                                    'loan_date' => request('loan_date'),
                                    'due_date' => request('due_date'),
                                ])) }}" class="btn btn-sm btn-primary">Ajukan</a>
                            @else
                                <span class="text-muted small">Tidak tersedia</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada barang yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $items->links('pagination::bootstrap-5') }}</div>
@endsection
