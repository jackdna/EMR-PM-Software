<?php
/*
 The MIT License (MIT)
 Distribute, Modify and Contribute under MIT License
 Use this software under MIT License
 
 Coded in PHP7
 Purpose: Medical History -> Order set class
 Access Type: Indirect Access.
 
*/
include_once $GLOBALS['srcdir'].'/classes/CLSAlerts.php';
$cls_alerts = new CLSAlerts;

class Order_set extends MedicalHistory
{
	//Public variables 
	public $file_content = '';
	public $order_file_content = '';
	public $opArr = '';
	public $orderSetNameArr = array();
	public $orderDetailArr = array();
	public $order_set_detail_full = array();
	
	
	//Status dropdown 
	public $selectArr = '';
	
	public function __construct($tab = 'ocular')
	{
		parent::__construct($tab);
		$this->order_set_vocabulary = $this->get_vocabulary("medical_hx", "order_sets");
		$this->selectArr = array('All','Ordered','In Progress','Completed');
		$this->opArr = array('Ordered','In Progress','Completed');
		$this->get_order_sets();
		$this->get_orders_details();
	}
	
	//Get order sets name arr
	public function get_order_sets(){
		$qry = imw_query("select id,orderset_name,order_id,order_set_option from order_sets");
		while($orderSetDetails = imw_fetch_assoc($qry)){
			$id = $orderSetDetails['id'];
			$orderset_name = $orderSetDetails['orderset_name'];
			$this->orderSetNameArr[$id] = $orderset_name;
		}
	}
	
	//Get order details arr
	public function get_orders_details(){
		$qry = imw_query("select * from order_details");
		while($orderQryRes = imw_fetch_assoc($qry)){
			$id = $orderQryRes['id'];
			$this->orderDetailArr[$id] = $orderQryRes;
		}
	}
	
	//Status dropdown 
	public function get_status_bar_opt($change_status){
		$str = '';
		foreach($this->selectArr as $key => $val){
			$sel = '';
			if($key == $change_status){
				$sel = 'selected';
			}
			$str .= '<option value="'.$key.'" '.$sel.'>'.$val.'</option>';
		}
		return $str;
	}
	
	//returns all order data sets
	public function get_all_order_set_data($obj){
		$this->file_content = '';
		foreach($obj as $val){
			$c_date =$val['c_date'];
			$order_id = $val['order_id'];
			$main_id = $val['order_set_associate_details_id'];
			$order_detail_arr = $this->orderDetailArr[$order_id];
			$order_name = $order_detail_arr['name'];
			$showSite = '';
			$showInf = 'none';
			//--- ORDER INFORMATIONAL CHECK ---------
			$o_type = $order_detail_arr['o_type'];
			preg_match('/Information/',$o_type,$infCheck);
			if(count($infCheck) > 0){
				$showSite = 'none';
				$showInf = '';
			}
			$orders_site_text = $val['orders_site_text'];
			$orders_when_text = $val['orders_when_day_txt'];
			$orders_when_text .= ' '.$val['orders_when_text'];
			$orders_priority_text = $val['orders_priority_text'];
			$order_set_options = preg_replace('/__/',', ',$val['order_set_options']);
			$orders_options = preg_replace('/__/',', ',$val['orders_options']);
			$instruction = $val['instruction_information_txt'];
			$delete_status = $val['delete_status'];
			$set_delete_status = $val['set_delete_status'];
			$orders_status = $val['orders_status'];
			$provider_name = $val['fname'][0];
			$provider_name .= $val['lname'][0];
			$provider_name = strtoupper($provider_name);
			//---- ORDERS STATUS CHECK -----			
			$status_option = '';
			$status_option_val = $this->opArr[$orders_status];
			for($p=0;$p<count($this->opArr);$p++){
				$sel = $p == $orders_status ? 'selected="selected"' : '';
				$status_option .= '<option value="'.$p.'" '.$sel.'>'.$this->opArr[$p].'</option>';
			}
			$showDrop = '';
			$showTxt = 'none';
			$class = 'text_10';
			if($delete_status > 0 || $set_delete_status > 0){
				$showDrop = 'none';
				$showTxt = '';
				$class = 'text-strike';
				$instruction = $val['orders_reason_text'];
			}
			//--- ORDER SET NAME ------
			$primary_set_id = $val['primary_set_id'];
			if($primary_set_id != $previous_id){
				$previous_id = $primary_set_id;
				$orderset_name = $this->orderSetNameArr[$val['order_set_id']];
				$this->file_content .= '<tr>
						<td class="'.$class.'">'.$c_date.'</td>
						<td class="'.$class.'">'.$orderset_name.'</td>
						<td class="'.$class.'">'.$provider_name.'</td>
						<td class="'.$class.'">&nbsp;</td>
						<td class="'.$class.'">&nbsp;</td>
						<td class="'.$class.'">&nbsp;</td>
						<td class="'.$class.'">'.$order_set_options.'</td>
						<td class="'.$class.'">&nbsp;</td>
					</tr>';
			}
			
			$ins_td = NULL;
			if($showInf != 'none'){
				$ins_td = "<td class=\"".$class."\" colspan=\"3\" style=\"display:".$showInf."\">".$instruction."&nbsp;</td>";
			}
			else{
				$ins_td = '<td class="'.$class.'" style="display:'.$showSite.';">'.$orders_site_text.'&nbsp;</td>
					<td class="'.$class.'" style="display:'.$showSite.';">'.$orders_when_text.'&nbsp;</td>
					<td class="'.$class.'" style="display:'.$showSite.';">'.$orders_priority_text.'&nbsp;</td>';
			}
			
			$sts_td_data = NULL;
			if($showDrop != 'none'){
				$sts_td_data = '<select name="order_set_status['.$main_id.'][]" class="selectpicker" data-width="100%" onChange="top.fmain.changeValue();">
						'.$status_option.'
					</select>';
			}
			else{
				$sts_td_data = $status_option_val;
			}
			$this->file_content .= '<tr>
					<td class="'.$class.'">&nbsp;</td>
					<td class="'.$class.'">'.$order_name.'</td>
					<td class="'.$class.'">&nbsp;</td>				
					'.$ins_td.'
					<td class="'.$class.'">'.$orders_options.'&nbsp;</td>
					<td class="'.$class.'">'.$sts_td_data.'</td>
				</tr>';
		}
		return $this->file_content;
	}
	
