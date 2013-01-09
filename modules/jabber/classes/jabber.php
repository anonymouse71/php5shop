<?php defined('SYSPATH') or die('No direct script access.');

class Jabber extends XMPPHP_XMPP {}
/**
 * Главный класс XMPPHP_XMPP
 * пример отправки сообщения:

$conn = new XMPPHP_XMPP('talk.google.com', 5222, 'username', 'password', 'xmpphp', 'gmail.com', $printlog=false, $loglevel=XMPPHP_Log::LEVEL_INFO);

try {
    $conn->connect();
    $conn->processUntil('session_start');
    $conn->presence();
    $conn->message('someguy@someserver.net', 'This is a test message!');
    $conn->disconnect();
} catch(XMPPHP_Exception $e) {
    die($e->getMessage());
}
 */