<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Message;
use App\Events\GotMessage;
use App\Jobs\SendMessage;



Route::get('/user', function (Request $request) {
    return response()->json(['user'=>$request->user()],200);

})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function(){
    Route::get('/user',function (Request $request){
        return response()->json(['user'=>$request->user()],200);
    });
  Route::get("/messages",function(){
    // return response()->json(['messages'=>"yelem"],200);

    return Message::all();
  });
    Route::post("/message",function(Request $request){
        $message = new Message();
        $message->message = $request->message;
        $message->user_id = Auth::user()->id;
        $message->save();
        // SendMessage::dispatch($message);
        // GotMessage::dispatch($message);
        // new Event(new GotMessage($message));
     event(new GotMessage($message));

        return response()->json(['message'=>'Message Created'],200);
    });

});

Route::get('/hello',function (){
    return response()->json(['message'=>'Hello World'],200);
});


Route::post('/login',function (Request $request){
    $credentials = $request->only('email','password');
    if (Auth::attempt($credentials)){
    $user = Auth::user();   
    $token = $user->createToken('token')->plainTextToken;
    return response()->json(['token'=>$token,'name'=>$user->name],200);
        // return response()->json(['message'=>'Login Success'],200);
    }
    return response()->json(['message'=>'Login Failed'],401);
})->name('login');

Route::post('/register',function (Request $request){
    $user = new App\Models\User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = bcrypt($request->password);
    $user->save();
    return response()->json(['message'=>'Register Success'],200);
})->name('register');
