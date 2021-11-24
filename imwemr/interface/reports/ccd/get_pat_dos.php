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
FILE : get_pat_dos.php
PURPOSE : Getting patinet data (DOS) for CCD report.
ACCESS TYPE : Indirect
*/

//Global File
include("../../../config/globals.php");
$rqPId = (int)$_REQUEST['pId'];
$cbXData = "";
if($rqPId > 0){
	$getFORMIDQry =imw_query("SELECT id as form_id, date_of_service FROM chart_master_table WHERE patient_id = '".$rqPId."' order by id desc");
	if($getFORMIDQry){
		if(imw_num_rows($getFORMIDQry)>0){
			$counterInc=1;
			while($getFormRow = imw_fetch_array($getFORMIDQry)){
			$form_idTemp = $getFormRow['form_id'];
			
					$date_of_service = date("m-d-Y", strtotime($getFormRow["date_of_service"]));	
						$checked="";
					if($counterInc==1){
						$cbXData .= "<select name='cmbxElectronicDOS' id='cmbxElectronicDOS' class=\"form-control minimal\"><option value='all'>-- All --</option>";																				
					}
					$cbXData .= "<option  value='".trim($form_idTemp)."'> ".$date_of_service." </option>";
					$counterInc++;
				
			}
			$cbXData .= "</select>";
	 	}
	}
}
echo trim($cbXData);
?>