<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
require($GLOBALS['incdir']."/chart_notes/chart_globals.php");
require($GLOBALS['srcdir']."/classes/work_view/wv_functions.php");

?>
<html>
<head>
<title>Get Chart Save Log</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<style> 
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    text-align: left;
    padding: 8px;
}

tr:nth-child(even) {background-color: #f2f2f2;}
tr table tr:nth-child(even) {background-color: #aaaaaa;}
</style>
</head>
<body>
	<?php
		echo "<h1>Enter patient id and DOS to get Chart note log!</h1><br/>
	<form name='frm' method='post'>
		* Patient Id: <input name='elem_pt' value=''> 		
		* DOS: <input name='elem_dos' value=''>
		FormID: <input name='elem_frmid' value=''>	
		<input type='submit' name='elem_sub' value='Show Log!'>
	</form>
	<br/>
";

$oChartLog = new ChartLog();
$oChartLog->main();
	?>
</body>
</html>