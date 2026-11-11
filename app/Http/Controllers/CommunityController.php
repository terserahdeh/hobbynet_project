<?php
namespace App\Http\Controllers;
use App\Models\ProjectSubmission;
use App\Models\CommunityComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class CommunityController extends Controller {
 private function publicPost(ProjectSubmission $post): void {abort_unless($post->status === 'submitted' && $post->is_showcase,404);}
 private function notify(ProjectSubmission $post, Request $request, string $type, string $message): void {
  if ($post->user_id === $request->user()->id) return;
  DB::table('community_notifications')->insert(['user_id'=>$post->user_id,'actor_id'=>$request->user()->id,'project_submission_id'=>$post->id,'type'=>$type,'message'=>$message,'created_at'=>now(),'updated_at'=>now()]);
 }
 public function index(Request $request){
  $posts=ProjectSubmission::with(['user','photos','project'])->withCount(['communityLikes','communityComments'])->where('status','submitted')->where('is_showcase',true)->whereHas('photos')->latest('submitted_at')->paginate(12);
  $ids=$posts->pluck('id');$uid=$request->user()->id;
  $liked=DB::table('community_likes')->where('user_id',$uid)->whereIn('project_submission_id',$ids)->pluck('project_submission_id')->all();
  $saved=DB::table('community_saves')->where('user_id',$uid)->whereIn('project_submission_id',$ids)->pluck('project_submission_id')->all();
  return view('community.index',compact('posts','liked','saved'));
 }
 public function show(Request $request, ProjectSubmission $post){
  $this->publicPost($post);$post->load(['user','photos','project'])->loadCount(['communityLikes','communityComments']);
  $comments=CommunityComment::with(['user','replies'])->where('project_submission_id',$post->id)->whereNull('parent_id')->oldest()->get();
  $liked=DB::table('community_likes')->where('user_id',$request->user()->id)->where('project_submission_id',$post->id)->exists();
  $saved=DB::table('community_saves')->where('user_id',$request->user()->id)->where('project_submission_id',$post->id)->exists();
  return view('community.show',compact('post','comments','liked','saved'));
 }
 public function like(Request $request,ProjectSubmission $post){
  $this->publicPost($post);$query=DB::table('community_likes')->where('user_id',$request->user()->id)->where('project_submission_id',$post->id);
  if($query->exists()) $query->delete();else {$query->insert(['user_id'=>$request->user()->id,'project_submission_id'=>$post->id,'created_at'=>now(),'updated_at'=>now()]);$this->notify($post,$request,'like','menyukai karyamu');}
  if ($request->expectsJson()) return response()->json(['active'=>DB::table('community_likes')->where('user_id',$request->user()->id)->where('project_submission_id',$post->id)->exists(),'count'=>DB::table('community_likes')->where('project_submission_id',$post->id)->count()]);
  return back();
 }
 public function save(Request $request,ProjectSubmission $post){
  $this->publicPost($post);$query=DB::table('community_saves')->where('user_id',$request->user()->id)->where('project_submission_id',$post->id);
  if($query->exists()) $query->delete();else {$query->insert(['user_id'=>$request->user()->id,'project_submission_id'=>$post->id,'created_at'=>now(),'updated_at'=>now()]);$this->notify($post,$request,'save','menyimpan karyamu');}
  if ($request->expectsJson()) return response()->json(['active'=>DB::table('community_saves')->where('user_id',$request->user()->id)->where('project_submission_id',$post->id)->exists()]);
  return back();
 }
 public function comment(Request $request,ProjectSubmission $post){
  $this->publicPost($post);$data=$request->validate(['body'=>['required','string','max:1000'],'parent_id'=>['nullable','integer','exists:community_comments,id']]);
  $parent=null;
  if(!empty($data['parent_id'])){$parent=CommunityComment::findOrFail($data['parent_id']);abort_unless($parent->project_submission_id===$post->id,422);}
  $comment=CommunityComment::create(['user_id'=>$request->user()->id,'project_submission_id'=>$post->id,'parent_id'=>$parent?->id,'body'=>$data['body']]);
  $this->notify($post,$request,'comment',$parent?'membalas diskusi di karyamu':'mengomentari karyamu');
  if($parent && $parent->user_id!==$request->user()->id && $parent->user_id!==$post->user_id){DB::table('community_notifications')->insert(['user_id'=>$parent->user_id,'actor_id'=>$request->user()->id,'project_submission_id'=>$post->id,'type'=>'reply','message'=>'membalas komentarmu','created_at'=>now(),'updated_at'=>now()]);}
  if ($request->expectsJson()) {
   $comment->load('user');
   return response()->json(['html'=>view('community._comment',['comment'=>$comment,'post'=>$post,'depth'=>0])->render(),'id'=>$comment->id,'parent_id'=>$comment->parent_id,'count'=>DB::table('community_comments')->where('project_submission_id',$post->id)->count()],201);
  }
  return redirect()->route('community.show',$post)->with('status','Komentar terkirim.');
 }
 public function unreadCount(Request $request){return response()->json(['count'=>DB::table('community_notifications')->where('user_id',$request->user()->id)->whereNull('read_at')->count()]);}
 public function notifications(Request $request){$notifications=DB::table('community_notifications as n')->join('users as u','u.id','=','n.actor_id')->where('n.user_id',$request->user()->id)->select('n.*','u.name as actor_name')->latest('n.created_at')->paginate(20);return view('community.notifications',compact('notifications'));}
 public function readNotifications(Request $request){DB::table('community_notifications')->where('user_id',$request->user()->id)->whereNull('read_at')->update(['read_at'=>now()]);return back();}
}
