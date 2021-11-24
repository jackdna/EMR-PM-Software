<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: User.php
Coded in PHP7
Purpose: This class file provides functions to manage User/Providers of chart notes.
Access Type : Include file
*/
?>
<?php
//User
class UserAp extends User{
	private $provider_id, $followedbyresi;
	private $arrConsole;
	public function __construct($id=""){
		parent::__construct($id);

		$this->followedbyresi = 0;
		$res_fellow_sess = trim($_SESSION['res_fellow_sess']);
		if(!empty($res_fellow_sess) && isset($res_fellow_sess))
		{
			$this->provider_id = $res_fellow_sess;
			$this->followedbyresi = ($_SESSION['logged_user_type'] == "11") ? $this->uid : 0;
		}
		else
		{
			$this->provider_id = $this->uid;

			//Check if providerId is tech or scribe: if follow to physician, then apply his assessments and policy
			$isTech = $this->getFollowPhyId4Tech();
			if(!empty($isTech)){
				$this->provider_id = $isTech; //$providerId
			}
		}

		//
		$this->arrConsole = array();
	}

	function setApPolFU(){
		$arrFU=array();
		$symp = trim($_POST["symp"]);
		$as = trim($_POST["as"]);
		$pln = trim($_POST["pln"]);
		$idOdr = trim($_POST["idOdr"]);
		$a_n = trim($_POST["a_n"]);
		$idOdrset = trim($_POST["idOdrset"]);
		$site = trim($_POST["site"]);
		$sig = trim($_POST["sig"]);
		$fInsrtOrdr = trim($_POST["fInsrtOrdr"]);

		if($symp=="to_do_id"){
			if(!empty($as)){$phrase_symp1 = " AND (to_do_id='".$as."') "; }
			$symp=$as="";
		}

		if(!empty($symp)){
			$phrase_symp = " AND (task!='' AND POSITION(UCASE('".sqlEscStr($symp)."') IN UCASE(task))>0) ";
			$phrase_symp1 = " AND (task!='' AND UCASE(task)=UCASE('".sqlEscStr($symp)."')) ";
		}

		$phrase_as="";
		if(!empty($as)){
			$as=remSiteDxFromAssessment($as);
			$phrase_as = " (assessment != '' AND POSITION(UCASE(assessment) IN UCASE('".sqlEscStr($as)."'))>0) ";
			$phrase_as1 = " (assessment != '' AND UCASE(assessment)=UCASE('".sqlEscStr($as)."')) ";
		}

		if(!empty($pln)){
			//$phrase_pln = " AND POSITION(UCASE('".sqlEscStr($pln)."') IN UCASE(plan))>0 ";
			if(!empty($phrase_as)){ $phrase_as .= " OR "; $phrase_as1 .= " OR "; }
			$phrase_as .= " (plan!='' AND POSITION(UCASE('".sqlEscStr($pln)."') IN UCASE(plan))>0) ";
			$phrase_as1 .= " (plan!='' AND UCASE(plan)=UCASE('".sqlEscStr($pln)."')) ";
		}
		//
		if(!empty($phrase_as)){
			$phrase_as =" AND (".$phrase_as.") ";
			$phrase_as1 =" AND (".$phrase_as1.") ";
		}

		if(!empty($phrase_symp)||!empty($phrase_as)||!empty($phrase_pln)||!empty($phrase_symp1)||!empty($phrase_as1)){

			$tmp_providerId = $this->provider_id;
			//$str_pro_phrase = !empty($this->followedbyresi) ? " OR providerID='".$this->followedbyresi."' " : "";
			$str_pro_phrase = "";

			$sql = "SELECT * FROM console_to_do
					WHERE (providerID='".$tmp_providerId."' OR providerID='0' ".$str_pro_phrase.")
					 ".$phrase_symp1." ".$phrase_as1." ".$phrase_pln1." ".
					 " AND dynamic_ap!='1' ".
					 "ORDER BY providerID DESC, task, assessment ";
			$row=sqlQuery($sql);
			if($row==false){
				if(!empty($phrase_symp)||!empty($phrase_as)||!empty($phrase_pln)){
				$sql = "SELECT * FROM console_to_do
					WHERE (providerID='".$tmp_providerId."' OR providerID='0' ".$str_pro_phrase.")
					 ".$phrase_symp." ".$phrase_as." ".$phrase_pln." ".
					 " AND dynamic_ap!='1' ".
					 "ORDER BY providerID DESC, task, assessment ";
				$row=sqlQuery($sql);
				}
			}
			if($row!=false){
				$strFU = $row["xmlFU"];
				if(!empty($strFU)){
					$oFu = new Fu();
					list($len,$arrFU) = $oFu->fu_getXmlValsArr($strFU, $GLOBALS["alwaysDocFU"]);
				}
			}
		}

		echo json_encode($arrFU);
	}

