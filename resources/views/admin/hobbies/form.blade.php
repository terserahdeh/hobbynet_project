@extends('admin.layouts.admin')

@php $isNew = ! $hobby->exists; @endphp

@section('title', $isNew ? 'New hobby' : 'Edit ' . $hobby->name)
@section('wrap', 'ad-wrap--narrow')

@section('content')
<nav class="ad-crumbs"><a href="{{ route('admin.hobbies.index') }}">Hobbies</a> / {{ $isNew ? 'New hobby' : $hobby->name }}</nav>
<div class="ad-head">
    <div>
        <h1>{{ $isNew ? 'New hobby' : 'Edit hobby' }}</h1>
        <p>{{ $isNew ? 'It will appear in the hobby selector and on Explore.' : 'Changes show up for learners straight away.' }}</p>
    </div>
</div>

<form method="POST" action="{{ $isNew ? route('admin.hobbies.store') : route('admin.hobbies.update', $hobby) }}" class="ad-card ad-card__pad ad-form">
    @csrf
    @unless ($isNew) @method('PUT') @endunless

    <div class="ad-grid2">
        <div class="ad-field">
            <label class="ad-label" for="name">Name</label>
            <input id="name" name="name" class="ad-input @error('name') has-error @enderror" value="{{ old('name', $hobby->name) }}" maxlength="255" placeholder="Crochet" required>
            @error('name') <p class="ad-error">{{ $message }}</p> @enderror
        </div>
        <div class="ad-field">
            <label class="ad-label" for="slug">Web address</label>
            <input id="slug" class="ad-input" value="{{ $isNew ? 'made from the name' : $hobby->slug }}" readonly>
            <p class="ad-help">Set once, so links keep working.</p>
        </div>
    </div>

    <div class="ad-field">
        <label class="ad-label" for="tagline">Tagline</label>
        <input id="tagline" name="tagline" class="ad-input @error('tagline') has-error @enderror" value="{{ old('tagline', $hobby->tagline) }}" maxlength="255" placeholder="Make something soft and handmade" required>
        @error('tagline') <p class="ad-error">{{ $message }}</p> @enderror
    </div>

    <div class="ad-field">
        <label class="ad-label" for="first_project">First project</label>
        <input id="first_project" name="first_project" class="ad-input @error('first_project') has-error @enderror" value="{{ old('first_project', $hobby->first_project) }}" maxlength="255" placeholder="A simple crochet coaster" required>
        <p class="ad-help">Shown to learners when they choose this hobby.</p>
        @error('first_project') <p class="ad-error">{{ $message }}</p> @enderror
    </div>

    <div class="ad-grid2">
        <div class="ad-field">
            <label class="ad-label" for="color">Colour</label>
            <select id="color" name="color" class="ad-input">
                @foreach (['pink' => 'Pink', 'green' => 'Green', 'blue' => 'Blue'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('color', $hobby->color) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="ad-field">
            <label class="ad-label" for="sort">Order</label>
            <input id="sort" name="sort" type="number" min="0" max="65535" class="ad-input @error('sort') has-error @enderror" value="{{ old('sort', $hobby->sort ?? 0) }}">
            <p class="ad-help">Lower numbers come first.</p>
            @error('sort') <p class="ad-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="ad-actions">
        <button type="submit" class="ad-btn ad-btn--primary">{{ $isNew ? 'Add hobby' : 'Save changes' }}</button>
        <a href="{{ route('admin.hobbies.index') }}" class="ad-btn ad-btn--ghost">Cancel</a>
    </div>
</form>
@endsection
