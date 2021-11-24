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
$createdBy = $_SESSION['authId'];		
?>
<script type="text/javascript">	
	function checkdata(){	 
		var msg = '<b>Please enter the following</b><br>';
		var focus_val = false;
		if(document.iOLinkSettings.iolinkUrl.value==""){
			document.iOLinkSettings.iolinkUrl.className = 'mandatory';
			if(focus_val == false){
				document.iOLinkSettings.iolinkUrl.focus();
				focus_val = true;
			}
			msg = msg + '&bull; URL of iOLink communication page.<br>'; 
		}		
		if(document.iOLinkSettings.iolinkUrlUsername.value==""){
			msg = msg + '&bull; Username of iOLink communication page.<br>'; 
			document.iOLinkSettings.iolinkUrlUsername.className = 'mandatory';
			if(focus_val == false){
				document.iOLinkSettings.iolinkUrlUsername.focus();
				focus_val = true;
			}
		}
		if(document.iOLinkSettings.iolinkUrlPassword.value==""){
			document.iOLinkSettings.iolinkUrlPassword.className = 'mandatory';
			if(focus_val == false){
				document.iOLinkSettings.iolinkUrlPassword.focus();
				focus_val = true;
			}
			msg = msg + '&bull; Password of iOLink communication page.<br>'; 
		}
		
		if(document.iOLinkSettings.iolinkUrlPracName.value==""){
			document.iOLinkSettings.iolinkUrlPracName.className = 'mandatory';
			if(focus_val == false){
				document.iOLinkSettings.iolinkUrlPracName.focus();
				focus_val = true;
			}
			msg = msg + '&bull; Practice Name.<br>'; 
		}
		if(msg == '<b>Please enter the following</b><br>'){					
			document.iOLinkSettings.submit();
		}
		else{
			parent.parent.show_loading_image('none');
			fAlert(msg);
			msg = '';
			return false;
		}			  
	}
	
	function downLoadCosentForm(iolinkUrl,iolinkUrlUsername,iolinkUrlPassword,wishToDownLoad,newOrAll){
		top.show_loading_image('show','70', 'Downloading Forms...');
		var url = 'downloadConsentFormSurgeryCenter.php?iolinkUrl='+iolinkUrl+'&iolinkUrlUsername='+iolinkUrlUsername+'&iolinkUrlPassword='+iolinkUrlPassword+'&wishToDownLoad='+wishToDownLoad+'&newOrAll='+newOrAll;
		$.ajax({
			type: "POST",
			url: url,
			success: function(d) {
				top.show_loading_image('hide');
				fAlert(d);
			}
		});
	}
	
	function new_entry(reftype){		
		document.location.href='add_insurance_case.php';
	}
	
	function restForm(){	 
		if(document.getElementById('downLoadTextTr')){
			document.getElementById('downLoadTextTr').style.display='none';
		}
		if(document.getElementById('downLoadTr')){
			document.getElementById('downLoadTr').style.display='none';
		}
		document.getElementById('iolinkUrl').value="";
		document.getElementById('iolinkUrlUsername').value="";
		document.getElementById('iolinkUrlPassword').value="";
		document.getElementById('iolinkAdminId').value="";
		document.getElementById('iolinkAdminIdSaved').value="";
		parent.parent.show_loading_image('none');
	}	
		
	function testPopUp(){
		var iolinkUrl = document.getElementById("iolink_url").value;
		var iolinkUrlUsername = document.getElementById("iolink_url_username").value;
		var iolinkUrlPassword = document.getElementById("iolink_url_password").value;
		parent.parent.show_loading_image('none');
		var focus_val = false;
		msg = '<b>Please enter the following</b><br>';
		if(document.add_edit_frm.iolink_url.value==""){
			if(focus_val == false){
				document.add_edit_frm.iolink_url.focus();
				focus_val = true;
			}
			msg = msg + '&bull;URL of iOLink communication page.<br>'; 
		}		
		if(document.add_edit_frm.iolink_url_username.value==""){
			if(focus_val == false){
				document.add_edit_frm.iolink_url_username.focus();
				focus_val = true;
			}
			msg = msg + '&bull; Username of iOLink communication page.<br>'; 
		}
		if(document.add_edit_frm.iolink_url_password.value==""){
			if(focus_val == false){
				document.add_edit_frm.iolink_url_password.focus();
				focus_val = true;
			}
			msg = msg + '&bull; Password of iOLink communication page.<br>'; 
		}
		if(msg != '<b>Please enter the following</b><br>'){			
			top.fAlert(msg);
			msg='';
		}else{
			var strTestUrl = 'iolinkUrl='+iolinkUrl+'&iolinkUrlUsername='+iolinkUrlUsername+'&iolinkUrlPassword='+iolinkUrlPassword+'&downloadForm=';
			top.show_loading_image('show','15', 'Testing Connection...');
			$.ajax({
				url: "../iolink_tabs/testConnection.php?"+strTestUrl,
				success: function(resp){
						if(resp) {
							top.show_loading_image('hide');
							top.fAlert(resp);
						}
					}
				});			
			}
			
		}
	function iolinkModifyFun(id) {
		if(id) {
			location.href = 'iOLinkSettings.php?edit_id='+id;
		}
	}
	function del_record(id,msg){
		if(typeof(msg)!='boolean'){msg = true;}
		if(msg){
			top.fancyConfirm("Are you sure you want to delete this record?","", "window.top.fmain.del_record('"+id+"',false)");
		}else{
			show_loading_image('block');
		}
	}
	
	var arrAllShownRecords = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('phrase_id','phrase');
	function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading iOLink Settings...');
		
		if(typeof(s)!='string' || s==''){s = 'Active';}
		s_url = "&s="+s;
		//if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
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
		$('.link_cursor span').html('');
		if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
		else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
		so_url='&so='+so+'&soAD='+soAD;	
		ajaxURL = "ajax.php?task=show_list"+s_url+f_url+so_url;
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
		result = r.records;//a=window.open();a.document.write(r.records);
		h='';var no_record='yes';
		var pass=link_url=link_user=link_pass="";
		if(r != null){
			row = '';
			for(x in result){
				no_record='no';
				s = result[x];
				rowData = new Array();
				row += '<tr>';
				for(y in s){
					tdVal = s[y];
					var prac_nm="";
					//alert(y+' => '+tdVal);
					if(y=='iolink_id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
					rowData[y] = tdVal;
					if(y=='iolink_url'){
						link_url = tdVal;
						row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
					}
					if(y=='iolink_url_username'){
						link_user = tdVal;
						row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+tdVal+'</td>';
					}
					if(y=='iolink_url_password'){
						link_pass = tdVal;
						if(tdVal!='')
						{
							pass = "&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;";
						}
						row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');" >&nbsp;'+pass+'</td>';
					}
					if(y=='iolink_practice_name'){
						prac_nm =1;
						row	+= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');" style="text-transform:capitalize;">&nbsp;'+tdVal+'</td>';
					}
					if(link_url && link_user && link_pass && prac_nm==1){
						row	+= '<td class="leftborder text_purple" onclick="downLoadCosentForm(\''+link_url+'\',\''+link_user+'\',\''+link_pass+'\',1,\'all\');" >&nbsp;All Consents</td>';
					}
					if(link_url && link_user && link_pass  && prac_nm==1){
						row	+= '<td class="leftborder text_purple" onclick="downLoadCosentForm(\''+link_url+'\',\''+link_user+'\',\''+link_pass+'\',1,\'\');" >&nbsp;New Consents</td>';
					}
					if(link_url && link_user && link_pass  && prac_nm==1){
						row	+= '<td class="leftborder text_purple" onclick="downLoadCosentForm(\''+link_url+'\',\''+link_user+'\',\''+link_pass+'\',2,\'\');" >&nbsp;Health Questionnaire</td>';
					}
					if(link_url && link_user && link_pass  && prac_nm==1){
						row	+= '<td class="leftborder text_purple" onclick="downLoadCosentForm(\''+link_url+'\',\''+link_user+'\',\''+link_pass+'\',3,\'\');" >&nbsp;H & P</td>';
					}
				}
				totalRecords++;
				row += '</tr>';
				arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
			}
			h = row;
		}
		if(no_record=='yes'){h+="<tr><td colspan='8' style='text-align:center;'>No Record Found</td></tr>";}
		$('#result_set').html(h);		
		top.show_loading_image('hide');
	}
	
	function addNew(ed,pkId){
		var modal_title = '';
		if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
		else {
			modal_title = 'Add New Record';
			$('#iolink_id').val('');
			document.add_edit_frm.reset();
		}
		$('#myModal .modal-header .modal-title').text(modal_title);
		$('#myModal').modal('show');
		if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	}
	
	function fillEditData(pkId){
		f = document.add_edit_frm;
		e = f.elements;
		add_edit_frm.reset();
		$('#iolink_id').val(pkId);
		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.phrase,formObjects)){
				on	= o.name;
				//alert(arrAllShownRecords[]);
				v	= arrAllShownRecords[pkId][on];
				//alert(on+ ' '+v);
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
	
	function saveFormData(){
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Saving data...');
		frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
		var msg="";
		
		if($.trim($('#iolink_url').val())==""){
			msg+="&nbsp;&bull;&nbsp;URL of iOLink communication page.<br>";
		}
		if($.trim($('#iolink_url_username').val())==""){
			msg+="&nbsp;&bull;&nbsp;Username of iOLink communication page.<br>";
		}
		if($.trim($('#iolink_url_password').val())==""){
			msg+="&nbsp;&bull;&nbsp;Password of iOLink communication page.<br>";
		}
		if($.trim($('#iolink_practice_name').val())==""){
			msg+="&nbsp;&bull;&nbsp;Practice Name.<br>";
		}
		if(msg){
			msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
			top.fAlert(msg_val);
			top.show_loading_image('hide');
			return false;
		}
		
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d=='enter_unique'){
					top.fAlert('Record already exist.');		
					return false;
				}
				if(d.toLowerCase().indexOf('success') > 0){
					top.alert_notification_show(d);
					//top.fAlert(d);
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
		})
		if(pos_id!=''){
			top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.deleteModifiers('"+pos_id+"')");
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
			url: "ajax.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
				else{top.fAlert(d+'Record delete failed. Please try again.');}
			}
		});
	}
	show_loading_image('none');
	var ar = [["test_connection","Test Connection","top.fmain.testPopUp();"],
			  ["save_iolink_setting","Save Settings","top.fmain.checkdata();"],
			  ["reset_iolink_setting","Reset Settings","top.fmain.restForm();"],
			  ["new_iolink_setting","New Settings","top.fmain.iolinkModifyFun();"]
			  ];
	top.btn_show("ADMN",ar);
	//Btn --
