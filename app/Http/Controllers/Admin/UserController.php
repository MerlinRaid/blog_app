<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with('roles')
            ->when($request->search, function($q) use ($request){
                $s ='%'.$request->search.'%';
                $q->where(fn($qq) => $qq->where('name', 'like', $s)
                       ->orWhere('email', 'like', $s));  
            })
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::whereIn('name', ['Admin', 'Moderator', 'Author'])->orderBy('name')->pluck('name','id');
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

       $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $role = Role::findOrFail($data['role_id']);
        $user->syncRoles([$role->name]); #üks roll  

        return redirect()->route('admin.users.index')->with('status', 'Kasutaja loodud.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::whereIn('name', ['Admin', 'Moderator', 'Author'])->orderBy('name')->pluck('name','id');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
      $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users','email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role_id' => ['required', 'exists:roles,id'],
        ]);
        
        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        $role = Role::findOrFail($data['role_id']);
        //Kaitse: ära luba eemaldada viimst Admin kasutajat
        if ($user->hasRole('Admin') && $role->name !=='Admin') {
            $admins = User::role('Admin')->count();
            if ($admins <= 1) {
                return back()->with('status', 'Viimast Admin kasutajat ei saa muuta.') ->withInput();
            }
        }
        $user->syncRoles([$role->name]); 

        return redirect()->route('admin.users.edit', $user)->with('status', 'Kasutaja uuendatud.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //Ei luba kustutada iseennast
        if(Auth::id() === $user->id) {
            return back()->with('status', 'Sa ei saa kustutada iseennast.');
        }

        //kaitse: ära luba kustutada viimst Admin kasutajat
        if ($user->hasRole('Admin')) {
            $admins = User::role('Admin')->count();
            if ($admins <= 1) {
                return back()->with('status', 'Viimast Admin kasutajat ei saa kustutada.');
            }
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'Kasutaja kustutatud.');

    }
}
