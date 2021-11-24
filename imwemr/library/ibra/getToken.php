<?php

##### URL Link and HTTP Method (tp = third party)
$apiBase = "https://www.zubisoft.eu/api/tokens";
$requestType = 'POST';

##### Authentication 
$apiKey = 'ioe5_780p3';							// this is the public key (of EMR company)
$apiSecretKey = 'kuz32n87';								// this is the private key (of EMR company)

##### Request Parameters
$data = array();
$data['esign']='kj9752rtzdsx';				// = esign (LINK ID of user)
$data['action']="create";							// possible actions: create, revoke

##### Content
$content = json_encode($data);

##### Signature (hash)
$apiHash = base64_encode(hash_hmac('SHA256',$requestType . "\n" . $content, $apiSecretKey));

var_dump($apiHash);

##### This part is used to request the SQL data
// cURL = client URL = a library that lets you make HTTP requests in PHP
// cURL is used to handover information (content, key, hash,...) to the API site (adiBase) and to retrieve the data from the API: this is a JSON file ($response) that is decided into an array ($obj)
$curl = curl_init();
curl_setopt($curl, CURLOPT_HEADER, 1);			// is 1 to provide response WITH header information > will be split below (for content only use 0)
curl_setopt($curl, CURLOPT_VERBOSE, 1);
curl_setopt($curl, CURLOPT_TIMEOUT, 10);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_URL, $apiBase );
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $requestType);
curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	'Content-type: application/json',
	'X-Public-Key: ' . $apiKey,
	'X-Signed-Request-Hash: ' . $apiHash
));

$response = curl_exec($curl);					// this is the JSON data received from the API - the response from the CURL action
$info = curl_getinfo($curl);					// get info from the last transfer

echo curl_error($curl) . '<br>';
$actualResponse = (isset($info["header_size"]))?substr($response,$info["header_size"]):"";									// response content > use for response parameters
$actualResponseHeaders = (isset($info["header_size"]))?substr($response,0,$info["header_size"]):"";					// response header > use for status code and reason phrase

//var_dump($actualResponse);
curl_close($curl);

##### Output the data
// the array ($obj) is read out line by line (row by row) if it exists (if SQL data was retrieved)
$obj = json_decode($actualResponse,true);				// JSON data will be converted into an 2 dimensional array (1st dim: each is a case, like a number - 2nd dim: the parameters with names, e.g. 'id', 'cid',...)

if (isset($obj))
{
	echo "Token = " . $obj['token'];
}
else 
{ 
	echo "<br>Status = " . $info['http_code'];		// comes from CURL via curl_getinfo
	
	preg_match('#^HTTP/1.(?:0|1) [\d]{3} (.*)$#m', $actualResponseHeaders, $match);
	$reason = trim($match[1]);
	echo "<br>Reason Phrase = " . $reason;
}