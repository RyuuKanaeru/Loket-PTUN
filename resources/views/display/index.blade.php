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
            <div class="title">
                <img src="{{ asset('img/PTUN logo remove.png') }}" alt="PTUN Logo" class="logo">
                Display Antrian PTUN Bandung
            </div>
            <div class="controls" id="controls">
                <button class="btn" id="toggleVoice">Voice: Off</button>
            </div>
        </div>

        <div class="grid">
            <div class="loket-container">
                <div class="loket-left" id="loket-group">
                    @foreach($lokets->take(3) as $loket)
                        <div class="box" data-loket-id="{{ $loket->id }}">
                            <div class="loket-name">{{ $loket->nama }}</div>
                            <div class="nomor empty">-</div>
                        </div>
                    @endforeach
                </div>
                <div class="loket-right">
                    @foreach($lokets->skip(3)->take(2) as $loket)
                        <div class="box" data-loket-id="{{ $loket->id }}">
                            <div class="loket-name">{{ $loket->nama }}</div>
                            <div class="nomor empty">-</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="video-container">
                <video id="displayVideo" autoplay loop muted>
                    <source src="{{ asset('video/vidio.mp4') }}" type="video/mp4">
                    <!-- Tambahkan format video lain jika diperlukan -->
                    <source src="{{ asset('video/vidio.mp4') }}" type="video/webm">
                    Browser Anda tidak mendukung tag video.
                </video>
            </div>
        </div>
    </div>

<script>
(() => {
    const DATA_URL = "{{ route('display.data') }}"; // endpoint untuk polling
    const POLL_INTERVAL = 3000; // ms
    const loketGroup = document.getElementById('loket-group');
    const toggleVoiceBtn = document.getElementById('toggleVoice');

    // store current shown numbers to detect perubahan
    const current = {};

    let voiceEnabled = false;
    toggleVoiceBtn.addEventListener('click', () => {
        voiceEnabled = !voiceEnabled;
        toggleVoiceBtn.textContent = voiceEnabled ? 'Voice: On' : 'Voice: Off';
    });

    // Auto refresh handling is done via setInterval

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
        return document.querySelector(`.box[data-loket-id="${id}"]`);
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
        } catch (err) {
            console.error('Fetch error', err);
        }
    }

    // init: immediate fetch + polling
    fetchAndRender();
    setInterval(fetchAndRender, POLL_INTERVAL);

    // keyboard shortcuts: tekan "v" utk toggle voice
    window.addEventListener('keydown', (e) => {
        if (e.key.toLowerCase() === 'v') {
            toggleVoiceBtn.click();
        }
    });
})();
</script>
</body>
</html>