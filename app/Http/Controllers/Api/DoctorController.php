<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

use App\Models\Doctor;

class DoctorController extends Controller
{
    public function index(){
        $doctors = Doctor::all();

        if(!$doctors){
            return response()->json(['status' => true, 'message' => 'На данный момент нет доступных врачей']);
        }

        foreach($doctors as $doctor){
            $specialization_id = $doctor['specialization_id'];
            $doctor['specialization'] = Specialization::where('id', $specialization_id)->first();
        }

        return response()->json(['status' => true, 'message' => '', 'data' => $doctors]);
    }

    public function showSpecializations()
    {
        $specializations = Specialization::all();

        if(!$specializations){
            return response()->json(['status' => true, 'message' => 'На данный момент нету доступных специализаций']);
        }

        return response()->json(['status' => true, 'message' => '', 'data' => $specializations]);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'surname' => 'required|string|min:4|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
            'name' => 'required|string|min:2|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
            'patronymic' => 'string|min:5|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
            'gender' => [
                'required',
                'string',
                Rule::in(['Мужской','Женский'])
            ],
            'date_of_birth' => 'required|date|before:'.Carbon::now()->subYears(18),
            'specialization_id' => 'required|exists:specializations,id'
        ], [
            'surname.required' => 'Поле Фамилия обязательно для заполнения',
            'surname.min' => 'Поле Фамилия должно иметь длину минимум в 4 символа',
            'surname.regex' => 'Поле Фамилия должно содержать кириллицу',
            'name.required' => 'Поле Имя обязательно для заполнения',
            'name.min' => 'Поле Имя должно иметь длину минимум в 2 символа',
            'name.regex' => 'Поле Имя должно содержать кириллицу',
            'patronymic.min' => 'Поле Имя должно иметь длину минимум в 5 символов',
            'patronymic.regex' => 'Поле Отчество должно содержать кириллицу',
            'gender.required' => 'Поле Пол обязательно для заполнения',
            'gender.in' => 'Поле Пол не может содержать данные кроме: Мужской, Женский',
            'date_of_birth.required' => 'Поле Дата рождения обязательно для заполнения',
            'date_of_birth.date' => 'Поле Дата рождения должно содержать дату',
            'date_of_birth.before' => 'Пользователь должен быть не младше 18 лет',
            'specialization_id.required' => 'Поле Специализация обязательно для заполнения',
            'specialization_id.exists' => 'ID специализации не существует'
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => 'Ошибка при добавлении врача', 'errors' => $validator->errors()], 422);
        }

        $doctor = Doctor::create($request->all());

        return response()->json(['status' => true, 'message' => 'Врач успешно добавлен', 'data' => $doctor]);
    }

    public function showDoctor($id){
        $doctor = Doctor::find($id);

        return response()->json(['status' => true, 'message' => '', 'data' => $doctor]);
    }

    public function update(Request $request, $id){

        $doctor = Doctor::find($id);

        if(!$doctor){
            return response()->json(['status' => false, 'message' => 'Указанного врача не существует'], 404);
        }
        else{
            $input = $request->all();

            $validator = Validator::make($input, [
                'surname' => 'required|string|min:4|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
                'name' => 'required|string|min:2|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
                'patronymic' => 'string|min:5|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
                'gender' => [
                    'required',
                    'string',
                    Rule::in(['Мужской','Женский'])
                ],
                'date_of_birth' => 'required|date|before:'.Carbon::now()->subYears(18),
                'specialization_id' => 'required|exists:specializations,id'
            ], [
                'surname.required' => 'Поле Фамилия обязательно для заполнения',
                'surname.min' => 'Поле Фамилия должно иметь длину минимум в 4 символа',
                'surname.regex' => 'Поле Фамилия должно содержать кириллицу',
                'name.required' => 'Поле Имя обязательно для заполнения',
                'name.min' => 'Поле Имя должно иметь длину минимум в 2 символа',
                'name.regex' => 'Поле Имя должно содержать кириллицу',
                'patronymic.min' => 'Поле Имя должно иметь длину минимум в 5 символов',
                'patronymic.regex' => 'Поле Отчество должно содержать кириллицу',
                'gender.required' => 'Поле Пол обязательно для заполнения',
                'gender.in' => 'Поле Пол не может содержать данные кроме: Мужской, Женский',
                'date_of_birth.required' => 'Поле Дата рождения обязательно для заполнения',
                'date_of_birth.date' => 'Поле Дата рождения должно содержать дату',
                'date_of_birth.before' => 'Пользователь должен быть не младше 18 лет',
                'specialization_id.required' => 'Поле Специализация обязательно для заполнения',
                'specialization_id.exists' => 'ID специализации не существует'
            ]);

            if($validator->fails()){
                return response()->json(['status' => false, 'message' => 'Ошибка при изменении данных врача', 'errors' => $validator->errors()], 422);
            }

            $doctor->surname = $input['surname'];
            $doctor->name = $input['name'];
            $doctor->patronymic = isset($input['patronymic']) ? $input['patronymic']: null;
            $doctor->gender = $input['gender'];
            $doctor->date_of_birth = $input['date_of_birth'];
            $doctor->specialization_id = $input['specialization_id'];
            $doctor->save();

            return response()->json(['status' => true, 'message' => 'Данные врача успешно изменены', 'data' => $doctor]);
        }
    }
}
