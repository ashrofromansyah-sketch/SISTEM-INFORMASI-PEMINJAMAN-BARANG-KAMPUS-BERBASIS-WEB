@extends('layouts.admin')

@section('title', 'Data Barang')
@section('heading', 'Data Barang')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.items.create') }}" class="btn btn-primary">Tambah Barang</a>
    </div>

    <form method="GET" action="{{ route('admin.items.index') }}" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Cari nama atau kode barang">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="condition" class="form-select">
                    <option value="">Semua kondisi</option>
                    @foreach ($conditions as $kondisi)
                        <option value="{{ $kondisi }}" @selected(request('condition') === $kondisi)>{{ ucfirst($kondisi) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">Semua status</option>
                    <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                    <option value="nonaktif" @selected(request('status') === 'nonaktif')>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">Cari</button>
                <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                <tr>
                    <th>Kode</th><th>Nama</th><th>Kategori</th><th>Kondisi</th>
                    <th class="text-center">Tersedia / Total</th><th>Status</th><th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($items as $item)
                    <tr class="{{ $item->is_active ? '' : 'table-secondary' }}">
                        <td>{{ $item->item_code }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->category->name }}</td>
                        <td>{{ ucfirst($item->condition) }}</td>
                        <td class="text-center">{{ $item->available_quantity }} / {{ $item->total_quantity }}</td>
                        <td>
                            <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.items.show', $item) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('admin.items.toggle', $item) }}" class="d-inline"
                                      onsubmit="return confirm('{{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }} barang ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $item->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
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
