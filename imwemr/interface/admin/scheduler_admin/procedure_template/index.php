<?php
include_once("../../admin_header.php");
include_once('../../../../library/classes/admin/scheduler_admin_func.php');

//activate / deactivate
if(isset($_REQUEST['strAction']) && $_REQUEST['strAction'] == "act_deact"){
    $strMode = "yes";
    if($_REQUEST['strMode'] == "no"){
        $strMode = "yes";
        $msg = "activated.";
    }else{
        $strMode = "no";
        $msg = "deactivated.";
    }
    $strUpdQry = "
                UPDATE slot_procedures SET 
                    active_status = '".$strMode."' 
                WHERE id = '".$_REQUEST['intTempId']."'
                ";
    imw_query($strUpdQry);
    echo "<script>";
    echo "alert(\"The procedure template has been ".$msg."\");";
    echo "window.location='index.php';"."\n";
    echo "</script>";
}
?>
        <script type="text/javascript">
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
			
            //to activate / deactivate procedure template
            function activeDeactive(id,mode){
				var url_dt="../admin/scheduler_admin/procedure_template/activate.php?pro_id="+id+"&mode="+mode; 
				//reset procedure status to active only
				//$("#procedure_status select").val("active");
				$('#procedure_status option[value=active]').attr('selected','selected');
				top.master_ajax_tunnel(url_dt,getProcedureTemplates,'','','');			
            }


            //to activate / deactivate procedure template
            function delRecord(id,mode)
				{if(mode==1) { mode = 'del'; }
				var sel_id_arr = new Array;
				$('.chk_sel').each(function(id,elem){
					if($(elem).is(':checked')){
						var value = $(elem).val();
						sel_id_arr.push(value);
					}
				});
				var new_id = sel_id_arr.join(',');
				var url_dt="../admin/scheduler_admin/procedure_template/activate.php?pro_id="+new_id+"&mode="+mode; 
				top.master_ajax_tunnel(url_dt,getProcedureTemplates,'','','');		
			}
			function deleteRecord(id,mode){
				var modenew = mode;
				var sel_id_arr = new Array;
				$('.chk_sel').each(function(id,elem){
					if($(elem).is(':checked')){
						var value = $(elem).val();
						sel_id_arr.push(value);
					}
				});
				if(sel_id_arr.length > 0){
					if(mode=='del'){ 
						modenew=1; 
					}
					top.fancyConfirm('Confirm to delete procedure template?',"",'window.top.fmain.delRecord(\'\','+modenew+')');
				}else{
					top.fAlert('Please select atleast one group to continue !');
					return false;
				}
				
            }
            
            //loading procedure template(s) listing
			function getProcedureTemplates(){
				var url_dt="load.php";                                
                $.ajax({
					type: "GET",
					url: url_dt,
					data: '',
					success:function(response){
						$("#divLoadProTmp").html(response);
					}
				});
            } 
            
            //to add edit template
            function edit(id, mode){
				if(mode != "inside"){
					var str_path = "../admin/scheduler_admin/procedure_template/";
				}else{
					var str_path = "";
				}
                if(id != ""){
                    url=str_path+'open.php?pro_id='+id;
                }else{
                    url=str_path+'open.php';
                }
                /* if the window doesn't exist or has been closed, open it */
                if((typeof newtmpwin == 'undefined') || !(newtmpwin.open) || newtmpwin.closed){
					window.open(url,'ap2','width=1150,height=500,resizable =0,scrollbars=0,titlebar=0,menubar=no,location=no,left=10');
                }
            }  
        </script>
		<div class="whtbox">
			 <div class="text-center" id="loading_img" style="display:none; top:220px; left:470px; z-index:1000; position:absolute;"><img src="../../../../library/images/loading_image.gif"></div>
			<table class="table table-hover table-striped table-bordered adminnw">    
					<thead>
						<tr>
							<!--<th class="text-center" style="width:3%">
								<div class="checkbox">
									<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="" autocomplete="off">
									<label for="chk_sel_all">
									</label>
								</div>
							</th>-->    
							<th><select name="procedure_status" id="procedure_status" class="minimal" style="width:100px" onChange="checkInactive(this.value);">
								<option value="active">Active</option>
								<option value="all">All</option>
							</select>
							Procedure</th>    
							<th>Practice Code</th>
							<th class="">Type</th> 
							<th class="text-nowrap">APPT. DURATION</th>  
							<th class="text-nowrap">EXPECTED ARRIVAL</th>   							
							<th>Color</th>
							<th class="text-nowrap">Default Timings</th>
							<th class="text-nowrap">Max. Allowed</th>        
							<th>Procedure Message</th>
							<th>Referral Required</th>
							<th>Auth/Verify Req.</th>
                            <th>Billable</th>
							<th>Status</th>
						</tr>
					</thead>	
					<tbody id="divLoadProTmp"></tbody>
			</table>
		</div>
        <script type="text/javascript">
			var ar = [["add_sch_template","Add New Procedure Template","top.fmain.edit('');"]];
				//,["delete_sch_temp","Delete","top.fmain.deleteRecord('','del');"]
			top.btn_show("ADMN",ar);
			set_header_title('Procedure Templates');
            getProcedureTemplates(); 
			check_checkboxes();
			function checkInactive(val)
			{
				if(val=='all')
					{
						$('.inactive').removeClass('hide');
					}
				else
					{
						$('.inactive').addClass('hide');
					}
			}
        </script>
<?php
	include('../../admin_footer.php');
?>