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
require_once("../../admin_header.php");
?>
	<script type="text/javascript">
	var arrAllShownRecords = facilities = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('id','orderset_name','order_id','consult_letter_id','order_set_option','orders_dx_code','recall_code');
	function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading Order Sets...');
		
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
		if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
		else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
		so_url='&so='+so+'&soAD='+soAD;
		ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
		$.ajax({
		  url: ajaxURL,
			success: function(r){
				showRecords(r);
			}
		});
	}
	
	var arrAllDxCodes= new Array();
	var arrAllOrderIds;
	var arrAllConsultLetters;
	var arrOrderedDxCodes;
		
	function showRecords(r){
		r = jQuery.parseJSON(r);
		result 		= r.records;
		arrAllDxCodes 				= r.arrAllDxCodes;
		arrAllDxIds 				= r.arrAllDxIds;
		arrAllDxCds 				= r.arrAllDxCds;
		arrAllOrderIds 				= r.arrAllOrderIds;
		arrAllOrderIdsNew			= r.arrAllOrderIdsNew;
		arrAllNameNew				= r.arrAllNameNew;
		arrAllConsultLetters		= r.arrAllConsultLetters;
		arrAllConsultLettersId		= r.arrAllConsultLettersId;
		arrAllConsultLettersName	= r.arrAllConsultLettersName;
		arrOrderedDxCodes	= r.strOrderedDxCodes.split(',');
		h='';
		if(r != null){
			row = '';
			row_class = '';
			for(x in result){
				s = result[x];
				rowData = new Array();
				row += '<tr class="link_cursor'+row_class+'">';
				for(y in s){
					tdVal = s[y];
					if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
					rowData[y] = tdVal;
					if(y!='id'){
						
						if(y=='orders_dx_code' && tdVal!=''){
							tempArr = tdVal.split(',');
							var tempArr1= new Array();
							for(z in tempArr){
								tempArr1[z] = arrAllDxCodes[tempArr[z]];
							} tdVal = tempArr1.join(', ');
						}
						if(y=='order_id' && tdVal!=''){
							tempArr = tdVal.split(',');
							var tempArr1= new Array();
							for(z in tempArr){
								if(typeof(arrAllOrderIds[tempArr[z]]) !="undefined"){
									if(arrAllOrderIds[tempArr[z]].replace(/\s/,'')!="")
									tempArr1[z] = arrAllOrderIds[tempArr[z]];
								}
							} tdVal = tempArr1.join(', ');
						}
						if(y=='consult_letter_id' && tdVal!=''){
							tempArr = tdVal.split(',');
							var tempArr1= new Array();
							for(z in tempArr){
								tempArr1[z] = arrAllConsultLetters[tempArr[z]];
							} tdVal = tempArr1.join(', ');
						}
						
						align='alignLeft';
						if(y=='recall_code'){ align='alignCenter'; }

						row	+= '<td class="leftborder '+align+'" style="padding-left:5px" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
					}
				}
				if(row_class==''){row_class=' alt';}else{row_class='';}
				totalRecords++;
				row += '</tr>';
				arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
			}
			h = row;
		}
		$('#result_set').html(h);		
		top.show_loading_image('hide');
	}
	
	function addNew(ed,pkId){
				var modal_title = '';
				if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
				else {
					modal_title = 'Add New Record';
					$('#id').val('');
					$('#orders_dx_code').html('');
					$('#order_id').html('');
					$('#consult_letter_id').html('');
					$('#selected_dx_code').html('');
					$('#selected_orders').html('');
					$('#selected_consult_letters').html('');
			
			//FILL OPTION VALUES
			
			fillSelectValues(arrAllDxCodes, '', '', 'all_dx_code', 'orders_dx_code');
			fillSelectValues(arrAllOrderIds, '', '', 'all_orders', 'order_id');
			fillSelectValues(arrAllConsultLetters, '', '', 'all_consult_letters', 'consult_letter_id');
					document.add_edit_frm.reset();
				}
				$('#myModal .modal-header .modal-title').text(modal_title);
				$('#myModal').modal('show');
				if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
			}	

	function fillEditData(pkId){
		f = document.add_edit_frm;
		e = f.elements;
		$('#id').val(pkId);
		currentDxCodes = currentOrderIds = currentConsultLett ='';
		
		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.name,formObjects)){
				on	= o.name;
				v	= arrAllShownRecords[pkId][on];
				if (o.tagName == "INPUT" || o.tagName == "TEXTAREA"){
					if (o.type == "checkbox" || o.type == "radio") {
						oid = on+'_'+v;
						$('#'+oid).attr('checked',true);
					} else if(o.type!='submit' && o.type!='button' && o.type!='select') {
						o.value = v;
					}
				}else if(o.tagName == "SELECT"){

					if(o.id=='orders_dx_code'){
						currentDxCodes = v;
					}
					if(o.id=='order_id'){
						currentOrderIds = v;
					}
					if(o.id=='consult_letter_id'){
						currentConsultLett = v;
					}
				}
			}
		}
		
		//FILL OPTION VALUES
		fillSelectValues(arrAllDxCodes, arrOrderedDxCodes, currentDxCodes, 'all_dx_code', 'orders_dx_code');
		fillSelectValues(arrAllOrderIds, '', currentOrderIds, 'all_orders', 'order_id');
		fillSelectValues(arrAllConsultLetters, '', currentConsultLett, 'all_consult_letters', 'consult_letter_id');
	}

	function fillSelectValues(arrAllData, arrAllOrderedData, currentData, idAllOptions, idSelectedOptions){
		var tempArr = arrAllOrderedData;
		var arrSelDx = currentData.split(',');
		var tempArr1=new Array();

		//REMOVE OTHER ORDERED
		for(x in tempArr){
			tempArr1[tempArr[x]] =tempArr[x];
		}
		for(x in arrSelDx){
			tempArr1[arrSelDx[x]]='';
		}
		var tempArr=new Array();
		for(x in tempArr1 ){
			if(tempArr1[x]>0){
				tempArr[tempArr1[x]] = tempArr1[x];
			}
		}

		var orders_dx_options='';
		var all_dx_options='';
		
		//arrAllConsultLettersId
		//arrAllConsultLettersName
		
		if(idAllOptions=='all_dx_code') {
			for(x in arrAllDxCds){
				if(jQuery.inArray(arrAllDxIds[x], tempArr)=='-1'){
					sel='';
					if(jQuery.inArray(arrAllDxIds[x], arrSelDx)!='-1'){
						sel='selected';
						orders_dx_options+='<option value="'+arrAllDxIds[x]+'" '+sel+'>'+arrAllDxCds[x]+'</option>';
					}
					all_dx_options+='<option value="'+arrAllDxIds[x]+'" '+sel+'>'+arrAllDxCds[x]+'</option>';
				}
				
			}
		}else if(idAllOptions=='all_orders') {
			for(x in arrAllNameNew){
				if(jQuery.inArray(arrAllOrderIdsNew[x], tempArr)=='-1'){
					sel='';
					if(jQuery.inArray(arrAllOrderIdsNew[x], arrSelDx)!='-1'){
						sel='selected';
						orders_dx_options+='<option value="'+arrAllOrderIdsNew[x]+'" '+sel+'>'+arrAllNameNew[x]+'</option>';
					}
					all_dx_options+='<option value="'+arrAllOrderIdsNew[x]+'" '+sel+'>'+arrAllNameNew[x]+'</option>';
				}
				
			}
		}else if(idAllOptions=='all_consult_letters') {
			for(x in arrAllConsultLettersName){
				if(jQuery.inArray(arrAllConsultLettersId[x], tempArr)=='-1'){
					sel='';
					if(jQuery.inArray(arrAllConsultLettersId[x], arrSelDx)!='-1'){
						sel='selected';
						orders_dx_options+='<option value="'+arrAllConsultLettersId[x]+'" '+sel+'>'+arrAllConsultLettersName[x]+'</option>';
					}
					all_dx_options+='<option value="'+arrAllConsultLettersId[x]+'" '+sel+'>'+arrAllConsultLettersName[x]+'</option>';
				}
				
			}
		}else {
			for(x in arrAllData){
				if(jQuery.inArray(x, tempArr)=='-1'){
					sel='';
					if(jQuery.inArray(x, arrSelDx)!='-1'){
						sel='selected';
						orders_dx_options+='<option value="'+x+'" '+sel+'>'+arrAllData[x]+'</option>';
					}
					all_dx_options+='<option value="'+x+'" '+sel+'>'+arrAllData[x]+'</option>';
				}
				
			}	
		}
		//arrAllDxCds
		$('#'+idSelectedOptions).html(orders_dx_options);
		$('#'+idAllOptions).html(all_dx_options);		
	}
	
	function saveFormData(){
		
		var msg="";
		if($.trim($('#orderset_name').val())==""){
			msg+="&nbsp;&bull;&nbsp;Please Enter Ordre Set Name<br>";
		}
		
		if(msg){
			msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
			top.fAlert(msg_val);
			top.show_loading_image('hide');
			return false;
		}
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Saving data...');
		
		//GET SELECTED DX CODES
		var x=document.getElementById("orders_dx_code");
		var tempArr=new Array();
		for (var i = 0; i < x.options.length; i++) {
		 if(x.options[i].selected ==true){
			  tempArr[i] = x.options[i].value;
		  }
		} 
		$('#sel_orders_dx_code').val(tempArr.join(','));
		// GET SELECTED ORDER IDS
		var x=document.getElementById("order_id");
		var tempArr=new Array();
		for (var i = 0; i < x.options.length; i++) {
		 if(x.options[i].selected ==true){
			  tempArr[i] = x.options[i].value;
		  }
		} 
		$('#sel_order_id').val(tempArr.join(','));
		// GET SELECTED CONSULT LETTERS
		var x=document.getElementById("consult_letter_id");
		var tempArr=new Array();
		for (var i = 0; i < x.options.length; i++) {
		 if(x.options[i].selected ==true){
			  tempArr[i] = x.options[i].value;
		  }
		} 
		$('#sel_consult_letter_id').val(tempArr.join(','));

		frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
		$.ajax({
            type: "POST",
            url: "ajax.php",
            data: frm_data,
            success: function(d) {
				top.show_loading_image('hide');
                if(d.toLowerCase().indexOf('success') > 0){
	                top.alert_notification_show(d);
				}else{
					top.fAlert(d);
				}
				$("#myModal").modal('hide');
				LoadResultSet();
            }
        });
	}
	
	function deleteSelectet(){
		id = '';
		$('.chk_sel').each(function(){
			if($(this).is(':checked')){
				id += $(this).val()+', ';
			}
		});
		if(id!=''){
			top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.deleteModifiers('"+id+"')");
		}else{
			top.fAlert('No Record Selected.');
		}
	}

	function deleteModifiers(id) {
		id = id.substr(0,id.length-2);
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Deleting Record(s)...');
		frm_data = 'pkId='+id+'&task=delete';
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
			
	function popup_dbl(divid,sourceid,destinationid,act,odiv){
		if(act=="single" || act=="all"){
				if(act=='single')	{
					$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
				}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
			}else if(act=="single_remove" || act=="all_remove"){
				if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
				if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
				$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
					var at = $(a).text(), bt = $(b).text();
					return (at > bt)?1:((at < bt)?-1:0);
				}));
				$("#"+destinationid).val('');
			}else{
				$("#"+destinationid+" option").remove();
				$("#"+odiv+" option").clone().appendTo("#"+destinationid);
				$("#"+divid).modal('show');
			}
	}	

	function selected_ele_close(divid,sourceid,destinationid,div_cover,action){
		if(action=="done"){
			var sel_cnt=$("#"+sourceid+" option").length;
			$("#"+divid).hide("clip");
			$("#"+destinationid+" option").each(function(){$(this).remove();})
			$("#"+sourceid+" option").appendTo("#"+destinationid);
			$("#"+destinationid+" option").attr({"selected":"selected"});
			$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
			if(sel_cnt>8){
				$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
			}
            $("#"+divid).modal('hide');
		}else if(action=="close"){
			$("#"+divid).modal('hide');
		}
	}		
