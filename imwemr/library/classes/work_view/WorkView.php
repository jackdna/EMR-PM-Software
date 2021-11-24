<?php
class WorkView{
	// Check if Patient Id is matched with formId
	function checkMisMatchedPatient($fid,$pid){
		$sql = "SELECT COUNT(*) AS num FROM chart_master_table ".
				"WHERE id='".$fid."' AND patient_id='".$pid."' ";
		//$res=$this->db->Execute($sql);
		$row = sqlQuery($sql);
		if($row!=false && $row["num"]>0){
			return false;
		}
		return true;
	}
	
	function getDefaultICDCode($caseId="", $fid="", $pid=""){
		$icd_code="";
		$icd_code_tmp="";
		$icd_code2="";
		if(!empty($caseId)){
			//
			$sql = "select c3.icd_code from insurance_case c1
					LEFT JOIN insurance_data c2 ON c1.ins_caseid=c2.ins_caseid
					LEFT JOIN insurance_companies c3 ON c3.id = c2.provider
					WHERE c1.ins_caseid='".$caseId."' AND c2.type='primary' AND c2.del_status='0' AND c2.provider!='' AND c2.provider!='0' AND c2.actInsComp='1'
					Limit 0, 1";
			$row=sqlQuery($sql);
			if($row!=false){
				$icd_code_tmp=trim($row["icd_code"]);
				if($icd_code_tmp=="ICD-10"||$icd_code_tmp=="ICD-9"){
					$icd_code=$icd_code_tmp;				
				}
			}	
		}
		
		//if(empty($flg_notcheckadmin)){	
		//admin
		if(empty($icd_code)){
			$sql = "select icd_code from copay_policies";
			$row=sqlQuery($sql);
			if($row!=false){
				$icd_code2=trim($row["icd_code"]);
				$icd_code=trim($row["icd_code"]);
			}
		}
		//}
		
		$ret = ($icd_code=="ICD-9") ? 9 : 1;
		
		$curdt = date("Y-m-d");
		if($ret == 9 && $curdt >= "2015-10-01"){ //double check		
			if($icd_code_tmp=="ICD-9" || $icd_code2=="ICD-9"){
				$ret = 9;
			}else{		
				$ret = 1;
			}
		}
		$ret = 1; //will always in r8
		//update chart_master_table
		if(!empty($pid) && !empty($fid)){
			$sql = "UPDATE chart_master_table SET enc_icd10='".$ret."' WHERE patient_id = '".$pid."' AND id='".$fid."' ";
			$row=sqlQuery($sql);
		}

		return $ret;	
	}
	
