<?php
// Under MIT License
// Use, Modify, Distribute under MIT License.
// MIT License 2019
?>
<?php
	session_start();
	set_time_limit(500);
	include_once("common/conDb.php");
	$bgHeadingImage = "";
	
	$prdtVrsnDt = 'Ver R5.2  Jan 03, 2013';
	if(constant('PRODUCT_VERSION_DATE')!='') { $prdtVrsnDt = constant('PRODUCT_VERSION_DATE'); }
		
	$posSt = strpos($prdtVrsnDt,' ',(strpos($prdtVrsnDt,' ')+1));
	$version = trim(substr($prdtVrsnDt,strpos($prdtVrsnDt,' '),$posSt-1));
	$versionDate = trim(substr($prdtVrsnDt,$posSt));


?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Surgerycenter EMR</title>
<script type="text/javascript" src="js/jquery-1.11.3.js"></script>
<script>

	window.focus();
</script>

<style>
	body {color:#333; font-family:Verdana,Arial; font-size:13px; margin:0;}
	thead tr { background-image:url(images/header_bg.jpg); font-weight:600; font-size:14px; }
	thead tr td {padding:5px; }
	tbody tr:first-child div {background-color:#ECF1EA; padding:10px; height:auto;min-height:400px; margin:0; overflow:hidden; overflow-x:auto; display:block; margin-top:28px; margin-bottom:30px; }
	thead, thead tr, thead td  { position:fixed; display:block; top:0; width:100%;}
	thead td {width:50%; }
	tfoot, tfoot tr, tfoot td  { background-color:white; z-index:99; position:fixed; display:block; bottom:0; width:100%;}
	tfoot td { text-align:center; }
	
</style>
</head>

<body >
<table class="text_10" style="width:100%; border:none;" cellpadding="0" cellspacing="0">
	<thead>
    	<tr style="height:28px;" >
        	<td  align="left"  style="float:left; "  >
            	Version Release Document
        	</td>
            <td align="right" style="float:right; right:0; " >
            	Version: <?=$version?> <small>Date: <?=$versionDate?></small>
            </td>
      	</tr>
 	</thead>
    
    <tfoot>
    	<tr>
        	<td class="valignTop nowrap" colspan="2">
				<a href="#" onclick="MM_swapImage('closeButton','','images/close_onclick1.gif',1);" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('closeButton','','images/close_hover.gif',1)"><img src="images/close.gif" id="closeButton" style="border:none;" alt="Close" onclick="window.close();"></a>
						</td>
      	</tr>
 	</tfoot>
    
    <tbody>
    	<tr>
    		<td colspan="2" class="tst11b">
    			<div >
                	<h2>Release Note : <?=$version ?></h2>
                	<p>
                    	
                    	<!-- Place Content Here -->
                    </p>
                </div>
    		</td>
    	</tr>
  	</tbody>      
</table>	
</body>
</html>