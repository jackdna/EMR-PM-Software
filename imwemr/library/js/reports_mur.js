// MUR JavaScript Document
//var result_gen = false;
function searchResult(btnNum){
	if(typeof(btnNum)=='undefined') btnNum = $('#task').val();
	df= document.form_mur;
	var dtfrom = $('#dtfrom').val();
	var dtupto = $('#dtupto').val();
	var mur_version = df.mur_version.value;
	var ob = df.provider_multi;
	provider = ob.value;
	l = ob.length;
	str='';

	for(i=0;i<l;i++)
	{
		if(ob.options[i].selected == true){
			if(str != ''){str = str+",";}
			str += ob.options[i].value;
		}
	}
	if(str != '')provider=str;
	
	strfac='';facility = '';
	if(typeof(df.facility_id_multi)!='undefined'){
		var ofm = df.facility_id_multi;
		facility = ofm.value;
		lf = ofm.length;
		for(i=0;i<lf;i++){
			if(ofm.options[i].selected == true){
				if(strfac != ''){strfac = strfac+",";}
				strfac += ofm.options[i].value;
			}
		}
	}
	if(strfac != '') facility=strfac;
	if((mur_version=='2019' || mur_version=='2020') && facility==''){
		top.fAlert('Please Select TIN.');
		return false;
	}
	
	if(provider==0 || provider==''){
		if(mur_version=='2019' || mur_version=='2020'){
			top.fAlert('Please Select NPI.');
		}else{
			top.fAlert('Please Select Provider.');
		}
		document.form_mur.provider_multi.focus();
		return false;
	}
	if(typeof(document.getElementById('provider')) != 'undefined'){
		$('#provider').val(provider);
	}
	
	if(typeof(document.getElementById('facility_id')) != 'undefined'){
		$('#facility_id').val(facility);
	}
	
	//alert(dtfrom+' :: '+dtupto);
	if(dtfrom==0 || dtfrom=='' || dtfrom=='00-00-0000'){
		top.fAlert('Please select "Date From".');
		$('#dtfrom').focus();
		return false;
	}
	if(dtupto==0 || dtupto=='' || dtupto=='00-00-0000'){
		top.fAlert('Please select "Date To".');
		$('#dtupto').focus();
		return false;
	}
	if(btnNum=='2' || btnNum == 7 || btnNum == 8) {
        var ar = [["qrda_cat1","QRDA Cat I","window.frames['fmain'].searchResult(7);"],
                  ["qrda_cat3","QRDA Cat III","window.frames['fmain'].searchResult(8);"]];
        top.btn_show("mur_stage_qrda",ar);
    } else {
        top.btn_show();
    }
	if(btnNum == 5){
		var o = new Array();
		o["task"] = ""+btnNum;
		o["provider"] = ""+provider;
		o["dtfrom"] = ""+dtfrom;
		o["dtupto"] = ""+dtupto;
		downXml(o);			
	}else if(btnNum == 6){
		downeRx();
    }else if(btnNum == 7){		//get cat I
		if(!result_gen){top.fAlert('Please select on "Cal-CMS" option first.'); return false;}
		QRDA('mur/download_qrda_cat1.php');
	}else if(btnNum == 8){		//get cat III
		if(!result_gen){top.fAlert('Please select on "Cal-CMS" option first.'); return false;}
		QRDA('mur/create_qrda_r2_cat3_xml.php');
	}else{
		//top.btn_show('mur_stage1_2015');
		if(btnNum=='1' || btnNum=='2' || btnNum=='3') action='get_report_html';
		form_data = $('#form_mur').serialize();
		top.show_loading_image('show');
		var check_success = false;
		$.ajax({
			url:'mur/mur_report_ajax_handler.php?'+form_data+'&action='+action,//action='+action+'&task='+btnNum+'&provider='+provider+'&dtfrom='+dtfrom+'&dtupto='+dtupto,
			type:'GET',
			success:function(r){
				//a=window.open(); a.document.write(r);return;
				$('#report_result_area').html(r);
				top.show_loading_image('hide');
				//update_basic_points();
				top.btn_show('MURanalyzePrint');
				check_success = true;
			},
			complete:function(r){
				if(!check_success){
					top.fAlert('Request not completed. Please try for a shorter time period.'+"<br>"+r);
				}
				top.show_loading_image('hide');
			}
		});
		
		result_gen = true;
	}
}//end of function searchResult.

function QRDA(val){
	document.form_mur.action=val;
    document.form_mur.method='POST';
	document.form_mur.submit();
}


function downeRx(){
	document.form_mur.action='mur/downerx.php';
	document.form_mur.target='_blank';
	document.form_mur.method='POST';	
	document.form_mur.submit();
}