	//Modify notes--
	//Function get Modified Notes: Checking + output to save
	function getModiNotes($prvSum, $prvWnl, $crSum, $crWnl, $prvUid, $defWNL="WNL" ){
		$uid = $_SESSION["authId"];
		$ret="";
		//Checking
		if( $prvUid!=$uid){		
			//if($prvSum!=$crSum || $prvWnl!=$crWnl){
				//Make Modi Note
				$mdSum = $prvSum;
				if(empty($prvSum)&&!empty($prvWnl)){
					$mdSum = $defWNL;
				}			
				
				//
				$oUser = new User($uid);
				$nm = $oUser->getName(1);
				
				$str_usr_time="";
				$str_usr_time .= "".date("m-d-y H:i")." ";
				$str_usr_time .= $nm;			
				
				$ret="<div>
					".$str_usr_time."<br/>
					".nl2br($mdSum)."
					</div>";
			//}
		}	
		return $ret;
	}
	//Modify notes Array--
	//Function get Modified Notes Array: Checking + output to save
	function getModiNotesArr($prvSum,$crSum,$prvUid,$site,$dbArr, $examDate ){
		$uid = $_SESSION["authId"];
		$arr = array();
		$seri_modi_note_LidsOdArr = $dbArr;
		if( $prvUid!=$uid && $prvSum != $crSum && $prvSum!=''){	
			if($dbArr!=''){
				$row_modi_note_LidsOdArr = unserialize($dbArr);
			}
			else{ 
				$row_modi_note_LidsOdArr = array();
			}
			$arrOD=array(); $arrOS=array();
			foreach($row_modi_note_LidsOdArr as $index=>$arrTmp){
				foreach($arrTmp as $key=>$arrOdOs){
					if($key == "OD")
					$arrOD[$index] = $arrOdOs;
					else if($key == "OS")
					$arrOS[$index] = $arrOdOs;
					
				}
			}
			if($site == "OD"){
				$tmp_arr = array_keys($arrOD);
				$index_to_unset = end($tmp_arr);
				$arrEle = array_pop($arrOD);
			}else if($site == "OS"){
				$tmp_arr = array_keys($arrOS);
				$index_to_unset = end($tmp_arr);
				$arrEle = array_pop($arrOS);
			}
			if($arrEle['modi_by'] == $uid){
				unset($row_modi_note_LidsOdArr[$index_to_unset]);
			}else{
				$prev_time = $row_modi_note_LidsOdArr[$index_to_unset][$site]["time"];
				if(empty($prev_time)){ $prev_time = date("m-d-y H:i", strtotime($examDate)); }
			}
			
			$str_usr_time = date("m-d-y H:i");
			$arr[$site]['time'] = $str_usr_time;
			$arr[$site]['preVal'] = $prvSum;
			$arr[$site]['val'] = $crSum;
			$arr[$site]['modi_by'] = $uid;
			$arr[$site]['preUsr'] = $prvUid;
			$arr[$site]['preTime'] = $prev_time;
			
			if( preg_replace('/\s+/', '',$arr[$site]['val']) !=  preg_replace('/\s+/', '',$arr[$site]['preVal'])){
				array_push($row_modi_note_LidsOdArr, $arr);
			}
			if(count($row_modi_note_LidsOdArr)>0){
				$seri_modi_note_LidsOdArr = serialize($row_modi_note_LidsOdArr);
			}
		}
		return $seri_modi_note_LidsOdArr;
	}
	function getModiNotes_drawing($uid,$dt,$dbfield){
		$oUser = new User($uid);
		$nm = $oUser->getName(1);
		$str_row_modify_Draw="<div>Drawing modified by ".$nm." on ".$dt."</div>";
		$tmp=" $dbfield = CONCAT('".sqlEscStr($str_row_modify_Draw)."',$dbfield) ";
		return $tmp;
	}
	//Modify notes--
	
	function alignWnlVals($arr,$eye){
		$flgAlignOd=$flgAlignOs=0;
		foreach($arr as $key=>$val){
		
			$val[3] = trim($val[3]);
			$val[4] = trim($val[4]);
		
			if(empty($val["0"])){
				if(empty($flgAlignOd)){
				if(($eye=="OU"||$eye=="OD")&&empty($val["1"])){
					$flgAlignOd=1;
				}
				}
				if(empty($flgAlignOs)){
				if(($eye=="OU"||$eye=="OS")&&empty($val["2"])){
					$flgAlignOs=1;
				}
				}
			}else{
				if(empty($flgAlignOd)){
				if(($eye=="OU"||$eye=="OD")&&empty($val["1"])&&empty($val["3"])){
					$flgAlignOd=1;
				}
				}
				if(empty($flgAlignOs)){
				if(($eye=="OU"||$eye=="OS")&&empty($val["2"])&&empty($val["4"])){
					$flgAlignOs=1;
				}
				}	
			}
		}
		
		return array("od"=>$flgAlignOd,"os"=>$flgAlignOs);
	}
	
	function toggleWNL($pos,$sumOd,$sumOs,$wOd,$wOs,$w,$eye){
		if(!empty($pos)){
			$sumOd = trim($sumOd);
			$sumOs = trim($sumOs);
		
			if($eye=="OU" || $eye=="OD"){
				if(empty($sumOd)){
					$wOd = empty($wOd) ? "1" : "0";
				}
			}
			
			if($eye=="OU" || $eye=="OS"){
				if(empty($sumOs)){
					$wOs = empty($wOs) ? "1" : "0";
				}
			}

		}else{
			if($eye=="OU"){
				if(!empty($wOd) && !empty($wOs)){
					$wOd = $wOs = "0";
				}else{
					$wOd = $wOs = "1";
				}
			}else{
				if($eye=="OD"){
					$wOd = empty($wOd) ? "1" : "0";
				}else if($eye=="OS"){
					$wOs = empty($wOs) ? "1" : "0";
				}
			}
		}

		if($wOd=="1" && $wOs=="1"){
			$w="1";
		}else{
			$w="0";
		}

		return array($wOd,$wOs,$w);
	}
	
