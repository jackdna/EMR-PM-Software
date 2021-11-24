var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('pt_id','ptVisit');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Records...');
	
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
	ajaxURL = "pt_chart_locked_ajax.php?task=show_loc_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
		success: function(r) {
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
		for(x in result){no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="elem_unlock[]" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='Pt_name'){
					row	+= '<td>'+tdVal+'</td>';
				}
				if(y=='us_name'){
					row	+= '<td>'+tdVal+'</td>';
				}
				if(y=='tab'){
					tdVal=(tdVal=="WorkView") ? "Work View" : tdVal;
					row	+= '<td>'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
			arrAllShownRecords[pkId] = rowData;
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='6' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);		
	top.show_loading_image('hide');
}
			
			
function checkLockedPt(objFrm){		
	var flg = false;
	var oElem = document.getElementsByName("elem_unlock[]");		
	var len = oElem.length; 
	for(var i=0; i<len; i++){
		if(oElem[i].checked){
			flg = true;
		}
	}
	if(flg == true){
		return true;
	}else {			
		fAlert("Please select any record to unlock.")			
		return false;
	}
}
	
function chart_check_data(){
	if(checkLockedPt(document.elem_frm_Lock))
	document.elem_frm_Lock.submit();
}
var ar = [["saveBtn_Locked","Unlink","top.fmain.chart_check_data();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('Pt Chart Locked');	
});
show_loading_image('none');