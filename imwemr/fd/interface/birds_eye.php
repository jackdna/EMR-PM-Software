<?php
/*
 * File: birds_eye.php
 * Coded in PHP7
 * Purpose: Show charges and receipts
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
ini_set("memory_limit","3072M"); 
$ins_data_arr=ins_comp_fun();
$fac_data_arr=pos_fun();
$cpt_data_arr=cpt_fun();
$dept_data_arr=department_fun();
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
                                <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 padding_adj_big">
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
                            <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 padding_adj_big">
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
                            <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 padding_adj_big">
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
			
			$inc_arr=array("pos","department","cpt","payment_fun","insurance");
			$inc_arr['cond']=$_REQUEST;
			$charges_arr=enc_charges_fun($inc_arr);
			$charges_org_fac_js_arr=json_encode($charges_arr['fac_org_chrg']);
			$payment_org_fac_js_arr=json_encode($charges_arr['fac_org_pay']);
			
			$charges_fac_js_arr=json_encode($charges_arr['fac_chrg']);
			$payment_fac_js_arr=json_encode($charges_arr['fac_pay']);
			
			
			$charges_dept_js_arr=json_encode($charges_arr['dept_chrg']);
			$payment_dept_js_arr=json_encode($charges_arr['dept_pay']);
			
			$top_cpt_chrg_js_arr=json_encode($charges_arr['top_cpt_chrg']);
			
			$tot_charges_fac=numberFormat(array_sum($charges_arr['tot_chrg']),2,'yes');
			$tot_payment_fac=numberFormat(array_sum($charges_arr['tot_pay']),2,'yes');
			
			$charges_ins_grp_js_arr=json_encode($charges_arr['ins_grp_detail_chrg']);
			$payment_ins_grp_js_arr=json_encode($charges_arr['tot_ins_grp_pay']);
			
			$payment_ins_js_arr=json_encode($charges_arr['tot_ins_pay']);
			$charges_ins_js_arr=json_encode($charges_arr['tot_ins_chrg']);
			
		$line_chart_data=line_chart('quarter',$charges_arr['tot_pay_by_quarter_fac']);
		$line_pay_graph_var_arr_js=json_encode($line_chart_data['line_pay_graph_var_detail']);
		$line_payment_tot_arr_js=json_encode($line_chart_data['line_payment_tot_detail']);
		
		$line_chart_dhrg_dept_data=line_chart('quarter',$charges_arr['tot_chrg_by_quarter_dept']);
		$line_chrg_graph_var_arr_js_dept=json_encode($line_chart_dhrg_dept_data['line_pay_graph_var_detail']);
		$line_chrg_tot_arr_js_dept=json_encode($line_chart_dhrg_dept_data['line_payment_tot_detail']);
		
		$line_chart_dept_data=line_chart('quarter',$charges_arr['tot_pay_by_quarter_dept']);
		$line_pay_graph_var_arr_js_dept=json_encode($line_chart_dept_data['line_pay_graph_var_detail']);
		$line_payment_tot_arr_js_dept=json_encode($line_chart_dept_data['line_payment_tot_detail']);
		
		$payment_wrt_ref_js_arr=json_encode($charges_arr['tot_pay_wrt_ref_by']);
	?>
    <?php if(array_sum($charges_arr['tot_chrg'])!=0 || array_sum($charges_arr['tot_pay'])!=0){?>
	<div class="middle_inner scrollable_yes">
    	<?php 	
			//echo "<pre>";
			//print_r($charges_arr['tot_pay_by_quarter_fac']); 
			//print_r($line_payment_tot_arr);
			//print_r($line_pay_graph_var_arr);
		?>
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="col-md-12 col-lg-9 col-sm-12 col-xs-12 padding_0">
                <div class="abs_head_total_charges text-center">
                    <h3> <span class="rob"> Total Charges (<?php echo $tot_charges_fac; ?>)  	</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="col-md-12 col-lg-9 col-sm-12 col-xs-12 padding_0">
                <div class="abs_head_total_charges text-center">
                   <h3>  <span class="rob"> Total Receipts (<?php echo $tot_payment_fac; ?>) </span> </h3>
                </div>
            </div>
        </div>
       <!-- <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                
            <div class="middle_sub_head text-left">
                <span>Total Charges by Facility (Originating)</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_org_fac_chrg_div" style="width: 100%; height: 400px;">
                </Div>			
            </div>	
         </Div>
         <div class="clearfix visible-md"></div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            
            <div class="middle_sub_head text-left">
                <span>Total Receipts by Facility (Originating)</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_org_fac_rcpt_div" style="width: 100%; height: 400px;">
                </Div>	
            </div>    	
         </Div>-->
         <Div class="clearfix visible-sm margin_clear"></Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Charges by Facility</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_fac_chrg_div" style="width: 100%; height: 400px;">
                </Div>			
            </div>	
         </Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Receipts by Facility</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_fac_rcpt_div" style="width: 100%; height: 400px;">
                </Div>	
            </div>    	
         </Div>
		 
	   <Div class="clearfix visible-sm margin_clear"></Div>
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
			
         <Div class="clearfix visible-sm margin_clear"></Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Charges by Insurance Group</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_ins_chrg_div" style="width: 100%; height: 400px;">
                </Div>		
            </div>	
         </Div>
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
          <Div class="clearfix visible-sm margin_clear"></Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Charges by Insurance Company</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_ins_comp_chrg_div" style="width: 100%; height: 400px;">
                </Div>		
            </div>	
         </Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Receipts by Insurance Company</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_ins_comp_pay_div" style="width: 100%; height: 400px;">
                </Div>		
            </div>    
         </Div>
         <Div class="clearfix visible-sm margin_clear"></Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Charges by Department</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_dept_chrg_div" style="width: 100%; height: 400px;">
                </Div>		
            </div>	
         </Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Receipts by Department</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div class="rel_chart_div">
                <Div class="sub_wrap" id="chart_dept_rcpt_div" style="width: 100%; height: 400px;">
                </Div>		
            </div>    
         </Div>
          <Div class="clearfix visible-sm margin_clear"></Div>
          <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Top 10 CPT Codes by Total Charges</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div>
                <Div class="sub_wrap" id="chart_top_cpt_chrg_div" style="width: 100%; height: 400px;">
                
                </Div>		
            </div>	
         </Div>
         <Div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Receipts by Quarter by Facility</span>
            </div>	
            <Div class="clearfix margin_line gradient"></Div>
            <div>
                <Div class="sub_wrap" id="chart_quarter_rcpt_div" style="width: 100%; height: 400px;">
                
                </Div>		
            </div>
         </Div>
         
        
			
		   <Div class="clearfix margin_clear"></Div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span>Total Write Off and Refunds</span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
				<Div class="sub_wrap" id="chart_wrt_ref_div" style="width: 100%; height: 400px;">
                </Div>		
            </div> <!-- Col-12 ends -->
      </div>    
         <script type="text/javascript">
            //pie_chart('pie','chart_org_fac_chrg_div','<?php echo $charges_org_fac_js_arr; ?>');
            //pie_chart('pie','chart_org_fac_rcpt_div','<?php echo $payment_org_fac_js_arr; ?>');
            pie_chart('pie','chart_fac_chrg_div','<?php echo $charges_fac_js_arr; ?>');
            pie_chart('pie','chart_fac_rcpt_div','<?php echo $payment_fac_js_arr; ?>');
			pie_chart('pie','chart_ins_chrg_div','<?php echo $charges_ins_grp_js_arr; ?>');
            pie_chart('pie','chart_ins_pay_div','<?php echo $payment_ins_grp_js_arr; ?>');
			pie_chart('pie','chart_ins_comp_chrg_div','<?php echo $charges_ins_js_arr; ?>');
			pie_chart('pie','chart_ins_comp_pay_div','<?php echo $payment_ins_js_arr; ?>');
            pie_chart('pie','chart_dept_chrg_div','<?php echo $charges_dept_js_arr; ?>');
            pie_chart('pie','chart_dept_rcpt_div','<?php echo $payment_dept_js_arr; ?>');
            bar_chart('serial','chart_top_cpt_chrg_div','<?php echo $top_cpt_chrg_js_arr; ?>');
			line_chart('serial','chart_quarter_rcpt_div','<?php echo $line_payment_tot_arr_js; ?>','<?php echo $line_pay_graph_var_arr_js; ?>');
			line_chart('serial','chart_quarter_dept_chrg_div','<?php echo $line_chrg_tot_arr_js_dept; ?>','<?php echo $line_chrg_graph_var_arr_js_dept; ?>');
			line_chart('serial','chart_quarter_dept_rcpt_div','<?php echo $line_payment_tot_arr_js_dept; ?>','<?php echo $line_pay_graph_var_arr_js_dept; ?>');
			pie_chart('pie','chart_wrt_ref_div','<?php echo $payment_wrt_ref_js_arr; ?>');
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