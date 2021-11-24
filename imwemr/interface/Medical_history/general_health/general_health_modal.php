
<!-- Start Referring Physician Modal -->
<div id="primaryCareProvider" class="modal" role="dialog" ></div>
<!-- Referring Physician Modal -->

<!-- Show Advanced Directive Image -->
<div id="ad_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Advanced Directive</h4>
     	</div>
      
      <div class="modal-body" style="max-height:450px; overflow:hidden; overflow-y:auto;">
      	<div class="loader-small"></div>
     	</div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Blood Sugar Graph Modal -->
<div id="blood_sugar_graph_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient Blood Sugar Graph </h4>
     	</div>
      
      <div class="modal-body graphs" id="blood_sugar_graph">
      	<div class="loader-small"></div>
     	</div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Cholesterol Graph Modal -->
<div id="cholesterol_graph_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient Cholesterol Graph </h4>
     	</div>
      
      <div class="modal-body graphs" id="cholesterol_graph">
      	<div class="loader-small"></div>
     	</div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Cholesterol LDL Graph Modal -->
<div id="cholesterol_ldl_graph_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient Cholesterol LDL Graph </h4>
     	</div>
      
      <div class="modal-body graphs" id="cholesterol_ldl_graph">
      	<div class="loader-small"></div>
     	</div>
      
       <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Cholesterol HDL Graph Modal -->
<div id="cholesterol_hdl_graph_modal" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient Cholesterol HDL Graph </h4>
     	</div>
      
      <div class="modal-body graphs" id="cholesterol_hdl_graph">
      	<div class="loader-small"></div>
     	</div>
      
      <div id="module_buttons" class="modal-footer ad_modal_footer">
        <button type="button" class="btn btn-danger"  data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Patient Blood Sugar Hx Modal -->
