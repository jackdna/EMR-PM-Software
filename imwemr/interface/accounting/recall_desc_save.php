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
$title = "Patient Recall";
require_once("acc_header.php");

$authUser=$_SESSION['authUser'];
$authUserID=$_SESSION['authUserID'];
$patient_id = $_SESSION["patient"];
$save=$_REQUEST['save'];
$procid=trim($_REQUEST['sel_proc_ids']);
$descs=htmlentities(addslashes($_REQUEST['txt_comment']));
$recall_m=trim($_REQUEST['recall_month']);

$fac=trim($_REQUEST['sel_facility_id']);
$erp_error=array();
$recall_date=date("Y-m-d",mktime(0,0,0,date("m")+$recall_m,date("d"),date("y")));

if($_REQUEST['post_action']=="save" && $procid<>""){
	//----------------- Update Recall -----------------//
	if($editid)
	{
		$qry="select * from patient_app_recall where patient_id='$patient_id' and id='$editid'";
		$res=imw_query($qry)or die(imw_error().' ln21');
		if(imw_num_rows($res)>0){
			$old_data=imw_fetch_object($res);

			if($old_data->recall_months!=$recall_m){
				$recall_str="recall_months='$recall_m',
				recalldate='$recall_date',";
			}

			$qry2="update patient_app_recall set
			descriptions='$descs',
			procedure_id='$procid',
			$recall_str
			facility_id='$fac',
			operator='$authUserID',
			current_date1='".date('Y-m-d H:i:s')."'
			where id='$editid'";
			$res=imw_query($qry2)or die(imw_error().' ln34');
		}
	}else{
		$qry="select * from patient_app_recall where procedure_id='$procid' and facility_id='$fac' and patient_id='$patient_id' AND descriptions != 'MUR_PATCH' AND recalldate='$recall_date'";
		$res=imw_query($qry)or die(imw_error().' ln21');
		if(imw_num_rows($res)>0){
			//nothing to do
		}else{
			$qry2="insert into patient_app_recall set
			descriptions='$descs',
			recall_months='$recall_m',
			operator='$authUserID',
			facility_id='$fac',

			procedure_id='$procid',
			patient_id='$patient_id',
			recalldate='$recall_date',
			current_date1='".date('Y-m-d H:i:s')."'";
			$res=imw_query($qry2)or die(imw_error().' ln51');
			$editid = imw_insert_id();

		}
	}

	if(isERPPortalEnabled()){
		try {
			include_once($GLOBALS['fileroot']."/library/erp_portal/erp_portal_core.php");
			include_once($GLOBALS['srcdir'].'/erp_portal/recalls.php');
			$patient_arr = array();
			$patient_arr["Date"]=$recall_date;
			$patient_arr["Active"]=true;
			$patient_arr["LocationExternalId"]=$fac;
			$patient_arr["DoctorExternalId"]=$authUserID;
			$patient_arr["PatientExternalId"]=$patient_id;
			$patient_arr["Id"]="";
			$patient_arr["ExternalId"]=$editid;
			$oIncSecMsg = new Recalls();
			$oIncSecMsg->update_pt_portal($patient_arr);
		} catch(Exception $e) {
			$erp_error[]='Unable to connect to ERP Portal';
		}
	}

	echo "<script>location.href='recall_desc_save.php';</script>";
}
if($editid<>""){
	//----------------- Recall Detail -----------------//
	$patient_app_recall_query11="SELECT * FROM patient_app_recall where  id='$editid' AND descriptions != 'MUR_PATCH' order by procedure_id ";
	$patient_app_recall_result11=imw_query($patient_app_recall_query11) or die(imw_error());
	$patient_app_recall_numrows11 =imw_num_rows($patient_app_recall_result11);
	$patient_row11=imw_fetch_array($patient_app_recall_result11);
	$procedure_id_alter=$patient_row11['procedure_id'];
	$recall_month=$patient_row11['recall_months'];
	$txt_comment=html_entity_decode(stripslashes($patient_row11['descriptions']));
	$facility=$patient_row11['facility_id'];
}else{
	if(in_array(strtolower($billing_global_server_name), array('derbyeye'))){
		$facility=$_SESSION['login_facility'];
	}
}

