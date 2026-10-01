<?php

namespace App\Http\Controllers\api;

use App\Http\Resources\AttachmentResource;

use App\Http\Requests\StoreAttachmentRequest;


use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\Task as Task;

use App\Models\Project as Project;

use App\Models\Attachment as Attachment;



class AttachmentController extends Controller
{
    

    public function store(StoreAttachmentRequest $request,Project $project,Task $task){

        $this->authorize('update',$task);

        $uploadedFile = $request->file('file');

        $path = $uploadedFile->store('attachments','public');

        $attachment = $task->attachments()->create([

        'user_id' => $request->user()->id,
        'name' => $uploadedFile->getClientOriginalName(),
        'file_path' => $path,
        'mime_type' => $uploadedFile->getClientMimeType(),
        'size' => $uploadedFile->getSize(),

        ]);


        return (new AttachmentResource($attachment))->response()->setStatusCode(201);


    }


    public function download(Project $project,Task $task,Attachment $attachment){

        $this->authorize('view',$task);

        abort_if($attachment->task_id !== $task->id,404);

        if(!Storage::disk('public')->exists($attachment->file_path)){

            abort(404,'file not found');

        }

        return Storage::disk('public')->download(

        $attachment->file_path,
        $attachment->name

        );


    }



    public function destroy(Project $project,Task $task,Attachment $attachment){

        $this->authorize('update',$task);

        abort_if($attachment->task_id!==$task->id,404);

        if(Storage::disk('public')->exists($attachment->file_path)){

            Storage::disk('public')->delete($attachment->file_path);
            

        }

        $attachment->delete();

        return response()->noContent();
        


    }



}
