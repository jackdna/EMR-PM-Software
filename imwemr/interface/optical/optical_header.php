<?php 
	$get_pt_ins_details = $optical_obj->get_pt_ins_details();
?>
<div class="row">
	<div class="col-sm-12 purple_bar">
		<div class="row">
			<div class="col-sm-3">
				<label>&nbsp;&nbsp;<?php echo $header_tab_title; ?></label>	
			</div>
			<div class="col-sm-6 text-center">
				<label><?php echo $get_pt_ins_details; ?></label>	
			</div>
			
			<div class="col-sm-3">
				<div class="input-group">
					<!--<select name="sale_operator" class="selectpicker" data-width="100%" data-size="5">
						<?php echo $cls_object->drop_down_providers($optical_obj->auth_id); ?>	
					</select>
					<label class="datepicker input-group-addon pointer" title="Search by date" onChange="changeFrmAction();">
						<span class="glyphicon glyphicon-calendar"></span>
					</label>-->	
				</div>	
			</div>	
		</div>	
	</div>
</div>
