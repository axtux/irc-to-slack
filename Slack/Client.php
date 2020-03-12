<?php

namespace Slack;

require_once('curl.php');

class Client {
  const BASE_API = 'https://slack.com/api/';
  
  protected $api_key;

  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  public function post_message($channel, $username, $message) {
    $url = self::BASE_API.'chat.postMessage';
    $data = array(
      'channel' => $channel,
      'username' => $username,
      'text' => $message,
    );
    $headers = array(
      "Authorization: Bearer $this->api_key",
      'Content-Type: application/json;charset=UTF-8',
    );
    $body = json_encode($data);
    $res = curl_post($url, $body, $headers);
    $code = $res['http_code'];
    $res = $res['response'];
    if($code != 200) {
      error_log("Slack error posting to $channel: http code $code");
      return false;
    }
    $data = json_decode($res, true);
    if($data === null) {
      error_log("Slack error decoding json response: $res");
      return false;
    }
    if(isset($data['error'])) {
      error_log('Slack error: '.$data['error']);
    }
    if(isset($data['warning'])) {
      error_log('Slack warning: '.$data['warning']);
    }
    return $data['ok'];
  }
}
