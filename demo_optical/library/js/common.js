function product_history(item_id){
	if(item_id!=''){
		var winTop=window.screen.availHeight;
		winTop = (winTop/2)-190;
		var winWidth = window.screen.availWidth;
		winWidth = (winWidth/2)-390;
		
		top.WindowDialog.closeAll();
		var vv=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/admin/product_history.php?item_id='+item_id,'product_history_pop','width=850,height=325,left='+winWidth+',scrollbars=no,top='+winTop);
		vv.focus();
	}else{
		top.falert('Item history does not exists.');
	}
}
var get_details_by_upc_data_arr = "";
function get_details_by_upc(code,row_num,action, vision)
{
	if(typeof(row_num)=='undefined' || row_num=='')
	{
		var row_num = 1;
	}
	if(typeof(action)=='undefined' || action==''){
		var action = 'managestock';
	}
	
	var ucode = (typeof(code) == "object" )? $.trim(code.value): code;
	
	if(ucode===""){return false;}
	var dataString = 'action='+action+'&code='+ucode;
	$.ajax({
		type: "POST",
		url: top.WRP+"/interface/patient_interface/ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{	
			 var dataArr = (response.trim()!="")?$.parseJSON(response):{};
			 get_details_by_upc_data_arr = dataArr;
			 call_back(dataArr,row_num,vision);
			 calculate_all();
		}
	}); 
}
function call_back(dataArr,row_num,vision){
	if(dataArr!="" && typeof(dataArr[0])!='undefined'){
		
		/*StockData for Frames*/
		var stockData = {};
		if( typeof(dataArr.stockData) != 'undefined' ){
			stockData = dataArr.stockData;
			delete dataArr.stockData;
		}
		/*End StockData for Frames*/
		
		 $.each(dataArr, function(i, item) 
		 {
			$("#dispUPCF").html(item.upc_code);
			$("#dispNameF").html(item.name);
			if(item.module_type_id == '1' || item.module_type_id == '3'){
				get_prac_code_text(item.item_prac_code,'dispPCodeF', 'frm_cont', item.module_type_id);
			}
			if(typeof(item.retail_price)!='undefined'){
				$("#dispPriceF").val(item.retail_price);
			}
			var cur_dateDisp = $("#cur_date").val();
			var disscDisp=item.discount;
			if(cur_dateDisp>item.discount_till && item.discount_till!="0000-00-00"){
				disscDisp = 0;
			}
			$("#dispDiscF").val(disscDisp);
			$("#dispQtyF").val(item.qty);
			
			total = 0;
			var dissc=item.discount;
			if(typeof(item.retail_price)!='undefined'){
				$("#price_"+row_num).val(item.retail_price);
				$("#rtl_price_"+row_num).val(item.retail_price);
				$("#allowed_"+row_num).val(item.retail_price);
				$("#price_hidden_"+row_num).val(item.retail_price);
				if(item.module_type_id == '3')
				{
					//$("#qty_right_"+row_num).val('0');
					total = cal_discount((item.retail_price*1), dissc);
					total = total*1;
				}
				else
				{
					total = cal_discount((item.retail_price*1), dissc);
				}
			}
			if(typeof(item.price)!='undefined'){
				$("#price_"+row_num).val(item.price);
				$("#allowed_"+row_num).val(item.price);
				total = cal_discount((item.price*item.qty), dissc);
			}
			$("#dispTotalF").val(total);
			$("#dispCommentF").val(item.item_comment);
			/**/
			/**/
			if(item.module_type_id == '3' && item.trial_chk=='1')
			{
				$("#trial_"+row_num).val('1');
				$("#trial_chk_"+row_num).prop('checked', true);
				$("#price_"+row_num).val('0.00');	item.retail_price = "0.00";
				$("#allowed_"+row_num).val('0.00');
				$("#discount_"+row_num).val('0.00');	item.discount = "0.00";
				$("#total_amount_"+row_num).val('0.00');
			}
			else if(item.module_type_id == '3'){
				$("#trial_chk_"+row_num).prop('checked', false);
			}
			
			if(item.module_type_id=='3'){
				
				if( typeof(row_num) === 'string' && row_num.slice(-2) === 'os' ){
					var temnpRowNum = row_num.replace('_os', '');
					$("#cl_sphere_min_os_"+temnpRowNum).val(item.sphere_positive);
					$("#cl_sphere_max_os_"+temnpRowNum).val(item.sphere_positive_max);
					$("#cl_cyl_min_os_"+temnpRowNum).val(item.cylindep_positive);
					$("#cl_cyl_max_os_"+temnpRowNum).val(item.cylindep_positive_max);
					$("#cl_axis_min_os_"+temnpRowNum).val(item.axis);
					$('#cl_bc_min_os_'+temnpRowNum).val(item.bc);
					$('#cl_dia_min_os_'+temnpRowNum).val(item.diameter);
				}
				else{
					$("#cl_sphere_min_"+row_num).val(item.sphere_positive);
					$("#cl_sphere_max_"+row_num).val(item.sphere_positive_max);
					$("#cl_cyl_min_"+row_num).val(item.cylindep_positive);
					$("#cl_cyl_max_"+row_num).val(item.cylindep_positive_max);
					$("#cl_axis_min_"+row_num).val(item.axis);
					$('#cl_bc_min_'+row_num).val(item.bc);
					$('#cl_dia_min_'+row_num).val(item.diameter);
				}
				
				/*Minimum Value for package is 1*/
				item.cl_packaging = (item.cl_packaging=="0" || item.cl_packaging=="")?1:item.cl_packaging;
				$('#cl_packaging_'+row_num).val(item.cl_packaging);
				
				if(item.qty_on_hand!="" && item.qty_on_hand>"0"){
					$("#use_on_hand_chk_"+row_num).prop('checked', true);
					$("#order_chk_"+row_num).prop('checked', false);
				}
				else{
					$("#order_chk_"+row_num).prop('checked', true);
					$("#use_on_hand_chk_"+row_num).prop('checked', false);
				}
				//$('#supply_id_'+row_num).val(item.supply_id);
				//chk_dis_fun();
				$("#dispQtyF").val((parseInt($("#qty_"+row_num).val())+parseInt($("#qty_right_"+row_num).val())));
				get_lens_types(item.cl_type);
			}
			
			/*Frame's quantity*/
			if(item.module_type_id=='1'){
				var custom_name=$('#item_name_'+row_num+'_other').val();
				if(customFrame.upc == item.upc_code){
					//show box to add custom frame name
					$('#item_name_'+row_num+'_label').html('Custom Item Name');
					$('#item_name_'+row_num).hide();
					$('#item_name_'+row_num+'_other').show();
				}
				else{
					$('#item_name_'+row_num+'_label').html('Item Name');
					$('#item_name_'+row_num).show();
					$('#item_name_'+row_num+'_other').hide();
					$('#itemDescription_frame_'+row_num).val(custom_name);
				}
				if(customFrame.upc == $('#upc_name_'+row_num).val() && $('#in_add_'+row_num).is(':checked')){
					
				}
				else{
					if(item.qty_on_hand!="" && item.qty_on_hand>"0"){
						$('#use_on_hand_chk_'+row_num).prop('checked', true);
						$('#order_chk_'+row_num+', #in_add_'+row_num).prop('checked', false);
					}
					else{
						$('#order_chk_'+row_num).prop('checked', true);
						$('#use_on_hand_chk_'+row_num+', #in_add_'+row_num).prop('checked', false);
					}
				}
				
				if(Object.keys(stockData).length > 0){
					var stockDetails = '';
					var stockContainer = $('#stockDetails_'+row_num+' > table > tbody');
					$(stockContainer).empty();
					$.each(stockData, function(index, obj){
						var tr = $('<tr>');
						$('<td>').text(obj.name).appendTo(tr);
						$('<td>').text(obj.stock).appendTo(tr);
						$(stockContainer).append(tr);
					});
					stockFramesItems[row_num-1]=true;
				}
			}
			
			$("#upc_name_"+row_num).val(item.upc_code);
			$("#item_name_"+row_num).val($("<span/>").html(item.name).text());
			$("#item_id_"+row_num).val(item.id);
			if(item.module_type_id == '1' || item.module_type_id == '3')
			{
				get_prac_code_text(item.item_prac_code,'item_prac_code_'+row_num,'frm_cont',item.module_type_id);
			}
			$("#contact_cat_id_"+row_num).val(item.class_id);
			$("#manufacturer_id_"+row_num).val(item.manufacturer_id);
			if(item.module_type_id=='1' || item.module_type_id=='3')
			{
				if(item.manufacturer_id!=0){
					get_manufacture_brand(item.manufacturer_id,item.brand_id,row_num,item.module_type_id);
					if( item.module_type_id == 3 ){
						get_vendor_manufacturer(item.manufacturer_id,item.vendor_id,row_num);
					}
				}
				else{
					$('#brand_id_'+row_num).val(item.brand_id);
					if( item.module_type_id == 3 ){
						$('#item_vendor_'+row_num).val(item.vendor_id);
					}
				}
			}
			
			var color_arr = Array();
			var newColorOptions = "";
				if((item.color).search(",")>0)
				{
					color_arr=(item.color).split(",");
					newColorOptions += '<option value=" ">Please Select</option>';
					for(i=0;i<color_arr.length;i++){
						if(color_arr[i]!=0)
						newColorOptions += '<option value="'+color_arr[i]+'">'+colorOptions[color_arr[i]]+'</option>'; 
					}
					if(newColorOptions!=""){
						$("#color_id_"+row_num).html(newColorOptions);
					}
					
				}
				else
				{
					$("#color_id_"+row_num).val(item.color);
				}
				
				if(item.module_type_id == '1'){
					$("#color_code_"+row_num).val(item.color_code);
				}
			
			if(item.module_type_id=='3'){
			var wear_arr = Array();
			var newWearOptions = "";
			wear_arr=(item.cl_wear_schedule).split(",");
				if(wear_arr && (wear_arr.length >0 && wear_arr[0]!=""))
				{
					newWearOptions += '<option value=" ">Please Select</option>';
					
					for(i=0;i<wear_arr.length;i++){
						if(wear_arr[i]!=0  && typeof(wearOptions[wear_arr[i]])!='undefined')
						newWearOptions += '<option value="'+wear_arr[i]+'">'+wearOptions[wear_arr[i]]+'</option>'; 
					}
					if(newWearOptions!=""){
						$("#cl_wear_sch_"+row_num).html(newWearOptions);
					}
				}
				else{
					var newWearOptions = "";
					newWearOptions += '<option value="1" repAttr="1">Please Select</option>';
					
					$.each(wearOptions, function(key, value){
						newWearOptions += '<option value="'+key+' "repAttr="'+value+'">'+value+'</option>';
					});
					if(newRepOptions!=""){
						$("#cl_wear_sch_"+row_num).html(newWearOptions);
					}
				}
			
				var rep_arr = Array();
				var newRepOptions = "";
				rep_arr=(item.cl_replacement).split(",");
				
				if(rep_arr && (rep_arr.length >0 && rep_arr[0]!=""))
				{
					newRepOptions += '<option value="1" repAttr="1">Please Select</option>';
					
					for(i=0;i<rep_arr.length;i++){
						if(rep_arr[i]!=0)
						{
						newRepOptions += '<option value="'+rep_arr[i]+' "repAttr="'+repOptions[rep_arr[i]]+'">'+repOptions[rep_arr[i]]+'</option>';
						}
					}
					if(newRepOptions!=""){
						$("#cl_replacement_"+row_num).html(newRepOptions);
					}
				}
				else{
					var newRepOptions = "";
					newRepOptions += '<option value="1" repAttr="1">Please Select</option>';
					
					$.each(repOptions, function(key, value){
						newRepOptions += '<option value="'+key+' "repAttr="'+value+'">'+value+'</option>';
					});
					if(newRepOptions!=""){
						$("#cl_replacement_"+row_num).html(newRepOptions);
					}
				}
				
			var supply_arr = Array();
			var newSupplyOptions = "";
			supply_arr=(item.supply_id).split(",");
				if(supply_arr && (supply_arr.length >0 && supply_arr[0]!=""))
				{
					newSupplyOptions += '<option value="1" cAttr="1">Please Select</option>';
					
					for(i=0;i<supply_arr.length;i++){
						if(supply_arr[i]!=0 && typeof(supplyOptions[supply_arr[i]])!='undefined')
						{
						newSupplyOptions += '<option value="'+supply_arr[i]+'" cAttr="'+supplyOptions[supply_arr[i]]+'"> '+supplyOptions[supply_arr[i]]+'</option>';
						}
					}
					if(newRepOptions!=""){
						$("#supply_id_"+row_num).html(newSupplyOptions);
					}
				}
				else{
					var newSupplyOptions = "";
					newSupplyOptions += '<option value="1" cAttr="1">Please Select</option>';
					
					$.each(supplyOptions, function(key, value){
						newSupplyOptions += '<option value="'+key+'" cAttr="'+value+'">'+value+'</option>';
					});
					if(newSupplyOptions!=""){
						$("#supply_id_"+row_num).html(newSupplyOptions);
					}
				}	
			}
				
			$("#brand_id_"+row_num).val(item.brand_id);
			$("#color_id_os_"+row_num).val(item.color);
			$("#manufacturer_id_os_"+row_num).val(item.manufacturer_id);
			$("#brand_id_os_"+row_num).val(item.brand_id);
			$("#style_id_os_"+row_num).val(item.style);
			if(item.module_type_id == '1')
			{
				$("#style_id_"+row_num).val(item.frame_style);
				if(item.brand_id!=0){
					get_brand_style(item.brand_id,item.frame_style, row_num);
				}
			}
			else
			{
				$("#style_id_"+row_num).val(item.style);
			}
			$("#temple_"+row_num).val(item.temple);
			
			$("#shape_id_"+row_num).val(item.frame_shape);
			
			if(item.module_type_id == '3'){
				//$("#qty_"+row_num).val('0');
			}else{
				$("#qty_"+row_num).val('1');
			}
			$("#qty_hidden_"+row_num).val('1');
			if(parseInt(item.qty_on_hand)<parseInt(item.threshold))
			{
				$("#qoh_"+row_num).css('color','#FF0000');	
			}
			else if(parseInt(item.qty_on_hand)==parseInt(item.threshold))
			{
				$("#qoh+"+row_num).css('color','#FF0000');
			}
			else
			{
				$("#qoh_"+row_num).css('color','#009900');
			}
			
			if(parseInt(item.stock)<parseInt(item.threshold) || item.stock==null)
			{
				$("#fqoh_"+row_num).css('color','#FF0000');	
			}
			else if(parseInt(item.stock)==parseInt(item.threshold))
			{
				$("#fqoh_"+row_num).css('color','#FF0000');
			}
			else
			{
				$("#fqoh_"+row_num).css('color','#009900');
			}
			
			if(item.qty_on_hand=="" || item.qty_on_hand<0){
				$("#qoh_"+row_num).html(0);
			}
			else{
				$("#qoh_"+row_num).html(item.qty_on_hand);
			}
				if(item.stock==null)
				{
					$("#fqoh_"+row_num).html("0");
				}
				else
				{
					if(item.stock=="" || item.stock<0){
						$("#fqoh_"+row_num).html(0);
					}
					else{
						$("#fqoh_"+row_num).html(item.stock);
					}
				}
			var cur_date = $("#cur_date").val();
			if(cur_date>item.discount_till && item.discount_till!='0000-00-00')
			{
				dissc = 0;
			}
			
			$("#discount_"+row_num).val(dissc);
			$("#discount_hidden_"+row_num).val(dissc);
			$("#frm_pic").html('');
			img = resizeImageWidBdr(top.WRP+"/images/frame_stock/"+item.stock_image,216);
			$("#frm_pic").append(img);
			
			if(typeof(item.retail_price)!='undefined'){
				$("#price_"+row_num).val(item.retail_price);
				$("#allowed_"+row_num).val(item.retail_price);
				$("#rtl_price_"+row_num).val(item.retail_price);
				$("#price_hidden_"+row_num).val(item.retail_price);
				if(item.module_type_id == '3')
				{
					//$("#qty_right_"+row_num).val('0');
					total = cal_discount((item.retail_price*0), dissc);
					total = total*0;
				}
				else
				{
					total = cal_discount((item.retail_price*1), dissc);
				}
			}
			if(typeof(item.price)!='undefined'){
				$("#price_"+row_num).val(item.price);
				$("#allowed_"+row_num).val(item.price);
				total = cal_discount((item.price*item.qty), dissc);
			}
			
			$("#total_amount_"+row_num).val(total);
			$("#total_amount_hidden_"+row_num).val(total);
			 
			 //keep orignal values if pt frame is selected
			if($("#in_add_"+row_num).is(':checked'))
			{
				if(!$("#a_"+row_num).val())
				$("#a_"+row_num).val(item.a).trigger('change');
				
				if(!$("#b_"+row_num).val())
				$("#b_"+row_num).val(item.b);
				
				if(!$("#ed_"+row_num).val())
				$("#ed_"+row_num).val(item.ed);
				
				if(!$("#dbl_"+row_num).val())
				$("#dbl_"+row_num).val(item.dbl);
				
				if(!$("#fpd_"+row_num).val())
				$("#fpd_"+row_num).val(item.fpd);
				
				if(!$("#bridge_"+row_num).val())
				$("#bridge_"+row_num).val(item.bridge);
			}else{
				$("#a_"+row_num).val(item.a);
				$("#b_"+row_num).val(item.b);
				$("#ed_"+row_num).val(item.ed);
				$("#dbl_"+row_num).val(item.dbl);
				$("#fpd_"+row_num).val(item.fpd);
				$("#bridge_"+row_num).val(item.bridge);
			}
			 
			if(item.module_type_id!='3'){
				if(item.module_type_id!='1'){
					$("#use_on_hand_chk_"+row_num).removeAttr('checked');
					$("#order_chk_"+row_num).removeAttr('checked');
					if(item.use_on_hand_chk>0)
					{
						$("#use_on_hand_chk_"+row_num).prop('checked','checked');
					}
					if(item.order_chk>0)
					{
						$("#order_chk_"+row_num).prop('checked','checked');
					}
				}
				$("#type_id_"+row_num).val(item.type_id);
			}
			
			$("#item_comment_"+row_num).val(item.item_comment);
			
			if(item.module_type_id == '3')
			{
				//$("#type_id_"+row_num).val(item.type_id);
				$("#type_id_"+row_num).val('');
				$("#contact_cat_id_"+row_num).val(item.type_id);
			}
			if(item.module_type_id == '2')
			{
				show_progressive_dropdown($("#type_id_"+row_num));
				itemdropdown('0');
			}
			$("#progressive_id_"+row_num).val(item.progressive_id);
			$("#material_id_"+row_num).val(item.material_id);
			$("#transition_id_"+row_num).val(item.transition_id);
			$("#a_r_id_"+row_num).val(item.a_r_id);
			$("#tint_id_"+row_num).val(item.tint_id);
			$("#polarized_id_"+row_num).val(item.polarized_id);
			$("#edge_id_"+row_num).val(item.edge_id);
			$("#other_"+row_num).val(item.other);
			if($("#in_add_"+row_num).is(':checked'))
			{
				$("#price_"+row_num+" , #allowed_"+row_num+" , #discount_"+row_num+" , #total_amount_"+row_num+"").val('0');
			}
			
			$("#uv400_"+row_num).removeAttr('checked');
			
			if(item.uv_check>0)
			{
				$("#uv400_"+row_num).prop('checked','checked');
			}
			
			$("#pgx_"+row_num).removeAttr('checked');
			
			if(item.pgx_check>0)
			{
				$("#pgx_"+row_num).prop('checked','checked');
			}
			
			if(item.module_type_id == '1'){
				if(item.stock_image_large!='no_image_xl.jpg')item.stock_image='view1.jpg';
				$("#stock_image_"+row_num).prop('src', top.WRP+'/images/frame_stock/'+item.stock_image);
					
				$("#stock_image_"+row_num).attr('large', top.WRP+'/images/frame_stock/'+item.stock_image_large);
			}
			else{
				if(item.stock_image==""){
					$("#contact_pic").prop("src","../../images/no_product_image.jpg");
				}else{
					$.ajax({
					  url: '../../images/contact_lens_stock/'+item.stock_image, //or your url
					  success: function(data){
						$("#contact_pic").prop("src","");
						$("#contact_pic").prop("src","../../images/contact_lens_stock/"+item.stock_image);
					  },
					  error: function(data){
						$("#contact_pic").prop("src","");
						$("#contact_pic").prop("src","../../images/no_product_image.jpg");
					  }
					});	
				}
			}
			
			if(item.module_type_id == '3' && item.trial_chk=='1')
			{
				$("#contact_pic").prop("src","../../images/trial_pic_image.jpg");
			}
			if(item.module_type_id == '1')
			{
				//get_related_frames(item.upc_code);
			}
			if(item.module_type_id=='2'){
				item_price(item.id);								
			}
			
			try{
				if(item.module_type_id == '3' && typeof(vision) !== 'undefined'){
					addNewRow(item.module_type_id, item, row_num, '', vision);
				}
				else{
					addNewRow(item.module_type_id, item, row_num);
				}
			}
			catch(e){
				console.log(e.message);
			}
			
			 if(item.module_type_id == 1){//if we do not add this check then customFrame throwing error in case of other modules as undefined
				/*Fix for preselected Patient's Frame Radio Button*/
				if(item.id !== customFrame.id && item.module_type_id == 1 && $('#in_add_'+row_num+':checked').length > 0)
				{
					$('#in_add_'+row_num+':checked').trigger('click');
				}
			 }
		 });
	 }else{
		 	$("#upc_name_"+row_num).val('');
			$("#item_name_"+row_num).val('');
			$("#item_id_"+row_num).val('');
			$("#manufacturer_id_"+row_num).val('');
			$("#color_id_"+row_num).val('');
			$("#brand_id_"+row_num).val('');
			$("#style_id_"+row_num).val('');
			$("#shape_id_"+row_num).val('');
			$("#price_"+row_num).val('');
			$("#allowed_"+row_num).val('');
			$("#discount_"+row_num).val('');
			$("#total_amount_"+row_num).val('');
			$("#qty_"+row_num).val('');
			$("#a_"+row_num).val('');
			$("#b_"+row_num).val('');
			$("#ed_"+row_num).val('');
			$("#bridge_"+row_num).val('');
			$("#fpd_"+row_num).val('');
			$("#use_on_hand_chk_"+row_num).removeAttr('checked');
			$("#order_chk_"+row_num).removeAttr('checked');
			$("#type_id_"+row_num).val('');
			$("#material_id_"+row_num).val('');
			$("#transition_id_"+row_num).val('');
			$("#a_r_id_"+row_num).val('');
			$("#tint_id_"+row_num).val('');
			$("#polarized_id_"+row_num).val('');
			$("#edge_id_"+row_num).val('');
			$("#other_"+row_num).val('');
			$("#uv400_"+row_num).removeAttr('checked');
			$("#frm_pic").html('');
			img = resizeImage('',180)
			$("#frm_pic").append(img);
			$("#all_images").html('');
			$("#div_rel_itm").css({"display":"none"});
	 }
}
function resizeImage(img_src,width,height){
	height = height || '';
	var img = document.createElement('img');
	img.src = img_src;
	$(img).error(function() {
		this.src = top.WRP+"/images/no_product_image.jpg";
	});
	
	img.onload = function() {
  		imgW = this.width;
		imgH = this.height;
		
		wDiv = imgW/width;
		hDiv = imgH/width;
		
		frac = (wDiv > hDiv)? wDiv : hDiv;
		img.width = width;
		if(height == '')
		img.height = imgH/frac;
		else
		img.height = height;
	}	

	return img;
}

