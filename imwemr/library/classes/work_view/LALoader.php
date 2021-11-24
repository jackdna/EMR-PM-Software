<?php
class LALoader extends LA{
	private $oOnload;
	public function __construct($pid, $fid){
		$this->oOnload =  new Onload();
		parent::__construct($pid,$fid);
	}

	function getPurgedHtm(){
		$patient_id = $this->pid;
		$form_id = $this->fid;
		$htmPurge = "";
		$oLids = new Lids($patient_id, $form_id);
		$ar_chart_lids_all = $oLids->getPurgedExm();

		$oLesion = new Lesion($patient_id, $form_id);
		$ar_chart_lesion_all = $oLesion->getPurgedExm();

		$oLidPos = new LidPos($patient_id, $form_id);
		$ar_chart_lid_pos_all = $oLidPos->getPurgedExm();

		$oLacSys = new LacSys($patient_id, $form_id);
		$ar_chart_lac_sys_all = $oLacSys->getPurgedExm();

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw_all = $oChartDraw->getPurgedExm();

		//
		$len = count($ar_chart_lids_all);


		//
		for($i=0; $i<$len; $i++){

		$ar_chart_lids = $ar_chart_lids_all[$i];
		$ar_chart_lesion = $ar_chart_lesion_all[$i];
		$ar_chart_lid_pos = $ar_chart_lid_pos_all[$i];
		$ar_chart_lac_sys = $ar_chart_lac_sys_all[$i];
		$ar_chart_draw = $ar_chart_draw_all[$i];

		// LA --
		$subExm = array_merge($ar_chart_lids["subExm"], $ar_chart_lesion["subExm"], $ar_chart_lid_pos["subExm"], $ar_chart_lac_sys["subExm"],$ar_chart_draw["subExm"]);
		$seList = array_merge($ar_chart_lids["seList"], $ar_chart_lesion["seList"], $ar_chart_lid_pos["seList"], $ar_chart_lac_sys["seList"], $ar_chart_draw["seList"]);
		$elem_noChangeLa = (!empty($ar_chart_lids["nochange"]) || !empty($ar_chart_lesion["nochange"]) || !empty($ar_chart_lid_pos["nochange"]) || !empty($ar_chart_lac_sys["nochange"]) || !empty($ar_chart_draw["nochange"])) ? "1" : "0";
		$bgColor = (!empty($ar_chart_lids["bgColor"]) && !empty($ar_chart_lesion["bgColor"]) && !empty($ar_chart_lid_pos["bgColor"]) && !empty($ar_chart_lac_sys["bgColor"]) && !empty($ar_chart_draw["bgColor"])) ? "bgSmoke" : "" ;
		$examdate = $ar_chart_draw["examdate"]; //draw date
		$drawdocId = $ar_chart_draw["drawdocId"];
		$drawapp = $ar_chart_draw["drawapp"];
		$drawSE =$ar_chart_draw["drawSE"];
		$flgGetDraw =$ar_chart_draw["flgGetDraw"];
		$purgerId = $ar_chart_lids["purgerId"];
		$purgeTime = $ar_chart_lids["purgeTime"];
		//--

		//--

		$arr=array();
		$arr["ename"] = "L&amp;A";
		$arr["subExm"] = $subExm;
		$arr["seList"] = $seList;
		$arr["nochange"] = $elem_noChangeLa;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc_la;
		$arr["bgColor"] = "".$bgColor;
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = $drawSE;
		$arr["examdate"] = $examdate;
		$arr["purgerId"] = $purgerId;
		$arr["purgeTime"] = $purgeTime;
		$arr["flgGetDraw"] = $flgGetDraw;
		$arr["exm_flg_se"] = array($flgSe_LA_Od,$flgSe_LA_Os);
		$htmPurge .= $this->oOnload->getSummaryHTML_purged($arr);

		}

		return $htmPurge;
	}

