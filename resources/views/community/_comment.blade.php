<div class="cm-comment" data-comment-id="{{ $comment->id }}" style="margin-left:{{ min($depth,5)*14 }}px"><strong>{{ $comment->user->name }}</strong> <small>{{ $comment->created_at->diffForHumans() }}</small><p>{{ $comment->body }}</p>
<details><summary>↳ Balas komentar</summary><form data-community-action="comment" method="POST" action="{{ route('community.comment',$post) }}">@csrf<input type="hidden" name="parent_id" value="{{ $comment->id }}"><textarea name="body" required maxlength="1000" placeholder="Tulis balasan..."></textarea><button type="submit">Kirim balasan</button></form></details>
<div class="cm-replies">@foreach($comment->replies as $reply) @include('community._comment',['comment'=>$reply,'depth'=>$depth+1]) @endforeach</div>
</div>
