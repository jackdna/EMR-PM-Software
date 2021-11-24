<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.


function printSpreadSheet($sheetName,$dataExist,$endLoop = 20)
{
	$dataArray	=	array();
	$html	=	'';
	$tabIndex	=	0;
	if($dataExist)
	{
		$dataArray	=	explode('~@~',$dataExist);
		$dataCount	=	count($dataArray);
		$endLoop		=	$endLoop + $dataCount;
	}
	
	$html	.=	'<table class="col-xs-12 col-md-12 col-lg-12 col-sm-12 table-bordered table-condensed cf width_table table-striped padding_0" id="'.$sheetName.'MedTable">';
	$html	.=	'<tbody>';
	
	for($loop = 0, $counter	=	1 ; $loop < $endLoop ; $loop++, $counter++)
	{ 
		$medFieldName	=	$sheetName.'Med';
		$lotFieldName	=	$sheetName.'Lot';
		
		$innValue	=	trim($dataArray[$loop]);
		$innArray	=	explode('@#@',$innValue);
		$medValue	=	$innArray[0];
		$lotValue	=	$innArray[1];
		
		$html	.=	'<tr>';
		
		$html	.=	'<td class="padding_0" style="width:45% !important">';
		$html	.=	'<input type="text" name="'.$medFieldName.'[]" id="'.$medFieldName.$counter.'" class="form-control" style="border-radius:0;" value="'.$medValue.'" tabindex="'.(++$tabIndex).'" />';
		$html	.=	'</td>';
		
		$html	.=	'<td class="padding_0" style="width: 45% !important;">';
		$html	.=	'<input type="text" name="'.$lotFieldName.'[]" id="'.$lotFieldName.$counter.'" class="form-control" style="border-radius:0;" value="'.$lotValue.'" tabindex="'.(++$tabIndex).'" />';
		$html	.=	'</td>';
		$html	.=	'</tr>';
	}
	
	$html	.=	'</tbody>';
	$html	.=	'</table>';
	
	echo $html;
}
?>