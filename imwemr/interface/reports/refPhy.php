<?php
	$without_pat = "yes";
	require_once("reports_header.php");
	include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
	require_once('../../library/classes/class.reports.php');
	require_once('../../library/classes/cls_common_function.php');
	
	//CHECKING IF PROPER VARIABLES ARE SUBMITTED TO OPEN THIS POPUP WINDOW
	$txtField = isset($_REQUEST['name_field']) ? strip_tags(trim($_REQUEST['name_field'])) : '';
	$idField = isset($_REQUEST['id_field']) ? strip_tags(trim($_REQUEST['id_field'])) : '';
	$q = isset($_REQUEST['lname']) ? strip_tags(trim($_REQUEST['lname'])) : '';
	
	$arrUserTitles=array('md'=>'md','od'=>'od','do'=>'do','pa'=>'pa','np'=>'np','op'=>'op','md-oph'=>'md-oph','md-phd'=>'md-phd','fnp'=>'fnp','pac'=>'pac','aprn'=>'aprn','cnm'=>'cnm');
	$strUserTitles= "'".implode("','", $arrUserTitles)."'";
?>
<html>
<head>
	<script language="javascript">
		function warn_slow(){
			var d=confirm('Are you sure? It may take long time to show all records.');
			if(d){
				document.frm_refPhy.submit();
			}else return false;
		}
		
		function uncheck_others(clickedVal){
			var titleSelected=false;
			var selectAll=false;
			var totSelected=0;
			
			//CHECK IF ALL SELECTED
			if(clickedVal=='all' && $('#all').is(':checked')){
				selectAll=true;
			}

			//IF ANYONE IS NOT SELECTED THEN UNCHECK ALL OPTION
			if(clickedVal!='all' && !($('#'+clickedVal).is(':checked'))){
				$('#all').prop('checked', false);
			}
			
			$('.ref_title').each(function () {
				if(selectAll==true){
					$(this).prop('checked', true);
					titleSelected=true;
					
				}else if(selectAll==false && clickedVal=='all'){
					$(this).prop('checked', false);	
					
				}else if($(this).prop('checked')){
					titleSelected=true;
					totSelected+=1;
				}
			});
			
			//IF MIN 13 OPTIONS SELECTED THEN SELECTE ALL OPTION AUTOMATICALLY
			if(totSelected>=13 && clickedVal!='all'){
				$('#all').prop('checked', true);
			}
			
			if(titleSelected==true){
				$('#lname').val('');
				$('#lname').prop('disabled', true);
				$('#lname').prop('readonly', true);
			}else{
				$('#lname').prop('disabled', false);
				$('#lname').prop('readonly', false);
			}
		}
	</script>
</head>
<body>
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="row">
			<div class="col-xs-12">
				<strong>Search Referring Physician</strong>
			</div>
		</div>
	</div>
	<div class="panel-body">
<?php
	if(empty($txtField) || empty($idField)){
		die('<div class="m10 warning"><b><i>name_field</i></b> or <b><i>id_field</i></b> not provided. It is mandatory to recieve the selected value.</div>');
	}
