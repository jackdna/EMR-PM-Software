<?php 
	/*
	File: print.php
	Coded in PHP7
	Purpose: Print Order Detail
	Access Type: Direct access
	*/
	require_once(dirname('__FILE__')."/../../config/config.php"); 
	require_once(dirname('__FILE__')."/../../library/classes/functions.php"); 
		
	$patient_id=$_SESSION['patient_session_id'];
		// COMMON FUNCTIONS
	$arrManufac=array();
	$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where frames_chk='1' and del_status='0'";
	$manu_detail_res = imw_query($manu_detail_qry);
	$manu_detail_nums = imw_num_rows($manu_detail_res);
	if($manu_detail_nums > 0)
	{	
		while($manu_detail_row = imw_fetch_array($manu_detail_res)) 
		{
			$arrManufac[$manu_detail_row['id']] = $manu_detail_row['manufacturer_name'];
		}
	}
	
	if($_SESSION['order_id']>0)
	{
			$order_id=$_SESSION['order_id'];
			$sel_qry_val = "select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='1' and del_status='0'";
			$sel_qry = imw_query($sel_qry_val);
			$sel_order=imw_fetch_array($sel_qry);
			$frame_order_detail_id=$sel_order['id'];			
						
			$sel_lens_qry=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='2' and del_status='0'");
			$sel_lens_order=imw_fetch_array($sel_lens_qry);
			$lens_order_detail_id=$sel_lens_order['id'];
			
			$sel_contact_qry=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='3' and del_status='0'");
			$sel_contact_order=imw_fetch_array($sel_contact_qry);
			$cl_order_detail_id=$sel_contact_order['id'];

			$lens_item_id=$sel_lens_order['item_id'];
			$contact_item_id=$sel_contact_order['item_id'];
			
			$sel_qry2=imw_query("select id,name from in_item where id in($lens_item_id,$contact_item_id)");
			while($sel_item2=imw_fetch_array($sel_qry2)){
				if($lens_item_id==$sel_item2['id'])
				{
					$lens_name=$sel_item2['name'];
				}
				if($contact_item_id==$sel_item2['id'])
				{
					$contact_name=$sel_item2['name'];
				}
			}
			
			 //LENS PRESCRIPTION
			$lensRs=imw_query("Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND det_order_id='$lens_order_detail_id' AND patient_id='".$_SESSION['patient_session_id']."'");
			
			$lensRes=imw_fetch_array($lensRs);
			//CONTACT LENS PRESCRIPTION
			$clLensRs=imw_query("Select * FROM in_cl_prescriptions WHERE order_id='".$order_id."' AND det_order_id='$cl_order_detail_id' AND patient_id='".$_SESSION['patient_session_id']."'");
			$clLensRes=imw_fetch_array($clLensRs);
			
		}
       

$manuFacOptions='';
foreach($arrManufac as $id => $manufacName)
{
	$sel=($id==$sel_order['manufacturer_id'])? 'selected': '';
	$manufacName=$manufacName;
}

	$frameBrandOpts='';                                    
	$sql = "SELECT frame_source,id FROM in_frame_sources WHERE del_status = 0 ORDER BY frame_source ASC";
	$res = imw_query($sql);
	while($row = imw_fetch_assoc($res)){
	$sel=($row['id']==$sel_order['brand_id'])? 'selected': '';
	$frameBrandName = $row['frame_source'];
}

$rsShape = imw_query("select * from in_frame_styles where del_status<='1' order by style_name");
while($resShape=imw_fetch_array($rsShape)){
$frame_style_name = ucfirst($resShape['style_name']); 
}
 
$rsColor = imw_query("select * from in_frame_color where del_status='0' order by color_name asc");
while($resColor=imw_fetch_array($rsColor))
{ 
$sel=($resColor['id']==$sel_order['color_id'])? 'selected': '';
$frame_color_name = ucfirst($resColor['color_name']); 
}

$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
while($lab_row = imw_fetch_assoc($lab_qry)){
if($lab_row['id']==$sel_order['lab_id']){ $frame_lab_name = $lab_row['lab_name']; }   
}

$rows="";
$lensTypeRs = imw_query("select * from in_lens_type where del_status='0' order by type_name asc");
while($lensTypeRes=imw_fetch_array($lensTypeRs))
{  
$sel=($lensTypeRes['id']==$sel_lens_order['type_id'])? 'selected': '';
$lense_type_name = $lensTypeRes['type_name'];
}


