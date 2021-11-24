<?php 
require_once(dirname('__FILE__')."/../../../config/config.php");
require_once(dirname('__FILE__')."/../../../library/classes/functions.php");
$inputwidth=100;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Stock Printing</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<style>
#table_list, #table_list1 {
	border-collapse: collapse;
	border: 1px solid #D4D4D4;
}
#content_tab_print {
	float: left;
	display: none;
	width: 100%;
}
#tab_data_cat td {
	text-align: center;
}
.printing_upc, .printing_data{
	display:none;
}
</style>
<script>
function get_rec_cat()
{
	if(document.getElementById('type_optical_id').value!="")
	{
		var type=$('#type_optical_id').val();
		var manuf_id=$('#manufacturer_Id_Srch').val();
		var vendor=$('#opt_vendor_id').val();
		var brand=$('#opt_brand_id').val();
		var color=$('#color').val();
		var shape=$('#shape').val();
		var style=$('#style').val();
		var upc_name=$('#upc_name').val();
		var name_txt=$('#name_txt').val();
		//var from=document.getElementById('from').value;
		var price_frm=$('#price_frm').val();
		var price_to=$('#price_to').val();
		var item_qty=$('#item_qty').val();
		if(manuf_id<=0 || !manuf_id)
		{
			top.falert("Please select Manufacturer");
			$('#manufacturer_Id_Srch').focus();
			return false;	
		}
		
		$('#load_img').show();
		$.ajax({
			type:"POST",
			url:"stock_print_ajax.php",
			data:"id="+type+"&manuf_id="+manuf_id+"&vendor="+vendor+"&brand="+brand+"&color="+color+"&shape="+shape+"&style="+style+"&price_frm="+price_frm+"&price_to="+price_to+"&name_txt="+name_txt+"&upc_name="+upc_name+"&item_qty="+item_qty,
			success: function(msg)
			{
				$('#tab_data_cat').html(msg);
				if(msg)
				{
					document.getElementById('content_tab_print').style.display="block";	
					//BUTTONS
					var mainBtnArr = new Array();
					mainBtnArr[0] = new Array("frame","Print","top.main_iframe.admin_iframe.print_rec();");
					top.btn_show("admin",mainBtnArr);						
				}else{
					//BUTTONS
					var mainBtnArr = new Array();
					top.btn_show("admin",mainBtnArr);						
				}
			},
			complete: function(){
				$('#load_img').hide();
			}
			});
			
	}
	
}

function checlAll(obj){
	if($(obj).is(":checked")){
		$(".rowCheck").prop('checked', true);
	}else{
		$(".rowCheck").prop('checked', false);
	}
}
</script>
</head>
</html>
<br>
<html>
<head>

<link rel="stylesheet" href="../../../library/css/inv_css.css?<?php echo constant("cache_version"); ?>" />
<script src="../../../library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../../library/js/common.js?<?php echo constant("cache_version"); ?>"></script>
<script src="../../../library/dymo/DYMO.Label.Framework.latest.js?<?php echo constant("cache_version"); ?>"></script>
</head>

