
<div class="reportlft" style="height:100%;">
    <div class="practbox">
        <div class="anatreport"><h2>Practice Filter</h2></div>
        <div class="clearfix"></div>
        <div class="pd5" id="searchcriteria">
<!--            <div class="row">
                <div class="col-sm-6">
                    <div id="show_saved_report_type" style="width:300px;display:none;position:absolute;margin-top:22px;height:<?php //echo $_SESSION['wn_height']-470;?>px;" class="border bg1"></div>
                    <a href="javascript:void(0);" onClick="javascript:show_saved_report_types();" class="text_10b_purpule text12b">Saved Reports</a>	&nbsp;<div id="show_saved_report_type1"></div>
                </div>
                <div class="col-sm-6">
                    <span class="alignRight" id="saved_report_types"></span>
                </div>
            </div>-->
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <label for="dxcodes10">ICD10 Codes</label>
                    <select id="dxcodes10" name="dxcodes10[]" data-container="#select_drop" class="selectpicker form-control" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-live-search="true" >
                        <?php echo $all_dx10_code_options; ?>	
                    </select>
                </div>
                <div class="col-sm-8 mt2"><br />
                    <div class="pdl_10">
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_problem_list_inc" name="rd_problem_list" value="1" checked <?php echo ($rd_problem_list == "1") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_problem_list_inc">Include</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_problem_list_exc" name="rd_problem_list" value="0" <?php echo ($rd_problem_list == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_problem_list_exc">Exclude</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <!--<label for="cptcodes">CPT Codes</label>
                    <select id="cptcodes" name="cptcodes[]" data-container="#select_drop" class="selectpicker form-control" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-live-search="true" >
                        <?php echo $all_dx10_code_options; ?>	
                    </select>-->
                    <label for="cpt">CPT Code</label>
                    <select name="proc_codes[]" id="proc_codes" data-container="#select_drop" class="selectpicker form-control" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-live-search="true">
                        <?php echo $cpt_code_options; ?>
                    </select>
                    
                </div>
                <div class="col-sm-8 mt2"><br />
                    <div class="pdl_10">
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_cpt_list_inc" name="rd_cpt_list" value="1" checked <?php echo ($rd_cpt_list == "1") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_cpt_list_inc">Include</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_cpt_list_exc" name="rd_cpt_list" value="0" <?php echo ($rd_cpt_list == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_cpt_list_exc">Exclude</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <label for="medications">Medications</label>
                    <select name="medications[]" id="medications" data-container="#select_drop" class="selectpicker form-control" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All" data-live-search="true">
                        <?php echo $medication_options; ?>	
                    </select>
                </div>
                <div class="col-sm-3">
                    <label for="lot_number">Lot#</label>
                    <input type="text" id="lot_number" name="lot_number" class="form-control" value="<?php echo $lot_number; ?>" >
                </div>
                <div class="col-sm-5 mt2"><br />
                    <div class="">
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_med_list_inc" name="rd_med_list" value="1" checked <?php echo ($rd_med_list == "1") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_med_list_inc" style="padding-left:2px;">Include</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_med_list_exc" name="rd_med_list" value="0" <?php echo ($rd_med_list == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_med_list_exc" style="padding-left:2px;">Exclude</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <label for="medication_allergy">Medication Allergy</label>
                    <input type="text" id="medication_allergy" name="medication_allergy" class="form-control" data-seperator="," data-provide="multiple" value="<?php echo $medication_allergy; ?>" >
                </div>
                <div class="col-sm-8 mt2"><br />
                    <div class="pdl_10">
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_med_allergy_inc" name="rd_med_allergy" value="1" checked <?php echo ($rd_med_allergy == "1") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_med_allergy_inc">Include</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_med_allergy_exc" name="rd_med_allergy" value="0" <?php echo ($rd_med_allergy == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_med_allergy_exc">Exclude</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-3">
                    <label for="weight_criteria">Weight (kg)</label>
                    <div class="form-inline">
                        <select name="weight_criteria" id="weight_criteria" class="form-control minimal">
                            <option value="equalsto" <?php echo ($weight_criteria == "equalsto") ? 'selected': ''; ?>> = </option>
                            <option value="less" <?php echo ($weight_criteria == "less") ? 'selected': ''; ?>>&lt;</option>
                            <option value="greater" <?php echo ($weight_criteria == "greater") ? 'selected': ''; ?>>&gt;</option>	
                        </select>
                        <input type="text" style="width:42px;"  id="weight_val" name="weight_val" value="<?php echo ($weight_val) ? $weight_val : '';?>" class="form-control"  />
                    </div>
                </div>
                <div class="col-sm-3">
                    <label for="height_criteria">Height (m)</label>
                    <div class="form-inline">
                        <select id="height_criteria" name="height_criteria" class="form-control minimal">
                            <option value="equalsto" <?php echo ($height_criteria == "equalsto") ? 'selected': ''; ?>> = </option>
                            <option value="less" <?php echo ($height_criteria == "less") ? 'selected': ''; ?>>&lt;</option>
                            <option value="greater" <?php echo ($height_criteria == "greater") ? 'selected': ''; ?>>&gt;</option>
                        </select>
                        <input type="text" style="width:42px;" id="height_val" name="height_val" value="<?php echo ($height_val) ? $height_val : '';?>" class="form-control"  />
                    </div>
                </div>
                <div class="col-sm-6 mt2"><br />
                    <div class="pdl_10">
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_ht_wt_inc" name="rd_ht_wt" value="1" checked <?php echo ($rd_ht_wt == "1") ? 'checked': ''; ?>  class="form-control">
                            <label for="rd_ht_wt_inc">Include</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_ht_wt_exc" name="rd_ht_wt" value="0" <?php echo ($rd_ht_wt == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_ht_wt_exc">Exclude</label>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <label for="">Immunizations</label>
                    <textarea id="immunizations" name="immunizations" rows="2" ><?php //echo implode(',',$medications); ?></textarea>
                </div>
                <div class="col-sm-6 mt2"><br />
                    <div class="pdl_10">
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_imm_list_inc" name="rd_imm_list" value="1" checked <?php echo ($rd_imm_list == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_imm_list_inc">Include</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_imm_list_exc" name="rd_imm_list" value="0" <?php echo ($rd_imm_list == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_imm_list_exc">Exclude</label>
                        </div>
                    </div>
                </div>
            </div>

                <div class="row">
                    <div class="clearfix"></div>
                    <div class="col-sm-12"><label><strong>Laboratory</strong></label></div>
                    <div class="clearfix"></div>
                    <div class="col-sm-5">
                        <label for="">Observation</label>
                    </div>
                    <div class="col-sm-3">
                        <label for="">Range From</label>
                    </div>
                    <div class="col-sm-3">
                        <label for="">Range To</label>
                    </div>
                    <div class="col-sm-1">&nbsp;</div>

                    <div class="clearfix"></div>
                    <div class="col-sm-12" id="table_lab" style="clear:both; height:55px; overflow-y:scroll">
                        <?php echo $laboratory_section; ?>
                    </div>
                    <input type="hidden" name="totLabRows" id="totLabRows" value="<?php echo $totLabRows; ?>">
                </div>

            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <label for="">Diabetic Exam</label>
                    <select name="diabetic_exam" id="diabetic_exam" class="form-control selectpicker" data-width="100%" data-size="10" data-actions-box="true" data-title="Select">
                        <option value=""></option>
                        <option value="all" <?php echo ($diabetic_exam == "all") ? 'selected': ''; ?>>All</option>
                        <option value="green" <?php echo ($diabetic_exam == "green") ? 'selected': ''; ?>>Green</option>
                        <option value="red" <?php echo ($diabetic_exam == "red") ? 'selected': ''; ?>>Red</option>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="">Physicians</label>
                    <select name="physicians[]" id="physicians" data-container="#select_drop" class="selectpicker form-control" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                        <?php echo $physiciansOption; ?>
                    </select>
                </div>
                <div class="col-sm-4">
                    <label for="">C:D Ratio</label>
                    <input type="text" id="cdRatio" name="cdRatio" value="<?php echo ($cdRatio)?$cdRatio:''; ?>" style="width:120px;"  class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <label for="">IOP Pressure</label>
                    <div class="form-inline">
                        <select id="iop_criteria" name="iop_criteria" class="form-control minimal">
                            <option value="equalsto" <?php echo ($iop_criteria == "equalsto") ? 'selected': ''; ?>> = </option>
                            <option value="less" <?php echo ($iop_criteria == "less") ? 'selected': ''; ?>>&lt;</option>
                            <option value="greater" <?php echo ($iop_criteria == "greater") ? 'selected': ''; ?>>&gt;</option>	
                        </select>
                        <input type="text" id="iopPressure"  name="iopPressure" value="<?php echo $iopPressure; ?>" style="width:80px;" class="form-control">
                        <input type="hidden" name="rd_lab_results" id="rd_lab_results" value="1">
                    </div>
                </div>
                <div class="col-sm-4">
                    <label for="">Keyword</label>
                    <input type="text" id="searchString" name="searchString" value="<?php echo $searchString; ?>" class="form-control" />
                </div>
                <div class="col-sm-4">
                    <div class="checkbox checkbox-inline pointer"><br />
                        <input type="checkbox" id="mrGiven" name="mrGiven" <?php echo ($mrGiven == "on") ? 'checked': ''; ?>/> 
                        <label for="mrGiven">MR Given</label>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="appointflt">
        <div class="anatreport"><h2>Analytic Filter</h2></div>
        <div class="clearfix"></div>
        <div class="pd5" id="searchcriteria">
            <div class="row">
                <div class="col-sm-12">
                    <label>Period</label>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <input type="text" name="eff_date" placeholder="From" style="font-size: 12px;" id="eff_date" value="<?php echo $_REQUEST['eff_date']; ?>" class="form-control date-pick">
                                <label class="input-group-addon" for="eff_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                            </div>
                        </div>	
                        <div class="col-sm-6">	
                            <div class="input-group">
                                <input type="text" name="eff_date2" placeholder="To" style="font-size: 12px;" id="eff_date2" value="<?php echo $_REQUEST['eff_date2']; ?>" class="form-control date-pick">
                                <label class="input-group-addon" for="eff_date2"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div> 
            <div class="row">
                <div class="col-sm-6">
                    <label for="hourFrom">Entered Time From</label>
                    <div class="form-inline">
                        <select name="enHourFrom" id="enHourFrom" style="width:42px" class="form-control minimal">
                            <option value=""></option>
                            <?php echo $CLSReports->timeNumbers(12, $enHourFrom); ?>
                        </select><strong>:</strong>
                        <select name="enMinFrom" id="enMinFrom" style="width:42px" class="form-control minimal">
                            <?php echo $CLSReports->timeNumbers(59, $enMinFrom); ?>
                        </select>
                        <select name="enAmpmFrom" id="enAmpmFrom" style="width:50px;" class="form-control minimal">
                            <option value="AM" <?php echo ($enAmpmFrom == "AM") ? 'selected': ''; ?>>AM</option>
                            <option value="PM" <?php echo ($enAmpmFrom == "PM") ? 'selected': ''; ?>>PM</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="hourTo">Entered Time To</label>
                    <div class="form-inline">
                        <select name="enHourTo" id="enHourTo" style="width:42px" class="form-control minimal">
                            <option value=""></option>
                            <?php echo $CLSReports->timeNumbers(12, $enHourTo); ?>
                        </select><strong>:</strong>
                        <select name="enMinTo" id="enMinTo" style="width:42px" class="form-control minimal">
                            <?php echo $CLSReports->timeNumbers(59, $enMinTo); ?>
                        </select>
                        <select name="enAmpmTo" id="enAmpmTo" style="width:50px" class="form-control minimal">
                            <option value="AM" <?php echo ($enAmpmTo == "AM") ? 'selected': ''; ?>>AM</option>
                            <option value="PM" <?php echo ($enAmpmTo == "AM") ? 'selected': ''; ?>>PM</option>
                        </select>	
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-5">
                    <div class="checkbox checkbox-inline pointer"><br />
                        <input type="checkbox" id="hippa_compliant" name="hippa_compliant" <?php echo ($hippa_compliant == "on") ? 'checked': ''; ?>/> 
                        <label for="hippa_compliant">HIPAA Compliant</label>
                    </div>
                </div>
                <div class="col-sm-3">
                    <label for="">AND/OR</label>
                    <select name="sel_and_or" id="sel_and_or" class="form-control minimal">
                        <option value="A" <?php echo ($sel_and_or == "A") ? 'selected': ''; ?>>AND ed Report</option>
                        <option value="O" <?php echo ($sel_and_or == "O") ? 'selected': ''; ?>>OR ed Report</option>
                    </select>
                </div>
                <div class="col-sm-4">&nbsp;
