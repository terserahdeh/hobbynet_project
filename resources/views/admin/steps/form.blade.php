@extends('admin.layouts.admin')

@php
    $isNew = ! $step->exists;

    $materialRows = old('materials', $step->materials->map(fn ($m) => [
        'id' => $m->id, 'kind' => $m->kind, 'name' => $m->name, 'spec' => $m->spec, 'is_optional' => $m->is_optional,
    ])->all()) ?: [['kind' => 'tool']];

    $instructionRows = old('instructions', $step->instructions->map(fn ($i) => [
        'id' => $i->id, 'body' => $i->body,
    ])->all()) ?: [[]];

    $checklistRows = old('checklist', $step->checklistItems->map(fn ($c) => [
        'id' => $c->id, 'label' => $c->label,
    ])->all()) ?: [[]];

    $types = \App\Http\Requests\Admin\RoadmapStepRequest::TYPES;
    $currentType = old('type', $step->type ?: 'lesson');
@endphp

@section('title', $isNew ? 'Add step' : 'Edit ' . $step->title)

@section('content')
<nav class="ad-crumbs">
    <a href="{{ route('admin.projects.index') }}">Projects &amp; roadmaps</a> /
    <a href="{{ route('admin.projects.steps.index', $project) }}">{{ $project->title }}</a> /
    {{ $isNew ? 'Add step' : $step->title }}
</nav>

<div class="ad-head">
    <div>
        <h1>{{ $isNew ? 'Add a step' : 'Edit step' }}</h1>
        <p>Keep it small and encouraging. One clear thing to do at a time.</p>
    </div>
</div>

@if ($learners && $project->roadmap_published && $learners['started'] > 0)
    <div class="ad-flash ad-flash--warn">
        {{ $learners['started'] }} {{ \Illuminate\Support\Str::plural('learner', $learners['started']) }} {{ $learners['started'] === 1 ? 'has' : 'have' }} started this roadmap. Their progress and ticked checklist items are kept when you edit.
    </div>
@endif

