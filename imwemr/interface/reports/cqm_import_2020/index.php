<?php
include_once(dirname(__FILE__)."/../../../config/globals.php");
$library_path = $GLOBALS['webroot'].'/library';
include_once($GLOBALS['srcdir'].'/classes/common_function.php');
require_once(__DIR__.'/cqm_import.php');

	$task = (isset($_REQUEST['taskVal']) && empty($_REQUEST['taskVal']) == false) ? $_REQUEST['taskVal'] : 'checkZip';
	$cqmObj = New CQIMPORT($task, $_SESSION['authId'], $_REQUEST, $_FILES);
	
	$arrPhpVar = array();
	$arrPhpVar['webRoot'] = $GLOBALS['webroot'];
	$arrPhpVar['uploadPath'] = $cqmObj->dirPath;
	$arrPhpVar['zipName'] = $cqmObj->zipName;
	$arrPhpVar['ajaxUrl'] = $GLOBALS['webroot'].'/interface/reports/cqm_import_2020/ajax.php';
	$arrPhpVar['dateFormat'] = 'm-d-Y';
	$arrPhpVar['rootUrl'] = $GLOBALS['webroot'].'/interface/reports/cqm_import_2020/';
	$arrPhpVar['Status'] = $cqmObj->statusVal;
	$arrPhpVar['Error'] = $cqmObj->errorVal;
	$arrPhpVar['globCounter'] = 0;
	$arrPhpVar['jsButton'] = array();
