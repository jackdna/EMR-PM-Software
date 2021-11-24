<?php
/*
File: index.php
Coded in PHP7
Purpose: Login Screen
Access Type: Direct access
*/
$ignoreAuth = true;
require_once(dirname(__FILE__)."/../config/config.php");
$hidd_multi_login = "";
if($_POST["hidd_multi_login"]) {
	$hidd_multi_login = $_POST["hidd_multi_login"];
	
	$login_facility_id = $_POST["l_facility"];
	$qry_fac=imw_query("select id from in_location where idoc_fac_id='$login_facility_id'");
	$row_fac = imw_fetch_array($qry_fac);
	if(imw_num_rows($qry_fac)==0 || $row_fac['id']==0)
	{
		$qry_pos_fac=imw_query("select in_location.id as loc_id
						from in_location join facility on (in_location.pos=facility.fac_prac_code)
						where facility.id='$login_facility_id' and in_location.del_status = '0' group by in_location.id order by loc_id ASC LIMIT 0,1");
		$row_pos_fac = imw_fetch_array($qry_pos_fac);	
		if($row_pos_fac['loc_id']>0){
			$_POST["faclity"] = $row_pos_fac['loc_id'];
		}else{
			$qry_pos_fac=imw_query("select id as loc_id
						from in_location where hq='1' and del_status = '0'");
			$row_pos_fac = imw_fetch_array($qry_pos_fac);	
			$_POST["faclity"] = $row_pos_fac['loc_id'];
		}
	}else $_POST["faclity"] = $row_fac['id'];
}
require_once($GLOBALS['DIR_PATH']."/login/login_functions.php");
require_once($GLOBALS['DIR_PATH']."/library/classes/common_functions.php");
//ini_set("session.cookie_httponly", 1);
//required constants
define("DEFAULT_CAPTCHA_COUNT", 3);
define("DEFAULT_MAX_LOGIN_ATTEMPT", 3);
define("DEFAULT_SESSION_TIMEOUT", 3600);
define("DEFAULT_PASS_EXPIRY_DAYS", 90);
$hide_captcha = "hide";
$status = false;
$u="e_r_1";
if(preg_match('/[^a-zA-Z0-9]/',$_POST['u_n'])!=1)
{
	$u = (isset($_POST["u_n"]) && !empty($_POST["u_n"])) ? trim(imw_real_escape_string($_POST['u_n'])) : '';
}
	$p = (isset($_POST["p_w"]) && !empty($_POST["p_w"])) ? trim($_POST['p_w']) : '';
	$a = (isset($_GET["act"]) && !empty($_GET["act"])) ? trim($_GET['act']) : '';

if($a=='logout'){
	app_logout();
	exit;
}

if(!empty($u) && !empty($p)){
	$return = check_wrong_attempts($u,$p,$hidd_multi_login);
	if(!$return){
		if($_SESSION["CAPTCHACOUNT"] >= constant("DEFAULT_CAPTCHA_COUNT")){
			$hide_captcha = "";
		}
		$script = '<script type="text/javascript">alert("Authentication Failed!");</script>';
	}elseif($return==='restricted'){
		$script = '<script type="text/javascript">alert("Permission denied!");</script>';
	}else{
		list($status,$msg) = app_check_pass_expiry();
		$script = '<script type="text/javascript">alert("'.$msg.'");</script>';
	}
}
$group_qry = imw_query("select groups_new.name from groups_new 
	join facility on facility.default_group=groups_new.gro_id where facility.facility_type='1'");
$get_group = imw_fetch_array($group_qry);
//pre($_SESSION);
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0; charset=UTF-8" />
<title>Optical Login Page</title>
<meta http-equiv="imagetoolbar" content="no" />
<link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script type="text/javascript" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<?php echo $script;if($status){app_login_success();}?>
<script type="text/javascript">
var d = document.forms; 
function valid_login(){
	var msg = ''; 
	var callback = ''; 
	var p = d.login.p_w.value; 
	var len = p.length;

	if(d.login.u_n.value == ''){
		msg = 'Please Enter Username.'; 
		callback = 'd.login.u_n.focus()';
	}else if(len < 1){
		msg = 'Please Enter Password.'; 
		callback = 'd.login.p_w.focus()';
	}
	else if($("#faclity").val()=="")
	{
		msg = 'Please Select Facility.'; 
	}

	if(msg != ''){
		alert(msg);
		eval(callback);
		msg = '';
		return false;
	}
}
$(document).ready(function(e) {
	log_height();
    d.login.u_n.focus();
	show_clock();
});

function log_height(){
	inHeight = pageHeight()-235;
	$("#logintbl").css('height',inHeight+"px");
}

function show_clock(){var C = new Date(); var h = C.getHours(); var m = C.getMinutes(); var s = C.getSeconds(); var dn = "PM"; if (h < 12) dn = "AM"; if (h > 12) h = h-12; if (h == 0) h = 12; if (h <=9 ) h = "0" + h; if (m <=9 ) m = "0" + m; if(s <= 9) s = "0" + s; var tm = h + ":" + m + ":" + s + " " + dn;$("#tick2").html("<span style='font-weight:bold;'>" + tm + "</span>");setTimeout("show_clock()", 1000);}

function pageHeight() { return window.innerHeight != null? window.innerHeight : document.documentElement && document.documentElement.clientHeight ? document.documentElement.clientHeight : document.body != null? document.body.clientHeight : null;}

$(document).unbind('keydown').bind('keydown', function (event) {
    var doPrevent = false;
    if (event.keyCode === 8) {
        var d = event.srcElement || event.target;
        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE')) 
             || d.tagName.toUpperCase() === 'TEXTAREA') {
            doPrevent = d.readOnly || d.disabled;
        }
        else {
            doPrevent = true;
        }
    }

    if (doPrevent) {
        event.preventDefault();
    }
});
</script>
</head>
<body>
<div class="container">
	<div class="header" style="height:text-align:center;">
    	<table cellpadding="0" cellspacing="0" width="100%">
        	<tr>
            	<td style="width:500px;">
                    <img src="../images/imedicware_logo.png" style="margin:5px 25px;">
                </td>
                <td class="user_name" style="width:685px; font-weight:bold;">
                	<?php echo stripcslashes($get_group['name']); ?>
                </td>
                <td>
                	<img src="../images/inventory_system.png" style="margin:5px 25px;">
                </td>
                <td>
                	<img src="../images/imwemr.png" style="margin:5px 25px;">
                </td>
            </tr>
        </table>
	</div>
    <div class="pagecontent">	
        <table class="table_collapse" id="logintbl"><tr><td style="text-align:center;">
        	<form onSubmit="return valid_login();" name="login" method="post">
            <table class="table_collapse" border="0" style="width:450px; display:inline-table;">
                <tbody class="module_border">
                <tr style="background:url(../images/3.png);">
                    <td align="left" style="color:#fff; padding:10px; font-weight:bold;" colspan="2">
                        Login Form
                    </td>  
                </tr>
                <tr>
                    <td style="padding-right:10px; padding-top:6px;" align="right"><span class="module_label">Username : </span></td> 
                    <td align="left" class="pt15"><input type="text" name="u_n" style="width:200px; height:25px; border-radius:10px; padding-left:10px;"></td>
                </tr>
                <tr>
                    <td style="padding-right:12px; padding-top:6px;" align="right"><span class="module_label">Password : </span></td>
                    <td align="left" class="pt15"><input type="password" class="form_fields" name="p_w" style="width:200px; height:25px; border-radius:10px; padding-left:10px;" autocomplete="off"></td>
                </tr>
				<tr>
                    <td style="padding-right:12px; padding-top:6px;" align="right"><span class="module_label">Facility : </span></td>
                    <td align="left" class="pt15">
						<select name="faclity" id="faclity" style="width:210px; height:30px; border-radius:9px;">
							<option value="">Select Facility</option>
							<?php $fac_name_qry = imw_query("select id, loc_name, hq from in_location where del_status='0' and loc_name!='' order by loc_name asc");
								  while($fac_name_row = imw_fetch_array($fac_name_qry)) { 
								  ?>
							<option value="<?php echo $fac_name_row['id']; ?>" <?php echo ($fac_name_row['hq'])?'selected=selected':'';?>><?php echo $fac_name_row['loc_name']; ?></option>
							<?php } ?>
						</select>
					</td>
                </tr>
                <tr class="<?php echo $hide_captcha;?>">
                    <td align="right"><img style="margin:5px 10px;" src="<?php echo $GLOBALS['WEB_PATH'];?>/library/inc_captcha.php" align="absmiddle">
                        </td>
                    <td align="left" class="pt15" valign="top"><input<?php if($hide_captcha=='hide'){echo ' disabled';}?> type="text" name="vericode" size="6" maxlength="6" style="width:200px; height:25px; border-radius:10px;padding-left:10px;" /></td>                        
                </tr>  
                <tr class="<?php echo $hide_captcha;?>">
                    <td colspan="2"><span class="module_label"><small>Please enter the "Verification Code" as shown in the image.</small></span></td>
                                 
                </tr>                        
                <tr>
                    <td align="center" colspan="2">
                        <div class="btn_cls">
                            <input type="submit" name="loginbtn" value="Login" />                        
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        </form>
    	</td></tr></table>
<div style="font-size:90%; color:#f00; text-align:center; padding:10px; margin-bottom:50px;"><b>Please Note: "Login with your own credentials."</b></div>
	<div class="page_footer_bar">
    	<span class="fr" style="display:inline-block;"><?php echo date('D M d, Y');?> <span id="tick2" style="margin-left:20px;"></span></span>
    	<span style="font-size:80%;">MIT License &copy; <?php echo date('Y');?>. imwemr &reg; All rights reserved.</span>
   	</div>
    
	</div>
</div>
</body>
</html>