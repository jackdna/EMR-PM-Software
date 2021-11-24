<?php 
	require_once("../../../config/config.php");
	require_once("../../../library/ckeditor/ckeditor.php");
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Print Settings</title>
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/library/css/inv_css.css?<?php echo constant("cache_version"); ?>" type="text/css" />
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/library/js/jquery-1.10.1.min.js?<?php echo constant("cache_version"); ?>"></script>

<!-- Redactor is here -->
<!-- CSS SHEET -->
<link rel="stylesheet" href="<?php echo $GLOBALS['WEB_PATH'];?>/redactor/redactor.css" />
<!-- Reactor JS SOURCE -->
<script src="<?php echo $GLOBALS['WEB_PATH'];?>/redactor/redactor.js"></script>

<!-- Plug Ins -->
<!-- <script src="<?php echo $GLOBALS['WEB_PATH'];?>/redactor/plugins/fontcolor.js"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/plugins/fontfamily.js"></script> -->
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/plugins/fontsize.js"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/plugins/imagemanager.js"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/plugins/table.js"></script>
<script src="<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/plugins/fullscreen.js"></script>

<script>
var WEB_PATH='<?php echo $GLOBALS['WEB_PATH'];?>';
function get_rec_op()
{
	var id=document.getElementById('sel_cat').value;
	$('.content_div').css("display","block");
	if(id==1)
	{
		$('.frame_tab').css("display","block");
		$('.lense_tab').css("display","none");
		$('.cnt_len_tab').css("display","none");
		$('.suply_tab').css("display","none");
		$('.med_tab').css("display","none");
		$('.acce_tab').css("display","none");
		$('.content_div1').css("display","none");
		var len=$('.frame_tab input').length;
		for(i=0;i<len;i++)
		{
			$('.frame_tab input')[i].checked=false;
		}
	}
	else if(id==2)
	{
		
		$('.lense_tab').css("display","block");
		$('.frame_tab').css("display","none");
		$('.cnt_len_tab').css("display","none");
		$('.suply_tab').css("display","none");
		$('.med_tab').css("display","none");
		$('.acce_tab').css("display","none");
		$('.content_div1').css("display","none");
		var len=$('.lense_tab input').length;
		for(i=0;i<len;i++)
		{
			$('.lense_tab input')[i].checked=false;
		}
	}
	else if(id==3)
	{
		$('.cnt_len_tab').css("display","block");
		$('.frame_tab').css("display","none");
		$('.lense_tab').css("display","none");
		$('.suply_tab').css("display","none");
		$('.med_tab').css("display","none");
		$('.acce_tab').css("display","none");
		$('.content_div1').css("display","none");
		var len=$('.cnt_len_tab input').length;
		for(i=0;i<len;i++)
		{
			$('.cnt_len_tab input')[i].checked=false;
		}
	}
	else if(id==5)
	{
		$('.suply_tab').css("display","block");
		$('.frame_tab').css("display","none");
		$('.lense_tab').css("display","none");
		$('.cnt_len_tab').css("display","none");
		$('.med_tab').css("display","none");
		$('.acce_tab').css("display","none");
		$('.content_div1').css("display","none");
		var len=$('.suply_tab input').length;
		for(i=0;i<len;i++)
		{
			$('.suply_tab input')[i].checked=false;
		}
	}
	else if(id==6)
	{
		$('.med_tab').css("display","block");
		$('.suply_tab').css("display","none");
		$('.frame_tab').css("display","none");
		$('.lense_tab').css("display","none");
		$('.cnt_len_tab').css("display","none");
		$('.acce_tab').css("display","none");
		$('.content_div1').css("display","none");
		var len=$('.med_tab input').length;
		for(i=0;i<len;i++)
		{
			$('.med_tab input')[i].checked=false;
		}
	}
	else if(id==7)
	{
		$('.acce_tab').css("display","block");
		$('.med_tab').css("display","none");
		$('.suply_tab').css("display","none");
		$('.frame_tab').css("display","none");
		$('.lense_tab').css("display","none");
		$('.cnt_len_tab').css("display","none");
		$('.content_div1').css("display","none");
		var len=$('.acce_tab input').length;
		for(i=0;i<len;i++)
		{
			$('.acce_tab input')[i].checked=false;
		}
	}
	else if(id==1001 || id==1002 || id==1003 || id==1004)
	{
		$('.acce_tab').css("display","none");
		$('.med_tab').css("display","none");
		$('.suply_tab').css("display","none");
		$('.frame_tab').css("display","none");
		$('.lense_tab').css("display","none");
		$('.cnt_len_tab').css("display","none");
		$('.content_div').css("display","none");
		$('.content_div1').css("display","block");
	}
	else
	{
		$('.med_tab').css("display","none");
		$('.suply_tab').css("display","none");
		$('.frame_tab').css("display","none");
		$('.lense_tab').css("display","none");
		$('.cnt_len_tab').css("display","none");
		$('.content_div').css("display","none");
		$('.content_div1').css("display","none");
		$('.acce_tab').css("display","none");
	}
	
	$('input[type="checkbox"]').prop("checked", false);
	
	$.ajax({
		type: 'POST',
		url:"print_ajax.php",
		data: 'action=get&moduleId='+id,
		success: function(data){
			data = jQuery.parseJSON(data);
			for(x in data){
				val=data[x];
				if(id==1001 || id==1002 )
				{
					if(x=='value')
					{
						$('#content').val(data['value']);		
						$('#redactor-toolbar-0').siblings('.redactor-editor').html(data['value']);		
					}
					else
					{
						$('#height').val(data['margin']);		
					}
				}
				else
				{
					if($("input[name='options["+val+"]']").length){
						$("input[name='options["+val+"]']").prop("checked", true);
					}
					else{
						$("input[name='options["+id+"]["+val+"]']").prop("checked", true);
					}
					
					if(id==1)
					{
						if(x=='Header')
						{
							//header
							$('#frame_header').val(val);		
							$('#redactor-toolbar-1').siblings('.redactor-editor').html(val);
						}else if(x=='Footer')
						{
							//footer
							$('#frame_footer').val(val);		
							$('#redactor-toolbar-2').siblings('.redactor-editor').html(val);	
						}
						
						if(x=='header_margin')
						$('#frame_header_height').val(data['header_margin']);
						
						if(x=='footer_margin')
						$('#frame_footer_height').val(data['footer_margin']);
						
					}
					if(id==3)
					{
						if(x=='Header')
						{
							//header
							$('#lenses_header').val(val);		
							$('#redactor-toolbar-3').siblings('.redactor-editor').html(val);
						}else if(x=='Footer')
						{
							//footer
							$('#lenses_footer').val(val);		
							$('#redactor-toolbar-4').siblings('.redactor-editor').html(val);	
						}
						
						if(x=='header_margin')
						$('#lenses_header_height').val(data['header_margin']);
						
						if(x=='footer_margin')
						$('#lenses_footer_height').val(data['footer_margin']);
					}
				}
			}//end for loop
			
		}
	});
	
}

