<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Graph;
use App\Models\Specialization;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Models\Record;

class UserController extends Controller
{
    public function registration(Request $request){

        $validator = Validator::make($request->all(), [
            'surname' => 'required|string|min:4|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
            'name' => 'required|string|alpha|min:2|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
            'patronymic' => 'string|alpha|min:5|regex:/^[\p{Cyrillic}А-Яа-я]*$/u',
            'gender' => [
                'required',
                'string',
                Rule::in(['Мужской','Женский'])
            ],
            'date_of_birth' => 'required|date|before:'.Carbon::now()->subYears(18),
            'phone' => [
                'required',
                'regex:/^(8|7{1})([0-9]{10})?$/',
            ],
            'email' => 'required|email|unique:users',
            'password' => [
                'required',
                'min:6',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*?:;]/',
            ],
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
            'phone.required' => 'Поле Телефон обязательно для заполнения',
            'phone.regex' => 'Поле Телефон должно иметь длину в 11 символов и заполняться без знака: +',
            'email.required' => 'Поле Почта обязательно для заполнения',
            'email.email' => 'Поле Почта должно содержать валидный адрес эл. почты',
            'email.unique' => 'Введенная почта должна быть уникальной',
            'password.required' => 'Поле Пароль обязательно для заполнения',
            'password.min' => 'Поле Пароль должно быть длиной минимум в 6 символов',
            'password.regex' => 'Поле пароль должно содержать латинские прописные и строчные буквы, цифры и специальные символы !@#$%^&*?:;',
        ]);

        if($validator->fails()){
            return response()->json(['status' => false, 'message' => 'Ошибка при регистрации пользователя', 'errors' => $validator->errors()], 422);
        }

        User::create([
            "surname" => $request->surname,
            "name" => $request->name,
            "patronymic" => $request->patronymic,
            "gender" => $request->gender,
            "date_of_birth" => $request->date_of_birth,
            "phone" => $request->phone,
            "email" => $request->email,
            "password" => Hash::make($request->password),
        ]);

        return response()->json(['status' => true, 'message' => 'Пользователь успешно зарегистрирован']);
    }

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
        ], [
            'email.required' => 'Поле Почта обязательно для заполнения',
            'email.email' => 'Поле Почта должно содержать валидный адрес эл. почты',
            'password.required' => 'Поле Пароль обязательно для заполнения',
        ]);
        
        if($validator->fails()){
            return response()->json(['status' => false, 'message' => 'Ошибка при попытке авторизации', 'errors' => $validator->errors()], 422);
        }
        
        if(!Auth::attempt($request->all())){
            return response()->json(['status' => false, 'message' => 'Введенные данные не относятся к существующему аккаунту'], 401);
        }
        
        $user = Auth::user();
        
        $token = $user->createToken('token', ['*'], now()->addHours(7)->addMinutes(60))->plainTextToken;
        
        $cookie = cookie('jwt', $token);
        
        return response()->json(['status' => true, 'data' => $user, 'message' => 'Пользователь успешно авторизирован'])->withCookie($cookie);
    }
    

    public function logout(){

        Auth::user()->currentAccessToken()->delete();
        $cookie = Cookie::forget('jwt');

        return response()->json(['status' => true, 'message' => 'Пользователь успешно вышел из аккаунта'])->withCookie($cookie);
    }

    public function profile(){
        return response()->json(['status' => true, 'message' => '', 'data' => Auth::user()]);
    }

    public function records(){
        $records = Record::where('user_id', Auth::user()->id)->get();

        foreach($records as $record){
            $specialization_id = $record['specialization_id'];
            $doctor_id = $record['doctor_id'];
            $graph_id = $record['graph_id'];
            $record['specialization'] = Specialization::where('id', $specialization_id)->first();
            $record['doctor'] = Doctor::where('id', $doctor_id)->first();
            $record['graph'] = Graph::withTrashed()->where('id', $graph_id)->first();

            $date = Carbon::parse($record['graph']->date_and_time);
            if($date <= Carbon::now()->addHours(7)){
                $record['graph']->forceDelete();
            }
        }

        $records = Record::where('user_id', Auth::user()->id)->get();

        if($records->isEmpty()){
            return response()->json(['status' => true, 'message' => 'На данный момент у вас нету записей к врачам']);
        }

        return response()->json(['status' => true, 'message' => '', 'data' => $records]);
    }

    public function destroy($id){
        $record = Record::where('id', $id)->first();

        if(!$record){
            return response()->json(['status' => false, 'message' => 'Указанной записи к врачу для отмены не существует'], 404);
        }

        $graph = Graph::withTrashed()->where('id', $record['graph_id'])->first();

        $graph->restore();

        $record->delete();

        return response()->json(['status' => true, 'message' => 'Запись к врачу успешно отменена']);
    }
}
