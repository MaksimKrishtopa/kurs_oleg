<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Doctor;
use App\Models\Graph;
use App\Models\Specialization;

class RecordController extends Controller
{
    public function index(){

        $graphs = Graph::withTrashed()->get();

        if(!$graphs->isEmpty()){
            foreach($graphs as $graph){
                $date = Carbon::parse($graph->date_and_time);
                if($date <= Carbon::now()->addHours(7)){
                    $graph->forceDelete();
                }
            }
        }

        $specializations = Specialization::all();

        if(!$specializations){
            return response()->json(['status' => true, 'message' => 'На данный момент нет доступных специализаций врачей']);
        }

        if(!empty($_GET['specialization']) && !empty($_GET['doctor'])){
            $doctor_id = $_GET['doctor'];
            $graphs = Graph::where('doctor_id', $doctor_id)->get();

            if($graphs->isEmpty()){
                return response()->json(['status' => true, 'message' => 'У выбранного врача отсутсвуют свободные записи на приём']);
            }

            return response()->json(['status' => true, 'message' => '', 'data' => $graphs]);
        }

        if(!empty($_GET['specialization'])){
            $specialization_id = $_GET['specialization'];
            $specialization = Specialization::where('id', $specialization_id)->first();
            $doctors = Doctor::where('specialization_id', $specialization_id)->get();

            if(!$specialization){
                return response()->json(['status' => false, 'message' => 'Выбранной специализации врача не существует'], 404);
            }

            if($doctors->isEmpty()){
                return response()->json(['status' => true, 'message' => 'Врачи с выбранной специализацией отсутствуют']);
            }

            return response()->json(['status' => true, 'message' => '', 'data' => $doctors]);
        }

        return response()->json(['status' => true, 'message' => '', 'data' => $specializations]);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'specialization_id' => 'required|exists:specializations,id',
            'doctor_id' => 'required|exists:doctors,id',
            'graph_id' => 'required|exists:graphs,id',
        ], [
            'specialization_id.required' => 'Поле Специализация обязательно для заполнения',
            'specialization_id.exists' => 'ID специализации не существует',
            'doctor_id.required' => 'Поле Врач обязательно для заполнения',
            'doctor_id.exists' => 'ID врача не существует',
            'graph_id.required' => 'Поле Расписание обязательно для заполнения',
            'graph_id.exists' => 'ID расписания не существует'
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => 'Ошибка при записи на приём', 'errors' => $validator->errors()], 422);
        }

        $request['user_id'] = Auth::user()->id;

        $graph = Graph::where('id', $request['graph_id'])->first();

        if($graph === null){
            return response()->json(['status' => false, 'message' => 'Ошибка при записи на приём', 'errors' => ['graph_id' => ['ID расписания не существует']]], 422);
        }

        $record = Record::create($request->all());

        $graph->delete();

        return response()->json(['status' => true, 'message' => 'Вы успешно записаны на приём', 'data' => $record]);
    }
}
