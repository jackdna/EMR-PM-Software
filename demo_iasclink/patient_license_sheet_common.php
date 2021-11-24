<?php 
	$Wid	=	$_REQUEST['multiPatientInWaitingId'];
	$from	=	array( 
								'{PATIENT_NAME}',
								'{DOB}',
								'{AGE}',
								'{PatientId}',
								'{SITE}',
								'{PRI PROC}',
								'{SEC PROC}',
								'{DOS}',
								'{SURGERY_TIME}',
								'{SURGEON_NAME}',
								'{EXTRA_LENS_ROW}',
								'{COMMENTS}',
								'{REFRACTION}',
								'{OTHER_EYE_REFRACTION}',
								'{K_FLAT}',
								'{K_STEEP}',
								'{K_AXIS}',
								'{K_CYL}',
								'{OTHER_K_FLAT}',
								'{OTHER_K_STEEP}',
								'{OTHER_K_AXIS}',
								'{OTHER_K_CYL}',
								'{DOMINANT_EYE}',
								'{PUPIL_DILATED}',
								'{LENS_SLE_SUMMARY}',
								'{SX_COMMENTS}',
								'{EXTRA_SURGEON_TECH_ROW}'
							) ;
								
	if($Wid)
	{
			$iolData	=	array();
			$iolQuery	=	"Select * From iolink_iol_manufacturer 
												Where patient_in_waiting_id IN(".$Wid.")
												And patient_in_waiting_id!= ''
												And patient_in_waiting_id!= '0'
												And patient_id != ''
												And patient_id != 0 
												ORDER BY opRoomDefault DESC, iol_manufacturer_id ASC
								";
			$iolSql		=	imw_query($iolQuery) or die( 'Error found at line no. '.(__LINE__).': '.imw_error()) ;
			$iolCnt		=	imw_num_rows($iolSql) ;
			
			//echo '<pre>';	 print_r($iolData); 
			
			
			$query	=	"Select 
							patient_in_waiting_tbl.*, patient_data_tbl.patient_fname, patient_data_tbl.patient_lname, patient_data_tbl.patient_mname, patient_data_tbl.date_of_birth, patient_data_tbl.imwPatientId,
							DATE_FORMAT(patient_in_waiting_tbl.dos,'%m/%d/%Y') as patient_waiting_dos
							FROM patient_in_waiting_tbl, patient_data_tbl
							WHERE patient_in_waiting_tbl.patient_id=patient_data_tbl.patient_id
							AND patient_in_waiting_tbl.patient_in_waiting_id IN(".$Wid.") 
							ORDER BY patient_in_waiting_tbl.surgeon_fname ASC, surgery_time ASC " ;
			
			$sql		=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).': '.imw_error()) ;
			
			$sxCnt = 0;
			while($row	=	imw_fetch_object($sql) )
			{
				
				$template=	$printTemplate; 
				
				$surgeonName	 =	($row->surgeon_lname)	?	$row->surgeon_lname		:	''	;
				$surgeonName	.=	($row->surgeon_fname)	?	', '.$row->surgeon_fname	:	''	;
				$surgeonName	.=	($row->surgeon_mname)?	'&nbsp;'.$row->surgeon_mname	:	''	;
				$surgeonName	=	trim($surgeonName) ;
				$surgeonName	=	($surgeonName)	?	$surgeonName : '';
				
				$patientName	 =	($row->patient_lname)		?	$row->patient_lname	:	''	;
				$patientName	.=	($row->patient_fname)	?	', '.$row->patient_fname	:	''	;
				$patientName	.=	($row->patient_mname)	?	'&nbsp;'.$row->patient_mname	:	''	;
				$patientName	=	trim($patientName);
				$patientName	=	($patientName)	?	$patientName : '';
				
				$age			=	($row->date_of_birth && $row->date_of_birth <> '0000-00-00')	?	$objManageData->dob_calc($row->date_of_birth)	:	''	;
				$dob			=	($row->date_of_birth && $row->date_of_birth <> '0000-00-00')	?	date('m/d/Y',strtotime($row->date_of_birth)) 	: 	''	 ;
				$dos			=	($row->dos && $row->dos <> '0000-00-00') ?  date('m/d/Y',strtotime($row->dos)) : '' ;	
				$specialNotes	=	($row->comment)	?	$row->comment	:	'' ;
				$patientId		=	($row->patient_id)	?	$row->patient_id	:	0 ;
				$pri_procedure	=	($row->patient_primary_procedure)	?	$row->patient_primary_procedure	:	'' ;
				$sec_procedure	=	($row->patient_secondary_procedure)	?	$row->patient_secondary_procedure	:	'' ;
				$dot			=	($row->surgery_time && $row->surgery_time!='00:00:00')			?	date('h:i A',strtotime($row->surgery_time))		:	'' ;
				$site			=	strtolower($row->site) ;
				if($site === 'left' || $site === 'left upper lid' || $site === 'left lower lid')				$site	=	'OS' ;
				else if($site === 'right' || $site === 'right upper lid' || $site === 'right lower lid')		$site	=	'OD' ;
				else if($site === 'both' || $site === 'bilateral upper lid' || $site === 'bilateral lower lid')	$site	=	'OU' ;
				else						$site 	=	''; 
				
				if($sxCnt>0 && $printSxPlanningSheet=="no") {
					$template='<table cellpadding="0" cellspacing="0" width="100%" >
									<tr><td height="20" style="background-color:#FFFFFF;">&nbsp;</td></tr>
									<tr valign="top" height="20" bgcolor="#F8F9F7" class="text_orangeb" >
										<td  class="text_10b alignCenter"  style="padding-left:5px;  background-image:url('.$bgHeadingImage.');" >Sx Planning Sheet</td>
									</tr>
								</table>'.$template;
				}
				
				
				$imwData		=	Imw($row->dos, $row->patient_in_waiting_id,$row->imwPatientId,$site);
				//echo '<pre>';print_r($imwData);
				$refraction		=	($imwData['refraction'])	?	$imwData['refraction'] 		: '' ;
				$orefraction	=	($imwData['orefraction'])	?	$imwData['orefraction']		: '' ;
				$kFlat			=	($imwData['kFlat'])			?	$imwData['kFlat'] 			: '' ;
				$kSteep			=	($imwData['kSteep'])		?	$imwData['kSteep'] 			: '' ;
				$kAxis			=	($imwData['kAxis'])			?	$imwData['kAxis'] 			: '' ;
				$kCyl			=	($imwData['kCyl'])			?	$imwData['kCyl'] 			: '' ;
				$okFlat			=	($imwData['okFlat'])		?	$imwData['okFlat'] 			: '' ;
				$okSteep		=	($imwData['okSteep'])		?	$imwData['okSteep'] 		: '' ;
				$okAxis			=	($imwData['okAxis'])		?	$imwData['okAxis'] 			: '' ;
				$okCyl			=	($imwData['okCyl'])			?	$imwData['okCyl'] 			: '' ;
				$dominantEye	=	($imwData['dominantEye'])	?	$imwData['dominantEye'] 	: '____' ;
				$pePupilDilated	=	($imwData['pePupilDilated'])?	"Pupil Dilated: ". $imwData['pePupilDilated'] 	: '' ;
				$lensSleSummary	=	($imwData['lensSleSummary'])?	$imwData['lensSleSummary'] 	: '' ;
				$sxPlnCom		=	($imwData['sxPlnCom'])		?	$imwData['sxPlnCom'] 		: '' ;
				$sxPlanningComments	=	($row->sx_planning_comments)?	stripslashes($row->sx_planning_comments) 	: stripslashes($sxPlnCom);
				$sxPlanningCommentsInputBox = '<textarea id="sxPlanningComments'.$row->patient_in_waiting_id.'" name="sxPlanningComments'.$row->patient_in_waiting_id.'"  class="field text1" style="font-family:verdana; border:1px solid #B9B9B9;  height:30px; width:730px; " tabindex="1"  >'.$sxPlanningComments.'</textarea>';
				
				if( $printSxPlanningSheet == "no") $sx_comments = $sxPlanningCommentsInputBox;
				else $sx_comments = $sxPlanningComments;
				// Printing Iol Lenses Detail
				
				$counter	=	0;
				$extraLens=	'';
				$extraSurgeonTechRow = '';
				for( $in = 1; $in < 5; $in++)
				{
					$iolLens		=	$imwData['iolLens'.$in.'AScan'.$site];
					$iolPower		=	$imwData['iol'.$in.'PowerAScan'.$site];
					$iolCyl			=	$imwData['iol'.$in.'CylAScan'.$site];
					$iolAxis		=	$imwData['iol'.$in.'AxisAScan'.$site];
					$iollensUsed	=	$imwData['iol'.$in.'lensUsedAScan'.$site];
					
					if(!array_key_exists($row->patient_in_waiting_id,$iolData))	$iolData[$row->patient_in_waiting_id]=array();
					$data				=	array() ; 
					$data['model']		=	$iolLens ;
					$data['diopter']	=	$iolPower ;
					$data['cyl']		=	$iolCyl ;
					$data['axis']		=	$iolAxis ;
					$data['lens_used']	=	$iollensUsed ;
					
					if($data['model'])
						array_push($iolData[$row->patient_in_waiting_id],$data) ;
				
				}
				
				/*for( $in = 1; $in < 5; $in++)
				{
					$iolLens	=	$imwData['iolLens'.$in.'IolMaster'.$site];
					$iolPower=	$imwData['iol'.$in.'PowerIolMaster'.$site];
					
					if(!array_key_exists($row->patient_in_waiting_id,$iolData))	$iolData[$row->patient_in_waiting_id]=array();
					$data	=	array() ; 
					$data['model']		=	$iolLens ;
					$data['diopter']	=	$iolPower ;
					if($data['model']) 
						array_push($iolData[$row->patient_in_waiting_id],$data) ;
				
				}*/

				while($iolRow	=	imw_fetch_object($iolSql)) 
				{
					if(!array_key_exists($iolRow->patient_in_waiting_id,$iolData))	$iolData[$iolRow->patient_in_waiting_id]=array();
					$data	=	array() ; 
					$data['model']		=	$iolRow->model ;
					$data['diopter']	=	$iolRow->Diopter ;
					$data['cyl']		=	"" ;
					$data['axis']		=	"" ;
					$data['lens_used']	=	"" ;
					
					/*
					REMOVE THIS CODE TO SHOW LENSES FROM "IOLINK_IOL_MANUFACTURE"
					if($iolCnt>0 && $data['model'])
					array_push($iolData[$iolRow->patient_in_waiting_id],$data) ;
					*/
					
				}

				// print"<pre>";
				 //print_r($iolData);
				if(is_array($iolData[$row->patient_in_waiting_id]) && count($iolData[$row->patient_in_waiting_id]) > 0  )
				{
					
					foreach($iolData[$row->patient_in_waiting_id] as $key=>$iolRowData)
					{
						
						$iolLens	=	($iolRowData['model'])		?	$iolRowData['model'] 	: '' ;
						$iolPower	=	($iolRowData['diopter'])	?	$iolRowData['diopter']	: '' ;
						$iolCyl		=	($iolRowData['cyl'])		?	$iolRowData['cyl']	: '' ;
						$iolAxis	=	($iolRowData['axis'])		?	$iolRowData['axis']	: '' ;
						$iolUsed	=	($iolRowData['lens_used'])	?	"Yes"	: '' ;
					
						$field		=	($counter > 0 ) ? 'BACKUP'.$counter : 'PRIMARY'; 
						
						$fromL		=	array('{'.$field.'_LENS}', '{'.$field.'_POWER}','{'.$field.'_CYL}', '{'.$field.'_AXIS}', '{'.$field.'_USED}') ;
						$toL			=	array($iolLens,$iolPower,$iolCyl,$iolAxis,$iolUsed) ;
						if($counter > 3) {
							$extraLens	.=	str_replace($fromL,$toL,str_replace('{COUNTER}',$counter,$LensRow));
							$extraSurgeonTechRow	.=	$surgeonTechRow;
						}
						else {
							$template=	str_replace($fromL,$toL,$template);
						}
					
						//echo 'Extra'.$row->patient_in_waiting_id . $extraLens.'<br>' ;
						//echo 'template' .$row->patient_in_waiting_id. $template.'<br>' ;
						$counter++;	
					}
				}
				
				if($counter < 4) {
					for ($i = $counter ; $i < 4 ; $i++)
					{	
						$iolLens	= 	$iolPower = ''; $iolCyl =	$iolAxis = $iolUsed = '';
						$field		=	($i > 0 ) ? 'BACKUP'.$i: 'PRIMARY'; 
						$fromL		=	array('{'.$field.'_LENS}', '{'.$field.'_POWER}','{'.$field.'_CYL}', '{'.$field.'_AXIS}', '{'.$field.'_USED}') ;
						$toL		=	array($iolLens,$iolPower,$iolCyl,$iolAxis,$iolUsed) ;
						$template	=	str_replace($fromL,$toL,$template);
					}
				}
				// End Printing Iol Lenses Detail
				
				include("common/conDb.php");
				$to				=	array( $patientName, $dob, $age, $patientId, $site, $pri_procedure, $sec_procedure, $dos, $dot, $surgeonName, $extraLens, $specialNotes, $refraction, $orefraction,$kFlat,$kSteep,$kAxis,$kCyl,$okFlat,$okSteep,$okAxis,$okCyl,$dominantEye,$pePupilDilated,$lensSleSummary,$sx_comments,$extraSurgeonTechRow ) ;
				
				$Html			.=	'<page>'.str_replace($from,$to,$template).'</page>';
				
				$sxCnt++;
			}
			
	}

	function Imw($dos,$patientInWaitingId, $imwPatientId,$site)
	{
			$return = array('refraction'			=> '', 'orefraction'			=> '', 'kFlat'					=> '',
							'kSteep'				=> '', 'kAxis'					=> '', 'kCyl'					=> '', 'okFlat'					=> '',
							'okSteep'				=> '', 'okAxis'					=> '', 'okCyl'					=> '', 'dominantEye'			=> '',
							'pePupilDilated'		=> '', 'lensSleSummary'			=> '', 'SX_COMMENTS'					=> '',
							
							'iolLens1AScanOD'		=> '', 'iol1PowerAScanOD'		=> '',	'iolLens2AScanOD'		=> '', 'iol2PowerAScanOD'		=> '',
							'iolLens3AScanOD'		=> '', 'iol3PowerAScanOD'		=> '',	'iolLens4AScanOD'		=> '', 'iol4PowerAScanOD'		=> '',
							'iolLens1AScanOS'		=> '', 'iol1PowerAScanOS'		=> '',	'iolLens2AScanOS'		=> '', 'iol2PowerAScanOS'		=> '',
							'iolLens3AScanOS'		=> '', 'iol3PowerAScanOS'		=> '',	'iolLens4AScanOS'		=> '', 'iol4PowerAScanOS'		=> '',
							
							'iolLens1IolMasterOD'	=> '', 'iol1PowerIolMasterOD'	=> '',	'iolLens2IolMasterOD'	=> '', 'iol2PowerIolMasterOD'	=> '',
							'iolLens3IolMasterOD'	=> '', 'iol3PowerIolMasterOD'	=> '',	'iolLens4IolMasterOD'	=> '', 'iol4PowerIolMasterOD'	=> '',
							'iolLens1IolMasterOS'	=> '', 'iol1PowerIolMasterOS'	=> '',	'iolLens2IolMasterOS'	=> '', 'iol2PowerIolMasterOS'	=> '',
							'iolLens3IolMasterOS'	=> '', 'iol3PowerIolMasterOS'	=> '',	'iolLens4IolMasterOS'	=> '', 'iol4PowerIolMasterOS'	=> ''
							
							);
			if($dos && $patientInWaitingId )
			{
				include('connect_imwemr.php');
				if(!$imwPatientId)
				{
					$querySA	=	"Select sa_patient_id as iDocPatientId, iolink_ocular_chart_form_id From `schedule_appointments` Where iolink_iosync_waiting_id = '".$patientInWaitingId."' " ;	
					$sqlSA		=	imw_query($querySA) or die('Error found at line no. '.(__LINE__).': '.imw_error());
					$cntSA		=	imw_num_rows($sqlSA);
					if($cntSA > 0 )
					{
						$rowSA	=	imw_fetch_object($sqlSA);
						$imwPatientId				=	$rowSA->iDocPatientId;
						$iolink_ocular_chart_form_id	=	$rowSA->iolink_ocular_chart_form_id;
					}
				}
				
				$site		=	strtolower($site);
				$other		=	($site === 'od') ? 'os' :  ($site === 'os' ? 'od' : '') ; 
				$Fields		=	"Cch.dominant,  CONCAT( CASE WHEN cpm_os1.mr_none_given != '' THEN CONCAT(cpm_os1.mr_none_given,',') ELSE '' END, 
														CASE WHEN cpm_os2.mr_none_given != '' THEN CONCAT(cpm_os2.mr_none_given,',') ELSE '' END,
														CASE WHEN cpm_os3.mr_none_given != '' THEN cpm_os3.mr_none_given ELSE '' END 
													 ) as vis_mr_none_given" ;
					
				//$os			=	", vis_mr_os_s, vis_mr_os_c, vis_mr_os_a, vis_mr_os_add, vis_mr_os_given_s, vis_mr_os_given_c, vis_mr_os_given_a, vis_mr_os_given_add, visMrOtherOsS_3, visMrOtherOsC_3, visMrOtherOsA_3, visMrOtherOsAdd_3,vis_ak_os_k, vis_ak_os_slash, vis_ak_os_x ";
				$os = ", cpmv_os1.sph as vis_mr_os_s, cpmv_os1.cyl as vis_mr_os_c, 
					   cpmv_os1.axs as vis_mr_os_a, cpmv_os1.ad as vis_mr_os_add,
					   
					   cpmv_os2.sph as vis_mr_os_given_s, cpmv_os2.cyl as vis_mr_os_given_c, 
					   cpmv_os2.axs as vis_mr_os_given_a, cpmv_os2.ad as vis_mr_os_given_add,

					   cpmv_os3.sph as visMrOtherOsS_3, cpmv_os3.cyl as visMrOtherOsC_3, 
					   cpmv_os3.axs as visMrOtherOsA_3, cpmv_os3.ad as visMrOtherOsAdd_3,

					   cak.k_os as vis_ak_os_k, cak.slash_os as vis_ak_os_slash, cak.x_os as vis_ak_os_x ";

				$od = ", cpmv_od1.sph as vis_mr_od_s, cpmv_od1.cyl as vis_mr_od_c, 
					   cpmv_od1.axs as vis_mr_od_a, cpmv_od1.ad as vis_mr_od_add,
					   
					   cpmv_od2.sph as vis_mr_od_given_s, cpmv_od2.cyl as vis_mr_od_given_c, 
					   cpmv_od2.axs as vis_mr_od_given_a, cpmv_od2.ad as vis_mr_od_given_add,

					   cpmv_od3.sph as visMrOtherOdS_3, cpmv_od3.cyl as visMrOtherOdC_3, 
					   cpmv_od3.axs as visMrOtherOdA_3, cpmv_od3.ad as visMrOtherOdAdd_3,

					   cak.k_od as vis_ak_od_k, cak.slash_od as vis_ak_od_slash, cak.x_od as vis_ak_od_x ";					   

				//$od	=	", vis_mr_od_s, vis_mr_od_c, vis_mr_od_a, vis_mr_od_add, vis_mr_od_given_s, vis_mr_od_given_c, vis_mr_od_given_a, vis_mr_od_given_add, visMrOtherOdS_3, visMrOtherOdC_3, visMrOtherOdA_3, visMrOtherOdAdd_3, vis_ak_od_k, vis_ak_od_slash, vis_ak_od_x" ;
				
				$Fields		=	$Fields . $os . $od ;
				$commonQuery1	=	"SELECT ".$Fields."  
											FROM `chart_master_table` Cmt 
											INNER JOIN  chart_vis_master Cv ON (Cmt.id = Cv.form_id) 
											
											LEFT JOIN chart_pc_mr cpm_os1 ON (Cv.id = cpm_os1.id_chart_vis_master  and cpm_os1.ex_type = 'MR' and cpm_os1.ex_number = '1')
											LEFT JOIN chart_pc_mr_values cpmv_os1 ON (cpm_os1.id = cpmv_os1.chart_pc_mr_id and cpmv_os1.site = 'OS' )
											LEFT JOIN chart_pc_mr_values cpmv_od1 ON (cpm_os1.id = cpmv_od1.chart_pc_mr_id and cpmv_od1.site = 'OD' )

											LEFT JOIN chart_pc_mr cpm_os2 ON (Cv.id = cpm_os2.id_chart_vis_master  and cpm_os2.ex_type = 'MR' and cpm_os2.ex_number = '2')
											LEFT JOIN chart_pc_mr_values cpmv_os2 ON (cpm_os2.id = cpmv_os2.chart_pc_mr_id and cpmv_os2.site = 'OS' )
											LEFT JOIN chart_pc_mr_values cpmv_od2 ON (cpm_os2.id = cpmv_od2.chart_pc_mr_id and cpmv_od2.site = 'OD' )

											LEFT JOIN chart_pc_mr cpm_os3 ON (Cv.id = cpm_os3.id_chart_vis_master  and cpm_os3.ex_type = 'MR' and cpm_os3.ex_number = '3')
											LEFT JOIN chart_pc_mr_values cpmv_os3 ON (cpm_os3.id = cpmv_os3.chart_pc_mr_id and cpmv_os3.site = 'OS' )
											LEFT JOIN chart_pc_mr_values cpmv_od3 ON (cpm_os3.id = cpmv_od3.chart_pc_mr_id and cpmv_od3.site = 'OD' )
		
											LEFT JOIN chart_ak cak ON (Cv.id = cak.id_chart_vis_master)
											LEFT JOIN chart_left_cc_history Cch
											ON Cmt.id = Cch.form_id
											WHERE Cmt.id != '0'
											AND Cmt.patient_id = '".$imwPatientId."' " ;	
				
				$commonQuery2 = " ORDER By Cmt.date_of_service DESC LIMIT 0, 1 " ;
				
				$query		=	$commonQuery1.($iolink_ocular_chart_form_id ? " AND Cmt.id = '".$iolink_ocular_chart_form_id."' " : '').$commonQuery2; 
												
				$sql		=	imw_query($query) or die('Error found at line no. '.(__LINE__).': '.imw_error());
				if(imw_num_rows($query)<=0) {	
					$query	=	$commonQuery1." AND Cmt.date_of_service <= '".$dos."' ".$commonQuery2; 
					$sql	=	imw_query($query) or die('Error found at line no. '.(__LINE__).': '.imw_error());
				}
				$row		=	imw_fetch_object($sql);
				//echo $query; 
					
				$od = $os = '&nbsp;';
				if(stristr($row->vis_mr_none_given,'MR 3'))
				{
					$od	.=	($row->visMrOtherOdS_3		?	$row->visMrOtherOdS_3		: '____').'&nbsp;&nbsp;&nbsp;' ;
					$od	.=	($row->visMrOtherOdC_3		?	$row->visMrOtherOdC_3		: '____') .'&nbsp;&nbsp;&nbsp;x&nbsp;&nbsp;';
					$od	.=	($row->visMrOtherOdA_3		?	$row->visMrOtherOdA_3		: '____').'&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;';
					$od	.=	($row->visMrOtherOdAdd_3)	?	$row->visMrOtherOdAdd_3		: '____' ;
					
					
					$os	.=	($row->visMrOtherOsS_3		?	$row->visMrOtherOsS_3		: '____').'&nbsp;&nbsp;&nbsp;' ;
					$os	.=	($row->visMrOtherOsC_3		?	$row->visMrOtherOsC_3		: '____') .'&nbsp;&nbsp;x&nbsp;&nbsp;';
					$os	.=	($row->visMrOtherOsA_3		?	$row->visMrOtherOsA_3		: '____').'&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;';
					$os	.=	($row->visMrOtherOsAdd_3)	?	$row->visMrOtherOsAdd_3		: '____' ;
						
					
						
				}
				elseif(stristr($row->vis_mr_none_given,'MR 2'))
				{
					$od	 =	($row->vis_mr_od_given_s	?	$row->vis_mr_od_given_s		: '____').'&nbsp;&nbsp;&nbsp;' ;
					$od	.=	($row->vis_mr_od_given_c	?	$row->vis_mr_od_given_c		: '____') .'&nbsp;&nbsp;x&nbsp;&nbsp;';
					$od	.=	($row->vis_mr_od_given_a	?	$row->vis_mr_od_given_a		: '____').'&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;';
					$od	.=	($row->vis_mr_od_given_add)	?	$row->vis_mr_od_given_add	: '____' ;
					
					$os	.=	($row->vis_mr_os_given_s	?	$row->vis_mr_os_given_s		: '____').'&nbsp;&nbsp;&nbsp;' ;
					$os	.=	($row->vis_mr_os_given_c	?	$row->vis_mr_os_given_c		: '____') .'&nbsp;&nbsp;x&nbsp;&nbsp;';
					$os	.=	($row->vis_mr_os_given_a	?	$row->vis_mr_os_given_a		: '____').'&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;';
					$os	.=	($row->vis_mr_os_given_add)	?	$row->vis_mr_os_given_add 	: '____' ;
					
						
				}
				elseif(stristr($row->vis_mr_none_given,'MR 1'))
				{
					$od	 =	($row->vis_mr_od_s			?	$row->vis_mr_od_s			: '____').'&nbsp;&nbsp;&nbsp;' ;
					$od	.=	($row->vis_mr_od_c			?	$row->vis_mr_od_c			: '____') .'&nbsp;&nbsp;x&nbsp;&nbsp;';
					$od	.=	($row->vis_mr_od_a			?	$row->vis_mr_od_a			: '____').'&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;';
					$od	.=	($row->vis_mr_od_add)		?	$row->vis_mr_od_add			: '____' ;
					
					$os	.=	($row->vis_mr_os_s			?	$row->vis_mr_os_s			: '____').'&nbsp;&nbsp;&nbsp;' ;
					$os	.=	($row->vis_mr_os_c			?	$row->vis_mr_os_c			: '____') .'&nbsp;&nbsp;x&nbsp;&nbsp;';
					$os	.=	($row->vis_mr_os_a			?	$row->vis_mr_os_a			: '____').'&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;';
					$os	.=	($row->vis_mr_os_add)		?	$row->vis_mr_os_add			: '____' ;
					
					
				}
				$return['refraction'] 	= $$site ;
				$return['orefraction']	= $$other ;
				
				
				$kFlat_od=$kSteep_od=$kAxis_od=$kCyl_od = $kFlat_os=$kSteep_os=$kAxis_os=$kCyl_os = '';
				if($row->vis_ak_od_k || $row->vis_ak_od_slash || $row->vis_ak_od_x)
				{
						$kFlat_od		=	($row->vis_ak_od_k			?	$row->vis_ak_od_k		: '');
						$kSteep_od		=	($row->vis_ak_od_slash		?	$row->vis_ak_od_slash 	: '');
						$kAxis_od		=	' x ' .($row->vis_ak_od_x	?	$row->vis_ak_od_x 		: '');
				}
					
				if($row->vis_ak_os_k || $row->vis_ak_os_slash || $row->vis_ak_os_x)
				{
						$kFlat_os		=	($row->vis_ak_os_k			?	$row->vis_ak_os_k		: '');
						$kSteep_os		=	($row->vis_ak_os_slash		?	$row->vis_ak_os_slash 	: '');
						$kAxis_os		=	' x ' .($row->vis_ak_os_x	?	$row->vis_ak_os_x 		: '');
				}
				$kFlat 					=	'kFlat_'.$site; 
				$kSteep					=	'kSteep_'.$site;
				$kAxis					=	'kAxis_'.$site;
				$kCyl					=	'kCyl_'.$site; 
				$okFlat 				=	'kFlat_'.$other; 
				$okSteep				=	'kSteep_'.$other;
				$okAxis					=	'kAxis_'.$other;
				$okCyl					=	'kCyl_'.$other; 
				
				
				$return['kFlat'] 		=	$$kFlat; 
				$return['kSteep']		=	$$kSteep; 
				$return['kAxis']		=	$$kAxis; 
				$return['kCyl']			=	$$kCyl; 
				
				$return['okFlat'] 		=	$$okFlat; 
				$return['okSteep']		=	$$okSteep; 
				$return['okAxis']		=	$$okAxis; 
				$return['okCyl']		=	$$okCyl; 
				
				$dominantEye			=	($row->dominant) ? $row->dominant : '' ;
				$return['dominantEye']	=	$dominantEye ;
			
			
				//START GET IOL LENSES FROM ASCAN 
				$iolinkAscanCommonQry1 = " SELECT 
										lt1.lenses_iol_type AS iolLens1AScanOD, st.iol1PowerOD AS iol1PowerAScanOD,
										lt2.lenses_iol_type AS iolLens2AScanOD, st.iol2PowerOD AS iol2PowerAScanOD,
										lt3.lenses_iol_type AS iolLens3AScanOD, st.iol3PowerOD AS iol3PowerAScanOD,
										lt4.lenses_iol_type AS iolLens4AScanOD, st.iol4PowerOD AS iol4PowerAScanOD,
										
										lt5.lenses_iol_type AS iolLens1AScanOS, st.iol1PowerOS AS iol1PowerAScanOS,
										lt6.lenses_iol_type AS iolLens2AScanOS, st.iol2PowerOS AS iol2PowerAScanOS,
										lt7.lenses_iol_type AS iolLens3AScanOS, st.iol3PowerOS AS iol3PowerAScanOS,
										lt8.lenses_iol_type AS iolLens4AScanOS, st.iol4PowerOS AS iol4PowerAScanOS
									
									FROM surgical_tbl st
									LEFT JOIN lenses_iol_type lt1 ON(lt1.iol_type_id = st.iol1OD)
									LEFT JOIN lenses_iol_type lt2 ON(lt2.iol_type_id = st.iol2OD)
									LEFT JOIN lenses_iol_type lt3 ON(lt3.iol_type_id = st.iol3OD)
									LEFT JOIN lenses_iol_type lt4 ON(lt4.iol_type_id = st.iol4OD)
									
									LEFT JOIN lenses_iol_type lt5 ON(lt5.iol_type_id = st.iol1OS)
									LEFT JOIN lenses_iol_type lt6 ON(lt6.iol_type_id = st.iol2OS)
									LEFT JOIN lenses_iol_type lt7 ON(lt7.iol_type_id = st.iol3OS)
									LEFT JOIN lenses_iol_type lt8 ON(lt8.iol_type_id = st.iol4OS)
									
									WHERE st.patient_id = '".$imwPatientId."'";
				
				$iolinkAscanCommonQry2 = " ORDER BY st.examDate DESC, st.surgical_id DESC LIMIT 0,1 ";
									
				$iolinkAscanQry		=	$iolinkAscanCommonQry1." AND st.form_id = '".$iolink_ocular_chart_form_id."'  AND st.form_id != '0' ".$iolinkAscanCommonQry2; 
				
				
				//start
				$andMnkEyeQry = "";
				if(trim($site)) {
					$andMnkEyeQry = " AND mank_eye = '".$site."' ";	
				}
				$sxPlanSheetQry = "SELECT id, pe_pupil_dilated, lens_sle_summary,mank_eye,surgeon_id,sx_pln_com FROM chart_sx_plan_sheet 
									WHERE sx_plan_dos <= '".$dos."' AND patient_id = '".$imwPatientId."' AND del_status = '0' 
									".$andMnkEyeQry."
									ORDER BY sx_plan_dos DESC
									LIMIT 0,1";
				$sxPlanSheetRes	 	= imw_query($sxPlanSheetQry) or die(imw_error($sxPlanSheetQry));					
				if(imw_num_rows($sxPlanSheetRes)>0) {
					$sxPlanSheetRow = imw_fetch_assoc($sxPlanSheetRes);	
					$idChartSxPlanSheet = $sxPlanSheetRow["id"];
					$pePupilDilated = $sxPlanSheetRow["pe_pupil_dilated"];
					$lensSleSummary = $sxPlanSheetRow["lens_sle_summary"];
					$sxPlnCom 		= $sxPlanSheetRow["sx_pln_com"];
					$lensSurgeonId = $sxPlanSheetRow["surgeon_id"];
					$return["pePupilDilated"] = $pePupilDilated;
					$return["lensSleSummary"] = $lensSleSummary;
					$return["sxPlnCom"] 	  = $sxPlnCom;
					
					$iolinkAscanCommonQry1 = "SELECT csl . * , csa.indx
												FROM chart_sps_lens csl
												INNER JOIN chart_sps_ast_plan_tpa csa ON(csa.lens_type=csl.lens_type AND csa.id_chart_sx_plan_sheet = '".$idChartSxPlanSheet."' AND csa.prov_id = '".$lensSurgeonId."') 
												WHERE csl.id_chart_sx_plan_sheet = '".$idChartSxPlanSheet."' ";
					$iolinkAscanCommonQry2 = "	ORDER BY csa.indx LIMIT 0 , 4 ";
					$iolinkAscanQry = $iolinkAscanCommonQry1.$andCspQry.$iolinkAscanCommonQry2;
					//echo $iolinkAscanQry;
					//end
					
					$iolinkAscanRes	 	= imw_query($iolinkAscanQry) or die(imw_error($iolinkAscanQry));
					$iolinkAscanNumRow 	= imw_num_rows($iolinkAscanRes);
					/*
					if($iolinkAscanNumRow<=0) {
						$iolinkAscanQry = $iolinkAscanCommonQry1.$iolinkAscanCommonQry2;
						$iolinkAscanRes = imw_query($iolinkAscanQry) or die(imw_error());
						$iolinkAscanNumRow 	= imw_num_rows($iolinkAscanRes);
					}*/
					//echo '<br><br>'.$iolinkAscanQry;
					if($iolinkAscanNumRow>0) {
						$cntr = 1;
						$idChartSxPlanSheetTmp = "";
						while($iolinkAscanRow = imw_fetch_assoc($iolinkAscanRes)) {
							if($cntr==1) {
								$idChartSxPlanSheetTmp = $iolinkAscanRow["id_chart_sx_plan_sheet"];
							}
							$idChartSxPlanSheet = $iolinkAscanRow["id_chart_sx_plan_sheet"];
							$siteUpper 			= strtoupper($site);
							if($idChartSxPlanSheet = $idChartSxPlanSheetTmp) {
								//echo '<br>hlo'.$cntr.' '."iolLens".$cntr."AScan".$site;echo'<pre>';print_r($iolinkAscanRow);
								$return["iolLens".$cntr."AScan".$siteUpper] 	= $iolinkAscanRow["lens_name"];
								$return["iol".$cntr."PowerAScan".$siteUpper] 	= $iolinkAscanRow["lens_pwr"];
								$return["iol".$cntr."CylAScan".$siteUpper] 	= $iolinkAscanRow["lens_cyl"];
								$return["iol".$cntr."AxisAScan".$siteUpper] 	= $iolinkAscanRow["lens_axis"];
								$return["iol".$cntr."lensUsedAScan".$siteUpper] 	= $iolinkAscanRow["lens_used"];
								
								$cntr++;
							}
							
							/*
							$return["iolLens2AScan".$site] 	= $iolinkAscanRow["iolLens2AScanOD"];
							$return["iol2PowerAScan".$site] = $iolinkAscanRow["iol2PowerAScanOD"];
							$return["iolLens3AScan".$site] 	= $iolinkAscanRow["iolLens3AScanOD"];
							$return["iol3PowerAScan".$site] = $iolinkAscanRow["iol3PowerAScanOD"];
							$return["iolLens4AScan".$site] 	= $iolinkAscanRow["iolLens4AScanOD"];
							$return["iol4PowerAScan".$site] = $iolinkAscanRow["iol4PowerAScanOD"];
							
							$return["iolLens1AScanOS"] 	= $iolinkAscanRow["iolLens1AScanOS"];
							$return["iol1PowerAScanOS"] = $iolinkAscanRow["iol1PowerAScanOS"];
							$return["iolLens2AScanOS"] 	= $iolinkAscanRow["iolLens2AScanOS"];
							$return["iol2PowerAScanOS"] = $iolinkAscanRow["iol2PowerAScanOS"];
							$return["iolLens3AScanOS"] 	= $iolinkAscanRow["iolLens3AScanOS"];
							$return["iol3PowerAScanOS"] = $iolinkAscanRow["iol3PowerAScanOS"];
							$return["iolLens4AScanOS"] 	= $iolinkAscanRow["iolLens4AScanOS"];
							$return["iol4PowerAScanOS"] = $iolinkAscanRow["iol4PowerAScanOS"];
							*/
						}
						//echo'<pre>';print_r($return);
					}
					//END GET IOL LENSES FROM ASCAN 
				}
				
				//START GET IOL LENSES FROM IOL_MASTER 
				/*
				$iolinkIolMasterCommonQry1 = " SELECT 
                                            lt1.lenses_iol_type AS iolLens1IolMasterOD, imt.iol1PowerOD AS iol1IolMasterPowerOD,
                                            lt2.lenses_iol_type AS iolLens2IolMasterOD, imt.iol2PowerOD AS iol2IolMasterPowerOD,
                                            lt3.lenses_iol_type AS iolLens3IolMasterOD, imt.iol3PowerOD AS iol3IolMasterPowerOD,
                                            lt4.lenses_iol_type AS iolLens4IolMasterOD, imt.iol4PowerOD AS iol4IolMasterPowerOD,
                                            
                                            lt5.lenses_iol_type AS iolLens1IolMasterOS, imt.iol1PowerOS AS iol1PowerIolMasterOS,
                                            lt6.lenses_iol_type AS iolLens2IolMasterOS, imt.iol2PowerOS AS iol2PowerIolMasterOS,
                                            lt7.lenses_iol_type AS iolLens3IolMasterOS, imt.iol3PowerOS AS iol3PowerIolMasterOS,
                                            lt8.lenses_iol_type AS iolLens4IolMasterOS, imt.iol4PowerOS AS iol4PowerIolMasterOS
                                        
                                        FROM iol_master_tbl imt
                                        LEFT JOIN lenses_iol_type lt1 ON(lt1.iol_type_id = imt.iol1OD)
                                        LEFT JOIN lenses_iol_type lt2 ON(lt2.iol_type_id = imt.iol2OD)
                                        LEFT JOIN lenses_iol_type lt3 ON(lt3.iol_type_id = imt.iol3OD)
                                        LEFT JOIN lenses_iol_type lt4 ON(lt4.iol_type_id = imt.iol4OD)
                                        
                                        LEFT JOIN lenses_iol_type lt5 ON(lt5.iol_type_id = imt.iol1OS)
                                        LEFT JOIN lenses_iol_type lt6 ON(lt6.iol_type_id = imt.iol2OS)
                                        LEFT JOIN lenses_iol_type lt7 ON(lt7.iol_type_id = imt.iol3OS)
                                        LEFT JOIN lenses_iol_type lt8 ON(lt8.iol_type_id = imt.iol4OS)
                                        
                                        WHERE imt.patient_id = '".$imwPatientId."'";				
				
				$iolinkIolMasterCommonQry2 = " ORDER BY imt.iol_master_id DESC LIMIT 0,1 ";				
				
				$iolinkIolMasterQry			= $iolinkIolMasterCommonQry1." AND imt.form_id = '".$iolink_ocular_chart_form_id."' AND imt.form_id != '0' ".$iolinkIolMasterCommonQry2; 
				$iolinkIolMasterRes 		= imw_query($iolinkIolMasterQry);
				$iolinkIolMasterNumRow 		= imw_num_rows($iolinkIolMasterRes);
				if($iolinkIolMasterNumRow<=0) {
					$iolinkIolMasterQry		= $iolinkIolMasterCommonQry1." AND imt.examDate <= '".$dos."' ".$iolinkIolMasterCommonQry2; 
					$iolinkIolMasterRes 	= imw_query($iolinkIolMasterQry);
					$iolinkIolMasterNumRow 	= imw_num_rows($iolinkIolMasterRes);
				}
				if($iolinkIolMasterNumRow>0) {
					$iolinkIolMasterRow = imw_fetch_assoc($iolinkIolMasterRes);
					
					$return["iolLens1IolMasterOD"] 	= $iolinkIolMasterRow["iolLens1IolMasterOD"];
					$return["iol1PowerIolMasterOD"] = $iolinkIolMasterRow["iol1PowerIolMasterOD"];
					$return["iolLens2IolMasterOD"] 	= $iolinkIolMasterRow["iolLens2IolMasterOD"];
					$return["iol2PowerIolMasterOD"] = $iolinkIolMasterRow["iol2PowerIolMasterOD"];
					$return["iolLens3IolMasterOD"] 	= $iolinkIolMasterRow["iolLens3IolMasterOD"];
					$return["iol3PowerIolMasterOD"] = $iolinkIolMasterRow["iol3PowerIolMasterOD"];
					$return["iolLens4IolMasterOD"] 	= $iolinkIolMasterRow["iolLens4IolMasterOD"];
					$return["iol4PowerIolMasterOD"] = $iolinkIolMasterRow["iol4PowerIolMasterOD"];
					
					$return["iolLens1IolMasterOS"] 	= $iolinkIolMasterRow["iolLens1IolMasterOS"];
					$return["iol1PowerIolMasterOS"] = $iolinkIolMasterRow["iol1PowerIolMasterOS"];
					$return["iolLens2IolMasterOS"] 	= $iolinkIolMasterRow["iolLens2IolMasterOS"];
					$return["iol2PowerIolMasterOS"] = $iolinkIolMasterRow["iol2PowerIolMasterOS"];
					$return["iolLens3IolMasterOS"] 	= $iolinkIolMasterRow["iolLens3IolMasterOS"];
					$return["iol3PowerIolMasterOS"] = $iolinkIolMasterRow["iol3PowerIolMasterOS"];
					$return["iolLens4IolMasterOS"] 	= $iolinkIolMasterRow["iolLens4IolMasterOS"];
					$return["iol4PowerIolMasterOS"] = $iolinkIolMasterRow["iol4PowerIolMasterOS"];
					
					
				}
				//END GET IOL LENSES FROM IOL_MASTER
				*/ 				
			}
			
			return $return ;
	}
	
?>