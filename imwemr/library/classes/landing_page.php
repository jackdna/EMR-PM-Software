<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

/********************************************************
/* Purpose: landing physicn page helper class.
/* Access Type: Indirect Access.
********************************************************/
class landing_physician
{
	//to hold current logged in physician/provider/surgeon id
	var $VAR_int_prov;
	//to hold logged in facility
	var $VAR_int_fac;
	//to hold patient location (room, priority, status) detail
	var $VAR_pt_location_arr;
	//to hold all procedures listing
	var $VAR_procedure_arr;
	//to hold all facilities list
	var $VAR_facility_arr; 
	//to hold priority names
	var $VAR_priority;
	//to hold dilated patient list
	var $VAR_pt_dilated;
	
	###################################################################
	# constructor function to set commonally used variable on page
	###################################################################
	function __construct()
	{
		//get physician id to show detail
		$res_fellow_sess = isset($_SESSION['res_fellow_sess']) ? trim($_SESSION['res_fellow_sess']) : '';
		if($res_fellow_sess != "" && isset($res_fellow_sess))
		{
			$this->VAR_int_prov = $res_fellow_sess;	
		}
		else
		{
			$this->VAR_int_prov = (isset($_SESSION["authId"]) && $_SESSION["authId"] != "") ? $_SESSION["authId"] : 0;	
		}
		//get login facility
		if((int)$_SESSION["login_facility"]){$this->VAR_int_fac =(int)$_SESSION["login_facility"];}
		
		//create array for logged in physician pt room status for current date
		$this->FUNC_room_assigned();
		//create array for procedures
		$this->FUNC_procedures();
		//create array for facilities
		$this->FUNC_fac_arr();
		//create pt priority 
		$this->VAR_priority = array(0=>'Normal',1=>'Priority 1',2=>'Priority 2',3=>'Priority 3');
	}
	
	###################################################################
	# function to return img for given pt status id
	###################################################################
	function FUNC_status_img($id)
	{
		if($id)
		{
			//appointment status name array
			$appt_status_img=array("1"=>"status3.png", "2"=>"status2.png", "3"=>"status1.png", "4"=>"status4.png", "6"=>"");
			$appt_status_name=array("1"=>"Doctor", "2"=>"Technician", "3"=>"Test", "4"=>"Waiting Room", "6"=>"Done");
			if($appt_status_img[$id])
			return '<img src="'.$GLOBALS['webroot'].'/library/images/'.$appt_status_img[$id].'" alt="'.$appt_status_name[$id].'" title="'.$appt_status_name[$id].'" />';
			else return $appt_status_name[$id];
		}//else return $id;
	}
	
	###################################################################
	# function to create room array for patients for logged in physicn 
	# to current date
	###################################################################
	function FUNC_room_assigned()
	{
		//get room assigned array to avoid reoccuring of query
		$pt_rm_q=imw_query("SELECT pla.app_room, pla.patientId, pla.pt_with FROM patient_location pla JOIN (Select MAX(pl.patient_location_id) as id From patient_location pl LEFT JOIN schedule_appointments sa On pl.sch_id = sa.id WHERE pl.cur_date = '".date("Y-m-d")."' and sa.sa_doctor_Id='".$this->VAR_int_prov."' GROUP BY pl.patientId) X ON pla.patient_location_id = X.id ");
		if(imw_num_rows($pt_rm_q)>=1)
		{
			while($pt_rm_d=imw_fetch_object($pt_rm_q)){
				$this->VAR_pt_location_arr['room'][$pt_rm_d->patientId]=$pt_rm_d->app_room;
				$this->VAR_pt_location_arr['status'][$pt_rm_d->patientId]=$pt_rm_d->pt_with;
			}
		}
	}
	
	###################################################################
	# function to create procedure arr
	###################################################################
	function FUNC_procedures()
	{
		//create procedure arr
		$q_proc=imw_query("select id,proc,acronym from slot_procedures where proc!='' and (procedureId=id || procedureId=0) and  active_status='yes' and source='' group by proc");
		while($proc_data=imw_fetch_object($q_proc))
		{
			$proc_name=($proc_data->acronym)?$proc_data->acronym:$proc_data->proc;
			$this->VAR_procedure_arr[$proc_data->id]=$proc_name;
		}		
	}
	
	###################################################################
	# function create facility arr
	###################################################################
	function FUNC_fac_arr()
	{
		//create facility array
		$q_fac=imw_query("select name, id  from facility ");
		while($d_fac=imw_fetch_object($q_fac))
		{
			$this->VAR_facility_arr[$d_fac->id]=$d_fac->name;	
		}	
	}
	
	###################################################################
	# function to handle various display message requests
	# parameters and there possible values
	# $cat : messages, direct, erx
	###################################################################
	function top_five_messages($cat)
	{
		$msgConsoleObj = new msgConsole();
		if($cat=='messages')
		{
			$top_5_physician_msg='';
			//get physician message using already created funtion
			$result_data_arr = $msgConsoleObj->get_messages_reminders('my_inbox',1,5);
			if(count($result_data_arr[0])==0){$top_5_physician_msg='<div class="alert alert-info">You don\'t have any Message/Reminder</div>';}
			//echo'<pre>';print_r($result_data_arr);echo'</pre>';
			
			foreach($result_data_arr as $key => $val_arr_1)
			{
				if(is_array($val_arr_1)){
					foreach($val_arr_1 as $val_arr){
						$msg_id 		= $val_arr['user_message_id'];
						//$sender_id 		= $val_arr['message_sender_id'];
						$msg_subject 	= $val_arr['message_subject'];
						//$msg_patientName= $val_arr['patient_name'];
						//$msg_patientId	= $val_arr['message_patient_id'];
						$msg_time		= explode(' ',$val_arr['msg_send_date']);
						$msg_sent_from	= $val_arr['message_sender_name'];
						$read=($val_arr['message_read_status'])?'read':'unread';
						$top_5_physician_msg.='<div class="phyrow">
						
						<div class="row">
							<div class="col-sm-1">
								<figure><label class="control control--checkbox"> <input class="landing_messages_checked" type="checkbox" value="'.$msg_id.'" /><div class="control__indicator"></div></label></figure>
							</div>
							<div class="col-sm-9" onClick="top.physician_console(\'message_reminders_opt\', '.$msg_id.')">
								<div class="row">
									<div class="col-sm-10"><span class="'.$read.'">'.$msg_sent_from.'</span><br>'.$msg_subject.'</div>
									<div class="col-sm-2">'.$msg_time[1].'&nbsp;'.$msg_time[2].'</div>
								</div>
								
							</div>
							<div class="col-sm-2 text-right">
								<!-- img src="'.$GLOBALS['webroot'].'/library/images/compose.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Edit" / -->
								<img src="'.$GLOBALS['webroot'].'/library/images/forward.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reply" onClick="top.physician_console(\'message_reminders_opt\', '.$msg_id.', \'reply\')" /> 
								<img src="'.$GLOBALS['webroot'].'/library/images/reply.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Forward" onClick="top.physician_console(\'message_reminders_opt\', '.$msg_id.', \'forward\')" />
							</div>
						</div>
						
					</div>';
					}
				}
			}
			
			//print physician message html
			echo $top_5_physician_msg;	
		}
		elseif($cat=='direct')
		{
			//get provider email/username to get direct message
			$provider_email = $msgConsoleObj->pt_direct_credentials($_SESSION['authId']);
			 $rq_qry = "SELECT *, DATE_FORMAT(local_datetime,'".get_sql_date_format()." %h:%i %p') as local_datetime from direct_messages WHERE `to_email` = '".$provider_email["email"]."' and imedic_user_id = '".$_SESSION['authId']."' and del_status = 0 and folder_type=1 order by id DESC LIMIT 0,5";
			$rs_qry=imw_query($rq_qry)or die(imw_error());
			if(imw_num_rows($rs_qry)==0)$top_5_direct_msg='<div class="alert alert-info">You don\'t have any Direct Message</div>';
			
			while($direct_data=imw_fetch_object($rs_qry))
			{
				$msg_time		= explode(' ',$direct_data->local_datetime);
				$from_email		= (strlen($direct_data->from_email)>29)?substr($direct_data->from_email,0,29).'..':$direct_data->from_email;
				//add class to identify unread messages
				$read=($direct_data->is_read)?'read':'unread';
				//add icon to show message have an attachment
				$attachment_icon="";
				if($this->have_attachment($direct_data->id))
				{
					$attachment_icon='<img src="'.$GLOBALS['webroot'].'/library/images/attachment.png" alt="Attachment" data-toggle="tooltip" data-placement="bottom" title="Attachment" width="28px" /> ';
				}
				
				$top_5_direct_msg.='<div class="phyrow">
					<div class="row">
						<div class="col-sm-1">
							<figure><label class="control control--checkbox"><input class="landing_direct_checked" type="checkbox" value="'.$direct_data->id.'" /><div class="control__indicator"></div></label></figure>
						</div>
						<div class="col-sm-10" onClick="top.physician_console(\'direct_messages\', '.$direct_data->id.')">
							<div class="row">
								<div class="col-sm-10"><span class="'.$read.'">'.$attachment_icon.' '.$from_email.'</span><br>'.$direct_data->subject.'</div>
								<div class="col-sm-2">'.$msg_time[1].'&nbsp;'.$msg_time[2].'</div>
							</div>
							
						</div>
						<div class="col-sm-1 text-right">
							<img src="'.$GLOBALS['webroot'].'/library/images/forward.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reply" onClick="top.physician_console(\'direct_messages\', '.$direct_data->id.', \'reply\')" />
						</div>
					</div>
					
				</div>';
			}
			echo $top_5_direct_msg;	
		}
		elseif($cat=='urgent')
		{
			$top_5_physician_msg='';
			//get physician message using already created funtion
			$result_data_arr = $msgConsoleObj->get_urgent_messages_reminders();
			//if(count($result_data_arr[0])==0){$top_5_physician_msg='<div class="alert alert-info">You don\'t have any Message/Reminder</div>';}
				foreach($result_data_arr as $val_arr){
					if($val_arr['msg_type']==0)//proceed only for message not for task
					{
						$msg_id 		= $val_arr['user_message_id'];
						//$sender_id 	= $val_arr['message_sender_id'];
						$msg_subject 	= $val_arr['message_subject'];
						//$msg_patientName= $val_arr['patient_name'];
						//$msg_patientId= $val_arr['message_patient_id'];
						$msg_time		= explode(' ',$val_arr['msg_send_date']);
						$msg_sent_from	= $val_arr['message_sender_name'];
						$read=($val_arr['message_read_status'])?'read':'unread';
						$top_5_physician_msg.='
						<div class="phyrow">
						<div class="row border-dashed">
							<div class="col-sm-10" onClick="top.physician_console(\'message_reminders_opt\', '.$msg_id.')">
								<div class="row">
									<div class="col-sm-10"><span class="'.$read.'">'.$msg_sent_from.'</span><br>'.$msg_subject.'</div>
									<div class="col-sm-2">'.$msg_time[1].'&nbsp;'.$msg_time[2].'</div>
								</div>

							</div>
							<div class="col-sm-2 text-right">
								<!-- img src="'.$GLOBALS['webroot'].'/library/images/compose.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Edit" / -->
								<img src="'.$GLOBALS['webroot'].'/library/images/forward.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Reply" onClick="top.physician_console(\'message_reminders_opt\', '.$msg_id.', \'reply\')" /> 
								<img src="'.$GLOBALS['webroot'].'/library/images/reply.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Forward" onClick="top.physician_console(\'message_reminders_opt\', '.$msg_id.', \'forward\')" />
							</div>
						</div>
						</div>';
					}
				}
			
			
			//print physician message html
			return $top_5_physician_msg;	
		}
	}
	
