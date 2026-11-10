<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') · HobbyNet Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="ad">
<div class="ad-shell">
    <aside class="ad-side">
        <a href="{{ route('admin.dashboard') }}" class="ad-brand">
            <span class="ad-brand__mark">@include('roadmap.partials.icon', ['name' => 'yarn'])</span>
            <span>HobbyNet <small>Admin</small></span>
        </a>

        <nav class="ad-nav" aria-label="Admin">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                @include('roadmap.partials.icon', ['name' => 'target']) Overview
            </a>

            <span class="ad-nav__label">Content</span>
            <a href="{{ route('admin.hobbies.index') }}" class="{{ request()->routeIs('admin.hobbies.*') ? 'is-active' : '' }}">
                @include('roadmap.partials.icon', ['name' => 'heart']) Hobbies
            </a>
            <a href="{{ route('admin.projects.index') }}" class="{{ request()->routeIs('admin.projects.*', 'admin.steps.*') ? 'is-active' : '' }}">
                @include('roadmap.partials.icon', ['name' => 'compass']) Projects &amp; roadmaps
            </a>
            <a href="{{ route('admin.badges.index') }}" class="{{ request()->routeIs('admin.badges.*') ? 'is-active' : '' }}">
                @include('roadmap.partials.icon', ['name' => 'star']) Badges
            </a>
        </nav>

        <div class="ad-side__foot">
            Signed in as<br><strong>{{ auth()->user()->name }}</strong><br>
            <a href="{{ url('/') }}">&larr; Back to the site</a>
        </div>
    </aside>

    <main class="ad-main">
        <div class="ad-wrap @yield('wrap')">
            @if (session('status'))
                <div class="ad-flash ad-flash--ok" role="status">{{ session('status') }}</div>
            @endif

            @if (session('warning'))
                <div class="ad-flash ad-flash--warn" role="alert">{{ session('warning') }}</div>
            @endif

            @if ($errors->any())
                <div class="ad-flash ad-flash--err" role="alert">
                    <div>
                        <strong>Please fix these before saving:</strong>
                        <ul>
                            @foreach (array_slice($errors->all(), 0, 6) as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
@stack('scripts')
</body>
</html>
