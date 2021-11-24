<?php
include __DIR__ . "/sync_api_global.php";
require_once __DIR__ . '/../library/vendor_api/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Connection\AMQPSSLConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPIOException;

$connection = new AMQPSSLConnection($inteHost, $intePort, $inteUser, $intePass, "/", $inteSSLOptions); //[host, port, user, pass, vHost, ssl_options]
$channel = $connection->channel();
/** Declare the queue if it does not exists */
$channel->queue_declare($inboundQueue, false, $inteDurable, false, false);
$data = '{"api_allergy_data":[{"Allergies":
[{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"23","AllergyCode":{"Description":"Bactrim"},"ReactionDesc":"React1","inteAllergyId":"2"},
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"23","AllergyCode":{"Description":"Ciprofloxin"},"ReactionDesc":"React2","inteAllergyId":"3"},
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"23","AllergyCode":{"Description":"Latex"},"ReactionDesc":"React3","inteAllergyId":"4"},
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"24","AllergyCode":{"Description":"Iodine"},"ReactionDesc":"React4","inteAllergyId":"5"},
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"24","AllergyCode":{"Description":"Morphine"},"ReactionDesc":"React5","inteAllergyId":"6"},
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"24","AllergyCode":{"Description":"Sulfa"},"ReactionDesc":"React6","inteAllergyId":"7"}]}]}';
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, '', $inboundQueue); //[msg, exchange, routing_key]
echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();
?>
