<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Peminjaman</title>
    <style>
        * { box-sizing: border-box; }
        body { color: #102033; font-family: Arial, sans-serif; margin: 1.4cm; }
        .header { align-items: center; border-bottom: 3px solid #075aa5; display: flex; gap: 16px; padding-bottom: 14px; }
        .header img { height: 58px; width: 58px; object-fit: contain; }
        h1 { font-size: 18pt; margin: 0; }
        .meta { color: #64748b; font-size: 10pt; margin-top: 4px; }
        table { border-collapse: collapse; margin-top: 20px; width: 100%; }
        th, td { border: 1px solid #cbd5e1; font-size: 10pt; padding: 8px; text-align: left; }
        th { background-color: #eef6fd; color: #0b2d4d; }
        .status { font-weight: bold; text-transform: capitalize; }
        @media print {
            body { margin: 1cm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/smp-11-logo.png') }}" alt="Logo SMP Negeri 11 Jember">
        <div>
            <h1>Laporan Peminjaman Buku</h1>
            <div class="meta">Perpustakaan SMP Negeri 11 Jember - {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Buku</th>
                <th>Tgl Pinjam</th>
                <th>Tenggat</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $index => $loan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $loan->user->name }}</td>
                    <td>{{ $loan->book->title }}</td>
                    <td>{{ $loan->loan_date->format('d/m/Y') }}</td>
                    <td>{{ $loan->due_date->format('d/m/Y') }}</td>
                    <td class="status">{{ $loan->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Tidak ada data peminjaman.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <script>window.print();</script>
</body>
</html>