<body>
<div class="rec_con">
  <div class="listheading">Stock Printing</div>
  <form name="stock_srch_frm">
   <!-- <table>
      <tr>
        <td><label>Select Type</label></td>
        <td><select name="sel_cat" id="sel_cat" onChange="get_rec_cat()">
            <option value="">Choose Option</option>
            <?php
  $query=imw_query("select * from in_module_type");
   while($row=imw_fetch_array($query))
   {?>
            <option value="<?php echo $row['id'];?>"><?php echo ucfirst($row['module_type_name']);?></option>
            <?php }
   ?>
          </select></td>
      </tr>
    </table>-->
          <div>
           <table style="width:100%;margin-top:5px;">
            <tr class="table_collapse listheading">
            <td style="width:150px; text-align:center;">Type</td>
            <td style="width:150px; text-align:center;">Manufacturer</td>
            <td style="width:150px; text-align:center;">Vendor</td>
            <td id="srch_brand" style="width:150px; text-align:center;">Brand</td>
            <?php if($from=="style"){ ?>
            <td style="width:80px; text-align:center;">Color</td>
            <td style="width:80px; text-align:center;">Shape</td>
            <td style="width:80px; text-align:center;">Style</td>
            <td style="width:80px; text-align:center;">Price Range</td>
            <?php } ?>
           
            <td style="width:150px; text-align:center;">Qty</td>
            <td style="width:150px; text-align:center;">UPC</td>
            <td style="width:<?php if($from=="style") { echo "110px"; }else{ echo "80px"; } ?>; text-align:center;">Name</td>
            <td style="width:150px; text-align:center;" colspan="2">&nbsp;</td>
            </tr>               
            <tr>
            <td style="text-align:center;">
                <select name="type_optical_id" id="type_optical_id" style="width:98%;" onChange="javascript:get_type_manufacture1(this.value,'0');  get_brandFromVendor1(document.getElementById('opt_vendor_id').value,'0',this.value); show_brand(this.value); change_width(this.value);">
                      <?php  
					  if(!$search_id)$search_id=1;
					  $rowsType="";
                      $rowsType = data("select * from in_module_type order by module_type_name asc");
                      foreach($rowsType as $rsultType)
                      { 
                      if($rsultType['module_type_name']=="medicine" || $rsultType['module_type_name']=="supplies" || $rsultType['module_type_name']=="accessories"){ $style_upc="126";}else{$style_upc="90";}
                      ?>
                        <option value="<?php echo $rsultType['id']; ?>" <?php if($rsultType['id']==$search_id) { echo "selected"; }?>><?php echo ucfirst($rsultType['module_type_name']); ?></option>	
                <?php }	?>
                </select></td>
            <td style="text-align:center;">
                <select name="manufacturer_Id_Srch" id="manufacturer_Id_Srch" style="width:98%; " onChange="javascript:get_vendorFromManufacturer1(this.value,'0');">
                    <option value="0">Select Manufacturer</option>
                </select>
            </td>
            
            <td style="text-align:center;">
                <select name="opt_vendor_id" style="width:98%" id="opt_vendor_id" onChange="javascript:get_brandFromVendor1(this.value,'0',document.getElementById('type_optical_id').value);">
                    <option value="0">Select Vendor</option>
                    <?php $rows="";
                          $rows = data("select * from in_vendor_details where del_status='0' order by vendor_name asc");
                          foreach($rows as $r)
                          { ?>
                            <option value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['vendor_name']); ?></option>	
                    <?php }	?>
                </select></td>
            <td id="srch_brand_sel" style="text-align:center;">
                <select name="opt_brand_id"  id="opt_brand_id" style="width:98%;" >
                    <option value="0">Select Brand</option>
                    <?php $rows="";
                        if($_REQUEST['srch_id']==3 || $_REQUEST['type_optical_id']==3)
                        {
                            $rows = data("select id,brand_name as frame_source from in_contact_brand where del_status='0' order by brand_name asc");
                        }
                        else
                        {
                          $rows = data("select * from in_frame_sources where del_status='0' order by frame_source asc");
                         }
                          foreach($rows as $r)
                          { ?>
                            <option <?php if($_REQUEST['brand']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['frame_source']); ?></option>	
                    <?php }	?>
                </select></td>							
      
            <?php if($from=="style"){ ?>                    
            <td style="width:100px; text-align:center;">
            <select name="color"  id="color" style="width:98%;">
            <option value="0">Select Color</option>
            <?php 
            $rows="";
            $rows = data("select id,color_name as frame_color from in_frame_color where del_status='0' order by color_name asc");
            foreach($rows as $r)
            { ?>
            <option <?php if($_REQUEST['color']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['frame_color']); ?></option>	
            <?php }	?>
            </select>
            </td>
            <td style="width:100px; text-align:center;">
            <select name="shape"  id="shape" style="width:98%;">
            <option value="0">Select Shape</option>
            <?php 
            $rows="";
            $rows = data("select id,shape_name from in_frame_shapes where del_status='0' order by shape_name asc");
            foreach($rows as $r)
            { ?>
            <option <?php if($_REQUEST['shape']==$r['id']) { echo "selected"; } ?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['shape_name']); ?></option>	
            <?php }	?>
            </select>
            </td>
            <td style="width:100px; text-align:center;">
            <select name="style"  id="style" style="width:98%;">
            <option value="0">Select Style</option>
            <?php 
            $rows="";
            $rows = data("select id,style_name from in_frame_styles where del_status='0' order by style_name asc");
            foreach($rows as $r)
            { ?>
            <option <?php if($_REQUEST['style']==$r['id']) { echo "selected"; }?> value="<?php echo $r['id']; ?>"><?php echo ucfirst($r['style_name']); ?></option>	
            <?php }	?>
            </select>
            </td>
            <?php
                if($price_frm==""){
                    $price_frm="Min";
                }else{
                    $price_frm=$_REQUEST['price_frm'];
                }
                if($price_to==""){
                    $price_to="Max";
                }else{
                    $price_to=$_REQUEST['price_to'];
                }
            ?>
            <td style="width:140px; text-align:center;">
             <input type="text" name="price_frm" id="price_frm" value="<?php echo $price_frm;?>" style="width:40px;" onClick="clean_val('price_frm');" /> -                 <input type="text" name="price_to" id="price_to" value="<?php echo $price_to;?>" style="width:40px;" onClick="clean_val('price_to');"/>
            </td>
            <?php } ?>
            <td style="width:100px; text-align:center;">
            
            <select name="item_qty"  id="item_qty" style="width:98%;">
              <option value="0">All Items</option>
              <option value="1">Items have Qty.</option>
            </select></td>
            <td style="width:100px; text-align:center;">
           
            <input type="text" name="upc_name" id="upc_name" value="<?php echo $upcval;?>" style="width:90%;" />
            </td>
            <td style="width:40px; text-align:center;">
            <input type="text" name="name_txt" id="name_txt" value="<?php echo $name_txt;?>" style="width:98%" /></td>
            <td style="width:100px; text-align:center;" colspan="2" class="btn_cls">
            <input type="button" name="search_result" value="Search" onClick="return get_rec_cat();" /></td>
            </tr>
            </table>
        </div>
          
   <img src="../../../images/loading_image.gif" id="load_img" style="display:none;margin:auto;position:absolute;left:48%;top:50%;">
    <div id="content_tab_print">
      <div style="height:<?php echo $_SESSION['wn_height']-528?>px;overflow:scroll;overflow-x:hidden;margin:5px 0 0 0">
        <table id="table_list1" border="1" width="100%" style="margin: 0px 0 0 0;">
          <tbody id="tab_data_cat">
          </tbody>
        </table>
      </div>
    </div>
    <div style="width:98%; margin:0 auto;clear:both;padding-top:5px;">
		<label for="dymoPrinter">Select Printer</label>
		<select id="dymoPrinter"></select>
		
		<label for="dymoPaper" style="margin-left:20px;">Paper Size</label>
		<select id="dymoPaper">
			<!--<option value="PriceTag.label">Price Tag 22mm x 24mm</option>-->
			<option value="PriceTag1.label">Price Tag 25.4mm x 76.2mm</option>
			<!--<option value="Address.label">Address 28mm x 89mm</option>
			<option value="ExtraSmall_2UP.label">Extra Small (2-Up) 13mm x 25mm</option>-->
		</select>
	</div>
  </form>
