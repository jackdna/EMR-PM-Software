<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: exam_options.php
Coded in PHP7
Purpose: This file provides chart notes exams summaries . not in use now.
Access Type : Include file
*/
?>
<?php
//include_once($GLOBALS['incdir']."/chart_notes/common/createXml.php");

// TEST
$menuFilePath = $GLOBALS['incdir']."/chart_notes/xml/";

/*
function getMenuArray($menuFilePath)
{
	$strMenuXml = getMenuXmlStringFromFile($menuFilePath);
	$parser = xml_parser_create();
	xml_parse_into_struct($parser, $strMenuXml, $testVals, $index);
	xml_parser_free($parser);
	$arrRet = array();
	if(count($testVals) > 0)
	{
		foreach($testVals as $var => $val)
		{
			if(isset($val["attributes"]["EXAMNAME"]) && (!empty($val["attributes"]["EXAMNAME"]))){
				$arrRet[] = $val["attributes"]["EXAMNAME"];
			}
		}
	}
	return $arrRet;
}
*/

//Get Common Exams
function eo_getExamMenuCommonExms(){

	global $arrRvs,$arrMedHx,
			$arrMenuOptions_Pupil,
			$arrMenuOptions_Ee,

			$arrMenuOptions_LA_lids,
			$arrMenuOptions_LA_lesion,
			$arrMenuOptions_LA_lidpos,
			$arrMenuOptions_LA_lacsys,

			$arrMenuOptions_Gonio,

			$arrMenuOptions_SLE_conj,
			$arrMenuOptions_SLE_cor,
			$arrMenuOptions_SLE_ac,
			$arrMenuOptions_SLE_iris,
			$arrMenuOptions_SLE_lens,

			$arrMenuOptions_RV_opt,
			$arrMenuOptions_RV_mac,
			$arrMenuOptions_RV_vit,
			$arrMenuOptions_RV_peri,
			$arrMenuOptions_RV_bv,

			$arrMenuOptions_Cvf,
			$arrMenuOptions_Dip;

	$arrMainAll = array( $arrRvs,$arrMedHx,
						$arrMenuOptions_Pupil,
						$arrMenuOptions_Ee,

						$arrMenuOptions_LA_lids,
						$arrMenuOptions_LA_lesion,
						$arrMenuOptions_LA_lidpos,
						$arrMenuOptions_LA_lacsys,

						$arrMenuOptions_Gonio,

						$arrMenuOptions_SLE_conj,
						$arrMenuOptions_SLE_cor,
						$arrMenuOptions_SLE_ac,
						$arrMenuOptions_SLE_iris,
						$arrMenuOptions_SLE_lens,

						$arrMenuOptions_RV_opt,
						$arrMenuOptions_RV_mac,
						$arrMenuOptions_RV_vit,
						$arrMenuOptions_RV_peri,
						$arrMenuOptions_RV_bv,

						$arrMenuOptions_Cvf,
						$arrMenuOptions_Dip);

	$arrCommon=$arrAll=array();
	foreach($arrMainAll as $key => $val){
		$arrTmp = $val;
		if(count($arrTmp) > 0){
			foreach($arrTmp as $key2 => $val2){
				$tmp = trim($val2);
				if(!in_array($tmp, $arrAll)){
					$arrAll[] = $tmp;
				}else{
					$arrCommon[] = $tmp;
				}
			}
		}
	}

	return $arrCommon;
}

