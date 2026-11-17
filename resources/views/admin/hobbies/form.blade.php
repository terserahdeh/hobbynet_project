@extends('admin.layouts.admin')

@php $isNew = ! $hobby->exists; @endphp

@section('title', $isNew ? 'New hobby' : 'Edit ' . $hobby->name)
@section('wrap', 'admin-wrap--narrow')

@section('content')
<nav class="admin-crumbs"><a href="{{ route('admin.hobbies.index') }}">Hobbies</a> / {{ $isNew ? 'New hobby' : $hobby->name }}</nav>
<div class="admin-head">
    <div>
        <h1>{{ $isNew ? 'New hobby' : 'Edit hobby' }}</h1>
        <p>{{ $isNew ? 'It will appear in the hobby selector and on Explore.' : 'Changes show up for learners straight away.' }}</p>
    </div>
</div>

<form method="POST" action="{{ $isNew ? route('admin.hobbies.store') : route('admin.hobbies.update', $hobby) }}" class="admin-card admin-card__pad admin-form">
    @csrf
    @unless ($isNew) @method('PUT') @endunless

    <div class="admin-grid2">
        <div class="admin-field">
            <label class="admin-label" for="name">Name</label>
            <input id="name" name="name" class="admin-input @error('name') has-error @enderror" value="{{ old('name', $hobby->name) }}" maxlength="255" placeholder="Crochet" required>
            @error('name') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
        <div class="admin-field">
            <label class="admin-label" for="slug">Web address</label>
            <input id="slug" class="admin-input" value="{{ $isNew ? 'made from the name' : $hobby->slug }}" readonly>
            <p class="admin-help">Set once, so links keep working.</p>
        </div>
    </div>

    <div class="admin-field">
        <label class="admin-label" for="tagline">Tagline</label>
        <input id="tagline" name="tagline" class="admin-input @error('tagline') has-error @enderror" value="{{ old('tagline', $hobby->tagline) }}" maxlength="255" placeholder="Make something soft and handmade" required>
        @error('tagline') <p class="admin-error">{{ $message }}</p> @enderror
    </div>

    <div class="admin-field">
        <label class="admin-label" for="first_project">First project</label>
        <input id="first_project" name="first_project" class="admin-input @error('first_project') has-error @enderror" value="{{ old('first_project', $hobby->first_project) }}" maxlength="255" placeholder="A simple crochet coaster" required>
        <p class="admin-help">Shown to learners when they choose this hobby.</p>
        @error('first_project') <p class="admin-error">{{ $message }}</p> @enderror
    </div>

    <div class="admin-grid2">
        <div class="admin-field">
            <label class="admin-label" for="color">Colour</label>
            <select id="color" name="color" class="admin-input">
                @foreach (['pink' => 'Pink', 'green' => 'Green', 'blue' => 'Blue'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('color', $hobby->color) === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="admin-field">
            <label class="admin-label" for="sort">Order</label>
            <input id="sort" name="sort" type="number" min="0" max="65535" class="admin-input @error('sort') has-error @enderror" value="{{ old('sort', $hobby->sort ?? 0) }}">
            <p class="admin-help">Lower numbers come first.</p>
            @error('sort') <p class="admin-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="admin-actions">
        <button type="submit" class="admin-btn admin-btn--primary">{{ $isNew ? 'Add hobby' : 'Save changes' }}</button>
        <a href="{{ route('admin.hobbies.index') }}" class="admin-btn admin-btn--ghost">Cancel</a>
    </div>
</form>
@endsection
