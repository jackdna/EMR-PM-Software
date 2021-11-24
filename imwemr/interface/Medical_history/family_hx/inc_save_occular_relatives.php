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

$strSep="~!!~~";
$strSep2=":*:";

if($rel_any_conditions_relative != ""){
	$any_conditions_relative_arr = ",";
	$any_conditions_relative_arr.= implode(",",$rel_any_conditions_relative);
	$any_conditions_relative_arr.= ",";
	//description 	
	$strDescRel="";
	if(count($rel_any_conditions_relative) > 0){
		foreach( $rel_any_conditions_relative as $key => $val  ){
			$strDescRel.="".$val.$strSep2.$_POST["rel_elem_chronicDesc_".$val].$strSep;
		}
	}
	$strDescRel = $strDescRel;
	
	//relatives
	if(count($rel_any_conditions_relative) > 0){
		foreach( $rel_any_conditions_relative as $key => $val  ){
			$tmpVal = ($_POST["elem_chronicRelative_".$val] == "Other") ? $_POST["elem_chronicRelative_".$val."_other"] : $_POST["elem_chronicRelative_".$val];	
			if(is_array($tmpVal) == true){
				$tmpVal = implode(", ", $tmpVal);
				if(preg_match("/\bOther\b/i", $tmpVal)) {$tmpVal.=", ".$_POST["rel_other_elem_chronicRelative_".$val]; } 
			}
			$strRelative.="".$val.$strSep2.$tmpVal.$strSep;
		}
	}
	$strRelative = $strRelative;
}

if(!empty($_REQUEST['rel_any_conditions_other_r']))
{
	$any_conditions_other_relative=$_REQUEST['rel_any_conditions_other_r'];
	$tmpVal = ($_POST["elem_chronicRelative_other"] == "Other") ? $_POST["elem_chronicRelative_other_other"] : $_POST["elem_chronicRelative_other"];
	$strDescRel.= ""."other".$strSep2.$_POST["rel_elem_chronicDesc_other"].$strSep;
}
else{
	$_REQUEST["rel_elem_chronicDesc_other"] = "";
}
$strDescForReviwedForRel = $strDescRel;

/*--------getting relative data ends here--------------*/
$OtherDescRel = $_REQUEST['rel_OtherDesc'];
$OtherSep = '~|~';
$query = "select OtherDesc,chronicDesc from ocular where patient_id='".$patient_id."'";
$sql = imw_query($query);
$tempOtDescRS = imw_fetch_assoc($sql);
$OtherDescRel = get_set_pat_rel_values_save($tempOtDescRS["OtherDesc"],$OtherDescRel,"rel",$delimiter);
$strDescRel = get_set_pat_rel_values_save($tempOtDescRS["chronicDesc"],$strDescRel,"rel",$delimiter);
/*---concatenation end----------*/

if( !empty($any_conditions_other_relative) ){
	$tmpVal = ($_POST["elem_chronicRelative_other"] == "Other") ? $_POST["elem_chronicRelative_other_other"] : $_POST["elem_chronicRelative_other"];
	if(is_array($tmpVal) == true){
		$tmpVal = implode(", ", $tmpVal);
		if(preg_match("/\bOther\b/i", $tmpVal)) {$tmpVal.=", ".$_POST["rel_other_elem_chronicRelative_other"]; } 
	}
	$strRelative.="other".$strSep2.$tmpVal.$strSep;
	$strRelative = $strRelative;
}


if($patient_id != "")
{
//insert / update
$check_data="select ocular_id from ocular where patient_id = '".$patient_id."'";
$checkSql = imw_query($check_data) or die (imw_error());
$checkrows = imw_num_rows($checkSql);
if($checkrows>0){			
	// update		
	$row = imw_fetch_array($checkSql);		
	$newOculerId = $row['ocular_id'];		
	$ocularsaveqry = "update ocular set ";
	//$ocularsaveqry .= " patient_id='".$patient_id."' ";
}else{			
	// insert new
	$ocularsaveqry = "insert into ocular set ";					
	$ocularsaveqry .= " patient_id='".$patient_id."', ";
}		

$ocularsaveqry.= " any_conditions_relative = '".imw_real_escape_string(htmlentities($any_conditions_relative_arr))."' ";
$ocularsaveqry.= " ,any_conditions_other_relative='".imw_real_escape_string(htmlentities($any_conditions_other_relative))."' ";
$ocularsaveqry.= " ,OtherDesc = '".imw_real_escape_string(htmlentities($OtherDescRel))."' ";
$ocularsaveqry.= " ,chronicDesc = '".imw_real_escape_string(htmlentities($strDescRel))."' ";
$ocularsaveqry.= " ,chronicRelative = '".imw_real_escape_string(htmlentities($strRelative))."' ";

if($checkrows>0){			
	// update
	$ocularsaveqry .= " where patient_id='".$patient_id."' ";
}
$saveSqlocular = imw_query($ocularsaveqry);
if(!$saveSqlocular){
	$oculerError = "Error : ". imw_errno() . ": " . imw_error();
	echo $oculerError."<br>".$saveSqlocular;
}
if($checkrows==0){			
	$newOculerId = imw_insert_id();
}
}
$_REQUEST['rel_any_conditions_relative'] = $any_conditions_relative_arr;
$strRelativeForReviwed = $strRelative;
?>