function resizeImageWidBdr(img_src,width,height){
	height = height || '';
	var img = document.createElement('img');
	img.src = img_src;
	img.className="img_border";
	img.className+=" module_border";
	img.style.padding="10px";
	
	$(img).error(function() {
		this.src = top.WRP+"/images/no_product_image.jpg";
	});
	
	img.onload = function() {
  		imgW = this.width;
		imgH = this.height;
		
		wDiv = imgW/width;
		hDiv = imgH/width;
		
		frac = (wDiv > hDiv)? wDiv : hDiv;
		img.width = width;
		if(height == '')
		img.height = imgH/frac;
		else
		img.height = height;
	}	

	return img;
}
function cal_discount(amt,dis)
{
	var pattern = /%$/; var total = '';
	
	if(pattern.test(dis)){
		//alert(pattern.test(dis));
		dis = dis.replace(/%/,'');
		total = amt - (amt*dis/100);
	}
	else
	{
		if(dis[0]=="$")
		{
			dis = dis.replace(/^[$]+/,"");
			total = amt - dis;
		}
		else
		{
			//alert('else 2');	
			total = amt - dis;
		}
	}
	return total;
}
function get_related_frames(upc_code){
	$.ajax({
		type: "POST",
		url: top.WRP+"/interface/patient_interface/pt_frame_selection.php?mode=get_related_frames&upc_code="+upc_code,
		cache: false,
		success: function(response)
		{ 		//a = window.open();
				//a.document.write(response)
				//alert(response);
				var dataArr = $.parseJSON(response);
				$("#all_images").html('');
				rel_callback(dataArr);
				$("#div_rel_itm").css({"display":"inline-block"});
				//$("#div_rel_itm").show();
		}
	}); 
}
function rel_callback(dataArr){
	if(dataArr!="" && dataArr!=null){
		count = 0;
		$.each(dataArr, function(i, item) 
		{	
			var divEle = document.createElement('div');
			divEle.className = "img_border";
			//divEle.onClick = function(){get_details_by_upc(item.id)};
			$(divEle).attr("onClick","get_details_by_upc('"+item.id+"')");
			$(divEle).css({"float":"left"});
			
			stock_image = top.WRP+"/images/frame_stock/"+item.stock_image;
			img = resizeImage(stock_image, 60, 60);
			$(divEle).append(img);
			$("#all_images").append(divEle);
			count++;
		});
	}
}