$rows="";
$lensMatRs= imw_query("select * from in_lens_material where del_status='0' order by material_name asc");
while($lensMatRes=imw_fetch_array($lensMatRs))
{ 
$sel=($lensMatRes['id']==$sel_lens_order['material_id'])? 'selected': '';
$lense_material_name = $lensMatRes['material_name'];	
}	

$rows="";
$lensColorRs= imw_query("select * from in_lens_color where del_status='0' order by color_name asc");
while($lensColorRes=imw_fetch_array($lensColorRs))
{ 
$sel=($lensColorRes['id']==$sel_lens_order['color_id'])? 'selected': '';
$lens_color_name = $lensColorRes['color_name'];
}

$lab_qry = imw_query("select * from in_lens_lab where del_status='0' order by lab_name asc");	
while($lab_row = imw_fetch_assoc($lab_qry)){
if($lab_row['id']==$sel_lens_order['lab_id']){ $lens_lab_name = $lab_row['lab_name']; }
}


$manuFacOptions='';
foreach($arrManufac as $id => $manufacName){
$sel=($id==$sel_contact_order['manufacturer_id'])? 'selected': '';
$manufacName = $manufacName;
}




$rows="";
$rows = data("select * from in_contact_cat where del_status='0' order by cat_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['contact_cat_id'])? 'selected': '';

$contact_cat_name = ucfirst($r['cat_name']);	
}

$rows = data("select * from in_type where del_status='0' order by type_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['type_id'])? 'selected': '';
$contacts_type_name = ucfirst($r['type_name']); 
}


$qry="";
$qry = imw_query("select * from in_brand where status='0' order by brand_name asc");
while($rows = imw_fetch_array($qry))
{
$sel=($rows['id']==$sel_contact_order['brand_id'])? 'selected': ''; 

$contacts_brand_name = $rows['brand_name']; 
}	




$rows = data("select * from in_color where del_status='0' order by color_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['color_id'])? 'selected': ''; 
$contacts_color_name = ucfirst($r['color_name']); 
}	


$rows="";
$rows = data("select * from in_supply where del_status='0' order by supply_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['supply_id'])? 'selected': ''; 
$contacts_supply_name = ucfirst($r['supply_name']);
}	



?>

