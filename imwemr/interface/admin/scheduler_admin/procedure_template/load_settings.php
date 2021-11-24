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
Purpose: set procedure timings
Access Type: Direct
*/

require_once('../../../../config/globals.php');

$pro_id = $_REQUEST['pro_id'];
$doctor_id = $_REQUEST['doctor_id'];

//fetching procedure timings
$strQry = "SELECT * FROM slot_procedures_timings WHERE procedureId = '".$pro_id."' AND doctor_id = '".$doctor_id."' ORDER BY timeCount";
$rsTmData = imw_query($strQry);
$strResponse = "";
if($rsTmData){
    while($arrTempProvTimings = imw_fetch_array($rsTmData)){
        $tempCntId = $arrTempProvTimings['timeCount'];
        $arrFrom = explode(":",$arrTempProvTimings['after_start_time']);
        $fromHr = $arrFrom[0];
        $fromMn = $arrFrom[1];
        
        if($fromHr > 12){
            $fromHr = $fromHr - 12;
            $fromAP = "PM";     
        }else if($fromHr == 12){
            $fromAP = "PM";
        }else if($fromHr == "00"){
            $fromHr = 12;
            $fromAP = "AM";
        }else{
            $fromAP = "AM";
        }
        $fromHr = (strlen($fromHr) == 1) ? "0".$fromHr : $fromHr;
        
        $arrTo = explode(":",$arrTempProvTimings['after_end_time']);
        $toHr = $arrTo[0];
        $toMn = $arrTo[1];
        
        if($toHr > 12){
            $toHr = $toHr - 12;
            $toAP = "PM";     
        }else if($toHr == 12){
            $toAP = "PM";
        }else if($toHr == "00"){
            $toHr = 12;
            $toAP = "AM";
        }else{
            $toAP = "AM";
        }
        $toHr = (strlen($toHr) == 1) ? "0".$toHr : $toHr;
        
        $strResponse .= $tempCntId."^".$fromHr."^".$fromMn."^".$fromAP."^".$toHr."^".$toMn."^".$toAP."~";
    }          
}
echo $strResponse;
?>