//----------------- Get Procedures -----------------//
$res = imw_query("SELECT id,proc,source,active_status FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id = 0 ORDER BY proc");
while($row = imw_fetch_array($res))
{
	if($row['active_status']=='yes'){
		if($row['source']=='')$slot_proc_list_arr[$row['id']]=$row['proc'];
	}
	$slot_proc_disp_arr[$row['id']]=$row['proc'];
}


/* Facility Data */
	$fac_data_arr = array();
	$fac_qry = "
		Select
			id,facility.name as fac_name
		FROM
			facility LEFT JOIN groups_new ON(groups_new.gro_id = facility.default_group and groups_new.del_status='0')
		ORDER BY
			facility.name ASC
		";

	$fac_sql = imw_query($fac_qry)or die(imw_error().' ln81');
	if($fac_sql && imw_num_rows($fac_sql) > 0){
		while($fac_row = imw_fetch_assoc($fac_sql)){
			$fac_data_arr[$fac_row['id']] = $fac_row['fac_name'];
		}
	}
?>
<div id="recall_div">
	<form name="recal_Form" method="post" action="recall_desc_save.php">
	<input type="hidden" name="sch_id" value="<?php echo xss_rem($_REQUEST['sch_id']);?>">
	<input type="hidden" name="patient_id" value="<?php echo xss_rem($_REQUEST['patient_id']);?>">
	<input type="hidden" name="loc" value="<?php echo xss_rem($_REQUEST['loc']);?>">
	<input type="hidden" name="editid" value="<?php echo xss_rem($_REQUEST['editid']); ?>">
    <input type="hidden" id="post_action" name="post_action" value="">
	<div  id="recs"  style="height:<?php echo $_SESSION['wn_height']-380; ?>px;overflow-y:scroll;">
		<?php include("recall_app1.php");?>
	</div>
	<div class="row" style="padding-top:5px;border-top:solid 1px #c0c0c0; box-shadow: rgba(0, 0, 0, 0.1) 0px 0px 2px 0.5px; margin:5px 0 8px;">
		<div class="col-sm-2">
			<label>Procedure</label>
			<select name="sel_proc_ids" id="sel_proc_ids" class="selectpicker" data-width="100%" data-size="10">
				<option value="">Select Procedure</option>
				<?php
				foreach($slot_proc_list_arr as $slot_key=>$slot_val){
					if($procedure_id_alter==$slot_key)
					{
						$sel='selected';
					}
					echo "<option $sel value=\"$slot_key\">$slot_val</option>";
					$sel="";
				}
				?>
			</select>
		</div>
		<div class="col-sm-2">
			<label>Recall (Month)</label>
			<select name="recall_month" id="recall_month" class="selectpicker" data-width="100%" data-size="10">
				<option value="">-</option>
				<?php
				$i=1;
				while($i<25){
				?>
					<option value='<?php echo $i;?>' <?php if($recall_month==$i) echo 'selected';?>><?php if($i<10){ echo "0".$i;}else{ echo $i;}?></option>
				<?php $i++;}?>
			</select>
		</div>

		<div class="col-sm-2">
			<label>Facility</label>
			<select name="sel_facility_id" id="sel_facility_id" class="form-control minimal">
			    <option value="">-Select Facility-</option>
			    <?php
						$str_opt = '';
						foreach($fac_data_arr as $key => $val){
							$selected = ($key == $facility && empty($facility) == false) ? 'selected' : '';
							$str_opt .= '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
						}
						echo $str_opt;
					?>
			</select>
		</div>
		<div class="col-sm-6">
			<label>Description</label>
			<textarea name="txt_comment" id="txt_comment" class="form-control" rows="1"><?php echo $txt_comment;?></textarea>
		</div>
	</div>
	</form>
</div>
<script type="text/javascript">
	var ar = [["save1","Save","top.fmain.save_recalls();"],
			  ["delete_fun","Delete","top.fmain.del_records('recal_Form');"]];
	top.btn_show("ACCOUNT",ar);
</script>
<?php require_once("acc_footer.php");?>
