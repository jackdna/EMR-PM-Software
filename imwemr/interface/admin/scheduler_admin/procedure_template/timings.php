<?php
/*
File: timings.php
Purpose: Add/Modify procedure timings
Access Type: Direct
*/
?>
<form name="frm_proc_time" method="post" action=""> 
    <input type="hidden" name="pro_id" value="">
    <input type="hidden" name="doctor_id" value="">
    <input type="hidden" name="action" value="save_timings">
	<table cellpadding="1" cellspacing="1" bgcolor="#FFCC99" class="table table-bordered">
		<?php
		for($l = 1; $l <= 4; $l++){
			$strNoOfTimes = date("jS",mktime(0,0,0,12,$l,2009));
			?>
		<tr bgcolor="#FFFFFF">
			<td class=""><?php echo $strNoOfTimes;?> Time</td>
			<td class="text-center text-nowrap">
			&nbsp;From&nbsp;
				<select id="time_aft_from_hourPHY<?php echo $l;?>" name="time_aft_from_hourPHY<?php echo $l;?>" class="selectpicker">        
					<option value="">--</option> 
					<?php 
					foreach ($tm_array as $tm){ 
						print "<option value=$tm>$tm</option>";
					}
					?>
				</select>                                            
				<select id="time_aft_from_minsPHY<?php echo $l;?>" name="time_aft_from_minsPHY<?php echo $l;?>" class="selectpicker">        
					<option value="">--</option>                                        
					<?php
					foreach ($tm_min_array as $tmm){
						print "<option value=$tmm>$tmm</option>";
					}
					?>
				</select>
				<select id='ap1_aftPHY<?php echo $l;?>' name='ap1_aftPHY<?php echo $l;?>' class="selectpicker" >                                                    
					<option value="AM">AM</option>
					<option value="PM">PM</option>                        
				</select>
			&nbsp;To&nbsp;
				<select id="time_aft_to_hourPHY<?php echo $l;?>" name="time_aft_to_hourPHY<?php echo $l;?>" class="selectpicker">        
					<option value="">--</option>                                        
					<?php
					foreach ($tm_array as $tm){
						print "<option value=".$tm." ".$chk_sel.">".$tm."</option>";
					}
					?>
				</select>
				<select id="time_aft_to_minsPHY<?php echo $l;?>" name="time_aft_to_minsPHY<?php echo $l;?>" class="selectpicker">                
					<option value="">--</option>                            
					<?php
					foreach ($tm_min_array as $tmm){
						print "<option value=$tmm $chk_sel_min>$tmm</option>";
					}
					?>
				</select>
				<select id='ap2_aftPHY<?php echo $l;?>' name='ap2_aftPHY<?php echo $l;?>' class="selectpicker" >
					<option value="AM">AM</option>
					<option value="PM">PM</option>
				</select>
			</td>
		</tr>
			<?php
		}
		?>
		<tr>
			<td colspan="2" align="center" class="">
				<input type="button" name="save" value=" Save " onclick="javascript:save_timings(this.form);" class="btn btn-default" id="Submit" />
				<input type="button"  class="btn btn-default" name="cancelMe" value=" Cancel " onclick="hideProviderProcTimeDiv();" id="cancelMe" />
			</td>
		</tr>
	</table>
</form>