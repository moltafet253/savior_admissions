<?php

namespace App\Http\Controllers;

use App\Models\Catalogs\School;
use App\Models\GeneralInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:list-users', ['only' => ['index']]);
        $this->middleware('permission:create-users', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-users', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-users', ['only' => ['destroy']]);
        $this->middleware('permission:search-user', ['only' => ['searchUser']]);
    }

    public function index()
    {
        $me = User::find(session('id'));
        if ($me) {
            if ($me->hasRole('Super Admin')) {
                $data = User::orderBy('id', 'DESC')->paginate(
                    $perPage = 15, $columns = ['*'], $pageName = 'users'
                );
                return view('users.index', compact('data'));
            } elseif ($me->hasRole('Principal')) {
                $data = User::where(function ($query) use ($me) {
                    if(isset($me->school_id) && is_array($me->school_id) && count($me->school_id) > 0 and !empty($me->school_id)) {
                        foreach ($me->school_id as $schoolId) {
                            $query->orWhereJsonContains('additional_information->school_id', $schoolId);
                        }
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                })
                    ->where('id','!=',$me->id)
                    ->orderBy('id', 'DESC')
                    ->paginate(15);
                return view('users.index', compact('data'));
            }
        }
        abort(403);
    }

    public function create()
    {
        $mySchools = User::find(session('id'))->school_id;
        $roles = Role::orderBy('name','asc')->get();
        $schools = School::whereIn('id', $mySchools)->get();
        return view('users.create', compact('roles','schools'));
    }

    public function store(Request $request)
    {
        $me = User::find(session('id'));
        $this->validate($request, [
            'name' => 'required',
            'family' => 'required',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|integer|unique:users,mobile',
            'password' => 'required|unique:users,mobile',
            'roles' => 'required',
            'school' => 'required|exists:schools,id'
        ]);

//        $input['password'] = Hash::make(12345678);
        $user = new User;
        $user->name=$request->name;
        $user->family=$request->family;
        $user->email=$request->email;
        $user->mobile=$request->mobile;
        $user->password=Hash::make($request->password);
        if ($me->hasRole('Principal')){
            $additionalInformation = [
                'school_id' => $request->school,
            ];
            $userAdditionalInformation = json_decode($user->additional_information, true) ?? [];
            $userAdditionalInformation = array_merge($userAdditionalInformation, $additionalInformation);
            $user->additional_information = json_encode($userAdditionalInformation);
        }
        if ($user->save()) {
            GeneralInformation::create(
                [
                    'user_id' => $user->id
                ]
            );
            $user->assignRole($request->input('roles'));
        }
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
        $generalInformation = GeneralInformation::where('user_id', $user->id)->first();
        $schools = School::where('status', 1)->get();
        return view('users.edit', compact('user', 'roles', 'userRole', 'generalInformation', 'schools'));
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

//    public function destroy($id)
//    {
//        User::find($id)->delete();
//        return redirect()->route('users.index')
//            ->with('success', 'User deleted successfully');
//    }

    public function changeUserPassword(Request $request)
    {
        $this->validate($request, [
            'New_password' => 'same:confirm-password|min:8|max:20|required',
            'user_id' => 'required|integer'
        ]);
        $input = $request->all();
        if (!empty($input['New_password'])) {
            $input['password'] = Hash::make($input['New_password']);
            $user = User::find($input['user_id']);
            $user->password = $input['password'];
            $user->save();
            $this->logActivity('Password Changed Successfully => ' . $user->password, request()->ip(), request()->userAgent(), session('id'));
        } else {
            $input = Arr::except($input, array('New_password'));
        }
    }

    public function searchUser(Request $request)
    {
        $activity = [];
        $data = User::where(function ($query) use ($request, &$activity) {
            $searchEduCode = $request->input('search-edu-code');
            $searchFirstName = $request->input('search-first-name');
            $searchLastName = $request->input('search-last-name');
            $activity['activity'] = 'search in users';

            if (!empty($searchEduCode)) {
                $query->where('id', $searchEduCode);
                $activity['edu_code'] = $searchEduCode;
            }

            if (!empty($searchFirstName)) {
                $query->where('name', 'LIKE', "%$searchFirstName%");
                $activity['first_name'] = $searchFirstName;
            }

            if (!empty($searchLastName)) {
                $query->where('family', 'LIKE', "%$searchLastName%");
                $activity['last_name'] = $searchLastName;
            }
        })
            ->orderBy('id', 'DESC')
            ->paginate($perPage = 15, $columns = ['*'], $pageName = 'users');

        $this->logActivity(json_encode($activity), request()->ip(), request()->userAgent(), session('id'));
        return view('users.index', compact('data'));
    }

    public function changeStudentInformation(Request $request)
    {
        $user = User::find($request->user_id);
        $studentInformation = [
            'school_id' => $request->school,
        ];
        $userAdditionalInformation = json_decode($user->additional_information, true) ?? [];
        $userAdditionalInformation = array_merge($userAdditionalInformation, $studentInformation);
        $user->additional_information = json_encode($userAdditionalInformation);
        $user->save();
        $this->logActivity('Student information saved successfully => ' . $request->user_id, request()->ip(), request()->userAgent(), session('id'));
        return response()->json(['success' => 'Student information saved successfully!'], 200);
    }

    public function changePrincipalInformation(Request $request)
    {
        $this->validate($request, [
            'school' => 'required|exists:schools,id',
            'user_id' => 'required|integer|exists:users,id'
        ]);
        $user = User::find($request->user_id);
        $studentInformation = [
            'school_id' => $request->school,
        ];
        $userAdditionalInformation = json_decode($user->additional_information, true) ?? [];
        $userAdditionalInformation = array_merge($userAdditionalInformation, $studentInformation);
        $user->additional_information = json_encode($userAdditionalInformation);
        $user->save();
        $this->logActivity('Principal information saved successfully => ' . $request->user_id, request()->ip(), request()->userAgent(), session('id'));
        return response()->json(['success' => 'Principal information saved successfully!'], 200);
    }
}
