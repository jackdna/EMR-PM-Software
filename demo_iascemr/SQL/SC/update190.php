<?php
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
?>
<?php
set_time_limit(0);
include_once("../../common/conDb.php");

$qry="ALTER TABLE `amd_charges_log` ADD `anes_status` VARCHAR( 100 ) NOT NULL ,
ADD `fac_status` VARCHAR( 100 ) NOT NULL ,
ADD `prov_status` VARCHAR( 100 ) NOT NULL ,
ADD `anes_reason` VARCHAR( 100 ) NOT NULL ,
ADD `fac_reason` VARCHAR( 100 ) NOT NULL ,
ADD `prov_reason` VARCHAR( 100 ) NOT NULL;";
imw_query($qry) or $msg_info[] = imw_error();


$qry="ALTER TABLE `icd10_data` ADD `operator_id` INT( 11 ) NOT NULL ,
ADD `entered_date_time` DATETIME NOT NULL ,
ADD `modified_by` INT( 11 ) NOT NULL ,
ADD `modified_date_time` DATETIME NOT NULL ,
ADD `del_operator_id` INT( 11 ) NOT NULL ,
ADD `del_date_time` DATETIME NOT NULL ,
ADD `status` INT( 2 ) NOT NULL ,
ADD `as_id` VARCHAR( 50 ) NOT NULL";
imw_query($qry) or $msg_info[] = imw_error();

$qry="ALTER TABLE `icd10_data` ADD INDEX deleted(deleted);";
imw_query($qry) or $msg_info[] = imw_error();

//START TAKE BACKUP OF TABLE BEFORE ANY ACTION
$tbl = "icd10_data";
$bkTbl = $tbl.'_'.date('Y_m_d');
$sql1="CREATE  TABLE ".$bkTbl." LIKE ".$tbl;
$res=imw_query($sql1) or $msg_info[] = imw_error();
if( $res ) {
	$sql1="INSERT INTO ".$bkTbl." (SELECT *  FROM ".$tbl.");";
	imw_query($sql1)or $msg_info[] = imw_error();
}
//END TAKE BACKUP OF TABLE BEFORE ANY ACTION

//idoc update 18
imw_query("update icd10_data set icd10_desc='TYPE 2 DIABETES MELLITUS WITH SEVERE PROLIFERATIVE DIABETIC RETINOPATHY WITH MACULAR EDEMA' where icd10 = 'E11.35--'") or $msg_info[] = imw_error();
imw_query("update icd10_data set icd10_desc='TYPE 2 DIABETES MELLITUS WITH SEVERE PROLIFERATIVE DIABETIC RETINOPATHY WITHOUT MACULAR EDEMA' where icd10 = 'E11.359-'") or $msg_info[] = imw_error();

//idoc update 74
imw_query("update icd10_data set deleted='1' where icd10 in ('C43.11','C43.12','C43.1-','C4A.11','C4A.12','C4A.1-','C44.102','C44.109','C44.112','C44.119','C44.122','C44.129','C44.192','C44.199','D03.11','D03.12','D03.1-','D04.11','D04.12','D04.1-','D22.11','D22.12','D22.1-','D23.11','D23.12','D23.1-','G51.3','H57.8')");

