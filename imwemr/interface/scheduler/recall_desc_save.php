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
//require_once(dirname(__FILE__)."/common/schedule_functions.php");
require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_page_functions.php');
	$erp_error=array();
	$authUser=$_SESSION['authUser'];
	$authUserID=$_SESSION['authUserID'];
	$descs=imw_real_escape_string(trim(nl2br($txt_comment)));
	$recall_m=trim($recall_month);
	$fac=trim($sel_facility_id);
	$recall_date=date("Y-m-d",mktime(0,0,0,date("m")+$recall_m,date("d"),date("y")));
	$patient_id=trim($patient_id);

	if($save<>"" && $sel_proc_ids<>""){
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
				procedure_id='$sel_proc_ids',
				$recall_str
				facility_id='$fac',
				operator='$authUserID',
				current_date1='".date('Y-m-d H:i:s')."'
				where id='$editid'";
				$res=imw_query($qry2)or die(imw_error().' ln34');
			}
		}else{
			$qry="select * from patient_app_recall where procedure_id='$sel_proc_ids' and facility_id='$fac' and patient_id='$patient_id' AND descriptions != 'MUR_PATCH' AND recalldate='$recall_date'";
			$res=imw_query($qry)or die(imw_error().' ln21');
			if(imw_num_rows($res)>0){
				//nothing to do
			}else{
				$qry2="insert into patient_app_recall set
				descriptions='$descs',
				recall_months='$recall_m',
				operator='$authUserID',
				facility_id='$fac',

				procedure_id='$sel_proc_ids',
				patient_id='$patient_id',
				recalldate='$recall_date',
				current_date1='".date('Y-m-d H:i:s')."'";
				$res=imw_query($qry2)or die(imw_error().' ln51');
				$editid = imw_insert_id();
			}
		}
		//echo $qry2;
		//echo "<script>";
		//echo "opener.pat_recall_appoinment();\n";
		//echo "window.close();\n";
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
	}
	if($editid<>""){
		$patient_app_recall_query11="SELECT * FROM patient_app_recall where  id='$editid' AND descriptions != 'MUR_PATCH' order by procedure_id ";

		$patient_app_recall_result11=imw_query($patient_app_recall_query11)or die(imw_error().' ln61');
		$patient_app_recall_numrows11 =imw_num_rows($patient_app_recall_result11);
		$patient_row11=imw_fetch_array($patient_app_recall_result11);
		$procedure_id_alter=$patient_row11['procedure_id'];
		$recall_monthEdit=$patient_row11['recall_months'];
		$txt_commentdescriptions=$patient_row11['descriptions'];
		$facility=$patient_row11['facility_id'];
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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>:: imwemr ::</title>

<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/schedulemain.css">
<link rel="stylesheet" href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css">

        <!--
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
<link rel="stylesheet" href="<?php echo $css_patient;?>" type="text/css">

<script language="javascript" src="../common/script_function.js"></script>-->

<script>
function save_recalls(){
		var proc_ids=document.getElementById("sel_proc_ids").value;
		var desc=document.getElementById("txt_comment").value;
		var recall=document.getElementById("recall_month").value;
		if(proc_ids=="" ){
			alert("Please select procedure");
			document.getElementById("sel_proc_ids").focus();
			return false;
		}else if(recall==""){
			alert("Please select recall months");
			document.getElementById("recall_month").focus();
			return false;
		}
		document.getElementById("save").value="yes";
		document.recal_Form.submit();

}
</script>
</head>
<body>
<div class=" container-fluid">
    <form name="recal_Form" id="recal_Form" method="post" action="">
    <input type="hidden" id="sch_id" name="sch_id" value="<?php echo $_REQUEST['sch_id'];?>">
    <input type="hidden" id="patient_id" name="patient_id" value="<?php echo $_REQUEST['patient_id'];?>">
    <input type="hidden" id="loc" name="loc" value="<?php echo $_REQUEST['loc'];?>">
    <input type="hidden" id="save" name="save" value="">
    <input type="hidden" id="editid" name="editid" value="<?php echo $_REQUEST['editid'];?>">
    <div class="whitebox">
        <div class="boxheadertop">
            <div class="row">
                <div class="col-sm-12 text-right">
                     <h3><?php $n=patient_name($patient_id); echo $n[0];?></h3>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 text-left">
	            <div  id="recs"  style="overflow:x-hidden; overflow:scroll;width:100%; left:0px;height:230px;">
                <?php include("recall_app1.php");?>
                </div>
            </div>
        </div>
     <div class="row grythead">
     	<div class="col-sm-12" style="line-height: 30px">Add New</div>
     </div>
    <div class="row">
    	<div class="col-sm-8 text-left">
        	<div class="form-group">
                <label for="">Procedure</label>
                <select name="sel_proc_ids" class="form-control minimal" id="sel_proc_ids">
                <option value="">-Select Procedure-</option>
                <?php
                    $procHave=0;
                    $res = imw_query("SELECT id,proc FROM slot_procedures WHERE times = '' AND proc != '' AND doctor_id = 0 AND active_status = 'yes' and source='' ORDER BY proc")or die(imw_error().' ln161');
                    while($row = imw_fetch_assoc($res))
                    {
                        extract($row);
                        if($proc<>"")
                        {
                            if($procedure_id_alter==$id)
                            {
                                $sel='selected';
                                $provHave=1;
                            }

                            echo "<option $sel value=\"$id\">$proc</option>";
                        }
                        $sel="";
                    }

                    if($procHave==0 && $procedure_id_alter!='')
                    {
                        $res = imw_query("select * from slot_procedures where proc!='' and (id = procedureId || procedureId =0) AND id = ".$procedure_id_alter)or die(imw_error().' ln179');
                        $row = imw_fetch_assoc($res);
                        extract($row);
                        if($proc<>"")
                        {
                            $fontColor = '';
                            if($active_status=='no' || $active_status=='del')
                            {
                                $fontColor="style=\"color:#CC0000\"";
                                $sel='selected';
                            }

                            echo "<option $sel value=\"$id\" ".$fontColor." >$proc</option>";
                        }
                        $sel="";
                    }
                ?>
          </select>
            </div>
        </div>
    	<div class="col-sm-4 text-left">
             <label for="">Recall[Month(s) from Today]</label>
             <select id="recall_month" name="recall_month"  class="form-control minimal">
                <option value="">-</option>
                <?php
                $i=1;
                while($i<25){ ?>
                    <option value='<?php echo $i;?>' <?php if($recall_monthEdit==$i) echo 'selected';?>><?php if($i<10){ echo "0".$i;}else{ echo $i;}?></option>
                <?php $i++;}?>
            </select>
        </div>
    </div>
    <div class="row">
    	<div class="col-sm-8 text-left">
        	<div class="form-group">
                <label for="">Description</label>
                <textarea class="form-control" name="txt_comment" id="txt_comment" cols="100" rows="1"><?php echo $txt_commentdescriptions;?></textarea>
            </div>
        </div>
        <div class="col-sm-4 text-left">
        	<div class="form-group">
                <label for="">Facility</label>
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
        </div>
    </div>
    </div>
    <div class="row">
    	<div class="col-sm-12 text-right">
        	<div class="form-group">
            	<button type="submit" value="save" class="btn btn-success" name="save_butt" id="save_butt" onclick="javascript:save_recalls();">Save</button>
            	<button class="btn btn-danger" name="btn_find" id="btn_find" onclick="javascript:window.close();">Close</button>
            </div>
        </div>
    </div>
	</form>
</div>
</body>
