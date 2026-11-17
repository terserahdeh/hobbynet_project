
<article class="cm-post" data-community-post>

    <div class="cm-user">
        <span class="cm-avatar">
            {{ mb_strtoupper(mb_substr($post->user->name, 0, 1)) }}
        </span>

        <div>
            <strong>{{ $post->user->name }}</strong>
            <small>
                {{ $post->submitted_at?->diffForHumans() }}
                · {{ $post->category }}
            </small>
        </div>

        @if(auth()->id() === $post->user_id)
            <details class="cm-owner-menu">
                <summary aria-label="Opsi postingan" title="Opsi postingan">
                    ⋯
                </summary>

                <button
                    type="button"
                    class="cm-delete-button"
                    onclick="document.getElementById('delete-modal-{{ $post->id }}').showModal(); this.closest('details').open = false;"
                >
                    Hapus Postingan
                </button>
            </details>
        @endif
    </div>

    <a class="cm-photo-link"
       href="{{ route('community.show', $post) }}">
        <img
            class="cm-photo"
            src="{{ $post->photos->first()?->url }}"
            alt="Karya {{ $post->title }}"
            loading="lazy"
        >
    </a>

    <div class="cm-body">
        <h2>{{ $post->title }}</h2>

        <p>{{ $post->reflection }}</p>

        <div class="cm-actions">

            <form data-community-action="like"
                  method="POST"
                  action="{{ route('community.like', $post) }}">
                @csrf

                <button
                    type="submit"
                    class="cm-action {{ $likedPost ? 'cm-on' : '' }}"
                    aria-pressed="{{ $likedPost ? 'true' : 'false' }}"
                >
                    <span class="cm-action-icon">♥</span>
                    <span>
                        <span data-like-count>
                            {{ $post->community_likes_count }}
                        </span>
                        Suka
                    </span>
                </button>
            </form>

            <a class="cm-action"
               href="{{ route('community.show', $post) }}#komentar">
                <span class="cm-action-icon">♧</span>
                <span>
                    <span data-comment-count>
                        {{ $post->community_comments_count }}
                    </span>
                    Komentar
                </span>
            </a>

            <form data-community-action="save"
                  method="POST"
                  action="{{ route('community.save', $post) }}">
                @csrf

                <button
                    type="submit"
                    class="cm-action {{ $savedPost ? 'cm-on' : '' }}"
                    aria-pressed="{{ $savedPost ? 'true' : 'false' }}"
                >
                    <span class="cm-action-icon">▱</span>
                    <span data-save-label>
                        {{ $savedPost ? 'Tersimpan' : 'Simpan' }}
                    </span>
                </button>
            </form>

        </div>
    </div>
</article>

{{-- MODAL KONFIRMASI HAPUS --}}
@if(auth()->id() === $post->user_id)
<dialog
    id="delete-modal-{{ $post->id }}"
    class="hobbynet-delete-modal"
    aria-labelledby="delete-title-{{ $post->id }}"
>
    <div class="delete-modal-content">

        <div class="delete-modal-icon">
            🗑️
        </div>

        <h2 id="delete-title-{{ $post->id }}">
            Hapus postingan ini?
        </h2>

        <p>
            Yakin ingin menghapus karya ini dari Komunitas?
            Postingan, suka, komentar, dan simpanan terkait
            akan dihapus secara permanen.
        </p>

        <p class="delete-modal-note">
            Tenang, hasil proyek pribadimu tetap tersimpan.
            Tindakan ini tidak dapat dibatalkan.
        </p>

        <div class="delete-modal-actions">

            <button
                type="button"
                class="delete-cancel-btn"
                onclick="document.getElementById('delete-modal-{{ $post->id }}').close()"
            >
                Batal
            </button>

            <form
                method="POST"
                action="{{ route('community.destroy', $post) }}"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="delete-confirm-btn"
                >
                    Ya, Hapus
                </button>
            </form>

        </div>
    </div>
</dialog>
@endif
