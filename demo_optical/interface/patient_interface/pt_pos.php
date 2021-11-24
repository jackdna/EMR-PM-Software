<!------------------------------- POS PART ----------------------------------->  
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

$sel_ord_qry_ins=imw_query("select main_default_discount_code,main_default_ins_case,comment,payment_mode,checkNo,creditCardNo,creditCardCo,expirationDate,overall_discount,overall_discount_code,total_overall_discount,overall_discount_prac_code,overall_discount_chld, tax_prac_code, tax_payable, tax_pt_paid, tax_custom, tax_chld, grand_total, re_make_id, due_date from in_order where id ='$order_id'");
$sel_ord_row_ins=imw_fetch_array($sel_ord_qry_ins);

$sel_qry = "select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and del_status='0' and module_type_id NOT IN(8, 9) and lens_frame_id='0' order by id desc";
$sel_qry=imw_query($sel_qry);
/*and lens_frame_id='0'(applied for combined Lens and Frame section in R7)*/


/*Fetch Custom Charge Rows*/
$sql_cs = "SELECT * FROM `in_order_details` WHERE `order_id`='".$order_id."' AND `patient_id`='".$patient_id."' AND `del_status`='0' AND `module_type_id`=9";
$cs_rows = imw_query($sql_cs);
/*End Fetch Custom Charge Rows*/

$top_cont=0;
while($sel_order_row=imw_fetch_array($sel_qry))
{
	$top_cont++;
	$sel_order_data[$top_cont]=$sel_order_row;
	$sel_order_module_data[$sel_order_row['module_type_id']][]=$sel_order_row;
}

?>
      
<?php	/*Discount codes*/
$discCodes = "";
$discCodes1 = array();
$sel_rec=imw_query("select d_id,d_code,d_default from discount_code ORDER BY d_code ASC");
while($sel_write=imw_fetch_array($sel_rec)){
	$discCodes .='<option value="'.$sel_write['d_id'].'">'.$sel_write['d_code'].'</option>';
	$discCodes1[$sel_write['d_id']] = $sel_write['d_code'];
}

$overall_discount_prac_code = "";
$sel_dis_prac_code=imw_query("select prac_code from in_prac_codes where sub_module='Overall Discount'");
$row_dis_prac_code=imw_fetch_array($sel_dis_prac_code);
$overall_discount_prac_code=$row_dis_prac_code['prac_code'];

/*Insurance Cases*/
$insCases = array();
foreach($ins_case_arr as $key => $insCoName){
	$insCases[$insCoName] = $key;
}

$taxLabel = "Tax";
$tax = imw_query("SELECT `tax_label` FROM `in_location` WHERE `id`='".$_SESSION['pro_fac_id']."'");
if($tax && imw_num_rows($tax)>0){
	$tax = imw_fetch_assoc($tax);
	$taxLabel = ($tax['tax_label']!="")?ucfirst($tax['tax_label']):'Tax';
}
$taxRate = get_tax_rates();

$taxPrac = "";
$tax = imw_query("SELECT `prac_code` FROM `in_prac_codes` WHERE `module_id`='8'");
if($tax){
	$tax = imw_fetch_assoc($tax);
	$taxPrac = ($tax['prac_code']!="")?$tax['prac_code']:0;
}

?>
<style type="text/css">
.posTable *{
	box-sizing:border-box;
}

#pos_pat_pos_grand_resp, #pos_pat_pos_grand_payed, #pos_pat_pos_grand_ins_amt, #pos_pat_pos_grand_total, #pos_pat_pos_grand_disc, #pos_pat_pos_grand_allowed, #pos_pat_pos_grand_price{
	text-align:right;
}
.delitem{
	cursor:pointer;
	vertical-align:middle;
}
.hideRow{
	display:none;
}
table.posTable>tbody>tr[id]>td:nth-last-child(2){
	text-align:center;
}
input.tax_applied{
	margin:0 !important;
	float:none !important;
	cursor:pointer;
	vertical-align:initial;
}
.addChargeRow {
	width: 18px;
	vertical-align: middle;
	cursor: pointer;
}
.customCharge{
	background-color: rgba(0, 153, 17, 0.490196);
}
</style>
<script type="text/javascript">
var discCodes = <?php echo json_encode($discCodes1); ?>;
var insCases = <?php echo json_encode($insCases); ?>;
var facTax = <?php echo json_encode($taxRate); ?>;
function pospage_title(pr_cnt){
	var oth_price = $("#pos_price_"+pr_cnt).val();
	var oth_dis = $("#pos_discount_"+pr_cnt).val();
	var oth_lqty = $("#pos_qty_"+pr_cnt).val();
	var oth_rqty = $("#pos_qty_right_"+pr_cnt).val();
	var oth_qty = parseInt(oth_lqty) + parseInt(oth_rqty);
	var title_price = cal_discount(oth_price,oth_dis);
	$("#pos_total_amount_"+pr_cnt).prop('title',title_price.toFixed(2)+' * '+oth_qty);
}
function changeMode(){
	var thisVal = document.getElementById('paymentMode').value;
	if(thisVal == 'Cash'){
		document.getElementById('checkTd').style.display = 'none';
		document.getElementById('ccTd').style.display = 'none';
	}else if(thisVal == 'Check' || thisVal == 'EFT' || thisVal == 'Money Order'){
		document.getElementById('checkTd').style.display = '';
		document.getElementById('ccTd').style.display = 'none';
	}else if(thisVal == 'Credit Card'){
		document.getElementById('checkTd').style.display = 'none';
		document.getElementById('ccTd').style.display = '';
	}
}
</script>
  <div class="container-fluid" style="padding:0">
  <div class="row">
    <input type="hidden" name="pos_frame_order_detail_id" id="pos_frame_order_detail_id" value="<?php echo $frame_order_detail_id;?>">
		<input type="hidden" name="pos_lens_order_detail_id" id="pos_lens_order_detail_id" value="<?php echo $lens_order_detail_id;?>">
		<input type="hidden" name="pos_cl_order_detail_id" id="pos_cl_order_detail_id" value="<?php echo $cl_order_detail_id;?>">
		<input type="hidden" name="pos_frame_module_type_id" id="pos_frame_module_type_id" value="1">
		<input type="hidden" name="pos_lens_module_type_id" id="pos_lens_module_type_id" value="2">
		<input type="hidden" name="pos_cl_module_type_id" id="pos_cl_module_type_id" value="3">
		<input type="hidden" name="pos_page_name" id="pos_page_name" value="<?php echo($page_name=="other_selection")?$page_name:'pos'; ?>">
		<input type="hidden" name="pos_reduc_stock" id="pos_reduc_stock" value="no">
        <table class="table_collapse posTable" border="0" style="margin:10px 0 0 0;float:left; table-layout:fixed;">
              <thead>
              <tr class="listheading" id="pos_item_tr_id">
                    <!--td style="width:10%;">UPC</td--> 