	function get_last_opr_id($db_last_opr_id,$uid){
		/*
		if($db_last_opr_id == "" || $db_last_opr_id == 0){
			$last_opr_id = "";
		}else 
		*/
		if($uid == $_SESSION["authId"]){
			$last_opr_id = $db_last_opr_id;
		}else if($uid != $_SESSION["authId"]){
			$last_opr_id = $uid;
		}	
		return $last_opr_id;
	}
	
	// Function check value --
	function hasArrVal($str , $arr, $ty=""){

		if(!empty($str)){ $str.=";";  }

		$ret = 0;
		if(($arr) > 0){
			foreach($arr as $key => $val){
				if($ty == "2"){
					if(strpos($str,$val.":") !== false){
						$ret = 1;
						break;
					}
				}else{
					if(strpos($str,$val.";") !== false || strpos($str,$val.".") !== false ){
						$ret = 1;
						break;
					}
				}
			}
		}
		return $ret;
	}

	function hasArrVal_2($arr){
		$tmp = implode("",$arr);
		return (!empty($tmp)) ? 1 : 0;
	}
	// Function check value --
	
	function getUTBgColor($ut_elem){
		$arrbgColor=array();
		if(!empty($ut_elem)){				
			$arrClr = User::getProviderColors(1);				
			$arUt = explode("|",$ut_elem);
			foreach($arUt as $k1 =>$v1){
				$arVt = explode("@",$v1);					
				if(!empty($arVt[0]) && !empty($arVt[1])){
					$arE=explode(",",$arVt[1]);
					foreach($arE as $ke => $ve){
						if(!empty($ve)){
							$arrbgColor[$ve] = $arrClr[$arVt[0]];
						}
					}
				}					
			}
		}
		return $arrbgColor;
	}
	
	function enter_phy_view(){		
		if(isset($_SESSION["flg_phy_view"])&&!empty($_SESSION["flg_phy_view"])){
			$_SESSION["flg_phy_view"]="";
			unset($_SESSION["flg_phy_view"]);					
		}else{
			$_SESSION["flg_phy_view"] = 1;
		}
		header("Location: ".$GLOBALS['rootdir']."/chart_notes/work_view.php");
	}
	
	function get_pt_alert($pid,$fid){		
		$html_pt_alerts="";
		$alertToDisplayAt="Chart Note";
		//require(dirname(__FILE__)."/../CLSAlerts.php");
		$OBJPatSpecificAlert = new CLSAlerts();
		if(trim($_SESSION['alertShowForThisSession']) != "Cancel"){
			$html_pt_alerts .= ($OBJPatSpecificAlert->getAdminAlert($pid,$alertToDisplayAt,$fid,"445px","100px","chartNote"));
			$html_pt_alerts .= ($OBJPatSpecificAlert->getPatSpecificAlert($pid,$alertToDisplayAt));
		}
		if($_SESSION['alertImmShowForThisSession'] == ""){
			$html_pt_alerts .= ($OBJPatSpecificAlert->ImmunizationAlerts($pid,$fid,"chartNote"));
		}
		if($_SESSION['alertShowForMedication']==''){ 
			$html_pt_alerts .= ($OBJPatSpecificAlert->alertMedications($pid,$alertToDisplayAt,"445px","100px"));
			$_SESSION['alertShowForMedication']='DONE';
		}
		
		if(!empty($html_pt_alerts)){
		//echo ($OBJPatSpecificAlert->autoSetDivLeftMargin("240","675"));
		$html_pt_alerts .= ($OBJPatSpecificAlert->autoSetDivLeftMargin("300","365","300"));
		$html_pt_alerts .= ($OBJPatSpecificAlert->autoSetDivTopMargin("350","10","180"));
		$html_pt_alerts .= ($OBJPatSpecificAlert->writeJS());
		}
		return $html_pt_alerts;
	}
	
