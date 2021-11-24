<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 

include_once("common/conDb.php");
define('FPDF_FONTPATH','font/');
use setasign\Fpdi\Tcpdf\Fpdi;
$pdf= new Fpdi();
$pdf->SetLeftMargin(6.9);
$pdf->SetTopMargin(-0.3);
$pdf->setCellHeightRatio(0);
$pdf->SetCellPadding(0);

$fpdiCheck=false;
$averyLargeLabel = ( defined('AVERY_LARGE_LABEL') && constant('AVERY_LARGE_LABEL') == 'YES') ? true : false;
$loginUser = $_SESSION['loginUserId'];
$date=$_REQUEST['date12'];
$showAllApptStatus=$_REQUEST['showAllApptStatus'];
$siteArr = array("left"=>"(OS)", "right"=>"(OD)", "both"=>"(OU)");
$genderArr = array("m"=>"(Male)", "f"=>"(Female)");
$selected_date= $date;
$showDate = $date;
if($date!="") {
	$dat1=explode("-",$date);
	$dat1[0];
	$dat1[1];
	$dat1[2];
	$selected_date=$dat1[2].'-'.$dat1[0].'-'.$dat1[1];
}
$label=$_REQUEST['label'];
$range=$_REQUEST['range'];
if($_SESSION['iasc_facility_id'])
{
	$fac_con=" AND stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]' "; 
}

$qry = "SELECT small_label_enable_surgeon, small_label_enable_procedure, small_label_enable_patient_mrn, small_label_enable_patient_gender, small_label_enable_patient_dos,
				small_label_enable_patient_dob,small_label_enable_site, large_label_enable_surgeon, large_label_enable_procedure, large_label_enable_patient_mrn,
				large_label_enable_patient_gender, large_label_enable_patient_dos ,large_label_enable_patient_dob ,large_label_enable_site
		FROM surgerycenter WHERE surgeryCenterId = '1' LIMIT 0,1";
$res = imw_query($qry)or $msg_info[] = $qry.imw_error();
if(imw_num_rows($res)>0) {
	$row = imw_fetch_assoc($res);
	$small_label_enable_surgeon 		= trim($row["small_label_enable_surgeon"]);
	$small_label_enable_procedure 		= trim($row["small_label_enable_procedure"]);
	$small_label_enable_patient_mrn 	= trim($row["small_label_enable_patient_mrn"]);
	$small_label_enable_patient_gender	= trim($row["small_label_enable_patient_gender"]);
	$small_label_enable_patient_dos 	= trim($row["small_label_enable_patient_dos"]);
	$small_label_enable_patient_dob 	= trim($row["small_label_enable_patient_dob"]);
	$small_label_enable_site 			= trim($row["small_label_enable_site"]);
	$large_label_enable_surgeon 		= trim($row["large_label_enable_surgeon"]);
	$large_label_enable_procedure 		= trim($row["large_label_enable_procedure"]);
	$large_label_enable_patient_mrn		= trim($row["large_label_enable_patient_mrn"]);
	$large_label_enable_patient_gender 	= trim($row["large_label_enable_patient_gender"]);
	$large_label_enable_patient_dos 	= trim($row["large_label_enable_patient_dos"]);
	$large_label_enable_patient_dob 	= trim($row["large_label_enable_patient_dob"]);
	$large_label_enable_site 			= trim($row["large_label_enable_site"]);
}
$ptQry = "SELECT patient_id, imwPatientId FROM patient_data_tbl ORDER BY patient_id";
$ptRes = imw_query($ptQry)or $msg_info[] = $ptQry.imw_error();
if(imw_num_rows($ptRes)>0) {
	while($ptRow = imw_fetch_assoc($ptRes)) {
		$patientMrnArr['patient_id'][$ptRow["imwPatientId"]] = $ptRow["patient_id"];
		$patientMrnArr['imwPatientId'][$ptRow["patient_id"]] = $ptRow["imwPatientId"];
	}
}
$sqlStr1 = "SELECT * FROM label_size WHERE l_id = '1'";
$sqlQry1 = imw_query($sqlStr1);
$sqlRows1 = @imw_fetch_array($sqlQry1);
$large_top= (float)$sqlRows1['large_top'];
$large_bottom=(float) $sqlRows1['large_bottom'];
$large_inner=(float) $sqlRows1['large_inner'];
$small_top=(float) $sqlRows1['small_top'];
$small_bottom=(float) $sqlRows1['small_bottom'];
$small_inner=(float) ($sqlRows1['small_inner']);

