$(document).ready( function() {
	$('.selectpicker').selectpicker();
});
var ar = [["custom_field_new","New","top.fmain.newForm();"],["custom_field_save","Save","top.fmain.chkForm();"]];
top.btn_show("ADMN",ar);

function getCustSubCategory(objValue,selectedValue,deleteRow){	
	document.getElementById("custCategory").className = "selectpicker";
	for(modCount =0; modCount<document.getElementById("custCategory").length;modCount++){
		
		if(objValue == document.getElementById("custCategory")[modCount].value )	
		document.getElementById("custCategory").options[modCount].selected = true;
		
	}
	if(deleteRow == true){
		var tableRows = document.getElementById('custFields').getElementsByTagName('tr');
		var rowCount = tableRows.length;
		for(var count = 1; count <= rowCount; count++){
			document.getElementById("custFields").deleteRow(0); 	
		}		
	}		
	var patientInfo = new Array("Demographics");
	var medHx = new Array("Medical Hx -> Ocular","Medical Hx -> General Health");
	var workView = new Array("VF","HRT","OCT","Pachy","IVFA","Fundus","External/Anterior","Topography","Ophthalmoscopy");
	var objVal = objValue;		
	switch(objVal){		
		case "Patient_Info":				
			var selectControl = '<select class="form-control" id="custSubCategory" name="custSubCategory[]" multiple size="2">';										  
			for(var intCount = 0; intCount < patientInfo.length; intCount++){
				var selected = "";	
				if(selectedValue == "") selectedValue = "Demographics";	
				var selecteudValue2 = selectedValue.replace(/'/gi,"");				
				var arrSelectedValue = selecteudValue2.split(',');								
				for(var a=0; a<arrSelectedValue.length;a++){
					if(patientInfo[intCount] == arrSelectedValue[a]){					
						selected = "selected";					
					}
				}				
				selectControl += '<option value="'+patientInfo[intCount]+'" '+selected+'>'+patientInfo[intCount]+'</option>'; 	
			}
			selectControl += '</select>'; 
			document.getElementById('tdCustSubCategory').innerHTML = selectControl;	
			document.getElementById("EnableOpt0").disabled = true;		
			break;
		case "Med_Hx":				
			var selectControl = '<select class="form-control" id="custSubCategory" name="custSubCategory[]" multiple size="2" >';										  					
			for(var intCount = 0; intCount < medHx.length; intCount++){
				var selected = "";	
				if(selectedValue == "") selectedValue = "Medical Hx -> Ocular";				
				var selecteudValue2 = selectedValue.replace(/'/gi,"");				
				var arrSelectedValue = selecteudValue2.split(',');								
				for(var a=0; a<arrSelectedValue.length;a++){
					if(medHx[intCount] == arrSelectedValue[a]){					
						selected = "selected";					
					}
				}				
				selectControl += '<option value="'+medHx[intCount]+'" '+selected+'>'+medHx[intCount]+'</option>'; 	
			}
			selectControl += '</select>'; 
			document.getElementById('tdCustSubCategory').innerHTML = selectControl;	
			document.getElementById("EnableOpt0").disabled = false;				
			break;
		case "Work_View":											  			
			var selectControl = '<select class="form-control" id="custSubCategory" name="custSubCategory[]" multiple size="2">';
			for(var intCount = 0; intCount < workView.length; intCount++){
				var selected = "";
				var selecteudValue2 = selectedValue.replace(/'/gi,"");
				var arrSelectedValue = selecteudValue2.split(',');						
				for(var a=0; a<arrSelectedValue.length;a++){
					if(workView[intCount] == arrSelectedValue[a]){
						selected = "selected";					
					}
				}				
				selectControl += '<option value="'+workView[intCount]+'" '+selected+'>'+workView[intCount]+'</option>'; 	
			}
			selectControl += '</select>'; 
			document.getElementById('tdCustSubCategory').innerHTML = selectControl;
						
						
			break;	
	}	
	
}
var count = 0;

function addNewRow(controlLable,fieldType,defaultValue){
	obj = document.getElementById("custSubCategory");
	for(countSubCat=0; countSubCat<=obj.length; countSubCat++){
		if(obj.options[countSubCat] != null && obj.options[countSubCat] != "undefined"){
			if(obj.options[countSubCat].selected){	
			addRow(controlLable,obj[countSubCat].value,fieldType,defaultValue)	
			}
		}
	}
}
function addRow(controlLable,subCatVal,fieldType,defaultValue){	
		
	if(document.getElementById("hidExitsRow").value != ""){				
		count = document.getElementById("hidExitsRow").value;
		rowNum = count;
		document.getElementById("hidExitsRow").value = "";
	}			
	if(controlLable && document.getElementById('custCategory').selectedIndex!=0 && document.getElementById('custSubCategory').selectedIndex!=-1){
		count = parseInt(count);
		
		var newRow = document.getElementById("custFields").insertRow();    
		var td = newRow.insertCell();
		td.innerHTML = subCatVal;
		td = newRow.insertCell();
		td.innerHTML = "<input type='text' name='controlLable[]' id='controlLable"+count+"' class='form-control'  value = \""+controlLable+"\" >";   
		td = newRow.insertCell();
		td.innerHTML = "<input type='text' name='defaultValue[]' id='txtdefaultValue"+count+"' class='form-control' value = \""+defaultValue+"\">";   
		if(fieldType == "check_box"){
			td = newRow.insertCell();					
			td.innerHTML = "<div class=\"checkbox\"><input type='hidden' name='hidFieldType[]' id='hidFieldType"+count+"' value = 'checkbox'><input type='checkbox' name='cbkDefault_"+count+"' id='cbkDefault_"+count+"' value = '1' onClick=\"javascript: selDeSelAllChkBox(this);\"><label for='cbkDefault_"+count+"'>Default</label></div>";   
		}
		else{
			td = newRow.insertCell();					
			td.innerHTML = "<input type='hidden' name='hidFieldType[]' id='hidFieldType"+count+"' value = 'text'>";   
		}
		if(document.getElementById("custCategory").value !="Patient_Info"){
		td = newRow.insertCell();
		td.innerHTML = html_select_speciality(count);  
		
		var values = $("#specialityValue"+count+"").val();
		$("#specialityValueHidd"+count+"").val(values);	
		}
		
		td = newRow.insertCell();
		td.innerHTML = "<input type='hidden' name='subCatValue[]' id='subCatValue"+count+"' value = '"+subCatVal+"' ><input type='button' name='del_catogery[]' id='del_catogery"+count+"' value =\"Delete\" class=\"btn btn-default\" onclick='removeRow(this);'/>";				
		count +=1;
	}
	else if (document.getElementById('custCategory').selectedIndex==0){
			fAlert('Please select customization category to precede add to list.');
	}
	else if (document.getElementById('custSubCategory').selectedIndex==-1){
			fAlert('Please select customization sub category to precede add to list.');
	}
	else{
		fAlert('Please enter field lable to precede add to list.');
	}
	$('.selectpicker').selectpicker('refresh');	
}

function html_select_speciality (count){
	var str = $("#splCategory").val();
	str = str.join(',');
	var selectedArr = str.split(",");
	var	html = "<select name='specialityValue"+count+"[]' class='selectpicker' data-width=\"100%\" multiple id='specialityValue"+count+"'>";
		
	arrSpeciality.each(function(id, val){
		selected = ($.inArray(id,selectedArr)!= -1)?"selected":"";
		html += "<option value="+id+" "+selected+">"+val+"</option>";
	})
	html += '</select>';
	html +='<input type=hidden name="specialityValueHidd[]" id="specialityValueHidd'+count+'">';
	return html;			
}
function removeRow(src,deleteId){
	var oRow = src.parentElement.parentElement;  	
	document.getElementById("custFields").deleteRow(oRow.rowIndex); 
	document.getElementById("hidDelete").value += deleteId+'-';		
}
function getcustomField(val,obj){
	if(val){
		var len = parseInt(obj.length);
		var objValue = ""
		var chosen = "";
		for (i = 0; i < len; i++) {
			if (obj[i].selected) {
				objValue = objValue + "," + "'" + obj[i].value  + "'";
			} 
		}
		objValue = objValue.replace(',','');
		if(objValue){	
			parent.parent.show_loading_image('block');
			var strCustCategory = val;
			var strCustSubCategory = objValue;
			if(strCustCategory && strCustSubCategory){		
				document.getElementById("module").value = strCustCategory;
				document.getElementById("sub_module").value = strCustSubCategory;
				document.customization.submit();
			}
		}
		else{
			fAlert('Please select customization sub category.');	
		}
	}
	else{
		fAlert('Please select customization category.');	
	}
}
function newForm(){
	document.getElementById('custCategory').options[0].selected = true;	
	document.getElementById('controlLabel').value = "";
	document.getElementById('defaultValue').value = "";
	$('.selectpicker').selectpicker('refresh');	
	var selectbox = document.getElementById('custSubCategory');
	for(i=selectbox.options.length-1;i>=0;i--){
		selectbox.remove(i);
	}
	var tableRows = document.getElementById('custFields').getElementsByTagName('tr');
	var rowCount = tableRows.length;
	for(var count = 1; count <= rowCount; count++){
		document.getElementById("custFields").deleteRow(0); 	
	}	
	parent.parent.show_loading_image('none');
}
function chkForm(){
	parent.parent.show_loading_image('none');			
	if(document.getElementById('hidDelete').value == ""){
		var tableRows = document.getElementById('custFields').getElementsByTagName('tr');
		var rowCount = tableRows.length;
		if(rowCount == 0){
			fAlert('Please create customization Template to precede save.');
			return false;
		}
	}
	document.getElementById('save_frm').value = 'save';
	parent.parent.show_loading_image('block');
	document.customization.submit();
}	
function callme(field){	
	if (trim(field.value)!=""){		
		field.className="form-control";
	}
}
function setVal(obj){	
	var val = obj.value;
	if(val == "check_box"){
		document.getElementById("tdval").innerHTML = "Value";
	}
	else{
		document.getElementById("tdval").innerHTML = "Default Value ";
	}
}
function selDeSelAllChkBox(obj){									
	var fieldID = obj.id;
	var control; 		
	for (var i = 0; i < document.customization.elements.length; i++) { 
		control = document.customization.elements[i]; 		
		switch (control.type) { 
			case 'checkbox': 				
				control.checked = false;
			break; 					
		}
	}			
	document.getElementById(fieldID).checked = true;												
}