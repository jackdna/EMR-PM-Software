<?php
	require_once('../../config/globals.php');
	extract($_REQUEST);
	//Check patient session and closing popup if no patient in session
	$window_popup_mode = true;
	require_once($GLOBALS['srcdir']."/patient_must_loaded.php");

	$library_path = $GLOBALS['webroot'].'/library';
	include_once $GLOBALS['srcdir']."/classes/common_function.php";
	include_once $GLOBALS['srcdir']."/classes/work_view/ChartGlucoma.php";
	include_once $GLOBALS['srcdir']."/classes/SaveFile.php";

	$pid = $_SESSION['patient'];
	$auth_id = $_SESSION['authId'];

	$glaucoma_obj = New ChartGlucoma($pid);

	//Get Gonio info
	if( $_GET["op"] == "get_gonio_inf" ){
		$glaucoma_obj->get_gonio_info();
		exit();
	}

	// Default Date of Activation
	$elem_dateActivation = get_date_format(date("Y-m-d"));
	$elem_activate = "-1";
	$elem_vfOdSummary = $elem_vfOsSummary = $elem_nfaOdSummary = $elem_nfaOsSummary = "Empty";

	/*// Date Activation
	if(isset($_POST["elem_dateActivation"]) && !empty($_POST["elem_dateActivation"])){
		$elem_dateActivation = $_POST["elem_dateActivation"];
	}
	*/
	//elem_activate
	if((isset($_POST["elem_activate"]) && !empty($_POST["elem_activate"]))){
		$elem_activate = $_POST["elem_activate"];
	}
	else if(isset($_GET["mode"]) && !empty($_GET["mode"])){
		$elem_activate = $_GET["mode"];
	}

	//osavefile
	$oSaveFile = new SaveFile($glaucoma_obj->pid);

	// get Initial Top
	$arrInitialTop = $glaucoma_obj->getIntialTop($glaucoma_obj->pid);
	$lenInitialTop = count($arrInitialTop);

	//if Past Glucoma Record exists
	$sql = "SELECT *
		  FROM glucoma_main
		  WHERE patientId = '".$glaucoma_obj->pid."'
		  AND activate = '1' ";

	$row = sqlQuery($sql); // get One Record
	if($row != false){
		// Record Exists
		$elem_dateActivation = get_date_format($row["dateActivation"],'mm-dd-yyyy');
		$elem_glucomaId = $row["glucomaId"];
		$elem_activate = $row["activate"];
		$elem_dateDiagnosis = get_date_format($row["dateDiagnosis"],'mm-dd-yyyy');
		$dxDescription = $row["diagnosis_description"];
		$elem_diagnosisOd = $row["diagnosisOd"];
		$elem_diagnosisOs = $row["diagnosisOs"];
		$elem_stagingCode_od = $row["staging_code_od"];
		$elem_stagingCode_os = $row["staging_code_os"];
		$elem_dateHighTaOd = get_date_format($row["dateHighTaOd"],'mm-dd-yyyy');
		$elem_highTaOdOd = $row["highTaOdOd"];
		$elem_highTaOdOs = $row["highTaOdOs"];
		$elem_dateHighTaOs = get_date_format($row["dateHighTaOs"],'mm-dd-yyyy');
		$elem_highTaOsOd = $row["highTaOsOd"];
		$elem_highTaOsOs = $row["highTaOsOs"];
		$row["elem_dateHighTmaxOd"];
		$elem_dateHighTmaxOd = get_date_format($row["elem_dateHighTmaxOd"],'mm-dd-yyyy');
		$elem_highTmaxOdOd = $row["elem_highTmaxOdOd"];
		$elem_dateHighTmaxOs = get_date_format($row["elem_dateHighTmaxOs"],'mm-dd-yyyy');
		$elem_highTmaxOsOs = $row["elem_highTmaxOsOs"];

		$elem_dateHighTxOd = get_date_format($row["dateHighTxOd"],'mm-dd-yyyy');
		$elem_highTxOdOd = $row["highTxOdOd"];
		$elem_highTxOdOs = $row["highTxOdOs"];
		$elem_dateHighTxOs = get_date_format($row["dateHighTxOs"],'mm-dd-yyyy');
		$elem_highTxOsOd = $row["highTxOsOd"];
		$elem_highTxOsOs = $row["highTxOsOs"];

		$elem_dateVf = get_date_format($row["dateVf"],'mm-dd-yyyy');
		$elem_vfOdSummary = $row["vfOdSummary"];
		$elem_vfOsSummary = $row["vfOsSummary"];
		$elem_dateNfa = get_date_format($row["dateNfa"],'mm-dd-yyyy');
		$elem_nfaOdSummary = $row["nfaOdSummary"];
		$elem_nfaOsSummary = $row["nfaOsSummary"];
		$elem_dateGonio = get_date_format($row["dateGonio"],'mm-dd-yyyy');
		$elem_gonioOd = $row["gonioOd"];
		$elem_gonioOs = $row["gonioOs"];
		$elem_datePachy = get_date_format($row["datePachy"],'mm-dd-yyyy');
		$elem_pachyOdReads = $row["pachyOdReads"];
		$elem_pachyOdAvg = $row["pachyOdAvg"];
		$elem_pachyOdCorr = $row["pachyOdCorr"];
		$elem_pachyOsReads = $row["pachyOsReads"];
		$elem_pachyOsAvg = $row["pachyOsAvg"];
		$elem_pachyOsCorr = $row["pachyOsCorr"];
		$elem_dateDiskPhoto = get_date_format($row["dateDiskPhoto"],'mm-dd-yyyy');
		$elem_diskPhotoOd = $row["diskPhotoOd"];
		$elem_diskPhotoOs = $row["diskPhotoOs"];
		$elem_dateCd = get_date_format($row["dateCd"],'mm-dd-yyyy');
		$elem_cdOd = $row["cdOd"];
		$elem_cdOs = $row["cdOs"];
		$elem_cdOdSummary = $row["cdOdSummary"];
		$elem_cdOsSummary = $row["cdOsSummary"];
		$elem_riskFactors = $row["riskFactors"];
		$elem_warnings = $row["warnings"];
		$elem_cd_app_od = $row["cdAppOd"];
		$elem_cd_app_os = $row["cdAppOs"];
		$elem_notes = $row["notes"];
		$elem_cee = $row["cee"];
		$elem_ceeNotes = $row["ceeNotes"];
		$elem_dateCee = get_date_format($row["ceeDate"],'mm-dd-yyyy');
		$targetOd=$row["iopTrgtOd"];
		$targetOs=$row["iopTrgtOs"];
		$sig_imgcd_app_od = (!empty($row["imgcd_app_od"])) ? $oSaveFile->getFilePath($row["imgcd_app_od"],"w") : "" ;
		$sig_imgcd_app_os = (!empty($row["imgcd_app_os"])) ? $oSaveFile->getFilePath($row["imgcd_app_os"],"w") : "" ;
	}

	if($elem_activate != "-1"){
		//Set High Values
		//Ta
		if(empty($elem_highTaOdOd) || ($elem_highTaOdOd < $arrInitialTop["HighTaOd"]["od"])){
			$elem_highTaOdOd =  $arrInitialTop["HighTaOd"]["od"];
			$elem_dateHighTaOd = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTaOd"]["date"]);
		}

		if(empty($elem_highTaOdOs) || ($elem_highTaOdOs < $arrInitialTop["HighTaOd"]["os"])){
			$elem_highTaOdOs = $elem_highTaOdOs;
		}

		if(empty($elem_highTaOsOd) || ($elem_highTaOsOd < $arrInitialTop["HighTaOs"]["od"])){
			$elem_highTaOsOd = $arrInitialTop["HighTaOs"]["od"];
		}

		if(empty($elem_highTaOsOs) || ($elem_highTaOsOs < $arrInitialTop["HighTaOs"]["os"])){
			$elem_highTaOsOs = $arrInitialTop["HighTaOs"]["os"];
			$elem_dateHighTaOs = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTaOs"]["date"]);
		}

		//Tx
		if(empty($elem_highTxOdOd) ||
			($elem_highTxOdOd < $arrInitialTop["HighTxOd"]["od"])){
			$elem_highTxOdOd = $arrInitialTop["HighTxOd"]["od"];
			$elem_dateHighTxOd = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTxOd"]["date"]);
		}

		if(empty($elem_highTxOdOs) ||
			($elem_highTxOdOs < $arrInitialTop["HighTxOd"]["os"])){
			$elem_highTxOdOs = $arrInitialTop["HighTxOd"]["os"];
		}

		if(empty($elem_highTxOsOd) ||
			($elem_highTxOsOd < $arrInitialTop["HighTxOs"]["od"])){
			$elem_highTxOsOd = $arrInitialTop["HighTxOs"]["od"];
		}

		if(empty($elem_highTxOsOs) ||
			($elem_highTxOsOs < $arrInitialTop["HighTxOs"]["os"])){
			$elem_highTxOsOs = $arrInitialTop["HighTxOs"]["os"];
			$elem_dateHighTxOs = $glaucoma_obj->checkWrongDate($arrInitialTop["HighTxOs"]["date"]);
		}

	   //Trgt
		 $row = $glaucoma_obj->getIopTrgtDef($glaucoma_obj->pid);
		 $targetOd = $row["iopTrgtOd"];
		 $targetOs = $row["iopTrgtOs"];
	}

	if($elem_activate != -1){
		//Disk Photo
	    $arrDateDisk = array();
	    $dateDiskLen = count($arrInitialTop["Disk"]);
	    for($i=0;$i<$dateDiskLen;$i++){
		    $arrDateDisk[$arrInitialTop["Disk"][$i]["id"]] = array($arrInitialTop["Disk"][$i]["date"],$arrEmpty,$arrInitialTop["Disk"][$i]["date"]."~~".$arrInitialTop["Disk"][$i]["id"]);
	    }

		$arrDateCEE = array();
		$dateCEELen = count($arrInitialTop["CEE"]);
		for($i=0;$i<$dateCEELen;$i++){
		   $arrDateCEE[$arrInitialTop["CEE"][$i]["id"]] = array($arrInitialTop["CEE"][$i]["date"],$arrEmpty,$arrInitialTop["CEE"][$i]["date"]."~~".$arrInitialTop["CEE"][$i]["id"]);
		}
	}

	$arrDoneNotDone = array();
	$arrDoneNotDone[] = array("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",$arrEmpty,"");
	$arrDoneNotDone[] = array("Done",$arrEmpty,"Done");
	$arrDoneNotDone[] = array("Not Done",$arrEmpty,"Not Done");

	//Menu Option
	$arrMenuVfNfa = array();
	$arrTmp = array("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", "Normal","Border Line", "PS", "Abnormal","Increase Abnormal","Decrease Abnormal","No Change Abnormal","Stable");
	foreach($arrTmp as $key => $var){
		$varTmp =  ($var == "Empty") ? "" : $var;
		$arrMenuVfNfa[] = array($var,$arrEmpty,$varTmp);
	}

	//Targets
	$curformId = $glaucoma_obj->isChartOpened($glaucoma_obj->pid);
	list($targetOdTmp,$targetOsTmp) = $glaucoma_obj->getIopTargets($glaucoma_obj->pid);
	if($curformId != false){
		//check
		$cQry = "select sumOdIop, sumOsIop FROM chart_iop WHERE form_id='".$curformId."' ";
		$row = sqlQuery($cQry);
		if($row != false){
			$targetOd = (!empty($targetOdTmp)) ? $targetOdTmp : $targetOd;
			$targetOs = (!empty($targetOsTmp)) ? $targetOsTmp : $targetOs;
		}
	}else{
		if(empty($targetOd) && empty($targetOs)){
			$targetOd = (!empty($targetOdTmp)) ? $targetOdTmp : $targetOd;
			$targetOs = (!empty($targetOsTmp)) ? $targetOsTmp : $targetOs;
		}
	}

	//TMAX
	$ar_mx_t = $glaucoma_obj->getTMax();
	if(empty($elem_highTmaxOdOd) || $elem_highTmaxOdOd<$ar_mx_t["od"]){
			$elem_highTmaxOdOd=$ar_mx_t["od"];
			$elem_dateHighTmaxOd = get_date_format($ar_mx_t["od_tm"],'yyyy-dd-mm');
	}
	if(empty($elem_highTmaxOsOs) || $elem_highTmaxOsOs<$ar_mx_t["os"]){
			$elem_highTmaxOsOs=$ar_mx_t["os"];
			$elem_dateHighTmaxOs = get_date_format($ar_mx_t["os_tm"],'yyyy-dd-mm');
	}

	//Pachy ---
	$tmpFid = ($curformId != false) ? $curformId : 0 ;
	$tmp = $glaucoma_obj->setDefPachyVals($tmpFid);
	if(count($tmp)>0){
		$elem_pachyOdReads=$tmp[0];
		$elem_pachyOdAvg=$tmp[1];
		$elem_pachyOdCorr=$tmp[2];
		$elem_pachyOsReads=$tmp[3];
		$elem_pachyOsAvg=$tmp[4];
		$elem_pachyOsCorr=$tmp[5];
		$elem_datePachy=$tmp[6];
	}

	//Check HTML 5
	$isHTML5OK = $glaucoma_obj->isHtml5OK();

	$glucoma_dx_menu_arr = array();
	$glucoma_dx_menu_arr[] = "GL-susp, open angle";
	$glucoma_dx_menu_arr[] = "GL-susp, narrow angle";
	$glucoma_dx_menu_arr[] = "POAG";
	$glucoma_dx_menu_arr[] = "NTG/LTG";
	$glucoma_dx_menu_arr[] = "PXFG/PXE";
	$glucoma_dx_menu_arr[] = "ACG";
	$glucoma_dx_menu_arr[] = "PG";
	$glucoma_dx_menu_arr[] = "GL w/ other ocular dx";
	$glucoma_dx_menu_arr[] = "Infl GL";
	$glucoma_dx_menu_arr[] = "Steroid GL";
	$glucoma_dx_menu_arr[] = "NVG";
	$glucoma_dx_menu_arr[] = "ICE";
	$glucoma_dx_menu_arr[] = html_entity_decode("Other 2&#176; GL", null, "UTF-8");
	$glucoma_dx_menu_arr[] = "Childhood OAG";
	$glucoma_dx_menu_arr[] = "Other";

	$glucoma_dx_menu_arr_str = json_encode($glucoma_dx_menu_arr);

	//Get DOS typeahead arr
	$dos_typeahead_arr = array();
		$vf_gl_dos_arr = array();
		$vf_gl_txt_qry = "SELECT vf_gl_id, ptUnderstanding, examDate,reliabilityOd,reliabilityOs,normal_od,normal_os,nonspecific_od,nonspecific_os,nasal_step_od,nasal_step_os,arcuate_od,arcuate_os,hemifield_od,hemifield_os,paracentral_od,paracentral_os,into_fixation_od,into_fixation_os,central_island_od,central_island_os,Stable,worse,improve,interpretation,synthesis_od,synthesis_os 	 FROM vf_gl WHERE patientId = '".$glaucoma_obj->pid."' order by vf_gl_id DESC ";
		$vf_gl_txt_qry_obj = imw_query($vf_gl_txt_qry);

		$interpretation_arr = array();
		$interpretation_arr["stable"] = "S";
		$interpretation_arr["worse"] = "W";
		$interpretation_arr["improve"] = "I";
		$interpretation_arr["not improve"] = "NI";
		$interpretation_arr["likely progression"] = "LW";
		$interpretation_arr["possible progression"] = "PW";

		while($vf_gl_txt_data = imw_fetch_assoc($vf_gl_txt_qry_obj)){
			$vf_gl_dos_val = get_date_format($vf_gl_txt_data["examDate"]);
			$vf_gl_dos_arr[$vf_gl_txt_data["vf_gl_id"]]['date'] = $vf_gl_dos_val;
			$vf_gl_od_txt_val_arr = array();
			if($vf_gl_txt_data["reliabilityOd"] != "")
			{
				$vf_gl_od_txt_val_arr[] = $vf_gl_txt_data["reliabilityOd"];
			}

			if($vf_gl_txt_data["ptUnderstanding"] != "")
			{
				$vf_gl_od_txt_val_arr[] = $vf_gl_txt_data["ptUnderstanding"];
			}

			if(trim($vf_gl_txt_data["normal_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'NL';
			}
			if(trim($vf_gl_txt_data["nonspecific_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'Nonspec';
			}
			if(trim($vf_gl_txt_data["nasal_step_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'NasalStep';
			}
			if(trim($vf_gl_txt_data["arcuate_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'A';
			}
			if(trim($vf_gl_txt_data["hemifield_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'HF';
			}
			if(trim($vf_gl_txt_data["paracentral_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'PC';
			}
			if(trim($vf_gl_txt_data["into_fixation_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'IF';
			}
			if(trim($vf_gl_txt_data["central_island_od"]) != "")
			{
				$vf_gl_od_txt_val_arr[] = 'CI '.$vf_gl_txt_data["central_island_od"];
			}

			$interpretation_vf_gs_od_arr = explode(',',$vf_gl_txt_data["interpretation"]);
			foreach($interpretation_vf_gs_od_arr as $interpretation_vf_od_val)
			{
				$interpretation_vf_od_val = strtolower($interpretation_vf_od_val);
				$interpretation_vf_od_val = trim($interpretation_vf_od_val);
				if(array_key_exists($interpretation_vf_od_val,$interpretation_arr))
				{
					$vf_gl_od_txt_val_arr[] = $interpretation_arr[$interpretation_vf_od_val];
				}
			}
			$elem_vfOdSummary = implode('/',$vf_gl_od_txt_val_arr);
			$vf_gl_dos_arr[$vf_gl_txt_data["vf_gl_id"]]['od'] = $vf_gl_txt_data['synthesis_od'];

			/* ---------- For OS ------------- */
			$vf_gl_os_txt_val_arr = array();
			if($vf_gl_txt_data["reliabilityOs"] != "")
			{
				$vf_gl_os_txt_val_arr[] = $vf_gl_txt_data["reliabilityOs"];
			}

			if($vf_gl_txt_data["ptUnderstanding"] != "")
			{
				$vf_gl_os_txt_val_arr[] = $vf_gl_txt_data["ptUnderstanding"];
			}

			if(trim($vf_gl_txt_data["normal_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'NL';
			}
			if(trim($vf_gl_txt_data["nonspecific_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'Nonspec';
			}
			if(trim($vf_gl_txt_data["nasal_step_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'NasalStep';
			}
			if(trim($vf_gl_txt_data["arcuate_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'A';
			}
			if(trim($vf_gl_txt_data["hemifield_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'HF';
			}
			if(trim($vf_gl_txt_data["paracentral_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'PC';
			}
			if(trim($vf_gl_txt_data["into_fixation_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'IF';
			}
			if(trim($vf_gl_txt_data["central_island_os"]) != "")
			{
				$vf_gl_os_txt_val_arr[] = 'CI '.$vf_gl_txt_data["central_island_os"];
			}

			$interpretation_vf_gs_os_arr = explode(',',$vf_gl_txt_data["interpretation"]);
			foreach($interpretation_vf_gs_os_arr as $interpretation_vf_os_val)
			{
				$interpretation_vf_os_val = strtolower($interpretation_vf_os_val);
				$interpretation_vf_os_val = trim($interpretation_vf_os_val);
				if(array_key_exists($interpretation_vf_os_val,$interpretation_arr))
				{
					$vf_gl_os_txt_val_arr[] = $interpretation_arr[$interpretation_vf_os_val];
				}
			}

			$elem_vfOsSummary = implode('/',$vf_gl_os_txt_val_arr);

			//$vf_gl_dos_arr[$vf_gl_txt_data["vf_gl_id"]]['os'] = $elem_vfOsSummary;
			$vf_gl_dos_arr[$vf_gl_txt_data["vf_gl_id"]]['os'] = $vf_gl_txt_data['synthesis_os'];
			$dos_typeahead_arr['vf_gl_dos_data_arr'] = $vf_gl_dos_arr;
			//echo "vf_gl_dos_data_arr = ".json_encode($vf_gl_dos_arr).";";
		}

		$rnfl_gl_txt_qry = "SELECT oct_rnfl_id,examDate,ptUndersatnding,interpretation,rnfl_od,contour_overall_od,contour_superior_od,contour_inferior_od,contour_temporal_od,symmetric_od,rnfl_os,contour_overall_os,contour_superior_os,contour_inferior_os,contour_temporal_os,symmetric_os,synthesis_od,synthesis_os FROM oct_rnfl WHERE patient_id = '".$glaucoma_obj->pid."' order by oct_rnfl_id DESC ";
		$rnfl_gl_txt_qry_obj = imw_query($rnfl_gl_txt_qry);
		$rnfl_status_arr = array('nl'=>'NL','thin'=>'T','very thin'=>'VT');

		$rnfl_dos_arr = array();
		while($rnfl_gl_txt_data = imw_fetch_assoc($rnfl_gl_txt_qry_obj)){
			$rnfl_dos_val = get_date_format($rnfl_gl_txt_data["examDate"]);
			$rnfl_dos_arr[$rnfl_gl_txt_data["oct_rnfl_id"]]['date'] = $rnfl_dos_val;
			$rnfl_od_val_arr = array();
			$rnfl_od_val_arr[] = $rnfl_gl_txt_data["ptUndersatnding"];

			if(trim($rnfl_gl_txt_data["contour_overall_od"]) != ""){
				$rnfl_status_parts = array();
				$contour_overall_od_arr = explode(',',$rnfl_gl_txt_data["contour_overall_od"]);
				foreach($contour_overall_od_arr as $contour_overall_od_val)
				{
					$contour_overall_od_val = trim($contour_overall_od_val);
					$contour_overall_od_val = strtolower($contour_overall_od_val);
					if(array_key_exists($contour_overall_od_val,$rnfl_status_arr))
					{
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_overall_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0)
				{
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_od_val_arr[] = 'Overall('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["contour_superior_od"]) != ""){
				$rnfl_status_parts = array();
				$contour_superior_od_arr = explode(',',$rnfl_gl_txt_data["contour_superior_od"]);
				foreach($contour_superior_od_arr as $contour_superior_od_val)
				{
					$contour_superior_od_val = trim($contour_superior_od_val);
					$contour_superior_od_val = strtolower($contour_superior_od_val);
					if(array_key_exists($contour_superior_od_val,$rnfl_status_arr))
					{
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_superior_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0)
				{
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_od_val_arr[] = 'Sup('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["contour_inferior_od"]) != ""){
				$rnfl_status_parts = array();
				$contour_inferior_od_arr = explode(',',$rnfl_gl_txt_data["contour_inferior_od"]);
				foreach($contour_inferior_od_arr as $contour_inferior_od_val)
				{
					$contour_inferior_od_val = trim($contour_inferior_od_val);
					$contour_inferior_od_val = strtolower($contour_inferior_od_val);
					if(array_key_exists($contour_inferior_od_val,$rnfl_status_arr))
					{
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_inferior_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0)
				{
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_od_val_arr[] = 'Inf('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["contour_temporal_od"]) != ""){
				$rnfl_status_parts = array();
				$contour_temporal_od_arr = explode(',',$rnfl_gl_txt_data["contour_temporal_od"]);
				foreach($contour_temporal_od_arr as $contour_temporal_od_val)
				{
					$contour_temporal_od_val = trim($contour_temporal_od_val);
					$contour_temporal_od_val = strtolower($contour_temporal_od_val);
					if(array_key_exists($contour_temporal_od_val,$rnfl_status_arr))
					{
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_temporal_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0)
				{
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_od_val_arr[] = 'Temporal('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["symmetric_od"]) != ""){
				$rnfl_od_val_arr[] = 'Symmetric '.substr($rnfl_gl_txt_data["symmetric_od"],0,1);
			}

			$interpretation_rnfl_arr = explode(',',$rnfl_gl_txt_data["interpretation"]);
			foreach($interpretation_rnfl_arr as $interpretation_rnfl_val){
				$interpretation_rnfl_val = strtolower($interpretation_rnfl_val);
				$interpretation_rnfl_val = trim($interpretation_rnfl_val);
				if(array_key_exists($interpretation_rnfl_val,$interpretation_arr))
				{
					$rnfl_od_val_arr[] = $interpretation_arr[$interpretation_rnfl_val];
				}
			}

			$elem_nfaOdSummary = implode('/',$rnfl_od_val_arr);
			$rnfl_dos_arr[$rnfl_gl_txt_data["oct_rnfl_id"]]['od'] = $rnfl_gl_txt_data['synthesis_od'];


			$rnfl_os_val_arr = array();
			$rnfl_os_val_arr[] = $rnfl_gl_txt_data["ptUndersatnding"];

			if(trim($rnfl_gl_txt_data["contour_overall_os"]) != ""){
				$rnfl_status_parts = array();
				$contour_overall_od_arr = explode(',',$rnfl_gl_txt_data["contour_overall_os"]);
				foreach($contour_overall_od_arr as $contour_overall_od_val){
					$contour_overall_od_val = trim($contour_overall_od_val);
					$contour_overall_od_val = strtolower($contour_overall_od_val);
					if(array_key_exists($contour_overall_od_val,$rnfl_status_arr)){
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_overall_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0){
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_os_val_arr[] = 'Overall('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["contour_superior_os"]) != ""){
				$rnfl_status_parts = array();
				$contour_superior_od_arr = explode(',',$rnfl_gl_txt_data["contour_superior_os"]);
				foreach($contour_superior_od_arr as $contour_superior_od_val){
					$contour_superior_od_val = trim($contour_superior_od_val);
					$contour_superior_od_val = strtolower($contour_superior_od_val);
					if(array_key_exists($contour_superior_od_val,$rnfl_status_arr)){
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_superior_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0){
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_os_val_arr[] = 'Sup('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["contour_inferior_os"]) != ""){
				$rnfl_status_parts = array();
				$contour_inferior_od_arr = explode(',',$rnfl_gl_txt_data["contour_inferior_os"]);
				foreach($contour_inferior_od_arr as $contour_inferior_od_val){
					$contour_inferior_od_val = trim($contour_inferior_od_val);
					$contour_inferior_od_val = strtolower($contour_inferior_od_val);
					if(array_key_exists($contour_inferior_od_val,$rnfl_status_arr)){
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_inferior_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0){
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_os_val_arr[] = 'Inf('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["contour_temporal_os"]) != ""){
				$rnfl_status_parts = array();
				$contour_temporal_od_arr = explode(',',$rnfl_gl_txt_data["contour_temporal_os"]);
				foreach($contour_temporal_od_arr as $contour_temporal_od_val){
					$contour_temporal_od_val = trim($contour_temporal_od_val);
					$contour_temporal_od_val = strtolower($contour_temporal_od_val);
					if(array_key_exists($contour_temporal_od_val,$rnfl_status_arr)){
						$rnfl_status_parts[] = $rnfl_status_arr[$contour_temporal_od_val];
					}
				}
				if(count($rnfl_status_parts) > 0){
					$rnfl_status_parts_str = implode('/',$rnfl_status_parts);
					$rnfl_os_val_arr[] = 'Temporal('.$rnfl_status_parts_str.')';
				}
			}

			if(trim($rnfl_gl_txt_data["symmetric_os"]) != ""){
				$rnfl_os_val_arr[] = 'Symmetric '.substr($rnfl_gl_txt_data["symmetric_os"],0,1);
			}

			$interpretation_rnfl_arr = explode(',',$rnfl_gl_txt_data["interpretation"]);
			foreach($interpretation_rnfl_arr as $interpretation_rnfl_val){
				$interpretation_rnfl_val = strtolower($interpretation_rnfl_val);
				$interpretation_rnfl_val = trim($interpretation_rnfl_val);
				if(array_key_exists($interpretation_rnfl_val,$interpretation_arr))
				{
					$rnfl_os_val_arr[] = $interpretation_arr[$interpretation_rnfl_val];
				}
			}
			$elem_nfaOsSummary = implode('/',$rnfl_os_val_arr);
			$rnfl_dos_arr[$rnfl_gl_txt_data["oct_rnfl_id"]]['os'] = $rnfl_gl_txt_data['synthesis_os'];
			$dos_typeahead_arr['rnfl_dos_data_arr'] = $rnfl_dos_arr;
			//echo "rnfl_dos_data_arr = ".json_encode($rnfl_dos_arr).";";
		}

		/* DISC APPEARANCE */
		$disc_app_qry = "SELECT optic_id,date_format(exam_date,'%Y-%m-%d') as exam_date, od_text, os_text, cd_val_od,cd_val_os,optic_nerve_od_summary,optic_nerve_os_summary FROM chart_optic WHERE patient_id = '".$glaucoma_obj->pid."' ORDER BY optic_id DESC";
		$disc_app_qry_obj = imw_query($disc_app_qry);
		$disc_app_data_arr = array();

		while($disc_app_data = imw_fetch_assoc($disc_app_qry_obj))
		{
			$disc_dos_val = get_date_format($disc_app_data["exam_date"]);
			$disc_app_data_arr[$disc_app_data["optic_id"]]["date"] = $disc_dos_val;
			if(trim($disc_app_data["cd_val_od"]) == ""){
				$disc_app_data_arr[$disc_app_data["optic_id"]]["cd_od"] = $disc_app_data["od_text"];
			}
			else{
				$disc_app_data_arr[$disc_app_data["optic_id"]]["cd_od"] = $disc_app_data["cd_val_od"];
			}

			if(trim($disc_app_data["cd_val_os"]) == ""){
				$disc_app_data_arr[$disc_app_data["optic_id"]]["cd_os"] = $disc_app_data["os_text"];
			}
			else{
				$disc_app_data_arr[$disc_app_data["optic_id"]]["cd_os"] = $disc_app_data["cd_val_os"];
			}

			$disc_app_data_arr[$disc_app_data["optic_id"]]["optic_od"] = $disc_app_data["optic_nerve_od_summary"];
			$disc_app_data_arr[$disc_app_data["optic_id"]]["optic_os"] = $disc_app_data["optic_nerve_os_summary"];
		}
		$dos_typeahead_arr['disc_app_dos_data_arr'] = $disc_app_data_arr;
		//echo "disc_app_dos_data_arr = '".json_encode($disc_app_data_arr)."';";


		/* GONIO */
		$gonio_req_qry = "SELECT gonio_id, date_format(examDateGonio,'%Y-%m-%d') as examDateGonio, gonio_od_summary, gonio_os_summary FROM chart_gonio WHERE patient_id = '".$glaucoma_obj->pid."' order by gonio_id DESC";
		$gonio_qry_obj = imw_query($gonio_req_qry);
		$gonio_dos_data_arr = array();
		while($gonio_dos_data = imw_fetch_assoc($gonio_qry_obj))
		{
			$gonio_dos_data_arr[$gonio_dos_data["gonio_id"]]["date"] = get_date_format($gonio_dos_data["examDateGonio"]);
			$gonio_dos_data_arr[$gonio_dos_data["gonio_id"]]["od_summary"] = $gonio_dos_data["gonio_od_summary"];
			$gonio_dos_data_arr[$gonio_dos_data["gonio_id"]]["os_summary"] = $gonio_dos_data["gonio_os_summary"];
		}
		$dos_typeahead_arr['gonio_dos_data_arr'] = $gonio_dos_data_arr;
		//echo "gonio_dos_data_arr = '".json_encode($gonio_dos_data_arr)."';";

		/* Pachy */
		$pachy_req_qry = "SELECT pachy_id,examDate,pachy_od_readings,pachy_od_average,pachy_od_correction_value,pachy_os_readings,pachy_os_average,pachy_os_correction_value FROM pachy WHERE patientId = '".$glaucoma_obj->pid."' order by pachy_id DESC";
		$pachy_req_qry_obj = imw_query($pachy_req_qry);
		$pachy_dos_data_arr = array();
		$pachy_inc = 0;
		while($pachy_dos_data = imw_fetch_assoc($pachy_req_qry_obj))
		{
			$pachy_dos_data_arr[$pachy_inc]["date"] = get_date_format($pachy_dos_data["examDate"]);
			$pachy_dos_data_arr[$pachy_inc]["od_readings"] = $pachy_dos_data["pachy_od_readings"];
			$pachy_dos_data_arr[$pachy_inc]["os_readings"] = $pachy_dos_data["pachy_os_readings"];
			$pachy_dos_data_arr[$pachy_inc]["od_correction"] = $pachy_dos_data["pachy_od_correction_value"];
			$pachy_dos_data_arr[$pachy_inc]["os_correction"] = $pachy_dos_data["pachy_os_correction_value"];
			$pachy_dos_data_arr[$pachy_inc]["od_avg"] = $pachy_dos_data["pachy_od_average"];
			$pachy_dos_data_arr[$pachy_inc]["os_avg"] = $pachy_dos_data["pachy_os_average"];

			$pachy_inc++;
		}

		$pachy_req_qry = "SELECT cor_id,cor_date,reading_od,reading_os,cor_val_od,cor_val_os,avg_od,avg_os FROM chart_correction_values WHERE patient_id = '".$glaucoma_obj->pid."' and (reading_od!='' or reading_os!='') order by cor_id DESC";
		$pachy_req_qry_obj = imw_query($pachy_req_qry);
		//$pachy_dos_data_arr = array();
		while($pachy_dos_data = imw_fetch_assoc($pachy_req_qry_obj))
		{
			$cor_date = get_date_format($pachy_dos_data["cor_date"]);
			if(trim($cor_date) != ""){
				$matched = 0;
				foreach($pachy_dos_data_arr as $pachy_val_arr){
					if(trim($pachy_val_arr["date"]) == $cor_date){
						if($pachy_val_arr["od_readings"] == $pachy_dos_data["reading_od"] && $pachy_val_arr["os_readings"] == $pachy_dos_data["reading_os"] && $pachy_val_arr["od_correction"] == $pachy_dos_data["cor_val_od"] && $pachy_val_arr["os_correction"] == $pachy_dos_data["cor_val_os"] && $pachy_val_arr["od_avg"] == $pachy_dos_data["avg_od"] && $pachy_val_arr["os_avg"] == $pachy_dos_data["avg_os"])
						{
							//break;
							$matched = 1;
						}
					}
				}

				if($matched == 0){
					$pachy_dos_data_arr[$pachy_inc]["date"] = get_date_format($pachy_dos_data["cor_date"]);
					$pachy_dos_data_arr[$pachy_inc]["od_readings"] = $pachy_dos_data["reading_od"];
					$pachy_dos_data_arr[$pachy_inc]["os_readings"] = $pachy_dos_data["reading_os"];
					$pachy_dos_data_arr[$pachy_inc]["od_correction"] = $pachy_dos_data["cor_val_od"];
					$pachy_dos_data_arr[$pachy_inc]["os_correction"] = $pachy_dos_data["cor_val_os"];
					$pachy_dos_data_arr[$pachy_inc]["od_avg"] = $pachy_dos_data["avg_od"];
					$pachy_dos_data_arr[$pachy_inc]["os_avg"] = $pachy_dos_data["avg_os"];
					$pachy_inc++;
				}
			}
		}
		$dos_typeahead_arr['pachy_dos_data_arr'] = $pachy_dos_data_arr;
		//echo "pachy_dos_data_arr = '".json_encode($pachy_dos_data_arr)."';";

		$ta_od_qry = "SELECT id, taOd,taOs, highTaOdDate,ta_time FROM glaucoma_past_readings WHERE patientId = '".$glaucoma_obj->pid."' and highTaOdDate != '' and (taOd!='' or taOs!='') order by id DESC";
		$ta_od_qry_obj = imw_query($ta_od_qry);
		$ta_od_dos_data_arr = array();
		while($ta_dos_data = imw_fetch_assoc($ta_od_qry_obj))
		{
			$ta_time=str_replace('AM',' AM',str_replace('PM',' PM',$ta_dos_data["ta_time"]));
			$ta_od_dos_data_arr[$ta_dos_data["id"]]["date"] = $ta_dos_data["highTaOdDate"];
			$ta_od_dos_data_arr[$ta_dos_data["id"]]["taOd"] = $ta_dos_data["taOd"];
			$ta_od_dos_data_arr[$ta_dos_data["id"]]["taOs"] = $ta_dos_data["taOs"];
			$ta_od_dos_data_arr[$ta_dos_data["id"]]["time"] = $ta_time;
		}
		$dos_typeahead_arr['ta_od_dos_data_arr'] = $ta_od_dos_data_arr;
		//echo "ta_od_dos_data_arr = '".json_encode($ta_od_dos_data_arr)."';";

		$ta_os_qry = "SELECT id, taOs, highTaOsDate,ta_time FROM glaucoma_past_readings WHERE patientId = '".$glaucoma_obj->pid."' and highTaOsDate != '' and taOs!='' order by id DESC";
		$ta_os_qry_obj = imw_query($ta_os_qry);
		$ta_os_dos_data_arr = array();
		while($ta_dos_data = imw_fetch_assoc($ta_os_qry_obj))
		{
			$ta_time=str_replace('AM',' AM',str_replace('PM',' PM',$ta_dos_data["ta_time"]));
			$ta_os_dos_data_arr[$ta_dos_data["id"]]["date"] = $ta_dos_data["highTaOsDate"];
			$ta_os_dos_data_arr[$ta_dos_data["id"]]["taOs"] = $ta_dos_data["taOs"];
			$ta_os_dos_data_arr[$ta_dos_data["id"]]["time"] = $ta_time;
		}
		$dos_typeahead_arr['ta_os_dos_data_arr'] = $ta_os_dos_data_arr;
		//echo "ta_os_dos_data_arr = '".json_encode($ta_os_dos_data_arr)."';";


		$tx_od_qry = "SELECT id, txOd,txOs, highTxOdDate,tx_time FROM glaucoma_past_readings WHERE patientId = '".$glaucoma_obj->pid."' and highTxOdDate != '' and (txOd!='' or txOs!='') order by id DESC";
		$tx_od_qry_obj = imw_query($tx_od_qry);
		$tx_od_dos_data_arr = array();
		while($tx_dos_data = imw_fetch_assoc($tx_od_qry_obj))
		{
			$tx_time=str_replace('AM',' AM',str_replace('PM',' PM',$tx_dos_data["tx_time"]));
			$tx_od_dos_data_arr[$tx_dos_data["id"]]["date"] = $tx_dos_data["highTxOdDate"];
			$tx_od_dos_data_arr[$tx_dos_data["id"]]["txOd"] = $tx_dos_data["txOd"];
			$tx_od_dos_data_arr[$tx_dos_data["id"]]["txOs"] = $tx_dos_data["txOs"];
			$tx_od_dos_data_arr[$tx_dos_data["id"]]["time"] = $tx_time;
		}
		$dos_typeahead_arr['tx_od_dos_data_arr'] = $tx_od_dos_data_arr;
		//echo "tx_od_dos_data_arr = '".json_encode($tx_od_dos_data_arr)."';";

		$tx_os_qry = "SELECT id, txOs, highTxOsDate,tx_time FROM glaucoma_past_readings WHERE patientId = '".$glaucoma_obj->pid."' and highTxOsDate != '' and txOs!='' order by id DESC";
		$tx_os_qry_obj = imw_query($tx_os_qry);
		$tx_os_dos_data_arr = array();
		while($tx_dos_data = imw_fetch_assoc($tx_os_qry_obj))
		{
			$tx_time=str_replace('AM',' AM',str_replace('PM',' PM',$tx_dos_data["tx_time"]));
			$tx_os_dos_data_arr[$tx_dos_data["id"]]["date"] = $tx_dos_data["highTxOsDate"];
			$tx_os_dos_data_arr[$tx_dos_data["id"]]["txOs"] = $tx_dos_data["txOs"];
			$tx_os_dos_data_arr[$tx_dos_data["id"]]["time"] = $tx_time;
		}
		$dos_typeahead_arr['tx_os_dos_data_arr'] = $tx_os_dos_data_arr;
		//echo "tx_os_dos_data_arr = '".json_encode($tx_os_dos_data_arr)."';";


		$tt_od_qry = "SELECT id, ttOd,ttOs, highTtOdDate,tt_time FROM glaucoma_past_readings WHERE patientId = '".$glaucoma_obj->pid."' and highTtOdDate != '' and (ttOd!='' or ttOs!='') order by id DESC";
		$tt_od_qry_obj = imw_query($tt_od_qry);
		$tt_od_dos_data_arr = array();
		while($tt_dos_data = imw_fetch_assoc($tt_od_qry_obj))
		{
			$tt_time=str_replace('AM',' AM',str_replace('PM',' PM',$tt_dos_data["tt_time"]));
			$tt_od_dos_data_arr[$tt_dos_data["id"]]["date"] = $tt_dos_data["highTtOdDate"];
			$tt_od_dos_data_arr[$tt_dos_data["id"]]["ttOd"] = $tt_dos_data["ttOd"];
			$tt_od_dos_data_arr[$tt_dos_data["id"]]["ttOs"] = $tt_dos_data["ttOs"];
			$tt_od_dos_data_arr[$tt_dos_data["id"]]["time"] = $tt_time;
		}
		$dos_typeahead_arr['tt_od_dos_data_arr'] = $tt_od_dos_data_arr;
		//echo "tt_od_dos_data_arr = '".json_encode($tt_od_dos_data_arr)."';";

		$tt_os_qry = "SELECT id, ttOs, highTtOsDate,tt_time FROM glaucoma_past_readings WHERE patientId = '".$glaucoma_obj->pid."' and highTtOsDate != '' and ttOs!='' order by id DESC";
		$tt_os_qry_obj = imw_query($tt_os_qry);
		$tt_os_dos_data_arr = array();
		while($tt_dos_data = imw_fetch_assoc($tt_os_qry_obj))
		{
			$tt_time=str_replace('AM',' AM',str_replace('PM',' PM',$tt_dos_data["tt_time"]));
			$tt_os_dos_data_arr[$tt_dos_data["id"]]["date"] = $tt_dos_data["highTtOsDate"];
			$tt_os_dos_data_arr[$tt_dos_data["id"]]["ttOs"] = $tt_dos_data["ttOs"];
			$tt_os_dos_data_arr[$tt_dos_data["id"]]["time"] = $tt_time;
		}
		$dos_typeahead_arr['tt_os_dos_data_arr'] = $tt_os_dos_data_arr;
		//echo "tt_os_dos_data_arr = '".json_encode($tt_os_dos_data_arr)."';";
	$json_dos_typeahead_arr = json_encode($dos_typeahead_arr);
?>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
		<title>Patient Refractive Sheet</title>
		<!-- Bootstrap -->
		<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
		<!-- Bootstrap Selctpicker CSS -->
		<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
		<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
		<!-- Messi Plugin for fancy alerts CSS -->
			<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
		<!-- DateTime Picker CSS -->
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
		<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/gfs.css"/>

		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
			  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
		<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
		<!-- jQuery's Date Time Picker -->
		<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
		<!-- Bootstrap -->
		<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>

		<!-- Bootstrap Selectpicker -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
		<!-- Bootstrap typeHead -->
		<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/amcharts.js" type="text/javascript"></script>
		<script src="<?php echo $library_path; ?>/amcharts/serial.js" type="text/javascript"></script>
		<style>
			.process_loader {
				border: 16px solid #f3f3f3;
				border-radius: 50%;
				border-top: 16px solid #3498db;
				width: 80px;
				height: 80px;
				-webkit-animation: spin 2s linear infinite;
				animation: spin 2s linear infinite;
				display: inline-block;
			}
			.adminbox{min-height:inherit}
			.adminbox label{overflow:initial;}
			.adminbox .panel-body{padding:5px}
			.adminbox div:nth-child(odd) {padding-right: 1%;}
			.od{color:blue;}
			.os{color:green;}
			.ou{color:#9900cc;}
			.checkbox label::after{padding-top:0px}
			td .input-group-btn .btn { display:inline!important;}
			#btn_gonio_warn.label-danger{ cursor:pointer; }
		</style>
	</head>
<script>
	var typeahead_iop_arr = JSON.parse('<?php echo str_replace(array('\r','\n'),'',$json_dos_typeahead_arr); ?>');
	vf_gl_dos_data_arr = typeahead_iop_arr.vf_gl_dos_data_arr;
	rnfl_dos_data_arr = typeahead_iop_arr.rnfl_dos_data_arr;
	disc_app_dos_data_arr = typeahead_iop_arr.disc_app_dos_data_arr;
	gonio_dos_data_arr = typeahead_iop_arr.gonio_dos_data_arr;
	pachy_dos_data_arr = typeahead_iop_arr.pachy_dos_data_arr;
	ta_od_dos_data_arr = typeahead_iop_arr.ta_od_dos_data_arr;
	ta_os_dos_data_arr = typeahead_iop_arr.ta_os_dos_data_arr;
	tx_od_dos_data_arr = typeahead_iop_arr.tx_od_dos_data_arr;
	tx_os_dos_data_arr = typeahead_iop_arr.tx_os_dos_data_arr;
	tt_od_dos_data_arr = typeahead_iop_arr.tt_od_dos_data_arr;
	tt_os_dos_data_arr = typeahead_iop_arr.tt_os_dos_data_arr;

	var isHTML5OK="<?php echo $isHTML5OK;?>";
	//Calendar funcs
	var today = new Date();
	var day = today.getDate();
	var month = today.getMonth()
	var year = y2k(today.getYear());

	function y2k(number)
	{
		return (number < 1000)? number+1900 : number;
	}
	function padout(number)
	{
		return (number < 10) ? '0' + number : number;
	}
	function restart(id)
	{
		gebi(id).value=''+ padout(month - 0 + 1) + '-'  + padout(day) + '-' +  year ;
		mywindow.close();
		if(typeof gebi(id).onchange == "function"){
			gebi(id).onchange();
		}
	}

	// Applet Funcs
	function getCoords(ap)
	{
		var coords = document.applets[ap].getSign();
		return coords;
	}

	function getAppClear(id)
	{
		with(document.applets[id])
		{
			clearIt();
		}

		get_App_Coords(id);
	}

	function get_App_Coords(str)
	{
		//var nameApplet = obj.name;
		var nameApplet = str;
		var nameHiddenElem = "elem_".concat(nameApplet);
		var coords = getCoords(nameApplet);
		var objHiddenElem = gebi(nameHiddenElem);
		objHiddenElem.value = refineCoords(coords);
	}

	function show_img_on_demand(obj)
	{
		var dataArr = $(obj).data();

        if(dataArr.src && dataArr.type){
            var srcType = dataArr.type.toLowerCase();

            switch(srcType){
                case 'pdf':
                    window.open(dataArr.src);
                break;

                default:
                    window.parent.$('#imgModal .modal-body').html('<img src="'+dataArr.src+'" width="100%"  height="100%"/>');
                    window.parent.$('#imgModal').modal('show');
            }
        }


        //window.open("large_image_gfs.php?img_src="+img_src,"_blank","width=800,height=800");
		//top.window.open("common/scanLoader.php?crId="+scan_id+"&prId=0","_blank");
	}

////////////////////////
// Pachy Correction value
////////////////////////
/*
function trim(val)
{
	return val.replace(/^\s+|\s+$/, '');
}
*/

function refineCorrectionValue(mxc,mnc,mx,mn,av)
{
	var totalVal = parseInt(mx) + parseInt(mn);
	var avgVal = parseInt(totalVal/2);
	//alert(av +" : "+ avgVal);
	if(av >= avgVal)
	{
		return mnc;
	}else if(av < avgVal)
	{
		return mxc;
	}
}

function getCorrectionValue(val)
{
	var maxVal,minVal,corVal,maxCorVal,minCorVal;
	if((val < 445) || (val > 645))
	{
		//alert("Average Value is out of the range of Correction Table (445 - 645).");
		if(val > 645){
			return ">-7";
		}
		else if(val < 445){
			return ">7";
		}
	}
	else if((val >= 445) && (val < 455))
	{
		maxCorVal = 7;
		minCorVal = 6;
		maxVal = 445;
		minVal = 455;
	}
	else if((val >= 455) && (val < 465))
	{
		maxCorVal = 6;
		minCorVal = 6;
		maxVal = 455;
		minVal = 465;

	}
	else if((val >= 465) && (val < 475))
	{
		maxCorVal = 6;
		minCorVal = 5;
		maxVal = 465;
		minVal = 475;
	}
	else if((val >= 475) && (val < 485))
	{
		maxCorVal = 5;
		minCorVal = 4;
		maxVal = 475;
		minVal = 485;
	}
	else if((val >= 485) && (val < 495))
	{
		maxCorVal = 4;
		minCorVal = 4;
		maxVal = 485;
		minVal = 495;
	}
	else if((val >= 495) && (val < 505))
	{
		maxCorVal = 4;
		minCorVal = 3;
		maxVal = 495;
		minVal = 505;
	}
	else if((val >= 505) && (val < 515))
	{
		maxCorVal = 3;
		minCorVal = 2;
		maxVal = 505;
		minVal = 515;
	}
	else if((val >= 515) && (val < 525))
	{
		maxCorVal = 2;
		minCorVal = 1;
		maxVal = 515;
		minVal = 525;
	}
	else if((val >= 525) && (val < 535))
	{
		maxCorVal = 1;
		minCorVal = 1;
		maxVal = 525;
		minVal = 535;
	}
	else if((val >= 535) && (val < 545))
	{
		maxCorVal = 1;
		minCorVal = 0;
		maxVal = 545;
		minVal = 535;
	}
	else if((val >= 545) && (val < 555))
	{
		maxCorVal = 0;
		minCorVal = -1;
		maxVal = 545;
		minVal = 555;
	}
	else if((val >= 555) && (val < 565))
	{
		maxCorVal = -1;
		minCorVal = -1;
		maxVal = 555;
		minVal = 565;
	}
	else if((val >= 565) && (val < 575))
	{
		maxCorVal = -1;
		minCorVal = -2;
		maxVal = 565;
		minVal = 575;
	}
	else if((val >= 575) && (val < 585))
	{
		maxCorVal = -2;
		minCorVal = -3;
		maxVal = 575;
		minVal = 585;
	}
	else if((val >= 585) && (val < 595))
	{
		maxCorVal = -3;
		minCorVal = -4;
		maxVal = 585;
		minVal = 595;
	}
	else if((val >= 595) && (val < 605))
	{
		maxCorVal = -4;
		minCorVal = -4;
		maxVal = 595;
		minVal = 605;
	}
	else if((val >= 605) && (val < 615))
	{
		maxCorVal = -4;
		minCorVal = -5;
		maxVal = 605;
		minVal = 615;
	}
	else if((val >= 615) && (val < 625))
	{
		maxCorVal = -5;
		minCorVal = -6;
		maxVal = 615;
		minVal = 625;
	}
	else if((val >= 625) && (val < 635))
	{
		maxCorVal = -6;
		minCorVal = -6;
		maxVal = 625;
		minVal = 635;
	}
	else if((val >= 635) && (val <= 645))
	{
		maxCorVal = -6;
		minCorVal = -7;
		maxVal = 635;
		minVal = 645;
	}
	corVal = refineCorrectionValue(maxCorVal,minCorVal,maxVal,minVal,val);
	return corVal;
}

function isValid(strVal)
{
	var bag = "0123456789,";
	var strLength = strVal.length;
	var chr;

	for(i=0;i<strLength;i++)
	{
		chr = strVal.charAt(i);
		if(bag.indexOf(chr) == -1)
		{
			// Char Nahi hai
			return false;
		}
	}
	return true;
}

function calCorrectionVal(val,wh)
{
	//Remove Spaces
	var strVal = $.trim(val);
	// Set Objs
	if(wh == "OD")
	{
		var objTdAvg = $("#td_pachy_od_avg");
		var objTextAvg = $("#elem_pachyOdAvg");
		var objTextCor = $("#elem_pachyOdCorr");
		var objTextRead = $("#elem_pachyOdReads");
	}
	else if(wh == "OS")
	{
		var objTdAvg = $("#td_pachy_os_avg");
		var objTextAvg = $("#elem_pachyOsAvg");
		var objTextCor = $("#elem_pachyOsCorr");
		var objTextRead = $("#elem_pachyOsReads");
	}

	//Check Value for numeric
	if(!isValid(strVal))
	{
		alert("Please enter comma separated Numeric values only.\n\n(0123456789,)");
		objTextRead.val() = objTextAvg.val() = objTextCor.val() = "";
	}
	else
	{
		var arrReadings = new Array();
		arrReadings = val.split(",");
		//Length of Array
		var arrLength = arrReadings.length;
		var counter = 0;
		var pachyReading = 0;
		//Add all values
		for(i=0;i<arrLength;i++)
		{
			if(trim(arrReadings[i]) != "")
			{
				pachyReading = parseInt(pachyReading) + parseInt(arrReadings[i]);
				counter += 1;
			}
		}

		//Get Avg
		var avgReading = parseInt(pachyReading)/parseInt(counter);
		avgReading = Math.round(avgReading);
		//Correction Value
		var correctionVal = getCorrectionValue(avgReading);
		if((typeof(correctionVal) != "undefined"))
		{
			if(counter > 1)
			{
				//
				objTdAvg.css('display','block');
				objTextAvg.val(avgReading);
				objTextCor.val(correctionVal);
			}
			else
			{
				objTdAvg.css('display','none');
				objTextAvg.val(0); //avgReading;
				objTextCor.val(correctionVal);
			}
		}
		else
		{
			objTextRead.val("");
			//objTextRead.onchange();
		}
	}
}



	// Others
	function checkdate(obj){
		//var flag = checkDate_v3(obj);
	}

	//var vf_gl_dos_data_arr = '';
	function displayVf(objElem)
	{
		var value_id = "";
		var value = objElem.value;
		var indexId = value.indexOf("~~");
		if(indexId != -1){
			text = value.substring(0,indexId);
			value = value.substring(indexId+2);
			value_id = value;
			objElem.value = text;
		}
		checkdate(objElem);// checkDate

		var objFrm = document.forms["frmGlucoma"];
		var id = "",od = os = "Abn";
		if(trim(value) != "")
		{
			var objHdFrm = document.forms["frmVF".concat(value)];
			if(objHdFrm != null){
			id = objHdFrm.elem_idVf.value;
			od = objHdFrm.elem_vfOdSummary.value;
			os = objHdFrm.elem_vfOsSummary.value;
			}
		}

		objFrm.elem_idVf.value = value_id;
		objFrm.elem_vfOdSummary.value = vf_gl_dos_data_arr[value_id].od;
		objFrm.elem_vfOsSummary.value = vf_gl_dos_data_arr[value_id].os;
	}

	//var rnfl_dos_data_arr = "";
	function displayNfa(objElem)
	{
		var value_id = "";
		var value = objElem.value;
		var indexId = value.indexOf("~~");
		if(indexId != -1){
			text = value.substring(0,indexId);
			value = value.substring(indexId+2);
			value_id = value;
			objElem.value = text;
		}
		checkdate(objElem);// checkDate

		var objFrm = document.forms["frmGlucoma"];
		var id = "",od = os = "Abn";
		if(trim(value) != "")
		{
			var objHdFrm = document.forms["frmNFA".concat(value)];
			if(objHdFrm != null){
			id = objHdFrm.elem_idNfa.value;
			od = objHdFrm.elem_nfaOdSummary.value;
			os = objHdFrm.elem_nfaOsSummary.value;
			}
		}

		objFrm.elem_idNfa.value = value_id;
		objFrm.elem_nfaOdSummary.value = rnfl_dos_data_arr[value_id].od;
		objFrm.elem_nfaOsSummary.value = rnfl_dos_data_arr[value_id].os;
	}

	//var gonio_dos_data_arr = "";
	function displayGonio(objElem)
	{
		var value_id = '';
		var value = objElem.value;
		var indexId = value.indexOf("~~");
		if(indexId != -1){
			text = value.substring(0,indexId);
			value = value.substring(indexId+2);
			value_id = value;
			objElem.value = text;
		}
		checkdate(objElem);// checkDate

		var objFrm = document.forms["frmGlucoma"];
		var id = "",od = os = "";
		if(trim(value) != "")
		{
			var objHdFrm = document.forms["frmGonio".concat(value)];
			if(objHdFrm != null){
			id = objHdFrm.elem_idGonio.value;
			od = objHdFrm.elem_gonioOd.value;
			os = objHdFrm.elem_gonioOs.value;
			}
		}

		objFrm.elem_idGonio.value = value_id;

		objFrm.elem_gonioOd.value = gonio_dos_data_arr[value_id].od_summary; // != "" ? "Done" : "Not Done";
		objFrm.elem_gonioOs.value = gonio_dos_data_arr[value_id].os_summary; // != "" ? "Done" : "Not Done";
		set_gonio_draw_btn();
	}

	function set_gonio_draw_btn(){
		var objFrm = document.forms["frmGlucoma"];
		var id = objFrm.elem_idGonio.value;
		if(id=="" || id=="0"){ return; }

		$("#btn_gonio_warn").popover('destroy');
		//$("#btn_gonio_warn").popover('destroy');
		$("#btn_gonio_warn").unbind("click");
		$("#btn_gonio_warn").removeClass("label-danger pointer").addClass("label-default");
		$.get("?op=get_gonio_inf&id="+id, function(d){
			//console.log(d);
			if(d.file_path!=""){
					$("#btn_gonio_warn").removeClass("label-default").addClass("label-danger");
			}
			if(d.is_edit==0 && d.file_path!=""){
				setTimeout(function(){
					$("#btn_gonio_warn").popover({title: "Gonio Drawing", content: "<img src='"+d.file_path+"' width='500'>",html:true, placement: "right"});
				}, 1000);
			}else if(d.is_edit==1){
				$("#btn_gonio_warn").addClass("pointer");
				$("#btn_gonio_warn").bind("click", function(){
						if(top.opener && top.opener.top &&
								top.opener.top.fmain &&
								top.opener.top.fmain.$("#flagWnlDrawGonio label").length>0){
							top.opener.top.fmain.$("#flagWnlDrawGonio label").trigger("click");
						}
					});
			}

		}, "json");
	}

	//var pachy_dos_data_arr = "";
	function displayPachy(objElem)
	{
		var value_id = "";
		var value = objElem.value;
		var indexId = value.indexOf("~~");
		if(indexId != -1){
			text = value.substring(0,indexId);
			value = value.substring(indexId+2);
			value_id = value;
			objElem.value = text;
		}
		checkdate(objElem);// checkDate

		var objFrm = document.forms["frmGlucoma"];
		var id = "";
		var pachyOdReads = pachyOdAvg = pachyOdCorr = pachyOsReads = pachyOsAvg = pachyOsCorr = "";
		if(trim(value) != "")
		{
			var objHdFrm = document.forms["frmPachy".concat(value)];
			if(objHdFrm != null){
			id = objHdFrm.elem_idPachy.value;
			pachyOdReads = objHdFrm.elem_pachyOdReads.value;
			pachyOdAvg = objHdFrm.elem_pachyOdAvg.value;
			pachyOdCorr = objHdFrm.elem_pachyOdCorr.value;
			pachyOsReads = objHdFrm.elem_pachyOsReads.value;
			pachyOsAvg = objHdFrm.elem_pachyOsAvg.value;
			pachyOsCorr = objHdFrm.elem_pachyOsCorr.value;
			}
		}
		objFrm.elem_idCd.value = value_id;
		objFrm.elem_pachyOdReads.value = pachy_dos_data_arr[value_id].od_readings;
		objFrm.elem_pachyOdAvg.value = pachy_dos_data_arr[value_id].od_avg;
		objFrm.elem_pachyOdCorr.value = pachy_dos_data_arr[value_id].od_correction;

		var elem_pachyOdReads_val = isNaN(parseInt(objFrm.elem_pachyOdReads.value)) ? 0 : parseInt(objFrm.elem_pachyOdReads.value);
		var elem_pachyOdCorr_val = isNaN(parseInt(objFrm.elem_pachyOdCorr.value)) ? 0 : parseInt(objFrm.elem_pachyOdCorr.value);
		var pachy_od_status =  elem_pachyOdReads_val;
		var pachy_od_status_val =pachy_status_view(pachy_od_status);
		$("#pachy_dos_od_status").val(pachy_od_status_val);

		objFrm.elem_pachyOsReads.value = pachy_dos_data_arr[value_id].os_readings;
		objFrm.elem_pachyOsAvg.value = pachy_dos_data_arr[value_id].os_avg;
		objFrm.elem_pachyOsCorr.value = pachy_dos_data_arr[value_id].os_correction;

		var elem_pachyOsReads_val = isNaN(parseInt(objFrm.elem_pachyOsReads.value)) ? 0 : parseInt(objFrm.elem_pachyOsReads.value);
		var elem_pachyOsCorr_val = isNaN(parseInt(objFrm.elem_pachyOsCorr.value)) ? 0 : parseInt(objFrm.elem_pachyOsCorr.value);
		var pachy_os_status = elem_pachyOsReads_val;
		var pachy_os_status_val = pachy_status_view(pachy_os_status);
		$("#pachy_dos_os_status").val(pachy_os_status_val);
	}

	function pachy_status_view(pachy_val)
	{
		var return_val = '';
		if(pachy_val != 0)
		{
			if(pachy_val < 555)
			{
				return_val = 'Thin';
			}
			else if(pachy_val >= 555 && pachy_val <= 588)
			{
				return_val = 'Average';
			}
			else if(pachy_val > 588)
			{
				return_val = 'Thick';
			}
		}
		return return_val;
	}

	function displayDisk(objElem)
	{
		var value = objElem.value;
		var indexId = value.indexOf("~~");
		if(indexId != -1){
			text = value.substring(0,indexId);
			value = value.substring(indexId+2);
			objElem.value = text;
		}
		checkdate(objElem);// checkDate

		var objFrm = document.forms["frmGlucoma"];
		var id = "";
		var diskPhotoOd = diskPhotoOs = "Empty";
		if(trim(value) != "")
		{
			var objHdFrm = document.forms["frmDiskPhoto".concat(value)];
			if(objHdFrm != null){
			id = objHdFrm.elem_idDiskPhoto.value;
			diskPhotoOd = objHdFrm.elem_diskPhotoOd.value;
			diskPhotoOs = objHdFrm.elem_diskPhotoOs.value;
			}
		}
		objFrm.elem_idCd.value = id;
		objFrm.elem_diskPhotoOd.value = diskPhotoOd;
		objFrm.elem_diskPhotoOs.value = diskPhotoOs;
	}

	//var disc_app_dos_data_arr = "";
	function displayCD(objElem)
	{
		var value_id = "";
		var value = objElem.value;
		var indexId = value.indexOf("~~");
		if(indexId != -1){
			text = value.substring(0,indexId);
			value = value.substring(indexId+2);
			value_id = value;
			objElem.value = text;
		}
		checkdate(objElem);// checkDate

		var objFrm = document.forms["frmGlucoma"];
		var id = cdOd = cdOs = "";
		if(trim(value) != "")
		{
			var objHdFrm = document.forms["frmCd".concat(value)];
			if(objHdFrm != null){
			id = objHdFrm.elem_idCd.value;
			cdOd = objHdFrm.elem_cdOd.value;
			cdOs = objHdFrm.elem_cdOs.value;
			}
		}

		objFrm.elem_idCd.value = value_id;
		objFrm.elem_cdOd.value = disc_app_dos_data_arr[value_id].cd_od;
		objFrm.elem_cdOs.value = disc_app_dos_data_arr[value_id].cd_os;
	}

    function displayCee(objElem)
    {
        var value = objElem.value;
        var indexId = value.indexOf("~~");
        if(indexId != -1){
            text = value.substring(0,indexId);
            value = value.substring(indexId+2);
            objElem.value = text;
        }
        checkdate(objElem);// checkDate

        var objFrm = document.forms["frmGlucoma"];
        var id = cee = notes = "";
        if(trim(value) != "")
        {
            var objHdFrm = document.forms["frmCEE".concat(value)];
            if(objHdFrm != null){
            id = objHdFrm.elem_idCee.value;
            cee = objHdFrm.elem_cee.value;
	    notes= objHdFrm.elem_ceeNotes.value;
            }
        }
        objFrm.elem_idCee.value = id;
        objFrm.elem_cee.value = cee;
	objFrm.elem_ceeNotes.value = notes;
    }

   function clearText(objElem)
   {
	if(objElem.value == "Notes:")
	{
		objElem.value="";
	}
	else if(objElem.value.length > 0)
	{
		objElem.select();
	}
   }

   //Toggle Other DD
   function toggleOther(id,flag){
		var oelem = gebi("elem_"+id);
		var odiv = gebi("div_"+id+"Other");
		var odivElem = gebi("elem_"+id+"Other");

		if(oelem && odiv){
			if(flag == 1 && oelem.value == "Other"){ //Other Display,hide Elem
				oelem.style.display = "none";
				odiv.style.display = "block";
				odivElem.value=""+odivElem.defaultValue;
				odivElem.select();

			}else{
				oelem.style.display = "block";
				odiv.style.display = "none";
				odivElem.value="";
			}
		}
   }

//--

$(document).ready(function () {
	var jquery_date_format = 'm-d-Y';
	$('.date-pick').datetimepicker({timepicker:false,format:jquery_date_format,autoclose: true,scrollInput:false});
	$("#vf_glucoma_lbl").bind('click',function(){
		show_vf_gl_synthesis('show');
	});
	$("#rnfl_glucoma_lbl").bind('click',function(){
		show_rnfl_gl_synthesis('show');
	});

	var glucoma_dx_menu = JSON.parse('<?php echo $glucoma_dx_menu_arr_str; ?>');
	$('#elem_diagnosisOd, #elem_diagnosisOs').each(function(index, element){
		$(element).typeahead({source:glucoma_dx_menu});
	});

	//gonio drawing
	set_gonio_draw_btn();

});

function show_vf_gl_synthesis(show_fn){
	if(show_fn == "show"){
		$("#vf_gl_synthesis_popup").css({'display':'block'});
	}
	else{
		$("#vf_gl_synthesis_popup").css({'display':'none'});
	}
}

function show_rnfl_gl_synthesis(show_fn){
	if(show_fn == "show"){
		$("#rnfl_gl_synthesis_popup").css({'display':'block'});
	}
	else{
		$("#rnfl_gl_synthesis_popup").css({'display':'none'});
	}
}

function showStagingCodePopUp(){
	top.staging_div_view('show');
}

//var ta_od_dos_data_arr = "";
function changeTaOd(objElem){
	value_id ="";
	var value = objElem.value;
	var indexId = value.indexOf("~~");
	if(indexId != -1){
		text = value.substring(0,indexId);
		value = value.substring(indexId+2);
		value_id = value;
		objElem.value = text;
	}
	checkdate(objElem);// checkDate

	var objFrm = document.forms["frmGlucoma"];
	objFrm.elem_dateHighTaOd.value = ta_od_dos_data_arr[value_id].date;
	objFrm.elem_highTaOdOd.value = ta_od_dos_data_arr[value_id].taOd;
	objFrm.elem_highTaOsOs.value = ta_os_dos_data_arr[value_id].taOs;
	objFrm.elem_highTaTime.value = ta_os_dos_data_arr[value_id].time;
}

//var ta_os_dos_data_arr = "";
function changeTaOs(objElem){
	value_id ="";
	var value = objElem.value;
	var indexId = value.indexOf("~~");
	if(indexId != -1){
		text = value.substring(0,indexId);
		value = value.substring(indexId+2);
		value_id = value;
		objElem.value = text;
	}
	checkdate(objElem);// checkDate

	var objFrm = document.forms["frmGlucoma"];
	objFrm.elem_dateHighTaOs.value = ta_os_dos_data_arr[value_id].date;
	objFrm.elem_highTaOsOs.value = ta_os_dos_data_arr[value_id].taOs;
}

//var tx_od_dos_data_arr = "";
function changeTxOd(objElem)
{
	value_id ="";
	var value = objElem.value;
	var indexId = value.indexOf("~~");
	if(indexId != -1){
		text = value.substring(0,indexId);
		value = value.substring(indexId+2);
		value_id = value;
		objElem.value = text;
	}
	checkdate(objElem);// checkDate

	var objFrm = document.forms["frmGlucoma"];
	objFrm.elem_dateHighTxOd.value = tx_od_dos_data_arr[value_id].date;
	objFrm.elem_highTxOdOd.value = tx_od_dos_data_arr[value_id].txOd;
	objFrm.elem_highTxOsOs.value = tx_os_dos_data_arr[value_id].txOs;
	objFrm.elem_highTxTime.value = tx_os_dos_data_arr[value_id].time;
}

//var tx_os_dos_data_arr = "";
function changeTxOs(objElem)
{
	value_id ="";
	var value = objElem.value;
	var indexId = value.indexOf("~~");
	if(indexId != -1){
		text = value.substring(0,indexId);
		value = value.substring(indexId+2);
		value_id = value;
		objElem.value = text;
	}
	checkdate(objElem);// checkDate

	var objFrm = document.forms["frmGlucoma"];
	objFrm.elem_dateHighTxOs.value = tx_os_dos_data_arr[value_id].date;
	objFrm.elem_highTxOsOs.value = tx_os_dos_data_arr[value_id].txOs;
}

//var tt_od_dos_data_arr = "";
function changeTtOd(objElem)
{
	value_id ="";
	var value = objElem.value;
	var indexId = value.indexOf("~~");
	if(indexId != -1){
		text = value.substring(0,indexId);
		value = value.substring(indexId+2);
		value_id = value;
		objElem.value = text;
	}
	checkdate(objElem);// checkDate

	var objFrm = document.forms["frmGlucoma"];
	objFrm.elem_dateHighTtOd.value = tt_od_dos_data_arr[value_id].date;
	objFrm.elem_highTtOdOd.value = tt_od_dos_data_arr[value_id].ttOd;
	objFrm.elem_highTtOsOs.value = tt_os_dos_data_arr[value_id].ttOs;
	objFrm.elem_highTtTime.value = tt_os_dos_data_arr[value_id].time;
}

//var tt_os_dos_data_arr = "";
function changeTtOs(objElem)
{
	value_id ="";
	var value = objElem.value;
	var indexId = value.indexOf("~~");
	if(indexId != -1){
		text = value.substring(0,indexId);
		value = value.substring(indexId+2);
		value_id = value;
		objElem.value = text;
	}
	checkdate(objElem);// checkDate

	var objFrm = document.forms["frmGlucoma"];
	objFrm.elem_dateHighTtOs.value = tt_os_dos_data_arr[value_id].date;
	objFrm.elem_highTtOsOs.value = tt_os_dos_data_arr[value_id].ttOs;
}
</script>
<style>
	#elem_riskFactors_td .dropdown-menu{right:0;left:auto}
</style>
<body style="background:#fff">
<div class="row">
	<form name="frmGlucoma" action="" method="post">
		<input type="hidden" name="elem_dateActivation" value="<?php echo $elem_dateActivation;?>" > <!--onchange="this.form.submit();">-->
		<input type="hidden" name="elem_glucomaId" value="<?php echo $elem_glucomaId;?>">
		<input type="hidden" name="elem_activate" value="<?php echo $elem_activate;?>">
		<input type="hidden" name="elem_idDiagnosis" value="<?php echo $elem_idHighTaOd;?>">
		<input type="hidden" name="elem_idPachy" value="<?php echo $elem_idPachy;?>">
    <div class="col-sm-12">
    <div class="row-flex">
		<div class="col-xs-8">
			<div class="row whtbox">
				<table class="table table-bordered table-striped" style="table-layout:fixed">
					<tr>
						<th style="width:30%">Description</th>
						<th style="width:35%" class="text-center od">OD</th>
						<th style="width:35%" class="text-center os">OS</th>
					</tr>
					<tr>
						<th colspan="3">Diagnosis</th>
					</tr>
					<tr>
						<td>
							<textarea class="form-control" name="diagnosis_description" id="diagnosis_description" rows="2"><?php echo $dxDescription; ?></textarea>
						</td>
						<td align="center">
							 <textarea name="elem_diagnosisOd" id="elem_diagnosisOd" class="form-control" rows="2" data-provide="multiple" data-seperator="newline"><?php echo $elem_diagnosisOd; ?></textarea>
						</td>
						<td align="center">
							<textarea name="elem_diagnosisOs" id="elem_diagnosisOs" class="form-control" rows="2" data-provide="multiple" data-seperator="newline"><?php echo $elem_diagnosisOs; ?></textarea>
						</td>
					</tr>
					<tr>
						<td class="pointer text_purple text-right" onClick="window.top.$('#staging_code_info').modal('show')">Staging code</td>
						<?php
							$staging_code_values = array("mild","moderate","severe","indeterminate","unspecified");
							$elem_stagingCode_od = str_ireplace("intermediate","indeterminate",$elem_stagingCode_od);
							$elem_stagingCode_os = str_ireplace("intermediate","indeterminate",$elem_stagingCode_os);
							$elem_stagingCode_od_arr = explode(',',strtolower($elem_stagingCode_od));
							$elem_stagingCode_os_arr = explode(',',strtolower($elem_stagingCode_os));
						?>
						<td align="center">
							<select id="staging_code_od" multiple name="staging_code_od" class="selectpicker" data-width="100%" data-title="Select Options" data-max-options="1">
								<?php
									$selectedOtherUnspecified = 0;
									foreach($staging_code_values as $sc_value)
									{
										$sel = '';
										if(in_array($sc_value,$elem_stagingCode_od_arr))
										{
											$selectedOtherUnspecified = 1;
											$sel = ' selected = "selected" ';
										}
										if($selectedOtherUnspecified == 0 && $sc_value == "unspecified")
										{
											$sel = ' selected = "selected" ';
										}
										echo '<option '.$sel.' value="'.$sc_value.'">'.ucfirst($sc_value).'</option>';
									}
								?>
							</select>
						</td>
						<td align="center">
							<select id="staging_code_os" multiple name="staging_code_os" class="selectpicker" data-width="100%" data-title="Select Options" data-max-options="1">
							<?php
								$selectedOtherUnspecified = 0;
								foreach($staging_code_values as $sc_value)
								{
									$sel = '';
									if(in_array($sc_value,$elem_stagingCode_os_arr))
									{
										$selectedOtherUnspecified = 1;
										$sel = ' selected = "selected" ';
									}
									if($selectedOtherUnspecified == 0 && $sc_value == "unspecified")
									{
										$sel = ' selected = "selected" ';
									}
									echo '<option '.$sel.' value="'.$sc_value.'">'.ucfirst($sc_value).'</option>';
								}
							?>
							</select>
						</td>
					</tr>
					<tr>
						<td align="right">T<sub>MAX</sub></td>
						<td>
							<div class="row">
								<div class="col-xs-9">
									<div class="input-group">
										<input type="text" name="elem_dateHighTmaxOd" id="elem_dateHighTmaxOd" class="form-control date-pick" maxlength="10" value="<?php echo $elem_dateHighTmaxOd; ?>">
										<label for="elem_dateHighTmaxOd" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>
									</div>
								</div>
								<div class="col-xs-3">
									<input type="text" class="form-control" name="elem_highTmaxOdOd" id="elem_highTmaxOdOd" value="<?php echo $elem_highTmaxOdOd; ?>" />
								</div>
							</div>
						</td>
						<td>
							<div class="row">
								<div class="col-xs-9">
									<div class="input-group">
										<input type='text' name="elem_dateHighTmaxOs" id="elem_dateHighTmaxOs" class="form-control date-pick" maxlength="10" value="<?php echo $elem_dateHighTmaxOs; ?>">
										<label for="elem_dateHighTmaxOs" class="input-group-addon pointer">
											<span class="glyphicon glyphicon-calendar"></span>
										</label>
									</div>
								</div>
								<div class="col-xs-3">
									<input type="text" class="form-control" name="elem_highTmaxOsOs" id="elem_highTmaxOsOs" value="<?php echo $elem_highTmaxOsOs; ?>" />
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th colspan="3">Pachy</th>
					</tr>
					<tr>
						<td >
							<?php
								$arrDatePachy = array();
								$elem_datePachy = "";
								$pachy_default_id = "";
								foreach($pachy_dos_data_arr as $pachy_val_id => $pachy_data_val_arr)
								{
									if($elem_datePachy == ""){ $elem_datePachy = $pachy_data_val_arr["date"]; $pachy_default_id = $pachy_val_id;}
									$arrDatePachy[$pachy_val_id] = array($pachy_data_val_arr["date"],$arrEmpty,$pachy_data_val_arr["date"]."~~".$pachy_val_id);
								}
							?>
							<div class="input-group">
								<input type="text" id="elem_datePachy" name="elem_datePachy" value="<?php echo $elem_datePachy; ?>" class="form-control" onchange="displayPachy(this);">
								<?php echo get_simple_menu($arrDatePachy,"menu_elem_datePachy","elem_datePachy","-58"); ?>
							</div>
						</td>
						<td>
							<?php
								$disAvgOd = (!empty($elem_pachyOdAvg)) ? "block" : "none";
								$disAvgOs = (!empty($elem_pachyOsAvg)) ? "block" : "none";
								//$disAvgOd = $disAvgOs = "block";

								$elem_pachyOdReads = $pachy_dos_data_arr[$pachy_default_id]["od_readings"];
								$elem_pachyOdCorr = $pachy_dos_data_arr[$pachy_default_id]["od_correction"];
								$elem_pachyOsReads = $pachy_dos_data_arr[$pachy_default_id]["os_readings"];
								$elem_pachyOsCorr = $pachy_dos_data_arr[$pachy_default_id]["os_correction"];

								$pachy_od_calc = str_replace(',','',$elem_pachyOdReads);
								$pachy_od_status = '';
								if($pachy_od_calc != 0){
									if($pachy_od_calc < 555)
									{
										$pachy_od_status = 'Thin';
									}
									else if($pachy_od_calc >= 555 && $pachy_od_calc <= 588)
									{
										$pachy_od_status = 'Average';
									}
									else if($pachy_od_calc > 588)
									{
										$pachy_od_status = 'Thick';
									}
								}
							?>
							<div class="row">
								<div class="col-xs-4">
									<input type="text" class="form-control" name="elem_pachyOdReads" value="<?php echo $elem_pachyOdReads;?>" onBlur="calCorrectionVal(this.value,'OD');">
								</div>
								<div class="col-xs-4" id="td_pachy_od_avg" style="display:<?php echo $disAvgOd;?>">
									<input type="text" class="form-control" id="elem_pachyOdAvg" name="elem_pachyOdAvg" value="<?php echo $elem_pachyOdAvg;?>">
								</div>
								<div class="col-xs-4">
									<input type="hidden" class="form-control" name="elem_pachyOdCorr" value="<?php echo $elem_pachyOdCorr;?>">
									<input type="text" class="form-control" name="pachy_dos_od_status" id="pachy_dos_od_status" value="<?php echo $pachy_od_status;?>">
								</div>
							</div>
						</td>
						<td>
							<div class="row">
								<div class="col-xs-4">
									<input type="text" class="form-control" name="elem_pachyOsReads" value="<?php echo $elem_pachyOsReads;?>" onBlur="calCorrectionVal(this.value,'OS');">
								</div>
								<div class="col-xs-4" id="td_pachy_os_avg" style="display:<?php echo $disAvgOs;?>">
									<input type="text" class="form-control" name="elem_pachyOsAvg" value="<?php echo $elem_pachyOsAvg;?>">
								</div>
								<div class="col-xs-4">
									<?php
										$pachy_os_calc = $elem_pachyOsReads;
										$pachy_os_status = '';
										if($pachy_os_calc != 0)
										{
											if($pachy_os_calc < 555)
											{
												$pachy_os_status = 'Thin';
											}
											else if($pachy_os_calc >= 555 && $pachy_os_calc <= 588)
											{
												$pachy_os_status = 'Average';
											}
											else if($pachy_os_calc > 588)
											{
												$pachy_os_status = 'Thick';
											}
										}
									?>
									<input type="hidden" name="elem_pachyOsCorr" value="<?php echo $elem_pachyOsCorr;?>">
									<input type="text" class="form-control" name="pachy_dos_os_status" id="pachy_dos_os_status" value="<?php echo $pachy_os_status;?>">
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<th colspan="3">
							<input type="hidden" name="elem_idVf" value="<?php echo $elem_idVf;?>">
							<label id="vf_glucoma_lbl" class="text_purple pointer" onClick="window.top.$('#vf_gl_synthesis_popup').modal('show')">VF</label>
						</th>
					</tr>
					<tr>
						<td>
							<?php
								//Set VF
								if($elem_vfOdSummary == "Empty"){
									$elem_vfOdSummary = "";
								}
								if($elem_vfOsSummary == "Empty"){
									$elem_vfOsSummary = "";
								}

								//if(empty($elem_dateVf)|| empty($elem_glucomaId))
								if((empty($elem_vfOdSummary) && empty($elem_vfOsSummary)) || empty($elem_glucomaId) || (empty($arrInitialTop["VF"][0]["new"])))
								{
									if(isset($arrInitialTop["VF"][0])){
										$elem_dateVf = $arrInitialTop["VF"][0]["date"];
										$elem_vfOdSummary = $arrInitialTop["VF"][0]["od"];
										$elem_vfOsSummary = $arrInitialTop["VF"][0]["os"];
									}
								}

								/* Getting the list of DOS for the test VF-GL */
								$arrDateVf = array();
								$elem_dateVf = "";
								$vf_default_id = "";
								foreach($vf_gl_dos_arr as $cur_vf_id => $cur_vf_gl_dos_arr)
								{
									if($elem_dateVf == "") { $elem_dateVf = $cur_vf_gl_dos_arr["date"]; $vf_default_id = $cur_vf_id; }
									$arrDateVf[$cur_vf_id] = array($cur_vf_gl_dos_arr["date"],$arrEmpty,$cur_vf_gl_dos_arr["date"].'~~'.$cur_vf_id);
								}
							?>
							<div class="input-group">
								<input type="text" id="elem_dateVf" name="elem_dateVf" value="<?php echo $elem_dateVf; ?>" class="form-control" onchange="displayVf(this);" >
								<?php echo get_simple_menu($arrDateVf,"menu_elem_dateVf","elem_dateVf","-79"); ?>
							</div>
						</td>
						<td>
							<?php
								if(empty($elem_vfOdSummary) ||
								$elem_vfOdSummary == "Normal" || $elem_vfOdSummary == "Border Line" ||
								$elem_vfOdSummary == "PS" || $elem_vfOdSummary == "Abnormal" ||
								$elem_vfOdSummary == "Increase Abnormal" || $elem_vfOdSummary == "Decrease Abnormal" ||
								$elem_vfOdSummary == "No Change Abnormal" || $elem_vfOdSummary == "Stable" ){
									$dis_elem = "block";
									$dis_div = "none";
								}else{
									$dis_elem = "none";
									$dis_div = "none";
								}

							?>
							<div id="div_vfOdSummaryOther" style="display:<?php echo $dis_div;?>;">
								<div class="input-group">
									<input type="text" name="elem_vfOdSummaryOther" value="<?php echo $elem_vfOdSummary;?>" class="form-control">
									<label class="input-group-addon pointer">
										<span class="spnFuDel" onClick="toggleOther('vfOdSummary','0')"></span>
									</label>
								</div>
							</div>

							<div class="input-group">
								<textarea name="elem_vfOdSummary" id="elem_vfOdSummary" rows="1" class="form-control"/><?php echo $vf_gl_dos_arr[$vf_default_id]["od"]; ?></textarea>
								<label class="input-group-addon pointer" onClick="window.top.$('#vf_gl_abbreviations').modal('show')">
									<span class="abbreviation_tooltip glyphicon glyphicon-question-sign"></span>
								</label>
							</div>
						</td>
						<td>
							<?php
								if(empty($elem_vfOsSummary) ||
								$elem_vfOsSummary == "Normal" || $elem_vfOsSummary == "Border Line" ||
								$elem_vfOsSummary == "PS" || $elem_vfOsSummary == "Abnormal" ||
								$elem_vfOsSummary == "Increase Abnormal" || $elem_vfOsSummary == "Decrease Abnormal" ||
								$elem_vfOsSummary == "No Change Abnormal" || $elem_vfOsSummary == "Stable" ){
									$dis_elem = "block";
									$dis_div = "none";
								}else{
									$dis_elem = "none";
									$dis_div = "none";
								}
							?>
							<div id="div_vfOsSummaryOther" style="display:<?php echo $dis_div;?>">
								<div class="input-group">
									<input type="text" name="elem_vfOsSummaryOther" value="<?php echo $elem_vfOsSummary;?>" class="form-control">
									<label class="input-group-addon pointer">
										<span class="spnFuDel" onClick="toggleOther('vfOsSummary','0')"></span>
									</label>
								</div>
							</div>

							<div class="input-group">
								<textarea name="elem_vfOsSummary" id="elem_vfOsSummary" rows="1" class="form-control"/><?php echo $vf_gl_dos_arr[$vf_default_id]["os"]; ?></textarea>
								<label class="input-group-addon pointer" onClick="window.top.$('#vf_gl_abbreviations').modal('show')">
									<span class="abbreviation_tooltip glyphicon glyphicon-question-sign"></span>
								</label>
							</div>
						</td>
					</tr>
					<tr>
						<th colspan="3">
							<input type="hidden" name="elem_idNfa" value="<?php echo $elem_idNfa;?>">
							<label id="rnfl_glucoma_lbl" class="text_purple pointer" onClick="window.top.$('#rnfl_gl_synthesis_popup').modal('show')">OCT-RNFL</label>
						</th>
					</tr>
					<tr>
						<td>
							<?php
								//Set NFA
								if($elem_nfaOdSummary == "Empty"){
									$elem_nfaOdSummary = "";
								}
								if($elem_nfaOsSummary == "Empty"){
									$elem_nfaOsSummary = "";
								}
								//if(empty($elem_dateNfa)|| empty($elem_glucomaId))
								if((empty($elem_nfaOdSummary) && empty($elem_nfaOsSummary))|| empty($elem_glucomaId) || empty($arrInitialTop["NFA"][0]["new"]))
								{
									if(isset($arrInitialTop["NFA"][0])){
										$elem_dateNfa = $arrInitialTop["NFA"][0]["date"];
										$elem_nfaOdSummary = $arrInitialTop["NFA"][0]["od"];
										$elem_nfaOsSummary = $arrInitialTop["NFA"][0]["os"];
									}
								}

								$arrDateNfa = array();
								$elem_dateNfa = "";
								$nfa_default_id = "";

								foreach($rnfl_dos_arr as $cur_rnfl_id => $cur_rnfl_dos_arr)
								{
									if($elem_dateNfa == "") { $elem_dateNfa = $cur_rnfl_dos_arr["date"]; $nfa_default_id = $cur_rnfl_id; }
									$arrDateNfa[$cur_rnfl_id] = array($cur_rnfl_dos_arr["date"],$arrEmpty,$cur_rnfl_dos_arr["date"].'~~'.$cur_rnfl_id);
								}
							?>
							<div class="input-group">
								<input type="text" name="elem_dateNfa" value="<?php echo $elem_dateNfa; ?>" class="form-control" onchange="displayNfa(this);" >
								<?php echo get_simple_menu($arrDateNfa,"menu_elem_dateNfa","elem_dateNfa","-58"); ?>
							</div>
						</td>
						<td>
							<?php
								if(empty($elem_nfaOdSummary) ||
								$elem_nfaOdSummary == "Normal" || $elem_nfaOdSummary == "Border Line" ||
								$elem_nfaOdSummary == "PS" || $elem_nfaOdSummary == "Abnormal" ||
								$elem_nfaOdSummary == "Increase Abnormal" || $elem_nfaOdSummary == "Decrease Abnormal" ||
								$elem_nfaOdSummary == "No Change Abnormal" || $elem_nfaOdSummary == "Stable" ){
									$dis_elem = "block";
									$dis_div = "none";
								}else{
									$dis_elem = "none";
									$dis_div = "none";
								}
							?>
							<div id="div_nfaOdSummaryOther" style="display:<?php echo $dis_div;?>">
								<div class="input-group">
									<input type="text" name="elem_nfaOdSummaryOther" value="<?php echo $elem_nfaOdSummary;?>" class="form-control">
									<label class="input-group-addon pointer">
										<span class="spnFuDel" onClick="toggleOther('nfaOdSummary','0')"></span>
									</label>
								</div>
							</div>
							<textarea name="elem_nfaOdSummary" id="elem_nfaOdSummary" rows="1" class="form-control"/><?php echo $rnfl_dos_arr[$nfa_default_id]["od"]; ?></textarea>
						</td>
						<td>
							<?php
								if(empty($elem_nfaOsSummary) ||
								$elem_nfaOsSummary == "Normal" || $elem_nfaOsSummary == "Border Line" ||
								$elem_nfaOsSummary == "PS" || $elem_nfaOsSummary == "Abnormal" ||
								$elem_nfaOsSummary == "Increase Abnormal" || $elem_nfaOsSummary == "Decrease Abnormal" ||
								$elem_nfaOsSummary == "No Change Abnormal" || $elem_nfaOsSummary == "Stable" ){
									$dis_elem = "block";
									$dis_div = "none";
								}else{
									$dis_elem = "none";
									$dis_div = "none";
								}
							?>
							<div id="div_nfaOsSummaryOther" style="display:<?php echo $dis_div;?>">
								<div class="input-group">
									<input type="text" name="elem_nfaOsSummaryOther" value="<?php echo $elem_nfaOsSummary;?>" class="form-control">
									<label class="input-group-addon pointer">
										<span class="spnFuDel" onClick="toggleOther('nfaOsSummary','0')"></span>
									</label>
								</div>
							</div>
							<textarea name="elem_nfaOsSummary" id="elem_nfaOsSummary" rows="1" class="form-control"/><?php echo $rnfl_dos_arr[$nfa_default_id]["os"]; ?></textarea>
						</td>
					</tr>
					<tr>

						<th >Gonio</th>
						<th colspan="2"><strong><span id="btn_gonio_warn" class="label label-default" >Drawing</strong></h5></th>
					</tr>
					<tr>
						<td>
							<?php
								$arrDateGonio = array();
								$elem_dateGonio = "";
								$elem_idGonio = "";
								foreach($gonio_dos_data_arr as $gonio_tb_id => $gonio_cur_data_arr)
								{
									if($elem_dateGonio == ""){ $elem_dateGonio = $gonio_cur_data_arr["date"]; $elem_idGonio = $gonio_tb_id; }
									$arrDateGonio[$gonio_tb_id] = array($gonio_cur_data_arr["date"],$arrEmpty,$gonio_cur_data_arr["date"].'~~'.$gonio_tb_id);
								}
							?>
							<div class="input-group">
								<input type="text" id="elem_dateGonio" name="elem_dateGonio" value="<?php echo $elem_dateGonio; ?>" class="form-control" onchange="displayGonio(this);">
								<?php echo get_simple_menu($arrDateGonio,"menu_elem_dateGonio","elem_dateGonio","-58"); ?>
								<input type="hidden" name="elem_idGonio" value="<?php echo $elem_idGonio;?>">
							</div>
						</td>
						<td>
							<textarea name="elem_gonioOd" id="elem_gonioOd" rows="1" class="form-control"/><?php echo $gonio_dos_data_arr[$elem_idGonio]["od_summary"]; ?></textarea>
							<!--
							<select class="selectpicker" name="elem_gonioOd" data-width="100%" data-title="Select">
								<option value="Done" <?php //echo ($gonio_dos_data_arr[$elem_idGonio]["od_summary"] != "")? "selected" : "";?>>Done</option>
								<option value="Not Done" <?php //echo ($gonio_dos_data_arr[$elem_idGonio]["od_summary"] == "")? "selected" : "";?>>Not Done</option>
							</select>
							-->
						</td>
						<td>
							<textarea name="elem_gonioOs" id="elem_gonioOs" rows="1" class="form-control"/><?php echo $gonio_dos_data_arr[$elem_idGonio]["os_summary"]; ?></textarea>
							<!--
							<select class="selectpicker" name="elem_gonioOs" data-width="100%" data-title="Select">
								<option value="" <?php //echo ($elem_gonioOs == "")? "selected" : "";?>></option>
								<option value="Done" <?php //echo ($gonio_dos_data_arr[$elem_idGonio]["os_summary"] != "")? "selected" : "";?>>Done</option>
								<option value="Not Done" <?php //echo ($gonio_dos_data_arr[$elem_idGonio]["os_summary"] == "")? "selected" : "";?>>Not Done</option>
							</select>
							-->
						</td>
					</tr>
					<tr>
						<th colspan="3">
							<input type="hidden" name="elem_idCd" value="<?php echo $elem_idCd;?>">
							<label>Disc Appearance</label>
						</th>
					</tr>
					<tr>
						<td>
							<?php
								$arrDateCD = array();
								$elem_dateCd = "";
								$cd_default_id = "";
								foreach($disc_app_data_arr as $disc_app_id => $disc_app_arr_val){
									if($elem_dateCd == ""){ $elem_dateCd = $disc_app_arr_val["date"]; $cd_default_id = $disc_app_id; }
									$arrDateCD[$disc_app_id] = array($disc_app_arr_val["date"],$arrEmpty,$disc_app_arr_val["date"].'~~'.$disc_app_id);
								}
							?>
							<div class="input-group">
								<input type="text" id="elem_dateCd" name="elem_dateCd" value="<?php echo $elem_dateCd; ?>" class="form-control" onchange="displayCD(this);" >
								<?php echo get_simple_menu($arrDateCD,"menu_elem_dateCd","elem_dateCd","-58"); ?>
							</div>
						</td>
						<td>
							<input type="text" name="elem_cdOd" id="elem_cdOd" class="form-control" value="<?php echo $disc_app_data_arr[$cd_default_id]["cd_od"]; ?>" title="<?php echo $disc_app_data_arr[$cd_default_id]["optic_od"]; ?>" />
						</td>
						<td>
							<input type="text" name="elem_cdOs" id="elem_cdOs" class="form-control" value="<?php echo $disc_app_data_arr[$cd_default_id]["cd_os"]; ?>" title="<?php echo $disc_app_data_arr[$cd_default_id]["optic_os"]; ?>" />
						</td>

						<input type="hidden" name="sig_datacd_app_od_1"  id="sig_datacd_app_od_1" value="0" />
						<input type="hidden" name="sig_datacd_app_od"  id="sig_datacd_app_od" />
						<input type="hidden" name="sig_imgcd_app_od"  id="sig_imgcd_app_od" value="<?php echo $sig_imgcd_app_od;?>" />

						<input type="hidden" name="sig_datacd_app_os_1"  id="sig_datacd_app_os_1" value="0"/>
						<input type="hidden" name="sig_datacd_app_os"  id="sig_datacd_app_os" />
						<input type="hidden" name="sig_imgcd_app_os"  id="sig_imgcd_app_os" value="<?php echo $sig_imgcd_app_os;?>" />

						<input type="hidden" name="elem_cd_app_od" value="<?php echo $elem_cd_app_od;?>">
						<input type="hidden" name="elem_cd_app_os" value="<?php echo $elem_cd_app_os;?>">
					</tr>

					<?php
					//Show scan images
						$disc_photo_qry = "SELECT scan_id, DATE_FORMAT(modi_date,'".get_sql_date_format()."') as modi_date, image_name, file_path FROM ".IMEDIC_SCAN_DB.".scans INNER JOIN ".$GLOBALS['dbase'].".disc ON ".$GLOBALS['dbase'].".disc.disc_id = ".IMEDIC_SCAN_DB.".scans.test_id WHERE ".IMEDIC_SCAN_DB.".scans.patient_id = '".$glaucoma_obj->pid."' and LOWER(".IMEDIC_SCAN_DB.".scans.image_form) = 'disc' order by ".IMEDIC_SCAN_DB.".scans.scan_id DESC LIMIT 2";
						$disc_photo_qry_obj = imw_query($disc_photo_qry);
						if(imw_num_rows($disc_photo_qry_obj) > 0){ ?>
							<tr>
								<td colspan="3">
									<table class="table table-striped table-bordered" style="table-layout:fixed">
										<tr>
										<?php
											while($disc_photo_data_arr = imw_fetch_assoc($disc_photo_qry_obj)){
												$disc_img_file_path = $disc_photo_data_arr['file_path'];
												$disc_modi_date = $disc_photo_data_arr['modi_date'];
                                                $extension = pathinfo($disc_img_file_path, PATHINFO_EXTENSION);
                                                if(isset($extension) && $extension == 'pdf') {
                                                    $img_src = $GLOBALS['webroot']."/library/images/test_pdf_Icon.png";
                                                } else {
                                                    $img_src = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$disc_img_file_path;
                                                }
												?>
												<td>
													<div style="float:left;padding:5px 5px 5px 5px;cursor:pointer;">
														<a onclick="show_img_on_demand(this);" data-type="<?php echo $extension; ?>" data-src="<?php echo $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$disc_img_file_path; ?>">
															<img src="<?php echo $img_src; ?>" alt="" width="80" height="45" style="border:none;text-align:left;" />
															<br/>
															<?php echo $disc_modi_date; ?>
														</a>
													</div>
												</td>
												<?php
											}
										?>
										</tr>
									</table>
								</td>
							</tr>
					<?php
						}
					?>
					<tr>
						<td colspan="3">
							<label>Notes:</label>
							<textarea name="elem_notes" rows="3" class="form-control" onFocus="clearText(this)" ><?php echo (!empty($elem_notes)) ? $elem_notes : "Notes:";?></textarea>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="col-xs-4">
			<?php
				$ocular_pt_qry = "SELECT any_conditions_relative FROM ocular WHERE patient_id = '".$glaucoma_obj->pid."'";
				$ocular_pt_qry_obj = imw_query($ocular_pt_qry);
				$ocular_pt_qry_data = imw_fetch_assoc($ocular_pt_qry_obj);
				$any_conditions_relative_arr = explode(",",$ocular_pt_qry_data["any_conditions_relative"]);
				for($epr2 = 1; $epr2 < count($any_conditions_relative_arr)-1; $epr2++){
					$acra_p[$any_conditions_relative_arr[$epr2]]="checked";
				}
				if(strpos($elem_riskFactors,"Family History") !== false){
					$acra_p[3]="checked";
				}
			?>
			<div class="rskfact">
				<h2>Risk Factor</h2>
				<div class="clearfix"></div>
				<ul>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_riskFactors_family_history" name="elem_riskFactors" value="Family History" vspace="0" <?php print $acra_p[3]; ?>>
							<label for="elem_riskFactors_family_history">Family Hx</label>
						</div>
					</li>
					<li id="elem_riskFactors_td">
						<label>Race</label>
						<?php
							$arrRace = array(
								"Race-American Indian or Alaska Native" => "American Indian or Alaska Native",
								"Race-Asian" => "Asian",
								"Race-Black or African American" => "Black or African American",
								"Race-Native Hawaiian or Other Pacific Islander" => "Native Hawaiian or Other Pacific Islander",
								"Race-Latin American" => "Latin American",
								"Race-White" => "White"
							);

							if(strpos($elem_riskFactors,'Race-') === false){
								$race_pt_qry = "SELECT race FROM patient_data WHERE id = '".$glaucoma_obj->pid."'";
								$race_pt_qry_obj = imw_query($race_pt_qry);
								$race_pt_qry_data = imw_fetch_assoc($race_pt_qry_obj);
								$elem_riskFactors .= $race_pt_qry_data["race"];
							}
							foreach($arrRace as $arrRaceKey => $arrRace_val){
								$selOpt = "";
								if(strpos($elem_riskFactors,trim($arrRace_val)) !== false){
									$selOpt = ' selected ="selected" ';
								}
								$raceOpts .= '<option value="'.$arrRaceKey.'" '.$selOpt.'>'.$arrRace_val.'</option>';
							}

						?>
						<select name="elem_riskFactors_race" id="elem_riskFactors_race" class="selectpicker" multiple="multiple" data-width="100%" data-title="Select">
							<?php echo $raceOpts; ?>
						</select>
					</li>
					<li>
						<?php
							//any conditions you
							$general_pt_qry = "SELECT any_conditions_you,review_card,review_resp FROM general_medicine WHERE patient_id = '".$glaucoma_obj->pid."'";
							$general_pt_qry_obj = imw_query($general_pt_qry);
							$general_pt_qry_data = imw_fetch_assoc($general_pt_qry_obj);
							$any_conditions_u1_arr = explode(",",$general_pt_qry_data["any_conditions_you"]);
							foreach($any_conditions_u1_arr as $key => $val){
								if($val != ""){
									$acya_p1[$val] = "checked";
								}
							}
							if(strpos($elem_riskFactors,"Diabetes") !== false){
								$acya_p1[3]="checked";
							}
						?>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_riskFactors_diabetes" name="elem_riskFactors" value="Diabetes" vspace="0" <?php print $acya_p1[3]; ?>>
							<label for="elem_riskFactors_diabetes">Diabetes</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_riskFactors_pxf" name="elem_riskFactors" value="PXF" vspace="0" <?php echo(strpos($elem_riskFactors,"PXF") !== false)? "checked" : "" ;?>>
							<label for="elem_riskFactors_pxf">PXF</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_riskFactors_st_res" name="elem_riskFactors" value="Steroid Responder" vspace="0" <?php echo(strpos($elem_riskFactors,"Steroid Responder") !== false)? "checked" : "" ;?>>
							<label for="elem_riskFactors_st_res">Steroid Responder</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_riskFactors_pds" name="elem_riskFactors" value="PDS" vspace="0" <?php echo(strpos($elem_riskFactors,"PDS") !== false)? "checked" : "" ;?>>
							<label for="elem_riskFactors_pds">PDS</label>
						</div>
					</li>
				</ul>
				<h2>Warnings</h2>
				<div class="clearfix"></div>
				 <?php
					//Cardiovascular
					$review_card_arr = explode(",",$general_pt_qry_data["review_card"]);
					foreach($review_card_arr as $key => $val){
						if($val != ""){
							$review_card[$val] = "checked";
						}
					}
					if(strpos($elem_warnings,"Arrhythmia") !== false){
						$review_card[3]="checked";
					}
					if(strpos($elem_warnings,"CHF") !== false){
						$review_card[2]="checked";
					}

					//Respiratory
					$review_resp_arr = explode(",",$general_pt_qry_data["review_resp"]);
					foreach($review_resp_arr as $key => $val){
						if($val != ""){
							$review_resp[$val] = "checked";
						}
					}
					if($review_resp[4]!="checked"){
						$review_resp[4]=$review_resp[6];
					}
					if(strpos($elem_warnings,"Asthma/COPD") !== false){
						$review_resp[4]="checked";
					}

					$sufla_chk="";
					$list_pt_qry = "SELECT id FROM lists WHERE pid = '".$glaucoma_obj->pid."' and ag_occular_drug='fdbATAllergenGroup' and title like'%sulfa%'";
					$list_pt_qry_obj = imw_query($list_pt_qry);
					if(imw_num_rows($list_pt_qry_obj)>0){
						$sufla_chk="checked";
					}
					if(strpos($elem_warnings,"Sulfa Allergy") !== false){
						$sufla_chk="checked";
					}

				?>
				<ul>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_warnings_Arrhythmia" name="elem_warnings" value="Arrhythmia" <?php print $review_card[3]; ?>>
							<label for="elem_warnings_Arrhythmia">Arrhythmia</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_warnings_copd" name="elem_warnings" value="Asthma/COPD" <?php print $review_resp[4]; ?>>
							<label for="elem_warnings_copd">Asthma/COPD</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_warnings_Bradycardia" name="elem_warnings" value="Bradycardia" <?php echo(strpos($elem_warnings,"Bradycardia") !== false)? "checked" : "" ;?>>
							<label for="elem_warnings_Bradycardia">Bradycardia</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_warnings_chf" name="elem_warnings" value="CHF" <?php print $review_card[2]; ?>>
							<label for="elem_warnings_chf">CHF</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_warnings_sul_all" name="elem_warnings" value="Sulfa Allergy" <?php echo $sufla_chk;?>>
							<label for="elem_warnings_sul_all">Sulfa Allergy</label>
						</div>
					</li>
					<li>
						<div class="checkbox checkbox-inline">
							<input type="checkbox" id="elem_warnings_depression" name="elem_warnings" value="Depression" <?php echo(strpos($elem_warnings,"Depression") !== false)? "checked" : "" ;?>>
							<label for="elem_warnings_depression">Depression</label>
						</div>
					</li>
				</ul>
				<h2>IOP Targets</h2>
				<div class="row">
					<div class="col-xs-12">
						<label>Trgt</label>
					</div>
					<div class="col-xs-6 text-center">
						<label>OD</label>
						<input type="text" name="elem_trgtIopOd" value="<?php echo $targetOd;?>" class="form-control">
					</div>
					<div class="col-xs-6 text-center">
						<label>OS</label>
						<input type="text" name="elem_trgtIopOs" value="<?php echo $targetOs;?>" class="form-control">
					</div>
				</div>
			</div>
		</div>
    </div>
    </div>
	</form>
<?php
	//Gonio
	if(!empty($elem_dateGonio))
	{
		echo "<form name=\"frmGonio".$elem_dateGonio."\">";
			echo "<input type=\"hidden\" name=\"elem_idGonio\" value=\"".$elem_idGonio."\">";
			echo "<input type=\"hidden\" name=\"elem_dateGonio\" value=\"".$elem_dateGonio."\">";
			echo "<input type=\"hidden\" name=\"elem_gonioOd\" value=\"".$elem_gonioOd."\">";
			echo "<input type=\"hidden\" name=\"elem_gonioOs\" value=\"".$elem_gonioOs."\">";
		echo "</form>";
	}

	$len = count($arrInitialTop["Gonio"]);
	for($i=0;$i<$len;$i++)
	{
		$id = $arrInitialTop["Gonio"][$i]["id"];
		$date = $arrInitialTop["Gonio"][$i]["date"];
		$od = $arrInitialTop["Gonio"][$i]["od"];
		$os = $arrInitialTop["Gonio"][$i]["os"];
		echo "<form name=\"frmGonio".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_idGonio\" value=\"".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_dateGonio\" value=\"".$date."\">";
			echo "<input type=\"hidden\" name=\"elem_gonioOd\" value=\"".$od."\">";
			echo "<input type=\"hidden\" name=\"elem_gonioOs\" value=\"".$os."\">";
		echo "</form>";
	}

	//CD
	if(!empty($elem_dateCd))
	{
		echo "<form name=\"frmCd".$elem_dateCd."\">";
			echo "<input type=\"hidden\" name=\"elem_idCd\" value=\"".$elem_idCd."\">";
			echo "<input type=\"hidden\" name=\"elem_dateCd\" value=\"".$elem_dateCd."\">";
			echo "<input type=\"hidden\" name=\"elem_cdOd\" value=\"".$elem_cdOd."\">";
			echo "<input type=\"hidden\" name=\"elem_cdOs\" value=\"".$elem_cdOs."\">";
		echo "</form>";
	}
	$len = count($arrInitialTop["CD"]);
	for($i=0;$i<$len;$i++)
	{
		$id = $arrInitialTop["CD"][$i]["id"];
		$date = $arrInitialTop["CD"][$i]["date"];
		$od = $arrInitialTop["CD"][$i]["od"];
		$os = $arrInitialTop["CD"][$i]["os"];
		echo "<form name=\"frmCd".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_idCd\" value=\"".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_dateCd\" value=\"".$date."\">";
			echo "<input type=\"hidden\" name=\"elem_cdOd\" value=\"".$od."\">";
			echo "<input type=\"hidden\" name=\"elem_cdOs\" value=\"".$os."\">";
		echo "</form>";
	}

	//Disk Photo
	if(!empty($elem_dateDiskPhoto))
	{
		echo "<form name=\"frmDiskPhoto".$elem_dateDiskPhoto."\">";
			echo "<input type=\"hidden\" name=\"elem_idDiskPhoto\" value=\"".$elem_idDiskPhoto."\">";
			echo "<input type=\"hidden\" name=\"elem_dateDiskPhoto\" value=\"".$elem_dateDiskPhoto."\">";
			echo "<input type=\"hidden\" name=\"elem_diskPhotoOd\" value=\"".$elem_diskPhotoOd."\">";
			echo "<input type=\"hidden\" name=\"elem_diskPhotoOs\" value=\"".$elem_diskPhotoOs."\">";
		echo "</form>";
	}
	$len = count($arrInitialTop["Disk"]);
	for($i=0;$i<$len;$i++)
	{
		$id = $arrInitialTop["Disk"][$i]["id"];
		$date = $arrInitialTop["Disk"][$i]["date"];
		$od = $arrInitialTop["Disk"][$i]["od"];
		$os = $arrInitialTop["Disk"][$i]["os"];
		echo "<form name=\"frmDiskPhoto".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_idDiskPhoto\" value=\"".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_dateDiskPhoto\" value=\"".$date."\">";
			echo "<input type=\"hidden\" name=\"elem_diskPhotoOd\" value=\"".$od."\">";
			echo "<input type=\"hidden\" name=\"elem_diskPhotoOs\" value=\"".$os."\">";
		echo "</form>";
	}
	//Pachy
	if(!empty($elem_datePachy))
	{
		echo "<form name=\"frmPachy".$elem_datePachy."\">";
			echo "<input type=\"hidden\" name=\"elem_idPachy\" value=\"".$elem_idPachy."\">";
			echo "<input type=\"hidden\" name=\"elem_datePachy\" value=\"".$elem_datePachy."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOdReads\" value=\"".$elem_pachyOdReads."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOdAvg\" value=\"".$elem_pachyOdAvg."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOdCorr\" value=\"".$elem_pachyOdCorr."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOsReads\" value=\"".$elem_pachyOsReads."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOsAvg\" value=\"".$elem_pachyOsAvg."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOsCorr\" value=\"".$elem_pachyOsCorr."\">";
		echo "</form>";
	}
	$len = count($arrInitialTop["Pachy"]);
	for($i=0;$i<$len;$i++)
	{
		$id = $arrInitialTop["Pachy"][$i]["id"];
		$date = $arrInitialTop["Pachy"][$i]["date"];
		$elem_pachyOdReads = $arrInitialTop["Pachy"][$i]["od"]["Read"];
		$elem_pachyOdAvg = $arrInitialTop["Pachy"][$i]["od"]["Avg"];
		$elem_pachyOdCorr = $arrInitialTop["Pachy"][$i]["od"]["Corr"];
		$elem_pachyOsReads = $arrInitialTop["Pachy"][$i]["os"]["Read"];
		$elem_pachyOsAvg = $arrInitialTop["Pachy"][$i]["os"]["Avg"];
		$elem_pachyOsCorr = $arrInitialTop["Pachy"][$i]["os"]["Corr"];

		echo "<form name=\"frmPachy".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_idPachy\" value=\"".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_datePachy\" value=\"".$date."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOdReads\" value=\"".$elem_pachyOdReads."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOdAvg\" value=\"".$elem_pachyOdAvg."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOdCorr\" value=\"".$elem_pachyOdCorr."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOsReads\" value=\"".$elem_pachyOsReads."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOsAvg\" value=\"".$elem_pachyOsAvg."\">";
			echo "<input type=\"hidden\" name=\"elem_pachyOsCorr\" value=\"".$elem_pachyOsCorr."\">";
		echo "</form>";
	}
	//VF
	if(!empty($elem_dateVf)){
		echo "<form name=\"frmVF".$elem_dateVf."\">";
			echo "<input type=\"hidden\" name=\"elem_idVf\" value=\"".$elem_idVf."\">";
			echo "<input type=\"hidden\" name=\"elem_dateVf\" value=\"".$date."\">";
			echo "<input type=\"hidden\" name=\"elem_vfOdSummary\" value=\"".$elem_vfOdSummary."\">";
			echo "<input type=\"hidden\" name=\"elem_vfOsSummary\" value=\"".$elem_vfOsSummary."\">";
		echo "</form>";
	}
	$len = count($arrInitialTop["VF"]);
	for($i=0;$i<$len;$i++)
	{
		$id = $arrInitialTop["VF"][$i]["id"];
		$date = $arrInitialTop["VF"][$i]["date"];
		$elem_vfOdSummary = $arrInitialTop["VF"][$i]["od"];
		$elem_vfOsSummary = $arrInitialTop["VF"][$i]["os"];

		echo "<form name=\"frmVF".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_idVf\" value=\"".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_dateVf\" value=\"".$date."\">";
			echo "<input type=\"hidden\" name=\"elem_vfOdSummary\" value=\"".$elem_vfOdSummary."\">";
			echo "<input type=\"hidden\" name=\"elem_vfOsSummary\" value=\"".$elem_vfOsSummary."\">";
		echo "</form>";
	}
	//Nfa
	if(!empty($elem_dateNfa))
	{
		echo "<form name=\"frmNFA".$elem_dateNfa."\">";
			echo "<input type=\"hidden\" name=\"elem_idNfa\" value=\"".$elem_idNfa."\">";
			echo "<input type=\"hidden\" name=\"elem_dateNfa\" value=\"".$elem_dateNfa."\">";
			echo "<input type=\"hidden\" name=\"elem_nfaOdSummary\" value=\"".$elem_nfaOdSummary."\">";
			echo "<input type=\"hidden\" name=\"elem_nfaOsSummary\" value=\"".$elem_nfaOsSummary."\">";
		echo "</form>";
	}
	$len = count($arrInitialTop["NFA"]);
	for($i=0;$i<$len;$i++)
	{
		$id = $arrInitialTop["NFA"][$i]["id"];
		$date = $arrInitialTop["NFA"][$i]["date"];
		$elem_nfaOdSummary = $arrInitialTop["NFA"][$i]["od"];
		$elem_nfaOsSummary = $arrInitialTop["NFA"][$i]["os"];

		echo "<form name=\"frmNFA".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_idNfa\" value=\"".$id."\">";
			echo "<input type=\"hidden\" name=\"elem_dateNfa\" value=\"".$date."\">";
			echo "<input type=\"hidden\" name=\"elem_nfaOdSummary\" value=\"".$elem_nfaOdSummary."\">";
			echo "<input type=\"hidden\" name=\"elem_nfaOsSummary\" value=\"".$elem_nfaOsSummary."\">";
		echo "</form>";
	}
    //CEE
    if(!empty($elem_dateCee))
    {
        echo "<form name=\"frmCEE".$elem_dateCee."\">";
            echo "<input type=\"hidden\" name=\"elem_idCee\" value=\"".$elem_idCee."\">";
            echo "<input type=\"hidden\" name=\"elem_dateCee\" value=\"".$elem_dateCee."\">";
            echo "<input type=\"hidden\" name=\"elem_cee\" value=\"".$elem_cee."\">";
	    echo "<input type=\"hidden\" name=\"elem_ceeNotes\" value=\"".$elem_ceeNotes."\">";
        echo "</form>";
    }
    $len = count($arrInitialTop["CEE"]);
    for($i=0;$i<$len;$i++)
    {
        $id = $arrInitialTop["CEE"][$i]["id"];
        $date = $arrInitialTop["CEE"][$i]["date"];
        $elem_cee = $arrInitialTop["CEE"][$i]["cee"];
	$elem_ceeNotes = $arrInitialTop["CEE"][$i]["notes"];

        echo "<form name=\"frmCEE".$id."\">";
            echo "<input type=\"hidden\" name=\"elem_idCee\" value=\"".$id."\">";
            echo "<input type=\"hidden\" name=\"elem_dateCee\" value=\"".$date."\">";
            echo "<input type=\"hidden\" name=\"elem_cee\" value=\"".$elem_cee."\">";
	    echo "<input type=\"hidden\" name=\"elem_ceeNotes\" value=\"".$elem_ceeNotes."\">";
        echo "</form>";
    }

?>

</div>
</body>
