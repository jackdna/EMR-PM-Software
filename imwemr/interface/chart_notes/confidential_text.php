<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

	File: confidential_text.php
	Purpose: This file provides Patient Confidential Information section in work view.
	Access Type : Direct
*/

include_once(dirname(__FILE__)."/../../config/globals.php");

//Check patient session and closing popup if no patient in session
$window_popup_mode = true;
require_once($GLOBALS['srcdir']."/patient_must_loaded.php");
$library_path = $GLOBALS['webroot'].'/library';

if((isset($_REQUEST["str_show_form"]) && $_REQUEST["str_show_form"] == "yes") ||
 (isset($_SESSION["conf_sec_show_form"]) && ($_SESSION["conf_sec_show_form"] == 1))){
	$bl_show_form = true;
	$_SESSION["conf_sec_show_form"] = 1; //Session
}else{
	$bl_show_form = false;
}

if((isset($_REQUEST["str_authorized"]) && $_REQUEST["str_authorized"] == "yes") ||
(isset($_SESSION["conf_sec_auth"]) && ($_SESSION["conf_sec_auth"] == 1))){
	$bl_authorized = true;
	$_SESSION["conf_sec_auth"] = 1; //Session
}else{
	$bl_authorized = false;
}
$actionBy= $_SESSION["authId"];
$patient_id = $_SESSION["patient"];
if(empty($_REQUEST['entry']))
{
$personnel_id = $_SESSION["authId"];
}
else if(!empty($_REQUEST['auth_arry']))
{
   $per_id = $_SESSION["authId"];
   $auth_arr=$_REQUEST['auth_arry'];
   $auth_array=explode(',', $auth_arr);
   if(in_array($per_id,$auth_array )){
   	$personnel_id = $_SESSION["authId"];
   }
}
$int_this_personnel_group_id = 0;
$arr_authorized_users = array();
$arr_authorized_groups = array();
$str_message = (isset($_REQUEST["str_msg"]) && $_REQUEST["str_msg"] != "") ? $_REQUEST["str_msg"] : "";


//--

function getCText($mode,$noteId){

	$phase=" AND action_performed!='DELETED'  ";
	if($mode==1){ //View All
		$phase="";
	}
	if( $noteId)
	{
		$str_log_qry = "SELECT *,pcta.action_performed,pcta.id as t_id,confidential_text_id,
											DATE_FORMAT(pcta.action_performed_on,'".get_sql_date_format('','y')." %h:%I %p') as date_and_time,
											u.fname, u.lname FROM patient_confidential_text_access pcta
											LEFT JOIN users u  ON u.id = pcta.action_performed_by
											WHERE confidential_text_id = '".$noteId."' ".
											$phase.
											"ORDER BY action_performed_on DESC";
		$rs_log = imw_query($str_log_qry);
		if(imw_num_rows($rs_log) > 0){
			$cnt = 1;
			while($arr_log = imw_fetch_array($rs_log)){

				$str_prov_name = ucfirst(substr($arr_log["fname"],0,1));
				if($arr_log["fname"] != ""){
					$str_prov_name .= "".ucfirst(substr($arr_log["lname"],0,1));
				}
				$confidential_text=	$arr_log["new_text"];
				if($arr_log["action_performed"]== "DELETED"){
					$confidential_text = "<span class='text-danger'><del>".$arr_log["old_text"]."</del></span>";
				}
	?>
			<tr>
				<td class="pointer col-sm-3" onClick="showConfidential(<?php echo $arr_log["t_id"];?>)">
					<?php echo $arr_log["date_and_time"];?>
				</td>
				<td class="pointer col-sm-2" onClick="showConfidential(<?php echo $arr_log["t_id"];?>)">
					<?php echo $str_prov_name;?>
				</td>
				<td class="pointer col-sm-7" onClick="showConfidential(<?php echo $arr_log["t_id"];?>)">
					<?php echo $confidential_text;?>
				</td>
			</tr>
	<?php
				$cnt++;
			}
		}
		else{
	?>
  		<tr><td colspan="3">No record found</td></tr>
 	<?php
		}
	}else
	{
	?>
  	<tr><td colspan="3">No record found</td></tr>
 	<?php
	}

}

