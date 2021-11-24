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
include_once($GLOBALS['srcdir']."/classes/medical_hx/ocular.class.php");
include_once($GLOBALS['srcdir']."/classes/audit_common_function.php");
include_once $GLOBALS['srcdir'].'/classes/class.cls_review_med_hx.php';
$ocular = new Ocular($medical->current_tab);
$cls_review  = new CLSReviewMedHx();
extract($ocular->ocular_data);
//pre($ocular->ocular_data);
?>
<style type="text/css">
	 div.input-group-addon { max-height: 26px; padding: 0!important; background-color: transparent; border: solid 1px transparent; vertical-align:top;}
	 .checkbox { margin:4px 0 3px 0;}
	.input-group-addon .checkbox label::before { top:-1px;} 
	.input-group-addon .checkbox label::after { padding-top:0px; padding-left:2px;}
	.input-group-addon + .form-control:not(textarea){ margin-left:10px; width:85%;}
	.input-group-addon + textarea.form-control { margin-left:10px; width:96%; }
	.input-group-addon + div.addon-sub{  margin-left:10px; width:85%; display:inline-block!important;}
	.input-group-addon + div.addon-sub-other {margin-left:10px; width:96%; display:inline-block!important;}
</style>

<script>
	var as_exception_msg = '<?php echo $ocular->as_exception_msg; ?>';
	if(as_exception_msg)
	{
		top.fAlert("Unable to save patient data from All Scripts.<br />"+as_exception_msg ,"","window.top.core_redirect_to(\""+window.top.document.getElementById('curr_main_tab').value+"\",\"\")");
		top.document.getElementById("findBy").value = "Active";
		top.document.getElementById("findByShow").value = "Active";
		top.show_loading_image('hide');
	}
</script>

