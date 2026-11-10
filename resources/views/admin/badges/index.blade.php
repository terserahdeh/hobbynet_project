@extends('admin.layouts.admin')

@section('title', 'Badges')

@section('content')
<div class="ad-head">
    <div>
        <h1>Badges</h1>
        <p>Small celebrations learners earn. Attach a badge to a roadmap step to hand it out.</p>
    </div>
    <a href="{{ route('admin.badges.create') }}" class="ad-btn ad-btn--primary">@include('roadmap.partials.icon', ['name' => 'plus']) New badge</a>
</div>

<section class="ad-card">
    @if ($badges->isEmpty())
        <div class="ad-empty"><strong>No badges yet</strong>Add one, then attach it to a step.</div>
    @else
        <div class="ad-scroll">
            <table class="ad-table">
                <thead>
                    <tr><th>Badge</th><th>Description</th><th>Used on</th><th>Earned</th><th></th></tr>
                </thead>
                <tbody>
                @foreach ($badges as $badge)
                    <tr>
                        <td><strong><span class="ad-dot ad-dot--{{ $badge->color }}"></span>{{ $badge->name }}</strong><small>{{ $badge->slug }}</small></td>
                        <td>{{ $badge->description }}</td>
                        <td>
                            @if (in_array($badge->slug, $protected, true))
                                <span class="ad-pill ad-pill--blue">Automatic</span>
                            @else
                                {{ $usedBy[$badge->id] ?? 0 }} {{ \Illuminate\Support\Str::plural('step', $usedBy[$badge->id] ?? 0) }}
                            @endif
                        </td>
                        <td>{{ $earned[$badge->id] ?? 0 }}</td>
                        <td class="is-actions">
                            <a href="{{ route('admin.badges.edit', $badge) }}" class="ad-btn ad-btn--ghost ad-btn--sm">Edit</a>
                            @unless (in_array($badge->slug, $protected, true))
                                <form method="POST" action="{{ route('admin.badges.destroy', $badge) }}" class="ad-inline"
                                      onsubmit="return confirm('Delete {{ addslashes($badge->name) }}? Learners who earned it will lose it, and steps using it will stop awarding it.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="ad-btn ad-btn--danger ad-btn--sm">Delete</button>
                                </form>
                            @endunless
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>
@endsection