//idoc update 74
$qry=imw_query("Select id from icd10_data where deleted='0' and icd10 in('C43.111')");
if(imw_num_rows($qry)==0){
	imw_query("INSERT INTO icd10_data (cat_id, icd10, laterality, staging, icd10_desc, operator_id,entered_date_time) VALUES
	(2, 'C43.111', '', '', 'MALIGNANT MELANOMA OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C43.112', '', '', 'MALIGNANT MELANOMA OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C43.121', '', '', 'MALIGNANT MELANOMA OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C43.122', '', '', 'MALIGNANT MELANOMA OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C4A.111', '', '', 'MERKEL CELL CARCINOMA OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C4A.112', '', '', 'MERKEL CELL CARCINOMA OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C4A.121', '', '', 'MERKEL CELL CARCINOMA OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C4A.122', '', '', 'MERKEL CELL CARCINOMA OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1021', '', '', 'UNSPECIFIED MALIGNANT NEOPLASM OF SKIN OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1022', '', '', 'UNSPECIFIED MALIGNANT NEOPLASM OF SKIN OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1091', '', '', 'UNSPECIFIED MALIGNANT NEOPLASM OF SKIN OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1092', '', '', 'UNSPECIFIED MALIGNANT NEOPLASM OF SKIN OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1121', '', '', 'BASAL CELL CARCINOMA OF SKIN OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1122', '', '', 'BASAL CELL CARCINOMA OF SKIN OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1191', '', '', 'BASAL CELL CARCINOMA OF SKIN OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1192', '', '', 'BASAL CELL CARCINOMA OF SKIN OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1221', '', '', 'SQUAMOUS CELL CARCINOMA OF SKIN OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1222', '', '', 'SQUAMOUS CELL CARCINOMA OF SKIN OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1291', '', '', 'SQUAMOUS CELL CARCINOMA OF SKIN OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1292', '', '', 'SQUAMOUS CELL CARCINOMA OF SKIN OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1321', '', '', 'SEBACEOUS CELL CARCINOMA OF SKIN OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1322', '', '', 'SEBACEOUS CELL CARCINOMA OF SKIN OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1391', '', '', 'SEBACEOUS CELL CARCINOMA OF SKIN OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1392', '', '', 'SEBACEOUS CELL CARCINOMA OF SKIN OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1921', '', '', 'OTHER SPECIFIED MALIGNANT NEOPLASM OF SKIN OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1922', '', '', 'OTHER SPECIFIED MALIGNANT NEOPLASM OF SKIN OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1991', '', '', 'OTHER SPECIFIED MALIGNANT NEOPLASM OF SKIN OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'C44.1992', '', '', 'OTHER SPECIFIED MALIGNANT NEOPLASM OF SKIN OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D03.111', '', '', 'MELANOMA IN SITU OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D03.112', '', '', 'MELANOMA IN SITU OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D03.121', '', '', 'MELANOMA IN SITU OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D03.122', '', '', 'MELANOMA IN SITU OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D04.111', '', '', 'CARCINOMA IN SITU OF SKIN OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D04.112', '', '', 'CARCINOMA IN SITU OF SKIN OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D04.121', '', '', 'CARCINOMA IN SITU OF SKIN OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D04.122', '', '', 'CARCINOMA IN SITU OF SKIN OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D22.111', '', '', 'MELANOCYTIC NEVI OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D22.112', '', '', 'MELANOCYTIC NEVI OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D22.121', '', '', 'MELANOCYTIC NEVI OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D22.122', '', '', 'MELANOCYTIC NEVI OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D23.111', '', '', 'OTHER BENIGN NEOPLASM OF SKIN OF RIGHT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D23.112', '', '', 'OTHER BENIGN NEOPLASM OF SKIN OF RIGHT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D23.121', '', '', 'OTHER BENIGN NEOPLASM OF SKIN OF LEFT UPPER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(2, 'D23.122', '', '', 'OTHER BENIGN NEOPLASM OF SKIN OF LEFT LOWER EYELID, INCLUDING CANTHUS','1','2018-10-01 01:01:01'),
	(7, 'G51.3-', '1', '', 'CLONIC HEMIFACIAL SPASM','1','2018-10-01 01:01:01'),
	(8, 'H01.00A', '', '', 'UNSPECIFIED BLEPHARITIS RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H01.00B', '', '', 'UNSPECIFIED BLEPHARITIS LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H01.01A', '', '', 'ULCERATIVE BLEPHARITIS RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H01.01B', '', '', 'ULCERATIVE BLEPHARITIS LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H01.02A', '', '', 'SQUAMOUS BLEPHARITIS RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H01.02B', '', '', 'SQUAMOUS BLEPHARITIS LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.151', '', '', 'PARALYTIC ECTROPION OF RIGHT UPPER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.152', '', '', 'PARALYTIC ECTROPION OF RIGHT LOWER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.154', '', '', 'PARALYTIC ECTROPION OF LEFT UPPER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.155', '', '', 'PARALYTIC ECTROPION OF LEFT LOWER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.159', '', '', 'PARALYTIC ECTROPION OF UNSPECIFIED EYE, UNSPECIFIED EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.20A', '', '', 'UNSPECIFIED LAGOPHTHALMOS RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.20B', '', '', 'UNSPECIFIED LAGOPHTHALMOS LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.20C', '', '', 'UNSPECIFIED LAGOPHTHALMOS, BILATERAL, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.21A', '', '', 'CICATRICIAL LAGOPHTHALMOS RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.21B', '', '', 'CICATRICIAL LAGOPHTHALMOS LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.21C', '', '', 'CICATRICIAL LAGOPHTHALMOS, BILATERAL, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.22A', '', '', 'MECHANICAL LAGOPHTHALMOS RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.22B', '', '', 'MECHANICAL LAGOPHTHALMOS LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.22C', '', '', 'MECHANICAL LAGOPHTHALMOS, BILATERAL, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.23A', '', '', 'PARALYTIC LAGOPHTHALMOS RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.23B', '', '', 'PARALYTIC LAGOPHTHALMOS LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.23C', '', '', 'PARALYTIC LAGOPHTHALMOS, BILATERAL, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.881', '', '', 'MEIBOMIAN GLAND DYSFUNCTION RIGHT UPPER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.882', '', '', 'MEIBOMIAN GLAND DYSFUNCTION RIGHT LOWER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.884', '', '', 'MEIBOMIAN GLAND DYSFUNCTION LEFT UPPER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.885', '', '', 'MEIBOMIAN GLAND DYSFUNCTION LEFT LOWER EYELID','1','2018-10-01 01:01:01'),
	(8, 'H02.88A', '', '', 'MEIBOMIAN GLAND DYSFUNCTION RIGHT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H02.88B', '', '', 'MEIBOMIAN GLAND DYSFUNCTION LEFT EYE, UPPER AND LOWER EYELIDS','1','2018-10-01 01:01:01'),
	(8, 'H10.82-', '1', '', 'ROSACEA CONJUNCTIVITIS','1','2018-10-01 01:01:01'),
	(8, 'H57.81-', '1', '', 'BROW PTOSIS','1','2018-10-01 01:01:01'),
	(20, 'H57.89', '', '', 'OTHER SPECIFIED DISORDERS OF EYE AND ADNEXA','1','2018-10-01 01:01:01');");
}

//idoc update 74
$qry=imw_query("Select id from icd10_data where deleted='0' and icd10 in('H04.20-')");
if(imw_num_rows($qry)==0){
	imw_query("INSERT INTO icd10_data (icd10, icd10_desc, laterality, cat_id) VALUES ('H04.20-', 'UNSPECIFIED EPIPHORA', '1', '8');");
}

//idoc update 74
$qry=imw_query("Select id from icd10_data where deleted='0' and icd10 in('H04.22-')");
if(imw_num_rows($qry)==0){
	imw_query("INSERT INTO icd10_data (icd10, icd10_desc, laterality, cat_id) VALUES ('H04.22-', 'EPIPHORA DUE TO INSUFFICIENT DRAINAGE', '1', '8');");
}

//idoc update 77
$qry="update icd10_data set icd10_desc='TYPE 2 DIABETES MELLITUS WITHOUT COMPLICATIONS' where icd10 in ('E11.9') and icd10_desc='TYPE 2 DIABETES WITHOUT OPHTHALMIC COMPLICATIONS'";
imw_query($qry) or $msg_info[] = imw_error();

//idoc update 115
$qry="update icd10_data set deleted='1' where icd10 in ('H50.151') and deleted='0'";
imw_query($qry) or $msg_info[] = imw_error();

//idoc update 131
$qry="update icd10_data set deleted='1' where icd10 in ('Q79.6','H81.41','H81.42','H81.43','H81.49','H81.4-')";
imw_query($qry) or $msg_info[] = imw_error();

//idoc update 131
$qry=imw_query("Select id from icd10_data where deleted='0' and icd10 in('S02.12--')");
if(imw_num_rows($qry)==0){
	imw_query("INSERT INTO `icd10_data` (`cat_id`, `icd9`, `icd10`, `laterality`, `staging`, `severity`, `pqri`, `cmnts`, `icd9_desc`, `icd10_desc`, `deleted`, `parent_id`, `master_codes`, `master_ids`, `no_bilateral`, `group_heading`, `operator_id`, `entered_date_time`, `modified_by`, `modified_date_time`, `del_operator_id`, `del_date_time`, `status`, `as_id`) VALUES
	(30, '', 'H81.4', '', '', '', '', '', '', 'VERTIGO OF CENTRAL ORIGIN', 0, 0, '', '', 0, '', 96, '2019-10-14 12:12:05', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'Q79.60', '', '', '', '', '', '', 'EHLERS-DANLOS SYNDROME, UNSPECIFIED', 0, 0, '', '', 0, '', 96, '2019-10-14 12:16:15', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'Q79.61', '', '', '', '', '', '', 'CLASSICAL EHLERS-DANLOS SYNDROME', 0, 0, '', '', 0, '', 96, '2019-10-14 12:16:36', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'Q79.62', '', '', '', '', '', '', 'HYPERMOBILE EHLERS-DANLOS SYNDROME', 0, 0, '', '', 0, '', 96, '2019-10-14 12:16:55', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'Q79.63', '', '', '', '', '', '', 'VASCULAR EHLERS-DANLOS SYNDROME', 0, 0, '', '', 0, '', 96, '2019-10-14 12:17:13', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'Q79.69', '', '', '', '', '', '', 'OTHER EHLERS-DANLOS SYNDROMES', 0, 0, '', '', 0, '', 96, '2019-10-14 12:17:29', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'S02.12--', '1', '5', '', '', '', '', 'FRACTURE OF ORBITAL ROOF', 0, 0, '', '', 1, '', 96, '2019-10-14 12:23:24', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'S02.83--', '1', '5', '', '', '', '', 'Fracture of medial orbital wall', 0, 0, '', '', 1, '', 96, '2019-10-14 12:23:24', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'S02.84--', '1', '5', '', '', '', '', 'FRACTURE OF LATERAL ORBITAL WALL', 0, 0, '', '', 1, '', 96, '2019-10-14 12:28:31', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'S02.85X-', '', '5', '', '', '', '', 'FRACTURE OF ORBIT, UNSPECIFIED', 0, 0, '', '', 0, '', 96, '2019-10-14 12:29:20', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'Z01.020', '', '', '', '', '', '', 'ENCOUNTER FOR EXAMINATION OF EYES AND VISION FOLLOWING FAILED VISION SCREENING WITHOUT ABNORMAL FINDINGS', 0, 0, '', '', 0, '', 96, '2019-10-14 12:30:06', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(30, '', 'Z01.021', '', '', '', '', '', '', 'ENCOUNTER FOR EXAMINATION OF EYES AND VISION FOLLOWING FAILED VISION SCREENING WITH ABNORMAL FINDINGS', 0, 0, '', '', 0, '', 96, '2019-10-14 12:30:37', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '');");
}

//idoc update 131
$qry=imw_query("Select id from icd10_data where deleted='0' and icd10 in('H54.0X33')");
if(imw_num_rows($qry)==0){
	imw_query("INSERT INTO `icd10_data` (`cat_id`, `icd9`, `icd10`, `laterality`, `staging`, `severity`, `pqri`, `cmnts`, `icd9_desc`, `icd10_desc`, `deleted`, `parent_id`, `master_codes`, `master_ids`, `no_bilateral`, `group_heading`, `operator_id`, `entered_date_time`, `modified_by`, `modified_date_time`, `del_operator_id`, `del_date_time`, `status`, `as_id`) VALUES
	(20, '', 'H54', '', '', '', '', '', '', 'BLINDNESS AND LOW VISION', 0, 0, '', '', 0, '', 96, '2019-10-19 12:21:02', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0', '', '', '', '', '', '', 'BLINDNESS, BOTH EYES', 0, 0, '', '', 0, '', 96, '2019-10-19 12:21:38', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X', '', '', '', '', '', '', 'BLINDNESS, BOTH EYES, DIFFERENT CATEGORY LEVELS', 0, 0, '', '', 0, '', 96, '2019-10-19 12:22:54', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X3', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE, CATEGORY 3', 0, 0, '', '', 0, '', 96, '2019-10-19 12:23:18', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X33', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 3, BLINDNESS LEFT EYE CATEGORY 3', 0, 0, '', '', 0, '', 96, '2019-10-19 12:23:35', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X34', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 3, BLINDNESS LEFT EYE CATEGORY 4', 0, 0, '', '', 0, '', 96, '2019-10-19 12:23:50', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X35', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 3, BLINDNESS LEFT EYE CATEGORY 5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:24:14', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X4', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE, CATEGORY 4', 0, 0, '', '', 0, '', 96, '2019-10-19 12:25:37', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X43', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 4, BLINDNESS LEFT EYE CATEGORY 3', 0, 0, '', '', 0, '', 96, '2019-10-19 12:25:49', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X44', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 4, BLINDNESS LEFT EYE CATEGORY 4', 0, 0, '', '', 0, '', 96, '2019-10-19 12:26:07', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X45', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 4, BLINDNESS LEFT EYE CATEGORY 5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:26:21', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X5', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE, CATEGORY 5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:26:51', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X53', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 5, BLINDNESS LEFT EYE CATEGORY 3', 0, 0, '', '', 0, '', 96, '2019-10-19 12:27:09', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X54', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 5, BLINDNESS LEFT EYE CATEGORY 4', 0, 0, '', '', 0, '', 96, '2019-10-19 12:27:24', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.0X55', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 5, BLINDNESS LEFT EYE CATEGORY 5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:27:40', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1', '', '', '', '', '', '', 'BLINDNESS, ONE EYE, LOW VISION OTHER EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:39:16', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.11', '', '', '', '', '', '', 'BLINDNESS, RIGHT EYE, LOW VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:39:29', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.113', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 3, LOW VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:39:45', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1131', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 3, LOW VISION LEFT EYE, CATEGORY 1', 0, 0, '', '', 0, '', 96, '2019-10-19 12:40:29', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1132', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 3, LOW VISION LEFT EYE,CATEGORY 2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:40:48', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.114', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 4, LOW VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:41:05', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1141', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 4, LOW VISION LEFT EYE,CATEGORY 1', 0, 0, '', '', 0, '', 96, '2019-10-19 12:41:21', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1142', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 4, LOW VISION LEFT EYE,CATEGORY 2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:41:42', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.115', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 5, LOW VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:41:59', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1151', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 5, LOW VISION LEFT EYE, CATEGORY 1', 0, 0, '', '', 0, '', 96, '2019-10-19 12:42:15', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1152', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 5, LOW VISION LEFT EYE, CATEGORY 2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:42:30', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.12', '', '', '', '', '', '', 'BLINDNESS, LEFT EYE, LOW VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:42:53', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.121', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 1, BLINDNESS LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:43:06', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1213', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 1, BLINDNESS LEFT EYE, CATEGORY 3', 0, 0, '', '', 0, '', 96, '2019-10-19 12:43:18', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1214', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 1, BLINDNESS LEFT EYE, CATEGORY 4', 0, 0, '', '', 0, '', 96, '2019-10-19 12:43:30', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1215', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 1, BLINDNESS LEFT EYE, CATEGORY 5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:43:43', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.122', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 2, BLINDNESS LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:44:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1223', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 2, BLINDNESS LEFT EYE, CATEGORY 3', 0, 0, '', '', 0, '', 96, '2019-10-19 12:44:13', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1224', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 2, BLINDNESS LEFT EYE, CATEGORY 4', 0, 0, '', '', 0, '', 96, '2019-10-19 12:44:27', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.1225', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 2, BLINDNESS LEFT EYE, CATEGORY 5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:44:42', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2', '', '', '', '', '', '', 'LOW VISION, BOTH EYES', 0, 0, '', '', 0, '', 96, '2019-10-19 12:45:01', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2X', '', '', '', '', '', '', 'LOW VISION, BOTH EYES, DIFFERENT CATEGORY LEVELS', 0, 0, '', '', 0, '', 96, '2019-10-19 12:45:13', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2X1', '', '', '', '', '', '', 'LOW VISION, RIGHT EYE, CATEGORY 1', 0, 0, '', '', 0, '', 96, '2019-10-19 12:45:27', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2X11', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 1, LOW VISION LEFT EYE CATEGORY 1', 0, 0, '', '', 0, '', 96, '2019-10-19 12:45:39', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2X12', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 1, LOW VISION LEFT EYE CATEGORY 2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:45:51', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2X2', '', '', '', '', '', '', 'LOW VISION, RIGHT EYE, CATEGORY 2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:46:03', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2X21', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 2, LOW VISION LEFT EYE CATEGORY 1', 0, 0, '', '', 0, '', 96, '2019-10-19 12:46:19', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.2X22', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 2, LOW VISION LEFT EYE CATEGORY 2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:46:33', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.4', '', '', '', '', '', '', 'BLINDNESS, ONE EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:47:07', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.41', '', '', '', '', '', '', 'BLINDNESS, RIGHT EYE, NORMAL VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:47:32', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.413', '', '', '', '', '', '', 'BLINDNESS, RIGHT EYE, CATEGORY 3', 0, 0, '', '', 0, '', 96, '2019-10-19 12:47:48', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.413A', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 3, NORMAL VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:48:00', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.414', '', '', '', '', '', '', 'BLINDNESS, RIGHT EYE, CATEGORY 4', 0, 0, '', '', 0, '', 96, '2019-10-19 12:48:12', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.414A', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 4, NORMAL VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:48:58', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.415', '', '', '', '', '', '', 'BLINDNESS, RIGHT EYE, CATEGORY 5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:49:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.415A', '', '', '', '', '', '', 'BLINDNESS RIGHT EYE CATEGORY 5, NORMAL VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:49:25', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.42', '', '', '', '', '', '', 'BLINDNESS, LEFT EYE, NORMAL VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:49:39', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.42A', '', '', '', '', '', '', 'BLINDNESS, LEFT EYE, CATEGORY 3-5', 0, 0, '', '', 0, '', 96, '2019-10-19 12:50:11', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.42A3', '', '', '', '', '', '', 'BLINDNESS LEFT EYE CATEGORY 3, NORMAL VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:50:27', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.42A4', '', '', '', '', '', '', 'BLINDNESS LEFT EYE CATEGORY 4, NORMAL VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:50:52', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.42A5', '', '', '', '', '', '', 'BLINDNESS LEFT EYE CATEGORY 5, NORMAL VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:51:07', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.5', '', '', '', '', '', '', 'LOW VISION, ONE EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:51:27', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.51', '', '', '', '', '', '', 'LOW VISION, RIGHT EYE, NORMAL VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:51:40', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.511', '', '', '', '', '', '', 'LOW VISION, RIGHT EYE, CATEGORY 1-2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:51:52', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.511A', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 1, NORMAL VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:52:06', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.512A', '', '', '', '', '', '', 'LOW VISION RIGHT EYE CATEGORY 2, NORMAL VISION LEFT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:55:43', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.52', '', '', '', '', '', '', 'LOW VISION, LEFT EYE, NORMAL VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:55:56', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.52A', '', '', '', '', '', '', 'LOW VISION, LEFT EYE, CATEGORY 1-2', 0, 0, '', '', 0, '', 96, '2019-10-19 12:56:13', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.52A1', '', '', '', '', '', '', 'LOW VISION LEFT EYE CATEGORY 1, NORMAL VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:56:28', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.52A2', '', '', '', '', '', '', 'LOW VISION LEFT EYE CATEGORY 2, NORMAL VISION RIGHT EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:56:43', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, ''),
	(20, '', 'H54.6', '', '', '', '', '', '', 'UNQUALIFIED VISUAL LOSS, ONE EYE', 0, 0, '', '', 0, '', 96, '2019-10-19 12:57:32', 0, '0000-00-00 00:00:00', 0, '0000-00-00 00:00:00', 0, '');");
}


if(imw_error() || count($msg_info)>0)
{
	$msg_info[] = "<br><br><b>Update 190 Failed!</b><br>".$message."<br>".imw_error();
	$color = "red";	
}
else
{	
	$msg_info[] = "<br><br><b>Update 190 Success.</b><br>".$message;
	$color = "green";			
}
?>

<html>
<head>
<title>Update 190</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
<br><br>
<?php if($msg_info!=""){?>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"><?php echo(implode("<br>",$msg_info));?></font>
<?php
@imw_close();
}
?> 
</body>
</html>