<?php 
include('../../admin_header.php');
include('../../../../library/classes/cls_common_function.php'); 
include('../../../../library/classes/admin/scheduler_admin_func.php');  

$obj_common_function = new CLSCommonFunction;

//get procedure list
$pro_q=imw_query("select id, proc from slot_procedures WHERE times = '' AND proc != '' AND doctor_id = 0 AND active_status='yes' and source='' order by proc asc");
while($data=imw_fetch_object($pro_q))
{
	$proc_arr[$data->id]=$data->proc;	
}
$obj_common_function->drop_down_providers($sel_pro,'1','');
//get flow default setting
$flowQuery=imw_query("select master_setting from chain_event where master_setting!=0 LIMIT 0,1");
$flowData=imw_fetch_object($flowQuery);
//check if we do have settings otherwise default will be 1
$defaultFlow=($flowData->master_setting)?$flowData->master_setting:1;
?>
        <script type="text/javascript">
			var master_setting ='<?php echo $defaultFlow;?>';
			var arrAllShownRecords = new Array();
			var totalRecords	   = 0;
			var formObjects		   = new Array('event_id','event_name');
			function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Loading Chain Events...');
				
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
				//so 		= 'pos_prac_code';
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
				r = jQuery.parseJSON(r);
				result = r.records;
				h='';var no_record='yes';
				if(r != null){
					row = '';
					row += '<tr>';
					for(x in result){no_record='no';
						s = result[x];
						rowData = new Array();
						for(y in s){
							tdVal = s[y];
							if(y=='event_id'){pkId = tdVal; row += '<td class="text-center"><div class="checkbox"><input type="checkbox" name="id" id="chk_sel_'+pkId+'" class="chk_sel" value="'+tdVal+'"><label for="chk_sel_'+pkId+'"></label></td>';}
							if(y=='event_name'){
								row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
								arrAllShownRecords[pkId] = tdVal;//this array will be used to fill edit record data in form.
							}
						}
						totalRecords++;
						if(totalRecords%2==0)row += '</tr><tr>';
						//arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
					}
					h = row;
				}
				if(no_record=='yes'){h+="<tr><td colspan='2'>"+imw_msg_js('no_rec')+"</td></tr>";}
				$('#result_set').html(h);		
				top.show_loading_image('hide');
			}
			function addNew(ed,pkId){
				if(typeof(ed)!='undefined' && ed!=''){$('#addNew_div .modal-header .modal-title').text('Edit Record');}
				else {$('#addNew_div .modal-header .modal-title').text('Add New Record'); $('#event_id').val(''); $('#add_edit_frm')[0].reset();
				$('#add_edit_frm .selectpicker').selectpicker('val','').selectpicker('refresh');}
				$('#addNew_div').modal('show');
				if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
				if(master_setting==3){$("#consolidation_time_div").show();}else{$("#consolidation_time_div").hide();}
			}
			function saveFormData(){
				frm_data = $('#add_edit_frm').serialize()+'&task=save&master_setting='+master_setting;
				var rname=$('#event_name').val();
				var have_provider=false;
				if(($.trim(rname))=='')
				{
					top.fAlert('Please enter event name');
					return false;
				}
				//#row 1
				if($.trim($("#procedure_0").val()))
				{
					if($("#provider_0").val())have_provider=true;
				}
				//#row 2
				if($.trim($("#procedure_1").val()))
				{
					if($("#provider_1").val())have_provider=true;
				}
				//#row 3
				if($.trim($("#procedure_2").val()))
				{
					if($("#provider_2").val())have_provider=true;
				}
				//#row 4
				if($.trim($("#procedure_3").val()))
				{
					if($("#provider_3").val())have_provider=true;
				}
				//#row 5
				if($.trim($("#procedure_4").val()))
				{
					if($("#provider_4").val())have_provider=true;
				}
				
				if(have_provider==false)
				{
					top.fAlert('Please mention atleast one provider');
					return false;
				}
				
				
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Saving data...');
				
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
				})
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
						else{top.fAlert(d+' Record delete failed. Please try again.');}
					}
				});
			}
			function fillEditData(pkId){
				$('#add_edit_frm')[0].reset();
				$('#event_id').val(pkId);
				$('#event_name').val(arrAllShownRecords[pkId]);
				ajaxURL = "ajax.php?task=item_detail&id="+pkId;
				$.ajax({
				  url: ajaxURL,
				  success: function(r) {/*dataType: "json",*/
				  	r = jQuery.parseJSON(r);
					result = r.records;
					h='';var no_record='yes';
					if(r != null){
							c=0;
						for(x in result){no_record='no';
							s = result[x];
							for(y in s){
								tdVal = s[y];
								if(y=='procedure_id')$("#procedure_"+c).val(tdVal);
								else $("#provider_"+c).val(tdVal);
							}
							c++;
						}
					}
					$("#consolidation_time").val(r.consolidation_time);
					$('.selectpicker').selectpicker('refresh');
				}
				});	
			}
			
			function saveSettings()
			{
				top.show_loading_image('hide');
				top.show_loading_image('show','300', 'Saving Setting...');
				
				master_setting = $("input:radio[name ='master_setting']:checked").val();
				frm_data = 'setting='+master_setting+'&task=master_setting';
				
				$.ajax({
					type: "POST",
					url: "ajax.php",
					data: frm_data,
					success: function(d) {
						top.show_loading_image('hide');
						top.fAlert('Setting saved sucessfully.');
					}
				});
			}
		</script>
		
		
		<div class="whtbox">
		<!-- master setting form -->
			<form>
				<div class="" id="chain_event_master_setting">
					<table class="table table-striped table-bordered table-condensed adminnw">
					<thead>
						<tr class="page_block_heading_patch">
							<th colspan="4">Record selection flow setting</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="col-lg-2">
								<div class="radio radio-inline">
									<input tabindex="1" name="master_setting" id="event_regular" type="radio" value="1" autocomplete="off" <?php echo($defaultFlow==1)?'checked':''; ?>>
									<label for="event_regular">Regular</label>
								</div>
							</td>
							<td class="col-lg-2">
								<div class="radio radio-inline">
									<input tabindex="1" name="master_setting" id="event_waterflow" type="radio" value="2" autocomplete="off" <?php echo($defaultFlow==2)?'checked':''; ?>>
									<label for="event_waterflow">Waterflow</label>
								</div>
							</td>
							<td class="col-lg-2">
								<div class="radio radio-inline">
									<input tabindex="1" name="master_setting" id="event_consolidated" type="radio" value="3" autocomplete="off" <?php echo($defaultFlow==3)?'checked':''; ?>>
									<label for="event_consolidated">Consolidated</label>
								</div>
							</td>
							<td class="text-left">
								<button type="button" name="save_setting" value="save_setting" class="btn btn-success" onClick="saveSettings();">Save Flow Setting</button>
							</td>
						</tr>
					</tbody>
					</table>
				</div>
			</form>
			<!-- sepration line -->
			<div>
				<hr>
			</div>
			<!-- chain event records -->
			<input type="hidden" name="ord_by_field" id="ord_by_field" value="event_name">
			<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
			<table class="table table-striped table-bordered table-condensed adminnw">
				<thead>
					<tr class="page_block_heading_patch">
						<th class="text-center col-sm-1"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th class="col-sm-5">Chain Events</th>
						<th class="text-center col-sm-1">&nbsp;</th>
						<th class="col-sm-5">Chain Events</th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
		<?php
			$content_opt = '';
			 foreach($proc_arr as $id=>$name){
				$content_options .= "<option value=".$id.">".$name."</option>";	  
			}
			for($loop=0;$loop<=4;$loop++){
				$content_opt .= '<div class="row pt10"><div class="col-sm-6">';
				$content_opt .= '<select class="selectpicker" data-width="100%" data-size="6" name="procedure_'.$loop.'" id="procedure_'.$loop.'">
				  <option value="">-- Procedure --</option>';
				$content_opt .= $content_options;
				$content_opt .= '</select></div>';
			
				$content_opt .= '<div class="col-sm-6"><select class="selectpicker" data-width="100%" data-size="6" name="provider_'.$loop.'" id="provider_'.$loop.'">
						<option value="">-- All Physician --</option>';
				$content_opt .= $obj_common_function->drop_down_providers($sel_pro,'1','');
				$content_opt .= '</select></div></div>';
			}
		?>