<form action="<?php echo $folder;?>/save.php" method="post" name="ocular_form" id="ocular_form">
	<input type="hidden" name="info_alert" id="info_alert" value="<?php echo ((is_array($ocular->ocular_vocabulary) && count($ocular->ocular_vocabulary) > 0) ? urlencode(serialize($ocular->ocular_vocabulary)) : "");?>">
  <input type="hidden" name="curr_tab" id="curr_tab" value="<?php echo $medical->current_tab;?>">
  <input type="hidden" name="preObjBack" id="preObjBack" value="">
  <input type="hidden" name="next_tab" id="next_tab" value="">
  <input type="hidden" name="next_dir" id="next_dir" value="">
  <input type="hidden" name="buttons_to_show" id="buttons_to_show" value="">			
  
  <div>
  	
    <div class="oculartop">
    	<div class="row">
      
      	<div class="col-lg-2 col-md-2 col-sm-12">
        	<div class="eyetst eyetstname">
          	<figure><img src="<?php echo $library_path ?>/images/eye_history.png" alt="Eye History"></figure>
           	<h2>EYE HISTORY</h2>
         	</div>
       	</div>
        
        <div class="col-lg-10 col-md-10 col-sm-12">
        	
          <div class="row">
          	<div class="col-lg-9 col-md-8 col-sm-12 simple_hd">
            	<span class="head">Do You wear</span>
              <div class="clearfix"></div>
              <div class="radio radio-inline">
              	<input type='radio' <?php echo $uwear0; ?> checked  onClick="chk_change('<?php echo addslashes($uwear0); ?>',this,event);" name='u_wear' id="u_wear_none" value="0">
                <label for="u_wear_none">None</label>
             	</div>   
              <div class="radio radio-inline">  
                <input type='radio' <?php echo $uwear1; ?> onClick="chk_change('<?php echo addslashes($uwear1); ?>',this,event);"  name='u_wear' id='u_wear_glasses' value="1">
                <label for="u_wear_glasses">Glasses</label>
              </div>   
              <div class="radio radio-inline">  
                <input type='radio' <?php echo $uwear2; ?> onClick="chk_change('<?php echo addslashes($uwear2); ?>',this,event);" name='u_wear' id="u_wear_lenses" value="2">
                <label for="u_wear_lenses">Contact Lenses</label>
              </div>   
              <div class="radio radio-inline">  
                <input type='radio' <?php echo $uwear3; ?> onClick="chk_change('<?php echo addslashes($uwear3); ?>',this,event);" name='u_wear' id="u_wear_glasses_lenses" value="3">
                <label for="u_wear_glasses_lenses">Glasses And Contact Lenses</label>
            	</div>
         		</div>
            
            <div class="col-lg-3 col-md-4 col-sm-12">
            	<span class="head">Last Eye Exam Date</span>
              <div class="clearfix"></div>
              <div class="input-group">
              	<input type="text" class="datepicker form-control" id="exam_date" name="exam_date" placeholder="Exam Date" onBlur="checkdate(this);" onChange="chk_change('<?php echo addslashes($last_eye_exam_date); ?>',this,event); checkdate(this);" onKeyUp="chk_change('<?php echo addslashes($last_eye_exam_date); ?>',this,event);" value="<?php echo $last_eye_exam_date; ?>" />
                <label class="input-group-addon btn" for="exam_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
             	</div>
           	</div>
        	</div>
    		
        </div>
    	
      </div>
  	</div>
    
    
    <div class="col-xs-12 ">
    	<div class="row">
        <div class="head">
          <span>Please mark any condition you have presently or have had in the past</span>
        </div>
				
				<div class="clearfix"></div>
      
      	<div class="row pt5">
           <!-- Dry Eyes -->
          <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="input-group">
              <div class="input-group-addon">
                <div class="checkbox">
                  <input type='checkbox' <?php echo $acya_p[1]; ?> name='any_conditions_u[]' id="any_conditions_u_1" value="1" onClick="chk_change('<?php echo addslashes($acya_p[1]); ?>',this,event); ">
                  <label for="any_conditions_u_1">Dry Eyes&nbsp;&nbsp;</label>
                </div>
              </div>
              <input type="text" name="elem_chronicDesc_1" onKeyUp="chk_change('<?php echo addslashes($elem_chronicDesc_1); ?>',this,event);" value="<?php echo html_entity_decode($elem_chronicDesc_1);?>" class="form-control" />	
            
            </div>
        	</div>
  
          <!-- Macular Degeneration -->    
          <div class="col-lg-4 col-md-4 col-sm-6 ">
            <div class="input-group">
            	<div class="input-group-addon">
              	<div class="checkbox">
                	<input type='checkbox' <?php echo $acya_p[2]; ?> name='any_conditions_u[]' id="any_conditions_u_2" value="2" onClick="chk_change('<?php echo addslashes($acya_p[2]); ?>',this,event); ">
                  <label for="any_conditions_u_2">Macular Degeneration</label>
              	</div>
            	</div>
              <input type="text" name="elem_chronicDesc_2" onKeyUp="chk_change('<?php echo addslashes($elem_chronicDesc_2); ?>',this,event);" value="<?php echo html_entity_decode($elem_chronicDesc_2);?>" class="form-control" />
          	</div>
         	</div>
          
          <div class="clearfix visible-sm mb5"></div>
          
          <!-- Glaucoma -->
          <div class="col-lg-4 col-md-4 col-sm-6 ">
            <div class="input-group">
            	<div class="input-group-addon">
                <div class="checkbox">
                  <input type='checkbox' <?php echo $acya_p[3]; ?> name='any_conditions_u[]' id="any_conditions_u_3" value="3" onClick="chk_change('<?php echo addslashes($acya_p[3]); ?>',this,event); ">
                  <label for="any_conditions_u_3">Glaucoma&nbsp;&nbsp;&nbsp;&nbsp;</label>
                </div>
              </div>
              
              <input type="text" name="elem_chronicDesc_3" onKeyUp="chk_change('<?php echo addslashes($elem_chronicDesc_3); ?>',this,event);" value="<?php echo html_entity_decode($elem_chronicDesc_3);?>" class="form-control" />
              
            </div>
          </div>
        
          <div class="clearfix hidden-sm mb5"></div>
          
          <!-- Cataracts -->
          <div class="col-lg-4 col-md-4 col-sm-6 ">
            <div class="input-group">
              <div class="input-group-addon">
                <div class="checkbox ">
                  <input type='checkbox' <?php echo $acya_p[5]; ?> name='any_conditions_u[]' id="any_conditions_u_5" value="5" onClick="chk_change('<?php echo addslashes($acya_p[5]); ?>',this,event); ">
                  <label for="any_conditions_u_5">Cataracts</label>
                </div>
              </div>
              
              <input type="text" name="elem_chronicDesc_5" onKeyUp="chk_change('<?php echo addslashes($elem_chronicDesc_5); ?>',this,event);" value="<?php echo html_entity_decode($elem_chronicDesc_5);?>" class="form-control" />
              
            </div>
          </div>
          
          <div class="clearfix visible-sm mb5"></div>
          
          <!-- Retinal Detachment -->
          <div class="col-lg-4 col-md-4 col-sm-6">
            <div class="input-group">
              <div class="input-group-addon">
              	<div class="checkbox">
                	<input type='checkbox' <?php echo $acya_p[4]; ?> name='any_conditions_u[]' id="any_conditions_u_4" value="4" onClick="chk_change('<?php echo addslashes($acya_p[4]); ?>',this,event); ">
                <label for="any_conditions_u_4">Retinal Detachment&nbsp;&nbsp;&nbsp;&nbsp;</label>
              	</div>
              </div>
              
              <input type="text" name="elem_chronicDesc_4" onKeyUp="chk_change('<?php echo addslashes($elem_chronicDesc_4); ?>',this,event);" value="<?php echo html_entity_decode($elem_chronicDesc_4);?>" class="form-control" />
              
            </div>
          </div>
          
          <!-- Keratoconus -->
          <div class="col-lg-4 col-md-4 col-sm-6 ">
            <div class="input-group">
              <div class="input-group-addon">
                <div class="checkbox">
                  <input type='checkbox' <?php echo $acya_p[6]; ?> name='any_conditions_u[]' id="any_conditions_u_6" value="6" onClick="chk_change('<?php echo addslashes($acya_p[6]); ?>',this,event); ">
                  <label for="any_conditions_u_6">Keratoconus</label>
                </div>
              </div>
							
              <input type="text" name="elem_chronicDesc_6" onKeyUp="chk_change('<?php echo addslashes($elem_chronicDesc_6); ?>',this,event);" value="<?php echo html_entity_decode($elem_chronicDesc_6);?>" class="form-control" />
              
            </div>
        	</div>    
            
        </div>
        
        <div class="row mt5">	  
          
          <!-- Others -->
          <div class="col-sm-12 ">
            <div class="input-group">
              <div class="input-group-addon">
                <div class="checkbox">
                  <input type='checkbox' <?php echo $aco_u_checked; ?> name='any_conditions_other_u' id="ocular_othr_chk" value="1" onClick="chk_change('<?php echo addslashes($aco_u_checked); ?>',this,event);">
                  <label for="ocular_othr_chk">Others&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                </div>
              </div>
              
              <textarea name="elem_chronicDesc_other" id="elem_chronicDesc_other" class="form-control" onKeyUp="chk_change('<?php echo addslashes(remLineBrk($elem_chronicDesc_other)); ?>',this,event);" onFocus="$('#ocular_othr_chk').prop('checked',true);"><?php echo html_entity_decode($elem_chronicDesc_other);?></textarea>
         		</div>
          </div>
          
        </div>
			
      </div>
   	</div>
    
    
    <div class="col-xs-12">
    	<div class="row pt10">
      	
        <div class="head">
        	<span>Please mark any condition your family member or blood relative have presently or have had in the past</span>
       	</div>
        
        <div class="clearfix"></div>
        
        
        <div class="row mt5">
        	
          
		  		<!-- Dry Eyes -->
        	<div class="col-lg-4 col-md-6 col-sm-12">
          	
          	<div class="input-group">
            	<div class="input-group-addon">
              	<div class="checkbox">
                	<input type='checkbox' id="ocular_fm_dryEyes_chk" onClick="chk_change('<?php echo addslashes($acra_p[1]); ?>',this,event);" <?php echo $acra_p[1]; ?> name='rel_any_conditions_relative[]' value="1" class="form-control" >
                  <label for="ocular_fm_dryEyes_chk">Dry Eyes&nbsp;&nbsp;</label>
               	</div>
            	</div>
              
              <div class="addon-sub">
              	<div class="row">
                	<div class="col-sm-6" id="div_elem_chronicRelative_1">
                  	<select class="selectpicker selectpicker_new" name="elem_chronicRelative_1[]" id="elem_chronicRelative_1" multiple data-title="Select Relation" data-width="100%">
                    	<?php echo $ocular->get_combo_multi($elem_chronicRelative_1,$arrPtRel); ?>
                  	</select>    
                	</div>
                  
                  <div class="col-sm-6 hidden" id="other_elem_chronicRelative_1">
                    <div class="input-group">	
                      <input type="text" name="rel_other_elem_chronicRelative_1" id="rel_other_elem_chronicRelative_1" value="<?php echo $other_elem_chronicRelative_1;?>" class="form-control">
                      <label class="input-group-addon btn btn-primary btn-xs back_other" data-tab-name="elem_chronicRelative_1">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                      </label>
                    </div>
            			</div>
              
                  <div class="col-sm-6">
                    <input type="text" name="rel_elem_chronicDesc_1" onKeyUp="chk_change('<?php echo addslashes($rel_elem_chronicDesc_1); ?>',this,event);" value="<?php echo html_entity_decode($rel_elem_chronicDesc_1);?>" class="form-control" />
                  </div>
            		</div>
             	</div>
                 	  
           	</div>
          </div>
          
          <div class="clearfix hidden-md hidden-lg mb5"></div>
          
          <!-- Macular Degeneration -->
        	<div class="col-lg-4 col-md-6 col-sm-12">
          	<div class="input-group">
            
            	<div class="input-group-addon">
              	<div class="checkbox">
                	<input type='checkbox' id="ocular_fm_macularDeg_chk" onClick="chk_change('<?php echo addslashes($acra_p[2]); ?>',this,event);" <?php echo $acra_p[2]; ?> name='rel_any_conditions_relative[]' value="2" class="form-control" >
                  <label for="ocular_fm_macularDeg_chk">Macular Degeneration</label>
               	</div>
            	</div>
              
              <div class="addon-sub">
              	<div class="row">
                	<div class="col-sm-6" id="div_elem_chronicRelative_2">
                    <select class="selectpicker selectpicker_new" name="elem_chronicRelative_2[]" id="elem_chronicRelative_2" multiple data-title="Select Relation" data-width="100%">
                      <?php
                        echo $ocular->get_combo_multi($elem_chronicRelative_2,$arrPtRel);
                      ?>
                    </select>    
              		</div>
              
                  <div class="col-sm-6 hidden" id="other_elem_chronicRelative_2">
                    <div class="input-group">	
                      <input type="text" name="rel_other_elem_chronicRelative_2" id="rel_other_elem_chronicRelative_2" value="<?php echo $other_elem_chronicRelative_2;?>" class="form-control">
                      <label class="input-group-addon btn btn-primary btn-xs back_other" data-tab-name="elem_chronicRelative_2">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                      </label>
                    </div>
                  </div>
              
                  <div class="col-sm-6">
                    <input type="text" name="rel_elem_chronicDesc_2" onKeyUp="chk_change('<?php echo addslashes($rel_elem_chronicDesc_2); ?>',this,event);" value="<?php echo html_entity_decode($rel_elem_chronicDesc_2);?>" class="form-control" />
                  </div>
             		</div>
             	</div>
                 	 
           	</div>
          </div>
          
          <div class="clearfix hidden-lg mb5"></div>
          
          <!-- Glaucoma -->
        	<div class="col-lg-4 col-md-6 col-sm-12">
          	<div class="input-group">
            	<div class="input-group-addon">
              	<div class="checkbox ">
                	<input type='checkbox' id="ocular_fm_glaucoma_chk" onClick="chk_change('<?php echo addslashes($acra_p[3]); ?>',this,event);" <?php echo $acra_p[3]; ?> name='rel_any_conditions_relative[]' value="3" class="form-control" >
                  <label for="ocular_fm_glaucoma_chk">Glaucoma&nbsp;&nbsp;&nbsp;&nbsp;</label>
               	</div>
            	</div>
              
              <div class="addon-sub">
              	<div class="row">
                	<div class="col-sm-6" id="div_elem_chronicRelative_3">
                    <select class="selectpicker selectpicker_new" name="elem_chronicRelative_3[]" id="elem_chronicRelative_3" multiple data-title="Select Relation" data-width="100%">
                      <?php
                        echo $ocular->get_combo_multi($elem_chronicRelative_3,$arrPtRel);
                      ?>
                    </select>    
                  </div>
              
                  <div class="col-sm-6 hidden" id="other_elem_chronicRelative_3">
                    <div class="input-group">	
                      <input type="text" name="rel_other_elem_chronicRelative_3" id="rel_other_elem_chronicRelative_3" value="<?php echo $other_elem_chronicRelative_3;?>" class="form-control">
                      <label class="input-group-addon btn btn-primary btn-xs back_other" data-tab-name="elem_chronicRelative_3">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                      </label>
                    </div>
                  </div>
              
                  <div class="col-sm-6">
                    <input type="text" name="rel_elem_chronicDesc_3" onKeyUp="chk_change('<?php echo addslashes($rel_elem_chronicDesc_3); ?>',this,event);" value="<?php echo html_entity_decode($rel_elem_chronicDesc_3);?>" class="form-control" />
                  </div>
              	</div>
            	</div>
                  
           	</div>
          </div>
      		
          <div class="clearfix hidden-md mb5"></div>
       			     
        	<!-- Cataracts -->
        	<div class="col-lg-4 col-md-6 col-sm-12">
          	<div class="input-group">
            	<div class="input-group-addon">
              	<div class="checkbox ">
                	<input type='checkbox' id="ocular_fm_cataracts_chk" onClick="chk_change('<?php echo addslashes($acra_p[5]); ?>',this,event);" <?php echo $acra_p[5]; ?> name='rel_any_conditions_relative[]' value="5" class="form-control" >
                  <label for="ocular_fm_cataracts_chk">Cataracts</label>
               	</div>
            	</div>
              
              <div class="addon-sub" >
              	<div class="row">
                	<div class="col-sm-6" id="div_elem_chronicRelative_5">
                    <select class="selectpicker selectpicker_new" name="elem_chronicRelative_5[]" id="elem_chronicRelative_5" multiple data-title="Select Relation" data-width="100%">
                      <?php
                        echo $ocular->get_combo_multi($elem_chronicRelative_5,$arrPtRel);
                      ?>
                    </select>    
             			</div>
                  
                  <div class="col-sm-6 hidden" id="other_elem_chronicRelative_5">
                    <div class="input-group">	
                      <input type="text" name="rel_other_elem_chronicRelative_5" id="rel_other_elem_chronicRelative_5" value="<?php echo $other_elem_chronicRelative_5;?>" class="form-control">
                      <label class="input-group-addon btn btn-primary btn-xs back_other" data-tab-name="elem_chronicRelative_5">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                      </label>
                    </div>
                  </div>
              
                  <div class="col-sm-6">
                    <input type="text" name="rel_elem_chronicDesc_5" onKeyUp="chk_change('<?php echo addslashes($rel_elem_chronicDesc_5); ?>',this,event);" value="<?php echo html_entity_decode($rel_elem_chronicDesc_5);?>" class="form-control" />
                  </div>
              	</div>
            	</div>    	
           	</div>
          </div>
		  		
          <div class="clearfix hidden-lg mb5"></div>
          
		   		<!-- Retinal Detachment -->
        	<div class="col-lg-4 col-md-6 col-sm-12">
          	<div class="input-group">
            
            	<div class="input-group-addon">
              	<div class="checkbox">
                	<input type='checkbox' id="ocular_fm_retinalDetachment_chk" onClick="chk_change('<?php echo addslashes($acra_p[4]); ?>',this,event);" <?php echo $acra_p[4]; ?> name='rel_any_conditions_relative[]' value="4" class="form-control" >
                  <label for="ocular_fm_retinalDetachment_chk">Retinal Detachment&nbsp;&nbsp;&nbsp;&nbsp;</label>
               	</div>
            	</div>
              
              <div class="addon-sub">
              	<div class="row">
                	
                  <div class="col-sm-6" id="div_elem_chronicRelative_4">
                    <select class="selectpicker selectpicker_new" name="elem_chronicRelative_4[]" id="elem_chronicRelative_4" multiple data-title="Select Relation" data-width="100%">
                      <?php
                        echo $ocular->get_combo_multi($elem_chronicRelative_4,$arrPtRel);
                      ?>
                    </select>    
                  </div>
              
                  <div class="col-sm-6 hidden" id="other_elem_chronicRelative_4">
                    <div class="input-group">	
                      <input type="text" name="rel_other_elem_chronicRelative_4" id="rel_other_elem_chronicRelative_4" value="<?php echo $other_elem_chronicRelative_4;?>" class="form-control">
                      <label class="input-group-addon btn btn-primary btn-xs back_other" data-tab-name="elem_chronicRelative_4">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                      </label>
                    </div>
                  </div>
              
                  <div class="col-sm-6">
                    <input type="text" name="rel_elem_chronicDesc_4" onKeyUp="chk_change('<?php echo addslashes($rel_elem_chronicDesc_4); ?>',this,event);" value="<?php echo html_entity_decode($rel_elem_chronicDesc_4);?>" class="form-control" />
                  </div>
              	</div>
             	</div>
              	
           	</div>
          </div>
          
          <div class="clearfix hidden-lg hidden-md mb5"></div>
          
          <!-- Keratoconus -->
        	<div class="col-lg-4 col-md-6 col-sm-12">
          	<div class="input-group">
            	<div class="input-group-addon">
              	<div class="checkbox ">
                	<input type='checkbox' id="ocular_fm_keratoconus_chk" onClick="chk_change('<?php echo addslashes($acra_p[6]); ?>',this,event);" <?php echo $acra_p[6]; ?> name='rel_any_conditions_relative[]' value="6" class="form-control" >
                  <label for="ocular_fm_keratoconus_chk">Keratoconus</label>
               	</div>
            	</div>
              
              <div class="addon-sub">
              	<div class="row">
                	
                  <div class="col-sm-6" id="div_elem_chronicRelative_6">
                    <select class="selectpicker selectpicker_new" name="elem_chronicRelative_6[]" id="elem_chronicRelative_6" multiple data-title="Select Relation" data-width="100%">
                      <?php
                        echo $ocular->get_combo_multi($elem_chronicRelative_6,$arrPtRel);
                      ?>
                    </select>    
                  </div>
              
                  <div class="col-sm-6 hidden" id="other_elem_chronicRelative_6">
                    <div class="input-group">	
                      <input type="text" name="rel_other_elem_chronicRelative_6" id="rel_other_elem_chronicRelative_6" value="<?php echo $other_elem_chronicRelative_6;?>" class="form-control">
                      <label class="input-group-addon btn btn-primary btn-xs back_other" data-tab-name="elem_chronicRelative_6">
                        <span class="glyphicon glyphicon-arrow-left"></span>
                      </label>
                    </div>
                  </div>
              
                  <div class="col-sm-6">
                    <input type="text" name="rel_elem_chronicDesc_6" onKeyUp="chk_change('<?php echo addslashes($rel_elem_chronicDesc_6); ?>',this,event);" value="<?php echo html_entity_decode($rel_elem_chronicDesc_6);?>" class="form-control" />
                  </div>
              	</div>
             	</div>
           	</div>
          </div>
      	</div>
        
        <div class="row mt5"> 	  
          <!-- Others -->
        	<div class="col-lg-12 col-md-12 col-sm-12">
          	<div class="input-group">
            
            	<div class="input-group-addon">
              	<div class="checkbox ">
                	<input type='checkbox' id="ocular_fm_others_chk" onChange="if(this.checked == false){document.getElementById('rel_elem_chronicDesc_other').value='';} chk_change('<?php echo addslashes($aco_relative_checked); ?>',this,event);" <?php echo $aco_relative_checked; ?> name='rel_any_conditions_other_r' value="1" class="form-control" >
                	<label for="ocular_fm_others_chk">Others&nbsp;&nbsp;&nbsp;&nbsp;</label>
               	</div>
            	</div>
              
              <div class="addon-sub-other">
              	
                
                <div class="col-sm-6 col-md-5 col-lg-3" id="div_elem_chronicRelative_other">
                  <div class="row">
                    <div class="col-md-2 visible-md">&nbsp;</div>
                    <div class="col-sm-12 col-md-10 col-lg-12">
                      <select class="selectpicker selectpicker_new pull-right" name="elem_chronicRelative_other[]" id="elem_chronicRelative_other" multiple data-title="Select Relation" data-width="100%">
                        <?php
                          echo $ocular->get_combo_multi($elem_chronicRelative_other,$arrPtRel);
                        ?>
                      </select>  
                    </div>
                  </div>           
                </div>
              
                <div class="col-sm-6 col-md-5 col-lg-3 hidden" id="other_elem_chronicRelative_other">
                  <div class="row">
                  	<div class="col-md-2 visible-md">&nbsp;</div>	
                    <div class="col-sm-12 col-md-10 col-lg-12">
                      <div class="input-group">	
                        <input type="text" name="rel_other_elem_chronicRelative_other" id="rel_other_elem_chronicRelative_other" value="<?php echo $other_elem_chronicRelative_other;?>" class="form-control">
                        <label class="input-group-addon btn btn-primary btn-xs back_other" data-tab-name="elem_chronicRelative_other">
                          <span class="glyphicon glyphicon-arrow-left"></span>
                        </label>
                      </div>
                    </div>
                  </div>    
                </div>
              
                <div class="col-sm-6 col-md-7 col-lg-9" style="padding:0!important;">
                  <textarea name="rel_elem_chronicDesc_other" id="rel_elem_chronicDesc_other" class="form-control pull-right" style="width:98%" rows="1" onKeyUp="chk_change('<?php echo addslashes(remLineBrk($rel_elem_chronicDesc_other)); ?>',this,event);" onFocus="$('#ocular_fm_others_chk').attr('checked',true);"><?php echo html_entity_decode($rel_elem_chronicDesc_other);?></textarea>
                </div>
              	
               	
            	</div>
              
          	</div>
          </div>
       	</div>
    	
      </div>
  	</div>
    
    <div class="col-xs-12">
    	<div class="row">
				<?php   
          $serialized = "";
          $serialized = $ocular->print_misc_question('ocular');
        ?>
    	</div>
		</div>
	
    <div class="col-xs-12">
    	<div class="row">
      	<?php $ocular->print_speacialty_question('ocular')  ?>
      </div>
    </div>
    
    <input type="hidden" name="hidDataMedicalHistory_Ocular" value="<?php echo $serialized; ?>">
    <input type="hidden" name="policyStatus" value="<?php echo $ocular->policy_status; ?>"/>
    
		<?php 
			$opreaterId = $_SESSION['authId'];
			if(count($ocular->ocular_data) == 0){
				$action = "add";
			}
			else{
				$action = "update";
			}
			$arrReview_Ocular = array();
			$arrReview_Ocular = $cls_review->getReviewArrayOcular($ocular->ocular_data,$elem_chronicDesc_other,$rel_elem_chronicDesc_other,$opreaterId,$action);
		?>
    <input type="hidden" name="hid_arr_review_ocular" value="<?php echo urlencode(serialize($arrReview_Ocular)); ?>"/>
			
	</div>

