<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
if($_REQUEST['hidd_report_format']!='csv') {
	echo '<table id="loader_tbl" align="center" width="100%" border="0" cellpadding="0" cellspacing="0" style="font-family:Arial, Helvetica, sans-serif;">
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center">Please wait while data is retrieving from the server.</td>
			</tr>
			<tr class="text_9" height="20" bgcolor="#EAF0F7" valign="top">
				<td align="center"><img src="images/pdf_load_img.gif"></td> 
			</tr>
		</table>';
}
set_time_limit(900);
include("common/conDb.php");
if($_SESSION["loginUserId"]=="" && $_SESSION['loginUserName']=="") {
	echo '<script>top.location.href="index.php"</script>';
}
include_once("admin/classObjectFunction.php");
$objManageData = new manageData;

$fac_qry	=	" and stub_tbl.iasc_facility_id='$_SESSION[iasc_facility_id]'";
$fac_con	=	($_SESSION['iasc_facility_id']	 ?	$fac_qry	 :	'' )	; 

$startDateR	=	$_REQUEST['startdate'];
$endDateR	=	$_REQUEST['enddate'];
list($sMon,$sDay,$sYear)		=	explode("-", $startDateR); 
list($eMon,$eDay,$eYear)		=	explode("-", $endDateR ); 

$startdate			=	date('Y-m-d', strtotime(date($sYear.'-'.$sMon.'-'.$sDay)));
$enddate				=	date('Y-m-d', strtotime(date($eYear.'-'.$eMon.'-'.$eDay)));

$provider_type		=	strtolower($_REQUEST['provider_type']);
$provider_data		=	$_REQUEST['provider'];
if(is_array($provider_data)) {
	$provider_data = implode(",",$provider_data);
}
$providerCheckF	=	($provider_type	===	'nurse' )		?	'nurseID'	:	($provider_type === 'anesthesiologist'	?	'anesthesiologist_id'	:	'surgeonId' )	;
$providerCheckA	=	($provider_type	===	'nurse' )		?	'signNurseActivate'	:	($provider_type === 'anesthesiologist'	?	'signAnesthesia1Activate'	:	'signSurgeon1Activate' )	;
$providerCheck	=	'';

if($provider_data	<>	'all' )
{
	
	$providerCheck	=	$providerCheckF  . ' IN (' . $provider_data . ') And ' ;
	
	//$provider_data_arr	=	array() ;
	//$provider_data_arr	=	explode(",",$_REQUEST['provider']);	
}
//get detail for logged in facility
$queryFac=imw_query("select * from facility_tbl where fac_id='$_SESSION[facility]'")or die(imw_error());
$dataFac=imw_fetch_object($queryFac);
$name=stripcslashes($dataFac->fac_name);
$address=stripcslashes($dataFac->fac_address1).' '.stripcslashes($dataFac->fac_address2).' '.stripcslashes($dataFac->fac_city).' '.stripcslashes($dataFac->fac_state);

$SurgeryQry ="select * from surgerycenter where surgeryCenterId=1";
$SurgeryRes = imw_query($SurgeryQry);
while($SurgeryRecord = imw_fetch_array($SurgeryRes)){
	//$name = stripslashes($SurgeryRecord['name']);
	//$address = stripslashes($SurgeryRecord['address'].' '.$SurgeryRecord['city'].' '.$SurgeryRecord['state']);
	$img = $SurgeryRecord['logoName'];
	$surgeryCenterLogo=$SurgeryRecord['surgeryCenterLogo'];
}
//$file=@fopen('html2pdf/white.jpg','w+');
//@fputs($file,$surgeryCenterLogo);
$bakImgResource = imagecreatefromstring($surgeryCenterLogo);
imagejpeg($bakImgResource,'new_html2pdf/white.jpg');

$size=getimagesize('new_html2pdf/white.jpg');
$hig=$size[1];
$wid=$size[0];
$higinc=$hig+10;
$filename='new_html2pdf/white.jpg';

