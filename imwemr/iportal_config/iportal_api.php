<?php
$ignoreAuth=true;
include_once("../config/globals.php");
if($_REQUEST['IPORTAL_REQUEST']!=(md5(constant("IPORTAL_SERVER")))){
	//$arr_return="<br>".constant("IPORTAL_SERVER")."=".md5($_REQUEST['IPORTAL_REQUEST'])."<br>".md5(constant("IPORTAL_SERVER"));
	die("[Error]:401 Unauthorized Access ");
}

function get_records($qryQry){
	$qryRowsReturn=array();
	$qryQryRes = imw_query($qryQry);
	if($qryQryRes){
		while($qryRow = imw_fetch_object($qryQryRes)){
			$qryRowsReturn[] = $qryRow;
		}		
		
	}else if(!$qryQryRes){
		$qryRowsReturn[]="[Error-]: query failed: $statement (" . imw_error() . ")";
	 }
	return $qryRowsReturn;	
}
function get_records_arr($qryQry){
	$qryRowsReturn=array();
	$qryQryRes = imw_query($qryQry);
	if($qryQryRes){
		while($qryRow = imw_fetch_assoc($qryQryRes)){
			$qryRowsReturn[] = $qryRow;
		}		
	}else if(!$qryQryRes){
		$qryRowsReturn[]="[Error-]:  query failed: $statement (" . imw_error() . ")";
	}
	return $qryRowsReturn;	
}
function sqlQuery_ip($statement){
  $rez=array();
  $query = imw_query($statement);
  if($query && imw_num_rows($query)>0){
  	$rez = @imw_fetch_assoc($query);
  }else if(!$query){
	$rez[]="[Error-]: query failed: $statement (" . imw_error() . ")";
  }
  return $rez;
}
function save_pt_sig($img_content,$img_name,$htmlFolder){
	/*if(!$htmlFolder) {
		$htmlFolder = "new_html2pdf";	
	}*/
	$ret=0;
	$sig_path='';
	global $webServerRootDirectoryName;
	global $web_RootDirectoryName;
	if(trim($img_content) && $img_name) {
		//THIS WAS THE R7 OLD ADDRESS WHICH IS CORRECTED..
		//$sigFolder = $webServerRootDirectoryName.$web_RootDirectoryName."/interface/common/".$htmlFolder."/iportal_sig";
		$sigFolder = data_path()."iportal_sig";
		if(!is_dir($sigFolder)){		
			mkdir($sigFolder, 0777);
		}
		$img_data=base64_decode($img_content);
		if($img_data){
			$sig_path = $sigFolder."/".$img_name;
			file_put_contents($sig_path, $img_data);
			$imagecreate = imagecreatefromjpeg($sig_path);
			if($imagecreate){
				$ret = 1;
			}
		}
		
	}
	return $ret;
}
$receive_requests=json_decode($_REQUEST['otherDT']);

$req_qry=$_REQUEST['get_qry']?trim(urldecode($_REQUEST['get_qry'])):$receive_requests->get_qry;
$req_qry_type=$_REQUEST['get_qry_type']?$_REQUEST['get_qry_type']:$receive_requests->get_qry_type;
$req_img_name=trim(urldecode($_REQUEST['get_img_name']));

if($req_qry && $req_qry_type){
	switch($req_qry_type) {
		case "select":
			$retun_arr=array();
			$qry_res=sqlQuery_ip($req_qry);
			if(is_array($qry_res)){
				$retun_arr=serialize($qry_res);
			}
			echo $retun_arr;	
		break;
		case "multi_select":
			$qry_res=get_records($req_qry);
			echo serialize($qry_res);	
		break;
		case "multi_select_assoc":
			$qry_res=get_records_arr($req_qry);
			echo serialize($qry_res);	
		break;
		case "insert":
			$qrt_insert=imw_query($req_qry);
			if($qrt_insert){
				$ret=imw_insert_id();
			}else{$ret="[Error-]: ".imw_error().$qrt_insert;}
			echo $ret;
		break;
		case "update":
			$qrt_update=imw_query($req_qry);
			if($qrt_update){
				echo $ret="1";
			}else{$ret="[Error-]: ".imw_error().$qrt_insert;}
		break;
		case "delete":
			$qrt_delete=imw_query($req_qry);
			if($qrt_delete){
				echo $ret=imw_affected_rows();
			}
		break;
		case "image":
			$data = file_get_contents($req_qry);
			echo $ret=base64_encode($data);
		break;
		case "iportal_patient_sign":
			$htmlFolder = "html2pdf";
			if(constant("CONSENT_FORM_VERSION")=="consent_v2") {
				$htmlFolder = "new_html2pdf";
			}			
			echo save_pt_sig($req_qry,$req_img_name,$htmlFolder);
		break;
		case "pt_login":
			$login_sucess=array();
		    $username = $receive_requests->username;
			$get_password = $receive_requests->password;
			list($str,$uname) = explode("~~||~~",base64_decode($username));
			
			$qry_login_u = "Select id,username,fname,lname,mname,password from patient_data where username='".$uname."' limit 0,1";
				$res_login_u = imw_query($qry_login_u);
				if(imw_num_rows($res_login_u)>0){
				$row_pt_flds = imw_fetch_assoc($res_login_u);
				$SQL_PASSWORD=sha1(substr(base64_decode($row_pt_flds['password']),3));
				if($get_password==$SQL_PASSWORD){
					unset($row_pt_flds['password']);
					$login_sucess=json_encode($row_pt_flds);	
				}else{
				
				}
			}else{
				
			}
			echo $login_sucess;
		break;
		case "resp_login":
			$login_sucess=0;
			$qry_user_arr=unserialize($req_qry);$arr_ret=array();
			if(count($qry_user_arr)>0){
				 //$arr_ret=$qrt_username_arr['user_id']."-".$qrt_username_arr['password'];
				if(trim($qry_user_arr['user_id']) && trim($qry_user_arr['user_password'])){
					$e_pass=$qry_user_arr['user_password'];
					$q_pt_pass="Select resp_password from resp_party where id='".$qry_user_arr['user_id']."' limit 0,1";
					$r_pt_pass=imw_query($q_pt_pass);
					$row_pt_pass=imw_fetch_assoc($r_pt_pass);
					$u_pass=sha1($row_pt_pass['resp_password']);
					if($u_pass==$e_pass){
						$login_sucess=1;
					}
				}
			}
			echo $login_sucess;
		break;
	}
}
?>