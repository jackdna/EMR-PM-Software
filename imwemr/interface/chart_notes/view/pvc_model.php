<!-- Start Patient Communication Modal -->
<div id="pt_comm" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
  	<!-- Modal content-->
    <div class="modal-content">
    	<div class="modal-header bg-primary drag_cursor">
      	<button type="button" class="close" data-dismiss="modal">×</button>
        <div class="pull-right mlr10">
        	<div class="checkbox">
          	<input type="checkbox" id="chk" onChange="pt_comm_all(this);" data-toggle="tooltip" title="Patient Communication">
          	<label for="chk">View All</label>
          </div>
        </div>
        <h4 class="modal-title" id="modal_title">Patient Verbal Communication <span class="ml10 small bg-primary"><?php echo $pt_name; ?></span></h4> 
     	</div>
      
      <div class="modal-body" style="max-height:430px; overflow:hidden;">
      	
        <div class="row mt5" id="pt_comm_data" >
        	<?php echo patient_communication($patient_id,'view_active');?>
      	</div>
        
        <div class="clearfix"></div>
        
      	<div class="row mt10 panel panel-default" id="pt_comm_form">
        	<div class="panel-body">
        	<form name="pat_commun_form" id="pat_commun_form" action="" method="post">
          	<input type="hidden" name="edit_id"  id="edit_id" value=""/>
            <input type="hidden" id="mode" value="add"/>
            <div class="col-xs-12 col-sm-3">
            	<label>Subject :</label><br>
              <input type="text" name="pat_msg_sub" id="pat_msg_sub" value="" class="form-control" />
            </div>
            
            <div class="col-xs-12 col-sm-3">
            	<label>Date :</label><br>
              <div class="input-group">
              	<input type="text" name="pat_msg_date" id="pat_msg_date" value="<?php echo get_date_format(date('Y-m-d')); ?>"  data-toggle="tooltip" title="<?php echo $GLOBALS['date_format'];?>" class="form-control datepicker" />
                <label class="input-group-addon btn" for="pat_msg_date">
                	<i class="glyphicon glyphicon-calendar"></i>
                </label>
            	</div>    
            </div>
            
            <div class="col-xs-12 col-sm-3">
            	<label>Operator :</label><br>
              <input id="el_pvc_op" class="form-control" value="<?php echo $_SESSION['authProviderName'];?>" disabled/>
						</div>
            
            <div class="col-xs-12 col-sm-3">
            	<input type="hidden" name="approve_status" id="approve_status" value="accept" /><br>
            	<div class="radio radio-inline mt5">
              	<input type="radio" name="acc_rej" id="msg_accept" onclick="javascript:document.getElementById('approve_status').value='accept'" checked="checked" />
                <label for="msg_accept">Accept</label>
              </div>
              <div class="radio radio-inline mt5">
              	<input type="radio" onclick="javascript:document.getElementById('approve_status').value='decline'" name="acc_rej" id="msg_decline" value="decline" />
                <label for="msg_decline">Decline</label>
              </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-xs-12">
            	<label>Message :</label><br>
            	<textarea class="form-control" rows="3" name="pat_msg_txt" id="pat_msg_txt"></textarea>
						</div>
			            
       		</form>
          </div>
     		</div>
        
        
     	</div>
      
      <div class="modal-footer ad_modal_footer" id="module_buttons">
      	<!-- On Cancel = top.update_toolbar_icon(); -->
        <button type="button" id="txt_sbmt" class="btn btn-success" onClick="pt_comm_action();">Done</button>
        <button type="button" id="txt_cancel" class="btn btn-danger"  onclick="" data-dismiss="modal">Cancel</button>
     	</div>
      
    </div>
  </div>
</div>
<!-- End Patient Communication Modal -->