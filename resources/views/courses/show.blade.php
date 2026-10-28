@extends('layouts.app')
@section('title', 'Belajar ' . $starterPack->project->title . ' · HobbyNet')
@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">
<style>
.hn-course-body{max-width:1160px;margin:30px auto 90px;padding:0 24px}.hn-course-layout{display:grid;grid-template-columns:280px minmax(0,1fr);gap:26px;margin-top:25px}.hn-course-panel{background:white;border:1px solid #eee2d9;border-radius:20px;padding:clamp(22px,4vw,40px);box-shadow:0 8px 30px #4a403008}.hn-course-nav{list-style:none;padding:0;margin:0;display:grid;gap:10px}.hn-course-nav a,.hn-course-nav span{display:flex;gap:12px;padding:14px;border-radius:13px;text-decoration:none;color:#503f36;background:#f9f5f1}.hn-course-nav .active{background:#e6ede2;font-weight:700}.hn-course-num{background:#d9e3d3;border-radius:50%;width:27px;height:27px;display:grid;place-items:center;flex-shrink:0}.hn-course-instruction{display:grid;grid-template-columns:36px 1fr;gap:14px;margin:22px 0;line-height:1.85}.hn-course-practice{background:#f7f2e9;padding:20px;border-radius:16px;margin:28px 0}.hn-course-bar{height:10px;border-radius:20px;overflow:hidden;background:#e7e6db}.hn-course-bar>span{display:block;background:#88a27f;height:100%}@media(max-width:760px){.hn-course-layout{grid-template-columns:1fr}}
</style>
<div class="hn-course-body">
<a class="pk-back" href="{{ route('packs.show', $starterPack) }}">← Kembali ke detail dan perlengkapan</a>
<header class="hn-course-panel" style="margin-top:20px"><p class="rm-eyebrow">Guided experience · {{ $starterPack->is_premium ? 'HobbyNet Plus' : 'Gratis' }}</p><h1>{{ $starterPack->project->title }}</h1><p>Ikuti langkahnya seperti roadmap Beranda. Selesaikan latihan agar tahap berikutnya terbuka.</p>
<div class="hn-course-bar" role="progressbar" aria-valuenow="{{ count($completed) }}" aria-valuemin="0" aria-valuemax="{{ count($lessons) }}"><span style="width:{{ round(100*count($completed)/max(1,count($lessons))) }}%"></span></div><small>{{ count($completed) }} / {{ count($lessons) }} tahap selesai</small></header>
@if(session('status'))<div class="pk-note" role="status">{{ session('status') }}</div>@endif
<div class="hn-course-layout"><aside><ol class="hn-course-nav">
@foreach($lessons as $l)<li>@php $isDone=in_array($l['number'],$completed);$isLocked=$l['number']>$active&&!$isDone;@endphp
@if($isLocked)<span><b class="hn-course-num">🔒</b>{{ $l['title'] }}</span>@else<a class="{{ $l['number']===$active?'active':'' }}" href="#lesson-{{ $l['number'] }}"><b class="hn-course-num">{{ $isDone?'✓':$l['number'] }}</b>{{ $l['title'] }}</a>@endif</li>@endforeach
</ol></aside><main>
@foreach($lessons as $lesson)
@php $done=in_array($lesson['number'],$completed);$locked=$lesson['number']>$active&&!$done;@endphp
<section class="hn-course-panel" id="lesson-{{ $lesson['number'] }}" style="margin-bottom:22px;{{ $locked?'opacity:.7':'' }}">
<p class="rm-eyebrow">Langkah {{ $lesson['number'] }} dari {{ count($lessons) }} · {{ $done?'Selesai':($locked?'Terkunci':'Siap dipelajari') }}</p><h2>{{ $lesson['title'] }}</h2>
@if(!$locked)<div class="hn-course-instruction"><span class="hn-course-num">{{ $lesson['number'] }}</span><p style="margin:0">{{ $lesson['content'] }}</p></div>
<div class="hn-course-practice"><strong>🎯 Latihan praktik</strong><p>{{ $lesson['task'] }}</p></div>
<details style="margin-bottom:22px"><summary>Petunjuk refleksi: {{ $lesson['question'] }}</summary><p>{{ $lesson['answer'] }}</p></details>
@if(!$done)<form method="POST" action="{{ route('courses.complete',[$starterPack,$lesson['number']]) }}">@csrf<button class="btn btn--primary" type="submit">✓ Selesaikan tahap & lanjutkan</button></form>@else<p style="color:#426e4c">✓ Tahap sudah selesai</p>@endif
@else<p>Selesaikan langkah sebelumnya untuk membuka tahap ini.</p>@endif
</section>@endforeach
@if(count($completed)===count($lessons))<section class="hn-course-panel"><h2>🏅 Course selesai — lencana berhasil diraih!</h2><p>Lencanamu otomatis tercatat di Profil. Sekarang abadikan hasil latihanmu melalui formulir karya.</p><a class="btn btn--primary" href="{{ route('courses.submission', $starterPack) }}">📷 Upload hasil karya</a> <a class="btn btn--ghost" href="{{ route('profile') }}">Lihat profil</a></section>@endif
</main></div></div>
@endsection
