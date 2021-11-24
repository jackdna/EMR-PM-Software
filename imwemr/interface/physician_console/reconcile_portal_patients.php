<?php
require_once(dirname(__FILE__) . '/../../config/globals.php');

$library_path = $GLOBALS['webroot'] . '/library';

$sel_op = (isset($_REQUEST['sel_op']) && $_REQUEST['sel_op']) ? $_REQUEST['sel_op'] : '';
$row_id = $_REQUEST['row_id'];
$req_id = (isset($_REQUEST['req_id']) && $_REQUEST['req_id']) ? $_REQUEST['req_id'] : '';
$pt_ids = (isset($_REQUEST['pt_ids']) && $_REQUEST['pt_ids']) ? $_REQUEST['pt_ids'] : '';

if (isset($_REQUEST['action']) && ($_REQUEST['action'] == 'new_patient' || ($_REQUEST['action'] == 'existing_patient' && $pt_ids!='') ) ) {
    $portal_sql = "Select * from erp_iportal_patients_data where id='" . $row_id . "' ";
    $portal_rs = imw_query($portal_sql);
    if ($portal_rs && imw_num_rows($portal_rs) == 1) {
        $row = imw_fetch_assoc($portal_rs);

        $birthdayArr = explode('T', $row['birthday']);

        if ($row['sexExternalId'] && $row['sexExternalId'] != '') {
            $qry = "Select gender_name,erp_gender_id from gender_code Where is_deleted = 0 and gender_id='" . $row['sexExternalId'] . "' ";
            $sql = imw_query($qry);
            if ($sql && imw_num_rows($sql) > 0) {
                $genderr = imw_fetch_assoc($sql);
                $gender = $genderr['gender_name'];
            }
        }
        $row['sex'] = $gender;

        $voice_phone = str_ireplace('+1', '', $row['communicationsVoicePhone']);
        $text_phone = str_ireplace('+1', '', $row['communicationsTextPhone']);
        if (strlen($voice_phone) != 10) {
            $voice_phone = '';
        }
        if (strlen($text_phone) != 10) {
            $text_phone = '';
        }

        $patientDataArr = array();
        $patientDataArr['fname'] = ucwords($row['firstName']);
        $patientDataArr['mname'] = ucwords($row['middleName']);
        $patientDataArr['lname'] = ucwords($row['lastName']);
        $patientDataArr['sex'] = $row['sex'];
        $patientDataArr['email'] = $row['communicationsEmail'];
        $patientDataArr['DOB'] = getDateFormatDB($birthdayArr[0]);
        $patientDataArr['street'] = $row['address1'];
        $patientDataArr['street2'] = $row['address2'];
        $patientDataArr['postal_code'] = (inter_country() != "UK") ? core_padd_char($row['zipCode'], 5) : $row['zipCode'];
        $patientDataArr['city'] = $row['city'];
        $patientDataArr['state'] = $row['state'];

        $preferr_contact = 0;
        $phone_cell = '';
        $phone_biz = '';
        $phone_home = '';
        if ($text_phone == '' && $voice_phone != '') {
            $phone_home = $voice_phone;
            $preferr_contact = 0;
        }
        if ($text_phone != '' && $voice_phone == '') {
            $phone_cell = $text_phone;
            $preferr_contact = 2;
        }
        if ($text_phone != '' && $voice_phone != '' && $text_phone == $voice_phone) {
            $phone_cell = $text_phone;
            $preferr_contact = 2;
        }
        if ($text_phone != '' && $voice_phone != '' && $text_phone != $voice_phone) {
            $phone_cell = $text_phone;
            $phone_home = $voice_phone;
            $preferr_contact = 0;
        }

        $patientDataArr['phone_home'] = core_phone_unformat($phone_home);
        $patientDataArr['phone_biz'] = core_phone_unformat($phone_biz);
        $patientDataArr['phone_cell'] = core_phone_unformat($phone_cell);
        $patientDataArr["preferr_contact"] = $preferr_contact;
        $patientDataArr["hipaa_mail"] = 1;
        $patientDataArr["hipaa_email"] = 1;
        $patientDataArr["hipaa_voice"] = 1;
        $patientDataArr["hipaa_text"] = 1;
        if ($row['communicationsEmail'] == '') {
            $patientDataArr["hipaa_email"] = 0;
        }
        if ($text_phone == '') {
            $patientDataArr["hipaa_text"] = 0;
        }
        if ($voice_phone == '') {
            $patientDataArr["hipaa_voice"] = 0;
        }
        $patientDataArr["date"] = date('Y-m-d  H:i:s');
        $patientDataArr["erp_patient_id"] = $row['pt_portal_id'];
		
		$pt_data_before_reconciled=array();
		if($pt_ids=='' && $_REQUEST['action'] == 'new_patient') {
			$pt_id = AddRecords($patientDataArr, 'patient_data');
			$is_reconciled=1;
			if ($pt_id) {
				imw_query("update patient_data set pid=" . $pt_id . " where id=$pt_id ");
			}
		} else {
			$pt_id = $pt_ids;
			$is_reconciled=2;
			
			$qry1='SELECT id,fname,lname,DOB,street,street2,postal_code,city,state,country_code,phone_home,
			phone_biz,phone_cell,sex,email,hipaa_email,hipaa_voice,hipaa_text,erp_patient_id from patient_data where id = "'.$pt_id.'" ';
			$sqlrs = imw_query($qry1);
			if (imw_num_rows($sqlrs) > 0 && $sqlrs) {
				$pt_data_before_reconciled = imw_fetch_assoc($sqlrs);
			}
			
			UpdateRecords($pt_id,'id',$patientDataArr,'patient_data');
			
		}
		
		$pt_data_before_reconciled = json_encode($pt_data_before_reconciled);

        $sql1 = "update erp_iportal_patients_data set 
			action_date='" . date('Y-m-d  H:i:s') . "',
			approved_by='" . $_SESSION['authId'] . "',
			approved_declined=1,
			patient_id='".$pt_id."',
			is_reconciled='".$is_reconciled."',
			pt_data_before_reconciled='".$pt_data_before_reconciled."'
			where id='" . $row_id . "'  ";
		imw_query($sql1);

        echo json_encode($pt_id);
        die;
    }
}


