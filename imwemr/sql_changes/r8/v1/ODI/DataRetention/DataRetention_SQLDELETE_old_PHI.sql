
/* Workview data related SQL queries */
DROP TABLE IF EXISTS ChartMasterId_ToDel;
CREATE TABLE ChartMasterId_ToDel(id INT auto_increment PRIMARY KEY, form_id INT);

INSERT INTO ChartMasterId_ToDel(form_id)
SELECT DISTINCT c.id
FROM chart_master_table c
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = c.date_of_Service
INNER JOIN patient_data pd ON pd.id = c.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE c.date_of_Service < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY c.id ASC;

DELETE C.* FROM chart_master_table C INNER JOIN ChartMasterId_ToDel T ON C.id = T.form_id ;

DELETE C.* FROM chart_ar_scan C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_assessment_plans C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_correction_values C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_cvf C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM chart_dialation C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_diplopia C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM chart_eom C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_external_exam C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_genhealth_archive C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_gonio C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_icp_color C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_iop C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_la C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_left_cc_history C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_left_provider_issue C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_master_table_binary C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_ood C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_optic C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_pc_mr_values C 
INNER JOIN chart_pc_mr P ON C.chart_pc_mr_id = P.id
INNER JOIN ChartMasterId_ToDel T ON P.form_id = T.form_id ;
DELETE C.* FROM chart_pc_mr C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_procedures C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_progress_notes C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_pupil C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM chart_records_archive C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_ref_surgery C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_remote_log C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_rv C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_save_log C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_schedule_test_external C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_signatures C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_slit_lamp_exam C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_smart_chart C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_steropsis C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_sx_plan_sheet C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_usr_roles C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_vis_lasik C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_vision C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM chart_w4dot C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;


DELETE C.* FROM alert_tbl_reason C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM amendments C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM amsler_grid C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM communication C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM consent_form C INNER JOIN ChartMasterId_ToDel T ON C.form_created_date = T.form_id ;
DELETE C.* FROM consent_form_signature C INNER JOIN ChartMasterId_ToDel T ON C.form_information_id = T.form_id ;
DELETE C.* FROM contact_lens_progress C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM contactlensmaster C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM direct_messages_attachment C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM disc C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM disc_external C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM disclosed_details C INNER JOIN ChartMasterId_ToDel T ON C.formid = T.form_id ;
DELETE C.* FROM document_patient_rel C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM dr_task C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM dss_tiu C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM formulaheadings C INNER JOIN ChartMasterId_ToDel T ON C.formula_id = T.form_id ;
DELETE C.* FROM formulaheadings C INNER JOIN ChartMasterId_ToDel T ON C.formula_heading_name = T.form_id ;
DELETE C.* FROM gas_trl_dsp_fnl_contact_lens C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM glaucoma_medication_comments C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM glaucoma_miscellaneous C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM glaucoma_past_readings C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM hc_observations C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM ibra_case C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM icg C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM immunizations_alerts C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM in_item C INNER JOIN ChartMasterId_ToDel T ON C.formula = T.form_id ;
DELETE C.* FROM in_retail_price_markup C INNER JOIN ChartMasterId_ToDel T ON C.formula = T.form_id ;
DELETE C.* FROM iol_master_tbl C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM iolphyformulavalues C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM iportal_admin C INNER JOIN ChartMasterId_ToDel T ON C.forms_vis = T.form_id ;
DELETE C.* FROM ivfa C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM log_ccda_creation C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM memo_tbl C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM nfa C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM oct C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM oct_rnfl C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM ophtha C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM order_set_associate_chart_notes C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM pachy C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM patientPayer C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM patient_consent_form_information C INNER JOIN ChartMasterId_ToDel T ON C.form_information_id = T.form_id ;
DELETE C.* FROM patient_consent_form_information C INNER JOIN ChartMasterId_ToDel T ON C.form_status = T.form_id ;
DELETE C.* FROM patient_consent_form_information C INNER JOIN ChartMasterId_ToDel T ON C.form_created_date = T.form_id ;
DELETE C.* FROM patient_consent_form_information_app C INNER JOIN ChartMasterId_ToDel T ON C.form_information_id = T.form_id ;
DELETE C.* FROM patient_consent_form_information_app C INNER JOIN ChartMasterId_ToDel T ON C.form_status = T.form_id ;
DELETE C.* FROM patient_consent_form_information_app C INNER JOIN ChartMasterId_ToDel T ON C.form_created_date = T.form_id ;
DELETE C.* FROM patient_custom_field C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM patient_goals C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM patient_health_status C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM patient_last_examined C INNER JOIN ChartMasterId_ToDel T ON C.formid = T.form_id ;
DELETE C.* FROM patient_procedure_injections C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM pn_reports C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM pnotes C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM prescriptions C INNER JOIN ChartMasterId_ToDel T ON C.form = T.form_id ;
DELETE C.* FROM prev_gas_contact_lens C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM prev_soft_contact_lens C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM print_orders_data C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM pt_printed_records C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM pt_problem_list C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM restricted_providers C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM restricted_reasons C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM schedule C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM send_fax_log_tbl C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM special_dgn_contact_lens C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM superbill C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM surgery_center_consent_forms_template C INNER JOIN ChartMasterId_ToDel T ON C.form_created_from = T.form_id ;
DELETE C.* FROM surgery_center_pre_op_health_ques C INNER JOIN ChartMasterId_ToDel T ON C.form_status = T.form_id ;
DELETE C.* FROM surgery_consent_filled_form C INNER JOIN ChartMasterId_ToDel T ON C.form_status = T.form_id ;
DELETE C.* FROM surgery_consent_filled_form C INNER JOIN ChartMasterId_ToDel T ON C.form_created_date = T.form_id ;
DELETE C.* FROM surgical_tbl C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM tbl_def_val C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM test_bscan C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM test_cellcnt C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM test_custom_patient C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM test_gdx C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM test_labs C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM test_other C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM topography C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM trl_dsp_fnl_contact_lens C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM vf C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM vf_gl C INNER JOIN ChartMasterId_ToDel T ON C.formId = T.form_id ;
DELETE C.* FROM vf_nfa C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM vision_contact_lens C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM vital_sign_master C INNER JOIN ChartMasterId_ToDel T ON C.form_id = T.form_id ;
DELETE C.* FROM vital_sign_patient C INNER JOIN
vital_sign_master v ON C.vital_master_id = v.id
INNER JOIN ChartMasterId_ToDel T ON v.form_id = T.form_id ;


