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
?>
<?php	
/*	
File: consent_form_details_ipad.php
Purpose: Get consent form details for iPad
Access Type: Include 
*/
?>
<!DOCTYPE HTML>
<html>
<head>
<title>Consent Form Detail</title>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<?php
$fopen_file=true;
include("consentFormDetails.php");

?><br><br>
<center>
<input type="button" class="dff_button" value="Save" onclick="save_form('save_form');">&nbsp;<input class="dff_button" type="button" value="Save & Print" onclick="save_form('save_form','','print_form');">&nbsp;<input class="dff_button" type="button" value="On Hold For:" onclick="$('#hold_to_phy_div').show();">&nbsp;<input class="dff_button" type="button" value="Close" onclick="window.close();">
</center>
<br>
<script type="text/javascript">
$(document).ready(function(e) {
	window.focus();
	$('#hold_to_phy_div .hold').click(function(){
		if($('#hold_to_physician').val()==''){
			alert('Please select a physician');
		}else{
			$('#hidd_hold_to_physician').val($('#hold_to_physician').val());
			save_form('save_form');
			$('#hold_to_phy_div').hide();
		}
	});
});
</script>
</html>