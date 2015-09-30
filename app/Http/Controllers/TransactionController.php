<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Veritrans\Veritrans;

class TransactionController extends Controller
{
    public function __construct()
    {   
        Veritrans::$serverKey = '<your server key>';
        Veritrans::$isProduction = false;
    }

    public function transaction() 
    {
        return view('transaction'); 
    }

    public function transaction_process(Request $request)
    {
        $vt = new Veritrans;
        $order_id = $request->input('order_id');
        $action = $request->input('action');
        switch ($action) {
            case 'status':
                $this->status($order_id);
                break;
            case 'approve':
                $this->approve($order_id);
                break;
            case 'expire':
                $this->expire($order_id);
                break;
            case 'cancel':
                $this->cancel($order_id);
                break;
        } 
    }

    public function status($order_id)
    {
        $vt = new Veritrans;
        echo 'test get status </br>';
        print_r ($vt->status($order_id) );
    }

    public function cancel($order_id)
    {
        $vt = new Veritrans;
        echo 'test cancel trx </br>';
        echo $vt->cancel($order_id);
    }

    public function approve($order_id)
    {
        $vt = new Veritrans;
        echo 'test get approve </br>';
        print_r ($vt->approve($order_id) );
    }

    public function expire($order_id)
    {
        $vt = new Veritrans;
        echo 'test get expire </br>';
        print_r ($vt->expire($order_id) );
    }


}    