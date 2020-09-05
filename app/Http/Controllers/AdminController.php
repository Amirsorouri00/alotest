<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Basket;
use App\Invoice;
use App\User;

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

}