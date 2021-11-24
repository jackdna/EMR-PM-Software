<?php
/*
 * File: payments_report.php
 * Coded in PHP7
 * Purpose: Show receipts
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
ini_set("memory_limit","3072M");  
$user_type_arr['type']=array("1","3","5","13");
$ins_data_arr=ins_comp_fun();
$fac_data_arr=pos_fun();
$cpt_data_arr=cpt_fun();
$dept_data_arr=department_fun();
$users_data_arr=users_fun($user_type_arr);
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
                                    <select class="form-control multi_drop" id="ins_drop" name="ins_drop[]" multiple="multiple">
                                      <?php
                                        foreach($ins_data_arr['ins_name_by_id'] as $key=>$val){
                                            $sel="";
                                            if(in_array($key,$_REQUEST['ins_drop'])){
                                                $sel="selected";
                                            }
                                            echo "<option value='$key' $sel>$val</option>";
                                        }
                                      ?>
                                    </select>	
                                    <small> Ins. Company </small>
                                </div>	     
                                <div class="col-md-2 col-lg-2 col-sm-6 col-xs-12 padding_adj_big">
                                    <select class="form-control multi_drop" id="pos_fac_drop" name="pos_fac_drop[]" multiple="multiple">
                                     <?php
                                        foreach($fac_data_arr['pos_name_by_id'] as $key=>$val){
                                            $sel="";
                                            if(in_array($key,$_REQUEST['pos_fac_drop'])){
                                                $sel="selected";
                                            }
                                            echo "<option value='$key' $sel>$val</option>";
                                        }
                                      ?>
                                    </select>	
                                    <small> POS Facility  </small>
                                </div>
                                <div class="clearfix visible-sm"></div>
                            <div class="col-md-2 col-lg-2 col-sm-6 col-xs-12 padding_adj_big">
                                <select class="form-control multi_drop" id="dept_drop" name="dept_drop[]" multiple="multiple">
                                <?php
                                    foreach($dept_data_arr['dept_desc_by_id'] as $key=>$val){
                                        $sel="";
                                        if(in_array($key,$_REQUEST['dept_drop'])){
                                            $sel="selected";
                                        }
                                        echo "<option value='$key' $sel>$val</option>";
                                    }
                                  ?>
                                </select>	
                                <small> Department  </small>
                            </div>
                            <div class="col-md-2 col-lg-2 col-sm-6 col-xs-12 padding_adj_big">
                                <select class="form-control multi_drop" id="cpt_drop" name="cpt_drop[]" multiple="multiple">
                                <?php
                                    foreach($cpt_data_arr['cpt_name_by_id'] as $key=>$val){
                                        $sel="";
                                        if(in_array($key,$_REQUEST['cpt_drop'])){
                                            $sel="selected";
                                        }
                                        echo "<option value='$key' $sel>$val</option>";
                                    }
                                  ?>
                                </select>	
                                <small> CPT Code  </small>
                            </div>   
                       </div>
                    </div>
                    <div class="clearfix visible-sm"></div>	 
                    <?php if($_REQUEST['date_range_for']==""){$_REQUEST['date_range_for']='date_of_payment';}?>	    
                    <div class="col-lg-4 col-sm-12 col-md-5 col-xs-12 padding_adj_big for_adj_in_lg">
                        <div class="row margin_adj_big">
                            <div class="col-md-5 col-lg-6 col-sm-6 col-xs-12 padding_adj_big first_lg_div">
                                <select class="form-control multi_drop" id="date_range_for" name="date_range_for">
                                     <option value="date_of_service"  <?php if($_REQUEST['date_range_for']=="date_of_service"){echo "selected";} ?>>Date Of Service</option>
                                     <option value="date_of_payment" <?php if($_REQUEST['date_range_for']=="date_of_payment"){echo "selected";} ?>>Payment Date</option>
                                     <option value="transaction_date" <?php if($_REQUEST['date_range_for']=="transaction_date"){echo "selected";} ?>>Transaction Date</option>
                                </select>	
                                <small> Date Range For  </small>
                            </div>
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
                    
                    <!--<div class="col-md-1 col-lg-1 col-xs-12 col-sm-12 text-center">
                        <input type="submit" name="srh_button" id="srh_button" class="rob_btn btn_custom btn_sign_out btn-lg" value="Search">
                    </div>-->
                </div>
            </form>
       </div>         
    </div>
</div>

	<?php
		if($_REQUEST['srh_button']!=""){
			
			$inc_arr=array("pos","department","cpt","charges","users","insurance","pt_pre_payments");
			$inc_arr['cond']=$_REQUEST;
			$charges_arr=enc_payment_fun($inc_arr);
			
			$payment_org_fac_js_arr=json_encode($charges_arr['fac_org_pay']);
			$payment_fac_js_arr=json_encode($charges_arr['fac_pay']);
			$payment_dept_js_arr=json_encode($charges_arr['dept_pay']);
			$payment_ins_grp_js_arr=json_encode($charges_arr['tot_ins_grp_pay']);
			$payment_ins_js_arr=json_encode($charges_arr['tot_ins_pay']);

			$tot_payment_fac=numberFormat(array_sum($charges_arr['tot_pay']),2,'yes');
			
			$top_cpt_rcpt_js_arr=json_encode($charges_arr['top_cpt_rcpt']);
			$all_cpt_rcpt_js_arr=json_encode($charges_arr['all_cpt_rcpt']);
			$top_phy_rcpt_js_arr=json_encode($charges_arr['tot_phy_pay']);
			
			$line_chart_dept_data=line_chart('quarter',$charges_arr['tot_pay_by_quarter_dept']);
			$line_pay_graph_var_arr_js_dept=json_encode($line_chart_dept_data['line_pay_graph_var_detail']);
			$line_payment_tot_arr_js_dept=json_encode($line_chart_dept_data['line_payment_tot_detail']);	
			
			//print_r($top_phy_rcpt_js_arr);
		
	?>
    <?php if(array_sum($charges_arr['tot_pay'])!=0){?>
        <div class="middle_inner scrollable_yes">
            <Div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                <!--<Div class="middle_head">
                     <span>  Total Receipts - <?php echo $tot_payment_fac; ?> </span>
                </Div>-->
                <div class="ledger_wrap">
                     <div class="panel price panel-green margin_adjust_panel_ledger">
                        <div class="panel-body text-center adjustable_ipad">
                            <ul class="list-group nav nav-justified">
                            	<li class="list-group-item"> 
                                    <div class="full_width2"> <span>   Total Receipts  </span> </div>     
                                    <div class="full_width2"> <span class="high">   <?php echo $tot_payment_fac; ?>	</span> </div>
                                </li>
                            	<?php
								ksort($charges_arr['tot_pay_paid_by']);
								foreach($charges_arr['tot_pay_paid_by'] as $key=>$val){
									$tot_pay_paid_by_arr=array();
                                	$tot_pay_paid_by_arr[]=array_sum($charges_arr['tot_pay_paid_by'][$key]);
									if($key=="Patient"){
										$tot_pay_paid_by_arr[]=array_sum($charges_arr['tot_pt_un_post_pre_amt']);
									}
									
								?>
                                    <li class="list-group-item"> 
                                        <div class="full_width2"> <span>   Paid by <?php echo $key; ?>  </span> </div>     
                                        <div class="full_width2"> <span class="high">   <?php echo numberFormat(array_sum($tot_pay_paid_by_arr),2,'yes'); ?>	</span> </div> 
                                    </li>
                              <?php } ?>
                              <li class="list-group-item" style="background:#eee;"> 
                                   <div class="full_width2"> <span>   Pre Payments</span> </div>     
                                   <div class="full_width2"> <span class="high">   <?php echo numberFormat(array_sum($charges_arr['tot_pt_un_post_pre_amt']),2,'yes'); ?>	</span> </div> 
                              </li>
                            </ul>
                    </div>
                 </div>
           	</div>
            </Div>
            <Div class="clearfix margin_clear"></Div>
            <!--<Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Facility (Originating)</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <Div class="sub_wrap" id="chart_org_fac_rcpt_div" style="width: 100%; height: 400px;">
                   
                </Div>		
             </Div>-->
             <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Facility</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <div class="rel_chart_div">
                    <Div class="sub_wrap" id="chart_fac_rcpt_div" style="width: 100%; height: 400px;">
                       
                    </Div>
                </div>		
             </Div>
            <!-- <Div class="clearfix margin_clear"></Div>-->
             <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Department</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <div class="rel_chart_div">
                    <Div class="sub_wrap" id="chart_dept_rcpt_div" style="width: 100%; height: 400px;">
                       
                    </Div>
                </div>    		
             </Div>
			 <Div class="clearfix margin_clear"></Div>
             <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Top 10 CPT Codes by Total Receipts</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <Div class="sub_wrap" id="chart_all_cpt_pay_div" style="width: 100%; height: 400px;">
                
                </Div>
            </Div> 
             <div class="clearfix hidden-lg"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Physician</span>
                </div>	
                <div class="clearfix margin_line gradient"></div>
                <Div class="sub_wrap" id="chart_all_phy_pay_div" style="width: 100%; min-height: 400px;">
                </Div>	
            </div> 
             <Div class="clearfix margin_clear"></Div>
             <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Insurance Group</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <div class="rel_chart_div">
                    <Div class="sub_wrap" id="chart_ins_pay_div" style="width: 100%; height: 400px;">
                    </Div>		
                </div>    
             </Div>
             <Div class="col-md-6 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Receipts by Method of Payment</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <Div class="sub_wrap" style="width: 60%;">
                   <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered  table-condensed cf  table-striped adj_pad_table">
                        <tbody>
                            <tr>
                                <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">Method of Payment</th>
                                <th class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6 text-center">Total Receipts</th>
                            </tr>
                            <?php 
                                foreach($charges_arr['tot_pay_method'] as $key=>$val){
                                $tot_pay_method_arr[]=array_sum($charges_arr['tot_pay_method'][$key]);
                            ?>
                            <tr>
                                <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                  <strong> <?php echo $key; ?></strong>
                                </td>
                                <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6 text-right">
                                    <strong>  <?php echo numberFormat(array_sum($charges_arr['tot_pay_method'][$key]),2,'yes'); ?></strong>
                                </td>
                            </tr>
                            <?php 
								if($key=="Credit Card"){
									foreach($charges_arr['tot_pay_cc_method']['Credit Card'] as $key2=>$val2){
										$credit_card_company="";
										if($key2=="AX"){
                                            $credit_card_company="American Express";
                                        }else if($key2=="Dis"){
                                            $credit_card_company="Discover";
                                        }else if($key2=="MC"){
                                            $credit_card_company="Master Card";
                                        }else if($key2=="Visa"){
                                            $credit_card_company="Visa";
                                        }else if($key2=="Care Credit"){
                                            $credit_card_company="Care Credit";
                                        }else{
											$credit_card_company="Other";
										}
							?>
                            	<tr>
                                    <td class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                       <span style="padding-left:30px;"><?php echo $credit_card_company; ?></span>
                                    </td>
                                    <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6 text-right">
                                       <?php echo numberFormat(array_sum($charges_arr['tot_pay_cc_method']['Credit Card'][$key2]),2,'yes'); ?>
                                    </td>
                                </tr>
							<?php 	}
								}
							} 
							?>
                            <tr style="border-top:2px solid #009933;border-bottom:2px solid #009933;">
                                <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6">
                                   
                                </td>
                                <td class="text-left col-md-6 col-lg-6 col-sm-6 col-xs-6 text-right">
                                   <strong><?php echo numberFormat(array_sum($tot_pay_method_arr),2,'yes'); ?></strong>
                                </td>
                            </tr>
                        </tbody>
                 </table>
                </Div>		
             </Div>
              <Div class="clearfix margin_clear"></Div>
             <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Top 10 CPT Codes by Total Receipts</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <Div class="sub_wrap" id="chart_top_cpt_rcpt_div" style="width: 100%; height: 400px;">
                
                </Div>
            </Div> 
             <div class="clearfix hidden-lg"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Department</span>
                </div>	
                <div class="clearfix margin_line gradient"></div>
                <Div class="sub_wrap" id="chart_quarter_dept_rcpt_div" style="width: 100%; min-height: 400px;">
                </Div>	
            </div> 
			
			<Div class="clearfix margin_clear"></Div>
             <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Insurance</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <Div class="sub_wrap" id="chart_ins_comp_pay_div" style="width: 100%; height: 400px;">
                
                </Div>
            </Div> 
             <div class="clearfix hidden-lg"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Receipts by Physician</span>
                </div>	
                <div class="clearfix margin_line gradient"></div>
                <Div class="sub_wrap" id="chart_phy_pay_div" style="width: 100%; min-height: 400px;">
                </Div>	
            </div> 
			
			
        </div>     
		<script type="text/javascript">
			//pie_chart('pie','chart_org_fac_rcpt_div','<?php echo $payment_org_fac_js_arr; ?>');
            pie_chart('pie','chart_fac_rcpt_div','<?php echo $payment_fac_js_arr; ?>');
            pie_chart('pie','chart_dept_rcpt_div','<?php echo $payment_dept_js_arr; ?>');
			pie_chart('pie','chart_ins_pay_div','<?php echo $payment_ins_grp_js_arr; ?>');
			bar_chart('serial','chart_top_cpt_rcpt_div','<?php echo $top_cpt_rcpt_js_arr; ?>');
			line_chart('serial','chart_quarter_dept_rcpt_div','<?php echo $line_payment_tot_arr_js_dept; ?>','<?php echo $line_pay_graph_var_arr_js_dept; ?>');
			pie_chart('pie','chart_ins_comp_pay_div','<?php echo $payment_ins_js_arr; ?>');
			bar_chart('serial','chart_phy_pay_div','<?php echo $top_phy_rcpt_js_arr; ?>');
			pie_chart('pie','chart_all_cpt_pay_div','<?php echo $top_cpt_rcpt_js_arr; ?>');
			pie_chart('pie','chart_all_phy_pay_div','<?php echo $top_phy_rcpt_js_arr; ?>');
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
