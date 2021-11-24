<?php
///////////////////
//Topcon Settings//
///////////////////

$topcon_enable            = 0; 									//Sets the visiablity of the button, If set to 0 then the button will not appear

$topcon_baseurl           = 'https://host.domain.com/'; 	//BaseURL is supplied by Topcon
$topcon_ccrr              = 'USNY0109'; 						//CCRR number is supplied by Topcon/Host  
$topcon_link_type         = '1';								//Should alway be set to 1
$topcon_username          = $_SESSION['authProviderName'];		//Pulled from page
$topcon_patient_lastname  = $pt_name_arr[2];					//Pulled from page
$topcon_patient_firstname = $pt_name_arr[1];				 	//Pulled from page
$topcon_date_format       = '11';								//Should always be set to 11. Its the correct date/format for imwemr

function generate_url($base_url, $ccrr, $link_type, $username, $patient_lastname, $patient_firstname, $date_format)
	{
		$topcon_URL = ' ';
		$topcon_URL .= $base_url;
		$topcon_URL .= $ccrr;
		$topcon_URL .= '#lt='.$link_type;
		$topcon_URL .= '&u='.$username;
		$topcon_URL .= '&ln='.$patient_lastname;
		$topcon_URL .= '&fn='.$patient_firstname;
		$topcon_URL .= '&df='.$date_format;

		return $topcon_URL;

	}

//This is the Topcon URL it is called when opening the window. 
$topcon = generate_url($topcon_baseurl,$topcon_ccrr,$topcon_link_type,$topcon_username,$topcon_patient_lastname,$topcon_patient_firstname,$topcon_date_format);



?>