<?php
class IncomingSecureMessages
{
  public function __construct()
	{

	}

  function sent_update_req($patient_arr,$patient_id,$resource,$method){
    $response=array();
    try{
    $OBJRabbitmqExchange = new Rabbitmq_exchange();
    /*Rabbit MQ call to create patient at Portal*/
    $response=$OBJRabbitmqExchange->send_request($patient_arr,$patient_id,$resource,$method);
    $response=json_decode($response, true);
    }catch(Exception $e){  }
    return $response;
  }

  public function getMsgs()
  {
      $patient_arr=array();
      //API Resource
      $resource='incomingSecureMessages/search?alreadySent=false'; //&page=1&itemsPerPage=1
      $method='GET';
      $patient_id = $_SESSION["authId"]."-".time();
      /*Rabbit MQ call to create patient at Portal*/
      $response=$this->sent_update_req($patient_arr,$patient_id,$resource,$method);
      if(count($response) > 0) {
          $this->log_req_data($response);
      }
  }

  function send_acknow($ar_uniq_req_id){
    if(count($ar_uniq_req_id) > 0){
      foreach($ar_uniq_req_id as $k => $appid){
        if(!empty($appid)){
          $patient_arr=array();
          //API Resource
          $resource='IncomingSecureMessagesSent'; //&page=1&itemsPerPage=1
          $method='POST';
          $patient_id = $_SESSION["authId"]."-".time();
          $patient_arr["InternalID"]=$appid;
          $patient_arr["Success"]=true;
          $patient_arr["ResultMessage"]="Recieved";

          //
          $response = $this->sent_update_req($patient_arr,$patient_id,$resource,$method);
        }
      }
    }
  }

  public function is_msg_exists_idoc($id){
    $ret = 0 ;
    if(!empty($id)){
      $sql = "SELECT pt_msg_id as id FROM patient_messages WHERE iportal_msg_id='".sqlEscStr($id)."' ";
      $row = sqlQuery($sql);
      if($row!=false && !empty($row["id"])){
        $ret = $row["id"];
      }
    }
    return $ret;
  }

  function frmt_date($req_date){
    $ar_req_date = explode("T", $req_date);
    $tmp_req_dt = isset($ar_req_date[0]) ? $ar_req_date[0] : "";
    $ar_req_tm = isset($ar_req_date[1]) ? explode(".",$ar_req_date[1]) : array();
    $tmp_req_tm = isset($ar_req_tm[0]) ? $ar_req_tm[0] : "";
    $req_date = trim($tmp_req_dt." ".$tmp_req_tm);
    return $req_date;
  }

  public function log_req_data($response)
  {
      $len = (isset($response["rows"])) ? count($response["rows"]) : 0 ;
      if(!empty($len)){
          $ar_uniq_req_id=array();
          $ar_sql=array();
          $ar_rws = $response["rows"];
          $operator = $_SESSION["authId"];
          $curdt = date("Y-m-d H:i:s");
          $ar_uniq_req_id=array();
          foreach($ar_rws as $k => $rw){
            if(!empty($rw["id"]) && !in_array($rw["id"], $ar_uniq_req_id) && !$this->is_msg_exists_idoc($rw["id"])){
              $pt_ex_id = $rw["patientExternalId"];
              $msg_id = $rw["id"];
              $fromRepresentative = $rw["fromRepresentative"];
              $doc_id = $rw["doctorExternalId"];
              $create_dt = $rw["creationDate"];
              $priority = $rw["priority"];
              $status = $rw["status"];
              $messageRead = $rw["messageRead"];
              $rcpnt_id = $rw["secureRecipientExternalId"];
              $subj = $rw["subject"];
              $body = $rw["body"];
              $sentDate = $rw["sentDate"];
              $type = ($rw["type"] == "AppointmentRequest") ? "1" : "0";
              $deletedByUser = $rw["deletedByUser"];
              $deletedByPatient = $rw["deletedByPatient"];
              $del_st = (!empty($deletedByUser) || !empty($deletedByPatient)) ? "1" : "0";
              $sentDate = $this->frmt_date($sentDate);

              $ar_sql[] = "(
                        '".sqlEscStr($rcpnt_id)."', '".sqlEscStr($pt_ex_id)."','0','2', '".sqlEscStr($subj)."',
                        '".sqlEscStr($body)."',
                        '".sqlEscStr($priority)."', '0','".sqlEscStr($sentDate)."', '".date('Y-m-d H:i:s')."',
                        '".sqlEscStr($type)."','0','0','0','".$del_st."', '".$deletedByPatient."',
                        '".sqlEscStr($fromRepresentative)."', '".sqlEscStr($msg_id)."'
                        )";
              $ar_uniq_req_id[] = $msg_id;
            }
          }

          $lnsq = count($ar_sql);
          if($lnsq > 0){
            $sql = "INSERT INTO patient_messages( receiver_id, sender_id, replied_id, communication_type,
                    msg_subject, msg_data,
                    flagged, msg_icon, delivery_date,
                    msg_date_time, is_appt, is_read, is_done,
                    message_urgent, del_status, del_status_by_pt,
                    from_rep, iportal_msg_id
                  ) VALUES ".implode(",", $ar_sql);
            sqlQuery($sql);

            //acknw Pt Portal
            $this->send_acknow($ar_uniq_req_id);
          }
      }
  }

  public function get_iportal_id($id){
    $ret = 0 ;
    if(!empty($id)){
      $sql = "SELECT iportal_msg_id as id FROM patient_messages WHERE pt_msg_id='".sqlEscStr($id)."' AND is_read='0' AND del_status='0' ";
      $row = sqlQuery($sql);
      if($row!=false && !empty($row["id"])){
        $ret = $row["id"];
      }
    }
    return $ret;
  }

  public function markRead($id, $flg_read){
      $msg_id = $this->get_iportal_id($id);
      if(!empty($msg_id)){
        $patient_arr=array();
        //API Resource
        $resource='incomingSecureMessages/'.$msg_id.'?read='.$flg_read;
        $method='POST';
        $patient_id = $_SESSION["authId"]."-".time();        
        $response=$this->sent_update_req($patient_arr,$patient_id,$resource,$method);
      }
  }
}
