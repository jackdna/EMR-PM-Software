<?php

require_once("../../admin_header.php");
require_once('../../../../library/classes/admin/scheduler_admin_func.php');  
require_once('../../../../library/classes/class.language.php');  
$arrSchTempAlerts = array();
$objCore_lang = new core_lang();
$arrSchTempAlerts = $objCore_lang->get_vocabulary("admin", "SchedulerTemplates");
?>   
        <script type="text/javascript">  
			// JavaScript Document			
			function keyCatcher() 
			{
				var e = event.srcElement.tagName;
				if (event.keyCode == 8 && e != "INPUT" && e != "TEXTAREA") 
				{
					event.cancelBubble = true;
					event.returnValue = false;
				}
			}
			document.onkeydown = keyCatcher;	  
            
			function getScheduleTemplates(){
				var archive=0;
				$("#chk_sel_all").prop("checked",false);
				if($("#include_archive").prop("checked") == true)
				{
					archive=1;
				}
				
				var url_dt="load.php?archive="+archive;
                $.ajax({
					url:url_dt,
					type:'GET',
					success:function(response){
						$("#divLoadSchTmp").html(response);
                        top.show_loading_image('hide');
					}
				});
            }
			
			function delShcConfCheck(chk, pid){
				pid = pid || 0; //defaly 0
				if(chk == true){
					var delReason;
					if($("#hidd_reason_text")){delReason=$("#hidd_reason_text").val();}
					if($.trim(delReason)==""){top.fAlert('Please enter reason to move it to archive.');return false;	}
					delete_confirmed(pid, true,delReason);
				}
				else if(chk == false){
					return false
				}
			}
			function onBlur_reason(Reasonval){$("#hidd_reason_text").val(Reasonval);}
			
            function confirm_child_del(pid){
                var sel_id_arr = new Array;
				if(sel_id_arr.length == 0)
				{
					if(pid){sel_id_arr.push(pid);}
				}
				
				if(sel_id_arr.length > 0){
					var reason_field="<br /><br />Reason: <textarea class='form-control' style='vertical-align:text-top; overflow:auto;' onblur='top.fmain.onBlur_reason(this.value);'></textarea>";
					var arrConfFun = new Array();
					arrConfFun[0] = "top.fmain.delShcConfCheck(true, '"+escape(sel_id_arr)+"')";
					arrConfFun[1] = "top.fmain.delShcConfCheck(false)";		
					top.fancyConfirm('<?php echo $arrSchTempAlerts["del_schedule_template"]; ?>'+reason_field,'', arrConfFun[0], arrConfFun[1]);
				}else{
					top.fAlert('Please select atleast one record to continue !');
				}
                return false;
            }
			
			function confirm_del(){
                var sel_id_arr = new Array;
				
				$('.chk_sel').each(function(id,elem){
					if($(elem).is(':checked')){
						var value = $(elem).val();
						sel_id_arr.push(value);
					}
				});
				
				if(sel_id_arr.length > 0){
					var reason_field="<br /><br />Reason: <textarea class='form-control' style='vertical-align:text-top; overflow:auto;' onblur='top.fmain.onBlur_reason(this.value);'></textarea>";
					var arrConfFun = new Array();
					arrConfFun[0] = "top.fmain.delShcConfCheck(true, '"+escape(sel_id_arr)+"')";
					arrConfFun[1] = "top.fmain.delShcConfCheck(false)";		
					top.fancyConfirm('<?php echo $arrSchTempAlerts["del_schedule_template"]; ?>'+reason_field,'', arrConfFun[0], arrConfFun[1]);
				}else{
					top.fAlert('Please select atleast one record to continue !');
				}
                return false;
            }
			
            function delete_confirmed(pid,blStartDelProcess,delReason){
                var sel_id_arr = pid;
				
				if(sel_id_arr.length > 0){
					blStartDelProcess = blStartDelProcess || false; //defaly false
					var url_dt="delete.php";                                
					url_dt=url_dt+"?temp_id="+sel_id_arr;
					$.ajax({
						url:url_dt,
						type:'GET',
						success:function(response){
							if(response == "error"){                            
								top.fAlert('<?php echo $arrSchTempAlerts["cant_del_schedule_template"]; ?>');		
								return false;
							}else{							
								if(blStartDelProcess == false){
									var reason_field="<br /><br />Reason: <textarea class='form-control' style='vertical-align:text-top; overflow:auto;' onblur='top.fmain.onBlur_reason(this.value);'></textarea>";
									var arrConfFun = new Array();
									arrConfFun[0] = "top.fmain.delShcConfCheck(true, '"+escape(sel_id_arr)+"')";
									arrConfFun[1] = "top.fmain.delShcConfCheck(false)";		
									top.fancyConfirm('<?php echo $arrSchTempAlerts["del_schedule_template"]; ?>'+reason_field,'', arrConfFun[0], arrConfFun[1]);		
									return;
								}
								var doaction = blStartDelProcess;
								if(doaction){
									var url_del = "../admin/scheduler_admin/schedule_template/delete.php";                                
									url_del = url_del+"?temp_id="+sel_id_arr+"&action=delete&del_reason="+delReason;
									top.master_ajax_tunnel(url_del,getScheduleTemplates,'','','');
								}else{
									return false;
								}
							} 
						}
					});
				}else{
					top.fAlert('Please select atleast one record to continue !');
				}
                return false;
            }
            
			
			function confirm_restore(pid)
			{
				var arrConfFun = new Array();
				arrConfFun[0] = "top.fmain.restoreConfCheck(true, '"+escape(pid)+"')";
				arrConfFun[1] = "top.fmain.restoreConfCheck(false)";		
				top.fancyConfirm('Are you sure to restore this template ?', '', arrConfFun[0], arrConfFun[1]);
			}
			function restoreConfCheck(chk, pid){
				if(chk == true){
					if(pid){					
						var url_del = "../admin/scheduler_admin/schedule_template/delete.php";                                
						url_del = url_del+"?temp_id="+pid+"&action=restore";
						top.master_ajax_tunnel(url_del,getScheduleTemplates,'','','');
					}else{
						top.fAlert('Please select atleast one record to continue !');
					}
				}
				else if(chk == false){
					return false
				}
			}
			
            function edit(id,mode,tmp_parent_id){
				parent.parent.show_loading_image('none');
				if(mode != "inside"){
					var str_path = "../admin/scheduler_admin/schedule_template/";
				}else{
					var str_path = "";
				}
                if(id != ""){
					if($.trim(tmp_parent_id) != '')
					{
						url=str_path+'open.php?temp_parent_id='+tmp_parent_id+'&pro_id='+id;	
					}
					else
					{
	                    url=str_path+'open.php?pro_id='+id;						
					}
                }else{
                    url=str_path+'open.php';
                }
                /* if the window doesn't exist or has been closed, open it */
				var opened=window.open(url,'ap','width=1090px,height=600px,scrollbars=0,titlebar=0,menubar=no,resizable=0,location=no,left=180,top=100');
				opened.focus();
            }
			
			function open_child_temp(temp_parent_id)
			{
				url = "open.php?temp_parent_id="+temp_parent_id;
				window.open(url,'_blank','width=1100px,height=650px,scrollbars=0,titlebar=0,menubar=no,resizable=0,location=no,left=180,top=100');
			}	
			
			function hide_child_temp(ths)
			{
				cur_row = $(ths).parent().parent();		
				expand_status = cur_row.next().hasClass('sch_tmp_child_row');
				if(expand_status == true)
				{
					cur_row.parent().find('.sch_tmp_child_row').each(function(id,elem){
						$(elem).remove();
					});
					$(ths).prev().html('[+]');	
					$(ths).prev().attr({'title':'Expand'});													
				}	
			}
			
			function show_child_temp(sch_tmp_id, ths)
			{		
				cur_row = $(ths).parent().parent();		
				expand_status = cur_row.next().hasClass('sch_tmp_child_row');
				if(expand_status == true)
				{
					cur_row.parent().find('.sch_tmp_child_row').each(function(id,elem){
						$(elem).remove();
					});
					
					$(ths).html('[+]');	
					$(ths).attr({'title':'Expand'});													
				}
				else
				{
					$.ajax({
						url : 'load_child.php',
						type: 'POST',
						data: 'sch_tmp_id='+sch_tmp_id,
						complete:function(resultObj)
						{
							resultData = resultObj.responseText;
							cur_row.after(resultData);	
							$(ths).html('[-]&nbsp;');
							$(ths).attr({'title':'Collapse'});								
						}
					})
				}
			}
			
		$(document).ready(function() { 
		  $("input[name='include_archive']").click(function() {
			getScheduleTemplates();
		  });
		});
        </script>   
        <style type="text/css">
			div.temp_child_loading{padding:5px;text-align:center;}
			.bgCl3{background-color:#ddd;}
			.bgCl4{background-color:#eee;}			
		</style> 
		<div class="whtbox">
			<table class="table table-bordered adminnw tbl_fixed table-condensed">    
				<thead>
					<tr>
						<th class="text-center" style="width:3%">
							<div class="checkbox">
								<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="" autocomplete="off">
								<label for="chk_sel_all">
								</label>
							</div>
						</th>
						<th>Template Name</th>            
						<th>Template Timings</th>
						<th>Lunch Timings</th>
						<th>Min/Max Appts.</th>
						<th>Action <div class="checkbox pull-right"><input type="checkbox" name="include_archive" id="include_archive" value="1" ><label for="include_archive">Include Archive</label></div></th>
					</tr>
				</thead>
				<tbody id="divLoadSchTmp" ></tbody>
			</table>
		</div>
        
        <script type="text/javascript">
		/*btn name--btnvalue--argumentfor click*/
			top.show_loading_image('hide');
			check_checkboxes();
			//Btn --
			var ar = [["add_sch_template","Add New Schedule Template","top.fmain.edit('');"],['delete_id','Archive','top.fmain.confirm_del();']];
			top.btn_show("ADMN",ar);
			//Btn --
			set_header_title('Schedule Templates');
			getScheduleTemplates();
        </script>
<?php 
	include('../../admin_footer.php');
?>