</script>
<body>
	<input type="hidden" name="ord_by_field" id="ord_by_field" value="orderset_name">
	<input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
	<div class="whtbox">
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width:20px; padding-left:8px;"><div class="checkbox"><input type="checkbox" name="chk_sel_all" id="chk_sel_all" value=""><label for="chk_sel_all"></label></div></th>
						<th onClick="LoadResultSet('','','','orderset_name',this);">Name<span></span></th>
						<th onClick="LoadResultSet('','','','orders_dx_code',this);">Dx code<span></span></th>
						<th onClick="LoadResultSet('','','','order_id',this);">Orders<span></span></th>
						<th onClick="LoadResultSet('','','','order_set_option',this);">Options<span></span></th>
						<th onClick="LoadResultSet('','','','recall_code',this);">Recall Code<span></span></th>
						<th onClick="LoadResultSet('','','','consult_letter_id',this);">Consult Letter<span></span></th>
					</tr>
				</thead>
				<tbody id="result_set"></tbody>
			</table>
		</div>
	</div>
	<div id="myModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg"> 
			<div class="modal-content">
				<div class="modal-header bg-primary">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title" id="modal_title">Modal Header</h4>
				</div>
				<form name="add_edit_frm" id="add_edit_frm" onSubmit="saveFormData();return false;">
					<input type="hidden" name="id" id="id" value="">
					<input type="hidden" name="sel_orders_dx_code" id="sel_orders_dx_code" value="">
					<input type="hidden" name="sel_order_id" id="sel_order_id" value="">
					<input type="hidden" name="sel_consult_letter_id" id="sel_consult_letter_id" value="">
					<div class="modal-body">
						<div class="form-group">
							<div class="row">
								<div class="col-sm-4">
									<label for="orderset_name">Order set Name</label>
									<input onBlur="changeClass(this)" type="text" tabindex="1" class="form-control" id="orderset_name" name="orderset_name" value="" >
								</div>
								<div class="col-sm-4" onClick="return popup_dbl('pop_up_dx_code','all_dx_code','selected_dx_code','','orders_dx_code')">
									<label for="orders_dx_code"><a href="javascript:void(0);" class="text_purple" onClick="return popup_dbl('pop_up_dx_code','all_dx_code','selected_dx_code','','orders_dx_code')">Dx code</a></label>
									<select name="orders_dx_code" id="orders_dx_code" multiple size="2" class="form-control"></select>
								</div>
								<div class="col-sm-4" onClick="return popup_dbl('pop_order','all_orders','selected_orders','','order_id')">
									<label for="order_id"><a href="javascript:void(0);" class="text_purple" onClick="return popup_dbl('pop_order','all_orders','selected_orders','','Orders')">Order</a></label><br />
									<select name="order_id" id="order_id" multiple size="2" class="form-control"></select>
								</div>
							</div>
							<div class="row">
								<div class="col-sm-4">
									<label for="recall_code">Recall code</label>
									<input class="form-control" type="text" name="recall_code" id="recall_code" value=""> 
								</div>
								<div class="col-sm-4">
									<label for="optName">Options</label>
									<textarea rows="2" name="order_set_option" id="order_set_option" tabindex="3" class="form-control"></textarea>
								</div>
								<div class="col-sm-4" id="div_consult_letter" style="padding-left:10px" onClick="return popup_dbl('pop_consult_letter','all_consult_letters','selected_consult_letters','','consult_letter_id')">
									<label for="consult_letter_id"><a href="javascript:void(0);" class="text_purple" onClick="return popup_dbl('pop_consult_letter','all_consult_letters','selected_consult_letters','','consult_let')">Consult Letter</a></label><br />
									<select name="consult_letter_id" id="consult_letter_id" multiple class="form-control" size="2"></select>
								</div>
							</div>
						</div>
					</div>
					<div id="module_buttons" class="ad_modal_footer modal-footer">
						<button type="submit" class="btn btn-success">Save</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>	
				</form>
			</div>
		</div>
