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
?>
<?php
require_once(dirname(__FILE__).'/../../config/globals.php');
require_once(dirname(__FILE__).'/../../library/classes/common_function.php');
require_once(dirname(__FILE__).'/../../library/classes/class.erx_functions.php');
$objERX 				= new ERXClass();
$objERX->patientId		= $_SESSION['patient'];
$patient_id 			= $_SESSION['patient'];
$user_id 				= $_SESSION['authId'];
	
//--- GET ERX STATUS AND EMDEON ACCESS URL -------
$copay_policies_res		= $objERX->get_copay_policies('*');
$Allow_erx_medicare 	= $copay_policies_res['Allow_erx_medicare'];
$EmdeonUrl 				= $copay_policies_res['EmdeonUrl'];

//--- GET ERX USERNAME AND PASSWORD --------
$phyQryRes 				= $objERX->getProviderDetails($user_id);
$eRx_user_name 			= $phyQryRes['eRx_user_name'];
$erx_password 			= $phyQryRes['erx_password'];
$eRx_facility_id 		= trim($_SESSION['login_facility_erx_id']);//$phyQryRes[0]['eRx_facility_id'];

$operator_name 			= $phyQryRes['lname'].', ';
$operator_name 		   .= $phyQryRes['fname'].' ';
$operator_name 		   .= $phyQryRes['mname'];
$operator_name 			= ucwords(trim($operator_name));
if($operator_name[0] == ','){
	$operator_name = substr($operator_name,1);
}

$noRecord = true;
if($eRx_user_name && $erx_password && $eRx_facility_id){
	//--- GET PATIENT ERX PERSON ID AND HSI VALUE -----
	$eRx_pres_res 	= $objERX->get_patient_erx_details_from_db();
	$person 		= $eRx_pres_res['patient_eRx_person'];
	$personhsi 		= $eRx_pres_res['patient_eRx_person_hsi'];

	if(empty($person) == true && empty($personhsi) == true){
		$eRx_pres_res = $objERX->put_patient_erx_details_to_db($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id,$patient_id);
		$person 	= $eRx_pres_res['patient_eRx_person'];
		$personhsi	= $eRx_pres_res['patient_eRx_person_hsi'];
	}
	
	//--- BEGIN PATIENT RX HISTORY (MEDICATIONS) ------
	$arrMedications = $objERX->get_patient_erx_prescription($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id,$patient_id,$person,$personhsi,'01/01/2009');
	if($arrMedications){
		$objERX->process_erx_prescription($arrMedications);
	}


	//---- BEGIN PATIENT ALLERGIES ----
	$arrAllergies = $objERX->get_patient_erx_allergy($EmdeonUrl,$eRx_user_name,$erx_password,$eRx_facility_id,$patient_id,$person);
	if($arrAllergies){
		$objERX->process_erx_allergies($arrAllergies);
	}
	
	$erx_medications = $objERX->fetch_erx_meds_from_db();
	if($erx_medications && is_array($erx_medications)){?>
		<div><big><b>e-Prescriptions</b></big></div>
        <div class="table-responsive col-sm-12">
        <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr class="grythead">
                <th>Start Date</th>
                <th>Prescription</th>
                <th>Dosage</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php
		 	$erx_meds_cnt = 0;
			foreach($erx_medications as $rs_erx_medication){$erx_meds_cnt++; ?>
            <tr>
                <td><?php echo $rs_erx_medication['begdate'];?></td>
                <td><?php echo $rs_erx_medication['title'];?></td>
                <td><?php echo $rs_erx_medication['destination'];?></td>
                <td><?php echo $rs_erx_medication['allergy_status'];?></td>
            </tr>
        <?php }
			if($erx_meds_cnt==0){echo '<tr><td colspan="3">No record found.</td></tr>';}
		?>
        </tbody>
        </table>
        </div>
	<?php	
	}
	$erx_allergies = $objERX->fetch_erx_allergies_from_db();
	if($erx_allergies && is_array($erx_allergies)){?>
		<div><big><b>eRx Allergies</b></big></div>
        <div class="table-responsive col-sm-12">
        <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr class="grythead">
                <th>Reported Date</th>
                <th>Allergy</th>
                <th>Allergy Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php 
			$allergy_cnt = 0;
			foreach($erx_allergies as $erx_allergy){$allergy_cnt++; ?>
            <tr>
                <td><?php echo $erx_allergy['begdate'];?></td>
                <td><?php echo $erx_allergy['title'];?></td>
                <td><?php echo $erx_allergy['allergen_type'];?></td>
                <td><?php echo $erx_allergy['allergy_status'];?></td>
            </tr>
        <?php }
		if($allergy_cnt==0){echo '<tr><td colspan="3">No record found.</td></tr>';}
		?>
        </tbody>
        </table>
        </div>
	<?php	
	}
}
else{
?>
<div class="info-warning">eRx authentication failed.</div>
<?php
}
?>