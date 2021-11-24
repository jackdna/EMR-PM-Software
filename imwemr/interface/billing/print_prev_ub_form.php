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
$sel_ub=implode(',',$selectpatient);
$time_hcfa=time();
$print_paper_type=$PrintCms_white_chk;
$newfile_ub_path=write_html('','ub_form.pdf');
//------------------------ HCFA Margin Detail ------------------------//
$group_margin_qry=imw_query("select top_margin,left_margin,top_line_margin from create_margins where margin_type='UB04'");
$group_margin=imw_fetch_array($group_margin_qry);
//------------------------ HCFA Margin Detail ------------------------//
$sel_hcfa_data=imw_query("select * from previous_ub where previous_ub_id in($sel_ub) order by previous_ub_id desc");
while($fet_rec=imw_fetch_array($sel_hcfa_data)){
	$ub_enc_data=$fet_rec['ub_data'];
	if($ub_enc_data){
		$fpdiCheck=true;
		$prev_ub_arr=array();
		$prev_ub_arr=unserialize(html_entity_decode($ub_enc_data));
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
		//BOX NO. 1,2,3a
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_1_1']);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(0.1);
		$pdf->Cell(62.9,5,$prev_ub_arr['ub_1a'],0,0,'');
		$pdf->Cell(68,5,$prev_ub_arr['ub_2a'],0,0,'');
		$pdf->Cell(0,5,$prev_ub_arr['ub_3a'],8,0,'');
		//BOX NO. 1,2,3b,4
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_1_2']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(63,5,$prev_ub_arr['ub_1b'],0,0,'');
		$pdf->Cell(68,5,$prev_ub_arr['ub_2b'],0,0,'');
		$pdf->Cell(60,5,$prev_ub_arr['ub_3b'],0,0,'');
		$pdf->Cell(0,5,$prev_ub_arr['ub_4'],0,0,'');
		
		//BOX NO. 1,2
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_1_3']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(63,5,$prev_ub_arr['ub_1c'],0,0,'');
		$pdf->Cell(0,5,$prev_ub_arr['ub_2c'],0,0,'');
		
		//BOX NO. 1,2,5,6
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_1_4']);
		}else{
			$pdf->Ln(5);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(63,5,$prev_ub_arr['ub_1d'],0,0,'');
		$pdf->Cell(61,5,$prev_ub_arr['ub_2d'],0,0,'');
		$pdf->Cell(24,5,$prev_ub_arr['ub_5'],0,0,'');
		$pdf->Cell(17,5,$prev_ub_arr['ub_6a'],0,0,'');
		$pdf->Cell(0,5,$prev_ub_arr['ub_6b'],0,0,'');
					
		//BOX NO. 8a,9a
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_8_1']);
		}else{
			$pdf->Ln(5);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(103,5,'',0,0,'');
		$pdf->Cell(130,5,$prev_ub_arr['ub_9a'],0,0,'');
		$pat_before_margin=5+$wo_page_margion5;
					
		//BOX NO. 8b,9b,9c,9d,9e
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_8_2']);
		}else{
			$pdf->Ln($pat_before_margin);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(2,2,'',0,0,'');
		$pdf->Cell(73,2,$prev_ub_arr['ub_8b'],0,0,'');
		$pdf->Cell(83,2,$prev_ub_arr['ub_9b'],0,0,'');
		$pdf->Cell(10,2,$prev_ub_arr['ub_9c'],0,0,'');
		$pdf->Cell(30,2,$prev_ub_arr['ub_9d'],0,0,'');
		
		//BOX NO. 10 to 30
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_10_1']);
		}else{
			$pdf->Ln(9);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(22,1,$prev_ub_arr['ub_10'],0,0,'');
		$pdf->Cell(6,1,$prev_ub_arr['ub_11'],0,0,'');
		$pdf->Cell(14.2,1,$prev_ub_arr['ub_12'],0,0,'');
		$pdf->Cell(10,1,$prev_ub_arr['ub_13'],0,0,'');
		$pdf->Cell(7,1,$prev_ub_arr['ub_14'],0,0,'');
		$pdf->Cell(5.1,1,$prev_ub_arr['ub_15'],0,0,'');
		$pdf->Cell(9,1,$prev_ub_arr['ub_16'],0,0,'');
		$pdf->Cell(30,1,$prev_ub_arr['ub_17'],0,0,'');
		
		//BOX NO. 38 to 41
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_38_1']);
		}else{
			$pdf->Ln(22);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(2,1,'',0,0,'');
		$pdf->Cell(105,1,$prev_ub_arr['ub_38a'],0,0,'');
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
		$pdf->Cell(105,1,$prev_ub_arr['ub_38b'],0,0,'');
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
		$pdf->Cell(105,1,$prev_ub_arr['ub_38c'],0,0,'');
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
		//BOX NO. 42 to 49
		for($top_name_var=1;$top_name_var<23;$top_name_var++){
			if($top_name_var<22){
				if($print_paper_type=='WithoutPrintub'){
					$pdf->Ln($top_line_margin_arr['top_42_'.$top_name_var]);
				}else{
					$pdf->Ln(4.4);
				}
			}
			if($prev_ub_arr['ub_44a'.$top_name_var]!=""){
				$pdf->Cell($left_margin);
				$pdf->Cell(10,1,$prev_ub_arr['ub_42a'.$top_name_var],0,0,'');
				$pdf->Cell(65,1,$prev_ub_arr['ub_43a'.$top_name_var],0,0,'');
				$pdf->Cell(38,1,$prev_ub_arr['ub_44a'.$top_name_var],0,0,'');
				$pdf->Cell(18,1,$prev_ub_arr['ub_45a'.$top_name_var],0,0,'');
				$pdf->Cell(20,1,$prev_ub_arr['ub_46a'.$top_name_var],0,0,'');
				$pdf->Cell(15,1,$prev_ub_arr['ub_47a'.$top_name_var],0,0,'');
				$pdf->Cell(25,1,$prev_ub_arr['ub_47b'.$top_name_var],0,0,'');
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
		$pdf->Cell(22,1,$prev_ub_arr['ub_42a23'],0,0,'');
		$pdf->Cell(15,1,$prev_ub_arr['ub_43a23'],0,0,'');
		$pdf->Cell(73,1,$prev_ub_arr['ub_42b23'],0,0,'');
		$pdf->Cell(36,1,$prev_ub_arr['ub_45a23'],0,0,'');
		$pdf->Cell(19,1,$prev_ub_arr['ub_47a23'],0,0,'');
		$pdf->Cell(15,1,$prev_ub_arr['ub_47b23'],0,0,'');
		$pdf->Cell(15,1,'0',0,0,'');
					
		//BOX NO. 56
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_56_1']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(170,1,'',0,0,'');
		$pdf->Cell(30,2,$prev_ub_arr['ub_56'],0,0,'');
		$after_ins_len=6+$wo_page_margion2;
		
		//BOX NO. 50a to 57a
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_50_1']);
		}else{
			$pdf->Ln($after_ins_len);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(60,1,$prev_ub_arr['ub_50a'],0,0,'');
		$pdf->Cell(34,1,$prev_ub_arr['ub_51a'],0,0,'');
		$pdf->Cell(8,-1,$prev_ub_arr['ub_52a'],0,0,'');
		$prev_ub_arr['ub_52a']=$pri_y;
		$pdf->Cell(2,-1,$prev_ub_arr['ub_53a'],0,0,'');
		$pdf->Cell(21,-1,$prev_ub_arr['ub_54a1'],0,0,'');
		$pdf->Cell(44,-1,$prev_ub_arr['ub_54a2'],0,0,'');
		$pdf->Cell(10,-1,$prev_ub_arr['ub_57a'],0,0,'');
		
		//BOX NO. 50b to 57b
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_50_2']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(60,1,$prev_ub_arr['ub_50b'],0,0,'');
		$pdf->Cell(34,1,$prev_ub_arr['ub_51b'],0,0,'');
		$pdf->Cell(8,1,$prev_ub_arr['ub_52b'],0,0,'');
		$pdf->Cell(29,1,$prev_ub_arr['ub_53b'],0,0,'');
		$pdf->Cell(21,-1,$prev_ub_arr['ub_54b1'],0,0,'');
		$pdf->Cell(44,-1,$prev_ub_arr['ub_54b2'],0,0,'');
	
		//BOX NO. 50c to 57c
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_50_3']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(60,1,$prev_ub_arr['ub_50c'],0,0,'');
		$pdf->Cell(34,1,$prev_ub_arr['ub_51c'],0,0,'');
		$pdf->Cell(8,1,$prev_ub_arr['ub_52c'],0,0,'');
		$pdf->Cell(8,1,$prev_ub_arr['ub_53c'],0,0,'');
		
		//BOX NO. 58a to 62a
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_58_1']);
		}else{
			$pdf->Ln(9);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(65,1,$prev_ub_arr['ub_58a'],0,0,'');
		$pdf->Cell(8,1,$prev_ub_arr['ub_59a'],0,0,'');
		$pdf->Cell(50,1,$prev_ub_arr['ub_60a'],0,0,'');
		$pdf->Cell(40,1,$prev_ub_arr['ub_61a'],0,0,'');
		$pdf->Cell(30,1,$prev_ub_arr['ub_62a'],0,0,'');
		
		//BOX NO. 58b to 62b
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_58_2']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(65,1,$prev_ub_arr['ub_58b'],0,0,'');
		$pdf->Cell(8,1,$prev_ub_arr['ub_59b'],0,0,'');
		$pdf->Cell(50,1,$prev_ub_arr['ub_60b'],0,0,'');
		$pdf->Cell(40,1,$prev_ub_arr['ub_61b'],0,0,'');
		$pdf->Cell(30,1,$prev_ub_arr['ub_62b'],0,0,'');
					
		//BOX NO. 58c to 62c
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_58_3']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(65,1,$prev_ub_arr['ub_58c'],0,0,'');
		$pdf->Cell(8,1,$prev_ub_arr['ub_59c'],0,0,'');
		$pdf->Cell(50,1,$prev_ub_arr['ub_60c'],0,0,'');
		$pdf->Cell(40,1,$prev_ub_arr['ub_61c'],0,0,'');
		$pdf->Cell(30,1,$prev_ub_arr['ub_62c'],0,0,'');
		
		//BOX NO. 63a
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_63_1']);
		}else{
			$pdf->Ln(10);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(80,1,$prev_ub_arr['ub_63a'],0,0,'');
		$pdf->Cell(65,1,$prev_ub_arr['ub_64a'],0,0,'');
					
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
					
		$pdf->Cell(20,1,$prev_ub_arr['ub_67'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67a'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67b'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67c'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67d'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67e'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67f'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67g'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67h'],0,0,'');
					
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
		$pdf->Cell(3,1,$prev_ub_arr['ub_66'],0,0,'');
		$pdf->Cell(2,1,'',0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67i'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67j'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67k'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67l'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67m'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67n'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67o'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67p'],0,0,'');
		$pdf->Cell(20,1,$prev_ub_arr['ub_67q'],0,0,'');
		
		//BOX NO.  76
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_74_1']);
		}else{
			$pdf->Ln(10);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(143,1,'',0,0,'');
		$pdf->Cell(38,1,$prev_ub_arr['ub_76a'],0,0,'');
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
		$pdf->Cell(45,1,$prev_ub_arr['ub_76d'],0,0,'');
		$pdf->Cell(30,1,$prev_ub_arr['ub_76e'],0,0,'');
					
		//BOX NO. 77
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_74_3']);
		}else{
			$pdf->Ln(5);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(143,1,'',0,0,'');
		$pdf->Cell(30.5,1,$prev_ub_arr['ub_77a'],0,0,'');
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
		$pdf->Cell(45,1,$prev_ub_arr['ub_77d'],0,0,'');
		$pdf->Cell(30,1,$prev_ub_arr['ub_77e'],0,0,'');
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
		$pdf->Cell(69,1,$prev_ub_arr['ub_80a'],0,0,'');
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
		$pdf->Cell(143,1,$prev_ub_arr['ub_80b'],0,0,'');
		$pdf->Cell(38,1,$prev_ub_arr['ub_79a'],0,0,'');
		$pdf->Cell(30,1,'',0,0,'');
		
		//BOX NO. 80 to 79
		if($print_paper_type=='WithoutPrintub'){
			$pdf->Ln($top_line_margin_arr['top_80_4']);
		}else{
			$pdf->Ln(4);
		}
		$pdf->Cell($left_margin);
		$pdf->Cell(131,1,$prev_ub_arr['ub_80c'],0,0,'');
		$pdf->Cell(45,1,$prev_ub_arr['ub_79d'],0,0,'');
		$pdf->Cell(30,1,$prev_ub_arr['ub_79e'],0,0,'');
	}
}
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
