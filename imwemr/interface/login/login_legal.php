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
$ignoreAuth = true;
require_once("../../config/globals.php");
$_REQUEST['pg']=(xss_rem($_REQUEST['pg']));
$_REQUEST['defaultProduct']=(xss_rem($_REQUEST['defaultProduct']));
$_REQUEST['doc']=(xss_rem($_REQUEST['doc']));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title><?php echo $_REQUEST['defaultProduct'].'&nbsp;-&nbsp;'. $_REQUEST['doc']; ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="imagetoolbar" content="no" />
	<!-- Bootstrap -->
	<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" rel="stylesheet">
	<link rel="stylesheet"
	type="text/css" href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.min.css" />
</head>
<body>
	<?php $mainHeading="";
		  if($_REQUEST['doc']=="privacy"){
			  $mainHeading="Privacy Statement";
		  }else if($_REQUEST['doc']=="copyright"){
			  $mainHeading="Copyright Notice";
		  }else if($_REQUEST['doc']=="softLic"){	
			  $mainHeading="Software License Agreement";
		  }
	?>
	<div class="purple_bar text"><?php echo $mainHeading; ?></div>
	<div style="overflow-x:hidden; overflow:auto;" class="m10">
		<?php if($_REQUEST['doc']=="privacy"){  ?>
		<p style="padding-left:5px;padding-top:5px;" >lorem ipusum some more lines goes here.</p>
		<?php }else if($_REQUEST['doc']=="copyright"){ ?>
		<p style="padding-left:5px; padding-top:5px;" ><b>&copy; Copyright under MIT License-<?php echo date("Y");?>, IMWEMR OpenSource Software.</b></p>
		
		<p style="padding-left:5px;">lorem ipusum some more lines goes here.</p>
		
		<p style="padding-left:5px; padding-top:5px;" >All Right Under MIT License.</p>
		
		<p style="padding-left:5px;"><b>Trademarks</b><br>lorem ipusum some more lines goes here.</p>
		
		<p style="padding-left:5px;"><b>Submit Ideas to this Software</b><br>lorem ipusum some more lines goes here.</p>
		
		<p style="padding-left:5px;"><b>Terms of Idea Submission</b><br>You agree that: lorem ipusum some more lines goes here.</p>
		<?php }else if($_REQUEST['doc']=="softLic"){?>
			<div style="height:550px; overflow-x:hidden; overflow:auto; margin-top:10px;">
				<p>
			<table width="90%" align="center" cellpadding="0" cellspacing="0" border="0" >
			<tr><th colspan="2">1. General</th></tr>
			<tr><th width="30px">1.1</th><td width="auto">lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>1.2</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>1.3</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><td colspan="2" height="20px"></td></tr>
			<tr><th colspan="2">2. Use of Content</th></tr>
			<tr><th>2.1</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>2.2</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>2.3</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>2.4</th><td><b>lorem ipusum some more lines goes here.</b></td></tr>
			
			<tr><td colspan="2" height="20px"></td></tr>
			<tr><th colspan="2">3. Indemnity</th></tr>
			<tr><th>3.1</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><td colspan="2" height="20px"></td></tr>
			<tr><th colspan="2">4. DISCLAIMER OF WARRANTIES; LIMITATION OF LIABILITY</th></tr>
			<tr><th>4.1</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>4.2</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>4.3</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><th>4.4</th><td>lorem ipusum some more lines goes here.</td></tr>
			
			<tr><td colspan="2" height="20px"></td></tr>
			<tr><th colspan="2">5. Miscellaneous</th></tr>
			<tr><th>5.1</th><td>lorem ipusum some more lines goes here.</td></tr>
			</table>
				</p>
			</div> 
		 <?php } ?>
		<br><br>
		<center><input type="button" value="Close" class="btn btn-success btn-sm" onClick="javascript:window.close();"></center><br><br><br>
	</div>
</body>
</html>
<script language="javascript" type="text/javascript" src="../../library/js/bootstrap.min.js">