// MUR JavaScript Document
//var result_gen = false;
function searchResult(btnNum){
	if(typeof(btnNum)=='undefined') btnNum = $('#task').val();
	df= document.form_mur;
	var dtfrom = $('#dtfrom').val();
	var dtupto = $('#dtupto').val();
	
	var ob = df.provider;
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
	if(provider==0 || provider==''){
		top.fAlert('Please Select Provider.');
		document.form_mur.provider.focus();
		return false;
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
		QRDA('./download_qrda_cat1.php');
	}else if(btnNum == 8){		//get cat III
		if(!result_gen){top.fAlert('Please select on "Cal-CMS" option first.'); return false;}
		QRDA('./download_qrda_cat3.php');
	}else{
		//top.btn_show('mur_stage1_2015');
		if(btnNum=='1' || btnNum=='2' || btnNum=='3') action='get_report_html';
		form_data = $('#form_mur').serialize();
		top.show_loading_image('show');
		$.ajax({
			url:'./qrda_report_ajax_handler.php?'+form_data+'&action='+action,
			type:'GET',
			success:function(r){
				//a=window.open(); a.document.write(r);return;
				$('#report_result_area').html(r);
				top.show_loading_image('hide');
				update_basic_points();
			}
		});
		
		result_gen = true;
	}
}//end of function searchResult.

function QRDA(val){
    document.form_mur.action=val;
    
    /*Selected NQF*/
    var selNQF = [];
    $('input.nqfchkbx:checked').each(function(){
		selNQF.push($(this).val());
    });
	if( selNQF.length ==  0 ){
        top.fAlert('Please select atleast one CQM');
        return false;
    }
    selNQF = selNQF.join(',');
	$('input#selectedNQF').val(selNQF);
	document.form_mur.method='POST';
    document.form_mur.submit();
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
function showPTs(commaPTids,curObj){
	measure_name = '';
	if(curObj){
		cur_tr = $(curObj).parent('td').parent('tr');
		if(cur_tr) measure_name = cur_tr.find('td.measure_name').html();
	}
	if(commaPTids!=''){
		//top.fAlert(commaPTids,'MUR Audit');
		//alert(commaPTids);
		
		postVals 	= "comma_patients="+commaPTids;
		top.show_loading_image('show');
		$.ajax({
			url:'mur/mur_report_ajax_handler.php?action=get_audit_patients',
			type:'POST',
			data: postVals,
			success:function(r){
				//alert(r);return;
				if(measure_name!=''){
					r = '<h4 class="text-center"><b>'+measure_name+'</b></h4>'+r;
				}
				top.fAlert(r,'MUR Audit');
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

function selectAllCQM()
{
    if( $('input#selectAllNQF').is(':checked') === true )
    {
	$('input.nqfchkbx:not(":disabled")').prop('checked', true);
    }
    else
    {
	$('input.nqfchkbx:not(":disabled")').prop('checked', false);
    }
}