</div>
<script>

/*Dymo Label Printer configuration*/
	
	/*
	function escapeXml(xmlStr){
        var result = xmlStr;
        var findReplace = [[/&/g, "&amp;"], [/</g, "&lt;"], [/>/g, "&gt;"], [/"/g, "&quot;"]];

        for(var i = 0; i < findReplace.length; ++i) 
            result = result.replace(findReplace[i][0], findReplace[i][1]);

        return result;
    }
	*/
	// stores loaded label info
	var label;
	
	// Printer's List
	var printersSelect = document.getElementById('dymoPrinter');
	// Label's List
	var labelSelected = document.getElementById('dymoPaper');
	
	// called when the document completly loaded
	// To load Dymo Printer
	function onload(){
		// loads all supported printers into a Select List 
		function loadPrinters()
		{
			var printers = dymo.label.framework.getLabelWriterPrinters();
			if (printers.length == 0)
			{
				alert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
				//return;
			}
	
			for (var i = 0; i < printers.length; ++i)
			{
				var printer = printers[i];
				var printerName = printer.name;
	
				var option = document.createElement('option');
				option.value = printerName;
				option.appendChild(document.createTextNode(printerName));
				printersSelect.appendChild(option);
			}
		}
		
		// load printers list on startup
		loadPrinters();
	};
	
	function loadLabelFromWeb(){
		
		// use jQuery API to load label
		$.ajax({
			url: top.WRP+"/library/dymo/"+labelSelected.value,
			async:false,
			success:function(data){
				label = dymo.label.framework.openLabelXml(data);
			}
		});
	}
	
	/*Print Labels for selected items*/
	function printLabel(){
		
		try{
			// Load label Structure
			loadLabelFromWeb();
			
			if (!label){
				top.falert("Load label before printing");
				return;
			}
			if(printersSelect.value==""){
				top.falert("No DYMO LabelWriter printers are installed. Install DYMO LabelWriter printers.");
				return;
			}
			
			// set data using LabelSet and text markup
			//var labelSet = new dymo.label.framework.LabelSetRecord();
			var labelSet = new dymo.label.framework.LabelSetBuilder();
			var record; 
			//alert(label);
			/*Getting Data from Tabele*/
			var selectedRecords = document.getElementsByName('selectedrecords[]');
			var selectedUPC = new Array();
			$.each(selectedRecords, function(i,obj){
				if(obj.checked===true){
					printCount = 0;
					
					upc_data = $(".printing_upc").get(i).innerHTML;
					print_data = $(".printing_data").get(i).innerHTML;
					
					if(labelSelected.value === 'ExtraSmall_2UP.label'){
						print_data = print_data.replace(/-/g, "<br/>");
					}
					
					print_data = print_data.replace(/<br>/g, "<br/>");
					printCount = $(".labelCount").get(i).value;
					printCount = printCount.replace(/[^\d]/g, "");
					
					if(printCount==""){printCount=0;}
					printCount = parseInt(printCount);
					for(i=printCount; i>0; i--){
						/*Add Data to Dymo LabelSet*/
						record = labelSet.addRecord();
						record.setText('BARCODE', upc_data);
						record.setTextMarkup('TEXT', print_data);
						/*End Add Data to Dymo LabelSet*/
					}
				}
			});
			/*End Getting Data from Table*/
			
			label.print(printersSelect.value, null, labelSet.toString());
			delete labelSet;
		}
		catch(e){
			alert(e.message || e);
		}
	}
	
	/*printlabel.onclick = function(){
			
		try{
			if (!label){
				alert("Load label before printing");
				return;
			}
			
			//alert("Test");
			// set data using LabelSet and text markup
			//var labelSet = new dymo.label.framework.LabelSetRecord();
			var labelSet = new dymo.label.framework.LabelSetBuilder();
			//alert("Testin 1");
			var record = labelSet.addRecord();
			//var record = labelSet;
			
			record.setText('BARCODE', '963258');
			record.setTextMarkup('TEXT', 'Testing Label Printing12');
			label.print(printersSelect.value, null, labelSet.toString());
			delete labelSet;
		}
		catch(e){
			alert(e.message || e);
		}
		
	}
	*/
	
