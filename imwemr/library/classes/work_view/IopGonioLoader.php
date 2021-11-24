<?php
class IopGonioLoader extends Gonio{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);
	}
	function getPurgedHtm(){
		$htmPurge="";
		$arrIOPVals=array();
		$arrGonioVals=array();
		$arrDilationVals=array();
		$arrOODVals=array();
		$arrPurgeTime=array();
		$arrPurgerID=array();

		//Get Values IOP
		$sql = "SELECT ".
			//c5-chart_iop-------
			"c5.sumOdIop, c5.sumOsIop, ".
			"c5.tetracaine,c5.flourocaine,c5.alcaine,c5.iop_time, c5.iop_id, c5.anesthetic,c5.statusElem AS statusElem_IOP, ".
			"c5.purgerId, c5.purgeTime ".
			"FROM chart_master_table c1 ".
			"INNER JOIN chart_iop c5 ON c5.form_id = c1.id AND c5.purged!='0' ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY c5.purgeTime DESC  ";
		$rez = sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){

			$prgTime=$row["purgeTime"];
			$prgrId=$row["purgerId"];
			if(!in_array($prgTime,$arrPurgeTime)){
				$arrPurgeTime[]=$prgTime;
				$arrPurgerID[$prgTime]=$prgrId;
			}

			$arrIOPVals[$prgTime] = $row;
		}

		//Get Values Gonio
		$sql ="SELECT ".
			//c6-chart_gonio-------
			"c6.gonio_od_drawing, c6.isPositive AS flagPosIopGonio, ".
			"c6.wnl AS flagWnlIopGonio, c6.examined_no_change AS chGon, ".
			"c6.gonio_os_drawing, c6.gonio_od_summary, c6.gonio_os_summary, ".
			"c6.wnlOd AS wnlGonioOd, c6.wnlOs AS wnlGonioOs, c6.desc_ig, ".
			"c6.posGonio, c6.posDraw AS posDrawGonio, c6.wnlGonio, c6.wnlDraw AS wnlDrawGonio, ".
			"c6.wnlDrawOd AS wnlDrawGonioOd, c6.wnlDrawOs AS wnlDrawGonioOs, c6.gonio_id, c6.noChange_drawing, ".
			"c6.examDateGonio, c6.idoc_drawing_id, c6.purgerId, c6.purgeTime, ".
			"c6.statusElem AS se_gon, c6.wnl_value ".
			"FROM chart_master_table c1 ".
			"INNER JOIN chart_gonio c6 ON c6.form_id = c1.id AND c6.purged!='0' ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY c6.purgeTime DESC  ";
		$rez = sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$prgTime=$row["purgeTime"];
			$prgrId=$row["purgerId"];
			if(!in_array($prgTime,$arrPurgeTime)){
				$arrPurgeTime[]=$prgTime;
				$arrPurgerID[$prgTime]=$prgrId;
			}

			$arrGonioVals[$prgTime] = $row;
		}

		//Get Value Dilation
		$sql ="SELECT ".
			//c14-chart_dialation-------
			"c14.patientAllergic, c14.pheny25, c14.tropicanide, ".
			"c14.cyclogel, c14.dilated_other, c14.dilated_time, ".
			"c14.dia_id, c14.eyeSide,c14.noDilation,c14.mydiacyl5, c14.dilation,c14.statusElem AS statusElem_Dilation, ".
			"c14.unableDilation, ".
			"c14.purgerId, c14.purgeTime ".
			"FROM chart_master_table c1 ".
			"INNER JOIN chart_dialation c14 ON c14.form_id = c1.id AND c14.purged!='0' ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY c14.purgeTime DESC  ";
		$rez = sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$prgTime=$row["purgeTime"];
			$prgrId=$row["purgerId"];
			if(!in_array($prgTime,$arrPurgeTime)){
				$arrPurgeTime[]=$prgTime;
				$arrPurgerID[$prgTime]=$prgrId;
			}

			$arrDilationVals[$prgTime] = $row;
		}

		//Get Value OOD
		$sql ="SELECT ".
			//C15-chart_ood ----
			"c15.ood_id, c15.ood,c15.statusElem AS statusElem_OOD, c15.eye AS eye_ood,  ".
			"c15.purgerId, c15.purgeTime ".
			"FROM chart_master_table c1 ".
			"INNER JOIN chart_ood c15 ON c15.form_id = c1.id AND c15.purged!='0' ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY c15.purgeTime DESC  ";
		$rez = sqlStatement($sql);
		for($i=0;$row = sqlFetchArray($rez);$i++){
			$prgTime=$row["purgeTime"];
			$prgrId=$row["purgerId"];
			if(!in_array($prgTime,$arrPurgeTime)){
				$arrPurgeTime[]=$prgTime;
				$arrPurgerID[$prgTime]=$prgrId;
			}

			$arrOODVals[$prgTime] = $row;
		}



		//$row = sqlQuery($sql);
		//$row=$oGonio->sqlExe($sql);
		//$rez = sqlStatement($sql);
		//for($i=0;$row = sqlFetchArray($rez);$i++){
		if(count($arrPurgeTime) > 0){
			//Sort
			arsort($arrPurgeTime);

			foreach($arrPurgeTime AS $k => $v){

				$purgTime = $v;
				$prgrid=$arrPurgerID[$purgTime];

				//IOP
				$row=$arrIOPVals[$purgTime];
				$elem_tetracaine=$row["tetracaine"];
				$elem_flourocaine=$row["flourocaine"];
				$elem_alcaine=$row["alcaine"];
				$elem_iop_time=$row["iop_time"];
				$elem_posIop = 0;
				$elem_wnlIop = 0;
				$elem_wnlIopOd=0;//assignZero($row["wnlIopOd"]);
				$elem_wnlIopOs=0;//assignZero($row["wnlIopOs"]);
				$elem_sumOdIop = $row["sumOdIop"];
				$elem_sumOsIop = $row["sumOsIop"];
				$elem_anesthetic = $row["anesthetic"];
				$elem_se_iop = $row["statusElem_IOP"];
				$elem_iop_id = $row["iop_id"];

				//Gonio
				$row=$arrGonioVals[$purgTime];
				if(!empty($row["chGon"])&&!empty($row["noChange_drawing"])){
					$elem_noChangeGonio=assignZero($row["chGon"]); //For Gonio only
				}
				$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]); //for gonio only
				$elem_posIopGonio=assignZero($row["flagPosIopGonio"]); // for gonio only
				$elem_posGonio=$row["posGonio"];
				$elem_posDrawGonio=$row["posDrawGonio"];
				$elem_wnlGonio=$row["wnlGonio"];
				$elem_wnlDrawGonio=$row["wnlDrawGonio"];
				//$elem_sideIop = $row["sideIop"];
				$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
				$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
				$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
				$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
				$elem_wnl_value_gonio=$row["wnl_value"];
				$elem_se_Gonio = $row["se_gon"];
				$elem_sumOdGon = $row["gonio_od_summary"];
				$elem_sumOsGon = $row["gonio_os_summary"];

				$elem_ncGonio = assignZero($row["chGon"]);
				$elem_ncDrawGonio = assignZero($row["noChange_drawing"]);
				$examdate = wv_formatDate($row["examDateGonio"]);
				$elem_gonio_id = $row["gonio_id"];
				$elem_txtDesc_iop = stripslashes(trim($row["desc_ig"])); //Gonio
				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $elem_gonio_id;
				}elseif( !empty($row["gonio_od_drawing"]) ){
					$ardrawApp = array($row["gonio_od_drawing"]);
				}

				//Dialation
				$row=$arrDilationVals[$purgTime];
				$elem_pheny25 = $row["pheny25"];
				$elem_tropicanide = $row["tropicanide"];
				$elem_cyclogel = $row["cyclogel"];
				$elem_dilated_other = $row["dilated_other"];
				$elem_dilated_time = $row["dilated_time"];
				$elem_eyeSide = $row["eyeSide"];
				$patientAllergic=assignZero($row["patientAllergic"]);
				$elem_no_dilation = $row["noDilation"];
				$elem_mydiacyl5=$row["mydiacyl5"];
				$elem_dilation=$row["dilation"];
				$elem_se_dilation = $row["statusElem_Dilation"];
				$unableDilation=$row["unableDilation"];
				$dilated_mm=$row["dilated_mm"];
				$elem_dia_id = $row["dia_id"];

				//C15-chart_ood
				$row=$arrOODVals[$purgTime];
				$ood = $row["ood"];
				$statusElem_OOD = $row["statusElem_OOD"];
				$eye_ood = $row["eye_ood"];
				$elem_ood_id = $row["ood_id"];

				//--------------------

				//StatusFlag IOP
				$flgSe_IOP = 0;
				if(!isset($bgColor_IOP)){
					if(!empty($elem_se_iop)){
						$flgSe_IOP=1;
					}
				}

				//Anes
				$spAnesTime="";
				$sumAnes = $sumAnes_od = $sumAnes_os = "";
				if(!empty($elem_anesthetic)){
					$arrAnes=unserialize($elem_anesthetic);
					$tmp = count($arrAnes);
					$spAnesTime = $arrAnes[$tmp-1]["time"];

					//*
					for($a=0;$a<$tmp;$a++){
						if((!empty($arrAnes[$a]["anes"])||!empty($arrAnes[$a]["other"]))&&!empty($arrAnes[$a]["time"])){

							$t = "";
							if(!empty($arrAnes[$a]["anes"])){
								$t .= $arrAnes[$a]["anes"];
							}

							if(!empty($arrAnes[$a]["other"])){
								if(!empty($t)){$t .= ",";}
								$t .= "".$arrAnes[$a]["other"];
							}

							$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
							$t = str_replace(",",", ",$t);
							$sumAnes .= $t." ".trim($arrAnes[$a]["time"])."<br/>";
						}
					}

					$sumAnes_od = $sumAnes_os = $sumAnes;
					// */

				}else if(!empty($elem_tetracaine) || !empty($elem_flourocaine) || !empty($elem_alcaine)){
					$spAnesTime = $elem_iop_time;

					$t = "";
					if(!empty($elem_tetracaine)) { $t .= "Tetracaine,"; }
					if(!empty($elem_flourocaine)) { $t .= "Flourocaine, "; }
					if(!empty($elem_alcaine)) { $t .= "Alcaine "; }
					$t .= " ".$spAnesTime;
					$sumAnes = $sumAnes_od = $sumAnes_os =  $t;
				}

				//Dilation --
				$spDialTime="";
				$arrDilatInx = array("pheny25","mydiacyl5","mydiacyl1","Cyclogyl","Paremyd");
				$arrDilatVals = array("Phenylephrine 2.5%","Mydriacyl 1/2%","Mydriacyl 1%","Cyclogyl 1%","Paremyd");

				$sumDilation = $sumDilation_od = $sumDilation_os = "";
				if(!empty($elem_dia_id) && !empty($elem_se_dilation)){
					if(!empty($elem_dilation)){
						$arrDilation=unserialize($elem_dilation);
						$tmp = count($arrDilation);
						for($a=0;$a<$tmp;$a++){
							if((!empty($arrDilation[$a]["dilate"])||!empty($arrDilation[$a]["other_desc"]))&&!empty($arrDilation[$a]["time"])){

								//if(empty($spDialTime)){
									$spDialTime = trim($arrDilation[$a]["time"].' '.$elem_eyeSide);
									//break;
								//}

								$t = "";
								if(!empty($arrDilation[$a]["dilate"])){
									$t = str_replace($arrDilatInx,$arrDilatVals,$arrDilation[$a]["dilate"]);
								}

								if(!empty($arrDilation[$a]["other_desc"])){
									$t = str_replace("Other", "".$arrDilation[$a]["other_desc"],$t);
								}
								$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
								$t = str_replace(",",", ",$t);
								$sumDilation .= $t." ".trim($arrDilation[$a]["time"])."<br/>";

							}
						}

					}else {
						if(!empty($elem_pheny25) || !empty($elem_tropicanide) || !empty($elem_cyclogel) || !empty($elem_mydiacyl5) ||
							!empty($elem_dilated_other)){
							$spDialTime = trim($elem_dilated_time.' '.$elem_eyeSide);

							$t = "";
							if(!empty($elem_pheny25)) {$t .= "Phenylephrine 2.5%,";}
							if(!empty($elem_tropicanide)) {$t .= "Mydriacyl 1%,";}
							if(!empty($elem_cyclogel)) {$t .= "Cyclogyl 1%,";}
							if(!empty($elem_mydiacyl5)) {$t .= "Mydriacyl 5%,";}
							if(!empty($elem_dilated_other)) {$t .= $elem_dilated_other.",";}
							$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
							$t = str_replace(",",", ",$t);
							$sumDilation .= $t." ".$elem_dilated_time."<br/>";

						}
					}

					if($elem_eyeSide=="OU"||$elem_eyeSide=="OD"){
						$sumDilation_od=$sumDilation;
					}
					if($elem_eyeSide=="OU"||$elem_eyeSide=="OS"){
						$sumDilation_os=$sumDilation;
					}

					if($elem_no_dilation==1){
						$spDialTime = "No Dilation";
					}else if($unableDilation==1){
						$spDialTime = "Refuse Dilation";
					}
				}
				//Dilation --

				//OOD
				$sumOOD = $sumOOD_od = $sumOOD_os = "";
				$arrOODInx = array("Piol","Alphagan","Iopidine","Diamox");
				$arrOODVals = array("Pilo 1%","Alphagan P 0.1%","Iopidine 0.5%","Diamox");

				if(!empty($elem_ood_id) && !empty($statusElem_OOD)){
					if(!empty($ood)){
						$arrOOD=unserialize($ood);
						$tmp = count($arrOOD);
						for($a=0;$a<$tmp;$a++){
							if((!empty($arrOOD[$a]["ood"])||!empty($arrOOD[$a]["other_desc"]))&&!empty($arrOOD[$a]["time"])){

								$t = "";
								if(!empty($arrOOD[$a]["ood"])){
									$t = str_replace($arrOODInx,$arrOODVals,$arrOOD[$a]["ood"]);
								}

								if(!empty($arrOOD[$a]["other_desc"])){
									$t = str_replace("Other", "".$arrOOD[$a]["other_desc"],$t);
								}
								$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
								$t = str_replace(",",", ",$t);
								$sumOOD .= $t." ".trim($arrOOD[$a]["time"])."<br/>";
							}
						}
					}
				}

				if($eye_ood=="OU"||$eye_ood=="OD"){
					$sumOOD_od=$sumOOD;
				}
				if($eye_ood=="OU"||$eye_ood=="OS"){
					$sumOOD_os=$sumOOD;
				}
				//OOD

				//WNL
				//Gonio --
				$wnlString = !empty($elem_wnl_value_gonio) ? $elem_wnl_value_gonio : $this->getExamWnlStr("Gonio");
				list($elem_sumOdGon,$elem_sumOsGon) = $this->oOnload->setWnlValuesinSumm(array("wVal"=>$wnlString,
										"wOd"=>$elem_wnlGonioOd,"sOd"=>$elem_sumOdGon,
										"wOs"=>$elem_wnlGonioOs,"sOs"=>$elem_sumOsGon));
				//Nochanged
				if(!empty($elem_se_Gonio)&&strpos($elem_se_Gonio,"=1")!==false){
					$elem_noChangeGonio=1;
				}

				//----
				//is Change is made in new chart -----
					$flgSe_Gonio_Od = $flgSe_Gonio_Os = "0";
					$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
					if(!isset($bgColor_gonio)){
						if(!empty($elem_se_Gonio)){
							$tmpArrSe = $this->se_elemStatus("GONIO","0",$elem_se_Gonio);
							$flgSe_Gonio_Od = $tmpArrSe["Iop"]["od"];
							$flgSe_Gonio_Os = $tmpArrSe["Iop"]["os"];
							$flgSe_Draw_Od = $tmpArrSe["Iop3"]["od"];
							$flgSe_Draw_Os = $tmpArrSe["Iop3"]["os"];
						}
					}
				//is Change is made in new chart -----

				$arr=array();
				$arr["ename"] = "IOP/Gonio";

				//IOP
				if(!empty($elem_iop_id)){
				$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"IOP",
													"sOd"=>$elem_sumOdIop,"sOs"=>$elem_sumOsIop,
													"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
													"arcJsOd"=>$moeArc["od"]["Iop"],"arcJsOs"=>$moeArc["os"]["Iop"],
													"arcCssOd"=>$flgArcColor["od"]["Iop"],"arcCssOs"=>$flgArcColor["os"]["Iop"],
													"enm_2"=>"IOP"));
				}

				//Gonio
				if(!empty($elem_gonio_id)){
				$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Gonio",
													"sOd"=>$elem_sumOdGon,"sOs"=>$elem_sumOsGon,
													"fOd"=>$flgSe_Gonio_Od,"fOs"=>$flgSe_Gonio_Os,"pos"=>$elem_posGonio,
													"arcJsOd"=>$moeArc["od"]["Gonio"],"arcJsOs"=>$moeArc["os"]["Gonio"],
													"arcCssOd"=>$flgArcColor["od"]["Gonio"],"arcCssOs"=>$flgArcColor["os"]["Gonio"],
													"enm_2"=>"Gonio"));
				}

				if(!empty($elem_iop_id)){
				//Anes : No archive working  for Anes
				$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Anesthetic",
											"sOd"=>$sumAnes_od,"sOs"=>$sumAnes_os,
											"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
											"arcJsOd"=>$moeArc["od"]["Anes"],"arcJsOs"=>$moeArc["os"]["Anes"],
											"arcCssOd"=>$flgArcColor["od"]["Anes"],"arcCssOs"=>$flgArcColor["os"]["Anes"],
											"enm_2"=>"Anesthetic"));

				}

				//Dilation: No archive working  for dilate
				if(!empty($elem_dia_id)){
				$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Dilation",
													"sOd"=>$sumDilation_od,"sOs"=>$sumDilation_os,
													"fOd"=>$elem_se_dilation,"fOs"=>$elem_se_dilation,
													"arcJsOd"=>$moeArc["od"]["dilation"],"arcJsOs"=>$moeArc["os"]["dilation"],
													"arcCssOd"=>$flgArcColor["od"]["dilation"],"arcCssOs"=>$flgArcColor["os"]["dilation"],
													"enm_2"=>"Dilation"));
				}

				//OOD: No archive working  for ood
				if(!empty($elem_ood_id)){
				$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Ophth. Drops",
													"sOd"=>$sumOOD_od,"sOs"=>$sumOOD_os,
													"fOd"=>$statusElem_OOD,"fOs"=>$statusElem_OOD,
													"arcJsOd"=>$moeArc["od"]["ood"],"arcJsOs"=>$moeArc["os"]["ood"],
													"arcCssOd"=>$flgArcColor["od"]["ood"],"arcCssOs"=>$flgArcColor["os"]["ood"],
													"enm_2"=>"Ophth. Drops"));
				}

				if(!empty($elem_gonio_id)){
				//Drawing
				$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Drawing",
													"sOd"=>$drawdocId,"sOs"=>"",
													"fOd"=>$flgSe_Draw_Od,"fOs"=>$flgSe_Draw_Os,"pos"=>$elem_posDrawGonio,
													"enm_2"=>"DrawGonio"));
				}


				//Sub Exam List
				$arr["seList"] = 	array("IOP"=>array("enm"=>"IOP","pos"=>$elem_posIop,
									"wOd"=>$elem_wnlIopOd,"wOs"=>$elem_wnlIopOs),
								"Gonio"=>array("enm"=>"Gonio","pos"=>$elem_posGonio,
									"wOd"=>$elem_wnlGonioOd,"wOs"=>$elem_wnlGonioOs),
								"DrawGonio"=>array("enm"=>"Drawing","pos"=>$elem_posDrawGonio,
									"wOd"=>$elem_wnlDrawGonioOd,"wOs"=>$elem_wnlDrawGonioOs)
								);
				$arr["nochange"] = $elem_noChangeGonio;
				$arr["oe"] = $oneeye;
				$arr["desc"] = $elem_txtDesc_iop;
				$arr["Dilation"] = array("Anesthetic"=>$spAnesTime,"Dilate"=>$spDialTime,"PtAllergic"=>$patientAllergic);
				$arr["bgColor"] = "".$bgColor_gonio;
				$arr["drawdocId"] = $drawdocId;
				$arr["drawapp"] = $ardrawApp;
				$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
				$arr["examdate"] = $examdate;
				$arr["purgerId"] = $prgrid;
				$arr["purgeTime"] = $purgTime;
				$arr["exm_flg_se"] = array($flgSe_Gonio_Od,$flgSe_Gonio_Os);
				$htmPurge .= $this->oOnload->getSummaryHTML_purged($arr);
				//---------
			}
		}
		return $htmPurge;
	}
	function getWorkViewSummery($post){

		$oneeye = $post["oe"]; //one eye

		//dos
		$elem_dos=$this->getDos();
		$elem_dos=wv_formatDate($elem_dos,0,0,"insert");

		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);
		//---
		$echo="";


		$sql ="SELECT ".
			//c5-chart_iop-------
			"c5.sumOdIop, c5.sumOsIop,".
			"c5.tetracaine,c5.flourocaine,c5.alcaine,c5.iop_time, c5.iop_id, c5.anesthetic,c5.statusElem AS statusElem_IOP, ".
			"c5.modi_note_IopOd, c5.modi_note_IopOs, c5.modi_note_AnestheticOd, c5.modi_note_AnestheticOs, ".
			"c5.sumAnesOd , c5.sumAnesOs, c5.spAnesTime,c5.modi_note_iopArr,c5.modi_note_AnestheticArr,".
			//c14-chart_dialation-------
			"c14.patientAllergic, c14.pheny25, c14.tropicanide,".
			"c14.cyclogel, c14.dilated_other, c14.dilated_time,".
			"c14.dia_id, c14.eyeSide,c14.noDilation,c14.mydiacyl5, c14.dilation,c14.statusElem AS statusElem_Dilation, ".
			"c14.unableDilation, c14.modi_note_DilationOd, c14.modi_note_DilationOs, c14.sumDilation_od, c14.sumDilation_os, c14.spDialTime,c14.modi_note_Arr AS modi_note_DilaArr,".
			//C15-chart_ood ----
			"c15.ood_id, c15.ood,c15.statusElem AS statusElem_OOD, c15.eye AS eye_ood,
			 c15.sumOOD_od, c15.sumOOD_os, c15.modi_note_OodOd, c15.modi_note_OodOs, c15.spOODTime, c15.modi_note_Arr  AS modi_note_oodArr,".
			//c6-chart_gonio-------
			"c6.gonio_od_drawing, c6.isPositive AS flagPosIopGonio, ".
			"c6.wnl AS flagWnlIopGonio, c6.examined_no_change AS chGon, ".
			"c6.gonio_os_drawing, c6.gonio_od_summary, c6.gonio_os_summary, ".
			"c6.wnlOd AS wnlGonioOd, c6.wnlOs AS wnlGonioOs, c6.desc_ig, ".
			"c6.posGonio, c6.posDraw AS posDrawGonio, c6.wnlGonio, c6.wnlDraw AS wnlDrawGonio, ".
			"c6.wnlDrawOd AS wnlDrawGonioOd, c6.wnlDrawOs AS wnlDrawGonioOs, c6.gonio_id, c6.noChange_drawing, ".
			"c6.examDateGonio, c6.idoc_drawing_id, c6.modi_note_GonioOd, c6.modi_note_GonioOs, c6.modi_note_Draw, ".
			"c6.statusElem AS se_gon, c6.modi_note_Arr as modi_note_gonioArr, c6.wnl_value ".
			"FROM chart_master_table c1 ".
			"LEFT JOIN chart_iop c5 ON c5.form_id = c1.id AND c5.purged='0' ".
			"LEFT JOIN chart_gonio c6 ON c6.form_id = c1.id AND c6.purged='0' ".
			"LEFT JOIN chart_dialation c14 ON c14.form_id = c1.id AND c14.purged='0' ".
			"LEFT JOIN chart_ood c15 ON c15.form_id = c1.id AND c15.purged='0' ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";
		$row = sqlQuery($sql);
		//$row=$oGonio->sqlExe($sql);

		if($row != false){
			if(!empty($row["chGon"])&&!empty($row["noChange_drawing"])){
				$elem_noChangeGonio=assignZero($row["chGon"]); //For Gonio only
			}
			$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]); //for gonio only
			$elem_posIopGonio=assignZero($row["flagPosIopGonio"]); // for gonio only

			$elem_tetracaine=$row["tetracaine"];
			$elem_flourocaine=$row["flourocaine"];
			$elem_alcaine=$row["alcaine"];
			$elem_iop_time=$row["iop_time"];
			$elem_posIop = 0;
			$elem_wnlIop = 0;
			$elem_wnlIopOd=0;//assignZero($row["wnlIopOd"]);
			$elem_wnlIopOs=0;//assignZero($row["wnlIopOs"]);
			$elem_sumOdIop = $row["sumOdIop"];
			$elem_sumOsIop = $row["sumOsIop"];
			$elem_sumAnesOd = $row["sumAnesOd"];
			$elem_sumAnesOs = $row["sumAnesOs"];
			$elem_spAnesTime = $row["spAnesTime"];

			$elem_posGonio=$row["posGonio"];
			$elem_posDrawGonio=$row["posDrawGonio"];
			$elem_wnlGonio=$row["wnlGonio"];
			$elem_wnlDrawGonio=$row["wnlDrawGonio"];
			//$elem_sideIop = $row["sideIop"];
			$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
			$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
			$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
			$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
			$elem_wnl_value_gonio=$row["wnl_value"];

			$elem_se_Gonio = $row["se_gon"];
			$elem_sumOdGon = $row["gonio_od_summary"];
			$elem_sumOsGon = $row["gonio_os_summary"];
			$elem_anesthetic = $row["anesthetic"];
			$elem_se_iop = $row["statusElem_IOP"];
			$elem_ncGonio = assignZero($row["chGon"]);
			$elem_ncDrawGonio = assignZero($row["noChange_drawing"]);
			$examdate = wv_formatDate($row["examDateGonio"]);
			$elem_sumAnesOd=$row["sumAnesOd"];
			$elem_sumAnesOs=$row["sumAnesOs"];
			$elem_spAnesTime=$row["spAnesTime"];

			//Dialation
			$elem_pheny25 = $row["pheny25"];
			$elem_tropicanide = $row["tropicanide"];
			$elem_cyclogel = $row["cyclogel"];
			$elem_dilated_other = $row["dilated_other"];
			$elem_dilated_time = $row["dilated_time"];
			$elem_eyeSide = $row["eyeSide"];
			$patientAllergic=assignZero($row["patientAllergic"]);
			$elem_no_dilation = $row["noDilation"];
			$elem_mydiacyl5=$row["mydiacyl5"];
			$elem_dilation=$row["dilation"];
			$elem_se_dilation = $row["statusElem_Dilation"];
			$unableDilation=$row["unableDilation"];
			$dilated_mm=$row["dilated_mm"];
			$elem_sumDilationOd =$row["sumDilation_od"];
			$elem_sumDilationOs =$row["sumDilation_os"];
			$elem_spDialTime = $row["spDialTime"];

			//C15-chart_ood
			$ood = $row["ood"];
			$statusElem_OOD = $row["statusElem_OOD"];
			$elem_spOODTime = $row["spOODTime"];
			$elem_sumOOD_od = $row["sumOOD_od"];
			$elem_sumOOD_os = $row["sumOOD_os"];
			$modi_note_OodOd = $row["modi_note_OodOd"];
			$modi_note_OodOs = $row["modi_note_OodOs"];

			$elem_iop_id = $row["iop_id"];
			$elem_gonio_id = $row["gonio_id"];
			$elem_dia_id = $row["dia_id"];
			$elem_ood_id = $row["ood_id"];
			$elem_txtDesc_iop = stripslashes(trim($row["desc_ig"])); //Gonio

			$eye_ood = $row["eye_ood"];

			if(!empty($row["idoc_drawing_id"])) {
				$drawdocId = $elem_gonio_id;
			}elseif( !empty($row["gonio_od_drawing"]) ){
				$ardrawApp = array($row["gonio_od_drawing"]);
			}

			$modi_note_GonioOd=$row["modi_note_GonioOd"];
			$modi_note_GonioOs=$row["modi_note_GonioOs"];
			$modi_note_DilationOd=$row["modi_note_DilationOd"];
			$modi_note_DilationOs=$row["modi_note_DilationOs"];
			$modi_note_IopOd=$row["modi_note_IopOd"];
			$modi_note_IopOs=$row["modi_note_IopOs"];
			$modi_note_AnestheticOd=$row["modi_note_AnestheticOd"];
			$modi_note_AnestheticOs=$row["modi_note_AnestheticOs"];
			$modi_note_Draw = $row["modi_note_Draw"];

			$modi_note_iopArr = unserialize($row["modi_note_iopArr"]);
			$modi_note_AnestheticArr = unserialize($row["modi_note_AnestheticArr"]);
			$modi_note_DilaArr = unserialize($row["modi_note_DilaArr"]);
			$modi_note_oodArr = unserialize($row["modi_note_oodArr"]);
			$modi_note_gonioArr = unserialize($row["modi_note_gonioArr"]);

				$arrHx = array();
				if(is_array($modi_note_iopArr) && count($modi_note_iopArr)>0 && $row["modi_note_iopArr"]!='')
				$arrHx['IOP']	= $modi_note_iopArr;
				if(is_array($modi_note_AnestheticArr) && count($modi_note_AnestheticArr)>0 && $row["modi_note_AnestheticArr"]!='')
				$arrHx['Anesthetic']	= $modi_note_AnestheticArr;
				if(is_array($modi_note_DilaArr) && count($modi_note_DilaArr)>0 && $row["modi_note_DilaArr"]!='')
				$arrHx['Dilation'] = $modi_note_DilaArr;
				if(is_array($modi_note_oodArr) && count($modi_note_oodArr)>0 && $row["modi_note_oodArr"]!='')
				$arrHx['Ophth. Drops'] = $modi_note_oodArr;
				if(is_array($modi_note_gonioArr) && count($modi_note_gonioArr)>0 && $row["modi_note_gonioArr"]!='')
				$arrHx['Gonio'] = $modi_note_gonioArr;

		}

		if(empty($elem_iop_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			$tmp = "";
			if(!isset($GLOBALS["STOP_PRV_IOP_SUMM"]) || empty($GLOBALS["STOP_PRV_IOP_SUMM"])){
			/*
			$tmp = "chart_iop.isPositive AS flagPosIopGonio, chart_iop.wnl AS flagWnlIopGonio, chart_iop.examined_no_change AS chIop, ";
			$tmp .= "chart_iop.tetracaine,chart_iop.flourocaine,chart_iop.alcaine,chart_iop.iop_time, ";
			$tmp .= "chart_iop.wnlIopOd AS wnlGonioOd, chart_iop.wnlIopOs AS wnlGonioOs, ";
			$tmp .= "chart_iop.gonio_od_drawing,chart_iop.gonio_os_drawing, chart_iop.desc_ig, ";
			$tmp .= "chart_iop.posGonio, chart_iop.posDraw AS posDrawGonio, chart_iop.wnlGonio, chart_iop.wnlDraw AS wnlDrawGonio, ";
			$tmp .= "chart_iop.wnlDrawOd AS wnlDrawGonioOd, chart_iop.wnlDrawOs AS wnlDrawGonioOs, ";
			$tmp .= "chart_iop.sumOdIop, chart_iop.sumOsIop, chart_iop.gonio_od_summary, chart_iop.gonio_os_summary, chart_iop.iop_id ";
			*/
			//$tmp .="chart_iop.sumOdIop, chart_iop.sumOsIop,";
			//$tmp .="chart_iop.tetracaine,chart_iop.flourocaine,chart_iop.alcaine,chart_iop.iop_time, chart_iop.iop_id ";
			$tmp .= "".
			"chart_iop.sumOdIop, chart_iop.sumOsIop,".
			"chart_iop.tetracaine,chart_iop.flourocaine,chart_iop.alcaine,chart_iop.iop_time, chart_iop.iop_id, chart_iop.anesthetic,chart_iop.statusElem AS statusElem_IOP, ".
			"chart_iop.modi_note_IopOd, chart_iop.modi_note_IopOs, chart_iop.modi_note_AnestheticOd, chart_iop.modi_note_AnestheticOs, ".
			"chart_iop.sumAnesOd , chart_iop.sumAnesOs, chart_iop.spAnesTime,chart_iop.modi_note_iopArr,chart_iop.modi_note_AnestheticArr";
			$tmp .= "";

			//IOP
			$ochartiop = new ChartIop($this->pid, $this->fid);
			$res = $ochartiop->valNewRecordIop($tmp,"0",$elem_dos);
			for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_sumOdIop = $row["sumOdIop"];
				$elem_sumOsIop = $row["sumOsIop"];
				$elem_noChangeIop=assignZero($row["chIop"]);
				$elem_tetracaine=$row["tetracaine"];
				$elem_flourocaine=$row["flourocaine"];
				$elem_alcaine=$row["alcaine"];
				$elem_iop_time=$row["iop_time"];

				$elem_sumAnesOd=$row["sumAnesOd"];
				$elem_sumAnesOs=$row["sumAnesOs"];
				$elem_spAnesTime=$row["spAnesTime"];
				$elem_anesthetic = $row["anesthetic"];
				/*$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]);
				$elem_posIopGonio=assignZero($row["flagPosIopGonio"]);*/
				/*$elem_sumOdGon = $row["gonio_od_summary"];
				$elem_sumOsGon = $row["gonio_os_summary"];*/
				/*
				$Drawing_OD = $this->isAppletDrawn($row["gonio_od_drawing"]);
				$Drawing_OS= $this->isAppletDrawn($row["gonio_os_drawing"]);
				$elem_txtDesc_iop = $row["desc_ig"];
				$elem_posGonio=$row["posGonio"];
				$elem_posDrawGonio=$row["posDrawGonio"];
				$elem_wnlGonio=$row["wnlGonio"];
				$elem_wnlDrawGonio=$row["wnlDrawGonio"];
				$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
				$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
				$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
				$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
				*/
			}
			}//

			$elem_posIop = 0;
			$elem_wnlIop = 0;
			$elem_wnlIopOd=0;
			$elem_wnlIopOs=0;
			//BG
			$bgColor_IOP = "bgSmoke";
		}

		if(empty($elem_gonio_id)){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			$tmp = "";
			$tmp .="c2.gonio_od_drawing, c2.isPositive AS flagPosIopGonio, c2.wnl AS flagWnlIopGonio, c2.examined_no_change AS chGon, ";
			$tmp .="c2.gonio_os_drawing, c2.gonio_od_summary, c2.gonio_os_summary, ";
			$tmp .="c2.wnlOd AS wnlGonioOd, c2.wnlOs AS wnlGonioOs, c2.desc_ig, ";
			$tmp .="c2.posGonio, c2.posDraw AS posDrawGonio, c2.wnlGonio,
					c2.wnlDraw AS wnlDrawGonio, c2.noChange_drawing,c2.examDateGonio, c2.idoc_drawing_id, ";
			$tmp .="c2.wnlDrawOd AS wnlDrawGonioOd, c2.wnlDrawOs AS wnlDrawGonioOs, c2.gonio_id, c2.wnl_value, ";
			$tmp .= "c2.statusElem AS se_gon ";
			//$row = valNewRecordGonio($this->pid, $tmp);
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			if($row != false){
				$elem_wnlIopGonio=assignZero($row["flagWnlIopGonio"]);
				$elem_posIopGonio=assignZero($row["flagPosIopGonio"]);
				$elem_sumOdGon = $row["gonio_od_summary"];
				$elem_sumOsGon = $row["gonio_os_summary"];

				$Drawing_OD = $this->isAppletDrawn($row["gonio_od_drawing"]);
				$Drawing_OS= $this->isAppletDrawn($row["gonio_os_drawing"]);
				$elem_txtDesc_iop = stripslashes($row["desc_ig"]);
				$elem_posGonio=$row["posGonio"];
				$elem_posDrawGonio=$row["posDrawGonio"];
				$elem_wnlGonio=$row["wnlGonio"];
				$elem_wnlDrawGonio=$row["wnlDrawGonio"];
				$elem_wnlGonioOd=assignZero($row["wnlGonioOd"]);
				$elem_wnlGonioOs=assignZero($row["wnlGonioOs"]);
				$elem_wnlDrawGonioOd=assignZero($row["wnlDrawGonioOd"]);
				$elem_wnlDrawGonioOs=assignZero($row["wnlDrawGonioOs"]);
				$elem_wnl_value_gonio=$row["wnl_value"];

				$elem_ncGonio = assignZero($row["chGon"]);
				$elem_ncDrawGonio = assignZero($row["noChange_drawing"]);
				$examdate = wv_formatDate($row["examDateGonio"]);
				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $row["gonio_id"];
				}elseif( !empty($row["gonio_od_drawing"]) ){
					$ardrawApp = array($row["gonio_od_drawing"]);
				}
				$elem_se_Gonio_prev = $row["se_gon"];

			}
			//BG
			$bgColor_gonio = "bgSmoke";
		}

		//* Stoped
		if(!isset($GLOBALS["STOP_PRV_IOP_SUMM"]) || empty($GLOBALS["STOP_PRV_IOP_SUMM"])){
		if(empty($elem_dia_id) ){ //&& ($finalize_flag == 0)
			$tmp = "";
			$tmp .= "chart_dialation.patientAllergic, chart_dialation.pheny25, chart_dialation.tropicanide,".
					"chart_dialation.cyclogel, chart_dialation.dilated_other, chart_dialation.dilated_time,".
					"chart_dialation.dia_id, chart_dialation.eyeSide,chart_dialation.noDilation,chart_dialation.mydiacyl5, chart_dialation.dilation,chart_dialation.statusElem AS statusElem_Dilation, ".
					"chart_dialation.unableDilation, chart_dialation.modi_note_DilationOd, chart_dialation.modi_note_DilationOs, chart_dialation.sumDilation_od, chart_dialation.sumDilation_os,".
					" chart_dialation.spDialTime,chart_dialation.modi_note_Arr AS modi_note_DilaArr ";
			$ochartdilation = new ChartDilation($this->pid, $this->fid);
			$res = $ochartdilation->valNewRecordDialation($tmp,"0",$elem_dos);
			for($i=0;$row=sqlFetchArray($res);$i++)	{
				//Dialation
				$elem_pheny25 = $row["pheny25"];
				$elem_tropicanide = $row["tropicanide"];
				$elem_cyclogel = $row["cyclogel"];
				$elem_dilated_other = $row["dilated_other"];
				$elem_dilated_time = $row["dilated_time"];
				$elem_eyeSide = $row["eyeSide"];
				$patientAllergic=assignZero($row["patientAllergic"]);
				$elem_no_dilation = $row["noDilation"];
				$elem_mydiacyl5=$row["mydiacyl5"];
				$elem_dilation=$row["dilation"];
				$elem_se_dilation_prv = $row["statusElem_Dilation"];
				$unableDilation=$row["unableDilation"];
				$dilated_mm=$row["dilated_mm"];
				$elem_sumDilationOd =$row["sumDilation_od"];
				$elem_sumDilationOs =$row["sumDilation_os"];
				$elem_spDialTime = $row["spDialTime"];
				$elem_dia_id_prv = $row["dia_id"];
			}
			//BG
			$bgColor_dialation = "bgSmoke";
		}//*/

		//OOD
		if(empty($elem_ood_id) ){
			$tmp = "";
			$tmp = "".
				"chart_ood.ood_id, chart_ood.ood,chart_ood.statusElem AS statusElem_OOD, chart_ood.eye AS eye_ood,
				chart_ood.sumOOD_od, chart_ood.sumOOD_os, chart_ood.modi_note_OodOd, chart_ood.modi_note_OodOs,
				chart_ood.spOODTime, chart_ood.modi_note_Arr  AS modi_note_oodArr";
			$ocood = new ChartOOD($this->pid, $this->fid);
			$res = $ocood->valNewRecordOod($tmp,"0",$elem_dos);
			for($i=0;$row=sqlFetchArray($res);$i++)	{
				//C15-chart_ood
				$ood = $row["ood"];
				$statusElem_OOD_prv = $row["statusElem_OOD"];
				$elem_spOODTime = $row["spOODTime"];
				$elem_sumOOD_od = $row["sumOOD_od"];
				$elem_sumOOD_os = $row["sumOOD_os"];
				$modi_note_OodOd = $row["modi_note_OodOd"];
				$modi_note_OodOs = $row["modi_note_OodOs"];
				$elem_ood_id_prv = $row["ood_id"];

			}
			$bgColor_ood = "bgSmoke";
		}
		}//
		//---------

		//StatusFlag IOP
		$flgSe_IOP = 0;
		if(!isset($bgColor_IOP)){
			if(!empty($elem_se_iop)){
				$flgSe_IOP=1;
			}
		}

		//Anes
		$spAnesTime="";
		$sumAnes = $sumAnes_od = $sumAnes_os = "";
		if(!empty($elem_anesthetic)){

			$sumAnes_od = $elem_sumAnesOd;
			$sumAnes_os = $elem_sumAnesOs;
			$spAnesTime = $elem_spAnesTime;

			/*
			$arrAnes=unserialize($elem_anesthetic);
			$tmp = count($arrAnes);
			$spAnesTime = $arrAnes[$tmp-1]["time"];

			// *
			//Test
			for($a=0;$a<$tmp;$a++){
				if((!empty($arrAnes[$a]["anes"])||!empty($arrAnes[$a]["other"]))&&!empty($arrAnes[$a]["time"])){

					$t = "";
					if(!empty($arrAnes[$a]["anes"])){
						$t .= $arrAnes[$a]["anes"];
					}

					if(!empty($arrAnes[$a]["other"])){
						if(!empty($t)){$t .= ",";}
						$t .= "".$arrAnes[$a]["other"];
					}

					$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
					$t = str_replace(",",", ",$t);
					$sumAnes .= $t." ".trim($arrAnes[$a]["time"])."<br/>";
				}
			}

			$sumAnes_od = $sumAnes_os = $sumAnes;
			//Test * /
			*/

		}/*
		else if(!empty($elem_tetracaine) || !empty($elem_flourocaine) || !empty($elem_alcaine)){
			$spAnesTime = $elem_iop_time;

			$t = "";
			if(!empty($elem_tetracaine)) { $t .= "Tetracaine,"; }
			if(!empty($elem_flourocaine)) { $t .= "Flourocaine, "; }
			if(!empty($elem_alcaine)) { $t .= "Alcaine "; }
			$t .= " ".$spAnesTime;
			$sumAnes = $sumAnes_od = $sumAnes_os =  $t;
		}*/

		//Dilation --
		$spDialTime="";
		//$arrDilatInx = array("pheny25","mydiacyl5","mydiacyl1","Cyclogyl");
		//$arrDilatVals = array("Phenylephrine 2.5%","Mydriacyl 1/2%","Mydriacyl 1%","Cyclogyl 1%");

		//echo "<br/>HE: ".$elem_dia_id." - ".$elem_se_dilation;
		$sumDilation = $sumDilation_od = $sumDilation_os = "";
		if((!empty($elem_dia_id) && !empty($elem_se_dilation)) || (!empty($elem_se_dilation_prv) && !empty($elem_dia_id_prv))){

			$sumDilation_od = $elem_sumDilationOd;
			$sumDilation_os = $elem_sumDilationOs;
			$spDialTime = $elem_spDialTime;

			/*
			if(!empty($elem_dilation)){

				if(!empty($elem_sumDilation)){
					$sumDilation = $elem_sumDilation;

				}else{

					/*
					//Need to remove with an update ---------
					$arrDilation=unserialize($elem_dilation);
					$tmp = count($arrDilation);
					for($a=0;$a<$tmp;$a++){
						if((!empty($arrDilation[$a]["dilate"])||!empty($arrDilation[$a]["other_desc"]))&&!empty($arrDilation[$a]["time"])){

							//if(empty($spDialTime)){
								$spDialTime = trim($arrDilation[$a]["time"].' '.$elem_eyeSide);
								//break;
							//}

							$t = "";
							if(!empty($arrDilation[$a]["dilate"])){
								$t = str_replace($arrDilatInx,$arrDilatVals,$arrDilation[$a]["dilate"]);
							}

							if(!empty($arrDilation[$a]["other_desc"])){
								$t = str_replace("Other", "".$arrDilation[$a]["other_desc"],$t);
							}
							$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
							$t = str_replace(",",", ",$t);
							$sumDilation .= $t." ".trim($arrDilation[$a]["time"])."<br/>";

						}
					}
					//Need to remove with an update ---------
					* /
				}

			}
			/*
			else {
				//Need to remove with an update ---------
				if(!empty($elem_pheny25) || !empty($elem_tropicanide) || !empty($elem_cyclogel) || !empty($elem_mydiacyl5) ||
					!empty($elem_dilated_other)){
					$spDialTime = trim($elem_dilated_time.' '.$elem_eyeSide);

					$t = "";
					if(!empty($elem_pheny25)) {$t .= "Phenylephrine 2.5%,";}
					if(!empty($elem_tropicanide)) {$t .= "Mydriacyl 1%,";}
					if(!empty($elem_cyclogel)) {$t .= "Cyclogyl 1%,";}
					if(!empty($elem_mydiacyl5)) {$t .= "Mydriacyl 5%,";}
					if(!empty($elem_dilated_other)) {$t .= $elem_dilated_other.",";}
					$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
					$t = str_replace(",",", ",$t);
					$sumDilation .= $t." ".$elem_dilated_time."<br/>";

				}
				//Need to remove with an update ---------
			}* /

			if($elem_eyeSide=="OU"||$elem_eyeSide=="OD"){
				$sumDilation_od=$sumDilation;
			}
			if($elem_eyeSide=="OU"||$elem_eyeSide=="OS"){
				$sumDilation_os=$sumDilation;
			}
			*/
			/*
			if($elem_no_dilation==1){
				$spDialTime = "No Dilation";
			}else if($unableDilation==1){
				$spDialTime = "Refuse Dilation";
			}
			*/

		}
		//Dilation --

		//OOD
		$spOODTime="";
		$sumOOD = $sumOOD_od = $sumOOD_os = "";
		//$arrOODInx = array("Piol","Alphagan","Iopidine","Diamox");
		//$arrOODVals = array("Pilo 1%","Alphagan P 0.1%","Iopidine 0.5%","Diamox");

		if((!empty($elem_ood_id) && !empty($statusElem_OOD)) || (!empty($elem_ood_id_prv) && !empty($statusElem_OOD_prv))){
			$sumOOD_od = $elem_sumOOD_od;
			$sumOOD_os = $elem_sumOOD_os;
			$spOODTime = $elem_spOODTime;

			/*
			if(!empty($ood)){
				$arrOOD=unserialize($ood);
				$tmp = count($arrOOD);
				for($a=0;$a<$tmp;$a++){
					if((!empty($arrOOD[$a]["ood"])||!empty($arrOOD[$a]["other_desc"]))&&!empty($arrOOD[$a]["time"])){

						$t = "";
						if(!empty($arrOOD[$a]["ood"])){
							$t = str_replace($arrOODInx,$arrOODVals,$arrOOD[$a]["ood"]);
						}

						if(!empty($arrOOD[$a]["other_desc"])){
							$t = str_replace("Other", "".$arrOOD[$a]["other_desc"],$t);
						}
						$t = preg_replace("/(\s)*\,(\s)*$/i", "", $t);
						$t = str_replace(",",", ",$t);
						$sumOOD .= $t." ".trim($arrOOD[$a]["time"])."<br/>";
					}
				}
			}
			*/
		}

		/*
		if($eye_ood=="OU"||$eye_ood=="OD"){
			$sumOOD_od=$sumOOD;
		}
		if($eye_ood=="OU"||$eye_ood=="OS"){
			$sumOOD_os=$sumOOD;
		}
		*/
		//OOD

		//----
		//is Change is made in new chart -----
			$flgSe_Gonio_Od = $flgSe_Gonio_Os = "0";
			$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
			if(!isset($bgColor_gonio)){
				if(!empty($elem_se_Gonio)){
					$tmpArrSe = $this->se_elemStatus("GONIO","0",$elem_se_Gonio);
					$flgSe_Gonio_Od = $tmpArrSe["Iop"]["od"];
					$flgSe_Gonio_Os = $tmpArrSe["Iop"]["os"];
					$flgSe_Draw_Od = $tmpArrSe["Iop3"]["od"];
					$flgSe_Draw_Os = $tmpArrSe["Iop3"]["os"];
				}
			}else{
				if(!empty($elem_se_Gonio_prev)){
					$tmpArrSe_prev = $this->se_elemStatus("GONIO","0",$elem_se_Gonio_prev);
					$flgSe_Gonio_Od_prev = $tmpArrSe_prev["Iop"]["od"];
					$flgSe_Gonio_Os_prev = $tmpArrSe_prev["Iop"]["os"];
					$flgSe_Draw_Od_prev = $tmpArrSe_prev["Iop3"]["od"];
					$flgSe_Draw_Os_prev = $tmpArrSe_prev["Iop3"]["os"];
				}
			}
		//is Change is made in new chart -----

		//WNL
		//Gonio --
		$wnlString = !empty($elem_wnl_value_gonio) ? $elem_wnl_value_gonio : $this->getExamWnlStr("Gonio");
		$wnlStringOd = $wnlStringOs = $wnlString;

		if(empty($flgSe_Gonio_Od) && empty($flgSe_Gonio_Od_prev) && !empty($elem_wnlGonioOd)){ $tmp = $this->getExamWnlStr_fromPrvExm("Gonio", "OD"); if(!empty($tmp)){ $wnlStringOd = $tmp;}  }
		if(empty($flgSe_Gonio_Os) && empty($flgSe_Gonio_Os_prev) && !empty($elem_wnlGonioOs)){  $tmp = $this->getExamWnlStr_fromPrvExm("Gonio", "OS"); if(!empty($tmp)){ $wnlStringOs = $tmp;}  }

		list($elem_sumOdGon,$elem_sumOsGon) = $this->oOnload->setWnlValuesinSumm(array("wValOd"=>$wnlStringOd,"wValOs"=>$wnlStringOs,
								"wOd"=>$elem_wnlGonioOd,"sOd"=>$elem_sumOdGon,
								"wOs"=>$elem_wnlGonioOs,"sOs"=>$elem_sumOsGon));
		//Nochanged
		if(!empty($elem_se_Gonio)&&strpos($elem_se_Gonio,"=1")!==false){
			$elem_noChangeGonio=1;
		}

		//Archive IOP --
		$arrDivArcCmn=array();
		if($bgColor_IOP != "bgSmoke"){
			$oChartRecArc->setChkTbl("chart_iop");
			$arrInpArc = array("elem_sumOdIOP"=>array("sumOdIop",$elem_sumOdIop,"smof","","",$modi_note_IopOd),
								"elem_sumOsIOP"=>array("sumOsIop",$elem_sumOsIop,"smof","","",$modi_note_IopOs));
			$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
			//OD
			if(!empty($arTmpRecArc["div"]["elem_sumOdIOP"])){
				//echo $arTmpRecArc["div"]["elem_sumOdIOP"];
				$arrDivArcCmn["IOP"]["OD"]= $arTmpRecArc["div"]["elem_sumOdIOP"];
				$moeArc["od"]["Iop"] = $arTmpRecArc["js"]["elem_sumOdIOP"];
				$flgArcColor["od"]["Iop"] = $arTmpRecArc["css"]["elem_sumOdIOP"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOdIOP"])) $elem_sumOdIop = $arTmpRecArc["curText"]["elem_sumOdIOP"];
			}else{
				$moeArc["od"]["Iop"]=$flgArcColor["od"]["Iop"]="";
			}
			//Os
			if(!empty($arTmpRecArc["div"]["elem_sumOsIOP"])){
				//echo $arTmpRecArc["div"]["elem_sumOsIOP"];
				$arrDivArcCmn["IOP"]["OS"]= $arTmpRecArc["div"]["elem_sumOsIOP"];
				$moeArc["os"]["Iop"] = $arTmpRecArc["js"]["elem_sumOsIOP"];
				$flgArcColor["os"]["Iop"] = $arTmpRecArc["css"]["elem_sumOsIOP"];
				if(!empty($arTmpRecArc["curText"]["elem_sumOsIOP"])) $elem_sumOsIop = $arTmpRecArc["curText"]["elem_sumOsIOP"];
			}else{
				$moeArc["os"]["Iop"]=$flgArcColor["os"]["Iop"]="";
			}
		}
		//Archive IOP --

		//Archive Gonio --
		if($bgColor_gonio != "bgSmoke"){

		$oChartRecArc->setChkTbl("chart_gonio");
		$arrInpArc = array("elem_sumOdGonio"=>array("gonio_od_summary",$elem_sumOdGon,"","wnlOd", $wnlString, $modi_note_GonioOd),
							"elem_sumOsGonio"=>array("gonio_os_summary",$elem_sumOsGon,"","wnlOs", $wnlString, $modi_note_GonioOs));
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);

		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdGonio"])){
			//echo $arTmpRecArc["div"]["elem_sumOdGonio"];
			$arrDivArcCmn["Gonio"]["OD"]= $arTmpRecArc["div"]["elem_sumOdGonio"];
			$moeArc["od"]["Gonio"] = $arTmpRecArc["js"]["elem_sumOdGonio"];
			$flgArcColor["od"]["Gonio"] = $arTmpRecArc["css"]["elem_sumOdGonio"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdGonio"])) $elem_sumOdGon = $arTmpRecArc["curText"]["elem_sumOdGonio"];
		}else{
			$moeArc["od"]["Gonio"]=$flgArcColor["od"]["Gonio"]="";
		}
		//Os
		if(!empty($arTmpRecArc["div"]["elem_sumOsGonio"])){
			//echo $arTmpRecArc["div"]["elem_sumOsGonio"];
			$arrDivArcCmn["Gonio"]["OS"]= $arTmpRecArc["div"]["elem_sumOsGonio"];
			$moeArc["os"]["Gonio"] = $arTmpRecArc["js"]["elem_sumOsGonio"];
			$flgArcColor["os"]["Gonio"] = $arTmpRecArc["css"]["elem_sumOsGonio"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOsGonio"])) $elem_sumOsGon = $arTmpRecArc["curText"]["elem_sumOsGonio"];
		}else{
			$moeArc["os"]["Gonio"]=$flgArcColor["os"]["Gonio"]="";
		}

		}
		//Archive Gonio --



		//Purged --------
		$htmPurge = $this->getPurgedHtm();
		//Purged --------

		//Modified Notes ----
		//if Edit is not Done && modified Notes exists
		//iop
		if(!empty($modi_note_IopOd) && empty($moeArc["od"]["Iop"])){ //Od
			list($moeMN["od"]["Iop"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdIOP", $modi_note_IopOd);
			//echo $tmpDiv;
			$arrDivArcCmn["IOP"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["Iop"]="";
		}
		if(!empty($modi_note_IopOs) && empty($moeArc["os"]["Iop"])){ //OS
			list($moeMN["os"]["Iop"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOsIOP", $modi_note_IopOs);
			//echo $tmpDiv;
			$arrDivArcCmn["IOP"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["Iop"]="";
		}

		//gonio
		if(!empty($modi_note_GonioOd) && empty($moeArc["od"]["Gonio"])){ //Od
			list($moeMN["od"]["Gonio"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdGonio", $modi_note_GonioOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Gonio"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["Gonio"]="";
		}
		if(!empty($modi_note_GonioOs) && empty($moeArc["os"]["Gonio"])){ //Os
			list($moeMN["os"]["Gonio"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOsGonio", $modi_note_GonioOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Gonio"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["Gonio"]="";
		}

		//No Edit is working for Anesthetic+Ans Div is not known
		if(!empty($modi_note_AnestheticOd) && empty($moeArc["od"]["Anes"])){ //OS
			list($moeMN["od"]["Anes"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdAnesthetic", $modi_note_AnestheticOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Anesthetic"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["Anes"]="";
		}
		//
		if(!empty($modi_note_AnestheticOs) && empty($moeArc["os"]["Anes"])){ //OS
			list($moeMN["os"]["Anes"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOsAnesthetic", $modi_note_AnestheticOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Anesthetic"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["Anes"]="";
		}

		//
		//No Edit is working for Dilation+Dilation Div is not known
		if(!empty($modi_note_DilationOd) && empty($moeArc["od"]["dilation"])){ //OD
			list($moeMN["od"]["dilation"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdDilation", $modi_note_DilationOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Dilation"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["dilation"]="";
		}
		if(!empty($modi_note_DilationOs) && empty($moeArc["os"]["dilation"])){ //OS
			list($moeMN["os"]["dilation"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOsDilation", $modi_note_DilationOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Dilation"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["dilation"]="";
		}

		//No Edit is working for OOD+OOD Div is not known
		if(!empty($modi_note_OodOd) && empty($moeArc["od"]["ood"])){ //OD
			list($moeMN["od"]["ood"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdOOD", $modi_note_OodOd);
			//echo $tmpDiv;
			$arrDivArcCmn["Ophth. Drops"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["ood"]="";
		}
		if(!empty($modi_note_OodOs) && empty($moeArc["os"]["ood"])){ //OS
			list($moeMN["os"]["ood"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOsOOD", $modi_note_OodOs);
			//echo $tmpDiv;
			$arrDivArcCmn["Ophth. Drops"]["OS"]=$tmpDiv;
		}else{
			$moeMN["os"]["ood"]="";
		}

		//Drawing
		if(!empty($modi_note_Draw)){ //Os
			list($moeMN["od"]["draw"],$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_drawOdGonio", $modi_note_Draw);
			//echo $tmpDiv;
			$arrDivArcCmn["Drawing"]["OD"]=$tmpDiv;
		}else{
			$moeMN["od"]["draw"]="";
		}
		//Modified Notes ----

		//create common div and echo ---
		//list($moeMN,$tmpDiv) = mkDivArcCmn("Gonio",$arrDivArcCmn);
		if($post["webservice"] != "1"){
		list($moeMN,$tmpDiv) = $this->oOnload->mkDivArcCmnNew("Gonio",$arrDivArcCmn,$arrHx);
		}
		$echo.= $tmpDiv;
		//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";

		$arr=array();
		$arr["ename"] = "IOP/Gonio";

		//IOP
		$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"IOP",
											"sOd"=>$elem_sumOdIop,"sOs"=>$elem_sumOsIop,
											"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
											//"arcJsOd"=>$moeArc["od"]["Iop"],"arcJsOs"=>$moeArc["os"]["Iop"],
											"arcCssOd"=>$flgArcColor["od"]["Iop"],"arcCssOs"=>$flgArcColor["os"]["Iop"],
											//"mnOd"=>$moeMN["od"]["Iop"],"mnOs"=>$moeMN["os"]["Iop"],
											"enm_2"=>"IOP"));
		//Gonio
		$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Gonio",
											"sOd"=>$elem_sumOdGon,"sOs"=>$elem_sumOsGon,
											"fOd"=>$flgSe_Gonio_Od,"fOs"=>$flgSe_Gonio_Os,"pos"=>$elem_posGonio,
											//"arcJsOd"=>$moeArc["od"]["Gonio"],"arcJsOs"=>$moeArc["os"]["Gonio"],
											"arcCssOd"=>$flgArcColor["od"]["Gonio"],"arcCssOs"=>$flgArcColor["os"]["Gonio"],
											///"mnOd"=>$moeMN["od"]["Gonio"],"mnOs"=>$moeMN["os"]["Gonio"],
											"enm_2"=>"Gonio"));

		//Anes : No archive working  for Anes
		$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Anesthetic",
											"sOd"=>$sumAnes_od,"sOs"=>$sumAnes_os,
											"fOd"=>$flgSe_IOP,"fOs"=>$flgSe_IOP,
											//"arcJsOd"=>$moeArc["od"]["Anes"],"arcJsOs"=>$moeArc["os"]["Anes"],
											"arcCssOd"=>$flgArcColor["od"]["Anes"],"arcCssOs"=>$flgArcColor["os"]["Anes"],
											//"mnOd"=>$moeMN["od"]["Anes"],"mnOs"=>$moeMN["os"]["Anes"],
											"enm_2"=>"Anesthetic"));

		//Dilation: No archive working  for dilate
		$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Dilation",
											"sOd"=>$sumDilation_od,"sOs"=>$sumDilation_os,
											"fOd"=>$elem_se_dilation,"fOs"=>$elem_se_dilation,
											//"arcJsOd"=>$moeArc["od"]["dilation"],"arcJsOs"=>$moeArc["os"]["dilation"],
											"arcCssOd"=>$flgArcColor["od"]["dilation"],"arcCssOs"=>$flgArcColor["os"]["dilation"],
											//"mnOd"=>$moeMN["od"]["dilation"],"mnOs"=>$moeMN["os"]["dilation"],
											"enm_2"=>"Dilation"));

		//OOD: No archive working  for ood
		$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Ophth. Drops",
											"sOd"=>$sumOOD_od,"sOs"=>$sumOOD_os,
											"fOd"=>$statusElem_OOD,"fOs"=>$statusElem_OOD,
											//"arcJsOd"=>$moeArc["od"]["ood"],"arcJsOs"=>$moeArc["os"]["ood"],
											"arcCssOd"=>$flgArcColor["od"]["ood"],"arcCssOs"=>$flgArcColor["os"]["ood"],
											//"mnOd"=>$moeMN["od"]["ood"],"mnOs"=>$moeMN["os"]["ood"],
											"enm_2"=>"OOD"));

		//Drawing: must be  at end of array:
		$arr["subExm"][] = $this->oOnload->getArrExms_ms(array("enm"=>"Drawing",
											"sOd"=>$drawdocId,"sOs"=>"",
											"fOd"=>$flgSe_Draw_Od,"fOs"=>$flgSe_Draw_Os,"pos"=>$elem_posDrawGonio,
											"enm_2"=>"DrawGonio"));

		//Sub Exam List
		$arr["seList"] = 	array("IOP"=>array("enm"=>"IOP","pos"=>$elem_posIop,
							"wOd"=>$elem_wnlIopOd,"wOs"=>$elem_wnlIopOs),
						"Gonio"=>array("enm"=>"Gonio","pos"=>$elem_posGonio,
							"wOd"=>$elem_wnlGonioOd,"wOs"=>$elem_wnlGonioOs),
						"DrawGonio"=>array("enm"=>"Drawing","pos"=>$elem_posDrawGonio,
							"wOd"=>$elem_wnlDrawGonioOd,"wOs"=>$elem_wnlDrawGonioOs)
						);
		$arr["nochange"] = $elem_noChangeGonio;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc_iop;
		$arr["Dilation"] = array("Anesthetic"=>$spAnesTime,"Dilate"=>$spDialTime,"PtAllergic"=>$patientAllergic,"OOD"=>$spOODTime,
						"bgColor_IOP"=>$bgColor_IOP, "bgColor_dialation"=>$bgColor_dialation, "bgColor_ood"=>$bgColor_ood );
		$arr["bgColor"] = "".$bgColor_gonio;
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
		$arr["examdate"] = $examdate;
		$arr["htmPurge"] = $htmPurge;
		$arr["moeMN"] = $moeMN;
		$arr["flgGetDraw"] = $this->oOnload->onwv_isDrawingChanged(array($elem_wnlDrawGonioOd,$elem_wnlDrawGonioOs,$elem_ncDrawGonio,$elem_ncDrawGonio));
		$arr["exm_flg_se"] = array($flgSe_Gonio_Od,$flgSe_Gonio_Os);

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

	function unify_utelem($bstr, $adstr){

		if(!empty($adstr)){
			$ar_adstr = explode("|", $adstr);
			if(count($ar_adstr)>0){
				foreach($ar_adstr as $k => $v){
					$v = trim($v);
					if(!empty($v)){
						$ar_v = explode("@", $v);
						$uid = trim($ar_v[0]);
						$elstr = trim($ar_v[1]);

						if(!empty($elstr)){
							$ar_elstr = explode(",", $elstr);
							if(count($ar_elstr)>0){
								$ttmp = "";
								foreach($ar_elstr as $k1 => $v1){
									$v1 = trim($v1);
									if(!empty($v1)){
										if(strpos($bstr, $v1)===false){
											$ttmp = $v1.",";
										}
									}
								}

								if(!empty($ttmp)){
									$ttmp = "|".$uid."@".$ttmp;
									$bstr .= $ttmp;
								}
							}
						}
					}
				}
			}
		}

		return $bstr;
	}

	function load_exam($finalize_flag = 0){
		$oWv = new WorkView();
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;

		$OBJDrawingData = new CLSDrawingData();
		$blEnableHTMLDrawing = false;
		$blEnableHTMLDrawing = $OBJDrawingData->getHTMLDrawingDispStatus();
		$drawCntlNum=2; //This setting will decide number of drawing instances
		list($idoc_arrDrwIcon, $idoc_htmlDrwIco) = $OBJDrawingData->drwico_getDrwIcon();

		$patient_id_asli = $patient_id;
		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);

		//
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		$ProClr = User::getProviderColors();//
		//$logged_user_type = $_SESSION["logged_user_type"];
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];

		//obj work view
		$owv = new WorkView();
		$ochartiop = new ChartIop($patient_id, $elem_formId);

		$arrTabs = array("Iop1"=>"IOP","Iop"=>"Gonioscopy","Iop3"=>"Drawing");
		$myflag=false;
		$myflagGonio=false;
		$elem_editMode="0";
		$elem_iopGonId="";
		$elem_isPositive = 0;
		$elem_wnl=0;
		$elem_examDate=date("Y-m-d H:i:s");
		$tabId = $_GET['tabId'];
		$elem_noChange=0;
		$elem_wnlGonioOd=0;
		$elem_wnlGonioOs=0;
		$elem_wnlDrawOd=0;
		$elem_wnlDrawOs=0;
		$elem_posGonio=0;
		$elem_posDraw=0;
		$elem_wnlGonio=0;
		$elem_wnlDraw=0;
		$elem_ci_iop=$elem_chng_iop=0;
		$elem_ci_dilation=$elem_chng_dilation=0;
		$elem_ci_gonio=0;
		$elem_cor_date=wv_formatDate(date("Y-m-d"));
		$arrAnes=array();
		$arrDilation= $arrOOD=array();
		$elem_ci_OOD=$elem_chng_OOD=0;
		$blDrwaingGray = false;

		//DOS
		$elem_dos=$this->getDos(1);

		//Chart_iop
		$sql = "SELECT * FROM chart_iop  WHERE form_id = '".$form_id."' AND patient_id='".$patient_id."' AND purged = '0' ";
		$res = imw_query($sql);
		if(imw_num_rows($res)<=0){ /* Show Previous values in finalized chart also ~&& ($finalize_flag == 0)~ */
			$res = $ochartiop->valNewRecordIop();
			$myflag=true;
		}

		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			$res = $ochartiop->valNewRecordIop(" * ", "1");
			$elem_ci_iop=1;
			$myflag=true;
		}

		if(imw_num_rows($res)>0){
			extract(sqlFetchArray($res));
			$patient_id = $patient_id_asli;//just to be sure.
			$iop_custom_field = stripslashes($custom_field);
			if($myflag){
				$elem_editMode=0;
			}else{
				$elem_editMode=1;
			}
			$elem_iopGonId=$iop_id;

			if($elem_editMode==1){
				$elem_examDate=$exam_date;
				$elem_chng_iop=$statusElem;
			}

			$elem_notApplicable=$not_applicable;
			//$elem_wnl=$wnl;
			$elem_descTa=$descTa;
			$elem_descTp=$descTp;
			$elem_descTx=$descTx;
			$time_up = $iop_time; // Time
			$elem_desc_ig = $desc_ig;
			$elem_anesthetic = $anesthetic;
			$elem_utElems = $ut_elem;
		}

		//Do Not carry forward values
		if($elem_editMode==0){
			$applanation = "";
			$puff = "";
			$tx = "";
			$app_od = "";
			$app_os_1 = "";
			$app_time = "";
			$puff_od = "";
			$puff_os_1 = "";
			$puff_time = "";
			$tx_od = "";
			$tx_os = "";
			$tx_time = "";
			$tetracaine = "";
			$flourocaine = "";
			$alcaine = "";
			$time_up = "";
			$elem_anesthetic = "";
			$elem_utElems = "";


			//get from def
			 $row = $ochartiop->getIopTrgtDef();
			 $trgtOd = $row["iopTrgtOd"];
			 $trgtOs = $row["iopTrgtOs"];
			if( empty($trgtOd) && empty($trgtOs) ){
				//getFrom Glucoma
				list($trgtOdTemp, $trgtOsTemp) = $ochartiop->getGlucomaTargetIop();
				if(!empty($trgtOdTemp)){
					$trgtOd = $trgtOdTemp;
				}
				if(!empty($trgtOsTemp)){
					$trgtOs = $trgtOsTemp;
				}
			}
		}

		//Empty if od/os has values
		if(empty($app_od) && empty($app_os_1)){
			$elem_descTa = $elem_descTaTmp;
		}else if($elem_descTa == $elem_descTaTmp){
			$elem_descTa = "";
		}

		if(empty($puff_od) && empty($puff_os_1)){
			$elem_descTp = $elem_descTpTmp;
		}else if($elem_descTp == $elem_descTpTmp){
			$elem_descTp = "";
		}

		if(empty($tx_od) && empty($tx_os)){
			$elem_descTx = $elem_descTxTmp;
		}else if($elem_descTx == $elem_descTxTmp){
			$elem_descTx = "";
		}

		if(!empty($elem_anesthetic)){
			$arrAnes=unserialize($elem_anesthetic);

		}else{
			$tmp="";
			if($tetracaine == "Tetracaine")$tmp.="Tetracaine,";
			if(($flourocaine == "Flourocaine") || ($flourocaine == "Fluorocaine"))$tmp.="Fluorocaine,";
			if($alcaine == "Alcaine")$tmp.="Alcaine";
			$arrAnes[0]["anes"]=$tmp;
			$arrAnes[0]["time"]=$time_up;
		}

		//
		if(!empty($multiple_pressure)){
			$mulPressureArr = $ochartiop->reFormatPressureArr($multiple_pressure);
		}else{
			$mulPressureArr = array();
		}

		if(($finalize_flag==0)){
			$rowU = $ochartiop->getPrvIOPVals();
		}

		//Get Tmax
		if(empty($tmaxOd) || empty($tmaxOs)){
			$glaucoma_obj = New ChartGlucoma($patient_id);
			$ar_mx_t = $glaucoma_obj->getTMax();
			if(empty($tmaxOd)){ $tmaxOd=$ar_mx_t["od"];}
			if(empty($tmaxOs)){ $tmaxOs=$ar_mx_t["os"];}
		}

		//chart gonio
		$sql = "SELECT * FROM chart_gonio WHERE form_id='".$elem_formId."' AND patient_id='".$patient_id."' AND purged = '0'  ";
		$row = sqlQuery($sql);
		if($row==false){
			//$row = valNewRecordGonio($patient_id);
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}

			$myflagGonio=true;
			$blDrwaingGray = true;
		}

		if(isset($_GET["prevValGonio"]) && ($_GET["prevValGonio"] == 1)){
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$elem_ci_gonio=1;
			$myflagGonio=true;
			$blDrwaingGray = true;
		}

		if($row != false){
			if($myflagGonio){
				$elem_editModeGonio=0;
			}else{
				$elem_editModeGonio=1;
				$elem_ci_gonio=1;
			}
			$elem_gonioId_LF=$elem_gonioId=$row["gonio_id"];

			//$elem_formId=$form_id;
			//$elem_patientId=$patient_id ;
			if($elem_editModeGonio==1){
				$elem_noChange=$row["examined_no_change"];
				$elem_noChange_draw=$row["noChange_drawing"];
				$elem_utElems = $this->unify_utelem($elem_utElems, $row["ut_elem"]);
			}
			$elem_examDateGonio = ($elem_editModeGonio==0) ? $elem_examDate : $row["examDateGonio"];
			$elem_wnl=$row["wnl"];
			$iop_gon_od= stripslashes($row["iopGon_od"]);
			$iop_gon_os= stripslashes($row["iopGon_os"]);

			$gonio_custom_field = stripslashes($row['custom_field']);

			/*
			$arriopGonOd = getXmlMenuArray($iop_gon_od);
			$retiopGonOd = getXmlValuesExtracted($arriopGonOd);
			$arriopGonOs = getXmlMenuArray($iop_gon_os);
			$retiopGonOs = getXmlValuesExtracted($arriopGonOs);
			*/

			$arr_vals_iop_gon_od = $oExamXml->extractXmlValue($iop_gon_od);
			extract($arr_vals_iop_gon_od);
			$arr_vals_iop_gon_os = $oExamXml->extractXmlValue($iop_gon_os);
			extract($arr_vals_iop_gon_os);

			$elem_isPositive = $row["isPositive"];
			$elem_gonio_od = $row["gonio_od"];
			$elem_gonio_os = $row["gonio_os"];
			$elem_wnlGonioOd=$row["wnlOd"];
			$elem_wnlGonioOs=$row["wnlOs"];
			$elem_desc_ig = $row["desc_ig"];
			$elem_wnlDrawOd=$row["wnlDrawOd"];
			$elem_wnlDrawOs=$row["wnlDrawOs"];
			$elem_posGonio=$row["posGonio"];
			$elem_posDraw=$row["posDraw"];
			$elem_wnlGonio=$row["wnlGonio"];
			$elem_wnlDraw=$row["wnlDraw"];
			$gonio_od_drawing=$row["gonio_od_drawing"];
			$intDraIUFDB = $row["drawing_insert_update_from"];
			$gonio_os_drawing=$row["gonio_os_drawing"];
			$gonio_od_desc = $row["gonio_od_desc"];
			$gonio_os_desc = $row["gonio_os_desc"];
			$elem_statusElements = ($elem_editModeGonio==0) ? "" : $row["statusElem"];

			$dbIdocDrawingId = $row["idoc_drawing_id"];
			$strCanvasWNL = "yes";
			if($dbIdocDrawingId != ""){
				$arrDrwaingData = array();
				$arrDrwaingData = $OBJDrawingData->getHTMLDrawingData($dbIdocDrawingId, 1);
			}
		}

		//Set Change Indicator values -----
		$arrCI = array("elem_chng_divIop_Od","elem_chng_divIop_Os","elem_chng_divIop3_Od","elem_chng_divIop3_Os");
		for($i=0;$i<4;$i++){
			$tmp = $arrCI[$i];
			if(!empty($elem_statusElements) && (strpos($elem_statusElements,"".$arrCI[$i]."=1") !== false)){
				$$tmp="1";
			}else{
				$$tmp="0";
			}
		}
		//Set Change Indicator values -----

		//Dialation
		$ochartdilation = new ChartDilation($patient_id, $elem_formId);
		$ar_dilation_info = $ochartdilation->getFormInfo();
		extract($ar_dilation_info);

		$elem_utElems = $this->unify_utelem($elem_utElems, $elem_utElems_dilation);

		//Correction Values
		$occv = new ChartCorrectionValues($patient_id, $elem_formId);
		$ar_ccv_info = $occv->getFormInfo();
		extract($ar_ccv_info);

		//OOD
		$ocood = new ChartOOD($patient_id, $elem_formId);
		$ar_cood_info = $ocood->getFormInfo();
		extract($ar_cood_info);
		//--

		//defualt Tab --
		$defTabKey = "Iop1";
		if(isset($_GET["pg"]) && !empty($_GET["pg"])){
			if($_GET["pg"]=="IOP"){
				$defTabKey = "Iop1";
			}else if($_GET["pg"]=="Gonio"){
				$defTabKey = "Iop";
			}else if($_GET["pg"]=="Drawing"){
				$defTabKey = "Iop3";
			}
		}
		//defualt Tab --

		//Anas _db_options
		$oadmn = new Admn();
		$arr_db_drops=$oadmn->get_drop_options_admin();
		$arr_db_anas = $arr_db_drops["anes"];
		$arr_db_dilate = $arr_db_drops["dilate"];
		$arr_db_ood = $arr_db_drops["ood"];
		$iop_def_method=$oadmn->get_iop_def_method();
		//

		//limit for tds
		$tr_lm_gl=array(5,6,5);
		$od_nm_ln = "16";

		//draw
		$intDrawingFormId = $elem_formId;
		$intDrawingExamId = $elem_iopGonId;
		$strScanUploadfor = "IOP_GON_DSU";

		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/iop_gonio.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");
	}
}
?>
