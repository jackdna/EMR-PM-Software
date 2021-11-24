<?php 
include_once("../../config/globals.php");

$fldERP = "id, name, erp_id, erp_contact_id, erp_email_id, erp_phone_id, erp_fax_id, erp_address_id, erp_country_id, email, street, city, state, postal_code, zip_ext, phone, phone_ext, fax, out_ofoffice ";
$qryERP = "Select ".$fldERP."  From facility Where (erp_id = '' OR erp_id IS NULL OR erp_id = '0' ) ";
$sqlERP = imw_query($qryERP) ;
$cntERP = imw_num_rows($sqlERP);
$counter=0;
$msg_info=array();
$erp_error=array();
if($sqlERP && $cntERP ){

    require_once(dirname(__FILE__) . "/../../library/erp_portal/locations.php");
    $objLoc = new Locations();

    while( $resERP = imw_fetch_assoc($sqlERP) ) {
		try {
			// create array of data to send
			$fac_id = $resERP['id'];
			$contact = $email = $address = $Phone = $data = array();
			
			$email[] = array(
							'id' => $resERP['erp_email_id'],
							'alias' => '',
							'address' => $resERP['email'],
							'default' =>  true,
							'sortOrder' => "0" );
			
			$address[] = array(	"id" => $resERP['erp_address_id'],
								"alias" => "",
								"address1" => $resERP['street'],
								"address2" =>  "",
								"city" => $resERP['city'],
								"state" => $resERP['state'],
								"countryId" => $resERP['erp_country_id'],
								"countryCode" => "USA",
								"countryName" => "United States",
								"zip" => $resERP['postal_code'] . ($resERP['zip_ext'] ? '-'.$resERP['zip_ext'] : '') ,
								"default" =>  true );

			$phone[] = array( "id" => $resERP['erp_phone_id'],
							"alias" => "Phone",
							"number" => $resERP['phone'].($resERP['phone_ext'] ? $resERP['phone_ext'] : ''),
							"default" => true,
							"useForSms" => false,
							"sortOrder" => 0);
			
			$phone[] = array( "id" => $resERP['erp_fax_id'],
							"alias" => "Fax",
							"number" => $resERP['fax'],
							"default" => false,
							"useForSms" => false,
							"sortOrder" => 1);

			list($fname,$lname) = explode(" ", $resERP['out_ofoffice']);
			$contact = array(	"id" => $resERP['erp_contact_id'],
								"firstName" => $fname,
								"middleName" => "",
								"lastName" => $lname,
								"suffix" => "",
								"prefix" => "",
								"fullName" => $fname.' '. $lname,
								"companyName" => "",
								"jobTitle" => "",
								"emailAddresses" => $email,
								"phoneNumbers" => $phone,
								"postalAddresses" => $address,
								"notes" => "" );

			$data['id']	= $resERP['erp_id'];
			$data['name'] = $resERP['name'];
			$data['alias'] = '';
			$data['active'] = true;
			$data['notes'] = '';
			$data['contact'] = $contact;
			$data['externalId'] = $fac_id;	
			
			// send request to Save/Update facility data at Eye Reach Patient API
			if($objLoc) {
				$result = $objLoc->addUpdateLocation($data);
			}
				
			//Update erp ids into relative fields
			if( $result )
			{
				$erp_fac_id = $result['id'];
				$erp_contact_id = $result['contact']['id'];
				$erp_email_id = $result['contact']['emailAddresses'][0]['id'];
				
				$erp_phone_id = "";
				if( $result['contact']['phoneNumbers'][0]['alias'] == 'Phone' )
					$erp_phone_id = $result['contact']['phoneNumbers'][0]['id'];
				else if( $result['contact']['phoneNumbers'][1]['alias'] == 'Phone')
					$erp_phone_id = $result['contact']['phoneNumbers'][1]['id'];
				
				$erp_fax_id = "";
				if( $result['contact']['phoneNumbers'][0]['alias'] == 'Fax' )
					$erp_fax_id = $result['contact']['phoneNumbers'][0]['id'];
				else if( $result['contact']['phoneNumbers'][1]['alias'] == 'Fax')
					$erp_fax_id = $result['contact']['phoneNumbers'][1]['id'];

				$erp_address_id = $result['contact']['postalAddresses'][0]['id'];

				$qryERP_U = "Update facility Set erp_id = '$erp_fac_id', 
												erp_contact_id = '".$erp_contact_id."', 
												erp_email_id = '".$erp_email_id."', 
												erp_phone_id = '".$erp_phone_id."', 
												erp_fax_id = '".$erp_fax_id."', 
												erp_address_id = '".$erp_address_id."' 
										Where id = ".$fac_id." ";

				$sqlERP_U = imw_query($qryERP_U) or $msg_info[] = imw_error($update_sql);
				if( $sqlERP_U && imw_affected_rows() > 0 ) $counter++;
			}
        } catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
        //echo $counter.' of ' . $cntERP .' location(s) uploaded.';
    }

    if(count($msg_info)>0){
        echo (implode("<br>",$msg_info));
    } else {
        echo "$counter existing locations uploaded successfully.";
    }

}
else
{
    echo "All Location(s) already uploaded to imwemr Portal";
}
die;

?>