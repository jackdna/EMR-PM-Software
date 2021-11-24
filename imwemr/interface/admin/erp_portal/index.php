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


require_once(dirname(__FILE__).'/../../../config/globals.php');
$msg='';
set_time_limit(0);

//upload recalls
$erp_error=array();
if(isset($_GET['op']) && $_GET['op']==='up_recalls'){
	if( isERPPortalEnabled() )
	{
		try {
			include_once($GLOBALS['fileroot']."/library/erp_portal/erp_portal_core.php");
			include_once($GLOBALS['srcdir'].'/erp_portal/recalls.php');
			$oIncSecMsg = new Recalls();
			$oIncSecMsg->upload();
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
	}
	exit();
}


if(isset($_POST['action']) && $_POST['action']==='save'){

	$erp_api_id = (int)trim($_POST['erp_api_id']);
	$account_id = xss_rem(trim($_POST['account_id']));
	$account_number = xss_rem(trim($_POST['account_number']));
	$synchronization_username = xss_rem(trim($_POST['synchronization_username']));
	$synchronization_password = xss_rem(trim($_POST['synchronization_password']));

	$sql = '';
	$where = '';
	if($erp_api_id === 0){
		$sql = 'INSERT INTO ';
    }else{
		$sql = 'UPDATE';
		$where = '`id`='.$erp_api_id;
	}

    if( isERPPortalEnabled() )
	{
        $linkedin_url = imw_real_escape_string($_POST['linkedin_url']);
        $yahoo_url = imw_real_escape_string($_POST['yahoo_url']);
        $google_plus_url = imw_real_escape_string($_POST['google_plus_url']);
        $facebook_url = imw_real_escape_string($_POST['facebook_url']);
        $adv_appointment_days = $_POST['adv_appointment_days'] ? $_POST['adv_appointment_days'] : '' ;
        $use_forms = $_POST['use_forms'] ?? 0;
        $online_appt = $_POST['online_appt'] ?? 0;
		$portal_def_user=$_POST['portal_def_user'] ?? 0;
		$portal_def_facility=$_POST['portal_def_facility'] ?? 0;
		$portal_def_medication=$_POST['portal_def_medication'] ?? 0;
    }
	$sql .= " `erp_api_credentials` SET
				`account_id`='".$account_id."',
				`account_number`='".$account_number."',
				`synchronization_username`='".$synchronization_username."',
				`synchronization_password`='".$synchronization_password."' ";
    if( isERPPortalEnabled() && $sql!='')
	{
	$sql .= ", `linkedin_url`='".$linkedin_url."',
				`yahoo_url`='".$yahoo_url."',
				`google_plus_url`='".$google_plus_url."',
				`facebook_url`='".$facebook_url."',
				`adv_appointment_days`='".$adv_appointment_days."',
				`use_forms`='".$use_forms."',
				`online_appt`='".$online_appt."',
				`portal_def_user`='".$portal_def_user."',
				`portal_def_facility`='".$portal_def_facility."',
				`portal_def_medication`='".$portal_def_medication."'
				";
    }
	imw_query($sql);
	$msg = 'Record Saved Succesfully';

	if( isERPPortalEnabled() )
	{
		try {
			$data = [];
			$data['accountId'] = $account_id;
			$data['linkedInUrl'] = $linkedin_url;
			$data['yahooUrl'] = $yahoo_url;
			$data['googlePlusUrl'] = $google_plus_url;
			$data['facebookUrl'] = $facebook_url;
			$data['appointmentAllowedTimeInAdvance'] = $adv_appointment_days;
			$data['usesForms'] = $use_forms ? true : false;
			$data['onlineAppointmentsActive'] = $online_appt ? true : false;

			// send request to delete location from Eye Reach Patient API
			require_once(dirname(__FILE__) . "/../../../library/erp_portal/account.php");
			$objAcc = new AccountSettings();
			$objAcc->addUpdateSettings($data);
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
		
	}
}

$creds = array();
$sql = 'SELECT * FROM `erp_api_credentials`';
$resp = imw_query($sql);
if($resp && imw_num_rows($resp)>0){
	$creds = imw_fetch_assoc($resp);
}

require_once('../admin_header.php');
?>

<body>
<style>.adminerp_syncbtn{border-bottom:1px solid #1b9e95;text-transform:uppercase;padding:8px;font-family:'robotobold';color: #616161;font-weight:500;}
	.adminnw .btn{margin-bottom: 2px;}
</style>
<div class="whtbox">
	<div class="section" style="height:<?php print $_SESSION['wn_height']-336?>px;">
		<form method="POST" action="" id="erp_api_frm" >
			<table class="table table-bordered">
				<tbody>
					<tr>
						<td style="width:250px;"><label>Account ID</label></td>
						<td>
							<input class="form-control" type="text" name="account_id" id="account_id" value="<?php echo (isset($creds['account_id']))?$creds['account_id']:''; ?>">
							<input type="hidden" name="erp_api_id" id="erp_api_id" value="<?php echo (isset($creds['id']))?$creds['id']:''; ?>">
							<input type="hidden" name="action" id="action" value="">
						</td>
					</tr>
					<tr>
						<td><label>Account Number</label></td>
						<td>
							<input class="form-control" type="text" name="account_number" id="account_number" value="<?php echo (isset($creds['account_number']))?$creds['account_number']:''; ?>">
						</td>
					</tr>
					<tr>
						<td><label>Synchronization Username</label></td>
						<td>
							<input class="form-control" type="text" name="synchronization_username" id="synchronization_username" value="<?php echo (isset($creds['synchronization_username']))?$creds['synchronization_username']:''; ?>">
						</td>
					</tr>
					<tr>
						<td><label>Synchronization Password</label></td>
						<td>
							<input class="form-control" type="password" name="synchronization_password" id="synchronization_password" value="<?php echo (isset($creds['synchronization_password']))?$creds['synchronization_password']:''; ?>">
						</td>
					</tr>
				</tbody>
			</table>

            <?php if (isERPPortalEnabled() && count($creds) > 0) { ?>
			<table class="table table-bordered table-hover adminnw">
				<thead >
					<tr><th colspan="2"><label>Settings</label></th></tr>
				</thead>
				<tbody>
					<tr>
						<td style="width:250px;" ><label>LinkedIn Url</label></td>
						<td><input class="form-control" type="text" name="linkedin_url" id="linkedin_url" value="<?php echo (isset($creds['linkedin_url']))?$creds['linkedin_url']:''; ?>"></td>
					</tr>

					<tr>
						<td><label>Yahoo Url</label></td>
						<td><input class="form-control" type="text" name="yahoo_url" id="yahoo_url" value="<?php echo (isset($creds['yahoo_url']))?$creds['yahoo_url']:''; ?>"></td>
					</tr>

					<tr>
						<td><label>Google Plus Url</label></td>
						<td><input class="form-control" type="text" name="google_plus_url" id="google_plus_url" value="<?php echo (isset($creds['google_plus_url']))?$creds['google_plus_url']:''; ?>"></td>
					</tr>

					<tr>
						<td><label>Facebook Url</label></td>
						<td><input class="form-control" type="text" name="facebook_url" id="facebook_url" value="<?php echo (isset($creds['facebook_url']))?$creds['facebook_url']:''; ?>"></td>
					</tr>

					<tr>
						<td><label>Advance Appointment Days</label></td>
						<td><input class="form-control" type="text" name="adv_appointment_days" id="adv_appointment_days" value="<?php echo (isset($creds['adv_appointment_days']))?$creds['adv_appointment_days']:''; ?>"></td>
					</tr>


					<tr>
						<td><label>Uses Forms</label></td>
						<td>
							<div class="checkbox">
								<input type="checkbox" name="use_forms" id="use_forms" <?php echo $creds['use_forms'] ? 'checked' : '';?> value="1">
								<label for="use_forms"></label>
							</div>
						</td>
					</tr>

					<tr>
						<td><label>Online Appointments</label></td>
						<td>
							<div class="checkbox">
								<input type="checkbox" name="online_appt" id="online_appt" <?php echo $creds['online_appt'] ? 'checked' : '';?> value="1">
								<label for="online_appt"></label>
							</div>
						</td>
					</tr>


				</tbody>
			</table>
			
			<?php 
					$users_option='';
                    $sql = "select `id`,`fname`,`mname`,`lname`,`username`,`user_type`,`Enable_Scheduler` 
                            from `users` 
                            where (user_type IN(1,2,7,8,9,10,11,12,14) OR Enable_Scheduler = '1')
                            and `delete_status` = '0' 
                            order by `lname` ASC";
                    $sql_rs = imw_query($sql);
                    if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                        $users_option .= '<option value="">- Select User -</option>';
                        while ($row1 = imw_fetch_assoc($sql_rs)) {
                            $sel1=($row1['id']==$creds['portal_def_user'])?' selected="selected" ':'';
                            $prov_name = core_name_format($row1['lname'], $row1['fname'], $row1['mname']);
                            $users_option .= '<option value="'.$row1['id'].'"  '.$sel1.'>' . $prov_name . '</opiton>';
                        }
                    }
                    
                    $facility_option='';
                    $fac_qry=imw_query("select id,name from facility order by name");
                    if ($fac_qry && imw_num_rows($fac_qry) > 0) {
                        $facility_option .= '<option value="">- Select Facility -</option>';
                        while($fac_row=imw_fetch_array($fac_qry)){
                            $sel2=($fac_row['id']==$creds['portal_def_facility'])?' selected="selected" ':'';
                            $facility_option .= '<option value="'.$fac_row['id'].'"  '.$sel2.'>'.trim($fac_row['name']).'</opiton>';
                        }
                    }
			
			
			?>
			<table class="table table-bordered table-hover adminnw">
				<thead >
					<tr><th colspan="4"><label>Default Provider and Facility For Payment Received From Portal</label></th></tr>
				</thead>
				<tbody>
					<tr>
						<td><label for="portal_def_user">Default Provider</label></td>
						<td><select name="portal_def_user" id="portal_def_user" class="form-control minimal" style="width:180px">
							<?php echo $users_option; ?>
						</select></td>
						<td><label for="portal_def_facility">Default Facility</label></td>
						<td><select name="portal_def_facility" id="portal_def_facility" class="form-control minimal" style="width:200px">
							<?php echo $facility_option; ?>
						</select></td>
						<td><label for="portal_def_medication">Default Medication</label></td>
						<td>
							<select name="portal_def_medication" id="portal_def_medication" class="form-control minimal" style="width:200px">
								<option value="0" <?php if($creds['portal_def_medication'] == 0){ echo 'selected';} ?>>- Both Medication -</option>
								<option value="1" <?php if($creds['portal_def_medication'] == 1){ echo 'selected';} ?>>Systemic Medication</option>
								<option value="4" <?php if($creds['portal_def_medication'] == 4){ echo 'selected';} ?> >Ocular Medication</option>
							</select>
						</td>
					</tr>

				</tbody>
			</table>
            <?php } ?>
		</form>

    <?php if (isERPPortalEnabled() && count($creds) > 0) { ?>
        <div class="adminnw">
            <div class="adminerp_syncbtn" ><label>Sync Data</label></div>

                <div class="pd10">
					<button type="button" class="btn btn-success" onclick="upload_existing_locations();" title="Upload Existing Locations To imwemr Portal.">Upload Existing Locations</button>
                    <button type="button" class="btn btn-success" onclick="upload_existing_doctors();" title="Upload Existing Doctors To imwemr Portal.">Upload Existing Doctors</button>
                    <button type="button" class="btn btn-success" onclick="sync_master_data('races');" title="Sync Races With imwemr Portal.">Races</button>
                    <button type="button" class="btn btn-success" onclick="sync_master_data('ethnicity');" title="Sync Races With imwemr Portal.">Ethnicity</button>
                    <button type="button" class="btn btn-success" onclick="sync_master_data('marital_status');" title="Sync Races With imwemr Portal.">Marital Status</button>
                    <button type="button" class="btn btn-success" onclick="sync_master_data('gender');" title="Sync Races With imwemr Portal.">Gender</button>
                    <button type="button" class="btn btn-success" onclick="sync_master_data('patient_relations');" title="Sync Patient Relations With imwemr Portal.">Patient Relations</button>
					<button type="button" class="btn btn-success" onclick="upload_existing_patients();" title="Upload Existing Patients To imwemr Portal.">Upload Existing Patients</button>

					<button type="button" class="btn btn-success" onclick="sync_master_data('schedule_statuses');" title="Upload Active Schedule Statuses To imwemr Portal.">Upload Active Schedule Statuses</button>
                    <button type="button" class="btn btn-success" onclick="upload_existing_appointments();" title="Existing Appointments To imwemr Portal.">Upload Existing Appointments</button>
										<button type="button" class="btn btn-success" onclick="upload_existing_recalls();" title="Existing Recalls To imwemr Portal.">Upload Existing Recalls</button>

                    <button type="button" class="btn btn-success" onclick="sync_master_data('slot_procedures');" title="Upload Slot Proedures To imwemr Portal.">Upload Appointments Request Reasons</button>
					<button type="button" class="btn btn-success" onclick="sync_master_data('allergy_severity');" title="Allergy Severity.">Allergy Severity</button>
					<button type="button" class="btn btn-success" onclick="sync_master_data('allergies');" title="Sync Allergies With imwemr Portal.">Allergies</button>
					<button type="button" class="btn btn-success" onclick="sync_master_data('medication_master');" title="Sync Medication Master With imwemr Portal.">Medication Master</button>
					<button type="button" class="btn btn-success" onclick="sync_master_data('route_master');" title="Sync Route Master With imwemr Portal.">Route Master</button>
<!--                    <button type="button" class="btn btn-success" onclick="sync_master_data('all');" title="Sync Races With imwemr Portal.">All</button>-->
					<button type="button" class="btn btn-success" onclick="sync_master_data('surgery');" title="Surgery">Surgery</button>
				</div>

        </div>
    <?php } ?>

	</div>
</div>

<script type="text/javascript">
    var webroot = '<?php echo $GLOBALS['webroot'];?>'
    <?php if(trim($msg)!='') { ?>
        top.fAlert("<?php echo $msg;?>");
    <?php } ?>

	var ar = [["save","Save","top.fmain.saveData();"]];
	top.btn_show("ADMN",ar);
	set_header_title('Eye Reach Patient Portal Settings');
	show_loading_image('none');

	$(document).ready(function(){
		/*Hide Loader Image*/
		parent.show_loading_image('none');
	});

	function saveData(){
		parent.show_loading_image('');
		$('#action').val('save');
		$('#erp_api_frm').submit();
	}

    function upload_existing_doctors() {
        top.show_loading_image('show', '', "Please Wait Uploading existing doctors...");
        $.ajax({
            url: webroot+'/library/erp_portal/create_existing_doctors.php',
            type: 'POST',
            success: function (resultData)
            {
                top.show_loading_image('hide');
                top.fAlert(resultData);
                return false;
            }
        });
    }

	function upload_existing_locations() {
		top.show_loading_image('show', '', "Please wait while uploading existing locations...");
		var xhr = new XMLHttpRequest();
		var url = webroot+'/library/erp_portal/create_locations.php';
		var last_index = 0;
		xhr.open("GET", url, true);
		xhr.onprogress = function () { // DO NOTHING
		};
		xhr.onload = function () {
			top.show_loading_image('hide');
			top.alert_notification_show(xhr.responseText);
		};
		xhr.send();
	}

	function upload_existing_patients(s_index,page,msg) {
		s_index = s_index || 0;
		page = page || 1;
		msg = msg || "Please wait while uploading existing patients...";

		top.show_loading_image('show', '', msg);
		var xhr = new XMLHttpRequest();
		var url = webroot+'/library/erp_portal/create_patients.php?index='+s_index+'&page='+page;
		xhr.open("GET", url, true);
		xhr.onprogress = function () { // DO NOTHING
		};
		xhr.onload = function () {
			var s = JSON.parse(xhr.responseText);
			if( s.last_page === true )
			{
				top.show_loading_image('hide');
				top.alert_notification_show(s.msg);
			}
			else
			{
				upload_existing_patients(s.next_index,s.next_page,s.msg);
			}
		};
		xhr.send();
	}


	function upload_existing_appointments(s_index,page,msg) {
		s_index = s_index || 0;
		page = page || 1;
		msg = msg || "Please wait while uploading existing appointments...";

		top.show_loading_image('show', '', msg);
		var xhr = new XMLHttpRequest();
		var url = webroot+'/library/erp_portal/create_appointments.php?index='+s_index+'&page='+page;
		xhr.open("GET", url, true);
		xhr.onprogress = function () { // DO NOTHING
		};
		xhr.onload = function () {
			var s = JSON.parse(xhr.responseText);
			if( s.last_page === true )
			{
				top.show_loading_image('hide');
				top.alert_notification_show(s.msg);
			}
			else
			{
				upload_existing_appointments(s.next_index,s.next_page,s.msg);
			}
		};
		xhr.send();
	}

	function upload_existing_recalls(s_index,page,msg) {
		s_index = s_index || 0;
		page = page || 1;
		msg = msg || "Please wait while uploading existing recalls...";

		top.show_loading_image('show', '', msg);
		var xhr = new XMLHttpRequest();
		var url = webroot+'/interface/admin/erp_portal/index.php?op=up_recalls&index='+s_index+'&page='+page;
		xhr.open("GET", url, true);
		xhr.onprogress = function () { // DO NOTHING
		};
		xhr.onload = function () {
			var s = JSON.parse(xhr.responseText);
			if( s.last_page === true )
			{
				top.show_loading_image('hide');
				top.alert_notification_show(s.msg);
			}
			else
			{
				upload_existing_recalls(s.next_index,s.next_page,s.msg);
			}
		};
		xhr.send();
	}

    function sync_master_data(section) {
        section = section || 'all';
        var postdata = {section:section};
        var loading_msg = "Please Wait Syncing Master Data...";
        if(section=='schedule_statuses') loading_msg = "Please Wait Uploading Active Schedule Statuses...";
        if(section=='slot_procedures') loading_msg = "Please Wait Uploading Slot Procedures...";
        top.show_loading_image('show', '', loading_msg);
        $.ajax({
            url: webroot+'/library/erp_portal/sync_imw_masters.php',
            method: 'POST',
            data: postdata,
            dataType: 'json',
            success: function (result)
            {
                var msg='';
                top.show_loading_image('hide');
                if(result.race) {
                    msg += "<br>"+result.race;
                }
                if(result.ethnicity) {
                    msg += "<br>"+result.ethnicity;
                }
                if(result.marital_status) {
                    msg += "<br>"+result.marital_status;
                }
                if(result.gender) {
                    msg += "<br>"+result.gender;
                }
                if(result.schedule_statuses) {
                    msg += "<br>"+result.schedule_statuses;
                }
                if(result.slot_procedures) {
                    msg += "<br>"+result.slot_procedures;
                }
				if(result.patient_relations) {
                    msg += "<br>"+result.patient_relations;
				}
				if(result.allergy_severity) {
                    msg += "<br>"+result.allergy_severity;
                }
				if(result.allergies) {
                    msg += "<br>"+result.allergies;
                }
				if(result.surgery) {
                    msg += "<br>"+result.surgery;
                 }
				top.fAlert(msg);
                return false;
            }
        });
    }

</script>
<?php require_once('../admin_footer.php');?>
