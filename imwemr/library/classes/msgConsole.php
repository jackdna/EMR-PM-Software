<?php
/*
The MIT License (MIT)
Distribute, Modify and Contribute under MIT License
Use this software under MIT License

File: msgConsole.php
Coded in PHP 7, 
Purpose:  Performs all the task of Console and Notification bubble
Access Type: Include File
*/
/*------------------------------------------------------------------*/
/*	Class written to centralize provider notification and messaging	*/
/*	The same class is being used in core_base and console popup		*/
/*------------------------------------------------------------------*/
include_once(dirname(__FILE__)."/ccd_xml_parser.php");
require_once(dirname(__FILE__)."/class.tests.php");
class msgConsole
{
	public $operator_id,$patient_id,$operator_type,$operator_group_id,$limit_from,$limit_upto,$callFrom,$array_threads,$doc_dir_path;
	public $common_order_type = array("1"=>"Meds", "2"=>"Labs", "3"=>"Imaging/Rad", "4"=>"Procedure/Sx",  "5"=>"Information/Instructions");
	public $ObjTests;
	
	//Default tabs for Physician Bubble notifications
	public $bubbleTabs = array(
		'get_messages_reminders' => array('enable' => true, 'params' => array('my_inbox')), 
		'get_forms_letters' => array('enable' => true, 'params' => array()), 
		'get_tests_tasks' => array('enable' => true, 'params' => array('scan_upload_tasks')), 
	);
	
