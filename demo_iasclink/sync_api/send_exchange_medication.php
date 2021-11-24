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
$data = '{ "api_med_data":[{"Medication":[
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"23","DrugName":"Actonel","DosageQuantity":{"Value":"123","Units":"1"},"inteMedId":"2"},
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"23","DrugName":"Accupril","DosageQuantity":{"Value":"124","Units":"2"},"inteMedId":"3"},
{"ExternalId":"11","ExternalDOS":"2019-08-25","ExternalPatientId":"23","DrugName":"Albuterol","DosageQuantity":{"Value":"125","Units":"3"},"inteMedId":"4"}
]}]}';
/*
{"ExternalId":"12","ExternalDOS":"2019-08-25","ExternalPatientId":"23","DrugName":"Altace","DosageQuantity":{"Value":"126","Units":"4"},"inteMedId":"5"},
{"ExternalId":"12","ExternalDOS":"2019-08-25","ExternalPatientId":"23","DrugName":"Amiodarone","DosageQuantity":{"Value":"127","Units":"5"},"inteMedId":"6"},
{"ExternalId":"12","ExternalDOS":"2019-08-25","ExternalPatientId":"23","DrugName":"Aspirin","DosageQuantity":{"Value":"128","Units":"6"},"inteMedId":"7"},
{"ExternalId":"6385c71e-a7a8-4596-a5f0-189aaea1885c","ExternalDOS":"2019-09-04","ExternalPatientId":"146","DrugName":"Amiodarone","DosageQuantity":{"Value":"127","Units":"5"},"inteMedId":"6"},
{"ExternalId":"6385c71e-a7a8-4596-a5f0-189aaea1885c","ExternalDOS":"2019-09-04","ExternalPatientId":"146","DrugName":"Amiodarone","DosageQuantity":{"Value":"127","Units":"5"},"inteMedId":"6"}
*/
$msg = new AMQPMessage($data);

$channel->basic_publish($msg, '', $inboundQueue); //[msg, exchange, routing_key]
echo ' [x] Sent ', $data, "\n";

$channel->close();
$connection->close();
?>
