<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ambil Nomor Antrian - PTUN</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('globalcss/user.css') }}" rel="stylesheet">
   
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Antrian PTUN</a>
        </div>
    </nav>

    <div class="container my-5 flex-grow-1">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2>Silahkan Pilih Loket</h2>
                    <p class="text-muted">Klik pada loket yang Anda tuju untuk mengambil nomor antrian</p>
                </div>
                <div class="row">
                    @foreach($lokets as $loket)
                    <div class="col-md-6">
                        <div class="loket-button" onclick="createAntrian({{ $loket->id }})">
                            <h3>{{ $loket->nama }}</h3>
                            <button class="btn btn-primary btn-lg">
                                <i class="fas fa-ticket-alt me-2"></i>Ambil Nomor
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include SweetAlert2 for better notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Create new queue number
        function createAntrian(loketId) {
            fetch('/antrian/create', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ loket_id: loketId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success popup with Swal
                    Swal.fire({
                        title: 'Nomor Antrian Anda',
                        html: `
                            <div class="number">${data.nomor}</div>
                            <p>${data.loket}</p>
                            <p class="text-muted">${data.message}</p>
                        `,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    });
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            });
        }
    </script>
</body>
</html>
