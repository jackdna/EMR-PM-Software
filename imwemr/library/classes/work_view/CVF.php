<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: CVF.php
Coded in PHP7
Purpose: This is a class file for CVF test providing some operations like insert, previous values and other.
Access Type : Include file
*/
?>
<?php
//CVF.php
class CVF extends ChartNote{
	public $uid;
	private $examName,$tbl,$xmlFileOd,$xmlFileOs,$divSE;
	public function __construct($pid,$fid=""){
		parent::__construct($pid,$fid);
		$this->tbl="chart_cvf";
		$this->examName="CVF";
		$this->uid = $_SESSION["authId"];
	}

	function isRecordExists($a="",$b="",$c="",$d=""){
		return parent::isRecordExists($this->tbl,"formId", "patientId");
	}

	function getRecord($sel=" * ",$a="",$b="",$c="",$d=""){
		return parent::getRecord($this->tbl,$sel,"formId", "patientId");
	}

	function getLastRecord($sel=" * ",$LF="0",$dt="",$a="",$b="", $c="" ){
		return parent::getLastRecord($this->tbl,"formId",$LF,$sel,$dt);
	}

	function insertNew(){
		if(!empty($this->pid) && !empty($this->fid)){
		$sql = "INSERT INTO ".$this->tbl." (cvf_id, formId, patientId, uid)
				VALUES (NULL, '".$this->fid."','".$this->pid."','".$this->uid."') ";
		$res=sqlInsert($sql);
		$return= $res;
		}else{ $return=0; }
		return $return;
	}

	function carryForward(){
		$res = $this->getLastRecord(" c2.cvf_id, c2.drwpth_od, c2.drwpth_os ","1");
		if($res!=false){
			$Id_LF = $res["cvf_id"];
			
			//
			$oSaveFile = new SaveFile($this->pid);
			
			
			$pth="";//$GLOBALS['incdir']."/main/uploaddir";
			if(!empty($res["drwpth_od"])){
				$destpath_od = "/PatientId_".$this->pid."/CVF/".
							time()."_OD.png";
				$oSaveFile->copyFileP2P($res["drwpth_od"], $destpath_od);
			}
			
			if(!empty($res["drwpth_os"])){
				$destpath_os = "/PatientId_".$this->pid."/CVF/".
							time()."_OS.png";
				$oSaveFile->copyFileP2P($res["drwpth_os"], $destpath_os);
			}
			
		}
		//Insert
		$insertId = $this->insertNew();	
		
		//CopyLF
		$ignoreFlds = "formId,uid,nochange,wnl_value";
		if(!empty($Id_LF)) $this->carryForwardExe($this->tbl,$insertId,$Id_LF,$ignoreFlds,$this->examName,'cvf_id');
		
		//
		//Carry forward images
		if(!empty($insertId) && (!empty($destpath_od)||!empty($destpath_os))){
			$sql="UPDATE ".$this->tbl." SET drwpth_od  = '".$destpath_od."', drwpth_os = '".$destpath_os."'  WHERE cvf_id='".$insertId."' ";
			sqlQuery($sql);
		}		
	}
	
