Veritrans Codeigniter library
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
