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
if(!isset($zflg_file_included) || empty($zflg_file_included)){
	require_once("../../config/globals.php");
	$patient_id = $_SESSION["patient"];
	$phyId = $_REQUEST["user_id"];
	$hidd_val = $_REQUEST["hidd_val"];
	$folder_name = $_REQUEST["folder_name"];
}

require_once(dirname(__FILE__)."/../../library/classes/imgGdFun.php");
require_once(dirname(__FILE__)."/../../library/classes/SaveFile.php");

global $oSaveFile;
$arr = array();
$num = 0;

//------
$oSaveFile = new SaveFile($patient_id);
$tmpDirPth_up = $oSaveFile->upDir;
//$tmpDirPth_sign = $oSaveFile->ptDir("test_sign");
$tmpDirPth_sign = $oSaveFile->ptDir("test_sign/".$folder_name);
$tmpDirPth_pt = "/PatientId_".$patient_id;
$form_sign_path = $tmpDirPth_pt.$tmpDirPth_sign;
$tmp_sign_path=realpath($tmpDirPth_up.$form_sign_path);
//------



if(!empty($phyId)){
	$sql = "SELECT sign, sign_path FROM users WHERE id = '".$phyId."' ";
	$row = sqlQuery($sql);
	if($row != false){
		$strpixls = trim($row["sign"]);
		$str_sign_path = trim($row["sign_path"]);
		
		$chk1=$chk2=0;
		if((!empty($strpixls) && $strpixls!="0-0-0:;")){  $chk1=1; }
		
		if((!empty($str_sign_path) && strpos($str_sign_path,"UserId") !== false && file_exists($oSaveFile->upDir.$str_sign_path) )){  $chk2=1; }					
		
		if($chk1==1||$chk2==1){
			$arr["str"] = "";
			//Make Image 			
			$img_nm = "/".$folder_name."_sig".$num."_".time().".jpg";			
			$tmp_sign_path1=$tmp_sign_path.$img_nm;						
			//global $gdFilename;
			if($chk2==1){
				if(copy($oSaveFile->upDir.$str_sign_path, $tmp_sign_path1)){ }else{ $form_sign_path=$img_nm=""; }
			}else{
				drawOnImage_new($strpixls,"",$tmp_sign_path1);
			}
			//-------------	
			//die($oSaveFile->upDirWeb.$form_sign_path.$img_nm);
			$arr["str"] .= '<img src="'.$oSaveFile->upDirWeb.$form_sign_path.$img_nm.'" alt="sign" style="width:225px; height:45px">';
			$arr["strpixls"]=$strpixls;
			$arr["strsignpath"]=$form_sign_path.$img_nm;	
		
		
			//Remove path of existing image for Signature if exist(because new path is already created above)
			if($hidd_val!=""){
				$hidd_path=realpath($tmpDirPth_up.$hidd_val);	
				$hidd_path = str_ireplace("\\","/",$hidd_path);
				if(file_exists($hidd_path)) {
					//@unlink($hidd_path);
				}
			$signatureSaveDateTime = date("Y-m-d H:i:s");
							
			}		
		}
	}
	
}

if(!isset($zflg_file_included) || empty($zflg_file_included)){
echo json_encode($arr);
}
?>