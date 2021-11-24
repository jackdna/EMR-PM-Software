<?php
/*
File: remoteConnect.php
Coded in PHP7
Purpose: Creat Connection Optical to IDoc.
Access Type: Include file
*/

require_once 'config/config.php';
$server_name = $GLOBALS['IMW_WEB_LOGIN_PATH'];
$user_name = $_SESSION['authUser'];
$user_password = $_SESSION['authPass'];
$send_data=$_SESSION['patient_session_id'].'_'.$_REQUEST['encounter_id'];
$pro_fac_id = $_SESSION['pro_fac_id'];

$qry_fac=imw_query("select idoc_fac_id from in_location where id='$pro_fac_id'");
$row_fac = imw_fetch_array($qry_fac);
if(imw_num_rows($qry_fac)==0 || $row_fac['idoc_fac_id']==0)
{
	$qry_pos_fac=imw_query("select pos_tbl.facility
							from in_location join pos_facilityies_tbl on in_location.pos=pos_facilityies_tbl.pos_facility_id
							join pos_tbl on pos_tbl.pos_id = pos_facilityies_tbl.pos_id
							where in_location.id='$pro_fac_id'");
	$row_pos_fac = imw_fetch_array($qry_pos_fac);		
	$remote_fac=$row_pos_fac['facility'];		
}else $remote_fac=$row_fac['idoc_fac_id'];		
?>

<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>imwemr: Remote Connection </title>
<style type="text/css">
	div.sever_connect_process
	{
		font-family:Arial, Helvetica, sans-serif;
		font-size:15px;
		padding:20px;
		background-color:#333;
		color:#fff;
		word-spacing:2px;
		letter-spacing:1.5px;
		border:1px 0px 1px 0px solid #000000;	
	}
	.hide_form
	{
		display:none;	
	}
</style>
<script type="text/javascript">
	window.onload = function()
	{
		if(document.getElementById('server_name').value=="")
		{
			alert("Please specify the server name");
			window.close();
			return false;
		}
		document.formRedirect.submit();
	}
</script>
</head>

<body style="background-color:#333;">
	<div class="sever_connect_process" id="server_connect_div">
    	<?php echo 'Connecting to '.$server_name.' ............ <br/> Please wait and do not refresh the window'; ?>
        
    </div>
	<form class="hide_form" name="formRedirect" method="post" action="<?php echo $server_name; ?>">
       <input type="text" name="server_name" id="server_name" value="<?php echo $server_name; ?>" />
       <input type="text" name="u_n" id="authProviderName" value="<?php echo $user_name; ?>" />
       <input type="text" name="p_w" id="authPass" value="<?php echo $user_password; ?>" />
       <input type="hidden" name="redMedMod" id="redMedMod" value="redirectMed" />
       <input type="hidden" name="redOptMod" value="redOptMod_<?php echo $send_data; ?>" />
       <input type="submit" name="sub" value="Sub" />
	   <input type="hidden" name="remote_fac" value="<?php echo $remote_fac; ?>" />
	   <input type="hidden" name="remote_opt_loc_id" value="<?php echo $pro_fac_id; ?>" />
   </form>    


</body>
</html>