function showThumbImages($fileName='white.jpg',$targetWidth=500,$targetHeight=70)
{ 

	if(file_exists($fileName))
	{ 
		$img_size=getimagesize('new_html2pdf/white.jpg');
		 $width=$img_size[0];
		 $height=$img_size[1];
		 $filename;
		do
		{
			if($width > $targetWidth)
			{
				 $width=$targetWidth;
				 $percent=$img_size[0]/$width;
				 $height=$img_size[1]/$percent; 
			}
			if($height > $targetHeight)
			{
				$height=$targetHeight;
				$percent=$img_size[1]/$height;
				$width=$img_size[0]/$percent; 
			}

		}while($width > $targetWidth || $height > $targetHeight);

		$returnArr[] = "<img src='white.jpg' width='$width' height='$height'>";
		$returnArr[] = $width;
		$returnArr[] = $height;
		return $returnArr; 
	} 
	return "";
 }				
 
 $current_date=date("m-d-Y");
$img_logo = showThumbImages('new_html2pdf/white.jpg',170,50);
$imgheight= $img_logo[2]+8;
$imgwidth= $img_logo[1]+200;	
$imgheight=	59; 
// end set surgerycenter detail 	

//START GET USER DETAIL
	$uRowArr = array();
	$uQry = "SELECT usersId,practiceName,fname, mname, lname, TRIM(CONCAT(lname,', ',fname, ' ',mname)) AS provider_name FROM users ORDER BY usersId";
	$uRes = imw_query($uQry) or die($uQry.imw_error());
	if(imw_num_rows($uRes)>0) {
		while($uRow = imw_fetch_array($uRes)) {
			$uRowArr[$uRow['usersId']] = $uRow;
			
		}
	}
//END GET USER DETAIL

$nurseCharts			=	array( 
										'Check List' 	=> 		array(
																				'pages'=>array('Check List'),
																				'table'=>array('surgical_check_list'),
																				'match'=>array('confirmation_id')
																			),
										'Consent Form' =>'',
										'Pre-Op Health' => array(
																				'pages'=>array('H & P Clearance'),
																				'table'=>array('history_physicial_clearance'),
																				'match'=>array('confirmation_id')
																			),
										'Nursing Record'=> array(
																				'pages'=>array('Pre-Op', 'Post-Op'),
																				'table'=>array('preopnursingrecord','postopnursingrecord'),
																				'match'=>array('confirmation_id','confirmation_id')
																			),
										'Physician Orders'=> array(
																				'pages'=>array('Pre-Op', 'Post-Op'),
																				'table'=>array('preopphysicianorders','postopphysicianorders'),
																				'match'=>array('patient_confirmation_id','patient_confirmation_id')
																			),
										'Anesthesia'=> 		array(
																				'pages'=>array('General Nurse Notes'),
																				'table'=>array('genanesthesianursesnotes'),
																				'match'=>array('confirmation_id')
																			),
										'Operating Room'=> array(
																				'pages'=>array('Intra-Op Record'),
																				'table'=>array('operatingroomrecords'),
																				'match'=>array('confirmation_id')
																			),
										'Laser Procedure'=> array(
																				'pages'=>array('Laser Procedure'),
																				'table'=>array('laser_procedure_patient_table'),
																				'match'=>array('confirmation_id')
																			),
										'Post Op Inst. Sheet' => array(
																				'pages'=>array('Instruction Sheet'),
																				'table'=>array('patient_instruction_sheet'),
																				'match'=>array('patient_confirmation_id')
																			),
									);
									
									
$anesthesiologistCharts	=	array( 
										'Consent Form' =>'',
										'Pre-Op Health' => array(
																				'pages'=>array('H & P Clearance'),
																				'table'=>array('history_physicial_clearance'),
																				'match'=>array('confirmation_id')
																			),
										'Anesthesia'=> 		array(
																				'pages'=>array('MAC/Regional','General'),
																				'table'=>array('localanesthesiarecord','genanesthesiarecord'),
																				'match'=>array('confirmation_id','confirmation_id')
																			),
										'Operating Room'=> array(
																				'pages'=>array('Intra-Op Record'),
																				'table'=>array('operatingroomrecords'),
																				'match'=>array('confirmation_id')
																			),
										
									);																	