	//returns all order data without set
	public function get_all_order_data_without_set($obj){
		$this->order_file_content = '';	
		foreach($obj as $val){
			$c_date = $val['c_date'];
			$order_id = $val['order_id'];
			$main_id = $val['order_set_associate_details_id'];
			$order_detail_arr = $this->orderDetailArr[$order_id];
			$order_name = $order_detail_arr['name'];
			$showSite = '';
			$showInf = 'none';
			//--- ORDER INFORMATIONAL CHECK ---------
			$o_type = $order_detail_arr['o_type'];
			preg_match('/Information/',$o_type,$infCheck);
			if(count($infCheck) > 0){
				$showSite = 'none';
				$showInf = '';
			}
			$bgcolor = $q%2 == 0 ? '#F4F9EE' : '#FFFFFF';
			$orders_site_text = $val['orders_site_text'];
			$orders_when_text = $val['orders_when_day_txt'];
			$orders_when_text .= ' '.$val['orders_when_text'];
			$orders_priority_text = $val['orders_priority_text'];
			$orders_options = preg_replace('/__/',', ',$val['orders_options']);
			$instruction = $val['instruction_information_txt'];
			$delete_status = $val['delete_status'];
			$orders_status = $val['orders_status'];
			$provider_name = $val['fname'][0];
			$provider_name .= $val['lname'][0];
			$provider_name = strtoupper($provider_name);
			//---- ORDERS STATUS CHECK -----			
			$status_option = '';
			$status_option_val = $this->opArr[$orders_status];
			for($p=0;$p<count($this->opArr);$p++){
				$sel = $p == $orders_status ? 'selected="selected"' : '';
				$status_option .= '<option value="'.$p.'" '.$sel.'>'.$this->opArr[$p].'</option>';
			}		
			$q++;
			$showDrop = '';
			$showTxt = 'none';
			$class = 'text_10';
			if($delete_status > 0){
				$showDrop = 'none';
				$showTxt = '';
				$class = 'text-strike';
				$instruction = $val['orders_reason_text'];
			}
			
			$ins_td = NULL;
			if($showInf != 'none'){
				$ins_td = "<td class=\"".$class."\" colspan=\"3\" style=\"display:".$showInf."; \">".$instruction."&nbsp;</td>";
			}
			else{
				$ins_td = '<td class="'.$class.'" style="display:'.$showSite.';">'.$orders_site_text.'&nbsp;</td>
					<td class="'.$class.'" style="display:'.$showSite.';">'.$orders_when_text.'&nbsp;</td>
					<td class="'.$class.'" style="display:'.$showSite.';">'.$orders_priority_text.'&nbsp;</td>';
			}
			
			$sts_td_data = NULL;
			if($showDrop != 'none'){
				$sts_td_data = '<select name="order_set_status['.$main_id.'][]" class="selectpicker" data-width="100%" onChange="changeValue();">
						'.$status_option.'
					</select>';
			}
			else{
				$sts_td_data = $status_option_val;
			}
			
			$this->order_file_content .= '<tr>
					<td class="'.$class.'" >'.$c_date.'&nbsp;</td>
					<td class="'.$class.'" >'.$order_name.'&nbsp;</td>
					<td class="'.$class.'" >'.$provider_name.'&nbsp;</td>
					'.$ins_td.'				
					<td class="'.$class.'" >'.$orders_options.'&nbsp;</td>
					<td class="'.$class.'" style="display:'.$showDrop.';">'.$sts_td_data.'</td>
				</tr>';
		}	
		return $this->order_file_content;
	}
	
