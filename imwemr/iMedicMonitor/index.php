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
File: index.php
Purpose: Landing page of iMedicMonitor. 
Access Type: Direct Access
*/
require_once("globals.php");
unset($_SESSION['login_sucess']);
$auth_fail=0;
if($_POST){
	if(xss_rem($_POST['u_n']) && xss_rem($_POST['p_w'])){
		$temp_u_n = xss_rem($_POST['u_n']);
		$user_name=addslashes(trim($temp_u_n));
		
		$temp_p_w = xss_rem($_POST['p_w']);
		$user_pass=hashPassword(addslashes(trim($temp_p_w)));
		if($_POST["hidd_multi_login"]=="yes"){
			$user_pass=trim($_POST['p_w']);	
		}
		$qry="select id,access_pri,user_type,follow_phy_id from users where username='".$user_name."' and password='".$user_pass."' and locked=0 and delete_status=0";
		$res=imw_query($qry)or die(imw_error());
		if(imw_num_rows($res)>0){
			$rs = imw_fetch_assoc($res);
			$_SESSION['login_sucess']		= "1";
			$_SESSION['login_fac']			= $_POST['l_facility'];
			$_SESSION['authId']	= $rs['id'];
			$_SESSION['logged_user_type']	= $rs['user_type'];
			$_SESSION['follow_phy_id']		= $rs['follow_phy_id'];
			$arr_privileges = unserialize(html_entity_decode($rs["access_pri"]));
			if($arr_privileges['priv_cl_work_view']=='1' || $arr_privileges['priv_admin']=='1'){
				$_SESSION['show_settings']="1";
			}else{
				$_SESSION['show_settings']="0";	
			}
		}else{
			$auth_fail=1;
		}
	}
}


if(!$_SESSION['login_sucess']){
	header("Location:login.php?login_status=$auth_fail");
}
 ?>
<!DOCTYPE HTML>
<html>
<head>
	<title>iMedicMonitor</title>
	<meta name="viewport" content="width=device-width; height=device-height; maximum-scale=1.4; initial-scale=1.0; user-scalable=yes" />
	<script>
		var options = "scrollbars=0,resizable=1,status=1,toolbar=0,menubar=0,location=0,width="+window.screen.availWidth+",height=";
		options += window.screen.availHeight;
		var wn_height = parseInt(window.screen.availHeight) + 70;
		var nav_name=navigator.appVersion;
		var date 	= new Date();
		local_tz = date.getTimezoneOffset();
		u = 'main.php';
		if (typeof(Storage) !== "undefined") {
			if(localStorage.imon_view) u = localStorage.imon_view;
		}
		url			= "local/"+u+"?wn_height="+wn_height+"&local_tz="+encodeURI(local_tz);

		var qq = window.open(url,"imedicMonitorFrame",options);
		qq.moveTo(0,0);
		if(self.name != "imedicMonitorFrame"){
			closeIT();
		}
		function closeIT(){
			!(window.ActiveXObject) && "ActiveXObject"
			function isIE11(){
				return !!navigator.userAgent.match(/Trident.*rv[ :]*11\./);
			}
			var ie7 = (document.all && !window.opera && window.XMLHttpRequest) ? true : false; 
			var ie11 = isIE11();
			if (ie7 || ie11){
				window.open("","_parent",""); 
				window.close(); 
			}else{
				this.focus();
				self.opener = this;
				self.close();
			}
		}
	</script>
</head>
<body>

</body>
</html>
