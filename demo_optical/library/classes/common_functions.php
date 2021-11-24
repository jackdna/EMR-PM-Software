<?php 
/*------function to validate form input data, set parameter for validation type----------------*/
function validate_input($str,$type='uname'){
	
}

function pre($array,$bl=false){
	echo '<pre>';
	print_r($array);
	echo '</pre>';
	if($bl) die();
}

function takeUserInput($str,$html=false){
	if(!$html) $str = strip_tags($str);
	return htmlentities(trim($str));
}

function giveUserInput($str){
	return html_entity_decode($str);
}
function resizeImage($img_src='', $width='',$height=''){
	if(file_exists($img_src) && $img_src != ''){
		$arrSize = getimagesize($img_src);
		$imgW = $arrSize['0'];
		$imgH = $arrSize['1'];
		
		$wFrac = $imgW/$width;
		$hFrac = $imgH/$width;
		
		$frac = ($wFrac > $hFrac)? $wFrac : $hFrac;
		$height = $imgH/$frac;
		$str = "<img  width='".$width."' height='".$height."' src ='".$img_src."'>";
	}else{
		$str = '<img src="'.$GLOBALS['WEB_PATH'].'/images/no_product_image.jpg" width="'.$width.'" height="'.$height.'">';
	}
	return $str;
}