$(document).ready(function(e) {
	$('#content').redactor({
		buttonSource: true,
		//imageUpload: '<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/upload.php',
		plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
		minHeight: <?php echo $_SESSION['wn_height'] - 550;?>, 
		maxHeight: <?php echo $_SESSION['wn_height'] - 550;?>,
		buttonsHide: ['deleted', 'formatting', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'link', 'file']
	});
	
	$('#frame_header').redactor({
		buttonSource: true,
		//imageUpload: '<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/upload.php',
		plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
		minHeight: 100, 
		maxHeight: 100,
		buttonsHide: ['deleted', 'formatting', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'link', 'file']
	});
	
	
	$('#frame_footer').redactor({
		buttonSource: true,
		//imageUpload: '<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/upload.php',
		plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
		minHeight: 100, 
		maxHeight: 100,
		buttonsHide: ['deleted', 'formatting', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'link', 'file']
	});
	
	$('#lenses_header').redactor({
		buttonSource: true,
		//imageUpload: '<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/upload.php',
		plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
		minHeight: 100, 
		maxHeight: 100,
		buttonsHide: ['deleted', 'formatting', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'link', 'file']
	});
	
	
	$('#lenses_footer').redactor({
		buttonSource: true,
		//imageUpload: '<?php echo $GLOBALS['WEB_PATH']; ?>/redactor/upload.php',
		plugins: ['table','fontsize','fontcolor','imagemanager','fullscreen'],
		minHeight: 100, 
		maxHeight: 100,
		buttonsHide: ['deleted', 'formatting', 'unorderedlist', 'orderedlist', 'outdent', 'indent', 'link', 'file']
	});
//	$('#save_data_tab').click(function(){
		submitFrom = function()
		{
		//event.preventDefault();
		var x=$('#data_tab_form').serialize();
			$.ajax({
				type:"POST",
				url:"print_ajax.php",
				data: "action=save&"+x,
				success: function(msg)
				{
					top.falert("Settings saved");
					location.reload();
				}
				})
		}
//	});
	
	//BUTTONS
	var mainBtnArr = new Array();
	mainBtnArr[0] = new Array("frame","Save","top.main_iframe.admin_iframe.submitFrom();");
	top.btn_show("admin",mainBtnArr);
});

