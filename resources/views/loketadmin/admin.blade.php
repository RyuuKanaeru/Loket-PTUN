<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Panel - Sistem Antrian</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('globalcss/admin.css') }}" rel="stylesheet">
    
</head>
<body class="bg-light">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h4 class="text-white mb-4">Sistem Antrian</h4>
            <hr class="bg-light">
            @foreach($lokets as $loket)
            <a href="{{ route('admin.set-active-loket', $loket->id) }}" 
               class="sidebar-link {{ $selectedLoket && $selectedLoket->id == $loket->id ? 'active' : '' }}">
                <i class="fas fa-desktop me-2"></i>{{ $loket->nama }}
                <span class="badge bg-primary float-end">
                    {{ $loket->antrianMenunggu->count() }}
                </span>
            </a>
            @endforeach
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if($selectedLoket)
        <div class="container">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2>{{ $selectedLoket->nama }}</h2>
                        <button class="btn btn-outline-primary" onclick="editLoketName({{ $selectedLoket->id }})">
                            <i class="fas fa-edit me-2"></i>Edit Nama
                        </button>
                    </div>
                </div>
            </div>

            <!-- Current Number and Controls -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="text-muted mb-3">Nomor yang Sedang Dipanggil</h4>
                            <div id="currentNumber" class="current-number {{ $selectedLoket->antrianCalling->first() ? 'calling' : '' }}">
                                @if($selectedLoket->antrianCalling->first())
                                    {{ $selectedLoket->antrianCalling->first()->nomor }}
                                @else
                                    -
                                @endif
                            </div>
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg mb-2" onclick="callNext({{ $selectedLoket->id }})">
                                    <i class="fas fa-bullhorn me-2"></i>Panggil Selanjutnya
                                </button>
                                <button class="btn btn-success btn-lg" onclick="markAsDone({{ $selectedLoket->id }})">
                                    <i class="fas fa-check me-2"></i>Selesai
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Daftar Antrian Menunggu</h5>
                        </div>
                        <div class="card-body">
                            <div class="queue-list" id="waitingList">
                                @foreach($selectedLoket->antrianMenunggu as $antrian)
                                <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
                                    <span class="fs-5">Nomor {{ $antrian->nomor }}</span>
                                    <span class="badge bg-warning">Menunggu</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- History Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Riwayat Antrian</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover history-table" id="historyTable">
                                    <thead>
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Status</th>
                                            <th>Waktu Selesai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedLoket->riwayat as $antrian)
                                        <tr>
                                            <td>{{ $antrian->nomor }}</td>
                                            <td><span class="badge bg-success">Selesai</span></td>
                                            <td>{{ $antrian->updated_at->format('H:i:s') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="container text-center">
            <div class="alert alert-info">
                <h4>Pilih loket dari menu samping untuk mulai memanggil antrian</h4>
            </div>
        </div>
        @endif
    </div>

    <!-- Modal untuk edit nama loket -->
    <div class="modal fade" id="editLoketModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Nama Loket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editLoketId">
                    <div class="mb-3">
                        <label for="loketName" class="form-label">Nama Loket</label>
                        <input type="text" class="form-control" id="loketName">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="updateLoketName()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include SweetAlert2 for better notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Include Admin JS -->
    <script src="{{ asset('globaljs/admin.js') }}"></script>
    
    <script>
        // Start auto refresh if a loket is selected
        @if($selectedLoket)
            setInterval(refreshData, 5000);
        @endif
    </script>
</body>
</html>