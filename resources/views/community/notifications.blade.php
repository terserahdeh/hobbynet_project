@extends('layouts.app')
@section('title','Notifikasi · HobbyNet')
@section('content')
<div class="cm-wrap"><div class="cm-heading"><h1>🔔 Notifikasi</h1><p>Aktivitas terbaru di komunitasmu</p></div>
<form method="POST" action="{{ route('community.notifications.read') }}">@csrf<button class="cm-read">Tandai semua sudah dibaca</button></form>
@forelse($notifications as $notification)
<a class="cm-notification {{ $notification->read_at ? '' : 'cm-unread' }}" href="{{ route('community.show',$notification->project_submission_id) }}"><strong>{{ $notification->actor_name }}</strong> {{ $notification->message }}<small>{{ \Illuminate\Support\Carbon::parse($notification->created_at)->diffForHumans() }}</small></a>
@empty <div class="cm-post cm-empty"><div style="font-size:30px;margin-bottom:10px">🔔✨</div><strong>Selamat datang di notifikasi HobbyNet!</strong><p style="margin:10px auto;max-width:460px">Di sinilah kamu akan tahu saat seseorang menyukai, mengomentari, membalas komentar, atau menyimpan karyamu di Komunitas. Yuk, bagikan karya pertamamu dan mulai terhubung dengan kreator lain!</p><a href="{{ route('community.index') }}" class="cm-filter-link" style="display:inline-block;margin-top:8px">Jelajahi Komunitas</a></div> @endforelse
{{ $notifications->links() }}</div>
@endsection
