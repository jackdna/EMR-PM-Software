<?php
class PupilLoader extends Pupil{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);	
	}
	function getPurgedHtm(){
		$htmPurge="";
		$sql = "SELECT ".
			 "c2.isPositive AS flagPosPupil, c2.wnl AS flagWnlPupil, c2.examinedNoChange AS chPupil, ".
			 "c2.sumOdPupil, c2.sumOsPupil, c2.pupil_id, ".
			 "c2.wnlPupilOd, c2.wnlPupilOs, c2.descPupil,c2.perrla, ".
			 "c2.statusElem AS se_pupil, c2.other_values, c2.purgerId, c2.purgeTime, c2.wnl_value ".
			 "FROM chart_master_table c1 ".
			 "INNER JOIN chart_pupil c2 ON c2.formId = c1.id  AND c2.purged!='0'  ".
			 "WHERE c1.id = '".$this->fid."' AND c1.patient_id = '".$this->pid."' ".
			 "ORDER BY c2.purgeTime DESC";
		$rz = sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rz);$i++){					
			
			$elem_noChangePupil=assignZero($row["chPupil"]);
			$elem_wnlPupil=assignZero($row["flagWnlPupil"]);
			$elem_posPupil=assignZero($row["flagPosPupil"]);
			//Pupil
			$elem_wnlPupilOd=assignZero($row["wnlPupilOd"]);
			$elem_wnlPupilOs=assignZero($row["wnlPupilOs"]);
			$elem_wnl_value=$row["wnl_value"];
			$elem_perrlaPupil = assignZero($row["perrla"]);
			$elem_se_Pupil = $row["se_pupil"];
			$elem_sumOdPupil = $row["sumOdPupil"];
			$elem_sumOsPupil = $row["sumOsPupil"];
			$elem_pupil_id = $row["pupil_id"];
			$elem_txtDesc_pupil = stripslashes(trim($row["descPupil"]));
			$elem_other_values = $row["other_values"];
			
			//Perrla
			if($elem_perrlaPupil == 1){
				$elem_sumOdPupil = $elem_sumOsPupil = "PERRLA";
			}
			
			//Wnl
			$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("Pupil"); //"PERRLA, -ve APD"
			list($elem_sumOdPupil,$elem_sumOsPupil) = $this->oOnload->setWnlValuesinSumm(array("wVal"=>$wnlString,
									"wOd"=>$elem_wnlPupilOd,"sOd"=>$elem_sumOdPupil,
									"wOs"=>$elem_wnlPupilOs,"sOs"=>$elem_sumOsPupil));
			//Color
			$cnm = $flgSe_Pupil_Od = $flgSe_Pupil_Os = "";
			if(!isset($bgColor_pupil)){
				//$elem_se_Pupil
				if(!empty($elem_se_Pupil)){
					$tmpArrSe = $this->se_elemStatus("PUPIL","0",$elem_se_Pupil);
					$flgSe_Pupil_Od = $tmpArrSe["Pupil"]["od"];
					$flgSe_Pupil_Os = $tmpArrSe["Pupil"]["os"];
				}
			}
			
			//Nochanged
			if(!empty($elem_se_Pupil)&&strpos($elem_se_Pupil,"=1")!==false){
				$elem_noChangePupil=1;
			}
			
			$arr=array();
			$arr["ename"] = "Pupil";
			$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"Pupil",
											"sOd"=>$elem_sumOdPupil,"sOs"=>$elem_sumOsPupil,
											"fOd"=>$flgSe_Pupil_Od,"fOs"=>$flgSe_Pupil_Os,
											"pos"=>$elem_posPupil));
			$arr["nochange"] = $elem_noChangePupil;
			$arr["oe"] = $oneeye;
			$arr["desc"] = htmlentities($elem_txtDesc_pupil);
			$arr["bgColor"] = "".$bgColor_pupil;
			$arr["other_values"] = $elem_other_values;
			$arr["purgerId"] = $row["purgerId"];
			$arr["purgeTime"] = $row["purgeTime"];
			$arr["exm_flg_se"] = array($flgSe_Pupil_Od,$flgSe_Pupil_Os);				
			$htmPurge .= $this->oOnload->getSummaryHTML_purged($arr);				
			
		}
		return $htmPurge;
	}
	
	function getWorkViewSummery($post){
	
		$oneeye = $post["oe"]; //one eye
	
		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);
		//---		
		
		$echo="";
		//c2-chart_pupil-------
					
		$sql = "SELECT ".
			 "c2.isPositive AS flagPosPupil, c2.wnl AS flagWnlPupil, c2.examinedNoChange AS chPupil, ".
			 "c2.sumOdPupil, c2.sumOsPupil, c2.pupil_id, ".
			 "c2.wnlPupilOd, c2.wnlPupilOs, c2.descPupil,c2.perrla, ".
			 "c2.statusElem AS se_pupil, c2.other_values, c2.modi_note_od, c2.modi_note_os,c2.modi_note_Arr,  ".
			 "c2.wnl_value ".
			 "FROM chart_master_table c1 ".
			 "LEFT JOIN chart_pupil c2 ON c2.formId = c1.id  AND c2.purged='0'  ".
			 "WHERE c1.id = '".$this->fid."' AND c1.patient_id = '".$this->pid."' ";	
		
		$row = sqlQuery($sql);
		//$row=$oPupil->sqlExe($sql);
		if($row != false){
			$elem_noChangePupil=assignZero($row["chPupil"]);
			$elem_wnlPupil=assignZero($row["flagWnlPupil"]);
			$elem_posPupil=assignZero($row["flagPosPupil"]);
			//Pupil
			$elem_wnlPupilOd=assignZero($row["wnlPupilOd"]);
			$elem_wnlPupilOs=assignZero($row["wnlPupilOs"]);
			$elem_perrlaPupil = assignZero($row["perrla"]);
			$elem_se_Pupil = $row["se_pupil"];
			$elem_sumOdPupil = $row["sumOdPupil"];
			$elem_sumOsPupil = $row["sumOsPupil"];
			$elem_pupil_id = $row["pupil_id"];
			$elem_txtDesc_pupil = stripslashes(trim($row["descPupil"]));
			$elem_other_values = $row["other_values"];
			$elem_wnl_value = $row["wnl_value"];
			
			$mnOd=$row["modi_note_od"];
			$mnOs=$row["modi_note_os"];
			if($row["modi_note_Arr"]!='')
			$arrHx['Pupil'] = unserialize($row["modi_note_Arr"]);
			
		}
		
		//previous chart Notes summary
		if(empty($elem_pupil_id)){
			
			//if(empty($elem_pupil_id) ){  /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
				$tmp = "";
				$tmp = " c2.isPositive AS flagPosPupil, c2.wnl AS flagWnlPupil, 
					     c2.examinedNoChange AS chPupil, ";
				$tmp .= " c2.wnlPupilOd, c2.wnlPupilOs, c2.descPupil, c2.perrla, ";
				$tmp .= " c2.sumOdPupil, c2.sumOsPupil, c2.pupil_id, c2.other_values, c2.wnl_value, ";
				$tmp .= " c2.statusElem as se_pupil ";

				//$res = valNewRecordPupil($patient_id, $tmp);
				$elem_dos=$this->getDos();
				$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
				$res = $this->getLastRecord($tmp,0,$elem_dos);
				if($res!=false){$row=$res;}else{$row=false;}
				//for($i=0;$row=sqlFetchArray($res);$i++)	{
				if($row!=false){
					$elem_sumOdPupil = $row["sumOdPupil"];
					$elem_sumOsPupil = $row["sumOsPupil"];
					$elem_wnlPupil=assignZero($row["flagWnlPupil"]);
					$elem_posPupil=assignZero($row["flagPosPupil"]);
					$elem_wnlPupilOd=assignZero($row["wnlPupilOd"]);
					$elem_wnlPupilOs=assignZero($row["wnlPupilOs"]);
					$elem_txtDesc_pupil = stripslashes($row["descPupil"]);
					$elem_perrlaPupil =assignZero($row["perrla"]);
					$elem_other_values = $row["other_values"];
					$elem_wnl_value = $row["wnl_value"];
					$elem_se_Pupil_prev = $row["se_pupil"];
				}
				//BG
				$bgColor_pupil = "bgSmoke";

			//}
		}
		
		//Perrla
		if($elem_perrlaPupil == 1){
			$elem_sumOdPupil = $elem_sumOsPupil = "PERRLA";
		}
		
		//Color
		$cnm = $flgSe_Pupil_Od = $flgSe_Pupil_Os = "";
		if(!isset($bgColor_pupil)){
			//$elem_se_Pupil
			if(!empty($elem_se_Pupil)){
				$tmpArrSe = $this->se_elemStatus("PUPIL","0",$elem_se_Pupil);
				$flgSe_Pupil_Od = $tmpArrSe["Pupil"]["od"];
				$flgSe_Pupil_Os = $tmpArrSe["Pupil"]["os"];
			}
		}else{
			if(!empty($elem_se_Pupil_prev)){
				$tmpArrSe_prev = $this->se_elemStatus("PUPIL","0",$elem_se_Pupil_prev);
				$flgSe_Pupil_Od_prev = $tmpArrSe_prev["Pupil"]["od"];
				$flgSe_Pupil_Os_prev = $tmpArrSe_prev["Pupil"]["os"];
			}
		}

		//Wnl			
		$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("Pupil");//PERRLA, -ve APD
		$wnlStringOd = $wnlStringOs = $wnlString; 
		
		if(empty($flgSe_Pupil_Od) && empty($flgSe_Pupil_Od_prev) && !empty($elem_wnlPupilOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Pupil", "OD"); if(!empty($tmp)){ $wnlStringOd = $tmp;}  }
		if(empty($flgSe_Pupil_Os) && empty($flgSe_Pupil_Os_prev) && !empty($elem_wnlPupilOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Pupil", "OS"); if(!empty($tmp)){ $wnlStringOs = $tmp;}  }
		
		list($elem_sumOdPupil,$elem_sumOsPupil) = $this->oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd,"wValOs"=>$wnlStringOs,
									"wOd"=>$elem_wnlPupilOd,"sOd"=>$elem_sumOdPupil,
									"wOs"=>$elem_wnlPupilOs,"sOs"=>$elem_sumOsPupil));
		
		//Nochanged
		if(!empty($elem_se_Pupil)&&strpos($elem_se_Pupil,"=1")!==false){
			$elem_noChangePupil=1;
		}
		
		//Archive Pupil --
		if($bgColor_pupil != "bgSmoke"){ //Show work for current records and not for previous carried forward values
		
		$arrDivArcCmn=array();
		$oChartRecArc->setChkTbl("chart_pupil");
		$arrInpArc = array("elem_sumOdPupil"=>array("sumOdPupil",$elem_sumOdPupil,"","wnlPupilOd",$wnlString,$mnOd),
							"elem_sumOsPupil"=>array("sumOsPupil",$elem_sumOsPupil,"","wnlPupilOs",$wnlString,$mnOs));
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdPupil"])){
			//echo $arTmpRecArc["div"]["elem_sumOdPupil"];
			
			$arrDivArcCmn["Pupil"]["OD"]=$arTmpRecArc["div"]["elem_sumOdPupil"];				
			$moeArc_od = $arTmpRecArc["js"]["elem_sumOdPupil"];
			$flgArcColor_od = $arTmpRecArc["css"]["elem_sumOdPupil"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdPupil"])) $elem_sumOdPupil = $arTmpRecArc["curText"]["elem_sumOdPupil"];
		}else{
			$moeArc_od = $flgArcColor_od="";
		}
		//OS
		if(!empty($arTmpRecArc["div"]["elem_sumOsPupil"])){
			//echo $arTmpRecArc["div"]["elem_sumOsPupil"];
			$arrDivArcCmn["Pupil"]["OS"]=$arTmpRecArc["div"]["elem_sumOsPupil"];
			$moeArc_os = $arTmpRecArc["js"]["elem_sumOsPupil"];
			$flgArcColor_os = $arTmpRecArc["css"]["elem_sumOsPupil"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsPupil"])) $elem_sumOsPupil = $arTmpRecArc["curText"]["elem_sumOsPupil"];
		}else{
			$moeArc_os = $flgArcColor_os="";
		}
		
		}//bgsmoke
		//Archive Pupil --			
		
		//Purged --------
		$htmPurge = $this->getPurgedHtm();
		//Purged --------
		
		//Modified Notes ----
		//if Edit is not Done && modified Notes exists
		if(!empty($mnOd) && empty($moeArc_od)){ //Od
			list($moeMN_od,$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdPupil", $mnOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Pupil"]["OD"]=$tmpDiv;
		}else{
			$moeMN_od="";
		}
		if(!empty($mnOs) &&empty($moeArc_os)){ //Os
			list($moeMN_os,$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOsPupil", $mnOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Pupil"]["OS"]=$tmpDiv;
		}else{
			$moeMN_os="";
		}	
		//Modified Notes ----
		
		//create common div and echo ---
		//list($moeMN,$tmpDiv) = mkDivArcCmn("Pupil",$arrDivArcCmn);
		if($post["webservice"] != "1"){
			list($moeMN,$tmpDiv) = $this->oOnload->mkDivArcCmnNew("Pupil",$arrDivArcCmn,$arrHx);
		}
		
		$echo.= $tmpDiv;
		//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";
		
		//---------
		
		$arr=array();
		$arr["ename"] = "Pupil";
		$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"Pupil",
										"sOd"=>$elem_sumOdPupil,"sOs"=>$elem_sumOsPupil,
										"fOd"=>$flgSe_Pupil_Od,"fOs"=>$flgSe_Pupil_Os,"pos"=>$elem_posPupil,
										//"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
										"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
										//"mnOd"=>$moeMN_od,"mnOs"=>$moeMN_os
										));
		$arr["nochange"] = $elem_noChangePupil;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc_pupil;
		$arr["bgColor"] = "".$bgColor_pupil;
		$arr["other_values"] = $elem_other_values;
		$arr["htmPurge"] = $htmPurge;
		$arr["moeMN"] = $moeMN;
		$arr["exm_flg_se"] = array($flgSe_Pupil_Od,$flgSe_Pupil_Os);
		$arr["elem_hidden_fields"] = array("elem_se_Pupil"=>$elem_se_Pupil, "elem_posPupil"=>$elem_posPupil, "elem_wnlPupil"=>$elem_wnlPupil, "elem_wnlPupilOd"=>$elem_wnlPupilOd, "elem_wnlPupilOs"=>$elem_wnlPupilOs);			
		
		if($post["webservice"] == "1"){
		$echo ="";
			$str = $this->oOnload->getSummaryHTML_appwebservice($arr);		
		}else{
			//$str = $this->getSummaryHTML($arr);	
			$str = $this->oOnload->getSummaryHTML($arr);				
		}
		
		//---------
		$echo.= $str;
		
		return $echo;
		//c2-chart_pupil-------
	
	}

	/*
	function getSummaryHTML($arr){
		$enm_var = $enm = $arr["ename"];
		$arrSE = $arr["subExm"];
		$seList= isset($arr["seList"]) ? $arr["seList"] : "";
		$nochange = $arr["nochange"]; //(isset($arr["nochange"]) && !empty($arr["nochange"])) ? " checked=\"checked\" " : "";
		$oneeye = (!empty($arr["oe"])) ? explode("~!~",$arr["oe"]):array();
		$desc = trim($arr["desc"]);
		$bgColor = $arr["bgColor"];
		$drawdocId = $arr["drawdocId"];
		$drawapp = $arr["drawapp"];	
		$htmPurge = $arr["htmPurge"];	
		$moeMN = $arr["moeMN"];
		
		//Pupil : Pharama	--
		if($enm=="Pupil"){
			if(trim($arr["other_values"])=="Pharmacologically dilated on arrival,"){
				$arr["other_values"]["elem_pharmadilated"] = "Pharmacologically dilated on arrival";	
			}else{
				$arr["other_values"] = unserialize($arr["other_values"]);
			}		
		}
		//Pupil : Pharama	--
		
		$elem_btnNoChangePupil = (!empty($nochange)) ? "Prv" : "NC";
		
		$btn_hx = "";
		if(!empty($moeMN)){		
			$btn_hx .= "<input id=\"lblHx".$enm_var."\" class=\"btn hx\" type=\"button\" value=\"Hx\" ".$moeMN.">";
		}
		
		
		//$Exam Header ---
		
		$exm_header="<div class=\"einfo\">
		<div class=\"hdr\">
		<label onclick=\"openPW('Pupil')\">Pupil</label>
		<input type=\"button\" id=\"elem_btnWnlPupil\" class=\"btn wnl\" value=\"WNL\" onClick=\"setWnlValues('Pupil')\"	 onmouseover=\"showWnlOpts(1,1,'Pupil')\" onmouseout=\"showWnlOpts(0,1)\"  > 
		<input type=\"button\" id=\"elem_btnNoChangePupil\" class=\"prv btn nc\" value=\"".$elem_btnNoChangePupil."\" onClick=\"autoSaveNoChange('Pupil')\"	onmouseover=\"showWnlOpts(1,2,'Pupil')\" onmouseout=\"showWnlOpts(0,2)\" > 
		".$btn_hx."
		</div>
		</div>";
		
		//$Exam Header ---		
		
		// Pharmacologically dilated on arrival --
		$first_row="";
		if($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival"){

			//Color
			if(empty($bgColor)){
				$tmp="bgWhite";
			}else{
				$tmp="";
			}

			$first_row .= "<tr>";
			$first_row .=	"<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>";
			if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
				$first_row .=	"<td class=\"prph sumod ".$tmp."\" > Pharmacologically dilated on arrival "."</td>";
			}else{
				$first_row .=	"<td class=\"prph sumod\" ></td>";
			}
			if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
				$first_row .=	"<td class=\"prph sumos ".$tmp."\"> Pharmacologically dilated on arrival "."</td>";
			}else{
				$first_row .=	"<td class=\"prph sumod\" ></td>";
			}						
			$first_row .= "".$strDesc;
			$first_row .="</tr>";

			$strDesc="";//Empty Desc after use
			$exm_header = ""; //Empty after use

		}
		// Pharmacologically dilated on arrival --
		
		$htm = "
			<div class=\"esum\">
			<table class=\"table table-bordered table-hover table-striped\">
			".$first_row."
			<tr    >
			<td class=\"edone ehdr leftpanel worshthd\">
				
			</td>
			<td id=\"elem_sumOdPupil\" class=\"sumod tablnk  \"    ></td>
			<td id=\"elem_sumOsPupil\" class=\"sumos tablnk  \"   ></td>
			<td rowspan=\"1\" class=\"edesc  \"></td>
			</tr></table>
			</div>
		";
	
	
	}
	*/
	
	function load_exam($finalize_flag=0){
		
		$oExamXml = new ExamXml();
		$oWv = new WorkView();
		
		$patient_id = $this->pid;
		$form_id = $this->fid;
		
		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);
		
		//
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		$ProClr = User::getProviderColors();//
		//$logged_user_type = $_SESSION["logged_user_type"];
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
		$arrTabs = array("Pupil"=>"Pupil");
		
		#####
		$myflag=false;
		$elem_editMode="0";
		$elem_pupilId="";
		$elem_patientId=$patient_id; //$_SESSION['patient'];
		$elem_examDate=date("Y-m-d H:i:s");
		$elem_perrla ="0";
		$elem_wnl=0;
		$elem_noChange = "0";
		$elem_wnlPupilOd="0";
		$elem_wnlPupilOs="0";
		$arr_other_values=array();
		
		$elem_dos=$this->getDos(1);
		
		// Extract Values
		$sql = "SELECT * FROM chart_pupil WHERE formId = '".$form_id."' AND patientId='".$elem_patientId."' AND purged='0' ";		
		$row = sqlQuery($sql);
		if($row==false){
			//$res = valNewRecordPupil($patient_id);	
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
		}
		
		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			//$res = valNewRecordPupil($patient_id, " * ", "1");
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
		}
		
		if($row!=false){
			if($myflag){
				$elem_editMode=0;
			}else{
				$elem_editMode=1;
			}	
			$elem_pupilId=$row["pupil_id"];
			//$elem_formId=$row["formId"];
			//$elem_patientId=$row["patientId"];
			$elem_examDate= ($elem_editMode==0 || $row["examDate"] == "0000-00-00 00:00:00") ? $elem_examDate : $row["examDate"];
			$elem_wnl=$row["wnl"];
			$elem_notApplicable=$row["notApplicable"];
			if($elem_editMode==1){
				$elem_noChange=$row["examinedNoChange"];
				$elem_perrla = $row['perrla'];
			}
			$pupil_pupil_od= stripslashes($row["pupilOd"]);
			$pupil_pupil_os= stripslashes($row["pupilOs"]);
			$custom_field_xml= stripslashes($row["custom_field"]);
			
			/*
			$arrPupilOd = getXmlMenuArray($pupil_pupil_od);
			//$arrOD = getExtractedXml($arrPupilOd);
			//$arrOD = getMenuInputValueArray($arrPupilOd);
			$retPupilOd = getXmlValuesExtracted($arrPupilOd);
			$arrPupilOs = getXmlMenuArray($pupil_pupil_os);
			$retPupilOs = getXmlValuesExtracted($arrPupilOs);
			*/
			
			$arr_vals_pupil_od = $oExamXml->extractXmlValue($pupil_pupil_od);
			extract($arr_vals_pupil_od);			
			$arr_vals_pupil_os = $oExamXml->extractXmlValue($pupil_pupil_os);
			extract($arr_vals_pupil_os);
			
			
			//$arrOD = getXmlValuesExtracted_pr($arrPupilOd);
			$elem_isPositive = $row['isPositive'];
			//Old Data
			$apdMinusOd = $row["apdMinusOd"];
			$apdMinusOs = $row["apdMinusOs"];
			$apdPlusOd = $row["apdPlusOd"];
			$apdPlusOs = $row["apdPlusOs"];
			$reactionOd = $row["reactionOd"];
			$reactionOs = $row["reactionOs"];
			$shapeOd = $row["shapeOd"];
			$shapeOs = $row["shapeOs"];
			$elem_wnlPupilOd=$row["wnlPupilOd"];
			$elem_wnlPupilOs=$row["wnlPupilOs"];
			$elem_descPupil = $row["descPupil"];
			$elem_statusElements = ($elem_editMode==0) ? "" : $row["statusElem"];
			$elem_other_values=trim($row["other_values"]);
			if($elem_other_values == "Pharmacologically dilated on arrival,"){
				$arr_other_values["elem_pharmadilated"] = "Pharmacologically dilated on arrival";
			}else{
				$arr_other_values = unserialize($elem_other_values);
			}
			
			//UT Elems // //Previous values should have color.
			$elem_utElems = ($elem_editMode==1) ? $row["ut_elem"] : "" ; 
			
			$chkPhrma="";
			if($arr_other_values["elem_pharmadilated"]=="Pharmacologically dilated on arrival") {
				$chkPhrma=" checked=\"checked\" ";
				$elem_pharmadilated_eye = $arr_other_values["elem_pharmadilated_eye"];
			}	
			
		}

		//Set Change Indicator values -----
		$arrCI = array("elem_chng_divPupil_Od","elem_chng_divPupil_Os");
		for($i=0;$i<2;$i++){
			$tmp = $arrCI[$i];
			if(!empty($elem_statusElements) && (strpos($elem_statusElements,"".$arrCI[$i]."=1") !== false)){
				$$tmp="1";
			}else{
				$$tmp="0";
			}
		}
		//Set Change Indicator values -----
		
		//Set Column widths
		$cwdth1=82;
		$cwdth2=77;
		$cwdth3=63;
		$cwdth4=77;		
		
		// Show comments if empty
		//if(empty($elem_pupilDescOd))$elem_pupilDescOd = "Comments:";
		//if(empty($elem_pupilDescOs))$elem_pupilDescOs = "Comments:";
		$elem_pupilDescOd = trim($elem_pupilDescOd);
		$elem_pupilDescOs = trim($elem_pupilDescOs);		
		//view only
		$optlock = new ChartPtLock();
		$elem_per_vo = $optlock->is_viewonly_permission();
		
		
		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/pupil.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");	
	}	
}
?>