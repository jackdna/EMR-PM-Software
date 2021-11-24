<?php
ini_set("memory_limit","5072M");
class Pghd_requests {

    public function __construct() {
        
    }
	
	function postponePGHDPopup($patient_id) {
		if(!isset($_SESSION['POSTPONEPGHD']) || $_SESSION['POSTPONEPGHD']==''){
			$_SESSION['POSTPONEPGHD']=$patient_id;
		}else if(isset($_SESSION['POSTPONEPGHD']) && $_SESSION['POSTPONEPGHD']!='' && $_SESSION['POSTPONEPGHD']!=$patient_id) {
			$_SESSION['POSTPONEPGHD']=$patient_id;
		}
		//echo $_SESSION['POSTPONEPGHD'];
	}

    function get_pghd_req_id($pghdid) {
        $ret = 0;
        $sql = "SELECT pghd_req_id FROM iportal_pghd_reqs WHERE id = '" . $pghdid . "' ";
        $row = sqlQuery($sql);
        if ($row != false && !empty($row["pghd_req_id"])) {
            $ret = $row["pghd_req_id"];
        }
        return $ret;
    }

    function updatePortal($recid, $res, $usr_con = 0) {
        $ret = 1;
        $pghdid = $this->get_pghd_req_id($recid);
		//$pghdid = $recid;
		
        if (!empty($pghdid)) {

            //call from user console
            if (!empty($usr_con)) {
                $res = ($res == '1') ? 'aprv' : 'dcln';
            }
			/*
            $data_arr = array();
            $data_arr["id"] = $pghdid;
            $data_arr["status"] = "Processed";

            if (!empty($pghdid)) {
				include_once($GLOBALS['srcdir'] . "/erp_portal/erp_portal_core.php");
                $erpPortalCore = new ERP_portal_core();
                $response = $erpPortalCore->CURL($data_arr, "api/PghdUpdateRequestsStatus", "POST");
            }

            if (count($response) > 0) {
                $ret = $this->update_log_req_data($response, $recid, $res);
            }
			*/
			$ret = $this->update_log_req_data($response, $recid, $res);
        }

        if (!empty($usr_con)) {
            if ($ret == 0) {
                $ret = ($res == 'aprv') ? "Approved" : "Declined";
            }
        }

        echo $ret;
    }

    function update_log_req_data($response, $recid, $res) {
        $aprv_dec = ($res == "aprv") ? "1" : "2";
        $action_date_time = date("Y-m-d H:i:s");
        $operator_id = $_SESSION["authId"];
        $sql = "UPDATE iportal_pghd_reqs
            SET approved_declined='" . $aprv_dec . "', action_date='" . $action_date_time . "',
                approved_by='" . $operator_id . "'
            WHERE id='" . $recid . "'
          ";
        $result = sqlQuery($sql);
        if ($result && $aprv_dec == "1") {
            $this->update_requested_data($recid);
        }
        return 0;
    }

    function update_requested_data($recid = 0) {
		$sqlMsg = "SELECT id,patient_id,pghd_req_id,
                    demo_qry,med_qry,alrg_qry,prob_qry,ins_qry,surg_qry 
                    FROM iportal_pghd_reqs
                    WHERE id = '".$recid."' ";

        $rs = imw_query($sqlMsg);
        if ($rs && imw_num_rows($rs) > 0) {
			$reqData=imw_fetch_assoc($rs);
		
			$pid=$reqData['patient_id'];
			
			$demo_status=$ins_status=$med_status=$alrg_status=$surg_status='';
			if($reqData['demo_qry'] && $reqData['demo_qry']!='') {
				$demo_status=imw_query($reqData['demo_qry']);
			}
			if($reqData['ins_qry'] && $reqData['ins_qry']!='') {
				$ins_qryArr = unserialize($reqData['ins_qry']);
				foreach($ins_qryArr as $query) {
					$ins_status=imw_query($query);
				}
			}

			if($demo_status || $ins_status) {
				include_once($GLOBALS['srcdir']."/erp_portal/patients.php");
				$obj_patients = new Patients();
				
				$obj_patients->addUpdatePatient($pid);
				if($demo_status) {
					$getptname=imw_query("SELECT CONCAT(lname,', ',fname,' ',mname) AS ptname from patient_data where id='".$pid."' ");
					$ptnamerow=imw_fetch_assoc($getptname);
					imw_query("UPDATE schedule_appointments SET sa_patient_name='".addslashes($ptnamerow['ptname'])."' where sa_patient_id='".$pid."' ");
				}
			}
			
			if($reqData['med_qry'] && $reqData['med_qry']!='') {
				$med_qryArr = unserialize($reqData['med_qry']);
				
				foreach($med_qryArr as $query) {
					$med_status=imw_query($query);
				}
				if($med_status) {
					$medSql="Select id,title,sites,destination,sig,med_comments,allergy_status,pid,referredby from lists where pid=$pid and allergy_status IN('Active','Administered','Order') and type IN('1','4') ";
					$medRs=imw_query($medSql);
					if($medRs && imw_num_rows($medRs)>0) {
						include_once($GLOBALS['srcdir']."/erp_portal/patient_medications.php");
						$obj_patients_med = new patient_medications();
						$arrSites=array(1=>'OS', 2=>'OD', 3=>'OU', 4=>'PO');
						
						while($med_data_arr=imw_fetch_assoc($medRs)) {
							$mid=$med_data_arr['id'];
							$arrInstructions=array();
							$arrInstructions[]=$arrSites[$med_data_arr['sites']];
							$arrInstructions[]=$med_data_arr['destination'];
							$arrInstructions[]=$med_data_arr['sig']; 
							$arrInstructions[]=$med_data_arr['med_comments'];
							$arrInstructions=array_filter($arrInstructions); //REMOVE EMPTY STRINGS
							
							$arrppApi=array();
							$arrppApi['name']=$med_data_arr['title'];
							$arrppApi['instructions']=implode(', ', $arrInstructions);
							$arrppApi['patientExternalId']=$med_data_arr['pid'];
							$arrppApi['doctorExternalId']=$med_data_arr['referredby'];				
							$arrppApi['active']= ($med_data_arr['allergy_status']=='Stop' || $med_data_arr['allergy_status']=='Discontinue')? false : true;
							$arrppApi['id']='';		
							$arrppApi['externalId']=$mid;	
				
							$obj_patients_med->addUpdateMedication($med_data_arr['pid'], $arrppApi);
						}
					}
						
				}
			}
			if($reqData['alrg_qry'] && $reqData['alrg_qry']!='') {
				$alrg_qryArr = unserialize($reqData['alrg_qry']);
				foreach($alrg_qryArr as $query) {
					$alrg_status=imw_query($query);
				}
			}
			if($reqData['surg_qry'] && $reqData['surg_qry']!='') {
				$surg_qryArr = unserialize($reqData['surg_qry']);
				foreach($surg_qryArr as $query) {
					$surg_status=imw_query($query);
				}
			}
			if($reqData['prob_qry'] && $reqData['prob_qry']!='') {
				$prob_qryArr = unserialize($reqData['prob_qry']);
				foreach($prob_qryArr as $query) {
					$prob_status=imw_query($query);
				}
			}

		}
    }

    function send_acknow($ar_uniq_req_id) {
        if (count($ar_uniq_req_id) > 0) {
            foreach ($ar_uniq_req_id as $k => $pghdid) {
                if (!empty($pghdid)) {
                    $data_arr = array();
                    $data_arr["id"] = $pghdid;

                    include_once($GLOBALS['srcdir'] . "/erp_portal/erp_portal_core.php");
                    $erpPortalCore = new ERP_portal_core();
                    $response = $erpPortalCore->CURL($data_arr, "api/PghdUpdateRequestsSent", "POST");

                    $data_arr["status"] = "SentToEhr";
                    $response2 = $erpPortalCore->CURL($data_arr, "api/PghdUpdateRequestsStatus", "POST");
                }
            }
        }
    }

    public function getPghdRequests($patient_id = '') {
		if($patient_id=="") return false;
        $OBJRabbitmqExchange = new Rabbitmq_exchange();
        $data_arr = array();
        //API Resource	/*patientExternalId={patientExternalId}&createdOperator={createdOperator}&created1={created1}&created2={created2}&alreadySent={alreadySent}&page={page}&itemsPerPage={itemsPerPage}*/
		$external_id='';
		if($patient_id!=''){
			$external_id='&patientExternalId='.$patient_id;
		}

        $resource = 'pghdupdaterequests/search?alreadySent=false'.$external_id;
        $method = 'GET';
        $message_id = $_SESSION['authId'] . "-" . time();
        /* Rabbit MQ call to create patient at Portal */
        $response = $OBJRabbitmqExchange->send_request($data_arr, $message_id, $resource, $method);
				
        $response = json_decode($response, true);

        $this->log_pghd_req_data($response);
    }

