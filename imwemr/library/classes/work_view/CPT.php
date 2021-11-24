<?php

class CPT{
	public $arrCptCodeAndDesc , $flgPtInsMedicare, $arr_SuperbillTestCpt ;

function getSuperTestCpt(){
	$arr=array();
	$sql ="SELECT *
			FROM superbill_test c1
			LEFT JOIN superbill_test_cpt c2 ON c2.superbill_test_id = c1.id
			ORDER BY test, practice_cpt
			";
	$rez=sqlStatement($sql);
	for($i=0;$row=sqlFetchArray($rez);$i++){

		$test = strtoupper($row["test"]);
		$prac_cpt = $row["practice_cpt"];
		$site = $row["site"];
		$var_id = $row["custom_test_variation_id"];
		//$inc_type = strtoupper($row["insurance_type"]);
		if(!empty($row["ins_commercial"])){ $arr[$test]["COMMERCIAL"][$site][$var_id][] = $prac_cpt;}
		if(!empty($row["ins_medicare"])){ $arr[$test]["MEDICARE"][$site][$var_id][] = $prac_cpt;	}
		//$arr[$test][$inc_type][] = $prac_cpt;

	}
	return $arr;
}

function get_visitcode_versions($cpt){
	$arr=array();
	$cpt = trim($cpt);

	if(!empty($cpt) && strlen($cpt) == '5' && preg_match('/((992|920)(0|1)[1-5])/', $cpt)){
		//check practic code first
		$sql = "SELECT  cpt_fee_tbl.cpt_prac_code ".
			 "FROM cpt_fee_tbl
			 WHERE (	LOWER(REPLACE(cpt_fee_tbl.cpt4_code,'\r\n','')) = LOWER('".$cpt."')
					)
			 AND delete_status = '0' AND status='Active' ";
		$rez = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($rez);$i++){
			$tmp=trim($row["cpt_prac_code"]);
			if(!empty($tmp)){$arr[]=$tmp;}
		}

		if(count($arr) == 1){$arr=array();}

	}
	return $arr;
}

function find_cpt(){
	$tmp=sqlEscStr(urldecode($_POST["elem_desc"]));
	if(strpos($tmp,"~!~")!==false){
		$tmp=explode("~!~",$tmp);
		$tmp=$tmp[0];//cpt
	}

	$ar = array();
	$flg=0;
	//check practic code first
	$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code,cpt_fee_tbl.cpt4_code, ".
		 " cpt_fee_tbl.mod1, cpt_fee_tbl.mod2, cpt_fee_tbl.mod3, cpt_fee_tbl.mod4, cpt_fee_tbl.units, ".
		 " cpt_fee_tbl.dx_codes ".
		 "FROM cpt_fee_tbl
		 WHERE (	LOWER(REPLACE(cpt_fee_tbl.cpt_prac_code,'\r\n','')) = LOWER('".$tmp."')
				)
		 AND delete_status = '0' AND status='Active' ";
	$row = sqlQuery($sql);
	if($row != false && !empty($row["cpt_prac_code"])){
		$flg=1;
	}

	if($flg==0){ //if  not found , then check cpt4code or desc
		$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code,cpt_fee_tbl.cpt4_code, ".
			 " cpt_fee_tbl.mod1, cpt_fee_tbl.mod2, cpt_fee_tbl.mod3, cpt_fee_tbl.mod4, cpt_fee_tbl.units, ".
			 " cpt_fee_tbl.dx_codes ".
			 "FROM cpt_fee_tbl
			 WHERE (LOWER(REPLACE(cpt_fee_tbl.cpt_desc,'\r\n','')) = LOWER('".$tmp."') OR
					LOWER(REPLACE(cpt_fee_tbl.cpt_prac_code,'\r\n','')) = LOWER('".$tmp."') OR
					LOWER(REPLACE(cpt_fee_tbl.cpt4_code,'\r\n','')) = LOWER('".$tmp."') )
			 AND delete_status = '0' AND status='Active' ";
		$row = sqlQuery($sql);
	}

	if($row != false){
		$ar["code"] = $row["cpt_prac_code"];
		$ar["desc"] = $row["cpt_desc"];
		$ar["cpt4_code"] = $row["cpt4_code"];
		if(!empty($row["mod1"])) $ar["mod"][] = $row["mod1"];
		if(!empty($row["mod2"])) $ar["mod"][] = $row["mod2"];
		if(!empty($row["mod3"])) $ar["mod"][] = $row["mod3"];
		if(!empty($row["mod4"])) $ar["mod"][] = $row["mod4"];
		if(!empty($row["units"])) $ar["units"] = $row["units"];
		if(!empty($row["dx_codes"])) $ar["dx_codes"] = $row["dx_codes"];
		$ar["all_version"] = (!empty($_POST["check_versions"])) ? $this->get_visitcode_versions($ar["cpt4_code"]) : array();

	}
	echo json_encode($ar);
}

