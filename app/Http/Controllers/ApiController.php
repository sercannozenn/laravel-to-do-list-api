<?php

namespace App\Http\Controllers;

use App\Models\TaskList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->email;
        $password = $request->password;

        if (Auth::attempt(['email' => $email, 'password' => $password]))
        {
            $user = Auth::user();
            $success['token'] = $user->createToken("Login")->accessToken;
            return response()->json([
                'success' => $success
            ], 200);
        }
        else
        {
            return response()->json(['error' => 'Unauthorised'], 401);
        }

    }

    public function addTask(Request $request)
    {
        $taskName = $request->task_name;
        $userName = Auth::user()->name;
        if ($taskName)
        {
            TaskList::create([
                'task_name' => $taskName,
                'task_status' => 0,
                'task_user' => $userName
            ]);

            $userTaskList = TaskList::query()
                ->where('task_user', $userName)
                ->get();

            return response()->json(['data' => $userTaskList], 200);
        }
        else
        {
            return response()->json(['error' => "Yanlış istekte bulundun."], 400);
        }

    }

    public function editTask(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $taskName = $request->task_name;

        $task = TaskList::where('id', $id)->where('task_user', $user->name)->first();

        if ($task)
        {
            $task->task_name = $taskName;
            $task->save();

            $userTaskList = TaskList::query()
                ->where('task_user', $user->name)
                ->get();
            return response()->json(['message' => 'Güncelleme İşlemi Başarılı', 'data' => $userTaskList], 200);
        }
        else
        {
            return response()->json(['message' => $id . ' ID ile eşleşen bir kayıt bulunamadı.'], 200);
        }

    }

    public function deleteTask(Request $request)
    {
        $id = $request->id;
        $user = Auth::user();

        $task = TaskList::where('id', $id)->where('task_user', $user->name)->first();

        if ($task)
        {
            $task->delete();
            $userTaskList = TaskList::query()
                ->where('task_user', $user->name)
                ->get();
            return response()->json(['message' => 'Silme İşlemi Başarılı', 'data' => $userTaskList], 200);
        }
        else
        {
            return response()->json(['message' => $id . ' numaralı ID ile eşleşen bir kayıt bulunamadı.'], 200);
        }
    }

    public function clearTaskList(Request $request)
    {
        $user = Auth::user();

        TaskList::where('task_user', $user->name)->where('task_status', 1)->delete();
        $userTaskList = TaskList::query()
            ->where('task_user', $user->name)
            ->get();
        return response()->json(['message' => 'Tüm Tamamlanmış Görevler silindi.', 'data' => $userTaskList], 200);

    }

    public function completedTask(Request $request)
    {
        $completedTaskList = $request->completedTaskList;
        $user = Auth::user();

        if (gettype($completedTaskList) == 'array')
        {
            $list= TaskList::where('task_user', $user->name)->whereIn('id', $completedTaskList)->update([
                'task_status' => 1
            ]);
            $userTaskList = TaskList::query()
                ->where('task_user', $user->name)
                ->get();

            return response()->json(['message' => 'Görev Tamamlama İşlemi Başarılı', 'data' => $userTaskList], 200);
        }
        else
        {
            return response()->json(['error' => "Yanlış istekte bulundun."], 400);
        }


    }

    public function undoCompletedTask(Request $request)
    {
        $undoCompletedTaskList = $request->undoCompletedTaskList;
        $user = Auth::user();
        if (gettype($undoCompletedTaskList) == 'array')
        {
            TaskList::where('task_user', $user->name)->whereIn('id', $undoCompletedTaskList)->update([
                'task_status' => 0
            ]);
            $userTaskList = TaskList::query()
                ->where('task_user', $user->name)
                ->get();

            return response()->json(['message' => 'Tamamlananlar Görevleri Geri Alma İşlemi Başarılı', 'data' => $userTaskList], 200);
        }
        else
        {
            return response()->json(['error' => "Yanlış istekte bulundun."], 400);
        }

    }
}
