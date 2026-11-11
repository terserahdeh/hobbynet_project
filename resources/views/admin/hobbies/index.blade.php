@extends('admin.layouts.admin')

@section('title', 'Hobbies')

@section('content')
<div class="ad-head">
    <div>
        <h1>Hobbies</h1>
        <p>The hobbies learners can pick during sign-up and explore.</p>
    </div>
    <a href="{{ route('admin.hobbies.create') }}" class="ad-btn ad-btn--primary">@include('roadmap.partials.icon', ['name' => 'plus']) New hobby</a>
</div>

<section class="ad-card">
    @if ($hobbies->isEmpty())
        <div class="ad-empty"><strong>No hobbies yet</strong>Add the first one to get started.</div>
    @else
        <div class="ad-scroll">
            <table class="ad-table">
                <thead>
                    <tr><th>Order</th><th>Hobby</th><th>Tagline</th><th>Projects</th><th>Chosen by</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($hobbies as $hobby)
                    <tr>
                        <td class="is-num">{{ $hobby->sort }}</td>
                        <td>
                            <strong><span class="ad-dot ad-dot--{{ $hobby->color }}"></span>{{ $hobby->name }}</strong>
                            <small>{{ $hobby->slug }}</small>
                        </td>
                        <td>{{ $hobby->tagline }}</td>
                        <td>{{ $projectCounts[$hobby->id] ?? 0 }}</td>
                        <td>{{ $learnerCounts[$hobby->id] ?? 0 }} learners</td>
                        <td class="is-actions">
                            <a href="{{ route('admin.hobbies.edit', $hobby) }}" class="ad-btn ad-btn--ghost ad-btn--sm">Edit</a>
                            <form method="POST" action="{{ route('admin.hobbies.destroy', $hobby) }}" class="ad-inline"
                                  onsubmit="return confirm('Delete {{ addslashes($hobby->name) }}? Its projects stay, but lose this hobby, and {{ $learnerCounts[$hobby->id] ?? 0 }} learners will lose it from their interests.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="ad-btn ad-btn--danger ad-btn--sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
