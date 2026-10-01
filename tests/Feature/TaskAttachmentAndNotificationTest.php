<?php

namespace Tests\Feature;

use App\Mail\TaskAssignedMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;

use App\Models\User;

use App\Models\Project;

use App\Models\Task;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

class TaskAttachmentAndNotificationTest extends TestCase
{
    
    use RefreshDatabase;

    /**
     * Test that creating a task queues an assignment email.
     */

    public function test_creating_task_queues_assigned_email(): void
    {

        // 1. Tell Laravel to fake the Mail facade

        Mail::fake();

        // 2. Arrange: Create user & project

        $user = User::factory()->create();

        $project = Project::factory()->create(['user_id' => $user->id]);

        // Authenticate as this user

        Sanctum::actingAs($user);

        // 3. Act: Send POST request to create task

        $response = $this->postJson("/api/projects/{$project->id}/tasks",[

            'title'       => 'Automated Test Task',
            'status'      => 'pending',
            'priority'    => 'high',
            'description' => 'Testing email queue dispatch'


        ]);

        // 4. Assert: Check response status

        $response->assertCreated();

        // Assert that the email was queued to the user's address

        Mail::assertQueued(TaskAssignedMail::class, function($mail) use ($user){

        return $mail->hasTo($user->email);
        

        });



    }


    public function test_user_can_upload_attachment_to_task(): void
    {

    // 1. Tell Laravel to fake the public storage disk

    Storage::fake('public');

    // 2. Arrange: Create user, project, and task

    $user = User::factory()->create();

    $project = Project::factory()->create(['user_id' => $user->id]);


    $task = Task::factory()->create(['project_id' => $project->id,'user_id' => $user->id]);

    Sanctum::actingAs($user);

    // Create a fake 500KB fake PNG image

    $file = UploadedFile::fake()->image('error_screenshot.png');

    // 3. Act: Upload the file

    $response = $this->postJson("/api/projects/{$project->id}/tasks/{$task->id}/attachments", [
        'file' => $file,
    ]);

    // 4. Assert: Status is 201 Created
        $response->assertCreated();

    // Assert record exists in the database
        $this->assertDatabaseHas('attachments', [
            'task_id'       => $task->id,
            'name' => 'error_screenshot.png',
        ]);

        // Assert file exists on the fake disk
        $attachment = $task->attachments()->first();
        
        Storage::disk('public')->assertExists($attachment->file_path);

    }




}
