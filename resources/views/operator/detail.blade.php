<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Loket</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: center; }
        th { background: #f3f3f3; }
        a { text-decoration: none; color: blue; }
    </style>
</head>
<body>
    <h1>Detail Loket: {{ $loket->nama }}</h1>

    <p><strong>Total Masuk:</strong> {{ $statistik['total_masuk'] }}</p>
    <p><strong>Total Selesai:</strong> {{ $statistik['total_selesai'] }}</p>
    <p><strong>Total Menunggu:</strong> {{ $statistik['total_menunggu'] }}</p>

    <h2>Daftar Antrian</h2>
    <table>
        <thead>
        <tr>
            <th>Nomor</th>
            <th>Status</th>
            <th>Dibuat</th>
        </tr>
        </thead>
        <tbody>
        @foreach($loket->antrians as $antrian)
            <tr>
                <td>{{ $antrian->nomor }}</td>
                <td>{{ $antrian->status }}</td>
                <td>{{ $antrian->created_at }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <br>
    <a href="{{ route('operator.dashboard') }}">Kembali ke Dashboard</a>
</body>
</html>
