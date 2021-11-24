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
require_once(dirname(__FILE__).'/../../config/globals.php');
$dataArr = array('sel_date' => $sel_date, 'facility_id' => $facility_id);
$query_str = http_build_query($dataArr);
?>
<html>
	<head>
		<title>imwemr :: eRx Registration</title>
		<link href="<?php echo $GLOBALS['webroot']; ?>/library/css/bootstrap.css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet">
		<link href="<?php echo $GLOBALS['webroot'];?>/library/messi/messi.css" type="text/css" rel="stylesheet">
        
        
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/jquery.min.1.12.4.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/messi/messi.js"></script>
        <script type="text/javascript" src="<?php echo $GLOBALS['webroot']?>/library/js/common.js"></script>
	</head>
	<body>
		<div id="divAjaxLoader" style="position:absolute;z-index:1000;width:250px; top:50;left:10; height:30px; display:block;">				
			<img src="<?php echo $GLOBALS['webroot'];?>/library/images/loading_image.gif" align="absmiddle">							
		</div>
		<table width="100%">
			<tr>
				<td id="pat_reg" style="text-align:center;" class="text_10b">Patient eRx registration in process</td>
			</tr>
			<tr>
				<td>
					<iframe src="patient_erx_registration.php?<?php print $query_str; ?>" width="100%" height="280px" scrolling="yes" frameborder="0"></iframe>
				</td>
			</tr>
		</table>
	</body>
</html>