function get_vendor_manufacturer(mid,vid,num)
{
	if(mid!='')
	{
		var string = 'action=get_vendor&mid='+mid+'&vid='+vid;
		$.ajax({
			type: "POST",
			url: top.WRP+"/interface/admin/ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Please Select</option>" + response;
				$('#item_vendor_'+num).html(opt_data);
			}
		});
	}
}

function get_manufacture_brand(mid,bid,num,module,change)
{
	if(mid!='')
	{
		if(typeof(num)=='undefined')
		{
			var num = 1;
		}
		
		var string = 'action=get_brand&mid='+mid+'&bid='+bid;
		var path = top.WRP+"/interface/patient_interface";
		if(typeof(module)!='undefined' && module==3){
			string = 'action=get_brand_contact&mid='+mid+'&bid='+bid;
			path = top.WRP+"/interface/admin";
		}
		$.ajax({
			type: "POST",
			url: path+"/ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Please Select</option>" + response;
				$('#brand_id_'+num).html(opt_data);
			}
		});
	}
}

function get_type_manufacture(tid,mid)
{
	if(tid!='')
	{
		var string = 'action=get_manufacture&tid='+tid+'&mid='+mid;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Select Manufacturer</option>" + response;
				$('#manufacturer_Id_Srch').html(opt_data);	
			}
		});
	}
}
function get_type_manufacture1(tid,mid)
{
	if(tid!='')
	{
		var string = 'action=get_manufacture&tid='+tid+'&mid='+mid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Select Manufacturer</option>" + response;
				$('#manufacturer_Id_Srch').html(opt_data);	
			}
		});
	}
}
function get_vendorFromManufacturer(mid,vid)
{
	if(mid!='')
	{
		var string = 'action=get_vendor&mid='+mid+'&vid='+vid;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Select Vendor</option>" + response;
				$('#opt_vendor_id').html(opt_data);
			}
		});
	}
}
function get_vendorFromManufacturer1(mid,vid)
{
	if(mid!='')
	{
		var string = 'action=get_vendor&mid='+mid+'&vid='+vid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value='0'>Select Vendor</option>" + response;
				$('#opt_vendor_id').html(opt_data);
			}
		});
	}
}
function get_brandFromVendor(vid,bid,mod_id){
	if(vid!=''){
		var string= 'action=get_vendor_brand&vid='+vid+'&bid='+bid+'&mod_id='+mod_id;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response){
				var opt_data= "<option value='0'>Select Brand</option>" + response;	
				$('#opt_brand_id').html(opt_data);	
			}
		});
	}
}

function get_brandFromManufacturer(mid,bid){
	if(mid!=''){
		var string= 'action=get_manufacturer_brand&mid='+mid+'&bid='+bid;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response){
				var opt_data= "<option value='0'>Select Brand</option>" + response;	
				$('#opt_brand_id').html(opt_data);	
			}
		});
	}
}
function get_collectionFromBrand(bid,cid){
	if(bid!=''){
		var string= 'action=get_brand_collection&bid='+bid+'&cid='+cid;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response){
				var opt_data= "<option value='0'>Select Collection</option>" + response;	
				$('#opt_collection_id').html(opt_data);	
			}
		});
	}
}

function get_brandFromVendor1(vid,bid,mod_id){
	if(vid!=''){
		var string= 'action=get_vendor_brand&vid='+vid+'&bid='+bid+'&mod_id='+mod_id;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response){
				var opt_data= "<option value='0'>Select Brand</option>" + response;	
				$('#opt_brand_id').html(opt_data);	
			}
		});
	}
}
function get_brand_style(bid,sid,num)
{
	if(bid!='')
	{
		if(typeof(num)=='undefined')
		{
			var num = 1;
		}
		var string = 'action=get_style&bid='+bid+'&sid='+sid;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value=''>Select</option>" + response;
				$('#style_id_'+num).html(opt_data);
			}
		});
	}
}
function get_brand_style1(bid,sid,num)
{
	if(bid!='')
	{
		if(typeof(num)=='undefined')
		{
			var num = 1;
		}
		var string = 'action=get_style&bid='+bid+'&sid='+sid;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				var opt_data = "<option value=''>Select</option>" + response;
				$('#style_id_'+num).html(opt_data);
			}
		});
	}
}
function item_price(item_id)
{
	var item_id = item_id;
	var dataString = 'action=managestock&item_id='+item_id;
	$.ajax({
		type: "POST",
		url: "lens_price_detail.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			 var dataArr = $.parseJSON(response);
			 if(dataArr!="")
			 {
				 $.each(dataArr, function(i, item) 
				 {
					$("#lens_item_price_1").val(item.lens_retail);
					$("#lens_item_price_2").val(item.material_retail);
					$("#lens_item_price_3").val(item.a_r_retail);
					$("#lens_item_price_4").val(item.transition_retail);
					$("#lens_item_price_5").val(item.polarization_retail);
					$("#lens_item_price_6").val(item.tint_retail);
					$("#lens_item_price_7").val(item.uv400_retail);
					$("#lens_item_price_8").val(item.other_retail);
					$("#lens_item_price_9").val(item.progressive_retail);
					$("#lens_item_price_10").val(item.edge_retail);
					$("#lens_item_price_11").val(item.color_retail);
					$("#lens_item_price_12").val(item.pgx_retail);
					
					$("#lens_item_allowed_1").val(item.lens_retail);
					$("#lens_item_allowed_2").val(item.material_retail);
					$("#lens_item_allowed_3").val(item.a_r_retail);
					$("#lens_item_allowed_4").val(item.transition_retail);
					$("#lens_item_allowed_5").val(item.polarization_retail);
					$("#lens_item_allowed_6").val(item.tint_retail);
					$("#lens_item_allowed_7").val(item.uv400_retail);
					$("#lens_item_allowed_8").val(item.other_retail);
					$("#lens_item_allowed_9").val(item.progressive_retail);
					$("#lens_item_allowed_10").val(item.edge_retail);
					$("#lens_item_allowed_11").val(item.color_retail);
					$("#lens_item_allowed_12").val(item.pgx_retail);
					
					$("#item_prac_code_1").val(item.type_prac_code);
					$("#item_prac_code_2").val(item.material_prac_code);
					$("#item_prac_code_3").val(item.ar_prac_code);
					$("#item_prac_code_4").val(item.transition_prac_code);
					$("#item_prac_code_5").val(item.polarized_prac_code);
					$("#item_prac_code_6").val(item.tint_prac_code);
					$("#item_prac_code_7").val(item.uv_prac_code);
					$("#item_prac_code_8").val(item.other_prac_code);
					$("#item_prac_code_9").val(item.progressive_prac_code);
					$("#item_prac_code_10").val(item.edge_prac_code);
					$("#item_prac_code_11").val(item.color_prac_code);
					$("#item_prac_code_12").val(item.pgx_prac_code);
					
					get_prac_code_text(item.type_prac_code,"item_prac_code_1","frm_cont", 2);
					get_prac_code_text(item.material_prac_code,"item_prac_code_2","frm_cont", 2);
					get_prac_code_text(item.ar_prac_code,"item_prac_code_3","frm_cont", 2);
					get_prac_code_text(item.transition_prac_code,"item_prac_code_4","frm_cont", 2);
					get_prac_code_text(item.polarized_prac_code,"item_prac_code_5","frm_cont", 2);
					get_prac_code_text(item.tint_prac_code,"item_prac_code_6","frm_cont", 2);
					get_prac_code_text(item.uv_prac_code,"item_prac_code_7","frm_cont", 2);
					get_prac_code_text(item.other_prac_code,"item_prac_code_8","frm_cont", 2);
					get_prac_code_text(item.progressive_prac_code,"item_prac_code_9","frm_cont", 2);
					get_prac_code_text(item.edge_prac_code,"item_prac_code_10","frm_cont", 2);
					get_prac_code_text(item.color_prac_code,"item_prac_code_11","frm_cont", 2);
					get_prac_code_text(item.pgx_prac_code,"item_prac_code_12","frm_cont", 2);
					
				 });
				 calculate_all();							 
			 }
			 else
			 {
				// $("#stock_form")[0].reset();
			 }
			 
			 $.each(get_details_by_upc_data_arr, function(i, item) 
			 {
				lens_row_display(item.type_id,'lens_display','in_lens_type');
				lens_row_display(item.progressive_id,'progressive_display','in_lens_progressive');
				lens_row_display(item.material_id,'material_display','in_lens_material');
				lens_row_display(item.transition_id,'transition_display','in_lens_transition');
				lens_row_display(item.a_r_id,'a_r_display','in_lens_ar');
				lens_row_display(item.tint_id,'tint_display','in_lens_tint');
				lens_row_display(item.polarized_id,'polarization_display','in_lens_polarized');
				lens_row_display(item.edge_id,'edge_display','in_lens_edge');
				lens_row_display(item.color,'color_display','in_lens_color');
				lens_row_display(item.uv_check,'uv400_display','in_item_price_details');
				itemized_other_display('','other_display','in_item_price_details');
				lens_row_display(item.pgx_check,'pgx_display','in_item_price_details');
			 });
		}
	}); 
}

function display_toggle(obj_id, property_name, property_value, iframe_name){//TO TOGGLE ANY STYLE PROPERTY
	if(iframe_name != "" && typeof(iframe_name) != "undefined"){
		eval(iframe_name+".document.getElementById(\""+obj_id+"\").style."+property_name+" = \""+property_value+"\"");
	}else{
		eval("document.getElementById(\""+obj_id+"\").style."+property_name+" = \""+property_value+"\"");
	}
}

function show_loading_image(mode, padd_top, show_text){//TO SHOW / HIDE LOADING IMAGE
	if(mode == "show"){
		display_toggle("div_loading_image", "display", "block");
		if(padd_top != "" && typeof(padd_top) != "undefined"){
			$("#div_loading_image").css("margin-top", padd_top+"px");
		}else{
			$("#div_loading_image").css("margin-top", "0px");
		}
		if(show_text != "" && typeof(show_text) != "undefined"){
			$("#div_loading_text").html(show_text);
			display_toggle("div_loading_text", "display", "block");
		}
	}
	if(mode == "hide"){
		$("#div_loading_text").html("");
		display_toggle("div_loading_image", "display", "none");
		display_toggle("div_loading_text", "display", "none");
	}
}

function get_frame_price_detail(frame_id)
{	
	var item_id = item_id;
	if(frame_id=="0")
	{
		$("#frame_order_id").val('0');	
	}
	else
	{
		$("#frame_order_id").val(frame_id);
	}
	var dataString = 'action=manageframe&frame_id='+frame_id;
	$.ajax({
		type: "POST",
		url: "ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			if(response!="" && response.length>0)
			{
				 var dataArr = $.parseJSON(response);
				 if(dataArr!="" && dataArr.length>0)
				 {
					 $.each(dataArr, function(i, item) 
					 {	
						if(item.item_name=="")
						{
							$("#frame_name_td").html('Frame');
						}
						else
						{
							$("#frame_name_td").html(item.item_name);
						}
						get_prac_code_text(item.item_prac_code,"frame_item_prac_code",'', 1);
						$("#frame_price").val(item.price);
						$("#frame_disc").val(item.discount);
						$("#frame_total").val(item.total_amount);
						$("#frame_qty").val(item.qty);
						var title_price = cal_discount(item.price,item.discount);
						$("#frame_total").prop('title',title_price+' * '+item.qty);
					 });
					 calculate_all();
				 }
			}
			 else
			 {
				$("#frame_name_td").html('Frame');
				$("#frame_price").val('0.00');
				$("#frame_disc").val('0');
				$("#frame_total").val('0.00');
				calculate_all();
			 }
		}
	}); 
}
function set_phone_format(objPhone, default_format, objfor){
	//alert('new');
	var msg = "";
	if(typeof(objfor)=="undefined")
	{
		msg = "Please Enter a valid phone number";
	}
	else if(objfor=="fax")
	{
		msg = "Please Enter a valid fax number";
	}
	if(objPhone.value == "" || objPhone.value.length < 10){
		top.falert(msg);
		objPhone.value="";
		setTimeout(function(){objPhone.focus(); },0);
		return;
	}else{
		var refinedPh = objPhone.value.replace(/[^0-9+]/g,"");					
		if(refinedPh.length < 10){
			top.falert(msg);
			objPhone.value="";
			setTimeout(function(){objPhone.focus(); },0);
		}else{
			switch(default_format){
				case "###-###-####":
					objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				case "(###) ###-####":
					objPhone.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
				default:
					objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
				break;
			}
		}
	}
	//changeClass(objPhone);
}