function getActiveCptCodesInfo($columnId=0){
	$arrDesc=array();
	$arrCptCodes = array();
	$arrProcedureFee = array();
	$arrCptModifiers = array();
	$arrCptCU = array();
	/*
	$sql = "SELECT
			cpt_fee_tbl.cpt_desc,
			cpt_fee_tbl.fee,
			cpt_fee_tbl.cpt4_code,
			cpt_fee_tbl.mod1,
			cpt_fee_tbl.mod2
			FROM cpt_category_tbl
			INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
			WHERE cpt_fee_tbl.commonlyUsed='1'
			AND cpt_fee_tbl.status='Active'
			ORDER BY cpt_category_tbl.cpt_category, cpt_fee_tbl.cpt_desc";
	*/
	$oMod = new Modifier();
	$columnId = ($columnId == 0) ? 1 : $columnId;
	$sql = "SELECT
		cpt_fee_tbl.cpt_desc,
		cpt_fee_tbl.cpt4_code,
		cpt_fee_tbl.cpt_prac_code,
		cpt_fee_tbl.mod1,
		cpt_fee_tbl.mod2,
		cpt_fee_tbl.commonlyUsed,
		cpt_fee_table.cpt_fee,
		cpt_fee_tbl.not_covered
		FROM cpt_category_tbl
		INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
		INNER JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id
		WHERE cpt_fee_tbl.status='Active' AND cpt_fee_tbl.delete_status!='1'
		AND cpt_fee_table.fee_table_column_id='".$columnId."'
		ORDER BY cpt_category_tbl.cpt_category, cpt_fee_tbl.cpt_desc";
	$rez = sqlStatement($sql);
	for($i=1;$row=sqlFetchArray($rez);$i++)
	{
		$desc = $row["cpt_desc"];
		$fee = $row["cpt_fee"]; //$row["fee"];
		$cptCode = $row["cpt_prac_code"];
		$commonlyUsed = $row["commonlyUsed"];
		$not_covered = $row["not_covered"];
		$modifier1 = (!empty($row["mod1"])) ? $oMod->getModifierCode($row["mod1"]) : "" ;
		$modifier2 = (!empty($row["mod2"])) ? $oMod->getModifierCode($row["mod2"]) : "" ;
		if(array_search($desc,$arrDesc) === false)
		{
			$arrDesc[] = $desc;
			$arrCptCodes[$desc] = $this->getCptCodesFromDesc_v2($desc);
		}
		$arrProcedureFee[$cptCode]=$fee;
		$arrCptModifiers[$cptCode][0]=$modifier1;
		$arrCptModifiers[$cptCode][1]=$modifier2;
		$arrCptCU[$cptCode]=$commonlyUsed;
		$arrCptNC[$cptCode]=$not_covered;
	}
	return array($arrDesc,$arrCptCodes,$arrProcedureFee,$arrCptModifiers,$arrCptCU,$arrCptNC);
}

	function getTestCptCodes(){
		$testname = $_GET["testname"];
		$eye = $_GET["eye"];
		$variation_id = isset($_GET["variation_id"]) ? (int)$_GET["variation_id"] : 0;

		$arrExamsDone = $this->checkTest4CptDesc($testname,$eye,$variation_id);

		//--

		//$str .="<allcptcode>";

			$arrSelectedProcs_2 = array();
			foreach($arrExamsDone as $var => $val)
			{
				$practice_code = $val;

				if(empty($practice_code)){ continue; }

				/*
				//$tmp = array_search($val,$arrCptCodes);
				$tmp = arr_search_2dim($val, $arrCptCodes);
				if($tmp !== false)
				{
					$val = $tmp;
				}*/

				//Check practice code --
					//pending..
					//Check Code
					$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
						   "FROM cpt_fee_tbl WHERE LCASE(cpt_fee_tbl.cpt_prac_code) = '".strtolower($practice_code)."'
						   AND status = 'Active'
						   AND delete_status = '0'  ";
					$row = sqlQuery($sql);
					if($row!=false){
						$desc =trim($row["cpt_desc"]);
					}

				//Check practice code --

				if(!empty($desc)){
				//if(array_search($val,$arrDesc) !== false)
				//{
					/*//check duplicate
					if(in_array($val, $arrSelectedProcs)){
						continue;
					}*/

					//$str .="<cptcodeinfo>";
					//$desc =
					$arrSelectedProcs[] = $val;
					//$val2var = str_replace(" ","_",$desc);
					//$tempCpt = (in_array($practice_code, $arrPracticCptCodes)) ? $practice_code : getProcedureCode($desc,$practice_code);
					$tempCpt = $practice_code ;

					if(!empty($tempCpt)){

						$arr_tmp=array();
						$arr_tmp["practice_code"]=$tempCpt;

						//DefMod+unit
						$arrCptDefVal = $this->getCptDefVals($tempCpt);

						//Test units
						$units = "1";
						if(!empty($arrCptDefVal["units"])) $units = $arrCptDefVal["units"];//Default set in admin
						$arr_tmp["units"]=$units;

						//Test units

						$arrSelectedCpt[] = $tempCpt;
						$tmp_desc_attr = (!empty($desc)) ? htmlentities($desc, ENT_QUOTES) : "";
						$arr_tmp["desc"]=$tmp_desc_attr;
						//$str .="<cptcode desc=\"".$tmp_desc_attr."\" >".$tempCpt."</cptcode>";
						//$str .="<units>".$units."</units>";

						//Modifiers
						$tmp_arr_mod=array();
						//Add default mods
						if(count($arrCptDefVal["mod"])>0){
							$tmp_arr_mod = array_merge($tmp_arr_mod,$arrCptDefVal["mod"]);
						}
						$arr_tmp["modifier"]=$tmp_arr_mod;

						/*
						$lnTam = count($tmp_arr_mod);
						if($lnTam > 0){
							for($iter=0;$iter<$lnTam&&$iter<3;$iter++){
								//$str .="<modifier".($iter+1).">".$tmp_arr_mod[$iter]."</modifier".($iter+1).">";
								$arr_tmp["modifier".($iter+1)]=$tmp_arr_mod[$iter];
							}
						}*/
						//Modifiers--
						/*
						//Dx
						$iDx = 1;
						if(array_key_exists($tempCpt,$arrDxExamsDone)||array_key_exists($desc,$arrDxExamsDone)){
							$tDxCode = $arrDxExamsDone[$tempCpt];
							if(empty($tDxCode)) $tDxCode = $arrDxExamsDone[$desc];
							if(!empty($tDxCode)){
								$tDxDesc = getDxTableInfo($tDxCode);
								$tDxDesc = ($tDxDesc !== false) ? htmlentities($tDxDesc, ENT_QUOTES) : "";
								$str .="<dx".$iDx." desc=\"".$tDxDesc."\">".$tDxCode."</dx".$iDx.">";
								$iDx++;
							}
						}

						//Dx 2 Pass
						if(isset($arrApCpt[$tempCpt])){
							$lenApCpt = count($arrApCpt[$tempCpt]);
							if($lenApCpt > 0){
								$iDx = 1;
								foreach($arrApCpt[$tempCpt] as $ki => $vi){
									$tDxCode = $vi;
									$tDxDesc = getDxTableInfo($tDxCode);
									$tDxDesc = ($tDxDesc !== false) ? htmlentities($tDxDesc, ENT_QUOTES) : "";
									$str .="<adx".$iDx." desc=\"".$tDxDesc."\">".$tDxCode."</adx".$iDx.">";
									$iDx++;
								}
							}
						}
						*/

						$arrSelectedProcs_2[] = $arr_tmp;

					}
					//$str .="</cptcodeinfo>";
				}
			}
			//Test

		//$str .="</allcptcode>";

		//--
		return $arrSelectedProcs_2;
		//echo json_encode($arrSelectedProcs_2);
	}

	//Check CPT cpde in prac_code and real code
	function mkCPTCode($str,$flgCode="Real"){
		//Check Code
		if($flgCode=="Prac"){
			$Where = "WHERE LCASE(cpt_fee_tbl.cpt_prac_code) = '".strtolower($str)."' ";
			$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code ".
				   "FROM cpt_fee_tbl ".
				   $Where.
				   "AND delete_status = '0'";
			$row = sqlQuery($sql);
			if($row==false){
				//check with cpt 4 code
				$Where = "WHERE LCASE(cpt_fee_tbl.cpt4_code) = '".strtolower($str)."' ";
				$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code ".
				   "FROM cpt_fee_tbl ".
				   $Where.
				   "AND delete_status = '0'";
				$row = sqlQuery($sql);
			}

			if($row!=false){
				$str = $row["cpt_prac_code"];
			}
			return $str;


		}else{
			$Where = "WHERE LCASE(cpt_fee_tbl.cpt_prac_code) = '".strtolower($str)."' ";

			$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code, cpt_fee_tbl.cpt4_code ".
				   "FROM cpt_fee_tbl ".
				   $Where.
				   "AND delete_status = '0'";
			$row = sqlQuery($sql);
			if($row!=false){
				$str = $row["cpt4_code"];
			}
			return $str;
		}
	}

	function getCommonlyUsedCPTCodes($columnId=0)
	{
		$arrDesc=array();
		$arrCptCodes = array();
		$arrProcedureFee = array();
		$arrCptModifiers = array();
		$oMod = new Modifier();
		/*
		$sql = "SELECT
				cpt_fee_tbl.cpt_desc,
				cpt_fee_tbl.fee,
				cpt_fee_tbl.cpt4_code,
				cpt_fee_tbl.mod1,
				cpt_fee_tbl.mod2
				FROM cpt_category_tbl
				INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
				WHERE cpt_fee_tbl.commonlyUsed='1'
				AND cpt_fee_tbl.status='Active'
				ORDER BY cpt_category_tbl.cpt_category, cpt_fee_tbl.cpt_desc";
		*/
		$columnId = ($columnId == 0) ? 1 : $columnId;
		$sql = "SELECT
			cpt_fee_tbl.cpt_desc,
			cpt_fee_tbl.cpt_prac_code,
			cpt_fee_tbl.mod1,
			cpt_fee_tbl.mod2,
			cpt_fee_table.cpt_fee
			FROM cpt_category_tbl
			INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
			INNER JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id
			WHERE ".
			//"cpt_fee_tbl.commonlyUsed='1' AND ".
			"cpt_fee_tbl.status='Active' AND cpt_fee_tbl.delete_status='0'
			AND cpt_fee_table.fee_table_column_id='".$columnId."'
			ORDER BY cpt_category_tbl.cpt_category, cpt_fee_tbl.cpt_desc";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++)
		{
			$desc = $row["cpt_desc"];
			$fee = $row["cpt_fee"]; //$row["fee"];
			$cptCode = $row["cpt_prac_code"];
			$modifier1 = (!empty($row["mod1"])) ? $oMod->getModifierCode($row["mod1"]) : "" ;
			$modifier2 = (!empty($row["mod2"])) ? $oMod->getModifierCode($row["mod2"]) : "" ;
			if(array_search($desc,$arrDesc) === false)
			{
				$arrDesc[] = $desc;
				$arrCptCodes[$desc] = $this->getCptCodesFromDesc($desc);
			}
			$arrProcedureFee[$cptCode]=$fee;
			$arrCptModifiers[$cptCode][0]=$modifier1;
			$arrCptModifiers[$cptCode][1]=$modifier2;
		}
		return array($arrDesc,$arrCptCodes,$arrProcedureFee,$arrCptModifiers);
	}

	function getCptCodesFromDesc($desc)
	{
		$arrCptCodes = array();
		$sql = "SELECT cpt_prac_code FROM cpt_fee_tbl WHERE cpt_desc='".sqlEscStr($desc)."' ".///*"and commonlyUsed='1'"*/
			 " and status = 'Active' AND delete_status = '0' ORDER BY cpt_desc";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			$arrCptCodes[] = $row["cpt_prac_code"];
		}
		return (count($arrCptCodes) > 1) ? $arrCptCodes : $arrCptCodes[0];
		//return $arrCptCodes;
	}

	function getCptCodesFromDesc_v2($desc)
	{
		$arrCptCodes = array();
		$sql = "SELECT cpt_prac_code,cpt4_code FROM cpt_fee_tbl WHERE cpt_desc='".sqlEscStr($desc)."' AND status = 'Active' AND delete_status = '0' ORDER BY cpt_desc";
		$rez = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++)
		{
			$arrCptCodes[] = $row["cpt_prac_code"];
			//$arrCptCodes[] = $row["cpt4_code"];
		}
		return (count($arrCptCodes) > 1) ? $arrCptCodes : $arrCptCodes[0];
		//return $arrCptCodes;
	}

	function set_flg_pt_ins($flg){
		$this->flgPtInsMedicare = $flg;
	}

	function isCptExist($cptChk){
		//Check Code
		$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
			   "FROM cpt_fee_tbl WHERE LCASE(cpt_fee_tbl.cpt_prac_code) = '".strtolower($cptChk)."' AND delete_status = '0'";
		return sqlQuery($sql);
	}

	function checkTest4CptDesc($str,$eye="",$variation_id=0){

		if(!isset($this->arr_SuperbillTestCpt)){

			//GET information w.r.t Tests CPTS--
			//$oAdmn = new Admn();
			$this->arr_SuperbillTestCpt=$this->getSuperTestCpt();

		}

		$arr_SuperbillTestCpt = $this->arr_SuperbillTestCpt;
		$flgPtInsMedicare = (!isset($this->flgPtInsMedicare)) ? 0 : $this->flgPtInsMedicare ;

		$str_Insurance = ($flgPtInsMedicare) ? "MEDICARE" : "COMMERCIAL";
		$str = strtoupper($str);
		$arrRet = array();

		if( isset($arr_SuperbillTestCpt) ){

			//Check Bileteral
			$arr  = $arr_SuperbillTestCpt[$str][$str_Insurance]["OU"][$variation_id];
			$len = count($arr);
			if($len>0){
				//
			}else if(!empty($eye)){
				//check Uni
				$arr=array();
				$len =0;
				if($eye=="OU"){
					$arr_od  = $arr_SuperbillTestCpt[$str][$str_Insurance]["OD"][$variation_id];
					$arr_os  = $arr_SuperbillTestCpt[$str][$str_Insurance]["OS"][$variation_id];

					if(is_array($arr_od) && count($arr_od)>0){
						$arr = array_merge($arr,$arr_od);
					}
					if(is_array($arr_os) && count($arr_os)>0){
						$arr = array_merge($arr,$arr_os);
					}

				}else{
					//get Eye
					$arr  = $arr_SuperbillTestCpt[$str][$str_Insurance][$eye][$variation_id];
				}

				//len
				if(is_array($arr) && count($arr)>0){
					$len = count($arr);
				}
			}

			for($i=0;$i<$len;$i++){
				$cptChk=$arr[$i];
				if($this->isCptExist($cptChk)){
					$arrRet[] = $cptChk;
				}
				/*
				if(isset($arrCptCodeAndDesc) && array_search($cptChk,$arrCptCodeAndDesc) !== false){
					$arrRet[] = $cptChk;
				}else{
					//Check Code
					$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
						   "FROM cpt_fee_tbl WHERE LCASE(cpt_fee_tbl.cpt_prac_code) = '".strtolower($cptChk)."' AND delete_status = '0'";
					$row = sqlQuery($sql);
					if($row!=false){
						$arrRet[] = $cptChk;
					}
				}
				*/
			}

		}

		return $arrRet;

		/*
		if(isset($GLOBALS['arrExamCpt'])){
			$cptChk = $GLOBALS['arrExamCpt'][$str];
			if(!empty($cptChk)){
				$cptChk =mkCPTCode($cptChk,"Prac");
				if(isset($arrCptCodeAndDesc) && array_search($cptChk,$arrCptCodeAndDesc) !== false){
					$str = $cptChk;
				}else{
					//Check Code
					$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
						   "FROM cpt_fee_tbl WHERE LCASE(cpt_fee_tbl.cpt_prac_code) = '".strtolower($cptChk)."' AND delete_status = '0'";
					$row = sqlQuery($sql);
					if($row!=false){
						$str = $cptChk;
					}
				}
			}
		}


		return $str;
		//*/
		/*
		if(array_search($val,$arrDesc) === false){

			$sql = "SELECT * FROM test_alias WHERE test_name='".addslashes($str)."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$alias = $row["alias"];

				$arrTmp = (!empty($alias)) ? explode(",", $alias) : array();
				if( count($arrTmp) > 0 ){
					foreach( $arrTmp as $key => $val ){
						if( !empty($val) && (array_search($val,$arrDesc) !== false) ){
							$str = $val;
						}
					}
				}
			}
		}
		return $str;
		*/
	}

	function checkTest4CptDesc_v2($str){
		$flag = false;
		$arcptCode = $this->checkTest4CptDesc($str);
		$str="";

		if(count($arcptCode)>0){
			$strCptCode = "'".implode("','", $arcptCode)."'";

			//Check Code
			$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
				   "FROM cpt_fee_tbl WHERE LCASE(cpt_fee_tbl.cpt_prac_code) IN (".strtolower($strCptCode).") ".
				   "AND delete_status = '0' ";
			$row = sqlQuery($sql);
			if($row != false && !empty($row["cpt_desc"])){
				$str = $row["cpt_desc"];
				$flag = true;
			}
			//Check String
			if($flag == false){
				$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
					   "FROM cpt_fee_tbl WHERE LCASE(cpt_fee_tbl.cpt_desc) IN (".strtolower($strCptCode).") ".
					   "AND delete_status = '0'";
				$row = sqlQuery($sql);
				if($row != false){
					$str = $row["cpt_desc"];
					$flag = true;
				}
			}

		}

		/*
		//Check in Alias
		if($flag == false){

			//Check Alias --
			$sql = "SELECT * FROM test_alias WHERE test_name='".addslashes($str)."' ";
			$row = sqlQuery($sql);
			if($row != false){
				$alias = $row["alias"];
				$arrTmp = (!empty($alias)) ? explode(",", $alias) : array();
				if( count($arrTmp) > 0 ){
					foreach( $arrTmp as $key => $val ){
						if(!empty($val)){
							//Check Again
							$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
								   "FROM cpt_fee_tbl WHERE LCASE(TRIM(cpt_fee_tbl.cpt_desc)) = '".strtolower($val)."'
									AND delete_status = '0'";
							$row = sqlQuery($sql);
							if($row != false){
								$str = $val;
								break;
							}
						}
					}
				}
			}
		}
		*/

		return $str;
	}

	function getCptDefVals($tempCpt,$sel="",$ret=0){
		if(empty($sel)){ $sel=" mod1,mod2,mod3,mod4,units,cpt_desc, dx_codes ";  }
		$sql = "SELECT ".$sel." FROM cpt_fee_tbl WHERE cpt_prac_code='".$tempCpt."' AND delete_status != '1' ";
		$row=sqlQuery($sql);
		if($row!=false){
			if(!empty($row["units"])) $arr["units"]=$row["units"];
			if(!empty($row["mod1"])) $arr["mod"][]=$row["mod1"];
			if(!empty($row["mod2"])) $arr["mod"][]=$row["mod2"];
			if(!empty($row["mod3"])) $arr["mod"][]=$row["mod3"];
			if(!empty($row["mod4"])) $arr["mod"][]=$row["mod4"];
			if(!empty($row["cpt_desc"])) $arr["cpt_desc"]=$row["cpt_desc"];
			if(!empty($row["dx_codes"])) $arr["dx_codes"]=$row["dx_codes"];

			if(!empty($ret)){
				if(!empty($row[$sel])){ return $row[$sel];  }
			}

		}else{ $arr=array(); }
		return $arr;
	}

	function getProcedureCode($desc)
	{
		global $arrDesc,$arrActiveCptCodes;
		$pCode = (is_array($arrActiveCptCodes[$desc])) ? $arrActiveCptCodes[$desc][0] : $arrActiveCptCodes[$desc];
		return $pCode;
	}

	function checkPqriCode($str){
		$ret = "";
		$sql = "SELECT cpt_fee_tbl.cpt_desc, cpt_fee_tbl.cpt_prac_code ".
			 "FROM cpt_fee_tbl ".
			 "INNER JOIN cpt_category_tbl ON cpt_category_tbl.cpt_cat_id = cpt_fee_tbl.cpt_cat_id ".
			 "WHERE cpt_category_tbl.cpt_category = 'PQRI' AND cpt_fee_tbl.delete_status = '0'".
			 "AND (cpt_fee_tbl.cpt4_code = '".$str."' || cpt_fee_tbl.cpt_prac_code='".$str."' || cpt_fee_tbl.cpt_desc='".$str."')  ";

		$row = sqlQuery($sql);
		if($row != false){
			$ret = $row["cpt_prac_code"];
		}
		return $ret;
	}

	function sb_getVisitCodeCPTCost($patientId){
		$insCaseId = xss_rem($_GET["elem_insCaseId"]);
		$strDos = xss_rem($_GET["elem_dos"]);


		//Insurance case
		if(!isset($insCaseId) || empty($insCaseId))
		{
			$opt = new Patient($patientId);
			$insCaseId = $opt->getInuranceCaseId();
		}

		// Fee Column
		$insFeeColumn = "1";
		if(isset($insCaseId) && !empty($insCaseId)){
			$oIns = new Insurance();
			$insFeeColumn_t = $oIns->getInsFeeColumn($insCaseId, $strDos, 1);
			if($insFeeColumn_t > 1 && !empty($insFeeColumn_t)){  $insFeeColumn .= ",".$insFeeColumn_t; }
		}

		$strCptCode=$lb_qlfy_lvl_code920="";

		/*
		if(!empty($_GET["lb_qlfy_lvl_code920"])) { $lb_qlfy_lvl_code920=str_replace(array("Comprehensive - ", "Intermediate - "),"",$_GET["lb_qlfy_lvl_code920"]); $strCptCode.="".$lb_qlfy_lvl_code920.","; }
		if(!empty($_GET["lb_qlfy_lvl_code992"])) { $strCptCode.="".$_GET["lb_qlfy_lvl_code992"].","; }
		if(!empty($_GET["lb_nxt_lvl_code920"])) { $strCptCode.="".$_GET["lb_nxt_lvl_code920"].","; }
		if(!empty($_GET["lb_nxt_lvl_code992"])) { $strCptCode.="".$_GET["lb_nxt_lvl_code992"].","; }
		*/
		$c=0;
		$arrCd_Fld=array();
		while(true){
			$tmp_xss_992 =  xss_rem($_GET["lb_qlfy_lvl_code992"]);
			$tmp_xss_920 =  xss_rem($_GET["lb_qlfy_lvl_code920"]);
			if(isset($tmp_xss_992)||isset($tmp_xss_920)){
				if(!empty($tmp_xss_920)){
					$lb_qlfy_lvl_code920=str_replace(array("Comprehensive - ", "Intermediate - "),"",$tmp_xss_920);
					$strCptCode.="".$lb_qlfy_lvl_code920.",";
					$arrCd_Fld[$lb_qlfy_lvl_code920] = "lb_qlfy_lvl_code920";
				}
				if(!empty($_GET["lb_qlfy_lvl_code992"])){
					$strCptCode.="".$_GET["lb_qlfy_lvl_code992"].",";
					$arrCd_Fld[$_GET["lb_qlfy_lvl_code992"]] = "lb_qlfy_lvl_code992";
				}
			}else{
				break;
			}
			if($c>10){break;}
			$c++;
		}
		$strCptCode = substr($strCptCode,0,-1);

		// Get Fee Rates
		$arrCptTmp = array();
		$cptCharges = 0;
		$arrCptFee = array();
		$sql = "SELECT
			cpt_fee_tbl.cpt_prac_code,
			cpt_fee_tbl.cpt4_code,
			cpt_fee_tbl.cpt_desc,
			cpt_fee_tbl.mod1,
			cpt_fee_tbl.mod2,
			cpt_fee_tbl.commonlyUsed,
			cpt_fee_table.cpt_fee,
			cpt_fee_table.fee_table_column_id
			FROM cpt_category_tbl
			INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
			INNER JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id
			WHERE cpt_fee_tbl.status='Active' AND cpt_fee_tbl.delete_status='0'
			AND cpt_fee_table.fee_table_column_id IN (".$insFeeColumn.")
			AND UCASE(cpt_fee_tbl.cpt_prac_code) IN (".$strCptCode.")
			ORDER BY cpt_category_tbl.cpt_category, cpt_fee_tbl.cpt_desc";
		//echo $sql;exit();
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++)
		{
			if(!empty($row["cpt_prac_code"]) && (!empty($row["cpt_fee"])) ){ //&& (!in_array($row["cpt_prac_code"], $arrCptTmp))
				$tmp = strtoupper($row["cpt_prac_code"]);
				$tmp_2 = ($row["fee_table_column_id"]=="1") ? "def" : "ins";
				$cpt4_code = strtoupper($row["cpt4_code"]);
				//$arrCptTmp[] = $row["cpt_prac_code"];

				$tmp_fld =  $arrCd_Fld[$cpt4_code];

				$arrCptFee [$tmp_fld][$tmp_2]=number_format($row["cpt_fee"],2);

				/*
				if(!empty($lb_qlfy_lvl_code920) && (strtoupper($lb_qlfy_lvl_code920) == $tmp || strtoupper($lb_qlfy_lvl_code920) == $cpt4_code)) {
					$arrCptFee ["lb_qlfy_lvl_code920_val"][$tmp_2]=number_format($row["cpt_fee"],2);
				}else if(!empty($_GET["lb_qlfy_lvl_code992"]) && (strtoupper($_GET["lb_qlfy_lvl_code992"]) == $tmp)) {
					$arrCptFee ["lb_qlfy_lvl_code992_val"][$tmp_2]=number_format($row["cpt_fee"],2);
				}else if(!empty($_GET["lb_nxt_lvl_code920"]) && (strtoupper($_GET["lb_nxt_lvl_code920"]) == $tmp)) {
					$arrCptFee ["lb_nxt_lvl_code920_val"][$tmp_2]=number_format($row["cpt_fee"],2);
				}else if(!empty($_GET["lb_nxt_lvl_code992"]) && (strtoupper($_GET["lb_nxt_lvl_code992"]) == $tmp)) {
					$arrCptFee ["lb_nxt_lvl_code992_val"][$tmp_2]=number_format($row["cpt_fee"],2);
				}
				*/
			}
		}

		//print_r($arrCptFee);
		//exit();

		echo json_encode($arrCptFee);
	}

