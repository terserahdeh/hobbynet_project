@extends('layouts.app')
@section('title', 'Edit Profil · HobbyNet')
@section('content')
@include('packs._assets')
<style>
.hn-edit{max-width:790px;margin:35px auto 90px;padding:0 18px}.hn-edit-card{background:white;border:1px solid #eaded5;border-radius:24px;padding:clamp(24px,5vw,48px);box-shadow:0 12px 36px #48372c0c}.hn-edit-form{display:grid;gap:26px;margin-top:30px}.hn-field{display:grid;gap:9px;font-weight:650;color:#4f3b35}.hn-field input{box-sizing:border-box;display:block;width:100%;padding:15px 17px;border:1px solid #ded0c8;border-radius:14px;font:inherit;background:#fffdfb}.hn-field input:focus{outline:2px solid #9aaf91;outline-offset:2px}.hn-photo-row{display:flex;align-items:center;gap:22px;flex-wrap:wrap}.hn-photo-preview{width:104px;height:104px;object-fit:cover;border-radius:50%;border:4px solid #ecf1e5;background:#e6e9da;display:grid;place-items:center;font-size:40px;color:#4e6251}.hn-help{color:#76655d;font-size:14px;font-weight:400;margin:0}.hn-edit-actions{padding-top:14px;display:flex;gap:14px;flex-wrap:wrap}.hn-errors{padding:14px 18px;background:#fff0ed;border-radius:12px;color:#963b30}
</style>
<div class="hn-edit">
<a class="pk-back" href="{{ route('profile') }}">← Kembali ke profil</a>
<section class="hn-edit-card"><h1 class="pk-title">Edit Profil</h1><p class="pk-sub">Perbarui identitas dan foto yang tampil di HobbyNet.</p>
@if($errors->any())<div class="hn-errors" role="alert"><strong>Periksa kembali formulirmu:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form class="hn-edit-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">@csrf @method('PATCH')
<div class="hn-photo-row">
@if($user->avatar_path)<img class="hn-photo-preview" src="{{ asset('storage/'.$user->avatar_path) }}" alt="Foto profil saat ini" id="hn-preview">@else <div class="hn-photo-preview" id="hn-placeholder">{{ mb_strtoupper(mb_substr($user->name,0,1)) }}</div><img class="hn-photo-preview" id="hn-preview" src="" alt="Pratinjau foto" hidden>@endif
<div style="flex:1;min-width:220px"><label class="hn-field" for="hn-avatar">Foto profil<input type="file" id="hn-avatar" name="avatar" accept="image/jpeg,image/png,image/webp"></label><p class="hn-help">JPG, PNG, atau WebP. Maksimal 2 MB.</p>
@if($user->avatar_path)<label style="display:flex;align-items:center;gap:8px;margin-top:12px"><input type="checkbox" name="remove_avatar" value="1"> Hapus foto profil</label>@endif</div>
</div>
<label class="hn-field">Nama lengkap<input required name="name" maxlength="100" value="{{ old('name', $user->name) }}" autocomplete="name"></label>
<label class="hn-field">Nickname / nama panggilan<input name="nickname" maxlength="40" value="{{ old('nickname', $user->nickname) }}" placeholder="Contoh: Varor"><span class="hn-help">Tidak harus unik dan tidak mengubah email login.</span></label>
<div class="hn-edit-actions"><button class="btn btn--primary" type="submit">Simpan perubahan</button><a class="btn btn--ghost" href="{{ route('profile') }}">Batal</a></div>
</form></section></div>
<script>document.getElementById('hn-avatar')?.addEventListener('change', function(){let f=this.files?.[0];if(!f)return;let img=document.getElementById('hn-preview');img.src=URL.createObjectURL(f);img.hidden=false;document.getElementById('hn-placeholder')?.setAttribute('hidden','');});</script>
@endsection
