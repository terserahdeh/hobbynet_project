@extends('admin.layouts.admin')

@section('title', 'Hobbies')

@section('content')
<div class="admin-head">
    <div>
        <h1>Hobbies</h1>
        <p>The hobbies learners can pick during sign-up and explore.</p>
    </div>
    <a href="{{ route('admin.hobbies.create') }}" class="admin-btn admin-btn--primary">@include('roadmap.partials.icon', ['name' => 'plus']) New hobby</a>
</div>

<section class="admin-card">
    @if ($hobbies->isEmpty())
        <div class="admin-empty"><strong>No hobbies yet</strong>Add the first one to get started.</div>
    @else
        <div class="admin-scroll">
            <table class="admin-table">
                <thead>
                    <tr><th>Order</th><th>Hobby</th><th>Tagline</th><th>Projects</th><th>Chosen by</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($hobbies as $hobby)
                    <tr>
                        <td class="is-num">{{ $hobby->sort }}</td>
                        <td>
                            <strong><span class="admin-dot admin-dot--{{ $hobby->color }}"></span>{{ $hobby->name }}</strong>
                            <small>{{ $hobby->slug }}</small>
                        </td>
                        <td>{{ $hobby->tagline }}</td>
                        <td>{{ $projectCounts[$hobby->id] ?? 0 }}</td>
                        <td>{{ $learnerCounts[$hobby->id] ?? 0 }} learners</td>
                        <td class="is-actions">
                            <a href="{{ route('admin.hobbies.edit', $hobby) }}" class="admin-btn admin-btn--ghost admin-btn--sm">Edit</a>
                            <form method="POST" action="{{ route('admin.hobbies.destroy', $hobby) }}" class="admin-inline"
                                  onsubmit="return confirm('Delete {{ addslashes($hobby->name) }}? Its projects stay, but lose this hobby, and {{ $learnerCounts[$hobby->id] ?? 0 }} learners will lose it from their interests.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn--danger admin-btn--sm">Delete</button>
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
