<div class="hn-work-detail">
    <a class="pk-back" href="{{ route('profile') }}#hasil-karya">← Kembali ke hasil karya</a>
    <article class="hn-work-detail-card">
        <p class="hn-eyebrow">HASIL KARYAKU</p>
        <h1 class="pk-title">{{ $workTitle }}</h1>
        <p class="hn-work-detail-meta">{{ $workCourse }} @if($workDate) · {{ $workDate->locale('id')->translatedFormat('j F Y') }} @endif</p>
        @if(session('status'))<p class="hn-success-message" role="status">{{ session('status') }}</p>@endif
        <p class="hn-photo-instruction">Satu foto karya · Klik untuk melihat ukuran penuh.</p>
        
            <div class="hn-work-photos">
                @foreach($workPhotos as $photoUrl)
                    <a
                        href="{{ $photoUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <img
                            src="{{ $photoUrl }}"
                            alt="Foto karya {{ $workTitle }}"
                            loading="lazy"
                            onerror="
                                this.hidden=true;
                                this.parentElement.nextElementSibling.hidden=false;
                            "
                        >
                    </a>

                    <p class="hn-missing-photo" hidden>
                        Foto tidak ditemukan di penyimpanan.
                        Klik Edit Karya untuk mengunggah ulang foto.
                    </p>
                @endforeach
            </div>
            


        <h2 class="pk-h2">Cerita di balik karya</h2>
        <p class="hn-work-story">{{ $workReflection ?: 'Belum ada cerita yang ditambahkan.' }}</p>
        @isset($workEditUrl)<a class="btn btn--ghost" href="{{ $workEditUrl }}">✎ Edit karya</a>@endisset
    </article>
</div>
<style>
.hn-work-detail{max-width:940px;margin:0 auto;padding:30px 18px 80px}.hn-work-detail-card{background:white;border:1px solid #f0e9e4;border-radius:26px;padding:clamp(22px,4vw,42px);margin-top:17px;box-shadow:0 10px 30px rgba(70,46,33,.035)}.hn-work-detail-card .pk-title{font-size:clamp(1.8rem,4vw,2.65rem);line-height:1.18;margin:0 0 10px}.hn-work-detail-meta{color:#97867a;font-size:.9rem;margin:0 0 25px}.hn-work-photos{display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,240px),1fr));gap:13px;margin-bottom:26px}.hn-work-photos img{width:100%;aspect-ratio:4/3;object-fit:cover;border-radius:17px;display:block}.hn-work-story{white-space:pre-line;line-height:1.8;color:#64544d;margin:12px 0 25px;overflow-wrap:anywhere}
.hn-photo-instruction{color:#89766b;font-size:.88rem}.hn-success-message{background:#ecf4e8;color:#456648;padding:12px 16px;border-radius:14px}.hn-work-photos a:first-child:nth-last-child(1){grid-column:1/-1;max-width:650px}.hn-work-photos a:first-child:not(:last-child){grid-column:1/-1}.hn-work-photos a:first-child:not(:last-child) img{aspect-ratio:16/9}.hn-work-photos a:hover img{filter:brightness(.95)}
.hn-missing-photo {
    padding: 16px;
    border-radius: 12px;
    background: #fff1e8;
    color: #8b4538;
}

.hn-missing-photo[hidden] {
    display: none;
}
</style>