<?php
		
		$css = '<style>	
			.table_collapse
			{	width:100%; padding:0px; border-collapse:collapse;	}
			.table_cell_padd5 td{ padding:5px; }
			.headingbg{ background:#c6ebfe; }						
		</style>';
					
					$pinfo = imw_query("select fname,lname,mname from patient_data where id = '".$patient_id."'");
					$pinforow = imw_fetch_assoc($pinfo);
					$pname = $pinforow['lname'].", ".$pinforow['fname']." ".$pinforow['mname'];
					
$pdfHTML='<page backtop="9mm" backbottom="5mm">
<page_header>
<table style="clear:both; width:100%;" class="fl table_collapse table_cell_padd5" border="0" width="100%">
<tr class="bgcolor">
<td width="343" align="left" class="headingbg"><strong>Order ID:</strong> '.$_SESSION['order_id'].'</td>
<td width="343" align="center" class="headingbg"><strong>'.$pname.'</strong></td>			  
<td width="343" align="right" class="headingbg"><strong>'.date("m-d-Y h:i a",time())." &nbsp; ".$_SESSION['authProviderName'].'</strong></td>
</tr>
</table></page_header>';				
		
$lensRs=imw_query("Select * FROM in_optical_order_form WHERE order_id='".$order_id."' AND det_order_id='$lens_order_detail_id' AND patient_id='".$_SESSION['patient_session_id']."'");
			
	if(imw_num_rows($lensRs)>0)
	{
							
		$pdfHTML.='
		 <table style="clear:both; width:100%;" class="fl table_collapse table_cell_padd5" border="0" width="100%">
            <tr  class="bgcolor">
              <td class="headingbg" colspan="9"><strong>Lens Prescription </strong></td>
            </tr>
			
			<tr>
              <td width="150"><strong>OD</strong> : Sph : '.$lensRes["sphere_od"].' </td>
              <td width="150"><strong>C :</strong> '.$lensRes["cyl_od"].' </td>              
              <td width="150"><strong>A :</strong> '.$lensRes["axis_od"].' </td>              
              <td width="150"><strong>P :</strong> '.$lensRes["mr_od_p"].' &nbsp; &nbsp; '.$lensRes["mr_od_prism"].' / '.$lensRes["mr_od_splash"].' &nbsp; &nbsp; '.$lensRes["mr_od_sel"].'</td>
              <td width="150">&nbsp;</td>           
              <td width="100">&nbsp;</td>              
              <td width="100">&nbsp;</td>              
            </tr>
            
            <tr>
              <td><strong>OS</strong> : Sph : '.$lensRes["sphere_os"].'</td>
              <td><strong>C :</strong> '.$lensRes["cyl_os"].' </td>              
              <td><strong>A :</strong> '.$lensRes["axis_os"].' </td>              
              <td><strong>P :</strong> '.$lensRes["mr_os_p"].' &nbsp; &nbsp; '.$lensRes["mr_os_prism"].' / '.$lensRes["mr_os_splash"].' &nbsp; &nbsp; '.$lensRes["mr_os_sel"].'</td>
              <td>&nbsp;</td>           
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
            </tr>

		    <tr>
              <td><strong>DPD</strong> : '.$lensRes["dist_pd_od"].' / '.$lensRes["dist_pd_os"].'</td>
              <td><strong>NPD</strong> : '.$lensRes["near_pd_od"].' / '.$lensRes["near_pd_os"].'</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td><strong>Seg</strong> : &nbsp; / &nbsp;</td>
              <td><strong>Add</strong> : '.$lensRes["add_od"].' / '.$lensRes["add_os"].'</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td><strong>Ship To</strong> : Home</td>
              <td><strong>Location</strong> : </td>
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
              <td>&nbsp;</td>
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
            </tr>                 
      </table>';
	}
	  
			$clLensRs=imw_query("Select * FROM in_cl_prescriptions WHERE order_id='".$order_id."' AND det_order_id='$cl_order_detail_id' AND patient_id='".$_SESSION['patient_session_id']."'");

if(imw_num_rows($clLensRs)>0)
{
	  
  $pdfHTML .= '<table style="clear:both;display:'.$clLensRsDisplay.';" class="fl table_collapse table_cell_padd5" border="0" width="100%">
            <tr bgcolor="#f4f4f4">
              <td class="headingbg" colspan="8"><strong>Contact Lens Prescription</strong></td>
            </tr>
            <tr>
              <td width="150"><strong>OD</strong> : Sph : '.$clLensRes["sphere_od"].' </td>
              <td width="150"><strong>C :</strong> '.$clLensRes["cylinder_od"].' </td>              
              <td width="150"><strong>A :</strong> '.$clLensRes["axis_od"].' </td>              
              <td width="150">&nbsp;</td>
              <td width="180">&nbsp;</td>
              <td width="185">&nbsp;</td>
            </tr>
            
            <tr>
              <td><strong>OS</strong> : Sph : '.$clLensRes["sphere_os"].'</td>
              <td><strong>C :</strong> '.$clLensRes["cylinder_os"].'</td>
              <td><strong>A :</strong> '.$clLensRes["axis_os"].'</td>
              <td>&nbsp;</td>              
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>

            <tr>
              <td><strong>Add</strong> : '.$clLensRes["add_od"].' / '.$clLensRes["add_os"].'</td>
              <td>&nbsp;</td>              
              <td>&nbsp;</td>
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
            </tr>  

            <tr>
              <td><strong>Base</strong> : '.$lensRes["base_od"].' / '.$clLensRes["base_os"].'</td>
              <td><strong>Diam</strong> : '.$lensRes["diameter_od"].' / '.$clLensRes["diameter_os"].'</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>   
			
            <tr>
              <td><strong>Ship To</strong> : '.$manufacName.'</td>
              <td><strong>Location</strong> : '.$lensRes["sphere_od"].'</td>
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
              <td>&nbsp;</td>              
            </tr>                      
        </table><br />';
}


	$sel_qry_check = imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='2' and del_status='0'");	
	
	if(imw_num_rows($sel_qry_check)>0)
	{

		$pdfHTML.='<table style="clear:both;" class="fl table_collapse table_cell_padd5" border="0" width="100%">
		  <tr bgcolor="#f4f4f4">
		  <td class="headingbg" colspan="16"><strong>Frame</strong></td>
		  </tr>
		  <tr>
			  <td width="30"><strong>UPC</strong></td>
			  <td width="90"><strong>Manufacturer</strong></td>
			  <td width="10"><strong>A</strong></td>
			  <td width="10"><strong>B</strong></td>
			  <td width="10"><strong>ED</strong></td>
			  <td width="25"><strong>Bridge</strong></td>             
			  <td width="20"><strong>FPD</strong></td>
			  <td width="40"><strong>Brand</strong></td>
			  <td width="40"><strong>Shape</strong></td>
			  <td width="40"><strong>Color</strong></td>
			  <td width="80"><strong>Sent to Lab</strong></td>
			  <td width="75"><strong>Ordered</strong></td>
			  <td width="75"><strong>Received</strong></td>
			  <td width="75"><strong>Notified</strong></td>
			  <td width="75"><strong>Dispensed</strong></td>
			  <td width="40"><strong>Notes</strong></td>
		</tr>';
			
$sel_qryy = "select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='1' and del_status='0'";

			$sel_qry = imw_query($sel_qryy);			
			while($sel_order_row=imw_fetch_array($sel_qry))
			{

$frameBrandOpts='';                                    
$frameBrandName='';
$sql2 = "SELECT frame_source,id FROM in_frame_sources WHERE id = '".$sel_order_row['brand_id']."' and del_status = 0";
$res2 = imw_query($sql2);
while($row2 = imw_fetch_assoc($res2))
{
	$frameBrandName = $row2['frame_source'];
}

$frame_style_name='';
$rsShape3 = imw_query("select * from in_frame_styles where id = '".$sel_order_row['style_id']."' and del_status<='1' order by style_name");
while($resShape3=imw_fetch_array($rsShape3)){
	$frame_style_name = ucfirst($resShape3['style_name']);
 }
 
$frame_color_names=''; 
$rsColor4 = imw_query("select * from in_frame_color where id = '".$sel_order_row['color_id']."' and del_status='0' order by color_name asc");
while($resColor4=imw_fetch_array($rsColor4))
{ 
$frame_color_names = ucfirst($resColor4['color_name']); 
}

$manu_detail_qry2 = "select manufacturer_name from in_manufacturer_details where id = '".$sel_order_row['manufacturer_id']."' and frames_chk='1' and del_status='0'";

$manu_detail_res2 = imw_query($manu_detail_qry2);
$manu_detail_nums2 = imw_num_rows($manu_detail_res2);
if($manu_detail_nums2 > 0)
{	
	while($manu_detail_row2 = imw_fetch_array($manu_detail_res2))
	{
		$manufacturer_name = $manu_detail_row2['manufacturer_name'];
	}
}
			  $pdfHTML.='<tr>
              <td>'.$sel_order_row['upc_code'].'</td>
			  <td>'.$manufacturer_name.'</td>
              <td>'.$sel_order_row['a'].'</td>
              <td>'.$sel_order['b'].'</td>
              <td>'.$sel_order_row['ed'].'</td>              
              <td>'.$sel_order_row["bridge"].'</td>              
              <td>'.$sel_order_row["fpd"].'</td>              
              <td>'.$frameBrandName.'</td>
              <td>'.$frame_style_name.'</td>
              <td>'.$frame_color_names.'</td>
              <td>'.$frame_lab_name.'</td>
          <td>'.getDateFormat($sel_order_row['ordered']).'</td>
          <td>'.getDateFormat($sel_order_row['received']).'</td>
          <td>'.getDateFormat($sel_order_row['notified']).'</td>
          <td>'.getDateFormat($sel_order_row['dispensed']).'</td>
          <td>'.getDateFormat($sel_order_row['item_comment']).'</td>           
            </tr>';
			
			}
			
  	$pdfHTML.='</table><br />';
	}
	
	
	$sel_lens_qryy_check=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='2' and del_status='0'");
	
	if(imw_num_rows($sel_lens_qryy_check)>0)
	{
	$pdfHTML.='<table style="clear:both; " class="fl table_collapse table_cell_padd5" border="0" width="100%">
        <tr bgcolor="#f4f4f4">
          <td class="headingbg" colspan="12"><strong>Lenses</strong></td>
        </tr>            
		<tr>
		  <td width="30"><strong>UPC</strong></td>
          <td width="50"><strong>Type</strong></td>
          <td width="55"><strong>Material</strong></td>
          <td width="30"><strong>Name</strong></td>
          <td width="130"><strong>List os lens options</strong></td>
          <td width="30"><strong>Color</strong></td>              
          <td width="75"><strong>Sent to lab</strong></td>
          <td width="70"><strong>Ordered</strong></td>
          <td width="70"><strong>Received</strong></td>
          <td width="70"><strong>Notified</strong></td>
          <td width="70"><strong>Dispensed</strong></td>
          <td width="90"><strong>Notes</strong></td>
        </tr>';
		
$sel_lens_qryy=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='2' and del_status='0'");

			while($sel_lens_order=imw_fetch_array($sel_lens_qryy))
			{
				
$lensTypeRs = imw_query("select * from in_lens_type where id = '".$sel_lens_order['type_id']."' and del_status='0' order by type_name asc");
while($lensTypeRes=imw_fetch_array($lensTypeRs))
{  
$lense_type_name = $lensTypeRes['type_name'];
}


$rows="";
$lensMatRs= imw_query("select * from in_lens_material where id='".$sel_lens_order['material_id']."' and del_status='0' order by material_name asc");
while($lensMatRes=imw_fetch_array($lensMatRs))
{ 
$lense_material_name = $lensMatRes['material_name'];	
}	

$rows="";
$lensColorRs= imw_query("select * from in_lens_color where id='".$sel_lens_order['color_id']."' and del_status='0' order by color_name asc");
while($lensColorRes=imw_fetch_array($lensColorRs))
{ 
$lens_color_name = $lensColorRes['color_name'];
}

$lab_qry = imw_query("select * from in_lens_lab where id='".$sel_lens_order['lab_id']."' and del_status='0' order by lab_name asc");	
                                   while($lab_row = imw_fetch_assoc($lab_qry)){
                                  $lens_lab_name = $lab_row['lab_name'];
 		                           	}			
        $pdfHTML.='<tr>
		 <td>'.$sel_lens_order['upc_code'].'</td>
          <td>'.$lense_type_name.'</td>
          <td>'.$lense_material_name.'</td>
          <td>'.$sel_lens_order['item_name'].'</td>              
          <td>'.$lensRes["sphere_od"].'</td>              
          <td>'.$lens_color_name.'</td>              
          <td>'.$lens_lab_name.'</td>              
          <td>'.getDateFormat($sel_lens_order['ordered']).'</td>
          <td>'.getDateFormat($sel_lens_order['received']).'</td>
          <td>'.getDateFormat($sel_lens_order['notified']).'</td>
          <td>'.getDateFormat($sel_lens_order['dispensed']).'</td>
          <td>'.getDateFormat($sel_lens_order['item_comment']).'</td>
        </tr>';
		
			}
$pdfHTML.='</table><br />';
}

		$sel_contact_qry_check=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='3' and del_status='0'");
	
	
	if(imw_num_rows($sel_contact_qry_check)>0)
	{
			

		$pdfHTML.='<table style="clear:both;" class="fl table_collapse table_cell_padd5" border="0" width="100%">
        <tr bgcolor="#f4f4f4">
          <td class="headingbg" colspan="13"><strong>Contacts</strong></td>
        </tr>            
		<tr>
          <td width="30"><strong>UPC</strong></td>
		  <td width="80"><strong>Type</strong></td>
          <td width="55"><strong>Category</strong></td>
          <td width="80"><strong>Wear Type</strong></td>
          <td width="30"><strong>Brand</strong></td>
          <td width="30"><strong>Name</strong></td>              
          <td width="30"><strong>Color</strong></td>
          <td width="50"><strong>Supply</strong></td>
          <td width="70"><strong>Ordered</strong></td>
          <td width="70"><strong>Received</strong></td>
          <td width="70"><strong>Notified</strong></td>
          <td width="70"><strong>Dispensed</strong></td>
          <td width="105"><strong>Notes</strong></td>
        </tr>';



		$sel_contact_qryy=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id='3' and del_status='0'");
		while($sel_contact_order=imw_fetch_array($sel_contact_qryy))
		{
			
			


$rows="";
$rows = data("select * from in_contact_cat where id = '".$sel_contact_order['contact_cat_id']."' and del_status='0' order by cat_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['contact_cat_id'])? 'selected': '';

$contact_cat_name = ucfirst($r['cat_name']);	
}





$rows = data("select * from in_type where id = '".$sel_contact_order['type_id']."' and del_status='0' order by type_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['type_id'])? 'selected': '';
$contacts_type_name = ucfirst($r['type_name']); 
}


$qry="";
$qry = imw_query("select * from in_brand where id = '".$sel_contact_order['brand_id']."' and status='0' order by brand_name asc");
while($rows = imw_fetch_array($qry))
{
$sel=($rows['id']==$sel_contact_order['brand_id'])? 'selected': ''; 

$contacts_brand_name = $rows['brand_name']; 
}	




$rows = data("select * from in_color where id = '".$sel_contact_order['color_id']."' and del_status='0' order by color_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['color_id'])? 'selected': ''; 
$contacts_color_name = ucfirst($r['color_name']); 
}	


$rows="";
$rows = data("select * from in_supply where id = '".$sel_contact_order['supply_id']."' and del_status='0' order by supply_name asc");
foreach($rows as $r)
{ 
$sel=($r['id']==$sel_contact_order['supply_id'])? 'selected': ''; 
$contacts_supply_name = ucfirst($r['supply_name']);
}	


$manu_detail_qry2 = "select manufacturer_name from in_manufacturer_details where id = '".$sel_contact_order['manufacturer_id']."' and frames_chk='1' and del_status='0'";

$manu_detail_res2 = imw_query($manu_detail_qry2);
$manu_detail_nums2 = imw_num_rows($manu_detail_res2);
if($manu_detail_nums2 > 0)
{	
	while($manu_detail_row2 = imw_fetch_array($manu_detail_res2))
	{
		$manufacturer_name = $manu_detail_row2['manufacturer_name'];
	}
}
        	  $pdfHTML.=
			   	'<tr>
					  <td>'.$sel_contact_order['upc_code'].'</td>
					  <td>'.$manufacturer_name.'</td>
					  <td>'.$contact_cat_name.'</td>
					  <td>'.$contacts_type_name.'</td>              
					  <td>'.$contacts_brand_name.'</td>              
					  <td>'.$sel_contact_order['item_name'].'</td>              
					  <td>'.$contacts_color_name.'</td>              
					  <td>'.$contacts_supply_name.'</td>
					  <td>'.getDateFormat($sel_contact_order['ordered']).'</td>
					  <td>'.getDateFormat($sel_contact_order['received']).'</td>
					  <td>'.getDateFormat($sel_contact_order['notified']).'</td>
					  <td>'.getDateFormat($sel_contact_order['dispensed']).'</td>
					  <td>'.getDateFormat($sel_contact_order['item_comment']).'</td>  
				</tr>';
		}
  $pdfHTML.= '</table><br />';
	}
	
$other_qry_check = imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id NOT IN(1,2,3) and del_status='0'");
	
	
	if(imw_num_rows($other_qry_check)>0)
	{			
	
	$pdfHTML.= '<table style="clear:both;" class="fl table_collapse table_cell_padd5" border="0" width="100%">
        <tr bgcolor="#f4f4f4">
          <td class="headingbg" colspan="13"><strong>Other</strong></td>
        </tr>        
		<tr>
          <td width="150"><strong>UPC</strong></td>
		  <td width="150"><strong>Manufacturer</strong></td>
          <td width="150"><strong>Vendor</strong></td>
          <td width="150"><strong>Name</strong></td>
          <td width="150"><strong>Wholesale Cost</strong></td>
          <td width="100"><strong>Discount</strong></td>
          <td width="100"><strong>Total</strong></td>
        </tr>';
		
		$other_qry=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and module_type_id NOT IN(1,2,3) and del_status='0'");
		
		while($other_qry_row=imw_fetch_array($other_qry))
		{
			
			$rows1="";			
			$rows1 = imw_query("select vendor_id from in_item where id = '".$other_qry_row['item_id']."'");
			$rowsrow = imw_fetch_assoc($rows1);
			$rowsrow1 = $rowsrow['vendor_id'];

			$rows2="";

			$rows2 = imw_query("select vendor_name from in_vendor_details where id = '".$rowsrow1."'");
			$rowsrow2 = imw_fetch_assoc($rows2);
						
		  $pdfHTML.='<tr>
          <td>'.$other_qry_row['upc_code'].'</td>
		  <td>'.$manufacName.'</td>
          <td>'.$rowsrow2['vendor_name'].'</td>
          <td>'.$other_qry_row['item_name'].'</td>              
          <td>'.$other_qry_row['price'].'</td>              
          <td>'.$other_qry_row['discount'].'</td>              
          <td>'.$other_qry_row['total_amount'].'</td>              
          </tr>';
		}		
  $pdfHTML.= '</table>';  
	}
  
  $pdfHTML.='</page>';	
  $pdfText = $css.$pdfHTML;
  
  file_put_contents('../../library/new_html2pdf/print_pos.html',$pdfText);
  
?>

<script>
var url = '<?php echo $GLOBALS['WEB_PATH'];?>/library/new_html2pdf/createPdf.php?op=l&file_name=../../library/new_html2pdf/print_pos';
window.location.href = url;
</script>