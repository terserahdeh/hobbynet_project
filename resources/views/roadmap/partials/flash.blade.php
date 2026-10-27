@if (session('status'))
    <div role="status" class="rm-flash">{{ session('status') }}</div>
@endif

@if (session('badges'))
    <div role="status" class="rm-toast">
        <span class="rm-toast__icon">@include('roadmap.partials.icon', ['name' => 'star'])</span>
        <span class="rm-toast__text">
            <span class="rm-toast__eyebrow">Achievement unlocked</span>
            <strong>{{ implode(', ', session('badges')) }}</strong>
            <span class="rm-toast__done"><span class="rm-toast__bar"></span>Done</span>
        </span>
    </div>
@endif
