<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CommunityComment extends Model {
 protected $fillable=['user_id','project_submission_id','parent_id','body'];
 public function user(){return $this->belongsTo(User::class);}
 public function replies(){return $this->hasMany(self::class,'parent_id')->with('user','replies')->oldest();}
}