	function getAssessmentAndPolicies($flgmode="", $srch="", $srch2="", $chkDynamic=""){
		$providerId = $this->provider_id;
		//$str_pro_phrase = !empty($this->followedbyresi) ? " || providerID='".$this->followedbyresi."' " : "";
		$str_pro_phrase = "";
		//
		/*
		$phraseAs_dyna = "";
		if(!empty($chkDynamic)){
			$strAPSettings = getAPPolicySettings();
			if(empty($strAPSettings) || strpos($strAPSettings,"Dynamic")===false){
				$phraseAs_dyna = " AND dynamic_ap!='1' ";
			}
		}
		*/
		$phraseAs_dyna = " AND dynamic_ap!='1' ";

		//ifassessment search
		if(($flgmode=="Assessment" || $flgmode=="Assessment2" ) && !empty($srch)){
			if(!empty($srch2)){ $lastTermPhrase = " OR assessment like '".sqlEscStr($srch2)."%' ";   }else{ $lastTermPhrase=""; }
			$phraseAs = "AND (assessment LIKE '".sqlEscStr($srch)."%' ".$lastTermPhrase." )";
		}else{
			$phraseAs = "";
		}

		/*
		Please, please ensure that Physician personal A&P takes precedence over the community for matching Assessment or Findings.
		*/
		$sql = "select task,assessment,plan,dxcode from console_to_do
				where to_do != 'yes' and (providerID ='".$providerId."' || providerID=0 ".$str_pro_phrase.") ".
				$phraseAs." ".$phraseAs_dyna.
				"order by providerID DESC, assessment, task DESC  ";
		if($flgmode=="Assessment2"){
			$sql .= "Limit 0, 1 ";
		}

		$res = sqlStatement($sql);
		$strPlan=$strAssess=$strDxCods="";
		$arrAs = array();
		for( $i=0;$row=sqlFetchArray($res);$i++ ){
			if(!empty($row["assessment"])){

				$temp = (!empty($row["assessment"])) ? jsEscape(trim($row["assessment"])) : "" ;
				$temp .= (!empty($row["dxcode"])) ? " \t\r(".$row["dxcode"].")" : "" ;

				//check if assessment already exists: if yes Do not add again.
				if(strpos($strAssess,$temp)!==false){
					continue;
				}

				if(!empty($temp)){
					if($flgmode=="Assessment2"){
						$strAssess .=  "".$temp.", " ;
					}else{
						$strAssess .=  "'".$temp."', " ;
					}
					$arrAs[] = $temp;
				}
				$tmpPlan =  (!empty($row["plan"])) ? jsEscape(trim($row["plan"])) : "" ;  //str_replace(array("\r","\n","'"),array("\\r","\\n","\'"),$row["plan"]);
				if($flgmode=="Assessment2"){
					$strPlan .= "".$tmpPlan.", " ;
				}else{
					$strPlan .= "'".$tmpPlan."', " ;
				}


				//js array of sym, assess, plan should have equal length;
				//if(!empty($row["task"])){ //Do Not Put a check for Empty task string
					/*
					$strTask = "";
					$tmpTask = explode(",",$row["task"]);
					if(count($tmpTask) > 0){
						foreach($tmpTask as $key => $val){
							$strTask = "'".addslashes(trim($val))."', ";
						}
					}else{
						$strTask = "'".addslashes(trim($row["task"]))."', ";
					}
					*/
					if($flgmode=="Assessment2"){
						$strTask = "".jsEscape(trim($row["task"])).", ";
						$strSymp .= "".$strTask."" ;
					}else{
						$strTask = "'".jsEscape(trim($row["task"]))."', ";
						$strSymp .= "".$strTask."" ; //"'".$row["task"]."', " ;
					}
				//}

				$strDxCods .= "'".$row["dxcode"]."'~!!~" ;

			}
		}

		$strAssess = substr($strAssess,0,-2);
		$strPlan = substr($strPlan,0,-2);
		$strSymp = substr($strSymp,0,-2);
		$strDxCods = substr($strDxCods,0,-4);

		if($flgmode=="Assessment"){
			return $arrAs;
		}else{
			return array($strAssess,$strPlan,$strSymp,$strDxCods);
		}
	}

function getAssessmentAndPolicies_physician($flgmode="", $srch="", $srch2="",$ICD_type='0', $nodx='0',$dos=''){
	$providerId = $this->provider_id;
	//$str_pro_phrase = !empty($this->followedbyresi) ? " OR providerID='".$this->followedbyresi."' " : "";
	$str_pro_phrase = "";


	//ifassessment search
	if($flgmode=="Assessment" && !empty($srch)){
		if(!empty($srch2)){ $lastTermPhrase = " OR assessment like '".$srch2."%' ";   }else{ $lastTermPhrase=""; }
		$phraseAs = "AND (assessment LIKE '".$srch."%' ".$lastTermPhrase." )";

		if($ICD_type==1){
			//$phraseAs .= " AND (dxcode_10 !='' OR ICD_type ='".$ICD_type."') ";
		}else{
			$phraseAs .= " AND ICD_type ='".$ICD_type."'";
		}

	}else{
		$phraseAs = "";
	}
	$oDx = new Dx();

	/*
	Please, please ensure that Physician personal A&P takes precedence over the community for matching Assessment or Findings.
	*/
	$sql = "select task,assessment,plan,dxcode,dxcode_10 from console_to_do
			where to_do != 'yes' and (providerID ='".$providerId."' ".$str_pro_phrase.") AND dynamic_ap=0 ".
			$phraseAs.
			"order by providerID DESC, assessment, task DESC  ";
	$res = sqlStatement($sql);
	$strPlan=$strAssess=$strDxCods="";
	$arrAs = array();
	for( $i=0;$row=sqlFetchArray($res);$i++ ){
		if(!empty($row["assessment"])){

			$temp = (!empty($row["assessment"])) ? jsEscape(trim($row["assessment"])) : "" ;

			if($nodx!="1"){
			if($ICD_type==1){
				$tdx = trim($row["dxcode_10"]);
				if(!empty($tdx)){
					list($tdx, $tdxId) = $oDx->refineDx($tdx);
					if(!empty($tdx)){
						$temp .= " \t\r(".$tdx.")" ;
						//check if Dx belongs to dos: if not continue;
						if(!Dx::isDxCodeBelongsToDos($dos, $tdx, $tdxId)){
							continue;
						}
					}
				}

			}else{
				$temp .= (!empty($row["dxcode"])) ? " \t\r(".$row["dxcode"].")" : "" ;
			}
			}
			//check if assessment already exists: if yes Do not add again.
			if(strpos($strAssess,$temp)!==false){
				continue;
			}

			if(!empty($temp)){
				$strAssess .=  "'".$temp."', " ;
				//$arrAs[] = $temp;
				$arrAs[] = array("label"=>$temp, "value"=>$temp, "dxid"=>$tdxId);
			}
			$tmpPlan =  (!empty($row["plan"])) ? jsEscape(trim($row["plan"])) : "" ;  //str_replace(array("\r","\n","'"),array("\\r","\\n","\'"),$row["plan"]);
			$strPlan .= "'".$tmpPlan."', " ;



			//js array of sym, assess, plan should have equal length;
			//if(!empty($row["task"])){ //Do Not Put a check for Empty task string
				/*
				$strTask = "";
				$tmpTask = explode(",",$row["task"]);
				if(count($tmpTask) > 0){
					foreach($tmpTask as $key => $val){
						$strTask = "'".addslashes(trim($val))."', ";
					}
				}else{
					$strTask = "'".addslashes(trim($row["task"]))."', ";
				}
				*/
				$strTask = "'".jsEscape(trim($row["task"]))."', ";
				$strSymp .= "".$strTask."" ; //"'".$row["task"]."', " ;
			//}

			if($ICD_type==1){
				$strDxCods .= "'".$row["dxcode_10"]."'~!!~" ;
			}else{
				$strDxCods .= "'".$row["dxcode"]."'~!!~" ;
			}
		}
	}

	$strAssess = substr($strAssess,0,-2);
	$strPlan = substr($strPlan,0,-2);
	$strSymp = substr($strSymp,0,-2);
	$strDxCods = substr($strDxCods,0,-4);

	if($flgmode=="Assessment"){
		return $arrAs;
	}else{
		return array($strAssess,$strPlan,$strSymp,$strDxCods);
	}
}

function getAssessmentAndPolicies_community($flgmode="", $srch="", $srch2="",$ICD_type=0, $flgDynamicAP="", $nodx='0', $dos=''){
	$providerId = $this->provider_id;
	//$str_pro_phrase = !empty($this->followedbyresi) ? " OR providerID='".$this->followedbyresi."' " : "";
	$str_pro_phrase = "";

	//ifassessment search
	if($flgmode=="Assessment" && !empty($srch)){
		if(!empty($srch2)){ $lastTermPhrase = " OR assessment like '".$srch2."%' ";   }else{ $lastTermPhrase=""; }
		//if(empty($flgDynamicAP)){  $phraseDynaAP=" AND dynamic_ap='0' "; }else{ $phraseDynaAP=""; }
		$phraseDynaAP=" AND dynamic_ap='0' ";

		$phraseAs = "AND (assessment LIKE '".$srch."%' ".$lastTermPhrase." )";

		/* query optimisation:: THIS should be part of processing for query optimisation. */
		//$phraseAs .= " AND assessment NOT IN (SELECT assessment FROM console_to_do WHERE providerID = '".$providerId."' ".$phraseDynaAP." )";
		$ar_dyna_ap = array();
		$sql = "SELECT assessment FROM console_to_do WHERE (providerID = '".$providerId."' ".$str_pro_phrase.") ".$phraseDynaAP;
		$rez = sqlStatement($sql);
		for($i=0; $row=sqlFetchArray($rez);$i++){
			$ar_dyna_ap[] = trim($row["assessment"]);
		}
		//--

		if($ICD_type==1){
			//$phraseAs .= " AND (dxcode_10 !='' OR ICD_type ='".$ICD_type."') ";
		}else{
			$phraseAs .= " AND ICD_type ='".$ICD_type."'";
		}

	}else{
		$phraseAs = "";
	}

	$oDx = new Dx();
	/*
	Please, please ensure that Physician personal A&P takes precedence over the community for matching Assessment or Findings.
	*/
	$sql = "select task,assessment,plan,dxcode,dxcode_10 from console_to_do
			where to_do != 'yes' and (providerID=0) ".
			$phraseAs.
			"order by providerID DESC, assessment, task DESC  ";
	$res = sqlStatement($sql);
	$strPlan=$strAssess=$strDxCods="";
	$arrAs = array();
	for( $i=0;$row=sqlFetchArray($res);$i++ ){
		if(!empty($row["assessment"])){

			//query optimisation --
			if($flgmode=="Assessment" && !empty($srch)){
				if(isset($ar_dyna_ap) && count($ar_dyna_ap)>0 && in_array($row["assessment"], $ar_dyna_ap)){	continue;	}
			}
			//query optimisation --

			$temp = (!empty($row["assessment"])) ? jsEscape(trim($row["assessment"])) : "" ;
			$tdxId="";
			if($nodx!="1"){
			if($ICD_type==1){
				$tdx = trim($row["dxcode_10"]);
				if(!empty($tdx)){
					list($tdx, $tdxId) = $oDx->refineDx($tdx);
					if(!empty($tdx)){
						$temp .= " \t\r(".$tdx.")" ;
						//check if Dx belongs to dos: if not continue;
						if(!Dx::isDxCodeBelongsToDos($dos, $tdx, $tdxId)){	continue;		}
					}
				}

			}else{
				$temp .= (!empty($row["dxcode"])) ? " \t\r(".$row["dxcode"].")" : "" ;
			}
			}

			//check if assessment already exists: if yes Do not add again.
			if(strpos($strAssess,$temp)!==false){
				continue;
			}

			if(!empty($temp)){
				$strAssess .=  "'".$temp."', " ;
				//$arrAs[] = $temp;
				$arrAs[] = array("label"=>$temp, "value"=>$temp, "dxid"=>$tdxId);
			}
			$tmpPlan =  (!empty($row["plan"])) ? jsEscape(trim($row["plan"])) : "" ;  //str_replace(array("\r","\n","'"),array("\\r","\\n","\'"),$row["plan"]);
			$strPlan .= "'".$tmpPlan."', " ;



			//js array of sym, assess, plan should have equal length;
			//if(!empty($row["task"])){ //Do Not Put a check for Empty task string
				/*
				$strTask = "";
				$tmpTask = explode(",",$row["task"]);
				if(count($tmpTask) > 0){
					foreach($tmpTask as $key => $val){
						$strTask = "'".addslashes(trim($val))."', ";
					}
				}else{
					$strTask = "'".addslashes(trim($row["task"]))."', ";
				}
				*/
				$strTask = "'".jsEscape(trim($row["task"]))."', ";
				$strSymp .= "".$strTask."" ; //"'".$row["task"]."', " ;
			//}

			if($ICD_type==1){
				$strDxCods .= "'".$row["dxcode_10"]."'~!!~" ;
			}else{
				$strDxCods .= "'".$row["dxcode"]."'~!!~" ;
			}

		}
	}

	$strAssess = substr($strAssess,0,-2);
	$strPlan = substr($strPlan,0,-2);
	$strSymp = substr($strSymp,0,-2);
	$strDxCods = substr($strDxCods,0,-4);

	if($flgmode=="Assessment"){
		return $arrAs;
	}else{
		return array($strAssess,$strPlan,$strSymp,$strDxCods);
	}
}

function console_get_smart_phrases($term, $lastTerm, $exmnm, $providerId){
	$ret_tmp=array();
	$followed_providerId = $this->provider_id;
	if(!empty($followed_providerId) && $followed_providerId!=$providerId){ $str_follow_proid=" || providerID = '".$followed_providerId."' "; }else{ $str_follow_proid=""; }

	$phrse_search = " phrase LIKE '".sqlEscStr($term)."%' ";

	$ar_term = explode(" ", $term);
	$lno = count($ar_term);
	$ln = ($lno>15) ? 15 : $lno;
	for($i=1;$i<=$ln;$i++){
		$tmp = array_slice($ar_term, $lno-$i);
		$str_new = implode(" ",$tmp);
		if(!empty($str_new)){ $phrse_search .= " OR phrase like '".sqlEscStr($str_new)."%' ";   }
	}

	$ret=array();
	if(!empty($lastTerm)){ $lastTermPhrase = " phrase like '".sqlEscStr($lastTerm)."%' ";   }else{ $lastTermPhrase=""; }
	if(!empty($exmnm)){ $exmnmPhrase = " AND (exam like '%".$exmnm."%' OR exam='') ";  }else{  $exmnmPhrase = " AND (exam='') "; }

	$arr_search = array($phrse_search, $lastTermPhrase);
	foreach($arr_search as $k => $val_srch){
		if(!empty($val_srch)){
			$sql = "SELECT distinct(phrase) FROM common_phrases ".
				   "WHERE ( ".$val_srch." ) AND (providerID = '".$providerId."' || providerID = 0 ".$str_follow_proid." ) ".
				   $exmnmPhrase.
				   "ORDER BY providerID DESC, phrase ASC ";

			$rez = sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++)
			{
				$tmp = $row["phrase"];
				$tmp = filter_var($tmp, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
				$tmp = str_replace(array("&shy;","&#39;"),array("","'"),$tmp);
				$tmp = html_entity_decode($tmp);
				$tmp = stripslashes("".str_replace(array("'","\n","\r"),array("\'","\\\n","\\\r"),$tmp)."");
				$ret_tmp[] = $tmp;
			}
		}
	}

	$ret_tmp = array_unique($ret_tmp);
	return $ret_tmp;
}

function getDxAP(){
	$strXml = "";
	$tDxCode = xss_rem($_POST["elem_dxCode"]);
	$tmp_proid = $this->provider_id;
	//$str_pro_phrase = !empty($this->followedbyresi) ? " OR providerID='".$this->followedbyresi."' " : "";
	$str_pro_phrase = "";

	if(!empty($tDxCode) && !empty($tmp_proid)){
		//$tDxDesc = getDxTableInfo($tDxCode);

		//get dynamic AP Setting--
		/*
		$strAPSettings = getAPPolicySettings();
		if(empty($strAPSettings) || strpos($strAPSettings,"Dynamic")===false){
			$sqlPhraseAPSettings = "AND dynamic_ap != '1' ";
		}else{
			$sqlPhraseAPSettings ="";
		}
		*/
		$sqlPhraseAPSettings = "AND dynamic_ap != '1' ";
		//End get dynamic AP Setting--

		//GET Assess and plan based on Dx Code.
		$sql = "SELECT
				assessment, plan, dxcode,order_set_name
				FROM console_to_do ".
			   "WHERE (dxcode LIKE '%".$tDxCode."%' OR dxcode_10 LIKE '%".$tDxCode."%') ".
			   "AND (providerID='".$tmp_proid."' OR providerID=0 ".$str_pro_phrase.") ".
			   $sqlPhraseAPSettings.
			   "ORDER BY providerID DESC, to_do, date_time3 ";
		//echo 	 $sql  ;
		$rez = sqlStatement($sql);
		$orderSetIdArr = array();
		for($i=1;$row=sqlFetchArray($rez);$i++){
			if(!empty($row["assessment"])){
				$orderSetIdArr[] = $row["order_set_name"];
				$assess = $row["assessment"];
				$plan=$row["plan"];
				$dxcode = $row["dxcode_10"];
				if(empty($dxcode)){$dxcode = $row["dxcode"];}
				if(!empty($dxcode)){
					$assess = $assess." (".$dxcode.")";
				}

				$plans = "";
				$arrPlans = explode("\r\n",$plan);
				$len = count($arrPlans);
				for($j=0;$j<$len;$j++){
					$plans .= "<plan>".htmlentities(stripslashes($arrPlans[$j]), ENT_QUOTES)."</plan>";
				}

				$strXml .= "<ap>";
				$strXml .= "<ases>".htmlentities(stripslashes($assess), ENT_QUOTES)."</ases>";
				$strXml .= "<plans>".$plans."</plans>";
				//$strXml .= "<dxcode>".htmlentities(stripslashes($dxcode), ENT_QUOTES)."</dxcode>";
				$strXml .= "</ap>";
			}
			//-- GET ALL ORDERS NAME -------
			$qry = "select id,name from order_details";
			$ordersQryRes = sqlStatement($qry);
			$orderNameArr = array();
			for($or=0;$row=sqlFetchArray($ordersQryRes);$or++){
				$name = $row['name'];
				$id = $row['id'];
				$orderNameArr[$id] = $name;
			}
			//-- Get Order set ----
			$orderIdStr = join(',',$orderSetIdArr);
			if(!empty($orderIdStr)){
			$qry = "select id,orderset_name,order_id,order_set_option from order_sets
					where id in ($orderIdStr)";
			$ordersQryRes = sqlStatement($qry);
			for($i=0;$row=sqlFetchArray($ordersQryRes);$i++){

				$strXml .= "<order_dx>";
				$orderset_name = $row['orderset_name'];

				$orderSetId = $row['id'];
				$strXml .= "<order_set_id>$orderSetId</order_set_id>";
				$strXml .= "<order_set>$orderset_name</order_set>";


				//--- Order name for an order set ---------
				$order_id_arr = preg_split('/,/',$row['order_id']);
				$orders = '';
				for($os=0;$os<count($order_id_arr);$os++){
					$id = trim($order_id_arr[$os]);
					$name = $orderNameArr[$id];
					$orders .= "<orders_id>$id</orders_id>";
					$orders .= "<orders>$name</orders>";
				}

				$strXml .= "<order>$orders</order>";

				//---  ORDER SET OPTIONS --------
				$order_set_option_arr = preg_split('/\n/',$row['order_set_option']);
				$set_options = '';
				for($op=0;$op<count($order_set_option_arr);$op++){
					$order_set_options = trim($order_set_option_arr[$op]);
					if($order_set_options != ''){
						$set_options .= "<order_set_options>$order_set_options</order_set_options>";
					}
				}
				//if(trim($set_options) != ''){
					$strXml .= "<set_options>$set_options</set_options>";
				//}
				$strXml .= "</order_dx>";
			}
			}

		}

	}

	//XML HEADER
	header('Content-Type: text/xml');
	// make xml
	$strXml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?><allap>".$strXml."</allap>";

	/*
	header('Content-Type: text/xml');
	$strXml = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>".
		"<allap>".
		"</allap>";
	*/

	print($strXml);
	exit();

}

function getApCptFrmDx($apid,$arrdx, $flg_icd10){
	$oDx = new Dx();
	$arrRet = array();
	$tmp = "'".str_replace(",","','",$apid)."'";
	$sql = "SELECT * FROM console_to_do WHERE to_do_id IN (".$tmp.") ";
	$rez = sqlStatement($sql);

	for($i=0;$row=sqlFetchArray($rez);$i++){
		$fl_dx = ($flg_icd10==1) ? "dxcode_10" : "dxcode";
		if(!empty($row[$fl_dx]) && !empty($row["strCptCd"])){
			$arrApDx = explode(",",$row[$fl_dx]);
			$arr = explode(", ",$row["strCptCd"]);
			$len2 = count($arrApDx);
			for($j=0;$j<$len2;$j++){
				$flag_t=0;
				$dx = trim($arrApDx[$j]);

				//
				if($flg_icd10==1){
					list($tdx, $tdxId) = $oDx->refineDx($dx);
					if(!empty($tdx)){
						$dx = $tdx;
					}
				}

				if(in_array($dx,$arrdx)){$flag_t=1;}

				if($flg_icd10==1&& $flag_t==0){
					//check again
					$dx_lw=strtolower($dx);
					foreach($arrdx as $k => $v ){
						$v=strtolower($v);
						$q1=substr($v,0,-1)."-";
						$q2=substr($v,0,-2)."--";
						$q3=substr($v,0,-3)."-x-";
						//
						if($q1==$dx_lw||$q2==$dx_lw||$q3==$dx_lw||$v==$dx_lw){
							$flag_t=1;
						}
					}
				}

				if($flag_t==1){
					//$arrRet[$dx] = explode(", ",$row["strCptCd"]);
					foreach($arr as $k => $vcpt ){
						$arrRet[$vcpt][] = $dx;
					}
				}
			}
		}
	}
	return $arrRet;
}

function getDxFrmAses(){

	$oDx = new Dx();
	$strApId="";
	$elem_mxLen = $_POST["elem_mxLen"];
	$arrAssSrN = $arrAssess = $arrAssessDx = $arrAsDxId = array();
	for($i=1;$i<$elem_mxLen;$i++){
		if(isset($_POST["elem_assessment".$i]) && !empty($_POST["elem_assessment".$i])){
			$tmp = $_POST["elem_assessment".$i];
			$indxTmp = strpos($tmp,";");
			if($indxTmp !== false){
				$tmp = substr($tmp,0,$indxTmp);
			}
			$arrAssess[] = trim($tmp);
			$arrAssSrN[] = $i;
			$tmpDx = $tmpDxId = "";
			$tmp_xss=xss_rem($_POST["elem_assessmentDx".$i]);
			if(isset($tmp_xss) && !empty($tmp_xss)){
				$tmpDx = $_POST["elem_assessmentDx".$i];
				//dxid
				if(isset($_POST["elem_asmtDxId".$i]) && !empty($_POST["elem_asmtDxId".$i])){
					$tmpDxId = $_POST["elem_asmtDxId".$i];
				}
			}

			$arrAssessDx[] = trim($tmpDx);
			$arrAsDxId[] = trim($tmpDxId);
		}
	}

	$pureAssess = array();
	//Remove Site and Dx Codes.
	//$ptrn = "/\s+(\-\s+(OD|OS|OU)\s+)?\((\s*\w{3}(\.\w{1,2})?(\,)?)+\)$/";
	if(count($arrAssess) > 0){
		foreach($arrAssess as $key=> $val){
			/*
			if(preg_match($ptrn, $val)){
				$val = preg_replace($ptrn, "", $val);
			}
			*/
			$val = remSiteDxFromAssessment($val);
			$pureAssess[] = addslashes(trim($val));
		}
	}
	//print_r($pureAssess);
	//exit;
	$flagShowPop = 0;
	$arrAssessmentQ = array();
	//Get Dx From Assess
	if(count($pureAssess) > 0){

		//get dynamic AP Setting--
		/*
		$strAPSettings = getAPPolicySettings();
		if(empty($strAPSettings) || strpos($strAPSettings,"Dynamic")===false){
			$sqlPhraseAPSettings = " AND dynamic_ap != '1' ";
		}else{
			$sqlPhraseAPSettings = "";
		}
		*/
		$sqlPhraseAPSettings = " AND dynamic_ap != '1' ";
		//End get dynamic AP Setting--

		foreach($pureAssess as $key => $val){
			//First Check Asessment has A&Policy if yes then if Dx Is Attached use it
			//else go to admin Biling DxCode
			//if one Dx Attached use it
			//For More then one Show Prompt
			//For Dx code Attache Show Warning and let them type
			$arrDxAp = array(); $arrDxApId = array();
			$arrDxCat = array();
			//1. Check in A&Policy
			$tmp_providerId = $this->provider_id; //$_SESSION["authId"];
			//$str_pro_phrase = !empty($this->followedbyresi) ? " OR providerID='".$this->followedbyresi."' " : "";
			$str_pro_phrase = "";
			/*
			//Check if providerId is tech or scribe: if follow to physician, then apply his assessments and policy
			$isTech = getFollowPhyId4Tech($tmp_providerId);
			if(!empty($isTech)){
				$tmp_providerId = $isTech;
			}
			*/
			//

			//check dx code from assessment
			if(isset($arrAssessDx[$key]) && !empty($arrAssessDx[$key])){
				if(strpos($arrAssessDx[$key], ";")!==false){
					$tmpArr = explode(";", $arrAssessDx[$key]);
				}else {
					$tmpArr = explode(",", $arrAssessDx[$key]);
				}
				$tmpArr = array_filter($tmpArr);

				//dxid
				$tmpArrId = array();
				if(isset($arrAsDxId[$key]) && !empty($arrAsDxId[$key])){
					$tmpArrId = explode(",", $arrAsDxId[$key]);
				}

				//
				if(is_array($tmpArr) && count($tmpArr)>0){
					foreach($tmpArr as $k => $v){
						$v = trim($v);
						if(!empty($v)){
							$tmpid="";
							if(isset($tmpArrId[$k]) && !empty($tmpArrId[$k])){
								$tmpid=trim($tmpArrId[$k]);
							}
							//
							if(empty($tmpid) && !empty($tmpArrId[0])){
								$tmpid=trim($tmpArrId[0]);
							}

							$arrDxAp[] = $v;
							$arrDxApId[] = $tmpid;
						}
					}
				}
			}
			//--

			//
			$tmp_ICD_type = ($_REQUEST['ICD_type']==1) ? 1 : 0;

			//if( $tmp_ICD_type==0 ){ // work for  9 only : stopped on 15oct2015 for dr melter point

			$sql = "select * from console_to_do where LCASE(REPLACE(assessment,'\r\n',''))='".strtolower($val)."'
						 AND (providerID ='".$tmp_providerId."' OR providerID ='0' ".$str_pro_phrase.") ".
						 //"AND ICD_type = '".$tmp_ICD_type."' ".
						 $sqlPhraseAPSettings.
						 "ORDER BY providerID DESC, assessment, task DESC
						 ";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
				$fl_dx = ($tmp_ICD_type==1) ? "dxcode_10" : "dxcode";
				if(!empty($row[$fl_dx])){
					if(strpos($row[$fl_dx], ";")!==false){
						$tmpArr = explode(";", $row[$fl_dx]);
					}else{
						$tmpArr = explode(",", $row[$fl_dx]);
					}
					$tmpArr = array_filter($tmpArr);
					$arrDxAp = $oDx->ar_mrg_dxcodes_icd10($arrDxAp,$tmpArr);//do not merge hyphen icd 10 codes if they exits
					$strApId .= $row["to_do_id"].",";
				}
			}

			//}

			//Unique with ID
			//$arrDxAp = array_unique($arrDxAp);
			if(is_array($arrDxAp) && count($arrDxAp)){
				$str_unique="";
				$arrDxAp_t=array();
				$arrDxApId_t=array();
				foreach($arrDxAp as $k => $tdx){
					$tdx = trim($tdx);
					if(!empty($tdx)){
						$tdxId = (isset($arrDxApId[$k]) && !empty($arrDxApId[$k])) ? $arrDxApId[$k] : "";
						$tchk1 = "".$tdx;
						if(!empty($tdxId)){$tchk1 .= "@".$tdxId."";}
						$tchk1 .="*";
						if(strpos($str_unique, $tchk1)!==false){
							continue;
						}else{
							$str_unique .= "".$tchk;
							$arrDxAp_t[]=$tdx;
							$arrDxApId_t[]=$tdxId;
						}
					}
				}
				$arrDxAp = array(); $arrDxAp = $arrDxAp_t;
				$arrDxApId = array(); $arrDxApId = $arrDxApId_t;
			}
			//--

			$lenAPDx = count($arrDxAp);
			if( $lenAPDx >= 1 ){
				$arrAssessmentQ[$key] = array($val, $arrDxAp, $arrDxApId);
			}else{

				//get string before special character <
				$tmp = strpos($val, "<");
				if($tmp !== false){
					$val = substr($tmp,0,$tmp);
				}

				//remove Eye Values
				$ptrn = '/\s+(OD|OS|OU)\s*/i';
				if(preg_match($ptrn, $val)){
					$val = preg_replace($ptrn, "", $val);
					$val = trim($val);
				}
				if($tmp_ICD_type == 0){
					//Check in Admin-> Billing->Dx.Code
					$sql = "SELECT c1.dx_code,c2.category FROM diagnosis_code_tbl c1 ".
							"LEFT JOIN diagnosis_category c2 ON c2.diag_cat_id=c1.diag_cat_id ".
							"WHERE LCASE(c1.dx_code)='".strtolower($val)."' || LCASE(c1.d_prac_code)='".strtolower($val)."' || ".
							"LCASE(REPLACE(c1.diag_description,'\r\n',''))='".strtolower($val)."' ";
					$rez = sqlStatement($sql);
					for($i=1;$row=sqlFetchArray($rez);$i++){
						if(!empty($row["dx_code"])){
							if(strpos($row["dx_code"], ";")!==false){
							$tmpArr = explode(";", $row["dx_code"]);
							}else{
							$tmpArr = explode(",", $row["dx_code"]);
							}
							$tmpArr = array_filter($tmpArr);
							$arrDxAp = array_merge($arrDxAp,$tmpArr);
							$arrDxCat[$row["dx_code"]]=$row["category"];
						}
					}
					//
					$arrDxAp = array_unique($arrDxAp);
				}
			}

			$lenAPDx = count($arrDxAp);
			if( $lenAPDx >= 1 ){
				$arrAssessmentQ[$key] = array($val, $arrDxAp, $arrDxApId);
				if($lenAPDx >= 2 ){
					$flagShowPop = 1; // show pop up if there is more than two dx codes in a assessment
				}

			}else{
				//No Dx. Attached
				//Warning
				$arrAssessmentQ[$key] = array($val, $arrDxAp, $arrDxApId);
				$flagShowPop = 1; // show pop up if user input
			}
		}
	}
	//show pop up if more than 12 dx codes
	if( $key >= 12 ){
		$flagShowPop = 1;
	}

