<?php
class Dx{

	function convertICDDxCode($strChkCd, $codetype, $changetype){
		$ret="";
		$strChkCd=trim($strChkCd);
		if(!empty($strChkCd)){
		$sql = "SELECT GROUP_CONCAT(icd9), COUNT(icd9) AS counter,
						icd9, icd10,concat(laterality,',',staging,',',severity) as lat_all
						FROM icd10_data ";
		if($codetype==9){
		$sql .= "WHERE icd9 IN ('".sqlEscStr($strChkCd)."') GROUP BY icd9";
		}else if($codetype==1){
		$sql .= "WHERE icd10 IN ('".sqlEscStr($strChkCd)."') GROUP BY icd10";
		}

		$row=sqlQuery($sql);
		if($row!=false){
			if($changetype==1){
				$ret=$row["icd10"];
			}else{
				$ret=$row["icd9"];
			}
		}
		}
		return $ret;
	}

	function getICD10CodeLit($cd, $gt="laterality"){
		$ret="";
		if(!empty($gt)){
			$sql = "select ".$gt." from icd10_data where icd10 = '".sqlEscStr($cd)."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				if(!empty($row[$gt])){
					$ret= $row[$gt];
				}
			}
		}
		return $ret;
	}

	function modifyICDDxCodeWEye($tmpDxCode_a, $eye, $lids="", $giveasmt="", $chkDx=0){

		$tmpDxCode_a=trim($tmpDxCode_a);
		$tmpDxCode_a=trim($tmpDxCode_a,",");
		$tmpDxCode_ret = "";
		//if multiple dx codes, take first
		$at = explode(",", $tmpDxCode_a);
		$tmpDxAsmt="";
		if(count($at)>0){
		foreach($at as $k => $v){
		$tmpDxCode="";
		$tmpDxCode = trim($v);

		if(empty($tmpDxCode)){return "";}
		$eye=trim($eye);
		if((!empty($eye) || !empty($lids)) && strpos($tmpDxCode,"-")!==false){

			//check  icdcode 10 db table : if code is  empty with eye value (RLB): if Lids then do not change
			if(!empty($eye) && $this->getICD10CodeLit($tmpDxCode) == "1"){
				$tEye="";
				if($eye=="OU"){
					$tEye="3";
					if(empty($tmpDxAsmt)){$tmpDxAsmt="Both Eyes";}

					if(!empty($chkDx)){ //Check if dx code is biletral
						$t_no_bil = $this->getICD10CodeLit($tmpDxCode, "no_bilateral");
						if(!empty($t_no_bil)){
							$tEye="";$tmpDxAsmt="";
						}
					}

				}else if($eye=="OD"){
					$tEye="1";
					if(empty($tmpDxAsmt)){$tmpDxAsmt="Right Eye";}
				} else if($eye=="OS"){
					$tEye="2";
					if(empty($tmpDxAsmt)){$tmpDxAsmt="Left Eye";}
				}
				if(!empty($tEye)){
					if(substr($tmpDxCode,6,1)=='-'){
						$tt = substr($tmpDxCode,0,6);
						$tt1 = substr($tmpDxCode,7);
						$tmpDxCode = $tt.$tEye.$tt1;
					}else{
						if(substr($tmpDxCode,-3,3)!='-X-' || substr($tmpDxCode,-3,3)!='-x-'){
							if((strpos($tmpDxCode, "E11.")!==false || strpos($tmpDxCode, "E10.")!==false) && strlen($tmpDxCode)==8){
								$tt = substr($tmpDxCode,0,7);
								$tt1 = substr($tmpDxCode,8);
								$tmpDxCode = $tt.$tEye.$tt1;
							}else{
								$tt = substr($tmpDxCode,0,5);
								$tt1 = substr($tmpDxCode,6);
								$tmpDxCode = $tt.$tEye.$tt1;
							}
						}
					}
				}
			}else{
				if($this->getICD10CodeLit($tmpDxCode) == "2"){//Lids Site should be RLL, RUL, LLL, LUL



					if(!empty($lids)){$eye=$lids;}//if lids are given separately, use them
					$arr_tasmt_done=array();
					$arr_teye = explode(",", $eye);
					if(count($arr_teye)>0){
					foreach($arr_teye as $k => $v){
						$tmpDxCode_v=$tmpDxCode;
						$teye_v = trim($v);

						$tEye="";
						$tAsmt="";
						if($teye_v=="RUL"){
							$tEye="1";
							$tAsmt="Right Upper Lid";
						}else if($teye_v=="RLL"){
							$tEye="2";
							$tAsmt="Right Lower Lid";
						}else if($teye_v=="LUL"){
							$tEye="4";
							$tAsmt="Left Upper Lid";
						}else if($teye_v=="LLL"){
							$tEye="5";
							$tAsmt="Left Lower Lid";
						}

						if(!empty($tEye)){
							if(substr($tmpDxCode_v,6,1)=='-'){
								$tt = substr($tmpDxCode_v,0,6);
								$tt1 = substr($tmpDxCode_v,7);
								$tmpDxCode_v = $tt.$tEye.$tt1;
							}else if(substr($tmpDxCode_v,5,1)=='-'){
								$tt = substr($tmpDxCode_v,0,5);
								$tt1 = substr($tmpDxCode_v,6);
								$tmpDxCode_v = $tt.$tEye.$tt1;
							}
							$arr_tasmt_done[]=$tAsmt;
						}

						$arr_tdc_done[] = $tmpDxCode_v;
					}
					$arr_tdc_done=array_unique($arr_tdc_done);
					$tmpDxCode = implode(", ",$arr_tdc_done);

					//
					//*
					if(count($arr_tasmt_done)>0){
					$arr_tasmt_done=array_unique($arr_tasmt_done);
					if(empty($tmpDxAsmt)){$tmpDxAsmt = implode(", ",$arr_tasmt_done);}
					}
					//*/
					}
				}
			}
		}


		if(!empty($tmpDxCode_ret)){ $tmpDxCode_ret .= ","; }
		$tmpDxCode_ret .= $tmpDxCode;

		}
		}//
		if($giveasmt=="1"){
			$ret = array($tmpDxCode_ret, $tmpDxAsmt) ;
		}else{
			$ret = $tmpDxCode_ret;
		}

		return  $ret;
		//return $tmpDxCode;
	}

	function getDxTableInfo($dxVal, $icd10="", $dxid="", $adlss=""){
		$dxVal = trim($dxVal);
		if(empty($dxVal)){return "";}
		if(!empty($icd10) && $icd10=="1"){
			$ret = $this->get_dx_desc($dxVal, "icd10_desc", $dxid, $adlss);
			return $ret;
			/*
			$sql = "SELECT id as mainId,icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10, (select count(id) from icd10_data where parent_id = mainId and deleted=0) as parents FROM icd10_data WHERE ((icd9 LIKE '$dxVal%') OR (icd10 LIKE '$dxVal%') OR (icd9_desc LIKE '$dxVal%') OR (icd10_desc LIKE '$dxVal%')) AND deleted=0 AND parent_id=0 ORDER BY parents DESC";
			$row = sqlQuery($sql);
			if($row != false){
				return $row["icd10_desc"];
			}else{
				$ret = false;
				//check with format--
				$dxVal_2 = substr($dxVal, 0, -1);
				$dxVal_2.="-";

				$dxVal_3 = substr($dxVal, 0, -2);
				$dxVal_3.="--";

				$dxVal_4 = substr($dxVal, 0, -3);
				$dxVal_4.="-x-";

				$sql = "SELECT id as mainId,icd10_desc, CONCAT(' [ICD-10: ',icd10,', ICD-9: ',icd9,']') AS code,icd10, (select count(id) from icd10_data where parent_id = mainId and deleted=0) as parents FROM icd10_data WHERE ((icd10 LIKE '$dxVal_2%') OR (icd10 LIKE '$dxVal_3%') OR (icd10 LIKE '$dxVal_4%') ) AND deleted=0 AND parent_id=0 ORDER BY parents DESC";
				$row = sqlQuery($sql);
				if($row != false){
					$ret = $row["icd10_desc"];
				}

				//check with format--
				return $ret;
			}
			*/

		}else{

			$sql = "SELECT diag_description FROM diagnosis_code_tbl WHERE dx_code='".$dxVal."' || d_prac_code='".$dxVal."' ";
			$row = sqlQuery($sql);
			if($row != false){
				return $row["diag_description"];
			}else{
				return false;
			}

		}
	}