function ins_providers(){
	$r8PracticeName = explode('/',$GLOBALS['IMW_WEB_PATH']);
	if(file_exists(realpath($GLOBALS['IMW_DIR_PATH'].'/data/'.$r8PracticeName[3]."/xml/Insurance_Comp.xml")) ) {
		$insCompXMLFile = realpath($GLOBALS['IMW_DIR_PATH'].'/data/'.$r8PracticeName[3]."/xml/Insurance_Comp.xml");
	}else{
		$insCompXMLFile = realpath($GLOBALS['IMW_DIR_PATH']."/xml/Insurance_Comp.xml");
	}
	
	
	
	$r8PracticeName = explode('/',$GLOBALS['IMW_WEB_PATH']);
	if($GLOBALS['SUB_DOMAIN']){$xml_path=$GLOBALS['IMW_DIR_PATH'].'/data/'.$GLOBALS['SUB_DOMAIN']."/xml/Insurance_Comp.xml";}
	else {
		$r8PracticeName = explode('/',$GLOBALS['IMW_WEB_PATH']);
		$xml_path=$GLOBALS['IMW_DIR_PATH'].'/data/'.$r8PracticeName[3]."/xml/Insurance_Comp.xml";
	}
	if(file_exists(realpath($xml_path)) ) {
		$insCompXMLFile = realpath($xml_path);
	}else{
		$insCompXMLFile = realpath($GLOBALS['IMW_DIR_PATH']."/xml/Insurance_Comp.xml");
	}
	
	
	
if(file_exists($insCompXMLFile)){
	$insCompXMLFileExits = true;
}
if($insCompXMLFileExits == true){
	$values = array();
	$XML = file_get_contents($insCompXMLFile);
	$values = XMLToArray($XML);		
	$arrInsCompData = array();	
	$ptResName = "";
	$resNameComp = "";	
	foreach($values as $key => $val){
		$insRtName = "";		
		if( ($val["tag"] =="insCompInfo") && ($val["type"]=="complete") && ($val["level"]=="2") ){		
			$insCompId = $insCompINHouseCode = $insCompName = $insCompAdd = $insCompCity = $insCompState = $insCompZip = "";
			$crossMapIdxInvRCOId = $crossMapInvisionPlanCode = $crossMapInvisionPlanDescription = $crossMapIDXDescription = $crossMapIDXFSC = "";
			
			$insCompId = $val["attributes"]["insCompId"];	
			$insCompINHouseCode = str_replace("'","",$val["attributes"]["insCompINHouseCode"]);
			$insCompName = str_replace("'","",$val["attributes"]["insCompName"]);
			$insCompAdd = str_replace("'","",$val["attributes"]["insCompAdd"]);
			$insCompCity = str_replace("'","",$val["attributes"]["insCompCity"]);
			$insCompState = str_replace("'","",$val["attributes"]["insCompState"]);
			$insCompZip = str_replace("'","",$val["attributes"]["insCompZip"]);
			
			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
				$crossMapIdxInvRCOId = str_replace("'","",$val["attributes"]["dbIdxInvRCOId"]);
				$crossMapInvisionPlanCode = str_replace("'","",$val["attributes"]["dbInvisionPlanCode"]);
				$crossMapInvisionPlanDescription = str_replace("'","",$val["attributes"]["dbInvisionPlanDescription"]);
				$crossMapIDXDescription = str_replace("'","",$val["attributes"]["dbIDXDescription"]);
				$crossMapIDXFSC = str_replace("'","",$val["attributes"]["dbIDXFSC"]);
			}
			
			//setting Resp. Party
			if(is_numeric($resName) == true){			
				if(trim($insCompId) == trim($resName) && empty($ptResName) == true){				
					if(strlen($insCompName) > 12){
						$resNameComp = substr($insCompName,0,12).'....';
					}
					else{
						$resNameComp = $insCompName;
					}				
					$resNameComp = trim($resNameComp);					
				}
			}
			//setting In House Code For All Ins.
			if($insCompINHouseCode){
				$insRtName = $insCompINHouseCode;
			}else{
				$insRtName = substr($insCompName,0,4).'....';
			}		
			//setting Ins. Comp Name For Primary Ins.
			if($insCompId == $primaryComDetail->provider){
				$primaryInsCompanyName = ($insCompName == "") ? 'Unassigned' : $insCompName;
				$primaryInsInHouseCode = $insRtName;
				$primaryInsCompanyId = $insCompId; 
			}
			/*if(empty($primaryInsCompanyName) == true and $primaryComDetail->provider > 0){
				if(strlen($primaryComDetail->comp_name) > 12){
					$primaryInsCompanyName = substr($primaryComDetail->comp_name,0,12).'....';
				}else{
					$primaryInsCompanyName = $primaryComDetail->comp_name;
				}
			}*/
			
			if($insCompId == $primaryComProvider){
				$insprimaryComProvider = $insCompName;
			}	
			//setting Ins. Comp Name DropDown DIV Primary Ins. 
			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
				$insPriName = "";
				$insPriName = $crossMapInvisionPlanCode." - ".$crossMapInvisionPlanDescription." - ".$crossMapIDXDescription." - ".$crossMapIDXFSC." - ".$insCompName;
				$insName .= '
							<a href="javascript:void(0);" class="text_10ab" onmouseover="getToolTip(\''.$insCompId.'\');" onclick="FillName(\''.addslashes($insRtName).'\',\''.$insCompId.'\')">'.trim($insPriName).'</a><br>
							';
			}
			else{
				if($insRtName){
					$insName .= '
								<a href="javascript:void(0);" class="text_10ab" onmouseover="getToolTip(\''.$insCompId.'\');" onclick="FillName(\''.addslashes($insRtName).'\',\''.$insCompId.'\')">'.trim($insRtName).'</a><br>
								';
				}	
			}
			//setting Ins. Comp Name For Secondary Ins.
			/*if($insCompId == $secondaryComDetail->provider){
				$secInsCompanyName = ($insCompName == "") ? 'Unassigned' : $insCompName;
				$secInsInHouseCode = $insRtName;
				$secInsCompanyId = $insCompId; 
			}	*/		
			if($insCompId == $secondaryComProvider){
				$inssecondaryComProvider = $insCompName;
			}
			/*if(empty($secInsCompanyName) == true and $secondaryComDetail->provider > 0){
				if(strlen($secondaryComDetail->comp_name) > 12){
					$secInsCompanyName = substr($secondaryComDetail->comp_name,0,12).'....';
				}else{
					$secInsCompanyName = $secondaryComDetail->comp_name;
				}
			}*/
			//setting Ins. Comp Name DropDown DIV Secondary Ins. 
			if($insRtName){
				$secInsName .= '
								<a href="javascript:void(0);" class="text_10ab" onmouseover="getToolTipSec(\''.$insCompId.'\');" onclick="secFillName(\''.addslashes($insRtName).'\',\''.$insCompId.'\')">'.trim($insRtName).'</a><br>
								';
			}
			
			//setting Ins. Comp Name For Tertiary Ins.
			/*if($insCompId == $tertiaryComDetail->provider){
				$terInsCompanyName = ($insCompName == "") ? 'Unassigned' : $insCompName;
				$terInsInHouseCode = $insRtName;
				$terInsCompanyId = $insCompId; 
			}*/
			if($insCompId == $tertiaryComProvider){
				$instertiaryComProvider = $insCompName;
			}
			/*if(empty($terInsCompanyName) == true and $tertiaryComDetail->provider > 0){
				if(strlen($tertiaryComDetail->comp_name) > 12){
					$terInsCompanyName = substr($tertiaryComDetail->comp_name,0,12).'....';
				}else{
					$terInsCompanyName = $tertiaryComDetail->comp_name;
				}
			}*/
			//setting Ins. Comp Name DropDown DIV Tertiary Ins. 
			$terInsName .= '
							<a class="text_10ab" href="javascript:void(0);" onmouseover="getToolTipTer(\''.$insCompId.'\');" onclick="terFillName(\''.addslashes($insRtName).'\',\''.$insCompId.'\')">'.trim($insRtName).'</a><br>
							';
			//setting Ins Comp Type Ahead
			$sep = '';
			if(empty($insCompINHouseCode) == false){
				$sep = ' - ';
			}
			
			if(constant("EXTERNAL_INS_MAPPING") == "YES"){
				$arrInsCompData[] = "'".$crossMapInvisionPlanCode." - ".$crossMapInvisionPlanDescription." - ".$crossMapIDXDescription." - ".$crossMapIDXFSC." - ".$insCompName." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId-$crossMapIdxInvRCOId'";
			}
			else{
				//$arrInsCompData[] = "'".$insCompINHouseCode." ".$sep." ".$insCompName." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip."'";		
				if(trim($insCompINHouseCode) && trim($insCompName)){
					//$arrInsCompData[] = "'".$insCompName." ".$sep." ".$insCompINHouseCode." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip."'";		
					$arrInsCompData[] = "'".$insCompINHouseCode." ".$sep." ".$insCompName." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId'";		
				}
				elseif((trim($insCompINHouseCode) == "") && (trim($insCompName) != "")){
					$arrInsCompData[] = "'".$insCompName." ".$sep." ".$insCompINHouseCode." ".$insCompAdd." - ".$insCompCity.", ".$insCompState." ".$insCompZip." * $insCompId'";		
				}
			}
			
		}
	}
	if(count($arrInsCompData)>0){
		$strAllInsComp=implode(',',$arrInsCompData);
	}	
}
return $strAllInsComp;
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
	function fnLineBrk($str){
	return str_replace(array("\r","\n"),array("\\r","\\n"),$str);
}

function ins_provider_id($ins_prov_str){
	$insprovider = explode("*",$ins_prov_str);
	$ins_prov_id = trim($insprovider[1]);
	return $ins_prov_id;
}

function provide_facility_name($fac_id)
{
	$fac_name_qry = imw_query("select name from facility where id = '".$fac_id."'");
	$fac_name_row = imw_fetch_assoc($fac_name_qry);
	$fac_name = $fac_name_row['name'];
	return $fac_name;
}
?>