/* Test pages related SQL queries to SELECT * the data*/

DROP TABLE IF EXISTS TestId_ToDel;
CREATE TABLE TestId_ToDel(id INT auto_increment PRIMARY KEY, test_id INT,test_name VARCHAR(500));

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.surgical_id,'A/Scan' FROM surgical_tbl t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.surgical_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.test_bscan_id,'B-Scan' 
FROM test_bscan t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.test_bscan_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.test_labs_id,'Laboratories'
FROM test_labs t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.test_labs_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.test_cellcnt_id,'Cell Count'
FROM test_cellcnt t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.test_cellcnt_id ASC;


INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.icg_id,'ICG'
FROM icg t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.exam_date
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.exam_date < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.icg_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.nfa_id,'HRT'
FROM nfa t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.nfa_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.vf_id,'VF'
FROM vf t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.vf_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.vf_gl_id,'VF-GL'
FROM vf_gl t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.vf_gl_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.oct_id,'OCT'
FROM oct t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.oct_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.oct_rnfl_id,'OCT-RNFL'
FROM oct_rnfl t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.oct_rnfl_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.gdx_id,'GDX'
FROM test_gdx t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.gdx_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.pachy_id,'Pachy'
FROM pachy t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.pachy_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.vf_id,'IVFA'
FROM ivfa t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.exam_Date
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.exam_Date < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.vf_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.disc_id,'Fundus'
FROM disc t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.disc_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.disc_id,'External/Anterior'
FROM disc_external t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.disc_id ASC;

INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.topo_id,'Topography'
FROM topography t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.topo_id ASC;


INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.iol_master_id,'IOL Master'
FROM iol_master_tbl t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.iol_master_id ASC;


INSERT INTO TestId_ToDel(test_id,test_name) 
SELECT DISTINCT t.test_other_id,'Other'
FROM test_other t
INNER JOIN schedule_appointments sa ON sa.sa_app_start_date = t.examDate
INNER JOIN patient_data pd ON pd.id = t.patientId
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE t.examDate < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  
ORDER BY t.test_other_id ASC;