	if(!empty($strApId)){
		$strApId = substr($strApId,0,-1);
	}

	//dx
	$oDx = new Dx();

	// make xml
	//XML HEADER
	header('Content-Type: text/xml');
	$str = "";
	$str .= "<?xml version='1.0' encoding='ISO-8859-1'?>";
	$str .= "<asinfo>";

		if( count($arrAssessmentQ) > 0 ){
			foreach($arrAssessmentQ as $key => $val ){
				if(empty($val[0]))continue;
				$str .= "<assess>";
					$str .= "<srno><![CDATA[".($key+1)."]]></srno>";
					$str .= "<name indx=\"".$arrAssSrN[$key]."\"><![CDATA[".htmlentities(stripslashes($val[0]), ENT_QUOTES)."]]></name>";
					$lenDx=count($val[1]);
					if($lenDx > 0){
						$tmp_ardxId = $val[2];
						//Check if multiple then add category of dx
						foreach($val[1] as $key2 => $val2){
							$val2=trim($val2);
							$catDx="";
							if($lenDx > 1){
								if(!empty($arrDxCat[$val2])){ $catDx= "cat=\"".htmlentities($arrDxCat[$val2], ENT_QUOTES)."\""; }
							}

							//if ICD 10 and dx code not complete then add modifiers
							$strLSS="";
							if($tmp_ICD_type == 1){
								//if(strpos($val2,"-")!==false){ ////if icd 10 and dx code has - then show pop
									$strLSS = $oDx->icd10_getDxLSS($val2);
									if($strLSS!="" && strpos($val2,"-")!==false){
										$flagShowPop = 1;
									}
								//}

								//
								//idDx
								$attrdxid = "";
								$tmpDxId = trim($tmp_ardxId[$key2]);
								if(!empty($tmpDxId)){
									$attrdxid = " dxid=\"".$tmpDxId."\" ";
								}
							}

							$str .= "<dx ".$attrdxid." ".$catDx." ".$strLSS." ><![CDATA[".htmlentities($val2, ENT_QUOTES)."]]></dx>";
						}
					}
				$str .= "</assess>";
			}
			$str .= "<prompt_user>".$flagShowPop."</prompt_user>";
		}