	function getWorkViewSummery($post){
		$oneeye = $post["oe"]; //one eye

		$patient_id = $this->pid;
		$form_id = $this->fid;

		$oLids = new Lids($patient_id, $form_id);
		$ar_chart_lids = $oLids->getWorkViewSummery($post);

		$oLesion = new Lesion($patient_id, $form_id);
		$ar_chart_lesion = $oLesion->getWorkViewSummery($post);

		$oLidPos = new LidPos($patient_id, $form_id);
		$ar_chart_lid_pos = $oLidPos->getWorkViewSummery($post);

		$oLacSys = new LacSys($patient_id, $form_id);
		$ar_chart_lac_sys = $oLacSys->getWorkViewSummery($post);

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw = $oChartDraw->getWorkViewSummery($elem_dos);


		// LA --
		$subExm = array_merge($ar_chart_lids["subExm"], $ar_chart_lesion["subExm"], $ar_chart_lid_pos["subExm"], $ar_chart_lac_sys["subExm"],$ar_chart_draw["subExm"]);
		$seList = array_merge($ar_chart_lids["seList"], $ar_chart_lesion["seList"], $ar_chart_lid_pos["seList"], $ar_chart_lac_sys["seList"], $ar_chart_draw["seList"]);
		$elem_noChangeLa = (!empty($ar_chart_lids["nochange"]) || !empty($ar_chart_lesion["nochange"]) || !empty($ar_chart_lid_pos["nochange"]) || !empty($ar_chart_lac_sys["nochange"]) || !empty($ar_chart_draw["nochange"])) ? "1" : "0";
		$bgColor = (!empty($ar_chart_lids["bgColor"]) && !empty($ar_chart_lesion["bgColor"]) && !empty($ar_chart_lid_pos["bgColor"]) && !empty($ar_chart_lac_sys["bgColor"]) && !empty($ar_chart_draw["bgColor"])) ? "bgSmoke" : "" ;
		$examdate = $ar_chart_draw["examdate"]; //draw date
		$drawdocId = $ar_chart_draw["drawdocId"];
		$drawapp = $ar_chart_draw["drawapp"];
		$drawSE =$ar_chart_draw["drawSE"];
		$flgGetDraw =$ar_chart_draw["flgGetDraw"];
		$arrHx = array_merge($ar_chart_lids["arrHx"], $ar_chart_lesion["arrHx"], $ar_chart_lid_pos["arrHx"], $ar_chart_lac_sys["arrHx"]);
		if($post["webservice"] != "1"){

			//echo "<pre>";
			//print_r($arrHx);
			//exit();

			list($moeMN,$tmpDiv) = $this->oOnload->mkDivArcCmnNew($this->examName,array(),$arrHx);
			$echo.= $tmpDiv;
		}
		//--

		$arr=array();
		$arr["ename"] = "L&amp;A";
		$arr["subExm"] = $subExm;
		$arr["seList"] = $seList;
		$arr["nochange"] = $elem_noChangeLa;
		$arr["oe"] = $oneeye;
		$arr["desc"] = $elem_txtDesc_la;
		$arr["bgColor"] = "".$bgColor;
		$arr["drawdocId"] = $drawdocId;
		$arr["drawapp"] = $ardrawApp;
		$arr["drawSE"] = $drawSE;
		$arr["examdate"] = $examdate;
		$arr["htmPurge"] = $this->getPurgedHtm();
		$arr["moeMN"] = $moeMN;
		$arr["flgGetDraw"] = $flgGetDraw;
		$arr["exm_flg_se"] = array($flgSe_LA_Od,$flgSe_LA_Os);

		//echo "<pre>";
		//print_r($arrHx);
		//exit();


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



	function getLesionAdvanceOpts( $examNm , $examField, $eye, $onEvent){

		//tempate check
		global $flg_showPlastic;
		if($flg_showPlastic!="1"){ //Check Template
			return "";
		}//End


		$nLocation="elem_".$examField.$eye."_Location";

		$idMedial="elem_".$examField.$eye."_Medial";
		$idCentral="elem_".$examField.$eye."_Central";
		$idLateral="elem_".$examField.$eye."_Lateral";

		$nHoriSize="elem_".$examField.$eye."_HoriSize";
		$nVertiSize="elem_".$examField.$eye."_VertiSize";

		$nPunIn="elem_".$examField.$eye."_PunIn";
		$idPunIn_Y="elem_".$examField.$eye."_PunInY";
		$idPunIn_N="elem_".$examField.$eye."_PunInN";

		$nLossLash="elem_".$examField.$eye."_LossLash";
		$idLossLash_Y="elem_".$examField.$eye."_LossLashY";
		$idLossLash_N="elem_".$examField.$eye."_LossLashN";

		$nRaised="elem_".$examField.$eye."_Raised";
		$idRaised_Y="elem_".$examField.$eye."_RaisedY";
		$idRaised_N="elem_".$examField.$eye."_RaisedN";

		$nUlcerated="elem_".$examField.$eye."_Ulcerated";
		$idUlcerated_Y="elem_".$examField.$eye."_UlceratedY";
		$idUlcerated_N="elem_".$examField.$eye."_UlceratedN";

		$nCrusted="elem_".$examField.$eye."_Crusted";
		$idCrusted_Y="elem_".$examField.$eye."_CrustedY";
		$idCrusted_N="elem_".$examField.$eye."_CrustedN";

		$nHemo="elem_".$examField.$eye."_Hemo";
		$idHemo_Y="elem_".$examField.$eye."_HemoY";
		$idHemo_N="elem_".$examField.$eye."_HemoN";

		$nPigm="elem_".$examField.$eye."_Pigm";
		$idPigm_Y="elem_".$examField.$eye."_PigmY";
		$idPigm_N="elem_".$examField.$eye."_PigmN";

		$nHyker="elem_".$examField.$eye."_Hyker";
		$idHyker_Y="elem_".$examField.$eye."_HykerY";
		$idHyker_N="elem_".$examField.$eye."_HykerN";

		$nTelV="elem_".$examField.$eye."_TelV";
		$idTelV_Y="elem_".$examField.$eye."_TelVY";
		$idTelV_N="elem_".$examField.$eye."_TelVN";

		$nCyst="elem_".$examField.$eye."_Cyst";
		$idCyst_Y="elem_".$examField.$eye."_CystY";
		$idCyst_N="elem_".$examField.$eye."_CystN";

		$nSolid="elem_".$examField.$eye."_Solid";
		$idSolid_Y="elem_".$examField.$eye."_SolidY";
		$idSolid_N="elem_".$examField.$eye."_SolidN";

		$nColor="elem_".$examField.$eye."_Color";
		//$idColor_Y="elem_".$examField.$eye."_ColorY";
		//$idColor_N="elem_".$examField.$eye."_ColorN";

		$nComments="elem_".$examField.$eye."_Comments";


		global $$nLocation, $$nHoriSize, $$nVertiSize,
				$$nPunIn, $$nLossLash, $$nRaised, $$nUlcerated, $$nCrusted, $$nHemo, $$nPigm, $$nHyker, $$nTelV, $$nCyst, $$nSolid, $$nColor, $$nComments;

		$isAdDone	=$$nLocation. $$nHoriSize. $$nVertiSize. $$nPunIn. $$nLossLash. $$nRaised. $$nUlcerated. $$nCrusted. $$nHemo. $$nPigm. $$nHyker. $$nTelV. $$nCyst. $$nSolid. $$nColor. $$nComments;
		$isAdDone=trim($isAdDone);
		if($isAdDone!=""){ $isAdDone=" btnModifiersActive "; }


		$chkMedial = ($$nLocation=="Medial") ? "checked" : "";

		$chkCentral = ($$nLocation=="Central") ? "checked" : "";

		$chkLateral = ($$nLocation=="Lateral") ? "checked" : "";

		$chkPunIn_Y = ($$nPunIn=="Punctal Involvement") ? "checked" : "";

		$chkPunIn_N = ($$nPunIn=="No Punctal Involvement") ? "checked" : "";

		$chkLossLash_Y = ($$nLossLash=="Loss of lashes") ? "checked" : "";

		$chkLossLash_N = ($$nLossLash=="No Loss of lashes") ? "checked" : "";

		$chkRaised_Y = ($$nRaised=="Raised") ? "checked" : "";

		$chkRaised_N = ($$nRaised=="Not Raised") ? "checked" : "";

		$chkUlcerated_Y = ($$nUlcerated=="Ulcerated") ? "checked" : "";

		$chkUlcerated_N = ($$nUlcerated=="Not Ulcerated") ? "checked" : "";

		$chkCrusted_Y = ($$nCrusted=="Crusted") ? "checked" : "";

		$chkCrusted_N = ($$nCrusted=="Not Crusted") ? "checked" : "";

		$chkHemo_Y = ($$nHemo=="Hemorrhagic") ? "checked" : "";

		$chkHemo_N = ($$nHemo=="No Hemorrhagic") ? "checked" : "";

		$chkPigm_Y = ($$nPigm=="Pigmented") ? "checked" : "";

		$chkPigm_N = ($$nPigm=="Not Pigmented") ? "checked" : "";

		$chkHyker_Y = ($$nHyker=="Hyperkeratotic") ? "checked" : "";

		$chkHyker_N = ($$nHyker=="No Hyperkeratotic") ? "checked" : "";

		$chkTelV_Y = ($$nTelV=="Telangiatatic Vessels") ? "checked" : "";

		$chkTelV_N = ($$nTelV=="No Telangiatatic Vessels") ? "checked" : "";

		$chkCyst_Y = ($$nCyst=="Cystic") ? "checked" : "";

		$chkCyst_N = ($$nCyst=="No Cystic") ? "checked" : "";

		$chkSolid_Y = ($$nSolid=="Solid") ? "checked" : "";

		$chkSolid_N = ($$nSolid=="Not Solid") ? "checked" : "";

		//$chkColor_Y = ($$nColor=="Color") ? "checked" : "";

		//$chkColor_N = ($$nColor=="No Color") ? "checked" : "";


		//$elem_$exam$eye_location
		$strHdr=$examNm."  <b class=\"".strtolower($eye)."\">".strtoupper($eye)."</b>";
		$onEvent="placs_checkSingle(this);".$onEvent;

		$dvId = "divModifier".$examField.$eye."";
		$str="
			<div id=\"".$dvId."\" class=\"divLesionModifier\">
			<div class=\"examhd\"><center>".$strHdr."</center></div>
			<div class=\"row form-inline\">
				<div class=\"col-sm-3\">Location</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idMedial."' name='".$nLocation."' value='Medial' onclick='".$onEvent."' ".$chkMedial."> <label for=\"".$idMedial."\">Medial</label>
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idCentral."' name='".$nLocation."' value='Central' onclick='".$onEvent."' ".$chkCentral."> <label for=\"".$idCentral."\">Central</label>
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idLateral."' name='".$nLocation."' value='Lateral' onclick='".$onEvent."' ".$chkLateral."> <label for=\"".$idLateral."\">Lateral</label>

				</div>
			</div>
			<div class=\"row form-inline\">
				<div class=\"col-sm-4\">
				Horizontal Size
				</div>
				<div class=\"col-sm-8\">
					<div class='form-group'>
						<input type='text' id='".$nHoriSize."' name='".$nHoriSize."' value='".$$nHoriSize."' onchange='".$onEvent."' class='form-control' >
						<label for='".$nHoriSize."'>mm</label>
					</div>
				</div>
			</div>
			<div class=\"row form-inline\">
				<div class=\"col-sm-4\">
				Vertical Size
				</div>
				<div class=\"col-sm-8\">
				<div class='form-group'>
					<input type='text' id='".$nVertiSize."'  name='".$nVertiSize."' value='".$$nHoriSize."' onchange='".$onEvent."' class='form-control'>
					<label for='".$nVertiSize."'>mm</label>
				</div>
				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Punctal Involvement
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idPunIn_Y."' name='".$nPunIn."' value='Punctal Involvement' onclick='".$onEvent."' ".$chkPunIn_Y." > <label for=\"".$idPunIn_Y."\">Yes</label>
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idPunIn_N."' name='".$nPunIn."' value='No Punctal Involvement' onclick='".$onEvent."' ".$chkPunIn_N."> <label for=\"".$idPunIn_N."\">No</label>
				</div>

			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Loss of lashes
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idLossLash_Y."' name='".$nLossLash."' value='Loss of lashes' onclick='".$onEvent."' ".$chkLossLash_Y." > <label for=\"".$idLossLash_Y."\">Yes</label>
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idLossLash_N."' name='".$nLossLash."' value='No Loss of lashes' onclick='".$onEvent."' ".$chkLossLash_N."> <label for=\"".$idLossLash_N."\">No</label>

				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Raised
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idRaised_Y."' name='".$nRaised."' value='Raised' onclick='".$onEvent."'  ".$chkRaised_Y." > <label for=\"".$idRaised_Y."\">Yes</label>
					</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idRaised_N."' name='".$nRaised."' value='Not Raised' onclick='".$onEvent."'  ".$chkRaised_N." > <label for=\"".$idRaised_N."\">No</label>

				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Ulcerated
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idUlcerated_Y."' name='".$nUlcerated."' value='Ulcerated' onclick='".$onEvent."' ".$chkUlcerated_Y." > <label for=\"".$idUlcerated_Y."\">Yes</label>
					</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idUlcerated_N."' name='".$nUlcerated."' value='Not Ulcerated' onclick='".$onEvent."' ".$chkUlcerated_N." > <label for=\"".$idUlcerated_N."\">No</label>

				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Crusted
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idCrusted_Y."' name='".$nCrusted."' value='Crusted' onclick='".$onEvent."'  ".$chkCrusted_Y." > <label for=\"".$idCrusted_Y."\">Yes</label>
					</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idCrusted_N."' name='".$nCrusted."' value='Not Crusted' onclick='".$onEvent."' ".$chkCrusted_N." > <label for=\"".$idCrusted_N."\">No</label>

				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Hemorrhagic
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idHemo_Y."' name='".$nHemo."' value='Hemorrhagic' onclick='".$onEvent."'  ".$chkHemo_Y." > <label for=\"".$idHemo_Y."\">Yes</label>
					</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idHemo_N."' name='".$nHemo."' value='No Hemorrhagic' onclick='".$onEvent."'  ".$chkHemo_N." > <label for=\"".$idHemo_N."\">No</label>

				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Pigmented
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idPigm_Y."' name='".$nPigm."' value='Pigmented' onclick='".$onEvent."'  ".$chkPigm_Y." > <label for=\"".$idPigm_Y."\">Yes</label>
					</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idPigm_N."' name='".$nPigm."' value='Not Pigmented' onclick='".$onEvent."'  ".$chkPigm_N." > <label for=\"".$idPigm_N."\">No</label>

				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Hyperkeratotic
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idHyker_Y."' name='".$nHyker."' value='Hyperkeratotic' onclick='".$onEvent."'  ".$chkHyker_Y." > <label for=\"".$idHyker_Y."\">Yes</label>
				</div>
				<div class=\"col-sm-3\">
					<input type='checkbox' id='".$idHyker_N."' name='".$nHyker."' value='No Hyperkeratotic' onclick='".$onEvent."'  ".$chkHyker_N." > <label for=\"".$idHyker_N."\">No</label>

				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Telangiatatic Vessels
				</div>
				<div class=\"col-sm-3\">
				<input type='checkbox' id='".$idTelV_Y."' name='".$nTelV."' value='Telangiatatic Vessels' onclick='".$onEvent."'  ".$chkTelV_Y."  > <label for=\"".$idTelV_Y."\">Yes</label>
				</div>
				<div class=\"col-sm-3\">
				<input type='checkbox' id='".$idTelV_N."' name='".$nTelV."' value='No Telangiatatic Vessels' onclick='".$onEvent."'  ".$chkTelV_N."  > <label for=\"".$idTelV_N."\">No</label>
				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Cystic
				</div>
				<div class=\"col-sm-3\">
				<input type='checkbox' id='".$idCyst_Y."' name='".$nCyst."' value='Cystic' onclick='".$onEvent."'  ".$chkCyst_Y." > <label for=\"".$idCyst_Y."\">Yes</label>
				</div>
				<div class=\"col-sm-3\">
				<input type='checkbox' id='".$idCyst_N."' name='".$nCyst."' value='No Cystic' onclick='".$onEvent."'  ".$chkCyst_N." > <label for=\"".$idCyst_N."\">No</label>
				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-6\">
				Solid
				</div>
				<div class=\"col-sm-3\">
				<input type='checkbox' id='".$idSolid_Y."' name='".$nSolid."' value='Solid' onclick='".$onEvent."' ".$chkSolid_Y." > <label for=\"".$idSolid_Y."\">Yes</label>
				</div>
				<div class=\"col-sm-3\">
				<input type='checkbox' id='".$idSolid_N."' name='".$nSolid."' value='Not Solid' onclick='".$onEvent."'  ".$chkSolid_N." > <label for=\"".$idSolid_N."\">No</label>
				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-4\">
				Color
				</div>
				<div class=\"col-sm-8\">
				<div class='form-group'>
					<input type='text' name='".$nColor."' value='".$$nColor."' onchange='".$onEvent."' >
				</div>
				</div>
			</div>

			<div class=\"row form-inline\">
				<div class=\"col-sm-4\">
				Comments
				</div>
				<div class=\"col-sm-8\">
				<div class='form-group'>
					<input type='text' name='".$nComments."' value='".$$nComments."' onchange='".$onEvent."' >
				</div>
				</div>
			</div>
			<div class=\"clearfix\"> </div>
			".
				"
				<div class=\"row form-inline\">
				<center>
				<input type=\"button\"  class=\"dff_button btn btn-info\"  value=\"Close\" onClick=\"placs_displayModifier('".$examField."','".$eye."')\">
				<input type=\"button\"  class=\"dff_button btn btn-info\"  value=\"Reset\" onClick=\"placs_reset('".$examField."','".$eye."')\" />
				</center>
				</div>

			</div>
			<span id=\"btnMod_".$examField.$eye."\" class=\"btnModifiers ".$isAdDone." glyphicon glyphicon-menu-down\" onclick=\"placs_displayModifier('".$examField."','".$eye."')\"></span>
		";
		//echo $str;

		return $str;
	}

	function load_exam($finalize_flag = 0){
		global $flg_showPlastic;
		$oWv = new WorkView();
		$oExamXml = new ExamXml();
		$patient_id = $this->pid;
		$elem_formId=$form_id = $this->fid;

		$OBJDrawingData = new CLSDrawingData();
		$blEnableHTMLDrawing = false;
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

		//obj work view
		$owv = new WorkView();

		//Dis None
		$cls_brwpto = $cls_OrbFat = $cls_PtoVF = $cls_SplTest = $cls_NslExm = $cls_LacProb = "";
		$arow_brwpto = $arow_OrbFat = $arow_PtoVF = $arow_Advance = $arow_Advance_lac = $arow_SplTest = $arow_NslExm = $arow_LacProb = $arow_LacUpper = $arow_LacLower= $html_entity_hide;

		//usertype
		//$user_type = $_SESSION["logged_user_type"];
		$user_type = (isset($_SESSION["user_role"]) && !empty($_SESSION["user_role"])) ? $_SESSION["user_role"] : $_SESSION["logged_user_type"];

		//default
		$arrTabs = array("1"=>"Lids","2"=>"Lesion","3"=>"Lid Position","4"=>"Lacrimal System","5"=>"Drawing");
		$elem_editMode=0;
		$elem_eeId="";
		$la_examined_nochange = 0;

		$elem_wnl=0;
		$elem_examDate=date("Y-m-d H:i:s");
		$elem_perrla =0;
		$elem_isPositive=0;
		$elem_wnlLids=0;
		$elem_wnlLesion=0;
		$elem_wnlLidPos=0;
		$elem_wnlLacSys=0;
		$elem_wnlDraw=0;

		$elem_posLids=0;
		$elem_posLesion=0;
		$elem_posLidPos=0;
		$elem_posLacSys=0;
		$elem_posDraw=0;

		$elem_ncLids=0;
		$elem_ncLesion=0;
		$elem_ncLidPos=0;
		$elem_ncLacSys=0;
		$elem_ncDraw=0;

		$elem_wnlLidsOd=0;
		$elem_wnlLidsOs=0;
		$elem_wnlLesionOd=0;
		$elem_wnlLesionOs=0;
		$elem_wnlLidPosOd=0;
		$elem_wnlLidPosOs=0;
		$elem_wnlLacSysOd=0;
		$elem_wnlLacSysOs=0;
		$elem_wnlDrawOd=0;
		$elem_wnlDrawOs=0;
		$blDrwaingGray = false;
		//print_r($_REQUEST);
		$myflag=false;
		$arow_brwpto=
		$arow_OrbFat=
		$arow_PtoVF=
		$arow_LacUpper=
		$arow_LacLower=
		$arow_LacProb=
		$arow_NslExm=
		$arow_SplTest="glyphicon-menu-down";
		$html_entity_show="glyphicon-menu-up";

		//DOS
		$elem_dos=$this->getDos(1);

		//Get Exam info --
		$oLids = new Lids($patient_id, $form_id);
		$ar_chart_lids = $oLids->get_chart_lids_info($elem_dos);
		extract($ar_chart_lids);

		$oLesion = new Lesion($patient_id, $form_id);
		$ar_chart_lesion = $oLesion->get_chart_lesion_info($elem_dos);
		extract($ar_chart_lesion);

		$oLidPos = new LidPos($patient_id, $form_id);
		$ar_chart_lid_pos = $oLidPos->get_chart_lid_pos_info($elem_dos);
		extract($ar_chart_lid_pos);

		$oLacSys = new LacSys($patient_id, $form_id);
		$ar_chart_lac_sys = $oLacSys->get_chart_lac_sys_info($elem_dos);
		extract($ar_chart_lac_sys);

		$oChartDraw = new ChartDraw($patient_id, $form_id,$this->examName);
		$ar_chart_draw = $oChartDraw->get_chart_draw_info($elem_dos);
		extract($ar_chart_draw);

		//Combine
		$elem_statusElements .= $elem_statusElementsLids;
		$elem_statusElements .= $elem_statusElementsLesion;
		$elem_statusElements .= $elem_statusElementsLidPos;
		$elem_statusElements .= $elem_statusElementsLacSys;
		$elem_statusElements .= $elem_statusElementsDraw;
		$elem_utElems .= $elem_utElemsLids;
		$elem_utElems .= $elem_utElemsLesion;
		$elem_utElems .= $elem_utElemsLidPos;
		$elem_utElems .= $elem_utElemsLacSys;
		$elem_utElems .= $elem_utElemsDraw;
		$elem_editMode = (!empty($elem_editModeLids)) ? "1" : "0";
		//--


		//Set Change Indicator values -----
		for($i=1;$i<=5;$i++){

			if(empty($elem_statusElements)){continue;}

			$nmIdOd = "elem_chng_div".$i."_Od";
			$nmIdOs = "elem_chng_div".$i."_Os";
			//Od
			if(strpos($elem_statusElements,"".$nmIdOd."=1") !== false){
				$$nmIdOd="1";
			}else{
				$$nmIdOd="0";
			}
			//Os
			if(strpos($elem_statusElements,"".$nmIdOs."=1") !== false){
				$$nmIdOs="1";
			}else{
				$$nmIdOs="0";
			}
		}
		//Set Change Indicator values -----

		//Get Template Procedures ---

		$arrTempProc=array("All");
		$oChartTemp = new ChartTemp();
		$elem_chartTemplateId = $oChartTemp->getChartTempId($patient_id,$form_id);
		if(!empty($elem_chartTemplateId)){
			$tmp = $oChartTemp->getTempInfo($elem_chartTemplateId);
			if(!empty($tmp[1])){
				$elem_chartTempName = $tmp[1];
				//$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array();
				//check for logged in user : physician or technician and chart finalized
				//Please remember Scribe are same as Physician i.e. their view should be same as Physician View: userid = 13
				//Can see physician view
				if( in_array($user_type, $GLOBALS['arrValidCNPhy']) || ($finalize_flag == 1 && $isReviewable==false) || $user_type == 13 || !empty($_SESSION["flg_phy_view"])){
					$arrTempProc = (!empty($tmp[2])) ? explode(",", stripslashes($tmp[2])) : array(); //Phy
				}else{
					$arrTempProc = (!empty($tmp[4])) ? explode(",", stripslashes($tmp[4])) : array(); //Tech
				}
			}
		}

		//
		//chart_admin_settings --
		$elem_settingCompPlastic = $oChartTemp->getCompPlasticSetting(); //Comprehensive Plastic

		//isPlasticOK
		$flg_showPlastic=0;
		if((in_array("All",$arrTempProc) && $elem_settingCompPlastic==1) || in_array("Plastic",$arrTempProc)){ //Check Template
			$flg_showPlastic=1;
		}
		//Get Template Procedures ---

		// Change Indicate Elements -----------------
		$strPtrnGray = "";
		$elem_changeInd = "";

		for($i=1;$i<=5;$i++){
			$nmIdOd = "elem_chng_div".$i."_Od";
			$nmIdOs = "elem_chng_div".$i."_Os";
			$elem_changeInd .= "<input type=\"hidden\" name=\"".$nmIdOd."\" id=\"".$nmIdOd."\" value=\"".$$nmIdOd."\">".
							  "<input type=\"hidden\" name=\"".$nmIdOs."\" id=\"".$nmIdOs."\" value=\"".$$nmIdOs."\">";
			//
			if(empty($$nmIdOd) && empty($$nmIdOs)){
				$strPtrnGray .= "#div".$i." :input,";
			}else{
				if(empty($$nmIdOd)){
					$strPtrnGray .= "#div".$i." :input[name*='Od_'],#div".$i." :input[name$=Od],";
				}else if(empty($$nmIdOs)){
					$strPtrnGray .= "#div".$i." :input[name*='Os_'],#div".$i." :input[name$=Os],";
				}
			}
		}

		if(!empty($strPtrnGray) && (preg_match("/,$/",$strPtrnGray))){
			$strPtrnGray = preg_replace("/,$/","",$strPtrnGray);
		}
		// Change Indicate Elements -----------------

		//defualt Tab --
		$defTabKey = "1";
		if(isset($_GET["pg"]) && !empty($_GET["pg"])){
			if($_GET["pg"]=="Lid"){
				$defTabKey = "1";
			}else if($_GET["pg"]=="Lesion"){
				$defTabKey = "2";
			}else if($_GET["pg"]=="LidPosition"){
				$defTabKey = "3";
			}else if($_GET["pg"]=="Lacrimal_System"){
				$defTabKey = "4";
			}else if($_GET["pg"]=="draw"){
				$defTabKey = "5";
			}
		}

		//defualt Tab --

		//Set Links Colors for sub Exams --

		if(!empty($lid_conjunctiva_summary) || !empty($sumLidsOs)){
		//Lids
		$arBrowPt = array("Brow Ptosis","Medial","Lateral");
		$arOrbFat = array("Orbital Fat","Medial Fat Prolapse","Central Fat Prolapse","Sub Brow Fat Prolapse");
		$arPtoVF = array("Ptosis VF", "Degree loss without taping","Degree Improvement with lid taped","% improvement With lids taped");

		if(!empty($lid_conjunctiva_summary)){
			$stbrwpto_od = ($owv->hasArrVal($lid_conjunctiva_summary,$arBrowPt) != false) ? "sbGrpDone" : "";
			$stOrbFat_od = ($owv->hasArrVal($lid_conjunctiva_summary,$arOrbFat) != false) ? "sbGrpDone" : "";
			$stPtoVF_od = ($owv->hasArrVal($lid_conjunctiva_summary,$arPtoVF) != false) ? "sbGrpDone" : "";
		}

		if(!empty($sumLidsOs)){
			$stbrwpto_os = ($owv->hasArrVal($sumLidsOs,$arBrowPt) != false) ? "sbGrpDone" : "";
			$stOrbFat_os = ($owv->hasArrVal($sumLidsOs,$arOrbFat) != false) ? "sbGrpDone" : "";
			$stPtoVF_os = ($owv->hasArrVal($sumLidsOs,$arPtoVF) != false) ? "sbGrpDone" : "";
		}

		//FlagAdvance
		$flgAdavceOpen="";
		//Display sett
		if($stbrwpto_od == "sbGrpDone" || $stbrwpto_os == "sbGrpDone"){
			$cls_brwpto = "sbGrpOpen";
			$arow_brwpto = $html_entity_show;
			$flgAdavceOpen="1";
		}

		if($stOrbFat_od == "sbGrpDone" || $stOrbFat_os == "sbGrpDone"){
			$cls_OrbFat = "sbGrpOpen";
			$arow_OrbFat = $html_entity_show;
			$flgAdavceOpen="1";
		}

		if($stPtoVF_od == "sbGrpDone" || $stPtoVF_os == "sbGrpDone"){
			$cls_PtoVF = "sbGrpOpen";
			$arow_PtoVF = $html_entity_show;
			$flgAdavceOpen="1";
		}

		//---


		if($flgAdavceOpen==""){

			$arrAdvanceOptions = array("Frontalis Use","Dermatochalesis","Lid Crease","Entropion","Ectropion","Lid Laxity",
									"Lid Contour","MRD 1","MRD 2","Sup Scleral Show","VFH","Levator Function",
									"Lagophthalmos","Lid Lag","Lacrimal gland prolapse","Punctal ectropion","Punctal stenosis",
									"Lower Lid Position","Laxity","LCT Laxity","MCT Laxity","Cicatricial skin Changes","ISS","Lagophthalmus",
									"Medial fat prolapse","Lateral fat prolapse","Tear Trough","Nasojugal fold");
			if(!empty($lid_conjunctiva_summary)){
				$flgAdavceOpen = ($owv->hasArrVal($lid_conjunctiva_summary,$arrAdvanceOptions) != false) ? "1" : "";

			}

			if($flgAdavceOpen=="" && !empty($sumLidsOs)){
				$flgAdavceOpen = ($owv->hasArrVal($sumLidsOs,$arrAdvanceOptions) != false) ? "1" : "";
			}
		}


		if($flgAdavceOpen==1){
			$arow_Advance = $html_entity_show;
			//$display_Advance = " style=\"display:block;\" ";
			$jsAdavceOpen = "#div1 .hdrAdvance";
		}

		//--

		}//lids Advance --

		//Lesion Advance --
		//Modifiers
		$tmp_htmadv_Chalazion_od = $this->getLesionAdvanceOpts("Chalazion", "chalazion","Od","checkwnls();");
		$tmp_htmadv_Chalazion_os = $this->getLesionAdvanceOpts("Chalazion", "chalazion","Os","checkwnls();");
		$tmp_htmadv_Hordeolum_od = $this->getLesionAdvanceOpts("Hordeolum", "hordeolum","Od","checkwnls();");
		$tmp_htmadv_Hordeolum_os = $this->getLesionAdvanceOpts("Hordeolum", "hordeolum","Os","checkwnls();");
		$tmp_htmadv_Papilloma_od = $this->getLesionAdvanceOpts("Papilloma", "papilloma","Od","checkwnls();");
		$tmp_htmadv_Papilloma_os = $this->getLesionAdvanceOpts("Papilloma", "papilloma","Os","checkwnls();");
		$tmp_htmadv_Cyst_od = $this->getLesionAdvanceOpts("Cyst", "cyst","Od","checkwnls();");
		$tmp_htmadv_Cyst_os = $this->getLesionAdvanceOpts("Cyst", "cyst","Os","checkwnls();");
		$tmp_htmadv_Neoplasia_od = $this->getLesionAdvanceOpts("Neoplasia", "neoplas","Od","checkwnls();");
		$tmp_htmadv_Neoplasia_os = $this->getLesionAdvanceOpts("Neoplasia", "neoplas","Os","checkwnls();");
		$tmp_htmadv_Hemangioma_od = $this->getLesionAdvanceOpts("Hemangioma", "Heman","Od","checkwnls();");
		$tmp_htmadv_Hemangioma_os = $this->getLesionAdvanceOpts("Hemangioma", "Heman","Os","checkwnls();");
		$tmp_htmadv_SeboKera_od = $this->getLesionAdvanceOpts("Seborrheic keratosis", "SeboKera","Od","checkwnls();");
		$tmp_htmadv_SeboKera_os = $this->getLesionAdvanceOpts("Seborrheic keratosis", "SeboKera","Os","checkwnls();");
		$tmp_htmadv_InNevus_od = $this->getLesionAdvanceOpts("Intradermal nevus", "InNevus","Od","checkwnls();");
		$tmp_htmadv_InNevus_os = $this->getLesionAdvanceOpts("Intradermal nevus", "InNevus","Os","checkwnls();");
		$tmp_htmadv_Hydrocystoma_od = $this->getLesionAdvanceOpts("Hidrocystoma", "HydCys","Od","checkwnls();");
		$tmp_htmadv_Hydrocystoma_os = $this->getLesionAdvanceOpts("Hidrocystoma", "HydCys","Os","checkwnls();");
		$tmp_htmadv_Xanthelasma_od = $this->getLesionAdvanceOpts("Xanthelasma", "Xantha","Od","checkwnls();");
		$tmp_htmadv_Xanthelasma_os = $this->getLesionAdvanceOpts("Xanthelasma", "Xantha","Os","checkwnls();");


		//Lesion Advance --

		//Lacrimal Advance --
		if(!empty($lacrimal_system_summary) || !empty($sumLacOs)){

		$arLacUpper = array("Upper","Size","Stenosis","Obstruction");
		$arLacLower = array("Lower","Size","Stenosis","Obstruction");
		$arLacProb = array("Lacrimal Probing", "Upper Canalicular Stenosis", "Lower Canalicular Stenosis", "Common Canalicular Stenosis", "Duct stenosis");
		$arNslExm = array("Nasal Exam","Nasal Endoscopy Done", "Septum", "Deviated Septum", "Middle Turbinate Size", "Inferior Turbinate Size", "Polyps", "Inflammation");
		$arSplExm = array("Special Tests", "Dacryocystography", "Lacrimal Scintogram", "CT/MRI");

		if(!empty($lacrimal_system_summary)){
			$stLacUpper_od = ($owv->hasArrVal($lacrimal_system_summary,$arLacUpper) != false) ? "sbGrpDone" : "";
			$stLacLower_od = ($owv->hasArrVal($lacrimal_system_summary,$arLacLower) != false) ? "sbGrpDone" : "";
			$stLacProb_od = ($owv->hasArrVal($lacrimal_system_summary,$arLacProb) != false) ? "sbGrpDone" : "";
			$stNslExm_od = ($owv->hasArrVal($lacrimal_system_summary,$arNslExm) != false) ? "sbGrpDone" : "";
			$stSplTest_od = ($owv->hasArrVal($lacrimal_system_summary,$arSplExm) != false) ? "sbGrpDone" : "";
		}

		if(!empty($sumLacOs)){
			$stLacUpper_os = ($owv->hasArrVal($sumLacOs, $arLacUpper) != false) ? "sbGrpDone" : "";
			$stLacLower_os = ($owv->hasArrVal($sumLacOs, $arLacLower) != false) ? "sbGrpDone" : "";
			$stLacProb_os = ($owv->hasArrVal($sumLacOs, $arLacProb) != false) ? "sbGrpDone" : "";
			$stNslExm_os = ($owv->hasArrVal($sumLacOs, $arNslExm) != false) ? "sbGrpDone" : "";
			$stSplTest_os = ($owv->hasArrVal($sumLacOs, $arSplExm) != false) ? "sbGrpDone" : "";
		}

		//FlagAdvance
		$flgAdavceOpen_Lac="";

		//
		if($stLacUpper_od == "sbGrpDone" || $stLacUpper_os == "sbGrpDone"){
			$cls_LacUpper = "sbGrpOpen";
			$arow_LacUpper = $html_entity_show;
			$flgAdavceOpen_Lac="1";
		}

		//
		if($stLacLower_od == "sbGrpDone" || $stLacLower_os == "sbGrpDone"){
			$cls_LacLower = "sbGrpOpen";
			$arow_LacLower = $html_entity_show;
			$flgAdavceOpen_Lac="1";
		}

		//
		if($stLacProb_od == "sbGrpDone" || $stLacProb_os == "sbGrpDone"){
			$cls_LacProb = "sbGrpOpen";
			$arow_LacProb = $html_entity_show;
			$flgAdavceOpen_Lac="1";
		}

		//
		if($stNslExm_od == "sbGrpDone" || $stNslExm_os == "sbGrpDone"){
			$cls_NslExm = "sbGrpOpen";
			$arow_NslExm = $html_entity_show;
			$flgAdavceOpen_Lac="1";
		}

		//
		if($stSplTest_od == "sbGrpDone" || $stSplTest_os == "sbGrpDone"){
			$cls_SplTest = "sbGrpOpen";
			$arow_SplTest = $html_entity_show;
			$flgAdavceOpen_Lac="1";
		}

		if($flgAdavceOpen_Lac==""){

			$arrAdvanceOptions_Lac = array("Tear Meniscus");
			if(!empty($lacrimal_system_summary)){
				$flgAdavceOpen_Lac = ($owv->hasArrVal($lacrimal_system_summary,$arrAdvanceOptions_Lac) != false) ? "1" : "";
			}

			if($flgAdavceOpen_Lac=="" && !empty($sumLacOs)){
				$flgAdavceOpen_Lac = ($owv->hasArrVal($sumLacOs,$arrAdvanceOptions_Lac) != false) ? "1" : "";
			}
		}

		if($flgAdavceOpen_Lac==1){
			$arow_Advance_lac = $html_entity_show;
			//$display_Advance = " style=\"display:block;\" ";
			if(!empty($jsAdavceOpen)){$jsAdavceOpen.=", ";}
			$jsAdavceOpen .= "#div4 .hdrAdvance";
		}

		}
		//Lacrimal Advance --

		//GetExamExtension --
		$arr_exm_ext_htm = array();
		$arr_exm_ext = $oExamXml->get_exam_extension("L&A");
		if(count($arr_exm_ext)>0){
			foreach($arr_exm_ext as $k_exm => $v_tab){
				if(count($v_tab)>0){
					foreach($v_tab as $k_exm_find => $v_file){
						if(count($v_file)>0){
							foreach($v_file as $k_tmp => $exmext_file_path){

								ob_start();
								include($exmext_file_path);
								$tmp = ob_get_contents();
								ob_end_clean();
								$ptmp="";
								if(isset($arr_exm_ext_htm[$k_exm][$k_exm_find])&&!empty($arr_exm_ext_htm[$k_exm][$k_exm_find])){
									$ptmp = $arr_exm_ext_htm[$k_exm][$k_exm_find];
								}
								$arr_exm_ext_htm[$k_exm][$k_exm_find] = $ptmp.$tmp;

							}
						}
					}
				}
			}
		}
		//End GetExamExtension --

		//Draw
		$intDrawingFormId = $elem_formId;
		$intDrawingExamId = $elem_drawId;
		$strScanUploadfor = "LA_DSU";

		##
		header('Content-Type: text/html; charset=utf-8');
		##
		$z_ob_get_clean=$GLOBALS['fileroot']."/interface/chart_notes/view/la.php";
		include($GLOBALS['fileroot']."/interface/chart_notes/minfy_inc.php");
	}
}
?>