DELETE t.* FROM surgical_tbl t  INNER JOIN TestId_ToDel td ON td.test_id = t.surgical_id  and td.test_name = 'A/Scan';
DELETE t.* FROM test_bscan t  INNER JOIN TestId_ToDel td ON td.test_id = t.test_bscan_id  and td.test_name = 'B-Scan';
DELETE t.* FROM test_labs t  INNER JOIN TestId_ToDel td ON td.test_id = t.test_labs_id  and td.test_name = 'Laboratories';
DELETE t.* FROM test_cellcnt t  INNER JOIN TestId_ToDel td ON td.test_id = t.test_cellcnt_id  and td.test_name = 'Cell Count';
DELETE t.* FROM icg t  INNER JOIN TestId_ToDel td ON td.test_id = t.icg_id  and td.test_name = 'ICG';
DELETE t.* FROM nfa t  INNER JOIN TestId_ToDel td ON td.test_id = t.nfa_id  and td.test_name = 'HRT';
DELETE t.* FROM vf t  INNER JOIN TestId_ToDel td ON td.test_id = t.vf_id  and td.test_name = 'VF';
DELETE t.* FROM vf_gl t  INNER JOIN TestId_ToDel td ON td.test_id = t.vf_gl_id  and td.test_name = 'VF-GL';
DELETE t.* FROM oct t  INNER JOIN TestId_ToDel td ON td.test_id = t.oct_id  and td.test_name = 'OCT';
DELETE t.* FROM oct_rnfl t  INNER JOIN TestId_ToDel td ON td.test_id = t.oct_rnfl_id  and td.test_name = 'OCT-RNFL';
DELETE t.* FROM test_gdx t  INNER JOIN TestId_ToDel td ON td.test_id = t.gdx_id  and td.test_name = 'GDX';
DELETE t.* FROM pachy t  INNER JOIN TestId_ToDel td ON td.test_id = t.pachy_id  and td.test_name = 'Pachy';
DELETE t.* FROM ivfa t  INNER JOIN TestId_ToDel td ON td.test_id = t.vf_id  and td.test_name = 'IVFA';
DELETE t.* FROM disc t  INNER JOIN TestId_ToDel td ON td.test_id = t.disc_id  and td.test_name = 'Fundus';
DELETE t.* FROM disc_external t  INNER JOIN TestId_ToDel td ON td.test_id = t.disc_id  and td.test_name = 'External/Anterior';
DELETE t.* FROM topography t  INNER JOIN TestId_ToDel td ON td.test_id = t.topo_id  and td.test_name = 'Topography';
DELETE t.* FROM iol_master_tbl t  INNER JOIN TestId_ToDel td ON td.test_id = t.iol_master_id  and td.test_name = 'IOL Master';
DELETE t.* FROM test_other t  INNER JOIN TestId_ToDel td ON td.test_id = t.test_other_id  and td.test_name = 'Other';


/* Schedule Appointments */
DELETE ps.* FROM schedule_appointments sa  
INNER JOIN previous_status ps ON sa.id = ps.sch_id
INNER JOIN patient_data pd ON pd.id = sa.sa_patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE sa.sa_app_start_date < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  ;


DELETE sa.* FROM schedule_appointments sa  
INNER JOIN patient_data pd ON pd.id = sa.sa_patient_id
INNER JOIN facility f ON  f.id = sa.sa_facility_id
WHERE sa.sa_app_start_date < (CASE WHEN LOWER(f.state) = 'ma' THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
															WHEN LOWER(f.state) = 'mn' AND FLOOR(DATEDIFF(now(),DOB)/365)>25 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'co'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'mt'  AND FLOOR(DATEDIFF(now(),DOB)/365)>28 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN LOWER(f.state) = 'nc'  AND FLOOR(DATEDIFF(now(),DOB)/365)>30 THEN DATE_ADD(Now(), INTERVAL -11 YEAR)
                                                            WHEN LOWER(f.state) = 'hi'  AND FLOOR(DATEDIFF(now(),DOB)/365)>43 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            WHEN FLOOR(DATEDIFF(now(),DOB)/365)>=23 THEN DATE_ADD(Now(), INTERVAL -10 YEAR)
                                                            END)  ;