<!--                    <label for="">Report Save Name</label>
                    <input type="text" id="save_report_name" name="save_report_name" class="form-control" value="<?php echo ($save_report_name) ? $save_report_name: ''; ?>" />-->
                </div>

            </div>

        </div>
    </div>

    <div class="grpara">
        <div class="anatreport"><h2>Refine your search</h2></div>
        <div class="clearfix"></div>
        <div class="pd5" id="searchcriteria">

            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-4">
                    <label for=""><?php getZipPostalLabel(); ?></label>
                    <input maxlength="<?php echo inter_zip_length();?>" type="text" id="zip" name="zip" value="<?php echo ($zip) ? $zip: ''; ?>" class="form-control" onBlur="zip_vs_state(this,'zip_ext','city','state');">
                    <input maxlength="4" type="hidden" id="zip_ext" name="zip_ext" value="<?php echo ($zip_ext) ? $zip_ext: ''; ?>" class="form-control">	
                </div>
                <div class="col-sm-5">
                    <label for="">City</label>
                    <input type="text" id="city" name="city" class="form-control" value="<?php echo ($city) ? $city: ''; ?>"/>
                </div>
                <div class="col-sm-3">
                    <label for="">State</label>
                    <input type="text" id="state" name="state" class="form-control" value="<?php echo ($state) ? $state: ''; ?>"/>
                </div>
            </div>
            
            <div class="row">
                <div class="clearfix"></div>

                    <?php
                    $arrEthnicity = array(
                        "African Americans" => "African Americans",
                        "American Indians" => "American Indians",
                        "Chinese" => "Chinese",
                        "European Americans" => "European Americans",
                        "Hispanic or Latino" => "Hispanic or Latino",
                        "Jewish" => "Jewish",
                        "Not Hispanic or Latino" => "Not Hispanic or Latino",
                        "Unknown" => "Unknown",
                        "Other" => "Other"
                    );
                    ?>

                    <?php
                    $arrRace = array(
                        "American Indian or Alaska Native" => "American Indian or Alaska Native",
                        "Asian" => "Asian",
                        "Black or African American" => "Black or African American",
                        "Native Hawaiian or Other Pacific Islander" => "Native Hawaiian or Other Pacific Islander",
                        "White" => "White",
                        "Other Race" => "Other"
                    );
                    ?>
                <?php
                            $arrLanguage = array('English','Spanish','French','German','Russian','Japanese','Portuguese','Italian');
                            sort($arrLanguage);
                            ?>
                <div class="col-sm-4">
                    <label for="">Ethnicity</label>
                    <select name="ethnicity[]" id="ethnicity" data-container="#select_drop" class="selectpicker form-control" onchange="addOtherDiv(this);" data-call="ethnicity" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                        <?php
                        foreach ($arrEthnicity as $k => $s) {
                            $sel = (in_array($s,$ethnicity)) ? 'selected' : '';
                            echo "<option value='" . $s . "' ".$sel.">" . ucfirst($k) . "</option>";
                        }
                        ?>
                    </select>
                    <div id="other_ethnicity" class="form-inline hide">
                        <input type="text" name="other_ethnicity" id="other_ethnicity" size="13" value="<?php echo ($other_ethnicity) ? $other_ethnicity: ''; ?>" class="">
                        <span class="glyphicon glyphicon-arrow-left" title="Back" onclick="hideThis(this);" data-call="ethnicity"></span>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label for="">Race</label>
                    <select name="race[]" id="race" data-container="#select_drop" class="selectpicker form-control" onchange="addOtherDiv(this);" data-call="race" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                        <?php
                        foreach ($arrRace as $k => $s) {
                            $sel = (in_array($s,$race)) ? 'selected' : '';
                            echo "<option value='" . $s . "' ".$sel.">" . ucfirst($k) . "</option>";
                        }
                        ?>
                    </select>
                    <div id="other_race" class="form-inline hide">
                        <input type="text" name="other_race" id="other_race" size="13" value="<?php echo ($other_race) ? $other_race: ''; ?>" class="">
                        <span class="glyphicon glyphicon-arrow-left" title="Back" onclick="hideThis(this);" data-call="race" ></span>
                    </div>

                </div>
                <div class="col-sm-4">
                    <label for="">Language</label>
                    <select name="language[]" id="language" data-container="#select_drop" class="selectpicker form-control" onchange="addOtherDiv(this);" data-call="language" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                        <?php
                        foreach ($arrLanguage as $s) {
                            $sel = (in_array($s,$language)) ? 'selected' : '';
                            echo "<option value='" . $s . "' ".$sel.">" . ucfirst($s) . "</option>";
                        }
                        ?>
                        <option value="Other">Other</option>
                    </select>
                    <div id="other_language" class="form-inline hide">
                        <input type="text" name="other_language" id="other_language" size="13" value="<?php echo ($other_language) ? $other_language: ''; ?>" class="">
                        <span class="glyphicon glyphicon-arrow-left" title="Back" onclick="hideThis(this);" data-call="language" ></span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-8">
                    <label for="age_criteria">Age (years)</label>
                    <div class="form-inline">
                        <select id="age_criteria" name="age_criteria" class="form-control minimal" style="width:40px;">
                            <option value="greater" <?php echo ($age_criteria == "greater") ? 'selected': ''; ?>>&gt;</option>
                            <option value="greater_equal" <?php echo ($age_criteria == "greater_equal") ? 'selected': ''; ?>> >= </option>
                            <option value="equalsto" <?php echo ($age_criteria == "equalsto") ? 'selected': ''; ?>> = </option>
                            <option value="less_equal" <?php echo ($age_criteria == "less_equal") ? 'selected': ''; ?>>&lt;=</option>
                            <option value="less" <?php echo ($age_criteria == "less") ? 'selected': ''; ?>>&lt;</option>
                        </select>
                        <input type="text" style="width:100px;" id="age_val" name="age_val" value="<?php echo $age_val;?>" class="form-control" />
                    </div>
                </div>
                <div class="col-sm-4">
                    <label for="">Gender</label>
                    <select name="sex" id="sex" class="form-control minimal">
                        <option value=""> - All -</option>
                        <option value="male" <?php echo ($sex == "male") ? 'selected': ''; ?>>Male</option>
                        <option value="female" <?php echo ($sex == "female") ? 'selected': ''; ?>>Female</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-8">
                    <div class="checkbox checkbox-inline pointer"><br />
                        <input type="checkbox" name="hippa_mail" id="hippa_mail" value="1" <?php echo ($hippa_mail == "1") ? 'checked': ''; ?>>
                        <label for="hippa_mail">Postal Mail</label>
                    </div>
                    <div class="checkbox checkbox-inline pointer"><br />
                        <input type="checkbox" name="hippa_email" id="hippa_email" value="1" <?php echo ($hippa_email == "1") ? 'checked': ''; ?>>
                        <label for="hippa_email">eMail</label>
                    </div>
                    <div class="checkbox checkbox-inline pointer"><br />
                        <input type="checkbox" name="hippa_voice" id="hippa_voice" value="1" <?php echo ($hippa_voice == "1") ? 'checked': ''; ?>>
                        <label for="hippa_voice">Voice</label>
                    </div>
                </div>
                <div class="col-sm-4">
                    <label for="">&nbsp;</label>
                    <select name="voiceType" id="voiceType"  class="form-control minimal">
                        <option value="">- All -</option>
                        <option value="0" <?php echo ($voiceType == "0") ? 'selected': ''; ?>>Home Phone</option>
                        <option value="1" <?php echo ($voiceType == "1") ? 'selected': ''; ?>>Work Phone</option>
                        <option value="2" <?php echo ($voiceType == "2") ? 'selected': ''; ?>>Mobile Phone</option>
                    </select> 

                </div>
            </div>
            
            <div class="row">
                <div class="clearfix"></div>
                <div class="col-sm-6">
                    <label for="">Contact Time From</label>
                    <div class="form-inline">
                        <select name="hourFrom" id="hourFrom" style="width:42px" class="form-control minimal">
                            <option value=""></option>
                            <?php echo $CLSReports->timeNumbers(12, $hourFrom); ?>
                        </select><strong>:</strong>
                        <select name="minFrom" id="minFrom" style="width:42px" class="form-control minimal">
                            <?php echo $CLSReports->timeNumbers(59, $minFrom); ?>
                        </select>
                        <select name="ampmFrom" id="ampmFrom" style="width:50px;"  class="form-control minimal">
                            <option value="AM" <?php echo ($ampmFrom == "AM") ? 'selected': ''; ?>>AM</option>
                            <option value="PM" <?php echo ($ampmFrom == "AM") ? 'selected': ''; ?>>PM</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <label for="">Contact Time To</label>
                    <div class="form-inline">
                        <select name="hourTo" id="hourTo" style="width:42px" class="form-control minimal">
                            <option value=""></option>
                            <?php echo $CLSReports->timeNumbers(12, $hourTo); ?>
                        </select><strong>:</strong>
                        <select name="minTo" id="minTo" style="width:42px" class="form-control minimal">
                            <?php echo $CLSReports->timeNumbers(59, $minTo); ?>
                        </select>
                        <select name="ampmTo" id="ampmTo" style="width:50px" class="form-control minimal">
                            <option value="AM" <?php echo ($ampmTo == "AM") ? 'selected': ''; ?>>AM</option>
                            <option value="PM" <?php echo ($ampmTo == "AM") ? 'selected': ''; ?>>PM</option>
                        </select>
                    </div>
                </div>
                
            </div>
            <div class="row">
                <div class="clearfix"></div><br/>
                <div class="col-sm-4">
                    Demographics
                </div>
                <div class="col-sm-8">
                    
                    <div class="pdl_10">
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_demographics_inc" name="rd_demographics" value="1" checked <?php echo ($rd_demographics == "1") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_demographics_inc">Include</label>
                        </div>
                        <div class="radio radio-inline pointer">
                            <input type="radio" id="rd_demographics_exc" name="rd_demographics" value="0" <?php echo ($rd_demographics == "0") ? 'checked': ''; ?> class="form-control">
                            <label for="rd_demographics_exc">Exclude</label>
                        </div>
                    </div>

                </div>

            </div>
            

        </div>
        <div class="clearfix">&nbsp;</div>
    </div>
	<div class="grpara">
		<div class="anatreport">
			<h2>Format</h2>
		</div>
		<div class="clearfix"></div>
		<div class="pd5" id="searchcriteria">
			<div class="row">
				<div class="col-sm-4">
					<div class="radio radio-inline pointer" >
						<input type="radio" name="output_option" id="output_csv" value="output_csv" <?php if ($_POST['output_option'] == 'output_csv' || $_POST['output_option']=='') echo 'CHECKED'; ?>/>
						<label for="output_csv">CSV</label>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix">&nbsp;</div>
	</div>
</div>
<div id="module_buttons" class="ad_modal_footer text-center">
	<button class="savesrch" type="" onClick="get_result(); return false;">Search</button>
</div>