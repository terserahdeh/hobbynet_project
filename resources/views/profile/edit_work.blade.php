@extends('layouts.app')
@section('title','Edit Hasil Karya · HobbyNet')
@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">
<div class="rm-page"><div class="rm-body"><div class="rm-container rm-container--form">
<a class="rm-link" href="{{ route('roadmap.work.show',$submission) }}">← Kembali ke karya</a>
<header class="rm-center" style="margin:30px 0"><h1 class="rm-display">Edit Karyamu</h1><p class="rm-sub">Tambahkan cerita atau ganti foto hasilmu. Foto pertama menjadi sampul.</p></header>
@if($errors->any())<div class="rm-card" style="padding:16px;color:#ad4550" role="alert">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
<form class="rm-card rm-form" method="POST" enctype="multipart/form-data" action="{{ route('roadmap.work.update',$submission) }}">@csrf @method('PATCH')
<div class="rm-form__body"><div><label class="rm-label" for="title">Judul karya</label><input class="rm-input" type="text" id="title" name="title" maxlength="120" required value="{{ old('title',$submission->title) }}"></div>
<div><span class="rm-label">Foto karya saat ini</span><ul class="rm-photos" id="old-photo">@if($submission->photos->first())<li class="rm-photo"><img src="{{ $submission->photos->first()->url }}" alt="Foto karya"></li>@else<li>Belum ada foto.</li>@endif</ul></div>
<div><label class="rm-label" for="photos">Ganti foto karya</label><input class="rm-input" type="file" accept="image/jpeg,image/png,image/webp" name="photos[]" id="photos"><p class="rm-sub">Maksimal satu foto, JPG/PNG/WebP, 5 MB. Jika memilih foto baru, foto lama akan otomatis digantikan ketika disimpan.</p><div id="new-preview" class="rm-photos"></div></div>
<div><label class="rm-label" for="reflection">Deskripsi & cerita karya</label><textarea class="rm-textarea" id="reflection" name="reflection" maxlength="2000" rows="5">{{ old('reflection',$submission->reflection) }}</textarea></div></div>
<div class="rm-form__foot"><a class="rm-link" href="{{ route('roadmap.work.show',$submission) }}">Batal</a><button class="rm-btn rm-btn--primary" type="submit">Simpan Perubahan →</button></div></form>
</div></div></div><script>document.getElementById('photos')?.addEventListener('change',function(){const box=document.getElementById('new-preview');box.replaceChildren();document.getElementById('old-photo').hidden=this.files.length>0;for(const file of this.files){if(!file.type.startsWith('image/'))continue;const img=document.createElement('img');img.src=URL.createObjectURL(file);img.alt='Pratinjau';img.className='rm-photo';box.append(img)}})</script>
@endsection
