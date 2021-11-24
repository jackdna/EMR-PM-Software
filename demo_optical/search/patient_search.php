<?php 
/*
File: patient_search.php
Coded in PHP7
Purpose: Patient Search
Access Type: Include File
*/
?>
<script type="text/javascript">

function vendor_check()
{
	  var msg ='';
	  var searchform = document.search_form;
      var objvsearchform = document.getElementById('txt_for');
	  var srh_val = objvsearchform.value.replace(/\s/g, "");
	  if(srh_val == '')
	  {
		  msg+= "Please Enter the Search Keyword";
		  objvsearchform.focus();
	  }	
	if(msg)
	{
		falert(msg);
		return false;
	}
	else
	{
		searchform.txt_save.value="save";
		document.searchform.submit();
	}
}
	
	function chkNew()
	{	
		var frm = document.search_form;
		frm.submit();
	}
	function searchPatient2(obj){
		var patientdetails = obj.value.split(':');
		if(isNaN(patientdetails[0]) == false){
			document.getElementById("txt_for").value = patientdetails[0];
			document.getElementById("sel_by").value = 'Active';
			document.search_form.submit();
		}
	}

</script>	


<script type="text/javascript">
$(document).ready(function()
{
	$("#txt_for").keypress(function()
	{		
		if (event.keyCode==13)
		{ 
			$("#patient_search").trigger("click");
		} 		
	});
});
</script>
<form name="search_form" action="search/patient_search_result.php" target="main_iframe" onsubmit="return vendor_check()" method="post" style="margin:0px;">

    <input type="hidden" id="patient_session_id" value="<?php echo $_SESSION['patient_session_id']; ?>" />
    <input type="text" name="txt_for" id="txt_for" value="" size="15" />
   <?php
		$searchOption="";
        //---get recent patient for search ----
		$max_recent_search_cache=$GLOBALS["max_recent_search_cache"];
        $inv_user_id = $_SESSION['authId'];
        $qry = imw_query("select patient_id,patientFindBy from recent_users where provider_id = '$inv_user_id' order by enter_date limit 0,$max_recent_search_cache");
        while($qryRes=imw_fetch_array($qry))
        {
            $patient_id = $qryRes['patient_id'];
            $patientFindBy = $qryRes['patientFindBy'];
            $pat_qry = imw_query("select concat(lname,', ',fname) as name , mname from patient_data where id = $patient_id");
            $patientDetails = imw_fetch_array($pat_qry);
            $patient_name = $patientDetails['name'].' '.substr($patientDetails['mname'],0,1);
            $patient_name2 = $patientDetails['name'];
            $searchOption .= '<option value = "'.$patient_id.':'.$patient_name2.':'.$patientFindBy.'">'.ucwords($patient_name).' - '.$patient_id.'</option>';
        }
	?>
    <select name="sel_by" id="sel_by" onChange="searchPatient2(this)" style="width:120px;" onkeypress="if (event.keyCode==13){ return chkNew() }">
        <option value="Active">Active</option>
        <option value="Inactive">Inactive</option>
        <option value="Deceased">Deceased</option>
        <option value="Resp.LN">Resp.LN</option>
        <option value="Ins.Policy">Ins.Policy</option>
		<option value="DOB">DOB</option>
        <?php print $searchOption; ?>
     </select> 
     <input type='hidden' name="btn_sub" id="btn_sub" value='a' />
     <input type="hidden" name="txt_save" id="txt_save" value="" />
     <span class="btn_cls">
        <input type='submit' name="submit_button" value='Search'  /> 
     </span>
</form>