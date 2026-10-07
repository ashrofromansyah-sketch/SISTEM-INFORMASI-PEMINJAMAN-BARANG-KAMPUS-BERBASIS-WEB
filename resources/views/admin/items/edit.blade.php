@extends('layouts.admin')

@section('title', 'Ubah Barang')
@section('heading', 'Ubah Barang')

@section('content')
    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.items.update', $item) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.items._form')
        </form>
    </div></div>
@endsection
