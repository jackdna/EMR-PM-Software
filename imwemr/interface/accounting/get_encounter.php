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
include_once(dirname(__FILE__)."/../../config/globals.php"); 
include_once(dirname(__FILE__)."/../../library/classes/acc_functions.php");
include_once(dirname(__FILE__)."/../../library/classes/common_function.php");
if($enc_dos){
	$data ='<option value="">Encounter ID</option>';
	$getCaseTypeStr = "SELECT encounter_id FROM patient_charge_list 
						WHERE del_status='0' and patient_id = '$pat_id' and  date_of_service ='$enc_dos' group by encounter_id  order by encounter_id desc";
	$getCaseTypeQry = imw_query($getCaseTypeStr);
	
	if(imw_num_rows($getCaseTypeQry) == '1'){
		while($getCaseTypeRow = imw_fetch_array($getCaseTypeQry)){
			$encounter_id = $getCaseTypeRow['encounter_id'];
			$data .='
				<option value="'.$encounter_id.'" selected="selected">'.$encounter_id.'</option>
			';
		}
	}
	else{
		$sel='selected="selected"';
		while($getCaseTypeRow = imw_fetch_array($getCaseTypeQry)){
			$encounter_id = $getCaseTypeRow['encounter_id'];
			$data .='
				<option value="'.$encounter_id.'" '.$sel.'>'.$encounter_id.'</option>
			';
			$sel="";
		}
	}
	
}else{
	$data ='<option value="">Encounter ID</option>';
}
echo $data;
?>