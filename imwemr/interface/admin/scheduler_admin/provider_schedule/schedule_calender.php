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
?>
<style type="text/css">
 div#month_1{ 
	position:relative; 
	overflow:hidden;	
	width:100%; 
	height:8px; 
	z-index:100	
}
div#month_2{ 
	position:absolute; 
	left:0px; top:0px; 
	width:100%; height:8px; 	
	overflow:hidden;
	border:0px solid #000000;
	z-index:1; 
}
div#month_3{ 
	position:absolute; 	
	left:0px; top:0px; 
	z-index:1; 
}			

div#month_11{ 
	position:relative; overflow:hidden;
	
	width:100%; height:8px; z-index:100	
}
div#month_12{ 
	position:absolute; 
	left:0px; top:0px; 
	width:100%; height:200px; 	
	overflow:hidden;
	border:0px solid #000000;
	z-index:1; 
}
div#month_13{ 
	position:absolute; 	
	left:0px; top:0px; 
	z-index:1; 
}	 

div#month_1_physician{ 
	position:relative; overflow:hidden;
	left:0px; top:0px; 
	width:100%;  height:11px; z-index:100;	
}
div#month_2_physician{ 
	position:relative; 
	left:0px; top:0px; 
	width:100%;  height:11px; 	
	overflow:hidden;
	z-index:100; 
}
div#month_3_physician{ 
	position:relative; 	
	left:0px; top:0px; 
	z-index:100; 
}			
 
div#month_11_physician{ 
	position:relative;
	overflow:hidden;
	width:100%;
	 height:10px; 
	 left:0px; top:0px; 
	 z-index:500	
}
div#month_12_physician{ 
	position:absolute; 
	left:0px; top:0px; 
	width:100%; height:10px; 	
	overflow:hidden;
	left:0px; top:0px; 
	border:0px solid #000000;
	z-index:1; 
}
div#month_13_physician{ 
	position:absolute; 	
	left:0px; top:0px; 
	
	z-index:1; 
}		

