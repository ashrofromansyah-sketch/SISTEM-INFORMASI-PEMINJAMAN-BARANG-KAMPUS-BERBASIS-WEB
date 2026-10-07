@extends('layouts.admin')

@section('title', $loan->loan_code)
@section('heading', 'Detail Peminjaman')

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach (array_unique($errors->all()) as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @include('loans._detail', ['loan' => $loan, 'availability' => $availability])

    {{-- Menunggu: setujui atau tolak --}}
    @if ($loan->status === 'menunggu')
        <div class="row g-3 mb-3">
            <div class="col-md-5">
                <div class="card h-100"><div class="card-body">
                    <h2 class="h6">Setujui pengajuan</h2>
                    <p class="small text-muted">Sistem akan mengecek ulang ketersediaan barang pada periode ini sebelum menyetujui.</p>
                    <form method="POST" action="{{ route('admin.loans.approve', $loan) }}"
                          onsubmit="return confirm('Setujui pengajuan ini?')">
                        @csrf
                        <button type="submit" class="btn btn-success">Setujui</button>
                    </form>
                </div></div>
            </div>
            <div class="col-md-7">
                <div class="card h-100"><div class="card-body">
                    <h2 class="h6">Tolak pengajuan</h2>
                    <form method="POST" action="{{ route('admin.loans.reject', $loan) }}">
                        @csrf
                        <textarea name="rejection_reason" rows="2" maxlength="500" class="form-control mb-2"
                                  placeholder="Alasan penolakan (wajib)" required>{{ old('rejection_reason') }}</textarea>
                        <button type="submit" class="btn btn-outline-danger">Tolak</button>
                    </form>
                </div></div>
            </div>
        </div>
    @endif

    {{-- Disetujui: serah terima --}}
    @if ($loan->status === 'disetujui')
        <div class="card mb-3"><div class="card-body">
            <h2 class="h6">Serah terima barang</h2>
            <p class="small text-muted">Catat saat barang diserahkan ke peminjam. Stok di tempat akan berkurang.</p>
            <form method="POST" action="{{ route('admin.loans.handover', $loan) }}"
                  onsubmit="return confirm('Barang sudah diserahkan ke peminjam?')">
                @csrf
                <button type="submit" class="btn btn-primary">Catat Serah Terima</button>
            </form>
        </div></div>
    @endif

    {{-- Dipinjam: pengembalian --}}
    @if ($loan->status === 'dipinjam')
        <div class="card mb-3">
            <div class="card-header">Catat pengembalian</div>
            <form method="POST" action="{{ route('admin.loans.return', $loan) }}">
                @csrf
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead><tr><th>Barang</th><th class="text-center">Jumlah</th><th>Kondisi saat kembali</th><th>Catatan</th></tr></thead>
                        <tbody>
                        @foreach ($loan->details as $detail)
                            <tr>
                                <td>{{ $detail->item->name }}</td>
                                <td class="text-center">{{ $detail->quantity }}</td>
                                <td>
                                    <select name="returns[{{ $detail->id }}][condition]" class="form-select form-select-sm" required>
                                        @foreach ($conditions as $kondisi)
                                            <option value="{{ $kondisi }}" @selected(old("returns.{$detail->id}.condition", 'baik') === $kondisi)>{{ ucfirst($kondisi) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="returns[{{ $detail->id }}][note]" maxlength="255"
                                           value="{{ old("returns.{$detail->id}.note") }}" class="form-control form-control-sm"
                                           placeholder="Opsional">
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-body">
                    <textarea name="return_notes" rows="2" maxlength="500" class="form-control mb-2"
                              placeholder="Catatan umum pengembalian (opsional)">{{ old('return_notes') }}</textarea>
                    <button type="submit" class="btn btn-success">Simpan Pengembalian</button>
                </div>
            </form>
        </div>
    @endif

    <div class="d-flex gap-2">
        <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-secondary">Kembali</a>
        @if (in_array($loan->status, ['disetujui', 'dipinjam', 'dikembalikan'], true))
            <a href="{{ route('loans.print', $loan) }}" target="_blank" class="btn btn-outline-primary">Cetak Bukti</a>
        @endif
    </div>
@endsection
