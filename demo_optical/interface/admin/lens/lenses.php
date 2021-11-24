<?php
/*
File: lenses.php
Coded in PHP7
Purpose: Add/Edit Lense
Access Type: Direct access
*/
require_once("../../../config/sql_conf.php");
require_once("../../../library/classes/functions.php"); 
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
</head>
<body>
<div class="lenses_div">
   <h3 align="center" style="padding:0 0 10px 0;">Lenses</h3>
        <div class="lenses_content">
        	<form action="">	
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;" class="top_table">
            <tr>
            	<td width="190">
                <select>
  						<?php $rows="";
                              $rows = data("select * from in_manufacturer_details where del_status='0' order by manufacturer_name asc");
                              foreach($rows as $r)
                              { ?>
                                <option value="<?php echo $r['manufacturer_name']; ?>"><?php echo $r['manufacturer_name']; ?></option>	
                        <?php }	?>
                </select>
                </td>
                <td width="106" class="module_label">Manufacturer</td>
            	<td width="170"><input type="text" /></td>
                <td width="68" class="module_label">UPC</td>
                <td width="38" align="center"><input type="checkbox" /></td>
                <td width="116" class="module_label">History</td>
            </tr>
            <tr>
            	<td><select>
                <option>Frame</option>
                <option>Lenses</option>
                <option>Contacts</option>
                <option>Materials</option>
                <option>Supplies</option>                
                </select></td>
                <td class="module_label">Type</td>
            	<td><input type="text" /></td>
                <td class="module_label">Name</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            </table>
            
            <div class="inner_box">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse; " class="top_table">
            <tr>
            	<td colspan="2" align="left" valign="top" >
                    <select name="manufacture">
                              <?php  $rows="";
                              $rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
                              foreach($rows as $r)
                              { ?>
                                <option value="<?php echo $r['vendor_name']; ?>"><?php echo $r['vendor_name']; ?></option>	
                        <?php }	?>
                    </select>
                </td>
                <td width="128" align="left" valign="top" class="module_label">
                   	Vendor
                </td>
                <td rowspan="5" colspan="3" align="left">
                	<table width="100%" cellpadding="0" cellspacing="0" style=" border:1px solid #ccc;">
                    	<tr>
                        	<td align="right" colspan="4" class="module_label" >
                            	Lens LIMITS
                            </td>
                            <td width="164" align="right">
                                <img src="" alt="picture" width="30" height="20"/>
                            </td>
                        </tr>
                        <tr>
                        	<td width="51" align="right">+</td>
                        	<td width="64" align="center"><input type="text" class="px40"  /></td>
                            <td width="22" align="right">-</td>
                            <td width="50" align="center"><input type="text" class="px40"  /></td>
                            <td colspan="2" align="left" class="module_label">Sphere</td>
                        </tr>
                        <tr>
                        	<td height="39" align="right">+</td>
                            <td width="64" align="center"><input type="text" class="px40"  /></td>
                             <td width="22" align="right">-</td>
                            <td  align="center"><input type="text" class="px40"  /></td>
                             <td width="164" align="left" class="module_label">Cylindep</td>
                            
                        </tr>
                        <tr>
                         	<td  align="center"><input type="text" class="px40"  /></td>
                        	<td colspan="2" align="left" class="module_label">Minimum Segment</td>
                            <td  align="center"><input type="text" class="px40"  /></td>
                            <td width="164" align="left" class="module_label">Diameter&nbsp;&nbsp;<input type="text" class="px40"  />&nbsp;th</td>
                        </tr>
                        <tr>
                        	
                          	<td align="right" class="module_label"><input type="checkbox"  />&nbsp;R</td>
                          	<td align="left" class="module_label"><input type="checkbox"  />&nbsp;L</td>
                            <td colspan="2" align="right" >
                            <select class="px60" name="supply2" >
                              <option value="demo">Finish</option>
                              <option value="demo">Semi-Finished</option>
                              <option value="demo">Outside Lab</option>
                              
                            </select>
                            </td>
                            <td align="left" class="module_label">Finish Type</td>
                        </tr>
                        
                    </table>
                	
                </td>
                
            </tr>
            <tr>
            	<td colspan="2" align="left" valign="top">
                    <select name="manufacturer">
                <?php 
					  $rows="";
					  $rows = data("select * from in_lens_type where del_status='0' order by type_name asc");
					  foreach($rows as $r)
					  {  ?>
						<option value="<?php echo $r['type_name']; ?>"><?php echo $r['type_name']; ?></option>	
				<?php }	 ?>
                    
				</select>
                </td>
                <td width="128" align="left" valign="top" class="module_label">Type</td>
            </tr>
            <tr>
                <td colspan="2" align="left" valign="top" >
                    
                    <select name="class_pattern">
						<?php 
                              $rows="";
                              $rows = data("select * from in_lens_material where del_status='0' order by material_name asc");
                              foreach($rows as $r)
                              { ?>
                                <option value="<?php echo $r['material_name']; ?>"><?php echo $r['material_name']; ?></option>	
                        <?php }	?>
                    </select>
                    
                </td>
                <td width="128" align="left" valign="top" class="module_label">Material</td>
               
            </tr>
            <tr>
                <td colspan="2" align="left" valign="top">
                    <select name="type">
       			<?php 
					  $rows="";
					  $rows = data("select * from in_lens_ar where del_status='0' order by ar_name asc");
					  foreach($rows as $r)
					  {  ?>
						<option value="<?php echo $r['ar_name']; ?>"><?php echo $r['ar_name']; ?></option>	
				<?php }	 ?>                    </select>
                </td>
                <td width="128" align="left" valign="top" class="module_label">AIR</td>
                
            </tr>
            <tr>
                <td colspan="2" align="left" valign="top" >
                    <select name="supply" >
				<?php 
					  $rows="";
					  $rows = data("select * from in_lens_transition where del_status='0' order by transition_name asc");
					  foreach($rows as $r)
					  {  ?>
						<option value="<?php echo $r['transition_name']; ?>"><?php echo $r['transition_name']; ?></option>	
				<?php }	 ?>   
                    </select>
                </td>
                <td width="128" align="left" valign="top" class="module_label">Transition</td>
                 <td width="9" colspan="3" align="center">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="left" valign="top">
                    <select name="supply" >
                <?php 
					  $rows="";
					  $rows = data("select * from in_lens_polarized where del_status='0' order by polarized_name asc");
					  foreach($rows as $r)
					  {  ?>
						<option value="<?php echo $r['polarized_name']; ?>"><?php echo $r['polarized_name']; ?></option>
				<?php }	 ?>  
                    </select>
                </td>
                <td width="128" align="left" valign="top" class="module_label">Polariced</td>
                 <td colspan="3" align="center">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" align="left" valign="top" >
                    <select name="supply">
                       <?php 
					  $rows="";
					  $rows = data("select * from in_lens_tint where del_status='0' order by tint_type  asc");
					  foreach($rows as $r)
					  {  ?>
						<option value="<?php echo $r['tint_type']; ?>"><?php echo $r['tint_type']; ?></option>
				<?php }	 ?> 
                    </select>
                </td>
                <td width="128" align="left" valign="top" class="module_label">Tnt</td>
                <td width="23" align="left">&nbsp;</td>
                <td width="193" align="left" class="module_label"><input type="text" class="px40"  />&nbsp;Quantity on Hand</td>
                <td width="145" align="left" class="module_label"><input type="text" class="px40"  />&nbsp;Amount</td>
            </tr>
            <tr>
                <td align="left" valign="top" class="module_label">&nbsp;<input type="checkbox"  />&nbsp;UV400</td>
                 <td colspan="2" align="left" valign="top" class="module_label"><input type="checkbox"  />&nbsp;PGX</td>
                 <td colspan="3" align="left">&nbsp;</td>
                 
            </tr>
            <tr>
                <td align="left" valign="top" ><input class="px80" type="text" name="Wholesale2" value="" /></td>
                 <td colspan="2" align="left" valign="top" class="module_label">Wholesale Cost</td>
                 <td colspan="2" align="right"><select class="px100" name="supply3" >
                   <option value="demo">Drop</option>
                   <option value="demo">Demo</option>
                   <option value="demo">Demo</option>
                   <option value="demo">Demo</option>
                 </select></td>
                 <td align="left" class="module_label">Labs</td>
            </tr>
             <tr>
                <td align="left"><input type="text" name="Wholesale" value="" class="px80"/>
                    
                </td>
                <td colspan="2" align="left"  valign="top" class="module_label">Retail Price</td>
                <td rowspan="2" colspan="3" align="center"><input type="button" name="submit2" value="Itemized" /></td>
            </tr>  
            <tr>
                <td width="90" align="left" valign="top"><input type="text" class="px80"  /></td>
                <td width="88" valign="top" class="module_label">Discount</td>
                <td width="128" align="left" ><input type="text" class="px80" value="12-09-1987" /></td>
            </tr>  
            <tr>
            	<td colspan="2">&nbsp;</td>
                <td align="right">
                    <input type="submit" name="submit" value="Save" />
                </td>
                <td colspan="2" align="center">
                    <input type="submit" name="reset" value="Cancel" />
                </td>
                <td>&nbsp;</td>
            </tr>                                               
        	</table>
      	 </div>
         </form>
        </div>
    </div>
</body>
</html>