<!----- BEGIN POPUP DIV FOR DX CODES ---------->
<div id="pop_up_dx_code" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" onClick="selected_ele_close('pop_up_dx_code','selected_dx_code','orders_dx_code','div_dx_code','close')">&times;</button>
				<h4 class="modal-title" id="modal_title">Please Add/Remove Dx Codes using Arrow Buttons.</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-5">
						<label> List of Dx Code</label>
						<select class="form-control" id="all_dx_code" name="all_dx_code[]" size="10" multiple="multiple"></select>
					</div>
					<div class="col-sm-2 text-center"><br />
						<input class="btn btn-default"  type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_up_dx_code','all_dx_code','selected_dx_code','all');"><br />
						<input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_up_dx_code','all_dx_code','selected_dx_code','single');"><br />
						<input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_up_dx_code','selected_dx_code','all_dx_code','single_remove');"><br />
						<input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_up_dx_code','selected_dx_code','all_dx_code','all_remove');"><br />
					</div>
					<div class="col-sm-5">
						<label> List of Selected Dx Code</label>
						<select class="form-control" id="selected_dx_code" name="selected_dx_code" size="10" multiple="multiple"></select>
					</div>
				</div>
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<input type="button" class="btn btn-success"  value="Save" onClick="selected_ele_close('pop_up_dx_code','selected_dx_code','orders_dx_code','div_dx_code','done')">
				<input type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_up_dx_code','selected_dx_code','orders_dx_code','div_dx_code','close')">
			</div>
		</div>
	</div>