	###################################################################
	# function to check is msg have any attachment
	###################################################################
	function have_attachment($id)
	{
		 $qry_str="select id from direct_messages_attachment where direct_message_id='$id'";
		 $qry=imw_query($qry_str);
		 if(imw_num_rows($qry)>=1)
		 return true;
		else
		return false;
	}
	
	//Function to get Distance values
	function get_dis_values($visid){
		$ar=array();
		if(!empty($visid)){
		$sql = "SELECT sel_od, txt_od, sel_os, txt_os, sec_indx FROM chart_acuity where id_chart_vis_master = '".$visid."' AND sec_name='Distance' AND sec_indx IN (1,2) ";
		$res = sqlStatement($sql);
		for($i=1; $row=sqlFetchArray($res); $i++){
			$c = $row["sec_indx"];
			$ar["vis_dis_od_sel_".$c]=$row["sel_od"];
			$ar["vis_dis_od_txt_".$c]=$row["txt_od"];
			$ar["vis_dis_os_sel_".$c]=$row["sel_os"];
			$ar["vis_dis_os_txt_".$c]=$row["txt_os"];
		}
		}
		
		return $ar;
	}
	###################################################################
	# function to get list of patient that are dilated today
	###################################################################
	function get_dilated_patient()
	{
		$dos=date('Y-m-d');
		/*
		$qry_str = "SELECT c1.date_of_service,c1.patient_id  FROM `chart_master_table` c1
					LEFT JOIN chart_dialation c2 ON c1.id = c2.form_id
					WHERE c1.purge_status = '0' and c1.delete_status='0' 
					AND (c1.date_of_service='$dos' OR c2.exam_date='$dos') AND
					c2.purged='0' AND c2.noDilation='0' AND (sumDilation_od !='' OR sumDilation_os !='')
					ORDER BY c1.date_of_service desc, c2.exam_date, c1.id desc";
		*/			
		$qry_str = " SELECT c1.date_of_service AS dateofservice ,c1.patient_id FROM `chart_master_table` c1 LEFT JOIN chart_dialation c2 ON c1.id = c2.form_id WHERE c1.purge_status = '0' and c1.delete_status='0' AND c1.date_of_service='$dos' AND c2.purged='0' AND c2.noDilation='0' AND (sumDilation_od !='' OR sumDilation_os !='') 
				UNION 
				SELECT c1.date_of_service AS dateofservice ,c1.patient_id FROM `chart_master_table` c1 LEFT JOIN chart_dialation c2 ON c1.id = c2.form_id WHERE c1.purge_status = '0' and c1.delete_status='0' AND c2.exam_date='$dos' AND c2.purged='0' AND c2.noDilation='0' AND (sumDilation_od !='' OR sumDilation_os !='') ORDER BY dateofservice desc; ";
		$qry=imw_query($qry_str);
		if(imw_num_rows($qry)>=1)
		{
			while($appt_data=imw_fetch_object($qry))
			{
				$this->VAR_pt_dilated[$appt_data->patient_id]=$appt_data->dateofservice;
			}
		}
	}
	###################################################################
	# function to display today scheduled patients for logged in
	# physician
	###################################################################
	function today_appts()
	{
		$this->get_dilated_patient();
		$today_appts='<div class="table-responsive checkpatient">';
		$qry_str = 'select sa.pt_priority, sa.sa_patient_id, sa.sa_app_starttime, sa.sa_patient_name, sa.sa_patient_id,
					sa.procedureid, sa.sa_patient_app_status_id, cc.ccompliant,
					cv.id as id_chart_vis_master,
					co.cd_val_od, co.cd_val_os,
					ci.multiple_pressure 
					
					FROM schedule_appointments sa USE INDEX(sa_multiplecol)
					LEFT JOIN chart_master_table cm ON (sa.sa_patient_id=cm.patient_id and sa.sa_app_start_date=cm.date_of_service)
					LEFT OUTER JOIN chart_master_table cm2 ON (sa.sa_patient_id=cm2.patient_id and sa.sa_app_start_date=cm2.date_of_service AND 
												(cm.date_of_service < cm2.date_of_service OR cm.date_of_service = cm2.date_of_service AND cm.id < cm2.id))
					LEFT JOIN chart_left_cc_history cc ON cc.form_id=cm.id
					LEFT JOIN chart_vis_master cv ON cc.form_id=cv.form_id
					LEFT JOIN chart_optic co ON cc.form_id=co.form_id
					LEFT JOIN chart_iop ci ON cc.form_id=ci.form_id
					
					where sa.sa_facility_id IN ('.$this->VAR_int_fac.') 
					AND sa.sa_doctor_id = "'.$this->VAR_int_prov.'" 
					AND sa.sa_test_id = 0 
					AND sa.sa_patient_app_status_id NOT IN (203,201,18,19,20,11,3) 
					AND IF( sa.sa_patient_app_status_id =271, sa.sa_patient_app_show =0, sa.sa_patient_app_show <>2 ) 
					AND "'.date('Y-m-d').'" between sa.sa_app_start_date and sa.sa_app_end_date 
					AND cm2.id IS NULL
					ORDER BY sa.sa_app_starttime ASC';
				
		$qry=imw_query($qry_str);
		if(imw_num_rows($qry)>=1)
		{
			$today_appts.='<table class="table table-bordered table-hover table-striped">
			<tr class="tophead">
			<td align="center">Priority </td>
			<td align="center">Room# </td>
			<td align="center">Status </td>
			<td align="center">Appt. Time </td>
			<td>Patient name-ID </td>
			<td>Reason </td>
			<td>Chief Complaint </td>
			<td>PAG </td>
			<td align="center">Vision
			<!--<div class="scccbg">
			<div class="sect">SC</div>
			<div class="sect">CC </div>
			<div class="clearfix"></div>
			
			</div>--></td>
			<td align="center" class="iop">IOP</td>
			</tr>';
			while($appt_data=imw_fetch_object($qry))
			{
				$room='';
				$room=$this->VAR_pt_location_arr['room'][$appt_data->sa_patient_id];
				$priority='';
				$priority=($appt_data->pt_priority)?$this->VAR_priority[$appt_data->pt_priority]:'.....';
				$checkin_time='';
				$checkin_time=date("h:i A",strtotime($appt_data->sa_app_starttime));
				//get last added presure for iop from multiple_pressure array values
				$iop_arr=unserialize($appt_data->multiple_pressure);
				$presure_count=count($iop_arr);
				$iop[0]=""; $iop['od']=""; $iop['os']="";	
				
				for($i=0;$i<$presure_count;$i++){						
					if($i>0){$presure_arr=$iop_arr["multiplePressuer".($i+1)];}
					else{	$presure_arr=$iop_arr["multiplePressuer"]; }
					
					if($i>0){$sub_key_count=$i;}else{$sub_key_count="";}					
					
					//checking values from array in reverse order to get last one
					if(!empty($presure_arr['elem_tactTrgtOd'.$sub_key_count])||!empty($presure_arr['elem_tactTrgtOs'.$sub_key_count]))
					{
						$iop[0]="T<sub>T</sub>";
						$iop['od']=$presure_arr['elem_tactTrgtOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_tactTrgtOs'.$sub_key_count];	
					}
					elseif(!empty($presure_arr['elem_appTrgtOd'.$sub_key_count])||!empty($presure_arr['elem_appTrgtOs'.$sub_key_count]))
					{
						$iop[0]="T<sub>X</sub>";
						$iop['od']=$presure_arr['elem_appTrgtOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_appTrgtOs'.$sub_key_count];	
					}
					elseif(!empty($presure_arr['elem_puffOd'.$sub_key_count]) || !empty($presure_arr['elem_puffOs'.$sub_key_count]))
					{
						$iop[0]="T<sub>P</sub>";
						$iop['od']=$presure_arr['elem_puffOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_puffOs'.$sub_key_count];	
					}					
					elseif(!empty($presure_arr['elem_appOd'.$sub_key_count]) || !empty($presure_arr['elem_appOs'.$sub_key_count]))
					{
						$iop[0]="T<sub>A</sub>";
						$iop['od']=$presure_arr['elem_appOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_appOs'.$sub_key_count];	
					}					
					
				} //end for
			$pt_img='<img src="'.$GLOBALS['webroot'].'/library/images/calendar.png" alt=""/>';
			if($this->VAR_pt_dilated[$appt_data->sa_patient_id]){
				$pt_img='<img src="'.$GLOBALS['webroot'].'/library/images/dilation.png" width="35px" alt="Dilated Patient" title="Dilated Patient"/>';
			}
			$today_appts_tmp='
			<tr>
			<td align="center">'.$priority.'</td>
			<td align="center">';
			$today_appts_tmp.=($room)?$room:'.....';
			$today_appts_tmp.='</td>
			<td align="center">'.$this->FUNC_status_img($this->VAR_pt_location_arr['status'][$appt_data->sa_patient_id]).'</td>
			<td align="center">'.$checkin_time.'</td>
			<td style="word-wrap:break-word" class="pointer" onclick="window.top.LoadWorkView(\''.$appt_data->sa_patient_id.'\');">'.$pt_img.$appt_data->sa_patient_name.'-'.$appt_data->sa_patient_id.'</td>
			<td>'.$this->VAR_procedure_arr[$appt_data->procedureid].'</td> 
			<td>';
			$cc='';
			$cc=(strlen($appt_data->ccompliant)>30)?substr($appt_data->ccompliant,0,90).'...':$appt_data->ccompliant;
			if(strlen($appt_data->ccompliant)>30)
			{
				$cc = substr_replace($cc, '<br/>', 30, 0);
			}
			if(strlen($appt_data->ccompliant)>60)
			{
				$cc = substr_replace($cc, '<br/>', 70, 0);
			}
			$today_appts_tmp.=$cc;
			
			$id_chart_vis_master = $appt_data->id_chart_vis_master;
			$ar_dis = $this->get_dis_values($id_chart_vis_master);
			
			$today_appts_tmp.='</td>
			<td>
				<img src="'.$GLOBALS['webroot'].'/library/images/Patient-at-a-glanceicon2.png" title="Patient at a glance" width="35px" class="pointer" onclick="top.icon_popups(\'pt_at_glance\',\''.$appt_data->sa_patient_id.'\'); ">
			</td>
			<td>
			<div class="od">
			<ul>
			<li>OD</li>
			<li>'.$ar_dis["vis_dis_od_sel_1"].'</li>
			<li class="sc">'.$ar_dis["vis_dis_od_txt_1"].'</li>
			<li>'.$ar_dis["vis_dis_od_sel_2"].'</li>
			<li class="sc">'.$ar_dis["vis_dis_od_txt_2"].'</li>
			</ul>
			
			</div>
			<div class="clearfix"></div>
			<div class="os">
			<ul>
			<li>OS</li>
			<li>'.$ar_dis["vis_dis_os_sel_1"].'</li>
			<li class="sc">'.$ar_dis["vis_dis_os_txt_1"].'</li>
			<li>'.$ar_dis["vis_dis_os_sel_2"].'</li>
			<li class="sc">'.$ar_dis["vis_dis_os_txt_2"].'</li>
			</ul>
			
			</div>
			<div class="clearfix"></div>
			
			
			</td>
			<td><div class="od">
			<ul>
			<li>OD</li>
			<li class="sc">';
			$today_appts_tmp.=($iop[0])?$iop[0].':'.$iop['od']:'--';
			$today_appts_tmp.='</li>
			
			</ul>
			
			</div>
			<div class="clearfix"></div>
			<div class="os">
			<ul>
			<li>OS</li>
			<li class="sc">';
			$today_appts_tmp.=($iop[0])?$iop[0].':'.$iop['os']:'--';
			$today_appts_tmp.='</li>
			
			</ul>
			
			</div>
			<div class="clearfix"></div></td>
			
			</tr>';
			if($appt_data->pt_priority==2)
			{$str1.=$today_appts_tmp;}
			else
			{$str2.=$today_appts_tmp;}
			$today_appts_tmp='';
			 }
			$today_appts.=$str1.$str2.'</table>';
        }
        else
        {
            //show no record alert
            $today_appts.= '<div class="alert alert-info">No patient scheduled for today.</div>';
        }
		$today_appts.= '</div>';
		echo $today_appts;
	}
	
