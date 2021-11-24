		<div class="row">
        	<div class="col-sm-3">
            <?php echo '<h4><b>'.$dbtemplate_name_show.'</b></h4>';?>
            </div>
            
            <?php if(in_array($mur_version,array('2017','2018','2019','2020'))){
			$fac_tins_ar = $objMUR->get_tin_options2();
			if($fac_tins_ar){?>
            <!-- ACI 2017/2018; Showing TIN number drop down -->
            <!--<div class="col-sm-1"></div>-->
            <div class="col-sm-2" id="tin_div">
            	<label for="task">TIN</label>
            	<select class="selectpicker show-menu-arrow" name="facility_id_multi" id="facility_id_multi" data-live-search="false" data-width="100%" data-actions-box="true" multiple>
                 <?php 
				//echo '<option value="" title="">Nothing Selected</option>';
                 foreach($fac_tins_ar as $fac_id=>$fac_TinRS){
					 $fac_TIN = $fac_TinRS['fac_tin'];
					 $fac_TINname = $fac_TinRS['name'];
					echo '<option value="'.$fac_id.'" title="'.$fac_TINname.'">'.$fac_TIN.'</option>'; 
				 }?>
                </select>
            </div>     
            <?php }
			}?>
            
        	<div class="col-sm-2">
			<?php
                $option_pro_arr = $objMUR->get_provider_ar(0);
				$options_str = '';
				$all_provs_ar = array();
                foreach($option_pro_arr as $OptphyId=>$OptphyRS){
					$OptphyName = $OptphyRS['name'];
					$OptphyNPI  = $OptphyRS['npi'];
					if(!empty($OptphyNPI)){
	                    $all_provs_ar[] = $OptphyId;
						$options_str .= '<option value="'.$OptphyId.'">'.$OptphyNPI.' - '.$OptphyName.'</option>';
					}
                }
				$all_provs_str = implode(',',$all_provs_ar);
				//$options_str = '<option value="'.$all_provs_str.'">-- ALL --</option>'.$options_str;
             ?>
                <label for="provider">NPI</label>
            	<!--<select class="form-control minimal" name="provider" id="provider">-->
                <select class="selectpicker show-menu-arrow"  nme="provider_multi" id="provider_multi" data-live-search="false" data-width="100%" data-actions-box="true" multiple>
                 <?php echo $options_str;?>                 
                </select>
            </div>

        	<div class="col-sm-1">
            	<label for="dtfrom">Date From</label>
            	<input type="text" name="dtfrom" id="dtfrom" size="11" maxlength="10" class="form-control date-pick" onBlur="checkdate(this);" value="<?php echo date(phpDateFormat(), strtotime(date('Y/m/1'))); ?>" />
            </div>
        	<div class="col-sm-1">
                <label for="dtupto">To</label>
                <input type="text" name="dtupto" id="dtupto" size="11" maxlength="10" class="form-control date-pick" onBlur="checkdate(this);" value="<?php echo date(phpDateFormat());?>" />
            </div>
            
            <input type="hidden" name="task" id="task" value="1">
            <div class="col-sm-1 text-center"><label>&nbsp;</label><br><input type="button" class="btn btn-success" value="Get Report" onClick="searchResult()"></div>
            <div class="col-sm-1 text-center pull-right" style="margin-right:20px;"><label>&nbsp;</label><br><input type="button" class="btn btn-success" value="Download eRx" onClick="searchResult(6)"></div>
      </div>