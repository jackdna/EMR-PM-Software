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
Access Type : Include
*/
//============GLOBAL FILE INCLUSION=========================
require_once(dirname(__FILE__).'/../../config/globals.php');

$pid = $_SESSION['patient'];

//============OTHERS FILE INCLUSION=========================
include_once(dirname(__FILE__)."/../../library/classes/work_view/ChartAP.php");
include_once(dirname(__FILE__)."/../../library/classes/work_view/CcHx.php");	
require_once(dirname(__FILE__)."/../../library/classes/work_view/CcHxPrint.php");		

$CcHxPrint = new CcHxPrint($pid,'');
?>
<html>
<head>
<title>Medication</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--------------BOOTSTRAP & COMMON CSS FILE------------->
<link href="<?php echo $library_path; ?>../../library/css/bootstrap.css" rel="stylesheet" type="text/css">
<link href="<?php echo $library_path; ?>../../library/css/common.css" rel="stylesheet" type="text/css">
</head>
<body leftmargin="0" rightmargin="0" topmargin="0" class="scrol_Vblue_color" style="background-color:#fff!important">
<div>
	<table class="table table-striped table-bordered" >
		<tr class="grythead">
			<td class="white_color" height="18">&nbsp;Name</td>
			<td  class="white_color">&nbsp;Start Date</td>
			<td class="white_color">&nbsp;End Date</td>
		</tr>
		<?php
		$arr_tmp_chk_meds = array();
		//======GET MEDICATIONS FROM LISTS TABLE
		$checkData="SELECT 
						*
					FROM 
						`lists` 
					WHERE
						pid=$pid 
					AND
						(type='1' or type='4') 
					AND 
						allergy_status!='Deleted'
					";
		$checkSql = imw_query($checkData);
		$checkrows=imw_num_rows($checkSql);
		while($checkl=imw_fetch_array($checkSql))
		{
			$name=$checkl['title'];  //MEDICATIONS NAME 
			$ocular_sites=$checkl['sites']; // MEDICATION SITES => EYES

			$ocular_sites_str_1 = "";
			
			if($ocular_sites=='1'){ $ocular_sites_str_1 = '(OS)'; } 
			else if($ocular_sites=='2'){ $ocular_sites_str_1 = '(OD)'; }
			else if($ocular_sites=='3'){ $ocular_sites_str_1 = '(OU)'; }
			else if($ocular_sites=='4'){$ocular_sites_str_1 = '(PO)'; }	
			
			$arr_tmp_chk_meds[] = $name." ".$ocular_sites_str_1;

			$begdate=(!empty($checkl['begdate']) && ($checkl['begdate'] != "0000-00-00")) ? get_date_format($checkl['begdate']) : "";
			$enddate=(!empty($checkl['enddate']) && ($checkl['enddate'] != "0000-00-00")) ? get_date_format($checkl['enddate']) : "";
		?>
			<tr class="txt_10">
				<td height="18">&nbsp;<a href="#" class="txt_10"><?php echo $name;?></a></td>
				<td>&nbsp;<?php echo $begdate;?></td>
				<td>&nbsp;<?php echo $enddate;?></td>
			<tr>
		<?php
		}

		//===ADD OCULAR MEDS========
		$addOcuMeds = $CcHxPrint->getOrderedMeds($arr_tmp_chk_meds,1);
		echo $addOcuMeds;
		?>
	</table>
</div>
</body>
</html>