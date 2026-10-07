@extends('layouts.admin')

@section('title', 'Pengguna')
@section('heading', 'Kelola Pengguna')

@section('content')
    <div class="mb-3">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Tambah Akun</a>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="card card-body mb-3">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Cari nama, email, atau NIM/NIP">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">Semua peran</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(request('role') === $role)>{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-secondary flex-fill">Cari</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                <tr><th>Nama</th><th>Email</th><th>NIM/NIP</th><th>Peran</th><th>Status</th><th class="text-end">Aksi</th></tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr class="{{ $user->is_active ? '' : 'table-secondary' }}">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->identity_number ?: '-' }}</td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-end text-nowrap">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                            @if ($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline"
                                      onsubmit="return confirm('{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada akun yang cocok.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $users->links('pagination::bootstrap-5') }}</div>
@endsection
