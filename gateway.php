<?php

ini_set( 'max_execution_time', 0 );
require_once('config.php');
require_once('Irc/Client.php');

function obj_to_string($str) {
  return json_encode($str);
}

$irc = new Irc\Client(CLIENT_NAME, SERVER);
$irc->setServerPassword(CLIENT_PWD);


$irc->on('connecting', function() {
  error_log('Connecting...');
});

$irc->on('connected', function($e, $irc) {
  error_log('Connected!');
  $irc->join(CHANNEL);
});

$irc->on('send', function($e) {
  error_log('Sending '.obj_to_string($e));
});

$irc->on('message', function($e) {
  error_log('Received '.$e->raw);
});

$irc->on('welcome', function($e) {
  error_log('Welcomed '.obj_to_string($e));
});

$irc->once( 'list', function($e) {
  error_log('Listed '.obj_to_string($e));
});

// join specific channel
$irc->on('join:TestBot', function($e) {
  error_log('Joined '.obj_to_string($e));
});

$irc->on('names', function($e) {
  error_log('Received names '.obj_to_string($e));
});

$irc->on('chat', function($e) {
  error_log('Chatted: '.obj_to_string($e));
});

$irc->on('pm', function($e) {
  error_log('PM: '.obj_to_string($e));
});

$irc->on('notice', function($e) {
  error_log('Notice: '.obj_to_string($e));
});

$irc->on('options', function($e) {
  error_log('Received server options '.obj_to_string($e));
});



$irc->connect();
