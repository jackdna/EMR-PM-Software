<?php 
include_once("../../config/globals.php");

$prov_sql=" SELECT id,fname,lname,user_type,default_facility,delete_status,erp_doctor_id FROM users WHERE user_type=1 and delete_status=0 and erp_doctor_id='' ";
$prov_res=imw_query($prov_sql);
$counter=0;
$msg_info=array();
$erp_error=array();
if($prov_res && imw_num_rows($prov_res)>0){
    while( $user = imw_fetch_assoc($prov_res) ) {
        $erp_doctor_id = $user['erp_doctor_id'];
        $user_type = $user['user_type'];
        $pro_id=$erp_external_id = $user['id'];
        $pro_lname=$user['lname'];
        $pro_fname=$user['fname'];
        $doctorDetails=array();
        if( $user_type==1 ){
			try {
				$data=$locations=array();
				$data["lastName"]= $pro_lname;
				$data["firstName"]= $pro_fname;
				$data["alias"]= "";
				$data["active"]= true;
				$data["inHouse"]= true;
				$data["locationExternalId"]= "";
				$data["secureRecipientExternalId"]= $erp_external_id;
				$data["emailAddress"]= "";
				$data["locations"]= $locations;
				$data["id"]= $erp_doctor_id;
				$data["externalId"]= $erp_external_id;

				include_once($GLOBALS['srcdir']."/erp_portal/doctors.php");
				$obj_doctors = new Doctors();

				$doctorDetails = $obj_doctors->addUpdateDoctor($data);

				if(count($doctorDetails)>0) {
					$erp_doctor_external_id=$doctorDetails['externalId']; 
					$erp_doctor_id=$doctorDetails['id'];

					$update_sql = "UPDATE users SET erp_doctor_id='".$erp_doctor_id."' WHERE id=".$pro_id." ";
					imw_query($update_sql) or $msg_info[] = imw_error($update_sql);
					
					$counter++;
				}
			
			} catch(Exception $e) {
				$erp_error[]='Unable to connect to ERP Portal';
			}
        }

    }
}


if(count($msg_info)>0){
    echo (implode("<br>",$msg_info));
} else {
    echo "$counter existing doctors uploaded successfull.";
}
die;

?>