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
require_once("../admin_header.php");
$rsQryInsert = "";
$createdBy = $_SESSION['authId'];
function just_clean($string){
 // Replace other special chars  
$specialCharacters = array('#' => '','$' => '','%' => '','&' => '','@' => '','.' => '',
 							'+' => '','=' => '','\\' => '','/' => '',' ' => '');
 
$cleanString = "";
for($strCounter=0;$strCounter<strlen($string);$strCounter++){
	foreach($specialCharacters as $key => $value){
		if(substr($string,$strCounter,1) == $key){
			$string = str_replace($key,$value,$string); 
		}
	}	
}

$string = preg_replace('/[^a-zA-Z0-9\-]/', '', $string);  
$string = preg_replace('/^[\-]+/', '', $string);  
$string = preg_replace('/[\-]+$/', '', $string);  
$string = preg_replace('/[\-]{2,}/', '', $string);
return $string;  
} 

if($_REQUEST['save_frm']!=""){
	
	$custCategory = $_REQUEST['custCategory'];
	$custSubCategory = $_REQUEST['custSubCategory'];

	$subModule = (isset($_REQUEST['sub_module']) && empty($_REQUEST['sub_module']) == false) ? str_replace('\'','',$_REQUEST['sub_module']) : '';
	
	$txtSectionName = $_REQUEST['txtSectionName'];
	if(!$txtSectionName){
		$txtSectionName = "Custom Fields";
	}
	$arrControlLable = $arrDefaultValue = $arrFieldType = $arrspecialityValue = array();
	$arrControlLable = $_REQUEST['controlLable']; 
	$arrDefaultValue = $_REQUEST['defaultValue'];
	$arrFieldType = $_REQUEST['hidFieldType'];
	if(isset($_REQUEST['specialityValueHidd']))
	$arrSpecValuHidd = $_REQUEST['specialityValueHidd'];
	//echo '<pre>';
	//print_r($arrFieldType);
	$arrHidId = $_REQUEST['hidId'];
	$arrHidDelete = $_REQUEST['hidDelete'];
	$arrHidDelete = substr(trim($arrHidDelete), 0, -1);
	$arrHidDelete = explode('-',$arrHidDelete);
	$customSubCategory = "";

	//foreach((array)$custSubCategory as $custSubCategoryKey => $custSubCategoryValue){ 
		//$customSubCategory .= "'".$custSubCategoryValue."',";
		foreach((array)$arrControlLable as $Key => $Value){  			
			$controlName = just_clean($Value);	
				$qryInsert = '';
			$intCustomId = 0;
			if(!$arrHidId[$Key]){		
				$getMaxCustomId = "select max(id) as maxCustomId from custom_fields";		
				$rsMaxCustomId = imw_query($getMaxCustomId);
				if($rsMaxCustomId){
					$rowMaxCustomId = imw_fetch_array($rsMaxCustomId);
					$intCustomId = $rowMaxCustomId['maxCustomId']; 
					$intCustomId = $intCustomId+1;	
				}	
				$qryInsert = "insert into custom_fields set ";				
				$qryInsert .= "control_name = '".imw_real_escape_string($controlName.$intCustomId)."',";
				$qryInsert .= "created_by = '$createdBy',";
				$qryInsert .= "created_date_time = now(),";
				
			}
			elseif($arrHidId[$Key]){
				$qryInsert = "update custom_fields set ";
				$qryInsert .= "modified_by = '$createdBy',";
				$qryInsert .= "modified_date_time = now(),";
				//$queryUpdate = " where id = '".$arrHidId[$Key]."' and sub_module = '".$custSubCategoryValue."'";
				$queryUpdate = " where id = '".$arrHidId[$Key]."' and sub_module = '".$subModule."' ";
			}
			
			$qryInsert .= "control_lable = '".imw_real_escape_string($Value)."',";
			$qryInsert .= "module = '$custCategory',";
			
			//$qryInsert .= "sub_module = '$custSubCategoryValue',";
			$qryInsert .= "sub_module = '".$subModule."',";
			//if(isset($_REQUEST['specialityValueHidd'])){
			if(isset($_REQUEST['specialityValue'.$Key])){
				//$arrSpecialityValue = serialize(explode(",",$arrSpecValuHidd[$Key]));
                $arrSpecialityValue = serialize($_REQUEST['specialityValue'.$Key]);
				$qryInsert .= "spl_id = '".$arrSpecialityValue."',";
            }
			$qryInsert .= "module_section = '$txtSectionName',";						
			$qryInsert .= "control_type = '$arrFieldType[$Key]',";
			if($arrFieldType[$Key] == "checkbox"){
				$cbk = 'cbkDefault_'.$Key;
				$cbk = $_REQUEST[$cbk];
				//echo $key;
				if($cbk == "1"){
					$qryInsert .= "cbk_default_select  = '1',";
				}
				else{
					$qryInsert .= "cbk_default_select  = '0',";
				}
			}
			$qryInsert .= "default_value = '".imw_real_escape_string($arrDefaultValue[$Key])."'";
					
			if($arrHidId[$Key]){
				$qryInsert .= $queryUpdate;			
			}
			//echo $qryInsert."<br>".$Key."<br>";
			$rsQryInsert = imw_query($qryInsert) or imw_error();
		}
	//}
	foreach($arrHidDelete as $Key => $Value){  
		$qryDelete = "update custom_fields set status = '1' where id=$Value";
		$rsDelete = imw_query($qryDelete);
	}
	if($_REQUEST['custCategory']!="" && $_REQUEST['custSubCategory']!=""){
		$_REQUEST['module'] = $_REQUEST['custCategory'];
		
		foreach((array)$custSubCategory as $custSubCategoryKey => $custSubCategoryValue)
		$customSubCategory .= "'".$custSubCategoryValue."',";
		$customSubCategory = substr(trim($customSubCategory), 0, -1);
		$_REQUEST['sub_module'] = $customSubCategory;			
	}
	/*if($rsQryInsert){
		echo "<script>alert('Record has been saved successfully');</script>";
	}*/	
	
}