if(isset($_GET["getCText"]) && isset($_GET["noteId"]) ){

	echo getCText($_GET["getCText"],$_GET["noteId"]);
	$flgStopExec=1;

}


if(!isset($flgStopExec)){ //$flgStopExec=1;
if(isset($_REQUEST["hid_action"]) && $_REQUEST["hid_action"] == "ADDED"){

	//inserting record
	if(isset($_REQUEST["responsible_person"]) && is_array($_REQUEST["responsible_person"]) && count($_REQUEST["responsible_person"]) > 0){
		$str_reponsible_person = implode(",",$_REQUEST["responsible_person"]);
		$str_reponsible_person .= ",".$personnel_id;
	}
	else
	{
	   $str_reponsible_person = $personnel_id;
	}

	if(isset($_REQUEST["responsible_group"]) && is_array($_REQUEST["responsible_group"]) && count($_REQUEST["responsible_group"]) > 0){
		$str_responsible_group = implode(",",$_REQUEST["responsible_group"]);
	}

	if(isset($_REQUEST["hid_text_id"]) && $_REQUEST["hid_text_id"] != ""){

		$ins_qry =	   "UPDATE patient_confidential_text
						SET confidential_text = '".addslashes($_REQUEST["confidential_text"])."',
							patient_id = '".$patient_id."',
							authorized_personnel = '".$str_reponsible_person."',
							authorized_group = '".$str_responsible_group."'
						WHERE id = '".$_REQUEST["hid_text_id"]."'";

		imw_query($ins_qry);
		$hid_text_id = $_REQUEST["hid_text_id"];
	}else{
		$ins_qry =	   "INSERT INTO patient_confidential_text
						SET confidential_text = '".addslashes($_REQUEST["confidential_text"])."',
							patient_id = '".$patient_id."',
							authorized_personnel = '".$str_reponsible_person."',
							authorized_group = '".$str_responsible_group."'
						";

		imw_query($ins_qry);
		$hid_text_id = imw_insert_id();
	}


	//saving reason and showing information
	$ins_log =	   "INSERT INTO patient_confidential_text_access
					SET confidential_text_id = '".$hid_text_id."',
						action_performed = 'ADDED',
						action_performed_on = NOW(),
						action_performed_by = '".$actionBy."',
						old_text = '',
						new_text = '".addslashes($_REQUEST["confidential_text"])."',
						old_provider_access = '',
						new_provider_access = '".$str_reponsible_person."',
						old_group_access = '',
						new_group_access = '".$str_responsible_group."',
						view_reason = ''
					";
	imw_query($ins_log);

	$url_header = "confidential_text.php?str_show_form=yes&str_authorized=yes&str_msg=Record added successfully";
	if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
		$zRemoteServerData["header"] = $url_header; //Go to this uri
		$flgStopExec=1;
		$zRemoteServerData["debug"]=1;
	}else{
		header("Location:".$url_header);
	}

}
} //$flgStopExec=1;