function print_mur_scorecard(){
	p = document.form_mur.provider.value;
	if(p.indexOf(',')>0) {
	//	top.fAlert('Report can be printed when only EP selected.');
	//	return;
	}
	document.frm_mur_report.submit();
	return;	
}
/****SETTING ACTION TO ACT UPON BONUS CHECKBOXES*****/
$(document).ready(function(e) {
	$(document).on("click", ".point_checkbox", function() {
	  	id 		= $(this).prop('id');
		td_id 	= 'td_'+id;
		if($(this).prop('checked')){$('#'+td_id).html('YES');}
		else{$('#'+td_id).html('NO');}
		
	});
});

function getNum(val) {
   if (isNaN(val)) {
     return 0;
   }
   return parseInt(val);
}

//var all_points_elements = new Array('td_num_erx','td_num_summofcare','td_num_accessphi','td_num_medrecon','td_num_viewphi','td_num_ptedu','td_num_secumsg');
var basic_points_elements = new Array('td_num_erx','td_num_summofcare','td_num_accessphi');
function update_basic_points(){
	basic_point_additions = 0;
	if($('#bsra').prop('checked')){
		if(getNum($('#td_num_erx').text())>0 && getNum($('#td_num_summofcare').text())>0 && getNum($('#td_num_accessphi').text())>0){
/*			$(basic_points_elements).each(function(index, element) {
				basic_point_additions += getNum($('#'+basic_points_elements[index]).next('td').next('td').next('td').text());
			});*/
			basic_point_additions += 50;
		}
	}else if($('#bsra').prop('checked')==false){
		basic_point_additions = 0;
	}
	$('#td_basic_socre').text(basic_point_additions);
	update_performance_points();
}

var all_performance_elements = new Array('td_num_summofcare','td_num_accessphi','td_num_medrecon','td_num_viewphi','td_num_ptedu','td_num_secumsg');
function update_performance_points(){
	performance_points = 0;
	if(parseInt($('#td_basic_socre').text())>0){
		$(all_performance_elements).each(function(index, element) {
			performance_points += getNum($('#'+all_performance_elements[index]).next('td').next('td').next('td').text());
		});
	}
	$('#td_performance_score').text(performance_points);
	update_aci_score()
}

function update_bonus_points(){
	bonus_point_additions = 0;
	if(parseInt($('#td_basic_socre').text())>0){
		if($('#biris').prop('checked') || $('#bssr').prop('checked')){
			bonus_point_additions += 5;
		}
		if($('#iatuc').prop('checked')){
			bonus_point_additions += 10;
		}
	}
	$('#td_bonus_points').text(bonus_point_additions);
	update_aci_score();
}

function update_aci_score(){
	d = 0;
	if(parseInt($('#td_basic_socre').text())>0){
		a = getNum($('#td_basic_socre').text());
		b = getNum($('#td_performance_score').text());
		c = getNum($('#td_bonus_points').text());
		d = a+b+c;
	}
	$('#td_aci_score').text(d);
	
	/*******TOTAL MIPS SCORE**********/
	e = d/100;
	if(e > 1 ){
		$('#td_mips_score').text(25);
	}else{
		f = e*25;
		f = f.toFixed(2);
		$('#td_mips_score').text(f);
	}
	
}


/*******FUNCTIONTO AUDIO NUMERATOR/DENOMINATOR*******/
function showPTs(commaPTids,curObj,PtCat,splCase){if(commaPTids=='') return;
	if(typeof(splCase)=='undefined') splCase = '';
	measure_name = '';
	if(curObj){
		cur_tr = $(curObj).parent('td').parent('tr');
		if(cur_tr) measure_name = cur_tr.find('td.measure_name').html();
	}
	if(commaPTids!=''){
		//top.fAlert(commaPTids,'MUR Audit');
		//alert(commaPTids);
		postVals 	= "mname="+measure_name+"&PtCat="+PtCat+"&comma_patients="+commaPTids+"&specialcase="+splCase;
		top.show_loading_image('show');
		$.ajax({
			url:'mur/mur_report_ajax_handler.php?action=get_audit_patients',
			type:'POST',
			data: postVals,
			success:function(r){
				//top.fAlert(r);top.show_loading_image('hide');return;
				
				if(measure_name!=''){
					r = '<h5 class="text-center bg-primary pd3"><b>'+measure_name+'</b></h5>'+r;
				}
				top.fancyModal(r,'MUR Audit - '+PtCat+' Patients');
				//a=window.open(); a.document.write(r);return;
				//r = jQuery.parseJSON(r);
				top.show_loading_image('hide');
			},
			complete:function(r){
				top.show_loading_image('hide');
			}
		});
	}else{
		top.fAlert(commaPTids,'MUR Audit');
	}
		
}