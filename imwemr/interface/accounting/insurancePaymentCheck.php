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
$qry = imw_query("select charge_list_id,primaryInsuranceCoId,secondaryInsuranceCoId,tertiaryInsuranceCoId,patient_id
		from patient_charge_list where del_status='0' and encounter_id = '$encounter_id'");
while($row = imw_fetch_array($qry)){
	$heardOffRes[]  = $row;
}
for($w=0;$w<count($heardOffRes);$w++){	
	$ins_comp_paid = false;
	$charge_list_id = $heardOffRes[$w]['charge_list_id'];
	$qry = imw_query("select charge_list_detail_id from patient_charge_list_details 
			where del_status='0' and charge_list_id = '$charge_list_id'");
	while($row = imw_fetch_array($qry)){
		$charge_list_detail[]  = $row;
	}		
	//$charge_list_detail = ManageData::getQryRes($qry);
	$charge_list_detail_id = array();
	for($d=0;$d<count($charge_list_detail);$d++){
		$charge_list_detail_id[] = $charge_list_detail[$d]['charge_list_detail_id'];
	}
	$chargeListDetailId = implode(',',$charge_list_detail_id);
	$patient_id = $heardOffRes[$w]['patient_id'];
	//-- get Primary Insurance company from insurance_data ------
	$primaryInsuranceCoId = $heardOffRes[$w]['primaryInsuranceCoId'];	
	//-- get Secondary Insurance company from insurance_data ------
	$secondaryInsuranceCoId = $heardOffRes[$w]['secondaryInsuranceCoId'];
	//-- get Tertiary Insurance company from insurance_data ------
	$tertiaryInsuranceCoId = $heardOffRes[$w]['tertiaryInsuranceCoId'];
	if($primaryInsuranceCoId > 0){
		//--- Check by primary insurance paid payments -------
		$primaryInsRes = paidInsComp($encounter_id,$primaryInsuranceCoId,1);
		if(count($primaryInsRes)>0) $ins_comp_paid = true;
		else $ins_comp_paid = false;
		if($ins_comp_paid == false){
			//--- Check by primary insurance denied payments -------
			$priDenied = priDeniedDetail($patient_id,$encounter_id,$primaryInsuranceCoId);
			if($priDenied) $ins_comp_paid = true;
			else $ins_comp_paid = false;
			if($ins_comp_paid == false){
				//--- Check by primary insurance deductable payments -------
				$priDeductable = priDeductableDetail($chargeListDetailId,$primaryInsuranceCoId);
				if($priDeductable) $ins_comp_paid = true;
				else $ins_comp_paid = false;
				if($ins_comp_paid == false){
					//---- check about write off and discount by primary insurance ----
					$pripaidDetails = writeOffDetail($patient_id,$encounter_id,$primaryInsuranceCoId);
					if($pripaidDetails) $ins_comp_paid = true;
					else $ins_comp_paid = false;
				}
			}
		}
		if($ins_comp_paid) $ins_comp_paid1 = 'true';
		else $ins_comp_paid1 = 'false';
		$qry1[] = "update patient_charge_list set primary_paid = '$ins_comp_paid1' where charge_list_id = '$charge_list_id'";		
		
		//--- Check by Secondary insurance paid payments -------
		$ins_comp_paid = false;
		if($secondaryInsuranceCoId > 0){
			$secondaryInsRes = paidInsComp($encounter_id,$secondaryInsuranceCoId,2);			
			if(count($secondaryInsRes)>0) $ins_comp_paid = true;
			else $ins_comp_paid = false;
			if($ins_comp_paid == false){
				//--- Check by Secondary insurance denied payments -------
				$secDenied = priDeniedDetail($patient_id,$encounter_id,$secondaryInsuranceCoId);
				if($secDenied) $ins_comp_paid = true;
				else $ins_comp_paid = false;
				if($ins_comp_paid == false){
					//--- Check by Secondary insurance deductable payments -------
					$secDeductable = priDeductableDetail($chargeListDetailId,$secondaryInsuranceCoId);
					if($secDeductable) $ins_comp_paid = true;
					else $ins_comp_paid = false;
					if($ins_comp_paid == false){
						//---- check about write off and discount by Secondary insurance ----
						$secPaidDetails = writeOffDetail($patient_id,$encounter_id,$secondaryInsuranceCoId);
						if($secPaidDetails) $ins_comp_paid = true;
						else $ins_comp_paid = false;
					}
				}
			}
		}
		
		if($ins_comp_paid) $ins_comp_paid2 = 'true';
		else $ins_comp_paid2 = 'false';
		$qry1[] = "update patient_charge_list set secondary_paid = '$ins_comp_paid2' where charge_list_id = '$charge_list_id'";
		//--- Check by tertairy insurance paid payments -------
		$ins_comp_paid = false;
		if($tertiaryInsuranceCoId > 0){
			$tertiaryInsRes = paidInsComp($encounter_id,$tertiaryInsuranceCoId,3);
			if(count($tertiaryInsRes)>0) $ins_comp_paid = true;
			else $ins_comp_paid = false;
			if($ins_comp_paid == false){
				//--- Check by tertairy insurance denied payments -------
				$terDenied = priDeniedDetail($patient_id,$encounter_id,$tertiaryInsuranceCoId);
				if($terDenied) $ins_comp_paid = true;
				else $ins_comp_paid = false;
				if($ins_comp_paid == false){
					//--- Check by tertairy insurance deductable payments -------
					$terDeductable = priDeductableDetail($chargeListDetailId,$tertiaryInsuranceCoId);
					if($terDeductable) $ins_comp_paid = true;
					else $ins_comp_paid = false;
					if($ins_comp_paid == false){
						//---- check about write off and discount by tertairy insurance ----
						$terPaidDetails = writeOffDetail($patient_id,$encounter_id,$tertiaryInsuranceCoId);
						if($terPaidDetails) $ins_comp_paid = true;
						else $ins_comp_paid = false;
					}
				}
			}
		}
		if($ins_comp_paid) $ins_comp_paid3 = 'true';
		else $ins_comp_paid3 = 'false';
		$qry1[] = "update patient_charge_list set tertiary_paid = '$ins_comp_paid3' where charge_list_id = '$charge_list_id'";
		foreach($qry1 as $query){
			if($query){
				imw_query($query);
			}
		}
	}
}	
?>