if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'cancel_reconcile') {
	if($row_id) {
		$sql1 = 'update erp_iportal_patients_data set action_date="' . date('Y-m-d  H:i:s') . '", approved_by="' . $_SESSION['authId'] . '",approved_declined=2 where id="' . $row_id . '"  ';
        imw_query($sql1);

		$rows=imw_affected_rows($sql1);
        echo json_encode($rows);
        die;
	}
}

if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'get_details' ) {
	require_once($GLOBALS['fileroot'] . '/library/classes/msgConsole.php');
	$msgConsoleObj = new msgConsole();
	$rq_obj = array();
	
	$portal_sql = "Select *,patient_id as pt_id,
              DATE_FORMAT(created_on,'" . get_sql_date_format() . " %h:%i %p') as reqDateTime 
			  from erp_iportal_patients_data where id='".$row_id."' ";
    $portal_rs = imw_query($portal_sql);
    if ($portal_rs && imw_num_rows($portal_rs) == 1) {
			$row = imw_fetch_assoc($portal_rs);

            $rq_obj[$msgId] = $row;
            
            $patientId = $row['pt_id'];

            $patientData = $msgConsoleObj->get_patient_more_info($patientId);

			$default_facility = $patientData['default_facility'];
			if ($default_facility != ''){
				$pt_fac = "";
				$res = imw_query("SELECT facility_name FROM pos_facilityies_tbl WHERE pos_facility_id='" . $default_facility . "'");
				$row = imw_fetch_assoc($res);
				$def_fac = trim($row['facility_name']);
				if($def_fac != ""){
					$pt_fac = $def_fac;
				} else{
					$pt_fac = "N/A";
				}
			}

			/* Patient Image */
            $image_path='';
            $dir_path = $GLOBALS['file_upload_dir'];
            if ($patientData['p_imagename'] != '') {
                $patientData['p_imagename'] = $dir_path . $patientData['p_imagename'];
                $image_path = data_path() . $patientData['p_imagename'];
            }
            if (trim($patientData['p_imagename']) == '' || !file_exists($image_path)) {
                $image_path = $GLOBALS['fileroot'] . '/library/images/no_image_found.png';
            }
            if (trim($patientData['p_imagename']) == '' || !file_exists($patientData['p_imagename']))
                $patientData['p_imagename'] = $GLOBALS['webroot'] . '/library/images/no_image_found.png';

            /* Address */
            $pt_address = $patientData['street'];
            if (trim($patientData['street2']) != '')
                $pt_address .= ', ' . trim($patientData['street2']);

            $csz = '';
            $csz .= $patientData['city'];
            if ($csz != ' ')
                $csz .= ', ' . $patientData['state'];
            else
                $csz = $patientData['state'];

            if ($csz != '' && trim($patientData['postal_code']) != '')
                $csz .= ' - ' . $patientData['postal_code'];
            else
                $csz = $patientData['postal_code'];

            if ($csz != '' && $patientData['zip_ext'] != '')
                $csz .= '-' . $patientData['zip_ext'];

            /* Phone */
            $home=$work=$cell='';
            if ($patientData['phone_home'] != '') {
                $home=str_replace(" ", "", core_phone_format($patientData['phone_home']));
                $patientData['phone_home'] = '<div class="col-sm-2 pt5"><strong>Home</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">' . $home . '</div><div class="clearfix"></div>';
            }
            if ($patientData['phone_biz'] != '') {
                $work=str_replace(" ", "", core_phone_format($patientData['phone_biz']));
                $patientData['phone_biz'] = '<div class="col-sm-2 pt5"><strong>Work</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">' . $work . '</div><div class="clearfix"></div>';
            }
            if ($patientData['phone_cell'] != ''){
                $cell=str_replace(" ", "", core_phone_format($patientData['phone_cell']));
                $patientData['phone_cell'] = '<div class="col-sm-2 pt5"><strong>Cell</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">' . $cell . '</div><div class="clearfix"></div>';
            }
			$curr_pt_rp_name = "";
			if ($row['pt_resp_party_id'] != 0){
					$id = $row['pt_resp_party_id'];
					$sqlRPMsg = "Select id, fname, 	lname from resp_party where id = $id";
					$respRPMsg = imw_query($sqlRPMsg);
					if ($respRPMsg && imw_num_rows($respRPMsg) > 0) {
					$rowRP = imw_fetch_assoc($respRPMsg);
					$curr_pt_rp_name = $rowRP['lname'] . ', ' . $rowRP['fname'];
					$curr_pt_rp_name .= ' - ' . $rowRP["id"].'<br />(Patient Representative)';
				}
			} else {
				$patient_RP_Data =   $patientData['lname'] . ', ' . $patientData['fname'];
				$patient_RP_Data .= ' - ' . $patientData["id"].'<br /> (Patient)';
				$curr_pt_rp_name = $patient_RP_Data;
			}

            /* Appointment Details */
            $pt_appt = $msgConsoleObj->get_pt_appt($patientData['id']);
            if ($pt_appt['appt_dt_time'] != '') {
                $facility_name = $pt_appt['facility_name'];
                if (str_word_count($facility_name) != 1) {
                    $arr_facility_name = str_word_count($facility_name, 1);
                    $tmp_arr_fac_name = '';
                    foreach ($arr_facility_name as $val) {
                        $tmp_arr_fac_name .= substr($val, 0, 1);
                    }
                    $facility_name = strtoupper($tmp_arr_fac_name);
                }
                $appt_data = $pt_appt['phy_init_name'] . ' / ' . $pt_appt['appt_dt_time'] . ' / ' . $facility_name;
            } else
                $appt_data = 'N/A';

            $dataHeight = (int) $_SESSION['wn_height'] - 720;

			$messageText = '';
            $subject = '';
			if($row['is_reconciled']==1) {
				$messageText = 'New Patient Created<br>';
				$subject = 'New Patient Created';
			}
			if($row['is_reconciled']==2) {
				$messageText = 'Patient data before reconcile<br>';
				$subject = 'Reconciled with existing Paient';
			}
			if($row['approved_declined']==2) {
				$messageText = 'Patient Rquest to create patient has been cancelled<br>';
				$subject = 'Reconciled Request cancelled';
			}
			
			//$messageText.=$row['pt_data_before_reconciled'];
			if($row['pt_data_before_reconciled']) {
				$ptDataArrr= json_decode($row['pt_data_before_reconciled'], true);
				
				 /* Address */
				$pt_address1 = $ptDataArrr['street'];
				if (trim($ptDataArrr['street2']) != '')
					$pt_address1 .= ', ' . trim($ptDataArrr['street2']);

				$csz1 = '';
				$csz1 .= $ptDataArrr['city'];
				if ($csz1 != ' ')
					$csz1 .= ', ' . $ptDataArrr['state'];
				else
					$csz1 = $ptDataArrr['state'];

				if ($csz1 != '' && trim($ptDataArrr['postal_code']) != '')
					$csz1 .= ' - ' . $ptDataArrr['postal_code'];
				else
					$csz1 = $ptDataArrr['postal_code'];

				if ($csz1 != '' && $ptDataArrr['zip_ext'] != '')
					$csz1 .= '-' . $ptDataArrr['zip_ext'];

				/* Phone */
				$home1=$work1=$cell1='';
				if ($ptDataArrr['phone_home'] != '') {
					$home1=str_replace(" ", "", core_phone_format($ptDataArrr['phone_home']));
				}
				if ($ptDataArrr['phone_biz'] != '') {
					$work1=str_replace(" ", "", core_phone_format($ptDataArrr['phone_biz']));
				}
				if ($ptDataArrr['phone_cell'] != ''){
					$cell1=str_replace(" ", "", core_phone_format($ptDataArrr['phone_cell']));
				}
				
				
				
			 $messageText .= '
                <table style="width:760px;">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td><strong>Patient Name : </strong>'.$ptDataArrr['lname'].', '.$ptDataArrr['fname'].' '.$ptDataArrr['mname'].'</td></tr>
                    <tr><td><strong>Gender : </strong>'.$ptDataArrr['sex'].'</td></tr>
                    <tr><td><strong>DOB : </strong>'.$ptDataArrr['DOB'].'</td></tr>
                    <tr><td><strong>Address : </strong>'.$pt_address1.' '.$csz1.'</td></tr>
                    <tr><td><strong>Home : </strong>'.$home1.'</td></tr>
                    <tr><td><strong>Work : </strong>'.$work1.'</td></tr>
                    <tr><td><strong>Cell : </strong>'.$cell1.'</td></tr>
                    <tr><td><strong>Email : </strong>'.$ptDataArrr['email'].'</td></tr>
                </table>';
			}
            
			//$messageText.=$row['pt_data_before_reconciled'];
			if($row['approved_declined']==2) {
				
				 /* Address */
				$pt_address2 = $row['address1'];
				if (trim($row['address2']) != '')
					$pt_address2 .= ', ' . trim($row['address2']);

				$csz2 = '';
				$csz2 .= $row['city'];
				if ($csz2 != ' ')
					$csz2 .= ', ' . $row['state'];
				else
					$csz2 = $row['state'];

				if ($csz2 != '' && trim($row['zipCode']) != '')
					$csz2 .= ' - ' . $row['zipCode'];
				else
					$csz2 = $row['zipCode'];


				$voice_phone = str_ireplace('+1', '', $portal_pt['communicationsVoicePhone']);
				$text_phone = str_ireplace('+1', '', $portal_pt['communicationsTextPhone']);
				if (strlen($voice_phone) != 10) {
					$voice_phone = '';
				}
				if (strlen($text_phone) != 10) {
					$text_phone = '';
				}


				$home2=$work2=$cell2='';
				if ($text_phone == '' && $voice_phone != '') {
					$home2 = core_phone_format($voice_phone);
				}
				if ($text_phone != '' && $voice_phone == '') {
					$cell2 = core_phone_format($text_phone);
				}
				if ($text_phone != '' && $voice_phone != '' && $text_phone == $voice_phone) {
					$cell2 = core_phone_format($text_phone);
				}
				if ($text_phone != '' && $voice_phone != '' && $text_phone != $voice_phone) {
					$cell2 = core_phone_format($text_phone);
					$home2 = core_phone_format($voice_phone);
				}
				
				$birthdayArr = explode('T', $row['birthday']);
				
				$gender='';
				if ($row['sexExternalId'] && $row['sexExternalId'] != '') {
					$qry = "Select gender_name,erp_gender_id from gender_code Where is_deleted = 0 and gender_id='" . $row['sexExternalId'] . "' ";
					$sql = imw_query($qry);
					if ($sql && imw_num_rows($sql) > 0) {
						$genderr = imw_fetch_assoc($sql);
						$gender = $genderr['gender_name'];
					}
				}
				
				$messageText .= '
                <table style="width:760px;">
                    <tr><td>&nbsp;</td></tr>
                    <tr><td><strong>Patient Name : </strong>'.$row['lastName'].', '.$row['firstName'].' '.$row['middleName'].'</td></tr>
                    <tr><td><strong>Gender : </strong>'.$gender.'</td></tr>
                    <tr><td><strong>DOB : </strong>'.$birthdayArr[0].'</td></tr>
                    <tr><td><strong>Address : </strong>'.$pt_address2.' '.$csz2.'</td></tr>
                    <tr><td><strong>Home : </strong>'.$home2.'</td></tr>
                    <tr><td><strong>Work : </strong>'.$work2.'</td></tr>
                    <tr><td><strong>Cell : </strong>'.$cell2.'</td></tr>
                    <tr><td><strong>Email : </strong>'.$row['email'].'</td></tr>
					<tr><td class="hide">&nbsp;</td></tr>
                </table>';
			}

            /*PDF print data Starts*/
            $image_tag=(file_exists($image_path))?'<img src="'.$image_path.'" alt="" style="width:76px;" />':'';

            $pdf_responseData .= '
                <table style="width:760px;">';
				
			if($row['approved_declined']!=2) {
				$pdf_responseData .= '<tr><td>'.$image_tag.'</td></tr>
						<tr><td><strong>Patient Name : </strong>'.$patientData['lname'].', '.$patientData['fname'].' '.$patientData['mname'].' - '.$patientData['id'].'</td></tr>
						<tr><td><strong>Gender : </strong>'.$patientData['sex'].'</td></tr>
						<tr><td><strong>DOB : </strong>'.$patientData['DOB'].'</td></tr>
						<tr><td><strong>Address : </strong>'.$pt_address.' '.$csz.'</td></tr>
						<tr><td><strong>Home : </strong>'.$home.'</td></tr>
						<tr><td><strong>Work : </strong>'.$work.'</td></tr>
						<tr><td><strong>Cell : </strong>'.$cell.'</td></tr>
						<tr><td><strong>Email : </strong>'.$patientData['email'].'</td></tr>
						<tr><td><strong>Appt : </strong>'.$appt_data.'</td></tr>
						<tr><td><strong>Sender : </strong>'. strip_tags($curr_pt_rp_name).'</td></tr>
						<tr><td><strong>Facility : </strong>'.$pt_fac.'</td></tr>';
				}
            $pdf_responseData .= '<tr><td style="width:730px;"><strong>Message Text : </strong><p>'.$messageText.'</p></td></tr>
                </table>';

            $final_data='<page backtop="5mm" backbottom="5mm">
                <page_footer>
                    <table style="width:760px;">
                        <tr>
                            <td style="text-align:center;width:100%">Page [[page_cu]]/[[page_nb]]</td>
                        </tr>
                    </table>
                </page_footer>
                <page_header>
                    <style>
                        .tb_headingHeader{
                            font-weight:bold;
                            color:#FFFFFF;
                            background-color:#4684ab;
                        }
                    </style>
                    <table style="width:760px;">
                        <tr><td style="width:755px;" class="tb_headingHeader">Patient Messages</td></tr>
                    </table>
                </page_header>';
            $final_data.=$pdf_responseData.'</page>';

            $filesPath = data_path()."/UserId_".$_SESSION['authId']."/tmp/pt_messages/*";
            $files = glob($filesPath);
            foreach($files as $file){
                if(is_file($file))@unlink($file);
            }
            $rand=rand(0,500);
            $print_file_name = 'pt_messages/patient_message_'.$_SESSION['authId']."_".$patientId."_".$msgId."_".$rand;
            $file_location = write_html($final_data,$print_file_name.".html");

            /*PDF print data ends*/

            $responseData .= '<div class="clearfix"></div>

			<!--Patient Details-->
			<div id="ptmsg_content" data-msg_id="'.$msgId.'"  data-patientId="'.$patientId.'">
			<div class="ptcommu">';
			
			if($row['approved_declined']!=2) {
			$responseData .= '<div class="row">
					<div class="col-sm-5">
						<div class="ptdtl">
							<figure>
								<img src="'.$patientData['p_imagename'].'" alt="" style="width:76px;" />
							</figure>
							<div><strong><span class="text_purple pointer" onClick="LoadWorkView('.$patientId.');">'.$patientData['lname'].', '.$patientData['fname'].' '.$patientData['mname'].' - '.$patientData['id'].'</span></strong></div>
							 <div class="clearfix"></div>
							<div class="row">
								<div class="col-sm-6"><strong>Gender</strong>   :	'.$patientData['sex'].'</div>
								<div class="col-sm-6"><strong>DOB</strong>   :     '.$patientData['DOB'].'</div>
							</div>

							<div class="clearfix"></div>

							<div class="row">
								<div class="col-sm-3"><strong>Address</strong> :</div>
								<div class="col-sm-9">'.$pt_address.' '.$csz.'</div>
							</div>
						</div>
					</div>

					<div class="col-sm-7 ptcontdtl">
						<div class="row">
							<div class="col-sm-5">
								<div class="row continfo">
									'.$patientData['phone_home'].'
									'.$patientData['phone_biz'].'
									'.$patientData['phone_cell'].'
									<div class="col-sm-2 pt5">
										<strong>Email</strong>
									</div>
									<div class="col-sm-1 pt5"> : </div>
									<div class="col-sm-9 pt5">'.$patientData['email'].'</div>
								</div>
							</div>
							<div class="col-sm-7">
								<div class="row continfo" style="min-height:0px!important;">
									<div class="col-sm-2">
										<strong>Appt</strong>
									</div>
									<div class="col-sm-1"> : </div>
									<div class="col-sm-9">'.$appt_data.'</div>
								</div>
								<div class="row continfo">
									<div class="col-sm-2">
										<strong>Sender</strong>
									</div>
									<div class="col-sm-1"> : </div>
									<div class="col-sm-9">'.$curr_pt_rp_name.'</div>
									<div class="col-sm-2">
										<strong>Facility</strong>
									</div>
									<div class="col-sm-1"> : </div>
									<div class="col-sm-9">'.$pt_fac.'</div>
								</div>
							</div>
						</div>

					<!--

						<div class="ptinfara">
							<div class="row">
								<div class="col-sm-5 ptcomubut">
									<div class="checkbox">
										<label>
											<input type="checkbox"> Patient Verbal Communication
										</label>
									</div>
								</div>
							</div>
						</div>
						-->
						<div class="clearfix"></div>
					</div>
				</div>';
				
				}
				
			$responseData .= '</div>

			<div class="clearfix"></div>

			<!--Complete Message Data-->
			<div>
				<div class="postmessage">
					<div class="scroll-content mCustomScrollbar" style="height:'.$dataHeight.'px;">
						<span class="ptmsgText">'.$messageText.'</span>
                        <input type="hidden" name="pt_msg_location" id="pt_msg_location" value="'.$file_location.'" />
					</div>
				</div>
			</div>
			</div>';

        }

        echo json_encode($responseData);
		die;
}