<?php if($pageName=="frameLensSelection"){ ?>
					<td style="width:2.5%;"></td>
                    <td style="width:7%;">Item</td>
					<td style="width:8%;">Description</td>
<?php }
	else{
?>
					<td style="width:2.5%;"></td>
					<td style="width:12.5%;">Item</td>

<?php } ?>
                    <td style="width:5%;">Code</td>
                    <td style="width:78px;">Unit Cost</td>
					<td style="width:34px;padding-left:3px;">Unit</td>
                    <td style="width:90px;">T. Unit Cost</td>
                    <td style="width:77px; display:none">Total</td>
                    <td style="width:78px;">Ins. Resp</td>
                    <td style="width:78px;">Discount</td>
                    <td style="width:78px;">Pt Paid</td>
					<td style="width:78px;">Pt Resp</td>
                    <td style="width:8%;">
						<select name="main_discount_code_1" id="main_discount_code_1" style="margin-left:-4px;width:100%;" onChange="auto_select_dis_code(this.value);">
							<option value="0">Discount Code</option>
							<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code order by d_code");
							while($sel_write=imw_fetch_array($sel_rec)){
							?>
							<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_ord_row_ins['main_default_discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?></option>
							<?php } ?>
						</select>
					</td>
                    <td style="padding:0;width:8%;">
						<select name="main_ins_case_id_1" id="main_ins_case_id_1" style="width:100%;" onChange="auto_select_ins(this.value);">
                        	<option value="0">Self Pay</option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_ord_row_ins['main_default_ins_case']) { echo 'SELECTED'; } ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
					</td>
					<td style="width:42px;text-align:center;padding-left:0px" title="<?php echo $taxLabel; ?>"><?php echo (strlen($taxLabel)>3)?ucfirst(substr($taxLabel, 0,3))."..":$taxLabel; ?></td>
					<td style="width:2%"></td>
                </tr>
                </thead>
                <tbody>
                <?php
				$pro_cont=0;
				$lensPro_cont = 0;
				$itemTotal = array();
				for($i=1;$i<=count($sel_order_data);$i++){
					
					$sel_records=$sel_order_data[$i];
					
					${'pro_cont'.$sel_records['module_type_id']}=(!isset(${'pro_cont'.$sel_records['module_type_id']}))?1:++${'pro_cont'.$sel_records['module_type_id']};
					
					$pro_cont = $i;
					
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
				?>
				<script type="text/javascript">
				var all_dx_codes = "<?php echo $all_dx_codes; ?>";
				</script>
            <?php
			/*Start Data specific to lenses*/
			if($sel_order_data[$i]['module_type_id']==2){
			$pos_lensd = "_lensD";
			$lensPro_cont++;
						$show_lens_value_arr = array('type_id','design_id','material_id','a_r_id','lens_other');
						/*'transition_id','pgx','polarized_id','progressive_id','color_id','uv400','tint_id','edge_id',*/
					
						$show_itemized_name_arr = array('lens','design','material','a_r','other');
						/*'transition','progressive','pgx','tint','polarization','edge','color','uv400'*/
						
						//$pro_cont=0;
						$pro=0;
						for($l=0;$l<count($show_lens_value_arr);$l++)
						{
							if(($sel_records[$show_lens_value_arr[$l]] > 0) || ($sel_records[$show_lens_value_arr[$l]]!="" && $show_itemized_name_arr[$l]=="other"))
							{
								$pro++;
								
							$sel_price_qry=imw_query("select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records['id']."' and patient_id='$patient_id' and itemized_name='".$show_itemized_name_arr[$l]."' and del_status='0'");	
							
							while($sel_lens_price_data=imw_fetch_array($sel_price_qry))
							{
								if($pro==1) { $clas = ""; } else { $clas = "even"; }
								
								/*if($sel_lens_price_data['itemized_name'] == "lens"
									&&(trim($sel_lens_price_data['wholesale_price'])==""
									|| $sel_lens_price_data['wholesale_price']=="0.00")){
									$clas .= " hideRow1";
								}*/
							?>
				<tr id="<?php echo $sel_records['module_type_id']."_".${'pro_cont'.$sel_records['module_type_id']}."_".$sel_lens_price_data['itemized_name']."_display"; ?>" class="<?php echo $clas; ?>">
                	<!--td-->
						<!--<input type="hidden" name="pos_dx_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $all_dx_codes; ?>" />-->
                        <input type="hidden" name="pos_order_chld_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['order_chld_id']; ?>" />
                     	<input type="hidden" name="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['id']; ?>" id="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>"/>
						<input type="hidden" name="lens_item_detail_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['itemized_id']; ?>" />
						<input type="hidden" name="lens_item_detail_name_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_detail_name_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['itemized_name']; ?>" />
						<input type="hidden" name="lens_price_detail_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_price_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['id']; ?>" />
                        <input type="hidden" name="pos_module_type_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['module_type_id']; ?>" />
						<input type="hidden" name="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="">
                    	<?php if($pro==1) { /*Not in AddNewRow()*/ ?>
						<input readonly style="width:100%" type="hidden" name="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['upc_code'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
						<?php } ?>
                    <!--/td-->
                    <td> <?php /*Not in AddNewRow()*/ ?>
                    	<input readonly style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php if(substr($sel_lens_price_data['itemized_name'],0,3)=="a_r") { echo "Treatment"; } elseif($sel_lens_price_data['itemized_name']=="lens") { echo "Seg type"; }elseif($sel_lens_price_data['itemized_name']=="uv400"){echo "UV 400"; }else { echo ucfirst($sel_lens_price_data['itemized_name']); } ?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))"/>
						<input readonly style="width:100%" type="hidden" name="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['item_name'];?>"/>
						
                        <input type="hidden" name="pos_item_id_<?php echo $lensPro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['item_id'];?>" />

                        <?php 
						 if($sel_records['module_type_id']!=1 && $sel_records['module_type_id']!=2)
						 {
						 ?>
                        <input type="hidden" name="pos_qty_on_hand_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_qty_on_hand_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['qty_on_hand'];?>" />
                                               

						<input type="hidden" name="pos_stock_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_stock_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['stock'];?>" />
						<?php } ?>	
                    </td>
<?php if($pageName=="frameLensSelection"): ?>
                    <td>
						<input readonly style="width:100%;" type="text" class="itemnameDisp" name="pos_lens_item_name_disp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_name_disp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['item_description']; ?>" />
					</td>
<?php endif; ?>
                    <td>
                    	<input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($proc_code_arr[$sel_lens_price_data['item_prac_code']]!="" && $sel_lens_price_data['item_prac_code']!="0")?$proc_code_arr[$sel_lens_price_data['item_prac_code']]:$sel_lens_price_data['item_prac_code_default'];?>" title="<?php echo $proc_code_desc_arr[$sel_lens_price_data['item_prac_code']]; ?>" />
						<!--onChange="show_price_from_praccode(this,'price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>','pos'); calculate_all();"-->
                    </td>
                    <td>
                    	<input style="width:100%; text-align:right;" type="text" name="lens_item_price_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_price_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['wholesale_price'];?>" class="price_cls currency" onChange="calculate_all();"/> 
                    </td>
					<td>
<!--  onChange="changeQty('<?php echo $sel_records['module_type_id']; ?>', this.value, '<?php echo $pro_cont; ?>');" -->
						<input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_qty_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['qty']; ?>" autocomplete="off" onChange="calculate_all();" onKeyUp="validate_qty(this);" />
						<input type="hidden" class="rqty_cls" name="pos_qty_right_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['qty_right']; ?>" />
					</td>
                    <td>
                    	<input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_allowed_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['allowed'];?>" class="allowed_cls currency" onChange="calculate_all();"/> 
                    </td>
                     
                    <td style="display:none">
                    	<input readonly style="width:100%; text-align:right;" type="text" name="lens_item_total_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_total_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['total_amt']; array_push($itemTotal, $sel_lens_price_data['total_amt']); ?>" class="price_total currency"  onChange="calculate_all();"/>
						<!-- Tax Calculations -->
						<input type="hidden" name="tax_p_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_p_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="tax_p" value="<?php echo $sel_lens_price_data['tax_rate'];?>" />
						<input type="hidden" name="tax_v_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_v_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="tax_v" value="<?php echo $sel_lens_price_data['tax_paid']; ?>" />
						<!-- End Tax Calculations -->
                    </td>
                    <td>
                    	<input style="width:100%; text-align:right;" type="text" name="ins_amount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="ins_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"/>
                    </td>
                    <td>
                    	<!-- Line item's share in overall discount -->
						<input type="hidden" name="lens_item_overall_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_overall_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($sel_lens_price_data['overall_discount']=="")?0:$sel_lens_price_data['overall_discount']; ?>" class="item_overall_disc" />
						<input type="hidden" name="lens_item_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($sel_lens_price_data['discount']=="")?0:$sel_lens_price_data['discount']; ?>" onChange="calculate_all();" class="price_disc_per_proc"/>
                        <input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_read_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="" class="price_disc currency" onChange="changeDiscount(this);" autocomplete="off" />
                    </td>
                    <td>
                    	<input style="width:100%; text-align:right;" type="text" name="pt_paid_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pt_paid_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"/>
                    </td>
                    <td>
                    	<input style="width:100%; text-align:right;" type="text" name="pt_resp_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pt_resp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['pt_resp'];?>" class="resp_cls currency" readonly/>
                    </td>
					<td>
						<select name="discount_code_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="discount_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" class="text_10 disc_code dis_code_class" style="width:100%;" onChange="discountChanged(this);">
							<option value="0">Please Select</option>
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
                   		<!--<select name="pos_ins_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?>" id="pos_ins_id_<?php echo $pro_cont; ?>" style="width:110px;">
                        	<option value=""></option>
                            <?php
								foreach($ins_data_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>-->
                        <select name="ins_case_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="ins_case_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>');">
                        	<option value="0">Self Pay</option>
                            <?php
								foreach($ins_case_arr as $key => $insCoName){
								?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
								<?php
								}
							?>
                        </select>
						<input type="hidden" name="del_status_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="del_status_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="del_status" value="0" />
                    </td>
					<td>
						<input type="checkbox" class="tax_applied" name="tax_applied_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_applied_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="1"  <?php echo ($sel_lens_price_data['tax_applied']=="1")?'checked="checked"':""; ?> onChange="cal_overall_discount()" />
					</td>
					<td>
						<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow('<?php echo $sel_records['module_type_id']; ?>', '<?php echo ${'pro_cont'.$sel_records_lens['module_type_id']}; ?>', '<?php echo $sel_lens_price_data['itemized_name']; ?>');" />
					</td>
                </tr>
				
				<?php } 	/*End Data Specific to Lens*/
				} } ?>
				<input type="hidden" name="lens_item_count_<?php echo $lensPro_cont;?>_lensD" id="lens_item_count_<?php echo $lensPro_cont;?>_lensD" value="<?php echo $pro; ?>">
				<?php 
				}
				/*$sel_order_data[$i]['pof_check']==0 && */
				elseif($sel_order_data[$i]['module_type_id']!='8'){
				$pos_lensd="";
				if($sel_records['module_type_id']=="2" && $page_name!="other_selection"){
					$lens_pro_cont++;
					$pos_lensd="_lensD";
					$pro_cont=$lens_pro_cont;
				}
				
				/*Condition for Contact Lens OD Row*/
				if($sel_order_data[$i]['module_type_id']!='3' || ($sel_order_data[$i]['item_id']!='0' || $sel_order_data[$i]['upc_code'])){ 
			?>
				<tr id="<?php echo $sel_records['module_type_id']."_".${'pro_cont'.$sel_records['module_type_id']}; ?>">
                	<!--td-->
						<!--<input type="hidden" name="pos_dx_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $all_dx_codes; ?>" />-->
                        <input type="hidden" name="pos_order_chld_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['order_chld_id']; ?>" />
                     	<input type="hidden" name="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['id']; ?>" id="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>"/>
                        <input type="hidden" name="pos_module_type_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['module_type_id']; ?>" />
						<input type="hidden" name="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="">
                    	<input readonly style="width:100%;" type="hidden" name="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['upc_code'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
                    <!--/td--->
				<?php if($pageName=="contactLensSelection"){?>
					<td>
						<span class="vis_type vision_od">OD</span>
					</td>
				<?php }
				 else //if($pageName == "frameLensSelection")
				 {
				?>
					<td></td>
				<?php 
				 }
				 if($pageName == "frameLensSelection"){
					$row_label = "Frame"; 
				 }
				 else{
					 $row_label =  $sel_records['item_name']; 
				}
				?>
					
					<td>
                    	<input readonly style="width:100%;" type="text" class="itemname" name="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $row_label; ?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))" />
                        <input type="hidden" name="pos_item_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['item_id'];?>" />

                        <?php 
						 if($sel_records['module_type_id']!=1 && $sel_records['module_type_id']!=2)
						 {
						 ?>
                        <input type="hidden" name="pos_qty_on_hand_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_qty_on_hand_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['qty_on_hand'];?>" />
                                               

						<input type="hidden" name="pos_stock_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_stock_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['stock'];?>" />
					<?php } ?>
                    </td>
					<?php if($pageName=="frameLensSelection"): ?>
					<td>
						<input type="text" id="itemDescription_frame_<?php echo $pro_cont; ?>" style="width:100%" class="itemnameDisp" value="<?php echo ($sel_records['item_name_other'])?$sel_records['item_name_other']:$sel_records['item_name']; ?>" />
					</td>
					<?php endif; ?>
                    <td>
                    	<input style="width:100%;" type="text" class="pracodefield" name="pos_item_prac_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_prac_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $proc_code_arr[$sel_records['item_prac_code']];?>" title="<?php echo $proc_code_desc_arr[$sel_records['item_prac_code']]; ?>" autocomplete="off" />
                    </td>
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="pos_price_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_price_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['price'];?>" class="price_cls currency" onChange="this.value=parseFloat(this.value).toFixed(2);$('<?php echo(($sel_records['module_type_id']==3)?'#rtl_price_':'#price_').$pro_cont; ?>').val(this.value)<?php echo(($sel_records['module_type_id']==3)?'.trigger(\'change\')':''); ?>; calculate_all();"/>
                    </td>
					<td>
						 <input type="text" style="width:100%;text-align:right;" class="qty_cls" id="pos_qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" name="pos_qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['qty']; ?>" onChange="changeQty('<?php echo $sel_records['module_type_id']; ?>', this.value, '<?php echo $pro_cont; ?>');" autocomplete="off" <?php echo ($sel_records['module_type_id']==3)?'readonly ':'';?> onKeyUp="validate_qty(this);" />
                        <input type="hidden" name="qty_hidden_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="qty_hidden_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>"  value="<?php echo (isset($sel_records['qty']))?$sel_records['qty']:"1"; ?>">

						<?php
						
						if(!in_array($sel_records['module_type_id'], array(1,2,3)))
						{
						 ?>
						 <input type="hidden" name="qty_reduced_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="qty_reduced_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['qty_reduced']; ?>" />						
						<input type="hidden" name="reduce_qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="reduce_qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="0" />
						
						 <input type="hidden" name="qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="" />

						 <input class="chk_box_pof_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" type="radio" name="use_on_hand_chk_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="1" id="use_on_hand_chk_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" style="display: none;" checked="checked" >						 
                    <?php } ?>
                    	<input type="hidden" class="rqty_cls" id="pos_qty_right_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" name="pos_qty_right_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['qty_right']; ?>" />
                        
					</td>
                    <td>
                    <?php if($sel_records['module_type_id']!=3){?>
                    	<input style="width:100%;text-align:right;" type="text" name="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['price'];?>" class="allowed_cls currency" onChange="calculate_all();"/> 
                        <?php }else
						{?>
							<input style="width:100%;text-align:right;" type="text" name="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['allowed'];?>" class="allowed_cls currency" onChange="calculate_all();"/>
						<?php }?>
                    </td>
                    
                    <td style="display:none">
                    	<input readonly style="width:100%;text-align:right;" type="text" name="total_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_total_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['total_amount']; array_push($itemTotal, $sel_records['total_amount']); ?>" class="price_total currency"  onChange="calculate_all();"/>
						<!-- tax calculations -->
						<input type="hidden" name="tax_p_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="tax_p_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" class="tax_p" value="<?php echo ($sel_records['tax_p']!="")?$sel_records['tax_p']:$taxRate[$sel_records['module_type_id']]; ?>" />
						<input type="hidden" name="tax_v_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="tax_v_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" class="tax_v" value="<?php echo ($sel_records['tax_v']!="")?$sel_records['tax_v']:"0.00"; ?>" />
						<!-- End tax calculations -->
                    </td>
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="ins_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="ins_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"/>
                    </td>
					<td>
                    	<!-- Line item's share in overall discount -->
						<input type="hidden" name="item_overall_discount_<?php echo $pro_cont; ?>" id="item_overall_discount_<?php echo $pro_cont; ?>" value="<?php echo ($sel_records['overall_discount']=="")?0:$sel_records['overall_discount']; ?>" class="item_overall_disc" />
						<input style="width:100%;text-align:right;" type="hidden" name="discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo ($sel_records['discount']=="")?0:$sel_records['discount']; ?>"  onChange="calculate_all();" class="price_disc_per_proc"/>
                    	<input style="width:100%;text-align:right;" type="text" name="pos_read_discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_read_discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['discount'];?>" class="price_disc currency" onChange="changeDiscount(this);" autocomplete="off" />
                    </td>                
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="pt_paid_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pt_paid_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"/>
                    </td>
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="pt_resp_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pt_resp_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records['pt_resp'];?>"  class="resp_cls currency" readonly />
                    </td>
					<td>
						<select name="discount_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="discount_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" class="text_10 disc_code dis_code_class" style="width:100%;">
							<option value="0">Please Select</option>
							<?php echo $discCodes; ?>
						</select>
						<?php
                        if($sel_records['discount_code']!=""){
                            echo '<script type="text/javascript">$(document).ready(function(){$("#discount_code_'.$pro_cont.$pos_lensd.'").val("'.$sel_records['discount_code'].'");});</script>';	
                        }
                        ?>
					</td>
                    <td>
                        <select name="ins_case_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="ins_case_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>');">
                        	<option value="0">Self Pay</option>
<?php foreach($insCases as $insVal=>$insKey): ?>
	<option value="<?php echo $insKey; ?>"><?php echo $insVal; ?></option>
<?php endforeach; ?>
                        </select>
					<?php
                    if($sel_records['ins_case_id']>0){
                        echo '<script type="text/javascript">$(document).ready(function(){$("#ins_case_id_'.$pro_cont.$pos_lensd.'").val("'.$sel_records['ins_case_id'].'");});</script>';
                    }
                    ?>
<?php if($page_name=="other_selection"): ?>
					<input type="hidden" name="del_status_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="del_status_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="0" />
<?php endif; ?>
                    </td>
					<td>
						<input type="checkbox" class="tax_applied" name="tax_applied_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="tax_applied_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="1" <?php echo ($sel_records['tax_applied']=="1")?'checked="checked"':""; ?> onChange="cal_overall_discount()" />
					</td>
					<td>
						<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow('<?php echo $sel_records['module_type_id']; ?>', '<?php echo ${'pro_cont'.$sel_records['module_type_id']}; ?>');"  />
					</td>
                </tr>
<?php
				}
			/*Lens Data by lens_frame_id*/
				if($sel_order_data[$i]['module_type_id']==1){
					$sel_lens_qry="select * from in_order_details where order_id ='$order_id' and patient_id='$patient_id' and del_status='0' and module_type_id='2' and lens_frame_id='".$sel_records['id']."' order by id asc";
					$sel_lens_qry=imw_query($sel_lens_qry);
					if($sel_lens_qry){
						${'pro_cont2'}=(!isset(${'pro_cont2'}))?1:++${'pro_cont2'};
						$lensPro_cont++;
					}
					if(imw_num_rows($sel_lens_qry)>0){
						$sel_records_lens = imw_fetch_array($sel_lens_qry);
						
						$pos_lensd = "_lensD";
						$show_lens_value_arr = array('seg_type_od','design_id_od','material_id_od','a_r_id_od','diopter_id_od', 'seg_type_os','design_id_os','material_id_os','a_r_id_os', 'diopter_id_os', 'oversize_id_', 'lens_other');
						/*'progressive_id','pgx','transition_id','tint_id','polarized_id','edge_id','color_id','uv400'*/
						
						$show_itemized_name_arr = array('lens','design','material','a_r', 'diopter', 'lens','design','material','a_r', 'diopter', 'oversize', 'other');
						/*'transition','progressive','pgx','tint','polarization','edge','color','uv400'*/
						
						$multipleLensVals = array('a_r', 'material');

						//$pro_cont=0;
						$pro=0;
	
						for($l=0;$l<count($show_lens_value_arr);$l++){
							
							if(
								(
									$sel_records_lens[$show_lens_value_arr[$l]] > 0
								) 
								|| 
								(
									$sel_records_lens[$show_lens_value_arr[$l]]!=""
									&& 
									$show_itemized_name_arr[$l]=="other"
								) 
								|| 
								in_array($show_itemized_name_arr[$l], $multipleLensVals)
								||
								$show_itemized_name_arr[$l] === 'diopter'
								||
								$show_itemized_name_arr[$l] === 'oversize'
							)
							{
								
								/*Pos Rows for multiselect values*/
								if(in_array($show_itemized_name_arr[$l], $multipleLensVals)!==false)
								{
									
									$vision_val = explode('_', $show_lens_value_arr[$l]);
									$vision		= array_pop($vision_val);
									$vision_qry=" AND vision='".$vision."'";
									
									$sel_price_qry="select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records_lens['id']."' and patient_id='$patient_id' and itemized_name LIKE '".$show_itemized_name_arr[$l]."%' and del_status='0' ".$vision_qry." ORDER BY id ASC";
									
									$sel_price_qry=imw_query($sel_price_qry);
									while($sel_lens_price_data=imw_fetch_array($sel_price_qry)){
										$pro++;
										//$nName = substr($sel_lens_price_data['itemized_name'], 0, strrpos($sel_lens_price_data['itemized_name'], "_"));
										$nName = $sel_lens_price_data['itemized_name'];
										$nName = trim($nName);
										if($pro==1) { $clas = ""; } else { $clas = "even"; }
										/*Fix for Previous Data*/
										$itmPrac = ($proc_code_arr[$sel_lens_price_data['item_prac_code']]!="" && $sel_lens_price_data['item_prac_code']!="0")?$proc_code_arr[$sel_lens_price_data['item_prac_code']]:$sel_lens_price_data['item_prac_code_default'];
										
										$itemized_name = $sel_lens_price_data['itemized_name'];
?>
										<tr id="<?php echo $sel_records_lens['module_type_id']."_".${'pro_cont'.$sel_records_lens['module_type_id']}."_".$itemized_name."_display_".$sel_lens_price_data['vision']; ?>" class="<?php echo $clas; ?> multiVals">
											<!--td-->
												<!--<input type="hidden" name="pos_dx_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $all_dx_codes; ?>" />-->
												<input type="hidden" name="pos_order_chld_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['order_chld_id']; ?>" />
												<input type="hidden" name="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['id']; ?>" id="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>"/>
												<input type="hidden" name="lens_item_detail_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['itemized_id']; ?>" />
												<input type="hidden" name="lens_item_detail_name_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_detail_name_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $itemized_name; ?>" />
												<input type="hidden" name="lens_price_detail_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_price_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['id']; ?>" />
												<input type="hidden" name="pos_module_type_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['module_type_id']; ?>" />
												<input type="hidden" name="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="">
											<?php
												if($pro==1) { /*Not in AddNewRow()*/ 
											?>
												<input readonly style="width:100%" type="hidden" name="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens[''];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
											<?php
												}
												$lensItemName = "";
												if(substr($nName,0,3)=="a_r")
													$lensItemName = "Treatment";
												elseif($nName=="lens")
													$lensItemName = "Seg type";
												elseif(substr($nName,0,8)=="material")
													$lensItemName = "Material";
												else
													$lensItemName = ucfirst($nName);
											?>
											<!--/td-->
											<td>
												<span class="vis_type vision_<?php echo $sel_lens_price_data['vision']; ?>"><?php echo $sel_lens_price_data['vision']; ?></span>
												<input type="hidden" name="pos_lens_item_vision_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_vision_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['vision']; ?>" class="row_vision_value" />
											</td>
											<td> 
												<?php /*Not in AddNewRow()*/ ?>
												<input readonly style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $lensItemName; ?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))"/>
												
												<input readonly style="width:100%" type="hidden" name="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['item_name'];?>"/>
												<input type="hidden" name="pos_item_id_<?php echo $lensPro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['item_id'];?>" />
											</td>
	<?php if($pageName=="frameLensSelection"): ?>
											<td>
												<input readonly style="width:100%;" type="text" class="itemnameDisp" name="pos_lens_item_name_disp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_name_disp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>"  value="<?php echo $sel_lens_price_data['item_description']; ?>" />
											</td>
	<?php endif; ?>
											<td>
												<input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($proc_code_arr[$sel_lens_price_data['item_prac_code']]!="" && $sel_lens_price_data['item_prac_code']!="0")?$proc_code_arr[$sel_lens_price_data['item_prac_code']]:$sel_lens_price_data['item_prac_code_default'];?>" title="<?php echo $proc_code_desc_arr[$sel_lens_price_data['item_prac_code']]; ?>" />
												<!--onChange="show_price_from_praccode(this,'price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>','pos'); calculate_all();"-->
											</td>
											<td>
												<input style="width:100%; text-align:right;" type="text" name="lens_item_price_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_price_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['wholesale_price'];?>" class="price_cls currency" onChange="calculate_all();"/> 
											</td>
											<td>
<!-- onChange="changeQty('<?php echo $sel_records['module_type_id']; ?>', this.value, '<?php echo $pro_cont; ?>');"  -->
												<input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_qty_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['qty']; ?>" autocomplete="off" onChange="calculate_all();" onKeyUp="validate_qty(this);" />
												<input type="hidden" class="rqty_cls" name="pos_qty_right_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['qty_right']; ?>" />
											</td>
											<td>
												<input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_allowed_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['allowed'];?>" class="allowed_cls currency" onChange="calculate_all();"/> 
											</td> 
											<td style="display:none">
												<input readonly style="width:100%; text-align:right;" type="text" name="lens_item_total_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_total_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['total_amt']; array_push($itemTotal, $sel_lens_price_data['total_amt']); ?>" class="price_total currency"  onChange="calculate_all();"/>
												<!-- tax calculations -->
												<input type="hidden" name="tax_p_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_p_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="tax_p" value="<?php echo ($sel_lens_price_data['tax_p']!="")?$sel_lens_price_data['tax_p']:$taxRate[$sel_lens_price_data['module_type_id']]; ?>" />
						<input type="hidden" name="tax_v_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_v_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="tax_v" value="<?php echo ($sel_lens_price_data['tax_v']!="")?$sel_lens_price_data['tax_v']:"0.00"; ?>" />
												<!-- End tax calculations -->
											</td>
											<td>
												<input style="width:100%; text-align:right;" type="text" name="ins_amount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="ins_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"/>
											</td>
											<td>
												<!-- Line item's share in overall discount -->
												<input type="hidden" name="lens_item_overall_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_overall_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($sel_lens_price_data['overall_discount']=="")?0:$sel_lens_price_data['overall_discount']; ?>" class="item_overall_disc" />
												<input type="hidden" name="lens_item_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($sel_lens_price_data['discount']=="")?0:$sel_lens_price_data['discount']; ?>" onChange="calculate_all();" class="price_disc_per_proc"/>
												<input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_read_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off"/>
											</td>
											<td>
												<input style="width:100%; text-align:right;" type="text" name="pt_paid_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pt_paid_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"/>
											</td>
											<td>
												<input style="width:100%; text-align:right;" type="text" name="pt_resp_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pt_resp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['pt_resp'];?>" class="resp_cls currency" readonly/>
											</td>
											<td>
												<select name="discount_code_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="discount_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" class="text_10 disc_code dis_code_class" style="width:100%;">
													<option value="0">Please Select</option>
													<?php
														$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
														while($sel_write=imw_fetch_array($sel_rec)){
													?>
														<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_lens_price_data['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?></option>
													<?php 
														}
													?>
												</select>	
											</td>
											<td>
												<select name="ins_case_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="ins_case_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>');">
													<option value="0">Self Pay</option>
													<?php
														foreach($ins_case_arr as $key => $insCoName){
													?>
															<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
													<?php
														}
													?>
												</select>
												<input type="hidden" name="del_status_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="del_status_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="0" class="del_status" />
											</td>
											<td>
												<input type="checkbox" class="tax_applied" name="tax_applied_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_applied_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="1" <?php echo ($sel_lens_price_data['tax_applied']=="1")?'checked="checked"':""; ?> onChange="cal_overall_discount()" />
											</td>
											<td>
												<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow('<?php echo $sel_records_lens['module_type_id']; ?>', '<?php echo ${'pro_cont'.$sel_records_lens['module_type_id']}; ?>', '<?php echo $sel_lens_price_data['itemized_name']; ?>', '<?php echo $sel_lens_price_data['vision']; ?>');" />
											</td>
										</tr>
<?php
									}
									continue;
								}
								/*End Pos Rows for multiselect values*/
								
								$vision_val = explode('_', $show_lens_value_arr[$l]);
								$vision		= array_pop($vision_val);
								$vision_qry=" AND vision='".$vision."'";

								// print "select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records_lens['id']."' and patient_id='$patient_id' and itemized_name='".$show_itemized_name_arr[$l]."' and del_status='0' ".$vision_qry;

								
								$sel_price_qry=imw_query("select * from in_order_lens_price_detail where order_id ='$order_id' and order_detail_id='".$sel_records_lens['id']."' and patient_id='$patient_id' and itemized_name='".$show_itemized_name_arr[$l]."' and del_status='0' ".$vision_qry);
								while($sel_lens_price_data=imw_fetch_array($sel_price_qry)){
									$pro++;
									if($pro==1) { $clas = ""; } else { $clas = "even"; }
								
									/*if($sel_lens_price_data['itemized_name'] == "lens"
										&& (trim($sel_lens_price_data['wholesale_price'])==""
										|| $sel_lens_price_data['wholesale_price']=="0.00")){
										$clas .=  " hideRow1";
									}*/
								?>
									<tr id="<?php echo $sel_records_lens['module_type_id']."_".${'pro_cont'.$sel_records_lens['module_type_id']}."_".$sel_lens_price_data['itemized_name']."_display_".$vision; ?>" class="<?php echo $clas; ?>">
										<!--td-->
											<!--<input type="hidden" name="pos_dx_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $all_dx_codes; ?>" />-->
											<input type="hidden" name="pos_order_chld_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['order_chld_id']; ?>" />
											<input type="hidden" name="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['id']; ?>" id="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>"/>
											<input type="hidden" name="lens_item_detail_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['itemized_id']; ?>" />
											<input type="hidden" name="lens_item_detail_name_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_detail_name_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['itemized_name']; ?>" />
											<input type="hidden" name="lens_price_detail_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_price_detail_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['id']; ?>" />
											<input type="hidden" name="pos_module_type_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['module_type_id']; ?>" />
											<input type="hidden" name="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="">
										<?php
											if($pro==1) { /*Not in AddNewRow()*/ 
										?>
											<input readonly style="width:100%" type="hidden" name="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['upc_code'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'), '<?php echo $pro_cont; ?>');"/>
										<?php
											}
										?>
										<!--/td-->
										
										<td>
											<span class="vis_type vision_<?php echo $vision; ?>"><?php echo $vision; ?></span>
											<input type="hidden" name="pos_lens_item_vision_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_vision_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $vision; ?>" class="row_vision_value" />
										</td>
										
										<td <?php echo (($show_itemized_name_arr[$l]==="diopter" || $show_itemized_name_arr[$l]==="oversize")?'colspan="2"':''); ?> > 
											<?php /*Not in AddNewRow()*/ ?>
											<input readonly style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php if(substr($sel_lens_price_data['itemized_name'],0,3)=="a_r") { echo "Treatment"; } elseif($sel_lens_price_data['itemized_name']=="lens") { echo "Seg type"; }elseif($sel_lens_price_data['itemized_name']=="uv400"){echo "UV 400"; }elseif($sel_lens_price_data['itemized_name']=="diopter"){echo "Prism Diopter Charges"; }elseif($sel_lens_price_data['itemized_name']=="oversize"){echo "Oversized Lens Charges"; } else { echo ucfirst($sel_lens_price_data['itemized_name']); } ?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>'))"/>
											<input readonly style="width:100%" type="hidden" name="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['item_name'];?>"/>
											<input type="hidden" name="pos_item_id_<?php echo $lensPro_cont; ?><?php echo $pos_lensd; ?>" id="pos_item_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['item_id'];?>" />
										<?php echo (($show_itemized_name_arr[$l]!=="diopter" && $show_itemized_name_arr[$l]!=="oversize")?'</td><td>':''); ?>										
<?php if($pageName=="frameLensSelection"): ?>
										
											<input readonly style="width:100%;" type="<?php echo (($show_itemized_name_arr[$l]!=="diopter" && $show_itemized_name_arr[$l]!=="oversize")?'text' : 'hidden'); ?>" class="itemnameDisp" name="pos_lens_item_name_disp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_lens_item_name_disp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['item_description']; ?>" />
										</td>
<?php endif; ?>
										<td>
											<input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="item_prac_code_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($proc_code_arr[$sel_lens_price_data['item_prac_code']]!="" && $sel_lens_price_data['item_prac_code']!="0")?$proc_code_arr[$sel_lens_price_data['item_prac_code']]:$sel_lens_price_data['item_prac_code_default'];?>" title="<?php echo $proc_code_desc_arr[$sel_lens_price_data['item_prac_code']]; ?>" />
										<!--onChange="show_price_from_praccode(this,'price_<?php echo $pro_cont; ?>_<?php echo $pro; ?>','pos'); calculate_all();"-->
										</td>
										<td>
											<input style="width:100%; text-align:right;" type="text" name="lens_item_price_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_price_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['wholesale_price'];?>" class="price_cls currency" onChange="calculate_all();"/> 
										</td>
										<td>
<!-- onChange="changeQty('<?php echo $sel_records['module_type_id']; ?>', this.value, '<?php echo $pro_cont; ?>');" -->
											<input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_qty_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['qty']; ?>" autocomplete="off" onChange="calculate_all();" onKeyUp="validate_qty(this);" />
											<input type="hidden" class="rqty_cls" name="pos_qty_right_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_records_lens['qty_right']; ?>" />
										</td>
										<td>
											<input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_allowed_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['allowed'];?>" class="allowed_cls currency" onChange="calculate_all();"/> 
										</td>
										<td style="display:none">
											<input readonly style="width:100%; text-align:right;" type="text" name="lens_item_total_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_total_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['total_amt']; array_push($itemTotal, $sel_lens_price_data['total_amt']); ?>" class="price_total currency"  onChange="calculate_all();"/>
											<!-- tax calculations -->
												<input type="hidden" name="tax_p_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_p_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="tax_p" value="<?php echo ($sel_lens_price_data['tax_p']!="")?$sel_lens_price_data['tax_p']:$taxRate[$sel_lens_price_data['module_type_id']]; ?>" />
						<input type="hidden" name="tax_v_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_v_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="tax_v" value="<?php echo ($sel_lens_price_data['tax_v']!="")?$sel_lens_price_data['tax_v']:"0.00"; ?>" />
											<!-- End tax calculations -->
										</td>
										<td>
											<input style="width:100%; text-align:right;" type="text" name="ins_amount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="ins_amount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['ins_amount'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"/>
										</td>
										<td>
											<!-- Line item's share in overall discount -->
											<input type="hidden" name="lens_item_overall_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_overall_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($sel_lens_price_data['overall_discount']=="")?0:$sel_lens_price_data['overall_discount']; ?>" class="item_overall_disc" />
											<input type="hidden" name="lens_item_discount_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo ($sel_lens_price_data['discount']=="")?0:$sel_lens_price_data['discount']; ?>" onChange="calculate_all();" class="price_disc_per_proc"/>
											<input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pos_read_discount_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off"/>
										</td> 
										<td>
											<input style="width:100%; text-align:right;" type="text" name="pt_paid_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pt_paid_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['pt_paid'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"/>
										</td>
										<td>
											<input style="width:100%; text-align:right;" type="text" name="pt_resp_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="pt_resp_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="<?php echo $sel_lens_price_data['pt_resp'];?>" class="resp_cls currency" readonly/>
										</td>
										<td>
											<select name="discount_code_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="discount_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" class="text_10 disc_code dis_code_class" style="width:100%;">
												<option value="0">Please Select</option>
												<?php
													$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
													while($sel_write=imw_fetch_array($sel_rec)){
												?>
													<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_lens_price_data['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?></option>
												<?php 
													}
												?>
											</select>	
										</td>
										<td>
											<select name="ins_case_id_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="ins_case_id_<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>');">
												<option value="0">Self Pay</option>
												<?php
													foreach($ins_case_arr as $key => $insCoName){
												?>
														<option value="<?php echo $key ; ?>" <?php if($key==$sel_lens_price_data['ins_case_id']) echo 'SELECTED'; ?>><?php echo $insCoName; ?></option>
												<?php
													}
												?>
											</select>
											<input type="hidden" name="del_status_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="del_status_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="0" class="del_status" />
										</td>
										<td>
											<input type="checkbox" class="tax_applied" name="tax_applied_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" id="tax_applied_<?php echo $lensPro_cont; ?>_<?php echo $pro; ?><?php echo $pos_lensd; ?>" value="1" <?php echo ($sel_lens_price_data['tax_applied']=="1")?'checked="checked"':""; ?> onChange="cal_overall_discount()" />
										</td>
										<td>
											<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow('<?php echo $sel_records_lens['module_type_id']; ?>', '<?php echo ${'pro_cont'.$sel_records_lens['module_type_id']}; ?>', '<?php echo $sel_lens_price_data['itemized_name']; ?>','<?php echo $vision;?>');" />
										</td>
									</tr>
							<?php 
								}
							} 
						}
?>
			<input type="hidden" name="lens_item_count_<?php echo $lensPro_cont;?>_lensD" id="lens_item_count_<?php echo $lensPro_cont;?>_lensD" value="<?php echo $pro; ?>">
<?
					}
				}
	/*End Lens Data by lens_frame_id*/
	
	/*Disinfectent POS Row for Contact Lens*/
			elseif($sel_order_data[$i]['module_type_id']==3){
			/*OS Row*/
			if($sel_records['item_id_os']!='0' || $sel_records['upc_code_os']!='0'){
			?>
				<tr id="<?php echo $sel_records['module_type_id']."_".${'pro_cont'.$sel_records['module_type_id']}.'_os'; ?>"> 
                	<!--td-->
						<!--<input type="hidden" name="pos_dx_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>" value="<?php echo $all_dx_codes; ?>" />-->
                        <input type="hidden" name="pos_order_chld_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['order_chld_id_os']; ?>" />
                     	<input type="hidden" name="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['id']; ?>" id="pos_order_detail_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os"/>
                        <input type="hidden" name="pos_module_type_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['module_type_id']; ?>" />
						<input type="hidden" name="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_upc_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="">
                    	<input readonly style="width:100%;" type="hidden" name="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_upc_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['upc_code_os'];?>"  onchange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>_os'), '<?php echo $pro_cont; ?>_os');"/>
                    <!--/td--->
				<?php if($pageName=="contactLensSelection"){ ?>
					<td>
						<span class="vis_type vision_os">OS</span>
					</td>
				<?php } ?>
                    <td>
                    	<input readonly style="width:100%;" type="text" class="itemname" name="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_item_name_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['item_name_os'];?>" onChange="javascript:upc(document.getElementById('upc_id_<?php echo $pro_cont; ?>_os'))"/>
                        <input type="hidden" name="pos_item_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_item_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['item_id_os'];?>" />
                    </td>
                    <td>
                    	<input style="width:100%;" type="text" class="pracodefield" name="pos_item_prac_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_item_prac_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $proc_code_arr[$sel_records['item_prac_code_os']];?>" title="<?php echo $proc_code_desc_arr[$sel_records['item_prac_code_os']]; ?>" autocomplete="off" />
                    </td>
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="pos_price_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_price_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['price_os'];?>" class="price_cls currency" onChange="this.value=parseFloat(this.value).toFixed(2);$('<?php echo(($sel_records['module_type_id']==3)?'#rtl_price_':'#price_').$pro_cont.'_os'; ?>').val(this.value)<?php echo(($sel_records['module_type_id']==3)?'.trigger(\'change\')':''); ?>; calculate_all();"/>
                    </td>
					<td>
						 <input type="text" style="width:100%;text-align:right;" class="qty_cls" id="pos_qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" name="pos_qty_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['qty_right']; ?>" onChange="changeQty('<?php echo $sel_records['module_type_id']; ?>', this.value, '<?php echo $pro_cont; ?>');" autocomplete="off" <?php echo ($sel_records['module_type_id']==3)?'readonly ':'';?> onKeyUp="validate_qty(this);" />
                        <input type="hidden" class="rqty_cls" id="pos_qty_right_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" name="pos_qty_right_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['qty_right']; ?>" />
					</td>
                    <td>
                    <?php if($sel_records['module_type_id']!=3){?>
                    	<input style="width:100%;text-align:right;" type="text" name="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['price_os'];?>" class="allowed_cls currency" onChange="calculate_all();"/> 
                        <?php }else
						{?>
							<input style="width:100%;text-align:right;" type="text" name="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_allowed_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['allowed_os'];?>" class="allowed_cls currency" onChange="calculate_all();"/>
						<?php }?>
                    </td>
                    
                    <td style="display:none">
                    	<input readonly style="width:100%;text-align:right;" type="text" name="total_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_total_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['total_amount_os']; array_push($itemTotal, $sel_records['total_amount_os']); ?>" class="price_total currency"  onChange="calculate_all();"/>
						<!-- tax calculations -->
						<input type="hidden" name="tax_p_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="tax_p_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" class="tax_p" value="<?php echo ($sel_records['tax_p_os']!="")?$sel_records['tax_p_os']:$taxRate[$sel_records['module_type_id']]; ?>" />
						<input type="hidden" name="tax_v_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="tax_v_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" class="tax_v" value="<?php echo ($sel_records['tax_v_os']!="")?$sel_records['tax_v_os']:"0.00"; ?>" />
						<!-- End tax calculations -->
                    </td>
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="ins_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="ins_amount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['ins_amount_os'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"/>
                    </td>
					<td>
                    	<!-- Line item's share in overall discount -->
						<input type="hidden" name="item_overall_discount_<?php echo $pro_cont; ?>_os" id="item_overall_discount_<?php echo $pro_cont; ?>_os" value="<?php echo ($sel_records['overall_discount_os']=="")?0:$sel_records['overall_discount_os']; ?>" class="item_overall_disc" />
						<input style="width:100%;text-align:right;" type="hidden" name="discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo ($sel_records['discount_os']=="")?0:$sel_records['discount_os']; ?>"  onChange="calculate_all();" class="price_disc_per_proc"/>
                    	<input style="width:100%;text-align:right;" type="text" name="pos_read_discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pos_read_discount_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['discount_os'];?>" class="price_disc currency" onChange="changeDiscount(this);" autocomplete="off" />
                    </td>                
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="pt_paid_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pt_paid_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['pt_paid_os'];?>"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"/>
                    </td>
                    <td>
                    	<input style="width:100%;text-align:right;" type="text" name="pt_resp_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="pt_resp_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="<?php echo $sel_records['pt_resp_os'];?>"  class="resp_cls currency" readonly />
                    </td>
					<td>
						<select name="discount_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="discount_code_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" class="text_10 disc_code dis_code_class" style="width:100%;">
							<option value="0">Please Select</option>
							<?php echo $discCodes; ?>
						</select>
						<?php
                        if($sel_records['discount_code_os']!=""){
                            echo '<script type="text/javascript">$(document).ready(function(){$("#discount_code_'.$pro_cont.$pos_lensd.'_os").val("'.$sel_records['discount_code_os'].'");});</script>';	
                        }
                        ?>
					</td>
                    <td>
                        <select name="ins_case_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="ins_case_id_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('<?php echo $pro_cont.'_os'; ?>');">
                        	<option value="0">Self Pay</option>
<?php foreach($insCases as $insVal=>$insKey): ?>
	<option value="<?php echo $insKey; ?>"><?php echo $insVal; ?></option>
<?php endforeach; ?>
                        </select>
					<?php
                    if($sel_records['ins_case_id_os']>0){
                        echo '<script type="text/javascript">$(document).ready(function(){$("#ins_case_id_'.$pro_cont.$pos_lensd.'_os").val("'.$sel_records['ins_case_id_os'].'");});</script>';
                    }
                    ?>
                    </td>
					<td>
						<input type="checkbox" class="tax_applied" name="tax_applied_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" id="tax_applied_<?php echo $pro_cont; ?><?php echo $pos_lensd; ?>_os" value="1" <?php echo ($sel_records['tax_applied_os']=="1")?'checked="checked"':""; ?> onChange="cal_overall_discount()" />
					</td>
					<td>
						<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow('<?php echo $sel_records['module_type_id']; ?>', '<?php echo ${'pro_cont'.$sel_records['module_type_id']}; ?>','','_os');"  />
					</td>
                </tr>
			<?php
			}
			/*End OS Row*/
				$sql_di = "SELECT * FROM `in_order_cl_detail` WHERE `order_detail_id`='".$sel_records['id']."' AND `order_id`='".$sel_records['order_id']."' AND `module_type_id`='".$sel_records['module_type_id']."' AND `del_status`=0";
				$sel_data = imw_query($sql_di);
					if($sel_data && imw_num_rows($sel_data)>0){
						$sel_data_di = imw_fetch_assoc($sel_data);
				?>
						<tr id="<?php echo $sel_records['module_type_id']."_".${'pro_cont'.$sel_records['module_type_id']}; ?>_di">
							<!--td-->
								<input type="hidden" name="di_id_<?php echo $pro_cont; ?>" id="di_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['id']; ?>">
								<input type="hidden" name="di_order_detail_id_<?php echo $pro_cont; ?>" id="di_order_detail_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['order_detail_id']; ?>">
								<input type="hidden" name="di_module_type_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['module_type_id']; ?>">
								<input type="hidden" name="di_item_type_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['item_type']; ?>">
							<!--/td-->
							<td></td>
							<td>
								<input readonly style="width:100%;" type="text" class="itemname" name="di_item_name_<?php echo $pro_cont; ?>" id="di_item_name_<?php echo $pro_cont; ?>" value="<?php echo $disinfectent[$sel_data_di['item_id']]['name']; ?>">
								<input type="hidden" name="di_item_id_<?php echo $pro_cont; ?>" id="di_item_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['item_id']; ?>">
							</td>
							<td>
								<input style="width:100%;" type="text" class="pracodefield" name="di_item_prac_code_<?php echo $pro_cont; ?>" id="di_item_prac_code_<?php echo $pro_cont; ?>" value="<?php echo $proc_code_arr[$sel_data_di['prac_code_id']]; ?>" title="<?php echo $proc_code_desc_arr[$sel_data_di['prac_code_id']]; ?>" autocomplete="off">
								<input type="hidden" name="di_prac_code_id_<?php echo $pro_cont; ?>" id="di_prac_code_id_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['prac_code_id']; ?>">
							</td>
							<td>
								<input style="width: 100%; text-align: right;" type="text" name="di_price_<?php echo $pro_cont; ?>" id="di_price_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['price']; ?>" class="price_cls currency" onchange="this.value=parseFloat(this.value).toFixed(2);calculate_all();">
							</td>
							<td>
								<input type="text" style="width:100%; text-align:right;" class="qty_cls" id="di_qty_<?php echo $pro_cont; ?>" name="di_qty_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['qty']; ?>" onchange="calculate_all();" autocomplete="off" onKeyUp="validate_qty(this);">
								<input type="hidden" class="rqty_cls" name="di_rqty" id="di_rqty" value="0" />
							</td>
							<td>
								<input style="width: 100%; text-align: right;" type="text" name="di_allowed_<?php echo $pro_cont; ?>" id="di_allowed_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['allowed']; ?>" class="allowed_cls currency" onchange="calculate_all();">
							</td>
							<td style="display:none">
								<input readonly style="width: 100%; text-align: right;" type="text" name="di_total_amount_<?php echo $pro_cont; ?>" id="di_total_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['total_amount']; ?>" class="price_total currency" onchange="calculate_all();"><?php array_push($itemTotal, $sel_data_di['total_amount']);?>
								<!-- tax calculations -->
								<input type="hidden" name="di_tax_p_<?php echo $pro_cont; ?>" id="di_tax_p_<?php echo $pro_cont; ?>" class="tax_p" value="<?php echo ($sel_data_di['tax_p']!="")?$sel_data_di['tax_p']:$taxRate[$sel_records['module_type_id']]; ?>" />
								<input type="hidden" name="di_tax_v_<?php echo $pro_cont; ?>" id="di_tax_v_<?php echo $pro_cont; ?>" class="tax_v" value="<?php echo ($sel_records['tax_v']!="")?$sel_records['tax_v']:"0.00"; ?>" />
								<!-- End tax calculations -->
							</td>
							<td>
								<input style="width: 100%; text-align: right;" type="text" name="di_ins_amount_<?php echo $pro_cont; ?>" id="di_ins_amount_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['ins_amount']; ?>" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency">
							</td>
							<td>
								<!-- Line item's share in overall discount -->
								<input type="hidden" name="di_overall_discount_<?php echo $pro_cont; ?>" id="di_overall_discount_<?php echo $pro_cont; ?>" value="<?php echo ($sel_data_di['overall_discount']=="")?0:$sel_data_di['overall_discount']; ?>" class="item_overall_disc" />
								<input style="width:100%;text-align:right;" type="hidden" name="di_discount_<?php echo $pro_cont; ?>" id="di_discount_<?php echo $pro_cont; ?>" value="<?php echo ($sel_data_di['discount']=="")?0:$sel_data_di['discount']; ?>" onchange="calculate_all();" class="price_disc_per_proc">
								<input style="width: 100%; text-align: right;" type="text" name="di_read_discount_<?php echo $pro_cont; ?>" id="di_read_discount_<?php echo $pro_cont; ?>" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off">
							</td>
							<td>
								<input style="width: 100%; text-align: right;" type="text" name="di_pt_paid_<?php echo $pro_cont; ?>" id="di_pt_paid_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['pt_paid']; ?>" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency">
							</td>
							<td>
								<input style="width: 100%; text-align: right;" type="text" name="di_pt_resp_<?php echo $pro_cont; ?>" id="di_pt_resp_<?php echo $pro_cont; ?>" value="<?php echo $sel_data_di['pt_resp']; ?>" class="resp_cls currency" readonly>
							</td>
							<td>
								<select name="di_discount_code_<?php echo $pro_cont; ?>" id="di_discount_code_<?php echo $pro_cont; ?>" class="text_10 disc_code dis_code_class" style="width:100%;">
								<option value="0">Please Select</option>
					<?php
							$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
							while($sel_write=imw_fetch_array($sel_rec)){
					?>
								<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$sel_data_di['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?></option>
					<?php } ?>
								</select>
							</td>
							<td>
								<select name="di_ins_case_id_<?php echo $pro_cont; ?>" id="di_ins_case_id_<?php echo $pro_cont; ?>" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('<?php echo $pro_cont; ?>_di');">
									<option value="0">Self Pay</option>
							<?php foreach($ins_case_arr as $key => $insCoName){ ?>
									<option value="<?php echo $key ; ?>" <?php if($key==$sel_data_di['ins_case_id']){ echo 'SELECTED'; } ?>><?php echo $insCoName; ?></option>
							<?php } ?>
								</select>
								<input type="hidden" name="di_del_item_<?php echo $pro_cont; ?>" id="di_del_item_<?php echo $pro_cont; ?>" value="0" />
							</td>
							<td>
								<input type="checkbox" class="tax_applied" name="di_tax_applied_<?php echo $pro_cont; ?>" id="di_tax_applied_<?php echo $pro_cont; ?>" value="1" <?php echo ($sel_data_di['tax_applied']=="1")?'checked="checked"':""; ?> onChange="cal_overall_discount()" />
							</td>
							<td>
								<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onclick="delPosRowDis('3', '<?php echo $pro_cont; ?>');">
							</td>
						</tr>
		<?php	}
		} 
	/*End Disinfectent POS Row for Contact Lens*/
			} 
		}

/*Custom Charge Rows*/
$cs_row_count = 0;
while($cs_row = imw_fetch_assoc($cs_rows)){
	$cs_row_count++;
?>	
	<tr id="9_<?php echo $cs_row_count; ?>" class="customCharge">
	<!--td-->
		<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][pos_order_chld_id]" value="<?php echo $cs_row['order_chld_id']; ?>" />
		<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][pos_order_detail_id]" id="pos_order_detail_id_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['id']; ?>" />
		<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][pos_module_type_id]" value="9" />
		<!--/td-->
	<?php //if($pageName!="other_selection"): ?>
		<td></td>
	<?php //endif; ?>
	<?php if($pageName=="frameLensSelection"): ?>
		<td colspan="2">
	<?php else: ?>
		<td>
	<?php endif; ?>
			<input style="width:100%;" type="text" class="itemname" name="cs[<?php echo $cs_row_count; ?>][pos_item_name]" id="pos_item_name_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['item_name']; ?>">
			<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][pos_item_id]" id="pos_item_id_9_<?php echo $cs_row_count; ?>" value="" />
		</td>
		<td>
			<input style="width:100%;" type="text" class="pracodefield" name="cs[<?php echo $cs_row_count; ?>][pos_item_prac_code]" id="pos_item_prac_code_9_<?php echo $cs_row_count; ?>" value="<?php echo $proc_code_arr[$cs_row['item_prac_code']]; ?>" title="<?php echo $proc_code_desc_arr[$cs_row['item_prac_code']]; ?>" autocomplete="off" />
			<input type="hidden" id="pos_prac_id_9_<?php echo $cs_row_count; ?>" value="" class="hiddenPracId" onChange="getChargeByPracCode(this);" />
		</td>
		<td>
			<input style="width:100%;text-align:right;" type="text" name="cs[<?php echo $cs_row_count; ?>][pos_price]" id="pos_price_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['price']; ?>" class="price_cls currency" onChange="this.value=parseFloat(this.value).toFixed(2);calculate_all();" />
		</td>
		<td>
			<input type="text" style="width:100%; text-align:right;" class="qty_cls" id="pos_qty_9_<?php echo $cs_row_count; ?>" name="cs[<?php echo $cs_row_count; ?>][pos_qty]" value="<?php echo $cs_row['qty']; ?>" onChange="calculate_all();" autocomplete="off" onKeyUp="validate_qty(this);" />
			<input type="hidden" class="rqty_cls" id="pos_qty_right_9_<?php echo $cs_row_count; ?>" name="cs[<?php echo $cs_row_count; ?>][pos_qty_right]" value="0" />
		</td>
		<td>
			<input style="width:100%;text-align:right;" type="text" name="cs[<?php echo $cs_row_count; ?>][pos_allowed]" id="pos_allowed_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['allowed']; ?>" class="allowed_cls currency" onChange="calculate_all();" />
		</td>
		<td style="display:none">
			<input readonly style="width:100%;text-align:right;" type="text" name="cs[<?php echo $cs_row_count; ?>][total_amount]" id="pos_total_amount_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['total_amount']; ?>" class="price_total currency"  onChange="calculate_all();" />
			<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][tax_p]" id="tax_p_9_<?php echo $cs_row_count; ?>" class="tax_p" value="<?php echo $cs_row['tax_rate']; ?>" />
			<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][tax_v]" id="tax_v_9_<?php echo $cs_row_count; ?>" class="tax_v" value="<?php echo $cs_row['tax_paid']; ?>" />
		</td>
		<td>
			<input style="width:100%;text-align:right;" type="text" name="cs[<?php echo $cs_row_count; ?>][ins_amount]" id="ins_amount_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['ins_amount']; ?>" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency" />
		</td>
		<td>
			<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][item_overall_discount]" id="item_overall_discount_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['overall_discount']; ?>" class="item_overall_disc" />
			<input style="width:100%;text-align:right;" type="hidden" name="cs[<?php echo $cs_row_count; ?>][discount]" id="pos_discount_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['discount']; ?>" onChange="calculate_all();" class="price_disc_per_proc" />
			<input style="width:100%;text-align:right;" type="text" name="cs[<?php echo $cs_row_count; ?>][pos_read_discount]" id="pos_read_discount_9_<?php echo $cs_row_count; ?>" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />
		</td>
		<td>
			<input style="width:100%;text-align:right;" type="text" name="cs[<?php echo $cs_row_count; ?>][pt_paid]" id="pt_paid_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['pt_paid']; ?>" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency" />
		</td>
		<td>
			<input style="width:100%;text-align:right;" type="text" name="cs[<?php echo $cs_row_count; ?>][pt_resp]" id="pt_resp_9_<?php echo $cs_row_count; ?>" value="<?php echo $cs_row['pt_resp']; ?>" class="resp_cls currency" readonly />
		</td>
		<td>
			<select name="cs[<?php echo $cs_row_count; ?>][discount_code]" id="discount_code_9_<?php echo $cs_row_count; ?>" class="text_10 disc_code dis_code_class" style="width:100%;" onChange="discountChanged(this);">
				<option value="0">Please Select</option>
				<?php echo $discCodes; ?>
			</select>
			<?php
			if($cs_row['discount_code']!=""){
				echo '<script type="text/javascript">$(document).ready(function(){$("#discount_code_9_'.$cs_row_count.'").val("'.$cs_row['discount_code'].'");});</script>';	
			}
			?>
		</td>
		<td>
			<select name="cs[<?php echo $cs_row_count; ?>][ins_case_id]" id="ins_case_id_9_<?php echo $cs_row_count; ?>" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('9_1');">
				<option value="0">Self Pay</option>
				<?php foreach($insCases as $insVal=>$insKey): ?>
					<option value="<?php echo $insKey; ?>"><?php echo $insVal; ?></option>
				<?php endforeach; ?>
			</select>
			<input type="hidden" name="cs[<?php echo $cs_row_count; ?>][del_status]" id="del_status_9_<?php echo $cs_row_count; ?>" value="0" class="del_status" />
			<?php
			if($cs_row['ins_case_id']>0){
				echo '<script type="text/javascript">$(document).ready(function(){$("#ins_case_id_9_'.$cs_row_count.'").val("'.$cs_row['ins_case_id'].'");});</script>';
			}
			?>
		</td>
		<td>
			<input type="checkbox" class="tax_applied" name="cs[<?php echo $cs_row_count; ?>][tax_applied]" id="tax_applied_9_<?php echo $cs_row_count; ?>" value="1" <?php echo ($cs_row['tax_applied']=="1")?'checked="checked"':""; ?> onChange="cal_overall_discount()" />
		</td>
		<td>
			<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow('9', '<?php echo $cs_row_count; ?>');" />
		</td>
	</tr>