</div>
<!----- END POPUP DIV FOR DX CODES ---------->

<!----- BEGIN POPUP DIV FOR ORDERS ---------->
<div id="pop_order" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" onClick="selected_ele_close('pop_order','selected_orders','order_id','div_order','close')">&times;</button>
				<h4 class="modal-title" id="modal_title">Please Add/Remove Orders using Arrow Buttons.</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-5">
						<label>List of Orders</label>
						<select class="form-control" id="all_orders" name="all_orders[]"  size="10" multiple="multiple"></select>
					</div>
					<div class="col-sm-2 text-center"><br />
						<input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_order','all_orders','selected_orders','all');"><br />
						<input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_order','all_orders','selected_orders','single');"><br />
						<input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_order','selected_orders','all_orders','single_remove');"><br />
						<input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_order','selected_orders','all_orders','all_remove');">
					</div>
					<div class="col-sm-5">
						<label> List of Selected Orders</label>
						<select class="form-control" id="selected_orders" name="selected_orders"  size="10" multiple="multiple"></select>
					</div>
				</div>
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<input type="button" class="btn btn-success" value="Save" onClick="selected_ele_close('pop_order','selected_orders','order_id','div_order','done')">
				<input type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_order','selected_orders','order_id','div_order','close')">
			</div>
		</div>
	</div>