	//Auto Finalize --
	/*
	function isFirstLogger(){
		$curDt = date("Y-m-d");
		$sql = "SELECT COUNT(*) AS num FROM user_firstLogger WHERE dt_cur='".$curDt."' ";
		$res = $this->db->Execute($sql);
		if($res != false && $res->fields["num"]>=1){
			return false;
		}
		return true;
	}
	*/
	
	function isFirstLogger_ChartsFinalized(){
		$curDt = date("Y-m-d");
		$sql = "SELECT COUNT(*) AS num FROM user_firstlogger WHERE dt_cur='".$curDt."' AND chartsFinalized='1' ";
		$res = sqlQuery($sql);
		if($res != false && $res["num"]>=1){
			return true;
		}
		return false;
	}
	/*
	function setFirstLogger(){
		$curDt = date("Y-m-d");
		$sql = "INSERT INTO user_firstlogger(id, uid, dt_cur) 
				VALUES (NULL,'".$this->uid."','".$curDt."' ) ";
		$res = $this->db->Execute($sql);
	}
	*/
	function setFirstLogger_ChartsFinalized(){
		$curDt = date("Y-m-d");
		$sql = "UPDATE user_firstlogger SET chartsFinalized='1' WHERE  dt_cur='".$curDt."'	 ";
		$res = sqlQuery($sql);
	}

	//Auto Finalize and Warn users about Chart Notes
	function autoFinalizeCharts(){
		//		
		
		//check if charts are auto finalized for today
		$ttmp = $this->isFirstLogger_ChartsFinalized();
		if($ttmp){	return 0; }else{
			$this->setFirstLogger_ChartsFinalized();			
		}
		
		include_once(dirname(__FILE__)."/wv_functions.php");
		include_once(dirname(__FILE__)."/Facility.php");
		include_once(dirname(__FILE__)."/User.php");
		include_once(dirname(__FILE__)."/Patient.php");
		include_once(dirname(__FILE__)."/ChartNote.php");
		include_once(dirname(__FILE__)."/MedHx.php");
		include_once(dirname(__FILE__)."/ChartLog.php");
		include_once(dirname(__FILE__)."/ChartAP.php");
		include_once(dirname(__FILE__)."/Signature.php");
		include_once(dirname(__FILE__)."/../SaveFile.php");
		
		//		
		//Get Facility Timers
		$oFacility = new Facility("HQ");
		$arrChartTimer = $oFacility->getChartTimers();
		
		//Get Unfinalized Charts info
		$sql = "SELECT 
				c1.date_of_service,
				c1.id,c1.patient_id,c1.providerId,c1.create_dt
				FROM chart_master_table c1 ".
				//"LEFT JOIN chart_left_cc_history c2 ON c2.form_id=c1.id".
				"WHERE c1.finalize='0'  "; //AND c1.providerId!=''
		$rez = sqlStatement($sql);
		for($i=1;$row=sqlFetchArray($rez);$i++){
			
			$dos = $row["date_of_service"];
			if(empty($dos) || $dos == "0000-00-00"){
				$dos = wv_formatDate($row["create_dt"],0,0,"insert"); 
			}
			$pid = $row["patient_id"];
			$proId = $row["providerId"];
			$formId = $row["id"];		
			
			//
			$strFinal = ($arrChartTimer["finalize"] * 24)." hours ";
			
			//Check if need to finalize
			if(isDtPassed($dos, $strFinal)){
				//
				if(!empty($proId)){
					$ousr= new User($proId);					 
					$uTpe = $ousr->getUType(1);
				}
				
				if(empty($proId)||$uTpe!=1){ //Empty Or No Physician
					$opt = new Patient($pid);
					if(empty($proId)) $proId="-1";//"987654321"; //Not Exist static id given to pass user checks
					$proIdDefault=$opt->getPro4CnIn($proId,"",$dos);
				}else{
					$proIdDefault=$proId;
				}
				
				if(!empty($proIdDefault)){ //Auto Finalize Only if Default Provider Exists
					//CN Obj
					$oChartNote = new ChartNote($pid,$formId);
					$oChartNote->autoFinalize($proIdDefault);
				}
			}			
		}
	}	
	//End Auto Finalize --
	
