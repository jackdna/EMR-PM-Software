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

?><?php
include_once(dirname(__FILE__)."/../../config/globals.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
use setasign\Fpdi\Tcpdf\Fpdi;
$pdf = new Fpdi();
$fpdiCheck=true;
$operator_id=$_SESSION['authId'];
$sel_hcfa=implode(',',$selectpatient);
$time_hcfa=time();
$print_paper_type=$PrintCms_white_chk;
$newfile_ub_path=write_html('','ub_form.pdf');
$batch_file_submitte_id=$_REQUEST['batch_file_submitte_id'];
$vchl=$_REQUEST['vchl'];
$vchld=$_REQUEST['vchld'];
$vchld_exp=explode(',',$vchld);

$chl_qry=imw_query("select * from patient_charge_list where charge_list_id='$vchl'");
$chl_row=imw_fetch_array($chl_qry);
$all_dx_codes_arr=unserialize(html_entity_decode($chl_row['all_dx_codes']));
$encounter_id=$chl_row['encounter_id'];
$primaryProviderId=$chl_row['primaryProviderId'];
$gro_id=$chl_row['gro_id'];
//$charge_list_dos1=explode('-',$chl_row['date_of_service']);
//$charge_list_dos=$charge_list_dos1[1].$charge_list_dos1[2].substr($charge_list_dos1[0],2);
$enc_icd10=$chl_row['enc_icd10'];
if($enc_icd10>0){
	$enc_icd10_ind="0";
}else{
	$enc_icd10_ind="9";
}

$type_of_bill="831";	
if(count($bill_type[$vchl])>0){
	$type_of_bill=$bill_type[$vchl];
}else{
	if($post_bill_type!=""){
		$type_of_bill=$post_bill_type;
	}
}

//--- PATIENT ADMIN DATE -------
$admitDate_exp = explode(" ",$chl_row['admit_date']);
$admitDate_exp_final = explode("-",$admitDate_exp[0]);
$admitDate = $admitDate_exp_final[1].'-'.$admitDate_exp_final[2].'-'.substr($admitDate_exp_final[0],2);				
if($admitDate == "00-00-00"){
	$admitDate = '';
}
$admitTime="";
if(trim($admitDate_exp[1]) != "00:00:00"){
	$admitTime = substr(trim($admitDate_exp[1]),0,5);
}

//--- PATIENT DISPATCH DATE -------
$dischDate_exp = explode(" ",$chl_row['disch_date']);
$dischDate_exp_final = explode("-",$dischDate_exp[0]);
$dischDate = $dischDate_exp_final[1].'-'.$dischDate_exp_final[2].'-'.substr($dischDate_exp_final[0],2);
if($dischDate == "00-00-00"){
	$dischDate = '';
}
$dischTime="";
if(trim($dischDate_exp[1]) != "00:00:00"){
	$dischTime = substr(trim($dischDate_exp[1]),0,5);
}

$usr_qry=imw_query("select fname,mname,lname,user_npi,TaxonomyId from users where id='$primaryProviderId'");
$usr_row=imw_fetch_array($usr_qry);
$provider_name=$usr_row['lname'].', '.$usr_row['fname'];
$hcfa_rend=$usr_row['user_npi'];
$TaxonomyId=$usr_row['TaxonomyId'];

//------------------------ Group Detail ------------------------//
$sql = imw_query("select * from groups_new where gro_id='$gro_id'");
while($row=imw_fetch_array($sql)){			
	$group_data[$row['gro_id']]=$row;
}		
//------------------------	Group Detail	------------------------//

//------------------------ Policies Detail ------------------------//
	$pol_info=imw_query("select Address1,Telephone,Zip,City,State,phone_ext,zip_ext from copay_policies");
	$pol_row=imw_fetch_array($pol_info);
//------------------------ Policies Detail ------------------------//

$batch_qry=imw_query("select file_data,ins_comp from batch_file_submitte where Batch_file_submitte_id='$batch_file_submitte_id'");
$batch_qry_row=imw_fetch_array($batch_qry);
$file_data=$batch_qry_row['file_data'];
$ins_comp=$batch_qry_row['ins_comp'];

if($ins_comp=="secondary"){
	$sbr_seg="~SBR*S";
}else{
	$sbr_seg="~SBR*P";
}

$file_data_sbr_exp=explode($sbr_seg,$file_data);
$file_header_data_exp=explode('~',$file_data_sbr_exp[0]);
//pre($file_header_data_exp);
$file_ins_chk=substr($file_header_data_exp[1],-2);
foreach($file_data_sbr_exp as $key => $val){
	$ins_chk="REF*D9*".$encounter_id;
	if(strpos($file_data_sbr_exp[$key],"REF*D9*") == false){
		$ins_chk="REF*6R*".$encounter_id;
	}
	if(strpos($file_data_sbr_exp[$key],$ins_chk) !== false){
		$file_data_exp=explode('~',$sbr_seg.$file_data_sbr_exp[$key]);
	}
}
//pre($file_data_exp);
//------------------------ HCFA Margin Detail ------------------------//
$group_margin_qry=imw_query("select top_margin,left_margin,top_line_margin from create_margins where margin_type='UB04'");
$group_margin=imw_fetch_array($group_margin_qry);
foreach($file_header_data_exp as $key => $val){
	$file_header_data_sub_exp=explode('*',$file_header_data_exp[$key]);
	if($hcfa_fac_group=="" && strpos($file_header_data_exp[$key],'NM1*85')!== false){
		$hcfa_fac_group=$file_header_data_sub_exp[3];
		$hcfa_fac_npi=$file_header_data_sub_exp[9];
		$hcfa_posfac_npi=$hcfa_fac_npi;
		
		if($hcfa_fac_street=="" && strpos($file_header_data_exp[$key+1],'N3*')!== false){
			$other_file_data_sub_exp=explode('*',$file_header_data_exp[$key+1]);
			$hcfa_fac_street=$other_file_data_sub_exp[1];
			$file_header_data_exp[$key+1]="";
		}
		if($hcfa_fac_city=="" && strpos($file_header_data_exp[$key+2],'N4*')!== false){
			$other_file_data_sub_exp=explode('*',$file_header_data_exp[$key+2]);
			$hcfa_fac_city=$other_file_data_sub_exp[1].', '.$other_file_data_sub_exp[2].' '.$other_file_data_sub_exp[3];
			$file_header_data_exp[$key+2]="";
		}
		$file_header_data_exp[$key]="";
	}
	if($hcfa_fac_group=="" && strpos($file_header_data_exp[$key],'PER*')!== false){
		$pol_Telephone="";
		$pol_Telephone=preg_replace('/[^0-9]/','',$file_header_data_sub_exp[4]);
		$hcfa_fac_ph = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$pol_Telephone);
		$file_header_data_exp[$key]="";
	}
	if($hcfa_fac_street=="" && strpos($file_header_data_exp[$key],'NM1*87')!== false){
		if($hcfa_fac_street=="" && strpos($file_header_data_exp[$key+1],'N3*')!== false){
			$other_file_data_sub_exp=explode('*',$file_header_data_exp[$key+1]);
			$hcfa_fac_street=$other_file_data_sub_exp[1];
			$file_header_data_exp[$key+1]="";
		}
		if($hcfa_fac_city=="" && strpos($file_header_data_exp[$key+2],'N4*')!== false){
			$other_file_data_sub_exp=explode('*',$file_header_data_exp[$key+2]);
			$hcfa_fac_city=$other_file_data_sub_exp[1];
			$hcfa_fac_city=$other_file_data_sub_exp[1].', '.$other_file_data_sub_exp[2].' '.$other_file_data_sub_exp[3];
			$file_header_data_exp[$key+2]="";
		}
		$file_header_data_exp[$key]="";
	}
	if($hcfa_posfac_tax=="" && strpos($file_header_data_exp[$key],'REF*EI')!== false){
		$hcfa_posfac_tax=$file_header_data_sub_exp[2];
		$file_header_data_exp[$key]="";
	}
	if($bill_type=="" && strpos($file_header_data_exp[$key],'ST*')!== false){
		$bill_type=$file_header_data_sub_exp[1];
		//$clm_control_num=$file_header_data_sub_exp[2];
		$file_header_data_exp[$key]="";
	}
}
foreach($file_data_exp as $key => $val){
	$file_data_sub_exp=explode('*',$file_data_exp[$key]);
	if(strpos($file_data_exp[$key],'NM1*PR')!== false){
		$hcfa_ins_name_arr[]=$file_data_sub_exp[3];
		$hcfa_Payer_id_arr[]=$file_data_sub_exp[9];
		$ins_qry=imw_query("select contact_name,contact_address,City,State,Zip,zip_ext,Payer_id_pro from insurance_companies where name like'".$file_data_sub_exp[3]."%' and Payer_id='".$file_data_sub_exp[9]."'");
		$ins_row=imw_fetch_array($ins_qry);
		$hcfa_ins_add_arr[]=$ins_row['contact_address'];
		$zip_ext="";
		if($ins_row['zip_ext']!=""){
			$zip_ext="-".$ins_row['zip_ext'];
		}
		$hcfa_ins_csz_arr[]=$ins_row['City'].', '.$ins_row['State'].' '.$ins_row['Zip'].$zip_ext;
		$hcfa_ins_house_code_arr[]=$ins_row['contact_name'];
		$hcfa_Payer_id_pro_arr[]=$ins_row['Payer_id_pro'];
		$hcfa_ins_img="";
		$file_data_exp[$key]="";
	}
	if(strpos($file_data_exp[$key],'NM1*IL')!== false){
		$hcfa_resp_name_arr[]=$file_data_sub_exp[3].', '.$file_data_sub_exp[4].' '.$file_data_sub_exp[5];
		$hcfa_insured_policy_arr[]=$file_data_sub_exp[9];
		$file_data_exp[$key]="";
		
		if(strpos($file_data_exp[$key+1],'N3*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+1]);
			$hcfa_resp_add_arr[]=$other_file_data_sub_exp[1];
			$file_data_exp[$key+1]="";
		}
		
		if(strpos($file_data_exp[$key+2],'N4*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+2]);
			$hcfa_resp_city_arr[]=$other_file_data_sub_exp[1];
			$hcfa_resp_state_arr[]=$other_file_data_sub_exp[2];
			$hcfa_resp_postcode_arr[]=$other_file_data_sub_exp[3];
			$hcfa_resp_areacode='';
			$hcfa_resp_phone='';
			$file_data_exp[$key+2]="";
		}
		
	}
	
	if($hcfa_pat_name=="" && strpos($file_data_exp[$key],'NM1*QC')!== false){
		$hcfa_pat_name=$file_data_sub_exp[3].', '.$file_data_sub_exp[4].' '.$file_data_sub_exp[5];
		$hcfa_insured_policy=$file_data_sub_exp[9];
		$file_data_exp[$key]="";
		
		if($hcfa_pat_add=="" && strpos($file_data_exp[$key+1],'N3*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+1]);
			$hcfa_pat_add=$other_file_data_sub_exp[1];
			$file_data_exp[$key+1]="";
		}
	
		if($hcfa_pat_city=="" && strpos($file_data_exp[$key+2],'N4*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+2]);
			$hcfa_pat_city=$other_file_data_sub_exp[1];
			$hcfa_pat_state=$other_file_data_sub_exp[2];
			$hcfa_pat_postcode=$other_file_data_sub_exp[3];
			$file_data_exp[$key+2]="";
		}
		
		if($hcfa_pat_date=="" && strpos($file_data_exp[$key+3],'DMG*D8*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+3]);
			$hcfa_pat_date=substr($other_file_data_sub_exp[2],4,2).''.substr($other_file_data_sub_exp[2],6,2).''.substr($other_file_data_sub_exp[2],0,4);
			$hcfa_pat_sex=$other_file_data_sub_exp[3];
			$file_data_exp[$key]="";
		}
	}
	
	if($hcfa_resp_dob=="" && strpos($file_data_exp[$key],'DMG*D8*')!== false){
		$hcfa_resp_dob=substr($file_data_sub_exp[2],4,2).' '.substr($file_data_sub_exp[2],6,2).' '.substr($file_data_sub_exp[2],0,4);
		$hcfa_resp_sex=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
	}
	
	if($hcfa_insured_name=="" && strpos($file_data_exp[$key],'NM1*IL')!== false){
		$hcfa_insured_name=$file_data_sub_exp[3].', '.$file_data_sub_exp[4].' '.$file_data_sub_exp[5];
		$hcfa_insured_policy=$file_data_sub_exp[9];
		$file_data_exp[$key]="";
	}
	
	if($hcfa_posfac_name=="" && (strpos($file_data_exp[$key],'NM1*77')!== false || strpos($file_data_exp[$key],'NM1*82')!== false )){
		$hcfa_posfac_name=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
		
		if($hcfa_posfac_add=="" && strpos($file_data_exp[$key+1],'N3*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+1]);
			$hcfa_posfac_add=$other_file_data_sub_exp[1];
			$file_data_exp[$key+1]="";
		}
	
		if($hcfa_posfac_city=="" && strpos($file_data_exp[$key+2],'N4*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+2]);
			$hcfa_posfac_city=$other_file_data_sub_exp[1].', '.$other_file_data_sub_exp[2].' '.$other_file_data_sub_exp[3];
			$file_data_exp[$key+2]="";
		}
	}
	
	if(strpos($file_data_exp[$key],'NM1*71')!== false){
		$hcfa_attending_npi=$file_data_sub_exp[9];
		$hcfa_attending_fname=$file_data_sub_exp[4];
		$hcfa_attending_lname=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
	}
	
	if(strpos($file_data_exp[$key],'NM1*72')!== false){
		$hcfa_operator_npi=$file_data_sub_exp[9];
		$hcfa_operator_fname=$file_data_sub_exp[4];
		$hcfa_operator_lname=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
	}
	
	//pre($file_data_exp);
	if($all_dx_codes=="" && strpos($file_data_exp[$key],'HI*')!== false){
		foreach($file_data_sub_exp as $dkey => $dval){
			$file_data_sub_dx_exp=explode(':',$file_data_sub_exp[$dkey]);
			if(count($file_data_sub_dx_exp)>1){
				foreach($all_dx_codes_arr as $cdkey => $cdval){
					if($file_data_sub_dx_exp[1]==str_replace('.','',$cdval)){
						$all_dx_codes[]=$cdval;
					}
				}
			}
		}
		
		$file_data_exp[$key]="";
		if(strpos($file_data_exp[$key+1],'HI*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+1]);
			foreach($other_file_data_sub_exp as $dkey => $dval){
				$file_data_sub_dx_exp=explode(':',$other_file_data_sub_exp[$dkey]);
				if(count($file_data_sub_dx_exp)>1){
					foreach($all_dx_codes_arr as $cdkey => $cdval){
						if($file_data_sub_dx_exp[1]==str_replace('.','',$cdval)){
							$all_dx_codes[]=$cdval;
						}
					}
				}
			}
			$file_data_exp[$key+1]="";
		}
	}
	
	if(strpos($file_data_exp[$key],'SV1*HC')!== false){
		if(strpos($file_data_exp[$key+1],'DTP*')!== false){
			$file_data_sub_dpt_exp=explode('*',$file_data_exp[$key+1]);
			$file_data_sub_exp[8]=$file_data_sub_dpt_exp[3];
			$file_data_exp[$key+1]="";
		}
		$all_proc_arr[]=$file_data_sub_exp;
		$file_data_exp[$key]="";
	}
	
	if(strpos($file_data_exp[$key],'SV2*')!== false){
		if(strpos($file_data_exp[$key+1],'DTP*')!== false){
			$file_data_sub_dpt_exp=explode('*',$file_data_exp[$key+1]);
			$file_data_sub_exp[8]=$file_data_sub_dpt_exp[3];
			$file_data_exp[$key+1]="";
		}
		$all_proc_arr[]=$file_data_sub_exp;
		$file_data_exp[$key]="";
	}
	
	if($hcfa_auth_no=="" && strpos($file_data_exp[$key],'REF*G1')!== false){
		$hcfa_auth_no=$file_data_sub_exp[2];
		$file_data_exp[$key]="";
	}
	if($hcfa_control_no=="" && strpos($file_data_exp[$key],'REF*F8')!== false){
		$hcfa_control_no=$file_data_sub_exp[2];
		$file_data_exp[$key]="";
	}
	
	if($hcfa_ins_prac_id=="" && strpos($file_data_exp[$key],'PRV*PE')!== false){
		$hcfa_ins_prac_id=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
	}
	
	if($hcfa_tot_chrg=="" && strpos($file_data_exp[$key],'CLM*')!== false){
		$tot_chrg=explode('.',$file_data_sub_exp[2]);
		$hcfa_pat_id=$file_data_sub_exp[1];
		$hcfa_tot_chrg=$tot_chrg[0];
		$hcfa_tot_chrg_cent=$tot_chrg[1];
		$file_data_exp[$key]="";
		$patient_qry=imw_query("select id,fname,mname,lname,DOB,sex,street,city,state,postal_code,zip_ext,phone_home from patient_data where id='$hcfa_pat_id'");
		$patient_row=imw_fetch_array($patient_qry);
	}
	
	if(strpos($file_data_exp[$key],'SBR*')!== false){
		$hcfa_sub_relat_arr[]=$file_data_sub_exp[2];
		$hcfa_plan_name_arr[]=$file_data_sub_exp[3];
		$hcfa_group_name_arr[]=$file_data_sub_exp[4];
		$file_data_exp[$key]="";
	}
	
	if($hcfa_ref_prov=="" && strpos($file_data_exp[$key],'NM1*DN')!== false){
		$hcfa_ref_prov=$file_data_sub_exp[1].' '.$file_data_sub_exp[3].', '.$file_data_sub_exp[4].' '.$file_data_sub_exp[5];
		$hcfa_MDCD="";
		$hcfa_npi=$file_data_sub_exp[9];
		$file_data_exp[$key]="";
	}
}
$patient_id=$patient_row['id'];
if($hcfa_pat_name==""){
	$hcfa_pat_name=$patient_row['lname'].', '.$patient_row['fname'].' '.$patient_row['mname'];
	$PatientDOB = explode("-",$patient_row['DOB']);
	$hcfa_pat_date=$PatientDOB[1].''.$PatientDOB[2].''.$PatientDOB[0];
	$hcfa_pat_sex=$patient_row['sex'];
	$hcfa_pat_add=$patient_row['street'];
	$hcfa_pat_city=$patient_row['city'];
	$hcfa_pat_state=$patient_row['state'];
	$hcfa_pat_postcode=$patient_row['postal_code'];
}
$hcfa_pat_areacode=$patient_row['zip_ext'];
$hcfa_pat_phone = preg_replace('/[^0-9]/','',$patient_row['phone_home']);
$hcfa_pat_areacode = substr($hcfa_pat_phone,0,3);
$hcfa_pat_phone = substr($hcfa_pat_phone,3);
$hcfa_resp_areacode=$hcfa_pat_areacode;
$hcfa_resp_phone=$hcfa_pat_phone;
$hcfa_cur_date=date('m-d-Y');
$hcfa_enc_icd10_point=0;

$hcfa_clm_control_num_type="7";
if($claim_ctrl_no!=""){
	$hcfa_clm_control_num=$claim_ctrl_no;
}else{
	//$hcfa_clm_control_num=billing_global_get_clm_control_num($hcfa_pat_id,$encounter_id,0,$insCheck);
}
if($clm_control_num=="" || $bill_type=="831"){
	$hcfa_clm_control_num_type="";
	$hcfa_clm_control_num="";
}

$hcfa_dx1_point=$all_dx_codes[0];
$hcfa_dx2_point=$all_dx_codes[1];
$hcfa_dx3_point=$all_dx_codes[2];
$hcfa_dx4_point=$all_dx_codes[3];
$hcfa_dx5_point=$all_dx_codes[4];
$hcfa_dx6_point=$all_dx_codes[5];
$hcfa_dx7_point=$all_dx_codes[6];
$hcfa_dx8_point=$all_dx_codes[7];
$hcfa_dx9_point=$all_dx_codes[8];
$hcfa_dx10_point=$all_dx_codes[9];
$hcfa_dx11_point=$all_dx_codes[10];
$hcfa_dx12_point=$all_dx_codes[11];

//--- GET HCFA TOP AND LEFT MARGIN -------
if($print_paper_type=='WithoutPrintub'){
	$top_line_margin_arr=json_decode(html_entity_decode($group_margin['top_line_margin']),true);
	foreach ($top_line_margin_arr as $top_key => $top_value) {
		$top_line_margin_arr[$top_key]=(int)$top_value;
	}
	$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/ub_form_wo.pdf");
	$top_margin=0.1;
	if($group_margin['left_margin']>0){
		$left_margin=$group_margin['left_margin']-0.1;
	}else if($group_margin['left_margin']<0){
		$left_margin=$group_margin['left_margin'];
	}else{
		$left_margin=0.1;
	}
}else{
	if(constant("global_ub_print_red")=="yes"){
		$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/ub_form_red.pdf");
		$top_margin=-9;
		$left_margin=-6.5;
		$wo_page_margion=0;
		$wo_page_margion1=1;
		$wo_page_margion2=0;
		$wo_page_margion3=0;
		$wo_page_margion5=0;
	}else{
		$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/ub_form.pdf");
		$top_margin=-5;
		$left_margin=-5;
		$wo_page_margion=0;
		$wo_page_margion1=0;
		$wo_page_margion2=0;
		$wo_page_margion3=0;
		$wo_page_margion5=0;
	}
}
$pdf->SetAutoPageBreak(false,0);
$pdf->setPrintHeader(false);
$tplidx = $pdf->importPage(1);
$pdf->AddPage();
$pdf->useTemplate($tplidx,0,0,210,295);
$pdf->SetFont('helvetica','',8);
$pdf->Ln($top_margin);

//------------------------ HCFA Margin Detail ------------------------//

$pol_add=$pol_row['Address1'];
if($pol_row['phone_ext']<>"" && $pol_row['phone_ext']<>"0"){
	$phone_ext='( '.$pol_row['phone_ext'].' )';
}
$pol_Telephone=preg_replace('/[^0-9]/','',$pol_row['Telephone']);
$pol_phone1 = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$pol_Telephone);

$pol_phone=$pol_phone1.' '.$phone_ext;

if($pol_row['Zip']){
	$pol_zip=', '.$pol_row['Zip'];
}
if($pol_row['zip_ext']){
	$pol_zip_ext='-'.$pol_row['zip_ext'];
}
$pol_csz=$pol_row['City'].' '.$pol_row['State'].$pol_zip.$pol_zip_ext;

$group_row=$group_data[$gro_id];
$grup_first_arr = explode(' ',$group_row['name']);
$grup_first = $grup_first_arr[0];
array_shift($grup_first_arr);
$grup_last = join(' ',$grup_first_arr);
$sc_group_sec_id=$group_row['sec_id'];
$sc_eni=$group_row['group_Federal_EIN'];
$sc_npi=$group_row['group_NPI'];
$sc_nam=$group_row['name'];
$pol_nam=$sc_nam;
$sc_address=$group_row['group_Address1'];
$group_Telephone=preg_replace('/[^0-9]/','',$group_row['group_Telephone']);
$sc_phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$group_Telephone);
if($group_row['group_Zip']){
	$s_zip=', '.$group_row['group_Zip'];
}
if($group_row['zip_ext']){
	$zip_ext='-'.$group_row['zip_ext'];
}
$sc_csz=$group_row['group_City'].' '.$group_row['group_State'].$s_zip.$zip_ext;
$chk_rem_grp_no="";
if($group_row['rem_address1']!="" && ($group_row['rem_telephone']!="" || $chk_rem_grp_no!="") && $group_row['rem_zip']!="" && $group_row['rem_state']!=""){
	$sc_address=$group_row['rem_address1'];
	if($group_row['rem_telephone']!=""){
		$rem_group_Telephone=preg_replace('/[^0-9]/','',$group_row['rem_telephone']);
		$sc_phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/","$1-$2-$3",$rem_group_Telephone);
	}
	if($group_row['rem_zip']){
		$s_zip=', '.$group_row['rem_zip'];
	}
	if($group_row['rem_zip_ext']){
		$zip_ext='-'.$group_row['rem_zip_ext'];
	}
	$sc_csz=$group_row['rem_city'].' '.$group_row['rem_state'].$s_zip.$zip_ext;
}

$sc_nam=$hcfa_fac_group;
$pol_nam=$sc_nam;
$sc_address=$hcfa_fac_street;
$sc_csz=$hcfa_fac_city;
$sc_phone=$hcfa_fac_ph;

$box_14="3";
if(in_array(strtolower($billing_global_server_name), array('essi','ocean'))){
	$admitTime=$dischTime=" 99";
}
$box_15="1";
if(in_array(strtolower($billing_global_server_name), array('summiteye'))){
	$box_15="2";
}
if(in_array(strtolower($billing_global_server_name), array('austineeye'))){
	$box_14="9";
	if(!in_array($objpriInsuranceCoData_rmark->Payer_id,array('66006'))){
		$box_15="9";
	}
}

	//BOX NO. 1,2,3a
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_1_1']);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(0.1);
	$pdf->Cell(62.9,5,$sc_nam,0,0,'');
	$pdf->Cell(68,5,$pol_nam,0,0,'');
	$pdf->Cell(0,5,$patient_id,8,0,'');
	//BOX NO. 1,2,3b,4
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_1_2']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(63,5,$hcfa_fac_street,0,0,'');
	$pdf->Cell(68,5,$pol_add,0,0,'');
	$pdf->Cell(60,5,$encounter_id,0,0,'');
	$pdf->Cell(0,5,$type_of_bill,0,0,'');
	
	//BOX NO. 1,2
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_1_3']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(63,5,$hcfa_fac_city,0,0,'');
	$pdf->Cell(0,5,$pol_csz,0,0,'');
	$charge_list_dos=substr($all_proc_arr[0][8],4,2).''.substr($all_proc_arr[0][8],6,2).''.substr($all_proc_arr[0][8],2,2);
	//BOX NO. 1,2,5,6
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_1_4']);
	}else{
		$pdf->Ln(5);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(63,5,$sc_phone,0,0,'');
	$pdf->Cell(61,5,$pol_phone,0,0,'');
	$pdf->Cell(24,5,$hcfa_posfac_tax,0,0,'');
	$pdf->Cell(17,5,$charge_list_dos,0,0,'');
	$pdf->Cell(0,5,$charge_list_dos,0,0,'');
				
	//BOX NO. 8a,9a
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_8_1']);
	}else{
		$pdf->Ln(5);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(103,5,'',0,0,'');
	$pdf->Cell(130,5,$hcfa_pat_add,0,0,'');
	$pat_before_margin=5+$wo_page_margion5;
				
	//BOX NO. 8b,9b,9c,9d,9e
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_8_2']);
	}else{
		$pdf->Ln($pat_before_margin);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(2,2,'',0,0,'');
	$pdf->Cell(73,2,$hcfa_pat_name,0,0,'');
	$pdf->Cell(83,2,$hcfa_pat_city,0,0,'');
	$pdf->Cell(10,2,$hcfa_pat_state,0,0,'');
	$pdf->Cell(30,2,$hcfa_pat_postcode,0,0,'');
	
	//BOX NO. 10 to 30
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_10_1']);
	}else{
		$pdf->Ln(9);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(22,1,$hcfa_pat_date,0,0,'');
	$pdf->Cell(6,1,$hcfa_pat_sex,0,0,'');
	$pdf->Cell(14.2,1,$admitDate,0,0,'');
	$pdf->Cell(10,1,$admitTime,0,0,'');
	$pdf->Cell(7,1,$box_14,0,0,'');
	$pdf->Cell(5.1,1,$box_15,0,0,'');
	$pdf->Cell(9,1,$dischTime,0,0,'');
	$pdf->Cell(30,1,'01',0,0,'');
	
	//BOX NO. 38 to 41
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_38_1']);
	}else{
		$pdf->Ln(22);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(2,1,'',0,0,'');
	$pdf->Cell(105,1,$hcfa_pat_name,0,0,'');
	$pdf->Cell(13,1,$prev_ub_arr['ub_39a1'],0,0,'');
	$pdf->Cell(12,1,$prev_ub_arr['ub_39a2'],0,0,'');
	$pdf->Cell(50,1,$prev_ub_arr['ub_39a3'],0,0,'');
	
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_38_2']);
	}else{
		$pdf->Ln(5);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(2,1,'',0,0,'');
	$pdf->Cell(105,1,$hcfa_pat_add,0,0,'');
	$pdf->Cell(13,1,$prev_ub_arr['ub_39b1'],0,0,'');
	$pdf->Cell(12,1,$prev_ub_arr['ub_39b2'],0,0,'');
	$pdf->Cell(50,1,$prev_ub_arr['ub_39b3'],0,0,'');
	
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_38_3']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(2,1,'',0,0,'');
	$pdf->Cell(105,1,$hcfa_pat_city.', '.$hcfa_pat_state.' '.$hcfa_pat_postcode,0,0,'');
	$pdf->Cell(13,1,$prev_ub_arr['ub_39c1'],0,0,'');
	$pdf->Cell(12,1,$prev_ub_arr['ub_39c2'],0,0,'');
	$pdf->Cell(50,1,$prev_ub_arr['ub_39c3'],0,0,'');
	
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_38_4']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(2,1,'',0,0,'');
	$pdf->Cell(105,1,"",0,0,'');
	$pdf->Cell(14,1,$prev_ub_arr['ub_39d1'],0,0,'');
	$pdf->Cell(12,1,$prev_ub_arr['ub_39d2'],0,0,'');
	$pdf->Cell(50,1,$prev_ub_arr['ub_39d3'],0,0,'');
	
	$cod_len=6+$wo_page_margion;
	$pdf->Ln($cod_len);
	$ins_plus=0;
	if($file_ins_chk=="A2"){
		$ins_plus=1;
	}
	$tot_amt=0;
	
	//BOX NO. 42 to 49
	for($k=0;$k<count($all_proc_arr);$k++){
		$rev_code=$all_proc_arr[$k][0+$ins_plus];
		if($rev_code=="SV1" || $rev_code=="SV2" || $rev_code=="SV3"){
			$rev_code="";
		}
		$hcfa_cpt_exp=explode(':',$all_proc_arr[$k][1+$ins_plus]);
	
		if($hcfa_cpt_exp[1]!=""){
			if($print_paper_type=='WithoutPrintub'){
				$pdf->Ln($top_line_margin_arr['top_42_'.$k]);
			}else{
				$pdf->Ln(4.4);
			}
			$chrg=explode('.',$all_proc_arr[$k][2+$ins_plus]);
			$totalAmount=$all_proc_arr[$k][2+$ins_plus];
			$hcfa_dos=$hcfa_dos2=$hcfa_resp_dob=substr($all_proc_arr[$k][8],4,2).''.substr($all_proc_arr[$k][8],6,2).''.substr($all_proc_arr[$k][8],2,2);
			
			$hcfa_dig_exp=explode(':',$all_proc_arr[$k][7]);
			$hcfa_dig_arr=array();
			foreach($hcfa_dig_exp as $dekey => $deval){
				$hcfa_dig_arr[]=$hcfa_dig_point_arr[$deval];
			}
			
			$hcfa_mod_arr=array();
			if($hcfa_cpt_exp[2]!="") $hcfa_mod_arr[]=$hcfa_cpt_exp[2];
			if($hcfa_cpt_exp[3]!="") $hcfa_mod_arr[]=$hcfa_cpt_exp[3];
			if($hcfa_cpt_exp[4]!="") $hcfa_mod_arr[]=$hcfa_cpt_exp[4];
			if($hcfa_cpt_exp[5]!="") $hcfa_mod_arr[]=$hcfa_cpt_exp[5];
			$hcfa_mod=implode(',',$hcfa_mod_arr);
			
			$hcfa_chrg=$chrg[0];
			$hcfa_chrg_cent=$chrg[1];
			$hcfa_unit=number_format($all_proc_arr[$k][4+$ins_plus],2);
			
			$arr = preg_split('/./',$totalAmount);
			$strlen = strlen(substr($totalAmount,0,-3));
			$spaces = 10 - $strlen;
			$stSp = '';
			for($s=0;$s<$spaces;$s++){
				$stSp .= ' ';
			}
			
			$sql = imw_query("select cpt_desc from cpt_fee_tbl WHERE cpt4_code='".$hcfa_cpt_exp[1]."'");
			$row=imw_fetch_array($sql);			
			$cpt_desc=$row['cpt_desc'];
			
			$pdf->Cell($left_margin);
			$pdf->Cell(10,1,$rev_code,0,0,'');
			$pdf->Cell(65,1,$cpt_desc,0,0,'');
			$pdf->Cell(38,1,$hcfa_cpt_exp[1].' '.$hcfa_mod,0,0,'');
			$pdf->Cell(18,1,$hcfa_dos,0,0,'');
			$pdf->Cell(20,1,$hcfa_unit,0,0,'');
			$pdf->Cell(15,1,$stSp.substr($totalAmount,0,-3),0,0,'');
			$pdf->Cell(25,1,substr($totalAmount,-2),0,0,'');
			
			$tot_amt+=$all_proc_arr[$k][2+$ins_plus];
		}
	}
	for($top_name_var=$k;$top_name_var<21;$top_name_var++){
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_42_'.$top_name_var]);
		}else{
			$pdf->Ln(4.4);
		}
	}
	
	$start_page=1;
	$end_page=1;
	$before_tot_price_len=9.5+$wo_page_margion1;			
	//BOX NO. 42(23) to 49(23)
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_42_23']);
	}else{
		$pdf->Ln($before_tot_price_len);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(22,1,'001',0,0,'');
	$pdf->Cell(15,1,1,0,0,'');
	$pdf->Cell(73,1,1,0,0,'');
	$pdf->Cell(36,1,$hcfa_cur_date,0,0,'');
	$strlen = strlen(substr(number_format($tot_amt,2),0,-3));
	$spaces = (14 - $strlen);
	$stSp = '';
	for($s=0;$s<$spaces;$s++){
		$stSp .= ' ';
	}
	$pdf->Cell(19,1,$stSp.substr(number_format($tot_amt,2),0,-3),0,0,'');
	$pdf->Cell(15,1,substr(number_format($tot_amt,2),-2),0,0,'');
	$pdf->Cell(15,1,'0',0,0,'');
				
	//BOX NO. 56
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_56_1']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(170,1,'',0,0,'');
	$pdf->Cell(30,2,$hcfa_fac_npi,0,0,'');
	$after_ins_len=6+$wo_page_margion2;
	//BOX NO. 50a to 57a
	for($k=0;$k<3;$k++){
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_50_'.$k+1]);
		}else{
			if($k==0){
				$pdf->Ln($after_ins_len);
			}else{
				$pdf->Ln(4);
			}
		}
		$pdf->Cell($left_margin);
		$pri_y="";
		if($hcfa_ins_name_arr[$k]){
			$pri_y='y';
		}
		$pdf->Cell(60,1,$hcfa_ins_name_arr[$k],0,0,'');
		$pdf->Cell(34,1,$prev_ub_arr['ub_51a'],0,0,'');
		$pdf->Cell(8,-1,$pri_y,0,0,'');
		$pdf->Cell(2,-1,$pri_y,0,0,'');
		$pdf->Cell(21,-1,$prev_ub_arr['ub_54a1'],0,0,'');
		$pdf->Cell(44,-1,$prev_ub_arr['ub_54a2'],0,0,'');
		$pdf->Cell(10,-1,$hcfa_fac_npi,0,0,'');
		$hcfa_fac_npi="";
	}
	
	for($k=0;$k<3;$k++){
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_58_'.$k+1]);
		}else{
			if($k==0){
				$pdf->Ln(9);
			}else{
				$pdf->Ln(4);
			}
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(65,1,$hcfa_resp_name_arr[$k],0,0,'');
		$pdf->Cell(8,1,$hcfa_sub_relat_arr[$k],0,0,'');
		$pdf->Cell(50,1,$hcfa_insured_policy_arr[$k],0,0,'');
		$pdf->Cell(40,1,$hcfa_group_name_arr[$k],0,0,'');
		$pdf->Cell(30,1,$hcfa_plan_name_arr[$k],0,0,'');
	}
	
	//BOX NO. 63a
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_63_1']);
	}else{
		$pdf->Ln(10);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(80,1,$hcfa_auth_no,0,0,'');
	$pdf->Cell(65,1,$hcfa_control_no,0,0,'');
				
	//BOX NO. 63b
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_63_2']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(80,1,'',0,0,'');
	
	//BOX NO. 63c
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_63_3']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(80,1,'',0,0,'');
	
	//BOX NO. 66 to 68
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_66_1']);
	}else{
		$pdf->Ln(5);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(3,1,'',0,0,'');
				
	$pdf->Cell(20,1,$all_dx_codes['0'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['1'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['2'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['3'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['4'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['5'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['6'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['7'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['8'],0,0,'');
				
	//BOX NO. 66 to 68
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_66_2']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	if($print_paper_type=='WithoutPrintub' && in_array(strtolower($billing_global_server_name), array('patel','seaside'))){
		$pdf->Cell(-4);
	}else{
		$pdf->Cell(-2);
	}
	$pdf->Cell(3,1,0,0,0,'');
	$pdf->Cell(2,1,'',0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['9'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['10'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['11'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['12'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['13'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['14'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['15'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['16'],0,0,'');
	$pdf->Cell(20,1,$all_dx_codes['17'],0,0,'');
	
	//BOX NO.  76
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_74_1']);
	}else{
		$pdf->Ln(10);
	}
	
	if(strpos($file_data_exp[$key],'NM1*71')!== false){
		$hcfa_attending_npi=$file_data_sub_exp[9];
		$hcfa_attending_fname=$file_data_sub_exp[4];
		$hcfa_attending_lname=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
	}
	
	if(strpos($file_data_exp[$key],'NM1*72')!== false){
		$hcfa_operator_npi=$file_data_sub_exp[9];
		$hcfa_operator_fname=$file_data_sub_exp[4];
		$hcfa_operator_lname=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
	}
	
	$pdf->Cell($left_margin);
	$pdf->Cell(143,1,'',0,0,'');
	$pdf->Cell(38,1,$hcfa_attending_npi,0,0,'');
	$pdf->Cell(30,1,$prev_ub_arr['ub_76c'],0,0,'');
				
	//BOX NO. 74 to 76
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_74_2']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(20,1,$prev_ub_arr['ub_74f1'],0,0,'');
	$pdf->Cell(17,1,$prev_ub_arr['ub_74f2'],0,0,'');
	$pdf->Cell(20,1,$prev_ub_arr['ub_74a1'],0,'');
	$pdf->Cell(17,1,$prev_ub_arr['ub_74a2'],0,0,'');
	$pdf->Cell(20,1,$prev_ub_arr['ub_74b1'],0,0,'');
	$pdf->Cell(36,1,$prev_ub_arr['ub_74b2'],0,0,'');
	$pdf->Cell(45,1,$hcfa_attending_lname,0,0,'');
	$pdf->Cell(30,1,$hcfa_attending_fname,0,0,'');
				
	//BOX NO. 77
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_74_3']);
	}else{
		$pdf->Ln(5);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(143,1,'',0,0,'');
	$pdf->Cell(30.5,1,$hcfa_operator_npi,0,0,'');
	$pdf->Cell(7.5,1,$prev_ub_arr['ub_77b'],0,0,'');
	$pdf->Cell(30,1,$prev_ub_arr['ub_77c'],0,0,'');
			
	//BOX NO. 74 to 77
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_74_4']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(20,1,$prev_ub_arr['ub_74c1'],0,0,'');
	$pdf->Cell(17,1,$prev_ub_arr['ub_74c2'],0,0,'');
	$pdf->Cell(20,1,$prev_ub_arr['ub_74d1'],0,0,'');
	$pdf->Cell(17,1,$prev_ub_arr['ub_74d2'],0,0,'');
	$pdf->Cell(20,1,$prev_ub_arr['ub_74e1'],0,0,'');
	$pdf->Cell(36,1,$prev_ub_arr['ub_74e2'],0,0,'');
	$pdf->Cell(45,1,$hcfa_operator_lname,0,0,'');
	$pdf->Cell(30,1,$hcfa_operator_fname,0,0,'');
	$before_80_box_magin=5;
				
	//BOX NO. 78
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_80_1']);
	}else{
		$pdf->Ln($before_80_box_magin);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(12,1,'',0,0,'');
	$pdf->Cell(57,1,$prev_ub_arr['ub_80'],0,0,'');
	$pdf->Cell(6,1,$prev_ub_arr['ub_81a1'],0,0,'');
	$pdf->Cell(74,1,$prev_ub_arr['ub_81a2'],0,0,'');
	$pdf->Cell(30.5,1,$prev_ub_arr['ub_78a'],0,0,'');
	$pdf->Cell(7.5,1,$prev_ub_arr['ub_78b'],0,0,'');
	$pdf->Cell(30,1,$prev_ub_arr['ub_78c'],0,0,'');
				
	//BOX NO. 80 to 78
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_80_2']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(69,1,$hcfa_ins_name_arr[0],0,0,'');
	$pdf->Cell(62,1,$prev_ub_arr['ub_81b2'],0,0,'');
	$pdf->Cell(45,1,$prev_ub_arr['ub_78d'],0,0,'');
	$pdf->Cell(30,1,$prev_ub_arr['ub_78e'],0,0,'');
				
	//BOX NO. 80 to 79
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_80_3']);
	}else{
		$pdf->Ln(5);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(143,1,$hcfa_ins_add_arr[0],0,0,'');
	$pdf->Cell(38,1,$prev_ub_arr['ub_79a'],0,0,'');
	$pdf->Cell(30,1,'',0,0,'');
	
	//BOX NO. 80 to 79
	if($print_paper_type=='WithoutPrintub'){
		$pdf->Ln($top_line_margin_arr['top_80_4']);
	}else{
		$pdf->Ln(4);
	}
	$pdf->Cell($left_margin);
	$pdf->Cell(131,1,$hcfa_ins_csz_arr[0],0,0,'');
	$pdf->Cell(45,1,$prev_ub_arr['ub_79d'],0,0,'');
	$pdf->Cell(30,1,$prev_ub_arr['ub_79e'],0,0,'');

//---- CREATE PDF FILE FOR PRINTING --------
if($fpdiCheck == true){
	$pdf->Output($newfile_ub_path,"F");
	$final_path=str_replace($GLOBALS['fileroot'],$GLOBALS['webroot'],$newfile_ub_path);
	print '
		<script type="text/javascript">
			window.open(\''.$final_path.'\',"printUB","resizable=1,width=650,height=450");
		</script>
	';	
}
?>
