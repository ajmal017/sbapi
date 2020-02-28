<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\User;
class UserController extends Controller
{
    public function index(Request $request)
    {
        // return "df";
        return $request->user();
    }

    public function logout(Request $request)
    {
        $request->user()->token()->delete();
        return $this->response(200, "Logged out");
    }

    public function changePassword(Request $request)
    {
        $this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = $request->user();
        if(!Hash::check($request->current_password, $user->password)){
            abort(422, "The current password does not match.");
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return $this->response(200, "Password successfully changed");
    }

    public function update(Request $request)
    {
        if($request->has('current_password')){
            return $this->changePassword($request);
        }

        $this->validate($request, [
            'name' => 'required',
            'contact_no' => 'required',
            'address' => 'required',
            'image' =>  'mimes:jpeg,bmp,png|max:2000'
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->contact_no = $request->contact_no;
        $user->address = $request->address;

        if($request->hasFile("image")){
            
            $path = Storage::disk('public')->putFile('avatars', $request->file('image'));
                    Storage::delete($user->getOriginal('image'));
            $user->image = $path;
        }

        $user->save();
        return $user;
    }
    // Personal access client created successfully.
    // Client ID: 13
    // Client secret: NeRUXrMsB6utgRu8Rr2bRl2jJ1CyvtjSfjZOg7lb
    // Password grant client created successfully.
    // Client ID: 14
    // Client secret: 7Vivl0EJuxusSD69d4XZOs3olprKHsZMRGBs2xZQ

    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        try {
            $client = new \GuzzleHttp\Client();
            // dump($request->all());
            $res = $response = $client->request('POST', $request->getHttpHost()."/v1/oauth/token",
                ['json' => [ 
                  'grant_type' => "password",
                  'client_id' => "14",
                  'client_secret' => "7Vivl0EJuxusSD69d4XZOs3olprKHsZMRGBs2xZQ",
                  'username' => $request->email,
                  'password' => $request->password,
                ]]
            );
            return $res->getBody();   
        } catch (\Exception $e) {
            abort(401, "Invalid email or password.");
        }
        
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => ['required', 
               'min:6', 
               'confirmed'],
        ]);
        User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => app('hash')->make($request->password),
        ]);
       return $this->authenticate($request);
    }

    public function reset(Request $request)
    {
                $this->validate($request, [
                    'email' => 'required|email',
                ]);
            $user = User::where('email', $request->email)->firstOrFail();
             //send reset password email
    }


    public function monitorInstruments(Request $request)
    {
        $instruments = $request->user()->settings()->where('key', 'monitor_instruments')->first();
        $instruments = explode(',', $instruments->value);
        $instruments = \App\Instrument::whereIn('id', $instruments)->select('instrument_code as code')->get();
        return $instruments;

    }

    public function monitorInstrumentsSave(Request $request)
    {
        $instruments = explode(',', $request->instruments);
        $ids = \App\Instrument::whereIn('instrument_code', $instruments)->select('id')->get()->pluck('id')->toArray();
        $instruments =  join(",", $ids);
        $request->user()->settings()->where('key', 'monitor_instruments')->update(['value' => $instruments]);
        return $instruments;
    }
}