<div id="bs_history" class="modal" role="dialog">
	<div class="modal-dialog modal-lg" style="width:80%;">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient Blood Sugar History </h4>
     	</div>
      
      <div class="modal-body" style="min-height:450px; max-height:450px; overflow:hidden; ">
      	<div id="selectContainer" style="position:absolute;"></div>
      	<div id="bs_selectContainer" style="position:absolute;"></div>
        
        <div class="row">
        
        	<div class="col-xs-12 ">
          	<div class="headinghd">
            	<h4>Add New
              <span class="pull-right glyphicon glyphicon-plus" onclick="top.fmain.add_blood_sugar_row();">
              </span>
              </h4>
           	</div>
          </div>
         
         	<div class="col-xs-12" >
          	<div id="bs_hx_header">
          	<table class="table table-bordered table-hover table-striped scroll release-table margin_0">
            	<thead class="grythead">
              	<tr>
                	<td class="col-xs-2">Date</td>
                  <td class="col-xs-1">Blood Sugar</td>
                  <td class="col-xs-1">HbA1c</td>
                  <td class="col-xs-1">&nbsp;</td>
                  <td class="col-xs-1">Fasting</td>
                  <td class="col-xs-2">Time of day</td>
                  <td class="col-xs-3">Description</td>  
                  <td class="col-xs-1">&nbsp;</td>  
                </tr>
             	</thead>
           	</table>
           	</div> 
        	</div>	    
            
         	<div class="col-xs-12" style="max-height:160px; min-height:160px; overflow:hidden; overflow-y:auto;"> 	   
            <table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="table-layout: fixed;">
              <tbody id="bs_add_body">
              	<input type="hidden" name="blood_sugar_rows" id="blood_sugar_rows" value="1" />
              	<?php
                            $blood_sugar_opt_str = '';
                            foreach($gen_medicine['blood_sugar_opt_arr'] as $key => $val){
                              $blood_sugar_opt_str .= '<option value="'.$val.'">'.$val.'</option>';
                            }
                            
									for($i = 1; $i < 2; $i++)
									{
										$html = '';
										$html .= '<tr id="bs_add_row_'.$i.'">';
										//Date Column
										$html .= '<td class="col-xs-2">';
										$html .= '<div class="input-group">';
										$html .= '<input type="text" name="blood_sugar_date'.$i.'" id="blood_sugar_date'.$i.'" onKeyUp="chk_change(\'\',this,event);" onChange="chk_change(\'\',this,event);" value="" onBlur="checkdate(this);" title="'.$GLOBALS['date_format'].'" class="datepicker form-control" onClick="getDate_and_setToField(this)" />';
										$html .= '<label class="input-group-addon pointer" for="blood_sugar_date'.$i.'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
										$html .= '</div>';
										$html .= '</td>';
										
										//Blood Sugar Column
										$html .= '<td class="col-xs-1">';
										$html .= '<input type="text" name="blood_sugar_mg'.$i.'" id="blood_sugar_mg'.$i.'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control" />';
										$html .= '</td>';
										
										//HBA1C Column
										$html .= '<td class="col-xs-1">';
										$html .= '<input type="text" name="blood_sugar_hba1c_val'.$i.'" id="blood_sugar_hba1c_val'.$i.'" onKeyUp="chk_change(\'\',this,event);" value="" class="form-control" />';
										$html .= '</td>';
                                        
                                        
										//HBA1C Val Column
										$html .= '<td class="col-xs-1">';
										$html .= '<select class="selectpicker" data-width="100%" data-container="#bs_selectContainer" data-title="Select" name="blood_sugar_hba1c'.$i.'" id="blood_sugar_hba1c'.$i.'" onChange="top.fmain.chk_change(\'\',this,event);" >';
										$html .= $blood_sugar_opt_str;
										$html .= '</select>';
										$html .= '</td>';
										
										
										//IS Fasting Column
										$html .= '<td class="col-xs-1">';
										$html .= '<div class="checkbox">';
										$html .= '<input type="checkbox" name="blood_sugar_fasting'.$i.'" id="blood_sugar_fasting'.$i.'" onClick="chk_change(\'\',this,event);" value="1"/>';
										$html .= '<label for="blood_sugar_fasting'.$i.'"></label>';
										$html .= '</div>';
										$html .= '</td>';
										
										//Time of Day Column
										$html .= '<td class="col-xs-2">';
										$html .= '<div class="select-container">';
										$html .= '<div id="div_blood_sugar_time_of_day'.$i.'">';
										$html .= '<select name="blood_sugar_time_of_day'.$i.'" id="blood_sugar_time_of_day'.$i.'" class="selectpicker" onChange="javascript:show_hide(\'other_blood_sugar_time_of_day'.$i.'\',\'div_blood_sugar_time_of_day'.$i.'\',this); chk_change(\'\',this,event);" title="'.imw_msg('drop_sel').'" data-width="100%" data-container="#selectContainer" data-size="6">';
										$html .= '<option value="0-" >'.imw_msg('drop_sel').'</option>';
										$html .= '<option value="1-Morning" >Morning</option>';
										$html .= '<option value="2-Post Breakfast" >Post Breakfast</option>';
										$html .= '<option value="3-Afternoon" >Afternoon</option>';
										$html .= '<option value="4-Post Lunch" >Post Lunch</option>';
										$html .= '<option value="5-Evening" >Evening</option>';
										$html .= '<option value="6-Night" >Night</option>';
										$html .= '<option value="7-Post Dinner" >Post Dinner</option>';
										$html .= '<option value="8-Other" >Other</option>';
										$html .= '</select>';
										$html .= '</div>';
										$html .= '</div>';
										
										$html .= '<div id="other_blood_sugar_time_of_day'.$i.'" class="hidden">';
										$html .= '<div class="input-group">';
										$html .= '<input type="text" class="form-control" id="blood_sugar_time_of_day_other'.$i.'" name="blood_sugar_time_of_day_other'.$i.'" onKeyUp="chk_change(\'\',this,event);" value="" />';
										$html .= '<label class="input-group-addon btn back_other" data-tab-name="blood_sugar_time_of_day'.$i.'"><i class="glyphicon glyphicon-arrow-left"></i></label>';
										$html .= '</div>';
										$html .= '</div>';
										$html .= '</td>';
										
										//Description Column
										$html .= '<td class="col-xs-3">';
										$html .= '<textarea name="blood_sugar_description'.$i.'" id="blood_sugar_description'.$i.'" rows="1" cols="10" onKeyUp="chk_change(\'\',this,event);" onFocus="getDate_and_setToField($(\'#blood_sugar_date'.$i.'\'));" class="form-control"></textarea>';
										$html .= '</td>';
										//<img src="'.$library_path.'/images/close_small.png" class="pointer" onclick="delete_blood_sugar(\'\', \'\','.$i.');">
										$html .= '<td class="col-xs-1"></td>';
										$html .= '</tr>';
										
										echo $html ;
										
									}
								?>
              </tbody>
            </table>
          </div>
       	</div>
    	
      	<!-- History HTML-->
      	<div class="row mt20">
        	<?php
						$bs_data = $health->hx_data('blood_sugar');
						$paddingRight = (is_array($bs_data) && count($bs_data) > 4) ? 'style="padding-right:12px !important;"' : '';
					?>
        	<div class="col-xs-12">
          	<div id="bs_history_header" <?php echo $paddingRight;?> >
          	<table class="table table-bordered table-hover table-striped scroll release-table margin_0">
            	<thead class="grythead">
              	<tr>
                	<td class="col-xs-2">Date</td>
                  <td class="col-xs-1">Blood Sugar</td>
                  <td class="col-xs-2">HbA1c</td>
                  <td class="col-xs-1">Fasting</td>
                  <td class="col-xs-2">Time of day</td>
                  <td class="col-xs-3">Description</td>  
                  <td class="col-xs-1">&nbsp;</td>  
                </tr>
             	</thead>
          	</table>
         	</div> 
       	</div>
        
          <div class="col-xs-12" style="max-height:130px; min-height:130px; overflow:hidden; overflow-y:auto; "> 	       
              <table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="table-layout: fixed;">
                <tbody id="bs_history_body">
                  <?php
                    
                    if(is_array($bs_data) && count($bs_data) > 0)
                    {
                      foreach($bs_data as $v)
                      {		
                          $hba1c = '';
                          if($v['hba1c'] != '' && $v['hba1c_val'] != '') {
                              $hba1c = $v['hba1c_val']. ' - ' .$v['hba1c'];
                          } else if($v['hba1c'] == '' && $v['hba1c_val'] != '') {
                              $hba1c = $v['hba1c_val'];
                          } else if($v['hba1c'] != '' && $v['hba1c_val'] == '') {
                              $hba1c = $v['hba1c'];
                          }
                        echo '<tr id="bs_row_'.$v['id'].'">';
                        echo '<td class="col-xs-2">'.$v['date'].'</td>';
                        echo '<td class="col-xs-1">'.$v['sugar_value'].'</td>';
                        echo '<td class="col-xs-2">'.$hba1c.'</td>';
                        echo '<td class="col-xs-1">'.$v['is_fasting'].'</td>';
                        echo '<td class="col-xs-2">'.$v['time_of_day'].'</td>';
                        echo '<td class="col-xs-3">'.$v['description'].'</td>';
                        echo '<td class="col-xs-1"><img src="'.$library_path.'/images/close_small.png" class="pointer" onclick="delete_blood_sugar('.$v['id'].', \'\','.$v['id'].');"></td>';
                        echo '</tr>';
                      }
                    }
                    else
                    {
                        echo '<tr><td class="alert alert-info" colspan="7">'.$bs_data.'</td></tr>';	
                    }
                  ?>
                </tbody>
              </table>
            </div>
        </div>
        
    	</div>
      
      <div class="modal-footer">
      	<button type="button" class="btn btn-success" onClick="save_blood_sugar();">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>