	//Get ORDERS/ ORDER SET DATA 
	public function get_all_order_set_details($request){
		$sql = "select order_set_associate_chart_notes.order_set_associate_id as primary_set_id,
		order_set_associate_chart_notes.order_set_id,
		order_set_associate_chart_notes.patient_id ,
		order_set_associate_chart_notes.logged_provider_id ,
		order_set_associate_chart_notes.order_set_options , 
		order_set_associate_chart_notes.delete_status as set_delete_status  ,
		date_format(order_set_associate_chart_notes.created_date,'".get_sql_date_format()." %H:%s %p') as c_date,
		order_set_associate_chart_notes.logged_provider_id ,
		order_set_associate_chart_notes_details.*,
		users.lname,users.fname,users.mname
		from order_set_associate_chart_notes left join
		order_set_associate_chart_notes_details on
		order_set_associate_chart_notes.order_set_associate_id = 
		order_set_associate_chart_notes_details.order_set_associate_id
		join users on users.id = 
		order_set_associate_chart_notes.logged_provider_id
		left join chart_master_table ON chart_master_table.id=order_set_associate_chart_notes.form_id
		where order_set_associate_chart_notes.patient_id = '$this->patient_id' AND chart_master_table.purge_status='0' AND chart_master_table.delete_status='0' ";
		if($request['change_order_set_status'] > 0){
			$provider_status_val = $request['change_order_set_status'] - 1;
			$sql .= " and order_set_associate_chart_notes_details.orders_status = '$provider_status_val'";
		}
		if(empty($request['ordersIdStr']) == false){
			$ordersIdStr = $request['ordersIdStr'];
			$sql .= " and order_set_associate_chart_notes_details.order_id in ($ordersIdStr)";
		}
		$sql .= " order by order_set_associate_chart_notes.created_date desc";
		$query = imw_query($sql);
		$counter = 0;
		if(imw_num_rows($query) > 0){
			while($row = imw_fetch_array($query)){
				$this->order_set_detail_full[] = $row;
				$counter++;
			}
			
			$file_content_arr = array();
			$order_file_content_arr = array();
			
			foreach($this->order_set_detail_full as $obj){
				$order_set_id = $obj['order_set_id'];	
				$pkIdAuditTrail .= $obj['order_set_associate_id']."-";
				if($pkIdAuditTrailID == ""){		
					$pkIdAuditTrailID = $obj['order_set_associate_id'];
				}
				
				if($order_set_id > 0){
					//GET ALL ORDER SET DATA
					$file_content_arr[] = $obj;
				}else{	
					//GET ALL ORDERS DATA WITHOUT ORDER SET
					$order_file_content_arr[] = $obj;
				}
			}
			
			//File content block
			$file_content_txt = $this->get_all_order_set_data($file_content_arr);
			//Order file content block
			$order_file_content_txt = $this->get_all_order_data_without_set($order_file_content_arr);
		}
		
		$return_arr['file_content'] = $file_content_txt;
		$return_arr['order_file_content'] = $order_file_content_txt;
		$return_arr['counter'] = $counter;
		$return_arr['pkIdAuditTrailID'] = $pkIdAuditTrailID;
		$return_arr['pkIdAuditTrail'] = $pkIdAuditTrail;
		return $return_arr;
	}
	
	//Saving order set changes
	public function save_order_set($request){
		$order_set_status_arr = array_keys($request['order_set_status']);
		$cDate = date('Y-m-d');
		$counter = 0;
		for($i=0;$i<count($order_set_status_arr);$i++){
			$id = $order_set_status_arr[$i];
			$status = join(',',$request['order_set_status'][$id]);
			$sql = "update order_set_associate_chart_notes_details set orders_status = '$status',
					modified_date = '$cDate',modified_operator = '".$_SESSION['authId']."'
					where order_set_associate_details_id = '$id'";	
			imw_query($sql);
			$counter = ($counter+imw_affected_rows());
		}
		return $counter;	
	}
	
	//Set CLS Alerts
	public function set_cls_alerts(){
		global $cls_alerts;
		$return_str= '';
		$alertToDisplayAt = "admin_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getAdminAlert($_SESSION['patient'],$alertToDisplayAt,$form_id,"350px");
		$alertToDisplayAt = "patient_specific_chart_note_med_hx";
		$return_str .= $cls_alerts->getPatSpecificAlert($_SESSION['patient'],$alertToDisplayAt,"350px");
		$return_str .= $cls_alerts->autoSetDivLeftMargin("140","265");
		$return_str .= $cls_alerts->autoSetDivTopMargin("250","30");
		$return_str .= $cls_alerts->writeJS();
		return $return_str;	
	}	
}
?>