<?php

namespace App\Http\Controllers;

use App\Permission;
use App\Role;
use App\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{   
	/**
	 * @Input
	 * @Output
	 * A CRUD Service to test the Role-Permission core functionalities
	 */
    public function Permission()
    {   
    	$customer_permission = Permission::where('slug','buy-stuff')->first();
		$admin_permission = Permission::where('slug', 'add-seller')->first();

		//RoleTableSeeder.php
		$customer_role = new Role();
		$customer_role->slug = 'customer';
		$customer_role->name = 'customer who buys stuff';
		$customer_role->save();
		$customer_role->permissions()->attach($customer_permission);

		$admin_role = new Role();
		$admin_role->slug = 'admin';
		$admin_role->name = 'admin controll panel';
		$admin_role->save();
		$admin_role->permissions()->attach($admin_permission);

		$customer_role = Role::where('slug','customer')->first();
		$admin_role = Role::where('slug', 'admin')->first();

		$buyStuff = new Permission();
		$buyStuff->slug = 'buy-stuff';
		$buyStuff->name = 'Buy Stuffs';
		$buyStuff->save();
		$buyStuff->roles()->attach($customer_role);

		$editUsers = new Permission();
		$editUsers->slug = 'add-seller';
		$editUsers->name = 'Add Seller';
		$editUsers->save();
		$editUsers->roles()->attach($admin_role);

		$customer_role = Role::where('slug','customer')->first();
		$admin_role = Role::where('slug', 'admin')->first();
		$customer_perm = Permission::where('slug','buy-stuff')->first();
		$admin_perm = Permission::where('slug','add-seller')->first();

		$customer = new User();
		$customer->name = 'Mahedi Hasan';
		$customer->email = 'mahedi@gmail.com';
		$customer->password = bcrypt('secrettt');
		$customer->save();
		$customer->roles()->attach($customer_role);
		$customer->permissions()->attach($customer_perm);

		$admin = new User();
		$admin->name = 'Hafizul Islam';
		$admin->email = 'hafiz@gmail.com';
		$admin->password = bcrypt('secrettt');
		$admin->save();
		$admin->roles()->attach($admin_role);
		$admin->permissions()->attach($admin_perm);

		
		return redirect()->back();
    }
}