</script>
<?php
$edit_id = $_REQUEST["edit_id"];
$delete_id = $_REQUEST["delete_id"];
if($delete_id) {
	$delQry = "UPDATE iolink_connection_settings SET del_status = 'yes' WHERE iolink_id='".$delete_id."' ";
	$delRes = imw_query($delQry) or die(imw_error());
}
if($save<>""){		
	
	if($edit_id){
		$qrySaveIOlinkSettings = " update iolink_connection_settings set modified_by = $createdBy,modified_date_time = NOW() ";
		$qryWhrIOlink = " WHERE iolink_id='".$edit_id."' ";
	}
	else{
		$qrySaveIOlinkSettings = " insert into iolink_connection_settings set created_by = $createdBy,created_date_time = NOW() ";	
		$qryWhrIOlink = "";
	}
	$qrySaveIOlinkSettings .= ",iolink_url 			= '".addslashes($iolinkUrl)."',
								iolink_url_username = '".addslashes($iolinkUrlUsername)."',
								iolink_url_password = '".addslashes($iolinkUrlPassword)."',
								iolink_practice_name= '".addslashes($iolinkUrlPracName)."',
								iolink_admin_id 	= '$iolinkAdminIdSaved' ".$qryWhrIOlink;
	
	$qrySaveIOlinkSettingsRsId = imw_query($qrySaveIOlinkSettings);
	if(!$qrySaveIOlinkSettingsRsId){
		echo ("Error : ". imw_error()."<br>".$qrySaveIOlinkSettings);
	}
	else{
		if($iolinkAdminId){
			echo "<script>fAlert('IOLink Connection Settings Successfully Updated');</script>";
		}
		else{
			echo "<script>fAlert('IOLink Connection Settings Successfully Saved');</script>";
		}
	
	}
	$iolinkAdminId = $iolinkAdminIdSaved;	
}

	$iolink_admin_id = $_SESSION['authId'];
	$qryGetIOlinkSettings = "select iolink_admin_id as iolinkAdminId, 
							iolink_id as iolinkId,
							iolink_url as iolinkUrl,
							iolink_url_username as iolinkUrlUsername,
							iolink_url_password as iolinkUrlPassword,
							iolink_practice_name as iolinkUrlPracName
							FROM iolink_connection_settings
							WHERE del_status != 'yes' ORDER BY iolink_id ";
	$rsGetIOlinkSettings = imw_query($qryGetIOlinkSettings);
	if(!$rsGetIOlinkSettings){
		echo ("Error : ". imw_error()."<br>".$qryGetIOlinkSettings);
	}
