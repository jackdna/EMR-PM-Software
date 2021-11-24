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

File: prescription.php
Purpose: This file provides Prescription section in work view.
Access Type : Direct
*/
//============GLOBAL FILE INCLUSION=========================
require_once(dirname(__FILE__).'/../../config/globals.php');

$pid = $_SESSION['patient'];

?>
<html>
<head>
<title>Allergies</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--------------BOOTSTRAP & COMMON CSS FILE------------->
<link href="<?php echo $library_path; ?>../../library/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>../../library/css/common.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" rightmargin="0" topmargin="0" style="background-color:#fff!important">
<div class="table-responsive">
	<table class="table table-striped table-bordered">
		<tr class="grythead">
			<td height="18">&nbsp;Name</td>
			<td>&nbsp;Start Date</td>
			<td>&nbsp;End Date</td>
		</tr>
		<?php
		//=======GET ALLERGIES FROM LIST TABLE==============
		$checkData="SELECT 
						*
					FROM 
						`lists`
					WHERE
						pid=$pid 
					AND 
						type in (3,7)
					";
		$checkSql = imw_query($checkData);
		$checkrows=imw_num_rows($checkSql);
		while($checkl=imw_fetch_array($checkSql))
		{ 
			$name=$checkl['title'];  // ALLERGIES NAME
			$begdate=(!empty($checkl['begdate']) && ($checkl['begdate'] != "0000-00-00")) ? get_date_format($checkl['begdate']) : "";
			$enddate=(!empty($checkl['enddate']) && ($checkl['enddate'] != "0000-00-00")) ? get_date_format($checkl['enddate']) : "";
		
		?>
		<tr class="txt_10">
			<td  height="18">&nbsp;<a href="#" class="txt_10"><?php echo $name;?></a></td>
			<td>&nbsp;<?php echo $begdate;?></td>
			<td>&nbsp;<?php echo $enddate;?></td>
		<tr>
		<?php
		}
		?>
	</table>
</div>
</body>
</html>