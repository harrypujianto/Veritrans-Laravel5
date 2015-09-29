<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Veritrans\Veritrans;

class PagesController extends Controller
{
    public function __construct()
    {   
        Veritrans::$serverKey = '<your server key>';
        Veritrans::$isProduction = false;
    }

    public function vtweb() 
    {
        $vt = new Veritrans;

        $transaction_details = array(
            'order_id'          => uniqid(),
            'gross_amount'  => 200000
        );

        // Populate items
        $items = [
            array(
                'id'                => 'item1',
                'price'         => 100000,
                'quantity'  => 1,
                'name'          => 'Adidas f50'
            ),
            array(
                'id'                => 'item2',
                'price'         => 50000,
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
            'email'                     => "andrisetiawan@asdasd.com",
            'phone'                     => "081322311801",
            'billing_address' => $billing_address,
            'shipping_address'=> $shipping_address
            );

        // Data yang akan dikirim untuk request redirect_url.
        // Uncomment 'credit_card_3d_secure' => true jika transaksi ingin diproses dengan 3DSecure.
        $transaction_data = array(
            'payment_type'          => 'vtweb', 
            'vtweb'                         => array(
                //'enabled_payments'    => [],
                'credit_card_3d_secure' => true
            ),
            'transaction_details'=> $transaction_details,
            'item_details'           => $items,
            'customer_details'   => $customer_details
        );
    
        try
        {
            $vtweb_url = $vt->vtweb_charge($transaction_data);
            return redirect($vtweb_url);
        } 
        catch (Exception $e) 
        {   
            return $e->getMessage;
        }
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

    public function notification()
    {
        $vt = new Veritrans;
        echo 'test notification handler';
        $json_result = file_get_contents('php://input');
        $result = json_decode($json_result);

        if($result){
        $notif = $vt->status($result->order_id);
        }

        error_log(print_r($result,TRUE));

        /*
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        if ($transaction == 'capture') {
          // For credit card transaction, we need to check whether transaction is challenge by FDS or not
          if ($type == 'credit_card'){
            if($fraud == 'challenge'){
              // TODO set payment status in merchant's database to 'Challenge by FDS'
              // TODO merchant should decide whether this transaction is authorized or not in MAP
              echo "Transaction order_id: " . $order_id ." is challenged by FDS";
              } 
              else {
              // TODO set payment status in merchant's database to 'Success'
              echo "Transaction order_id: " . $order_id ." successfully captured using " . $type;
              }
            }
          }
        else if ($transaction == 'settlement'){
          // TODO set payment status in merchant's database to 'Settlement'
          echo "Transaction order_id: " . $order_id ." successfully transfered using " . $type;
          } 
          else if($transaction == 'pending'){
          // TODO set payment status in merchant's database to 'Pending'
          echo "Waiting customer to finish transaction order_id: " . $order_id . " using " . $type;
          } 
          else if ($transaction == 'deny') {
          // TODO set payment status in merchant's database to 'Denied'
          echo "Payment using " . $type . " for transaction order_id: " . $order_id . " is denied.";
        }*/
   
    }

}
