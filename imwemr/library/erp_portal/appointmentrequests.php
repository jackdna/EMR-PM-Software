<?php
class AppointmentRequests
{
  public function __construct()
	{

	}

  function get_app_req_id($appid){
    $ret =0;
    $sql = "SELECT appt_req_id FROM iportal_app_reqs WHERE id = '".$appid."' ";
    $row=sqlQuery($sql);
    if($row!=false && !empty($row["appt_req_id"])){
      $ret = $row["appt_req_id"];
    }
    return $ret;
  }

  function sent_update_req($patient_arr,$patient_id,$resource,$method){
    $response=$erp_error=array();
    try{
		$OBJRabbitmqExchange = new Rabbitmq_exchange();
		/*Rabbit MQ call to create patient at Portal*/
		$response=$OBJRabbitmqExchange->send_request($patient_arr,$patient_id,$resource,$method);
		$response=json_decode($response, true);
    }catch(Exception $e){
		$erp_error[]='Unable to connect to ERP Portal';
	}
    return $response;
  }

  function updatePortal($recid, $res, $usr_con=0){
    $ret = 1;
    $appid = $this->get_app_req_id($recid);

    if(!empty($appid)){

      //call from user console
      if(!empty($usr_con)){
        $res = ($res=='1') ? 'aprv':'dcln';
      }

      if($res=='aprv'){
        $aprved = true;
        $res_msg = "Approved";
        $err_msg = "";
      }else{
        $aprved = false;
        $res_msg = "Declined";
        $err_msg = "";
      }

      $patient_arr=array();
      //API Resource
      $resource='appointmentrequests'; //&page=1&itemsPerPage=1
      $method='POST';
      $patient_id = $_SESSION["authId"]."-".time();
      $patient_arr["InternalID"]=$appid;
      $patient_arr["Approved"]=$aprved;
      $patient_arr["ResultMessage"]=$res_msg;
      $patient_arr["ErrorMessage"]=$err_msg;

      //
      $response = $this->sent_update_req($patient_arr,$patient_id,$resource,$method);

      if(count($response) > 0) {
          $ret = $this->update_log_req_data($response, $recid, $res);
      }
      //
    }

    if(!empty($usr_con)){
      if($ret==0){
          $ret = ($res=='aprv') ? "Approved" : "Declined" ;
      }
    }

    echo $ret;
  }

  function update_log_req_data($response, $appid, $res){
    $aprv_dec = ($res=="aprv") ? "1" : "2";
    $action_date_time = date("Y-m-d H:i:s");
    $operator_id = $_SESSION["authId"];
    $sql = "UPDATE iportal_app_reqs
            SET aprv_dec='".$aprv_dec."', action_date_time='".$action_date_time."',
                operator_id='".$operator_id."'
            WHERE id='".$appid."'
          ";
    sqlQuery($sql);
    return 0 ;
  }

  public function getRequests()
  {
      $patient_arr=array();
      //API Resource
      $resource='appointmentrequests/search?alreadySent=false'; //&page=1&itemsPerPage=1
      $method='GET';
      $patient_id = $_SESSION["authId"]."-".time();
      /*Rabbit MQ call to create patient at Portal*/
      $response=$this->sent_update_req($patient_arr,$patient_id,$resource,$method);

      //if(count($response) > 0 && $response['externalId']==$patient_id) {
          $this->log_req_data($response);
      //}
  }