?>
<form method="post" name="frm_refPhy" id="frm_refPhy">
	<div class="row">
		<div class="col-xs-8">
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="md" value="md" onClick="uncheck_others('md');"/>
				<label for="md">MD</label>
			</div>&nbsp;&nbsp;
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="od" value="od" onClick="uncheck_others('od');"/>
				<label for="od">OD</label>
			</div>
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="do" value="do" onClick="uncheck_others('do');"/>
				<label for="do">DO</label>
			</div>
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="pa" value="pa" onClick="uncheck_others('pa');"/>
				<label for="pa">PA</label>
			</div>
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="np" value="np" onClick="uncheck_others('np');"/>
				<label for="np">NP</label>
			</div>
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="op" value="op" onClick="uncheck_others('op');"/>
				<label for="op">OP</label>
			</div>
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="md-oph" value="md-oph" onClick="uncheck_others('md-oph');"/>
				<label for="md-oph">MD-OPH</label>
			</div>
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="md-phd" value="md-phd" onClick="uncheck_others('md-phd');"/>
				<label for="md-phd">MD-PHD</label>
			</div>
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="fnp" value="fnp" onClick="uncheck_others('fnp');"/>
				<label for="fnp">FNP</label>
			</div>			
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="pac" value="pac" onClick="uncheck_others('pac');"/>
				<label for="pac">PAC</label>
			</div>				
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="aprn" value="aprn" onClick="uncheck_others('aprn');"/>
				<label for="aprn">APRN</label>
			</div>	
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="cnm" value="cnm" onClick="uncheck_others('cnm');"/>
				<label for="cnm">CNM</label>
			</div>				
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="other" value="other" onClick="uncheck_others('other');"/>
				<label for="other">Other</label>
			</div>&nbsp;&nbsp;
			<div class="checkbox checkbox-inline">
				<input type="checkbox" class="ref_title" name="ref_title[]" id="all" value="all" onClick="uncheck_others('all');"/>
				<label for="all">All</label>
			</div>
		</div>
		<div class="col-xs-4">
			<input type="text" name="lname" id="lname" placeholder="Last Name" value="<?php echo $q;?>" />
			<input type="submit" value="Search" class="btn btn-success" name="btn" id="btn" style="vertical-align:top" />
		</div>
		
	</div>
</form>
<div id="body_area" style="overflow:auto;">
<?php
//IF FORM IS SUBMITTED...

if((isset($_REQUEST['lname']) && !empty($q)) || sizeof($_REQUEST['ref_title'])>0){
	$objDb = $GLOBALS['adodb']['db'];
	//ASSIGNING VALUES TO KEYS
	$ref_title= array_combine($_REQUEST['ref_title'], $_REQUEST['ref_title']); 
	
	$qry = "SELECT Title, physician_Reffer_id,FirstName,MiddleName,LastName,delete_status FROM refferphysician WHERE 1=1";
	if(empty($q)==false){	
		$qry.=" AND LastName LIKE '$q%'";
	}
	
	
	if(!$ref_title['all'] && sizeof($ref_title)>0){
		
		if($ref_title['other']){
			$sel_titles= "'".implode("','", $ref_title)."'";
			$sel_titles.=",''";  //SO THAT BLANK TITLE RECORDS ALSO DISPLAY
			$arrTitlesNotSelected=array_diff($arrUserTitles, $ref_title);
			$not_sel_titles="'".implode("','", $arrTitlesNotSelected)."'";
			//$qry.= " AND LOWER(Title) IN(".$sel_titles.") AND LOWER(Title) NOT IN(".$not_sel_titles.")";
			$qry.= " AND LOWER(Title) NOT IN(".$not_sel_titles.")";
		}else{
			$sel_titles= "'".implode("','", $ref_title)."'";
			$qry.= " AND LOWER(Title) IN(".$sel_titles.")";
		}
	}
	$qry.=" ORDER BY LastName, FirstName";
	$result = imw_query($qry);
	if($result && imw_num_rows($result)>0){
		//NOW PRINT THE RESULT
		$cnt = 1;
		?>
		<br />
		<div class="table-responsive" id="show_all_div">
		<table class="table table-bordered" id="ref_phy_result">
			<thead>
				<tr class="page_block_heading_patch">
					<th style="width:20px;"><div class="checkbox"><input type="checkbox" name="selectAll" id="selectAll" value=""><label for="selectAll"></label></div></th>
					<th width="40px" align="left">&nbsp;Title</th>
					<th width="auto" align="left">&nbsp;Name</th>
				</tr>
			</thead>
			<tbody>		
			<?php
			while($rs = imw_fetch_assoc($result)){
				$physician_Reffer_id = $rs['physician_Reffer_id'];
				$phy_name = $rs['LastName'].', ';
				$phy_name .= $rs['FirstName'].' ';
				$phy_name .= $rs['MiddleName'];
				$phy_name = ucfirst(trim($phy_name));
				if($phy_name[0] == ','){
					$phy_name = substr($phy_name,1);
				}
				$fontColor = '#000';
				if($rs['delete_status']=='1'){$fontColor = '#CC0000';}
			?>
			<tr>
				<td align="center"><div class="checkbox"><input type="checkbox" name="chk<?php echo $physician_Reffer_id;?>" id="chk<?php echo $physician_Reffer_id;?>" value="<?php echo $physician_Reffer_id;?>"><label for="chk<?php echo $physician_Reffer_id;?>"></label></div></td>
				<td style="color:<?php echo $fontColor;?>"><?php echo $rs['Title'];?></td>
                <td id="ref<?php echo $physician_Reffer_id;?>" style="color:<?php echo $fontColor;?>"><?php echo $phy_name;?></td>
			</tr>
			<?php
			$cnt++;
		}?>
		</tbody>
		</table>
		</div>
        
		<input type="hidden" name="res_found" id="res_found" value="<?php echo ($cnt-1).' Result(s) Found';?>" />
		<?php		
	}else{
		//SHOW THAT RESULT NOT FOUND
		echo '<div class="m10 warning"><b>Result Not Found!</b></div>';
	}
}else if(isset($_REQUEST['lname']) && empty($q)){
	echo '<br /><div class="text-center alert alert-info">Empty Search Not Allowed!</div>';
}
?>
</div>
</div>
<div class="panel-footer">
	<input type="button" value="OK" class="btn btn-success" id="btn_ok" style="margin-right:10px;" />
	<input type="button" value="Close" class="btn btn-success" onClick="window.close();" /></div>