if(!isset($flgStopExec)){ //$flgStopExec=1;
//updating text
if(isset($_REQUEST["hid_action"]) && $_REQUEST["hid_action"] == "MODIFIED"){
	//print "<pre>";
	//print_r($_REQUEST);

	//getting existing values
	$str_is_note_qry2 = "SELECT id, confidential_text, authorized_personnel, authorized_group
					FROM patient_confidential_text
					WHERE id = '".$_REQUEST["hid_text_id"]."'";
	$rs_is_note2 = imw_query($str_is_note_qry2);
	$arr_is_note2 = imw_fetch_array($rs_is_note2);

	if(isset($_REQUEST["responsible_person"]) && is_array($_REQUEST["responsible_person"]) && count($_REQUEST["responsible_person"]) > 0){
		$str_reponsible_person = implode(",",$_REQUEST["responsible_person"]);
		$str_reponsible_person .= ",".$personnel_id;
	}
	else
	{
	   $str_reponsible_person = $personnel_id;
	}

	if(isset($_REQUEST["responsible_group"]) && is_array($_REQUEST["responsible_group"]) && count($_REQUEST["responsible_group"]) > 0){
		$str_responsible_group = implode(",",$_REQUEST["responsible_group"]);
	}
	//saving reason and showing information
    if($_REQUEST['old_info']!=$_REQUEST["confidential_text"])
	{
	$ins_log =	   "INSERT INTO patient_confidential_text_access
					SET confidential_text_id = '".$_REQUEST["hid_text_id"]."',
						action_performed = 'MODIFIED',
						action_performed_on = NOW(),
						action_performed_by = '".$actionBy."',
						old_text = '".$arr_is_note2["confidential_text"]."',
						new_text = '".addslashes($_REQUEST["confidential_text"])."',
						old_provider_access = '".$arr_is_note2["authorized_personnel"]."',
						new_provider_access = '".$str_reponsible_person."',
						old_group_access = '".$arr_is_note2["authorized_group"]."',
						new_group_access = '".$str_responsible_group."',
						view_reason = ''
					";
	imw_query($ins_log);
    }
	//updating record


	$upd_qry =	   "UPDATE patient_confidential_text
					SET confidential_text = '".addslashes($_REQUEST["confidential_text"])."',
						authorized_personnel = '".$str_reponsible_person."',
						authorized_group = '".$str_responsible_group."'
					WHERE id = '".$_REQUEST["hid_text_id"]."'";
	imw_query($upd_qry);

	$url_header="confidential_text.php?str_show_form=yes&str_authorized=yes&str_msg=Record updated successfully";
	if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
		$zRemoteServerData["header"] = $url_header; //Go to this uri
		$flgStopExec=1;
	}else{
		header("Location:".$url_header);
	}
}
} //$flgStopExec=1;

if(!isset($flgStopExec)){ //$flgStopExec=1;
//deleting text
if(isset($_REQUEST["hid_action"]) && $_REQUEST["hid_action"] == "DELETED"){
	//print "<pre>";
	//print_r($_REQUEST);
	if(isset($_REQUEST["responsible_person"]) && is_array($_REQUEST["responsible_person"]) && count($_REQUEST["responsible_person"]) > 0){
		$str_reponsible_person = implode(",",$_REQUEST["responsible_person"]);
		$str_reponsible_person .= ",".$personnel_id;
	}
	else
	{
	   $str_reponsible_person = $personnel_id;
	}

	if(isset($_REQUEST["responsible_group"]) && is_array($_REQUEST["responsible_group"]) && count($_REQUEST["responsible_group"]) > 0){
		$str_responsible_group = implode(",",$_REQUEST["responsible_group"]);
	}
	//getting existing values
	$str_is_note_qry2 = "SELECT id, confidential_text, authorized_personnel, authorized_group
					FROM patient_confidential_text
					WHERE id = '".$_REQUEST["hid_text_id"]."'";
	$rs_is_note2 = imw_query($str_is_note_qry2);
	$arr_is_note2 = imw_fetch_array($rs_is_note2);

	//saving reason and showing information
	$ins_log =	   "INSERT INTO patient_confidential_text_access
					SET confidential_text_id = '".$_REQUEST["hid_text_id"]."',
						action_performed = 'DELETED',
						action_performed_on = NOW(),
						action_performed_by = '".$actionBy."',
						old_text = '".$arr_is_note2["confidential_text"]."',
						new_text = '',
						old_provider_access = '".$arr_is_note2["authorized_personnel"]."',
						new_provider_access = '',
						old_group_access = '".$arr_is_note2["authorized_group"]."',
						new_group_access = '',
						view_reason = ''
					";
	imw_query($ins_log);

	//updating record
	$upd_qry =	   "UPDATE patient_confidential_text
					SET confidential_text = '',
						authorized_personnel = '".$str_reponsible_person."',
						authorized_group = '".$str_responsible_group."'
					WHERE id = '".$_REQUEST["hid_text_id"]."'";
	imw_query($upd_qry);

	$url_header="confidential_text.php?str_show_form=yes&str_authorized=yes&str_msg=Record deleted successfully";
	if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
		$zRemoteServerData["header"] = $url_header; //Go to this uri
		$flgStopExec=1;
	}else{
		header("Location:".$url_header);
	}
}

} //$flgStopExec=1;

