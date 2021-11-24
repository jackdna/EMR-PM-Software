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
require_once("../admin_header.php");

if($_REQUEST['cpoe_severity_save']!=""){
	$cpoe_severity=$_REQUEST['cpoe_severity'];
	imw_query("update copay_policies set cpoe_severity='$cpoe_severity'");
	$msg = 'Record Saved Succesfully';
}

/********GETTING CHC SETTINS****************/
$errMsg = '';
//--- GET ERX STATUS AND CHC ACCESS URL -------
$res1 = imw_query("select Allow_erx_medicare, EmdeonUrl from copay_policies LIMIT 0,1");
$copay_policies_rs = imw_fetch_assoc($res1);
$Allow_erx_medicare = trim($copay_policies_rs['Allow_erx_medicare']);
$EmdeonUrl = trim($copay_policies_rs['EmdeonUrl']);


if(strtolower($Allow_erx_medicare) != 'yes'){
	$errMsg = 'eRx is not allowed. Check E/RX SETTINGS with HQ Facility.';
}else if($EmdeonUrl==''){
	$errMsg = 'CHC Clinician URL not entered. Check Test/Production settings with HQ Facility.';
}

if($errMsg==''){
	$res2 = imw_query("select eRx_user_name, erx_password,eRx_facility_id,eRx_prescriber_id,lname,fname,mname FROM users WHERE id = '".$_SESSION['authId']."' LIMIT 0,1");
	$phyQryRes = imw_fetch_assoc($res2);
	$eRx_user_name = trim($phyQryRes['eRx_user_name']);
	$erx_password = trim($phyQryRes['erx_password']);
	$eRx_facility_id = trim($_SESSION['login_facility_erx_id']);
	
	if($eRx_user_name==''){
		$errMsg = 'You don\'t have CHC Clinician Credentials assigned.';
	}else if($eRx_user_name!='' && $eRx_facility_id==''){
		$errMsg = 'eRX facility ID not found linked with Login Facitliy.';
	}
}

$main_btn_disabled='';
if($errMsg!=''){$main_btn_disabled=' disabled';}
?>
	<script type="text/javascript">
		function changeSelection(fileName){		 
			if(fileName){
				var frm_data = 'ajax_request=yes&erx_request='+fileName;
				var url = top.JS_WEB_ROOT_PATH+'/interface/admin/eRx_preferences/ajax.php';
				var parentWid = parent.document.body.clientWidth;
				var parenthei = parent.document.body.clientHeight;
				
				$.ajax({
					url:url,
					data:frm_data,
					type:'POST',
					success:function(response){
						var win_url = response;
						if(win_url != ''){
							top.popup_win(win_url,'width='+parentWid+',height='+parenthei+'');
						}
					}
				});
			}
		}	
	</script>
<body>
<div class="whtbox">
<br />
<div class="panel panel-defult">
	<?php if($errMsg!=''){?><div style="color:#f00; text-align:center; font-weight:bold; font-size:13px;"><?php echo $errMsg;?></div><br /><?php }?>
	<div class="row panel-body">

		<div class="col-sm-4 text-right">
			<input <?php echo $main_btn_disabled;?> type="button" class="btn btn-primary" onClick="changeSelection('DUR');" name="DUR" value=" DUR Preferences" class="dff_button" id="DUR_Preferences">
		</div>
		<div class="col-sm-4 text-center">
			<input <?php echo $main_btn_disabled;?> type="button" class="btn btn-primary" onClick="changeSelection('Pharmacy');" name="Pharmacy" value=" Pharmacy Preferences" class="dff_button" id="Pharmacy">
		</div>
		<div class="col-sm-4 text-left">
			<input <?php echo $main_btn_disabled;?> type="button" class="btn btn-primary" onClick="changeSelection('Prescription');" name="DUR" value=" Prescription Preferences" class="dff_button" id="Prescription_Preferences">
		</div>
	</div>
<br />
<?php
$pol_qry=imw_query("select cpoe_severity from copay_policies");
$pol_row=imw_fetch_array($pol_qry);
?>
	<form action="index.php" method="post" name="frm">
		<input type="hidden" name="cpoe_severity_save" value="1">
		<div class="row panel-body">
			<div class="col-sm-4 text-right" style="padding-top:7px;">
				<label for="cpoe_severity">Severity Level:</label>
			</div>
			<div class="col-sm-4">
				<select class="selectpicker" name="cpoe_severity" id="cpoe_severity" data-width="100%" data-title="Select Level">
					<option value="">Severity Level</option>
					<option value="1" <?php if($pol_row['cpoe_severity']==1){echo "selected";} ?>>1 - Minor</option>
					<option value="2" <?php if($pol_row['cpoe_severity']==2){echo "selected";} ?>>2 - Moderate</option>
					<option value="3" <?php if($pol_row['cpoe_severity']==3){echo "selected";} ?>>3 - Severe</option>
				</select>
			</div>
			<div class="col-sm-4">
				<input type="submit" name="save_cpoe" value="Save" class="btn btn-success" id="save_cpoe">
			</div>
		</div>
	</form>
</div>
</div>
<script type="text/javascript">
	set_header_title('ERx Preferences');
	show_loading_image('none');
</script>
<?php 
	if(trim($msg)) {
		echo '<script type="text/javascript">top.alert_notification_show("'.$msg.'");</script>';
	}
	require_once('../admin_footer.php');
?>