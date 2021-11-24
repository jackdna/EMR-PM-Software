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
//require_once($GLOBALS['fileroot'].'/library/classes/scheduler/appt_schedule_functions.php');
include("schedule_functions.php");

$patient_id=$_REQUEST['pat_id'];
$flags=0;
$total_copay="";
if($case_typeid<>""){
	$Ins_nameqry=imw_query("SELECT b.case_name,b.vision FROM `insurance_case` a ,insurance_case_types b WHERE b.case_id=a.ins_case_type and a.ins_caseid='".$case_typeid."' ");
	list($ins_name,$visionYeNo)=imw_fetch_array($Ins_nameqry);	
}

//if($ins_name=="Normal"){
	$query_pri = "select ins.copay, ins.type, ic.name, ic.id, ins.referal_required from insurance_companies ic INNER JOIN insurance_data ins ON ic.id=ins.provider
	where ins.pid=$patient_id and ins.ins_caseid ='$case_typeid' and ins.actInsComp = '1' "; 
	$sql_pri = imw_query($query_pri);
	while($rt_pri = imw_fetch_assoc($sql_pri))
	{
		$ins_data_arr[$rt_pri['type']]=$rt_pri;
	}
	$ins_pri=$ins_data_arr['primary']['copay'];
	$insPriProvider=$ins_data_arr['primary']['name'];
	$pri_ins_id=$ins_data_arr['primary']['id'];
	$ins_pri_referal_required=$ins_data_arr['primary']['referal_required'];

	$rt_sec=$ins_data_arr['secondary'];
	//$ins_sec=$ins_data_arr['secondary']['copay'];
	$insSecProvider=$ins_data_arr['secondary']['name'];

	$rt_ter=$ins_data_arr['tertiary'];
	//$ins_ter=$ins_data_arr['tertiary']['copay'];
	$insTerProvider=$ins_data_arr['tertiary']['name'];
	
	$copay_policy_qry="SELECT secondary_copay,tertiary_copay,sec_copay_collect_amt,sec_copay_for_ins FROM `copay_policies`";
	$copay_policy_res=imw_query($copay_policy_qry);
	list($secondary_copay ,$tertiary_copay,$sec_copay_collect_amt,$sec_copay_for_ins)=imw_fetch_array($copay_policy_res);
	$ins_sec=0;
	if(strtolower($secondary_copay) == 'yes' && $sec_copay_for_ins==''){
		$ins_sec=$rt_sec["copay"];
	}else if($sec_copay_for_ins=='Medicare as Primary'){
		$chk_med = imw_query("SELECT id FROM insurance_companies WHERE 
				(in_house_code = 'medicare' or name like '%medicare%')
				and id='$pri_ins_id'");
		$chk_med_num = imw_num_rows($chk_med);
		if($chk_med_num>0 && $sec_copay_collect_amt>=$rt_sec["copay"]){
			$ins_sec=$rt_sec["copay"];
		}
	}
	$ins_ter = 0;
	if(strtolower($tertiary_copay) == 'yes'){
		$ins_ter=$rt_ter["copay"];
	}
	$total_copay=trim($ins_pri+$ins_sec+$ins_ter);
	
	//caseid,patient_id,actInsComp,type =primary , referal_required=yes
	//patient_reff->ins_data_id and reff_type=1 and order by effective date,rermainid desc
	//Reffrel conditions 
		
		if(strtolower($ins_pri_referal_required)=='yes'){
			$qry = "select reffral_no  from patient_reff where 
					patient_id='$patient_id'
					and ins_data_id='$pri_ins_id'
					and  ((patient_reff.end_date >= current_date() and 
					patient_reff.effective_date <= current_date())
					or(patient_reff.no_of_reffs > 0))
					order by effective_date desc,reff_id desc limit 0,1";
			$r=imw_query($qry);
			list($reffral_no)=imw_fetch_array($r);	 
		}
	$flags=1;
//}
$date_app="";
if($appt_id>0){
	$date_app=$appt_date;
}
$phy_id='';
$copay_paid=getSuperBillCharge2('copayPaid',$patient_id,$phy_id,$date_app);
$copays=getSuperBillCharge2('copay',$patient_id,$phy_id,$date_app);
$coPayNotRequired =getSuperBillCharge2('coPayNotRequired ',$patient_id,$phy_id,$date_app);
if($copays[0]>0){
	$total_copay=$copays[0];
	if($copay_paid[0]==1){
		$colr="green";
	}else{
		if($appt_id>0){
			$colr="red";
		}
		if($coPayNotRequired[0]==1){
			$copay_req="NR";
		}else{
			$copay_req="";
		}
	}
	
}else if($total_copay>0){
	if($appt_id>0){
		$colr="red";
	}
}
$item_ids_arr=array();
$sel_row=imw_query("select id from check_in_out_fields where item_name like '%copay%'");
while($fet_row=imw_fetch_array($sel_row)){
	$item_ids_arr[]=$fet_row['id'];
}
$item_ids_imp=implode(',',$item_ids_arr);
$copay_cico_payment=0;

// for slow query fix
if(!empty($appt_id)){ $phrse_checkinoutpay = " and check_in_out_payment.sch_id='$appt_id' "; }else{ $phrse_checkinoutpay = ""; }

$sel_row1=imw_query("select sum(check_in_out_payment_details.item_payment) as copay_cico_payment from check_in_out_payment_details 
					join check_in_out_payment on check_in_out_payment.payment_id = check_in_out_payment_details.payment_id
					where item_id in($item_ids_imp) and status='0' and check_in_out_payment.patient_id='$patient_id'
					".$phrse_checkinoutpay." and check_in_out_payment.sch_id !='0'");
$fet_row1=imw_fetch_array($sel_row1);
$copay_cico_payment=$fet_row1['copay_cico_payment'];

if($appt_id>0 && $copay_cico_payment>0){
	$colr="green";
}
if($appt_id==0 || $appt_id<0){
	$total_copay=0;
	$colr="black";
}
$substrCaseName="";
if($ins_name!=""){
	$substrCaseName=substr($ins_name,0,6);
}
if($pri_ins_id){
	$qryCheckInsCompClaim="Select claim_type,ins_accept_assignment from insurance_companies where id=".$pri_ins_id;
	$resCheckInsCompClaim=imw_query($qryCheckInsCompClaim)or die(imw_error());
	$rowCheckInsCompClaim=imw_fetch_assoc($resCheckInsCompClaim);
	$insCompClaim=$rowCheckInsCompClaim['claim_type'].'-|S|-'.$rowCheckInsCompClaim['ins_accept_assignment'];
}
echo $flags."~"."<span style=\"color:$colr;\">".numberformat($total_copay,2,"yes")."<b style='color:blue;'> ".$copay_req."</b></span>"."~".$reffral_no."~".$coPayNotRequired[0]."~".$insPriProvider."~".$insSecProvider."~".$insTerProvider."~".$visionYeNo."~".$insCompClaim;
?>				