if(!isset($flgStopExec)){ //$flgStopExec=1;
//to check as if any confidential info has been saved for this patient or not
$str_is_note_qry = "SELECT id, confidential_text, authorized_personnel, authorized_group
												FROM patient_confidential_text
												WHERE patient_id = '".$patient_id."'";
$rs_is_note = imw_query($str_is_note_qry);
if(imw_num_rows($rs_is_note) > 0){
	$arr_is_note = imw_fetch_array($rs_is_note);
	if(trim($arr_is_note["confidential_text"]) == "" && trim($arr_is_note["authorized_personnel"]) == "" && trim($arr_is_note["authorized_group"]) == ""){

		$bl_show_form = true;
		$bl_authorized = true;

	}else{

		//print_r($arr_is_note);
		//get this personnel's group
		$str_user_group_qry =  "SELECT user_group_id
								FROM users
								WHERE id = '".$personnel_id."'";
		$rs_user_group = imw_query($str_user_group_qry);
		$arr_user_group = imw_fetch_array($rs_user_group);
		if($arr_user_group[0]["user_group_id"] != ""){
			$int_this_personnel_group_id = $arr_user_group["user_group_id"];
		}

		//authorizing this personnel to access information

		$arr_authorized_users = explode(",", trim($arr_is_note["authorized_personnel"]));
		$arr_authorized_groups = explode(",",  trim($arr_is_note["authorized_group"]));

		if($int_this_personnel_group_id != 0){
			if(count($arr_authorized_groups) > 0){
				if(in_array($int_this_personnel_group_id, $arr_authorized_groups)){

					$bl_show_form = true;
					$bl_authorized = true;
				}
			}
		}
		if($bl_show_form == false){
			if(count($arr_authorized_users) > 0){
				if(in_array($personnel_id, $arr_authorized_users)){

					$bl_show_form = true;
					$bl_authorized = true;
				}
			}
		}

	}

}else{
	$bl_show_form = true;
	$bl_authorized = true;
}

} //$flgStopExec=1;

if(!isset($flgStopExec)){ //$flgStopExec=1;

//saving reasong
if(isset($_REQUEST["hid_reason"]) && $_REQUEST["hid_reason"] == "process"){


	//saving reason and showing information
	$ins_log =	   "INSERT INTO patient_confidential_text_access
					SET confidential_text_id = '".$arr_is_note["id"]."',
						action_performed = 'VIEWED',
						action_performed_on = NOW(),
						action_performed_by = '".$actionBy."',
						old_text = '".$arr_is_note["confidential_text"]."',
						new_text = '".$arr_is_note["confidential_text"]."',
						old_provider_access = '".$arr_is_note["authorized_personnel"]."',
						new_provider_access = '".$arr_is_note["authorized_personnel"]."',
						old_group_access = '".$arr_is_note["authorized_group"]."',
						new_group_access = '".$arr_is_note["authorized_group"]."',
						view_reason = '".addslashes($_REQUEST["access_reason"])."'
					";
	imw_query($ins_log);

	$bl_authorized = true;
	$bl_show_form = true;
	$url_header="confidential_text.php?str_show_form=yes&str_authorized=yes";
	if(constant("REMOTE_SYNC") == 1 && !empty($zOnParentServer)){
		$zRemoteServerData["header"] = $url_header; //Go to this uri
		$flgStopExec=1;
	}else{
		header("Location:".$url_header);
	}
}

} ////$flgStopExec=1;

