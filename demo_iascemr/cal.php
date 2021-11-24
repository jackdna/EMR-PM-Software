<?php
$display_calender="block";

?>
<table class="table_collapse new_table_cal" id="cal" style=" background-color:#ECF1EA; display:<?php echo $display_calender;?>; " >	<!-- onClick="closeEpostPopNew();" -->			
	
	
	
	<tr style="background-color:#ECF1EA;" >
		<td style=" width:100%" id="cal_ajax_id">
		<?php 
			if($_REQUEST["sel_month_number"]<>""){
				$selected_month_number=$_REQUEST["sel_month_number"];
				$year_now=$_REQUEST["year_now"];
				if($selected_month_number>12) {
					$selected_month_number = 1;
					if(strlen($selected_month_number)==1) {
						$selected_month_number = '0'.$selected_month_number;
					}
					$year_now = $year_now+1;
				}
				if($selected_month_number==0) {
					$selected_month_number = 12;
					$year_now = $year_now-1;
				}
			}else{	
				$selected_month_number = date("m");
				$year_now = date("Y");
			}
			$selected_month_number_IncrByOne = $selected_month_number+1;//date("m",mktime(0,0,0,$selected_month_number+1,1,$year_now));
			$selected_month_number_DecrByOne = $selected_month_number-1;//date("m",mktime(0,0,0,$selected_month_number-1,1,$year_now));
			$year_now_IncrByOne = $year_now+1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now+1));
			$year_now_DecrByOne = $year_now-1;//date("Y",mktime(0,0,0,$selected_month_number,1,$year_now-1));
			$lastday=date("t",mktime(0,0,0,$selected_month_number,1,$year_now));
			$weekday=date("w",mktime(0,0,0,$selected_month_number,1,$year_now));
			if($weekday==0) {
				$weekday = 7;
			}
			$days=array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
			$month_name = date("F",mktime(0,0,0,$selected_month_number,1,$year_now));
			?>			
			<table class="table_collapse" style="width:100%;">
				<tr>
					<td class="valignMiddle text_homeb all_border"  style="width:100%; text-align:center; padding-left:300px;height:23px; background-color:#FFFFFF;border-bottom:1px solid #B4C8AC;border-top:1px solid #B4C8AC;">
                    	
						<table class="table_pad_bdr" style="text-align:center;">
							<tr>
								<td class="alignCenter">
									<a href="javascript:change_month(<?php echo $selected_month_number;?>,<?php echo $year_now_DecrByOne;?>);">
										<img style="border:none;" src="images/cal2_back.jpg" >
									</a>		
								</td>
								<td style="width:3px;"></td>
								<td class="alignCenter">
									<a href="javascript:change_month(<?php echo $selected_month_number_DecrByOne;?>,<?php echo $year_now;?>);">
										<img style="border:none;" src="images/cal_back.jpg" >
									</a>
								</td>
								<td style="width:3px;"></td>					
								<td class="alignCenter text_10b nowrap">
									<select class="text_10" name="monthList" onChange="javascript:change_month(this.value,<?php echo $year_now;?>);">
										<?php
										for($k=1;$k<=12;$k++) {
											$monthListValue = date("F",mktime(0,0,0,$k,1,$year_now));
											if(strlen($k)==1) { $k='0'.$k;}
											if(strlen($selected_month_number)==1) { $selected_month_number='0'.$selected_month_number;}
										?>
											<option value="<?php echo $k;?>" <?php if($selected_month_number==$k) { echo "selected"; }?>><?php echo $monthListValue;?></option>
										<?php
										}
										?>	
									</select>
									<?php echo $year_now;?>
									<?php //echo $month_name." ".$year_now;?>
								</td>
								<td style="width:3px;"></td>
								<td class="alignCenter">
									<a href="javascript:change_month(<?php echo $selected_month_number_IncrByOne;?>,<?php echo $year_now;?>);">
										<img style="border:none;" src="images/cal_next.jpg" >
									</a>
								</td>
								<td style="width:3px;"></td>
								<td class="alignCenter">
									<a href="javascript:change_month(<?php echo $selected_month_number;?>,<?php echo $year_now_IncrByOne;?>);">
										<img style="border:none;" src="images/cal2_next.jpg" >
									</a>
								</td>
							</tr>
						</table>
                        			
					</td>
				</tr>
				<tr style="background-color:#F8F9F7;">
					<td>
						<table class=""  style="width:100%; height:100%; text-align:center;padding:0; border-collapse:separate; border:1px; border-style:solid; border-color:#E4E4E4;">
							<tr class="text_homeb" style="background-color:#F8F9F7;">
								<?php 
								for($i=0;$i<7;$i++){?>
									<td class="text_10"><?php echo $days[$i];?></td>
									<?php 
								}
								?>
							</tr>
							<tr class="alignCenter valignMiddle" style="height:35px; background-color:#FFFFFF;">
								<?php 
								$j=1;
								$weekday2 = 0;
								$days = false;
								$p = 1;
								while($p<$lastday){
									if($days == true){
										$p++;
									}
									if($j<=7){				
										$weekday2++;
										if($p==date("d")){
											$color="#FBD78D";
											
										}else{
											$color="";
										
										}
										
										if($weekday2==$weekday || $days == true){				
											$days = true;
											echo "<td style='height:15px; background-color:".$color.";' id='mon_".$p."' onMouseOver='javascript:swap_cal_color(this.id,\"Yes\");'  onMouseOut='javascript:swap_cal_color(this.id,\"No\");'  ><a class='link_home text_10' href='?day=$p&selected_month_number=$selected_month_number&amp;sel_year_number=$year_now&amp;date_click=yes' >$p</a></td>";	
										}else{
											echo "<td>&nbsp;</td>";	
										}
										if($j%7==0){
											echo "</tr><tr class='alignCenter valignMiddle' style='height:35px;background-color:#FFFFFF;' >";
										}
									}else{	
										if($p==date("d")){
											$color="#FBD78D";
											
										}else{
											$color="";
									}
									echo "<td style='height:15px; background-color:".$color.";' id='mon_".$p."'  onMouseOver='javascript:swap_cal_color(this.id,\"Yes\");'  onMouseOut='javascript:swap_cal_color(this.id,\"No\");'><a class='link_home text_10' href='?day=$p&amp;selected_month_number=$selected_month_number&amp;sel_year_number=$year_now&amp;date_click=yes'>$p</a></td>";
									if($j%7==0){
										echo "</tr><tr class='alignCenter valignMiddle' style='height:35px;background-color:#FFFFFF;' >";
									}
								}
								$j++;
							}
						?>
                        	</tr>
						</table>
					</td>
				</tr>			
			</table>
		</td>
	</tr>			
</table>