<!doctype html>
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
            {{-- Optional initial items for progressive enhancement --}}
            @foreach($lokets as $loket)
                <div class="box" data-loket-id="{{ $loket->id }}">
                    <div class="loket-name">{{ $loket->nama }}</div>
                    <div class="nomor empty">-</div>
                </div>
            @endforeach
        </div>
    </div>

<script>
(() => {
    const DATA_URL = "{{ route('display.data') }}"; // endpoint untuk polling
    const POLL_INTERVAL = 3000; // ms
    const grid = document.getElementById('grid');
    const lastUpdated = document.getElementById('lastUpdated');
    const toggleVoiceBtn = document.getElementById('toggleVoice');
    const forceRefreshBtn = document.getElementById('forceRefresh');

    // store current shown numbers to detect perubahan
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

    function findBoxById(id) {
        return grid.querySelector(`.box[data-loket-id="${id}"]`);
    }

    function renderLokets(lokets) {
        lokets.forEach(l => {
            const id = l.id;
            const box = findBoxById(id);
            if (!box) {
                // jika belum ada, buat kotak baru (output safety)
                const newBox = document.createElement('div');
                newBox.className = 'box';
                newBox.setAttribute('data-loket-id', id);
                newBox.innerHTML = `<div class="loket-name">${l.nama}</div><div class="nomor ${l.nomor===null ? 'empty' : ''}">${l.nomor===null ? '-' : l.nomor}</div>`;
                grid.appendChild(newBox);
                if (l.nomor !== null) {
                    current[id] = l.nomor;
                }
                return;
            }

            const nomorEl = box.querySelector('.nomor');
            const prev = current[id] ?? null;
            const next = l.nomor;

            if (next === null) {
                nomorEl.classList.add('empty');
                nomorEl.textContent = '-';
            } else {
                nomorEl.classList.remove('empty');
                nomorEl.textContent = next;
            }

            // jika berubah — tambahkan highlight dan suara
            if (prev === null && next !== null || (prev !== null && String(prev) !== String(next))) {
                // visual highlight
                box.classList.add('highlight');
                setTimeout(() => box.classList.remove('highlight'), 900);

                // speak
                speak(`Loket ${l.nama.replace(/[^a-z0-9 ]/ig,'')}, nomor ${next}`);
            }

            // update cache
            current[id] = next;
        });
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

    // init: immediate fetch + polling
    fetchAndRender();
    setInterval(fetchAndRender, POLL_INTERVAL);

    // keyboard shortcuts (optional): tekan "v" utk toggle voice
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