if(!isset($flgStopExec)){ //$flgStopExec=1;

//authorization request
if(isset($_REQUEST["hid_auth"]) && $_REQUEST["hid_auth"] == "process"){

	//matching password to authorize
	$str_match_pass_qry = "SELECT id FROM facility WHERE facility_type = 1 AND Confidential_psw = '".md5($_REQUEST["access_pass"])."'";
	$rs_match_pass = imw_query($str_match_pass_qry);
	if(imw_num_rows($rs_match_pass) > 0){
		$bl_authorized = true;
		$bl_show_form = false;
	}else{
		$bl_authorized = false;
		$str_message = "Password does not match. Please enter correct password.";
	}
}

//check permissions

//
if($bl_show_form==false && core_check_privilege(array("priv_cnfdntl_txt"))){
  $bl_show_form = true;
  $bl_authorized = true;

  $readonly_rights = core_check_privilege(array("priv_CnfdntlTxt_Read")) ? true : false;
  if(core_check_privilege(array("priv_CnfdntlTxt_Full"))){
    //$readonly_rights = false;
  }
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient Refractive Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>

		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<style>
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
			.adminbox{min-height:inherit}
			.adminbox label{overflow:initial;}
			.adminbox .panel-body{padding:5px}
			.adminbox div:nth-child(odd) {padding-right: 1%;}
			.od{color:blue;}
			.os{color:green;}
			.ou{color:#9900cc;}
			.modal-content { border-radius:0px!important;}
		</style>
		<script>
			<?php
			if($str_message != ""){
				?>
			   $(document).ready(function(){
				  top.fAlert("<?php echo $str_message;?>");
			   });
				<?php
				$str_message = "";
			}
			?>

			function showAllC(obj,id){
				var v = (obj.checked) ? "1" : "0";
				$.ajax({
					url:"confidential_text.php?getCText="+v+"&noteId="+id,
					type:'GET',
					success:function(response){
						$("#divCT").html(response);
					}
				});
			}

			function update_patient_info(){
			if(document.getElementById("confidential_text").value == ""){
					top.fAlert("Please enter confidential text");
					return false;
				}else{
				document.getElementById("hid_action").value = "MODIFIED";
				document.frm_text.submit();
				}
			}
			function delete_patient_info(){
				top.fancyConfirm('Are you sure you want to delete this record?','Delete Record','document.getElementById("hid_action").value = "DELETED";document.frm_text.submit();')
			}
			function add_patient_info(){
				if(document.getElementById("confidential_text").value == ""){
					top.fAlert("Please enter confidential text");
					return false;
				}else{
					document.getElementById("hid_action").value = "ADDED";
					document.frm_text.submit();
				}
			}

			function showConfidential(str){
				$.ajax({
					url:window.opener.top.JS_WEB_ROOT_PATH+'/interface/chart_notes/confidential_text_detail.php?t_id='+str,
					type:'GET',
					success:function(response){
						show_modal('con_info','Information',response,'','','modal-lg');
					}
				});
			}

			function get_opt_val(modal_id,select_id){
				var opt_arr = new Array();
				$('#'+modal_id+'').find('select option:selected').each(function(id,elem){
					var value = $(elem).val();
					opt_arr.push(value);
				});
				$('#'+select_id+'').selectpicker('val',opt_arr);
				$('#'+modal_id+'').modal('hide');
			}

			function set_opt_val(obj,sel_id){
				var opt_arr = new Array();
				$('#'+sel_id+' option').each(function(id,elem){
					if($(elem).prop('selected') === true){
						var value = $(elem).val();
						opt_arr[id] = value;
					}
				});
				var modal_id = $(obj).data('target');
				$(''+modal_id+' select option').each(function(id,elem){
					var val = $(elem).val();
					if(opt_arr[id] == val){
						$(elem).prop('selected',true);
					}
				});
			}

			$(window).load(function(){
				popup_resize(1150,680,0.9);
			});
		</script>
	</head>

	<body>

		<?php if($bl_show_form == true){ ?>
			<div class="mainwhtbox pd10">
      	<div class="row">

          <!-- Heading -->
          <div class="col-sm-12 purple_bar">
          	<label>Patient Confidential Information</label>
         	</div>

          <!-- Header Section -->
          <div class="col-sm-12 pt10">
          	<div class="row">
            	<form name="frm_text" method="POST">
              	<input type="hidden" id="hid_text_id" name="hid_text_id" value="<?php echo $arr_is_note["id"];?>">
                <input type="hidden" id="hid_action" name="hid_action" value="">

                <div class="col-sm-8">
                	<label>Confidential Text</label>
                  <textarea cols="60" rows="4" tabindex="1" class="form-control" id="confidential_text" name="confidential_text"><?php echo $arr_is_note["confidential_text"];?></textarea><input type="hidden" name="old_info" value="<?php echo $arr_is_note["confidential_text"];?>">
              	</div>

                <div class="col-sm-4">
                  <div class="row">

                    <div class="col-sm-12">
                      <label >Authorized Providers</label>
                      <select name="responsible_person[]" id="responsible_person" tabindex="23" class="selectpicker" data-width="100%" multiple data-size="5" data-title="Select Provider" data-actions-box="true">
                        <?php

                          $t_responsible= "select id,fname,lname from users where id != '".$actionBy."' and delete_status='0' order by lname,fname";
                          $sqlt_responsible = imw_query($t_responsible);
                          $numt_reponsible = imw_num_rows($sqlt_responsible);

                        while($vrst_reponsible = imw_fetch_array($sqlt_responsible)){

                          $phyName_drop = $vrst_reponsible['lname'];
                          if(!empty($vrst_reponsible['lname'])){
                            $phyName_drop .= ', '.$vrst_reponsible['fname'];
                          }
                          else{
                            $phyName_drop .= $vrst_reponsible['fname'];
                          }

                          if(count($arr_authorized_users) > 0){
                            if(in_array($vrst_reponsible['id'],$arr_authorized_users)){
                              $select1="selected";
                            }else{
                              $select1="";
                            }
                          }
                          echo "<option value='".$vrst_reponsible['id']."' $select1>".$phyName_drop."</option>";
                        }

                        ?>
                      </select>

                    </div>

                    <div class="col-sm-12">
                      <label class="pt10">Authorized Groups</label>
                      <select name="responsible_group[]" id="responsible_group" class="selectpicker" data-width="100%" data-size="5" multiple data-title="Select Groups" data-actions-box="true">
                        <?php
                        $t_responsible= "select id,name from user_groups order by name";
                        $sqlt_responsible = imw_query($t_responsible);
                        $numt_reponsible=imw_num_rows($sqlt_responsible);
                         while($vrst_reponsible=imw_fetch_array($sqlt_responsible)){

                            $phyName_drop = $vrst_reponsible['name'];

                            if(count($arr_authorized_groups) > 0){
                              if(in_array($vrst_reponsible['id'],$arr_authorized_groups)){
                                $select1="selected";
                              }else{
                                $select1="";
                              }
                            }
                            echo "<option value='".$vrst_reponsible['id']."' $select1>".$phyName_drop."</option>";
                            }
                           ?>
                      </select>

                    </div>

                  </div>
								</div>

                <input type="hidden" name="auth_arry" value="<?php echo $arr_is_note["authorized_personnel"]?>">
                <input type="hidden" name="entry" value="<?php echo $arr_is_note["id"]?>">
                <input type="hidden" id="hid_reason" name="hid_reason" value="">
							</form>
						</div>
					</div>
      	</div>
    	</div>

      <div class="mainwhtbox pd10 mt10">
      	<div class="row">
          <!-- Access History sec. -->
          <div class="col-sm-12">
          	<div class="row">

              <!-- Heading -->
              <div class="col-sm-12 purple_bar">
              	<label>Access History</label>

                <div class="checkbox checkbox-inline pull-right">
                	<input type="checkbox" id="elem_viewAll" name="elem_viewAll" value="1" onclick="showAllC(this,'<?php echo $arr_is_note["id"]; ?>')">
                  <label for="elem_viewAll"  title="click View All to see all text including deleted.">View All</label>
               	</div>
            	</div>

              <!-- Access History -->
              <div class="col-sm-12">
              	<div class="row" style="height:250px;overflow-y:auto;">
										<table class="table table-striped table-bordered " >
											<thead>
												<tr class="grythead">
													<th class="col-sm-3">Date & Time</th>
													<th class="col-sm-2">Provider</th>
													<th class="col-sm-7">Confidential Text</th>
												</tr>
											</thead>
											<tbody id="divCT">
												<?php echo getCText("0",$arr_is_note["id"]); ?>
											</tbody>
										</table>
								</div>
							</div>

						</div>
        	</div>
      	</div>
     	</div>

      <div class="mainwhtbox mt10">
      	<div class="row">
          <div class="col-sm-12 text-center ad_modal_footer" id="module_buttons">
          	<?php if(trim($arr_is_note["confidential_text"]) == "" ){ ?>
            <input tabindex="2" type="button" class="btn btn-success" id="add_info" onClick="javascript:add_patient_info();" name="add_info" value="Save" />
          <?php }else if(!isset($readonly_rights) || $readonly_rights==false){ ?>
            <input tabindex="2" type="button" class="btn btn-success" id="update_info" onClick="javascript:update_patient_info();" name="update_info" value="Update" />
            <input tabindex="2" type="button" class="btn btn-danger" id="delete_info" onClick="javascript:delete_patient_info();" name="delete_info" value="Delete" />
            <?php } ?>
            <input tabindex="2" type="button" class="btn btn-danger" id="close_win" onClick="javascript:window.close();" name="close_win" value="Close" />
        	</div>
				</div>
     	</div>
		<?php
		}
		else{

			if($bl_authorized == true){
		?>
			<script>
				<?php if($str_message != ""){ ?>
				alert("<?php echo $str_message;?>");
				<?php
					$str_message = "";
				}
				?>
				function show_info(){
					if(document.getElementById("access_reason").value == ""){
						alert("Please provide reason.");
						return false;
					}else{
						document.frm_reason.submit();
					}
				}
		</script>

      <div class="mainwhtbox pd10">
      	<div class="row">

        <!-- Heading -->
        <div class="col-sm-12 purple_bar">
        	<label>Reason to access Patient Confidential Information</label>
       	</div>

        <div class="col-sm-12 pt10">
       		<div class="row">
          	<div class="alert alert-success">
            	<strong>You have been authorized to access this confidential information. Please provide the reason (required) to continue.</strong>
          	</div>
        	</div>
       	</div>

        <div class="col-sm-12 pt10">
        	<div class="row">
          	<form name="frm_reason" method="post" action="" target="_self">

            	<div class="col-sm-12">
              	<label>Give Reason</label>
                <textarea cols="40" rows="5" tabindex="1" id="access_reason" name="access_reason" class="form-control"></textarea>
            	</div>

              <div class="col-sm-12 mt10 text-center ad_modal_footer" id="module_buttons">
              	<input tabindex="2" type="button" class="btn btn-success" id="get_info" onClick="javascript:show_info();" name="get_info" value="Show Information" />
             	</div>

              <input type="hidden" id="hid_reason" name="hid_reason" value="process">
          	</form>
					</div>
       	</div>

    		</div>
    	</div>

			<?php } else { ?>

    	<script>
				<?php if($str_message != ""){ ?>
				alert("<?php echo $str_message;?>");
				<?php
					$str_message = "";
				}
				?>
				function get_info_access(){
						if(document.getElementById("access_pass").value == ""){
							alert("Please enter password.");
							return false;
						}else{
							document.frm_auth.submit();
						}
				}
			</script>

      <div class="mainwhtbox pd10">
      	<div class="row">

          <!-- Heading -->
          <div class="col-sm-12 purple_bar">
          	<label>Access Patient Confidential Information</label>
          </div>

          <div class="col-sm-12 pt10">
          	<div class="row">
            	<div class="alert alert-danger">
              	<strong>You are not authorized to access this confidential information. Please enter the password to get access.</strong>
             	</div>
           	</div>
        	</div>

          <div class="col-sm-12 pt10">

            	<form name="frm_auth" method="post" action="" target="_self">

              	<div class="row">
                	<div class="col-sm-6 col-sm-offset-3">
                  	<label>Enter Password</label>
                    <input tabindex="1" type="password" id="access_pass" name="access_pass" class="form-control">
                 	</div>
               	</div>

                <div class="clearfix mt20"></div>

                <div class="row mt20">
                  <div class="col-sm-12 text-center mt20 ad_modal_footer" id="module_buttons">
                    <input tabindex="2" type="button" class="btn btn-success " id="get_access" onClick="javascript:get_info_access();" name="get_access" value="Get Access" />
                  </div>
                </div>
                <input type="hidden" id="hid_auth" name="hid_auth" value="process">

              </form>

        	</div>

     		</div>
    	</div>

			<?php } ?>

    <?php } ?>
	</body>
</html>
<?php } ?>
