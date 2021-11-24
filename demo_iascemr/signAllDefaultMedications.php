<?php
	
	function signAllDefautlMedications($pConfId,$primaryProcedureCatId,$surgeon_id,$procedureId,$secProcedureId,$terProcedureId,$preOpTableName = 'preopphysicianorders')
	{
			$otherPreOpOrdersFound = "";
			$objManageData1 = new manageData;
			$preOpOrdersFoundExplode	=	0;	
			
			$preOpConfirmationField	=	'patient_confirmation_id';
			$preOpPrimaryKeyField		=	'preOpPhysicianOrdersId';
			
			$getPreOpOrderDetails	= $objManageData1->getRowRecord($preOpTableName,$preOpConfirmationField, $pConfId,'prefilMedicationStatus,'.$preOpPrimaryKeyField.'');
			$preOpPrefilMedicationStatus = $getPreOpOrderDetails->prefilMedicationStatus ;
			$preOpOrdersId	=	$getPreOpOrderDetails->$preOpPrimaryKeyField ;
			
			$str_prelaserprocedure_templete = "SELECT * FROM laser_procedure_patient_table WHERE confirmation_id='$pConfId' ";
			$qry_prelaserprocedure_templete = imw_query($str_prelaserprocedure_templete);
			$prelaserprocedure_templete_tblNumRow = imw_num_rows($qry_prelaserprocedure_templete);
			$fetchRows_preprocedure1 = imw_fetch_array($qry_prelaserprocedure_templete);
			
			$laserprocedurePatientRecordid=$fetchRows_preprocedure1['patient_id'];
			$laserPrefilMedicationStatus = $fetchRows_preprocedure1['prefilMedicationStatus']	;
			
			$prefilMedicationStatus	=	($primaryProcedureCatId == 2 )  ?  $laserPrefilMedicationStatus : $preOpPrefilMedicationStatus ;
			
			//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
				$selectSurgeonQry = "Select * From surgeonprofile Where surgeonId = '".$surgeon_id."' and del_status='' " ;
				$selectSurgeonRes = imw_query($selectSurgeonQry) or die(imw_error());
				while($selectSurgeonRow = imw_fetch_array($selectSurgeonRes)) {
					$surgeonProfileIdArr[] = $selectSurgeonRow['surgeonProfileId'];
				}
				if(is_array($surgeonProfileIdArr)){
					$surgeonProfileIdImplode = implode(',',$surgeonProfileIdArr) ;
				}else {
					$surgeonProfileIdImplode = 0;
				}
				$selectSurgeonProcedureQry = "Select * From surgeonprofileprocedure Where profileId In ($surgeonProfileIdImplode) Order By procedureName";
				$selectSurgeonProcedureRes = imw_query($selectSurgeonProcedureQry) or die(imw_error());
				$selectSurgeonProcedureNumRow = imw_num_rows($selectSurgeonProcedureRes);
				if($selectSurgeonProcedureNumRow>0) {
					while($selectSurgeonProcedureRow = imw_fetch_array($selectSurgeonProcedureRes)) {
						$surgeonProfileProcedureId = $selectSurgeonProcedureRow['procedureId'];
						if($procedureId == $surgeonProfileProcedureId) { 
							$surgeonProfileIdFound = $selectSurgeonProcedureRow['profileId'];
							
						}
					}
					/*if($surgeonProfileIdFound) {*/
						$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonProfileId = '$surgeonProfileIdFound' and del_status=''";/*
					}else {	//ELSE SELECT DEFAULT PROFILE OF SURGOEN
						$selectSurgeonProfileFoundQry = "select * from surgeonprofile where surgeonId = '$surgeon_id' AND defaultProfile = '1'";
					}*/
						$selectSurgeonProfileFoundRes = imw_query($selectSurgeonProfileFoundQry) or die(imw_error());
						$selectSurgeonProfileFoundNumRow = imw_num_rows($selectSurgeonProfileFoundRes);
						if($selectSurgeonProfileFoundNumRow > 0) {
							$selectSurgeonProfileFoundRow = imw_fetch_array($selectSurgeonProfileFoundRes);
							
							$postOpDropSurgeonProfile = stripslashes($selectSurgeonProfileFoundRow['postOpDrop']);
							$medicalEvaluationSurgeonProfile = stripslashes($selectSurgeonProfileFoundRow['medicalEvaluation']);
							$preOpOrdersFound = $selectSurgeonProfileFoundRow['preOpOrders'];
							$otherPreOpOrdersFound 	= $selectSurgeonProfileFoundRow['otherPreOpOrders'];
							
							
						}
				}	
				
				
			//GETTING SURGEON PROFILE TO SHOW FIRST VIEW OF SURGEONID
			
			/*****
			* Start Procedure Preference Card to show first view
			*****/
			if( $selectSurgeonProfileFoundNumRow == 0 )
			{
				$proceduresArr	=	array($procedureId,$secProcedureId,$terProcedureId);
				foreach($proceduresArr as $procId)
				{
					if($procId)
					{		
						$procPrefCardQry	=	"Select * From procedureprofile Where procedureId = '".$procId."' ";
						$procPrefCardSql	=	imw_query($procPrefCardQry) or die( 'Error at line no.'. (__LINE__).': '.imw_error());
						$procPrefCardCnt	=	imw_num_rows($procPrefCardSql);
						if($procPrefCardCnt > 0 )
						{
							$procPrefCardRow	=	imw_fetch_object($procPrefCardSql);
							$preOpOrders 		= 	$procPrefCardRow->preOpOrders;
							$preOpOrdersFound	=	$preOpOrders;
							$otherPreOpOrdersFound = $procPrefCardRow->otherPreOpOrders;	
							/*if(strpos($preOpOrders, ", ")){
								$preOpOrdersArr = explode(",", $preOpOrders);
							}else{
								$preOpOrdersArr[] = $preOpOrders;
							}*/
							
							break; 
						}
					}
				}
			}
			
			
			/*****
			* End Procedure Preference Card to show first view
			*****/
			
			
			$laser_pre_op_medication =  ''; 
			
			if( $primaryProcedureCatId == 2 )
			{
				if($laserprocedurePatientRecordid == 0 )
				{ 
						// GETTING CONFIRMATION DETAILS
						$laserprocedure_Id = $procedureId ;
		
						// GETTING laser procedure templete detail
						$str_procedure_templete = "SELECT * FROM laser_procedure_template WHERE laser_procedureID = '$laserprocedure_Id' ORDER BY laser_templateID DESC  ";
						$qry_procedure_templete = imw_query($str_procedure_templete);
						$procedure_templete_tblNumRow = imw_num_rows($qry_procedure_templete);
						
						$laser_preop_medication		=	''	;
						while($fetchRows_procedure = imw_fetch_array($qry_procedure_templete))
						{
							$procedure_surgeonId 	=	$surgeon_id;
							$surgeon_select_explode	=	$fetchRows_procedure['laser_surgeonID'];

							if($surgeon_select_explode!="all")
							{
								$surgeon_select=explode(",",$surgeon_select_explode);
								$count_surgeon= count($surgeon_select);
								if($count_surgeon==1)
								{ 
									if($procedure_surgeonId==$surgeon_select_explode)
									{
										$laser_preop_medication	=	$fetchRows_procedure['laser_preop_medication'];
										break;
									}
								}
								
								$matchedSurgeon=false;
								
								if($count_surgeon>1)
								{
									for($i=0;$i<$count_surgeon;$i++)
									{
											$match_surgeonid=$procedure_surgeonId;
											$surgeon=$surgeon_select[$i];
											if($surgeon==$match_surgeonid)
											{
												$matchedSurgeon=true;
												$laser_preop_medication	=	$fetchRows_procedure['laser_preop_medication'];
											}
									}
								}
								if($matchedSurgeon==true) { break; }
								
							}
							else
							{ 
								$laser_preop_medication	=	$fetchRows_procedure['laser_preop_medication'];
							}
						}
						
						$tempLaser_preop_medication = str_replace(',','',$laser_preop_medication);
						
						if($tempLaser_preop_medication )  
							$preOpOrdersFound = $laser_preop_medication ;
						
						
				}
					
			}
			$medicationLists =array();
			if($prefilMedicationStatus <> 'true') 
			{		
					$preOpOrdersFoundExplode = explode(',',$preOpOrdersFound);
					for($k=0;$k<=count($preOpOrdersFoundExplode);$k++) 
					{
						if($preOpOrdersFoundExplode[$k] <> '' )
						{
								$selectPreOpmedicationOrderQry = "select * from preopmedicationorder where preOpMedicationOrderId = '$preOpOrdersFoundExplode[$k]'";
								$selectPreOpmedicationOrderRes = imw_query($selectPreOpmedicationOrderQry) or die(imw_error());
								$selectPreOpmedicationOrderRow = imw_fetch_array($selectPreOpmedicationOrderRes);
										
								$selectMedicationName = $selectPreOpmedicationOrderRow['medicationName'];
								$selectStrength = $selectPreOpmedicationOrderRow['strength'];
								$selectDirections = $selectPreOpmedicationOrderRow['directions'];
								
								$data = array();
								$data['medicationName']		=	$selectMedicationName;
								$data['strength'] 				=	$selectStrength;
								$data['direction'] 				=	$selectDirections;
								
								array_push($medicationLists,$data);
						}
					}
				
			}
			
			$medicationLists = array_map("unserialize", array_unique(array_map("serialize", $medicationLists)));
			return  array($medicationLists,$otherPreOpOrdersFound);
		
	}
	
	
	function signAllDefaultPostOpMedications($pConfId,$primaryProcedureCatId,$surgeon_id,$procedureId,$secProcedureId,$terProcedureId,$preOpTableName = 'postopphysicianorders')
	{
		$medicationPostOpLists = array();
		$objManageData2 = new manageData;
		//START GET POST OP ORDERS FOR LASER PROCEDURE
		$patientToTakeHome = "";
		if($primaryProcedureCatId == "2") {
			$laserProcTempQry 	=	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procedureId."' And (FIND_IN_SET(".$surgeon_id.",laser_surgeonID)) ORDER BY laser_templateID DESC LIMIT 0,1 ";
			$laserProcTempSql	=	imw_query($laserProcTempQry) or die('Error found at line no '.(__LINE_).': '. imw_error());
			$laserProcTempCnt	=	imw_num_rows($laserProcTempSql);
			if( $laserProcTempCnt == 0 )
			{
				$laserProcTempQry 	=	"SELECT * FROM laser_procedure_template WHERE laser_procedureID = '".$procedureId."' And laser_surgeonID = 'all' ORDER BY laser_templateID DESC LIMIT 0,1 ";
				$laserProcTempSql	=	imw_query($laserProcTempQry) or die('Error found at line no. '.(__LINE_).': '. imw_error());
				$laserProcTempCnt	=	imw_num_rows($laserProcTempSql);	
			}
			if( $laserProcTempCnt > 0 ) {
				$laserProcTempRow	=	imw_fetch_array($laserProcTempSql);
				$patientToTakeHome 	= 	trim($laserProcTempRow["laser_post_progress"]);
			}
		}
		//END GET POST OP ORDERS FOR LASER PROCEDURE
		 
		if($patientToTakeHome == "") {
			$surgeonProfileQry="
				SELECT a.postOpDrop FROM surgeonprofile a,surgeonprofileprocedure b
				WHERE a.surgeonId			=	'".$surgeon_id."'
				AND   b.procedureId			=	'".$procedureId."'
				AND   a.surgeonProfileId	=	b.profileId
				AND   a.del_status=''
			";
			$surgeonProfileRes = imw_query($surgeonProfileQry) or die(imw_error());
			$surgeonProfileNumRow = imw_num_rows($surgeonProfileRes);
			if($surgeonProfileNumRow>0) {
				$surgeonProfileRow = imw_fetch_array($surgeonProfileRes);
				$patientToTakeHome = stripslashes($surgeonProfileRow['postOpDrop']);
			}else {	
			
				/* Start Procedure Preference Card if surgeon's profile/Default  Not found*/
				$proceduresArr	=	array($procedureId,$secProcedureId,$terProcedureId);
				foreach($proceduresArr as $procedureIdMain)
				{
					if($procedureIdMain)
					{		
						$procPrefCardQry	=	"Select * From procedureprofile Where procedureId = '".$procedureIdMain."' ";
						$procPrefCardSql		=	imw_query($procPrefCardQry) or die( 'Error at line no.'. (__LINE__).': '.imw_error());
						$procPrefCardCnt	=	imw_num_rows($procPrefCardSql);
						if($procPrefCardCnt > 0 )
						{
							$procPrefCardRow		=	imw_fetch_object($procPrefCardSql);
							$patientToTakeHome	= $procPrefCardRow->postOpDrop;
							
							break; 
						}
					}
				}
				/* End Procedure Preference Card if surgeon's profile/Default  Not found*/
			
			}
		}
		//Start Get Default Post Op Order
		$defaultPostOpOrder	=	'';
		if( $patientToTakeHome == '' && $primaryProcedureCatId <> '2')
		{
			$defaultPostOpOrder	= $objManageData2->getDefault('postopdrops','name',"@@");
			$explodeDefault		= true;
			$patientToTakeHome 	= $defaultPostOpOrder;
		}
		//End Get Default Post Op Order
		$pOrderData = array();
		if($explodeDefault) {
			$pOrderData	=	explode('@@',$patientToTakeHome);
		}
		else {
			$pOrderData	=	explode(',',$patientToTakeHome);
		}
		//print'<pre>';print_r($pOrderData);
		//array_push($medicationPostOpLists,$pOrderData);
		$medicationPostOpLists = $pOrderData;
		return  $medicationPostOpLists;		
	}
	
?>	