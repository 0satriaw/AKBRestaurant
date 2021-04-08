<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class PegawaiController extends Controller
{
    //show all
    public function index(){
        $user = User::all();

        if(count($user)>0){
                return response([
                'message' =>'Retrieve All Success',
                'data' =>$user
                ],200);
            }

        return response([
            'message' => 'Empty',
            'data' =>null
            ],404);
    }

    //create
    public function register(Request $request){
        $registrationData = $request->all();
        $validate = Validator::make($registrationData,[
            'nama'=>'required|max:60',
            'email'=>'required|email:rfc,dns|unique:users',
            'password'=>'required',
            'jenis_kelamin'=>'required',
            'no_telp'=>'required|numeric|digits_between:10,13|starts_with:08',
            'tanggal_bergabung'=>'date',
            'status'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],400); //return error invalid

        $registrationData['password'] = bcrypt($request->password); //enkripsi password

        $user = User::create($registrationData)->sendApiEmailVerificationNotification();

        return response([
            'message'=>'Register Success',
            'user'=>$user,
        ],200);
    }

    public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData,[
            'email'=>'required|email:rfc,dns',
            'password'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],400);

        if(!Auth::attempt($loginData))
            return response(['message'=>'Invalid Credentials'],401);

        $user = Auth::user();
        $token = $user->createToken('Authenticaton Token')->accessToken;
        return response([
            'message'=>'Login Success',
            'user'=>$user,
            'token_type'=>'Bearer',
            'access_token'=>$token
        ]);
    }

    public function logout(Request $request){
        $request->user()->token()->revoke();

        return response()->json([
            'message'=>'Succesfully logged out'
        ]);
    }

    //show using id
    public function show($id){
        $user = User::find($id);

        if(!is_null($user)){
            return response([
                'message'=>'Retrieve User Success',
                'data'=>$user
            ],200);
        }

        return response([
            'message'=>'User Not Found',
            'data'=>null
        ],404);
    }

    public function update(Request $request, $id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message'=>'User Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        //validate update blm
        $validate = Validator::make($updateData,[
            'nama'=>'required|max:60',
            'email'=>'required|email:rfc,dns|unique:users',
            'jenis_kelamin'=>'required',
            'no_telp'=>'required|numeric|digits_between:10,13|starts_with:08',
            'tanggal_bergabung'=>'date',
            'status'=>'required'
        ]);

        if($validate->fails())
            return response(['message'=>$validate->errors()],404);//return error invalid input

        $user->nama = $updateData['nama'];
        $user->email = $updateData['email'];
        $user->no_telp = $updateData['no_telp'];
        $user->jenis_kelamin = $updateData['jenis_kelamin'];
        $user->tanggal_bergabung = $updateData['tanggal_bergabung'];
        $user->status = $updateData['status'];

        if($user->save()){
            return response([
                'message'=>'Update User Success',
                'data'=>$user,
            ],200);
        }//return user yg telah diedit

        return response([
            'message'=>'Update User Failed',
            'data'=>null,
        ],404);//return message saat user gagal diedit
    }

    public function updatePassword(Request $request,$id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message'=>'User Not Found',
                'data'=>null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData,[
            'password'=>'required',
            'newPassword'=>'required',
            'confirmPassword'=>'required'
        ]);

        if($validate->fails()){
            return response(['message'=>$validate->errors()],404);//return error invalid input
        }else{
                if((Hash::check(request('password'), Auth::user()->password))==false){
                    return response([
                        'message'=>'Please check your old password ',
                        'data'=>null,
                    ],404);//return message saat user gagal diedit
                }else if($updateData['newPassword'] != $updateData['confirmPassword']){
                    return response([
                        'message'=>'new password doesnt match',
                        'data'=>null,
                    ],404);//return message saat user gagal diedit
                }else{
                    $user->password = bcrypt($updateData['newPassword']);
                }
        }

        if($user->save()){
            return response([
                'message'=>'Update User Success',
                'data'=>$user,
            ],200);
        }//return user yg telah diedit

        return response([
            'message'=>'Update User Failed',
            'data'=>null,
        ],404);//return message saat user gagal diedit
    }

    public function destroy($id){
        $user = User::find($id);

        if(is_null($user)){
            return response([
                'message'=>'User Not Found',
                'data'=>null
            ],404);
        }//return message saat data user tidak ditemukan

        if($user->delete()){
            return response([
                'message'=>'Delete User Success',
                'data'=>$user,
            ],200);
        }//return message saat berhasil menghapus data

        return response([
            'message'=>'Delete User Failed',
            'data'=>null
        ],400);//return message saat gagal menghapus data user
    }


}
