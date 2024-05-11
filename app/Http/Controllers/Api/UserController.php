<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Passport;

class UserController extends Controller
{
   //function to create user
    public function createUser (Request $request) {

        $validator = Validator:: make($request->all(), [
            'name' => "required | string",
            'email' => "required | string| unique:users",
            'phone' => "required | numeric|digits:10",
            'password' => "required|min:4"
            ]);
            if($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occured",
            'error_message' => $validator->errors());
            return response()->json($result, 400); 
            }

        $user=User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'password'=>$request->password,
        ]);

        if($user->id) {
            $result = array('status' => true, 'message'=> "User created", "data" => $user);
            $responseCode = 200;
            }
            else {
            $result = array('status' => false, 'message'=>  "Something went wrong");
            $responseCode = 404;
            }
            return response()->json($result, $responseCode);

    // return response()->json(['status' => true, 'message' => "Hello world", 'data'=>$request->all()]);
    }
    #function to return all users
    public function getUsers() {
        $users = User::all();
        try{
        $result = array('status' => true, 'message' => count($users). " user(s) fetched", "data" => $users);
        $responseCode = 200; // Success
        return response()->json($result, $responseCode);
        }
        catch(Exception $e) {
            $result = array('status' => false, 'message' => "API failed due to an error",
            "error" => $e->getMessage());
            return response()->json($result, 500);
        }
        
    }
    # function to return all users
    public function getUserDetail($id) {
        $user = User::find($id);
        if(!$user) {
            return response()->json(['status'=> false, 'message'=> "User not found"], 404);
        }
        $result = array('status' => true, 'message' => "User found", "data" => $user); 
        $responseCode = 200; // Success
        return response()->json($result, $responseCode);
    }
    # function to update user
    public function updateUser (Request $request, $id) {
        $user = User::find($id);
        if(!$user) {
            return response()->json(['status' => false, 'message' => "User not found"], 404);
        }
        // Validation
        $validator = Validator:: make($request->all(), [
            'name' => "required | string",
            'email' => "required|string|unique:users,email,".$id,
            'phone' => "required| numeric|digits:10"
            ]);
            if($validator->fails()) {
                $result = array('status' => false, 'message' => "Validation error occurred",
                'error_message' => $validator->errors());
                return response()->json($result, 400); // Bad Request
            }
            // Update code
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            $result = array('status' => true, 'message'=> "User updated", "data" => $user);
            $responseCode = 200;
            return response()->json($result, $responseCode);
    }
    #function to delete user
    public function deleteUser($id){
        $user = User::find($id);
        if(!$user) {
            return response()->json(['status' => false, 'message' => "User not found"], 404);
        }
        $user->delete();
        $result = array('status' => true, 'message' => "User has been deleted successfully");
        return response()->json($result, 200);
    }
    public function login(Request $request){
        
        $validator = Validator:: make($request->all(), [
            'email' => "required | string",
            'password' => "required|min:4"
            ]);
            if($validator->fails()) {
            $result = array('status' => false, 'message' => "Validation error occured",
            'error_message' => $validator->errors());
            return response()->json($result, 400); 
            }

            $credentials = $request->only("email", "password");
            if(Auth:: attempt($credentials)){
            $user = Auth::user();
            $token= $user->createToken('MyApp')->accessToken;
            return response()->json(['status' => true, "message" => "Login successful", "token" => $token], 200);
            }
            return response()->json(['status' => false, "message" => "Invalid login credentials"], 401);
    }
    public function unauthenticate()
    {
        return response()->json(['status' => false, 'message'=> "Only authorised user can access", "error" => "unauthenticate"], 401);
    }
    #function to logout user
    public function logout() {
        $user = Auth:: user();
        /*$user->tokens->each(function ($token, $key) {
        $token->delete();
        });*/
        return response()->json(['status' => true, 'message' => 'Logged out successfully', 'data' => $user], 200);
    }
}

