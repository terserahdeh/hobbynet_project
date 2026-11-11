@extends('layouts.app')
@section('title',($post->title ?? 'Postingan').' · HobbyNet')
@section('content')
<div class="cm-wrap"><a class="cm-back" href="{{ route('community.index') }}">← Kembali ke Komunitas</a>
@if(session('status'))<p>{{ session('status') }}</p>@endif
@php $likedPost=$liked; $savedPost=$saved; @endphp
@include('community._post')
<section class="cm-post cm-comments" id="komentar"><h2>Komentar (<span data-detail-comment-count>{{ $post->community_comments_count }}</span>)</h2>
<form data-community-action="comment" method="POST" action="{{ route('community.comment',$post) }}">@csrf<textarea name="body" required maxlength="1000" placeholder="Tulis komentar yang positif..."></textarea>@error('body')<p>{{ $message }}</p>@enderror<button type="submit">Kirim komentar</button></form>
<div data-comments-list>@forelse($comments as $comment) @include('community._comment',['depth'=>0]) @empty <p data-empty-comments>Belum ada komentar. Jadi yang pertama!</p> @endforelse</div>
</section></div>
@endsection
