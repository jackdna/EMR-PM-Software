<?php

// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

class implantable_device_list {

	private $api_url = "https://accessgudid.nlm.nih.gov/api/v1/";
	private $utslogin_url = "https://utslogin.nlm.nih.gov/cas/v1/";
	private $serviceName = false;
	private $wsdl = false;
	private $proxyTicket = false;
	private $api_tgt = false;
	private $api_key = false;
	private $ssl = false;
	private $error, $umlsUser, $umlsPass;
	

	function __construct() {
		$umls_proxy_time = time();
		//$this->umls_init();
	}
	
	function listDevices() {
		if(!$this->proxyTicket) {
			//$this->umls_init();
		}
		
		$url = $this->api_url . "devices/implantable/list.json?ticket=".$this->proxyTicket;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		curl_close($ch);

		return json_decode($result, true);
	}
	
	function getDeviceBydi($di) {
		$url = $this->api_url . "devices/lookup.json?di=".$di;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		curl_close($ch);

		$device = json_decode($result, true);
		if(isset($device['gudid']['device'])) {
			return $device['gudid']['device'];
		}
		return false;
	}
	
	function getDeviceByUdi($udi) {
		$url = $this->api_url . "devices/lookup.json?udi=".$udi;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		curl_close($ch);

		$device = json_decode($result, true);
		if(isset($device['gudid']['device'])) {
			return $device['gudid']['device'];
		}
		return false;
	}
	
	function getParseUdi($udi) {
		$url = $this->api_url . "parse_udi.json?udi=".$udi;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->ssl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url);
		$result=curl_exec($ch);
		curl_close($ch);

		return json_decode($result, true);
	}
	
}

//END CLASS
?>