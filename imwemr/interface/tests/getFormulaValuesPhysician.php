<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?>
<?php
/*
File: scanImages.php
Purpose: This file provides Scan, Upload and Preview section in tests.
Access Type : Direct
*/
?>
<?php
set_time_limit(90);
require_once(dirname(__FILE__)."/../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
require_once("../../library/classes/class.tests.php");
require_once("../../library/classes/SaveFile.php");
$objTests				= new Tests;
$patient_id 	= $_SESSION['patient'];
//MAKING OBJECT TO SAVE IMAGE FILES
$oSaveFile = new SaveFile($patient_id);

$phyId = $_REQUEST['phyId'];
$type = $_REQUEST['type'];
$pId = $_REQUEST['pId'];
$fId = $_REQUEST['fId'];
//================= FUNCTION TO GET LENSE TYPE
function getLenseName($lenseID){
	$getLenseTypeStr = "SELECT * FROM lenses_iol_type WHERE iol_type_id = '$lenseID'";
	$getLenseTypeQry = imw_query($getLenseTypeStr);
	$getLenseTypeRow = imw_fetch_array($getLenseTypeQry);
	$lenses_iol_type = $getLenseTypeRow['lenses_iol_type'];
	return $lenses_iol_type;
}
//================= FUNCTION TO GET LENSE TYPE

//=================== GETTING FORMULA VALUES OF PATIENT BY PROVIDER
$getFormulaValuesStr = "SELECT * from iolphyformulavalues 
						WHERE patient_id = '$pId'
						AND provider_id = '$phyId'
						AND form_id = '$fId'
						AND type_OD_OS = '$type'";
$getFormulaValuesQry = imw_query($getFormulaValuesStr);
$countRows = imw_num_rows($getFormulaValuesQry);
if($countRows>0){
		$getFormulaValuesRow = imw_fetch_array($getFormulaValuesQry);
		$iol1Id = $getFormulaValuesRow['iol1'];
		if(($iol1Id!='') && ($iol1Id!=0)){
			$iol1 = getLenseName($iol1Id)."!*!".$iol1Id;
		}else{
			$iol1 = '';
		}
		$iol1Power = $getFormulaValuesRow['iol1Power'];
		$iol1Holladay = $getFormulaValuesRow['iol1Holladay'];
		$iol1srk_t = $getFormulaValuesRow['iol1srk_t'];
		$iol1Hoffer = $getFormulaValuesRow['iol1Hoffer'];		
		$iol2Id = $getFormulaValuesRow['iol2'];
		if(($iol2Id!='') && ($iol2Id!=0)){
			$iol2 = getLenseName($iol2Id)."!*!".$iol2Id;
		}else{
			$iol2 = '';
		}
			
		$iol2Power = $getFormulaValuesRow['iol2Power'];
		$iol2Holladay = $getFormulaValuesRow['iol2Holladay'];
		$iol2srk_t = $getFormulaValuesRow['iol2srk_t'];
		$iol2Hoffer = $getFormulaValuesRow['iol2Hoffer'];
		$iol3Id = $getFormulaValuesRow['iol3'];
		if(($iol3Id!='') && ($iol3Id!=0)){
			$iol3 = getLenseName($iol3Id)."!*!".$iol3Id;
		}else{
			$iol3 = '';
		}
		$iol3Power = $getFormulaValuesRow['iol3Power'];
		$iol3Holladay = $getFormulaValuesRow['iol3Holladay'];
		$iol3srk_t = $getFormulaValuesRow['iol3srk_t'];
		$iol3Hoffer = $getFormulaValuesRow['iol3Hoffer'];
		$iol4Id = $getFormulaValuesRow['iol4'];
		if(($iol4Id!='') && ($iol4Id!=0)){
			$iol4 = getLenseName($iol4Id)."!*!".$iol4Id;
		}else{
			$iol4 = '';
		}
		$iol4Power = $getFormulaValuesRow['iol4Power'];
		$iol4Holladay = $getFormulaValuesRow['iol4Holladay'];
		$iol4srk_t = $getFormulaValuesRow['iol4srk_t'];
		$iol4Hoffer = $getFormulaValuesRow['iol4Hoffer'];
		$cellCount = $getFormulaValuesRow['cellCount'];
		$pachymetryVal = $getFormulaValuesRow['pachymetryVal'];
		$pachymetryCorrec = $getFormulaValuesRow['pachymetryCorrec'];
		$cornealDiam = $getFormulaValuesRow['cornealDiam'];
		$dominantEye = $getFormulaValuesRow['dominantEye'];
		$pupilSize1 = $getFormulaValuesRow['pupilSize1'];
		$pupilSize2 = $getFormulaValuesRow['pupilSize2'];
		$notes = $getFormulaValuesRow['notes'];
		$plan = $getFormulaValuesRow['plan'];
			$plan = getLenseName($plan);
		$formulaValues = $iol1.",".$iol1Power.",".$iol1Holladay.",".$iol1srk_t.",".$iol1Hoffer.",".$iol2.",".$iol2Power.",".$iol2Holladay.",".$iol2srk_t.",".$iol2Hoffer.",".$iol3.",".$iol3Power.",".$iol3Holladay.",".$iol3srk_t.",".$iol3Hoffer.",".$iol4.",".$iol4Power.",".$iol4Holladay.",".$iol4srk_t.",".$iol4Hoffer.",".$cellCount.",".$pachymetryVal.",".$pachymetryCorrec.",".$cornealDiam.",".$dominantEye.",".$pupilSize1.",".$pupilSize2.",".$notes.",".$plan;
		echo $formulaValues;
}else{
			//================= GETTING IOL DEFINED FOR PHYSICIAN
	$getIolDefinedStr = "SELECT * FROM lensesdefined a, lenses_iol_type b 
						WHERE a.physician_id = '$phyId' AND a.iol_type_id = b.iol_type_id";
	$getIolDefinedQry = imw_query($getIolDefinedStr);
	while($getIolDefinedRow = imw_fetch_array($getIolDefinedQry)){
		$iol_type_id = $getIolDefinedRow['iol_type_id'];
		$lenses_iol_type = $getIolDefinedRow['lenses_iol_type'];
		$lenses_iol_type = $lenses_iol_type."!*!".$iol_type_id;
		if($lensesIolTypeStr!=''){
			$lensesIolTypeStr = $lensesIolTypeStr.",".$lenses_iol_type.",".''.",".''.",".''.",".'';
		}else{
			$lensesIolTypeStr.= $lenses_iol_type.",".''.",".''.",".''.",".'';
		}
	}
	echo $lensesIolTypeStr;
	//================= GETTING IOL DEFINED FOR PHYSICIAN
}
//=================== GETTING FORMULA VALUES OF PATIENT BY PROVIDER
?>