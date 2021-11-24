<?php
//ReferringPhy.php
class ReferringPhy{
	
	function get_reffphysician_detail($id){
		$arr=array();
		if(!empty($id)){
			$qryReffPhysician=" Select FirstName, LastName, MiddleName, physician_fax, physician_email from refferphysician 
							WHERE physician_Reffer_id='".$id."' LIMIT 1 ";
			$resReffPhysician=sqlQuery($qryReffPhysician);		
			if($row!=false){				
				$reff_last_name=str_ireplace("'","", $row['LastName']);
				$reff_first_name=str_ireplace("'","", $row['FirstName']);
				$reff_middle_name=str_ireplace("'","", $row['MiddleName']);
				$reff_fax_no=$row['physician_fax'];
				$reff_email_id=$row["physician_email"];
				$reff_phy_full_name=$reff_last_name.", ".$reff_first_name." ".$reff_middle_name;	
				$arr = $row;				
				$arr["name"] = $reff_phy_full_name;
				$arr["fax"] = $reff_phy_full_name."@@".$reff_fax_no;
				$arr["email"] = $reff_phy_full_name."@@".$reff_email_id;
					
			}
		}
		return $arr;
	}	

}

?>