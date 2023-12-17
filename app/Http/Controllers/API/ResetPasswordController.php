<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    public function sendEmail(Request $request)
    {
        if(! $this->validEmail($request->email)){
            return $this->failedResponse('E-Mail doesn\'t exist!.');
        }

        $this->send($request->email);
        $this->successResponse('Reset E-Mail sent successfully. Please check your inbox.');
    }

    public function validEmail($email)
    {
        return !!User::where('email', '=', $email)->first();
    }

    public function send($email)
    {
        $token = $this->createToken($email);
        Mail::to($email)->send(new ResetPasswordMail($token));
    }

    public function createToken($email)
    {
        $oldToken = DB::table('password_resets')->where('email', $email)->first();
        if($oldToken){
            return $oldToken->token;
        }
        $token = rand(1000, 9999);
        $this->saveToken($token, $email);
        return $token;
    }

    public function saveToken($token, $email)
    {
        DB::table('password_resets')->insert([
            'email' => $email,
            'token' => $token,
            'created_at' => Carbon::now()
        ]);
    }

    public function verifyToken(Request $request)
    {
        return $this->validToken($request->email, $request->code) ? $this->successResponse('Successfull')
                : $this->tokenNotFoundResponse('Verification Code not Found!');
    }

    public function validToken($email, $token)
    {
        $tokenInfo = DB::table('password_resets')->where(['email' => $email, 'token' => $token])->count();
        if($tokenInfo > 0){
            return true;
        }
        return false;
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|exists:users,email',
            'password' => 'required|max:20'
        ], $messages = [
            'email.required' => 'Provide a valid E-Mail Address',
            'email.exists' => 'E-Mail not Available',
            'password.required' => 'Please provide the new password',
            'password.max' => 'Password is too long!.',
        ]);

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => bcrypt($request->password)]);
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json([
            'data' => 'Password Changed Successfully'
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function successResponse($message)
    {
        return response()->json([
            'data' => $message
        ], Response::HTTP_OK);
    }

    public function failedResponse($message)
    {
        return response()->json([
            'error' => $message
        ], Response::HTTP_NOT_FOUND);
    }

    public function tokenNotFoundResponse($message)
    {
        return response()->json([
            'error' => $message
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
