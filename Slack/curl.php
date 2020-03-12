<?php

function curl_my_init($url, $tag) {
  // check module installation
  if(!function_exists('curl_init')) {
    error_log('cURL is not installed');
    return false;
  }

  // reuse handler to keep opened connections
  static $c = null;
  static $t = '';
  if($t != $tag) {
    // new handler if tag changes
    $c = curl_init();
    $t = $tag;
    // return response
    curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
    // include headers
    //curl_setopt($c, CURLOPT_HEADER, true);
    // do not wait indifinetely
    curl_setopt($c, CURLOPT_CONNECTTIMEOUT, 5);
    // do not wait indifinetely
    curl_setopt($c, CURLOPT_TIMEOUT, 10);
  }
  // set URL
  curl_setopt($c, CURLOPT_URL, $url);

  return $c;
}

function curl_my_exec($c) {
  $res = curl_exec($c);

  return array(
    'response' => $res,
    'http_code' => curl_getinfo($c, CURLINFO_HTTP_CODE),
    'cookies' => curl_getinfo($c, CURLINFO_COOKIELIST),
  );
}

function curl_get($url, $headers=null) {
  $c = curl_my_init($url, 'GET');
  if($c === false) {
    return false;
  }
  // headers
  if(isset($headers)) curl_setopt($c, CURLOPT_HTTPHEADER, $headers);

  return curl_my_exec($c);
}

function curl_post($url, $body='', $headers=null) {
  $c = curl_my_init($url, 'POST');
  if($c === false) {
    return false;
  }
  // method
  curl_setopt($c, CURLOPT_POST, true);
  // body
  curl_setopt($c, CURLOPT_POSTFIELDS, $body);
  // headers
  if(isset($headers)) curl_setopt($c, CURLOPT_HTTPHEADER, $headers);

  return curl_my_exec($c);
}