// register onload event
$(window).on('load', function(){
	onload();
});
/*End Dymo Label Printer Configuration*/

function print_rec()
{
		printLabel();
		return;
		var len=document.getElementsByName('selectedrecords[]').length;
		var upc_code=document.getElementsByName('upc_code[]');
		var label_cnt=document.getElementsByName('label[]').length;
		var label=document.getElementsByName('label[]');
		var data="";
		var item1=document.getElementsByName('selectedrecords[]');
		var module=document.getElementById('type_optical_id').value;
		for(var i=0;i<len;i++)
		{
			if(item1[i].checked==true)
			{
				for(var p=0;p<label[i].value;p++)
				data+=(item1[i].value)+",";
			}
		}
		if(data=="")
		{
			top.falert("Please Select record(s) to print");
		}
		$.ajax({
		type:"POST",
		url:"stock_print_ajax.php",
		data:"upc_code="+data+"&module="+module,
		success: function(msg)
		{
			if(msg==1)
			{
				var url='<?php echo $GLOBALS['WEB_PATH']?>/library/new_html2pdf/createPdf.php?op=p&file_name=print_data&mod=stock_print';
				top.WindowDialog.closeAll();
				var Add_new_popup=top.WindowDialog.open('Add_new_popup',url);
			}
		}
		});
}


function show_brand(get_type_id)
{
	if(get_type_id=="5" || get_type_id=="6" || get_type_id=="7"  || get_type_id=="2")
	{
		//$("#srch_brand").css('display','none');
		//$("#srch_brand_sel").css('display','none');
		//$("#res_brand").css('display','none');
		//$(".res_brand_dis").css('display','none');
		$("#srch_brand").css('color','#CCC');
		$("#opt_brand_id").attr('disabled',true);
	}
	else
	{
		//$("#srch_brand").css('display','');
		//$("#srch_brand_sel").css('display','');
		//$("#res_brand").css('display','');
		//$(".res_brand_dis").css('display','');
		$("#srch_brand").css('color','#000');
		$("#opt_brand_id").attr('disabled',false);
	}
}

function change_width(wid)
{
	if(wid=="7" || wid=="6" || wid=="5" || wid=="2")
	{
		$("#type_optical_id").css('width','98%');
		$("#manufacturer_Id_Srch").css('width','98%');
		$("#opt_vendor_id").css('width','98%');
	}
	else
	{
		$("#type_optical_id").css('width','98%');
		$("#manufacturer_Id_Srch").css('width','98%');
		$("#opt_vendor_id").css('width','98%');
	}
}

function clean_val(id){
	if(document.getElementById(id).value=="Min"){
		document.getElementById(id).value="";
	}
	if(document.getElementById(id).value=="Max"){
		document.getElementById(id).value="";
	}
}

$(document).ready(function(e) { 
	var type_id =$("#type_optical_id").val();
	var srch_id = document.getElementById('type_optical_id').value;
	var vendor_id ="<?php echo $opt_vendor_id; ?>";
	var brand_id ="<?php echo $opt_brand_id; ?>";
	var manufacture_id ="<?php echo $manufacturer_Id_Srch; ?>";
	get_type_manufacture1(type_id,manufacture_id);
	get_vendorFromManufacturer1(manufacture_id,vendor_id);
	get_brandFromVendor1(vendor_id,brand_id,srch_id);
	show_brand(srch_id);
	change_width(srch_id);
	
	//BUTTONS
	var mainBtnArr = new Array();
	top.btn_show("admin",mainBtnArr);		
  });				
				
</script>
</body>
</html>
