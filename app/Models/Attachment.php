<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;


    protected $fillable = [

    'task_id',
    'user_id',
    'name',
    'file_path',
    'mime_type',
    'size'
    ];



    public function user(){

    return $this->belongsTo(User::class);

    }

    public function task(){

    return $this->belongsTo(Task::class);

    }



}