  function send_acknow($ar_uniq_req_id){
    if(count($ar_uniq_req_id) > 0){
      foreach($ar_uniq_req_id as $k => $appid){
        if(!empty($appid)){
          $patient_arr=array();
          //API Resource
          $resource='AppointmentRequestsSent'; //&page=1&itemsPerPage=1
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

  public function is_valid_pt($id){
      $ret = 0;
      $id=trim($id);
      if(!empty($id)){
        $oPt = new Patient($id);
        $nm = $oPt->getName("7");
        if(!empty($nm))
        {
            $ret = 1;
        }
      }
      return $ret;
  }

  public function is_app_req_exists($id){
    $ret = 0 ;
    if(!empty($id)){
      $sql = "SELECT id FROM iportal_app_reqs WHERE appt_req_id='".sqlEscStr($id)."' ";
      $row = sqlQuery($sql);
      if($row!=false && !empty($row["id"])){
        $ret = $row["id"];
      }
    }
    return $ret;
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
          foreach($ar_rws as $k => $rw){
              $pt_ex_id = $rw["patientExternalId"];
              $validPt = $this->is_valid_pt($pt_ex_id);
              $len_ap = (isset($rw["appointmentRequestList"])) ? count($rw["appointmentRequestList"]) : 0 ;
              if(!empty($len_ap)){
                $ar_rqs = $rw["appointmentRequestList"];
                foreach($ar_rqs as $krq => $rq){
                  if(!empty($rq["appointmentRequestID"]) && !in_array($rq["appointmentRequestID"], $ar_uniq_req_id)){
                    $app_req_id_ed = $this->is_app_req_exists($rq["appointmentRequestID"]);
                    if(!empty($app_req_id_ed)){
                    // do noting
                    } else {
                    $ar_sql[] = "( '".sqlEscStr($pt_ex_id)."',
                              '".sqlEscStr($rq["appointmentRequestID"])."', '".sqlEscStr($rq["visionInsuranceCarrier"])."', '".sqlEscStr($rq["visionGroupNumber"])."',
                              '".sqlEscStr($rq["visionPolicyNumber"])."', '".sqlEscStr($rq["medicalInsuranceCarrier"])."', '".sqlEscStr($rq["medicalGroupNumber"])."',
                              '".sqlEscStr($rq["medicalPolicyNumber"])."',
                              '".sqlEscStr($rq["requestedDate"])."', '".sqlEscStr($rq["comments"])."', '".sqlEscStr($rq["patientEmailAddress"])."',
                              '".sqlEscStr($rq["phoneNumberType"])."',
                              '".sqlEscStr($rq["phoneNumber"])."', '".sqlEscStr($rq["status"])."', '".sqlEscStr($rq["appointmentRequestReasonId"])."',
                              '".sqlEscStr($rq["countryId"])."',
                              '".sqlEscStr($rq["appointmentExternalId"])."', '".sqlEscStr($rq["doctorExternalId"])."', '".sqlEscStr($rq["locationExternalId"])."',
                              '".$validPt."','0','".$operator."','".$curdt."','".sqlEscStr($pt_ex_id)."'
                              )";

                    }
                    $ar_uniq_req_id[] = $rq["appointmentRequestID"];
                  }
                }
              }

              $lnsq = count($ar_sql);
              if($lnsq > 0 && ($lnsq==100 || $len-1==$k)){
                $sql = "INSERT INTO iportal_app_reqs( patient_id, appt_req_id, vis_ins_car, vis_grp_num,
                        vis_pol_num, med_ins_car, med_grp_num,
                        med_pol_num, req_date, comments, pt_email,
                        phone_num_type, phone_num, app_status, app_req_rsn_Id,
                        country_id, app_ext_id, doc_ext_id, loc_ext_id,
                        valid_pt, aprv_dec, operator_id, created_on, pt_ext_id
                      ) VALUES ".implode(",", $ar_sql);
                sqlQuery($sql);
                $ar_sql=array();
              }
          }

          //acknw Pt Portal
          $this->send_acknow($ar_uniq_req_id);
      }
  }



  function get_app_reqs(){
      $sql = "SELECT patient_id,appt_req_id, vis_ins_car, vis_grp_num,
              vis_pol_num, med_ins_car, med_grp_num, med_pol_num, req_date,
              comments, pt_email, phone_num_type, phone_num,
              app_status, app_req_rsn_Id, country_id, app_ext_id, doc_ext_id,
              loc_ext_id, operator_id, created_on, pt_ext_id, id as iportal_app_reqs_id
              FROM iportal_app_reqs
              where valid_pt='1' AND aprv_dec='0' AND app_can_req_id='' AND appt_req_id!='' ORDER BY req_date ";
      $res = sqlStatement($sql);
      return $res;
  }

  function show_requests($mthd=""){
    $tbl="";
    $res = $this->get_app_reqs();
    if(imw_num_rows($res)>0){
      for($i=1;$row=sqlFetchArray($res);$i++){
        extract($row);

        $oPt = new Patient($patient_id);
        $pt_nm = $oPt->getName("7");

        $oUsr = new User($doc_ext_id);
        $doc_nm = $oUsr->getName("3");

        $oFac = new Facility($loc_ext_id);
        $fac_nm = $oFac->getFacilityName();

        $ar_req_date = explode("T", $req_date);
        $tmp_req_dt = isset($ar_req_date[0]) ? $ar_req_date[0] : "";
        $ar_req_tm = isset($ar_req_date[1]) ? explode(".",$ar_req_date[1]) : array();
        $tmp_req_tm = isset($ar_req_tm[0]) ? $ar_req_tm[0] : "";
        $req_date = trim(get_date_format($tmp_req_dt)." ".core_time_format($tmp_req_tm));
        //for($j=1; $j<500; $j++){
        $tbl .= "<tr >
                  <td>".$i."</td>
                  <td>".$pt_nm."</td>
                  <td>".$req_date."</td>
                  <td>".$comments."</td>
                  <td>".$doc_nm."</td>
                  <td>".$fac_nm."</td>
                  <td>
                    <button type=\"button\" class=\"btn btn-xs btn-success btn_aprv\" data-app_id=\"".$iportal_app_reqs_id."\">Approve</button>
                    <button type=\"button\" class=\"btn btn-xs btn-danger btn_dcln\" data-app_id=\"".$iportal_app_reqs_id."\">Decline</button>
                  </td>
                </tr>";
        //}
      }

      if(!empty($tbl)){
        $tbl = "<table class=\"table table-striped table-bordered\">
                <thead>
                <tr class=\"grythead vlign-top\">
                  <th>Sr.</th>
                  <th>Patient</th>
                  <th>Request Date</th>
                  <th>Comments</th>
                  <th>Doctor</th>
                  <th>Location</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody>
                ".$tbl."
                </tbody>
              </table>";
      }
    }

    //
    if(empty($tbl)){
      $tbl = "No record found!";
    }

    if(!empty($mthd) && $mthd=="popup"){
      return $tbl;
    }
  }

  function get_app_reqs_qry(){
    $sql = "SELECT patient_id as pt_id, aprv_dec as is_approved, 'iportal_app_reqs' as tb_name,
          id, comments as title_msg, req_date as reqDateTime,
          DATE_FORMAT(req_date,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime2, app_can_req_id, can_reason
    FROM `iportal_app_reqs`
    WHERE valid_pt='1' AND app_can_req_id='' AND appt_req_id!=''
    ";
    return $sql;
  }

  function get_req_inf($rec_id, $ptId){
      $sqlMsg = "SELECT patient_id as pt_id,
              DATE_FORMAT(req_date,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime,
              comments as new_val_lbl
              FROM iportal_app_reqs
              WHERE valid_pt='1' AND app_can_req_id=''
              AND appt_req_id!=''
              AND patient_id = '".$ptId."' AND id = '".$rec_id."' ";
      return  $sqlMsg;
  }


	/* communicationupdatesappointments code starts here*/
	public function getUpdatesAppointments()
	{
	  $patient_arr=array();
	  //API Resource
	  $resource='communicationupdatesappointments/search?alreadySent=false'; //&page=1&itemsPerPage=1
	  $method='GET';
	  $messageId=$_SESSION["authId"]."-".time();
	  /*Rabbit MQ call to create patient at Portal*/
	  $response=$this->sent_update_req($patient_arr,$messageId,$resource,$method);

	  return $response;
	  //if(count($response) > 0 && $response['externalId']==$patient_id) {
		  //$this->log_req_data($response);
	  //}
	}
  
	/*IM-7589 :- Mark appointment status as received from Portal to IMW as successful*/
	public function appointmentUpdatesSent($idsArr=array()){
		if(count($idsArr)>0) {
			$idsChunkArr=array_chunk($idsArr,5);
			global $OBJRabbitmqExchange;
			foreach($idsChunkArr as $chunkArr) {
				foreach($chunkArr as $internalID) {
					$data=array();
					$data['InternalID']=$internalID;
					$data['Success']=true;
					$data['ResultMessage']='Success';
					
					$messageId=$internalID.'-'.time();
					$method="POST";
					$resource='AppointmentUpdatesSent';
					//Rabbit MQ call to send acknowledege regarding received Communication Preferences at Portal
					$result=$OBJRabbitmqExchange->send_request($data,$messageId,$resource,$method);
	
				}
				unset($chunkArr);
			}
		}
	}
	
	/* communicationupdatesappointments code ends here*/
}
