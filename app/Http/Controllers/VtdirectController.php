<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Veritrans\Veritrans;

class VtdirectController extends Controller
{
    public function __construct()
    {   
        Veritrans::$serverKey = '<your server key>';
        Veritrans::$isProduction = false;
    }

    public function vtdirect() 
    {
        return view('checkout'); 
    }

    public function checkout_process(Request $request)
    {
        $token = $request->input('token_id');
        $vt = new Veritrans;

        $transaction_details = array(
            'order_id'          => uniqid(),
            'gross_amount'  => 10000
        );

        // Populate items
        $items = [
            array(
                'id'                => 'item1',
                'price'         => 5000,
                'quantity'  => 1,
                'name'          => 'Adidas f50'
            ),
            array(
                'id'                => 'item2',
                'price'         => 2500,
                'quantity'  => 2,
                'name'          => 'Nike N90'
            )
        ];

        // Populate customer's billing address
        $billing_address = array(
            'first_name'        => "Andri",
            'last_name'         => "Setiawan",
            'address'           => "Karet Belakang 15A, Setiabudi.",
            'city'                  => "Jakarta",
            'postal_code'   => "51161",
            'phone'                 => "081322311801",
            'country_code'  => 'IDN'
            );

        // Populate customer's shipping address
        $shipping_address = array(
            'first_name'    => "John",
            'last_name'     => "Watson",
            'address'       => "Bakerstreet 221B.",
            'city'              => "Jakarta",
            'postal_code' => "51162",
            'phone'             => "081322311801",
            'country_code'=> 'IDN'
            );

        // Populate customer's Info
        $customer_details = array(
            'first_name'            => "Andri",
            'last_name'             => "Setiawan",
            'email'                     => "andrisetiawan@me.com",
            'phone'                     => "081322311801",
            'billing_address' => $billing_address,
            'shipping_address'=> $shipping_address
            );

        $transaction_data = array(
            'payment_type'      => 'credit_card', 
            'credit_card'       => array(
               'token_id'  => $token,
               'bank'    => 'bni'
               ),
            'transaction_details'   => $transaction_details,
            'item_details'           => $items
        );

        
        $response = null;
        try
        {
            $response= $vt->vtdirect_charge($transaction_data);
        } 
        catch (Exception $e) 
        {
            return $e->getMessage; 
        }

        //var_dump($response);
        if($response)
        {
            if($response->transaction_status == "capture")
            {
                //success
                echo "Transaksi berhasil. <br />";
                echo "Status transaksi untuk order id ".$response->order_id.": ".$response->transaction_status;

                echo "<h3>Detail transaksi:</h3>";
                var_dump($response);
            }
            else if($response->transaction_status == "deny")
            {
                //deny
                echo "Transaksi ditolak. <br />";
                echo "Status transaksi untuk order id ".$response->order_id.": ".$response->transaction_status;

                echo "<h3>Detail transaksi:</h3>";
                var_dump($response);
            }
            else if($response->transaction_status == "challenge")
            {
                //challenge
                echo "Transaksi challenge. <br />";
                echo "Status transaksi untuk order id ".$response->order_id.": ".$response->transaction_status;

                echo "<h3>Detail transaksi:</h3>";
                var_dump($response);
            }
            else
            {
                //error
                echo "Terjadi kesalahan pada data transaksi yang dikirim.<br />";
                echo "Status message: [".$response->status_code."] ".$response->status_message;

                echo "<h3>Response:</h3>";
                var_dump($response);
            }   
        }

    }
}    