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
	
	$data	=	array(); 
	$query	=	"Select modifier_code, mod_prac_code, mod_description From modifiers_tbl Where modifier_code <> '' And status='Active' And delete_status = '0' Order By modifier_code ";
	$sql		=	imw_query($query) or die( 'Error found at line no. '.(__LINE__).': ' . imw_error());
	$cnt		=	imw_num_rows($sql);
	if($cnt > 0 )
	{
		while($row = imw_fetch_assoc($sql) )
		{
			array_push($data,$row);
		}
	}
	
	echo json_encode($data);

?>