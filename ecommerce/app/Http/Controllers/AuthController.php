<?php


namespace App\Http\Controllers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ApiLoginRequest;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Password;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * 
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'firstname' => $validatedData['firstname'],
            'lastname' => $validatedData['lastname'],
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'registration successful',
            'user' => $user,
             'token' => $token], 201);
    }



    public function login(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
    
        // Attempt to authenticate the user with the provided credentials
        if (!Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            // Return an error response if authentication fails
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    
        // Get the authenticated user
        $user = Auth::user();
    
        // Generate a new token for the user
        $token = $user->createToken('authToken')->plainTextToken;
    
        // Return the user and the token as the response
        return response()->json([
            'message' => 'login successful',
            'user' => $user, 
            'token' => $token], 200);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function logout()
    {
        //
        request()->user()->tokens()->delete();
        return [
            'message' => 'logout successful',
        ];
    }

    

}