	###################################################################
	# function to show appt graph for pre and post lunch
	###################################################################
	function appt_summary()
	{
		// code to find the no. of appointments before and after the lunch time (specify lunch time as 12:00 if not defined)
		$before_lunchTm = "12:00:00";
		
		$beforeLunchApptsQry = 'select count(id) as appts_count FROM schedule_appointments USE INDEX(sa_multiplecol) where sa_facility_id IN ('.$this->VAR_int_fac.') and sa_doctor_id = "'.$this->VAR_int_prov.'" and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_starttime < "'.$before_lunchTm.'" and sa_app_starttime !="00:00:00" and "'.date('Y-m-d').'" between sa_app_start_date and sa_app_end_date ';
		
		$afterLunchApptsQry = 'select count(id) as appts_count FROM schedule_appointments USE INDEX(sa_multiplecol) where sa_facility_id IN ('.$this->VAR_int_fac.') and sa_doctor_id = "'.$this->VAR_int_prov.'" and sa_test_id = 0 and sa_patient_app_status_id NOT IN (203,201,18,19,20) AND IF( sa_patient_app_status_id =271, sa_patient_app_show =0, sa_patient_app_show <>2 ) and sa_app_starttime >= "'.$before_lunchTm.'" and "'.date('Y-m-d').'" between sa_app_start_date and sa_app_end_date ';
									
		$beforeLunchApptsQryObj = imw_query($beforeLunchApptsQry);
		$afterLunchApptsQryObj = imw_query($afterLunchApptsQry);
		
		$no_of_appts_beforeLunch = imw_fetch_assoc($beforeLunchApptsQryObj);
		$no_of_appts_afterLunch = imw_fetch_assoc($afterLunchApptsQryObj);
		
		$arrApptSummary=array();	
		$arrApptSummary[0]['kee']='AM';
		$arrApptSummary[0]['val']=$no_of_appts_beforeLunch['appts_count'];
		
		$arrApptSummary[1]['kee']='PM';
		$arrApptSummary[1]['val']=$no_of_appts_afterLunch['appts_count'];
		
		$appt_summary = '';		
		//for($i=0;$i<=10;$i++)
		//{
			$i = "";
		$appt_summary.='
		<div class="item">
			<h3>Appointment summary</h3>
			<div class="clearfix"></div>
			<div class="text-center">
			<div id="appt_summary_div'.$i.'" style="width:100%;height:380px;"></div>
			</div><!--
			<div class="clearfix"></div>
			<div class="avgwaitin">
				<h3>Avg. Wait Time</h3>
				<div class="clearfix"></div>
				<div class="procbar">
					<div class="timeproc"><span> Technician Time  </span>20 Min</div>
					<div class="physproc"><span> Physician   </span>15 Min</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
				<div class="procindica"> 
					<img src="'.$GLOBALS['webroot'].'/library/images/bludot.png" alt=""/> Time Tech  <img src="'.$GLOBALS['webroot'].'/library/images/greendot.png" alt=""/> Physician
				</div>
			</div>
			<div class="clearfix"></div>-->
		</div>';
		//}
		if($arrApptSummary)$arrApptSummary=json_encode($arrApptSummary);
		else $arrApptSummary='';
		
		return $arrApptSummary.'~:~'.$appt_summary;
	}
	
	###################################################################
	# function to show list of unfinalized charts for logged in 
	# physician
	###################################################################
	function unfinalized_chart()
	{
		require_once(dirname(__FILE__)."/work_view/wv_functions.php");
		require_once(dirname(__FILE__)."/work_view/Facility.php");
		$oFacility = new Facility("HQ");
		$arrChartTimer = $oFacility->getChartTimers();
	
		$un_chart='<div class="table-responsive respotable">';
		//get all unfinalized charts in asc order
		$q_unfinalized_chart=imw_query("select cm.id,cm.patient_id, date_format(cm.date_of_service,'".get_sql_date_format('','y')."') as date_of_service, cm.providerId, cm.facilityid,
		pd.fname, pd.mname, pd.lname ,
		cm.date_of_service AS date_of_service_db, 
		cm.ptVisit, cm.testing, 
		cm.create_dt
		FROM chart_master_table cm 
		LEFT JOIN patient_data pd ON cm.patient_id=pd.id
		where cm.finalize='0' and cm.delete_status = '0' and pd.id != '0' and cm.not2show = '0' and cm.providerId='".$this->VAR_int_prov."'");
		if(imw_num_rows($q_unfinalized_chart))
		{
			$un_chart.='<table class="table table-bordered table-striped table-hover">
			<thead>
				<tr class="unfinhead">
				  <th width="10%"><!-- <label class="control control--checkbox"><input type="checkbox"/><div class="control__indicator"></div></label>--> DOS</th>
				  <th width="30%">Patient Name</th>
				  <th width="20%">Visit Type</th>
				  <th width="20%">Finalize By</th>
				  <th width="20%">Facility</th>
				</tr>
			  </thead>
			<tbody>';
			$sr=0;
			while($d_unfinalized_chart=imw_fetch_object($q_unfinalized_chart))
			{	
				$finalize_by="";
				$docId = $d_unfinalized_chart->providerId;
				if(!empty($docId)){
					$strReview = ($arrChartTimer["review"] * 24)." hours ";
					$strFinal = ($arrChartTimer["finalize"] * 24)." hours ";
					$strWarn = $strFinal." - ".$strReview;
					
					$dos_ymd="";
					$dateOfService = $d_unfinalized_chart->date_of_service_db;
					if(empty($dateOfService) || ($dateOfService=="0000-00-00")){
						$dos_ymd = getDateFormatDB($d_unfinalized_chart->create_dt);
					}else{
						$dos_ymd = $dateOfService;
					}
					
					//Check
					if(!empty($dos_ymd) ){ //&& isDtPassed($dos_ymd, $strWarn)						
						$finalizeDt = dtCalc($dos_ymd,"+".$strFinal,phpDateFormat());
						$finalize_by=$finalizeDt;
					}
				}
				
				$visit_type="";
				$ptVisit = trim($d_unfinalized_chart->ptVisit);
				$testing = trim($d_unfinalized_chart->testing);
				if(!empty($ptVisit)){  $visit_type=$ptVisit; }
				if(!empty($testing)){ 
					if(!empty($visit_type)){ $visit_type.=" - "; }
					$visit_type.=$testing; 
				}
				
			
				$sr++;
				
				$patient_name='';
				$patient_name=core_name_format($d_unfinalized_chart->lname,$d_unfinalized_chart->fname,$d_unfinalized_chart->mname);
		
                $un_chart.='<tr>
                <td data-label="DOS : ">  <!--<label class="control control--checkbox"><input type="checkbox"/>
                      <div class="control__indicator"></div>
                    </label>--><a class="text_purple" href="javascript:;" onclick="window.top.LoadWorkView(\''.$d_unfinalized_chart->patient_id.'\');">'.$d_unfinalized_chart->date_of_service.'</a></td>
                <td data-label="Patient Name : ">'.$patient_name.' - '.$d_unfinalized_chart->patient_id.'</td>
		<td data-label="Visit Type : ">'.$visit_type.'</td>
                <td data-label="Finalize By : ">'.$finalize_by.'</td>
                <td data-label="Facility : ">'.$this->VAR_facility_arr[$d_unfinalized_chart->facilityid].'</td>
                </tr>';
			}
			$un_chart.='</tbody>
			</table>';
		}//end of checking if we do have unfinalized chart for current physician
		else
		{$un_chart.="<div class='alert alert-info'>No unfinalized chart found.</div>";}
		$un_chart.='</div><div class="clearfix"></div>';
		echo $un_chart;
	}
	
	###################################################################
	# function to show list of uninterpreated tests for logged in
	# physician
	###################################################################
	function un_int_test()
	{
		$un_int_test='<div class="table-responsive respotable">';
		$msgConsoleObj = new msgConsole();
		$result_data_arr = $msgConsoleObj->get_tests_tasks('tests');
		$tableElem = '';
		if(count($result_data_arr)>0)
		{
			$un_int_test = '<table class="table table-bordered table-striped table-hover">
							<thead>
								<tr class="unfinhead">
								  <th> <!--<label class="control control--checkbox"><input type="checkbox"/>
								  <div class="control__indicator"></div>
								</label>--> DOS </th>
								  <th>Patient Name </th>
								  <th>Test Name</th>
								  <th>Comments </th>
								</tr>
							  </thead>
							<tbody>';
							
			$ar_pt_info = array();	
			$printed_test_rows = 0;		
			for($i = 0; $i<count($result_data_arr); $i++)
			{ 
				$val_arr = $result_data_arr[$i];
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
									
				$un_int_test .= '
				<tr>
					<td data-label="Dos : ">  <!--<label class="control control--checkbox">
					<input name="chk_record[]" type="checkbox" value="'.$val_arr['testName'].'-'.$val_arr['main_id'].'" />
					<div class="control__indicator"></div>
					</label>--><a class="text_purple" href="javascript:;" onclick="loadPtThenTEST(\''.$pt_id.'\',\''.$val_arr['test_js_key'].'\',\''.$val_arr['main_id'].'\');">'.$val_arr['taskDate'].'</a></td>
					<td data-label="Patient Name : "> '.$pt_name.' </td>
					<td data-label="Test Name : "> '.strtoupper($val_arr['testDesc']).' </td>
					<td data-label="Comments : "> '.$val_arr['comments'].'</td>
				</tr>';
				//<a class="a_clr1" title="Click to load Chart" href="javascript:;" onclick="LoadWorkView(\''.$pt_id.'\');">'.$pt_name.'</a>
				$printed_test_rows++;
				if($printed_test_rows>14) break;
			}
			$un_int_test .= '</tbody></table>';
		}else{$un_int_test='<div class="alert alert-info">No Un-Interpreted Tests Found.</div>';}
		$un_int_test .= '</div><div class="clearfix"></div>';
		echo $un_int_test;	
	}
}

/********************************************************
/* Purpose: landing technician page helper class.
/* Access Type: Indirect Access.
*********************************************************/

class landing_technician extends landing_physician
{
	//to hold current logged in physician/provider/surgeon id
	//var $VAR_int_prov;
	