span.glyphicon{color:#4f4f4f;vertical-align:bottom; background-color:#dcdcdc; border-radius:3px; padding:4px; margin-top:5px;}
.table>tbody>tr>td{border-top:none!important}
.table{margin-bottom:0px!important}	
.scecladtab td{ padding:0px!important}
#monthly_View .checkbox label::after{margin-left:-22px!important;}
</style>
<script type="text/javascript">
	function see_sel_month(){
		document.frm_dump_month.submit();
	}
	
	function setMonthValue(strVal){
		var mandyear=strVal.split("-");
		$("#themonth").val(parseInt(mandyear[0]));		
		$("#theyear").val(parseInt(mandyear[1]));	
	}
	
	function change_dateByDropDown(){
		var new_year = $("#theyear").val();
		var new_month = $("#themonth").val();
		$("#theyear").val(new_year);
		$("#themonth").val(new_month);
		$("#newDat").val('1');
		document.frm_dump_month.submit();
	}
	
	function change_date(obj){
		var new_year = $("#theyear").val();
		var new_month = $("#themonth").val();
		switch(obj){						
			case 'prev_year':
				new_year = parseInt(new_year) - parseInt(1);
			break;
			case 'prev_month':
				new_month = parseInt(new_month) - parseInt(1);
			break;
			case 'next_month':
				var tmp = parseInt(new_month) + parseInt(1);
				new_month = tmp;
			break;
			case 'next_year':
				new_year = parseInt(new_year) + parseInt(1);
			break;
		}
		$("#theyear").val(new_year);
		$("#themonth").val(new_month);
		$("#newDat").val('1');
		document.frm_dump_month.submit();
	}
	
	function change_template(obj,curDay,wrdata){	
		//reset form values
		formReset();
		var date_format_set_="<?php echo $GLOBALS['date_format']; ?>";
		
		var pro_id = $("#sel_pro_month").val();
		var highlight = $('#highlight_td').val();		
		if(highlight){
			if($("#"+highlight).parent().hasClass('calweeend'))
			   {
			   	//do not change bg for weekend dates
			   }
			   else
			   {
				document.getElementById(highlight).style.background = '#ffeccc';
				}
		}
		$('#wrdata').val('');
		if(typeof wrdata!= "undefined")
		{
			$('#wrdata').val(wrdata);
		}	
		document.getElementById(obj).style.background = '#FFCC66';
		$('#highlight_td').val(obj);
		if(pro_id){
			var curmonth = $("#themonth").val();
			var curyear = $("#theyear").val();
			var url = 'provider_schedule_template2.php?cur_day='+curDay;	
			url += '&proId='+pro_id+'&cur_month='+curmonth+'&cur_year='+curyear+'&wrdata='+wrdata;
			var check = true;
			if(document.getElementsByName("dateSelect[]")){
				var chekedValstr="";
				var objGrp=document.getElementsByName("dateSelect[]");
				for(i=0;i<objGrp.length;i++){
					if(objGrp[i].checked==true){
						check = false;
						if(date_format_set_!="" && (date_format_set_=="DD-MM-YYYY" || date_format_set_=="dd-mm-yyyy" || date_format_set_=="DD/MM/YYYY")){
							chekedValstr=chekedValstr+objGrp[i].value+"-"+curmonth+"-"+curyear+","
						}else{
							chekedValstr=chekedValstr+curmonth+"-"+objGrp[i].value+"-"+curyear+","
						}
					}
				}
				$("#Start_date_String").val(chekedValstr);
			}
			var cur_week = curDay.split('_');
			//--- Check Save template for saturday and sunday -------
			if(check){
				var set_format_type=curmonth+"-"+cur_week[0]+"-"+curyear+",";
				if(date_format_set_!="" && (date_format_set_=="DD-MM-YYYY" || date_format_set_=="dd-mm-yyyy" || date_format_set_=="DD/MM/YYYY")){
					set_format_type=cur_week[0]+"-"+curmonth+"-"+curyear+",";
				}
				$("#Start_date_String").val(set_format_type);
			}
			$("#sel_child_schedule_name").html('<option value="">'+imw_msg_js('drop_sel')+'</option>');
			$("#sel_facility").val('');
			$("#sel_schedule_name").val('');
			$("#start_hour").val('');
			$("#start_min").val('');
			$("#start_time").val('');
			$("#end_hour").val('');
			$("#end_min").val('');
			$("#end_time").val('');
			$("#sch_app_id").val('');
			$("#template_label").val('');
			$("#Start_date").val(curmonth+'-'+cur_week[0]+'-'+curyear);
			$('#tmp_expiry_dt').val('');
			//show loading image
			top.show_loading_image("show");
			$.ajax({
				url:url,
				type:'GET',
				success:function(response){
					stateChanges_new(response);
					top.show_loading_image("hide");
					if(curmonth<10)curmonth="0"+curmonth;
					if(cur_week[0]<10)cur_week[0]="0"+cur_week[0];
					$('#get_cur_date label').html(curmonth+"-"+cur_week[0]+"-"+curyear);
				}
			});
		}
		else{
			top.fAlert("Please select Provider.");
			var objGrp = document.getElementsByName("dateSelect[]");
			for(i=0;i<objGrp.length;i++){
				objGrp[i].checked = false;
			}
		}
		
		Start_date_String=document.getElementById("Start_date_String").value;	
		if(Start_date_String != "")
		{
			Start_date_String_arr_us = Start_date_String.split(',');
			start_date_string_mod = '';
			if($.trim(wrdata) != "" && check == true && Start_date_String_arr_us.length ==2)
			{
				wrdata_arr_us = wrdata.split('|');
				Start_date_String_arr_us_split = Start_date_String_arr_us[0].split('-');
				Start_date_String_arr_us_split[1] = eval(Start_date_String_arr_us_split[1])+eval(wrdata_arr_us[1]);
				Start_date_String_arr_us[0] = Start_date_String_arr_us_split.join('-');
			}			
			for(var i=0; i<Start_date_String_arr_us.length; i++)
			{
				if(Start_date_String_arr_us[i] != "")
				{
					Start_date_String_arr_us[i] = know_dates_by_mnth_year(Start_date_String_arr_us[i]);
				}
				else
				{
					Start_date_String_arr_us.slice(i);
				}
			}
			
			Start_date_String_arr_us = Start_date_String_arr_us.slice(0,Start_date_String_arr_us.length-1);				
			Start_date_String = Start_date_String_arr_us.join(',');
			document.getElementById('tmp_start_dt_view_us').value = Start_date_String;			
		}
	}
	
	function know_dates_by_mnth_year(req_dt_us)
	{
		req_dt_us_arr = req_dt_us.split('-');
		feb_days = 28;
		if(req_dt_us_arr[2]%4 == 0 && req_dt_us_arr[2]%100 != 0)
		{
			feb_days = 29;
		}
		else
		{
			if(req_dt_us_arr[2]%400 == 0)
			{
				feb_days = 29;	
			}
		}
		
		dts_mnths_arr_us = new Array();
		dts_mnths_arr_us[1] = 31;
		dts_mnths_arr_us[2] = feb_days;
		dts_mnths_arr_us[3] = 31;
		dts_mnths_arr_us[4] = 30;
		dts_mnths_arr_us[5] = 31;
		dts_mnths_arr_us[6] = 30;
		dts_mnths_arr_us[7] = 31;
		dts_mnths_arr_us[8] = 31;
		dts_mnths_arr_us[9] = 30;
		dts_mnths_arr_us[10] = 31;
		dts_mnths_arr_us[11] = 30;
		dts_mnths_arr_us[12] = 31;
		
		if(req_dt_us_arr[1] > dts_mnths_arr_us[req_dt_us_arr[0]])
		{
			req_dt_us_arr[1] = req_dt_us_arr[1] - dts_mnths_arr_us[req_dt_us_arr[0]];
			req_dt_us_arr[0]++;
			
			if(req_dt_us_arr[0] == 13)
			{
				req_dt_us_arr[0] = 1;
				req_dt_us_arr[2]++;	
			}
		}
		
		return req_dt_us_arr.join('-');
	}
	
	function stateChanges(response){
		if(response != ''){
			top.show_loading_image('hide');
			$("#template_div_id").html(response);
		}
		else{
			top.show_loading_image('show');
		}
	}
	function stateChanges_new(response){
		if(response != ''){
			top.show_loading_image('hide');
			$("#template_div_id").html(response);
		}
		else{
			top.show_loading_image('show');
		}
	}
</script>
<div id="monthly_View">
<input type="hidden" name="themonth" id="themonth" value="<?php echo $month_number;?>">
<input type="hidden" name="theyear" id="theyear" value="<?php echo $year;?>">
<input type="hidden" name="newDat" id="newDat" value="">
<input type="hidden" name="cur_week" id="cur_week" value="">

<table class="table table-condensed table-hover	table_collapse">
	<tr>
		<td height="30">
			<table class="table table-bordered table-condensed scecladtab">
				<tr class="dayara" >
					<th class="col-sm-2">Monday</th>
					<th class="col-sm-2">Tuesday</th>
					<th class="col-sm-2">Wednesday</th>
					<th class="col-sm-2">Thursday</th>
					<th class="col-sm-2">Friday</th>
					<th class="col-sm-2">Sat/Sun</th>
				</tr>
				<?php
				$bg_close_color="#ffeccc";
				$week_tname_arr = array("Mon"=>1,"Tue"=>2,"Wed"=>3,"Thu"=>4,"Fri"=>5,"Sat"=>6,"Sun"=>7);
				$day=1;							
				/// LAST DAY OF MONTH + ADD ALL WEEK DAYS ////
				$eff_day=date("D");	
				$title_bg_color="#ECE9D8";		
				$month_number=$date["mon"];
				$year=$date["year"];
				if($year_now) $year = $year_now;
				if($selmonth_number) $month_number = $selmonth_number;
				$date_first=getdate(mktime(0,0,0,$month_number,1,$year)); //get info of first day of this month
				$first_week_day=$date_first["wday"]; //get first week day as 0 is Sunday and so on
				$last_week_day=$date_first["wday"]; //get first week day as 0 is Sunday and so on
				$month_name=$date_first["month"];
				//Get the last date of the month($counter)
				$counter=27;	
				$flag=true;	
				while(($counter<=32) && ($flag))
				{
					$date_first=getdate(mktime(0,0,0,$month_number,$counter,$year));		
					if($date_first["mon"] != $month_number)
					{
						$lastday_of_month=$counter-1;
						$flag=false; 
					}		
					$counter++;
				}
				$prev_month_days=date("w",(mktime(0,0,0,$month_number,$day,$year)));							
				$next_month_days=date("w",(mktime(0,0,0,$month_number,$lastday_of_month,$year)));									
				/////////////////////////////////////////////						
				$firstweek=true;
				if($prev_month_days==0)
				{
					$fwday=(7-$first_week_day); //store for future use as value of $first_week_day is changed then														
					$first_week_day=7-$first_week_day;
					$prev_month_days=7-$prev_month_days;
				}else{						
					$fwday=($first_week_day-1); //store for future use as value of $first_week_day is changed then														
					$first_week_day=$fwday;
					$prev_month_days=$prev_month_days;						
				}							
				$last_day=$lastday_of_month;
				if($next_month_days>0)
				{
					$lastday_of_month=$lastday_of_month+(6-$next_month_days);
				}
				// Make AS Many TR And TD for storing values
				// Make AS Many TR And TD for storing values
				$ex1='col1';
				$ex2='col2';
				$ex3='col3';
				$ex4='col4';
				$wk=1;				
				$wn_for_rows = date("N",mktime(0, 0, 0, $month_number,1,$year));
				$lc_for_rows = $wn_for_rows - 1;
				$total_rows_lt = ceil(($lc_for_rows + 35)/7);
				while($day <= $lastday_of_month || $wk<=$total_rows_lt)
				{
					if($firstweek)
					{
						echo  "<tr >";
						for($i=1;$i<=$first_week_day;$i++)
						{
							$dy=$day-($prev_month_days-$i);										
							$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$dy,$year));	
							$eff_day=date("j",mktime(0, 0, 0, $month_number,$dy,$year));
							$eff_month=date("F",mktime(0, 0, 0, $month_number,$dy,$year));
							$eff_wday=date("w",mktime(0, 0, 0, $month_number,$dy,$year));
							$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
							if($i==6)
							{
									echo  '<td colspan="2" >';
									echo  '<table class="table table-condensed" >';
									echo  "<tr>";
								if($i < $first_week_day)
								{
									echo  '<td class="text-right " >';
								}else{
									$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
									$td_bg_color=$bg_close_color;
									echo  '<td bgcolor="'.$td_bg_color.'" class="  text-right">&nbsp;';
									echo  '</td>';
									echo  '<td bgcolor="'.$td_bg_color.'" class="  text-right">&nbsp;';
									if($i == $first_week_day)
									{
										$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
									}
									echo  $eff_day.'</b>&nbsp;';
								}
								echo  '</td></tr>';
								echo  '<tr>';
								if($i < $first_week_day)
								{									
									echo  '<td >&nbsp';
									echo  '</td>';
									echo  '<td >&nbsp;';
									echo  '<div id="month_1">';
									echo  '<div id="month_2">';
									echo  '<div id="month_3">';	
									echo  '</div>';
									echo  '</div>';
									echo  '</div>';		
								}else{									
									$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
									$td_bg_color=$bg_close_color;
									echo  '<td bgcolor="'.$td_bg_color.'">&nbsp';
									echo  '</td>';
									echo  '<td bgcolor="'.$td_bg_color.'">';									
									echo  '<div id="month_1">';
									echo  '<div id="month_2">';
									echo  '<div id="month_3">';	
									echo  '&nbsp;</div>';
									echo  '</div>';
									echo  '</div>';	
								}
								echo  '</td>';	
								echo  '</tr>';	
								$day=$day+1;
								$i=$i+1;
								$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$dy+1,$year));
								$eff_day=date("j",mktime(0, 0, 0, $month_number,$dy+1,$year));
								$eff_wday=date("w",mktime(0, 0, 0, $month_number,$dy+1,$year));
								$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;

								echo  '<tr><td colspan="2" class="calweeend">';
								if($i < $first_week_day)
								{
									//echo  '<td bgcolor="#FEF4BC" class="text-right "  >&nbsp;';
								}else{
								$wk++;
								$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
								$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
								$td_bg_color=$bg_close_color;

								$ret_data = adminSchTmpData($eff_date_add,$df_pro);
								if($i == $first_week_day)
								{
									$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
								}
								
								$clickFunction = ' onClick="javascript:change_template(\''.$day.$ex2.'\',\''.$eff_day.'_'.$wk.'\');"';							
								
								$checkBox="<div class=checkbox><input type='checkbox' onClick='change_template(this,\"$eff_day\");' id='$eff_day' value='".$eff_day."_".$wk."' name='idays[]' class=><label for='$eff_day'></label></div>";
								
								echo'<div class="row"  id="'.$day.$ex2.'"'.$clickFunction.' bgcolor="'.$td_bg_color.'">
									<div class="col-sm-6 text-left">'.$checkBox.'</div>
									<div class="col-sm-6 text-right cal_date" onClick="change_template(this,\''.$eff_day.'_'.$wk.'\');">'.$eff_day.'</div>
									<div class="col-sm-12 applied_templates">'.implode('<br>',(array)$ret_data).'</div>
								</div>';
									
								}			
								
								
								
								if($i < $first_week_day)
								{
									//echo  '<td bgcolor="#FEF4BC" rowspan="2" colspan="2"> &nbsp;';
								}else{
									$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
									$td_bg_color=$bg_close_color;
									echo  '<div id="'.$eff_day.$ex3.'">';
									echo  '<div id="month_1">';
									echo  '<div id="month_2">';
									echo  '<div id="month_3">';	
									echo   implode('<br>',(array)adminSchTmpData($get_cur_date,$df_pro));
									echo  '&nbsp;</div>';
									echo  '</div>';
									echo  '</div>';	
								}								
								echo  '</div>';
								echo '</td></tr>';
								echo  '</table>';
								echo  '</td>';
								echo  '</tr>';								
								$fwday=$fwday-1;									
							}else{
								echo  '<td style="border-right:1px inset #cccccc;">';
								echo  '<table style="width:100%" >';
								echo  '<tr><td class="text-right">';													
								echo  '</td>';
								echo  '</tr></table></td>';
							}
						}
						$firstweek=false;
					}
					if($fwday == 0)
					{
						echo  '<tr>';
					}	
					if($fwday == 5)
					{
							$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
							$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
							$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
							echo '<td colspan="0" class="calweeend">';
							
							if(($day==1)||($day == ($last_day+1)))
							{
								$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
							}			
							$ret_data = adminSchTmpData($eff_date_add,$df_pro);
							$clickFunction = ' onClick="change_template(\''.$day.$ex1.'\',\''.$eff_day.'_'.$wk.'\');"';
							if($day > $last_day){
								$eff_day = '';
								$ret_data = array();
								$clickFunction = '';
								$get_week_row_no_t = ceil($day/7);
								if($get_week_row_no_t == 5)
								{
									$week_row_val_t = $week_tname_arr[$eff_day_d];
									$eff_day = $get_week_row_no_t.'<sup>th</sup>';
									$wrdata = $get_week_row_no_t.'|'.$week_row_val_t;
									$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$last_day,$year));
									$ret_data = adminSchTmpData($eff_date_add,$df_pro,$wrdata);	
									$clickFunction = ' onClick="change_template(\''.$day.$ex1.'\',\''.$last_day.'_'.$wk.'\',\''.$wrdata.'\');" ';
								}
							}
							if($day == $thedate){
								//$bgColor = 'FFCC66';
								$hidden_bg = $day.''.$ex1;
							}
							else{
								//$bgColor = 'ffeccc';
							}
							$checkBox = '';
							if($eff_day){
								$checkBox='<div class="checkbox"><input id="checkbox_'.$day.$ex1.'" class="text_10" type="checkbox"  name="dateSelect[]" value="'.$day.'"><label for="checkbox_'.$day.$ex1.'"></label></div>';
							}
							echo '
										<div class="row"  id="'.$day.$ex1.'"'.$clickFunction.' >
											<div class="col-sm-6 text-left">'.$checkBox.'</div>
											<div class="col-sm-6 text-right cal_date">'.$eff_day.'</div>
											<div class="col-sm-12 applied_templates">'.implode('<br>',(array)$ret_data).'</div>
										</div>
									
								';
						$day=$day+1;
						$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
						$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
						$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
						$wk++;
						$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
						$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
						if(($day==1)||($day == ($last_day+1)))
						{
							$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));
						}
						$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
						$ret_data = adminSchTmpData($eff_date_add,$df_pro);
						$clickFunction = ' onClick="change_template(\''.$day.$ex2.'\',\''.$eff_day.'_'.$wk.'\');"';
						if($day > $last_day){
							$eff_day = '';
							$ret_data = array();
							$clickFunction = '';
							$get_week_row_no_t = ceil($day/7);
							if($get_week_row_no_t == 5)
							{
								$week_row_val_t = $week_tname_arr[$eff_day_d];
								$eff_day = $get_week_row_no_t.'<sup>th</sup>';
								$wrdata = $get_week_row_no_t.'|'.$week_row_val_t;
								$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$last_day,$year));
								$ret_data = adminSchTmpData($eff_date_add,$df_pro,$wrdata);	
								$clickFunction = ' onClick="change_template(\''.$day.$ex2.'\',\''.$last_day.'_'.$wk.'\',\''.$wrdata.'\');" ';
							}
						}
						if($day == $thedate){
							//$bgColor = 'FFCC66';
							$hidden_bg = $day.''.$ex2;
						}
						else{
							//$bgColor = 'ffeccc';
						}
						$checkBox = '';
						if($eff_day){
							$checkBox='<div class="checkbox"><input id="checkbox_'.$day.$ex2.'" type="checkbox"  name="dateSelect[]" value="'.$day.'"><label for="checkbox_'.$day.$ex2.'"></label></div>';
						}
						echo '
							
							<div class="row " id="'.$day.$ex2.'"'.$clickFunction.'>
								<div class="col-sm-6 text-left">'.$checkBox.'</div>
								<div class="col-sm-6 text-right cal_date">'.$eff_day.'</div>
								<div class="col-sm-12 applied_templates">'.implode('<br>',(array)$ret_data).'</div>
							</div>
							';
						//--- end 
					}else{
						$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$day,$year));
						$eff_day=date("j",mktime(0, 0, 0, $month_number,$day,$year));
						$eff_wday=date("w",mktime(0, 0, 0, $month_number,$day,$year));
						$eff_day_arr=$wk."_".$eff_wday."_".$eff_day;
						$eff_day_d=date("D",mktime(0, 0, 0, $month_number,$day,$year));
						if(($day==1)||($day == ($last_day+1)))
						{
							$eff_month=date("F",mktime(0, 0, 0, $month_number,$day,$year));							
						}							
						//echo  $eff_date_add.','.$df_pro;				
						$ret_data = adminSchTmpData($eff_date_add,$df_pro);
						$clickFunction = ' onClick="change_template(\''.$day.$ex3.'\',\''.$eff_day.'_'.$wk.'\');"';
						if($day > $last_day){
							$eff_day = '';
							$ret_data = array();
							$clickFunction = '';
							$get_week_row_no_t = ceil($day/7);
							if($get_week_row_no_t == 5)
							{
								$week_row_val_t = $week_tname_arr[$eff_day_d];
								$eff_day = $get_week_row_no_t.'<sup>th</sup>';
								$wrdata = $get_week_row_no_t.'|'.$week_row_val_t;
								$eff_date_add=date("Y-m-d",mktime(0, 0, 0, $month_number,$last_day,$year));
								$ret_data = adminSchTmpData($eff_date_add,$df_pro,$wrdata);	
								$clickFunction = ' onClick="change_template(\''.$day.$ex3.'\',\''.$last_day.'_'.$wk.'\',\''.$wrdata.'\');" ';
							}							
						}
						if($day == $thedate){
							$bgColor = 'FFCC66';
							$hidden_bg = $day.''.$ex3;
						}
						else{
							$bgColor = 'ffeccc';
						}
						echo  '<td id="'.$day.$ex3.'"'.$clickFunction.' style="border-right:1px inset #cccccc; cursor:pointer; background:#'.$bgColor.'">';
							
								if($eff_day<>""){
								$checkBox='<div class="checkbox"><input id="checkbox_'.$day.$ex3.'" type="checkbox" name="dateSelect[]" value="'.$day.'"><label for="checkbox_'.$day.$ex3.'"></label></div>';
								}else{
								$checkBox="";
								}
								
								echo '
									<div class="row">	
										<div class="col-sm-6 text-left">'.$checkBox.'</div>
										<div class="col-sm-6 text-right cal_date">'.$eff_day.'</div>
										<div class="col-sm-12 applied_templates">'.implode('<br>',(array)$ret_data).'</div>
									</div>
								';
						echo  '</td>';
					}																		
					$fwday++;
					$fwday = $fwday % 6;											
					$day++;
				}
				?>		
			</table>
		</td>
	</tr>	
<input type="hidden" name="highlight_td" id="highlight_td" value="<?php echo  $hidden_bg; ?>" />
</table>
</div>