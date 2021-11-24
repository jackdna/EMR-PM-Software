	var fee_column_arr_js = json_arr.json_column_name;
	$(document).ready( function() {
		$('textarea').keypress(function(event) {
			if (event.keyCode == 13) {
				event.preventDefault();
			}
		});
		$("#fee_tbl_cols").on('rendered.bs.select',function(){
			if( $("#updateColBtn").hasClass('hide') ) {
				$("#updateColBtn").removeClass('hide');
			}
		});
	});
	function save_new_field(){
		var msg = '';
		document = top.fmain.document;	
		if(document.cptFrm.table_column.value == ''){
			msg = '&bull; Please Enter Column Name.<br>';
			document.cptFrm.table_column.className = 'mandatory form-control';
			document.cptFrm.table_column.focus();
		}else if($.inArray($.trim(document.cptFrm.table_column.value.toLowerCase()),fee_column_arr_js) != -1){
			msg = '&bull; Fee Table Column Already Exist.<br>';
			document.cptFrm.table_column.className = 'mandatory form-control';
			document.cptFrm.table_column.focus();
		}
		else{
			document.cptFrm.saveDataFld.value = 'save';
		}
		if(msg){
			fAlert(msg)
		}
		else{
			parent.parent.show_loading_image('block');
			var curobj = top.fmain.cptFrm;
			$("#cat_fee_tbl").val($("#fee_tbl_cat",curobj).val());
			var form_data = $('#cptFrm').serialize();
			$.ajax({
				url:'ajax_handler.php?ajax_request=yes',
				data:form_data,
				type:'POST',
				success:function(data){
					if($.trim(data) != ''){
						top.alert_notification_show(data);
						window.location.reload();
					}
				}
			});
		}		
			
	}
	
	function saveFeeData(){
		parent.parent.show_loading_image('block');	
		$("#saveData").val('Save');
		var cat_array = new Array();
		$('#fee_tbl_cat option:selected').each(function() {
			cat_array.push($(this).val());
		});
		$("#feeTableFrm #cat_fee_tbl_u").val(cat_array);
		var form_data = $("#feeTableFrm").serialize();
		if(chkFullLoaded()){
			$.ajax({
				url:'ajax_handler.php?ajax_request=yes',
				data:form_data,
				type:'POST',
				success:function(data){
					make_cat_search();
					top.alert_notification_show(data);
				}
			})
		} else {
			return false;
		}
	}
	
	function updatefee(msg){
		var cpt_array = new Array();
		$('#fee_tbl_col_opt option:selected').each(function() {
			cpt_array.push($(this).val());
		});
		if(cpt_array.length==0){
			fAlert("Please Select Fee Column Name.");
		}else if(document.getElementById('inc_dec_per').value==""){
			fAlert("Please Enter Percentage Value.");
			document.update_fee_frm.inc_dec_per.className = 'mandatory form-control';
		}else{
			if(msg=="yes"){
				document.update_fee_frm.inc_dec_save.value = 'update';
				parent.parent.show_loading_image('block');
				var cat_array = new Array();
				$('#fee_tbl_cat option:selected').each(function() {
					cat_array.push($(this).val());
				});
				$("#cat_fee_tbl_u").val(cat_array);
				var form_data = $("#update_fee_frm").serialize();
				$.ajax({
					url:'ajax_handler.php?ajax_request=yes',
					data:form_data,
					type:'POST',
					success:function(response){
						if($.trim(response) != ''){
							top.alert_notification_show(response);
							window.location.reload();
						}
					}
				})
			}else{
				top.fancyConfirm("Are you sure to "+document.getElementById('inc_dec_opt').value+" the CPT fee?","", "window.top.fmain.updatefee('yes')");
			}	
		}
	}
	
	function make_csv(){
		var cat_array = new Array();
			$('#fee_tbl_cat option:selected').each(function() {
				cat_array.push($(this).val());
			});
		var data = top.JS_WEB_ROOT_PATH+"/interface/admin/billing/cpt_fee/cpt_fee_csv.php?cat_fee_tbl="+cat_array;
		window.location=data;
	}
	
	function make_cat_search(){
		var cat_array = new Array();
			$('#fee_tbl_cat option:selected').each(function() {
				cat_array.push($(this).val());
			});
		var data = top.JS_WEB_ROOT_PATH+"/interface/admin/billing/cpt_fee/index.php?fee_tbl_cat="+cat_array;
		window.location=data;
	}
	
	function delColumn(obj,msg,colmnName){ 		
		if(typeof(msg)!='boolean'){msg = true;}
		if(msg){
			top.fancyConfirm("Are you sure to delete this record?","", "window.top.fmain.delColumn('"+obj+"',false,'"+colmnName+"')");
		}
		else{
			parent.parent.parent.show_loading_image('block');
			var objt = document.feeTableFrm.DelColumn.value = obj;
			var col_name = document.feeTableFrm.DelColumnName.value = colmnName;
			var form_data = 'DelColumn='+objt+'&col_name='+col_name;
			$.ajax({
					url:'ajax_handler.php?ajax_request=yes',
					data:form_data,
					type:'POST',
					success:function(response){
						if($.trim(response) != ''){
							top.alert_notification_show(response);
							window.location.reload();
						}
					}
				})
			
		}
	}
	
	var rowsToPrint = json_arr.rowsToPrint;
	var rowsPrinted = $('#cpt_fee_rows tr').length;
	function chkFullLoaded(){
		if(rowsToPrint != rowsPrinted){
			fAlert('Page not loaded properly, unable to update records.')
			return false;
		}else{
			return true;
		}
	}
	top.show_loading_image("hide");

	function set_order(filed){
		var sort_order = $('#sort_order').val();
		var sort_time = $('#sort_order').data('first-time');

		var sort_first_time = '';
		if(sort_time == 'yes'){
			sort_first_time = 'no';
		}

		var sort_value = 'ASC';
		if(sort_order == 'ASC'){
			sort_value = 'DESC';
		}
		
		var prev_sort_field = $('#order_field').val();
		if(filed != prev_sort_field){
			sort_value = 'ASC';
		}
		
		window.location.href = top.JS_WEB_ROOT_PATH+'/interface/admin/billing/cpt_fee/index.php?sort_field='+filed+'&sort_by='+sort_value+'&sort_first_time='+sort_first_time;
}