<?php 
	require_once("../../admin_header.php");
	require_once('../../../../library/classes/admin/scheduler_admin_func.php');
?>
       <style>
		   .disabled{color: #939393}
</style>
        <script type="text/javascript">
		
			var arrAllShownRecords = new Array();
			var totalRecords	   = 0;
			var formObjects		   = new Array('id','status_name');
			function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Loading Schedule Status...');
				var filter = '';
				if($('#schedule_status option:selected').val() != '') {
					filter = $('#schedule_status option:selected').val();
				}
				var url_dt="ajax.php?task=show_list&filter="+filter                               
                $.ajax({
					type: "GET",
					url: url_dt,
					data: '',
					success:function(response){
						$("#result_set").html(response);
					}
				});
				top.show_loading_image('hide');
			}
			function checkInactive(){
				LoadResultSet();
			}
			function addNew(pkId,name,alias,status_color,all_form){
				if(typeof(pkId)!='undefined' && pkId!=''){
					$('#addNew_div .modal-header .modal-title').text('Edit Record');
					$('#status_id').val(pkId);
					$('#status_name').val(name);
					$('#status_alias').val(alias);
					$('#status_color').val(status_color);
					$(".grid_color_picker").spectrum("set", status_color);
					if(typeof(all_form)!='undefined' && all_form==1){$('#status_name').attr('readOnly',false); $('#status_alias').attr('readOnly',false);}
					else{$('#status_name').attr('readOnly',true); $('#status_alias').attr('readOnly',true);}	
				}
				else 
				{
					$('#addNew_div .modal-header .modal-title').text('Add New Record'); 
					$('#id').val(''); $('#status_id').val('');
					document.add_edit_frm.reset();
					$(".grid_color_picker").spectrum("set", '#FFF');
					$('#status_name').attr('readOnly',false); $('#status_alias').attr('readOnly',false);
				}
				$('#addNew_div').modal('show');
			}
			
			function saveFormData(){
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Saving data...');
				var frm_data = 'task=save_update';
				if($.trim($('#status_name').val())==""){
					top.fAlert("Please enter the Schedule Status.<br>");
					top.show_loading_image('hide');
					return false;
				}if($.trim($('#status_alias').val())==""){
					top.fAlert("Please enter the Schedule Status Alias.<br>");
					top.show_loading_image('hide');
					return false;
				}/*if($.trim($('#status_color').val())==""){
					top.fAlert("Please enter the Schedule Status Color.<br>");
					top.show_loading_image('hide');
					return false;
				}*/
				var sname=$('#status_name').val();
				frm_data +="&status_name="+sname;
				var salias=$('#status_alias').val();
				frm_data +="&status_alias="+salias;
				var scolor=$('#status_color').val();
				frm_data +="&status_color="+scolor;
				var sid=$('#status_id').val();
				frm_data +="&status_id="+sid;
				$.ajax({
					type: "POST",
					url: "ajax.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='enter_unique'){
							top.fAlert("Status '<b>"+sname+"</b>' or '<b>"+salias+"</b>' already exist.");		
							return false;
						}
						
						top.fAlert(d);
						
						$('.modal').modal('hide');
						LoadResultSet();
					}
				});
			}
			//to activate / deactivate procedure template
            function activeDeactive(id,mode){
				var url_dt="../admin/scheduler_admin/schedule_status/ajax.php?task=action&id="+id+"&mode="+mode; 
				top.master_ajax_tunnel(url_dt,LoadResultSet,'','','');		
            }
			
		</script>
	<div class="whtbox">
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="reason_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
        <table class="table table-bordered adminnw tbl_fixed">
            <thead>
                <tr>
                    <th class="col-sm-3">Schedule Status</th>
					<th class="col-sm-1">Color</th>
					<th class="col-sm-1">Alias</th>
					<th class="col-sm-1"><select name="schedule_status" id="schedule_status" class="minimal" style="width:100px" onChange="checkInactive(this.value);">
								<option value="all" selected>All</option>
								<option value="active">Active</option>
                                <option value="inactive">In-Active</option>
							</select></th>
                    <th class="col-sm-3">Schedule Status</th>
					<th class="col-sm-1">Color</th>
					<th class="col-sm-1">Alias</th>
					<th class="col-sm-1">Status</th>
                    
                </tr>
            </thead>
			<tbody id="result_set">
			</tbody>
        </table>
		
		
		<div id="addNew_div" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<form name="add_edit_frm" id="add_edit_frm"  onSubmit="saveFormData();return false;">
			  		<input type="hidden" name="status_id" id="status_id" value="">
				  <div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				  </div>
				  <div class="modal-body">
					<div class="row">
						<input type="hidden" name="id" id="id" >
						<div class="col-sm-6">
							<label>Schedule Status</label>
							<input type="text" name="status_name" id="status_name" class="form-control" value="" title="Schedule Status" required>	
						</div>
						<div class="col-sm-4">
							<label>Color</label>
							<input type="text" name="status_color" id="status_color" class="form-control grid_color_picker" value="" title="Color" required>	
						</div>
						<div class="col-sm-2">
							<label>Alias</label>
							<input type="text" name="status_alias" id="status_alias" class="form-control" value="" title="Alias" required>	
						</div>
					</div>
				  </div>
				  <div id="module_buttons" class="modal-footer ad_modal_footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
				  </div>
				</form>
			</div>
		  </div>
		</div>
	</div>
    <script type="text/javascript">
		LoadResultSet();
		var ar = [["add_new","Add New","top.fmain.addNew();"]];
		top.btn_show("ADMN",ar);
			$(document).ready(function(){
				check_checkboxes();
				set_header_title('Schedule Status');
			});	
		
	</script>
<?php 
	include('../../admin_footer.php');
?>