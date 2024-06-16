<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Doctor;
use App\Models\Graph;

class GraphController extends Controller
{
    public function index(){
        $doctors = Doctor::all();

        if(!$doctors){
            return response()->json(['status' => true, 'message' => 'На данный момент нет доступных врачей']);
        }

        return response()->json(['status' => true, 'message' => '', 'data' => $doctors]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'date_and_time' => [
                'required',
                'date_format:Y-m-d H:i',
                'after_or_equal:'.Carbon::tomorrow(),
                'before_or_equal:'.Carbon::tomorrow()->addDays(8),
                function ($attribute, $value, $fail){
                    $date = Carbon::parse($value);
                    if (($date->hour < 8) || ($date->hour > 20) || ($date->hour === 20 && $date->minute > 0)) {
                        $fail('Поле с датой и временем должно находится в диапазоне с 8 утра до 20 вечера включительно');
                    }
                }
            ],
            'doctor_id' => 'required|exists:doctors,id',
        ], [
            'date_and_time.required' => 'Поле с датой и временем обязательно для заполнения',
            'date_and_time.date_format' => 'Поле Дата и время должно содержать дату и время с точностью до минут',
            'date_and_time.after_or_equal' => 'Поле Дата и время должно быть после или равно завтрашней дате',
            'date_and_time.before_or_equal' => 'Поле Дата и время должно быть до или равно завтрашней дате на неделю вперед',
            'doctor_id.required' => 'Поле Врач обязательно для заполнения',
            'doctor_id.exists' => 'ID врача не существует'
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => 'Ошибка при создании даты и времени приёма', 'errors' => $validator->errors()], 422);
        }

        $graph = Graph::create($request->all());

        return response()->json(['status' => true, 'message' => 'Дата и время приёма успешно добавлена', 'data' => $graph]);
    }
}