// GETTING LOGIN USER FIRST NAME, MIDDLE NAME, LAST NAME, USERTYPE, PRACTICENAME.
	$userTypeQry = "SELECT fname, mname, lname, 
			user_type,coordinator_type, practiceName FROM users
			WHERE usersId = '$loginUser'";
	$userTypeRes = imw_query($userTypeQry);
	$userTypeRows = imw_fetch_array($userTypeRes);
	$surgeonLoggedFirstName = trim(stripslashes($userTypeRows['fname']));
	$surgeonLoggedMiddleName = trim(stripslashes($userTypeRows['mname']));
	$surgeonLoggedLastName = trim(stripslashes($userTypeRows['lname']));
	$userType = $userTypeRows['user_type'];
	$coordinatorType = $userTypeRows['coordinator_type'];
	$practiceName = stripslashes($userTypeRows['practiceName']);
	$user_type = $userTypeRows['user_type'];
// GETTING LOGIN USER FIRST NAME, MIDDLE NAME, LAST NAME, USERTYPE, PRACTICENAME.

$andCancelledQry = " AND  stub_tbl.patient_status!='Canceled' ";	
if($userType=='Coordinator' && $coordinatorType!='Master') { //IF USER TYPE IS Coordinator AND HE IS NOT MASTER THEN SHOW RECORD RELATED TO HIS PRACTICENAME
	$stub_tbl_group_query = "select stub_tbl.*, DATE_FORMAT(stub_tbl.patient_dob,'%m/%d/%Y') as patient_dob_format 
							FROM stub_tbl,users  
							WHERE stub_tbl.dos = '".$selected_date."' 
							AND users.practiceName='".addslashes($practiceName)."' 
							AND users.practiceName!='' 
							AND users.fname=stub_tbl.surgeon_fname 
							AND users.lname=stub_tbl.surgeon_lname 
							".$andCancelledQry.$fac_con."
							ORDER BY stub_tbl.surgeon_fname, stub_tbl.surgery_time"; 		
}elseif($userType=='Surgeon') {
	$stub_tbl_group_query = "select *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where stub_tbl.dos = '".$selected_date."' ".$andCancelledQry." AND stub_tbl.surgeon_fname ='".addslashes($surgeonLoggedFirstName)."' AND stub_tbl.surgeon_lname = '".addslashes($surgeonLoggedLastName)."' ".$fac_con." ORDER BY stub_tbl.surgeon_fname, stub_tbl.surgery_time"; 		
}else {
	$stub_tbl_group_query = "select *, DATE_FORMAT(patient_dob,'%m/%d/%Y') as patient_dob_format from stub_tbl where stub_tbl.dos = '".$selected_date."' ".$andCancelledQry.$fac_con." ORDER BY stub_tbl.surgeon_fname, stub_tbl.surgery_time"; 		
}
$stub_tbl_group_res = imw_query($stub_tbl_group_query) or die(imw_error());
$stub_tbl_group_NumRow = imw_num_rows($stub_tbl_group_res);
$t=0;
$stub_tbl_group_row_arr = array();
if($stub_tbl_group_NumRow>0){
$stub_tbl_groupTemp = "";
$stub_tbl_pattemp = "";
$stub_dos="";
$stub_dob="";
$stub_procedure="";
$stub_tbl_site="";
if($label) {
	$fpdiCheck=true;
}

$dymoOptions = array();
$is_dymo = (isset($_REQUEST['sel_dymo']) && $_REQUEST['sel_dymo']=='1')?true:false;
$selected_dymo_printer = trim($_REQUEST['dymoPrintersList']);

	$i=0;
	while($stub_tbl_group_row = imw_fetch_assoc($stub_tbl_group_res)) {
		array_map('stripslashes',$stub_tbl_group_row);
		$stub_tbl_group_row['patient_first_name'] 	= ucwords($stub_tbl_group_row['patient_first_name']);
		$stub_tbl_group_row['patient_middle_name'] 	= ucwords($stub_tbl_group_row['patient_middle_name']);
		$stub_tbl_group_row['patient_last_name'] 	= ucwords($stub_tbl_group_row['patient_last_name']);
		
		
		$tempDymo = array();
		/*Data for Dymo Label*/
		if($is_dymo===true && (in_array($stub_tbl_group_row['stub_id'],$pt_stub_id_list) || $pt_stub_id_list == 'all') )
		{
			
			/*DOS*/
			list($dos_y,$dos_m,$dos_d)=explode('-',$stub_tbl_group_row['dos']);
			$stub_dos=$dos_m.'/'.$dos_d.'/'.$dos_y;
			$tempDymo['dos'] = $stub_dos;
			/*END DOS*/
			
			/*Surgeon Name*/
			$tempDymo['surgeon'] = $stub_tbl_group_row['surgeon_fname'].' '.$stub_tbl_group_row['surgeon_lname'];
			
			/*Patient Name*/
			$maindataArr_sub = $stub_tbl_group_row['patient_last_name'].', '.$stub_tbl_group_row['patient_first_name'];
			if(trim($stub_tbl_group_row['patient_middle_name']))
				$maindataArr_sub = $maindataArr_sub." ".trim($stub_tbl_group_row['patient_middle_name']);
			
			$tempDymo['pt'] = $maindataArr_sub;
			/*End Patient Name*/
			
			/*DOB*/
			list($dob_y,$dob_m,$dob_d) = explode('-',$stub_tbl_group_row['patient_dob']);
			$stub_dob = $dob_m.'/'.$dob_d.'/'.$dob_y;
			$tempDymo['dob'] = $stub_dob;
			/*End DOB*/
			
			/*Procedure*/
			if($stub_tbl_group_row['site']=='left')
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'].'(OS)';
			elseif($stub_tbl_group_row['site']=='right')
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'].'(OD)';
			elseif($stub_tbl_group_row['site']=='both')
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'].'(OU)';
			else
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'];

			if($stub_tbl_group_row['site']=='left')
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'].'(OS)';
			elseif($stub_tbl_group_row['site']=='right')
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'].'(OD)';
			elseif($stub_tbl_group_row['site']=='both')
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'].'(OU)';
			else
				$stub_procedure = $stub_tbl_group_row['patient_primary_procedure'];
			
			$tempDymo['proc'] = $stub_procedure;
			/*End Procedure*/
			
			if($label=='large'){
				$tempDymo['dos']	= 'DOS&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$tempDymo['dos'];
				$tempDymo['surgeon']= 'Surgeon&nbsp;: '.$tempDymo['surgeon'];
				$tempDymo['pt']		= 'Pt Name: '.$tempDymo['pt'];
				$tempDymo['dob']	= 'DOB&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$tempDymo['dob'];
				$tempDymo['proc']	= 'Proc&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: '.$tempDymo['proc'];
			}
			array_push($dymoOptions, $tempDymo);
		}
		/*End Data for Dymo Label*/
		
		$stub_tbl_group_row_arr[] = $stub_tbl_group_row;
		$t++;
		if( (is_array($pt_stub_id_list) && in_array($stub_tbl_group_row['stub_id'],$pt_stub_id_list)) || $pt_stub_id_list == 'all')
		{
			for($r=0;$r<$range;$r++){
				foreach($stub_tbl_group_row as $key => $val){
					$maindataArr[$i][$key] = $val;
				}
				$i++;
			}
		}
	}
	if($label=='large'){

		$cell_height = 2;
		$no_of_labels = $averyLargeLabel ? 10 : 5;
		$cell_height3 = $averyLargeLabel ? 2 : 3;
		$cell_height4 = $averyLargeLabel ? 2 : 4;
		$font_size = $averyLargeLabel ? 9 : 12;

		$loopEnd = ceil(count($maindataArr)/2);
		$p=0;
		$j=0;
		//print '<pre>';
		//print_r($maindataArr);
		for($i=0;$i<$loopEnd;$i++,$j++){
			if($j==0 || $j >= $no_of_labels){
				$pagecount = $pdf->setSourceFile("library/predefined_forms/label_form_wo.pdf");
				$tplidx = $pdf->importPage(1);
				$pdf->SetAutoPageBreak(TRUE, 0);
				$pdf->addPage();
				$pdf->useTemplate($tplidx);
					
				$j=0;
				$pdf->Ln($large_top);
			}
			$pdf->SetFont('helvetica','',$font_size);
			$p ++;
			if($large_label_enable_patient_dos == "Y") { //BASED ON CONFIGURATION IN ADMIN->SETTING
				$pdf->Cell(19,$cell_height3,'DOS:',0,0,'');
				
				list($dos_y,$dos_m,$dos_d)=explode('-',$maindataArr[$p-1]['dos']);
				$stub_dos=$dos_m.'/'.$dos_d.'/'.$dos_y;
				$pdf->Cell(85,$cell_height3,$stub_dos,0,0,'');
				
				if($maindataArr[$p]['dos']<>""){
					$pdf->Cell(19,$cell_height3,'DOS:',0,0,'');
					list($dos_y1,$dos_m1,$dos_d1)=explode('-',$maindataArr[$p]['dos']);
					$stub_dos1=$dos_m1.'/'.$dos_d1.'/'.$dos_y1;
					$pdf->Cell(41,$cell_height3,$stub_dos1,0,0,'');
				}
				
				$pdf->Ln($large_inner);
			}
			if($large_label_enable_surgeon == "Y") { //BASED ON CONFIGURATION IN ADMIN->SETTING
				$pdf->Cell(19,$cell_height,'Surgeon:',0,0,'');
				$pdf->Cell(85,$cell_height,$maindataArr[$p-1]['surgeon_fname'].' '.$maindataArr[$p-1]['surgeon_lname'],0,0,'');
				if($maindataArr[$p]['surgeon_fname']){
					$pdf->Cell(19,$cell_height,'Surgeon:',0,0,'');
					$pdf->Cell(41,$cell_height,$maindataArr[$p]['surgeon_fname'].' '.$maindataArr[$p]['surgeon_lname'],0,0,'');
				}
			}
			
			$maindataArr_sub=$maindataArr[$p-1]['patient_last_name'].', '.$maindataArr[$p-1]['patient_first_name'];
			if(trim($maindataArr[$p-1]['patient_middle_name'])) {
				$maindataArr_sub = $maindataArr_sub." ".trim($maindataArr[$p-1]['patient_middle_name']);
			}
			//$maindataArr_sub="";
			$lenOfName=strlen($maindataArr_sub);
			//$maindataArr_sub1=$maindataArr_sub;
			
			if($lenOfName>21){
				$maindataArr_sub1 = substr($maindataArr_sub, 0, 21).'..';
			}else{
				$maindataArr_sub1=$maindataArr_sub;
			}
			
			$maindataArr_sub_p=$maindataArr[$p]['patient_last_name'].', '.$maindataArr[$p]['patient_first_name'];
			if(trim($maindataArr[$p]['patient_middle_name'])) {
				$maindataArr_sub_p = $maindataArr_sub_p." ".trim($maindataArr[$p]['patient_middle_name']);
			}
			//$maindataArr_sub_p="";
			$lenOfName_p=strlen($maindataArr_sub_p);
			if($lenOfName_p>21){
				$maindataArr_sub_p1 = substr($maindataArr_sub_p, 0, 21).'..';
			}else{
				$maindataArr_sub_p1=$maindataArr_sub_p;
			}
			
			$ptmrn = $ptmrn1 = $ptmrn2 = "";
			if($large_label_enable_patient_mrn == "Y") {
				if( defined("SHOW_IMW_PATIENT_ID_IN_LABEL") && constant("SHOW_IMW_PATIENT_ID_IN_LABEL") == 'YES') {
						$ptmrn1 = $maindataArr[$p-1]['imwPatientId'] ? ' - '.$maindataArr[$p-1]['imwPatientId'] : '';
						$ptmrn2 = $maindataArr[$p]['imwPatientId'] ? ' - '.$maindataArr[$p]['imwPatientId'] : '';
				} else {
					
					if($maindataArr[$p-1]['patient_id_stub']){
						$ptmrn1	= ' - '.$maindataArr[$p-1]['patient_id_stub'];//.' / '.$patientMrnArr['imwPatientId'][$maindataArr[$p-1]['patient_id_stub']];
					}else {
						$ptmrn1	= ' - '.$patientMrnArr['patient_id'][$maindataArr[$p-1]['imwPatientId']];//.' / '.$maindataArr[$p-1]['imwPatientId'];	
					}

					if($maindataArr[$p]['patient_id_stub']){
						$ptmrn2	= ' - '.$maindataArr[$p]['patient_id_stub'];//.' / '.$patientMrnArr['imwPatientId'][$maindataArr[$p]['patient_id_stub']];
					}else {
						$ptmrn2	= ' - '.$patientMrnArr['patient_id'][$maindataArr[$p]['imwPatientId']];//.' / '.$maindataArr[$p]['imwPatientId'];	
					}
				}
				
				
			}
			$pdf->SetFont('helveticaB','B',$font_size);
			$pdf->Ln($large_inner);
			$pdf->Cell(19,$cell_height3,'Pt Name:',0,0,'');
			$pdf->Cell(85,$cell_height3,$maindataArr_sub1.$ptmrn1,0,0,'');
			if($maindataArr[$p]['patient_last_name']){
				$pdf->Cell(19,$cell_height3,'Pt Name:',0,0,'');
				$pdf->Cell(41,$cell_height3,$maindataArr_sub_p1.$ptmrn2,0,0,'');
			}
			$pdf->Ln($large_inner);

			// Start Printing DOB and GENDER in Large Label
			if( $large_label_enable_patient_dob == "Y" || $large_label_enable_patient_gender == "Y"  ) 
			{
				$dob_label = ( $large_label_enable_patient_dob <> "Y" && $large_label_enable_patient_gender == "Y" ) ? 'Gender:' : 'DOB:';

				// Get patient Gender 
				$gender1 	= ($large_label_enable_patient_gender == "Y") ? $genderArr[$maindataArr[$p-1]['patient_sex']] : '';
				$gender2 	= ($large_label_enable_patient_gender == "Y") ? $genderArr[$maindataArr[$p]['patient_sex']] : '';
				
				// GET patient DOB
				$stub_dob = $stub_dob1 = '';
				if( $large_label_enable_patient_dob == "Y" ) {
					list($dob_y,$dob_m,$dob_d)=explode('-',$maindataArr[$p-1]['patient_dob']);
					$stub_dob=$dob_m.'/'.$dob_d.'/'.$dob_y;

					list($dob_y1,$dob_m1,$dob_d1)=explode('-',$maindataArr[$p]['patient_dob']);
					$stub_dob1=$dob_m1.'/'.$dob_d1.'/'.$dob_y1;
				} else {
					$gender1 = $gender1 ? substr($gender1,1,-1) : '';
					$gender2 = $gender2 ? substr($gender2,1,-1) : '';
				}
				
				$pdf->Cell(19,$cell_height4,$dob_label,0,0,'');
				$pdf->Cell(85,$cell_height4,$stub_dob.$gender1,0,0,'');

				if($maindataArr[$p]['patient_dob']){
					$pdf->Cell(19,$cell_height4,$dob_label,0,0,'');
					$pdf->Cell(41,$cell_height4,$stub_dob1.$gender2,0,0,'');
				}
				$pdf->Ln($large_inner);
			}
			// End  Printing DOB and GENDER in Large Label

			// Start Printing Procedure and site
			if($large_label_enable_procedure == "Y" || $large_label_enable_site == "Y"  ) {
				
				// Procedure Site
				$site1	= $large_label_enable_site == 'Y' ? $siteArr[$maindataArr[$p-1]['site']] : '';
				$site2 	= $large_label_enable_site == 'Y' ? $siteArr[$maindataArr[$p]['site']] : '';

				if( $large_label_enable_procedure == "Y" ) {
					$second_line_proc1 = $site1?30:35;
					$second_line_proc2 = $site2?30:35;
				
					// Appointment Procedure
					$stub_procedure1	= $maindataArr[$p-1]['patient_primary_procedure'];
					$stub_procedure		= $maindataArr[$p]['patient_primary_procedure'];
					$stub_procedure_sub	= trim(ucwords(($stub_procedure1)));
					$lenOfName_pp		= strlen($stub_procedure_sub.$site1);
				
					if($lenOfName_pp>35){
						$stub_procedure_sub_1 = trim(substr($stub_procedure_sub, 0, 35));
						$stub_procedure_sub_1_remaining = trim(substr($stub_procedure_sub, 35, $second_line_proc1)).$site1;
					}else{
						$stub_procedure_sub_1=$stub_procedure_sub.$site1;
						$stub_procedure_sub_1_remaining='';
					}
				
					$stub_procedure_sub_p=trim(ucwords(($stub_procedure)));
					$lenOfName_p1=strlen($stub_procedure_sub_p.$site2);
				
					if($lenOfName_p1>35){
						$stub_procedure_sub_p1 = trim(substr($stub_procedure_sub_p, 0, 35));
						$stub_procedure_sub_p1_remaining = trim(substr($stub_procedure_sub_p, 35, $second_line_proc1)).$site2;
					}else{
						$stub_procedure_sub_p1=$stub_procedure_sub_p.$site2;
						$stub_procedure_sub_p1_remaining = '';
					}
				}
				else {
					$stub_procedure_sub_1=$site1 ? substr($site1,1,-1) : '';
					$stub_procedure_sub_1_remaining='';

					$stub_procedure_sub_p1= $site2 ? substr($site2,1,-1) : '';
					$stub_procedure_sub_p1_remaining = '';
				}

				$proc_label = ( $large_label_enable_procedure <> "Y" && $large_label_enable_site == "Y" ) ? 'Site:' : 'Proc:';
				
				$pdf->Cell(19,$cell_height4,$proc_label,0,0,'');
				$pdf->Cell(85,$cell_height4,$stub_procedure_sub_1,0,0,'');
				if($maindataArr[$p]['patient_primary_procedure']){
					$pdf->Cell(19,$cell_height4,$proc_label,0,0,'');
					$pdf->Cell(41,$cell_height4,$stub_procedure_sub_p1,0,0,'');
				}
				
				$pdf->Ln($large_inner);
				$pdf->Cell(19,$cell_height4,'',0,0,'');
				$pdf->Cell(85,$cell_height4,$stub_procedure_sub_1_remaining,0,0,'');
					
				if($maindataArr[$p]['patient_primary_procedure']){
					$pdf->Cell(19,$cell_height4,'',0,0,'');
					$pdf->Cell(41,$cell_height4,$stub_procedure_sub_p1_remaining,0,0,'');
				}
			}
			// End Printing Procedure and site
			
			if($maindataArr[$p]['patient_primary_procedure']){
				$p++;
			}
			if( $averyLargeLabel) {
				$large_bottom1 = $large_bottom + ($j>5 ? 0.8 : 0.4);
				$pdf->Ln($large_bottom1);
			} else {
				$pdf->Ln($large_bottom);
			}
		}
	}else if($label=='small'){
		$loopEnd = ceil(count($maindataArr)/3);
		$p=0;
		$p1=0;
		$j=0;
		$jend=10;
		if($small_bottom>=15 && $small_bottom<19){
			$jend=7;
		}else if($small_bottom>=20){
			$jend=6;
		}
		for($i=0;$i<$loopEnd;$i++,$j++){
			
			if($j==0 || $j >= $jend){
				$pagecount = $pdf->setSourceFile("library/predefined_forms/label_form_wo.pdf");
				$tplidx = $pdf->importPage(1);
				$pdf->SetAutoPageBreak(TRUE, 0);
				$pdf->addPage();
				$pdf->useTemplate($tplidx);
				$pdf->SetFont('helveticaB','B',10);					
				$j=0;
				$pdf->Ln($small_top);
			}
			$p +=2;
			$surgeon_name = $surgeon_name1 = $surgeon_name2 = '';
			if($small_label_enable_surgeon == "Y") {
				$surgeon_name = ' '.$maindataArr[$p-2]['surgeon_fname'].' '.$maindataArr[$p-2]['surgeon_lname'];
				if($maindataArr[$p-1]['dos']){
					$surgeon_name1 = ' '.$maindataArr[$p-1]['surgeon_fname'].' '.$maindataArr[$p-1]['surgeon_lname'];
				}
				if($maindataArr[$p]['dos']){
					$surgeon_name2 = ' '.$maindataArr[$p]['surgeon_fname'].' '.$maindataArr[$p]['surgeon_lname'];
				}
			}
			//$pdf->Cell(30,5,'DOS:',0,0,'');
			list($dos_y,$dos_m,$dos_d)=explode('-',$maindataArr[$p-2]['dos']);
			$stub_dos=($small_label_enable_patient_dos == "Y") ? $dos_m.'/'.$dos_d.'/'.$dos_y : "";
			if($small_label_enable_surgeon == "Y" || $small_label_enable_patient_dos == "Y") { //BASED ON CONFIGURATION IN ADMIN->SETTING
				$pdf->Cell(70,7,trim($stub_dos.$surgeon_name),0,0,'');
				if($maindataArr[$p-1]['dos']){
					//$pdf->Cell(30,5,'DOS:',0,0,'');
					list($dos_y1,$dos_m1,$dos_d1)=explode('-',$maindataArr[$p-1]['dos']);
					$stub_dos1=($small_label_enable_patient_dos == "Y") ? $dos_m1.'/'.$dos_d1.'/'.$dos_y1 : "";
					$pdf->Cell(70,7,trim($stub_dos1.$surgeon_name1),0,0,'');
				}
				if($maindataArr[$p]['dos']){
					//$pdf->Cell(30,5,'DOS:',0,0,'');
					list($dos_y2,$dos_m2,$dos_d2)=explode('-',$maindataArr[$p]['dos']);
					$stub_dos2=($small_label_enable_patient_dos == "Y") ? $dos_m2.'/'.$dos_d2.'/'.$dos_y2 : "";
					$pdf->Cell(35,7,trim($stub_dos2.$surgeon_name2),0,0,'');
				}
				if($i == 4)
					$pdf->Ln($small_inner);
				else{
					$pdf->Ln($small_inner);
				}
			}
			
			$small_pat_1=$maindataArr[$p-2]['patient_last_name'].', '.$maindataArr[$p-2]['patient_first_name'];
			if(trim($maindataArr[$p-2]['patient_middle_name'])) {
				$small_pat_1 = $small_pat_1." ".trim($maindataArr[$p-2]['patient_middle_name']);
			}
			$lenOfName_p1=strlen($small_pat_1);
			$small_pat_final_1=$small_pat_1;

			$small_pat_2=$maindataArr[$p-1]['patient_last_name'].', '.$maindataArr[$p-1]['patient_first_name'];
			if(trim($maindataArr[$p-1]['patient_middle_name'])) {
				$small_pat_2 = $small_pat_2." ".trim($maindataArr[$p-1]['patient_middle_name']);
			}
			$lenOfName_p2=strlen($small_pat_2);
			$small_pat_final_2=$small_pat_2;

			
			$small_pat_3=$maindataArr[$p]['patient_last_name'].', '.$maindataArr[$p]['patient_first_name'];
			if(trim($maindataArr[$p]['patient_middle_name'])) {
				$small_pat_3 = $small_pat_3." ".trim($maindataArr[$p]['patient_middle_name']);
			}
			$lenOfName_p3=strlen($small_pat_3);
			$small_pat_final_3=$small_pat_3;

			$ptmrn = $ptmrn1 = $ptmrn2 = "";
			if($small_label_enable_patient_mrn == "Y") {

				if( defined("SHOW_IMW_PATIENT_ID_IN_LABEL") && constant("SHOW_IMW_PATIENT_ID_IN_LABEL") == 'YES') {
					$ptmrn = $maindataArr[$p-2]['imwPatientId'] ? ' - '.$maindataArr[$p-2]['imwPatientId'] : '';
					$ptmrn1 = $maindataArr[$p-1]['imwPatientId'] ? ' - '.$maindataArr[$p-1]['imwPatientId'] : '';
					$ptmrn2 = $maindataArr[$p]['imwPatientId'] ? ' - '.$maindataArr[$p]['imwPatientId'] : '';
				} else {

					if($maindataArr[$p-2]['patient_id_stub']){
						$ptmrn	= ' - '.$maindataArr[$p-2]['patient_id_stub'];//.' / '.$patientMrnArr['imwPatientId'][$maindataArr[$p-2]['patient_id_stub']];
					}else {
						$ptmrn	= ' - '.$patientMrnArr['patient_id'][$maindataArr[$p-2]['imwPatientId']];//.' / '.$maindataArr[$p-2]['imwPatientId'];
					}
					if($maindataArr[$p-1]['patient_id_stub']){
						$ptmrn1	= ' - '.$maindataArr[$p-1]['patient_id_stub'];//.' / '.$patientMrnArr['imwPatientId'][$maindataArr[$p-1]['patient_id_stub']];
					}else {
						$ptmrn1	= ' - '.$patientMrnArr['patient_id'][$maindataArr[$p-1]['imwPatientId']];//.' / '.$maindataArr[$p-1]['imwPatientId'];	
					}
					if($maindataArr[$p]['patient_id_stub']){
						$ptmrn2	= ' - '.$maindataArr[$p]['patient_id_stub'];//.' / '.$patientMrnArr['imwPatientId'][$maindataArr[$p]['patient_id_stub']];
					}else {
						$ptmrn2	= ' - '.$patientMrnArr['patient_id'][$maindataArr[$p]['imwPatientId']];//.' / '.$maindataArr[$p]['imwPatientId'];	
					}
				}
			}

			$pdf->Cell(70,5,$small_pat_final_1.$ptmrn,0,0,'');
			if($maindataArr[$p-1]['patient_last_name']){
				$pdf->Cell(70,5,$small_pat_final_2.$ptmrn1,0,0,'');
			}
			if($maindataArr[$p]['patient_last_name']){
				$pdf->Cell(35,5,$small_pat_final_3.$ptmrn2,0,0,'');
			}
			
			$pdf->Ln($small_inner);
			
			// Start printing patient dob and gender
			if( $small_label_enable_patient_dob == 'Y' || $small_label_enable_patient_gender == "Y" )
			{
				// Collect Patient Gender value
				$gender = $gender1 = $gender2 = "";
				if($small_label_enable_patient_gender == "Y") {
					$gender 	= $genderArr[$maindataArr[$p-2]['patient_sex']];
					$gender1 	= $genderArr[$maindataArr[$p-1]['patient_sex']];
					$gender2 	= $genderArr[$maindataArr[$p]['patient_sex']];
				}

				// Collect Patient DOB value
				$stub_dob = $stub_dob1 = $stub_dob2 = '';
				if( $small_label_enable_patient_dob == 'Y' )
				{
					list($dob_y,$dob_m,$dob_d)=explode('-',$maindataArr[$p-2]['patient_dob']);
					$stub_dob=$dob_m.'/'.$dob_d.'/'.$dob_y;

					if($maindataArr[$p-1]['patient_dob']){
						list($dob_y1,$dob_m1,$dob_d1)=explode('-',$maindataArr[$p-1]['patient_dob']);
						$stub_dob1=$dob_m1.'/'.$dob_d1.'/'.$dob_y1;
					}
					if($maindataArr[$p]['patient_dob']){
						list($dob_y2,$dob_m2,$dob_d2)=explode('-',$maindataArr[$p]['patient_dob']);
						$stub_dob2=$dob_m2.'/'.$dob_d2.'/'.$dob_y2;
					}
				}
				else {
					$gender = $gender ? substr($gender,1,-1) : '';
					$gender1 = $gender1 ? substr($gender1,1,-1) : '';
					$gender2 = $gender2 ? substr($gender2,1,-1) : '';	
				}

				$pdf->Cell(70,4,$stub_dob.$gender,0,0,'');
				if($stub_dob1 || $gender1 ){
					$pdf->Cell(70,4,$stub_dob1.$gender1,0,0,'');
				}
				if($stub_dob2|| $gender2 ){
					$pdf->Cell(35,4,$stub_dob2.$gender2,0,0,'');
				}

				$pdf->Ln($small_inner);
			}
			// End printing patient dob and gender
			
			// Start Printing Procedure and site
			if($small_label_enable_procedure == "Y" || $small_label_enable_site == "Y"  ) {
				
				// Procedure Site
				$site2	= $small_label_enable_site == 'Y' ? $siteArr[$maindataArr[$p-2]['site']] : '';
				$site1	= $small_label_enable_site == 'Y' ? $siteArr[$maindataArr[$p-1]['site']] : '';
				$site 	= $small_label_enable_site == 'Y' ? $siteArr[$maindataArr[$p]['site']] : '';
				
				if( $small_label_enable_procedure == "Y" ) {
					
					$second_line_proc2 = $site2?29:34;
					$second_line_proc1 = $site1?29:34;
					$second_line_proc = $site?29:34;
				
					// Appointment Procedure
					$stub_procedure2	= $maindataArr[$p-2]['patient_primary_procedure'];
					$stub_procedure1	= $maindataArr[$p-1]['patient_primary_procedure'];
					$stub_procedure		= $maindataArr[$p]['patient_primary_procedure'];
					
					$small_proc			= trim(ucwords($stub_procedure));
					$lenOfName_pro		= strlen($small_proc.$site);
					if($lenOfName_pro>34){
						$small_proc_final = trim(substr($small_proc, 0, 34));
						$small_proc_final_remaining = trim(substr($small_proc, 34, $second_line_proc)).$site;
					}else{
						$small_proc_final=$small_proc.$site;
						$small_proc_final_remaining = '';
					}
			
					$small_proc1=trim(ucwords(($stub_procedure1)));
					$lenOfName_pro1=strlen($small_proc1.$site1);
					if($lenOfName_pro1>34){
						$small_proc_final1 = trim(substr($small_proc1, 0, 34));
						$small_proc_final1_remaining = trim(substr($small_proc1, 34, $second_line_proc1)).$site1;
					}else{
						$small_proc_final1=$small_proc1.$site1;
						$small_proc_final1_remaining = '';
					}
			
					$small_proc2=trim(ucwords(($stub_procedure2)));
					$lenOfName_pro2=strlen($small_proc2.$site2);
					if($lenOfName_pro2>34){
						$small_proc_final2 = trim(substr($small_proc2, 0, 34));
						$small_proc_final2_remaining = trim(substr($small_proc2, 34, $second_line_proc2)).$site2;
					}else{
						$small_proc_final2=$small_proc2.$site2;
						$small_proc_final2_remaining = '';
					}
			
				}
				else {
					
					$small_proc_final=$site ? substr($site,1,-1) : '';
					$small_proc_final_remaining = '';

					$small_proc_final1=$site1 ? substr($site1,1,-1) : '';
					$small_proc_final1_remaining = '';

					$small_proc_final2=$site2 ? substr($site2,1,-1) : '';
					$small_proc_final2_remaining = '';
				}

				$proc_label = ( $small_label_enable_procedure <> "Y" && $small_label_enable_site == "Y" ) ? 'Site:' : 'Proc:';
				
				//START DISPLAY FIRST 34 CHARACTERS OF  PROCEDURE 	
				$pdf->Cell(70,3,$small_proc_final2,0,0,'');
				if($maindataArr[$p-1]['patient_primary_procedure']){
					$pdf->Cell(70,3,$small_proc_final1,0,0,'');
				}
				if($maindataArr[$p]['patient_primary_procedure']){
					$pdf->Cell(35,3,$small_proc_final,0,0,'');
				}
				$pdf->Ln($small_inner);
				//END DISPLAY FIRST 34 CHARACTERS OF  PROCEDURE 


				//START DISPLAY REMAINING CHARACTERS OF  PROCEDURE 	
				$pdf->Cell(70,1,$small_proc_final2_remaining,0,0,'');
				if($maindataArr[$p-1]['patient_primary_procedure']){
					$pdf->Cell(70,1,$small_proc_final1_remaining,0,0,'');
				}
				if($maindataArr[$p]['patient_primary_procedure']){
					$pdf->Cell(35,1,$small_proc_final_remaining,0,0,'');
				}
				//END DISPLAY REMAINING CHARACTERS OF  PROCEDURE 

			}
			$pdf->Ln($small_bottom);
			// End Printing Procedure and site

			if($maindataArr[$p]['patient_primary_procedure']){
				$p++;
			}
		}
	}	
	if($fpdiCheck == true){
		$flNmeNew = __DIR__."/".$flNme;
		$pdf->Output($flNmeNew,"F");
		/*echo "<script>window.open('pdf/label_final.pdf','_blank');</script>";*/
		$msg = 'Label Successfully Printed.';
	}
}else{
	$msg="No Record Found!";
	 echo '<table width="100%" cellpadding="0" cellspacing="0"> 
				
				<tr valign="middle">
					<td  align="center" colspan=8><span class="text_10b link_top">'.$msg.'</span></td>
				</tr>
			</table>';
}	


?>
