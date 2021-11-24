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
require_once(dirname(__FILE__).'/../../../config/globals.php');
$library_path = $GLOBALS['webroot'].'/library';


/* Get Users */
$users_Arr=array();
$sql = "select id,fname,mname,lname from `users` where `delete_status` = '0' order by `lname`";
$sql_rs = imw_query($sql);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    while ($row = imw_fetch_assoc($sql_rs)) {
        $prov_name = core_name_format($row['lname'], $row['fname'], $row['mname']);
        $users_Arr[$row['id']]=$prov_name;
    }
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>imwemr</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
	<?php if(constant('DEFAULT_PRODUCT') == "imwemr") { ?>
  	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/imw_css.css" rel="stylesheet">
 	<?php } ?>
 	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/buttons.js"></script>
	<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
	<script>top.show_loading_image('show')</script>

	<style type="text/css">
		.span { display: block; font-weight:599; font-size:14px; font-style:italic; font-family:robotobold; }
	</style>
</head>
<body>
<div class="whtbox" style="height:<?php echo ($_SESSION['wn_height']);?>px; overflow-x:hidden; overflow-y:auto;"> 
	<form method="post" name="frmVerify" id="frmVerify">
		<table class="table table-hover table-striped table-bordered adminnw">
			<thead>
				<tr>
					<th width="10%">Pt. Name-ID</th>
					<th width="8%">DOB</th>
					<th width="5%">Pri.&nbsp;Ins.</th>
					<th width="7%">Procedure</th>
					<th width="10%">Appt.&nbsp;Date&nbsp;&&nbsp;Time</th>
<!--					<th width="5%">Appt.&nbsp;Status</th>-->
					<th width="10%">Physician</th>
					<th width="6%">Facility</th>
					<th width="10%">Assigned User</th>
					<th width="6%">Professional Cost</th>
					<th width="3%">Facility Cost</th>
					<th width="6%">Anesthesia Cost</th>
					<th width="5%">Status</th>
					<th width="22%">Comment</th>
					<th width="2%">Hx</th>
				</tr>
			</thead>
			<tbody id="results"></tbody>			
		</table>
	</form>

	<div id="verification_modal" class="modal" >
		<div class="modal-dialog modal_90">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h4 class="modal-title" id="modal_title">Auth/Verify History</h4>
				</div>

				<div class="modal-body" style="min-height:300px;max-height:450px; overflow:hidden; overflow-y:auto;">
                    <table class="table table-hover table-striped table-bordered adminnw">
                        <thead>
                            <tr>
<!--                                <th width="1%">&nbsp;</th>-->
                                <th width="12%">Date</th>
                                <th width="15%">Assigned User</th>
                                <th width="15%">Professional Cost</th>
                                <th width="15%">Facility Cost</th>
                                <th width="15%">Anesthesia Cost</th>
                                <th width="8%">Status</th>
                                <th width="20%">Comment</th>
<!--                                <th></th>-->
                            </tr>
                        </thead>
                        <tbody id="verification_history"></tbody>			
                    </table>

				</div>

<!--				<div class="modal-footer text-center">
					<button type="button" class="btn btn-success" onClick="submit_form();">Save</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
				</div>-->

			</div>
		</div>
	</div>
	
</div>

