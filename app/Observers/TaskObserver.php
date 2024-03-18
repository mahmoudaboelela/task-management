<?php

namespace App\Observers;

use App\Models\Task;
use http\Exception\InvalidArgumentException;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function creating(Task $task)
    {
      $task->creator()->associate(auth()->user());
    }

    public function saving(Task $task)
    {

    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
