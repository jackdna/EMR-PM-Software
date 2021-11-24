<?php
/*
File: left.php
Coded in PHP7
Purpose: Left Menu Information
Access Type: Include File
*/
?>
<script type="text/javascript">
var link_selected = '';
	function page_link(page, checkFlag){
		WindowDialog.closeAll();
		
		if(typeof(checkFlag)=="undefined" &&  top.main_iframe.admin_iframe){
			var pos_page_name_chk = top.main_iframe.admin_iframe.document.getElementById('page_name');
			if(typeof(pos_page_name_chk)!="undefined" && pos_page_name_chk!=null && pos_page_name_chk.value=="pt_frame_selection"){
				link_selected = page;
				top.main_iframe.admin_iframe.frm_sub_fun('auto_save');
				if( page != "billing" )
					$("#loading").show();
				return false;
			}
		}
		
		var sub_page = page.split('-');
		page = sub_page[0];
		
		if(page=="demo"){
			top.main_iframe.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/demographics/index.php?newpatientpage';
		}
		else if(page=="pos"){
			
			if(sub_page.length>1){
				if(sub_page[1]==''){
					top.main_iframe.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/patient_interface/index.php';
				}
				else if(sub_page[1]=='frame' || sub_page[1]=="lens"){
					 top.document.getElementById('main_iframe').contentWindow.$('div#tab1 #frame_li').trigger('click');
				}
				else if(sub_page[1]=="contact_lens"){
					top.document.getElementById('main_iframe').contentWindow.$('div#tab1 #contact_li').trigger('click');
				}
				else if(sub_page[1]=="patient_rx_history"){
					top.document.getElementById('main_iframe').contentWindow.$('div#tab1 #patient_rx_history').trigger('click');
				}
				else if(sub_page[1]=="other_selection"){
					top.document.getElementById('main_iframe').contentWindow.$('div#tab1 #other_selection').trigger('click');
				}
			}
			else{
				top.main_iframe.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/patient_interface/index.php';
			}
		}
		else if(page=="billing"){
			WindowDialog.open('Add_new_popup','remoteConnect.php','opt_med');
		}
		else if(page=="inventory"){
			top.main_iframe.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/stock_order/index.php';
		}
		else if(page=="order"){
			top.main_iframe.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/stock_order/order_tracking.php';
		}
		else if(page=="admin"){
			top.main_iframe.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/admin/index.php';
		}
		else if(page=="report"){
			top.main_iframe.location.href='<?php echo $GLOBALS['WEB_PATH'];?>/interface/reports/index.php';
		}
	}
</script>
<div id="loading" style="display:none;">
    <img src="<?php echo $GLOBALS['WEB_PATH']; ?>/images/loading_image.gif" />
</div>

<div class="left_content fl" style="height:<?php echo $_SESSION['wn_height']-298;?>px;">
        <div class="nav fl" id="left_navi">
            <ul style="margin-top:10px;" class="fl">
                <li><a onClick="page_link('demo');" href="javascript:void(0);" target="main_iframe" id="demo_id"><div class="nav_div">Demographics</div></a></li>
				<?php if( check_privillage('pos') ): ?>
                <li><a onClick="page_link('pos');" href="javascript:void(0);" target="main_iframe" id="pt_interface_id"><div class="nav_div">Point of Sale</div></a></li>
				<?php endif; ?>
                <li><a onclick="page_link('billing');" href="javascript:void(0);"><div class="nav_div">Billing</div></a></li>
				<?php if( check_privillage('inventory') ): ?>
                <li><a onClick="page_link('inventory');" href="javascript:void(0);" target="main_iframe"><div class="nav_div">Inventory</div></a></li>
				<?php endif; ?>
				<li><a onClick="page_link('order');" href="javascript:void(0);" target="main_iframe"><div class="nav_div">Order Tracking</div></a></li>
                <?php if( check_privillage('admin') ): ?>
				<li><a onClick="page_link('admin');" href="javascript:void(0);" target="main_iframe"><div class="nav_div">Admin</div></a></li>
				<?php endif; ?>
				<?php if( check_privillage('reports') ): ?>
                <li><a onClick="page_link('report');" href="javascript:void(0);" target="main_iframe"><div class="nav_div" target="main_iframe">Reports</div></a></li>
				<?php endif; ?>
            </ul>
       </div>
</div>