<?php	
}
/*End Custom Charge Rows*/

/*Remake Procedure Row*/
if($sel_ord_row_ins['re_make_id'] && $sel_ord_row_ins['re_make_id']!="0"): 
	$remake_data_qry = imw_query("SELECT * FROM `in_order_remake_details` WHERE `order_id`='".$order_id."'");
	if($remake_data_qry && imw_num_rows($remake_data_qry)>0):
		$remake_data = imw_fetch_assoc($remake_data_qry); ?>
		<tr id="remake_row">
			<td></td>
			<td colspan="2">
				<input type="text" name="remake_label" id="remake_label" value="Remake Charges" style="width:100%;text-align:center;" readonly />
				<input type="hidden" name="remake_id" id="remake_id" value="<?php echo $remake_data['id']; ?>" />
			</td>
			<td>
				<input style="width:100%;" type="text" class="pracodefield" name="remake_prac_code" id="remake_prac_code" value="<?php echo $proc_code_arr[$remake_data['prac_code_id']]; ?>" title="<?php echo $proc_code_desc_arr[$remake_data['prac_code_id']]; ?>" readonly />
			</td>
			<td>
				<input style="width: 84px; text-align: right;" type="text" name="remake_price" id="remake_price" value="<?php echo $remake_data['price']; ?>" class="price_cls currency" onchange="calculate_all();" autocomplete="off" />
			</td>
			<td>