	function get_prv_chart_img($pid){
		$s="";
		//Check File Type
		$sql= "select file_path, upload_comment, doc_title, pdf_url from ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl where scan_doc_id ='".$_GET["id"]."' ";
		$row=sqlQuery($sql);
		if($row != false && !empty($row["file_path"]))
		{
			$oSaveFile = new SaveFile($pid);
			$filePath = $oSaveFile->getFilePath($row["file_path"], "w");
			
			if(!empty($row["upload_comment"])){$caption = $row["upload_comment"];}
			else if(!empty($row["doc_title"])){$caption = $row["doc_title"];}
			else if(!empty($row["pdf_url"])){$caption = $row["pdf_url"];}			
			
			if(stripos($filePath,".jpg")!=false || stripos($filePath,".jpeg")!=false || stripos($filePath,".gif")!=false || stripos($filePath,".png")!=false){
				$str_file_path = "<img class=\"modal-content\" id=\"chart_img\" src=\"".$filePath."\" style=\"margin: auto;display: block;width: 80%;\">";
			}else if(stripos($filePath,".pdf")!=false){
				$str_file_path = "<iframe class=\"modal-content\" id=\"chart_img\" src=\"".$filePath."\" style=\"margin: auto;display: block;width: 80%; height:90%;\"></iframe>";
			}			
			
			$s="<div id=\"chart_img_modal\" class=\"modal\" style=\"display: block;position: fixed;z-index: 1001;padding-top: 10px;left: 0;top: 0;width: 100%;height: 100%;overflow: auto;background-color: rgb(0,0,0);background-color: rgba(0,0,0,0.9);\">".
				"<span class=\"close\" style=\"position: absolute;top: 15px;right: 35px;color: #ffffff;font-size: 40px;font-weight: bold;transition: 0.3s;z-index: 1003;opacity:1;\">&times;</span>".
				$str_file_path.
				"<div id=\"caption_chart_img\" style=\"display: block;width: auto;text-align: center;color: #ccc;padding: 10px 0;height: 150px;z-index: 1003;\">".$caption."</div>".
				"</div>";
			echo $s;			
		}else{
			echo "No File Found.";
		}	
	}
	
	function get_min_file($ar, $t){
		$str="";
		$vrsn = $GLOBALS['CHART_APP_VERSION'];		
		if(count($ar)>0){
			$dpth_w = $GLOBALS['webroot']."/cache";
			$dpth_cache = $GLOBALS['fileroot']."/cache";
			
			if($t=="js"){				
				$dpth = $GLOBALS['fileroot']."/library/js";
				$ext=".js";
				//$lib=$GLOBALS['fileroot']."/library/min/jsmin.php";
			}
			else if($t=="css"){
				$dpth = $GLOBALS['fileroot']."/library/css";				
				$ext=".css";
				//$lib=$GLOBALS['fileroot']."/library/min/cssmin-v3.0.1-minified.php";
			}
			
			foreach($ar as $k => $v){
				if(!empty($v) && file_exists($dpth."/".$v)){
					$stm = filemtime ( $dpth."/".$v );
					$vc = basename($dpth."/".$v);					
					$vc=str_replace($ext,"_v".$vrsn.$ext,$vc);
					//$vc=$dpth."/".$vc;
					if(!file_exists($dpth_cache."/".$vc) || (file_exists($dpth_cache."/".$vc) && $stm != filemtime ( $dpth_cache."/".$vc ))){ // && !empty($vrsn)						
						//create min
						//require_once($lib);
						$js = '';
						$tmp="";
						$tmp = file_get_contents($dpth."/".$v);							
						if($t=="js"){$tmp = JSMin::minify($tmp);}
						else if($t=="css"){
							//$tmp = CssMin::minify($tmp);  
							$ar_chk = array("url('../fonts", "url(\"../../library/images", "url(../../library/images", "url(../images");
							$ar_rep = array("url('../library/fonts", "url(\"../library/images", "url(../library/images", "url(../library/images");
							$tmp = str_replace($ar_chk, $ar_rep, $tmp);
						}	
						$js .= $tmp;
						
						$fp = fopen($dpth_cache."/".$vc,'w');	
						fwrite($fp,$js);
						fclose($fp);
						//
						touch($dpth_cache."/".$vc, $stm);						
					}
					//else{
					//	$vc=$v;
					//}
					if($t=="js"){$str.="<script type=\"text/javascript\" src=\"".$dpth_w."/".$vc."\"></script>";}
					else if($t=="css"){$str.="<link href=\"".$dpth_w."/".$vc."\" rel=\"stylesheet\">";}	
				}
			}			
		}			
		
		return $str;
	}
	
