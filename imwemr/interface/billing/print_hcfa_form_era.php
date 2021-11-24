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
//A2 -  ins
//A1 - Prof or anes

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
$newfile_hcfa_path=write_html('','hcfa_form.pdf');
$batch_file_submitte_id=$_REQUEST['batch_file_submitte_id'];
$vchl=$_REQUEST['vchl'];
$vchld=$_REQUEST['vchld'];
$vchld_exp=explode(',',$vchld);

$chl_qry=imw_query("select encounter_id,all_dx_codes,primaryProviderId from patient_charge_list where charge_list_id='$vchl'");
$chl_row=imw_fetch_array($chl_qry);
$all_dx_codes_arr=unserialize(html_entity_decode($chl_row['all_dx_codes']));
$encounter_id=$chl_row['encounter_id'];
$primaryProviderId=$chl_row['primaryProviderId'];

$usr_qry=imw_query("select fname,mname,lname,user_npi,TaxonomyId from users where id='$primaryProviderId'");
$usr_row=imw_fetch_array($usr_qry);
$provider_name=$usr_row['lname'].', '.$usr_row['fname'];
$hcfa_rend=$usr_row['user_npi'];
$TaxonomyId=$usr_row['TaxonomyId'];

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

$file_ins_chk=substr($file_header_data_exp[1],-2);
foreach($file_data_sbr_exp as $key => $val){
	$ins_chk="REF*D9*".$encounter_id;
	if(strpos($file_data_sbr_exp[$key],"REF*D9*") == false){
		$ins_chk="REF*6R*".$encounter_id;
	}
	if(strpos($file_data_sbr_exp[$key],$ins_chk) !== false){
		$file_data_exp=explode('~',$sbr_seg.$file_data_sbr_exp[$key]);
	}
	/*foreach($vchld_exp as $chld_key => $chld_val){
		if(strpos($file_data_sbr_exp[$key],$chld_val) !== false){
			$file_data_exp=explode('~',$sbr_seg.$file_data_sbr_exp[$key]);
		}
	}*/
}
//pre($file_header_data_exp);
//pre($file_data_sbr_exp);
//pre($file_data_exp);
//------------------------ HCFA Margin Detail ------------------------//
$group_margin_qry=imw_query("select top_margin,left_margin,top_line_margin from create_margins where margin_type='HCFA'");
$group_margin=imw_fetch_array($group_margin_qry);

