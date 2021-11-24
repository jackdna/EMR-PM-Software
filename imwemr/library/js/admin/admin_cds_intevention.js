var ar = [["cds_intervention_save","Save","top.fmain.submit_frm();"]];
top.btn_show("ADMN",ar);

function checkCombination() {
	var obj = document.getElementsByName("combination[]");
	var objLength = document.getElementsByName("combination[]").length;
	var q=0;
	for(i=0; i<objLength; i++){
		if(obj[i].checked == true){
			q++;
		}
	}
	return q;
	
}
function submit_frm(){
	var q = checkCombination();
	if(q!='0' && q < 2) {
		fAlert('Please select atleast two combination');
		return false;	
	}
	document.frm_cds.save_data.value = 'save';
	document.frm_cds.submit();
	
}
set_header_title('CDS Intervention');
show_loading_image('none');