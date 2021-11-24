<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php require_once('common/conDb.php'); 
session_start();
//include("common/linkfile.php");
//include("common/link_new_file.php");
//$ascID = $_SESSION['ascId']; 
$pConfId = $_REQUEST['pConfId'];
//$pConfId = $_SESSION['pConfId'];
$query_allergiespop = "SELECT * from patient_allergies_tbl WHERE patient_confirmation_id = '$pConfId'";
$result_allergiespop = imw_query($query_allergiespop, $link) or die(imw_error());
$totalRows_allergiespop = imw_num_rows($result_allergiespop);
//$row_allergiespop = imw_fetch_array($result_allergiespop);   ?>
<!--div id="AllergiesHeaderPopDiv" style="position:absolute;background-color:#E0E0E0;width:410px;display:none;"-->
	<?php if($totalRows_allergiespop>0) { ?>
			<!--<table class="text_10 table_collapse">
				<tr>
					<td style="width:1%;" class="alignRight"><img src="common/images/left.gif" style="width:3px; height:24px;"></td>
					<td class="alignLeft text_10b right_border" style="background-color:#BCD2B0; width:50%; border-right-color:#FFFFFF;">Name</td>
					<td style="width:48%; background-color:#BCD2B0;" class="alignLeft text_10b">&nbsp;Reaction</td>
					<td style="width:1%;" class="alignLeft valignTop"><img src="common/images/right.gif" style="width:3px; height:24px;"></td>
				</tr>-->
                <div style="width:99%">
                <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12" style="height:30px; width:100%;  background:#d9534f;  padding-top:5px">
                 	<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6" style="text-align:left; font-weight:bold; color:#FFF; margin:0px; padding:0px;">Name</div>
                 	<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6" style="text-align:left; font-weight:bold; color:#FFF; margin:0px; padding:0px;">Reaction</div>
                </div>
				<?php
				while($row_allergiespop = imw_fetch_array($result_allergiespop)){
				?>
                	<div class="clearfix" style="border-bottom:1px solid #CCC;"></div>
                    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
						<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6" style="text-align:left; margin:2px 0px; padding:0px;"><?php echo $row_allergiespop["allergy_name"];?></div>
                 		<div class="col-md-6 col-lg-6 col-xs-6 col-sm-6" style="text-align:left; padding:0px;"><?php echo $row_allergiespop["reaction_name"];?></div>
                    </div>
                	
				<?php
				} 
				?>
                </div>
                
			<!--</table>-->
	<?php } ?>		
<!--</div>-->
<script>window.focus();</script>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />