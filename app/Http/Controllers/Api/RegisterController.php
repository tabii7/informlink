<?php
   
namespace App\Http\Controllers\API;
   
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
   
class RegisterController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $token =  $user->createToken('MyApp')->plainTextToken;

        $emailCode = rand(1000, 9999);
            $user->otp = $emailCode;
            $user->save();
            dispatch(new \App\Jobs\EmailVerificationJob($user, $emailCode));
            return response()->json([
                'data' => [
                    'message'=>'otp sended to the email',
                    'user' => new UserResource($user),
                    'token' => $token
                ]
            ], 200);
   
    }
   
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $token =  $user->createToken('MyApp')->plainTextToken; 

            $emailCode = rand(1000, 9999);
            $user->otp = $emailCode;
            $user->save();
            dispatch(new \App\Jobs\EmailVerificationJob($user, $emailCode));

            return response()->json([
            'data' => [
                'user' => new UserResource($user),
                'activity' => $user->activity ? $user->activity->log : '',
                'token' => $token
            ]
        ], 200);
   
        } 
        else{ 
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        } 
    }

    public function otp(Request $request){
        
        $user = User::where('email', $request->email)->first();
        if($request->otp == $user->otp){

            $user->email_verified_at = now();
            return response()->json([
                'data' => [
                    'message' => 'verified',
                    'user' => new UserResource($user),
                ]
            ], 200);

        }else{
            return response()->json([
                'data' => [
                    'message' => 'invalid otp',
                    'email' => $request->email,
                ]
            ], 204);
        }
    
        

    }
}