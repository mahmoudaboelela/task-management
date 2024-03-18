<?php

namespace App\Models;

use App\Observers\TaskObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([TaskObserver::class])]

class Task extends Model
{

    protected $with = ['dependencies'];
    protected $fillable = ['title','description','due_date'];


    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by','id');
    }

    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies','task_id','dependent_task_id')->without('dependencies');
    }

    public function dependents()
    {
        return $this->belongsToMany(Task::class,'task_dependencies', 'dependent_task_id');
    }

    public function checkDependencies(): bool
    {
       foreach ($this->dependencies as $dependency){
           if ($dependency->status != "completed")
               return false;
       }
       return true;
    }
}