	function set2PrvVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" cvf_id ");
			if($res1!=false){
				$Id = $res1["cvf_id"];
			}
			
			$res = $this->getLastRecord(" c2.cvf_id,c2.drwpth_od, c2.drwpth_os ","1");		
			if($res!=false){
				$Id_LF = $res["cvf_id"];
				
				//
				$oSaveFile = new SaveFile($this->pid);
				
				$pth=""; //$GLOBALS['incdir']."/main/uploaddir";
				if(!empty($res["drwpth_od"])){
					$destpath_od = "/PatientId_".$this->pid."/CVF/".
								time()."_OD.png";
					$oSaveFile->copyFileP2P($res["drwpth_od"], $destpath_od);
				}
				
				if(!empty($res["drwpth_os"])){
					$destpath_os = "/PatientId_".$this->pid."/CVF/".
								time()."_OS.png";
					$oSaveFile->copyFileP2P($res["drwpth_os"], $destpath_os);
				}
				
			}

			//CopyLF
			$ignoreFlds = "formId,uid,nochange,wnl_value";
			if(!empty($Id_LF)&&!empty($Id)){
				$this->carryForwardExe($this->tbl,$Id,$Id_LF,$ignoreFlds);
				$this->setStatus("",$this->tbl);
			}else if(!empty($Id)){ //when no previous exam
				$this->resetVals(); //empty exam values 
			}
			
			//Carry forward images
			if(!empty($Id) && (!empty($destpath_od)||!empty($destpath_os))){
				$sql="UPDATE ".$this->tbl." SET drwpth_od  = '".$destpath_od."', drwpth_os = '".$destpath_os."'  WHERE cvf_id='".$Id."' ";
				sqlQuery($sql);
			}			
		}	
	}

	//Reset
	function resetVals(){
		if($this->isRecordExists()){
			$res1 = $this->getRecord(" cvf_id ");
			if($res1!=false){
				$Id = $res1["cvf_id"];
			}

			//CopyLF
			$ignoreFlds = "formId,uid,patientId";
			if(!empty($Id)){
				$this->resetValsExe($this->tbl,$Id,$ignoreFlds,$this->examName,'cvf_id');
				$this->setStatus("",$this->tbl);
			}
		}else{
			//
			$this->insertNew();
		}	
	}
	
	//
	function isNoChanged(){
		$res= $this->getRecord("nochange");
		if($res!=false){
			if( !empty($res["nochange"]) ){
				return true;
			}		
		}
		return false;
	}
	
	function getCvfSum($strXml){
		$ret = "";
		$oExamXml = new ExamXml();
		$arrMain = $oExamXml->getXmlMenuArray($strXml);
		if(count($arrMain) > 0){
			foreach($arrMain as $var => $val){
				//echo $var." &nbsp;&nbsp; ";
				//print_r($val);
				//echo "<br><br>";
				if(($val["type"] == "close") && ($val["level"] == "3")){
					$ret .= "<br>";
				}
				if($val["type"] == "complete"){
					if($val["attributes"]["elem_type"] == "CB"){
						$ret .= ($val["value"] == "1") ? $val["attributes"]["label"].", " : "";
					}else if($val["attributes"]["elem_type"] == "TA"){
						$ret .= ($val["value"] != "") ? "Comments: ".$val["value"] : "";
					}
					//br
					if($val["level"] == "3"){
						$ret .= "<br>";
					}
				}
			}
		}
		// remove <br>;
		$ret = trim(preg_replace("/(<br>)+/","<br>",$ret));
		$ret = trim(preg_replace("/(^(<br>)*)|((<br>)*$)/","",$ret));
		if($ret == "<br>"){
			$ret = "";
		}
		$ret = trim(preg_replace("/,$/","",$ret));
		return addslashes($ret);
	}
	
	function save_wnl(){
		// make WNL			
		$wnl_value=$wnl_phrase="";
		
		//Check and Add record		
		$patientId=$this->pid;
		$form_id=$this->fid;
		if(!$this->isRecordExists()){
			$this->carryForward();				
		}
		$oExamXml = new ExamXml();
		$arXmlFiles = $oExamXml->getExamXmlFiles("CVF");
		/*
		//Get status string --
		$statusElem="";
		if($elem_noChangeCVF==1) $statusElem=se_elemStatus($w,"1","","1","1");
		//Get status--
		*/
		$wnl="Yes";
		
		$elem_fullOs = $_POST['elem_fullOs']="1";
		$elem_fullOd = $_POST['elem_fullOd']="1";

		//$cvfOd =
		$menuName = "confrontationFieldOd";
		$menuFilePath = $arXmlFiles["od"];
		$cvfOd = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);		
		$sumCvfOd = $this->getCvfSum($cvfOd);
		$cvfOd = addslashes($cvfOd);

		//$cvfOs =
		$menuName = "confrontationFieldOs";
		$menuFilePath = $arXmlFiles["os"];
		$cvfOs = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
		$sumCvfOs = $this->getCvfSum($cvfOs);
		$cvfOs = addslashes($cvfOs);
		
		//WNL
		$wnl_value=$this->getExamWnlStr("CVF");
		$wnl_phrase = ", wnl_value='".imw_real_escape_string($wnl_value)."' ";
		
		//
		$sql = "UPDATE chart_cvf SET 
				cvfOd = '".$cvfOd."',
				cvfOs = '".$cvfOs."',
				drawOd = '',
				drawOs = '',
				wnl = '".$wnl."',						
				summaryOd = '$sumCvfOd',
				summaryOs = '$sumCvfOs',
				wnlOSHiddden = '".$wnl."',
				wnlODHiddden = '".$wnl."',
				uid='".$_SESSION["authId"]."' ".$wnl_phrase."
				WHERE formId='".$form_id."' AND patientId='".$patientId."' ";
		$res = sqlQuery($sql);
	}
	
	function save_no_change(){
		//-----
		//$elem_noChangeCVF = $_POST["elem_noChangeCVF"];
		//Check and Add record		
		$patientId = $this->pid;
		$form_id = $this->fid;
		
		if(!$this->isRecordExists()){
			$this->carryForward();
			$elem_noChangeCVF=1;
		}else if(!$this->isNoChanged()){
			$elem_noChangeCVF=1;
		}else{
			$this->set2PrvVals();
			$elem_noChangeCVF=0;
		}
		
		//----
		$sql = "UPDATE chart_cvf SET nochange='".$elem_noChangeCVF."',
				uid='".$_SESSION["authId"]."'
				WHERE formId='".$form_id."' AND patientId='".$patientId."' ";
		$res = sqlQuery($sql);		
		//-----	
	}
	
	function getCVFSection($finalize_flag){
		
		$patient_id = $this->pid;
		$form_id = $this->fid;
		
		$bggrey=" bggrey "; //default bg color
		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		
		//Obj
		$oSaveFile = new SaveFile($patient_id);
		$oExamXml = new ExamXml();
		
		//Dos
		$elem_dos=$this->getDos(1);
		
		//Get Form id based on patient id	
		$sql = "SELECT * FROM chart_cvf WHERE patientId = '$patient_id' AND formId = '$form_id' ";					
		$row = sqlQuery($sql);
		if(($row == false)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			//	SET MODE
			$cvf_mode = "new";
			$cvf_edid = "";	
			//New Records
			//$row = valuesNewRecordsCvf($patient_id);		
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
		}else{
			$cvf_mode = "update";
			$cvf_edid = $row["cvf_id"];
			$bggrey="";
			//Prev Values
			if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
				//Prev Values
				//valuesNewRecordsEomLa($patient_id, " * ", "1");
				$res = $this->getLastRecord(" * ",1,$elem_dos);
				if($res!=false){$row=$res;}else{$row=false;}
			}
		}
		if($row != false){
			$elem_cvfOd = stripslashes($row["cvfOd"]);
			$elem_cvfOs = stripslashes($row["cvfOs"]);			
			
			$arr_vals_cvfod = $oExamXml->extractXmlValue($elem_cvfOd);
			extract($arr_vals_cvfod);
			$arr_vals_cvfos = $oExamXml->extractXmlValue($elem_cvfOs);
			extract($arr_vals_cvfos);
			
			$elem_cvfOdDrawing = $row["drawOd"];
			$elem_cvfOsDrawing = $row["drawOs"];
			$elem_wnl_cvf = $row["wnl"];
			$elem_nochange_cvf = $row["nochange"];
			
			$wnlOSHiddden_cvf = $row["wnlOSHiddden"];
			$wnlODHiddden_cvf = $row["wnlODHiddden"];
			
			$sig_path_od = (!empty($row["drwpth_od"])) ? $oSaveFile->getFilePath($row["drwpth_od"],"w") : "" ;
			$sig_path_os = (!empty($row["drwpth_os"])) ? $oSaveFile->getFilePath($row["drwpth_os"],"w") : "" ;
			
		}		
		
		//include --
		//ob_start();
		$tmp = str_replace("\x", "\\x", $GLOBALS['incdir']);		
		include($tmp."/chart_notes/view/cvf.php");
		//$out2 = ob_get_contents();
		//ob_end_clean();
		//return $out2; 		
	}
	
	function getWorkViewSummery($post=array()){
		
		$echo="";
		//$oCVF = new CVF($patient_id,$form_id);
		$patient_id = $this->pid;
		$form_id = $this->fid;
		$sql =	"SELECT ".
				//c12-chart_cvf-------
				"c12.drawOd, c12.wnlODHiddden, c12.wnlOSHiddden, c12.wnl AS CVFwnl,".
				"c12.drawOs, c12.summaryOd AS cvfSumOd, c12.summaryOs AS cvfSumOs,  c12.cvf_id, ".
				"c12.cvfOd, c12.cvfOs,nochange, ".
				"c12.drwpth_od, c12.drwpth_os, c12.wnl_value ".
				//c12-chart_cvf-------
				"FROM chart_master_table c1 ".
				"LEFT JOIN chart_cvf c12 ON c12.formId = c1.id ".
				"WHERE c1.id = '".$form_id."' AND c1.patient_id='".$patient_id."' ";
		$row = sqlQuery($sql);		
		if($row != false){
			$elem_cvfOd = $row["cvfOd"];
			$elem_cvfOs = $row["cvfOs"];
			$CVFwnl = $row["CVFwnl"];
			$elem_cvfSumOd = $row["cvfSumOd"];
			$elem_cvfSumOs = $row["cvfSumOs"];
			$elem_cvf_id = $row["cvf_id"];
			$elem_drawOd = $this->isAppletDrawn($row["drawOd"]);
			$elem_drawOs = $this->isAppletDrawn($row["drawOs"]);
			$wnlODHiddden = $row["wnlODHiddden"];
			$wnlOSHiddden = $row["wnlOSHiddden"];
			$nochange = $row["nochange"]; //!empty($row["nochange"]) ? "checked='checked'" : "";
			$drwpth_od=$row["drwpth_od"];
			$drwpth_os=$row["drwpth_os"];
			$elem_wnl_value = $row["wnl_value"];			
		}
		
		if(empty($elem_cvf_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			$tmp = "";
			$tmp .= "	c2.drawOd, c2.drawOs, 
						c2.summaryOd AS cvfSumOd, c2.summaryOs AS cvfSumOs, 
						c2.drwpth_od, c2.drwpth_os, c2.wnl_value, 
						c2.cvf_id ";
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			if($row!=false){
			//$row = valuesNewRecordsCvf($patient_id, $tmp);
			//if($row != false){
				$elem_drawOd = $this->isAppletDrawn($row["drawOd"]);
				$elem_drawOs = $this->isAppletDrawn($row["drawOs"]);
				//$elem_cvf_id = $row["cvf_id"];
				$elem_cvfSumOd = $row["cvfSumOd"];
				$elem_cvfSumOs = $row["cvfSumOs"];
				$drwpth_od=$row["drwpth_od"];
				$drwpth_os=$row["drwpth_os"];
				$elem_wnl_value = $row["wnl_value"];
			}
			//BG
			$bgColor_Cvf = "gray_color";
		}
		
		$var1=$var2="";
		$tmpcvfSum = explode(",",str_replace("<br>","",$elem_cvfSumOd));//od
		if($elem_drawOd || !empty($drwpth_od) ){
			$var1 = 'ABN';
		}else if((in_array("Full",$tmpcvfSum))){
			$var1 = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("CVF");
		}else if( (in_array("Constriction",$tmpcvfSum)) || (in_array("Superotemporal Defect",$tmpcvfSum)) ||
				(in_array("Inferotemporal Defect",$tmpcvfSum)) || (in_array("Superonasal Defect",$tmpcvfSum)) ||
				(in_array("Inferonasal Defect",$tmpcvfSum)) || (in_array("Superior Half Defect",$tmpcvfSum)) ||
				(in_array("Inferior Half Defect",$tmpcvfSum)) || (strpos($elem_cvfSumOd, "Comments:")!==false)  ){
			$var1 = 'ABN';
		}else{
			$var1 = '';
		}
		
		$tmpcvfSum = explode(",",str_replace("<br>","",$elem_cvfSumOs));//os
		if($elem_drawOs || !empty($drwpth_os)){
			$var2 = 'ABN';
		}else if((in_array("Full",$tmpcvfSum))){
			$var2 = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("CVF", $patient_id, $form_id);
		}else if( (in_array("Constriction",$tmpcvfSum)) || (in_array("Superotemporal Defect",$tmpcvfSum)) ||
				(in_array("Inferotemporal Defect",$tmpcvfSum)) || (in_array("Superonasal Defect",$tmpcvfSum)) ||
				(in_array("Inferonasal Defect",$tmpcvfSum)) || (in_array("Superior Half Defect",$tmpcvfSum)) ||
				(in_array("Inferior Half Defect",$tmpcvfSum)) || (strpos($elem_cvfSumOs, "Comments:")!==false)  ){
			$var2 = 'ABN';
		}else{
			$var2 = '';
		}
		
		if($elem_cvfSumOd!="" || $elem_cvfSumOs!=""){
			if($elem_cvfSumOd!="Full" || $elem_cvfSumOs!="Full" || $elem_drawOd || $elem_drawOs || !empty($drwpth_os) || !empty($drwpth_od) ){					
				if(!isset($bgColor_Cvf)){
					$posCvf="positive";
				}else{
					$posCvf=$bgColor_Cvf;
				}	
			}else{
				if(!isset($bgColor_Cvf)){							
					$posCvf="wnl_lbl";
				}else{
					$posCvf=$bgColor_Cvf;
				}
			}
		}
		
		//
		$var1_s=$var1; $var2_s=$var2;
		if(strlen($var1_s)>=5){ $var1_s=substr($var1_s, 0,3)."..";  } //
		if(strlen($var2_s)>=5){ $var2_s=substr($var2_s, 0,3)."..";  } //
		
		//NoChnage
		$strNC = (!empty($nochange)) ? "Undo" : "NC";
		
		//app
		if(isset($post) && $post["webservice"] == "1"){
			$tmp=array();
			$oSaveFile = new SaveFile($patient_id);
			$drwpth_od = $oSaveFile->getFilePath($drwpth_od, 'http');
			$drwpth_os = $oSaveFile->getFilePath($drwpth_os, 'http');					
	
			$tmp["CVF"] = array("show_label_od"=>$var1, "show_label_os"=>$var2, "pos"=>$posCvf, "sumod"=>$elem_cvfSumOd, "sumos"=>$elem_cvfSumOs, "draw_od"=>$drwpth_od, "draw_os"=>$drwpth_os);
			//$tmp["htm"] = $str;
			return serialize($tmp);		
		}else{
			//return array('val_nc' => $strNC, 'css_pos' => $posCvf, 'od' => $var1_s, 'os' => $var2_s);			
			$str = "<div class=\" ".$posCvf." \">
					<div class=\"header\">
						<div class=\"hdr form-inline\">
							<ul class=\"list-inline\">
								<li><h2 class=\"clickable\" onclick=\"openPW('CVF')\">CVF</h2></li>
								<li><input type=\"button\" id=\"elem_btnWNLCVF\" class=\"wnl btn btn-xs\" value=\"WNL\" onClick=\"setWnlValues('CVF')\"></li>
								<li><input type=\"button\" id=\"elem_btnNoChangeCVF\" class=\"nc btn btn-xs\" value=\"".$strNC."\" onClick=\"autoSaveNoChange('CVF')\"></li>
							</ul>
						</div>
					</div>
					<div class=\"clearfix\"></div>
					<div class=\"exampd default text-center\">".	
						"<ul class=\"list-unstyled list-inline ".$posCvf."\" onClick=\"openPW('CVF')\">".
						"<li id=\"cvfShowOd\">".
						"<div class=\"hr\"></div>
						<div class=\"vr\"></div>".
						$var1_s.
						"</li>".
						"<li id=\"cvfShowOs\">".
						"<div class=\"hr\"></div>
						<div class=\"vr\"></div>".						
						$var2_s.
						"</li>".
						"</ul>".
					"</div>							
				</div>";
			return $str;
		}
	}
	
	//
	function save_form(){
		$patientId = $this->pid; //$_POST["elem_patientId"];
		$formId = $this->fid; //$_POST["elem_formId"];
		//	
		if(empty($patientId) || empty($formId)){ return; }
		
		//$oCVF= new CVF($patientId, $formId);
		$oSaveFile = new SaveFile($patientId);
		$oExamXml = new ExamXml();
		
		$elem_fullOs = $_POST['elem_fullOs'];
		$elem_fullOd = $_POST['elem_fullOd'];		
		$hd_cvf_mode =  $_POST["hd_cvf_mode"];
		$cvf_id =  $_POST["elem_cvfId"];
		$wnl =  $_POST["elem_wnl_cvf"];
		$nochange =  $_POST["elem_noChange_cvf"];
		
		//draw--
		$drawOd =  $_POST["elem_cvfOdDrawing"];
		$drawOs =  $_POST["elem_cvfOsDrawing"];
		
		$sql_sig_path_od=$sql_sig_path_os=$sig_path_od=$sig_path_os="";		
		if(isset($_POST["sig_dataapp_cvf_od_drawing"]) && !empty($_POST["sig_dataapp_cvf_od_drawing"])){
			$fileSize=strlen($_POST["sig_dataapp_cvf_od_drawing"]);
			
			if($fileSize > 326){ //
				$sig_path_od = $oSaveFile->mkHx2Img($_POST["sig_dataapp_cvf_od_drawing"],"CVF","OD");
			}
			
			//$sql_sig_path_od =", drwpth_od='".$sig_path_od."'";	
		}
		
		if(isset($_POST["sig_dataapp_cvf_os_drawing"]) && !empty($_POST["sig_dataapp_cvf_os_drawing"])){			
			$fileSize=strlen($_POST["sig_dataapp_cvf_os_drawing"]);
			if($fileSize > 326){ //
				$sig_path_os = $oSaveFile->mkHx2Img($_POST["sig_dataapp_cvf_os_drawing"],"CVF","OS");
			}	
			
			//$sql_sig_path_os =", drwpth_os='".$sig_path_os."'";			
		}
		//draw--		

		$wnlOSHiddden =  $_POST["wnlOSHiddden_cvf"];
		$wnlODHiddden =  $_POST["wnlODHiddden_cvf"];
		
		//
		$arXmlFiles = $oExamXml->getExamXmlFiles("CVF");
		
		//$cvfOd =
		$menuName = "confrontationFieldOd";
		$menuFilePath = $arXmlFiles["od"]; //dirname(__FILE__)."/xml/confrontationField_od.xml";
		$cvfOd = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
		$sumCvfOd = $this->getCvfSum($cvfOd);
		$cvfOd = sqlEscStr($cvfOd);

		//$cvfOs =
		$menuName = "confrontationFieldOs";
		$menuFilePath = $arXmlFiles["os"] ; //dirname(__FILE__)."/xml/confrontationField_os.xml";
		$cvfOs = $oExamXml->newXmlString($menuName,$strMenu,$menuFilePath);
		$sumCvfOs = $this->getCvfSum($cvfOs);
		$cvfOs = sqlEscStr($cvfOs);
		
		//check
		$cQry = "select cvf_id, drwpth_od, drwpth_os,wnl_value FROM chart_cvf WHERE formId='".$formId."' AND patientId = '".$patientId."' ";
		$row = sqlQuery($cQry);
		$hd_cvf_mode = ($row == false) ? "new" : "update";
		if($hd_cvf_mode == "new"){
			//WNL --
			$elem_wnl_value = $this->getExamWnlStr("CVF");
			
			$sql = "INSERT INTO `chart_cvf` SET
					cvfOd = '$cvfOd',
					cvfOs = '$cvfOs',
					drawOd = '$drawOd',
					drawOs = '$drawOs',
					wnl = '$wnl',
					nochange = '$nochange',
					patientId = '$patientId',
					formId = '$formId',
					summaryOd = '$sumCvfOd',
					summaryOs = '$sumCvfOs',
					wnlOSHiddden = '$wnlOSHiddden',
					wnlODHiddden = '$wnlODHiddden',
					uid='".$_SESSION["authId"]."',
					drwpth_od='".$sig_path_od."',
					drwpth_os='".$sig_path_os."',
					wnl_value='".sqlEscStr($elem_wnl_value)."'
					";
			//*/				
			$insertId = sqlInsert($sql);
		}else if($hd_cvf_mode == "update"){
			//wnl
			$elem_wnl_value=$row["wnl_value"];
			
			//Unlink prev drawings			
			$oSaveFile->unlinkfile($row["drwpth_od"]);
			$oSaveFile->unlinkfile($row["drwpth_os"]);			
			
			$sql = "UPDATE chart_cvf  `chart_cvf` SET
					cvfOd = '$cvfOd',
					cvfOs = '$cvfOs',
					drawOd = '$drawOd',
					drawOs = '$drawOs',
					wnl = '$wnl',
					nochange = '$nochange',
					formId = '$formId',
					summaryOd = '$sumCvfOd',
					summaryOs = '$sumCvfOs',
					wnlOSHiddden = '$wnlOSHiddden',
					wnlODHiddden = '$wnlODHiddden',
					uid='".$_SESSION["authId"]."',
					drwpth_od='".$sig_path_od."',
					drwpth_os='".$sig_path_os."'
					WHERE formId = '$formId' AND patientId = '$patientId' ";
			$res = sqlQuery($sql);
		}
		
		// Make chart notes valid
		$this->makeChartNotesValid();
		echo "0";	
	}
	
	
	
}

?>