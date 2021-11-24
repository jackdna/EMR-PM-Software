<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.

?><?php
function get_image_prop($image_name,$tw,$th)
  {
   $image_attributes=@getimagesize("$image_name");
   $ow=$image_attributes[0];
   $oh=$image_attributes[1];
//echo($ow."=$tw Ram W".$oh."Ram H".$th);
   
   if($ow<=$tw && $oh<=$th){
   $ret[0]=$ow;
   $ret[1]=$oh;
   return($ret);
 }else{
   $pc_width=$tw/$ow; 
   $pc_height=$th/$oh; 
   $pc_width=number_format($pc_width,2);
   $pc_height=number_format($pc_height,2);
//echo("Percentage Width=".$pc_width."and Perscentage height=".$pc_height);
		 if($pc_width<$pc_height){
		   $rd_image_width=number_format(($ow*$pc_width),2);
		   $rd_image_height=number_format(($oh*$pc_width),2);
		   $ret[0]=$rd_image_width;
		   $ret[1]=$rd_image_height;
		   return($ret);
		   }else if($pc_height<$pc_width){   
		   $rd_image_width=number_format(($ow*$pc_height),2);
		   $rd_image_height=number_format(($oh*$pc_height),2);
		   $ret[0]=$rd_image_width;
		   $ret[1]=$rd_image_height;
		   return($ret);
		   }
		}   
  }
  
$table = '
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Surgery Center EMR</title>
</head>


<body topmargin="0" rightmargin="0" leftmargin="0">

<table width="600" border="0" id="print_tbl1" align="left" cellpadding="1" cellspacing="1" style="border:1px solid #4684ab;">';
$path = "uploaddir"; //'//192.168.0.3/documents/test';
$ab =  opendir($path);
while(($filename = readdir($ab)) !== false)
	{
		$path1 = pathinfo($filename);
		if($path1['extension'] == 'jpg')
		{
			$g1=get_image_prop($path.'\\'.$filename,595,842);
			$table .='<tr><td align="center">
			<img src="'.$path.'\\'.$filename.'" height="'.$g1[1].'" width="'.$g1[0].'" align="center">
		
			</td></tr>';
		}
				
	}
	$table .='</table>
</body>
</html>';
//echo $table;
$file = fopen('pdfFile.html','w');
$data = fputs($file,$table);

fclose($file);

$pconfirmId = $_REQUEST['pconfirmId'];
$patient_id = $_REQUEST['patient_id'];
$ptStubId 	= $_REQUEST['ptStubId'];
$formName 	= $_REQUEST['formName'];
$folderId 	= $_REQUEST['folderId'];
$scanIOL	= $_REQUEST['scanIOL'];
$IOLScan 	= $_REQUEST['IOLScan'];
?>

<form name="frm" action="index.php">
	<input type="hidden" name="page" value="5">
	<input type="hidden" name="font_size" value="10">
	
	<input type="hidden" name="pconfirmId" value="<?php echo $pconfirmId;?>">
	<input type="hidden" name="patient_id" value="<?php echo $patient_id;?>">
	<input type="hidden" name="ptStubId" value="<?php echo $ptStubId;?>">
	<input type="hidden" name="formName" value="<?php echo $formName;?>">	
	<input type="hidden" name="folderId" value="<?php echo $folderId;?>">	
	<input type="hidden" name="scanIOL" value="<?php echo $scanIOL;?>">	
	<input type="hidden" name="IOLScan" value="<?php echo $IOLScan;?>">	
	
</form>
<script language="javascript">


 document.frm.submit();

</script>