	function showAnotherChartNote($flgnopt=""){
		###
		#print_r($_POST);
		#exit;
		###
		$finalize_id = $_REQUEST["hd_finalize_id"];
		$opCode = $_REQUEST["elem_openForm"];
		$memo = $_REQUEST["memo"];

		$_SESSION["form_id"] = "";
		$_SESSION["form_id"] = NULL;
		unset($_SESSION["form_id"]);

		$_SESSION["scanned_chart_image"] = "";
		$_SESSION["scanned_chart_image"] = NULL;
		unset($_SESSION["scanned_chart_image"]);
		
		if(!empty($flgnopt)){
			$elem_ptId = $_REQUEST["elem_ptId"];
			if(!empty($elem_ptId)){
				$_SESSION["patient"] = $elem_ptId;
			}
		}		

		$_SESSION["finalize_id"] = $finalize_id;

		//"../main/main_screen.php?imedic_R22_patient=".$_SESSION["patient"]."&imedic_R2_type=work_view"
		
		$qStr = $GLOBALS['cndir']."/work_view.php";
		if(!empty($memo)){ $qStr .= "?memo=$memo"; }
		
		header("Location: ".$qStr);		
	}	
	
	function break_glass(){
		//Break Glass
		if(trim($_REQUEST["rp_reason_code"])!="" && trim($_REQUEST["rp_reason_comments"])!="" && !empty($_REQUEST['patient_searched'])){
			$insertQuery=" insert into restricted_reasons set ".
						 "patient_id='".$_REQUEST['patient_searched']."', ".
						 "operator_id='".$_SESSION['authId']."', ".
						 "form_id='".$_SESSION['form_id']."', ".
						 "access_reason='".addslashes($_REQUEST["rp_reason_comments"])."', ".
						 "access_date='".wv_dt('now')."',scp_code_id='".$_REQUEST["rp_reason_code"]."' ";
			$res=imw_query($insertQuery) or die(imw_error());
			//Set Session break glass--
			$_SESSION["glassBreaked_ptId"] = $_REQUEST['patient_searched'];
		}
		echo "OK";
	}
	
	function get_menu_options(){
		$oAdmn = new Admn();
		//Visit type : menu
		$arrPtVisit=$arrPtTesting=array();
		$arrPtVisit=$oAdmn->wv_getPtVisit(0,1);
		$arrPtTesting=$oAdmn->wv_getPtTesting(0,1); //array("Empty", "Gonio", "Disc Photo", "Pachy","VF", "NFA/HRT", "Color Plates", "Other" );
		$data_visit_testing = wv_getMenuHtmlHidden(array("VISIT"=>$arrPtVisit, "TESTING"=>$arrPtTesting)," id=\"divMenuVisitTest\" ", 2 );
		echo $data_visit_testing;
	}