$surgeonCharts	=	array( 
										'Consent Form' =>'',
										'Pre-Op Health' => array(
																				'pages'=>array('H & P Clearance'),
																				'table'=>array('history_physicial_clearance'),
																				'match'=>array('confirmation_id')
																			),
										'Physician Orders'=> array(
																				'pages'=>array('Pre-Op', 'Post-Op'),
																				'table'=>array('preopphysicianorders','postopphysicianorders'),
																				'match'=>array('patient_confirmation_id','patient_confirmation_id')
																			),
										'Operating Room'=> array(
																				'pages'=>array('Intra-Op Record'),
																				'table'=>array('operatingroomrecords'),
																				'match'=>array('confirmation_id')
																			),
										'Laser Procedure'=> array(
																				'pages'=>array('Laser Procedure'),
																				'table'=>array('laser_procedure_patient_table'),
																				'match'=>array('confirmation_id')
																			),
										'Surgical'	=>			 array(
																				'pages'=>array('Operative Report'),
																				'table'=>array('operativereport'),
																				'match'=>array('confirmation_id')
																			),
										'Discharge Summary' => array(
																				'pages'=>array('Discharge Summary'),
																				'table'=>array('dischargesummarysheet'),
																				'match'=>array('confirmation_id')
																			),
										'Post Op Inst. Sheet' => array(
																				'pages'=>array('Instruction Sheet'),
																				'table'=>array('patient_instruction_sheet'),
																				'match'=>array('patient_confirmation_id')
																			),
									);

// Store Stub Table Data in variable
$stubData		=	array();
$patientData	=	array();
$stubCheck['patient_status <>']	=	'Canceled';
if($_SESSION['iasc_facility_id']) {
	$stubCheck['iasc_facility_id =']		=	$_SESSION['iasc_facility_id'];	
}
$stubCheck['dos Between']			=	$startdate."' And '".$enddate ;
$stubDataArr	=	$objManageData->getAllRecords('stub_tbl',array('patient_confirmation_id'), $stubCheck );

if(is_array($stubDataArr) && count($stubDataArr) > 0 )
{
	foreach($stubDataArr as $data)
	{
		$stubData[$data->patient_confirmation_id]	=	$data->patient_confirmation_id;
	}
}
// End Store Stub Table Data in variable




$ptConfQuery 	= "Select PC.surgeon_name,PC.dos, PC.patientConfirmationId, PC.patientId, PC.ascId, PC.patient_primary_procedure_id, PC.patient_primary_procedure, PC.surgeonId, PC.anesthesiologist_id, PC.nurseId, PDT.patient_fname, PDT.patient_mname, PDT.patient_lname, PDT.date_of_birth, PRC.catId  
												From patientconfirmation PC
												Left Join stub_tbl On PC.patientConfirmationId = stub_tbl.patient_confirmation_id 
												Left Join users ON PC.".$providerCheckF." = users.usersId  
												Left Join patient_data_tbl  PDT ON PC.patientId = PDT.patient_id
												Left Join procedures  PRC ON PC.patient_primary_procedure_id = PRC.procedureId
												Where ".$providerCheck." PC.dos Between '".$startdate."' And '".$enddate."'
												".$fac_con."
												 ORDER BY PC.dos Asc "; 

$ptConfRes 		= imw_query($ptConfQuery) or die($ptConfQuery . imw_error());
$ptConfNum	= imw_num_rows($ptConfRes);

