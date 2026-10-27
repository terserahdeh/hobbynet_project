@extends('layouts.app')

@section('title', $step->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">

@php
    $stepUrl = fn ($s) => $s->isShare()
        ? route('roadmap.submission.show', $hobby->slug)
        : route('roadmap.steps.show', [$hobby->slug, $s->slug]);
    $firstName = \Illuminate\Support\Str::before(auth()->user()->name, ' ');
    $items = $step->checklistItems;
    $allChecked = $items->every(fn ($i) => in_array($i->id, $checkedIds));
    $hero = $step->instructions->first(fn ($i) => filled($i->image));
@endphp

<div class="rm-page">
    <header class="rm-topbar">
        <div class="rm-topbar__left">
            <a href="{{ route('roadmap.show', $hobby->slug) }}" class="rm-topbar__close" aria-label="Back to your journey">@include('roadmap.partials.icon', ['name' => 'close'])</a>
            <div class="rm-topbar__title">
                <strong>{{ $project->title }}</strong>
                <span>Guided experience</span>
            </div>
        </div>

        <div class="rm-topbar__mid">
            <div class="rm-topbar__labels">
                <span>Step {{ $step->position }} of {{ $summary['total'] }}</span>
                <span>{{ $summary['percent'] }}% roadmap progress</span>
            </div>
            <div class="rm-bar" role="progressbar" aria-valuenow="{{ $summary['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Roadmap progress">
                <span class="rm-bar__fill" style="width: {{ $summary['percent'] }}%"></span>
            </div>
        </div>

        <div class="rm-topbar__right"><a href="{{ route('roadmap.show', $hobby->slug) }}">Save &amp; Exit</a></div>
    </header>

    <div class="rm-body">
    <div class="rm-container rm-container--wide">
        @include('roadmap.partials.flash')

        <div class="rm-layout">
            <aside>
                <ol class="rm-steplist">
                    @foreach ($steps as $s)
                        <li>
                            @if ($s->state === 'locked')
                                <span class="is-locked">
                                    <span class="rm-step-dot">{{ $s->position }}</span>
                                    <span>{{ $s->position }}. {{ $s->title }}</span>
                                </span>
                            @else
                                <a href="{{ $stepUrl($s) }}"
                                   class="{{ $s->id === $step->id ? 'is-current' : ($s->state === 'completed' ? 'is-done' : '') }}"
                                   @if ($s->id === $step->id) aria-current="step" @endif>
                                    <span class="rm-step-dot">
                                        @if ($s->state === 'completed' && $s->id !== $step->id) @include('roadmap.partials.icon', ['name' => 'check']) @else {{ $s->position }} @endif
                                    </span>
                                    <span>{{ $s->position }}. {{ $s->title }}</span>
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ol>

                @if ($step->encouragement)
                    <div class="rm-hint">
                        <strong>@include('roadmap.partials.icon', ['name' => 'bulb']) You're doing great, {{ $firstName }}!</strong>
                        <p>{{ $step->encouragement }}</p>
                        <small>Keep it up!</small>
                    </div>
                @endif
            </aside>

            <div>
                <main class="rm-card rm-step">
                    <p class="rm-eyebrow">Instructional step</p>
                    <h1>{{ $step->title }}</h1>
                    <p class="rm-step__meta">
                        <span>@include('roadmap.partials.icon', ['name' => 'clock']) Estimated time: <b>{{ $step->estimated_minutes }} minutes</b></span>
                        @if ($step->objective)
                            <i class="rm-step__sep" aria-hidden="true"></i>
                            <span>@include('roadmap.partials.icon', ['name' => 'target']) Objective: <b>{{ $step->objective }}</b></span>
                        @endif
                    </p>

                    @if ($step->materials->isNotEmpty())
                        <ul class="rm-chips">
                            @foreach ($step->materials as $m)
                                <li class="rm-chip {{ $m->kind === 'material' ? 'rm-chip--material' : '' }}">
                                    <span class="rm-chip__icon">@include('roadmap.partials.icon', ['name' => $m->kind === 'tool' ? 'bulb' : 'plus'])</span>
                                    <span>
                                        <small>{{ $m->kind === 'tool' ? 'Tool' : 'Material' }}{{ $m->is_optional ? ' (optional)' : '' }}</small>
                                        <strong>{{ $m->name }}@if ($m->spec) ({{ $m->spec }})@endif</strong>
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="rm-split {{ $hero ? '' : 'rm-split--single' }}">
                        <ol class="rm-instr">
                            @foreach ($step->instructions as $ins)
                                <li>
                                    <span class="rm-instr__num">{{ $ins->position }}</span>
                                    <p>{{ $ins->body }}</p>
                                </li>
                            @endforeach
                        </ol>

                        @if ($hero)
                            <figure class="rm-visual">
                                <img src="{{ asset($hero->image) }}" alt="{{ $hero->image_caption ?? $step->title }}">
                                @if ($hero->image_caption) <figcaption>{{ $hero->image_caption }}</figcaption> @endif
                            </figure>
                        @endif
                    </div>

                    @if ($items->isNotEmpty())
                        <section class="rm-checklist" aria-labelledby="rm-checklist-title">
                            <h2 id="rm-checklist-title">Action Checklist</h2>
                            <ul id="rm-checklist">
                                @foreach ($items as $item)
                                    <li>
                                        <label class="rm-check">
                                            <input type="checkbox"
                                                   data-url="{{ route('roadmap.steps.checklist', [$hobby->slug, $step->slug, $item->id]) }}"
                                                   @checked(in_array($item->id, $checkedIds))>
                                            <span>{{ $item->label }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                            @error('checklist') <p class="rm-error">{{ $message }}</p> @enderror
                        </section>
                    @endif
                </main>

                <div class="rm-foot">
                    @if ($previous)
                        <a href="{{ $stepUrl($previous) }}" class="rm-link">@include('roadmap.partials.icon', ['name' => 'chev-left']) Previous Step</a>
                    @else
                        <a href="{{ route('roadmap.show', $hobby->slug) }}" class="rm-link">@include('roadmap.partials.icon', ['name' => 'chev-left']) Back to Roadmap</a>
                    @endif

                    <form method="POST" action="{{ route('roadmap.steps.complete', [$hobby->slug, $step->slug]) }}" class="rm-foot__right">
                        @csrf
                        <button type="submit" id="rm-complete" class="rm-btn rm-btn--primary"
                                @disabled(! $allChecked && $step->state !== 'completed')>
                            {{ $step->state === 'completed' ? 'Next Step' : 'Mark as Complete' }} @include('roadmap.partials.icon', ['name' => 'chev-right'])
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<script>
    (function () {
        var boxes = document.querySelectorAll('#rm-checklist input[type=checkbox]');
        var btn = document.getElementById('rm-complete');
        var alreadyDone = {{ $step->state === 'completed' ? 'true' : 'false' }};
        var meta = document.querySelector('meta[name="csrf-token"]');
        var token = meta ? meta.content : '{{ csrf_token() }}';

        function refresh() {
            if (btn && !alreadyDone) {
                btn.disabled = ![].slice.call(boxes).every(function (b) { return b.checked; });
            }
        }

        boxes.forEach(function (box) {
            box.addEventListener('change', function () {
                var wanted = box.checked;
                fetch(box.dataset.url, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                }).then(function (res) {
                    if (!res.ok) throw new Error();
                    return res.json();
                }).then(function (data) {
                    box.checked = data.checked;
                }).catch(function () {
                    box.checked = !wanted; // roll back if the save failed
                }).then(refresh);
            });
        });
    })();
</script>
@endsection
