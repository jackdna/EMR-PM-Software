<?php 
	include '../../../config/globals.php';
	include '../../../library/classes/cls_common_function.php';
	
	$cls = new CLSCommonFunction();
	
	$_SESSION['pri_referral']="";
	$_SESSION['sec_referral']="";
	$_SESSION['ter_referral']="";
	$_SESSION['btn']="";
	
	$i_key= (int) $ref_type;
	$i_key= ($i_key > 0)	? $i_key : 1;
	
	$pri_url = ($i_key == 1) ? '#' : '?ref_type=1';
	$sec_url = ($i_key == 2) ? '#' : '?ref_type=2';
	$ter_url = ($i_key == 3) ? '#' : '?ref_type=3';
	
	$ins_type= 'primary';
	$ins_type= ($i_key == 2) ? 'secondary' : $ins_type;
	$ins_type= ($i_key == 3) ? 'tertiary' : $ins_type;
	
	$s_name  = substr($ins_type,0,3);
	$u_name_s = substr(ucwords($ins_type),0,3);
	
	if($_SESSION['insId'.$u_name_s] == '')
	{
		$_SESSION['insId'.$u_name_s] = get_active_ins_id($i_key,$_SESSION['patient']);
	}
	
	/*if(!isset($_SESSION['currentCaseid']) && $_SESSION['currentCaseid'] == '')
	{
		$_SESSION['currentCaseid'] = get_active_ins_id($i_key,$_SESSION['patient']);		
	}*/
	
	$ins_data_id = $_SESSION['insId'.$u_name_s];
	$ins_caseid	= $_SESSION["currentCaseid"];	
	
	$rc_updated_flag = 0;	
	if(trim($_REQUEST['sbtExpRef']) != "")
	{
		$pri_ref_phy_id_arr = $_REQUEST['ref1_phyId'];
		if(!is_array($pri_ref_phy_id_arr))
		{
			$pri_ref_phy_id_arr = array($pri_ref_phy_id_arr);	
		}
		foreach($pri_ref_phy_id_arr as $key => $ref1_phyId_str)
		{
			if(trim($ref1_phyId_str) == "")
			{
				continue;	
			}
			$ref_id_pri = $_REQUEST['ref_id_pri'][$key];		
			$ref1_phy_str = $_REQUEST['ref1_phy'][$key];
			$reffral_no1_str = $_REQUEST['reffral_no1'][$key];	
			$reff1_date_str = $_REQUEST['reff1_date'][$key];	
			$end1_date_str = $_REQUEST['end1_date'][$key];	
			$eff1_date_str = $_REQUEST['eff1_date'][$key];
			
			$no_ref1_str = $_REQUEST['no_ref1'][$key];
			
			$note1_str = $_REQUEST['note1'][$key];
		
			if(trim($ref1_phyId_str) == ""){
				if($ref1_phy_str){
					$Reffer_physician_arr = preg_split('/,/',$ref1_phy_str);
					$phyLnameArr = explode(' ',trim($Reffer_physician_arr[0]));
					$phylname = trim($phyLnameArr[0]);
					$phyFnameArr = explode(' ',trim($Reffer_physician_arr[1]));
					$phyfname = trim($phyFnameArr[0]);
					$ref_phy_res_id = get_reffer_physician_id('FirstName',$phyfname,'LastName',$phylname);
					$ref1_phyId = $ref_phy_res_id[0][physician_Reffer_id];
				}
			}	
			else{
				$ref1_phyId = $_REQUEST[$ref1_phyId_str];
			}
			
			if($reff1_date_str){
				$reff1_date_str = getDateFormatDB($reff1_date_str);
			}
			if($eff1_date_str){
				$eff1_date_str = getDateFormatDB($eff1_date_str);
			}
			if($end1_date_str){
				$end1_date_str = getDateFormatDB($end1_date_str);
			}
			$no_ref_arr = explode('/',$no_ref1_str);
			$_REQUEST['priNoRef'] = trim($no_ref_arr[0]); 
			$_REQUEST['priUsedRef'] = trim($no_ref_arr[1]); 
			$no_reff = trim($no_ref_arr[0]) - trim($no_ref_arr[1]);
			$reff_used = $no_ref_arr[1];
			
			if($ref_id_pri){			
				$query = "update patient_reff set patient_id = ".$_SESSION['patient'].",
						reff_phy_id = '$ref1_phyId_str', reff_by = '".addslashes($ref1_phy_str)."',	no_of_reffs = '".$no_reff."',
						md = '".$mode1."', reffral_no = '".$reffral_no1_str."', reff_date = '".$reff1_date_str."', 
						reff_used = '".$reff_used."', effective_date = '".$eff1_date_str."', end_date = '".$end1_date_str."', 
						insCaseid = '".$ins_caseid."', note = '".$note1_str."', ins_data_id = '".$ins_data_id."',
						reff_type = '".$i_key."' where reff_id = ".$ref_id_pri." ";					
				$sql = imw_query($query);
				$insert_id = imw_insert_id();
				$rc_updated_flag = ($insert_id > 0) ? 1 : 0;
			}							
		}				
	}
	
	if($i_key != "" && $ins_data_id != "")
	{
		$req_qry = "SELECT pat_ref.*,TRIM(CONCAT(reff.LastName,', ',reff.FirstName,' ',reff.MiddleName,if(reff.MiddleName!='',' ',''),reff.Title)) as refphy, 
									if(pat_ref.end_date < current_date() and pat_ref.end_date != '0000-00-00',1,
										if(pat_ref.no_of_reffs = 0 AND pat_ref.reff_used >0,1,0)
									) as exp_status,
									insurance_companies.in_house_code as in_house_code  
									FROM patient_reff pat_ref
									LEFT JOIN refferphysician reff 
									ON pat_ref.reff_phy_id = reff.physician_Reffer_id 
									left join insurance_companies on insurance_companies.id = pat_ref.ins_provider 								
									left join insurance_data on insurance_data.id =  pat_ref.ins_data_id 									
									WHERE pat_ref.reff_type = '".$i_key."' and insurance_data.ins_caseid = '".$ins_caseid."' and 									
									pat_ref.ins_data_id = '".$ins_data_id."' and pat_ref.del_status = 0 
									order by pat_ref.reff_id desc";	
		$exp_ref_obj = imw_query($req_qry);														
	}
	
