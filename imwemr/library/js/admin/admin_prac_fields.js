var ar = [["practice_fields_save","Save","top.fmain.save_form();"]];
	top.btn_show("ADMN",ar);
function save_form(){
	document.demographicsMmandatoryFrm.submit();
}
function demographicSelAddMark(val)	{
	if(val=='1'){
		document.getElementById('address_1').checked=true;
		document.getElementById('city1').checked=true;
		document.getElementById('state1').checked=true;
		document.getElementById('zip1').checked=true;
		
		document.getElementById('address_2').checked=false;
		document.getElementById('city2').checked=false;
		document.getElementById('state2').checked=false;
		document.getElementById('zip2').checked=false;
		
	}else if(val=='2'){

		document.getElementById('address_2').checked=true;
		document.getElementById('city2').checked=true;
		document.getElementById('state2').checked=true;
		document.getElementById('zip2').checked=true;
		
		document.getElementById('address_1').checked=false;
		document.getElementById('city1').checked=false;
		document.getElementById('state1').checked=false;
		document.getElementById('zip1').checked=false;
	}
}
function demographicSelPtPortalMark(val){
	if(val=='1'){
		document.getElementById('loginId1').checked=true;
		document.getElementById('password1').checked=true;
		document.getElementById('reenterPassword1').checked=true;
		
		document.getElementById('loginId2').checked=false;
		document.getElementById('password2').checked=false;
		document.getElementById('reenterPassword2').checked=false;	
		
	}else if(val=='2'){

		document.getElementById('loginId2').checked=true;
		document.getElementById('password2').checked=true;
		document.getElementById('reenterPassword2').checked=true;	
		
		document.getElementById('loginId1').checked=false;
		document.getElementById('password1').checked=false;
		document.getElementById('reenterPassword1').checked=false;
	}
}
function responsiblePartySelAddMark(val)	{
	if(val=='1'){
		document.getElementById('rPAddress_1').checked=true;
		document.getElementById('resCity1').checked=true;
		document.getElementById('resState1').checked=true;
		document.getElementById('resZip1').checked=true;
		
		document.getElementById('rPAddress_2').checked=false;
		document.getElementById('resCity2').checked=false;
		document.getElementById('resState2').checked=false;
		document.getElementById('resZip2').checked=false;
		
	}else if(val=='2'){

		document.getElementById('rPAddress_2').checked=true;
		document.getElementById('resCity2').checked=true;
		document.getElementById('resState2').checked=true;
		document.getElementById('resZip2').checked=true;
		
		document.getElementById('rPAddress_1').checked=false;
		document.getElementById('resCity1').checked=false;
		document.getElementById('resState1').checked=false;
		document.getElementById('resZip1').checked=false;
	}
}

function insurenceSelAddMark(_this)	
{
	var t = $(_this).data('type');
	var s = $(_this).val();
	var h = s == 1 ? 2 : 1;
	
	$("#Ins"+t+"AddressId_"+s+", #Ins"+t+"ZipId_"+s+", #Ins"+t+"CityId_"+s+", #Ins"+t+"SateId_"+s).prop('checked',true);
	$("#Ins"+t+"AddressId_"+h+", #Ins"+t+"ZipId_"+h+", #Ins"+t+"CityId_"+h+", #Ins"+t+"SateId_"+h).prop('checked',false);
}

function insurenceSecSelAddMark(val)	{
	if(val=='1'){
		document.getElementById('InsSecAddressId_1').checked=true;
		document.getElementById('InsSecZipId_1').checked=true;
		document.getElementById('InsSecCityId_1').checked=true;
		document.getElementById('InsSecSateId_1').checked=true;
		
		document.getElementById('InsSecAddressId_2').checked=false;
		document.getElementById('InsSecZipId_2').checked=false;
		document.getElementById('InsSecCityId_2').checked=false;
		document.getElementById('InsSecSateId_2').checked=false;
		
	}else if(val=='2'){

		document.getElementById('InsSecAddressId_2').checked=true;
		document.getElementById('InsSecZipId_2').checked=true;
		document.getElementById('InsSecCityId_2').checked=true;
		document.getElementById('InsSecSateId_2').checked=true;
		
		document.getElementById('InsSecAddressId_1').checked=false;
		document.getElementById('InsSecZipId_1').checked=false;
		document.getElementById('InsSecCityId_1').checked=false;
		document.getElementById('InsSecSateId_1').checked=false;
	}
}
function insurenceTerSelAddMark(val)	{
	if(val=='1'){
		document.getElementById('InsTerAddressId_1').checked=true;
		document.getElementById('InsTerZipId_1').checked=true;
		document.getElementById('InsTerCityId_1').checked=true;
		document.getElementById('InsTerSateId_1').checked=true;
		
		document.getElementById('InsTerAddressId_2').checked=false;
		document.getElementById('InsTerZipId_2').checked=false;
		document.getElementById('InsTerCityId_2').checked=false;
		document.getElementById('InsTerSateId_2').checked=false;
	}else if(val=='2'){

		document.getElementById('InsTerAddressId_2').checked=true;
		document.getElementById('InsTerZipId_2').checked=true;
		document.getElementById('InsTerCityId_2').checked=true;
		document.getElementById('InsTerSateId_2').checked=true;
		
		document.getElementById('InsTerAddressId_1').checked=false;
		document.getElementById('InsTerZipId_1').checked=false;
		document.getElementById('InsTerCityId_1').checked=false;
		document.getElementById('InsTerSateId_1').checked=false;
	}
}
top.show_loading_image('none');
set_header_title('Practice Fields');
$(function(){
	$("body").on('click','input[type=checkbox]',function(){
	if($(this).prop("checked") == true) {
		checked = true;
	}else {
		checked = false;
	}
	ele_name = $(this).prop('name');
	$("input[name="+ele_name+"]").prop("checked",false);
	$(this).prop("checked",checked);
	
	switch(ele_name){
		case "address":
			val = $(this).val();
			demographicSelAddMark(val);
		break;
		case "rPAddress":
			val = $(this).val();
			responsiblePartySelAddMark(val);
		break;
		case "ptPortalAccess":
			val = $(this).val();
			demographicSelPtPortalMark(val);
		break;
	}
	});
});