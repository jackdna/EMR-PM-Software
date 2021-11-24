<?php
/*
 * File: ref_phy_report.php
 * Coded in PHP7
 * Purpose: Show Referring Physician's charges and receipts
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
ini_set("memory_limit","3072M");  
$fac_data_arr=pos_fun();
$cpt_data_arr=cpt_fun();
$dept_data_arr=department_fun();
$ref_data_arr=ref_phy_fun();
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
                                    <select class="form-control multi_drop" id="ref_drop" name="ref_drop[]" multiple="multiple">
                                      <?php
                                        foreach($ref_data_arr['ref_name_by_id'] as $key=>$val){
                                            $sel="";
                                            if(in_array($key,$_REQUEST['ref_drop'])){
                                                $sel="selected";
                                            }
                                            echo "<option value='$key' $sel>$val</option>";
                                        }
                                      ?>
                                    </select>	
                                    <small> Referring Physician </small>
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
                                <select class="form-control multi_drop" id="all_initial_enc" name="all_initial_enc">
                                     <option value="all_enc" <?php if($_REQUEST['all_initial_enc']=="all_enc"){echo "selected";} ?>>All</option>
                                     <option value="initial_enc" <?php if($_REQUEST['all_initial_enc']=="initial_enc"){echo "selected";} ?>>Initial</option>
                                </select>
                                <small> All/Initial Encounter  </small>
                            </div>   
                       </div>
                    </div>	
                    <div class="clearfix visible-sm"></div>	     
                    <div class="col-lg-4 col-sm-12 col-md-5 col-xs-12 padding_adj_big for_adj_in_lg">
                        <div class="row margin_adj_big">
                            <div class="col-md-7 col-lg-6 col-sm-6 col-xs-12 padding_adj_big second_lg_div">
                             <div class="pull-right date-pick" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                    <span class="fa fa-calendar"></span>&nbsp;
                                    <span id="date_display"></span>
                                </div>
                                 <input type="hidden" name="date_range_for" id="date_range_for" value="date_of_service" />
                                 <input type="hidden" class="form-control" name="start_date" id="start_date" value="<?php echo xss_rem($_REQUEST['start_date']); ?>"/>
                                 <input type="hidden" class="form-control" name="end_date" id="end_date" value="<?php echo xss_rem($_REQUEST['end_date']); ?>"/>	
                                 <small> Date Range - DOS</small>
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
			
			$inc_arr=array("pos","department","cpt","ref_phy","charges");
			$inc_arr['cond']=$_REQUEST;
			$charges_arr=enc_payment_fun($inc_arr);
			
			$payment_ref_js_arr=json_encode($charges_arr['tot_ref_phy_pay']);
			foreach($ref_data_arr['ref_name_by_id'] as $key=>$val){
				$final_tot_ref_phy_chg[]=array_sum($charges_arr['tot_ref_phy_chg'][$key]);
			}
	?>
    <?php if(array_sum($final_tot_ref_phy_chg)!=0){?>
	<div class="middle_inner scrollable_yes">
    	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
        	<div class="middle_sub_head text-left">
				<span>Top 10 Receipts by Referring Physician </span>
			</div>	
			<Div class="clearfix margin_line gradient"></Div>
            <Div class="sub_wrap" id="chart_top_ref_rcpt_div" style="width: 100%; height: 400px;">
                
            </Div>	
        </div> <!-- Col-12 ends -->
        <div class="clearfix hidden-lg"></div>
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
      	    <div class="row margin_adj_led">
                    <div class="full_width2 margin_adjust_panel_ledger ref_phy_holder">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-hover table-bordered  table-condensed cf table-striped "><!--table_with_caption
                                <caption class="text-center"> Physician Detail  </caption>	-->
                                <thead>
                                    <tr>
                                        <th class="col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center"> Referring Physicians	</th>
                                        <th class="col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">Total Charges	</th>
                                        <th class="col-md-4 col-lg-4 col-sm-4 col-xs-4 text-center">Total Receipts	</th>
                                    </tr>
                                 </thead>   
                                 <tbody>
                                    <?php
									//print_r($charges_arr['tot_ref_phy_chg']);
									foreach($charges_arr['tot_ref_phy_chg'] as $key=>$val){
										$all_tot_ref_phy_chg[$key]=array_sum($charges_arr['tot_ref_phy_chg'][$key]);
									}
									arsort($all_tot_ref_phy_chg);	
									//print_r($final_tot_ref_phy_chg);	
									$ref_data_arr['ref_name_by_id'][0]='Other';	
									foreach($all_tot_ref_phy_chg as $key=>$val){
										if($val!=0){
										$tot_ref_chrg_arr[]=$val;	
										$tot_ref_pay_arr[]=array_sum($charges_arr['tot_ref_phy_detail_pay'][$key]);
									?>
                                    <tr>
                                        <td  class="text-left"> <?php echo $ref_data_arr['ref_name_by_id'][$key]; ?> </td>	  
                                        <td  class="text-right"> <?php echo numberFormat($val,2,'yes'); ?>   </td>	                                  
                                        <td  class="text-right"> <?php echo numberFormat(array_sum($charges_arr['tot_ref_phy_detail_pay'][$key]),2,'yes'); ?>   </td>	  
                                    </tr>
                                     <?php }}if(array_sum($tot_ref_chrg_arr)!=0 || array_sum($tot_ref_pay_arr)!=0 ){ ?>
                                       	 <tr>
                                            <th class="col-md-4 col-lg-4 col-sm-4 col-xs-4 text-right">Grand Total&nbsp;</th>
                                            <th class="col-md-4 col-lg-4 col-sm-4 col-xs-4 text-right"><?php echo numberFormat(array_sum($tot_ref_chrg_arr),2,'yes'); ?></th>
                                            <th class="col-md-4 col-lg-4 col-sm-4 col-xs-4 text-right"><?php echo numberFormat(array_sum($tot_ref_pay_arr),2,'yes'); ?></th>
                                        </tr>
                                     <?php }?>
                                </tbody>
                         </table> 
                    </div>    
                <div class="clearfix margin_clear"></div>  
            </div>
      </div> <!-- Col-12 ends -->
	</div> <!-- Middle Inner -->    
<script type="text/javascript">
	bar_chart('serial','chart_top_ref_rcpt_div','<?php echo $payment_ref_js_arr; ?>');
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