<!-- onchange="changeQty('1', this.value, '1');"  -->
				<input type="text" style="width:100%; text-align:right;" class="qty_cls" name="remake_qty" id="remake_qty" value="<?php echo $remake_data['qty']; ?>" autocomplete="off"  onChange="calculate_all();" onKeyUp="validate_qty(this);"/>
				<input type="hidden" class="rqty_cls" name="remake_qty_right" value="0" />
			</td>
			<td>
				<input style="width: 97px; text-align: right;" type="text" name="remake_allowed" id="remake_allowed" value="<?php echo $remake_data['allowed']; ?>" class="allowed_cls currency" onchange="calculate_all();" autocomplete="off">
			</td>
			
			<td style="display:none">
				<input readonly style="width: 78px; text-align: right;" type="text" name="remake_total" id="remake_total" value="<?php echo $remake_data['total_amount']; ?>" class="price_total currency" onchange="calculate_all();" />
				<!-- tax calculations -->
					<input type="hidden" name="remake_tax_p" id="remake_tax_p" class="tax_p" value="<?php echo ($remake_data['tax_rate']!="")?$remake_data['tax_rate']:$taxRate[8]; ?>">
					<input type="hidden" name="remake_tax_v" id="remake_tax_v" class="tax_v" value="<?php echo ($remake_data['tax_paid']!="")?$remake_data['tax_paid']:"0.00"; ?>">
				<!-- End tax calculations -->
			</td>
			<td>
				<input style="width: 84px; text-align: right;" type="text" name="remake_ins_amount" id="remake_ins_amount" value="<?php echo $remake_data['ins_amount']; ?>" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency" />
			</td>
            <td>
				<!-- Line item's share in overall discount -->
				<input type="hidden" name="remake_overall_discount" id="remake_overall_discount" value="<?php echo ($remake_data['overall_discount']=="")?0:$remake_data['overall_discount']; ?>" class="item_overall_disc" />
								
				<input type="hidden" name="remake_discount" id="remake_discount" value="<?php echo ($remake_data['discount']=="")?0:$remake_data['discount']; ?>" onchange="calculate_all();" class="price_disc_per_proc" />
				<input style="width: 84px; text-align: right;" type="text" name="remake_discount_read" id="remake_discount_read" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />
			</td>
			<td>
				<input style="width: 84px; text-align: right;" type="text" name="remake_pt_paid" id="remake_pt_paid" value="<?php echo $remake_data['pr_paid']; ?>" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency">
			</td>
			<td>
				<input style="width: 84px; text-align: right;" type="text" name="remake_pt_resp" id="remake_pt_resp" value="<?php echo $remake_data['pt_resp']; ?>" class="resp_cls currency" readonly />
			</td>
			<td>
				<select name="remake_discount_code" id="remake_discount_code" class="text_10 disc_code dis_code_class" style="width:100%;">
				<option value="0">Please Select</option>
			<?php
					$sel_rec=imw_query("select d_id,d_code,d_default from discount_code");
					while($sel_write=imw_fetch_array($sel_rec)){
			?>
				<option value="<?php echo $sel_write['d_id'];?>" <?php if($sel_write['d_id']==$remake_data['discount_code']){ echo "selected";} ?>><?php echo $sel_write['d_code'];?></option>
			  <?php } ?>
				</select>
			</td>
			<td>
				<select name="remake_ins_case" id="remake_ins_case" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp('remake_ins');">
					<option value="0">Self Pay</option>
			<?php foreach($ins_case_arr as $key => $insCoName){ ?>
					<option value="<?php echo $key ; ?>" <?php if($key==$remake_data['ins_case_id']){ echo 'selected="selected"'; } ?>><?php echo $insCoName; ?></option>
			<?php } ?>
				</select>
			</td>
			<!--<td>
				<input type="checkbox" class="tax_applied" name="remake_tax_applied" id="remake_tax_applied" value="1" <?php /*echo ($remake_data['tax_applied']=="1")?'checked="checked"':"";*/ ?> />
			</td>-->
			<td></td>
		</tr>
<?php
	endif;
endif;
/*End Remake Procedure Row*/
 ?>
    	<tr>
<?php
	if($pageName=="frameLensSelection")
		$colspan = 4;
	else
		$colspan = 3;
?>
            <td align="right" colspan="<?php echo $colspan; ?>" style="font-weight:bold;"><label>Sub Total:</label></td>
            <td><input class="currency" readonly style="width:100%;" type="text" name="pos_pat_pos_grand_price" id="pos_pat_pos_grand_price" value="" /></td>
			<td><input readonly style="width:100%;text-align:right;" type="text" name="pos_pat_pos_grand_qty" id="pos_pat_pos_grand_qty" value="" /></td>
            <td><input class="currency" readonly style="width:100%;" type="text" name="pos_pat_pos_grand_allowed" id="pos_pat_pos_grand_allowed" value="" /></td>
            <td style="display:none"><input class="currency" readonly style="width:100%;" type="text" name="pos_pat_pos_grand_total" id="pos_pat_pos_grand_total" value="" /></td>
			<td><input class="currency" readonly style="width:100%;" type="text" name="pos_pat_pos_grand_ins_amt" id="pos_pat_pos_grand_ins_amt" value="" /></td>
            <td><input class="currency" readonly style="width:100%;" type="text" name="pos_pat_pos_grand_disc" id="pos_pat_pos_grand_disc" value="" /></td>
            <td><input class="currency" readonly style="width:100%;" type="text" name="pos_pat_pos_grand_payed" id="pos_pat_pos_grand_payed" value="" /></td>
            <td><input class="currency" readonly style="width:100%;" type="text" name="pos_pat_pos_grand_resp" id="pos_pat_pos_grand_resp" value="" /></td>
            <td></td><td></td><td></td>
            <td style="text-align: center;">
            	<img src="<?php echo $GLOBALS['WEB_PATH'];?>/images/addrow.png" alt="Add POS Row" class="addChargeRow" title="Add additional charge" onClick="add_custom_charge_row();" />
            </td>
		</tr>
        <?php if($sel_ord_row_ins['overall_discount_prac_code']!=""){$overall_discount_prac_code=$sel_ord_row_ins['overall_discount_prac_code'];}?>
       <tr>
<?php
	if($pageName=="frameLensSelection"){
		$colspan = 3;
		$colspan1 = 3;
	}
	elseif($pageName=="contactLensSelection"){
		$colspan = 2;
		$colspan1 = 3;
	}
	else{
		$colspan = 3;
		$colspan1 = 2;
	}

/*Payment Details from iDoc*/
$idoc_payment = 0;
$idoc_adjustment = 0;
$idoc_credit = 0;
$idoc_pat_due = 0;
if(isset($idoc_enc_id) && $idoc_enc_id>0){
	$sql_idoc_detail = "SELECT SUM(`pcd`.`paidForProc`) AS 'payment' FROM `patient_chargesheet_payment_info` `pci` INNER JOIN `patient_charges_detail_payment_info` `pcd` ON(`pci`.`payment_id`=`pcd`.`payment_id`) WHERE `pci`.`encounter_id`='".$idoc_enc_id."' AND `pci`.`markPaymentDelete`=0 AND `pcd`.`deletePayment`=0";
	$resp_idoc = imw_query($sql_idoc_detail);
	if($resp_idoc && imw_num_rows($resp_idoc)>0){
		$resp_idoc = imw_fetch_assoc($resp_idoc);
		$idoc_payment = ($resp_idoc['payment'])?$resp_idoc['payment']:0;
	}
	
	$sql_idoc_detail = "SELECT SUM(`write_off_amount`) AS 'payment' FROM `paymentswriteoff` WHERE `encounter_id`='".$idoc_enc_id."' AND `delStatus`=0";
	$resp_idoc = imw_query($sql_idoc_detail);
	if($resp_idoc && imw_num_rows($resp_idoc)>0){
		$resp_idoc = imw_fetch_assoc($resp_idoc);
		$idoc_adjustment = ($resp_idoc['payment'])?$resp_idoc['payment']:0;
	}
	
	$sql_idoc_detail = "SELECT SUM(`amountApplied`) AS 'payment' FROM `creditapplied` WHERE `crAppliedToEncId_adjust`='".$idoc_enc_id."' AND `delete_credit`=0";
	$resp_idoc = imw_query($sql_idoc_detail);
	if($resp_idoc && imw_num_rows($resp_idoc)>0){
		$resp_idoc = imw_fetch_assoc($resp_idoc);
		$idoc_credit = ($resp_idoc['payment'])?$resp_idoc['payment']:0;
	}
	
	$sql_idoc_detail = "SELECT patientDue FROM `patient_charge_list` WHERE `encounter_id`='".$idoc_enc_id."' AND `del_status`='0'";
	$resp_idoc = imw_query($sql_idoc_detail);
	if($resp_idoc && imw_num_rows($resp_idoc)>0){
		$resp_idoc = imw_fetch_assoc($resp_idoc);
		$idoc_pat_due = ($resp_idoc['patientDue'])?$resp_idoc['patientDue']:0;
	}
}
/*End Payment Details from iDoc*/
?>
			<td style="font-weight: bold;" colspan="<?php echo $colspan1; ?>">
				<label style="width: 84px; text-align: right; display: inline-block; margin-right: 4px;">Payment: </label><?php echo currency_symbol().number_format((float)$idoc_payment, 2, '.', ''); ?>
			</td>
			<td style="text-align:right;font-weight:bold;" colspan="<?php echo $colspan; ?>">
				<input type="hidden" name="tax_prac_code" id="tax_prac_code" value="<?php echo ($sel_ord_row_ins['tax_prac_code']!="")?$sel_ord_row_ins['tax_prac_code']:$taxPrac; ?>" />
				<input type="hidden" name="tax_chld" id="tax_chld" value="<?php echo $sel_ord_row_ins['tax_chld']; ?>" />
				<label style="float:right;font-weight:bold;">Total <?php echo $taxLabel; ?> Payable: </label></td>
			<td>
				<input class="currency" type="text" name="tax_payable" id="tax_payable" value="<?php echo ($sel_ord_row_ins['tax_payable'])?$sel_ord_row_ins['tax_payable']:'0.00'; ?>" style="width:100%;text-align:right;" onChange="convert_float(this);cal_overall_discount(true);" />
				<input type="hidden" id="tax_custom" name="tax_custom" value="<?php echo $sel_ord_row_ins['tax_custom']; ?>" />
			</td>
			<td></td>
			<td></td>
			<td>
				<input class="currency" type="text" name="tax_pt_paid" id="tax_pt_paid" value="<?php echo ($sel_ord_row_ins['tax_pt_paid'])?$sel_ord_row_ins['tax_pt_paid']:"0.00"; ?>" style="width:100%;text-align:right;" onChange="convert_float(this);cal_overall_discount(true);" />
			</td>
			<td>
				<input type="text" class="currency" name="tax_pt_resp" id="tax_pt_resp" readonly value="<?php echo ($sel_ord_row_ins['tax_pt_resp'])?$sel_ord_row_ins['tax_pt_resp']:"0.00"; ?>" style="width:100%;text-align:right;" />
			</td>
		</tr>
		
		<tr>
			<td style="font-weight: bold;" colspan="<?php echo $colspan1; ?>">
				<label style="width: 84px; text-align: right; display: inline-block; margin-right: 4px;">Adjustment: </label><?php echo currency_symbol().number_format((float)$idoc_adjustment, 2, '.', ''); ?>
			</td>
			<td style="text-align:right;font-weight:bold;" colspan="<?php echo $colspan; ?>">
				<label for="overall_discount">Overall Disc:</label>
			</td>
			<td>
				<input type="text" name="overall_discount" id="overall_discount" value="<?php echo ($sel_ord_row_ins['overall_discount']!="")?$sel_ord_row_ins['overall_discount']:"0"; ?>" style="width:95px;text-align:right;" onChange="cal_overall_discount('chnageVal');" autocomplete="off" />
			</td>
			<td align="right" style="font-weight:bold;"></td>
			<td colspan="3"></td>
			<td>
				<select name="overall_discount_code" id="overall_discount_code" class="text_10 disc_code dis_code_class" style="width:100%">
					<option value="0">Please Select</option>
<?php
				$sel_rec=imw_query("select d_id,d_code,d_default from discount_code order by d_code");
				while($sel_write=imw_fetch_array($sel_rec)){
					echo '<option value="'.$sel_write['d_id'].'" '.(($sel_write['d_id']==$sel_ord_row_ins['overall_discount_code'])?'selected':'').'>'.$sel_write['d_code'].'</option>';
				}
?>
				</select>
			</td>
		</tr>
		
		<tr>
			<td style="font-weight: bold;" colspan="<?php echo $colspan1; ?>">
				<label style="width: 84px; text-align: right; display: inline-block; margin-right: 4px;">Credit: </label><?php echo currency_symbol().number_format((float)$idoc_credit, 2, '.', ''); ?>
		</td>
			<td colspan="<?php echo $colspan; ?>" style="text-align:right;font-weight:bold;"><label>Grand Total: </label></td>
			<td>
				<input class="currency" readonly type="text" name="grand_total" id="grand_total" value="<?php echo $sel_ord_row_ins['grand_total']; ?>" style="width:100%;text-align:right;" />
			</td>
			<td>
				<input class="currency" readonly type="text" name="grand_ins_amt" id="grand_ins_amt" value="" style="width:100%;text-align:right;" />
			</td><td><input class="currency" type="text" readonly name="total_overall_discount" id="total_overall_discount" value="<?php echo $sel_ord_row_ins['total_overall_discount']; ?>" style="width:100%;text-align:right;">
				<input type="hidden" name="overall_discount_prac_code" id="overall_discount_prac_code" value="<?php echo $overall_discount_prac_code; ?>" />
                <input type="hidden" name="overall_discount_chld" id="overall_discount_chld" value="<?php echo $sel_ord_row_ins['overall_discount_chld']; ?>" /></td>
			<td>
				<input class="currency" readonly type="text" name="grand_pt_paid" id="grand_pt_paid" value="" style="width:100%;text-align:right;" />
			</td>
			<td>
				<input class="currency" readonly type="text" name="grand_pt_resp" id="grand_pt_resp" value="" style="width:100%;text-align:right;" />
			</td>
            <td>&nbsp;</td>
		</tr>
        <tr>
			<td style="font-weight: bold; vertical-align:top;" colspan="<?php echo $colspan1; ?>">
				<label style="width: 84px; text-align: right; display: inline-block; margin-right: 4px;">Pt Balance: </label><?php echo currency_symbol().number_format((float)$idoc_pat_due, 2, '.', ''); ?>
			</td>
<?php
	if($pageName=="frameLensSelection")
		$colspan = 3;
	elseif($pageName=="contactLensSelection")
		$colspan = 2;
	else
		$colspan = 2;
