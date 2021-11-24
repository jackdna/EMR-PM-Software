<?php
class RefSurgLoader extends RefSurg{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);	
	}
	function getPurgedHtm(){
		$htmPurge="";
		$sql =  "SELECT ".
				"c2.id_ref_surg, ".
				"c2.isPositive AS flagPosRS, c2.wnl AS flagWnlRS, ".
				"c2.examined_no_change  AS chRS, ".
				"c2.sumOdRefSurg, ".
				"c2.sumOsRefSurg, ".
				"c2.wnlRefSurgOd, c2.wnlRefSurgOs, c2.descRefSurg, c2.purgerId, c2.purgeTime, ".
				"c2.statusElem AS se_RS ".				
				"FROM chart_master_table c1 ".
				"INNER JOIN chart_ref_surgery c2 ON c2.form_id = c1.id AND c2.purged!='0'  ".	
				"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
				"ORDER BY c2.purgeTime DESC ";			
		//$row = sqlQuery($sql);
		//$row=$oRefSurg->sqlExe($sql);
		$rez=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rez);$i++){
			$elem_noChangeRS=assignZero($row["chRS"]);
			//$elem_wnlRS=assignZero($row["flagWnlRS"]);
			$elem_posRS=assignZero($row["flagPosRS"]);
			//$elem_wnlRSOd=assignZero($row["wnlRefSurgOd"]);
			//$elem_wnlRSOs=assignZero($row["wnlRefSurgOs"]);
			$elem_se_RS = $row["se_RS"];					
			$elem_sumOdRS = $row["sumOdRefSurg"];
			$elem_sumOsRS = $row["sumOsRefSurg"];
			$elem_RS_id = $row["id_ref_surg"];
			$elem_txtDesc_RS = stripslashes(trim($row["descRefSurg"]));
			
			//Wnl
			list($elem_sumOdRS,$elem_sumOsRS) = $this->oOnload->setWnlValuesinSumm(array("wVal"=>"WNL",
						"wOd"=>$elem_wnlRSOd,"sOd"=>$elem_sumOdRS,
						"wOs"=>$elem_wnlRSOs,"sOs"=>$elem_sumOsRS));
			
			//Color
			$flgSe_RS_Od = $flgSe_RS_Os = "";
			if(!isset($bgColor_RS)){
				if(!empty($elem_se_RS)){
					$tmpArrSe = $this->se_elemStatus("REF_SURG","0",$elem_se_RS);
					$flgSe_RS_Od = $tmpArrSe["1"]["od"];
					$flgSe_RS_Os = $tmpArrSe["1"]["os"];
				}
			}
			//Nochanged
			if(!empty($elem_se_RS)&&strpos($elem_se_RS,"=1")!==false){
				$elem_noChangeRS=1;
			}
			
			//---------
			
			$arr=array();
			$arr["ename"] = "Refractive Surgery"; 
			$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"Refractive Surgery",
												"sOd"=>$elem_sumOdRS,"sOs"=>$elem_sumOsRS,
												"fOd"=>$flgSe_RS_Od,"fOs"=>$flgSe_RS_Os,"pos"=>$elem_posRS,
												"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
												"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
												));
			$arr["nochange"] = $elem_noChangeRS;
			$arr["oe"] = $oneeye;
			$arr["desc"] = $elem_txtDesc_RS;
			$arr["bgColor"] = "".$bgColor_RS;
			$arr["purgerId"] = $row["purgerId"];
			$arr["purgeTime"] = $row["purgeTime"];
			$arr["exm_flg_se"] = array($flgSe_RS_Od,$flgSe_RS_Os);
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
		
		$sql =  "SELECT ".
				"c2.id_ref_surg, ".
				"c2.isPositive AS flagPosRS, c2.wnl AS flagWnlRS, ".
				"c2.examined_no_change  AS chRS, ".
				"c2.sumOdRefSurg, ".
				"c2.sumOsRefSurg, ".
				"c2.wnlRefSurgOd, c2.wnlRefSurgOs, c2.descRefSurg, ".
				"c2.statusElem AS se_RS ".						
				"FROM chart_master_table c1 ".
				"LEFT JOIN chart_ref_surgery c2 ON c2.form_id = c1.id AND c2.purged='0'  ".	
				"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";
		$row = sqlQuery($sql);
		//$row=$oRefSurg->sqlExe($sql);
		if($row != false){
			$elem_noChangeRS=assignZero($row["chRS"]);
			//$elem_wnlRS=assignZero($row["flagWnlRS"]);
			$elem_posRS=assignZero($row["flagPosRS"]);
			//$elem_wnlRSOd=assignZero($row["wnlRefSurgOd"]);
			//$elem_wnlRSOs=assignZero($row["wnlRefSurgOs"]);
			$elem_se_RS = $row["se_RS"];					
			$elem_sumOdRS = $row["sumOdRefSurg"];
			$elem_sumOsRS = $row["sumOsRefSurg"];
			$elem_RS_id = $row["id_ref_surg"];
			$elem_txtDesc_RS = stripslashes(trim($row["descRefSurg"]));
		}

		//Previous
		if(empty($elem_RS_id)){
			$tmp = "";
			$tmp = "  c2.isPositive AS flagPosRS, 
					  c2.wnl AS flagWnlRS, c2.examined_no_change  AS chRS, ";
			$tmp .= " c2.wnlRefSurgOd, c2.wnlRefSurgOs, c2.descRefSurg, ";
			$tmp .= " c2.sumOdRefSurg, 
					  c2.sumOsRefSurg, c2.id_ref_surg ";
			
			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row!=false){
			//$res = valNewRecordRef_Surg($patient_id, $tmp);
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				
				//$elem_wnlRS=assignZero($row["flagWnlRS"]);
				$elem_posRS=assignZero($row["flagPosRS"]);
				//$elem_wnlRSOd=assignZero($row["wnlRefSurgOd"]);
				//$elem_wnlRSOs=assignZero($row["wnlRefSurgOs"]);
				$elem_sumOdRS = $row["sumOdRefSurg"];
				$elem_sumOsRS = $row["sumOsRefSurg"];						
				$elem_txtDesc_RS = stripslashes(trim($row["descRefSurg"]));						
				
			}
			//BG
			$bgColor_RS = "bgSmoke";
		}

		//Wnl
		list($elem_sumOdRS,$elem_sumOsRS) = $this->oOnload->setWnlValuesinSumm(array("wVal"=>"WNL",
					"wOd"=>$elem_wnlRSOd,"sOd"=>$elem_sumOdRS,
					"wOs"=>$elem_wnlRSOs,"sOs"=>$elem_sumOsRS));
		
		//Color
		$flgSe_RS_Od = $flgSe_RS_Os = "";
		if(!isset($bgColor_RS)){
			if(!empty($elem_se_RS)){
				$tmpArrSe = $this->se_elemStatus("REF_SURG","0",$elem_se_RS);
				$flgSe_RS_Od = $tmpArrSe["1"]["od"];
				$flgSe_RS_Os = $tmpArrSe["1"]["os"];
			}
		}
		//Nochanged
		if(!empty($elem_se_RS)&&strpos($elem_se_RS,"=1")!==false){
			$elem_noChangeRS=1;
		}
		
		//Purged --------
		$htmPurge = $this->getPurgedHtm();
		//Purged --------
		
		//---------			
		
		$arr=array();
		$arr["ename"] = "Refractive Surgery"; 
		$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"Refractive Surgery",
											"sOd"=>$elem_sumOdRS,"sOs"=>$elem_sumOsRS,
											"fOd"=>$flgSe_RS_Od,"fOs"=>$flgSe_RS_Os,"pos"=>$elem_posRS,
											"arcJsOd"=>$moeArc_od,"arcJsOs"=>$moeArc_os,
											"arcCssOd"=>$flgArcColor_od,"arcCssOs"=>$flgArcColor_os
											));
		$arr["nochange"] = $elem_noChangeRS;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc_RS;
		$arr["bgColor"] = "".$bgColor_RS;
		$arr["htmPurge"] = $htmPurge;
		$arr["exm_flg_se"] = array($flgSe_RS_Od,$flgSe_RS_Os);
		
		if($post["webservice"] == "1"){
		$echo ="";
		$str = $this->oOnload->getSummaryHTML_appwebservice($arr);			
		}else{			
		$str = $this->oOnload->getSummaryHTML($arr);			
		}
		
		//---------	
		$echo.= $str;
		return $echo;
	}
	
	function load_exam($finalize_flag = 0){
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;
		
		//
		$oPt=new Patient($patient_id);
		
		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);
		
		//
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		$ProClr = User::getProviderColors();//
		//$logged_user_type = $_SESSION["logged_user_type"];
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];
		
		//obj work view
		$owv = new WorkView();
		
		//DOS
		$elem_dos=$this->getDos(1);
		
		#patient Age
		$ptName = $oPt->getName(2);
		
		//default
		$arrTabs = array("RefSurg"=>"Refractive Surgery");	
		$elem_editMode="0";
		$elem_refSurgId="";
		$elem_wnl=0;
		$elem_examDate=date("Y-m-d H:i:s");
		$myflag=false;
		$elem_isPositive=0;
		$elem_noChange=0;
		$elem_wnlRefSurgOd=0;
		$elem_wnlRefSurgOs=0;
		$elem_mode_refsx="Traditional";
		
		// Extract Values
		$sql = "SELECT * FROM chart_ref_surgery  WHERE form_id  = '".$form_id."' AND patient_id='".$patient_id."' AND purged = '0' ";		
		$row=sqlQuery($sql);
		//if(mysql_num_rows($res)<=0){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
		if($row==false){
			//$res = valNewRecordRef_Surg($patient_id);
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
		}

		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){			
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
			
			$elem_refSurgId=$row["id_ref_surg"];	
			$elem_examDate=$row["exam_date"];
			$elem_wnl=$row["wnl"];
			$elem_isPositive=$row["isPositive"];
			$elem_wnlRefSurgOd=$row["wnlRefSurgOd"];
			$elem_wnlRefSurgOs=$row["wnlRefSurgOs"];
			$elem_descRefSurg=$row["descRefSurg"];
			if($elem_editMode==1) $elem_noChange=$row["examined_no_change"];
			$elem_notApplicable=$row["not_applicable"];
			$elem_statusElements = ($elem_editMode==0) ? "" : $row["statusElem"];

			$sumOdRefSurg = trim($row["sumOdRefSurg"]);
			$sumOsRefSurg = trim($row["sumOsRefSurg"]);

			$refSurgOd = stripslashes($row["refSurgOd"]);
			$refSurgOs = stripslashes($row["refSurgOs"]);
			/*
			$arrRefSurgOd = getXmlMenuArray($refSurgOd);
			$retVOd = getXmlValuesExtracted($arrRefSurgOd);
			$arrRefSurgOs = getXmlMenuArray($refSurgOs);
			$retVOs = getXmlValuesExtracted($arrRefSurgOs);
			*/
			$arr_vals_refSurg_od = $oExamXml->extractXmlValue($refSurgOd);
			extract($arr_vals_refSurg_od);
			$arr_vals_refSurg_os = $oExamXml->extractXmlValue($refSurgOs);
			extract($arr_vals_refSurg_os);
			
			if(!empty($row["mode_refsx"]))$elem_mode_refsx = $row["mode_refsx"];			
			
			//UT Elems
			$elem_utElems = ($elem_editMode==1) ?  $row["ut_elem"] : "" ;
			
		}
		
		//Get Values from MR
		if($finalize_flag != 1 && $myflag==true){
			if(isset($_GET["sod"])){
				$elem_mr_sOd=$this->arrMR["S"]["od"]=$_GET["sod"];
				$elem_mr_sOs=$this->arrMR["S"]["os"]=$_GET["sos"];
				$elem_mr_cOd=$this->arrMR["C"]["od"]=$_GET["cod"];
				$elem_mr_cOs=$this->arrMR["C"]["os"]=$_GET["cos"];
				$elem_mr_aOd=$_GET["aod"];
				$elem_mr_aOs=$_GET["aos"];
				
				//Set status flag
				if(!empty($_GET["sod"])||!empty($_GET["cod"])){
					$elem_statusElements.="elem_chng_div1_Od=1,";
				}
				if(!empty($_GET["sos"])||!empty($_GET["cos"])){
					$elem_statusElements.="elem_chng_div1_Os=1";
				}
			}

			list($elem_sphericalEqOd,$elem_sphericalEqOs)=$this->getSurgEqi();
			list($elem_adjstpppOd,$elem_adjstpppOs)=$this->getAdjstPPP();
			list($elem_laserEntryOd,$elem_laserEntryOs)=$this->getLaserEn();
			//list($elem_laserEnPhypadjstOd,$elem_laserEnPhypadjstOs)=$oRefSurg->getPhyPAdj();
		}
		
		//Get Pachy Values ----
		if(empty($elem_corrOd)&&empty($elem_corrOs)){
			list($cor_od,$cor_os)=$this->getPachy();
			$elem_corrOd=$cor_od;
			$elem_corrOs=$cor_os;
		}else{
			$cor_od=$elem_corrOd;
			$cor_os=$elem_corrOs;
		}
		if($cor_od=="null"){ $cor_od=""; }
		if($cor_os=="null"){ $cor_os=""; }
		
		$strcor_od="Pachy='".$cor_od."' ";
		$strcor_os="Pachy='".$cor_os."' ";
		$strcor_od="<span id=\"sp_corOd\">".$strcor_od."</span>";
		$strcor_os="<span id=\"sp_corOs\">".$strcor_os."</span>";
		//Get Pachy Values ----
		
		//Set Change Indicator values -----
		$arrCI = array("elem_chng_div1_Od","elem_chng_div1_Os");
		for($i=0;$i<2;$i++){
			$tmp=$arrCI[$i];
			if(!empty($elem_statusElements) && (strpos($elem_statusElements,"".$arrCI[$i]."=1") !== false)){
				$$tmp="1";
			}else{
				$$tmp="0";
			}
		}
		
		$strPtrnGray = "";
		if(empty($elem_chng_div1_Od) && empty($elem_chng_div1_Os)){
			$strPtrnGray .= "#div1 :input,";
		}else{
			if(empty($elem_chng_div1_Od)){
				$strPtrnGray .= "#div1 :input[name*='Od'],";
			}else if(empty($elem_chng_div1_Os)){
				$strPtrnGray .= "#div1 :input[name*='Os'],";
			}
		}
		//Set Change Indicator values -----
		
		//Ablation --
		if(empty($elem_ablationOd))$elem_ablationOd="6";
		if(empty($elem_ablationOs))$elem_ablationOs="6";
		$arrAblat=$this->arrAblation;	
		
		
		//MR C & A
		if(!empty($elem_mr_cOd)){	
			$tmp = $elem_mr_cOd;
			if(strlen($tmp)>6) $tmp="".substr($tmp,0,6)."";
		}else{$tmp ="";}		
		$sp_c_od = "<span id=\"sp_c_od\" title=\"".$elem_mr_cOd."\">"."C='".$tmp."'"."</span>";

		if(!empty($elem_mr_aOd)){
			$tmp = $elem_mr_aOd;
			if(strlen($tmp)>3) $tmp="".substr($tmp,0,3)."";
		}else{$tmp ="";}		
		$sp_a_od = "<span id=\"sp_a_od\" title=\"".$elem_mr_aOd."\">"."A='".$tmp."'"."</span>";
		
		//MR C & A
		if(!empty($elem_mr_cOs)){	
			$tmp = $elem_mr_cOs;
			if(strlen($tmp)>6) $tmp="".substr($tmp,0,6)."";
		}else{$tmp ="";}		
		$sp_c_os = "<span id=\"sp_c_os\" title=\"".$elem_mr_cOs."\">"."C='".$tmp."'"."</span>";

		if(!empty($elem_mr_aOs)){
			$tmp = $elem_mr_aOs;
			if(strlen($tmp)>3) $tmp="".substr($tmp,0,3)."";
		}else{$tmp ="";}		
		$sp_a_os = "<span id=\"sp_a_os\" title=\"".$elem_mr_aOs."\">"."A='".$tmp."'"."</span>";
		
		
		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/refractive_surgery.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");	
	}
}
?>