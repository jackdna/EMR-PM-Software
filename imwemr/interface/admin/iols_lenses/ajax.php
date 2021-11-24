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
?><?php

require_once("../../../config/globals.php");

$task = isset($_REQUEST['task']) ? trim($_REQUEST['task']) : '';
$so = isset($_REQUEST['so']) ? trim($_REQUEST['so']) : 'heard_options';
$soAD = isset($_REQUEST['soAD']) ? trim($_REQUEST['soAD']) : 'ASC';
$table = "lenses_iol_type";
$pkId = "iol_type_id";
$chkFieldAlreadyExist = "heard_options";
switch ($task) {
	case 'getLenses':
		$definedTypeToPhyArr = array();
		
		$physicianId = (isset($_REQUEST['id']) && empty($_REQUEST['id']) == false) ? $_REQUEST['id'] : '';
		
		$getDefinedStr = "SELECT iol_type_id FROM lensesdefined 
					WHERE physician_id = '$physicianId'";
		$getDefinedQry = imw_query($getDefinedStr);
		
		if($getDefinedQry && imw_num_rows($getDefinedQry) > 0){
			while ($getDefinedRows = imw_fetch_array($getDefinedQry)) {
				$definedTypeToPhyArr[] = $getDefinedRows['iol_type_id'];
			}
		}
		
		echo json_encode($definedTypeToPhyArr);
		
	break;
	
	case 'saveLenses':
		$phyId = (isset($_REQUEST['phyId']) && empty($_REQUEST['phyId']) == false) ? $_REQUEST['phyId'] : '';
		$selChkBox = (is_array($_REQUEST['selId']) && count($_REQUEST['selId']) > 0) ? $_REQUEST['selId'] : '';
		
		$counter = 0;
		if(empty($phyId) == false){
		//if(empty($phyId) == false && empty($selChkBox) == false){
			//Delete the prev. saved lenses
			$delQry = imw_query('DELETE FROM lensesdefined WHERE physician_id = '.$phyId.' ');
			
			//Insert New ones 
			if(empty($selChkBox) == false){
				foreach ($selChkBox as $iolTypeId) {
					$insertDefinedTypesStr = "INSERT INTO lensesdefined SET
											physician_id = '$phyId',
											iol_type_id = '$iolTypeId'";
					$insertDefinedTypesQry = imw_query($insertDefinedTypesStr);
					$counter++;
				}
			}
		}
		echo $counter;
	break;
	
    case 'delete':
        $id = $_POST['pkId'];
        //$q 		= "UPDATE ".$table." set status='1' WHERE ".$pkId." IN (".$id.")";
        //$res 	= imw_query($q);
        // DELETE TYPE
        $deleteLenseTypeStr = "DELETE  FROM lenses_iol_type WHERE " . $pkId . " IN (" . $id . ")";
        $delDefinesQry = imw_query($deleteLenseTypeStr);

        // DELETE DEFINES BASED ON TYPE ID
        $delDefinesStr = "DELETE FROM lensesdefined WHERE " . $pkId . " IN (" . $id . ")";
        $delDefinesQry = imw_query($delDefinesStr);
        if ($delDefinesQry && $delDefinesQry) {
            echo '1';
        } else {
            echo '0'; //.imw_error()."\n".$q;
        }
        break;
    case 'save_update':
        $id = $_POST[$pkId];
        unset($_POST[$pkId]);
        unset($_POST['task']);
        $query_part = "";
        foreach ($_POST as $k => $v) {
            $query_part .= $k . "='" . addslashes($v) . "', ";
        }
        $query_part = substr($query_part, 0, -2);
        $qry_con = "";
        if ($id == '') {
            $q = "INSERT INTO " . $table . " SET " . $query_part;
        } else {
            $q = "UPDATE " . $table . " SET " . $query_part . " WHERE " . $pkId . " = '" . $id . "'";
        }
        $res = imw_query($q);
        if ($res) {
            echo 'Record Saved Successfully.';
        } else {
            echo 'Record Saving failed.' . imw_error() . "\n" . $q;
        }
        break;
    case 'show_list':
        $q = "SELECT iol_type_id,lenses_category, lenses_iol_type,lenses_manufacturer, lenses_brand FROM " . $table . " ORDER BY $so $soAD";
        $r = imw_query($q);
        $rs_set = array();
        if ($r && imw_num_rows($r) > 0) {
            while ($rs = imw_fetch_assoc($r)) {
                $rs_set[] = $rs;
            }
        }
        echo json_encode(array('records' => $rs_set));
        break;
    default:
}
?>