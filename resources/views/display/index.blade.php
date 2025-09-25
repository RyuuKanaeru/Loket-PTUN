<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Display Antrian</title>
<link href="{{ asset('globalcss/display.css') }}" rel="stylesheet">
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div class="title">Display Antrian</div>
            <div style="display:flex; gap:16px; align-items:center;">
                <div class="last-updated" id="lastUpdated">—</div>
                <div class="controls" id="controls">
                    <button class="btn" id="toggleVoice">Voice: Off</button>
                    <button class="btn" id="forceRefresh">Refresh</button>
                </div>
            </div>
        </div>

        <div class="grid" id="grid">
            <!-- Loket aktif di kiri -->
            <div class="big-box" id="activeBox">
                <div class="nomor empty">-</div>
                <div class="loket-name">—</div>
            </div>

            <!-- Loket lain di kanan -->
            <div class="side-list" id="sideList">
                @foreach($lokets as $loket)
                    <div class="side-item" data-loket-id="{{ $loket->id }}">
                        <span>{{ $loket->nama }}</span>
                        <span class="nomor small empty">-</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="footer">
            Berikutnya: <span id="nextQueue">—</span>
        </div>
    </div>

<script>
(() => {
    const DATA_URL = "{{ route('display.data') }}";
    const POLL_INTERVAL = 3000;
    const activeBox = document.getElementById('activeBox');
    const sideList = document.getElementById('sideList');
    const lastUpdated = document.getElementById('lastUpdated');
    const toggleVoiceBtn = document.getElementById('toggleVoice');
    const forceRefreshBtn = document.getElementById('forceRefresh');
    const nextQueueEl = document.getElementById('nextQueue');

    const current = {};
    let voiceEnabled = false;

    toggleVoiceBtn.addEventListener('click', () => {
        voiceEnabled = !voiceEnabled;
        toggleVoiceBtn.textContent = voiceEnabled ? 'Voice: On' : 'Voice: Off';
    });

    forceRefreshBtn.addEventListener('click', fetchAndRender);

    function speak(text) {
        if (!voiceEnabled) return;
        if ('speechSynthesis' in window) {
            try {
                const utter = new SpeechSynthesisUtterance(text);
                utter.lang = 'id-ID';
                speechSynthesis.cancel();
                speechSynthesis.speak(utter);
            } catch (e) {
                console.warn('Speech error', e);
            }
        }
    }

    function renderLokets(lokets) {
        if (!lokets.length) return;

        // Anggap loket pertama di API = loket aktif
        const active = lokets[0];
        const others = lokets.slice(1);

        // Render active
        const nomorEl = activeBox.querySelector('.nomor');
        const nameEl = activeBox.querySelector('.loket-name');
        const prev = current[active.id] ?? null;
        const next = active.nomor;

        if (next === null) {
            nomorEl.classList.add('empty');
            nomorEl.textContent = '-';
        } else {
            nomorEl.classList.remove('empty');
            nomorEl.textContent = next;
        }
        nameEl.textContent = active.nama;

        if (prev === null && next !== null || (prev !== null && String(prev) !== String(next))) {
            activeBox.classList.add('highlight');
            setTimeout(() => activeBox.classList.remove('highlight'), 900);
            speak(`Loket ${active.nama.replace(/[^a-z0-9 ]/ig,'')}, nomor ${next?.split('').join(' ')}`);
        }
        current[active.id] = next;

        // Render others
        sideList.querySelectorAll('.side-item').forEach(item => {
            const id = item.dataset.loketId;
            const loket = others.find(l => String(l.id) === id);
            const nomorEl = item.querySelector('.nomor');

            if (!loket || loket.nomor === null) {
                nomorEl.classList.add('empty');
                nomorEl.textContent = '-';
            } else {
                nomorEl.classList.remove('empty');
                nomorEl.textContent = loket.nomor;
            }
        });

        // contoh: nextQueue bisa ambil dari data API kalau ada
        nextQueueEl.textContent = lokets.filter(l => l.nomor).map(l => l.nomor).slice(1,4).join(' | ');
    }

    async function fetchAndRender() {
        try {
            const res = await fetch(DATA_URL, { cache: 'no-store' });
            if (!res.ok) throw new Error('Network error');
            const json = await res.json();
            const lokets = json.lokets || [];
            renderLokets(lokets);
            lastUpdated.textContent = 'Terakhir: ' + (json.timestamp || new Date().toLocaleTimeString());
        } catch (err) {
            console.error('Fetch error', err);
            lastUpdated.textContent = 'Terakhir: gagal mengambil data';
        }
    }

    fetchAndRender();
    setInterval(fetchAndRender, POLL_INTERVAL);

    window.addEventListener('keydown', (e) => {
        if (e.key.toLowerCase() === 'v') {
            toggleVoiceBtn.click();
        } else if (e.key.toLowerCase() === 'r') {
            forceRefreshBtn.click();
        }
    });
})();
</script>
</body>
</html>
