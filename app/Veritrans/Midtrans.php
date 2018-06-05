<?php

namespace App\Veritrans;
use App\Exceptions\VeritransException as Exception;

class Midtrans {

  /**
    * Your merchant's server key
    * @static
    */
  public static $serverKey;
  
  /**
    * true for production
    * false for sandbox mode
    * @static
    */
  public static $isProduction;

    /**
    * Default options for every request
    * @static
    */
    public static $curlOptions = array(); 

    const SANDBOX_BASE_URL = 'https://api.sandbox.veritrans.co.id/v2';
    const PRODUCTION_BASE_URL = 'https://api.veritrans.co.id/v2';
    const SNAP_SANDBOX_BASE_URL = 'https://app.sandbox.midtrans.com/snap/v1';
    const SNAP_PRODUCTION_BASE_URL = 'https://app.midtrans.com/snap/v1';
    

    public function config($params)
    {
        Midtrans::$serverKey = $params['server_key'];
        Midtrans::$isProduction = $params['production'];
    }

    /**
    * @return string Veritrans API URL, depends on $isProduction
    */
    public static function getBaseUrl()
    {
      return Midtrans::$isProduction ?
          Midtrans::PRODUCTION_BASE_URL : Midtrans::SANDBOX_BASE_URL;
    }

    public static function getSnapBaseUrl()
    {
      return Midtrans::$isProduction ?
          Midtrans::SNAP_PRODUCTION_BASE_URL : Midtrans::SNAP_SANDBOX_BASE_URL;
    }

  /**
   * Send GET request
   * @param string  $url
   * @param string  $server_key
   * @param mixed[] $data_hash
   */
  public static function get($url, $server_key, $data_hash)
  {
    return self::remoteCall($url, $server_key, $data_hash, false);
  }

  /**
   * Send POST request
   * @param string  $url
   * @param string  $server_key
   * @param mixed[] $data_hash
   */
  public static function post($url, $server_key, $data_hash)
  {
      return self::remoteCall($url, $server_key, $data_hash, true);
  }

    /**
   * Actually send request to API server
   * @param string  $url
   * @param string  $server_key
   * @param mixed[] $data_hash
   * @param bool    $post
   */
    public static function remoteCall($url, $server_key, $data_hash, $post = true)
    { 
      $ch = curl_init();

      $curl_options = array(
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Accept: application/json',
          'Authorization: Basic ' . base64_encode($server_key . ':')
        ),
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_CAINFO => dirname(__FILE__) . "/data/cacert.pem"
      );

      // merging with Veritrans_Config::$curlOptions
      if (count(Midtrans::$curlOptions)) {
        // We need to combine headers manually, because it's array and it will no be merged
        if (Midtrans::$curlOptions[CURLOPT_HTTPHEADER]) {
          $mergedHeders = array_merge($curl_options[CURLOPT_HTTPHEADER], Midtrans::$curlOptions[CURLOPT_HTTPHEADER]);
          $headerOptions = array( CURLOPT_HTTPHEADER => $mergedHeders );
        } else {
          $mergedHeders = array();
        }

        $curl_options = array_replace_recursive($curl_options, Midtrans::$curlOptions, $headerOptions);
      }

      if ($post) {
        $curl_options[CURLOPT_POST] = 1;

        if ($data_hash) {
          $body = json_encode($data_hash);
          $curl_options[CURLOPT_POSTFIELDS] = $body;
        } else {
          $curl_options[CURLOPT_POSTFIELDS] = '';
        }
      }

      curl_setopt_array($ch, $curl_options);

      $result = curl_exec($ch);
      $info = curl_getinfo($ch);
      // curl_close($ch);

      if ($result === FALSE) {
        throw new Exception('CURL Error: ' . curl_error($ch), curl_errno($ch));
      }
      else {
        $result_array = json_decode($result);
	      if (!in_array($info['http_code'], array(200, 201, 202, 407))) {
        $message = 'Midtrans Error (' . $info['http_code'] . '): '
            . implode(',', $result_array->error_messages);
        throw new Exception($message, $info['http_code']);

      }
        else {
          return $result_array;
        }
      }
    }

  public static function getSnapToken($params)
  {
    
    $result = Midtrans::post(
        Midtrans::getSnapBaseUrl() . '/transactions',
        Midtrans::$serverKey,
        $params);

    return $result->token;
  }

}