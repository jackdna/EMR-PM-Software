<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
$userLoginId = $_SESSION['iolink_loginUserId'];
$userLoginName = $_SESSION['iolink_loginUserName'];
$userLoginPrivileges = $_SESSION['iolink_userPrivileges'];
$today = date('Y-m-d');
class manageData{
	// INSERT TABLE ROW
	function addRecords($arrayRecord, $table){
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$insertStr = "INSERT INTO $table SET ";
			foreach($arrayRecord as $field => $value){
				++$seq;
				$insertStr .= "$field = '$value'";
				if($seq<$countFields){
					$insertStr .= ", ";
				}
			}
			//echo '<br>'.$insertStr; 
			$insertQry = imw_query($insertStr);
			$insertId = imw_insert_id();
			return $insertId;
		}
	}
	// INSERT TABLE ROW
	
	// FETCH ROW OBJECT
	function getRowRecord($table, $conditionId, $value, $orderBy=0, $sortOrder=0){
		if($conditionId && $orderBy && $sortOrder){
			$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy $sortOrder";
		} else if($orderBy){
			$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy DESC";
		}else{
			$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value'";
		}		
		//echo '<br>'.$qryStr;
		$qryQry = imw_query($qryStr);
		if($qryQry){
			$qryRow = imw_fetch_object($qryQry);
			return $qryRow;
		}
	}
	// FETCH ROW OBJECT

	// FETCH ROW TO EXTRACT
	function getExtractRecord($table, $conditionId, $value){
		$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value'";
		$qryQry = imw_query($qryStr);
		if($qryQry){
			$qryRow = imw_fetch_assoc($qryQry);
			return $qryRow;
		}
	}
	// FETCH ROW TO EXTRACT

	// GET OBJECT ON MULTIPLE CONDITIONS
		function getMultiChkArrayRecords($table, $conditionArr, $orderBy=0, $sortOrder=0,$extraCondition=''){
			$elements = count($conditionArr);
			$counter = 0;
			$qry = "SELECT * FROM $table WHERE";
			foreach($conditionArr as $keyEle => $keyValue){
				++$counter;
				$qry .= " $keyEle = '$keyValue'";
				if($counter<$elements){
					$qry .= " AND";
				}				
			}
			if($elements>0) {
				$qry .= $extraCondition;
			}
			if($qry) {
				if($orderBy) {
					$qry .=" ORDER BY $orderBy $sortOrder";
				}
			}
			//echo '<br>'.$qry;
			$qryQry = imw_query($qry);
			$qryRows = array();
			if($qryQry){
				while($qryRow = imw_fetch_object($qryQry)){
					$qryRows[] = $qryRow;
				}
				return $qryRows;
			}
		}
	// GET OBJECT ON MULTIPLE CONDITIONS

	// FETCH ARRAY OBJECT
	function getArrayRecords($table, $conditionId=0, $value=0, $orderBy=0, $sortOrder=0){
		if($orderBy){
			if($conditionId && !$value && $sortOrder){
				$qryStr = "SELECT * FROM $table WHERE $conditionId <> '' ORDER BY $orderBy $sortOrder";
			}else if($conditionId && $sortOrder){
				$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy $sortOrder";
			}else if($conditionId && !$sortOrder){
				$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ORDER BY $orderBy DESC";
			}else if($sortOrder){
				$qryStr = "SELECT * FROM $table ORDER BY $orderBy $sortOrder";
			}else {
				$qryStr = "SELECT * FROM $table ORDER BY $orderBy DESC";
			}
		}else{
			if($conditionId){
				$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value'";
			}else{
				$qryStr = "SELECT * FROM $table";
			}
		}
		$qryQry = imw_query($qryStr);
		$qryRows = array();
		if($qryQry){
			while($qryRow = imw_fetch_object($qryQry)){
				$qryRows[] = $qryRow;
			}		
			return $qryRows;
		}
	}
	// FETCH ARRAY OBJECT
	
	// UPDATE TABLE ROW
	function updateRecords($arrayRecord, $table, $condId, $condValue){
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$updateStr = "UPDATE $table SET ";
			$seq = 0;
			foreach($arrayRecord as $field => $value){
				++$seq;
				$updateStr .= "$field = '$value'";
				if($seq<$countFields){
					$updateStr .= ", ";
				}
			}			
			$updateStr .= " WHERE $condId = '$condValue'";
			//echo $updateStr; die;
			$updateQry = imw_query($updateStr);
		}		
	}
	// UPDATE TABLE ROW
	
	// DELETE RECORD
	function delRecord($table, $field, $value){
		$qryStr = "DELETE FROM $table WHERE $field = '$value'";
		$qryQry = imw_query($qryStr);
	}
	// DELETE RECORD
	
	// CHANGE DATE FORMAT
	function changeDateMDY($dateStr){
		list($yyDate, $mmDate, $ddDate) = explode('-', $dateStr);
		$showDate = $mmDate.'-'.$ddDate.'-'.$yyDate;
		return $showDate;
	}
	function changeDateYMD($dateStr){
		list($mmDate, $ddDate, $yyDate) = explode('-', $dateStr);
		$showDate = $yyDate.'-'.$mmDate.'-'.$ddDate;
		return $showDate;
	}
	// CHANGE DATE FORMAT
	
	// DATE DIFFER
	function getDateDifferance($date1, $date2){
		$qryStr = "SELECT DATEDIFF('$date1', '$date2') as differ";
		$qryQry = imw_query($qryStr);
		$qryRow = imw_fetch_assoc($qryQry);
		$differDate = $qryRow['differ'];
		return $differDate;
	}
	// DATE DIFFER

	//get patient insurance data
	function getInsurance($wid,$type,$patient_id,$caseid=0){
		$caseid = (int) $caseid;
		if( !$caseid) {
			$getInsCaseQry 	= "SELECT * FROM iolink_insurance_case WHERE patient_id='".$patient_id."' AND patient_id!='' Order by ins_caseid";
			$getInsCaseRes 	= imw_query($getInsCaseQry);
			$getInsCaseNumRow 	= imw_num_rows($getInsCaseRes);
			$getInsCaseRow = imw_fetch_assoc($getInsCaseRes);
			$caseid = $getInsCaseRow['ins_caseid'];
		}
		$qryStr = "SELECT * from insurance_data where patient_id = '".$patient_id."' and type = '".$type."' and ins_caseid > 0 and actInsComp='1' and ins_caseid = ".$caseid." order by id";
		$qryQry = imw_query($qryStr);
		if(@imw_num_rows($qryQry) > 0){
			$qryRow = imw_fetch_array($qryQry);
		}else{
			$qryRow = false;
		}
		return $qryRow;
	}
	//end get patient insurance data

	//START FUNCTION TO CALCULATE AGE
	function dob_calc($dob){
		$dob_yy = substr($dob,0,4);
		$dob_rem = substr($dob,4);
		$dob_rem = str_replace("-","",$dob_rem);
		$dob_curr = date("Y").$dob_rem;
		$age = date("Y")-$dob_yy;
		if ($dob_curr > date("Ymd")) {
			$age = $age-1;
		}
		return $age;
	}
	//END FUNCTION TO CALCULATE AGE

	function imageResize($width, $height, $target) {
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}
		$width = round($width * $percentage);
		$height = round($height * $percentage);
		return "width=\"$width\" height=\"$height\"";
	}

	function getDateFormat($date,$output_sep = '-'){
		$output_sep = trim($output_sep);
		$output_sep = $output_sep ? $output_sep : '-';
		$date = substr($date,0,10);
		$old_format = preg_split('/-/',$date);
		$date = preg_replace('/[^0-9]/','',$date);
		$date_result = '';
		$date = substr($date,0,8);
		if(empty($date) == false && $date != '00000000'){
			if(strlen(end($old_format)) == 2){
				$date_result = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})/","$2".$output_sep."$3".$output_sep."$1",$date);
			}
			else{
				$date_result = preg_replace("/([0-9]{2})([0-9]{2})([0-9]{4})/","$3".$output_sep."$1".$output_sep."$2",$date);
			}
		}
		return $date_result;
	}
	
	function get_days($date)
	{
		//$days=substr($date,8,10);
		$years=substr($date,0,4);
		$months=substr($date,5,2);
		$days=substr($date,8,2);
		return $days;
	}
	
	
	function getSigImage($postDataWit,$pth="",$rootPath){
		/*
		if(!trim($rootPath)) {
			$rootPath = "/var/www/html";
		}
		*/
		$rootPath = "/var/www/html";
		$output = shell_exec("java -cp .:".$rootPath."/SigPlusLinuxHSB/SigPlus.jar:".$rootPath."/SigPlusLinuxHSB/RXTXcomm.jar:".$rootPath."/SigPlusLinuxHSB SigPlusImgDemoV2 ".$postDataWit." 2>&1 ");
		@copy($rootPath."/SigPlusLinuxHSB/sig.jpg",$pth);

/*
$postData = "06003800B302F50200FF000001FF01FFFFFFFFFEFEFCFCFBFCFAF9FAF8F9F6FAF4FAF4FDF3FEF5FFF501F602F903FA04FE060008050A070D0B100D110E120E110E100C0E0A0D060A04080008FD07FA07F606F305F104F001F000F0FFF0FCF2FBF4F8F6F7F9F7FCF600F704F706F905FB03FE01FFFF000F00E702E102000200050109000C000DFF0E000E000E010E020D01090003000100FF0D00D802BB0200FB00F801F502F502F904FD0301040202030101000000003F004E030E03FFFFFF00FEFFFD00FC00FC01FC02FC04FB05FC07FB0AFA0BFA0EFB0FFB0EFD0EFF0B02090505070209FD09FB0AF708F508F306F303F403F501F700FA00FD00010104040A050F06150619051B031B0019FE17FC13FA10F90CF808F705F603F5FFF5FEF5FBF7F9F9F7FAF6FDF5FEF4FFF501F502F802FA01FE00000000ED008F0326030000000301050209010B020E010F010E010B010901060104030004FE04FA04F804F604F403F304F303F203F402F303F502F703F903FC02FF0302030403080309030C040C030B040C04090609060706050702070008FE07FC08F907F807F706F706F604F603F601F700F9FEFCFDFEFC01FA04FB07FA0AF90BF90DF90DFA0CFD0BFF08020503040501060006FD06FB06F906F806F705F604F703F802F901FB01FC0000000300060009010A010B010A030903070504050207FF08FD08FA07F807F606F506F404F403F203F002ED01EB01EA01EB00EC00F100F501F9FFFD0000FE03FE06FE0BFD10FE13FD17FE19FE19FF1801160314060F060B06080704060206FF07FD05FB06F905F704F604F502F502F401F600F701FA00FDFF000003FF06FF09FF0CFE0CFE0D000B00090108020603040503060106FE07FE05FB06FC07FA05FA05FA05FA03F902FA02FC02FC00FF0100FF04FF050006FF07000701070106010403040301050106FE08FC08FA0AF80BF80AF70BF609F707F806F904FB02FC02FF0101FF04FF06FE07FD08FF0800090107020604050604070109000BFE0BFC0CFA0BF90BF80AF807F804F802FA00FCFDFEFC02FA05FA08F90CFA0FFD11FF110210050C090A0C060F0210FE11FB10F80FF50AF704FC01FEFF011200D503CF0202FF07FE0EFB16F91EF726F72CF631F634F834F932F92BFC2300160207010202FEFF00000000000000010000000000000000000000007800000001000000000000000000";
*/
	}
	//START FUNCTION TO GET OPERATOR INITIALS
	function getOperatorInitialsArray(){
		$oprInitQry = "SELECT usersId, UCASE(IF(mname!='',CONCAT(SUBSTRING(fname,1,1),SUBSTRING(mname,1,1),SUBSTRING(lname,1,1)),CONCAT(SUBSTRING(fname,1,1),SUBSTRING(lname,1,1)))) AS user_initials, concat(fname,' ',mname,' ',lname) FROM users ORDER BY usersId";
		$oprInitRes = imw_query($oprInitQry);
		$oprInitArr = array();
		$oprInitArr[0]='iMW';
		if(imw_num_rows($oprInitRes)>0) {
			while($oprInitRow = imw_fetch_array($oprInitRes)) {
				$oprInitUserID = $oprInitRow['usersId'];
				$oprInitArr[$oprInitUserID] = $oprInitRow['user_initials'];
			}
		}
		return $oprInitArr;
	}
	//END FUNCTION TO GET OPERATOR INITIALS
	
	function getASCInfo($fac_id) {
		$ascAddr = $ascPhoneNum = "";
		$qryStr = "SELECT fac_id, fac_name,fac_address1,fac_address2,fac_city,fac_state,fac_zip,fac_contact_phone FROM facility_tbl WHERE fac_id = '".$fac_id."'";
		$qryQry = imw_query($qryStr);
		if(imw_num_rows($qryQry)>0){
			$qryRow = imw_fetch_object($qryQry);
			if(trim($qryRow->fac_address1)) {
				$ascAddr .= '<table style="border:none;" cellpadding="0" cellspacing="0">';
				$ascAddr .= '	<tr><td>'.trim(stripslashes($qryRow->fac_address1)).'</td></tr>';
				if(trim($qryRow->fac_address2)) {
					$ascAddr .= '	<tr><td>'.trim(stripslashes($qryRow->fac_address2)).'</td></tr>';
				}
				if(trim($qryRow->fac_city) && trim($qryRow->fac_zip)) {
					$ascAddr .= '	<tr><td>'.trim(stripslashes($qryRow->fac_city)).', '.trim(stripslashes($qryRow->fac_state)).' '.trim(stripslashes($qryRow->fac_zip)).'</td></tr>';
				}
				$ascAddr .= '</table>';	
			}
			if(trim($qryRow->fac_contact_phone)) {
				$ascPhoneNum = trim($qryRow->fac_contact_phone);
			}
		}
		return array($ascAddr,$ascPhoneNum);
	}
	
	function Imw_IOL($dos,$patientInWaitingId, $imwPatientId,$site)
	{
			$return = array(							
							'iolLens1AScanOD'		=> '', 'iol1PowerAScanOD'		=> '',	'iolLens2AScanOD'		=> '', 'iol2PowerAScanOD'		=> '',
							'iolLens3AScanOD'		=> '', 'iol3PowerAScanOD'		=> '',	'iolLens4AScanOD'		=> '', 'iol4PowerAScanOD'		=> '',
							'iolLens1AScanOS'		=> '', 'iol1PowerAScanOS'		=> '',	'iolLens2AScanOS'		=> '', 'iol2PowerAScanOS'		=> '',
							'iolLens3AScanOS'		=> '', 'iol3PowerAScanOS'		=> '',	'iolLens4AScanOS'		=> '', 'iol4PowerAScanOS'		=> '',
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
				
				//start
				$andMnkEyeQry = "";
				if(trim($site)) {
					$andMnkEyeQry = " AND mank_eye = '".$site."' ";	
				}
				$sxPlanSheetQry = "SELECT id, mank_eye, surgeon_id, lens_sle_summary FROM chart_sx_plan_sheet 
									WHERE sx_plan_dos <= '".$dos."' AND patient_id = '".$imwPatientId."' AND del_status = '0' 
									".$andMnkEyeQry."
									ORDER BY sx_plan_dos DESC
									LIMIT 0,1";
				$sxPlanSheetRes	 	= imw_query($sxPlanSheetQry) or die(imw_error($sxPlanSheetQry));					
				if(imw_num_rows($sxPlanSheetRes)>0) {
					$sxPlanSheetRow = imw_fetch_assoc($sxPlanSheetRes);	
					$idChartSxPlanSheet = $sxPlanSheetRow["id"];
					$lensSurgeonId = $sxPlanSheetRow["surgeon_id"];
					$lensSleSummary = $sxPlanSheetRow["lens_sle_summary"]; 
					$return["lensSleSummary"] = $lensSleSummary;
					
					// Stop to get IOL Lenses information in booking sheet for  patient name column**
					$iolinkAscanCommonQry1 = "SELECT csl . * , csa.indx
												FROM chart_sps_lens csl
												INNER JOIN chart_sps_ast_plan_tpa csa ON(csa.lens_type=csl.lens_type AND csa.id_chart_sx_plan_sheet = '".$idChartSxPlanSheet."' AND csa.prov_id = '".$lensSurgeonId."') 
												WHERE csl.id_chart_sx_plan_sheet = '".$idChartSxPlanSheet."' ";
					$iolinkAscanCommonQry2 = "	ORDER BY csa.indx LIMIT 0 , 4 ";
					$iolinkAscanQry = $iolinkAscanCommonQry1.$iolinkAscanCommonQry2;
					//echo $iolinkAscanQry;
					//end
					
					$iolinkAscanRes	 	= imw_query($iolinkAscanQry) or die(imw_error($iolinkAscanQry));
					$iolinkAscanNumRow 	= imw_num_rows($iolinkAscanRes);
					
					
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
						}
						//echo'<pre>';print_r($return);
					}
					//END GET IOL LENSES FROM ASCAN 
				}
			}
			include('common/conDb.php');
			return $return ;
	}

	
	function getAllRecords($table,$fields = array(), $where = array(), $groupBy = array(), $orderBy = array(), $returnKey = '' )
	{
		$return	=	array(); $matchString = ''; $orderString = ''; 
		
		if(empty($table))	return $return;
		
		$fields	=	implode(",",$fields);
		$fields	=	empty($fields) ? '*' : $fields ;
		
		$groupBy=	implode(",",$groupBy);
		
		if(is_array($where) && count($where) > 0)
		{
			$matchString	.=	' Where '	;
			$whereCount	=	count($where);
			$counter=	0;
			foreach($where as $key=>$value)
			{	$counter++;
				$matchString	.=	$key . " '".$value."' " ;
				$matchString	.=	($counter < $whereCount ) ? ' AND ' : '' ;
			}
		}
		
		if(is_array($orderBy) && count($orderBy) > 0 )
		{
			$orderString	=	' Order By ';
			$counter			=	0;
			$orderCount	=	count($orderBy);
			foreach($orderBy as $key=>$value)
			{	$counter++;
				$orderString	.=	$key . ' ' . $value ;
				$orderString	.=	($counter < $orderCount ) ? ', ' : '' ;
			}
		}
		
		$query	=	"Select ".$fields." From ".$table. " ".$matchString." ".$groupBy.$orderString;
		$sql	=	imw_query($query);
		//return $query; 
		if($sql)
		{
			while($row	=	imw_fetch_object($sql))
			{
				$return[]=	$row;
			}
		}
		
		return $return;
		
	}

	function get_patient_data($patient_id, $fields = "*" ) {
		
		

		$return = array();
		$fields = trim($fields);
		$fields = $fields ? $fields : '*';
		$ptDataQry = "SELECT $fields from patient_data_tbl WHERE patient_id = ".(int)$patient_id;
		$ptDataSql = imw_query($ptDataQry) or die(imw_error());
		if(imw_num_rows($ptDataSql)){
			$ptDataRow = imw_fetch_array($ptDataSql);
			$return = $ptDataRow;
		}

		return $return;
	}
}
?>