@extends('layouts.app')
@section('title','Komunitas · HobbyNet')
@section('content')
<div class="cm-wrap"><div class="cm-heading"><p>HOBBYNET COMMUNITY</p><h1>Inspirasi dari sesama kreator ✨</h1><span>Temukan karya, berbagi apresiasi, dan saling menyemangati.</span></div>
@if(session('status'))<p class="cm-status" role="status">{{ session('status') }}</p>@endif
<nav class="cm-filters" aria-label="Filter komunitas berdasarkan hobi">
 @foreach ([''=>'✨ Semua Karya','crochet'=>'🧶 Crochet','painting'=>'🎨 Melukis','photography'=>'📷 Fotografi'] as $slug=>$label)
  <a class="cm-filter-link {{ request('hobby','') === $slug ? 'cm-filter-active' : '' }}" href="{{ route('community.index', $slug !== '' ? ['hobby'=>$slug] : []) }}" {{ request('hobby','') === $slug ? 'aria-current=page' : '' }}>{{ $label }}</a>
 @endforeach
</nav>
@forelse($posts as $post)
 @php $likedPost=in_array($post->id,$liked);$savedPost=in_array($post->id,$saved); @endphp
 @include('community._post')
@empty <div class="cm-post cm-empty">Belum ada karya di kategori ini. Coba pilih kategori lain atau jadilah yang pertama membagikan karyamu!</div>
@endforelse
<div class="cm-pagination">{{ $posts->links() }}</div></div>
@endsection
