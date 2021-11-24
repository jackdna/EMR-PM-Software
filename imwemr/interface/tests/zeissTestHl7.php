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
 *File: zeissTestHl7.php
 *Purpose: Make Hl7 message for ZEISS FORUM
 *Access Type: Include
 */
require_once("../../config/globals.php");

$testData = array(
                  "A/SCAN" => array("code"=>1, "patient_column"=>"patient_id", "uid_col"=>"surgical_id", "table"=>"surgical_tbl", "tcode"=>"ASCAN"),
                  "BSCAN" => array("code"=>2, "patient_column"=>"patientId", "uid_col"=>"test_bscan_id", "table"=>"test_bscan", "tcode"=>"BSCAN"),
                  "CELLCOUNT" => array("code"=>3, "patient_column"=>"patientId", "uid_col"=>"test_cellcnt_id", "table"=>"test_cellcnt", "tcode"=>"CELLCOUNT"),
                  "DISCEXTERNAL" => array("code"=>4, "patient_column"=>"patientId", "uid_col"=>"disc_id", "table"=>"disc_external", "tcode"=>"DISCEXTERNAL"),
                  "DISC" => array("code"=>5, "patient_column"=>"patientId", "uid_col"=>"disc_id", "table"=>"disc", "tcode"=>"DISC"),
                  "ICG" => array("code"=>6, "patient_column"=>"patient_id", "uid_col"=>"icg_id", "table"=>"icg", "tcode"=>"ICG"),
                  "IVFA" => array("code"=>8, "patient_column"=>"patient_id", "uid_col"=>"vf_id", "table"=>"ivfa", "tcode"=>"IVFA"),
                  "IOL-MASTER" => array("code"=>7, "patient_column"=>"patient_id", "uid_col"=>"iol_master_id", "table"=>"iol_master_tbl", "tcode"=>"IOLMASTER"),
                  "OCT" => array("code"=>9, "patient_column"=>"patient_id", "uid_col"=>"oct_id", "table"=>"oct", "tcode"=>"OCT"),
                  "OCT-RNFL" => array("code"=>10, "patient_column"=>"patient_id", "uid_col"=>"oct_rnfl_id", "table"=>"oct_rnfl", "tcode"=>"OCTRNFL"),
                  "TOPOGRAPHY" => array("code"=>11, "patient_column"=>"patientId", "uid_col"=>"topo_id", "table"=>"topography", "tcode"=>"TOPOGRAPHY"),
                  "VF" => array("code"=>12, "patient_column"=>"patientId", "uid_col"=>"vf_id", "table"=>"vf", "tcode"=>"VF"),
                  "VF-GL" => array("code"=>13, "patient_column"=>"patientId", "uid_col"=>"vf_gl_id", "table"=>"vf_gl", "tcode"=>"VFGL")
              );

