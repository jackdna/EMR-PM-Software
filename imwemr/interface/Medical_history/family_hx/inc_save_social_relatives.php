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

$family_smoke = addslashes($_POST["radio_family_smoke"]);

$smokers_in_relatives = implode(",",(array)$_REQUEST["smokers_in_relatives"]);
if((preg_match("/\bOther\b/i", $smokers_in_relatives) || preg_match("/\b Other\b/i", $smokers_in_relatives)) && $_REQUEST['rel_other_smokers_in_relatives']!='') {
	$smokers_in_relatives.=', '.$_REQUEST['rel_other_smokers_in_relatives'];
}

$smoke_description = addslashes($_POST["smoke_description"]);

$Check_Social_Query="select * from social_history where patient_id = ".(int) $pid." ";
$Check_Social_Result = imw_query($Check_Social_Query) or die (imw_error());
$checkrows2=imw_num_rows($Check_Social_Result);
if($checkrows2>0){			
	// update
	$row = imw_fetch_assoc($Check_Social_Result);
	$newSocialHistoryId =  $row['social_id'];
	$socialsaveqry = "update social_history set ";
	$socialsaveqry .= " patient_id=".(int) $pid." ";
}else{			
	// insert new
	$socialsaveqry = "insert into social_history set ";					
	$socialsaveqry .= " patient_id=".(int) $pid." ";
}
	$socialsaveqry.= " ,family_smoke ='".$family_smoke."' ";
	$socialsaveqry.= " ,smokers_in_relatives ='".imw_real_escape_string($smokers_in_relatives)."' ";
	$socialsaveqry.= " ,smoke_description ='".$smoke_description."' ";

	if($checkrows2>0){			
		$socialsaveqry .= " where patient_id=".(int) $pid." ";
	}
	$socialsaveSql = imw_query($socialsaveqry);
	if($checkrows2==0){			
		$newSocialHistoryId = imw_insert_id();
	}
	if(!$socialsaveSql){
		echo ("Error : ". imw_error()."<br>".$socialsaveSql);
		$socialHistoryError = "Error : ".imw_error();
	}
$_REQUEST["smokers_in_relatives"] = $smokers_in_relatives;	
if(!$_REQUEST["radio_family_smoke"]){
	$_REQUEST["radio_family_smoke"] = "0";
}
?>