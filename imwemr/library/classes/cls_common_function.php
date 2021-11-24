<?php
class CLSCommonFunction {
	private $siteRootDir;
	function __construct() { 
		//$this->siteRootDir = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH')."/";
		$this->siteRootDir = data_path();
	} 
	
	function xml_to_array($XML){
		$values = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $XML, $values);
		xml_parser_free($parser);
		return $values;
	}
	
	function create_ref_phy_xml($orderBy = "LastName asc")
	{			
		$getReqry = "select physician_Reffer_id,Title,FirstName,MiddleName,LastName,Address1,Address2,ZipCode,
					City,State,	physician_phone, physician_fax from refferphysician  where delete_status = 0 order by ".$orderBy."";
		$rsGetReqry = imw_query($getReqry); 
		if($rsGetReqry){
			if(imw_num_rows($rsGetReqry)>0){
				$referringPhysiciansDataXML = "";
				$refPhyAll = array();
				$aa = "?>";
				$referringPhysiciansDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$referringPhysiciansDataXML .= "<referringPhysiciansData>";
				$rep = array("&oelig;","&euro;","'","&quot;","Â","&acirc","&Acirc;","&nbsp;","’","
");
				$replace =  array("","","","","","",""," ","","");
				while ($rowGetReqry = imw_fetch_array($rsGetReqry)){			
					$refPhyID = $refPhyTitle = $refPhyFname = $refPhyMname = $refPhyLname = $refPhyAdd1 = $refPhyAdd2 = $refPhyCity = $refPhyState = $refPhyZip = $refPhone = NULL;
					$refPhyID = $rowGetReqry['physician_Reffer_id'];
					$refPhyTitle = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['Title']))));
					//$refPhyTitle = stripslashes(filter_var($refPhyTitle, FILTER_SANITIZE_URL));
					
					$refPhyFname = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['FirstName']))));
					//$refPhyFname = stripslashes(filter_var($refPhyFname, FILTER_SANITIZE_URL));
					
					$refPhyMname = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['MiddleName']))));
					//$refPhyMname = stripslashes(filter_var($refPhyMname, FILTER_SANITIZE_URL));
					
					$refPhyLname = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['LastName']))));
					//$refPhyLname = stripslashes(filter_var($refPhyLname, FILTER_SANITIZE_URL));
					
					$refPhyAdd1 = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['Address1']))));
					//$refPhyAdd1 = stripslashes(filter_var($refPhyAdd1, FILTER_SANITIZE_URL));
					
					$refPhyAdd2 = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['Address2']))));
					//$refPhyAdd2 = stripslashes(filter_var($refPhyAdd2, FILTER_SANITIZE_URL));
					
					$refPhyCity = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['City']))));
					//$refPhyCity = stripslashes(filter_var($refPhyCity, FILTER_SANITIZE_URL));
					
					$refPhyState = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['State']))));
					//$refPhyState = stripslashes(filter_var($refPhyState, FILTER_SANITIZE_URL));
					
					$refPhyZip = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['ZipCode']))));
					//$refPhyZip = stripslashes(filter_var($refPhyZip, FILTER_SANITIZE_URL));
					$refPhone = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['physician_phone']))));
					$refFax = trim((str_replace($rep,$replace,htmlentities($rowGetReqry['physician_fax']))));
					
					if($refPhyFname && $refPhyLname){
						if (preg_match("/[a-zA-Z]/", $refPhyFname) && preg_match("/[a-zA-Z]/", $refPhyLname)) {
							$referringPhysiciansDataXML .= "<refPhyInfo refphyId=\"".$refPhyID."\" refPhyTitle=\"".$refPhyTitle."\" refphyFName=\"".$refPhyFname."\"  refPhyMname=\"".$refPhyMname."\" refphyLName=\"".$refPhyLname."\" refPhyAdd1=\"".$refPhyAdd1."\" refPhyAdd2=\"".$refPhyAdd2."\" refPhyCity=\"".$refPhyCity."\" refPhyState=\"".$refPhyState."\" refPhyZip=\"".$refPhyZip."\" refPhone=\"".$refPhone."\" refFax=\"".$refFax."\"></refPhyInfo>";			
						}
					}
				}
				imw_free_result($rsGetReqry);
				$referringPhysiciansDataXML .= "</referringPhysiciansData>";				
				$refPhyXMLFile = $this->siteRootDir."xml/Referring_Physicians.xml";
				file_put_contents($refPhyXMLFile,$referringPhysiciansDataXML);
			}	
		}
		return $refPhyXMLFile;
	}
	
	function create_ref_phy_main_xml($fileName=""){
		if($fileName != ""){
			// CREATE XML FILE FOR PHYSIANS LAST NAME STARTING WITH $fileName
			$this->create_ref_phy_chunks_xml($fileName);
		}else{ // CREATE ALL XML FILES FOR ALL REF PHYSICIANS IN DATABASE
			$charString = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y"
			,"z");
			for($i=0;$i<count($charString);$i++){
				$char1 = $charString[$i];
				
				for($j = 0;$j<count($charString);$j++){
				$char2 = $charString[$j];
				$lastNameStr =  $char1.$char2;
				
					$this->create_ref_phy_chunks_xml($lastNameStr);
					
				}
			}
		}
	}
	
	function create_ref_phy_chunks_xml($lastNameStr,$orderBy = "LastName asc, FirstName asc"){	
		
		$getReqry = "select physician_Reffer_id,Title,FirstName,MiddleName,LastName,Address1,Address2,ZipCode,
					City,State,	physician_phone, physician_fax, physician_email from refferphysician  where primary_id = 0 AND delete_status = 0  and REPLACE(LastName,'\'','') like '".$lastNameStr."%' order by ".$orderBy."";
		$rsGetReqry = imw_query($getReqry); 
		if($rsGetReqry){
			if(imw_num_rows($rsGetReqry)>0){
				$referringPhysiciansDataXML = "";
				$refPhyAll = array();
				$aa = "?>";
				$referringPhysiciansDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$referringPhysiciansDataXML .= "<referringPhysiciansData>";
				$rep = array("’","
");			
				while ($rowGetReqry = imw_fetch_array($rsGetReqry)){			
					$refPhyID = $refPhyTitle = $refPhyFname = $refPhyMname = $refPhyLname = $refPhyAdd1 = $refPhyAdd2 = $refPhyCity = $refPhyState = $refPhyZip = $refPhone = NULL;
					$refPhyID = $rowGetReqry['physician_Reffer_id'];
					$refPhyTitle = trim(htmlentities(str_replace($rep,"",$rowGetReqry['Title'])));
						
						$refPhyFname = trim(htmlentities(str_replace($rep,"",$rowGetReqry['FirstName'])));
						
						$refPhyMname = trim(htmlentities(str_replace($rep,"",$rowGetReqry['MiddleName'])));
						
						$refPhyLname = trim(htmlentities(str_replace($rep,"",$rowGetReqry['LastName'])));
						
						$refPhyAdd1 = trim(htmlentities(str_replace($rep,"",$rowGetReqry['Address1'])));
						
						$refPhyAdd2 = trim(htmlentities(str_replace($rep,"",$rowGetReqry['Address2'])));
						
						$refPhyCity = trim(htmlentities(str_replace($rep,"",$rowGetReqry['City'])));
						
						$refPhyState = trim(htmlentities(str_replace($rep,"",$rowGetReqry['State'])));
						
						$refPhyZip = trim(htmlentities(str_replace($rep,"",$rowGetReqry['ZipCode'])));
						$refPhone = trim(htmlentities(str_replace($rep,"",$rowGetReqry['physician_phone'])));
						$refFax = trim(htmlentities(str_replace($rep,"",$rowGetReqry['physician_fax'])));
						$refEmail = trim(htmlentities(str_replace($rep,"",$rowGetReqry['physician_email'])));
					
							if($refPhyFname && $refPhyLname){
								if (preg_match("/[a-zA-Z]/", $refPhyFname) && preg_match("/[a-zA-Z]/", $refPhyLname)) {
									$referringPhysiciansDataXML .= "<refPhyInfo>";
									$referringPhysiciansDataXML .= "  <refphyId>".$refPhyID."</refphyId>";
									$referringPhysiciansDataXML .= "  <refPhyTitle><![CDATA[".$refPhyTitle."]]></refPhyTitle>";
									$referringPhysiciansDataXML .= "  <refphyFName><![CDATA[".$refPhyFname."]]></refphyFName>";
									$referringPhysiciansDataXML .= "  <refPhyMname><![CDATA[".$refPhyMname."]]></refPhyMname>";
									$referringPhysiciansDataXML .= "  <refphyLName><![CDATA[".$refPhyLname."]]></refphyLName>";
									$referringPhysiciansDataXML .= "  <refPhyAdd1><![CDATA[".$refPhyAdd1."]]></refPhyAdd1>";
									$referringPhysiciansDataXML .= "  <refPhyAdd2><![CDATA[".$refPhyAdd2."]]></refPhyAdd2>";
									$referringPhysiciansDataXML .= "  <refPhyCity><![CDATA[".$refPhyCity."]]></refPhyCity>";
									$referringPhysiciansDataXML .= "  <refPhyState><![CDATA[".$refPhyState."]]></refPhyState>";
									$referringPhysiciansDataXML .= "  <refPhyZip><![CDATA[".$refPhyZip."]]></refPhyZip>";
									$referringPhysiciansDataXML .= "  <refPhone><![CDATA[".$refPhone."]]></refPhone>";
									$referringPhysiciansDataXML .= "  <refFax><![CDATA[".$refFax."]]></refFax>";
									$referringPhysiciansDataXML .= "  <refEmail><![CDATA[".$refEmail."]]></refEmail>";
									
									
									/*$referringPhysiciansDataXML .= "  <refphyId>".$refPhyID."</refphyId>";
									$referringPhysiciansDataXML .= "  <refPhyTitle>".$refPhyTitle."</refPhyTitle>";
									$referringPhysiciansDataXML .= "  <refphyFName>".$refPhyFname."</refphyFName>";
									$referringPhysiciansDataXML .= "  <refPhyMname>".$refPhyMname."</refPhyMname>";
									$referringPhysiciansDataXML .= "  <refphyLName>".$refPhyLname."</refphyLName>";
									$referringPhysiciansDataXML .= "  <refPhyAdd1>".$refPhyAdd1."</refPhyAdd1>";
									$referringPhysiciansDataXML .= "  <refPhyAdd2>".$refPhyAdd2."</refPhyAdd2>";
									$referringPhysiciansDataXML .= "  <refPhyCity>".$refPhyCity."</refPhyCity>";
									$referringPhysiciansDataXML .= "  <refPhyState>".$refPhyState."</refPhyState>";
									$referringPhysiciansDataXML .= "  <refPhyZip>".$refPhyZip."</refPhyZip>";
									$referringPhysiciansDataXML .= "  <refPhone>".$refPhone."</refPhone>";
									$referringPhysiciansDataXML .= "  <refFax>".$refFax."</refFax>";*/
									
									$referringPhysiciansDataXML .= "</refPhyInfo>";
								}
					}
				}
				imw_free_result($rsGetReqry);
				$referringPhysiciansDataXML .= "</referringPhysiciansData>";
				
				$xmlFileName = $lastNameStr.".xml";
				
				// MAKE refphy DIR FOR STORING XML CHUNKS OF REFPHY
				
				if(!is_dir($this->siteRootDir."xml/refphy")){
					mkdir($this->siteRootDir."xml/refphy", 0755, true);
				}
				
				// CREATE PHYSICIAN FILES IN refphy DIRECTORY 
				
				//if(!file_exists($this->siteRootDir."xml/refphy/".$xmlFileName))
				//	fopen($this->siteRootDir."xml/refphy/".$xmlFileName,"w");
				
								
				$refPhyXMLFile = $this->siteRootDir."xml/refphy/".$xmlFileName;
				file_put_contents($refPhyXMLFile,$referringPhysiciansDataXML);
			}	
		}
		

}

	function create_provider_xml($orderBy = "lname, fname"){			
		$getProqry = "select id,fname,mname,lname,Enable_Scheduler,user_type,delete_status from users order by ".$orderBy." ";
		$rsGetproqry = imw_query($getProqry); 
		if($rsGetproqry){
			if(imw_num_rows($rsGetproqry)>0){
				$providerDataXML = "";
				$proAll = array();
				$aa = "?>";
				$providerDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$providerDataXML .= "<providerData>";
				$rep = array("'"," ","’");			
				while ($rowGetproqry = imw_fetch_array($rsGetproqry)){			
					$proID = $proFname = $proMname = $proLname = $enSch = "";
					$proID = $rowGetproqry['id'];
					$proFname = trim(htmlentities(str_replace($rep,"",$rowGetproqry['fname'])));
					//$proFname = filter_var($proFname, FILTER_SANITIZE_URL);	
																		
					$proMname = trim(htmlentities(str_replace($rep,"",$rowGetproqry['mname'])));
					//$proMname = filter_var($proMname, FILTER_SANITIZE_URL);														
					
					$proLname = trim(htmlentities(str_replace($rep,"",$rowGetproqry['lname'])));
					//$proLname = filter_var($proLname, FILTER_SANITIZE_URL);														
					
					$user_type = trim($rowGetproqry['user_type']);
					//$user_type = filter_var($user_type, FILTER_SANITIZE_URL);														
					
					$enSch = trim($rowGetproqry['Enable_Scheduler']);
					//$enSch = filter_var($enSch, FILTER_SANITIZE_URL);														
					
					$proStatus = trim($rowGetproqry['delete_status']);
					
					if($proFname && $proLname){
						if (preg_match("/[a-zA-Z0-9]/", $proFname) && preg_match("/[a-zA-Z0-9]/", $proLname)) {
							$providerDataXML .= "<proInfo proId=\"".$proID."\" proFName=\"".$proFname."\"  proMname=\"".$proMname."\" proLname=\"".$proLname."\"  enSch=\"".$enSch."\"  user_type=\"".$user_type."\" proStatus=\"".$proStatus."\"></proInfo>";				
						}
					}
				}
				imw_free_result($rsGetproqry);
				$providerDataXML .= "</providerData>";
				if(!is_dir($this->siteRootDir.'xml'))		mkdir($this->siteRootDir.'xml');
				$proXMLFile = $this->siteRootDir."xml/Provider_Data.xml";
				file_put_contents($proXMLFile,$providerDataXML);
			}	
		}
		return $proXMLFile;
	}
	
	// Function drop_down_providers -  if $user_type='' then all, $user_type=1 then Physician, $user_type=3 then Technicians	
	function drop_down_providers($saved_val = "",$EnableSch="",$user_type="", $returnType="", $callFrom="", $reportType="", $arr_exclude_user= array(), $arr_include_users= array())
	{
		$prividerDataArr = array();
		$drop_down = $drop_down_deleted= "";
		
		if($user_type!='')
		{	
			$qryInit = 'Select user_type_id from user_type WHERE ';
			$user_type_attending = 0;					
			if($user_type==1){	$user_type_name= 'Physician'; $qryUsrType = ' user_type_name IN("Physician","Attending Physician","Physician Assistant")';}
			if($user_type==3){	$user_type_name= 'Technician'; $qryUsrType = ' user_type_name = "Technician"';}
			if($user_type==6){	$user_type_name= 'Surgical Coordinator'; $qryUsrType = ' user_type_name = "Surgical Coordinator"';}
			if($user_type==2){	$user_type_name= 'Nurse'; $qryUsrType = ' user_type_name = "Nurse"';}
			$qryReq = $qryInit.$qryUsrType;	
			$qryRs=imw_query($qryReq);
			$qryResRows = imw_num_rows($qryRs);
			$qryRes = imw_fetch_row($qryRs);
			$user_type = $qryRes[0];
			if($qryResRows > 1)
			{
				$qryRes = imw_fetch_row($qryRs);
				$user_type_attending = $qryRes[0];							
			}
			if($qryResRows > 2)
			{
				$qryRes = imw_fetch_row($qryRs);
				$user_type_assistant = $qryRes[0];							
			}
		}
		if($reportType=='consult_letter') {
			$uTypeIdArr=array();
			$uTypeQry = 'Select user_type_id from user_type WHERE user_type_name IN("Physician","Attending Physician","Fellow","Resident")';	
			$uTypeRes=imw_query($uTypeQry);
			if(imw_num_rows($uTypeRes)>0) {
				while($uTypeRow = imw_fetch_array($uTypeRes)) {
					$uTypeIdArr[]=$uTypeRow['user_type_id'];
				}
			}
		}
		$providerXMLFile = $this->siteRootDir."xml/Provider_Data.xml";
		if(file_exists($providerXMLFile)){
			$providerXMLFileExits = true;
		}
		else{
			$this->create_provider_xml();	
			if(file_exists($providerXMLFile)){
				$providerXMLFileExits = true;	
			}	
		}
		if($providerXMLFileExits == true){
			$values = array();
			$XML = file_get_contents($providerXMLFile);
			$values = $this->xml_to_array($XML);
			
				$active_provider_array = array();
				$inactive_provider_array = array();
				
				foreach($values as $val_new)
				{
					if ($val_new['attributes']['proStatus']==0){
						$active_provider_array[] = $val_new;
					} else if ($val_new['attributes']['proStatus']==1){
						$inactive_provider_array[] = $val_new;
					}
				}
				$values = array_merge($active_provider_array,$inactive_provider_array);	
				
				foreach($values as $val)
				{
					if(trim(!empty($val['attributes']['proId']))){
						
						if(sizeof($arr_include_users)<=0 || (sizeof($arr_include_users)>0 && $arr_include_users[$val['attributes']['proId']])){
						
							if(($callFrom=='report') || ($callFrom=='' && $val['attributes']['proStatus']==0)){
								$color='';
								if(!empty($val['attributes']['proLname']) || !empty($val['attributes']['proMname'])){
									$Last_name = ', '.$val['attributes']['proFName'];
								}
								else{
									$Last_name = $val['attributes']['proFName'];
								}
								$firstName=ucfirst($val['attributes']['proLname']).' '.ucfirst($val['attributes']['proMname']);
								$print_val = trim($firstName).ucfirst(trim($Last_name));
								
								if($callFrom=='report'){
									if($val['attributes']['proStatus']==1)$color='color:#CC0000!important';
								}
								
								$select="";
								//------------Select Option -----------//
								if($saved_val!=""){
									$saved_val_arr = explode(",",$saved_val);
									if(in_array($val['attributes']['proId'] ,$saved_val_arr)){
										$select="selected='selected'";
									}
								}							
	/*							if ($val['attributes']['proId'] == $saved_val){
								$select = "selected='selected'";
								}*/
								if(empty($returnType) === true){
									
									if($val['attributes']['proStatus']<=0 || $val['attributes']['proStatus']==''){

										if(!empty($user_type) && $EnableSch=='1' && ($user_type == $val['attributes']['user_type'] || $user_type_attending == $val['attributes']['user_type'] || $user_type_assistant == $val['attributes']['user_type'] || $val['attributes']['enSch']=='1')){
											$drop_down .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>".$print_val."</option>";
										}else if(!empty($user_type) && ($user_type == $val['attributes']['user_type'] || $user_type_attending == $val['attributes']['user_type'] || $user_type_assistant == $val['attributes']['user_type'])){
											$drop_down .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>".$print_val."</option>";
										}
										else if(empty($user_type)){
											if($reportType=='consult_letter' && (($EnableSch=='1'&& $val['attributes']['enSch']=='1') || (in_array($val['attributes']['user_type'],$uTypeIdArr)))) {
												$drop_down .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";		
											}
											else if($EnableSch=='1'&& $val['attributes']['enSch']=='1'){
												$drop_down .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";
											}
											else if($EnableSch!='1' && count($arr_exclude_user)>0 && !in_array($val['attributes']['user_type'],$arr_exclude_user)){
												$drop_down .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";
											}else if($EnableSch!='1' && count($arr_exclude_user)<=0){
												$drop_down .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";
											}
										}
									}else{
										if(!empty($user_type) && $EnableSch=='1' && ($user_type == $val['attributes']['user_type'] || $user_type_attending == $val['attributes']['user_type'] || $user_type_assistant == $val['attributes']['user_type'] || $val['attributes']['enSch']=='1')){
											$drop_down_deleted.= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>".$print_val."</option>";
										}else if(!empty($user_type) && ($user_type == $val['attributes']['user_type'] || $user_type_attending == $val['attributes']['user_type'] || $user_type_assistant == $val['attributes']['user_type'])){
											$drop_down_deleted .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>".$print_val."</option>";
										}
										else if(empty($user_type)){
											if($reportType=='consult_letter' && (($EnableSch=='1'&& $val['attributes']['enSch']=='1') || (in_array($val['attributes']['user_type'],$uTypeIdArr)))) {
												$drop_down_deleted .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";		
											}
											else if($EnableSch=='1'&& $val['attributes']['enSch']=='1'){
												$drop_down_deleted .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";
											}
											else if($EnableSch!='1' && count($arr_exclude_user)>0 && !in_array($val['attributes']['user_type'],$arr_exclude_user)){
												$drop_down_deleted .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";
											}else if($EnableSch!='1' && count($arr_exclude_user)<=0){
												$drop_down_deleted .= "<option $select value='".$val['attributes']['proId']."' style='".$color."'>$print_val</option>";
											}
										}
									}
								}
								else{
									if(!empty($user_type) && ($user_type==$val['attributes']['user_type']) || ($EnableSch=='1' && $val['attributes']['enSch']=='1')){
										$prividerDataArr[$val['attributes']['proId']] = $print_val;
									}else if(empty($user_type)){
										$prividerDataArr[$val['attributes']['proId']] = $print_val;
									}
								}
							}
						}
					}
				}//print_r(array_merge($active_provider_array,$inactive_provider_array));	
		}
		
		$drop_down.=$drop_down_deleted;
	
		if(empty($returnType) === true){
			return $drop_down;
		}else{
			return $prividerDataArr;
		}
	}
	
	
	function drop_down_api_users($saved_val = "")
	{
		$prividerDataArr = array();
		$drop_down = "";
		
		$sql = 'SELECT `id`, `fname`, `lname`, `mname`, `access_pri` FROM `users`';
		$resp = imw_query($sql);
		
		if( $resp && imw_num_rows($resp) > 0)
		{
			while($row = imw_fetch_assoc($resp))
			{
				$access_arr = unserialize(html_entity_decode(trim($row["access_pri"])));
				
				if( isset($access_arr['priv_api_access']) && $access_arr['priv_api_access'] == 1 )
				{
					$providerName = ucfirst($row['lname']);
					$providerName .= (trim($row['mname']) !== '')? ' '.ucfirst($row['mname']):'';
					$providerName .= ', '.$row['fname'];
					
					$drop_down .= '<option value="'.$row['id'].'">'.$providerName.'</option>';
				}
			}
		}
		
		return $drop_down;
	}
	
	function get_pat_age_year($patId){
		$sql = "SELECT DATE_FORMAT(NOW(), '%Y') - DATE_FORMAT(dob, '%Y') - (DATE_FORMAT(NOW(), '00-%m-%d') < DATE_FORMAT(dob, '00-%m-%d')) AS ageYear FROM `patient_data` WHERE id = '".$patId."'";
		$result = imw_query($sql);
		$row = imw_fetch_row($result);
		$age = "";
		$ageYear = $row[0];
		if($ageYear >= 1)
		{
			$age = $ageYear;
		}
	
		return $age;
	}
	
	function get_ref_phy_name($id){
		$strName = '';
		if(!empty($id)){
			$qry = "select Title,FirstName,MiddleName,LastName from refferphysician where physician_Reffer_id = ".$id;
			$res = imw_query($qry);
			if(imw_num_rows($res)>0){
				$row = imw_fetch_assoc($res);
				if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
					$strName .= $row['Title'] != '' ? trim($row['Title']).' ':'';
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']):'';
				}
				else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']).' ':'';
					$strName .= $row['Title'] != '' ? trim($row['Title']):'';
				}
			}
		}
		return $strName;
	}
	
	function chk_create_ref_phy($strRefPhy, $source='')
	{
		$strRefPhyArr = explode(";",$strRefPhy);
		$strRefPhy= trim($strRefPhyArr[0]);
		$arrTitle = array("DR","DR.", "Dr", "Dr.", "dr", "dr.", "D.R", "D.r.", "d.r", "d.r.", 
							"DO", "DO.", "Do", "Do.", "do", "do.", "D.O", "D.o.", "d.o.", 
							"OD", "OD.", "Od", "Od.", "od", "od.", "O.D", "O.d.", "o.d.", 
							"MD", "MD.", "Md", "Md.", "md", "md.", "M.D", "M.D.", "M.d.", "m.d.", 
							"MR.", "MR", "Mr", "Mr.", "mr", "mr.", "M.R.", "MR.", "M.r.", "m.r.",
							"MRS.", "Mrs.", "MRS", "Mrs", "mrs.", "mrs", "M.R.S.", "M.r.s.", "M.R.S", "m.r.s.",
							"MISS", "Miss", "MISS.", "Miss.", "miss.", "miss", "M.I.S.S.", "M.i.s.s.", "M.i.s.s.", "m.i.s.s.", 
							"MS", "MS.", "ms", "ms.", "M.s.","M.S.", "m.s", "M.s.");
							
		$arrTitleDR = array("DR","DR.", "Dr", "Dr.", "dr", "dr.", "D.R", "D.r.", "d.r", "d.r.");
		$arrTitleDO = array("DO", "DO.", "Do", "Do.", "do", "do.", "D.O", "D.o.", "d.o.");
		$arrTitleOD = array("OD", "OD.", "Od", "Od.", "od", "od.", "O.D", "O.d.", "o.d.");
		$arrTitleMD = array("MD", "MD.", "Md", "Md.", "md", "md.", "M.D", "M.D.", "M.d.", "m.d.");
		$arrTitleMR = array("MR.", "MR", "Mr", "Mr.", "mr", "mr.", "M.R.", "MR.", "M.r.", "m.r.");
		$arrTitleMRS = array("MRS.", "Mrs.", "MRS", "Mrs", "mrs.", "mrs", "M.R.S.", "M.r.s.", "M.R.S", "m.r.s.");
		$arrTitleMISS = array("MISS", "Miss", "MISS.", "Miss.", "miss.", "miss", "M.I.S.S.", "M.i.s.s.", "M.i.s.s.", "m.i.s.s.");
		$arrTitleMS = array("MS", "MS.", "ms", "ms.", "M.s.","M.S.", "m.s", "M.s.");
		
		$strPhyTitle = $strPhyLname = $strPhyFname = $strPhyMname = "";
		$intRefPhyIdDB = 0;
		$strRefPhyTitleDB = $strRefPhyFNameDB = $strRefPhyMNameDB = $strRefPhyLNameDB = $strRefPhyName = "";
		$arrRefPhy = $arrReFPhyLastName = $arrReturn = array();
		$arrRefPhy = explode(',',trim($strRefPhy));		
		foreach($arrRefPhy as $key => $value){
			$arrRefPhy[$key] = trim($value);
		}
		//--------NOT FOR BOSTON SERVER------
		if(!isset($GLOBALS["REF_PHY_FORMAT"]) || strtolower($GLOBALS["REF_PHY_FORMAT"])!='boston'){
				$arrPhyLname = explode(' ',trim($arrRefPhy[0]));
				$arrPhyFname = explode(' ',trim($arrRefPhy[1]));
				if(count($arrPhyLname) == 1){
					$strPhyLname = trim(ucfirst($arrPhyLname[0]));	
				}
				else if(count($arrPhyLname) == 2){
					$strPhyLname = trim(ucfirst(end($arrPhyLname)));	
					$strPhyTitleChk = trim(ucfirst($arrPhyLname[0]));
					if(in_array(trim($strPhyTitleChk), $arrTitle) == true){
						$strPhyTitle = trim($strPhyTitleChk);
						if(in_array(trim($strPhyTitle), $arrTitleDR) == true){
							$strPhyTitle = "Dr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleDO) == true){
							$strPhyTitle = "DO";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleOD) == true){
							$strPhyTitle = "OD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMD) == true){
							$strPhyTitle = "MD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMR) == true){
							$strPhyTitle = "Mr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMRS) == true){
							$strPhyTitle = "Mrs.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMISS) == true){
							$strPhyTitle = "Miss.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMS) == true){
							$strPhyTitle = "Ms";
						}
				//$strPhyLname = trim(ucfirst($arrReFPhyLastName[1]));
				}
					else{
						$strPhyTitle = '';
					}
				}
			
				if(count($arrPhyFname) == 1){
					$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
				}
				else{
					$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
					$strPhyMname = trim(ucfirst($arrPhyFname[1]));
				}
		}	//--------END NOT FOR BOSTON SERVER-----------------------------
		else if(isset($GLOBALS["REF_PHY_FORMAT"]) && strtolower($GLOBALS["REF_PHY_FORMAT"]) == 'boston'){ //--------FOR BOSTON SERVER------------------------
					$strPhyLname = trim(ucfirst($arrRefPhy[0]));
					$arrPhyFname = explode(' ',trim($arrRefPhy[1]));
					if(count($arrPhyFname) == 1){
						$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
					}
					else if(count($arrPhyFname) == 2){
				$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
				$strPhyMnameChk = trim(ucfirst(end($arrPhyFname)));
				if(in_array(trim($strPhyMnameChk), $arrTitle) == true){
						$strPhyTitle = trim($strPhyMnameChk);
						if(in_array(trim($strPhyTitle), $arrTitleDR) == true){
							$strPhyTitle = "Dr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleDO) == true){
							$strPhyTitle = "DO";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleOD) == true){
							$strPhyTitle = "OD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMD) == true){
							$strPhyTitle = "MD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMR) == true){
							$strPhyTitle = "Mr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMRS) == true){
							$strPhyTitle = "Mrs.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMISS) == true){
							$strPhyTitle = "Miss.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMS) == true){
							$strPhyTitle = "Ms";
						}
						//$strPhyLname = trim(ucfirst($arrReFPhyLastName[1]));
				}else{
						$strPhyMname = trim(ucfirst(end($arrPhyFname)));
				}
			}
					else if(count($arrPhyFname) == 3){
				$strPhyFname = trim(ucfirst($arrPhyFname[0]));	
				$strPhyMname = trim(ucfirst($arrPhyFname[1]));
				$phyTitle = trim(ucfirst(end($arrPhyFname)));
				if(in_array(trim($phyTitle), $arrTitle) == true){
						$strPhyTitle = trim($phyTitle);
						if(in_array(trim($strPhyTitle), $arrTitleDR) == true){
							$strPhyTitle = "Dr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleDO) == true){
							$strPhyTitle = "DO";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleOD) == true){
							$strPhyTitle = "OD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMD) == true){
							$strPhyTitle = "MD";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMR) == true){
							$strPhyTitle = "Mr.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMRS) == true){
							$strPhyTitle = "Mrs.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMISS) == true){
							$strPhyTitle = "Miss.";
						}
						elseif(in_array(trim($strPhyTitle), $arrTitleMS) == true){
							$strPhyTitle = "Ms";
						}
						//$strPhyLname = trim(ucfirst($arrReFPhyLastName[1]));
				}
			}
		}//--------END FOR BOSTON SERVER-----------------------------
			$strMqry = "";
			if(empty($strPhyMname) == false){
				$strMqry = " and (MiddleName = '".addslashes($strPhyMname)."' or MiddleName = '".addslashes($strPhyMname).".') ";
			}
			if((empty($strPhyLname) == false) && (empty($strPhyFname) == false)){
			$qryGetRefPhy = "select physician_Reffer_id,Title,FirstName,MiddleName,LastName from refferphysician where LastName = '".addslashes($strPhyLname)."' 
								and FirstName = '".addslashes($strPhyFname)."' ".$strMqry." and (delete_status = 0  or delete_status = 2 ) ORDER BY delete_status ASC limit 1";
			$rsGetRefPhy = imw_query($qryGetRefPhy);
			if(imw_num_rows($rsGetRefPhy)==0 && $source==7){
				$qryGetRefPhy = "select physician_Reffer_id,Title,FirstName,MiddleName,LastName from refferphysician where LastName = '".addslashes($strPhyLname)."' 
								and FirstName = '".addslashes($strPhyFname).' '.addslashes($strPhyMname)."' and (delete_status = 0  or delete_status = 2 ) ORDER BY delete_status ASC limit 1";	
				$rsGetRefPhy = imw_query($qryGetRefPhy);
			}
			
			if($rsGetRefPhy){
				if(imw_num_rows($rsGetRefPhy) > 0){
					$rowGetRefPhy = imw_fetch_row($rsGetRefPhy);
					$intRefPhyIdDB = $rowGetRefPhy[0];
					$strRefPhyTitleDB = $rowGetRefPhy[1];
					$strRefPhyFNameDB = $rowGetRefPhy[2];
					$strRefPhyMNameDB = $rowGetRefPhy[3];
					$strRefPhyLNameDB = $rowGetRefPhy[4];
					//$strRefPhyName = $strRefPhyLNameDB.", ".$strRefPhyFNameDB." ".$strRefPhyMNameDB." ".$strRefPhyTitleDB;
					$strRefPhyName = $this->get_ref_phy_name($intRefPhyIdDB);
					$strRefPhyName = trim($strRefPhyName);
				}
				else if($source != "7"){
					$delete_status = ($source == "1")?'0':'2';
					$qryInsertRefPhy = "insert into refferphysician (Title,FirstName,LastName,MiddleName,created_date,source,delete_status) Values ('".core_refine_user_input($strPhyTitle)."', 
										'".core_refine_user_input($strPhyFname)."','".core_refine_user_input($strPhyLname)."','".core_refine_user_input($strPhyMname)."',
										'".date('Y-m-d')."','".$source."','".$delete_status."'
										)";
					$rsInsertRefPhy = imw_query($qryInsertRefPhy);
					$intRefPhyIdDB = imw_insert_id();
					if($intRefPhyIdDB){
						$this->create_ref_phy_main_xml(strtolower(substr(trim($strPhyLname),0,2)));
					}
					//$strRefPhyName = $strPhyLname.", ".$strPhyFname." ".$strPhyMname." ".$strPhyTitle;	
					$strRefPhyName = $this->get_ref_phy_name($intRefPhyIdDB);			
					$strRefPhyName = trim($strRefPhyName);
				}
				imw_free_result($rsGetRefPhy);
			}
		}
			$arrReturn = array($intRefPhyIdDB, $strRefPhyName);
		return $arrReturn;
	}
	
	function createInsCompXML($orderBy = "in_house_code"){			
		if($orderBy == "in_house_code"){
			$orderBy = "trim(in_house_code)";
		}
		$getInsComp = "select id,name,in_house_code,contact_address,City,State,Zip,Insurance_payment,secondary_payment_method from insurance_companies where ins_del_status = 0 order by ".$orderBy."";
		$rsInsComp = imw_query($getInsComp); 
		if($rsInsComp){
			if(imw_num_rows($rsInsComp)>0){
				$insCompDataXML = "";
				$insCompName = array();
				$aa = "?>";
				$insCompDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$insCompDataXML .= "<insCompData>";
				$rep = array("'","'","Â","&Acirc;","&nbsp;");
				$replace = array("","","",""," ");
				while ($rowInsComp = imw_fetch_array($rsInsComp)){							
					$insCompId = $insCompINHouseCode = $insCompName = $insCompAdd = $insCompCity = $insCompState = $insCompZip = $insCompPayMethod = $secInsCompPayMethod = "";
					$insCompId = $rowInsComp['id'];
					$insCompINHouseCode = trim(htmlentities(str_replace($rep,$replace,htmlentities($rowInsComp['in_house_code']))));
					//$insCompINHouseCode = filter_var($insCompINHouseCode, FILTER_SANITIZE_URL);
										
					$insCompName = trim(htmlentities(str_replace($rep,$replace,htmlentities($rowInsComp['name']))));
					//$insCompName = filter_var($insCompName, FILTER_SANITIZE_URL);
					
					$insCompAdd = trim(htmlentities(str_replace($rep,$replace,htmlentities($rowInsComp['contact_address']))));
					//$insCompAdd = filter_var($insCompAdd, FILTER_SANITIZE_URL);
					
					$insCompCity = trim(htmlentities(str_replace($rep,$replace,$rowInsComp['City'])));
					//$insCompCity = filter_var($insCompCity, FILTER_SANITIZE_URL);
					
					$insCompState = trim(htmlentities(str_replace($rep,$replace,$rowInsComp['State'])));
					//$insCompState = filter_var($insCompState, FILTER_SANITIZE_URL);
					
					$insCompZip = trim(htmlentities(str_replace($rep,$replace,$rowInsComp['Zip'])));					
					//$insCompZip = filter_var($insCompZip, FILTER_SANITIZE_URL);
					
					$insCompPayMethod = trim(htmlentities(str_replace($rep,$replace,htmlentities($rowInsComp['Insurance_payment']))));	
					$secInsCompPayMethod = trim(htmlentities(str_replace($rep,$replace,htmlentities($rowInsComp['secondary_payment_method']))));
					
					if($insCompName || $insCompInHouseCode){
						if (preg_match("/[a-zA-Z0-9]/", $insCompName) || preg_match("/[a-zA-Z0-9]/", $insCompInHouseCode)) {					
							$insCompDataXML .= "<insCompInfo insCompId=\"".$insCompId."\" insCompINHouseCode=\"".$insCompINHouseCode."\" insCompName=\"".$insCompName."\"  insCompAdd=\"".$insCompAdd."\" insCompCity=\"".$insCompCity."\" insCompState=\"".$insCompState."\" insCompZip=\"".$insCompZip."\" insCompPayMethod=\"".$insCompPayMethod."\" secInsCompPayMethod=\"".$secInsCompPayMethod."\"></insCompInfo>";				
						}
					}
				}
				imw_free_result($rsInsComp);
				$insCompDataXML .= "</insCompData>";				
				$insCompXMLFile = $this->siteRootDir."xml/Insurance_Comp.xml";				
				file_put_contents($insCompXMLFile,$insCompDataXML);
			}	
		}
		return $insCompXMLFile;
	}
	
	function createInsCompXMLCrossMap($orderBy = "in_house_code"){			
		if($orderBy == "in_house_code"){
			$orderBy = "trim(in_house_code)";
		}
		$getInsComp = "select id,name,in_house_code,contact_address,City,State,Zip,idx_code from insurance_companies where ins_del_status = 0 order by ".$orderBy."";
		$rsInsComp = imw_query($getInsComp); 
		if($rsInsComp){
			if(imw_num_rows($rsInsComp)>0){
				$insCompDataXML = "";
				$insCompName = array();
				$aa = "?>";
				$insCompDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$insCompDataXML .= "<insCompData>";
				$rep = array("'","'");			
				while ($rowInsComp = imw_fetch_array($rsInsComp)){							
					$insCompId = $insCompINHouseCode = $insCompName = $insCompAdd = $insCompCity = $insCompState = $insCompZip = $insCompIDXCode = "";
					$insCompId = $rowInsComp['id'];
					$insCompINHouseCode = trim(htmlentities(str_replace($rep,"",$rowInsComp['in_house_code'])));
					$insCompName = trim(htmlentities(str_replace($rep,"",$rowInsComp['name'])));
					$insCompAdd = trim(htmlentities(str_replace($rep,"",$rowInsComp['contact_address'])));
					$insCompCity = trim(htmlentities(str_replace($rep,"",$rowInsComp['City'])));
					$insCompState = trim(htmlentities(str_replace($rep,"",$rowInsComp['State'])));
					$insCompZip = trim(htmlentities(str_replace($rep,"",$rowInsComp['Zip'])));
					$insCompIDXCode = trim(htmlentities(str_replace($rep,"",$rowInsComp['idx_code'])));
					
					$qryGetIdxInvRCOId = "SELECT id, invision_plan_code, invision_plan_description, IDX_description, IDX_FSC 
											FROM idx_invision_rco WHERE LOWER(IDX_FSC) = LOWER('".$insCompIDXCode."') ";
					$rsGetIdxInvRCOId = imw_query($qryGetIdxInvRCOId);
					if(imw_num_rows($rsGetIdxInvRCOId) > 0){
						while($rowGetIdxInvRCOId = imw_fetch_array($rsGetIdxInvRCOId)){
							$dbIdxInvRCOId = 0;
							$dbInvisionPlanCode = $dbInvisionPlanDescription = $dbIDXDescription = $dbIDXFSC = "";
							$dbIdxInvRCOId = $rowGetIdxInvRCOId['id'];
							$dbInvisionPlanCode = $rowGetIdxInvRCOId['invision_plan_code'];
							$dbInvisionPlanDescription = $rowGetIdxInvRCOId['invision_plan_description'];
							$dbIDXDescription = $rowGetIdxInvRCOId['IDX_description'];
							$dbIDXFSC = $rowGetIdxInvRCOId['IDX_FSC'];
							
							if(($insCompName || $insCompInHouseCode) && ((empty($dbInvisionPlanCode) == false) && (empty($dbInvisionPlanDescription) == false) && (empty($dbIDXDescription) == false) && (empty($dbIDXFSC) == false) && ($dbIdxInvRCOId > 0))){
								if (preg_match("/[a-zA-Z0-9]/", $insCompName) || preg_match("/[a-zA-Z0-9]/", $insCompInHouseCode)) {					
									$insCompDataXML .= "<insCompInfo insCompId=\"".$insCompId."\" insCompINHouseCode=\"".$insCompINHouseCode."\" insCompName=\"".$insCompName."\"  insCompAdd=\"".$insCompAdd."\" insCompCity=\"".$insCompCity."\" insCompState=\"".$insCompState."\" insCompZip=\"".$insCompZip."\" dbIdxInvRCOId=\"".$dbIdxInvRCOId."\" dbInvisionPlanCode=\"".$dbInvisionPlanCode."\" dbInvisionPlanDescription=\"".$dbInvisionPlanDescription."\" dbIDXDescription=\"".$dbIDXDescription."\" dbIDXFSC=\"".$dbIDXFSC."\" ></insCompInfo>";				
								}
							}
							
						}
						imw_free_result($rsGetIdxInvRCOId);
					}
				}
				imw_free_result($rsInsComp);
				$insCompDataXML .= "</insCompData>";				
				$insCompXMLFile = $this->siteRootDir."xml/Insurance_Comp_Cross_Map.xml";
				file_put_contents($insCompXMLFile,$insCompDataXML);
			}	
		}
		return $insCompXMLFile;
	}
	
	//to create xml for medications type ahead
	function create_medications_xml()
	{
		$getMedication = "select DISTINCT(md.medicine_name) as medName,md.id as medId, md.ocular as medOcular, md.alert as medAlert, md.ccda_code, md.fdb_id,  od.dosage, od.sig from medicine_data md 
		LEFT JOIN order_details od 
		ON (md.id=od.med_id and od.delete_status=0)
		where md.del_status = 0 order by medName";		
		$rsGetMedication = imw_query($getMedication); 
		if($rsGetMedication){
			if(imw_num_rows($rsGetMedication)>0){
				$medicationDataXML = "";
				$medicationData = array();
				$aa = "?>";
				$medicationDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$medicationDataXML .= "<medicationsData>";
				$rep = array("'","’");			
				while ($rowGetMedication = imw_fetch_array($rsGetMedication)){							
					$medName = $medId = $sig = $dosage = "";	
					$intDbMedOcular	= $intDbMedAlert = 0;
					$medId = $rowGetMedication['medId'];
					$medName = trim(htmlentities(str_replace($rep,"",$rowGetMedication['medName'])));	
					//echo $medName = filter_var($medName, FILTER_SANITIZE_URL);						
					$intDbMedOcular = $rowGetMedication['medOcular'];
					$intDbMedAlert = $rowGetMedication['medAlert'];
					
					$sig = trim(htmlentities(str_replace($rep,"",$rowGetMedication['sig'])));	
					$dosage = trim(htmlentities(str_replace($rep,"",$rowGetMedication['dosage'])));
					
					if($medName){
						if (preg_match("/[a-zA-Z0-9]/", $medName)) {					
							$medicationDataXML .= "<medicationInfo name=\"".$medName."\" id=\"".$medId."\" medOcular=\"".$intDbMedOcular."\" medAlert=\"".$intDbMedAlert."\" ccda_code=\"".$rowGetMedication['ccda_code']."\" fdb_id=\"".$rowGetMedication['fdb_id']."\" dosage=\"".$dosage."\" sig=\"".$sig."\"></medicationInfo>";				
						}
					}
				}
				imw_free_result($rsGetMedication);
				$medicationDataXML .= "</medicationsData>";				
				$medicationXMLFile = $this->siteRootDir."xml/Medications.xml";				
				file_put_contents($medicationXMLFile,$medicationDataXML);
			}	
		}
		return $medicationXMLFile;
	}
	
	
	//Create xml for allergies typeahead
	//to create xml for medications type ahead
	function create_allergies_xml(){			
		$getAllergies = "select DISTINCT(allergie_name) as allergiesName,allergies_id as allergiesId from allergies_data order by allergiesName";		
		$rsGetAllergies = imw_query($getAllergies); 
		if($rsGetAllergies){
			if(imw_num_rows($rsGetAllergies)>0){
				$allergiesDataXML = "";
				$allergiesData = array();
				$aa = "?>";
				$allergiesDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$allergiesDataXML .= "<allergiesData>";
				$rep = array("'","'");			
				while ($rowGetAllergies = imw_fetch_array($rsGetAllergies)){							
					$allergiesName = $allergiesId = "";				
					$allergiesId = $rowGetAllergies['allergiesId'];
					$allergiesName = trim(htmlentities(str_replace($rep,"",$rowGetAllergies['allergiesName'])));	
					//$allergiesName = filter_var($allergiesName, FILTER_SANITIZE_URL);														
					if($allergiesName){
						if (preg_match("/[a-zA-Z0-9]/", $allergiesName)) {					
							$allergiesDataXML .= "<allergiesInfo name=\"".$allergiesName."\" id=\"".$allergiesId."\"></allergiesInfo>";				
						}
					}
				}
				imw_free_result($rsGetAllergies);
				$allergiesDataXML .= "</allergiesData>";				
				$allergiesXMLFile = $this->siteRootDir."xml/Allergies.xml";				
				file_put_contents($allergiesXMLFile,$allergiesDataXML);
			}	
		}
		return $allergiesXMLFile;
	}	
	
	// Create Xml for Sx Procedures
	function create_sx_procedures_xml()
	{			
		$getSxPro = "select DISTINCT(title) as sx_title from  lists_admin where type in (5,6) and delete_status = 0 order by sx_title";		
		$rsGetSxPro = imw_query($getSxPro); 
		if($rsGetSxPro){
			if(imw_num_rows($rsGetSxPro)>0){
				$sxProceduresDataXML = "";
				$sxProceduresData = array();
				$aa = "?>";
				$sxProceduresDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$sxProceduresDataXML .= "<sxProceduresData>";
				while ($rowGetSxPro = imw_fetch_array($rsGetSxPro)){							
					$SxProcedureName = $intListId = "";
					$SxProcedureName = trim(htmlentities(stripslashes($rowGetSxPro['sx_title'])));
					//$SxProcedureName = stripslashes(filter_var($SxProcedureName, FILTER_SANITIZE_URL));
					if($SxProcedureName){
						if (preg_match("/[a-zA-Z0-9]/", $SxProcedureName)) {					
							$sxProceduresDataXML .= "<sxProceduresInfo name=\"".$SxProcedureName."\"></sxProceduresInfo>";				
						}
					}
				}
				imw_free_result($rsGetSxPro);
				$sxProceduresDataXML .= "</sxProceduresData>";				
				$sxProceduresXMLFile = $this->siteRootDir."xml/SxProcedures.xml";				
				file_put_contents($sxProceduresXMLFile,$sxProceduresDataXML);
			}	
		}
		return $sxProceduresXMLFile;
	}
	
	function getRefPhyName($id){
		$strName = '';
		if(!empty($id)){
			$qry = "select Title,FirstName,MiddleName,LastName from refferphysician where physician_Reffer_id = ".$id;
			$res = imw_query($qry);
			if(imw_num_rows($res)>0){
				$row = imw_fetch_assoc($res);
				if(!isset($GLOBALS['REF_PHY_FORMAT']) || strtolower($GLOBALS['REF_PHY_FORMAT']) != 'boston'){
					$strName .= $row['Title'] != '' ? trim($row['Title']).' ':'';
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']):'';
				}
				else if(isset($GLOBALS['REF_PHY_FORMAT']) && strtolower($GLOBALS['REF_PHY_FORMAT']) == 'boston'){
					$strName .= $row['LastName'] != '' ? trim($row['LastName']).', ':'';
					$strName .= $row['FirstName'] != '' ? trim($row['FirstName']).' ':'';
					$strName .= $row['MiddleName'] != '' ? trim($row['MiddleName']).' ':'';
					$strName .= $row['Title'] != '' ? trim($row['Title']):'';
				}
			}
		}
		return $strName;
	}
	
	
	function getRefferPhysician($condition,$value){
		$query = "select * from refferphysician where $condition like '$value%' and delete_status = '0'
				order by FirstName asc";
		$qryId = imw_query($query);
		if(imw_num_rows($qryId)>0){
			while($tmpData=imw_fetch_assoc($qryId))
			{
				$return[]=$tmpData;
			}
		}
		return $return;
	}
	

	public function mysqlifetchdata($query=''){
		$return = array();
		$sql = imw_query($query);
		$cnt = imw_num_rows($sql);
		if($cnt > 0 )
		{
			while( $row = imw_fetch_assoc($sql))
			{
				$return[] = $row;
			}
		}
		return $return;
	}
	
	function __getApptFuture($patient_id,$report_start_date,$report_end_date,$time_inc){
		$appFu = '';
		$dateRange=" AND sc.sa_app_start_date >= current_date() ";
		if($report_end_date){
			$dateRange=" AND sc.sa_app_start_date >='".$report_end_date."' ";	
		}
		$schDataQry = "SELECT DATE_FORMAT(sc.sa_app_start_date,'%m-%d-%Y') as appStrtDate,DATE_FORMAT(sc.sa_app_starttime,'%h:%i %p') as appStrtTime,
						sc.sa_patient_app_status_id as appStatus, CONCAT_WS(', ', us.lname, us.fname) as doctorName,fac.name as facName,slp.proc as procName 
						FROM schedule_appointments sc 
						LEFT JOIN users us ON us.id = sc.sa_doctor_id 
						LEFT JOIN facility fac ON fac.id = sc.sa_facility_id 
						LEFT JOIN slot_procedures slp ON slp.id = sc.procedureid 
						WHERE sc.sa_patient_id = '".$patient_id."'
						AND sc.sa_patient_app_status_id != '18'
						$dateRange
						ORDER BY sc.sa_app_start_date ASC
						";
		
		$schDataQryRes = $this->mysqlifetchdata($schDataQry);
		$schDataDetailsArr = array();
		if(count($schDataQryRes)>0) {
			$appStatusArr = array(18=>"Cancel",13=>"Check-in",11=>"Check-out",2=>"Chart Pulled",202=>"Reschedule",6=>"Left Without Visit",3=>"No-Show",
									201=>"To-Do",0=>"New",200=>"Room # assignment",7=>"Insurance/Financial Issue");
			
			$appStatus='';
			foreach($appStatusArr as $key=>$val) {
				if($key==$schDataQryRes[$i]['appStatus']) {
					$appStatus = $val;	
				}
			}
			$appFu.='<table  border="0" cellpadding="2" cellspacing="2" style="width:650px;">
						<tr align="left">
							<td class="text_10b" valign="top" style="width:100px; height:20px;"><strong>Date</strong></td>';
			if($time_inc==''){
				$appFu.='	<td class="text_10b" valign="top" style="width:80px; height:20px;"><strong>Time</strong></td>';
			}
				$appFu.='	<td class="text_10b" valign="top" style="width:120px; height:20px;padding-left:10px;"><strong>Doctor</strong></td>
							<td class="text_10b" valign="top" style="width:130px; height:20px;"><strong>Facility</strong></td>
							<td class="text_10b" valign="top" style="width:140px; height:20px;"><strong>Procedure</strong></td>';
			if($time_inc==''){				
				$appFu.='	<td class="text_10b" valign="top" style="width:100px;"><strong>Status</strong></td>';
			}
				$appFu.='</tr>';
				
			for($i=0;$i<count($schDataQryRes);$i++){
				$appFu.='
						<tr align="left">
							<td class="text_10" valign="top" style=" height:20px;">'.$schDataQryRes[$i]['appStrtDate'].'</td>';
				if($time_inc==''){			
					$appFu.='<td class="text_10" valign="top" style=" height:20px;">'.$schDataQryRes[$i]['appStrtTime'].'</td>';
				}
				$appFu.='	<td class="text_10" valign="top" style="padding-left:10px; height:20px;">'.$schDataQryRes[$i]['doctorName'].'</td>
							<td class="text_10" valign="top" style=" height:20px;">'.$schDataQryRes[$i]['facName'].'</td>
							<td class="text_10" valign="top" style=" height:20px;">'.$schDataQryRes[$i]['procName'].'</td>';
				if($time_inc==''){
					$appFu.='	<td class="text_10" valign="top" style=" height:20px;">'.$appStatus.'</td>';
				}
					$appFu.='</tr>';
			}
			$appFu.='</table>';
		}else{$appFu.='&nbsp;&nbsp;No appointments';}
		return $appFu;
	}
	
	function XMLToArray($XML){
		$values = array();
		$parser = xml_parser_create();
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
		xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
		xml_parse_into_struct($parser, $XML, $values);
		xml_parser_free($parser);
		return $values;
	}

	function createProviderXML($orderBy = "lname, fname"){			
		$getProqry = "select id,fname,mname,lname,Enable_Scheduler,user_type,delete_status from users order by ".$orderBy." ";
		$rsGetproqry = imw_query($getProqry); 
		if($rsGetproqry){
			if(imw_num_rows($rsGetproqry)>0){
				$providerDataXML = "";
				$proAll = array();
				$aa = "?>";
				$providerDataXML = "<?xml version=\"1.0\" encoding=\"UTF-8\"$aa";
				$providerDataXML .= "<providerData>";
				$rep = array("'"," ","’");			
				while ($rowGetproqry = imw_fetch_array($rsGetproqry)){			
					$proID = $proFname = $proMname = $proLname = $enSch = "";
					$proID = $rowGetproqry['id'];
					$proFname = trim(htmlentities(str_replace($rep,"",$rowGetproqry['fname'])));
					//$proFname = filter_var($proFname, FILTER_SANITIZE_URL);	
																		
					$proMname = trim(htmlentities(str_replace($rep,"",$rowGetproqry['mname'])));
					//$proMname = filter_var($proMname, FILTER_SANITIZE_URL);														
					
					$proLname = trim(htmlentities(str_replace($rep,"",$rowGetproqry['lname'])));
					//$proLname = filter_var($proLname, FILTER_SANITIZE_URL);														
					
					$user_type = trim($rowGetproqry['user_type']);
					//$user_type = filter_var($user_type, FILTER_SANITIZE_URL);														
					
					$enSch = trim($rowGetproqry['Enable_Scheduler']);
					//$enSch = filter_var($enSch, FILTER_SANITIZE_URL);														
					
					$proStatus = trim($rowGetproqry['delete_status']);
					
					if($proFname && $proLname){
						if (preg_match("/[a-zA-Z]/", $proFname) && preg_match("/[a-zA-Z]/", $proLname)) {
							$providerDataXML .= "<proInfo proId=\"".$proID."\" proFName=\"".$proFname."\"  proMname=\"".$proMname."\" proLname=\"".$proLname."\"  enSch=\"".$enSch."\"  user_type=\"".$user_type."\" proStatus=\"".$proStatus."\"></proInfo>";				
						}
					}
				}
				imw_free_result($rsGetproqry);
				$providerDataXML .= "</providerData>";				
				$proXMLFile = $this->siteRootDir."xml/Provider_Data.xml";
				file_put_contents($proXMLFile,$providerDataXML);
			}	
		}
		return $proXMLFile;
	}


	function dropDown_providers($saved_val = "",$EnableSch="",$user_type="", $returnType="", $callFrom="", $reportType="", $arr_exclude_user= array())
	{
		$prividerDataArr = array();
		$drop_down = "";
		
		if($user_type!='')
		{	
			$qryInit = 'Select user_type_id from user_type WHERE ';
			$user_type_attending = 0;					
			if($user_type==1){	$user_type_name= 'Physician'; $qryUsrType = ' user_type_name IN("Physician","Attending Physician","Physician Assistant")';}
			if($user_type==3){	$user_type_name= 'Technician'; $qryUsrType = ' user_type_name = "Technician"';}
			if($user_type==6){	$user_type_name= 'Surgical Coordinator'; $qryUsrType = ' user_type_name = "Surgical Coordinator"';}
			if($user_type==2){	$user_type_name= 'Nurse'; $qryUsrType = ' user_type_name = "Nurse"';}
			$qryReq = $qryInit.$qryUsrType;	
			$qryRs=imw_query($qryReq);
			$qryResRows = imw_num_rows($qryRs);
			$qryRes = imw_fetch_row($qryRs);
			$user_type = $qryRes[0];
			
			if($qryResRows > 1)
			{
				$qryRes = imw_fetch_row($qryRs);
				$user_type_attending = $qryRes[0];							
			}
			if($qryResRows > 2)
			{
				$qryRes = imw_fetch_row($qryRs);
				$user_type_assistant = $qryRes[0];							
			}
		}
		if($reportType=='consult_letter') {
			$uTypeIdArr=array();
			$uTypeQry = 'Select user_type_id from user_type WHERE user_type_name IN("Physician","Attending Physician","Fellow","Resident","Physician Assistant")';	
			$uTypeRes=imw_query($uTypeQry);
			if(imw_num_rows($uTypeRes)>0) {
				while($uTypeRow = imw_fetch_array($uTypeRes)) {
					$uTypeIdArr[]=$uTypeRow['user_type_id'];
				}
			}
		}
		$providerXMLFile = $this->siteRootDir."xml/Provider_Data.xml";
		if(file_exists($providerXMLFile)){
			$providerXMLFileExits = true;
		}
		else{
			$this->createProviderXML();	
			if(file_exists($providerXMLFile)){
				$providerXMLFileExits = true;	
			}	
		}
		if($providerXMLFileExits == true){
			$values = array();
			$XML = file_get_contents($providerXMLFile);
			$values = $this->XMLToArray($XML);
			
				$active_provider_array = array();
				$inactive_provider_array = array();
				
				foreach($values as $val_new)
				{
					if ($val_new['attributes']['proStatus']==0){
						$active_provider_array[] = $val_new;
					} else if ($val_new['attributes']['proStatus']==1){
						$inactive_provider_array[] = $val_new;
					}
				}
				$values = array_merge($active_provider_array,$inactive_provider_array);	
				
				foreach($values as $val)
				{
					if(trim(!empty($val['attributes']['proId']))){
						if(($callFrom=='report') || ($callFrom=='' && $val['attributes']['proStatus']==0)){
							if(!empty($val['attributes']['proLname']) || !empty($val['attributes']['proMname'])){
								$Last_name = ', '.$val['attributes']['proFName'];
							}
							else{
								$Last_name = $val['attributes']['proFName'];
							}
							$firstName=ucfirst($val['attributes']['proLname']).' '.ucfirst($val['attributes']['proMname']);
							$print_val = trim($firstName).ucfirst(trim($Last_name));
							
							if($callFrom=='report'){
								$print_val.="*~|#".$val['attributes']['proStatus'];
							}
							
							$select="";
							//------------Select Option -----------//
							if($saved_val!=""){
								$saved_val_arr = explode(",",$saved_val);
								if(in_array($val['attributes']['proId'] ,$saved_val_arr)){
									$select="selected='selected'";
								}
							}							
/*							if ($val['attributes']['proId'] == $saved_val){
							$select = "selected='selected'";
							}*/
							if(empty($returnType) === true){
								
								if(!empty($user_type) && $EnableSch=='1' && ($user_type == $val['attributes']['user_type'] || $user_type_attending == $val['attributes']['user_type'] || $user_type_assistant == $val['attributes']['user_type'] || $val['attributes']['enSch']=='1')){
									$drop_down .= "<option $select value='".$val['attributes']['proId']."'>".$print_val."</option>";
								}else if(!empty($user_type) && ($user_type == $val['attributes']['user_type'] || $user_type_attending == $val['attributes']['user_type'] || $user_type_assistant == $val['attributes']['user_type'])){
									$drop_down .= "<option $select value='".$val['attributes']['proId']."'>".$print_val."</option>";
								}
								else if(empty($user_type)){
									if($reportType=='consult_letter' && (($EnableSch=='1'&& $val['attributes']['enSch']=='1') || (in_array($val['attributes']['user_type'],$uTypeIdArr)))) {
										$drop_down .= "<option $select value='".$val['attributes']['proId']."'>$print_val</option>";		
									}
									else if($EnableSch=='1'&& $val['attributes']['enSch']=='1'){
										$drop_down .= "<option $select value='".$val['attributes']['proId']."'>$print_val</option>";
									}
									else if($EnableSch!='1' && count($arr_exclude_user)>0 && !in_array($val['attributes']['user_type'],$arr_exclude_user)){
										$drop_down .= "<option $select value='".$val['attributes']['proId']."'>$print_val</option>";
									}else if($EnableSch!='1' && count($arr_exclude_user)<=0){
										$drop_down .= "<option $select value='".$val['attributes']['proId']."'>$print_val</option>";
									}
								}
							}
							else{
								if(!empty($user_type) && ($user_type == $val['attributes']['user_type']) || ($EnableSch=='1' && $val['attributes']['enSch']=='1')){
									$prividerDataArr[$val['attributes']['proId']] = $print_val;
								}else if(empty($user_type)){
									$prividerDataArr[$val['attributes']['proId']] = $print_val;
								}
							}
					  }
					}
				}//print_r(array_merge($active_provider_array,$inactive_provider_array));	
		}
		
	
		if(empty($returnType) === true){
			return $drop_down;
		}else{
			return $prividerDataArr;
		}
	}
	
	function changeDateSelection(){
		$globalDateFormat= inter_date_format();
		$source=array('dd', 'mm', 'yyyy');
		$target=array('d', 'm', 'Y');
		$globalDateFormat=str_replace($source, $target, $globalDateFormat);
		
		$day = date('w')-1;				
		if($day < 0){
			$StartDay = 6;
		}
		else{
			$StartDay = $day;
		}
		$newDate = date($globalDateFormat, mktime(0, 0, 0, date("m")  , date("d")-$StartDay, date("Y")));
		$monthDate  = date($globalDateFormat ,mktime(0, 0, 0, date("m")  , '01', date("Y")));
		
		//$newDate  = date('m-d-Y',mktime(0, 0, 0, date("m")  , date("d")-$StartDay, date("Y")));
		//$monthDate  = date('m-d-Y',mktime(0, 0, 0, date("m")  , '01', date("Y")));
		
		$arr_quaMon = array('1' => 1, '2' => 4, '3' => 7, '4' => 10);
		$quarter = $arr_quaMon[ceil(date('n')/3)];
		
		$quarter = $quarter < 10 ? '0'.$quarter : $quarter;
		$quater_month_start = date($globalDateFormat, strtotime(date('Y').'-'.$quarter.'-01'));
		$quater_month_end = date($globalDateFormat, mktime(0,0,0,$quarter+3,1-1,date('Y')));
			
		//$quater_month_start = $quarter.'-01-'.date('Y');		
		//$quater_month_end = date('m-d-Y',mktime(0,0,0,$quarter+3,1-1,date('Y')));		
		
		$arrDateRange['WEEK_DATE']= $newDate;
		$arrDateRange['MONTH_DATE']= $monthDate;
		$arrDateRange['QUARTER_DATE_START']= $quater_month_start;
		$arrDateRange['QUARTER_DATE_END']= $quater_month_end;
		
		return $arrDateRange;
	}	
		
	// Fetch ids of Providers - Added by Jaswant
	function provider_Ids($saved_val = "",$EnableSch="",$user_type="")
	{
		$providerIds = array();
		$providerXMLFile = $this->siteRootDir."xml/Provider_Data.xml";
		if(file_exists($providerXMLFile)){
			$providerXMLFileExits = true;
		}
		else{
			$this->createProviderXML();	
			if(file_exists($providerXMLFile)){
				$providerXMLFileExits = true;	
			}	
		}
		if($providerXMLFileExits == true){
			$values = array();
			$XML = file_get_contents($providerXMLFile);
			$values = $this->XMLToArray($XML);
				foreach($values as $val)
				{
					if(trim(!empty($val['attributes']['proId']))){
							if(!empty($val['attributes']['proLname']) || !empty($val['attributes']['proMname'])){
								$Last_name = ', '.$val['attributes']['proFName'];
							}
							else{
								$Last_name = $val['attributes']['proFName'];
							}
							$firstName=ucfirst($val['attributes']['proLname']).' '.ucfirst($val['attributes']['proMname']);
							$print_val = trim($firstName).ucfirst(trim($Last_name)) ;
							
							if(!empty($user_type) && $user_type == $val['attributes']['user_type']){
							    $providerIds[$val['attributes']['proId']] = $val['attributes']['proId'];
							}
							else if(empty($user_type)){
								if($EnableSch=='1'&& $val['attributes']['enSch']=='1'){
									$providerIds[$val['attributes']['proId']] = $val['attributes']['proId'];
								}							
								else if($EnableSch!='1'){
									$providerIds[$val['attributes']['proId']] = $val['attributes']['proId'];
								}
							}
					  }
				}	
		}
		return $providerIds;
	}
    
    function get_ref_phy_del_status($id){
        if(!$id){ return false;}
		$ref_phy_status = true;
        $qry = "select delete_status from refferphysician where physician_Reffer_id = ".$id;
        $res = imw_query($qry);
        if(imw_num_rows($res)>0){
            $row = imw_fetch_assoc($res);
            if($row['delete_status']==0) {
                $ref_phy_status = false;
            }
        }
		return $ref_phy_status;
	}
}
?>