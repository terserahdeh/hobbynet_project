@extends('layouts.app')
@section('title','Komunitas · HobbyNet')
@section('content')
<div class="cm-wrap"><div class="cm-heading"><p>HOBBYNET COMMUNITY</p><h1>Inspirasi dari sesama kreator ✨</h1><span>Temukan karya, berbagi apresiasi, dan saling menyemangati.</span></div>
@forelse($posts as $post)
 @php $likedPost=in_array($post->id,$liked);$savedPost=in_array($post->id,$saved); @endphp
 @include('community._post')
@empty <div class="cm-post cm-empty">Belum ada karya publik. Selesaikan roadmap dan aktifkan “Bagikan ke Komunitas” saat mengunggah foto.</div>
@endforelse
<div class="cm-pagination">{{ $posts->links() }}</div></div>
@endsection