<!-- Patient Cholesterol Hx Modal -->
<div id="ch_history" class="modal" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title" id="modal_title">Patient Cholesterol History </h4>
     	</div>
      
      <div class="modal-body" style="min-height:450px;">
      	
        <div class="row">
        	<div class="col-xs-12 ">
          	<div class="headinghd">
            	<h4>Add New
            	<span class="pull-right glyphicon glyphicon-plus" onclick="top.fmain.add_cholesterol_row();">
              </span>
            	</h4></div>
          </div>
          
          <div class="col-xs-12">
          	<div id="ch_hx_header">
              <table class="table table-bordered table-hover table-striped scroll release-table margin_0">
                <thead class="grythead">
                  <tr>
                    <td class="col-xs-2">Date</td>
                    <td class="col-xs-1">Total</td>
                    <td class="col-xs-1">Trig</td>
                    <td class="col-xs-1">LDL</td>
                    <td class="col-xs-1">HDL</td>
                    <td class="col-xs-5">Description</td>  
                    <td class="col-xs-1">&nbsp;</td>  
                  </tr>
                </thead>
              </table>
            </div>
         	</div>
          
          <div class="col-xs-12" style="max-height:160px; min-height:160px; overflow:hidden; overflow-y:auto;">
           	<table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="table-layout: fixed;">	    
              <tbody id="ch_add_body">
              	<input type="hidden" name="cholesterol_rows" id="cholesterol_rows" value="1" />
              	<?php
									for($i = 1; $i < 2; $i++)
									{
										$html  = '';
										$html .= '<tr id="ch_add_row_'.$i.'">';
										//Date Column
										$html .= '<td class="col-xs-2">';
										$html .= '<div class="input-group">';
										$html .= '<input type="text" class="form-control datepicker" name="cholesterol_date'.$i.'" id="cholesterol_date'.$i.'" title="mm-dd-yy" onBlur="checkdate(this);" onClick="getDate_and_setToField(this)" onKeyUp="chk_change(\'\',this,event);" onChange="chk_change(\'\',this,event);" value="" />';
										$html .= '<label class="input-group-addon pointer" for="cholesterol_date'.$i.'"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>';
										$html .= '</div>';
										$html .= '</td>';
										
										//Cholesterol Total Column
										$html .= '<td class="col-xs-1">';
										$html .= '<input type="text" name="cholesterol_total'.$i.'" id="cholesterol_total'.$i.'" class="form-control" onKeyUp="chk_change(\'\',this,event);" value="" />';
										$html .= '</td>';
										
										//Cholesterol Triglycerides
										$html .= '<td class="col-xs-1">';
										$html .= '<input type="text" class="form-control" name="cholesterol_triglycerides'.$i.'" id="cholesterol_triglycerides'.$i.'" onKeyUp="chk_change(\'\',this,event);" value="" />';
										$html .= '</td>';
										
										
										//Cholesterol LDL
										$html .= '<td class="col-xs-1">';
										$html .= '<input type="text" name="cholesterol_LDL'.$i.'" class="form-control" id="cholesterol_LDL'.$i.'" onKeyUp="chk_change(\'\',this,event);" value="" />';
										$html .= '</td>';
										
										//Cholesterol HDL
										$html .= '<td class="col-xs-1">';
										$html .= '<input type="text" name="cholesterol_HDL'.$i.'" class="form-control" id="cholesterol_HDL'.$i.'" onKeyUp="chk_change(\'\',this,event);" value="" />';
										$html .= '</td>';
										
										//Cholesterol Description
										$html .= '<td class="col-xs-5">';
										$html .= '<textarea rows="1" cols="10" name="cholesterol_description'.$i.'" id="cholesterol_description'.$i.'" onKeyUp="chk_change(\'\',this,event);" onFocus="getDate_and_setToField($(\'#cholesterol_date'.$i.'\'));" class="form-control" ></textarea>';
										$html .= '</td>';
										
										//<img src="'.$library_path.'/images/close_small.png" class="pointer" onclick="delete_cholesterol(\'\', \'\','.$i.');">
										$html .= '<td class="col-xs-1"></td>';
										$html .= '</tr>';
										
										echo $html ;
										
									}
								?>
              </tbody>
            </table>
         	</div>
        </div>
        
      	<div class="row mt20">
        	<?php 
						$c_data = $health->hx_data('cholesterol');
						$paddingRight = (is_array($c_data) && count($c_data) > 4) ? 'style="padding-right:12px !important;"' : '';
					?>
        	<div class="col-xs-12">
          	<div id="ch_history_header" <?php echo $paddingRight;?> >
              <table class="table table-bordered table-hover table-striped scroll release-table margin_0">
                <thead class="grythead">
                  <tr>
                    <td class="col-xs-2">Date</td>
                    <td class="col-xs-1">Total</td>
                    <td class="col-xs-1">Trig</td>
                    <td class="col-xs-1">LDL</td>
                    <td class="col-xs-1">HDL</td>
                    <td class="col-xs-5">Description</td>  
                    <td class="col-xs-1">&nbsp;</td>  
                  </tr>
                </thead>
              </table>
         		</div> 
       		</div>
        
        	<div class="col-xs-12" style="max-height:130px; min-height:130px; overflow:hidden; overflow-y:auto; "> 	       
        		<table class="table table-bordered table-hover table-striped scroll release-table margin_0" style="table-layout: fixed;">
            	<tbody id="ch_history_body">
              	<?php
                	if(is_array($c_data) && count($c_data) > 0)
									{
										foreach($c_data as $v)
										{		
											echo '<tr id="ch_row_'.$v['id'].'">';
											echo '<td class="col-xs-2">'.$v['date'].'</td>';
											echo '<td class="col-xs-1">'.$v['cholesterol_total'].'</td>';
											echo '<td class="col-xs-1">'.$v['cholesterol_triglycerides'].'</td>';
											echo '<td class="col-xs-1">'.$v['cholesterol_LDL'].'</td>';
											echo '<td class="col-xs-1">'.$v['cholesterol_HDL'].'</td>';
											echo '<td class="col-xs-5">'.$v['description'].'</td>';
											echo '<td class="col-xs-1"><img src="'.$library_path.'/images/close_small.png" class="pointer" onclick="delete_cholesterol('.$v['id'].', \'\',\''.$v['id'].'\' );"></td>';
											echo '</tr>';
										}
									}
									else
									{
											echo '<tr><td class="alert alert-info" colspan="7">'.$c_data.'</td></tr>';	
									}
                ?>
              </tbody>
            </table>
          </div>
        </div>		
     	
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-success" onClick="save_cholesterol();">Save</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
      
    </div>
  </div>
</div>