/*Get Name of Facility*/
$mapVals = array("BK"=>"IMBRO","FM"=>"IMFRM","LM"=>"IMLEO","MTAB"=>"IMMTA","STEL"=>"IMSTE","WT"=>"IMWAT","WL"=>"IMWEL","BO"=>"IMBOS");
$dt  = imw_query("SELECT `appt`.`sa_facility_id` FROM `schedule_appointments` `appt` INNER JOIN `users` `u` ON(`appt`.`sa_doctor_id`=`u`.`id`) LEFT JOIN `user_type` `ut` ON(`u`.`user_type`=`ut`.`user_type_id`) WHERE `appt`.`sa_patient_id`='".$zeissPatientId."' AND `appt`.`sa_patient_app_status_id`!='18' AND `appt`.`sa_app_start_date`='".date("Y-m-d")."' AND `ut`.`user_type_name`='Test' ORDER BY `appt`.`id` DESC LIMIT 1");
if($dt && imw_num_rows($dt)>0){
    $facility = imw_fetch_assoc($dt); $facility = $facility['sa_facility_id'];
    $dt1 = imw_query("SELECT `sl`.`abbre` FROM `facility` `f` LEFT JOIN `server_location` `sl` ON(`f`.`server_location`=`sl`.`id`) WHERE `f`.`id`='".$facility."'");
    if($dt1){
        $dt1 = imw_fetch_assoc($dt1);
        $loginFacilityName = $mapVals[$dt1['abbre']];
    }
}
else{    
    $dt  = imw_query("SELECT `appt`.`sa_facility_id` FROM `schedule_appointments` `appt` INNER JOIN `users` `u` ON(`appt`.`sa_doctor_id`=`u`.`id`) LEFT JOIN `user_type` `ut` ON(`u`.`user_type`=`ut`.`user_type_id`) WHERE `appt`.`sa_patient_id`='".$zeissPatientId."' AND `appt`.`sa_patient_app_status_id`!='18' AND `appt`.`sa_app_start_date`='".date("Y-m-d")."' ORDER BY `appt`.`id` DESC LIMIT 1");
    if($dt && imw_num_rows($dt)>0){
        $facility = imw_fetch_assoc($dt); $facility = $facility['sa_facility_id'];
        $dt1 = imw_query("SELECT `sl`.`abbre` FROM `facility` `f` LEFT JOIN `server_location` `sl` ON(`f`.`server_location`=`sl`.`id`) WHERE `f`.`id`='".$facility."'");
        if($dt1){
            $dt1 = imw_fetch_assoc($dt1);
            $loginFacilityName = $mapVals[$dt1['abbre']];
        }
    }
    else{
        $dt1 = imw_query("SELECT `sl`.`abbre` FROM `facility` `f` LEFT JOIN `server_location` `sl` ON(`f`.`server_location`=`sl`.`id`) WHERE `f`.`id`='".$_SESSION['login_facility']."'");
        if($dt1){
            $dt1 = imw_fetch_assoc($dt1);
            $loginFacilityName = $mapVals[$dt1['abbre']];
        }
    }
}

/*Get Name and ID of Ordering Provier*/
$orderingProvider = array();
$phyId = $_REQUEST['elem_opidTestOrdered'];
$sql = imw_query("SELECT `user_npi`, `fname`, `lname`, `mname` FROM `users` WHERE `id`='".$phyId."'");
if($sql && imw_num_rows($sql)>0){
    $sql = imw_fetch_assoc($sql);
    $orderingProvider['id'] = $sql['user_npi'];
    $orderingProvider['fname'] = $sql['fname'];
    $orderingProvider['lname'] = $sql['lname'];
    $orderingProvider['mname'] = $sql['mname'];
}
else{
    
    $sql = imw_query("SELECT `u`.`user_npi`, `u`.`fname`, `u`.`lname`, `u`.`mname` FROM `patient_data` `pd` INNER JOIN `users` `u` ON(`pd`.`providerID`=`u`.`id`) WHERE `pd`.`id`='".$zeissPatientId."'");
    if($sql && imw_num_rows($sql)>0){
        $sql = imw_fetch_assoc($sql);
        $orderingProvider['id'] = $sql['user_npi'];
        $orderingProvider['fname'] = $sql['fname'];
        $orderingProvider['lname'] = $sql['lname'];
        $orderingProvider['mname'] = $sql['mname'];
    }
}


