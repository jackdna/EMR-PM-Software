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
File: test_signature.php
Purpose: This file provide Signature section in tests.
Access Type : Include file
*/
?>
<?php
require_once("../../config/globals.php");
require_once("../../library/patient_must_loaded.php");
$sigStyle = $singJs = "";
if(!$sigFolderName) {
	$sigFolderName = 'new_folder';
}
if($sigUserType == "1"){
	$sigStyle = "color:#800080;cursor:pointer;";
	$singJs = "javascript:getPhySigForTest(document.getElementById('td_external_drawing_id'), document.getElementById('hidd_signature_path'),'".$sigFolderName."', '".$_SESSION["authId"]."', '".$GLOBALS['rootdir']."');";
}
?>
<table class="table">
    <tr>
        <td id="signature_cell" onClick="<?php echo $singJs;?>" style="width:80px; <?php echo $sigStyle;?>" class="txt_11b alignMiddle">Signature</td>
        <td style="width:250px;">
            <input type="hidden" name="hidd_prev_signature_path" id="hidd_prev_signature_path" value="<?php echo $sign_path;?>">
            <input type="hidden" name="hidd_signature_path" id="hidd_signature_path" value="<?php echo $sign_path;?>">
            <div id="td_external_drawing_id" style="border:1px solid orange; width:250px; height:50px; background-color:#FFF;">
		   <?php
            if($sign_path!='') {
                $tmpDirPth_up = $oSaveFile->upDir;//dirname(__FILE__)."/../main/uploaddir";
                $sign_real_path=$tmpDirPth_up.$sign_path;//realpath($tmpDirPth_up.$sign_path);	var_dump($tmpDirPth_up.$sign_path);
                //echo $sign_real_path; var_dump(file_exists($sign_real_path));
                if(file_exists($sign_real_path)) {
                    echo '<img src="'.$oSaveFile->upDirWeb.$sign_path.'" alt="sign" style="width:225px;height:45px">';
                }
            }
           ?>
            </div>
            <?php if($sign_path_date) {echo "<div><b>Date</b> ".$sign_path_date." <b>Time</b> ".$sign_path_time.'</div>';} ?>
        </td>
    </tr>                                                                                                                                                                
</table>
<?php
$sign_path_print='';
if($sign_path!='') {
	if(file_exists($sign_real_path)) {
		$sign_path_print.='
				<table class="table" id="test_sign_path_pdf">
					<tr>
						<td><b>Signature </b></td>
						<td >
							<!-- <div style="border:1px solid #E5E4E2; background-color:#FFF;padding:3px; "><img src="'.$oSaveFile->upDirWeb.$sign_path.'" alt="sign" width="225" height="45" ></div> -->
							<div style="border:1px solid #E5E4E2; background-color:#FFF;padding:3px; "><img src="'.$sign_real_path.'" alt="sign" width="225" height="45" ></div>
							<div style="white-space:nowrap; padding-left:5px;"><b>Date</b> '.$sign_path_date.' <b>Time</b> '.$sign_path_time.'</div>
						</td>
					</tr>
				</table>';
	}
}
?>