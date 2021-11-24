<?php               
require_once("../../../../config/globals.php");
?>
<script>
    function populateProcLabel(){
		//selVal.join('; '), existing_label
        var strReturn = $("#tempSelectedCache").val();   
        var arrReturn = strReturn.split("~:~");
        var strLen = arrReturn.length;
        strReturn = "";
        for(i = 0; i < strLen-1; i++){
            var arrTemp = arrReturn[i].split("~~~");
            strReturn += arrTemp[1]+"; ";
        } 
        var strLength = parseInt(strReturn.length)-2;
        strReturn = strReturn.substring(0,strLength);
		var strToSendAttrib='';
       if(existing_label)
	   {
		   var arr=existing_label.split('; ');
		   	$.each(arr, function(key, value) {   
				strToSendAttrib += value+"~:~";
				$('#availableOptions')
				.append($("<option></option>")
				.attr("value",value)
				.text(value)); 
			});
		   document.getElementById("tempSelectedCache").value += strToSendAttrib;
		   refreshProcList("custom");
		   
		   document.frm_proc_time.template_label.value=existing_label;
		   existing_label='';//set this variable to null
	   }else{
        document.frm_proc_time.template_label.value=strReturn;
	   }
    }
	
    var selectedList;
    var availableList;

    function createListObjects(){
        availableList = document.getElementById("availableOptions");
        selectedList = document.getElementById("selectedOptions");
    }
         
    function setSize(list1,list2){
        list1.size = getSize(list1);
        list2.size = getSize(list2);
    }

    function selectNone(list1,list2){
        list1.selectedIndex = -1;
        list2.selectedIndex = -1;
        addIndex = -1;
        selIndex = -1;
    }

    function getSize(list){
        var len = list.childNodes.length;
        var nsLen = 0;
        for(i=0; i<len; i++){
            if(list.childNodes.item(i).nodeType==1)
            nsLen++;
        }
        if(nsLen<2)
        return 2;
        else
        return nsLen;
    }     
    
    function refreshProcList(strSelectType,strMode){
		if(strSelectType == "custom"){
            var strAttribs = $("#tempSelectedCache").val();
            var url_dt = "proc_list.php?strSelectType="+strSelectType+"&strAttribs="+strAttribs;                                
        }else{
            var url_dt = "proc_list.php?strSelectType="+strSelectType;
        }   
        $.ajax({
			url:url_dt,
			type:'GET',
			success:function(response){
                var arrResponse = response.split("[{(^)}]");
                if(strSelectType == "available"){
                    $("#divAvailableOptions").html(arrResponse[0]);
                    $("#divSelectedOptions").html(arrResponse[1]);
                    
                }else if(strSelectType == "custom"){
                    $("#divAvailableOptions").html(arrResponse[0]);
                    $("#divSelectedOptions").html(arrResponse[1]);
                    
                    if(arrResponse[2] == 1){
                         document.getElementById('addall').disabled = false;
                         document.getElementById('addsel').disabled = false;
                    }else{
                         document.getElementById('addall').disabled = true;
                         document.getElementById('addsel').disabled = true;
                    }
                    
                    if(arrResponse[3] == 1){    
                         document.getElementById('remall').disabled = false;
                         document.getElementById('remsel').disabled = false;
                    }else{
                         document.getElementById('remall').disabled = true;
                         document.getElementById('remsel').disabled = true;
                    }
        
                }else{
                    $("#divSelectedOptions").html(arrResponse[0]);
                    $("#divAvailableOptions").html(arrResponse[1]);
                    
                    var selectedList = document.getElementById("selectedOptions");
                    var strReturn = "";
                    for(i = 0; i < selectedList.length; i++){               
                        strReturn += selectedList.options.item(i).value+"~:~";
                    }
                    
					$("#tempSelectedCache").val(strReturn);
                }    
                populateProcLabel();
                if(strMode == "lunch"){
                    $("#template_label").val('lunch'); document.getElementById("chkLunch").checked=true; document.getElementById("chkReserved").checked=false;
                }
                
                if(strMode == "Reserved"){
                    $("#template_label").val('Reserved'); document.getElementById("chkLunch").checked=false; document.getElementById("chkReserved").checked=true;
                }         
				top.show_loading_image('hide');
            }
		});
	}
    
    function delAll(strMode){
        $("#tempSelectedCache").val("");
        refreshProcList("available", strMode);
        selectedList.options.length = 0;
        selectNone(selectedList,availableList);
        setSize(selectedList,availableList);
        document.getElementById('addall').disabled = false;
        document.getElementById('addsel').disabled = false;
        document.getElementById('remall').disabled = true;
        document.getElementById('remsel').disabled = true;
    }

    function addAll(){
        $("#tempSelectedCache").val("");
		refreshProcList("selectedOptions");
        availableList.options.length = 0; 
        selectNone(selectedList,availableList);
        setSize(selectedList,availableList);
		document.getElementById('addall').disabled = true;
        document.getElementById('addsel').disabled = true;
        document.getElementById('remall').disabled = false;
        document.getElementById('remsel').disabled = false;
    }
    

    function delAttribute(){
        var strToSendAttrib = ""; 
        var selectedList = document.getElementById("selectedOptions");
        var selIndex = selectedList.selectedIndex;
        if(selIndex < 0){
            alert("Please select some procedure(s) to continue.");
            return;
        }
        var arrRefinedSelection = new Array();
        var j = 0;
        var existingValue = $("#tempSelectedCache").val();
        var arrExistingValue = existingValue.split("~:~");
        
        for(i = 0; i < selectedList.length; i++){
            blRemove = "";
            if(selectedList.options.item(i).selected == true){             
                var blRemove = selectedList.options.item(i).value;
                for(z = 0; z < arrExistingValue.length-1; z++){
                    if(arrExistingValue[z] == selectedList.options.item(i).value){
                        arrExistingValue[z] = "";            
                    }                    
                }                
            }
        }
        
        for(i = 0; i < arrExistingValue.length ; i++){
            if(arrExistingValue[i] != "undefined" && arrExistingValue[i] != "")
                strToSendAttrib += arrExistingValue[i]+"~:~";
        }
        
        if(strToSendAttrib == ""){
            delAll();
        }else{
            $("#tempSelectedCache").val(strToSendAttrib);
            refreshProcList("custom");
        }
    }

    function addAttribute(){
        var strToSendAttrib = "";
        var availableList = document.getElementById("availableOptions");
        var addIndex = availableList.selectedIndex;
        if(addIndex < 0){
            alert("Please select some procedure(s) to continue.");
            return;
        }

        for(i = availableList.length-1; i >= 0 ; i--){
            if(availableList.options.item(i).selected == true){
                strToSendAttrib += availableList.options.item(i).value+"~:~";
            }
        }
		
        document.getElementById("tempSelectedCache").value += strToSendAttrib;
        refreshProcList("custom");
    }
	
	var objFormGl;
	function save_labels_check(objForm)
	{    
		 objFormGl = objForm;	
         var fromHr = objForm.time_mor_from_hour.value;
         var fromMn = objForm.time_mor_from_mins.value;
         var toHr = objForm.time_mor_to_hour.value;
         var toMn = objForm.time_mor_to_mins.value;
         var fromAP = objForm.ap1_mor.value;
         var toAP = objForm.ap2_mor.value;
		 	     
		 var label_type = objForm.label_type.value;
		 var temp_id = objForm.pro_id.value;
		 if(label_type.toLowerCase() == "lunch")
		 {
			post_str = "fromHr="+fromHr+"&fromMn="+fromMn+"&toHr="+toHr+"&toMn="+toMn+"&fromAP="+fromAP+"&toAP="+toAP+"&temp_id="+temp_id; 
			top.show_loading_image('show');
			$.ajax({
				url:'lunch_interval_access.php',
				type:'POST',
				data:post_str,
				complete:function(respData)
				{
					resultData = respData.responseText;
					if(resultData == "no")
					{
						top.show_loading_image('hide');
						alert("There can be only one Lunch interval in a template");
						return false;	
					}
					save_labels(objFormGl);
				}
			});	 
		 }
		 else
		 {
			 save_labels(objForm);
		 }
	}
    
    function save_labels(objForm){
         var fromHr = objForm.time_mor_from_hour.value;
         var fromMn = objForm.time_mor_from_mins.value;
         var toHr = objForm.time_mor_to_hour.value;
         var toMn = objForm.time_mor_to_mins.value;
         var fromAP = objForm.ap1_mor.value;
         var toAP = objForm.ap2_mor.value;  

         if(objForm.template_label.value == ""){
             alert("Please enter a label.");
             return false;
         }else if(fromHr == "" || fromMn == "" || toHr == "" || toMn == ""){
             alert("Please enter Timings.");
             return false;
         }else{
			var thisFromTime = fromHr+":"+fromMn+":"+fromAP;
            var thisToTime = toHr+":"+toMn+":"+toAP;
            var fromTime = document.getElementById("hidTmpFromHr").value+":"+document.getElementById("hidTmpFromMn").value+":"+document.getElementById("hidTmpFromAP").value; 
            var toTime = document.getElementById("hidTmpToHr").value+":"+document.getElementById("hidTmpToMn").value+":"+document.getElementById("hidTmpToAP").value; 
            
            var showfromTime = document.getElementById("hidTmpFromHr").value+":"+document.getElementById("hidTmpFromMn").value+" "+document.getElementById("hidTmpFromAP").value; 
            var showtoTime = document.getElementById("hidTmpToHr").value+":"+document.getElementById("hidTmpToMn").value+" "+document.getElementById("hidTmpToAP").value; 
            
            
            var url_dt = "check_time_range.php?rangeFromTime="+fromTime+"&rangeToTime="+toTime+"&thisFromTime="+thisFromTime+"&thisToTime="+thisToTime;
			$.ajax({
				url:url_dt,
				type:'GET',
				success:function(responseText){
					 var strResponse = responseText;
					 if(strResponse == 1){
                        alert("Entered timings are out of range.\n\nPlease enter within "+showfromTime+" to "+showtoTime);
                       top.show_loading_image('hide');
                        return false;
                    }else{
						top.show_loading_image('show');
						objForm.submit();
                    } 
				}
			});	
        }
    }

    function remove_labels(objForm){
        var fromHr = objForm.time_mor_from_hour.value;
        var fromMn = objForm.time_mor_from_mins.value;
        var toHr = objForm.time_mor_to_hour.value;
        var toMn = objForm.time_mor_to_mins.value;
         
        if(fromHr == "" || fromMn == "" || toHr == "" || toMn == ""){
             alert("Please enter Timings.");
             return false;
        }
		top.show_loading_image('show');
        objForm.doremove.value='remove';
        objForm.submit();
    }
    //ajax div swap by amit - ends here

	function set_reset_options(mode){
		$("#group").prop("disabled", true);
		$("#template_label").prop("readonly", false);
		if(mode == "Lunch"){
			$("#template_label").val("Lunch");
			$("#template_label").prop("readonly", true);
			if($('#show_proc_options').hasClass('show')){
				$('#show_proc_options').removeClass('show');
			}
			$('#show_proc_options').addClass('hide');
		}else if(mode == "Reserved"){
			$("#template_label").val("Reserved");
			if($('#show_proc_options').hasClass('show')){
				$('#show_proc_options').removeClass('show');
			}
			$('#show_proc_options').addClass('hide');
		}else if(mode == "Information"){
			$("#template_label").val("");
			if($('#show_proc_options').hasClass('hied')){
				$('#show_proc_options').removeClass('hide');
			}
			$('#show_proc_options').addClass('show');
		}else if(mode == "Procedure"){
			$("#group").prop("disabled", false);
			$("#template_label").val("");
			if($('#show_proc_options').hasClass('hide')){
				$('#show_proc_options').removeClass('hide');
			}
			$('#show_proc_options').addClass('show');
		}
		$("#group").selectpicker("refresh");
	}
