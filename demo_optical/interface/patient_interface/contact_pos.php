<?php 
require_once(dirname('__FILE__')."/../../config/config.php"); 
require_once(dirname('__FILE__')."/../../library/classes/functions.php"); 

$arrManufac=array();
$manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where frames_chk='1' and del_status='0'";
$manu_detail_res = imw_query($manu_detail_qry);
$manu_detail_nums = imw_num_rows($manu_detail_res);
if($manu_detail_nums > 0)
{	
	while($manu_detail_row = imw_fetch_array($manu_detail_res)) {
		$arrManufac[$manu_detail_row['id']] = $manu_detail_row['manufacturer_name'];
	}	
} 

$cl_arrManufac=array();

$cl_manu_detail_qry = "select id, manufacturer_name from in_manufacturer_details where cont_lenses_chk='1' and del_status='0'";

$cl_manu_detail_res = imw_query($cl_manu_detail_qry);
$cl_manu_detail_nums = imw_num_rows($cl_manu_detail_res);
if($cl_manu_detail_nums > 0)
{	
	while($cl_manu_detail_row = imw_fetch_array($cl_manu_detail_res)) {
		$cl_arrManufac[$cl_manu_detail_row['id']] = $cl_manu_detail_row['manufacturer_name'];
	}	
} 


$ins_opt="";
$getins_data="SELECT insct.case_name,insc.ins_caseid, insc.ins_case_type 
				FROM insurance_case_types insct 
				JOIN insurance_case insc ON (insc.ins_case_type=insct.case_id AND insc.case_status ='Open') 
				JOIN insurance_data insd ON (insd.ins_caseid=insc.ins_caseid AND insd.provider >0) 
				JOIN insurance_companies inscomp ON (inscomp.id=insd.provider AND inscomp.in_house_code !='n/a') 
				WHERE insc.patient_id='$patient_id'
				GROUP BY insc.ins_caseid 
				ORDER BY insc.ins_case_type";