	###################################################################
	# constructor function to set commonally used variable on page
	###################################################################
	function __construct()
	{
		//call physician class constructor to initialize basic variables declared in physician class
		parent::__construct(); 
	}
	
	###################################################################
	# function to get list physicians to be followed by technician
	###################################################################
	function get_main_att_phys()
	{
		$result_return = '';
		$user_type_arr[1] = 'PHYSICIAN';
		$user_type_arr[12] = 'ATTENDING PHYSICIAN';
		$query = "SELECT id, fname, mname, lname, user_type FROM users WHERE (user_type = 1 or user_type = 12) and  delete_status = 0 ORDER BY lname";
		$phy_result_obj = imw_query($query);
		if(imw_num_rows($phy_result_obj)>0)
		{
			while($phy_data = imw_fetch_assoc($phy_result_obj))
			{
				$result_return .= '
				<div class="phyrow">
					<strong>'.core_name_format($phy_data['lname'],$phy_data['fname'],$phy_data['mname']).'</strong><br>
					'.$user_type_arr[$phy_data['user_type']].' <div class="followbutton">';
				if($_SESSION['res_fellow_sess']==$phy_data['id'])
				{
					$result_return .= '<a class="folwbut hvr-rectangle-out" href="javascript:void(0)" role="button">Following</a>';	
				}
				else
				{
				$result_return .= '<a class=" folwbut hvr-rectangle-out" href="javascript:void(0)" onClick="follow_main_att_phy('.$phy_data['id'].');" role="button">Follow</a>';
				}
				$result_return .= '</div></div>';
			}
		}
		return $result_return;
	}
	
	###################################################################
	# function to get list of patient with status CI for logged in user
	###################################################################
	function checked_patient()
	{
		$checked_patient='<div class=" table-responsive checkpatient">';
		$checked_patient.='<table class="table table-bordered table-hover table-striped">
		<tr class="tophead">
			<td width="5%" align="center">Priority </td>
			<td width="5%" align="center">Room# </td>
			<td width="5%" align="center">Status </td>
			<td width="5%" align="center">Appt. Time </td>
			<td width="31%">Patient name-ID </td>
			<td width="7%">Reason </td>
			<td width="22%">Chief Complaint </td>
			<td width="10%" align="center">Vision</td>
			<td width="10%" align="center" class="iop"><span class="sect"> IOP </span>    <span class="sect">C:D</span></td>
		</tr>';
		$qry_str = 'select sa.pt_priority, sa.sa_patient_id, sa.sa_app_starttime, sa.sa_patient_name, sa.sa_patient_id,
					sa.procedureid, sa.sa_patient_app_status_id, cc.ccompliant,
					cv.id as id_chart_vis_master,
					co.cd_val_od, co.cd_val_os,
					ci.multiple_pressure 
					
					FROM schedule_appointments sa  USE INDEX(sa_multiplecol)
					LEFT JOIN chart_left_cc_history cc ON (sa.sa_patient_id=cc.patient_id and sa.sa_app_start_date=cc.date_of_service)
					LEFT JOIN chart_vis_master cv ON cc.form_id=cv.form_id
					LEFT JOIN chart_optic co ON cc.form_id=co.form_id
					LEFT JOIN chart_iop ci ON cc.form_id=ci.form_id
					
					where sa.sa_facility_id IN ('.$this->VAR_int_fac.') 
					AND sa.sa_doctor_id = "'.$this->VAR_int_prov.'" 
					AND sa.sa_test_id = 0 
					AND sa.sa_patient_app_status_id=13 
					AND "'.date('Y-m-d').'" between sa.sa_app_start_date and sa.sa_app_end_date 
					ORDER BY sa.sa_app_starttime ASC ';
		
	
		$qry=imw_query($qry_str);
		if(imw_num_rows($qry)>=1)
		{
			while($appt_data=imw_fetch_object($qry))
			{
				$room='';
				$room=$this->VAR_pt_location_arr['room'][$appt_data->sa_patient_id];
				$priority='';
				$priority=($appt_data->pt_priority)?$this->VAR_priority[$appt_data->pt_priority]:'.....';
				$checkin_time='';
				$checkin_time=date("h:i A",strtotime($appt_data->sa_app_starttime));
				//get last added presure for iop from multiple_pressure array values
				$iop_arr=unserialize($appt_data->multiple_pressure);
				$presure_count=count($iop_arr);
				if($presure_count>1)
				$presure_arr=$iop_arr["multiplePressuer".$presure_count];
				else
				$presure_arr=$iop_arr["multiplePressuer"];
				if($presure_count>1){
				$sub_key_count=$presure_count-1;
				}else{$sub_key_count="";}
				//checking values from array in reverse order to get last one
				if($presure_arr['elem_tactTrgtOd'.$sub_key_count])
				{
					$iop[0]="T<sub>T</sub>";
					$iop['od']=$presure_arr['elem_tactTrgtOd'.$sub_key_count];
					$iop['os']=$presure_arr['elem_tactTrgtOs'.$sub_key_count];	
				}
				elseif($presure_arr['elem_appTrgtOd'.$sub_key_count])
				{
					$iop[0]="T<sub>X</sub>";
					$iop['od']=$presure_arr['elem_appTrgtOd'.$sub_key_count];
					$iop['os']=$presure_arr['elem_appTrgtOs'.$sub_key_count];	
				}
				elseif($presure_arr['elem_puffOd'.$sub_key_count])
				{
					$iop[0]="T<sub>P</sub>";
					$iop['od']=$presure_arr['elem_puffOd'.$sub_key_count];
					$iop['os']=$presure_arr['elem_puffOs'.$sub_key_count];	
				}
				
				elseif($presure_arr['elem_appOd'.$sub_key_count])
				{
					$iop[0]="T<sub>A</sub>";
					$iop['od']=$presure_arr['elem_appOd'.$sub_key_count];
					$iop['os']=$presure_arr['elem_appOs'.$sub_key_count];	
				}
				$checked_patient.='
				<tr>
				<td align="center">'.$priority.'</td>
				<td align="center">';
				$checked_patient.=($room)?$room:'.....';
				$checked_patient.='</td>
				<td align="center">'.$this->FUNC_status_img($this->VAR_pt_location_arr['status'][$appt_data->sa_patient_id]).'</td>
				<td align="center">'.$checkin_time.'</td>
				<td style="word-wrap:break-word" class="pointer" onclick="window.top.LoadWorkView(\''.$appt_data->sa_patient_id.'\');"> <img src="'.$GLOBALS['webroot'].'/library/images/calendar.png" alt=""/>'.$appt_data->sa_patient_name.'-'.$appt_data->sa_patient_id.'</td>
				<td>'.$this->VAR_procedure_arr[$appt_data->procedureid].'</td> 
				<td>';
				$checked_patient.=(strlen($appt_data->ccompliant)>30)?substr($appt_data->ccompliant,0,30).'...':$appt_data->ccompliant;
				
				$id_chart_vis_master = $appt_data->id_chart_vis_master;
				$ar_dis = $this->get_dis_values($id_chart_vis_master);
				
				$checked_patient.='</td>
				<td>
				<div class="od">
				<ul>
				<li>OD</li>
				<li>'.$ar_dis["vis_dis_od_sel_1"].'</li>
				<li class="sc">'.$ar_dis["vis_dis_od_txt_1"].'</li>
				<li>'.$ar_dis["vis_dis_od_sel_2"].'</li>
				<li class="sc">'.$ar_dis["vis_dis_od_txt_2"].'</li>
				</ul>
				
				</div>
				<div class="clearfix"></div>
				<div class="os">
				<ul>
				<li>OS</li>
				<li>'.$ar_dis["vis_dis_os_sel_1"].'</li>
				<li class="sc">'.$ar_dis["vis_dis_os_txt_1"].'</li>
				<li>'.$ar_dis["vis_dis_os_sel_2"].'</li>
				<li class="sc">'.$ar_dis["vis_dis_os_txt_2"].'</li>
				</ul>
				
				</div>
				<div class="clearfix"></div>
				
				</td>
				<td><div class="od">
				<ul>
				<li>OD</li>
				<li class="sc">';
				$checked_patient.=($iop[0])?$iop[0].':'.$iop['od']:'--';
				$checked_patient.='</li>
				<li class="sc">';
				$checked_patient.=($appt_data->cd_val_od)?$appt_data->cd_val_od:'--';
				$checked_patient.='</li>
				</ul>
				
				</div>
				<div class="clearfix"></div>
				<div class="os">
				<ul>
				<li>OS</li>
				<li class="sc">';
				$checked_patient.=($iop[0])?$iop[0].':'.$iop['os']:'--';
				$checked_patient.='</li>
				<li class="sc">';
				$checked_patient.=($appt_data->cd_val_os)?$appt_data->cd_val_os:'--';
				$checked_patient.='</li>
				</ul>
				
				</div>
				<div class="clearfix"></div></td>
				
				</tr>';
			}
		}
		else
		{
			//show none record message
			$checked_patient.='<tr><td align="center" colspan="9"><div class="alert alert-info">No patient is Checked In.</div></td></tr>';	
		}
		$checked_patient.='</table>';	
		$checked_patient.='</div>';
		echo $checked_patient;
	}
	
