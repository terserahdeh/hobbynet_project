@extends('layouts.app')
@section('title','Notifikasi · HobbyNet')
@section('content')
<div class="cm-wrap"><div class="cm-heading"><h1>🔔 Notifikasi</h1><p>Aktivitas terbaru di komunitasmu</p></div>
<form method="POST" action="{{ route('community.notifications.read') }}">@csrf<button class="cm-read">Tandai semua sudah dibaca</button></form>
@forelse($notifications as $notification)
<a class="cm-notification {{ $notification->read_at ? '' : 'cm-unread' }}" href="{{ route('community.show',$notification->project_submission_id) }}"><strong>{{ $notification->actor_name }}</strong> {{ $notification->message }}<small>{{ \Illuminate\Support\Carbon::parse($notification->created_at)->diffForHumans() }}</small></a>
@empty <div class="cm-post cm-empty">Belum ada notifikasi.</div> @endforelse
{{ $notifications->links() }}</div>
@endsection