$getins_data_qry = imw_query($getins_data);
while($getins_data_row = imw_fetch_array($getins_data_qry)){
	$insCasesArr = $getins_data_row['case_name'].'-'.$getins_data_row['ins_caseid'];
	$ins_case_arr[$getins_data_row['ins_caseid']]=$insCasesArr;
}

	    $sel_qry=imw_query("select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and del_status='0' and module_type_id!='8' order by id asc");
		$top_cont=0;
		while($sel_order_row=imw_fetch_array($sel_qry))
		{
			$top_cont++;
			$sel_order_data[$top_cont]=$sel_order_row;
			$sel_order_module_data[$sel_order_row['module_type_id']][]=$sel_order_row;
		}
	  ?>
 <script>
 function upc(upc_code, num)
{
	var ucode = $.trim(upc_code.value);
	var dataString = 'action=managestock&code='+ucode;
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
				 	$("#upc_name_"+num).val(item.upc_code);
					$("#item_name_"+num).val(item.name);
					$("#price_"+num).val(item.retail_price);
					$("#discount_"+num).val(item.discount);
					$("#total_amount_"+num).val(item.amount);
					$("#item_id_"+num).val(item.id);
					$("#module_type_id_"+num).val(item.module_type_id);
				 });
			 }
			 else
			 {
				// $("#stock_form")[0].reset();
			 }
			 calculate_all();
		}
	});
	
	 var getRows = $("#last_cont").val();
	 if(getRows==num)
	 {
		// addrow();
	 }
}
 function auto_select_dis_code(dis_cd)
{
	$(".dis_code_class").val(dis_cd);
}
function auto_select_ins(sel_val)
{
	$(".ins_case_class").val(sel_val);
	var payed_clas=trans_amt=total_clas=0;
	$('.ins_case_class').each(function(index, element) {
		payed_clas = parseFloat($('.payed_cls').get(index).value);
		total_clas = parseFloat($('.price_total').get(index).value);
		trans_amt = total_clas - payed_clas;
		if($(this).val()=="")
		{
			$('.ins_amt_cls').get(index).value = "0.00";
			$('.resp_cls').get(index).value = trans_amt.toFixed(2);
		}
		else if($(this).val()!="" && $(this).val()>0)
		{
			$('.ins_amt_cls').get(index).value = trans_amt.toFixed(2);
		}
	});
	calculate_all();
}
 function pospage_title(pr_cnt)
{
	var oth_price = $("#price_"+pr_cnt).val();
	var oth_dis = $("#discount_"+pr_cnt).val();
	var oth_lqty = $("#qty_"+pr_cnt).val();
	var oth_rqty = $("#qty_right_"+pr_cnt).val();
	var oth_qty = parseInt(oth_lqty) + parseInt(oth_rqty);
	var title_price = cal_discount(oth_price,oth_dis);
	$("#total_amount_"+pr_cnt).prop('title',title_price.toFixed(2)+' * '+oth_qty);
}
function switch_pat_ins_resp(pr_cont)
{
	var ins_id=tot_amt=pt_paid=transfer_amt=0;
	ins_id = $("#ins_case_id_"+pr_cont).val();
	tot_amt = $("#total_amount_"+pr_cont).val();
	pt_paid = $("#pt_paid_"+pr_cont).val();
	transfer_amt = parseFloat(tot_amt)-parseFloat(pt_paid);
	if(ins_id=="")
	{
		$("#ins_amount_"+pr_cont).val("0.00");
		$("#pt_resp_"+pr_cont).val(transfer_amt.toFixed(2));
	}
	else if(ins_id!="" && ins_id>0)
	{
		$("#ins_amount_"+pr_cont).val(transfer_amt.toFixed(2));
	}
	calculate_all();
}
  function calculate_all()
{
	grand_price = grand_disc = grand_total = grand_allowed = 0;
	grand_payed = grand_resp = grand_ins_amt = 0;
	$('.price_cls').each(function(index, element) {
	price_cls = parseFloat($('.price_cls').get(index).value);
	allowed_cls = parseFloat($('.allowed_cls').get(index).value);
	qty_cls = $('.qty_cls').get(index).value;
	rqty_cls = $('.rqty_cls').get(index).value;
	payed_cls = parseFloat($('.payed_cls').get(index).value);
	ins_amt_cls = parseFloat($('.ins_amt_cls').get(index).value);
	disc_val = $('.price_disc_per_proc').get(index).value;
	tot_qty = parseInt(qty_cls)+parseInt(rqty_cls);
	if(disc_val.slice(-1)=='%'){
		disc_val = disc_val.replace('%','');
		disc_val = allowed_cls * (parseFloat(disc_val)/100);
	}else{
		disc_val = tot_qty * parseFloat(disc_val);
		if(disc_val>0){
			$('.price_disc').get(index).value = disc_val.toFixed(2);
		}
	}
	disc_val = parseFloat(disc_val);
	
	if(isNaN(price_cls)){
		price_cls = 0;
		$('.price_cls').get(index).value = price_cls.toFixed(2);
	}
	if(isNaN(allowed_cls)){
		allowed_cls = 0;
		$('.allowed_cls').get(index).value = allowed_cls.toFixed(2);
	}
	if(isNaN(payed_cls)){
		payed_cls = 0;
		$('.payed_cls').get(index).value = payed_cls.toFixed(2);
	}
	if(isNaN(ins_amt_cls)){
		ins_amt_cls = 0;
		$('.ins_amt_cls').get(index).value = ins_amt_cls.toFixed(2);
	}
	if(isNaN(disc_val)){
		disc_val = 0;
		$('.price_disc').get(index).value = disc_val.toFixed(2);
	}
	if(allowed_cls>0){
		price_total = (allowed_cls-disc_val);
	}else{
		price_total = (price_cls-disc_val)*tot_qty;
	}
	resp_cls = price_total-ins_amt_cls-payed_cls;
	
	if(!isNaN(price_total)){
		$('.price_total').get(index).value = price_total.toFixed(2);
	}
	if(!isNaN(resp_cls)){
		if(resp_cls>0){
			$('.resp_cls').get(index).value = resp_cls.toFixed(2);
		}else{
			$('.resp_cls').get(index).value = '0.00';
		}
	}
	grand_price = grand_price + price_cls;
	grand_allowed = grand_allowed + allowed_cls;
	grand_disc = grand_disc + disc_val;
	grand_total = grand_total + parseFloat(price_total);
	grand_payed = grand_payed + payed_cls;
	grand_resp = grand_resp + resp_cls;
	grand_ins_amt = grand_ins_amt + ins_amt_cls;
	});
	if(!isNaN(grand_price)){
		$('#pat_pos_grand_price').val(grand_price.toFixed(2));
	}else{
		grand_price=0;
	}
	if(!isNaN(grand_allowed)){
		$('#pat_pos_grand_allowed').val(grand_allowed.toFixed(2));
	}else{
		grand_allowed=0;
	}
	if(!isNaN(grand_payed)){
		$('#pat_pos_grand_payed').val(grand_payed.toFixed(2));
	}else{
		grand_payed=0;
	}
	if(!isNaN(grand_resp)){
		$('#pat_pos_grand_resp').val(grand_resp.toFixed(2));
	}else{
		grand_resp=0;
	}
	if(!isNaN(grand_ins_amt)){
		$('#pat_pos_grand_ins_amt').val(grand_ins_amt.toFixed(2));
	}else{
		grand_ins_amt=0;
	}
	if(!isNaN(grand_disc)){
		$('#pat_pos_grand_disc').val(grand_disc.toFixed(2));
	}else{
		grand_disc=0;
	}
	//grand_total = grand_price-grand_disc;
	$('#pat_pos_grand_total').val(grand_total.toFixed(2));
	GDTChange();
}
function changeMode(){
	var thisVal = document.getElementById('paymentMode').value;
	if(thisVal == 'Cash'){
		document.getElementById('checkTd').style.display = 'none';
		document.getElementById('ccTd').style.display = 'none';
	}else if(thisVal == 'Check' || thisVal == 'EFT' || thisVal == 'Money Order'){
		document.getElementById('checkTd').style.display = 'block';
		document.getElementById('ccTd').style.display = 'none';
	}else if(thisVal == 'Credit Card'){
		document.getElementById('checkTd').style.display = 'none';
		document.getElementById('ccTd').style.display = 'block';
	}
}
 </script>