	###################################################################
	# function to get list of patient with status ready for doctor for 
	# logged in user, this status is set from physician console
	###################################################################
	function ready4doctor()
	{
		$ready4doctor='<div class="table-responsive checkpatient">';
		$ready4doctor.='<table class="table table-bordered table-hover table-striped">
			<tr class="tophead">
				<td width="5%" align="center">Priority </td>
				<td width="5%" align="center">Room# </td>
				<td width="5%" align="center">Status </td>
				<td width="5%" align="center">Appt. Time </td>
				<td width="31%">Patient name-ID </td>
				<td width="7%">Reason </td>
				<td width="22%">Chief Complaint </td>
				<td width="10%" align="center">Vision</td>
				<td width="10%" align="center" class="iop"><span class="sect"> IOP </span>    <span class="sect">C:D</span></td>
			</tr>';
			
		$qry_str = 'select sa.pt_priority, sa.sa_patient_id, sa.sa_app_starttime, sa.sa_patient_name, sa.sa_patient_id,
			sa.procedureid, sa.sa_patient_app_status_id, cc.ccompliant,
			cv.id as id_chart_vis_master,
			co.cd_val_od, co.cd_val_os,
			ci.multiple_pressure 
			
			FROM schedule_appointments sa  USE INDEX(sa_multiplecol)
			LEFT JOIN chart_left_cc_history cc ON (sa.sa_patient_id=cc.patient_id and sa.sa_app_start_date=cc.date_of_service)
			LEFT JOIN chart_vis_master cv ON cc.form_id=cv.form_id
			LEFT JOIN chart_optic co ON cc.form_id=co.form_id
			LEFT JOIN chart_iop ci ON cc.form_id=ci.form_id
			
			where sa.sa_facility_id IN ('.$this->VAR_int_fac.') 
			AND sa.sa_doctor_id = "'.$this->VAR_int_prov.'" 
			AND sa.sa_test_id = 0 
			AND sa.sa_patient_app_status_id NOT IN (203,201,18,19,20) 
			AND IF( sa.sa_patient_app_status_id =271, sa.sa_patient_app_show =0, sa.sa_patient_app_show <>2 ) 
			AND "'.date('Y-m-d').'" between sa.sa_app_start_date and sa.sa_app_end_date ';
		$qry=imw_query($qry_str);
		if(imw_num_rows($qry)>=1)
		{
			while($appt_data=imw_fetch_object($qry))
			{
				if($this->VAR_pt_location_arr['status'][$appt_data->sa_patient_id]==1)
				{
					$room='';
					$room=$this->VAR_pt_location_arr['room'][$appt_data->sa_patient_id];
					$priority='';
					$priority=($appt_data->pt_priority)?$this->VAR_priority[$appt_data->pt_priority]:'.....';
					$checkin_time='';
					$checkin_time=date("h:i A",strtotime($appt_data->sa_app_starttime));
					//get last added presure for iop from multiple_pressure array values
					$iop_arr=unserialize($appt_data->multiple_pressure);
					$presure_count=count($iop_arr);
					if($presure_count>1)
					$presure_arr=$iop_arr["multiplePressuer".$presure_count];
					else
					$presure_arr=$iop_arr["multiplePressuer"];
					if($presure_count>1){
					$sub_key_count=$presure_count-1;
					}else{$sub_key_count="";}
					//checking values from array in reverse order to get last one
					if($presure_arr['elem_tactTrgtOd'.$sub_key_count])
					{
						$iop[0]="T<sub>T</sub>";
						$iop['od']=$presure_arr['elem_tactTrgtOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_tactTrgtOs'.$sub_key_count];	
					}
					elseif($presure_arr['elem_appTrgtOd'.$sub_key_count])
					{
						$iop[0]="T<sub>X</sub>";
						$iop['od']=$presure_arr['elem_appTrgtOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_appTrgtOs'.$sub_key_count];	
					}
					elseif($presure_arr['elem_puffOd'.$sub_key_count])
					{
						$iop[0]="T<sub>P</sub>";
						$iop['od']=$presure_arr['elem_puffOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_puffOs'.$sub_key_count];	
					}
					
					elseif($presure_arr['elem_appOd'.$sub_key_count])
					{
						$iop[0]="T<sub>A</sub>";
						$iop['od']=$presure_arr['elem_appOd'.$sub_key_count];
						$iop['os']=$presure_arr['elem_appOs'.$sub_key_count];	
					}
					$ready4doctor.='
					<tr>
					<td align="center">'.$priority.'</td>
					<td align="center">';
					$ready4doctor.=($room)?$room:'.....';
					$ready4doctor.='</td>
					<td align="center">'.$this->FUNC_status_img($this->VAR_pt_location_arr['status'][$appt_data->sa_patient_id]).'</td>
					<td align="center">'.$checkin_time.'</td>
					<td style="word-wrap:break-word" class="pointer" onclick="window.top.LoadWorkView(\''.$appt_data->sa_patient_id.'\');"> <img src="'.$GLOBALS['webroot'].'/library/images/calendar.png" alt=""/>'.$appt_data->sa_patient_name.'-'.$appt_data->sa_patient_id.'</td>
					<td>';
					$ready4doctor.=$this->VAR_procedure_arr[$appt_data->procedureid].'</td> 
					<td>';
					$ready4doctor.=(strlen($appt_data->ccompliant)>30)?substr($appt_data->ccompliant,0,30).'...':$appt_data->ccompliant;
					$id_chart_vis_master = $appt_data->id_chart_vis_master;
					$ar_dis = $this->get_dis_values($id_chart_vis_master);
					$ready4doctor.='</td>
					<td>
					<div class="od">
					<ul>
					<li>OD</li>
					<li>'.$ar_dis["vis_dis_od_sel_1"].'</li>
					<li class="sc">'.$ar_dis["vis_dis_od_txt_1"].'</li>
					<li>'.$ar_dis["vis_dis_od_sel_2"].'</li>
					<li class="sc">'.$ar_dis["vis_dis_od_txt_2"].'</li>
					</ul>
					
					</div>
					<div class="clearfix"></div>
					<div class="os">
					<ul>
					<li>OS</li>
					<li>'.$ar_dis["vis_dis_os_sel_1"].'</li>
					<li class="sc">'.$ar_dis["vis_dis_os_txt_1"].'</li>
					<li>'.$ar_dis["vis_dis_os_sel_2"].'</li>
					<li class="sc">'.$ar_dis["vis_dis_os_txt_2"].'</li>
					</ul>
					
					</div>
					<div class="clearfix"></div>
					
					
					</td>
					<td><div class="od">
					<ul>
					<li>OD</li>
					<li class="sc">';
					$ready4doctor.=($iop[0])?$iop[0].':'.$iop['od']:'--';
					$ready4doctor.='</li>
					<li class="sc">';
					$ready4doctor.=($appt_data->cd_val_od)?$appt_data->cd_val_od:'--';
					$ready4doctor.='</li>
					</ul>
					
					</div>
					<div class="clearfix"></div>
					<div class="os">
					<ul>
					<li>OS</li>
					<li class="sc">';
					$ready4doctor.=($iop[0])?$iop[0].':'.$iop['os']:'--';
					$ready4doctor.='</li>
					<li class="sc">';
					$ready4doctor.=($appt_data->cd_val_os)?$appt_data->cd_val_os:'--';
					$ready4doctor.='</li>
					</ul>
					
					</div>
					<div class="clearfix"></div></td>
					
					</tr>';
				}
			}
		}
		else
		{
			//show none record message
			$ready4doctor.='<tr><td align="center" colspan="9"><div class="alert alert-info">No patient is ready for doctor yet.</div></td></tr>';	
		}
		$ready4doctor.='</table></div>';
		
		echo $ready4doctor;	
	}
	
	###################################################################
	# function to get to do list for logged in user
	###################################################################
	function to_do_list()
	{
		$to_do_list='';
		$a=0;	$qryPart = '';
		
		$todo_qry = imw_query("select pn.id, pn.patient_id, pn.provider_id, pn.patient_note, DATE_FORMAT(pn.note_date, '".get_sql_date_format('','','/')."') as note_date, pd.fname, pd.mname, pd.lname from patient_notes pn 
		LEFT JOIN patient_data pd ON pn.patient_id = pd.id 
		WHERE pn.provider_id= ".$_SESSION["authId"]." ORDER BY pn.id DESC");
		
		if(imw_num_rows($todo_qry) > 0)
		{
			while($todo_data=imw_fetch_object($todo_qry))
			{
				//patient name
				$pat_name = core_name_format($todo_data->lname, $todo_data->fname, $todo_data->mname);
				$a++;
				$to_do_list.='
				<div class="phyrow">
					<div class="todorow">
						<figure><label class="control control--checkbox"><input type="checkbox" checked="checked"/><div class="control__indicator"></div></figure>
						<strong>'.$pat_name.' - '.$todo_data->patient_id.'</strong><br>'.stripslashes($todo_data->patient_note).'
					</div>
					<div class="mesgopt">
						'.$todo_data->note_date.' <!--
						<img src="'.$GLOBALS['webroot'].'/library/images/edit1.png" alt="" data-toggle="tooltip" data-placement="bottom" title="Edit" /> -->
						<img src="'.$GLOBALS['webroot'].'/library/images/close.png" alt=""  data-toggle="tooltip" data-placement="bottom" title="Delete" onClick="delete_patient_note(\'del\', \''.$todo_data->id.'\')" />
					</div>
				</div>';
            }
		}
		else{ $to_do_list.='<div class="alert alert-info">No Record Found.</div>';}
		echo $to_do_list;
	}
}

/********************************************************
/* Purpose: landing billing admin page helper class.
/* Access Type: Indirect Access.
********************************************************/

class landing_billing_admin{
	
	var $arrUsers;
	var $loginFacilityName;
	
	function __construct(){
		//USERS
		$qry="Select id, fname, lname FROM users";
		$rs = imw_query($qry);
		while($res=imw_fetch_assoc($rs)){	
			$first_initial=strtoupper(substr($res['fname'],0,1));
			$last_initial=strtoupper(substr($res['lname'],0,1));
			$users[$res['id']] = $last_initial.', '.$first_initial;
		}
		$this->arrUsers=$users;
		unset($users);

		//LOGIN FACILITY NAME
		$qry="Select name FROM facility WHERE id='".$_SESSION['login_facility']."'";
		$rs = imw_query($qry);
		$res=imw_fetch_assoc($rs);
		$this->loginFacilityName=$res['name'];
		unset($rs);
		
	}
	
	function get_unapplied_charges($startDate='', $endDate='', $chart_view='1'){
		$arrUnappliedCharges=array();
		//UNAPPLIED SUPERBILL
		$qry="Select primary_provider_id_for_reports, todaysCharges FROM superbill WHERE (dateOfService BETWEEN '".$startDate."' AND '".$endDate."') 
		AND del_status='0' AND postedStatus='0' AND merged_with='0'";
		$rs = imw_query($qry);
		while($res=imw_fetch_assoc($rs)){	
			$phyid=$res['primary_provider_id_for_reports'];
			$phyName=$this->arrUsers[$phyid];
			$arrUnappliedCharges['Superbill'][$phyName]+=	$res['todaysCharges'];
		}
		
		//NOT POSTED CHARGES
		$qry="Select primary_provider_id_for_reports, totalAmt FROM patient_charge_list WHERE (date_of_service BETWEEN '".$startDate."' AND '".$endDate."') 
		AND del_status='0' AND (submitted='false' OR submitted='')";
		$rs = imw_query($qry);
		while($res=imw_fetch_assoc($rs)){	
			$phyid=$res['primary_provider_id_for_reports'];
			$phyName=$this->arrUsers[$phyid];
			$arrUnappliedCharges['Not Posted'][$phyName]+= $res['totalAmt'];
		}

		//UNBILLED CLAIMS
		$qry = "Select patChg.totalAmt,	patChg.primary_provider_id_for_reports 
		FROM patient_charge_list patChg 
		LEFT JOIN submited_record ON submited_record.encounter_id = patChg.encounter_id 
		WHERE patChg.del_status='0' AND (patChg.date_of_service BETWEEN '".$startDate."' AND '".$endDate."') 
		AND submited_record.encounter_id is NULL AND patChg.submitted='true' 
		AND patChg.primaryInsuranceCoId>0 AND patChg.totalBalance>0";
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$phyid=$res['primary_provider_id_for_reports'];
			$phyName=$this->arrUsers[$phyid];
			$arrUnappliedCharges['Unbilled Claims'][$phyName]+= $res['totalAmt'];
		}
		
		return $arrUnappliedCharges;
	}
	
	function get_unapplied_superbills($startDate='', $endDate='', $chart_view='1'){
		//UNAPPLIED SUPERBILL
		$arrUnappliedSuperbills=array();
		$qry="Select primary_provider_id_for_reports, todaysCharges FROM superbill WHERE (dateOfService BETWEEN '".$startDate."' AND '".$endDate."') 
		AND del_status='0' AND postedStatus='0' AND merged_with='0' ORDER BY primary_provider_id_for_reports";
		$rs = imw_query($qry);
		while($res=imw_fetch_assoc($rs)){	
			$phyid=$res['primary_provider_id_for_reports'];
			$phyName=$this->arrUsers[$phyid];
			$arrTemp[$phyName]+=$res['todaysCharges'];
		}
		
		$i=0;
		foreach($arrTemp as $phyName => $charges){
			$arrUnappliedSuperbills[$i]['kee']=$phyName;
			$arrUnappliedSuperbills[$i]['val']=$charges;
			$i++;
		}unset($arrTemp);

		return $arrUnappliedSuperbills;
	}
	