	$str .=	"<apid>".$strApId."</apid>";
	$str .= "</asinfo>";
	echo $str;

}

/**/
function getPhyConsoleTasks(){

	$arrConsoleExLower=array();
	$arrConsoleEx=array();
	$arrConsoleSev=$arrConsoleLoc=array();
	$arrConsolePhyId=array();
	/*
	$providerId = $_SESSION['authUserID'];

	//Check if providerId is tech or scribe: if follow to physician, then apply his assessments and policy
	$isTech = getFollowPhyId4Tech($providerId);
	if(!empty($isTech)){
		$providerId = $isTech;
	}
	*/
	$providerId = $this->provider_id;
	//$str_pro_phrase = !empty($this->followedbyresi) ? " || providerID='".$this->followedbyresi."' " : "";
	$str_pro_phrase = "";

	//
	$sql = "select task,assessment,plan, severity, location,to_do_id, providerID from console_to_do where ".
		   "to_do != 'yes' and (providerID ='".$providerId."' || providerID=0 ".$str_pro_phrase.") ".
		   "AND task != '' ".
		   "order by providerID DESC, assessment, task DESC ";
	$res = sqlStatement($sql);
	for( $i=0;$row=sqlFetchArray($res);$i++ ){
		if( !empty($row["task"]) && !empty($row["assessment"])){

			$tmp = $row["task"];
			$arrTmp = (strpos($tmp,";") !== false) ? explode(";",$tmp) : explode(",",$tmp);

			if(count($arrTmp) > 0){
				foreach($arrTmp as $key=> $val){
					$arrConsoleExLower[]= trim(strtolower($val));
					$arrConsoleEx[]= trim($val);
					$arrConsoleSev[]=$row["severity"];
					$arrConsoleLoc[]=$row["location"];
					$arrConsolePhyId[]=$row["providerID"];
				}
			}else{
				$arrConsoleExLower[]= trim(strtolower($tmp));
				$arrConsoleEx[]= trim($tmp);
				$arrConsoleSev[]=$row["severity"];
				$arrConsoleLoc[]=$row["location"];
				$arrConsolePhyId[]=$row["providerID"];
			}
		}
	}

	// set global --
	$this->arrConsole["Sev"]=$arrConsoleSev;
	$this->arrConsole["Loc"]=$arrConsoleLoc;
	$this->arrConsole["Id"]=$arrConsoleId;
	$this->arrConsole["ExLower"]=$arrConsoleExLower;
	$this->arrConsole["Ex"]=$arrConsoleEx;
	//--

	return array($arrConsoleExLower,$arrConsoleEx,$arrConsoleSev,$arrConsoleLoc,$arrConsolePhyId);
}