$result				= array();
$patientCounter		=	0;
while($ptConfRow 	= imw_fetch_array($ptConfRes)) {
	
	$pCheck				=	$ptConfRow['surgeon_name'];
	$dos				=	date('Ymd', strtotime($ptConfRow['dos'])) ;
	$confirmationId		=	$ptConfRow['patientConfirmationId'] ;
	$patientId			=	$ptConfRow['patientId'] ;
	$ascId				=	$ptConfRow['ascId'] ;
	$procedureId		=	$ptConfRow['patient_primary_procedure_id'];
	$procedure			=	$ptConfRow['patient_primary_procedure'];
	$patientName		=	$ptConfRow['patient_lname'] . ', ' . $ptConfRow['patient_fname'] ;
	$patientDob			=	date('m-d-Y', strtotime($ptConfRow['date_of_birth'])) ;
	$procedureCatId		=	$ptConfRow['catId'] ;
	$surgeonId			=	$ptConfRow['surgeonId'];
	$anesthesiologistId	=	$ptConfRow['anesthesiologist_id'];
	$nurseId			=	$ptConfRow['nurseId'];
	if( !$stubData[$confirmationId] ) continue ;
	
	if( !array_key_exists($pCheck,$result))											$result[$pCheck]				=	array() ;
	if( !array_key_exists($dos,$result[$pCheck]))									$result[$pCheck][$dos]	=	array() ;	
	if( !array_key_exists($confirmationId,$result[$pCheck][$dos] ))	$result[$pCheck][$dos][$confirmationId]	=	array() ;	
	
	
	$result[$pCheck][$dos][$confirmationId]	['patientName']			=	$patientName ;
	$result[$pCheck][$dos][$confirmationId]	['AscId']				=	$ascId;
	$result[$pCheck][$dos][$confirmationId]	['dob']					=	$patientDob;
	$result[$pCheck][$dos][$confirmationId]	['procedure']			=	$procedure;
	$result[$pCheck][$dos][$confirmationId]	['surgeonId']			=	$surgeonId;
	$result[$pCheck][$dos][$confirmationId]	['anesthesiologistId']	=	$anesthesiologistId;
	$result[$pCheck][$dos][$confirmationId]	['nurseId']				=	$nurseId;
	
	
	$chartData	=	array()	;

	$charts			=	($provider_type == 'nurse' ? $nurseCharts : ($provider_type == 'anesthesiologist' ? $anesthesiologistCharts : $surgeonCharts ))	;
	
	foreach($charts as $chartKey=>$chart)
	{
			if($chartKey === 'Consent Form')
			{		
					$chartData[$chartKey]	=	array();
					$query	=	"Select * From consent_multiple_form WHERE confirmation_id='".$confirmationId."'  And consent_purge_status!='true' AND ".$providerCheckA."='yes' Order By consent_category_id Asc" ;
					$sql		=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).' : ' .imw_error() );
					while( $row	=	imw_fetch_assoc($sql) )
					{
							if($row['form_status'] === 'not completed' )		
							{	
									$consentCatData	=	$objManageData->getExtractRecord('consent_category','category_id', $row['consent_category_id'],'category_name');
									extract($consentCatData);
									if( !array_key_exists($category_name,$chartData[$chartKey] ))	$chartData[$chartKey][$category_name]	=	$row['surgery_consent_name'];	
									else	$chartData[$chartKey][$category_name] .= ',' . $row['surgery_consent_name'];
							}
					}
					
					if(count($chartData[$chartKey]) == 0 ) unset($chartData[$chartKey]) ;
					
			}
			
			elseif( ($chartKey === 'Operating Room' && $procedureCatId === 2 ) || ($chartKey === 'Laser Procedure' && $procedureCatId !== 2 ) )
			{
				//Do Nothing 	
			}
			
			else
			{
					foreach($chart['pages'] as $key=>$page)
					{
							$form_status	=	''	;
							$query	=	"Select form_status From ".$chart['table'][$key]."  Where  ".$chart['match'][$key]." = ". $confirmationId." "	;
							
							if(	 $chartKey === 'Post Op Inst. Sheet' )	
								$query	.=	"  And sign".($provider_type == 'nurse' ? 'Nurse' : 'Surgeon1')."Activate = 'yes'"	;
							
							$sql		=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).' : ' .imw_error() );
							$row	=	imw_fetch_assoc($sql) ;
							extract($row) ;
							if($form_status  ===  'not completed' )
							{
									if( !array_key_exists($chartKey,$chartData))	$chartData[$chartKey]	=	$page;	
									else	$chartData[$chartKey] .= ',' . $page ;
								
							}
							
					}
			
			}
			
	}
	
	//print_r($chartData); echo '<br>';
	
	if(is_array($chartData) && count($chartData) > 0 )
	{
		$result[$pCheck][$dos][$confirmationId]	['charts']		=	$chartData	;
		$patientCounter++;
	}
	else
	{
		unset($result[$pCheck][$dos][$confirmationId]) ;
	}
	$result	=	array_filter($result,'array_filter');
	//ksort($result[$pCheck],SORT_NUMERIC);
}



