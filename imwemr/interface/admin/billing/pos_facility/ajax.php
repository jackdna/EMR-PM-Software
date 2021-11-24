<?php
set_time_limit(600);
require_once("../../../../config/globals.php");
require_once($GLOBALS['fileroot'].'/library/classes/common_function.php');
$mpay="";
if(verify_payment_method("MPAY")){
	$mpay=" if(pft.mpay_locid IS NULL,'',pft.mpay_locid) as mpay_locid, ";

}

$_REQUEST['so'] = xss_rem($_REQUEST['so'], 2, 'sanitize');	/* Sanitize arbitrary values - Security Fix */

$task	= isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so		= isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'pos_id';
$soAD	= (strtoupper($_REQUEST['soAD'])=='DESC') ? 'DESC' : 'ASC';	/* Prevent arbitrary values - Security Fix  */
$table	= "pos_facilityies_tbl";
$pkId	= "pos_facility_id";
$chkFieldAlreadyExist = "pos_id";

switch($task){
	case 'delete':
		$id = $_POST['pkId'];
		$q 		= "delete from ".$table." WHERE ".$pkId." IN (".$id.") and headquarter!='1'";
		$res 	= imw_query($q);
		if($res){
			echo '1';
		}else{
			echo '0';//.imw_error()."\n".$q;
		}
		break;
	case 'save_update':
		$id = $_POST[$pkId];
		unset($_POST[$pkId]);
		unset($_POST['task']);
		$query_part = "";
		
		foreach($_POST as $k=>$v){
			$query_part .= $k."='".addslashes($v)."', ";
		}
		$query_part = substr($query_part,0,-2);
		$qry_con = "";
		/*if($id){$qry_con=" AND ".$pkId."!='".$id."'";}
		$q_c="SELECT ".$pkId." from ".$table." WHERE ".$chkFieldAlreadyExist."='".$_POST[$chkFieldAlreadyExist]."'".$qry_con;
		$r_c=imw_query($q_c);
		if(imw_num_rows($r_c)==0){		*/
		if($id==''){
			$q = "INSERT INTO ".$table." SET ".$query_part;
		}else{
			$q = "UPDATE ".$table." SET ".$query_part." WHERE ".$pkId." = '".$id."'";
		}
		$res = imw_query($q);
		if($_POST['headquarter']==1 && $id){
			//update_hq('headquarter','0',$id);
			$qry="UPDATE ".$table." set headquarter='0' WHERE ".$pkId."!=".$id;
			$res=imw_query($qry);
		}
		if($res){
			echo 'Record Saved Successfully.';
		}else{
			echo 'Record Saving failed.'.imw_error()."\n".$q;
		}
		/*}else {
			echo "enter_unique".$q_c;	
		}*/
		break;
	case 'show_list':
		$q = "SELECT pft.pos_facility_id,pft.posfacilitygroup_id,pft.facilityPracCode,pft.facility_name,if(pos_t.pos_code IS NULL,'',pos_t.pos_code) as pos_code_val,pft.npiNumber,pft.taxId,pft.pos_facility_address,pft.pos_facility_city,pft.pos_facility_state,if(zip_ext!='',concat(pos_facility_zip,'-',zip_ext),pos_facility_zip) as pos_facility_zip_code,if(pft.phone_ext!='',concat(pft.phone,'<br>&nbsp;&nbsp;Ext. ',pft.phone_ext),pft.phone) as pos_facility_phone,".$mpay."if(pft.headquarter='1','Yes','No') as headquarter,pft.pos_id,pft.pos_facility_zip,pft.zip_ext,pft.phone,pft.phone_ext,pft.thcic_id FROM pos_facilityies_tbl pft LEFT JOIN pos_tbl pos_t on (pft.pos_id=pos_t.pos_id) ORDER BY $so $soAD";
		$r = imw_query($q);
		$rs_set = array();
		if($r && imw_num_rows($r)>0){

			while($rs = imw_fetch_assoc($r)){
				$rs_set[] = $rs;
			}
		}
		$pos = pos_tbl();
        
        $return=array();
        $return['records']=$rs_set;
        $return['pos']=$pos;
        if( isPosFacGroupEnabled() ) {
            $pos_facility_group = pos_facility_group();
            $return['pos_facility_group']=$pos_facility_group;
        }
		echo json_encode($return);
		break;
	default: 
}
function pos_tbl()	{
	$q="select pos_id,pos_prac_code,pos_description from pos_tbl";
	$res=imw_query($q);
	if(imw_num_rows($res)>0){
		$result=array();
		while($rs=imw_fetch_assoc($res)){
			$result[]=$rs;
		}
		return $result;
	}
	return false;
}

?>