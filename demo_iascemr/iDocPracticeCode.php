<?php 
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
header("Cache-control: private, no-cache"); 
header("Expires: Mon, 26 Jun 1997 05:00:00 GMT"); 
header("Pragma: no-cache");

	include_once('connect_imwemr.php'); // imwemr connection
	if($_REQUEST['ctype']=='cpt')
	{
		$data	=	array(); 
		$query	=	"Select distinct(cpt4_code) From cpt_fee_tbl Where cpt4_code <> '' And status='Active' And delete_status = '0' Order By cpt4_code";
		$sql		=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).': ' . imw_error());
		$cnt		=	imw_num_rows($sql);
		if($cnt > 0 )
		{
			while($row = imw_fetch_assoc($sql) )
			{
				array_push($data,$row);
			}
		}	
	}
	elseif($_REQUEST['ctype']=='proceduresCode')
	{
		$data	=	array(); 
		$query	=	"Select distinct(proc) From slot_procedures Where proc <> '' And active_status='yes' Order By proc";
		$sql	=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).': ' . imw_error());
		$cnt	=	imw_num_rows($sql);
		if($cnt > 0 )
		{
			while($row = imw_fetch_assoc($sql) )
			{
				array_push($data,$row['proc']);
			}
		}	
	}
	else
	{
		$data	=	array(); 
		$query	=	"Select cpt4_code, cpt_prac_code From cpt_fee_tbl Where cpt4_code <> '' And status='Active' And delete_status = '0' Order By cpt_prac_code ";
		$sql		=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).': ' . imw_error());
		$cnt		=	imw_num_rows($sql);
		if($cnt > 0 )
		{
			while($row = imw_fetch_object($sql) )
			{
				if(!array_key_exists($row->cpt4_code,$data))
					$data[$row->cpt4_code]	=	array();
					
				$data[$row->cpt4_code][]	=	$row->cpt_prac_code ;
			}
		}
	}
	echo json_encode($data);

?>