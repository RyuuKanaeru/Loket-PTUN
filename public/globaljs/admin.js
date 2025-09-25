// Get CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Auto refresh data every 5 seconds
function refreshData() {
    fetch('/admin/latest-data')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateUI(data.data);
            }
        });
}

// Update UI with new data
function updateUI(data) {
    // Update current number
    const currentNumber = document.getElementById('currentNumber');
    if (data.antrian_calling) {
        currentNumber.textContent = data.antrian_calling.formatted_nomor;
        currentNumber.classList.add('calling');
    } else {
        currentNumber.textContent = '-';
        currentNumber.classList.remove('calling');
    }

    // Update waiting list
    const waitingList = document.getElementById('waitingList');
    waitingList.innerHTML = data.antrian_menunggu.map(antrian => `
        <div class="d-flex justify-content-between align-items-center mb-2 p-2 border-bottom">
            <span class="fs-5">Nomor ${antrian.formatted_nomor}</span>
            <span class="badge bg-warning">Menunggu</span>
        </div>
    `).join('');

    // Update history table
    const historyTable = document.querySelector('#historyTable tbody');
    historyTable.innerHTML = data.riwayat.map(antrian => `
        <tr>
            <td>${antrian.formatted_nomor}</td>
            <td><span class="badge bg-success">Selesai</span></td>
            <td>${new Date(antrian.updated_at).toLocaleTimeString()}</td>
        </tr>
    `).join('');
}

// Call next number
function callNext(loketId) {
    fetch(`/admin/loket/${loketId}/call-next`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshData();
            // Text to speech
            const msg = new SpeechSynthesisUtterance();
            msg.text = `Nomor antrian ${data.nomor.split('').join(' ')}, silahkan menuju ${data.loket}`;
            msg.lang = 'id-ID';
            window.speechSynthesis.speak(msg);
        } else {
            Swal.fire('Info', data.message, 'info');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
}

// Mark current number as done
function markAsDone(loketId) {
    fetch(`/admin/loket/${loketId}/mark-done`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            refreshData();
            Swal.fire({
                title: 'Selesai',
                text: data.message,
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            Swal.fire('Info', data.message, 'info');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
}

// Edit loket name
function editLoketName(loketId) {
    const currentName = document.querySelector('h2').textContent;
    document.getElementById('editLoketId').value = loketId;
    document.getElementById('loketName').value = currentName;
    
    const modal = new bootstrap.Modal(document.getElementById('editLoketModal'));
    modal.show();
}

// Update loket name
function updateLoketName() {
    const loketId = document.getElementById('editLoketId').value;
    const newName = document.getElementById('loketName').value;

    fetch(`/admin/loket/${loketId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ nama: newName })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Terjadi kesalahan', 'error');
    });
}
