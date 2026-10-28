@extends('layouts.app')
@section('title', 'Hasil Karya · HobbyNet')
@section('content')
@include('packs._assets')
@php
$workTitle = $submission->title;
$workCourse = $submission->starterPack?->project?->title ?? 'Course Jelajah';
$workDate = $submission->submitted_at;
$workReflection = $submission->reflection;
$workPhotos = collect($submission->photos ?? [])->map(fn ($path) => \Illuminate\Support\Facades\Storage::disk('public')->url($path));
$workEditUrl = route('courses.submission', $submission->starterPack);
@endphp
@include('profile._work_detail')
@endsection
