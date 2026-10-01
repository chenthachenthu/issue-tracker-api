<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Task extends Model
{
    use HasFactory;

    protected $fillable =[

    'user_id',
    'project_id',
    'title',
    'description',
    'status',
    'priority',
    'due_date'

    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project(){

        return $this->belongsTo(Project::class);

    }

    public function activitylog(){

        return $this->hasMany(Activitylog::class);

    }

    public function attachments(){

        return $this->hasMany(Attachment::class);

    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['status'] ?? false, function ($query, $status) {
            $query->where('status', $status);
        });

        $query->when($filters['priority'] ?? false, function ($query, $priority) {
            $query->where('priority', $priority);
        });

        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        });
    }

}
