@extends('layouts.admin')

@section('title', 'Tambah Barang')
@section('heading', 'Tambah Barang')

@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.items._form')
        </form>
    </div></div>
@endsection
