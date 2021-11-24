var today = new Date();
var day = today.getDate();
var month = today.getMonth()
var year = y2k(today.getYear());

function y2k(number){
	return (number < 1000)? number+1900 : number;
}

function select_frame(){
	var vendor_name = $("#vendor_name").val();
	if(vendor_name !=''){
		var url = top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php?ajax_request=yes';
		var form_data = 'vendor_name_val='+vendor_name;
		$.ajax({
			type:'POST',
			url:url,
			data:form_data,
			success:function(response){
				var modal_string = $.trim(response);
				if(modal_string != ''){
					$('#frame_search_modal .modal-body').html(modal_string);
				}
				$('#frame_search_modal').modal('show');
			}
		});
	}else{
		top.fAlert('Please provide some input to search');
	}
}

function chkNew(obj_id){
	var obj = $('#'+obj_id+'');
	if($(obj).val() == ''){
		fAlert("Please enter vendor Name.");
	}else{
		var url = top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php?ajax_request=yes';
		var form_data = $('#vendor_search_form').serialize();
		$.ajax({
			url:url,
			data:form_data,
			type:'POST',
			success:function(response){
				var modal_string = $.trim(response);
				if(modal_string != ''){
					$('#frame_search_modal .modal-body').html(modal_string);
				}
			}
		});	
	}
}

/* function get_frame(id){
	$("#vendor_name").val('');
	$("#vendor_name_val").val(id);
	//document.frm_sel.submit();
} */


function get_frames(manf_name){
	var case_val ='';
	$('[name^=order_confirm]').each(function(id,elem){
		if($(elem).prop('selected') === true){
			case_val = $(elem).val();
		}
	});
	
	cptCode = '';
	if(case_val == 'Medicare'){
		cptCode = 'V2020';
	}
	
	var url = 'ajax_handler.php?ajax_request=yes&manf_name='+manf_name+'&cptCode='+cptCode;
	$.ajax({
		type:'POST',
		url:url,
		success:function(response){
			$("#frames_td").html(response);
			return false;
		}
	});
}

function get_frame_name(obj){				
	var vals = obj.value.split(',');						
	var vendor_name = vals[0];
	var color = vals[1];
	var frame_cost = vals[2];
	var style = vals[3];
	var discount = vals[4];
	var frame = vals[5];
	var horizontal = vals[6];
	var bridge = vals[7];
	var vertical = vals[8];
	var diagonal = vals[9];
	var cptVal = vals[10];
	var discount_app = '';
	var actualPer = '';
	if(discount){
		discount_app = parseFloat(discount.substr(0,discount.length-1));
		actualPer = discount.substr(discount.length-1);
	}		
	if(frame_cost){
		frame_cost = parseFloat(frame_cost);
	}
	var deposit =  document.getElementById("deposit");
	var balance = document.getElementById("balance");
	var total = document.getElementById("total");
	var frameCostVal = document.getElementById("frameCostVal");
	var netAmt = 0;
	if(horizontal){
		document.getElementById('frame_a').value = horizontal;
	}
	if(bridge){
		document.getElementById('frame_bridge').value = bridge;
	}
	if(vertical){
		document.getElementById('frame_b').value = vertical;
	}
	if(diagonal){
		document.getElementById('frame_ed').value = diagonal;
	}
	var txtframePrice = document.getElementById("txtframePrice").value.substr(1);
	if(discount){
		var discount_val = discount.substr(0,discount.length-1);
	}
	
	var caseObj = document.getElementsByName("order_confirm");
	var case_val = '';
	for(i=0;i<caseObj.length;i++){
		if(caseObj[i].checked == true){
			case_val = caseObj[i].value;
		}
	}
	
	if(case_val == 'Patient'){			
		document.getElementById("frame_cost").value = js_php_arr.currency+frame_cost.toFixed(2);
	}
	else if(case_val == 'Medicare'){			
		var val = parseFloat(cptVal);
		var adminFrameCost = parseFloat(frame_cost);					
		//var adminFrameDiscount = parseFloat(val_arr[5]);			
		if(val > adminFrameCost){
			document.getElementById("txtframePrice").value = js_php_arr.currency+val;				
			document.getElementById("frame_cost").value = '';
		}
		else{
			document.getElementById("txtframePrice").value = js_php_arr.currency+val;								
			var adjustFrameValue = adminFrameCost - val ;
			document.getElementById("frame_cost").value = js_php_arr.currency+adjustFrameValue;
		}				
	}
	
	
	if(actualPer == 'a')
	{
		discount_val = js_php_arr.currency+discount_val;
		//netAmt = frame_cost - discount_app;
		document.getElementById("discount_frames").value = js_php_arr.currency+discount_app.toFixed(2);
	}
	else if(actualPer == 'p')
	{
		discount_val = discount_val+'%';
		//netAmt = frame_cost - ((frame_cost * discount_app)/100);
		document.getElementById("discount_frames").value = discount_app.toFixed(2);
	}
	
	if(style){
		document.getElementById("frame_style").value = style;
	}
	if(color){
		document.getElementById("frame_color").value = color;
	}
	if(discount_app > 0)
	{			
		document.getElementById("discount_frames").value = discount_val;
		var total_amount = document.getElementById("total").value;
		if(total_amount == '')
		{
			total_amount = js_php_arr.currency+netAmt;	
		}	
		else{
			var lensFrame = parseFloat(total_amount.replace(js_php_arr.currency,''));
			total_amount = js_php_arr.currency+(netAmt + lensFrame);
		}
		document.getElementById("total").value = total_amount;
	}	
	get_total();	
}

