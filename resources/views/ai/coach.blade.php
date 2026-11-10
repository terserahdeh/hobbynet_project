@extends('layouts.app')

@section('title', 'Coach AI · HobbyNet')

@section('content')
<div class="coach" data-ask-url="{{ route('coach.ask') }}" data-plus-url="{{ route('plus') }}">
    <header class="coach__head">
        <div>
            <p class="eyebrow">✦ Coach AI</p>
            <h1 class="coach__title">Tanya apa saja soal hobimu</h1>
            <p class="coach__lead">Jawabannya menyesuaikan level, alat, dan langkah roadmap yang sedang kamu kerjakan.</p>
        </div>

        <div class="coach__meter" aria-live="polite">
            @if ($isPlus)
                <span class="pill pill--plus">Plus ✦ tanpa batas harian</span>
            @else
                <span class="coach__left"><b id="coach-left">{{ $remaining }}</b> / {{ $limit }} pertanyaan gratis hari ini</span>
                <span class="coach__meterbar"><span id="coach-meterfill" style="width: {{ $limit > 0 ? round($remaining / $limit * 100) : 0 }}%"></span></span>
                <a class="link-quiet" href="{{ route('plus') }}">Upgrade ke Plus</a>
            @endif
        </div>
    </header>

    <div class="coach__grid">
        {{-- Percakapan --}}
        <section class="chat" aria-label="Percakapan dengan Coach AI">
            <div class="chat__log" id="chat-log" role="log" aria-live="polite">
                @if ($messages->isEmpty())
                    <div class="msg msg--bot" id="chat-welcome">
                        <span class="msg__avatar" aria-hidden="true">✦</span>
                        <div class="msg__bubble">
                            Halo! Aku Coach AI-mu.
                            @if ($context['step'])
                                Kamu sedang di langkah <b>{{ $context['step']['title'] }}</b>. Ceritakan bagian yang bikin bingung, atau pilih salah satu pertanyaan cepat di samping.
                            @else
                                Ceritakan hobi yang ingin kamu mulai, atau pilih salah satu pertanyaan cepat di samping.
                            @endif
                        </div>
                    </div>
                @endif

                @foreach ($messages as $m)
                    <div class="msg {{ $m->role === 'user' ? 'msg--me' : 'msg--bot' }}">
                        @if ($m->role !== 'user')<span class="msg__avatar" aria-hidden="true">✦</span>@endif
                        <div class="msg__bubble">{{ $m->content }}</div>
                    </div>
                @endforeach
            </div>

            @error('message') <p class="chat__error" role="alert">{{ $message }}</p> @enderror
            <p class="chat__error" id="chat-error" role="alert" hidden></p>

            <form class="chat__form" id="chat-form" method="POST" action="{{ route('coach.ask') }}">
                @csrf
                <label class="sr-only" for="chat-input">Pertanyaanmu</label>
                <textarea id="chat-input" name="message" rows="2" maxlength="600" required
                          placeholder="Contoh: tusukanku selalu kendor, kenapa ya?">{{ old('message') }}</textarea>
                <button type="submit" class="btn btn--primary" id="chat-send">Kirim</button>
            </form>

            <div class="chat__foot">
                @if ($provider === 'mock')
                    <span class="pill pill--demo" title="Isi ANTHROPIC_API_KEY atau GEMINI_API_KEY di .env untuk jawaban AI penuh">Mode simulasi (bukan AI generatif)</span>
                @else
                    <span class="pill pill--live">AI generatif aktif · {{ $provider === 'anthropic' ? 'Claude' : 'Gemini' }}</span>
                @endif
                @if ($messages->isNotEmpty())
                    <form method="POST" action="{{ route('coach.clear') }}" onsubmit="return confirm('Hapus semua riwayat percakapan?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="link-quiet link-btn">Hapus riwayat</button>
                    </form>
                @endif
            </div>
        </section>

        {{-- Samping: konteks + pertanyaan cepat --}}
        <aside class="coach__side">
            <section class="panel">
                <h2 class="panel__title">Yang Coach ketahui</h2>
                <ul class="ctx">
                    <li><span>Level</span><b>{{ ucfirst($context['level']) }}</b></li>
                    <li><span>Hobi</span><b>{{ $context['hobbies'] ? implode(', ', $context['hobbies']) : 'Belum memilih' }}</b></li>
                    @if ($context['step'])
                        <li><span>Langkah aktif</span><b>{{ $context['step']['title'] }}</b></li>
                    @endif
                    <li><span>Target harian</span><b>{{ $context['goal'] }} menit</b></li>
                </ul>
            </section>

            <section class="panel">
                <h2 class="panel__title">Pertanyaan cepat</h2>
                <div class="quick">
                    <button type="button" class="quick__btn" data-q="Aku bingung dan tersangkut di langkah ini, bantu aku ya.">Aku bingung di langkah ini</button>
                    <button type="button" class="quick__btn" data-q="Rekomendasikan proyek dan bahan yang cocok untuk budget-ku.">Rekomendasi proyek sesuai budget</button>
                    <button type="button" class="quick__btn" data-q="Aku lagi malas, semangatin aku dong.">Aku lagi malas, semangatin</button>
                    @if ($isPlus)
                        <button type="button" class="quick__btn quick__btn--plus" data-q="Buatkan rencana belajar 7 hari untukku." data-mode="weekly_plan">✦ Rencana belajar 7 hari</button>
                    @else
                        <a class="quick__btn quick__btn--plus is-locked" href="{{ route('plus') }}">✦ Rencana belajar 7 hari <small>Plus</small></a>
                    @endif
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var root = document.querySelector('.coach');
    if (!root) return;
    var form = document.getElementById('chat-form');
    var input = document.getElementById('chat-input');
    var log = document.getElementById('chat-log');
    var errBox = document.getElementById('chat-error');
    var sendBtn = document.getElementById('chat-send');
    var tokenMeta = document.querySelector('meta[name="csrf-token"]');
    var token = tokenMeta ? tokenMeta.content : form.querySelector('input[name=_token]').value;
    var busy = false;

    function scrollDown() { log.scrollTop = log.scrollHeight; }
    scrollDown();

    function bubble(role, text) {
        var wrap = document.createElement('div');
        wrap.className = 'msg ' + (role === 'user' ? 'msg--me' : 'msg--bot');
        if (role !== 'user') {
            var av = document.createElement('span');
            av.className = 'msg__avatar'; av.setAttribute('aria-hidden', 'true'); av.textContent = '✦';
            wrap.appendChild(av);
        }
        var b = document.createElement('div');
        b.className = 'msg__bubble';
        b.textContent = text;           // textContent: aman dari HTML injection
        wrap.appendChild(b);
        log.appendChild(wrap);
        scrollDown();
        return wrap;
    }

    function showError(text, upgrade) {
        errBox.hidden = false;
        errBox.textContent = text + ' ';
        if (upgrade) {
            var a = document.createElement('a');
            a.href = root.dataset.plusUrl; a.textContent = 'Lihat HobbyNet Plus';
            errBox.appendChild(a);
        }
    }

    function setLeft(n) {
        var el = document.getElementById('coach-left');
        if (!el) return;
        el.textContent = n;
        var fill = document.getElementById('coach-meterfill');
        var total = parseInt(el.parentNode.textContent.split('/')[1], 10) || 1;
        if (fill) fill.style.width = Math.round(n / total * 100) + '%';
    }

    function send(text, mode) {
        if (busy || !text.trim()) return;
        busy = true; sendBtn.disabled = true; errBox.hidden = true;
        var welcome = document.getElementById('chat-welcome');
        if (welcome) welcome.remove();
        bubble('user', text);
        var typing = bubble('bot', '');
        typing.querySelector('.msg__bubble').innerHTML = '<span class="typing" aria-label="Coach sedang mengetik"><i></i><i></i><i></i></span>';

        fetch(root.dataset.askUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
            body: JSON.stringify({ message: text, mode: mode || null })
        }).then(function (res) {
            return res.json().then(function (data) { return { ok: res.ok, data: data }; });
        }).then(function (r) {
            typing.remove();
            if (!r.ok) {
                showError(r.data.error || (r.data.errors && r.data.errors.message ? r.data.errors.message[0] : 'Terjadi masalah. Coba lagi.'), r.data.upgrade);
                if (typeof r.data.remaining === 'number') setLeft(r.data.remaining);
                return;
            }
            bubble('bot', r.data.reply);
            if (typeof r.data.remaining === 'number') setLeft(r.data.remaining);
            input.value = '';
        }).catch(function () {
            typing.remove();
            showError('Koneksi bermasalah. Coba lagi sebentar lagi.');
        }).then(function () {
            busy = false; sendBtn.disabled = false; input.focus();
        });
    }

    form.addEventListener('submit', function (e) { e.preventDefault(); send(input.value, null); });
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); form.requestSubmit(); }
    });
    document.querySelectorAll('.quick__btn[data-q]').forEach(function (btn) {
        btn.addEventListener('click', function () { send(btn.dataset.q, btn.dataset.mode); });
    });
})();
</script>
@endpush
