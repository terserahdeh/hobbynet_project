@extends('layouts.app')
@section('title', 'Bagikan Karya Course · HobbyNet')
@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">
<div class="rm-page"><div class="rm-body"><div class="rm-container rm-container--form">
<a class="rm-link" href="{{ route('courses.show', $starterPack) }}">← Kembali ke course</a>
<header class="rm-center" style="margin:34px 0"><div class="rm-hero-icon">✦</div><h1 class="rm-display">Lihat Karyamu!</h1><p class="rm-sub">Course {{ $starterPack->project->title }} selesai. Bagikan foto dan ceritakan pengalamanmu.</p></header>
@if(session('status'))<p role="status">{{ session('status') }}</p>@endif
@if($errors->any())<div class="rm-card" style="padding:16px;color:#ac4355" role="alert"><strong>Periksa formulir:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<p class="rm-sub" style="margin:10px 0 20px">Satu karya menggunakan satu foto (JPG, PNG, WebP · maksimal 5 MB). Memilih foto baru akan otomatis mengganti foto lama saat disimpan.</p>
<form action="{{ route('courses.submission.save', $starterPack) }}" method="POST" enctype="multipart/form-data" class="rm-card rm-form">@csrf
<div class="rm-form__body">
<div><label class="rm-label" for="work-title">Judul hasil karya</label><input class="rm-input" id="work-title" type="text" name="title" maxlength="120" value="{{ old('title', $submission?->title) }}" placeholder="Contoh: Tatakan gelas rajut pertamaku" required></div>
<div class="rm-media"><div><span class="rm-label">Foto hasil karya (maksimal 1 foto)</span><label class="rm-upload" for="work-photos"><span class="rm-upload__icon">📷</span><strong>Klik untuk memilih foto</strong><small>Satu foto · JPG, PNG, WebP · maksimal 5 MB</small><input type="file" id="work-photos" name="photos[]" accept="image/jpeg,image/png,image/webp"></label></div>
<div><span class="rm-label">Preview</span><div class="rm-preview"><div class="rm-preview__empty" id="rm-empty" @if(count($submission?->photos ?? [])) hidden @endif>Foto akan terlihat di sini.</div><ul class="rm-photos" id="rm-photos">@foreach($submission?->photos ?? [] as $photoIndex => $path)<li class="rm-photo"><img src="{{ route('courses.work.photo', [$submission, $photoIndex]) }}" alt="Foto yang tersimpan"></li>@endforeach</ul></div></div></div>
<div><label class="rm-label" for="work-reflection">Deskripsi dan pengalaman</label><textarea class="rm-textarea" id="work-reflection" name="reflection" maxlength="2000" rows="5" placeholder="Alat yang kamu gunakan, tantangan, dan hal yang kamu pelajari…">{{ old('reflection', $submission?->reflection) }}</textarea></div>
</div><div class="rm-form__foot"><a class="rm-link" href="{{ route('courses.show', $starterPack) }}">← Kembali</a><div class="rm-form__actions"><button class="rm-btn rm-btn--ghost" type="submit" name="action" value="draft">Simpan Draft</button><button class="rm-btn rm-btn--primary" type="submit" name="action" value="submit">Kirim Hasil Karya →</button></div></div>
</form></div></div></div>
<script>(function(){
const input=document.getElementById('work-photos'),list=document.getElementById('rm-photos'),empty=document.getElementById('rm-empty');
input?.addEventListener('change',()=>{
  list.querySelectorAll('[data-preview]').forEach(el=>el.remove());
  const file=input.files[0];
  list.querySelectorAll('li:not([data-preview])').forEach(el=>el.hidden=!!file);
  if(file){const li=document.createElement('li');li.className='rm-photo';li.dataset.preview='1';const img=document.createElement('img');img.src=URL.createObjectURL(file);img.alt='Foto pengganti';li.append(img);list.append(li)}
  empty.hidden=!!list.querySelector('li:not([hidden])');
});})();</script>
@endsection