    public function log_pghd_req_data($response) {
        $len = (isset($response["rows"])) ? count($response["rows"]) : 0;
        if (!empty($len)) {
            $ar_uniq_req_id = array();
            $ar_sql = array();
            $ar_rws = $response["rows"];
            $operator = $_SESSION["authId"];
            $curdt = date("Y-m-d H:i:s");
            foreach ($ar_rws as $k => $rw) {
                $id = $rw["id"];
                $pt_ex_id = $rw["patientExternalId"];
                $is_valid_pt = $this->is_valid_pt($pt_ex_id);
                if (!$is_valid_pt)
                    continue;

                $demogrArr = $this->get_demo_changed_values($rw['demographics'], $pt_ex_id);
				$demographics=(count($demogrArr['demograhics'])>0)?htmlentities($demogrArr['demograhics']):"";
				$demo_qry=(count($demogrArr['demo_qry'])>0)?addslashes($demogrArr['demo_qry']):"";

				$medicArr = $this->get_med_changed_values($rw['medications'], $pt_ex_id);
				$medications=(count($medicArr['medications'])>0)?serialize($medicArr['medications']):"";
				$med_qry=(count($medicArr['med_qry'])>0)?addslashes(serialize($medicArr['med_qry'])):"";
								
                $allerArr = $this->get_alrg_changed_values($rw['allergies'], $pt_ex_id);
				$allergies=(count($allerArr['allergies'])>0)?serialize($allerArr['allergies']):"";
				$alrg_qry=(count($allerArr['alrg_qry'])>0)?addslashes(serialize($allerArr['alrg_qry'])):"";
				
                $problArr = $this->get_prob_changed_values($rw['familyHistoryProblems'], $pt_ex_id);
				$problems=(count($problArr['problems'])>0)?serialize($problArr['problems']):"";
				$prob_qry=(count($problArr['prob_qry'])>0)?addslashes(serialize($problArr['prob_qry'])):"";
				
                $insuArr = $this->get_ins_changed_values($rw['insurances'], $pt_ex_id);
				$insurances=(count($insuArr['insurances'])>0)?serialize($insuArr['insurances']):"";
				$ins_qry=(count($insuArr['ins_qry'])>0)?addslashes(serialize($insuArr['ins_qry'])):"";
				
                $surgeArr = $this->get_surg_changed_values($rw['surgeries'], $pt_ex_id);
				$surgeries=(count($surgeArr['surgeries'])>0)?serialize($surgeArr['surgeries']):"";
				$surg_qry=(count($surgeArr['surg_qry'])>0)?addslashes(serialize($surgeArr['surg_qry'])):"";

                $created = $rw['created'];
                $alreadySent = $rw['alreadySent'];
                $sentOn = $rw['sentOn'];

                $created_on = date('Y-m-d H:i:s');
                $pghd_req_id_ed = $this->is_pghd_req_exists($rw["id"]);
                if (!empty($pghd_req_id_ed)) {
                    // do noting
                } else {
					if($demographics!="" || $insurances!="") {
						$ar_sql[] = "( '" . sqlEscStr($pt_ex_id) . "','" . sqlEscStr($pt_ex_id) . "',
                                '" . sqlEscStr($id) . "', '" . $demographics . "', '','', '', '" . $insurances . "',
                                '','0','" . $created_on . "','" . $operator . "',
								'" . $demo_qry . "','','','',
								'" . $ins_qry . "',''
                                )";
					}
					if($medications!="") {
						$ar_sql[] = "( '" . sqlEscStr($pt_ex_id) . "','" . sqlEscStr($pt_ex_id) . "',
                                '" . sqlEscStr($id) . "', '', '" . $medications . "',
                                '', '', '','','0','" . $created_on . "','" . $operator . "',
								'','" . $med_qry . "','','','',''
                                )";
					}
					if($allergies!="") {
						 $ar_sql[] = "( '" . sqlEscStr($pt_ex_id) . "','" . sqlEscStr($pt_ex_id) . "',
                                '" . sqlEscStr($id) . "', '', '','" . $allergies . "', '', '',
                                '','0','" . $created_on . "','" . $operator . "',
								'','','" . $alrg_qry . "','','',''
                                )";
					}
					if($problems!="") {
						 $ar_sql[] = "( '" . sqlEscStr($pt_ex_id) . "','" . sqlEscStr($pt_ex_id) . "',
                                '" . sqlEscStr($id) . "', '', '','', '" . $problems . "', '',
                                '','0','" . $created_on . "','" . $operator . "',
								'','','','" . $prob_qry . "','',''
                                )";
					}
					if($surgeries!="") {
						 $ar_sql[] = "( '" . sqlEscStr($pt_ex_id) . "','" . sqlEscStr($pt_ex_id) . "',
                                '" . sqlEscStr($id) . "', '', '','', '', '',
                                '" . $surgeries . "','0','" . $created_on . "','" . $operator . "',
								'','','','','','" . $surg_qry . "'
                                )";
					}
					/*
                    $ar_sql[] = "( '" . sqlEscStr($pt_ex_id) . "','" . sqlEscStr($pt_ex_id) . "',
                                '" . sqlEscStr($id) . "', '" . $demographics . "', '" . $medications . "',
                                '" . $allergies . "', '" . $problems . "', '" . $insurances . "',
                                '" . $surgeries . "','0','" . $created_on . "','" . $operator . "',
								'" . $demo_qry . "','" . $med_qry . "','" . $alrg_qry . "','" . $prob_qry . "',
								'" . $ins_qry . "','" . $surg_qry . "'
                                )";
					*/
                    $lnsq = count($ar_sql);
                }
                $ar_uniq_req_id[] = $id;
            }

            if ($lnsq > 0 && ($lnsq == 100 || $len - 1 == $k)) {
                $sql = "INSERT INTO iportal_pghd_reqs( patient_id, pt_external_id, pghd_req_id, demographics,
                                medications, allergies, familyHistoryProblems,
                                insurances, surgeries, approved_declined, created_on, operator,
								demo_qry, med_qry, alrg_qry, prob_qry, ins_qry, surg_qry
                          ) VALUES " . implode(",", $ar_sql);

                sqlQuery($sql);
                $ar_sql = array();
            }

            //acknw Pt Portal
            $this->send_acknow($ar_uniq_req_id);
        }
    }

    public function is_pghd_req_exists($id = '') {
        $ret = 0;
        if (!empty($id)) {
            $sql = "SELECT id FROM iportal_pghd_reqs WHERE pghd_req_id='" . sqlEscStr($id) . "' ";
            $row = sqlQuery($sql);
            if ($row != false && !empty($row["id"])) {
                $ret = $row["id"];
            }
        }
        return $ret;
    }

    function get_app_reqs() {
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

    function show_requests($mthd = "") {
        $tbl = "";
		if($mthd=="popup") {
			//$this->getPghdRequests();
		}
        $res = $this->get_message_text();

        return $res;
    }

    public function is_valid_pt($id) {
        $ret = 0;
        $id = trim($id);
        if (!empty($id)) {
            $sql = "Select id from patient_data where id=$id";
            $rs = imw_query($sql);
            if ($rs && imw_num_rows($rs) == 1) {
                $ret = 1;
            }
        }
        return $ret;
    }

    function get_pghd_reqs_qry() {
        $sql = "SELECT patient_id as pt_id, approved_declined as is_approved, 'iportal_pghd_reqs' as tb_name,
            id, 'Request to PGHD Update' AS title_msg, created_on as reqDateTime,
            DATE_FORMAT(created_on,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime2, pghd_req_id, null as can_reason
            FROM `iportal_pghd_reqs`
            WHERE pghd_req_id!=''
            ";
        return $sql;
    }

    function get_req_inf($rec_id, $ptId) {
        $sqlMsg = "SELECT patient_id as pt_id,
              DATE_FORMAT(created_on,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime 
              FROM iportal_pghd_reqs
              WHERE pghd_req_id!=''
              AND patient_id = '" . $ptId . "' AND id = '" . $rec_id . "' ";
        return $sqlMsg;
    }
	
	function show_pghd_request_data($meth='',$ptId='',$callFrom='') {
		
		$select=" demographics, insurances ";
		$where=" AND (demographics!='' OR insurances!='') ";
		if($meth=='med_hx') {
			$select=" medications, allergies, familyHistoryProblems, surgeries ";
			$where=" AND (medications!='' OR allergies!='' OR familyHistoryProblems!='' OR surgeries!='') ";
		}
		$html = '';
        $sqlMsg = "SELECT id,patient_id as pt_id,pghd_req_id,
                    DATE_FORMAT(created_on,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime,
                    ".$select."
                    FROM iportal_pghd_reqs
                    WHERE pghd_req_id!='' 
					".$where."
					AND approved_declined='0' 
					AND patient_id = '" . $ptId . "' ORDER BY reqDateTime ";

        $rs = imw_query($sqlMsg);
        $counter = 1;
        if ($rs && imw_num_rows($rs) > 0) {
            $html.='';

            while ($row = imw_fetch_assoc($rs)) {
                include_once($GLOBALS['fileroot'] . "/library/classes/work_view/Patient.php");
                $tbl = '';
                $oPt = new Patient($row['pt_id']);
                $pt_nm = $oPt->getName("7");

                $iportal_pghd_reqs_id = $row['id'];

                $req_date = $row['reqDateTime'];

                $tbl .= "<tr >
                  <td>" . $counter . "</td>
                  <td>" . $pt_nm . "</td>
                  <td>" . $req_date . "</td>
                  <td>
                    <button type=\"button\" class=\"btn btn-xs btn-success btn_aprv\" data-callfrom=\"".$callFrom."\" data-pghd=\"1\" data-app_id=\"" . $iportal_pghd_reqs_id . "\">Approve</button>
                    <button type=\"button\" class=\"btn btn-xs btn-danger btn_dcln\" data-callfrom=\"".$callFrom."\" data-pghd=\"1\" data-app_id=\"" . $iportal_pghd_reqs_id . "\">Decline</button>
                  </td>
                </tr>";


                $demo_tbl = "";
                if ($row['demographics'] && $row['demographics'] != '') {
                    $demographics = html_entity_decode($row['demographics']);
                    $demo_tbl .= "<tr >";
                    $demo_tbl.='<td><div class="clearfix"></div><h4>Demographics</H4></td>';
                    $demo_tbl.='<td colspan="3">'.$demographics.'</td>';
                    $demo_tbl .= "</tr>";
                }
                $ins_tbl = "";
                if ($row['insurances'] && $row['insurances'] != '') {
                    $insurancesArr = unserialize($row['insurances']);
					$insurances = implode("<br>",$insurancesArr);
                    $ins_tbl .= "<tr >";
                    $ins_tbl.='<td><div class="clearfix"></div><h4>Insurances</H4></td>';
                    $ins_tbl.='<td colspan="3">' . $insurances . '</td>';
                    $ins_tbl .= "</tr>";
                }
				
				$med_tbl = $alrg_tbl = $prob_tbl = $surg_tbl = "";
				if($meth=='med_hx') {
					if ($row['medications'] && $row['medications'] != '') {
						$medicationsArr = unserialize($row['medications']);
						$medications = implode("<br>",$medicationsArr);
						$med_tbl .= "<tr >";
						$med_tbl.='<td><div class="clearfix"></div><h4>Medications</H4></td>';
						$med_tbl.='<td colspan="3">' . $medications . '</td>';
						$med_tbl .= "</tr>";
					}
					if ($row['allergies'] && $row['allergies'] != '') {
						$allergiesArr = unserialize($row['allergies']);
						$allergies = implode("<br>",$allergiesArr);
						$alrg_tbl .= "<tr >";
						$alrg_tbl.='<td><div class="clearfix"></div><h4>Allergies</H4></td>';
						$alrg_tbl.='<td colspan="3">' . $allergies . '</td>';
						$alrg_tbl .= "</tr>";
					}
					if ($row['familyHistoryProblems'] && $row['familyHistoryProblems'] != '') {
						$familyHistoryProblemsArr = unserialize($row['familyHistoryProblems']);
						$familyHistoryProblems = implode("<br>",$familyHistoryProblemsArr);
						$prob_tbl .= "<tr >";
						$prob_tbl.='<td><div class="clearfix"></div><h4>Problems</H4></td>';
						$prob_tbl.='<td colspan="3">' . $familyHistoryProblems . '</td>';
						$prob_tbl .= "</tr>";
					}
					if ($row['surgeries'] && $row['surgeries'] != '') {
						$surgeriesArr = unserialize($row['surgeries']);
						$surgeries = implode("<br>",$surgeriesArr);
						$surg_tbl .= "<tr >";
						$surg_tbl.='<td><div class="clearfix"></div><h4>Surgeries</H4></td>';
						$surg_tbl.='<td colspan="3">' . $surgeries . '</td>';
						$surg_tbl .= "</tr>";
					}
				}
				
				$final_tbl = $tbl . $demo_tbl . $ins_tbl;
				$final_tbl1 = $demo_tbl . $ins_tbl;
				if($meth=='med_hx') {
					$final_tbl = $tbl . $med_tbl . $alrg_tbl . $prob_tbl . $surg_tbl;
					$final_tbl1 = $med_tbl . $alrg_tbl . $prob_tbl . $surg_tbl;
				}
				
                if ($rec_id == '' && $rec_id == '') {
                    $html.="<table class=\"table table-striped table-bordered\">
							<thead>
							<tr class=\"grythead vlign-top\">
								  <th>Sr.</th>
								  <th>Patient</th>
								  <th>Request Date</th>
								  <th>Action</th>
							</tr>
							</thead>
							<tbody>
							" . $final_tbl . "
							</tbody>
						  </table>";
                } else {
                    $html.="<table class=\"table table-striped table-bordered\">
							<tbody>
							" . $final_tbl1 . "
							</tbody>
						  </table>";
                }


                $counter++;
            }
        }


        return $html;
	}
	

    function get_message_text($rec_id = '', $ptId = '') {
        $html = '';
        $sqlMsg = "SELECT id,patient_id as pt_id,pghd_req_id,
                    DATE_FORMAT(created_on,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime,
                    demographics,medications, allergies, familyHistoryProblems,
                    insurances, surgeries
                    FROM iportal_pghd_reqs
                    WHERE pghd_req_id!='' ";
        if ($rec_id == '' && $rec_id == '') {
            $sqlMsg .= " AND approved_declined='0' ORDER BY reqDateTime ";
        } else {
            if ($rec_id != '') {
                $sqlMsg .= " AND patient_id = '" . $ptId . "' ";
            }
            if ($rec_id != '') {
                $sqlMsg .= " AND id = '" . $rec_id . "' ";
            }
        }

        $rs = imw_query($sqlMsg);
        $counter = 1;
        if ($rs && imw_num_rows($rs) > 0) {
            $html.='';

            while ($row = imw_fetch_assoc($rs)) {
                include_once($GLOBALS['fileroot'] . "/library/classes/work_view/Patient.php");
                $tbl = '';
                $oPt = new Patient($row['pt_id']);
                $pt_nm = $oPt->getName("7");

                $iportal_pghd_reqs_id = $row['id'];

                $req_date = $row['reqDateTime'];

                $tbl .= "<tr >
                  <td>" . $counter . "</td>
                  <td>" . $pt_nm . "</td>
                  <td>" . $req_date . "</td>
                  <td>
                    <button type=\"button\" class=\"btn btn-xs btn-success btn_aprv\" data-pghd=\"1\" data-app_id=\"" . $iportal_pghd_reqs_id . "\">Approve</button>
                    <button type=\"button\" class=\"btn btn-xs btn-danger btn_dcln\" data-pghd=\"1\" data-app_id=\"" . $iportal_pghd_reqs_id . "\">Decline</button>
                  </td>
                </tr>";


                $demo_tbl = "";
                if ($row['demographics'] && $row['demographics'] != '') {
                    $demographics = html_entity_decode($row['demographics']);
                    $demo_tbl .= "<tr >";
                    $demo_tbl.='<td><div class="clearfix"></div><h4>Demographics</H4></td>';
                    $demo_tbl.='<td colspan="3">'.$demographics.'</td>';
                    $demo_tbl .= "</tr>";
                }
                $ins_tbl = "";
                if ($row['insurances'] && $row['insurances'] != '') {
                    $insurancesArr = unserialize($row['insurances']);
					$insurances = implode("<br>",$insurancesArr);
                    $ins_tbl .= "<tr >";
                    $ins_tbl.='<td><div class="clearfix"></div><h4>Insurances</H4></td>';
                    $ins_tbl.='<td colspan="3">' . $insurances . '</td>';
                    $ins_tbl .= "</tr>";
                }
                $med_tbl = "";
                if ($row['medications'] && $row['medications'] != '') {
					$medicationsArr = unserialize($row['medications']);
					$medications = implode("<br>",$medicationsArr);
                    $med_tbl .= "<tr >";
                    $med_tbl.='<td><div class="clearfix"></div><h4>Medications</H4></td>';
                    $med_tbl.='<td colspan="3">' . $medications . '</td>';
                    $med_tbl .= "</tr>";
                }
                $alrg_tbl = "";
                if ($row['allergies'] && $row['allergies'] != '') {
					$allergiesArr = unserialize($row['allergies']);
					$allergies = implode("<br>",$allergiesArr);
                    $alrg_tbl .= "<tr >";
                    $alrg_tbl.='<td><div class="clearfix"></div><h4>Allergies</H4></td>';
                    $alrg_tbl.='<td colspan="3">' . $allergies . '</td>';
                    $alrg_tbl .= "</tr>";
                }
                $prob_tbl = "";
                if ($row['familyHistoryProblems'] && $row['familyHistoryProblems'] != '') {
					$familyHistoryProblemsArr = unserialize($row['familyHistoryProblems']);
					$familyHistoryProblems = implode("<br>",$familyHistoryProblemsArr);
                    $prob_tbl .= "<tr >";
                    $prob_tbl.='<td><div class="clearfix"></div><h4>Problems</H4></td>';
                    $prob_tbl.='<td colspan="3">' . $familyHistoryProblems . '</td>';
                    $prob_tbl .= "</tr>";
                }
                $surg_tbl = "";
                if ($row['surgeries'] && $row['surgeries'] != '') {
					$surgeriesArr = unserialize($row['surgeries']);
					$surgeries = implode("<br>",$surgeriesArr);
                    $surg_tbl .= "<tr >";
                    $surg_tbl.='<td><div class="clearfix"></div><h4>Surgeries</H4></td>';
                    $surg_tbl.='<td colspan="3">' . $surgeries . '</td>';
                    $surg_tbl .= "</tr>";
                }

                if ($rec_id == '' && $rec_id == '') {
                    $html.="<table class=\"table table-striped table-bordered\">
							<thead>
							<tr class=\"grythead vlign-top\">
								  <th>Sr.</th>
								  <th>Patient</th>
								  <th>Request Date</th>
								  <th>Action</th>
							</tr>
							</thead>
							<tbody>
							" . $tbl . $demo_tbl . $ins_tbl . $med_tbl . $alrg_tbl . $prob_tbl . $surg_tbl . "
							</tbody>
						  </table>";
                } else {
                    $html.="<table class=\"table table-striped table-bordered\">
							<tbody>
							" . $demo_tbl . $ins_tbl . $med_tbl . $alrg_tbl . $prob_tbl . $surg_tbl . "
							</tbody>
						  </table>";
                }


                $counter++;
            }
        }


        return $html;
    }

	
	function get_ins_changed_values($insData = array(), $patient_id = '') {

        $returnArr=array();
		$ins_qryArr=array();
        if (!empty($insData) && $insData) {
			foreach($insData as $row) {
				$return = "";
				$ins_qry = "";
				$tableStr= "";
				$final_query="";
				$ins_type=strtolower(trim($row['type']));
				$insuranceName=trim($row['insuranceName']);
				$idNumber=trim($row['idNumber']);
				$title="<h4><b>".ucfirst($ins_type)."</b></h4>";
				
				$provider=$ins_casetype_id=$ins_caseid="";
				$sql1 = "Select id,name from insurance_companies where name LIKE '%".$insuranceName."%' ";
				$res1 = imw_query($sql1);
				if ($res1 && imw_num_rows($res1) == 1) {
					$row1 = imw_fetch_assoc($res1);
					$provider=$row1['id'];
					$company_name=$row1['name'];
				} else {
					$sql2="Insert into insurance_companies set name='".$insuranceName."' ";
					imw_query($sql2);
					$provider=imw_insert_id();
					$sql8="update insurance_companies set in_house_code='".$insuranceName."-".$provider."' ";
					imw_query($sql8);
					$company_name=$insuranceName;
				}
				
				$sql3 = "Select case_id from insurance_case_types where status='0' and normal='1' ";
				$res3 = imw_query($sql3);
				if ($res3 && imw_num_rows($res3) == 1) {
					$row3 = imw_fetch_assoc($res3);
					$ins_casetype_id=$row3['case_id'];
				}
				$sql4 = "Select ins_caseid from insurance_case where patient_id='".$patient_id."' and ins_case_type='".$ins_casetype_id."' ";
				$res4 = imw_query($sql4);
				if ($res4 && imw_num_rows($res4) == 1) {
					$row4 = imw_fetch_assoc($res4);
					$ins_caseid=$row4['ins_caseid'];
				} else {
					$sql5="Insert into insurance_case set ins_case_type=".$ins_casetype_id.", patient_id='".$patient_id."', start_date='".date('Y-m-d H:i:s')."', case_status='Open' ";
					imw_query($sql5);
					$ins_caseid=imw_insert_id();
				}
				
				$relationship = "Other";
				switch(strtolower($row['relationshipToInsured']) ) {
					case 'self':
						$relationship = strtolower($row['relationshipToInsured']);
					break;
					case 'spouse':
						$relationship = $row['relationshipToInsured'];
					break;
					case 'child':
						$relationship = 'Other';
					break;
				}	
				
				$subscriberDOBArr=explode('T',$row['insuredPersonBirthday']);
				$subscriber_DOB	= $subscriberDOBArr[0];
					
				$sql3 = "Select * from insurance_data where pid='".$patient_id."' and type='".$ins_type."' and provider=$provider ";
				$res3 = imw_query($sql3);
				if ($res3 && imw_num_rows($res3) == 1) {
					$row3 = imw_fetch_assoc($res3);
					if ($row3['provider']!=$provider) {
						$old_val = $company_name;
						$new_val = $insuranceName;
						$lbl = 'Insurance Company';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" provider='".$provider."', ";
					}
					if ($row3['policy_number']!=$idNumber) {
						$old_val = $row3['policy_number'];
						$new_val = $idNumber;
						$lbl = 'Policy Number';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" policy_number='".$idNumber."', ";
					}
					if ($row3['group_number']!=$row['groupNumber']) {
						$old_val = $row3['group_number'];
						$new_val = $row['groupNumber'];
						$lbl = 'Group Number';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" group_number='".$row['groupNumber']."', ";
					}
					if ($row3['subscriber_relationship']!=$relationship) {
						$old_val = $row3['subscriber_relationship'];
						$new_val = $relationship;
						$lbl = 'Subscriber Relationship';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_relationship='".$relationship."', ";
					}
					if ($row3['subscriber_fname']!=$row['insuredPersonFirstName']) {
						$old_val = $row3['subscriber_fname'];
						$new_val = $row['insuredPersonFirstName'];
						$lbl = 'Subscriber First Name';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_fname='".$row['insuredPersonFirstName']."', ";
					}
					if ($row3['subscriber_mname']!=$row['insuredPersonMiddleName']) {
						$old_val = $row3['subscriber_mname'];
						$new_val = $row['insuredPersonMiddleName'];
						$lbl = 'Subscriber Middle Name';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_mname='".$row['insuredPersonMiddleName']."', ";
					}
					if ($row3['subscriber_lname']!=$row['insuredPersonLastName']) {
						$old_val = $row3['subscriber_lname'];
						$new_val = $row['insuredPersonLastName'];
						$lbl = 'Subscriber Last Name';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_lname='".$row['insuredPersonLastName']."', ";
					}
					if ($row3['subscriber_DOB']!=$subscriber_DOB) {
						$old_val = $row3['subscriber_DOB'];
						$new_val = date('m-d-Y',strtotime($subscriber_DOB));
						$lbl = 'Subscriber DOB';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_DOB='".$subscriber_DOB."', ";
					}
					if ($row3['subscriber_street']!=$row['address1']) {
						$old_val = $row3['subscriber_street'];
						$new_val = $row['address1'];
						$lbl = 'Street 1';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_street='".$row['address1']."', ";
					}
					if ($row3['subscriber_street_2']!=$row['address2']) {
						$old_val = $row3['subscriber_street_2'];
						$new_val = $row['address2'];
						$lbl = 'Street 2';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_street_2='".$row['address2']."', ";
					}
					if ($row3['subscriber_city']!=$row['city']) {
						$old_val = $row3['subscriber_city'];
						$new_val = $row['city'];
						$lbl = 'City';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_city='".$row['city']."', ";
					}
					if ($row3['subscriber_state']!=$row['state']) {
						$old_val = $row3['subscriber_state'];
						$new_val = $row['state'];
						$lbl = 'State';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_state='".$row['state']."', ";
					}
					if ($row3['subscriber_postal_code']!=$row['zip']) {
						$old_val = $row3['subscriber_postal_code'];
						$new_val = $row['zip'];
						$lbl = 'Zip Code';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_postal_code='".$row['zip']."', ";
					}
					
					$ins_qry=substr($ins_qry,0,-2);
					$final_query = "Update insurance_data set $ins_qry, ins_caseid='".$ins_caseid."' where pid='".$patient_id."' and type='".$ins_type."' and id='".$row3['id']."' ";
					
				} else {
					if ($provider && $insuranceName) {
						$old_val = '';
						$new_val = $insuranceName;
						$lbl = 'Insurance Company';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" provider='".$provider."', ";
					}
					if ($idNumber) {
						$old_val = '';
						$new_val = $idNumber;
						$lbl = 'Policy Number';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" policy_number='".$idNumber."', ";
					}
					if ($row['groupNumber']) {
						$old_val = '';
						$new_val = $row['groupNumber'];
						$lbl = 'Group Number';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" group_number='".$row['groupNumber']."', ";
					}
					if ($relationship) {
						$old_val = '';
						$new_val = $relationship;
						$lbl = 'Subscriber Relationship';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_relationship='".$relationship."', ";
					}
					if ($row['insuredPersonFirstName']) {
						$old_val = '';
						$new_val = $row['insuredPersonFirstName'];
						$lbl = 'Subscriber First Name';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_fname='".$row['insuredPersonFirstName']."', ";
					}
					if ($row['insuredPersonMiddleName']) {
						$old_val = '';
						$new_val = $row['insuredPersonMiddleName'];
						$lbl = 'Subscriber Middle Name';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_mname='".$row['insuredPersonMiddleName']."', ";
					}
					if ($row['insuredPersonLastName']) {
						$old_val = '';
						$new_val = $row['insuredPersonLastName'];
						$lbl = 'Subscriber Last Name';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_lname='".$row['insuredPersonLastName']."', ";
					}
					if ($subscriber_DOB) {
						$old_val = '';
						$new_val = date('m-d-Y', strtotime($subscriber_DOB));
						$lbl = 'Subscriber DOB';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_DOB='".$subscriber_DOB."', ";
					}
					if ($row['address1']) {
						$old_val = '';
						$new_val = $row['address1'];
						$lbl = 'Street 1';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_street='".$row['address1']."', ";
					}
					if ($row['address2']) {
						$old_val = '';
						$new_val = $row['address2'];
						$lbl = 'Street 2';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_street_2='".$row['address2']."', ";
					}
					if ($row['city']) {
						$old_val = '';
						$new_val = $row['city'];
						$lbl = 'City';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_city='".$row['city']."', ";
					}
					if ($row['state']) {
						$old_val = '';
						$new_val = $row['state'];
						$lbl = 'State';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_state='".$row['state']."', ";
					}
					if ($row['zip']) {
						$old_val = '';
						$new_val = $row['zip'];
						$lbl = 'Zip Code';
							
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
							
						$ins_qry.=" subscriber_postal_code='".$row['zip']."', ";
					}
					
					$ins_qry=substr($ins_qry,0,-2);
					$final_query = "insert into insurance_data set $ins_qry, ins_caseid='".$ins_caseid."', pid='".$patient_id."', type='".$ins_type."',referal_required='No',actInsComp=1,newComDate='".date('Y-m-d')."',auth_required='No' ";
				}
				
				
				$return .= "<table class=\"table-responsive table table-striped table-bordered\">
						<thead>
								<tr class=\"grythead vlign-top\">
										  <th>Label</th>
										  <th>Old Value</th>
										  <th>New Value</th>
								</tr>
						</thead>
						<tbody><tr><td colspan=\"3\">$title</td></tr>$tableStr</tbody>
						</table>";
				
				$returnArr[]=$return;
				$ins_qryArr[]=$final_query;
				
			}
		}

		$data=array();
		$data['insurances']=$returnArr;
		$data['ins_qry']=$ins_qryArr;

        return $data;
    }
	
	
    function get_demo_changed_values($demo = array(), $patient_id = '') {

        $return = "";
		$demo_qry = "";
		$final_query="";
        if (!empty($demo) && $demo) {
            $labelArr = $this->get_label_array();
            $keysArr = $this->get_keys_array();
            $sql = "Select * from patient_data where id='" . $patient_id . "' ";
            $res = imw_query($sql);
            if ($res && imw_num_rows($res) == 1) {
				include_once($GLOBALS['fileroot'] . "/library/classes/demographics.class.php");
				$objDemo = new Demographics($patient_id);
				
                $row = imw_fetch_assoc($res);
				$tableStr='';
				
				$race_arr=$objDemo->race_modal(1);
				$ethnicity_arr=$objDemo->ethnicity_modal(1);
				
				$ethnicity = $race = $marital_status = $gender = '';
				if($demo['raceExternalId'] && $demo['raceExternalId']!='') {
					$races_sql = "select race_name,erp_race_id from race where is_deleted=0 and race_id='".$demo['raceExternalId']."' ";
					$races_res=imw_query($races_sql);
					if($races_res && imw_num_rows($races_res)>0){
						$racer = imw_fetch_assoc($races_res);
						$race=$racer['race_name'];
						
						if(!in_array($race,$race_arr)) {
							$sql1="Update race set common_use = 1 where race_id='".$demo['raceExternalId']."' ";
							imw_query($sql1);
						}
					}
				}

				if($demo['ethnicityExternalId'] && $demo['ethnicityExternalId']!='') {
					$ethnicity_sql = "select ethnicity_name,erp_ethn_id from ethnicity where is_deleted=0 and ethnicity_id='".$demo['ethnicityExternalId']."' ";
					$ethnicity_res=imw_query($ethnicity_sql);
					if($ethnicity_res && imw_num_rows($ethnicity_res)>0){
						$ethnicityr = imw_fetch_assoc($ethnicity_res);
						$ethnicity=$ethnicityr['ethnicity_name'];
						
						if(!in_array($ethnicity,$ethnicity_arr)) {
							$sql2="Update ethnicity set common_use = 1 where ethnicity_id='".$demo['ethnicityExternalId']."' ";
							imw_query($sql2);
						}
					}
				}

				if($demo['maritalStatusExternalId'] && $demo['maritalStatusExternalId']!='') {
					$marital_sql = "select mstatus_name,erp_marital_id from marital_status where is_deleted=0 and mstatus_id='".$demo['maritalStatusExternalId']."' ";
					$marital_res=imw_query($marital_sql);
					if($marital_res && imw_num_rows($marital_res)>0){
						$marital = imw_fetch_assoc($marital_res);
						$marital_status=trim($marital['mstatus_name']);
					}
				}

				if($demo['sexExternalId'] && $demo['sexExternalId']!='') {
					$qry = "Select gender_name,erp_gender_id from gender_code Where is_deleted = 0 and gender_id='".$demo['sexExternalId']."' ";
					$sql=imw_query($qry);
					if($sql && imw_num_rows($sql)>0){
						$genderr = imw_fetch_assoc($sql);
						$gender=$genderr['gender_name'];
					}
				}
				
				$dob='';
				if($demo['birthday'] && $demo['birthday']!='') {
					$arr=explode('T',$demo['birthday']);
					$dob=trim($arr[0]);
				}
				
				$emergencyContact=$emergencyRelationship=$phone_contact='';
				if($demo['emergencyContact1Name'] && $demo['emergencyContact1Name']!='') {
					$emergencyContact=$demo['emergencyContact1Name'];
				} else if($demo['emergencyContact2Name'] && $demo['emergencyContact2Name']!='') {
					$emergencyContact=$demo['emergencyContact2Name'];
				}
				
				if($demo['emergencyContact1Relationship'] && $demo['emergencyContact1Relationship']!='') {
					$emergencyRelationship=$demo['emergencyContact1Relationship'];
				} else if($demo['emergencyContact2Relationship'] && $demo['emergencyContact2Relationship']!='') {
					$emergencyRelationship=$demo['emergencyContact2Relationship'];
				}
				
				if($demo['emergencyContact1CellPhoneNumber'] && $demo['emergencyContact1CellPhoneNumber']!='') {
					$phone_contact=$demo['emergencyContact1CellPhoneNumber'];
				} else if($demo['emergencyContact1HomePhoneNumber'] && $demo['emergencyContact1HomePhoneNumber']!='') {
					$phone_contact=$demo['emergencyContact1HomePhoneNumber'];
				} else if($demo['emergencyContact1WorkPhoneNumber'] && $demo['emergencyContact1WorkPhoneNumber']!='') {
					$phone_contact=$demo['emergencyContact1WorkPhoneNumber'];
				} else if($demo['emergencyContact2CellPhoneNumber'] && $demo['emergencyContact2CellPhoneNumber']!='') {
					$phone_contact=$demo['emergencyContact2CellPhoneNumber'];
				} else if($demo['emergencyContact2HomePhoneNumber'] && $demo['emergencyContact2HomePhoneNumber']!='') {
					$phone_contact=$demo['emergencyContact2HomePhoneNumber'];
				} else if($demo['emergencyContact2WorkPhoneNumber'] && $demo['emergencyContact2WorkPhoneNumber']!='') {
					$phone_contact=$demo['emergencyContact2WorkPhoneNumber'];
				}
		
				$preferr_contact='';
				$phoneNumbers=$demo['phoneNumbers'];
				$phone_cell='';
				$phone_biz='';
				$phone_home='';
				if(is_array($phoneNumbers) && count($phoneNumbers)>0) {
					$defaultPhoneNumber=false;
					foreach($phoneNumbers as $phone) {
						$phone['number']=str_ireplace('+1','',$phone['number']);
						if( $phone['alias'] == 'Home' ){
							$phone_home = core_phone_unformat($phone['number']);
							if($phone['defaultPhoneNumber'])$preferr_contact=0;
						}
						if( $phone['alias'] == 'Work' ){
							$phone_biz = core_phone_unformat($phone['number']);
							if($phone['defaultPhoneNumber'])$preferr_contact=1;
						}
						if( $phone['alias'] == 'Mobile' ){
							$phone_cell = core_phone_unformat($phone['number']);
							if($phone['defaultPhoneNumber'])$preferr_contact=2;
						}
					}
				}

				foreach($keysArr as $key => $val) {
					$old_val=$new_val=$lbl='';
					if ($row[$key] != $demo[$val] && $demo[$val]!='') {
						$old_val = $row[$key];
						$new_val = $demo[$val];
						$lbl = $labelArr[$key];
					}
			
					if($new_val!='' && $lbl!='') {
						if($key!='lang_code'){
							$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						}
						
						$demo_qry.=" $key='".$new_val."', ";
					}
				}

				if ($row['race'] != $race && $race!='') {
					$old_val = $row['race'];
					$new_val = $race;
					$lbl = 'Race';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" race='".$new_val."', ";
				}
				if ($row['ethnicity'] != $ethnicity && $ethnicity!='') {
					$old_val = $row['ethnicity'];
					$new_val = $ethnicity;
					$lbl = 'Ethnicity';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" ethnicity='".$new_val."', ";
				}
				if ($row['status'] != $marital_status && $marital_status!='') {
					$old_val = $row['status'];
					$new_val = $marital_status;
					$lbl = 'Marital Status';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" status='".$new_val."', ";
				}
				if ($row['sex'] != $gender && $gender!='') {
					$old_val = $row['sex'];
					$new_val = $gender;
					$lbl = 'Gender';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" sex='".$new_val."', ";
				}
				if ($row['DOB'] != $dob && ($dob!='' || $dob!='0000-00-00') &&  $row['DOB']!='0000-00-00') {
					$old_val = $row['DOB'];
					$new_val = date('m-d-Y',strtotime($dob));
					$lbl = 'DOB';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" DOB='".$new_val."', ";
				}
				
				if($phone_home!=core_phone_unformat($row['phone_home']) && $phone_home!='') {
					$old_val = $row['phone_home'];
					$new_val = $phone_home;
					$lbl = 'Home Phone';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" phone_home='".$new_val."', ";
				}
				if($phone_biz!=core_phone_unformat($row['phone_biz']) && $phone_biz!='') {
					$old_val = $row['phone_biz'];
					$new_val = $phone_biz;
					$lbl = 'Work Phone';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" phone_biz='".$new_val."', ";
				}
				if($phone_cell!=core_phone_unformat($row['phone_cell']) && $phone_cell!='') {
					$old_val = $row['phone_cell'];
					$new_val = $phone_cell;
					$lbl = 'Mobile';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" phone_cell='".$new_val."', ";
				}
				if($preferr_contact==0 || $preferr_contact==1 || $preferr_contact==2) {
					$demo_qry.=" preferr_contact='".$preferr_contact."', ";
				}
				
				if ($row['contact_relationship'] != $emergencyContact && $emergencyContact!='' ) {
					$old_val = $row['contact_relationship'];
					$new_val = $emergencyContact;
					$lbl = 'Emergency Name';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" contact_relationship='".$new_val."', ";
				}
				if ($row['emergencyRelationship'] != $emergencyRelationship && $emergencyRelationship!='' ) {
					$old_val = $row['emergencyRelationship'];
					$new_val = $emergencyRelationship;
					$lbl = 'Emergency Relationship';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" emergencyRelationship='".$new_val."', ";
				}
				if ($row['phone_contact'] != $phone_contact && $phone_contact!='' ) {
					$old_val = $row['phone_contact'];
					$new_val = $phone_contact;
					$lbl = 'Emergency Tel';
					
					$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					
					$demo_qry.=" phone_contact='".$new_val."', ";
				}
				
				$demo_qry=substr($demo_qry,0,-2);
				$final_query = "update patient_data set $demo_qry where id='".$patient_id."' ";
				
                $return .= "<table class=\"table-responsive table table-striped table-bordered\">
                            <thead>
                                    <tr class=\"grythead vlign-top\">
                                              <th>Label</th>
                                              <th>Old Value</th>
                                              <th>New Value</th>
                                    </tr>
                            </thead>
                            <tbody>$tableStr</tbody>
                            </table>";
            }
        }
		
		$data=array();
		$data['demograhics']=$return;
		$data['demo_qry']=$final_query;

        return $data;
    }
	
	function get_med_changed_values($med=array(),$patient_id='') {
		
		$qry_arr = array();
		$return_arr = array();
		
		if(!empty($med) && $med) {
			
			foreach ($med as $value) {
				$return = "";
				$med_qry = "";
				$tableStr= "";
				$final_query="";
				
				$medication_id = $value['patientMedicationRecordExternalId'];
				
				$sql="SELECT title, destination, begdate, endDate, sites, comments   FROM lists WHERE pid='".$patient_id."' AND type IN (1,4) AND id ='".$medication_id."' AND title='".$value['medicationName']."' ";
				
				$res=imw_query($sql);
				if($res && imw_num_rows($res)==1) {
					
					$row=imw_fetch_assoc($res);
					
					$startDate = $endDate = '';
					$startDate = $this->get_correct_date($value['startDate']);
					$endDate = $this->get_correct_date($value['endDate']);
					
					if($row['title']!=$value['medicationName']) {
						$old_val=$row['title'];
						$new_val=$value['medicationName'];
						$lbl='Medication Name';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" title='".$new_val."', ";
					}
					
					if($row['destination']!=$value['medicationDosageName']) {
						$old_val=$row['destination'];
						$new_val=$value['medicationDosageName'];
						$lbl='Dosage';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" destination='".$new_val."', ";
					}
					
					if($row['begdate']!=$startDate) {
						$old_val=$row['begdate'];
						$new_val=$startDate;
						$lbl='Begin Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" begdate='".$new_val."', ";
					}
					
					if($row['endDate']!=$endDate) {
						$old_val=$row['endDate'];
						$new_val=$endDate;
						$lbl='End Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" endDate='".$new_val."', ";
					}
					
					if($row['sites']!=trim($value['eyeMedication'])) {
						$old_val=$row['sites'];
						$new_val=$value['eyeMedication'];
						$lbl='Site';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" sites='".$new_val."', ";
					}
					$med_type=(trim($value['eyeMedication']) == true)? "4":"1";
					
					if($row['comments']!=trim($value['notes'])) {
						$old_val=$row['comments'];
						$new_val=$value['notes'];
						$lbl='Comments';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" med_comments='".$new_val."', ";
                                        }
				
					$med_qry=substr($med_qry,0,-2);
					$final_query = "UPDATE lists SET $med_qry,allergy_status='Active',type='".$med_type."',date='".date('Y-m-d H:i:s')."' WHERE pid='".$patient_id."' AND type='".$med_type."' AND id='".$medication_id."'";
				}
				else
				{  
					$tableStr= $startDate = $endDate = '';
					$startDate = $this->get_correct_date($value['startDate']);
					$endDate = $this->get_correct_date($value['endDate']);
					
					if($value['medicationName']) {
						$old_val="";
						$new_val=$value['medicationName'];
						$lbl='Medication Name';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" title='".$new_val."', ";
					}
					
					if($value['medicationDosageName']) {
						$old_val="";
						$new_val=$value['medicationDosageName'];
						$lbl='Dosage';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" destination='".$new_val."', ";
					}
					
					if($startDate && $startDate!= "0000-00-00 00:00:00") {
						$old_val="";
						$new_val=$startDate	;
						$lbl='Begin Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" begdate='".$new_val."', ";
					}
					
					if($endDate && $endDate!= "0000-00-00 00:00:00") {
						$old_val="";
						$new_val=$endDate;
						$lbl='End Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" endDate='".$new_val."', ";
					}
					
					if($value['eyeMedication']) {
						$old_val="";
						$new_val=$value['eyeMedication'];
						$lbl='Site';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" sites='".$new_val."', ";
					}
					$med_type=(trim($value['eyeMedication']) == true)?"4":"1";
					
					if(trim($value['notes'])) {
						$old_val="";
						$new_val=$value['notes'];
						$lbl='Comments';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$med_qry.=" med_comments='".$new_val."', ";
                                        }
			
					$med_qry=substr($med_qry,0,-2);
					$final_query = "INSERT INTO lists SET $med_qry, pid='".$patient_id."',type='".$med_type."',allergy_status='Active',date='".date('Y-m-d H:i:s')."' ";
				}
			
					$return .= "<table class=\"table-responsive table table-striped table-bordered\">
                            <thead>
                                    <tr class=\"grythead vlign-top\">
                                              <th>Label</th>
                                              <th>Old Value</th>
                                              <th>New Value</th>
                                    </tr>
                            </thead>
                            <tbody>$tableStr</tbody>
                            </table>";	

				$qry_arr[] = $final_query;
				$return_arr[] = $return;
			}	
		}
		
		$data=array();
		$data['medications']=$return_arr;
		$data['med_qry']=$qry_arr;

        return $data;
	}
	
	function get_alrg_changed_values($alrg=array(),$patient_id='') {
		
		$qry_arr = array();
		$return_arr = array();
		
		if(!empty($alrg) && $alrg) {
			
			foreach ($alrg as $value) {
				$final_query="";
				$return = "";
				$alrg_qry = "";
				$tableStr = "";
				$alrg_id = $value['patientAllergyExternalId'];  
			 
			  $sql="SELECT 
						 title, severity, begdate, comments 
					FROM 
						lists 
					WHERE 
						pid='".$patient_id."' 
					AND 
						type = 7 
					AND 
						title ='".$value['allergyName']."'
					AND 
						id ='".$alrg_id."'";
				
				$res=imw_query($sql);
				if($res && imw_num_rows($res)==1) {
					
					$row=imw_fetch_assoc($res);
					
					 $startDate = '';
					$startDate = $this->get_correct_date($value['startDate']);
					
					if($row['title']!=$value['allergyName']) {
						$old_val=$row['title'];
						$new_val=$value['allergyName'];
						$lbl='Allergy Name';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_qry.=" title='".$new_val."', ";
					}
					
					if($row['severity']!=$value['allergySeverityName']) {
						$old_val=$row['severity'];
						$new_val=$value['allergySeverityName'];
						$lbl='Severity';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_qry.=" severity='".$new_val."', ";
					}
					
					if($row['begdate']!=$startDate) {
						$old_val=$row['begdate'];
						$new_val=$startDate;
						$lbl='Begin Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_qry.=" begdate='".$new_val."', ";
					}
					$alrg_comments="";
					if($row['comments']!=trim($value['notes'])) {
						$old_val=$row['comments'];
						$new_val=$value['notes'];
						$lbl='Comments';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_comments=" Comments=> $new_val ";
					}
					$alrg_reaction="";
					if($row['comments']!=trim($value['allergyReactionName'])) {
						$old_val=$row['comments'];
						$new_val=$value['allergyReactionName'];
						$lbl='Reactions';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_reaction=" Reactions=> $new_val ";
					}
					if($alrg_reaction || $alrg_comments){
						$comments = $alrg_reaction.'/'.$alrg_comments;
						$alrg_qry.=" comments='".$comments."', ";
					}
					$alrg_qry=substr($alrg_qry,0,-2);
					$final_query = "UPDATE lists SET $alrg_qry WHERE pid='".$patient_id."' AND type = 7 AND id= '".$alrg_id."'";
				}
				else
				{  
					$startDate = $endDate = '';
					$startDate = $this->get_correct_date($value['startDate']);
					
					
					if($value['allergyName']) {
						$old_val="";
						$new_val=$value['allergyName'];
						$lbl='Allergy Name';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_qry.=" title='".$new_val."', ";
					}
					
					if($value['allergySeverityName']) {
						$old_val="";
						$new_val=$value['allergySeverityName'];
						$lbl='Severity';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_qry.=" severity='".$new_val."', ";
					}
					
					if($startDate && $startDate!= "0000-00-00 00:00:00") {
						$old_val="";
						$new_val=$startDate	;
						$lbl='Begin Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_qry.=" begdate='".$new_val."', ";
					}
					
					$alrg_comments="";
					if(trim($value['notes'])) {
						$old_val="";
						$new_val=$value['notes'];
						$lbl='Comments';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_comments=" Comments=> $new_val ";
					}
					$alrg_reaction="";
					if(trim($value['allergyReactionName'])) {
						$old_val="";
						$new_val=$value['allergyReactionName'];
						$lbl='Reactions';
					
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$alrg_reaction=" Reactions=> $new_val ";
                                        }
					if($alrg_reaction || $alrg_comments){
						$comments = $alrg_reaction.'/'.$alrg_comments;
						$alrg_qry.=" comments='".$comments."', ";
					}
                                        
					$alrg_qry=substr($alrg_qry,0,-2);
					$final_query = "INSERT INTO lists SET $alrg_qry, pid='".$patient_id."',ag_occular_drug='fdbATAllergenGroup',type = 7, allergy_status='Active',date='".date('Y-m-d H:i:s')."' ";
				}
			$return .= "<table class=\"table-responsive table table-striped table-bordered\">
                            <thead>
                                    <tr class=\"grythead vlign-top\">
                                              <th>Label</th>
                                              <th>Old Value</th>
                                              <th>New Value</th>
                                    </tr>
                            </thead>
                            <tbody>$tableStr</tbody>
                            </table>";	
							
				$qry_arr[] = $final_query;
				$return_arr[] = $return;			
							
			}	
		}
		
		$data=array();
		$data['allergies']=$return_arr;
		$data['alrg_qry']=$qry_arr;

        return $data;
	}
	
	function get_surg_changed_values($surg=array(),$patient_id='') {
		
		$qry_arr = array();
		$return_arr = array();
		
		if(!empty($surg) && $surg) {
			
			foreach ($surg as $value) {
                        $final_query="";
			$return = "";
			$surg_qry = "";
			$tableStr = "";
			$surg_id = $value['patientSurgeryExternalId'];	
			$allergy_type=(trim($value['eyeSurgery']) == 1)?"6":"5";

			$surgerySite="";
			switch(strtolower($value['surgeryLocationType'])) {
				case 'both eye':
					$surgerySite=3;
				break;
				case 'right eye':
					$surgerySite=2;
				break;
				case 'left eye':
					$surgerySite=1;
				break;
			}
			 $sql="SELECT 
						 title, begdate, comments, sites    
					FROM 
						lists 
					WHERE 
						pid='".$patient_id."' 
					AND 
						type IN (5,6,9) 
					AND 
						title ='".$value['surgeryName']."'
					AND 
						id ='".$surg_id."' ";
				
				$res=imw_query($sql);
				if($res && imw_num_rows($res)==1) {
					
					$row=imw_fetch_assoc($res);
					
					$startDate = '';
					$startDate = $this->get_correct_date($value['surgeryDate']);
					
					if($row['title']!=$value['surgeryName']) {
						$old_val=$row['title'];
						$new_val=$value['surgeryName'];
						$lbl='Surgery Name';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" title='".$new_val."', ";
					}
					
					if($row['begdate']!=$startDate) {
						$old_val=$row['begdate'];
						$new_val=$startDate;
						$lbl='Surgery Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" begdate='".$new_val."', ";
					}
					
					if($row['comments']!=trim($value['comments'])) {
						$old_val=$row['comments'];
						$new_val=$value['comments'];
						$lbl='Comments';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" comments='".$new_val."', ";
					}
					
					if($row['sites']!=$surgerySite) {
						$old_val=$row['sites'];
						$new_val=$surgerySite;
						$lbl='Site';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" sites='".$new_val."', ";
					}
					$surg_qry = substr($surg_qry, 0, -2);
					$final_query = "UPDATE lists SET $surg_qry WHERE pid='".$patient_id."' AND id='".$surg_id."' and type='".$allergy_type."' ";
				}
				else
				{ 
					$startDate = '';
					$startDate = $this->get_correct_date($value['surgeryDate']);
					
					
					if($value['surgeryName']) {
						$old_val="";
						$new_val=$value['surgeryName'];
						$lbl='Surgery Name';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" title='".$new_val."', ";
					}
					
					if($startDate && $startDate!= "0000-00-00 00:00:00") {
						$old_val="";
						$new_val=$startDate	;
						$lbl='Surgery Date';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" begdate='".$new_val."', ";
					}
					
					if($value['comments']) {
						$old_val="";
						$new_val=$value['comments'];
						$lbl='Comments';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" comments='".$new_val."', ";
					}
					
					if($surgerySite) {
						$old_val="";
						$new_val=$surgerySite;
						$lbl='Site';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
						$surg_qry.=" sites='".$new_val."', ";
					}
					
					$surg_qry = substr($surg_qry, 0, -2);
					$final_query = "INSERT INTO lists SET $surg_qry,type='".$allergy_type."',pid='".$patient_id."',allergy_status='Active',proc_type='surgery',procedure_status='completed',date='".date('Y-m-d H:i:s')."' ";
				}
			
				$return .= "<table class=\"table-responsive table table-striped table-bordered\">
                            <thead>
                                    <tr class=\"grythead vlign-top\">
                                              <th>Label</th>
                                              <th>Old Value</th>
                                              <th>New Value</th>
                                    </tr>
                            </thead>
                            <tbody>$tableStr</tbody>
                            </table>";	
							
				$qry_arr[] = $final_query;
				$return_arr[] = $return;			
			}	
		}
				
		$data=array();
		$data['surgeries']=$return_arr;
		$data['surg_qry']=$qry_arr;

        return $data;
	}
	
	function get_prob_changed_values($prob=array(),$patient_id='') {
		
		$qry_arr = array();
		$return_arr = array();
		
		if(!empty($prob) && $prob) {
			
			$strDivider = '~|~';
			$delimiter = '~|~';

			//GETTING DB VALUE TO MERGE/SPLIC pt & relatives VALUES.
			$getGHquery = "select * from general_medicine where patient_id='".(int) $patient_id."'";
			$getGHResult = imw_query($getGHquery);
			$getGHrs = imw_fetch_assoc($getGHResult);
			
			//insert / update general health
			$check_data1 = "select general_id from general_medicine where patient_id = '".$patient_id."'";
			$checkSql1 = imw_query($check_data1) or die (imw_error());
			$checkrows1 = imw_num_rows($checkSql1);
			
			$relFinalArr=array();
			$relArr=get_relationship_array('general_health');
			foreach($relArr as $key=>$val) {
				if($key!=''){
					$relFinalArr[]=strtolower($key);
				}
			}

			if($checkrows1>0){			
				// update
				$row = imw_fetch_array($checkSql1);		
				$newGeneralMedicineId =  $row['general_id'];		//for audit
				$generalsaveqry = "update general_medicine set ";
				$generalsaveqry .= " patient_id = '".$patient_id."' ";
			}else{			
				// insert new
				$generalsaveqry = "insert into general_medicine set ";					
				$generalsaveqry .= " patient_id = '".$patient_id."' ";
			}
			
			$strRelDescHighBp=$strRelDescArthritisProb=$strRelDescStrokeProb =$strDesc_r =$strRelDescUlcersProb =$strRelDescCancerProb =$strRelDescHeartProb =$strRelDescLungProb =$strRelDescThyroidProb =$strRelDescLDL =$strGhRelDescOthers = $OtherChkVal = '';
			$relTxtHighBloodPresher =$relTxtArthrities = $relTxtStroke = $desc_u = $relTxtUlcers = $relTxtCancer = $relTxtHeartProblem = $relTxtLungProblem = $relTxtThyroidProblems = $reltxtLDL = $rel_any_conditions_others1 = '';
			
			$any_conditions_relative1=array();
			foreach ($prob as $value) {
				$return = "";
				$tableStr = '';
				$prob_id = $value['patientFamilyHistoryProblemExternalId'];
				
				$prob_name=$value['familyHistoryProblemName'];
				$prob_desc=($value['comments'])?$value['comments']:'';
				$frelationship=ucfirst($value['familyHistoryRelationshipName']);
				if(!in_array(strtolower($frelationship),$relFinalArr)) {
					$frelationship='Other, '.$frelationship;
				}
				if( $prob_name && $prob_name!='' ) {
					$relProbName=strtolower($prob_name);
					if( stripos('high blood pressure', $relProbName)!== false || stripos('high bp', $relProbName)!== false ) {
						$any_conditions_relative1[]=1;
						//$strRelDescHighBp = $relProbName;
						$relTxtHighBloodPresher = get_set_pat_rel_values_save($getGHrs["desc_high_bp"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescHighBp='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_high_bp = '".imw_real_escape_string(htmlentities($relTxtHighBloodPresher))."' ";
					}
					if( stripos('arthiritis', $relProbName)!== false ) {
						$any_conditions_relative1[]=7;
						//$strRelDescArthritisProb = $relProbName;
						$relTxtArthrities = get_set_pat_rel_values_save($getGHrs["desc_arthrities"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescArthritisProb='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_arthrities = '".imw_real_escape_string(htmlentities($relTxtArthrities))."' ";
					}
					if( stripos('stroke', $relProbName)!== false ) {
						$any_conditions_relative1[]=5;
						//$strRelDescStrokeProb = $relProbName;
						$relTxtStroke = get_set_pat_rel_values_save($getGHrs["desc_stroke"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescStrokeProb='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_stroke = '".imw_real_escape_string(htmlentities($relTxtStroke))."' ";
					}
					if( stripos('diabetic', $relProbName)!== false ) {
						$any_conditions_relative1[]=3;
						//$strDesc_r = $relProbName;
						$desc_u = get_set_pat_rel_values_save($getGHrs["desc_u"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,desc_r='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_u='".imw_real_escape_string(htmlentities($desc_u))."' ";
					}
					if( stripos('ulcers', $relProbName)!== false || stripos('ulcer', $relProbName)!== false ) {
						$any_conditions_relative1[]=8;
						//$strRelDescUlcersProb = $relProbName;
						$relTxtUlcers = get_set_pat_rel_values_save($getGHrs["desc_ulcers"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescUlcersProb='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_ulcers = '".imw_real_escape_string(htmlentities($relTxtUlcers))."' ";
					}
					if( stripos('cancer', $relProbName)!== false ) {
						$any_conditions_relative1[]=14;
						//$strRelDescCancerProb = $relProbName;
						$relTxtCancer = get_set_pat_rel_values_save($getGHrs["desc_cancer"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescCancerProb='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_cancer = '".imw_real_escape_string(htmlentities($relTxtCancer))."' ";
					}
					if( stripos('heart problem', $relProbName)!== false || stripos('heart', $relProbName)!== false ) {
						$any_conditions_relative1[]=2;
						//$strRelDescHeartProb = $relProbName;
						$relTxtHeartProblem = get_set_pat_rel_values_save($getGHrs["desc_heart_problem"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescHeartProb='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_heart_problem = '".imw_real_escape_string(htmlentities($relTxtHeartProblem))."' ";
					}
					if( stripos('lung problem', $relProbName)!== false || stripos('lung', $relProbName)!== false || stripos('lungs', $relProbName)!== false ) {
						$any_conditions_relative1[]=4;
						//$strRelDescLungProb = $relProbName;
						$relTxtLungProblem = get_set_pat_rel_values_save($getGHrs["desc_lung_problem"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescLungProb='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_lung_problem = '".imw_real_escape_string(htmlentities($relTxtLungProblem))."' ";
					}
					if( stripos('thyroid problem', $relProbName)!== false || stripos('thyroid', $relProbName)!== false ) {
						$any_conditions_relative1[]=6;
						//$strRelDescThyroidProb = $relProbName;
						$relTxtThyroidProblems = get_set_pat_rel_values_save($getGHrs["desc_thyroid_problems"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescThyroidProb='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_thyroid_problems = '".imw_real_escape_string(htmlentities($relTxtThyroidProblems))."'";
					}
					if( stripos('ldl', $relProbName)!== false ) {
						$any_conditions_relative1[]=13;
						//$strRelDescLDL = $relProbName;
						$reltxtLDL = get_set_pat_rel_values_save($getGHrs["desc_LDL"],$prob_desc,"rel",$delimiter);
						
						$generalsaveqry.= " ,relDescLDL = '".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,desc_LDL= '".imw_real_escape_string(htmlentities($reltxtLDL))."' ";
					}
					if( count($any_conditions_relative1)==0 ) {
						$OtherChkVal=2;
						//$strGhRelDescOthers = $relProbName;
						
						$rel_any_conditions_others1 = get_set_pat_rel_values_save($getGHrs["any_conditions_others"],$prob_desc,"rel",$delimiter);
						$rel_any_conditions_others1=$rel_any_conditions_others1." ## Problem Name = ".ucfirst($prob_name);
						
						$generalsaveqry.= " ,ghRelDescOthers='".imw_real_escape_string(htmlentities($frelationship))."' ";
						$generalsaveqry.= " ,any_conditions_others='".imw_real_escape_string(htmlentities($rel_any_conditions_others1))."' ";
					}
					
				}
							
				if( isset($value['operation']) && $value['operation']=='Added' ) {
					if($value['familyHistoryProblemName']) {
						$old_val="";
						$new_val=ucfirst($prob_name);
						$lbl='Problem Name';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					}
					if($value['familyHistoryRelationshipName']) {
						$old_val="";
						$new_val=ucfirst($value['familyHistoryRelationshipName']);
						$lbl='Relationship';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					}
					if($value['comments']) {
						$old_val="";
						$new_val=$value['comments'];
						$lbl='Comments';
						
						$tableStr.="<tr><td>$lbl</td><td>$old_val</td><td>$new_val</td></tr>";
					}
					
				}
				
				
				$return .= "<table class=\"table-responsive table table-striped table-bordered\">
				<thead>
						<tr class=\"grythead vlign-top\">
								  <th>Label</th>
								  <th>Old Value</th>
								  <th>New Value</th>
						</tr>
				</thead>
				<tbody>$tableStr</tbody>
				</table>";	
				
				$return_arr[] = $return;				
			}	
			
			$any_conditions_relative1_n_arr=array();
			$any_conditions_relative1_n=explode(",",$getGHrs["any_conditions_relative1_n"]);
			foreach($any_conditions_relative1_n as $n_arr) {
				if(!in_array($n_arr,$any_conditions_relative1) && $n_arr) {
					$any_conditions_relative1_n_arr[]=$n_arr;
				}
			}
			
			$any_conditions_relative_arr=array();
			$any_conditions_relative_arr=explode(",",$getGHrs["any_conditions_relative"]);
			if(count($any_conditions_relative_arr)>0){
				$any_conditions_relative1=array_merge($any_conditions_relative1,$any_conditions_relative_arr);
				$any_conditions_relative1=array_unique($any_conditions_relative1);
			}
			if(count($any_conditions_relative1)>0){
				$any_conditions_relative_arr1 = ",";
				$any_conditions_relative_arr1 .= implode(",",$any_conditions_relative1);
				$any_conditions_relative_arr1 .= ",";
			}
			if(count($any_conditions_relative1_n_arr)>0){
				$anyConditionsRelativeN = ",";
				$anyConditionsRelativeN .= implode(",",$any_conditions_relative1_n_arr);
				$anyConditionsRelativeN .= ",";
			}
			if(!empty($OtherChkVal)){
				$OtherChkVal = ','.$OtherChkVal.',';
				$generalsaveqry.= " ,any_conditions_others_both='".imw_real_escape_string(htmlentities($OtherChkVal))."' ";
			}
			
			$generalsaveqry.= " ,any_conditions_relative='".imw_real_escape_string(htmlentities($any_conditions_relative_arr1))."' ";
			$generalsaveqry.= " ,cbk_master_fam_con='".imw_real_escape_string(htmlentities("yes"))."' ";
			$generalsaveqry.= " ,any_conditions_relative1_n='".imw_real_escape_string(htmlentities($anyConditionsRelativeN))."' ";
			if($checkrows1>0){
				$generalsaveqry .= " where patient_id='".$patient_id."' ";
			}
		}
		
		$data=array();
		$data['problems']=$return_arr;
		$data['prob_qry'][]=$generalsaveqry;

        return $data;
	}
	
    function get_label_array() {
        $labelArr = array();
        $labelArr['fname'] = 'First Name';
		$labelArr['mname'] = 'Middle Name';
		$labelArr['lname'] = 'Last Name';
		$labelArr['street'] = 'Address1';
		$labelArr['street2'] = 'Address2';
		$labelArr['city'] = 'City';
		$labelArr['state'] = 'State';
		$labelArr['postal_code'] = 'Zip';
		$labelArr['email'] = 'Email';
		$labelArr['language'] = 'Language';
		$labelArr['lang_code'] = 'Language Code';
        return $labelArr;
    }

    function get_keys_array() {
        $keysArr = array();
        $keysArr['fname'] = 'firstName';
		$keysArr['mname'] = 'middleName';
		$keysArr['lname'] = 'lastName';
		$keysArr['street'] = 'address1';
		$keysArr['street2'] = 'address2';
		$keysArr['city'] = 'city';
		$keysArr['state'] = 'state';
		$keysArr['postal_code'] = 'zip';
		$keysArr['email'] = 'emailAddress';		
		$keysArr['language'] = 'languageDisplayName';
		$keysArr['lang_code'] = 'languageName';
        return $keysArr;
    }
	
    function get_correct_date($defaultDate="") {
        $showDateTime = "";
		$getDateTime = explode('.',trim($defaultDate));
		$dateTime =  explode('T',$getDateTime[0]);
		
		$date = isset($dateTime[0]) ? get_date_format($dateTime[0]) : "";
		$time = (isset($dateTime[1]) && $dateTime[1]!=='00:00:00') ? core_time_format($dateTime[1]) : "";	
		
		$showDateTime = $date." ".$time;
		
		return $showDateTime;
    }
}