<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: ChartAP.php
Coded in PHP7
Purpose: This is a class file for Assessment Plan related functions.
Access Type : Include file
*/
?>
<?php
//Chart AP
class ChartAP{
	private $pid, $fid;
	public function __construct($pid,$fid=""){
		$this->pid=$pid;
		$this->fid=$fid;
	}

	public function getAPId(){
		$sql = "SELECT id FROM chart_assessment_plans ".
			   "WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."' ";
		$res = sqlQuery($sql);
		if($res!=false){
			return $res["id"];
		}
		return false;
	}

	public function isRecordExists(){
		$sql = "SELECT count(*) AS num FROM chart_assessment_plans ".
			   "WHERE patient_id='".$this->pid."' AND form_id='".$this->fid."' ";
		//$res = $this->db->Execute($sql);
		$res = sqlQuery($sql);
		if($res!=false && $res["num"]>0){
			return true;
		}
		return false;
	}

	public function insertNewRec(){
		$ousr = new User();
		$uTp = $ousr->getUType(1);
		if($uTp=="1"){
			$sqlDocId=$_SESSION["authId"];
		}

		//Insert a blank record
		$sql =	"INSERT INTO chart_assessment_plans(id,patient_id,form_id,doctorId,exam_date) ".
				"VALUES (NULL,'".$this->pid."','".$this->fid."','".$sqlDocId."','".date("Y-m-d")."' ) ";
		//$res = $this->db->Execute($sql);
		$res = sqlQuery($sql);
	}

	public function getLFAssess(){
		$arrAp=array();
		$ocn = new PtChart($this->pid);
		$idLF = $ocn->getLFFormId();
		if($idLF!=false){
			//Ap xml array
			$oChartApXml = new ChartAP($this->pid,$idLF);
			$arrApVals = $oChartApXml->getVal();
			$arrAp = $arrApVals["data"]["ap"];
			$lenAssess = count($arrAp);
			for($i=0;$i<$lenAssess;$i++){
				if(empty($arrAp[$i]["resolve"]) && !empty($arrAp[$i]["assessment"])){
					$arrAp["assess"][] = $arrAp[$i]["assessment"];
					$arrAp["plan"][] = "";
					$arrAp["resolve"][] = "";
					$arrAp["ne"][] = "";
					$arrAp["eye"][] = "";
					$arrAp["conmed"][] = "";
					$arrAp["pbid"][] = $arrAp[$i]["pbid"];
					$arrAp["pt_ap_id"][] = $arrAp[$i]["pt_ap_id"];
					$arrAp["dx_id"][] = $arrAp[$i]["dxcodeId"];
				}
			}
		}
		return $arrAp;
	}

	public function getArrAP($flgAll="0", $flgDx="0"){
		$arrAp=array();
		//Ap xml array

		$arrApVals = $this->getVal();
		$arrAp = $arrApVals["data"]["ap"];
		$lenAssess = count($arrAp);
		for($i=0;$i<$lenAssess;$i++){
			if((empty($arrAp[$i]["resolve"]) && !empty($arrAp[$i]["assessment"])) || !empty($flgAll)){
				$arrAp["assess"][] = trim($arrAp[$i]["assessment"]);
				$arrAp["plan"][] = trim($arrAp[$i]["plan"]);
				$arrAp["resolve"][] = $arrAp[$i]["resolve"];
				$arrAp["ne"][] = $arrAp[$i]["ne"];
				$arrAp["eye"][] = $arrAp[$i]["eye"];
				$arrAp["conmed"][] = $arrAp[$i]["conmed"];
				$arrAp["pbid"][] = $arrAp[$i]["pbid"];
				$arrAp["pt_ap_id"][] = $arrAp[$i]["pt_ap_id"];
				$arrAp["dx_id"][] = $arrAp[$i]["dxcodeId"];

				if(!empty($flgDx)){
					$dx_tmp="";
					$tmp = trim($arrAp[$i]["assessment"]);
					if(!empty($tmp)){ list($as_tmp, $dx_tmp) = $this->getDxCodeFromAssess($tmp);  }
					$arrAp["dx"][] = $dx_tmp;
				}
			}
		}
		return $arrAp;
	}

	public function getArrLog(){
		$arrAp=array();
		//Ap xml array

		$arrApVals = $this->getVal();
		$arrAp = $arrApVals["updates"]["update"];
		$lenAssess = count($arrAp);
		for($i=0;$i<$lenAssess;$i++){
			if(!empty($arrAp[$i]["dt_time"]) && !empty($arrAp[$i]["usrId"])){
				$arrAp[$i]["dt_time"] = date("m-d-Y H:i:s",$arrAp[$i]["dt_time"]);

				$user= new User($arrAp[$i]["usrId"]);
				$arrAp[$i]["usr"] = $user->getName();
			}
		}
		return $arrAp;
	}

	public function getArrFU(){
		$oFu = new Fu($this->pid,$this->fid);
		list($lenFu, $arrFuVals) = $oFu->fu_getXmlValsArr_db();
		return $arrFuVals;
	}

	public function mergeFuArr($arr1,$arr2){
		$oFu = new Fu($this->pid,$this->fid);
		return $oFu->mergeFuArr($arr1,$arr2);
	}

	public function updateAp_Fu($arrAP_db,$arrFU_db){

		$idAp = $this->getAPId();
		$strXml = $this->getDbVal();

		if(!empty($strXml)){
			$strApXml = $this->getXml(array($arrAP_db["assess"],$arrAP_db["plan"],
												$arrAP_db["resolve"],$arrAP_db["ne"],$arrAP_db["eye"],$arrAP_db["conmed"],$arrAP_db["pbid"]),
											$_SESSION["authId"]);
		}else{
			$strApXml="";
			$this->save_pt_assess_plan(array($arrAP_db["assess"],$arrAP_db["plan"],
												$arrAP_db["resolve"],$arrAP_db["ne"],$arrAP_db["eye"],$arrAP_db["conmed"],$arrAP_db["pbid"], $arrAP_db["pt_ap_id"],$arrAP_db["dxid"]), $idAp);
		}

		$oFu = new Fu($this->pid,$this->fid);
		$strFuXml = $oFu->fu_getXml($arrFU_db);

		//Update
		$sql = "UPDATE chart_assessment_plans ".
				"SET ".
				"assess_plan = '".sqlEscStr($strApXml)."', ".
				"followup = '".sqlEscStr($strFuXml)."' ".
				"WHERE form_id='".$this->fid."' AND patient_id='".$this->pid."'  ";
		//$res = $this->db->Execute($sql);
		$res = sqlQuery($sql);
	}

	function clear_eso_exo($str){
		if(stripos($str, 'esotropia')!==false || stripos($str, 'exotropia')!==false){
		$arr=array('Unspecified', 'Monocular', 'Alternating', 'A pattern', 'V pattern', 'Intermittent', 'Right', 'Left', ',', 'with', 'eye' );
		$str = str_ireplace($arr, '', $str);
		$str = trim($str);
		}
		return $str;
	}

	public function getPlansofAsmt($asmt){
		$asmt=trim($asmt);
		$asmt_complete=$asmt;

		//dos
		if(!empty($this->fid)){
			$oCN = new ChartNote($this->pid,$this->fid);
			$dos = $oCN->getDos(1);
		}

		//clear
		$indxTmp = strpos($asmt,";");
		if($indxTmp !== false){
			$asmt = substr($asmt,0,$indxTmp);
			$asmt = trim($asmt);
		}

		//if Esotropia, Exotropia
		$asmt = $this->clear_eso_exo($asmt);

		$arrplans=array();
		$high_flg=0;
		if(!empty($asmt)){
			$sql="SELECT c2.assess_plan, c2.id FROM chart_master_table c1
				LEFT JOIN chart_assessment_plans c2 ON c2.form_id=c1.id
				WHERE c1.patient_id='".$this->pid."' AND delete_status='0' AND purge_status='0'
				AND c1.finalize='1'
				ORDER BY c1.date_of_service DESC, c1.create_dt DESC, c1.id DESC ".
				/*07-02-2015
				//Previous DOS order should only be listed and selected in Red in the Order/Order Set form.  Currently it is listing all the previous Order/Order Set.  This was the main issue identified by TUFTS also.
				*/
				"LIMIT 0,1 ".
				"";
			$rez=sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++){
				$strXml = $row["assess_plan"];
				$idAp = $row["id"];
				//if(empty($strXml))continue;
				if(!empty($strXml)){	$arrTmp=$this->getVal_Str($strXml); }
				else{ $arrTmp=$this->get_pt_ap_db($idAp); }

				if(count($arrTmp["data"])>0){
					foreach($arrTmp["data"] as $key=>$val){
						$ap=$val;
						$lnap = count($ap);
						for($k=0;$k<$lnap;$k++){
							$asTmp=trim($ap[$k]["assessment"]);
							$plTmp=trim($ap[$k]["plan"]);

							//Remove Site and Dx Codes.
							$asTmp_2="";
							$asTmp_2 = remSiteDxFromAssessment($asTmp);
							/*
							$ptrn = "/\s+(\-\s+(OD|OS|OU)\s+)?\((\s*\d{3}(\.\d{1,2})?(\,)?)+\)$/";
							if(preg_match($ptrn, $asTmp)){
								$asTmp_2 = preg_replace($ptrn, "", $asTmp);
								$asTmp_2 = trim($asTmp_2);
							}
							*/

							if(!empty($asmt) && ($asTmp==$asmt || $asTmp_2==$asmt) && !empty($plTmp)){
								//Check if Multiple plans
								$arrMCPlans = explode("\n",$plTmp);
								if(count($arrMCPlans)>0){
									foreach($arrMCPlans as $kMCP => $vMCP){
										//remove added characters
										$vMCP = str_replace(chr(0xC2).chr(0xAD),"", $vMCP);
										$vMCP = trim($vMCP);
										if(empty($vMCP))continue;
										if(!in_array($vMCP,$arrplans)){
											if($i==0){
												//$plTmp="~HI@GH#".$plTmp;
												$high_flg++;
											}

											if(!empty($vMCP)){
												$arrplans[]=$vMCP;
											}
										}
									}
								}

							}
						}
					}
				}
			}

			////////////////////


			//Remove Site and Dx Codes.
			$asmt_full = $asmt;
			$asmt = remSiteDxFromAssessment($asmt);

			//get Ap Settings --
			/*
			if(!function_exists("getAPPolicySettings")){
				include_once(dirname(__FILE__)."/functions.php");
			}
			$strAPSettings = getAPPolicySettings();
			if(empty($strAPSettings) || strpos($strAPSettings,"Dynamic")===false){
				$sqlPhraseAPSettings = " AND dynamic_ap != '1' ";
			}else{
				$sqlPhraseAPSettings = "";
			}
			*/
			$sqlPhraseAPSettings = " AND dynamic_ap != '1' ";
			//End get Ap Settings --

			//Check fellow doctor ---
			if(isset($_SESSION["res_fellow_sess"]) &&  !empty($_SESSION["res_fellow_sess"])){
				$tmp_proid=$_SESSION["res_fellow_sess"];
			}else{
				$tmp_proid=$_SESSION["authId"];

				//check if function exists
				//if(!function_exists("getFollowPhyId4Tech")){
					//include_once(dirname(__FILE__)."/functions.php");
				//}

				//Check if providerId is tech or scribe: if follow to physician, then apply his assessments and policy
				$oUsr = new User($tmp_proid);
				$isTech = $oUsr->getFollowPhyId4Tech();
				if(!empty($isTech)){
					$tmp_proid = $isTech;
				}

			}

			//
			$oDx = new Dx();

			$strAssess=""; $strAssesswpro="";
			$sql = "select assessment, plan, dxcode_10, providerID from console_to_do
					where plan!='' AND
					(LCASE(REPLACE(assessment,'\r\n',''))='".sqlEscStr(strtolower($asmt))."' OR LCASE(assessment)='".sqlEscStr(strtolower($asmt))."' OR  LCASE(REPLACE(assessment,'\r\n',''))='".sqlEscStr(strtolower($asmt_complete))."' OR LCASE(assessment)='".sqlEscStr(strtolower($asmt_complete))."' OR LCASE(REPLACE(assessment,'\r\n',''))='".sqlEscStr(strtolower($asmt_full))."' OR LCASE(assessment)='".sqlEscStr(strtolower($asmt_full))."')
					AND (providerID ='".$tmp_proid."' OR providerID ='0') ".
					$sqlPhraseAPSettings.
					"ORDER BY providerID DESC, assessment, task DESC
				   ";
			$rez = sqlStatement($sql);
			for($i=0;$row=sqlFetchArray($rez);$i++){

			$temp="'".$row["assessment"]."'";
			$temp2="'".$row["assessment"].$row["providerID"]."'";

			//check if assessment already exists: if yes Do not add again.
			if(strpos($strAssess,$temp)!==false && strpos($strAssesswpro,$temp2)===false){
				continue;
			}

			// --

			if(!empty($row["dxcode_10"])){
				list($tdx, $tdxId) = $oDx->refineDx($row["dxcode_10"]);
				//check if Dx belongs to dos: if not continue;
				if(!Dx::isDxCodeBelongsToDos($dos, $tdx)){	continue;		}
			}

			// --

			//
			$strAssess.=$temp.",";
			$strAssesswpro.=$temp2.",";

			$pln = trim($row["plan"]);
			$pln = str_replace(chr(173), ',', $pln);
			$arrpn = explode("\n",$pln);
			$ln = count($arrpn);
			for($k=0;$k<$ln;$k++){
				$arrpn[$k]=trim($arrpn[$k]);
				if(!empty($arrpn[$k])){

					$arrpn[$k] = filter_var($arrpn[$k], FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH); //remove special chars

					//if(!in_array($arrpn[$k],$arrplans)&&!in_array($arrpn[$k].chr(0xC2).chr(0xAD),$arrplans)){
					if(!in_array_nocase($arrpn[$k],$arrplans)&&!in_array_nocase($arrpn[$k].chr(0xC2).chr(0xAD),$arrplans)){
						$arrplans[]=$arrpn[$k];
					}else if(in_array($arrpn[$k],$arrplans)){
						//--
						//*
						if($high_flg>0 && $k>=$high_flg){
							$key_search = array_search($arrpn[$k], $arrplans);
							if($key_search!==false){
								unset($arrplans[$key_search]);
								$arrplans[]=$arrpn[$k];
							}
						}
						//*/
						//--
					}
				}
			}
			}

			//
			if(count($arrplans)>0){
				$arrplans = array_values($arrplans);
			}

			//Highlight first
			if($high_flg>0){
				for($f=0;$f<$high_flg;$f++){
					if(!empty($arrplans[$f])){
						$arrplans[$f]="~HI@GH#".$arrplans[$f];
					}
				}
			}
		}

