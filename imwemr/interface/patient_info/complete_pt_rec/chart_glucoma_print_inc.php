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

global $cpr;
$insideGlucoma = true; 
    // Default Date of Activation
    $elem_dateActivation = date("m-d-Y");
    $elem_activate = "-1";    
    
    //if Past Glucoma Record exists    
    $sql = imw_query("SELECT dateActivation,activate
            FROM glucoma_main 
            WHERE patientId = '".$cpr->patient_id."' 
            AND activate = '1' ");
    while($row = imw_fetch_array($sql)){
		$elem_dateActivation = get_date_format($row["dateActivation"],'mm-dd-yyyy');
        $elem_activate = $row["activate"];
	}; 
    
    //Patient Info
    $patientInfo = $cpr->getPatientInfo($cpr->patient_id);
    
?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Medical History:: imwemr ::</title>
	<!-- Bootstrap -->
	<link href="<?php echo $library_path; ?>/css/bootstrap.css" rel="stylesheet" type="text/css">
	<!-- Bootstrap Selctpicker CSS -->
	<link href="<?php echo $library_path; ?>/css/bootstrap-select.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/common.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/medicalhx.css" rel="stylesheet" type="text/css">
	<link href="<?php echo $library_path; ?>/css/jquery.mCustomScrollbar.css" rel="stylesheet" type="text/css">
	<!-- Messi Plugin for fancy alerts CSS -->
		<link href="<?php echo $library_path; ?>/messi/messi.css" rel="stylesheet" type="text/css">
	<!-- DateTime Picker CSS -->
	<link rel="stylesheet" type="text/css" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css"/>
	
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]--> 
	
	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js" type="text/javascript" ></script>
	<!-- jQuery's Date Time Picker -->
	<script src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js" type="text/javascript" ></script>
	<!-- Bootstrap -->
	<script src="<?php echo $library_path; ?>/js/bootstrap.js" type="text/javascript" ></script>
	
	<!-- Bootstrap Selectpicker -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-select.js" type="text/javascript"></script>
	<!-- Bootstrap typeHead -->
	<script src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/js/common.js" type="text/javascript"></script>
	<script src="<?php echo $library_path; ?>/messi/messi.js" type="text/javascript"></script>
</head>
<!-- Style Sheet -->
<style>
   fieldset{ float:left;padding:2px;width:100%}
   .tblgrid {border-bottom:1px solid #000000;border-right:1px solid #000000;}
   .tblgrid td { border-top:1px solid #000000;border-left:1px solid #000000; padding:1px;}    
   div#conAddNewTable{ display:none;}
   table#noStyle, table#noStyle td {border:0px; padding:0px;}
   #clrVoilet{/*color:#800080;*/ color:#000000; }
   
   #tbl_menu_vfNfa { z-index: 100;}
   .spechar{ text-decoration: none; font-size:14px; font-weight:600; }
   .spechar1{ text-decoration: none; font-size:13px; font-weight:600; }
   .divText{border:1px solid #7F9DB9; width: 40px; height:18px; } 
   /**---Style Sheet Changes For Printing Only----**/
        .text_gp9b    { font-family:"verdana"; font-size:9px; color:#000000; font-weight:bold;}
        .text_gp9    { font-family:"verdana"; font-size:9px; color:#000000;}
        .text_gp10    { font-family:"verdana"; font-size:10px; color:#000000;}
        .text_gp10b    { font-family:"verdana"; font-size:10px; color:#000000; font-weight:bold;  }        
   /**---Style Sheet Changes For Printing Only----**/     
   td.text_gp9,td.text_gp9b,td.text_gp10b,td.text_gp10,td.text_gp9b{ color:#000000;}
   legend.text_gp9b{ color:#000000;}
   .bluelne{border-bottom:1px solid #012778;!important;height:1px;}
	.bluehed{font-size:14px;color:#012778;font-weight:bold;}
</style>
<script>
	function onPageLoad()
    {   
       // window.resizeTo(810,700);        
        window.print();  
    }
</script>
<!-- Style Sheet -->
<!-- Main Box -->
<body onload="onPageLoad()">
	<div class="mainwhtbox">
		<div class="row">
			<table class="table table-condensed">
				
				<tr>
					<td style="background-color:#012778;color:#fff; font-size:13px;">GLAUCOMA FLOW SHEET</td>
					<?php
						foreach($patientInfo as $obj){
							?>
							<td style="background-color:#012778;color:#fff; font-size:13px;"><?php echo $obj['name']; ?>	</td>
							<td style="background-color:#012778;color:#fff; font-size:13px;">DOB: <?php echo $obj['DOB']; ?></td>	
							<?php	
						}
					?>
					<td style="background-color:#012778;color:#fff; font-size:13px;">Date of Activation: <?php echo $elem_dateActivation;?> </td>
					<td style="background-color:#012778;color:#fff; font-size:13px;">(<?php echo ($elem_activate == "1") ? "Activate" : "Deactivate";?>)</td>
				</tr>	
			</table>
		</div>
		<div class="row">
			<table class="table table-condensed">
				<tr>
					<td style="width:60%">
						<fieldset class="button">
							<legend class="text_gp9b">Initial</legend>
						  <?php 
							// InitialTop 
							require('initialTopPrint.php');
						  ?>
						</fieldset>
					</td>
					<td style="width:40%">
						<fieldset class="button">
							<legend class="text_gp9b">Medication Grid</legend>
							<!-- Medication -->    
								 <?php
									$str = $cpr->getMediGridPrint();
									echo $str;
								 ?>
							<!-- Medication -->
							<br>
							<!-- Surgery -->    
								<?php
								  $str = $cpr->getSurgeryGridPrint();
								  echo $str;
								?>        
							<!-- Surgery -->    
						</fieldset> 	
					</td>	
				</tr>
				<tr>
					<td colspan="2">	
						<fieldset class="button" style="position:relative;">                        
							<legend class="text_gp9b">Log</legend>                                                                                                                        
							<!-- Readings -->                        
							<?php
								echo $cpr->getGlaucomaLog();
							?>                       
							<!-- Readings -->                       
						</fieldset> 
					</td>
				</tr>
			</table>	
		</div>
	</div>
</body>
<!-- Main Box -->
</html>