function zip_vs_state_length(zipthis,c)
{
	zval = $.trim(zipthis.value);
	if(zval!="" && zval.length<5)
	{
		top.falert("Please Enter Correct Zip Code");
		$("#zip_"+c).val('');
		$("#zip_"+c).focus();
		$("#city_"+c).val('');
		$("#state_"+c).val('');
	}
}

function zip_vs_state(zipco,c,pl)
{
	var zcod ='';
	zcod = $.trim(zipco.value);
	var dataStrings='';
	var res='';
	var ur="";
	ur = "../ajax.php";
	if(pl=="dem")
	{
		ur = "../admin/ajax.php";
	}
	if(zcod.length==5)
	{
		dataStrings = 'action=getcitystate&zipcode='+zcod;
		$.ajax({
			type: "POST",
			url: ur,
			data: dataStrings,
			cache: false,
			beforeSend:function(){	
			$("#loading_img").show();	
		},
			success: function(response)
			{
				if(response=='false')
				{
					top.falert("Please Enter Correct Zip Code");
					$("#zip_"+c).val('');
					$("#zip_"+c).focus();
					$("#city_"+c).val('');
					$("#state_"+c).val('');
					$("#loading_img").hide();	

				}
				else
				{
					res = response.split("-");
					$("#city_"+c).val(res[0]);
					$("#state_"+c).val(res[1]);
					$("#loading_img").hide();	
				}
			}
		}); 
	}

}

function get_prac_code_name(prac_code_id,td_id,cl_idoc)
{
	if(prac_code_id!='')
	{
		var string = 'action=get_prac_code_name&prac_code_id='+prac_code_id;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				get_resp = response.split("~~~~");
				if(td_id)
				{
					$('#'+td_id).val(get_resp[0]);
					$('#'+td_id).attr("title",get_resp[1]);	
				}
				else
				{
					$('#item_prac_code').val(get_resp[0]);
					$('#item_prac_code').attr("title",get_resp[1]);
					if(cl_idoc){
						show_price_from_praccode($("#item_prac_code").val(), 'retail_price');
					}
				}
			}
		});
	}
}

function get_prac_code_text(prac_code_id,td_id,pag,modType)
{
	if(prac_code_id!='')
	{
		modType = (modType=="undefined")?0:modType;
		var string = 'action=get_prac_code_text&prac_code_id='+prac_code_id+'&modType='+modType+'&td_id='+td_id;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				get_response = response.split("~~~~");
				if(pag=="frm_cont")
				{
					$('#'+td_id).val(get_response[0]);
					$('#'+td_id).attr("title",get_response[1]);
				}
				else
				{
					$('#'+td_id).html(get_response[0]);
					$('#'+td_id).attr("title",get_response[1]);
				}
			}
		});
	}
}

function prac_by_type(type_id,tb,call_back,seg_val)
{
	if(type_id!='')
	{
		var string = 'action=prac_by_type&type_id='+type_id+'&tb_name='+tb;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				Get_resp = response.split("~~~~");
				if(typeof(type_codes)!="undefined"){
					if(Get_resp[0]==""){
						if(tb=='in_lens_type'){
							var type_code = $('#lens_type option[value="'+type_id+'"]').attr('default_val');
							Get_resp[0] = default_prac_code[type_code];
						}
						else{
							Get_resp[0] = default_prac_code[type_codes[tb]];
						}
					}
				}
				$('#'+call_back).val(Get_resp[0]);
				$('#'+call_back).attr("title",Get_resp[1]);
			}
		});
	}
	else
	{
		$('#'+call_back).val('');
	}
}

function prac_by_type_multi(type_id,tb,call_back){
	resp = "";
	async = (typeof(call_back)=="undefined")?false:true;
	if(type_id!=''){
		var string = 'action=prac_by_type_multi&type_id='+type_id+'&tb_name='+tb;
		$.ajax({
			type: "POST",
			url: top.WRP+"/interface/admin/ajax.php",
			data: string,
			cache: false,
			async: async,
			success: function(response){
				if(typeof(call_back)=="undefined"){
					resp = response;
				}
				else{
					$('#'+call_back).val(response);
				}
			}
		});
	}
	else{
		if(typeof(call_back)!="undefined"){
			$('#'+call_back).val('');
		}
	}
	if(typeof(call_back)=="undefined"){
		return(resp);
	}
}

function itemized_row_display(val,row_id,tb)
{
	var string='';
	var uv4 = $("#uv400_1").is(":checked");
	var pgx = $("#pgx_1").is(":checked");
	if((val>0 && val!="" && row_id!="uv400_display") || (row_id=="uv400_display" && uv4!=false) || (row_id=="pgx_display" && pgx!=false))
	{
		if(row_id=="uv400_display")
		{
			if($("#uv400_1").is(":checked"))
			{
				var item_id = $("#item_id_1").val();
				string = 'action=get_price_from_praccode&sel_id='+item_id+'&for=uv&tb_name='+tb;
			}
		}
		else if(row_id=="pgx_display")
		{
			if($("#pgx_1").is(":checked"))
			{
				var item_id = $("#item_id_1").val();
				string = 'action=get_price_from_praccode&sel_id='+item_id+'&for=pgx&tb_name='+tb;
			}
		}
		else
		{
			string = 'action=get_price_from_praccode&sel_id='+val+'&tb_name='+tb;	
		}
		
		if(string!='')
		{
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: string,
				cache: false,
				success: function(response)
				{
					var mystr = response.split('-:');
					$("#"+row_id+" .pracodefield").val(mystr[0]);
					$("#"+row_id+" .pracodefield").attr("title",mystr[3]);
					$("#"+row_id+" .price_cls").val(mystr[1]);
					$("#"+row_id+" .qty_cls").val('2');
					$("#"+row_id).show();
					calculate_all();
				}
			});
		}
	}
	else
	{
		$("#"+row_id+" .price_cls").val(0);
		$("#"+row_id+" .price_disc").val(0);
		$("#"+row_id+" .qty_cls").val(0);
		$("#"+row_id+" .price_total").val(0);
		$("#"+row_id).hide();
		calculate_all();
	}
	
}

function lens_row_display(val,row_id,tb,stats)
{
	var string='';
	var uv4 = $("#uv400_1").is(":checked");
	var pgx = $("#pgx_1").is(":checked");
	if((val>0 && val!="" && row_id!="uv400_display") || (row_id=="uv400_display" && uv4!=false) || (row_id=="pgx_display" && pgx!=false))
	{
		if(stats!="order")
		{
			$("#"+row_id+" .qty_cls").val(2);
		}
		$("#"+row_id).show();
	}
	else
	{
		$("#"+row_id+" .price_cls").val(0);
		$("#"+row_id+" .price_disc").val(0);
		$("#"+row_id+" .qty_cls").val(0);
		$("#"+row_id+" .price_total").val(0);
		$("#"+row_id).hide();
	}
	calculate_all();
}

function itemized_other_display(val,row_id,tb,stat)
{
	if(row_id=="other_display" && val!="")
	{
		if(stat=="order")
		{
			$("#"+row_id).show();
			calculate_all();
		}
		else
		{
			var item_id = $("#item_id_1").val();
			string = 'action=get_price_from_otherrow&sel_id='+item_id+'&tb_name='+tb;
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: string,
				cache: false,
				success: function(response)
				{
					var mystr = response.split('-:');
					$("#"+row_id+" .pracodefield").val(mystr[0]);
					$("#"+row_id+" .pracodefield").attr("title",mystr[3]);
					$("#"+row_id+" .price_cls").val(mystr[1]);
					$("#"+row_id+" .qty_cls").val('2');
					$("#"+row_id).show();
					calculate_all();
				}
			});
		}
	}
	else
	{
		$("#"+row_id+" .price_cls").val(0);
		$("#"+row_id+" .price_disc").val(0);
		$("#"+row_id+" .qty_cls").val(0);
		$("#"+row_id+" .price_total").val(0);
		$("#"+row_id).hide();
		calculate_all();
	}
}

function getPriceFromPracCode(prac_id){
	var prac_price = "";
	if(prac_id!=""){
		var string = 'action=get_price_from_praccode&type=cl_disc&prac_code='+prac_id;
		$.ajax({
			type: "POST",
			url: top.WRP+'/interface/admin/ajax.php',
			data: string,
			cache: false,
			async: false,
			success: function(response){
				prac_price = response;
			}
		});
	}
	return(prac_price);
}

function show_price_from_praccode(pracval,input_id,plc,trl,preserve)
{
	var val = (typeof(pracval) == "object" )? $.trim(pracval.value) : pracval;
	preserve = ( typeof(preserve)=='undefined' ) ? false : preserve;
	if(val!='')
	{
		if(plc=="pos" || plc=="frm")
		{
			var ur = "../admin/ajax.php";	
		}
		else if(plc=="add_new")
		{
			var ur = "ajax.php";	
		}
		else
		{
			var ur = "../ajax.php";
		}
		var string = 'action=get_price_from_praccode&prac_code='+val;
		$.ajax({
			type: "POST",
			url: ur,
			data: string,
			cache: false,
			success: function(response)
			{
				var mystr = response.split('~~~');
				if(preserve && mystr[0]==''){
					$(pracval).val(val);
					$(pracval).attr("title", val);
				}
				else{
					$(pracval).val(mystr[0]);
					$(pracval).attr("title",mystr[2]);
				}
				if(trl!="tax")
				{
					var get_allowed_id = input_id;
					var allowed_id = get_allowed_id.replace("price", "allowed"); 

					if(input_id!="")
					{
						$('#'+input_id).val(mystr[1]);
						$('#'+allowed_id).val(mystr[1]);
					}
					if(trl==1)
					{
						$('#'+input_id).val('0.00');
						$('#'+allowed_id).val('0.00');
						$("#discount_1").val('0.00');
						$("#total_amount_1").val('0.00');
					}
					if(plc=="itemized")
					{
						get_retail_total();	
					}
					else if(plc=="pos")
					{
						calculate_all();
					}
					else if(plc=="frm")
					{
						chk_dis_fun();
					}
					else if(plc=="admin" || plc=="add_new")
					{}
					else
					{
						var id = $.trim(input_id).substr(-1,1);
						price_total_fun(id);
					}
				}
			}
		});
	}
}

function prac_code_by_item(item_id,cl_name)
{
	if(item_id!='')
	{
		var string = 'action=prac_code_by_item&item_id='+item_id+'&cl_name='+cl_name;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				$('#'+cl_name).val(response);
			}
		});
	}
}

function prac_code_by_item_multi(item_id,cl_name){
	if(item_id!=''){
		var string = 'action=prac_code_by_item_multi&item_id='+item_id+'&cl_name='+cl_name;
		$.ajax({
			type: "POST",
			url: "../ajax.php",
			data: string,
			cache: false,
			success: function(response){
				$('#'+cl_name).val(response);
			}
		});
	}
}

function get_dxcode(dx_codes)
{
	var dx_code_val = dx_codes.value;
	var go_dx_val="";
	var dx_val = Array();
	if(dx_code_val!='')
	{
		dx_val = dx_code_val.split(';');
		
		$(dx_val).each(function(i, v){
			dx_val[i] = $.trim(v);
		});
		
		dx_val_len = [dx_val.length-1];
		go_dx_val=dx_val[dx_val_len];
		var string = 'action=dx_code_id&dx_code='+go_dx_val;
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: string,
			cache: false,
			success: function(response)
			{
				dx_val[dx_val_len] = response;
				var res = dx_val.join('; ');
				$(dx_codes).val(res);
			}
		});
	}
}

function copy_item_new()
{
	$("#edit_item_id").val('');	
	$("#upc_id").val('');
	$("#upc_name").val('');
	$("#name").val('');
	$("#qty_on_hand").val('');
	$("#qty_on_hand_td").html('');
	$("#item_image").html('<img alt="" src="../../../images/no_product_image.jpg" width="220" class="module_border" style="padding:5px">');
	$("#amount").val('');
	
	
}
function check_cancelled_callBack(result)
{
	if(result==true)
	{
		$("#save").val('');
		$("#cancel").val('Cancel');
		$("#firstform1").submit();	
	}	
}
function check_cancelled()
{
	var len = $(".getchecked:checked");
	if(len.length > 0 )
	{
		top.fconfirm('Do you want to cancel selected items?',check_cancelled_callBack);
	}
	else
	{
		falert("Please select a record");	
	}
}
function check_cancelled_order_callBack(result)
{
	if(result==true)
	{
		$("#save").val('');
		$("#cancel").val('Cancel');
		$("#firstform1").submit();
	}	
}
function check_cancelled_order()
{
	var len = $(".getchecked:checked");
	if(len.length > 0 )
	{
		top.fconfirm('Do you want to cancel selected items?',check_cancelled_order_callBack);
	}
	else
	{
		top.falert("Please select items to cancel")	
	}
}
function check_dispensed(path)
{
	var len = $(".getchecked:checked");
	var is_dispensed=0;
	$("#save").val('Save');
	$("#cancel").val('');
	//by default whole sale box set to be hidden
	$("#update_wholesale_lb").hide();
	
	if(len.length > 0 )
	{
		if($("#pagename").val()=='item_detail')
		{
			//show whole sale alert
			$("#update_wholesale_lb").show();
			$("#status_reason").hide();
		}
		
		for(var i=0;i<len.length;i++)
		{
			if($(".status_dropdown_"+len[i].value).not(".noQtyReduce").val()=="dispensed")
			{
				is_dispensed++;
			}
		}
		
		if(is_dispensed>=1){$("#reduce_qty_lb").show();}
		else {$("#reduce_qty_lb").hide();}
		
		//stopped on 31 oct 2017 due to user forum requirement
		//top.fcustomReason(path);
		top.fcustomReasonFake(path);
		top.submitMe();
	}else{
		top.falert('Please select a record');
	}
}
function delete_item_callBack(result)
{
	if(result==true){
		$('#delBtn').click();
	}
}

