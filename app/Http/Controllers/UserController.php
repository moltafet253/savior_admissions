<?php

namespace App\Http\Controllers;

use App\Models\GeneralInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data = User::orderBy('id', 'DESC')->paginate(5);
        return view('users.index', compact('data'))
            ->with('i', ($request->input('page', 1) - 1) * 5);

    }

    public function create()
    {
        $roles = Role::get();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'family' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|integer|unique:users,mobile',
            'roles' => 'required'
        ]);

        $input = $request->all();
//        $input['password'] = Hash::make($input['password']);
        $input['password'] = Hash::make(12345678);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        $generalInformation = GeneralInformation::create(
            [
                'user_id' => $user->id
            ]
        );

        return redirect()->route('users.index')
            ->with('success', 'User created successfully');
    }

    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', compact('user'));
    }

    public function edit($id)
    {
        $user = User::find($id);
        $roles = Role::pluck('name', 'name')->all();
        $userRole = $user->roles->pluck('name', 'name')->all();
        $generalInformation=GeneralInformation::where('user_id',$user->id)->first();

        return view('users.edit', compact('user', 'roles', 'userRole','generalInformation'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if (!empty($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = Arr::except($input, array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id', $id)->delete();

        $user->syncPermissions($request->input('roles'));

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }

    public function changeUserPassword(Request $request)
    {
        $this->validate($request, [
            'New_password' => 'same:confirm-password|min:8|max:20|required',
            'user_id' => 'required|integer'
        ]);
        $input = $request->all();
        if (!empty($input['New_password'])) {
            $input['password'] = Hash::make($input['New_password']);
            $user=User::find($input['user_id']);
            $user->password=$input['password'];
            $user->save();
            $this->logActivity('Password Changed Successfully => ' . $user->password, request()->ip(), request()->userAgent(), session('id'));
        } else {
            $input = Arr::except($input, array('New_password'));
        }
    }
}
