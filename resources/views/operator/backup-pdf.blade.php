<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Backup Data Antrian</h1>
    <p>Dicetak: {{ date('d-m-Y H:i:s') }}</p>

    @foreach($lokets as $loket)
        <h3>{{ $loket->nama }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Nomor</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($loket->antrians as $antrian)
                    <tr>
                        <td>{{ $antrian->nomor }}</td>
                        <td>{{ $antrian->status }}</td>
                        <td>{{ $antrian->created_at }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <br>
    @endforeach
</body>
</html>
