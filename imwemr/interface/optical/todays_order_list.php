<?php 
	$print_data = '';
	
	//Get master arr for order data
	$order_data = $optical_obj->get_today_order_array();
	
	if(count($order_data) == 0){
		if(empty($optical_obj->patient_id) === true){
			$print_data = '<tr><td colspan="12" class="text-center text-danger"><b>No orders exists for '.date('m-d-Y').'</b></td></tr>';
		}else{
			$print_data = '<tr><td colspan="12" class="text-center text-danger">There is no order for selected patient.</td></tr>';
		}
	}
	
	//Get Operator details
	$operator_details = $optical_obj->get_operator();

	//creating html to display
	$counter = 1;
	foreach($order_data as $obj){
		$patient_name = $obj['Name'].' '.substr($obj['fname'],0,1);
		$balance = $obj['balance'] > 0 ? show_currency().number_format($obj['balance'],2):'';
		$user_id = $obj['operator_id'];
		$Optical_Order_Form_id = $obj['Optical_Order_Form_id'];
		$patient_id = $obj['patient_id'];
		$order_date = $obj['orderDate'];
		$orderPlaceDate = $obj['orderPlaceDate'];
		$modified_date = $obj['modified_date'];
		$phyName = $operator_details[$user_id];
		$print_data .= '
			<tr>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.$counter.'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.$orderPlaceDate.'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.$order_date.'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.ucwords(trim($patient_name)).' - '.$patient_id.'</a></td>			
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.core_phone_format($obj['phone_home']).'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.ucwords(trim($obj['frame_name'])).'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.ucwords(trim($obj['frame_style'])).'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.trim($obj['frame_color']).'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.ucwords(trim($obj['lens_opt'])).'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.$modified_date.'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.$balance.'</a></td>
				<td ><a href="javascript:edit_order('.$Optical_Order_Form_id.','.$patient_id.');" >'.ucwords(trim($phyName)).'</a></td>
			</tr>';
		$counter++;
	}
?>
<div class="row pt10">
	<div class="col-sm-12">
		<div class="row">
			<table class="table table-striped table-hover table-bordered">
				<tr class="grythead">
					<th>#</th>
					<th>Order Date</th>
					<th>Modified Date</th>
					<th>Patient Name</th>
					<th>Patient Phone</th>
					<th>Frame Make</th>
					<th>Frame Style</th>
					<th>Frame Color</th>
					<th>Vision</th>
					<th>Modified Date</th>
					<th>Balance</th>
					<th>Sale Operator</th>
				</tr>
				<?php echo $print_data; ?>	
			</table>	
		</div>	
	</div>	
</div>
<script type="text/javascript">
	function edit_order(id,pid){
		window.location.href = top.JS_WEB_ROOT_PATH+'/interface/optical/index.php?showpage=optical_order_form&order_id='+id+'&patient_id='+pid;
	}
</script>