?>
            <td align="right" colspan="<?php echo $colspan; ?>" style="font-weight:bold;"><label for="charge_comment_1">Comment:</label></td>
            <td colspan="5">
            	<textarea name="charge_comment_1" id="charge_comment_1" style="width:99%;resize:none; height:40px;border:1px solid #ccc; font-family:'Gill Sans', 'Gill Sans MT', 'Myriad Pro', 'DejaVu Sans Condensed', Helvetica, Arial, sans-serif"><?php echo $sel_ord_row_ins['comment']; ?></textarea>
			</td>
			<td colspan="0" style="text-align: right"><strong>Due Date: </strong></td>
			<td colspan="3" style="text-align: left">
			<?php
			  if($sel_ord_row_ins['due_date']!='' && $sel_ord_row_ins['due_date']!='0000-00-00'){
		  		list($due_y,$due_m,$due_d)=explode('-',$sel_ord_row_ins['due_date']);
			  	$due_date="$due_m-$due_d-$due_y";
				}else{$due_date='';}
			?>
    	  	<input type="text"  class="date-pick" name="due_date" id="due_date" style="height: 21px; background-size:17px 21px;width: 95px;" value="<?php echo $due_date; ?>" autocomplete="off" />
			</td>
            </tr>
    	<tr>
			<td colspan="<?php echo $colspan+1; ?>">
				<table><tr>
					<td style="font-weight:bold;text-align:right; width: 105px"><label style="float:right;">Total Payment:</label></td>
            <td style="width: auto">
            	<input class="currency" type="text" name="show_pos_pat_pos_grand_payed" id="show_pos_pat_pos_grand_payed" value="<?php echo $sel_ord_row_ins['pt_paid']; ?>" style="width:100%;text-align:right;" onchange="dev_pay_in_prac();">
            </td>
					</tr></table>
            </td>
            <td colspan="2" style="text-align:right;font-weight:bold;"><label for="paymentMode">Method:</label></td>
            <td colspan="2">
                <select name="paymentMode" id="paymentMode" class="input_text_10" style="width:100%;" onChange="return changeMode();">
                    <option value="Cash" <?php if($sel_ord_row_ins['payment_mode']=="Cash") echo 'SELECTED'; ?>>Cash</option>
                    <option value="Check" <?php if($sel_ord_row_ins['payment_mode']=="Check") echo 'SELECTED'; ?>>Check</option>
                    <option value="Credit Card" <?php if($sel_ord_row_ins['payment_mode']=="Credit Card") echo 'SELECTED'; ?>>Credit Card</option>
                    <option value="EFT" <?php if($sel_ord_row_ins['payment_mode']=="EFT") echo 'SELECTED'; ?>>EFT</option>
                    <option value="Money Order" <?php if($sel_ord_row_ins['payment_mode']=="Money Order") echo 'SELECTED'; ?>>Money Order</option>
                </select>
            </td>
            <td id="checkTd" colspan="5" style="font-weight:bold;border:none;text-align:left;display:<?php if($sel_ord_row_ins['payment_mode']=="Check" || $sel_ord_row_ins['payment_mode']=="EFT" || $sel_ord_row_ins['payment_mode']=="Money Order"){ echo ''; }else{ echo 'none'; } ?>;">
            	 <table class="table_collapse_autoW">
                    <tr>
                        <td style="text-align:left; border:none;font-weight:bold; padding-left:20px;">Check&nbsp;#:</td>
                		 <td style="text-align:left;border:none;">
                         	<input name="checkNo" id="checkNo" type="text" class="input_text_10" value="<?php echo $sel_ord_row_ins['checkNo']; ?>" style="width:95px;"/>
                     	</td>
                    </tr>
                </table>    
            </td>

            <td id="ccTd" colspan="5" style="padding-left:20px;text-align:left;border:none;display:<?php if($sel_ord_row_ins['payment_mode']=="Credit Card"){ echo ''; }else{ echo 'none'; } ?>;">
                <table class="table_collapse_autoW">
                    <tr>
                        <td style="text-align:left;border:none;font-weight:bold; padding-left:10px;">Type:</td>
                        <td style="text-align:left;border:none;" id="creditCardCoTd">
                            <select name="creditCardCo" id="creditCardCo" style="width:80px;">
                                <option value=""></option>
                                <option value="AX" <?php if($sel_ord_row_ins['creditCardCo'] == "AX") echo 'SELECTED'; ?>>American Express</option>
								<option value="Care Credit" <?php if($sel_ord_row_ins['creditCardCo'] == "Care Credit") echo 'SELECTED'; ?>>Care Credit</option>
                                <option value="Dis" <?php if($sel_ord_row_ins['creditCardCo'] == "Dis") echo 'SELECTED'; ?>>Discover</option>
                                <option value="MC" <?php if($sel_ord_row_ins['creditCardCo'] == "MC") echo 'SELECTED'; ?>>Master Card</option>
                                <option value="Visa" <?php if($sel_ord_row_ins['creditCardCo'] == "Visa") echo 'SELECTED'; ?>>Visa</option>
                            </select>
                        </td>
                        <td style="text-align:left; border:none;font-weight:bold;">CC&nbsp;#:</td>
                        <td style="text-align:left; border:none;"><input name="cCNo" id="cCNo" type="text" style="width:75px;" value="<?php echo $sel_ord_row_ins['creditCardNo']; ?>" /></td>
                        <td style="text-align:left;border:none;font-weight:bold; padding-left:10px;">Exp.&nbsp;Date:</td>
                        <td style="text-align:left;border:none;">
                            <input type="text" name="expireDate" id="expireDate" value="<?php echo $sel_ord_row_ins['expirationDate']; ?>" size='4' maxlength="10" />
                        </td>
                    </tr>
                </table>
            </td> 
        </tr>
        </tbody>
    </table>
  </div>
</div>

<?php
/*Patch for fixing wrong value in order total*/
$itemTotal = array_sum($itemTotal);
$overall_discountV = trim($sel_ord_row_ins['overall_discount']);
$overall_discount = explode("%", trim($sel_ord_row_ins['overall_discount']));
if(count($overall_discount)>1){
	$total_overall_discount = trim(($itemTotal*$overall_discount[0])/100);
}
else{
	$total_overall_discount = trim($overall_discount[0]);
}
$total_overall_discount = ($total_overall_discount=="")?0:$total_overall_discount;
$grossTotal = $itemTotal-$total_overall_discount;
//$tax_rate = ($sel_ord_row_ins['tax_rate']=="")?$taxRate[1]:$sel_ord_row_ins['tax_rate'];
$tax_rate = "10";
$tax_payable = ($grossTotal*$tax_rate)/100;
$grand_total = $grossTotal+$tax_payable;

$itemTotal = number_format((float)$itemTotal, 2, '.', '');
$total_overall_discount = number_format((float)$total_overall_discount, 2, '.', '');
$tax_payable = number_format((float)$tax_payable, 2, '.', '');
$grand_total = number_format((float)$grand_total, 2, '.', '');

/*$fixSql = "UPDATE `in_order` SET
			`total_price`='".$itemTotal."',
			`overall_discount`='".$overall_discountV."',
			`total_overall_discount`='".$total_overall_discount."',
			`tax_payable`='".$tax_payable."',
			`grand_total`='".$grand_total."'
			WHERE `id`='".$order_id."'";*/
//imw_query($fixSql);
/*End Patch for fixing wrong value in order total*/
?>

<script type="text/javascript">
var pro_cnt = <?php echo $pro_cont; ?>;
var contactInserted = false;
var custom_tax_flag = '<?php echo $sel_ord_row_ins['tax_custom']; ?>';

