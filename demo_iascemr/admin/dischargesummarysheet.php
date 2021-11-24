<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
include_once("../globalsSurgeryCenter.php");
include_once("logout.php");
include("adminLinkfile.php");
include_once("funcSurgeryCenter.php");
include_once("classObjectFunction.php");
$objManageData = new manageData;
$getProcedureDetails = $objManageData->getArrayRecords('procedurescategory');
?>
<html>
<head>
<title>Discharge Summary Sheet</title>
<link href="adminStyle.css" rel="stylesheet" type="text/css">
</head>
<body><br>
	<table border="0" width="100%" cellpadding="0" cellspacing="0" align="center">
		<!--
		<tr>
			<td colspan="2" align="left" class="text_10b" style="font-size:14px;">Discharge Summary Sheet</td>
		</tr>
		 -->		
 		<tr bgcolor="#BCD2B0" height="25">
			<td class="text_10b" align="left">Discharge Summary</td>
			<td class="text_10b" align="right" style="padding-right:5px;">Progress Notes</td>
		</tr>		
		<tr>
			<td colspan="2" class="text_10b" align="left">
				<table border="0" cellpadding="0" cellspacing="0">
					<?php
					if(is_array($getProcedureDetails)){
						foreach($getProcedureDetails as $procedureName){
							$proceduresCatListId = $procedureName->proceduresCategoryId;
							$proceduresListName = $procedureName->name;
							?>
							<tr>
								<td align="left" class="text_10b"><?php echo $proceduresListName; ?></td>
							</tr>
							<?php
							$getProcedures = $objManageData->getArrayRecords('procedures', 'catId', $proceduresCatListId);
							if($getProcedures){
								foreach($getProcedures as $subProcedures){
									?>
									<tr height="20">
										<td align="left">
											<table border="0" cellpadding="0" cellspacing="0">
												<tr>
													<td width="15"></td>
													<td class="text_10"><?php echo $subProcedures->code; ?></td>
													<td width="15"></td>
													<td class="text_10"><?php echo $subProcedures->name; ?></td>
												</tr>
											</table>
										</td>
										<td align="center"><input type="checkbox"></td>
									</tr>
									<?php
								}
							}
						}
					}
					?>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>
