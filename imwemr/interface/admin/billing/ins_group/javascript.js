
//DELETING SMART TAG
function delGrp(id,msg){
	if(typeof(msg)!='boolean'){msg = true;}
	if(msg){
			top.fancyConfirm("Are you sure, you want to delete this Tag?","","window.top.fmain.delGrp('"+id+"',false)")
		}
	else{
		top.show_loading_image('show',200);			
		$.ajax({
			type: "GET",
			url: "InsGroup.class.php?do=delGrp&id="+id,
			success: function(resp){
				top.alert_notification_show('Record Deleted Successfully.');
				$('#div_InsGroup').html(resp);
				top.show_loading_image('hide');
				
			}
		});
	}
}

//TO SEND VALUE IN FORM TO EDIT SMART TAG
function editGrp(id,grpName,nextFunction)
{
	$('#editid').val(id);
	$('#insGrpName').val(grpName);
	$('#btn_save').val('Update Group');
	//if(nextFunction == 'EmptyInsCom'){	$('#div_InsCompany').html('');}
}

//SAVING SMART TAG (SAVE/UPDATE)
function saveGrp(){
	var editId = $('#editid').val();
	var insGrpName = $('#insGrpName').val();
	if(insGrpName) {
		top.show_loading_image('show',200);				
	}
//	alert(editId+', '+tagname);
	$.ajax({
		type: "GET",
		url: "InsGroup.class.php?do=saveGrp&id="+editId+"&insGrpName="+escape(insGrpName),
		success: function(resp){//a = window.open();a.document.write(resp);
			var arrData  = resp.split('~~');
			
			if(arrData[0].toLowerCase().indexOf('success') > 0){
				top.alert_notification_show(arrData[0]);
			}else{
				top.fAlert(arrData[0]);
			}
			
			$('#div_InsGroup').html(arrData[1]);
			top.show_loading_image('hide');
		}
	});
}

//GETTING SECTION PART, LOADING SUBTAGS AFTER CLICKING ON MAIN SMART TAG
function getInsComp(grpID, page,text,grpName){grpName = grpName || '';
	top.show_loading_image('show',200);
	$('#div_InsCompany').html('');
	if(grpName != ""){
		$('#grpName').show();
		$('#grpName').html("Insurance Group: "+grpName);
	}
	$.ajax({
		type: "GET",
		//url: "InsGroup.class.php?do=getInsComp&id="+id+"&grpname="+escape(grpname),
		url: "insurance_companies.php?grpID="+grpID+"&page="+page+"&text="+text,
		success: function(resp){
			//a = window.open(); a.document.write(resp);
			$('#div_InsCompany').html(resp);
			top.show_loading_image('hide');
		}
	});
}//end of getSubTags.

