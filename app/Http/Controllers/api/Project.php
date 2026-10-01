<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project As ProjectModel;

use App\Http\Resources\ProjectResource;

use App\Http\Requests\StoreProjectRequest;

use App\Http\Requests\UpdateProjectRequest;

class Project extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $data = $request->user()->projects()->withCount('tasks')->latest()->paginate(20);

        return ProjectResource::collection($data);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request,ProjectModel $project)
    {

        
        $project = $request->user()->projects()->create($request->validated());

        return (new ProjectResource($project))->response()->setStatusCode(201);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectModel $project)
    {

        $this->authorize('view',$project);

        return new ProjectResource($project->loadCount('tasks'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, ProjectModel $project)
    {

        $this->authorize('update',$project);
        
        

        $project->update($request->validated());

        return new ProjectResource($project);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectModel $project)
    {

        $this->authorize('delete',$project);

        $project->delete();

        return response()->noContent();
        
    }
}