foreach($file_header_data_exp as $key => $val){
	$file_header_data_sub_exp=explode('*',$file_header_data_exp[$key]);
	if($hcfa_fac_group=="" && strpos($file_header_data_exp[$key],'NM1*85')!== false){
		$hcfa_fac_group=$file_header_data_sub_exp[3];
		$hcfa_fac_npi=$file_header_data_sub_exp[9];
		$hcfa_posfac_npi=$hcfa_fac_npi;
		$file_header_data_exp[$key]="";
	}
	if($hcfa_fac_group=="" && strpos($file_header_data_exp[$key],'PER*')!== false){
		$hcfa_fac_phcode=substr($file_header_data_sub_exp[4],0,3);
		$hcfa_fac_ph=substr($file_header_data_sub_exp[4],3);
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
	if($hcfa_ins_name=="" && strpos($file_data_exp[$key],'NM1*PR')!== false){
		$hcfa_ins_name=$file_data_sub_exp[3];
		$hcfa_Payer_id=$file_data_sub_exp[9];

		$ins_qry=imw_query("select contact_name,contact_address,City,State,Zip,zip_ext,Payer_id_pro from insurance_companies where name='$hcfa_ins_name' and Payer_id='$hcfa_Payer_id'");
		$ins_row=imw_fetch_array($ins_qry);
		$hcfa_ins_add=$ins_row['contact_address'];
		$zip_ext="";
		if($ins_row['zip_ext']!=""){
			$zip_ext="-".$ins_row['zip_ext'];
		}
		$hcfa_ins_csz=$ins_row['City'].', '.$ins_row['State'].' '.$ins_row['Zip'].$zip_ext;
		$hcfa_ins_house_code=$ins_row['contact_name'];
		$hcfa_Payer_id_pro=$ins_row['Payer_id_pro'];
		$hcfa_ins_img="";
		$file_data_exp[$key]="";
	}
	if($hcfa_resp_name=="" && strpos($file_data_exp[$key],'NM1*IL')!== false){
		$hcfa_resp_name=$file_data_sub_exp[3].', '.$file_data_sub_exp[4].' '.$file_data_sub_exp[5];
		$hcfa_policy=$file_data_sub_exp[9];
		$hcfa_group_no=$hcfa_policy;
		$file_data_exp[$key]="";
		
		if($hcfa_resp_add=="" && strpos($file_data_exp[$key+1],'N3*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+1]);
			$hcfa_resp_add=$other_file_data_sub_exp[1];
			$file_data_exp[$key+1]="";
		}
		
		if($hcfa_resp_city=="" && strpos($file_data_exp[$key+2],'N4*')!== false){
			$other_file_data_sub_exp=explode('*',$file_data_exp[$key+2]);
			$hcfa_resp_city=$other_file_data_sub_exp[1];
			$hcfa_resp_state=$other_file_data_sub_exp[2];
			$hcfa_resp_postcode=$other_file_data_sub_exp[3];
			$hcfa_resp_areacode='';
			$hcfa_resp_phone='';
			$hcfa_insured_policy=$hcfa_policy;
			$file_data_exp[$key+2]="";
		}
		
	}
	
	if($hcfa_resp_dob=="" && strpos($file_data_exp[$key],'DMG*D8*')!== false){
		$hcfa_resp_dob=substr($file_data_sub_exp[2],4,2).' '.substr($file_data_sub_exp[2],6,2).' '.substr($file_data_sub_exp[2],0,4);
		$hcfa_resp_sex=$file_data_sub_exp[3];
		$file_data_exp[$key]="";
	}

	if($hcfa_other_ins_name=="" && strpos($file_data_exp[$key],'NM1*PR')!== false){
		$hcfa_other_ins_name=$file_data_sub_exp[3];
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
	
	if($hcfa_federal=="" && strpos($file_data_exp[$key],'REF*G2')!== false){
		$hcfa_federal=$file_data_sub_exp[2];
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
		$patient_qry=imw_query("select fname,mname,lname,DOB,sex,street,city,state,postal_code,zip_ext,phone_home from patient_data where id='$hcfa_pat_id'");
		$patient_row=imw_fetch_array($patient_qry);
	}
	
	if($hcfa_sub_relat=="" && strpos($file_data_exp[$key],'SBR*')!== false){
		$hcfa_sub_relat=$file_data_sub_exp[2];
		if($file_data_sub_exp[4]!=""){
			$hcfa_plan_name=$file_data_sub_exp[4];
		}else{
			$hcfa_plan_name=$file_data_sub_exp[3];
		}
		$file_data_exp[$key]="";
	}
	
	if($hcfa_ref_prov=="" && strpos($file_data_exp[$key],'NM1*DN')!== false){
		$hcfa_ref_prov=$file_data_sub_exp[1].' '.$file_data_sub_exp[3].', '.$file_data_sub_exp[4].' '.$file_data_sub_exp[5];
		$hcfa_MDCD="";
		$hcfa_npi=$file_data_sub_exp[9];
		$file_data_exp[$key]="";
	}
}
		//pre($file_data_exp);
		$hcfa_pat_name=$patient_row['lname'].', '.$patient_row['fname'].' '.$patient_row['mname'];
		$PatientDOB = explode("-",$patient_row['DOB']);
		$hcfa_pat_date=$PatientDOB[1].' '.$PatientDOB[2].' '.$PatientDOB[0];
		$hcfa_pat_sex=$patient_row['sex'];
		$hcfa_pat_add=$patient_row['street'];
		$hcfa_pat_city=$patient_row['city'];
		$hcfa_pat_state=$patient_row['state'];
		$hcfa_pat_postcode=$patient_row['postal_code'];
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
		
		//$chk_work_val=str_replace(' HCFA work 9a ','',$hcfa_enc_data_arr[$f]);
		//$chk_auto_val=str_replace(' HCFA auto 9a ','',$hcfa_enc_data_arr[$f]);
		//$hcfa_pat_occup=str_replace(' HCFA 11b ','',$hcfa_enc_data_arr[$f]);
		
		//$hcfa_onset_date=str_replace(' HCFA 14 ','',$hcfa_enc_data_arr[$f]);	
		//$hcfa_admit_date=str_replace(' HCFA 18a ','',$hcfa_enc_data_arr[$f]);
		//$hcfa_disch_date=str_replace(' HCFA 18b ','',$hcfa_enc_data_arr[$f]);
		
		//$hcfa_notes=str_replace(' HCFA 19 ','',$hcfa_enc_data_arr[$f]);
		//$hcfa_lab=str_replace(' HCFA 20 ','',$hcfa_enc_data_arr[$f]);	
		
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
		
		$hcfa_approval=str_replace(' HCFA 23 ','',$hcfa_enc_data_arr[$f]);
		
		$hcfa_tot_paid="";
		$hcfa_paid_cent="";	
		$hcfa_tot_bal="";
		$hcfa_bal_cent="";
		
		$hcfa_sc_name=str_replace(' HCFA 31_4 ','',$hcfa_enc_data_arr[$f]);
		
		$hcfa_posfac_ph=str_replace(' HCFA 32_2b ','',$hcfa_enc_data_arr[$f]);
	
		
		
		if($hcfa_pat_sex == "M" || $hcfa_resp_sex == "M"){
			$Sx = 12;
			$S2x = 2;
		}
		else{
			$Sx = 0;
			$S2x = 20;
		}
		
		if($hcfa_pat_status == "married"){
			$Mx = -26;
		}
		elseif($hcfa_pat_status == "single"){
			$Mx=-42;
		}
		else{
			$Mx=-11;
		}
		
		//--- GET HCFA TOP AND LEFT MARGIN -------
		if($print_paper_type=='PrintCms_white'){
			$left_marg = - 10;
			$top_marg = ($group_margin['top_margin'] - 5);
			$left_marg = ($group_margin['left_margin'] + $left_marg);
			$left_marg = $left_marg == 0 ? 1 : $left_marg;
			$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/HCFA_FORM_WO.pdf");
		}else{
			$pagecount = $pdf->setSourceFile("../../library/html_to_pdf/predefined_forms/HCFA_FORM_ICD10.pdf");
			$left_marg=0.1;
		}
		$pdf->setPrintHeader(false);
		$tplidx = $pdf->importPage(1);
		$pdf->AddPage();
		$pdf->useTemplate($tplidx,0,0);			
		$pdf->SetFont('Courier','',10);
		$page_count++;
		if($print_paper_type=='PrintCms_white'){
			$pdf->Ln($top_marg);
		}else{
			$pdf->Ln(8);
		}
		
		//BOX NO. 0
		$pdf->Cell(110);				
		$pdf->Cell(115,1,$hcfa_ins_name,12,0,'');
		$pdf->Ln(5);
		$pdf->Cell(110);
		$pdf->Cell(115,1,$hcfa_ins_add,0,0,'');
		$pdf->Ln(5);
		$pdf->Cell(110);
		if($hcfa_ins_csz){
			$pdf->Cell(115,1,$hcfa_ins_csz,0,0,'');
		}
		$pdf->Ln(7);
		$insName = strtoupper($hcfa_ins_name);
		$insHouseCode = strtoupper($hcfa_ins_house_code);

		//BOX NO. 1,1a
		if($insName == "MEDICARE" || $insHouseCode == "MEDICARE" || in_array($hcfa_Payer_id_pro,$arr_RRM_payers) || in_array($hcfa_Payer_id,$arr_RRM_payers)){
			$InsComImgWidth = -3;
		}
		else if($insName == "MEDICAID" || $insHouseCode == "MEDICAID" || count($ins_house_code)>0){
			$InsComImgWidth = 14;
		}
		else if($insName == "MEDICAID/UNISYS" || $insHouseCode == "MEDICAID/UNISYS"){
			$InsComImgWidth = 14;
		}
		else if($insName == "TRICARE CHAMPUS" || $insHouseCode == "TRICARE CHAMPUS"){
			$InsComImgWidth = 32;
		}
		else if($insName == "CHAMPVA" || $insHouseCode == "CHAMPVA"){
			$InsComImgWidth = 55;
		}
		else if($insName == "GROUP HEALTH PLAN" || $insHouseCode == "GROUP HEALTH PLAN"){
			$InsComImgWidth = 73;
		}
		else if($insName == "FECA BLKLUNG" || $insHouseCode == "FECA BLKLUNG"){
			$InsComImgWidth = 93;
		}
		else{
			if($insName == "WORK COMP" || $insHouseCode == "WORK COMP"){
				$InsComImgWidth = 108;
			}else{
				$InsComImgWidth = 108;
			}
		}
		
		$pdf->Cell($left_marg);
		$pdf->Cell($InsComImgWidth,10,'',0,0,'');
		$pdf->Cell(125-$InsComImgWidth,10,'X',0,0,'');
		$pdf->Cell(115,10,$hcfa_policy,0,0,'');
		$pdf->Ln(9);
		
		if($hcfa_pat_sex == "Male"){
			$Sx = 12;
		}
		else{
			$Sx = 0;
		}
		//BOX NO. 2,3,4
		$pdf->Cell($left_marg);
		$pdf->Cell(75,9,$hcfa_pat_name,0,0,'');
		$pdf->Cell(38-$Sx,9,$hcfa_pat_date,0,0,'');
		$pdf->Cell(10+$Sx,9,'X',0,0,'');
		$pdf->Cell(70,9,$hcfa_resp_name,0,0,'');
		$pdf->Ln(9);
		
		//BOX NO. 5a,6,7
		$pdf->Cell($left_marg);
		$relationship = $hcfa_sub_relat;
		if($relationship == "18"){
			$sRx = 1;
		}
		elseif($relationship == "01"){
			$sRx = 14;
		}
		elseif($relationship == "G8" || $relationship == "G8"){
			$sRx = 24;
		}
		else{
			$sRx = 37;
		}
		
		$pdf->Cell(77+$sRx,9,$hcfa_pat_add,0,0,'');
		$pdf->Cell(47-$sRx,8,'X',0,0,'');
		$pdf->Cell(10,9,$hcfa_resp_add,0,0,'');
		$pdf->Ln(8);
		
		
		//BOX NO. 5b,5c,8,7b,7c
		$pdf->Cell($left_marg);
		$pdf->Cell(61,9,$hcfa_pat_city,0,0,'');
		$pdf->Cell(64,9,$hcfa_pat_state,0,0,'');
		
		$pdf->Cell(57,9,$hcfa_resp_city,0,0,'');
		$pdf->Cell(10,9,$hcfa_resp_state,0,0,'');
		$pdf->Ln(8);
		
		//BOX NO. 5d,5e,5f,7d,7e,7f
		$pdf->Cell($left_marg);
		$pdf->Cell(34,9,$hcfa_pat_postcode,0,0,'');
		$pdf->Cell(11,9,$hcfa_pat_areacode,0,0,'');
		$pdf->Cell(82,9,$hcfa_pat_phone,0,0,'');
		
		$WD = $left_marg + 94;
		$HG = $top_marg + 62;
		$pdf->Cell(33,9,$hcfa_resp_postcode,0,0,'');
		$pdf->Cell(11,9,$hcfa_resp_areacode,0,0,'');
		$pdf->Cell(10,9,$hcfa_resp_phone,0,0,'');
		$pdf->Ln(7);
		
		//BOX NO. 9,11
		$pdf->Cell($left_marg);
		$pdf->Cell(77,11,$hcfa_insured_name,0,0,'');
		$pdf->Cell(48,10,'',0,0,'');
		$pdf->Cell(10,11,$hcfa_group_no,0,0,'');
		$pdf->Ln(8);
		
		//BOX NO. 9a,10a,10b,10c,11a,11a1
		$pdf->Cell($left_marg);
		if($chk_work_val!=""){
			$final_width=1;
		}else{
			$final_width=16;
		}
		if($chk_auto_val!=""){
			$final_width_auto=1;
		}else{
			$final_width_auto=16;
		}
		
		$pdf->Cell(82+$final_width,12,$hcfa_insured_policy,0,0,'');	
		$pdf->Cell(46-$final_width,14,'X',0,0,'');
		$pdf->Cell(2);
		$pdf->Cell(35+$S2x,15,$hcfa_resp_dob,0,0,'');
		$pdf->Cell(2,14,'X',0,0,'');
		$pdf->Ln(8);
			
		//BOX NO. 9b,11b
		$pdf->Cell($left_marg);
		$pdf->Cell(82+$final_width_auto,15,'',0,0,'');
		$pdf->Cell(47-$final_width_auto,15,'X',0,0,'');
		$pdf->Cell(19,14,$chk_auto_st,0,0,'');
		$pdf->Cell(10,14,$hcfa_pat_occup,0,0,'');
		$pdf->Ln(9);
		
		//BOX NO. 9c,11c
		$pdf->Cell($left_marg);
		$pdf->Cell(98,10,'',0,0,'');
		$pdf->Cell(28,14,'X',0,0,'');
		$pdf->Cell(6,12,$hcfa_plan_name,0,0,'');
		$pdf->Ln(9);
		
		if($insuredName != 'None' && !in_array(strtolower($billing_global_server_name), array('clearvue'))){
			$health= 1;
		}else{
			$health= 14;
		}
		
		//BOX NO. 9d,10d,11d
		$pdf->Cell($left_marg);
		$pdf->Cell(125+$health,12,$hcfa_other_ins_name,0,0,'');
		$pdf->Cell(2,13,'X',0,0,'');
		$pdf->Ln(18);

		//BOX NO. 12,13
		$pdf->Cell($left_marg);
		$pdf->Cell(15,10,'',0,0,'');
		$pdf->Cell(72,8,'Signature On File',0,0,'');				
		$pdf->Cell(60,8,$hcfa_cur_date,0,0,'');
		$pdf->Cell(60,8,'Signature On File',0,0,'');
		$pdf->Ln(10);
		
		//BOX NO. 14,15,16
		$pdf->Cell($left_marg);
		$pdf->Cell(85,8,$hcfa_onset_date,0,0,'');
		$pdf->Ln(3);
		
		//BOX NO. 17a
		$pdf->Cell($left_marg);
		$pdf->Cell(85,8,'',0,0,'');
		$pdf->Cell(85,10,$hcfa_MDCD,0,0,'');
		$pdf->Ln(5);
		
		//BOX NO. 17,17b,18a,18b
		$pdf->Cell(-2);
		$pdf->Cell($left_marg);
		$pdf->Cell(85,8,$hcfa_ref_prov,0,0,'');
		$pdf->Cell(50,8,$hcfa_npi,0,0,'');	
		$pdf->Cell(35,9,$hcfa_admit_date,0,0,'');
		$pdf->Cell(10,9,$hcfa_disch_date,0,0,'');	
		$pdf->Ln(7);
		
		//BOX NO. 19,20
		$pdf->Cell($left_marg);
		$pdf->Cell(139,10,$hcfa_notes,0,0,'');
		$pdf->Cell(2,11,'X',0,0,'');
		$pdf->Ln(6);
		
		//BOX NO. 21a
		$pdf->Cell($left_marg);
		$pdf->Cell(102);
		$pdf->Cell(2,10,$hcfa_enc_icd10_point,0,0,'');	
		$pdf->Ln(3);
		
		//BOX NO. 21a,21b,21c,21d,21e,21f,21g,21h,21i,21j,21k,21l,22a,22b,23					
		$pdf->Cell($left_marg);
		$pdf->Cell(3,10,'',0,0,'');	
		$pdf->Cell(35,10,$hcfa_dx1_point,0,0,'');
		$pdf->Cell(33,10,$hcfa_dx2_point,0,0,'');
		$pdf->Cell(32,10,$hcfa_dx3_point,0,0,'');
		$pdf->Cell(23,10,$hcfa_dx4_point,0,0,'');
		$pdf->Cell(28,10,$hcfa_clm_control_num_type,0,0,'');
		$pdf->Cell(25,10,$hcfa_clm_control_num,0,0,'');

		$pdf->Ln(4.4);
		$pdf->Cell($left_marg);
		$pdf->Cell(3,10,'',0,0,'');
		$pdf->Cell(35,10,$hcfa_dx5_point,0,0,'');
		$pdf->Cell(33,10,$hcfa_dx6_point,0,0,'');
		$pdf->Cell(32,10,$hcfa_dx7_point,0,0,'');
		$pdf->Cell(35,10,$hcfa_dx8_point,0,0,'');
		
		$pdf->Ln(4.4);
		$pdf->Cell($left_marg);
		$pdf->Cell(3,10,'',0,0,'');
		$pdf->Cell(35,10,$hcfa_dx9_point,0,0,'');
		$pdf->Cell(33,10,$hcfa_dx10_point,0,0,'');
		$pdf->Cell(32,10,$hcfa_dx11_point,0,0,'');
		$pdf->Cell(35,10,$hcfa_dx12_point,0,0,'');	
		
		//BOX NO. 23
		$pdf->Cell($left_marg);
		$pdf->Cell(50,9,$hcfa_approval,0,0,'');
		$pdf->Ln(9);
		$hcfa_dig_point_arr=array("1"=>"A","2"=>"B","3"=>"C","4"=>"D","5"=>"E","6"=>"F","7"=>"G","8"=>"H","9"=>"I","10"=>"J","11"=>"K","12"=>"L");
		$d=0;
		$ins_plus=0;
		if($file_ins_chk=="A2"){
			$ins_plus=1;
		}
		for($k=0;$k<count($all_proc_arr);$k++){
			if($k<6){
				$d++;
				$pdf->Cell($left_marg);
				$pdf->Ln(3.4);
				$pdf->Ln(5);
				$hcfa_start_time=$hcfa_end_time=$hcfa_acc_anes_unit=$hcfa_admin_cpt_units=$hcfa_anes_total_units="";
				
				$pdf->Cell(-2);
				$chrg=explode('.',$all_proc_arr[$k][2+$ins_plus]);
				$hcfa_dos=$hcfa_dos2=$hcfa_resp_dob=substr($all_proc_arr[$k][8],4,2).' '.substr($all_proc_arr[$k][8],6,2).' '.substr($all_proc_arr[$k][8],2,2);
				$hcfa_pos=$all_proc_arr[$k][5];
				$hcfa_cpt_exp=explode(':',$all_proc_arr[$k][1+$ins_plus]);
				$hcfa_dig_exp=explode(':',$all_proc_arr[$k][7]);
				$hcfa_dig_arr=array();
				foreach($hcfa_dig_exp as $dekey => $deval){
					$hcfa_dig_arr[]=$hcfa_dig_point_arr[$deval];
				}
				$hcfa_mod1=$hcfa_cpt_exp[2];
				$hcfa_mod2=$hcfa_cpt_exp[3];
				$hcfa_mod3=$hcfa_cpt_exp[4];
				$hcfa_mod4=$hcfa_cpt_exp[5];
				$hcfa_chrg=$chrg[0];
				$hcfa_chrg_cent=$chrg[1];
				$hcfa_unit=$all_proc_arr[$k][4+$ins_plus];
				//$hcfa_rend="";
				$chrg_space = 13;
				$chrg_cent_space = 15;
				if(strlen($hcfa_chrg)<7){
					$chrg_space = $chrg_space+((7-strlen($hcfa_chrg))*2);
					$chrg_cent_space=$chrg_cent_space-((7-strlen($hcfa_chrg))*2);
				}
				$pdf->Cell(23,10,$hcfa_dos,0,0,'');
				$pdf->Cell(20,10,$hcfa_dos2,0,0,'');
				$pdf->Cell(20,10,$hcfa_pos,0,0,'');
				$pdf->Cell(17,10,$hcfa_cpt_exp[1],0,0,'');
				$pdf->Cell(8,10,$hcfa_mod1,0,0,'');
				$pdf->Cell(8,10,$hcfa_mod2,0,0,'');
				$pdf->Cell(8,10,$hcfa_mod3,0,0,'');
				$pdf->Cell(7,10,$hcfa_mod4,0,0,'');
				$pdf->Cell($chrg_space,10,implode(',',$hcfa_dig_arr),0,0,'');
				$pdf->Cell($chrg_cent_space,10,$hcfa_chrg,0,0,'');
				$pdf->Cell(8,10,$hcfa_chrg_cent,0,0,'');
				$pdf->Cell(25,10,$hcfa_unit,0,0,'');
				$pdf->Cell(33,10,$hcfa_rend,0,0,'');
			}
		}	
		
		
		//--- LINE BREAK CHECK AFTER PROCEDURE DISPLAY ----
		if($d == 1){
			$ln = 50;
		}
		else if($d == 2){
			$ln = 42;
		}
		else if($d == 3){
			$ln = 33;
		}
		else if($d == 4){
			$ln = 24;
		}
		else if($d == 5){
			$ln = 16;
		}
		else{
			$ln = 8;
		}
		
		$pdf->Ln($ln);
		
		//BOX NO. 25,26,27,28,29,30
		$pdf->Cell($left_marg);	
		$pdf->Cell(43,10,$hcfa_federal,0,0,'');
		$pdf->Cell(12,10,'X',0,0,'');
		
		//-- TOTAL CHARGES OF A SINGLE ENCOUNTER ------
		$totalAmt = $hcfa_tot_chrg;
		$totalCent = $hcfa_tot_chrg_cent;
		//-- TOTAL CHARGES OF A SINGLE ENCOUNTER ------
		
		//--- TOTAL PAID AMOUNT FOR SINGLE PROCEDURE ------
		$amtPaid = $hcfa_tot_paid;
		$amtPaidCent = $hcfa_paid_cent;
		//--- TOTAL PAID AMOUNT FOR SINGLE PROCEDURE ------
		
		//--- GET TOTAL BALANCE OF SINGLE PROCEDURES -----
		$totalBalance = $hcfa_tot_bal;
		$totalBalanceCent = $hcfa_bal_cent;
		//--- GET TOTAL BALANCE OF SINGLE PROCEDURES -----
	
		$chrg_space = 34;
		$chrg_cent_space = 17;
		if(strlen($totalAmt)<8){
			$chrg_space = $chrg_space+((8-strlen($totalAmt))*2);
			$chrg_cent_space=$chrg_cent_space-((8-strlen($totalAmt))*2);
		}
		
		$paid_space = 10;
		$paid_cent_space = 15;
		if(strlen($amtPaid)<7){
			$paid_space = $paid_space+((7-strlen($amtPaid))*2);
			$paid_cent_space=$paid_cent_space-((7-strlen($amtPaid))*2);
		}
		
		$bal_space = 6;
		$bal_cent_space = 17;
		if(strlen($totalBalance)<8){
			$bal_space = $bal_space+((8-strlen($totalBalance))*2);
			$bal_cent_space=$bal_cent_space-((8-strlen($totalBalance))*2);
		}
	
		
		$pdf->Cell(36,10,$hcfa_pat_id,0,0,'');
		$pdf->Cell($chrg_space,10,'X',0,0,'');				
		$pdf->Cell($chrg_cent_space,10,$totalAmt,0,0,'');
		$pdf->Cell($paid_space,10,$totalCent,0,0,'');
		$pdf->Cell($paid_cent_space,10,$amtPaid,0,0,'');
		$pdf->Cell($bal_space,10,$amtPaidCent,0,0,'');
		$pdf->Cell($bal_cent_space,10,$totalBalance,0,0,'');
		$pdf->Cell(23,10,$totalBalanceCent,0,0,'');		
		$pdf->Ln(2);
				
		//BOX NO. 31,32,33
		$pdf->Cell($left_marg);
		$pdf->Cell(97,9,'',0,0,'');
		$pdf->Cell(66,22,$hcfa_posfac_ph,0,0,'');
		$pdf->Cell(11,15,$hcfa_fac_phcode,0,0,'');
		$pdf->Cell(65,15,$hcfa_fac_ph,0,0,'');
		$pdf->Ln(5);
		$pdf->Cell(53,10,'',0,0,'');							
		$pdf->Cell(68,12,$hcfa_posfac_name,0,0,'');									
		$pdf->Cell(35,12,$hcfa_fac_group,0,0,'');
		$pdf->Ln(5);
		$pdf->Cell(53,10,'',0,0,'');
		$pdf->Cell(68,10,$hcfa_posfac_add,0,0,'');
		$pdf->Cell(65,10,$hcfa_fac_street,0,0,'');
		$pdf->Ln(3);
		$pdf->Cell(30,10,$provider_name,0,0,'');
		$pdf->Cell(23,18,$hcfa_cur_date,0,0,'');
		$pdf->Cell(68,12,$hcfa_posfac_city,0,0,'');
		$pdf->Cell(63,12,$hcfa_fac_city,0,0,'');
		$pdf->Ln(5);
		$pdf->Cell(58,12,'',0,0,'');
		$pdf->Cell(28,12,$hcfa_posfac_npi,0,0,'');
		$pdf->Cell(38,12,$hcfa_posfac_tax,0,0,'');
		$pdf->Cell(28,12,$hcfa_fac_npi,0,0,'');
		$pdf->Cell(76,12,$hcfa_ins_prac_id,0,0,'');
		

//---- CREATE PDF FILE FOR PRINTING --------
if($fpdiCheck == true){
	$pdf->Output($newfile_hcfa_path,"F");
	$final_path=str_replace($GLOBALS['fileroot'],$GLOBALS['webroot'],$newfile_hcfa_path);
	print '
		<script type="text/javascript">
			window.open(\''.$final_path.'\',"printHcfa","resizable=1,width=650,height=450");
		</script>
	';	
}
?>