	function get_top_rejection($startDate='', $endDate=''){
		
		// REJECTION RESONS 
		$arrReasonCode[0] = 'No Reason Code';
		$rs=imw_query("Select * FROM cas_reason_code");
		while($res=imw_fetch_array($rs)){
			if(strlen($res['cas_desc'])>78){
				$res['cas_desc'] = substr($res['cas_desc'], 0 ,78).'...';
			}
			$arrReasonCode[$res['cas_code']]= $res['cas_code'].' - '.$res['cas_desc'];
		}
		// INSURANCE COMPANIES
		$rs=imw_query("Select id, name, in_house_code FROM insurance_companies");
		while($res=imw_fetch_array($rs)){
			$arrInsuranceComps[$res['id']]= $res['in_house_code'].' - '.$res['name'];
		}

		//--- GET POSTED PAYMENT
		$qry = "Select patChg.encounter_id, patChg.primaryInsuranceCoId, patChg.secondaryInsuranceCoId, patChg.tertiaryInsuranceCoId,
		den.deniedAmount, den.CAS_code, den.CAS_type,
		DATE_FORMAT(patChg.date_of_service, '".get_sql_date_format()."') as 'date_of_service', den.patient_id, 
		den.deniedBy, den.denialOperatorId, den.deniedById, patChgDet.procCode, patChgDet.diagnosis_id1,
		patChgDet.diagnosis_id2, patChgDet.diagnosis_id3, patChgDet.diagnosis_id4, 
		pd.fname,pd.mname,pd.lname 
		FROM patient_charge_list patChg 
		JOIN deniedpayment den ON den.encounter_id = patChg.encounter_id 
		JOIN patient_charge_list_details patChgDet ON patChgDet.charge_list_detail_id = den.charge_list_detail_id  
		LEFT JOIN patient_data pd ON pd.id = den.patient_id
		WHERE (patChg.date_of_service BETWEEN '$startDate' AND '$endDate') 
		ORDER BY den.CAS_code ,pd.lname";
		
		$rs = imw_query($qry);
		while($res = imw_fetch_array($rs)){
			$denAmt=0;
			$compType=$patient_name='';
			$pid = $res['patient_id'];
			$eid = $res['encounter_id'];
			$code = ($res['CAS_code']=='') ? 0 : $res['CAS_code'];
			if($code==0 && $res['CAS_type']!=""){
				$code=$res['CAS_type'];
			}
		
			$patient_name = core_name_format($res['lname'], $res['fname'], $res['mname']);
			$patient_name.= ' - '.$pid;
			
			$encounterIdArr[$eid] = $eid;
			
			$denAmt = $res['deniedAmount'];
			
			$arrResDataSumm[$code]['count']+= 1; 
			$arrResDataSumm[$code]['amount']+= $denAmt; 
			
			if(strtolower($res['deniedBy'])=='insurance'){
				$denInsComp = $arrInsuranceComps[$res['deniedById']];
				switch($res['deniedById']){
					case $res['primaryInsuranceCoId']:
						$compType='Primary';
					break;	
					case $res['secondaryInsuranceCoId']:
						$compType='Secondary';
					break;	
					case $res['tertiaryInsuranceCoId']:
						$compType='Tertiary';
					break;
					default:
				        $compType="Not Exist";						
				}

			}else{
				$denInsComp = 'Denied by Patient';
			}
			
			$arrResData[$eid]['patient_name']=$patient_name;
			$arrResData[$eid]['dos']=$res['date_of_service'];
			$arrResData[$eid]['ins_comp']= $denInsComp;
			$arrResData[$eid]['ins_comp_type']= $compType;
			$arrResData[$eid]['amount']+= $denAmt;
			$arrResData[$eid]['reason']= $arrReasonCode[$code];
		} 
		unset($rs);

		//HTML CREATION
		$content_part='';
		$arrSize=sizeof($arrResData);
		if($arrSize>0){
			  $content_part='
			  <thead>
				<tr class="eratr">
				  <th>Patient Name – ID </th>
				  <th>DOS</th>
				  <th>EID </th>
				  <th>Payer (Type)</th>
				  <th>Claim Amount </th>
				  <th>Reason </th>
				</tr>
			  </thead>
			  ';
			
			foreach($arrResData as $eid => $resDetail){
				
				$cptCodes = implode(', ', $arrCPTnDXCodes['cpt'][$code][$eid]);
				$dxCodes = implode(', ', $arrCPTnDXCodes['dx'][$code][$eid]);
				$content_part .=
				'<tr>
				  <td align="left" data-label="Patient ID"><span>'.$resDetail['patient_name'].'</span></td>
				  <td align="left" data-label="DOS">'.$resDetail['dos'].'</td>
				  <td align="left" data-label="Encounter ID">'.$eid.'</td>
				  <td align="left" data-label="Payer">'.$resDetail['ins_comp'];
				if($resDetail['ins_comp_type']!=""){  
					$content_part .=' ('.$resDetail['ins_comp_type'].')</td>';
				}
				$content_part .='<td align="left" data-label="Claim" class="text-right">'.numberFormat($resDetail['amount'],2).'</td>
				  <td data-label="Reason">'.$resDetail['reason'].'</td>
				</tr>';
			}
		}else{
		  $content_part='<br>
		  <div style="text-align:center">No Record Exists</div>';
		}
		
		return array('topRejHTML'=>$content_part, 'topRejRows'=>$arrSize);					
	}
	