?>

<input type="hidden" name="preObjBack" value=""/>
<input type="hidden" id="iolinkAdminId" name="iolinkAdminId" value=""/>
<input type="hidden" id="iolinkAdminIdSaved" name="iolinkAdminIdSaved" value=""/>
<input type="hidden" id="downloadForm" name="downloadForm" value=""/>
<input type="hidden" name="save" value="save">
<input type="hidden" name="ord_by_field" id="ord_by_field" value="iolink_url_username">
<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
<div class="whtbox">
	<div class="table-responsive respotable adminnw">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
					<th onClick="LoadResultSet('','','','iolink_url',this);">URL of iASC Link communication page<span></span></th>
					<th onClick="LoadResultSet('','','','iolink_url_username',this);">Username<span></span></th>
					<th>Password<span></span></th>
					<th onClick="LoadResultSet('','','','iolink_practice_name',this);">Practice Name<span></span></th>
					<th colspan="4">Downloads Surgerycenter Forms<span></span></th>
				</tr>
			</thead>
			<tbody id="result_set"></tbody>
		</table>
	</div>
</div>
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog"> 
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" id="modal_title">Modal Header</h4>
			</div>
			<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
				<input type="hidden" name="iolink_id" id="iolink_id" value="">	
				<div class="modal-body">
					<div class="row">
						<div class="col-sm-12">
							<label for="iolink_url">URL of iOLink communication page</label>
							<input type="text" name="iolink_url" id="iolink_url" class="form-control">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<label for="iolink_url_username">Username</label>
							<input type="text" name="iolink_url_username" id="iolink_url_username" class="form-control">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<label for="iolink_url_password">Password</label>
							<input type="password" name="iolink_url_password" id="iolink_url_password" class="form-control">
						</div>
					</div>
					<div class="row">
						<div class="col-sm-12">
							<label for="iolink_practice_name">Practice Name</label>
							<input type="text" name="iolink_practice_name" id="iolink_practice_name" class="form-control">
						</div>
					</div>
				</div>	
				<div id="module_buttons" class="ad_modal_footer modal-footer">
					<button type="submit" class="btn btn-success">Save</button>
					<button type="button" onClick="testPopUp();" class="btn btn-success">Test Connection</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
<script type="text/javascript">
	LoadResultSet();
	
	var ar = [["add_new","Add New","top.fmain.addNew();"],
		  ["dx_cat_del","Delete","top.fmain.deleteSelectet();"]
		 ];
	
	top.btn_show("ADMN",ar);
	$(document).ready(function(){
		check_checkboxes();
		set_header_title('iASC Link Settings');	
	});
	parent.parent.show_loading_image('none');
</script>
<?php 
	require_once('../admin_footer.php');
?>