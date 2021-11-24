<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php 
require_once('conDb.php'); 

$qry_medication_admin = "Select * From medications  Order By `name`";
$res_medication_admin = imw_query($qry_medication_admin) or die(imw_error());
$totalRows_medication_admin = imw_num_rows($res_medication_admin);
?>
<script>
	
function getInnerHTML_medicationAdmin(medication){
	
	var Container	=	document.getElementById('selected_frame_name_id').value;
	var inputObj	=	document.getElementsByName(Container+'Med[]');
	for(var i = 0; i < inputObj.length; i++)
	{
		if(inputObj[i].value == '')
		{
			inputObj[i].value = medication;
			return;
		}
	}
}

var tOutAdminMedication; 
function closeAdminmedication(){
	var HiddObj	=	top.frames[0].frames[0].document.getElementById("hiddPreDefineId")
	var PopObj	=	top.frames[0].frames[0].document.getElementById('medicationPopupAdmin');
	if(!HiddObj){
		HiddObj	=	top.frames[0].frames[0].frames[0].document.getElementById("hiddPreDefineId") ;
	}
	
	if(!PopObj){
		PopObj	=	top.frames[0].frames[0].frames[0].document.getElementById('medicationPopupAdmin'); 
	}
	
	if(HiddObj.value == "preDefineOpenYes")
	{
		if(PopObj.style.display == "block"){ 
			PopObj.style.display = "none";
		}
	}

}

function closeAdminPopup(){
	tOutAdminMedication = setTimeout("closeAdminmedication()", 500);
}
function stopClosePopupAdmin() {
	clearTimeout(tOutAdminMedication);
}

</script>

<div id="medicationPopupAdmin" onMouseOver="stopClosePopupAdmin();" onMouseOut="closeAdminPopup();" class="col-md-4 col-lg-3 col-xs-4 col-sm-4 padding_0 adminPopUp"  > 
	
  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_6 popup-header" >
  	Medication
    <span class="popup-close" onClick="document.getElementById('medicationPopupAdmin').style.display='none';" style="">X</span>
  </div>
  
  <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_0 popup-body">
  <?php
		$counter = 0;
		while($res_medication_row = imw_fetch_array($res_medication_admin))
		{
			$counter++;
	?>
  		<div class="col-md-12 col-lg-12 col-xs-12 col-sm-12 padding_6 hoverdiv" style="padding:5px; border-bottom:1px solid #CCC;" onClick="return getInnerHTML_medicationAdmin('<?php echo stripslashes($res_medication_row['name']); ?>')"><?php echo stripslashes($res_medication_row['name']); ?></div>
	<?php
		}
	?>
  </div>
</div>