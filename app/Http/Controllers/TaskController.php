<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Task;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        request()->validate([
            'task' => 'required|mimes:pdf,doc,docx,pptx,xlsx,zip|max:2048',
        ]);

        $check_task = Task::where('lesson_id', $request->lesson_id)->where('student_id', auth()->user()->id)->first();
        if ($check_task) {
            Alert::error('Error', __("Siz bu mavzu vazifasini avval yuklagansiz"));
            return redirect()->back();
        } else {
            $task = new Task();

            $task1 = $request->file('task');
            $task1_name = time() . $task1->getClientOriginalName();
            $task1->move('uploads/tasks', $task1_name);

            $task->task = $task1_name;
            $task->lesson_id = $request->lesson_id;
            $task->student_id = auth()->user()->id;
            $task->save();
            Alert::success('Success', __("Vazifa muvaffaqiyatli yuklandi"));
            return redirect()->back();
        }

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        //
    }

    public function taskDownload($task_id)
    {
        $task = Task::find($task_id);
        return response()->download(public_path('uploads/tasks/' . $task->task));
    }

    public function check_save(Request $request)
    {

        $task = Task::findorFail($request->task_id);
        $task->baho = $request->task_baho;
        $task->save();
        Alert::success('Success', __("Baho muvaffaqiyatli qo'yildi"));
        return redirect()->back();


    }

public function get_sertificate()
    {
        $tasks = Task::where('student_id', auth()->user()->id)->where('baho', '>=', 60)->get();
        dd($tasks);
        if (count($tasks) < 3) {
            Alert::error('Error', __("messages.sertificate_error"));
            return redirect()->back();
        }
        return view('students.sertificate', compact('tasks'));
    }
}
