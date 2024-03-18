<?php

namespace App\Http\Controllers\User;

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




    public function index(Request $request)
    {
        $request->validate([
            'sort'=>"nullable|in:desc,asc",
            "filterByStatus"=>"nullable|in:pending,completed,canceled",
            "filterByDate"=>"nullable|array",
            "filterByDate.start"=>"date|nullable|required_with:filterByDate.end",
            "filterByDate.end"=>"date|nullable|required_with:filterByDate.start|after_or_equal:filterByDate.start",
        ]);
        $filterByStatus = $request->get('filterByStatus');
        $filterByDate = $request->get('filterByDate');
        $sortType = $request->get('sort')??"desc";
        $tasks = auth()->user()->tasks()->orderBy('due_date',$sortType)->whereNotNull("assignee_id")
            ->when($filterByStatus,function ($query,$filterByStatus){
                return $query->where("status",$filterByStatus);
            })
            ->when($filterByDate,function ($query,$filterByDate){
                return $query->whereBetween("due_date",$filterByDate);
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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $task = auth()->user()->tasks()->findOrFail($id);
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
            "dependency"=>"nullable|numeric|exists:tasks,id",
            "status"=>"required|in:pending,completed,cancelled"
        ]);
        $task = auth()->user()->tasks()->findOrFail($id);
        if ($request->has("dependency")){
            $task = $task->dependencies()->findOrFail($request->dependency);
        }

        $task->status = $request->status;

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
            return response()->json(["message"=>$exception->getMessage(),400]);
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