$portal_pt = array();
$ptExists = array();
if ($sel_op == 'reconcile' && $row_id && $req_id) {
    $portal_sql = "Select * from erp_iportal_patients_data where id='" . $row_id . "' ";
    $portal_rs = imw_query($portal_sql);
    if ($portal_rs && imw_num_rows($portal_rs) == 1) {
        $portal_pt = imw_fetch_assoc($portal_rs);

        $birthdayArr = explode('T', $portal_pt['birthday']);
        $portal_pt['pt_dob'] = $birthdayArr[0];

        if ($portal_pt['sexExternalId'] && $portal_pt['sexExternalId'] != '') {
            $qry = "Select gender_name,erp_gender_id from gender_code Where is_deleted = 0 and gender_id='" . $portal_pt['sexExternalId'] . "' ";
            $sql = imw_query($qry);
            if ($sql && imw_num_rows($sql) > 0) {
                $genderr = imw_fetch_assoc($sql);
                $gender = $genderr['gender_name'];
            }
        }
        $portal_pt['sex'] = $gender;

        $voice_phone = str_ireplace('+1', '', $portal_pt['communicationsVoicePhone']);
        $text_phone = str_ireplace('+1', '', $portal_pt['communicationsTextPhone']);
        if (strlen($voice_phone) != 10) {
            $voice_phone = '';
        }
        if (strlen($text_phone) != 10) {
            $text_phone = '';
        }
        $portal_pt['voice_phone'] = $voice_phone;
        $portal_pt['text_phone'] = $text_phone;
    }
}

