<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HobbyRequest;
use App\Models\Hobby;
use App\Models\Project;
use App\Support\UniqueSlug;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class HobbyController extends Controller
{
    public function index(): View
    {
        return view('admin.hobbies.index', [
            'hobbies' => Hobby::orderBy('sort')->orderBy('name')->get(),
            'projectCounts' => Project::whereNotNull('hobby_id')
                ->selectRaw('hobby_id, count(*) as total')->groupBy('hobby_id')->pluck('total', 'hobby_id'),
            'learnerCounts' => DB::table('hobby_user')
                ->selectRaw('hobby_id, count(*) as total')->groupBy('hobby_id')->pluck('total', 'hobby_id'),
        ]);
    }

    public function create(): View
    {
        return view('admin.hobbies.form', [
            'hobby' => (new Hobby)->forceFill(['color' => 'pink', 'sort' => 0]),
        ]);
    }

    public function store(HobbyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $hobby = new Hobby;
        $hobby->forceFill(array_merge($data, ['sort' => $data['sort'] ?? 0]))
            ->forceFill(['slug' => UniqueSlug::make($data['name'], 'hobbies')])
            ->save();

        return redirect()->route('admin.hobbies.index')
            ->with('status', "“{$hobby->name}” was added. Next, create its starter project.");
    }

    public function edit(Hobby $hobby): View
    {
        return view('admin.hobbies.form', ['hobby' => $hobby]);
    }

    public function update(HobbyRequest $request, Hobby $hobby): RedirectResponse
    {
        $data = $request->validated();
        $hobby->forceFill(array_merge($data, ['sort' => $data['sort'] ?? 0]))->save();

        return redirect()->route('admin.hobbies.index')->with('status', "“{$hobby->name}” was updated.");
    }

    public function destroy(Hobby $hobby): RedirectResponse
    {
        $name = $hobby->name;
        $hobby->delete(); // projects keep existing but lose their hobby; learners' picks are removed

        return redirect()->route('admin.hobbies.index')->with('status', "“{$name}” was deleted.");
    }
}
