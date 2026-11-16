@extends('admin.layouts.admin')

@php
    $isNew = ! $project->exists;
    $hobbyOf = fn ($id) => $hobbies->firstWhere('id', $id);
    $liveHobby = $isNew ? null : $hobbyOf($project->hobby_id);
@endphp

@section('title', $isNew ? 'New project' : 'Edit ' . $project->title)

@section('content')
<nav class="admin-crumbs"><a href="{{ route('admin.projects.index') }}">Projects &amp; roadmaps</a> / {{ $isNew ? 'New project' : $project->title }}</nav>
<div class="admin-head">
    <div>
        <h1>{{ $isNew ? 'New project' : $project->title }}</h1>
        <p>{{ $isNew ? 'Set it as a hobby\'s starter project to give it a guided roadmap.' : 'Project details. Roadmap steps are managed separately.' }}</p>
    </div>
</div>

<div class="admin-layout">
    <div>
        <form method="POST" action="{{ $isNew ? route('admin.projects.store') : route('admin.projects.update', $project) }}" class="admin-card admin-card__pad admin-form">
            @csrf
            @unless ($isNew) @method('PUT') @endunless

            <div class="admin-field">
                <label class="admin-label" for="title">Title</label>
                <input id="title" name="title" class="admin-input @error('title') has-error @enderror" value="{{ old('title', $project->title) }}" maxlength="255" placeholder="Beginner Crochet Coaster" required>
                @error('title') <p class="admin-error">{{ $message }}</p> @enderror
            </div>

            <div class="admin-grid2">
                <div class="admin-field">
                    <label class="admin-label" for="hobby_id">Hobby</label>
                    <select id="hobby_id" name="hobby_id" class="admin-input @error('hobby_id') has-error @enderror">
                        <option value="">No hobby</option>
                        @foreach ($hobbies as $hobby)
                            <option value="{{ $hobby->id }}" @selected((string) old('hobby_id', $project->hobby_id) === (string) $hobby->id)>{{ $hobby->name }}</option>
                        @endforeach
                    </select>
                    @error('hobby_id') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
                <div class="admin-field">
                    <label class="admin-label" for="category">Category</label>
                    <input id="category" name="category" class="admin-input @error('category') has-error @enderror" value="{{ old('category', $project->category) }}" maxlength="255" placeholder="Crochet" required>
                    @error('category') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="admin-field">
                <label class="admin-label" for="summary">Short summary</label>
                <input id="summary" name="summary" class="admin-input @error('summary') has-error @enderror" value="{{ old('summary', $project->summary) }}" maxlength="255" placeholder="Make a soft, simple coaster with one basic stitch." required>
                <p class="admin-help">One line, shown on cards.</p>
                @error('summary') <p class="admin-error">{{ $message }}</p> @enderror
            </div>

            <div class="admin-field">
                <label class="admin-label" for="description">Description</label>
                <textarea id="description" name="description" class="admin-input @error('description') has-error @enderror" maxlength="5000" placeholder="What learners will make, and why it's a good first project.">{{ old('description', $project->description) }}</textarea>
                @error('description') <p class="admin-error">{{ $message }}</p> @enderror
            </div>

            <div class="admin-grid3">
                <div class="admin-field">
                    <label class="admin-label" for="level">Level</label>
                    <select id="level" name="level" class="admin-input">
                        @foreach (\App\Http\Requests\Admin\ProjectRequest::LEVELS as $level)
                            <option value="{{ $level }}" @selected(old('level', $project->level) === $level)>{{ ucfirst($level) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="admin-field">
                    <label class="admin-label" for="duration_minutes">Duration (min)</label>
                    <input id="duration_minutes" name="duration_minutes" type="number" min="1" max="10080" class="admin-input @error('duration_minutes') has-error @enderror" value="{{ old('duration_minutes', $project->duration_minutes) }}" required>
                    @error('duration_minutes') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
                <div class="admin-field">
                    <label class="admin-label" for="color">Colour</label>
                    <select id="color" name="color" class="admin-input">
                        @foreach (['pink' => 'Pink', 'green' => 'Green', 'blue' => 'Blue'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('color', $project->color) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="admin-grid2">
                <div class="admin-field">
                    <label class="admin-label" for="image">Card image</label>
                    <input id="image" name="image" class="admin-input @error('image') has-error @enderror" value="{{ old('image', $project->image) }}" maxlength="255" placeholder="images/projects/coaster.jpg">
                    <p class="admin-help">A path inside <code>public/</code> or a full link. Optional.</p>
                    @error('image') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
                <div class="admin-field">
                    <label class="admin-label" for="icon">Icon name</label>
                    <input id="icon" name="icon" class="admin-input @error('icon') has-error @enderror" value="{{ old('icon', $project->icon) }}" maxlength="30" placeholder="default">
                    <p class="admin-help">Used when there is no card image.</p>
                    @error('icon') <p class="admin-error">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="admin-field">
                <label class="admin-check">
                    <input type="checkbox" name="is_starter" value="1" @checked(old('is_starter', $project->is_starter))>
                    <span>
                        <strong>Starter project for this hobby</strong>
                        <small>Gives the hobby a guided roadmap. A hobby has one starter project.</small>
                    </span>
                </label>
                @error('is_starter') <p class="admin-error">{{ $message }}</p> @enderror
            </div>

            <label class="admin-check">
                <input type="checkbox" name="is_popular" value="1" @checked(old('is_popular', $project->is_popular))>
                <span>
                    <strong>Show in Popular Projects</strong>
                    <small>Appears on the home page for learners of every hobby.</small>
                </span>
            </label>

            <div class="admin-actions">
                <button type="submit" class="admin-btn admin-btn--primary">{{ $isNew ? 'Create project' : 'Save project' }}</button>
                <a href="{{ route('admin.projects.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
            </div>
        </form>

        @unless ($isNew)
            <div class="admin-danger">
                <p><strong>Delete this project.</strong> Its roadmap steps, learner progress and shared creations go with it.</p>
                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                      onsubmit="return confirm('Delete {{ addslashes($project->title) }}? This also removes its roadmap and every learner\'s progress. This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="admin-btn admin-btn--danger">Delete project</button>
                </form>
            </div>
        @endunless
    </div>

    {{-- Roadmap panel --}}
    @unless ($isNew)
        <aside class="admin-sticky">
            <section class="admin-card admin-card__pad">
                <h2>Roadmap</h2>

                @if (! $project->is_starter)
                    <p class="admin-card__sub">Only a hobby's starter project has a guided roadmap. Tick “Starter project” and save to build one.</p>
                @else
                    <p style="margin-top: 12px;">
                        @if ($project->roadmap_published)
                            <span class="admin-pill admin-pill--green">Live for learners</span>
                        @else
                            <span class="admin-pill admin-pill--amber">Draft, hidden from learners</span>
                        @endif
                    </p>

                    <dl class="admin-side-list" style="margin-bottom: 0;">
                        <div><dt>Steps</dt><dd style="margin:0;"><strong>{{ $stepCount }}</strong></dd></div>
                        <div><dt>Learners started</dt><dd style="margin:0;"><strong>{{ $learners['started'] ?? 0 }}</strong></dd></div>
                        <div><dt>Finished</dt><dd style="margin:0;"><strong>{{ $learners['finished'] ?? 0 }}</strong></dd></div>
                    </dl>

                    @if ($problems !== [])
                        <div class="admin-ready admin-ready--todo" style="margin-top: 18px;">
                            <span class="admin-ready__icon">@include('roadmap.partials.icon', ['name' => 'bulb'])</span>
                            <div>
                                <strong>Before you publish</strong>
                                <ul>@foreach ($problems as $problem) <li>{{ $problem }}</li> @endforeach</ul>
                            </div>
                        </div>
                    @endif

                    <div class="admin-actions" style="margin-top: 18px;">
                        <a href="{{ route('admin.projects.steps.index', $project) }}" class="admin-btn admin-btn--primary">Manage steps</a>
                    </div>

                    <div class="admin-actions" style="padding-top: 10px;">
                        @if ($project->roadmap_published)
                            <form method="POST" action="{{ route('admin.projects.publish', $project) }}" class="admin-inline">
                                @csrf <input type="hidden" name="publish" value="0">
                                <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm">Hide from learners</button>
                            </form>
                            @if ($liveHobby)
                                <a href="{{ route('roadmap.show', $liveHobby->slug) }}" class="admin-btn admin-btn--ghost admin-btn--sm" target="_blank" rel="noopener">View as learner</a>
                            @endif
                        @else
                            <form method="POST" action="{{ route('admin.projects.publish', $project) }}" class="admin-inline">
                                @csrf <input type="hidden" name="publish" value="1">
                                <button type="submit" class="admin-btn admin-btn--ghost admin-btn--sm" @disabled($problems !== [])>Publish roadmap</button>
                            </form>
                        @endif
                    </div>
                @endif
            </section>
        </aside>
    @endunless
</div>
@endsection
