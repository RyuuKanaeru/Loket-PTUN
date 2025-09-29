<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Dashboard Operator</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f5f7fa; margin:0; padding:20px; }
        h1 { margin-bottom:20px; }
        table { width:100%; border-collapse: collapse; background: white; }
        th, td { padding: 10px; border:1px solid #ccc; text-align:center; }
        th { background:#222; color:white; }
        a.button, button {
            display:inline-block; padding:8px 12px; margin:5px;
            background:#007bff; color:#fff; text-decoration:none;
            border-radius:4px; border:none; cursor:pointer;
        }
        .danger { background:#dc3545 !important; }
        .success { background: #28a745 !important; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; }
    </style>
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
