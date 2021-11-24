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
$without_pat="yes"; 
$title = "Edit Dx Codes";
include_once(dirname(__FILE__)."/acc_header.php");
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
//include_once("../main/Functions.php");
//include_once("../main/main_functions.php");
$ref_webroot=$GLOBALS['webroot']."/data/".constant('PRACTICE_PATH')."/xml/refphy/";
?>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/bootstrap-typeahead.js"></script>
<script src="<?php echo $GLOBALS['webroot']; ?>/library/js/core_main.js?<?php echo filemtime('../../library/js/core_main.js');?>"></script>
<?php
$operatorName = $_SESSION['authUser'];
$operatorId = $_SESSION['authUserID'];
$edit_id = preg_replace('/[^A-Za-z0-9 \_]+/','',$_REQUEST['edit_id']);
$edit_id = xss_rem($_REQUEST['edit_id'], 1);
$sup_arr=explode('_',$edit_id);
$phy_id_cn=$GLOBALS['arrValidCNPhy'];

$sel_prov=imw_query("select id,lname,fname,mname,user_type from users order by lname,fname asc");
while($fet_prov=imw_fetch_array($sel_prov)){
	if($fet_prov["Enable_Scheduler"]=='1' || in_array($fet_prov["user_type"],$phy_id_cn)){
		$phy_id_name[$fet_prov['id']]=$fet_prov['lname'].', '.$fet_prov['fname'];
		$phy_id_name_int[$fet_prov['id']]=strtoupper(substr($fet_prov['fname'],0,1).substr($fet_prov['mname'],0,1).substr($fet_prov['lname'],0,1));
	}
}

if($_REQUEST['frm_sub']!=""){
	$primary_provider=$_REQUEST['primary_provider'];
	$secondary_provider=$_REQUEST['secondary_provider'];
	$refferingPhysician=$_REQUEST['refferingPhysician'];
	$id_num=$_REQUEST['id_num'];
	
	if($refferingPhysician>0){
		$sel_prov=imw_query("select physician_Reffer_id,LastName,FirstName,MiddleName from refferphysician where physician_Reffer_id ='$refferingPhysician' order by LastName,FirstName asc");
		$fet_prov=imw_fetch_array($sel_prov);
		$reffer_physician_name=$fet_prov['LastName'].', '.$fet_prov['FirstName'].' '.$fet_prov['MiddleName'];
		$reffer_physician_name_int=substr($fet_prov['FirstName'],0,1).substr($fet_prov['MiddleName'],0,1).substr($fet_prov['LastName'],0,1);
	}
	
	if($sup_arr[0]=="sup"){
		imw_query("update superbill set physicianId='$primary_provider',refferingPhysician='$refferingPhysician' where idSuperBill='".$sup_arr[1]."'");
		$secondary_provider="";
	}else{
		imw_query("update patient_charge_list set primaryProviderId='$primary_provider',secondaryProviderId='$secondary_provider',reff_phy_id='$refferingPhysician' where charge_list_id='$edit_id'");
	}
?>
<script type='text/javascript'>
	var id_num = <?php echo $id_num; ?>;
	window.opener.$('#primary_provider_'+id_num).html('<?php echo $phy_id_name_int[$primary_provider];?>');
	window.opener.$('#secondary_provider_'+id_num).html('<?php echo $phy_id_name_int[$secondary_provider];?>');
	window.opener.$('#primary_provider_'+id_num).attr('title','<?php echo $phy_id_name[$primary_provider];?>');
	window.opener.$('#secondary_provider_'+id_num).attr('title','<?php echo $phy_id_name[$secondary_provider];?>');
	window.opener.$('#ref_provider_'+id_num).html('<?php echo $reffer_physician_name_int;?>');
	window.opener.$('#ref_provider_'+id_num).attr('title','<?php echo $reffer_physician_name;?>');
	window.opener.$('#reff_phy_id'+id_num).val('<?php echo $refferingPhysician;?>');
	window.close();
</script>
<?php	
}

if($sup_arr[0]=="sup"){
	$pcl_qry = imw_query("SELECT * FROM superbill WHERE idSuperBill = '".$sup_arr[1]."'");
}else{
	$pcl_qry = imw_query("SELECT * FROM patient_charge_list WHERE charge_list_id = '".$edit_id."'");
}
$pcl_row = imw_fetch_array($pcl_qry);

