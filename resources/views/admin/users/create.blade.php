@extends('layouts.admin')

@section('title', 'Tambah Akun')
@section('heading', 'Tambah Akun')

@section('content')
    <form method="POST" action="{{ route('admin.users.store') }}" class="card card-body">
        @csrf
        @include('admin.users._form')
        <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
        </div>
    </form>
@endsection
