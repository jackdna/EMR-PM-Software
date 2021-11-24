<?php 
/*
File: supplies.php
Coded in PHP7
Purpose: View/Edit Supplies
Access Type: Direct access
*/
require_once("../../../config/sql_conf.php"); ?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Optical</title>
<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
</head>
<body>
	<div>
        <div class="module_heading">Supplies</div>
        <div class="module_border">
        	<form action=""  name="material_form" method="post">	
                <table class="table_collapse">
                    <tr>
                        <td width="168">
                        <select style="width:160px;">
                        <?php $sql="select manufacturer_name from in_manufacturer_details where del_status != '2'";
                            $res = imw_query($sql);
                            while($row = imw_fetch_array($res)) {
							?>
                            <option value="<?php echo $row['manufacturer_name']; ?>"><?php echo $row['manufacturer_name']; ?> </option>
                            <?php } ?>
                        </select>
                        </td>
                        <td width="167" class="module_label">Manufacturer</td>
                        <td width="110"><input type="text" /></td>
                        <td width="109" class="module_label">UPC</td>
                        <td width="55" align="right"><input type="checkbox" /></td>
                        <td width="67" class="module_label">History</td>
                    </tr>
                    <tr>
                        <td><select style="width:160px;">
                            <option>Frame Type</option>
                            <option>Frame Type</option>
                            <option>Frame Type</option>
                            <option>Frame Type</option>
                            <option>Frame Type</option>                
                        </select></td>
                        <td class="module_label">Type</td>
                        <td width="110"><input type="text" /></td>
                         <td class="module_label">Name</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                    <tr>
                    <td colspan="6">
                    <div class="module_border mt15">
                        <table class="table_collapse">
                          <tr>
                              <td width="185" style="padding-bottom:10px;">
                              <select style="width:155px;">
                                  <?php $sql="select vendor_name from in_vendor_details where del_status != '2'";
                            $res = imw_query($sql);
                            while($row = imw_fetch_array($res)) {
							?>
                            <option value="<?php echo $row['vendor_name']; ?>"><?php echo $row['vendor_name']; ?> </option>
                            <?php } ?>
                              </select></td>
                              <td width="140" style="padding-bottom:10px;" class="module_label">Vendor</td>
                              <td width="100" style="padding-bottom:10px;">&nbsp;</td>
                              <td width="168" style="padding-bottom:10px;"><input type="text" /></td>
                              <td width="198" style="padding-bottom:10px;" class="module_label">Description</td>
                              <td width="87"  style="padding-bottom:10px;"></td>
                              <td width="100"  style="padding-bottom:10px;text-align:right" ><img alt="" src="" width="30" height="20" /></td>
                          </tr>
                          <tr>
                          	<td colspan="2">
                            	<div class="module_border">
                                	<table class="table_collapse">
                                        <tr>
                                          <td valign="top" width="70">
                                            <select style="width:65px;">
                                                <?php $sql="select size_name from in_supplies_size where del_status != '2'";
											$res = imw_query($sql);
											while($row = imw_fetch_array($res)) {
											?>
											<option value="<?php echo $row['size_name']; ?>"><?php echo $row['size_name']; ?> </option>
											<?php } ?>
                                            </select> 
                                         </td>
                                          <td valign="top" width="40" class="module_label">Size</td>
                                           <td valign="top" width="70">
                                            <select style="width:65px;" >
                                            <?php $sql="select measurment_name from in_supplies_measurment where del_status != '2'";
											$res = imw_query($sql);
											while($row = imw_fetch_array($res)) {
											?>
											<option value="<?php echo $row['measurment_name']; ?>"><?php echo $row['measurment_name']; ?> </option>
											<?php } ?>
                                            </select>
                                          </td>
                                          <td valign="top" class="module_label">Measurement</td>
                                          
                                         </tr>
                                        <tr>
                                          <td  valign="top" width="70">
                                            <select style="width:65px;"  >
                                            <option>Small</option>
                                            <option>Medium</option>
                                            <option>Large</option>
                                          </select> 
                                         </td>
                                          <td  valign="top" width="40" class="module_label">Size</td>
                                           <td  valign="top" width="70">
                                            <input type="text"  style="width:58px;" />
                                           </td>
                                          <td valign="top" class="module_label"><span style="width:83px;">Other</span></td>
                                       </tr>
                                    </table>
                                </div>
                            </td>
                            <td width="100" style="padding-bottom:10px;">&nbsp;</td>
                            <td colspan="4">
                            	<table class="table_collapse">
                                    <tr>
                                    	<td colspan="4" valign="top"  class="module_label"><input type="checkbox" />&nbsp;Hazardous</td>
                                     </tr>
                                    <tr>
                                    	<td valign="top" style="width:60px;"><input type="text" style="width:50px;"/></td>
                                      	<td  class="module_label" >Quantity on Hand</td>
                                      	<td valign="top" style="width:50px;"><input type="text" style="width:50px;"/></td>
                                      	<td class="module_label"> &nbsp;Amount</td>	
                                   </tr>
                                </table>
                            </td>
                          </tr>
                          <tr>
                            <td style="width:70px;padding-top:10px;"><input type="text" /></td>
                            <td colspan="6" style="width:630px;" class="module_label">Wholesale Cost</td>	
                        </tr>
                        <tr>
                            <td style="width:70px;"><input type="text" /></td>
                            <td colspan="6" style="width:630px;" class="module_label">Retail Price</td>	
                          </tr>
                        <tr>
                            <td style="width:70px;"><input type="text" /></td>
                            <td style="width:70px;" class="module_label">Discount </td>	
                            <td colspan="5"><input type="text" value="08-27-2013" /></td>	
                        
                         </tr> 
                        </table>
                    </div>
                    </td>
                    </tr>
                         
                    </table>   
               		           
                    <div class="btn_cls">
                        <input type="submit" name="save" value="Save" />                        
                        <input type="button" name="new" value="Cancel" onClick="javascript:refrsh();"/>
                   	</div> 
         	 </form>
        </div>
    </div>
</body>
</html>