/*function auto_select_ins(nVal){
	$(".ins_case_class").val(nVal);
}
*/
function auto_select_ins(sel_val){
	$(".ins_case_class").val(sel_val);
	var payed_clas=trans_amt=total_clas=0;
	$('.ins_case_class').each(function(index, element) {
		payed_clas = parseFloat($('.payed_cls').get(index).value);
		total_clas = parseFloat($('.price_total').get(index).value);
		trans_amt = total_clas - payed_clas;
		if($(this).val()=="" || $(this).val()=="0")
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


function auto_select_dis_code(nVal){
	$(".disc_code").val(nVal);
}

function switch_pat_ins_resp(pr_cont){
	
	var ins_id=tot_amt=pt_paid=transfer_amt=0;
	var disinfSuffix = '';	/*element Id Suffix*/
	pr_cont = $.trim(pr_cont);
	
	/*identifier deliminator*/
	var diIndex	= pr_cont.lastIndexOf('_');
	identifier	= pr_cont.substr(diIndex);
	if( identifier=='_di' ){
		disinfSuffix = 'di_';
		pr_cont = pr_cont.substr(0, diIndex);
	}
	
	ins_id = $('#'+disinfSuffix+'ins_case_id_'+pr_cont).val();
	tot_amt = $('#'+disinfSuffix+'pos_total_amount_'+pr_cont).val();
	pt_paid = $('#'+disinfSuffix+'pt_paid_'+pr_cont).val();
	transfer_amt = parseFloat(tot_amt)-parseFloat(pt_paid);
	
	if(ins_id=="0" || ins_id=="")
	{
		$('#'+disinfSuffix+'ins_amount_'+pr_cont).val("0.00");
		$('#'+disinfSuffix+'pt_resp_'+pr_cont).val(transfer_amt.toFixed(2));
	}
	else if(ins_id!="" && ins_id>0)
	{
		$('#'+disinfSuffix+'ins_amount_'+pr_cont).val(transfer_amt.toFixed(2));
	}
	calculate_all();
}

function calculate_all_Grand_POS(){
	
	grand_price = grand_disc = grand_total = grand_allowed = pt_paid_grand_diff = pt_resp_grand_diff = 0;
	grand_payed = grand_resp = grand_ins_amt = 0;
	
	var price_cls=allowed_cls=qty_cls=rqty_cls=payed_cls=ins_amt_cls=disc_val=tot_qty=last_index_calculated=0;
	$('.posTable tr:not(.hideRow) .price_cls').not('.tax').each(function(index, element){
			price_cls = parseFloat($('.posTable tr:not(.hideRow) .price_cls').not('.tax').get(index).value);
			itelQty = parseFloat($('.posTable tr:not(.hideRow) .qty_cls').not('.tax').get(index).value);
			
			allowed_cls = parseFloat(itelQty*price_cls);
			$('.posTable tr:not(.hideRow) .allowed_cls').not('.tax').get(index).value = allowed_cls.toFixed(2);
			
			//console.log(allowed_cls+" - "+price_cls+" - "+itelQty);
		try{
			qty_cls = $('.posTable tr:not(.hideRow) .qty_cls').get(index).value;
			rqty_cls = $('.posTable tr:not(.hideRow) .rqty_cls').get(index).value;
			
			payed_cls = parseFloat($('.posTable tr:not(.hideRow) .payed_cls').get(index).value);
			ins_amt_cls = parseFloat($('.posTable tr:not(.hideRow) .ins_amt_cls').get(index).value);
			tot_qty = parseInt(qty_cls)+parseInt(rqty_cls);
			if(isNaN(tot_qty)){
				tot_qty=0;
			}
		}
		catch(e){
			console.log("pt_pos 626: "+e.message);
		}
		if(isNaN(price_cls)){
			price_cls = 0;
			$('.posTable tr:not(.hideRow) .price_cls').get(index).value = price_cls.toFixed(2);
		}
		if(isNaN(allowed_cls)){
			allowed_cls = 0;
			$('.posTable tr:not(.hideRow) .allowed_cls').get(index).value = allowed_cls.toFixed(2);
		}
		if(isNaN(payed_cls)){
			payed_cls = 0;
			$('.posTable tr:not(.hideRow) .payed_cls').get(index).value = payed_cls.toFixed(2);
		}
		if(isNaN(ins_amt_cls)){
			ins_amt_cls = 0;
			$('tr:not(.hideRow) .ins_amt_cls').get(index).value = ins_amt_cls.toFixed(2);
		}
		
		/*Adjust discount collectively at total discount instead of each line item*/
		if(allowed_cls>0){
			//price_total = (allowed_cls-disc_val);
			price_total = allowed_cls;
		}else{
			//price_total = (price_cls-disc_val)*tot_qty;
			//price_total = (price_cls*tot_qty)-disc_val;
			price_total = price_cls*tot_qty;
		}
		resp_cls = price_total-payed_cls;		
		
	/*AutoSelect Ins*/
		var insPlanSelected = parseFloat($('.posTable tr:not(.hideRow) .ins_case_class').get(index).value);
		if( insPlanSelected!='' || insPlanSelected!='0' ){
			var ptCopay = parseFloat(top.main_iframe.ptVisionCopay.pt);
			if( !isNaN(ptCopay) && ptCopay!=0 && insPlanSelected == top.main_iframe.ptVisionPlanId ){
				
				var temp_rep  = allowed_cls;
				temp_rep = (temp_rep/100)*ptCopay;
				ins_amt_cls = allowed_cls - temp_rep;
				ins_amt_cls = (Math.round(100*ins_amt_cls)/100);
				$('.posTable tr:not(.hideRow) .ins_amt_cls').get(index).value = ins_amt_cls.toFixed(2);
			}
			else if(ins_amt_cls=='' || ins_amt_cls=='0'){
				ins_amt_cls = resp_cls;
				ins_amt_cls = (Math.round(100*ins_amt_cls)/100);
				$('.posTable tr:not(.hideRow) .ins_amt_cls').get(index).value = ins_amt_cls.toFixed(2);
			}	
		}
		
		resp_cls = price_total-ins_amt_cls-payed_cls;
	/*End AutoSelect Ins*/
		
		/*discount Calculation*/
		disc_val = $('.posTable tr:not(.hideRow) .price_disc_per_proc').get(index).value;
		disc_flag = false;
		if(disc_val.slice(-1)=='%'){
			disc_val = disc_val.replace('%','');
			if(disc_val>100){
				disc_val = 100;
				$('.posTable tr:not(.hideRow) .price_disc_per_proc').get(index).value = disc_val+"%";
			}
			//disc_val = allowed_cls * (parseFloat(disc_val)/100);
			disc_val = (price_total-ins_amt_cls) * (parseFloat(disc_val)/100);
			disc_flag = true;
		}else{
			/*disc_val = tot_qty * parseFloat(disc_val);*/
			disc_val = parseFloat(disc_val);
			
			/*Fix for Discont greater the Total Cost*/
			if(disc_val>price_total){
				disc_val = price_total;
				$('.posTable tr:not(.hideRow) .price_disc_per_proc').get(index).value=disc_val.toFixed(2);
			}
		}
		
		var temp_balance = price_total-disc_val;
		if(ins_amt_cls>temp_balance){
			ins_amt_cls = temp_balance;
			$('tr:not(.hideRow) .ins_amt_cls').get(index).value = ins_amt_cls.toFixed(2);
		}
		temp_balance = temp_balance-ins_amt_cls;
		
		if(payed_cls>temp_balance){
			payed_cls = temp_balance;
		}
		
		resp_cls = price_total-ins_amt_cls-payed_cls;
		resp_cls  = parseFloat(resp_cls.toFixed(2));
		//if(allowed_cls=="0" || allowed_cls=="0.00"){
		if(resp_cls=="0" || resp_cls=="0.00"){
			disc_val = parseFloat(0.00);
		}
		disc_val = parseFloat(disc_val);
		$('.posTable tr:not(.hideRow) .price_disc').get(index).value = disc_val.toFixed(2);
		if(disc_val.toFixed(2)==0 && !disc_flag){
			$('.posTable tr:not(.hideRow) .price_disc_per_proc').get(index).value = 0;
		}
		
		if(isNaN(disc_val)){
			disc_val = 0;
			$('.posTable tr:not(.hideRow) .price_disc').get(index).value = disc_val.toFixed(2);
		}
		/*End discount Calculation*/
		
		/*Pt paid Amt.*/
		if(isNaN(payed_cls)){
			payed_cls = 0;
			$('.posTable tr:not(.hideRow) .payed_cls').get(index).value = payed_cls.toFixed(2);
		}
		else{
			$('.posTable tr:not(.hideRow) .payed_cls').get(index).value = payed_cls.toFixed(2);
			var pt_paid_diff=parseFloat(payed_cls)-parseFloat(payed_cls.toFixed(2));
			pt_paid_grand_diff+=pt_paid_diff;
		}
		if(!isNaN(price_total)){
			$('.posTable tr:not(.hideRow) .price_total').get(index).value = price_total.toFixed(2);
		}
		
		if(!isNaN(resp_cls)){
			if(resp_cls>0){
				resp_cls = resp_cls - disc_val;
				$('.posTable tr:not(.hideRow) .resp_cls').get(index).value = resp_cls.toFixed(2);
				var pt_resp_diff=parseFloat(resp_cls)-parseFloat(resp_cls.toFixed(2));
				pt_resp_grand_diff+=pt_resp_diff;
				
			}else{
				$('.posTable tr:not(.hideRow) .resp_cls').get(index).value = '0.00';
			}
		}
		
		grand_price = grand_price + price_cls;
		grand_allowed = grand_allowed + allowed_cls;
		grand_disc = grand_disc + disc_val;
		grand_total = parseFloat(grand_total) + parseFloat(price_total);
		grand_payed = grand_payed + payed_cls;	/*Payed*/
		grand_resp = grand_resp + resp_cls;
		grand_ins_amt = grand_ins_amt + ins_amt_cls;
		last_index_calculated=index;
	});
	if(last_index_calculated>0)
	{
		//pt paid
		if(pt_paid_grand_diff)
		{
			var ptPaidRecCal=$('.posTable tr:not(.hideRow) .payed_cls').get(last_index_calculated).value;
			var ptPaidDiff= parseFloat(pt_paid_grand_diff)+parseFloat(ptPaidRecCal);
			$('.posTable tr:not(.hideRow) .payed_cls').get(last_index_calculated).value=ptPaidDiff.toFixed(2);
		}
		//pt resp
		/*if(pt_resp_grand_diff>0)
		{
			var ptRespRecCal=$('.posTable tr:not(.hideRow) .resp_cls').get(last_index_calculated).value;
			var ptRespDiff= parseFloat(pt_resp_grand_diff+ptRespRecCal);
			console.log("pt diff merged "+pt_resp_grand_diff+" to sum "+ptRespRecCal+" total now "+ptRespDiff);
			$('.posTable tr:not(.hideRow) .resp_cls').get(last_index_calculated).value=ptRespDiff.toFixed(2);
		}*/
	}
	if(!isNaN(grand_price)){
		$('.posTable #pos_pat_pos_grand_price').val(grand_price.toFixed(2));
	}else{
		grand_price=0;
	}
	if(!isNaN(grand_allowed)){
		$('.posTable #pos_pat_pos_grand_allowed').val(grand_allowed.toFixed(2));
	}else{
		grand_allowed=0;
	}
	if(!isNaN(grand_payed)){
		$('.posTable #pos_pat_pos_grand_payed').val(grand_payed.toFixed(2));
		grand_payed1 = grand_payed+parseFloat($('#tax_pt_paid').val());
		$('.posTable #show_pos_pat_pos_grand_payed').val(grand_payed1.toFixed(2));
	}else{
		grand_payed=0;
	}
	if(!isNaN(grand_resp)){
		if(grand_resp<=0){
			grand_resp=0;
		}
		$('.posTable #pos_pat_pos_grand_resp').val(grand_resp.toFixed(2));
	}else{
		grand_resp=0;
	}
	if(!isNaN(grand_ins_amt)){
		$('.posTable #pos_pat_pos_grand_ins_amt').val(grand_ins_amt.toFixed(2));
	}else{
		grand_ins_amt=0;
	}
	if(!isNaN(grand_disc)){
		$('.posTable #pos_pat_pos_grand_disc').val(grand_disc.toFixed(2));
	}else{
		grand_disc=0;
	}
	$('.posTable #pos_pat_pos_grand_total').val(grand_total.toFixed(2));
	
	var qtys = $(".posTable>tbody>tr[id]:not(.hideRow)  .qty_cls");
	var grand_qty_count = 0;
	$(qtys).each(function(i, obj){
		var qtyElem = $(obj).val();
		grand_qty_count = grand_qty_count+parseFloat(qtyElem);
	});
	
	$("#pos_pat_pos_grand_qty").val(grand_qty_count);
	cal_overall_discount();
}

function addNewRow(moduleType, data, row_num, page_type, vision){
	
	/*Discount code for New Row to be added*/
	var mainDiscountCode = $("#main_discount_code_1").val();
	
	var disc="";
	var dis_till = "";
	var cur_date = "";
	var dis_date = "";
	<?php if(defined('TAX_CHECKBOX_CHECKED') && constant('TAX_CHECKBOX_CHECKED')=='FALSE'){ echo'var tax_applied = false;';}else{ echo'var tax_applied = true;';}?>
	
	page_type = page_type || 'other';
	
	vision_id = '';
	if(moduleType==3 && typeof(vision)!=='undefined'){
		//vision_id = vision+'_';
	}
	var elem = $(".posTable tbody>tr#"+vision_id+moduleType+"_"+row_num);
	
	
	if(elem.length>0){
		pro_cont = row_num;
		
		if($("tr#"+vision_id+moduleType+"_"+row_num+" #pos_upc_name_"+pro_cont).length==0){
			pro_cont++;
			if($("tr#"+vision_id+moduleType+"_"+row_num+" #pos_upc_name_"+pro_cont).length==0){
				pro_cont = pro_cont-2;
			}
		}
		
		/*Discount*/
		disc="";
		dis_till = (data.discount_till).split("-");
		cur_date = new Date();
		dis_date = new Date();
		dis_date.setFullYear(parseInt(dis_till[0]), parseInt(dis_till[1])-1, parseInt(dis_till[2]));
		if(cur_date>dis_date && data.discount_till!="0000-00-00"){
			disc=0;
		}
		else{
			disc=data.discount;
		}
		disc=(data.discount=="")?"0":disc;
		
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_upc_name_"+pro_cont).val(data.upc_code);
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_item_name_"+pro_cont).val(data.name);
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_item_id_"+pro_cont).val(data.id);
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_price_"+pro_cont).val(parseFloat(data.retail_price).toFixed(2));
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_allowed_"+pro_cont).val('0.00');
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_discount_"+pro_cont).val(disc);
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_read_discount_"+pro_cont).val((disc=="")?0.00:parseFloat(disc).toFixed(2));
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_total_amount_"+pro_cont).val('0.00');
		$("tr#"+vision_id+moduleType+"_"+row_num+" #ins_amount_"+pro_cont).val('0.00');
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pt_paid_"+pro_cont).val('0.00');
		$("tr#"+vision_id+moduleType+"_"+row_num+" #pt_resp_"+pro_cont).val('0.00');
		
		tax_p = parseFloat($("tr#"+vision_id+moduleType+"_"+row_num+" input.tax_p").val());
		if(tax_p>0)
			$("tr#"+vision_id+moduleType+"_"+row_num+" #tax_applied_"+pro_cont).attr('checked', true);
		
		/*Update Quantity For Frame*/
		if(moduleType==1){
			var frameQty = $("input#qty_"+pro_cont).val();
			$("tr#"+vision_id+moduleType+"_"+row_num+" #pos_qty_"+pro_cont).val(frameQty);
			
			/*Patient's Frame*/
			if($('#in_add_'+pro_cont).is(':checked'))
				$("tr#"+vision_id+moduleType+"_"+row_num+" #itemDescription_frame_"+pro_cont).val('Pt Own Frame');
			else
				$("tr#"+vision_id+moduleType+"_"+row_num+" #itemDescription_frame_"+pro_cont).val('');
		}
		/*End Update Quantity For Frame*/
		
		//$("tr#"+moduleType+"_"+row_num+" #discount_code_"+pro_cont).val('');
		$("tr#"+vision_id+moduleType+"_"+row_num+" #ins_case_id_"+pro_cont).val(top.main_iframe.ptVisionPlanId);
		if(moduleType!=2)
		{
			get_prac_code_text(data.item_prac_code,'pos_item_prac_code_'+pro_cont, 'frm_cont', moduleType);
			changeLensPosLabel();
			calculate_all_Grand_POS();
			
			/*update Qty for contact Lens*/
			if(moduleType==3){
				chk_dis_fun(pro_cont);
			}
			return;
		}
	}
	var pro_cont = row_num;
	var lensD_cont= "";
	if(moduleType==2){
		lensD_cont= "_lensD";
	}
	var details ="";
	if(moduleType!=2){
		
		details += '<tr id="'+vision_id+moduleType+"_"+pro_cont+'">';
		
		details +='<!--td-->';
		//details +='<input type="hidden" name="pos_dx_code_'+pro_cont+lensD_cont+'" value="" />';
		details +='<input type="hidden" name="pos_order_chld_id_'+pro_cont+lensD_cont+'" value="" />';

		// new code starts
		details +='<input type="hidden" name="qty_hidden_'+pro_cont+lensD_cont+'" id="qty_hidden_'+pro_cont+lensD_cont+'"  value="1">';
		details +='<input type="hidden" name="qty_reduced_'+pro_cont+lensD_cont+'" id="qty_reduced_'+pro_cont+lensD_cont+'"  value="0">';
		if(moduleType!=2 && moduleType!=1 && moduleType!=3)
		{
			details +='<input type="hidden" name="reduce_qty_'+pro_cont+lensD_cont+'" id="reduce_qty_'+pro_cont+lensD_cont+'"  value="0">';			
			details +='<input type="hidden" name="qty_'+pro_cont+lensD_cont+'" id="qty_'+pro_cont+lensD_cont+'" value="" />';
		}
		// new code ends here



		details +='<input type="hidden" name="pos_order_detail_id_'+pro_cont+lensD_cont+'" id="pos_order_detail_id_'+pro_cont+lensD_cont+'" value="" />';
		details +='<input type="hidden" name="pos_module_type_id_'+pro_cont+lensD_cont+'" value="'+moduleType+'" />';
		details +='<input type="hidden" name="pos_upc_id_'+pro_cont+lensD_cont+'" id="pos_upc_id_'+pro_cont+lensD_cont+'" value="">';
		details +='<input readonly style="width:100%;" type="hidden" name="pos_upc_name_'+pro_cont+lensD_cont+'" id="pos_upc_name_'+pro_cont+lensD_cont+'" value="'+data.upc_code+'"  onchange="javascript:upc(document.getElementById(\'upc_id_'+pro_cont+lensD_cont+'\'), \''+pro_cont+lensD_cont+'\');" /><!--/td-->';
		
		/*if(moduleType=='1'){
			details +='<td></td>';
		}
		else */if(moduleType=='3'){
			/*Vision Row For Contact Lens*/
			details +='<td>';
			if(typeof(pro_cont)==='string' && pro_cont.slice(-2)==="os"){
				details +='<span class="vis_type vision_os">OS</span>';
			}
			else{
				details +='<span class="vis_type vision_od">OD</span>';
			}
			details +='</td>';
			/*End vision Row For Contact Lens*/
		}else details +='<td></td>';
		
		details +='<td>';
		details +='<input type="radio" name="use_on_hand_chk_'+pro_cont+lensD_cont+'" value="1" id="use_on_hand_chk_'+pro_cont+lensD_cont+'" style="display: none;" checked="checked" >';
		details +='<input readonly style="width:100%;" type="text" class="itemname" name="pos_item_name_'+pro_cont+lensD_cont+'" id="pos_item_name_'+pro_cont+lensD_cont+'" value="'+data.name+'" onChange="javascript:upc(document.getElementById(\'upc_id_'+pro_cont+lensD_cont+'\'));" />';
		details +='<input type="hidden" name="pos_item_id_'+pro_cont+lensD_cont+'" id="pos_item_id_'+pro_cont+lensD_cont+'" value="'+data.id+'" />';
		details +='<input type="hidden" name="pos_item_id_'+pro_cont+lensD_cont+'" id="pos_item_id_'+pro_cont+lensD_cont+'" value="'+data.id+'" /></td>';
		details +='<input type="hidden" name="pos_qty_on_hand_'+pro_cont+lensD_cont+'" id="pos_qty_on_hand_'+pro_cont+lensD_cont+'" value="'+data.qty_on_hand+'" />';
		details +='<input type="hidden" name="pos_stock_'+pro_cont+lensD_cont+'" id="pos_stock_'+pro_cont+lensD_cont+'" value="'+data.stock+'" /></td>';
<?php if($pageName=="frameLensSelection"): ?>
		details +='<td>';
			details +='<input type="text" id="itemDescription_frame_'+pro_cont+lensD_cont+'" class="itemnameDisp" style="width:100%;" ';
			if($('#in_add_'+pro_cont).is(':checked'))
				details +='value="Pt Own Frame" ';
			details +='/>';
		details +='</td>';
<?php endif; ?>
	
		/*Entering Prac At bottom*/
		var readOnly= (moduleType==3)?'readonly ':'';
		details +='<td><input style="width:100%;" type="text" class="pracodefield" name="pos_item_prac_code_'+pro_cont+lensD_cont+'" id="pos_item_prac_code_'+pro_cont+lensD_cont+'" value="" title="" autocomplete="off" /></td>';
		
		details +='<td><input style="width:100%;text-align:right;" type="text" name="pos_price_'+pro_cont+lensD_cont+'" id="pos_price_'+pro_cont+lensD_cont+'" value="'+parseFloat(data.retail_price).toFixed(2)+'" class="price_cls currency" onChange="this.value=parseFloat(this.value).toFixed(2);$('+((data.module_type_id==3)?'\'#rtl_price_':'\'#price_')+pro_cont+'\').val(this.value)'+((data.module_type_id==3)?'.trigger(\'change\')':'')+';calculate_all();" /></td>';
		
		details +='<td><input type="text" style="width:100%; text-align:right;" class="qty_cls" id="pos_qty_'+pro_cont+lensD_cont+'" name="pos_qty_'+pro_cont+lensD_cont+'" value="1" onChange="changeQty(\''+moduleType+'\', this.value, \''+pro_cont+'\');" autocomplete="off" '+readOnly+' onKeyUp="validate_qty(this);" />';
		if(moduleType!=1 && moduleType!=7 && moduleType!=6 && moduleType!=5){
		details +='<input type="hidden" class="rqty_cls" id="pos_qty_right_'+pro_cont+lensD_cont+'" name="pos_qty_right_'+pro_cont+lensD_cont+'" value="0" /></td>';
		}
		else
		{
			details +='<input type="hidden" class="rqty_cls" id="pos_qty_right_'+pro_cont+lensD_cont+'" name="pos_qty_right_'+pro_cont+lensD_cont+'" value="0" /></td>';	
		}
		details +='<td><input style="width:100%;text-align:right;" type="text" name="pos_allowed_'+pro_cont+lensD_cont+'" id="pos_allowed_'+pro_cont+lensD_cont+'" value="'+parseFloat(data.retail_price).toFixed(2)+'" class="allowed_cls currency" onChange="calculate_all();" /></td>';
		
		
		/*Calculative Field*/
		details +='<td style="display:none"><input readonly style="width:100%;text-align:right;" type="text" name="total_amount_'+pro_cont+lensD_cont+'" id="pos_total_amount_'+pro_cont+lensD_cont+'" value="0.00" class="price_total currency"  onChange="calculate_all();" />';
			/*Tax Calculations*/
			tax_applied = (tax_applied && facTax[moduleType]>0);
			details +='<input type="hidden" name="tax_p_'+pro_cont+lensD_cont+'" id="tax_p_'+pro_cont+lensD_cont+'" class="tax_p" value="'+facTax[moduleType]+'" />';
			details +='<input type="hidden" name="tax_v_'+pro_cont+lensD_cont+'" id="tax_v_'+pro_cont+lensD_cont+'" class="tax_v" value="0.00" />';
			/*End Tax Calculations*/
		details +='</td>';
		
		details +='<td><input style="width:100%;text-align:right;" type="text" name="ins_amount_'+pro_cont+lensD_cont+'" id="ins_amount_'+pro_cont+lensD_cont+'" value="0.00"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency" /></td>';
		
		/*Discount*/
		disc="";
		dis_till = (data.discount_till).split("-");
		cur_date = new Date();
		dis_date = new Date();
		dis_date.setFullYear(parseInt(dis_till[0]), parseInt(dis_till[1])-1, parseInt(dis_till[2]));
		if(cur_date>dis_date && data.discount_till!="0000-00-00"){
			disc=0;
		}
		else{
			disc=data.discount;
		}
		
		details +='<td>';
			/*Line item's share in overall discount*/
			details +='<input type="hidden" name="item_overall_discount_'+pro_cont+lensD_cont+'" id="item_overall_discount_'+pro_cont+lensD_cont+'" value="0.00" class="item_overall_disc" />';
			
			details +='<input style="width:100%;text-align:right;" type="hidden" name="discount_'+pro_cont+lensD_cont+'" id="pos_discount_'+pro_cont+lensD_cont+'" value="'+((data=="")?0.00:disc)+'"  onChange="calculate_all();" class="price_disc_per_proc" />';
			details +='<input style="width:100%;text-align:right;" type="text" name="pos_read_discount_'+pro_cont+lensD_cont+'" id="pos_read_discount_'+pro_cont+lensD_cont+'" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />';
		details += '</td>';
		/*Discount End*/
		
		details +='<td><input style="width:100%;text-align:right;" type="text" name="pt_paid_'+pro_cont+lensD_cont+'" id="pt_paid_'+pro_cont+lensD_cont+'" value="0.00"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency" /></td>';
		
		details +='<td><input style="width:100%;text-align:right;" type="text" name="pt_resp_'+pro_cont+lensD_cont+'" id="pt_resp_'+pro_cont+lensD_cont+'" value="0.00"  class="resp_cls currency" readonly /></td>';
		
		details +='<td><select name="discount_code_'+pro_cont+lensD_cont+'" id="discount_code_'+pro_cont+lensD_cont+'" class="text_10 disc_code dis_code_class" style="width:100%;" onChange="discountChanged(this);"><option value="">Please Select</option>';
		var defDisc = "";
		$.each(discCodes, function(di, dval){
			defDisc = (di==mainDiscountCode)?'selected="selected"':"";
			details += '<option value="'+di+'" '+defDisc+'>'+dval+'</option>';
		});
		details +'</select></td>';
/*Insurance Cases*/
		details +='<td><select name="ins_case_id_'+pro_cont+lensD_cont+'" id="ins_case_id_'+pro_cont+lensD_cont+'" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp(\''+pro_cont+lensD_cont+'\');"><option value="0">Self Pay</option>';
		$.each(insCases, function(insVal, insKey){
			insSelected = (insKey==top.main_iframe.ptVisionPlanId)?' selected="selected"':'';
			details +='<option value="'+insKey+'"'+insSelected+'>'+insVal+'</option>';
		});
		details +='</select>';
		details +='</td>';
		details +='<td><input type="checkbox" class="tax_applied" name="tax_applied_'+pro_cont+lensD_cont+'" id="tax_applied_'+pro_cont+lensD_cont+'" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" /></td>';
		
		var delItemFunction = 'delPosRow(\''+moduleType+'\', \''+pro_cont+'\');';
		if( moduleType == 3 && vision == 'os' ){
			delItemFunction = 'delPosRow(\''+moduleType+'\', \''+(pro_cont.replace(/_os|_od$/, ''))+'\', \'\', \'_'+vision+'\');';
		}
		
		details += '<td><img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="'+delItemFunction+'" /></td>';
		details +='</tr>';
	}
	else
	{
		//details += '<tr id="'+moduleType+"_"+pro_cont+'"><td colspan="12"></td></tr>';
	}
	
	if(moduleType==2)
	{
		if(data.type_prac_code!=""){
		
		var show_itemized_name_arr = {type:'lens', material:'material', a_r:'a_r', other:'other'};
		var pos_qty_Rows = $('tr[id^="2_'+pro_cont+'"][id$="_'+vision+'"]');
		$(pos_qty_Rows).each(function(i, row){
			$(row).find('.del_status').val("1");
			$(row).addClass('hideRow');
		});
		
		
			var data_pos_val="";
			$.ajax({
				async: false,
				type: 'POST', 
				url: './ajax.php',
				data: 'type=get_lens_fields&module='+moduleType+'&item_id='+data.id,
				dataType:"json",
				success: function(data_pos){
					var counter = $('#lens_item_count_'+pro_cont+lensD_cont);
					var pro = (counter.length>0)?$(counter).val():0;
					var lens_quantity = $("#qty_"+pro_cont+"_lensD").val();
					
					$.each(data_pos, function( index, pos_value ){
						
						if(index=="pgx" || index=="uv400"){return true;} /*skip adding PGX row in POS as it is moved to drop-down*/
						pro++;
						
						if(index=="material"){
							pracCodes = (pos_value.prac_code).split(';');
							if(pracCodes[0]!=""){
								retailPrices = ((pos_value.retail).toString()).split(";");
								detailVals = ((pos_value.details).toString()).split(";");
								ids = ((pos_value.ids).toString()).split(";");
								var rowIdArray = [];
								lensItem = pro;
								$.each(pracCodes, function(key, pracCode){
									if(key>0){lensItem++;}
									retailPrice = (typeof(retailPrices[key])=="undefined")?parseFloat(retailPrices[0]).toFixed(2):parseFloat(retailPrices[key]).toFixed(2);
									detailVal = (typeof(detailVals[key])=="undefined")?detailVals[0]:detailVals[key];
									
									rowId = moduleType+'_'+pro_cont+'_'+index+'_'+ids[key]+'_display_'+vision;
									
									rowIdArray.push(rowId);
									itemName = index+"_"+ids[key];
									rowClass = 'class="multiVals"';
									
									/*Discount*/
									disc="";
									dis_till = (data.discount_till).split("-");
									cur_date = new Date();
									dis_date = new Date();
									dis_date.setFullYear(parseInt(dis_till[0]), parseInt(dis_till[1])-1, parseInt(dis_till[2]));
									if(cur_date>dis_date && data.discount_till!="0000-00-00"){
										disc=0;
									}
									else{
										disc=data.discount;
									}
									
									var row = $("#"+rowId);
									var rown = "";
									if(row.length>0){
										$("#"+rowId+" .pracodefield").val(pracCode);
										$("#"+rowId+" .pracodefield").attr("title",pracCode);
										$("#"+rowId+" .itemnameDisp").val(detailVal);
										
										$("#"+rowId+" .price_cls").val(retailPrice);
										$("#"+rowId+" .allowed_cls").val(retailPrice);
										
										$("#"+rowId+" .qty_cls").val($("#qty_"+pro_cont+"_lensD").val());
										
										$("#"+rowId+" .price_disc_per_proc").val(disc);
										$("#"+rowId+" .price_disc").val('0.00');
										
										$("#"+rowId).find(".del_status").val("0"); /*Unset Del Status*/
										
										tax_p = parseFloat($("#"+rowId+" input.tax_p").val());
										if(tax_p>0)
											$("#"+rowId).find(".tax_applied").attr('checked', true);
										$("#"+rowId).removeClass('hideRow');
									}
									else{
										
										rown +='<tr id="'+rowId+'" '+rowClass+'><!--td-->';
										
										rown +='<input type="hidden" name="pos_order_chld_id_'+pro_cont+'_lensD" value=""><input type="hidden" name="pos_order_detail_id_'+pro_cont+'_lensD" value="" id="pos_order_detail_id_'+pro_cont+'_lensD"><input type="hidden" name="lens_item_detail_id_'+pro_cont+'_'+lensItem+'_lensD" id="lens_item_detail_id_'+pro_cont+'_'+lensItem+'_lensD" value="'+lensItem+'">';
										rown +='<input type="hidden" name="lens_item_detail_name_'+pro_cont+'_'+lensItem+'_lensD" id="lens_item_detail_name_'+pro_cont+'_'+lensItem+'_lensD" value="'+itemName+'"><input type="hidden" name="lens_price_detail_id_'+pro_cont+'_'+lensItem+'_lensD" id="lens_price_detail_id_'+pro_cont+'_'+lensItem+'_lensD" value="">';
					
										rown +='<input type="hidden" name="pos_upc_id_1_lensD" id="pos_upc_id_1_lensD" value=""><!--/td-->';
										rown+='<td>';
											rown+='<span class="vis_type vision_'+vision+'">'+vision+'</span>';
											rown+='<input type="hidden" name="pos_lens_item_vision_'+pro_cont+'_'+lensItem+'_lensD" id="pos_lens_item_vision_'+pro_cont+'_'+lensItem+'_lensD" value="'+vision+'" class="row_vision_value">';
										rown+='</td>';
							
										rown +='<td><input readonly="" style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_'+pro_cont+'_'+lensItem+'_lensD" id="pos_lens_item_name_'+pro_cont+'_lensD" value="Material"></td>';
										
										rown +='<td><input readonly style="width:100%;" type="text" class="itemnameDisp" name="pos_lens_item_name_disp_'+pro_cont+'_'+lensItem+'_lensD" id="pos_lens_item_name_disp_'+pro_cont+'_'+lensItem+'_lensD" value="'+detailVal+'" /></td>';
										
										rown +='<td><input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_'+pro_cont+'_'+lensItem+'_lensD" id="item_prac_code_'+pro_cont+'_'+lensItem+'_lensD" value="'+pracCode+'" title="'+pracCode+'"></td>';
										/*onchange="show_price_from_praccode(this,\'price_'+pro_cont+'_'+lensItem+'_lensD\',\'pos\'); calculate_all();"*/
										
										rown +='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_price_'+pro_cont+'_'+lensItem+'_lensD" id="lens_item_price_'+pro_cont+'_'+lensItem+'_lensD" value="'+retailPrice+'" class="price_cls currency" onchange="calculate_all();"></td>';
					
					/*onChange="changeQty(\'2\', this.value, \''+pro_cont+'\');"*/
										rown += '<td><input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_'+pro_cont+'_'+lensItem+'_lensD" id="lens_qty_'+pro_cont+'_'+lensItem+'_lensD_lensD" value="'+$("#qty_"+pro_cont+"_lensD").val()+'" autocomplete="off"  onChange="calculate_all();" onKeyUp="validate_qty(this);" /><input type="hidden" class="rqty_cls" name="pos_qty_right_1_lensD" value="0"><input type="hidden" name="pos_module_type_id_1_lensD" value="2"></td>';
					
										rown +='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_'+pro_cont+'_'+lensItem+'_lensD" id="lens_item_allowed_'+pro_cont+'_'+lensItem+'_lensD" value="'+retailPrice+'" class="allowed_cls currency" onchange="calculate_all();"></td>';
										
										
										rown +='<td style="display:none"><input readonly="" style="width:100%; text-align:right;" type="text" name="lens_item_total_'+pro_cont+'_'+lensItem+'_lensD" id="pos_total_amount_'+pro_cont+'_'+lensItem+'_lensD" value="0.00" class="price_total currency" onchange="calculate_all();">';
											/*Tax Calculations*/
											tax_applied = (tax_applied && facTax[moduleType]>0);
											rown +='<input type="hidden" name="tax_p_'+pro_cont+'_'+lensItem+'_lensD" id="tax_p_'+pro_cont+'_'+lensItem+'_lensD" class="tax_p" value="'+facTax[moduleType]+'" />';
											rown +='<input type="hidden" name="tax_v_'+pro_cont+'_'+lensItem+'_lensD" id="tax_v_'+pro_cont+'_'+lensItem+'_lensD" class="tax_v" value="0.00" />';
											/*End Tax Calculations*/
										rown +='</td>';
										
										rown +='<td><input style="width:100%; text-align:right;" type="text" name="ins_amount_'+pro_cont+'_'+lensItem+'_lensD" id="ins_amount_'+pro_cont+'_'+lensItem+'_lensD" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"></td>';
										
										rown +='<td>';
											/*Line item's share in overall discount*/
											rown +='<input type="hidden" name="lens_item_overall_discount_'+pro_cont+'_'+lensItem+'_lensD" id="lens_item_overall_discount_'+pro_cont+'_'+lensItem+'_lensD" value="0.00" class="item_overall_disc" />';
											rown +='<input type="hidden" name="lens_item_discount_'+pro_cont+'_'+lensItem+'_lensD" id="lens_item_discount_'+pro_cont+'_'+lensItem+'_lensD" value="'+disc+'" onchange="calculate_all();" class="price_disc_per_proc">';
											rown +='<input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_'+pro_cont+'_'+lensItem+'_lensD" id="pos_read_discount_'+pro_cont+'_'+lensItem+'_lensD" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />';
										rown +='</td>';
										
										
										rown +='<td><input style="width:100%; text-align:right;" type="text" name="pt_paid_'+pro_cont+'_'+lensItem+'_lensD" id="pt_paid_'+pro_cont+'_'+lensItem+'_lensD" value="0.00" onchange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"></td>';
										rown +='<td><input style="width:100%; text-align:right;" type="text" name="pt_resp_'+pro_cont+'_'+lensItem+'_lensD" id="pt_resp_'+pro_cont+'_'+lensItem+'_lensD" value="0.00" class="resp_cls currency" readonly=""></td>';
										
										rown +='<td><select name="discount_code_'+pro_cont+'_'+lensItem+'_lensD" id="discount_code_1" class="text_10 disc_code dis_code_class" style="width:100%;"><option value="0">Please Select</option><?php $sel_rec=imw_query("select d_id,d_code,d_default from discount_code"); while($sel_write=imw_fetch_array($sel_rec)){ ?><option value="<?php echo $sel_write['d_id'];?>" ><?php echo $sel_write['d_code'];?></option><?php } ?></select></td>';
										
										rown +='<td><select name="ins_case_id_'+pro_cont+'_'+lensItem+'_lensD" id="ins_case_id_'+pro_cont+'_'+lensItem+'_lensD" class="ins_case_class" style="width:100%;" onchange="switch_pat_ins_resp(\''+pro_cont+'_'+lensItem+'_lensD\');"><option value="0">Self Pay</option>';
		$.each(insCases, function(insVal, insKey){
			insSelected = (insKey==top.main_iframe.ptVisionPlanId)?' selected="selected"':'';
			rown +='<option value="'+insKey+'"'+insSelected+'>'+insVal+'</option>';
		});
		rown +='</select><input type="hidden" name="del_status_'+pro_cont+'_'+lensItem+'_lensD" id="del_status_'+pro_cont+'_'+lensItem+'_lensD" value="0" class="del_status"></td>';
											
										/*rown +='<input type="hidden" name="del_status_'+pro_cont+'_'+lensItem+'_lensD" id="del_status_'+pro_cont+'_'+lensItem+'_lens" class="del_status" value="0" />';*/
										
										rown +='<td><input type="checkbox" class="tax_applied" name="tax_applied_'+pro_cont+'_'+lensItem+'_lensD" id="tax_applied_'+pro_cont+'_'+lensItem+'_lensD" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" /></td>';
										
										rown += '<td><img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow(\'2\', \''+pro_cont+'\', \''+index+'_'+pracCode+'\', \''+vision+'\');" /></td>';
										
										rown +='</tr>';
										
										/*Add Row To POS*/
										details += rown;
									}
								});
								pro = lensItem;
								return true;
							}
						}
						else if(index=="a_r"){
							rtVal = lens_add_multi_pos(index, pos_value, data, pro, pro_cont, vision);
							rtVal = rtVal.split('~~~');
							pro = rtVal[0] - 1;
							pro_cont = rtVal[1];
							details += rtVal[2];
							return true;
						}
						
						var existingRow = $('#'+moduleType+'_'+pro_cont+'_'+index+'_display_'+vision);
						
						//console.log('#'+moduleType+'_'+pro_cont+'_'+index+'_display_'+vision);
						
						
						if(existingRow.length>0){
							
							var rowid_chk = '#'+moduleType+'_'+pro_cont+'_'+index+'_display_'+vision;
							
							var upcField = $(rowid_chk).find("#pos_upc_name_"+pro_cont+"_lensD");
							if(upcField.length>0){
								$(upcField[0]).val($("#upc_name_"+pro_cont+"_lensD").val());
							}
							
							/*Discount*/
							disc="";
							dis_till = (data.discount_till).split("-");
							cur_date = new Date();
							dis_date = new Date();
							dis_date.setFullYear(parseInt(dis_till[0]), parseInt(dis_till[1])-1, parseInt(dis_till[2]));
							if(cur_date>dis_date && data.discount_till!="0000-00-00"){
								disc=0;
							}
							else{
								disc=data.discount;
							}
							
							$(rowid_chk+" .pracodefield").val(pos_value.prac_code);
							$(rowid_chk+" .pracodefield").attr("title",pos_value.prac_code);
							
							retail_price_1 = parseFloat(pos_value.retail).toFixed(2);
							$(rowid_chk+" .price_cls").val(retail_price_1);
							$(rowid_chk+" .allowed_cls").val(parseFloat(pos_value.retail).toFixed(2));
							$(rowid_chk+" .price_disc_per_proc").val(disc);
							
							$(rowid_chk).find('.del_status').val("0");
							$(rowid_chk).find('.qty_cls').val(lens_quantity);
							
							tax_p = parseFloat($(rowid_chk+" input.tax_p").val());
							if(tax_p>0)
								$(rowid_chk).find(".tax_applied").attr('checked', true);
							
							/*if(index==="lens" && retail_price_1=="0.00"){
								$(rowid_chk+"").addClass('hideRow1');
							}
							else{*/
								$(rowid_chk).removeClass('hideRow');
								
								/*$("#"+moduleType+"_"+pro_cont+"_"+index+"_display").removeClass('hideRow1');
							}*/
							
							/*$("#"+rowId+" .qty_cls").val($("#qty_"+counter+"_lensD").val());
							$("#"+rowId+" .price_disc").val('0.00');*/
							pro--;
						}
						else{
							
							
							var rowid_chk = moduleType+'_'+pro_cont+'_'+index+'_display_'+vision;
							
							tr_class="";
							retail_price_1 = parseFloat(pos_value.retail).toFixed(2);
							/*if(index==="lens" && retail_price_1=="0.00"){
								tr_class = 'class="hideRow1"';
							}*/
							
							details+='<tr id="'+rowid_chk+'" '+tr_class+'>';
							
							/*Hidden Fields*/
							details+='<!--td-->';
								//details+='<input type="hidden" name="pos_dx_code_'+pro_cont+lensD_cont+'" value="" />';
								if(pro==1){
									details +='<input readonly style="width:100%;" type="hidden" name="pos_upc_name_'+pro_cont+lensD_cont+'" id="pos_upc_name_'+pro_cont+lensD_cont+'" value="'+data.upc_code+'"  onchange="javascript:upc(document.getElementById(\'upc_id_'+pro_cont+lensD_cont+'\'), \''+pro_cont+lensD_cont+'\');" />';
								}
								details+='<input type="hidden" name="pos_order_chld_id_'+pro_cont+lensD_cont+'" value="" />';
								details+='<input type="hidden" name="pos_order_detail_id_'+pro_cont+lensD_cont+'" id="pos_order_detail_id_'+pro_cont+lensD_cont+'" value="" />';
								details+='<input type="hidden" name="lens_item_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" value="'+pro+'" />';
								details+='<input type="hidden" name="lens_item_detail_name_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_detail_name_'+pro_cont+'_'+pro+lensD_cont+'" value="'+index+'" />';
								details+='<input type="hidden" name="lens_price_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_price_detail_id_'+pro_cont+'_'+pro+lensD_cont+'" value="" />';
								details+='<input type="hidden" name="pos_module_type_id_'+pro_cont+lensD_cont+'" value="'+moduleType+'" />';
								details+='<input type="hidden" name="pos_upc_id_'+pro_cont+lensD_cont+'" id="pos_upc_id_'+pro_cont+lensD_cont+'" value="">';
							details+='<!--/td-->';
							
							details+='<td>';
								details+='<span class="vis_type vision_'+vision+'">'+vision+'</span>';
								details+='<input type="hidden" name="pos_lens_item_vision_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_lens_item_vision_'+pro_cont+'_'+pro+lensD_cont+'" value="'+vision+'" class="row_vision_value">';
							details+='</td>';
							
							
							details+='<td><input readonly style="width:100%;" type="text" class="itemname" name="pos_lens_item_name_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_lens_item_name_'+pro_cont+lensD_cont+'" value="'+pos_value.name+'"/></td>';

<?php if($pageName=="frameLensSelection"): ?>
							details+='<td><input readonly style="width:100%;" type="text" class="itemnameDisp" name="pos_lens_item_name_disp_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_lens_item_name_disp_'+pro_cont+'_'+pro+lensD_cont+'" value="" /></td>';
<?php endif; ?>
							
							details+='<td><input style="width:100%;" type="text" class="pracodefield" name="item_prac_code_'+pro_cont+'_'+pro+lensD_cont+'" id="item_prac_code_'+pro_cont+'_'+pro+lensD_cont+'" value="'+pos_value.prac_code+'" title="" /></td>';
							/*onChange="show_price_from_praccode(this,\'price_'+pro_cont+'_'+pro+lensD_cont+'\',\'pos\'); calculate_all();"*/
							
							details+='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_price_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_price_'+pro_cont+'_'+pro+lensD_cont+'" value="'+retail_price_1+'" class="price_cls currency" onChange="calculate_all();"/></td>';

/* onChange="changeQty(\''+moduleType+'\', this.value, \''+pro_cont+'\');" */
							details+='<td><input type="text" style="width:100%; text-align:right;" class="qty_cls" name="lens_qty_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_qty_'+pro_cont+'_'+pro+lensD_cont+'_lensD" value="'+lens_quantity+'" onChange="calculate_all();" onKeyUp="validate_qty(this);" />';
							details+='<input type="hidden" class="rqty_cls" name="pos_qty_right_'+pro_cont+lensD_cont+'" value="0" /></td>';
							
							details+='<td><input style="width:100%; text-align:right;" type="text" name="lens_item_allowed_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_allowed_'+pro_cont+'_'+pro+lensD_cont+'" value="'+parseFloat(pos_value.retail).toFixed(2)+'" class="allowed_cls currency" onChange="calculate_all();"/></td>';
							
							details+='<td style="display:none"><input readonly style="width:100%; text-align:right;" type="text" name="lens_item_total_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_total_amount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="price_total currency"  onChange="calculate_all();"/>';
								/*Tax Calculations*/
								tax_applied = (tax_applied && facTax[moduleType]>0);
								details+='<input type="hidden" name="tax_p_'+pro_cont+'_'+pro+lensD_cont+'" id="tax_p_'+pro_cont+'_'+pro+lensD_cont+'" class="tax_p" value="'+facTax[moduleType]+'" />';
								details +='<input type="hidden" name="tax_v_'+pro_cont+'_'+pro+lensD_cont+'" id="tax_v_'+pro_cont+'_'+pro+lensD_cont+'" class="tax_v" value="0.00" />';
								/*End Tax Calculations*/
							details +='</td>';
							
							details+='<td><input style="width:100%; text-align:right;" type="text" name="ins_amount_'+pro_cont+'_'+pro+lensD_cont+'" id="ins_amount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency"/></td>';
						/*Discount*/
						disc="";
						dis_till = (data.discount_till).split("-");
						cur_date = new Date();
						dis_date = new Date();
						dis_date.setFullYear(parseInt(dis_till[0]), parseInt(dis_till[1])-1, parseInt(dis_till[2]));
						if(cur_date>dis_date && data.discount_till!="0000-00-00"){
							disc=0;
						}
						else{
							disc=data.discount;
						}
							
							details+='<td>';
								/*Line item's share in overall discount*/
								details +='<input type="hidden" name="lens_item_overall_discount_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_overall_discount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="item_overall_disc" />';
								details+='<input type="hidden" name="lens_item_discount_'+pro_cont+'_'+pro+lensD_cont+'" id="lens_item_discount_'+pro_cont+'_'+pro+lensD_cont+'" value="'+((disc=="")?0:disc)+'" onChange="calculate_all();" class="price_disc_per_proc"/>';
								details+='<input style="width:100%; text-align:right;" type="text" name="read_lens_item_discount_'+pro_cont+'_'+pro+lensD_cont+'" id="pos_read_discount_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />';
							details+'</td>';
					/*Discount End*/
							
							details+='<td><input style="width:100%; text-align:right;" type="text" name="pt_paid_'+pro_cont+'_'+pro+lensD_cont+'" id="pt_paid_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00"  onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency"/></td>';
							
							details+='<td><input style="width:100%; text-align:right;" type="text" name="pt_resp_'+pro_cont+'_'+pro+lensD_cont+'" id="pt_resp_'+pro_cont+'_'+pro+lensD_cont+'" value="0.00" class="resp_cls currency" readonly /></td>';
							
							details+='<td><select name="discount_code_'+pro_cont+'_'+pro+lensD_cont+'" id="discount_code_'+pro_cont+'" class="text_10 disc_code dis_code_class" style="width:100%;"><option value="">Please Select</option>';
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
							
							if(page_type=='other_selection' && pro==1)
							{
								details +='<input type="hidden" name="type_id_'+pro_cont+'" id="type_id_'+pro_cont+'" value="'+data.type_id+'" />';
								details +='<input type="hidden" name="progressive_id_'+pro_cont+'" id="progressive_id_'+pro_cont+'" value="'+data.progressive_id+'" />';
								details +='<input type="hidden" name="material_id_'+pro_cont+'" id="material_id_'+pro_cont+'" value="'+data.material_id+'" />';
								details +='<input type="hidden" name="transition_id_'+pro_cont+'" id="transition_id_'+pro_cont+'" value="'+data.transition_id+'" />';
								details +='<input type="hidden" name="a_r_id_'+pro_cont+'" id="a_r_id_'+pro_cont+'" value="'+data.a_r_id+'" />';
								details +='<input type="hidden" name="tint_id_'+pro_cont+'" id="tint_id_'+pro_cont+'" value="'+data.tint_id+'" />';
								details +='<input type="hidden" name="polarized_id_'+pro_cont+'" id="polarized_id_'+pro_cont+'" value="'+data.polarized_id+'" />';
								details +='<input type="hidden" name="edge_id_'+pro_cont+'" id="edge_id_'+pro_cont+'" value="'+data.edge_id+'" />';
								details +='<input type="hidden" name="color_id_'+pro_cont+'" id="color_id_'+pro_cont+'" value="'+data.color+'" />';
								details +='<input type="hidden" name="uv400_'+pro_cont+'" id="uv400_'+pro_cont+'" value="'+data.uv_check+'" />';
								details +='<input type="hidden" name="pgx_'+pro_cont+'" id="pgx_'+pro_cont+'" value="'+data.pgx_check+'" />';
							}
							details+= '</td>';
							
							details +='<td><input type="checkbox" class="tax_applied" name="tax_applied_'+pro_cont+'_'+pro+lensD_cont+'" id="tax_applied_'+pro_cont+'_'+pro+lensD_cont+'" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" /></td>';
							
							details+= '<td><img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow(\''+moduleType+'\', \''+pro_cont+'\', \''+index+'\', \''+vision+'\');" /></td>';
							
							details+= '</tr>';
						}
					});
					if(elem.length==0){//if we are updating existing one then do not update counter
						$('#lens_item_count_'+pro_cont+lensD_cont).remove();
						details+='<input type="hidden" name="lens_item_count_'+pro_cont+lensD_cont+'" id="lens_item_count_'+pro_cont+lensD_cont+'" value="'+pro+'">';
					}
				}
			});
		}
	}
	
	if(moduleType==2 && pos_qty_Rows.length>0){
		
		var lastRow = $(pos_qty_Rows[pos_qty_Rows.length-1]);
		
		/*Group Rows on the basis of vision*/
		visRows = $("table.table_collapse.posTable > tbody > tr[id^='2_"+pro_cont+"'][id$='"+vision+"']");
		if( visRows.length > 0 ){
			lastRow = visRows[visRows.length-1];
		}
		
		/*OD at top always*/
		if( vision == 'od' && visRows.length == 0 ){
			lastRow = pos_qty_Rows[0];
			$(details).insertBefore($(lastRow));
		}
		else
			$(details).insertAfter($(lastRow));
	}
	else{
		if(moduleType==3){
			var checkRow = '';
			var rowType = '';
			
			if(typeof(pro_cont)=="string" && pro_cont.slice(-2)=="os"){
				checkRow = $('.posTable>tbody>tr#'+moduleType+'_'+pro_cont.replace('_os', ''));
				rowType = 'os';
			}
			else{
				checkRow = $('.posTable>tbody>tr#'+moduleType+'_'+pro_cont+'_os');
				rowType = 'od';
			}
			
			if(checkRow.length==1 && rowType=='os'){
				$(details).insertAfter(checkRow);
			}
			else if(checkRow.length==1 && rowType=='od'){
				$(details).insertBefore(checkRow);
			}
			else{
				$(".posTable>tbody").prepend(details);
			}
		}
		else{
			
			if( moduleType==2 ){
				
				var frameRow =  $("table.table_collapse.posTable > tbody > tr#1_"+pro_cont);
				var prevRowsCount = 0;
				var lastRow = '';
				if( vision == 'od' ){
					
					prevRowsCount = $("table.table_collapse.posTable > tbody > tr[id^='2_"+pro_cont+"'][id$='os']");
					
					if( prevRowsCount.length > 0 )
						$(details).insertBefore(prevRowsCount[0]);
					else if( frameRow.length > 0 )
						$(details).insertAfter(frameRow[frameRow.length-1]);
					else
						$(".posTable>tbody").prepend(details);
				}
				else if( vision == 'os' ){
					
					prevRowsCount = $("table.table_collapse.posTable > tbody > tr[id^='2_"+pro_cont+"'][id$='od']")
					if( prevRowsCount.length > 0 )
						$(details).insertAfter(prevRowsCount[prevRowsCount.length-1]);
					else if( frameRow.length > 0 )
						$(details).insertAfter(frameRow[frameRow.length-1]);
					else
						$(".posTable>tbody").prepend(details);
				}
			}
			else if( moduleType==1 ){
				/*Insert before Lens Row*/
				var lensRowsF = $(".posTable>tbody tr[id^='2_"+pro_cont+"']");
				if( lensRowsF.length > 0 ){
					$(details).insertBefore(lensRowsF[0]);
				}
				else
					$(".posTable>tbody").prepend(details);
			}
			else
				$(".posTable>tbody").prepend(details);
		}
	}
	
	if(moduleType==2){
		var lensRows = $(".posTable>tbody tr[id^='2_"+pro_cont+"']");
		$("#lens_item_count_"+pro_cont+lensD_cont).val(lensRows.length);
		$.each(lensRows, function(i, obj){
			k = i+1;
			$(obj).find("#lens_item_detail_id_"+pro_cont+"_"+k+"_lensD").val(k);
		});
		
		var posUpcCells = $('input[id="pos_upc_name_'+pro_cont+lensD_cont+'"]');
		if(posUpcCells.length>1){
			delete posUpcCells[0];
			$(posUpcCells).each(function(i, obj){
				$(obj).remove();
			});
		}
	}
	
	if(moduleType==3)
	{
		var q_left="";
		var q_rth="";
		var allo="";
		var tot="";
		var cont_qty_left=""
		var cont_qty_right="";
		var pric="";
		
		/*CL Fields*/
		var pro_cont1 = pro_cont;
		if(typeof(pro_cont)==='string' && pro_cont!='')
			pro_cont1 = pro_cont.replace(/_os/, '');
		
		cont_qty_left=document.getElementById('qty_'+pro_cont1).value;
		cont_qty_right=document.getElementById('qty_right_'+pro_cont1).value;
		pric=document.getElementById('pos_price_'+pro_cont).value;
		
		/*CL POS*/
		document.getElementById('pos_qty_'+pro_cont).value = (typeof(vision)=='undefined')?parseInt(cont_qty_right):parseInt(cont_qty_left);
		document.getElementById('pos_qty_right_'+pro_cont).value = cont_qty_right;
		
		tot=(pric*(parseInt(cont_qty_left)+parseInt(cont_qty_right)));
		document.getElementById('pos_allowed_'+pro_cont).value=tot;
	}
	get_prac_code_text(data.item_prac_code,'pos_item_prac_code_'+pro_cont+lensD_cont, 'frm_cont', moduleType);
	//get_prac_code_text("0",'tax_item_prac_code_1', 'frm_cont', 8);
	if(moduleType==2){
		//get_prac_code_text(data.type_prac_code,'pos_item_prac_code_'+pro_cont+'_'+index, 'frm_cont', moduleType);
	}
	
	currencySymbols();
	changeLensPosLabel();
	prac_code_typeahead();
	calculate_all_Grand_POS();
}

function changeQty(moduleId, value, count){	
	moduleId = $.trim(moduleId);
	if(moduleId=="1"){
		$("#qty_"+$.trim(count)).val($.trim(value));
	}
	calculate_all();
}

function changeDiscount(obj){
	var nVal = $(obj).val();
	$("#overall_discount").val(0);
	$(obj).siblings("input.price_disc_per_proc").val(nVal);
	$(obj).siblings("input.price_disc_per_proc").trigger('change');
	cal_overall_discount(false);
}

function cal_overall_discount(mflag){
	
	var tax = parseFloat($('#tax_payable').val());
	tax = (isNaN(tax))?0.00:tax;
	
	mflag = ( typeof(mflag)=="undefined" && mflag != 'chnageVal' )?((custom_tax_flag=="0")?false:true):mflag;
	
	var overall_discount = $("#overall_discount").val();
	var tax_paid = parseFloat($("#tax_pt_paid").val());
	tax_paid = (isNaN(tax_paid))?0.00:tax_paid;
	
	var flag_Call = false;
	
	var pos_pat_pos_grand_total = $("#pos_pat_pos_grand_allowed").val();
	var pos_pat_pos_grand_ins_amt = $("#pos_pat_pos_grand_ins_amt").val();
	var disc_elem = Array();
	var totalTaxPayable = (mflag)?parseFloat($('#tax_payable').val()):0;
	
	var pos_rows = $("table.posTable>tbody>tr[id]:not(.hideRow)");
	
	
	if(overall_discount.slice(-1)=='%'){
		overall_discount = overall_discount.replace('%','');
		overall_discount = (pos_pat_pos_grand_total-pos_pat_pos_grand_ins_amt) * (parseFloat(overall_discount)/100);
	}
	
	
	pos_pat_pos_grand_total1 = pos_pat_pos_grand_total - pos_pat_pos_grand_ins_amt;
	if(parseFloat(overall_discount)>parseFloat(pos_pat_pos_grand_total1)){
		overall_discount = pos_pat_pos_grand_total1;
		$("#overall_discount").val(overall_discount.toFixed(2));
	}
	
	final_overall_discount = 0.00;
	
	if(!mflag || mflag == 'chnageVal'){
		if(overall_discount=="" || overall_discount=="0"){
			
			var amt = 0.00;
			var disc = 0.00;
			var cost = 0.00;
			var tax_p = 0;
			var tax_v = 0.00;
			var ins_paid=0.00;
			$.each(pos_rows, function(i, row){
				disc = parseFloat($(row).find('input.price_disc').val());	/*Discount appplied to line item*/
				$(row).find('input.item_overall_disc').val('0.00'); /*Unset Overall Discount for the Line Item*/
				cost = parseFloat($(row).find('input.allowed_cls').val());	/*Total Cost of line item before discount*/
				ins_paid=parseFloat($(row).find('input.ins_amt_cls').val());	/*Total paid by ins*/
				tax_p = parseFloat($(row).find('input.tax_p').val());		/*Tax %applied to line Item*/
				amt = cost-disc;	/*Actual cost of line Item after discount*/
				tax_v = (amt*tax_p)/100;
				//tax_v = tax_v.toFixed(2);	/*Tax payable for line Item*/// commented due to 27193
				if($(row).find('input.tax_applied').is(':checked')){
					totalTaxPayable += parseFloat(tax_v);		/*Calculae Total Tax Payable*/
				}
				
				$(row).find('input.tax_v').val(tax_v);
			});
		}
		else{
			var amt = 0.00;
			var disc = 0.00;
			var disc_p = 0.00;
			var cost = 0.00;
			var tax_p = 0.00;
			var tax_v = 0.00;
			var ins_amt = 0.00;
			
			pos_pat_pos_grand_total1 = parseFloat(pos_pat_pos_grand_total1);
			pos_pat_pos_grand_ins_amt1=parseFloat(pos_pat_pos_grand_ins_amt);
			overall_discount1 = parseFloat(overall_discount);
			$.each(pos_rows, function(i, row){
				
				/*Total Cost of line item before discount*/
				cost = parseFloat($(row).find('input.allowed_cls').val());
				
				/*Total payment by Insurance*/
				ins_amt = parseFloat($(row).find('input.ins_amt_cls').val());
				//overall_discount1 = overall_discount1 - ins_amt;
				
				/*Discount % for Line Item*/
				disc_p = ((cost-ins_amt)/pos_pat_pos_grand_total1)*100;
				disc_p = isNaN(disc_p)?0.00:disc_p;
				
				/*Discount appplied to line item*/
				disc = ((overall_discount1*disc_p)/100);
				disc = parseFloat(disc);
				
				/*Actual cost of line Item after discount*/
				print_val = ''; 
				if(disc>cost){
					disc = cost;
				}
				
				$(row).find('input.item_overall_disc').val(disc);
				
				/*Tax %applied to line Item*/
				tax_p = parseFloat($(row).find('input.tax_p').val());
				
				discount_1 = disc;
				item_paid = parseFloat($(row).find('input.payed_cls').val());
				item_pt_resp = parseFloat($(row).find('input.resp_cls').val());
				
				if(item_pt_resp > 0){
					
					if(item_pt_resp > discount_1){
						item_pt_resp = item_pt_resp - discount_1;
						discount_1 = 0.00;
					}
					else{
						discount_1 = discount_1 - item_pt_resp;
						item_pt_resp = 0.00;
					}
				}
				
				if(item_paid > 0 && discount_1 > 0){
					item_pt_resp = parseFloat($(row).find('input.resp_cls').val());
					if(item_paid > discount_1){
						item_paid = item_paid - discount_1;
						$(row).find('input.resp_cls').val((item_pt_resp + discount_1).toFixed(2));
						discount_1 = 0.00;
					}
					else{
						discount_1 = discount_1 - item_paid;
						item_paid = 0.00;
					}
					item_pt_resp = item_pt_resp + discount_1;
					$(row).find('input.payed_cls').val(item_paid.toFixed(2));
					flag_Call = true;
				}
				
				final_overall_discount = final_overall_discount+parseFloat(disc);
				
				amt = cost-disc;
				tax_v = (amt*tax_p)/100;
				
				//tax_v = tax_v.toFixed(2); // commented due to 27193
				/*Tax payable for line Item*/
				if($(row).find('input.tax_applied').is(':checked')){
					totalTaxPayable += parseFloat(tax_v);	/*Calculae Total Tax Payable*/
				}
				$(row).find('input.tax_v').val(tax_v);
			});
			
			disc_elem = $('.posTable tr[id]:not(.hideRow) input.price_disc_per_proc[value!="0"]');
			disc_elem_overall = $('.posTable tr[id]:not(.hideRow) input.item_overall_disc[value!="0"]');
			if(disc_elem.length>0 && flag_Call){
				$('.posTable tr[id]:not(.hideRow) input.price_disc_per_proc[value!="0"]').val(0);
				calculate_all();
				return;
			}
		}
		$('#tax_custom').val(0);
		custom_tax_flag = "0";
	}
	else{
		$('#tax_custom').val(1);
		custom_tax_flag = "0";
	}
	
	var grossTotal = grandTotal = 0;
	
	/*Adjust total disocunt*/
	//grossTotal = pos_pat_pos_grand_total - overall_discount;
	grossTotal = pos_pat_pos_grand_total;
	
	grossTotal = parseFloat(grossTotal);
	grandTotal = grossTotal + totalTaxPayable;
	grandTotal = parseFloat(grandTotal);
	
	if(isNaN(overall_discount) || overall_discount==""){
		overall_discount=0;
	}
	overall_discount = parseFloat(overall_discount);
	
	var overallInsResp = parseFloat($("#pos_pat_pos_grand_ins_amt").val());		/*Overall Insurance Resp.*/
	var overallPtpaid = parseFloat($("#pos_pat_pos_grand_payed").val());		/*Overall Pt. Paid.*/
	overallPtpaid = overallPtpaid+tax_paid;
	var grandPtResp = 0;
	
	if(overall_discount<=0){/*Overall Discount*/
		overall_discount = parseFloat($("#pos_pat_pos_grand_disc").val());
	}
	
	/* - overall_discount - Adjusting Discount collectively at the end instead of at each line item.*/
	grandPtResp = grandTotal - overallInsResp - overallPtpaid - overall_discount.toFixed(2);
	if(grandPtResp<0){grandPtResp=0;}
	
	if(final_overall_discount>0){
		overall_discount = final_overall_discount;
	}
	
	$('#total_overall_discount').val(overall_discount.toFixed(2));
	$("#tax_payable").val(totalTaxPayable.toFixed(2));
	$("#grand_total").val(grandTotal.toFixed(2));
	
	$("#grand_ins_amt").val(overallInsResp.toFixed(2));
	$("#grand_pt_paid").val(overallPtpaid.toFixed(2));
	$('#show_pos_pat_pos_grand_payed').val(overallPtpaid.toFixed(2));
	
	$("#grand_pt_resp").val(grandPtResp.toFixed(2));
	
	/*Tax Pt Payed*/
	var tax_paid = parseFloat($('#tax_pt_paid').val());
	tax_paid = (isNaN(tax_paid))?0.00:tax_paid;
	
	totalTaxPayable = parseFloat(totalTaxPayable.toFixed(2));
	
	/*Tax Pt.Resp.*/
	var tax_pt_resp = totalTaxPayable - tax_paid;
	$('#tax_pt_resp').val(tax_pt_resp.toFixed(2));
	
	if(tax_paid > totalTaxPayable){
		tax_paid = totalTaxPayable;
		$('#tax_pt_paid').val(tax_paid.toFixed(2));
		
		/*Tax Pt.Resp.*/
		var tax_pt_resp = totalTaxPayable - tax_paid;
		$('#tax_pt_resp').val(tax_pt_resp.toFixed(2));
		
		calculate_all();
	}
	
	if(mflag === 'chnageVal'){
		calculate_all();
	}
	/*End Tax Pt Payed*/
}

/*Divide Pt. Paid (Total Payment)*/
function dev_pay_in_prac(){
	var posRow = $("table.posTable tr");
	var grand_payed = $("#show_pos_pat_pos_grand_payed").val();
	var overall_discount = $("#overall_discount").val();
		overall_discount = overall_discount.replace('%','');
	$.each(posRow, function(i, obj){
		var allowed_cls = $(obj).find(".allowed_cls").val();
		if(allowed_cls>0){
			var price_disc = 0;
			var applied_amt = 0;
			if(overall_discount>0){
				price_disc = $(obj).find(".item_overall_disc").val();
			}else{
				price_disc = $(obj).find(".price_disc").val();
			}
			var ins_resp = parseFloat($(obj).find(".ins_amt_cls").val());
			var payed_amt=(allowed_cls-ins_resp)-price_disc;
			
			if(grand_payed>payed_amt){
				applied_amt = payed_amt;
				grand_payed = grand_payed - payed_amt;
			}else{
				applied_amt=grand_payed;
				grand_payed = 0;
			}
			//alert(payed_amt+'-'+grand_payed+'-'+applied_amt);
			$(obj).find(".payed_cls").val(applied_amt);
		}
	});
	
	/*Set Value for Tax paid*/
	var payed_amt = parseFloat($('#tax_payable').val());
	if(grand_payed>payed_amt){
		applied_amt=payed_amt;
		grand_payed = grand_payed - payed_amt;
	}else{
		applied_amt=grand_payed;
		grand_payed = 0;
	}
	$('#tax_pt_paid').val(applied_amt.toFixed(2));
	
	custom_tax_flag = "1";
	cal_overall_discount();
	calculate_all();
}

function add_custom_charge_row(){
	
	/*Get Coun of existing Custom Charge Rows*/
	var rowCount = $('.posTable > tbody > tr[id^="9_"]').length+1;
	var details = '';
	<?php if(defined('TAX_CHECKBOX_CHECKED') && constant('TAX_CHECKBOX_CHECKED')=='FALSE'){ echo'var tax_applied = false;';}else{ echo'var tax_applied = true;';}?>
	details += '<tr id="9_'+rowCount+'" class="customCharge">';
	
	details +='<!--td-->';
	details +='<input type="hidden" name="cs['+rowCount+'][pos_order_chld_id]" value="" />';
	details +='<input type="hidden" name="cs['+rowCount+'][pos_order_detail_id]" id="pos_order_detail_id_9_'+rowCount+'" value="" />';
	details +='<input type="hidden" name="cs['+rowCount+'][pos_module_type_id]" value="9" />';
	/*details +='<input type="hidden" name="cs['+rowCount+'][pos_upc_id]" id="pos_upc_id_9_'+rowCount+'" value="">';
	details +='<input type="hidden" name="cs['+rowCount+'][pos_upc_name]" id="pos_upc_name_9_'+rowCount+'" value="">';*/
	details += '<!--/td-->';
	
	/*OD/OS Column*/
<?php //if($pageName!="other_selection"): ?>
	details +='<td></td>';
<?php //endif; ?>
	
<?php if($pageName=="frameLensSelection"): ?>
	details +='<td colspan="2">';
<?php else: ?>
	details +='<td>';
<?php endif; ?>
		details += '<input style="width:100%;" type="text" class="itemname" name="cs['+rowCount+'][pos_item_name]" id="pos_item_name_9_'+rowCount+'" value="">';
		details += '<input type="hidden" name="cs['+rowCount+'][pos_item_id]" id="pos_item_id_9_'+rowCount+'" value="" />';
	details += '</td>';
	
	/*Prac Code*/
	details += '<td>';
		details += '<input style="width:100%;" type="text" class="pracodefield" name="cs['+rowCount+'][pos_item_prac_code]" id="pos_item_prac_code_9_'+rowCount+'" value="" title="" autocomplete="off" />';
		details += '<input type="hidden" id="pos_prac_id_9_'+rowCount+'" value="" class="hiddenPracId" onChange="getChargeByPracCode(this);" />';
	details += '</td>';
	
	/*Unit Price*/
	details += '<td>';
		details += '<input style="width:100%;text-align:right;" type="text" name="cs['+rowCount+'][pos_price]" id="pos_price_9_'+rowCount+'" value="" class="price_cls currency" onChange="this.value=parseFloat(this.value).toFixed(2);calculate_all();" />';
	details += '</td>';
	
	/*Unit*/
	details += '<td>';
		details += '<input type="text" style="width:100%; text-align:right;" class="qty_cls" id="pos_qty_9_'+rowCount+'" name="cs['+rowCount+'][pos_qty]" value="1" onChange="calculate_all();" autocomplete="off" onKeyUp="validate_qty(this);" />';
		details += '<input type="hidden" class="rqty_cls" id="pos_qty_right_9_'+rowCount+'" name="cs['+rowCount+'][pos_qty_right]" value="0" />';
	details += '</td>';
	
	/*Total Unit Cost*/
	details += '<td>';
		details += '<input style="width:100%;text-align:right;" type="text" name="cs['+rowCount+'][pos_allowed]" id="pos_allowed_9_'+rowCount+'" value="" class="allowed_cls currency" onChange="calculate_all();" />';
	details += '</td>';
	
	/*Calculative Field - Hidden*/
	details += '<td style="display:none">';
		details += '<input readonly style="width:100%;text-align:right;" type="text" name="cs['+rowCount+'][total_amount]" id="pos_total_amount_9_'+rowCount+'" value="0.00" class="price_total currency"  onChange="calculate_all();" />';
		/*Tax Calculations*/
		tax_applied = (tax_applied && facTax[7]>0);	/*Is tax is applicale by default at this practice*/
		details += '<input type="hidden" name="cs['+rowCount+'][tax_p]" id="tax_p_9_'+rowCount+'" class="tax_p" value="'+facTax[7]+'" />';
		details += '<input type="hidden" name="cs['+rowCount+'][tax_v]" id="tax_v_9_'+rowCount+'" class="tax_v" value="0.00" />';
		/*End Tax Calculations*/
	details += '</td>';
	
	/*Ins Resp*/
	details += '<td>';
		details += '<input style="width:100%;text-align:right;" type="text" name="cs['+rowCount+'][ins_amount]" id="ins_amount_9_'+rowCount+'" value="0.00" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="ins_amt_cls currency" />';
	details += '</td>';
	
	/*Discount*/
	details += '<td>';
		/*Line item's share in overall discount*/
		details += '<input type="hidden" name="cs['+rowCount+'][item_overall_discount]" id="item_overall_discount_9_'+rowCount+'" value="0.00" class="item_overall_disc" />';

		details += '<input style="width:100%;text-align:right;" type="hidden" name="cs['+rowCount+'][discount]" id="pos_discount_9_'+rowCount+'" value="0.00" onChange="calculate_all();" class="price_disc_per_proc" />';
		details += '<input style="width:100%;text-align:right;" type="text" name="cs['+rowCount+'][pos_read_discount]" id="pos_read_discount_9_'+rowCount+'" value="0.00" class="price_disc currency" onchange="changeDiscount(this);" autocomplete="off" />';
	details += '</td>';
	
	/*Pt. Paid*/
	details += '<td>';
		details += '<input style="width:100%;text-align:right;" type="text" name="cs['+rowCount+'][pt_paid]" id="pt_paid_9_'+rowCount+'" value="0.00" onChange="this.value = parseFloat(this.value).toFixed(2); calculate_all();" class="payed_cls currency" />';
	details += '</td>';
	
	/*Pt. Resp*/
	details += '<td>';
		details += '<input style="width:100%;text-align:right;" type="text" name="cs['+rowCount+'][pt_resp]" id="pt_resp_9_'+rowCount+'" value="0.00" class="resp_cls currency" readonly />';
	details += '</td>';
	
	/*Discount Code*/
	details += '<td>';
		details += '<select name="cs['+rowCount+'][discount_code]" id="discount_code_9_'+rowCount+'" class="text_10 disc_code dis_code_class" style="width:100%;" onChange="discountChanged(this);">';
			details += '<option value="">Please Select</option>';
			var defDisc = "";
			$.each(discCodes, function(di, dval){
				/*defDisc = (di==mainDiscountCode)?'selected="selected"':"";*/
				defDisc = '';
				details += '<option value="'+di+'" '+defDisc+'>'+dval+'</option>';
			});
		details += '</select>';
	details += '</td>';
	
	/*Insurance Cases*/
	details += '<td>';
		details += '<select name="cs['+rowCount+'][ins_case_id]" id="ins_case_id_9_'+rowCount+'" class="ins_case_class" style="width:100%;" onChange="switch_pat_ins_resp(\'9_'+rowCount+'\');">';
			details += '<option value="0">Self Pay</option>';
			$.each(insCases, function(insVal, insKey){
				insSelected = (insKey==top.main_iframe.ptVisionPlanId)?' selected="selected"':'';
				details += '<option value="'+insKey+'"'+insSelected+'>'+insVal+'</option>';
			});
		details += '</select>';
		details += '<input type="hidden" name="cs['+rowCount+'][del_status]" id="del_status_9_'+rowCount+'" value="0" class="del_status">';
	details += '</td>';
	
	/*Tax CheckBox*/
	details += '<td>';
		details += '<input type="checkbox" class="tax_applied" name="cs['+rowCount+'][tax_applied]" id="tax_applied_9_'+rowCount+'" value="1" '+((tax_applied)?'checked="checked"':'')+' onChange="cal_overall_discount()" />';
	details += '</td>';
	
	/*Delete Row Icon*/
	var delItemFunction = '';
	details += '<td>';
		details += '<img class="delitem" src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/del.png" onClick="delPosRow(\'9\', \''+rowCount+'\');" />';
	details += '</td>';
	
	details +='</tr>';
	
	if($('.posTable > tbody > tr[id]:last').length > 0)
		$(details).insertAfter($('.posTable > tbody > tr[id]:last'));
	else
		$(details).prependTo($('.posTable > tbody'));
	
	currencySymbols();
	prac_code_typeahead();
	calculate_all_Grand_POS();
}

function getChargeByPracCode(obj){
	var cpt_fee_id = $(obj).val();
	
	/*Fetch fee for CPT code*/
	if( cpt_fee_id !='' ){
		
		params = {fee_id:cpt_fee_id, action:'get_price_custom_charge'};
		
		$.ajax({
			type: 'POST', 
			url: top.WRP+'/interface/patient_interface/ajax.php',
			data: params,
			success: function(response){
				response = $.trim(response);
				response = (response=='')?0.00:response;
				$(obj).parent().next('td').children('input.price_cls').val(response);
			},
			complete: function(){
				calculate_all_Grand_POS();
			}
		});
	}
}
$(".date-pick").datepicker({
	changeMonth: true,changeYear: true,
	dateFormat: 'mm-dd-yy',
	onSelect: function() {
	$(this).change();
	}
});
</script>
<!------------------------------- POS PART END ------------------------------->