// Get Menu Exams
function getMenuArray($menuFilePath, $Exam="") {

	$retArr = array();
	//
	$dom = new DOMDocument;
	$dom->preserveWhiteSpace = false;
	// Load Dom object
	$dom->load($menuFilePath);

	$root = $dom->documentElement->firstChild;
	foreach($root->childNodes as $field){
		 $attr_examName = "";
		 $attr_commonexam = "";
		 $attr_tabname ="";
		 $levelEx1="";
		 if($field->hasAttributes()) {
			$attr_examName = $field->getAttribute("examname");
			$attr_commonexam = $field->getAttribute("commonexam");
			$attr_tabname = "".$field->getAttribute("tabname");
			if(!empty($attr_examName)){
$levelEx1 = (!empty($attr_commonexam)) ? $Exam."/".$attr_examName : $attr_examName;
				if(!in_array($levelEx1,$retArr)){
					$retArr[] = $levelEx1;
				}
			}
		 }

		 //exam_name
		 if(!empty($attr_examName)){

			 //Loop inner values
			 foreach($field->childNodes as $field_level){

				$attr_examName2 = "";
				$attr_commonexam2 = "";
				$levelEx2="";
				//Check if Node has Child Nodes and attribute examName
				if($field_level->hasAttributes()) {
					$attr_examName2 = $field_level->getAttribute("examname");
					$attr_commonexam2 = $field_level->getAttribute("commonexam");

					if(!empty($attr_examName2)){
$levelEx2 = (!empty($attr_commonexam2)) ? $levelEx1."/".$attr_examName2 : $attr_examName."/".$attr_examName2;
						if(!in_array($levelEx2,$retArr)){
							$retArr[] = $levelEx2;
						}
					}
 				}

				if(!empty($attr_examName2)){

					//Loop inner values - 2
					foreach($field_level->childNodes as $field_level2){
						$attr_examName3 = "";
						$attr_commonexam3 = "";
						$levelEx3="";
						//Check if Node has Child Nodes and attribute examName
						if($field_level2->hasAttributes()) {
							$attr_examName3 = $field_level2->getAttribute("examname");
							$attr_commonexam3 = $field_level2->getAttribute("commonexam");

							if(!empty($attr_examName3)){
$levelEx3 = (!empty($attr_commonexam3)) ? $levelEx2."/".$attr_examName3 : $attr_examName."/".$attr_examName2."/".$attr_examName3;
								if(!in_array($levelEx3,$retArr)){
									$retArr[] = $levelEx3;
								}
							}
						}
					}
				}
			 }

		 }else if(!empty($attr_tabname)){ //tabname

			$strTabName=$attr_tabname."/";
			if($attr_tabname == "advanced_plastics" || $attr_tabname == "Advanced Plastics"){
				$strTabName="";
			}

			//Loop inner values
			 foreach($field->childNodes as $field_level){

				$attr_examName2 = "";
				$attr_commonexam2 = "";
				//Check if Node has Child Nodes and attribute examName
				if($field_level->hasAttributes()) {
					$attr_examName2 = $field_level->getAttribute("examname");
					$attr_commonexam2 = $field_level->getAttribute("commonexam");

					if(!empty($attr_examName2)){
$levelEx2 = (!empty($attr_commonexam2)) ? $Exam."/".$strTabName.$attr_examName2 : $attr_examName2;
						if(!in_array($levelEx2,$retArr)){
							$retArr[] = $levelEx2;
						}
					}
 				}

				if(!empty($attr_examName2)){

					//Loop inner values - 2
					foreach($field_level->childNodes as $field_level2){
						$attr_examName3 = "";
						$attr_commonexam3 = "";
						//Check if Node has Child Nodes and attribute examName
						if($field_level2->hasAttributes()) {
							$attr_examName3 = $field_level2->getAttribute("examname");
							$attr_commonexam3 = $field_level2->getAttribute("commonexam");

							if(!empty($attr_examName3)){
$levelEx3 = (!empty($attr_commonexam3)) ? $levelEx2."/".$attr_examName3 : $attr_examName2."/".$attr_examName3;
								if(!in_array($levelEx3,$retArr)){
									$retArr[] = $levelEx3;
								}
							}
						}
					}
				}

			}

		 }
	}

	return $retArr;
}

//--- All Tests

$arrMain=array();
$arrRvs = array("Blurred","Poor Night vision","Poor depth","Glare","Halos","Tearing",
				"Dry eyes","Diplopia","Spots","Itching","Burning","Red eyes","Redness",
				"Floaters");

$arrMedHx = array("Diet", "IDDM", "NIDDM", "HTN", "Heart", "Lungs", "Neuro");

$arrMenuXml_Pupil = array('pupil_od.xml');

$arrMenuXml_Ee = array('external_od.xml');