	/*	constructor to initialize basic variable  */
	public function __construct()
	{
		$this->operator_id = isset($_SESSION['authId']) ? $_SESSION['authId'] : '';
		$this->patient_id = isset($_SESSION['patient']) ? $_SESSION['patient'] : '';
		$this->operator_type = isset($_SESSION['logged_user_type']) ? $_SESSION['logged_user_type'] : '';
		$this->operator_group_id = isset($_SESSION['authGroupId']) ? $_SESSION['authGroupId'] : '';
		$this->imedic_scan_db = constant("IMEDIC_SCAN_DB");
		$this->dbase = constant('IMEDIC_IDOC');
		$this->limit_from = 0;
		$this->limit_upto = 4;
		$this->callFrom='console';
		$this->array_threads = array();
		$this->doc_dir_path = $GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
		if(in_array($this->operator_type,array('1','11','12','19','21'))){$this->operator_type = '1';}
		$this->ObjTests = new Tests;
	}
	
	
	/*------------------------ This function is used to get the messages of the Messages/Reminders --------------------*/
	public function get_messages_reminders($action,$page_get='',$per_page='',$case_filter='', $sort='',$folder_id='')
	{
		$action = trim($action);
		$result_arr = array();		
		$filter = '';
		$sel_user_name = ' CONCAT(users.fname," ",users.mname,", ",users.lname) ';
		$group_by = '';
		$user_msg_selection_type = 'user_messages.user_message_id';
		switch($action)
		{
			case 'my_inbox':
				if($this->callFrom=='console'){$consoleinbox1 = '';}else{$consoleinbox1 = 'user_messages.message_sender_id != '.$this->operator_id.' and ';}
				$filter = ' user_messages.message_status = 0 and user_messages.receiver_delete=0 and user_messages.message_to = "'.$this->operator_id.'" and '.$consoleinbox1.'user_messages.message_sender_id > 0 and user_messages.delivery_date <= CURDATE()';
				$join_on = ' LEFT JOIN users ON user_messages.message_sender_id = users.id ';				
			break;
			case 'patient_messages':
				if(isset($this->patient_id) && trim($this->patient_id)!='')
				{					
					$filter =' user_messages.message_status = 0 and user_messages.receiver_delete=0 and user_messages.message_to = "'.$this->operator_id.'" and user_messages.message_sender_id != '.$this->operator_id.' and user_messages.message_sender_id > 0 and user_messages.delivery_date <= CURDATE() and user_messages.patientId = '.$this->patient_id;						
				}
				$join_on = ' LEFT JOIN users ON user_messages.message_sender_id = users.id ';
			break;
			case 'future_alerts':
				$filter .= ' user_messages.message_status = 0 and user_messages.del_future_alert=0 and user_messages.del_status ="0" and user_messages.message_sender_id = '.$this->operator_id.' and date_format(user_messages.delivery_date,"%Y-%m-%d") > CURDATE() AND user_messages.delivery_date != "0000-00-00" ';
				$join_on = ' LEFT JOIN users ON user_messages.message_to = users.id ';
				$sel_user_name = ' GROUP_CONCAT(CONCAT(users.fname," ",users.mname,", ",users.lname) SEPARATOR " :: ") ';
				$group_by = ' group by (CASE WHEN (msg_id != 0) THEN msg_id ELSE (CONCAT_WS(",",user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,"%Y-%m-%d"))) END) ';		
				$user_msg_selection_type = 'GROUP_CONCAT(user_messages.user_message_id) AS user_message_id ';
			break;
			case 'sent_messages':
				$filter .= ' user_messages.del_status ="0" and user_messages.message_sender_id = '.$this->operator_id.' and (date_format(user_messages.delivery_date,"%Y-%m-%d") <= "'.date('Y-m-d').'" OR user_messages.delivery_date = "0000-00-00")';
				$join_on = ' LEFT JOIN users ON user_messages.message_to = users.id ';
				$sel_user_name = ' GROUP_CONCAT(CONCAT(users.fname," ",users.mname,", ",users.lname) SEPARATOR "; ") ';
				//$group_by = ' group by IF(msg_id!=0,msg_id,(user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,"%Y-%m-%d"))) ';		
				$group_by = ' group by (CASE WHEN (msg_id != 0) THEN msg_id ELSE (CONCAT_WS(",",user_messages.sent_to_groups, user_messages.message_subject, user_messages.message_text, DATE_FORMAT(user_messages.message_send_date,"%Y-%m-%d"))) END) ';		
				//$user_msg_selection_type = 'GROUP_CONCAT(user_messages.user_message_id) AS user_message_id ';
			break;
			case 'deleted_messages':
				$filter .= ' user_messages.message_status = 0 and ((user_messages.message_sender_id = "'.$this->operator_id.'" AND user_messages.del_status = "1") OR (user_messages.message_to = "'.$this->operator_id.'" AND user_messages.receiver_delete ="1" AND user_messages.del_future_alert=0) )';
				$join_on = ' LEFT JOIN users ON user_messages.message_to = users.id ';
				$sel_user_name = ' GROUP_CONCAT(CONCAT(users.fname," ",users.mname,", ",users.lname) SEPARATOR "; ") ';
				$group_by = ' group by user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,"%Y-%m-%d") ';		
				$user_msg_selection_type = 'GROUP_CONCAT(user_messages.user_message_id) AS user_message_id ';
			break;
		}
        
        if($folder_id) {
            $filter=' user_messages.receiver_delete ="0" and (user_messages.message_sender_id = "'.$this->operator_id.'" OR user_messages.message_to = "'.$this->operator_id.'") and user_messages.saved_folder_id='.$folder_id.' '; //user_messages.del_status ="0"
            $join_on = ' LEFT JOIN users ON user_messages.message_sender_id = users.id ';
        } else {
            if($action != 'deleted_messages'){
                $filter.=' and user_messages.saved_folder_id=0';
            }
        }
		if($filter!='')
		{
			$orderby_msg="";
			$Arrtemp1 = explode("_",$sort);
			$sort = isset($Arrtemp1[0]) ? $Arrtemp1[0] : '';
			$sopt = isset($Arrtemp1[1]) ? $Arrtemp1[1] : '';
			if(empty($sopt)){ $sopt=""; }
			if($sort=="Sender"){
				$orderby_msg .= ' message_sender_name '.$sopt.' , ';
			}
			else if($sort=="Date"){
				$orderby_msg .= ' message_send_date '.$sopt.' , ';
			}
			else if($sort=="Urgency"){	
				$orderby_msg .= ' message_urgent '.$sopt.' , ';
			}
			else if($sort=="Flagged"){	
				$orderby_msg .= ' flagged '.$sopt.' , ';
			}
			
			if($this->callFrom=='core'){
				$orderby_msg .= ' user_messages.message_read_status, user_messages.user_message_id desc';
			}else{
				$orderby_msg .= ' user_messages.user_message_id desc';
			}
			$limit = '';
			if(isset($this->limit_from) && isset($this->limit_upto))
			{
				//$limit = ' LIMIT '.$this->limit_from.','.$this->limit_upto;	
			}
			$req_qry_cont = imw_query('SELECT count(user_messages.user_message_id) as msg_cont
						FROM user_messages
						where 
						'.$filter.' and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0 
						'.$group_by);
			$req_row_cont=imw_fetch_array($req_qry_cont);
			$user_message_id_cont=$req_row_cont['msg_cont'];
			if($action == 'sent_messages'){
				$user_message_id_cont = imw_num_rows($req_qry_cont);
			}			
			$req_qry = 'SELECT '.$user_msg_selection_type.', user_messages.message_read_status, user_messages.message_subject, user_messages.message_text, user_messages.message_urgent, 
						'.$sel_user_name.' AS message_sender_name, user_messages.message_sender_id as message_sender_id,  
						user_groups.name as user_group, if(TRIM(patient_data.fname)!="",CONCAT(patient_data.lname,", ",patient_data.fname," ",patient_data.mname," - ",patient_data.id),"") AS patient_name, patient_data.id AS message_patient_id, 							DATE_FORMAT(message_send_date,"'.get_sql_date_format().' %h:%i %p") AS msg_send_date, user_messages.flagged, user_messages.msg_icon 
						FROM user_messages USE INDEX (usermsg_multicol) LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						'.$join_on.' 
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						'.$filter.' and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0 
						'.$group_by.'
						order by'.$orderby_msg.$limit;			
			//$result_arr = $this->create_array_from_qry($req_qry);
			include_once("paging.inc.php");
			$per_page_records="20";
			if($per_page!='' && (int)$per_page>0){$per_page_records=$per_page;}
			$page = (!isset($page_get) || $page_get=="")?1:$page_get;
			$objPaging = new Paging($per_page_records,$page);
			$objPaging->sort_by = $per_page;
			$objPaging->sort_order = $case_filter;
			
			$objPaging->totalRecords = $user_message_id_cont;
			$objPaging->query = $req_qry;
			$objPaging->func_name = "load_messages";
			$result_arr = $objPaging->fetchLimitedRecords();
			$p_link1=$objPaging->getPagingString($page);
			$p_link2=$objPaging->buildComponentR8($page);
		}
		return array("0"=>$result_arr,"1"=>$p_link1,"2"=>$p_link2);
	}
	
	/*------------------------ This function is used to get the urget messages of the Messages/Reminders --------------------*/
	public function get_urgent_messages_reminders()
	{
		
		$result_arr = array();		
		$filter = '';
		$sel_user_name = ' CONCAT(users.fname," ",users.mname,", ",users.lname) ';
		$group_by = '';
		$user_msg_selection_type = 'user_messages.user_message_id';
		
		$consoleinbox1 = '';
		//$consoleinbox1 = 'user_messages.message_sender_id != '.$this->operator_id.' and ';
		$filter = ' user_messages.message_status = 0 and user_messages.receiver_delete=0 and user_messages.message_to = "'.$this->operator_id.'" and '.$consoleinbox1.'user_messages.message_sender_id > 0 and user_messages.delivery_date <= CURDATE()';
		$join_on = ' LEFT JOIN users ON user_messages.message_sender_id = users.id ';				

        $filter.=' and user_messages.saved_folder_id=0';
        
		if($filter!='')
		{
			$orderby_msg="";
			$orderby_msg .= ' user_messages.user_message_id desc';
			if(!isset($limit)) $limit = "";		
			$req_qry = 'SELECT '.$user_msg_selection_type.', user_messages.msg_type, user_messages.message_read_status, user_messages.message_subject, user_messages.message_text, user_messages.message_urgent, 
						'.$sel_user_name.' AS message_sender_name, user_messages.message_sender_id as message_sender_id,  
						user_groups.name as user_group, if(TRIM(patient_data.fname)!="",CONCAT(patient_data.lname,", ",patient_data.fname," ",patient_data.mname," - ",patient_data.id),"") AS patient_name, patient_data.id AS message_patient_id, 							DATE_FORMAT(message_send_date,"'.get_sql_date_format().' %h:%i %p") AS msg_send_date, user_messages.flagged, user_messages.msg_icon 
						FROM user_messages USE INDEX (usermsg_multicol) LEFT JOIN patient_data ON patient_data.id = user_messages.patientId 
						'.$join_on.' 
						LEFT JOIN user_groups ON user_groups.id = users.user_group_id 
						where 
						'.$filter.' and user_messages.Pt_Communication = 0 and user_messages.message_sender_id != 0 and user_messages.message_urgent =1 and user_messages.message_read_status=0
						'.$group_by.'
						order by'.$orderby_msg.$limit;			
			$result_arr = $this->create_array_from_qry($req_qry);
			return $result_arr;
		}
	}
	
	public function pt_direct_credentials($user_id)
	{
		$rq = "SELECT email, email_password FROM users WHERE id = '".$user_id."'";
		$result_arr = $this->create_array_from_qry($rq,true);
		return $result_arr;
	} 	
	
	
	/*	this function will form the the array which will be returned as query result	*/
	public function create_array_from_qry($target_qry,$single_row=false)
	{
		$target_qry = trim($target_qry);
		if($target_qry=='') die('Query could not be blank');
		$result_arr = array();
		$qry_result = imw_query($target_qry) or die('Error in Query : '.$target_qry);
		if($single_row==true)
		{
			$result_arr = imw_fetch_assoc($qry_result);	
		}
		else
		{
			while($result_row = imw_fetch_assoc($qry_result))
			{
				$result_arr[] = $result_row;
			}			
		}
		
		return $result_arr;
	}
	
	
	/*------------------------- this function is used to get the tests/tasks -------------------------------------*/
	public function get_tests_tasks($action,$sub_action="")
	{
		$action = trim($action);
		$result_arr = array();
		switch($action)
		{
			case 'scan_upload_tasks':			
				$task_status = 'sdt.task_status!=2';
				if($this->callFrom=='core')
				{
					$task_status = 'sdt.task_status=0';	
				}
				$req_qry = "SELECT sdt.task_physician_id, sdt.scan_doc_id, sdt.task_status, if(TRIM(sdt.doc_title)='',sdt.pdf_url,sdt.doc_title) AS doc_title, sdt.pdf_url, sdt.doc_type, sdt.doc_upload_type as doc_upload_type, 
							if(TRIM(doc_upload_type)='scan',sdt.scandoc_comment,if(TRIM(doc_upload_type)='upload',upload_comment,'')) as doc_comments,
							if(TRIM(doc_upload_type)='scan',DATE_FORMAT(sdt.upload_date,'".get_sql_date_format()." %h:%i %p'),if(TRIM(doc_upload_type)='upload',DATE_FORMAT(sdt.upload_docs_date,'".get_sql_date_format()." %h:%i %p'),'')) as doc_upload_date,
							DATE_FORMAT(sdt.task_review_date,'".get_sql_date_format()." %h:%i %p') AS task_review_date_new, 
							pd.pid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.pid) as patient_name, fc.folder_name 
							FROM ".$this->imedic_scan_db.".scan_doc_tbl sdt 
							INNER JOIN ".$this->dbase.".patient_data pd ON (pd.pid=sdt.patient_id) 
							LEFT JOIN ".$this->imedic_scan_db.".folder_categories fc ON (fc.folder_categories_id=sdt.folder_categories_id) 
							WHERE sdt.task_physician_id='".$this->operator_id."' and ".$task_status."  and sdt.task_status!='' order by scan_doc_id ";
				return $result_arr = $this->create_array_from_qry($req_qry);			
			break;
			case 'tests':
				/*
				$req_qry = "select 'vf' as testName,'VF' as testDesc,vf.vf_id as main_id,vf.phyName,
							date_format(vf.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"vf.comments,vf.ordrby, vf.patientId ".
							"from vf ".
							"where vf.del_status = '0' and vf.purged = '0'";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and vf.patientId = ".$this->patient_id."";
				}else{			
				$req_qry .=" and vf.finished = '0' and (vf.phyName = '' || vf.phyName = '0') and ".
							" ( vf.ordrby=".$this->operator_id." || vf.ordrby=0 ) ";
				}			
				$vf_arr = $this->create_array_from_qry($req_qry);				
				
				$req_qry = "select 'vf_gl' as testName,'VF-GL' as testDesc,vf_gl.vf_gl_id as main_id,vf_gl.phyName,
							date_format(vf_gl.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"vf_gl.comments,vf_gl.ordrby, vf_gl.patientId ".
							"from vf_gl ".
							"where vf_gl.del_status = '0' and vf_gl.purged = '0'";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and vf_gl.patientId = ".$this->patient_id."";
				}else{
				$req_qry .=" and vf_gl.finished = '0' and (vf_gl.phyName = '' || vf_gl.phyName = '0') and ".
							" ( vf_gl.ordrby=".$this->operator_id." || vf_gl.ordrby=0 ) ";
				}			
				$vf_gl_arr = $this->create_array_from_qry($req_qry);				
				
				$req_qry = "select 'nfa' as testName, 'HRT' as testDesc,nfa.nfa_id as main_id,nfa.phyName,
							date_format(nfa.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"nfa.comments,nfa.ordrby, nfa.patient_id ".
							"from nfa ".
							"where nfa.del_status = '0' and nfa.purged = '0'and nfa.finished = '0'";
							
				if($this->callFrom == "iconbar"){
				$req_qry .=" and nfa.patient_id = ".$this->patient_id."";
				}else{
				$req_qry .=" and (nfa.phyName = '' || nfa.phyName = '0') and ".
							" ( nfa.ordrby=".$this->operator_id." || nfa.ordrby=0 ) ";
				}			
				$nfa_arr = $this->create_array_from_qry($req_qry);				
				
				$req_qry = "select 'oct' as testName, 'OCT' as testDesc,oct.oct_id as main_id,oct.phyName,
							date_format(oct.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"oct.comments ,oct.ordrby, oct.patient_id ".
							"from oct ".
							"where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and oct.patient_id = ".$this->patient_id."";
				}else{
				$req_qry .=" and (oct.phyName = '' || oct.phyName = '0') and ".
							" ( oct.ordrby=".$this->operator_id." || oct.ordrby=0 ) ";
				}
				$oct_arr = $this->create_array_from_qry($req_qry);
				

				$req_qry = "select 'oct_rnfl' as testName, 'OCT-RNFL' as testDesc,oct.oct_rnfl_id as main_id,oct.phyName,
							date_format(oct.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"oct.comments ,oct.ordrby, oct.patient_id ".
							"from oct_rnfl oct ".
							"where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and oct.patient_id = ".$this->patient_id."";
				}else{
				$req_qry .=" and (oct.phyName = '' || oct.phyName = '0') and ".
							" ( oct.ordrby=".$this->operator_id." || oct.ordrby=0 ) ";
				}			
				$oct_rnfl_arr = $this->create_array_from_qry($req_qry);				
				
				
				$req_qry = "select 'test_gdx' as testName, 'GDX' as testDesc,test_gdx.gdx_id as main_id,test_gdx.phyName,
							date_format(test_gdx.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"test_gdx.phyName, ".
							"test_gdx.comments ,test_gdx.ordrby, test_gdx.patient_id ".
							"from test_gdx ".
							"where test_gdx.del_status = '0' and test_gdx.purged = '0' and test_gdx.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and test_gdx.patient_id  = ".$this->patient_id."";
				}else{
				$req_qry .=" and (test_gdx.phyName = '' || test_gdx.phyName = '0') and ".
							" ( test_gdx.ordrby=".$this->operator_id." || test_gdx.ordrby=0 ) ";
				}			
				$gdx_arr = $this->create_array_from_qry($req_qry);
				
				$req_qry = "select 'pachy' as testName, 'Pachy' as testDesc,pachy.pachy_id as main_id,pachy.phyName,
							date_format(pachy.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"pachy.comments,pachy.ordrby, pachy.patientId ".
							"from pachy  ".
							"where pachy.del_status = '0' and pachy.purged = '0' and pachy.finished = '0' ";
				if($this->callFrom == "iconbar"){
					$req_qry .=" and pachy.patientId  = ".$this->patient_id."";
				}else{
				$req_qry .=" and (pachy.phyName = '' || pachy.phyName = '0') and ".
							" ( pachy.ordrby=".$this->operator_id." || pachy.ordrby=0 ) ";
				}
				$pachy_arr = $this->create_array_from_qry($req_qry);
				
				
				$req_qry = "select 'ivfa' as testName, 'IVFA' as testDesc,ivfa.vf_id as main_id,ivfa.phy as phyName,
							date_format(ivfa.exam_date,'".get_sql_date_format('','y')."') as taskDate, ".
							"ivfa.ivfaComments as comments,ivfa.ordrby, ivfa.patient_id ".
							"from ivfa ".
							"where ivfa.del_status = '0' and ivfa.purged = '0' and ivfa.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and ivfa.patient_id = ".$this->patient_id."";
				}else{
				$req_qry .=" and (ivfa.phy = '' || ivfa.phy = '0') and ".
							" ( ivfa.ordrby=".$this->operator_id." || ivfa.ordrby=0 ) ";
				}			
				$ivfa_arr = $this->create_array_from_qry($req_qry);
				
				
				$req_qry = "select 'disc' as testName, 'Fundus' as testDesc,disc.disc_id as main_id,disc.phyName,
							date_format(disc.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"disc.discComments as comments, disc.ordrby, disc.patientId ".
							"from disc ".
							"where disc.del_status = '0' and disc.purged = '0' and disc.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and disc.patientId  = ".$this->patient_id."";
				}else{
				$req_qry .=" and (disc.phyName = '' || disc.phyName = '0') and ".
							" ( disc.ordrby=".$this->operator_id." || disc.ordrby=0 ) ";
				}			
				$disc_arr = $this->create_array_from_qry($req_qry);
				
				$req_qry = "select 'discexternal' as testName, 'External / Anterior' as testDesc,
							disc_external.disc_id as main_id, date_format(disc_external.examDate,'".get_sql_date_format('','y')."') as taskDate,disc_external.phyName, ".
							"disc_external.discComments as comments, disc_external.ordrby, disc_external.patientId ".
							"from disc_external join patient_data on patient_data.id = disc_external.patientId ".
							"where disc_external.del_status = '0' and disc_external.purged = '0' and disc_external.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and disc_external.patientId = ".$this->patient_id."";
				}else{
				$req_qry .=" and (disc_external.phyName = '' || disc_external.phyName = '0') and ".
							" ( disc_external.ordrby=".$this->operator_id." || disc_external.ordrby=0 ) ";
				}
				$discexternal_arr = $this->create_array_from_qry($req_qry);
				
				
				$req_qry = "select 'topography' as testName, 'Topography' as testDesc,topography.topo_id as main_id,topography.phyName,
							date_format(topography.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"topography.comments, topography.ordrby, topography.patientId ".
							"from topography ".
							"where topography.del_status = '0' and topography.purged = '0' and topography.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and topography.patientId = ".$this->patient_id."";
				}else{
				$req_qry .=" and (topography.phyName = '' || topography.phyName = '0') and ".
							" ( topography.ordrby=".$this->operator_id." || topography.ordrby=0 ) ";
				}
				$topography_arr = $this->create_array_from_qry($req_qry);
				
								
				$req_qry = "select 'surgical_tbl' as testName, 'A/Scan' as testDesc,surgical_tbl.surgical_id as main_id,surgical_tbl.signedById as phyName,
							date_format(surgical_tbl.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							" surgical_tbl.ordrby, surgical_tbl.patient_id ".
							" from surgical_tbl ".
							"where surgical_tbl.del_status = '0' and surgical_tbl.purged = '0' and surgical_tbl.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and surgical_tbl.patient_id = ".$this->patient_id."";
				}else{
				$req_qry .=" and (surgical_tbl.signedById = '' || surgical_tbl.signedById = '0') and ".
							" ( surgical_tbl.ordrby=".$this->operator_id." || surgical_tbl.ordrby=0 ) ";
				}			
				$ascan_arr = $this->create_array_from_qry($req_qry);
				
				
				$req_qry = "select 'test_bscan' as testName, 'B-scan' as testDesc,test_bscan.test_bscan_id as main_id,test_bscan.phyName,
							date_format(test_bscan.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"test_bscan.techComments as comments, test_bscan.ordrby, test_bscan.patientId ".
							"from test_bscan ".
							"where test_bscan.del_status = '0' and test_bscan.purged = '0' and test_bscan.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and test_bscan.patientId = ".$this->patient_id."";
				}else{
				$req_qry .=" and (test_bscan.phyName = '' || test_bscan.phyName = '0') and ".
							" ( test_bscan.ordrby=".$this->operator_id." || test_bscan.ordrby=0 ) ";
				}			
				$test_bscan_arr = $this->create_array_from_qry($req_qry);
				

				$req_qry = "select 'test_labs' as testName, 'Laboratories' as testDesc,test_labs.test_labs_id as main_id,test_labs.phyName,
							date_format(test_labs.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"test_labs.techComments as comments, test_labs.ordrby, test_labs.patientId ".
							"from test_labs  ".
							"where test_labs.del_status = '0' and test_labs.purged = '0' and test_labs.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and test_labs.patientId = ".$this->patient_id."";
				}else{
				$req_qry .=" and (test_labs.phyName = '' || test_labs.phyName = '0') and ".
							" ( test_labs.ordrby=".$this->operator_id." || test_labs.ordrby=0 ) ";
				}
				$labs_arr = $this->create_array_from_qry($req_qry);	
				
				
				$req_qry = "select 'test_other' as testName, 'Test Other' as testDesc,test_other.test_other_id as main_id,test_other.phyName,
							date_format(test_other.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"test_other.techComments as comments, test_other.ordrby, test_other.patientId ".
							"from test_other  ".
							"where test_other.del_status = '0' and test_other.purged = '0' and test_other.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and test_other.patientId = ".$this->patient_id."";
				}else{
				$req_qry .=" and (test_other.phyName = '' || test_other.phyName = '0') and ".
							" ( test_other.ordrby=".$this->operator_id." || test_other.ordrby=0 ) ";
				}
				$test_other_arr = $this->create_array_from_qry($req_qry);
				
				
				$req_qry = "select 'test_cellcnt' as testName, 'Cell Count' as testDesc,test_cellcnt.test_cellcnt_id as main_id,test_cellcnt.phyName,
							date_format(test_cellcnt.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"test_cellcnt.techComments as comments, test_cellcnt.ordrby, test_cellcnt.patientId ".
							"from test_cellcnt  ".
							"where test_cellcnt.del_status = '0' and test_cellcnt.purged = '0' and test_cellcnt.finished = '0' ";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and test_cellcnt.patientId = ".$this->patient_id."";
				}else{
				$req_qry .=" and (test_cellcnt.phyName = '' || test_cellcnt.phyName = '0') and ".
							" ( test_cellcnt.ordrby=".$this->operator_id." || test_cellcnt.ordrby=0 ) ";
				}
				$test_cellcnt_arr = $this->create_array_from_qry($req_qry);
				
				
				$req_qry = "select 'icg' as testName, 'ICG' as testDesc,icg.icg_id as main_id,icg.phy as phyName,
							date_format(icg.exam_date,'".get_sql_date_format('','y')."') as taskDate, ".
							"icg.comments_icg as comments, icg.ordrby, icg.patient_id ".
							"from icg  ".
							"where icg.del_status = '0' and icg.purged = '0' and icg.finished = '0'";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and icg.patient_id = ".$this->patient_id."";
				}else{
				$req_qry .=" and (icg.phy = '' || icg.phy = '0') and ".
							" ( icg.ordrby=".$this->operator_id." || icg.ordrby=0 ) ";
				}
				$icg_arr = $this->create_array_from_qry($req_qry);
				

				$req_qry = "select 'iol_master_tbl' as testName,'IOL Master' as testDesc,iol_master_tbl.iol_master_id as main_id,iol_master_tbl.performedByOD as phyName,
							date_format(iol_master_tbl.examDate,'".get_sql_date_format('','y')."') as taskDate, ".
							"('') as comments,iol_master_tbl.ordrby, iol_master_tbl.patient_id ".
							"from iol_master_tbl ".
							"where iol_master_tbl.del_status = '0' and iol_master_tbl.purged = '0'";
				if($this->callFrom == "iconbar"){
				$req_qry .=" and iol_master_tbl.patient_id = ".$this->patient_id."";
				}else{		
				$req_qry .=" and iol_master_tbl.finished = '0' and (iol_master_tbl.signedById = '' || iol_master_tbl.signedById = '0') and ".
							" ( iol_master_tbl.ordrby=".$this->operator_id." || iol_master_tbl.ordrby=0 ) ";
				}
				$IOL_OCT_arr = $this->create_array_from_qry($req_qry);
				

				$result_arr = array_merge($vf_arr,$vf_gl_arr,$nfa_arr,$oct_arr,$oct_rnfl_arr,$gdx_arr,$pachy_arr,$ivfa_arr,$disc_arr,$discexternal_arr,$topography_arr,$ascan_arr,$test_bscan_arr,$labs_arr,$test_other_arr,$test_cellcnt_arr,$icg_arr,$IOL_OCT_arr);
				*/
				$result_arr = $this->ObjTests->un_interpreted_tests($this->callFrom);
				return $result_arr;
			break;
			case 'completed_tasks':
				$vf_arr=$vf_gl_arr=$nfa_arr=$oct_arr=$oct_rnfl_arr=$pachy_arr=$ivfa_arr=$disc_arr=$discexternal_arr=$topography_arr=$gdx_arr=$ascan_arr=$test_bscan_arr=$labs_arr=$test_other_arr=$completed_user_msg_arr=$phy_to_do_task_arr=$icg_arr=$test_cellcount_arr=array();
				if($sub_action=="" || $sub_action=="comp_tasks"){
					/*---------------------------- Test VF ---------------------------------------- */
					$req_qry = "select 'VF' as TableName,vf.performedBy,date_format(vf.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(vf.curDate,'".get_sql_date_format('','y')."') as cur_date, 'vf' as tb_name, 
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,vf.phyName as completed_by,
								vf.vf_id as tid,'VF' AS message_text,pd.id from vf 
								join patient_data pd on pd.id = vf.patientId
								where vf.del_status = '0' and vf.purged = '0' and vf.finished = '0' and vf.phyName = '".$this->operator_id."'
								order by vf.examDate desc,pd.lname,pd.fname";
					$vf_arr = $this->create_array_from_qry($req_qry);
					/*---------------------------- Test VF-GL ---------------------------------------- */				
					$req_qry = "select 'VF-GL' as TableName,vf.performedBy,date_format(vf.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(vf.curDate,'".get_sql_date_format('','y')."') as cur_date, 'vf_gl' as tb_name, 
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,vf.phyName as completed_by,
								vf.vf_gl_id as tid,'VF' AS message_text,pd.id from vf_gl vf 
								join patient_data pd on pd.id = vf.patientId
								where vf.del_status = '0' and vf.purged = '0' and vf.finished = '0' and vf.phyName = '".$this->operator_id."'
								order by vf.examDate desc,pd.lname,pd.fname";
					$vf_gl_arr = $this->create_array_from_qry($req_qry);
					/*-------------------------- Test HRT (nfa) ----------------------------------*/
					$req_qry = "select 'HRT' as TableName,nfa.performBy as performedBy,date_format(nfa.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(nfa.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'nfa' as tb_name,		
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,nfa.phyName as completed_by,
								nfa.nfa_id as tid,'HRT' as message_text, pd.id from nfa 
								join patient_data pd on pd.id = nfa.patient_id
								where nfa.del_status = '0' and nfa.purged = '0' and nfa.finished = '0' and nfa.phyName = '".$this->operator_id."'
								order by nfa.examDate desc,pd.lname,pd.fname";
					$nfa_arr = $this->create_array_from_qry($req_qry);
					/*----------------------------- Test OCT -----------------------------------------*/				
					$req_qry = "select 'OCT' as TableName,oct.oct_id as tid,date_format(oct.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(oct.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'oct' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,oct.phyName as completed_by,
								oct.performBy as performedBy, 'OCT' as message_text, pd.id from oct 
								join patient_data pd on pd.id = oct.patient_id
								where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' and oct.phyName = '".$this->operator_id."'
								order by oct.examDate desc";							
					$oct_arr = $this->create_array_from_qry($req_qry);
					/*----------------------------- Test OCT-RNFL -----------------------------------------*/				
					$req_qry = "select 'OCT-RNFL' as TableName,oct.oct_rnfl_id as tid,date_format(oct.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(oct.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'oct_rnfl' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,oct.phyName as completed_by,
								oct.performBy as performedBy, 'OCT' as message_text, pd.id from oct_rnfl oct 
								join patient_data pd on pd.id = oct.patient_id
								where oct.del_status = '0' and oct.purged = '0' and oct.finished = '0' and oct.phyName = '".$this->operator_id."'
								order by oct.examDate desc";							
					$oct_rnfl_arr = $this->create_array_from_qry($req_qry);
					/*--------------------------------- Test Pachy ---------------------------------*/
					$req_qry = "select 'PACHY' as TableName,pachy.performedBy,date_format(pachy.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(pachy.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'pachy' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,pachy.phyName as completed_by,
								pachy.pachy_id as tid,'Pachy' as message_text, pd.id from pachy 
								join patient_data pd on pd.id = pachy.patientId
								where pachy.del_status = '0' and pachy.purged = '0' and pachy.finished = '0' and pachy.phyName = '".$this->operator_id."'
								order by pachy.examDate desc,pd.lname,pd.fname";													
					$pachy_arr = $this->create_array_from_qry($req_qry);
					/*------------------------------- Test IVFA ------------------------------*/
					$req_qry = "select 'IVFA' as TableName,ivfa.vf_id as tid,date_format(ivfa.exam_date,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(ivfa.cur_date ,'".get_sql_date_format('','y')."') as cur_date, 'ivfa' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,ivfa.phy as completed_by,
								ivfa.performed_by as performedBy,'IVFA' as message_text, pd.id from ivfa 
								join patient_data pd on pd.id = ivfa.patient_id
								where ivfa.del_status = '0' and ivfa.purged = '0' and ivfa.finished = '0' and ivfa.phy = '".$this->operator_id."'
								order by ivfa.exam_date desc,pd.lname,pd.fname";
					$ivfa_arr = $this->create_array_from_qry($req_qry);
					/*-------------------------------- Test Disc -------------------------------------*/
					$req_qry = "select 'DISC' as TableName,disc.disc_id as tid,date_format(disc.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(disc.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'disc' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,disc.phyName as completed_by,
								disc.performedBy,'DISC' as message_text, pd.id from disc 
								join patient_data pd on pd.id = disc.patientId
								where disc.del_status = '0' and disc.purged = '0' and disc.finished = '0' and disc.phyName = '".$this->operator_id."'
								order by disc.examDate desc,pd.lname,pd.fname";
					$disc_arr = $this->create_array_from_qry($req_qry);
					/*----------------------------- Test Disc External -------------------------------*/
					$req_qry = "select 'EXTERNAL/ANTERIOR' as TableName,disc_external.disc_id as tid,date_format(disc_external.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(disc_external.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'disc_external' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,disc_external.phyName as completed_by,
								disc_external.performedBy,'External/Anterior' as message_text, pd.id from disc_external 
								join patient_data pd on pd.id = disc_external.patientId
								where disc_external.del_status = '0' and disc_external.purged = '0' and disc_external.finished = '0' and disc_external.phyName = '".$this->operator_id."' order by disc_external.examDate desc";
					$discexternal_arr = $this->create_array_from_qry($req_qry);
					/*------------------------------------ Test Topography ---------------------------------------*/
					$req_qry = "select 'TOPOGRAPHY' as TableName,topography.topo_id as tid,date_format(topography.examDate,'".get_sql_date_format('','y')."') as taskDate,
								date_format(topography.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'topography' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,topography.phyName as completed_by,
								topography.performedBy,'Topography' as message_text, pd.id from topography 
								join patient_data pd on pd.id = topography.patientId
								where topography.del_status = '0' and topography.purged = '0' and topography.finished = '0' and topography.phyName = '".$this->operator_id."' order by topography.examDate DESC";
					$topography_arr = $this->create_array_from_qry($req_qry);
					/*------------------------------------ Test GDX ---------------------------------------*/
					$req_qry = "select 'GDX' as TableName,test_gdx.gdx_id as tid,date_format(test_gdx.examDate,'".get_sql_date_format('','y')."') as taskDate,
								date_format(test_gdx.cur_date,'".get_sql_date_format('','y')."') as cur_date, 'test_gdx' as tb_name,
								concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,test_gdx.phyName as completed_by,
								test_gdx.performBy as performedBy,'GDX' as message_text, pd.id from test_gdx 
								join patient_data pd on pd.id = test_gdx.patient_id
								where test_gdx.del_status = '0' and test_gdx.purged = '0' and test_gdx.finished = '0' and test_gdx.phyName = '".$this->operator_id."' order by test_gdx.examDate DESC";

					$gdx_arr = $this->create_array_from_qry($req_qry);
					/*------------------------------------ Test ASCAN ---------------------------------------*/
					$req_qry = "select 'A/SCAN' as TableName,surgical_tbl.performedByOD as performedBy, 'surgical_tbl' as tb_name, 'A/Scan' as message_text,
								surgical_tbl.surgical_id as tid,
								date_format(surgical_tbl.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(surgical_tbl.dateOS,'".get_sql_date_format('','y')."') as cur_date,
								concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,surgical_tbl.signedById as completed_by,
								patient_data.id, surgical_tbl.ordrby, patient_data.providerID from surgical_tbl 
								join patient_data on patient_data.id = surgical_tbl.patient_id
								where surgical_tbl.del_status = '0' and surgical_tbl.purged = '0' and surgical_tbl.finished = '0' and surgical_tbl.signedById = '".$this->operator_id."'";							
					$ascan_arr = $this->create_array_from_qry($req_qry);
	
					/*------------------------------------ Test BSCAN ---------------------------------------*/
					$req_qry = "select 'B-SCAN' as TableName,test_bscan.performedBy  as performedBy, 'test_bscan' as tb_name, 'B-scan' as message_text,
								test_bscan.test_bscan_id as tid,
								date_format(test_bscan.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(test_bscan.cur_date,'".get_sql_date_format('','y')."') as cur_date,
								concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,test_bscan.phyName as completed_by,
								test_bscan.techComments as comments,patient_data.id, test_bscan.ordrby, patient_data.providerID from test_bscan 
								join patient_data on patient_data.id = test_bscan.patientId
								where test_bscan.del_status = '0' and test_bscan.purged = '0' and test_bscan.finished = '0' and test_bscan.phyName = '".$this->operator_id."'";							
					$test_bscan_arr = $this->create_array_from_qry($req_qry);
					/*------------------------------------ Test LABS ---------------------------------------*/
					$req_qry = "select 'LABORATORIES' as TableName,test_labs.performedBy  as performedBy, 'test_labs' as tb_name, 'Laboratories' as message_text,
								test_labs.test_labs_id as tid,
								date_format(test_labs.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(test_labs.cur_date,'".get_sql_date_format('','y')."') as cur_date,
								concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,test_labs.phyName as completed_by,
								test_labs.techComments as comments,patient_data.id, test_labs.ordrby, patient_data.providerID from test_labs  
								join patient_data on patient_data.id = test_labs.patientId
								where test_labs.del_status = '0' and test_labs.purged = '0' and test_labs.finished = '0' and test_labs.phyName = '".$this->operator_id."'";							
					$labs_arr = $this->create_array_from_qry($req_qry);	
					/*------------------------------------ Test Other ---------------------------------------*/
					$req_qry = "select 'OTHER TEST' as TableName,test_other.performedBy  as performedBy, 'test_other' as tb_name, 'Test Other' as message_text,
								test_other.test_other_id as tid,
								date_format(test_other.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(test_other.cur_date,'".get_sql_date_format('','y')."') as cur_date,
								concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,test_other.phyName as completed_by,
								test_other.techComments as comments,patient_data.id, test_other.ordrby, patient_data.providerID from test_other  
								join patient_data on patient_data.id = test_other.patientId
								where test_other.del_status = '0' and test_other.purged = '0' and test_other.finished = '0' and test_other.phyName = '".$this->operator_id."'";							
					$test_other_arr = $this->create_array_from_qry($req_qry);	
					
					$req_qry = "select 'ICG' as TableName,icg.performed_by  as performedBy, 'icg' as tb_name, 'ICG' as message_text,
								icg.icg_id as tid,
								date_format(icg.exam_date,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(icg.cur_date,'".get_sql_date_format('','y')."') as cur_date,
								concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,icg.phy as completed_by,
								icg.comments_icg as comments,patient_data.id, icg.ordrby, patient_data.providerID from icg  
								join patient_data on patient_data.id = icg.patient_id
								where icg.del_status = '0' and icg.purged = '0' and icg.finished = '0' and icg.phy = '".$this->operator_id."'";							
					$icg_arr = $this->create_array_from_qry($req_qry);	
					
					$req_qry = "select 'CELL COUNT' as TableName,test_cellcnt.performedBy  as performedBy, 'test_cellcnt' as tb_name, 'Test Cellcount' as message_text,
								test_cellcnt.test_cellcnt_id as tid,
								date_format(test_cellcnt.examDate,'".get_sql_date_format('','y')."') as taskDate, 
								date_format(test_cellcnt.cur_date,'".get_sql_date_format('','y')."') as cur_date,
								concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,' - ',patient_data.id) as patient_name,test_cellcnt.phyName as completed_by,
								test_cellcnt.techComments as comments,patient_data.id, test_cellcnt.ordrby, patient_data.providerID 
								from test_cellcnt  
								join patient_data on patient_data.id = test_cellcnt.patientId
								where test_cellcnt.del_status = '0' and test_cellcnt.purged = '0' and test_cellcnt.finished = '0' and test_cellcnt.phyName = '".$this->operator_id."'";							
					$test_cellcount_arr = $this->create_array_from_qry($req_qry);
					
					/*-------------------------------- Completed physician to do task ----------------------------------*/
					$req_qry = "select 'PHY. TO-DO' as TableName,phy_todo_task.phy_todo_task_id as tid, phy_todo_task.task_by as performedBy,
								phy_todo_task.patient_name, 'phy_todo_task' as tb_name,
								date_format(task_completed_date,'".get_sql_date_format('','y')."') as cur_date,
								date_format(task_created_date,'".get_sql_date_format('','y')."') as taskDate, task_text as message_text
								from phy_todo_task where task_status = '1' and task_completed_by = '".$this->operator_id."' AND del_status='0'
								order by task_created_date desc";
					$phy_to_do_task_arr = $this->create_array_from_qry($req_qry);
				}
				if($sub_action=="" || $sub_action=="comp_msg"){
					/*------------------------------- Completed user messages ---------------------------*/
					$req_qry = "select 'MESSAGES' as TableName,
									user_messages.user_message_id as tid, 
									user_messages.message_sender_id as performedBy,
									user_messages.message_subject as message_subject, 
									user_messages.message_text,pd.id as id,
									user_messages.message_read_status,
									date_format(user_messages.message_send_date,'".get_sql_date_format('','y')."') as taskDate, 
									'user_messages' as tb_name,
									date_format(user_messages.message_completed_date,'".get_sql_date_format('','y')."') as cur_date,
									concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,
									concat(users.lname,', ',users.fname,' ',users.mname) as completed_by_name
								from user_messages 
								JOIN users ON users.id = user_messages.msg_completed_by
								left join patient_data pd on pd.id = user_messages.patientId
								where message_status = '1' 
									AND user_messages.del_status='0'
									AND msg_completed_by = IF(msg_id = 0,'".$this->operator_id."',msg_completed_by)
									AND message_to = IF(msg_id = 0, message_to, '".$this->operator_id."')
								group by (CASE WHEN(msg_id != 0) THEN msg_id ELSE user_message_id END)	 
								order by message_completed_date desc
								";//echo $req_qry;
					$completed_user_msg_arr = $this->create_array_from_qry($req_qry);
				}
				
				$result_arr = array_merge($vf_arr,$vf_gl_arr,$nfa_arr,$oct_arr,$oct_rnfl_arr,$pachy_arr,$ivfa_arr,$disc_arr,$discexternal_arr,$topography_arr,$gdx_arr,$ascan_arr,$test_bscan_arr,$labs_arr,$test_other_arr,$completed_user_msg_arr,$phy_to_do_task_arr,$icg_arr,$test_cellcount_arr);				
				
				/*----------------------------- GET Associated Orders with chart notes ------------------------*/
				if($sub_action=="" || $sub_action=="comp_order_set" || $sub_action=="comp_orders"){
					$qry = "select id from order_details where delete_status = '0' and 
							(resp_person = '".$this->operator_id."' or resp_person like '".$this->operator_id.",%'
							or resp_person like '%,".$this->operator_id."' or resp_person like '%,".$this->operator_id.",%')";
					if(empty($this->operator_group_id) == false){
						$qry .= " or (resp_group = '".$this->operator_group_id."' or resp_group like '%,".$this->operator_group_id.",%'
								or resp_group like '%,".$this->operator_group_id."' or resp_group like '".$this->operator_group_id.",%')";
					}
					//get ordrids
					$ordersIdArr = array();
					while ($ordersDetails = imw_fetch_assoc($qry)) {
						$id = $ordersDetails['id'];
						$ordersIdArr[] = $ordersDetails['id'];
					}
					$ordersIdStr = join(',', $ordersIdArr);
					$ordersIdStr_phrs = "";
					if (!empty($ordersIdStr)) {
						$ordersIdStr_phrs = " order_set_associate_chart_notes_details.order_id in ($ordersIdStr) OR ";
					}
					
					
					$req_qry = "select 'ORDERS' as TableName,order_set_associate_chart_notes.order_set_associate_id as primary_set_id,
								order_set_associate_chart_notes.order_set_id, 'order_set_associate_chart_notes_details' as tb_name,
								order_set_associate_chart_notes_details.order_set_associate_details_id as tid, 
								order_set_associate_chart_notes.patient_id ,
								order_set_associate_chart_notes.logged_provider_id ,
								date_format(order_set_associate_chart_notes.created_date,'".get_sql_date_format('','y')."') as c_date,
								date_format(order_set_associate_chart_notes_details.modified_date,'".get_sql_date_format('','y')."') as m_date, 
								order_set_associate_chart_notes_details.*,
								pd.lname,pd.fname,pd.mname,pd.id 
								from order_set_associate_chart_notes left join
								order_set_associate_chart_notes_details on
								order_set_associate_chart_notes.order_set_associate_id = 
								order_set_associate_chart_notes_details.order_set_associate_id
								join patient_data pd on pd.id = 
								order_set_associate_chart_notes.patient_id
								where order_set_associate_chart_notes_details.orders_status = '2'
								and order_set_associate_chart_notes_details.delete_status = '0'
								and (	" . $ordersIdStr_phrs . " " .
									" (order_set_associate_chart_notes_details.resp_person = '".$this->operator_id."' or order_set_associate_chart_notes_details.resp_person like '%,".$this->operator_id.",%' " .
									"	or order_set_associate_chart_notes_details.resp_person like '%,".$this->operator_id."' or order_set_associate_chart_notes_details.resp_person like '".$this->operator_id.",%') " .
								")
								order by order_set_associate_chart_notes.created_date desc";					
					$orders_data_arr = $this->create_array_from_qry($req_qry);	
				}
				if(count($orders_data_arr)>0 || count($result_arr)>0){
					$user_qry = "select id,lname,fname,mname from users";
					$user_arr = array();
					$result_object = imw_query($user_qry) or die('Query not worked');
					while($result_row = imw_fetch_assoc($result_object))
					{
						$user_arr[$result_row['id']] = $result_row['lname'].', '.$result_row['fname'].' '.$result_row['mname'];  	
					}
				}

				$final_arr =array('users_arr' => $user_arr,'result_arr' => $result_arr, 'orders_arr' => $orders_data_arr);
				return $final_arr;	
			break;
		}
	}
	
	public function get_patient_more_info($pt_id, $sel=""){
		if(intval($pt_id)<=0){$pt_id = $this->patient_id;}
		if(empty($sel)){ $sel=" *, date_format(DOB,'".get_sql_date_format()."') as DOB ";  }
		$query = "SELECT ".$sel."  FROM patient_data WHERE id = '".$pt_id."' LIMIT 0,1";
		$result = imw_query($query);
		$rs = imw_fetch_assoc($result);
		return $rs;
	}
	
	/* this function get all the active flags for pending tasks/msgs for provider */
	public function get_active_flags($particular_status="")
	{
		$particular_status = trim($particular_status);
		$data_var = array();
		
		$search_type = 'All Users';
		if($this->operator_type == 3){
			$search_type = 'All Technicians';
		} 
		else if($this->operator_type == 5){
			$search_type = 'All Staff';
		}
					
		$data_var['unread_messages']= 'unread_messages.unread_messages_status';
		$data_var['unfinalized_patients']= 'unfinalized_patients.unfinalized_patients_status';
		$data_var['phy_to_do_task'] = 'phy_to_do_task.phy_to_do_task_status';
		$data_var['phy_notes'] = 'paymentscomment.commentId';
		$data_var['unread_scan_docs']= 'unread_scan_docs.unread_scan_docs_status';
		$data_var['un_consent_forms']= 'un_consent_forms.un_consent_form_status';					
		$data_var['un_sx_consent_forms']= 'un_sx_consent_forms.un_sx_consent_forms_status';
		$data_var['un_op_notes']= 'un_op_notes.un_op_notes_status';
		$data_var['un_consult_letters']= 'un_consult_letters.un_consult_letters_status';
		$data_var['orders']= 'orders.orders_flag';
		/* ------------ Query for unread messages -----------------------*/
		$qry_unread_messages = 'select count(message_read_status) AS unread_messages_status from user_messages 
								where message_read_status = 0 and Pt_Communication = 0 and receiver_delete="0" 
								and (message_to = "'.$this->operator_id.'" || message_to = "All Users" ||
								message_to = "'.$search_type.'") AND message_to != "0" AND message_sender_id != "'.$this->operator_id.'" AND message_sender_id != "0" and message_status = 0 AND delivery_date <= CURDATE() LIMIT 0, 1';
		
		/* ------------ Query for unfinalized patients -----------------------*/
		$qry_unfinalized_patients = 'SELECT count(chart_master_table.id) AS unfinalized_patients_status FROM 
									chart_master_table LEFT JOIN chart_assessment_plans 
									ON chart_assessment_plans.form_id = chart_master_table.id 
									WHERE chart_master_table.finalize = 0 AND chart_master_table.delete_status = 0 
									AND (chart_assessment_plans.doctorId = "'.$this->operator_id.'" 
									OR chart_master_table.providerId = "'.$this->operator_id.'") LIMIT 0,1';
		
		/* ------------ Query for unsigned consent forms -----------------------*/
		$qry_un_consent_forms = 'SELECT count(id) AS un_consent_form_status FROM consent_hold_sign WHERE consent_id != 0 AND physician_id = "'.$this->operator_id.'" AND signed = 0 LIMIT 0,1';					
		
		/* ------------ Query for unsigned surgery consent forms -----------------------*/
		$qry_un_surgery_consent_forms = 'SELECT count(id) AS un_sx_consent_forms_status FROM consent_hold_sign WHERE sx_consent_id != 0 AND physician_id = "'.$this->operator_id.'" AND signed = 0 LIMIT 0,1';
		
		/* ------------ Query for unsigned op notes -----------------------*/
		$qry_un_opnotes = 'SELECT count(chs.id) AS un_op_notes_status FROM consent_hold_sign chs JOIN pn_reports pnr ON (pnr.pn_rep_id = chs.opnote_id) JOIN pn_template pnt ON (pnr.tempId = pnt.temp_id) WHERE chs.signed = 0 AND chs.physician_id="'.$this->operator_id.'" LIMIT 0,1';

		/* ------------ Query for unsigned consult letters -----------------------*/
		$qry_un_consult_letters = 'SELECT count(id) AS un_consult_letters_status FROM consent_hold_sign WHERE consult_id != 0 AND physician_id = "'.$this->operator_id.'" AND signed = 0 LIMIT 0,1';

		/* ------------ Query for unread scan documents  -----------------------*/
		/*
		$qry_unread_scan_docs = 'SELECT count(sdt.task_physician_id) AS unread_scan_docs_status FROM '.constant("IMEDIC_SCAN_DB").'.scan_doc_tbl sdt  
								INNER JOIN '.$this->dbase.'.patient_data pd ON (pd.pid=sdt.patient_id) 
								LEFT JOIN '.$this->imedic_scan_db.'.folder_categories fc ON (fc.folder_categories_id=sdt.folder_categories_id) 
								WHERE (sdt.task_physician_id = "'.$_SESSION['authId'].'") AND sdt.task_status = 0 AND sdt.task_status != "" ';
		*/						
		/*$qry_unread_scan_docs = 'SELECT count(sdt.task_physician_id) AS unread_scan_docs_status FROM '.constant("IMEDIC_SCAN_DB").'.scan_doc_tbl sdt 
								USE INDEX(scandoctbl_ptidtaskphyidtaskstatus) 
								INNER JOIN '.$this->dbase.'.patient_data pd ON (pd.pid=sdt.patient_id) 
								WHERE (sdt.task_physician_id = "'.$_SESSION['authId'].'") AND sdt.task_status = 0 AND sdt.task_status != "" ';*/		
		
		/* ------------ Query for Order/Order Sets -----------------------*/
		$qry_orders = 'Select id from order_details where delete_status = 0 and 
					((resp_person = "'.$this->operator_id.'" or resp_person like "'.$this->operator_id.',%"
					or resp_person like "%,'.$this->operator_id.'" or resp_person like "%,'.$this->operator_id.',%")';
		if(empty($this->operator_group_id) == false){
			$qry_orders .= ' or (resp_group = "'.$this->operator_group_id.'" or resp_group like "%,'.$this->operator_group_id.',%"
							or resp_group like "%,'.$this->operator_group_id.'" or resp_group like "'.$this->operator_group_id.',%")';
		}
			$qry_orders .= ")";
		
		$qry_orders_final = 'Select count(order_set_associate_chart_notes.order_set_associate_id) AS orders_flag
							from order_set_associate_chart_notes 
							left join order_set_associate_chart_notes_details on
							order_set_associate_chart_notes.order_set_associate_id = order_set_associate_chart_notes_details.order_set_associate_id
							where order_set_associate_chart_notes_details.orders_status != 2
							and order_set_associate_chart_notes_details.delete_status = 0
							and order_set_associate_chart_notes_details.order_id in ('.$qry_orders.') limit 0,1';
		//) AS orders

		/*------------------------Query for Physician to do task--------------------------------*/
		$phy_to_do_task = "select count(task_read_status) AS phy_to_do_task_status from phy_todo_task 
							where task_read_status = 0 and (task_to = '".$this->operator_id."' || task_to = 'All Users') 
							and task_status = 0 LIMIT 0,1 ";	
		
		/*------------------------Query for Physician/accounting notes --------------------------------*/
		$phy_notes = "SELECT COUNT(pay.commentId) AS phy_notes
						FROM paymentscomment pay 
						LEFT JOIN users usr ON (usr.id = pay.task_assign_by)
						WHERE 
							usr.delete_status = 0 AND
							pay.task_assign_for IN ('".$this->operator_id."') AND 
							pay.task_done = 1 AND 
							pay.task_assign = 2";	
		
		/* ------------ Combined query of above defined queries -----------------------*/
		$select = 'SELECT ';
		$from = ' FROM ';
		/*------------------------ particular status is used to get the status of particular option of physician console. -----------------------------*/
		if(trim($particular_status) != '')
		{
			switch($particular_status)
			{
				case 'unread_messages':
					$final_qry = $qry_unread_messages;
				break;				
				case 'unfinalized_patients':
					$final_qry = $qry_unfinalized_patients;
				break;
				case 'unread_scan_docs':
					//$final_qry = $qry_unread_scan_docs;
				break;
				case 'un_consent_forms':
					$final_qry = $qry_un_consent_forms;
				break;
				case 'un_sx_consent_forms':
					$final_qry = $qry_un_surgery_consent_forms;
				break;
				case 'un_op_notes':
					$final_qry = $qry_un_opnotes;
				break;
				case 'un_consult_letters':
					$final_qry = $qry_un_consult_letters;
				break;
				case 'orders':
					$final_qry = $qry_orders_final;
				break;
				case 'phy_to_do_task':
					$final_qry = $phy_to_do_task;
				break;
				case 'phy_notes':
					$final_qry = $phy_notes;
				break;	
			}
			$result_arr = $this->create_array_from_qry($final_qry,true);
		}
		else
		{
			if($this->operator_type == 1)
			{
				$msg_pat_query = $select.$data_var['unread_messages'].','.$data_var['unfinalized_patients'].$from.'('.$qry_unread_messages.') AS unread_messages, ('.$qry_unfinalized_patients.') AS unfinalized_patients';
				$msg_pt_arr = $this->create_array_from_qry($msg_pat_query,true);		
			}else{
				$msg_pat_query = $qry_unread_messages;
				$msg_pt_arr = $this->create_array_from_qry($msg_pat_query,true);
			}
			$phy_scan_op_qry = $select.$data_var['phy_to_do_task'].','.$data_var['un_op_notes'].$from.'('.$qry_un_opnotes.') AS un_op_notes, ('.$phy_to_do_task.') AS phy_to_do_task';
			$phy_scan_op_arr = $this->create_array_from_qry($phy_scan_op_qry,true);				
			
			$con_consult_qry = $select.$data_var['un_consent_forms'].','.$data_var['un_sx_consent_forms'].','.$data_var['un_consult_letters'].$from.'('.$qry_un_consent_forms.') AS un_consent_forms, ( '.$qry_un_surgery_consent_forms.') AS un_sx_consent_forms, ('.$qry_un_consult_letters.') AS un_consult_letters';
			$con_consult_arr = $this->create_array_from_qry($con_consult_qry,true);
			
			$orders_qry = $qry_orders_final;
			$orders_arr = $this->create_array_from_qry($orders_qry,true);
			$result_arr = array_merge($msg_pt_arr,$phy_scan_op_arr,$con_consult_arr,$orders_arr);
			$result_arr['tests_flag'] = 0;
			$ArrUnInterpreted = $this->ObjTests->un_interpreted_tests($this->callFrom);
			if(is_array($ArrUnInterpreted) && count($ArrUnInterpreted) > 0){
				$result_arr['tests_flag'] = 1;
			}
		}	
		
		if($this->operator_type==1 && $this->callFrom != 'core' && $get_erx_status==true)
		{
			/* ------------ Query for Erx essentials - erx url, username, password, facility etc -----------------------*/
			$qry_erx1 = 'SELECT copay_policies.EmdeonUrl, users.eRx_user_name, users.erx_password, users.eRx_facility_id, users.name 
						FROM (select EmdeonUrl from copay_policies where Allow_erx_medicare = "Yes" LIMIT 0,1) AS copay_policies, 
						(select eRx_user_name, erx_password, eRx_facility_id, concat(lname,",",fname) as name from users 
						where id = "'.$this->operator_id.'" LIMIT 0,1) AS users';
			
			$erx_info_arr = $this->create_array_from_qry($qry_erx1,true);
			$emdeonUrl = trim($erx_info_arr['EmdeonUrl']);
			$erx_user_name = trim($erx_info_arr['eRx_user_name']);
			$erx_password = trim($erx_info_arr['erx_password']);
			$erx_facility = trim($_SESSION['login_facility_erx_id']);//trim($erx_info_arr['eRx_facility_id']);
			$phyName = trim($erx_info_arr['name']);
			
			/* ------------ If the data exists for the particualar user, then sent the request at Emdeon for the user information. -----------------------*/
			
			if($emdeonUrl !='' && $erx_user_name !='' && $erx_password !='')
			{
				$url = $emdeonUrl.'/servlet/DxLogin?userid='.$erx_user_name.'&PW='.$erx_password.'&hdnBusiness='.$erx_facility.'&target=servlet/servlets.apiRxServlet&actionCommand=rxinboxext&apiLogin=true&textError=true';				
				$url = preg_replace('/ /','%20',$url);
				$cur = curl_init();
				curl_setopt($cur,CURLOPT_URL,$url);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
				curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
				
				curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
				$erx_data = curl_exec($cur);
				curl_close($cur);
	
				$erx_data = preg_replace('/<--BEGIN RX>/','',$erx_data);
				$erx_data = trim(preg_replace('/<--END RX>/','',$erx_data));
				$erx_data_arr  = explode("|^",$erx_data);	
				preg_match("/<-- ERROR :/",$erx_data_arr[0],$errorArr);
	
				if(count($errorArr) == 0){
					$erx_data = preg_replace('/<--BEGIN RX>/','',$erx_data);
					$erx_data = trim(preg_replace('/<--END RX>/','',$erx_data));
					$erx_data_arr  = explode("|^",$erx_data);
					
					for($i=0;$i<count($erx_data_arr);$i++){
						$inbox_data = $erx_data_arr[$i];
						$inbox_data_arr = explode('|',$inbox_data);
						if(strtoupper($inbox_data_arr[5]) == 'PENDING'){					
							if(!empty($inbox_data_arr[8])){
								$eRxflag = true;
								$result_arr['erx_flag'] = 1;
								break;
							}							
						}
					}
					
					//--- EMDEON LOGOUT CALL --------
					$cur = curl_init();				
					curl_setopt($cur,CURLOPT_URL,$url);
					curl_setopt ($cur, CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt ($cur, CURLOPT_SSL_VERIFYPEER, false); 
					curl_setopt($cur, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($cur, CURLOPT_COOKIEJAR, $cookie_file);
					curl_setopt($cur, CURLOPT_FOLLOWLOCATION, true); 
					$log_data = curl_exec($cur);
					curl_close($cur);
				}			
			}			
		}
		
		foreach($result_arr as $key=>$val){
			if($val==1){
				$result_arr['notifier_flag'] = 1;
				break;
			}
		}
		
		$arr_all_test = array("nfa", "vf", "vf_gl", "pachy", "ivfa", "disc", "oct", "oct_rnfl", "disc_external", "topography","test_gdx");
		
		return $result_arr;
	}
	
	function get_patient_messages_status()
	{
		$pt_unread_msg_not_arr = array();
		$req_sql = "SELECT COUNT(pt_msg_id) as unread_pt_messages FROM patient_messages WHERE is_read = 0 and del_status = 0 and is_done = 0 and communication_type = 2 AND sender_id!='0' LIMIT 1";
		$req_sql_obj = imw_query($req_sql);
		$req_arr = imw_fetch_assoc($req_sql_obj);
		$pt_unread_msg_not_arr["unread_pt_messages"] = $req_arr["unread_pt_messages"];
		
		if($pt_unread_msg_not_arr["unread_pt_messages"] == 0)
		{
			$req_sql = "SELECT COUNT(id) as unread_pt_notifications FROM iportal_req_changes WHERE is_approved = 0 and del_status = 0 LIMIT 1";
			$req_sql_obj = imw_query($req_sql);
			$req_arr = imw_fetch_assoc($req_sql_obj);
			$pt_unread_msg_not_arr["unread_pt_messages"] = $req_arr["unread_pt_notifications"];			
		}
		
		return $pt_unread_msg_not_arr;		
	}
	function get_direct_messages_status($chkUsrAcc = false)
	{
		$direct_unread_msg_not_arr = array();
		$pt_direct_credentials = $this->pt_direct_credentials($_SESSION['authId']);
		$req_sql = "SELECT COUNT(id) as direct_messages_count FROM direct_messages WHERE is_read = 0 and del_status = 0 and to_email = '".$pt_direct_credentials["email"]."' and folder_type = 1  LIMIT 1";
		$req_sql_obj = imw_query($req_sql);
		$req_arr = imw_fetch_assoc($req_sql_obj);
		$direct_unread_msg_not_arr["direct_messages_count"] = $req_arr["direct_messages_count"];	
		
		//to chk whether user has any other users assigned to it
		if($chkUsrAcc === true){
			$accessArr = $this->getDirectAllowedUsers($_SESSION['authId']);
			if(is_array($accessArr) && count($accessArr) > 0) $direct_unread_msg_not_arr["direct_messages_count"] = count($accessArr);
		}	

		return $direct_unread_msg_not_arr;
	}
	/*	to left side links of physician console popup	*/
	public function load_left_bar()
	{
		/*1	Physician: A&P Policies, e/Rx Inbox, Tests/Tasks, Messages/Reminders, Smart Phrases, Unfinalized Pt, Orders, Forms
		2/3	Technician: Tests/Tasks, Messages/Reminders, Smart Phrases, Unfinalized Pt, Orders, Forms, Patient Notify
		  	Others: Tests/Tasks, Messages/Reminders, Smart Phrases, Unfinalized Pt, Orders, Forms
		*/
		$left_bar_opts_arr  = array(
			0 => array('view_name'=>'SMART A&amp;P','id'=>'a_p_policies_opt'),
			1 => array('view_name'=>'e/Rx Inbox','id'=>'erx_inbox_opt'),
			2 => array('view_name'=>'Tasks/Tests','id'=>'test_tasks_opt'),
			3 => array('view_name'=>'Messages/Reminders','id'=>'message_reminders_opt'),
			4 => array('view_name'=>'Smart Phrases','id'=>'smart_phrases'),
			5 => array('view_name'=>'Unfinalized Patients','id'=>'unfinalized_patients_opt'),
			6 => array('view_name'=>'Orders/Order Set','id'=>'orders_set_opt'),
			7 => array('view_name'=>'Forms & Letters','id'=>'forms_letters_opt'),
			8 => array('view_name'=>'Patient Notify','id'=>'patient_notify_opt'),
			9 => array('view_name'=>'Responsible Person','id'=>'resp_person_opt'),
			10 => array('view_name'=>'Completed Tasks','id'=>'completed_tasks'),
			11 => array('view_name'=>'Patient Messages','id'=>'patient_messages'),
			12 => array('view_name'=>'WNL/Chart Template','id'=>'wnl_charttemplate'),
			13 => array('view_name'=>'Direct eMail','id'=>'direct_messages'),
            14 => array('view_name'=>'Tasks','id'=>'rule_tasks_opt')
		);
								// 1,5 customized for the operator type
		switch($this->operator_type)
		{
			case 1:
				$load_left_bar_opts = array(0,1,9,2,10,3,14,4,5,6,7,12);
			break;
			case 2:
			case 3:
			case 13:
				$load_left_bar_opts = array(1,2,9,10,3,14,6,7,8);  //array(0,9,2,10,3,6,7,8,12);
			break;
			
			default:
				$load_left_bar_opts = array(9,2,10,3,14,6,7);
			break;
		}
		
		$pt_coord_auth = $this->check_pt_coordinator_access($_SESSION['authId']);
		if($pt_coord_auth == 1)
		{
			$load_left_bar_opts[] = 11;
		}
		
		$pt_direct_credentials = $this->check_pt_direct_credentials($_SESSION['authId']);
		if($pt_direct_credentials == 1)
		{
			$load_left_bar_opts[] = 13;
		}
		
		//Chk for assigned allowed users
		$accessArr = $this->getDirectAllowedUsers($_SESSION['authId']);
		if(is_array($accessArr) && count($accessArr) > 0 && array_search(13, $load_left_bar_opts) == false){
			$load_left_bar_opts[] = 13;
		}
		
		$return_arr = array();
		foreach($load_left_bar_opts as $val)
		{
			$return_arr[] = $left_bar_opts_arr[$val];			
		}
		return $return_arr;
	}
	
	public function check_pt_coordinator_access($user_id)
	{
		$pt_coord_qry = "SELECT c1.access_pri, c1.pt_coordinator, c1.groups_prevlgs_id, c2.prevlgs FROM users c1 
					LEFT JOIN groups_prevlgs c2 ON c1.groups_prevlgs_id=c2.id AND c2.deleted_by='0'
					WHERE c1.id = '".$user_id."'";
		$result_arr = $this->create_array_from_qry($pt_coord_qry,true);
		if(!empty($result_arr['prevlgs'])){
			$access_pri_arr=unserialize(html_entity_decode($result_arr['prevlgs']));
			return $access_pri_arr['priv_pt_coordinate'];
		}else{
			return $result_arr['pt_coordinator'];
		}
	}
	public function check_pt_direct_credentials($user_id)
	{
		$returnVal = 0;

		$result_arr = $this->pt_direct_credentials($user_id);
		if(trim($result_arr["email"]) != "" && trim($result_arr["email_password"]) != "")
		{
			$returnVal = 1;
		}

		return $returnVal;
	}
	
	public function get_ap_policies()
	{		
		$flg_dis_user_ap = 1;
		$res_fellow_sess = trim($_SESSION['res_fellow_sess']);
		if(!empty($res_fellow_sess) && isset($res_fellow_sess))
		{
			$target_prov = $res_fellow_sess;
			if($_SESSION['logged_user_type'] == "11"){ $flg_dis_user_ap = 0; }	
		}
		else
		{
			$target_prov = $this->operator_id;
		}

		//Resident
		if(!empty($flg_dis_user_ap)){
			$followedbyresi = ($_SESSION['logged_user_type'] == "11") ? $this->operator_id : 0;
			$str_pro_phrase = !empty($followedbyresi) ? " || providerID='".$followedbyresi."' " : "";
		}else{ $followedbyresi = 0; $str_pro_phrase = "";  }

		$target_qry = "SELECT tb.to_do_id, tb.task,if(tb.dxcode !='',concat(tb.assessment,' - ',tb.dxcode),tb.assessment) as assessment_dx,tb.plan,
					tb.providerID,dynamic_ap,
					group_concat(distinct(os.orderset_name) SEPARATOR ', ') as order_set_name,
					group_concat(distinct(od.name) SEPARATOR ', ') as order_name,
					tb.strCptCd, tb.dxcode, tb.assessment, tb.specialityId, tb.xmlFU, tb.order_id ,
					group_concat(od.order_type_id SEPARATOR ', ') as order_type_id,
					group_concat(od.o_type SEPARATOR ', ') as o_type,
					group_concat(distinct(tb.order_id) SEPARATOR ', ') as order_id_new,
					tb.order_set_name as order_set_ids,
					tb.dxcode_10,tb.severity,tb.location, tb.site_type, tb.date_time3
					FROM console_to_do tb
					LEFT JOIN order_sets os ON FIND_IN_SET(os.id, tb.order_set_name)>0
					LEFT JOIN order_details od ON FIND_IN_SET(od.id, tb.order_id)>0
					WHERE (tb.providerID='".$target_prov."' || tb.providerID='0' ".$str_pro_phrase.")
						AND (dynamic_ap=0 || dynamic_ap=1)
					group by tb.to_do_id 
					ORDER BY tb.task,tb.assessment
					";
		$r = imw_query($target_qry);
		$rs_set_provider = $rs_set_community = $rs_set_dynamic = $fu_arr=array();
		$dyn_c=0;$dyn_lm=50;
		if($r && imw_num_rows($r)>0){
			while($rs = imw_fetch_assoc($r)){
				//-----BEGIN SET ALL OPTIONS FOR ORDERS----------
				$arrOrderTypeId = explode(",",$rs['order_type_id']);
				$arrOrderType = explode(",",$rs['o_type']);
				$count = (count($arrOrderTypeId)>count($arrOrderType))?count($arrOrderTypeId):count($arrOrderType);
				
				$arrOrderId = explode(",",$rs['order_id_new']);
				$arrOrderName = explode(",",$rs['order_name']);
				
				$arrOrderSetId = explode(",",$rs['order_set_ids']);
				$arrOrderSetName = explode(",",$rs['order_set_name']);
				
				if(count($arrOrderId)>0){
					foreach($arrOrderId as $koi => $vOrdrid){
						if(!empty($vOrdrid)){
							$sql = "SELECT id,order_type_id,o_type,name FROM order_details WHERE id IN (".$vOrdrid.")";
							$res = imw_query($sql);
							$arr = array();
							while($row = imw_fetch_assoc($res)){
								$tmp_ordr_nm="";
								if(!empty($row['order_type_id'])){
									$tmp_ordr_nm = $this->common_order_type[$row['order_type_id']];
								}
								if(empty($tmp_ordr_nm)){									
									$tmp_ordr_nm = $row['o_type'];
								}
								
								$order_type_name = trim($tmp_ordr_nm);
								if(!empty($order_type_name) && in_array($order_type_name,$this->common_order_type)){
									//$arr[$order_type_name][] = $row['id'];
									$rs['ele_order_'.$order_type_name."[]"][$row['id']] = $row['name'];
								}
							}
						}
					}
				}
				//*/
				if($rs['order_set_ids']!="" && $rs['order_set_ids']!="NULL"){
					$arr_order_set_ids = explode(",", $rs['order_set_ids']);
					$arr_order_set_name = explode(",", $rs['order_set_name']);
					foreach($arr_order_set_ids as $key=>$val){
						$rs['ele_order_orderset[]'][$val] = $arr_order_set_name[$key];
					}
				}
				
				if(empty($flg_dis_user_ap)){ $rs['no_delete']="1"; }
				
				$t_dxc10 = $rs['dxcode_10'];
				$ar_dxc10 = explode('@@@', $t_dxc10);
				$rs['dxcode_10'] = trim($ar_dxc10[0]);
				$rs['dxcode_10_id'] = (isset($ar_dxc10[1]) && !empty($ar_dxc10[1])) ? trim($ar_dxc10[1]) : "" ;
				
				$tasmt = trim($rs['assessment_dx']);
				if($rs['providerID'] != 0 && $rs['dynamic_ap'] == 0){
					 
					$rs_set_provider[] = $rs;
					
				}
				if($rs['providerID'] == 0 && $rs['dynamic_ap'] == 0){
					$rs_set_community[] = $rs;
				}
				
				if($rs['providerID'] != 0 && $rs['dynamic_ap'] == 1 && !empty($tasmt)){ //assessment_dx				
				if(strtotime($rs['date_time3']) > strtotime('-30 days')) {
				if($dyn_c<=$dyn_lm){	//limit for only 50:: otherwise it will not load. 
				$rs_set_dynamic[] = $rs; $dyn_c+=1;
				}
				}
				}
				
			}
		}	
		$provider_sp_arr = 	$rs_set_provider;	
		$community_arr = 	$rs_set_community;
		$dynamic_arr = 	$rs_set_dynamic;
		
		$final_arr = array($provider_sp_arr,$community_arr, $dynamic_arr);
		
		return $final_arr;	
	}
	
	public function get_username_by_id($id_arr = array())
	{
		$filterById = '';
		$result_arr = array();		
		if(count($id_arr)>0)
		{
			$id_arr_str = implode(',',$id_arr);
			$filterById = ' and id IN ('.$id_arr_str.')';
		}
		
		$reqQry = 'select id, concat(SUBSTRING(fname,1,1),SUBSTRING(lname,1,1)) as short_name, concat(lname,", ",fname) as medium_name, concat(lname,", ",fname," ",mname) as full_name from users where id > 0 '.$filterById.' and delete_status = 0 order by lname,fname';		
		$resultOb = imw_query($reqQry);
		while($result_row = imw_fetch_assoc($resultOb))
		{
			$result_arr[$result_row['id']]['short'] = $result_row['short_name'];
			$result_arr[$result_row['id']]['medium'] = $result_row['medium_name'];
			$result_arr[$result_row['id']]['full'] = $result_row['full_name'];
		}
		return $result_arr;
	}
	
	/*-------------------- This function is used to get the responsible persons for sent messages, orders/Order Sets, physician to do task -----------------*/
	public function get_responsible_person($page_get,$per_page)
	{
		$req_qry = "select phy_todo_task_id as msg_id, task_subject as msg_subject, task_text as msg_body, concat(patient_name, if(TRIM(patientId)='','',concat(' - ',patientId))) as patient_name, 
					task_status, sent_to_groups as user_name,
				 	date_format(task_created_date,'".get_sql_date_format('','y')."') as msg_date, 'phy_todo_task' as tb_name 
					from phy_todo_task where task_by = '".$this->operator_id."' and task_to != '".$this->operator_id."'
					and task_status = '0' and del_status = '0' group by sent_to_groups order by task_created_date desc";
		$phy_to_do_arr = $this->create_array_from_qry($req_qry);

		$req_qry = "select GROUP_CONCAT(user_messages.user_message_id) as msg_id, user_messages.message_text as msg_body, 
					date_format(user_messages.message_send_date,'".get_sql_date_format('','y')."') as msg_date, user_messages.sent_to_groups as user_name,
					user_messages.message_subject as msg_subject, 'user_messages' as tb_name,
					concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname,if(TRIM(user_messages.patientId)='','',concat(' - ',user_messages.patientId))) as patient_name
					from user_messages left join patient_data on patient_data.id = user_messages.patientId
					where user_messages.message_sender_id = '".$this->operator_id."'
					and user_messages.message_to != '".$this->operator_id."'
					and user_messages.message_status = '0' 
					and user_messages.resp_delete = '0' 
					group by user_messages.sent_to_groups, user_messages.message_subject, DATE_FORMAT(user_messages.message_send_date,'%Y-%m-%d') 
					order by user_messages.message_send_date desc";
		$user_messages_arr = $this->create_array_from_qry($req_qry);
		
		$req_qry = "select distinct(order_set_associate_chart_notes.order_set_id), 'order_set_associate_chart_notes' as tb_name,
					order_set_associate_chart_notes.order_set_associate_id,patient_data.id as patient_id,
					concat(patient_data.lname,', ',patient_data.fname,' ',patient_data.mname) as patient_name,
					date_format(order_set_associate_chart_notes.created_date,'".get_sql_date_format('','y')."') as created_date
					from order_set_associate_chart_notes join patient_data on 
					patient_data.id = order_set_associate_chart_notes.patient_id
					where logged_provider_id='".$this->operator_id."' 
					and delete_status='0'";
		
		$orders_arr = $this->create_array_from_qry($req_qry);
		
		$result_arr = array_merge($phy_to_do_arr,$user_messages_arr);
        include ('paging.inc.php');
        $per_page_records="20";
		if($per_page!='' && (int)$per_page>0){$per_page_records=$per_page;}
		$page = (!isset($page_get) || $page_get=="")?1:$page_get;
		$objPaging = new Paging($per_page_records,$page);
		$objPaging->sort_by = $per_page;
		//$objPaging->sort_order = $case_filter;
        $objPaging->totalRecords = count($result_arr);
		$objPaging->func_name = "load_responsible_person";
        $objPaging->data = $result_arr;
		$result_arr1 = $objPaging->fetchLimitedRecords();
		$p_link1=$objPaging->getPagingString($page);
		$p_link2=$objPaging->buildComponentR8($page);

        return array("0"=>$result_arr1,"1"=>$p_link1,"2"=>$p_link2);
	}
	
	public function get_users()
	{
		$reqQry = 'select id, concat(lname,", ",fname," ",mname) as patient_name from users where id > 0 and delete_status = 0 order by lname,fname';
		$result_arr = $this->create_array_from_qry($reqQry);
		return $result_arr;	
	}
	
	function getProvGroupOpts($id="", $grp="", $ta=""){
		if(!empty($id) || !empty($grp)){
			$phrs="";
			if(!empty($id)){ $phrs .= " id='".$id."' "; }			
			if(!empty($grp)){ if(!empty($phrs)){ $phrs .=" AND ";  }   $phrs .= " group_name='".imw_real_escape_string($grp)."' ";  }
			
			$arr=array();
			$sql = "SELECT phy FROM prov_group where ".$phrs;
			$result1 = imw_query($sql);
			if($result1 && imw_num_rows($result1)>0){
				while($rs1 = imw_fetch_array($result1)){
				//	if(!in_array($rs1['id'], $arrayChkUser)){
						$ar_phy = explode(",", $rs1['phy']);
						if(count($ar_phy)>0){
							$arr=array_merge($arr, $ar_phy);
						}
				//	}
				}
			}
			return $arr;
		}else{
			$groupsOption_arr=array();
			$groupsOption="";
			$qry = "SELECT concat(group_name,'--',id) AS grp, group_name FROM `prov_group` ORDER BY group_name ";
			$res = imw_query($qry);		
			if(imw_num_rows($res)>0){
				while($rs_order_set=imw_fetch_assoc($res)){
					if($ta=="1"){
						$groupsOption_arr[$rs_order_set['grp']] = $rs_order_set['group_name'].' (Group)'; 
					}else{
					$groupsOption .= '
						    <option value="'.$rs_order_set['grp'].'">'.$rs_order_set['group_name'].'</option>
						';
					}	
				}
				
				if($ta=="1"){
					$groupsOption="'".implode("','",$groupsOption_arr)."',";
				}
			}
			return $groupsOption;
		}
	}
	public function get_pt_appt($pt_id){
		$qry = "SELECT sa.sa_patient_id, facility.name as facility_name, sp.acronym as acronym, concat(SUBSTRING(users.fname,1,1),SUBSTRING(users.lname,1,1)) as phy_init_name, concat(users.lname,', ',users.fname,' ',users.mname) as phy_name, CONCAT(date_format(sa.sa_app_start_date,'".get_sql_date_format()."'),' ',date_format(sa.sa_app_starttime,'%h:%i %p')) as appt_dt_time
				FROM schedule_appointments sa JOIN slot_procedures sp ON (sp.id=sa.procedureid) INNER JOIN users ON sa.sa_doctor_id = users.id INNER JOIN facility ON facility.id = sa.sa_facility_id 
				WHERE sa.sa_patient_id = '".$pt_id."' AND sa.sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				AND CONCAT(sa.sa_app_start_date, ' ', sa.sa_app_starttime) > '".date('Y-m-d H:i:s')."' ORDER BY sa.sa_app_start_date, sa.sa_app_starttime LIMIT 0,1";
		$result_arr = $this->create_array_from_qry($qry,true);		
		return $result_arr;		
	}
	
	
	/*-------------------- This function is used to get the smart phrases for the logged provider ---------------------*/
	public function get_smart_phrases($edit_id='')
	{
		$filter_by_id = '';
		if(trim($edit_id)!="" && isset($edit_id))
		{
			$filter_by_id = ' and phrase_id  = '.trim($edit_id);
		}
		$req_qry = "Select phrase_id, phrase, providerID, DATE_FORMAT(date_time,'".get_sql_date_format()."') AS date_time, exam from common_phrases where providerID='".$this->operator_id."'".$filter_by_id." order by phrase";
		$result_arr = $this->create_array_from_qry($req_qry);
		return $result_arr; 	
	}
	
	/*------------------- This function is used to get the unfinalized patients according to provider auth -------------------*/
	public function get_unfinalized_patients($sall=0)
	{		
		$sub_qry = "patient_data.fname AS patient_fname, ".
					"patient_data.mname AS patient_mname, ".
					"patient_data.lname AS patient_lname, ".
					"users.fname AS provider_fname, ".
					"users.mname AS provider_mname, ".
					"users.lname AS provider_lname, ".
					//"facility.name AS facility_name, ".
					"chart_master_table.date_of_service AS date_of_service, ".
					"chart_master_table.providerId AS doctorId, ".
					//"chart_assessment_plans.doctorId, ".
					"chart_master_table.* ";
			
		$pv1 = array();
		$pv2 = array();

		$req_qry =  "SELECT ".
							$sub_qry.
							"FROM chart_master_table ".
							//"LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
							//"LEFT JOIN chart_assessment_plans ON chart_assessment_plans.form_id = chart_master_table.id ".
							"LEFT JOIN patient_data ON patient_data.id = chart_master_table.patient_id ".
							//"LEFT JOIN users ON users.id = chart_assessment_plans.doctorId ".
							"LEFT JOIN users ON users.id = chart_master_table.providerId ".
							//"LEFT JOIN facility ON facility.fac_prac_code = patient_data.default_facility and facility.fac_prac_code!=0 ".
							"WHERE chart_master_table.finalize = '0' 
							and chart_master_table.delete_status = '0' and patient_data.id != 0 and chart_master_table.not2show = '0' ".
							"ORDER BY patient_lname,patient_fname";
		if(!empty($sall)){
		$pv1 = $this->create_array_from_qry($req_qry);
		}	

		$req_qry = "SELECT ".
						   $sub_qry.
						   "FROM chart_master_table ".//"FROM users ".
						   "USE INDEX (finalize) ".
						  // "LEFT JOIN chart_left_cc_history ON chart_left_cc_history.form_id = chart_master_table.id ".
						  // "LEFT JOIN chart_assessment_plans ON chart_assessment_plans.form_id = chart_master_table.id ".
						   "LEFT JOIN patient_data ON chart_master_table.patient_id = patient_data.id ".
						   //"LEFT JOIN users ON users.id = chart_assessment_plans.doctorId ". 
						   "LEFT JOIN users ON users.id = chart_master_table.providerId ". 
						   //"LEFT JOIN facility ON facility.fac_prac_code = patient_data.default_facility and facility.fac_prac_code!=0 ".
						   "WHERE chart_master_table.finalize = '0' 
						   and chart_master_table.delete_status = '0' and patient_data.id != 0 and chart_master_table.not2show = '0' ". 
						   
						   //"AND (chart_assessment_plans.doctorId = '".$this->operator_id."' OR chart_master_table.providerId = '".$this->operator_id."') ". //modified on 07-06-2012: to optimise query for slowness
							"AND (chart_master_table.providerId = '".$this->operator_id."') ".		
						   "ORDER BY patient_lname,patient_fname";
		if(empty($sall)){
		$pv2 = $this->create_array_from_qry($req_qry);
		}	

		$result_arr = array('pv1' => $pv1,'pv2' => $pv2);
		return $result_arr;
	}
	
	
	/* This function is used to get the forms and letters records */
	public function get_forms_letters($action='')
	{	
		$action = trim($action);	
		switch($action)
		{
			case 'consent_forms':
				$req_qry = "SELECT chs.id as chsid, chs.consent_id as chscon_id, chs.physician_id as chsphy_id,DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p')  as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,
							pcfi.consent_form_name as form_name, pcfi.consent_form_id as pcficonsent_id, pcfi.form_information_id as pcfiform_information_id,  
							'consent_form' as form_type, pcfi.chart_procedure_id
							FROM consent_hold_sign chs 
							JOIN patient_consent_form_information pcfi ON (chs.consent_id = pcfi.form_information_id AND chs.consent_id != '0') 
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND chs.del_status ='0' AND physician_id='".$this->operator_id."'";
				$result_arr = $this->create_array_from_qry($req_qry);				
				return $result_arr;
			break;
			case 'sx_consent_forms':
				$req_qry = "SELECT chs.id as chsid, chs.sx_consent_id as chssxcon_id, chs.physician_id as chsphy_id, DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p') as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name, 
							scff.surgery_consent_name as form_name, scff.surgery_consent_id as sxconsent_id, scff.consent_template_id as form_info_id,
							'sx_consent_form' as form_type 
							FROM consent_hold_sign chs 
							JOIN surgery_consent_filled_form scff ON (chs.sx_consent_id = scff.surgery_consent_id AND chs.sx_consent_id != '0') 
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND physician_id='".$this->operator_id."'";
				$result_arr = $this->create_array_from_qry($req_qry);			
				return $result_arr;				
			break;
			case 'op_notes':
				$req_qry = "SELECT chs.id as chsid, chs.opnote_id as chsop_id, chs.physician_id as chsphy_id, 
							DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p')  as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name, 
							pnr.patient_id as pnr_patient_id, pnr.tempId as pnr_template_id, pnr.pn_rep_id as pnr_id, 
							pnt.temp_name as form_name, pnt.temp_id as opnote_id, 'opnotes' as form_type, pnr.chart_procedure_id 
							FROM consent_hold_sign chs 
							JOIN pn_reports pnr ON (pnr.pn_rep_id = chs.opnote_id) 
							JOIN pn_template pnt ON (pnr.tempId = pnt.temp_id)
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND chs.del_status ='0' AND chs.physician_id='".$this->operator_id."'";	
				$result_arr = $this->create_array_from_qry($req_qry);			
				return $result_arr;				
			break;
			case 'consult_letters':		
				$req_qry = "SELECT chs.id as chsid, chs.consult_id as chsconsult_id, chs.physician_id as chsphy_id, 
							DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p')  as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name, 
							pclt.patient_id as pclt_patient_id, pclt.templateId as pclt_template_id, pclt.patient_consult_id as pclt_id, 
							pclt.templateName as form_name, 'consult_letters' as form_type 
							FROM consent_hold_sign chs 
							JOIN patient_consult_letter_tbl pclt ON (pclt.patient_consult_id = chs.consult_id) 
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND chs.physician_id='".$this->operator_id."'";
				$result_arr = $this->create_array_from_qry($req_qry);	
				return $result_arr;
			break;
			default:
				$req_qry = "SELECT chs.id as chsid, chs.consent_id as chscon_id, chs.physician_id as chsphy_id,DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p')  as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name,
							pcfi.consent_form_name as form_name, pcfi.consent_form_id as pcficonsent_id, pcfi.form_information_id as pcfiform_information_id,  
							'consent_form' as form_type
							FROM consent_hold_sign chs 
							JOIN patient_consent_form_information pcfi ON (chs.consent_id = pcfi.form_information_id AND chs.consent_id != '0') 
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND physician_id='".$this->operator_id."'";
				$consent_frm_arr = $this->create_array_from_qry($req_qry);					
				
		
				$req_qry = "SELECT chs.id as chsid, chs.sx_consent_id as chssxcon_id, chs.physician_id as chsphy_id, DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p') as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name, 
							scff.surgery_consent_name as form_name, scff.surgery_consent_id as sxconsent_id, scff.consent_template_id as form_info_id,
							'sx_consent_form' as form_type 
							FROM consent_hold_sign chs 
							JOIN surgery_consent_filled_form scff ON (chs.sx_consent_id = scff.surgery_consent_id AND chs.sx_consent_id != '0') 
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND physician_id='".$this->operator_id."'";
				$sx_consent_frm_arr = $this->create_array_from_qry($req_qry);												
		
				$req_qry = "SELECT chs.id as chsid, chs.opnote_id as chsop_id, chs.physician_id as chsphy_id, 
							DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p')  as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name, 
							pnr.patient_id as pnr_patient_id, pnr.tempId as pnr_template_id, pnr.pn_rep_id as pnr_id, 
							pnt.temp_name as form_name, pnt.temp_id as opnote_id, 'opnotes' as form_type 
							FROM consent_hold_sign chs 
							JOIN pn_reports pnr ON (pnr.pn_rep_id = chs.opnote_id) 
							JOIN pn_template pnt ON (pnr.tempId = pnt.temp_id)
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND chs.physician_id='".$this->operator_id."'";	
				$opnotes_frm_arr = $this->create_array_from_qry($req_qry);					
		
				$req_qry = "SELECT chs.id as chsid, chs.consult_id as chsconsult_id, chs.physician_id as chsphy_id, 
							DATE_FORMAT(chs.created_on,'".get_sql_date_format()." %h:%i %p')  as chscre_on, chs.created_by as chscre_by, 
							pd.id as pdid, concat(pd.lname,', ',pd.fname,' ',pd.mname,' - ',pd.id) as patient_name, 
							pclt.patient_id as pclt_patient_id, pclt.templateId as pclt_template_id, pclt.patient_consult_id as pclt_id, 
							pclt.templateName as form_name, 'consult_letters' as form_type 
							FROM consent_hold_sign chs 
							JOIN patient_consult_letter_tbl pclt ON (pclt.patient_consult_id = chs.consult_id) 
							JOIN patient_data pd ON (pd.id = chs.patient_id) 
							WHERE chs.signed = '0' AND chs.physician_id='".$this->operator_id."'";
				$consult_letters_arr = $this->create_array_from_qry($req_qry);			
				
				$result_arr = array_merge($consent_frm_arr,$sx_consent_frm_arr,$opnotes_frm_arr,$consult_letters_arr);		
				return $result_arr;
			break;			
		}
	}
	
	public function get_sign_status()
	{
		$check_sign_qry = "SELECT sign FROM users WHERE id='".$this->operator_id."'";
		$check_sign_result_arr = $this->create_array_from_qry($check_sign_qry,true);
		$sign_val = $check_sign_result_arr['sign'];
		$yetToSignInAdmin= false;
		if(!$sign_val || $sign_val == "0-0-0:;") {
			$yetToSignInAdmin = true;	
		}
		return $yetToSignInAdmin;	
	}
	
	/*--------------------- This function is used to get the uncompleted/in progress orders ---------------------------*/
	public function get_orders_orderSets()
	{
		/*--------------------- Getting user data in $user_arr --------------------------*/
		$user_qry = "select id,lname,fname,mname from users";
		$user_arr = array();
		$result_object = imw_query($user_qry) or die('Query not worked');
		while($result_row = imw_fetch_assoc($result_object))
		{
			$user_arr[$result_row['id']] = $result_row['lname'].', '.$result_row['fname'].' '.$result_row['mname'];  	
		}
		
		/*-------------------------------- Getting Order set data in $order_set_arr -------------------------*/
		$order_set_qry = "select * from order_sets order by createdy_on desc";
		$order_set_arr = array();
		$result_object = imw_query($order_set_qry) or die('Query not worked');
		while($result_row = imw_fetch_assoc($result_object))
		{
			$order_set_arr[$result_row['id']] = $result_row;	
		}
		
		/*----------------------------- Getting Order details data in $orders_details_arr --------------------*/
		$orders_qry = "select * from order_details order by created_on desc";
		$orders_detail_arr = array();
		$result_object = imw_query($orders_qry) or die('Query not worked');
		while($result_row = imw_fetch_assoc($result_object))
		{
			$orders_detail_arr[$result_row['id']] = $result_row;
		}
		
		/*--------------------------- Get orders id on which behalf the data comes of order chart notes --------------------*/				
		$sub_qry = "select id from order_details where delete_status = '0' and 
				(resp_person = '".$this->operator_id."' or resp_person like '".$this->operator_id.",%'
				or resp_person like '%,".$this->operator_id."' or resp_person like '%,".$this->operator_id.",%')";
		if(empty($this->operator_group_id) == false){
			$sub_qry .= " or (resp_group = '".$this->operator_group_id."' or resp_group like '%,".$this->operator_group_id.",%'
					or resp_group like '%,".$this->operator_group_id."' or resp_group like '".$this->operator_group_id.",%')";
		}			
		
		$req_qry = "select order_set_associate_chart_notes.order_set_associate_id as primary_set_id,
					order_set_associate_chart_notes.order_set_id,
					order_set_associate_chart_notes.patient_id ,
					order_set_associate_chart_notes.logged_provider_id ,
					order_set_associate_chart_notes.order_set_options,
					date_format(order_set_associate_chart_notes.created_date,'".get_sql_date_format('','y')."') as c_date,
					order_set_associate_chart_notes.logged_provider_id ,
					order_set_associate_chart_notes.delete_status as set_delete_status,
					order_set_associate_chart_notes.order_set_reason_text ,
					order_set_associate_chart_notes_details.*,
					patient_data.lname,patient_data.fname,patient_data.mname
					from order_set_associate_chart_notes left join
					order_set_associate_chart_notes_details on
					order_set_associate_chart_notes.order_set_associate_id = 
					order_set_associate_chart_notes_details.order_set_associate_id
					join patient_data on patient_data.id = 
					order_set_associate_chart_notes.patient_id
					where order_set_associate_chart_notes_details.orders_status != '2'
					and order_set_associate_chart_notes_details.delete_status = '0'
					and order_set_associate_chart_notes_details.order_id in (".$sub_qry.")
					order by order_set_associate_chart_notes.created_date desc";
					
		$orders_result_arr = $this->create_array_from_qry($req_qry);
		$final_arr = array('users_arr' => $user_arr, 'order_set_arr' => $order_set_arr, 'orders_detail_arr' => $orders_detail_arr,'result_arr' => $orders_result_arr);					
		return $final_arr;
	}
	
	//Returns Patient suggestions array
	public function get_patient_suggestions($xml_obj,$direct_msg_id=false,$real_ccda_file_path=false){
		$sql_arr = array('fname' => $xml_obj['fname'], 'lname' => $xml_obj['lname'], 'mname' => $xml_obj['mname'], 'sex' => $xml_obj['sex'], 'DOB' => $xml_obj['dob'], 'postal_code' => $xml_obj['zip']);
		$sql_fields = implode(',',array_keys($sql_arr));
		$tmp_arr = array();
		$qry_arr = $return_arr = array();
		
		$counter = 0;
		$qry_str = 'SELECT id,'.$sql_fields.' FROM patient_data WHERE ';
		foreach($sql_arr as $key => $val){
			if($key=='mname' || empty($val)) continue;
			if($counter > 0){ 								// If array has more than one item 
				$qry_str_fields .= ' AND ';
			}
			
			$qry_str_fields .= "".$key." = '".$val."'";
			if($counter > 1){								// To keep first three elements of array and above
				$qry_arr[] = $qry_str.$qry_str_fields;
			}
			
			if(count($sql_arr) > 1){						//Remove last elements of array untill its count is > 3
				array_pop($sql_arr);
			}
			$counter++;
		}
		
		krsort($qry_arr);
		$loop_iterate = true;			// Below loop will only work untill this var is true
		foreach($qry_arr as $key => $val){
			if($loop_iterate == true){
				if(empty($val) == false){
					$qry = imw_query($val);
					if(imw_num_rows($qry) > 0){
						$loop_iterate = false;
						while($row = imw_fetch_assoc($qry)){
							$tmp_arr[] = $row;
						}
					}
				}
			}
		}
		if(count($tmp_arr)==1){//Mark this attachment for current patient if only one patient found in match.
			$CURR_PATIENT_ID = $tmp_arr[0]['id'];
			/****GETTING ONE NEXT FUTURE APPOINTMENT***/
			$next_appt_id = $this->get_next_appt_id($CURR_PATIENT_ID);
			imw_query("UPDATE direct_messages_attachment SET patient_id = '".$CURR_PATIENT_ID."' WHERE direct_message_id='".$direct_msg_id."' AND complete_path LIKE '".$real_ccda_file_path."'");
			imw_query("UPDATE direct_messages_attachment SET sch_id = '".$next_appt_id."' WHERE direct_message_id='".$direct_msg_id."' AND complete_path LIKE '".$real_ccda_file_path."' AND (sch_id='' OR sch_id='0')");
			
		}
		$return_arr['pt_details'] = $tmp_arr;
		$return_arr['common_name'] = $xml_obj['lname'].', '.$xml_obj['fname'].' '.$xml_obj['mname'];
		return $return_arr;
	}
	
	function get_next_appt_id($pt_id){
		$q = "SELECT id FROM schedule_appointments 
				WHERE sa_patient_id = '".$pt_id."' 
					AND sa_app_start_date >= '".date('Y-m-d')."' 
					AND sa_patient_app_status_id NOT IN(203,201,18,19,20,3) 
				LIMIT 0,1";
		$res = imw_query($q);
		if($res && imw_num_rows($res)==1){
			$rs = imw_fetch_assoc($res);
			return $rs['id'];
		}
		return false;
	}
	
	public function create_ccda_patient($zip_name = '',$file_name = '',$dir_root_path = ''){
		if(empty($zip_name) && empty($file_name)) return ;
		$field_mapped_arr = array(
			'First Name' => 'fname',
			'Middle Name' => 'mname',
			'Birth Name' => 'mname_br',
			'Last Name' => 'lname',
			'Date of Birth' => 'DOB',
			'Gender' => 'sex',
			'Postal Code (Zip Code)' => 'postal_code',
			'Street 1' => 'street',
			'Street 2' => 'street2',
			'City' => 'city',
			'State' => 'state',
			'Country' => 'country_code',
			'Home Phone' => 'phone_home',
			'Work Phone' => 'phone_biz',
			'Mobile Contact' => 'phone_cell',
			'Language'=>'language',
			'Language Code'=>'lang_code',
			'Ethnicity' => 'ethnicity',
			'Race' => 'race',
			'Race Code' => 'race_code'
		);
		if(empty($zip_name) == false){
			$file_info = pathinfo($zip_name);
		}else{
			$file_info = pathinfo($file_name);
		}
		
		$doc_path = $this->doc_dir_path.$file_info['dirname'].'/';
		$web_doc_path = str_replace($GLOBALS['fileroot'],$GLOBALS['webroot'],$doc_path);
		if(file_exists($doc_path.$file_info['basename'])){
			$content = '';
			$ccda_file = $doc_path.$file_info['basename'];
			if(empty($zip_name) == false){
				$zip = new ZipArchive;
				if($zip->open($ccda_file) == TRUE){
					for($i=0; $i<$zip->numFiles; $i++){
						$name = $zip->getNameIndex($i);
						if(strpos(strtolower($name),".xml") !== false || strpos(strtolower($name),".txt") !== false){
							if($file_name == $name){
								$move_status = $zip->extractTo($doc_path, $name);
								if(!$move_status){
									$return_arr['error'] = 'Problem occured. Please try again';
								}
							}
						}
					}
				}	
			}
			
			$file_pointer = $doc_path.$file_name;
			if(empty($zip_name)){$file_pointer = $doc_path.$file_info['basename'];}

			$objCDA = new CDAXMLParser($file_pointer);
			$patientData = $objCDA->arrPatientData;
			if(count($patientData) > 0){
				$pt_details_arr = array();
				
				if(isset($patientData['raceExtension'])) $patientData['race'] = $patientData['raceExtension'];
				
				$tmp_arr['Basic'] = array(
					'First Name' => (empty($patientData['fname']) == false) ? $patientData['fname'] : '',
					'Middle Name' => (empty($patientData['mname']) == false) ? $patientData['mname'] : '',
					'Last Name' => (empty($patientData['lname']) == false) ? $patientData['lname'] : '',
					'Birth Name' => (empty($patientData['mname_br']) == false) ? $patientData['mname_br'] : '',
					'Date of Birth' => (empty($patientData['dob']) == false) ? get_date_format($patientData['dob']) : '' ,
					'Gender' => (empty($patientData['gender']) == false) ? ucfirst($patientData['gender']) : ''  ,
					'Postal Code (Zip Code)' => (empty($patientData['zip']) == false) ? $patientData['zip'] : '',
					'Language' => (empty($patientData['language']) == false) ? $patientData['language'] : '',
					'Language Code' => (empty($patientData['lang_code']) == false) ? $patientData['lang_code'] : '',
					'Race' => (empty($patientData['race']) == false) ? $patientData['race'] : '',
					'Race Code' => (empty($patientData['raceExtension_code']) == false) ? $patientData['raceExtension_code'] : '',
					'Ethnicity' => (empty($patientData['ethnicity']) == false) ? $patientData['ethnicity'] : ''
				);
				
				$tmp_arr['Address'] = array(
					'Street 1' => (empty($patientData['street_1']) == false) ? $patientData['street_1'] : '',
					'Street 2' => (empty($patientData['street_2']) == false) ? $patientData['street_2'] : '',
					'City' => (empty($patientData['city']) == false) ? $patientData['city'] : '',
					'State' => (empty($patientData['state']) == false) ? $patientData['state'] : '',
					'Country' => (empty($patientData['country']) == false) ? $patientData['country'] : ''
				);
				
				$tmp_arr['Contact'] = array(
					'Home Phone' => (empty($patientData['home_phone']) == false) ? $patientData['home_phone'] : '',
					'Work Phone' => (empty($patientData['work_phone']) == false) ? $patientData['work_phone'] : '',
					'Mobile Contact' => (empty($patientData['mobile_contact']) == false) ? $patientData['mobile_contact'] : ''
				);
				$return_arr['FieldsMappedArr'] = $field_mapped_arr;
				$return_arr['PatientData'] = $tmp_arr;
			}
		}else{
			$return_arr['error'] = 'Provided file does not exists on the server !';
		}
		return $return_arr;
	}
	
	function check_patient_details($xml_file,$xml_file_path=''){
		$arr_pt_details_return=array();
		$arr_xml_file_content=array();
		
		if($xml_file_path!=''){
			$objCDA = new CDAXMLParser($xml_file_path);
			$patientData = $objCDA->arrPatientData;
			//pre($patientData);
			if($patientData){
				$arr_pt_details_return['fname'] = $patientData['fname'];
				$arr_pt_details_return['mname'] = isset($patientData['mname']) ? $patientData['mname'] : '';
				$arr_pt_details_return['lname'] = $patientData['lname'];
				$arr_pt_details_return['sex']   = ucfirst($patientData['gender']);
				$arr_pt_details_return['dob']   = $patientData['dob'];				
				$arr_pt_details_return['city']  = $patientData['city'];
				$arr_pt_details_return['state'] = $patientData['state'];
				$arr_pt_details_return['zip']   = $patientData['zip'];
				//$arr_pt_details_return['language'] = code_to_language((string)$arr_xml_file_content->recordTarget->patientRole->patient->languageCommunication->languageCode['code']);
			}
			return $arr_pt_details_return;
		}
		
		if($xml_file){
			$arr_xml_file_content=simplexml_load_string($xml_file);
			if(count($arr_xml_file_content)>0){
				$arr_pt_details_return['fname']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->name->given;
				for($i=0; $i<sizeof($arr_xml_file_content->recordTarget->patientRole->patient->name->given); $i++){
					if($arr_xml_file_content->recordTarget->patientRole->patient->name->given[$i]['qualifier'] == "CL"){
						$arr_pt_details_return['fname'] = trim($arr_xml_file_content->recordTarget->patientRole->patient->name->given->$i);
					}else{
						$arr_pt_details_return['mname'] = trim($arr_xml_file_content->recordTarget->patientRole->patient->name->given->$i);
					}
					/* if($xml->recordTarget->patientRole->patient->name->given[$i]['qualifier'] == "CL" || $i==0)
					$this->arrPatientData['fname'] = trim($xml->recordTarget->patientRole->patient->name->given->$i);
					else
					$this->arrPatientData['mname'] = trim($xml->recordTarget->patientRole->patient->name->given->$i); */
				}
				if($arr_pt_details_return['fname'] == ""){
					$arr_pt_details_return['fname'] = (string)$arr_pt_details_return['mname'];
				}
				$arr_pt_details_return['lname']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->name->family;
				$arr_pt_details_return['sex']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->administrativeGenderCode['displayName'];
				$arr_pt_details_return['dob']=(string)$arr_xml_file_content->recordTarget->patientRole->patient->birthTime['value'];
				$arr_pt_details_return['dob'] = date("Y-m-d", strtotime($arr_pt_details_return['dob']));
				
				$arr_pt_details_return['city']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->city;
				$arr_pt_details_return['state']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->state;
				$arr_pt_details_return['zip']=(string)$arr_xml_file_content->recordTarget->patientRole->addr->postalCode;
			}
			return $arr_pt_details_return;
		}
	}
	
	function get_ccd_folder_category_id($patientId){
		$folder_categories_id = false;
		$sql = "SELECT folder_categories_id FROM ".constant("IMEDIC_SCAN_DB").".folder_categories 
				WHERE folder_status ='active' 
				AND folder_name = 'CCD' 
				AND patient_id = '".$patientId."'"; 
		$res = imw_query($sql);
		if(imw_num_rows($res) == 0){
			$folder_categories_id = 0;
			$insertSql = "Insert into ".		
							"".constant("IMEDIC_SCAN_DB").".folder_categories ".
							"(folder_name,folder_status,patient_id)".
							"VALUES ('CCD', 'active', '".$patientId."')"; 
			$rsInsertSql = imw_query($insertSql);	
			$folder_categories_id = imw_insert_id();
		}else{
			$rowSql = imw_fetch_array($res);
			$folder_categories_id = $rowSql['folder_categories_id'];
		}
		return $folder_categories_id;
	}
	
	function get_ccd_xml_id($patient_id,$xml_path){
		$folder_categories_id = $this->get_ccd_folder_category_id($patient_id);
		$arrName = explode("/",$xml_path);
		$db_name = end($arrName);
		
		$dir_path=$GLOBALS['fileroot']."/data/".constant('PRACTICE_PATH');
		$full_file_path = $dir_path.$xml_path;
		$pathinfo = pathinfo($full_file_path);
		$filesize = filesize($full_file_path);
		
		if($folder_categories_id > 0){
			/*****FIRST CHECK IF EXISTS******/
			$sel_query = "SELECT scan_doc_id FROM ".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
							WHERE folder_categories_id = '".$folder_categories_id."' AND  
								patient_id = '".$patient_id."' AND 
								doc_title = 'CCD-".$db_name."' AND 
								doc_type = 'xml' AND 
								doc_size = '".$filesize."' AND 
								doc_upload_type='upload' AND 
								pdf_url = 'CCD-".$patient_id."-".date('Ymd')."' AND 
								upload_operator_id = '".$_SESSION['authId']."' AND 
								file_path = '".$xml_path."' AND 
								file_extension = 'xml' AND 
								CCDA_type = 'Ambulatory_CCDA' LIMIT 0,1";
			$sel_res = imw_query($sel_query);
			if($sel_res && imw_num_rows($sel_res)==1){
				$sel_rs = imw_fetch_assoc($sel_res);
				return $sel_rs['scan_doc_id'];				
			}else{
				/******INSERT IF NOT EXISTS*******/
				$dtNew = date('F d, Y',strtotime(date('Y-m-d')));
				$qry_ccd_ins = "Insert into  ".	"".constant("IMEDIC_SCAN_DB").".scan_doc_tbl 
					SET folder_categories_id = '".$folder_categories_id."', 
					patient_id = '".$patient_id."',
					doc_title = 'CCD-".$db_name."',
					doc_type = 'xml',
					doc_size = '".$filesize."',
					doc_upload_type='upload',
					pdf_url = 'CCD-".$patient_id."-".date('Ymd')."',
					upload_docs_date = '".date('Y-m-d H:i:s')."',
					upload_operator_id = '".$_SESSION['authId']."',
					file_path = '".$xml_path."',
					file_extension = 'xml',
					CCDA_type = 'Ambulatory_CCDA'			
				";
							
				$res_ccd_ins = imw_query($qry_ccd_ins);	
				$scan_doc_id = imw_insert_id();
				return $scan_doc_id;
			}
		}
		return false;
	}

	//This returns the array containg the allowed direct access users 
	public function getDirectAllowedUsers($authId = ''){
		if(empty($authId)) $authId = ((int)$_SESSION['authId']);

		$returnArr = array();
		$sql = 'SELECT `direct_access` FROM `users` WHERE `id`='.($authId).'';
		
		$resp = imw_query($sql);
		if($resp && imw_num_rows($resp) > 0)
		{
			$accessList = imw_fetch_assoc($resp);
			$accessList = $accessList['direct_access'];
			
			$sqlAccessName = 'SELECT `id`, CONCAT(`lname`, \',\', `fname`, IF(`mname`!=\'\', CONCAT(\', \', SUBSTRING(`mname`, 1, 1), \'.\'), \'\')) AS \'name\' FROM `users` WHERE `id` IN('.$accessList.')';
			$respAccessName = imw_query($sqlAccessName);
			
			if( $respAccessName && imw_num_rows($respAccessName) > 0 )
			{
				while( $row = imw_fetch_assoc($respAccessName) )
					array_push($returnArr, $row);
			}
		}
		
		/*Add Current User Data at top*/
		if($this->check_pt_direct_credentials($authId) == 1){
			array_unshift($returnArr, array('id'=>$authId, 'name'=>'My Inbox'));
		}

		return $returnArr;
		
	}
    
    
    public function fetch_messages_folder() {
        $return=array();
        $sql = 'SELECT folder_id,folder_name FROM user_messages_folder WHERE folder_status="active" order by folder_name ASC';
        $resp = imw_query($sql);
		if($resp && imw_num_rows($resp) > 0)
		{
            while( $row = imw_fetch_assoc($resp) ){
                $return[$row['folder_id']]=trim($row['folder_name']);
            }
        }
        return $return;
    }
}
?>