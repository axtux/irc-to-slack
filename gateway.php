<?php

ini_set( 'max_execution_time', 0 );

require_once('config.php');
require_once('Irc/Client.php');
require_once('Slack/Client.php');

function obj_to_string($str) {
  return json_encode($str);
}

$irc = new Irc\Client(CLIENT_NAME, SERVER);
$irc->setServerPassword(CLIENT_PWD);
$slack = new Slack\Client(SLACK_API_KEY);

function slack_send($message, $user='IRC Bot') {
  global $slack;
  $slack->post_message(SLACK_CHANNEL, $user, $message);
}

$irc->on('connecting', function($e) {
  error_log('Connecting to '.$e->server);
  slack_send('Connecting to '.$e->server);
});

$irc->on('connected', function($e, $irc) {
  error_log('Connected to '.$e->server.', waiting server welcome');
  slack_send('Connected to '.$e->server.', waiting server welcome');
});

$irc->on('disconnecting', function($e) {
  error_log('Disconnecting to '.$e->server);
  //slack_send('Disconnecting to '.$e->server);
});

$irc->on('disconnected', function($e, $irc) {
  error_log('Disconnected to '.$e->server);
  //slack_send('Disconnected to '.$e->server);
});

$irc->on('reconnecting', function($e) {
  error_log('Reconnecting to '.$e->server);
  //slack_send('Reconnecting to '.$e->server);
});

$irc->on('send', function($e) {
  error_log('Sending '.strval($e->message));
});

$irc->on('message', function($e) {
  error_log('Received '.$e->raw);
});

$irc->on('welcome', function($e, $irc) {
  error_log('Welcomed, joining channel '.CHANNEL);
  slack_send('Welcomed, joining channel '.CHANNEL);
  $irc->join(CHANNEL);
});

$irc->once( 'list', function($e) {
  error_log('Listed '.obj_to_string($e));
});

$irc->on('join', function($e, $irc) {
  error_log('Joined channel '.$e->channel);
  slack_send('Joined channel '.$e->channel);
  // annonce yourself if you want
  //$irc->chat($e->channel, 'Now transfering messages to Slack');
});

$irc->on('names', function($e) {
  error_log('Received names '.obj_to_string($e->names->names));
});

$irc->on('chat', function($e) {
  error_log('Chat from '.$e->from.': '.$e->text);
  slack_send($e->text, $e->from);
});

$irc->on('pm', function($e) {
  error_log('Chat from '.$e->from.': '.$e->text);
  slack_send('PM: '.$e->text, $e->from);
});

$irc->on('notice', function($e) {
  error_log('Notice from '.$e->from.': '.$e->text);
});

$irc->on('options', function($e) {
  error_log('Received server options '.obj_to_string($e->options));
});

$irc->connect();
