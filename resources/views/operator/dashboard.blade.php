<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Operator</title>
    <link rel="stylesheet" href="{{ asset('globalcss/operator-dashboard.css') }}">
</head>
<body>

    <div class="top-bar">
        <h1>Dashboard Operator</h1>
        <div>
            <form action="{{ route('operator.reset') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="button danger"
                    onclick="return confirm('Yakin ingin reset semua data antrian?')">
                    Reset Semua Data
                </button>
            </form>

            <a href="{{ route('operator.backup') }}" class="button success">
                Backup ke PDF
            </a>
        </div>
    </div>

    @if(session('success'))
        <div style="padding:10px; background:#d4edda; color:#155724; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nama Loket</th>
                <th>Total Antrian Masuk</th>
                <th>Antrian Menunggu</th>
                <th>Antrian Selesai</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($lokets as $loket)
                <tr>
                    <td>{{ $loket->nama }}</td>
                    <td>{{ $loket->total_masuk }}</td>
                    <td>{{ $loket->total_menunggu }}</td>
                    <td>{{ $loket->total_selesai }}</td>
                    <td>
                        <a href="{{ route('operator.detail', $loket->id) }}" class="button">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data loket</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