$sql = "SELECT * FROM admn_speciality WHERE status='0' ORDER BY name ";
$rez= imw_query($sql);
$arrSpeciality = array();
for($i=0;$row=imw_fetch_array($rez);$i++)
{
$arrSpeciality[$row["id"]] = $row["name"];
}
function html_select_speciality($ids,$arrSpeciality,$count){
	
$html = ""; 
$html .= "<select name='specialityValue".$count."[]' data-count=\"".$count."\" class='selectpicker' multiple data-width=\"100%\" id='specialityValue".$count."' >";
foreach($arrSpeciality as $key=>$val){
$selected = (in_array($key,$ids))?"selected":"";
$html .= "<option value=$key $selected>$val</option>";
}
$html .= '</select>';
$html .= '<input type=hidden name="specialityValueHidd[]" id="specialityValueHidd'.$count.'">';
return $html;			
}
$_REQUEST['sub_module'] = (isset($_REQUEST['sub_module']) && empty($_REQUEST['sub_module']) == false) ? str_replace('\'','',$_REQUEST['sub_module']) : '';
if($_REQUEST['module']!="" && $_REQUEST['sub_module']!=""){
	$strModule = $_REQUEST['module'];
	$strSubModule = $_REQUEST['sub_module'];
	$querySelectPerticuler = "select * from custom_fields where module = '$strModule' and sub_module IN (\"".$strSubModule."\") and status = '0' order by sub_module";
	
	$rsSelectPerticuler = imw_query($querySelectPerticuler) or die($querySelectPerticuler.imw_error());
	$counter = 0;
	$tableData = "";
	$strCustSec = "";
	while($row = imw_fetch_assoc($rsSelectPerticuler)){
		$hidCbk = $checked = "";
		if($row['control_type'] == "text"){
			$hidCbk = "<input type=\"hidden\" name=\"hidFieldType[]\"  id=\"hidFieldType$counter\" value = \"text\">";
		}
		elseif($row['control_type'] == "checkbox"){			
			$checked = ($row['cbk_default_select'] == 1) ? "checked" : "";
			$hidCbk = "<input type='hidden' name='hidFieldType[]' id=\"hidFieldType$counter\" value ='checkbox'><div class=\"checkbox\"><input type='checkbox' name=\"cbkDefault_$counter\" id=\"cbkDefault_$counter\" value = '1' $checked onClick=\"javascript: selDeSelAllChkBox(this);\"><label for=\"cbkDefault_$counter\">Default</label></div>";  
		}
		$tableData .= "<tr>
							<input type=\"hidden\" name=\"hidId[]\" id=\"hidId$counter\" value =\"".$row['id']."\">
							<td nowrap >".$row['sub_module']."</td>
							<td><input type=\"text\" name=\"controlLable[]\" id=\"controlLable$counter\" class=\"form-control\" value =\"".$row['control_lable']."\"></td>
							<td><input type=\"text\" name=\"defaultValue[]\" id=\"txtdefaultValue$counter\" class=\"form-control\" value =\"".$row['default_value']."\"></td>
							<td>".$hidCbk."</td>";
				if($row['spl_id']!="")			
				$tableData .="<td>".html_select_speciality(unserialize($row['spl_id']),$arrSpeciality,$counter)." </td>";
				$tableData .= " <td>
							<input type='hidden' name='subCatValue[]' value = '".$row['sub_module']."' >
							<input type=\"button\" width=\"auto\" name=\"del_catogery\" id=\"del_catogery$counter\" value =\"Delete\" class=\"btn btn-danger\" onclick='removeRow(this,\"".$row['id']."\");' /></td>
					</tr>
					<script>
						$('#specialityValueHidd".$counter."').val($('#specialityValue".$counter."').val());
					</script>";													
		$counter++;
		if(!$strCustSec)
		$strCustSec = $row['module_section'];
	}
}
?>
<script type="text/javascript">
	$(document).ready( function() {
		$('.selectpicker').selectpicker();
		$('body').on('change','select[id^="specialityValue"]',function(){
            var c = $(this).data('count');
            var h = 'specialityValueHidd'+c;
            $("#"+h).val($(this).val());
        });
	});
	var ar = [["custom_field_new","New","top.fmain.newForm();"],
			  ["custom_field_save","Save","top.fmain.chkForm();"]	
			  ];
	top.btn_show("ADMN",ar);
	//Btn --

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
				td.innerHTML = "<input type='hidden' name='subCatValue[]' id='subCatValue"+count+"' value = '"+subCatVal+"' ><input type='button' name='del_catogery[]' id='del_catogery"+count+"' value =\"Delete\" class=\"btn btn-danger\" onclick='removeRow(this);'/>";				
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
			<?php foreach($arrSpeciality as $key=>$val){?>
					
					selected = ($.inArray('<?php echo $key?>',selectedArr)!= -1)?"selected":"";
					html += "<option value='<?php echo $key?>' " +selected+"><?php echo $val?></option>";
			<?php }?>
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
						objValue = objValue + "," + obj[i].value;
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
	