	//FD COLLECTION
	function get_fd_collection($startDate='', $endDate='', $facility=''){
	
		if($startDate!='' && $endDate!=''){
	
			// GET NOT APPLIED CI/CO for selected month
			$arrCICOFields=array();
			$arrCICOPhysicians=array();
			$arrCICOPayments=array();
			$qry="Select id, item_name FROM check_in_out_fields";
			$rs=imw_query($qry);
			while($res=imw_fetch_assoc($rs)){
				$arrCICOFields[$res['id']]=$res['item_name'];
			}
			
			$arrModes=array('cash'=>'CASH', 'check'=>'CHECK', 'eft'=>'EFT', 'money order'=>'MONEY ORDER', 'credit card'=>'CREDIT CARD', 'veep'=>'VEEP');
	
			$qry="SELECT sa.sa_facility_id, sa.sa_doctor_id, cioPayDet.id as cioPaydetID,
			cioPay.patient_id, 
			cioPay.payment_id, 
			cioPay.payment_method, cioPay.cc_type,
			cioPay.created_by, cioPay.created_time, 
			cioPayDet.item_payment, cioPayDet.item_id, 
			pd.fname, pd.mname, pd.lname 
			FROM schedule_appointments sa 
			JOIN check_in_out_payment cioPay ON cioPay.sch_id  = sa.id 
			JOIN check_in_out_payment_details cioPayDet ON cioPayDet.payment_id= cioPay.payment_id
			JOIN patient_data pd ON pd.id = cioPay.patient_id 
			WHERE cioPay.total_payment>0 
			AND (cioPay.created_on BETWEEN '".$startDate."' AND '".$endDate."')
			AND (cioPay.del_status=0 OR (cioPay.del_status=1 AND cioPay.delete_date>'".$endDate."'))";
			if(empty($facility)==false){
				$qry.=" AND sa.sa_facility_id='".$facility."'";
			}
	
			$rs=imw_query($qry);
			while($res=imw_fetch_array($rs)){
				$payment_id = $res['payment_id'];
				$cioPaydetID = $res['cioPaydetID'];
				$phyId = $res['sa_doctor_id'];
				$grpId = $phyId;
				
				//query to get refund detail for current ci/co payments if any
				$refundAmt=0;
				$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND ci_co_id = '".$res['cioPaydetID']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')");
				while($rsRef=imw_fetch_array($qryRef))
				{
					$refundAmt+=$rsRef['ref_amt'];
				}
				
				$tempCIOArr[$grpId][$cioPaydetID]['payment']+= $res['item_payment'];
				$tempCIOArr[$grpId][$cioPaydetID]['pay_mode']= strtolower($res['payment_method']);
				$tempCIOArr[$grpId][$cioPaydetID]['item_id']= $res['item_id']; 
				$tempCIOArr[$grpId][$cioPaydetID]['refund']+=$refundAmt;
				
				$tempCIOArr[$grpId][$cioPaydetID]['facility']= $facility;
	
				$tempPayIds[$cioPaydetID] =$cioPaydetID;
			}
	
			$splitted_encounters=array();
			if(sizeof($tempPayIds)>0){
				$splitted_encounters = array_chunk($tempPayIds,4000);
				$tempCIOPaid=array();
				foreach($splitted_encounters as $arr){
					$str_splitted_encs 	 = implode(',',$arr);
					$arr_acc_payment_id=array();
					$temp_acc_payment_id=array();
					
					$qry="SELECT cioPost.check_in_out_payment_detail_id, 
					 cioPost.manually_payment, 
					 cioPost.acc_payment_id, 
					 cioPost.manually_date 
					 FROM check_in_out_payment_post cioPost 
					 WHERE cioPost.check_in_out_payment_detail_id IN(".$str_splitted_encs.") 
					 AND cioPost.status='0'";
					$rs=imw_query($qry);
					while($res=imw_fetch_array($rs)){
						$payment_id = $res['check_in_out_payment_detail_id'];
						
						if($res['manually_payment']>0 && $res['manually_date']<=$endDate){
							$tempCIOPaid[$payment_id]+=$res['manually_payment'];
						}
						if($res['acc_payment_id']>0){ 
							$arr_acc_payment_id[$res['acc_payment_id']]=$res['acc_payment_id'];
							$temp_acc_payment_id[$res['acc_payment_id']] = $res['check_in_out_payment_detail_id'];
						}
					}
					if(sizeof($arr_acc_payment_id)>0){
						$str_acc_payment_id = implode(',', $arr_acc_payment_id);
						
						$qry="SELECT patPay.payment_id, 
						patPayDet.paidForProc FROM 
						patient_chargesheet_payment_info patPay 
						LEFT JOIN patient_charges_detail_payment_info patPayDet ON patPayDet.payment_id = patPay.payment_id 
						WHERE patPay.payment_id IN(".$str_acc_payment_id.") AND patPay.date_of_payment <='".$endDate."' 
						AND ((patPayDet.deletePayment='0' OR (patPayDet.deletePayment='1' AND patPayDet.deleteDate > '".$endDate."'))  
						AND (patPayDet.unapply='0' OR (patPayDet.unapply='1' AND DATE_FORMAT(patPayDet.unapply_date, '%Y-%m-%d')>'".$endDate."')))";
						$rs=imw_query($qry);
						while($res=imw_fetch_array($rs)){
							$payment_id = $temp_acc_payment_id[$res['payment_id']];
							$tempCIOPaid[$payment_id]+=$res['paidForProc'];
						}
					}
				}
	
				if(sizeof($tempCIOArr)>0){
					$groupArr=array();
					$tempPhy = array_keys($tempCIOArr);
	
					$strTempPhy = implode(',', $tempPhy);
					$qry = "Select id FROM users WHERE id IN(".$strTempPhy.") ORDER BY lname, fname";
					$rs=imw_query($qry);
					while($posQryRes = imw_fetch_array($rs)){
						$groupArr[$posQryRes['id']] = $posQryRes['id'];
					}	
				}
				
				//FINAL ARRAY
				foreach($groupArr as $grpId){		
					foreach($tempCIOArr[$grpId] as $payment_id => $cioData){
						$payment = $cioData['payment'];
						$refund=$cioData['refund'];
						$itemId=$cioData['item_id'];
						$balance=$payment;
	
						$pay_mode = $cioData['pay_mode'];
						
						if($tempCIOPaid[$payment_id]>0){
							$balance = floatval($payment) - floatval($tempCIOPaid[$payment_id]);
						}
	
						
						$arrCICOPhysicians[$grpId]['payment']+=($payment-$refund);
						$arrCICOPhysicians[$grpId]['applied']+=$tempCIOPaid[$payment_id];
						$arrCICOPhysicians[$grpId]['balance']+=$balance;
						
						$arrCICOPayments[$grpId][$itemId]['payment']+=($payment-$refund);
						$arrCICOPayments[$grpId][$itemId]['applied']+=$tempCIOPaid[$payment_id];
						$arrCICOPayments[$grpId][$itemId]['balance']+=$balance;						
					}
				}
			}
			
			// CI/CO HTML
			$cicoHTML=$HTML='';
			if(sizeof($arrCICOPhysicians)>0){
				$arrCICOTotal=array();
				foreach($arrCICOPhysicians as $grpId => $grpData){
					$arrSubTotal=array();
					$rowClass='row'+$grpId;
					$HTML.='
					<tr class="link_cursor" onClick="hide_display('.$rowClass.');">
						<td data-label="Physician Name : " class="text_purple"><strong>'.$this->arrUsers[$grpId].'</strong></td>
						<td data-label="Payment Type : ">All</td>
						<td data-label="Payment : " class="text-right"><strong>'.numberFormat($grpData['payment'],2).'</strong></td>
						<td data-label="Applied : " class="text-right"><strong>'.numberFormat($grpData['applied'],2).'</strong></td>
						<td data-label="Balance  : " class="text-right"><strong>'.numberFormat($grpData['balance'],2).'</strong></td>
					</tr>';					
					
					foreach($arrCICOPayments[$grpId] as $itemId => $itemDetail){
						
						$arrSubTotal['payment']+=$itemDetail['payment'];
						$arrSubTotal['applied']+=$itemDetail['applied'];
						$arrSubTotal['balance']+=$itemDetail['balance'];
						
						$HTML.='
						<tr class="'.$rowClass.' cicoSubPart" style="display:none" >
							<td data-label="Physician Name : ">&nbsp;</td>
							<td data-label="Payment Type : ">'.$arrCICOFields[$itemId].'</td>
							<td data-label="Payment : " class="text-right">'.numberFormat($itemDetail['payment'],2).'</td>
							<td data-label="Applied : " class="text-right">'.numberFormat($itemDetail['applied'],2).'</td>
							<td data-label="Balance  : " class="text-right">'.numberFormat($itemDetail['balance'],2).'</td>
						</tr>';
					}
						
					$arrCICOTotal['payment']+=$arrSubTotal['payment'];
					$arrCICOTotal['applied']+=$arrSubTotal['applied'];
					$arrCICOTotal['balance']+=$arrSubTotal['balance'];
				}
				
				$cicoHTML.='
				<table class="table table-bordered table-striped table-hover cicoTable">
				<thead>
				  <tr class="eratr">
					<th width="30%">Physician Name</th>
					<th width="22%">Payment Type </th>
					<th width="15%">Payment</th>
					<th width="11%">Applied </th>
					<th width="11%">Balance </th>
				  </tr>
				</thead>
				<tbody>'
				.$HTML.
				'<tr>
					<td>&nbsp;</td>
					<td data-label="Payment  : " class="purple_bar">TOTAL</td>
					<td data-label="Payment Total : " class="text-right purple_bar">'.numberFormat($arrCICOTotal['payment'],2).'</td>
					<td data-label="Applied Total : " class="text-right purple_bar">'.numberFormat($arrCICOTotal['applied'],2).'</td>
					<td data-label="Balance Total  : " class="text-right purple_bar">'.numberFormat($arrCICOTotal['balance'],2).'</td>
				  </tr>
				</tbody>
              </table>';
			}else{
				$cicoHTML.='<div style="text-align:center">No Record Exist</div>';
			}
		
			// GET PATIENT PRE PAYMENTS
			$groupArr=array();
			$tempCCTypeAmts=array();
			$qry="Select pDep.id, pDep.patient_id, pDep.paid_amount, 
			pDep.apply_payment_date, pData.providerID, pData.default_facility, pDep.apply_payment_type, pDep.apply_amount,
			pDep.entered_by, pDep.payment_mode, pDep.credit_card_co,
			pData.fname, pData.mname, pData.lname 
			FROM patient_pre_payment pDep 
			LEFT JOIN patient_data pData ON pData.id = pDep.patient_id 
			WHERE 
			(pDep.del_status='0' OR (pDep.del_status='1' AND DATE_FORMAT(pDep.trans_del_date, '".$formatDelDate."')>'".$endDate."')) 
			AND (pDep.paid_date between '".$startDate."' and '".$endDate."')";
			if(empty($facility)==false){
				$qry.=" AND pDep.facility_id='".$facility."'";
			}
			$qry.=' ORDER BY pData.lname, pData.fname';
			$rs = imw_query($qry);

			$arrDepIds=array();
			$tempData=array(); $arrDepIds=array(); $arrAllIds=array();
			while($res=imw_fetch_assoc($rs)){	
				$balance_amount=0; $doc_id=0;$refundAmt=0;
				
				//query to get refund detail for current pre payment if any
				$qryRef=imw_query("Select ref_amt FROM ci_pmt_ref WHERE del_status='0' AND pmt_id = '".$res['id']."' AND (entered_date BETWEEN '".$startDate."' AND '".$endDate."')");
				while($rsRef=imw_fetch_array($qryRef))
				{
					$refundAmt=$rsRef['ref_amt'];
				}
			
				$oprId=$res['entered_by'];
				$grpId= $oprId;
				
				$id= $res['id'];
						
				$balance_amount=($res['paid_amount']);
		
				if($res['apply_payment_type']=='manually' && $res['apply_payment_date']<= $endDate){
					$balance_amount-=$res['apply_amount'];
				}
				
				if($balance_amount>0){
					$tempData[$id]['PAT_DEPOSIT']=$res['paid_amount'];
					$tempData[$id]['PAT_DEPOSIT_REF']=$refundAmt;
					
					if($res['apply_payment_type']=='manually'){
						$tempData[$id]['APPLIED_AMT']+= $res['apply_amount'];
					}
					if($res['apply_payment_date']!='0000-00-00'){
						$arrDepIds[$id]=$id;	
					}
					
					$arrAllIds[$id]=$id;
					$arrAllIdsData[$grpId][$id]['pay_mode']=strtolower($res['payment_mode']);
					$arrAllIdsData[$grpId][$id]['pat_name']=$patName;
					
					$groupArr[$grpId] = $grpId;
				}
			}

			// GET PRE PAT ENCOUNTER APPLIED AMTS
			if(count($arrDepIds)>0){
				$strDepIds=implode(',', $arrDepIds);
				$preAppQry="Select payChgDet.patient_pre_payment_id, payChgDet.paidForProc FROM patient_chargesheet_payment_info payChg  
				JOIN patient_charges_detail_payment_info payChgDet ON payChgDet.payment_id = payChg.payment_id
				WHERE payChgDet.patient_pre_payment_id IN($strDepIds) 
				AND (payChg.date_of_payment BETWEEN '".$startDate."' and '".$endDate."') 
				AND ((payChgDet.deletePayment='0' OR (payChgDet.deletePayment='1' AND payChgDet.deleteDate>'".$endDate."'))
				AND (payChgDet.unapply='0' OR (payChgDet.unapply='1' AND DATE_FORMAT(payChgDet.unapply_date, '%Y-%m-%d')>'".$endDate."')))";
				
				$preAppRs=imw_query($preAppQry);
				while($preAppRes=imw_fetch_array($preAppRs)){
					$id = $preAppRes['patient_pre_payment_id'];
					$tempData[$id]['APPLIED_AMT']+= $preAppRes['paidForProc'];
				}
			}
			// GROUPING OF DATA
			if(sizeof($groupArr)>0){
				$groupStr = implode(',', $groupArr);
				$groupArr=array();
				$groupArr[0]=0;
				$qry="Select id FROM users WHERE id IN(".$groupStr.") ORDER by lname, fname";
				$rs=imw_query($qry)or die(imw_error().'_731');
				while($res=imw_fetch_array($rs)){
					$groupArr[$res['id']] = $res['id'];
				}
			}			
			//print_r($arrAllIdsData);
			// FINAL ARRAY
			$arrPrePayNotApplied=array();
			$arrPrePayAmountsModes=array();
			
			foreach($groupArr as $grpId){
				foreach($arrAllIdsData[$grpId] as $id => $grpData){
					$balance_amount= floatval($tempData[$id]['PAT_DEPOSIT']) - floatval($tempData[$id]['APPLIED_AMT']);

					$pay_mode= $grpData['pay_mode'];
					$balAmt=(floatval($balance_amount)-floatval($tempData[$id]['PAT_DEPOSIT_REF']));

					$arrPrePayAmounts[$grpId]['payment']+=$tempData[$id]['PAT_DEPOSIT'];
					$arrPrePayAmounts[$grpId]['applied']+=$tempData[$id]['APPLIED_AMT'];
					$arrPrePayAmounts[$grpId]['balance']+=$balAmt;
					
					$arrPrePayAmountsModes[$pay_mode]['applied']+=$tempData[$id]['APPLIED_AMT'];
					$arrPrePayAmountsModes[$pay_mode]['balance']+=$balAmt;
				}	
			}
			
			// PRE-PAYMENT HTML
			$prePaymentHTML=$HTML='';
			if(sizeof($arrPrePayAmounts)>0){
				$arrPrePayTotal=array();
				foreach($arrPrePayAmounts as $grpId => $grpDetail){
					
					$arrPrePayTotal['payment']+=$grpDetail['payment'];
					$arrPrePayTotal['applied']+=$grpDetail['applied'];
					$arrPrePayTotal['balance']+=$grpDetail['balance'];
					
					$HTML.='
					<tr>
						<td data-label="Physician Name : ">'.$this->arrUsers[$grpId].'</td>
						<td data-label="Payment Type : " class="text-right">'.numberFormat($grpDetail['payment'],2).'</td>
						<td data-label="Payment : " class="text-right">'.numberFormat($grpDetail['applied'],2).'</td>
						<td data-label="Applied : " class="text-right">'.numberFormat($grpDetail['balance'],2).'</td>
					</tr>';
				}
				
				//PAYMENT MODES				
				$HTML_Modes='';
				if(sizeof($arrPrePayAmountsModes)>0){
					$totPrePayApplied=$totPrePayBalance=0;
					foreach($arrModes as $modeKey => $mode){
						if($arrPrePayAmountsModes[$modeKey]){
							$modeDetails= $arrPrePayAmountsModes[$modeKey];
							$totPrePayApplied+=$modeDetails['applied'];
							$totPrePayBalance+=$modeDetails['balance'];
							$HTML_Modes.='
							<tr class="operttotla2">
								<td>&nbsp;</td>
								<td align="right" data-label="Payment  : " >'.$mode.'</td>
								<td data-label="Applied : " class="text-right">'.numberFormat($modeDetails['applied'],2).'</td>
								<td data-label="Balance : " class="text-right">'.numberFormat($modeDetails['balance'],2).'</td>
							</tr>';
						}
					}
					$HTML_Modes.='
					<tr>
						<td>&nbsp;</td>
						<td align="right" data-label="Payment  : " class="text-right tabtotal"><strong>Total</strong></td>
						<td data-label="Payment Total : " class="text-right tabtotal"><strong>'.numberFormat($totPrePayApplied,2).'</strong></td>
						<td data-label="Applied Total : " class="text-right tabtotal"><strong>'.numberFormat($totPrePayBalance,2).'</strong></td>
					</tr>';
				}
				
				$prePaymentHTML.='
				<table class="table table-bordered table-striped table-hover">
				<thead>
                  <tr class="eratr">
                    <th width="35%">Operator</th>
                    <th width="30%">Payment </th>
                    <th width="19%">Applied</th>
                    <th width="16%">Balance</th>
                  </tr>
				</thead>
				<tbody>'
				.$HTML.
				'<tr>
                    <td>&nbsp;</td>
                    <td align="right" data-label="Payment  : " class="purple_bar">TOTAL</td>
                    <td data-label="Payment Total : " class="purple_bar">'.numberFormat($arrPrePayTotal['applied'],2).'</td>
                    <td data-label="Applied Total : " class="purple_bar">'.numberFormat($arrPrePayTotal['balance'],2).'</td>
                  </tr>'
				  .$HTML_Modes.'
				</tbody>
              </table>';
			  
			}else{
				$prePaymentHTML.='<div style="text-align:center">No Record Exist</div>';
			}			
			//END UNAPPLIED PRE-PPAYMENTS
		}