$arrMenuXml_LA_lids = array('lids_od.xml');
$arrMenuXml_LA_lesion = array('lesion_od.xml');
$arrMenuXml_LA_lidpos = array('lidposition_od.xml');
$arrMenuXml_LA_lacsys = array('lacrimalSys_od.xml');

$arrMenuXml_Gonio = array('iopGon_od.xml');

$arrMenuXml_SLE_conj = array('conjunctiva_od.xml');
$arrMenuXml_SLE_cor = array('cornea_od.xml');
$arrMenuXml_SLE_ac = array('antChamber_od.xml');
$arrMenuXml_SLE_iris = array('iris_od.xml');
$arrMenuXml_SLE_lens = array('lens_od.xml');

$arrMenuXml_RV_opt = array('opticNerveDisc_od.xml');

$arrMenuXml_RV_mac = array('macula_od.xml');
$arrMenuXml_RV_peri = array('periphery_od.xml');
$arrMenuXml_RV_bv = array('bloodVessels_od.xml');

$arrMenuXml_RV_vit = array('vitreous_od.xml');
$arrMenuXml_RV_retinal = array('retinal_od.xml');

$arrMenuXml_Dip = array('diplopia_od.xml');
$arrMenuXml_Cvf = array('confrontationField_od.xml');

//$arrMenuXml = array();

//Pupil
$arrMenuOptions_Pupil = array();
$arrMenuOptions_Pupil = getMenuArray($menuFilePath.$arrMenuXml_Pupil[0],"Pupil");

//echo "<br><br>Pupil<br>";
//print_r($arrMenuOptions_Pupil);


//Ee
$arrMenuOptions_Ee = array();
$arrMenuOptions_Ee = getMenuArray($menuFilePath.$arrMenuXml_Ee[0],"External");

//echo "<br><br>EE<br>";
//print_r($arrMenuOptions_Ee);

//LA
$arrMenuOptions_LA = array();
//LA - Lids
$arrMenuOptions_LA_lids = array();
$arrMenuOptions_LA_lids = getMenuArray($menuFilePath.$arrMenuXml_LA_lids[0],"Lids");
$arrMenuOptions_LA = array_merge($arrMenuOptions_LA,$arrMenuOptions_LA_lids);

//echo "<br><br>LIDS<br>";
//print_r($arrMenuOptions_LA_lids);

//LA - Lesion
$arrMenuOptions_LA_lesion = array();
$arrMenuOptions_LA_lesion = getMenuArray($menuFilePath.$arrMenuXml_LA_lesion[0],"Lesion");
$arrMenuOptions_LA = array_merge($arrMenuOptions_LA,$arrMenuOptions_LA_lesion);

//echo "<br><br>LESION<br>";
//print_r($arrMenuOptions_LA_lesion);

//LA - LidPos
$arrMenuOptions_LA_lidpos = array();
$arrMenuOptions_LA_lidpos = getMenuArray($menuFilePath.$arrMenuXml_LA_lidpos[0],"Lid Positions");
$arrMenuOptions_LA = array_merge($arrMenuOptions_LA,$arrMenuOptions_LA_lidpos);

//echo "<br><br>LIDPOS<br>";
//print_r($arrMenuOptions_LA_lidpos);

//LA - LacSys
$arrMenuOptions_LA_lacsys = array();
$arrMenuOptions_LA_lacsys = getMenuArray($menuFilePath.$arrMenuXml_LA_lacsys[0],"Lacrimal System");
$arrMenuOptions_LA = array_merge($arrMenuOptions_LA,$arrMenuOptions_LA_lacsys);

//echo "<br><br>LACYSY<br>";
//print_r($arrMenuOptions_LA_lacsys);

//Gonio
$arrMenuOptions_Gonio = array();
$arrMenuOptions_Gonio = getMenuArray($menuFilePath.$arrMenuXml_Gonio[0],"Gonio");

//echo "<br><br>GONIO<br>";
//print_r($arrMenuOptions_Gonio);

//SLE - conjuctiva
$arrMenuOptions_SLE_conj = array();
$arrMenuOptions_SLE_conj = getMenuArray($menuFilePath.$arrMenuXml_SLE_conj[0],"Conjunctiva");

