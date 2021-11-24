<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
session_start();
include_once("common/conDb.php");
include("common/linkfile.php");
include_once("admin/classObjectFunction.php"); 
include("common_functions.php");
$objManageData = new manageData;
$pconfId= $_REQUEST['pConfId'];
if(!$pconfId) {
	$pconfId= $_SESSION['pConfId'];
}	
include_once("new_header_print.php");
//echo $patient_id = $_SESSION['patient_id'];

$Qry="select *, date_format(signSurgeon1DateTime,'%m-%d-%Y %h:%i %p') as signSurgeon1DateTimeFormat from dischargesummarysheet where confirmation_id= $pconfId";
$recordQry=imw_query($Qry);
$i = 0;
$DischargeResult=imw_fetch_array($recordQry);

$date= $DischargeResult['signSurgeon1DateTime']; 
$date_surgeon=explode(' ',$date);
$date_sign=explode('-',$date_surgeon[0]);
$date_surgeon_sign= $date_sign[1].'/'.$date_sign[2].'/'.$date_sign[0];

$procedures_code_list= $DischargeResult['procedures_code'];
$disSummryFormStatus= $DischargeResult['form_status'];
$procedures_codes= array_filter(explode(',',$procedures_code_list)); 
$disclaimer_txt = $DischargeResult['disclaimer_txt'];
$diag_ids_list=$DischargeResult['diag_ids'];
$diag_ids=explode(',',$diag_ids_list);
$diag_namesArr=explode('@@',$DischargeResult['diag_names']);
foreach($diag_ids as $_key => $diagID)
{
	$diag_names[$diagID] = $diag_namesArr[$_key];	
}
	
$diagids_length=count($diag_ids);

//START GETTING ICD10
$icd10_id_length = 0;
$icd10_code = $icd10_id = array();
if($DischargeResult['icd10_id']) {
	$icd10_code  = explode(',',$DischargeResult['icd10_code']);
	$icd10_id = explode(',',$DischargeResult['icd10_id']);
	$icd10_nameArr = explode('@@',$DischargeResult['icd10_name']);
	$icd10_id_length=count($icd10_id);

	foreach($icd10_id as $_key => $val)
	{
		$icd10_name[$val]	= $icd10_nameArr[$_key];
	}
	
}

//END GETTING ICD10

$procedures_list= $DischargeResult['procedures_name'];
if($procedures_list){
	$procedures =explode(',',$procedures_list);
}

$procNameArray = $procCodeNameArray = array();
$procedures_nameDB	=	$DischargeResult['procedures_name'];
$procedures_codeDB	=	$DischargeResult['procedures_code_name'];
$procNameExplode	=	array_filter(explode("!,!",$procedures_nameDB));
$procCodeNameExplode=	array_filter(explode("##",$procedures_codeDB));

if(is_array($procedures_codes) && count($procedures_codes) > 0)
{
	foreach($procedures_codes as $_key=>$_val)	
	{
		$procNameArray[$_val]		=	trim($procNameExplode[$_key]);
		$procCodeNameArray[$_val]	=	trim($procCodeNameExplode[$_key]);
	}
}
				
		
$procedures_length= count($procedures_codes);
$qry_procedure_category="Select proceduresCategoryId,name from procedurescategory";
	$res_procedure_category=imw_query($qry_procedure_category);