function icd10_getLSS($undr){
	$strret="";
	if(!empty($undr)){
		if($undr==3){
			$query2 = "SELECT CONCAT(title,' - ',code) AS name FROM icd10_laterality WHERE under = '".$undr."' AND under>0 AND deleted=0";
		}else{
			$query2 = "SELECT CONCAT(abbr,' - ',code) AS name FROM icd10_laterality WHERE under = '".$undr."' AND under>0 AND deleted=0";
		}
	}else{
		$query2 = "SELECT CONCAT(abbr,' - ',code) AS name FROM icd10_laterality WHERE deleted=0 AND under>0";
	}
	$rez	= sqlStatement($query2);
	for($i=1;$row=sqlFetchArray($rez);$i++){
		$row["name"]=str_replace('Moderate','Mod',$row["name"]);
		$strret.=$row["name"].",";
	}
	return $strret;
}


	function icd10_getDxLSS($chkDesc){
		$strRet=$strLat=$strStg=$strSvr=$strdxIcd10="";

		if(strpos($chkDesc, "E10.")!==false || strpos($chkDesc, "E11.")!==false){
			//echo "\nLen: ".strlen($chkDesc);
			if(strlen($chkDesc)==8){ //diabetic codes

				$strdxIcd10=substr($chkDesc, 0, -3)."---";
				$strLat="R - 1,L - 2,B - 3,"; //lit
				$strSvr="Mild - 2,Mod - 3,Sev - 4,Pdr - 5,"; //staging
				$strStg="W/ ed - 1,2 - 2,3 - 3,4 - 4,5 - 5,W/O ed - 9,"; //encounter

				$strRet.=" strICD10Dx=\"".htmlentities($strdxIcd10, ENT_QUOTES)."\" ";
				if(!empty($strLat)){
					$strRet.=" strLat=\"".htmlentities($strLat, ENT_QUOTES)."\" ";
				}

				if(!empty($strSvr)){
					$strRet.=" strSvr=\"".htmlentities($strSvr, ENT_QUOTES)."\" ";
				}

				if(!empty($strStg)){
					$strRet.=" strStg=\"".htmlentities($strStg, ENT_QUOTES)."\" ";
				}
			}
		}else{

			$sql = "SELECT laterality, staging, severity, icd10,
					id as mainId, (select count(id) from icd10_data where parent_id = mainId and deleted=0) as parents
					FROM icd10_data
					WHERE (icd10 LIKE '".$chkDesc."%')
					AND deleted=0 AND parent_id=0 AND (laterality!='' || severity!='' || staging !='')
					ORDER BY parents DESC";
			$row = sqlQuery($sql);
			if($row==false){
				// v1
				$chkDesc_v1=substr($chkDesc,0,-1)."-";
				// v2
				$chkDesc_v2=substr($chkDesc,0,-2)."--";
				// v3
				$chkDesc_v3=substr($chkDesc,0,-3)."-x-";

				//
				$sql = "SELECT laterality, staging, severity,icd10,
					id as mainId, (select count(id) from icd10_data where parent_id = mainId and deleted=0) as parents
					FROM icd10_data
					WHERE (icd10 LIKE '".$chkDesc_v1."%' OR icd10 LIKE '".$chkDesc_v2."%' OR LOWER(icd10) LIKE '".strtolower($chkDesc_v3)."%')
					AND deleted=0 AND parent_id=0 AND (laterality!='' || severity!='' || staging !='')
					ORDER BY parents DESC";
				$row = sqlQuery($sql);
			}
			if($row!=false){
				if(!empty($row["laterality"])){
					$strLat= $this->icd10_getLSS($row["laterality"]);
				}

				if(!empty($row["severity"])){
					if(strpos($chkDesc, "H35.31")!==false){
						$strStg="Early - 1,Int - 2,Adv atr WO subfoveal - 3,Adv atr W subfoveal - 4,"; //staging
					}elseif(strpos($chkDesc, "H35.32")!==false){
						$strStg="Active NV - 1,Inactive NV - 2,Inactive scar - 3,"; //staging
					}elseif(strpos($chkDesc, "H34.81")!==false || strpos($chkDesc, "H34.83")!==false){
						$strStg="Mac edema - 0,Ret NV - 1,Stable - 2,"; //staging
					}else{
						$strStg= $this->icd10_getLSS($row["severity"]);
					}

				}elseif(!empty($row["staging"])){
					$strSvr= $this->icd10_getLSS($row["staging"]);

				}

				//
				$strdxIcd10=$row["icd10"];
				$strRet.=" strICD10Dx=\"".htmlentities($strdxIcd10, ENT_QUOTES)."\" ";

				/*
				if(empty($strLat) && empty($strSvr) && empty($strStg) ){
					$strLat= icd10_getLSS("");
				}
				*/

				if(!empty($strLat)){
					$strRet.=" strLat=\"".htmlentities($strLat, ENT_QUOTES)."\" ";
				}

				if(!empty($strSvr)){
					$strRet.=" strSvr=\"".htmlentities($strSvr, ENT_QUOTES)."\" ";
				}else	if(!empty($strStg)){
					$strRet.=" strStg=\"".htmlentities($strStg, ENT_QUOTES)."\" ";
				}


				//echo "\nRet:  ".$strRet;

			}
		}
		return $strRet;
	}

	//filter matching dx codes format
	function ar_mrg_dxcodes_icd10($arr1,$arr2){

		if(count($arr1)>0){
			foreach($arr1 as $k => $v){
				$v1 = $v;
				$v=trim(strtolower($v));
				$q1=substr($v,0,-1)."-";
				$q2=substr($v,0,-2)."--";
				$q3=substr($v,0,-3)."-x-";

				if(!in_array_nocase($v, $arr2) && !in_array_nocase($q1, $arr2) &&
					!in_array_nocase($q2, $arr2) && !in_array_nocase($q3, $arr2) ){
					$arr1[]=$v1;
				}
			}
		}
		return $arr1;

	}
	//---

	function refineDx($dxcode){
		$tdx=""; $tid="";
		$dxcode = trim($dxcode);
		if(!empty($dxcode)){
			$artdx = explode("@@@", $dxcode);
			$tdx = trim($artdx[0]);
			$tid = isset($artdx[1]) ? trim($artdx[1]) : "" ;
		}
		return array($tdx, $tid);
	}

	static function isDxCodeBelongsToDos($dos, $dxcode, $dxid=""){
		$ret=true;
		if(!empty($dxcode)){
			$dxcode = filter_var($dxcode, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			$icd_dos_whr="";
			if(strpos($dos,'-')==true){
				$chart_dos=wv_formatDate($dos,0,0,"insert");
				if($chart_dos>='2016-10-01'){
					$icd_dos_whr=" and status!=1";
				}else{
					$icd_dos_whr=" and status!=2";
				}
			}

			$ar=array();
			$dxcode=str_replace(";",",",$dxcode);
			$ar= explode(",",$dxcode);

			//-- // added dx code with -- as some codes may be complete and will not come in sql query
			$strDx = "";
			$strDx2 = "";

			if(count($ar) > 0){
				foreach($ar as $k => $v){
					$v=trim($v);
					if(!empty($v)){
						$v1=substr($v,0,-1)."-";
						$v2=substr($v,0,-2)."--";
						$v3=substr($v,0,-3)."-x-";
						$v4=substr($v,0,-3)."-X-";

						//Exception
						if(strpos($v,'H40.11')!==false){
							if(strpos($v,'X')!==false){
								$v2=""; //Otherwise it will match with new code , which is wrong
							}
							//else{
							//	$v1="";
							//}
						}

						if(!empty($v)){ $v="'".sqlEscStr($v)."',";  }
						if(!empty($v1)){ $v1="'".sqlEscStr($v1)."',";  }
						if(!empty($v2)){ $v2="'".sqlEscStr($v2)."',";  }
						if(!empty($v3)){ $v3="'".sqlEscStr($v3)."',";  }
						if(!empty($v4)){ $v4="'".sqlEscStr($v4)."',";  }
						//
						$strDx .= $v.$v1.$v2.$v3.$v4;
						$strDx2 .= $v;


					}
				}
			}
			//--
			if(!empty($strDx)){ $strDx=trim($strDx);  $strDx=trim($strDx, ","); }
			if(!empty($strDx2)){ $strDx2=trim($strDx2);  $strDx2=trim($strDx2, ","); }
			//--

			//$strDx = "'".implode("','", $ar)."'";

			$sql = "select id from icd10_data where (icd10 IN (".$strDx.") OR icd9 IN (".$strDx2.")) AND deleted='0' ".$icd_dos_whr."";
			$row=sqlQuery($sql);
			if($row==false){
				$ret=false;
			}
		}
		return $ret;
	}
	#################################################################################
	//functions related to a and p policies in physician console
	#################################################################################

	//Get Diagnosis Desc
	function getDxTHs(){
		global $z_flg_arrDesc2;
		$strDesc = $strPracCode = $strSnowmed_ct = "";
		$z_flg_arrDesc2=array();
		$sql = "SELECT diagnosis_category.category, diagnosis_code_tbl.* FROM diagnosis_code_tbl ".
			   "INNER JOIN diagnosis_category ".
			   "ON diagnosis_code_tbl.diag_cat_id = diagnosis_category.diag_cat_id ".
			   "ORDER BY diagnosis_code_tbl.d_prac_code ";
		$rez = imw_query($sql);
		while($row=imw_fetch_assoc($rez))
		{
			if( !empty($row["diag_description"]) && !empty($row["d_prac_code"])){
				$strDesc .= "'".addslashes($row["diag_description"])." - ".$row["d_prac_code"]."', ";
				$strPracCode .= "'".$row["d_prac_code"]."', " ;
				$strDesc2 .= "'".addslashes($row["diag_description"])."', " ;
				$strSnowmed_ct .= "'".addslashes($row["snowmed_ct"])."', " ;
				$z_flg_arrDesc2[]=trim($row["diag_description"]);
			}
		}
		$strDesc = remLineBrk(substr($strDesc,0,-2));
		$strPracCode = remLineBrk(substr($strPracCode,0,-2));
		$strDesc2 = remLineBrk(substr($strDesc2,0,-2));
		$strSnowmed_ct = remLineBrk(substr($strSnowmed_ct,0,-2));
		return array($strDesc,$strPracCode,$strDesc2,$strSnowmed_ct);
	}

	function getCptCodes4Menu(){
		//Array Cpt Codes And Desc
		$arrCptCats4Menu = array();
		$arrCptCodeAndDesc = array();
		$arrActiveCptCodes=array();
		$sql = "SELECT * FROM cpt_category_tbl ORDER BY cpt_category ";
		$rez = imw_query($sql);
		while($rowCat=imw_fetch_assoc($rez))
		{
			$sql1 = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_prac_code  ".
			"FROM cpt_fee_tbl ".
			"WHERE cpt_cat_id = '".$rowCat["cpt_cat_id"]."' AND cpt_fee_tbl.status = 'Active' AND cpt_fee_tbl.delete_status = '0'".
			"ORDER BY cpt_fee_tbl.cpt_desc ";
			$rez1 = imw_query($sql1);
			$arrCodeTmp = array();
			while($row=imw_fetch_assoc($rez))
			{
				if(array_search($row["cpt_desc"],$arrCptCodeAndDesc) === false)
				{
					$arrCptCodeAndDesc[] = addslashes($row["cpt_desc"]);
					$arrActiveCptCodes[addslashes($row["cpt_desc"])]=getCptCodesFromDesc_v2($row["cpt_desc"]);
				}
				//if not equal then add in menu
				if(trim($row["cpt4_code"])!=trim($row["cpt_prac_code"])){
					$arrCptCodeAndDesc[] = $row["cpt4_code"];
				}
				//$arrCodeTmp[] = array($row["cpt4_code"]."-".$row["cpt_desc"],$arrEmpty,$row["cpt4_code"]."~!~".$row["cpt_desc"]);

				$arrCptCodeAndDesc[] = $row["cpt_prac_code"];
				$arrCodeTmp[] = array($row["cpt_prac_code"]."-".$row["cpt_desc"],$arrEmpty,$row["cpt_prac_code"]."~!~".$row["cpt_desc"]);
			}
			if(count($arrCodeTmp) > 0)
			{
				$arrCptCats4Menu[] = array($rowCat["cpt_category"],$arrCodeTmp);
			}
		}

		// implode into string
		$strCptCodeAndDesc = "'".implode("', '",$arrCptCodeAndDesc)."'";
		$strCptCodeAndDesc = remLineBrk($strCptCodeAndDesc);
		$strCptCodeDescActive = imploadAssoc($arrActiveCptCodes);
		$strCptCodeDescActive = remLineBrk($strCptCodeDescActive);

		return array($arrCptCats4Menu,$arrCptCodeAndDesc,$arrActiveCptCodes,$strCptCodeAndDesc,$strCptCodeDescActive);
	}

	function getDXCodes4Menu(){
		//Array Dx Codes
		$arrDxCodes = array();
		$arrDxCodesAndDesc = array();
		$sql = "SELECT * FROM diagnosis_category ORDER BY category ";
		$rez = imw_query($sql);
		while($row=imw_fetch_assoc($rez))
		{
			$cat_id = $row["diag_cat_id"];
			$sql1 = "SELECT * FROM diagnosis_code_tbl WHERE diag_cat_id ='".$cat_id."' AND delete_status='0' ORDER BY d_prac_code,diag_description ";
			$rezCodes =imw_query($sql1);
			$arrSubOptions = array();
			if(imw_num_rows($rezCodes) > 0)
			{
				while($rowCodes=imw_fetch_assoc($rezCodes))
				{
					$arrSubOptions[] = array($rowCodes["dx_code"]." - ".$rowCodes["diag_description"],$arrEmpty,$rowCodes["dx_code"]);
					if(array_search(addslashes($rowCodes["dx_code"]),$arrDxCodesAndDesc) === false)
					{
						$arrDxCodesAndDesc[] = addslashes($rowCodes["dx_code"]);
					}
					if(array_search(addslashes($rowCodes["d_prac_code"]),$arrDxCodesAndDesc) === false)
					{
						$arrDxCodesAndDesc[] = addslashes($rowCodes["d_prac_code"]);
					}
					if(array_search(addslashes($rowCodes["diag_description"]),$arrDxCodesAndDesc) === false)
					{
						$arrDxCodesAndDesc[] = addslashes($rowCodes["diag_description"]);
					}
				}
			}
			if(count($arrSubOptions) > 0)
			{
				$arrDxCodes[] = array($row["category"],$arrSubOptions);
			}
		}
		//implode to string
		$strDxCodesAndDesc = "'".implode("', '",$arrDxCodesAndDesc)."'";
		$strDxCodesAndDesc = remLineBrk($strDxCodesAndDesc);

		return array($arrDxCodes,$arrDxCodesAndDesc,$strDxCodesAndDesc);
	}

	function getDXCodes4Menu10(){
		global $z_flg_uniq_desc, $z_flg_arrDesc2;

		//Array Dx Codes
		$strDesc10 = $strPracCode10 = $strSnowmed_ct10 = "";
		$arrDxCodes = array();
		$arrDxCodesAndDesc = array();
		$sql = "SELECT * FROM icd10_categories ORDER BY title ";
		$rez = imw_query($sql);
		while($row=imw_fetch_assoc($rez))
		{
			$cat_id = $row["id"];
			$sql = "SELECT * FROM icd10_data WHERE cat_id ='".$cat_id."' AND deleted = '0' ORDER BY icd10,icd10_desc ";
			$rezCodes = imw_query($sql);
			$arrSubOptions = array();
			if(imw_num_rows($rezCodes) > 0)
			{
				while($rowCodes=imw_fetch_assoc($rezCodes))
				{
					$arrSubOptions[] = array($rowCodes["icd10"]." - ".$rowCodes["icd10_desc"],$arrEmpty,$rowCodes["icd10"]);
					if(array_search(addslashes($rowCodes["icd10"]),$arrDxCodesAndDesc) === false)
					{
						$arrDxCodesAndDesc[] = addslashes($rowCodes["icd10"]);
					}
					if(array_search(addslashes($rowCodes["icd10_desc"]),$arrDxCodesAndDesc) === false)
					{
						$arrDxCodesAndDesc[] = addslashes($rowCodes["icd10_desc"]);
					}
					if( !empty($rowCodes["icd10_desc"]) && !empty($rowCodes["icd10"])){
						$strDesc10 .= "'".addslashes($rowCodes["icd10_desc"])." - ".addslashes($rowCodes["icd10"])."', ";
						$strPracCode10 .= "'".addslashes($rowCodes["icd10"])."', " ;
						if($z_flg_uniq_desc==1){
							$tmp_arr = preg_grep( "/".trim($rowCodes["icd10_desc"])."/i" , $z_flg_arrDesc2 );
							if(count($tmp_arr)<=0){ //no match
								$strDesc2_10 .= "'".addslashes($rowCodes["icd10_desc"])."', " ;
							}
						}else{
							$strDesc2_10 .= "'".addslashes($rowCodes["icd10_desc"])."', " ;
						}
						$strSnowmed_ct10 .= "" ;
					}
				}
			}
			if(count($arrSubOptions) > 0)
			{
				$arrDxCodes[] = array($row["title"],$arrSubOptions);
			}
		}
		//implode to string
		$strDxCodesAndDesc = "'".implode("', '",$arrDxCodesAndDesc)."'";
		$strDxCodesAndDesc = remLineBrk($strDxCodesAndDesc);

		$strDesc10 = remLineBrk(substr($strDesc10,0,-2));
		$strPracCode10 = remLineBrk(substr($strPracCode10,0,-2));
		$strDesc2_10 = remLineBrk(substr($strDesc2_10,0,-2));
		$strSnowmed_ct10 = remLineBrk(substr($strSnowmed_ct10,0,-2));

		return array($arrDxCodes,$arrDxCodesAndDesc,$strDxCodesAndDesc,$strDesc10,$strPracCode10,$strDesc2_10,$strSnowmed_ct10);
	}

	function getCptCodes4Menu_console(){
		//Array Cpt Codes And Desc
		$arrCptCats4Menu = array();
		$arrCptCodeAndDesc = array();
		$arrActiveCptCodes=array();
		$sql = "SELECT * FROM cpt_category_tbl ORDER BY cpt_category ";
		$rezCat = imw_query($sql);
		while($rowCat=imw_fetch_assoc($rezCat))
		{
			$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt4_code, cpt_fee_tbl.cpt_prac_code,
					(select group_concat(cpt_prac_code) from cpt_fee_tbl where cpt_desc=cpt_fee_tbl.cpt_desc) AS cpt_prac_code_new
					 ".
				 "FROM cpt_fee_tbl ".
					 "WHERE cpt_cat_id = '".$rowCat["cpt_cat_id"]."' AND cpt_fee_tbl.status = 'Active' AND cpt_fee_tbl.delete_status = '0'".
					 "ORDER BY cpt_fee_tbl.cpt_desc ";
			$rez = imw_query($sql);
			$arrCodeTmp = array();
			while($row=imw_fetch_assoc($rez))
			{
				if(array_search($row["cpt_desc"],$arrCptCodeAndDesc) === false)
				{
					$arrCptCodeAndDesc[] = addslashes($row["cpt_desc"]);
					//$arrActiveCptCodes[addslashes($row["cpt_desc"])]=getCptCodesFromDesc_v2($row["cpt_desc"]);
					$arrActiveCptCodes[addslashes($row["cpt_desc"])]=explode(",",$row["cpt_prac_code_new"]);
				}
				//if not equal then add in menu
				if(trim($row["cpt4_code"])!=trim($row["cpt_prac_code"])){
					$arrCptCodeAndDesc[] = $row["cpt4_code"];
				}

				$arrCptCodeAndDesc[] = $row["cpt_prac_code"];
				$arrCodeTmp[] = array($row["cpt_prac_code"]."-".$row["cpt_desc"],$arrEmpty,$row["cpt_prac_code"]."~!~".$row["cpt_desc"]);
			}
			if(count($arrCodeTmp) > 0)
			{
				$arrCptCats4Menu[] = array($rowCat["cpt_category"],$arrCodeTmp);
			}
		}

		// implode into string
		$strCptCodeAndDesc = "'".implode("', '",$arrCptCodeAndDesc)."'";
		$strCptCodeAndDesc = remLineBrk($strCptCodeAndDesc);
		$strCptCodeDescActive = $this->imploadAssoc($arrActiveCptCodes);
		$strCptCodeDescActive = remLineBrk($strCptCodeDescActive);

		return array($arrCptCats4Menu,$arrCptCodeAndDesc,$arrActiveCptCodes,$strCptCodeAndDesc,$strCptCodeDescActive);
	}

	function imploadAssoc($arr)
	{
		$strRet = "";
		foreach($arr as $key => $val)
		{
			$val = (is_array($val)) ? "[".$this->imploadAssoc($val)."]" : "\"".$val."\"";
			$strRet .= "\"".$key."\":".$val.",";
		}
		return "{".substr($strRet,0,-1)."}";
	}

function dx_code_arr_get(){
	$dx_code_qry="Select concat(dx_code,'; ') as dx_code  from diagnosis_code_tbl Where delete_status=0 order by dx_code";
	$dx_code_res=imw_query($dx_code_qry);
	$dx_code_arr=array();
	if(imw_num_rows($dx_code_res)>0){
		while($rs_cpt=imw_fetch_assoc($dx_code_res)){
			$dx_code_arr[]=$rs_cpt['dx_code'];
		}
	}
	return $dx_code_arr;
}

function getDxAses($rtrn=0, $cdx="", $cdxid=""){
	$ret="";

	$dx = !empty($cdx) ? $cdx : rawurldecode($_GET["dx"]);
	$dxid = !empty($cdxid) ? $cdxid : $_GET["dxid"];

	$dx=trim($dx);
	if(!empty($dx)){

	$phrsedxid = (!empty($dxid)) ? " AND id='".$dxid."' " : "";

	$sql = "SELECT icd10_desc FROM icd10_data WHERE icd10 = '".sqlEscStr($dx)."' ".$phrsedxid." AND deleted=0 AND status!=1 ";
	$row = sqlQuery($sql);

	if(!empty($phrsedxid) && $row==false){
		$sql = str_replace($phrsedxid, "", $sql);
		$row = sqlQuery($sql);
	}

	if($row!=false && !empty($row["icd10_desc"])){
		$ret="".$row["icd10_desc"];
	}else{
		$v = $dx;
		$v1=substr($v,0,-1)."-";
		$v2=substr($v,0,-2)."--";
		$v3=substr($v,0,-3)."-x-";
		$v4=substr($v,0,-3)."-X-";

		if(!empty($v1)){ $v1="'".sqlEscStr($v1)."',";  }
		if(!empty($v2)){ $v2="'".sqlEscStr($v2)."',";  }
		if(!empty($v3)){ $v3="'".sqlEscStr($v3)."',";  }
		if(!empty($v4)){ $v4="'".sqlEscStr($v4)."',";  }

		$strDx .= $v1.$v2.$v3.$v4;
		if(!empty($strDx)){ $strDx=trim($strDx);  $strDx=trim($strDx, ","); }
		$sql = "select icd10_desc from icd10_data where (icd10 IN (".$strDx.")) ".$phrsedxid." AND deleted='0' AND status!=1 ";
		$row=sqlQuery($sql);

		if(!empty($phrsedxid) && $row==false){
			$sql = str_replace($phrsedxid, "", $sql);
			$row = sqlQuery($sql);
		}

		if($row!=false && !empty($row["icd10_desc"])){
			$ret="".$row["icd10_desc"];
		}
	}
	}
	if(!empty($rtrn)){ return $ret; }
	else{	echo $ret;}
}

//This function can return any value based on dx code: by default desc
function get_dx_desc($dx, $ret="icd10_desc", $id=0, $adlss=""){
	$dsc="";
	if(!empty($dx)){
		//get lss
		$phrs_lss="";
		if($adlss==1){
			$phrs_lss = ", icd10, laterality, staging, severity ";
		}


		$row = false;
		if(!empty($id)){
			$sql = "SELECT ".$ret.$phrs_lss." FROM icd10_data WHERE id = '".sqlEscStr($id)."' ";
			$row = sqlQuery($sql);
		}

		if($row==false){
			//remove special characters from dx codes
			$dx = filter_var($dx, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
			$q1=substr($dx,0,-1)."-";
			$q2=substr($dx,0,-2)."--";
			$q3=substr($dx,0,-3)."-x-";

			$sql = "SELECT ".$ret.$phrs_lss." FROM icd10_data WHERE icd10 = '".sqlEscStr($dx)."' AND deleted=0 AND status!=1 ";
			$row = sqlQuery($sql);
			if($row==false){
				$sql = "SELECT ".$ret.$phrs_lss." FROM icd10_data WHERE ( icd10 = '".sqlEscStr($q1)."' || icd10 = '".sqlEscStr($q2)."' || icd10 = '".sqlEscStr($q3)."' ) AND deleted=0 AND status!=1 ";
				$row = sqlQuery($sql);
			}
		}

		if($row!=false){
			$dsc = $row[$ret];

			if($adlss==1){
				//--add LSS
				if(strpos($dx,"-")===false){//check if code is complete
					$str_dx_lss = $this->get_lss_from_2dx($dx, $row["icd10"], array($row["laterality"], $row["staging"], $row["severity"]));
					if(!empty($str_dx_lss) && !empty($dsc)){
						$dsc = $dsc."; ".$str_dx_lss;
					}
				}
				//--
			}
		}
	}
	return $dsc;
}

function is_incomplete_dx($dx){
	$dx_r = $this->get_dx_desc($dx,"icd10");
	return (strpos($dx_r,"-")!==false) ? "1" : "0";
}

function match_dx_codes($dx1, $dx2){
	$ret = false;
	$dx1 = trim($dx1);
	$dx2 = trim($dx2);

	$v11=substr($dx1,0,-1)."-";
	$v21=substr($dx1,0,-2)."--";
	$v31=substr($dx1,0,-3)."-x-";
	$v41=substr($dx1,0,-3)."-X-";

	$v12=substr($dx2,0,-1)."-";
	$v22=substr($dx2,0,-2)."--";
	$v32=substr($dx2,0,-3)."-x-";
	$v42=substr($dx2,0,-3)."-X-";

	if($dx1 == $dx2 || $v11==$v12 || $v21==$v22 || $v31==$v33 || $v41==$v44){
		$ret = true;
	}
	return $ret;
}

function in_array_dx($dx, $ar){
	$ret = false;
	if(count($ar)>0){
		foreach($ar as $k => $v){
			$v = trim($v);
			if(!empty($v)){ 	$t = $this->match_dx_codes($dx, $v); 	if($t){ $ret = true; break; } 	}
		}
	}
	return $ret;
}

function getDxWidthDesc($ar, $ar_id=array()){
	$t = array();
	if(count($ar)>0){
		foreach($ar as $k => $w){
			if(!empty($w)){
				$tmpid = (isset($ar_id[$k]) && !empty($ar_id[$k])) ? $ar_id[$k] : "";
				$d = $this->get_dx_desc($w,'icd10_desc',$tmpid) ;
				$t[] = $w." :Dsc: ".$d." :Dsc: ".$tmpid;
			}
		}
	}
	return $t;
}

function get_lss_from_2dx($dx1,$dx2, $arLss){
	$dx1 = trim($dx1);
	$dx2 = trim($dx2);
	$len = strlen($dx1);
	if($len == strlen($dx2)){
		$dif=array();
		for($i=0;$i<$len;$i++){
			if($dx1[$i] != $dx2[$i]){
				$dif[$i] = $dx1[$i];
			}
		}

		$arLss_ln = count($arLss);
		$dif_ln = count($dif);

		if($arLss_ln>0 && $dif_ln>0){

			$lit = $arLss[0]; $stg = $arLss[1]; $svr = $arLss[2];
			$dif_keys = array_keys($dif);

			if($dif_ln == 1){
				$val = array_pop($dif);
				if(!empty($lit)){ $undr=$lit; }
				elseif(!empty($stg)){ $undr=$stg; }
				elseif(!empty($svr)){ $undr=$svr; }

				//--
				$q = "SELECT title FROM icd10_laterality WHERE under = '".$undr."' AND code = '".$val."'  AND under>0 AND deleted=0";
				$row = sqlQuery($q);
				if($row!=false){
					$str_title = $row["title"];
				}
				//--
			}else{
				$e11_title="";
				foreach($dif as $k => $v){
					if(!empty($v)){
						$e11_arr=array("1"=>"PDR With ME - 1","2"=>"withTRD macula - 2","3"=>"with TRD no macula - 3","4"=>"with TRD and RD - 4","5"=>"with stable PDR - 5","9"=>"PDR without ME - 9");
						$undr=0;
						if(!empty($lit)){ $undr=$lit; $lit=0; }
						elseif(!empty($stg)){ $undr=$stg; $stg=0; }
						elseif(!empty($svr)){ $undr=$svr; $svr=0; }
						$val = $v;
						if((stripos($dx2, "E10.35--")!==false || stripos($dx2, "E11.35--")!==false)){
							if($undr=='1'){
								$undr=0;
								$e11_title=$e11_arr[$val]." ";
							}else{
								$undr=1;
							}
						}
						//--
						if(!empty($undr) && !empty($val)){
							$q = "SELECT title FROM icd10_laterality WHERE under = '".$undr."' AND code = '".$val."'  AND under>0 AND deleted=0";
							$row = sqlQuery($q);
							if($row!=false){
								$str_title .= $e11_title.$row["title"]." ";
							}
						}
						//--
					}
				}

			}

			$str_title = trim($str_title);
		}
	}

	return $str_title;

}

function get_dx_titles(){
	$ret=array();
	$ar_dx = $_REQUEST["ar_dx"];
	if(is_array($ar_dx) && count($ar_dx)>0){
		foreach($ar_dx as $k => $v){
			$av = explode(",", $v);
			$dx = trim($av[0]);
			$dxid = trim($av[1]);
			$dxElId = trim($av[2]);
			if(!empty($dx) && !empty($dxElId)){
				$title = $this->get_dx_desc($dx, "icd10_desc", $dxid);
				$title = trim($title);
				if(!empty($title)){
					$ret[$dxElId] = $title;
				}
			}
		}
	}
	echo json_encode($ret);
}

}
?>