function RedactorOnChangeEvenAction(){
	h = $('#content').redactor('code.get');
	for(x in logo_url_arr){
		f = new RegExp('{'+x+'}', "i");
		r = '<img src="'+logo_url_arr[x]+'">';
		if(h.search(f)>=0){
			h = h.replace(f, r);
			$('#content').redactor('code.set',h);
		}
	}
	
}
</script>
</head>
<body>
<div class="mt10 rec_con print_st" style="width:100%">
  <div class="listheading">Printing Settings</div>
  <form id="data_tab_form" name="data_tab_form" method="post" style="width:100%">
    <table style="width:100%">
      <tr>
        <td width="14%"><label>Select Type</label></td>
        <td width="86%"><select name="sel_cat" id="sel_cat" onChange="get_rec_op()">
            <option value="">Choose Option</option>
            <?php
		
  $query=imw_query("select * from in_module_type order by module_type_name asc");
   while($row=imw_fetch_array($query))
   {
	   ?>
          <option value="<?php echo $row['id'];?>"><?php echo ucfirst($row['module_type_name']);?></option>
			<?php }
	$query=imw_query("select * from in_print_header where pid=0 order by label asc");
	if(imw_num_rows($query)>0){echo '<option disabled>-------------------</option>';}
   while($row=imw_fetch_array($query))
   {
	?> <option value="<?php echo $row['id'];?>"><?php echo ucfirst($row['label']);?></option>
    <?php   
	}?>
          </select></td>
      </tr>
    </table>


  <div class="content_div" style="height:<?php echo $_SESSION['wn_height']-430;?>px; overflow-y:auto; width:100%">
   
      <table style="width:100%">
        <tr class="listheading">
          <td colspan="16"><!--Tick The Option You want in Printing-->Select Label Options</td>
        </tr>
        <tr style="height:60px;">
        <td><label for="upc">Upc</label></td>
        <td style="width:100px"><input type="checkbox" name="options[upc_chk]" id="upc"/></td>
        <td><label for="manuf">Manufacturer</label></td>
        <td style="width:100px"><input type="checkbox" name="options[mf_chk]" id="manuf"/></td>
        <td><label for="type">Type</label></td>
        <td style="width:100px"><input type="checkbox" name="options[type_chk]" id="type"/></td>
        <td><label for="ven">Vendor</label></td>
        <td style="width:100px"><input type="checkbox" name="options[ven_chk]" id="ven"/></td>
        <td>&nbsp;</td>
        <td style="width:100px">&nbsp;</td>
         <td>&nbsp;</td>
        <td style="width:100px">&nbsp;</td>
        <td>&nbsp;</td>
        <td style="width:100px">&nbsp;</td>
        </tr>
        <tr>
          <td colspan="16"><hr></td>
        </tr>
      </table>
      <table class="frame_tab" style="width:100%">

		<tr>
          <td><label for="col1">Color</label></td>
          <td style="width:100px"><input type="checkbox" name="options[1][colr_chk]" id="col1"></td>
          <td><label for="brnd1">Brand</label></td>
          <td style="width:100px"><input type="checkbox" name="options[1][brnd_chk]" id="brnd1"/></td>
          <td><label for="fr_sh1">Frame Shape</label></td>
          <td style="width:84px"><input type="checkbox" name="options[1][shp_chk]" id="fr_sh1"></td>
          <td><label for="fr_st">Frame Style</label></td>
          <td style="width:84px"><input type="checkbox" name="options[1][styl_chk]" id="fr_st"></td>
          <td><label for="gen1">Gender</label></td>
          <td style="width:auto"><input type="checkbox" name="options[1][gender_chk]" id="gen1"></td>
          	
        </tr>
		<tr>
		  <td colspan="10"><hr></td>
	    </tr>
		<tr>
		  <td colspan="4"><strong>Printing Header</strong></td>
		  <td colspan="3">Header Height : <input type="text" name="frame_header_height" id="frame_header_height" max="60" min="0" value="" style="width:30px"></td>
		  <td colspan="3"></td>
	    </tr>
		<tr>
		  <td colspan="10"><textarea id="frame_header" name="frame_header"></textarea></td>
	    </tr>
		<tr>
		  <td colspan="4"><strong>Printing Footer</strong></td>
		  <td colspan="3">Footer Height : <input type="text" name="frame_footer_height" id="frame_footer_height" max="60" min="0" value="" style="width:30px"></td>
		  <td colspan="3"></td>
	    </tr>
		<tr>
		  <td colspan="10"><textarea id="frame_footer" name="frame_footer"></textarea></td>
	    </tr>

      </table>
      <table class="lense_tab" style="width:100%">
        <tr>
          <td><label for="col2">Color</label></td>
          <td style="width:100px"><input type="checkbox" name="options[2][colr_chk]" id="col2"></td>
          <td><label for="fc_ty">Seg Type</label></td>
          <td style="width:84px;"><input type="checkbox" name="options[2][lens_focl_chk]" id="fc_ty"></td>
          <td><label for="mat2">Material</label></td>
          <td style="width:84px;"><input type="checkbox" name="options[2][lens_mate_chk]" id="mat2"></td>
          <td><label for="a_r">Coating</label></td>
          <td style="width:84px;"><input type="checkbox" name="options[2][lens_a_r_chk]" id="a_r"></td>
          <td><label for="tran">Transition</label></td>
          <td style="width:84px;"><input type="checkbox" name="options[2][lens_tran_chk]" id="tran"></td>
          <td><label for="pol">Polarized</label></td>
          <td style="width:84px;"><input type="checkbox" name="options[2][lens_pol_chk]" id="pol"></td>
          <td><label for="edge">Edge</label></td>
          <td style="width:84px;"><input type="checkbox" name="options[2][lens_edge_chk]" id="edge"></td>
          <td><label for="tint">Tint</label></td>
          <td><input type="checkbox" name="options[2][lens_tint_chk]" id="tint"></td>
        </tr>
        
      </table>
      <table class="cnt_len_tab">
        <tr>
          <td><label for="col3">Color</label></td>
          <td style="width:100px"><input type="checkbox" name="options[3][colr_chk]" id="col3"></td>
          <td><label for="brnd">Brand</label></td>
       	  <td style="width:100px"><input type="checkbox" name="options[3][brnd_chk]" id="brnd"/></td>
          <td><label for="mat3">Material</label></td>
          <td style="width:84px"><input type="checkbox" name="options[3][cnt_len_mat_chk]" id="mat3"/></td>
          <td><label for="wr_tm">Wear Time</label></td>
          <td style="width:84px"><input type="checkbox" name="options[3][cnt_len_wer_chk]" id="wr_tm"></td>
          <td><label for="sup3">Supply</label></td>
          <td style="width:84px"><input type="checkbox" name="options[3][cnt_len_sup_chk]" id="sup3"></td>
        </tr>
        <tr>
          <td colspan="10"><hr></td>
        </tr>
        <tr>
         <tr>
		  <td colspan="4"><strong>Printing Header</strong></td>
		  <td colspan="3">Header Height : <input type="text" name="lenses_header_height" id="lenses_header_height" max="60" min="0" value="" style="width:30px"></td>
		  <td colspan="3"></td>
	    </tr>
        </tr>
        <tr>
          <td colspan="10"><textarea id="lenses_header" name="lenses_header"></textarea></td>
        </tr>
        <tr>
		  <td colspan="4"><strong>Printing Footer</strong></td>
		  <td colspan="3">Footer Height : <input type="text" name="lenses_footer_height" id="lenses_footer_height" max="60" min="0" value="" style="width:30px"></td>
		  <td colspan="3"></td>
	    </tr>
        <tr>
          <td colspan="10"><textarea id="lenses_footer" name="lenses_footer"></textarea></td>
        </tr>
      </table>
      <table class="suply_tab">
        <tr>
          <td><label for="msmt5">Measurement</label></td>
          <td style="width:84px"><input type="checkbox" name="options[5][suply_mnt_chk]" id="msmt5"></td>
        </tr>
      </table>
      <table class="acce_tab">
        <tr>
          <td><label for="msmt5">Measurement</label></td>
          <td style="width:84px"><input type="checkbox" name="options[7][suply_mnt_chk]" id="msmt6"></td>
        </tr>
      </table>
      <table class="med_tab">
        <tr>
          <td><label for="exp_dt">Exp. Date</label></td>
          <td style="width:84px"><input type="checkbox" name="options[6][med_exp_chk]" id="exp_dt"></td>
        </tr>
      </table>
      <hr>
      <table>
        <tr style="height:60px;">
          <td><label for="wh_pr">Wholesale Price</label></td>
        <td style="width:100px"><input type="checkbox" name="options[wholesale_chk]" id="wh_pr" /></td>
         <td><label for="rt_pr">Retail Price</label></td>
        <td style="width:100px"><input type="checkbox" name="options[retail_chk]" id="rt_pr"/></td>
       
        </tr>
      </table>
      
        <input type="hidden" value="Save" name="save_qnty" id="save_data_tab" >
  </div>
  
  <div class="content_div1" style="width:999px; display:none"><br>

  Max Height : <input type="text" name="height" id="height" max="60" min="0" value="" style="width:30px">
  <br>

  <textarea id="content" name="content"></textarea>
  </div>
  </form>
</div>

</body>
</html>