<div id="addNew_div" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<form name="add_edit_frm" id="add_edit_frm" style="margin:0px;" onSubmit="return saveFormData();return false;">
			<input type="hidden" name="event_id" id="event_id" >
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Modal Header</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						
						<div class="col-sm-12 text-center pt10">
							<div class="row">
								<div class="col-sm-4 text-left">
									Chain Event Name
								</div>	
								<div class="col-sm-8 text-left">
									<input type="text" name="event_name" id="event_name" value="" placeholder="Chain Event Name" class="form-control">
								</div>
							</div>
							
							<div class="row pt10" id="consolidation_time_div">
								<div class="col-sm-4 text-left">
									Consolidation Time (Hour)
								</div>	
								<div class="col-sm-8 text-left">
									<select name="consolidation_time" id="consolidation_time" class="selectpicker">
										<?php for($i=1;$i<=10;$i++){
											echo"<option value='$i'>$i</option>";
										} ?>
										
									</select>
								</div>
							</div>	
						</div>
						
						<div class="col-sm-12 text-center pt10">
							<div class="row head">
								<div class="col-sm-6">
									<span>Procedure</span>
								</div>	
								<div class="col-sm-6">
									<span>Provider</span>
								</div>
							</div>	
						</div>
						<div class="col-sm-12">
							<?php echo $content_opt; ?>
						</div>	
					</div>
				</div>
				<div id="module_buttons" class="modal-footer ad_modal_footer">
					<input type="submit" class="btn btn-success" value="Done">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</div>
		</form>
	</div>
</div>
    <script type="text/javascript">
		LoadResultSet();
		var ar = [["add_new","Add New","top.fmain.addNew();"],
		   	  ["dx_cat_del","Delete","top.fmain.deleteSelectet();"]
			 ];
		top.btn_show("ADMN",ar);
		$(document).ready(function(){
			check_checkboxes();
			set_header_title('Chain Event');
		});
		show_loading_image('none');
	</script>
<?php
	include('../../admin_footer.php');
 ?>	
       