function fill_price(){
	var obj = document.getElementById("frame_scr");
	var filed_val = '';
	if(obj.value == 'Yes'){
		var caseObj = document.getElementsByName("order_confirm");
		var case_val = '';
		for(i=0;i<caseObj.length;i++){
			if(caseObj[i].checked == true){
				case_val = caseObj[i].value;
			}
		}
		var cptCode = '';
		switch(case_val){
			case 'Medicare':
				cptCode = 'V2760';
			break;
			default:
				cptCode = 'SCRATCH COATING';
			break;
		}			
	}
	else{
		document.getElementById("scr_cost").value = '';
		get_total();
	}
	if(cptCode){
		var url = top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php?ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode;
		var data = 'ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode;
		$.ajax({
			type:'POST',
			url:url,
			data:data,
			success:function(response){
				var val_arr = response.split('__');
				var val = val_arr[1];

				if(val){
					$("#prism_cost").val(js_php_arr.currency+val);
				}
				else{
					top.fAlert('CPT '+val_arr[0]+' does not Exists');
					$("#prism_cost").val('');
				}
				get_total();
				top.show_loading_image('hide');
			}
		});
	}
}



function getPrismCode(obj){
	checked_val = $(obj).val();
	get_total();
	if(checked_val){
		var case_val ='';
		$('[name^=order_confirm]').each(function(id,elem){
			if($(elem).prop('selected') === true){
				case_val = $(elem).val();
			}
		});

		if(case_val == 'Medicare'){
			cptCode = 'V2715';
		}
		else{
			cptCode = 'PRISM';
		}

		if(cptCode){
			var url = top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php?ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode;
			var data = 'ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode;
			$.ajax({
				type:'POST',
				url:url,
				data:data,
				success:function(response){
					var val_arr = response.split('__');
					var val = val_arr[1];

					if(val){
						$("#prism_cost").val(js_php_arr.currency+val);
					}
					else{
						top.fAlert('CPT '+val_arr[0]+' does not Exists');
						$("#prism_cost").val('');
					}
					get_total();
					top.show_loading_image('hide');
				}
			});
		}
		else{
			$("#prism_cost").val('');
			get_total();
		}
	}
	else{
		$("#prism_cost").val('');
		get_total();
	}
}

	function getUvCost(){
		var checked_val = document.getElementById("frame_uv").value;
		if(checked_val == 'Yes'){
			var caseObj = document.getElementsByName("order_confirm");
			var case_val = '';
			for(i=0;i<caseObj.length;i++){
				if(caseObj[i].checked == true){
					case_val = caseObj[i].value;
				}
			}
			if(case_val == 'Medicare'){
				cptCode = '2755';
			}
			else{
				cptCode = 'UV';
			}
			if(cptCode){
				var url = top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php?ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode;
				var data = 'ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode;

				$.ajax({
					type:'POST',
					url:url,
					data:data,
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							document.getElementById("uv_cost").value = js_php_arr.currency+val;
						}
						else{
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("uv_cost").value = '';
						}
						get_total();
						parent.show_loading_image('hide');
					}
				});
			}
			else{
				document.getElementById("uv_cost").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("uv_cost").value = '';
			get_total();
		}		
	}
	
	function fill_cost(obj) {   
		obj.value = js_php_arr.currency;
		var range = obj.createTextRange();   
		range.move("character", 1);   
		range.select();   
	} 
	
	//function for checking field value should be numeric
	function checkFieldData(id){
		var field = document.getElementById(id);
		var getNum = field.value.replace(js_php_arr.currency,"");
		getNum = getNum.replace(".","");
		
		if(isNaN(getNum) || getNum.indexOf('.') > -1)
		{
			top.fAlert('Field must contain a numeric value');
			field.value = '';
			return false;
		}
	}
	
	function priceValid(obj){
		checkFieldData(obj.id);		
		var field = document.getElementById(obj.id);		
		if(field.value != '')
		{
			var val = field.value.replace(js_php_arr.currency,"");
			
			if(val.length <= 3)
			{
				if(val.indexOf('.') == 1)
				{
					field.value = js_php_arr.currency+val+"0";
				}
				else if(val.indexOf('.') < 0)
				{
					field.value = js_php_arr.currency+val+".00";
				}
			}
			else if(val.length == 4)
			{
				if(val.indexOf('.') == 1)
				{
					field.value = js_php_arr.currency+val;	
				}
				else if(val.indexOf('.') == 2)
				{
					field.value = js_php_arr.currency+val+"0";	
				}
				else if(val.indexOf('.') < 0)
				{
					var setDec = val.split('');
					setDec.splice(3,0,".");
					setDec.splice(5,0,"0");
					field.value = js_php_arr.currency;
					
					for(var i=0;i<setDec.length;i++)
					{
						field.select();
						field.value += setDec[i];
					}
				}
			}
			else if(val.length > 4)
			{
				field.value = js_php_arr.currency;
				if(val.indexOf('.') == 1)
				{
					var setDecimal = val.split('');
					for(var j=0;j<4;j++)
					{
						field.value += setDecimal[j];
					}		
				}
				else if(val.indexOf('.') == 2)
				{
					var setDecimal = val.split('');
					for(var j=0;j<5;j++)
					{
						field.value += setDecimal[j];
					}		
				}
				else if(val.indexOf('.') == 3)
				{
					var setDecimal = val.split('');
					for(var j=0;j<6;j++)
					{
						field.value += setDecimal[j];
					}		
				}
				else if(val.indexOf('.') < 0)
				{
					var setDecimal = val.split('');
					setDecimal.splice(3,0,".");
					
					
					for(var j=0;j<6;j++)
					{
						field.select();
						field.value += setDecimal[j];
					} 
				}
			}
		}
	}
	
	//function for setting currency sign
	function setCurrSign(obj){		
		var field = document.getElementById(obj.id);
		if(field.value == '' || field.value == null){
			field.select();
			field.value = js_php_arr.currency;
		}
		else{
			return false;
		}
	}
	
	function getcur_Dates(objss){
		var curdate=new Date();
		var months = curdate.getMonth()+1;	
		if(months<10){
			var month="0"+parseInt(months);
		}else{
			var month=parseInt(months);
		}
		var mdays = curdate.getDate();
		if(mdays<10){
			var mday="0"+mdays;
		}else{
			var mday=mdays;
		}
		var years = curdate.getYear();
		document.getElementById(objss.name).value=""+month+"-"+mday+"-"+year;
	}
	

	function get_total(){
		var lensArr = new Array("prism_cost","polar_cost","trans_cost","scr_cost","ar_cost","tint_cost_price","hi_cost_price","Slad_Off_cost","uv_cost","Photochromatic_cost","lenese_cost");
		var frame_val = "frame_cost";		
		var frameDiscount = $('#discount_frames');
		var dis_actual_per = $('#dis_actual_per');
		var frame_dis_ap = $('#frame_dis_ap');
		var total = $('#total');
		var bal = $('#balance');
		var deposit = $('#deposit');
		var vendor_name = $('#vendor_name').val();	
		var txtUnit = $('#txtUnit').val();	
		var setTotal = 0;
		var frameTotal = 0;
		var netCost = 0;

		$.each(lensArr, function(id,val){
			var elem = $('#'+val+'');
			if(elem.val() != ''){
				if(id == 0 && vendor_name != ''){
					var lensObj = elem;
					lensObj = lensObj.val().replace(js_php_arr.currency,'');				
					setTotal += parseFloat(lensObj);
				}
				else if(id > 0){
					var lensObj = elem;
					lensObj = lensObj.val().replace(js_php_arr.currency,'');						
					setTotal += parseFloat(lensObj);				
				}
			}
		});
		
		//--- Frame Discounts ----
		var first_val = frameDiscount.val().substr(0,1);

		var last_val = frameDiscount.val().substr(frameDiscount.val().length -1);
		var txtframePrice = $("#txtframePrice").val().replace(js_php_arr.currency,'');
		var frame_total = $("#frame_cost").val().replace(js_php_arr.currency,'');
		if(txtframePrice){
			frame_total = parseFloat(txtframePrice) + parseFloat(frame_total);
		}
		
		if(frameDiscount.val()){
			if(first_val == js_php_arr.currency || last_val == '%'){			
				if(first_val == js_php_arr.currency ){
					lensDiscount = parseFloat(frameDiscount.val().substr(1));
					frame_total = frame_total - lensDiscount;
				}
				else if(last_val == '%'){
					lensDiscount = parseFloat(frameDiscount.val().substr(0,frameDiscount.val().length -1));
					frame_total = frame_total - (frame_total * lensDiscount/100);
				}
				frame_total = frame_total.toFixed(2);
			}
			else{
				top.fAlert('Discount could be in actual or in percentage');
				frameDiscount.val('');
				frameDiscount.select();
				return false;
			}
		}
		if(frame_total > 0){
			setTotal = parseFloat(setTotal) + parseFloat(frame_total);
		}
		//--- Lens Discounts --------
		var lensDiscount = $('#discount');
		var first_val = lensDiscount.val().substr(0,1);
		var last_val = lensDiscount.val().substr(lensDiscount.val().length -1);
		var lenese_cost_medicare = $("#lenese_cost").val().replace(js_php_arr.currency,'');
		var lenese_cost = $("#adminPatientLenseCost").val().replace(js_php_arr.currency,'');
		
		if(lensDiscount.val()){			
			if(first_val == js_php_arr.currency || last_val == '%'){	
				if(lenese_cost_medicare){		
					lenese_cost = parseFloat(lenese_cost) + parseFloat(lenese_cost_medicare);
				}
				if(first_val == js_php_arr.currency ){
					lensDiscount = parseFloat(lensDiscount.val().substr(1));
					lenese_cost = lenese_cost - lensDiscount;
				}
				else if(last_val == '%'){
					lensDiscount = parseFloat(lensDiscount.val().substr(0,lensDiscount.val().length -1));
					lenese_cost = lenese_cost - (lenese_cost * lensDiscount/100);					
				}
				lenese_cost = lenese_cost.toFixed(2);
			}
			else{
				top.fAlert('Discount could be in actual or in percentage');
				lensDiscount.val('');
				lensDiscount.select();
				return false;
			}
		}
		if(lenese_cost > 0){			
			setTotal = parseFloat(setTotal) + parseFloat(lenese_cost);
		}
		
		if(setTotal < 0){
			top.fAlert('Discount could not be more than total amount');
			frameDiscount.val('');
			frameDiscount.select();
			return false;
		}
		
		netCost = setTotal;
		if(setTotal >= 0){
			if(deposit.val() == ''){	
				total.val(js_php_arr.currency+netCost.toFixed(2));
				bal.val(js_php_arr.currency+netCost.toFixed(2));
			}
			else if(deposit.val() != ''){
				total.val(js_php_arr.currency+netCost.toFixed(2));
				var depVal = parseFloat(deposit.val().replace(js_php_arr.currency,''));
				bal.val(js_php_arr.currency+(netCost - depVal).toFixed(2));
			}
		}
	}
	
	
	function getLensCptCost(val){
		var caseObj = document.getElementsByName("order_confirm");
		var case_val = '';
		for(i=0;i<caseObj.length;i++){
			if(caseObj[i].checked == true){
				case_val = caseObj[i].value;
			}
		}	
		var cptCode = ''
		var unit_val = 2;
		switch(val){
			case 'Single Vision':				
				switch(case_val){	
					case 'Patient':
						cptCode = 'V2100';
					break;
					case 'Commercial':
						cptCode = '92340';
					break;
					case 'Medicare':
						cptCode = 'V2100';
						unit_val = 2;
					break;
				}
			break;
			case 'Bifocal':
				switch(case_val){	
					case 'Patient':
						cptCode = 'V2203';
					break;
					case 'Commercial':
						cptCode = '92341';
					break;
					case 'Medicare':
						cptCode = 'V2203';
						unit_val = 2;
					break;
				}
			break;
			case 'Trifocal':
				switch(case_val){	
					case 'Patient':
						cptCode = '2305';
					break;
					case 'Commercial':
						cptCode = '2305';
					break;
					case 'Medicare':
						cptCode = '2305';
						unit_val = 2;
					break;
				}
			break;
			case 'Progressive':
				switch(case_val){	
					case 'Patient':
						cptCode = 'V2300';
					break;
					case 'Commercial':
						cptCode = '92342';
					break;
					case 'Medicare':
						cptCode = 'V2300';
						unit_val = 2;
					break;
				}
			break;
			case 'Deluxe Progressive':
				switch(case_val){	
					case 'Patient':
						cptCode = '2781';
					break;
					case 'Commercial':
						cptCode = '92342';
					break;
					case 'Medicare':
						cptCode = '2781';
						unit_val = 2;
					break;
				}
			break;
		}		
		if(cptCode){
			document.getElementById("txtUnit").value = unit_val;
			$.ajax({
				url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
				data:'ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode+'&lenseType='+val,
				type:'POST',
				success:function(response){
					getCptCost(response);
				}
			});
		}
	}
	
	function getCptCost(response){
		var val_arr = response.split('__');						
		var val = val_arr[1];
		var adminLenseCost = val_arr[2];					
		var adminLensDiscount = val_arr[3];		
		
		var caseObj = document.getElementsByName("order_confirm");
		var case_val = '';
		for(i=0;i<caseObj.length;i++){
			if(caseObj[i].checked == true){
				case_val = caseObj[i].value;
			}
		}			
		
		if(case_val == 'Medicare'){		
			if(val){
				if(val < adminLenseCost){
					val = val * 2;
					document.getElementById("lenese_cost").value = js_php_arr.currency+val;
					document.getElementById("cptVal").value = val;
				}
				else{					
					document.getElementById("cptVal").value = val;					
					document.getElementById("adminLenseCost").value = adminLenseCost;			
					adminLenseCost = parseFloat(adminLenseCost) * 2;										
					val = val * 2;
					document.getElementById("lenese_cost").value = js_php_arr.currency+val;
					var adjustLenseValue = adminLenseCost - val ;
					document.getElementById("adminPatientLenseCost").value = js_php_arr.currency+adjustLenseValue;
					document.getElementById("discount").value = adminLensDiscount+'%';
				}
			}
			else{
				top.fAlert('CPT '+val_arr[0]+' does not Exists');
				document.getElementById("lenese_cost").value = '';
				document.getElementById("adminPatientLenseCost").value = '';
			}
			parent.show_loading_image('hide');		
			get_total();
		}
		else if(case_val == 'Patient' || case_val == 'Commercial'){						
			if(val){
				if(val < adminLenseCost){
					val = val * 2;
					document.getElementById("lenese_cost").value = js_php_arr.currency+val;
					document.getElementById("cptVal").value = val;
				}
				else{					
					document.getElementById("cptVal").value = val;					
					document.getElementById("adminLenseCost").value = adminLenseCost;					
					adminLenseCost = parseFloat(adminLenseCost) * 2;
					document.getElementById("adminPatientLenseCost").value = js_php_arr.currency+adminLenseCost;
					document.getElementById("discount").value = adminLensDiscount+'%';
				}
			}
			else{
				top.fAlert('CPT '+val_arr[0]+' does not Exists');
				document.getElementById("lenese_cost").value = '';
				document.getElementById("adminPatientLenseCost").value = '';
			}
			parent.show_loading_image('hide');
			get_total();			
		}
	}
	
	function getPolariodCost(){
		var checked_val = document.getElementById("Polaroid_material").checked;
		if(checked_val == true){
			cptCode = 'POLAROID';
			if(cptCode){
				$.ajax({
					type:'POST',
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode,
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							$("#polar_cost").value = js_php_arr.currency+val;
						}
						else{
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("polar_cost").value = '';
						}
						parent.show_loading_image('hide');
						get_total();	
					}
				});
			}else{
				document.getElementById("polar_cost").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("polar_cost").value = '';
			get_total();
		}		
	}
	
	function getTransCost(){
		var checked_val = document.getElementById("trans_cost").value;
		if(checked_val == ''){
			var caseObj = document.getElementsByName("order_confirm");
			var case_val = '';
			for(i=0;i<caseObj.length;i++){
				if(caseObj[i].checked == true){
					case_val = caseObj[i].value;
				}
			}
			if(case_val == 'Medicare'){
				cptCode = 'V2744';
			}
			else{
				cptCode = 'TRANS';
			}
			if(cptCode){
				$.ajax({
					type:'POST',
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&get_cpt_cost=yes&cptCode='+cptCode,
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							document.getElementById("trans_cost").value = js_php_arr.currency+val;
						}
						else{						
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("trans_cost").value = '';
						}
						parent.show_loading_image('hide');
						get_total();
					}
				});
			}
			else{
				document.getElementById("trans_cost").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("trans_cost").value = '';
			get_total();
		}		
	}
	
	function getSladOffCost(checked_val){
		if(checked_val == true){
			var caseObj = document.getElementsByName("order_confirm");
			var case_val = '';
			for(i=0;i<caseObj.length;i++){
				if(caseObj[i].checked == true){
					case_val = caseObj[i].value;
				}
			}
			if(case_val == 'Medicare'){
				cptCode = 'V2710';
			}
			else{
				cptCode = 'SLAB OFF';
			}
			if(cptCode){
				$.ajax({
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&cptCode='+cptCode,
					type:'POST',
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							document.getElementById("Slad_Off_cost").value = js_php_arr.currency+val;
						}
						else{
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("Slad_Off_cost").value = '';
						}
						parent.show_loading_image('none');
						get_total();
					}
				});
			}
			else{
				document.getElementById("Slad_Off_cost").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("Slad_Off_cost").value = '';
			get_total();
		}		
	}
	
	function getArCost(){
		var checked_val = document.getElementById("ar_charge").checked;
		if(checked_val == true){
			var caseObj = document.getElementsByName("order_confirm");
			var case_val = '';
			for(i=0;i<caseObj.length;i++){
				if(caseObj[i].checked == true){
					case_val = caseObj[i].value;
				}
			}
			if(case_val == 'Medicare'){
				cptCode = 'V2750';
			}
			else{
				cptCode = 'A/R';
			}
			if(cptCode){
				$.ajax({
					type:'POST',
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&cptCode='+cptCode,
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							document.getElementById("ar_cost").value = js_php_arr.currency+val;
						}
						else{
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("ar_cost").value = '';
						}
						get_total();
					}
				});
			}
			else{
				document.getElementById("ar_cost").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("ar_cost").value = '';
			get_total();
		}		
	}
	
	function getPhotochromaticCost(){
		var checked_val = document.getElementById("Photochromatic").checked;
		if(checked_val == true){
			var caseObj = document.getElementsByName("order_confirm");
			var case_val = '';
			for(i=0;i<caseObj.length;i++){
				if(caseObj[i].checked == true){
					case_val = caseObj[i].value;
				}
			}
			if(case_val == 'Medicare'){
				cptCode = 'V2744';
			}
			else{
				cptCode = 'PHOTOCHROMATIC';
			}
			if(cptCode){
				
				var url = 'getCptCost.php?cptCode='+cptCode;
				$.ajax({
					type:'POST',
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&cptCode='+cptCode,
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							document.getElementById("Photochromatic_cost").value = js_php_arr.currency+val;
						}
						else{
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("Photochromatic_cost").value = '';
						}
						get_total();
						parent.show_loading_image('none');
					}
				});
			}
			else{
				document.getElementById("Photochromatic_cost").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("Photochromatic_cost").value = '';
			get_total();
		}		
	}
	
	function getTintCptCost(checked_val){
		if(checked_val){
			var caseObj = document.getElementsByName("order_confirm");
			var case_val = '';
			for(i=0;i<caseObj.length;i++){
				if(caseObj[i].checked == true){
					case_val = caseObj[i].value;
				}
			}
			if(case_val == 'Medicare'){
				cptCode = 'Tint';
			}
			else{
				cptCode = 'MISC';
			}
			if(cptCode){
				
				$.ajax({
					type:'POST',
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&cptCode='+cptCode,
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							document.getElementById("tint_cost_price").value = js_php_arr.currency+val;
						}
						else{
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("tint_cost_price").value = '';
						}
						get_total();
						parent.show_loading_image('none');
					}
				});
			}
			else{
				document.getElementById("tint_cost_price").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("tint_cost_price").value = '';
			get_total();
		}		
	}
	
	function getHiCptCost(){
		var checked_val = document.getElementById("HT_lens").checked;
		if(checked_val == true){
			var caseObj = document.getElementsByName("order_confirm");
			var case_val = '';
			for(i=0;i<caseObj.length;i++){
				if(caseObj[i].checked == true){
					case_val = caseObj[i].value;
				}
			}
			if(case_val == 'Medicare'){
				cptCode = 'Hi/SV';
			}
			else{
				cptCode = 'MISC';
			}
			if(cptCode){
				$.ajax({
					type:'POST',
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&cptCode='+cptCode,
					success:function(response){
						var val_arr = response.split('__');
						var val = val_arr[1];
						if(val){
							document.getElementById("hi_cost_price").value = js_php_arr.currency+val;
						}
						else{
							top.fAlert('CPT '+val_arr[0]+' does not Exists');
							document.getElementById("hi_cost_price").value = '';
						}
						get_total();
						parent.show_loading_image('none');
					}
				});
			}
			else{
				document.getElementById("hi_cost_price").value = '';
				get_total();
			}
		}
		else{
			document.getElementById("hi_cost_price").value = '';
			get_total();
		}		
	}
	
	function getLensCost12(check_val){
		if(check_val == 'Medicare' || check_val == 'Patient'){			
			document.getElementById("txtUnit").value = 2;
			var frameMake = document.getElementById("frames_name").value;
			frameMake = frameMake.split(',');			
			frameMake = frameMake[5];
			cptCode = 'V2020';
			if(cptCode){
				if(frameMake){
					$.ajax({
						type:'POST',
						url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
						data:'ajax_request=yes&get_cpt_cost=yes&frameMake='+frameMake+'&cptCode='+cptCode,
						success:function(response){
							var frame_cost = document.getElementById("frame_cost").value;
							document.getElementById("frameTypeHide").value = frame_cost;							
							var val_arr = response.split('__');							
							var val = parseFloat(val_arr[1]);
							var adminFrameCost = parseFloat(val_arr[4]);
							var adminFrameDiscount = parseFloat(val_arr[5]);
							if(check_val == 'Medicare'){
								if(val){						
									if(val > adminFrameCost){
										document.getElementById("frame_cost").value = js_php_arr.currency+val;
										get_total()
									}
									else{
										document.getElementById("txtframePrice").value = js_php_arr.currency+val;								
										var adjustFrameValue = adminFrameCost - val ;
										document.getElementById("frame_cost").value = js_php_arr.currency+adjustFrameValue;
										get_total()
									}							
								}
								else{
									top.fAlert('CPT '+val_arr[0]+' does not Exists');
									document.getElementById("txtframePrice").value = '';
									get_total();
								}
							}	
							else if(check_val == 'Patient'){						
								document.getElementById("txtframePrice").value = '';		
								document.getElementById("frame_cost").value = js_php_arr.currency+adminFrameCost;	
								get_total();							
							}				
							parent.show_loading_image('none');
						}
					});
				}
			}							
		}
		else{
			document.getElementById("txtframePrice").value = '';
			var frameTypeHide = document.getElementById("frameTypeHide").value;
			document.getElementById("frame_cost").value = frameTypeHide;
			var lenseTypeHide = document.getElementById("lenseTypeHide").value;
			document.getElementById("adminPatientLenseCost").value = lenseTypeHide; 
			document.getElementById("lenese_cost").value = '';
			get_total();
		}
		
		if(check_val == 'Medicare' || check_val == 'Patient'){			
			cptCode = 'V2100';
			var lenseType = document.getElementById("lens_opt").value;
			if(lenseType){
				if(cptCode){				
				$.ajax({
					type:'POST',
					url:top.JS_WEB_ROOT_PATH+'/interface/optical/ajax_handler.php',
					data:'ajax_request=yes&get_cpt_cost=yes&cptCode='+cptCode+'&lenseType='+lenseType,
					success:function(response){
						var val_arr = xmlHttp2.responseText.split('__');			
						var val = val_arr[1];
						var adminLenseCost = val_arr[2];					
						var adminLensDiscount = val_arr[3];			
						if(check_val == 'Medicare'){		
							if(val){
								if(val < adminLenseCost){
									val = val * 2;
									document.getElementById("lenese_cost").value = js_php_arr.currency+val;
									document.getElementById("cptVal").value = val;
								}
								else{					
									document.getElementById("cptVal").value = val;					
									document.getElementById("adminLenseCost").value = adminLenseCost;
									adminLenseCost = adminLenseCost * 2;
									val = val * 2;
									document.getElementById("lenese_cost").value = js_php_arr.currency+val;
									var adjustLenseValue = adminLenseCost - val ;
									document.getElementById("adminPatientLenseCost").value = js_php_arr.currency+adjustLenseValue;
									document.getElementById("discount").value = adminLensDiscount+'%';
								}
							}
							else{
								top.fAlert('CPT '+val_arr[0]+' does not Exists');
								document.getElementById("lenese_cost").value = '';
								document.getElementById("adminPatientLenseCost").value = '';
							}
						}
						else if(check_val == 'Patient'){
							document.getElementById("txtUnit").value = 2;
							if(val){
								if(val < adminLenseCost){
									val = val * 2;
									document.getElementById("lenese_cost").value = js_php_arr.currency+val;
									document.getElementById("cptVal").value = val;
								}
								else{					
									document.getElementById("cptVal").value = val;					
									document.getElementById("adminLenseCost").value = adminLenseCost;
									adminLenseCost = adminLenseCost * 2;
									//val = val * 2;
									document.getElementById("lenese_cost").value = '';
									//var adjustLenseValue = adminLenseCost - val ;
									document.getElementById("adminPatientLenseCost").value = js_php_arr.currency+adminLenseCost;
									document.getElementById("discount").value = adminLensDiscount+'%';
								}
							}
							else{
								top.fAlert('CPT '+val_arr[0]+' does not Exists');
								document.getElementById("lenese_cost").value = '';
								document.getElementById("adminPatientLenseCost").value = '';
							}
						}					
						parent.show_loading_image('none');
						get_total();
					}
				});
				}	
			}					
		}		
	}
	
	function calLenseCost(obj){
		var myValue = parseFloat(obj.value);
		var myVal = parseFloat(document.getElementById("cptVal").value);
		var myAdminLenseCost = parseFloat(document.getElementById("adminLenseCost").value);				
		var myCptValNew = myVal * myValue;
		var myAdminLenseCostNew = myAdminLenseCost * myValue;
		var caseObj = document.getElementsByName("order_confirm");
		var case_val = '';
		for(i=0;i<caseObj.length;i++){
			if(caseObj[i].checked == true){
				case_val = caseObj[i].value;
			}
		}		
		if(case_val == 'Medicare'){
			if(myCptValNew > myAdminLenseCostNew){			
			document.getElementById("lenese_cost").value = js_php_arr.currency+myCptValNew;			
			}
			else{								
				document.getElementById("lenese_cost").value = js_php_arr.currency+myCptValNew;
				var adjustLenseValue = myAdminLenseCostNew - myCptValNew ;			
				document.getElementById("adminPatientLenseCost").value = js_php_arr.currency+adjustLenseValue;
			}
		}	
		if(case_val == 'Patient'){				
			document.getElementById("lenese_cost").value = '';										
			document.getElementById("adminPatientLenseCost").value = js_php_arr.currency+myAdminLenseCostNew;
			
		}
		get_total();	
	}
	
	function setLastOrderVal(){
		if(document.getElementById('reorder').checked && document.getElementById('txt_order_save_id').value == ''){
			var id = atob(js_php_arr.p_id);
			reOrderVal(id);
		}
	}
	
	function reOrderVal(id){
		var reOrderArr = new Array("sphere_od","cyl_od","axis_od","add_od","elem_visMrOdP","elem_visMrOdPrism","elem_visMrOdSlash",
								   "elem_visMrOdSel1","optic_ht","dist_pd_od","near_pd_od","base_od","sphere_os","cyl_os","axis_os",
								   "add_os","elem_visMrOsP","elem_visMrOsPrism","elem_visMrOsSlash","elem_visMrOsSel1","optic_ht_os",
								   "dist_pd_os","near_pd_os","base_os","vendor_name","frame_name","frame_style","frame_color","frame_eye",
								   "frame_bridge","frame_a","frame_b","frame_ed","temple","frame_scr","frame_uv","lens_opt","bifocal_opt",
								   "lens_material","tini_opt","HT_lens","ar_charge","ar_desc","frame_cost","Notification_comments","ref_frame_order",
								   "lenese_cost","lens_order","ref_lens_order","tint_cost","frame_recieve","ref_frame_recieve","polar_cost",
								   "lens_recieve","ref_lens_recieve","trans_cost","patient_notify","ref_pt_notify","scr_cost","patient_picked_up",
								   "ref_pt_picked","ar_cost","sale_date","ref_date_sale","other_cost","other_cost","promotions","discount_frames",
								   "paid_by","payment_method","discount","comments","total","balance"
								   );	
		
		$.ajax({
			type:'POST',
			url:top.JS_WEB_ROOT_PATH+"/interface/optical/ajaxData.php?patientId="+id,
			success:function(response){
				var str = response;
				for(var i=0;i<reOrderArr.length;i++)
				{
					if(str.getElementsByTagName(reOrderArr[i])[0].firstChild)
					{
						if(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue != '0' && str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue != '0000-00-00')
						{
							if(str.getElementsByTagName(reOrderArr[i])[0].nodeName == 'lens_opt')
							{
								if(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue == 'Single Vision')
								{
									lensTypeSet(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue)	
								}
								else if(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue == 'Bifocal')
								{
									lensTypeSet(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue)	
								}
								else if(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue == 'Trifocal')
								{
									lensTypeSet(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue)	
								}
								else if(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue == 'Progressive')
								{
									lensTypeSet(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue)	
								}
							}
							
							if(str.getElementsByTagName(reOrderArr[i])[0].nodeName.indexOf('cost') >= 0 || str.getElementsByTagName(reOrderArr[i])[0].nodeName.indexOf('total') >= 0 || str.getElementsByTagName(reOrderArr[i])[0].nodeName.indexOf('balance') >= 0 || str.getElementsByTagName(reOrderArr[i])[0].nodeName.indexOf('deposit') >= 0 || str.getElementsByTagName(reOrderArr[i])[0].nodeName.indexOf('discount') >= 0)
							{
								document.getElementById(reOrderArr[i]).value = "$"+(parseFloat(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue)).toFixed(2);
								if(str.getElementsByTagName(reOrderArr[i])[0].nodeName.indexOf('discount') >= 0 && str.getElementsByTagName("frame_dis_ap")[0].firstChild.nodeValue == 'percent')
								{
									document.getElementById(reOrderArr[i]).value = (parseFloat(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue)).toFixed(2);		
								}
								else if(str.getElementsByTagName(reOrderArr[i])[0].nodeName.indexOf('discount') >= 0 && str.getElementsByTagName("frame_dis_ap")[0].firstChild.nodeValue == 'actual')
								{
									document.getElementById(reOrderArr[i]).value = "$"+(parseFloat(str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue)).toFixed(2);
									
								}
							}
							else
							{
								document.getElementById(reOrderArr[i]).value = str.getElementsByTagName(reOrderArr[i])[0].firstChild.nodeValue;
							}
						}
					}
				}
			}
		});	
	}
	
	function lensTypeSet(id){
		var singleVision = new Array('DV','NV');
		var bifocal = new Array('FT 28','FT 35','FT 22','Blended');
		var trifocal = new Array('FT 7 x 28','FT 8 x 35');
		var progressive = new Array('Creation','Varilux','Other');


	  

		document.getElementById('bifocal_opt').options.length = '';
		if(id == 'Single Vision')
		{
			for(var i=0;i<singleVision.length;i++)
			{
				document.getElementById('bifocal_opt').options[i] = new Option(singleVision[i],singleVision[i]);
			}
		}
		else if(id == 'Bifocal')
		{
			for(var i=0;i<bifocal.length;i++)
			{
				document.getElementById('bifocal_opt').options[i] = new Option(bifocal[i],bifocal[i]);
			}
		}
		else if(id == 'Trifocal')
		{
			for(var i=0;i<trifocal.length;i++)
			{
				document.getElementById('bifocal_opt').options[i] = new Option(trifocal[i],trifocal[i]);
			}
		}
		else if(id == 'Progressive')
		{
			for(var i=0;i<progressive.length;i++)
			{
				document.getElementById('bifocal_opt').options[i] = new Option(progressive[i],progressive[i]);
			}
		}
	}
	
	function save_form_data(call_from){
		switch(call_from){
			case 'savePost':
				document.order_form.submit();
			break;
			
			case 'save':
				$('#txtpostCharges').val('save');
				document.order_form.submit();
			break;
		}	
	}
	
	function print_pdf(save){
		var order_id = $('#txt_order_save_id').val();
		if(!save){
			window.open(top.JS_WEB_ROOT_PATH+'/interface/optical/pdfFile2.php?order_id='+order_id+'&print=1','print_pdf','');
		}
		else{
			window.open(top.JS_WEB_ROOT_PATH+'/interface/optical/pdfFile2.php?order_id='+order_id+'&print=1','print_pdf','');
		}
	}
	
	function perform_action(call_from){
		if(call_from == 'save'){
			save_form_data('save');
		}else{
			window.location.href = top.JS_WEB_ROOT_PATH+'/interface/optical/index.php?showpage=todays_order_list';
		}
	}
	
	function cancelBtn(val){
		top.fancyConfirm("Do you want save the Order Form?","","top.fmain.perform_action('save')","top.fmain.perform_action('redirect')");
	}
	
$(document).ready(function(){
	$('#vendor_name').typeahead({source:js_php_arr.typeahead});
	var ar = [["txtpostCharges","Save & Post","top.fmain.save_form_data('savePost')"],
			  ["txtsave","Save","top.fmain.save_form_data('save');"],
			  ["print","Print","top.fmain.print_pdf('save');top.fmain.show_loading_image('none');"],
			  ["cancel","Cancel","top.fmain.cancelBtn();"]
			 ];
	top.btn_show("OPTL",ar);
});