</div>
<!----- END POPUP DIV FOR ORDERS ----------->
<!----- BEGIN POPUP DIV FOR CONSULT LETTERS ---------->

<div id="pop_consult_letter" class="modal fade" role="dialog">
	<div class="modal-dialog">
	<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header bg-primary">
				<button type="button" class="close" onClick="selected_ele_close('pop_consult_letter','selected_consult_letters','consult_letter_id','div_consult_letter','close')">&times;</button>
				<h4 class="modal-title" id="modal_title">Please Add/Remove Consult Letters using Arrow Buttons.</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-5">
						<label>List of Consult Letters</label>
						<select class="form-control" id="all_consult_letters" name="all_consult_letters[]" size="10" multiple="multiple">
							<?php print $consult_letter_option; ?>
						</select>
					</div>
					<div class="col-sm-2 text-center"><br />
						<input class="btn btn-default" type="button" title="All Move Right" value="&gt;&gt;" onClick="popup_dbl('pop_consult_letter','all_consult_letters','selected_consult_letters','all');"><br />
						<input class="btn btn-default" type="button" title="Selected Move Right" value=" &gt; " onClick="popup_dbl('pop_consult_letter','all_consult_letters','selected_consult_letters','single');"><br />
						<input class="btn btn-default" type="button" title="Selected Move Left" value=" &lt; " onClick="popup_dbl('pop_consult_letter','selected_consult_letters','all_consult_letters','single_remove');"><br />
						<input class="btn btn-default" type="button" title="All Move Left" value="&lt;&lt;" onClick="popup_dbl('pop_consult_letter','selected_consult_letters','all_consult_letters','all_remove');">
					</div>
					<div class="col-sm-5">
						<label>List of Selected Consult Letters</label>
						<select class="form-control" id="selected_consult_letters" name="selected_consult_letters" size="10" multiple="multiple"></select>
					</div>
				</div>
			</div>
			<div id="module_buttons" class="ad_modal_footer modal-footer">
				<input type="button" class="btn btn-success" value="Save" onClick="selected_ele_close('pop_consult_letter','selected_consult_letters','consult_letter_id','div_consult_letter','done')">
				<input type="button" class="btn btn-danger"  name="clos" id="clos_1" value="Close" onClick="selected_ele_close('pop_consult_letter','selected_consult_letters','consult_letter_id','div_consult_letter','close')">
			</div>
		</div>
	</div>
</div>
<!----- END POPUP DIV FOR CONSULT LETTERS ----------->
</div></div>

<script type="text/javascript">
	LoadResultSet();
	var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelectet();"]];
	top.btn_show("ADMN",ar);
	$(document).ready(function(){
       check_checkboxes();
	   set_header_title('Order Sets');
    });
	show_loading_image('none');
</script>
<?php 
	require_once("../../admin_footer.php");
?>