function delete_item()
{
	var itemid = $("#edit_item_id").val();
	if(itemid!="")
	{
		top.fconfirm('Are you sure to delete selected record ?',delete_item_callBack);
	}
	else
	{
		return false;
	}
}

var get_details_by_upc_data_arr_lensD = "";
function get_details_by_upc_lensD(code,row_num,action)
{
	if(typeof(row_num)=='undefined')
	{
		var row_num = 1;
	}
	if(typeof(action)=='undefined'){
		var action = 'managestock';
	}
	var ucode = (typeof(code) == "object" )? $.trim(code.value): code;
	if(ucode===""){return false;}
	var dataString = 'action='+action+'&code='+ucode;
	$.ajax({
		type: "POST",
		url: top.WRP+"/interface/patient_interface/ajax.php",
		data: dataString,
		cache: false,
		success: function(response)
		{	
			 var dataArr = (response.trim()!="")?$.parseJSON(response):{};
			 get_details_by_upc_data_arr = dataArr;
			 call_back_lensD(dataArr,row_num);
		}
	}); 
}
function call_back_lensD(dataArr,row_num){
	if(dataArr!="" && dataArr.length>0){
		 $.each(dataArr, function(i, item) 
		 {	
			
			var vision_val = $('#lens_vision_'+row_num+'_lensD').val();
			
			$('#dispLensUpc').html(item.upc_code);
			$("#upc_name_"+row_num+"_lensD").val(item.upc_code);
			$("#item_name_"+row_num+"_lensD").val(item.name);
			$("#item_id_"+row_num+"_lensD").val(item.id);
			if(item.module_type_id == '1' || item.module_type_id == '3')
			{
				get_prac_code_text(item.item_prac_code,'item_prac_code_'+row_num+"_lensD",'frm_cont',item.module_type_id);
			}
			$("#contact_cat_id_"+row_num+"_lensD").val(item.class_id);
			$("#manufacturer_id_"+row_num+"_lensD").val(item.manufacturer_id);
			if(item.module_type_id == '1')
			{
				get_manufacture_brand(item.manufacturer_id,item.brand_id);
			}
			if(item.color==""){item.color=0;}
			$("#color_id_"+row_num+"_lensD").val(item.color);
			$("#brand_id_"+row_num+"_lensD").val(item.brand_id);
			$("#color_id_os_"+row_num+"_lensD").val(item.color);
			$("#manufacturer_id_os_"+row_num+"_lensD").val(item.manufacturer_id);
			$("#brand_id_os_"+row_num+"_lensD").val(item.brand_id);
			$("#style_id_os_"+row_num+"_lensD").val(item.style);
			if(item.module_type_id == '1')
			{
				$("#style_id_"+row_num+"_lensD").val(item.frame_style);
				get_brand_style(item.brand_id,item.frame_style);
			}
			else
			{
				$("#style_id_"+row_num+"_lensD").val(item.style);
			}
			$("#dbl_"+row_num+"_lensD").val(item.dbl);
			$("#temple_"+row_num+"_lensD").val(item.temple);

			$("#shape_id_"+row_num+"_lensD").val(item.frame_shape);
			/*$("#qty_"+row_num+"_lensD").val(item.qty);
			$("#qty_"+row_num+"_lensD").val('1');*/
			
			if(parseInt(item.qty_on_hand)<parseInt(item.threshold))
			{
				$("#qoh_lensD").css('color','#FF0000');	
			}
			else if(parseInt(item.qty_on_hand)==parseInt(item.threshold))
			{
				$("#qoh_lensD").css('color','#FF0000');
			}
			else
			{
				$("#qoh_lensD").css('color','#009900');
			}
			$("#qoh_lensD").html(item.qty_on_hand);
			
			var cur_date = $("#cur_date_lensD").val();
			
			/*Discount*/
			var dissc="";
			dis_till = (item.discount_till).split("-");
			cur_date = new Date();
			dis_date = new Date();
			dis_date.setFullYear(parseInt(dis_till[0]), parseInt(dis_till[1])-1, parseInt(dis_till[2]));
			if(cur_date>dis_date && item.discount_till!="0000-00-00"){
				dissc=0;
			}
			else{
				dissc=item.discount;
			}
			dissc=(item.discount=="")?"0":dissc;
			
			$("#discount_"+row_num+"_lensD").val(dissc);
			
			$("#discount_hidden_"+row_num+"_lensD").val(dissc);
			
			$("#frm_pic_lensD").html('');
			img = resizeImageWidBdr(top.WRP+"/images/frame_stock/"+item.stock_image,216);
			$("#frm_pic_lensD").append(img);
			
			if(typeof(item.retail_price)!='undefined'){
				$("#price_"+row_num+"_lensD").val(item.retail_price);
				$("#allowed_"+row_num+"_lensD").val(item.retail_price);
				$("#price_hidden_"+row_num+"_lensD").val(item.retail_price);
				if(item.module_type_id == '3')
				{
					$("#qty_right_"+row_num+"_lensD").val('1');
					total = cal_discount((item.retail_price*1), dissc);
					total = total*2;
				}
				else
				{
					total = cal_discount((item.retail_price*1), dissc);
				}
			}
			if(typeof(item.price)!='undefined'){
				$("#price_"+row_num+"_lensD").val(item.price);
				$("#allowed_"+row_num+"_lensD").val(item.price);
				total = cal_discount((item.price*item.qty), dissc);
			}
			
			$("#total_amount_"+row_num+"_lensD").val(total);
			$("#total_amount_hidden_"+row_num+"_lensD").val(total);
			if(item.module_type_id == '3' && item.trial_chk=='1')
			{
				$("#trial_"+row_num+"_lensD").val('1');
				$("#price_"+row_num+"_lensD").val('0.00');
				$("#allowed_"+row_num+"_lensD").val('0.00');
				$("#discount_"+row_num+"_lensD").val('0.00');
				$("#total_amount_"+row_num+"_lensD").val('0.00');
			}
			$("#a_"+row_num+"_lensD").val(item.a);
			$("#b_"+row_num+"_lensD").val(item.b);
			$("#ed_"+row_num+"_lensD").val(item.ed);
			$("#bridge_"+row_num+"_lensD").val(item.bridge);
			$("#fpd_"+row_num+"_lensD").val(item.fpd);
			$("#use_on_hand_chk_"+row_num+"_lensD").removeAttr('checked');
			$("#order_chk_"+row_num+"_lensD").removeAttr('checked');
			if(item.use_on_hand_chk>0)
			{
				$("#use_on_hand_chk_"+row_num+"_lensD").prop('checked','checked');
			}
			if(item.order_chk>0)
			{
				$("#order_chk_"+row_num+"_lensD").prop('checked','checked');
			}
			$("#item_comment_"+row_num+"_lensD").val(item.item_comment);
			if(item.type_id==""){item.type_id=0;}
			
			var itemVAls = {};
			itemVAls.seg_type = item.type_id;
			itemVAls.design = item.design_id;
			itemVAls.material = item.material_id;
			itemVAls.treatment = item.a_r_id;
			
			if(vision_val=='ou'){
				
				fetch_vision_dd(item.type_id, 'seg_type', row_num, 'admin', itemVAls, 'od');
				fetch_vision_dd(item.type_id, 'seg_type', row_num, 'admin', itemVAls, 'os');
				$("#seg_type_id_"+row_num+"_od_lensD").val(item.type_id);
				$("#seg_type_id_"+row_num+"_os_lensD").val(item.type_id);
				
				$("#design_id_"+row_num+"_od_lensD").val(item.design_id);
				$("#design_id_"+row_num+"_os_lensD").val(item.design_id);
				
				$("#material_id_"+row_num+"_od_lensD").val(item.material_id);
				$("#material_id_"+row_num+"_os_lensD").val(item.material_id);
			}
			else{
				
				fetch_vision_dd(item.type_id, 'seg_type', row_num, 'admin', itemVAls, vision_val);
				
				$("#seg_type_id_"+row_num+"_"+vision_val+"_lensD").val(item.type_id);
				$("#design_id_"+row_num+"_"+vision_val+"_lensD").val(item.design_id);
				$("#material_id_"+row_num+"_"+vision_val+"_lensD").val(item.material_id);
			}
			
			
			$("#progressive_id_"+row_num+"_lensD").val(item.progressive_id);
			
			$("#transition_id_"+row_num+"_lensD").val(item.transition_id);
			
			//lens_load_multi_options('a_r',item.a_r_id,"a_r_id_"+row_num+"_lensD");
			//$("#a_r_id_"+row_num+"_lensD").val(item.a_r_id);

			$("#tint_id_"+row_num+"_lensD").val(item.tint_id);
			$("#polarized_id_"+row_num+"_lensD").val(item.polarized_id);
			$("#edge_id_"+row_num+"_lensD").val(item.edge_id);
			$("#other_"+row_num+"_lensD").val(item.other);
			if($("#in_add_"+row_num+"_lensD").is(':checked'))
			{
				$("#price_1_lensD , #allowed_1_lensD ,#qty_1_lensD , #discount_1_lensD , #total_amount_1_lensD").val('0');
			}
			
			$("#uv400_"+row_num+"_lensD").removeAttr('checked');
			
			if(item.uv_check>0)
			{
				$("#uv400_"+row_num+"_lensD").prop('checked','checked');
			}
			
			$("#pgx_"+row_num+"_lensD").removeAttr('checked');
			//base curve
			$("#lens_base_od_"+row_num+"_lensD").val(item.bc);
			$("#lens_base_os_"+row_num+"_lensD").val(item.bc);
			//min seg ht
			$("#lens_seg_od_"+row_num+"_lensD").val(item.minimum_segment);
			$("#lens_seg_os_"+row_num+"_lensD").val(item.minimum_segment);
			
			/*Select lab*/
			$("#lab_id_"+row_num+"_lensD").val(item.lab_id);
			
			
			try{
				if(item.module_type_id == '2' && typeof(vision_val) !== 'undefined'){
					
					if(vision_val=='ou'){
						addNewRow(item.module_type_id, item, row_num, '', 'od');
						addNewRow(item.module_type_id, item, row_num, '', 'os');
					}
					else{
						addNewRow(item.module_type_id, item, row_num, '', vision_val);
					}
				}
				else{
					addNewRow(item.module_type_id, item, row_num);
				}
			}
			catch(e){
				console.log(e.message);
			}
			 
		 });
	 }else{
		 	$("#upc_name_"+row_num+"_lensD").val('');
			$("#item_name_"+row_num+"_lensD").val('');
			$("#item_id_"+row_num+"_lensD").val('');
			$("#manufacturer_id_"+row_num+"_lensD").val('');
			$("#color_id_"+row_num+"_lensD").val('');
			$("#brand_id_"+row_num+"_lensD").val('');
			$("#style_id_"+row_num).val('');
			$("#shape_id_"+row_num+"_lensD").val('');
			$("#price_"+row_num+"_lensD").val('');
			$("#allowed_"+row_num+"_lensD").val('');
			$("#discount_"+row_num+"_lensD").val('');
			$("#total_amount_"+row_num+"_lensD").val('');
			/*$("#qty_"+row_num+"_lensD").val('');*/
			$("#a_"+row_num+"_lensD").val('');
			$("#b_"+row_num+"_lensD").val('');
			$("#ed_"+row_num+"_lensD").val('');
			$("#bridge_"+row_num+"_lensD").val('');
			$("#fpd_"+row_num+"_lensD").val('');
			$("#use_on_hand_chk_"+row_num+"_lensD").removeAttr('checked');
			$("#order_chk_"+row_num+"_lensD").removeAttr('checked');
			$("#type_id_"+row_num+"_lensD").val('');
			$("#material_id_"+row_num+"_lensD").val('');
			$("#transition_id_"+row_num+"_lensD").val('');
			$("#a_r_id_"+row_num+"_lensD").val('');
			$("#tint_id_"+row_num+"_lensD").val('');
			$("#polarized_id_"+row_num+"_lensD").val('');
			$("#edge_id_"+row_num+"_lensD").val('');
			$("#other_"+row_num+"_lensD").val('');
			$("#uv400_"+row_num+"_lensD").removeAttr('checked');
			$("#frm_pic_lensD").html('');
			img = resizeImage('',180)
			$("#frm_pic_lensD").append(img);
			$("#all_images_lensD").html('');
			$("#div_rel_itm_lensD").css({"display":"none"});
	}
}

