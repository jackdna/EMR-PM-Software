<?php
/*
 * File: ledger_report.php
 * Coded in PHP7
 * Purpose: Show charges and receipts
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
			
			$inc_arr=array("pos","department","cpt","users","payment_fun");
			$inc_arr['cond']=$_REQUEST;
			$charges_arr=enc_charges_fun($inc_arr);
			
			$tot_charges=array_sum($charges_arr['tot_chrg']);
			$tot_payment=array_sum($charges_arr['tot_pay']);
			
			$tot_wrt_payment=array_sum($charges_arr['tot_pay_wrt_by']);
			$tot_adj_payment=array_sum($charges_arr['tot_pay_adj_by']);
			
			$top_cpt_chrg_js_arr=json_encode($charges_arr['top_cpt_chrg']);
			$top_cpt_rcpt_js_arr=json_encode($charges_arr['top_cpt_rcpt']);
			$tot_balance=$tot_charges-($tot_payment+$tot_wrt_payment+$tot_adj_payment);
			$tot_pat_due=array_sum($charges_arr['chld_pat_due_arr']);
			
			$line_chart_dhrg_dept_data=line_chart('quarter',$charges_arr['tot_chrg_by_quarter_dept']);
			$line_chrg_graph_var_arr_js_dept=json_encode($line_chart_dhrg_dept_data['line_pay_graph_var_detail']);
			$line_chrg_tot_arr_js_dept=json_encode($line_chart_dhrg_dept_data['line_payment_tot_detail']);
			
			$line_chart_dept_data=line_chart('quarter',$charges_arr['tot_pay_by_quarter_dept']);
			$line_pay_graph_var_arr_js_dept=json_encode($line_chart_dept_data['line_pay_graph_var_detail']);
			$line_payment_tot_arr_js_dept=json_encode($line_chart_dept_data['line_payment_tot_detail']);
	?>
    <?php if($tot_charges!=0 || $tot_payment!=0){?>
	<div class="middle_inner scrollable_yes">
    	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        	<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 padding_0">
                <div class="abs_head_total_charges text-center">
                    <h3> <span class="rob"> Total Charges (<?php echo numberFormat($tot_charges,2,'yes'); ?>)  	</span></h3>
                </div>
            </div><!-- Heading -->
            <div class="row margin_adj_led">
      			<div class="full_width2 margin_adjust_panel_ledger">
                	<div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <table class="table-hover table-bordered  table-condensed cf table-striped">
                            <thead>
                                <tr>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Facility</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Total Charges</th>
                                </tr>
                             </thead>   
                             <tbody>
                                <?php
                                //print_r($charges_arr['fac_detail_chrg']);
                                $fac_data_arr['pos_name_by_id'][0]='Other';
                                foreach($fac_data_arr['pos_name_by_id'] as $key=>$val){
									if($charges_arr['fac_detail_chrg'][$key]!=0){
                                ?>
                                <tr>
                                    <td  class="text-left">  <?php echo $val; ?>  </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['fac_detail_chrg'][$key],2,'yes'); ?>      </td>	                                  
                                </tr>
                                <?php }} ?>
                               
                            </tbody>
                         </table>
                     </div> 
                     <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                         <table class="table-hover table-bordered  table-condensed cf table-striped">
                            <thead>
                                <tr>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Physician</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Total Charges</th>
                                </tr>
                             </thead>   
                             <tbody>
                                <?php
                                //print_r($payment_dept_arr);
                                $users_data_arr['user_name_by_id'][0]='Other';
                                foreach($users_data_arr['user_name_by_id'] as $key=>$val){
									if($charges_arr['phy_detail_chrg'][$key]!=0){
                                ?>
                                <tr>
                                    <td  class="text-left">  <?php echo $val; ?>  </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['phy_detail_chrg'][$key],2,'yes'); ?>      </td>	                                  
                                </tr>
                                <?php }} ?>
                               
                            </tbody>
                         </table>
                      </div>   
                </div>    
                <div class="clearfix margin_clear"></div>  
            </div>
             <!-- Ledger Wrap -->
        </div> <!-- Col-12 ends -->
        <div class="clearfix margin_clear"></div>
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
        	<div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 padding_0">
                <div class="abs_head_total_charges text-center">
                    <h3>  <span class="rob"> Total Receipts (<?php echo numberFormat($tot_payment,2,'yes'); ?>) </span> </h3>
                </div>
            </div><!-- Heading -->
            <div class="row margin_adj_led">
      			<div class="full_width2 margin_adjust_panel_ledger">
                	<div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                        <table class="table-hover table-bordered  table-condensed cf table-striped">
                            <thead>
                                <tr>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Facility</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Total Receipts</th>
                                </tr>
                             </thead>   
                             <tbody>
                                <?php
                                //print_r($charges_arr['fac_detail_chrg']);
                                $fac_data_arr['pos_name_by_id'][0]='Other';
                                foreach($fac_data_arr['pos_name_by_id'] as $key=>$val){
									if($charges_arr['tot_fac_rcpt'][$key]!=0){
                                ?>
                                <tr>
                                    <td  class="text-left">  <?php echo $val; ?>  </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['tot_fac_rcpt'][$key],2,'yes'); ?>      </td>	                                  
                                </tr>
                                <?php }} ?>
                               
                            </tbody>
                         </table>
                     </div> 
                     <div class="col-xs-12 col-md-12 col-lg-6 col-sm-12">
                         <table class="table-hover table-bordered  table-condensed cf table-striped">
                            <thead>
                                <tr>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Physician</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">Total Receipts</th>
                                </tr>
                             </thead>   
                             <tbody>
                                <?php
                                //print_r($charges_arr['tot_phy_rcpt']);
                                $users_data_arr['user_name_by_id'][0]='Other';
                                foreach($users_data_arr['user_name_by_id'] as $key=>$val){
									if($charges_arr['tot_phy_rcpt'][$key]!=0){
                                ?>
                                <tr>
                                    <td  class="text-left">  <?php echo $val; ?>  </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['tot_phy_rcpt'][$key],2,'yes'); ?>      </td>	                                  
                                </tr>
                                <?php }} ?>
                               
                            </tbody>
                         </table>
                      </div>   
                </div>    
                <div class="clearfix margin_clear"></div>  
            </div>
            <div class="ledger_wrap">
                     <div class="panel price panel-green margin_adjust_panel_ledger">
                        <div class="panel-body text-center adjustable_ipad">
                            <ul class="list-group nav nav-justified">
                            	<?php
									ksort($charges_arr['tot_pay_method']);
									foreach($charges_arr['tot_pay_method'] as $key=>$val){
                                		$tot_pay_method_arr[]=array_sum($charges_arr['tot_pay_method'][$key]);
								?>
                                <li class="list-group-item"> 
                                    <div class="full_width2"> <span>   <?php echo $key; ?>  </span> </div>     
                                    <div class="full_width2"> <span class="high">   <?php echo numberFormat(array_sum($charges_arr['tot_pay_method'][$key]),2,'yes'); ?>	</span> </div>                                                                           
                                </li>
                              <?php } ?>
                            </ul>
                    </div>
                 </div>
           	</div> <!-- Ledger Wrap -->
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-12">
      	    <!-- Heading -->
            <div class="ledger_wrap"> 
            	<div class="panel price panel-green margin_adjust_panel_ledger">
                       <div class="panel-body text-center adjustable_ipad">
                                <ul class="list-group nav nav-justified">
                                    <li class="list-group-item"> 
                                        <div class="full_width2"> <span>  Adjustments  </span> </div>     
                                        <div class="full_width2"> <span class="high">  <?php echo numberFormat($tot_adj_payment,2,'yes'); ?>	</span> </div>                                                                       
                                    </li>
                                    <li class="list-group-item"> 
                                        <div class="full_width2"> <span>  Write Off  </span> </div>     
                                        <div class="full_width2"> <span class="high">  <?php echo numberFormat($tot_wrt_payment,2,'yes'); ?>	</span> </div>
                                    </li>
                                </ul>
                        </div>
              </div>	
            </div>
            <!-- Total Balance Div -->
             <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12 padding_0">
                <div class="abs_head_total_charges text-center">
                   <h3>  <span class="rob"> Total Balance (<?php echo numberFormat($tot_balance,2,'yes'); ?>)</span> </h3>
                </div>
            </div><!-- Heading -->
            
            <div class="ledger_wrap"> 
            	<div class="panel price panel-green margin_adjust_panel_ledger">
                   <div class="panel-body text-center adjustable_ipad">
                        <ul class="list-group nav nav-justified">
                            <li class="list-group-item"> 
                                <div class="full_width2"> <span>  Pt Balance </span> </div>     
                                <div class="full_width2"> <span class="high">  <?php echo numberFormat($tot_pat_due,2,'yes'); ?>	</span> </div>                                                                       
                            </li>
                            <li class="list-group-item"> 
                                <div class="full_width2"> <span>  Ins Balance  </span> </div>     
                                <div class="full_width2"> <span class="high">  <?php echo numberFormat($tot_balance-$tot_pat_due,2,'yes'); ?>	</span> </div>
                            </li>
                        </ul>
                    </div>
              </div>	
            </div>
            	<Div class="clearfix visible-sm margin_clear"></Div>
                <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                    <div class="middle_sub_head text-left">
                        <span>Top 10 CPT Codes by Total Charges</span>
                    </div>	
                    <Div class="clearfix margin_line gradient"></Div>
                    <Div class="sub_wrap" id="chart_top_cpt_chrg_div" style="width: 100%; height: 400px;">
                    
                    </Div>
                </Div>    	
                <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                    <div class="middle_sub_head text-left">
                        <span>Top 10 CPT Codes by Total Receipts</span>
                    </div>	
            		<Div class="clearfix margin_line gradient"></Div>
                    <Div class="sub_wrap" id="chart_top_cpt_rcpt_div" style="width: 100%; height: 400px;">
                    
                    </Div>
                </Div>
                
                 <Div class="clearfix margin_clear"></Div>
                <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                    <div class="middle_sub_head text-left">
                        <span>Total Charges by Department</span>
                    </div>	
                    <Div class="clearfix margin_line gradient"></Div>
                    <Div class="sub_wrap" id="chart_quarter_dept_chrg_div" style="width: 100%; min-height: 400px;">
                    </Div>		
                </div> <!-- Col-12 ends -->
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
				<div class="row margin_adj_led">
      			<div class="full_width2 margin_adjust_panel_ledger">
                    <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-hover table-bordered  table-condensed cf table-striped">
                    		<thead>
                                <tr>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">	Department	</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">	Total Charges	</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">	Total Receipts	</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">	Adjustments	</th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">	Write-Off </th>
                                    <th class="col-md-2 col-lg-2 col-sm-2 col-xs-2 text-center">	Balance	</th>
                                </tr>
                             </thead>   
                             <tbody>
                             	<?php
								//print_r($payment_dept_arr);
								$dept_data_arr['dept_desc_by_id'][0]='Other';
								foreach($dept_data_arr['dept_desc_by_id'] as $key=>$val){
									$dept_name=$val;
									$total_balance=$charges_arr['dept_detail_chrg'][$key]-($charges_arr['dept_detail_pay'][$key]+$charges_arr['dept_pay_adj'][$key]+$charges_arr['dept_pay_adj'][$key]+$charges_arr['dept_pay_wrt'][$key]);
								?>
                                <tr>
                                    <td  class="text-left">  <?php echo $dept_name; ?>  </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['dept_detail_chrg'][$key],2,'yes'); ?>      </td>	                                  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['dept_detail_pay'][$key],2,'yes'); ?>  </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['dept_pay_adj'][$key],2,'yes'); ?> </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($charges_arr['dept_pay_wrt'][$key],2,'yes'); ?> </td>	  
                                    <td  class="text-right">  <?php echo numberFormat($total_balance,2,'yes'); ?>  </td>	  
                                </tr>
                                <?php } ?>
                               
                            </tbody>
                     </table> 
                </div>    
                <div class="clearfix margin_clear"></div>  
            </div>
               
            </div>	
         </Div>
      </div> <!-- Col-12 ends -->
		 
	</div> <!-- Middle Inner -->    
<script type="text/javascript">
	bar_chart('serial','chart_top_cpt_chrg_div','<?php echo $top_cpt_chrg_js_arr; ?>');
	bar_chart('serial','chart_top_cpt_rcpt_div','<?php echo $top_cpt_rcpt_js_arr; ?>');
	line_chart('serial','chart_quarter_dept_chrg_div','<?php echo $line_chrg_tot_arr_js_dept; ?>','<?php echo $line_chrg_graph_var_arr_js_dept; ?>');
	line_chart('serial','chart_quarter_dept_rcpt_div','<?php echo $line_payment_tot_arr_js_dept; ?>','<?php echo $line_pay_graph_var_arr_js_dept; ?>');
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