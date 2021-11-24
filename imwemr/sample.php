<?php 
	include_once('config/globals.php');
	include_once('library/classes/common_function.php');
?>
<!DOCTYPE html>
	<html>
		<head>
			<meta charset="UTF-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, maximum-scale=1.0" />
			<title>sample</title>
			<link href="library/css/bootstrap.min.css" type="text/css" rel="stylesheet">
			<link href="library/css/bootstrap-select.css" type="text/css" rel="stylesheet">
			<link href="library/css/common.css" type="text/css" rel="stylesheet">
			<script src="library/js/jquery.min.1.12.4.js"></script>
			<script src="library/js/common.js"></script>
			<script src="library/js/bootstrap.min.js"></script>
			<script src="library/js/bootstrap-select.js"></script>
		</head>
	<body>
<style type="text/css">
/**** Page Css ****/
body{background-color:#fff;font-family: sans-serif;}
legend{font-size:15px;margin-bottom:10px;font-weight:bold;border-bottom:1px solid #cccccc}
.tooltip-inner {max-width: none;white-space: nowrap;}
.container{width:100%;}
.page-title .title {font-size: 2em;}
.page-title {
	font-family: 'Roboto Condensed', sans-serif;
    margin-left: -10px;
    margin-right: -10px;
    padding: 15px 10px;
    margin-bottom: 0px;
    height: auto;
}
.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #cccccc;
}


</style>	
	<div class="container">
		<div class="page-title">
			<span class="title">Form UI Elements</span>
			<div class="description">A ui elements use in form, input, select, etc.</div>
		</div>
		<div class="panel-group">
		<!-- Input Text Field -->
			<div class="panel panel-primary">
				<div class="panel-heading">Input</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-6">
							<fieldset>
								<legend>
									Basic Input
								</legend>
								<div class="row">
									<form class="col-sm-12">
                                        <!-- Block for input field -->
										<div class="form-group">
                                            <label for="exampleInputName">Name</label>
                                            <input type="text" class="form-control" id="exampleInputName2" placeholder="Jane Doe">
                                        </div>
									</form>	
								</div>
							</fieldset>		
						</div>
						<div class="col-sm-6">
							<fieldset>
								<legend data-toggle="tooltip" data-placement="top" data-html="true" title="<p class='text-left'>'form-inline' class can be given to parent to place every child group inline or can be given to single group</p>">
									Inline Input [ Hover for more info. ]
								</legend>
								<div class="row">
									<div class="col-sm-12" style="height:25px"></div>
									<div class="col-sm-12">
										<form class="form-inline">
											<!-- Block for input field -->
											<div class="form-group col-sm-6">
												<label for="exampleInputName">First Name</label>
												<input type="text" class="form-control" id="exampleInputName2" placeholder="Jane">
											</div>
											<div class="form-group col-sm-6">
												<label for="exampleInputName">Last Name</label>
												<input type="text" class="form-control" id="exampleInputName2" placeholder="Doe">
											</div>	
										</form>	
									</div>	
								</div>
							</fieldset>		
						</div>	
					</div>
				</div>
			</div>
			
		<!-- Textarea -->
			<div class="panel panel-primary">
				<div class="panel-heading">Textarea</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<fieldset>
								<!-- Block for textarea -->
								<div>
									<textarea class="form-control" rows="3"></textarea>
								</div>
							</fieldset>		
						</div>
					</div>
				</div>
			</div>	
			
		<!-- Inline Form -->
			<div class="panel panel-primary">
				<div class="panel-heading">Inline Form</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<fieldset>
								<!-- Block for Inline form -->
								<div class="row">
									<form class=" form-inline">
                                        <div class="form-group col-sm-3">
                                            <label for="exampleInputName2">First Name</label>
                                            <input type="text" class="form-control" id="exampleInputName2" placeholder="Jane">
                                        </div>
										<div class="form-group col-sm-3">
                                            <label for="exampleInputName2">Last Name</label>
                                            <input type="text" class="form-control" id="exampleInputName2" placeholder="Doe">
                                        </div>	
                                        <div class="form-group col-sm-3">
                                            <label for="exampleInputEmail2">Email</label>
                                            <input type="email" class="form-control" id="exampleInputEmail2" placeholder="jane.doe@example.com">
                                        </div>
										<div class="form-group col-sm-3">
                                            <label for="exampleInputpassword2">Password</label>
                                            <input type="password" class="form-control" id="exampleInputpassword2" placeholder="Password">
                                        </div>	
										<div class="clearfix"></div>
										<div class="col-sm-12 text-center" style="padding-top:1%">
											 <button type="button" class="btn btn-success">Send</button>
										</div>
                                    </form>
								</div>
							</fieldset>		
						</div>
					</div>
				</div>
			</div>	
		
		<!-- Horizontal Form -->
			<div class="panel panel-primary">
				<div class="panel-heading">Horizontal Form</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<fieldset>
								<!-- Block for Horizontal form -->
								<div class="row">
									<form class="form-horizontal">
                                        <div class="form-group">
                                            <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                                            <div class="col-sm-6">
                                                <input type="email" class="form-control" id="inputEmail3" placeholder="Email">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
                                            <div class="col-sm-6">
                                                <input type="password" class="form-control" id="inputPassword3" placeholder="Password">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-6">
												<div class="checkbox">
													<input id="checkbox_form" type="checkbox" checked>
													<label for="checkbox_form">
														 Remember me
													</label>
												</div>	
											</div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <button type="button" class="btn btn-success">Sign in</button>
                                            </div>
                                        </div>
                                    </form>
								</div>
							</fieldset>		
						</div>
					</div>
				</div>
			</div>		
		
		<!-- Select Drop Down -->
			<div class="panel panel-primary">
				<div class="panel-heading">Select</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-4">
							<fieldset>
								<legend>
									Basic
								</legend>
								<div class="row">
									<!-- Block for select drop down -->
									<div class="col-sm-12">
										<select class="form-control">
											<option value="CA">California</option>
											<option value="NV">Nevada</option>
											<option value="OR">Oregon</option>
											<option value="WA">Washington</option>
										</select>
									</div>	
								</div>
							</fieldset>		
						</div>
						<div class="col-sm-4">
							<fieldset>
								<legend>
									With Opt Group
								</legend>
								<!-- Block for select with OptGroup -->
								<div class="row">
									<div class="col-sm-12">
										<select class="form-control">
											<optgroup label="Pacific Time Zone">
												<option value="CA">California</option>
												<option value="NV">Nevada</option>
												<option value="OR">Oregon</option>
												<option value="WA">Washington</option>
											</optgroup>
											<optgroup label="Mountain Time Zone">
												<option value="AZ">Arizona</option>
												<option value="CO">Colorado</option>
												<option value="ID">Idaho</option>
												<option value="MT">Montana</option>
												<option value="NE">Nebraska</option>
												<option value="NM">New Mexico</option>
												<option value="ND">North Dakota</option>
												<option value="UT">Utah</option>
												<option value="WY">Wyoming</option>
											</optgroup>
										</select>	
									</div>
								</div>
							</fieldset>
						</div>	
						<div class="col-sm-4">
							<fieldset>
								<legend data-toggle="tooltip" data-placement="bottom" data-html="true" title="More examples at : https://silviomoreto.github.io/bootstrap-select/examples/">
									With selectpicker(multi-select) [ Hover over me ]
								</legend>
								<div class="row">
									<!-- Block for selectpicker [multiple] -->
									<div class="col-sm-12">
										<select class="selectpicker show-menu-arrow" data-live-search="true" data-width="100%" data-actions-box="true" multiple>
											<option>Mustard</option>
											<option>Ketchup</option>
											<option>Relish</option>
											<option>Mustard</option>
											<option>Ketchup</option>
											<option>Relish</option>	
										</select>
									</div>
								</div>
							</fieldset>
						</div>		
					</div>
				</div>
			</div>
		<!-- Multi Level Dropdown [ Simple Menu ] -->
			<div class="panel panel-primary">
				<div class="panel-heading">Multi Level Dropdown [ Simple Menu ]</div>
				<div class="panel-body">
					<div class="row">
						
						<div class="col-sm-6">
							<?php
								$arrTos = array();
								$rez = "SELECT lname, mname, fname, id FROM users WHERE user_type='1' AND delete_status = '0' ORDER BY lname,fname";
								$sql_qry = imw_query($rez);
								for($i=1;$row=imw_fetch_array($sql_qry);$i++)
								{
									$mnameTemp = ($row["mname"] != NULL) ? $row["mname"]."" : "";
									$phyNameTemp = $row["lname"].", ".$row["fname"]." ".$mnameTemp;
									$arrPhysiciansMenu[] = array($phyNameTemp,$arrEmpty,$phyNameTemp);
								} 
							?>
							<fieldset>
								<legend>
									Single Level
								</legend>
								<!-- Block for Single-Level Dropdown -->
								<div class="input-group">
									<input type="text" name="text_field_to_append_val" id="text_field_to_append_val" class="text form-control">
									<?php  echo get_simple_menu($arrPhysiciansMenu,"menu_id","text_field_to_append_val");?>
								</div>	
							</fieldset>
						</div>		
						<div class="col-sm-6">
							<?php
								$arrTos = array();
								$rezTosCat = imw_query("Select * FROM tos_category_tbl ORDER BY tos_category");
								for($i=1;$rowCat=imw_fetch_array($rezTosCat);$i++)
								{
									$arrTosTemp = array();
									
									$rez = imw_query("SELECT * FROM tos_tbl WHERE tos_cat_id='".$rowCat["tos_cat_id"]."'");
									for($j=1;$row=imw_fetch_array($rez);$j++)
									{
										$arrTosTemp[] = array($row["tos_prac_cod"]."-".$row["tos_description"],$arrEmpty,$row["tos_prac_cod"]);
										$arrTos4TypeAhead[] = addslashes($row["tos_prac_cod"]);			
										if(strtoupper($tosPracCodeDefault) == strtoupper($row["tos_description"]))
										{				
											$tosPracCodeDefault = $row["tos_prac_cod"];
										}		
									}		
									if(count($arrTosTemp) > 0)
									{
										$arrTos[] = array($rowCat["tos_category"],$arrTosTemp);			
									}	
								} 
							?>
							<fieldset>
								<legend>
									Multi Level
								</legend>
								<!-- Block for Multi-Level Dropdown -->
								<div class="input-group">
									<input type="text" name="text_field_to_append_val_2" id="text_field_to_append_val_2" class="text form-control">
									<?php  echo get_simple_menu($arrTos,"menu_id_2","text_field_to_append_val_2");?>
								</div>	
							</fieldset>
						</div>		
					</div>
				</div>
			</div>	
		
		<!-- Checkboxes -->
			<div class="panel panel-primary">
				<div class="panel-heading">Checkboxes</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-6">
							<fieldset>
								<legend>
									Basic
								</legend>
								<!-- Block for normal checkbox -->
								<div class="checkbox">
									<input id="checkbox" type="checkbox" checked>
									<label for="checkbox">
										I m a checkbox [ Default ]
									</label>
								</div>
								<div class="checkbox checkbox-default">
									<input id="checkbox1" type="checkbox" checked>
									<label for="checkbox1">
										I m a checkbox [ Black ]
									</label>
								</div>	
							</fieldset>		
						</div>
						<div class="col-sm-6">
							<fieldset>
								<legend>
									Inline
								</legend>
								<!-- Block for inline checkbox -->
								<div class="checkbox checkbox-inline">
									<input type="checkbox" id="inlineCheckbox1" value="option1">
									<label for="inlineCheckbox1"> Click here </label>
								</div>
								<div class="checkbox checkbox-default checkbox-inline">
									<input type="checkbox" id="inlineCheckbox2" value="option1" checked="">
									<label for="inlineCheckbox2"> Click here </label>
								</div>
								<div class="checkbox checkbox-inline">
									<input type="checkbox" id="inlineCheckbox3" value="option1">
									<label for="inlineCheckbox3"> Click here </label>
								</div>
								<div class="checkbox checkbox-inline">
									<input type="checkbox" id="inlineCheckboxDisabled" value="option1" disabled>
									<label for="inlineCheckboxDisabled"> Disabled </label>
								</div>	
							</fieldset>
						</div>	
					</div>
				</div>
			</div>
			
		<!-- Radio Boxes -->
			<div class="panel panel-primary">
				<div class="panel-heading">Radio Buttons</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-6">
							<fieldset>
								<legend>
									Basic
								</legend>
								<!-- Block for radio buttons -->
								<div class="radio">
									<input type="radio" name="radio1" id="radio1" value="option1" checked="">
									<label for="radio1">
										Select Me [ Default ]
									</label>
								</div>
								<div class="radio radio-default">
									<input type="radio" name="radio1" id="radio2" value="option2">
									<label for="radio2">
										or Me [ Black ]
									</label>
								</div>
							</fieldset>		
						</div>
						<div class="col-sm-6">
							<fieldset>
								<legend>
									Inline
								</legend>
								<!-- Block for inline radio buttons -->
								 <div class="radio radio-inline">
									<input type="radio" id="inlineRadio1" value="option1" name="radioInline" checked="">
									<label for="inlineRadio1"> Inline One </label>
								</div>
								<div class="radio radio-inline radio-default">
									<input type="radio" id="inlineRadio2" value="option1" name="radioInline">
									<label for="inlineRadio2"> Inline Two </label>
								</div>
								<div class="radio radio-inline radio-default">
									<input type="radio" id="inlineRadioDisabled" value="option1" name="radioInline" disabled>
									<label for="inlineRadioDisabled"> I m disabled </label>
								</div>	
							</fieldset>
						</div>	
					</div>
				</div>
			</div>
			
		<!-- Modal -->
			<div class="panel panel-primary">
				<div class="panel-heading">Modal Box</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<fieldset>
								<!-- Block for textarea -->
								<div class="row">
									<div class="col-sm-12 text-center">
										<button class="btn btn-primary" onclick="show_modal('Modal_id','Demo Modal','Hello, I m a Modal Box')">Click to lauch <kbd>Modal Box</kbd></button>
									</div>
								</div>
							</fieldset>		
						</div>
					</div>
				</div>
			</div>

		<!-- Modal -->
			<div class="panel panel-primary">
				<div class="panel-heading">Multi level menu</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<style>
							.dropdown-menu{overflow:visible;}
							.dropdown-submenu {
							    position: relative;
							}

							.dropdown-submenu .dropdown-menu {
							    top: 0;
							    left: 100%;
							    margin-top: -1px;
							}
							</style>
							<div class="dropdown">
							    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">Tutorials
							    <span class="caret"></span></button>
							    <ul class="dropdown-menu">
							      <li><a tabindex="-1" href="#">HTML</a></li>
							      <li><a tabindex="-1" href="#">CSS</a></li>
							      <li class="dropdown-submenu">
								<a class="test" tabindex="-1" href="#">New dropdown <span class="caret"></span></a>
								<ul class="dropdown-menu">
								  <li><a tabindex="-1" href="#">2nd level dropdown</a></li>
								  <li><a tabindex="-1" href="#">2nd level dropdown</a></li>
								  <li class="dropdown-submenu">
								    <a class="test" href="#">Another dropdown <span class="caret"></span></a>
								    <ul class="dropdown-menu">
								      <li><a href="#">3rd level dropdown</a></li>
								      <li><a href="#">3rd level dropdown</a></li>
								    </ul>
								  </li>
								</ul>
							      </li>
							    </ul>
							  </div>								
						</div>
					</div>
					
					<div class="row">
						<div class="col-sm-12">
							<div class="input-group">
							<label class="input-group-btn dropdown_toggle_trigger open">
								<span id="" class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="true"><span class="caret"></span></span>
								<ul class="dropdown-menu menu_id_2" id="" style="">
									<li class="dropdown-submenu">
										<a class="test" href="#" >
											<label>Paid</label>
											<span class="glyphicon glyphicon-chevron-right pull-right"></span>
										</a>
										<ul class="dropdown-menu menu_id_3 " id="" style="">
											<li class="lead">
												<a class="" href="javascript:void(0);" >Cash</a>
												<input type="hidden" name="menuOptionValue" id="menuOptionValue" value="Paid - Cash" autocomplete="off">
											</li>
											<li class="dropdown-submenu lead">
												<a class="test" href="javascript:void(0);" >
													<label>Credit Card</label>
													<span class="glyphicon glyphicon-chevron-right pull-right"></span>
												</a>
												<ul class="dropdown-menu menu_id_7" id="" style="">
													<li class="lead">
														<a class="" href="javascript:void(0);" >American Express</a>
														<input type="hidden" name="menuOptionValue" id="menuOptionValue" value="Paid - Credit Card - American Express" autocomplete="off">
													</li>
												</ul>
											</li>
										</ul>
									</li>
								</ul>
							</label>
							</div>
						</div>
					</div>

					<script>
						$(document).ready(function(){
						  $('.dropdown-submenu a.test').on("click", function(e){						  
						    $(this).next('ul').toggle();
						    e.stopPropagation();
						    e.preventDefault();
						  });
						});
					</script>
				</div>
			</div>

		<!-- Table -->
			<div class="panel panel-primary">
				<div class="panel-heading">Table</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-sm-12">
							<fieldset>
								<!-- Block for textarea -->
								<div class="row">
									<div class="col-sm-6">
										<fieldset>
											<legend class="text-left">Basic Table</legend>
											<table class="table">
												<thead>
												  <tr>
													<th>Firstname</th>
													<th>Lastname</th>
													<th>Email</th>
												  </tr>
												</thead>
												<tbody>
												  <tr>
													<td>John</td>
													<td>Doe</td>
													<td>john@example.com</td>
												  </tr>
												  <tr>
													<td>Mary</td>
													<td>Moe</td>
													<td>mary@example.com</td>
												  </tr>
												  <tr>
													<td>July</td>
													<td>Dooley</td>
													<td>july@example.com</td>
												  </tr>
												</tbody>
											</table>
										</fieldset>
									</div>
									<div class="col-sm-6">
										<fieldset>
											<legend class="text-left">Stiped Table [ add zebra-stripes to a table ]</legend>
											<table class="table table-striped">
												<thead>
												  <tr>
													<th>Firstname</th>
													<th>Lastname</th>
													<th>Email</th>
												  </tr>
												</thead>
												<tbody>
												  <tr>
													<td>John</td>
													<td>Doe</td>
													<td>john@example.com</td>
												  </tr>
												  <tr>
													<td>Mary</td>
													<td>Moe</td>
													<td>mary@example.com</td>
												  </tr>
												  <tr>
													<td>July</td>
													<td>Dooley</td>
													<td>july@example.com</td>
												  </tr>
												</tbody>
											</table>
										</fieldset>
									</div>
									<div class="clearfix"></div>
									<div class="col-sm-6">
										<fieldset>
											<legend class="text-left">Bordered Table [ add borders on all sides of the table and cells ]</legend>
											<table class="table table-bordered">
												<thead>
												  <tr>
													<th>Firstname</th>
													<th>Lastname</th>
													<th>Email</th>
												  </tr>
												</thead>
												<tbody>
												  <tr>
													<td>John</td>
													<td>Doe</td>
													<td>john@example.com</td>
												  </tr>
												  <tr>
													<td>Mary</td>
													<td>Moe</td>
													<td>mary@example.com</td>
												  </tr>
												  <tr>
													<td>July</td>
													<td>Dooley</td>
													<td>july@example.com</td>
												  </tr>
												</tbody>
											</table>
										</fieldset>
									</div>	
									<div class="col-sm-6">
										<fieldset>
											<legend class="text-left">Hover Rows [ add a hover effect on table rows ]</legend>
											<table class="table table-hover">
												<thead>
												  <tr>
													<th>Firstname</th>
													<th>Lastname</th>
													<th>Email</th>
												  </tr>
												</thead>
												<tbody>
												  <tr>
													<td>John</td>
													<td>Doe</td>
													<td>john@example.com</td>
												  </tr>
												  <tr>
													<td>Mary</td>
													<td>Moe</td>
													<td>mary@example.com</td>
												  </tr>
												  <tr>
													<td>July</td>
													<td>Dooley</td>
													<td>july@example.com</td>
												  </tr>
												</tbody>
											</table>
										</fieldset>
									</div>	
									<div class="clearfix"></div>
									<div class="col-sm-6">
										<fieldset>
											<legend class="text-left">Condensed Table [ makes a table more compact by cutting cell padding in half ]</legend>
											<table class="table table-condensed">
												<thead>
												  <tr>
													<th>Firstname</th>
													<th>Lastname</th>
													<th>Email</th>
												  </tr>
												</thead>
												<tbody>
												  <tr>
													<td>John</td>
													<td>Doe</td>
													<td>john@example.com</td>
												  </tr>
												  <tr>
													<td>Mary</td>
													<td>Moe</td>
													<td>mary@example.com</td>
												  </tr>
												  <tr>
													<td>July</td>
													<td>Dooley</td>
													<td>july@example.com</td>
												  </tr>
												</tbody>
											</table>
										</fieldset>
									</div>	

									<div class="col-sm-6">
										<fieldset>
											<legend class="text-left">Responsive Table [ creates a responsive table ]</legend>
											<div class="responsive">
												<table class="table table-condensed">
													<thead>
													  <tr>
														<th>Firstname</th>
														<th>Lastname</th>
														<th>Email</th>
													  </tr>
													</thead>
													<tbody>
													  <tr>
														<td>John</td>
														<td>Doe</td>
														<td>john@example.com</td>
													  </tr>
													  <tr>
														<td>Mary</td>
														<td>Moe</td>
														<td>mary@example.com</td>
													  </tr>
													  <tr>
														<td>July</td>
														<td>Dooley</td>
														<td>july@example.com</td>
													  </tr>
													</tbody>
												</table>
											</div>
										</fieldset>
									</div>		
								</div>
							</fieldset>		
						</div>
					</div>
				</div>
			</div>
				
				
		</div>
	</div>

</body>
<script>
	$(document).ready(function(){
	
		//Init. selectpicker
		$('.selectpicker').selectpicker();
		//Init. tooltip
		$("[data-toggle='tooltip']").tooltip();
	});
</script>
</html>