//return console id with matched exam symptom
function refineByConsoleSymp_v2($exmNm, $symp,$strExmSum,$arlinx,$symp_db){
	$arrConsoleSev = $this->arrConsole["Sev"];
	$arrConsoleLoc = $this->arrConsole["Loc"];
	$arrConsolePhyId = $this->arrConsole["Id"];

	$retStr = ""; ///with severity or location
	$retStrE = ""; ///without
	if(count($arlinx)>0){
		$septr = ($exmNm=="Pupil" || $exmNm=="External") ? "." : ";";
		$arrExmSum = explode($septr, $strExmSum);
		//get symptom summary
		$strSympSumm="";
		if(count($arrExmSum)>0){
			foreach($arrExmSum as $k1 => $v1){

				//for symp with parent sub exam, get last word that is sub sub exam name
				if(strpos($symp,"/")!==false){ $tmp = explode("/", $symp); $symp_s=end($tmp);}
				if(stripos($v1, $symp)!==false||stripos($v1, $symp_s)!==false){

					$strSympSumm=$v1;
					//refine summ from symp name: T can be found in blepharitis
					$strSympSumm= trim(str_replace("$symp","",$strSympSumm));
					if(stripos($v1, $symp_s)!==false){$strSympSumm= trim(str_replace("$symp_s","",$strSympSumm));}
					break;
				}
			}
		}

		//lids opts
		$ar_lids=array("RUL","RLL","LUL","LLL");

		// to check for physician only if exists
		$arrUniSev=$arrUniLoc=$arrUniSevLoc=array();//
		$allow_phyId="";

		foreach($arlinx as $k => $v){
			if($v!==""){
				$tmp_sev = $arrConsoleSev[$v];
				if($tmp_sev=="null"){$tmp_sev="";}
				$tmp_loc = "";//$arrConsoleLoc[$v]; // stopped location check
				$tmp_phyid = $arrConsolePhyId[$v];

				//
				if(!empty($strSympSumm)){

					//Check Lids in summ
					$lidsOpts="";
					foreach($ar_lids as $k_lids => $v_lids){
						if (preg_match("/\b".$v_lids."\b/i", $strSympSumm)) {
							if(!empty($lidsOpts)){  $lidsOpts.=","; }
							$lidsOpts.=$v_lids;
						}
					}


					//if(!empty($tmp_sev) && !in_array_nocase($v1,$arrUniSev) ){ //
					if(!empty($tmp_sev)){ //
						//$arrUniSev[] = $v1;
						$ar_tmp_sev = explode(",", $tmp_sev);
						if(count($ar_tmp_sev) > 0){
							$retStr_tmp=""; //same symp do not get entered again and again
							foreach($ar_tmp_sev as $k1 => $v1){
								$v1=trim($v1);
								//check severity if exists in summary only then add
								//if(stripos($strSympSumm, $tmp_sev)!==false){
								if (empty($retStr_tmp) && preg_match("/\b".$v1."\b/i", $strSympSumm)) {
									//$retStr.=$arrConsoleId[$v].",";
									$chkunistr="$v1";
									if(!in_array_nocase($chkunistr,$arrUniSev)){ // ||  $tmp_phyid==$allow_phyId
										//$retStr_tmp.= $symp_db."!~!".$tmp_sev."~!~".$tmp_loc."~!!~";
										$retStr_tmp.= $symp_db."!~!".$v1."~!~".$tmp_loc."~L~".$lidsOpts."~!!~";
										$arrUniSev[] = $chkunistr;
										$allow_phyId = $tmp_phyid;
									}
								}
							}
							$retStr.=$retStr_tmp;
						}
					}

				}

				//
				if(empty($tmp_sev) && empty($tmp_loc)){
					if(empty($retStrE)){
						//$retStrE = $arrConsoleId[$v];
						$retStrE .= $symp_db."~L~".$lidsOpts."~!!~";
					}
				}
			}
		}

		//attach empty if nothing found with severity/location
		if(empty($retStr) && !empty($retStrE)){
			$retStr = 	$retStrE;
		}

	}

	return $retStr;

}

