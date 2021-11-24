<?php
//---- File For Add The Zip Code -------
require_once('functions.inc.php');
$objDataManage = new DataManage;
if($city){
	$insertId = $objDataManage->AddRecords($_POST,'zip_codes');	
}
?>
<html>
<head>
<title>imwemr</title>

<script type="text/javascript">
	function fillvalue(){
		<?php
			if($insertId){
		?>
			var zip = '<?php print $zip_code; ?>';
			var city = '<?php print $city; ?>';
			var state = '<?php print $state_abb; ?>';
			opener.zipCodes(zip,city,state);
			window.close();
		<?php
			}		
		?>
		document.zipCodeFrm.zip_code.value = "<?php print $code; ?>"
	}
	function submitForm(){
		var form = document.zipCodeFrm;
		var msg = '';
		if(form.zip_code.value == ""){
			msg = 'Please enter Zip Code.\n';
		}
		if(form.city.value == ""){
			msg += 'Please enter city.\n';
		}
		if(form.state.value == ""){
			msg += 'Please enter state.\n';
		}
		if(form.state_abb.value == ""){
			msg += 'Please enter state_abb.';
		}
		if(msg){
			alert(msg);
		}
		else{
			document.zipCodeFrm.submit();
		}
	}
	function resetForm(){
		window.close();
	}
</script>
</head>
<body topmargin="0" onLoad="fillvalue();" leftmargin="0" rightmargin="0">
<table width="100%" align="center" cellpadding="0" cellspacing="0" border="0">
	<tr height="15">
		<td bgcolor="#FEDC8B" align="center" class="text_10b">Warning Zip code Not Recognized</td>
	</tr>
	<tr height="15"><td></td></tr>
	<tr>
		<td width="100%" align="center">
			<form name="zipCodeFrm" action="" method="post">
			<table width="100%" align="center" border="0" bgcolor="#FFF3E8">
				<tr>
					<td class="text_10b" bgcolor="#FFFFFF" align="right">Zip Code:</td>
					<td align="left">
						<input type="text" name="zip_code" onKeyPress="{if (event.keyCode==13)submitForm()}"  class="text_10" size="20" value="" />
					</td>
					<td class="text_10b" width="40" bgcolor="#FFFFFF" align="right">City:</td>
					<td align="left">
						<input type="text" name="city" onKeyPress="{if (event.keyCode==13)submitForm()}" class="text_10" size="20" value="" />
					</td>
					<td class="text_10b" bgcolor="#FFFFFF" align="right">State:</td>
					<td align="left">
						<input type="text" name="state" onKeyPress="{if (event.keyCode==13)submitForm()}" class="text_10" size="20" value="" />
					</td>
					<td class="text_10b" bgcolor="#FFFFFF" align="right">State_Abb:</td>
					<td align="left">
						<input type="text" name="state_abb" onKeyPress="{if (event.keyCode==13)submitForm()}" class="text_10" size="20" value="" />
					</td>
				</tr>
				<tr height="8"><td colspan="8" bgcolor="#FFFFFF"></td></tr>
				<tr>
					<td bgcolor="#FFFFFF" align="right" colspan="4">
						<input type="button" class="button" name="txtAdd" value="Add" onClick="submitForm();">
					</td>
					<td bgcolor="#FFFFFF" align="left" colspan="4">
						<input type="button"  class="button" name="cancel" value="Cancel" onClick="resetForm();">
					</td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
</body>
</html>