<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use http\Exception\InvalidArgumentException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{

    /**
     * Display a listing of the resource.
     */



    public function index(Request $request)
    {
        $request->validate([
            'sort'=>"nullable|in:desc,asc",
            "filterByStatus"=>"nullable|in:pending,completed,canceled",
            "filterByDate"=>"nullable|array",
            "filterByDate.start"=>"date|nullable|required_with:filterByDate.end",
            "filterByDate.end"=>"date|nullable|required_with:filterByDate.start|after_or_equal:filterByDate.start",
            "filterByAssignee"=>"nullable|numeric|gt:0|exists:users,id",
        ]);
        $filterByStatus = $request->get('filterByStatus');
        $filterByDate = $request->get('filterByDate');
        $filterByAssignee = $request->get('filterByAssignee');
        $sortType = $request->get('sort')??"desc";
        $tasks = Task::orderBy('due_date',$sortType)->whereNotNull("assignee_id")
            ->when($filterByStatus,function ($query,$filterByStatus){
                return $query->where("status",$filterByStatus);
            })
            ->when($filterByDate,function ($query,$filterByDate){
                return $query->whereBetween("due_date",$filterByDate);
            })
            ->when($filterByAssignee,function ($query,$filterByAssignee){
                return $query->whereHas("assignee",function ($q) use($filterByAssignee){
                    $q->where("id",$filterByAssignee);
                });
            })
            ->get();
        return response()->json(TaskResource::collection($tasks));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "title"=>"required|string",
            "description"=>"required|string",
            "assignee"=>"required|exists:users,id",
            "due_date"=>"required|date|after_or_equal:today",
            "dependencies" => "nullable|array",
            "dependencies.*.title"=>"required|string",
            "dependencies.*.description"=>"required|string",
        ]);
        $user = User::find($request->assignee);
        $task = new Task();
        $task->title = $request->title;
        $task->description = $request->description;
        $task->due_date = $request->due_date;
        $task->assignee()->associate($user);

        DB::beginTransaction();
        try {
            $task->save();
            if (count($request->dependencies) > 0){
                foreach ($request->dependencies as $dependency){
                    $task->dependencies()->create($dependency);
                }
            }
            DB::commit();

        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(["message"=>$exception->getMessage()]);
        }
        return response()->json(["message"=>"Task created."]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = Task::findOrFail($id);
        return response()->json(new TaskResource($task));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            "title"=>"required|string",
            "description"=>"required|string",
            "assignee"=>"required|exists:users,id",
            "due_date"=>"required|date|after_or_equal:today",
            "status"=>"required|in:pending,completed,cancelled"
        ]);
        $task = Task::findOrFail($id);
        $user = User::find($request->assignee);
        $task->title = $request->title;
        $task->description = $request->description;
        $task->due_date = $request->due_date;
        $task->status = $request->status;
        $task->assignee()->associate($user);

        DB::beginTransaction();
        try {
            if ($task->isDirty("status")){
                if ($task->getDirty()['status'] == "completed" && !$task->checkDependencies())
                   return response()->json(["message"=>"Incomplete dependency."],400);
            }
            $task->save();
            DB::commit();

        }catch (\Exception $exception){
            DB::rollBack();
            return response()->json(["message"=>$exception->getMessage()],400);
        }
        return response()->json(["message"=>"Task updated."]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