?>
<html>
	<head>
		<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo 'Referrals :: imwemr ::';?></title>
   	
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/bootstrap.css" type="text/css" rel="stylesheet" />
		<link href="<?php echo $GLOBALS['webroot'];?>/library/css/common.css" type="text/css" rel="stylesheet" />
    <link href="<?php echo $GLOBALS['webroot'];?>/library/css/jquery.datetimepicker.min.css" type="text/css" rel="stylesheet" />
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
    <!-- jQuery's Date Time Picker -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/core_main.js"></script>
		<script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/common.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/insurance.js"></script>
    <!-- Bootstrap typeHead -->
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap-typeahead.js"></script>
    <script type="text/javascript">
			window.focus();
			window.onload =function()
			{
				var parWidth = (screen.availWidth > 800) ? 800 : screen.availWidth ;
				window.resizeTo(parWidth,745);
				var t = 10;
				var l = parseInt((screen.availWidth - window.outerWidth) / 2)
				window.moveTo(l,t);
			}
			
			var i_key = '<?php echo $i_key;?>';	
			var mandatory_fld = [];
			var arr_opened_popups = window.opener.top.arr_opened_popups;
			$(function(){
				$("#"+i_key).addClass('active')
			});
		</script>
	</head>
  <body>
  	
      <div class="panel panel-primary">
        <div class="panel-heading">Referrals</div>
        <div class="panel-body popup-panel-body" style="max-height:580px; height:580px;">
          
          <!-- Tabs -->
          <ul class="nav nav-tabs">
            <li id="1"><a href="<?php echo $pri_url;?>">Primary</a></li>
            <li id="2"><a href="<?php echo $sec_url;?>">Secondary</a></li>
            <li id="3"><a href="<?php echo $ter_url;?>">Tertiary</a></li>
          </ul>
          
          <!-- Contents -->
          <div class="tab-content mt10">
          	<?php if($rc_updated_flag == 1){ ?>
							<div style="alert alert-success"> Referral Updated Successfully </div>
          	<?php } ?>

          	<form name="insuranceReff" id="insuranceReff" enctype="multipart/form-data" method="post">  
            	<input type="hidden" id="preObjBack" name="preObjBack" value=""/>
              <input type="hidden" name="ins_provider" value="<?php echo $ins_provider; ?>" />
              <input type="hidden" name="ins_type" value="<?php echo $i_key; ?>" />
              <input type="hidden" name="ins_data_id" value="<?php echo $ins_data_id; ?>" />
              <input type="hidden" name="sbtExpRef" value="1" />
              
							<?php
								$btn_class="";
								$cnt = imw_num_rows($exp_ref_obj);
								if( $cnt > 0 )
								{
									$request_iterator = 0;
									while($referral_row = imw_fetch_assoc($exp_ref_obj))
									{
										
										$request_iterator++;
										$cur_reff_id = $referral_row['reff_id'];
										$cur_reff_phy_id = $referral_row['reff_phy_id'];
										$cur_reff_date = $referral_row['reff_date'];
										$cur_effective_date = $referral_row['effective_date'];
										$cur_end_date = $referral_row['end_date'];
										$cur_no_of_reffs = $referral_row['no_of_reffs'];
										$cur_reff_used = $referral_row['reff_used'];
										$cur_reffral_no = $referral_row['reffral_no'];
										$cur_note = $referral_row['note'];
										$target_img = '<img onclick="del_reff_ins_act(this,'.$cur_reff_id.');" class="pointer" src="../../../library/images/close1.png" alt="Delete Referral" title="Delete Referral" />';			
							
										if($cur_reff_phy_id != 0){
											$cur_reff_by = $cls->get_ref_phy_name($cur_reff_phy_id);                
										}
							
										if($cur_reff_date == '0000-00-00' || $cur_reff_date == ''){
											$cur_reff_date = '';
										}else{
											$cur_reff_date = get_date_format($cur_reff_date);
										}
							
										if($cur_effective_date == '0000-00-00' || $cur_effective_date == ''){
											$cur_effective_date = '';
										}else{
											$cur_effective_date = get_date_format($cur_effective_date);
										}
							
										if($cur_end_date == '0000-00-00' || $cur_end_date == ''){
											$cur_end_date = '';
										}else{
											$cur_end_date = get_date_format($cur_end_date);
										}
										
							
							?>		
              			
                    <div class="table_grid" id="<?php echo $i_key.'grid'.$request_iterator;?>">
                    <div class="col-sm-12 margin-top-5">
                      <div class="row">
                        <div class="col-sm-7">
                          <label class="sub-heading">Referral</label>
                        </div>
                        <div class="col-sm-5 text-right">
                          <img onclick="del_reff_ins_act(this,<?php echo $cur_reff_id;?>);" class="pointer" src="<?php echo $GLOBALS['webroot'];?>/library/images/close1.png" alt="Delete Referral" title="Delete Referral" width="28">
                          <a href="javascript:foo();" class="btn btn-success btn-xs" id="scanner_image_<?php echo $s_name;?>_<?php echo $request_iterator;?>" onClick="openScanDocument('<?php echo $cur_reff_id; ?>','<?php echo $ins_type;?>_reff','<?php echo intval($ins_data_id);?>');">
                          	<img src="<?php echo $GLOBALS['webroot'] ?>/library/images/scanner.png" alt="Referral scan document"/>
                        	</a>
                          
                        </div>
                      </div>
                    </div>
                  		
              			<div class="col-sm-12">
                    	<?php $_SESSION['ref_id_pri'] = $cur_reff_id; ?>
                    	<input type="hidden" name="ref<?php echo $i_key;?>_phyId[]" id="ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>" value="<?php echo $cur_reff_phy_id; ?>" />
                    	<input type="hidden" name="ref_id_<?php echo $s_name;?>[]" id="ref_id_<?php echo $s_name;?><?php echo $request_iterator; ?>" value="<?php echo $cur_reff_id; ?>" />
                  
                      <div class="row">
                      
                        <div class="col-sm-3">
                          <label>Ref. Physician</label><br>
                          <?php
                              $strRefPhy = "";
                              $strRefPhy = trim(stripslashes($cur_reff_by));
                          ?>
                          
                          <input type="text" name="ref<?php echo $i_key;?>_phy[]" id="ref<?php echo $i_key;?>_phy<?php echo $request_iterator; ?>" value="<?php echo trim(stripslashes($strRefPhy)); ?>" class="form-control" data-search-by="" data-action="search_physician" data-text-box="ref<?php echo $i_key;?>_phy<?php echo $request_iterator; ?>" data-id-box="ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>" size="25" onKeyPress="javascript: document.insuranceCaseFrm.ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>.value = '';" onFocus="loadPhysicians(this,'ref<?php echo $i_key;?>_phyId<?php echo $request_iterator; ?>');">
                     		</div>
                        
                        <div class="col-sm-3">
                          <label>Start Date</label><br>
                          <div class="input-group">
                            <input class="datepicker form-control" type="text" name="eff<?php echo $i_key;?>_date[]" id="eff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_effective_date); ?>" size="11" onBlur="checkdate(this);"  maxlength="10" />
                            <label for="eff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                          </div>    
                        </div>
                        
                        <div class="col-sm-3">
                          <label>End Date</label><br>
                          <div class="input-group">
                            <input type="text" class="datepicker form-control" name="end<?php echo $i_key;?>_date[]" id="end<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_end_date); ?>" size="11" onBlur="checkdate(this); chkFuture('eff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>',this);" onChange="checkdate(this);" maxlength="10" />
                            <label for="end<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                          </div>   
                        </div>
                        
                        <div class="col-sm-3">
                          <label>Visits</label><br>
                          <?php
                            if($cur_no_of_reffs + $cur_reff_used == '0' && $InsPriRefVisits==1){
                              $class="form-control mandatory-chk mandatory";
                            } 
                            else{
                              $class="form-control";
                            }
                            if($cur_no_of_reffs + $cur_reff_used=='0'){
                              $value="";
                            }
                            else{
                              $value = $cur_no_of_reffs + $cur_reff_used .'/'.$cur_reff_used;
                            }
                          ?>
                          <input type="hidden" name="<?php echo $s_name; ?>NoRef[]" id="<?php echo $s_name; ?>NoRef<?php echo $request_iterator; ?>" value="<?php echo $cur_no_of_reffs; ?>"/>
                          <input type="hidden" name="<?php echo $s_name; ?>UsedRef[]" id="<?php echo $s_name; ?>UsedRef<?php echo $request_iterator; ?>" value="<?php echo $cur_reff_used; ?>"/>
                          <input type="text"  name="no_ref<?php echo $i_key;?>[]" id="no_ref<?php echo $i_key;?><?php echo $request_iterator; ?>" value="<?php echo stripslashes($value); ?>" size="3" class="form-control" />
                        </div>
                        
                      </div>
                  </div>
                
            				<div class="clearfix"></div>
              
                    <div class="col-sm-12">
                      <div class="row">
                        
                        <div class="col-sm-3">
                          <label>Referral#</label><br>
                          <input type="text" name="reffral_no<?php echo $i_key;?>[]" id="reffral_no<?php echo $i_key;?><?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_reffral_no); ?>" size="11" class="form-control" />
                        </div>
                        
                        <div class="col-sm-3">
                          <label>Ref. Date</label><br>
                          <div class="input-group">
                            <input type="text" name="reff<?php echo $i_key;?>_date[]" id="reff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" value="<?php echo stripslashes($cur_reff_date); ?>" size="11" onBlur="checkdate(this);" maxlength="10" class="form-control datepicker" />
                            <label for="reff<?php echo $i_key;?>_date<?php echo $request_iterator; ?>" class="input-group-addon btn"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                          </div>    
                        </div>
                        
                        <div class="col-sm-6">
                          <label>Notes</label><br>
                          <?php
                            $strNotesRef = "";
                            $strNotesRef = ucwords($cur_note);
                          ?>
                          <textarea style="height:34px;" name="note<?php echo $i_key;?>[]" id="note<?php echo $i_key;?><?php echo $request_iterator; ?>" cols="40" rows="1" class="form-control"><?php echo stripslashes($strNotesRef); ?></textarea>
                        </div>
                        
                      </div>  
                    </div>
             			
                  	<div class="clearfix <?php echo ($request_iterator < $cnt) ? 'border-dashed' : '';?>">&nbsp;</div>
              			</div>      
              			
							<?php 
									} 	
								}
								else
								{
									$btn_class = 'hidden';
									echo '<div class="mt20 alert alert-info">No Referral Exists</div>';
								}
            	?>
						</form>
          </div>
          
            
        </div>
        <footer class="panel-footer" id="exp_reff_opts">
          <input type="submit" value="Save" class="btn btn-success <?php echo $btn_class;?>" onClick="$('#insuranceReff').submit();" /> 
          <input type="button" value="Close" onclick="javascript:window.close();" class="btn btn-danger" />
        </footer>
      </div>
 	</body>
</html>