//echo '<pre>';
//print_r($result);
//exit;



//background-color:#F7A270;
$table.='<style>
	table { width:700px;  font-family:Arial, Helvetica, sans-serif; color:#000; border:0px;  }
	td { padding-left:3px; border:0px;}
	.bottomBorder { border-bottom:solid  1px #C0C0C0 ;  padding-top:3px;padding-bottom:3px;}
	.topBorder { border-top:solid  1px #C0C0C0 ; }
	.leftBorder { border-left:solid  1px #C0C0C0 ; }
	.rightBorder { border-right:solid  1px #C0C0C0 ; }
	.tb_heading { font-size:12px; font-weight:bold; height:12px; padding-top:1px;padding-bottom:1px; }
	.tb_dos { height:10px; background-color:#C0C0C0; color:#000; font-weight:bold; padding-top:1px; padding-bottom:1px; }
	.text_b { font-size:14px; font-family:Arial, Helvetica, sans-serif; font-weight:bold; color:#000; }
	.text_16b { font-size:16px; font-family:Arial, Helvetica, sans-serif; font-weight:bold; color:#000; }
	.text { font-size:14px; font-family:Arial, Helvetica, sans-serif; background-color:#FFF; }
	.text_12 { font-size:12px; font-family:Arial, Helvetica, sans-serif; }
	.parent { font-weight:bold; }
	.sub 		{ padding-left:15px;  }
	.sub_sub { padding-left:25px; }
	</style>
	
	';
/**
* 	PHP variables holds Border classes combinations
**/
$LB		=	"leftBorder bottomBorder";
$LR		=	"leftBorder rightBorder";
$LBR		=	"leftBorder bottomBorder rightBorder";
$LBT		=	"leftBorder bottomBorder topBorder"; 
$LBRT	=	"leftBorder bottomBorder rightBorder topBorder"; 

/*
*
* Common header to print in each pdf page 
* Replace {{SURGEON_NAME}} 
*
*/
$pageHeader	='<page_header>
								<table  cellpadding="0" cellspacing="0" style="width:725px;" width="725" class="headTable" border="0" >
										<tr height="'.$higinc.'" >
											<td  class="text_16b" style="background-color:#CD523F; padding-left:5px; color:white; width:475px; "  width="475" align="left" valign="middle" >
												<b>'.$name.'<br>'.$address.'</b>
											</td>
											<td style="background-color:#CD523F; width:250px;" width="250" align="right" height="'.$imgheight.'" >'.$img_logo[0].'&nbsp;</td>
										</tr>
										<tr style="background-color:#FFFFFF;padding-top:5px;"><td colspan="2" width="725" style="width:725px;"></td></tr>
										<tr height="22" >
											<td colspan="2" class="text_b" style="background-color:#F1F4F0; width:725px; " width="725"  >
												<table style="width:725px;" border="0" cellpadding="0" cellspacing="0" width="725">
													<tr height="22" bgcolor="#F1F4F0">
														<td align="right" width="450" nowrap style="width:450px;">Incomplete Chart Report</td>
														<td align="right" style=" padding-right:5px; width:275px; " width="275" >
																Created On :'.$current_date.'
														</td>
													</tr>
												</table>
											</td>
										</tr>
										<tr height="25">
											<td colspan="2" class="text_b" style="background-color:#F1F4F0; width:725px; " width="725"  >
												<table style="width:725px;" border="0" cellpadding="0" cellspacing="0" width="725">
													<tr height="22" bgcolor="#F1F4F0">
														<td align="left" width="375" nowrap style="width:375px;"><b>Surgeon Name:&nbsp;{{SURGEON_NAME}}</b></td>
														<td align="right" style=" padding-right:5px; width:350px; " width="350" >
																From&nbsp;'.$startDateR.'
																&nbsp;&nbsp;-&nbsp;&nbsp;To&nbsp;'.$endDateR.'
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
									<table  cellpadding="0" cellspacing="0" style="width:700px;" width="700" class="headTable2"  border="0">
										<tr >
											<td class="tb_heading '.$LBT.'" style="width:50px" >Seq</td>
											<td class="tb_heading '.$LBT.'" style="width:180px" nowrap >Patient Name - ASCID</td>
											<td class="tb_heading '.$LBT.'" style="width:90px" >DOB</td>
											<td class="tb_heading '.$LBT.'" style="width:150px" >Procedure</td>
											<td class="tb_heading '.$LBRT.'" style="width:230px" >Incompleted Charts</td>
										</tr>
									</table>
							</page_header>';
							$frm_date_csv 	= '"From '.trim($startDateR).'"';
							$to_date_csv 	= '"To '.trim($endDateR).'"';
							$csv_content1 = $name.','."".',Incomplete Chart Report Created On '.$current_date.','."";
							$csv_content2 = $address.','."".','."".','.$frm_date_csv.','.$to_date_csv;
							$provider_type_csv_label = ucfirst(strtolower($provider_type));
							$csv_content .= 'Seq'.','.'"Patient Name"'.','.'"ASC-ID"'.','.'"DOB"'.','.'"Procedure"'.','.'"Incompleted By '.$provider_type_csv_label.'"'.','.'"Incompleted Charts"'."\n";
							
		
	// Holds Start Page tag & common footer tag 
	$pageNFooter	=	'<page backtop="32mm" backbottom="15mm">
									<page_footer>
											<table >
												<tr><td style="text-align:center; width:700px; ">Page [[page_cu]]/[[page_nb]]</td></tr>
											</table>
									</page_footer>';
									
				// PDF Printing starts here	
					$surgeonCounter	=	0;
					foreach($result as $surgeonName=>$surgeonData)
					{	
							$counter = 0;
							if(is_array($surgeonData)	&& count($surgeonData) > 0 )
							{
								$surgeonCounter++; 
								$table	.=	(($surgeonCounter > 1) ? ('</page>'.$pageNFooter) : $pageNFooter ).str_replace('{{SURGEON_NAME}}',$surgeonName,$pageHeader) ;
								$csv_content .= '"Surgeon Name: '.$surgeonName.'"'.','.'""'.','.'""'.','.'""'.','.'""'.','.'""'."\n";
								foreach($surgeonData as $dos => $patients)
								{		
										
										if(is_array($patients) and count($patients) > 0 )
										{
											$table.='<table  cellpadding="0" cellspacing="0" style="width:700px;" width="700" >';
											$dosY	=	substr($dos,0,4);
											$dosM	=	substr($dos,4,2);
											$dosD	=	substr($dos,6,2);
											$dos	=	date('m-d-Y', strtotime(date($dosY.'-'.$dosM.'-'.$dosD)));
											$table.='<tr><td colspan="5" class="tb_dos '.$LBR.'">DOS: '.$dos .'</td></tr>';	
											$csv_content .= '"DOS: '.$dos.'"'.','.'""'.','.'""'.','.'""'.','.'""'.','.'""'."\n";
											foreach($patients as $confirmationId => $patient)
											{
												$counter++ ; 
												$providerTypeName = $providerTypeNameCsv = '';
												if(trim($patient['AscId'])) {
													if($provider_type == "nurse") {
														$providerTypeName = "<br><b>Nurse:</b> ".$uRowArr[$patient['nurseId']]['provider_name'];
													}else if($provider_type == "anesthesiologist") {
														$providerTypeName = "<br><b>Anesthesiologist:</b> ".$uRowArr[$patient['anesthesiologistId']]['provider_name'];
													}
													$providerTypeNameCsv = trim(str_ireplace(array('<b>','</b>','<br>','Surgeon:','Nurse:','Anesthesiologist:'),'',$providerTypeName));
												}
												if($provider_type == "surgeon") {
													$providerTypeNameCsv = $uRowArr[$patient['surgeonId']]['provider_name'];
												}

												$table.='<tr >
																	<td class="text_12" style="width:50px;"   valign="top" class="'.$LB.'">'.$counter.'</td>
																	<td class="text_12" style="width:180px;" valign="top" class="'.$LB.'">'.$patient['patientName']	.'&nbsp;-&nbsp;' . $patient['AscId'].'&nbsp;'.$providerTypeName.'</td>
																	<td class="text_12" style="width:90px;" 	 valign="top" class="'.$LB.'">'.$patient['dob'] .'</td>
																	<td class="text_12" style="width:150px;" valign="top" class="'.$LB.'">'.$patient['procedure'] .'</td>
																	<td class="text_12" style="width:230px;" valign="top" class="'.$LBR.'">
																	<table width="280" celpadding="0" cellspacing="0" class="listTable" >
																	';
																	
																	//START CSV CODE
																	$seq_csv 						=	'"'.trim($counter).'"';
																	$patient_name_csv 				=	'"'.trim($patient['patientName']).'"';
																	$asc_id_csv 					=	'"'.trim($patient['AscId']).'"';
																	$patient_dob_csv 				=	'"'.trim($patient['dob']).'"';
																	$procedure_name_csv 			= 	'"'.trim(str_ireplace("<br>","  ",$patient['procedure'])).'"';
																	$provider_type_name_csv			=	'"'.trim($providerTypeNameCsv).'"';

																	$csv_content 			.= $seq_csv.','.$patient_name_csv.','.$asc_id_csv.','.$patient_dob_csv.','.$procedure_name_csv.','.$provider_type_name_csv;
																					
																	//END CSV CODE
																	
																	//$csv_content .= '"DOS: '.$dos.'"'.','.'""'.','.'""'.','.'""'.','.'""'.','.'""'."\n";
																	$chartList	=	'';
																	$cnt_csv = 0;
																	foreach($patient['charts'] as $key=> $chart)
																	{
																		
																		$chartList .= '<tr><td class="parent">'.$key.'</td></tr>';	
																		$key_csv = '"'.trim($key).'"';
																		if($cnt_csv =='1') {
																			//$csv_content 			.= ','.$key_csv;
																		}else {
																			//$csv_content 			.= '""'.','.'""'.','.'""'.','.'""'.','.'""'.','.$key_csv;
																		}
																		if($key === 'Consent Form' )
																		{
																			foreach($chart as $ckey => $clist)
																			{
																					$pages		=	explode(",",$clist) ;
																					if(is_array($pages) && count($pages) > 0 )
																					{
																							$chartList	.=	'<tr><td class="sub"><b>&raquo;&nbsp;</b>' . $ckey.'</td></tr>';
																							//$csv_content 			.= '>>'.$ckey;
																							foreach($pages as $chartName)
																							{
																								$chart_str = '';
																								$chartNameStrLen = 25;
																								$chartName = ucwords(strtolower($chartName));
																								$brSpace = '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
																								if( strlen($chartName) > $chartNameStrLen) {
																									$chart_str = substr($chartName,0,$chartNameStrLen).$brSpace;
																									$chart_str .=substr($chartName,($chartNameStrLen*1),$chartNameStrLen).((strlen($chartName)>($chartNameStrLen*2))?$brSpace:''); 
																									$chart_str .=substr($chartName,($chartNameStrLen*2),$chartNameStrLen).((strlen($chartName)>($chartNameStrLen*3))?$brSpace:''); 
																									$chart_str .=substr($chartName,($chartNameStrLen*3),$chartNameStrLen);
																								}
																								else $chart_str = $chartName;

																								$chartList	.=	'<tr><td class="sub_sub" ><b>&raquo;&raquo;&nbsp;</b>' .$chart_str.'</td></tr>';
																								$cnt_csv++;
																								$chartName_csv = '"'.trim($key).' >> '.trim($ckey).' >> '.trim($chartName).'"';
																								
																								if($cnt_csv =='1') {
																									$csv_content 			.= ','.$chartName_csv;
																								}else {
																									$csv_content 			.= "\n";
																									$csv_content 			.= '""'.','.'""'.','.'""'.','.'""'.','.'""'.','.'""'.','.$chartName_csv;
																								}	
																							}
																					}
																				
																			}
																		}
																		else
																		{
																			
																			$pages		=	explode(",",$chart) ;
																			if(is_array($pages) && count($pages) > 0 )
																			{
																				//$sub_cnt_csv = 0;
																				foreach($pages as $chartName)
																				{
																					$chartList	.=	'<tr><td class="sub"><b>&raquo;&nbsp;</b>' . $chartName.'</td></tr>';
																					$cnt_csv++;
																					$chartName_csv = '"'.trim($key).' >> '.trim($chartName).'"';
																					
																					if($cnt_csv =='1') {
																						$csv_content 			.= ','.$chartName_csv;
																					}else {
																						$csv_content 			.= "\n";
																						$csv_content 			.= '""'.','.'""'.','.'""'.','.'""'.','.'""'.','.'""'.','.$chartName_csv;
																					}
																					
																				}
																			}
																		}	
																		
																	}
																	
																	$table	.=	$chartList ;
																						
												$table.='</table>
														</td>
													</tr>';
													$csv_content 			.= "\n";
												
											}
											$table.='</table>';
											
										}
								}
							}
					}
	
	$table.='</page>';

if($_REQUEST['hidd_report_format']=='csv' && $patientCounter > 0) {
	$file_name=$_SERVER['DOCUMENT_ROOT'].'/'.$surgeryCenterDirectoryName.'/admin/pdfFiles/incomplete_chart_reportpop.csv';
	if(file_exists($file_name)) {
		@unlink($file_name);
	}
	$fpH1 = fopen($file_name,'w');
	fwrite($fpH1, $csv_content1."\n");
	fwrite($fpH1, $csv_content2."\n\r");
	fwrite($fpH1, $csv_content."\n\r");
	$objManageData->download_file($file_name);
	fclose($fpH1);
	exit;
} else {
	$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
	$intBytes = fputs($fileOpen,$table);
	//echo $table;die;
	fclose($fileOpen);
}
						


?>	
<!DOCTYPE html>
<html>
<head>
<title>Incomplete Chart Report</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<link rel="stylesheet" href="css/style_surgery.css" type="text/css" >   
<script language="javascript">
	window.focus();
	function submitfn()
	{
		document.printFrm.submit();
	}
</script>
</head>
<body>
 <form name="printFrm" action="new_html2pdf/createPdf.php" method="post">

</form>
<?php
if($patientCounter <= 0 && $_REQUEST['hidd_report_format']=='csv') {
?>
	<script type="text/javascript">
		location.href = "incomplete_chart_report.php?no_record=yes&date1=<?php echo $_REQUEST['startdate'];?>&date2=<?php echo $_REQUEST['enddate'];?>&provider_type=<?php echo $_REQUEST['provider_type'];?>&provider=<?php echo $provider_data;?>";
	</script>
<?php
}else if( $patientCounter > 0 ){?>		
	<script type="text/javascript">
        submitfn();
    </script>
<?php 
}else {?>
	<script>
		if(document.getElementById("loader_tbl")) {
			document.getElementById("loader_tbl").style.display = "none";	
		}
	</script>	
	<table style=" font-family:Verdana, Geneva, sans-serif; font-size:12px; background-color:#EAF0F7; width:100%; height:100%;">
		<tr>
			<td class="alignCenter valignTop" style="width:100%;"><b>No Record Found</b></td> 
		</tr>
	</table>
<?php		
}?>

</body>
</html>