if(constant("ZEISS_FORUM") == "YES" && isset($zeissAction) && $zeissAction==1){
    
    $elem_saveForm1 = strtoupper($zeissMsgType);
    if(in_array($elem_saveForm1, array_keys($testData))){
        /*Update `forum_counter` in test table*/
        $sqlFC = "UPDATE `".$testData[$elem_saveForm1]['table']."` SET `forum_counter`=`forum_counter`+1 WHERE `".$testData[$elem_saveForm1]['uid_col']."`='".$zeissTestId."'";
        
        if(imw_query($sqlFC)){
            
            $sqlFC1 = "SELECT `forum_counter` FROM `".$testData[$elem_saveForm1]['table']."` WHERE `".$testData[$elem_saveForm1]['uid_col']."`='".$zeissTestId."'";
            $FC = imw_query($sqlFC1);
            $FC = imw_fetch_assoc($FC);
            $FC = $FC['forum_counter']; /*Get `forum_counter` for test table*/
                
            $path = dirname(__FILE__)."/../../hl7sys/hl7GP/";
            include($path."hl7_feedData.php");
            $hl7 = new hl7_feedData();
            $hl7->filePath = $path;
            $hl7->PD['id'] = $zeissPatientId;
            $hl7->insertSegment("PID");
            
            $data = array();
            $data['order_control'] = "NW";
            $data['placer_order_number'] =$zeissTestId.padVals($testData[$elem_saveForm1]['code']).padVals((int)$FC);
            $data['ordering_facility_name'] = $loginFacilityName;
            
            $data['ordering_provider']['id'] = $orderingProvider['id'];
            $data['ordering_provider']['family_name'] = $orderingProvider['lname'];
            $data['ordering_provider']['given_name'] = $orderingProvider['fname'];
            $data['ordering_provider']['middle_name'] = $orderingProvider['mname'];
            $hl7->addSegment("ORC", $data);
            
            $sql = "SELECT `order_type` FROM `zeiss_forum_order_type` WHERE `id`='".addslashes($forum_procedure)."'";
            $resp = imw_query($sql);
            $respDt_rs = imw_fetch_assoc($resp);
            $respDt = $respDt_rs['order_type'];
            $data = array();
            $data['setId_OBR'] = 1;
            $data['placer_order_number'] = $zeissTestId.padVals($testData[$elem_saveForm1]['code']).padVals((int)$FC);
            $data['universal_service_identifier']['identifer'] = $forum_procedure;
            $data['universal_service_identifier']['text'] = $respDt;
            $data['universal_service_identifier']['name_of_coding_system'] = "FORUM";
            $data['universal_service_identifier']['alternate_text'] = $respDt;
            $hl7->addSegment("OBR", $data);
            
            $hl7->log_message("zeiss_forum", $zeissTestId, $elem_saveForm1, "FORUM_ADD");
        }
    }
}
elseif(constant("ZEISS_FORUM")=="YES" && isset($zeissAction) && ($zeissAction==2 || $zeissAction==3)){
    
    $testName1 = strtoupper($zeissMsgType);
    if(in_array($testName1, array_keys($testData))){
        
        $sqlFC1 = "SELECT `forum_counter` FROM `".$testData[$testName1]['table']."` WHERE `".$testData[$testName1]['uid_col']."`='".$zeissTestId."'";
        $FC = imw_query($sqlFC1);
        $FC = imw_fetch_assoc($FC);
        $FC = $FC['forum_counter']; /*Get `forum_counter` for test table*/
        
        $path = dirname(__FILE__)."/../../hl7sys/hl7GP/";
        include($path."hl7_feedData.php");
        $hl7 = new hl7_feedData();
        $hl7->filePath = $path;
        $hl7->PD['id'] = $zeissPatientId;
        $hl7->insertSegment("PID");
        
        $data = array();
            if($zeissAction==2){$msgType = $data['order_control']="CA"; $seissPurgeStatus="FORUM_UPDATE";}
            else{$msgType = $data['order_control']="DC"; $seissPurgeStatus="FORUM_DELETE";}
        $data['placer_order_number'] = $zeissTestId.padVals($testData[$testName1]['code']).padVals((int)$FC);
        $data['ordering_facility_name'] = $loginFacilityName;
        
        $data['ordering_provider']['id'] = $orderingProvider['id'];
        $data['ordering_provider']['family_name'] = $orderingProvider['lname'];
        $data['ordering_provider']['given_name'] = $orderingProvider['fname'];
        $data['ordering_provider']['middle_name'] = $orderingProvider['mname'];
        $hl7->addSegment("ORC", $data);
        
        $sql = "SELECT `order_type` FROM `zeiss_forum_order_type` WHERE `id`='".addslashes($forum_procedure)."'";
        $resp = imw_query($sql);
        $respDt_rs = imw_fetch_assoc($resp);
		$respDt = $respDt_rs['order_type'];
        
        $data = array();
        $data['setId_OBR'] = 1;
        $data['placer_order_number'] = $zeissTestId.padVals($testData[$testName1]['code']).padVals((int)$FC);
        $data['universal_service_identifier']['identifer'] = $forum_procedure;
        $data['universal_service_identifier']['text'] = $respDt;
        $data['universal_service_identifier']['name_of_coding_system'] = "FORUM";
        $data['universal_service_identifier']['alternate_text'] = $respDt;
        $hl7->addSegment("OBR", $data);
        
        $hl7->log_message("zeiss_forum", $zeissTestId, $testName1, $seissPurgeStatus);
    }
}

function padVals($val){
    if(is_int($val))
        return str_pad($val, 4, 0, STR_PAD_LEFT);
}
?>