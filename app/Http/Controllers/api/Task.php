<?php

namespace App\Http\Controllers\api;

use Illuminate\Support\Facades\DB;

use App\Http\Resources\TaskResource;

use App\Mail\TaskAssignedMail;

use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

use Illuminate\Http\Request;

use App\Models\Task as TaskModel;

use App\Models\Project as ProjectModel;

class Task extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, ProjectModel $project)
    {
        $tasks = $project->tasks()
            ->with('user')
            ->filter($request->only(['status','priority','search']))
            ->latest()
            ->paginate(20);

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest  $request, ProjectModel $project)
    {

        $this->authorize('view',$project);

        $task = DB::transaction(function() use($request,$project){

        $newtask = $project->tasks()->create([

                ...$request->validated(),
                'user_id'=>$request->user()->id

        ]);

        $newtask->activitylog()->create([

        'user_id' => $request->user()->id,
        'action' => 'task created',
        'details' => 'task created in project'.$project->name

        ]);

            return  $newtask;

        });


        Mail::to($request->user()->email)->send(new TaskAssignedMail($task));
       

        return (new TaskResource($task))->response()->setStatusCode(201);

    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectModel $project ,TaskModel $task)
    {
        $this->authorize('view',$task);

        $task->load(['user','project']);

        return new TaskResource($task);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request,ProjectModel $project,TaskModel $task)
    {
        $this->authorize('update',$task);

        $oldstatus=$task->status;

        $task = DB::transaction(function() use($request,$task,$oldstatus){

            $task->update($request->validated());

            if($request->has('status')&&$request->status!==$oldstatus){

                    $task->activitylog()->create([

                        'user_id'=>$request->user()->id,
                        'action' =>$request->status,
                        'details'=>'status changed from '.$oldstatus.' to '.$request->status


                    ]);

                    
            }

            return $task;


        });
        
        
        return new TaskResource($task->load(['user','project']));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectModel $project,TaskModel $task)
    {
        $this->authorize('delete',$task);
        
        $task->delete();

        return response()->noContent();
    }
}
