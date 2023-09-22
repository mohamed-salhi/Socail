<?php

namespace App\Http\Controllers\Api;

use App\Events\NotificationAdminEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Http\Resources\Login;
use App\Models\City;
use App\Models\Country;
use App\Models\FCM;
use App\Models\FcmToken;
use App\Models\Intro;
use App\Models\NotificationAdmin;
use App\Models\Package;
use App\Models\PackageUser;
use App\Models\User;
use App\Models\Setting;
use App\Models\Verification;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = [
            'mobile' => 'required|unique:users,mobile',
            'name' => 'required|string',
            'user' => 'required|string',
            'password' => 'required|min:7',
            'email' => 'required|email|unique:users,email',
            'gender' => 'required|in:1,2',
            'dateOfBirth' => 'required|date',


        ];
        $request->merge([
            'full_mobile' => str_replace('-', '', ($request->mobile)),
        ]);

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $password= Hash::make($request->password);
        $request['password']=$password;

        $user = User::query()->create($request->only('mobile', 'name', 'email', 'user', 'password', 'gender', 'dateOfBirth'));
        if ($user) {
//            $code = rand(1000, 9999);
//            $code = '1111';
//            Verification::query()->updateOrCreate([
//                'mobile' => $request->mobile,
//            ], [
//                'code' => Hash::make($code)
//            ]);
            $token = $user->createToken('api')->plainTextToken;
//            $user->setAttribute('token', $token);
//            $user = new Login($user);
            return mainResponse(true, __('ok'), $token, []);
        } else {
            return mainResponse(false, __('حصل خطا ما'), [], []);
        }

    }

    public function login(Request $request)
    {
        $rules = [
            'email' => 'required|exists:users,email',
            'password' => 'required|min:7',
            'fcm_token' => 'required',
            'fcm_device' => 'required|in:android,ios'

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user=User::query()->where('email',$request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('api')->plainTextToken;
            FcmToken::query()->create([
                "user_uuid" => $user->uuid,
                "fcm_device" => $request->fcm_device,
                "fcm_token" => $request->fcm_token
            ]);
        }

        return mainResponse(true, 'User Send successfully', $token, []);
    }

//    public function verifyCode(Request $request)
//    {
//        $rules = [
//            'mobile' => 'required_if:type,==,mobile|exists:users,mobile',
//            'email' => 'required_if:type,==,email|exists:users,email',
//            'type' => 'required|in:email,mobile',
//        ];
//        $validator = Validator::make($request->all(), $rules);
//        if ($validator->fails()) {
//            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
//        }
//        $item = Verification::query()->where('mobile', $request->mobile)->first();
//        if ($item && Hash::check($request->code, $item->code)) {
//            $user = User::query()->where('mobile', $request->mobile)->first();
////                $user->setAttribute('token', $user->createToken('api')->plainTextToken);
//            $token = $user->createToken('api')->plainTextToken;
//            FcmToken::query()->create([
//                "user_uuid" => $user->uuid,
//                "fcm_device" => $request->fcm_device,
//                "fcm_token" => $request->fcm_token
//            ]);
//            Verification::query()->where('mobile', $request->mobile)->delete();
//
//        } else {
//            return mainResponse(false, __('Code is not correct'), [], []);
//        }
//
//        $user->setAttribute('token', $token);
//        $user = new Login($user);
//
//        return mainResponse(true, __('ok'), $user, []);
//    }
//


    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        $user = Auth::guard('sanctum')->user();

        $user->fcm_tokens()->where('fcm_token', $request->fcm_token)->delete();
        if ($token === null) {
            $user->tokens()->delete();
        } else {
            $user->tokens()->where('id', $token)->delete();
        }
        return mainResponse(true, '', [], []);
    }


}
