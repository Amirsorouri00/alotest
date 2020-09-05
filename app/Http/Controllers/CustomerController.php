<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Basket;
use App\Invoice;
use App\User;
use App\Order;

class CustomerController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /**
         * CustomerMiddlewareAuth could check if user has role customer or not
         * and results into more cleaner code.
         *  */
        $this->middleware(['api']);//, 'CustomerMiddlewareauth']);
    }

    /**
     * return the customer-specific dashboard data.
     *
     * @return dashboard-data
     */
    public function login($request)
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        else {
            $user = User::where('api_token', $token);
            if ($user->hasRole('customer')) {
                return response(['data' => 'this json must return the customer-specific dashboard data.'], 200);
            }
            else {
                return response(['error' => 'the role is not match'], 403);
            }
        }
        
    }

    /**
     * return the list of stores and their panel's products to the client.
     *
     * @return dashboard-data
     */
    public function list_product($request)
    {
        $user = $request->user()->id;
        if (!$user->hasRole('customer')) {
            return response(['data' => 'role doesn\'t match'], 403);
        }
        else {
            $lat = $request->input('latitude');
            $lang = $request->input('longitude');
            $nearByStores = find_nearby_stores($lat, $long);

            $storesRes = DB::table('stores')->whereIn('id', $nearByStores);
        
            return response(['data' => $storesRes], 200);
        }
    }


    /** 
     * calculates "the closest 20 locations that are within a 
     * radius of 25 miles to the 37, -122 coordinate."
     * 
     * @param lat,long
    */
    private function find_nearby_stores ($lat, $long) 
    {
        $nearByStores = DB::table('stores')->where('status', 'open')
                    ->select(DB::raw('SELECT id, ( 3959 * acos( cos( radians(37) ) * cos( radians( '.$lat.' ) ) \
                        * cos( radians( '.$lang.') - radians(-122) ) + sin( radians(37) ) * sin(radians('.$lat.')) ) ) AS distance \
                        HAVING distance < 25 \
                        ORDER BY distance ') );
        return $nearByStores;
    }

    /**
     * adds product to user basket
     *
     * @return success response
     */
    public function add_to_basket($request)
    {
        $user = $request->user()->id;
        if (!$user->hasRole('customer')) {
            return response(['data' => 'role doesn\'t match.'], 403);
        }
        else {
            $productId = $request->input('product_id');
            $basket = Null;
            if (!Basket::where('user_id', $user->id)->where('status', 'active')->exists() ) {
                $basket = Basket::create(array('status' => 'active', 'user_id' => $user->id));
            }
            else {
                $basket = Basket::where('user_id', $user->id)->where('status', 'active')->get();
            }
            $basket->products()->attach($productId);
        
            return response(['data' => 'successfuly added to basket'], 200);
        }
    }


    /**
     * generate receipt for the user's active basket
     *
     * @return success response
     */
    public function generate_receipt($request) {
        $user = $request->user()->id;
        if (!$user->hasRole('customer')) {
            return response(['data' => 'role doesn\'t match.'], 403);
        }
        else {
            $productId = $request->input('product_id');
            $basket = Null;
            if (!Basket::where('user_id', $user->id)->where('status', 'active')->exists() ) {
                return response(['error' => 'user couldn\'t be able to call this API without having any active basket'], 403);
            }
            else {
                $basket = Basket::where('user_id', $user->id)->where('status', 'active')->get();
                if (Invoice::where('basket_id', $basket->id)->where('status', 'active')->exists()) {
                    return response(['data' => 'already exists', 'invoice' => $invoice], 203);
                }
                $products = $basket->products();
                $amount = 0.0;
                foreach($products as $product) {
                    $amount+=$product->price;
                }
                $invoice = Invoice::create(array('amount' => $amount, 'status' => 'unpayed'));
                $basket->invoice()->associate($invoice);
                return response(['data' => 'receipt successfuly generated', 'invoice' => $invoice], 200);
            }
        }
    }

    /**
     * buy an unpayed invoice
     *
     * @return success response
     */
    public function buy($request) {

        $MERCHANT = '9f35b4e2-4022-11e9-ad5c-000c295eb8fc';
        $client = Client('https://www.zarinpal.com/pg/services/WebGate/wsdl');
        # amount = 1000  # Toman / Required
        $description = "پرداخت رسید سبد خرید کاربر.";  # Required
        # email = 'amirsorouri26@gmail.com'  # Optional
        # mobile = '09128048897'  # Optional
        $CallbackURL = 'http://neolej.ir/api/payment/verify_redirect/'; # Important: need to edit for realy server.

        $user = $request->user()->id;
        if (!$user->hasRole('customer')) {
            return response(['data' => 'role doesn\'t match'], 403);
        }
        else {
            $invoiceId = $requst->input('invoice');
            if (!Invoice::where('id', $invoiceId)->where('status', 'unpayed')->exists() ) {
                return response(['error' => 'This invoice has been payed or doesn\'t exists at all.'], 403);
            }
            else {
                $result = $client.service().PaymentRequest($MERCHANT, $invoice->amount, $description, $email, $mobile, $CallbackURL);
                
                if ($result->status == 100) {
                    $invoice->update(array('authority' => $result.Authority, 'status' => 'payed') );
                    $order = Order::create(array("status" => "unresolved", "invoice_id" => $invoice->id, "store_id" => $store_id) );
                    
                    return Response(['auth'=> $result.Authority], 200);
                }
                else {
                    return Response(['Error'=>  'from zarrinpal'], 500);
                }
            }
        }
    }
}