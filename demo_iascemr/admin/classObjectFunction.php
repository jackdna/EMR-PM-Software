<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
$userLoginId = $_SESSION['loginUserId'];
$userLoginName = $_SESSION['loginUserName'];
$userLoginPrivileges = $_SESSION['userPrivileges'];
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
			$insertQry = imw_query($insertStr);
			$insertId = imw_insert_id();
			return $insertId;
		}		
	}
	// INSERT TABLE ROW
	
	// FETCH ROW OBJECT
	function getRowRecord($table='', $conditionId='', $value='', $orderBy=0, $sortOrder=0, $fieldName='', $extraCondition=''){
		$field="*";
		if($fieldName) {$field=$fieldName;  }
		if($conditionId && $orderBy && $sortOrder){
			$qryStr = "SELECT ".$field." FROM $table WHERE $conditionId = '$value' ".$extraCondition." ORDER BY $orderBy $sortOrder";
		} else if($orderBy){
			$qryStr = "SELECT ".$field." FROM $table WHERE $conditionId = '$value' ".$extraCondition." ORDER BY $orderBy DESC";
		}else{
			$qryStr = "SELECT ".$field." FROM $table WHERE $conditionId = '$value' ".$extraCondition;
		}		
		$qryQry = imw_query($qryStr);
		if($qryQry){
			$qryRow = imw_fetch_object($qryQry);
			return $qryRow;
		}
	}
	// FETCH ROW OBJECT

	// FETCH ROW TO EXTRACT
	function getExtractRecord($table='', $conditionId='', $value='', $fieldName=''){
		$field="*";
		if($fieldName) {$field=$fieldName;  }
		$qryStr = "SELECT ".$field." FROM $table WHERE $conditionId = '$value'"; 
		$qryQry = imw_query($qryStr);
		
		if($qryQry){
			$qryRow = imw_fetch_assoc($qryQry);
			return $qryRow;
		}
	}
	
	// FETCH ROW TO EXTRACT
	function getRowCount($table, $where = array()){
		$return 	=	0; $matchString = '';
		if(empty($table))	return $return;
		
		if(is_array($where) && count($where) > 0)
		{
			
			$matchString	.=	' WHERE '	;
			$counter=	0;
			foreach($where as $key=>$value)
			{	$counter++;
				$matchString	.=	$key . "'".$value."'" ;
				$matchString	.=	($counter < count($where) ) ? ' AND ' : '' ;
			}
		}
		
		$query = "SELECT count(*) as resultCount FROM ".$table." ".$matchString; 
		
		$sql		= imw_query($query);
		
		if($sql)
		{
			$row = imw_fetch_assoc($sql);
			$return	=	$row['resultCount'];
		}
		
		return $return;
	}
	
	function getRecord($table,$fields = array(), $where = array(), $groupBy = array(), $orderBy = array() )
	{
		$return	=	array(); $matchString = ''; $orderString = ''; 
		
		if(empty($table))	return $return;
		
		$fields	=	implode(",",$fields);
		$fields	=	empty($fields) ? '*' : $fields ;
		
		$groupBy=	implode(",",$groupBy);
		
		if(is_array($where) && count($where) > 0)
		{
			
			$matchString	.=	' WHERE '	;
			$counter=	0;
			foreach($where as $key=>$value)
			{	$counter++;
				$matchString	.=	$key . "'".$value."'" ;
				$matchString	.=	($counter < count($where) ) ? ' AND ' : '' ;
			}
		}
		
		if(is_array($orderBy) && count($orderBy) > 0 )
		{
			$orderString	=	' ORDER BY ';
			$counter=	0;
			foreach($orderBy as $key=>$value)
			{	$counter++;
				$orderString	.=	$key . ' ' . $value ;
				$orderString	.=	($counter < count($orderBy) ) ? ', ' : '' ;
			}
		}
		
		$query	=	"SELECT ".$fields." FROM ".$table. " ".$matchString." ".$groupBy.$orderString;  
		$sql	=	imw_query($query);
		
		if($sql){
			$row	=	imw_fetch_assoc($sql);
			$return	=	$row;
		}
		
		return $return;
		
	}
	// FETCH ROW TO EXTRACT

	// GET OBJECT ON MULTIPLE CONDITIONS
		function getMultiChkArrayRecords($table='', $conditionArr=array(), $orderBy=0, $sortOrder=0, $extraCondition=''){
			$elements = count($conditionArr);
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
			$qryQry = imw_query($qry); 
			if($qryQry){
				while($qryRow = imw_fetch_object($qryQry)){
					$qryRows[] = $qryRow;
				}
				return $qryRows;
			}
		}
	// GET OBJECT ON MULTIPLE CONDITIONS

	// FETCH ARRAY OBJECT
	function getArrayRecords($table, $conditionId=0, $value=0, $orderBy=0, $sortOrder=0, $extraCondition=''){
		if($orderBy){
			if($conditionId && !$value && $sortOrder){
				$qryStr = "SELECT * FROM $table WHERE $conditionId <> '' ".$extraCondition." ORDER BY $orderBy $sortOrder";
			}else if($conditionId && $sortOrder){
				$qryStr = "SELECT * FROM $table WHERE $conditionId = '".trim($value)."' ".$extraCondition." ORDER BY $orderBy $sortOrder";
			}else if($conditionId && !$sortOrder){
				$qryStr = "SELECT * FROM $table WHERE $conditionId = '".trim($value)."' ".$extraCondition." ORDER BY $orderBy DESC";
			}else if($sortOrder){
				$qryStr = "SELECT * FROM $table ORDER BY $orderBy $sortOrder";
			}else {
				$qryStr = "SELECT * FROM $table ORDER BY $orderBy DESC";
			}
		}else{
			if($conditionId){
				$qryStr = "SELECT * FROM $table WHERE $conditionId = '$value' ".$extraCondition." ";
			}else{
				$qryStr = "SELECT * FROM $table";
			}
		}
		$qryQry = imw_query($qryStr);
		if($qryQry){
			while($qryRow = imw_fetch_object($qryQry)){
				$qryRows[] = $qryRow;
			}		
			return $qryRows;
		}
	}
	// FETCH ARRAY OBJECT
	
	
	
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
	
	
	// UPDATE TABLE ROW
	function updateRecords($arrayRecord=array(), $table='', $condId='', $condValue='', $extraCondition=''){
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$updateStr = "UPDATE $table SET ";
			foreach($arrayRecord as $field => $value){
				++$seq;
				$updateStr .= "$field = '$value'";
				if($seq<$countFields){
					$updateStr .= ", ";
				}
			}			
			$updateStr .= " WHERE $condId = '$condValue' ".$extraCondition;
			//echo '<br>'.$updateStr;
			$updateQry = imw_query($updateStr);
			
		}		
		
	}
	// UPDATE TABLE ROW
	
	
	// DELETE RECORDS
	function DeleteRecord($table, $field='', $value=''){
		
		$query	=	"DELETE FROM $table WHERE $field = '$value'";
		
		$sql	=	imw_query($query) or die($query.imw_error());
		
		return	$sql ;
		
	}
	
	
	function UpdateRecord($arrayRecord=array(), $table='', $condId='', $condValue=''){
		if(is_array($arrayRecord)){
			$countFields = count($arrayRecord);
			$updateStr = "UPDATE $table SET ";
			foreach($arrayRecord as $field => $value){
				++$seq;
				$updateStr .= "$field = '$value'";
				if($seq<$countFields){
					$updateStr .= ", ";
				}
			}			
			$updateStr .= " WHERE $condId = '$condValue'";
			$updateQry = imw_query($updateStr);
			
			return	$updateQry;
		}		
		
	}
	
	// DELETE RECORD
	function delRecord($table='', $field='', $value=''){
		$qryStr = "DELETE FROM $table WHERE $field = '$value'";
		$qryQry = imw_query($qryStr);
		return $qryQry;
	}
	// DELETE RECORD
	
	// CHANGE DATE FORMAT
	function changeDateMDY($dateStr=''){
		list($yyDate, $mmDate, $ddDate) = explode('-', $dateStr);
		$showDate = $mmDate.'-'.$ddDate.'-'.$yyDate;
		return $showDate;
	}
	function changeDateYMD($dateStr=''){
		list($mmDate, $ddDate, $yyDate) = explode('-', $dateStr);
		$showDate = $yyDate.'-'.$mmDate.'-'.$ddDate;
		return $showDate;
	}
	// CHANGE DATE FORMAT
	
	// DATE DIFFER
	function getDateDifferance($date1='', $date2=''){
		$qryStr = "SELECT DATEDIFF('$date1', '$date2') as differ";
		$qryQry = imw_query($qryStr);
		$qryRow = imw_fetch_assoc($qryQry);
		$differDate = $qryRow['differ'];
		return $differDate;
	}
	// DATE DIFFER

	// DATE DIFFER
	function getDateSubtract($dateStr='', $days=''){
		$days = (int)$days;
		list($yyDate, $mmDate, $ddDate) = explode('-', $dateStr);
		$dateSubtract = date("Y-m-d",mktime(0,0,0,$mmDate,$ddDate - $days,$yyDate));
		return $dateSubtract;
	}
	// DATE DIFFER

	//START FUNCTION TO CALCULATE AGE
	function dob_calc($dob=''){
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
	//END FUNCTION TO CALCULATE AG

	function imageResize($width='', $height='', $target='') {
		if ($width > $height) {
			$percentage = ($target / $width);
		} else {
			$percentage = ($target / $height);
		}
		$width = round($width * $percentage);
		$height = round($height * $percentage);
		return "width=\"$width\" height=\"$height\"";
	}

	function format_phone($phone=''){	
		$phone = preg_replace("/[^0-9]/", "", $phone); 	
		if(strlen($phone) == 7)	{
			$phone = preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);	
		}else if(strlen($phone) == 10) {
			$phone = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "$1-$2-$3", $phone);	
		}
		return $phone;
	}

	function getSigImage($postDataWit="",$pth="",$rootPath=""){
		$rootPath = (!trim($rootPath)) ? "/var/www/html/" : $rootPath."/";
		$output = shell_exec("java -cp .:".$rootPath."SigPlusLinuxHSB/SigPlus.jar:".$rootPath."SigPlusLinuxHSB/RXTXcomm.jar:".$rootPath."SigPlusLinuxHSB SigPlusImgDemoV2 ".$postDataWit." ".$rootPath."SigPlusLinuxHSB/sig.jpg 2>&1");
		@copy($rootPath."SigPlusLinuxHSB/sig.jpg",$pth);
/*
$postData = "06003800B302F50200FF000001FF01FFFFFFFFFEFEFCFCFBFCFAF9FAF8F9F6FAF4FAF4FDF3FEF5FFF501F602F903FA04FE060008050A070D0B100D110E120E110E100C0E0A0D060A04080008FD07FA07F606F305F104F001F000F0FFF0FCF2FBF4F8F6F7F9F7FCF600F704F706F905FB03FE01FFFF000F00E702E102000200050109000C000DFF0E000E000E010E020D01090003000100FF0D00D802BB0200FB00F801F502F502F904FD0301040202030101000000003F004E030E03FFFFFF00FEFFFD00FC00FC01FC02FC04FB05FC07FB0AFA0BFA0EFB0FFB0EFD0EFF0B02090505070209FD09FB0AF708F508F306F303F403F501F700FA00FD00010104040A050F06150619051B031B0019FE17FC13FA10F90CF808F705F603F5FFF5FEF5FBF7F9F9F7FAF6FDF5FEF4FFF501F502F802FA01FE00000000ED008F0326030000000301050209010B020E010F010E010B010901060104030004FE04FA04F804F604F403F304F303F203F402F303F502F703F903FC02FF0302030403080309030C040C030B040C04090609060706050702070008FE07FC08F907F807F706F706F604F603F601F700F9FEFCFDFEFC01FA04FB07FA0AF90BF90DF90DFA0CFD0BFF08020503040501060006FD06FB06F906F806F705F604F703F802F901FB01FC0000000300060009010A010B010A030903070504050207FF08FD08FA07F807F606F506F404F403F203F002ED01EB01EA01EB00EC00F100F501F9FFFD0000FE03FE06FE0BFD10FE13FD17FE19FE19FF1801160314060F060B06080704060206FF07FD05FB06F905F704F604F502F502F401F600F701FA00FDFF000003FF06FF09FF0CFE0CFE0D000B00090108020603040503060106FE07FE05FB06FC07FA05FA05FA05FA03F902FA02FC02FC00FF0100FF04FF050006FF07000701070106010403040301050106FE08FC08FA0AF80BF80AF70BF609F707F806F904FB02FC02FF0101FF04FF06FE07FD08FF0800090107020604050604070109000BFE0BFC0CFA0BF90BF80AF807F804F802FA00FCFDFEFC02FA05FA08F90CFA0FFD11FF110210050C090A0C060F0210FE11FB10F80FF50AF704FC01FEFF011200D503CF0202FF07FE0EFB16F91EF726F72CF631F634F834F932F92BFC2300160207010202FEFF00000000000000010000000000000000000000007800000001000000000000000000";
*/
	}

	function getNumPagesPdf($filepath=''){ 
		$fp = @fopen(preg_replace("/\[(.*?)\]/i", "",$filepath),"r"); 
		$max=0; 
		while(!feof($fp)) { 
				$line = fgets($fp,255); 
				if (preg_match('/\/Count [0-9]+/', $line, $matches)){ 
						preg_match('/[0-9]+/',$matches[0], $matches2); 
						if ($max<$matches2[0]) $max=$matches2[0]; 
				} 
		} 
		fclose($fp); 
		if($max==0){ 
			$im = new imagick($filepath); 
			$max=$im->getNumberImages(); 
		} 
	 
		return $max; 
	} 

	//start create xml for type ahead
	function createXML($tableName='',$field1='',$field2='',$xmlFileName='',$xmltTag=''){
		global $surgeryCenterDirectoryName;
		$xmlQry = "select ".$field1.", ".$field2." from ".$tableName."  order by ".$field1;
		$xmlRes = imw_query($xmlQry); 
		if($xmlRes){
			if(imw_num_rows($xmlRes)>0){
				$dataXML = "";
				$aa = "?>";
				$dataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$dataXML .= "<".$xmltTag."Data>";
				$rep = array("'","'");			
				while ($xmlRow = imw_fetch_array($xmlRes)){							
					$name = $id = "";				
					$id = $xmlRow[$field2];
					$name = trim(htmlentities(str_replace($rep,"",$xmlRow[$field1])));	
					//$name = filter_var($name, FILTER_SANITIZE_URL);														
					if($name){
						if (preg_match("/[a-zA-Z0-9]/", $name)) {					
							$dataXML .= "<".$xmltTag."Info name=\"".$name."\" id=\"".$id."\"></".$xmltTag."Info>";				
						}
					}
				}
				imw_free_result($xmlRes);
				$dataXML .= "</".$xmltTag."Data>";				
				$xmlFilePath = $_SERVER['DOCUMENT_ROOT']."/".$surgeryCenterDirectoryName."/xml/".$xmlFileName;
				file_put_contents($xmlFilePath,$dataXML);
			}	
		}
		return $xmlFilePath;
	}
	//end create xml for type ahead
	
	function XMLToArray($XML=''){
		$values = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $XML, $values);
		xml_parser_free($parser);
		return $values;
	}
	
	
	//to create xml for type ahead
	function createEvaluationXML(){			
		global $surgeryCenterDirectoryName;
		$evaluationQry = "select name, evaluationId from evaluation  order by `name`";
		$evaluationRes = imw_query($evaluationQry); 
		if($evaluationRes){
			if(imw_num_rows($evaluationRes)>0){
				$evaluationDataXML = "";
				$evaluationData = array();
				$aa = "?>";
				$evaluationDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$evaluationDataXML .= "<evaluationData>";
				$rep = array("'","'");			
				while ($evaluationRow = imw_fetch_array($evaluationRes)){							
					$evaluationName = $evaluationId = "";				
					$evaluationId = $evaluationRow['evaluationId'];
					$evaluationName = trim(htmlentities(str_replace($rep,"",$evaluationRow['name'])));	
					//$evaluationName = filter_var($evaluationName, FILTER_SANITIZE_URL);														
					if($evaluationName){
						if (preg_match("/[a-zA-Z0-9]/", $evaluationName)) {					
							$evaluationDataXML .= "<evaluationInfo name=\"".$evaluationName."\" id=\"".$evaluationId."\"></evaluationInfo>";				
						}
					}
				}
				imw_free_result($evaluationRes);
				$evaluationDataXML .= "</evaluationData>";				
				$evaluationXMLFile = $_SERVER['DOCUMENT_ROOT']."/".$surgeryCenterDirectoryName."/xml/evaluationPreOp.xml";
				file_put_contents($evaluationXMLFile,$evaluationDataXML);
			}	
		}
		return $evaluationXMLFile;
	}
	function replacePageTag($content='',$headerBarTable='',$backtop=5){
		$values=str_ireplace('<page></page>','</page><page backtop="'.$backtop.'">'.$headerBarTable,$content);
		return $values;
	}
	
	function save_user_image($arr = array(),$user_id='',$user_fname,$signOf="user_sign"){
		$saveDBpath="";
		global $surgeryCenterDirectoryName;
		$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
		
		$user_fname = str_ireplace(" ","_",$user_fname);
		$user_fname = str_ireplace(",","",$user_fname);
		$user_fname = str_ireplace("!","",$user_fname);
		$user_fname = str_ireplace("@","",$user_fname);
		$user_fname = str_ireplace("%","",$user_fname);
		$user_fname = str_ireplace("^","",$user_fname);
		$user_fname = str_ireplace("$","",$user_fname);
		$user_fname = str_ireplace("'","",$user_fname);
		$user_fname = str_ireplace("*","",$user_fname);
		
		$drawingFolderUser = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles/user_detail";
		if(!is_dir($drawingFolderUser)){		
			mkdir($drawingFolderUser, 0777);
		}
		$drawingFolder = $rootServerPath."/".$surgeryCenterDirectoryName."/admin/pdfFiles/user_detail/".$user_fname."_".$user_id;
		if(!is_dir($drawingFolder)){		
			mkdir($drawingFolder, 0777);
		}
	
		$baseFileName	= $signOf.'_'.$user_id.'_'.date('YmdHis');
		$baseFileExt 	= "jpg";
		$sData = $_REQUEST["sig_datasign"];
		if(empty($sData)== false){
			$sImg		= $drawingFolder."/".$baseFileName.".".$baseFileExt;
			$data 		= str_ireplace("data:image/png;base64,","",$sData);
			$data 		= base64_decode($data);
			$r 			= file_put_contents($sImg, $data);
			$saveDBpath = str_ireplace($rootServerPath."/".$surgeryCenterDirectoryName."/admin/","",$sImg);
		}
		if(empty($_REQUEST['sig_imgsign']) == false){
			$existingImgPath = $rootServerPath.$_REQUEST['sig_imgsign'];
			if(file_exists($existingImgPath)) {
				unlink($existingImgPath);
			}
		}
		return $saveDBpath;
	}
	
	//FUNCTION TO GET USER NAME FROM USER TABLE
	function getUserName($UserId='',$UserType='') {
		$ViewUserNameQry = "select * from `users` where  usersId = '".$UserId."' and user_type ='".$UserType."'";
		$ViewUserNameRes = imw_query($ViewUserNameQry) or die(imw_error()); 
		$UserName = "";
		if(imw_num_rows($ViewUserNameRes)>0) {
			$ViewUserNameRow = imw_fetch_array($ViewUserNameRes); 
			if($ViewUserNameRow["lname"]) {
				$UserName = trim(stripslashes($ViewUserNameRow["lname"].", ".$ViewUserNameRow["fname"]." ".$ViewUserNameRow["mname"]));
			}else {
				$UserName = "";
			}
		}	
		return $UserName;
	}
	//END FUNCTION TO GET USER NAME FROM USER TABLE	
	
	
	
	/*
	*
	*	Function Name :	getDirContentStatus
	*	
	*	params : 
	*	1. $pConfigId	:	Patient Confirmation Id 
	*	2. $return		:	1|2|3 - FolderStatus | ContentCount | Content Default is 3
	*	3. $returnType	:	1|2|3 - htmlString | htmlArray | Array	Default is 3
	*	4. 	$dirName	:	Name of Directory 
	*								Default value is 'H&P'
	* 
	*/
	
	
	function getDirContentStatus( $pConfId='', $return = 3, $returnType = 3 , $dirName = 'H&P' ) 
	{
		$dirName			=	trim($dirName);
		$dirName			=	($dirName)	?	$dirName		:	'H&P' ; 	
		
		$return 			=	(int) $return;
		$returnType 	=	(int) $returnType;
		$isDirCreated	=	false;
		$dirListCount	=	0;
		$dirListContent=	'';
		
		$scanDirQry		= "Select sut.scan_upload_id, sut.image_type, sut.pdfFilePath From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '".$pConfId."' And sut.confirmation_id 	= '".$pConfId."' And sd.document_name = '".$dirName."' And sd.document_id = sut.document_id Order By sd.document_id, sut.document_id ";
		
		if($dirName == 'Sx Planning Sheet') {
			$scanDirQry		= "Select sut.scan_upload_id, sut.image_type, sut.pdfFilePath From  scan_upload_tbl sut, scan_documents sd WHERE sd.confirmation_id = '".$pConfId."' And sut.confirmation_id 	= '".$pConfId."' And sd.document_name = 'Clinical' And sd.document_id = sut.document_id AND sut.pdfFilePath LIKE '%Sx_Planing_Sheet_%' Order By sd.document_id, sut.document_id ";	
		}
		//return $scanDirQry;							
		$scanDirSql		=	imw_query($scanDirQry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
		$scanDirNum	=	imw_num_rows($scanDirSql);
		
		
		if($scanDirNum > 0 ) 
		{
				$isDirCreated	=	true	;
				$dirListCount	=	$scanDirNum ;
				
				if($return === 3 )
				{		
						$listArray	=	array();
						while($scanDirRow = imw_fetch_array($scanDirSql))
						{
									$scan_upload_id	= $scanDirRow['scan_upload_id'];
									$image_type		= $scanDirRow['image_type'];
									$pdfFilePath			= $scanDirRow['pdfFilePath'];
									
									if($image_type=='application/pdf')		$image_type = 'pdf';
									
									$data	=	array();
									$data['imageType']		=	$image_type;
									$data['scanUploadId']	=	$scan_upload_id;
									$data['pdfFilePath']		=	$pdfFilePath;
									
									array_push($listArray,$data);
							}
						
						if($returnType === 1 || $returnType === 2)
						{	
							$onClick	=	array();
							foreach($listArray as $key=>$link)
							{
								$on	=	" top.openImage(\'".$link['scanUploadId']."\',\'".$link['imageType']."\',\'".$link['pdfFilePath']."\')"	;
								array_push($onClick,$on);
							}
							
							$dirName = str_ireplace('H&P','H&amp;P',$dirName);
							$dirName = str_ireplace('Ocular Hx','OCX',$dirName);
							$dirName = str_ireplace('Health Questionnaire','HQ',$dirName);
							$dirName = str_ireplace('Sx Planning Sheet','SxP',$dirName);
							
							if($returnType === 1 )	
							{
								$dirListContent	=	'<a href="#" class="btn-sm" onclick="'.implode(";",$onClick).'">'.$dirName.'</a>&nbsp;';
								return $dirListContent;
							}
							else
							{
								$dirListContent	=	array();
								foreach($onClick as $on)
								{
									$html	=	'';
									$html	=	'<a href="#" class="btn-sm" onclick="return '.$on.'>'.$dirName.'</a>&nbsp;';
									array_push($dirListContent,$html);
									
								}
							}
						}
						elseif($returnType === 3) $dirListContent	=	$listArray; 
				}
		}
			
		if($return === 1)			return $isDirCreated ;
		elseif($return === 2)	return $dirListCount ;
		elseif($return === 3)	return $dirListContent ;
			
				
			
	}
	
	
	/*
	*
	*	Function	:	validateChart
	*	Params		:
	*	$chartName	:	Name of chart needs to validate
	*	$pConfId	:	Patient Confirmation Id
	*/
	
	public function validateChart($chartName='',$pConfId='', $priProCatId = 0)
	{
		$fields	=	array(); $tableName	= $confIdName = '';
		$priProCatId = (int) $priProCatId;
		if($chartName == 'history_physicial_clearance.php')
		{
			$tableName	=	'history_physicial_clearance';	
			$confIdName	=	'confirmation_id';
			$fields		=	array('cadMI'=>'', 'cvaTIA'=>'', 'htnCP'=>'', 'anticoagulationTherapy'=>'', 'respiratoryAsthma'=>'', 'arthritis'=>'', 'diabetes'=>'', 'recreationalDrug'=>'', 'giGerd'=>'', 'ocular'=>'', 'kidneyDisease'=>'', 'hivAutoimmune'=>'', 'historyCancer'=>'', 'organTransplant'=>'', 'badReaction'=>'', 'wearContactLenses'=>'', 'smoking'=>'', 'drinkAlcohal'=>'', 'haveAutomatic'=>'', 'medicalHistoryObtained'=>'', 'signSurgeon1Id'=>'0', 'signAnesthesia1Id'=>'0', 'signNurseId'=>'0');
		}
		elseif($chartName == 'pre_op_health_quest.php')
		{
			$tableName	=	'preophealthquestionnaire';
			$confIdName	=	'confirmation_id';	
			$fields		=	array('heartTrouble'=>'', 'stroke'=>'', 'HighBP'=>'', 'anticoagulationTherapy'=>'', 'asthma'=>'', 'tuberculosis'=>'', 'diabetes'=>'', 'epilepsy'=> '', 'restlessLegSyndrome'=>'', 'hepatitis'=>'',  'kidneyDisease'=> '','hivAutoimmuneDiseases'=>'', 'cancerHistory'=>'', 'organTransplant'=>'', 'anesthesiaBadReaction'=>'', 'walker'=>'', 'contactLenses'=>'', 'smoke'=>'', 'alchohol'=>'', 'autoInternalDefibrillator'=>'', 'metalProsthetics'=>'', 'emergencyContactPerson'=>'', 'emergencyContactPhone'=>'', 'witnessname'=>'','patient_sign_image_path'=>'');		
				
		}
		
		$chartStatus	=	true;
		$row	=	$this->getRowRecord($tableName,$confIdName,$pConfId);
		if(is_array($fields) && count($fields) > 0 )
		{
			foreach($fields as $fieldName => $fieldValue)
			{
				if( $chartName == 'history_physicial_clearance.php' && $fieldName == 'signAnesthesia1Id' && $priProCatId == 2)
					continue;

				if($row->$fieldName == $fieldValue)
				{
					$chartStatus	=	false;	
				}
							
			}
		}
		
		return ($tableName)	?	$chartStatus : false;
		
	}
	
	function getASCInfo($fac_id='') {
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
	
	
	public function getDefault($table="",$field="",$replaceWith = ", ")
	{
		$table	=	trim($table);
		$field	=	trim($field);
		$return	=	'';
		
		$where	=	"";
		if($table == 'intra_op_post_op_order' || $table == 'predefine_suppliesused' )
		{
			$where = " Where deleted = '0' ";
		}
		
		if($table && $field)
		{
			$query	=	"Select group_concat(TBL.".$field." SEPARATOR  '@@') as ".$field." From (Select * From ".$table." ".$where." Order By ".$field.") TBL Where TBL.isDefault = 1 ";
			$sql	=	imw_query($query) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
			$cnt	=	imw_num_rows($sql);
			$row	=	imw_fetch_object($sql);
		
			$return	=	$row->$field;
			$returnArr = explode("@@",$return);
			natsort($returnArr);
			$return = implode($replaceWith,$returnArr);
		}
		
		return $return ;
	}

	
	
	public function surgeonProfile($surgeonID='',$procedureID='')
	{
		$return	=	array();
		$profilesList = $this->getMultiChkArrayRecords('surgeonprofile', array('surgeonId'=> $surgeonID, 'del_status'=>'') );
		if($profilesList)
		{
			foreach($profilesList as $profile)
			{
				$profileID		= $profile->surgeonProfileId;
				$profileDetails	= $this->getMultiChkArrayRecords('surgeonprofileprocedure', array('profileId'=>$profileID) );			
				foreach($profileDetails as $row)
				{
					if( $procedureID == $row->procedureId)
					{
						$data	=	array();	
						foreach($profile as $k=>$v)	$data[$k]	=	$v;
						foreach($row as $k=>$v)		$data[$k]	=	$v;
						$return[0] = $data;
					}
				}
			}
		}
		//print_r($return);
		return $return;
	}
	
	
	public function preferenceCardProfile($procedureID='')
	{
		$profilesList = $this->getMultiChkArrayRecords('procedureprofile', array('procedureId'=>$procedureID));
		return $profilesList;
	}
	
	function relpaceNewLine($val='') {
			$val = str_ireplace("
","~~",$val);
		return $val;
	}
	//calculate cost for surgery
	function calculateCost($p_confirmation_id='')
	{
		$model = "";
		//get list of already saved cost detail , will be used in case of re finalize case
		$qList	=	imw_query("select * from surgery_cost where confirmation_id='$p_confirmation_id' and deleted=0");
		while($list=imw_fetch_assoc($qList))
		{
			$existingArr[$list['item_type']][stripslashes($list['item_name'])]=$list;	
		}
		
		//get array of supply name and cost
		$qSupInfo=imw_query("select * from predefine_suppliesused ");
		while($supInfo=imw_fetch_object($qSupInfo))
		{
			//$supplyName[$supInfo->name]=$supInfo->name;
			$supplyCost[$supInfo->name]=$supInfo->supplies_cost;
		}
		
		$modelCostArr = array();
		$qModelInfo=imw_query("select * from model order by `name`");
		while($modelInfo=imw_fetch_object($qModelInfo))
		{
			$modelCostArr[trim($modelInfo->name)]=$modelInfo->model_cost;
		}
		
		// Start Collecting Procedure 
		$procArr = $laserProcArr = $injMiscProcArr = array();
		$qProcInfo=imw_query("select p.*, pc.isMisc, pc.isInj from procedures p join procedurescategory pc on p.catId = pc.proceduresCategoryId ORDER BY p.code ASC, p.name ASC ") or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
		while($procInfo=imw_fetch_assoc($qProcInfo))
		{
			if($procInfo['catId'] == '2'){
				$laserProcArr[$procInfo['procedureId']] = $procInfo;
			}else{
				if($procInfo['isMisc'] || $procInfo['isInj'] )
					$injMiscProcArr[$procInfo['procedureId']] = $procInfo;
				else if($procInfo['catId'] == '1')
					$procArr[$procInfo['procedureId']] = $procInfo;
			}
		}
		
		if(trim($p_confirmation_id))
		{
			//get primary procedure & Procedures info
			$qProcInfo=	imw_query("Select PC.dos, PC.surgeonId, PC.patient_primary_procedure_id, PC.patient_primary_procedure, PC.patientId, PC.prim_proc_is_misc, P.catId, P.labor_cost, P.code, DS.procedures_code, DS.procedures_name From patientconfirmation PC Join procedures P On PC.patient_primary_procedure_id = P.procedureid JOIN dischargesummarysheet DS ON DS.confirmation_id = PC.patientConfirmationId Where patientConfirmationId='$p_confirmation_id'");
			$procInfo	=	imw_fetch_object($qProcInfo);
			
			$patientId	=	$procInfo->patientId;
			$procCatId	=	$procInfo->catId;
			$dos				=	$procInfo->dos;
			$surgeonId	=	$procInfo->surgeonId;
			$procedureId= $procInfo->patient_primary_procedure_id;
			$procedureName= $procInfo->patient_primary_procedure;
			$laborCost = $procInfo->labor_cost;
			
			$proc_codes_id	=	array_filter(explode(',',$procInfo->procedures_code));
			$proc_names	=	array_filter(explode('!,!',$procInfo->procedures_name));
			
			// Start Check CPT Selected in Discharge Summary Sheet
			// Extract CPT Selcted Under Procedure Category having higher cost
			$tmpDataArr = array();
			if($procCatId == 2) $tmpDataArr = $laserProcArr;
			else{
				if($procInfo->prim_proc_is_misc) $tmpDataArr = $injMiscProcArr;
				else $tmpDataArr = $procArr;
			}
			
			$tmpProcId = $tmpProcName = ''; $tmpLaborCost = 0;
			foreach($proc_codes_id as $val){
				if(array_key_exists($val,$tmpDataArr) && $tmpDataArr[$val]['labor_cost'] > $tmpLaborCost){
					$tmpProcId = $val;
					$tmpProcName = $tmpDataArr[$val]['name'];
					$tmpLaborCost= $tmpDataArr[$val]['labor_cost'];
				}
			}
			
			if($tmpProcId){
				$procedureId = $tmpProcId; 
				$procedureName = $tmpProcName;
				$laborCost = $tmpLaborCost;
			}
					
			//echo $procedureId.' == '.$procedureName.' == '.$laborCost.' == '.$procCatId; 
			
			//get total surgery time to calculate labor cost
			if($procCatId == 2)//get detail from laser_procedure table
			{
				$opTableName						=	'laser_procedure_patient_table';
				$surgeryStartFieldName	=	'proc_start_time';
				
				$qTimeDetail	=	imw_query("Select proc_start_time, proc_end_time From ".$opTableName."  Where confirmation_id = '".$p_confirmation_id."'");
				$timeDetail		=	imw_fetch_object($qTimeDetail);
				
				$proc_start_time	=	$timeDetail->proc_start_time;
				$proc_end_time		=	$timeDetail->proc_end_time;
				
				list($start_date,$proc_start_time)	=	explode(' ',$proc_start_time);
				list($end_date,$proc_end_time)			=	explode(' ',$proc_end_time);
				
				//get surgery time - room in , room out
				$room_in_time		=	$proc_start_time;
				$room_out_time	=	$proc_end_time;
				
			}
			else
			{
				if($procInfo->prim_proc_is_misc)//get time for injection or miscllaneous
				{
					$opTableName						=	'injection';
					$surgeryStartFieldName	=	'startTime';
					
					$qTimeDetail	=	imw_query("Select startTime, endTime From ".$opTableName." Where confirmation_id='".$p_confirmation_id."'");		
					$timeDetail		=	imw_fetch_object($qTimeDetail);
					
					$proc_start_time	=	$timeDetail->startTime;
					$proc_end_time		=	$timeDetail->endTime;
					
					//get surgery time - room in , room out
					$room_in_time		=	$proc_start_time;
					$room_out_time	=	$proc_end_time;	
				}
				else//get detail from operative room record
				{
					$opTableName						=	'operatingroomrecords';
					$surgeryStartFieldName	=	'surgeryStartTime';
					
					$qTimeDetail	=	imw_query("Select surgeryStartTime, surgeryEndTime, surgeryTimeIn, surgeryTimeOut, model From ".$opTableName." Where confirmation_id='".$p_confirmation_id."'");	
					$timeDetail		=	imw_fetch_object($qTimeDetail);
					
					//get room in time
					if($timeDetail->surgeryTimeIn)
					{
						$tm	=	'';
						$tm	=	strtotime($timeDetail->surgeryTimeIn);
						$room_in_time	=	date("H:i:s",$tm);
					}
					
					//get room out time
					if($timeDetail->surgeryTimeOut)
					{
						$tm	=	'';
						$tm	=	strtotime($timeDetail->surgeryTimeOut);
						$room_out_time	=	date("H:i:s",$tm);
					}
					
					$proc_start_time	=	($timeDetail->surgeryStartTime) ?	$timeDetail->surgeryStartTime 	:	$room_in_time;
					$proc_end_time		=	($timeDetail->surgeryEndTime) 	?	$timeDetail->surgeryEndTime 		:	$room_out_time;
				
					$model = $timeDetail->model;
				}
			}
		
			$tblArray	=	array(
												'operatingroomrecords' => 'surgeryStartTime',
												'injection' => 'startTime',
												'laser_procedure_patient_table' => 'proc_start_time'
											);
			foreach($tblArray as $opTableName => $surgeryStartFieldName)
			{		
				// Connect to DB to get next surgery's start time to set surgery end time  
				$nextSurgeryQry	=	"Select TBL.".$surgeryStartFieldName." From patientconfirmation P JOIN ".$opTableName." TBL ON P.patientConfirmationId = TBL.confirmation_id  Where P.dos = '".$dos."' AND surgeonId = '".$surgeonId."' And TIME(TBL.".$surgeryStartFieldName.") <> '00:00:00' And TIME(TBL.".$surgeryStartFieldName.") > '".$proc_start_time."' And TIME(TBL.".$surgeryStartFieldName.") < '".$proc_end_time."' ";
				
				$nextSurgerySql	=	imw_query($nextSurgeryQry) or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.'Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
				
				$nextSurgeryCnt	=	imw_num_rows($nextSurgerySql);
				
				if($nextSurgeryCnt > 0 )
				{
					$nextSurgeryRow	=	imw_fetch_object($nextSurgerySql);
					$proc_end_time	=	$nextSurgeryRow->$surgeryStartFieldName;
					
					if($opTableName == 'laser_procedure_patient_table' )
					{
						list($end_date,$proc_end_time) =	explode(' ',$proc_end_time);
					}
				}
			}
			
			//get total surgeon time in minutes
			if($proc_start_time!='00:00:00' && $proc_end_time!='00:00:00')
			{
				$start	=	strtotime($proc_start_time);
				$end		=	strtotime($proc_end_time);
				$surgeon_time	=	ceil(round(abs($end - $start) / 60,2));
			}
		
			//get total surgery time in minutes
			if($room_in_time && $room_in_time!='00:00:00' && $room_out_time && $room_out_time!='00:00:00')
			{
				$start	=	strtotime($room_in_time);
				$end		=	strtotime($room_out_time);
				$surgery_time	=	ceil(round(abs($end - $start) / 60,2));
			}
		
		if($laborCost && $surgeon_time)
		{
			$total_labor_cost=$laborCost*$surgeon_time;	
			if(is_array($existingArr['Labor'][$procedureName]))
			{
				//get previous values
				$pValues=$existingArr['Labor'][$procedureName];
				$pDate=$pValues['datetime'];
				$pCost=serialize($pValues);
				//update existing	
				imw_query("update surgery_cost set item_cost='".$laborCost."',
					item_id='".$procedureId."',
					item_name='".imw_real_escape_string($procedureName)."',
					item_qty='$surgeon_time',
					item_total_cost='$total_labor_cost',
					datetime='".date('Y-m-d H:i:s')."',
					
					previous_datetime='$pDate',
					previous_cost='".imw_real_escape_string($pCost)."'
					WHERE cost_detail_id='".$pValues['cost_detail_id']."'")or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
					unset($existingArr['Labor'][$procedureName]);
			}
			else
			{
				//insert new
				imw_query("insert into surgery_cost set confirmation_id='".$p_confirmation_id."',
					patient_id='".$patientId."',
					item_type='Labor',
					item_id='".$procedureId."',
					item_name='".imw_real_escape_string($procedureName)."',
					item_cost='".$laborCost."',
					item_qty='".$surgeon_time."',
					item_total_cost='".$total_labor_cost."',
					datetime='".date('Y-m-d H:i:s')."'")or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
			}
		}
		
		//delete mark remaining records
		foreach($existingArr['Labor'] as $name=>$arr)
		{
			$id=$arr['cost_detail_id'];	
			imw_query("update surgery_cost set deleted=1 where cost_detail_id='$id'");
		}
									
		$grand_sup_cost=0;
		//get total supply cost
		$q	=	"select * from operatingroomrecords_supplies where confirmation_id='$p_confirmation_id' and suppChkStatus=1";
		$qSupUsed=imw_query($q)or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());

		while($supUsed=imw_fetch_object($qSupUsed))
		{ 
			$sup_cost=$total_sup_cost=$qty=$insert=0;
			if($supUsed->suppQtyDisplay==0)$qty=1;
			elseif($supUsed->suppList)
			{
				//remove character
				$qty = preg_replace("/[^0-9]/","",$supUsed->suppList);	
			}
			elseif(trim($supUsed->suppList) == '') $qty = 1;
			else $qty=0;
					
			if($qty)
			{
				$sup_cost=$supplyCost[$supUsed->suppName];
				if($sup_cost)
				{
					$total_sup_cost=$supplyCost[$supUsed->suppName]*$qty;
					$grand_sup_cost+=$total_sup_cost;
					if(is_array($existingArr['Supply Used'][$supUsed->suppName]))
					{
						//get previous values
						$pValues=$existingArr['Supply Used'][$supUsed->suppName];
						$pDate=$pValues['datetime'];
						$pCost=serialize($pValues);
						//update existing	
						imw_query("update surgery_cost set item_cost='$sup_cost',
							item_qty='$qty',
							item_total_cost='$total_sup_cost',
							datetime='".date('Y-m-d H:i:s')."',
							
							previous_datetime='$pDate',
							previous_cost='".imw_real_escape_string($pCost)."'
							WHERE cost_detail_id='".$pValues['cost_detail_id']."'")or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
						unset($existingArr['Supply Used'][$supUsed->suppName]);
					}
					else
					{
						$insert="insert into surgery_cost set confirmation_id='$p_confirmation_id',
									patient_id='$patientId',
									item_type='Supply Used',
									item_name='".imw_real_escape_string($supUsed->suppName)."',
									item_cost='$sup_cost',
									item_qty='$qty',
									datetime='".date('Y-m-d H:i:s')."',
									item_total_cost='$total_sup_cost'";
						imw_query($insert)or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
					}
					
		
				}
			}
		}	
		
		
		//delete mark remaining records
		foreach($existingArr['Supply Used'] as $name=>$arr)
		{
			$id=$arr['cost_detail_id'];	
			imw_query("update surgery_cost set deleted=1 where cost_detail_id='$id'");
		}
		
		//START CODE FOR MODEL
		if($model) {
			$model = $this->relpaceNewLine($model);
			if(stripos($model,'~~')!==false) {
				$modelArr = explode('~~',$model);
			}else {
				$modelArr = explode('\n',$model);
			}
			//echo $model;print'<pre>';print_r($modelArr);die;
			foreach($modelArr as $modelName) {
				$modelName 	= trim($modelName);
				$modelCost 	= $modelCostArr[$modelName];
				$modelQty 	= 1;
				$totalModelCost=$modelCost*$modelQty;
				$grand_sup_cost+=$totalModelCost;
				if($modelCost) {
					if(is_array($existingArr['Model'][$modelName])) {
						$pValues=$existingArr['Model'][$modelName];
						$pDate=$pValues['datetime'];
						$pCost=serialize($pValues);
						imw_query("update surgery_cost set item_cost='".$modelCost."',
							item_qty='".$modelQty."',
							item_total_cost='".$modelCost."',
							datetime='".date('Y-m-d H:i:s')."',
							previous_datetime='".$pDate."',
							previous_cost='".imw_real_escape_string($pCost)."'
							WHERE cost_detail_id='".$pValues['cost_detail_id']."'")or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
							unset($existingArr['Model'][$modelName]);
					}else{
						//insert new
						imw_query("insert into surgery_cost set confirmation_id='".$p_confirmation_id."',
							patient_id='".$patientId."',
							item_type='Model',
							item_id='0',
							item_name='".imw_real_escape_string($modelName)."',
							item_cost='".$modelCost."',
							item_qty='".$modelQty."',
							item_total_cost='".$modelCost."',
							datetime='".date('Y-m-d H:i:s')."'")or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
					}
					
				}
				
				//$modelCostArr[trim($modelInfo->name)]=$modelInfo->model_cost;	
			}
		}
		//delete mark remaining records
		foreach($existingArr['Model'] as $name=>$arr)
		{
			$id=$arr['cost_detail_id'];	
			imw_query("update surgery_cost set deleted=1 where cost_detail_id='$id'");
		}
		//END CODE FOR MODEL
		
		imw_query("update patientconfirmation set 
					cost_procedure_id = '".$procedureId."',
					cost_procedure_name = '".imw_real_escape_string($procedureName)."',
					labor_cost='".$total_labor_cost."', 
					supply_cost='".$grand_sup_cost."',
					surgeon_time_in_mins='$surgeon_time',
					surgery_time_in_mins='$surgery_time'
					where patientConfirmationId='$p_confirmation_id'") or die('Error at Line No.('.(__LINE__).') in function '.(__FUNCTION__).': '.imw_error());
		
		//end of getting surgery total time
		}
	}

	//START FUNCTION TO STORE FILE IN CSV FORMAT
	function download_file($file_name='', $display_name=''){
		if($display_name==''){
			$display_name= end(explode("/",$file_name));
		}
		$filename = $file_name;
		$content_type = "text/csv";
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		
		header("Cache-Control: private",false);
		header("Content-Description: File Transfer");
		
		header("Content-Type: ".$content_type."; charset=utf-8");
		//die();
		header("Content-disposition:attachment; filename=\"".$display_name."\"");
		
		header("Content-Length: ".@filesize($filename));
		//echo filesize($filename);
		@readfile($filename) or die("File not found.");
		exit;	
	}
	//END FUNCTION TO STORE FILE IN CSV FORMAT

	
	public function injectionProfile($procedureID='',$surgeonID='',$fields = '*' )
	{
		$data	=	array();
		$data['profileFound']	=	false;
		$fields	=	(trim($fields)) ?	$fields : '*';
		$profileDataQry	=	"Select ".$fields." From inj_misc_procedure_template Where procedureID = '".$procedureID."' And FIND_IN_SET('".$surgeonID."',surgeonID) Order By templateID Desc Limit 1 ";
		$profileDataSql	=	imw_query($profileDataQry) or die('Error Found in function '.(__FUNCTION__).' at line no. '.(__LINE__).imw_error());
		$profileDataCnt	=	imw_num_rows($profileDataSql);
		$profileFound		=	($profileDataCnt > 0) ? true : false;
		
		if(!$profileFound)
		{
			$profileDataQry	=	"Select ".$fields." From inj_misc_procedure_template Where procedureID='".$procedureID."' And surgeonID = 'all' Order By templateID Desc Limit 1 ";
			$profileDataSql	=	imw_query($profileDataQry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
			$profileDataCnt	=	imw_num_rows($profileDataSql);
			$profileFound		=	($profileDataCnt > 0) ? true : false;
		}
		
		if($profileFound)
		{
				$data['profileFound']	=	$profileFound;
				$profileDataRow	=	imw_fetch_assoc($profileDataSql);
				$data['data']	=	$profileDataRow ;
		}
		return $data;	
	
	}
	
	public function validateGroupOR($fieldsArr=array())
	{
			$status	=	false;
			if(is_array($fieldsArr) && count($fieldsArr) > 0 )
			{
					foreach($fieldsArr as $key=>$val)
					{
							if($val) { $status = true; }
					}
			}
			
			return $status;
	}
	
	public function validateGroupAND($fieldsArr=array())
	{
			$status	=	true;
			if(is_array($fieldsArr) && count($fieldsArr) > 0 )
			{
					foreach($fieldsArr as $key=>$val)
					{
							if(!$val) { $status = false; }
					}
			}
			return $status;
	}
	
	public function loadSettings($fields = '*')
	{
			$result	=	$this->getExtractRecord('surgerycenter','surgeryCenterId','1',$fields);
			return $result;
	}
	
	public function loadVitalSignGridStatus($currentFormStatus='',$vitalSignGridStatus='',$pageName='')
	{
			$pagesAllowed	=	array('oproom','macAnes','genAnes','transferFollowup');
			if(in_array($pageName,$pagesAllowed) && ($currentFormStatus <> 'completed' && $currentFormStatus <> 'not completed') )
			{
				$fieldName	=	'vital_sign_'.$pageName;
				$settings	=	$this->loadSettings($fieldName);
				$vitalSignGridStatus	=	($settings[$fieldName] == 'Y') ? 1 : 0;
				
			}
			return $vitalSignGridStatus;
	}
	
	function getPracMatch($loginUsrPracName='', $srgPracName='') {
		$pracMatch = "";
		if($loginUsrPracName && $srgPracName) {
			$loginUsrPracNameArr 	= explode(",",$loginUsrPracName);
			$srgPracNameArr 		= explode(",",$srgPracName);
			if(array_intersect($loginUsrPracNameArr,$srgPracNameArr)) {
				$pracMatch = "yes";	
			}
		}
		return $pracMatch;
	}
	
	function getPracMatchUserId($userId1='',$userId2='') {
		$pracIdMatch='';
		if($userId1 && $userId2 && ($userId1 != $userId2)) {
			$userIdQry 			= "SELECT practiceName FROM users WHERE  usersId in('$userId1','$userId2')";
			$userIdRes 			= imw_query($userIdQry);
			$practiceNameArr 	= array();
			if(imw_num_rows($userIdRes)>0) {
				while($userIdRows = imw_fetch_array($userIdRes)) {
					$practiceNameArr[] = $userIdRows['practiceName'];
				}
				$pracIdMatch	=	$this->getPracMatch($practiceNameArr[0],$practiceNameArr[1]);
			}
		}
		return $pracIdMatch;
	}
	
	function loadPracticeUser($practiceName='') {
			
			$fieldName 	= "practiceName";
			$data				=	array();
			$subQryTxt .= " And $fieldName = '' ";
			if($practiceName){
				$practiceNameArr = array_filter(explode(",",$practiceName));
				if(is_array($practiceNameArr) && count($practiceNameArr) > 0 )
				{
					$subQryTxt	=	" And (";
					foreach($practiceNameArr as $prNme)
					{
						$subQryTxt .= " $fieldName REGEXP '[[:<:]]".$prNme."[[:>:]]' OR ";
					}
					$subQryTxt = substr($subQryTxt,0,-4);
					$subQryTxt.=	" )";
				}
			}
	
			$userQry		=	"Select fname,mname,lname From users U Where user_type = 'Surgeon' ".$subQryTxt ;
			$userSql	=	imw_query($userQry) or die('Error Found in function '.(__FUNCTION__).' at line no. '.(__LINE__).imw_error());
			$userCnt	=	imw_num_rows($userSql);
			if($userCnt > 0 )
			{
				while($userRow	=	imw_fetch_assoc($userSql))
				{
					array_push($data,$userRow);
				}
			
			}
			return $data;
	}
	
	
	public function getInjectionMiscProcCategories()
	{
			$qry	=	"Select GROUP_CONCAT(proceduresCategoryId) as injMiscCatIds From procedurescategory Where (isMisc = '1' OR isInj = '1') AND del_status !='yes' Order By name Asc";
			$sql	=	imw_query($qry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
			$row	=	imw_fetch_assoc($sql);
			
			$injMiscCatIds	=	$row['injMiscCatIds'];
			
			return $injMiscCatIds;
			
	}	

	public function getInjectionMiscProcedures()
	{
			$data		=	array();
			$catIds	=	$this->getInjectionMiscProcCategories();
			$data		=	$this->getMultiChkArrayRecords("procedures", array('1'=>'1'),"name","ASC"," And catId IN (".$catIds.") AND del_status !='yes' ");
			
			return $data;
			
	}
	
	public function verifyProcIsInjMisc($procedureId='')
	{
			$return =	'';
			if($procedureId)
			{
				$qry	=	"Select PC.isMisc, PC.isInj From procedures P Join procedurescategory PC on P.catId = PC.proceduresCategoryId Where procedureId = '".$procedureId."' ";
				$sql	=	imw_query($qry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error());
				$row	=	imw_fetch_assoc($sql);
			
				$isMisc	=	$row['isMisc'];
				$isInj	=	$row['isInj'];
				if($isInj) 			$return	=	'injection';
				elseif($isMisc) $return = 'misc';
			}
			return $return;		
			
	}

	public function signatureHTML($tblName='', $signOf='', $loggedInUserId='', $formName='', $ajaxFileName='', $confirmationId='', $confirmationIdField = 'confirmation_id')
	{
		
		$signatureHTML	=	'';
		$smallSignOf		=	strtolower($signOf);
		$userToMatch		=	'';
		$sigTitle				=	'';
		
		if(stristr($signOf,'Nurse'))	{	
			$userToMatch	=	'Nurse';
			$sigTitle		=	'Nurse';
		}
		else if(stristr($signOf,'Anesthesiologist'))
		{
			$userToMatch	=	'Anesthesiologist';
			$sigTitle		=	'Anesthesia Provider';
		}
		if(stristr($signOf,'Surgeon'))
		{
			$userToMatch	=	'Surgeon';
			$sigTitle		=	'Surgeon';
		}
			
		if($userToMatch)
		{
				
				// Get Signature Fields Value from Database
				$signatureFields	=	"sign".$signOf."Id as signId, sign".$signOf."FirstName as signFirstName , sign".$signOf."MiddleName as signMiddleName, sign".$signOf."LastName as signLastName, sign".$signOf."Status as signStatus, sign".$signOf."DateTime as signDateTimeFormat";
				$getSigDetails	=	$this->getExtractRecord($tblName,	$confirmationIdField,$confirmationId,$signatureFields);
				extract($getSigDetails);
				// Get Signature Fields Value from Database
				
				
				//Get Signed User Detail
				$getSignedUserDetails		=	$this->getExtractRecord('users','usersId',$signId);
				//$signedInUserFirstName	= $getSignedUserDetails["fname"];
				//$signedInUserMiddleName = $getSignedUserDetails["mname"];
				//$signedInUserLastName		= $getSignedUserDetails["lname"];
				//$signedInUserIdAdd 			= $getSignedUserDetails["usersId"];
				//$signedInUserName 			= $getSignedUserDetails["lname"].", ".$getSignedUserDetails["fname"]." ".$getSignedUserDetails["mname"];
				$signedInUserType 			= $getSignedUserDetails["user_type"];
				$signedInUserSubType 		= $getSignedUserDetails["user_sub_type"];
				//End Get Signed In User Detail
				
				
				//Get Logged In User Detail
				$getloggedUserDetails		=	$this->getExtractRecord('users','usersId',$loggedInUserId);
				$loggedInUserFirstName	= $getloggedUserDetails["fname"];
				$loggedInUserMiddleName = $getloggedUserDetails["mname"];
				$loggedInUserLastName		= $getloggedUserDetails["lname"];
				$loggedInUserIdAdd 			= $getloggedUserDetails["usersId"];
				$loggedInUserName 			= $getloggedUserDetails["lname"].", ".$getloggedUserDetails["fname"]." ".$getloggedUserDetails["mname"];
				$loggedInUserType 			= $getloggedUserDetails["user_type"];
				$loggedInUserSubType 		= $getloggedUserDetails["user_sub_type"];
				//End Get Logged In User Detail
				
				$signUserPreFix = '';
				if($signId && ($signedInUserType == 'Surgeon' || ($signedInUserType == 'Anesthesiologist' && $signedInUserSubType <> 'CRNA')) )
				{
						$signUserPreFix = 'Dr.';
				}
				elseif(($loggedInUserType == 'Surgeon'  || ($loggedInUserType == 'Anesthesiologist' && $loggedInUserSubType <> 'CRNA')) && !$signId )
				{
					$signUserPreFix = 'Dr.';
				}
				
				
				// Onclick Function 
				if($loggedInUserType <> $userToMatch) 
				{
					$callJavaFun = "return noAuthorityFunCommon('".$userToMatch."');";
				}
				else
				{
					$callJavaFun	= "document.".$formName.".hiddSignatureId.value='TD".$smallSignOf."SignatureId'; return displaySignature('TD".$smallSignOf."NameId','TD".$smallSignOf."SignatureId','".$ajaxFileName."','".$loggedInUserId."','".$signOf."');";
				}
				
				// Display Text
				$signOnFileStatus 		= "Yes";
				$TDNameIdDisplay			= "block";
				$TDSignatureIdDisplay = "none";
				$signUserName 				= $loggedInUserName;
				$signDateTimeFormatNew= $this->getFullDtTmFormat(date("Y-m-d H:i:s"));
				
				if($signId<>0 && $signId<>"")
				{
						$signUserName = $signLastName.", ".$signFirstName." ".$signMiddleName;
						$signOnFileStatus = $signStatus;	
						$TDNameIdDisplay = "none";
						$TDSignatureIdDisplay = "block";
						//$signDateTimeFormatNew = date("m-d-Y h:i A", strtotime($signDateTimeFormat));
						$signDateTimeFormatNew = $this->getFullDtTmFormat($signDateTimeFormat);
				}
	
				// Onclick Delete Functions
				if($loggedInUserId == $signId)
				{
						$callJavaFunDel = "document.".$formName.".hiddSignatureId.value='TD".$smallSignOf."NameId'; return displaySignature('TD".$smallSignOf."NameId','TD".$smallSignOf."SignatureId','".$ajaxFileName."','".$loggedInUserId."','".$signOf."','delSign');";
				}
				else
				{
						$callJavaFunDel = "alert('Only ".addslashes($signUserName)." can remove this signature');";
				}
				
				$signBackColor 	 = 'background-color:'.(($signId > 0 ) ? '#FFFFFF' : '#F6C67A').'; ';
				
		
				$signatureHTML	.=	'<div class="inner_safety_wrap" id="TD'.$smallSignOf.'NameId" style="display:'.$TDNameIdDisplay.';">';
				$signatureHTML	.=	'<a href="javascript:void(0);" class="sign_link" style="cursor:pointer;'.$signBackColor.'" onClick="javascript:'.$callJavaFun.'"> '.$sigTitle.'  Signature </a>';
				$signatureHTML	.=	'</div>';
				
				$signatureHTML	.=	'<div class="inner_safety_wrap collapse" id="TD'.$smallSignOf.'SignatureId" style="display:'.$TDSignatureIdDisplay.';">';
				$signatureHTML	.=	'<span class="rob full_width"><a href="javascript:void(0);" class="sign_link" style="cursor:pointer;" onClick="javascript:'. $callJavaFunDel.'"> <b>'.$sigTitle.':</b> '.$signUserPreFix.'&nbsp;'.$signUserName .'</a></span>';
				$signatureHTML	.=	'<span class="rob full_width"> <b> Electronically Signed: </b> '.$signOnFileStatus.'</span>';
				$signatureHTML	.=	'<span class="rob full_width">';
				$signatureHTML	.=	'<b> Signature Date :</b>';
				if($sigTitle === 'Nurse' || $sigTitle === 'Surgeon')
				{		
						$faSurgClass = "";
						if($sigTitle == 'Surgeon') {
							$faSurgClass = "fa-editsurg";	
						}
						$signatureHTML	.=	'<span class="dynamic_sig_dt" data-field-name="sign'.$signOf.'DateTime" data-table-name="'.$tblName.'" data-id-value="'.$confirmationId.'" data-id-name="'.$confirmationIdField.'">'.$signDateTimeFormatNew.' ';
						$signatureHTML	.=	'<span class="fa fa-edit '.$faSurgClass.'"></span>';
						$signatureHTML	.=	'</span>';
						$signatureHTML	.=	'</span>';
				}
				else
				{
						$signatureHTML	 .=	$signDateTimeFormatNew;
				}
				$signatureHTML	.=	'</div>';
				
		}
		
		return $signatureHTML;
	}
		
		
	function containsNumbers($string=''){
		return preg_match('/\\d/', $string) > 0;
	}
		
	function extract_numbers($string='')
	{
		preg_match_all('/([\d]+)/', $string, $match);
		return $match[0];
	}
	
	function userInitialsArrFun() {
		$userInitialsArr = array();
		$userInitialsDetail = $this->getArrayRecords("users", 1, 1, "", "", " AND deleteStatus !='Yes' AND initial !='' ");
		if($userInitialsDetail) {
			foreach($userInitialsDetail as $key => $userInitialsVal){
				$usersId = $userInitialsVal->usersId;
				$initial = trim(strtolower($userInitialsVal->initial));
				$userInitialsArr["user_id"][$initial] 		= $usersId;
				
				$userInitialsArr["user_fname"][$usersId] 	= $userInitialsVal->fname;
				$userInitialsArr["user_mname"][$usersId] 	= $userInitialsVal->mname;
				$userInitialsArr["user_lname"][$usersId] 	= $userInitialsVal->lname;
				$userInitialsArr["user_initial"][$usersId] 	= $userInitialsVal->initial;
				
			}
		}
		return $userInitialsArr;
	}
	
	public function calculate_narcotics_data($confirmation_id='', $userInitialsArr = array())
	{
		
		if(!$confirmation_id)  return;
		
		//START CODE TO DELETE RECORD IF ALREADY EXISTS
		$delMedQry = "DELETE FROM narcotics_data_tbl WHERE confirmation_id = '".$confirmation_id."' AND confirmation_id !='0' AND confirmation_id !=''";
		$delMedRes = imw_query($delMedQry) or die($delMedQry.imw_error());
		//END CODE TO DELETE RECORD IF ALREADY EXISTS
		if(count($userInitialsArr)=='0') {
			$userInitialsArr = $this->userInitialsArrFun();	
		}
		//print'<pre>';print_r($userInitialsArr);exit;
		
		$space = ' ';
		$fieldsArr	= array('lm.blank1_label' => 'lm.blank1', 'lm.blank2_label' => 'lm.blank2', 'lm.blank3_label' => 'lm.blank3',
												'lm.blank4_label' => 'lm.blank4', 'lm.mgPropofol_label' => 'lm.propofol',
												'lm.mgMidazolam_label' => 'lm.midazolam', 'lms.mgKetamine_label' => 'lms.ketamine',
												'lms.mgLabetalol_label' => 'lms.labetalol','lms.mcgFentanyl_label' => 'lms.Fentanyl');
											
		$fields	=	implode(",",array_keys($fieldsArr));
		foreach($fieldsArr as $fieldName)
		{
			for($i = 1; $i <= 20; $i++)
				$fields	.=	','.$fieldName.'_'.$i;
		}
		$fields=	($fields) ? $fields : '*';
		
		$qry	=	"Select p.anesthesiologist_id AS assignedAnesId, ".$fields." From localanesthesiarecord l 
					INNER JOIN patientconfirmation p ON (p.patientConfirmationId = l.confirmation_id)
					INNER JOIN stub_tbl st ON (st.patient_confirmation_id = l.confirmation_id AND st.chartSignedByAnes = 'green')
					LEFT JOIN localanesthesiarecordmedgrid lm ON p.patientConfirmationId = lm.confirmation_id
					LEFT JOIN localanesthesiarecordmedgridsec lms ON p.patientConfirmationId = lms.confirmation_id
					Where l.confirmation_id = '".$confirmation_id."' LIMIT 0,1";
		$sql	=	imw_query($qry) or die('Error Found in function '.(__FUNCTION__). ' at line no. '.(__LINE__).imw_error()."\n".$qry);
		$cnt	=	imw_num_rows($sql);
		if($cnt > 0 )
		{
			$row	=	imw_fetch_object($sql);
			$assignedAnesId = $row->assignedAnesId;
			$medArr = array();
			foreach($fieldsArr as $labelFieldName => $dataFieldName)
			{
				$labelFieldName = str_ireplace("l.","",$labelFieldName);
				$labelFieldName = str_ireplace("lm.","",$labelFieldName);
				$labelFieldName = str_ireplace("lms.","",$labelFieldName);
				$dataFieldName 	= str_ireplace("l.","",$dataFieldName);
				$dataFieldName 	= str_ireplace("lm.","",$dataFieldName);
				$dataFieldName 	= str_ireplace("lms.","",$dataFieldName);
				$labelFieldValue	=	$row->$labelFieldName;
				if($labelFieldValue)
				{
					if(!array_key_exists($labelFieldValue,$medArr)) $medArr[$labelFieldValue] = array();
					for($loop = 1; $loop <= 20; $loop++)
					{
							$intFieldName = $intFieldValue = '';
							$intFieldName	=	$dataFieldName.'_'.$loop;
							list($intFieldValue,$intFieldTime) = explode("@@",trim($row->$intFieldName));
							$intFieldValue = trim($intFieldValue);
							//$intFieldValue = '2-LG, 7,4-MM';
							$tempVal = '';
							$tempProv = '';
							$tempValChar = '';
							$tempHold = 0;
							$tempValArr = $tempValCharArr = array();
							$isCharacter = $isNumeric = false;
							$tempCurrValue = '';
							$tempNextValue = '';
							if($intFieldValue) {
								$intFieldValue	=	trim(preg_replace("/[^a-zA-Z0-9.]/",$space,$intFieldValue));
								//echo '<br>'.$intFieldValue;
								for($k = 0; $k < strlen($intFieldValue) ; $k++ )
								{
									$tempCurrValue = $intFieldValue[$k];
									$tempNextValue = $intFieldValue[$k+1];
									if(is_numeric($tempCurrValue) || $tempCurrValue == '.')
									{
										$isNumeric= true;
										$tempVal .= $tempCurrValue;
									}
									else
									{
										$isCharacter	 = true;
										$tempValChar	.=	$tempCurrValue;
									}
									
									
									if((((is_numeric($tempNextValue) || $tempNextValue == '.') && $isCharacter) || empty($tempNextValue)) && trim($tempValChar) )
									{
										$tempValCharArr[$tempHold] = trim($tempValChar);
										$tempValChar = '';
									}
									else if($isNumeric && $tempNextValue <> '.' && !is_numeric($tempNextValue) && trim($tempVal))
									{
										$tempValArr[] = trim($tempVal);
										$tempHold = (count($tempValArr)-1);
										$tempVal = "";
									}
									
								}
								
								$medArr[$labelFieldValue][] = array('qty'=>$tempValArr,'provider'=>$tempValCharArr);
								//print'<pre>'; print_r($tempValArr); print_r($tempValCharArr);
							}
					}
				}
			}
			//print'<pre>'; print_r($medArr);
			//START ASSIGNING MEDICATION WITH QUANTITY TO ANESTHESIOLOGIST
			if(count($medArr)>0) {
				foreach($medArr as $medKey => $medValArr) {
					$medName = 	$medKey;
					foreach($medValArr as $medValInn) {
						$tempMedQty = $medValInn['qty'];
						$tempMedProv = $medValInn['provider'];
						foreach($tempMedQty as $key =>$medQty)
						{
							$medProv = trim(strtolower($tempMedProv[$key]));
							$medProvId = trim($userInitialsArr["user_id"][$medProv]);
							if(!$medProvId) {
								$medProvId = $assignedAnesId;
							}
							
							$medProvIntial 			= $userInitialsArr["user_initial"][$medProvId];
							$medProvFirstName 	= addslashes($userInitialsArr["user_fname"][$medProvId]);
							$medProvMiddleName 	= addslashes($userInitialsArr["user_mname"][$medProvId]);
							$medProvLastName 		= addslashes($userInitialsArr["user_lname"][$medProvId]);
	
							if($medName && $medQty > 0 && $medProvId) {
								$insQry = "INSERT INTO narcotics_data_tbl SET
										   confirmation_id 	= '".$confirmation_id."',
										   medicine_name 	= '".$medName."',
										   quantity 		= '".$medQty."',
										   user_id 			= '".$medProvId."',
										   user_initial 	= '".$medProvIntial."',
										   user_fname 		= '".$medProvFirstName."',
										   user_mname 		= '".$medProvMiddleName."',
										   user_lname 		= '".$medProvLastName."',
										   created_date 	= '".date("Y-m-d H:i:s")."'";
								//echo '<br>'.$insQry;
								$insRes = imw_query($insQry) or die($insRes.imw_error());
							}
						}
							
					}
					
				}
			}
			//END ASSIGNING MEDICATION WITH QUANTITY TO ANESTHESIOLOGIST
		}
	}

	function superBillProcIdArrFun() {
		//START GET PROCEDURE ID FROM SUPERBILL TABLE
		$superBillProcIdArr = array();
		$procCatIdInjMisc = 0;
		$cptCatQry = "SELECT GROUP_CONCAT(proceduresCategoryId) AS procCatIdInjMisc FROM procedurescategory WHERE `name` IN('Injection','Miscellaneous','Cataract') ORDER BY proceduresCategoryId LIMIT 0,3";
		$cptCatRes = imw_query($cptCatQry);
		if(imw_num_rows($cptCatRes)>0) {
			$cptCatRow = imw_fetch_assoc($cptCatRes);
			$procCatIdInjMisc = $cptCatRow['procCatIdInjMisc'];
		}
		$superBillProcQry = "SELECT s.* FROM superbill_tbl s INNER JOIN procedures p ON(p.procedureId = s.cpt_id and p.catId IN(1,2,".$procCatIdInjMisc.")) WHERE s.confirmation_id != '0' AND s.deleted = '0' ORDER BY s.confirmation_id";
		$superBillProcRes = imw_query($superBillProcQry);
		if(imw_num_rows($superBillProcRes)>0) {
			while($superBillProcRow = imw_fetch_assoc($superBillProcRes)) {
				$superBillConfId = $superBillProcRow["confirmation_id"];
				if( !array_key_exists($superBillConfId,$superBillProcIdArr)) $superBillProcIdArr[$superBillConfId] = array();
				if ( !in_array($superBillProcRow["cpt_id"],$superBillProcIdArr[$superBillConfId]) )
					$superBillProcIdArr[$superBillConfId][] = $superBillProcRow["cpt_id"];	
			}
		}//print'<pre>';print_r($superBillProcIdArr);
		
		return $superBillProcIdArr;
		//END GET PROCEDURE ID FROM SUPERBILL TABLE
	}

	function vcnaPatientModal($pDetail='')
	{
		$html = '';
		$html .= '<div id="pDetail" class="modal fade" role="dialog" style="top:25px;">';
		$html .= '<div class="modal-dialog modal-lg">';
		//Modal content-->
		$html .= '<div class="modal-content">';
		$html .= '<div class="modal-header">';
		$html .= '<button type="button" class="close" data-dismiss="modal">&times;</button>';
		$html .= '<h4 class="modal-title">Patient Details</h4>';
		$html .= '</div>';
		$html .= '<div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">';
		$html .= '<table class = "table table-bordered">';
		$html .= '<thead>';
		$html .= '<tr class="bg-primary">';
		$html .= '<th class="col-md-1 col-sm-1 col-xs-1 col-lg-1">Sr.</th>';
		$html .= '<th class="col-md-3 col-sm-3 col-xs-3 col-lg-3">Patient Name -ID</th>';
		$html .= '<th class="col-md-2 col-sm-2 col-xs-2 col-lg-2">DOS</th>';
		$html .= '<th class="col-md-5 col-sm-5 col-xs-5 col-lg-5">Procedures</th>';
		$html .= '</tr>';
		$html .= '</thead>';
		$html .= '<tbody>';
		
		$tmpCounter = 0;
		if( is_array($pDetail) && count($pDetail) > 0 )
			{
			foreach( $pDetail as $tmpSurgeon => $tmp )
			{
				$sCount = count($tmp);
				$html .= '<tr><td colspan="4" class="bg bg-info"><b>'.$tmpSurgeon.'<b>&nbsp;<span class="badge bg-primary">'.$sCount.'</span></td></tr>';
				foreach($tmp as $tmpDetail)
				{
					$tmpCounter++;
					$html .= '<tr>';
					$html .= '<td>'.$tmpCounter.'</td>';
					$html .= '<td>'.$tmpDetail['name'].'</td>';
					$html .= '<td>'.date('m/d/Y',strtotime($tmpDetail['dos'])).'</td>';
					$html .= '<td>'.$tmpDetail['procedures'].'</td>';
					$html .= '</tr>';
				}
			}
		}
		
		if( $tmpCounter == 0)
		{
			$html .= '<tr><td colspan="4" class="bg-info text-center">No Rcord Found</td></tr>';
		}
		$html .= '</tbody>';
		$html .= '</table>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		
		return $html;
	}

	//START FUNCTION TO GET STATUS OF USERS
	function getUserStatus($passChangedLatDate='',$maxDaysToExpire='') {
		$status='Active';
		$today = date('Y-m-d');
		if($today!=$passChangedLatDate){
			$differanceBetween	=	$this->getDateDifferance($today, $passChangedLatDate);
			$expireDaysLeft		=	$maxDaysToExpire-$differanceBetween;
			
			if($expireDaysLeft <= 0){
				$status='Expired';
			}else if(($expireDaysLeft >= 1) && ($expireDaysLeft<=7)){
				$status='Expire after '.$expireDaysLeft.'days.';
			}else{
				$status='Active';
			}
		}
		else{
				$status='Active';
		}
		return $status;		
	}
	//END FUNCTION TO GET STATUS OF USERS

	function getImedicDirPath($imwPatientId='',$folderName='') {
		$rootServerPath = $_SERVER['DOCUMENT_ROOT'];
		global $imwDirectoryName;
		global $imwPracticeName;
		$imwPath = $rootServerPath.'/'.$imwDirectoryName.'/interface/main/uploaddir/PatientId_'.$imwPatientId;
		if(trim($imwPracticeName)) {
			$imwPath = $rootServerPath.'/'.$imwDirectoryName.'/data/'.$imwPracticeName.'/PatientId_'.$imwPatientId;
		}
		if(!is_dir($imwPath)){		
			mkdir($imwPath);
		}
		if(trim($folderName)) {
			$imwPath = $imwPath.'/'.$folderName;
			if(!is_dir($imwPath)){		
				mkdir($imwPath);
			}
		}
		return $imwPath;
	}
	
	function getSiteNo($site=''){
		
		$site = trim($site);
		$confSiteNo = 0;
		if( $site ) {
				
				if($site=='left') 		{ $confSiteNo=1;
				}else if($site=='right'){ $confSiteNo=2;
				}else if($site=='both') { $confSiteNo=3;
				}else if($site=='left upper lid')  { $confSiteNo=4;
				}else if($site=='left lower lid')  { $confSiteNo=5;
				}else if($site=='right upper lid') { $confSiteNo=6;
				}else if($site=='right lower lid') { $confSiteNo=7;
				}else if($site=='bilateral upper lid') { $confSiteNo=8;
				}else if($site=='bilateral lower lid') { $confSiteNo=9;
				}
				return $confSiteNo;
		}
		
		return false;
	}
	
	
	function procedureBaseSiteNo($procedureName=''){
		
		if( trim($procedureName) ){
				$siteTemp = substr(trim($procedureName),-2,2); //READ LAST TWO CHARACTERS OF PRIMARY PROCEDURE EXCLUDING SPACE
				if($siteTemp=='OS') {
					$procedureBasedSite = 'left';
					$procedureBasedConfSiteNo=1;
					$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTemp=='OD') {
					$procedureBasedSite = 'right';
					$procedureBasedConfSiteNo=2;
					$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTemp=='OU') {
					$procedureBasedSite = 'both';
					$procedureBasedConfSiteNo=3;
					$procedureName = trim(str_replace($siteTemp,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}
				
				$siteTempNew = substr(trim($procedureName),-3,3); //READ LAST THREE CHARACTERS OF PRIMARY PROCEDURE EXCLUDING SPACE
				if($siteTempNew=='LUL') {
					$procedureBasedSite = 'left upper lid';
					$procedureBasedConfSiteNo=4;
					$procedureName = trim(str_replace($siteTempNew,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTempNew=='LLL') {
					$procedureBasedSite = 'left lower lid';
					$procedureBasedConfSiteNo=5;
					$procedureName = trim(str_replace($siteTempNew,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTempNew=='RUL') {
					$procedureBasedSite = 'right upper lid';
					$procedureBasedConfSiteNo=6;
					$procedureName = trim(str_replace($siteTempNew,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTempNew=='RLL') {
					$procedureBasedSite = 'right lower lid';
					$procedureBasedConfSiteNo=7;
					$procedureName = trim(str_replace($siteTempNew,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTempNew=='BUL') {
					$procedureBasedSite = 'bilateral upper lid';
					$procedureBasedConfSiteNo=8;
					$procedureName = trim(str_replace($siteTempNew,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}else if($siteTempNew=='BLL') {
					$procedureBasedSite = 'bilateral lower lid';
					$procedureBasedConfSiteNo=8;
					$procedureName = trim(str_replace($siteTempNew,'',$procedureName)); //REMOVE LAST TWO CHARACTERS EXCLUDING SPACE
				}
				$procedureName = addslashes($procedureName);
				
				return array('site'=>$procedureBasedSite, 'site_no'=>$procedureBasedConfSiteNo, 'proc_name'=>$procedureName );
				
		}
		
		return false;
	}
	
	
	function setTmFormat($timeValue='',$tmType = '') {
		//$timeValue = obj.value;
		$tFlag=false;
		$tmpArr = explode(':',$timeValue);
		if(strlen($timeValue)==8 || !$timeValue) { 
			if(!$timeValue) {return; }
			
			$HH = substr($tmpArr[0],0,2);
			//$HH = $timeValue.explode(':')[0].substr(0,2);
			$MM = (int)(substr($tmpArr[1],0,2));
			//$MM = (int)($timeValue.explode(':')[1].substr(0,2));
			if($HH>24) {$tFlag=true;}	
			if($MM>59) {$tFlag=true;}	
		}
		$MM = '00';
		if(strlen($timeValue) >=1){
			$HH = substr($timeValue,0,2);
			if(stristr($timeValue,':')) {
				$HH = substr($tmpArr[0],0,2);
				//$HH = $timeValue.explode(':')[0].substr(0,2);
				$MM = (int)(substr($tmpArr[1],0,2));
				//$MM = (int)($timeValue.explode(':')[1].substr(0,2));
			}
			if($HH>24 || strlen($timeValue)<=3) { 
				$HH = substr($timeValue,0,1);
				//$HH = $timeValue.substr(0,1); 
				$MM = (int)(substr($timeValue,1,2));
				//$MM = (int)($timeValue.substr(1,2));
	
				if((int)substr($timeValue,0,2) <= 24) {
					$HH = substr($timeValue,0,2);
					$MM = (int)(substr($timeValue,2,1));	
				}
				
				if(stristr($timeValue,':')) {
					$HH = substr($tmpArr[0],0,2);
					//$HH = $timeValue.explode(':')[0].substr(0,2);
					if($HH>24) { $HH = substr($tmpArr[0],0,1); }
					//if($HH>24) { $HH = $timeValue.explode(':')[0].substr(0,1); }
					$MM = (int)(substr($tmpArr[1],0,2));
					//$MM = (int)($timeValue.explode(':')[1].substr(0,2));
				}
				if($HH==0) {
					$HH = substr($timeValue,0,2);
					//$HH = $timeValue.substr(0,2); 
					$MM = (int)(substr($timeValue,2,2));
					//$MM = (int)($timeValue.substr(2,2)); 
				}
				if($MM <= 9 && $MM!=0) {$MM = '0'.$MM; }
				if($MM=='' || $MM==0) {$MM = '00';}
			}
			if($HH=='') { $HH = '00';}
			if($HH <= 9 && strlen($HH)==1) {$HH = '0'.(int)($HH);}
		}else{
			$HH = '00';
		}
		if(strlen($timeValue) >3){
			if($MM=='00') {
				$MM = (int)(substr($timeValue,2,2));
				//$MM = (int)($timeValue.substr(2,2));
			}
			if(stristr($timeValue,':')) {
				$MM = (int)(substr($tmpArr[1],0,2));
				//$MM = (int)($timeValue.explode(':')[1].substr(0,2));
			}
			if($MM <= 9 && strlen($MM)==2) { $MM = (int)(substr($MM,1,1)); }
			if(($MM <= 9 && $MM!=0) || strlen($MM)==1){ $MM = '0'.$MM; }
			if($MM=='' || $MM==0) {$MM = '00'; }
		}else{
			//$MM = '00';
		}
		$tFlagPM=true;
		if($HH>12){
			$tFlagPM=false;
			$HH = $HH- 12;
			if($HH <= 9 && $HH!=0){
				$HH = '0'.$HH;
			}
		}
		//if(($HH >= 7 && $HH <= 11 && $tFlagPM==true))
		if(($HH <= 11 && $tFlagPM==true)) {
			$Suffix = 'AM';
		}else{
			$Suffix = 'PM';
		}
		if(stristr($timeValue,'A')) { $Suffix = 'AM'; }
		//if($timeValue.search('a')>=0 || $timeValue.search('A')>=0) {$Suffix = 'AM'; }
		if(stristr($timeValue,'P')) { $Suffix = 'PM'; }
		//if($timeValue.search('p')>=0 || $timeValue.search('P')>=0) {$Suffix = 'PM'; }
		if(!is_numeric($HH))  { $HH = '00';	}
		if(!is_numeric($MM))  {	$MM = '00'; }
		
		if($MM>59 || $HH=='00' || $tFlag==true) { $timeValue='';return;}
		$timeValue = $HH.':'.$MM.' '.$Suffix;
		$timeValue = date("H:i:s", strtotime($timeValue));	
		if($tmType == 'static') {
			$timeValue = $HH.':'.$MM.' '.$Suffix;
		}
		if($HH=='00' && $MM == '00') { return;  }
		return $timeValue;
	}
	
	function getTmFormat($timeValue='') {
		if(!trim($timeValue) || $timeValue == "00:00:00" || $timeValue == "00:00") {return; }
		if( strtotime($timeValue) === false ) return $timeValue;
		$timeValueShow = date("h:i A", strtotime($timeValue));
		if(constant("SHOW_MILITARY_TIME")=="YES") {
			$timeValueShow = date("H:i", strtotime($timeValue));
		}
		if(trim(substr($timeValueShow,0,5))=='00:00') {return; }
		return $timeValueShow;
	}
	
	function getFullDtTmFormat($dtTimeValue='') {
		if(!trim($dtTimeValue) || trim($dtTimeValue)=="0000-00-00 00:00:00" || trim($dtTimeValue)=="0000-00-00 00:00") {return; }
		$dtTimeValueShow = date("m-d-Y h:i A", strtotime($dtTimeValue));
		if(constant("SHOW_MILITARY_TIME")=="YES") {
			$dtTimeValueShow = date("m-d-Y H:i", strtotime($dtTimeValue));
		}
		return $dtTimeValueShow;
	}
	
	function getFullDtTmFormatLocalAnes($dtTimeValue='') {
		if(!trim($dtTimeValue)) {return; }
		$dtTimeValueShow = date("m/d/Y h:i:s A", strtotime($dtTimeValue));
		if(constant("SHOW_MILITARY_TIME")=="YES") {
			$dtTimeValueShow = date("m/d/Y H:i:s", strtotime($dtTimeValue));
		}
		return $dtTimeValueShow;
	}
	
	//Execute mysql sql qry and return result or false on failure; used in wv
	function sqlQuery ($statement=''){
		$query = imw_query($statement) or die("query failed: $statement (" . imw_error() . ")");
		if(is_bool($query)===false){	
			$rez = imw_fetch_assoc($query);
		}
		elseif( is_bool($query) === TRUE )
		{
			$rez = $query;
		}
		if ($rez == FALSE) return FALSE;
		
		return $rez;
	}
	
	function proc_site_modifiers($pid='', $dos='', $pri_site_id='', $pri_proc_id='', $sec_site_id = '',$sec_proc_id = '', $ter_site_id = '', $ter_proc_id = ''){
		
		$pri_proc_id = (int)$pri_proc_id; $sec_proc_id = (int)$sec_proc_id; $ter_proc_id = (int)$ter_proc_id;
		$sec_proc_id = 18;
		$qryProc = "Select procedureId, code, poe_enable, poe_days From procedures Where procedureId IN ($pri_proc_id,$sec_proc_id, $ter_proc_id) ";
		$sqlProc = imw_query($qryProc) or die($qryProc.imw_error());
		
		
		
		$return = array();
		
		while( $row = imw_fetch_assoc($sqlProc) ) {
			
			$mod1 = $mod2 = $mod3 = '';
			$priArr = $secArr = $terArr = array();
			$in_poe_period = false;
			$pri_mod = false;
			$proc_id = $row['procedureId'];
			if( $proc_id == $pri_proc_id ) { 
				$pri_mod = true;
				$priArr = $this->filter_site_modifiers($pri_site_id);
			}
			
			if( $proc_id == $sec_proc_id ) {
				$secArr = $this->filter_site_modifiers($sec_site_id);
			}
			
			if( $proc_id == $ter_proc_id ) { 
				$terArr = $this->filter_site_modifiers($ter_site_id);
			}
			
			$mergeArr = array_merge($priArr, $secArr, $terArr);
			$mergeArr = array_unique($mergeArr);
			foreach($mergeArr as $mod){
				if( empty($mod1) ) $mod1 = $mod;
				else if( empty($mod2) ) $mod2 = $mod;
				else if( empty($mod3) ) $mod3 = $mod;
			}
			
			// Fill Modifier value if within poe period
			if( $row['poe_enable'] ) {
				$in_poe_period = $this->chk_poe_period_appt($pid, $dos, $row['poe_days'], $row['code']);
			}
			
			if( $in_poe_period && empty($mod1) ) $mod1 = '79';
			else if( $in_poe_period && empty($mod2) ) $mod2 = '79';
			else if( $in_poe_period && empty($mod3) ) $mod3 = '79';
			
			if( $pri_mod ) { 
				$return['pri']['mod1'] = $mod1;
				$return['pri']['mod2'] = $mod2;
				$return['pri']['mod3'] = $mod3;
			}
			
			$return[$row['code']]['mod1'] = $mod1;
			$return[$row['code']]['mod2'] = $mod2;
			$return[$row['code']]['mod3'] = $mod3;
			
			
		}
		return $return;
	}
	
	function filter_site_modifiers($site_id='')
	{
			$mod1 = $mod2 = '';
			// Fill modifier value based upon site
					 if($site_id == 1) { $mod1 = 'LT'; }
			else if($site_id == 2) { $mod1 = 'RT'; }
			else if($site_id == 3) { $mod1 = '50'; }
			else if($site_id == 4) { $mod1 = 'E1'; }
			else if($site_id == 5) { $mod1 = 'E2'; }
			else if($site_id == 6) { $mod1 = 'E3'; }
			else if($site_id == 7) { $mod1 = 'E4'; }
			else if($site_id == 8) { $mod1 = 'E1'; $mod2 = 'E3'; }
			else if($site_id == 9) { $mod1 = 'E2'; $mod2 = 'E4'; }
			
			// return modifier values
			return array($mod1,$mod2);
	}
	
	function chk_poe_period_appt($pid='', $dos='', $poe_days='', $cpt_code='') 
	{
		$pid = (int)$pid;
		$date_from = date('Y-m-d', strtotime($dos.'-'.$poe_days.'days'));
		$date_to = date('Y-m-d', strtotime($dos.'-1day'));
		
		$qry = "Select pc.patientConfirmationId From patientconfirmation pc
																							Join stub_tbl st on st.patient_confirmation_id = pc.patientConfirmationId
																							Left Join procedures p1 on pc.patient_primary_procedure_id = p1.procedureId
																							Left Join procedures p2 on pc.patient_secondary_procedure_id = p2.procedureId
																							Left Join procedures p3 on pc.patient_tertiary_procedure_id = p3.procedureId
																							Where pc.patientId = '".$pid."' 
																							And st.patient_status Not In ('Canceled','No Show','Aborted Surgery')
																							And pc.dos Between '".$date_from."' And '".$date_to."'
																							And (p1.code = '".$cpt_code."' Or p2.code = '".$cpt_code."' Or p3.code = '".$cpt_code."' )";
		$sql = imw_query($qry) or die($qry.imw_error());
		$cnt = imw_num_rows($sql);
		
		return ($cnt > 0) ? true : false;
	}
	
	function getInjMiscProc() {
		//START GET PROCEDURE TYPE - INJECTION OR MISCELLANEOUS
		$injMiscArr = array();
		$injMiscQry = "SELECT pc.isMisc, pc.isInj, p.procedureId, p.catId FROM procedures p INNER JOIN procedurescategory pc ON ( pc.proceduresCategoryId = p.catId AND (isInj='1' OR isMisc='1')) ORDER BY proceduresCategoryId";
		$injMiscRes = imw_query($injMiscQry) or die(imw_error());
		if(imw_num_rows($injMiscRes)) {
			while($injMiscRow = imw_fetch_assoc($injMiscRes)) {
				if($injMiscRow["isInj"]) {
					$injMiscArr[$injMiscRow["procedureId"]]	= "injection";
				}elseif($injMiscRow["isMisc"]) {
					$injMiscArr[$injMiscRow["procedureId"]]	= "misc";
				}
			}
		}
		return $injMiscArr;
		//END GET PROCEDURE TYPE - INJECTION OR MISCELLANEOUS
	}
	function getChartShowStatus($confId, $form) {
		$form = trim($form);
		$confId = (int)$confId;
		
		if( $form && $confId) {
			$arrTbl = array('checklist'=>'show_checklist');
	
			$fld = $arrTbl[$form];
			$qry = "SELECT ".$fld." FROM patientconfirmation WHERE patientConfirmationId = ".$confId;
			$sql = imw_query($qry) or die($qry. imw_error());
			$cnt = imw_num_rows($sql);
			if( $cnt > 0 ) {
				$row = imw_fetch_assoc($sql);
				return (int)$row[$fld];
			}
		}
		return false;
	}

	function getStubWaitingDetail($pConfId,$dos) {
		//START GET ARRIVAL TIME
		$stubWaitingDetailArr = array();
		if(!$pConfId || !$dos || $dos == '0000-00-00') return $stubWaitingDetailArr;
		$arvQry = "SELECT st.arrival_time as stub_arrival_time, pwt.arrival_time AS pwt_arrival_time FROM stub_tbl st 
					LEFT JOIN patient_in_waiting_tbl pwt ON (pwt.patient_in_waiting_id = st.iolink_patient_in_waiting_id)
					WHERE st.patient_confirmation_id = '".$pConfId."' and st.dos='".$dos."' LIMIT 0, 1";
		$arvRes = imw_query($arvQry) or die(imw_error());
		if(imw_num_rows($arvRes)>0) {
			$arvRow = imw_fetch_array($arvRes);
			$stub_arrival_time = $arvRow['stub_arrival_time'];
			$pwt_arrival_time = $arvRow['pwt_arrival_time'];
			$arrivalTime = (trim($stub_arrival_time)) ? $stub_arrival_time : $pwt_arrival_time;
			$arrivalTime = $this->getTmFormat($arrivalTime);
			array_push($stubWaitingDetailArr,$arrivalTime);
		}
		return $stubWaitingDetailArr;
		//END GET ARRIVAL TIME
	}
}
?>