while($rowProc=imw_fetch_assoc($res_procedure_category)){
	$proc_id=$rowProc['proceduresCategoryId'];
	$procNameArr[$proc_id]=$rowProc['name'];
	
}
//print_r($DischargeResult);
$surgeon=$DischargeResult['signSurgeon1LastName'].','.$DischargeResult['signSurgeon1FirstName'];
$tableDSummery.=$head_table."<br>";
$tableDSummery.='
	<table style="width:740px;border:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
		<tr>
			<td colspan="2" style="width:740px;" class="fheader">Discharge Summary Sheet</td>
		</tr>
		<tr>
			<td style="width:370px;" class="bdrbtm">
				<table style="width:370px; border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
					';
				if($procedures_length>0 && $procedures_codes[0]){		
					$procInsertArr=array();
					for($len=0;$len<$procedures_length;$len++)
					{
						$procedureQry="select * from procedures where procedureId = $procedures_codes[$len]";
						$proced= imw_query($procedureQry);
						$procedurelisting=@imw_fetch_array($proced);
						$procedurelistingNumRows=@imw_num_rows($proced);
						
						
						if($procNameArray[$procedurelisting['procedureId']] && $procedurelisting['name'] <> $procNameArray[$procedurelisting['procedureId']])
						{
							$procedurelisting['name'] = $procNameArray[$procedurelisting['procedureId']];
						}
						if($procCodeNameArray[$procedurelisting['procedureId']] && $procedurelisting['code'] <> $procCodeNameArray[$procedurelisting['procedureId']])
						{
							$procedurelisting['code'] = $procCodeNameArray[$procedurelisting['procedureId']];
						}
						
						$procedurecode[] = $procedurelisting['code'];
						$procedurename[] = $procedurelisting['name'];
						$procedureCatId[]= $procedurelisting['catId'];
						
						if($procNameArr[$procedureCatId[$len]] && !in_array($procedureCatId[$len],$procInsertArr)){
							$tableDSummery.='
								<tr>
									<td colspan="3" class="bdrbtm bgcolor bold">'.$procNameArr[$procedureCatId[$len]].'</td>
								</tr>
							';	
						}
						$procInsertArr[]=$procedureCatId[$len];
				$tableDSummery.=
					'<tr>	
						<td style="width:100px;" class="bdrbtm pl5">'.wordwrap($procedurecode[$len],12,"<br>",1).'</td>
						<td style="width:210px;" class="bdrbtm pl5">'.$procedurename[$len].'</td>
						<td style="width:30px;" class="bdrbtm pl5">Yes</td>
					</tr>';
					}
				}else{
					$tableDSummery.=
					'
					<tr>	
						<td colspan="3" class="bdrbtm pl5 bold bgcolor">Procedures</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>
					<tr>	
						<td style="width:100px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:210px;" class="bdrbtm pl5">&nbsp;</td>
						<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
					</tr>';
				}
				if(stripslashes($DischargeResult['otherMiscellaneous'])){
					$tableDSummery.='
						<tr>
							<td style="width:100px;" class="bdrbtm pl5">Other:</td>
							<td colspan="2" style="width:240px;" class="bdrbtm pl5">'.stripslashes($DischargeResult['otherMiscellaneous']).'</td>
						</tr>	
					';
				}
				if(stripslashes($DischargeResult['comment'])){
					$tableDSummery.='
						<tr>
							<td style="width:100px;" class="bdrbtm pl5">Comments:</td>
							<td colspan="2" style="width:240px;" class="bdrbtm pl5">'.stripslashes($DischargeResult['comment']).'</td>
						</tr>	
					';
				}
				 
				
				$tableDSummery.=	
				'</table>	
			</td>
			<td valign="top" style="width:370px; vertical-align:text-top;" class="bdrbtm" >
				<table valign="top" style="width:370px; border-left:1px solid #C0C0C0;border-right:1px solid #C0C0C0;" cellpadding="0" cellspacing="0">
					<tr>
						<td colspan="3" class="bold pl5 bdrbtm bgcolor">Diagnosis</td>
					</tr>';
					if($diagids_length>0 && $diag_ids[0]){
						for($c=0;$c<$diagids_length;$c++){
							$diagQry ="select * from diagnosis_tbl where diag_id= $diag_ids[$c]";
							$resDiag=imw_query( $diagQry);
							$diagnosis=@imw_fetch_array($resDiag);
							$diagcodes=$diagnosis['diag_code'];
							$diagcodeslist=explode(',',$diagcodes);
							
							if($diag_names[$diagcodeslist[0]] && $diagcodeslist[1] <> $diag_names[$diagcodeslist[0]])
							{
								$diagcodeslist[1] = $diag_names[$diagcodeslist[0]];	
							}
							$diagcode[]= $diagcodeslist[0];
							$diagdesc[]= $diagcodeslist[1];
							if($diagcodes!=''){ 
								$tableDSummery.=
								'<tr>	
									<td style="width:60px;" class="bdrbtm pl5">'.wordwrap($diagcode[$c],12,"<br>",1).'</td>
									<td style="width:240px;" class="bdrbtm pl5">'.wordwrap($diagdesc[$c],30,"<br>",1).'</td>
									<td style="width:30px;" class="bdrbtm pl5">Yes</td>
								</tr>';
							}
						}
					}else if($icd10_id_length>0){
						
						$icd10Qry ="select id, icd10_desc from icd10_data where id IN(".$DischargeResult['icd10_id'].")";
						$icd10Res = imw_query($icd10Qry) or die($icd10Qry.imw_error());
						while($icd10Row = imw_fetch_assoc($icd10Res)) {
							$db_icd10_id = $icd10Row['id'];	
							$db_icd10_desc = $icd10Row['icd10_desc'];
							if($icd10_name[$db_icd10_id] && $db_icd10_desc <> $icd10_name[$db_icd10_id])
							{
								$db_icd10_desc = $icd10_name[$db_icd10_id];
							}
							$db_icd10_desc_arr[$db_icd10_id] = $db_icd10_desc;
						}
						
						for($c=0;$c<$icd10_id_length;$c++){
							$tempCode = implode(", " ,explode("@@",$icd10_code[$c]));
							$tableDSummery.=
							'<tr>	
								<td style="width:60px;" class="bdrbtm pl5">'.wordwrap($tempCode,12,"<br>",1).'</td>
								<td style="width:240px;" class="bdrbtm pl5">'.wordwrap($db_icd10_desc_arr[$icd10_id[$c]],30,"<br>",1).'</td>
								<td style="width:30px;" class="bdrbtm pl5">Yes</td>
							</tr>';
						}
					}else{
						$tableDSummery.=
						'<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>
						<tr>	
							<td style="width:60px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:240px;" class="bdrbtm pl5">&nbsp;</td>
							<td style="width:30px;" class="bdrbtm pl5">&nbsp;</td>
						</tr>';	
					}
					 $disAttached = $DischargeResult["disAttached"];
					$dis_ScanUpload = $DischargeResult["dis_ScanUpload"];
					$dis_ScanUpload2 = $DischargeResult["dis_ScanUpload2"];
					if($disAttached=='Yes'){
						$tableDSummery.='
							<tr>
								<td colspan="2" class="bdrbtm">See attached discharge Summary</td>
								<td style="width:30px;" class="bdrbtm pl5 cbold">Yes</td>
							</tr>
						';
						if($dis_ScanUpload!='' || $dis_ScanUpload2!=''){
						$tableDSummery.='
							<tr>
								<td colspan="3" class="bdrbtm pl5 cbold">Attached Discharge Summary</td>
							</tr>';
						$tableDSummery.='
							<tr>
								<td colspan="3" class="bdrbtm pl5 cbold" style="width:350px;">
									<table style="width:350px;" cellpadding="0" cellspacing="0">
										<tr>';
										if($dis_ScanUpload!='') {
											$bakImgResourceDischarge = imagecreatefromstring($dis_ScanUpload);
											imagejpeg($bakImgResourceDischarge,'html2pdfnew/disSummarySheet.jpg');
											$tableDSummery.='<td style="width:175px; text-align:center;"><img src="../html2pdfnew/disSummarySheet.jpg" style="height:100px;width:100px; border:1px solid #C0C0C0;"></td>';
										}
										if($dis_ScanUpload2!='') {
											$bakImgResourceDischarge1 = imagecreatefromstring($dis_ScanUpload2);
											imagejpeg($bakImgResourceDischarge1,'html2pdfnew/disSummarySheet1.jpg');
											$tableDSummery.='<td style="width:175px; text-align:center; border:1px solid #C0C0C0;"><img src="../html2pdfnew/disSummarySheet1.jpg" style="height:100px;width:100px; float:left;"></td>';
										}
								$tableDSummery.='			
										</tr>	
									</table>
								</td>		
							</tr>';
						}
					}
					if(stripslashes($DischargeResult['other1'])){
						$tableDSummery.='
						<tr>
							<td style="width:60px;" class="bdrbtm">Other1:</td>
							<td colspan="2" class="bdrbtm" style="width:270px;">'.stripslashes($DischargeResult['other1']).'</td>	
						</tr>';
					}
					if(stripslashes($DischargeResult['other2'])){
						$tableDSummery.='
						<tr>
							<td style="width:60px;" class="bdrbtm">Other2:</td>
							<td colspan="2" style="width:270px;" class="bdrbtm">'.stripslashes($DischargeResult['other2']).'</td>	
						</tr>';
					}
					//START GET ALLERGIES VALUE
					$allergyQry = "Select * from patient_allergies_tbl where patient_confirmation_id='".$pconfId."'";
					$allergyRes = imw_query($allergyQry);
					$allergyNumRow = @imw_num_rows($allergyRes);	
					if($allergyNumRow>0){
						$tableDSummery.='
							<tr>
								<td colspan="3" class="cbold bdrbtm bgcolor">Allergies/Drug Reaction</td>
							</tr>
							<tr>
								<td colspan="3" style="width:350px;">
									<table style="width:350px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:170px;" class="bdrbtm bold pl5">Name</td>
											<td style="width:170px;" class="bdrbtm bold pl5">Reaction</td>
										</tr>';
										while($allergyRow=imw_fetch_assoc($allergyRes)){
											$allergyNameShow = $allergyRow['allergy_name'];
											$reactionShow	 = $allergyRow['reaction_name'];
										$tableDSummery.='
										<tr>
											<td style="width:170px; " class="bdrbtm pl5">'.htmlentities($allergyNameShow).'</td>
											<td style="width:170px;border-left:1px solid #C0C0C0;" class="bdrbtm pl5">'.htmlentities($reactionShow).'</td>	
										</tr>';
										}
									$tableDSummery.='	
									</table>
								</td>
							</tr>
						';	
					}
					
					
					
					
					
					//START IOL SCAN UPLOAD IMAGE
					$ViewOpRoomRecordQry = "select * from `operatingroomrecords` where  confirmation_id = '".$pconfId."'";
					$ViewOpRoomRecordRes = imw_query($ViewOpRoomRecordQry) or die(imw_error()); 
					$ViewOpRoomRecordNumRow = imw_num_rows($ViewOpRoomRecordRes);
					$ViewOpRoomRecordRow = imw_fetch_array($ViewOpRoomRecordRes); 
					$operatingRoomRecordsId = $ViewOpRoomRecordRow["operatingRoomRecordsId"];
					$iol_ScanUpload = $ViewOpRoomRecordRow["iol_ScanUpload"];
					$iol_ScanUpload2 = $ViewOpRoomRecordRow["iol_ScanUpload2"];
					if($ViewOpRoomRecordNumRow>0){
						if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
							$tableDSummery.='
								<tr>
									<td style="width:350px;" colspan="3" class="bdrbtm cbold">IOL Scanned Image</td>
								</tr>
								<tr>
									<td colspan="3">
										<table style="width:350px;" cellpadding="0" cellspacing="0">
											<tr>
								';
								if($iol_ScanUpload!=''){
									$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
									imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
									if(file_exists("html2pdfnew/oproom.jpg")){
										$tableDSummery.='
												<td style="width:175px;text-align:center; border:1px solid #C0C0C0;">
													<img src="../html2pdfnew/oproom.jpg" style="width:100px; height:100px;">
												</td>
											';
									}
												
								}
								if($iol_ScanUpload2!=''){
									$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload2);
									imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom1.jpg');
									if(file_exists("html2pdfnew/oproom.jpg")){
										$tableDSummery.='
													<td style="width:175px;text-align:center; border:1px solid #C0C0C0;">
														<img src="../html2pdfnew/oproom1.jpg" style="width:100px; height:100px;">
													</td>';
									}
												
								}
								$tableDSummery.='
									</tr>
								</table>
								</td>
							</tr>';
						}
					}
					
				$tableDSummery.='
				</table>				
			</td>
		</tr>
		<tr>
			<td colspan="2">
			<table style="width:750px;" cellpadding="0" cellspacing="0">';
			
			// Super Bill Records for facility | Surgeon | Anesthesia
			$p_array = array('Surgeon' => 2,'Facility' => 3, 'Anesthesia' => 1);
			foreach($p_array as $dsTitle => $buType)
			{
				$superBillQuery		=	"SELECT sb.* FROM superbill_tbl sb 
											INNER JOIN procedures pr ON(pr.procedureId = sb.cpt_id)
											INNER JOIN procedurescategory prc ON(prc.proceduresCategoryId = pr.catId)
											WHERE sb.confirmation_id = '".$pconfId."'
											AND sb.deleted = '0'
											AND sb.bill_user_type= '".$buType."'
											ORDER BY prc.name = 'G-Codes' DESC, sb.cpt_code";
					$superBillSql		=	imw_query($superBillQuery) or die(imw_error());
					$superBillNum		=	imw_num_rows($superBillSql);
					if($superBillNum > 0 )
					{
						$tableDSummery.='
							<tr>
								<td class="bdrbtm bold bgcolor" style="width:750px;">Discharge Summary ('.$dsTitle.')</td>
							</tr>
							';
						$tableDSummery.='
							<tr>
								<td style="width:750px;">
									<table style="width:750px;" cellpadding="0" cellspacing="0">
										<tr>
											<td style="width:100px;" class="bdrbtm bold pl5">CPT&nbsp;Codes</td>
											<td style="width:50px;" class="bdrbtm bold pl5">Unit</td>
											<td style="width:380px;" class="bdrbtm bold pl5">Dx Codes</td>
											<td style="width:50px;" class="bdrbtm bold pl5">Mod1</td>
											<td style="width:50px;" class="bdrbtm bold pl5">Mod2</td>
											<td style="width:40px;" class="bdrbtm bold pl5">Mod3</td>
										</tr>';	
										while( $superBillRow = imw_fetch_object($superBillSql))	
										{
											$DxCodes	=	($icd10_id_length > 0 ) ? $superBillRow->dxcode_icd10 : $superBillRow->dxcode_icd9;
											$DxCodes	=	str_replace(",",", ",$DxCodes);
											
											$tableDSummery.='
													<tr>
															<td class="bdrbtm pl5">'.$superBillRow->cpt_code.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->quantity.'</td>
															<td class="bdrbtm pl5" style="width:380px;">'.$DxCodes.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->modifier1.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->modifier2.'</td>
															<td class="bdrbtm pl5">'.$superBillRow->modifier3.'</td>
													</tr>';	
										}
										$tableDSummery.='	
									</table>
								</td>
							</tr>
						';		
							
					} 
			}
			
			$tableDSummery.='</table>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="width:740px;" class="bdrbtm">
				I certify that the diagnosis and procedures performed are accurate and complete to the best of my knowledge ';
				if($DischargeResult['surgeon_knowledge']!=''){
					$tableDSummery.='<b>'.$DischargeResult['surgeon_knowledge'].'</b>';
				}
			$tableDSummery.=
			'</td>
		 </tr>
		 '.($disclaimer_txt ? '<tr><td colspan="2" style="width:740px;" class="bdrbtm">'.$disclaimer_txt.'</td></tr>':'').'
		 <tr>
			<td style="width:370px;">';
			if($DischargeResult['signSurgeon1Status']){	
				$tableDSummery.='
					<b>Surgeon:&nbsp;</b>'.$surgeon.'
					<br><b>Electronically Signed:&nbsp;</b>'.$DischargeResult['signSurgeon1Status'].'
					<br><b>Signature Date:&nbsp;</b>'.$objManageData->getFullDtTmFormat($DischargeResult['signSurgeon1DateTime']);
					
			}else{
				$tableDSummery.='
					<b>Surgeon:&nbsp;</b>______
					<br><b>Electronically Signed:&nbsp;</b>________
					<br><b>Signature Date:&nbsp;</b>________';
			}
		$tableDSummery.='
			</td>	
			<td style="width:370px; text-align:right;">';
			if($date_surgeon_sign && $date_surgeon_sign!="00/00/0000"){	
				$tableDSummery.='Date:&nbsp;'.$date_surgeon_sign.'&nbsp;&nbsp;';
					
			}else{
				$tableDSummery.='Date:&nbsp;_______&nbsp;&nbsp;';
			}
		$tableDSummery.='
			</td>					
		 </tr>
	 </table>
	 ';
	 if($disAttached=='Yes') {
		$tableDSummery.=' <page></page>
	 <table style="width:740px; border:apx solid #C0C0C0;" cellpadding="0" cellspacing="0">';
			if($dis_ScanUpload!='' || $dis_ScanUpload2!=''){
				$tableDSummery.='<tr><td style="width:700px;" class="cbold bdrbtm bgcolor">Attached Discharge Summary</td></tr>';
			}
			
			if($dis_ScanUpload!=''){
				$bakImgResourceDischarge = imagecreatefromstring($dis_ScanUpload);
				imagejpeg($bakImgResourceDischarge,'html2pdfnew/disSummarySheet.jpg');
				$newSize=' width="150" height="100"';
				$priImageSize=array();
				if(file_exists('html2pdfnew/disSummarySheet.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/disSummarySheet.jpg');
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 500;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 600;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '<page></page>';
					}
				}
				$tableDSummery.='<tr><td style="width:700px;text-align:center;" class="bdrbtm"><img src="../html2pdfnew/disSummarySheet.jpg" '.$newSize.'></td></tr>';
			}
		
			if($dis_ScanUpload2!=''){
				$bakImgResourceDischarge1 = imagecreatefromstring($dis_ScanUpload2);
				imagejpeg($bakImgResourceDischarge1,'html2pdfnew/disSummarySheet1.jpg');
				
				$priImageSize=array();
				if(file_exists('html2pdfnew/disSummarySheet1.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/disSummarySheet1.jpg');
					$newSize = ' width="150" height="100"';
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 500;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 800;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '<page></page>';												
					}
				}
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm"><img src="../html2pdfnew/disSummarySheet1.jpg" '.$newSize.'></td></tr>';
			}	
		$tableDSummery.='
			</table>
			 ';			
		}
			
	if($ViewOpRoomRecordNumRow>0 && ($iol_ScanUpload!='' || $iol_ScanUpload2!='')) {
		$tableDSummery.='<page></page>
	 <table style="width:740px; border:apx solid #C0C0C0;" cellpadding="0" cellspacing="0">';
			if($iol_ScanUpload!='' || $iol_ScanUpload2!=''){
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm bgcolor">IOL Scanned Image</td></tr>';
			}
			
			if($iol_ScanUpload!=''){
				$bakImgResourceOproom = imagecreatefromstring($iol_ScanUpload);
				imagejpeg($bakImgResourceOproom,'html2pdfnew/oproom.jpg');
				
				$newSize=' height="100"';
				$priImageSize=array();
				if(file_exists('html2pdfnew/oproom.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/oproom.jpg');
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 400;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 500;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '<page></page>';												
					}
				}
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom.jpg" '.$newSize.'></td></tr>';
			}
		
			if($iol_ScanUpload2!=''){
				$bakImgResourceOproom1 = imagecreatefromstring($iol_ScanUpload2);
				imagejpeg($bakImgResourceOproom1,'html2pdfnew/oproom1.jpg');
				
				$priImageSize=array();
				if(file_exists('html2pdfnew/oproom1.jpg')) {
					$priImageSize = getimagesize('html2pdfnew/oproom1.jpg');
					$newSize = 'height="100"';
					if($priImageSize[0] > 395 && $priImageSize[1] < 840){
						$newSize = $objManageData->imageResize(680,400,400);						
						$priImageSize[0] = 400;
					}					
					elseif($priImageSize[1] > 840){
						$newSize = $objManageData->imageResize($priImageSize[0],$priImageSize[1],500);						
						$priImageSize[1] = 500;
					}
					else{					
						$newSize = $priImageSize[3];
					}							
					if($priImageSize[1] > 800 ){					
						echo '<page></page>';												
					}
				}
				$tableDSummery.='<tr><td style="width:700px; text-align:center;" class="bdrbtm"><img src="../html2pdfnew/oproom1.jpg" '.$newSize.'></td></tr>';
			}
	$tableDSummery.='</table>';		
	}
			

$fileOpen = fopen('new_html2pdf/pdffile.html','w+');
$filePut  = fputs($fileOpen,$tableDSummery);
fclose($fileOpen);
$URL='http://'. $_SERVER['HTTP_HOST'].'/surgerycenter/testPdf.html';

if($disSummryFormStatus=='completed' || $disSummryFormStatus=='not completed') {
?>	

 <form name="printDischargeSheet" action="new_html2pdf/createPdf.php?op=p" method="post">
 </form>

<script language="javascript">
	function submitfn(){
		document.printDischargeSheet.submit();
	}
</script>

<script type="text/javascript">
	submitfn();
</script>
<?php
}else {
	echo "<center>Please verify/save this form before print</center>";
}
?>	

