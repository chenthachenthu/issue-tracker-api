<x-mail::message>
# Task Assigned

You have been assigned to the following task in **{{ $task->project->name }}**:

**Title:** {{ $task->title }}  
**Priority:** {{ ucfirst($task->priority) }}  
**Status:** {{ ucfirst($task->status) }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