	function get_sb_menu(){
		$wh = trim($_GET["wh"]);
		if($wh=="menu_cpt"){
			$ocpt = new CPT();
			$ocpt->get_menu_html();
		}else if($wh=="menu_mod"){
			$omod = new Modifier();
			$omod->get_menu_html();
		}else if($wh=="menu_visit_type"){
			$this->get_menu_options();
		}else if($wh=="menu_Lasik_trgt_Excimer" || $wh=="menu_Lasik_trgt_mode"){
			VisLasik::get_menu_html();
		}else{			
			Vision::get_menu_html();
		}
	}
	
	function load_pvc($patient_id){
		if(!empty($_GET["eid"])){
			patient_communication($patient_id,'view_active', 'get_edit_vals',$_GET);
		}else{
		$opt = new Patient($patient_id);
		$pt_name = $opt->getName(6);
		include($GLOBALS['incdir']."/chart_notes/view/pvc_model.php");
		}
	}
	
	function get_iportal_req_changes_alert($pid){
		if(empty($pid)){return "";}
		$title=$msg="";
		$iportal_ch_qry = "SELECT id FROM iportal_req_changes WHERE pt_id = '".$pid."' and del_status = 0 and is_approved = 0 and tb_name in('social_history','immunizations','lists','general_medicine','ocular')";
		$iportal_ch_obj = imw_query($iportal_ch_qry);
		if(imw_num_rows($iportal_ch_obj)>0) {
			$qry_rs = imw_query("select concat(lname,', ',fname,' - ',id) as patient_details from patient_data where id='".$_SESSION['patient']."' LIMIT 0,1"); $res_pat=imw_fetch_assoc($qry_rs);
			$title = "Patient Alert(s) for ".$res_pat['patient_details']."";
			$msg="<table cellspacing='0' cellpadding='0'><tr><td style='vertical-align:top;font-size:30px;color:#1b9e95'><i class=\"glyphicon glyphicon-bell\"></i></td><td style='vertical-align:middle;padding-left:10px;font-weight:500;text-transform:capitalize;font-size:18px;'>updated clinical information available</td></tr></table>";
		}
		$htm ="";
		if(!empty($msg)){$htm = "top.fAlert(".json_encode($msg).",".json_encode($title).");";}		
		return $htm;
	}
	
	function line_chart($graph_name,$graph_data,$graph_clr=array()){	
		$key_i=0;$kk=0;
		$mxln = count($graph_name);
		foreach($graph_data[$mxln] as $key=>$val){
			$line_payment_tot_arr[$key]["category"]=$val;
		}

		foreach($graph_data as $key=>$val){
			if($key!=$mxln){	
				$key_i++;
				$title="";
				$title=$graph_name[$key];
				
				
				$tmp_ar=array("alphaField"=> "C",
					"balloonText"=> "[[title]] of [[category]]: [[value]]",
					"bullet"=> "round",
					"bulletField"=> "C",
					"bulletSizeField"=> "C",
					"closeField"=> "C",
					"colorField"=> "C",
					"customBulletField"=> "C",
					"dashLengthField"=> "C",
					"descriptionField"=> "C",
					"errorField"=> "C",
					"fillColorsField"=> "C",
					"gapField"=> "C",
					"highField"=> "C",
					"id"=> "AmGraph-$key_i",
					"labelColorField"=> "C",
					"lineColorField"=> "C",
					"lowField"=> "C",
					"openField"=> "C",
					"patternField"=> "C",
					"title"=> $title,
					"valueField"=> "column-$key_i",
					"xField"=> "C",
					"yField"=> "C");
				
				if(count($graph_clr)>0){
					$tmp_clr=$graph_clr[$key];
					if(!empty($tmp_clr)){
						$tmp_ar["lineColor"] = $tmp_clr;
					}
				}
				
				$line_pay_graph_var_arr[] = $tmp_ar;	
				
				foreach($graph_data[$key] as $key2=>$val2){
					if($graph_data[$key][$key2]>0){	
						$line_payment_tot_arr[$key2]["column-".$key_i]=$graph_data[$key][$key2];
					}
					$kk++;
				}
			}
		}
		
		$return_arr['line_payment_tot_detail']=$line_payment_tot_arr;
		$return_arr['line_pay_graph_var_detail']=$line_pay_graph_var_arr;
		return $return_arr;
	}
}
?>