function getOtherProceduresFee($columnId=0)
{
	$arrProcedureFee = array();
	$columnId = ($columnId == 0) ? 1 : $columnId;
	$sql = "SELECT
		cpt_fee_tbl.cpt_prac_code,
		cpt_fee_table.cpt_fee
		FROM cpt_category_tbl
		INNER JOIN cpt_fee_tbl ON cpt_fee_tbl.cpt_cat_id = cpt_category_tbl.cpt_cat_id
		INNER JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id
		WHERE cpt_fee_table.fee_table_column_id = '".$columnId."'
		AND
		 (".///*"cpt_fee_tbl.commonlyUsed != '1' OR"*/.
		 " cpt_fee_tbl.status != 'Active' AND cpt_fee_tbl.delete_status='0')
		ORDER BY cpt_category_tbl.cpt_category, cpt_fee_tbl.cpt_desc";
	$rez = sqlStatement($sql);
	for($i=1;$row=sqlFetchArray($rez);$i++)
	{
		$fee = $row["cpt_fee"];
		$cptCode = $row["cpt_prac_code"];
		$arrProcedureFee[$cptCode]=$fee;
	}
	return $arrProcedureFee;
}

function getCptFee($cpt_prc_code, $fee_column){
	$cptFee=0;
	$cptDesc="";
	$procedurePracCode = $cpt_prc_code;
	$insFeeColumn = $fee_column;
	$getProcedureDetailsStr="SELECT * ".
					   "FROM cpt_fee_tbl ".
					   "LEFT JOIN cpt_fee_table ON cpt_fee_table.cpt_fee_id = cpt_fee_tbl.cpt_fee_id ".
					   "WHERE (cpt_fee_tbl.cpt4_code='$procedurePracCode' || cpt_fee_tbl.cpt_prac_code='$procedurePracCode') AND cpt_fee_tbl.delete_status = '0' AND cpt_fee_tbl.status='Active' ".
					   "AND cpt_fee_table.fee_table_column_id='".$insFeeColumn."'";

	$getProcedureDetailsRow=sqlQuery($getProcedureDetailsStr);
	if($getProcedureDetailsRow!=false){
	$cptFee=$getProcedureDetailsRow['cpt_fee'];	// Fee
	$cptDesc=$getProcedureDetailsRow['cpt_desc'];
	}
	return array($cptFee, $cptDesc);
}

function get_cpt_code_arr($find=""){
	$str_get="";$str_find="";
	if(!empty($find)){
		$rows_cpt = "";
		//$str_find = " AND (cpt_prac_code='".$find."' OR  cpt4_code='".$find."') ";
		$str_get = ", cpt_desc ";
		$prac_code_qry = "select cpt_prac_code ".$str_get." from cpt_fee_tbl where cpt_prac_code != '' and delete_status = '0' AND cpt_prac_code='".$find."' group by cpt_prac_code order by cpt_prac_code";
		$row = sqlQuery($prac_code_qry);
		if($row==false){
			$prac_code_qry = "select cpt_prac_code ".$str_get." from cpt_fee_tbl where cpt_prac_code != '' and delete_status = '0' AND cpt4_code='".$find."' group by cpt_prac_code order by cpt_prac_code";
			$row = sqlQuery($prac_code_qry);
		}
		if($row!=false){
			if(!empty($row['cpt_desc'])){	$rows_cpt = $row['cpt_desc'];}
		}
		return $rows_cpt;
	}else{
		$rows_cpt = array();
		$prac_code_qry = "select cpt_prac_code ".$str_get." from cpt_fee_tbl where cpt_prac_code != '' and delete_status = '0' ".$str_find." group by cpt_prac_code order by cpt_prac_code";
		$prac_code_qry_obj = imw_query($prac_code_qry);
		$rows_cpt=!empty($find) ? "" : array();
		if(imw_num_rows($prac_code_qry_obj)>0){
			while($rs_cpt=imw_fetch_assoc($prac_code_qry_obj)){
				$rows_cpt[]=$rs_cpt['cpt_prac_code'];
			}
		}
		return $rows_cpt;
	}
}

function getValidDxAses(){
	$ret=array();
	$i=1;
	while(isset($_GET["elem_cptCode_".$i])){
		$cpt = $_GET["elem_cptCode_".$i];
		if(!empty($cpt)){
			$tmp = $this->getCptDefVals($cpt, "dx_codes" );
			$ret["elem_cptCode_".$i]=$tmp["dx_codes"];
		}
		if($i>50){	break; }
		$i++;
	}
	echo json_encode($ret);
}

function get_menu_html(){
	$str_cat ="";
	$sql = " SELECT * FROM cpt_category_tbl ORDER BY cpt_category ";
	$rez1 = sqlStatement($sql);
	for($i=1; $row1=sqlFetchArray($rez1);$i++){

		$catid=$row1["cpt_cat_id"];
		$cpt_category=$row1["cpt_category"];

		$str_cpt = "";
		$sql = " SELECT cpt_prac_code, cpt_desc FROM cpt_fee_tbl WHERE status='Active' AND delete_status!='1' AND cpt_cat_id = '".$catid."' ";
		$rez = sqlStatement($sql);
		for($j=1; $row=sqlFetchArray($rez);$j++){
			if(!empty($row["cpt_prac_code"]) && !empty($row["cpt_desc"])){$str_cpt .= "<li><a href=\"#\" data-val=\"".$row["cpt_prac_code"]."\">".$row["cpt_prac_code"]." - ".$row["cpt_desc"]."</a></li>";}
		}

		if(!empty($str_cpt)){
			$str_cpt = "<ul class=\"dropdown-menu\">".$str_cpt."</ul>";
			$str_cat .= "<li><a tabindex=\"-1\" href=\"#\" class=\"cat\">".$cpt_category." <span class=\"caret\"></span></a>".$str_cpt."</li>";
		}
	}

	if(!empty($str_cat)){
		$str_cat = "<ul class=\"dropdown-menu menu_cpt_ul \">".$str_cat."</ul>";
	}

	echo $str_cat;
}

}

?>
