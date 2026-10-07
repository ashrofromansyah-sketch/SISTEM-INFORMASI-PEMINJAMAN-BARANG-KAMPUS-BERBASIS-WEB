@extends('layouts.admin')

@section('title', $item->name)
@section('heading', 'Detail Barang')

@section('content')
    <div class="card"><div class="card-body">
        <div class="row">
            @if ($item->image)
                <div class="col-md-3 mb-3">
                    <img src="{{ asset('storage/'.$item->image) }}" alt="Foto {{ $item->name }}" class="img-fluid rounded">
                </div>
            @endif
            <div class="col">
                <h2 class="h4">{{ $item->name }}
                    <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }} fs-6">
                        {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </h2>
                <table class="table table-borderless table-sm w-auto">
                    <tr><th>Kode barang</th><td>{{ $item->item_code }}</td></tr>
                    <tr><th>Kategori</th><td>{{ $item->category->name }}</td></tr>
                    <tr><th>Kondisi</th><td>{{ ucfirst($item->condition) }}</td></tr>
                    <tr><th>Jumlah tersedia</th><td>{{ $item->available_quantity }} dari {{ $item->total_quantity }}</td></tr>
                    <tr><th>Lokasi</th><td>{{ $item->location }}</td></tr>
                    <tr><th>Deskripsi</th><td>{{ $item->description ?: '-' }}</td></tr>
                </table>
                <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-primary">Ubah</a>
                <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">Kembali</a>
            </div>
        </div>
    </div></div>
@endsection
