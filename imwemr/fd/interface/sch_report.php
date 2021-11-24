<?php
/*
 * File: sch_report.php
 * Coded in PHP7
 * Purpose: Show Appointments
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
ini_set("memory_limit","3072M"); 
$user_type_arr['type']=array("1");
$proc_cond_arr['active_status']=array("del");
$appt_status_arr=array("0"=>"New Appointment","2"=>"Chart Pulled","3"=>"No Show","6"=>"Left Without Visit","7"=>"Insurance/Finance Issue",
"13"=>"Check-In","11"=>"Check-Out","17"=>"Confirm","18"=>"Cancel","23"=>"Not Confirmed","100"=>"Waiting for Surgery",
"101"=>"Scheduled for Surgery","200"=>"Room Assigned","201"=>"To-Do-Rescheduled","202"=>"Rescheduled","203"=>"Deleted","271"=>"First Available");
$users_data_arr=users_fun($user_type_arr);
$fac_data_arr=facility_fun();
$sch_proc_data_arr=sch_proc_fun($proc_cond_arr);
?>
<script type="text/javascript">
	var start_date_js = '<?php echo xss_rem($_REQUEST['start_date']); ?>';
	var end_date_js =  '<?php echo xss_rem($_REQUEST['end_date']); ?>';
	$(document).ready(function() {
		if(start_date_js!=''){
			var send_start_date_js = moment(start_date_js,'MM-DD-YYYY');
			var send_end_date_js =  moment(end_date_js,'MM-DD-YYYY');
			cb(send_start_date_js,send_end_date_js);
		}
	});
	function submit_form(){
		var returnVal = validDateCheck("start_date","end_date");
		if(returnVal == true){
			alert('Start date should be less than End date.');	
			document.getElementById("start_date").select();		
			return false;
		}
		show_loading_image('show');
	 	document.getElementById('search_frm').submit();
	}
</script>
<div class="container-fluid padding_0">
    <div class="filter_area bordered_div_inside">
		<div class="inside_wrap_filter">	    	
            <form method="post" name="search_frm" id="search_frm" action="index.php" onsubmit="return submit_form();">
            <input type="hidden" name="tab_name" id="tab_name" value="<?php echo $_REQUEST['tab_name']; ?>" />
            <input type="hidden" name="srh_button" id="srh_button" value="srh_button" />
                <div class="row">
                    <div class="col-md-7 col-lg-8 col-xs-12 col-sm-12 padding_adj_big">
                        <div class="row margin_adj_big">
                              <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 padding_adj_big">
                                     <select class="form-control multi_drop" id="appt_drop" name="appt_drop[]" multiple="multiple">
									  <?php
                                        foreach($appt_status_arr as $key=>$val){
                                            $sel="";
                                            if(in_array($key,$_REQUEST['appt_drop'])){
                                                $sel="selected";
                                            }
                                            echo "<option value='$key' $sel>$val</option>";
                                        }
                                      ?>
                                    </select>	
                                    <small> Appointment Status </small>
                                </div>	     
                                <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 padding_adj_big">
                                      <select class="form-control multi_drop" id="fac_drop" name="fac_drop[]" multiple="multiple">
										 <?php
                                            foreach($fac_data_arr['fac_name_by_id'] as $key=>$val){
                                                $sel="";
                                                if(in_array($key,$_REQUEST['fac_drop'])){
                                                    $sel="selected";
                                                }
                                                echo "<option value='$key' $sel>$val</option>";
                                            }
                                          ?>
                                        </select>	
                                        <small> Facility  </small>
                                </div>
                                <div class="clearfix visible-sm"></div>
                            <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 padding_adj_big">
                                <select class="form-control multi_drop" id="users_drop" name="users_drop[]" multiple="multiple">
								 <?php
                                    foreach($users_data_arr['user_name_by_id'] as $key=>$val){
                                        $sel="";
                                        $txt_color="";
                                        if(in_array($key,$_REQUEST['users_drop'])){
                                            $sel="selected";
                                        }
                                        if($users_data_arr['user_del_status_by_id'][$key]>0){
                                           $txt_color="class='text-danger'";
                                        }
                                        echo "<option value='$key' $sel $txt_color>$val</option>";
                                    }
                                  ?>
                                </select>	
                                <small> Physician  </small>
                            </div>
                            <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 padding_adj_big">
                                <select class="form-control multi_drop" id="sch_proc_drop" name="sch_proc_drop[]" multiple="multiple">
								 <?php
                                    foreach($sch_proc_data_arr['proc_name_by_id'] as $key=>$val){
                                        $sel="";
                                        if(in_array($key,$_REQUEST['sch_proc_drop'])){
                                            $sel="selected";
                                        }
                                        echo "<option value='$key' $sel>$val</option>";
                                    }
                                  ?>
                                </select>	
                                <small> Procedure  </small>
                            </div>   
                       </div>
                    </div>	
                    <div class="clearfix visible-sm"></div>	     
                    <div class="col-lg-4 col-sm-12 col-md-5 col-xs-12 padding_adj_big for_adj_in_lg">
                        <div class="row margin_adj_big">
                            <!--<div class="col-md-5 col-lg-6 col-sm-6 col-xs-12 padding_adj_big first_lg_div">
                                <select class="form-control multi_drop" id="date_range_for" name="date_range_for">
                                     <option value="date_of_service"  <?php if($_REQUEST['date_range_for']=="date_of_service"){echo "selected";} ?>>Date Of Service</option>
                                     <option value="date_of_payment" <?php if($_REQUEST['date_range_for']=="date_of_payment"){echo "selected";} ?>>Payment Date</option>
                                     <option value="transaction_date" <?php if($_REQUEST['date_range_for']=="transaction_date"){echo "selected";} ?>>Transaction Date</option>
                                </select>	
                                <small> Date Range For  </small>
                            </div>-->
                            <div class="col-md-7 col-lg-6 col-sm-6 col-xs-12 padding_adj_big second_lg_div">
                             <div class="pull-right date-pick" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <span class="fa fa-calendar"></span>&nbsp;
                                    <span id="date_display"></span>
                                </div>
                                 <input type="hidden" class="form-control" name="start_date" id="start_date" value="<?php echo xss_rem($_REQUEST['start_date']); ?>"/>
                                 <input type="hidden" class="form-control" name="end_date" id="end_date" value="<?php echo xss_rem($_REQUEST['end_date']); ?>"/>	
                                 <small> Date Range </small>
                            </div>
                        </div>                
                    </div>	
                </div>
            </form>
       </div>         
    </div>
</div>
	<?php
		if($_REQUEST['srh_button']!=""){
			
			$inc_arr=array("sch_pay_chrg");
			$inc_arr['cond']=$_REQUEST;
			$appt_data_arr=appointment_fun($inc_arr);
			//print_r($appt_data_arr);
			$appt_status_detail_cnt=0;
			foreach($appt_status_arr as $key=>$val){
				$total_appt_arr[]=count($appt_data_arr['appt_status_detail'][$key]);
				
				if($val=="No Show"){
					$total_no_show_appt=count($appt_data_arr['appt_status_detail'][$key]);
				}
				if($val=="Cancel"){
					$total_cancel_appt=count($appt_data_arr['appt_status_detail'][$key]);
				}
				if($val=="Check-In" || $val=="Check-Out"){
					$total_pt_seen_appt=count($appt_data_arr['appt_status_detail'][$key]);
					$k++;
				}
				
				$appt_status_detail_cnt=$appt_status_detail_cnt+count($appt_data_arr['appt_status_detail'][$key]);
			}
			
			$k=0;
			foreach($sch_proc_data_arr['proc_name_by_id'] as $key=>$val){
				if((count($appt_data_arr['appt_proc_status_detail'][$key][13])>0 || count($appt_data_arr['appt_proc_status_detail'][$key][11])>0) && $k<10){
					$appt_total_seen_data_chart[$k]["kee"]=substr($val,0,13);
					$appt_total_seen_data_chart[$k]["val"]=count($appt_data_arr['appt_proc_status_detail'][$key][13])+count($appt_data_arr['appt_proc_status_detail'][$key][11]);
					$k++;
				}
			}
			
			/*$appt_status_data_chart[0]["kee"]="Total Appointments";
			$appt_status_data_chart[0]["val"]=$appt_status_detail_cnt;*/
			
			$appt_status_data_chart[0]["kee"]="Patient Seen";
			$appt_status_data_chart[0]["val"]=$total_pt_seen_appt;
			
			$appt_status_data_chart[1]["kee"]="No Shows";
			$appt_status_data_chart[1]["val"]=$total_no_show_appt;
			
			$appt_status_data_chart[2]["kee"]="Cancel/Rescheduled/Other";
			$appt_status_data_chart[2]["val"]=array_sum($total_appt_arr)-($total_pt_seen_appt+$total_no_show_appt);
				
			//print_r($appt_status_data_chart);
			$appt_status_data_js_chart=json_encode($appt_status_data_chart);
			$appt_total_seen_data_js_chart=json_encode($appt_total_seen_data_chart);
			if(count($appt_data_arr['appt_status_detail'])>0){
	?>
                <div class="middle_inner scrollable_yes">
                
                    <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
                        <div class="ledger_wrap">
                                 <div class="panel price panel-green margin_adjust_panel_ledger">
                                    <div class="panel-body text-center adjustable_ipad">
                                        <ul class="list-group nav nav-justified">
                                            <li class="list-group-item"> 
                                                <div class="full_width2"> <span>   Total Appointments  </span> </div>     
                                                <div class="full_width2"> <span class="high">   <?php echo array_sum($total_appt_arr); ?>	</span> </div>                                                                           
                                            </li>
                                             <li class="list-group-item"> 
                                                <div class="full_width2"> <span>   Patient Seen  </span> </div>     
                                                <div class="full_width2"> <span class="high">   <?php echo $total_pt_seen_appt; ?>	</span> </div>                                                                           
                                            </li>
                                             <li class="list-group-item"> 
                                                <div class="full_width2"> <span>   No Shows  </span> </div>     
                                                <div class="full_width2"> <span class="high">   <?php echo $total_no_show_appt; ?>	</span> </div>                                                                           
                                            </li>
                                            <li class="list-group-item"> 
                                                <div class="full_width2"> <span>  Cancel/Rescheduled/Other  </span> </div>     
                                                <div class="full_width2"> <span class="high">   <?php echo array_sum($total_appt_arr)-($total_pt_seen_appt+$total_no_show_appt); ?>	</span> </div>                                                                           
                                            </li>
                                        </ul>
                                </div>
                             </div>
                        </div> <!-- Ledger Wrap -->
                    </div>
                
                     <Div class="clearfix margin_clear"></Div>
                      <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                        <div class="middle_sub_head text-left">
                            <span>Appointment Status Summary</span>
                        </div>	
                        <Div class="clearfix margin_line gradient"></Div>
                        <div class="rel_chart_div">
                        	<Div class="sub_wrap" id="chart_status_appt_div" style="width: 100%; min-height: 400px;">
                               <!--<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped">
                                    <tbody>
                                        <tr>
                                            <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">Appointment Status</th>
                                            <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">Count</th>
                                        </tr>
                                        <?php 
                                            /*foreach($appt_status_arr as $key=>$val){
                                                if(count($appt_data_arr['appt_status_detail'][$key])>0){
                                                $tot_pay_method_arr[]=count($appt_data_arr['appt_status_detail'][$key]);
                                        ?>
                                        <tr>
                                            <td class="text-left col-md-8 col-lg-8 col-sm-8 col-xs-8">
                                               <?php echo $val; ?>
                                            </td>
                                            <td class="text-left col-md-4 col-lg-4 col-sm-4 col-xs-4 text-right">
                                               <?php echo count($appt_data_arr['appt_status_detail'][$key]); ?>
                                            </td>
                                        </tr>
                                        <?php }}*/ ?>
                                    </tbody>
                             </table>-->
                       		</Div>	
                        </div> 	
                     </Div>
                     <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                        <div class="middle_sub_head text-left">
                            <span>Total Seen</span>
                        </div>	
                        <Div class="clearfix margin_line gradient"></Div>
                        <div class="rel_chart_div">
                        	<Div class="sub_wrap" id="chart_seen_appt_div" style="width: 100%; min-height: 400px;">
                               
                       		</Div>	
                        </div> 	
                     </Div>
					 
					 <Div class="clearfix margin_clear"></Div>
					 <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
						<div class="middle_sub_head text-left">
							<span>Total Charges by Procedure</span>
						</div>	
						<Div class="clearfix margin_line gradient"></Div>
						<Div class="sub_wrap" id="chart_sch_cpt_chrg_div" style="width: 100%; height: 400px;">
						
						</Div>
					</Div> 
					 <div class="clearfix hidden-lg"></div>
					<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
						<div class="middle_sub_head text-left">
							<span>Total Receipts by Procedure</span>
						</div>	
						<div class="clearfix margin_line gradient"></div>
						<Div class="sub_wrap" id="chart_sch_cpt_rcpt_div" style="width: 100%; min-height: 400px;">
						</Div>	
					</div> 
					
                     <Div class="clearfix margin_clear"></Div>
                     
                     <Div class="col-md-12 col-lg-6 col-sm-12 col-xs-12">
                        <div class="middle_sub_head text-left">
                            <span>Procedure Charges/Payments</span>
                        </div>	
                        <Div class="clearfix margin_line gradient"></Div>
                        <Div class="sub_wrap" style="width: 100%;">
                           <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped adj_pad_table">
                                <tbody>
                                    <tr>
                                        <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Procedure</th>
                                        <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Charges</th>
                                        <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Payments</th>
                                    </tr>
                                     <?php 
									 	$hk=0;
                                        foreach($sch_proc_data_arr['proc_name_by_id'] as $key=>$val){
                                            if(array_sum($appt_data_arr['appt_proc_chrg'][$key])!=0 || array_sum($appt_data_arr['appt_proc_pay'][$key])!=0){
                                    ?>
                                    <tr>
                                        <td class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                           <?php echo $val; ?>
                                        </td>
                                        <td class="text-right col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                           <?php echo numberFormat(array_sum($appt_data_arr['appt_proc_chrg'][$key]),2,'yes'); ?>
                                        </td>
                                        <td class="text-right col-md-2 col-lg-2 col-sm-2 col-xs-2">
                                           <?php echo numberFormat(array_sum($appt_data_arr['appt_proc_pay'][$key]),2,'yes'); ?>
                                        </td>
                                    </tr>
                                    <?php 
											$sch_grand_tot_chrg_arr[]=array_sum($appt_data_arr['appt_proc_chrg'][$key]);
											$sch_grand_tot_pay_arr[]=array_sum($appt_data_arr['appt_proc_pay'][$key]);
											
											$sch_cpt_tot_chrg_arr[$hk]["kee"]=$val;
											$sch_cpt_tot_chrg_arr[$hk]["val"]=array_sum($appt_data_arr['appt_proc_chrg'][$key]);
											
											$sch_cpt_tot_pay_arr[$hk]["kee"]=$val;
											$sch_cpt_tot_pay_arr[$hk]["val"]=array_sum($appt_data_arr['appt_proc_pay'][$key]);
											
											$hk++;
										}
									} 
										$sch_cpt_tot_chrg_js_arr=json_encode($sch_cpt_tot_chrg_arr);
										$sch_cpt_tot_pay_js_arr=json_encode($sch_cpt_tot_pay_arr);
									if(array_sum($sch_grand_tot_chrg_arr)!=0 || array_sum($sch_grand_tot_pay_arr)!=0){
									?>
                                    <tr>
                                        <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2 text-right">Grand Total&nbsp;</th>
                                        <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2 text-right"><?php echo numberFormat(array_sum($sch_grand_tot_chrg_arr),2,'yes'); ?></th>
                                        <th class="text-left col-md-2 col-lg-2 col-sm-2 col-xs-2 text-right"><?php echo numberFormat(array_sum($sch_grand_tot_pay_arr),2,'yes'); ?></th>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                         </table>
                         </Div> 	
                     </Div>
                </div> 
                <script type="text/javascript">
					pie_chart('pie','chart_status_appt_div','<?php echo $appt_status_data_js_chart; ?>','');
					bar_chart('serial','chart_seen_appt_div','<?php echo $appt_total_seen_data_js_chart; ?>','','');
					pie_chart('pie','chart_sch_cpt_chrg_div','<?php echo $sch_cpt_tot_chrg_js_arr; ?>');
					pie_chart('pie','chart_sch_cpt_rcpt_div','<?php echo $sch_cpt_tot_pay_js_arr; ?>');
				</script> 
              <?php 
    		}else{
    	?>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12 text-center">
                <h3 class="alert alert-danger"> <span class="rob"> No record exists. </span></h3>
            </div>   
    <?php	
    		} 
		}
	?>
<script type="text/javascript">
	var ar = [["search","Search","submit_form();"]];
	$(document).ready(function() {
		top.btn_show("financial",ar);
	});
</script>