if($sup_arr[0]=="sup"){
	$patient_id=$pcl_row['patientId'];
	$encounter_id=$pcl_row['encounterId'];
	$primary_provider_id=$pcl_row['physicianId'];
	$secondary_provider_id="";
	$reff_phy_id=$pcl_row['refferingPhysician'];
}else{
	$patient_id=$pcl_row['patient_id'];
	$encounter_id=$pcl_row['encounter_id'];
	$primary_provider_id=$pcl_row['primaryProviderId'];
	$secondary_provider_id=$pcl_row['secondaryProviderId'];
	$reff_phy_id=$pcl_row['reff_phy_id'];
}

if($_REQUEST['ref_phy']>0){
	$reff_phy_id=$_REQUEST['ref_phy'];
}

if($reff_phy_id>0){
	$sel_prov=imw_query("select physician_Reffer_id,LastName,FirstName,MiddleName from refferphysician where physician_Reffer_id ='$reff_phy_id' order by LastName,FirstName asc");
	$fet_prov=imw_fetch_array($sel_prov);
	$reffer_physician=$fet_prov['LastName'].', '.$fet_prov['FirstName'].' '.$fet_prov['MiddleName'];
}

$patientDt = imw_query('select * from patient_data where id= '.$patient_id.'');
$patientDetails = imw_fetch_object($patientDt);
?>	
<div style="width:100%; overflow-y:auto; height:330px; overflow-x:hidden;" id="main_container_div" class="pt10">
<div class="col-sm-12 purple_bar">
    <div class="row">
        <div class="col-sm-4">
            <label>Edit Day Charges</label>	
        </div>	
        <div class="col-sm-6">
            <label><b>Patient Name&nbsp;:&nbsp;</b><?php echo $patientDetails->fname.', '.$patientDetails->lname.' ('.$patientDetails->id.')'; ?></label>	
        </div>	
        <div class="col-sm-2">
            <label><b>EId&nbsp;:&nbsp;</b><?php echo $encounter_id; ?></label>	
        </div>		
    </div>
</div>
    <form name="dx_form" action="edit_day_charges.php" method="post">
    <input type="hidden" value="<?php echo $edit_id; ?>" name="edit_id" id="edit_id">
    <input type="hidden" value="<?php echo $_REQUEST['id_num']; ?>" name="id_num" id="id_num">
    <input type="hidden" value="yes" name="frm_sub" id="frm_sub">
    <table class="table table-bordered">
        <tr class="grythead">
            <th>Billing Provider</th>
            <th>Credited Provider</th>
            <th>Referring Provider</th>
        </tr>
        <tr class="text-center">
            <td>
				<select name="primary_provider" id="primary_provider" class="selectpicker">
                	<option value="">Billing Provider</option>
				   <?php 
                    foreach($phy_id_name as $phy_key=>$phy_val){
						$phy_sel="";
						if($phy_key==$primary_provider_id){
                       	 $phy_sel="selected";
						}
                     ?>
                        <option value="<?php echo $phy_key; ?>" <?php echo $phy_sel; ?>><?php echo $phy_val; ?></option>
                   <?php } ?>
                  </select>
            </td>
            <td>
                <select name="secondary_provider" id="secondary_provider" class="selectpicker">
                	<option value="">Credited Provider</option>
				   <?php 
                    foreach($phy_id_name as $phy_key=>$phy_val){
						$phy_sel="";
						if($phy_key==$secondary_provider_id){
                       	 $phy_sel="selected";
						}
                     ?>
                        <option value="<?php echo $phy_key; ?>" <?php echo $phy_sel; ?>><?php echo $phy_val; ?></option>
                   <?php } ?>
                  </select>
            </td>
            <td>
            	<input type="hidden" name="refferingPhysician" id="refferingPhysician" value="<?php print $reff_phy_id; ?>" />
                <input type="text" name="reffer_physician" id="reffer_physician" value="<?php echo $reffer_physician; ?>" class="form-control" onKeyUp="loadPhysicians(this,'refferingPhysician','<?php echo $ref_webroot; ?>');">
            </td>
        </tr> 
    </table>
    </form> 
</div>
   
<div class="ad_modal_footer module_buttons" style="margin-top:5px;">
    <input name="sbmtFrm" id="sbmtFrm" type="button" class="btn btn-success" value="Update" onClick="document.dx_form.submit();">
    <input type="button" name="CancelBtn" id="CancelBtn" class="btn btn-danger" value="Cancel" onClick="window.close();">
</div>
