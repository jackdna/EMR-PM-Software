<?php
Class Onload{

	function se_checkFlgExm($arr,$ignr=0){
		$od = $os = 0;

		if(isset($arr) && count($arr)>0){
			foreach($arr as $key => $val){
				if($key!=$ignr){
					if($val["od"]==1){$od=1;}
					if($val["os"]==1){$os=1;}
				}
			}
		}
		return array($od,$os);
	}

	function onwv_isDrawingChanged($arr){

		if(in_array(1,$arr)){
			return 1;
		}else{
			return 0;
		}

	}

	function setWnlValuesinSumm($arr){
		//wnl
		if($arr["wOd"] == 1){
			$arr["sOd"] = !empty($arr["wValOd"]) ? $arr["wValOd"] : $arr["wVal"];
		}
		if($arr["wOs"] == 1){
			$arr["sOs"] = !empty($arr["wValOs"]) ? $arr["wValOs"] : $arr["wVal"];
		}
		return array($arr["sOd"],$arr["sOs"]);
	}

	function getArrExms_ms($arr){

		//if pos Not Set, set it to 1
		if(!isset($arr["pos"])){$arr["pos"]=1;}

		//Color
		//color_label
		$cnm="";
		if((!empty($arr["fOd"]) && !empty($arr["pos"]) && $arr["sOd"]!="")||
			(!empty($arr["fOs"]) && !empty($arr["pos"]) && $arr["sOs"]!="")
			){
			$cnm="color_red";
		}

		// Label CD  to red if there is any value --
		if($arr["enm"] == "CD" && ($arr["sOd"]!="" || $arr["sOs"]!="")){
			$cnm="color_red";
		}
		//--

		$tmp = array("nm"=>$arr["enm"],
					"sumod"=>$arr["sOd"],
					"sumos"=>$arr["sOs"],
					"cnm"=>$cnm,"cOd"=>$arr["fOd"],"cOs"=>$arr["fOs"],
					"arcJsOd"=>$arr["arcJsOd"],"arcJsOs"=>$arr["arcJsOs"],
					"arcCssOd"=>$arr["arcCssOd"],"arcCssOs"=>$arr["arcCssOs"],
					"mnOd"=>$arr["mnOd"],"mnOs"=>$arr["mnOs"],
					"nm_2"=>$arr["enm_2"]
				);
		if(isset($arr["nm_2show"])){	$tmp["nm_2show"] = $arr["nm_2show"];  }
		if(isset($arr["elem_periNotExamined"])){	$tmp["elem_periNotExamined"] = $arr["elem_periNotExamined"];  }
		if(isset($arr["elem_peri_ne_eye"])){	$tmp["elem_peri_ne_eye"] = $arr["elem_peri_ne_eye"];  }

		return $tmp;
	}

	function getModiNoteConDiv($elmId, $divch, $focsjs="", $divCls=""){
		$strMoe = $focsjs;
		if(!empty($divch)){
			//$strMoe .= " onmouseover=\"showdivArc('".$elmId."',1,1);\" onmouseout=\"showdivArc('".$elmId."',0,1);\" ";
			$strMoe = "";
			$divch = "<div id=\"div_mn_".$elmId."\" ".
							//"onmouseover=\"showdivArc('".$elmId."',1,1);\"".
							//"onmouseout=\"showdivArc('".$elmId."',0,1);\"".
							"class=\"mnDiv ".$divCls."\">".$divch."</div>";
		}
		return array($strMoe,$divch);
	}

	function mkDivArcCmnNew($nm,$arr,$arrHx){
		$elmId="divArcCmn".$nm;
		/*
		$str = "<div id=\"".$elmId."\" class=\"divArcCmn\" ".
					"onmouseover=\"showdivArc('".$nm."',1,1);\" ".
					"onmouseout=\"showdivArc('".$nm."',0,1);\" ".
					" style='padding:0px 5px 5px 5px;max-height:300px;overflow:v-scroll'><div id='boxhead' class='boxhead' style='cursor:move;'><span class=\"closeBtn\" onClick=\"$('#".$elmId."').hide()\"></span>Test History</div> ";
		*/
		/*
		if(count($arr)>0){
			$strMoe ="";
			$elmId="divArcCmn".$nm;
			$strMoe .= " onClick=\"showdivArc('".$nm."',1,1);\" onmouseout=\"showdivArc('".$nm."',0,1);\" ";

			foreach($arr as $key => $val){
				$vod=$val["OD"];
				$vos=$val["OS"];

				$str .= "<label>".$key."</label>".$vod.$vos."<br/>";

			}
			//$str .= "</div>";
		}
		*/
		if(is_array($arrHx) && count($arrHx)>0){
			$strMoe ="";
			$elmId="divArcCmn".$nm;
			$strMoe .= " onClick=\"showdivArc('".$nm."',1,1);\" onmouseout=\"showdivArc('".$nm."',0,1);\" ";
			/*$str = "<div id=\"".$elmId."\" class=\"divArcCmn\" ".
					"onmouseover=\"showdivArc('".$nm."',1,1);\" ".
					"onmouseout=\"showdivArc('".$nm."',0,1);\" ".
					">";*/
			/*$str .= "<div style='display: none; top: 370px; left: 237px; max-height:400px;overflow:v-scroll' id=\"".$elmId."\" class=\"divArcCmn\" ".
					"onmouseover=\"showdivArc('".$nm."',1,1);\" ".
					"onmouseout=\"showdivArc('".$nm."',0,1);\" ".
					">";*/
			foreach($arrHx as $testName => $arrSubTest){
				$vod=$val["OD"];
				$vos=$val["OS"];
				//$str .= "<label style='width:100px'>".$testName."</label>";
				//$str .= "<label>".$key."</label>".$vod.$vos."<br/>";
				$c=0;
				foreach($arrSubTest as $arrOdOs){
					//$str .= "<label>&nbsp;</label>";
					//$str .= "<div id='div_mn_elem_sumOdLids' class='hxDiv '>";
					array_reverse($arrOdOs);
					foreach($arrOdOs as $key=>$arrSub){
						//if($key == "OD")
						//$arrOD[] = $arrSub;
						//if($key == "OS")
						//$arrOS[] = $arrSub;

						$ousr = new User($arrSub["modi_by"]);
						$usr_nm = $ousr->getName(); //getUserFirstName($arrSub["modi_by"],1)
						$arrHx[$testName][$c][$key]['usrnm'] = $usr_nm;

						if($arrSub['preVal'] != $arrSub['val']){
							/*
							$str .= "<div class='hxDiv '>".$arrSub['time']."</div>";
							$str .= "<div class='hxDiv ' style='width:200px'>".$arrSub['preVal']."</div>";
							$str .= "<div class='hxDiv ' style='width:200px'>".$arrSub['val']."</div>";
							$str .= "<div class='hxDiv '>".$key."</div>";
							$str .= "<div class='hxDiv' style='border-right:0px'>".$usr_nm."</div>";
							*/

						}
						//$str .= "<div >&nbsp;</div>";
					}
					//$str .= "</div>";
					$c+=1;
				}

			}


		}
		//$str .= "</div>";
		//echo "HELLO: ".$strMoe;

		//--
		$str="";
		ob_start();
		include($GLOBALS['incdir']."/chart_notes/view/test_hx.php");
		$out2 = ob_get_contents();
		ob_end_clean();
		$str = $out2;
		//--

		return array($strMoe,$str);

	}

	//
	function getSubExamLabel($enm_main, $enm_sub, $arr){

		if($enm_sub=="Lac"){
			$enm_sub="LacSys";
		}else if($enm_sub=="Periphery"){
			$enm_sub="Peri";
		}else if($enm_sub=="Vitreous"){
			$enm_sub="Vit";
		}else if($enm_sub=="Macula"){
			$enm_sub="Mac";
		}else if($enm_sub=="Drawing"&&$enm_main=="LA"){
			$enm_sub="DrawLa";
		}else if($enm_sub=="Drawing"&&$enm_main=="Gonio"){
			$enm_sub="DrawGonio";
		}else if($enm_sub=="Drawing"&&$enm_main=="SLE"){
			$enm_sub="DrawSle";
		}else if($enm_sub=="Drawing"&&$enm_main=="RV"){
			$enm_sub="DrawRv";
		}else if($enm_sub=="Anesthetic"||$enm_sub=="Dilation"||$enm_sub=="OOD"){
			$enm_sub="IOP";
		}else if($enm_sub=="Retinal"){
			$enm_sub="Ret";
		}

		$ret = array();
		$val = $arr[$enm_sub];
		if(is_array($val) && count($val)>0){
			$enm_s = $enm_sub;

			$enm=$val["enm"];
			if(!empty($val["pos"])){
				$cls = "flgPos";
			}else if(!empty($val["wOd"])&&!empty($val["wOs"])){
				$cls = "flgWnl";
			}else{
				$cls ="";
			}

			$ret["id"] = " id=\"flagWnl".$enm_s."\" ";
			$ret["cls"] = $cls;
			$ret["onclick"] = " onclick=\"openPW('".$enm_main."','".$enm_s."')\" ";
		}else{

			if($enm_sub=="CD"){ //do not show flag to CD
				$enm_s = $enm_sub;
				$ret["id"] = " id=\"flagWnl".$enm_s."\" ";
				$ret["cls"] = "";
				$ret["onclick"] = " onclick=\"openPW('".$enm_main."','".$enm_s."')\" ";
			}

		}

		return $ret;
	}


	//webs service
	function getSummaryHTML_appwebservice($arr){
		$enm_var = $enm = $arr["ename"];
		$arrSE = $arr["subExm"];
		$seList= isset($arr["seList"]) ? $arr["seList"] : "";
		$nochange = $arr["nochange"];
		$oneeye = (!empty($arr["oe"])) ? explode("~!~",$arr["oe"]):array();
		$desc = trim($arr["desc"]);
		$bgColor = $arr["bgColor"];
		$drawdocId = $arr["drawdocId"];
		$drawapp = $arr["drawapp"];
		$htmPurge = $arr["htmPurge"];
		$moeMN = $arr["moeMN"];

		//For Variable
		if($enm=="L&amp;A")$enm_var = "LA";
		else if($enm=="IOP/Gonio")$enm_var = "Gonio";
		else if($enm=="Fundus Exam"||$enm=="Fundus")$enm_var = "RV";
		else if($enm=="Refractive Surgery")$enm_var = "Ref_Surg";

		//One Eye
		if($oneeye[0]=="Poor View" && $enm_var!="RV"){
			$oneeye=array();
		}

		//--------------

		///* ----------

		$exm_header = "<div class=\"einfo\">".
				"<div class=\"hdr\">".
				"<label onclick=\"openPW('".$enm_var."')\">".$enm."</label>";

		//$exm_header_xml = "<header><title>".$enm."</title>";
		$exm_header_xml ["header"]["title"]=$enm;

		//Pupil : Pharama	--
		if($enm=="Pupil"){
			if(trim($arr["other_values"])=="Pharmacologically dilated on arrival,"){
				$arr["other_values"]["elem_pharmadilated"] = "Pharmacologically dilated on arrival";
			}else{
				$arr["other_values"] = unserialize($arr["other_values"]);
			}
		}
		//Pupil : Pharama	--

		//<!-- Reset -->

		/*
		//<!-- WNL -->
		//No Wnl for IOP/Gonio && Refractive Surgery
		if($enm != "IOP/Gonio" && $enm != "Refractive Surgery"){
			//NO OD+OS Wnl for EOM and External
			if($enm !="EOM" && $enm != "External"){
				$mouseEvt = " onmouseover=\"showWnlOpts(1,1,'".$enm_var."')\" onmouseout=\"showWnlOpts(0,1)\" ";
			}else{
				$mouseEvt = "";
			}

		$exm_header .=	"".
				"<input type=\"button\" id=\"elem_btnWnl".$enm_var."\" class=\"wnl\" value=\"WNL\" onClick=\"setWnlValues('".$enm_var."')\"".
				"".$mouseEvt1."> ";
		}

		//<!-- No Change -->
		$tmp = (!empty($nochange)) ? "Prv" : "NC";
		$exm_header .=	"".
				"<input type=\"button\" id=\"elem_btnNoChange".$enm_var."\" class=\"prv\" value=\"".$tmp."\" onClick=\"autoSaveNoChange('".$enm_var."')\"";
		if($enm !="EOM") $exm_header .=	"	onmouseover=\"showWnlOpts(1,2,'".$enm_var."')\" onmouseout=\"showWnlOpts(0,2)\"";
		$exm_header .=	" > ";
		//hx
		if(!empty($moeMN)){
		$exm_header .= "<label id=\"lblHx".$enm_var."\" class=\"hxlbl\" ".$moeMN." >Hx</label>";
		}
		*/

		$exm_header .= "</div>";

		$exm_header .=	"</div>";

		//$exm_header_xml .= "</header>";
		// -------------  */



		///IOP :: Dilation & Ansthetic ------------
		/*
		$dilatDropAllergy = $anes = $dilate = $ood= "";
		if($enm == "IOP/Gonio"){
			if(!empty($arr["Dilation"]["PtAllergic"])){
				$dilatDropAllergy = "<label id=\"ptAlrgic\">Dilation Drop Allergies</label>";
			}

			if(!empty($arr["Dilation"]["Anesthetic"])) {
				$anes = 	"<label id=\"spAnesTime\">".
						//"Anesthetic ".
							"<a href=\"javascript:void(0);\" onclick=\"updateADTime('Anes')\" >".
							$arr["Dilation"]["Anesthetic"]."</a>".
						"</label>";
			}

			if(!empty($arr["Dilation"]["Dilate"])) {
				if($arr["Dilation"]["Dilate"]=="No Dilation"||$arr["Dilation"]["Dilate"]=="Refuse Dilation"){
					$dilate = $arr["Dilation"]["Dilate"];
				}else{
					$dilate = 	"".
							//"Dilation ".
							"<a href=\"javascript:void(0);\" onclick=\"updateADTime('Dial')\" >".
								$arr["Dilation"]["Dilate"]."</a>";
				}
				$dilate = "<label id=\"spDialTime\">".$dilate."</label>";
			}

			if(!empty($arr["Dilation"]["OOD"])) {
				$ood = 	"<label id=\"spOODTime\">".
						//"Anesthetic ".
							"<a href=\"javascript:void(0);\" onclick=\"updateADTime('OOD')\" >".
							$arr["Dilation"]["OOD"]."</a>".
						"</label>";
			}

		}
		*/
		///IOP :: Dilation & Ansthetic ------------

		//Summary --
		$str = ""; $str_xml ="";
		$str .=	"<div class=\"esum\">".
					"<table border=\"1\" >";

		//$str_xml .="<exam>";
		///$str_xml .= $exm_header_xml;
		$str_xml["exam"] = array();
		$str_xml["exam"] = array_merge($str_xml["exam"], $exm_header_xml);

			$len = count($arrSE);
			$rowspan = (count($oneeye)>0) ? $len+1 : $len;
			if(($enm_var == "LA" || $enm_var == "Gonio" || $enm_var == "RV" || $enm_var == "SLE") ||
				($enm=="SLE" && !empty($arr["penLight"]))||
				($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival")){
				$rowspan +=1;
			}

			//Pen Light--
			if($enm=="SLE" && !empty($arr["penLight"])){

				//Color
				if(empty($bgColor)){
					$tmp="bgWhite";
				}else{
					$tmp="";
				}

				$str .= "<tr>";
				$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
				$str .=	"<td class=\"prph sumod ".$tmp."\" > PenLight Exam "."</td>";
				$str .=	"<td class=\"prph sumos ".$tmp."\"> PenLight Exam "."</td>";
				//$str .= "".$strDesc;
				$str .="</tr>";

				$strDesc="";//Empty Desc after use
				$exm_header = ""; //Empty after use

				/*
				$str_xml .="<penlight_exam>
								<od class=\"".$tmp."\"> PenLight Exam </od>
								<os class=\"".$tmp."\"> PenLight Exam </os>
							</penlight_exam>";
				*/
				$ar_temp=array();
				$ar_temp["penlight_exam"]["od"]["value"] = " PenLight Exam ";
				$ar_temp["penlight_exam"]["od"]["class"] = $tmp;

				$ar_temp["penlight_exam"]["os"]["value"] = " PenLight Exam ";
				$ar_temp["penlight_exam"]["os"]["class"] = $tmp;
				$str_xml["exam"] = array_merge($str_xml["exam"], $ar_temp);
			}
			//Pen Light--

			// Pharmacologically dilated on arrival --
			if($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival"){

				//Color
				if(empty($bgColor)){
					$tmp="bgWhite";
				}else{
					$tmp="";
				}

				$str .= "<tr>";
				$str .=	"<td class=\"edone ehdr\">".$exm_header."</td>";
				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
					$str .=	"<td class=\"prph sumod ".$tmp."\" > Pharmacologically dilated on arrival "."</td>";
				}else{
					$str .=	"<td class=\"prph sumod\" ></td>";
				}
				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
					$str .=	"<td class=\"prph sumos ".$tmp."\"> Pharmacologically dilated on arrival "."</td>";
				}else{
					$str .=	"<td class=\"prph sumod\" ></td>";
				}
				$str .= "".$strDesc;
				$str .="</tr>";

				$strDesc="";//Empty Desc after use
				$exm_header = ""; //Empty after use

				//
				/*
				$str_xml .="<pharma_dilated>";
					if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
						$str_xml .= "<od class=\"".$tmp."\">  Pharmacologically dilated on arrival </od>";
					}else{
						$str_xml .= "<od></od>";
					}

					if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
						$str_xml .= "<os class=\"".$tmp."\">  Pharmacologically dilated on arrival </os>";
					}else{
						$str_xml .= "<os></os>";
					}

				$str_xml .= "</pharma_dilated>";
				*/
				$ar_temp["pharma_dilated"]=array();
				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
					$ar_temp["pharma_dilated"]["od"]["value"] = " Pharmacologically dilated on arrival ";
					$ar_temp["pharma_dilated"]["od"]["class"] = $tmp;
				}else{
					$ar_temp["pharma_dilated"]["od"]["value"] = "";
					$ar_temp["pharma_dilated"]["od"]["class"] = "";
				}

				if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
					$ar_temp["pharma_dilated"]["os"]["value"] = " Pharmacologically dilated on arrival ";
					$ar_temp["pharma_dilated"]["os"]["class"] = $tmp;
				}else{
					$ar_temp["pharma_dilated"]["os"]["value"] = "";
					$ar_temp["pharma_dilated"]["os"]["class"] = "";
				}
				$str_xml["exam"] = array_merge($str_xml["exam"], $ar_temp);
			}
			// Pharmacologically dilated on arrival --



			//One Eye --
			if(count($oneeye)>0){
				if($oneeye[1]=="OU" || $oneeye[1]=="OD"){
					$on_nm_od = $oneeye[0];
					if($oneeye[2]==1){
						$cOd = "bgWhite";
					}else{
						$cOd = (!empty($arr["exm_flg_se"][0])) ? "bgWhite" : "";
					}
				}

				if($oneeye[1]=="OU" || $oneeye[1]=="OS"){
					$on_nm_os = $oneeye[0];
					if($oneeye[2]==1){
						$cOs = "bgWhite";
					}else{
						$cOs = (!empty($arr["exm_flg_se"][1])) ? "bgWhite" : "";
					}
				}

				$str .= "<tr>";
				$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
				$str .=	"<td class=\"prph sumod ".$cOd."\" >".$on_nm_od.
						"</td>";
				$str .=	"<td class=\"prph sumos ".$cOs."\">".$on_nm_os.
						"</td>";
				$str .= "".$strDesc;
				$str .="</tr>";

				$strDesc="";//Empty Desc after use
				$exm_header = ""; //Empty after use

				//
				/*
				$str_xml .= "<one_eye>";
					$str_xml .= "<od class=\"".$cOd."\">  ".$on_nm_od." </od>";
					$str_xml .= "<os class=\"".$cOd."\">  ".$on_nm_od." </os>";
				$str_xml .= "</one_eye>";
				*/

				$ar_temp["one_eye"]=array();
				$ar_temp["one_eye"]["od"]["value"] = " ".$on_nm_od." ";
				$ar_temp["one_eye"]["od"]["class"] = $cOd;
				$ar_temp["one_eye"]["os"]["value"] = " ".$on_nm_os." ";
				$ar_temp["one_eye"]["os"]["class"] = $cOs;
				$str_xml["exam"] = array_merge($str_xml["exam"], $ar_temp);

			}
			//--



			//Header Info ----------------------
				/*
				//Add header bar for LA, Gonio, RV SLE
				if(!empty($exm_header) && ($enm_var == "LA" || $enm_var == "Gonio" || $enm_var == "RV" || $enm_var == "SLE")){

					//Color
					if(empty($bgColor)){
						$tmp="bgWhite";
					}else{
						$tmp="";
					}

					$str .= "<tr>";
					$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
					$str .= "<td class=\"sumod  ".$tmp."\"></td>";
					$str .= "<td class=\"sumos ".$tmp."\"></td>";
					$str .= "".$strDesc;
					$str .= "</tr>";

					$strDesc="";//Empty Desc after use
					$exm_header = ""; //Empty after use

				}
				*/
			//Header Info ----------------------

			//add header
			//if($enm_var == "Pupil" || $enm_var == "EOM" || $enm_var == "External" || $enm_var == "Ref_Surg" ){
				$str .=	"<tr><td class=\"edone ehdr\" colspan=\"2\">".$exm_header."</td></tr>";
			//}

			//$str_xml .= "<sub_exams>";
			$str_xml["exam"]["sub_exams"]=array();

			for($i=0;$i<$len;$i++){
			$sumod = trim($arrSE[$i]["sumod"]);
			$sumos = trim($arrSE[$i]["sumos"]);
			$cnm=$cOd=$cOs=$se_nm="";

			//$str_xml_se ="";
			$str_xml_se =array();

			//if($sumod!="" || $sumos!=""){ // Stopped check so that all exam appear in list
				$se_nm = $arrSE[$i]["nm"];
				if($sumod!="" && !empty($arrSE[$i]["cOd"])){
					$cOd = "bgWhite";
				}
				if($sumos!="" && !empty($arrSE[$i]["cOs"])){
					$cOs = "bgWhite";
				}
				if(!empty($arrSE[$i]["cnm"])){
					$cnm = "color_red";
				}

				$se_nm_var=$se_nm;
				if($se_nm=="Lid Position"){
					$se_nm_var="LidPos";
				}else if($se_nm=="Lacrimal System"){
					$se_nm_var="Lac";
				}else if($se_nm=="Conjunctiva"){
					$se_nm_var="Conj";
				}else if($se_nm=="Cornea"){
					$se_nm_var="Corn";
				}else if($se_nm=="Ant. Chamber"){
					$se_nm_var="Ant";
				}else if($se_nm=="Iris & Pupil"){
					$se_nm_var="Iris";
				}else if($se_nm=="Blood Vessels"){
					$se_nm_var="BV";
				}else if($se_nm=="Optic Nerve"){
					$se_nm_var="Optic";
				}else if($se_nm=="Ophth. Drops"){
					$se_nm_var="OOD";
				}else if($se_nm=="Retinal Exam"){
					$se_nm_var="Retinal";
				}

				//Stop Drawing to be last row --
				if($se_nm_var == "Drawing"){
					continue;
				}
				//Stop Drawing to be last row --

				$elem_summOd = "elem_sumOd".$se_nm_var;
				$elem_summOs = "elem_sumOs".$se_nm_var;
				$js_od = $arrSE[$i]["arcJsOd"];
				$js_os = $arrSE[$i]["arcJsOs"];
				$css_od = $arrSE[$i]["arcCssOd"];
				$css_os =  $arrSE[$i]["arcCssOs"];
				$mn_od = $arrSE[$i]["mnOd"];
				$mn_os = $arrSE[$i]["mnOs"];

				//echo "".$js_od." ".$mn_od."";

			//}



			if($enm_var == "Pupil" || $enm_var == "EOM" || $enm_var == "External" || $enm_var == "Ref_Surg" ){
				//$str .=	"<td class=\"edone ehdr\">".$exm_header."</td>";
			}else{
				//First Row, Add Drawing label also
				if($i==0){
					$se_nm_drw = $arrSE[$len-1]["nm"];
				}else{
					$se_nm_drw = "";
				}

				if($se_nm_drw=="Drawing"){
					/*
					$cnm_drw = (!empty($arrSE[$len-1]["cnm"])) ? "color_red" : "color_black";
					//GET link settings
					$arlinkDrw= getSubExamLabel($enm_var, "Drawing", $seList);
					$strDrw ="<div ".$arlinkDrw["id"]." class=\"drawtab ".$cnm_drw." \" >".
							//"<span class=\"".$arlinkDrw["cls"]."\"></span>".
							"<label ".$arlinkDrw["onclick"]." >".$se_nm_drw."</label>".
							"</div>";
					*/

					//if CD
					if($se_nm == "CD"){
						/*
						$strCDRV ="<div id=\"dvCDRV\" class=\"formCDRV \" >".
							"<label class=\"od\" >OD</label>".
							"<input type=\"text\" name=\"elem_rvcd_od\" value=\"".$arr["elem_rvcd"]["od"]."\" onblur=\"saveCDRV()\" >".
							"<label class=\"os\" >OS</label>".
							"<input type=\"text\" name=\"elem_rvcd_os\" value=\"".$arr["elem_rvcd"]["os"]."\" onblur=\"saveCDRV()\" >".
							//"<input type=\"button\" name=\"elem_rvcd_btn\" value=\"Save\" class=\"dff_button_sm\" onclick=\"saveCDRV()\">".
							"</div>";
						$cssCDRV	=" tdCDRV ";
						*/
						$strCDRV="";
						$cssCDRV	="";
					}else{
						$strCDRV="";
						$cssCDRV	="";
					}

					//GET link settings
					$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);
					if(empty($cnm) && $arlink["cls"]=="flgPos"){ //show red color label if summary exists
						$cnm = "color_red";
					}

					//display name
					$tmp_se_nm = $se_nm;
					if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }

					$str .= "<tr>";
					$str .="<td ".$arlink["id"]." class=\"edone esubexam ".$cnm." ".$cssCDRV." \"  colspan=\"2\" >".
							//"<span class=\"".$arlink["cls"]."\"></span>".
							"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>".
						//$strDrw.
						$strCDRV.
						"</td>";
					$str .="</tr>";

					//
					/*
					$str_xml_se.="<header_info>";
					$str_xml_se.="<title class=\"".$cnm."\">".htmlentities($tmp_se_nm)."</title>";
					$str_xml_se.="</header_info>";
					*/

					$str_xml_se["header_info"]=array();
					$str_xml_se["header_info"]["title"]=$tmp_se_nm;
					$str_xml_se["header_info"]["class"]=$cnm;

				}else{
					//GET link settings
					$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);
					if(empty($cnm) && $arlink["cls"]=="flgPos"){ ////show red color label if summary exists
						$cnm = "color_red";
					}

					//$str_xml_se.="<header_info>";
					$str_xml_se["header_info"]=array();

					//display name
					$tmp_se_nm = $se_nm;
					if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }
					$str .= "<tr>";
					$str .="<td ".$arlink["id"]." class=\"edone esubexam ".$cnm." \"  colspan=\"2\" >".
							//"<span class=\"".$arlink["cls"]."\"></span>".
							"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>";
					//
					//$str_xml_se.="<title class=\"".$cnm."\">".htmlentities($tmp_se_nm)."</title>";
					$str_xml_se["header_info"]["title"]["value"]=htmlentities($tmp_se_nm);
					$str_xml_se["header_info"]["title"]["class"]=$cnm;

					if($se_nm_var=="Anesthetic") {$str .="".$anes;}
					if($se_nm_var=="Dilation") {
						if(!empty($dilate)){$str .="".$dilate; }else if(!empty($dilatDropAllergy)){$str .="".$dilatDropAllergy; }
					}
					if($se_nm_var=="OOD") { $str .= $ood;  }

					//
					/*
					if($se_nm_var=="Anesthetic") { if(!empty($anes)){$str_xml_se.="<other_info>".$anes."</other_info>";}}
					if($se_nm_var=="Dilation") { if(!empty($dilate)){ $str_xml_se.="<other_info>".$dilate."</other_info>"; }
											else if(!empty($dilatDropAllergy)){ $str_xml_se.="<other_info>".$dilatDropAllergy."</other_info>";}}
					if($se_nm_var=="OOD") { if(!empty($ood)){$str_xml_se.="<other_info>". $ood."</other_info>";} }
					*/
					if($se_nm_var=="Anesthetic") { if(!empty($anes)){$str_xml_se["other_info"]=$anes;}}
					if($se_nm_var=="Dilation") { if(!empty($dilate)){ $str_xml_se["other_info"]=$dilate; }
											else if(!empty($dilatDropAllergy)){ $str_xml_se["other_info"]=$dilatDropAllergy;}}
					if($se_nm_var=="OOD") { if(!empty($ood)){$str_xml_se["other_info"]=$ood;} }

					/*
					//if last row and RV
					if($enm_var == "RV" && $i==$len-2){
						$elem_periNotExamined = $arr["elem_periNotExamined"];
						$elem_peri_ne_eye = $arr["elem_peri_ne_eye"];

						$elem_periNotExamined = ($elem_periNotExamined==1) ? "checked" : "";
						$elem_peri_ne_OU = ($elem_peri_ne_eye=="OU") ? "selected" : "";
						$elem_peri_ne_OD = ($elem_peri_ne_eye=="OD") ? "selected" : "";
						$elem_peri_ne_OS = ($elem_peri_ne_eye=="OS") ? "selected" : "";

						$str .="<div class=\"peritab\" >".
							"
							<input type=\"checkbox\"  name=\"elem_periNotExamined\" value=\"1\" ".$elem_periNotExamined."  onclick=\"checkPneEye();savePNE();\" >
								Peri not ex.
							<select name=\"elem_peri_ne_eye\" onchange=\"savePNE();\">
								<option value=\"\"></option>
								<option value=\"OU\" ".$elem_peri_ne_OU.">OU</option>
								<option value=\"OD\" ".$elem_peri_ne_OD.">OD</option>
								<option value=\"OS\" ".$elem_peri_ne_OS.">OS</option>
							</select>
							".
							"</div>";
					}
					*/

					$str .="</td>";
					$str .="</tr>";

					//$str_xml_se.="</header_info>";

				}
			}



			if($se_nm_var == "Drawing"){ //This will not work now as drawing is stopped above.

				//Color
				//$str .=	"<td class=\"sumod ".$bgDraw."\" ></td>";
				//$str .=	"<td class=\"sumos ".$bgDraw."\" ></td>";

			}else{
				$str .= "<tr>";
				$str .=	"<td id=\"".$elem_summOd."\" class=\"sumod ".$cOd." ".$css_od."\" ".$strColspan." ".$js_od." ".$mn_od." >".$sumod.
						"</td>";

				if($enm != "EOM"){
				$str .=	"<td id=\"".$elem_summOs."\" class=\"sumos ".$cOs." ".$css_os."\" ".$js_os." ".$mn_os." >".$sumos.
						"</td>";
				}
				$str .="</tr>";

				/*
				$str_xml_se.="<summary>";
					$tmpod=trim("".$cOd." ".$css_od."");
					$tmpos=trim("".$cOs." ".$css_os."");
					$str_xml_se.="<od class=\"".$tmpod."\">".$sumod."</od>";
					if($enm != "EOM"){$str_xml_se.="<os class=\"".$tmpos."\">".$sumos."</os>";}
				$str_xml_se.="</summary>";
				*/

				$str_xml_se["summary"]=array();
				$tmpod=trim("".$cOd." ".$css_od."");
				$tmpos=trim("".$cOs." ".$css_os."");

				$str_xml_se["summary"]["od"]=array("value"=>$sumod, "class"=>$tmpod);
				if($enm != "EOM"){
					$str_xml_se["summary"]["os"]=array("value"=>$sumos, "class"=>$tmpos);
				}

			}

			//
			//if(!empty($str_xml_se)){$str_xml_se="<sub_exam>".$str_xml_se."</sub_exam>";}
			//$str_xml .= $str_xml_se;

			if(count($str_xml_se)>0){ $str_xml["exam"]["sub_exams"][] = $str_xml_se; }
			///$str_xml["sub_exam"][] = $str_xml_se;
			//$str_xml["sub_exams"] = array_merge($str_xml["sub_exams"], $str_xml_se);


			}// end loop


		$str .= "</table>";
		$str .= "</div>";

		//
		//$str_xml .= "</sub_exams>";
		//$str_xml .="</exam>";
		//$str_xml["exams"] = array_merge($str_xml["exams"], $str_xml["sub_exams"]);

		//Purgeed Records ------------
		/*
		if(!empty($htmPurge)){
			$str .= $htmPurge;
		}
		*/
		//Purgeed Records ------------

		//
		if($flgRet=="html"){ return $str; }
		else{	return count($str_xml>0) ? serialize($str_xml):""; }

	}

	function getDrwThumb($enm_main,$pth, $idocid="",$flgprvdraw="", $strLMB="", $flgApp=""){
		//require_once(dirname(__FILE__)."/common/SaveFile.php");
		$strDesc = $pthDrwingImageW = "";
		$oSaveFile = new SaveFile($_SESSION["patient"]);

		if(!empty($pth) && strpos($pth, "_b.png")!==false){
			$pth_s=str_replace("_b.png","_s.png",$pth);
		}else if(!empty($pth)){
			$pth_s=str_replace(".png","_s.png",$pth);
		}

		if(file_exists($pth_s)){
			$pthDrwingImage =$pth_s;
			$pthDrwingImage = $oSaveFile->getFilePath($pthDrwingImage, $type="w2");
		}else{
			$pthDrwingImage = $oSaveFile->createThumbs($pth,"",265,169);
			$pthDrwingImage = $oSaveFile->getFilePath($pthDrwingImage, $type="w");
		}

		if(!empty($pthDrwingImage)){
			if($flgApp==1){

				if(strpos("http://",$pthDrwingImage)===false){
					$tmp = "http" . (($_SERVER['SERVER_PORT']==443) ? "s://" : "://") . $_SERVER['HTTP_HOST'];
					$pthDrwingImage = $tmp."".$pthDrwingImage;
				}

				return array("docId"=>$idocid, "url"=>$pthDrwingImage, "LMB"=>$strLMB[1],"Date"=>$strLMB[0]);
			}else{
				//$pthDrwingImage=base64_encode($pthDrwingImage);
				//$strDesc .="<img class=\"summ_drw\" src=\"common/requestHandler.php?elem_formAction=showImg&img=".$pthDrwingImage."\" alt=\"drawing\" onclick=\"openPW('".$enm_main."','draw')\" />";
				$strDesc .="<img class=\"summ_drw\" src=\"".$pthDrwingImage."\" alt=\"drawing\" width=\"265\" height=\"169\" />"; //onclick=\"openPW('".$enm_main."','draw')\"
				/*
				if(!empty($flgprvdraw)){ $css=""; $divinr="<div class=\"greyscale\"></div>"; }else{ $divinr=$css=""; }
				$strDesc .="<div class=\"wvprvdraw\" onclick=\"openPW('drawingpane','".$idocid."')\">".$divinr."<img class=\"summ_drw\" src=\"".$pthDrwingImage."\" alt=\"drawing\" />".$strLMB."</div>";
				*/
			}
		}

		return $strDesc;
	}

	//
	function insertFindingLinks2Sum_exe($exm, $nm, $sum_od, $arr_tm, $ar_ee_find_full, $ar_ee_finding,$cOd){

		$sep = ($exm=="Pupil" || $exm=="External") ? "." : ";";
		$ar_sum_od = explode($sep, $sum_od);
		$len_sum = count($ar_sum_od);

		for($i=0;$i<$len_sum;$i++){
			$tmp_sum_od =  ""; //$tmp_sum_os =
			$tmp_sum_od = (isset($ar_sum_od[$i])) ? trim($ar_sum_od[$i]) : "" ;

			//
			if(empty($tmp_sum_od) ){ continue; } //

			//if(count($arr_tm) > 1){ //why it was 1
			if(count($arr_tm) > 0){
				$ar_notsb_ex=array();
				$ar_sb_ex=array();
				$ar_sb_ex_w_nm=array();

				//check sub exams first
				foreach($arr_tm as $key => $val){

					if($nm=="Lens" && $val=="IOL"){ continue; } //

					//check in ee_findings
					if(isset($ar_ee_find_full) && $ar_ee_finding){
						$tmp = array_search($val, $ar_ee_finding);
						if($tmp !== false){
							$val = $ar_ee_find_full[$tmp];
						}
					}
					//--

					if(strpos($val, "/")===false){$ar_notsb_ex[]=$val;continue;}else{$ar_sb_ex[]=$val;}
					if(strpos($val, $exm."/")!==false){ $ar_sb_ex_w_nm[]=$val;  }

					$val_chk = ($exm=="Pupil" || $exm=="External") ? $val : " ".$val;

					$tmp_unset=0;
					if($cOd=="bgWhite"){
						if(!empty($tmp_sum_od) && strpos($tmp_sum_od, $val_chk)!==false ){
							$tmp_sum_od = str_replace($val_chk," <b class=\"finding\" data-sen=\"".$nm."\"  >".$val."</b>", $tmp_sum_od);
							$tmp_unset=1;

						}
					}

					//unset finding
					if(!empty($tmp_unset)){ unset($arr_tm[$key]); }
				}

				//
				if(strpos($tmp_sum_od,"data-sen")===false){
				if(count($ar_notsb_ex)>0){
					foreach($ar_notsb_ex as $key => $val){
						if(strpos($val, $exm."/")!==false){ $ar_sb_ex_w_nm[]=$val;  }

						$tmp_unset=0;$val_sfx="";
						$val_chk= ($val=="Comments:"||$exm=="Pupil" || $exm=="External") ? "".$val  : " ".$val;

						if($exm=="Pupil" && $val!="Comments:"){
							$val_chk= "".$val.":";
							$val_sfx=":";
						}

						if($cOd=="bgWhite"){
							if(!empty($tmp_sum_od) && strpos($tmp_sum_od, $val_chk)!==false && strpos($tmp_sum_od,"data-sen")===false  ){
								$tmp_sum_od = str_replace($val_chk," <b class=\"finding\" data-sen=\"".$nm."\"  >".$val."</b>".$val_sfx."", $tmp_sum_od);
								$tmp_unset=1;

							}
						}

						//unset finding
						if(!empty($tmp_unset)){
							$index = array_search($val, $arr_tm);
							if($index!==false){ unset($arr_tm[$index]); }
						}
					}
				}
				}

				if(strpos($tmp_sum_od,"data-sen")===false){
				if(count($ar_sb_ex_w_nm)>0){
					foreach($ar_sb_ex_w_nm as $key => $val){
						$val_org = $val;
						$val = str_replace($exm."/", "", $val);
						$val_chk= ($exm=="Pupil" || $exm=="External") ? "".$val  : " ".$val;

						$tmp_unset=0;
						if($cOd=="bgWhite"){
							if(!empty($tmp_sum_od) && strpos($tmp_sum_od, $val_chk)!==false && strpos($tmp_sum_od,"data-sen")===false  ){
								$tmp_sum_od = str_replace($val_chk," <b class=\"finding\" data-sen=\"".$nm."\" data-symp-full=\"".trim($val_chk)."\"  >".trim($val_chk)."</b>", $tmp_sum_od);
								$tmp_unset=1;

							}
						}

						//unset finding
						if(!empty($tmp_unset)){
							$index = array_search($val_org, $arr_tm);
							if($index!==false){ unset($arr_tm[$index]); }
						}
					}
				}
				}

				//
				if(strpos($tmp_sum_od,"data-sen")===false){
				if(count($ar_sb_ex)>0){
					foreach($ar_sb_ex as $key => $val){
						$val_chk= ($exm=="Pupil" || $exm=="External") ? "".$val  : " ".$val;

						$tmp_unset=0;
						if($cOd=="bgWhite"){
							if(!empty($tmp_sum_od) && strpos($tmp_sum_od,"data-sen")===false ){
							if(strpos($tmp_sum_od, $val_chk)!==false){
								$tmp_sum_od = str_replace($val_chk," <b class=\"finding\" data-sen=\"".$nm."\"  >".trim($val_chk)."</b>", $tmp_sum_od);
								$tmp_unset=1;
							}
							else if(strpos($val, "/")!==false && $exm!="Gonio" ){ //will not work for gonio inner findings: non clerity issue
								$ar_val = explode("/",$val);
								$val_last = array_pop($ar_val);
								$val_last= ($exm=="Pupil" || $exm=="External") ? "".$val_last  : " ".$val_last;
								$excp = (($exm=="External" && $val_last == "Edema")) ? 1 : 0;	//||($exm=="SLE" && $val_last == "Stromal")
								if(strpos($tmp_sum_od, $val_last)!==false && $excp==0){
									$tmp_sum_od = str_replace($val_last," <b class=\"finding\" data-sen=\"".$nm."\" data-symp-full=\"".$val."\" >".trim($val_last)."</b>", $tmp_sum_od);
									$tmp_unset=1;
								}
							}
							}
						}

						//unset finding
						if(!empty($tmp_unset)){
							$index = array_search($val, $arr_tm);
							if($index!==false){ unset($arr_tm[$index]); }
						}
					}
				}
				}
			}

			if(!empty($tmp_sum_od)){ $ar_sum_od[$i] = $tmp_sum_od ; }

		} //End for

		if(count($ar_sum_od) > 0){ $sum_od = implode($sep." ", $ar_sum_od);  }
		return $sum_od;
	}

	function apd_red($str){
		$tptrn = '/APD\:\s*(Trace)?\s*(1\+)?\s*(2\+)?\s*(3\+)?\s*(4\+)?\s*(RAPD)?\s*(\.)?/';

		if(preg_match($tptrn, $str, $mtch)){
			$tmp = $mtch[0];
			$tmp = str_replace("APD:", "", $tmp);
			$tmp = "APD: <span class=\"apd_sum\">".$tmp."</span>";
			$str = preg_replace($tptrn, $tmp, $str);
		}

		return $str;
	}

	//
	function insertFindingLinks2Sum($exm,$nm, $sum_od,$sum_os, $cOd, $cOs){
		global $arrMain_examswise;
		include_once(dirname(__FILE__)."/exam_options.php");
		if($exm=="L&amp;A")$exm = "LA";
		else if($exm=="IOP/Gonio")$exm = "Gonio";
		else if($exm=="Fundus Exam")$exm = "Fundus";
		$arr_tm = array();
		if($exm != "LA" && $exm != "SLE" && $exm != "Fundus"){
			$arr_tm = $arrMain_examswise[$exm];
		}else{

			//getExam Extention findings --
			$oExmXml = new ExamXml();
			list($ar_ee_find_full, $ar_ee_finding) = $oExmXml->get_exm_ext_findings($exm, $nm);

			if(count($ar_ee_finding)>0){
				$arr_tm = array_merge($arr_tm, $ar_ee_finding);
			}
			//getExam Extention findings --
			//--
			$se_nm=$se_nm_var=$nm;
			if($se_nm=="Lid Position"){
				$se_nm_var="Lids Position";
			}else if($se_nm=="Ant. Chamber"){
				$se_nm_var="Ant Chamber";
			}else if($se_nm=="Iris & Pupil"){
				$se_nm_var="Iris";
			}else if($se_nm=="Retinal Exam"){
				$se_nm_var="Retinal";
			}
			//--
			$arrtmp = $arrMain_examswise[$exm][$se_nm_var];
			if(is_array($arrtmp) && count($arrtmp)>0){
				$arr_tm = array_merge($arr_tm, $arrtmp);
			}
		}

		$arr_tm[] = "Comments:";

		//if($nm=="Lids"){
		//echo "<pre>";
		//print_r($arr_tm);
		//echo "</pre>";
		//}

		// sort based on string length ---
		array_multisort(array_map('strlen', $arr_tm), SORT_DESC, $arr_tm);
		// sort based on string length ---


		//
		if(!empty($sum_od)){ $sum_od=" ".$sum_od; }
		if(!empty($sum_os)){ $sum_os=" ".$sum_os; }
		$sum_os_org=$sum_os;
		$str_dbg="";

		//check IOL in good Position
		$arrExceptional = array("IOL in good position"=>"IXLingoodposition");
		foreach($arrExceptional as $key_srch => $val_rep){
			$sum_od=str_replace($key_srch, $val_rep, $sum_od);
			$sum_os=str_replace($key_srch, $val_rep, $sum_os);
		}

		//stoped
		if($exm=="Pupil"||$exm=="Gonio"||$exm=="External"||$exm=="LA" || $exm == "SLE" || $exm == "Fundus"){ $cOd = $cOs="bgWhite"; }

		//if($exm=="Pupil" || $exm=="External"){ return array($sum_od, $sum_os); }

		//--


		//--

		//*
		/*
		if($se_nm=="Retinal Exam"){
			echo "<pre>";
			print_r($arr_tm);
			print_r($ar_sum_od);
		}
		*/
		//*/

		//APD in Red: if Positive :: APD: Trace 1+ 2+ 3+ 4+ RAPD
		if($exm=="Pupil"){
			$sum_od = $this->apd_red($sum_od);
			$sum_os = $this->apd_red($sum_os);
		}
		//--

		//--
		$sum_od = $this->insertFindingLinks2Sum_exe($exm,$nm, $sum_od, $arr_tm, $ar_ee_find_full, $ar_ee_finding,$cOd);
		$sum_os = $this->insertFindingLinks2Sum_exe($exm,$nm, $sum_os, $arr_tm, $ar_ee_find_full, $ar_ee_finding,$cOs);

		//

		//if($exm=="Fundus"){
		//	$sum_od="RESR".print_r($arrMain_examswise,1)."";
			//$sum_od="RESR".print_r($arr_tm,1)."";
			//$sum_os=$sum_os_org."<br/>--</br><xmp>".$sum_os."</xmp>";
		//}

		//reset Exceptional
		foreach($arrExceptional as $key_rep => $val_srch){
			$sum_od=str_replace($val_srch, $key_rep, $sum_od);
			$sum_os=str_replace($val_srch, $key_rep, $sum_os);
		}

		return array($sum_od, $sum_os);
	}

	//Get Summary HTML
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
		$draw_type = $arr["draw_type"];
		$lens_used = $arr["lens_used"];

		//For Variable
		if($enm=="L&amp;A")$enm_var = "LA";
		else if($enm=="IOP/Gonio")$enm_var = "Gonio";
		else if($enm=="Fundus Exam"||$enm=="Fundus")$enm_var = "RV";
		else if($enm=="Refractive Surgery")$enm_var = "Ref_Surg";

		//One Eye
		if($oneeye[0]=="Poor-view"){$oneeye[0]="Poor View";}
		if($oneeye[0]=="Poor View" && $enm_var!="RV"){ //&& $enm_var!="SLE"
			$oneeye=array();
		}

		//--------------

		///* ----------


		/*Tufts: Pen Light exam check box should be moved to the Exam pages
		//Sle : Pen Light --
		if($enm=="SLE"){
			$tmpChecked = !empty($arr["penLight"]) ? "checked=\"checked\"" : "" ;
			$exm_header .= "<label style=\"margin-right:8px;\">".
						"<input type=\"checkbox\" name=\"elem_penLight_sle\" value=\"1\" onclick=\"slePenLight()\" ".$tmpChecked.">Pen Light".
					"</label>";
		}
		//Sle : Pen Light --
		*/

		//Pupil : Pharama	--
		if($enm=="Pupil"){
			if(trim($arr["other_values"])=="Pharmacologically dilated on arrival,"){
				$arr["other_values"]["elem_pharmadilated"] = "Pharmacologically dilated on arrival";
			}else{
				$arr["other_values"] = unserialize($arr["other_values"]);
			}
		}
		//Pupil : Pharama	--

		//<!-- Reset -->

		/*
		// Prv Button - I do not think we need Prv button.
		//<!-- Prv -->
		// NO Prv for Ref Sx
		if($enm != "Refractive Surgery"){
		$str .=	"".
				"<input type=\"button\" id=\"elem_btnPrv".$enm_var."\" class=\"prv\" value=\"Prv\" onClick=\"setPrvValues('".$enm_var."')\"".
				" > ";
		}
		*/

		//<!-- WNL -->
		//No Wnl for IOP/Gonio && Refractive Surgery
		$btnWNL="";
		if($enm != "IOP/Gonio" && $enm != "Refractive Surgery"){
			//NO OD+OS Wnl for EOM and External
			if($enm !="EOM" && $enm != "External"){
				$mouseEvt = " onmouseover=\"showWnlOpts(1,1,'".$enm_var."')\" onmouseout=\"showWnlOpts(0,1)\" ";
			}else{
				$mouseEvt = "";
			}

		$btnWNL =	"".
				"<input type=\"button\" id=\"elem_btnWnl".$enm_var."\" class=\"btn wnl\" value=\"WNL\" onClick=\"setWnlValues('".$enm_var."')\"".
				"	".$mouseEvt." > ";
		}
		//<!-- No Change -->
		//Prv Button - I do not think we need Prv button.  Just Line WNL, if they select NC then it makes the selected eye white marking it as No change.
		//However if they select it again then Previous (i.e. last DOS) findings are shown.  Therefore NC is not a check box but a button just like WNL

		$btn_iop_grph="";
		if($enm =="IOP/Gonio"){
			$btn_iop_grph= "<a href=\"javascript:void(0)\" >
								<img id=\"img_flowsheet\" src=\"".$GLOBALS['webroot']."/library/images/space.gif\"  onClick=\"show_iop_graphs()\" width=\"26\" height=\"25\" alt=\"img_flowsheet\" />
					</a>";
		}

		$btn_nochange="";
		$tmp = (!empty($nochange)) ? "UNDO" : "NC";
		$btn_nochange .=	"".
				//"<input type=\"checkbox\" id=\"elem_noChange".$enm_var."\" name=\"elem_noChange".$enm_var."\" value=\"1\" class=\"checkbox\" ".
				//"	onClick=\"autoSaveNoChange('".$enm_var."')\" ".
				//"	".$nochange." >".
				//"<label for=\"elem_noChange".$enm_var."\">NC</label>".
				"<input type=\"button\" id=\"elem_btnNoChange".$enm_var."\" class=\"prv btn nc\" value=\"".$tmp."\" onClick=\"autoSaveNoChange('".$enm_var."')\"";
		if($enm !="EOM") $btn_nochange .=	"	onmouseover=\"showWnlOpts(1,2,'".$enm_var."')\" onmouseout=\"showWnlOpts(0,2)\"";
		$btn_nochange .=	" > ";

		$btn_hx="";
		if(!empty($moeMN)){
		//$exm_header .= "<label id=\"lblHx".$enm_var."\" class=\"hxlbl\" ".$moeMN." >Hx</label>";
		$btn_hx .= "<input id=\"lblHx".$enm_var."\" class=\"btn hx\" type=\"button\" value=\"Hx\" ".$moeMN.">";
		}

		//Exam Headar --
		$exm_header = "<div class=\"einfo\">".
				"<div class=\"hdr\">".
				"<label onclick=\"openPW('".$enm_var."')\">".$enm."</label>";
		$exm_header .=$btnWNL.$btn_iop_grph.$btn_nochange.$btn_hx;
		$exm_header .= "</div>";

		/**
		$exm_header .=	$seList;
		**/

		$exm_header .=	"</div>";

		// -------------  */


		///IOP :: Dilation & Ansthetic ------------
		$dilatDropAllergy = $anes = $dilate = $ood= "";
		if($enm == "IOP/Gonio"){
			if(!empty($arr["Dilation"]["PtAllergic"])){
				$dilatDropAllergy = "<label id=\"ptAlrgic\">Dilation Drop Allergies</label>";
			}

			if(!empty($arr["Dilation"]["Anesthetic"])) {
				$anes = 	"<label id=\"spAnesTime\">".
						//"Anesthetic ".
							((empty($arr["Dilation"]["bgColor_IOP"])) ? "<a href=\"javascript:void(0);\" onclick=\"updateADTime('Anes')\"  >" : "" ).
							$arr["Dilation"]["Anesthetic"].
							((empty($arr["Dilation"]["bgColor_IOP"])) ? "</a>" : "" ).
						"</label>";
			}

			if(!empty($arr["Dilation"]["Dilate"])) {
				if($arr["Dilation"]["Dilate"]=="No Dilation"||$arr["Dilation"]["Dilate"]=="Refuse Dilation"){
					$dilate = $arr["Dilation"]["Dilate"];
				}else{
					$dilate = 	"".
							//"Dilation ".
								((empty($arr["Dilation"]["bgColor_dialation"])) ? "<a href=\"javascript:void(0);\" onclick=\"updateADTime('Dial')\" >" : "" ).
								$arr["Dilation"]["Dilate"].
								((empty($arr["Dilation"]["bgColor_dialation"])) ? "</a>" : "" );
				}
				$dilate = "<label id=\"spDialTime\">".$dilate."</label>";
			}

			if(!empty($arr["Dilation"]["OOD"])) {
				$ood = 	"<label id=\"spOODTime\">".
						//"Anesthetic ".
							((empty($arr["Dilation"]["bgColor_ood"])) ? "<a href=\"javascript:void(0);\" onclick=\"updateADTime('OOD')\" >"  : "" ).
							$arr["Dilation"]["OOD"].
							((empty($arr["Dilation"]["bgColor_ood"])) ? "</a>" : "" ).
						"</label>";
			}

		}

		///IOP :: Dilation & Ansthetic ------------

		//Summary --
		$str = "";
		$str .=	"<div class=\"esum\">".
					"<table class=\"table table-bordered table-hover table-striped\">";

						$len = count($arrSE);
						$rowspan = (count($oneeye)>0) ? $len+1 : $len;
						if(($enm_var == "LA" || $enm_var == "Gonio" || $enm_var == "RV" || $enm_var == "SLE") ||
							($enm=="SLE" && !empty($arr["penLight"]))||
							($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival")){
								$rowspan +=1;
						}

						//Desc --
							//Color
							if(empty($bgColor) && $desc!=""){
								$tmp="bgWhite";
							}else{
								$tmp="";
							}

							//Drawing Processing--
							if($enm_var == "RV"||$enm_var == "LA"||$enm_var == "SLE"||$enm_var == "Gonio"||$enm_var == "EOM" || $enm_var == "External"){

								$tmp="";
								if(!empty($drawdocId)){//iDocDrawing
									$OBJDrawingData = new CLSDrawingData();
									global $objImageManipulation;
									$objImageManipulation = new CLSImageManipulation();
									list($exm_idDrwingImage,$drawDate,$idLMB,$dtLMB) =$OBJDrawingData->getIDOCdrawingsImage($enm_var,$primaryId=$drawdocId,"2",$arr["flgGetDraw"]) ;
									$dtLMB = wv_formatDate($dtLMB);
									$drawDate = wv_formatDate($drawDate);
									if($exm_idDrwingImage!=""){
										$strDrw .= $this->getDrwThumb($enm_var,$exm_idDrwingImage);
									}else{
										//$strDrw .= "<a href=\"javascript:void(0);\" onclick=\"openPW('".$enm_var."','draw')\">No Drawing</a>";
									}

								}else if(!empty($drawapp[0]) && (strpos($drawapp[0],"0-0-0:;")===false)){//Applet Drawing

									$im = imagecreatefromstring(base64_decode($drawapp[0]));
									if ($im != false) {
										$fileNameTempOldData = dirname(__FILE__)."/common/tmp/".time()."-".session_id().".png";
										imagepng($im,$fileNameTempOldData);
										$strDrw .= $this->getDrwThumb($enm_var,$fileNameTempOldData);
										unlink($fileNameTempOldData);
										$drawDate=$arr["examdate"];
										//if(empty($bgColor))$tmp="bgWhite";
									}

								}else{ //No Drawing
									//$strDrw .= "<a href=\"javascript:void(0);\" onclick=\"openPW('".$enm_var."','draw')\">No Drawing</a>";
								}
								// set draw color
								$bgDraw="";
								if($exm_idDrwingImage!=""||$im != false){
									if(empty($bgColor)&&(!empty($arr["drawSE"][0])||!empty($arr["drawSE"][1]))){
										$bgDraw="bgWhite";
									}
								}
							}
							//Drawing Processing--

							$strDesc = "<td rowspan=\"".$rowspan."\" class=\"edesc ".$tmp." ".$bgDraw."\">";

							//OCT21,2011:: Is it possible to show the thumbnail of the drawings for L&A, SLE and Fundus
							if($enm_var == "RV"||$enm_var == "LA"||$enm_var == "SLE"||$enm_var == "Gonio"||$enm_var == "EOM" || $enm_var == "External"){

							//Show Drawing Here --
							$strDrw = trim($strDrw);
							if(!empty($strDrw)){

								$strDesc .="<div class=\"wvprvdraw\" onclick=\"openPW('".$enm_var."','draw')\">";

								//add bggray
								if($bgDraw!="bgWhite"){
									$strDesc.="<div class=\"greyscale\"></div>";
								}

								$strDesc .= $strDrw;
								if(isset($drawDate)&&!empty($drawDate)){

									//LMB--
									$strLMB="";
									if(isset($idLMB)&&!empty($idLMB)){
										$ousr = new User($idLMB);
										$tmpLMB = $ousr->getName(2);
										//$tmpLMB = getUserFirstName($idLMB,2);
										$strLMB="LMB ".$tmpLMB[2];
										if(!empty($dtLMB)) {$strLMB.=" ".$dtLMB;}
									}
									//LMB--

									$strDesc .= "<label>".date(phpDateFormat(), strtotime(str_replace('-','/',$drawDate)))."<br/>".$strLMB."</label>";
								}

								$strDesc.="</div>";
							}

							//Show Drawing Here --

							}else{
								/*
								$strDesc .="<textarea name=\"elem_txtDesc_".$enm_var."\" onChange=\"setExamDescChange(this)\">".$desc."</textarea>".
										"<input type=\"hidden\" name=\"elem_txtDesc_".$enm_var."_changed\" value=\"0\">";
								*/
							}
							//$strDesc .="<div></div>";//Used to hgt cell
							$strDesc .="</td>";
							//$strDesc = ""; //stopped drawing column  : 08-04-2014
						//Desc --

						//Pen Light--
						if($enm=="SLE" && !empty($arr["penLight"])){

							//Color
							if(empty($bgColor)){
								$tmp="bgWhite";
							}else{
								$tmp="";
							}

							$str .= "<tr>";
							$str .= "<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>";
							$str .=	"<td class=\"prph sumod ".$tmp."\" > PenLight Exam "."</td>";
							$str .=	"<td class=\"prph sumos ".$tmp."\"> PenLight Exam "."</td>";
							$str .= "".$strDesc;
							$str .="</tr>";

							$strDesc="";//Empty Desc after use
							$exm_header = ""; //Empty after use

						}
						//Pen Light--

						// Pharmacologically dilated on arrival --
						if($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival"){

							//Color
							if(empty($bgColor)){
								$tmp="bgWhite";
							}else{
								$tmp="";
							}

							$str .= "<tr>";
							$str .=	"<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>";
							if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
								$str .=	"<td class=\"prph sumod ".$tmp."\" > Pharmacologically dilated on arrival "."</td>";
							}else{
								$str .=	"<td class=\"prph sumod\" ></td>";
							}
							if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
								$str .=	"<td class=\"prph sumos ".$tmp."\"> Pharmacologically dilated on arrival "."</td>";
							}else{
								$str .=	"<td class=\"prph sumos\" ></td>";
							}
							$str .= "".$strDesc;
							$str .="</tr>";

							$strDesc="";//Empty Desc after use
							$exm_header = ""; //Empty after use

						}
						// Pharmacologically dilated on arrival --

						//One Eye --
						if(count($oneeye)>0){
							if($oneeye[1]=="OU" || $oneeye[1]=="OD"){
								$on_nm_od = $oneeye[0];
								if($oneeye[2]==1){
									$cOd = "bgWhite";
								}else{
									$cOd = (!empty($arr["exm_flg_se"][0])) ? "bgWhite" : "";
								}
							}

							if($oneeye[1]=="OU" || $oneeye[1]=="OS"){
								$on_nm_os = $oneeye[0];
								if($oneeye[2]==1){
									$cOs = "bgWhite";
								}else{
									$cOs = (!empty($arr["exm_flg_se"][1])) ? "bgWhite" : "";
								}
							}

							$str .= "<tr>";
							$str .= "<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>";
							$str .=	"<td class=\"prph sumod ".$cOd."\" >".$on_nm_od.
									"</td>";
							$str .=	"<td class=\"prph sumos ".$cOs."\">".$on_nm_os.
									"</td>";
							$str .= "".$strDesc;
							$str .="</tr>";

							$strDesc="";//Empty Desc after use
							$exm_header = ""; //Empty after use
						}
						//--

						//Header Info ----------------------
							//Add header bar for LA, Gonio, RV SLE
							if(!empty($exm_header) && ($enm_var == "LA" || $enm_var == "Gonio" || $enm_var == "RV" || $enm_var == "SLE")){

								//Color
								if(empty($bgColor)){
									$tmp="bgWhite";
								}else{
									$tmp="";
								}

								$str .= "<tr>";
								$str .= "<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>";
								$str .= "<td class=\"sumod  ".$tmp."\"></td>";
								$str .= "<td class=\"sumos ".$tmp."\"></td>";
								$str .= "".$strDesc;
								$str .= "</tr>";

								$strDesc="";//Empty Desc after use
								$exm_header = ""; //Empty after use

							}
						//Header Info ----------------------

						for($i=0;$i<$len;$i++){
						$sumod = trim($arrSE[$i]["sumod"]);
						$sumos = trim($arrSE[$i]["sumos"]);
						$cnm=$cOd=$cOs=$se_nm="";

						//2.when protheses is checked, you are not able to put anything in that eye for prescription or medical reason. Could you pls fix it that you can type everything into that eye as if it was not checked.
						/*
						//OneEye Prosthisis Check--
						if(count($oneeye)>0 && $oneeye[0]=="Prosthesis"){
							if($oneeye[1]=="OD"){
								$sumod = "";
							}else if($oneeye[1]=="OS"){
								$sumos = "";
							}
						}*/

						//if($sumod!="" || $sumos!=""){ // Stopped check so that all exam appear in list
							$se_nm = $arrSE[$i]["nm"];
							if($sumod!="" && !empty($arrSE[$i]["cOd"])){
								$cOd = "bgWhite";
							}
							if($sumos!="" && !empty($arrSE[$i]["cOs"])){
								$cOs = "bgWhite";
							}
							if(!empty($arrSE[$i]["cnm"])){
								$cnm = "color_red";
							}

							$se_nm_var=$se_nm;
							if($se_nm=="Lid Position"){
								$se_nm_var="LidPos";
							}else if($se_nm=="Lacrimal System"){
								$se_nm_var="Lac";
							}else if($se_nm=="Conjunctiva"){
								$se_nm_var="Conj";
							}else if($se_nm=="Cornea"){
								$se_nm_var="Corn";
							}else if($se_nm=="Ant. Chamber"){
								$se_nm_var="Ant";
							}else if($se_nm=="Iris & Pupil"){
								$se_nm_var="Iris";
							}else if($se_nm=="Blood Vessels" || $se_nm=="Vessels"){
								$se_nm_var="BV";
							}else if($se_nm=="Optic Nerve"){
								$se_nm_var="Optic";
							}else if($se_nm=="Ophth. Drops"){
								$se_nm_var="OOD";
							}else if($se_nm=="Retinal Exam"){
								$se_nm_var="Retinal";
							}

							//Stop Drawing to be last row --
							if($se_nm_var == "Drawing"){
									continue;
							}
							//Stop Drawing to be last row --

							$elem_summOd = "elem_sumOd".$se_nm_var;
							$elem_summOs = "elem_sumOs".$se_nm_var;
							$js_od = $arrSE[$i]["arcJsOd"];
							$js_os = $arrSE[$i]["arcJsOs"];
							$css_od = $arrSE[$i]["arcCssOd"];
							$css_os =  $arrSE[$i]["arcCssOs"];
							$mn_od = $arrSE[$i]["mnOd"];
							$mn_os = $arrSE[$i]["mnOs"];

							//echo "".$js_od." ".$mn_od."";

						//}

						//increase height of TR when there is drawing and one Eye only in case of external exam--
						if(count($oneeye)>0 && $enm_var == "External" && !empty($strDrw)){
							$css_tr_hgt=" class=\"css_tr_hgt\" ";
						}else{
							$css_tr_hgt="";
						}



						$str .= "<tr ".$css_tr_hgt."   >";

						if($enm_var == "Pupil" || $enm_var == "EOM" || $enm_var == "External" || $enm_var == "Ref_Surg" ){
							$str .= (!empty($exm_header)) ? "<td class=\"edone ehdr leftpanel worshthd\">".$exm_header."</td>" : "<td class=\"edone\"></td>" ;
						}else{
							//First Row, Add Drawing label also
							if($i==0){
								$se_nm_drw = $arrSE[$len-1]["nm"];
							}else{
								$se_nm_drw = "";
							}

							if($se_nm_drw=="Drawing"){
								//*
								$cnm_drw = (!empty($arrSE[$len-1]["cnm"])) ? "color_red" : "color_black";
								//GET link settings
								$arlinkDrw= $this->getSubExamLabel($enm_var, "Drawing", $seList);
								if(($cnm_drw == "color_black") && $arlinkDrw["cls"]=="flgPos"){ ////show red color label if summary exists
									$cnm_drw = "color_red";
								}
								/*
								$strDrw ="<div ".$arlinkDrw["id"]." class=\"drawtab ".$cnm_drw." \" >".
										//"<span class=\"".$arlinkDrw["cls"]."\"></span>".
										"<label ".$arlinkDrw["onclick"]." >".$se_nm_drw."</label>".
										"</div>";
								*/

								$strDrw ="<tr><td ".$arlinkDrw["id"]." class=\"edone esubexam leftpanel sublink ".$cnm_drw." \" >";
										//"<span class=\"".$arlinkDrw["cls"]."\"></span>".
										$td_lens_used="";
										if($enm_var=="RV"){
											$cnm_drw1=$cnm_drw2=$cnm_drw3="";
											if($draw_type==8){ $cnm_drw2=$cnm_drw; }
											else if($draw_type==9){ $cnm_drw3=$cnm_drw; }
											else if($draw_type==0||$draw_type==5){ $cnm_drw1=$cnm_drw; }

											$strDrw .=	"<div class=\"btn-group\">".
												"<button type=\"button\" class=\"btn btn-default btn-xs ".$cnm_drw1."\" onclick=\"openPW('RV','DrawRv')\" >Draw RT</button>".
												"<button type=\"button\" class=\"btn btn-default btn-xs ".$cnm_drw2."\" onclick=\"openPW('RV','DrawRvOn')\" >Draw ON</button>".
												"<button type=\"button\" class=\"btn btn-default btn-xs ".$cnm_drw3."\" onclick=\"openPW('RV','DrawRvMa')\" >Draw MA</button>".
												"</div>";

											//Draw  SUM
											if($enm_var == "RV" && !empty($lens_used) ){
												//echo "INDRAW: ".$bgColor;
												//Color
												if(empty($bgColor)){
													$tmp="bgWhite";
												}else{
													$tmp="";
												}

												$td_lens_used =	"<td class=\"sumod ".$tmp."\" colspan=\"2\" >Lens Used: ".$lens_used."</td>";
											}


										}else{

											$strDrw .="<label ".$arlinkDrw["onclick"]." >".$se_nm_drw."</label>";

										}

										$strDrw .="</td>".$td_lens_used."</tr>";
								//*/

								//if CD
								if($se_nm == "CD"){
									$strCDRV ="<div id=\"dvCDRV\" class=\"formCDRV leftpanel cdform form-inline sublink\" >".
										"<label class=\"od\" >OD</label>".
										"<input type=\"text\" name=\"elem_rvcd_od\" value=\"".$arr["elem_rvcd"]["od"]."\" onblur=\"saveCDRV()\" class=\"form-control\" >".
										"<label class=\"os\" >OS</label>".
										"<input type=\"text\" name=\"elem_rvcd_os\" value=\"".$arr["elem_rvcd"]["os"]."\" onblur=\"saveCDRV()\" class=\"form-control\" >".
										//"<input type=\"button\" name=\"elem_rvcd_btn\" value=\"Save\" class=\"dff_button_sm\" onclick=\"saveCDRV()\">".
										"</div>";
									$cssCDRV	=" tdCDRV ";
								}else{
									$strCDRV="";
									$cssCDRV	="";
								}

								//GET link settings
								$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);
								if(empty($cnm) && $arlink["cls"]=="flgPos"){ //show red color label if summary exists
									$cnm = "color_red";
								}

								//display name
								$tmp_se_nm = $se_nm;
								if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }

								$str .="<td ".$arlink["id"]." class=\"edone esubexam leftpanel sublink ".$cnm." ".$cssCDRV." \" >".
										//"<span class=\"".$arlink["cls"]."\"></span>".
										"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>".
									//$strDrw.
									$strCDRV.
									"</td>";

							}else{
								//GET link settings
								$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);
								if(empty($cnm) && $arlink["cls"]=="flgPos"){ ////show red color label if summary exists
									$cnm = "color_red";
								}

								//display name
								$tmp_se_nm = $se_nm;
								if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }

								$str .="<td ".$arlink["id"]." class=\"edone esubexam leftpanel sublink ".$cnm." \" >".
										//"<span class=\"".$arlink["cls"]."\"></span>".
										"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>";
								if($se_nm_var=="Anesthetic") {$str .="".$anes;}
								if($se_nm_var=="Dilation") {
									if(!empty($dilate)){$str .="".$dilate;}else if(!empty($dilatDropAllergy)){$str .="".$dilatDropAllergy;}
								}
								if($se_nm_var=="OOD") { $str .= $ood; }

								//if last row and RV
								if($enm_var == "RV" && isset($arrSE[$i]["elem_periNotExamined"]) && isset($arrSE[$i]["elem_peri_ne_eye"])){
									$var_peri = ($arrSE[$i]["nm_2"] == "Peri") ? "_peri" : "";
									$elem_periNotExamined = $arrSE[$i]["elem_periNotExamined"];
									$elem_peri_ne_eye = $arrSE[$i]["elem_peri_ne_eye"];

									$elem_periNotExamined = ($elem_periNotExamined==1) ? "checked" : "";
									$elem_peri_ne_OU = ($elem_peri_ne_eye=="OU") ? "selected" : "";
									$elem_peri_ne_OD = ($elem_peri_ne_eye=="OD") ? "selected" : "";
									$elem_peri_ne_OS = ($elem_peri_ne_eye=="OS") ? "selected" : "";

									$str .="<div class=\"peritab leftpanel sublink form-inline\" >".
										"
										<input type=\"checkbox\"  id=\"elem_periNotExamined".$var_peri."\" name=\"elem_periNotExamined".$var_peri."\" value=\"1\" ".$elem_periNotExamined."  onclick=\"checkPneEye(this);savePNE(this);\" class=\"frcb\">
										<label for=\"elem_periNotExamined".$var_peri."\" class=\"frcb\">Peri NE</label>
										<select name=\"elem_peri_ne_eye".$var_peri."\" id=\"elem_peri_ne_eye".$var_peri."\" onchange=\"checkPneEye(this);savePNE(this);\" class=\"form-control\">
											<option value=\"\"></option>
											<option value=\"OU\" ".$elem_peri_ne_OU.">OU</option>
											<option value=\"OD\" ".$elem_peri_ne_OD.">OD</option>
											<option value=\"OS\" ".$elem_peri_ne_OS.">OS</option>
										</select>
										".
										"</div>";
								}

								$str .="</td>";

							}
						}

						if($se_nm_var == "Drawing"){ //This will not work now as drawing is stopped above.
							//Color
							$str .=	"<td class=\"sumod ".$bgDraw."\" ></td>";
							$str .=	"<td class=\"sumos ".$bgDraw."\" ></td>";

						}else{

							//R7: show findings with label to expand --
								list($sumod,$sumos) = $this->insertFindingLinks2Sum($enm,$se_nm,$sumod,$sumos,$cOd, $cOs);
							//--

							//
							if(!empty($strDrw) && $i==$len-2){
								if($enm_var != "RV" || empty($lens_used)){
									$str_row_span=" rowspan=\"2\" ";
								}
							}else{ $str_row_span=""; }

							$str .=	"<td id=\"".$elem_summOd."\" ".$str_row_span." class=\"sumod tablnk ".$cOd." ".$css_od."\" ".$strColspan." ".$js_od." ".$mn_od." >".$sumod.
									"</td>";

							if($enm != "EOM"){
							$str .=	"<td id=\"".$elem_summOs."\" ".$str_row_span." class=\"sumos tablnk ".$cOs." ".$css_os."\" ".$js_os." ".$mn_os." >".$sumos.
									"</td>";
							}
						}

						if(!empty($strDesc)){
							$str .= "".$strDesc;
							$strDesc="";
						}

						$str .="</tr>";
						//if last Row , then add drawing
						if(!empty($strDrw) && $i==$len-2){

								$str .= $strDrw;
								$strDrw="";

						}

						}// end loop

					$str .= "</table>";
					$str .= "</div>";

					//Purgeed Records ------------

					if(!empty($htmPurge)){
						$str .= $htmPurge;
					}

					//Purgeed Records ------------

					//add hidden fields for mandatory
					if(is_array($arr["elem_hidden_fields"]) && count($arr["elem_hidden_fields"])>0){
						foreach($arr["elem_hidden_fields"] as $key=>$val){
							$str .= "<input type=\"hidden\" id=\"".$key."\" name=\"".$key."\" value=\"".$val."\">";
						}
					}
					//--

		//if($enm_var == "RV"){$str = "<xmp>".$str."</xmp>";}

		return $str;
	}


	//GET Purged Summary HTML
	function getSummaryHTML_purged($arr){
		$enm_var = $enm = $arr["ename"];
		$arrSE = $arr["subExm"];
		$seList= isset($arr["seList"]) ? $arr["seList"] : "";
		$nochange = $arr["nochange"]; //(isset($arr["nochange"]) && !empty($arr["nochange"])) ? " checked=\"checked\" " : "";
		$oneeye = (!empty($arr["oe"])) ? explode("~!~",$arr["oe"]):array();
		$desc = trim($arr["desc"]);
		$bgColor = $arr["bgColor"];
		$drawdocId = $arr["drawdocId"];
		$drawapp = $arr["drawapp"];
		$purgerId = $arr["purgerId"];
		$purgeTime = $arr["purgeTime"];

		//For Variable
		if($enm=="L&amp;A")$enm_var = "LA";
		else if($enm=="IOP/Gonio")$enm_var = "Gonio";
		else if($enm=="Fundus Exam"||$enm=="Fundus")$enm_var = "RV";
		else if($enm=="Refractive Surgery")$enm_var = "Ref_Surg";

		//One Eye
		if($oneeye[0]=="Poor View" && $enm_var!="RV"){ //&& $enm_var!="SLE"
			$oneeye=array();
		}

		///* ----------

		//Pupil : Pharama	--
		if($enm=="Pupil"){
			if(trim($arr["other_values"])=="Pharmacologically dilated on arrival,"){
				$arr["other_values"]["elem_pharmadilated"] = "Pharmacologically dilated on arrival";
			}else{
				$arr["other_values"] = unserialize($arr["other_values"]);
			}
		}
		//Pupil : Pharama	--

		// -------------  */

		$str = "";
		$str .=	"".
					"<table class=\"table table-bordered table-hover table-striped\" >";

						//Purge Info --
						$ousr = new User($purgerId);
						$purgeInfo = $ousr->getName(4);
						$purgeInfo .= " on ".wv_formatDate($purgeTime,0,1);

						$str .= "<tr>
								<td class=\"prg_hdr\" colspan=\"4\">Purged by ".$purgeInfo."</td>
								</tr>";

						//Purge Info --

						$len = count($arrSE);
						$rowspan = (count($oneeye)>0) ? $len+1 : $len;
						if(($enm=="SLE" && !empty($arr["penLight"]))||
							($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival")){
							$rowspan +=1;
						}

						//Desc --
							//Color
							if(empty($bgColor) && $desc!=""){
								$tmp="bgWhite";
							}else{
								$tmp="";
							}

							//Drawing Processing--
							if($enm_var == "RV"||$enm_var == "LA"||$enm_var == "SLE"||$enm_var == "Gonio" ||$enm_var == "EOM" || $enm_var == "External"){

								$tmp="";
								if(!empty($drawdocId)){//iDocDrawing

									//echo "HELLLLO".$drawdocId;

									$OBJDrawingData = new CLSDrawingData();
									global $objImageManipulation;
									$objImageManipulation = new CLSImageManipulation();
									list($exm_idDrwingImage,$drawDate) =$OBJDrawingData->getIDOCdrawingsImage($enm_var,$primaryId=$drawdocId,"2") ;

									//echo "<br>-".$exm_idDrwingImage."-<br>";

									if($exm_idDrwingImage!=""){
										$strDrw .= $this->getDrwThumb($enm_var,$exm_idDrwingImage);
									}else{
										//$strDrw .= "<a href=\"javascript:void(0);\" onclick=\"openPW('".$enm_var."','draw')\">No Drawing</a>";
									}

								}else if(!empty($drawapp[0]) && (strpos($drawapp[0],"0-0-0:;")===false)){//Applet Drawing

									$im = imagecreatefromstring(base64_decode($drawapp[0]));
									if ($im != false) {
										$fileNameTempOldData = dirname(__FILE__)."/common/tmp/".time()."-".session_id().".png";
										imagepng($im,$fileNameTempOldData);
										$strDrw .= $this->getDrwThumb($enm_var,$fileNameTempOldData);
										unlink($fileNameTempOldData);
										$drawDate=$arr["examdate"];
										//if(empty($bgColor))$tmp="bgWhite";
									}

								}else{ //No Drawing
									//$strDrw .= "<a href=\"javascript:void(0);\" onclick=\"openPW('".$enm_var."','draw')\">No Drawing</a>";
								}
								// set draw color
								$bgDraw="";
								if($exm_idDrwingImage!=""||$im != false){
									if(empty($bgColor)&&(!empty($arr["drawSE"][0])||!empty($arr["drawSE"][1]))){
										$bgDraw="bgWhite";
									}
								}
							}
							//Drawing Processing--

							$strDesc = "<td rowspan=\"".$rowspan."\" class=\"edesc ".$tmp." ".$bgDraw."\">";

							//OCT21,2011:: Is it possible to show the thumbnail of the drawings for L&A, SLE and Fundus
							if($enm_var == "RV"||$enm_var == "LA"||$enm_var == "SLE"||$enm_var == "Gonio" ||$enm_var == "EOM" || $enm_var == "External"){

							$strDesc .="<div class=\"wvprvdraw\" onclick=\"openPW('".$enm_var."','draw')\">";

							//add bggray
							if($bgDraw!="bgWhite"){
								$strDesc.="<div class=\"greyscale\"></div>";
							}

							//Show Drawing Here --
							$strDrw = trim($strDrw);
							if(!empty($strDrw)){
								$strDesc .= $strDrw;
								if(isset($drawDate)&&!empty($drawDate)){
									$strDesc .= "<label>".$drawDate."</label>";
								}
							}
							//Show Drawing Here --

							$strDesc.="</div>";

							}else{
								/*
								$strDesc .="<textarea name=\"elem_txtDesc_".$enm_var."\" onChange=\"setExamDescChange(this)\">".$desc."</textarea>".
										"<input type=\"hidden\" name=\"elem_txtDesc_".$enm_var."_changed\" value=\"0\">";
								*/
								$strDesc .="<span>".$desc."</span>";
							}
							//$strDesc .="<div></div>";//Used to hgt cell
							$strDesc .="</td>";
							//$strDesc ="";//stopped
						//Desc --

						/*Stopped Headers
						//Header Info ----------------------
							//Add header bar for LA, Gonio, RV SLE
							if($enm_var == "LA" || $enm_var == "Gonio" || $enm_var == "RV" || $enm_var == "SLE" ){

								//Color
								if(empty($bgColor)){
									$tmp="bgWhite";
								}else{
									$tmp="";
								}

								$str .= "<tr>";
								$str .= "<td class=\"edone ehdr\">".$exm_header."</td>";
								$str .= "<td class=\"sumod  ".$tmp."\"></td>";
								$str .= "<td class=\"sumos ".$tmp."\"></td>";
								$str .= "".$strDesc;
								$str .= "</tr>";

								$strDesc="";//Empty Desc after use
							}
						//Header Info ----------------------
						*/

						//Pen Light--
						if($enm=="SLE" && !empty($arr["penLight"])){

							//Color
							if(empty($bgColor)){
								$tmp="bgWhite";
							}else{
								$tmp="";
							}

							$str .= "<tr>";
							$str .=	"<td class=\"edone leftpanel sublink\"></td>";
							$str .=	"<td class=\"prph sumod tablnk ".$tmp."\" ><span> PenLight Exam </span>"."</td>";
							$str .=	"<td class=\"prph sumos tablnk ".$tmp."\"><span> PenLight Exam </span>"."</td>";
							$str .= "".$strDesc;
							$str .="</tr>";

							$strDesc="";//Empty Desc after use

						}
						//Pen Light--

						// Pharmacologically dilated on arrival --
						if($enm=="Pupil" && $arr["other_values"]["elem_pharmadilated"]=="Pharmacologically dilated on arrival"){

							//Color
							if(empty($bgColor)){
								$tmp="bgWhite";
							}else{
								$tmp="";
							}

							$str .= "<tr>";
							$str .=	"<td class=\"edone ehdr leftpanel sublink\">".$exm_header."</td>";
							if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OD"){
								$str .=	"<td class=\"prph sumod tablnk ".$tmp."\" ><span> Pharmacologically dilated on arrival </span>"."</td>";
							}else{
								$str .=	"<td class=\"prph sumod\" ></td>";
							}
							if($arr["other_values"]["elem_pharmadilated_eye"]=="OU"||$arr["other_values"]["elem_pharmadilated_eye"]=="OS"){
								$str .=	"<td class=\"prph sumos tablnk ".$tmp."\"><span> Pharmacologically dilated on arrival </span>"."</td>";
							}else{
								$str .=	"<td class=\"prph sumod\" ></td>";
							}
							$str .= "".$strDesc;
							$str .="</tr>";

							$strDesc="";//Empty Desc after use
							$exm_header = ""; //Empty after use

						}
						// Pharmacologically dilated on arrival --

						//One Eye --
						if(count($oneeye)>0){
							if($oneeye[1]=="OU" || $oneeye[1]=="OD"){
								$on_nm_od = $oneeye[0];
								if($oneeye[2]==1){
									$cOd = "bgWhite";
								}else{
									$cOd = (!empty($arr["exm_flg_se"][0])) ? "bgWhite" : "";
								}
							}

							if($oneeye[1]=="OU" || $oneeye[1]=="OS"){
								$on_nm_os = $oneeye[0];
								if($oneeye[2]==1){
									$cOs = "bgWhite";
								}else{
									$cOs = (!empty($arr["exm_flg_se"][1])) ? "bgWhite" : "";
								}
							}

							$str .= "<tr>";
							$str .=	"<td class=\"edone leftpanel sublink\"></td>";
							$str .=	"<td class=\"prph sumod tablnk ".$cOd."\" ><span>".$on_nm_od."</span></td>";
							$str .=	"<td class=\"prph sumos tablnk ".$cOs."\"><span>".$on_nm_os."</span></td>";
							$str .= "".$strDesc;
							$str .="</tr>";

							$strDesc="";//Empty Desc after use
						}
						//--

						for($i=0;$i<$len;$i++){
						$sumod = trim($arrSE[$i]["sumod"]);
						$sumos = trim($arrSE[$i]["sumos"]);
						$cnm=$cOd=$cOs=$se_nm="";

						//2.when protheses is checked, you are not able to put anything in that eye for prescription or medical reason. Could you pls fix it that you can type everything into that eye as if it was not checked.
						/*
						//OneEye Prosthisis Check--
						if(count($oneeye)>0 && $oneeye[0]=="Prosthesis"){
							if($oneeye[1]=="OD"){
								$sumod = "";
							}else if($oneeye[1]=="OS"){
								$sumos = "";
							}
						}*/

						//if($sumod!="" || $sumos!=""){ // Stopped check so that all exam appear in list
							$se_nm = $arrSE[$i]["nm"];
							if($sumod!="" && !empty($arrSE[$i]["cOd"])){
								$cOd = "bgWhite";
							}
							if($sumos!="" && !empty($arrSE[$i]["cOs"])){
								$cOs = "bgWhite";
							}
							if(!empty($arrSE[$i]["cnm"])){
								$cnm = "color_red";
							}

							$se_nm_var=$se_nm;
							if($se_nm=="Lid Position"){
								$se_nm_var="LidPos";
							}else if($se_nm=="Lacrimal System"){
								$se_nm_var="Lac";
							}else if($se_nm=="Conjunctiva"){
								$se_nm_var="Conj";
							}else if($se_nm=="Cornea"){
								$se_nm_var="Corn";
							}else if($se_nm=="Ant. Chamber"){
								$se_nm_var="Ant";
							}else if($se_nm=="Iris & Pupil"){
								$se_nm_var="Iris";
							}else if($se_nm=="Blood Vessels"){
								$se_nm_var="BV";
							}else if($se_nm=="Optic Nerve"){
								$se_nm_var="Optic";
							}

							//Stop Drawing to be last row --
							if($se_nm_var == "Drawing"){
								continue;
							}
							//Stop Drawing to be last row --

							$elem_summOd = "elem_sumOd".$se_nm_var;
							$elem_summOs = "elem_sumOs".$se_nm_var;
							$js_od = $arrSE[$i]["arcJsOd"];
							$js_os = $arrSE[$i]["arcJsOs"];
							$css_od = $arrSE[$i]["arcCssOd"];
							$css_os =  $arrSE[$i]["arcCssOs"];
						//}

						$str .= "<tr>";

						if($enm_var == "Pupil" || $enm_var == "EOM" || $enm_var == "External" || $enm_var == "Ref_Surg" ){
							$str .=	"<td class=\"edone ehdr leftpanel sublink\">".$exm_header."</td>";
						}else{

							//First Row, Add Drawing label also
							if($i==0){
								$se_nm_drw = $arrSE[$len-1]["nm"];
							}else{$se_nm_drw="";}

							if($se_nm_drw=="Drawing"){
								/*
								//Check Drawing
								$cnm_drw = (!empty($arrSE[$len-1]["cnm"])) ? "color_red" : "";

								//GET link settings
								$arlinkDrw= getSubExamLabel($enm_var, "Drawing", $seList);
								$strDrw ="<div ".$arlinkDrw["id"]." class=\"drawtab ".$cnm_drw." \" >".
										//"<span class=\"".$arlinkDrw["cls"]."\"></span>".
										"<label ".$arlinkDrw["onclick"].">".$se_nm_drw."</label>".
										"</div>";
								*/
								//GET link settings
								$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);

								$tmp_se_nm = $se_nm;
								if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }

								$str .="<td ".$arlink["id"]." class=\"edone esubexam leftpanel sublink ".$cnm." \" >".
										//"<span class=\"".$arlink["cls"]."\"></span>".
										"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>".
									//$strDrw.
									"</td>";

							}else{

								//GET link settings
								$arlink= $this->getSubExamLabel($enm_var, $se_nm_var, $seList);

								$tmp_se_nm = $se_nm;
								if(isset($arrSE[$i]["nm_2show"]) && !empty($arrSE[$i]["nm_2show"])){ $tmp_se_nm = $arrSE[$i]["nm_2show"];  }

								$str .=	"<td ".$arlink["id"]." class=\"edone esubexam leftpanel sublink ".$cnm." \" >".
										//"<span class=\"".$arlink["cls"]."\"></span>".
										"<label ".$arlink["onclick"]." >".$tmp_se_nm."</label>".
										"</td>";

							}
						}

						if($se_nm_var == "Drawing"){

							//Color
							$str .=	"<td class=\"sumod ".$bgDraw."\" ></td>";
							$str .=	"<td class=\"sumos ".$bgDraw."\" ></td>";

						}else{

							$str .=	"<td id=\"".$elem_summOd."\" class=\"sumod tablnk ".$cOd." ".$css_od."\" ".$strColspan." ".$js_od." ><span>".$sumod."</span></td>";

							if($enm != "EOM"){
							$str .=	"<td id=\"".$elem_summOs."\" class=\"sumos tablnk ".$cOs." ".$css_os."\" ".$js_os." ><span>".$sumos."</span></td>";
							}
						}

						if(!empty($strDesc)){
							$str .= "".$strDesc;
							$strDesc="";
						}

						$str .="</tr>";

						}

					$str .= "</table>";
					$str .= "";

		//
		$str=trim($str);
		if(!empty($str)){
			$str="<div class=\"purged\">".$str."</div>";
		}

		return $str;
	}

}
?>