</div>
<script language="javascript" type="text/javascript">
	var ref_title = '<?php echo implode(',', $ref_title);?>';
	var arr_ref_title='';
	if(ref_title!=''){
		arr_ref_title = ref_title.split(',');
	}

	$(document).ready( function() {
		titleSelected=false;
		if(arr_ref_title.length>0){
			for(x in arr_ref_title){
				$('#'+arr_ref_title[x]).prop('checked', true);
				titleSelected=true;
			}		
		}
		
		if(titleSelected==true){
			$('#lname').val('');
			$('#lname').prop('disabled', true);
			$('#lname').prop('readonly', true);
		}
		window.resizeTo(850, 550);
	});
	
	//SETTING CONTAINER HEIGHT ACCORDING TO POPUP HEIGHT
	var winHeight = parseInt($(window).height());
	$('#body_area').height(winHeight-177);
	$('#show_res_found').text($('#res_found').val());
	if(parseInt($('#res_found').val()) >= 3000){
		$('#show_all_div').css('height',winHeight-110);
	}
	
	//SETTING MOUSE OVER ANIMATION FOR ROW COLOR
	$("#ref_phy_result tbody tr").hover(
			function () {
				if(!$(this).hasClass('bg3')){
					$(this).addClass("bg3");
				}
			},
			function () {
				if(!$(this).find('INPUT:checkbox').prop('checked')){
					$(this).removeClass("bg3");
				}
			}
	);
	
	//SHOWING ok BUTTON IF RESULT FOUND
	if($('#res_found').val() != ''){
		$('#btn_ok').show();
	}
		
	//SELECTING ALL CHECKBOX ON CLICK OF MAIN CHECKBOX
	$('#ref_phy_result').find('INPUT#selectAll').click( function() {
		$('#ref_phy_result').find('INPUT:checkbox').prop('checked', $(this).prop('checked'));
		$('#ref_phy_result').find("TR").toggleClass('bg3', $(this).prop('checked'))
	});
	
	//FINAL OPERTION ON CLICK OF ok BUTTON
	$('#btn_ok').click(function(){
		var IDs = [];
		var Names = [];
		var oldNames = '';
		var oldIDs = '';
		$('#ref_phy_result').find('INPUT:checkbox').not('#selectAll').each( function() {
			if( $(this).prop('checked') == true ) {				
				IDs.push($(this).val());
				tdObj = $('#ref'+$(this).val()).text();
				Names.push(tdObj);				
			}
			window.close();
		});
		
		window.opener.document.getElementById('<?php echo $txtField;?>').value = "";
		window.opener.document.getElementById('<?php echo $idField;?>').value = "";
		if(Names.length && IDs.length){
			window.opener.document.getElementById('<?php echo $txtField;?>').value = Names.join(',');
			window.opener.document.getElementById('<?php echo $idField;?>').value = IDs.join(',');
		}
	});
</script>
</body>
</html>
