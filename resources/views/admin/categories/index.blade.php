@extends('layouts.admin')

@section('title', 'Kategori Barang')
@section('heading', 'Kategori Barang')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Tambah Kategori</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                <tr><th>Nama</th><th>Deskripsi</th><th class="text-center">Jumlah barang</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description ?: '-' }}</td>
                        <td class="text-center">{{ $category->items_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                            @if (auth()->user()->isAdmin())
                                <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $categories->links('pagination::bootstrap-5') }}</div>
@endsection
