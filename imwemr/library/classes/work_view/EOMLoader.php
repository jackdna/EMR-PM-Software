<?php
class EOMLoader extends EOM{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);
	}
	function getPurgedHtm(){
		$htmPurge="";
		$sql ="SELECT ".
			"c3.isPositive AS flagPosEom, c3.wnl AS flagWnlEom, c3.examined_no_change  AS chEom, ".
			"c3.examined_no_change2, c3.examined_no_change3, ".
			"c3.sumEom, c3.eomDrawing, c3.eom_id, c3.descEom, c3.statusElem, c3.purgerId, c3.purgeTime, ".
			"c3.eomDrawing_2, c3.idoc_drawing_id, c3.wnl_value ".
			"FROM chart_master_table c1 ".
			"INNER JOIN chart_eom c3 ON c3.form_id = c1.id AND c3.purged!='0'  ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ".
			"ORDER BY c3.purgeTime DESC";
		$rz=sqlStatement($sql);
		for($i=0;$row=sqlFetchArray($rz);$i++){
			if(!empty($row["chEom"])&&!empty($row["examined_no_change2"])&&!empty($row["examined_no_change3"])){
				$elem_noChangeEom=assignZero($row["chEom"]);
			}
			$elem_wnlEom=assignZero($row["flagWnlEom"]);
			$elem_posEom=assignZero($row["flagPosEom"]);
			$elem_eomDraw = $row["eomDrawing"];
			$elem_sumEom = $row["sumEom"];
			$elem_eom_id = $row["eom_id"];
			$elem_txtDesc_eom = stripslashes(trim($row["descEom"]));
			$elem_se_Eom=$row["statusElem"];
			$elem_wnl_value=$row["wnl_value"];

			if(!empty($row["idoc_drawing_id"])) {
				$drawdocId = $elem_eom_id;
			}elseif( !empty($row["eomDrawing_2"]) ){
				$ardrawApp = array($row["eomDrawing_2"]);
			}


			//StatusFlag
			$flgSe_Eom = "";
			if(!isset($bgColor_eom)){
				if(!empty($elem_se_Eom) && strpos($elem_se_Eom,'Eom=1')!==false){
					$flgSe_Eom=1;
				}
			}

			$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
			$tmpArrSe=array();
			if(!isset($bgColor_eom)){
				if(!empty($elem_se_Eom)){
					if(strpos($elem_se_Eom,"Eom3=1")!==false){ $flgSe_Draw_Od ="1"; $flgSe_Draw_Os ="1";  }
				}
			}

			//WNL : "~NPC = WNL, EOM = Full & Ortho"
			$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("EOM"); //"Full ductions, alternate cover test - ortho distance and near."
			list($elem_sumEom) = $this->oOnload->setWnlValuesinSumm(array("wVal"=>$wnlString,
															"wOd"=>$elem_wnlEom,"sOd"=>$elem_sumEom));
			//---------

			//Nochanged
			if(!empty($elem_se_Eom)&&strpos($elem_se_Eom,"=1")!==false){
				$elem_noChangeEom=1;
			}

			$arr=array();
			$arr["ename"] = "EOM";
			$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"EOM",
											"sOd"=>$elem_sumEom,
											"fOd"=>$flgSe_Eom,"pos"=>$elem_posEom,
											"arcJsOd"=>$moeArc_od, "arcCssOd"=>$flgArcColor_od,
											"arcJsOs"=>"", "arcCssOs"=>""
											));
			$arr["nochange"] = $elem_noChangeEom;
			$arr["desc"] = htmlentities($elem_txtDesc_eom);
			$arr["bgColor"] = "".$bgColor_eom;
			$arr["purgerId"] = $row["purgerId"];
			$arr["purgeTime"] = $row["purgeTime"];
			$arr["drawdocId"] = $drawdocId;
			$arr["drawapp"] = $ardrawApp;
			$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
			$htmPurge .= $this->oOnload->getSummaryHTML_purged($arr);
		}
		return $htmPurge;

	}
	function getWorkViewSummery($post){

		$oneeye = $post["oe"]; //one eye

		//require("common/ChartRecArc.php");
		//object Chart Rec Archive --
		$oChartRecArc = new ChartRecArc($this->pid,$this->fid,$_SESSION['authId']);
		//---
		$echo="";


		$sql ="SELECT ".
			"c3.isPositive AS flagPosEom, c3.wnl AS flagWnlEom, c3.examined_no_change  AS chEom, ".
			"c3.examined_no_change2, c3.examined_no_change3, c3.wnl_3, ".
			"c3.sumEom, c3.eomDrawing, c3.eom_id, c3.descEom, c3.statusElem, ".
			"c3.modi_note, c3.modi_note_Draw, ".
			"c3.modi_note_Arr, ".
			"c3.eomDrawing_2, c3.idoc_drawing_id, c3.wnl_value ".
			"FROM chart_master_table c1 ".
			"LEFT JOIN chart_eom c3 ON c3.form_id = c1.id AND c3.purged='0'  ".
			"WHERE c1.id = '".$this->fid."' AND c1.patient_id='".$this->pid."' ";
		//$row = sqlQuery($sql);
		//$row=$oEOM->sqlExe($sql);
		$row = sqlQuery($sql);
		if($row != false){
			if(!empty($row["chEom"])&&!empty($row["examined_no_change2"])&&!empty($row["examined_no_change3"])){
				$elem_noChangeEom=assignZero($row["chEom"]);
			}
			$elem_wnlEom=assignZero($row["flagWnlEom"]);
			$elem_posEom=assignZero($row["flagPosEom"]);
			$elem_eomDraw = $row["eomDrawing"];
			$elem_sumEom = $row["sumEom"];
			$elem_eom_id = $row["eom_id"];
			$elem_txtDesc_eom = stripslashes(trim($row["descEom"]));
			$elem_se_Eom=$row["statusElem"];
			$modi_note = $row["modi_note"];
			$modi_note_Draw = $row["modi_note_Draw"];

			$elem_examined_no_change3=$row["examined_no_change3"];
			$elem_wnl_3=$row["wnl_3"];
			$elem_wnl_value = $row["wnl_value"];

			if($row["modi_note_Arr"]!='')
			$arrHx['EOM'] = unserialize($row["modi_note_Arr"]);

			if(!empty($row["idoc_drawing_id"])) {
				$drawdocId = $elem_eom_id;
			}elseif( !empty($row["eomDrawing_2"]) ){
				$ardrawApp = array($row["eomDrawing_2"]);
			}

		}

		//Previous
		if(empty($elem_eom_id)){
			$tmp = "";
			$tmp = "c2.isPositive AS flagPosEom, c2.wnl AS flagWnlEom, c2.examined_no_change  AS chEom, ";
			$tmp .= "c2.descEom, wnl_3, examined_no_change3, ";
			$tmp .= "c2.sumEom, c2.eom_id, ";
			$tmp .= "c2.eomDrawing_2, c2.idoc_drawing_id, c2.wnl_value ";

			$elem_dos=$this->getDos();
			$elem_dos=wv_formatDate($elem_dos,0,0,"insert");
			$res = $this->getLastRecord($tmp,0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			//$res = valNewRecordEom($patient_id, $tmp);
			if($row!=false){
			//for($i=0;$row=sqlFetchArray($res);$i++)	{
				$elem_sumEom = $row["sumEom"];
				$elem_wnlEom=assignZero($row["flagWnlEom"]);
				$elem_posEom=assignZero($row["flagPosEom"]);
				$elem_eomDraw = $row["eomDrawing"];
				$elem_txtDesc_eom = stripslashes($row["descEom"]);
				$elem_examined_no_change3=$row["examined_no_change3"];
				$elem_wnl_3=$row["wnl_3"];
				$elem_wnl_value = $row["wnl_value"];

				if(!empty($row["idoc_drawing_id"])) {
					$drawdocId = $row["eom_id"];
				}elseif( !empty($row["eomDrawing_2"]) ){
					$ardrawApp = array($row["eomDrawing_2"]);
				}

			}
			//BG
			$bgColor_eom = "bgSmoke";
		}

		//StatusFlag
		$flgSe_Eom = "";
		if(!isset($bgColor_eom)){
			if(!empty($elem_se_Eom) && strpos($elem_se_Eom,'Eom=1')!==false){
				$flgSe_Eom=1;
			}
		}

		//WNL
		$wnlString = !empty($elem_wnl_value) ? $elem_wnl_value : $this->getExamWnlStr("EOM");
		list($elem_sumEom) = $this->oOnload->setWnlValuesinSumm(array("wVal"=>$wnlString,
														"wOd"=>$elem_wnlEom,"sOd"=>$elem_sumEom));
		//---------

		//Nochanged
		if(!empty($elem_se_Eom)&&strpos($elem_se_Eom,"=1")!==false){
			$elem_noChangeEom=1;
		}

		//Archive EOM --
		if($bgColor_eom != "bgSmoke"){

		$arrDivArcCmn=array();
		$oChartRecArc->setChkTbl("chart_eom");
		$arrInpArc = array("elem_sumOdEOM"=>array("sumEom",$elem_sumEom,"","wnl",$wnlString,$modi_note));
		$arTmpRecArc = $oChartRecArc->getArcRec($arrInpArc);
		//print_r($arTmpRecArc);
		//OD
		if(!empty($arTmpRecArc["div"]["elem_sumOdEOM"])){
			//echo $arTmpRecArc["div"]["elem_sumOdEOM"];
			$arrDivArcCmn["EOM"]["OD"]= $arTmpRecArc["div"]["elem_sumOdEOM"];
			$moeArc_od = $arTmpRecArc["js"]["elem_sumOdEOM"];
			$flgArcColor_od = $arTmpRecArc["css"]["elem_sumOdEOM"];
			if(!empty($arTmpRecArc["curText"]["elem_sumOdEOM"]))
				$elem_sumEom = $arTmpRecArc["curText"]["elem_sumOdEOM"];
		}else{
			$moeArc_od = $flgArcColor_od="";
		}

		}//
		//Archive EOM --

		//

		$flgSe_Draw_Od = $flgSe_Draw_Os = "0";
		$tmpArrSe=array();
		if(!isset($bgColor_eom)){
			if(!empty($elem_se_Eom)){
				if(strpos($elem_se_Eom,"Eom3=1")!==false){ $flgSe_Draw_Od ="1"; $flgSe_Draw_Os ="1";  }
			}
		}
		//--


		//Purged --------
		$htmPurge = $this->getPurgedHtm();
		//Purged --------

		//Modified Notes ----
		//if Edit is not Done && modified Notes exists
		if(!empty($modi_note) && empty($moeArc_od)){ //Od
			list($moeMN_od,$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_sumOdEOM", $modi_note);
			//echo $tmpDiv;
			$arrDivArcCmn["EOM"]["OD"]= $tmpDiv;
		}else{
			$moeMN_od="";
		}

		//Draw
		if(!empty($modi_note_Draw)){ //Od
			list($moeMN_od,$tmpDiv)=$this->oOnload->getModiNoteConDiv("elem_drawOdEOM", $modi_note_Draw);
			//echo $tmpDiv;
			$arrDivArcCmn["Drawing"]["OD"]= $tmpDiv;
		}else{
			$moeMN_od="";
		}

		//Modified Notes ----

		//create common div and echo ---
		//list($moeMN,$tmpDiv) = mkDivArcCmn("EOM",$arrDivArcCmn);
		if($post["webservice"] != "1"){
		list($moeMN,$tmpDiv) = $this->oOnload->mkDivArcCmnNew("EOM",$arrDivArcCmn,$arrHx);
		}
		$echo.= $tmpDiv;
		//echo "<xmp>CHECKKKKK: ".$tmpDiv."</xmp>";

		$arr=array();
		$arr["ename"] = "EOM";
		$arr["subExm"][0] = $this->oOnload->getArrExms_ms(array("enm"=>"EOM",
										"sOd"=>$elem_sumEom,
										"fOd"=>$flgSe_Eom,"pos"=>$elem_posEom,
										//"arcJsOd"=>$moeArc_od, "arcJsOs"=>"",
										"arcCssOd"=>$flgArcColor_od, "arcCssOs"=>""
										//"mnOd"=>$moeMN_od,"mnOs"=>""
										));
		$arr["nochange"] = $elem_noChangeEom;
		$arr["desc"] = $elem_txtDesc_eom;
		$arr["bgColor"] = "".$bgColor_eom;
		$arr["htmPurge"] = $htmPurge;
		$arr["moeMN"] = $moeMN;
		$arr["flgGetDraw"] = $this->oOnload->onwv_isDrawingChanged(array($elem_wnlDrawRvOd,$elem_wnlDrawRvOs,$elem_ncDrawRv,$elem_ncDrawRv));

		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = array($flgSe_Draw_Od,$flgSe_Draw_Os);
		$arr["elem_hidden_fields"] = array("elem_se_Eom"=>$elem_se_Eom, "elem_posEom"=>$elem_posEom, "elem_wnlEom"=>$elem_wnlEom);


		if($post["webservice"] == "1"){
			$echo ="";
			$str = $this->oOnload->getSummaryHTML_appwebservice($arr);
		}else{
			$str = $this->oOnload->getSummaryHTML($arr);
		}

		$echo.= $str;
		return $echo;
		//---------

	}

	function menu_arr_options(){
		$arr = array();
		//--------------------------GRID MENU OPTIONS --------

		$arr_grid_options_other = array(
		"ALT"=>array("ALT",$arrEmpty,"ALT"),
		"DVD"=>array("DVD",$arrEmpty,"DVD"),
		"L"=>array("L",$arrEmpty,"L"),
		"Ortho"=>array("Ortho",$arrEmpty,"Ortho"),
		"Ortho'"=>array("Ortho'",$arrEmpty,"Ortho'"),
		"R"=>array("R",$arrEmpty,"R")
		);

		$arr_grid_options_num = array(
		"0"=>array("0",$arrEmpty,"0"),
		"0.5"=>array("0.5",$arrEmpty,"0.5"),
		"1"=>array("1",$arrEmpty,"1"),
		"1.5"=>array("1.5",$arrEmpty,"1.5"),
		"2"=>array("2",$arrEmpty,"2"),
		"2.5"=>array("2.5",$arrEmpty,"2.5"),
		"3"=>array("3",$arrEmpty,"3"),
		"3.5"=>array("3.5",$arrEmpty,"3.5"),
		"4"=>array("4",$arrEmpty,"4"),
		"4.5"=>array("4.5",$arrEmpty,"4.5"),
		"5"=>array("5",$arrEmpty,"5"),
		"5.5"=>array("5.5",$arrEmpty,"5.5"),
		"6"=>array("6",$arrEmpty,"6"),
		"6.5"=>array("6.5",$arrEmpty,"6.5"),
		"7"=>array("7",$arrEmpty,"7"),
		"7.5"=>array("7.5",$arrEmpty,"7.5"),
		"8"=>array("8",$arrEmpty,"8"),
		"8.5"=>array("8.5",$arrEmpty,"8.5"),
		"9"=>array("9",$arrEmpty,"9"),
		"9.5"=>array("9.5",$arrEmpty,"9.5"),
		"10"=>array("10",$arrEmpty,"10"),

		"12"=>array("12",$arrEmpty,"12"),
		"14"=>array("14",$arrEmpty,"14"),
		"16"=>array("16",$arrEmpty,"16"),
		"18"=>array("18",$arrEmpty,"18"),
		"20"=>array("20",$arrEmpty,"20"),
		/*
		"22"=>array("22",$arrEmpty,"22"),
		"24"=>array("24",$arrEmpty,"24"),
		"26"=>array("26",$arrEmpty,"26"),
		"28"=>array("28",$arrEmpty,"28"),
		*/
		"25"=>array("25",$arrEmpty,"25"),
		"30"=>array("30",$arrEmpty,"30"),
		"35"=>array("35",$arrEmpty,"35"),
		"40"=>array("40",$arrEmpty,"40"),
		"45"=>array("45",$arrEmpty,"45"),
		"50"=>array("50",$arrEmpty,"50"),
		"55"=>array("55",$arrEmpty,"55"),
		"60"=>array("60",$arrEmpty,"60"),
		"65"=>array("65",$arrEmpty,"65"),
		"70"=>array("70",$arrEmpty,"70"),
		"75"=>array("75",$arrEmpty,"75"),
		"80"=>array("80",$arrEmpty,"80"),
		"90"=>array("90",$arrEmpty,"90")
		);

		//--------------------------GRID MENU OPTIONS --------

		//--------------------------HT MENU OPTIONS --------

		$arr_ht_ESO_options_dis = array(
		"E"=>array("E",$arrEmpty,"E"),
		"E(T)"=>array("E(T)",$arrEmpty,"E(T)"),
		"ET"=>array("ET",$arrEmpty,"ET"),
		"ORTHO"=>array("ORTHO",$arrEmpty,"ORTHO"),
		"X"=>array("X",$arrEmpty,"X"),
		"X(T)"=>array("X(T)",$arrEmpty,"X(T)"),
		"XT"=>array("XT",$arrEmpty,"XT"),
		"DHD OD"=>array("DHD OD",$arrEmpty,"DHD OD"),
		"DHD OS"=>array("DHD OS",$arrEmpty,"DHD OS"),
		"DHD OU"=>array("DHD OU",$arrEmpty,"DHD OU"),
		"ME"=>array("ME",$arrEmpty,"ME")
		);

		$arr_ht_ESO_options_near = array(
		"E'"=>array("E'",$arrEmpty,"E'"),
		"E(T)'"=>array("E(T)'",$arrEmpty,"E(T)'"),
		"ET'"=>array("ET'",$arrEmpty,"ET'"),
		"ORTHO'"=>array("ORTHO'",$arrEmpty,"ORTHO'"),
		"X'"=>array("X'",$arrEmpty,"X'"),
		"X(T)'"=>array("X(T)'",$arrEmpty,"X(T)'"),
		"XT'"=>array("XT'",$arrEmpty,"XT'"),
		"DHD OD'"=>array("DHD OD'",$arrEmpty,"DHD OD'"),
		"DHD OS'"=>array("DHD OS'",$arrEmpty,"DHD OS'"),
		"DHD OU'"=>array("DHD OU'",$arrEmpty,"DHD OU'"),
		"ME'"=>array("ME'",$arrEmpty,"ME'")
		);

		$arr_ht_Hyper_options_dis = array(
		"RH"=>array("RH",$arrEmpty,"RH"),
		"RH(T)"=>array("RH(T)",$arrEmpty,"RH(T)"),
		"RHT"=>array("RHT",$arrEmpty,"RHT"),
		"R hypo"=>array("R hypo",$arrEmpty,"R hypo"),
		"R hypo(T)"=>array("R hypo(T)",$arrEmpty,"R hypo(T)"),
		"R hypoT"=>array("R hypoT",$arrEmpty,"R hypoT"),
		"RDVD"=>array("RDVD",$arrEmpty,"RDVD"),
		"ORTHO"=>array("ORTHO",$arrEmpty,"ORTHO"),
		"LH"=>array("LH",$arrEmpty,"LH"),
		"LH(T)"=>array("LH(T)",$arrEmpty,"LH(T)"),
		"LHT"=>array("LHT",$arrEmpty,"LHT"),
		"L hypo"=>array("L hypo",$arrEmpty,"L hypo"),
		"L hypo(T)"=>array("L hypo(T)",$arrEmpty,"L hypo(T)"),
		"L hypoT"=>array("L hypoT",$arrEmpty,"L hypoT"),
		"LDVD"=>array("LDVD",$arrEmpty,"LDVD")
		);

		$arr_ht_Hyper_options_near = array(
		"RH'"=>array("RH'",$arrEmpty,"RH'"),
		"RH(T)'"=>array("RH(T)'",$arrEmpty,"RH(T)'"),
		"RHT'"=>array("RHT'",$arrEmpty,"RHT'"),
		"R hypo'"=>array("R hypo'",$arrEmpty,"R hypo'"),
		"R hypo(T)'"=>array("R hypo(T)'",$arrEmpty,"R hypo(T)'"),
		"R hypoT'"=>array("R hypoT'",$arrEmpty,"R hypoT'"),
		"RDVD'"=>array("RDVD'",$arrEmpty,"RDVD'"),
		"ORTHO'"=>array("ORTHO'",$arrEmpty,"ORTHO'"),
		"LH'"=>array("LH'",$arrEmpty,"LH'"),
		"LH(T)'"=>array("LH(T)'",$arrEmpty,"LH(T)'"),
		"LHT'"=>array("LHT'",$arrEmpty,"LHT'"),
		"L hypo'"=>array("L hypo'",$arrEmpty,"L hypo'"),
		"L hypo(T)'"=>array("L hypo(T)'",$arrEmpty,"L hypo(T)'"),
		"L hypoT'"=>array("L hypoT'",$arrEmpty,"L hypoT'"),
		"LDVD'"=>array("LDVD'",$arrEmpty,"LDVD'")
		);

		//--------------------------HT MENU OPTIONS --------

		//-------------------------Duction Optios -------------

		$arr_duction_options=array(
			"4+"=>array("4+",$arrEmpty,"4+"),
			"3+"=>array("3+",$arrEmpty,"3+"),
			"2+"=>array("2+",$arrEmpty,"2+"),
			"1+"=>array("1+",$arrEmpty,"1+"),
			"0"=>array("0",$arrEmpty,"0"),
			"1-"=>array("1-",$arrEmpty,"1-"),
			"2-"=>array("2-",$arrEmpty,"2-"),
			"3-"=>array("3-",$arrEmpty,"3-"),
			"4-"=>array("4-",$arrEmpty,"4-"));

		//-------------------------Duction Optios -------------

		//------------------------ Worth 4 Dot -----------------

		$arr_w4dotOptions=array(
			"Fuses"=>array("Fuses",$arrEmpty,"Fuses"),
			"2 red"=>array("2 red",$arrEmpty,"2 red"),
			"3 green"=>array("3 green",$arrEmpty,"3 green"),
			"5 Lights"=>array("5 Lights",$arrEmpty,"5 Lights")
		);

		//------------------------ Worth 4 Dot -----------------

		// -----------------------  APCt --

		$arr_apct_options = array(
			"APCT"=>array("APCT",$arrEmpty,"APCT"),
			"PCT"=>array("PCT",$arrEmpty,"PCT"),
			"HIRSCHBERG"=>array("HIRSCHBERG",$arrEmpty,"HIRSCHBERG"),
			"KRIMSKY"=>array("KRIMSKY",$arrEmpty,"KRIMSKY"),
			"MADDOX"=>array("MADDOX",$arrEmpty,"MADDOX"),
			"MOD THOR"=>array("MOD THOR",$arrEmpty,"MOD THOR")
		);

		// -----------------------  APCt --
		return array("grid_options_other"=>$arr_grid_options_other,
				  "grid_options_num"=>$arr_grid_options_num,
				  "ht_ESO_options_dis"=>$arr_ht_ESO_options_dis,
				  "ht_ESO_options_near"=>$arr_ht_ESO_options_near,
				  "ht_Hyper_options_dis"=>$arr_ht_Hyper_options_dis,
				  "ht_Hyper_options_near"=>$arr_ht_Hyper_options_near,
				  "duction_options"=>$arr_duction_options,
				  "w4dotOptions"=>$arr_w4dotOptions,
				  "apct_options"=>$arr_apct_options
				);

	}

	function getStreopsisInfo($elem_dos=""){
		$patient_id = $this->pid;
		$elem_formId = $this->fid;

		// ------------- Streopsis ---------------------
		$elem_utElems="";
		$sql = "SELECT * FROM chart_steropsis WHERE form_id= '".$elem_formId."' AND patient_id='".$patient_id."' AND purged='0' ";
		$row = sqlQuery($sql);
		if($row==false){
			//GET PAST values
			if(empty($elem_dos)){
				//DOS
				$elem_dos=$this->getDos(1);
			}
			//Obj
			$oCN = new ChartNote($patient_id,$elem_formId);
			$res = $oCN->getLastRecord("chart_steropsis","form_id",0," * ",$elem_dos); //
			if($res!=false){$row=$res;}else{$row=false;}
			$elem_edit_st=0;
		}else{
			$elem_edit_st=1;
		}

		if($row!=false){
			if(!empty($row["seconds_of_arc"])){
				$elem_stereo_SecondsArc=$row["seconds_of_arc"];
				//append UT ELEM Streopsis to EOM
				if($elem_edit_st==1){$elem_utElems=$elem_utElems."".$row["ut_elem"];}
			}
		}

		// ------------- Streopsis ---------------------
		$ar=array();
		$ar["elem_utElems_streopsis"] = $elem_utElems;
		$ar["elem_edit_st"] = $elem_edit_st;
		$ar["elem_stereo_SecondsArc"] = $elem_stereo_SecondsArc;
		return $ar;
	}

	function getColorVisionInfo($elem_dos=""){
		$patient_id = $this->pid;
		$elem_formId = $this->fid;

		// COLOR Vision test ------------------------
		$ar=array();
		$elem_utElems="";
		$sql = "SELECT * FROM chart_icp_color WHERE form_id= '".$elem_formId."' AND patient_id='".$patient_id."' AND purged='0' ";
		$row = sqlQuery($sql);
		if($row==false){
			//GET PAST values
			if(empty($elem_dos)){
				$elem_dos=$this->getDos(1);
			}
			//Obj
			$oCN = new ChartNote($patient_id,$elem_formId);
			$res = $oCN->getLastRecord("chart_icp_color","form_id",0," * ",$elem_dos); //
			if($res!=false){$row=$res;}else{$row=false;}
			$elem_edit_icp=0;
		}else{
			$elem_edit_icp=1;
		}

		if($row!=false){
			//if(!empty($row["seconds_of_arc"])){
				$elem_color_sign_od=$row["control_od"];
				$elem_color_od_1 = $row["cval1_od"];
				$elem_color_od_2 = $row["cval2_od"];

				$elem_color_sign_os=$row["control_os"];
				$elem_color_os_1 = $row["cval1_os"];
				$elem_color_os_2 = $row["cval2_os"];

				$elem_color_sign_ou=$row["control_ou"];
				$elem_color_ou_1 = $row["cval1_ou"];
				$elem_color_ou_2 = $row["cval2_ou"];

				$elem_comm_colorVis = $row["icp_desc"];

				//append UT ELEM Streopsis to EOM
				if($elem_edit_icp==1){$elem_utElems=$elem_utElems."".$row["ut_elem"];} //Need To WORK
			//}
		}

		// COLOR Vision test ------------------------

		$ar["elem_color_sign_od"]=$elem_color_sign_od;
		$ar["elem_color_od_1"]=$elem_color_od_1;
		$ar["elem_color_od_2"]=$elem_color_od_2;
		$ar["elem_color_sign_os"]=$elem_color_sign_os;
		$ar["elem_color_os_1"]=$elem_color_os_1;
		$ar["elem_color_os_2"]=$elem_color_os_2;
		$ar["elem_color_sign_ou"]=$elem_color_sign_ou;
		$ar["elem_color_ou_1"]=$elem_color_ou_1;
		$ar["elem_color_ou_2"]=$elem_color_ou_2;
		$ar["elem_comm_colorVis"]=$elem_comm_colorVis;
		$ar["elem_edit_icp"]=$elem_edit_icp;
		$ar["elem_utElems_icp"]=$elem_utElems;

		return $ar;
	}

	function getW4DotInfo($elem_dos=""){
		$patient_id = $this->pid;
		$elem_formId = $this->fid;
		$ar=array();
		//W4Dot-------------------------------------

		$elem_utElems="";
		$sql = "SELECT * FROM chart_w4dot  WHERE form_id= '".$elem_formId."' AND patient_id='".$patient_id."' AND purged='0' ";
		$row = sqlQuery($sql);
		if($row==false){
			//GET PAST values
			//Obj
			$oCN = new ChartNote($patient_id,$elem_formId);
			$res = $oCN->getLastRecord("chart_w4dot","form_id",0," * ",$elem_dos); //
			if($res!=false){$row=$res;}else{$row=false;}
			$elem_edit_w4d=0;
		}else{
			$elem_edit_w4d=1;
		}

		if($row!=false){
			//if(!empty($row["seconds_of_arc"])){
				$elem_w4dot_distance=$row["distance"];
				$elem_w4dot_near = $row["near"];
				$elem_comm_w4Dot = $row["desc_w4dot"];

				//append UT ELEM Streopsis to EOM
				if($elem_edit_w4d==1){$elem_utElems=$elem_utElems."".$row["ut_elem"];} //Need To WORK
			//}
		}

		//W4Dot-------------------------------------

		$ar["elem_w4dot_distance"] = $elem_w4dot_distance;
		$ar["elem_w4dot_near"] = $elem_w4dot_near;
		$ar["elem_comm_w4Dot"] = $elem_comm_w4Dot;
		$ar["elem_utElems_w4d"] = $elem_utElems;
		$ar["elem_edit_w4d"] = $elem_edit_w4d;

		return $ar;

	}

	// Head Tilt
	function makeHTGrid($htNum,$arrVals,$label){

		//Arr Menus
		$ar_menu_eom = $this->menu_arr_options();
		$arr_grid_options_other = $ar_menu_eom["grid_options_other"];
		$arr_grid_options_num = $ar_menu_eom["grid_options_num"];
		$arr_ht_ESO_options_dis = $ar_menu_eom["ht_ESO_options_dis"];
		$arr_ht_ESO_options_near = $ar_menu_eom["ht_ESO_options_near"];
		$arr_ht_Hyper_options_dis = $ar_menu_eom["ht_Hyper_options_dis"];
		$arr_ht_Hyper_options_near = $ar_menu_eom["ht_Hyper_options_near"];
		$arr_apct_options = $ar_menu_eom["apct_options"];

		//$arr_ht_type=array("Right","Left");
		$counterHT = $htNum;
		$coords="-1,-1,-1,25";
		$coords_sm="-1,-1,-1,25";

		//$grid = "<div id=\"HT_".$counterHT."\" class=\"htrow\" >";
		$grid = "";
		//foreach($arr_ht_type as $key=>$val){

		$labelHT=($label=="Distance") ? "Right" : "Left" ;
		$elem_ht = "elem_ht_".$labelHT."_".$counterHT."[]";
		$elem_comm = "elem_comm_".$labelHT."_".$counterHT."";
		$indx=0;

		$grid .=	"<div class=\"headtilt\">
				<div class=\"row\">
				<table class=\"table table-responsive\">
				<tr>
				<td>
				<h3>".$labelHT." Head Tilt</h3>
				</td>";

				//$tmpMenuAdd = ($labelHT=="Right") ? "Distance" : "Near" ;
				$tmpMenuAdd = "Distance";

				$grid .="<td class=\"form-inline\">";

				for($inp=1;$inp<=4;$inp++){
					$elem_ht_id = "elem_ht_".$labelHT."_".$counterHT."_".$indx;
					$elem_ht_val=$arrVals[$labelHT][$indx]; //Need Work HERE
					$grid .= "<input type=\"text\" id=\"".$elem_ht_id."\" name=\"".$elem_ht."\" value=\"".$elem_ht_val."\" onblur=\"checkYFlagP(this);checkFunction(this);\" class=\"form-control\">";
					$grid .= "<div class=\"input-group\">";
					if($inp==2){
						$arr_ht_ESO_options = ($labelHT=="Right") ? $arr_ht_ESO_options_dis : $arr_ht_ESO_options_near;
						//$grid .= getSimpleMenu($arr_ht_ESO_options,"menu_grids_eso_".$tmpMenuAdd,$elem_ht_id,0,0,array("coords"=>$coords_sm,"pdiv"=>"divconExam"));
						$grid .= wv_get_simple_menu($arr_ht_ESO_options,"menu_grids_eso_".$tmpMenuAdd,$elem_ht_id);
					}else if($inp==4){
						$arr_ht_options = ($labelHT=="Right") ? $arr_ht_Hyper_options_dis : $arr_ht_Hyper_options_near;
						//$grid .= getSimpleMenu($arr_ht_options,"menu_grids_hyper_".$tmpMenuAdd,$elem_ht_id,0,0,array("coords"=>$coords_sm,"pdiv"=>"divconExam"));
						$grid .= wv_get_simple_menu($arr_ht_options,"menu_grids_hyper_".$tmpMenuAdd,$elem_ht_id);
					}else{
						//$grid .= getSimpleMenu($arr_grid_options_num,"menu_grids_num",$elem_ht_id,0,0,array("coords"=>$coords,"pdiv"=>"divconExam"));
						$grid .= wv_get_simple_menu($arr_grid_options_num,"menu_grids_num",$elem_ht_id);
					}
					$grid .="</div>";
					if($inp==2){ $grid .= "<br/>"; }
					$indx++; //Increment
				}

				$grid .="</td>";
				$grid .="</tr></table>";
				$grid .="</div>";

		$elem_comm_val=$arrVals[$labelHT]["comments"];
		$grid .=   "<div class=\"examcomt\"><textarea name=\"".$elem_comm."\" class=\"form-control\" >".$elem_comm_val."</textarea></div>";
		$grid .=   "</div>";
		//}

		return $grid;
	}


	function makeDisNearGrid($gridNum,$arrVals,$arrVals2,$wnl){
		global $flgGridBtnAdd;

		//Arr Menus
		$ar_menu_eom = $this->menu_arr_options();
		$arr_grid_options_other = $ar_menu_eom["grid_options_other"];
		$arr_grid_options_num = $ar_menu_eom["grid_options_num"];
		$arr_ht_ESO_options_dis = $ar_menu_eom["ht_ESO_options_dis"];
		$arr_ht_ESO_options_near = $ar_menu_eom["ht_ESO_options_near"];
		$arr_ht_Hyper_options_dis = $ar_menu_eom["ht_Hyper_options_dis"];
		$arr_ht_Hyper_options_near = $ar_menu_eom["ht_Hyper_options_near"];
		$arr_apct_options = $ar_menu_eom["apct_options"];

		$arr_grid_type=array("Distance","Near");
		$counterGrid = $gridNum;
		$coords="-1,-1,-1,25";

		$grid = "<div id=\"Grid_".$counterGrid."\" class=\"row gridrow\" >";

		foreach($arr_grid_type as $key=>$val){

		$labelGrid=$val;
		$elem_gf = "elem_gf_".$labelGrid."_".$counterGrid."[]";
		$elem_comm = "elem_comm_".$labelGrid."_".$counterGrid."";
		$elem_apct = "elem_apct_".$labelGrid."_".$counterGrid."";
		$elem_apct_val=$arrVals[$labelGrid]["APCT"];

		$elem_sc = "elem_sc_".$labelGrid."_".$counterGrid."";
		$elem_sc_chckd=!empty($arrVals[$labelGrid]["SC"]) ? "checked" : "";

		$elem_cc = "elem_cc_".$labelGrid."_".$counterGrid."";
		$elem_cc_chckd=!empty($arrVals[$labelGrid]["CC"]) ? "checked" : "";

		$elem_ccprisms = "elem_ccprisms_".$labelGrid."_".$counterGrid."";
		$elem_ccprisms_chckd=!empty($arrVals[$labelGrid]["CCPrisms"]) ? "checked" : "";

		$elem_bifocal = "elem_bifocal_".$labelGrid."_".$counterGrid."";
		$elem_bifocal_chckd=!empty($arrVals[$labelGrid]["Bifocal"]) ? "checked" : "";

		//Default is APCT
		if((!is_array($arrVals) || count($arrVals)==0) && empty($elem_apct_val) && empty($wnl)){ $elem_apct_val="APCT"; }

		//
		//Add buttons
		$add_btn="";
		if($labelGrid=="Near"){
			if(!isset($flgGridBtnAdd)){
				$flgGridBtnAdd = 1;
				$add_btn ="<input type=\"button\" name=\"btn_addgrid\" value=\"Add\"  class=\"dff_button_sm btn btn-sm btn-success\" onclick=\"addGrid()\">";
			}else{
				$add_btn ="<input type=\"button\" name=\"btn_delgrid\" value=\"Remove\"  class=\"dff_button_sm btn btn-sm btn-danger\" onclick=\"delGrid('".$counterGrid."')\">";
			}
		}//

		$indx=0;
		$grid .=	"<div class=\"col-sm-6\">
					<div class=\"exambox\">
					<div class=\"grid\">
						<div class=\"head\">
							<div class=\"row\">
								<div class=\"col-md-1\" ><h2>".$labelGrid."</h2></div>
								<div class=\"col-md-11 form-inline text-right\">
										<input type=\"button\" name=\"btn_rst_".$key."\" value=\"Set To Ortho\" class=\"dff_button_sm exambut\"  onclick=\"setGridVal('".$counterGrid."',this)\">
										<div class=\"input-group\">
										<input type=\"text\" id=\"".$elem_apct."\" name=\"".$elem_apct."\" value=\"".$elem_apct_val."\" class=\"apct form-control\" onblur=\"checkYFlagP(this);\" >";

		$grid .= wv_get_simple_menu($arr_apct_options,"menu_apct",$elem_apct);
		$grid .=							"</div>";

		$grid .=							"<input type=\"checkbox\" id=\"".$elem_sc."\" name=\"".$elem_sc."\" value=\"1\" ".$elem_sc_chckd." onclick=\"checkYFlagP(this);\" ><label for=\"".$elem_sc."\">SC</label>
										<input type=\"checkbox\" id=\"".$elem_cc."\" name=\"".$elem_cc."\" value=\"1\" ".$elem_cc_chckd." onclick=\"checkYFlagP(this);\" ><label for=\"".$elem_cc."\">CC</label>
										<input type=\"checkbox\" id=\"".$elem_ccprisms."\" name=\"".$elem_ccprisms."\" value=\"1\" ".$elem_ccprisms_chckd." onclick=\"checkYFlagP(this);\" ><label for=\"".$elem_ccprisms."\">CC Prisms</label> ";

		if($labelGrid=="Near"){
		$grid .=							"<input type=\"checkbox\" id=\"".$elem_bifocal."\" name=\"".$elem_bifocal."\" value=\"1\" ".$elem_bifocal_chckd." onclick=\"checkYFlagP(this);\" ><label for=\"".$elem_bifocal."\">Bifocal</label> ";
		}

		$grid .=							"<input type=\"button\" name=\"btn_comi_".$key."\" value=\"Comitant\"  class=\"dff_button_sm exambut\" onclick=\"setGridVal('".$counterGrid."',this)\">
										".$add_btn."
								</div>
							</div>
						</div>
						<div class=\"clearfix\"></div>
						<div class=\"pd10\">
							<div class=\"row\">";

		$grid .=	"<table class=\"table table-responsive\">";
		for($tr=1;$tr<=3;$tr++){

		if($tr==3){$csstr="class=\"lasttr\"";}else{$csstr="";}

		$grid .=	"<tr ".$csstr." >";

			for($td=1;$td<=3;$td++){

				if($td==3){$css="class=\"lasttd form-inline\"";}else{$css="class=\"form-inline\"";}

				$grid .="<td  ".$css." >";
					for($inp=1;$inp<=4;$inp++){
						$elem_gf_id = "elem_gf_".$labelGrid."_".$counterGrid."_".$indx;
						$elem_gf_val=$arrVals[$labelGrid][$indx]; //Need Work HERE

						$grid .= "<div class=\"input-group\">";
						$grid .= "<input type=\"text\" id=\"".$elem_gf_id."\" name=\"".$elem_gf."\" value=\"".$elem_gf_val."\" onblur=\"checkYFlagP(this);checkFunction(this);\" class=\"form-control\" aria-label=\"...\" >";


						if($inp==2){
							$arr_grid_options = ($labelGrid=="Distance") ? $arr_ht_ESO_options_dis : $arr_ht_ESO_options_near;
							//$grid .= getSimpleMenu($arr_grid_options,"menu_grids_eso_".$labelGrid,$elem_gf_id,0,0,array("coords"=>$coords,"pdiv"=>"divconExam"));
							$grid .= wv_get_simple_menu($arr_grid_options,"menu_grids_eso_".$labelGrid,$elem_gf_id);
						}else if($inp==4){
							$arr_grid_options = ($labelGrid=="Distance") ? $arr_ht_Hyper_options_dis : $arr_ht_Hyper_options_near;
							//$grid .= getSimpleMenu($arr_grid_options,"menu_grids_hyper_".$labelGrid,$elem_gf_id,0,0,array("coords"=>$coords,"pdiv"=>"divconExam"));
							$grid .= wv_get_simple_menu($arr_grid_options,"menu_grids_hyper_".$labelGrid,$elem_gf_id);
						}else{
							//$grid .= getSimpleMenu($arr_grid_options_num,"menu_grids_num",$elem_gf_id,0,0,array("coords"=>$coords,"pdiv"=>"divconExam"));
							$grid .= wv_get_simple_menu($arr_grid_options_num,"menu_grids_num",$elem_gf_id);
						}

						$grid .= "</div>";

						if($inp==2){ $grid .= "<br/>"; }
						$indx++; //Increment
					}
				$grid .="</td>";

			}

		$grid .=	"</tr>";

		}

		$grid .=	"</table>";


		$grid .= 			"</div>";//row

		$elem_comm_val=$arrVals[$labelGrid]["comments"];
		$grid .=   "<div class=\"examcomt\"><textarea name=\"".$elem_comm."\" onblur=\"checkYFlagP(this);\" class=\"form-control\" >".$elem_comm_val."</textarea></div>
				<div class=\"clearfix\"></div>";
		$grid .=   "	</div>";//pd
		$grid .=   "	</div>";//grid

		//<!-- Head Tilt -->

		$grid .= $this->makeHTGrid($counterGrid,$arrVals2,$labelGrid);

		//<!-- Head Tilt -->

		$grid .=   "	</div>
				</div>";//col-sm-6, exambox
		}

		$grid .= "</div>"; //gridrow

		//echo "<xmp>".$grid."</xmp>";

		return $grid;

	}

	function makeDuctions($ducNum,$arrVals){

		//Arr Menus
		$ar_menu_eom = $this->menu_arr_options();
		$arr_duction_options = $ar_menu_eom["duction_options"];

		$arrValsDuction=$arrVals;

		$cross="
		<div class=\"exambox\" >
		<div class=\"head\">
			<div class=\"row\">
			<div class=\"col-sm-1\">
			<h2>Ductions</h2>
			</div>
			<div class=\"col-sm-5 text-center\">
			<h2>Right</h2>
			</div>
			<div class=\"col-sm-6 text-center\">
			<h2>Left</h2>
			</div>
			</div>
		</div>
		<div class=\"clearfix\"></div>
		";

		$cross.="<div class=\"pd10\">
				<div class=\"row\">
				";

		$duction_types = array("1","2");
		$coords="-1,-1,-1,25";
		$duction_label = array("1"=>array("RSO","DOWNGAZE","RIR","RMR","RLR","RIO","UPGAZE","RSR"),
						"2"=>array("LIR","DOWNGAZE","LSO","LLR","LMR","LSR","UPGAZE","LIO"));

		foreach($duction_types as $key=>$val){

		$duc_type = $val;

		$cross.="<div class=\"col-sm-6\">";
		$cross.="<table class=\"table table-responsive\"  >";
		$indx=0;

		for($i=0;$i<3;$i++){

		$cross.="	<tr>";
			for($j=0;$j<5;$j++){

				$flgShow=0;
				$colspn=$style="";
				if(($i!=1&&$j!=0&&$j!=4)||($i==1&&($j==0||$j==4))){
					$flgShow=1;
				}else{

					if($i==1&&($j!=0||$j!=4)){

						if($j==1){
							$colspn=" colspan=\"3\" ";
							$style = " duction_cross ";
						}else{
							//continue;
						}

					}

				}

				$style_td = "class=\"text-center\" ";

				$cross.="<td ".$colspn." ".$style_td." >";

					if(!empty($style)){
						$cross.="<img class=\"img-responsive center-block\" src=\"".$GLOBALS['webroot']."/library/images/duction_cross.png\" alt=\"duction\">";
					}

					if($flgShow==1){

						if($i==2){
							$tmp = array_pop($duction_label[$duc_type]);
							$cross.="<label>".$tmp."</label><br/>";
						}

						$elem_ductions = "elem_duction_".$ducNum."_".$duc_type."[]";
						$elem_ductions_id = "elem_duction_".$ducNum."_".$duc_type."_".($indx+1);

						$cross .= "<div class=\"input-group\">";
						$cross.="<input type=\"text\" id=\"".$elem_ductions_id."\" name=\"".$elem_ductions."\" value=\"".$arrValsDuction[$duc_type][$indx]."\" onclick=\"checkYFlagP(this);\" class=\"form-control\">";
						//$cross.= getSimpleMenu($arr_duction_options,"menu_ductions",$elem_ductions_id,0,0,array("coords"=>$coords,"pdiv"=>"divconExam"));
						$cross .= wv_get_simple_menu($arr_duction_options,"menu_ductions",$elem_ductions_id);
						$cross .= "</div>";

						if($i==0||$i==1){
							$cross.= "";
							$tmp = array_pop($duction_label[$duc_type]);
							$cross.="<label>".$tmp."</label>";
						}

						$indx++;

					}


				$cross.="</td>";

			}

		$cross.="	</tr>";
		}

		$elem_comments_duc=$arrValsDuction[$duc_type]["comments"];
		$cross.="	<tr>	".
			"<td colspan=\"5\">".
				"<textarea onBlur=\"checkYFlagP(this);\" name=\"elem_comments_duc_".$ducNum."_".$duc_type."\" class=\"form-control\" >".$elem_comments_duc."</textarea>".
			"</td>".
			"</tr>";

		$cross.="</table>";
		$cross.="</div>";
		}
		$cross.="</div>";
		$cross.="</div>";
		$cross.="</div>";
		return $cross;

	}

	function load_exam($finalize_flag = 0){

		$oWv = new WorkView();
		$patient_id = $this->pid;
		$elem_formId=$form_id=$this->fid;

		$OBJDrawingData = new CLSDrawingData();
		$blEnableHTMLDrawing = $OBJDrawingData->getHTMLDrawingDispStatus();
		$drawCntlNum=2; //This setting will decide number of drawing instances
		list($idoc_arrDrwIcon, $idoc_htmlDrwIco) = $OBJDrawingData->drwico_getDrwIcon();


		//Is Reviewable
		$isReviewable = $this->isChartReviewable($_SESSION["authId"]);

		//
		$elem_per_vo =  ChartPtLock::is_viewonly_permission();
		$ProClr = User::getProviderColors();//
		//$logged_user_type = $_SESSION["logged_user_type"];
		$logged_user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];

		$arrTabs = array("Eom"=>"EOM","Eom3"=>"Drawing"); //Tabs
		$elem_editMode="0";
		$elem_eomId="";
		$elem_patientId=$patient_id;
		$isPositive="0";
		$elem_examDate=date("Y-m-d H:i:s");
		$wnl=0;
		$myflag=false;
		$examined_no_change=0;
		$blDrwaingGray = false;

		//Arr Menus
		$ar_menu_eom = $this->menu_arr_options();
		$arr_grid_options_other = $ar_menu_eom["grid_options_other"];
		$arr_grid_options_num = $ar_menu_eom["grid_options_num"];
		$arr_ht_ESO_options_dis = $ar_menu_eom["ht_ESO_options_dis"];
		$arr_ht_ESO_options_near = $ar_menu_eom["ht_ESO_options_near"];
		$arr_ht_Hyper_options_dis = $ar_menu_eom["ht_Hyper_options_dis"];
		$arr_ht_Hyper_options_near = $ar_menu_eom["ht_Hyper_options_near"];
		$arr_duction_options = $ar_menu_eom["duction_options"];
		$arr_w4dotOptions = $ar_menu_eom["w4dotOptions"];
		$arr_apct_options = $ar_menu_eom["apct_options"];

		//DOS
		$elem_dos=$this->getDos(1);

		// Extract Values
		$sql = "SELECT * FROM chart_eom  WHERE form_Id = '".$elem_formId."' AND patient_id='".$patient_id."' AND purged='0'  ";
		$row = sqlQuery($sql);

		if($row==false){
			$res = $this->getLastRecord(" * ",0,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$blDrwaingGray = true;
			$myflag=true;
		}

		if(isset($_GET["prevVal"]) && ($_GET["prevVal"] == 1)){
			$res = $this->getLastRecord(" * ",1,$elem_dos);
			if($res!=false){$row=$res;}else{$row=false;}
			$myflag=true;
			$blDrwaingGray = true;
		}

		//if(@mysql_num_rows($res)>0){
		if($row!=false){
			if($myflag){
				$elem_editMode=0;
			}else{
				$elem_editMode=1;
			}
			$strCanvasWNL = "yes";
			//$row_eom=mysql_fetch_array($res);
			extract($row);
			$elem_eomId_LF = $eom_id;
			$elem_descEom = $descEom;
			$elem_rightHeadTilt_Dis = $rightHdTiltDis;
			$elem_leftHeadTilt_Dis = $leftHdTiltDis;
			$elem_commentsDis = $commDis;
			$elem_eomDrawingNear = $eomDrwNear;
			$elem_rightHeadTilt_Near = $rightHdTiltNear;
			$elem_leftHeadTilt_Near = $leftHdTiltNear;
			$elem_commentsNear = $commNear;
			$elem_eomControl=$eomControl;
			$elem_eomDrawing_Dis2 = $eomDrawing_Dis2;
			$elem_eomDrawingNear_2 = $eomDrwNear_2;

			$elem_commentsDis_1 = $commDis_1;
			$elem_commentsNear_1 = $commNear_1;

			$dbIdocDrawingId = $idoc_drawing_id;
			if($dbIdocDrawingId != ""){
				$arrDrwaingData = array();
				$arrDrwaingData = $OBJDrawingData->getHTMLDrawingData($dbIdocDrawingId, 1);
				//pre($arrDrwaingData,1);
				//$dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $imgDB = "";
				//list($dbTollImage, $dbPatTestName, $dbPatTestId, $dbTestImg, $imgDB) = $OBJDrawingData->getHTMLDrawingData($dbIdocDrawingId, 1);
			}
			/*if($dbIdocDrawingId > 0){
				$dbRedPixel = $dbGreenPixel = $dbBluePixel = $dbAlphaPixel = $dbTollImage = $dbPatTestName = $dbPatTestId = $dbTestImg = $canvasDataFileNameDB = $dbCanvasImageDataPoint = "";
				list($dbRedPixel, $dbGreenPixel, $dbBluePixel, $dbAlphaPixel, $dbTollImage, $dbPatTestName, $dbPatTestId, $dbTestImg, $canvasDataFileNameDB, $dbCanvasImageDataPoint, $imgDB) = $OBJDrawingData->getHTMLDrawingData($dbIdocDrawingId);
				if((empty($dbRedPixel) == false) && (empty($dbGreenPixel) == false) && (empty($dbBluePixel) == false) && (empty($dbAlphaPixel) == false)){
					$strCanvasWNL = "no";
				}
			}*/
			//UT Elems
			$elem_utElems = $ut_elem;
			$elem_gridvals = $gridvals;
			$elem_htvals = $htvals;

			$elem_ductionvals = $ductionvals;
			$elem_comments_AnoHead = $commAnoHead;
			$elem_comments_Nystag = $commNystag;

			$elem_npaWnlAbn = $npa;
			$elem_npaCm = $npa_cm;
			$npa_desc = $npa_desc;

			$elem_eomAvpAesoAexo = $avpat_eso;
			$elem_eomAvpVesoVexo = $avpat_exo;

			$elem_ranSt = $randot_st;
			$elem_ranSt_Dots9 = $randot_dots;
			$elem_ahp_no = $ahp_no;
			$elem_nysta_no = $nysta_no;
			$elem_comments_gen=$comments_gen;

		}else{
			//default values
			//$npc_wnl_abn="WNL";

			/* SIMI: 12-03-2013
			$eom_full=1;
			$eom_ortho=1;
			*/

		}

		if($elem_editMode==0){
			$examined_no_change=0;
			$examined_no_change2=0;
			$examined_no_change3=0;
			$statusElem="";
			$elem_utElems = "";
		}

		//Streopsis
		$ar_streopsis = $this->getStreopsisInfo($elem_dos);
		extract($ar_streopsis);
		if($elem_edit_st==1&&!empty($elem_utElems_streopsis)){$elem_utElems=$elem_utElems."|".$elem_utElems_streopsis;}

		// COLOR Vision test
		$ar_color_vision = $this->getColorVisionInfo($elem_dos);
		extract($ar_color_vision);
		if($elem_edit_icp==1&&!empty($elem_utElems_icp)){$elem_utElems=$elem_utElems."|".$elem_utElems_icp;}

		//W4Dot
		$ar_w4dot = $this->getW4DotInfo($elem_dos);
		extract($ar_w4dot);
		if($elem_edit_w4d==1&&!empty($elem_utElems_w4d)){$elem_utElems=$elem_utElems."|".$elem_utElems_w4d;}

		// Change Indicate Elements -----------------
		$elem_changeInd = "";
		for($i=1;$i<=3;$i++){
			$tmp = ($i==1) ? "" : $i;
			$nmIdOd = "elem_chng_divEom".$tmp."";
			$$nmIdOd = (!empty($statusElem) && strpos($statusElem,$nmIdOd."=1")!==false) ? "1" : "0";
			$elem_changeInd .= "<input type=\"hidden\" name=\"".$nmIdOd."\" id=\"".$nmIdOd."\" value=\"".$$nmIdOd."\">";
		}
		// Change Indicate Elements -----------------

		//grid--
		$data_grid="";
		$max_key_gv =0;
		$arrGridVals=array();
		if(!empty($elem_gridvals)){
			$arrGridVals= unserialize($elem_gridvals);
			if(is_array($arrGridVals) && count($arrGridVals) > 0){
			$max_key_gv = max( array_keys( $arrGridVals ) );
			}
		}
		//
		$lenGridVals=count($arrGridVals);

		$arrHTVals=array();
		if(!empty($elem_htvals)){
			$arrHTVals = unserialize($elem_htvals);
			$max_key_htv = (is_array($arrHTVals) && count($arrHTVals) > 0) ? max( array_keys( $arrHTVals ) ) : 0 ;
			if($max_key_gv<$max_key_htv){ $max_key_gv = $max_key_htv; }
		}
		$lenHTVals=count($arrHTVals);
		if($lenGridVals<$lenHTVals){$lenGridVals=$lenHTVals;}

		$counterGrid = 0; $counterGrid_val = 0;
		$flgGrid=true;

		do{//test

		$counterGrid++;
		if($lenGridVals>0){

			if(isset($arrGridVals[$counterGrid])||isset($arrHTVals[$counterGrid])){
				$counterGrid_val++;
				$data_grid .= $this->makeDisNearGrid($counterGrid,$arrGridVals[$counterGrid],$arrHTVals[$counterGrid],$wnl);
				//stop
				if($counterGrid_val>=$lenGridVals){ $flgGrid=false;  }
			}

			//stop
			if($counterGrid>=$max_key_gv){ $flgGrid=false;  }

		}else{
			$data_grid .= $this->makeDisNearGrid($counterGrid,$arrGridVals[$counterGrid],$arrHTVals[$counterGrid],$wnl);
			//stop
			if($counterGrid>=$lenGridVals){ $flgGrid=false;  }
		}

		}while($flgGrid==true&&$counterGrid<=20);

		//if run once : for very old data --
		if($lenGridVals>0 && $counterGrid_val==0){
			if(isset($arrGridVals[0])){ $arrGridVals[1]=$arrGridVals[0];  } //Hack for very old data
			if(isset($arrHTVals[0])){ $arrHTVals[1]=$arrHTVals[0];  } //Hack for very old data
			$counterGrid=1;
			$data_grid .= $this->makeDisNearGrid($counterGrid,$arrGridVals[$counterGrid],$arrHTVals[$counterGrid],$wnl);
		}
		//--
		//------

		// w4dot
		$menu_w4dot_distance = wv_get_simple_menu($arr_w4dotOptions,"menu_w4dot","elem_w4dot_distance");
		$menu_w4dot_near = wv_get_simple_menu($arr_w4dotOptions,"menu_w4dot","elem_w4dot_near");

		//Duction
		$data_duction="";
		$arrDuctionVals=array("1"=>array("1"=>array(0,0,0,0,0,0,0,0),"2"=>array(0,0,0,0,0,0,0,0))); //Default
		if(!empty($elem_ductionvals)){
			$arrDuctionVals=unserialize($elem_ductionvals);
		}
		$data_duction=$this->makeDuctions("1",$arrDuctionVals[1]);

		//draw
		$intDrawingFormId = $elem_formId;
		$intDrawingExamId = $eom_id;
		$strScanUploadfor = "EOM_DSU";


		##
		header('Content-Type: text/html; charset=utf-8');
		##

		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/eom.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");

	}

}
?>
