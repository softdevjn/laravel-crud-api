<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Crm\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index()
    {
        return User::orderBy('id', 'asc')->get();
    }
    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string'
        ]);
        if ($validator->fails()) {
            return ['status' => -1, 'msg' => $validator->errors()];
        }

        $password = str_random(8);

        $result = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'initial_password' => $password,
            'password' => bcrypt($password)
          ]
        );
        return ['status' => 0, 'id' => $result->id];
    }
    public function show(User $user)
    {
        return $user;
    }
    public function edit(User $user)
    {
        //
    }
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string',
            'email' => 'string',
            'active' => 'boolean'
        ]);
        if ($validator->fails()) {
            return ['status' => -1, 'msg' => $validator->errors()];
        }
        $user->update($request->all());
        return ['status' => 0, 'id' => $user->id];
    }
    public function destroy(User $user)
    {
        
    }
    // custom
    public function resetPassword(Request $request, $id)
    {
        $user = User::find($id);
        $password = str_random(8);

        $data = [
            'initial_password' => $password,
            'password' => bcrypt($password)
        ];
        $user->update($data);
        return ['status' => 0, 'id' => $user->id];
    }
    public function userPermissions(Request $request, $id)
    {
        $userPermissions = Permission
            ::orderBy('id', 'asc')
            ->with(
                [
                    'permissionUsers' => function($query) use($id) {
                        $query->where('user_id', $id);
                    },
                ]
            )
            ->get();
        return $userPermissions;
    }
    public function multipleUpdate(Request $request)
    {
        $ids = $request->get('ids');

        $validator = Validator::make($request->get('request'), [
            'active' => 'boolean'
        ]);
        if ($validator->fails()) {
            return ['status' => -1, 'msg' => $validator->errors()];
        }

        User
            ::whereIn('id', $ids)
            ->update($request->get('request'));

        return ['status' => 0];
    }
}