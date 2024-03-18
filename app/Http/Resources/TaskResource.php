<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return array(
           'id'=>$this->id,
           'title'=>$this->title,
           'description'=>$this->description,
           'due_date'=>$this->due_date,
           'status'=>$this->status,
           'created_by'=>new UserResource($this->creator),
           'assignee'=> $this->assignee? new UserResource($this->assignee):null,
           'dependencies'=>TaskDependencyResource::collection($this->dependencies)
       );
    }
}
