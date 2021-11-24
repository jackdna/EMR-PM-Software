<?php
error_reporting(0);
//error_reporting(E_ALL);
//ini_set("display_errors",1);
ini_set("default_socket_timeout", -1);

$inteHost = "10.1.12.58";
$intePort = "5672";
$inteUser = "QA";
$intePass = "qa@123";
$inteExchange = "IntegrityExchangeIMWDev";
$inteExchangeType = "topic";
$inteDurable = true;
$inteInboundRoutingKey = "inte.dev.inbound";
$inteOutboundRoutingKey = "inte.dev.outbound";
$inboundQueue = "INTE.IMW.Dev.Inbound";
$outboundQueue = "INTE.IMW.Dev.Outbound";
$inteSSLOptions = array('verify_peer' => false);
$_SERVER['DOCUMENT_ROOT'] = "D:/imedic/apache/htdocs";
$GLOBALS["sync_api"] = "yes";

?>