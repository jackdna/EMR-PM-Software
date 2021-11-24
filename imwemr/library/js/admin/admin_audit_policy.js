	function getAuditPolicies(){  
		var url_dt = top.JS_WEB_ROOT_PATH+"/interface/admin/audit/ajax.php";
		var frm_data = 'ajax_request=yes&task=get_listing';
		$.ajax({
			url:url_dt,
			type:'POST',
			data:frm_data,
			beforeSend:function(){
				top.show_loading_image('show');
			},
			success:function(response){
				$("#divLoadSchRsn").html(response);
				top.show_loading_image('hide');
			},
		});
   }

   function set_this_policy(hid_id, hid_value){
		if(hid_value == "1"){
			var hid_suffix1 = "_on";
			var hid_suffix2 = "_off";
		}else{
			var hid_suffix1 = "_off";
			var hid_suffix2 = "_on";
		}
	   
		document.getElementById(hid_id).value = hid_value;		
		var hid_policy1 = hid_id.substring(5)+hid_suffix1;
		var hid_policy2 = hid_id.substring(5)+hid_suffix2;
		document.getElementById(hid_policy2).checked = false;
		document.getElementById(hid_policy1).checked = true;
   }

   function save_changes(msg){
		if(typeof(msg)!='boolean'){msg = true;}
		if(msg){
			top.fancyConfirm("Are you sure you want to change Audit Policies?","", "window.top.fmain.save_changes(false)");
		}else{
			parent.parent.show_loading_image('none');
			var hid_policies = document.getElementById("hid_policies").value;
			var arr_hid_policies = hid_policies.split(",");
			var hid_policies_length = arr_hid_policies.length;

			var str_query_string = "";
			for(i = 1; i < hid_policies_length; i++){
				var this_hid_policy = arr_hid_policies[i];
				str_query_string += this_hid_policy+"="+document.getElementById(this_hid_policy).value+"&";
			}
			str_query_string += "save=1";
			top.show_loading_image('show');
			
			var url_dt = top.JS_WEB_ROOT_PATH+"/interface/admin/audit/ajax.php";
			var frm_data = 'ajax_request=yes&task=save_policy&'+str_query_string;
			$.ajax({
				url:url_dt,
				type:'POST',
				data:frm_data,
				success:function(response){
                    top.show_loading_image('hide');
					if($.trim(response) != '' && response > 0){
						top.fAlert('Record updated successfully');
						getAuditPolicies();
					}
					
				}
			});	
		}
	}
	
	function set_all_policies(intMode){
		var hid_policies = document.getElementById("hid_policies").value;
		var arr_hid_policies = hid_policies.split(",");
		var hid_policies_length = arr_hid_policies.length;
		
		if(intMode == "1"){
			var hid_suffix1 = "_on";
			var hid_suffix2 = "_off";
		}else{
			var hid_suffix1 = "_off";	
			var hid_suffix2 = "_on";
		}
		
		for(i = 1; i < hid_policies_length; i++){
			var this_hid_policy = arr_hid_policies[i];				
			document.getElementById(this_hid_policy).value = intMode;
			var hid_policy1 = this_hid_policy.substring(5)+hid_suffix1;
			var hid_policy2 = this_hid_policy.substring(5)+hid_suffix2;
			document.getElementById(hid_policy2).checked = false;
			document.getElementById(hid_policy1).checked = true;
		}
	}
	function show2(){
		if (!document.all&&!document.getElementById)
		return
	}
	
	$(document).ready(function(){
		var ar = [["audit_update","Update","top.fmain.save_changes();"],["audit_cancel","Cancel","top.fmain.getAuditPolicies();"]];
		top.btn_show("ADMN",ar);
		show2();
		getAuditPolicies();
		set_header_title('Audit Policies');	
		top.show_loading_image('hide');
	});