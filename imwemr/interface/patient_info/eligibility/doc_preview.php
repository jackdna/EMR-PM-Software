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
require_once('../../../config/globals.php');

extract($_REQUEST);

$rqRTMEId = $patId = "";
$rqRTMEId = (int)$id;
$patId = $_SESSION["patient"];

if((empty($rqRTMEId) == true) && (empty($patId) == true)){
	die("Please select patient.");
}

$data = array();

switch($action)
{
	case 'del':
		$data['success'] = false;
		$qryDelScan = "update ".constant("IMEDIC_SCAN_DB").".scans set status = '1' where scan_id = '".$rqRTMEId."'";
		$rsDelScan = imw_query($qryDelScan);
		if($rsDelScan) $data['success'] = true;
		break;	
		
	case 'list':
		
		$qryGetData = "select sc.scan_id as scanId, sc.image_name as docName, DATE_FORMAT(sc.created_date, '%m-%d-%Y') as docDate, sc.file_type as docType, 
													sc.file_path as docPath, concat(pd.lname,', ',pd.fname) as patName , pd.mname as patMname
													from ".constant("IMEDIC_SCAN_DB").".scans sc 
													LEFT JOIN patient_data pd on pd.id = sc.patient_id
													where sc.test_id = '".$rqRTMEId."' and sc.patient_id = '".$patId."' and sc.status = '0'";
		$rsGetData = imw_query($qryGetData);
		
		if($rsGetData){
			
			$cnt = imw_num_rows($rsGetData);
			if( $cnt > 0){
				
				$data['count'] = $cnt;
				while($rowGetData =  imw_fetch_assoc($rsGetData)){
					
					if($data['p_name'] == ""){
						$data['p_name'] = trim($rowGetData["patName"] . ' ' .$rowGetData["patMname"]);
					}
				
					$dbDocPath = data_path() . substr($rowGetData["docPath"],1);
					
					if(file_exists($dbDocPath) == true){
						list($width, $height, $type, $attr) = getimagesize($dbDocPath);
						$rowGetData['width'] = $width;
						$rowGetData['height'] = $height;
						$data['images'][] = $rowGetData;
					}
				} // End While
			}
		}
		imw_free_result($rsGetData);
		break;
}
echo json_encode($data);
exit;
?>