</script>
<form name="frm_proc_time" method="post" action="save_labels.php">
	<input type="hidden" name="temp_parent_id" value="<?php echo $temp_parent_id; ?>" />
    <input type="hidden" name="pro_id" value="<?php echo $pro_id;?>">
    <input type="hidden" name="hidTmpFromHr" id="hidTmpFromHr" value="<?php echo $arrTm_frm_tm[0];?>">
    <input type="hidden" name="hidTmpFromMn" id="hidTmpFromMn" value="<?php echo $arrTm_frm_tm[1];?>">
    <input type="hidden" name="hidTmpToHr" id="hidTmpToHr" value="<?php echo $arrTm_t_tm[0];?>">
    <input type="hidden" name="hidTmpToMn" id="hidTmpToMn" value="<?php echo $arrTm_t_tm[1];?>">
    <input type="hidden" name="hidTmpFromAP" id="hidTmpFromAP" value="<?php echo $arrTm_frm[1];?>">
    <input type="hidden" name="hidTmpToAP" id="hidTmpToAP" value="<?php echo $arrTm_t[1];?>">
	<input type="hidden" name="hidTimeRangeFinalString" id="hidTimeRangeFinalString" value="">
	<input type="hidden" name="doremove" value="">
    <input type="hidden" name="tempSelectedCache" id="tempSelectedCache" value="">
   <div class="row">
   	<div class="col-sm-3">
	   <div class="time_group">
	   <div class="addschtime" id="defaultTimeSelectionTR2" style="display: none"><h2><img src="../../../../library/images/time1.png" width="28" height="28" alt=""> SCHEDULE TIME</h2>	
	   <div style="width: 100%; height: 65px; overflow: auto" id="selTimeStr"></div>
	   </div>
	   <div class="addschtime" id="defaultTimeSelectionTR"><h2><img src="../../../../library/images/time1.png" width="28" height="28" alt=""/> SCHEDULE TIME</h2>	
	   <div class="row">
			<div class="col-sm-3">From</div>
    				<div class="col-sm-9">
    					<div class="row">
							<div class="col-sm-4">
								<select name="time_mor_from_hour" class="selectpicker form-control">                
									<option value="">--</option>                                    
									<?php
									foreach ($tm_array as $tm){                                                
										if ($tm == $arrTm_frm_tm[0]){                                                        
											$chk_sel="selected";
										}                                                
										print "<option value=".$tm." ".$chk_sel.">".$tm."</option>";
										$chk_sel="";
									}
									?>
								</select>
							</div>
							<div class="col-sm-4">
								<select name="time_mor_from_mins" class="selectpicker form-control">    
									<option value="">--</option>                                        
									<?php
									foreach ($tm_min_array as $tmm){
										 if ($tmm == $arrTm_frm_tm[1]){
											$chk_sel_min="selected";
										}
										print "<option value=".$tmm." ".$chk_sel_min.">".$tmm."</option>";
										$chk_sel_min="";
									}
									?>
								</select>
							</div>
							<div class="col-sm-4">
								<select name='ap1_mor' class="selectpicker form-control">                                                    
									<option value="AM" <?php if($arrTm_frm[1]=="AM"){ echo 'selected';}?>>AM</option>
									<option value="PM"  <?php if($arrTm_frm[1]=="PM"){ echo 'selected';}?>>PM</option>
								</select>
							</div>	
						</div>
    				</div>
		  </div>
	   	<div class="clearfix"></div>
	   	<div class="row">
    				<div class="col-sm-3">To</div>
    				<div class="col-sm-9"><div class="row">
						<div class="col-sm-4">
							<select name="time_mor_to_hour" class="selectpicker form-control">
								<option value="">--</option>                                                
								<?php
								foreach ($tm_array as $tm){
									if ($tm == $arrTm_t_tm[0]){                                                        
										$chk_sel="selected";
									}                                                
									print "<option value=$tm $chk_sel>$tm</option>";
									$chk_sel="";
								}
								?>
							</select>
						</div>	
						<div class="col-sm-4">
							<select name="time_mor_to_mins" class="selectpicker form-control">    
								<option value="">--</option>                                            
								<?php
								foreach ($tm_min_array as $tmm){
									if ($tmm == $arrTm_t_tm[1]){
										$chk_sel_min="selected";
									}
									print "<option value=$tmm $chk_sel_min>$tmm</option>";
									$chk_sel_min="";
								}
								?>
							</select>
						</div>		
						<div class="col-sm-4">
							<select name='ap2_mor' class="selectpicker form-control">
								<option value="AM" <?php if($arrTm_t[1]=="AM"){ echo 'selected';}?>>AM</option>
								<option value="PM"  <?php if($arrTm_t[1]=="PM"){ echo 'selected';}?>>PM</option>
							</select>
						</div>		
					</div>
					</div>
				</div></div>
	   	<div class="clearfix"></div>
	   	<div class="info_group">
    			<div class="row">
    				<div class="col-sm-12"><h2><img src="../../../../library/images/cald.png" width="28" height="28" alt=""/> SCHEDULE INFO.</h2></div>
    			</div>
    			<div class="row">
    				<div class="col-sm-12">
    					<label>Label Type</label>
						<select id="label_type" class="selectpicker form-control" name="label_type" onchange="set_reset_options(value);">
							<option value="Information">Informative</option>
							<option value="Lunch">Lunch</option>
							<option value="Reserved">Reserved</option>
							<option value="Procedure">Mandatory</option>
						</select>
    				</div>
    			</div>
    			<div class="row">
    				<div class="col-sm-12">
    					<label>Label</label>
						<input type="text" name="template_label" id="template_label" class="form-control" value="" />
    				</div>
    			</div>
    			<div class="row">
    				<div class="col-sm-12">
    					<label>Color</label>
    					<input type="text" class="grid_color_picker" name="label_color" id="label_color" value="">
    				</div>
    			</div>
		 </div>	
	   	<div class="clearfix"></div>
	   	   	
	   	
	   </div>
	   
	   
	   
	   </div>
   	<div class="col-sm-9">
		<div class="row">
			<div class="show_proc_options_parent">
				<div id="show_proc_options">
					<h4>Please Add/Remove Procedure(s) using Arrow Buttons.</h4>	
					<div class="pdr5"><div class="row text-center">
									<div class="col-xs-5">
										<div class="lstofsche"><h2>List of All Procedures</h2>
										<div id="divAvailableOptions">
											<select disabled size="6" id="availableOptions" name="availableOptions" class="selectpicker form-control" multiple data-width="100%">
												<option value=""></option>
											</select> 
										</div>	</div>				
									</div>
									<div class="col-xs-2">
										<div class="row schebutpos">
											<div class="col-xs-12" style="margin-bottom: 10px">
												<select name="group" id="group" class="selectpicker" data-width="55%" disabled='disabled'>
													<option value="0">Split Labels</option>
													<option value="1">Group Label</option>
												</select>
											</div>
											<div class="col-xs-12">
												<input name="addall" id="addall" type="button" value="&gt;&gt;" onclick="addAll();" class="alllist" />
											</div>
											<div class="col-xs-12">
												<input type="button" id="addsel" name="addsel" class="snglist" value="&nbsp;&gt;&nbsp;" onclick="addAttribute()" />
											</div>
											<div class="col-xs-12">
												<input type="button" id="remsel" name="remsel" class="snglist" value="&nbsp;&lt;&nbsp;" onclick="delAttribute()" />
											</div>
											<div class="col-xs-12">
												<input type="button" id="remall" name="remall" class="alllist" value="&lt;&lt;" onclick="delAll()" />
											</div>	
										</div>	
									</div>
									<div class="col-xs-5">
										<div class="lstofsche"><h2>List of Selected Procedures</h2>	
										<div id="divSelectedOptions" >
											<select size="6" id="selectedOptions" name="selectedOptions" class="selectpicker form-control" multiple data-width="100%">    
												<option value=""></option>
											</select> 
										</div>	</div>
									</div>	
								</div>	</div>
				</div>
			</div>
			<div class="text-center">
				<input type="button" name="btnRemoveLabels" value=" Remove " onclick="javascript:remove_labels(this.form);" class="btn btn-success" id="btnRemoveLabels" />	
				<input type="button" name="save" value=" Save " onclick="javascript:save_labels_check(this.form);" class="btn btn-success" id="Submit" />	
				<input type="button"  class="btn btn-danger" name="cancelMe" value=" Cancel " onclick="hideProviderProcDiv();" id="cancelMe"  />
			</div>	
		</div>
	   </div>
   </div>
</form>
<script language="javascript">
    createListObjects();
    delAll();
	var mode=$("#label_type").val();
	if(mode == "Lunch"){
		$("#template_label").prop("readonly", true);
	}else{
		$("#template_label").prop("readonly", false);
	}
	//$('.bfh-colorpicker').colorpicker('setValue','transparent');
</script>