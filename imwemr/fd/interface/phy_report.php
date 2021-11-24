<?php
/*
 * File: phy_report.php
 * Coded in PHP7
 * Purpose: Show Physician's receipts
 * Access Type: Direct access
 * The MIT License (MIT)
 * Distribute, Modify and Contribute under MIT License
 * MIT License and Usage
 */
ini_set("memory_limit","3072M");  
$user_type_arr['type']=array("1","3","5","13");
//$ins_data_arr=ins_comp_fun();
$fac_data_arr=pos_fun();
$cpt_data_arr=cpt_fun();
$dept_data_arr=department_fun();
$users_data_arr=users_fun($user_type_arr);
$aging_arr=array("0"=>"00-30","1"=>"31-60","2"=>"61-90","3"=>"91-120","4"=>"121-150","5"=>"151-180","6"=>"180+");
$cur_date=date("Y-m-d");
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
			
			$inc_arr=array("pos","department","cpt","users","charges");
			$inc_arr['cond']=$_REQUEST;
			$charges_arr=enc_payment_fun($inc_arr);
			
			$payment_phy_js_arr=json_encode($charges_arr['tot_phy_pay']);
			$payment_dept_js_arr=json_encode($charges_arr['dept_pay']);
			
			$tot_payment_fac=numberFormat(array_sum($charges_arr['tot_pay']),2,'yes');
			foreach($users_data_arr['user_name_by_id'] as $key=>$val){
				$users_name_arr[$key]=$val;
				foreach($charges_arr['phy_dos_chrg'][$key] as $key2=>$val2){
					$phy_aging_arr[$key2][]=array_sum($charges_arr['phy_dos_chrg'][$key][$key2]);
				}
			}
			$users_name_arr[0]="Other";

			$k=0;
			foreach($aging_arr as $key=>$val){
				$aging_phy_pie_arr[$k]["kee"]=$val;
				$aging_phy_pie_arr[$k]["val"]=array_sum($phy_aging_arr[$key]);
				$k++;
			}
			
			$aging_phy_js_arr=json_encode($aging_phy_pie_arr);
			
			$key_i=0;$kk=0;
			$quarter_arr=array("0"=>"Quarter1","1"=>"Quarter2","2"=>"Quarter3","3"=>"Quarter4");
			foreach($quarter_arr as $key=>$val){
				$line_charges_tot_arr[$key]["category"]=$val;
			}
			foreach($charges_arr['tot_pay_by_quarter_chrg'] as $key=>$val){	
				$key_i++;
				$user_name=$users_name_arr[$key];
				$line_charges_graph_var_arr[]=array("alphaField"=> "C",
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
					"title"=> "$user_name",
					"valueField"=> "column-$key_i",
					"xField"=> "C",
					"yField"=> "C");
				
				foreach($charges_arr['tot_pay_by_quarter_chrg'][$key] as $key2=>$val2){	
					$line_charges_tot_arr[$key2]["column-".$key_i]=array_sum($charges_arr['tot_pay_by_quarter_chrg'][$key][$key2]);
					$tot_chr_chk_arr[]=array_sum($charges_arr['tot_pay_by_quarter_chrg'][$key][$key2]);
					$kk++;
				}
			}
			//print_r($line_charges_tot_arr);
			$key_i=0;$kk=0;
			$quarter_arr=array("0"=>"Quarter1","1"=>"Quarter2","2"=>"Quarter3","3"=>"Quarter4");
			foreach($quarter_arr as $key=>$val){
				$line_payment_tot_arr[$key]["category"]=$val;
			}
			foreach($charges_arr['tot_pay_by_quarter_phy'] as $key=>$val){	
				$key_i++;
				$user_name=$users_name_arr[$key];
				$line_pay_graph_var_arr[]=array("alphaField"=> "C",
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
					"title"=> "$user_name",
					"valueField"=> "column-$key_i",
					"xField"=> "C",
					"yField"=> "C");
				
				foreach($charges_arr['tot_pay_by_quarter_phy'][$key] as $key2=>$val2){	
					$line_payment_tot_arr[$key2]["column-".$key_i]=array_sum($charges_arr['tot_pay_by_quarter_phy'][$key][$key2]);
					$tot_pay_chk_arr[]=array_sum($charges_arr['tot_pay_by_quarter_chrg'][$key][$key2]);
					$kk++;
				}
			}
			
			
			$line_payment_tot_arr_js=json_encode($line_payment_tot_arr);
			$line_pay_graph_var_arr_js=json_encode($line_pay_graph_var_arr);
			
			$line_charges_tot_arr_js=json_encode($line_charges_tot_arr);
			$line_charges_graph_var_arr_js=json_encode($line_charges_graph_var_arr);
	?>
    <?php if(array_sum($tot_pay_chk_arr)!=0 || array_sum($tot_chr_chk_arr)!=0){?>
        <div class="middle_inner scrollable_yes">
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span> Total Receipts by Physician </span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <div class="rel_chart_div">
                    <Div class="sub_wrap" id="chart_phy_rcpt_div" style="width: 100%; height: 400px;">
                    </Div>			
                </div>	
            </div> <!-- Col-12 ends -->
            <div class="clearfix hidden-lg"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span> Total Receipts by Department </span>
                </div>	
                <div class="clearfix margin_line gradient"></div>
                <div class="rel_chart_div">
                    <Div class="sub_wrap" id="chart_dept_rcpt_div" style="width: 100%; height: 400px;">
                    </Div>			
                </div>	
            </div> 
            <div class="clearfix"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span> Aging </span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <div class="rel_chart_div">
                    <Div class="sub_wrap" id="chart_phy_aging_div" style="width: 100%; height: 400px;">
                    </Div>			
                </div>	
            </div> <!-- Col-12 ends -->
            <div class="clearfix hidden-lg"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span> Physician Write off and Refunds </span>
                </div>	
                 <Div class="clearfix margin_line gradient"></Div>
            </div> 
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="row margin_adj_led">
                    <div class="full_width2 margin_adjust_panel_ledger">
                        <table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-hover table-bordered  table-condensed cf table-striped "><!--table_with_caption
                                <caption class="text-center"> Physician Detail  </caption>	-->
                                <thead>
                                    <tr>
                                        <th class="col-md-3 col-lg-3 col-sm-3 col-xs-3 text-center">Physicians</th>
                                        <th class="col-md-3 col-lg-3 col-sm-3 col-xs-3 text-center">Write-Off</th>
                                        <th class="col-md-3 col-lg-3 col-sm-3 col-xs-3 text-center">Refunds</th>
										<th class="col-md-3 col-lg-3 col-sm-3 col-xs-3 text-center">Adjustment</th>
                                    </tr>
                                 </thead>   
                                 <tbody>
                                    <?php
                                        //print_r($charges_arr['tot_phy_pay']);
                                    $users_data_arr['user_name_by_id'][0]='Other';	
									$phy_wrt_ref=0;
                                    foreach($users_data_arr['user_name_by_id'] as $key=>$val){
                                        if(array_sum($charges_arr['phy_pay_wrt'][$key])!=0 || array_sum($charges_arr['phy_pay_refund'][$key])!=0 || array_sum($charges_arr['phy_pay_adj'][$key])!=0){
											$phy_wrt_ref=1;
                                    ?>
                                    <tr>
                                        <td  class="text-left"> <?php echo $val; ?> </td>	  
                                        <td  class="text-right"> <?php echo numberFormat(array_sum($charges_arr['phy_pay_wrt'][$key]),2,'yes'); ?>   </td>	                                  
                                        <td  class="text-right"> <?php echo numberFormat(array_sum($charges_arr['phy_pay_refund'][$key]),2,'yes'); ?>   </td>	
										<td  class="text-right"> <?php echo numberFormat(array_sum($charges_arr['phy_pay_adj'][$key]),2,'yes'); ?>   </td>  
                                    </tr>
                                    <?php }}if($phy_wrt_ref==0){ ?>
                                    <tr>
                                        <td  class="text-center" colspan="4">  <span class="rob"> No record exists. </span> </td>	  
                                    </tr>
                                    <?php }?>
                                </tbody>
                         </table> 
                    </div>    
                    <div class="clearfix margin_clear"></div>  
                </div>
          </div>
          <div class="clearfix"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span> Physician Charges </span>
                </div>	
                <Div class="clearfix margin_line gradient"></Div>
                <Div class="sub_wrap" id="chart_quarter_chrg_div" style="width: 100%; min-height: 400px;">
                </Div>		
            </div> <!-- Col-12 ends -->
            <div class="clearfix hidden-lg"></div>
            <div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
                <div class="middle_sub_head text-left">
                    <span> Physician Receipts </span>
                </div>	
                <div class="clearfix margin_line gradient"></div>
                <Div class="sub_wrap" id="chart_quarter_rcpt_div" style="width: 100%; min-height: 400px;">
                </Div>	
            </div> 
            <div class="clearfix"></div>
           
		   <?php 
		   		$lk=0;
				foreach($charges_arr['payment_phy_cpt_final_arr'] as $key=>$val){
				//arsort($charges_arr['payment_phy_cpt_final_arr'][$key]);
				//print_r($charges_arr['payment_phy_cpt_final_arr'][$key]);
				$payment_phy_cpt_tot_arr_lk=array();
				$payment_phy_cpt_tot_arr_other=array();
					$kk=0;
					foreach($charges_arr['payment_phy_cpt_final_arr'][$key] as $key_cpt=>$val_cpt){
						if($charges_arr['payment_phy_cpt_final_arr'][$key][$key_cpt]!=0){
							if($kk<=9 && $key_cpt!=""){
								$payment_phy_cpt_tot_arr_lk[$kk]["kee"]=ucfirst($key_cpt);
								$payment_phy_cpt_tot_arr_lk[$kk]["val"]=$charges_arr['payment_phy_cpt_final_arr'][$key][$key_cpt];
							}else{
								$payment_phy_cpt_tot_arr_other[]=$charges_arr['payment_phy_cpt_final_arr'][$key][$key_cpt];
							}
							$kk++;
						}
					}
					if(array_sum($payment_phy_cpt_tot_arr_other)!=0){
						$payment_phy_cpt_tot_arr_lk[10]["kee"]="Other";
						$payment_phy_cpt_tot_arr_lk[10]["val"]=array_sum($payment_phy_cpt_tot_arr_other);
					}
					$payment_phy_cpt_tot_arr_js_lk=json_encode($payment_phy_cpt_tot_arr_lk);
			?>
				<div class="col-md-12 col-sm-12 col-xs-12 col-lg-6">
					<div class="middle_sub_head text-left">
						<span> CPT Wise Receipts by <?php echo $key; ?></span>
					</div>	
					<Div class="clearfix margin_line gradient"></Div>
					<div class="rel_chart_div">
						<Div class="sub_wrap" id="chart_phy_cpt_rcpt_div_<?php echo $lk; ?>" style="width: 100%; height: 400px;">
						</Div>			
					</div>	
				</div> 
			<?php	
					echo "<script type='text/javascript'>pie_chart('pie','chart_phy_cpt_rcpt_div_$lk','".$payment_phy_cpt_tot_arr_js_lk."');</script>";
					$lk++;
					
				}
		   ?>
		   
			
        </div> <!-- Middle Inner -->    
	<script type="text/javascript">
		pie_chart('pie','chart_phy_rcpt_div','<?php echo $payment_phy_js_arr; ?>');
		pie_chart('pie','chart_dept_rcpt_div','<?php echo $payment_dept_js_arr; ?>');
		pie_chart('pie','chart_phy_aging_div','<?php echo $aging_phy_js_arr; ?>');
		line_chart('serial','chart_quarter_chrg_div','<?php echo $line_charges_tot_arr_js; ?>','<?php echo $line_charges_graph_var_arr_js; ?>');
		line_chart('serial','chart_quarter_rcpt_div','<?php echo $line_payment_tot_arr_js; ?>','<?php echo $line_pay_graph_var_arr_js; ?>');
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