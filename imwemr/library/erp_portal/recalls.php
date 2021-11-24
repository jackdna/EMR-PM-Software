<?php
class Recalls
{
  public $skip_exception;
  public function __construct()
	{
    $this->skip_exception=0;
	}

  function sent_update_req($patient_arr,$patient_id,$resource,$method){
    $response=array();
    try{
    $arIn=array();
    $arIn["skip_exception"] = $this->skip_exception;
    $erpPortalCore = new ERP_portal_core($arIn);
    /*Rabbit MQ call to create patient at Portal*/
    $response=$erpPortalCore->CURL($patient_arr,$resource,$method);
    $response=json_decode($response, true);
    }catch(Exception $e){  }
    return $response;
  }

  function update_pt_portal($ar_params, $fDelId="0"){
    if(count($ar_params) > 0){
        //API Resource
        $patient_arr=array();
        $patient_id = ''; //$_SESSION["authId"]."-".time();
        $patient_arr = $ar_params;
        if(!empty($fDelId)){
          $resource = 'api/Recalls?externalId='.$ar_params["externalId"]; //&page=1&itemsPerPage=1
          $method = 'DELETE';
        }else{
          $resource = 'api/Recalls'; //&page=1&itemsPerPage=1
          $method = 'POST';
        }

        //
        list($flg_valid, $msg) = $this->validate($patient_arr);
        if($flg_valid){
          //
          $response = $this->sent_update_req($patient_arr,$patient_id,$resource,$method);

          if($method != 'DELETE'){
            $this->save_update_id($response);
          }
        }else{
          $this->save_log($patient_arr, $msg);
        }
    }
  }

  function save_log($patient_arr, $msg){
    $operator_id = $_SESSION["authId"];
    $dt = date("Y-m-d H:i:s");
    $ar_msg = array("Message"=>$msg);
    $msg = json_encode($ar_msg);
    $request_data = json_encode($patient_arr);
    $sql = "INSERT INTO erp_api_log (request_data, request_date_time, response_data, response_date_time, operator_id)
            VALUES ( '".sqlEscStr($request_data)."', '".sqlEscStr($dt)."',
                    '".sqlEscStr($msg)."', '".sqlEscStr($dt)."',
                    '".sqlEscStr($operator_id)."' ) ";
    $row = sqlQuery($sql);
  }

  function validate($ar){
    $msg = '';
    if(isset($ar)){
      if(isset($ar["LocationExternalId"]) && !empty($ar["LocationExternalId"])){
        $sql = "SELECT id FROM facility WHERE erp_id!='' AND id='".sqlEscStr($ar["LocationExternalId"])."' ";
        $row = sqlQuery($sql);
        if($row==false || empty($row["id"])){
          $msg .= 'Invalid Facility Id, ';
        }
      }
      if(isset($ar["DoctorExternalId"]) && !empty($ar["DoctorExternalId"])){
        //
        $sql = "SELECT id FROM users WHERE erp_doctor_id!='' AND id='".sqlEscStr($ar["DoctorExternalId"])."' ";
        $row = sqlQuery($sql);
        if($row==false || empty($row["id"])){
          $msg .= 'Invalid User Id, ';
        }
      }
      if(isset($ar["PatientExternalId"]) && !empty($ar["PatientExternalId"])){
        $sql = "SELECT id FROM patient_data WHERE erp_patient_id!='' AND id='".sqlEscStr($ar["PatientExternalId"])."' ";
        $row = sqlQuery($sql);
        if($row==false || empty($row["id"])){
          $msg .= 'Invalid Patient Id, ';
        }
      }
    }
    $flg = (empty($msg)) ? true : false ;
    return array($flg, $msg);
  }

  function save_update_id($ar){
    if(count($ar) > 0){
      if(!empty($ar["externalId"]) && !empty($ar["id"])){
        $sql ="UPDATE patient_app_recall SET ext_id='".sqlEscStr($ar["id"])."' WHERE id='".sqlEscStr($ar["externalId"])."' ";
        $res = sqlQuery($sql);
      }
    }
  }