<script type="text/javascript">
	var result_data = [];
	var raw_data = [];
	var users_Arr = JSON.parse('<?php echo json_encode($users_Arr);?>');

	
	function submit_form(){
		//top.fmain.$("#btnVerify").trigger("click");
		
        var formData = $('#frmVerify').serialize();
        var postData={action:'update',form_data:formData};
        top.show_loading_image('show');
        $.ajax({
            url: top.JS_WEB_ROOT_PATH+'/interface/scheduler/verification/ajax.php',
            type: 'POST',
            data: postData,
            success: function(response) {
                top.show_loading_image('hide');
                response=$.parseJSON(response);
                if(response.success==1) {
                    top.fAlert("Record saved successfully");
                    load_listing();
                } 
            }
        });
	}
	
	
	function verification_hx(vhxid){
        if(!vhxid)return false;
        var postData={action:'get_hx',vhxid:vhxid};
        $.ajax({
            url:top.JS_WEB_ROOT_PATH+'/interface/scheduler/verification/ajax.php',
            data:postData,
            type:'POST',
            success:function(respData){
                show_history(respData);
            }
        });
	}
    
    
    function show_history(result_data) {
        result_data = JSON.parse(result_data);var html = '';
        
        var keys = Object.keys(result_data).reverse();
        
        var e = false;
        for(i=0; i< keys.length; i++)
        {
            e = true;
            
            var x = result_data[keys[i]];
            
            var user_name='';
            if(users_Arr[x.assigned_user])user_name=users_Arr[x.assigned_user];
				//html += '<tr class="row_'+id+'"><input type="hidden" id="vhx_id" name="vhx_id" value="'+id+'"/>';
				html += '<tr class="row_'+i+'">';
				//html += '<td><div class="checkbox"><input type="checkbox" id="chk_vhx_'+id+'" name="chk_vhx_'+id+'" class="chk_record" value="'+id+'" /><label for="chk_vhx_'+id+'">&nbsp;</label></div></td>';
				html += '<td>'+x.created_on+'</td>';
                html += '<td>'+user_name+'</td>';
                html += '<td>'+x.professional_cost+'</td>';
				html += '<td>'+x.facility_cost+'</td>';
				html += '<td>'+x.anesthesia_cost+'</td>';
				html += '<td>'+x.status+'</td>';
				html += '<td>'+x.comment+'</td>';
                //html += '<td><img src="'+top.JS_WEB_ROOT_PATH+'/library/images/search.png" style="min-width:25px!important;" class="pointer" title="Delete" onClick="verification_hx('+x.v_id+');" /></td>';
				html += '</tr>';
            
            
        }
		if(!e) {
			html = '<tr><td colspan="8" class="text-center bg-warning f-bold">No record found</td></tr>'
		}
		
		$("tbody#verification_history").html(html);
        //$('#verification_history').html(respData);
        $("#verification_modal").modal('show');
    }
	

	
	//load listing
	function load_listing(type) {
		if( typeof type === 'undefined') type = '';
		var data = {}; 
		if( type) { data['appt_type'] = type; }
		$.get(top.JS_WEB_ROOT_PATH+'/interface/scheduler/verification/ajax.php', data)
					.done(function(r){ after_load(r) });	
	}
	

	function after_load(r) {
		r = $.parseJSON(r);var html = '';result_data = r;
		var e = false;

		$.each(r,function(i,x){ e = true;
				html += '<tr><input type="hidden" id="track" name="data['+x.v_id+'][track]" value="0"/>';
				html += '<input type="hidden" id="appt_id" name="data['+x.v_id+'][appt_id]" value="'+i+'"/>';
				html += '<input type="hidden" id="v_required" name="data['+x.v_id+'][v_required]" value="'+x.v_required+'"/>';
				html += '<td>'+x.pt_name_id+'</td>';
                html += '<td>'+x.pt_dob+'</td>';
                html += '<td>'+x.pri_ins_prov+'</td>';
				html += '<td>'+x.proc_type+'</td>';
				html += '<td>'+x.appt_date_time+'</td>';
				//html += '<td>'+x.appt_status+'</td>';
				html += '<td>'+x.physician+'</td>';
				html += '<td>'+x.facility_name+'</td>';
				html += '<td><select class="form-control minimal" name="data['+x.v_id+'][assigned_user]" id="userName" onchange="trackChanges(this)">\n\
                            <option value="">Select User</option>';
                $.each(users_Arr, function(id,value) {
                    html += '<option value="'+id+'" '+((x.assigned_user==id)?'selected':'')+' >'+value+'</option>';
                });
				html += '</select></td>';
				html += '<td><input type="text" class="form-control" name="data['+x.v_id+'][professional_cost]" onkeyup="trackChanges(this)" value="'+x.professional_cost+'"></td>';
				html += '<td><input type="text" class="form-control" name="data['+x.v_id+'][facility_cost]" onkeyup="trackChanges(this)" value="'+x.facility_cost+'"></td>';
				html += '<td><input type="text" class="form-control" name="data['+x.v_id+'][anesthesia_cost]" onkeyup="trackChanges(this)" value="'+x.anesthesia_cost+'"></td>';
				html += '<td><select class="form-control minimal" name="data['+x.v_id+'][status]" id="status" onchange="trackChanges(this)">\n\
                    <option value="pending" '+((x.status=='pending')?'selected':'')+'>Pending</option>\n\
                    <option value="followup" '+((x.status=='followup')?'selected':'')+'>Follow Up</option>\n\
                    <option value="completed" '+((x.status=='completed')?'selected':'')+'>Completed</option>\n\
                   </select></td>';
				
				
				html += '<td><textarea class="form-control" name="data['+x.v_id+'][comment]" onkeyup="trackChanges(this)">'+x.comment+'</textarea></td>';

                html += '<td><img src="'+top.JS_WEB_ROOT_PATH+'/library/images/search.png" style="min-width:25px!important;" class="pointer" title="Edit" data-appt-id="'+i+'" onClick="verification_hx('+x.v_id+');" /></td>';
				html += '</tr>';
		});
		
		if(!e) {
			html = '<tr><td colspan="15" class="text-center bg-warning f-bold">No record found</td></tr>'
		}
		
		$("tbody#results").html(html);
	};


	function trackChanges(obj) {
		$(obj).parent('td').parent('tr').find('input#track').val(1);
	}
		    
    
    function delete_vhx_record() {
        var del_id = '';
        $('.chk_record').each(function () {
            if ($(this).attr('checked') == 'checked' || $(this).attr('checked') == true || $(this).prop('checked') == true) {
                if (del_id == '') {
                    del_id += $(this).val();
                } else {
                    del_id += ',' + $(this).val();
                }
            }
        });
        var postData={action:'delete_vhx',del_id:del_id};
        
        $.ajax({
            url:top.JS_WEB_ROOT_PATH+'/interface/scheduler/verification/ajax.php',
            data:postData,
            type:'POST',
            success: function (r) {
                if (r == 'taskdeleted') {
                    verification_hx(vhxid);
                }
            }
        });
    }
	
	//Setting table height
	function set_container_height(){
		var header_position = $('#first_toolbar',top.document).position();
		var window_height = parseInt(window.innerHeight - ($('footer',top.document).outerHeight() + header_position.top));

		//If pagination is there on the page i.e --> .pgn_prnt class
		if($('.pgn_prnt:first').length){
			window_height = window_height - $('.pgn_prnt').outerHeight();
			$('.pgn_prnt').css('overflow-x','hidden');
		}else{
			window_height = parseInt(window_height + 30);
		}
        
		$('#fmain', top.document).height(window_height);
        $('.whtbox').height(window_height - 30);
	} 
	
    
	$(document).ready(function(){
		top.set_header_title('Auth/Verify Sheet');

		load_listing();
		top.show_loading_image('hide');
        
	});
    
    $(top.document).on('toolBarAdded', function(){
        setTimeout(function(){
            set_container_height();
        }, 200);
    });
    
	
	$(window).load(function(){
		setTimeout(function(){
			$(top.document).contents().find('#first_toolbar').addClass('Admin').show().trigger('toolBarAdded');
		}, 1000);
        
	});
//		
	
	
    var ar = [["btn_submit","Save","top.fmain.submit_form();"]];
	top.btn_show("VERIFICATION_SHEET",ar);		  
</script>
</body>
</html>