@extends('layouts.admin')

@section('title', $loan->loan_code)
@section('heading', 'Detail Peminjaman')

@section('content')
    @include('loans._detail', ['loan' => $loan])

    <div class="d-flex gap-2">
        <a href="{{ route('peminjam.loans.index') }}" class="btn btn-outline-secondary">Kembali</a>

        @if ($loan->status === 'menunggu')
            <form method="POST" action="{{ route('peminjam.loans.cancel', $loan) }}"
                  onsubmit="return confirm('Batalkan pengajuan ini?')">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Batalkan Pengajuan</button>
            </form>
        @endif

        @if (in_array($loan->status, ['disetujui', 'dipinjam', 'dikembalikan'], true))
            <a href="{{ route('loans.print', $loan) }}" target="_blank" class="btn btn-outline-primary">Cetak Bukti</a>
        @endif
    </div>

    @if ($loan->status === 'disetujui')
        <p class="text-muted mt-3 mb-0">Pengajuan disetujui. Datang ke petugas pada {{ $loan->loan_date->format('d/m/Y') }} untuk mengambil barang.</p>
    @endif
@endsection
