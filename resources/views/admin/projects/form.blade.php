@extends('admin.layouts.admin')

@php
    $isNew = ! $project->exists;
    $hobbyOf = fn ($id) => $hobbies->firstWhere('id', $id);
    $liveHobby = $isNew ? null : $hobbyOf($project->hobby_id);
@endphp

@section('title', $isNew ? 'New project' : 'Edit ' . $project->title)

@section('content')
<nav class="ad-crumbs"><a href="{{ route('admin.projects.index') }}">Projects &amp; roadmaps</a> / {{ $isNew ? 'New project' : $project->title }}</nav>
<div class="ad-head">
    <div>
        <h1>{{ $isNew ? 'New project' : $project->title }}</h1>
        <p>{{ $isNew ? 'Set it as a hobby\'s starter project to give it a guided roadmap.' : 'Project details. Roadmap steps are managed separately.' }}</p>
    </div>
</div>

<div class="ad-layout">
    <div>
        <form method="POST" action="{{ $isNew ? route('admin.projects.store') : route('admin.projects.update', $project) }}" class="ad-card ad-card__pad ad-form">
            @csrf
            @unless ($isNew) @method('PUT') @endunless

            <div class="ad-field">
                <label class="ad-label" for="title">Title</label>
                <input id="title" name="title" class="ad-input @error('title') has-error @enderror" value="{{ old('title', $project->title) }}" maxlength="255" placeholder="Beginner Crochet Coaster" required>
                @error('title') <p class="ad-error">{{ $message }}</p> @enderror
            </div>

            <div class="ad-grid2">
                <div class="ad-field">
                    <label class="ad-label" for="hobby_id">Hobby</label>
                    <select id="hobby_id" name="hobby_id" class="ad-input @error('hobby_id') has-error @enderror">
                        <option value="">No hobby</option>
                        @foreach ($hobbies as $hobby)
                            <option value="{{ $hobby->id }}" @selected((string) old('hobby_id', $project->hobby_id) === (string) $hobby->id)>{{ $hobby->name }}</option>
                        @endforeach
                    </select>
                    @error('hobby_id') <p class="ad-error">{{ $message }}</p> @enderror
                </div>
                <div class="ad-field">
                    <label class="ad-label" for="category">Category</label>
                    <input id="category" name="category" class="ad-input @error('category') has-error @enderror" value="{{ old('category', $project->category) }}" maxlength="255" placeholder="Crochet" required>
                    @error('category') <p class="ad-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="ad-field">
                <label class="ad-label" for="summary">Short summary</label>
                <input id="summary" name="summary" class="ad-input @error('summary') has-error @enderror" value="{{ old('summary', $project->summary) }}" maxlength="255" placeholder="Make a soft, simple coaster with one basic stitch." required>
                <p class="ad-help">One line, shown on cards.</p>
                @error('summary') <p class="ad-error">{{ $message }}</p> @enderror
            </div>

            <div class="ad-field">
                <label class="ad-label" for="description">Description</label>
                <textarea id="description" name="description" class="ad-input @error('description') has-error @enderror" maxlength="5000" placeholder="What learners will make, and why it's a good first project.">{{ old('description', $project->description) }}</textarea>
                @error('description') <p class="ad-error">{{ $message }}</p> @enderror
            </div>

            <div class="ad-grid3">
                <div class="ad-field">
                    <label class="ad-label" for="level">Level</label>
                    <select id="level" name="level" class="ad-input">
                        @foreach (\App\Http\Requests\Admin\ProjectRequest::LEVELS as $level)
                            <option value="{{ $level }}" @selected(old('level', $project->level) === $level)>{{ ucfirst($level) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="ad-field">
                    <label class="ad-label" for="duration_minutes">Duration (min)</label>
                    <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="10080" class="ad-input @error('duration_minutes') has-error @enderror" value="{{ old('duration_minutes', $project->duration_minutes) }}" required>
                    @error('duration_minutes') <p class="ad-error">{{ $message }}</p> @enderror
                </div>
                <div class="ad-field">
                    <label class="ad-label" for="color">Colour</label>
                    <select id="color" name="color" class="ad-input">
                        @foreach (['pink' => 'Pink', 'green' => 'Green', 'blue' => 'Blue'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('color', $project->color) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="ad-grid2">
                <div class="ad-field">
                    <label class="ad-label" for="image">Card image</label>
                    <input id="image" name="image" class="ad-input @error('image') has-error @enderror" value="{{ old('image', $project->image) }}" maxlength="255" placeholder="images/projects/coaster.jpg">
                    <p class="ad-help">A path inside <code>public/</code> or a full link. Optional.</p>
                    @error('image') <p class="ad-error">{{ $message }}</p> @enderror
                </div>
                <div class="ad-field">
                    <label class="ad-label" for="icon">Icon name</label>
                    <input id="icon" name="icon" class="ad-input @error('icon') has-error @enderror" value="{{ old('icon', $project->icon) }}" maxlength="30" placeholder="default">
                    <p class="ad-help">Used when there is no card image.</p>
                    @error('icon') <p class="ad-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="ad-field">
                <label class="ad-check">
                    <input type="checkbox" name="is_starter" value="1" @checked(old('is_starter', $project->is_starter))>
                    <span>
                        <strong>Starter project for this hobby</strong>
                        <small>Gives the hobby a guided roadmap. A hobby has one starter project.</small>
                    </span>
                </label>
                @error('is_starter') <p class="ad-error">{{ $message }}</p> @enderror
            </div>

            <label class="ad-check">
                <input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $project->is_popular))>
                <span>
                    <strong>Show in Popular Projects</strong>
                    <small>Appears on the home page for learners of every hobby.</small>
                </span>
            </label>

            <div class="ad-actions">
                <button type="submit" class="ad-btn ad-btn--primary">{{ $isNew ? 'Create project' : 'Save project' }}</button>
                <a href="{{ route('admin.projects.index') }}" class="ad-btn ad-btn--ghost">Cancel</a>
            </div>
        </form>

        @unless ($isNew)
            <div class="ad-danger">
                <p><strong>Delete this project.</strong> Its roadmap steps, learner progress and shared creations go with it.</p>
                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete {{ addslashes($project->title) }}? This also removes its roadmap and every learner\'s progress. This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="ad-btn ad-btn--danger">Delete project</button>
                </form>
            </div>
        @endunless
    </div>

    {{-- Roadmap panel --}}
    @unless ($isNew)
        <aside class="ad-sticky">
            <section class="ad-card ad-card__pad">
                <h2>Roadmap</h2>

                @if (! $project->is_starter)
                    <p class="ad-card__sub">Only a hobby's starter project has a guided roadmap. Tick “Starter project” and save to build one.</p>
                @else
                    <p style="margin-top: 12px;">
                        @if ($project->roadmap_published)
                            <span class="ad-pill ad-pill--green">Live for learners</span>
                        @else
                            <span class="ad-pill ad-pill--amber">Draft, hidden from learners</span>
                        @endif
                    </p>

                    <dl class="ad-side-list" style="margin-bottom: 0;">
                        <div><dt>Steps</dt><dd style="margin:0;"><strong>{{ $stepCount }}</strong></dd></div>
                        <div><dt>Learners started</dt><dd style="margin:0;"><strong>{{ $learners['started'] ?? 0 }}</strong></dd></div>
                        <div><dt>Finished</dt><dd style="margin:0;"><strong>{{ $learners['finished'] ?? 0 }}</strong></dd></div>
                    </dl>

                    @if ($problems !== [])
                        <div class="ad-ready ad-ready--todo" style="margin-top: 18px;">
                            <span class="ad-ready__icon">@include('roadmap.partials.icon', ['name' => 'bulb'])</span>
                            <div>
                                <strong>Before you publish</strong>
                                <ul>@foreach ($problems as $problem) <li>{{ $problem }}</li> @endforeach</ul>
                            </div>
                        </div>
                    @endif

                    <div class="ad-actions" style="margin-top: 18px;">
                        <a href="{{ route('admin.projects.steps.index', $project) }}" class="ad-btn ad-btn--primary">Manage steps</a>
                    </div>

                    <div class="ad-actions" style="padding-top: 10px;">
                        @if ($project->roadmap_published)
                            <form method="POST" action="{{ route('admin.projects.publish', $project) }}" class="ad-inline">
                                @csrf <input type="hidden" name="publish" value="0">
                                <button type="submit" class="ad-btn ad-btn--ghost ad-btn--sm">Hide from learners</button>
                            </form>
                            @if ($liveHobby)
                                <a href="{{ route('roadmap.show', $liveHobby->slug) }}" class="ad-btn ad-btn--ghost ad-btn--sm" target="_blank" rel="noopener">View as learner</a>
                            @endif
                        @else
                            <form method="POST" action="{{ route('admin.projects.publish', $project) }}" class="ad-inline">
                                @csrf <input type="hidden" name="publish" value="1">
                                <button type="submit" class="ad-btn ad-btn--ghost ad-btn--sm" @disabled($problems !== [])>Publish roadmap</button>
                            </form>
                        @endif
                    </div>
                @endif
            </section>
        </aside>
    @endunless
</div>
@endsection
