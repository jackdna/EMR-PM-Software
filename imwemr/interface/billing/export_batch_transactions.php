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
$csv_file = "batch_transaction_list_".$_SESSION['authId'].".csv"; 
$filePath=write_html('',$csv_file);
if($_REQUEST['mode'] == "list"){
	$csv_headers = array('Patient-ID','Encounter Id','C.P.T.','Total-Charges','Allowed','Deductible','Write-off','Adjustments','Paid','Negative-Amt','Method','Paid-By');
}else if($_REQUEST['mode'] == "chk_list"){
	$csv_headers = array('Patient-ID','Encounter-Id','C.P.T.','Check-No.','Total-Charges','Allowed','Deductible','Write-off','Adjustments','Paid','Negative-Amt','Paid-By');
}
$fp = fopen($filePath,'w');
fputcsv($fp,$csv_headers);
foreach($arr as $row)
fputcsv($fp, $row,",","\"");
fclose($fp);	
?>
