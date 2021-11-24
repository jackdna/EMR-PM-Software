<?php
/*
 * File: trends.php
 * Coded in PHP7
 * Purpose: Show charges and receipts
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
ini_set("memory_limit","3072M"); 
$user_type_arr['type']=array("1");
$fac_data_arr=pos_fun();
$dept_data_arr=department_fun();
$users_data_arr=users_fun($user_type_arr);
?>
<script type="text/javascript">
	function submit_form(){
		show_loading_image('show');
	 	document.getElementById('search_frm').submit();
	}
</script>
<?php
/*$charges_arr['2015']=array("Jan"=>"2000","Feb"=>"3000","Mar"=>"4000");
$charges_arr['2014']=array("Jan"=>"1000","Feb"=>"5000","Mar"=>"2000");
$charges_arr['2013']=array("Jan"=>"4000","Feb"=>"1000","Mar"=>"3000");

$payments_arr['2015']=array("Jan"=>"10000","Feb"=>"2000","Mar"=>"3000");
$payments_arr['2014']=array("Jan"=>"1200","Feb"=>"5200","Mar"=>"2100");
$payments_arr['2013']=array("Jan"=>"2000","Feb"=>"1030","Mar"=>"3020");
*/
?>
<div class="container-fluid padding_0">
    <div class="filter_area bordered_div_inside">
		<div class="inside_wrap_filter">	    	
            <form method="post" name="search_frm" id="search_frm" action="index.php" onsubmit="return submit_form();">
                <input type="hidden" name="tab_name" id="tab_name" value="<?php echo $_REQUEST['tab_name']; ?>" />
                <input type="hidden" name="srh_button" id="srh_button" value="srh_button" />
                <div class="row">
                    <div class="col-md-7 col-lg-7 col-xs-12 col-sm-12 padding_adj_big">
                        <div class="row margin_adj_big">
                              <div class="col-md-4 col-lg-4 col-sm-6 col-xs-12 padding_adj_big">
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
                            <div class="col-md-4 col-lg-4 col-sm-6 col-xs-12 padding_adj_big">
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
                            <div class="clearfix visible-sm"></div>
                            <div class="col-md-4 col-lg-4 col-sm-6 col-xs-12 padding_adj_big">
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
                       </div>
                    </div>	
                   <!-- <div class="clearfix visible-sm"></div>	 -->   
                    <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 padding_adj_big for_adj_in_lg">
                        <div class="row margin_adj_big">
                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12 padding_adj_big">
                                <select class="form-control multi_drop" id="date_range_trend" name="date_range_trend">
                                	<option value="monthly" <?php if($_REQUEST['date_range_trend']=="monthly"){echo "selected";} ?>> Monthly </option>
                                	<option value="quarterly" <?php if($_REQUEST['date_range_trend']=="quarterly"){echo "selected";} ?>> Quarterly </option>
                                </select>	
                                <small> Date Range For  </small>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-12 padding_adj_big">
                              	<select class="form-control multi_drop" name="date_range_year_trend[]" id="date_range_year_trend" multiple>
									<?php for($yr=2010;$yr<=2016;$yr++){?>
                                        <option value="<?php echo $yr ?>" <?php if(in_array($yr,$_REQUEST['date_range_year_trend'])){echo "selected";} ?>> <?php echo $yr ?> </option> 
                                    <?php }?>
                                </select>
                                 <input type="hidden" name="date_range_for" id="date_range_for" value="date_of_service" />
                                 <small> Yearly </small>
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
		
		$inc_arr=array("pos","department","cpt","payment_fun");
		$inc_arr['cond']=$_REQUEST;
		$charges_arr=enc_charges_fun($inc_arr);
		//print_r($charges_arr['year_detail_chrg']);
		$key_i=0;
		foreach($charges_arr['year_detail_chrg'] as $key=>$val){
			$key_i++;
			$chrg_graph_var_arr[]=array("alphaField"=> "C",
					"balloonText"=> "[[title]] of [[category]]: $[[value]]",
					"bullet"=> "round",
					"bulletField"=> "C",
					"bulletSizeField"=> "C",
					"closeField"=> "C",
					"colorField"=> "C",
					"customBulletField"=> "C",
					"dashLengthField"=> "C",
					"descriptionField"=> "C",
					"errorField"=> "C",
					"fillColorsField"=> "C",
					"gapField"=> "C",
					"highField"=> "C",
					"id"=> "AmGraph-$key_i",
					"labelColorField"=> "C",
					"lineColorField"=> "C",
					"lowField"=> "C",
					"openField"=> "C",
					"patternField"=> "C",
					"title"=> "$key",
					"valueField"=> "column-$key_i",
					"xField"=> "C",
					"yField"=> "C");
				$kk=0;
				foreach($charges_arr['year_detail_chrg'][$key] as $key2=>$val2){	
					$charges_tot_arr[$kk]["category"]=$key2;
					$charges_tot_arr[$kk]["column-".$key_i]=$val2;
					$kk++;
					$grand_chrg_total[]=$val2;
				}
				
				
			$chrg_bar_var_arr[]=array("balloonText"=> "[[title]] of [[category]]: $[[value]]",
									"fillAlphas"=> 1,
									"id"=> "AmGraph-$key_i",
									"title"=> "$key",
									"type"=> "column",
									"valueField"=> "column-$key_i");
		}
		
		$key_i=0;
		foreach($charges_arr['tot_dos_pay'] as $key=>$val){
			$key_i++;
			$pay_graph_var_arr[]=array("alphaField"=> "C",
					"balloonText"=> "[[title]] of [[category]]: $[[value]]",
					"bullet"=> "round",
					"bulletField"=> "C",
					"bulletSizeField"=> "C",
					"closeField"=> "C",
					"colorField"=> "C",
					"customBulletField"=> "C",
					"dashLengthField"=> "C",
					"descriptionField"=> "C",
					"errorField"=> "C",
					"fillColorsField"=> "C",
					"gapField"=> "C",
					"highField"=> "C",
					"id"=> "AmGraph-$key_i",
					"labelColorField"=> "C",
					"lineColorField"=> "C",
					"lowField"=> "C",
					"openField"=> "C",
					"patternField"=> "C",
					"title"=> "$key",
					"valueField"=> "column-$key_i",
					"xField"=> "C",
					"yField"=> "C");
				$kk=0;
				foreach($charges_arr['tot_dos_pay'][$key] as $key2=>$val2){	
					$payment_tot_arr[$kk]["category"]=$key2;
					$payment_tot_arr[$kk]["column-".$key_i]=$val2;
					$kk++;
					$grand_pay_total[]=$val2;
				}
				
				$pay_bar_var_arr[]=array("balloonText"=> "[[title]] of [[category]]: $[[value]]",
									"fillAlphas"=> 1,
									"id"=> "AmGraph-$key_i",
									"title"=> "$key",
									"type"=> "column",
									"valueField"=> "column-$key_i");					
		}
	?>
	<div class="middle_inner scrollable_yes">
    	<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="col-md-12 col-lg-9 col-sm-12 col-xs-12 padding_0">
                <div class="abs_head_total_charges text-center">
                    <h3> <span class="rob"> Total Charges (<?php echo numberFormat(array_sum($grand_chrg_total),2,'yes'); ?>)  	</span></h3>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="col-md-12 col-lg-9 col-sm-12 col-xs-12 padding_0">
                <div class="abs_head_total_charges text-center">
                   <h3>  <span class="rob"> Total Receipts (<?php echo numberFormat(array_sum($grand_pay_total),2,'yes'); ?>) </span> </h3>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Charges</span>
            </div>	
            <div class="clearfix margin_line gradient"></div>
            <div>
            	<?php
					
					$charges_tot_js_arr=json_encode($charges_tot_arr);
					$chrg_graph_var_js_arr=json_encode($chrg_graph_var_arr);
					$chrg_bar_var_js_arr=json_encode($chrg_bar_var_arr);
				?>
                <div class="sub_wrap" id="chart_line_chrg_div" style="width: 100%; height: 400px;">
                 <script type="text/javascript">
				 	var chartChrgData = '<?php echo $charges_tot_js_arr; ?>';
					var chartChrgData_js = $.parseJSON(chartChrgData);
					
					var chartChrgGraphData = '<?php echo $chrg_graph_var_js_arr; ?>';
					var chartChrgGraphData_js = $.parseJSON(chartChrgGraphData);
					
					var chartChrgBarData = '<?php echo $chrg_bar_var_js_arr; ?>';
					var chartChrgBarData_js = $.parseJSON(chartChrgBarData);
					
					
					AmCharts.makeChart("chart_line_chrg_div",
					{
						"type": 'serial',
						"categoryField": "category",
						"startDuration": 1,
						"theme": "light",
						"categoryAxis": {
							"gridPosition": "start"
						},
						"trendLines": [],
						"graphs": chartChrgGraphData_js,
						"guides": [],
						"valueAxes": [{
							"unit": "$",
							"unitPosition": "left"
						}],
						"allLabels": [],
						"balloon": {},
						"legend": {
							"useGraphSettings": true
						},
						"titles": [],
						"dataProvider": chartChrgData_js
					}
					);
				</script>
                </div>		
            </div>	
         </div>	
         <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span>Total Receipts</span>
            </div>	
            <div class="clearfix margin_line gradient"></div>
            <div>
            	<?php
					$payment_tot_js_arr=json_encode($payment_tot_arr);
					$pay_graph_var_js_arr=json_encode($pay_graph_var_arr);
					$pay_bar_var_js_arr=json_encode($pay_bar_var_arr);
				?>
                <div class="sub_wrap" id="chart_line_rcpt_div" style="width: 100%; height: 400px;">
                 <script type="text/javascript">
				 
				 	var chartPayData = '<?php echo $payment_tot_js_arr; ?>';
					var chartPayData_js = $.parseJSON(chartPayData);
					
					var chartPayGraphData = '<?php echo $pay_graph_var_js_arr; ?>';
					var chartPayGraphData_js = $.parseJSON(chartPayGraphData);
					
					var chartPayBarData = '<?php echo $pay_bar_var_js_arr; ?>';
					var chartPayBarData_js = $.parseJSON(chartPayBarData);
					
					AmCharts.makeChart("chart_line_rcpt_div",
					{
						"type": 'serial',
						"categoryField": "category",
						"startDuration": 1,
						"theme": "light",
						"categoryAxis": {
							"gridPosition": "start"
						},
						"trendLines": [],
						"graphs": chartPayGraphData_js,
						"guides": [],
						"valueAxes": [{
							"unit": "$",
							"unitPosition": "left"
						}],
						"allLabels": [],
						"balloon": {},
						"legend": {
							"useGraphSettings": true
						},
						"titles": [],
						"dataProvider": chartPayData_js
					}
					);
				</script>
                </div>		
            </div>	
         </div>	
  		<div class="clearfix"></div>         
        <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span> Total Charges </span>
            </div>	
            <div class="clearfix margin_line gradient"></div>
            <div>
                <div class="sub_wrap" id="chart_bar_chrg_div" style="width: 100%; height: 400px;">
                <script type="text/javascript">
					AmCharts.makeChart("chart_bar_chrg_div",
						{
							"type": "serial",
							"categoryField": "category",
							"startDuration": 1,
							"theme": "light",
							"categoryAxis": {
								"gridPosition": "start"
							},
							"trendLines": [],
							"graphs": chartChrgBarData_js,
							"guides": [],
							"valueAxes": [{
								"unit": "$",
								"unitPosition": "left"
							}],
							"allLabels": [],
							"balloon": {},
							"legend": {
								"useGraphSettings": true
							},
							"titles": [],
							"dataProvider": chartChrgData_js
						}
					);
				</script>
                </div>		
            </div>
         </div>
         <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
            <div class="middle_sub_head text-left">
                <span> Total Receipts </span>
            </div>	
            <div class="clearfix margin_line gradient"></div>
            <div>
                <div class="sub_wrap" id="chart_bar_rcpt_div" style="width: 100%; height: 400px;">
                <script type="text/javascript">
					AmCharts.makeChart("chart_bar_rcpt_div",
						{
							"type": "serial",
							"categoryField": "category",
							"startDuration": 1,
							"theme": "light",
							"categoryAxis": {
								"gridPosition": "start"
							},
							"trendLines": [],
							"graphs": chartPayBarData_js,
							"guides": [],
							"valueAxes": [{
								"unit": "$",
								"unitPosition": "left"
							}],
							"allLabels": [],
							"balloon": {},
							"legend": {
								"useGraphSettings": true
							},
							"titles": [],
							"dataProvider": chartPayData_js
						}
					);
				</script>
                </div>		
            </div>
         </div>
   </div> <!-- Middle Inner -->    
<?php } ?>
<script type="text/javascript">
	var ar = [["search","Search","submit_form();"]];
	$(document).ready(function() {
		top.btn_show("financial",ar);
	});
</script>