function refineByConsoleSymp($exmNm,$arrExmDone,$strExmSum){

	//initialise : $this->arrConsole
	if(count($this->arrConsole["Id"])<=0){
		//Get Phy Console tasks
		list($arrConsoleExLower,$arrConsoleEx,$arrConsoleSev,$arrConsoleLoc,$arrConsolePhyId) = $this->getPhyConsoleTasks();
	}else{
		$arrConsoleSev=$this->arrConsole["Sev"];
		$arrConsoleLoc=$this->arrConsole["Loc"];
		$arrConsoleId=$this->arrConsole["Id"];
		$arrConsoleExLower=$this->arrConsole["ExLower"];
		$arrConsoleEx=$this->arrConsole["Ex"];
	}

	$retStr = "";
	if(count($arrConsoleExLower) > 0){
		foreach($arrExmDone as $key => $val){
			$symp = $key;
			$sympParent = $val;
			//Check symp
			list($tmp,$arlinx) = in_array_nocase($symp, $arrConsoleExLower,1);
			if($tmp!==false){
				///$retStr .= $symp.",";
				$retStr .= $this->refineByConsoleSymp_v2($exmNm,$symp,$strExmSum,$arlinx,$symp);
			}else{
				//Check symp with Exam name
				list($tmp,$arlinx) = in_array_nocase($exmNm."/".$symp, $arrConsoleExLower,1);
				if($tmp!==false){
					//$retStr .= $exmNm."/".$symp.",";
					$retStr .= $this->refineByConsoleSymp_v2($exmNm,$symp,$strExmSum,$arlinx,$exmNm."/".$symp);
				}else{
					//Check Parent Symp
					list($tmp,$arlinx) = in_array_nocase($sympParent, $arrConsoleExLower,1);
					if($tmp!==false){
						//$retStr .= $sympParent.",";
						$retStr .= $this->refineByConsoleSymp_v2($exmNm,$sympParent,$strExmSum,$arlinx,$sympParent);

					}else{
						//Check Parent Symp with Exam Name
						list($tmp,$arlinx) = in_array_nocase($exmNm."/".$sympParent, $arrConsoleExLower,1);
						if($tmp!==false){
							//$retStr .= $exmNm."/".$sympParent.",";
							$retStr .= $this->refineByConsoleSymp_v2($exmNm,$sympParent,$strExmSum,$arlinx,$exmNm."/".$sympParent);
						}
					}
				}
			}
		}
	}

	return $retStr;
}