</form>

<?php
$policyStatus = (int)$_SESSION['AUDIT_POLICIES']['Patient_record_Created_Viewed_Updated'];

//--- AUDIT TRIAL CODE FOR VIEW OCCULAR
if($policyStatus == 1 and isset($_SESSION['Patient_Viewed']) == true){
	$opreaterId = $_SESSION["authId"];												 
	$ip = getRealIpAddr();
	$URL = $_SERVER['PHP_SELF'];													 
	//$os = get_os_($_SERVER['HTTP_USER_AGENT']);
	$os = getOS();
	$browserInfoArr = array();
	$browserInfoArr = _browser();
	$browserInfo = $browserInfoArr['browser'] . "-" .$browserInfoArr['version'];
	$browserName = str_replace(";","",$browserInfo);													 
	$machineName = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$arrAuditTrailView_Ocular = array();
	$arrAuditTrailView_Ocular[0]['Pk_Id'] = $ocular_id;
	$arrAuditTrailView_Ocular[0]['Table_Name'] = 'ocular';
	$arrAuditTrailView_Ocular[0]['Action'] = 'view';
	$arrAuditTrailView_Ocular[0]['Operater_Id'] = $opreaterId;
	$arrAuditTrailView_Ocular[0]['Operater_Type'] = getOperaterType($opreaterId);
	$arrAuditTrailView_Ocular[0]['IP'] = $ip;
	$arrAuditTrailView_Ocular[0]['MAC_Address'] = $_REQUEST['macaddrs'];
	$arrAuditTrailView_Ocular[0]['URL'] = $URL;
	$arrAuditTrailView_Ocular[0]['Browser_Type'] = $browserName;
	$arrAuditTrailView_Ocular[0]['OS'] = $os;
	$arrAuditTrailView_Ocular[0]['Machine_Name'] = $machineName;
	$arrAuditTrailView_Ocular[0]['Category'] = 'patient_info-medical_history';
	$arrAuditTrailView_Ocular[0]['Filed_Label'] = 'Patient Ocular Data';
	$arrAuditTrailView_Ocular[0]['Category_Desc'] = 'ocular';
	$arrAuditTrailView_Ocular[0]['pid'] = $ocular->patient_id;
	$patientViewed = $_SESSION['Patient_Viewed'];	
	//$patientViewed["Medical History"]["Ocular"];
	if(is_array($patientViewed) && $patientViewed["Medical History"]["Ocular"] == 0){
		auditTrail($arrAuditTrailView_Ocular,$mergedArray);
		$patientViewed["Medical History"]["Ocular"] = 1;			
		$_SESSION['Patient_Viewed'] = $patientViewed;
	}
}