//echo "<br><br>CONJUCTIVA<br>";
//print_r($arrMenuOptions_SLE_conj);

//SLE - cornea
$arrMenuOptions_SLE_cor = array();
$arrMenuOptions_SLE_cor = getMenuArray($menuFilePath.$arrMenuXml_SLE_cor[0],"Cornea");

//echo "<br><br>CORNEA<br>";
//print_r($arrMenuOptions_SLE_cor);

//SLE - ant chamber
$arrMenuOptions_SLE_ac = array();
$arrMenuOptions_SLE_ac = getMenuArray($menuFilePath.$arrMenuXml_SLE_ac[0],"Ant. Chamber");

//echo "<br><br>ANT<br>";
//print_r($arrMenuOptions_SLE_ac);

//SLE - iris
$arrMenuOptions_SLE_iris = array();
$arrMenuOptions_SLE_iris = getMenuArray($menuFilePath.$arrMenuXml_SLE_iris[0],"Iris");

//echo "<br><br>IRIS<br>";
//print_r($arrMenuOptions_SLE_iris);

//SLE - lens
$arrMenuOptions_SLE_lens = array();
$arrMenuOptions_SLE_lens = getMenuArray($menuFilePath.$arrMenuXml_SLE_lens[0],"Lens");

//echo "<br><br>LENS<br>";
//print_r($arrMenuOptions_SLE_lens);

//RV - optic
$arrMenuOptions_RV_opt = array();
$arrMenuOptions_RV_opt = getMenuArray($menuFilePath.$arrMenuXml_RV_opt[0],"Optic Nerve");

//echo "<br><br>OPTIC<br>";
//print_r($arrMenuOptions_RV_opt);

//RV - vit
$arrMenuOptions_RV_vit = array();
$arrMenuOptions_RV_vit = getMenuArray($menuFilePath.$arrMenuXml_RV_vit[0],"Vitreous");

//echo "<br><br>VIT<br>";
//print_r($arrMenuOptions_RV_vit);

//RV - retinal
$arrMenuOptions_RV_retinal = array();
$arrMenuOptions_RV_retinal = getMenuArray($menuFilePath.$arrMenuXml_RV_retinal[0],"Retinal");

//$arrMenuOptions_RV_mac = array();
//$arrMenuOptions_RV_peri = array();
//$arrMenuOptions_RV_bv = array();
//echo "<br><br>VIT<br>";
//print_r($arrMenuOptions_RV_vit);


//RV - mac
$arrMenuOptions_RV_mac = array();
$arrMenuOptions_RV_mac = getMenuArray($menuFilePath.$arrMenuXml_RV_mac[0],"Macula");

//echo "<br><br>MAC<br>";
//print_r($arrMenuOptions_RV_mac);

//RV - peri
$arrMenuOptions_RV_peri = array();
$arrMenuOptions_RV_peri = getMenuArray($menuFilePath.$arrMenuXml_RV_peri[0],"Periphery");

//echo "<br><br>PERI<br>";
//print_r($arrMenuOptions_RV_peri);

//RV - bv
$arrMenuOptions_RV_bv = array();
$arrMenuOptions_RV_bv = getMenuArray($menuFilePath.$arrMenuXml_RV_bv[0],"Vessels");

//echo "<br><br>BV<br>";
//print_r($arrMenuOptions_RV_bv);


//Cvf
$arrMenuOptions_Cvf = array();
//$arrMenuOptions_Cvf = getMenuArray($menuFilePath.$arrMenuXml_Cvf[0],"CVF"); ///stopped by me: i think it is not a symptom

//echo "<br><br>CVF<br>";
//print_r($arrMenuOptions_Cvf);

//Dip
$arrMenuOptions_Dip = array();
//$arrMenuOptions_Dip = getMenuArray($menuFilePath.$arrMenuXml_Dip[0],"Diplopia");

//echo "<br><br>Diplopia<br>";
//print_r($arrMenuOptions_Dip);

