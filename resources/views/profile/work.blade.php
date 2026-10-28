@extends('layouts.app')
@section('title', 'Hasil Karya · HobbyNet')
@section('content')
@include('packs._assets')
@php
$workTitle = $submission->title ?: $submission->project->title;
$workCourse = $submission->project->title;
$workDate = $submission->submitted_at;
$workReflection = $submission->reflection;
$workPhotos = $submission->photos->map(fn ($photo) => $photo->url);
@endphp
@include('profile._work_detail')
@endsection
