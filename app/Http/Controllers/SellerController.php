<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Basket;
use App\Product;
use App\User;
use App\Store;
use App\Panel;

class SellerController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * SellerMiddlewareAuth could check if user has role customer or not
         * and results into more cleaner code.
         *  */
        $this->middleware(['api']);//, 'SellerMiddlewareauth']);
    }

    /**
     * Add new Products to the store.
     *
     * @return json
     */
    public function add_product($request) {
        $user = $request->user()->id;
        if (!$user->hasRole('seller')) {
            return response(['data' => 'role doesn\'t match.'], 403);
        }

        $products::insert($request->input('products'));
        $store = Store::where('id' == $request->input("store_id"));
        $panel = $store->panel();
        foreach ($products as $product) {
            $panel->products()->attach($product);
        }
        return Response(['data' => 'products just added to the store.'], 201);
    }

    /**
     * Report unresolved orders.
     *
     * @return json
     */
    public function report_orders($request) {
        $user = $request->user()->id;
        if (!$user->hasRole('seller')) {
            return response(['data' => 'role doesn\'t match.'], 403);
        }
        $orders = Order::where('store_id' == $request->input("store_id"))->where('status' == 'unresolved');
        
        return Response(['orders' => $orders], 200);
    }

    /**
     * Resolve Order.
     *
     * @return json
     */
    public function resolve_order($request) {
        $user = $request->user()->id;
        if (!$user->hasRole('seller')) {
            return response(['data' => 'role doesn\'t match.'], 403);
        }
        $orders = Order::where('store_id' == $request->input("store_id"));
        $order->update(array('status' => 'resolved') );
        return Response(['orders' => $orders], 200);
    }

}