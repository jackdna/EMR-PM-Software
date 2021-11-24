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
//include_once(dirname(__FILE__)."/../../library/html_to_pdf/fpdi/fpdi.php");
include_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
include_once(dirname(__FILE__)."/../../library/classes/billing_functions.php");
use setasign\Fpdi\Tcpdf\Fpdi;
$pdf = new Fpdi();
$fpdiCheck=false;
$operator_id=$_SESSION['authId'];
$sel_hcfa=implode(',',$selectpatient);
$time_hcfa=time();
$print_paper_type=$PrintCms_white_chk;
$newfile_hcfa_path=write_html('','hcfa_form.pdf');
/*function destroy($newfile_hcfa_path) {
	if (!is_dir($dir)) {
		mkdir($dir, 0777);
	}
}
destroy($newfile_hcfa_path);*/
//------------------------ HCFA Margin Detail ------------------------//
$group_margin_qry=imw_query("select top_margin,left_margin,top_line_margin from create_margins where margin_type='HCFA'");
$group_margin=imw_fetch_array($group_margin_qry);
//------------------------ HCFA Margin Detail ------------------------//
$sel_hcfa_data=imw_query("select * from previous_hcfa where previous_hcfa_id in($sel_hcfa) order by previous_hcfa_id desc");
while($fet_rec=imw_fetch_array($sel_hcfa_data)){
$hcfa_enc_data=$fet_rec['hcfa_data'];
if($hcfa_enc_data){
	$fpdiCheck=true;
	$hcfa_enc_data_arr=array();
	$hcfa_enc_data_arr=explode('--~~',$hcfa_enc_data);
	//pre($hcfa_enc_data_arr);exit();
	for($f=0;$f<count($hcfa_enc_data_arr);$f++){
		
		//Top Insurance data
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 0 ")){
			$hcfa_ins_name=str_replace(' HCFA 0 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 0a ")){
			$hcfa_ins_add=str_replace(' HCFA 0a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 0b ")){
			$hcfa_ins_csz=str_replace(' HCFA 0b ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 0c ")){
			$hcfa_ins_house_code=str_replace(' HCFA 0c ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 0d ")){
			$hcfa_Payer_id_pro=str_replace(' HCFA 0d ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 0e ")){
			$hcfa_Payer_id=str_replace(' HCFA 0e ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 1
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 1 ")){
			$hcfa_ins_img=str_replace(' HCFA 1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 1a ")){
			$hcfa_policy=str_replace(' HCFA 1a ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 2
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 2 ")){
			$hcfa_pat_name=str_replace(' HCFA 2 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 3
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 3a ")){
			$hcfa_pat_date=str_replace(' HCFA 3a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 3b ")){
			$hcfa_pat_sex=str_replace(' HCFA 3b ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 4
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 4 ")){
			$hcfa_resp_name=str_replace(' HCFA 4 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 5
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 5a ")){
			$hcfa_pat_add=str_replace(' HCFA 5a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 5b ")){
			$hcfa_pat_city=str_replace(' HCFA 5b ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 5c ")){
			$hcfa_pat_state=str_replace(' HCFA 5c ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 5d ")){
			$hcfa_pat_postcode=str_replace(' HCFA 5d ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 5e ")){
			$hcfa_pat_areacode=str_replace(' HCFA 5e ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 5f ")){
			$hcfa_pat_phone=str_replace(' HCFA 5f ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 6
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 6 ")){
			$hcfa_sub_relat=str_replace(' HCFA 6 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 7
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 7a ")){
			$hcfa_resp_add=str_replace(' HCFA 7a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 7b ")){
			$hcfa_resp_city=str_replace(' HCFA 7b ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 7c ")){
			$hcfa_resp_state=str_replace(' HCFA 7c ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 7d ")){
			$hcfa_resp_postcode=str_replace(' HCFA 7d ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 7e ")){
			$hcfa_resp_areacode=str_replace(' HCFA 7e ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 7f ")){
			$hcfa_resp_phone=str_replace(' HCFA 7f ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 8
		/*if(strstr($hcfa_enc_data_arr[$f]," HCFA 8 ")){
			$hcfa_pat_status=str_replace(' HCFA 8 ','',$hcfa_enc_data_arr[$f]);
		}*/
		
		//coloum 9
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 9 ")){
			$hcfa_insured_name=str_replace(' HCFA 9 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA work 9a ")){
			$chk_work_val=str_replace(' HCFA work 9a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA auto 9a ")){
			$chk_auto_val=str_replace(' HCFA auto 9a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 9a ")){
			$hcfa_insured_policy=str_replace(' HCFA 9a ','',$hcfa_enc_data_arr[$f]);
		}
		
		/*if(strstr($hcfa_enc_data_arr[$f]," HCFA 9b ")){
			$hcfa_insured_dob=str_replace(' HCFA 9b ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 9b1 ")){
			$hcfa_insured_sex=str_replace(' HCFA 9b1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 9c ")){
			$hcfa_insured_policy=str_replace(' HCFA 9c ','',$hcfa_enc_data_arr[$f]);
		}*/
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 9d ")){
			$hcfa_other_ins_name=str_replace(' HCFA 9d ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 11
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 11 ")){
			$hcfa_group_no=str_replace(' HCFA 11 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 11a ")){
			$hcfa_resp_dob=str_replace(' HCFA 11a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 11a1 ")){
			$hcfa_resp_sex=str_replace(' HCFA 11a1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 11b ")){
			$hcfa_pat_occup=str_replace(' HCFA 11b ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 11c ")){
			$hcfa_plan_name=str_replace(' HCFA 11c ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 11d ")){
			$hcfa_insured_name=str_replace(' HCFA 11d ','',$hcfa_enc_data_arr[$f]);
		}
		
		
		//coloum 12
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 12b ")){
			$hcfa_cur_date=str_replace(' HCFA 12b ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 14
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 14 ")){
			$hcfa_onset_date=str_replace(' HCFA 14 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 17
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 17 ")){
			$hcfa_ref_prov=str_replace(' HCFA 17 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 17a ")){
			$hcfa_MDCD=str_replace(' HCFA 17a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 17b ")){
			$hcfa_npi=str_replace(' HCFA 17b ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 18
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 18a ")){
			$hcfa_admit_date=str_replace(' HCFA 18a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 18b ")){
			$hcfa_disch_date=str_replace(' HCFA 18b ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 19
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 19 ")){
			$hcfa_notes=str_replace(' HCFA 19 ','',$hcfa_enc_data_arr[$f]);
		}

		//coloum 20
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 20 ")){
			$hcfa_lab=str_replace(' HCFA 20 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 21
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21a ")){
			$hcfa_enc_icd10_point=str_replace(' HCFA 21a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21a1 ")){
			$hcfa_dx1_point=str_replace(' HCFA 21a1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21b1 ")){
			$hcfa_dx2_point=str_replace(' HCFA 21b1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21c1 ")){
			$hcfa_dx3_point=str_replace(' HCFA 21c1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21d1 ")){
			$hcfa_dx4_point=str_replace(' HCFA 21d1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21e1 ")){
			$hcfa_dx5_point=str_replace(' HCFA 21e1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21f1 ")){
			$hcfa_dx6_point=str_replace(' HCFA 21f1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21g1 ")){
			$hcfa_dx7_point=str_replace(' HCFA 21g1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21h1 ")){
			$hcfa_dx8_point=str_replace(' HCFA 21h1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21i1 ")){
			$hcfa_dx9_point=str_replace(' HCFA 21i1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21j1 ")){
			$hcfa_dx10_point=str_replace(' HCFA 21j1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21k1 ")){
			$hcfa_dx11_point=str_replace(' HCFA 21k1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 21l1 ")){
			$hcfa_dx12_point=str_replace(' HCFA 21l1 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 22
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 22a ")){
			$hcfa_clm_control_num_type=str_replace(' HCFA 22a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 22b ")){
			$hcfa_clm_control_num=str_replace(' HCFA 22b ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 23
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 23 ")){
			$hcfa_approval=str_replace(' HCFA 23 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 25
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 25a1 ")){
			$hcfa_federal=str_replace(' HCFA 25a1 ','',$hcfa_enc_data_arr[$f]);
		}
		/*if(strstr($hcfa_enc_data_arr[$f]," HCFA 25a ")){
			$hcfa_federal=str_replace(' HCFA 25a ','',$hcfa_enc_data_arr[$f]);
		}*/
		
		//coloum 26
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 26 ")){
			$hcfa_pat_id=str_replace(' HCFA 26 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 27
		/*if(strstr($hcfa_enc_data_arr[$f]," HCFA 27 ")){
			$hcfa_pat_id=str_replace(' HCFA 27 ','',$hcfa_enc_data_arr[$f]);
		}*/
		
		//coloum 28
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 28a1 ")){
			$hcfa_tot_chrg=str_replace(' HCFA 28a1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 28a2 ")){
			$hcfa_tot_chrg_cent=str_replace(' HCFA 28a2 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 29
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 29a1 ")){
			$hcfa_tot_paid=str_replace(' HCFA 29a1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 29a2 ")){
			$hcfa_paid_cent=str_replace(' HCFA 29a2 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 30
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 30a1 ")){
			$hcfa_tot_bal=str_replace(' HCFA 30a1 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 30a2 ")){
			$hcfa_bal_cent=str_replace(' HCFA 30a2 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 31
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 31_4 ")){
			$hcfa_sc_name=str_replace(' HCFA 31_4 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 31_6 ")){
			$hcfa_cur_date=str_replace(' HCFA 31_6 ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 32
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 32_2 ")){
			$hcfa_posfac_name=str_replace(' HCFA 32_2 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 32_2b ")){
			$hcfa_posfac_ph=str_replace(' HCFA 32_2b ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 32_3 ")){
			$hcfa_posfac_add=str_replace(' HCFA 32_3 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 32_4 ")){
			$hcfa_posfac_city=str_replace(' HCFA 32_4 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 32a ")){
			$hcfa_posfac_npi=str_replace(' HCFA 32a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 32b ")){
			$hcfa_posfac_tax=str_replace(' HCFA 32b ','',$hcfa_enc_data_arr[$f]);
		}
		
		//coloum 33
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 33_1a ")){
			$hcfa_fac_phcode=str_replace(' HCFA 33_1a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 33_1b ")){
			$hcfa_fac_ph=str_replace(' HCFA 33_1b ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 33_2 ")){
			$hcfa_fac_group=str_replace(' HCFA 33_2 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 33_3 ")){
			$hcfa_fac_street=str_replace(' HCFA 33_3 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 33_4 ")){
			$hcfa_fac_city=str_replace(' HCFA 33_4 ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 33a ")){
			$hcfa_fac_npi=str_replace(' HCFA 33a ','',$hcfa_enc_data_arr[$f]);
		}
		if(strstr($hcfa_enc_data_arr[$f]," HCFA 33b ")){
			$hcfa_ins_prac_id=str_replace(' HCFA 33b ','',$hcfa_enc_data_arr[$f]);
		}
	}

	if($hcfa_pat_sex == "Male" || $hcfa_resp_sex == "Male"){
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
		//--- FILL HCFA FORM FIELDS --------
		
		
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
		if($insName == "MEDICARE" || $insHouseCode == "MEDICARE" || in_array($objInsuranceCoData->Payer_id_pro,$arr_RRM_payers) || in_array($objInsuranceCoData->Payer_id,$arr_RRM_payers)){
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
			if($insName == "Work comp" || $insHouseCode == "Work comp"){
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
		if($relationship == "self"){
			$sRx = 1;
		}
		elseif($relationship == "Spouse"){
			$sRx = 14;
		}
		elseif($relationship == "Father" || $relationship == "Mother"){
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
		
		$d=0;
		for($l=0;$l<6;$l++){
			$hcfa_start_time="";
			for($k=0;$k<count($hcfa_enc_data_arr);$k++){
				if(strstr($hcfa_enc_data_arr[$k]," HCFA 24")){
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a1-1".$l." ")){
						$hcfa_start_time=str_replace(" HCFA 24a1-1".$l." ",'',$hcfa_enc_data_arr[$k]);
					}	
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a2-1".$l." ")){
						$hcfa_end_time=str_replace(" HCFA 24a2-1".$l." ",'',$hcfa_enc_data_arr[$k]);
					}	
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d-1".$l." ")){
						$hcfa_acc_anes_unit=str_replace(" HCFA 24d-1".$l." ",'',$hcfa_enc_data_arr[$k]);
					}	
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24f-1".$l." ")){
						$hcfa_admin_cpt_units=str_replace(" HCFA 24f-1".$l." ",'',$hcfa_enc_data_arr[$k]);
					}	
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24g-1".$l." ")){
						$hcfa_anes_total_units=str_replace(" HCFA 24g-1".$l." ",'',$hcfa_enc_data_arr[$k]);
					}	
					
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a1-1".$l." ")){
						$pdf->Cell($left_marg);
						$pdf->Ln(3.4);
					}
					
					if(strstr("N4",$hcfa_start_time)!=""){
						if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a1-1".$l." ")){
							$pdf->Cell(-2);
							$pdf->Cell(6,10,$hcfa_start_time,0,0,'');
						}
						if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a2-1".$l." ")){
							$ndc_space_cont=15+strlen($hcfa_end_time);
							$pdf->Cell($ndc_space_cont,10,$hcfa_end_time,0,0,'');
						}
						if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d-1".$l." ")){
							$pdf->Cell(28,10,$hcfa_acc_anes_unit,0,0,'');
						}
						if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d-1".$l." ")){
							if(strstr($hcfa_enc_data_arr[$k+2]," HCFA 24j-1".$l." ")){
								$pdf->Cell(102,10,'',0,0,'');
								$hcfa_med_id_zz=str_replace(" HCFA 24i-1".$l." ",'',$hcfa_enc_data_arr[$k+1]);
								$pdf->Cell(10,10,$hcfa_med_id_zz,0,0,'');
								$hcfa_med_id=str_replace(" HCFA 24j-1".$l." ",'',$hcfa_enc_data_arr[$k+2]);
								$pdf->Cell(23,10,$hcfa_med_id,0,0,'');
							}
							$pdf->Ln(5);
						}
					}else{
						if($hcfa_start_time!=""){
							if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a1-1".$l." ")){
								$pdf->Cell(13,10,'START',0,0,'');
								$pdf->Cell(11,10,$hcfa_start_time,0,0,'');
							}
							if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a2-1".$l." ")){
								$pdf->Cell(9,10,'END',0,0,'');
								$pdf->Cell(11,10,$hcfa_end_time,0,0,'');
								$pdf->Cell(10,10,'TIME',0,0,'');
							}	
							if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d-1".$l." ")){
								$pdf->Cell(13,10,'UNITS',0,0,'');
								$pdf->Cell(11,10,$hcfa_acc_anes_unit,0,0,'');
								$pdf->Cell(23,10,'MOD  UNITS',0,0,'');
								$pdf->Cell(8,10,'0.0',0,0,'');
							}
							if(strstr($hcfa_enc_data_arr[$k]," HCFA 24f-1".$l." ")){
								$pdf->Cell(13,10,'RVS = ',0,0,'');
								$pdf->Cell(8,10,$hcfa_admin_cpt_units,0,0,'');
							}
							if(strstr($hcfa_enc_data_arr[$k]," HCFA 24g-1".$l." ")){
								$pdf->Cell(17,10,'TOTAL = ',0,0,'');
								$pdf->Cell(14,10,$hcfa_anes_total_units,0,0,'');
								if(strstr($hcfa_enc_data_arr[$k+2]," HCFA 24j-1".$l." ")){
									$hcfa_med_id_zz=str_replace(" HCFA 24i-1".$l." ",'',$hcfa_enc_data_arr[$k+1]);
									$pdf->Cell(10,10,$hcfa_med_id_zz,0,0,'');
									$hcfa_med_id=str_replace(" HCFA 24j-1".$l." ",'',$hcfa_enc_data_arr[$k+2]);
									$pdf->Cell(23,10,$hcfa_med_id,0,0,'');
									$pdf->Ln(5);
								}else{
									$pdf->Ln(5);
								}
							}
						}
					}
							
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a1_".$l." ")){
						
						if($hcfa_start_time==""){
							$pdf->Ln(3);
							$pdf->Cell(160);
							if(strstr($hcfa_enc_data_arr[$k-1]," HCFA 24j ")){
								if(strstr($hcfa_enc_data_arr[$k-2]," HCFA 24i ")){
									$pdf->Cell(10,12,str_replace(" HCFA 24i ",'',$hcfa_enc_data_arr[$k-2]),0,0,'');
								}else{
									$pdf->Cell(10,12,'',0,0,'');
								}
								$pdf->Cell(23,12,str_replace(" HCFA 24j ",'',$hcfa_enc_data_arr[$k-1]),0,0,'');
							}
							$pdf->Ln(5.3);
						}
						
						$d++;
						$pdf->Cell(-2.5);
						$hcfa_dos=str_replace(" HCFA 24a1_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(23,10,$hcfa_dos,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24a2_".$l." ")){
						$hcfa_dos2=str_replace(" HCFA 24a2_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(20,10,$hcfa_dos2,0,0,'');
					}
					
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24b_".$l." ")){
						$pdf->SetFont('Courier','',10);
						$hcfa_pos=str_replace(" HCFA 24b_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(20,10,$hcfa_pos,0,0,'');
					}
					
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d1_".$l." ")){
						$pdf->SetFont('Courier','',10);
						$hcfa_cpt=str_replace(" HCFA 24d1_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(17,10,$hcfa_cpt,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d2_".$l." ")){
						$hcfa_mod1=str_replace(" HCFA 24d2_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(8,10,$hcfa_mod1,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d3_".$l." ")){
						$hcfa_mod2=str_replace(" HCFA 24d3_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(8,10,$hcfa_mod2,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d4_".$l." ")){
						$hcfa_mod3=str_replace(" HCFA 24d4_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(8,10,$hcfa_mod3,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24d5_".$l." ")){
						$hcfa_mod4=str_replace(" HCFA 24d5_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(7,10,$hcfa_mod4,0,0,'');
					}
					
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24e_".$l." ")){
						$chrg_space = 13;
						$chrg_cent_space = 15;
						$hcfa_chrg_cond=str_replace(" HCFA 24f_".$l." ",'',$hcfa_enc_data_arr[$k+1]);
						if(strlen($hcfa_chrg_cond)<7){
							$chrg_space = $chrg_space+((7-strlen($hcfa_chrg_cond))*2);
							$chrg_cent_space=$chrg_cent_space-((7-strlen($hcfa_chrg_cond))*2);
						}
						$hcfa_dig=str_replace(" HCFA 24e_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell($chrg_space,10,$hcfa_dig,0,0,'');
					}
					
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24f_".$l." ")){
						$hcfa_chrg=str_replace(" HCFA 24f_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell($chrg_cent_space,10,$hcfa_chrg,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24f2_".$l." ")){
						$hcfa_chrg_cent=str_replace(" HCFA 24f2_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(8,10,$hcfa_chrg_cent,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24g_".$l." ")){
						$hcfa_unit=str_replace(" HCFA 24g_".$l." ",'',unit_format($hcfa_enc_data_arr[$k]));
						$pdf->Cell(25,10,$hcfa_unit,0,0,'');
					}
					if(strstr($hcfa_enc_data_arr[$k]," HCFA 24j_".$l." ")){
						$hcfa_rend=str_replace(" HCFA 24j_".$l." ",'',$hcfa_enc_data_arr[$k]);
						$pdf->Cell(33,10,$hcfa_rend,0,0,'');
					}
				}
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
		$pdf->Cell(30,10,$hcfa_sc_name,0,0,'');
		$pdf->Cell(23,18,$hcfa_cur_date,0,0,'');
		$pdf->Cell(68,12,$hcfa_posfac_city,0,0,'');
		$pdf->Cell(63,12,$hcfa_fac_city,0,0,'');
		$pdf->Ln(5);
		$pdf->Cell(58,12,'',0,0,'');
		$pdf->Cell(28,12,$hcfa_posfac_npi,0,0,'');
		$pdf->Cell(38,12,$hcfa_posfac_tax,0,0,'');
		$pdf->Cell(28,12,$hcfa_fac_npi,0,0,'');
		$pdf->Cell(76,12,$hcfa_ins_prac_id,0,0,'');
		
	}
}
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
