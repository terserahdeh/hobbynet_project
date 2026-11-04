<?php
namespace App\Http\Controllers;
use App\Models\ProjectSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class WorkController extends Controller {
    public function show(Request $request, ProjectSubmission $submission) {
        abort_unless($submission->user_id === $request->user()->id, 403);
        abort_unless($submission->status === ProjectSubmission::STATUS_SUBMITTED, 404);
        $submission->load(['photos','project']);
        return view('profile.work', compact('submission'));
    }
    public function edit(Request $request, ProjectSubmission $submission) {
        abort_unless((int)$submission->user_id === (int)$request->user()->id,403);
        $submission->load(['photos','project']);
        return view('profile.edit_work', compact('submission'));
    }
    public function update(Request $request, ProjectSubmission $submission) {
        abort_unless((int)$submission->user_id === (int)$request->user()->id,403);
        $data=$request->validate([
          'title'=>['required','string','max:120'], 'reflection'=>['nullable','string','max:2000'],
          'photos'=>['nullable','array','max:1'], 'photos.*'=>['image','mimes:jpg,jpeg,png,webp','max:5120'],
          'remove_photo_ids'=>['nullable','array'], 'remove_photo_ids.*'=>['integer'],
        ]);
        $newFile = $request->file('photos', [])[0] ?? null;
        $oldPhotos = $submission->photos()->get();
        if (! $newFile && $oldPhotos->isEmpty()) {
            throw ValidationException::withMessages(['photos' => 'Karya harus memiliki satu foto.']);
        }
        $newPath = null;
        if ($newFile) $newPath = $newFile->store('submissions/'.$submission->user_id.'/'.$submission->project_id, 'public');
        try {
            DB::transaction(function () use ($submission, $data, $newPath) {
                $submission->update(['title' => $data['title'], 'reflection' => $data['reflection'] ?? null]);
                if ($newPath) {
                    $submission->photos()->delete();
                    $submission->photos()->create(['path' => $newPath, 'position' => 1]);
                }
            });
        } catch (\Throwable $e) {
            if ($newPath) Storage::disk('public')->delete($newPath);
            throw $e;
        }
        if ($newPath) foreach ($oldPhotos as $photo) Storage::disk('public')->delete($photo->path);
        return redirect()->route('roadmap.work.show',$submission)->with('status','Karya berhasil diperbarui.');
    }
}
