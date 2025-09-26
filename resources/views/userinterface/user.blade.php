    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Ambil Nomor Antrian - PTUN</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="{{ asset('globalcss/user.css') }}" rel="stylesheet">
    </head>
    <body class="bg-light d-flex flex-column vh-100">
            <div class="container-fluid flex-grow-1 d-flex flex-column justify-content-center align-items-center">

                <!-- Baris atas: 2 loket -->
                <div class="row justify-content-center mb-4 w-100">
                    @foreach($lokets->take(2) as $loket)
                        <div class="col-5 col-md-3 mb-4">
                            <div class="loket-button" onclick="createAntrian({{ $loket->id }})">
                                <h3>{{ $loket->nama }}</h3>
                                <button class="btn btn-primary btn-lg">
                                    <i class="fas fa-ticket-alt me-2"></i>Ambil Nomor
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Baris bawah: 3 loket -->
                <div class="row justify-content-center w-100">
                    @foreach($lokets->slice(2,3) as $loket)
                        <div class="col-4 col-md-3 mb-4">
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

            <!-- Bootstrap JS -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
            <!-- SweetAlert2 -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                function createAntrian(loketId) {
                    fetch('/antrian/create', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ loket_id: loketId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                title: 'Nomor Antrian Anda',
                                html: `<div class="swal-number">${data.nomor}</div>
                                    <p>${data.loket}</p>
                                    <p class="text-muted">${data.message}</p>`,
                                icon: 'success',
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                                scrollbarPadding: false,
                                heightAuto: false,
                                width: 'auto',
                                showClass: {
                                    popup: 'animate__animated animate__fadeIn'
                                },
                                customClass: {
                                    popup: 'swal2-popup',
                                    title: 'swal2-title',
                                    htmlContainer: 'swal2-html-container',
                                    confirmButton: 'swal2-confirm',
                                    container: 'swal2-container'
                                },
                                showClass: {
                                    popup: 'animate__animated animate__fadeIn'
                                }
                            });
                        }
                    })
                    .catch(err => Swal.fire('Error','Terjadi kesalahan','error'));
                }
            </script>
    </body>
    </html>
