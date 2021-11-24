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
require_once("../admin_header.php");
?>
<script type="text/javascript">
	var arrAllShownRecords = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('phrase_id','phrase');
	function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading iPortal Security Questions...');
		
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
				
		ajaxURL = "ajax_security_questions.php?task=show_list"+s_url+p_url+f_url+so_url;
		//a=window.open(); a.document.write(ajaxURL);
		$.ajax({
		  url: ajaxURL,
		  success: function(r) {//a=window.open();a.document.write(r); ///*dataType: "json",*/
			showRecords(r);
		  }
		});
	}
	function showRecords(r){
	 //$('#result_set').html(r+'<hr>end of Response');exit;
		r = jQuery.parseJSON(r);
		result = r.records;
		h='';var no_record='yes';
		if(r != null){
			row = '';
			row_class = '';
			for(x in result){no_record='no';
				s = result[x];
				rowData = new Array();
				row += '<tr class="link_cursor'+row_class+'">';
				for(y in s){
					tdVal = s[y];
					if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:8px;" class="text-center"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
								
					rowData[y] = tdVal;
					if(y=='name'){
						row	+= '<td data-label="iPortal Security Questions List" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					}
					if(y=='modified_on'){
						row	+= '<td data-label="Last modified" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					}
					if(y=='del_status'){
						var stat = "";
						var stat_val = "";
						if(tdVal==0) 
						{ stat = '<span style="color:green;">Active</span>'; stat_val=1; }
						else if(tdVal==1)
						{ stat = '<span style="color:red;">Deleted</span>'; stat_val=0; }
						
						row	+= '<td data-label="Status" id="status_'+pkId+'" onclick="setStatus(\''+pkId+'\',\''+stat_val+'\')">'+stat+'</td>';
					}
				}
				if(row_class==''){row_class=' alt';}else{row_class='';}
				totalRecords++;
				row += '</tr>';
				arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
			}
			h = row;
		}
		if(no_record=='yes'){h+="<tr><td colspan='4' style='text-align:center;'>No Record Found</td></tr>";}
		$('#result_set').html(h);		
		top.show_loading_image('hide');
	}
	function addNew(ed,pkId){
		var modal_title = '';
		if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
		else {
			modal_title = 'Add New Record';
			$('#adm_epostId').val('');
			document.add_edit_frm.reset();
		}
		$('#myModal .modal-header .modal-title').text(modal_title);
		$('#myModal').modal('show');
		if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	}
	
	function saveFormData(){
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Saving data...');
		frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
		if($.trim($('#epost_pre_defines').val())==""){
			top.fAlert("Please enter the security question");
			top.show_loading_image('hide');
			return false;
		}
		$.ajax({
			type: "POST",
			url: "ajax_security_questions.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d=='enter_unique'){
					top.fAlert('Record already exist.');		
					return false;
				}
				if(d.toLowerCase().indexOf('success') > 0){
					top.alert_notification_show(d);
				}else{
					top.fAlert(d);
				}
				$('#myModal').modal('hide');
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
			top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.all_data.all_data2.deleteModifiers('"+pos_id+"')");
		}else{
			top.fAlert('No Record Selected.');
		}
	}
	function deleteModifiers(pos_id) {
		pos_id = pos_id.substr(0,pos_id.length-2);
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Deleting Record(s)...');
		frm_data = 'pkId='+pos_id+'&task=delete';
		$.ajax({
			type: "POST",
			url: "ajax_security_questions.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
				else{top.fAlert(d+'Record delete failed. Please try again.');}
			}
		});
	}
	
	function setStatus(rowid,value)
	{
		var dataString = 'task=change_status&rid='+rowid+'&value='+value;
		$.ajax({
			type: "POST",
			url: "ajax_security_questions.php",
			data: dataString,
			cache: false,
			success: function(response)
			{
				if(response=="true")
				{
					if(value==1)
					{
						$("#status_"+rowid).html('<span class="text-danger">Deleted</span>');
						$("#status_"+rowid).attr('onClick','setStatus("'+rowid+'","0")');
					}
					else if(value==0)
					{
						$("#status_"+rowid).html('<span class="text-success">Active</span>');
						$("#status_"+rowid).attr('onClick','setStatus("'+rowid+'","1")');
					}
					top.alert_notification_show("Record Updated Successfully");
				}
			}
		});
	}
	
	function fillEditData(pkId){
		f = document.add_edit_frm;
		e = f.elements;
		add_edit_frm.reset();
		$('#adm_epostId').val(pkId);
		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.phrase,formObjects)){
				on	= o.name;
				//alert(arrAllShownRecords[]);
				v	= arrAllShownRecords[pkId][on];
				if (o.tagName == "INPUT" || o.tagName == "SELECT"){
					if (o.type == "checkbox" || o.type == "radio"){
						oid = on;
						if(v==1)
						{
							$('#'+oid).attr('checked',true);
						}
					} else if(o.type!='submit' && o.type!='button'){
						o.value = v;
					}
				}
			}
		}		
	}
</script>
<body>
    <input type="hidden" name="ord_by_field" id="ord_by_field" value="name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable" style="height:<?php echo ($_SESSION['wn_height']-320);?>px;">
			<table class="table table-bordered adminnw">
				<thead>
					<tr>
						<th style="width:20px; padding-left:10px;">
							<div class="checkbox">
								<input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
								<label for="chk_sel_all"></label>
							</div>
						</th>
						<th onClick="LoadResultSet('','','','name',this);" class="link_cursor">iPortal Security Questions List<span></span></th>
						<th onClick="LoadResultSet('','','','last_modified',this);" class="link_cursor">Last modified<span></span></th>
						<th onClick="LoadResultSet('','','','del_status',this);" class="link_cursor">Status<span></span></th>
				  </tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div class="common_modal_wrapper"> 
	<!-- Modal -->
		<div id="myModal" class="modal fade" role="dialog">
			<div class="modal-dialog"> 
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" id="modal_title">Modal Header</h4>
					</div>
					<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
						<div class="modal-body">
							<div class="form-group">
								<input type="hidden" class="form-control" name="id" id="adm_epostId" >
								<label for="epost_pre_defines">iPortal Security Question</label>
								<input class="form-control" name="name" id="epost_pre_defines" type="text">
							</div>
						</div>
						<div id="module_buttons" class="modal-footer ad_modal_footer">
							<button type="submit" class="btn btn-success">Save</button>
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
		set_header_title('Security Questions');
	});
	show_loading_image('none');
</script>
<?php 
	require_once('../admin_footer.php');
?>