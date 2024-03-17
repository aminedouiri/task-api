<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Events\TaskCreatedEvent;
use App\Events\TaskDeletedEvent;
use App\Events\TaskUpdatedEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ListTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;

class TaskController extends Controller
{
    public function store(StoreTaskRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $data = $request->validated();
            $data['user_id'] = $user->id;
            $task = Task::create($data);
            broadcast(new TaskCreatedEvent($user , $task));
            DB::commit();
            Log::info('Task registered successfully.', ['task' => $task->title]);
            return $this->sendSuccessResponse($task, 'Task registered successfully!', 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Task registration failed.', ['error' => $e->getMessage()]);
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function index(ListTaskRequest $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $search = $request->input('search', '');
            $tasks = Task::with('user');
            $tasks = $tasks->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhere('due_date', 'like', '%'.$search.'%');
            $totalTasks = $tasks->count();
            $paginator = $tasks->paginate($perPage, ['*'], 'page', $page);
            $currentPage = $paginator->currentPage();
            $totalPages = $paginator->lastPage();
            DB::commit();
            Log::info('Task list retrieved successfully.');
            return $this->sendSuccessResponse([
                'tasks' => TaskResource::collection($paginator),
                'current_page' => $currentPage,
                'total_pages' => $totalPages,
            ], 'Task list retrieved successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Task list retrieved failed.', ['error' => $e->getMessage()]);
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function show(int $id)
    {
        try {
            $user = Auth::user();
            $task = Task::find($id);
            if(!isset($task)) {
                DB::commit();
                Log::warning('Task not found');
                return $this->sendErrorResponse('Task not found', 404);
            }
            Log::info('Task retreived successfully.', ['task' => $task->titre]);
            return $this->sendSuccessResponse(new TaskResource($task), 'Task retreived successfully!');
        } catch (Exception $e) {
            Log::error('Task registration failed.', ['error' => $e->getMessage()]);
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $task = Task::find($id);
            $data = $request->validated();
            $user = Auth::user();
            if(!isset($task)) {
                DB::commit();
                Log::warning('Task not found');
                return $this->sendErrorResponse('Task not found', 404);
            }
            $task->update($data);
            broadcast(new TaskUpdatedEvent($user , $task));
            DB::commit();
            Log::info('Task updated successfully.', ['task' => $task->title]);
            return $this->sendSuccessResponse($task, 'Task updated successfully!', 201);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Task updated failed.', ['error' => $e->getMessage()]);
            return $this->sendErrorResponse($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $task = Task::find($id);
            if(!isset($task)) {
                DB::commit();
                Log::warning('Task not found');
                return $this->sendErrorResponse('Task not found', 404);
            }
            $task->delete();
            broadcast(new TaskDeletedEvent($user, $task));
            DB::commit();
            Log::info('Task deleted successfully.');
            return $this->sendSuccessResponse($task, 'Task deleted successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Task deleted failed.', ['error' => $e->getMessage()]);
            return $this->sendErrorResponse($e->getMessage());
        }
    }
}