?>
<!DOCTYPE html>
<html>
	<head>
		<title>imwemr</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery-ui.min.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap.min.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/jquery.datetimepicker.min.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-select.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/bootstrap-colorpicker.css" type="text/css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/messi/messi.css">
		<link rel="stylesheet" href="<?php echo $library_path; ?>/css/common.css" type="text/css">
		<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="<?php echo $GLOBALS['webroot'];?>/library/js/html5shiv.min.js"></script>
			<script src="<?php echo $GLOBALS['webroot'];?>/library/js/respond.min.js"></script>
		<![endif]-->
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.min.1.12.4.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery-ui.min.1.11.2.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/jquery.datetimepicker.full.min.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-typeahead.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/bootstrap-select.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/messi/messi.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/core_main.js"></script>
		<script type="text/javascript" src="<?php echo $library_path; ?>/js/common.js"></script>
		<style>
			.alert{
				padding: 2px;
				margin-bottom: 0px;
			}
			
			.panel-body {
				padding: 5px 15px 5px 15px;
			}
		</style>
	</head>
	<body>
		<div class="container-fluid">
			<input type="hidden" id="zipNameUpload" value="<?php echo $cqmObj->zipName; ?>" name="zipName">
			<div class="whtbox">
				<div class="head">
					<div class="col-sm-4">
						<!-- Title Div -->
						<span class="titleHead"><?php echo $cqmObj->title; ?></span>
					</div>
					
					<div class="col-sm-8">
						<!-- Error Div -->
						<div class="row">
							<div id="alertDiv" class="col-sm-6 alert alert-danger hide text-center">
								<span></span>
							</div>
							<div id="successDiv" class="col-sm-offset-1 col-sm-5 alert alert-success hide text-center">
								<span></span>
							</div>		
						</div>	
					</div>
					<div class="clearfix"></div>
					<?php 
						//Get file if no name is available
						if($cqmObj->zipName === false){ ?>
						<div class="form-group pt10">
							<form action="" method="POST" enctype="multipart/form-data">
								<input type="hidden" id="taskVal" name="taskVal" value="<?php echo $cqmObj->task; ?>">
								<div class="input-group input-file">
									<input id="zipFile" type="file" name="file" class="form-control" placeholder='Choose a file...' data-action="" onChange="checkZip(this);"/>
									<span class="input-group-btn">
										<button class="btn btn-warning btn-reset" type="submit" id="uploadZip" disabled>Upload</button>
									</span>
								</div>	
							</form>
						</div>
					<?php 
						}
					?>	
				</div>
				<div class="tblBg">
					<div class="row">
						<?php 
							//If filename is available
							$arrResult = array();
							if(isset($cqmObj->zipName) && $cqmObj->zipName !== false && empty($cqmObj->zipName) == false){
								$arrUnique = array();
								$arrResult = $cqmObj->getZipContent($cqmObj->dirPath.$cqmObj->zipName);
								if(count($arrResult) > 0){	?>
									<!-- Provider Dropdown -->
									<div class="col-sm-12">
										<div class="pd5">
											<div class="row">
												<div class="col-sm-4">
													<div class="form-group">
														<label for="providerId">Import data for provider : </label>
														<select name="providerId" id="providerId" class="selectpicker" data-width="100%" data-title="Please select">
															<?php 
																$providerArr = $cqmObj->get_provider_ar();
																$optStr = '';
																if(count($providerArr) > 0){
																	foreach($providerArr as $key => $val){
																		$optStr .= '<option value="'.$key.'">'.$val.'</option>';	
																	}
																}
																echo $optStr;
															?>	
														</select>
													</div>
												</div>
												<div class="col-sm-4">
													<div class="form-group">
														<label for="facilityId">Select Facility</label>
														<select name="facilityId" id="facilityId" class="selectpicker" data-width="100%" data-title="Please select">
															<?php 
																$facArr = $cqmObj->get_facility_arr();
																$FacoptStr = '';
																if(count($facArr) > 0){
																	foreach($facArr as $key => $val){
																		$FacoptStr .= '<option value="'.$key.'">'.$val.'</option>';	
																	}
																}
																echo $FacoptStr;
															?>	
														</select>	
													</div>
												</div>
											</div>
											
										</div>
									</div>
									<div class="col-sm-12">
										<div class="pd5">
											<form id="patientDetails" name="patientDetails" method="POST">
												<?php 
													$counter = 0;
													foreach($arrResult as $key => $val){
														$coll_in = ($counter == 0) ? 'in' : '';
														$title = str_replace('__',' ','Patients__Found').'  -  '.count($val); ?>
														<div class="panel group" id="<?php echo $key.$counter ?>_mainParent">
															<div class="panel panel-info">
																<div class="panel-heading">
																	<div class="row">
																		<div class="pull-left" style="width:3%">
																			<div class="checkbox">
																				<input type="checkbox" id="checkAll" checked>
																				<label for="checkAll"></label>	
																			</div>
																		</div>
																		<div style="width:97%">
																			<h4 class="panel-title">
																				<a data-toggle="collapse" data-parent="#<?php echo $key.$counter ?>_mainParent" href="#<?php echo $key.$counter; ?>_main">
																				<?php echo $title; ?></a>
																			</h4>
																		</div>
																	</div>
																</div>	
																
																<div  id="<?php echo $key.$counter; ?>_main" class="panel-collapse collapse <?php echo $coll_in; ?>">
																	<div class="panel-group" id="accordion_<?php echo $key.'__'.$counter; ?>">
																	<?php
																		if(count($val) > 0){
																			$loop = 0;
																			foreach($val as $OBJ){
																				foreach($OBJ as $obj){
																					//Checking Duplicate Patients
																					//Checking Pt unique id exists or not
																					$uniqueRec = 1;
																					$chkUnique = imw_query('SELECT * FROM patient_data where External_MRN_1 = "'.$obj['External_MRN_1'].'" AND id != "'.$obj['ptId'].'"');
																					if(imw_num_rows($chkUnique) > 0){
																						$uniqueRec = 0;
																					}
																					$obj['uniqueRec'] = $uniqueRec;
																					
																					$fName = (isset($obj['fname']) && empty($obj['fname']) == false) ? $obj['fname'] : ''; 
																					$lName = (isset($obj['lname']) && empty($obj['lname']) == false) ? $obj['lname'] : ''; 
																					$checked = (isset($obj['ptId']) && empty($obj['ptId']) == false && isset($obj['uniqueRec']) && empty($obj['uniqueRec']) == false && $obj['uniqueRec'] == 1) ? 'checked' : 'disabled';
																					$blockClass = ($obj['uniqueRec'] == 0) ? 'danger' : 'default';
																					if($obj['uniqueRec'] == 0) $arrUnique[$obj['External_MRN_1']][] = $obj['ptId'];
																					
																					//Creating Selection Block
																					$selBox = $cqmObj->createSelBlock($obj['uniqueRec'], $obj);
																					
																					$ptName = core_name_format($lName, $fName);
																					?>
																					<div class="panel panel-<?php echo $blockClass; ?>" id="pt_<?php echo $obj['ptId']; ?>">
																						<div class="panel-heading pointer">
																							<div class="row">
																								<div style="width:3%" class="pull-left">
																									<div class="checkbox">
																										<input data-filename="<?php echo $obj['fileName']; ?>" data-elem = "<?php echo $obj['ptId']; ?>" type="checkbox" class="chkPtBox" data-nm = "<?php echo $ptName; ?>" id="checkBox_<?php echo $obj['ptId']; ?>" value="<?php echo $obj['ptId']; ?>" <?php echo $checked; ?>>
																										<label for="checkBox_<?php echo $loop; ?>"></label>
																									</div>
																								</div>	
																								<div style="width:97%" class="pull-left" data-toggle="collapse" data-parent="#accordion_<?php echo $key.'__'.$counter; ?>" href="#<?php echo $key; ?>_collapse_<?php echo $loop; ?>" style="vertical-align:sub;">
																									<div class="row">
																										<div class="col-sm-3">
																											<h4 class="panel-title">
																												<a data-toggle="tooltip" data-placement="top" title="Click for Patient Details">
																													<?php echo $ptName; ?>
																												</a>
																											</h4>
																										</div>
																										<?php if($obj['uniqueRec'] == 0){ ?>
																											<div class="col-sm-9">
																												<div class="row">
																													<div class="col-sm-9 text-center same_doc_id">
																														<span>Same Patient Role Id Found - <?php echo $obj['External_MRN_1'] ?></span>
																													</div>
																													<div class="col-sm-3 text-right">
																														<?php echo $selBox; ?>
																													</div>	
																												</div>
																											</div>
																										<?php } ?>
																									</div>
																								</div>	
																							</div>
																						</div>
																						<div id="<?php echo $key; ?>_collapse_<?php echo $loop; ?>" class="panel-collapse collapse">
																							<div class="panel-body">
																								<div class="row">
																									<div class="pd5 col-sm-12 btn-group btn-group-md importStatus" data-id="<?php echo $obj['ptId']; ?>">
																										<?php 
																											if(isset($obj['ptId']) && empty($obj['ptId']) == false){
																												echo ' <button type="button" class="btn btn-success">Pt. Created</button>';
																											}
																										?>	
																									</div>
																									<div class="clearfix"></div>
																									<?php 
																										$divStr = '';
																										$ptValues = array();
																										/*
																										$fieldPatinet = str_ireplace('id','ptId',implode(',',array_keys($cqmObj->ptXmlMapArr)));
																										
																										 $ptQry = imw_query('SELECT '.$fieldPatinet.' FROM patient_data where id = "'.$obj['ptId'].'"');
																										if(imw_num_rows($ptQry) > 0){
																											$ptValues = imw_fetch_assoc($ptQry);
																										} */
																										foreach($cqmObj->ptXmlMapArr as $key => $title){
																											if(isset($obj[$key]) && empty($obj[$key]) == false){
																												$divStr .= '
																													<div class="col-sm-2">
																														<div class="form-group">
																															<label for="">'.$title.'</label>
																															<input type="text" class="form-control" value="'.$obj[$key].'" readonly>	
																														</div>
																													</div>
																												';
																											}
																										}
																										echo $divStr;
																									?>
																								</div>
																							</div>
																						</div>
																					</div>
																				<?php
																					$loop++;
																				}
																				
																			}
																		}
																	?>
																	</div>
																</div>	
															</div>	
														</div>
														<?php
														$counter++;
													}	
												?>
											</form>
										</div>	
									</div>	
						<?php	}else{	?>
									<div class="alert alert-danger ">
										<strong>Warning !</strong>	<span>No xml found to read in the uploaded file.</span>
									</div>
						<?php	}
								//If zip file name is there than show import button
								$arrPhpVar['jsButton'] = array(array('import_pt', 'Import Patients', 'top.fmain.checkPatients();'), array('reset_import', 'New Import', 'top.fmain.resetImport();'));	
								
								//If unique array has something dont let the import happen
								$arrPhpVar['arrUnique'] = $arrUnique;
							}	
						?>
					</div>
				</div>
			</div>	
		</div>
		<script>
			set_header_title('CQM Import');
			var phpArr = <?php echo json_encode($arrPhpVar) ?>;
		</script>
		<script src="./cqm_import.js" type="text/javascript"></script>
	</body>
</html>