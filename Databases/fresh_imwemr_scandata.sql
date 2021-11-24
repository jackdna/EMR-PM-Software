/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 5.7.36 : Database - demo_imwscan
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `folder_categories` */

DROP TABLE IF EXISTS `folder_categories`;

CREATE TABLE `folder_categories` (
  `folder_categories_id` int(10) NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `parent_id` int(10) NOT NULL,
  `folder_status` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `patient_id` int(10) NOT NULL,
  `alertPhysician` int(2) NOT NULL,
  `favourite` tinyint(2) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_section` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  PRIMARY KEY (`folder_categories_id`),
  KEY `folder_status` (`folder_status`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `folder_categories` */

insert  into `folder_categories`(`folder_categories_id`,`folder_name`,`parent_id`,`folder_status`,`patient_id`,`alertPhysician`,`favourite`,`created_by`,`date_created`,`date_modified`,`modified_by`,`modified_section`) values 
(1,'Visual Field',0,'active',35137,1,0,NULL,NULL,NULL,NULL,NULL),
(2,'ASCAN',0,'active',35149,1,0,NULL,NULL,NULL,NULL,NULL),
(3,'chart notes',0,'active',7,1,0,NULL,NULL,NULL,NULL,NULL),
(4,'testScan',0,'active',34786,1,0,NULL,NULL,NULL,NULL,NULL),
(5,'OCT',0,'active',5,1,0,NULL,NULL,NULL,NULL,NULL),
(6,'VF',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(7,'Office visits',0,'active',24886,1,0,NULL,NULL,NULL,NULL,NULL),
(9,'Demographics',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(11,'Surgery Records',0,'active',35658,1,0,NULL,NULL,NULL,NULL,NULL),
(13,'Fundus Photos',0,'active',30100,1,0,NULL,NULL,NULL,NULL,NULL),
(14,'Photos',0,'active',24886,1,0,NULL,NULL,NULL,NULL,NULL),
(15,'Reports',0,'active',35646,1,0,NULL,NULL,NULL,NULL,NULL),
(16,'Office visits',0,'active',35314,1,0,NULL,NULL,NULL,NULL,NULL),
(17,'fundus photos',0,'active',35505,1,0,NULL,NULL,NULL,NULL,NULL),
(18,'photo',0,'active',35581,1,0,NULL,NULL,NULL,NULL,NULL),
(19,'Fundus Photos',0,'active',32609,1,0,NULL,NULL,NULL,NULL,NULL),
(20,'Chart Notes',0,'active',35581,1,0,NULL,NULL,NULL,NULL,NULL),
(21,'anterior photos',0,'active',35646,1,0,NULL,NULL,NULL,NULL,NULL),
(22,'reports',0,'active',35658,1,0,NULL,NULL,NULL,NULL,NULL),
(24,'photos',0,'active',32833,1,0,NULL,NULL,NULL,NULL,NULL),
(25,'office notes',0,'active',35894,1,0,NULL,NULL,NULL,NULL,NULL),
(26,'Office Visits',0,'active',35117,1,0,NULL,NULL,NULL,NULL,NULL),
(27,'RVC',22,'active',35658,1,0,NULL,NULL,NULL,NULL,NULL),
(28,'photos',0,'active',18245,1,0,NULL,NULL,NULL,NULL,NULL),
(29,'RCC',0,'active',35046,1,0,NULL,NULL,NULL,NULL,NULL),
(30,'Reports',0,'active',35872,1,0,NULL,NULL,NULL,NULL,NULL),
(31,'Old Records',0,'active',35847,1,0,NULL,NULL,NULL,NULL,NULL),
(32,'Photos',0,'active',10021,1,0,NULL,NULL,NULL,NULL,NULL),
(33,'old records',0,'active',35802,1,0,NULL,NULL,NULL,NULL,NULL),
(34,'old records',0,'active',35803,1,0,NULL,NULL,NULL,NULL,NULL),
(35,'RCC',0,'active',27046,1,0,NULL,NULL,NULL,NULL,NULL),
(36,'old records',0,'active',35832,1,0,NULL,NULL,NULL,NULL,NULL),
(37,'REPORTS',0,'active',35666,1,0,NULL,NULL,NULL,NULL,NULL),
(38,'Photos',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(39,'Chart Notes',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(40,'Old Records',0,'active',27279,1,0,NULL,NULL,NULL,NULL,NULL),
(41,'Consults',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(42,'Surgery records',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(43,'Office Visit',0,'active',35181,1,0,NULL,NULL,NULL,NULL,NULL),
(44,'Patient History form',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(46,'Old Records',0,'active',0,1,0,34,'2017-03-15 08:56:07','2017-03-15 08:56:07',34,'adminConMF'),
(49,'Allergies',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(50,'Immunization',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(51,'Medication',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(52,'Sx/Procedures',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(53,'Vital Sign',0,'active',0,1,0,NULL,NULL,NULL,NULL,NULL),
(54,'sub demo',9,'active',38557,0,0,1,'2011-09-26 07:30:35',NULL,NULL,'CNscanDocs'),
(61,'CCD',0,'active',69721,0,0,NULL,NULL,NULL,NULL,NULL),
(62,'CCD',0,'active',69722,0,0,NULL,NULL,NULL,NULL,NULL),
(65,'Outside Labs',0,'active',0,1,0,34,'2017-03-15 08:56:51',NULL,NULL,'adminConMF'),
(67,'CCD',0,'active',69764,0,0,NULL,NULL,NULL,NULL,NULL),
(68,'CCD',0,'active',62633,0,0,NULL,NULL,NULL,NULL,NULL),
(69,'CCD',0,'active',55840,0,0,NULL,NULL,NULL,NULL,NULL),
(70,'CCD',0,'active',70044,0,0,NULL,NULL,NULL,NULL,NULL),
(71,'CCD',0,'active',70045,0,0,NULL,NULL,NULL,NULL,NULL),
(72,'CCD',0,'active',70046,0,0,NULL,NULL,NULL,NULL,NULL),
(73,'CCD',0,'active',70049,0,0,NULL,NULL,NULL,NULL,NULL),
(74,'CCD',0,'active',70048,0,0,NULL,NULL,NULL,NULL,NULL),
(75,'CCD',0,'active',70050,0,0,NULL,NULL,NULL,NULL,NULL);

/*Table structure for table `idoc_drawing` */

DROP TABLE IF EXISTS `idoc_drawing`;

CREATE TABLE `idoc_drawing` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  `red_pixel` longblob NOT NULL COMMENT 'this hold Red pixel data of drawing',
  `green_pixel` longblob NOT NULL COMMENT 'this hold Green pixel data of drawing',
  `blue_pixel` longblob NOT NULL COMMENT 'this hold Blue pixel data of drawing',
  `alpha_pixel` longblob NOT NULL COMMENT 'this hold Alpha pixel data of drawing',
  `toll_image` varchar(255) NOT NULL COMMENT 'this store pre difiend image class',
  `drawing_for` varchar(255) NOT NULL COMMENT 'this store for what this drawing made',
  `drawing_for_master_id` int(11) NOT NULL COMMENT 'this store numaric id of for what this drawing made',
  `drawing_image_path` varchar(255) NOT NULL COMMENT 'this store drawing jpg path',
  `row_created_by` int(11) NOT NULL COMMENT 'this store which operator first time create row',
  `row_created_date_time` datetime NOT NULL COMMENT 'this store row created date time',
  `row_modify_by` int(11) NOT NULL COMMENT 'this store which operator modify this row',
  `row_modify_date_time` datetime NOT NULL COMMENT 'this store row modify date time',
  `patient_test_name` varchar(255) NOT NULL COMMENT 'this store patient test name',
  `patient_test_id` int(11) NOT NULL COMMENT 'this store master id of patient test',
  `patient_id` int(11) NOT NULL COMMENT 'it store patient id for this row drawing',
  `patient_form_id` int(11) NOT NULL COMMENT 'this store form id of patient test',
  `patient_test_image_path` varchar(255) NOT NULL COMMENT 'this store path of patient test',
  `drawing_images_data_points` longtext NOT NULL COMMENT 'this will store javascript array which is string for dragable images',
  `st_carryfwd` int(2) NOT NULL,
  `row_visit_dos` date NOT NULL,
  `deletedby` int(10) NOT NULL,
  `drwNE` varchar(20) NOT NULL,
  `carryfwd_id` int(10) NOT NULL,
  `drw_data_json` longtext NOT NULL,
  PRIMARY KEY (`id`),
  KEY `drawing_for` (`drawing_for`,`drawing_for_master_id`),
  KEY `patient_id` (`patient_id`),
  KEY `patient_form_id` (`patient_form_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='this table store data regarding HTML drawing';

/*Data for the table `idoc_drawing` */

insert  into `idoc_drawing`(`id`,`red_pixel`,`green_pixel`,`blue_pixel`,`alpha_pixel`,`toll_image`,`drawing_for`,`drawing_for_master_id`,`drawing_image_path`,`row_created_by`,`row_created_date_time`,`row_modify_by`,`row_modify_date_time`,`patient_test_name`,`patient_test_id`,`patient_id`,`patient_form_id`,`patient_test_image_path`,`drawing_images_data_points`,`st_carryfwd`,`row_visit_dos`,`deletedby`,`drwNE`,`carryfwd_id`,`drw_data_json`) values 
(1,'','','','','imgLidsAndLacrimalCanvas','LA',1,'/PatientId_1/idoc_drawing/canvas_drawing_image/LA_idoc_drawing_20171220040942_ff17c43b0cbecc62803799_0.png',2,'2017-12-20 03:42:09',0,'0000-00-00 00:00:00','',0,1,1,'','',0,'2017-12-20',0,'',0,'{\"objects\":[{\"type\":\"cPath\",\"originX\":\"center\",\"originY\":\"center\",\"left\":343.3,\"top\":201.5,\"width\":346,\"height\":257,\"fill\":null,\"stroke\":\"#000000\",\"strokeWidth\":1,\"strokeDashArray\":null,\"strokeLineCap\":\"round\",\"strokeLineJoin\":\"round\",\"strokeMiterLimit\":10,\"scaleX\":1,\"scaleY\":1,\"angle\":0,\"flipX\":false,\"flipY\":false,\"opacity\":1,\"shadow\":null,\"visible\":true,\"clipTo\":null,\"backgroundColor\":\"\",\"fillRule\":\"nonzero\",\"globalCompositeOperation\":\"source-over\",\"path\":[[\"M\",372.8000183105469,73],[\"Q\",372.8000183105469,73,373.3000183105469,73],[\"Q\",373.8000183105469,73,370.0500183105469,115],[\"Q\",366.3000183105469,157,347.3000183105469,203.5],[\"Q\",328.3000183105469,250,319.8000183105469,264],[\"Q\",311.3000183105469,278,307.8000183105469,282.5],[\"Q\",304.3000183105469,287,296.3000183105469,295.5],[\"Q\",288.3000183105469,304,286.8000183105469,304.5],[\"Q\",285.3000183105469,305,284.3000183105469,305.5],[\"Q\",283.3000183105469,306,281.8000183105469,306],[\"Q\",280.3000183105469,306,278.3000183105469,306],[\"Q\",276.3000183105469,306,273.8000183105469,306],[\"Q\",271.3000183105469,306,268.3000183105469,306],[\"Q\",265.3000183105469,306,262.8000183105469,306],[\"Q\",260.3000183105469,306,256.8000183105469,306],[\"Q\",253.30001831054687,306,250.30001831054687,306],[\"Q\",247.30001831054687,306,243.30001831054687,306],[\"Q\",239.30001831054687,306,235.80001831054687,306],[\"Q\",232.30001831054687,306,228.30001831054687,306],[\"Q\",224.30001831054687,306,221.30001831054687,306],[\"Q\",218.30001831054687,306,213.80001831054687,305.5],[\"Q\",209.30001831054687,305,205.30001831054687,304.5],[\"Q\",201.30001831054687,304,197.80001831054687,303.5],[\"Q\",194.30001831054687,303,191.30001831054687,302.5],[\"Q\",188.30001831054687,302,186.30001831054687,301],[\"Q\",184.30001831054687,300,181.80001831054687,299],[\"Q\",179.30001831054687,298,177.80001831054687,297.5],[\"Q\",176.30001831054687,297,175.30001831054687,296.5],[\"Q\",174.30001831054687,296,174.30001831054687,295.5],[\"Q\",174.30001831054687,295,173.80001831054687,295],[\"Q\",173.30001831054687,295,173.30001831054687,294.5],[\"Q\",173.30001831054687,294,173.30001831054687,293.5],[\"Q\",173.30001831054687,293,173.30001831054687,291.5],[\"Q\",173.30001831054687,290,172.80001831054687,288],[\"Q\",172.30001831054687,286,171.80001831054687,283],[\"Q\",171.30001831054687,280,171.30001831054687,278],[\"Q\",171.30001831054687,276,170.80001831054687,272],[\"Q\",170.30001831054687,268,170.30001831054687,264],[\"Q\",170.30001831054687,260,170.30001831054687,256],[\"Q\",170.30001831054687,252,170.80001831054687,247],[\"Q\",171.30001831054687,242,175.30001831054687,237],[\"Q\",179.30001831054687,232,184.30001831054687,227],[\"Q\",189.30001831054687,222,193.80001831054687,219],[\"Q\",198.30001831054687,216,206.30001831054687,212],[\"Q\",214.30001831054687,208,222.80001831054687,205.5],[\"Q\",231.30001831054687,203,239.30001831054687,201],[\"Q\",247.30001831054687,199,253.80001831054687,198],[\"Q\",260.3000183105469,197,267.8000183105469,196.5],[\"Q\",275.3000183105469,196,281.3000183105469,195.5],[\"Q\",287.3000183105469,195,292.8000183105469,195],[\"Q\",298.3000183105469,195,305.3000183105469,195],[\"Q\",312.3000183105469,195,318.3000183105469,195],[\"Q\",324.3000183105469,195,330.3000183105469,195.5],[\"Q\",336.3000183105469,196,342.3000183105469,198.5],[\"Q\",348.3000183105469,201,355.3000183105469,203.5],[\"Q\",362.3000183105469,206,368.8000183105469,209],[\"Q\",375.3000183105469,212,381.3000183105469,215.5],[\"Q\",387.3000183105469,219,392.3000183105469,222.5],[\"Q\",397.3000183105469,226,401.8000183105469,229],[\"Q\",406.3000183105469,232,410.8000183105469,235.5],[\"Q\",415.3000183105469,239,418.3000183105469,242],[\"Q\",421.3000183105469,245,424.3000183105469,248],[\"Q\",427.3000183105469,251,430.3000183105469,253.5],[\"Q\",433.3000183105469,256,435.8000183105469,259],[\"Q\",438.3000183105469,262,440.3000183105469,264],[\"Q\",442.3000183105469,266,444.3000183105469,269],[\"Q\",446.3000183105469,272,448.8000183105469,274.5],[\"Q\",451.3000183105469,277,454.8000183105469,280.5],[\"Q\",458.3000183105469,284,461.8000183105469,288],[\"Q\",465.3000183105469,292,469.3000183105469,296.5],[\"Q\",473.3000183105469,301,479.3000183105469,304.5],[\"Q\",485.3000183105469,308,489.3000183105469,311.5],[\"Q\",493.3000183105469,315,497.8000183105469,317.5],[\"Q\",502.3000183105469,320,505.8000183105469,323],[\"Q\",509.3000183105469,326,511.3000183105469,326.5],[\"Q\",513.3000183105469,327,514.3000183105469,328],[\"Q\",515.3000183105469,329,515.8000183105469,329.5],[\"L\",516.3000183105469,330]],\"pathOffset\":{\"x\":343.3000183105469,\"y\":201.5}}],\"background\":\"\"}'),
(2,'','','','','imgLidsAndLacrimalCanvas','LA',7,'/PatientId_1/idoc_drawing/canvas_drawing_image/LA_idoc_drawing_1_20180427103454_4cef7d10178548a14e5b79.png',2,'2017-12-20 03:42:09',0,'0000-00-00 00:00:00','',0,1,7,'','',1,'2017-12-20',0,'',1,'{\"objects\":[{\"type\":\"cPath\",\"originX\":\"center\",\"originY\":\"center\",\"left\":343.3,\"top\":201.5,\"width\":346,\"height\":257,\"fill\":null,\"stroke\":\"#000000\",\"strokeWidth\":1,\"strokeDashArray\":null,\"strokeLineCap\":\"round\",\"strokeLineJoin\":\"round\",\"strokeMiterLimit\":10,\"scaleX\":1,\"scaleY\":1,\"angle\":0,\"flipX\":false,\"flipY\":false,\"opacity\":1,\"shadow\":null,\"visible\":true,\"clipTo\":null,\"backgroundColor\":\"\",\"fillRule\":\"nonzero\",\"globalCompositeOperation\":\"source-over\",\"path\":[[\"M\",372.8000183105469,73],[\"Q\",372.8000183105469,73,373.3000183105469,73],[\"Q\",373.8000183105469,73,370.0500183105469,115],[\"Q\",366.3000183105469,157,347.3000183105469,203.5],[\"Q\",328.3000183105469,250,319.8000183105469,264],[\"Q\",311.3000183105469,278,307.8000183105469,282.5],[\"Q\",304.3000183105469,287,296.3000183105469,295.5],[\"Q\",288.3000183105469,304,286.8000183105469,304.5],[\"Q\",285.3000183105469,305,284.3000183105469,305.5],[\"Q\",283.3000183105469,306,281.8000183105469,306],[\"Q\",280.3000183105469,306,278.3000183105469,306],[\"Q\",276.3000183105469,306,273.8000183105469,306],[\"Q\",271.3000183105469,306,268.3000183105469,306],[\"Q\",265.3000183105469,306,262.8000183105469,306],[\"Q\",260.3000183105469,306,256.8000183105469,306],[\"Q\",253.30001831054687,306,250.30001831054687,306],[\"Q\",247.30001831054687,306,243.30001831054687,306],[\"Q\",239.30001831054687,306,235.80001831054687,306],[\"Q\",232.30001831054687,306,228.30001831054687,306],[\"Q\",224.30001831054687,306,221.30001831054687,306],[\"Q\",218.30001831054687,306,213.80001831054687,305.5],[\"Q\",209.30001831054687,305,205.30001831054687,304.5],[\"Q\",201.30001831054687,304,197.80001831054687,303.5],[\"Q\",194.30001831054687,303,191.30001831054687,302.5],[\"Q\",188.30001831054687,302,186.30001831054687,301],[\"Q\",184.30001831054687,300,181.80001831054687,299],[\"Q\",179.30001831054687,298,177.80001831054687,297.5],[\"Q\",176.30001831054687,297,175.30001831054687,296.5],[\"Q\",174.30001831054687,296,174.30001831054687,295.5],[\"Q\",174.30001831054687,295,173.80001831054687,295],[\"Q\",173.30001831054687,295,173.30001831054687,294.5],[\"Q\",173.30001831054687,294,173.30001831054687,293.5],[\"Q\",173.30001831054687,293,173.30001831054687,291.5],[\"Q\",173.30001831054687,290,172.80001831054687,288],[\"Q\",172.30001831054687,286,171.80001831054687,283],[\"Q\",171.30001831054687,280,171.30001831054687,278],[\"Q\",171.30001831054687,276,170.80001831054687,272],[\"Q\",170.30001831054687,268,170.30001831054687,264],[\"Q\",170.30001831054687,260,170.30001831054687,256],[\"Q\",170.30001831054687,252,170.80001831054687,247],[\"Q\",171.30001831054687,242,175.30001831054687,237],[\"Q\",179.30001831054687,232,184.30001831054687,227],[\"Q\",189.30001831054687,222,193.80001831054687,219],[\"Q\",198.30001831054687,216,206.30001831054687,212],[\"Q\",214.30001831054687,208,222.80001831054687,205.5],[\"Q\",231.30001831054687,203,239.30001831054687,201],[\"Q\",247.30001831054687,199,253.80001831054687,198],[\"Q\",260.3000183105469,197,267.8000183105469,196.5],[\"Q\",275.3000183105469,196,281.3000183105469,195.5],[\"Q\",287.3000183105469,195,292.8000183105469,195],[\"Q\",298.3000183105469,195,305.3000183105469,195],[\"Q\",312.3000183105469,195,318.3000183105469,195],[\"Q\",324.3000183105469,195,330.3000183105469,195.5],[\"Q\",336.3000183105469,196,342.3000183105469,198.5],[\"Q\",348.3000183105469,201,355.3000183105469,203.5],[\"Q\",362.3000183105469,206,368.8000183105469,209],[\"Q\",375.3000183105469,212,381.3000183105469,215.5],[\"Q\",387.3000183105469,219,392.3000183105469,222.5],[\"Q\",397.3000183105469,226,401.8000183105469,229],[\"Q\",406.3000183105469,232,410.8000183105469,235.5],[\"Q\",415.3000183105469,239,418.3000183105469,242],[\"Q\",421.3000183105469,245,424.3000183105469,248],[\"Q\",427.3000183105469,251,430.3000183105469,253.5],[\"Q\",433.3000183105469,256,435.8000183105469,259],[\"Q\",438.3000183105469,262,440.3000183105469,264],[\"Q\",442.3000183105469,266,444.3000183105469,269],[\"Q\",446.3000183105469,272,448.8000183105469,274.5],[\"Q\",451.3000183105469,277,454.8000183105469,280.5],[\"Q\",458.3000183105469,284,461.8000183105469,288],[\"Q\",465.3000183105469,292,469.3000183105469,296.5],[\"Q\",473.3000183105469,301,479.3000183105469,304.5],[\"Q\",485.3000183105469,308,489.3000183105469,311.5],[\"Q\",493.3000183105469,315,497.8000183105469,317.5],[\"Q\",502.3000183105469,320,505.8000183105469,323],[\"Q\",509.3000183105469,326,511.3000183105469,326.5],[\"Q\",513.3000183105469,327,514.3000183105469,328],[\"Q\",515.3000183105469,329,515.8000183105469,329.5],[\"L\",516.3000183105469,330]],\"pathOffset\":{\"x\":343.3000183105469,\"y\":201.5}}],\"background\":\"\"}');

/*Table structure for table `media` */

DROP TABLE IF EXISTS `media`;

CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL DEFAULT '0',
  `filename` varchar(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `filetype` varchar(100) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `set_id` varchar(50) NOT NULL COMMENT 'to group audio and video',
  `filesize` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `source` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `source_id` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `made_by` smallint(6) NOT NULL,
  `made_on` datetime NOT NULL,
  `deleted` smallint(6) NOT NULL DEFAULT '0',
  `deleted_by` smallint(6) NOT NULL,
  `delelted_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `media` */

/*Table structure for table `pt_ccda_import` */

DROP TABLE IF EXISTS `pt_ccda_import`;

CREATE TABLE `pt_ccda_import` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL,
  `file_path` text COLLATE latin1_general_ci NOT NULL,
  `operator` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `file_type` varchar(255) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `pt_ccda_import` */

/*Table structure for table `scan_doc_tbl` */

DROP TABLE IF EXISTS `scan_doc_tbl`;

CREATE TABLE `scan_doc_tbl` (
  `scan_doc_id` int(10) NOT NULL AUTO_INCREMENT,
  `folder_categories_id` int(10) NOT NULL,
  `patient_id` bigint(255) NOT NULL,
  `doc_title` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `doc_type` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `doc_size` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `doc_upload_type` varchar(30) COLLATE latin1_general_ci NOT NULL,
  `upload_date` datetime NOT NULL,
  `vf` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `chart_note` enum('no','yes') COLLATE latin1_general_ci NOT NULL DEFAULT 'no',
  `chart_note_date` date NOT NULL,
  `pdf_url` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `scandoc_operator_id` int(11) NOT NULL,
  `scandoc_comment` text COLLATE latin1_general_ci NOT NULL,
  `upload_operator_id` int(11) NOT NULL,
  `upload_docs_date` datetime NOT NULL,
  `upload_comment` text COLLATE latin1_general_ci NOT NULL,
  `file_path` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `task_physician_id` bigint(11) NOT NULL,
  `task_status` varchar(10) COLLATE latin1_general_ci NOT NULL COMMENT '0-To Review, 1-Reviewed, 2-Deleted',
  `task_review_date` datetime NOT NULL,
  `file_extension` text COLLATE latin1_general_ci NOT NULL,
  `CCDA_type` text COLLATE latin1_general_ci NOT NULL,
  `sch_id` int(11) NOT NULL COMMENT 'pkid of schedule_appointments',
  `direct_attach_id` int(11) NOT NULL COMMENT 'FK_id of direct_attachement table',
  PRIMARY KEY (`scan_doc_id`),
  KEY `task_physician_id` (`task_physician_id`),
  KEY `patient_id` (`patient_id`),
  KEY `scandoctbl_multicol` (`patient_id`,`task_physician_id`,`task_status`,`folder_categories_id`),
  KEY `scandoctbl_ptidtaskphyidtaskstatus` (`patient_id`,`task_physician_id`,`task_status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `scan_doc_tbl` */

/*Table structure for table `scan_doc_tbl_binary` */

DROP TABLE IF EXISTS `scan_doc_tbl_binary`;

CREATE TABLE `scan_doc_tbl_binary` (
  `scan_doc_id` int(10) NOT NULL,
  `scan_blob` longblob NOT NULL,
  `thumb_blob` longblob NOT NULL,
  PRIMARY KEY (`scan_doc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `scan_doc_tbl_binary` */

/*Table structure for table `scans` */

DROP TABLE IF EXISTS `scans`;

CREATE TABLE `scans` (
  `scan_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `image_form` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `scan_or_upload` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `image_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
  `created_date` datetime NOT NULL,
  `file_type` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `testing_docscan_operator` int(11) NOT NULL,
  `test_id` int(10) NOT NULL,
  `testing_docscan` text COLLATE latin1_general_ci NOT NULL,
  `status` int(2) NOT NULL,
  `modi_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `operator_id` int(12) NOT NULL,
  `doc_upload_date` datetime NOT NULL,
  `multi_doc_upload_comment` text COLLATE latin1_general_ci NOT NULL,
  `file_path` varchar(250) COLLATE latin1_general_ci NOT NULL,
  `rename_date` date NOT NULL COMMENT 'it store new rename date of file',
  `rename_performed_by` int(11) NOT NULL COMMENT 'it store who have performed rename',
  `draw_template` int(1) NOT NULL COMMENT 'Show in drawing template',
  `site` int(11) NOT NULL COMMENT ' OS-1\nOD-2 \nOU-3',
  PRIMARY KEY (`scan_id`),
  KEY `image_form` (`image_form`),
  KEY `patient_id` (`patient_id`),
  KEY `test_id` (`test_id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `scans` */

/*Table structure for table `scans_binary` */

DROP TABLE IF EXISTS `scans_binary`;

CREATE TABLE `scans_binary` (
  `scan_id` int(10) NOT NULL,
  `scan_blob` longblob NOT NULL,
  PRIMARY KEY (`scan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci;

/*Data for the table `scans_binary` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