<form id="step-form" method="POST" enctype="multipart/form-data"
      action="{{ $isNew ? route('admin.projects.steps.store', $project) : route('admin.steps.update', $step) }}">
    @csrf
    @unless ($isNew) @method('PUT') @endunless

    {{-- Basics --}}
    <section class="ad-card ad-card__pad ad-form">
        <h2>Basics</h2>

        <div class="ad-field">
            <label class="ad-label" for="title">Title</label>
            <input id="title" name="title" class="ad-input @error('title') has-error @enderror" value="{{ old('title', $step->title) }}" maxlength="255" placeholder="Build Your Basic Skills" required>
            @error('title') <p class="ad-error">{{ $message }}</p> @enderror
        </div>

        <div class="ad-field">
            <label class="ad-label" for="summary">Short description</label>
            <input id="summary" name="summary" class="ad-input @error('summary') has-error @enderror" value="{{ old('summary', $step->summary) }}" maxlength="255" placeholder="Master the “single crochet” stitch and complete your first row." required>
            <p class="ad-help">Shown on the roadmap overview. On the active step it reads “Today's goal: …”.</p>
            @error('summary') <p class="ad-error">{{ $message }}</p> @enderror
        </div>

        <div class="ad-grid3">
            <div class="ad-field">
                <label class="ad-label" for="type">Type</label>
                <select id="type" name="type" class="ad-input @error('type') has-error @enderror">
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" @selected($currentType === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type') <p class="ad-error">{{ $message }}</p> @enderror
            </div>
            <div class="ad-field">
                <label class="ad-label" for="estimated_minutes">Time (minutes)</label>
                <input id="estimated_minutes" name="estimated_minutes" type="number" min="1" max="600" class="ad-input @error('estimated_minutes') has-error @enderror" value="{{ old('estimated_minutes', $step->estimated_minutes ?? 15) }}" required>
                @error('estimated_minutes') <p class="ad-error">{{ $message }}</p> @enderror
            </div>
            <div class="ad-field">
                <label class="ad-label" for="badge_id">Badge on completion</label>
                <select id="badge_id" name="badge_id" class="ad-input">
                    <option value="">No badge</option>
                    @foreach ($badges as $badge)
                        <option value="{{ $badge->id }}" @selected((string) old('badge_id', $step->badge_id) === (string) $badge->id)>{{ $badge->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="ad-field" data-lesson-only>
            <label class="ad-label" for="objective">Objective</label>
            <input id="objective" name="objective" class="ad-input @error('objective') has-error @enderror" value="{{ old('objective', $step->objective) }}" maxlength="255" placeholder="Learn single crochet">
            <p class="ad-help">Shown under the title as “Objective: …”.</p>
            @error('objective') <p class="ad-error">{{ $message }}</p> @enderror
        </div>

        <div class="ad-field">
            <label class="ad-label" for="encouragement">Encouragement note</label>
            <textarea id="encouragement" name="encouragement" class="ad-input @error('encouragement') has-error @enderror" maxlength="1000" placeholder="Almost halfway there! The single crochet stitch is the heart of most projects.">{{ old('encouragement', $step->encouragement) }}</textarea>
            <p class="ad-help" data-lesson-only>Appears in the pink card beside the step.</p>
            <p class="ad-help" data-share-only>Appears as the quote under the upload form.</p>
            @error('encouragement') <p class="ad-error">{{ $message }}</p> @enderror
        </div>

        <div class="ad-flash ad-flash--ok" data-share-only style="margin: 0;">
            This is the final step. Learners see the “Look What You Made!” page, where they add photos and submit. It needs no instructions.
        </div>
    </section>

    {{-- Picture --}}
    <section class="ad-card ad-card__pad ad-form" data-lesson-only>
        <div>
            <h2>Demonstration picture</h2>
            <p class="ad-card__sub">Shown beside the instructions so learners can see what to aim for.</p>
        </div>

        <div class="ad-upload">
            <div class="ad-thumb" id="image-preview">
                @if ($step->image_url)
                    <img src="{{ $step->image_url }}" alt="Current picture">
                @else
                    @include('roadmap.partials.icon', ['name' => 'image'])
                @endif
            </div>

            <div class="ad-form" style="gap: 14px;">
                <div class="ad-field">
                    <label class="ad-label" for="image">{{ $step->image ? 'Replace picture' : 'Upload picture' }}</label>
                    <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" class="ad-input @error('image') has-error @enderror">
                    <p class="ad-help">JPG, PNG or WebP, up to 5 MB. A square-ish photo with the subject in the middle works best.</p>
                    @error('image') <p class="ad-error">{{ $message }}</p> @enderror
                </div>

                <div class="ad-field">
                    <label class="ad-label" for="image_caption">Caption</label>
                    <input id="image_caption" name="image_caption" class="ad-input" value="{{ old('image_caption', $step->image_caption) }}" maxlength="120" placeholder="Visual Guide: Single Crochet Stitch">
                    <p class="ad-help">Leave blank to use “Visual Guide: {step title}”.</p>
                </div>

                @if ($step->image)
                    <label class="ad-opt"><input type="checkbox" name="remove_image" value="1"> Remove the current picture</label>
                @endif

                @if (! $step->image)
                    <p class="ad-help">With no picture, learners see the built-in illustration for this step.</p>
                @endif
            </div>
        </div>
    </section>

    {{-- Materials --}}
    <section class="ad-card ad-card__pad ad-form" data-lesson-only data-repeater data-next="{{ count($materialRows) + 100 }}">
        <div class="ad-section-title">
            <div>
                <h3>Tools &amp; materials</h3>
                <p class="ad-help">What the learner needs for this step. Optional.</p>
            </div>
            <button type="button" class="ad-btn ad-btn--ghost ad-btn--sm" data-add>@include('roadmap.partials.icon', ['name' => 'plus']) Add</button>
        </div>
        <div class="ad-rows" data-rows>
            @foreach ($materialRows as $i => $row)
                @include('admin.steps.row', ['type' => 'material', 'i' => $i, 'row' => $row])
            @endforeach
        </div>
        <template>@include('admin.steps.row', ['type' => 'material', 'i' => '__I__', 'row' => []])</template>
    </section>

    {{-- Instructions --}}
    <section class="ad-card ad-card__pad ad-form" data-lesson-only data-repeater data-next="{{ count($instructionRows) + 100 }}">
        <div class="ad-section-title">
            <div>
                <h3>Instructions</h3>
                <p class="ad-help">Numbered steps, in order. One action each.</p>
            </div>
            <button type="button" class="ad-btn ad-btn--ghost ad-btn--sm" data-add>@include('roadmap.partials.icon', ['name' => 'plus']) Add</button>
        </div>
        @error('instructions') <p class="ad-error">{{ $message }}</p> @enderror
        <div class="ad-rows" data-rows>
            @foreach ($instructionRows as $i => $row)
                @include('admin.steps.row', ['type' => 'instruction', 'i' => $i, 'row' => $row])
            @endforeach
        </div>
        <template>@include('admin.steps.row', ['type' => 'instruction', 'i' => '__I__', 'row' => []])</template>
    </section>

    {{-- Checklist --}}
    <section class="ad-card ad-card__pad ad-form" data-lesson-only data-repeater data-next="{{ count($checklistRows) + 100 }}">
        <div class="ad-section-title">
            <div>
                <h3>Action checklist</h3>
                <p class="ad-help">Learners tick every item before “Mark as Complete” unlocks. Leave empty to let them continue straight away.</p>
            </div>
            <button type="button" class="ad-btn ad-btn--ghost ad-btn--sm" data-add>@include('roadmap.partials.icon', ['name' => 'plus']) Add</button>
        </div>
        <div class="ad-rows" data-rows>
            @foreach ($checklistRows as $i => $row)
                @include('admin.steps.row', ['type' => 'check', 'i' => $i, 'row' => $row])
            @endforeach
        </div>
        <template>@include('admin.steps.row', ['type' => 'check', 'i' => '__I__', 'row' => []])</template>
    </section>

    <div class="ad-card ad-card__pad">
        <div class="ad-actions" style="padding-top: 0;">
            <button type="submit" class="ad-btn ad-btn--primary">{{ $isNew ? 'Add step' : 'Save step' }}</button>
            <a href="{{ route('admin.projects.steps.index', $project) }}" class="ad-btn ad-btn--ghost">Cancel</a>
        </div>
    </div>
</form>

@unless ($isNew)
    <div class="ad-danger">
        <p><strong>Delete this step.</strong> Learners who finished it will lose that progress, and their percentage is recalculated.</p>
        <form method="POST" action="{{ route('admin.steps.destroy', $step) }}"
              onsubmit="return confirm('Delete {{ addslashes($step->title) }}? This cannot be undone.')">
            @csrf @method('DELETE')
            <button type="submit" class="ad-btn ad-btn--danger">Delete step</button>
        </form>
    </div>
@endunless
@endsection

@push('scripts')
<script>
    (function () {
        var form = document.getElementById('step-form');
        var type = document.getElementById('type');

        // The final "share" step has no instructions, so hide what doesn't apply.
        function applyType() {
            var share = type.value === 'share';
            [].forEach.call(form.querySelectorAll('[data-lesson-only]'), function (el) { el.hidden = share; });
            [].forEach.call(form.querySelectorAll('[data-share-only]'), function (el) { el.hidden = !share; });
        }
        type.addEventListener('change', applyType);
        applyType();

        // Repeatable rows: add, remove, move up/down.
        [].forEach.call(document.querySelectorAll('[data-repeater]'), function (box) {
            var list = box.querySelector('[data-rows]');
            var tpl = box.querySelector('template');
            var next = parseInt(box.getAttribute('data-next'), 10) || 100;

            function renumber() {
                [].forEach.call(list.querySelectorAll('[data-num]'), function (el, i) { el.textContent = i + 1; });
            }

            box.querySelector('[data-add]').addEventListener('click', function () {
                list.insertAdjacentHTML('beforeend', tpl.innerHTML.replace(/__I__/g, String(next++)));
                renumber();
                var field = list.lastElementChild.querySelector('input[type=text], input:not([type]), textarea');
                if (field) field.focus();
            });

            list.addEventListener('click', function (e) {
                var btn = e.target.closest('button');
                var row = btn && btn.closest('[data-row]');
                if (!row) return;

                if (btn.hasAttribute('data-remove')) {
                    row.remove();
                } else if (btn.hasAttribute('data-up') && row.previousElementSibling) {
                    list.insertBefore(row, row.previousElementSibling);
                } else if (btn.hasAttribute('data-down') && row.nextElementSibling) {
                    list.insertBefore(row.nextElementSibling, row);
                }
                renumber();
            });

            renumber();
        });

        // Preview a newly chosen picture before saving.
        var file = document.getElementById('image');
        var preview = document.getElementById('image-preview');
        if (file && preview) {
            file.addEventListener('change', function () {
                if (!file.files.length) return;
                preview.innerHTML = '';
                var img = document.createElement('img');
                img.alt = 'New picture preview';
                img.src = URL.createObjectURL(file.files[0]);
                preview.appendChild(img);
            });
        }
    })();
</script>
@endpush