if (count($portal_pt) > 0) {

    //AND sex = "'.$portal_pt['sex'].'" AND postal_code = "'.$portal_pt['postal_code'].'" 
    //$sql = imw_query('SELECT * from patient_data where id IN (2,3,5,8)');
    $sql = imw_query('SELECT id,fname,lname,DOB,street,street2,postal_code,city,state,country_code,phone_home,phone_biz,phone_cell,sex,email from patient_data where fname = "'.$portal_pt['firstName'].'" AND lname = "'.$portal_pt['lastName'].'" AND DOB = "'.$portal_pt['pt_dob'].'" ');
    if (imw_num_rows($sql) > 0 && $sql) {
        while ($row = imw_fetch_assoc($sql)) {
            $ptExists[$row['id']] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>Reconcile Portal Patient</title>
        <!-- Bootstrap -->
        <link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
        <!-- Bootstrap Selctpicker CSS -->
        <link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">

        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>

        <!-- Bootstrap -->
        <script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>

        <!-- Bootstrap Selectpicker -->
        <script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
        <script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
        <script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
        <style>
            .process_loader {
                border: 16px solid #f3f3f3;
                border-radius: 50%;
                border-top: 16px solid #3498db;
                width: 80px;
                height: 80px;
                -webkit-animation: spin 2s linear infinite;
                animation: spin 2s linear infinite;
                display: inline-block;
            }

        </style>
    </head>
    <body>
        <div class="container-fluid pd0">
            <div class="mainwhtbox">
                <div class="row">
                    <div id="div_loading_image" class="col-sm-12 text-center" style="top:50%;margin-top: 0px; display: none;position:absolute;z-index:9999">
                        <div class="loading_container">
                            <div class="process_loader"></div>
                            <div id="div_loading_text" class="text-info"></div>
                        </div>
                    </div>	
                    <!-- Header -->
                    <div id="content_div_head" class="col-sm-12 pt10 purple_bar">
                        Reconcile Portal Patient	
                    </div>	

                    <!-- Content Block -->
                    <div id="content_block" class="col-sm-12" style="min-height:630px;" >
                        <?php if (count($portal_pt) > 0) { ?>
                            <div class="adminnw">
                                <div class="row">
                                    <div class="pd5 col-sm-12 btn-group btn-group-md">
                                        <h4>Portal Patient</h4>	
                                    </div>
                                    <div class="clearfix"></div>


                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">First Name</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['firstName']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Last Name</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['lastName']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Date of Birth</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['pt_dob']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Gender</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['sex']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Postal Code</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['zipCode']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Street</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['address1']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Street 2</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['address2']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">City</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['city']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">State</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['state']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Voice Phone</label>
                                            <input type="text" class="form-control" value="<?php echo core_phone_format($portal_pt['voice_phone']); ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Text Phone</label>
                                            <input type="text" class="form-control" value="<?php echo core_phone_format($portal_pt['text_phone']); ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="">Email</label>
                                            <input type="text" class="form-control" value="<?php echo $portal_pt['communicationsEmail']; ?>" readonly="" autocomplete="off">	
                                        </div>
                                    </div>

                                </div>
                            </div>
                        <?php } ?>
                        
                        
                        <?php
                        if (count($ptExists) > 0) {

                            $title = str_replace('__', ' ', 'Patient(s)__Found') . '  -  ' . count($ptExists);
                            ?>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="pd5">
                                        <div class="panel group" id="mainParent">
                                            <div class="panel panel-info">
                                                <div class="panel-heading">
                                                    <div class="row">
                                                        <div style="width:100%">
                                                            <h4 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#mainParent" href="#main"><?php echo $title; ?></a>
                                                            </h4>
                                                        </div>
                                                    </div>
                                                </div>	
                                            </div>	
                                        </div>

                                        <?php
                                        $counter = 0;
                                        $loop = 0;
                                        foreach ($ptExists as $key => $obj) {
                                            $coll_in = 'in';
                                            $blockClass = 'danger';

                                            $ptName = core_name_format($obj['lname'], $obj['fname']);
                                            ?>

                                            <div class="panel-collapse collapse <?php echo $coll_in; ?>">
                                                <div class="panel-group" id="accordion_<?php echo $key . '__' . $counter; ?>">

                                                    <div class="panel panel-<?php echo $blockClass; ?>" id="pt_<?php echo $obj['id']; ?>">
                                                        <div class="panel-heading pointer">
                                                            <div class="row">
                                                                <div style="width:3%" class="pull-left">
                                                                    <div class="checkbox">
                                                                        <input id="checkbox<?php echo $obj['id']; ?>" type="checkbox" name="chk_record" value="<?php echo $obj['id']; ?>" class="chk_record" onclick="select_existing_patient('<?php echo $obj['id']; ?>');">
                                                                        <label for="checkbox<?php echo $obj['id']; ?>">&nbsp;</label>
                                                                    </div>
                                                                </div>	
                                                                <div style="width:97%" class="pull-left" data-toggle="collapse" data-parent="#accordion_<?php echo $key . '__' . $counter; ?>" href="#<?php echo $key; ?>_collapse_<?php echo $loop; ?>" style="vertical-align:sub;">
                                                                    <div class="row">
                                                                        <div class="col-sm-3">
                                                                            <h4 class="panel-title">
                                                                                <a data-toggle="tooltip" data-placement="top" title="Click for Patient Details">
                                                                                    <?php echo $ptName . ' - ' . $obj['id']; ?>
                                                                                </a>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                </div>	
                                                            </div>
                                                        </div>
                                                        <div id="<?php echo $key; ?>_collapse_<?php echo $loop; ?>" class="panel-collapse collapse">
                                                            <div class="panel-body">
                                                                <div class="clearfix"></div>
                                                                <div class="row">
                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">First Name</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['fname']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Last Name</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['lname']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Date of Birth</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['DOB']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Gender</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['sex']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Postal Code</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['postal_code']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Street</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['street']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Street 2</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['street2']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">City</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['city']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">State</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['state']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Voice Phone</label>
                                                                            <input type="text" class="form-control" value="<?php echo core_phone_format($obj['voice_phone']); ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Mobile Phone</label>
                                                                            <input type="text" class="form-control" value="<?php echo core_phone_format($obj['phone_cell']); ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-sm-2">
                                                                        <div class="form-group">
                                                                            <label for="">Email</label>
                                                                            <input type="text" class="form-control" value="<?php echo $obj['email']; ?>" readonly="" autocomplete="off">	
                                                                        </div>
                                                                    </div>


                                                                    <div class="clearfix"></div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <?php
                                            $counter++;
                                            $loop++;
                                        }
                                        ?>
                                    </div>	
                                </div>

                            </div>



                        <?php } ?>

                    </div>

                    <!-- Footer Buttons -->
                    <div id="core_buttons_bar" class="col-sm-12 pt10 text-center">
                        <div id="page_buttons">
                            <?php if ($portal_pt['approved_declined'] == 0) { 
								if (count($ptExists) > 0) {
								?>
                                <button name="reconcile"  id="reconcile" class="btn btn-success" onClick="reconcile_existing_patient('<?php echo $row_id; ?>')">Reconcile</button> 
								<?php } ?>
                                <button name="new_pt" id="new_pt" class="btn btn-success" onClick="create_new_patient('<?php echo $row_id; ?>')">Create New Patient</button>
                                <button name="cancel" id="cancel" class="btn btn-danger" onClick="cancel_operation('<?php echo $row_id; ?>');">Cancel</button>
                            <?php } ?>
                            <button name="close" id="close" class="btn btn-danger" onClick="window.close();">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
<script>

    function create_new_patient(row_id) {
        if (!row_id)
            return false;

        $.ajax({
            url: 'reconcile_portal_patients.php',
            data: 'action=new_patient&row_id=' + row_id,
            type: 'POST',
            dataType: 'JSON',
            success: function (resultData)
            {
                if (resultData) {
                    window.opener.do_action("load_patient_messages", "pt_changes_approval");
                }
            },
            complete: function () {
                window.close();
            }
        });
    }

    function select_existing_patient(pt_id) {
        if (!pt_id) return false;
		var propVal=$('#checkbox'+pt_id).is(':checked');
        $('.chk_record').prop('checked', false);
        
        if(propVal) {
            $('#checkbox'+pt_id).prop('checked', true);
        } else {
            $('#checkbox'+pt_id).prop('checked', false);
        }
    }

	function reconcile_existing_patient(row_id) {
        if (!row_id)
            return false;
		
		var chkVal = [];
		var chk_values = '';
		$('.chk_record').each(function(id, ele){
			var value = $(ele).val();
			if ($(ele).attr('checked') == 'checked' || $(ele).attr('checked') == true || $(ele).prop('checked') == true) {
				if(value && typeof(value) !== 'undefined') chkVal.push(value);
			}
		});
		if(chkVal.length){
			chk_values = chkVal.join(',');
		}
		
		if(chk_values=='') {
			top.fAlert('Please select a patient to Reconcile.');
			return false;
		}
		
		$.ajax({
            url: 'reconcile_portal_patients.php',
            data: 'action=existing_patient&row_id=' + row_id +'&pt_ids='+chk_values,
            type: 'POST',
            dataType: 'JSON',
            success: function (resultData)
            {
                if (resultData) {
                    window.opener.do_action("load_patient_messages", "pt_changes_approval");
                }
            },
            complete: function () {
                window.close();
            }
        });
	}
	
	
	function cancel_operation(row_id) {
        if (!row_id)
            return false;
		
		window.opener.cancel_reconcile(row_id,'popup');
		
		window.close();
		
	}

</script>