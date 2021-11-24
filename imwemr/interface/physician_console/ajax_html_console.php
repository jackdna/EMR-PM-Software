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
switch($task)
{
	case 'ptcomm':
		$sort=$_REQUEST['sort'];
		$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'msg_my_inbox';
		$call_case = '';
		$del_type = 0; // 0 for receiver delete and 1 for sent delete
		$my_inbox_status = '';
		$patient_messages_status = '';
		$future_alerts_status = '';
		$msg_sent_messages_status = '';
		$no_data_msg = 'No record found';
		$detail_opts_show = 0;
		$load_buttons = '';
		$show_flag_cols = 0;
		$show_reply_buttons = 1;
		$page_no=$_REQUEST['page_no'];
		$per_page=$_REQUEST['per_page'];
		if($per_page && (int)$per_page>0 && $_SESSION['authUserID']!=''){
			$qry_user="UPDATE users set msg_per_page='".$per_page."' WHERE id=".$_SESSION['authUserID'];
			$res_user=imw_query($qry_user);
			$_SESSION['per_page']=$per_page;
		}
		if(!$_SESSION['per_page'] || $_SESSION['per_page']==""){
			$qry_user="SELECT msg_per_page FROM users WHERE id='".$_SESSION['authUserID']."'";
			$res_user=imw_query($qry_user);
			$row_user=imw_fetch_assoc($res_user);
			$_SESSION['per_page']=($row_user['msg_per_page']!="")?$row_user['msg_per_page']:"20";
		}
		unset($msgConsoleObj->limit_upto);
		switch($filter)
		{
			case 'msg_my_inbox':
				$call_case = 'my_inbox';
				$show_flag_cols = 1;
				$my_inbox_status ='checked="checked"';
				$from_to = 'FROM';
				$load_buttons = '<button class="btn btn-danger" onClick="del_messages(\'inbox\');" >Delete</button>';
			break;
			case 'msg_pt_messages':
				$call_case = 'patient_messages';
				$show_flag_cols = 1;
				$patient_messages_status = 'checked="checked"';
				if(trim($msgConsoleObj->patient_id)=='')
				{
					$no_data_msg = 'No patient selected';
				}
				$from_to = 'FROM';
				$load_buttons = '<button class="btn btn-danger" onClick="del_messages(\'inbox\');">Delete</button>';
			break;
			case 'msg_future_alerts':
				$call_case = 'future_alerts';
				$future_alerts_status = 'checked="checked"';
				$from_to = 'TO';
				$del_type = 1;
				$detail_opts_show = 1;
				$load_buttons = '<button class="btn btn-danger" onClick="del_messages(\'future_alerts\');">Delete</button>';
			break;
			case 'msg_sent_messages':
				$call_case = 'sent_messages';
				$msg_sent_messages_status ='checked="checked"';
				$from_to ='TO';
				$del_type =  1;
				$detail_opts_show = 0;
				$load_buttons = '<button class="btn btn-danger" onClick="del_messages(\'sent\');">Delete</button>';
			break;
			case 'msg_deleted_messages':
				$call_case = 'deleted_messages';
				$msg_sent_messages_status ='checked="checked"';
				$from_to ='TO';
				$del_type =  1;
				$detail_opts_show = 0;
				$show_reply_buttons = 0 ;
				$load_buttons = '<button class="btn btn-default" onClick="un_del_messages(\'deleted_messages\');">Restore</button>';
			break;
		}
		$per_page=$_SESSION['per_page'];

        $filter_arr=array();
        $move_to='';
        $messages_folder=$msgConsoleObj->fetch_messages_folder();
        if (empty($messages_folder) == false && $filter!='msg_deleted_messages') {
            foreach ($messages_folder as $key => $folder) {
                $filter_arr[] = str_replace(' ', '_', $folder) . '-' . $key;
            }
        }

        $folder_id=0;
        if($filter && in_array($filter,$filter_arr)) {
            $filter_req=explode('-',$filter);
            $folder_name=trim($filter_req[0]);
            $folder_id=trim($filter_req[1]);

            $call_case = trim($filter);
            $show_flag_cols = 1;
            $my_inbox_status ='checked="checked"';
            $from_to = 'FROM';
            $load_buttons = '<button class="btn btn-danger" onClick="del_messages(\''.$call_case.'\', \''.$folder_id.'\');" >Delete</button>';
        }

        if (empty($messages_folder) == false && $filter!='msg_deleted_messages') {
            $move_to='<select name="folder_name" id="folder_name" class="form-control minimal move_to_folder" disabled="disabled" onchange="save_to_folder(this)">';
                $move_to.='<option value="">Save To</option>';
                foreach ($messages_folder as $key => $folder) {
                    //$filter_arr[] = str_replace(' ', '_', $folder) . '-' . $key;
                    if($key!=$folder_id) {
                        $move_to.='<option value="'.$key.'" data-folder_name="'.$folder.'">'.$folder.'</option>';
                    }
                }
            $move_to.='</select>';
        }



		$result_data_arr_GET = $msgConsoleObj->get_messages_reminders($call_case,$page_no,$per_page,$filter, $sort,$folder_id);
		$result_data_arr=$result_data_arr_GET[0];
		$p_link1=$result_data_arr_GET[1];
		$p_link2=$result_data_arr_GET[2];
		$options="";
		for($b=10;$b<=100;$b+=($b>=40)?20:10){
			$selected="";
			if((int)$per_page>0){
				if($per_page==$b){$selected="selected";}
			}else if($b==20){$selected="selected";}
			$options.="<option ".$selected." value='".$b."'>".$b."</option>";
		}

		$dataList = array();
		$tableData = '';
		$tableElem = '';

		/*Scrollable Data List*/
		$tableData .= '<div class="col-sm-4 messuser" id="messagelist">';

			/*List Messsage Count*/
			$tableData .= '<div class="row msgCount">';
				$tableData .= '<div class="col-sm-9">';
					$tableData .= $p_link1.' Record(s) per page &nbsp;';
				$tableData .= '</div>';
				$tableData .= '<div class="col-sm-3" style="padding-right:10px !important;">';
					$tableData .= '<select id="no_per_page" onChange="load_messages(\'\',this.value);" class="selectpicker" data-width="100%">'.$options.'</select>';
				$tableData .= '</div>';
			$tableData .= '</div>';

			/*Order By*/
			$tableData .= '<div class="row msgCount">';
				$tableData .= '<div class="col-sm-3">';
					$tableData .= '<div class="checkbox pull-left" style="margin-top: -4px !important;">';
					$tableData .= '<input type="checkbox" name="el_chk_all" id="el_chk_all" onclick="select_all_checkbox1(this)" ><label for="el_chk_all"></label>';
					$tableData .= '</div>';
				$tableData .= '</div>';
				$tableData .= '<div class="col-sm-5 form-inline">'.$move_to.'</div>';
				$tableData .= '<div class="col-sm-4 form-inline">';
					$tableData .= '<div class="form-group">';
					$tableData .= '<label for="el_sort_msg">Sort By: </label>';
					$tableData .= '<select id="el_sort_msg" class="form-control" onchange="load_messages()"><option value=""></option><option value="Sender" '.(strpos($sort,"Sender")!==false?"selected":"").'>Sender</option><option value="Date" '.(strpos($sort,"Date")!==false?"selected":"").'>Date</option><option value="Urgency" '.(strpos($sort,"Urgency")!==false?"selected":"").'>Urgency</option><option value="Flagged" '.(strpos($sort,"Flagged")!==false?"selected":"").'>Flagged</option></select>';
					$tableData .= '<span class="glyphicon glyphicon-sort" id="el_sort_val" onclick="load_messages_sort()" data-val="'.(strpos($sort,"DESC")!==false?"DESC":"ASC").'" style="cursor:pointer;" ></span></div>';
				$tableData .= '</div>';
			$tableData .= '</div>';


			$tableData .= '<div class="scroll-content mCustomScrollbar" style="height:'.($_SESSION['wn_height']-350).'px;">';
				$tableData .= '<ul class="messageList">';


		if(count($result_data_arr)>0)
		{
			$curr_pt_arr = core_get_patient_name($msgConsoleObj->patient_id);
			$curr_pt_name = $curr_pt_arr['2'].', '.$curr_pt_arr['1'];
			if(trim($curr_pt_arr['3'])!='')$curr_pt_name = $curr_pt_arr['2'].', '.$curr_pt_arr['1'].' '.$curr_pt_arr['3'];
			$curr_pt_name .= ' - '.$curr_pt_arr['0'];

			foreach($result_data_arr as $key=>$val_arr)
			{
				$senderDetails = array();
				if(isset($val_arr['message_sender_id']) && empty($val_arr['message_sender_id']) == false){
					$senderDetails = $msgConsoleObj->get_username_by_id(array($val_arr['message_sender_id']));
					if(is_array($senderDetails) && count($senderDetails) > 0) $senderDetails = $senderDetails[$val_arr['message_sender_id']];
				}

				$msgicon_tip = $unread_msg_icon = $unread_msg_bold = $urgent_icon_img = '';
				if(intval($val_arr['message_urgent'])==1){
					$urgent_icon_img = '<span class="icons_ptComm msg_status_icon icon_msg_urgent">a</span>';
				}
				if($val_arr['message_read_status']==1 || $filter=='msg_sent_messages' || $filter=='msg_future_alerts'){
					$unread_msg_bold = '';
					$unread_msg_icon = 'read';
				}else{
					$unread_msg_bold = ' unredBold';
					$unread_msg_icon = 'unread';
				}
				if($val_arr['msg_icon']==1){
					$unread_msg_icon = 'replied';
					$msgicon_tip = ' title="You replied to this message"';
				}else if($val_arr['msg_icon']==2){
					$unread_msg_icon = 'forwarded';
					$msgicon_tip = ' title="You forwarded this message"';
				}

				$msgUrgentStatus = false;
				if(isset($val_arr['message_urgent']) && $val_arr['message_urgent'] == 1) $msgUrgentStatus = true;

				$msgUrgentString = '';
				if($msgUrgentStatus === true){
					$msgUrgentString = '<span class="glyphicon glyphicon-exclamation-sign text-danger" title="Urgent" style="font-size:19px"></span>';
				}

				$msgSenderName = '';
				if(is_array($senderDetails) && count($senderDetails) > 0) $msgSenderName = $senderDetails['full'];
				if(empty($msgSenderName)) $msgSenderName = $val_arr['message_sender_name'];

				$msgTitle		= '';
				$name_length = 40;
				if( strlen($msgSenderName) > $name_length )
				{
					$msgSenderName	= substr($msgSenderName, 0, $name_length).'...';
					$msgTitle		= $msgSenderName;
				}

				$patient_id = 0;
				if($msgConsoleObj->patient_id != ""){
					$patient_id = $msgConsoleObj->patient_id;
				}

				$flagged_msg_icon = '';
				$flagOpt = '';
				if($show_flag_cols==1)
				{
					if(intval($val_arr['flagged'])==1){$flagged_msg_icon = 'flagged';}else{$flagged_msg_icon = 'unflagged';}
					$flagOpt = '<div class="pull-left" onclick="alter_msg_flag_status(\''.$val_arr['user_message_id'].'\',this);">
								<span class="icons_ptComm msg_status_icon '.$flagged_msg_icon.'" style="width:20px;height:20px;"></span>
							</div>';
				}

				/*Scrollable Data List*/
				$tableData .= '<li id="msg_'.$val_arr['user_message_id'].'" onClick="loadMessageDetails('.$val_arr['user_message_id'].', this, '.$patient_id.')" class="'.$unread_msg_bold.'">
					<div class="row">
						<div class="col-sm-10">
							'.$flagOpt.'
							<div class="checkbox pull-left" style="margin-top: -4px !important;">
								<input type="checkbox" name="chk_record[]" id="message_urgent'.$val_arr['user_message_id'].'" value="'.$val_arr['user_message_id'].'" class="chk_record user_msg_chk_record" onclick="enable_saveto(this)">
								<label for="message_urgent'.$val_arr['user_message_id'].'"></label>
							</div>
							<h2 '.(($msgTitle!='')?show_tooltip($msgTitle, 'top'):'').'>'.$msgSenderName.'</h2>
						</div>
						<div class="col-sm-2 text-right">
							'.$msgUrgentString.'
						</div>
					</div>';
					//$tableData .= '<h2 '.(($msgTitle!='')?show_tooltip($msgTitle, 'top'):'').'>'.$msgSenderName.'</h2>';
					$tableData .= '<div class=" clearfix"></div>';
					$tableData .= '<div class="pull-left physiname">';
					$tableData .= '<!-- img src="'.$GLOBALS['webroot'].'/library/images/physic.jpg" alt=""/ -->'.$val_arr['user_group'];
					$tableData .= '</div>';
					$tableData .= '<div class="pull-right mesuser ptName">';
					$tableData .= $val_arr['patient_name'];
					$tableData .= '</div>';
					$tableData .= '<div class="clearfix"></div>';
					$tableData .= '<div class="messsub">'.$val_arr['message_subject'].'</div>';
					$tableData .= '<div class="mesgdate">'.$val_arr['msg_send_date'].'</div>';
				$tableData .= '</li>';
			}

					/*End Scrollable Data List*/
						$tableData .= '</ul>';
					$tableData .= '</div>';


				/*Paging*/
				$tableData .= '<div class="clearfix"></div><div class="row msgCount countFooter" style="min-height:50px;">';
				$tableData .= $p_link2;
				$tableData .= '<div class="col-sm-2">'.$load_buttons.'</div></div>';

			$tableData .='</div>';

$tableData .= '
				<!--Container to load the message Details-->
				<div class="col-sm-8" >
					<div class="row">
						<div class="col-sm-12" id="messageData">

						</div>
					</div>

				</div>';

		}
		else
		{
			$tableData .='<div class="alert alert-danger">'.$no_data_msg.'</div>
			<script type="text/javascript">update_link_status(\'#message_reminders_opt\',\'unread\',\'read\');</script>';
		}

		//New message Popup
		$tableData .= '<div id="mrmsg_popup" class="modal common_modal_wrapper"><div class="modal-dialog modal-lg model_width"><div class="modal-content">
            <div class="modal-header bg-primary"><button type="button" class="close" data-dismiss="modal">x</button><h4 class="modal-title">New Message/ Task</h4></div>
            <div class="modal-body"><span class="replymrmsg_popup"></span></div>
            </div></div>';

		print $tableData;
	break;
	case 'getMessageDetails':

		/*Pull Complete Message Data*/
		$msgId = (int)$_POST['msgId'];

		$sqlMsg = 'SELECT `message_text`, `patientId`, `message_sender_id`, `message_subject`, `sent_to_groups` FROM `user_messages` WHERE `user_message_id`='.$msgId;
		$respMsg = imw_query($sqlMsg);

		$responseData = '';

        $users_arr = $msgConsoleObj->get_users();
		$usernameArr = array();
		foreach($users_arr as $user_row)
		{
			$id = $user_row['id'];
			$name = $user_row['patient_name'];
			$usernameArr[$id] = $name;
		}

		if( $respMsg && imw_num_rows($respMsg) > 0)
		{
			$row = imw_fetch_assoc($respMsg);

			$messageText	= nl2br(html_entity_decode($row['message_text']));
			$patientId		= $row['patientId'];

			$patientData  = array();
			if( $patientId > 0)
				$patientData	= $msgConsoleObj->get_patient_more_info($patientId);

			/*Patient Image*/
			$dir_path	=	$GLOBALS['file_upload_dir'];
			if(	$patientData['p_imagename'] != '' )
				$patientData['p_imagename'] = $dir_path.$patientData['p_imagename'];

			if( trim($patientData['p_imagename']) == '' || !file_exists($patientData['p_imagename']) )
				$patientData['p_imagename'] = $GLOBALS['webroot'].'/library/images/no_image_found.png';

			/*Address*/
			$pt_address = $patientData['street'];
			if( trim($patientData['street2']) != '' )
				$pt_address.=', '.trim($patientData['street2']);

			$csz = '';
			$csz .= $patientData['city'];
			if( $csz != ' ' )
				$csz .= ', '.$patientData['state'];
			else
				$csz = $patientData['state'];

			if( $csz != '' && trim($patientData['postal_code'])!='' )
				$csz .= ' - '.$patientData['postal_code'];
			else
				$csz = $patientData['postal_code'];

			if( $csz != '' && $patientData['zip_ext']!='' )
				$csz .= '-'.$patientData['zip_ext'];

			/*Phone*/
			if( $patientData['phone_home'] != '' )
				$patientData['phone_home'] = '<div class="col-sm-2 pt5"><strong>Home</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">'.str_replace(" ","",core_phone_format($patientData['phone_home'])).'</div><div class="clearfix"></div>';

			if( $patientData['phone_biz'] != '' )
				$patientData['phone_biz'] = '<div class="col-sm-2 pt5"><strong>Work</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt65">'.str_replace(" ","",core_phone_format($patientData['phone_biz'])).'</div><div class="clearfix"></div>';

			if( $patientData['phone_cell'] != '' )
				$patientData['phone_cell'] = '<div class="col-sm-2 pt5"><strong>Cell</strong></div><div class="col-sm-1 pt5"> : </div><div class="col-sm-9 pt5">'.str_replace(" ","",core_phone_format($patientData['phone_cell'])).'</div><div class="clearfix"></div>';

			/*Appointment Details*/
			$pt_appt = $msgConsoleObj->get_pt_appt($patientData['id']);
			if( $pt_appt['appt_dt_time'] != '' )
			{
				$facility_name = $pt_appt['facility_name'];
				if( str_word_count($facility_name) != 1 )
				{
					$arr_facility_name = str_word_count($facility_name,1);
					$tmp_arr_fac_name = '';
					foreach($arr_facility_name as $val)
					{
						$tmp_arr_fac_name .= substr($val,0,1);
					}
					$facility_name = strtoupper($tmp_arr_fac_name);
				}
				$appt_data = $pt_appt['phy_init_name'].' / '.$pt_appt['appt_dt_time'].' / '.$facility_name;
			}
			else
				$appt_data = 'N/A';

			$reply = show_tooltip('Reply', 'top');
			$replyAll = show_tooltip('Reply All', 'top');
			$forward = show_tooltip('Forward', 'top');
			$completed = show_tooltip('Task Completed', 'top');
			$deleted = show_tooltip('Delete', 'top');

			$dataHeight = (int)$_SESSION['wn_height']-720;

			$subject = urlencode($row['message_subject']);

            //Create recipients list
            $temp_users=array();
            $temp_users = explode('<br>', html_entity_decode($row['sent_to_groups']));
            $group_list = array_shift($temp_users);
            $Ugroup_arr = explode(',', $group_list);
            $send_to_list='';
            if(!empty($Ugroup_arr)) {
                foreach($Ugroup_arr as $Ugroup) {
                    $tugrp='';
                    if(!empty($Ugroup)) {
                        $tUgroup=explode('--', trim($Ugroup));
                        if(count($tUgroup)>0) {
                            $tugrp.=$tUgroup[0].'; ';
                        } else {
                            $tugrp.=trim($Ugroup).'; ';
                        }

                        $send_to_list.=$tugrp;
                    }
                }
            }

            if(count($temp_users)>0)
            $send_to_list = $send_to_list.implode('; ',$temp_users).';';
            $send_to_list=trim($send_to_list);
            $tmpPatienRow = '';
			if( $patientId > 0 ) {
			$tmpPatienRow = '

			<!--Patient Details-->
			<div class="ptcommu">
				<div class="row">
					<div class="col-sm-5">
						<div class="ptdtl">
							<figure>
								<img src="'.$patientData['p_imagename'].'" alt="" style="width:76px;" />
							</figure>
							<div><strong><span class="text_purple pointer" onClick="LoadWorkView(\''.$patientId.'\');">'.$patientData['lname'].', '.$patientData['fname'].' '.$patientData['mname'].' - '.$patientData['id'].'</span></strong></div>
							 <div class="clearfix"></div>
							<div class="row">
								<div class="col-sm-6"><strong>Gender</strong>   :	'.$patientData['sex'].'</div>
								<div class="col-sm-6"><strong>DOB</strong>   :    '.$patientData['DOB'].'</div>
							</div>

							<div class="clearfix"></div>

							<div class="row">
								<div class="col-sm-12"><strong>Address</strong> : '.$pt_address.' '.$csz.'</div>
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
								<div class="row continfo">
									<div class="col-sm-2">
										<strong>Appt</strong>
									</div>
									<div class="col-sm-1"> : </div>
									<div class="col-sm-9">'.$appt_data.'</div>
								</div>
							</div>
						</div>
						<!-- <div class="ptinfara">
							<div class="row">
								<div class="col-sm-5 ptcomubut">
									<div class="checkbox">
										<input type="checkbox" checked readonly>
										<label style="padding-left:20px" for="ptVerbalCommCheck">
											 Patient Verbal Communication
										</label>
									</div>
								</div>
							</div>
						</div> -->
						<div class="clearfix"></div>
					</div>
				</div>
			</div>

			<div class="clearfix"></div>';

			}

$responseData .=  '
			<!--Message Action icons-->
			<div class="mesoption">
				<img src="'.$GLOBALS['webroot'].'/library/images/mes1.jpg" '.$reply.' onclick="loadReplyForm(\'msg_sender_id='.$row['message_sender_id'].'&replied_id='.$msgId.'&message_edit_id='.$msgId.'&subject='.$subject.'&action=reply\')" alt="" id="replyBtn_'.$msgId.'" />
				<img src="'.$GLOBALS['webroot'].'/library/images/mes2.jpg" '.$replyAll.' onclick="loadReplyForm(\'msg_sender_id='.$row['message_sender_id'].'&replied_id='.$msgId.'&message_edit_id='.$msgId.'&subject='.$subject.'&action=replyAll\')" alt=""/>
				<img src="'.$GLOBALS['webroot'].'/library/images/mes3.jpg" '.$forward.' onclick="loadReplyForm(\'message_edit_id='.$msgId.'&action=forward\')" alt="" id="forwardBtn_'.$msgId.'" />
				<img src="'.$GLOBALS['webroot'].'/library/images/mes4.jpg" '.$completed.' onclick="msg_completed('.$msgId.','.$msgConsoleObj->operator_id.'); do_action(\'ptcomm\', \'msg_my_inbox\');" alt=""/>
			</div>

			<div class="clearfix"></div>
            <div class="to_from_details pd5">
                <div class="row">
					<div class="col-sm-12"><strong>Recipients :</strong> '.$send_to_list.'</div>
                    <div class="col-sm-12"><strong>Sender :</strong> '.$usernameArr[$row['message_sender_id']].';</div>
                </div>
            </div>
            <div class="clearfix"></div>
			'.$tmpPatienRow.'
			<!--Complete Message Data-->
			<div>
				<div class="postmessage">
					<div class="scroll-content mCustomScrollbar" style="height:'.$dataHeight.'px;">
						<span class="mrMsgText">'.$messageText.'</span>
					</div>
				</div>
			</div>';

		}


		print $responseData;

	break;
	case 'del_msg':
	case 'del_messages':
		$msg_id = $_REQUEST['msg_id'];
		$del_type = $_REQUEST['del_type'];
		$folder_id = (isset($_REQUEST['folder_id']) && trim($_REQUEST['folder_id']) !== '' && $_REQUEST['folder_id'] != 'undefined') ? $_REQUEST['folder_id'] : (bool) false;

		if($del_type==1){
			$reqQry = 'UPDATE user_messages SET del_status = "1" WHERE user_message_id IN ('.$msg_id.')';
		}else if($del_type==2){//----DELETE FUTURE ALERT
			$reqQry = 'UPDATE user_messages SET del_status = "1",receiver_delete = "1",del_future_alert=1 WHERE user_message_id IN ('.$msg_id.')';
		}
		else{
			$reqQry = 'UPDATE user_messages SET receiver_delete = 1 WHERE user_message_id IN ('.$msg_id.')';
		}
        if($folder_id) {
            $reqQry = 'UPDATE user_messages SET message_status = 0,message_sender_id = "1",del_status = "1",receiver_delete ="1",Pt_Communication=0
                     WHERE user_message_id IN ('.$msg_id.') AND saved_folder_id='.$folder_id.' ';
		}
		$result_status = imw_query($reqQry);
		if($result_status) { echo 'success'; }
	break;
	case 'del_direct':
		$msg_id = $_REQUEST['msg_id'];
		$reqQry = 'UPDATE direct_messages SET del_status = 1 WHERE id IN ('.$msg_id.')';
		$result_status = imw_query($reqQry);
		if($result_status) { echo 'success'; }
	break;
	case 'un_del_msg':
		$msg_id = $_REQUEST['msg_id'];
		$del_type = $_REQUEST['del_type'];
		$reqQry = 'UPDATE user_messages
					SET del_status = IF(message_sender_id = "'.$_SESSION['authId'].'" && del_status = "1",0,IF(del_status=1,1,0)),
						receiver_delete = IF(message_to = "'.$_SESSION['authId'].'" && receiver_delete = "1",0,IF(receiver_delete=1,1,0)),
						del_future_alert= IF(del_future_alert=1,0,del_future_alert)
					WHERE user_message_id IN ('.$msg_id.')';
		$result_status = imw_query($reqQry);
		if($result_status) { echo 'success'; }
	break;
	case 'mark_as_unread':
		$msg_id = $_REQUEST['msg_id'];
		$reqQry = 'UPDATE user_messages SET message_read_status = 0 WHERE user_message_id = '.$msg_id;
		$result_status = imw_query($reqQry);
		if($result_status) { echo 'success'; }
	break;
	case 'msg_completed':
		$msg_id = $_REQUEST['msg_id'];
		$review_by = $_REQUEST['review_by'];
		$reqQry = 'UPDATE user_messages SET message_read_status = 1, message_status = 1, message_completed_date = CURDATE(), msg_completed_by = "'.$review_by.'" WHERE user_message_id = '.$msg_id;
		$result_status = imw_query($reqQry);
		if($result_status) { echo 'success'; }
	break;
	case 'tests_tasks':
		$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'scan_upload';
		unset($msgConsoleObj->limit_upto);
		switch($filter)
		{
			case 'scan_upload':
				$result_data_arr = $msgConsoleObj->get_tests_tasks('scan_upload_tasks');
				$tableElem ='';
				if(count($result_data_arr)>0)
				{
					$tableElem .= '
                        <form name="task_form" id="task_form" method="POST" onSubmit="return false;" class="table-responsive">
                            <div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
									<table class="table table-bordered sortable"  style="margin-bottom:0px">
									<thead>
										<tr class="purple_bar">
                                            <th width="5%" class="text-center sorttable_nosort">
                                                <div class="checkbox">
                                                    <input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
                                                    <label for="checkbox">&nbsp;</label>
                                                </div>
                                            </th>
											<th width="20%">Patient Name</th>
											<th width="10%">Folder</th>
											<th width="15%">Document Name</th>
											<th width="20%">Date Scan/Upload</th>
											<th width="20%">Comment</th>
											<th width="10%" class="title-row">Date Reviewed</th>
										</tr>
									</thead>
									<tbody>
								';
					$saveBtnVisi = 'hidden';
					foreach($result_data_arr as $key => $val_arr)
					{
					/**********************/
						$scan_doc_id 	= $val_arr['scan_doc_id'];
						$task_status 	= $val_arr['task_status'];
						//$doc_title 		= '<a href="javascript:;" title="Click to Review" class="a_clr1" onClick="javascript:large(\''.$scan_doc_id.'\',\''.$doc_type.'\');">'.$val_arr['doc_title'].'</a>';

						$pdf_url 		= $val_arr['pdf_url'];
						$doc_type 		= $val_arr['doc_type'];
						$doc_upload_type= $val_arr['doc_upload_type'];
						$docDt = $doc_comment = $task_review_date_show = "";
						$doc_title 		= '<span class="text_purple" data-toggle="modal" data-target="#image_popup_'.$scan_doc_id.'" onClick="javascript:large(\''.$scan_doc_id.'\',\''.$doc_type.'\');">'.$val_arr['doc_title'].'</span>';

						if(!$doc_title) { $doc_title = $pdf_url; }
						if($task_status=='0' && $task_status!=''){$toReview=true;}
						if(getNumber(substr($val_arr['doc_upload_date'],0,10))!="00000000") {
							$docDt = $val_arr['doc_upload_date'];
						}
						$doc_comment = $val_arr['doc_comments'];
						$doc_comment_new = $doc_comment;
						if(getNumber(substr($val_arr['task_review_date_new'],0,10))!="00000000") {
							$task_review_date_show = $val_arr['task_review_date_new'];
							$doc_comment_new = '<textarea style="width:96%; height:20px; margin:0px;" name="comment_'.$scan_doc_id.'" id="comment_'.$scan_doc_id.'" class="form-control">'.$doc_comment.'</textarea><input type="hidden" name="hidd_comment['.$scan_doc_id.']" id="hidd_comment'.$scan_doc_id.'" value="'.$doc_upload_type.'" >';
							$saveBtnVisi = 'visible';
						}
						$patient_name = $val_arr['patient_name'];
						//$patient_name = '<a class="a_clr1" title="Click to load Chart" href="javascript:;" onclick="LoadWorkView(\''.$val_arr['pid'].'\');">'.$patient_name.'</a>';
						$patient_name = '<span class="text_purple" onclick="LoadWorkView(\''.$val_arr['pid'].'\')">'.$patient_name.'</span>';
					/***********************/
						$tableElem .= '<tr class="even-odd-test-task">
											<td class="text-center">
											<div class="checkbox">
												<input name="message_id[]" id="message_id_'.$scan_doc_id.'" type="checkbox" value="msg_'.$scan_doc_id.'" autocomplete="off" class="chk_record">
												<label for="message_id_'.$scan_doc_id.'">&nbsp;</label>
											</div></td>
											<td>'.$patient_name.'</td>
											<td>'.$val_arr['folder_name'].'</td>
											<td>'.$doc_title.'</td>
											<td>'.$docDt.'</td>
											<td>'.$doc_comment_new.'</td>
											<td>'.$task_review_date_show.'</td>
									   </tr>
										';

					}


					$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5">
					<button onClick="del_selected_tasks();" type="button" name="Delete_Record" title="Delete Selected" value="Delete" class="btn btn-danger" id="Delete_Record">Delete Selected</button>
					<button class="btn btn-default" onclick="window.focus();window.print();" name="Print_window" value="Print">
            		<span class="glyphicon glyphicon-print"></span> Print</button>
					<span style="visibility:'.$saveBtnVisi.'">
                        <input type="hidden" name="task_form_submit" id="form_submit" value="1" >
                        <button onClick="save_selected_scan_tasks();" class="btn btn-success" value="Save">Save</button>
						</span>
					</div>
					</form>';
				}
				else
				{
					$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No Scan/Upload task pending.</div></div>';
				}
				echo $tableElem;
			break;
			case 'tests':
				$result_data_arr = $msgConsoleObj->get_tests_tasks('tests');
				$tableElem = '';
				if(count($result_data_arr)>0)
				{
					$tableElem1 = '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
					<table class="table table-bordered table-striped table-hover sortable" style="margin-bottom:0px" id="console_data">
							<thead>
								<tr class="purple_bar">
									<th width="5%" class="text-center sorttable_nosort">
										<div class="checkbox">
											<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
											<label for="checkbox">&nbsp;</label>
										</div>
									</th>
									<th width="10%">DOS</th>
									<th width="20%">Patient Name</th>
									<th width="10%">Test Name</th>
									<th width="55%" class="title-row">Comments</th>
								</tr>
							</thead>
							<tbody>
						';
					$ar_pt_info = array();
					foreach($result_data_arr as $key => $val_arr)
					{

						$pt_id ="";
						if(isset($val_arr['patient_id'])){
							$pt_id = $val_arr['patient_id'];
						}else if(isset($val_arr['patientId'])){
							$pt_id = $val_arr['patientId'];
						}
						if(empty($pt_id)){ continue; }

						if(isset($ar_pt_info[$pt_id])){
						$pt_arr = $ar_pt_info[$pt_id];
						}else{
						$pt_arr = $msgConsoleObj->get_patient_more_info($pt_id, " concat(lname,', ',fname,' ',mname,' - ',id) as patient_name, providerID ");
						$ar_pt_info[$pt_id] = $pt_arr;
						}

						if(!$pt_arr || count($pt_arr)<=0){ continue; }

						$pt_name = $pt_arr["patient_name"];
						$pt_prov_id=$pt_arr["providerID"];

						$ordrby = $val_arr['ordrby'];
						if(empty($ordrby)){ 	if(!empty($pt_prov_id) && $pt_prov_id!=$msgConsoleObj->operator_id){ continue; }  }

						if(trim($val_arr['comments'])=="!~!"){ $val_arr['comments']=""; }
						$val_arr['test_table_name'] = $val_arr['testName'];
						if($val_arr['testName']=='discexternal') $val_arr['test_table_name']='disc_external';

						$tableElem .= '<tr class="even-odd-test-task">
											<td class="text-center">
											<div class="checkbox">
												<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['testName'].'-'.$val_arr['main_id'].'" class="chk_record">
												<label for="checkbox'.$key.'">&nbsp;</label>
											</div>
											</td>
											<td>'.$val_arr['taskDate'].'</td>
											<td><span class="text_purple" onclick="loadPtThenTEST(\''.$pt_id.'\',\''.$val_arr['test_js_key'].'\',\''.$val_arr['main_id'].'\')">'.$pt_name.'</span></td>
											<td>'.strtoupper($val_arr['testDesc']).'</td>
											<td>'.$val_arr['comments'].'</td>
										</tr>';
					}
				}

				if(!empty($tableElem)){
					$tableElem = $tableElem1.$tableElem;
					$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5">
						<button onclick="del_messages(\'tests\')" class="btn btn-danger">Delete</button>
						<button onclick="del_messages(\'save_tests\')" class="btn btn-success">Save</button>
						<button class="btn btn-default" onclick="window.open(\'test_tasks_print.php\',\'width=1200,height=550,top=10,left=40,scrollbars=yes,resizable=yes\');">
            			<span class="glyphicon glyphicon-print"></span> Print</button></div>
					';
				}else
				{
					$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No Test pending.</div></div>';
				}
				echo $tableElem;
			break;
			case 'del_scan_tasks':
				$del_tasks_id = (isset($_REQUEST['del_tasks_id']) && trim($_REQUEST['del_tasks_id'])!='') ? trim($_REQUEST['del_tasks_id']) : '';
				$del_tasks_id_arr = explode(',',$del_tasks_id);
				$taskId = array();
				for($i=0;$i<count($del_tasks_id_arr);$i++){
					$message_id_arr = explode('_',$del_tasks_id_arr[$i]);
					$taskId[] = "'".$message_id_arr[1]."'";

				}
				$taskIdStr = join(',',$taskId);
				if($taskIdStr!=''){
					$qry = "UPDATE ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl SET task_status = '2' where scan_doc_id in($taskIdStr)";
					$result = imw_query($qry);
					if($result){die('taskdeleted');}
				}
			break;

			case 'acc_notes':
				$authId = (isset($_SESSION['authId']) && empty($_SESSION['authId']) == false) ? $_SESSION['authId'] : '';
				$htmlStr = '';

				if(empty($authId) == false){
					//Fetching Results
					$chkQry = imw_query('SELECT
						usr.fname,
						usr.mname,
						usr.lname,
						pay.commentId,
						pay.encComments,
						pay.task_done,
						pay.task_assign_by,
						DATE(pay.task_assign_date) as task_date
					FROM paymentscomment pay
					LEFT JOIN users usr ON (usr.id = pay.task_assign_by)
					WHERE
						usr.delete_status = 0 AND
						pay.task_assign_for IN ('.$authId.') AND
						pay.task_done = 1 AND
						pay.task_assign = 2
				  ');

				  $htmlHeadStr = '
					  <thead>
						  <tr class="purple_bar">
							  <th class="text-center sorttable_nosort" width="5%">
								  <div class="checkbox">
									  <input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
									  <label for="checkbox">&nbsp;</label>
								  </div>
							  </th>
							  <th>Assigned Task</th>
							  <th width="20%">Assigned By</th>
						  </tr>
					  </thead>';

					$htmlStr = '';
					$emptyRec = true;
					if($chkQry && imw_num_rows($chkQry) > 0){

						while($rowFetch = imw_fetch_assoc($chkQry)){
							$id = $rowFetch['commentId'];
							$htmlStr .= '<tr class="even-odd-test-task">';
							$htmlStr .= '
							<td width="5%" class="text-center">
								<div class="checkbox">
									<input id="checkbox'.$id.'" type="checkbox" name="chk_record[]" value="'.$id.'" class="chk_record">
									<label for="checkbox'.$id.'">&nbsp;</label>
								</div>
							</td>';
							$htmlStr .= '<td>'.$rowFetch['encComments'].'</td>';
							$phyName = core_name_format($rowFetch['lname'], $rowFetch['fname'], $rowFetch['mname']);
							$htmlStr .= '<td width="20%">'.$phyName.'</td>';

							$htmlStr .= '</tr>';
						}

						if(empty($htmlStr) == false) $htmlStr = '<tbody>'.$htmlStr.'</tbody>';
						$emptyRec = false;
					}else{
						$htmlStr .= '<tbody><tr><td colspan="3" class="text-center">No task assigned</td></tr></tbody>';
						$emptyRec = true;
					}

					$finalHtml = '';
					if(empty($htmlStr) == false){
						$finalHtml = '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
						<table class="table table-bordered table-striped table-hover sortable" id="console_data">'.$htmlHeadStr.$htmlStr.'</table></div>';
					}

					if(empty($finalHtml) == false ){
						if($emptyRec == false) $finalHtml = $finalHtml.'<div class="col-sm-12 text-center pt5 pdb5"><button onclick="del_messages(\'complete_task\')" class="btn btn-success">Mark as Done</button></div>';
						$htmlStr = $finalHtml;
					}
				}

				echo $htmlStr;
			break;

		}
	break;
	case 'del_tests':
		$test_inbox_id = explode(',',$_REQUEST['del_tests_vals']);
		for($i=0;$i<=count($test_inbox_id);$i++){
			$id_arr = explode('-',$test_inbox_id[$i]);
			$tableName = $id_arr[0];
			$id = $id_arr[1];
			$table_id = $tableName.'_id';
			$filed = 'phyName';
			if($tableName == 'ivfa'){
				$table_id = 'vf_id';
				$filed = 'phy';
			}
			if($tableName == 'icg'){
				$table_id = 'icg_id';
				$filed = 'phy';
			}
			if($tableName == 'discexternal' || $tableName == 'disc_external'){
				$table_id = 'disc_id';
				$tableName = 'disc_external';
			}
			if($tableName == 'topography'){
				$table_id = 'topo_id';
			}
			if($tableName == 'test_gdx'){
				$table_id = 'gdx_id';
			}
			if($tableName == 'surgical_tbl')
			{
				$table_id = 'surgical_id';
				$filed = 'performedByPhyOD';
			}
			if($tableName == 'iol_master_tbl')
			{
				$table_id = 'iol_master_id';
				$filed = 'signedById';
			}
			echo $qry = "update $tableName set $filed = '$auth_Id',finished = '1'
					where $table_id = '$id'";
			imw_query($qry);
		}
	break;
	case 'save_tests':
		$test_inbox_id = explode(',',$_REQUEST['save_tests_vals']);

		for($i=0;$i<count($test_inbox_id);$i++){
			$id_arr = explode('-',$test_inbox_id[$i]);
			$tableName = $id_arr[0];
			$id = $id_arr[1];
			$table_id = $tableName.'_id';
			$filed = 'phyName';
			if($tableName == 'ivfa'){
				$table_id = 'vf_id';
				$filed = 'phy';
			}
			if($tableName == 'discexternal'){
				$table_id = 'disc_id';
				$tableName = 'disc_external';
			}
			if($tableName == 'topography'){
				$table_id = 'topo_id';
			}
			if($tableName == 'test_gdx'){
				$table_id = 'gdx_id';
			}
			if($tableName == 'surgical_tbl'){
				$table_id = 'surgical_id';
				$filed = 'signedById';
			}
			if($tableName == 'icg'){
				$filed = 'phy';
			}
			if($tableName == 'iol_master_tbl')
			{
				$table_id = 'iol_master_id';
				$filed = 'signedById';
			}
			$updateDataArr = array();
			$updateDataArr[$filed] = $msgConsoleObj->operator_id;

			UpdateRecords($id,$table_id,$updateDataArr,$tableName);
		}
	break;
	case 'smart_phrases':

		$tableElem = '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="comp-tasks" style="height:'.($_SESSION['wn_height']-344).'px">';

		$result_data_arr = $msgConsoleObj->get_smart_phrases();
		if(count($result_data_arr)>0)
		{
			$tableElem .= '<table class="table table-bordered" style="margin-bottom:0">
							<thead>
								<tr class="purple_bar">
									<th class="text-center" style="width:80px;">
										<div class="checkbox">
											<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
											<label for="checkbox">&nbsp;</label>
										</div>
									</th>
									<th style="width:auto;">SMART PHRASES</th>
									<th style="width:170px;">DATE AND TIME</th>
									<th style="width:60px;">ACTION</th>
								</tr>
							</thead>
							<tbody>
						';
			foreach($result_data_arr as $key => $val_arr)
			{
				$tableElem .= '<tr class="even-odd-resp-person" id="phraseRow_'.$val_arr['phrase_id'].'">
									<td class="text-center">
										<div class="checkbox">
											<input id="phrase_'.$val_arr['phrase_id'].'" type="checkbox" value="'.$val_arr['phrase_id'].'" class="chk_record smart_phrase_chkbx">
											<label for="phrase_'.$val_arr['phrase_id'].'">&nbsp;</label>
										</div>
									</td>
									<td class="phraseText">'.$val_arr['phrase'].'</td>
									<td style="min-width:150px">'.$val_arr['date_time'].'</td>
									<td style="min-width:80px" class="text-center">
										<!--a href="smart_phrase_frm.php?editid='.$val_arr['phrase_id'].'" target="console_form" alt="Edit Record" title="Edit Record"><img src="'.$GLOBALS['webroot'].'/library/images/edit.png" class="edit_smart_phrase"></a-->
										<img src="'.$GLOBALS['webroot'].'/library/images/edit.png" class="edit_smart_phrase" title="Edit Record" onclick="edit_phrase('.$val_arr['phrase_id'].');" />
									</td>
								</tr>';
			}
			$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5"><button class="btn btn-danger" onClick="del_phrase();">Delete</button>';
		}
		else
		{
			$tableElem .= '<div class="alert alert-danger">No Record Found.</div>';
		}

		$tableElem .= '</div>';

		$authUserID = $_SESSION['authUserID'];

	/*Edit Permission Check*/
		$sql=imw_query("SELECT * FROM users WHERE id='$authUserID'");
		$row = imw_fetch_assoc($sql);
		$providerType = 0;
		if($row != false)
		{
			//User Type
			$providerType = $msgConsoleObj->operator_type;
		}

		$accessEdit = 'true';
		if( ($providerType != 1) && (core_check_privilege(array("priv_admin")) == false) )
		{
			$accessEdit = 'false';
		}
		else
		{
$tableElem .= '
<div id="phraseModal" class="modal moadl-sm" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Send Phrase</h4>
			</div>
			<div class="modal-body">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-1">
							<label for="phrase_data">Phrase</label>
						</div>
						<div class="col-sm-11">
							<textarea name="phrase_data" id="phrase_data" class="form-control" style="height:100px !important;"></textarea>
							<input type="hidden" name="phrase_data_id" id="phrase_data_id" value="" />
						</div>
					</div>
				</div>
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<div class="row">
					<div id="divBtnCont" class="col-sm-12 text-center">
						<button type="button" class="btn btn-success" onclick="submitPhrase();">Save</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>';

		}
	/*End Edit Permission Check*/

$tableElem .= '
<script type="text/javascript">
	var accessEdit = '.$accessEdit.';
</script>';


		echo $tableElem;
	break;
	case 'smart_phrases_edit':

		$phraseData = $_POST['phraseData'];
		$phraseDataId = (int)$_POST['phraseDataId'];
		$authUserID = (int)$_SESSION['authUserID'];

		$response = '';

		/*Check for Duplicate Phrase*/
		$duplicate = "Select * from common_phrases where phrase='$phraseData' AND (providerID='0' OR providerID='$authUserID') AND `phrase_id`!=".$phraseDataId;
		$duplicateResp = imw_query($duplicate);
		if( imw_num_rows($duplicateResp) > 0)
		{
			$response = 'duplicate';
		}
		else
		{
			$where = '';
			if($phraseDataId>0)
			{
				$phrseSql = 'UPDATE ';
				$where = ' WHERE `phrase_id`='.$phraseDataId;
			}
			else{
				$phrseSql = 'INSERT INTO ';
			}
			$phraseData = mb_convert_encoding($phraseData,'HTML-ENTITIES', 'UTF-8');
			$phrseSql .= "`common_phrases`
						SET
							`phrase`='".addslashes($phraseData)."',
							`providerID`=".$authUserID.",
							date_time='".date("Y-m-d H:i:s")."'".$where;
			imw_query($phrseSql);

			$response = 'success';
		}

		echo $response;
	break;
	case 'del_phrase':
		$phrase_id = implode(',', $_REQUEST['phrase_id']);
		$reqQry = 'DELETE FROM common_phrases WHERE phrase_id IN('.$phrase_id.')';
		$result = imw_query($reqQry);
		if($result){ echo 'success'; }
	break;
	case 'ap_policies':
		include_once("anp_policies.php");
	break;
	case 'forms_letters':
		$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'msg_my_inbox';
		$call_case = '';
		$load_buttons = '<button class="btn btn-success" onClick="del_messages(\'consent\');">Sign Selected</button>';
		switch($filter)
		{
			case 'consent_forms':
				$result_data_arr = $msgConsoleObj->get_forms_letters('consent_forms');
				$tableElem = '';
				if(count($result_data_arr)>0)
				{
					$tableElem_1 .= '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
					<table class="table table-bordered table-striped table-hover"  style="margin-bottom:0px;" id="console_data">
					<thead>
						<tr class="purple_bar">
							<th class="text-center" width="5%">
								<div class="checkbox">
									<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
									<label for="checkbox">&nbsp;</label>
								</div>
							</th>
							<th>Patient</th>
							<th>Consent Form</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>';
					$tableElem='';
					foreach($result_data_arr as $key=>$val_arr)
					{//pcficonsent_id
						$rcn = $key+1;

						//--
						//Check procedure notes related consent form
						$chart_procedure_id = $val_arr['chart_procedure_id'];
						if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id)){
							//continue;
							//COMMENTED ABOVE LINE. iT WAS SKIPPING TO SHOW PENDING LETTERS UNTIL CHART IS FINALIZED.
						}
						//--

						$tableElem .= '<tr class="even-odd-test-task">
							<td class="text-center">
							<div class="checkbox">
								<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['chscon_id'].'" class="chk_record">
								<label for="checkbox'.$key.'">&nbsp;</label>
							</div></td>
							<td><span class="text_purple" onclick="LoadWorkView(\''.$val_arr['pdid'].'\')">'.$val_arr['patient_name'].'</span></td>
							<td><a target="_blank" href="../patient_info/consent_forms/print_consent_form.php?consent_form_id='.$val_arr['pcficonsent_id'].'&consent=yes&form_information_id='.$val_arr['chscon_id'].'" class="text_purple"><span class=""><img src="'.$GLOBALS['webroot'].'/library/images/pdf_icon[1].png" alt=""/></span>&nbsp;'.$val_arr['form_name'].'</a></td>
							<td>'.$val_arr['chscre_on'].'</td>
						</tr>';
					}

					if(!empty($tableElem)){
						$tableElem = $tableElem_1.$tableElem;
						$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5" id="page_buttons">'.$load_buttons.'</div>';
					}else{
						$tableElem = '<div class="alert alert-danger">No Consent Form pending for signature.</div>';
					}


				}
				else
				{
					$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No Consent Form pending for signature.</div></div>';
				}
				echo $tableElem;
			break;
			case 'sx_consent_forms':
				$result_data_arr = $msgConsoleObj->get_forms_letters('sx_consent_forms');
				$tableElem = '';
				$load_buttons = '<button class="btn btn-success" onClick="del_messages(\'sx_consent\');">Sign Selected</button></div>';
				if(count($result_data_arr)>0)
				{
					$tableElem .= '
					<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
					<table class="table table-bordered table-striped table-hover" id="console_data" style="margin-bottom:0px;" >
						<thead>
							<tr class="purple_bar">
								<th class="text-center" width="5%">
									<div class="checkbox">
										<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
										<label for="checkbox">&nbsp;</label>
									</div>
								</th>
								<th>Patient</th>
								<th>Sx Consent Form</th>
								<th>Date</th>
							</tr>
						</thead>
						<tbody>
					';
					foreach($result_data_arr as $key=>$val_arr)
					{
						$rcn = $key+1;
						$tableElem .= '<tr class="even-odd-test-task">
							<td class="text-center">
							<div class="checkbox">
								<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['chssxcon_id'].'" class="chk_record">
								<label for="checkbox'.$key.'">&nbsp;</label>
							</div>
							</td>
							<td><span class="text_purple" onclick="LoadWorkView(\''.$val_arr['pdid'].'\')">'.$val_arr['patient_name'].'</span></td>
							<td><a target="_blank" href="../patient_info/surgery_consent_forms/print_consent_form_surgery.php?consent_form_id='.$val_arr['form_info_id'].'&form_information_id='.$val_arr['sxconsent_id'].'" class="text_purple"><span class=""><img src="'.$GLOBALS['webroot'].'/library/images/pdf_icon[1].png" alt=""/></span>&nbsp;'.$val_arr['form_name'].'</a></td>
							<td>'.$val_arr['chscre_on'].'</td>
						</tr>';
					}
					$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5" id="page_buttons">'.$load_buttons.'</div>';
				}
				else
				{
					$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No Surgery Consent Form pending for signature.</div></div>';
				}
				echo $tableElem;
			break;
			case 'op_notes':
				$result_data_arr = $msgConsoleObj->get_forms_letters('op_notes');
				$tableElem = '';
				$load_buttons = '<button class="btn btn-success dff_button" onClick="del_messages(\'op_notes\');">Sign Selected</button>';
				if(count($result_data_arr)>0)
				{
					$tableElem_1 .= '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
					<table class="table table-bordered table-striped table-hover" id="console_data" style="margin-bottom:0px;" >
					<thead>
						<tr class="purple_bar">
							<th class="text-center" width="5%">
								<div class="checkbox">
									<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
									<label for="checkbox">&nbsp;</label>
								</div>
							</th>
							<th>Patient</th>
							<th>OP Notes</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
								';
					foreach($result_data_arr as $key=>$val_arr)
					{
						$rcn = $key+1;

						//--
						//Check procedure notes related consent form
						$chart_procedure_id = $val_arr['chart_procedure_id'];
						if(!empty($chart_procedure_id) && !isProcedureNoteFinalized($chart_procedure_id)){
							//continue;
							//COMMENTED ABOVE LINE. iT WAS SKIPPING TO SHOW PENDING LETTERS UNTIL CHART IS FINALIZED.
						}
						//--

						$tableElem .= '
						<tr class="even-odd-test-task">
							<td class="text-center">
							<div class="checkbox">
								<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['pnr_id'].'" class="chk_record">
								<label for="checkbox'.$key.'">&nbsp;</label>
							</div>
							</td>
							<td><span class="text_purple" onclick="LoadWorkView(\''.$val_arr['pdid'].'\')">'.$val_arr['patient_name'].'</span></td>
							<td onClick="openEditor(\'opnote\','.$val_arr['chsid'].','.$val_arr['pdid'].');" class="link_cursor"><a href="javascript:;" class="text_purple" title="Click to edit">'.$val_arr['form_name'].'</a></td>
							<td>'.$val_arr['chscre_on'].'</td>
						</tr>';
					}

					if(!empty($tableElem)){
						$tableElem = $tableElem_1.$tableElem;
						$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5" id="page_buttons">'.$load_buttons.'</div>';
					}else{
						$tableElem = '<div class="pt5 pdl_10 dynamicRightPadding"><div class="alert alert-danger">No OPNote pending for signature.</div></div>';
					}

				}
				else
				{
					$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No OPNote pending for signature.</div></div>';
				}
				echo $tableElem;
			break;
			case 'consult_letters':
				$result_data_arr = $msgConsoleObj->get_forms_letters('consult_letters');
				$tableElem = '';
				$load_buttons = '<button class="btn btn-success dff_button" onClick="del_messages(\'consult_letters\');">Sign Selected</button>';
				if(count($result_data_arr)>0)
				{
					$tableElem .= '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
					<table class="table table-bordered table-striped table-hover" id="console_data" style="margin-bottom:0px;" >
					<thead>
						<tr class="purple_bar">
							<th class="text-center" width="5%">
								<div class="checkbox">
									<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
									<label for="checkbox">&nbsp;</label>
								</div>
							</th>
							<th>Patient</th>
							<th>Consult Letters</th>
							<th>Date</th>
						</tr>
					</thead>
					<tbody>
								';
					foreach($result_data_arr as $key=>$val_arr)
					{
						$rcn = $key+1;
						$tableElem .= '<tr class="even-odd-test-task">
							<td class="text-center">
							<div class="checkbox">
								<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['pclt_id'].'" class="chk_record">
								<label for="checkbox'.$key.'">&nbsp;</label>
							</div>
							</td>
							<td><span class="text_purple" onclick="LoadWorkView(\''.$val_arr['pdid'].'\')">'.$val_arr['patient_name'].'</span></td>
							<td onClick="openEditor(\'consult_letter\','.$val_arr['chsid'].','.$val_arr['pdid'].');" class="link_cursor"><a href="javascript:;" class="text_purple" title="Click to edit">'.$val_arr['form_name'].'</a></td>
							<td>'.$val_arr['chscre_on'].'</td>
						</tr>';
					}
					$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5" id="page_buttons">'.$load_buttons.'</div>';
				}
				else
				{
					$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger">No Consult Letter pending for signature.</div></div>';
				}
				echo $tableElem;
			break;
		}
	break;
	case 'consent_sign_selected':
		$id = $msgConsoleObj->operator_id;
		$chs_ids = $_REQUEST['chs_ids'];
		$saveImg = dirname(__FILE__)."/../common/new_html2pdf/user_id_".$id.".jpg";
		$tblName = "users";
		$pixelFieldName = "sign";
		$idFieldName = "id";
		$imgPath = "";
		$imgNme = "user_id_".$id.".jpg";
		$qry_u="SELECT sign_path from users where id='".$id."'";
		$res_u=imw_query($qry_u);
		$row_u=imw_fetch_assoc($res_u);
		if($row_u['sign_path']){
			$imgNme=$GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$row_u['sign_path'];
			$TData='<img src="'.$imgNme.'" height="80" width="240">';
		}else{
			include($GLOBALS['rootdir']."/library/classes/imgGdFun.php");
			$TData = '<img src="'.$imgNme.'" height="80" width="240">';
		}
		//save physician signature
		$updtQry="UPDATE `patient_consent_form_information` SET
					`consent_form_content_data` = REPLACE(`consent_form_content_data`, '{PHYSICIAN SIGNATURE}', '".$TData."')
					WHERE form_information_id IN(".$chs_ids.")";

		$updtRes = imw_query($updtQry) or die(imw_error());

		$updtQryCHS = "UPDATE consent_hold_sign SET signed='1', signed_by='".$msgConsoleObj->operator_id."', signed_on=NOW()  WHERE consent_id IN(".$chs_ids.")";
		$updtResCHS = imw_query($updtQryCHS) or die(imw_error());
		if($updtResCHS) { echo 'success';}
	break;
	case 'sx_consent_sign_selected':
		$id = $msgConsoleObj->operator_id;
		$chs_ids = $_REQUEST['chs_ids'];
		$tblName = "users";
		$pixelFieldName = "sign";
		$idFieldName = "id";
		$imgPath = "";

		$saveImg = dirname(__FILE__)."/../common/new_html2pdf/user_id_".$id.".jpg";
		$imgNme = "user_id_".$id.".jpg";
		$qry_u="SELECT sign_path from users where id='".$id."'";
		$res_u=imw_query($qry_u);
		$row_u=imw_fetch_assoc($res_u);
		if($row_u['sign_path']){
			$imgNme= $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$row_u['sign_path'];
			$TData='<img src="'.$imgNme.'" height="80" width="240">';
		}else{
			include("../patient_info/complete_pt_rec/imgGd.php");
			$TData = '<img src="'.$imgNme.'" height="80" width="240">';
		}
		//save physician signature
		$updtQry="UPDATE `surgery_consent_filled_form` SET
					`surgery_consent_data` = REPLACE(`surgery_consent_data`, '{PHYSICIAN SIGNATURE}', '".$TData."')
					WHERE surgery_consent_id IN(".$chs_ids.")";
		$updtRes = imw_query($updtQry) or die(imw_error());

		$updtQryCHS = "UPDATE consent_hold_sign SET signed='1', signed_by='".$msgConsoleObj->operator_id."', signed_on=NOW()  WHERE sx_consent_id IN(".$chs_ids.")";
		$updtResCHS = imw_query($updtQryCHS) or die(imw_error());
		if($updtResCHS){ echo 'success'; }
		$chkSxQry = "SELECT surgery_consent_id, consent_template_id, patient_id FROM surgery_consent_filled_form WHERE surgery_consent_id IN(".$chs_ids.")";
		$chkSxRes = imw_query($chkSxQry) or die(imw_error());
		if(imw_num_rows($chkSxRes)>0) {
			while($chkSxRow = imw_fetch_assoc($chkSxRes)) {
				$form_information_id= $chkSxRow["surgery_consent_id"];
				$consent_form_id 	= $chkSxRow["consent_template_id"];
				$patient_id 		= $chkSxRow["patient_id"];
				$comeFrom			= "physician_console";
				include("../patient_info/surgery_consent_forms/print_consent_form_surgery.php");
			}
		}
	break;
	case 'op_notes_sign_selected':
		$srchVal1 = "<div title='replacePhySig'>";
		$srchVal2 = '{PHYSICIAN SIGNATURE}';

		$id = $msgConsoleObj->operator_id;
		$tblName = "users";
		$pixelFieldName = "sign";
		$idFieldName = "id";
		$imgPath = "";
		$saveImg = dirname(__FILE__)."/../common/new_html2pdf/user_id_".$id.".jpg";
		$imgNme = "user_id_".$id.".jpg";
		$qry_u="SELECT sign_path from users where id='".$id."'";
		$res_u=imw_query($qry_u);
		$row_u=imw_fetch_assoc($res_u);
		if($row_u['sign_path']){
			$imgNme=$GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$row_u['sign_path'];
			$TData='<img src="'.$imgNme.'"  height="80" width="240">';
		}else{
			include("../patient_info/complete_pt_rec/imgGd.php");
			$TData = '<img src="'.$imgNme.'" height="80" width="240">';
		}

		$updtQry="UPDATE `pn_reports` SET
					`txt_data` = REPLACE(`txt_data`, '".$srchVal2."', '".$TData."')
					WHERE pn_rep_id IN(".$chs_ids.")";

		$updtRes = imw_query($updtQry) or die(imw_error());

		$updtQryCHS = "UPDATE consent_hold_sign SET signed='1', signed_by='".$msgConsoleObj->operator_id."', signed_on=NOW()  WHERE opnote_id IN(".$chs_ids.")";
		$updtResCHS = imw_query($updtQryCHS) or die(imw_error());
		$cls_notifications->set_doc_hold_status('opnote',$chs_ids);
	break;
	case 'consult_letters_sign_selected':
		$srchVal1 = "<div title='replacePhySig'>";
		$srchVal2 = '{PHYSICIAN SIGNATURE}';

		$id = $msgConsoleObj->operator_id;
		$tblName = "users";
		$pixelFieldName = "sign";
		$idFieldName = "id";
		$imgPath = "";
		$saveImg = dirname(__FILE__)."/../common/new_html2pdf/user_id_".$id.".jpg";
		$imgNme = "user_id_".$id.".jpg";
		$qry_u="SELECT sign_path from users where id='".$id."'";
		$res_u=imw_query($qry_u);
		$row_u=imw_fetch_assoc($res_u);
		if($row_u['sign_path']){
			$imgNme=$GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$row_u['sign_path'];
			$TData='<img src="'.$imgNme.'"  height="80" width="240">';
		}else{
			include("../patient_info/complete_pt_rec/imgGd.php");
			$TData = '<img src="'.$imgNme.'" height="80" width="240">';
		}

		$updtQry="UPDATE `patient_consult_letter_tbl` SET
					`templateData` = REPLACE(`templateData`, '".$srchVal2."', '".$TData."')
					WHERE patient_consult_id IN(".$chs_ids.")";

		$updtRes = imw_query($updtQry) or die(imw_error());

		$updtQryCHS = "UPDATE consent_hold_sign SET signed='1', signed_by='".$msgConsoleObj->operator_id."', signed_on=NOW()  WHERE consult_id IN(".$chs_ids.")";
		$updtResCHS = imw_query($updtQryCHS) or die('Error:'.imw_error());
		$cls_notifications->set_doc_hold_status('consult',$chs_ids);
	break;
	case 'resp_person':
        $page_no=$_REQUEST['page_no'];
		$per_page=$_REQUEST['per_page'];

		$result_data_arr_GET = $msgConsoleObj->get_responsible_person($page_no,$per_page);
        $result_data_arr=$result_data_arr_GET[0];
		$p_link1=$result_data_arr_GET[1];
		$p_link2=$result_data_arr_GET[2];
		$options="";
		for($b=10;$b<=100;$b+=($b>=40)?20:10){
			$selected="";
			if((int)$per_page>0){
				if($per_page==$b){$selected="selected";}
			}else if($b==20){$selected="selected";}
			$options.="<option ".$selected." value='".$b."'>".$b."</option>";
		}



		$users_arr = $msgConsoleObj->get_users();
		$load_buttons = '<button class="btn btn-danger" onClick="del_resp_person();">Delete</button>
						<button class="btn btn-default" onClick="printWindowResposiblePerson();">
            			<span class="glyphicon glyphicon-print"></span> Print</button>';
		$usernameArr = array();
		foreach($users_arr as $user_row)
		{
			$id = $user_row['id'];
			$name = $user_row['lname'].', ';
			$name .= $user_row['fname'].' ';
			$name .= $user_row['mname'];
			if($name[0]==',')
			{
				$name = preg_replace("/, /","",$name);
			}
			$name = trim(ucfirst($name));
			$usernameArr[$id] = $name;
		}

		if(count($result_data_arr)>0)
		{
            /*List Messsage Count*/
			$tableElem .= '<div class="row msgCount">';
				$tableElem .= '<div class="col-sm-6 form-inline">';
					$tableElem .= $p_link1.' Record(s) per page &nbsp;';
					$tableElem .= '<select id="no_per_page" onChange="load_responsible_person(\'\',this.value);" class="form-control minimal" data-width="100%">'.$options.'</select>';
				$tableElem .= '</div>';
                $tableElem .= '<div class="col-sm-6">'.$p_link2;
                $tableElem .= '</div>';
			$tableElem .= '</div>';

			$tableElem .= '
            <div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="resp-person" style="height:'.($_SESSION['wn_height']-340).'px">
			<table class="table table-bordered sortable" style="margin-bottom:0px;">
				<thead>
					<tr class="purple_bar">
						<th width="5%" class="text-center sorttable_nosort" >
							<div class="checkbox">
								<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
                                <label for="checkbox">&nbsp;</label>
							</div>
						</th>
						<th width="5%" class="title-row">Date</th>
						<th width="12%" class="title-row">Subject</th>
						<th width="45%" class="title-row">Task Text</th>
						<th width="12%" class="title-row">Patient Name</th>
						<th width="25%" class="title-row">Assign To</th>
					</tr>
				</thead>
				<tbody>
						';
			$keys = array_keys($result_data_arr);

			foreach($result_data_arr as $key => $val_arr)
			{
				$username = html_entity_decode($val_arr['user_name']);
				$TempPtNameVal = explode(' - ',$val_arr['patient_name']);
				$ptName = trim($val_arr['patient_name']);
				$ptId = trim($TempPtNameVal[1]);
				$ptNameLink = '<span class="text_purple" onclick="LoadWorkView(\''.$ptId.'\')">'.$ptName.'</span>';
				if(is_numeric($sent_to_groups)){
					$username = html_entity_decode($usernameArr[$sent_to_groups]);
				}

				if( strlen($username) > 50 )
				{
					$usernameDisp =  substr($username, 0, 50);
					$usernameTitle = show_tooltip(str_replace('<br>', ', ', $username), 'top');
				}
				else
				{
					$usernameDisp = $username;
					$usernameTitle = '';
				}

				$tableElem .= '
				<tr class="even-odd-resp-person">
					<td class="text-center">
                        <div class="checkbox">
						<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['tb_name'].'-'.$val_arr['msg_id'].'" class="chk_record">
						<label for="checkbox'.$key.'">&nbsp;</label>
                            </div>
					</td>
					<td class="min_width">'.$val_arr['msg_date'].'</td>
					<td class="dont-break-out">'.$val_arr['msg_subject'].'</td>
					<td class="dont-break-out">'.nl2br($val_arr['msg_body']).'</td>
					<td>'.$ptNameLink.'</td>
					<td class="dont-break-out"><span '.$usernameTitle.'>'.$usernameDisp.'</span></td>
				</tr>';
			}
			$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5">'.$load_buttons.'</div>';

		}
		else
		{
			$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger"> No Record Available </div></div>';
		}
		echo $tableElem;
	break;
	case 'load_erx':
		require_once 'erx_result.php';
	break;
	case 'completed_tasks':
		$sub_task=$_REQUEST['sub_task'];
		$final_arr = $msgConsoleObj->get_tests_tasks('completed_tasks',$sub_task);
		$result_data_arr = $final_arr['result_arr'];
		$order_data_arr  = $final_arr['orders_arr'];
		$users_arr = $final_arr['users_arr'];
		$tableElem='';
		$load_buttons = '
		<button class="btn btn-danger" onClick="del_completed_tasks();">Delete</button>
		<button class="btn btn-default" onclick="window.open(\'completed_tasks_print.php\',\'width=1200,height=550,top=10,left=40,scrollbars=yes,resizable=yes\');">
        <span class="glyphicon glyphicon-print"></span> Print</button>';
		if(count($result_data_arr)>0 || count($order_data_arr)>0)
		{
			$tableElem .= '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="comp-tasks" style="height:'.($_SESSION['wn_height']-344).'px">';
			if($sub_task=="" || $sub_task=='comp_tasks'){
				$tableElem .= '
				<table class="table table-bordered" style="margin-bottom:0px"><thead>
					<tr class="purple_bar">
						<th width="5%" class="text-center">
							<div class="checkbox">
								<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
								<label for="checkbox">&nbsp;</label>
							</div>
						</th>
						<th width="15%">Date Assigned</th>
						<th width="15%">Date Completed</th>
						<th width="20%">Patient Name</th>
						<th width="15%">Subject</th>
						<th width="15%">Assigned By</th>
						<th width="20%" class="title-row">Completed By</th>
					</tr>
				</thead>
				<tbody>';
			}else{
				$tableElem .= '<table class="table table-bordered table-striped table-hover"><tbody>';
			}
			if($sub_task=="" || $sub_task=='comp_tasks' || $sub_task=='comp_msg'){

				foreach($result_data_arr as $key => $val_arr)
				{
					$username = $users_arr[$val_arr['performedBy']];
					$TempPtNameVal = explode(' - ',$val_arr['patient_name']);
					$ptName = trim($val_arr['patient_name']);
					$ptId = trim($TempPtNameVal[1]);
                    $ptNameLink = '<span class="text_purple" onclick="LoadWorkView(\''.$ptId.'\')">'.$ptName.'</span>';
					if($val_arr['tb_name']=='user_messages')
					{
						$subject_view = strtoupper($val_arr['message_subject']);
					}
					else
					{
						$subject_view = strtoupper($val_arr['TableName']);
					}

					if($val_arr['tb_name']!='user_messages' &&($sub_task=="" || $sub_task=='comp_tasks')){
					$completed_by = $users_arr[$val_arr['completed_by']];
					$tableElem .= '<tr class="even-odd-comp-task">
									<td class="text-center">
										<div class="checkbox">
											<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['tb_name'].'-'.$val_arr['tid'].'" class="chk_record">
											<label for="checkbox'.$key.'">&nbsp;</label>
										</div>
									</td>
									<td>'.$val_arr['taskDate'].'</td>
									<td>'.$val_arr['cur_date'].'</td>
									<td>'.$ptNameLink.'</td>
									<td>'.$subject_view.'</td>
									<td>'.$username.'</td>
									<td>'.$completed_by.'</td>
								</tr>';
					$comp_task_chk=1;
					}
					if($val_arr['tb_name']=='user_messages' && ($sub_task=="" || $sub_task=='comp_msg')){
						if($val_arr['message_read_status']==1){
							$unread_msg_bold = '';
						}else{
							$unread_msg_bold = ' text12b';
						}
						$tableElem_msg .= '<tr class="even-odd-comp-task">
										<td class="text-center">
											<div class="checkbox">
											<input id="checkbox'.$key.'" type="checkbox" name="chk_record[]" value="'.$val_arr['tb_name'].'-'.$val_arr['tid'].'" class="chk_record">
											<label for="checkbox'.$key.'">&nbsp;</label>
											</div>
										</td>
										<td onclick="open_msg_block(\''.$val_arr['tid'].'\',this,\''.$val_arr['patient_name'].'\');")>'.$val_arr['taskDate'].'</td>
										<td onclick="open_msg_block(\''.$val_arr['tid'].'\',this,\''.$val_arr['patient_name'].'\');")>'.$val_arr['cur_date'].'</td>
										<td>'.$ptNameLink.'</td>
										<td onclick="open_msg_block(\''.$val_arr['tid'].'\',this,\''.$val_arr['patient_name'].'\');") style="cursor:pointer">'.$subject_view.'</td>
										<td onclick="open_msg_block(\''.$val_arr['tid'].'\',this,\''.$val_arr['patient_name'].'\');") style="cursor:pointer">'.$username.'</td>
										<td onclick="open_msg_block(\''.$val_arr['tid'].'\',this,\''.$val_arr['patient_name'].'\');") style="cursor:pointer">'.$val_arr['completed_by_name'].'</td>
									</tr>';
							{
							$tableElem_msg .= '<tr style="display:none" id="open_msg_tr_'.$val_arr['tid'].'" class="tr_msg_details">
											<td colspan="8">
											<div style="width:180px;" class="fr div_tr">';
							$tableElem_msg .='</div>
									<div id="msg_content_viewer">
										<p style="width:90%">'.nl2br(html_entity_decode($val_arr['message_text'])).'</p>
									</div>
								';
							$tableElem_msg .= '</td></tr>';
							}
					}
				}
			}

			if($sub_task=="" || $sub_task=='comp_order_set'){
				$qry = "select * from order_sets order by createdy_on desc";
				$orderSetDetails = $msgConsoleObj->create_array_from_qry($qry);
				$orderSetArr = array();
				for($i=0;$i<count($orderSetDetails);$i++){
					$id = $orderSetDetails[$i]['id'];
					$orderSetArr[$id] = $orderSetDetails[$i];
				}
			}

			if($sub_task=="" || $sub_task=='comp_orders'){
				$qry = "select * from order_details order by created_on desc";
				$ordersQryRes = $msgConsoleObj->create_array_from_qry($qry);
				$ordersDetailsArr = array();
				for($o=0;$o<count($ordersQryRes);$o++){
					$id = $ordersQryRes[$o]['id'];
					$ordersDetailsArr[$id] = $ordersQryRes[$o];
				}
			}

			$previous_primary_set_id = 0;
			$orderSetContentData = '';
			$ordersContentData = '';
			if($sub_task=="" || $sub_task=='comp_order_set' || $sub_task=='comp_orders'){
				$ordersQryRes = $final_arr['orders_arr'];
			}
	for($i=0,$q=1;$i<count($ordersQryRes);$i++){
		$order_set_id = $ordersQryRes[$i]['order_set_id'];
		$patient_name = $ordersQryRes[$i]['lname'].', ';
		$patient_name .= $ordersQryRes[$i]['fname'].' ';
		$patient_name .= $ordersQryRes[$i]['mname'];
		$patient_name = trim(ucfirst($patient_name));
		if($patient_name[0] == ','){
			$patient_name = substr($patient_name,1);
		}
		$patient_name .= ' - '.$ordersQryRes[$i]['patient_id'];
        $ptNameLink = '<span class="text_purple" onclick="LoadWorkView(\''.$ordersQryRes[$i]['patient_id'].'\')">'.$patient_name.'</span>';
		//---  GET ALL ORDER SETS  ---------
		if($order_set_id > 0 && ($sub_task=="" || $sub_task=='comp_order_set')){
			$c_date = $ordersQryRes[$i]['c_date'];
			$modified_date = $ordersQryRes[$i]['m_date'];
			$order_id = $ordersQryRes[$i]['order_id'];
			$main_id = $ordersQryRes[$i]['order_set_associate_details_id'];
			$order_detail_arr = $ordersDetailsArr[$order_id];
			$order_name = $order_detail_arr['name'];
			$logged_provider_id = $ordersQryRes[$i]['logged_provider_id'];
			$provider_name = $users_arr[$logged_provider_id];
			$os_chk_val = 'order_set_associate_chart_notes_details-'.$main_id;

			$primary_set_id = $ordersQryRes[$i]['primary_set_id'];
			if($primary_set_id != $previous_primary_set_id){
				$order_set_arr = $orderSetArr[$order_set_id];
				$orderset_name = $order_set_arr['orderset_name'];
				$orderSetContentData .= '
					<tr class="even-odd-comp-task">
						<td colspan="3">&nbsp;</td>
						<td><b>Order Set</b></td>
						<td>'.$orderset_name.'</td>
						<td>'.$provider_name.'</td>
					</tr>';
				$previous_primary_set_id = $primary_set_id;
			}
			$orderSetContentData .= '
				<tr class="even-odd-comp-task">
					<td class="text-center">
						<div class="checkbox">
						<input id="checkbox'.$i.'" type="checkbox" name="order_set[]" value="order-'.$os_chk_va.'" class="chk_record">
						<label for="checkbox'.$i.'">&nbsp;</label>
						</div>
					</td>
					<td>'.$c_date.'</td>
					<td>'.$modified_date.'</td>
					<td>'.$ptNameLink.'</td>
					<td>'.$order_name.'</td>
					<td>&nbsp;</td>
				</tr>';
		}
		else if($sub_task=="" || $sub_task=='comp_orders'){
		//---  GET ALL SINGLE ORDERS WITHOUT ORDER SETS ---------
			$c_date = $ordersQryRes[$i]['c_date'];
			$modified_date = $ordersQryRes[$i]['m_date'];
			$order_id = $ordersQryRes[$i]['order_id'];
			$main_id = $ordersQryRes[$i]['order_set_associate_details_id'];
			$order_detail_arr = $ordersDetailsArr[$order_id];
			$order_name = $order_detail_arr['name'];
			$logged_provider_id = $ordersQryRes[$i]['logged_provider_id'];
			$provider_name = $usernameArr[$logged_provider_id];
			//---- ORDERS STATUS CHECK -----
			$status_option = '';
			$provider_status = $ordersQryRes[$i]['orders_status'];
			for($p=0;$p<count($opArr);$p++){
				$sel = $p == $provider_status ? 'selected="selected"' : '';
				$status_option .= "
					<option value=\"$p\" $sel>$opArr[$p]</option>";
			}
			$bgcolor = $q%2 == 0 ? '#F4F9EE' : '#FFFFFF';
			$q++;

			//Instruction ---
			$instruction = '';
			$o_type = $order_detail_arr['o_type'];
			preg_match('/Information/', $o_type, $infCheck);
			$tmp = trim($ordersQryRes[$i]['instruction_information_txt']);
			$tmp_con = trim($ordersQryRes[$i]['template_content']);
			if (count($infCheck) > 0 || !empty($tmp)) {
				$showDis = 'none';
				$infDis = 'table-row';
				$instruction = $tmp;
			}

			$inst_data="";
			if(!empty($instruction)|| !empty($tmp_con)){
				if(!empty($tmp_con)){
					$tmp_con = html_entity_decode($tmp_con);
					$tmp_con = trim($tmp_con,"\\n");
					$tmp_con = nl2br($tmp_con);
					$tmp_con = trim($tmp_con);
					//if(!empty($instruction)){ $instruction.= "<br/>"; }
					if(empty($instruction)){$tmp_con = str_replace(array("<p>", "</p>"), "", $tmp_con);}
				}
				$inst_data = '<br><span class=\"text-nowrap\"><b>Instruction</b>: '.$instruction.$tmp_con.'</span>';
			}
			//End Instruction ---

			$ordersContentData .= '
				<tr class="even-odd-comp-task">
					<td class="text-center">
						<div class="checkbox">
						<input id="checkbox'.$i.'" type="checkbox" name="order_set[]" value="order-'.$main_id.'" class="chk_record">
						<label for="checkbox'.$i.'">&nbsp;</label>
						</div>
					</td>
					<td>'.$c_date.'</td>
					<td>'.$modified_date.'</td>
					<td>'.$ptNameLink.'</td>
					<td>'.$order_name.$inst_data.'</td>
					<td>'.$provider_name.'</td>
				</tr>';
		}
}
	//-----SHOW COMPLETED MESSAGES------
	if($tableElem_msg != ""){
		$tableElem .= '<thead><tr class="purple_bar">
				<th class="title-row" colspan="7">Messages</th>
			</tr></thead>'.$tableElem_msg;
	}

	//--- SHOW COMPLETED ORDER SET DATA ----
	if(trim($orderSetContentData) != ''){
		$tableElem .= '<thead><tr class="purple_bar">
				<th class="title-row" colspan="6">Order Sets</th>
			</tr></thead>'.$orderSetContentData;
	}
	//--- SHOW COMPLETED ORDERS DATA ----
	if(trim($ordersContentData) != ''){
		$tableElem .= '<thead><tr class="purple_bar">
				<th class="title-row" colspan="6">Orders</th>
			</tr></thead>'.$ordersContentData;
	}
	if($comp_task_chk!="1" && $tableElem_msg=="" && $ordersContentData=="" && $orderSetContentData==""){
		$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger"> No Record Available </div></div></div>';
	}else{
		$tableElem .= '</tbody></table></div><div class="col-sm-12 text-center pt5 pdb5">'.$load_buttons.'</div>';;
	}
}
else
{
	$tableElem = '<div class="pt5 pdl_10 mCustomScrollbar dynamicRightPadding"><div class="alert alert-danger"> No Record Available </div></div>';
}
	echo $tableElem;
	break;
	case 'del_completed_tasks':
		if(trim($_REQUEST['chk_values_vf'])!="")
		{
			$qry = "update vf set finished = '1' where vf_id in(".$_REQUEST['chk_values_vf'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_vf_gl'])!="")
		{
			$qry = "update vf_gl set finished = '1' where vf_gl_id in(".$_REQUEST['chk_values_vf_gl'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_nfa'])!="")
		{
			$qry = "update nfa set finished = '1' where nfa_id in(".$_REQUEST['chk_values_nfa'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_oct'])!="")
		{
			$qry = "update oct SET finished = '1' where oct_id in (".$_REQUEST['chk_values_oct'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_oct_rnfl'])!="")
		{
			$qry = "update oct_rnfl SET finished = '1' where oct_rnfl_id in (".$_REQUEST['chk_values_oct_rnfl'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_pachy'])!="")
		{
			$qry = "update pachy set finished = '1' where pachy_id in(".$_REQUEST['chk_values_pachy'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_ivfa'])!="")
		{
			$qry = "update ivfa set finished = '1' where vf_id in(".$_REQUEST['chk_values_ivfa'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_disc'])!="")
		{
			$qry = "update disc set finished = '1' where disc_id in(".$_REQUEST['chk_values_disc'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_disc_external'])!="")
		{
			$qry = "update disc_external SET finished='1' where disc_id in (".$_REQUEST['chk_values_disc_external'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_topography'])!="")
		{
			$qry = "update topography SET finished='1' where topo_id in (".$_REQUEST['chk_values_topography'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_test_gdx'])!="")
		{
			$qry = "update test_gdx SET finished='1' where gdx_id in (".$_REQUEST['chk_values_test_gdx'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_surgical_tbl'])!="")
		{
			$qry = "update surgical_tbl SET finished='1' where surgical_id in (".$_REQUEST['chk_values_surgical_tbl'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_test_labs'])!="")
		{
			$qry = "update test_labs SET finished='1' where test_labs_id in (".$_REQUEST['chk_values_test_labs'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_test_other'])!="")
		{
			$qry = "update test_other SET finished='1' where test_other_id in (".$_REQUEST['chk_values_test_other'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_test_bscan'])!="")
		{
			$qry = "update test_bscan SET finished='1' where test_bscan_id in (".$_REQUEST['chk_values_test_bscan'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_icg'])!="")
		{
			$qry = "update icg SET finished='1' where icg_id in (".$_REQUEST['chk_values_icg'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_test_cellcnt'])!="")
		{
			$qry = "update test_cellcnt SET finished='1' where test_cellcnt_id in (".$_REQUEST['chk_values_test_cellcnt'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_user_messages'])!="")
		{
			$qry = "update user_messages SET del_status='1' where user_message_id in (".$_REQUEST['chk_values_user_messages'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_phy_todo_task'])!="")
		{
			$qry = "update phy_todo_task SET del_status='1' where phy_todo_task_id in (".$_REQUEST['chk_values_phy_todo_task'].")";
			imw_query($qry);
		}
		if(trim($_REQUEST['chk_values_orders'])!="")
		{
			$qry = "update order_set_associate_chart_notes_details set delete_status = '1'
				where order_set_associate_details_id in (".$_REQUEST['chk_values_orders'].")";
			imw_query($qry);	echo imw_error();
		}
		echo 'success';
	break;
	case 'del_resp_person':
		$phy_ids = trim($_REQUEST['phy_ids']);
		$msg_ids = trim($_REQUEST['msg_ids']);
		$order_set_ids = trim($_REQUEST['order_set_ids']);
		if(isset($phy_ids) && $phy_ids!="")
		{
			$reqQry = 'UPDATE phy_todo_task SET del_status = "1" where phy_todo_task_id in ('.$phy_ids.')';
			$result1 = imw_query($reqQry);
		}

		if(isset($msg_ids) && $msg_ids!="")
		{
			$reqQry2 = 'UPDATE user_messages SET resp_delete = "1" where user_message_id in ('.$msg_ids.')';
			$result2 = imw_query($reqQry2);
		}

		if(isset($order_set_ids) && $order_set_ids!=""){
			$reqQry3 = 'DELETE from order_set_associate_chart_notes where order_set_associate_id in ('.$order_set_ids.')';
			$result3 = imw_query($reqQry3);
		}

		if($result1 || $result2 || $result3)
		{ echo 'success'; }

	break;
	case 'unfinalized_patients':
		require_once($GLOBALS['fileroot'].'/library/classes/work_view/Facility.php');
		require_once($GLOBALS['fileroot'].'/library/classes/work_view/wv_functions.php');
		//require_once(dirname(__FILE__)."/../main/main_functions.php");
		$oFacility = new Facility("HQ");
		$arrChartTimer = $oFacility->getChartTimers();

		// Getting provider type
		$provider_type = $msgConsoleObj->operator_type;

		if(($provider_type != 1) && (core_check_privilege(array("priv_admin")) == false))
		{
			die('Provider has not privileged to see the content');
		}
		$displayAll =  (($provider_type != 1) && (core_check_privilege(array("priv_admin")) == true)) ? "block" : "none";
		$showUser = ($provider_type == 1) ? "block" : "none";
		$sall = !empty($_GET['sall']) ? "1" : "0";
		$result_arr = $msgConsoleObj->get_unfinalized_patients($sall);
		$result_data_arr = $result_arr['pv2'];

		//$result_data_arr = array_fill(2, 500, $result_arr['pv2'][0]);

		$tableElem = '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="comp-tasks" style="height:'.($_SESSION['wn_height']-344).'px">';

		$loadButtons = '
						<button onClick="del_unfinalized_pats(0)" class="btn btn-success" title="This button will refresh the unfinalized chart notes list so that finalized chart notes do not appear in this list.">Refresh List</button>
						<button onClick="del_unfinalized_pats(1)" class="btn btn-danger" title="This button will delete selected unfinalized chart notes. You will not see deleted chart notes again in Unfinalized Patients list.">Remove Pt.</button>
						<button onClick="printUnf();"  class="btn btn-success" title="Print"><span class="glyphicon glyphicon-print"></span> Print</button>';

		if(count($result_data_arr)>0)
		{
			$tableElem .= '<div id="data_show_by_default" style="display:'.$showUser.';">';
			$tableElem .= '<table class="table table-bordered sortable" style="margin-bottom: 0px;">
							<thead>
								<tr class="purple_bar">
									<th class="text-center noprint" style="width:70px;">
										<div class="checkbox">
											<input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
											<label for="checkbox">&nbsp;</label>
										</div>
									</th>
									<th style="width:70px;">S.no</th>
									<th>Patient Name</th>
									<th style="width:160px;">Date of Service</th>
									<th>Provider</th>
									<th>Facility</th>
								</tr>
							</thead>
							<tbody>
						';

			foreach($result_data_arr as $key=>$val_arr)
			{
				$patientName_cb ='';
				$dateOfService = $val_arr['date_of_service'];
				if(empty($dateOfService) || ($dateOfService=="0000-00-00")){
					$dos_ymd = getDateFormatDB($val_arr["create_dt"]);
				}else{
					$dos_ymd = $dateOfService;
				}
				$dateOfService = get_date_format($dos_ymd);

				$docId = $val_arr['doctorId'];
				if(!empty($docId)){
					$strReview = ($arrChartTimer["review"] * 24)." hours ";
					$strFinal = ($arrChartTimer["finalize"] * 24)." hours ";
					$strWarn = $strFinal." - ".$strReview;

					//Check
					if(isDtPassed($dos_ymd, $strWarn)){
						$finalizeDt = dtCalc($dos_ymd,"+".$strFinal,"m/d/y");
						$patientName_cb = "<span class=\"finalWarn\"> (Please finalize by ".$finalizeDt.")</span>";
					}
				}
				$patient_name = '<span class="text_purple" onclick="LoadWorkView(\''.$val_arr['patient_id'].'\')">'.$val_arr['patient_lname'].','.$val_arr['patient_fname'].' '.$val_arr['patient_mname'].' - '.$val_arr['patient_id'].' '.$patientName_cb.'</span>';

				//$patient_name = '<a class="a_clr1" title="Click to load Chart" href="javascript:;" onclick="top.LoadWorkView(\''.$val_arr['patient_id'].'\');">'.$val_arr['patient_lname'].','.$val_arr['patient_fname'].' '.$val_arr['patient_mname'].' - '.$val_arr['patient_id'].' '.$patientName_cb.'</a>';

				$provider_name = '';
				if(trim($val_arr['provider_lname'])!="")
				{
					$provider_name = $val_arr['provider_lname'].','.$val_arr['provider_fname'].' '.$val_arr['provider_mname'];
				}
				$rcn = $key+1;
				$tableElem .= '<tr class="even-odd-resp-person">
									<td class="text-center noprint">
										<div class="checkbox">
											<input id="checkbox'.$key.'" type="checkbox" name="chk_record_1[]" value="'.$val_arr['id'].'" class="chk_record">
											<label for="checkbox'.$key.'">&nbsp;</label>
										</div>
									</td>
									<td>'.$rcn.'</td>
									<td>'.$patient_name.'</td>
									<td sorttable_customkey="'.$dos_ymd.'">'.$dateOfService.'</td>
									<td>'.$provider_name.'</td>
									<td>'.$val_arr['facility_name'].'</td>
								</tr>';
			}

			$tableElem .= '</tbody></table></div>';
		}
		else
		{
			$tableElem .= '<div id="data_show_by_default" style="display:'.$showUser.';" class="alert alert-danger"> No unfinalized patient found </div><script type="text/javascript">update_link_status(\'#unfinalized_patients_opt\',\'unread\',\'read\');</script>';
		}

		$result_data_arr = $result_arr['pv1'];

		if(count($result_data_arr)>0)
		{
			$tableElem .= '<div id="show_on_chk" style="display:'.$displayAll.';">';
			$tableElem .= '<table class="table table-bordered sortable" style="margin-bottom: 0px;">
							<thead>
								<tr class="purple_bar">
									<th class="text-center noprint" style="width:70px;">
										<div class="checkbox">
											<input id="checkbox_hidden" type="checkbox" name="chk_sel_all1" class="chk_sel_all_hidden">
											<label for="checkbox_hidden">&nbsp;</label>
										</div>
									</th>
									<th style="width:70px;">S.no</th>
									<th>Patient Name</th>
									<th style="width:160px;">Date of Service</th>
									<th>Provider</th>
									<th>Facility</th>
								</tr>
							</thead>
							<tbody>
						';
			//$result_data_arr = array_fill(2, 500, $result_arr['pv2'][1]);

			foreach($result_data_arr as $key=>$val_arr)
			{
				$patientName_cb ='';
				$dateOfService = $val_arr['date_of_service'];
				if(empty($dateOfService) || ($dateOfService=="0000-00-00")){
					$dos_ymd = getDateFormatDB($val_arr["create_dt"]);
				}else{
					$dos_ymd = $dateOfService;
				}
				$dateOfService = get_date_format($dos_ymd);

				$docId = $val_arr['doctorId'];
				if(!empty($docId)){
					$strReview = ($arrChartTimer["review"] * 24)." hours ";
					$strFinal = ($arrChartTimer["finalize"] * 24)." hours ";
					$strWarn = $strFinal." - ".$strReview;

					//Check
					if(isDtPassed($dos_ymd, $strWarn)){
						$finalizeDt = dtCalc($dos_ymd,"+".$strFinal,"m/d/y");
						$patientName_cb = "<span class=\"finalWarn\"> (Please finalize by ".$finalizeDt.")</span>";
					}
				}
				$patient_name = '<span class="text_purple" onclick="LoadWorkView(\''.$val_arr['patient_id'].'\')">'.$val_arr['patient_lname'].','.$val_arr['patient_fname'].' '.$val_arr['patient_mname'].' - '.$val_arr['patient_id'].' '.$patientName_cb.'</span>';

				//$patient_name = '<a class="a_clr1" title="Click to load Chart" href="javascript:;" onclick="top.LoadWorkView(\''.$val_arr['patient_id'].'\');">'.$val_arr['patient_lname'].','.$val_arr['patient_fname'].' '.$val_arr['patient_mname'].' - '.$val_arr['patient_id'].' '.$patientName_cb.'</a>';
				$provider_name = '';

				if(trim($val_arr['provider_lname'])!="")
				{
					$provider_name = $val_arr['provider_lname'].','.$val_arr['provider_fname'].' '.$val_arr['provider_mname'];
				}

				$rcn = $key+1;
				$tableElem .= '<tr class="even-odd-resp-person">
									<td class="text-center noprint">
										<div class="checkbox">
											<input id="checkbox_hidden'.$key.'" type="checkbox" name="chk_record2[]" value="'.$val_arr['id'].'" class="chk_record_hidden">
											<label for="checkbox_hidden'.$key.'">&nbsp;</label>
										</div>
									</td>
									<td>'.$rcn.'</td>
									<td>'.$patient_name.'</td>
									<td sorttable_customkey="'.$dos_ymd.'">'.$dateOfService.'</td>
									<td>'.$provider_name.'</td>
									<td>'.$val_arr['facility_name'].'</td>
								</tr>';
			}

			$tableElem .= '</tbody></table></div>';
		}
		else
		{
			$tableElem .= '<div id="show_on_chk" style="display:'.$displayAll.';" class="alert alert-danger"> No unfinalized patient found </div>';
		}
		$tableElem .= '</div><div class="col-sm-12 text-center pt5 pdb5">'.$loadButtons.'</div>';

		echo $tableElem;
	break;
	case 'load_patient_notify':
		require_once 'patient_notify.php';
	break;
	case 'del_unf_pats':
		$pats_id = implode(',', $_REQUEST['pat_ids']);
		$flgdel = $_REQUEST['flgdel'];
		if($flgdel==1){
			$reqQry = 'update chart_master_table set not2show = "1" where id in ('.$pats_id.') and finalize="0" '; //ONLY DELETE UNFINALIZED CHARTS NOTES
			$result = imw_query($reqQry);
		}
		if($result){ echo 'success'; }
	break;
	case 'order_sets':
		require_once 'order_set.php';
	break;
	case 'wnl_charttemplate':
		require_once 'wnl_charttemplate.php';
	break;
	case 'import_ccda_interface':
		require_once 'import_ccda_interface.php';
	break;
	case 'load_direct_messages':
		require_once 'direct_messages.php';
	break;
	case 'set_read_direct_msg':
		$direct_msg_id = $_REQUEST["direct_msg_id"];
		imw_query("UPDATE direct_messages SET is_read = 1 WHERE id = '".$direct_msg_id."'");
	break;
	case 'pt_details_ajax':
		$patient_id = (isset($_GET['ptid']) && intval($_GET['ptid'])>0) ? intval($_GET['ptid']) : 0;
		if($patient_id>0){
			$rs = $msgConsoleObj->get_patient_more_info($patient_id);
			$PTimageFile = '';
			$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
			if($rs['p_imagename'] !="")
			$file = $dir_path.$rs['p_imagename'];
			if(trim($rs['p_imagename'])!='' && file_exists($file)){
				if($rs['p_imagename'] !="")
                $rs['p_imagename'] = $GLOBALS['rootdir'] . "../../data/".constant('PRACTICE_PATH') . $rs['p_imagename'];
				$PTimageFile = '<img src="'.$rs['p_imagename'].'" style="width:80px;">';}
			else{$PTimageFile = '<img src="'.$GLOBALS['rootdir'].'../../library/images/no_image_found.png" style="width: 105px;">';}
			$pt_address = $rs['street'];
			$pt_appt = $msgConsoleObj->get_pt_appt($rs['id']);
			if(trim($rs['street2'])!=''){$pt_address.=', '.trim($rs['street2']);}
			$csz = '';
			$csz .= $rs['city'];
			if($csz != ''){$csz .= ', '.$rs['state'];}else{$csz = $rs['state'];}
			if($csz != '' && trim($rs['postal_code'])!=''){$csz .= ' - '.$rs['postal_code'];}else{$csz = $rs['postal_code'];}
			if($csz != '' && $rs['zip_ext']!=''){$csz .= '-'.$rs['zip_ext'];}
			if($csz != '') { $csz = '<br>'.$csz.'<br>'; }
			$phones = '';
			if($rs['phone_home']!=''){$phone_home = str_replace(" ","",core_phone_format($rs['phone_home']));}
			if($rs['phone_biz']!=''){$phone_biz = str_replace(" ","",core_phone_format($rs['phone_biz']));}
			if($rs['phone_cell']!=''){$phone_cell = str_replace(" ","",core_phone_format($rs['phone_cell']));}
			if($pt_appt['appt_dt_time']!="")
			{
				$facility_name = $pt_appt['facility_name'];
				if(str_word_count($facility_name)!=1){
					$arr_facility_name = str_word_count($facility_name,1);
					$tmp_arr_fac_name = '';
					foreach($arr_facility_name as $val){
						$tmp_arr_fac_name .= substr($val,0,1);
					}
					$facility_name = strtoupper($tmp_arr_fac_name);
				}
				$appt_data = $pt_appt['phy_init_name'].' / '.$pt_appt['appt_dt_time'].' / '.$facility_name;
			}
			else
			{
				$appt_data = 'N/A';
			}


			$pt_name_str = core_name_format($rs['lname'],$rs['fname'],$rs['mname']);

			$removePtIcon = '<i class="glyphicon glyphicon-remove pointer" style="position:absolute; right:5px; top:5px;" title="Remove Patient" onClick="delPtDetails();"></i>';
			$tableElem .='
			<div class="form-group">
				<div class="table-responsive">
				<table class="table table-condensed table-bordered pt_detail_inmsg">
					<tr>
						<td><b>Name:</b></td>
						<td colspan="3" style="position:relative;">'.$pt_name_str.' - '.$rs['id'].$removePtIcon.'</td>
						<td rowspan="4" class="text-center">'.$PTimageFile.'</td>
					</tr>
					<tr>
						<td><b>Gender:</b></td>
						<td>'.$rs['sex'].'</td>
						<td><b>DOB:</b></td>
						<td class="text-nowrap">'.$rs['DOB'].'</td>
					</tr>
					<tr>
						<td class="valignTop"><b>Address:</b></td>
						<td colspan="3">'.$pt_address.$csz.'</td>
					</tr>
					<tr>
						<td><b>Home: </b></td>
						<td class="nowrap">'.$phone_home.'</td>
						<td class="nowrap"><b>Work: </b></td>
						<td class="nowrap">'.$phone_biz.'</td>
					</tr>
					<tr>
						<td class="nowrap"><b>Cell:</b></td>
						<td>'.$phone_cell.'</td>
						<td><b>Email: </b></td>
						<td colspan="2">'.$rs['email'].'</td>
					</tr>
					<tr>
						<td><b>Appt:</b></td>
						<td colspan="4">'.$appt_data.'</td>
					</tr>
				</table>
				</div>
			</div>';
			if(isset($_GET['return_data']) && empty($_GET['return_data']) == false){	//Returns Only Pt. data
				$rs['pt_name'] = $pt_name_str;
				echo json_encode($rs);
			}else{		//returns Pt. data HTML
				echo $tableElem;
			}
		}else{
			echo '<div class="alert alert-danger">No patient selected.</div>';
		}
	break;
	case 'pt_more_details': //to show more details on click on Expand icon
		$rs = $msgConsoleObj->get_patient_more_info();
		$PTimageFile = ''; $rs['p_imagename'] = $GLOBALS['webroot']."/data/".constant('PRACTICE_PATH').$rs['p_imagename'];
		if(file_exists($rs['p_imagename'])){$PTimageFile = '<img src="'.$rs['p_imagename'].'" style="width:150px;">';}
		else{$PTimageFile = '<div style="height:120px; width:150px; border:2px solid #ccc; background-color:#eee; text-align:center;">
		<br><br><br>Patient Image NOT Found</div>';}
		$pt_appt = $msgConsoleObj->get_pt_appt($rs['id']);
		$csz = '';
		$csz .= $rs['city'];
		if(trim($rs['street2'])!="" && trim($rs['street'])!=""){ $rs['street2'] = ', '.$rs['street2']; }
		if($csz != ''){$csz .= ', '.$rs['state'];}else{$csz = $rs['state'];}
		if($csz != ''){$csz .= ' - '.$rs['postal_code'];}else{$csz = $rs['postal_code'];}
		if($csz != '' && $rs['zip_ext']!=''){$csz .= '-'.$rs['zip_ext'];}
		$phones = '';
		if($rs['phone_home']!=''){$phone_home = core_phone_format($rs['phone_home']).' (Home)<br>';}
		if($rs['phone_biz']!=''){$phone_work = core_phone_format($rs['phone_biz']).' (Work)<br>';}
		if($rs['phone_cell']!=''){$phone_mobile = core_phone_format($rs['phone_cell']).' (Mobile)';}
		if($pt_appt['appt_dt_time']!="")
		{
			$facility_name = $pt_appt['facility_name'];
			if(str_word_count($facility_name)!=1){
				$arr_facility_name = str_word_count($facility_name,1);
				$tmp_arr_fac_name = '';
				foreach($arr_facility_name as $val){
					$tmp_arr_fac_name .= substr($val,0,1);
				}
				$facility_name = strtoupper($tmp_arr_fac_name);
			}
			$appt_data = $pt_appt['phy_init_name'].' / '.$pt_appt['appt_dt_time'].' / '.$facility_name;
		}
		else
		{
			$appt_data = 'N/A';
		}
		$returnHTML = '
		<table class="table table-bordered table-striped table-hover table_separate" id="tbl_pt_more_info" style="margin-bottom:10px;">
			<tr>
				<td style="width:80px;"><b>Name</b></td>
				<td colspan="3" style="width:250px;">'.$rs['lname'].', '.$rs['fname'].' '.$rs['mname'].' - '.$rs['id'].'</td>
				<td style="width:150px;" rowspan="8"><small class="ml20 link_cursor" onclick="hide_pt_more_details();"><img src="../themes/default/images/icon_contract_info.png" onclick="hide_pt_more_details();"> (Close)</small></td>
				<td style="width:auto;" rowspan="8">'.$PTimageFile.'</td>
			</tr>
			<tr>
				<td><b>Gender</b></td>
				<td>'.$rs['sex'].'</td>
				<td><b>DOB</b></td>
				<td>'.$rs['DOB'].'</td>
			</tr>
			<tr>
				<td valign="top"><b>Address</b></td>
				<td colspan="3">'.$rs['street'].$rs['street2'].'<br/>'.$csz.'</td>
			</tr>
			<tr>
				<td><b>Home</b></td>
				<td>'.$phone_home.'</td>
				<td><b>Work</b></td>
				<td>'.$phone_work.'</td>
			</tr>
			<tr>
				<td><b>Cell</b></td>
				<td>'.$phone_mobile.'</td>
				<td><b>Email</b></td>
				<td>'.$rs['email'].'</td>
			</tr>
			<tr>
				<td><b>Appt:</b></td>
				<td colspan="3">'.$appt_data.'</td>
			</tr>
		</table>';
		echo $returnHTML;
		break;

		case 'load_patient_messages':
			require_once "patient_messages.php";
		break;

		case 'get_direct_email':
			$return_arr = array();
			$query = imw_query("select email as d_email from `users` where email != '' and email_password != '' and delete_status = 0");
			if(imw_num_rows($query) > 0){
				while($row = imw_fetch_assoc($query)){
					$return_arr[] = $row['d_email'];
				}
				array_unique($return_arr);
			}
			echo json_encode($return_arr);
		break;
		case 'set_unread_pt_msg':
			$pt_msgid = $_REQUEST["pt_msgid"];

			// this is taking time to process so commented
			/*if(isERPPortalEnabled()){
				include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
				include_once($GLOBALS['srcdir'].'/erp_portal/incomingsecuremessages.php');
				$oIncSecMsg = new IncomingSecureMessages();
				$oIncSecMsg->markRead($pt_msgid,'true');
			}*/

			$req_qry = "UPDATE patient_messages SET is_read = 1 WHERE pt_msg_id = ".$pt_msgid;
			imw_query($req_qry);

			$read_msg="select COUNT(pt_msg_id) as total_unread from patient_messages where is_read=0 and communication_type = 2 and del_status = 0 and is_done = 0 and  sender_id='".$sender_id."'";
			$res=imw_query($read_msg);
			$row=imw_fetch_assoc($res);
			$total_unread=$row['total_unread'];
			echo $total_unread;
		break;
		case 'set_msg_done':
			$pt_msgid = $_REQUEST["pt_msgid"];
			$req_qry = "UPDATE patient_messages SET is_done = 1 WHERE pt_msg_id = ".$pt_msgid;
			imw_query($req_qry);
		break;
		case 'del_pt_messages':
			$chs_ids = $_REQUEST["chs_ids"];
			$req_qry = "UPDATE patient_messages SET del_status = 1 WHERE pt_msg_id  IN(".$chs_ids.")";
			imw_query($req_qry);
		break;
		case 'del_approvals':
			$chs_ids = $_REQUEST["chs_ids"];
			$req_qry = "UPDATE iportal_req_changes SET del_status = 1 WHERE id  IN(".$chs_ids.")";
			imw_query($req_qry);
		break;
		case 'get_pat_dos':
			$return_arr = array();
			$rqPId = (int)$_REQUEST['pId'];
			$cbXData = "<div class='form-group'><div class='row'>";
			if($rqPId > 0){
				$getFORMIDQry =imw_query("SELECT id as form_id, date_of_service FROM chart_master_table WHERE patient_id = '".$rqPId."' order by id desc");
				if($getFORMIDQry){
					$cbXData .= "<div class='col-sm-2'><label>Select Visit</label></div>
								<div class='col-sm-3'><select  name='cmbxElectronicDOS' id='cmbxElectronicDOS' class='form-control minimal'><option value='all'> All </option>";
					$return_arr['counter'] = imw_num_rows($getFORMIDQry);
					if(imw_num_rows($getFORMIDQry)>0){
						$counterInc=1;
						while($getFormRow = imw_fetch_array($getFORMIDQry)){
						$form_idTemp = $getFormRow['form_id'];
						//$qryDOS=imw_query("select date_of_service from  chart_master_table where patient_id='".$rqPId."' and id='".$form_idTemp."'  order by date_of_service DESC ");
						//$co=imw_num_rows($qryDOS);
							//if(($co > 0)){
								//$crow=imw_fetch_array($qryDOS);
								$date_of_service = date("m-d-Y", strtotime($getFormRow["date_of_service"]));
									$checked="";

								$cbXData .= "<option  value='".trim($form_idTemp)."'> ".$date_of_service." </option>";
								$counterInc++;
							// }
						}

					}
					$cbXData .= "</select></div>
					<div class=\"col-sm-3\">
						<div class=\"row\">
							<div class='col-sm-6'><label>&nbsp;<input type=\"radio\" name=\"ccd_type\" id=\"ccd_type_ccd\" checked value=\"ccd\"> CCD</label></div>
						<div class='col-sm-6'><label><input type=\"radio\" name=\"ccd_type\" id=\"ccd_type_rn\" value=\"rn\"> Ref.Note</label></div>
						</div>
					</div>
					<div class=\"col-sm-4 text-center\">
						<div class=\"row\">
							<div class='col-sm-6 text-center' id=\"ccda_button_attachment\"><button class='btn btn-success btn-sm' data-attach=\"ccda\" onclick=\"toggle_attach_modal(this);\" type='button'>Attach CCDA <span id='ccda_attachment' class='glyphicon glyphicon-paperclip hide'></span></button></div>
							<div class='col-sm-6 text-center' id=\"xml_button_attachment\"><button class='btn btn-success btn-sm' type='button' data-attach=\"xml\" onclick=\"toggle_attach_modal(this);\">Attach XML <span id='xml_attachment' class='glyphicon glyphicon-paperclip hide'></span></button></div>
							<input type=\"hidden\" name=\"attachment_type\" value=\"ccda\" id=\"direct_attachment_value\">
						</div>
					</div>";
				}
			}
			$cbXData .= "</div></div>";
			$return_arr['dos_str'] = $cbXData;
			echo json_encode($return_arr);
		break;

		case 'complete_task':
			$selID = array();
			$selID = explode(',', $_REQUEST['chs_ids']);
			$counter = 0;

			if(count($selID) > 0){
				foreach($selID as $comId){
					$valArr = array();

					$valArr['task_modify_date'] = date('Y-m-d H:i:s');
					$valArr['task_assign'] = 1;
					$valArr['task_done'] = 2;

					$updateStat = UpdateRecords($comId,'commentId',$valArr,'paymentscomment');
                    if($updateStat){
                        $sql ="Update tm_assigned_rules set status=1 where payment_comtId = '$comId'";
                        imw_query($sql);
                    }
					if($updateStat) $counter++;
				}
			}

			echo json_encode($counter);
		break;


        case 'rule_tasks':
				if(isERPPortalEnabled()){
					try {
						include_once($GLOBALS['fileroot']."/library/erp_portal/rabbitmq_exchange.php");
						include_once($GLOBALS['srcdir']."/erp_portal/patient_payments.php");
						$objPatient_payments = new Patient_payments;
						$objPatient_payments->getPatientPayments();
					} catch(Exception $e) {
						$erp_error[]='Unable to connect to ERP Portal';
					}
				}
		
				$authId = (isset($_SESSION['authId']) && empty($_SESSION['authId']) == false) ? $_SESSION['authId'] : '';
				$htmlStr = '';

                $users_arr = $msgConsoleObj->get_users();
                $usernameArr = array();
                foreach($users_arr as $user_row)
                {
                    $id = $user_row['id'];
                    //patient_name => user name (lname,", ",fname," ",mname)
                    $name = $user_row['patient_name'];
                    $usernameArr[$id] = $name;
                }

                /* Get User Groups */
                $user_groups = array();
                $sql = "select * from `user_groups` where `status` = '1' order by `name`";
                $sql_rs = imw_query($sql);
                if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                    while ($row = imw_fetch_assoc($sql_rs)) {
                        $user_groups[$row['id']] = $row['name'];
                    }
                }

                $categories = array();
                $sql = 'SELECT * FROM tm_rule_category ';
                $resp = imw_query($sql);
                if ($resp && imw_num_rows($resp) > 0) {
                    while ($row = imw_fetch_assoc($resp)) {
                        $categories[$row['id']] = $row['tm_rule_category'];
                    }
                }

                $rules = array();
                $rule_name = array();
                $rule_sql = "SELECT * FROM tm_rules ";
                $rule_rs = imw_query($rule_sql);
                if ($rule_rs && imw_num_rows($rule_rs) > 0) {
                    while ($rules_row = imw_fetch_assoc($rule_rs)) {
                        $rules[] = $rules_row;
                        $rule_name[$rules_row['id']] = $rules_row['tm_rule_name'];
                    }
                }

                $pt_status=array();
                $pt_sql = "SELECT * FROM patient_status_tbl ";
                $pt_rs = imw_query($pt_sql);
                if ($pt_rs && imw_num_rows($pt_rs) > 0) {
                    while ($row = imw_fetch_assoc($pt_rs)) {
                        $pt_status[$row['pt_status_id']] = $row['pt_status_name'];
                    }
                }

                $pt_ac_status=array();
                $pt_ac_sql = "Select id, status_name FROM account_status WHERE del_status='0' ORDER BY status_name ";
                $pt_ac_rs = imw_query($pt_ac_sql);
                if ($pt_ac_rs && imw_num_rows($pt_ac_rs) > 0) {
                    while ($row = imw_fetch_assoc($pt_ac_rs)) {
                        $pt_ac_status[$row['id']] = $row['status_name'];
                    }
                }

                /* Get schedule_status array */
                $ss_rule_name=array();
                $ssrule_sql = "SELECT id,status_name FROM schedule_status where status=1";
                $sssql_rs = imw_query($ssrule_sql);
                if ($sssql_rs && imw_num_rows($sssql_rs) > 0) {
                    while ($ssrow = imw_fetch_assoc($sssql_rs)) {
                        if(trim($ssrow['status_name'])!=''){
                            $ss_rule_name[$ssrow['id']] = $ssrow['status_name'];
                        }
                    }
                }

                /* Get Ref Physicians */
                $ref_phy_arr = array();
                $qry = "select physician_Reffer_id, TRIM(CONCAT(IF(Title!='',CONCAT(Title,' '),''),LastName,', ', FirstName ,' ',MiddleName)) AS name from refferphysician
                        WHERE delete_status=0
                        AND LastName!=''
                        AND FirstName!=''
                        AND primary_id = 0
                        order by TRIM(LastName) asc
                        ";
                $sql_rs = imw_query($qry);
                if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                    while ($row = imw_fetch_assoc($sql_rs)) {
                        if(trim($row['name'])!=''){
                            $ref_phy_arr[$row['physician_Reffer_id']] = $row['name'];
                        }
                    }
                }

                /* Get Facilities */
                $facility_arr = array();
                $qry = "select id, name from facility order by name";
                $sql_rs = imw_query($qry);
                if ($sql_rs && imw_num_rows($sql_rs) > 0) {
                    while ($row = imw_fetch_assoc($sql_rs)) {
                        $facility_arr[$row['id']] = $row['name'];
                    }
                }

                //--- GET Groups SELECT BOX ----
                $group_id_arr = array();
                $group_query = "Select gro_id,name,del_status from groups_new order by name";
                $group_query_res = imw_query($group_query);
                while ($group_res = imw_fetch_array($group_query_res)) {
                    $group_id = $group_res['gro_id'];
                    $group_id_arr[$group_id] = $group_res['name'];
                }

                //Operator Initials
                $op_name_arr = preg_split('/, /', strtoupper($_SESSION['authProviderName']));
                $op_name=$op_name_arr[1][0];
                $op_name.=$op_name_arr[0][0];

                $mainRefArr= array();
                $reqQry = "select id,user_group_id from users where id > 0 and delete_status = 0";
                $reqQryrs = imw_query($reqQry);
                while($row = imw_fetch_assoc($reqQryrs)) {
                    $mainRefArr[$row['user_group_id']][] = $row['id'];
                }

                $filter_where='';
                $reminder_date='';
                if(isset($_REQUEST['reminder_filter']) && $_REQUEST['reminder_filter']!='') {
                    $reminder_date=$_REQUEST['reminder_filter'];
                    $reminder_filter=date('Y-m-d',strtotime(str_ireplace('-', '/', $reminder_date)));
                    $filter_where=' AND (reminder_date="'.$reminder_filter.'") ';
                }

				if(empty($authId) == false){
					//Fetching Results
                    $query='SELECT *, tm_assigned_rules.id as taid FROM tm_assigned_rules
					LEFT JOIN tm_rules_list ON (tm_rules_list.id = tm_assigned_rules.rule_list_id and tm_rules_list.rule_status = 0)
					WHERE
						tm_assigned_rules.status = 0  '.$filter_where.' 
						AND (tm_assigned_rules.task_on_reminder=0 OR (tm_assigned_rules.task_on_reminder=1 AND tm_assigned_rules.reminder_date<="'.date('Y-m-d').'") ) 
                        order by tm_assigned_rules.added_on desc, tm_assigned_rules.reminder_date desc
                        ';
					$chkQry = imw_query($query);

                    $resultArr=array();
                    if($chkQry && imw_num_rows($chkQry) > 0){
                        while($rowFetch=imw_fetch_assoc($chkQry)){
                            $id=$rowFetch['taid'];
                            $user_group=$rowFetch['user_group'];
                            $user_names=$rowFetch['user_name'];

                            $user_names_arr = explode(', ', $user_names);
                            $user_names_arr = array_unique($user_names_arr);

                            $user_groups_arr = explode(', ', $user_group);
                            $user_groups_arr = array_unique($user_groups_arr);
                            if(!empty($user_groups_arr)) {
                                foreach($user_groups_arr as $row_arr) {
                                    if($mainRefArr[$row_arr]) {
                                        $user_names_arr = array_merge($user_names_arr, $mainRefArr[$row_arr]);
                                    }

//                                    $reqQry = "select id from users where user_group_id='".$row_arr."' and id > 0 and delete_status = 0";
//                                    $reqQryrs = imw_query($reqQry);
//                                    while($row = imw_fetch_assoc($reqQryrs)) {
//                                        $user_names_arr[]=$row['id'];
//                                    }
                                }
                            }
                            $user_names_arr = array_unique($user_names_arr);
                            if($rowFetch['notes_users']!='' && $rowFetch['section_name']=='Accounting Notes' && $rowFetch['rule_list_id']=='0') {
                                $user_names_arr=explode(',', $rowFetch['notes_users']);
                                $user_names=$rowFetch['notes_users'];
                            }
                            if(!in_array($authId,$user_names_arr)){
                                continue;
                            }

                            $resultArr[]=$rowFetch;
                        }
                    }

                    $page_get=$_REQUEST['page_no'];
                    $per_page=$_REQUEST['per_page'];

                    include_once($GLOBALS['srcdir']."/classes/paging.inc.php");
                    $per_page_records="20";
                    if($per_page!='' && (int)$per_page>0){$per_page_records=$per_page;}
                    $page = (!isset($page_get) || $page_get=="")?1:$page_get;
                    $objPaging = new Paging($per_page_records,$page);
                    $objPaging->sort_by = $per_page;
                    $objPaging->sort_order = $case_filter;

                    $objPaging->totalRecords = count($resultArr);
                    $objPaging->func_name = "load_rules_tasks";
                    $objPaging->data = $resultArr;

                    $result_arr = $objPaging->fetchLimitedRecords();
                    $p_link1=$objPaging->getPagingString($page);
                    $p_link2=$objPaging->buildComponentR8($page);
                  //pre(count($result_arr));die;
                    $options="";
                    for($b=10;$b<=100;$b+=($b>=40)?20:10){
                        $selected="";
                        if((int)$per_page>0){
                            if($per_page==$b){$selected="selected";}
                        }else if($b==20){$selected="selected";}
                        $options.="<option ".$selected." value='".$b."'>".$b."</option>";
                    }

                    //<th>Changed To</th>
                    $htmlHeadTopStr = '';
                    $htmlHeadTopStr .= '<div class="row msgCount">';
                    $htmlHeadTopStr .= '<div class="col-sm-4">';
                    $htmlHeadTopStr .= $p_link1 . ' Record(s) per page &nbsp;';
                    $htmlHeadTopStr .= '</div>';
                    $htmlHeadTopStr .= '<div class="col-sm-1" style="padding-right:10px !important;">';
                    $htmlHeadTopStr .= '<select id="no_per_page" onChange="load_rules_tasks(\'\',this.value);" class="selectpicker" data-width="100%">' . $options . '</select>';
                    $htmlHeadTopStr .= '</div>';
                    $htmlHeadTopStr .= '<div class="col-sm-5" style="padding-right:10px !important;">';
                    $htmlHeadTopStr .= $p_link2;
                    $htmlHeadTopStr .= '</div>';
                    $htmlHeadTopStr .= '<div class="col-sm-2 form-inline" style="padding-right:10px !important;">';
                    $htmlHeadTopStr .= '<div class="input-group">';
                    $htmlHeadTopStr .= '<input type="text" name="filter_rem_date" id="filter_rem_date" value="'.$reminder_date.'" class="reminder_date_pick form-control" placeholder="Reminder Date" autocomplete="off">';
                    $htmlHeadTopStr .= '<span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>';
                    $htmlHeadTopStr .= '</div>';
                    $htmlHeadTopStr .= '</div>';
                    $htmlHeadTopStr .= '</div>';

                    $htmlHeadStr = '
					  <thead>
						  <tr class="purple_bar">
							  <th class="text-center sorttable_nosort" width="3%">
								  <div class="checkbox">
									  <input id="checkbox" type="checkbox" name="chk_sel_all" class="chk_sel_all">
									  <label for="checkbox">&nbsp;</label>
								  </div>
							  </th>
                              <th width="10%">Assigned Date</th>
                              <th width="7%">Category</th>
                              <th width="8%">Rule Name</th>
                              <th width="12%">Patient Name</th>
                              <th width="7%">DOS</th>
							  <th width="24%">Comments</th>
							  <th width="10%">Modified By</th>
							  <th width="19%">Assigned To</th>
						  </tr>
					  </thead>';

					$htmlStr = '';
					$emptyRec = true;
                    if(count($resultArr) > 0){
						//while($rowFetch = imw_fetch_assoc($chkQry)){
						foreach($result_arr as $rowFetch){
                            $id=$rowFetch['taid'];
                            $user_group=$rowFetch['user_group'];
                            $user_names=$rowFetch['user_name'];

                            $user_names_arr = explode(', ', $user_names);
                            $user_names_arr = array_unique($user_names_arr);

                            $user_groups_arr = explode(', ', $user_group);
                            $user_groups_arr = array_unique($user_groups_arr);
                            if(!empty($user_groups_arr)) {
                                foreach($user_groups_arr as $row_arr) {
                                    if($mainRefArr[$row_arr]) {
                                        $user_names_arr = array_merge($user_names_arr, $mainRefArr[$row_arr]);
                                    }

//                                    $reqQry = "select id from users where user_group_id='".$row_arr."' and id > 0 and delete_status = 0";
//                                    $reqQryrs = imw_query($reqQry);
//                                    while($row = imw_fetch_assoc($reqQryrs)) {
//                                        $user_names_arr[]=$row['id'];
//                                    }
                                }
                            }
                            $user_names_arr = array_unique($user_names_arr);
                            if($rowFetch['notes_users']!='' && $rowFetch['section_name']=='Accounting Notes' && $rowFetch['rule_list_id']=='0') {
                                $user_names_arr=explode(',', $rowFetch['notes_users']);
                                $user_names=$rowFetch['notes_users'];
                            }
                            if(!in_array($authId,$user_names_arr)){
                                continue;
                            }


                            $comments = '';
                            if(isset($rowFetch['comments']) && $rowFetch['comments']!='') {
                                $comments.= "Rule comment => ".$rowFetch['comments'];
                            } else {
                                if(isset($rowFetch['comment']) && $rowFetch['comment']!='') {
                                    $comments.= "Rule comment => ".$rowFetch['comment'];
                                }
                            }
                            if(isset($rowFetch['section_name']) && $rowFetch['section_name']=='Accounting Notes') {
                                $comments.= '<b>'.$rowFetch['section_name'].'</b>';
                            }
							if( isERPPortalEnabled() ) {
								if(isset($rowFetch['section_name']) && $rowFetch['section_name']=='Payments Received From Portal' && isset($rowFetch['patient_portal_payment_id']) && $rowFetch['patient_portal_payment_id']!=0) {
									$comments.= '<b>'.$rowFetch['section_name'].'</b>';
								}
								if(isset($rowFetch['section_name']) && $rowFetch['section_name']=='Payments Received From Portal' && isset($rowFetch['patient_portal_payments']) && $rowFetch['patient_portal_payments']!='') {
									$comments.= '<br />'.$rowFetch['patient_portal_payments'].'</b>';
								}
							}
                            if(isset($rowFetch['outbound_fax']) && $rowFetch['outbound_fax']!='' && isset($rowFetch['section_name']) && $rowFetch['section_name']=='Outgoing Fax') {
                                $array2 = json_decode($rowFetch['outbound_fax'], true);
                                $comments.= '<br /> '.prettyViewArr($array2);
                            }
                            if(isset($rowFetch['changed_value']) && $rowFetch['changed_value']!='' && $rowFetch['section_name']=='Accounting Notes') {
                                $comments.= '<br />Assigned Task => '.$rowFetch['changed_value'];
                            }
                            if(isset($rowFetch['reminder_date']) && $rowFetch['reminder_date']!='0000-00-00' && $rowFetch['section_name']=='Accounting Notes') {
                                $comments.= '<br /><b>Reminder Date</b> => '.date('m-d-Y',strtotime($rowFetch['reminder_date']));
                            }
                            if(isset($rowFetch['changed_value']) && $rowFetch['changed_value']!='0' && $rowFetch['section_name']=='reason_code') {
                                $comments.= '<br />Reason Code => '.$rowFetch['changed_value'];
                            }
                            if(isset($rowFetch['encounter_id']) && $rowFetch['encounter_id']!='0') {
                                $comments.= '<br />Encounter ID => '.$rowFetch['encounter_id'];
                            }
                            if(isset($rowFetch['cpt_code']) && $rowFetch['cpt_code']!='') {
                                $comments.= '<br />CPT => '.$rowFetch['cpt_code'];
                            }
                            if(isset($rowFetch['appt_type']) && $rowFetch['appt_type']!='') {
                                $comments.= '<br />Appt Type => '.$rowFetch['appt_type'];
                            }
                            if(isset($rowFetch['appt_date']) && $rowFetch['appt_date']!='0000-00-00') {
                                $comments.= '<br />Appt Date => '.date('m-d-Y',strtotime($rowFetch['appt_date']));
                            }
                            if(isset($rowFetch['appt_time']) && $rowFetch['appt_time']!='00:00:00') {
                                $comments.= '<br />Appt Time => '.$rowFetch['appt_time'];
                            }
                            if(isset($rowFetch['appt_status']) && $rowFetch['appt_status']!='') {
                                $comments.= '<br />Appt Status => '.$rowFetch['appt_status'];
                 	        }
                            if(isset($rowFetch['group_name']) && $rowFetch['group_name']!='') {
                                if(isset($group_id_arr[$rowFetch['group_name']]) && $group_id_arr[$rowFetch['group_name']]!='') {
                                    $comments.= '<br />Group => '.$group_id_arr[$rowFetch['group_name']];
                                } else {
                                    $comments.= '<br />Group => '.$rowFetch['group_name'];
                                }
                            }
                            if(isset($rowFetch['appt_facility_id']) && $rowFetch['appt_facility_id']!='0') {
                                $comments.= '<br />Facility => '.$facility_arr[$rowFetch['appt_facility_id']];
                            }
                            if(isset($rowFetch['appt_ref_phy_id']) && $rowFetch['appt_ref_phy_id']!='0') {
                                $comments.= '<br />Ref. Physician => '.$ref_phy_arr[$rowFetch['appt_facility_id']];
                            }
                            if(isset($rowFetch['appt_comment']) && $rowFetch['appt_comment']!='') {
                                $comments.= '<br />Appt Comment => '.$rowFetch['appt_comment'];
                            }
                            if(isset($rowFetch['days_aged']) && $rowFetch['days_aged']!='0') {
                                $comments.= '<br />Days Aged => '.$rowFetch['days_aged'];
                            }
                            if(isset($rowFetch['amount_due']) && $rowFetch['amount_due']!='0.00') {
                                $comments.= '<br />Amount Due => '.(($rowFetch['amount_due']=='any')?'':'$').$rowFetch['amount_due'];
                            }
                            if(isset($rowFetch['ar_comment']) && $rowFetch['ar_comment']!='') {
                                $comments.= '<br />Comment assoc. DOS => '.$rowFetch['ar_comment'];
                            }
                            if(isset($rowFetch['ins_comp']) && $rowFetch['ins_comp']!='') {
                                $comments.= '<br />Insurance Co => '.$rowFetch['ins_comp'];
                            }
                            if(isset($rowFetch['ins_group']) && $rowFetch['ins_group']!='') {
                                $comments.= '<br />Insurance Group => '.$rowFetch['ins_group'];
                            }


                            if(isset($rowFetch['inbound_fax_id']) && $rowFetch['inbound_fax_id']!='0') {
                                $inbound_fax_id=trim($rowFetch['inbound_fax_id']);
                                $infax_reqSql = "SELECT `id`, `from_number`, `message`, date_format(`received_at`, '%m-%d-%Y') AS 'date_received' FROM `inbound_fax` WHERE id='".$inbound_fax_id."' AND `patient_id`=0 AND `from_number`!='' AND `del_status`=0 ";
                                $infax_reqQry = imw_query($infax_reqSql);
                                $inbound_fax_row=imw_fetch_assoc($infax_reqQry);
                                if(isset($inbound_fax_row['from_number']) && $inbound_fax_row['from_number']!='') {
                                    $comments.= '<br />From Number => '.$inbound_fax_row['from_number'];
                                }
                                if(isset($inbound_fax_row['message']) && $inbound_fax_row['message']!='') {
                                    $comments.= '<br />Message => '.$inbound_fax_row['message'];
                                }
                                if(isset($inbound_fax_row['date_received']) && $inbound_fax_row['date_received']!='') {
                                    $comments.= '<br />Received Date => '.$inbound_fax_row['date_received'];
                                }
                            }

                            if(isset($rowFetch['transaction']) && $rowFetch['transaction'] != '') {
                                $trans_arr=array("discount"=>"Discount","denial"=>"Denied","deductible"=>"Deductible","write_off"=>"Write Off");
                                $comments.= '<br />Transaction Mode => '. $trans_arr[$rowFetch['changed_value']];
                            }


                            $dos='';
                            if(isset($rowFetch['date_of_service']) && $rowFetch['date_of_service']!='0000-00-00') {
                                $dos = $rowFetch['date_of_service'];
                                $dos=date('m-d-Y',strtotime($dos));
                            }
                            if($rowFetch['pt_status'] != '') {
                                $changed_value = $pt_status[$rowFetch['changed_value']];
                            }
                            if($rowFetch['pt_account_status'] != '') {
                                $changed_value = $pt_ac_status[$rowFetch['changed_value']];
                            }
                            if($rowFetch['reason_code'] != '') {
                                $changed_value = $pt_ac_status[$rowFetch['changed_value']];
                            }
                            if($rowFetch['pt_appt_status'] != '') {
                                $changed_value = $pt_ac_status[$rowFetch['changed_value']];
                            }
                            if($rowFetch['ar_aging'] != '') {
                                $changed_value = $pt_ac_status[$rowFetch['changed_value']];
                            }



                            if($rowFetch['rule_id']==0 && $rowFetch['ss_id'] > 0 && $rowFetch['ss_type'] == 'schedule_status') {
                                $section_name = $rowFetch['ss_id'];
                                $tm_rule_name_str= $ss_rule_name[$section_name];
                            } else {
                                $section_name = $rowFetch['rule_id'];
                                $tm_rule_name_str= $rule_name[$section_name];
                            }
                            $cat_id = $rowFetch['cat_id'];
                            //$modified_by = $usernameArr[$rowFetch['operatorid']].'-'.$rowFetch['operatorid'];
                            $modified_by = $usernameArr[$rowFetch['operatorid']];

                            $assigned_date = date('m-d-Y',strtotime($rowFetch['added_on']));

                            $phyName='';
                            if($user_group) {
                                $user_groups_arr = explode(', ', $user_group);
                                $user_groups_arr = array_unique($user_groups_arr);
                                foreach($user_groups_arr as $group) {
                                    $phyName.= $user_groups[$group].'; ';
                                }
                            }
                            if($user_names) {
                                $user_names_arr = explode(',', $user_names);
                                array_walk($user_names_arr, 'trim');
                                $user_names_arr = array_unique($user_names_arr);
                                foreach($user_names_arr as $user) {
                                    $user=trim($user);
                                    $phyName.= $usernameArr[$user].'; ';
                                }
                            }
                            $PatientName='';
                            if(isset($rowFetch['patient_name']) && $rowFetch['patient_name']!='') {
                                $PatientName=$rowFetch['patient_name'].' - '.$rowFetch['patientid'];
                            } else {
                                $getPatientName = "SELECT id, CONCAT(lname,', ',fname) as name FROM patient_data WHERE id = '".$rowFetch['patientid']."'";
                                $PatientName_rs = imw_query($getPatientName);
                                $ptName_row = imw_fetch_assoc($PatientName_rs);
                                if($ptName_row['name']!='' && $ptName_row['id']!='') {
                                    $PatientName= $ptName_row['name'].' - '.$ptName_row['id'];
                                }
                            }

                            $data_attr='';
                            if(isset($rowFetch['section_name']) && $rowFetch['section_name']=='Accounting Notes') {
                                $data_attr=' data-section="'.$rowFetch['payment_comtId'].'" ';
                            }

                            $htmlStr .= '<tr class="even-odd-test-task">';
							$htmlStr .= '
							<td width="3%" class="text-center" style="vertical-align:top!important;">
								<div class="checkbox">
									<input id="checkbox'.$id.'" type="checkbox" name="chk_record[]" value="'.$id.'" '.$data_attr.' class="chk_record">
									<label for="checkbox'.$id.'">&nbsp;</label>
								</div>
							</td>';

                            $htmlStr .= '<td width="10%" style="vertical-align:top!important;">'.$assigned_date.'</td>';
                            $htmlStr .= '<td width="7%" style="vertical-align:top!important;">'.$categories[$cat_id].'</td>';
                            $htmlStr .= '<td width="8%" style="vertical-align:top!important;">'.$tm_rule_name_str.'</td>';
                           // $htmlStr .= '<td>'.$changed_value.'</td>';
                            $htmlStr .= '<td width="12%" style="vertical-align:top!important;"><span class="text_purple" onclick="LoadWorkView(\''.$rowFetch['patientid'].'\')">'.$PatientName.'</span></td>';
							$htmlStr .= '<td width="7%" style="vertical-align:top!important;">'.$dos.'</td>';
							$htmlStr .= '<td width="24%" style="vertical-align:top!important;">'.$comments.'</td>';
							$htmlStr .= '<td width="10%" style="vertical-align:top!important;">'.$modified_by.'</td>';

                            $htmlStr .= '<td width="19%" style="vertical-align:top!important;">'.trim($phyName).'</td>';

							$htmlStr .= '</tr>';
                        }

                        if(empty($htmlStr) == false) $htmlStr = '<tbody>'.$htmlStr.'</tbody>';
						$emptyRec = false;
                    }else{
						$htmlStr .= '<tbody><tr><td colspan="9" class="text-center">No task assigned</td></tr></tbody>';
						$emptyRec = true;
					}

					$finalHtml = '';
					if(empty($htmlStr) == false){
						$finalHtml = '<div class="pt5 pdl_10 scroll-content mCustomScrollbar dynamicRightPadding" id="tests-tasks" style="height:'.($_SESSION['wn_height']-340).'px">
						<table class="table table-bordered table-striped table-hover sortable" id="console_data">'.$htmlHeadTopStr.$htmlHeadStr.$htmlStr.'</table></div>';
					}

					if(empty($finalHtml) == false ){
						if($emptyRec == false) $finalHtml = $finalHtml.'<div class="col-sm-12 text-center pt5 pdb5"><button onclick="del_messages(\'complete_rule_task\')" class="btn btn-success">Mark as Done</button></div>';
						if($emptyRec == false) $finalHtml = $finalHtml.'<div class="col-sm-12 text-center pt5 pdb5"></div>';
						$htmlStr = $finalHtml;
					}
				}

				echo $htmlStr;
			break;


		case 'tm_complete_rule_task':
			$selID = array();
			$selID = explode(',', $_REQUEST['chs_ids']);
			$counter = 0;

			if(count($selID) > 0){
				foreach($selID as $taskId){
                    if(strpos($taskId, '||')==false) {
                        $sql ="Update tm_assigned_rules set status=1 where id='$taskId' ";
                        $updateStat=imw_query($sql);
                    }

                    if(strpos($taskId, '||')!==false) {
                        $comId_arr=explode('||', $taskId);
                        if(isset($comId_arr[1]) && trim($comId_arr[1])!=''){
                            $comId=trim($comId_arr[1]);
                            $taskId=trim($comId_arr[0]);

                            $valArr = array();
                            $valArr['task_modify_date'] = date('Y-m-d H:i:s');
                            $valArr['task_assign'] = 1;
                            $valArr['task_done'] = 2;

                            $notesUpdateStat = UpdateRecords($comId,'commentId',$valArr,'paymentscomment');
                            if($notesUpdateStat){
                                $sql ="Update tm_assigned_rules set status=1 where payment_comtId='$comId' and id='$taskId' ";
                                imw_query($sql);
                            }
                            if($notesUpdateStat) $counter++;
                        }
                    }

					if($updateStat) $counter++;
				}
			}

			echo json_encode($counter);
		break;


    case 'save_to_folder':
        $selID = array();
        $selID = explode(',', trim($_REQUEST['msg_id']));
        $folder_id = $_REQUEST['folder_id'];
        $counter=0;
        if (count($selID)>0) {
            foreach($selID as $msgId) {
                $sql = "Update user_messages set saved_folder_id='$folder_id' where user_message_id='$msgId' ";
                $updateStat = imw_query($sql);

                if ($updateStat)
                    $counter++;
            }
        }

        echo json_encode($counter);
        break;
}

function prettyViewArr($arr) {
    $str='';
    if(!empty($arr)){
        foreach($arr as $key=>$value) {
             $str.='<b>'.ucfirst($key).'</b> => '.$value.'<br>';
        }
    }
    return $str;
}
?>
