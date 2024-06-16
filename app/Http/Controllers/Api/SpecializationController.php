<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Specialization;

class SpecializationController extends Controller
{
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|min:4|regex:/^[\p{Cyrillic}А-Яа-я]*$/u'
        ], [
            'name.required' => 'Поле Название специализации обязательно для заполнения',
            'name.min' => 'Поле Название специализации должно иметь длину минимум в 4 символа',
            'name.regex' => 'Поле Название специализации должно содержать кириллицу',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => 'Ошибка при создании специализации', 'errors' => $validator->errors()], 422);
        }

        $specialization = Specialization::create($request->all());

        return response()->json(['status' => true, 'message' => 'Специализация успешно создана', 'data' => $specialization]);
    }
}