//Seprate Severity and location from Symp and return array
function sepSevLocFromSymp($str){
	$symp=$sev=$loc=$ap_site="";
	$str=trim($str);
	if(!empty($str)){
		$ar_str = explode("!~!",$str) ;
		$symp = trim($ar_str[0]);
		$t2 = trim($ar_str[1]);
		if(!empty($t2)){
			$ar_str_2 = explode("~!~",$t2);
			$sev = trim($ar_str_2[0]);
			$t3 = trim($ar_str_2[1]);
			if(!empty($t3)){
				$ar_str_3 = explode("~^~",$t3);
				$loc = trim($ar_str_3[0]);
				$ap_site = trim($ar_str_3[1]);
			}
		}
	}
	return array($symp,$sev,$loc,$ap_site);
}

//
function isAP_ExistsForDxDos($res, $dos, $icd_code_10="9"){
	$oDx = new Dx();
	$ret=0;
	if(imw_num_rows($res)>0){
		for( $i=0;$row=sqlFetchArray($res);$i++ ){
			if(!empty($row["assessment"])){
				if($icd_code_10=="1"){
				$tmpDxCode10=trim($row["dxcode_10"]);
				if(!empty($tmpDxCode10)){
					list($tmpDxCode, $tmpDxCodeId) = $oDx->refineDx($tmpDxCode10);
					//--
					//check if Dx belongs to dos: if not continue;
					if(!Dx::isDxCodeBelongsToDos($dos, $tmpDxCode, $tmpDxCodeId)){		continue;		}
					//--
					$ret+=1;
				}else{$ret+=1;}
				}else{
					$ret+=1;
				}
			}
		}
	}
	return $ret;
}

