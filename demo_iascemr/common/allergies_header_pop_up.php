<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php require_once('common/conDb.php'); 
session_start();
include("common/linkfile.php");
$ascID = $_SESSION['ascId']; 
$pConfId = $_SESSION['pConfId'];
$query_allergiespop = "SELECT * from patient_allergies_tbl WHERE patient_confirmation_id = '$pConfId'  AND asc_id = '$ascID'";
$result_allergiespop = imw_query($query_allergiespop, $link) or die(imw_error());
$totalRows_allergiespop = imw_num_rows($result_allergiespop);
//$row_allergiespop = imw_fetch_array($result_allergiespop);   ?>
<!--div id="AllergiesHeaderPopDiv" style="position:absolute;background-color:#E0E0E0;width:410px;display:none;"-->
	<?php if($totalRows_allergiespop>0) { ?>
			<table border="0" cellpadding="0" class="text_10" cellspacing="0" width="100%" >
				<tr>
					<td width="1%" align="right"><img src="images/left.gif" width="3" height="24"></td>
					<td align="left" bgcolor="#BCD2B0" width="50%" class="text_10b right_border" style="border-right-color:#FFFFFF;">Name</td>
					<td width="48%" align="left" bgcolor="#BCD2B0" class="text_10b">&nbsp;Reaction</td>
					<td width="1%" align="left" valign="top"><img src="images/right.gif" width="3" height="24"></td>
				</tr>
				<?php
				while($row_allergiespop = imw_fetch_array($result_allergiespop)){
				?>
					<tr>
						<td width="1%" align="right"></td>
						<td align="left"  width="50%" class="text_10 right_border" style="border-right-color:#FFFFFF; "><?php echo stripslashes($row_allergiespop["allergy_name"]);?></td>
						<td width="48%" align="left"  class="text_10">&nbsp;<?php echo stripslashes($row_allergiespop["reaction_name"]);?></td>
						<td width="1%" align="left" valign="top"></td>
					</tr>
				<?php
				} 
				?>
			</table>
	<?php } ?>		
<!--</div>-->