</script>
</head>
<body>
<form name="customization" method="post" onSubmit="return chkForm();">
<input type="hidden" name="module" id="module" value="<?php echo  $_REQUEST['module'];?>" />
<input type="hidden" name="sub_module" id="sub_module" value="<?php if($_REQUEST['sub_module']){echo $_REQUEST['sub_module'];} ?>"/>
<input type="hidden" name="hidDelete" id="hidDelete" />
<input type="hidden" name="hidExitsRow" id="hidExitsRow" value="<?php echo $counter; ?>" />
<input type="hidden" name="save_frm" id="save_frm" value="">
	<div class="whtbox">
		<div class="row">
			<div class="col-sm-4">
				<div class="newtemplate">
					<div class="row plr10" id="newTemplate">
						<div class="col-sm-6">
							<div class="form-group" id="tdCustCategory">
								<label for="custCategory">Customization category</label><br />
								<select class="selectpicker" data-width="100%" id="custCategory" name="custCategory" onChange="getCustSubCategory(this.value,'',true);">
									<option value="">Please Select</option>
									<option value="Patient_Info" <?php if($_REQUEST['module'] == "Patient_Info"){ echo "selected";} ?>>Patient Info</option>	
									<option value="Med_Hx" <?php if($_REQUEST['module'] == "Med_Hx"){ echo "selected";} ?>>Med Hx</option>
								</select>	
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label for="custSubCategory">Customization sub category</label>
								<span id="tdCustSubCategory">
								<select class="form-control" data-width="100%" id="custSubCategory" name="custSubCategory[]" multiple size="2" >
									<option value="">Please Select</option>																	
								</select>
								</span>
							</div>
						</div>
						<div class="col-sm-12">
							<div class="form-group text-center">
								<input type="button" name="getField" id="getField" value="Get Field" class="btn btn-success" onClick="getcustomField(document.getElementById('custCategory').value,document.getElementById('custSubCategory'));"/>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-8 " >
				<div class="adminfild">
					<div class="row">
						<div class="col-sm-3">
							<label>Field Label</label><br />
							<input type="text" class="form-control" name="controlLabel" id="controlLabel" onKeyUp="callme(this);"/>
						</div>
						<div class="col-sm-3">
							<label>Field Type</label><br />
							<select name="fieldType" id="fieldType" class="selectpicker" data-width="100%" onChange="setVal(this);">
								<option value="text_box">Text Box</option>
								<option value="check_box">Check Box</option>
							</select>
						</div>
						<div class="col-sm-3">
							<label id="tdval">Default Value</label><br />
							<input type="text" class="form-control" name="defaultValue" id="defaultValue"/>
						</div>
						<div class="col-sm-3" id="tdsplCategory">
							<label>Specialty</label><br />
							<div id="EnableOpt0">
								<select name="splCategory" id="splCategory" class="selectpicker" data-width="100%" size="1" multiple >
								<?php														
									foreach($arrSpeciality as $key=>$val){							
										$selCat="";
										if($splCategory==$key){
											$selCat ="selected";
										}
										print "<option value='".$key."' $selCat>".$val."</option>";
									}
								?>
								</select>
							</div>
						</div>
						<div class="col-sm-12 text-center pt10">
							<input type="button" name="btAdd" id="btAdd" value="Add To List" class="btn btn-success" onClick="addNewRow(document.getElementById('controlLabel').value,document.getElementById('fieldType').value,document.getElementById('defaultValue').value);" />
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div><br /><br />
		<div class="table-responsive respotable adminnw">
			<table class="table table-bordered table-hover">
				<tbody id="custFields">
					<?php echo $tableData;?>	
				</tbody>
			</table>
		</div>
	</div>
</form>
<script type="text/javascript">
	<?php 
	
	if($rsQryInsert){?>	
		top.alert_notification_show('Record has been saved successfully');
	<?php	
	}?>
	function show2(){
		if (!document.all&&!document.getElementById)
		return
	}
	show2();
	<?php if($_SERVER['REQUEST_METHOD'] == "POST"){?>	
	module = "<?php echo  $_REQUEST['custCategory']?>";
	subModule = "<?php echo  implode(",",$_REQUEST['custSubCategory']); ?>";
	<?php }else{?>
	module = "Patient_Info";
	subModule =  "Demographics";
	<?php }?>
	
	if(document.getElementById('custCategory')){					
		getCustSubCategory(module,subModule,false);																	
	}
	show_loading_image('none');
	set_header_title('Custom Fields');
	</script>    
<?php
	require_once("../admin_footer.php");
?>