  function reset_ext_id($id,$ext_id=''){
    $dummy = "-1";
    if($ext_id=="DUMMY"){ $ext_id=$dummy; }
    //Update ex_id with dUMMY_value
    if(!empty($id) && $id!="ALL")
    {
      $sql .=  "UPDATE patient_app_recall SET ext_id='".$ext_id."' WHERE id='".$id."' LIMIT 1 ";
    }
    else if($id=="ALL")
    {
      $sql = "UPDATE patient_app_recall SET ext_id='' WHERE ext_id='".$dummy."' ";
    }
    $row = imw_query($sql) ;
  }

  function upload(){
    $index = $_REQUEST['index'] ? $_REQUEST['index'] : 0;
    $page = $_REQUEST['page'] ? $_REQUEST['page'] : 1;
    $length = 10;
    $response = array('last_page' => true, 'msg' => '');
    $this->skip_exception=1;

    //mark
    if(empty($index)){
      $this->reset_ext_id("ALL");
    }

    $qryERPTotal = "SELECT id FROM patient_app_recall
                  WHERE ((ext_id = '' OR ext_id IS NULL OR ext_id = '0' ) AND
                          (id!='' AND id!='0' AND
                            recalldate!='' AND recalldate!='0' AND recalldate!='0000-00-00' AND
                            facility_id!='' AND facility_id!='0' AND
                            operator!='' AND operator!='0' AND
                            patient_id!='' AND patient_id!='0'
                          )  )
    				ORDER BY id ASC ";
    $sqlERPTotal = imw_query($qryERPTotal) ;
    $cntERPTotal = imw_num_rows($sqlERPTotal);

    $qryERP = "SELECT id,recalldate,facility_id,operator,patient_id
          FROM patient_app_recall
          WHERE ((ext_id = '' OR ext_id IS NULL OR ext_id = '0' ) AND
                  (id!='' AND id!='0' AND
                    recalldate!='' AND recalldate!='0' AND recalldate!='0000-00-00' AND
                    facility_id!='' AND facility_id!='0' AND
                    operator!='' AND operator!='0' AND
                    patient_id!='' AND patient_id!='0'
                  )  )
    			ORDER BY id ASC LIMIT 0, $length ";

    $sqlERP = imw_query($qryERP) ;
    $cntERP = imw_num_rows($sqlERP);
    $counter=0;
    $msg_info=array();

    if($sqlERP && $cntERP ){

        $next_index = ($cntERP < $length) ? $index + $cntERP : $index+$length;
        $last_page = ($cntERP < $length || $cntERP == $cntERPTotal) ? true : false;
        while( $resERP = imw_fetch_assoc($sqlERP) ) {

            //Update ex_id with dummy_value
            $this->reset_ext_id($resERP["id"],'DUMMY');

            if(empty($resERP["patient_id"]) || empty($resERP["operator"]) ||
              empty($resERP["facility_id"]) || empty($resERP["recalldate"]) || empty($resERP["id"])){
                $counter++;
                 continue;
            }

    				$patient_arr = array();
    				$patient_arr["Date"]=$resERP["recalldate"];
    				$patient_arr["Active"]=true;
    				$patient_arr["LocationExternalId"]=$resERP["facility_id"];
    				$patient_arr["DoctorExternalId"]=$resERP["operator"];
    				$patient_arr["PatientExternalId"]=$resERP["patient_id"];
    				$patient_arr["Id"]="";
    				$patient_arr["ExternalId"]=$resERP["id"];
    				$this->update_pt_portal($patient_arr);

            $counter++;
        }

        //if(count($msg_info)>0){
            //
        //} else {
            $response = array('last_page' => $last_page, 'next_index' => $next_index , 'next_page' => ($page+1), 'msg' => "Upload Recalls to imwemr Portal:<br/> ". ($next_index)." Recall(s) processed!");
        //}

    }
    else
    {
        $response = array('last_page' => true, 'msg' => "Upload Recalls to imwemr Portal: All Recall(s) are processed!");
    }

    echo json_encode($response);
    exit();
  }
}