function item_price_lensD(item_id){
	var item_id = item_id;
	var dataString = 'action=managestock&item_id='+item_id;
	$.ajax({
		type: "POST",
		url: "lens_price_detail.php",
		data: dataString,
		cache: false,
		success: function(response)
		{
			 var dataArr = (response.trim()!="")?$.parseJSON(response):{};
			 if(dataArr!="")
			 {
				 $.each(dataArr, function(i, item) 
				 {
					$("#lens_item_price_1_lensD").val(item.lens_retail);
					$("#lens_item_price_2_lensD").val(item.material_retail);
					$("#lens_item_price_3_lensD").val(item.a_r_retail);
					//$("#lens_item_price_4_lensD").val(item.transition_retail);
					//$("#lens_item_price_5_lensD").val(item.polarization_retail);
					//$("#lens_item_price_6_lensD").val(item.tint_retail);
					$("#lens_item_price_7_lensD").val(item.uv400_retail);
					$("#lens_item_price_8_lensD").val(item.other_retail);
					//$("#lens_item_price_9_lensD").val(item.progressive_retail);
					//$("#lens_item_price_10_lensD").val(item.edge_retail);
					//$("#lens_item_price_11_lensD").val(item.color_retail);
					$("#lens_item_price_12_lensD").val(item.pgx_retail);
					
					$("#lens_item_allowed_1_lensD").val(item.lens_retail);
					$("#lens_item_allowed_2_lensD").val(item.material_retail);
					$("#lens_item_allowed_3_lensD").val(item.a_r_retail);
					//$("#lens_item_allowed_4_lensD").val(item.transition_retail);
					//$("#lens_item_allowed_5_lensD").val(item.polarization_retail);
					//$("#lens_item_allowed_6_lensD").val(item.tint_retail);
					$("#lens_item_allowed_7_lensD").val(item.uv400_retail);
					$("#lens_item_allowed_8_lensD").val(item.other_retail);
					//$("#lens_item_allowed_9_lensD").val(item.progressive_retail);
					//$("#lens_item_allowed_10_lensD").val(item.edge_retail);
					//$("#lens_item_allowed_11_lensD").val(item.color_retail);
					$("#lens_item_allowed_12_lensD").val(item.pgx_retail);
					
					$("#item_prac_code_1_lensD").val(item.type_prac_code);
					$("#item_prac_code_2_lensD").val(item.material_prac_code);
					$("#item_prac_code_3_lensD").val(item.ar_prac_code);
					//$("#item_prac_code_4_lensD").val(item.transition_prac_code);
					//$("#item_prac_code_5_lensD").val(item.polarized_prac_code);
					//$("#item_prac_code_6_lensD").val(item.tint_prac_code);
					$("#item_prac_code_7_lensD").val(item.uv_prac_code);
					$("#item_prac_code_8_lensD").val(item.other_prac_code);
					//$("#item_prac_code_9_lensD").val(item.progressive_prac_code);
					//$("#item_prac_code_10_lensD").val(item.edge_prac_code);
					//$("#item_prac_code_11_lensD").val(item.color_prac_code);
					$("#item_prac_code_12_lensD").val(item.pgx_prac_code);
					
					get_prac_code_text(item.type_prac_code,"item_prac_code_1_lensD","frm_cont", 2);
					get_prac_code_text(item.material_prac_code,"item_prac_code_2_lensD","frm_cont", 2);
					get_prac_code_text(item.ar_prac_code,"item_prac_code_3_lensD","frm_cont", 2);
					//get_prac_code_text(item.transition_prac_code,"item_prac_code_4_lensD","frm_cont", 2);
					//get_prac_code_text(item.polarized_prac_code,"item_prac_code_5_lensD","frm_cont", 2);
					//get_prac_code_text(item.tint_prac_code,"item_prac_code_6_lensD","frm_cont", 2);
					get_prac_code_text(item.uv_prac_code,"item_prac_code_7_lensD","frm_cont", 2);
					get_prac_code_text(item.other_prac_code,"item_prac_code_8_lensD","frm_cont", 2);
					//get_prac_code_text(item.progressive_prac_code,"item_prac_code_9_lensD","frm_cont", 2);
					//get_prac_code_text(item.edge_prac_code,"item_prac_code_10_lensD","frm_cont", 2);
					//get_prac_code_text(item.color_prac_code,"item_prac_code_11_lensD","frm_cont", 2);
					get_prac_code_text(item.pgx_prac_code,"item_prac_code_12_lensD","frm_cont", 2);
					
				 });
				 calculate_all();
			 }
			 else
			 {
				// $("#stock_form")[0].reset();
			 }
			 
			 $.each(get_details_by_upc_data_arr, function(i, item) 
			 {
				lens_row_display(item.type_id,'lens_display','in_lens_type');
				//lens_row_display(item.progressive_id,'progressive_display','in_lens_progressive');
				lens_row_display(item.material_id,'material_display','in_lens_material');
				//lens_row_display(item.transition_id,'transition_display','in_lens_transition');
				lens_row_display(item.a_r_id,'a_r_display','in_lens_ar');
				//lens_row_display(item.tint_id,'tint_display','in_lens_tint');
				//lens_row_display(item.polarized_id,'polarization_display','in_lens_polarized');
				//lens_row_display(item.edge_id,'edge_display','in_lens_edge');
				//lens_row_display(item.color,'color_display','in_lens_color');
				lens_row_display(item.uv_check,'uv400_display','in_item_price_details');
				itemized_other_display('','other_display','in_item_price_details');
				lens_row_display(item.pgx_check,'pgx_display','in_item_price_details');
			 });
		}
	}); 
}

var ptwin="";
function printpos(order_id,section)
{
	var orderId=order_id;
	
	var order_sel_ids='';
	if($(".getchecked").length){	
	var len = $(".getchecked:checked");
	if(len.length > 0 )
	{		
		for(var i=0;i<len.length;i++)
		{			
			if($(len[i]).is(':checked'))
			{
				order_sel_ids=order_sel_ids+$(len[i]).val()+',';
			}
		}
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/print_pos.php?order_sel_ids='+order_sel_ids, "ptwindow","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=0");
	ptwin.focus();
	}
	catch(e) 
	{
		//location.target = "_self";
		//location.href = url;
	}	
	}
	else{
		top.falert('Please select a record');
	}
	}
	else{
		top.WindowDialog.closeAll();
		var ptwin=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/print_pos.php?order_id='+order_id+'&section='+section, "ptwindow","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=0");
		ptwin.focus();
	}
}

function printSelPos(order_id,order_det_ids,section)
{
	var order_det_ids='';
	var len = $(".getchecked:checked");
	if(len.length > 0 )
	{
		for(var i=0;i<len.length;i++)
		{
			if($(len[i]).is(':checked'))
			{order_det_ids=order_det_ids+$(len[i]).val()+',';}
		}
		try 
		{
		window.opener.top.WindowDialog.closeAll();
	var ptwin=window.opener.top.WindowDialog.open('Add_new_popup','../../patient_interface/print_pos.php?order_id='+order_id+'&section='+section+'&order_det_ids='+order_det_ids, "ptwindow","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=0");
		ptwin.focus();
		}
		catch(e) 
		{
			//location.target = "_self";
			//location.href = url;
		}
	}else{falert('Please select a record');}
}
function patientReceipt(order_id,section)
{
	try 
	{
	top.WindowDialog.closeAll();
	var ptwin=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/patient_interface/print_pos_patient.php?order_id='+order_id+'&section='+section, "ptwindow","width=1200,height=700,location=0,menubar=0,scrollbars=1,status=1,resizable=0");
	ptwin.focus();
	}
	catch(e) 
	{
		//location.target = "_self";
		//location.href = url;
	}

}
function item_reorder_callback(result)
{
	if(result==true)
	{
		$("#save").val('');
		$("#cancel").val('');
		$("#reorder").val('reorder');
		$("#firstform1").submit();	
	}
	else
	{
		return false;	
	}
}

function item_reorder()
{
	var count=document.getElementsByName('update_item_id[]').length;
	var item1=document.getElementsByName('update_item_id[]');
	var check="";
	for(i=0;i<count;i++)
	{
		if(item1[i].checked==true)
		{
			check=1;
		}
	}
	
	if(check==1)
	{
		top.fconfirm('Reorder the selected items  <br> Please confirm',item_reorder_callback);
	}
	else
	{
		falert("Please select a record");
	}
}

function changeLensPosLabel(){
	var rows = $('.posTable tr[id^="2_"]:not(".multiVals")');
	var prevId = "";
	var j = 0;
	var optValue = "";
	
	$(rows).each(function(i, val){
		var id = $(val).attr('id');
		id = id.charAt(2);
		if(id!=prevId){
			j = 0;	
		}
		j++;
		prevId = id;
		
		itemkey = $(val).find('input[id^="lens_item_detail_name_'+id+'_"]').val();
		vision = $(val).find('input.row_vision_value').val();
		
		if(itemkey!="pgx" && itemkey!="uv400"){
			if(itemkey=="lens"){itemkey="type";}
			else if(itemkey=="polarization"){itemkey="polarized";}
			valelem = $("#"+itemkey+"_id_"+id+"_lensD").val();
			opt = $('#'+itemkey+'_id_'+id+'_'+vision+'_lensD option:selected').text();
			$(val).find(".itemnameDisp").val(opt);
		}
	});
	
	/*Material Rows*/
	var rows = $('.posTable tr[id*="_material_"]');
	var prevId = "";
	var j = 0;
	var optValue = "";
	$(rows).each(function(i, val){
		var id = $(val).attr('id');
		id = id.charAt(2);
		if(id!=prevId){
			j = 0;	
		}
		j++;
		prevId = id;
		
		itemkey = $(val).find('input[id^="lens_item_detail_name_'+id+'_"]').val();
		vision = $(val).find('input.row_vision_value').val();
		
		if(itemkey!=""){
			if(itemkey.substr(0, 8)=="material"){itemkey="material";}
			valelem = $("#"+itemkey+"_id_"+id+"_lensD").val();
			opt = $('#'+itemkey+'_id_'+id+'_'+vision+'_lensD option:selected').text();
			$(val).find(".itemnameDisp").attr('title', opt);
			
			if( $.trim($(val).find(".itemnameDisp").val()) == '' )
				$(val).find(".itemnameDisp").val($(val).find(".pracodefield").attr('title'));
		}
	});
	
	/*Fill Values for multiselect fields*/
	var mulstiselects = $("a.multiSelect");
	//console.log(mulstiselects);
	$.each(mulstiselects, function(i, obj){
		id = $(obj).attr('id');
		idV = id.split('_id_');
		tb = idV[0];
		count = idV[1].charAt(0);
		rowId = "2_"+count+"_"+tb;
		vision = idV[1].split('_');
		vision = vision[1];
		
		
		selectedVals = $("#"+id).selectedValuesString();
		
		pracCodes = prac_by_type_multi(selectedVals,'in_lens_ar');
		pracCodes = pracCodes.split(";");
		selectedVals = selectedVals.split(',');
		
		var typeRows = $('.posTable tr[id^="'+rowId+'"][id$="_'+vision+'"].multiVals');
		//console.log(typeRows);
		if(typeRows.length>0){
			$.each(typeRows, function(ri, robj){
				rid = $(robj).attr('id');
				
				rItemId1	= rid.substr((rowId.length)+1);
				rItemId		= rItemId1.split('_');
				rItemId		= rItemId[0];
				
				itemIndex = $.inArray(rItemId, selectedVals);
				/*pracIndex !='-1' && itemIndex!="-1";*/ /*Prac code Check removed to make compatible with Previous Data*/
				if(itemIndex!="-1"){
					var optText = $(obj).next('.multiSelectOptions').find('input[value="'+rItemId+'"]').parent('label').text();
					$(robj).find(".itemnameDisp").val(optText);
					$(robj).find(".qty_cls").val($( '#qty_'+count+'_lensD' ).val());
					$(robj).find(".del_status").val(0);
					$(robj).removeClass('hideRow');
				}
				else{
					var lensItemCounter = parseInt($("#lens_item_count_"+count+"_lensD").val());
					
					//$(robj).find(".pracodefield").val("");
					//$(robj).find(".pracodefield").attr("title","");
					//$(robj).find(".price_cls").val('0.00');
					//$(robj).find(".price_disc").val('0.00');
					$(robj).find(".qty_cls").val(0);
					$(robj).find(".price_total").val('0.00');
					$(robj).find(".allowed_cls").val('0.00');
					$(robj).find("#pos_lens_item_name_disp_"+count+"_lensD").val('');
					$(robj).addClass('hideRow');
					
					var delStatusField = $(robj).find(".del_status");
					if(delStatusField.length>0){
						$(delStatusField[0]).val("1"); /*Set Del Status*/
					}
					else{
						/*Remove Pos Row for Unselected Value*/
						$(robj).remove();
						lensItemCounter--;
						$("#lens_item_count_"+count+"_lensD").val(lensItemCounter);
					}
				}
			});
			
		}
	});
	
	/*Pt. Frame's Description*/
	var ptFrameradios = $('.ptFrame');
	$.each(ptFrameradios, function(i, obj){
		if($(obj).is(':checked'))
			$('#itemDescription_frame_'+(i+1)).val('Pt Own Frame');
		else
			$('#itemDescription_frame_'+(i+1)).val($('#item_name_'+(i+1)).val());
	});
	
	var total_rows = $('.posTable > tbody > tr[id^="2_"]');
	var countArr = [];
	
	if(total_rows.length > 0){
		$.each(total_rows, function(i, obj){
			id = $(obj).attr('id').split('_');
			if(typeof(countArr[id[1]])=="undefined"){
				countArr[id[1]] = 1;
			}
			else{
				countArr[id[1]]++;
			}
		});
		$.each(countArr, function(i, value){
			$('#lens_item_count_'+i+'_lensD').val(value);
		});
	}
}

