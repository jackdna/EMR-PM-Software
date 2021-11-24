<?php 
	require_once("../../admin_header.php");
	require_once('../../../../library/classes/admin/scheduler_admin_func.php');
?>
        <script type="text/javascript">
		
			var arrAllShownRecords = new Array();
			var totalRecords	   = 0;
			var formObjects		   = new Array('reason_id','reason_name');
			function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Loading Schedule Reasons...');
				
				if(typeof(s)!='string' || s==''){s = 'Active';}
				s_url = "&s="+s;
				
				if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
				if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
				
				oso		= $('#ord_by_field').val(); //old_so
				soAD	= $('#ord_by_ascdesc').val();
				if(typeof(so)=='undefined' || so==''){
					so 		= $('#ord_by_field').val();
				}else{
					$('#ord_by_field').val(so);
					if(oso==so){
						if(soAD=='ASC') soAD = 'DESC';
						else  soAD = 'ASC';
					}else{
						soAD = 'ASC';
					}
					$('#ord_by_ascdesc').val(soAD);
				};
				$('.link_cursor span').removeAttr('class');
				if(soAD=='ASC')	$(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
				else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
				
				so_url='&so='+so+'&soAD='+soAD;
						
				ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
				$.ajax({
				  url: ajaxURL,
				  success: function(r) {/*dataType: "json",*/
					showRecords(r);
				  }
				});
			}
			function showRecords(r){
			 	r = $.parseJSON(r);
				result = r.records;
				h='';var no_record='yes';
				if(r != null){
					row = '';
					row += '<tr class="link_cursor">';
					for(x in result){no_record='no';
						s = result[x];
						rowData = new Array();
						for(y in s){
							tdVal = s[y];
							
							if(y=='reason_id'){pkId = tdVal; row += '<td class="text-center"><div class="checkbox"><input type="checkbox" name="id" class="chk_sel" id="chk_sel_'+pkId+'" value="'+tdVal+'"><label for="chk_sel_'+pkId+'"></label></div></td>';}
							rowData[y] = tdVal;
							if(y=='reason_name'){
								row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
							}
						}
						totalRecords++;
						if(totalRecords%2==0)row += '</tr><tr>';
						arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
					}
					h = row;
				}
				if(no_record=='yes'){h+="<tr><td colspan='2' style='text-align:center;'>No Record Found</td></tr>";}
				$('#result_set').html(h);		
				top.show_loading_image('hide');
			}
			function addNew(ed,pkId){
				if(typeof(ed)!='undefined' && ed!=''){$('#addNew_div .modal-header .modal-title').text('Edit Record');}
				else {$('#addNew_div .modal-header .modal-title').text('Add New Record'); $('#reason_id').val(''); document.add_edit_frm.reset();}
				$('#addNew_div').modal('show');
				if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
			}
			function saveFormData(){
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Saving data...');
				frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
				if($.trim($('#reason_name').val())==""){
					top.fAlert("Please enter the Schedule Reason.<br>");
					top.show_loading_image('hide');
					return false;
				}
				var rname=$('#reason_name').val();
				$.ajax({
					type: "POST",
					url: "ajax.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='enter_unique'){
							top.fAlert("Reason '<b>"+rname+"</b>' already exist.");		
							return false;
						}
						if(d.toLowerCase().indexOf('success') > 0){
							top.alert_notification_show(d);
						}else{
							top.fAlert(d);
						}
						$('.modal').modal('hide');
						LoadResultSet();
					}
				});
			}
			function deleteSelectet(){
				pos_id = '';
				$('.chk_sel').each(function(){
					if($(this).is(':checked')){
						pos_id += $(this).val()+', ';
					}
				});
				if(pos_id!=''){
					top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.deleteModifiers('"+pos_id+"')");
				}else{
					top.fAlert('Please select a record to continue !');
				}
			}
			function deleteModifiers(pos_id) {
				pos_id = pos_id.substr(0,pos_id.length-2);
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Deleting Record(s)...');
				frm_data = 'pkId='+pos_id+'&task=delete';
				$.ajax({
					type: "POST",
					url: "ajax.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
						else{top.fAlert(d+'Record delete failed. Please try again.');}
					}
				});
			}
			function fillEditData(pkId){
				f = document.add_edit_frm;
				e = f.elements;
				add_edit_frm.reset();
				$('#reason_id').val(pkId);
				for(i=0;i<e.length;i++){
					o = e[i];
					if($.inArray(o.phrase,formObjects)){
						on	= o.name;
						v	= arrAllShownRecords[pkId][on];
						if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
							if (o.type == "checkbox" || o.type == "radio"){
								oid = on+'_'+v;
								$('#'+oid).attr('checked',true);
							} else if(o.type!='submit' && o.type!='button'){
								o.value = v;
							}
						}
					}
				}		
			}
		</script>
	<div class="whtbox">
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="reason_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
        <table class="table table-bordered adminnw tbl_fixed">
            <thead>
                <tr>
					<th class="text-center col-sm-1">
						<div class="checkbox">
							<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
							<label for="chk_sel_all">
							</label>
						</div>
					</th>
                    <th onClick="LoadResultSet('','','','reason_name',this);" class="col-sm-5">Schedule Reason<span></span></th>
					<th class="text-center col-sm-1">&nbsp;</th>
                    <th onClick="LoadResultSet('','','','reason_name',this);" class="col-sm-5">Schedule Reason<span></span></th>
                    
                </tr>
            </thead>
			<tbody id="result_set">
			</tbody>
        </table>
		
		
		<div id="addNew_div" class="modal fade" role="dialog">
		  <div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<form name="add_edit_frm" id="add_edit_frm" style="margin:0px;" onSubmit="saveFormData();return false;">
				  <div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				  </div>
				  <div class="modal-body">
					<div class="row">
						<input type="hidden" name="reason_id" id="reason_id" >
						<div class="col-sm-12">
							<label>Schedule Reason&nbsp;</label>
							<textarea name="reason_name" id="reason_name" class="form-control" style="overflow:auto;"></textarea>	
						</div>
					</div>
				  </div>
				  <div id="module_buttons" class="modal-footer ad_modal_footer">
					<button type="submit" class="btn btn-success">Done</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				  </div>
				</form>
			</div>
		  </div>
		</div>
	</div>
    <script type="text/javascript">
		LoadResultSet();
		var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
		top.btn_show("ADMN",ar);
			$(document).ready(function(){
				check_checkboxes();
				set_header_title('Schedule Reasons');
			});	
		
	</script>
<?php 
	include('../../admin_footer.php');
?>