		return $arrplans;
	}

	function getOrdersofAsmt($key, $flgfltr="",$arraporderid=array()){
		$arrOrderTypes=array("1"=>"Meds","2"=>"Labs","3"=>"Imaging/Rad","4"=>"Procedure/Sx","5"=>"Information/Instructions");
		$arrRet_meds=array();
		$arrRet=array();

		if($flgfltr=="ALL"){
			$phrse_fltr=" ";
		}else if($flgfltr=="VISIT"){
			$phrse_fltr="AND c1.form_id='".$this->fid."' ";
		}else{
			$phrse_fltr="AND c1.form_id='".$this->fid."' AND c1.plan_num='".$key."' ";
		}

		$sql = "SELECT c3.name, c3.o_type, c3.order_type_id, c3.order_set_option, c1.order_set_associate_id,c1.order_set_id,
					c2.orders_site_text, c2.instruction_information_txt, c2.orders_options,
					c2.dosage, c2.qty, c2.sig, c2.refill, c2.ndccode, c2.snowmed,c3.snowmed	as snowmed_ct,
					c2.order_set_associate_details_id, c2.order_id
				FROM  order_set_associate_chart_notes c1
				LEFT JOIN order_set_associate_chart_notes_details c2 ON c1.order_set_associate_id = c2.order_set_associate_id
				LEFT JOIN order_details c3 ON c3.id = c2.order_id
				WHERE c1.patient_id='".$this->pid."' ".
				$phrse_fltr.
				"AND c1.delete_status='0' AND c2.delete_status='0'
				ORDER BY c3.name, c1.order_set_associate_id
				";
		//echo "<br/>".$sql."<br/>";
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			if($row["name"]!=""){

				//*
				$order_name = $row["name"];
				$order_site = $row["orders_site_text"];
				$instruction = $row["instruction_information_txt"];
				$order_set_option = trim($row["orders_options"]);
				//if(empty($order_set_option)){$order_set_option = $row["order_set_option"];}
				$order_type = $row["o_type"];
				$order_type_id = $row["order_type_id"];
				if(empty($order_type) && !empty($order_type_id)){	$order_type = $arrOrderTypes[$order_type_id];	}

				$snowmed = $row["snowmed"];
				$snowmed_admin = $row["snowmed_ct"];
				$order_id = $row["order_id"];
				$order_set_id = $row["order_set_id"];
				//*/

				//check if Suppli Order
				if(!empty($order_id)){
					if(!empty($order_set_id)){
						$flg_supli_order= (!in_array($order_id,$arraporderid[$order_set_id])) ? 1 : 0;
					}else{
						$flg_supli_order= (recursive_array_search($order_id,$arraporderid)===false) ? 1 : 0;
					}
				}


				if($order_type == "Meds"){
					//CPOE - show full med order
					//order name (site) Dosage Qty Sig Refill NDC code
					$dosage = $qty = $sig = $refill = $ndccode = "";
					if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
					if(!empty($row["dosage"])){  $dosage = " ".$row["dosage"]."";  }
					if(!empty($row["qty"])){  $qty = " ".$row["qty"]."";  }
					if(!empty($row["sig"])){  $sig = " ".$row["sig"]."";  }
					if(!empty($row["refill"])){  $refill = " ".$row["refill"]." refills";  }
					if(!empty($row["ndccode"])){  $ndccode = " ".$row["ndccode"]."";  }

					$strFormatPlan = $order_name."".$order_site."".$dosage."".$qty."".$sig."".$refill."".$ndc_code;

					$strFormatPlan_wo_site_sig = $order_name."".$dosage."".$qty."".$refill."".$ndc_code;

					$arrRet[$order_type][]=array($strFormatPlan, $row["order_set_associate_details_id"],"ORDER","",$order_name, $flg_supli_order, $strFormatPlan_wo_site_sig);

				}else{

					//order name (site)(Instruction) - Option Optionname
					if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
					if(!empty($instruction)){  $instruction = " (".$instruction.")";  }
					if($order_type == "Labs" || $order_type == "Imaging/Rad" || $order_type == "Procedure/Sx"){
						if(!empty($snowmed)){ $snowmed = " (SNOMED CT: ".$snowmed.")";  }
					}else{  $snowmed = ""; }
					if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }
					if($order_type == "Information/Instructions"){
							if(!empty($snowmed_admin)){ $snowmed = " (SNOMED CT: ".$snowmed_admin.")";  }
					}
					$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname."".$snowmed;

					$strFormatPlan_wo_site_sig = $order_name."".$instruction."".$optionname."".$snowmed;

					$arrRet[$order_type][]=array($strFormatPlan, $row["order_set_associate_details_id"],"ORDER","",$order_name, $flg_supli_order, $strFormatPlan_wo_site_sig);
				}
			}
		}

		//merge
		//$arrRet = array_merge($arrRet_meds, $arrRet);

		return $arrRet;
	}

	function isAlreadyAddedOrder($key,$order_det_id,$order_set_id=""){
		if(!empty($order_set_id)){ //check if order exits with same order set id or without order set id.
			$phrseorderset=" AND (c1.order_set_id='".$order_set_id."' || c1.order_set_id='') ";
		}else{
			$phrseorderset=" AND c1.order_set_id='' ";
		}

		$ret = 0;
		$sql = "SELECT count(*) AS num
				FROM  order_set_associate_chart_notes c1
				LEFT JOIN order_set_associate_chart_notes_details c2 ON c1.order_set_associate_id = c2.order_set_associate_id
				LEFT JOIN order_details c3 ON c3.id = c2.order_id
				WHERE c1.patient_id='".$this->pid."' AND c1.form_id='".$this->fid."' AND c1.plan_num='".$key."' AND c1.delete_status='0' AND c2.delete_status='0'
				AND c2.order_id = '".$order_det_id."' ".
				$phrseorderset.
				"ORDER BY c1.order_set_associate_id
				";
		$row = sqlQuery($sql);
		if($row!=false){
			if($row["num"]>0){
				$ret = 1;
			}
		}
		return $ret;

	}

	function getDxICD10Format($q){
		$q1=substr($q,0,-1)."-";
		$q2=substr($q,0,-2)."--";
		$q3=substr($q,0,-3)."-x-";
		return array($q1, $q2, $q3);
	}

	//compare dx array with dx array 9 + 10 : return true is value exists or return false
	function isDxCodeExists($ndx, $hdx, $hdx10){
		$ret = false;
		$ndx=trim($ndx);
		$hdx=trim($hdx);
		$hdx10=trim($hdx10);

		$arrn = array();
		if(!empty($ndx)){ $arrn = (strpos($ndx,",") !== false) ? explode(",", $ndx) : explode(";", $ndx) ; }else{ return true; /*return true when needle is empty*/}

		$arrh = array();
		if(!empty($hdx)){ $arrh = (strpos($hdx,",") !== false) ? explode(",", $hdx) : explode(";", $hdx) ; }

		if(!empty($hdx10)){
			$ah_t = (strpos($hdx10,",") !== false) ? explode(",", $hdx10) : explode(";", $hdx10) ;
			if(count($ah_t)>0){
				foreach($ah_t as $k => $v){
					$v = trim($v);
					if(!empty($v)){
						$arrh[] = $v;
					}
				}
			}
		}

		// if no dx code in AP policy and dx code is set assessment: return true.
		if(count($arrh)<=0){ $ret = true; }

		if(count($arrn)>0){
			foreach($arrn as $key => $val){
				$val = trim($val);
				if(!empty($val)){
					list($v1,$v2,$v3) = $this->getDxICD10Format($val);
					if(in_array($val, $arrh)||in_array($v1, $arrh)||in_array($v2, $arrh)||in_array($v3, $arrh)){
						$ret = true;
					}
				}
			}
		}
		return $ret;
	}

	function isMultiSigs($sig){
		$ret=false;
		$sig=trim($sig);
		$sig=trim($sig,"\r\n");
		if(!empty($sig)){
			$ar_sig = explode("\n",$sig);

			if(count($ar_sig)>1){
				$ret = true;
			}
		}
		return $ret;
	}

	function getOrdersofAsmtAdmin($key,$asses = array(),$dx = array()){
		//include_once(dirname(__FILE__)."/../../admin/order_sets/config_order.php");
		/*
		if(isset($_SESSION["form_id"]) && !empty($_SESSION["form_id"])){
			$elem_formId = $_SESSION["form_id"];
			$finalize_flag = 0;
		}else if(isset($_SESSION["finalize_id"]) && !empty($_SESSION["finalize_id"])){
			$elem_formId = $_SESSION["finalize_id"];
			$finalize_flag = 1;
		}
		*/

		//dos
		if(!empty($this->fid)){
			$oCN = new ChartNote($this->pid,$this->fid);
			$dos = $oCN->getDos(1);
		}

		//
		$asses_complete=trim($asses);

		//Remove Site and Dx Codes.
		//$asTmp_2="";
		$asses = remSiteDxFromAssessment(trim($asses));

		$arrOrderTypes=array("1"=>"Meds","2"=>"Labs","3"=>"Imaging/Rad","4"=>"Procedure/Sx","5"=>"Information/Instructions");
		$arrRet_meds=array();
		$arrRet=array();
		$arr_comb_order_det_id=array();
		$arr_comb_orders=array(); //order ids with order set id

		$oDx = new Dx();
		//icd 10 format
		//if("".$dx!=""){
		//	list($q1, $q2, $q3) = $this->getDxICD10Format($dx);
		//}

			$sql = "SELECT od.*, ctd.dxcode, ctd.dxcode_10
					FROM console_to_do ctd
					JOIN order_details od ON (FIND_IN_SET(od.id, ctd.order_id)>0)
					WHERE (ctd.assessment = '".sqlEscStr($asses)."' OR ctd.assessment = '".sqlEscStr($asses_complete)."') ";
			//if("".$dx!=""){
			//	$sql .=	"	AND (ctd.dxcode = '".$dx."' || ctd.dxcode_10 = '".$dx."' || ctd.dxcode_10 = '".$q1."' || ctd.dxcode_10 = '".$q2."' || LOWER(ctd.dxcode_10) = '".strtolower($q3)."') ";
			//}
			$sql .=	"	AND (providerID = 0 || providerID = '".$_SESSION["authId"]."')
						AND (od.o_type!='' || (od.order_type_id != '' AND od.order_type_id != 0)) ".
						"AND od.delete_status = 0
						ORDER BY name
					";
			//echo "<br>".$sql."<br>";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
					if($row["name"]!="" && $row["name"]!=NULL){

							if(!empty($row["dxcode_10"])){
									list($row["dxcode_10"], $tdxId) = $oDx->refineDx($row["dxcode_10"]);
							}

							//check dx code
							if("".$dx!=""){
								if(!$this->isDxCodeExists($dx, $row["dxcode"],$row["dxcode_10"])){ continue; }
							}


							//check if Dx belongs to dos: if not continue;
							if(!empty($row["dxcode_10"])){
							if(!Dx::isDxCodeBelongsToDos($dos, $row["dxcode_10"])){
								continue;
							}
							}

							//--

							$order_details_id = $row['id'];
							$order_name = trim($row["name"]);
							$order_type = $row["o_type"];
							$order_type_id = $row["order_type_id"];
							if(empty($order_type) && !empty($order_type_id)){	$order_type = $arrOrderTypes[$order_type_id];	}

							$snowmed = $row["snowmed"];
							//$order_set_option = $row["order_set_option"];
							$instruction = $row["instruction"];

							//check duplicate
							if(in_array($order_details_id, $arr_comb_order_det_id)){ continue;  }

							$arr_comb_order_det_id[] = $order_details_id;
							$arr_comb_orders[0][]=$order_details_id;//with no order set

							//filter already added
							if(!empty($key)){
								if($this->isAlreadyAddedOrder($key,$order_details_id)){ continue; }
							}

							//--
							if($order_type == "Meds"){
								//CPOE - show full med order
								//order name (site) Dosage Qty Sig Refill NDC code

								$flgMSig = $this->isMultiSigs($row["sig"]);

								$dosage = $qty = $sig = $refill = $ndccode = "";
								if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
								if(!empty($row["dosage"])){  $dosage = " ".$row["dosage"]."";  }
								if(!empty($row["qty"])){  $qty = " ".$row["qty"]."";  }
								if(!empty($row["sig"])&&!$flgMSig){  $sig = " ".$row["sig"]."";  }
								if(!empty($row["refill"])){  $refill = " ".$row["refill"]." refills";  }
								if(!empty($row["ndccode"])){  $ndccode = " ".$row["ndccode"]."";  }

								$strFormatPlan = $order_name."".$order_site."".$dosage."".$qty."".$sig."".$refill."".$ndc_code;

								$mSig = ($flgMSig) ? $row["sig"] : "";
								$sSig = (!$flgMSig && !empty($row["sig"])) ? $row["sig"] : "";


								//sig_locator --
								$sig_locator=" SIGLOCATION";
								$strFormatPlan_SigPosition = $order_name."".$order_site."".$dosage."".$qty."".$sig_locator."".$refill."".$ndc_code;
								//--


								//$strFormatPlan.$order_details_id
								$arrRet[$order_type][]=array($strFormatPlan, $order_details_id,"", $order_name, $mSig, $sSig, $strFormatPlan_SigPosition );

							}else{

								//order name (site)(Instruction) - Option Optionname
								if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
								if(!empty($instruction)){  $instruction = " (".$instruction.")";  }
								if($order_type == "Labs" || $order_type == "Imaging/Rad" || $order_type == "Procedure/Sx"){
									if(!empty($snowmed)){ $snowmed = " (SNOMED CT: ".$snowmed.")";  }
								}else{  $snowmed = ""; }
								if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }
								if($order_type == "Information/Instructions"){
										if(!empty($snowmed_admin)){ $snowmed = " (SNOMED CT: ".$snowmed_admin.")";  }
								}
								$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname."".$snowmed;

								//$strFormatPlan.$order_details_id
								$arrRet[$order_type][]=array($strFormatPlan, $order_details_id,"", $order_name,"","","");
							}
							//--


							/*
							$sql_sel = "SELECT * FROM order_set_associate_chart_notes oscn
										JOIN order_set_associate_chart_notes_details oscnd ON oscnd.order_set_associate_id = oscn.order_set_associate_id
										WHERE oscn.patient_id = '".$_SESSION["patient"]."'
											AND oscn.form_id = '".$elem_formId."'
											AND oscnd.order_id = '".$row['id']."'
											AND plan_num = '".$key."'
										";
							$res_sel = mysql_query($sql_sel);
							if(mysql_num_rows($res_sel)<=0){

								$sql_in = "INSERT INTO order_set_associate_chart_notes SET
											patient_id='".$_SESSION["patient"]."',
											form_id='".$elem_formId."',
											created_date=now(),
											logged_provider_id='".$_SESSION["authId"]."',
											plan_num='".$key."'
										";
								$order_set_associate_id = sqlInsert($sql_in);

								$resp_person = @join(',',$row['resp_person']);
								$orders_dx_code = @join(',',$row['orders_dx_code']);
								$order_lab_name = @join(',',$row['order_lab_name']);
								$sql_d_in = "INSERT INTO order_set_associate_chart_notes_details SET
												order_set_associate_id='".$order_set_associate_id."',
												order_id='".$row['id']."',
												created_date=NOW(),
												orders_status='0',
												modified_date = CURDATE(),
												modified_operator = '".$_SESSION["authId"]."',
												instruction_information_txt='".$row["instruction"]."',

												dosage = '".$row["dosage"]."',
												qty = '".$row["qty"]."',
												sig  = '".$row["sig"]."',
												refill  = '".$row["refill"]."',
												ndccode = '".$row["ndccode"]."',
												testname = '".$row["testname"]."',
												loinc_code = '".$row["loinc_code"]."',
												cpt_code = '".$row["cpt_code"]."',
												inform = '".$row["inform"]."',
												resp_person = '".$resp_person."',
												orders_dx_code = '".$orders_dx_code."',
												order_lab_name = '".$order_lab_name."',
												snowmed = '".$row["snowmed"]."',
												template_id = '".$row["template_id"]."',
												template_content = '".$row['template_content']."'
										";
								$id_rr = sqlInsert($sql_d_in);
							}
							*/
					}
			}

			$sql = "SELECT os.id AS order_set_id,os.order_id, ctd.dxcode, ctd.dxcode_10
					FROM console_to_do ctd ".
					"JOIN order_sets os ON (FIND_IN_SET(os.id, ctd.order_set_name)>0) ".
					//"LEFT JOIN order_sets os ON os.id = ctd.order_set_name ".
					"WHERE (ctd.assessment = '".sqlEscStr($asses)."' OR ctd.assessment = '".sqlEscStr($asses_complete)."') ";
			//if("".$dx!=""){
			//$sql .=		"AND (ctd.dxcode = '".$dx."' || ctd.dxcode_10 = '".$dx."' || ctd.dxcode_10 = '".$q1."' || ctd.dxcode_10 = '".$q2."' || LOWER(ctd.dxcode_10) = '".strtolower($q3)."' )";
			//}
			$sql .=		"AND (providerID = 0 || providerID = '".$_SESSION["authId"]."')
					";
			//echo "<br>".$sql."<br>";
			$rez = sqlStatement($sql);
			for($i=1;$row=sqlFetchArray($rez);$i++){
					if($row["order_set_id"]!="" && $row["order_set_id"]!=NULL){

							if(!empty($row["dxcode_10"])){
									list($row["dxcode_10"], $tdxId) = $oDx->refineDx($row["dxcode_10"]);
							}

							//check dx code
							if("".$dx!=""){
								if(!$this->isDxCodeExists($dx, $row["dxcode"],$row["dxcode_10"])){ continue; }
							}

							//check if Dx belongs to dos: if not continue;
							if(!empty($row["dxcode_10"])){
							if(!Dx::isDxCodeBelongsToDos($dos, $row["dxcode_10"])){
								continue;
							}
							}
							//--

							$order_set_id = $row["order_set_id"];
							$arrOrderId = explode(",",$row['order_id']);

							//--
							if(count($arrOrderId)>0){
								foreach($arrOrderId as $orderId){

									$sq_od = "SELECT *
											FROM order_details
											WHERE id = '".$orderId."'
											AND ((o_type!='' AND o_type!='Tests') || (order_type_id != '' AND order_type_id != 0) ) AND delete_status='0' ".
											"ORDER BY name ";
									$rez_od = sqlStatement($sq_od);
									for($j=1;$row =sqlFetchArray($rez_od);$j++){
										if($row["name"]!="" && $row["name"]!=NULL){
											$order_details_id = $row['id'];
											$order_name = trim($row["name"]);
											$order_type = $row["o_type"];
											$snowmed = $row["snowmed"];
											///$order_set_option = $row["order_set_option"];
											$instruction = $row["instruction"];
											$order_type_id = $row["order_type_id"];
											if(empty($order_type) && !empty($order_type_id)){	$order_type = $arrOrderTypes[$order_type_id];	}

											//check duplicate
											if(isset($arr_comb_orders[$order_set_id]) && in_array($order_details_id, $arr_comb_orders[$order_set_id])){ continue;  }

											$arr_comb_order_det_id[] = $order_details_id;
											$arr_comb_orders[$order_set_id][]=$order_details_id;//with order set id

											//filter already added
											if($this->isAlreadyAddedOrder($key,$order_details_id,$order_set_id)){ continue; }

											//--
											if($order_type == "Meds"){
												//CPOE - show full med order
												//order name (site) Dosage Qty Sig Refill NDC code
												$dosage = $qty = $sig = $refill = $ndccode = "";

												$flgMSig = $this->isMultiSigs($row["sig"]);

												if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
												if(!empty($row["dosage"])){  $dosage = " ".$row["dosage"]."";  }
												if(!empty($row["qty"])){  $qty = " ".$row["qty"]."";  }
												if(!empty($row["sig"])&&!$flgMSig){  $sig = " ".$row["sig"]."";  }
												if(!empty($row["refill"])){  $refill = " ".$row["refill"]." refills";  }
												if(!empty($row["ndccode"])){  $ndccode = " ".$row["ndccode"]."";  }

												$strFormatPlan = $order_name."".$order_site."".$dosage."".$qty."".$sig."".$refill."".$ndc_code;

												$mSig = ($flgMSig) ? $row["sig"] : "";
												$sSig = (!$flgMSig && !empty($row["sig"])) ? $row["sig"] : "";

												//sig_locator --
												$sig_locator=" SIGLOCATION";
												$strFormatPlan_SigPosition = $order_name."".$order_site."".$dosage."".$qty."".$sig_locator."".$refill."".$ndc_code;
												//--

												//$strFormatPlan.$order_details_id
												$arrRet[$order_type][]=array($strFormatPlan, $order_details_id, $order_set_id, $order_name,$mSig, $sSig, $strFormatPlan_SigPosition);

											}else{

												//order name (site)(Instruction) - Option Optionname
												if(!empty($order_site)){  $order_site = " (".$order_site.")";  }
												if(!empty($instruction)){  $instruction = " (".$instruction.")";  }
												if($order_type == "Labs" || $order_type == "Imaging/Rad" || $order_type == "Procedure/Sx"){
													if(!empty($snowmed)){ $snowmed = " (SNOMED CT: ".$snowmed.")";  }
												}else{  $snowmed = ""; }
												if(!empty($order_set_option)){ $optionname = " - Option ".$order_set_option;  }
												if($order_type == "Information/Instructions"){
														if(!empty($snowmed_admin)){ $snowmed = " (SNOMED CT: ".$snowmed_admin.")";  }
												}
												$strFormatPlan = $order_name."".$order_site."".$instruction."".$optionname."".$snowmed;

												//$strFormatPlan.$order_details_id
												$arrRet[$order_type][]=array($strFormatPlan, $order_details_id, $order_set_id, $order_name,"","","");
											}
											//--
										}

									}

								}
							}
							//--



							/*
							$sql_sel = "SELECT * FROM order_set_associate_chart_notes oscn
										WHERE oscn.patient_id = '".$_SESSION["patient"]."'
											AND oscn.form_id = '".$elem_formId."'
											AND oscn.order_set_id = '".$row['order_set_id']."'
											AND delete_status = 0
											AND plan_num = '".$key."'
										";
							$res_sel = mysql_query($sql_sel);
							if(mysql_num_rows($res_sel)<=0){
								$sql_in = "INSERT INTO order_set_associate_chart_notes SET
											order_set_id='".$row['order_set_id']."',
											patient_id='".$_SESSION["patient"]."',
											form_id='".$elem_formId."',
											created_date=now(),
											logged_provider_id='".$_SESSION["authId"]."',
											plan_num='".$key."'
										";
								$order_set_associate_id = sqlInsert($sql_in);
							}else{
								$row_sel = mysql_fetch_assoc($res_sel);
								$order_set_associate_id = $row_sel['order_set_associate_id'];
							}
								$arrOrderId = explode(",",$row['order_id']);
								foreach($arrOrderId as $orderId){
									$sql_osd_sel = "SELECT * FROM order_set_associate_chart_notes_details
												WHERE order_set_associate_id = '".$order_set_associate_id."'
												AND order_id = '".$orderId."'
												";
									$res_osd_sel = mysql_query($sql_osd_sel);
									if(mysql_num_rows($res_osd_sel)<=0){
										$sq_od = "SELECT *
												FROM order_details
												WHERE id = '".$orderId."'
												AND o_type!=''
												AND order_type_id != ''
												AND order_type_id != 0
												";
										$rez_od = sqlStatement($sq_od);
										for($j=1;$row_od=sqlFetchArray($rez_od);$j++){
											$resp_person = @join(',',$row_od['resp_person']);
											$orders_dx_code = @join(',',$row_od['orders_dx_code']);
											$order_lab_name = @join(',',$row_od['order_lab_name']);
											$sql_d_in = "INSERT INTO order_set_associate_chart_notes_details SET
															order_set_associate_id='".$order_set_associate_id."',
															order_id='".$row_od['id']."',
															created_date=NOW(),
															orders_status='0',
															modified_date = CURDATE(),
															modified_operator = '".$_SESSION["authId"]."',
															instruction_information_txt='".$row_od["instruction"]."',

															dosage = '".$row_od["dosage"]."',
															qty = '".$row_od["qty"]."',
															sig  = '".$row_od["sig"]."',
															refill  = '".$row_od["refill"]."',
															ndccode = '".$row_od["ndccode"]."',
															testname = '".$row_od["testname"]."',
															loinc_code = '".$row_od["loinc_code"]."',
															cpt_code = '".$row_od["cpt_code"]."',
															inform = '".$row_od["inform"]."',
															resp_person = '".$resp_person."',
															orders_dx_code = '".$orders_dx_code."',
															order_lab_name = '".$order_lab_name."',
															snowmed = '".$row_od["snowmed"]."',
															template_id = '".$row_od["template_id"]."',
															template_content = '".$row_od['template_content']."'
													";
											$id_rr = sqlInsert($sql_d_in);
										}
									}
								}
							*/
					}
			}

		//merge
		//$arrRet = array_merge($arrRet_meds, $arrRet);

		ksort($arrRet);

		//$arrRet = array_values($arrRet);

		return array($arrRet, $arr_comb_orders);

	}

	function resetDiabetes($as,$dx){


		$xmlApVals = $this->getDbVal();
		$idAP  = $this->getAPId();

		$ar_dm_as = array("Diabetes Type 1 No retinopathy","DM Type 1 Mild with ME","DM Type 1 Mod with ME","DM Type 1 Severe with ME","DM Type 1 Proliferative with ME","DM Type 1 Mild without ME",
					"DM Type 1 Mod without ME","DM Type 1 Severe without ME","DM Type 1 Proliferative without ME","DM Type 1","DM Type 1 no retinopathy","Diabetes Type 1");

		$flg_1=0;
		if(empty($xmlApVals) && !empty($idAP)){

			$ar = $this->get_pt_ap_db($idAP);
			$arrAp = $ar["data"]["ap"];
			$lenAssess = count($arrAp);
			for($i=0;$i<$lenAssess;$i++){
				$chk = trim($arrAp[$i]["assessment"]);
				$ptapid = trim($arrAp[$i]["pt_ap_id"]);

				if(!empty($chk) && !empty($ptapid)){
					foreach($ar_dm_as as $k => $v){
						$v=strtolower($v);
						$v2 = str_replace("1","2", $v);
						if($v == $chk || strpos($chk, $v)!==false || $v2 == $chk || strpos($chk, $v2)!==false){
							if($flg_1==0){
								$assessment_set="".$as." (".$dx.")";
								$sql = "UPDATE chart_pt_assessment_plans SET assessment='".sqlEscStr($assessment_set)."' WHERE id = '".$ptapid."' ";
								$row = sqlQuery($sql);
								$flg_1=1;
							}
						}
					}
				}
				if($flg_1==1){break;}
			}

		}else{

		$apv = new SimpleXMLElement($xmlApVals);


		foreach ($apv->xpath('//ap') as $ap) {
			if(!empty($ap->assessment)){
				$chk = strtolower($ap->assessment);
				foreach($ar_dm_as as $k => $v){
					$v=strtolower($v);
					$v2 = str_replace("1","2", $v);
					if($v == $chk || strpos($chk, $v)!==false || $v2 == $chk || strpos($chk, $v2)!==false){
						if($flg_1==0){
							$ap->assessment="".$as." (".$dx.")";
							$flg_1=1;
						}
					}
				}
			}
		}

		$xml = $apv->asXML();
		//
		$sql = "UPDATE chart_assessment_plans SET assess_plan='".sqlEscStr($xml)."' WHERE form_id='".$this->fid."' and patient_id='".$this->pid."'
				 ";
		$row = sqlQuery($sql);

		}
	}

	//--
	public function encodeSplChars($str){
		if(!empty($str)){
			$str = $this->clearBadChars($str);
		}
		return $str;
		//return str_replace(array("&","<",">","\\","\"","–","-"),array("&amp;","&lt;","&gt;","&apos;","&quot;","&#45;","&#45;"),$str);
		//return htmlspecialchars($str,ENT_QUOTES);
	}

	//Get str xml for saving
	public function getXml($arr,$uid,$retModi=""){

		$arrAssess=$arr[0];
		$arrPlan=$arr[1];
		$arrResolve=$arr[2];
		$arrNe=$arr[3];
		$arrEye=$arr[4];
		$arrConMed=$arr[5];
		$arrProbListId=$arr[6];


		//--
		// Make new Xml
		// Get Old xml if any
		// GET USER INFO from old xml
		// Create a new xml with new info and user info
		//--

		//Step 1. Get Older xml if form id
		// GET User Info in array
		$arrXml = $this->getVal();

		//echo "<pre>";
		//print_r($arrXml);
		//echo "<br/>";
		//exit();

		//Create DOM
		$dom = new DOMDocument('1.0','utf-8');
		$assess_plan = $dom->appendChild($dom->createElement('assess_plan'));
		$data = $assess_plan->appendChild($dom->createElement('data'));

		//Step 2. Make new data block
		//Loop array values
		$length = (count($arrAssess) >= count($arrPlan)) ? count($arrAssess) : count($arrPlan);
		//$strBlockData = "";
		for($i=0;$i<$length;$i++){
			if(trim($arrAssess[$i]) || trim($arrPlan[$i])) {
				$ap = $data->appendChild($dom->createElement('ap'));

				$asCdata = $dom->createCDATASection($this->encodeSplChars($arrAssess[$i]));
				$assessment = $dom->createElement('assessment');
				$assessment->appendChild($asCdata);
				$ap->appendChild($assessment);

				$apCdata = $dom->createCDATASection($this->encodeSplChars($arrPlan[$i]));
				$plan = $dom->createElement('plan');
				$plan->appendChild($apCdata);
				$ap->appendChild($plan);

				$ne = $ap->appendChild($dom->createElement('ne',$arrNe[$i]));
				$resolve = $ap->appendChild($dom->createElement('resolve',$arrResolve[$i]));
				$eye = $ap->appendChild($dom->createElement('eye',$arrEye[$i]));
				$conmed = $ap->appendChild($dom->createElement('conmed',$arrConMed[$i]));
				$pbid = $ap->appendChild($dom->createElement('pbid',$arrProbListId[$i]));
			}
		}

		//Test--

		if($retModi==1){
			//String modifications
			$strModi="";
			$strUsrinfo="";
			$strUsrinfo="".date("m-d-y H:i")." ";
			if(!empty($uid)){

				if(!class_exists("User")){
					require(dirname(__FILE__)."/User.php");
				}

				$ouser = new User($uid);
				$strUsrinfo.="".$ouser->getName(1);
			}

			if(count($arrXml["data"]["ap"]) > 0){

				//Get Last userId
				if(count($arrXml["updates"]["update"]) > 0){
					$prvUid = $arrXml["updates"]["update"][0]["usrId"];
				}
				if(!empty($prvUid) && $prvUid!=$uid){

					foreach($arrXml["data"]["ap"] as $j => $val){
						$flgModi=0;
						$key = array_search($val["assessment"], $arrAssess);
						if($key!==false){

							if($val["plan"]!=$arrPlan[$key] || $val["eye"]!=$arrEye[$key] ){
								$flgModi=1;
							}

						}else{
							$flgModi=1;
						}

						//
						if($flgModi==1){
							$strModi.="<div>
									$strUsrinfo<br/>
									<div class=\"md_s\">".($j+1).".</div>
									<div class=\"md_as\">".nl2br($val["assessment"])."</div>
									<div class=\"md_i\">".$val["eye"]."</div>
									<div class=\"md_pln\">".nl2br($val["plan"])."</div>
									</div>
									";
						}
					}
				}
			}
		}//ifretModii=1

		//echo "$prvUid != $uid";exit();
		//echo "OUTPUT - ".$strModi." -- ";
		//Test --

		//exit("DONE");

		//Add new user update block
		if(!empty($uid)){
			$updates = $assess_plan->appendChild($dom->createElement('updates'));

			$update = $updates->appendChild($dom->createElement('update'));
			$dt_time = $update->appendChild($dom->createElement('dt_time',time()));
			$usrId = $update->appendChild($dom->createElement('usrId',$uid));

			//Get Older Update block Block
			//$strBlocksUpdate_old = "";
			if(count($arrXml["updates"]["update"]) > 0){
				//$strBlocksUpdate_old = "";
				foreach($arrXml["updates"]["update"] as $val){
					$update = $updates->appendChild($dom->createElement('update'));
					$dt_time = $update->appendChild($dom->createElement('dt_time',$val["dt_time"]));
					$usrId = $update->appendChild($dom->createElement('usrId',$val["usrId"]));

				}
			}
		}

		if($retModi==1){
			return array($dom->saveXML(), $strModi);
		}else{
			//return xml string
			return $dom->saveXML();
		}
	}

	function get_pt_ap_db($id_ch_ap=0, $yes_id=1){
		$arrRet = array();

		$str_wh="";
		if(!empty($id_ch_ap)){
			$str_wh=" c1.id='".$id_ch_ap."' ";
		}else{
			$str_wh=" c1.form_id='".$this->fid."' ";
		}

		$sql= "SELECT c2.* FROM chart_assessment_plans c1 ".
				"LEFT JOIN chart_pt_assessment_plans c2 ON c2.id_chart_ap = c1.id ".
				"WHERE c1.patient_id = '".$this->pid."' ".
				"AND ".$str_wh.
				"AND c2.delete_by = '0' ORDER BY c2.diag_order, c2.id ";
		$rez = sqlStatement($sql);
		for($i=1, $c=0; $row = sqlFetchArray($rez); $i++, $c++){
			$strXml = $res["assess_plan"];
			$not_examin = $row["not_examin"];
			$resolve = $row["resolve"];
			$assessment = $this->clearBadChars($row["assessment"],2);
			$plan = $this->clearBadChars($row["plan"],2);
			$eye = $row["eye"];
			$pbid = $row["id_pt_problem_list"];
			$conmed = 0;
			$pt_ap_id=$row["id"];
			$dxcodeId=$row["dxcode"];

			$arrRet["data"]["ap"][$c]["assessment"]=$assessment;
			$arrRet["data"]["ap"][$c]["plan"]=$plan;
			$arrRet["data"]["ap"][$c]["ne"]=$not_examin;
			$arrRet["data"]["ap"][$c]["resolve"]=$resolve;
			$arrRet["data"]["ap"][$c]["eye"]=$eye;
			$arrRet["data"]["ap"][$c]["conmed"]=$conmed;
			$arrRet["data"]["ap"][$c]["pbid"]=$pbid;
			$arrRet["data"]["ap"][$c]["pt_ap_id"]=(empty($yes_id)) ? 0 : $pt_ap_id;
			$arrRet["data"]["ap"][$c]["dxcodeId"]=$dxcodeId;
		}
		return $arrRet;
	}

	function getVal_Str($strXml){
		//Ret Array
		$arrRet = array();

		//Check empty string
		if(empty($strXml)){
			return $arrRet;
		}

		//Clean Bad Chars
		$strXml = $this->clearBadChars($strXml,1);

		//4 Errors
		libxml_use_internal_errors(true);

		//Make DOM xml
		$dom = new DOMDocument;

		//Ignore White space in xml
		$dom->preserveWhiteSpace = false;

		//Load xml string
		$flgRes = $dom->loadXML($strXml);

		//Check for bad xml
		if($flgRes == false){
			$errors = libxml_get_errors();
			if(!empty($errors))
			{
				$lines = explode("r", $strXml);
				$line = $lines[($error->line)-1];
				$error = $errors[ 0 ];
				if ($error->level < 3)
				{

				}else{
					$message = $error->message . ' at line ' . $error->line . ': ' . htmlentities($line);

					echo "<font color=\"Red\">Error:".$message."</font> ".
						 "<xmp>".str_replace($this->xhdr,"",$strXml)."</xmp>";
					//exit;
					return $arrRet;
				}
			}
		}

		//root element
		$root = $dom->documentElement;

		//Data
		$oData = $dom->getElementsByTagname("data")->item(0);
		//object ap under data for individual assessments

		//Array
		$arrTND = array("assessment", "plan", "ne", "resolve","eye","conmed","pbid");

		//Loop
		$c=0;
		$oData_cns = $oData->childNodes;

		if($oData_cns->length > 0){
			foreach($oData_cns as $ap){
				//Check node 4 element
				if($ap->nodeType == 1){
					//Loop for data values
					foreach($arrTND as $key => $nmd){
						//$nmd
						$oNmd = $ap->getElementsByTagname($nmd)->item(0);
						$vNmd = $oNmd->firstChild->nodeValue;
						$arrRet["data"]["ap"][$c][$nmd]=$vNmd;
						//$nmd
					}
					$c++;
				}
			}
		}
		//Updates
		$oUpdates = $dom->getElementsByTagname("updates")->item(0);
		//Get Individual update tags

		//Array
		$arrTNU = array("dt_time", "usrId");

		//Loop
		$c=0;
		$oUp_cns = $oUpdates->childNodes;

		if($oUp_cns->length > 0){
			foreach($oUp_cns as $update){
				//Check if Node is element
				if($update->nodeType == 1){
					//Loop for values
					foreach($arrTNU as $key => $nmu){
						//$nmu
						$oNmu = $update->getElementsByTagname($nmu)->item(0);
						$vNmu = $oNmu->firstChild->nodeValue;
						$arrRet["updates"]["update"][$c][$nmu]=$vNmu;
						//$nmu
					}
					$c++;
				}
			}
		}

		//Return Array
		return($arrRet);
	}

	//Get Xml values in array for use
	function getVal(){

		//Get xml string from db
		$strXml = $this->getDbVal();
		if(!empty($strXml)){
		$arrRet = $this->getVal_Str($strXml);
		}else{
		$arrRet = $this->get_pt_ap_db();
		}

		//Return Array
		return($arrRet);
	}

	//Get xml values from database
	public function getDbVal(){

		$sql= "SELECT assess_plan FROM chart_assessment_plans ".
				"WHERE form_id='".$this->fid."'".
				"AND patient_id = '".$this->pid."'";
		$res = sqlQuery($sql); //$this->db->Execute($sql) or die("Error in Query: ".$this->db->errorMsg());
		if($res !== false){
			$strXml = $res["assess_plan"];
		}

		return !empty($strXml) ? $strXml : "";
	}

	public function clearBadChars($str,$flg=0){

		global $phpServerIP;

		$find_ptrn = '/\x10|\x92|\x99|\x94|\0x93|0x96|0x97|\x86|\xAD|\xC2|\x9D|\xB6|\xC3|\x82|\xA2|\xA5|\xA8|\xE2|\xAC|\x84|\x83|\x80|\xAF|\x9A|\xB7|\x9C|\xBE|\xA9|\x94|\xB9|\xBD|\x0|\xA0|\0xA6|\0x20|\0x69|\0x66/';
		if($phpServerIP == "neecimwapp.tuftsmedicalcenter.org"){
			$find_ptrn = '/\x10|\x0|\xA0/'; //Tufts only : $phpServerIP = "neecimwapp.tuftsmedicalcenter.org";
		}

		if($flg == 1){

			$indx = strpos($str,"?>");
			if($indx !== false){
				$str = substr($str,$indx+2);
			}
			$str = preg_replace('/\xB0/', '&#176;', $str);
			$str = preg_replace($find_ptrn, '', $str); //remove no printable character DLE
			$str = $this->xhdr.$str;
		}else if($flg == 2){
			$str = str_replace("<", "&lt;", $str);
			$str = filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES | FILTER_FLAG_STRIP_HIGH);
			$str = str_replace( "&lt;", "<", $str);
			$ar_sp = array('/\xE2\x84\xA2/', '/\xE2\x82\xAC/', '/\xC3\xA2/', '/\xC3\x82/');
			$ar_rp = array("");
			$str = preg_replace($ar_sp, $ar_rp, $str);
		}else{
			$str = preg_replace('/\xB0/', '&#176;', $str);
			$str = preg_replace($find_ptrn, '', $str); //[\x10-\x1F\x80-\xFF]  //remove no printable character DLE
		}


		return $str;
	}

	function compare2Asesment($as1,$as2){

		$arr=array();
		//Temporal arteritis - OU (446.5), POAG - OU, Transient visual loss (368.12)
		//get str before site
		//1
		$as_nosite_1 = $as1;
		$ind = strpos($as1,"- O");
		if($ind!==false){
			$as_nosite_1 =  trim(substr($as1,0,$ind));
		}else{
			$ind = strpos($as1,"(");
			if($ind!==false){
				$as_nosite_1 =  trim(substr($as1,0,$ind));
			}
		}
		//2
		$as_nosite_2=$as2;
		$ind = strpos($as2,"- O");
		if($ind!==false){
			$as_nosite_2 =  trim(substr($as2,0,$ind));
		}else{
			$ind = strpos($as2,"(");
			if($ind!==false){
				$as_nosite_2 =  trim(substr($as2,0,$ind));
			}
		}

		if(strcasecmp($as_nosite_1,$as_nosite_2)==0){
			//true
			$arr["res"]=true;
			//New String --
			if(strpos($as2,"OU")!==false||
				(strpos($as1,"OD")!==false&&strpos($as2,"OS")!==false)||
				(strpos($as1,"OS")!==false&&strpos($as2,"OD")!==false)){
				$arr["newAsmt"] = preg_replace('/\bO(U|D|S)\b/', "OU",$as1);
			}
			//New String --
		}else{
			//false
			$arr["res"]=false;
		}

		return $arr;
	}

	function mergeApArr($arr1,$arr2){
		$ln = count($arr2["A"]);
		for($i=0;$i<$ln;$i++){
			$tmpAs = $this->clearBadChars($arr2["A"][$i]);
			$flg=true;
			$ln2 = count($arr1["assess"]);
			for($j=0;$j<$ln2;$j++){
				$arrChk = $this->compare2Asesment($arr1["assess"][$j],$tmpAs);
				if($arrChk["res"]==true){
					if(isset($arrChk["newAsmt"])&&!empty($arrChk["newAsmt"])){
						$arr1["assess"][$j]=$arrChk["newAsmt"];
					}
					$flg=false;
					break;
				}
			}

			if($flg==true){
				$arr1["assess"][] = $arr2["A"][$i];
				$arr1["plan"][] = $arr2["P"][$i];
				$arr1["resolve"][] = $arr2["R"][$i];
				$arr1["ne"][] = $arr2["N"][$i];
				$arr1["eye"][] = $arr2["EYE"][$i];
				$arr1["pbid"][] = $arr2["PBID"][$i];
				$arr1["dxid"][] = $arr2["DXID"][$i];
			}
		}
		return $arr1;
	}

	function getPrevPlans($dos){

		$sql = "SELECT c2.assess_plan, c2.id FROM chart_master_table c1
				INNER JOIN chart_assessment_plans c2 ON c2.form_id = c1.id
				WHERE c1.patient_id = '".$this->pid."' AND c1.id < '".$this->fid."' AND c1.date_of_service <= '".$dos."'
				AND c1.delete_status='0' AND c1.purge_status='0'
				ORDER BY c1.date_of_service DESC, c1.id DESC
				LIMIT 0,1 ";
		$res = sqlQuery($sql);
		if($res !== false){
			$strXml = $res["assess_plan"];
			$id_chart_ap_id = $res["id"];
		}

		$arrRet = array();
		if(!empty($strXml)){
			$arrRet =  $this->getVal_Str($strXml);
		}else if(!empty($id_chart_ap_id)){
			$arrRet = $this->get_pt_ap_db($id_chart_ap_id);
		}

		return $arrRet;
	}

	//
	function valuesNewRecordsAssess($sel=" * ",$LF="0",$flgmemo=1)
	{
		global $cryfwd_form_id;

		//DOS BASED carry forward
		$dt ="";
		if(!empty($cryfwd_form_id)){
			$dt = " AND (chart_master_table.id =  '".$cryfwd_form_id."' ) ";
		}

		$strmemo = ($flgmemo==1) ? "AND chart_master_table.memo != '1' " : "" ;
		$LF = ($LF == "1") ? "AND chart_master_table.finalize = '1' " : "";
		$qry = "SELECT ".$sel." FROM chart_master_table ".
			  "INNER JOIN chart_assessment_plans ON chart_master_table.id = chart_assessment_plans.form_id ".
			  "WHERE chart_master_table.patient_id = '".$this->pid."' AND chart_master_table.delete_status='0' AND chart_master_table.purge_status='0' ".
			  "AND chart_master_table.record_validity = '1' ".
			  $strmemo. //do not get memo assessments and plans: 08-04-2014
			  $LF.
			  $dt.
			  "ORDER BY chart_master_table.date_of_service DESC, chart_master_table.create_dt DESC, chart_master_table.id DESC LIMIT 0,1 ";
		$row = sqlQuery($qry);
		return $row;
	}

	//
	function resetDxCodesOfPrevAssess($vAssess, $curr_icd_code,  $prev_icd_code){
		//get code from assessment
		$vAssess_org = $vAssess;
		$lastChar = substr(trim($vAssess), -1);
		$dxcode = '';$dx_class = '';
		if($lastChar == ")"){
			preg_match("/\([^\(]*$/",trim($vAssess),$arr_match);
			$ptrn22="/[0-9]+/";
			if(preg_match($ptrn22, $arr_match[0])){ //check alphanumeric dx code, if not do not remove
				$dxcode = preg_replace("/^\(|\)$/",'',$arr_match[0]);
				$arrAssSplit = preg_split("/\([^\(]*$/",$vAssess);
				$vAssess = $arrAssSplit[0];
				/*
				$laterlity = substr(trim($dxcode),-1);
				if($dxcode != "" && $laterlity == '-'){
					$dx_class = 'mandatory';
				}
				*/
			}
		}

		if($dxcode!=''){
			$dxcode_new="";
			//change code
			if($prev_icd_code==9 && $curr_icd_code==1){ //9 2 10
				if(!empty($dxcode)){
					//Dx
					$odx = new Dx();
					$dxcode_new = $odx->convertICDDxCode($dxcode, $prev_icd_code, $curr_icd_code);
				}
			}else if(($prev_icd_code==1||$prev_icd_code==0) && $curr_icd_code==1){
				if(!empty($dxcode)){
					$odx = new Dx();
					$dxcode_new = $odx->get_dx_desc($dxcode, "icd10"); //check if code exists
					if(!empty($dxcode_new)){
						$dxcode_new = $dxcode;//set complete code
					}else{
						$dxcode_new = $odx->convertICDDxCode($dxcode, "9", $curr_icd_code);  //check by converting
					}
				}
			}else{
				$dxcode_new="";
			}
			//reset new code in assessment
			if(!empty($dxcode_new)){
				$vAssess_org = str_replace($dxcode,$dxcode_new,$vAssess_org);
			}else{
				$vAssess_org = $vAssess;
			}
		}

		return $vAssess_org;
	}

	//Empty alll Dx code of status 1 if Dos is equal or more that 1 oct , 2016
	function resetDxCodesOfPrevAssess_dibetese($vAssess){
		global $dos_ymd;
		//get code from assessment
		$vAssess_org = $vAssess;
		$lastChar = substr(trim($vAssess), -1);
		$dxcode = '';$dx_class = '';
		if($lastChar == ")"){
			preg_match("/\([^\(]*$/",trim($vAssess),$arr_match);
			$ptrn22="/[0-9]+/";
			if(preg_match($ptrn22, $arr_match[0])){ //check alphanumeric dx code, if not do not remove
				$dxcode = preg_replace("/^\(|\)$/",'',$arr_match[0]);
				$arrAssSplit = preg_split("/\([^\(]*$/",$vAssess);
				$vAssess = $arrAssSplit[0];
			}
		}

		if($dxcode!=''){
			$dxcode_new="";

			$ar_dxCd = explode(",", $dxcode);
			if(count($ar_dxCd)>0){
				foreach($ar_dxCd as $k => $v){
					$v=trim($v);
					if(!empty($v)){
						if(Dx::isDxCodeBelongsToDos($dos_ymd, $v)){
							if(!empty($dxcode_new)){ $dxcode_new.=", ";  }
							$dxcode_new.=$v;
						}
					}
				}
			}

			//
			if(!empty($dxcode_new)){ $dxcode_new="(".$dxcode_new.")";  }
			$vAssess_org = str_replace("(".$dxcode.")", $dxcode_new,$vAssess_org);

			/*
			//old DM code will not move to new chart after oct 1, 2016
			$arr_old_db_dx = array("E10.321","E10.329","E10.331","E10.339","E10.341","E10.349","E10.351","E10.359",
							"E11.321","E11.329","E11.331","E11.339","E11.341","E11.349","E11.351","E11.359");
			if(in_array($dxcode,$arr_old_db_dx)){
				if(isDosAfterOct16()){
					$vAssess_org = str_replace("(".$dxcode.")", $dxcode_new,$vAssess_org);
				}
			}else

			}
			*/
			//--
		}

		return $vAssess_org;
	}

	//
	function ap_resetOrderNum($oldNum,$newNum){
		$sql = "SELECT * FROM `order_set_associate_chart_notes` WHERE plan_num='".$oldNum."' AND form_id='".$this->fid."' AND patient_id = '".$this->pid."'";
		$row = sqlQuery($sql);
		if($row != false){
			$sql = "UPDATE order_set_associate_chart_notes SET plan_num='".$newNum."' WHERE plan_num='".$oldNum."' AND form_id='".$this->fid."' AND patient_id = '".$this->pid."' ";
			$row = sqlQuery($sql);
		}
	}

	function chk2remdx($str){
		$str = trim($str);

		//get str B4 semi colon
		$strIndx = strpos($str,";");
		if( $strIndx !== false ){
			$str = substr($str,0, $strIndx);
		}

		$strIndx = strpos($str,"(");

		if( $strIndx !== false ){

			$endIndx = strpos($str, ")", $strIndx);
			//$strRem = substr($str,$strIndx,($endIndx-$strIndx));
			$strRem = substr($str,$strIndx);
			$str = str_replace( $strRem, "", $str );
		}
		return trim($str);
	}

	function getDxCodeFromAssess($str){
		if(!empty($str)){
		$lastChar = substr(trim($str), -1);
		$dxcode = '';$dx_class = '';
		if($lastChar == ")"){
			preg_match("/\([^\(]*$/",trim($str),$arr_match);
			$ptrn22="/[0-9]+/";
			if(preg_match($ptrn22, $arr_match[0])){ //check alphanumeric dx code, if not do not remove
				$dxcode = preg_replace("/^\(|\)$/",'',$arr_match[0]);
				$arrAssSplit = preg_split("/\([^\(]*$/",$str);
				$str = $arrAssSplit[0];
			}
		}

		if(!empty($dxcode)){//remove special characters from dx codes
			$dxcode = filter_var($dxcode, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		}


		}
		return array($str, $dxcode);
	}

	function add_literality_in_asmt($as, $ey){
		$append_literality=""; $append_literality_2="";
		if($ey=="OU"){
			$append_literality= "Both Eyes";
			$append_literality_2= "Both";
		}else if($ey=="OD"){
			$append_literality= "Right Eye";
			$append_literality_2= "Right";
		}else if($ey=="OS"){
			$append_literality= "Left Eye";
			$append_literality_2= "Left";
		}

		//check lids::  both eyes populating in fields that should only have lids indicated
		if(stripos($as, "upper lid")===false && stripos($as, "lower lid")===false && stripos($as, " EYELID")===false){
		//Agreed, they will be added with the “;”  after the assessment.
		if(!empty($append_literality) && !empty($as) && stripos($as, $append_literality_2)===false){

			//if Both,  check if right and left both exists, then do not add
			if($append_literality_2 == "Both"){
				if(stripos($as, "Right")!==false && stripos($as, "Left")!==false){
					return $as;
				}
			}
			//--

			if(stripos($as, ";")===false){ $as .= ";";  }
			$as .= " ".$append_literality;
		}
		//--
		}
		return $as;
	}

	function show_site_dropdown($dx){
		$ret="0";
		$dx = trim($dx);
		if(!empty($dx)){
			$odx = new Dx();
			if(strpos($dx,"-")!==false){
				$ret="0";
			}else{
				$ardx = explode(",",$dx);
				if(count($ardx)>0){
					foreach($ardx as $k => $v){
						$v = trim($v);
						if(!empty($v)){
							$tmp= $odx->is_incomplete_dx($v);
							if(empty($tmp)){ $ret="1"; break; }
						}
					}
				}
			}
		}
		return $ret;
	}

	function get_prv_ases_indx_of_plan($j, $asmt, $arfind){
		list($asmt , $tdx) = $this->getDxCodeFromAssess($asmt);
		$asmt=trim($asmt);
		$tmp = explode(";", $asmt);
		$asmt_w = trim($tmp[0]);

		$ret=0;
		$varAssessment = "elem_assessment".($j);
		$tmpasmt = trim($arfind[$varAssessment]);
		$tmp = explode(";", $tmpasmt);
		$tmpasmt_w = trim($tmp[0]);

		if($asmt_w==$tmpasmt_w){
			$ret=$j;
		}else{
			$j=1;
			while(true){
				$varAssessment = "elem_assessment".($j);

				if(!isset($arfind[$varAssessment]) || $j>50){ break; }

				$tmpasmt = trim($arfind[$varAssessment]);
				$tmp = explode(";", $tmpasmt);
				$tmpasmt_w = trim($tmp[0]);

				if($asmt_w==$tmpasmt_w){
					$ret=$j;
					break;
				}

				$j++;
			}

		}

		return $ret;
	}

	//
	function getFormInfo($finalize_flag=0, $enc_icd10=0){
		// Chart Assessment and plan -----
		global $elem_ccHx, $elem_ccId,$dos_ymd;

		$arrRet=array();

		//$strPrvPlanPop="";//

		//Follow Up ------------
		//$arrFuVals = array();
		//$lenFu = 0;
		//Follow Up ------------

		$sql = "SELECT * FROM chart_assessment_plans WHERE patient_id = '".$this->pid."' AND form_id = '".$this->fid."' ";
		$row = sqlQuery($sql);

		if(($row == false) && ($finalize_flag == 0)){
			// New
			$elem_assessId = 0;
			$elem_editModeAssess = "0";
			//New Records
			$row = $this->valuesNewRecordsAssess(" * ", "0", 1);
			$new_row = true;
		}else{
			// Update
			$elem_assessId = $row["id"];
			$elem_editModeAssess = "1";
			//Default
			if(isset($_POST["defaultValsAssess"]) && ($_POST["defaultValsAssess"] == 1)){
				//New Records
				$row = $this->valuesNewRecordsAssess();
			}
		}

		if($row != false){

			/*$no_change_1 = $row["no_change_1"];
			$no_change_2 = $row["no_change_2"];
			$no_change_3 = $row["no_change_3"];
			$no_change_4 = $row["no_change_4"];
			$no_change_5 = $row["no_change_5"];
			$elem_resolve1 = $row["plan_resolve_1"];
			$elem_assessment1 = stripslashes($row["assessment_1"]);
			$elem_resolve2 = $row["plan_resolve_2"];
			$elem_assessment2 = stripslashes($row["assessment_2"]);
			$elem_resolve3 = $row["plan_resolve_3"];
			$elem_assessment3 = stripslashes($row["assessment_3"]);
			$elem_resolve4 = $row["plan_resolve_4"];
			$elem_assessment4 = $elem_assessmentAll = stripslashes($row["assessment_4"]);
			//$elem_resolve5 = $row[""];
			//$elem_assessment5 = $row[""];
			$elem_plan1 = stripslashes($row["plan_notes_1"]);
			$elem_plan2 = stripslashes($row["plan_notes_2"]);
			$elem_plan3 = stripslashes($row["plan_notes_3"]);
			$elem_plan4 = $elem_planAll = stripslashes($row["plan_notes_4"]);
			//$elem_plan5 = $row[""];
			*/
			$elem_followUpNumber = $row["follow_up_numeric_value"];
			//$elem_followUpNumber1 = $row["followNumber"];
			$elem_followUp = $row["follow_up"];
			$elem_retina = $row["retina"];
			$elem_neuroOptha = $row["neuro_ophth"];
			$elem_doctorName = $row["doctor_name"];
			$elem_idPrecautions = $row["id_precation"];
			$elem_rdPrecautions = $row["rd_precation"];
			$elem_lidScrubs = $row["lid_scrubs_oint"];
			$elem_patientUnderstands = $row["patient_understands"];
			$elem_patientUnderstandsSelect = $row["patient_understands_select"];
			$elem_notes = stripslashes($row["plan_notes"]);
			$elem_pt_goal_str = html_entity_decode(addslashes($row['pt_goal']));
			$elem_pt_health_concern = html_entity_decode(addslashes($row['health_concern']));
			/*
			$strpixls = $elem_signCoords = $row["sign_coords"];
			$elem_physicianId= ($elem_editModeAssess != "0") ? $row["doctorId"] : "" ;
			*/
			//$elem_physicianName= showDoctorName($elem_physicianId);
			$elem_continue_meds = $row["continue_meds"];
			$elem_followUpVistType  = $row["followUpVistType"];
			$elem_scribedBy = $row["scribedBy"];
			$elem_follow_up = $row["followup"];
			$commentsForPatient = stripslashes($row["commentsForPatient"]);
			$strXml = stripslashes($row["assess_plan"]);
			/*
			//START
			$strpixlsCosigner = $elem_signCoords = $row["sign_coordsCosigner"];
			$elem_cosignerId= ($elem_editModeAssess != "0") ? $row["cosigner_id"] : "" ;
			//END
			$elem_sign_path = ($elem_editModeAssess != "0") ? $row["sign_path"] : "" ;
			$elem_cosign_path = ($elem_editModeAssess != "0") ? $row["cosign_path"] : "";
			*/
			$elem_resiHxReviewd=$row["resiHxReviewd"];
			$elem_rxhandwritten=$row["rxhandwritten"];
			$elem_labhandwritten=$row["labhandwritten"];
			$elem_radhandwritten=$row["imagehandwritten"];
			$elem_modi_note_Asses=$row["modi_note_Asses"];
			$modi_note_AssesArr=$row["modi_note_AssesArr"];
			$elem_consult_reason=$row["consult_reason"];
			$elem_sur_ocu_hx=$row["surgical_ocular_hx"];
			$elem_sur_sent_4_dos=wv_formatDate($row["surgical_ocular_hx_sent_dos"]);
			$el_soc = $row["vst_soc"];
			$el_soc_commnts = $row["soc_desc"];

			$elem_refer_to_id=$row["refer_to_id"];
			$elem_refer_to=$row["refer_to"];
			$refer_code=$row["refer_to_code"];
			$elem_transition_reason=$row["transition_reason"];
			$elem_transition_notes=$row["transition_notes"];
			$elem_doctorName_id=$row["doctorName_id"];


			/*
			// Set 4 assessment and plan ---
			list($arrAssessTmp,$arrPlanTmp,$arrResolveTmp,$arrNeTmp) = cleanAssessPlan($elem_assessment4,$elem_plan4);
			//Curr
			$lenAssess = 3;
			$lenAdd = 0;
			$lenAdd = count($arrAssessTmp);
			$lenAssess += $lenAdd;
			for($j=4,$i=0;$i<$lenAdd;$i++,$j++){
				$elem_assessment = "elem_assessment".$j;
				$$elem_assessment = $arrAssessTmp[$i];
				$elem_plan = "elem_plan".$j;
				$$elem_plan = $arrPlanTmp[$i];
				$elem_resolve = "elem_resolve".$j;
				$$elem_resolve = $arrResolveTmp[$i];
				$no_change_Assess = "no_change_".$j;
				$$no_change_Assess = $arrNeTmp[$i];
			}
			// Set 4 assessment and plan ---
			*/

			//Set Assess Plan Resolve NE Value FROM xml ---
			$arrApVals = !empty($strXml) ? $this->getVal_Str($strXml) : $this->get_pt_ap_db($row["id"], $elem_editModeAssess);
			$arrAp = $arrApVals["data"]["ap"];
			$lenAssess = is_array($arrAp) ? count($arrAp) : 0 ;
			for($i=0;$i<$lenAssess;$i++){
				$j=$i+1;
				$elem_assessment = "elem_assessment".$j;
				$elem_assessment_full = "elem_assessment_full".($j);
				$arrRet[$elem_assessment] = $arrAp[$i]["assessment"];
				$arrRet[$elem_assessment_full] = $arrAp[$i]["assessment"];
				$elem_plan = "elem_plan".$j;
				$arrRet[$elem_plan] = $arrAp[$i]["plan"];
				$elem_resolve = "elem_resolve".$j;
				$arrRet[$elem_resolve] = ($arrAp[$i]["resolve"] == "1") ? "checked=\"checked\"" : "" ;
				$no_change_Assess = "no_change_".$j;
				$arrRet[$no_change_Assess] = ($arrAp[$i]["ne"] == "1") ? "checked=\"checked\"" : "" ;
				$elem_apConMeds = "elem_apConMeds".$j; //Continue meds
				$arrRet[$elem_apConMeds] = $arrAp[$i]["conmed"];
				$elem_problist_id_assess = "elem_problist_id_assess".$j;
				$arrRet[$elem_problist_id_assess] = $arrAp[$i]["pbid"];
				$el_pt_ap_id = "el_pt_ap_id".$j;
				$arrRet[$el_pt_ap_id] = $arrAp[$i]["pt_ap_id"];

				if($arrAp[$i]["eye"]=="OU"){
					$elem_apOu = "elem_apOu".$j;
					$arrRet[$elem_apOu] = " checked=\"checked\" ";
				}elseif($arrAp[$i]["eye"]=="OD"){
					$elem_apOd = "elem_apOd".$j;
					$arrRet[$elem_apOd] = "checked=\"checked\"";
				}elseif($arrAp[$i]["eye"]=="OS"){
					$elem_apOs = "elem_apOs".$j;
					$arrRet[$elem_apOs] = "checked=\"checked\"";
				}

				//--
				$elem_assessment_dxcode = "elem_assessment_dxcode".$j;
				list($arrRet[$elem_assessment] , $arrRet[$elem_assessment_dxcode]) = $this->getDxCodeFromAssess($arrRet[$elem_assessment]);

				//Agreed, they will be added with the “;”  after the assessment.
				$arrRet[$elem_assessment] = $this->add_literality_in_asmt($arrRet[$elem_assessment], $arrAp[$i]["eye"]);
				//-----

				//check incomplete dx
				$elem_incomplete_dxcode = "elem_incomplete_dxcode".$j;
				$arrRet[$elem_incomplete_dxcode] = $this->show_site_dropdown($arrRet[$elem_assessment_dxcode]);

				// dx codes ids --
				$elem_asmt_dxid = "elem_asmt_dxid".$j;
				$arrRet[$elem_asmt_dxid] = $arrAp[$i]["dxcodeId"];

				//--
			}

			//--- Set Assess Plan Resolve NE Value FROM xml

			/// Empty Plan and Signature
			if($elem_editModeAssess == "0"){

				//get form id of previous records
				$prev_form_id_ap = $row["form_id"];
				$ocn =new ChartNote($this->pid, $prev_form_id_ap);
				$prev_icd_code = $ocn->getChartICD10Code();


				//Empty AP elements---
				//$elem_physicianId = $elem_physicianName = $elem_cosignerId = $elem_cosignerName = "";
				//$strpixls = $strpixlsCosigner = $elem_signCoords = "";
				//$strpixls = $strpixlsCosigner = $elem_signCoords = "";
				$elem_planAll = "";
				$elem_followUpNumber = $elem_followUp = $elem_retina = $elem_neuroOptha = $elem_doctorName = "";
				$elem_idPrecautions = $elem_rdPrecautions = $elem_lidScrubs = $elem_patientUnderstands = $elem_continue_meds = $elem_patientUnderstandsSelect = "";
				$elem_followUpVistType= "";
				$elem_scribedBy = "";
				$elem_follow_up = "";
				$commentsForPatient="";
				$elem_resiHxReviewd = "";
				$elem_labhandwritten="";
				$elem_radhandwritten="";
				$elem_rxhandwritten = "";
				$elem_modi_note_Asses="";
				$elem_consult_reason="";
				$elem_sur_ocu_hx="";
				$elem_sur_sent_4_dos="";
				$elem_doctorName_id="";
				$elem_refer_to_id="";
				$elem_refer_to="";
				$refer_code="";
				$elem_transition_reason="";
				//$elem_transition_notes="";

				//Soc
				$el_soc_alert = "";
				$oAdmn = new Admn();
				$el_soc_alert=$oAdmn->get_standrad_of_care($el_soc, 1, $el_soc_commnts);
				if(!empty($el_soc_alert)){ $el_soc_alert = " top.fAlert('".jsEscape($el_soc_alert)."','STANDARDS OF CARE'); "; }
				$el_soc = "";
				$el_soc_commnts = "";



				//17-09-2112: Comments from the comment box  at bottom left corner of WorkView should be carry forward.
				//$elem_notes = "";
				//---Empty AP elements

				/*
				//Assessment and Plan Resollved
				$arrResolve = array($elem_resolve1,$elem_resolve2,$elem_resolve3);
				$arrNotes = array($elem_plan1,$elem_plan2,$elem_plan3);
				$arrAssessment = array($elem_assessment1,$elem_assessment2,$elem_assessment3);
				$arrNE = array($no_change_1,$no_change_2,$no_change_3);

				for($j=4,$i=0;$i<$lenAdd;$i++,$j++){
					$elem_assessment = "elem_assessment".$j;
					$elem_plan = "elem_plan".$j;
					$elem_Resolve = "elem_resolve".$j;
					$no_change_Assess = "no_change_".$j;
					//echo "<br>".$elem_plan." : ".$$elem_plan;

					$arrResolve[] = $$elem_Resolve;
					$arrNotes[] = $$elem_plan;
					$arrAssessment[] = $$elem_assessment;
					$arrNE[] = $$no_change_Assess;
				}
				*/

				/*
				echo "<pre>";
				print_r($arrResolve);
				echo "<br>";
				print_r($arrNotes);
				echo "<br>";
				print_r($arrAssessment);
				echo "<br></pre>";
				//*/

				/*
				//Set Values
				$text_dataTmp = "";
				$lenAssess = count($arrAssessment);
				for($i=0,$j=1;$i<$lenAssess;$i++){
					//echo "<br>Resolve: ".$arrResolve[$i].", NE: ".$arrNE[$i]."";
					if(($arrResolve[$i] != 1) || ($arrNE[$i] == 1)){
						$varAssessment = "elem_assessment".($j);
						$$varAssessment = $arrAssessment[$i];
						$varNotes = "elem_plan".($j);
						$$varNotes = ($arrNE[$i] == 1) ? $arrNotes[$i] : "" ;
						$varPlanResolve = "elem_resolve".($j);
						$$varPlanResolve = $arrResolve[$i];
						$varNe = "no_change_".($j);
						$$varNe = $arrNE[$i];
						$varCFNotes = "elem_CFPlan".($j);
						$$varCFNotes = $arrNotes[$i];
						//if($arrNE[$i] == 1){
							$varScheduleId = "elem_scheduleId".($j);
							//$varTSxSumm = "divTSxSumm".($j);
							//Insert Into Schedule carried Forward SX/TEST
						$$varScheduleId = getPrevScheduleId($j,$i+1,$patient_id,$form_id,$arrNE[$i]);
						//}
						//echo "<br>"."I: ".$i.",J: ".$j.",".$varNotes.": ".$$varNotes;
						//echo ", ".$varCFNotes." : ".$$varCFNotes;
						$text_dataTmp .=(!empty($arrAssessment[$i])) ? "".chk2remdx($arrAssessment[$i]).", " : ""; // Add assessments
						$j++;
					}
				}
				*/

				//Set Values
				$cntrLenAssess = 1;
				$text_dataTmp = "";
				for($i=0,$j=1;$i<$lenAssess;$i++){

					//Check for empty records---
					if(empty($arrAp[$i]["assessment"]) && empty($arrAp[$i]["plan"]) &&
						empty($arrAp[$i]["resolve"]) && empty($arrAp[$i]["ne"])
						){
						continue;
					}
					//---Check for empty records



					if(($arrAp[$i]["resolve"] != 1) || ($arrAp[$i]["ne"] == 1)){

						$varAssessment = "elem_assessment".($j);
						$varAssessmentFull = "elem_assessment_full".($j);

						//remove old diabetic codes after 1 oct 2016
						$arrAp[$i]["assessment"] = $this->resetDxCodesOfPrevAssess_dibetese($arrAp[$i]["assessment"]);

						//check icd code of previous and current visit
						if($prev_icd_code != $enc_icd10){
							$arrAp[$i]["assessment"] = $this->resetDxCodesOfPrevAssess($arrAp[$i]["assessment"], $enc_icd10,  $prev_icd_code);
						}

						$arrRet[$varAssessmentFull] = $arrAp[$i]["assessment"];
						$arrRet[$varAssessment] = $arrAp[$i]["assessment"];
						$varNotes = "elem_plan".($j);
						$arrRet[$varNotes] = ($arrAp[$i]["ne"] == 1) ? $arrAp[$i]["plan"] : "" ;
						$varPlanResolve = "elem_resolve".($j);
						$arrRet[$varPlanResolve] = ($arrAp[$i]["resolve"] == "1") ? "checked=\"checked\"" : "" ; //$arrResolve[$i];
						$varNe = "no_change_".($j);
						$arrRet[$varNe] = ($arrAp[$i]["ne"] == "1") ? "checked=\"checked\"" : "" ;
						$varCFNotes = "elem_CFPlan".($j);
						$arrRet[$varCFNotes] = str_replace('"',"'",$arrAp[$i]["plan"]); //$arrAp[$i]["plan"];
						$varapConMeds = "elem_apConMeds".$j; //Continue meds
						$arrRet[$varapConMeds] = $arrAp[$i]["conmed"];
						$var_problist_id_assess = "elem_problist_id_assess".$j;
						$arrRet[$var_problist_id_assess] = $arrAp[$i]["pbid"];
						$var_pt_ap_id = "el_pt_ap_id".$j;
						$arrRet[$var_pt_ap_id] = $arrAp[$i]["pt_ap_id"];

						//Need to Set Eye values: working here --
						$varOu = "elem_apOu".($j);
						$varOd = "elem_apOd".($j);
						$varOs = "elem_apOs".($j);
						$arrRet[$varOu] = $arrRet[$varOd] = $arrRet[$varOs] = "";
						if($arrAp[$i]["eye"]=="OU"){
							$arrRet[$varOu] = "checked=\"checked\"";
						}else if($arrAp[$i]["eye"]=="OD"){
							$arrRet[$varOd] = "checked=\"checked\"";
						}else if($arrAp[$i]["eye"]=="OS"){
							$arrRet[$varOs] = "checked=\"checked\"";
						}

						$varAssessment_dxcode = "elem_assessment_dxcode".$j;
						list($arrRet[$varAssessment] , $arrRet[$varAssessment_dxcode]) = $this->getDxCodeFromAssess($arrRet[$varAssessment]);

						//Agreed, they will be added with the “;”  after the assessment.
						$arrRet[$varAssessment] = $this->add_literality_in_asmt($arrRet[$varAssessment], $arrAp[$i]["eye"]);
						//-----

						//check incomplete dx
						$var_incomplete_dxcode = "elem_incomplete_dxcode".$j;
						$arrRet[$var_incomplete_dxcode] = $this->show_site_dropdown($arrRet[$varAssessment_dxcode]);

						//dx code id
						$var_asmt_dxid = "elem_asmt_dxid".$j;
						$arrRet[$var_asmt_dxid] = $arrAp[$i]["dxcodeId"];

						//if($arrNE[$i] == 1){
						//	$varScheduleId = "elem_scheduleId".($j); //This is replaced by order set
							//$varTSxSumm = "divTSxSumm".($j);
							//Insert Into Schedule carried Forward SX/TEST
						//$$varScheduleId = getPrevScheduleId($j,$i+1,$patient_id,$form_id,$arrAp[$i]["ne"]); // replaced by order set
						//}
						//echo "<br>"."I: ".$i.",J: ".$j.",".$varNotes.": ".$$varNotes;
						//echo ", ".$varCFNotes." : ".$$varCFNotes;
						if(!empty($arrAp[$i]["assessment"])){
							$tmpAssess = $this->chk2remdx($arrAp[$i]["assessment"]);
							if(!empty($elem_ccHx) && strpos($elem_ccHx,$tmpAssess)!==false){
								//if Already in CC Hx and do not add again
							}else{
								$text_dataTmp .= "\n".$tmpAssess." "; // Add assessments
							}
						}

						//reset plan num in order //$patient_id,$form_id
						$this->ap_resetOrderNum($i+1,$j);

						//reset plan num in order

						$j++;
						$cntrLenAssess++;

						/*
						//PP html
						if(!empty($$varCFNotes)){
							$strPrvPlanPop.="<br/><input type=\"checkbox\" name=\"elem_ppopt[]\" value=\"".$$varCFNotes."\" checked=\"checked\" >".$$varCFNotes."";
						}
						*/
					}
				}

				//Add Assemsnt in CCHx
				$text_dataTmp = trim(substr($text_dataTmp,0,-1));//trim(substr($text_dataTmp,0,-2));
				//$text_data .= ($text_dataTmp != "") ? "\r\n".$text_dataTmp : "";

				/*
				$lenAdd = ($j>5) ? $j : 0;
				if($lenAdd>4){
					$lenAdd = $lenAdd - 4;
				}
				*/

				//Empty Values
				for($j=$cntrLenAssess;$j<=$lenAssess;$j++){
					$varAssessment = "elem_assessment".($j);
					$arrRet[$varAssessment] = "";
					$varAssessment_dxcode = "elem_assessment_dxcode".($j);
					$arrRet[$varAssessment_dxcode] = "";
					$varNotes = "elem_plan".($j);
					$arrRet[$varNotes] = "";
					$varPlanResolve = "elem_resolve".($j);
					$arrRet[$varPlanResolve] = "";
					$varNe = "no_change_".($j);
					$arrRet[$varNe] = "";
					$varapConMeds = "elem_apConMeds".($j);
					$arrRet[$varapConMeds] = 0;
					$var_problist_id_assess = "elem_problist_id_assess".($j);
					$arrRet[$var_problist_id_assess] = 0;
					$var_pt_ap_id = "el_pt_ap_id".($j);
					$arrRet[$var_pt_ap_id] = 0;
					$var_asmt_dxid = "elem_asmt_dxid".($j);
					$arrRet[$var_asmt_dxid] = "";
				}

				//Decrease in LenAssess so that less assessments are made
				//Only done in New Chart
				//Plus one for empty row - will do later
				$lenAssess = ($cntrLenAssess>=5) ? $cntrLenAssess : 5;
			}

			//Check for Last Empty---
			//Add One if last is not empty
			if(!empty($arrAp[$lenAssess-1]["assessment"]) || !empty($arrAp[$lenAssess-1]["plan"])){
				$lenAssess = $lenAssess+1;
			}
			//---Check for Last Empty


			//Follow Up ------------
			//if(!empty($elem_follow_up)){
			//	list($lenFu, $arrFuVals) = fu_getXmlValsArr($elem_follow_up);
			//}
			//Follow Up ------------

		}
		//GET last Assessment --------
		if(($finalize_flag == 0)){
			$tmp = " commentsForPatient,assess_plan, form_id ";
			$row = $this->valuesNewRecordsAssess($tmp,1);
			if($row != false){
				$form_id_prev_ap = $row["form_id"];
				if($elem_editModeAssess == "0"){ //will work for first time when chart is opened
				if(empty($commentsForPatient) && !empty($row["commentsForPatient"])){
					$commentsForPatient = stripslashes($row["commentsForPatient"]);
					$commentsForPatient_bgclr = "bgSmoke";
				}
				}

				if(($elem_editModeAssess == "1")){ //!empty($row["assess_plan"]) &&
					$strXml_prev = stripslashes($row["assess_plan"]);
					//Set Assess Plan Resolve NE Value FROM xml ---
					$oChartApXml = new ChartAP($this->pid,$form_id_prev_ap);
					$arrApVals_prev = !empty($strXml_prev) ? $oChartApXml->getVal_Str($strXml_prev) : $oChartApXml->get_pt_ap_db();
					$arrAp_prev = $arrApVals_prev["data"]["ap"];

					$lenAssess_prev = is_array($arrAp_prev) ? count($arrAp_prev) : 0 ;
					for($i=0,$j=1;$i<$lenAssess_prev;$i++){

						//Check for empty records---
						if(empty($arrAp_prev[$i]["assessment"]) && empty($arrAp_prev[$i]["plan"]) &&
							empty($arrAp_prev[$i]["resolve"]) && empty($arrAp_prev[$i]["ne"])
						){
							continue;
						}
						//---Check for empty records

						if(($arrAp_prev[$i]["resolve"] != 1) || ($arrAp_prev[$i]["ne"] == 1)){

							$jindx = $this->get_prv_ases_indx_of_plan($j, $arrAp_prev[$i]["assessment"], $arrRet);
							$varCFNotes = "elem_CFPlan".($jindx);
							$arrRet[$varCFNotes] = str_replace('"',"'",$arrAp_prev[$i]["plan"]); //$arrAp_prev[$i]["plan"];
							$j++;

							/*
							//PP html
							if(!empty($$varCFNotes)){
								$strPrvPlanPop.="<br/><input type=\"checkbox\" name=\"elem_ppopt[]\" value=\"".$$varCFNotes."\" checked=\"checked\" >".$$varCFNotes."";
							}
							*/
						}
					}
					//--- Set Assess Plan Resolve NE Value FROM xml

				}

			}
		}else{

			/*
			[1/11/13 AK] 
			*/

			//$oChartApXml = new ChartApXml($patient_id,$form_id);
			$arrApVals_prev = $this->getPrevPlans($dos_ymd);
			$arrAp_prev = $arrApVals_prev["data"]["ap"];
			//print_r($arrAp_prev);

			//--
			$lenAssess_prev = is_array($arrAp_prev) ? count($arrAp_prev) : 0 ;
			for($i=0,$j=1;$i<$lenAssess_prev;$i++){

				//Check for empty records---
				if(empty($arrAp_prev[$i]["assessment"]) && empty($arrAp_prev[$i]["plan"]) &&
					empty($arrAp_prev[$i]["resolve"]) && empty($arrAp_prev[$i]["ne"])
				){
					continue;
				}
				//---Check for empty records

				if(($arrAp_prev[$i]["resolve"] != 1) || ($arrAp_prev[$i]["ne"] == 1)){
					$jindx = $this->get_prv_ases_indx_of_plan($j, $arrAp_prev[$i]["assessment"], $arrRet);
					$varCFNotes = "elem_CFPlan".($jindx);
					$arrRet[$varCFNotes] = str_replace('"',"'",$arrAp_prev[$i]["plan"]); //$arrAp_prev[$i]["plan"];
					$j++;

					/*
					//PP html
					if(!empty($$varCFNotes)){
						$strPrvPlanPop.="<br/><input type=\"checkbox\" name=\"elem_ppopt[]\" value=\"".$$varCFNotes."\" checked=\"checked\" >".$$varCFNotes."";
					}
					*/
				}
			}
			//--
		}
		//GET last Assessment --------

		//sx ocu hx
		$elem_sur_ocu_hx_checked = ($elem_sur_ocu_hx==1) ? "checked" : "";
		$elem_sur_ocu_hx_sent4dos = ($elem_sur_ocu_hx==1 && !empty($elem_sur_sent_4_dos) && $elem_sur_sent_4_dos != "00-00-0000") ? " (Sent for DOS : ".$elem_sur_sent_4_dos.")" : "";

		//
		//Add to CC Hx All the Last finalized assessments
		if(($finalize_flag == 0) && ($elem_ccId == "0")){
		$elem_ccHx .= ($text_dataTmp != "") ? "\n".$text_dataTmp : ""; // Add Prev. Assessments and plans
		//$elem_ccHx = preg_replace("/\s\s/", " ", trim($elem_ccHx)); // remove double spaces;
		$elem_ccHx = str_replace(array("  ","\n\n"),array(" ","\n"),trim($elem_ccHx));

			//12/21/2012:If New Patient and not Medical Hx then it show "Patient past medical and optical history is unknown"
			if(strtoupper(trim($elem_ccHx))==strtoupper(PTHISTORY)){
				$elem_ccHx = PTMEDUNKNOWN."\r";
			}
		}
		//---


		///
		if(empty($lenAssess)){ $lenAssess=5; }

		//Follow Up ------------
		$oFu = new Fu($this->pid,$this->fid);
		list($lenFu, $arrFuVals) = $oFu->getFormInfo();
		global $arrFuNum_menu, $arrFuVist_menu;
		$arrFuNum_menu = $oFu->get_fu_menu("n");
		$arrFuVist_menu = $oFu->get_fu_menu("v");

		//--

		//Archive Assess & Plan --
		if($elem_editModeAssess != "0"){ //check archive only if values are NOT coming from previous visit
			//object Chart Rec Archive --
			$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);

			if($oChartRecArc->isArchived()){
				//OverAll Len
				$lenAssess_oa_arc=$lenAssess;
				$lenFu_oa_arc = $lenFu;
				$oChartRecArc->setChkTbl("chart_assessment_plans");
				$arrInpArc = array( "assess_plan"=>array("assess_plan",$lenAssess_oa_arc,"smof",$arrRet),
									"followup"=>array("followup",$lenFu_oa_arc,"smof",$arrFuVals),
									"commentsForPatient"=>array("commentsForPatient",$commentsForPatient,"smof"),
									"elem_scribedBy"=>array("scribedBy",$elem_scribedBy,"smof"),
									"elem_notes"=>array("plan_notes",$elem_notes,"smof"),
									"elem_consult_reason"=>array("consult_reason",$elem_consult_reason,"smof"),
									"elem_refer_to"=>array("refer_to",$elem_refer_to,"smof"),
									"elem_transition_reason"=>array("transition_reason",$elem_transition_reason,"smof"),
									"elem_transition_notes"=>array("transition_notes",$elem_transition_notes,"smof"),
									"elem_doctorName"=>array("doctor_name",$elem_doctorName,"smof")	 );
				//
				$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

				//commentsForPatient
				if(!empty($arTmpRecArc["div"]["commentsForPatient"])){
					$commentsForPatient_htm = $arTmpRecArc["div"]["commentsForPatient"];
					$commentsForPatient_js = $arTmpRecArc["js"]["commentsForPatient"];
					$commentsForPatient_css = $arTmpRecArc["css"]["commentsForPatient"];
					if(!empty($arTmpRecArc["curText"]["commentsForPatient"])){$commentsForPatient = $arTmpRecArc["curText"]["commentsForPatient"];}
				}

				//Doctor Name
				if(!empty($arTmpRecArc["div"]["elem_doctorName"])){
					$elem_doctorName_htm = $arTmpRecArc["div"]["elem_doctorName"];
					$elem_doctorName_js = $arTmpRecArc["js"]["elem_doctorName"];
					$elem_doctorName_css = $arTmpRecArc["css"]["elem_doctorName"];
					if(!empty($arTmpRecArc["curText"]["elem_doctorName"])){$elem_doctorName = $arTmpRecArc["curText"]["elem_doctorName"];}
				}

				//Plan Notes
				if(!empty($arTmpRecArc["div"]["elem_notes"])){
					$elem_notes_htm = $arTmpRecArc["div"]["elem_notes"];
					$elem_notes_js = $arTmpRecArc["js"]["elem_notes"];
					$elem_notes_css = $arTmpRecArc["css"]["elem_notes"];
					if(!empty($arTmpRecArc["curText"]["elem_notes"])){	$elem_notes = $arTmpRecArc["curText"]["elem_notes"];}
				}

				//consult_reason
				if(!empty($arTmpRecArc["div"]["elem_consult_reason"])){
					$elem_consult_reason_htm = $arTmpRecArc["div"]["elem_consult_reason"];
					$elem_consult_reason_js = $arTmpRecArc["js"]["elem_consult_reason"];
					$elem_consult_reason_css = $arTmpRecArc["css"]["elem_consult_reason"];
					if(!empty($arTmpRecArc["curText"]["elem_consult_reason"])){$elem_consult_reason = $arTmpRecArc["curText"]["elem_consult_reason"];}
				}

				//ScribdBy
				if(!empty($arTmpRecArc["div"]["elem_scribedBy"])){
					$elem_scribedBy_htm = $arTmpRecArc["div"]["elem_scribedBy"];
					$elem_scribedBy_js = $arTmpRecArc["js"]["elem_scribedBy"];
					$elem_scribedBy_css = $arTmpRecArc["css"]["elem_scribedBy"];
					if(!empty($arTmpRecArc["curText"]["elem_scribedBy"])){$elem_scribedBy = $arTmpRecArc["curText"]["elem_scribedBy"];}
				}

				//Refer to
				if(!empty($arTmpRecArc["div"]["elem_refer_to"])){
					$elem_refer_to_htm = $arTmpRecArc["div"]["elem_refer_to"];
					$elem_refer_to_js = $arTmpRecArc["js"]["elem_refer_to"];
					$elem_refer_to_css = $arTmpRecArc["css"]["elem_refer_to"];
					if(!empty($arTmpRecArc["curText"]["elem_refer_to"])){	$elem_refer_to = $arTmpRecArc["curText"]["elem_refer_to"];}

				}

				//Transition reason
				if(!empty($arTmpRecArc["div"]["elem_transition_reason"])){
					$elem_transition_reason_htm = $arTmpRecArc["div"]["elem_transition_reason"];
					$elem_transition_reason_js = $arTmpRecArc["js"]["elem_transition_reason"];
					$elem_transition_reason_css = $arTmpRecArc["css"]["elem_transition_reason"];
					if(!empty($arTmpRecArc["curText"]["elem_transition_reason"])){	$elem_transition_reason = $arTmpRecArc["curText"]["elem_transition_reason"];}

				}

				//Transition Notes
				if(!empty($arTmpRecArc["div"]["elem_transition_notes"])){
					$elem_transition_notes_htm = $arTmpRecArc["div"]["elem_transition_notes"];
					$elem_transition_notes_js = $arTmpRecArc["js"]["elem_transition_notes"];
					$elem_transition_notes_css = $arTmpRecArc["css"]["elem_transition_notes"];
					if(!empty($arTmpRecArc["curText"]["elem_transition_notes"])){	$elem_transition_notes = $arTmpRecArc["curText"]["elem_transition_notes"];}
				}

			}//

		}
		//Archive Assess & Plan --

		//
		// commentsForPatient : separate discussion comments, date and initial
		$commentsForPatient_Dt="";
		$commentsForPatient_nm="";
		$commentsForPatient_nm_prv="";
		if(!empty($commentsForPatient)){
			$tmp = explode("~||~", $commentsForPatient);
			$commentsForPatient = $tmp[0];
			if(!empty($tmp[1])) { $commentsForPatient_Dt = $tmp[1]; }
			if(!empty($tmp[2])) {
				$commentsForPatient_nm_prv = $tmp[2];
				$ousr = new User($tmp[2]);
				$commentsForPatient_nm = $ousr->getName(4);
			}
		}

		//elem_notes : separate comments, date and initial
		$elem_notes_Dt="";
		$elem_notes_nm="";
		$el_elem_notes_nm_prv="";
		if(!empty($elem_notes)){
			$tmp = explode("~||~", $elem_notes);
			$elem_notes = $tmp[0];
			if(!empty($tmp[1])) { $elem_notes_Dt = $tmp[1]; }
			if(!empty($tmp[2])) {
				$el_elem_notes_nm_prv = $tmp[2];
				$ousr = new User($tmp[2]);
				$elem_notes_nm = $ousr->getName(4);
			}
		}
		//--


		//elem_transition_notes : separate comments, date and initial
		$elem_transition_notes_Dt="";
		$elem_transition_notes_nm="";
		$el_elem_transition_notes_nm_prv="";
		if(!empty($elem_transition_notes)){
			$tmp = explode("~||~", $elem_transition_notes);
			$elem_transition_notes = $tmp[0];
			if(!empty($tmp[1])) { $elem_transition_notes_Dt = $tmp[1]; }
			if(!empty($tmp[2])) {
				$el_elem_transition_notes_nm_prv = $tmp[2];
				$ousr = new User($tmp[2]);
				$elem_transition_notes_nm = $ousr->getName(4);
			}
		}
		//--

		//check HX button--
		$cls_hx_button=" hidden ";
		if(!empty($elem_modi_note_Asses) || !empty($modi_note_AssesArr)){
			if((!empty($modi_note_AssesArr) && $this->mkDivAssHx(unserialize($modi_note_AssesArr))) || !empty($elem_modi_note_Asses)){
				$cls_hx_button="";
			}
		}
		//--

		//scribe by select
		$ousr = new User();
		$sel_scribeby = $ousr->getUsersDropDown("elem_scribedBy", $elem_scribedBy_js, $elem_scribedBy, "scribeby", $elem_scribedBy_css);
		$sel_scribeby = $sel_scribeby.$elem_scribedBy_htm;

		// return --
		$arrRet["cls_hx_button"] = $cls_hx_button;
		$arrRet["elem_sur_ocu_hx_checked"] = $elem_sur_ocu_hx_checked;
		$arrRet["elem_sur_ocu_hx_sent4dos"] = $elem_sur_ocu_hx_sent4dos;
		if(isset($lenAssess)){$arrRet["lenAssess"] = $lenAssess;}
		if(isset($arrFuVals)){$arrRet["arrFuVals"] = $arrFuVals;}
		if(isset($lenFu)){$arrRet["lenFu"] = $lenFu;}
		if(isset($elem_assessId)){$arrRet["elem_assessId"] = $elem_assessId;}
		if(isset($elem_editModeAssess)){$arrRet["elem_editModeAssess"] = $elem_editModeAssess;}

		if(isset($elem_followUpNumber)){$arrRet["elem_followUpNumber"] = $elem_followUpNumber ;}
		if(isset($elem_followUp)){$arrRet["elem_followUp"] = $elem_followUp ;}
		if(isset($elem_retina)){$arrRet["elem_retina"] = $elem_retina ;}
		if(isset($elem_neuroOptha)){$arrRet["elem_neuroOptha"] = $elem_neuroOptha;}
		if(isset($elem_doctorName)){$arrRet["elem_doctorName"] = $elem_doctorName ;}
		if(isset($elem_doctorName_id)){$arrRet["elem_doctorName_id"] = $elem_doctorName_id ;}
		if(isset($elem_idPrecautions)){$arrRet["elem_idPrecautions"] = $elem_idPrecautions ;}
		if(isset($elem_rdPrecautions)){$arrRet["elem_rdPrecautions"] = $elem_rdPrecautions ;}
		if(isset($elem_lidScrubs)){$arrRet["elem_lidScrubs"] = $elem_lidScrubs ;}
		if(isset($elem_patientUnderstands)){$arrRet["elem_patientUnderstands"] = $elem_patientUnderstands;}
		if(isset($elem_patientUnderstandsSelect)){$arrRet["elem_patientUnderstandsSelect"] = $elem_patientUnderstandsSelect;}
		if(isset($elem_notes)){$arrRet["elem_notes"] = $elem_notes ;}

		if(isset($elem_continue_meds)){$arrRet["elem_continue_meds"] = $elem_continue_meds;}
		if(isset($elem_followUpVistType)){$arrRet["elem_followUpVistType"] = $elem_followUpVistType;}
		if(isset($elem_scribedBy)){$arrRet["elem_scribedBy"] = $elem_scribedBy ;}
		if(isset($elem_follow_up)){$arrRet["elem_follow_up"] = $elem_follow_up ;}
		if(isset($commentsForPatient)){$arrRet["commentsForPatient"] = $commentsForPatient;}
		if(isset($strXml)){$arrRet["strXml"] = $strXml ;}

		if(isset($elem_resiHxReviewd)){$arrRet["elem_resiHxReviewd"] = $elem_resiHxReviewd;}
		if(isset($elem_rxhandwritten)){$arrRet["elem_rxhandwritten"] = $elem_rxhandwritten;}
		if(isset($elem_labhandwritten)){$arrRet["elem_labhandwritten"] = $elem_labhandwritten;}
		if(isset($elem_radhandwritten)){$arrRet["elem_radhandwritten"] = $elem_radhandwritten;}
		if(isset($elem_modi_note_Asses)){$arrRet["elem_modi_note_Asses"] = $elem_modi_note_Asses;}
		if(isset($modi_note_AssesArr)){$arrRet["modi_note_AssesArr"] = $modi_note_AssesArr;}
		if(isset($elem_consult_reason)){$arrRet["elem_consult_reason"] = $elem_consult_reason;}
		if(isset($elem_sur_ocu_hx)){$arrRet["elem_sur_ocu_hx"] = $elem_sur_ocu_hx;}
		if(isset($elem_sur_sent_4_dos)){$arrRet["elem_sur_sent_4_dos"] = $elem_sur_sent_4_dos;}
		if(isset($elem_ccHx)){$arrRet["elem_ccHx"] = $elem_ccHx;}
		$arrRet["commentsForPatient_Dt"] = $commentsForPatient_Dt;
		$arrRet["commentsForPatient_nm"] = $commentsForPatient_nm;
		$arrRet["elem_notes_Dt"] = $elem_notes_Dt;
		$arrRet["elem_notes_nm"] = $elem_notes_nm;
		$arrRet["el_elem_notes_nm_prv"] = $el_elem_notes_nm_prv;

		$arrRet["elem_refer_to"] = $elem_refer_to;
		$arrRet["elem_refer_to_id"] = $elem_refer_to_id;
		$arrRet["elem_refer_to_htm"] = $elem_refer_to_htm;
		$arrRet["elem_refer_to_js"] = $elem_refer_to_js;
		$arrRet["elem_refer_to_css"] = $elem_refer_to_css;
		$arrRet["refer_code"] = $refer_code;

		$arrRet["elem_transition_reason"] = $elem_transition_reason;
		$arrRet["elem_transition_notes"] = $elem_transition_notes;
		$arrRet["elem_transition_reason_htm"] = $elem_transition_reason_htm;
		$arrRet["elem_transition_reason_js"] = $elem_transition_reason_js;
		$arrRet["elem_transition_reason_css"] = $elem_transition_reason_css;

		$arrRet["elem_transition_notes_Dt"] = $elem_transition_notes_Dt;
		$arrRet["elem_transition_notes_nm"] = $elem_transition_notes_nm;
		$arrRet["el_elem_transition_notes_nm_prv"] = $el_elem_transition_notes_nm_prv;
		$arrRet["elem_transition_notes_htm"] = $elem_transition_notes_htm;
		$arrRet["elem_transition_notes_js"] = $elem_transition_notes_js;
		$arrRet["elem_transition_notes_css"] = $elem_transition_notes_css;

		$arrRet["commentsForPatient_htm"] = $commentsForPatient_htm;
		$arrRet["commentsForPatient_js"] = $commentsForPatient_js;
		$arrRet["commentsForPatient_css"] = $commentsForPatient_css;

		$arrRet["elem_doctorName_htm"] = $elem_doctorName_htm;
		$arrRet["elem_doctorName_js"] = $elem_doctorName_js;
		$arrRet["elem_doctorName_css"] = $elem_doctorName_css;

		$arrRet["elem_consult_reason_htm"] = $elem_consult_reason_htm;
		$arrRet["elem_consult_reason_js"] = $elem_consult_reason_js;
		$arrRet["elem_consult_reason_css"] = $elem_consult_reason_css;

		$arrRet["elem_notes_htm"] = $elem_notes_htm;
		$arrRet["elem_notes_js"] = $elem_notes_js;
		$arrRet["elem_notes_css"] = $elem_notes_css;

		$arrRet["sel_scribeby"] = $sel_scribeby;

		$arrRet["arTmpRecArc"] = $arTmpRecArc;
		$arrRet["lenFu"] = $lenFu;
		$arrRet["arrFuVals"] = $arrFuVals;

		//-- Pt. Goals & Health Concerns & reason of refferal
		$arrRet['elem_pt_goal'] = $elem_pt_goal_str;
		$arrRet['elem_pt_health_concern'] = $elem_pt_health_concern;
		//soc
		$arrRet['el_soc'] = $el_soc;
		$arrRet['el_soc_commnts'] = $el_soc_commnts;
		$arrRet['el_soc_alert'] = $el_soc_alert;

		//--

		//--

		return $arrRet;

	}

	function mkDivAssHx($arrHx){
		$show = 0;
		if(count($arrHx)>0){
				foreach($arrHx as $arrTmpHx){
				if(preg_replace('/\s+/', '',$arrTmpHx['curr']['NE']) != preg_replace('/\s+/', '',$arrTmpHx['prev']['NE']) ||
				   preg_replace('/\s+/', '',$arrTmpHx['curr']['Res']) != preg_replace('/\s+/', '',$arrTmpHx['prev']['Res']) ||
				   preg_replace('/\s+/', '',$arrTmpHx['curr']['Asses']) != preg_replace('/\s+/', '',$arrTmpHx['prev']['Asses']) ||
				   preg_replace('/\s+/', '',$arrTmpHx['curr']['Site']) != preg_replace('/\s+/', '',$arrTmpHx['prev']['Site']) ||
				   preg_replace('/\s+/', '',$arrTmpHx['curr']['Plan']) != preg_replace('/\s+/', '',$arrTmpHx['prev']['Plan'])){
						$show = 1;
						return $show;
					}
				}
			}
		return $show;
	}

	function showAsHxNew(){
		$str="";
		$fid = $this->fid;
		$pid = $this->pid;
		$str_modi_note_AssesArr=array();
		if(!empty($fid) && !empty($pid) ){
			$sql = "SELECT modi_note_AssesArr,modi_note_Asses FROM chart_assessment_plans WHERE patient_id='".$pid."' AND form_id='".$fid."' ";
			$row=sqlQuery($sql);
			if($row!=false){
				$str_modi_note_AssesArr = $row["modi_note_AssesArr"];
				$str_modi_note_Asses = $row["modi_note_Asses"];
			}
		}
		$arrHx = unserialize($str_modi_note_AssesArr);
		if( $str_modi_note_Asses !='' || count($arrHx)>0 ){

			if($str_modi_note_Asses !=''){
				$str .= $str_modi_note_Asses;
			}
			if(count($arrHx)>0){
				$elmId="divassHx";
				foreach($arrHx as $arrTmpHx){
					$ousr = new User($arrTmpHx["modi_by"]);
					$nm = $ousr->getName(1);

					$prev_ne_res="";
					if(!empty($arrTmpHx['prev']['NE'])){ $prev_ne_res="(NE)"; }
					else if(!empty($arrTmpHx['prev']['Res'])){ $prev_ne_res="(Res)"; }

					$curr_ne_res="";
					if(!empty($arrTmpHx['curr']['NE'])){ $curr_ne_res="(NE)"; }
					else if(!empty($arrTmpHx['curr']['Res'])){ $curr_ne_res="(Res)"; }

					$str .= "<tr>";
						$str .= "<td >".date(''.phpDateFormat().' H:i:s', strtotime($arrTmpHx['time']))."</td>";
						$str .= "<td class=\"testhx_prev hxv\" title=\"Previous value\">".$prev_ne_res.' '.$arrTmpHx['prev']['Asses'].' '.$arrTmpHx['prev']['Site'].' '.$arrTmpHx['prev']['Plan']."</td>";
						$str .= "<td class=\"hxv\" title=\"New value\">".$curr_ne_res.' '.$arrTmpHx['curr']['Asses'].' '.$arrTmpHx['curr']['Site'].' '.$arrTmpHx['curr']['Plan']."</td>";
						$str .= "<td >".$nm."</td>";
					$str .= "</tr>";
				}
			}

			// style='padding:0px 12px 0px 0px;max-height:500px;overflow:visible;overflow:v-scroll;width:80%'
			//style='width:200px', style='width:250px'

			if(!empty($str)){
				$str ="<div id=\"divAsHx\" class=\"panel panel-primary\" >
					<div id='boxhead' class='panel-heading'><span class=\"glyphicon glyphicon-remove pull-right\" onclick=\"showAsHx(0)\"></span>Assessment History</div>
					<div class=\"panel-body table-responsive\"><table class=\"table table-bordered\">".$str."</table></div>
					</div>";
			}

		}
		echo $str;
	}

	//
	function cpoe_get_dup_med_order($arr){
		$arrRet=array();
		if(count($arr)>0){
			foreach($arr as $key=> $val2){
				if(count($val2)>0){
					$tmp_arrAss=$val2;
					foreach($tmp_arrAss as $key3=> $val3){

						if(count($val3)==5 && !empty($val3[3])){
							$tmp = trim($val3[3]);

							if(isset($arrRet[$tmp])){
								$arrRet[$tmp]=$arrRet[$tmp]+1;
							}else{
								$arrRet[$tmp]=1;
							}
						}
					}
				}
			}
		}
		return $arrRet;
	}

	//
	function get_plansof_asmt_handler(){
		$form_id=$this->fid; //$_GET["form_id"];
		$pid=$this->pid; //$_SESSION["patient"];
		$asmt = urldecode($_GET["asmt"]);
		$arrAsmt = explode("~!~",$asmt);
		$strIndx = urldecode($_GET["strIndx"]);
		$arAsIndx = explode("~!~",$strIndx);
		$arrDx = explode("~!~",urldecode($_GET["strDx"]));

		$arr=array();
		$arr["assess"]=array();
		if(count($arrAsmt)>0){
			foreach($arrAsmt as $key=>$val){
				$asmtTmpOrg=$asmtTmp=$val;
				if(empty($asmtTmp))continue;

				$oChartAP =  $this;//new ChartAP($pid, $form_id);
				$arr["assess"][$asmtTmpOrg]=$oChartAP->getPlansofAsmt($asmtTmp);
				$arr["arAsIndx"][$asmtTmpOrg]=$arAsIndx[$key+1];
				list($arr["order_assess_admin"][$asmtTmpOrg], $arrAPOrderIds)=$oChartAP->getOrdersofAsmtAdmin($arr["arAsIndx"][$asmtTmpOrg],$arrAsmt[$key],$arrDx[$key]);
				$arr["order"][$asmtTmpOrg]=$oChartAP->getOrdersofAsmt($arr["arAsIndx"][$asmtTmpOrg], "",$arrAPOrderIds);

				//combine orders --
				$arr["order"][$asmtTmpOrg] = array_merge_recursive($arr["order"][$asmtTmpOrg],$arr["order_assess_admin"][$asmtTmpOrg]);
				$arr["order"][$asmtTmpOrg] = arrmultisort($arr["order"][$asmtTmpOrg]);

				//
				$arr["dup_med_order"][$asmtTmpOrg]=$this->cpoe_get_dup_med_order($arr["order"][$asmtTmpOrg]);

				//unset
				unset($arr["order_assess_admin"][$asmtTmpOrg]);

			}
		}

		/*
		echo "<pre>";
		print_r($arr);
		exit();
		//*/

		//Printing Ocu Meds
		//final_flag="+final_flag+"&form_id="+form_id+"&txtbx_len="+lnAs
		$final_flag=$_GET["final_flag"];
		//$form_id=$_GET["form_id"];
		$lnAs=$_GET["lnAs"];
		$arr["ocumed"]="";
		//$arr["ocumed"]=getOcuMedsPrint($final_flag,$form_id,$lnAs);
		//--

		/* 15-12-2014 we do not need it.
		//Smart Chart Assessments --
		if(!empty($_GET["strSC"])){
			//get Assessment and Plan html
			$arrApId =urldecode($_GET["strSC"]);
			$arrApId =unserialize(gzuncompress(stripslashes(base64_decode(strtr($arrApId, '-_,', '+/=')))));
			$arrSC = getApPlansHtml($arrApId);

			if(count($arrSC)>0){
			$arr["assess"]= array_merge($arr["assess"],$arrSC);
			}
		}
		*/

		echo json_encode($arr);

	}

	function med_admnsrd(){
		//$contnt = print_r($_REQUEST, 1);

		//
		$form_id=$this->fid; //$_GET["form_id"];
		$pid=$this->pid; //$_SESSION["patient"];

		if(isset($_GET["or_det_id"])){
			//include_once(dirname(__FILE__)."/../chartNotesSaveFunction.php");
			$or_det_id = $_GET["or_det_id"];
			if(!empty($or_det_id)){
				$ocn = new ChartNote($pid, $form_id);
				$dos = $ocn->getDos(1);
				$o_corder = new ChartOrders($pid, $form_id);
				$o_corder->moveOrders_OrdersSets2OcuMeds($or_det_id, $dos);
			}
			echo "1";
			exit();
		}

		//show all the Meds ordered in all the Orders (Meds, Labs, Imaging/Rad….). with Administered check box next to them
		$oChartAP =  $this; //new ChartAP($pid, $form_id);
		$arrMeds = $oChartAP->getOrdersofAsmt('', "VISIT");

		$contnt="";
		if(count($arrMeds)>0){
			$contnt="";

			//$contnt=print_r($arrMeds, 1);

			foreach($arrMeds as $k1 => $v1){
				$ordertype = $k1;
				if(count($v1)>0){
					$arordr = $v1;
					$contnt.="<tr><td colspan='2'><b>".$ordertype."</b></td></tr>";
					foreach($arordr as $k2 => $v2){
						$ordrnm = $v2[0];
						$ordr_det_id = $v2[1];
						$contnt.="<tr><td width=\"80%\">".$ordrnm."</td><td><input type='checkbox' name='elem_mdsAdmrsd' value='".$ordr_det_id."' checked></td></tr>";
					}
				}
			}

			$contnt = "<table width=\"100%\">".$contnt."</table>";

		}else{
			$contnt="No Order is given in this visit!";
		}

		$str ="<div id=\"divMedAdmnrd\" >
					<div class=\"purple_bar\" id=\"boxhead\" >Meds Administered</div>
					<div class=\"med_con\" >
						$contnt
					</div>
					<input type=\"button\" class=\"btn btn-success\" id=\"btnMA_done\" onClick=\"funApPlan(51)\" value=\"Done\" />
					<input type=\"button\" class=\"btn btn-danger\" id=\"btnMA_close\" onClick=\"funApPlan(50)\" value=\"Close\"/>
				</div>
			";

		echo $str;

	}

	function updateOrderDetail(){
		$assi = $_GET["assi"];
		$form_id=$this->fid; //$_GET["form_id"];
		$pid=$this->pid;//$_SESSION["patient"];
		$asmt = trim(urldecode($_GET["asmt"]));
		$strDx = trim(urldecode($_GET["strDx"]));


		$arr=array();
		$str = "";
		$oChartAP = $this; // new ChartAP($pid, $form_id);
		list($arrOrder_0,$arrAPOrderIds) = $oChartAP->getOrdersofAsmtAdmin($assi,$asmt,$strDx);
		$arrOrder_1 = $oChartAP->getOrdersofAsmt($assi, "",$arrAPOrderIds);

		//combine orders --
		$arrOrder = array_merge_recursive($arrOrder_0, $arrOrder_1);
		$arrOrder = arrmultisort($arrOrder);

		//print_r($arrOrder);
		//exit();


		/*
		$ln = count($arrOrder);
		for($i=0;$i<$ln;$i++){
			//$text = $arrOrder[$i][0];
			$text = $arrOrder[$i][0];
			$id = $arrOrder[$i][1];

			if(!empty($text) && !empty($id)){
				$arr["orders"][] = array($id,$text);
				//$str .="<input type=\"checkbox\" name=\"elem_app".($assi-1)."\" value=\"".$text."\" hspace=\"23\" checked=\"checked\"><label onclick=\"showOrderDetail('".$assi."', '0', '".$id."')\" style=\"cursor:pointer\">".$text."</label>&nbsp;&nbsp;<label onclick=\"saveOrderDetail(3, '".$assi."', '".$id."','del')\" title=\"Delete\" style=\"cursor:pointer;font-weight:bold;color:purple;\">X</label><br/>";
			}
		}
		*/

		$arrOrder["dup_med_order"]=$this->cpoe_get_dup_med_order($arrOrder);
		//echo "<pre>";
		//print_r($arrOrder);
		echo json_encode($arrOrder);

	}

	function getLastVisitDxCodes($flg_add_dx=0){
		$arrdx = array();
		$sql_c1 = " AND finalize='0' ";
		$sql_base = "SELECT id FROM chart_master_table where patient_id ='".$this->pid."' AND delete_status='0' and purge_status='0' and record_validity='1' ";
		$sql = $sql_base.$sql_c1;
		$row = sqlQuery($sql);
		if($row==false){
			$sql = $sql_base." AND finalize='1' ORDER BY date_of_service DESC, id DESC  ";
			$row = sqlQuery($sql);
		}

		if($row!=false){
			$form_id = $row["id"];
			if(!empty($form_id)){
				$this->fid=$form_id;
				$arrAp = $this->getArrAP(1,1);
				$arrdx = (count($arrAp["dx"])>0) ? array_values($arrAp["dx"]) : array();
				array_filter($arrdx);

				if(count($arrdx)>0){
					$t = array();
					foreach($arrdx as $k =>$v){
						if(!empty($v)){
							$are = explode(",", $v);
							if(count($are)>0){
								foreach($are as $j => $w){
									$w = trim($w);
									if(!empty($w)){
										$t[] = $w;
									}
								}
							}
						}
					}
					$arrdx = $t;
				}
			}
		}
		if(count($arrdx)>0){
			$arrdx = array_unique($arrdx); $arrdx = array_values($arrdx);
			//Add Desc with dx
			if(!empty($flg_add_dx)){
				$odx = new Dx();
				$arrdx = $odx->getDxWidthDesc($arrdx);
			}
		}
		return $arrdx;
	}

	function get_pt_ap_id($as, $apId, $t_pt_ap_id){
		$ret = 0;
		if(!empty($t_pt_ap_id)){
			$sql = "SELECT id FROM chart_pt_assessment_plans WHERE id='".$t_pt_ap_id."' AND delete_by='0'  ";
			$row = sqlQuery($sql);
			if($row!=false && !empty($row["id"])){
				$ret = $row["id"];
			}
		}else{
		$sql = "SELECT id FROM chart_pt_assessment_plans WHERE id_chart_ap='".$apId."' AND assessment='".sqlEscStr($as)."' AND delete_by='0'  ";
		$row = sqlQuery($sql);
		if($row!=false && !empty($row["id"])){
			$ret = $row["id"];
		}else{
			/*
			$aswod = remSiteDxFromAssessment(trim($as));
			$sql = "SELECT id FROM chart_pt_assessment_plans WHERE id_chart_ap='".$apId."' AND assessment = '".sqlEscStr($aswod)."' AND delete_by='0'  ";
			$row = sqlQuery($sql);
			if($row!=false && !empty($row["id"])){
				$ret = $row["id"];
			}else{
				if(strpos($as,";")!==false){
					$ar_as = explode(";", $as);
					$aswos = trim($ar_as[0]);
					$sql = "SELECT id, assessment FROM chart_pt_assessment_plans WHERE id_chart_ap='".$apId."' AND assessment like '".sqlEscStr($aswos)."%' AND delete_by='0'  ";
					$row = sqlQuery($sql);
					if($row!=false && !empty($row["id"])){
						$chk_as = $row["assessment"];
						$aswod = remSiteDxFromAssessment(trim($as));
						$chk_as = remSiteDxFromAssessment(trim($chk_as));
						if($aswod == $chk_as){
							$ret = $row["id"];
						}
					}
				}
			}
			*/
		}
		}
		return $ret;
	}

	function save_pt_assess_plan($arr,$apid){
		if(empty($apid)){ return;  }

		$arrAssess=$arr[0];
		$arrPlan=$arr[1];
		$arrResolve=$arr[2];
		$arrNe=$arr[3];
		$arrEye=$arr[4];
		$arrConMed=$arr[5];
		$arrProbListId=$arr[6];
		$arrPtApId=$arr[7];
		$arrAp_dx_id=$arr[8];

		//Loop array values
		$length = (count($arrAssess) >= count($arrPlan)) ? count($arrAssess) : count($arrPlan);

		$t_now = wv_dt('now');
		$ar_cur_pt_ap_ids=array();
		for($i=0;$i<$length;$i++){
			$t_assess = trim($this->clearBadChars($arrAssess[$i],2));
			$t_plan =trim($this->clearBadChars($arrPlan[$i],2));
			if(!empty($arrAssess[$i]) || !empty($arrPlan[$i])) {

				$t_ne =$arrNe[$i];
				$t_resolve =$arrResolve[$i];
				$t_eye =$arrEye[$i];
				$t_conmed =$arrConMed[$i];
				$t_problistid =$arrProbListId[$i];
				$t_pt_ap_id =$arrPtApId[$i];
				$t_ap_dx_id =$arrAp_dx_id[$i];

				$diag_order = $i+1;

				$t_pt_ap_id = $this->get_pt_ap_id($t_assess, $apid, $t_pt_ap_id);


				if(!empty($t_pt_ap_id)){
					//update
					$sql_a = "UPDATE chart_pt_assessment_plans SET ";
					$sql_c = "WHERE id='".$t_pt_ap_id."' ";
				}else{
					//insert
					$sql_a = "INSERT INTO chart_pt_assessment_plans SET ".
							"id_chart_ap='".$apid."', ".
							"create_by='".$_SESSION["authId"]."', ".
							"create_time='".$t_now."', ".
							"";
					$sql_c ="";
				}

				$sql_b =	"id_pt_problem_list='".$t_problistid."', ".
						"not_examin='".$t_ne."', ".
						"resolve='".$t_resolve."', ".
						"assessment='".sqlEscStr($t_assess)."', ".
						"plan='".sqlEscStr($t_plan)."', ".
						"eye='".$t_eye."', ".
						"modify_by='".$_SESSION["authId"]."', ".
						"modify_time='".$t_now."', ".
						"diag_order='".$diag_order."', ".
						"dxcode='".$t_ap_dx_id."' ".
						"";
				$sql = $sql_a.$sql_b.$sql_c;
				if(!empty($t_pt_ap_id)){
					$row = sqlQuery($sql);
				}else{
					$t_pt_ap_id = sqlInsert($sql);
				}
				$t_pt_ap_id = trim($t_pt_ap_id);
				if($t_pt_ap_id=="undefined"){$t_pt_ap_id="";}
				if(!empty($t_pt_ap_id) && is_numeric($t_pt_ap_id)){
					$ar_cur_pt_ap_ids[] = $t_pt_ap_id;
				}
			}
		}

		//Delete Rest
		$str_del = "";
		if(count($ar_cur_pt_ap_ids)>0){ $str_del = " AND id NOT IN (".implode(",", $ar_cur_pt_ap_ids).") ";  }
		$sql = " UPDATE chart_pt_assessment_plans SET delete_by='".$_SESSION["authId"]."', delete_time = '".$t_now."' WHERE delete_by='0' AND id_chart_ap='".$apid."' ".$str_del;
		$row = sqlQuery($sql);

		return $ar_cur_pt_ap_ids;
	}

	function save_sur_ocu(){

		$el_sur_ocu_hx = $_REQUEST["elem_sur_ocu_hx"];
		$sql = "UPDATE chart_assessment_plans SET surgical_ocular_hx='".$el_sur_ocu_hx."' WHERE form_id = '".$this->fid."' AND patient_id = '".$this->pid."' ";
		$row = sqlQuery($sql);

	}

	function load_dos_plans(){
		$ar = $this->getVal();
		$str="";
		if(count($ar["data"]["ap"])>0){
			foreach($ar["data"]["ap"] as $k => $arv){
				$tmp = trim($arv["plan"]);
				if(!empty($tmp) && empty($arv["resolve"])){
					$i = $k+1;
					$str.="<tr valign=\"top\"><td>".$i.".</td><td id=\"tdprevplan".$i."\" >".$tmp."</td><td align=\"center\"  title=\"Remove\" onclick=\"remPrevPlan('".$i."')\"><span class=\"glyphicon glyphicon-remove\"></span></td></tr>";
				}
			}
		}
		if(!empty($str)){
			$str = "<table border=\"0\" class=\"table table-bordered table-hover table-striped\">".$str."</table>";
		}else{
			$str = "No Previous Plan";
		}

		echo $str;
	}
}

?>