function delPosRow_callBack(result)
{
	if(result==true)
	{
		top.WindowDialog.closeAll();
		var ptwin=top.WindowDialog.open('Add_new_popup',top.WRP+'/interface/admin/order/cancel_order_ajax.php?source=itemDetailPage&frameName=admin_iframe','prod_popup', 'width=800,height=200,left=300,scrollbars=no,top=100,fullscreen=0,resizable=0');	
	}
}

function delPosRow_callBack2(result)
{
	if(result==true){
		/*Send Cancel Item Request by Item Detail Id*/
		var itemId = $("#delItemId").val();
		$.ajax({
			url: top.WRP+"/interface/patient_interface/ajax.php",
			type: "POST",
			data: "type=cancelOrderItem&itemId="+itemId,
			success: function(resp){
				window.location.reload();
			}
		});
	}
}

function delPosRow(moduleType, count, key, vision){
	
	if((moduleType==1 || moduleType ==2) && order_edit_btn_status == false){
		return true;
	}
	
	
	cancelFlag = false;
	pageName = (typeof(pageName)!="undefined")?pageName:"";
	if(pageName=="otherSelection"){
		posRows = $('.posTable>tbody>tr[id]:not(.hideRow)');
		if(posRows.length==1){
			cancelFlag = true;
		}
	}
	else if(moduleType==3){
		posRows = $('.posTable>tbody>tr[id^="'+moduleType+'_"]:not(.hideRow)');
		if(posRows.length==1){
			cancelFlag = true;
		}
	}
	else if(moduleType==1){
		posRows = $('.posTable>tbody>tr[id^="'+moduleType+'_"]:not(.hideRow)');
		if(posRows.length==1){
			cancelFlag = true;
		}
	}
	
	if(cancelFlag){
		top.fconfirm("Do You want to delete complete order?",delPosRow_callBack);
		return false;
	}
	
	if(moduleType!=2){
		
		cnFlag = true;
		if(moduleType==1 && pageName!="otherSelection"){
			var frameId = $("#pos_order_detail_id_"+count).val();
			$("#delItemId").val(frameId);
			cnFlag = top.fconfirm("You are deleting Frame from order. The lens associated with it will also be deleted.",delPosRow_callBack2);
		}
		else if(moduleType==3 && pageName!="otherSelection"){
			
/*Contact Lens Delete*/
			
			vision = ( typeof(vision) == 'undefined' ) ? '' : vision;
			
			/*POS Rows Count (OD and OS) for the selected Item block*/
			var itemPosRows = $('table.posTable > tbody > tr[id]:not(.hideRow) > input[id^="pos_order_detail_id_'+count+'"]');
			
			if(itemPosRows.length==1){
				/*If only one POS row for the Item block, then mark complete order Item/detail as deleted*/
				
				$('#delItemId').val($('#order_detail_id_'+count).val());
				cnFlag = top.fconfirm('You are deleting a contact Lens from order. The Disindectant associated with it will also be deleted.', delPosRow_callBack2);
			}
			else{
				
				/*Clear Item details for the selected Vision type for the Item block*/
				var selectBlankOption = $('<option>').val(0).text('Please Select');
				
				$('#item_id_'+count+vision).val('');
				$('#upc_name_'+count+vision).val('');
				$('#item_name_'+count+vision).val('');
				$('#item_vendor_'+count+vision).html(selectBlankOption).val(0);
				$('#brand_id_'+count+vision).html(selectBlankOption.clone()).val(0);
				$('#manufacturer_id_'+count+vision).val(0).trigger('change');
				$('#item_prac_code_'+count+vision).val('');
				$('#allowed_'+count+vision).val(0);
				$('#trial_chk_'+count+vision).attr('checked', false);
				
				$('#price_'+count+vision).val('0.00');
				$('#pos_price_'+count+vision).val('0.00');
				$('#rtl_price_'+count+vision).val('0.00');
				$('#total_amount_'+count+vision).val('0.00');
				$('#pos_allowed_'+count+vision).val('0.00');
				$('#discount_'+count+vision).val(0);
				$('#item_overall_discount_'+count+vision).val(0);
				$('#tax_applied_'+count+vision).attr('checked', false);
				$('#tax_p_'+count+vision).val('');
				$('#tax_v_'+count+vision).val('0.00');
				$('#ins_amount_'+count+vision).val('0.00');
				$('#pt_paid_'+count+vision).val('0.00');
				$('#pt_resp_'+count+vision).val('0.00');
				$('#discount_code_'+count+vision).val(0);
				$('#ins_case_id_'+count+vision).val(0);
				
				/*Unset Quantity*/
				if(vision != ""){
					$('#qty_'+count).val(0);
				}
				else{
					$('#qty_right_'+count).val(0);
				}
				/*POS Row Quantity*/
				$('#pos_qty_'+count+vision).val(0);
				
				/*Hide POS Row*/
				if( $('#pos_order_detail_id_'+count+vision).val() == '' ){
					$('table.posTable > tbody > tr#3_'+count+vision).remove();
				}
				else{
					$('table.posTable > tbody > tr#3_'+count+vision).addClass('hideRow');
				}
			}
			cnFlag = false;
			calculate_all();
/*End Contact Lens Delete*/
		}
		
		if(cnFlag){
		
			if(moduleType==9){
				$('#del_status_'+moduleType+'_'+count).val(1);
				$(".posTable>tbody>tr#"+moduleType+"_"+count).addClass("hideRow");
				calculate_all();
			}
			else if(pageName!="otherSelection"){
				$("#upc_id_"+count).val('');
				$("#del_status_"+count).val(1);
				$("#upc_name_"+count).val('').trigger('change');
			}
			if(moduleType==1){
				
				$("#upc_id_"+count+"_lensD").val('');
				$("#del_status_"+count+"_lensD").val(1);
				$("#upc_name_"+count+"_lensD").val('').trigger('change');
				$('.posTable>tbody>tr[id^="2_'+count+'"]').addClass("hideRow");
			}
			
			if(pageName!="otherSelection"){
				$(".posTable>tbody>tr#"+moduleType+"_"+count).addClass("hideRow");
			}
			else{
				savedItems = $(".posTable>tbody>tr#"+moduleType+"_"+count).find('input[id^="del_status_"]');
				if(savedItems.length>0){
					$(savedItems[0]).val(1);
					$(".posTable>tbody>tr#"+moduleType+"_"+count).addClass("hideRow");
				}
				else{
					$(".posTable>tbody>tr#"+moduleType+"_"+count).remove();
				}
			}
			
			if(moduleType==3){
				$("div.multiSection#contactlens_"+count).hide();
			}
			else if(moduleType==1){
				$("div.all_data.refData#all_data_"+count).hide();
			}
		}
		
	}
	else{
		if(pageName=="otherSelection"){
			posRows = $('.posTable>tbody>tr[id^="'+moduleType+'_"]:not(.hideRow)');
			if(posRows.length==1){
				alert("There must be minimum one item in the order.");
				return false;
			}else{
				if(moduleType==2){
					$("#"+moduleType+"_"+count+"_"+key+"_display").remove();
				}
				else{
					$("#"+key+"_id_"+count+"_lensD").remove();
				}
			}
		}else{
			var subKey = key.substring(0,4);
			var sflag = false;
			
			if(key=="lens"){key="type";}
			else if(key=="design" || key=="material" || key=="diopter"  || key=="oversize"){
				var ar_rows = $('.posTable tr[id^="2_'+count+'_'+key+'"][id$="_'+vision+'"]');
				$.each(ar_rows, function(i, obj){
					$(obj).find(".pracodefield").val("");
					$(obj).find(".pracodefield").attr("title","");
					$(obj).find(".price_cls").val('0.00');
					$(obj).find(".price_disc").val('0.00');
					$(obj).find(".qty_cls").val(0);
					$(obj).find(".price_total").val('0.00');
					$(obj).find(".allowed_cls").val('0.00');
					$(obj).addClass('hideRow');
					
					var delStatusField = $(obj).find(".del_status");
					if(delStatusField.length>0){
						$(delStatusField[0]).val("1"); /*Set Del Status*/
					}
					else{
						$(obj).remove();	
					}
				});
			}
			else if(subKey=="a_r_"){
				
				var arvalRem = key.split("_");	/*Value to be deselected from multiselect*/
				arvalRem = arvalRem.pop();
				
				var arvals = $("#a_r_id_"+count+"_"+vision+"_lensD").selectedValuesString();	/*Currenct Selected values*/
				arvals = arvals.split(',');
				
				var valIndex = arvals.indexOf(arvalRem);
				
				if(valIndex!="-1"){
					arvals.splice(valIndex, 1);	/*New selected values list*/
					arvals = arvals.join(',');
					$("#a_r_id_"+count+"_"+vision+"_lensD").changeSelected(arvals);
					var row = $("#2_"+count+"_"+key+"_display_"+vision);
					
					$(row).find(".pracodefield").val("");
					$(row).find(".pracodefield").attr("title","");
					$(row).find(".price_cls").val('0.00');
					$(row).find(".price_disc").val('0.00');
					$(row).find(".qty_cls").val(0);
					$(row).find(".price_total").val('0.00');
					$(row).find(".allowed_cls").val('0.00');
					$(row).addClass('hideRow');
					
					var delStatusField = $(row).find(".del_status");
					if(delStatusField.length>0){
						$(delStatusField[0]).val("1"); /*Set Del Status*/
					}
					else{
						$(row).remove();	
					}
				}
			}
			
			if(!sflag){
				var tempKey = key.split('_');
				if(tempKey[0] === 'material'){
					$("#"+tempKey[0]+"_id_"+count+"_"+vision+"_lensD").val(0).trigger('change');
				}
				else{
					$("#"+key+"_id_"+count+"_"+vision+"_lensD").val(0).trigger('change');
				}
			}
		}
		calculate_all();
	}
	
	if(pageName=="otherSelection"){
		calculate_all();
	}
}
function currencySymbols(){
	var currencyFields = $("input.currency:not(.currencyAdded)");
	$(currencyFields).each(function(i,val){
		width = $(val).width()-11;
		height = $(val).outerHeight();
		if(height!="0"){
			$('<span></span>').addClass('currency_symbol').css('height',height+'px').text(top.CURRENCY_SYMBOL).insertBefore(val);
			$(val).css("width",width+"px");
			$(val).addClass("currencyAdded");
		}
	});
}

