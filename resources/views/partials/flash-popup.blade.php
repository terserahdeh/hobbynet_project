@if (session('popup'))
    @php($popup = session('popup'))

    <dialog id="flash-popup" class="flash-popup" aria-labelledby="flash-popup-title">
        <h2 id="flash-popup-title">{{ $popup['title'] ?? 'Berhasil' }}</h2>
        <p>{{ $popup['message'] ?? '' }}</p>
        <form method="dialog">
            <button type="submit">Oke</button>
        </form>
    </dialog>

    <style>
        .flash-popup {
            border: 0; border-radius: 16px; padding: 24px 28px;
            max-width: 380px; width: calc(100% - 32px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, .25);
            text-align: center;
        }
        .flash-popup::backdrop { background: rgba(0, 0, 0, .45); }
        .flash-popup h2 { margin: 0 0 8px; font-size: 1.15rem; }
        .flash-popup p { margin: 0 0 20px; color: #555; line-height: 1.5; }
        .flash-popup button {
            border: 0; border-radius: 999px; padding: 10px 28px;
            background: #111; color: #fff; font-weight: 600; cursor: pointer;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const dialog = document.getElementById('flash-popup');
            if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
        });
    </script>
@endif