<?php 
/*
File: frame_main.php
Coded in PHP7
Purpose: Add New Frame
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

<link rel="stylesheet" href="../../../library/js/themes/base/jquery.ui.all.css?<?php echo constant("cache_version"); ?>">
<script src="../../../library/js/ui/jquery.ui.core.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../../library/js/ui/jquery.ui.widget.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../../library/js/ui/jquery.ui.datepicker.js?<?php echo constant("cache_version"); ?>"></script>
<script>
$(function() 
{
	var cyear = new Date().getFullYear();		
	$( "#datepicker" ).datepicker({
		changeMonth: true,
		changeYear: true,
		dateFormat: 'mm-dd-yy'
	});
});
</script>

<script type="text/javascript">
function upc(upc_code)
{
	var ucode = $.trim(upc_code.value);
	var dataString = 'action=managestock&upc='+ucode;
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			 var dataArr = $.parseJSON(response);
			 if(dataArr!="")
			 {
				 $.each(dataArr, function(i, item) 
				 {
					$("#id").val(item.id);
					
					if(item.stock_image!="")
					{
						$("#item_image img").attr("src","../../../images/frame_stock/"+item.stock_image);
					}
					else
					{
						$("#item_image img").attr("src","../../../images/no_product_image.jpg");	
					}
					
					$("#retail_price").val(item.retail_price);
					$("#manufacturer").val(item.manufacturer_id);
					$("#upc_name").val(item.upc_code);
					$("#module_type").val(item.module_type_id);
					$("#name").val(item.name);
					$("#vendor").val(item.vendor_id);
					$("#brand").val(item.brand_id);
					$("#frame_style").val(item.frame_style);
					$("#a").val(item.a);
					$("#b").val(item.b);
					$("#ed").val(item.ed);
					$("#bridge").val(item.bridge);
					$("#color_code").val(item.color_code);
					$("#color").val(item.color);
					$("#retail_price").val(item.retail_price);
					$("#wholesale_cost").val(item.wholesale_cost);				
					$("#qty_on_hand").val(item.qty_on_hand);
					$("#amount").val(item.amount);
					$("#discount").val(item.discount);
					$("#disc_date").val(item.disc_date);
					$("#gender").val(item.gender);
					$("#style").val(item.style);			
					$("#datepicker").val(item.discount_till);			
				 });
			 }
			 else
			 {
					
			 }
		}
	}); 
}
</script>    

</head>
<body>
<?php 
if(isset($_REQUEST['save']))
{
	extract($_POST);
	frame_stock($id,$manufacturer,$upc_name,$module_type,$name,$vendor,$brand,$frame_style,$a,$b,$ed,$bridge,$color_code,$color,$wholesale_cost,$retail_price,$qty_on_hand,$amount,$discount,$disc_date,$gender,$style);
}
?>
	<div>
        <div class="module_heading">Inventory Frames</div>
        <div class="module_border">
        <form action="" name="frame_form" id="stock_form" method="post" enctype="multipart/form-data">        
        <input type="hidden" name="id" id="id" />
           <table class="table_collapse table_cell_padd5">
            <tr>
            	<td width="175">
                <select style="width:165px;" name="manufacturer" id="manufacturer">
                    	<?php $rows="";
                              $rows = data("select * from in_manufacturer_details where del_status='0' order by manufacturer_name asc");
                              foreach($rows as $r)
                              {
						?>
                                <option value="<?php echo $r['id']; ?>"><?php echo $r['manufacturer_name']; ?></option>	
                        <?php } ?>
                </select>
                </td>
                <td width="273" class="module_label">Manufacturer</td>
            	<td width="155"><input type="text" onChange="javascript:upc(this)" name="upc_name" id="upc_name" /></td>
                <td width="164" class="module_label">UPC</td>
                <td width="55" align="center">&nbsp;</td>
                <td width="162" class="module_label">HX</td>
            </tr>
            <tr>
            	<td>
                <select style="width:165px;" name="module_type" id="module_type">
					<?php $rows="";
                          $rows = data("select * from in_module_type where del_status='0' order by module_type_name asc");
                          foreach($rows as $r)
                          { ?>
                            <option <?php if($r['module_type_name']=="frame"){ echo "selected"; } ?> value="<?php echo $r['id']; ?>"><?php echo $r['module_type_name']; ?></option>	
                    <?php }	?>
                </select>
                </td>
                <td class="module_label">Type</td>
            	<td><input type="text" name="name" id="name" /></td>
                <td class="module_label">Name</td>
                <td colspan="2">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6">
                	<div class="module_border mt5">
                        <table class="table_collapse table_cell_padd5">
                       	 <tr>
                          <td width="167" valign="top">
                            <select style="width:159px;" name="vendor" id="vendor">	
							<?php $rows="";
                                  $rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
                                  foreach($rows as $r)
                                  { ?>
                                    <option value="<?php echo $r['id']; ?>"><?php echo $r['vendor_name']; ?></option>	
                            <?php }	?>
                            </select>
                            </td>
                            <td width="310" height="30" valign="top" class="module_label">Vendor</td>
                            <td colspan="2" rowspan="5">
                            	<div class="module_border">
                                	<table class="table_collapse table_cell_padd5">
                                    	<tr>
                                        	<td align="center"><div id="item_image"><img alt="" src="../../../images/no_product_image.jpg" width="280" height="154" /></div></td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                          <td width="167" valign="top">
                            <select style="width:159px;" name="brand" id="brand">
                            <?php  $rows="";
                            $rows = data("select * from in_frame_sources where del_status='0' order by frame_source asc");
                            foreach($rows as $r)
                            { ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo $r['frame_source']; ?></option>
                            <?php }	?>
                            </select>
                            </td>
                          <td height="31" valign="top" class="module_label">Brand</td>
                        </tr>
                        <tr>
                          <td valign="top"><input value="TYPABLE" name="frame_style" id="frame_style" type="text" /></td>
                          <td valign="top" class="module_label">Frame Style</td>
                        </tr>
                        <tr>
                          <td width="167" valign="top" class="module_label"><input name="a" id="a"  style="width:20px" type="text" value="" />&nbsp;A&nbsp;&nbsp;&nbsp;<input name="b" id="b" style="width:20px" type="text" value="" />&nbsp;B&nbsp;&nbsp;&nbsp;<input style="width:20px" type="text" name="ed" id="ed" value="" />&nbsp;ED    
                        </td>
                          <td valign="top" class="module_label">&nbsp;<input name="bridge" id="bridge" style="width:20px" type="text" value="" />&nbsp;Bridge</td>
                        </tr>
                        <tr>
                          <td width="167" valign="top" class="module_label"><input name="color_code" id="color_code" style="width:60px" type="text" /> 
                          Color Code</td>
                          <td width="310" valign="top" class="module_label"><span class="l">
                            <select name="color" id="color" style="width:88px;">
                            <?php  $rows="";
                            $rows = data("select * from in_frame_color where del_status='0' order by color_name asc");
                            foreach($rows as $r)
                            { ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo $r['color_name']; ?></option>	
                            <?php }	?>                        
                            </select>
                          </span>&nbsp;Color</td>
                          
                        </tr>
                        <tr>
                          <td colspan="2" valign="top" class="module_label"><input name="wholesale_cost" id="wholesale_cost" style="width:60px" type="text" /> 
                            Wholesale Cost</td>
                          <td colspan="2" valign="top" align="right">
                          <!--<input style="margin-left:30px;" type="submit" value="CAPTURE" /> -->
                          <input type="file" name="file" style="margin-right:10px;" />
                          </td>
                        </tr>
                        <tr>
                          <td valign="top" class="module_label"><input name="retail_price" id="retail_price" style="width:60px" type="text" />
                            Retail Price
                              
                          </td>
                          <td colspan="2" align="right" valign="top" class="module_label"><input name="qty_on_hand" id="qty_on_hand" style="width:60px;" type="text" />
                          Quantity on hand&nbsp;</td>
                          <td width="250" align="left" valign="top" class="frame_checks"><span class="module_label">
                            <input name="amount" id="amount" style="width:60px;" type="text" />Amount</span></td>
                          </tr>
                        <tr>
                         <td valign="top" class="module_label"><input name="discount" id="discount" style="width:60px" type="text" /> 
                            &nbsp;Discount</td>
                         <td valign="top" class="module_label"><input id="datepicker" style="width:80px" name="disc_date" value="" type="text" />&nbsp;Discount Till</td>
                         <td width="245" valign="top" class="module_label">
                            <select name="gender" id="gender" style="width:88px;">
                              <option value="men">Men</option>
                              <option value="women">Women</option>
                              <option value="boy">Boy</option>
                              <option value="girl">Girl</option>
                              <option value="unisex">Unisex</option>
                            </select>
                          Gender </td>
                          <td valign="top" class="module_label">
                            <select name="style" id="style" style="width:88px;">
                              <option value="1">Plastic</option>
                              <option value="2">Metal</option>
                              <option value="3">Rimless</option>
                              <option value="4">Frameecees</option>
                              <option value="5">Plastic Safety</option>
                              <option value="5">Metal Safety</option>
                              <option value="6">Other</option>
                            </select>
                          Style</td>
                        </tr>                   
                        </table>
                    </div>
                </td>
             </tr>           
            </table>
            
             <div class="btn_cls">
                <input type="submit" name="save" value="Save" />                        
                <input type="button" name="new" value="Cancel" onClick="javascript:refrsh();" />
            </div> 
            
		</form>
	</div>
	</div>
</body>
</html>