<input type="hidden" name="frame_order_detail_id" id="frame_order_detail_id" value="<?php echo $frame_order_detail_id;?>">
		<input type="hidden" name="lens_order_detail_id" id="lens_order_detail_id" value="<?php echo $lens_order_detail_id;?>">
		<input type="hidden" name="cl_order_detail_id" id="cl_order_detail_id" value="<?php echo $cl_order_detail_id;?>">
		<input type="hidden" name="frame_module_type_id" id="frame_module_type_id" value="1">
		<input type="hidden" name="lens_module_type_id" id="lens_module_type_id" value="2">
		<input type="hidden" name="cl_module_type_id" id="cl_module_type_id" value="3">
		<input type="hidden" name="page_name" id="page_name" value="pos">
		<input type="hidden" name="reduc_stock" id="reduc_stock" value="no">
        
              <table class="table_collapse" border="0" style="margin:50px 0 0 0;float:left;">
              <tr class="listheading" style="width:100%;"><th colspan="12" align="left" >Order Detail</th></tr>
                <tr class="listheading" id="item_tr_id">
                    <td width="100">UPC</td> 
                    <td width="100">Item</td>
                    <td width="50">Prac Code</td>
                    <td width="50">Cost</td>
                    <td width="78px">Allowed</td>
                    <td width="70px">Discount</td>
                    <td width="70">Total</td>
                    <td width="90px">Ins. Resp</td>
                    <td width="90px">Pt Payed</td>
					<td width="90px">Pt Resp</td>
                    <td width="100" style="padding:0;">
						<select name="main_discount_code_1" id="main_discount_code_1" style="width:90px;" onChange="auto_select_dis_code(this.value);">
							<option value="">Please Select</option>
							<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
							while($sel_write=imw_fetch_array($sel_rec)){
							?>
							<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_ord_row_ins['main_default_discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
							</option>
							<?php } ?>
						</select>
					</td>
                    <td style="padding:0;" width="100">
						<select name="main_ins_case_id_1" id="main_ins_case_id_1" style="width:90px;" onChange="auto_select_ins(this.value);">
                        	<option value="">Insurance</option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_ord_row_ins['main_default_ins_case']) { echo 'SELECTED'; } ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
					</td>
                </tr>   
                <?php
				$pro_cont=0;
				for($i=1;$i<=count($sel_order_data);$i++){
					$pro_cont=$i;
					$sel_records=$sel_order_data[$i];
					$all_dx_codes="";
					if($sel_records['dx_code']!="")
					{
						$dx_singl=array();
						$get_dxs = explode(",",$sel_records['dx_code']);
						for($fd=0;$fd<count($get_dxs);$fd++)
						{
							$dx_singl[] = $dx_code_arr[$get_dxs[$fd]];
						}
						$all_dx_codes = join('; ',$dx_singl);
					}
					
					/*if($sel_order_data[$i]['module_type_id']==2){
					
						$show_lens_value_arr = array('type_id','progressive_id','material_id','transition_id','a_r_id','tint_id','polarized_id','edge_id','color_id','uv400','lens_other','pgx');
					
						$show_itemized_name_arr = array('lens','progressive','material','transition','a_r','tint','polarization','edge','color','uv400','other','pgx');
						
						$pro=0;
						for($l=0;$l<count($show_lens_value_arr);$l++)
						{
							if(($sel_records[$show_lens_value_arr[$l]] > 0) || ($sel_records[$show_lens_value_arr[$l]]!="" && $show_itemized_name_arr[$l]=="other"))
							{
								$pro++;
								
							$sel_price_qry=imw_query("select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records['id']."' and patient_id='$patient_id' and itemized_name='".$show_itemized_name_arr[$l]."' and del_status='0'");	
							
							while($sel_lens_price_data=imw_fetch_array($sel_price_qry))
							{
								if($pro==1) { $clas = ""; } else { $clas = "even"; } ?>  
				<tr id="<?php echo $sel_lens_price_data['itemized_name']."_display"; ?>" class="<?php echo $clas; ?>">
                	<td>
						<input type="hidden" name="dx_code_<?php echo $pro_cont; ?>" value="<?php echo $all_dx_codes; ?>" />
                        <input type="hidden" name="order_chld_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['order_chld_id']; ?>" />
                     	<input type="hidden" name="order_detail_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['id']; ?>" />
						<input type="hidden" name="lens_item_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['itemized_id']; ?>" />
						<input type="hidden" name="lens_item_detail_name_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['itemized_name']; ?>" />
						<input type="hidden" name="lens_price_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['id']; ?>" />
                        <input type="hidden" class="qty_cls" name="lens_qty_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['qty']; ?>" />
                        <input type="hidden" class="rqty_cls" name="qty_right_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['qty_right']; ?>" />
                        <input type="hidden" name="module_type_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['module_type_id']; ?>" />
						<input type="hidden" name="upc_id_<?php echo $pro_cont; ?>" id="upc_id_<?php echo $pro_cont; ?>" value="">
                    	<?php if($pro==1) { ?>
						<input readonly style="width:90px;" type="text" name="upc_name_<?php echo $pro_cont; ?>" id="upc_name_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['upc_code'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
						<?php } ?>
                    </td>
                    <td>
                    	<input readonly style="width:90px;" type="text" class="itemname" name="lens_item_name_<?php echo $pro_cont; ?>" id="lens_item_name_<?php echo $pro_cont; ?>" value="<?php if($sel_lens_price_data['itemized_name']=="a_r") { echo "a/r"; } elseif($sel_lens_price_data['itemized_name']=="lens") { echo "Focal type"; } else { echo $sel_lens_price_data['itemized_name']; } ?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))"/>
						<input readonly style="width:90px;" type="hidden" name="item_name_<?php echo $pro_cont; ?>" id="item_name_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_name'];?>"/>
						
                        <input type="hidden" name="item_id_<?php echo $pro_cont; ?>" id="item_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_id'];?>" />
                    </td>
                    <td>
                    	<input style="width:50px;" type="text" class="pracodefield" name="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $proc_code_arr[$sel_lens_price_data['item_prac_code']];?>" title="<?php echo $proc_code_desc_arr[$sel_lens_price_data['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>','pos'); calculate_all();"/>
                    </td>
                    <td>
                    	<input readonly style="width:50px; text-align:right;" type="text" name="lens_item_price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['wholesale_price'];?>" class="price_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="lens_item_allowed_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="allowed_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['allowed'];?>" class="allowed_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input  style="width:70px; text-align:right;" type="hidden" name="lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="discount_<?php echo $pro_cont; ?>" value="<?php echo $sel_lens_price_data['discount'];?>" onChange="calculate_all();" class="price_disc_per_proc"/>
                    	<input readonly style="width:70px; text-align:right;" type="text" name="read_lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="read_discount_<?php echo $pro_cont; ?>" value="<?php echo $sel_lens_price_data['discount'];?>" class="price_disc" onChange="calculate_all();"/>
                    </td> 
                    <td>
                    	<input readonly style="width:90px; text-align:right;" type="text" name="lens_item_total_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="total_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['total_amt'];?>" class="price_total"  onChange="calculate_all();"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="ins_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="ins_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls"/>
                    </td>                  
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_paid_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="pt_paid_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_resp_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="pt_resp_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" value="<?php echo $sel_lens_price_data['pt_resp'];?>" class="resp_cls" readonly/>
                    </td>
					<td>
						<select name="discount_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="discount_code_<?php echo $pro_cont; ?>" class="text_10 disc_code dis_code_class" style="width:90px;">
							<option value="">Please Select</option>
							<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
							while($sel_write=imw_fetch_array($sel_rec)){
							?>
							<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_lens_price_data['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
							</option>
							<?php } ?>
						</select>	
					</td>
                    <td>
                   		<!--<select name="ins_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="ins_id_<?php echo $pro_cont; ?>" style="width:110px;">
                        	<option value=""></option>
                            <?php
								foreach($ins_data_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>-->
                        <select name="ins_case_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" id="ins_case_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?>" class="ins_case_class" style="width:90px;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>_<?php echo $pro; ?>');">
                        	<option value=""></option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
                    </td>                                    
                </tr>
				
				<?php } } } ?>
				<input type="hidden" name="lens_item_count_<?php echo $pro_cont;?>" id="lens_item_count_<?php echo $pro_cont;?>" value="<?php echo $pro; ?>">
				<?php } else*/if($sel_order_data[$i]['pof_check']==0 && $sel_order_data[$i]['module_type_id']!='8'){ ?>
				<tr id="<?php echo $pro_cont; ?>"> 
                	<td>
						<!--<input type="hidden" name="dx_code_<?php echo $pro_cont; ?>" value="<?php echo $all_dx_codes; ?>" />-->
                        <input type="hidden" name="order_chld_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['order_chld_id']; ?>" />
                     	<!--<input type="hidden" name="order_detail_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['id']; ?>" />-->
                        <!--<input type="hidden" class="qty_cls" id="qty_<?php echo $pro_cont; ?>" name="qty_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['qty']; ?>" />-->
                        <!--<input type="hidden" class="rqty_cls" id="qty_right_<?php echo $pro_cont; ?>" name="qty_right_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['qty_right']; ?>" />-->
                        <!--<input type="hidden" name="module_type_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['module_type_id']; ?>" />-->
						<input type="hidden" name="upc_id_1_<?php echo $pro_cont; ?>" id="upc_id_<?php echo $pro_cont; ?>" value="">
                    	<input readonly style="width:90px;" type="text" name="upc_name_1_<?php echo $pro_cont; ?>" id="upc_name_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['upc_code'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
                    </td>
                    <td>
                    	<input readonly style="width:90px;" type="text" class="itemname" name="item_name_1_<?php echo $pro_cont; ?>" id="item_name_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_name'];?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))"/>
                        <input type="hidden" name="item_id_<?php echo $pro_cont; ?>" id="item_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['item_id'];?>" />
                    </td>
                    <td>
                    	<input style="width:70px;" type="text" class="pracodefield" name="item_prac_code_1_<?php echo $pro_cont; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_1" value="<?php echo $proc_code_arr[$sel_records['item_prac_code']];?>" title="<?php echo $proc_code_desc_arr[$sel_records['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'price_1_<?php echo $pro_cont; ?>','pos','<?php echo $sel_records['trial_chk']; ?>'); pospage_title('<?php echo $pro_cont; ?>'); calculate_all();"/>
                    </td>
                    <td>
                    	<input readonly style="width:70px; text-align:right;" type="text" name="price_1_<?php echo $pro_cont; ?>" id="price_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['price'];?>" class="price_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input style="width:70px; text-align:right;" type="text" name="allowed_1_<?php echo $pro_cont; ?>" id="allowed_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['allowed'];?>" class="allowed_cls" onChange="calculate_all();"/> 
                    </td>
                    <td>
                    	<input style="width:65px; text-align:right;" type="hidden" name="discount_1_<?php echo $pro_cont; ?>" id="discount_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['discount'];?>"  onChange="calculate_all();" class="price_disc_per_proc"/>
                    	<input readonly style="width:65px; text-align:right;" type="text" name="read_discount_<?php echo $pro_cont; ?>" id="read_discount_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['discount'];?>" class="price_disc" onChange="calculate_all();"/>
                    </td> 
                    <td>
                    	<input readonly style="width:70px; text-align:right;" type="text" name="total_amount_1_<?php echo $pro_cont; ?>" id="total_amount_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['total_amount'];?>" class="price_total"  onChange="calculate_all();"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="ins_amount_<?php echo $pro_cont; ?>" id="ins_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls"/>
                    </td>                  
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_paid_<?php echo $pro_cont; ?>" id="pt_paid_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls"/>
                    </td>
                    <td>
                    	<input style="width:90px; text-align:right;" type="text" name="pt_resp_<?php echo $pro_cont; ?>" id="pt_resp_<?php echo $pro_cont; ?>" value="<?php echo $sel_records['pt_resp'];?>"  class="resp_cls" readonly />
                    </td>
					<td>
						<select name="discount_code_1_<?php echo $pro_cont; ?>" id="discount_code" class="text_10 disc_code dis_code_class" style="width:90px;">
							<option value="">Please Select</option>
							<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
							while($sel_write=imw_fetch_array($sel_rec)){
							?>
							<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_records['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?>
							</option>
							<?php } ?>
						</select>
					</td>
                    <td>
                   		<!--<select name="ins_id_<?php echo $pro_cont; ?>" id="ins_id_<?php echo $pro_cont; ?>" style="width:110px;">
                        	<option value=""></option>
                            <?php
								foreach($ins_data_arr as $key => $insCoName){

								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_records['ins_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>-->
                        <select name="ins_case_id_<?php echo $pro_cont; ?>" id="ins_case_id_<?php echo $pro_cont; ?>" class="ins_case_class" style="width:90px;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>');">
                        	<option value=""></option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_records['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
                    </td>                                    
                </tr>
				<script>pospage_title('<?php echo $pro_cont; ?>');</script>
				<input type="hidden" name="lens_item_count_<?php echo $pro_cont;?>" id="lens_item_count_<?php echo $pro_cont;?>" value="1">
				<?php } }
				if(count($sel_order_data)>0)
				{
				$sel_rec=array();
				$sel_tax_ord = imw_query("select * from in_order_details where order_id='$order_id' and module_type_id='8' and del_status='0'");
				$pro_cont = $pro_cont+1;
				if(imw_num_rows($sel_tax_ord)>0)
				{
					$sel_rec = imw_fetch_array($sel_tax_ord);	
				}
				 ?>
				<tr>
					<td>
						<input type="hidden" name="lens_item_count_<?php echo $pro_cont;?>" id="lens_item_count_<?php echo $pro_cont;?>" value="1">
						<input type="hidden" name="module_type_id_1_<?php echo $pro_cont; ?>" value="8" />
						<input type="hidden" name="order_chld_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['order_chld_id']; ?>" />
						<input type="hidden" name="order_detail_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['id']; ?>" />
						<input type="hidden" class="qty_cls" name="qty_<?php echo $pro_cont;?>" value="1" />
                        <input type="hidden" class="rqty_cls" name="qty_right_<?php echo $pro_cont;?>" value="0" />
						<input type="text" value="Taxes" name="upc_name_<?php echo $pro_cont; ?>" style="width:90px;" readonly>
					</td>
					<td><input type="text" value="Taxes" name="item_name_<?php echo $pro_cont; ?>" class="itemname" style="width:90px;" readonly></td>
					<td>
						<input style="width:70px;" type="text" class="pracodefield" name="item_prac_code_1_<?php echo $pro_cont; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_1" value="<?php echo $proc_code_arr[$sel_rec['item_prac_code']];?>" title="<?php echo $proc_code_desc_arr[$sel_rec['item_prac_code']]; ?>" onChange="show_price_from_praccode(this,'','pos','tax');"/>
					</td>
					<td>
						<input type="text" name="price_1_<?php echo $pro_cont; ?>" id="price_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['price']; ?>" style="width:70px; text-align:right;" class="price_cls" onChange="set_allowed_amt('<?php echo $pro_cont; ?>');calculate_all();">
                    </td>
                    <td>
						<input type="text" name="allowed_1_<?php echo $pro_cont; ?>" id="allowed_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['allowed']; ?>" style="width:70px; text-align:right;" class="allowed_cls" onChange="calculate_all();">
                    </td>
					<td>
						<input type="hidden" name="discount_<?php echo $pro_cont; ?>" value="0" style="width:70px; text-align:right;"  onChange="calculate_all();" class="price_disc_per_proc">
						<input type="text" name="read_discount_1_<?php echo $pro_cont; ?>" value="0" style="width:65px; text-align:right;" class="price_disc" onChange="calculate_all();" readonly>
                    </td>
					<td>
						<input type="text" name="total_amount_1_<?php echo $pro_cont; ?>" id="total_amount_1_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['total_amount']; ?>" style="width:70px; text-align:right;" class="price_total"  onChange="calculate_all();" readonly>
					</td>
					<td>
						<input type="text" name="ins_amount_<?php echo $pro_cont; ?>" id="ins_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['ins_amount']; ?>" style="width:90px; text-align:right;" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls">
					</td>
					<td>
						<input type="text" name="pt_paid_<?php echo $pro_cont; ?>" id="pt_paid_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['pt_paid']; ?>" style="width:90px; text-align:right;" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls">
					</td>
					<td>
					<input type="text" name="pt_resp_<?php echo $pro_cont; ?>" id="pt_resp_<?php echo $pro_cont; ?>" value="<?php echo $sel_rec['pt_resp']; ?>" style="width:90px; text-align:right;" class="resp_cls" readonly>
					</td>
					<td>&nbsp;</td>
					<td>
						<select name="ins_case_id_<?php echo $pro_cont; ?>" id="ins_case_id_<?php echo $pro_cont; ?>" class="ins_case_class" style="width:90px;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>');">
                        	<option value=""></option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_rec['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
					</td>
				</tr>
				<?php } ?>
               <input type="hidden" id="last_cont" name="last_cont" value="<?php echo $pro_cont;?>">
            </table>
            <table class="table_collapse" border="0">
    	<tr>
            <td align="right" style="font-weight:bold; padding-left:170px;width:93px;">Grand Total: </td>
            <td style="padding-left:20px;"><input readonly style="width:73px;" type="text" name="pat_pos_grand_price" id="pat_pos_grand_price" value="" /></td>
            <td><input readonly style="width:70px;" type="text" name="pat_pos_grand_allowed" id="pat_pos_grand_allowed" value="" /></td>
            <td><input readonly style="width:72px;" type="text" name="pat_pos_grand_disc" id="pat_pos_grand_disc" value="" /></td>
            <td><input readonly style="width:72px;" type="text" name="pat_pos_grand_total" id="pat_pos_grand_total" value="" /></td>
			<td><input readonly  style="width:90px;" type="text" name="pat_pos_grand_ins_amt" id="pat_pos_grand_ins_amt" value="" /></td>
            <td><input readonly style="width:90px;" type="text" name="pat_pos_grand_payed" id="pat_pos_grand_payed" value="" /></td>
            <td><input readonly style="width:90px;" type="text" name="pat_pos_grand_resp" id="pat_pos_grand_resp" value="" /></td>
            <td style="width:200px;">&nbsp;</td>
        </tr>
    </table>
    <table class="table_collapse" border="0">
    	<tr><td colspan="4" style="height:7px;"></td></tr>
    	<tr>
			<td style="font-weight:bold; text-align:left;width:420px;">
            	Comment:
            	<input type="text" name="charge_comment_1" value="<?php echo $sel_ord_row_ins['comment'];?>" style="width:235px;"/>
            </td>
            <td style="text-align:left;font-weight:bold;">Method:
                <select name="paymentMode" id="paymentMode" class="input_text_10" style="width:78px;" onChange="return changeMode();">
                    <option value="Cash" <?php if($sel_ord_row_ins['payment_mode']=="Cash") echo 'SELECTED'; ?>>Cash</option>
                    <option value="Check" <?php if($sel_ord_row_ins['payment_mode']=="Check") echo 'SELECTED'; ?>>Check</option>
                    <option value="Credit Card" <?php if($sel_ord_row_ins['payment_mode']=="Credit Card") echo 'SELECTED'; ?>>Credit Card</option>
                    <option value="EFT" <?php if($sel_ord_row_ins['payment_mode']=="EFT") echo 'SELECTED'; ?>>EFT</option>
                    <option value="Money Order" <?php if($sel_ord_row_ins['payment_mode']=="Money Order") echo 'SELECTED'; ?>>Money Order</option>
                </select>
            </td>
            <td id="checkTd" style="font-weight:bold;border:none;text-align:left;display:<?php if($sel_ord_row_ins['payment_mode']=="Check" || $sel_ord_row_ins['payment_mode']=="EFT" || $sel_ord_row_ins['payment_mode']=="Money Order"){ echo 'block'; }else{ echo 'none'; } ?>;">
            	Check&nbsp;#:
                <input name="checkNo" id="checkNo" type="text" class="input_text_10" size="15" value="<?php echo $sel_ord_row_ins['checkNo']; ?>" />	
            </td>
            <td id="ccTd" style="text-align:left;border:none;display:<?php if($sel_ord_row_ins['payment_mode']=="Credit Card"){ echo 'block'; }else{ echo 'none'; } ?>;">
                <table class="table_collapse_autoW">
                    <tr>
                        <td style="text-align:left; border:none;font-weight:bold;" class="text_b_w">CC&nbsp;#:</td>
                        <td style="text-align:left; border:none;"><input name="cCNo" id="cCNo" type="text" class="input_text_10" size="12" value="<?php echo $sel_ord_row_ins['creditCardNo']; ?>" /></td>
                        <td style="width:6px;border:none;"></td>
                        <td class="text_b_w" style="text-align:left;border:none;font-weight:bold;">Type:</td>
                        <td style="text-align:left;border:none;" id="creditCardCoTd">
                            <select name="creditCardCo" id="creditCardCo" style="width:100px;" class="input_text_10">
                                <option value=""></option>
                                <option value="AX" <?php if($sel_ord_row_ins['creditCardCo'] == "AX") echo 'SELECTED'; ?>>American Express</option>
                                <option value="Dis" <?php if($sel_ord_row_ins['creditCardCo'] == "Dis") echo 'SELECTED'; ?>>Discover</option>
                                <option value="MC" <?php if($sel_ord_row_ins['creditCardCo'] == "MC") echo 'SELECTED'; ?>>Master Card</option>
                                <option value="Visa" <?php if($sel_ord_row_ins['creditCardCo'] == "Visa") echo 'SELECTED'; ?>>Visa</option>
                            </select>
                        </td>
                        <td style="width:5px;border:none;"></td>
                        <td style="text-align:right;border:none;font-weight:bold;" class="text_b_w">Exp.&nbsp;Date:</td>
                        <td style="text-align:left;border:none;">
                            <input type="text" name="expireDate" id="expireDate" value="<?php echo $sel_ord_row_ins['expirationDate']; ?>" size='4' maxlength="10" class="input_text_10" />
                            </form>
                        </td>
                    </tr>
                </table>
            </td>									
        </tr>
    </table>
   <script>

var last_cont=document.getElementById("last_cont").value;
$(document).ready(function(e) {
	//calculate_all();
	if(last_cont==0)
	{
		addrow();
	}
	show_loading_image("hide");
});

for(var j=1;j<=last_cont;j++){
	//var obj6 = new actb(document.getElementById('upc_name_'+j),custom_array_upc,"","",document.getElementById('upc_id_'+j),custom_array_upc_id);
var lens_item_count=document.getElementById("lens_item_count_"+j).value;	
	for(var t=1;t<=lens_item_count;t++){
		var obj71 = new actb(document.getElementById('item_prac_code_'+j+'_'+t),customarrayProcedure);
	}
	//var obj8 = new actb(document.getElementById('item_name_'+j),custom_array_name,"","",document.getElementById('upc_id_'+j),custom_array_upc_id);
}
</script>