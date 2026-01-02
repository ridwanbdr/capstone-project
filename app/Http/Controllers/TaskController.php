<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Routing\Controller;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Route middleware handles admin-only access for create, store, destroy
        // updateStatus is available to all authenticated users (checked in method)
    }
    
    // ...existing code...

    /**
     * Display a listing of tasks.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            // Admin sees all tasks
            $tasks = Task::with(['assignedBy', 'assignedTo'])->orderBy('created_at', 'desc')->paginate(10);
        } else {
            // Other users see only their assigned tasks
            $tasks = Task::where('assigned_to', $user->id)
                ->with(['assignedBy', 'assignedTo'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new task (Admin only).
     */
    public function create()
    {
        $users = User::where('role', '!=', 'admin')
            ->where('id', '!=', Auth::id())
            ->get();

        return view('tasks.create', compact('users'));
    }

    /**
     * Store a newly created task (Admin only).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'due_date' => 'nullable|date|after:today',
        ]);

        $task = Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'assigned_by' => Auth::id(),
            'assigned_to' => $validated['assigned_to'],
            'priority' => $validated['priority'],
            'due_date' => $validated['due_date'] ? Carbon::parse($validated['due_date']) : null,
            'status' => 'pending',
        ]);

        // Create notification
        Notification::create([
            'user_id' => $validated['assigned_to'],
            'type' => 'task_assigned',
            'title' => 'Task Baru Ditetapkan',
            'message' => "Anda telah ditugaskan untuk: {$validated['title']}",
            'task_id' => $task->id,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task berhasil dibuat dan ditugaskan.');
    }

    /**
     * Display the specified task.
     */
    public function show(Task $task)
    {
        $user = Auth::user();
        
        // Only assigned user or admin can view
        if ($user->role !== 'admin' && $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $task->load(['assignedBy', 'assignedTo']);

        return view('tasks.show', compact('task'));
    }

    /**
     * Update task status.
     */
    public function updateStatus(Request $request, Task $task)
    {
        $user = Auth::user();

        // Only assigned user or admin can update status
        if ($user->role !== 'admin' && $task->assigned_to !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled',
        ], [
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        $oldStatus = $task->status;
        $task->update(['status' => $validated['status']]);

        if ($validated['status'] === 'completed') {
            $task->update(['completed_at' => now()]);

            // Notify admin
            $userName = $user->nama_lengkap ?? $user->name;
            Notification::create([
                'user_id' => $task->assigned_by,
                'type' => 'task_completed',
                'title' => 'Task Selesai',
                'message' => "Task '{$task->title}' telah diselesaikan oleh $userName",
                'task_id' => $task->id,
            ]);

            return redirect()->route('tasks.show', $task)->with('success', 'Task berhasil ditandai sebagai selesai.');
        } elseif ($oldStatus !== $validated['status']) {
            // Notify admin of status change
            $statusLabel = str_replace('_', ' ', $validated['status']);
            Notification::create([
                'user_id' => $task->assigned_by,
                'type' => 'task_status_changed',
                'title' => 'Status Task Berubah',
                'message' => "Task '{$task->title}' status berubah menjadi: " . ucfirst($statusLabel),
                'task_id' => $task->id,
            ]);
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Status task berhasil diperbarui.');
    }

    /**
     * Remove the specified task (Admin only).
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task berhasil dihapus.');
    }
}

