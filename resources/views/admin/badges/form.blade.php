@extends('admin.layouts.admin')

@php $isNew = ! $badge->exists; @endphp

@section('title', $isNew ? 'New badge' : 'Edit ' . $badge->name)
@section('wrap', 'ad-wrap--narrow')

@section('content')
<nav class="ad-crumbs"><a href="{{ route('admin.badges.index') }}">Badges</a> / {{ $isNew ? 'New badge' : $badge->name }}</nav>
<div class="ad-head">
    <div>
        <h1>{{ $isNew ? 'New badge' : 'Edit badge' }}</h1>
        <p>Celebrate effort, not competition. Keep the wording warm and short.</p>
    </div>
</div>

<form method="POST" action="{{ $isNew ? route('admin.badges.store') : route('admin.badges.update', $badge) }}" class="ad-card ad-card__pad ad-form">
    @csrf
    @unless ($isNew) @method('PUT') @endunless

    <div class="ad-field">
        <label class="ad-label" for="name">Name</label>
        <input id="name" name="name" class="ad-input @error('name') has-error @enderror" value="{{ old('name', $badge->name) }}" maxlength="255" placeholder="Stitch Master" required>
        @error('name') <p class="ad-error">{{ $message }}</p> @enderror
    </div>

    <div class="ad-field">
        <label class="ad-label" for="description">Description</label>
        <input id="description" name="description" class="ad-input @error('description') has-error @enderror" value="{{ old('description', $badge->description) }}" maxlength="255" placeholder="For learning the single crochet stitch." required>
        @error('description') <p class="ad-error">{{ $message }}</p> @enderror
    </div>

    <div class="ad-grid2">
        <div class="ad-field">
            <label class="ad-label" for="icon">Icon</label>
            <select id="icon" name="icon" class="ad-input">
                @foreach (\App\Http\Requests\Admin\BadgeRequest::ICONS as $icon)
                    <option value="{{ $icon }}" @selected(old('icon', $badge->icon) === $icon)>{{ ucfirst($icon) }}</option>
                @endforeach
            </select>
        </div>
        <div class="ad-field">
            <label class="ad-label" for="color">Colour</label>
            <select id="color" name="color" class="ad-input">
                @foreach (['green' => 'Green', 'pink' => 'Pink', 'blue' => 'Blue'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('color', $badge->color) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="ad-actions">
        <button type="submit" class="ad-btn ad-btn--primary">{{ $isNew ? 'Add badge' : 'Save changes' }}</button>
        <a href="{{ route('admin.badges.index') }}" class="ad-btn ad-btn--ghost">Cancel</a>
    </div>
</form>
@endsection