		return array('cicoHTML'=>$cicoHTML, 'prePaymentHTML'=>$prePaymentHTML);
	}
	
	//GET TOTAL PAYMETNS FOR TODAY
	function get_total_payments($startDate='', $endDate=''){
		$totalPayments=0;
		
		// CI/CO PAYMENTS
		$qry="Select total_payment FROM check_in_out_payment WHERE (created_on BETWEEN '".$startDate."' AND '".$endDate."') AND del_status='0'";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$totalPayments+= $res['total_payment'];
		}
		
		// PRE-PAYMENTS
		$qry="Select paid_amount FROM patient_pre_payment WHERE (paid_date BETWEEN '".$startDate."' and '".$endDate."') AND del_status='0'";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$totalPayments+= $res['paid_amount'];
		}

		// POSTED PAYMENTS
		$qry="Select paydet.paidForProc, payinfo.paymentClaims FROM patient_chargesheet_payment_info payinfo 
		JOIN patient_charges_detail_payment_info paydet ON paydet.payment_id= payinfo.payment_id 
		WHERE (payinfo.date_of_payment BETWEEN '".$startDate."' AND '".$endDate."') AND deletePayment='0'";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			if($res['paymentClaims']=='Negative Payment'){
				$totalPayments-= $res['paidForProc'];
			}else{
				$totalPayments+= $res['paidForProc'];
			}
		}
		
		return numberformat($totalPayments,2,1);
	}

	//GET TOTAL CHARGES FOR TODAY
	function get_total_charges($startDate='', $endDate=''){
		$totalCharges=0;
		
		//POSTED CHARGES
		$qry="Select totalAmt FROM patient_charge_list WHERE (date_of_service BETWEEN '".$startDate."' AND '".$endDate."') AND del_status='0'";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$totalCharges+= $res['totalAmt'];
		}
		
		//SUPERBILL CHARGES
		$qry="Select todaysCharges FROM superbill WHERE (dateOfService BETWEEN '".$startDate."' AND '".$endDate."') 
		AND postedStatus='0' AND del_status='0'";
		$rs=imw_query($qry);
		while($res=imw_fetch_array($rs)){
			$totalCharges+= $res['todaysCharges'];
		}
		
		return numberformat($totalCharges,2,1);
	}

	//GET ERA NOT POSTED RECORDS
	function get_era(){
		$eraHTML='';
		
		$getElectronicStr = "SELECT * FROM electronicfiles_tbl a,
		era_835_details b
		WHERE a.id = b.electronicFilesTblId
		AND a.post_status != 'Posted'
		and a.archive_status='0' 
		ORDER BY b.chk_issue_EFT_Effective_date DESC";
		$getElectronicQry = imw_query($getElectronicStr);
		$countRows = imw_num_rows($getElectronicQry);
		$arr_id = array();
		if($countRows>0){
			while($getElectronicRows = imw_fetch_array($getElectronicQry)){
				$era_file_arr[$getElectronicRows['id']]=$getElectronicRows;
				$era_total_amount_arr[$getElectronicRows['id']][] = $getElectronicRows['provider_payment_amount'];
			}
			
			foreach($era_file_arr as $era_key=>$era_val){
				$EFTChkDate=get_date_format($era_file_arr[$era_key]['chk_issue_EFT_Effective_date'],'','');
				$file_temp_name = $era_file_arr[$era_key]['file_temp_name'];
				$status = $era_file_arr[$era_key]['post_status'];	
				$show_status = str_replace("Partially Posted","Partial",$status);		
				$show_status = str_replace("Partialy Posted","Partial",$show_status);	
				$HTML.='
				<tr>
				  <td data-label="Claims File : ">
				  	<a href="#" id="BillingEPP" onClick="top.change_main_Selection(this,'.$era_key.');" class="text_purple">'.$file_temp_name.'</a>
				 </td>
				  <td data-label="Check Date : "><span>'.$EFTChkDate.'</span></td>
				  <td data-label="Amount : " class="text-right">'.numberFormat(array_sum($era_total_amount_arr[$era_key]),2).'</td>
				  <td data-label="Status : ">'.$show_status.'</td>
				</tr>';					
			}

			$eraHTML='
            <table class="table table-bordered table-striped table-hover">
              <thead>
                <tr class="eratr">
                  <th width="40%">
				  	<label class="control"  style="padding-left:0px"></label>Claim File
				  </th>
				  <th width="16%">Check Date </th>
                  <th width="19%">Amount </th>
                  <th width="16%"> Status</th>
                </tr>
              </thead>
              <tbody>'
			  .$HTML.'
              </tbody>
            </table>
			';
		}else{
			$eraHTML='<div style="text-align:center">No Record Exist</div>';
		}
		
		return $eraHTML;
	}
	
	
	function bar_chart_fun($data,$valueType='',$barRotation=true){
		$user_arr=array();
		foreach($data as $data_key=>$data_val){
			foreach($data[$data_key] as $user_key=>$user_val){
				$user_arr[$user_key]=$user_key;
			}
		}
		ksort($user_arr);
		$key_i=0;
		foreach($data as $key=>$val){
			$key_i++;$kk=0;
			ksort($data[$key]);
			foreach($user_arr as $user_key=>$user_val){
				$column_graph_arr[$kk]["category"]=$user_key;
				$column_graph_arr[$kk]["column-".$key_i]=$data[$key][$user_key];
				$kk++;
				$grand_graph_total[]=$data[$key][$user_key];
			}
			
			$value_field_graph_arr[]=array("balloonText"=> "[[title]] ".$valueType.": $[[value]]",
			"fillAlphas"=> 1,
			"id"=> "AmGraph-$key_i",
			"title"=> "$key",
			"type"=> "column",
			"valueField"=> "column-$key_i",
			"visibleInLegend"=>$barRotation);
		}
		$return_arr['column_graph_arr']=$column_graph_arr;
		$return_arr['value_field_graph_arr']=$value_field_graph_arr;
		$return_arr['grand_graph_total']=$grand_graph_total;
		return $return_arr;
	}
	
    
    function get_top_tasks() {
        $eraHTML = '';
        $HTML = '';
        
        //Fetching Rsesults
        $query = "select tm_assigned_rules .*,tm_rules_list.*  from tm_assigned_rules LEFT JOIN tm_rules_list ON (tm_rules_list.id = tm_assigned_rules.rule_list_id)
                    AND tm_assigned_rules.status = 0
                    AND tm_rules_list.rule_status = 0
                    AND tm_rules_list.cat_id = 1
                    ORDER BY tm_assigned_rules.added_on DESC ";
        $chkQry = imw_query($query);
        if ($chkQry && imw_num_rows($chkQry) > 0) {
            $counter = 0;
            while ($rowFetch = imw_fetch_assoc($chkQry)) {
                $id = $rowFetch['taid'];
                $user_group = $rowFetch['user_group'];
                $user_names = $rowFetch['user_name'];

                $user_names_arr = explode(', ', $user_names);
                $user_names_arr = array_unique($user_names_arr);

                $user_groups_arr = explode(', ', $user_group);
                $user_groups_arr = array_unique($user_groups_arr);
                if (!empty($user_groups_arr)) {
                    foreach ($user_groups_arr as $row_arr) {
                        $reqQry = "select id from users where user_group_id='".$row_arr."' and id > 0 and delete_status = 0";
                        $reqQryrs = imw_query($reqQry);
                        while ($row = imw_fetch_assoc($reqQryrs)) {
                            $user_names_arr[] = $row['id'];
                        }
                    }
                }
                $user_names_arr = array_unique($user_names_arr);
                if (!in_array($_SESSION['authId'], $user_names_arr)) {
                    continue;
                }
                $counter++;
                if ($counter > 5) {
                    break;
                }
                $section_name = $rowFetch['rule_id'];

                $assigned_date = date('m-d-Y',strtotime($rowFetch['added_on']));

                $PatientName = '';
                if (isset($rowFetch['patient_name']) && $rowFetch['patient_name'] != '') {
                    $PatientName = $rowFetch['patient_name'] . ' - ' . $rowFetch['patientid'];
                } else {
                    $getPatientName = "SELECT id, CONCAT(lname,', ',fname) as name FROM patient_data WHERE id = '" . $rowFetch['patientid'] . "'";
                    $PatientName_rs = imw_query($getPatientName);
                    $ptName_row = imw_fetch_assoc($PatientName_rs);
                    if ($ptName_row['name'] != '' && $ptName_row['id'] != '') {
                        $PatientName = $ptName_row['name'] . ' - ' . $ptName_row['id'];
                    }
                }
                $comments = '';
                if (isset($rowFetch['comment']) && $rowFetch['comment'] != '') {
                    $comments .= $rowFetch['comment'];
                }
                $HTML .= '<tr>
                            <td align="left" data-label="Date"><span>' . $assigned_date . '</span></td>
                            <td align="left" data-label="Patient ID">' . $PatientName . '</td>
                            <td align="left" data-label="Encounter ID">' . $comments . '</td>
                        </tr>';
            }

            $eraHTML .= '
            <thead>
                <tr class="eratr">
                  <th width="30%">Assigned Date</th>
                  <th width="35%">Patient Name – ID</th>
                  <th width="35%">Comments</th>
                </tr>
              </thead>
              <tbody>' . $HTML . '<tbody>';
        } else {
            $eraHTML = '<br/><div style="text-align:center">No Record Exist</div>';
        }

        return $eraHTML;
    }
} //END CLASS
?>