<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::user()->id;

        $tasks = Task::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->with('user')
            ->get();

        // return response()->json(['data' => $tasks]);
        return $this->success($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        return $this->success($task, 'Task created successfully', 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        // Make sure the user gets only their tasks
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $this->success($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        // Make sure the user updates only their tasks
        if (Auth::user()->id !== $task->user_id) {
            return $this->error([], 'You are not authorized', 403);
        }
        $task->update($request->all());

        return $this->success($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) :  $task->delete();
    }

    private function isNotAuthorized($task)
    {
        if (Auth::user()->id !== $task->user_id) {
            return $this->error([], 'You are not authorized', 403);
        }
    }
}
