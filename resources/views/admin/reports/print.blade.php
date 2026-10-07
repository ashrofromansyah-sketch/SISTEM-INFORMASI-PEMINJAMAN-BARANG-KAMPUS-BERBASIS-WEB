<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Peminjaman</title>
    <style>
        body { font-family: Calibri, Arial, sans-serif; font-size: 11pt; color: #000; margin: 0; padding: 24px; }
        h1 { font-size: 15pt; text-align: center; margin: 0 0 4px; }
        .sub { text-align: center; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        th, td { border: 1px solid #000; padding: 3px 6px; text-align: left; vertical-align: top; }
        .toolbar { text-align: center; margin-bottom: 16px; }
        @media print { .toolbar { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
<div class="toolbar"><button onclick="window.print()">Cetak / Simpan sebagai PDF</button></div>

<h1>LAPORAN PEMINJAMAN BARANG KAMPUS</h1>
<div class="sub">
    Periode tanggal pinjam:
    {{ $from ? \Illuminate\Support\Carbon::parse($from)->format('d/m/Y') : 'awal' }}
    s.d.
    {{ $to ? \Illuminate\Support\Carbon::parse($to)->format('d/m/Y') : 'sekarang' }}
    @if (request('status')) | Status: {{ $statuses[request('status')] ?? request('status') }} @endif
</div>

<table>
    <thead>
    <tr><th>Status</th><th>Jumlah</th></tr>
    </thead>
    <tbody>
    @foreach ($statuses as $value => $label)
        <tr><td>{{ $label }}</td><td>{{ $summary[$value] ?? 0 }}</td></tr>
    @endforeach
    </tbody>
</table>

<table>
    <thead>
    <tr><th>No</th><th>Kode</th><th>Peminjam</th><th>Periode</th><th>Barang</th><th>Status</th></tr>
    </thead>
    <tbody>
    @forelse ($loans as $loan)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $loan->loan_code }}</td>
            <td>{{ $loan->user->name }}</td>
            <td>{{ $loan->loan_date->format('d/m/Y') }} - {{ $loan->due_date->format('d/m/Y') }}</td>
            <td>{{ $loan->details->map(fn ($d) => $d->item->name.' x'.$d->quantity)->implode(', ') }}</td>
            <td>{{ $loan->statusLabel() }}</td>
        </tr>
    @empty
        <tr><td colspan="6">Tidak ada data.</td></tr>
    @endforelse
    </tbody>
</table>

<p style="font-size:9pt">Dicetak {{ now()->format('d/m/Y H:i') }} oleh {{ auth()->user()->name }}</p>
</body>
</html>
