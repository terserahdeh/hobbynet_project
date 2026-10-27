@extends('layouts.app')

@section('title', 'Look What You Made!')

@section('content')
<link rel="stylesheet" href="{{ asset('css/roadmap.css') }}">

@php
    $tags = array_pad(array_slice(old('tags', $submission->tags ?? []), 0, 3), 3, '');
    $existing = $submission->exists ? $submission->photos : collect();
@endphp

<div class="rm-page">
    <header class="rm-topbar">
        <div class="rm-topbar__left">
            <a href="{{ route('roadmap.show', $hobby->slug) }}" class="rm-topbar__close" aria-label="Back to your journey">@include('roadmap.partials.icon', ['name' => 'close'])</a>
            <div class="rm-topbar__title">
                <strong>{{ $project->title }}</strong>
                <span>Final milestone</span>
            </div>
        </div>

        <div class="rm-topbar__mid">
            <div class="rm-topbar__labels">
                <span>Final step</span>
                <span>{{ $summary['percent'] }}% roadmap progress</span>
            </div>
            <div class="rm-bar" role="progressbar" aria-valuenow="{{ $summary['percent'] }}" aria-valuemin="0" aria-valuemax="100" aria-label="Roadmap progress">
                <span class="rm-bar__fill" style="width: {{ $summary['percent'] }}%"></span>
            </div>
        </div>
    </header>

    <div class="rm-body">
    <div class="rm-container rm-container--form">
        @include('roadmap.partials.flash')

        <header class="rm-center" style="margin-top: 24px;">
            <div class="rm-hero-icon">@include('roadmap.partials.icon', ['name' => 'heart'])</div>
            <h1 class="rm-display">Look What You Made!</h1>
            <p class="rm-sub">You've learned the basics. Now share your creation and celebrate how far you've come.</p>
        </header>

        <form method="POST" action="{{ route('roadmap.submission.store', $hobby->slug) }}" enctype="multipart/form-data" class="rm-card rm-form">
            @csrf

            <div class="rm-form__body">
                <div>
                    <label for="title" class="rm-label">Project title</label>
                    <input id="title" name="title" type="text" maxlength="120" class="rm-input"
                           value="{{ old('title', $submission->title) }}" placeholder="My First {{ $project->title }}">
                    @error('title') <p class="rm-error">{{ $message }}</p> @enderror
                </div>

                <div class="rm-media">
                    <div>
                        <span class="rm-label">Add photos</span>
                        <label for="photos" class="rm-upload">
                            <span class="rm-upload__icon">@include('roadmap.partials.icon', ['name' => 'image'])</span>
                            <strong>Click to upload or drag &amp; drop</strong>
                            <small>PNG, JPG or WebP &middot; up to {{ $maxPhotos }} photos &middot; 5 MB each</small>
                            <input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                        </label>
                        @error('photos') <p class="rm-error">{{ $message }}</p> @enderror
                        @error('photos.*') <p class="rm-error">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <span class="rm-label">Preview</span>
                        <div class="rm-preview">
                            <div class="rm-preview__empty" id="rm-empty" @if ($existing->isNotEmpty()) hidden @endif>
                                Your photos will show up here.
                            </div>
                            <ul class="rm-photos {{ $existing->count() <= 1 ? 'rm-photos--single' : '' }}" id="rm-photos">
                                @foreach ($existing as $photo)
                                    <li class="rm-photo">
                                        <img src="{{ $photo->url }}" alt="Photo of your creation">
                                        <span class="rm-photo__actions">
                                            <label class="rm-remove">
                                                <input type="checkbox" name="remove_photo_ids[]" value="{{ $photo->id }}">
                                                <span>Remove</span>
                                            </label>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="rm-meta-row">
                    <div>
                        <label for="reflection" class="rm-label">Reflection (optional)</label>
                        <textarea id="reflection" name="reflection" maxlength="2000" class="rm-textarea"
                                  placeholder="What did you enjoy most about making this?">{{ old('reflection', $submission->reflection) }}</textarea>
                    </div>
                    <div>
                        <span class="rm-label">Category</span>
                        <div class="rm-chipset">
                            <input name="category" type="text" maxlength="50" class="rm-chipinput rm-chipinput--on"
                                   value="{{ old('category', $submission->category) }}" aria-label="Category">
                            @foreach ($tags as $tag)
                                <input name="tags[]" type="text" maxlength="30" class="rm-chipinput" value="{{ $tag }}"
                                       placeholder="+ Add Tag" aria-label="Tag {{ $loop->iteration }}">
                            @endforeach
                        </div>
                    </div>
                </div>

                <label class="rm-showcase">
                    <input type="checkbox" name="is_showcase" value="1" @checked(old('is_showcase', $submission->is_showcase))>
                    <span>
                        <strong>Ready for Showcase</strong>
                        <small>I have completed all steps in the roadmap.</small>
                    </span>
                </label>
            </div>

            <div class="rm-form__foot">
                <a href="{{ route('roadmap.show', $hobby->slug) }}" class="rm-link">@include('roadmap.partials.icon', ['name' => 'chev-left']) Back to Roadmap</a>
                <div class="rm-form__actions">
                    <button type="submit" name="action" value="draft" class="rm-btn rm-btn--ghost">Save as Draft</button>
                    <button type="submit" name="action" value="submit" class="rm-btn rm-btn--primary">Submit My Project @include('roadmap.partials.icon', ['name' => 'chev-right'])</button>
                </div>
            </div>
        </form>

        @if ($step->encouragement)
            <p class="rm-quote">
                <span class="rm-quote__icon">@include('roadmap.partials.icon', ['name' => 'bulb'])</span>
                &ldquo;{{ $step->encouragement }}&rdquo;
            </p>
        @endif
    </div>
    </div>
</div>

<script>
    // Show newly chosen photos in the preview before uploading.
    (function () {
        var input = document.getElementById('photos');
        var list = document.getElementById('rm-photos');
        var empty = document.getElementById('rm-empty');
        if (!input || !list) return;

        input.addEventListener('change', function () {
            [].slice.call(list.querySelectorAll('[data-new]')).forEach(function (el) { el.remove(); });

            [].slice.call(input.files).forEach(function (file) {
                var li = document.createElement('li');
                li.className = 'rm-photo';
                li.setAttribute('data-new', '');
                var img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.alt = 'New photo preview';
                li.appendChild(img);
                list.appendChild(li);
            });

            var count = list.children.length;
            list.classList.toggle('rm-photos--single', count <= 1);
            if (empty) empty.hidden = count > 0;
        });
    })();
</script>
@endsection