function getAssessmentAndPolicies_from_Symp($symp, $icd_code_10="9", $eye="",$cnslid="", $dos=""){

	if(empty($symp)&&empty($cnslid)){  return array(); }

	$ret = array();
	$providerId = $this->provider_id;
	//$str_pro_phrase = !empty($this->followedbyresi) ? " OR providerID='".$this->followedbyresi."' " : "";
	$str_pro_phrase = "";
	/*
	$res_fellow_sess = trim($_SESSION['res_fellow_sess']);
	if($res_fellow_sess != "" && isset($res_fellow_sess))
	{
		$providerId = $res_fellow_sess;
	}
	else
	{
		$providerId = $_SESSION['authUserID'];

		//Check if providerId is tech or scribe: if follow to physician, then apply his assessments and policy
		$isTech = getFollowPhyId4Tech($providerId);
		if(!empty($isTech)){
			$providerId = $isTech;
		}
	}
	*/

	//if console _to_do_id is not empty
	if(!empty($cnslid)){
		$sql = "select task,assessment,plan,dxcode,dxcode_10 from console_to_do
			where ".
			" to_do_id = '".$cnslid."' ".
			//"to_do != 'yes' and (providerID ='".$providerId."') ".
			//"AND (UPPER(TRIM(task)) = UPPER('".trim($symp)."'))".
			//"order by providerID DESC, assessment, task DESC  ".
			//"LIMIT 0,1 ";
			"";
		$res = sqlStatement($sql);

	}else{

	///seprate severity and location from finding
	$symp = trim($symp);

	list($symp, $sev, $loc, $ap_site) = $this->sepSevLocFromSymp($symp);

	$phr_sev = $phr_loc="";
	if(!empty($sev)){
		$phr_sev = " severity='' ";
		$arsev = explode(",", $sev);
		$ar_ap_site = explode(",", $ap_site);
		foreach($arsev as $k => $v){
			$v=trim($v);
			//$phr_sev = " AND severity='".sqlEscStr($sev)."' ";
			if(!empty($phr_sev)){  $phr_sev .= " OR ";  }
			$phr_sev .= " CONCAT(severity,',') LIKE '%".sqlEscStr($v.",")."%' ";
		}
		//
		if(!empty($phr_sev)){  $phr_sev = " AND (".$phr_sev.") ";  }
	}

	if(!empty($loc)){
		$phr_loc = " AND location='".sqlEscStr($loc)."' ";
	}

	/*
	Please, please ensure that Physician personal A&P takes precedence over the community for matching Assessment or Findings.
	*/
	///check provider
	$sql = "select task,assessment,plan,dxcode,dxcode_10,severity, to_do_id from console_to_do
			where to_do != 'yes' and (providerID ='".$providerId."' ".$str_pro_phrase.") ".
			"AND (UPPER(TRIM(task)) = UPPER('".trim($symp)."')) ".
			$phr_sev.$phr_loc.
			"order by providerID DESC, assessment, task DESC  ".
			//"LIMIT 0,1 ";
			"";
	$res = sqlStatement($sql);

	//check community
	if($this->isAP_ExistsForDxDos($res, $dos, $icd_code_10)<=0){
		$sql = "select task,assessment,plan,dxcode,dxcode_10,severity, to_do_id from console_to_do
				where to_do != 'yes' and (providerID=0) ".
				"AND (UPPER(TRIM(task)) = UPPER('".trim($symp)."')) ".
				$phr_sev.$phr_loc.
				"order by providerID DESC, assessment, task DESC  ".
				//"LIMIT 0,1 ";
				"";
		$res = sqlStatement($sql);

		//check provider for similar asssessments
		if($this->isAP_ExistsForDxDos($res, $dos, $icd_code_10)<=0){
			$sql = "select task,assessment,plan,dxcode,dxcode_10,severity, to_do_id from console_to_do
				where to_do != 'yes' and (providerID ='".$providerId."' ".$str_pro_phrase.") ".
				"AND (UPPER(task) LIKE '".strtoupper($symp)."%') ".
				$phr_sev.$phr_loc.
				"order by providerID DESC, assessment, task DESC  ".
				//"LIMIT 0,1 ";
				"";
			$res = sqlStatement($sql);

			//check community for similar asssessments
			if($this->isAP_ExistsForDxDos($res, $dos, $icd_code_10)<=0){
				$sql = "select task,assessment,plan,dxcode,dxcode_10,severity, to_do_id from console_to_do
					where to_do != 'yes' and (providerID=0) ".
					"AND (UPPER(task) LIKE '".strtoupper($symp)."%') ".
					$phr_sev.$phr_loc.
					"order by providerID DESC, assessment, task DESC  ".
					//"LIMIT 0,1 ";
					"";
				$res = sqlStatement($sql);
			}
		}
	}

	}

	//--
	//rerun sql which prevails
	$res = sqlStatement($sql);

	$ln2=is_array($arsev) ? count($arsev) : 0 ;
	$flg_empty_sev=0;
	$arrAs = array();$ar_sev_all = array();
	//Dx
	$odx = new Dx();

	for( $i=0;$row=sqlFetchArray($res);$i++ ){

		$strPlan=$strAssess=$strDxCods=$strDxIds="";
		if(!empty($row["assessment"])){

			$temp = (!empty($row["assessment"])) ? trim($row["assessment"]) : "" ;
			if($icd_code_10=="1"){
				$tmpDxCode10=trim($row["dxcode_10"]);
				if(!empty($tmpDxCode10)){
					list($tmpDxCode, $tmpDxCodeId) = $odx->refineDx($tmpDxCode10);

					//--
					//check if Dx belongs to dos: if not continue;
					if(!Dx::isDxCodeBelongsToDos($dos, $tmpDxCode, $tmpDxCodeId)){
						continue;
					}
					//--

				}else{

					$tmpDxCode = $odx->convertICDDxCode($tmpDxCode, "9", "1");
				}
				//$tmpDxCode = modifyICDDxCodeWEye($tmpDxCode, $eye);
			}else{
				$tmpDxCode = trim($row["dxcode"]);
			}
			if(!empty($tmpDxCode)){
				//$temp .=  " (".$tmpDxCode.")" ;
				$strDxCods=$tmpDxCode;
				$strDxIds =$tmpDxCodeId;
			}

			if(!empty($temp)){
				$strAssess .=  "".$temp." " ;
				$arrAs[] = $temp;
			}
			$tmpPlan =  (!empty($row["plan"])) ? trim($row["plan"]) : "" ;  //str_replace(array("\r","\n","'"),array("\\r","\\n","\'"),$row["plan"]);
			$strPlan .= "".$tmpPlan." " ;

			//$strDxCods .= "'".$row["dxcode"]."'~!!~" ;
			//severity check for pop up
			$severity = $row["severity"];
			if(!empty($severity)){
				$tmp = explode(",", $severity); $ar_sev_all = array_merge($ar_sev_all,$tmp);
			}else{
				$flg_empty_sev=1;
			}

			///
			if(!empty($strAssess) || !empty($strPlan)){
				//loop seveirty
				if($ln2>0){


					$str_ap_site="";
					foreach($arsev as  $as_k => $as_v){
						$as_v=trim($as_v);


						if(strpos($severity.",", $as_v.",")!==false){
							if($ar_ap_site[$as_k] == "OU"){
								$str_ap_site="OU";
								break;
							}else{
								if(empty($str_ap_site)){
									$str_ap_site=$ar_ap_site[$as_k];
								}else{
									if($str_ap_site!=$ar_ap_site[$as_k]){
										$str_ap_site="OU";
										break;
									}
								}
							}
						}
					}
				}

				//
				if($icd_code_10=="1"&&!empty($strDxCods)){
					if($str_ap_site==""){  $str_ap_site=$eye; }
					$arrmodifyICD = $odx->modifyICDDxCodeWEye($strDxCods, $str_ap_site,"", "1",1);
					$strDxCods=$arrmodifyICD[0];
					$str_lids_asmt=$arrmodifyICD[1];
				}
				//

				$ret[]=array("assess"=>$strAssess, "plan"=>$strPlan, "dxcode"=>$strDxCods, "severity"=>$severity, "ap_site"=>$str_ap_site, "to_do_id"=>$row["to_do_id"], "asmt_com"=>$str_lids_asmt, "dxId"=>$strDxIds);
			}
		}
	}

	//
	$ln0=count($ret);

	if($ln0>0){
		//working here--
		if($ln2>0&&$flg_empty_sev==0){
			$ar_count_val = array_count_values ($ar_sev_all);

			foreach($ar_count_val as $cval => $cn ){
				if($cn>1){
					if(in_array($cval,$arsev)){
						for($j=0;$j<$ln0;$j++){
							if(strpos($ret[$j]["severity"].",", $cval.",")!==false){
								$ret[$j]["pu"]=1;//pop up
							}
						}
					}
				}
			}

		}else{
			for($j=0;$j<$ln0;$j++){
				$ret[$j]["pu"]=1;//pop up
			}
		}
	}

	return $ret;
}

function getConsoleVal($todoid, $site, $icd="",$lids_opts=""){
	$oDx = new Dx();
	if(!empty($lids_opts)){  $site = $lids_opts; }
	$assessment=$plan="";
	$arrFu=array();
	$sql = "SELECT * FROM console_to_do WHERE ".
			"to_do_id='".$todoid."' ";
	$row = sqlQuery($sql);
	if($row != false){
		$assessment = trim($row["assessment"]);
		$plan = trim($row["plan"]);

		if($icd==1){
			$dxcode = trim($row["dxcode_10"]);
			list($dxcode, $dxcodeId) = $oDx->refineDx($dxcode);
		}else{
			$dxcode = trim($row["dxcode"]);
			$dxcodeId = "";
		}

		$xmlFU = $row["xmlFU"];

		if(!empty($dxcode)){
			$odx = new Dx();
			$arrmodifyICD=$odx->modifyICDDxCodeWEye($dxcode, $site, "", 1);
			$dxcode=$arrmodifyICD[0];
			$str_lids_asmt=trim($arrmodifyICD[1]);
			if(!empty($str_lids_asmt)){
				if(strpos($assessment, ";")===false){ $assessment=$assessment."; "; }else{ $assessment=$assessment.", "; }
				$assessment = $assessment.$str_lids_asmt;
			}
		}

		//$assessment = sc_getAssesProcessed($assessment,$site,$dxcode);
		//$arrFu = $this->sc_getFUArr($xmlFU);
		if(!empty($xmlFU)){
			$oFu = new Fu();
			list($lenFu, $arrFu) = $oFu->fu_getXmlValsArr($xmlFU);
		}
	}
	return array("A"=>$assessment,"P"=>$plan,"FU"=>$arrFu,"DX"=>$dxcode, "DXID"=>$dxcodeId);
}


}
?>