if(trim($_SESSION['alertShowForThisSession']) != "Cancel")
{	
	require_once($GLOBALS['srcdir'].'/classes/CLSAlerts.php');
	$OBJPatSpecificAlert = new CLSAlerts();
	$alertToDisplayAt = "admin_specific_chart_note_med_hx";
	echo $OBJPatSpecificAlert->getAdminAlert($ocular->patient_id,$alertToDisplayAt,$form_id,"350px","100px",'',"no");	
	$alertToDisplayAt = "patient_specific_chart_note_med_hx";
	echo $OBJPatSpecificAlert->getPatSpecificAlert($ocular->patient_id,$alertToDisplayAt,"350px");
	echo $OBJPatSpecificAlert->autoSetDivLeftMargin("140","265");
	echo $OBJPatSpecificAlert->autoSetDivTopMargin("250","30");
	echo $OBJPatSpecificAlert->writeJS();
}

if($_SESSION['alertShowForMedication']=='')
{ 
	require_once($GLOBALS['srcdir'].'/classes/CLSAlerts.php');
	$OBJPatSpecificAlert = new CLSAlerts();
	echo ($OBJPatSpecificAlert->alertMedications($ocular->patient_id,$alertToDisplayAt,"140px","100px"));
	$_SESSION['alertShowForMedication']='DONE';
}
?>

</body>
</html>
<script type="text/javascript" src="<?php echo $library_path; ?>/js/ocular.js"></script>
<script type="text/javascript">
	var current_tab = document.getElementById('curr_tab').value;
	top.btn_show("OCU");
</script>