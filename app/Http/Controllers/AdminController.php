<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Basket;
use App\Invoice;
use App\User;
use App\Store;
use App\Panel;

class AdminController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * AdminMiddlewareAuth could check if user has role customer or not
         * and results into more cleaner code.
         *  */
        $this->middleware(['api']);//, 'AdminMiddlewareauth']);
    }

    public function add_seller($request) {
        $seller = new User();
		$seller->name = $request->input('name');
		$seller->name = $request->input('email');
		$seller->password = bcrypt($request->input('email'));
        $seller->save();
        
        $seller_role = Role::where('slug','seller')->first();
        $seller->roles()->attach($seller_role);
		$seller_perm = Permission::where('slug','sell-stuff')->first();
        $seller->permissions()->attach($seller_perm);
        
        $store = Store::create(array('name' => $request->input('store_name'), 'user_id' => $seller->id, 
            'lat' => $request->input("lat"), 'long' => $request->input("long")) );

        $panel = Panel(array('store_id' => $store->id));
        return Response(['data' => ['user' => $user, 'store' => $store]], 200);
    }

}