function lens_add_multi_pos(index, pos_value, data, pro, pro_cont, vision){
	
	/*Discount code for New Row to be added*/
	var mainDiscountCode = $("#main_discount_code_1").val();
	
	var multi_vals_prac = (String(pos_value.prac_code)).split(";");
	var multi_vals_retail = (String(pos_value.retail)).split(";");
	var multi_vals_wholesale = (String(pos_value.wholesale)).split(";");
	var multi_vals_item_val = (String(pos_value.detail_id)).split(",");
	
	var lensD_cont = "_lensD";
	var details = "";
	var lens_quantity = $("#qty_"+pro_cont+"_lensD").val(); 
	var lens_discount = $("#discount_hidden_"+pro_cont+"_lensD").val();
	
	$.each(multi_vals_prac, function(key, pracCode){
		var existingRow = $('#2_'+pro_cont+'_'+index+'_'+multi_vals_item_val[key]+'_display_'+vision);
		if(existingRow.length>0){
			var upcField = $(existingRow).find("#pos_upc_name_"+pro_cont+"_lensD");
			if(upcField.length>0){
				$(upcField[0]).val($("#upc_name_"+pro_cont+"_lensD").val());
			}
			
			$(existingRow).find(".pracodefield").val(pracCode);
			$(existingRow).find(".pracodefield").attr("title",pracCode);
			
			$(existingRow).find(".price_cls").val(parseFloat(multi_vals_retail[key]).toFixed(2));
			$(existingRow).find(".allowed_cls").val(parseFloat(multi_vals_retail[key]).toFixed(2));
			$(existingRow).find(".qty_cls").val(lens_quantity);
			$(existingRow).find(".price_disc_per_proc").val(lens_discount);
			$(existingRow).find('.del_status').val("0");
			
			if(top.main_iframe.admin_iframe.remakeFlag){
				tax_p = parseFloat($(existingRow).find("input.tax_p").val());
				if(tax_p>0)
					$(existingRow).find(".tax_applied").attr('checked', true);
			}
			
			$(existingRow).removeClass('hideRow');
		}
		else{
			details+='<tr id="2_'+pro_cont+'_'+index+'_'+multi_vals_item_val[key]+'_display_'+vision+'" class="multiVals">';
			
			/*Hidden Fields*/
			details+='<!--td-->';
				//details+='<input type="hidden" name="pos_dx_code_'+pro_cont+lensD_cont+'" value="" />';
				if(pro==1){
					details +='<input readonly style="width:100%;" type="hidden" name="pos_upc_name_'+pro_cont+lensD_cont+'" id="pos_upc_name_'+pro_cont+lensD_cont+'" value="'+data.upc_code+'"  onchange="javascript:upc(document.getElementById(\'upc_id_'+pro_cont+lensD_cont+'\'), \''+pro_cont+lensD_cont+'\');" />';
				}
				details+='<input type="hidden" name="pos_order_chld_id_'+pro_cont+lensD_cont+'" value="" />';
				details+='<input type="hidden" name="pos_order_detail_id_'+pro_cont+lensD_cont+'" id="pos_order_detail_id_'+pro_cont+lensD_cont+'" value="" />';
				details+='<input type="hidden" name="lens_item_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" value="'+pro+'" />';
				details+='<input type="hidden" name="lens_item_detail_name_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_detail_name_'+pro_cont+'_'+pro+lensD_cont+'" value="'+index+'_'+multi_vals_item_val[key]+'" />';
				details+='<input type="hidden" name="lens_price_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_price_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" value="" />';
				details+='<input type="hidden" name="pos_module_type_id_'+pro_cont+lensD_cont+'" value="2" />';
				details+='<input type="hidden" name="pos_upc_id_'+pro_cont+lensD_cont+'" id="pos_upc_id_'+pro_cont+lensD_cont+'" value="">';
			details+='<!--/td-->';
			
			details+='<td>';
				details+='<span class="vis_type vision_'+vision+'">'+vision+'</span>';
				details+='<input type="hidden" name="pos_lens_item_vision_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_lens_item_vision_'+pro_cont+'_'+pro+lensD_cont+'" value="'+vision+'" class="row_vision_value">';
			details+='</td>';
			
			details+='<td><input readonly style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_lens_item_name_'+pro_cont+lensD_cont+'" value="'+pos_value.name+'"/></td>';
	
			/*Only for Frames & Lenses*/
			details+='<td><input readonly style="width:100%;" type="text" class="itemnameDisp" name="pos_lens_item_name_disp_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_lens_item_name_disp_'+pro_cont+'_'+pro+lensD_cont+'" value="" /></td>';
			
			details+='<td><input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_'+pro_cont+'_'+pro+lensD_cont+'" id="item_prac_code_'+pro_cont+'_'+pro+lensD_cont+'" value="'+pracCode+'" title="" /></td>';
			/*onChange="show_price_from_praccode(this,\'price_'+pro_cont+'_'+pro+lensD_cont+'\',\'pos\'); calculate_all();"*/
			
			details+='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_price_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_price_'+pro_cont+'_'+pro+lensD_cont+'" value="'+parseFloat(multi_vals_retail[key]).toFixed(2)+'" class="price_cls currency" onChange="calculate_all();"/></td>';

/*onChange="changeQty(\'2\', this.value, \''+pro_cont+'\');"*/
			details+='<td><input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_qty_'+pro_cont+'_'+pro+lensD_cont+'_lensD" value="'+lens_quantity+'" onChange="calculate_all();" onKeyUp="validate_qty(this);" />';
			details+='<input type="hidden" class="rqty_cls" name="pos_qty_right_'+pro_cont+lensD_cont+'" value="0" /></td>';
			
			details+='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_allowed_'+pro_cont+'_'+pro+lensD_cont+'" value="'+parseFloat(multi_vals_retail[key]).toFixed(2)+'" class="allowed_cls currency" onChange="calculate_all();"/></td>';
			
			/*Discount*/
				var disc="";
				var dis_till = (data.discount_till).split("-");
				var cur_date = new Date();
				var dis_date = new Date();
				dis_date.setFullYear(parseInt(dis_till[0]), parseInt(dis_till[1])-1, parseInt(dis_till[2]));
				if(cur_date>dis_date && data.discount_till!="0000-00-00"){
					disc=0;
				}
				else{
					disc=data.discount;
				}
			/*Discount End*/
			
			details+='<td style="display:none"><input readonly style="width:100%; text-align:right;" type="text" name="lens_item_total_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_total_amount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="price_total currency"  onChange="calculate_all();"/>';
				/*Tax Calculations*/
				tax_applied = facTax[2]>0;
				details +='<input type="hidden" name="tax_p_'+pro_cont+'_'+pro+lensD_cont+'" id="tax_p_'+pro_cont+'_'+pro+lensD_cont+'" class="tax_p" value="'+facTax[2]+'" />';
				details +='<input type="hidden" name="tax_v_'+pro_cont+'_'+pro+lensD_cont+'" id="tax_v_'+pro_cont+'_'+pro+lensD_cont+'" class="tax_v" value="0.00" />';
				/*End Tax Calculations*/
			details+='</td>';
			
			details+='<td><input style="width:100%; text-align:right;" type="text" name="ins_amount_'+pro_cont+'_'+pro+lensD_cont+'" id="ins_amount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"/></td>';
			
			details+='<td>';
				/*Line item's share in overall discount*/
				details+='<input type="hidden" name="lens_item_overall_discount_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_overall_discount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="item_overall_disc" />';
				details+='<input type="hidden" name="lens_item_discount_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_discount_'+pro_cont+'_'+pro+lensD_cont+'" value="'+((disc=="")?0:disc)+'" onChange="calculate_all();" class="price_disc_per_proc"/>';
				details+='<input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_read_discount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />';
			details+'</td>';
			
			details+='<td><input style="width:100%; text-align:right;" type="text" name="pt_paid_'+pro_cont+'_'+pro+lensD_cont+'" id="pt_paid_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"/></td>';
			
			details+='<td><input style="width:100%; text-align:right;" type="text" name="pt_resp_'+pro_cont+'_'+pro+lensD_cont+'" id="pt_resp_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="resp_cls currency" readonly /></td>';
			
			details+='<td><select name="discount_code_'+pro_cont+'_'+pro+lensD_cont+'" id="discount_code_'+pro_cont+'" class="text_10 disc_code dis_code_class" style="width:100%;"><option value="0">Please Select</option>';
				defDisc = "";
				$.each(discCodes, function(di, dval){
					defDisc = (di==mainDiscountCode)?'selected="selected"':"";
					details += '<option value="'+di+'" '+defDisc+'>'+dval+'</option>';
				});
			
			details+='</select></td>';
			details+='<td><select name="ins_case_id_'+pro_cont+'_'+pro+lensD_cont+'" id="ins_case_id_'+pro_cont+'_'+pro+lensD_cont+'" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp(\''+pro_cont+'_'+pro+lensD_cont+'\');"><option value="0">Self Pay</option>';
		$.each(insCases, function(insVal, insKey){
			insSelected = (insKey==top.main_iframe.ptVisionPlanId)?' selected="selected"':'';
			details +='<option value="'+insKey+'"'+insSelected+'>'+insVal+'</option>';
		});
		details +='</select><input type="hidden" name="del_status_'+pro_cont+'_'+pro+lensD_cont+'" id="del_status_'+pro_cont+'_'+pro+lensD_cont+'" value="0" class="del_status">';
			
			details+= '</td>';
			
			details+='<td><input type="checkbox" class="tax_applied" name="tax_applied_'+pro_cont+'_'+pro+lensD_cont+'" id="tax_applied_'+pro_cont+'_'+pro+lensD_cont+'" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" /></td>';
			
			details+= '<td><img class="delitem" src="'+top.WRP+'/images/del.png" onClick="delPosRow(\'2\', \''+pro_cont+'\', \''+index+'_'+multi_vals_item_val[key]+'\', \''+vision+'\');" /></td>';
			
			details+= '</tr>';
			pro++;
		}
	});
	details = pro+"~~~"+pro_cont+"~~~"+details;
	return(details);
}

$(document).ready(function(){
	currencySymbols();
});

/* 
 * Check Tax Filed's value for only interger or Float Values.
 * No special symbols are allowed
 */
function checkTaxVal(obj){
	var value = $(obj).val();
	/*Allow only numerics and .*/
	value = value.replace(/[^\d\.]/g, '');; 
	/*Set Value of the element*/
	$(obj).val(value);
}

/*Change Discount in Pos or item details*/
function discountChanged(obj){
	var name = $(obj).attr('name');
	var value = $(obj).val();
	var elements = document.getElementsByName(name);
	$(elements).val(value);
}

/*
 * Function: validate_qty
 * Purpose: Validate quantity for numeric Values only
 */
function validate_qty(obj){
	var qty = $(obj).val();
	qty = qty.trim();
	if(qty!=""){
		qty = qty.replace(/[^\d.]/, '');	/*Strip non int values*/
		if(qty=="")qty=1;
	}
	$(obj).val(qty);
}

/*
 * Function: parse_float()
 * Prpose: parse values to float with Fix for blank value
 */
function parse_float(obj){
	var val = $(obj).val();
	val = parseFloat(val);
	if(isNaN(val)){
		val = "0.00";
	}
	val = parseFloat(val).toFixed(2);
	$(obj).val(val);
}
  
/*Change value of price columns to loat*/
function convert_float(obj){
	var value = parseFloat($(obj).val());
	value = (value=="" || isNaN(value))?0.00:value;
	$(obj).val(value.toFixed(2));
}

/*Calculate Retail Price by formula*/
function calculate_retail_price(formula, wholesale, purchase){
	
	var resp = 0;
	if(formula != ''){
		
		if( formula.indexOf('W') > -1 ){
			resp = formula.replace(/W/g, parseFloat(wholesale));
			formula = resp;
		}
		
		if( formula.indexOf('P') > -1 ){
			resp = formula.replace(/P/g, parseFloat(purchase));
		}
		
		if( resp == 0 && formula != ''){
			resp = parseFloat($.trim(formula));
		}
		
		resp = eval(resp);
	}
	return resp;
}

function validate_formula(obj){
	
	var value = $.trim($(obj).val());
	value = value.toUpperCase();
	$(obj).val(value);
	
	var resp = value;
	if( value!= '' ){
		
		if( value.indexOf('W') > -1 ){
			resp = value.replace(/W/g, parseFloat(1));
			value = resp;
		}
		
		if( value.indexOf('P') > -1 ){
			resp = value.replace(/P/g, parseFloat(1));
		}
	}
	
	try{
		resp = eval(resp);
		resp = parseFloat(resp);
		
		if( isNaN(resp) )
			throw 'Invalid';
	}
	catch(error){
		top.falert('Please enter a valid formula.');
		$(obj).val('');
	}
}
/*function to show hide tool tip */
function tooltip(obj,act){
	//if(act=='show')
//	{
		var obj = $(obj);
		var objPosition = obj.position();
		var objheight = obj.outerHeight();
		var objWidth = obj.width();
		
		if(objheight && typeof(objheight) !== 'undefined'){
			objPosition.top = parseInt(objPosition.top - objheight);
		}
		
		if($('.toolDetails').length == 0){
			$('body').append('<div class="toolDetails"><span>'+obj.data('title')+'</span></div>');
			//$('.toolDetails span').html(obj.data('title'));
			$('.toolDetails').css({
				'top' : objPosition.top,
				'left' : objPosition.left,
				'display':act
				//'width':objWidth
			});	
		}else{
			$('.toolDetails').remove();
			tooltip(obj, act);
		}
		
		
		
		//var elemtnt = $(obj).siblings('.toolDetails')[0];
//		var height = $(elemtnt).height();
//		$(elemtnt).css('top', '-'+height+'px');
//		$(elemtnt).stop().fadeTo(500,1);
	//}
//	else
//	{
//		$(obj).siblings('.toolDetails').stop().fadeTo(500,0).hide();
//	}
}