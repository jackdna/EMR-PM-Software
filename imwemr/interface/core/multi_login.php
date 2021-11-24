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
*/
require_once(dirname(__FILE__).'/../../config/globals.php');
$app_name = $_REQUEST["app_name"];
$frmAction = $srcFile = '';
switch($app_name) {
	case 'imedicmonitor':
		$frmAction = $GLOBALS['webroot']."/iMedicMonitor/index.php";
		$srcFile = $GLOBALS['fileroot']."/iMedicMonitor/index.php";
	break;
	case 'optical':
		$frmAction = $GLOBALS['optical_directory_location']."/".$GLOBALS['optical_directory_name']."/login/index.php";
		$srcFile = $GLOBALS['optical_directory_location']."/".$GLOBALS['optical_directory_name']."/login/index.php";
	break;
	case 'financial_dashboard':
		$frmAction = $GLOBALS['webroot']."/fd/index.php";
		$srcFile = $GLOBALS['fileroot']."/fd/index.php";
	break;
    case 'iasclink':
		$frmAction = $GLOBALS['iasclink_directory_location']."/".$GLOBALS['iasclink_directory_name']."/index.php";
		$srcFile = $GLOBALS['iasclink_directory_location']."/".$GLOBALS['iasclink_directory_name']."/index.php";
	break;
}

$qry = "SELECT id, username, `password` AS pass_new, sso_identifier FROM users WHERE id= '".$_SESSION["authId"]."' AND locked='0' AND delete_status='0'";
$res = imw_query($qry) or die(imw_error());
if(imw_num_rows($res)>0) {
	$row = imw_fetch_assoc($res);
	$username = $row["username"];
	$pass_new = $row["pass_new"];
	$sso_identifier = $row["sso_identifier"];	
}
$login_facility = $_SESSION["login_facility"];

//
if(!empty($sso_identifier)){
	include($GLOBALS['incdir']."/sso/SSOS.php");
	$ossos = new SSOS();
	$ossos->login($frmAction, $sso_identifier, $_SESSION["authId"],$app_name);
}

?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Multi Login</title>
	<meta name="viewport" content="width=device-width; height=device-height; maximum-scale=1.4; initial-scale=1.0; user-scalable=yes" />
</head>
<body>
<form name="frm_multi_login" enctype="multipart/form-data" method="post" action="<?php echo $frmAction;?>" autocomplete="off">
	<input type="hidden" name="hidd_multi_login" id="hidd_multi_login" value="yes">
    <input type="hidden" name="u_n" id="u_n" value="<?php echo $username;?>">
    <input type="hidden" name="p_w" id="p_w" value="<?php echo $pass_new;?>">
    <input type="hidden" name="username" id="username" value="<?php echo $username;?>"><!--FOR FD LOGIN-->
    <input type="hidden" name="pwd" id="pwd" value="<?php echo $pass_new;?>"> <!--FOR FD LOGIN-->
    <input type="hidden" name="l_facility" id="l_facility" value="<?php echo $login_facility;?>">
    <input type="hidden" name="faclity" id="faclity" value="<?php echo $login_facility;?>">
</form>
</body>
	<script>
		window.focus();
		document.frm_multi_login.submit();
	</script>

</html>
