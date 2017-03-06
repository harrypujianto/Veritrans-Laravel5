Veritrans Laravel library
=======================================================
Veritrans :heart: Laravel!

This is the all new Laravel client library for Veritrans 2.0. Visit [https://www.veritrans.co.id](https://www.veritrans.co.id) for more information about the product and see documentation at [http://docs.veritrans.co.id](http://docs.veritrans.co.id) for more technical details.

### Requirements
The following plugin is tested under following environment:
* PHP v5.4.x or greater
* Laravel 5

## Installation
* Download the library and extract the .zip 
* Merge all the files.
* install from composer (Soon :) )

## Using Veritrans Library

### Use Veritrans Class
###### Add this following line in your controller
```php
//before 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class YourController extends Controller
{
...

// after 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Veritrans\Veritrans;

class YourController extends Controller
{
...
```
###### Add/Edit this following line in your __construct() function
```php
//set $isproduction to true for prodution environment
//before 
class YourController extends Controller
{

...

// after 
use App\Veritrans\Veritrans;
class YourController extends Controller
{
    public function __construct(){   
        Veritrans::$serverKey = 'your-server-key';
        Veritrans::$isProduction = false;
    }
...
```
### SNAP
You can see how to get snap token by reading the controller [here](https://github.com/harrypujianto/Veritrans-Laravel5/blob/master/app/Http/Controllers/SnapController.php).

#### Get Snap Token
```php
public function token() 
    {
        error_log('masuk ke snap token adri ajax');
        $midtrans = new Midtrans;
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
        $transaction_data = array(
            'transaction_details'=> $transaction_details,
            'item_details'           => $items,
            'customer_details'   => $customer_details
        );
    
        try
        {
            $snap_token = $midtrans->getSnapToken($transaction_data);
            //return redirect($vtweb_url);
            echo $snap_token;
        } 
        catch (Exception $e) 
        {   
            return $e->getMessage;
        }
    }
```

#### SNAP UI
In this section you could see the code, how to get snap token with ajax and open the snap pop up on the page. Please refer [here](https://github.com/harrypujianto/Veritrans-Laravel5/blob/master/resources/views/snap_checkout.blade.php)

For sandbox use https://app.sandbox.midtrans.com/snap/snap.js
For production use https://app.midtrans.com/snap/snap.js
```
<html>
<title>Checkout</title>
  <head>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="<CLIENT-KEY>"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  </head>
  <body>

    
    <form id="payment-form" method="post" action="snapfinish">
      <input type="hidden" name="_token" value="{!! csrf_token() !!}">
      <input type="hidden" name="result_type" id="result-type" value=""></div>
      <input type="hidden" name="result_data" id="result-data" value=""></div>
    </form>
    
    <button id="pay-button">Pay!</button>
    <script type="text/javascript">
  
    $('#pay-button').click(function (event) {
      event.preventDefault();
      $(this).attr("disabled", "disabled");
    
    $.ajax({
      
      url: './snaptoken',
      cache: false,
      success: function(data) {
        //location = data;
        console.log('token = '+data);
        
        var resultType = document.getElementById('result-type');
        var resultData = document.getElementById('result-data');
        function changeResult(type,data){
          $("#result-type").val(type);
          $("#result-data").val(JSON.stringify(data));
          //resultType.innerHTML = type;
          //resultData.innerHTML = JSON.stringify(data);
        }
        snap.pay(data, {
          
          onSuccess: function(result){
            changeResult('success', result);
            console.log(result.status_message);
            console.log(result);
            $("#payment-form").submit();
          },
          onPending: function(result){
            changeResult('pending', result);
            console.log(result.status_message);
            $("#payment-form").submit();
          },
          onError: function(result){
            changeResult('error', result);
            console.log(result.status_message);
            $("#payment-form").submit();
          }
        });
      }
    });
  });
  </script>


</body>
</html>

```

### VT-Web

You can see some more details of VT-Web examples [here](https://github.com/harrypujianto/Veritrans-Laravel5/blob/master/app/Http/Controllers/VtwebController.php).

#### Get Redirection URL of a Charge
```php
//you don't have to use the function name 'vtweb', it's just an example
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
```

#### Handle Notification Callback

Create a route in the route.php. The route must be post, since we're getting http post notification. 
```php
Route::post('/vt_notif', 'VtwebController@notification');
```

You need to exclude the notification route from CsrfToken Verification
Edit VerifyCsrfToken.php which located in App/http/middleware/VerifyCsrfToken.php
```php
//before
    class VerifyCsrfToken extends BaseVerifier
    {
        protected $except = [
            //
        ];
    }
    
//after
    class VerifyCsrfToken extends BaseVerifier
    {
        protected $except = [
            //
        'vt_notif'
        ];
    }
```

You can see some more details of notification handler examples [here](https://github.com/harrypujianto/Veritrans-Laravel5/blob/master/app/Http/Controllers/VtwebController.php) (line 101).
```php
//you don't have to use the function name 'notification', it's just an example
public function notification()
{
        $vt = new Veritrans;
        echo 'test notification handler';
        $json_result = file_get_contents('php://input');
        $result = json_decode($json_result);
        if($result){
        $notif = $vt->status($result->order_id);
        }
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
```

### VT-Direct
You can see VT-Direct form [here](https://github.com/harrypujianto/Veritrans-Laravel5/blob/master/resources/views/checkout.blade.php).

you can see VT-Direct process [here](https://github.com/harrypujianto/Veritrans-Laravel5/blob/master/app/Http/Controllers/VtdirectController.php).

#### Checkout Page

```html
<html>
<head>
    <title>Checkout</title>
    <!-- Include PaymentAPI  -->
    <link href="{{ URL::to('css/jquery.fancybox.css') }}" rel="stylesheet"> 
</head>
<body>
    <script type="text/javascript" src="https://api.sandbox.veritrans.co.id/v2/assets/js/veritrans.min.js"></script>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script> 
    <script type="text/javascript" src="{{ URL::to('js/jquery.fancybox.pack.js') }}"></script>

    <h1>Checkout</h1>
    <form action="vtdirect" method="POST" id="payment-form">
        <fieldset>
            <legend>Checkout</legend>
            <p>
                <label>Card Number</label>
                <input class="card-number" value="4811111111111114" size="20" type="text" autocomplete="off"/>
            </p>
            <p>
                <label>Expiration (MM/YYYY)</label>
                <input class="card-expiry-month" value="12" placeholder="MM" size="2" type="text" />
                <span> / </span>
                <input class="card-expiry-year" value="2018" placeholder="YYYY" size="4" type="text" />
            </p>
            <p>
                <label>CVV</label>
                <input class="card-cvv" value="123" size="4" type="password" autocomplete="off"/>
            </p>

            <p>
                <label>Save credit card</label>
                <input type="checkbox" name="save_cc" value="true">
            </p>

            <input id="token_id" name="token_id" type="hidden" />
            <button class="submit-button" type="submit">Submit Payment</button>
        </fieldset>
    </form>

    <!-- Javascript for token generation -->
    <script type="text/javascript">
    $(function(){
        // Sandbox URL
        Veritrans.url = "https://api.sandbox.veritrans.co.id/v2/token";
        // TODO: Change with your client key.
        Veritrans.client_key = "<your client key>";
        
        //Veritrans.client_key = "d4b273bc-201c-42ae-8a35-c9bf48c1152b";
        var card = function(){
            return {    'card_number'       : $(".card-number").val(),
                        'card_exp_month'    : $(".card-expiry-month").val(),
                        'card_exp_year'     : $(".card-expiry-year").val(),
                        'card_cvv'          : $(".card-cvv").val(),
                        'secure'            : true,
                        'bank'              : 'bni',
                        'gross_amount'      : 10000
                         }
        };

        function callback(response) {
            if (response.redirect_url) {
                // 3dsecure transaction, please open this popup
                openDialog(response.redirect_url);

            } else if (response.status_code == '200') {
                // success 3d secure or success normal
                closeDialog();
                // submit form
                $(".submit-button").attr("disabled", "disabled"); 
                $("#token_id").val(response.token_id);
                $("#payment-form").submit();
            } else {
                // failed request token
                console.log('Close Dialog - failed');
                //closeDialog();
                //$('#purchase').removeAttr('disabled');
                // $('#message').show(FADE_DELAY);
                // $('#message').text(response.status_message);
                //alert(response.status_message);
            }
        }

        function openDialog(url) {
            $.fancybox.open({
                href: url,
                type: 'iframe',
                autoSize: false,
                width: 700,
                height: 500,
                closeBtn: false,
                modal: true
            });
        }

        function closeDialog() {
            $.fancybox.close();
        }
        
        $('.submit-button').click(function(event){
            event.preventDefault();
            //$(this).attr("disabled", "disabled"); 
            Veritrans.token(card, callback);
            return false;
        });
    });

    </script>
</body>
</html>
```

#### Checkout Process

##### 1. Create Transaction Details

```php
$transaction_details = array(
  'order_id'    => time(),
  'gross_amount'  => 10000
);
```

##### 2. Create Item Details, Billing Address, Shipping Address, and Customer Details (Optional)

```php
// Populate items
$items = array(
    array(
      'id'       => 'item1',
      'price'    => 5000,
      'quantity' => 1,
      'name'     => 'Adidas f50'
    ),
    array(
      'id'       => 'item2',
      'price'    => 2500,
      'quantity' => 2,
      'name'     => 'Nike N90'
    ));

// Populate customer's billing address
$billing_address = array(
    'first_name'   => "Andri",
    'last_name'    => "Setiawan",
    'address'      => "Karet Belakang 15A, Setiabudi.",
    'city'         => "Jakarta",
    'postal_code'  => "51161",
    'phone'        => "081322311801",
    'country_code' => 'IDN'
  );

// Populate customer's shipping address
$shipping_address = array(
    'first_name'   => "John",
    'last_name'    => "Watson",
    'address'      => "Bakerstreet 221B.",
    'city'         => "Jakarta",
    'postal_code'  => "51162",
    'phone'        => "081322311801",
    'country_code' => 'IDN'
  );

// Populate customer's info
$customer_details = array(
    'first_name'       => "Andri",
    'last_name'        => "Setiawan",
    'email'            => "payment-api@veritrans.co.id",
    'phone'            => "081322311801",
    'billing_address'  => $billing_address,
    'shipping_address' => $shipping_address
  );
```

##### 3. Get Token ID from Checkout Page

```php
// Token ID from checkout page
$token_id = $request->input('token_id');
```
##### 4. Create Transaction Data

```php
// Transaction data to be sent
$transaction_data = array(
    'payment_type' => 'credit_card',
    'credit_card'  => array(
      'token_id'      => $token_id,
      'bank'          => 'bni',
      'save_token_id' => isset($_POST['save_cc'])
    ),
    'transaction_details' => $transaction_details,
    'item_details'        => $items,
    'customer_details'    => $customer_details
  );
```

##### 5. Charge

```php
//create new object from Veritrans class
$vt = new Veritrans;
$response= $vt->vtdirect_charge($transaction_data);
```

##### 6. Handle Transaction Status

```php
// Success
if($response->transaction_status == 'capture') {
  echo "<p>Transaksi berhasil.</p>";
  echo "<p>Status transaksi untuk order id $response->order_id: " .
      "$response->transaction_status</p>";

  echo "<h3>Detail transaksi:</h3>";
  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
// Deny
else if($response->transaction_status == 'deny') {
  echo "<p>Transaksi ditolak.</p>";
  echo "<p>Status transaksi untuk order id .$response->order_id: " .
      "$response->transaction_status</p>";

  echo "<h3>Detail transaksi:</h3>";
  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
// Challenge
else if($response->transaction_status == 'challenge') {
  echo "<p>Transaksi challenge.</p>";
  echo "<p>Status transaksi untuk order id $response->order_id: " .
      "$response->transaction_status</p>";

  echo "<h3>Detail transaksi:</h3>";
  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
// Error
else {
  echo "<p>Terjadi kesalahan pada data transaksi yang dikirim.</p>";
  echo "<p>Status message: [$response->status_code] " .
      "$response->status_message</p>";

  echo "<pre>";
  var_dump($response);
  echo "</pre>";
}
```

#### Process Transaction
More details can be found [here](https://github.com/harrypujianto/Veritrans-Laravel5/blob/master/app/Http/Controllers/TransactionController.php)

Don't forget to create new veritrans object
```php
//creating new veritrans object
$vt = new Veritrans;
```
##### Get a Transaction Status
```php
$status = $vt->status($order_id);
var_dump($status);
```
##### Approve a Transaction
```php
$approve = $vt->approve($order_id);
var_dump($approve);
```
##### Cancel a Transaction
```php
$cancel = $vt->cancel($order_id);
var_dump($cancel);
```
