@extends('layouts.app')

@section('title', $step->title)

@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">
<link rel="stylesheet" href="{{ asset('css/guided.css') }}">
<link rel="stylesheet" href="{{ asset('css/plus-lock.css') }}">

@php
    $stepUrl = fn ($s) => route('projects.steps.show', [$project->slug, $s->slug]);
    $firstName = \Illuminate\Support\Str::before(auth()->user()->name, ' ');
    $items = $step->checklistItems;
    $allChecked = $items->every(fn ($i) => in_array($i->id, $checkedIds));
    $hero = $step->instructions->first(fn ($i) => filled($i->image));
    $locked = $locked ?? false;
    $video = $locked ? null : \App\Support\YouTube::id($step->video_url);
    $hasVisual = $video || $hero;
    $instructions = $locked ? $step->instructions->take(1) : $step->instructions;
    $phase = ['materials' => 'Preparation phase', 'lesson' => 'Tutorial phase', 'practice' => 'Practice phase', 'project' => 'Project phase'][$step->type] ?? 'Guided step';
    $isLast = $next === null;
    $goalPercent = min(100, (int) round($goal['minutes'] / $goal['goal'] * 100));
@endphp

<div class="rm-page">
    <header class="rm-topbar">
        <div class="rm-topbar__left">
            <a href="{{ route('projects.show', $project->slug) }}" class="rm-topbar__close" aria-label="Back to project">@include('roadmap.partials.icon', ['name' => 'close'])</a>
            <div class="rm-topbar__title">
                <strong>{{ $project->title }}</strong>
                <span>Pengalaman Terpadu</span>
            </div>
        </div>

        <div class="rm-topbar__mid">
            <div class="rm-topbar__labels">
                <span>Step {{ $step->position }} dari {{ $summary['total'] }}</span>
                <span>{{ $summary['percent'] }}% progres proyek</span>
            </div>
            <div class="rm-bar" role="progressbar" aria-valuenow="{{ $summary['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Project progress">
                <span class="rm-bar__fill" style="width: {{ $summary['percent'] }}%"></span>
            </div>
        </div>

        <div class="rm-topbar__right"><a href="{{ route('projects.show', $project->slug) }}">Simpan &amp; Keluar</a></div>
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
                        <strong>@include('roadmap.partials.icon', ['name' => 'bulb']) Kamu hebat, {{ $firstName }}!</strong>
                        <p>{{ $step->encouragement }}</p>
                        <small>Terus semangat!</small>
                    </div>
                @endif

                <div class="gp-session">
                    <p><span class="gp-session__dot" aria-hidden="true"></span> Session Goal <b>{{ $goal['minutes'] }}/{{ $goal['goal'] }} min</b></p>
                    <div class="gp-bar gp-bar--light" role="progressbar" aria-valuenow="{{ $goalPercent }}" aria-valuemin="0" aria-valuemax="100" aria-label="Daily goal">
                        <span style="width: {{ $goalPercent }}%"></span>
                    </div>
                    <small>Selesaikan langkah ini untuk semakin mendekati target harian Anda.</small>
                </div>
            </aside>

            <div>
                <main class="rm-card rm-step">
                    <p class="rm-eyebrow">{{ $phase }}</p>
                    <h1>{{ $step->title }}</h1>
                    <p class="rm-step__meta">
                        <span>@include('roadmap.partials.icon', ['name' => 'clock']) Estimasi waktu: <b>{{ $step->estimated_minutes }} menit</b></span>
                        @if ($step->objective)
                            <i class="rm-step__sep" aria-hidden="true"></i>
                            <span>@include('roadmap.partials.icon', ['name' => 'target']) Tujuan: <b>{{ $step->objective }}</b></span>
                        @endif
                    </p>

                    @if ($step->materials->isNotEmpty())
                        @php
                            $groups = [
                                'Alat'  => $step->materials->where('kind', 'tool'),
                                'Bahan' => $step->materials->where('kind', '!=', 'tool'),
                            ];
                        @endphp

                        @foreach ($groups as $label => $group)
                            @if ($group->isNotEmpty())
                                <h2 class="rm-chips__title">{{ $label }}</h2>
                                <ul class="rm-chips" style="margin-top:8px !important">
                                    @foreach ($group as $m)
                                        <li class="rm-chip {{ $m->kind === 'material' ? 'rm-chip--material' : '' }}">
                                            <span class="rm-chip__icon">@include('roadmap.partials.icon', ['name' => $m->kind === 'tool' ? 'bulb' : 'plus'])</span>
                                            <span>
                                                <small>{{ $m->is_optional ? ' (opsional)' : '' }}</small>
                                                <strong>{{ $m->name }}@if ($m->spec) ({{ $m->spec }})@endif</strong>
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        @endforeach
                    @endif

                    <div class="rm-split {{ $hasVisual ? '' : 'rm-split--single' }}">
                        <ol class="rm-instr">
                            @foreach ($instructions as $ins)
                                <li>
                                    <span class="rm-instr__num">{{ $ins->position }}</span>
                                    <p>{{ $ins->body }}</p>
                                </li>
                            @endforeach
                        </ol>

                        @if ($video)
                            @include('partials.youtube', ['url' => $video, 'title' => $step->title . ' video'])
                        @elseif ($hero)
                            <figure class="rm-visual">
                                <img src="{{ asset($hero->image) }}" alt="{{ $hero->image_caption ?? $step->title }}">
                                @if ($hero->image_caption) <figcaption>{{ $hero->image_caption }}</figcaption> @endif
                            </figure>
                        @endif
                    </div>

                    @if ($locked)
                        <aside class="gp-lockcard" aria-label="HobbyNet Plus">
                            <span class="gp-lockcard__icon" aria-hidden="true">&#9733;</span>
                            <div>
                                <strong>Anda sedang melihat pratinjau</strong>
                                <p>Sisa langkah ini{{ $step->instructions->count() > 1 ? ' (' . ($step->instructions->count() - 1) . ' instruksi lagi)' : '' }}, video, Tips Pro, dan checklist adalah bagian dari HobbyNet Plus.</p>
                                <a class="gp-btn gp-btn--plus gp-btn--sm" href="{{ route('plus') }}">&#9733; Buka dengan Plus</a>
                            </div>
                        </aside>
                    @else
                        @if ($step->tip)
                            <aside class="gp-tip" aria-label="Pro tip">
                                <span class="gp-tip__icon">@include('roadmap.partials.icon', ['name' => 'bulb'])</span>
                                <div><strong>Tips</strong><p>{{ $step->tip }}</p></div>
                            </aside>
                        @endif

                        @if ($items->isNotEmpty())
                            <section class="rm-checklist" aria-labelledby="rm-checklist-title">
                                <h2 id="rm-checklist-title">Checklist Aksi</h2>
                                <ul id="rm-checklist">
                                    @foreach ($items as $item)
                                        <li>
                                            <label class="rm-check">
                                                <input type="checkbox"
                                                       data-url="{{ route('projects.steps.checklist', [$project->slug, $step->slug, $item->id]) }}"
                                                       @checked(in_array($item->id, $checkedIds))>
                                                <span>{{ $item->label }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                                @error('checklist') <p class="rm-error">{{ $message }}</p> @enderror
                            </section>
                        @endif
                    @endif
                </main>

                <div class="rm-foot">
                    @if ($previous)
                        <a href="{{ $stepUrl($previous) }}" class="rm-link">@include('roadmap.partials.icon', ['name' => 'chev-left']) Previous Step</a>
                    @else
                        <a href="{{ route('projects.show', $project->slug) }}" class="rm-link">@include('roadmap.partials.icon', ['name' => 'chev-left']) Back to Project</a>
                    @endif

                    <details class="gp-help">
                        <summary>Need More Help?</summary>
                        <p>Re-read the instructions slowly, take a short break, then try the Pro Tip. You can always come back to this step. One small step at a time.</p>
                    </details>

                    @if ($locked)
                        <a class="rm-btn rm-btn--primary rm-foot__right" href="{{ route('plus') }}">&#9733; Unlock with Plus</a>
                    @else
                        <form method="POST" action="{{ route('projects.steps.complete', [$project->slug, $step->slug]) }}" class="rm-foot__right">
                            @csrf
                            <button type="submit" id="rm-complete" class="rm-btn rm-btn--primary"
                                    @disabled(! $allChecked && $step->state !== 'completed')>
                                @if ($step->state === 'completed')
                                    {{ $isLast ? 'Back to Project' : 'Next Step' }}
                                @else
                                    {{ $isLast ? 'Finish Project' : 'Mark as Complete' }}
                                @endif
                                @include('roadmap.partials.icon', ['name' => 'chev-right'])
                            </button>
                        </form>
                    @endif
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
                    box.checked = !wanted;
                }).then(refresh);
            });
        });
    })();
</script>
@endsection