/*
//Other
$arrMenuOptions = array();
$arrSize = count($arrMenuXml);
for($i=0;$i<$arrSize;$i++)
{
	$tmp = getMenuArray($menuFilePath.$arrMenuXml[$i]);
	$arrMenuOptions = array_merge($arrMenuOptions,$tmp);
}
*/

$arrMain = array_merge( $arrRvs,$arrMedHx,
						$arrMenuOptions_Pupil,
						$arrMenuOptions_Ee,

						$arrMenuOptions_LA_lids,
						$arrMenuOptions_LA_lesion,
						$arrMenuOptions_LA_lidpos,
						$arrMenuOptions_LA_lacsys,

						$arrMenuOptions_Gonio,

						$arrMenuOptions_SLE_conj,
						$arrMenuOptions_SLE_cor,
						$arrMenuOptions_SLE_ac,
						$arrMenuOptions_SLE_iris,
						$arrMenuOptions_SLE_lens,

						$arrMenuOptions_RV_opt,
						$arrMenuOptions_RV_mac,
						$arrMenuOptions_RV_vit,
						$arrMenuOptions_RV_peri,
						$arrMenuOptions_RV_bv,
						$arrMenuOptions_RV_retinal,

						$arrMenuOptions_Cvf,
						$arrMenuOptions_Dip);


/*
//Check common values in all arrays
$eo_arrCommonExams = eo_getExamMenuCommonExms();

echo "<br><br>Common<br>";
print_r($eo_arrCommonExams);
//*/

$arrMain = array_unique($arrMain);
$arrMainTmp = array();
foreach($arrMain as $val)
{
	$arrMainTmp[] = strtoupper($val);
}

//Array exam names->subexams
$arrMain_examswise=array(
	"Pupil"=>$arrMenuOptions_Pupil,
	"External"=>$arrMenuOptions_Ee,
	"LA"=>array("Lids"=>$arrMenuOptions_LA_lids,
				"Lesion"=>$arrMenuOptions_LA_lesion,
				"Lids Position"=>$arrMenuOptions_LA_lidpos,
				"Lacrimal System"=>$arrMenuOptions_LA_lacsys	),
	"Gonio"=>$arrMenuOptions_Gonio,
	"SLE"=>array("Conjunctiva"=>$arrMenuOptions_SLE_conj,
				"Cornea"=>$arrMenuOptions_SLE_cor,
				"Ant Chamber"=>$arrMenuOptions_SLE_ac,
				"Iris"=>$arrMenuOptions_SLE_iris,
				"Lens"=>$arrMenuOptions_SLE_lens),
	"Fundus"=>array("Optic Nerve"=>$arrMenuOptions_RV_opt,
					"Vitreous"=>$arrMenuOptions_RV_vit,
					"Macula"=>$arrMenuOptions_RV_mac,
					"Vessels"=>$arrMenuOptions_RV_bv,
					"Periphery"=>$arrMenuOptions_RV_peri,
					"Retinal"=>$arrMenuOptions_RV_retinal),
	"CVF"=>$arrMenuOptions_Cvf
);

/*
function sc_getExamMenuOptions($arr, $id="", $css="", $exm="",$lvl=0){
	$ret = "";


	if(count($arr) > 0){

		foreach($arr as $key => $val){

			if(is_array($val) && count($val) > 0){

				$ret .="<li><a href=\"javascript:void(0);\" >".$key."</a>";
				$ret .= sc_getExamMenuOptions($val,"","",$key,$lvl+1);
				$ret .="</li>";

			}else if(!empty($val)){

				if(!empty($exm) && $lvl>1){
					$data_tmp =  " data-exam=\"".$exm."\" " ;
				}

				$ret .="<li><a href=\"javascript:void(0);\" onclick=\"sc_searchExam(this);\" ".$data_tmp.">".$val."</a></li>";
			}

		}

		//if(!empty($ret)){

			$strId="";
			//if(empty($lvl)){
			if(!empty($id)){
				$strId = " id=\"".$id."\" ";
			}
			//}

			if(!empty($css)){
				$strCss = " style=\"".$css."\"  ";
			}

			$ret = "<ul ".$strId." ".$strCss.">".$ret."</ul>";

		//}

	}

	return $ret;
}

*/




?>
