<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

//        Role::create(['name' => 'writer']);
//        Permission::create(['name' => 'write post']);

//        $role = Role::findById(1);
//        $permission = Permission::findById(1);
//        $role->givePermissionTo($permission);

//        Permission::create(['name' => 'edit post']);
//        auth()->user()->givePermissionTo('edit post');
//        auth()->user()->assignRole('writer');

//        return $permissionNames = auth()->user()->getPermissionNames();
//        return $permissions = auth()->user()->permissions;
//        return $permissions = auth()->user()->getDirectPermissions();
//        return $permissions = auth()->user()->getPermissionsViaRoles();
//        return $permissions = auth()->user()->getAllPermissions();
//        return $roles = auth()->user()->getRoleNames();

//        return $users = User::role('writer')->get();
//        return $users = User::permission('edit post')->get();

        return view('home');
    }
}
