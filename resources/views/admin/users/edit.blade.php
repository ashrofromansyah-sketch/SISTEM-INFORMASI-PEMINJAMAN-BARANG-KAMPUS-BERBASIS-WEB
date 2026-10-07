@extends('layouts.admin')

@section('title', 'Ubah Akun')
@section('heading', 'Ubah Akun')

@section('content')
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="card card-body">
        @csrf
        @method('PUT')
        @include('admin.users._form')
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
@endsection
