<?php 
$ignoreAuth = true;
include("../../../../config/globals.php");
/*
msg type field added because we have added send message using Direct and Simple Email in pt portal->Send Message/Direct
*/
$qry = array();

$qry[] = "CREATE TABLE IF NOT EXISTS `route_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(15) NOT NULL,
  `codelist_code` varchar(15) NOT NULL,
  `codelist_extensible` varchar(15) NOT NULL,
  `codelist_name` varchar(50) NOT NULL,
  `route_name` varchar(100) NOT NULL,
  `cdisc_synonyms` varchar(50) NOT NULL,
  `cdisc_definition` varchar(150) NOT NULL,
  `nci_preferred_term` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM ";

$qry[] = "INSERT INTO `route_codes` (`id`, `code`, `codelist_code`, `codelist_extensible`, `codelist_name`, `route_name`, `cdisc_synonyms`, `cdisc_definition`, `nci_preferred_term`) VALUES
(1, 'C66729', '', 'Yes', 'Route of Administration Response', 'ROUTE', 'Route of Administration Response', 'A terminology codelist relevant to the course by which a substance is administered in order to reach the site of action in the body.', 'CDISC SDTM Route of Administration Terminology'),
(2, 'C38192', 'C66729', '', 'Route of Administration Response', 'AURICULAR (OTIC)', '', 'Administration to or by way of the ear. (FDA)', 'Auricular Route of Administration'),
(3, 'C38193', 'C66729', '', 'Route of Administration Response', 'BUCCAL', '', 'Administration directed toward the cheek, generally from within the mouth. (FDA)', 'Buccal Route of Administration'),
(4, 'C38194', 'C66729', '', 'Route of Administration Response', 'CONJUNCTIVAL', '', 'Administration to the conjunctiva, the delicate membrane that lines the eyelids and covers the exposed surface of the eyeball. (FDA)', 'Conjunctival Route of Administration'),
(5, 'C38675', 'C66729', '', 'Route of Administration Response', 'CUTANEOUS', '', 'Administration to the skin. (FDA)', 'Cutaneous Route of Administration'),
(6, 'C38197', 'C66729', '', 'Route of Administration Response', 'DENTAL', '', 'Administration to a tooth or teeth. (FDA)', 'Dental Route of Administration'),
(7, 'C78373', 'C66729', '', 'Route of Administration Response', 'DIETARY', '', 'Administration by way of food or water.', 'Dietary Route of Administration'),
(8, 'C38633', 'C66729', '', 'Route of Administration Response', 'ELECTRO-OSMOSIS', '', 'Administration of through the diffusion of substance through a membrane in an electric field. (FDA)', 'Electro-osmosis Route of Administration'),
(9, 'C38205', 'C66729', '', 'Route of Administration Response', 'ENDOCERVICAL', 'Intracervical Route of Administration', 'Administration within the canal of the cervix uteri. Synonymous with the term intracervical. (FDA)', 'Endocervical Route of Administration'),
(10, 'C38206', 'C66729', '', 'Route of Administration Response', 'ENDOSINUSIAL', '', 'Administration within the nasal sinuses of the head. (FDA)', 'Endosinusial Route of Administration'),
(11, 'C38208', 'C66729', '', 'Route of Administration Response', 'ENDOTRACHEAL', 'Intratracheal Route of Administration', 'Administration directly into the trachea. Synonymous with the term intratracheal. (FDA)', 'Endotracheal Route of Administration'),
(12, 'C38209', 'C66729', '', 'Route of Administration Response', 'ENTERAL', '', 'Administration directly into the intestines. (FDA)', 'Enteral Route of Administration'),
(13, 'C38210', 'C66729', '', 'Route of Administration Response', 'EPIDURAL', '', 'Administration upon or over the dura mater. (FDA)', 'Epidural Route of Administration'),
(14, 'C38211', 'C66729', '', 'Route of Administration Response', 'EXTRA-AMNIOTIC', '', 'Administration to the outside of the membrane enveloping the fetus. (FDA)', 'Extraamniotic Route of Administration'),
(15, 'C38212', 'C66729', '', 'Route of Administration Response', 'EXTRACORPOREAL', '', 'Administration outside of the body. (FDA)', 'Extracorporeal Circulation Route of Administration'),
(16, 'C38200', 'C66729', '', 'Route of Administration Response', 'HEMODIALYSIS', '', 'Administration through hemodialysate fluid. (FDA)', 'Administration via Hemodialysis'),
(17, 'C38215', 'C66729', '', 'Route of Administration Response', 'INFILTRATION', '', 'Administration that results in substances passing into tissue spaces or into cells. (FDA)', 'Infiltration Route of Administration'),
(18, 'C38219', 'C66729', '', 'Route of Administration Response', 'INTERSTITIAL', '', 'Administration to or in the interstices of a tissue. (FDA)', 'Interstitial Route of Administration'),
(19, 'C38220', 'C66729', '', 'Route of Administration Response', 'INTRA-ABDOMINAL', '', 'Administration within the abdomen. (FDA)', 'Intraabdominal Route of Administration'),
(20, 'C38221', 'C66729', '', 'Route of Administration Response', 'INTRA-AMNIOTIC', '', 'Administration within the amnion. (FDA)', 'Intraamniotic Route of Administration'),
(21, 'C38222', 'C66729', '', 'Route of Administration Response', 'INTRA-ARTERIAL', '', 'Administration within an artery or arteries. (FDA)', 'Intraarterial Route of Administration'),
(22, 'C38223', 'C66729', '', 'Route of Administration Response', 'INTRA-ARTICULAR', '', 'Administration within a joint. (FDA)', 'Intraarticular Route of Administration'),
(23, 'C38224', 'C66729', '', 'Route of Administration Response', 'INTRABILIARY', '', 'Administration within the bile, bile ducts or gallbladder. (FDA)', 'Intrabiliary Route of Administration'),
(24, 'C38225', 'C66729', '', 'Route of Administration Response', 'INTRABRONCHIAL', '', 'Administration within a bronchus. (FDA)', 'Intrabronchial Route of Administration'),
(25, 'C38226', 'C66729', '', 'Route of Administration Response', 'INTRABURSAL', '', 'Administration within a bursa. (FDA)', 'Intrabursal Route of Administration'),
(26, 'C64984', 'C66729', '', 'Route of Administration Response', 'INTRACAMERAL', '', 'Administration by injection directly into the anterior chamber of the eye.', 'Intracameral Route of Administration'),
(27, 'C38227', 'C66729', '', 'Route of Administration Response', 'INTRACARDIAC', '', 'Administration within the heart. (FDA)', 'Intracardiac Route of Administration'),
(28, 'C38228', 'C66729', '', 'Route of Administration Response', 'INTRACARTILAGINOUS', '', 'Administration within a cartilage; endochondral. (FDA)', 'Intracartilaginous Route of Administration'),
(29, 'C38229', 'C66729', '', 'Route of Administration Response', 'INTRACAUDAL', '', 'Administration within the cauda equina. (FDA)', 'Intracaudal Route of Administration'),
(30, 'C38230', 'C66729', '', 'Route of Administration Response', 'INTRACAVERNOUS', '', 'Administration within a pathologic cavity, such as occurs in the lung in tuberculosis. (FDA)', 'Intracavernous Route of Administration'),
(31, 'C38231', 'C66729', '', 'Route of Administration Response', 'INTRACAVITARY', '', 'Administration within a non-pathologic cavity, such as that of the cervix, uterus, or penis, or such as that is formed as the result of a wound. (FDA)', 'Intracavitary Route of Administration'),
(32, 'C38232', 'C66729', '', 'Route of Administration Response', 'INTRACEREBRAL', '', 'Administration within the cerebrum. (FDA)', 'Intracerebral Route of Administration'),
(33, 'C38233', 'C66729', '', 'Route of Administration Response', 'INTRACISTERNAL', '', 'Administration within the cisterna magna cerebellomedularis. (FDA)', 'Intracisternal Route of Administration'),
(34, 'C38234', 'C66729', '', 'Route of Administration Response', 'INTRACORNEAL', '', 'Administration within the cornea (the transparent structure forming the anterior part of the fibrous tunic of the eye). (FDA)', 'Intracorneal Route of Administration'),
(35, 'C38217', 'C66729', '', 'Route of Administration Response', 'INTRACORONAL, DENTAL', '', 'Administration of a drug within a portion of a tooth which is covered by enamel and which is separated from the roots by a slightly constricted region', 'Intracoronal Dental Route of Administration'),
(36, 'C38218', 'C66729', '', 'Route of Administration Response', 'INTRACORONARY', '', 'Administration within the coronary arteries. (FDA)', 'Intracoronary Route of Administration'),
(37, 'C38235', 'C66729', '', 'Route of Administration Response', 'INTRACORPORUS CAVERNOSUM', '', 'Administration within the dilatable spaces of the corporus cavernosa of the penis. (FDA)', 'Intracorporus Cavernosum Route of Administration'),
(38, 'C38238', 'C66729', '', 'Route of Administration Response', 'INTRADERMAL', '', 'Administration within the dermis. (FDA)', 'Intradermal Route of Administration'),
(39, 'C38239', 'C66729', '', 'Route of Administration Response', 'INTRADISCAL', '', 'Administration within a disc. (FDA)', 'Intradiscal Route of Administration'),
(40, 'C38240', 'C66729', '', 'Route of Administration Response', 'INTRADUCTAL', '', 'Administration within the duct of a gland. (FDA)', 'Intraductal Route of Administration'),
(41, 'C38241', 'C66729', '', 'Route of Administration Response', 'INTRADUODENAL', '', 'Administration within the duodenum. (FDA)', 'Intraduodenal Route of Administration'),
(42, 'C38242', 'C66729', '', 'Route of Administration Response', 'INTRADURAL', '', 'Administration within or beneath the dura. (FDA)', 'Intradural Route of Administration'),
(43, 'C38243', 'C66729', '', 'Route of Administration Response', 'INTRAEPIDERMAL', '', 'Administration within the epidermis. (FDA)', 'Intraepidermal Route of Administration'),
(44, 'C38245', 'C66729', '', 'Route of Administration Response', 'INTRAESOPHAGEAL', '', 'Administration within the esophagus. (FDA)', 'Intraesophageal Route of Administration'),
(45, 'C38246', 'C66729', '', 'Route of Administration Response', 'INTRAGASTRIC', '', 'Administration within the stomach. (FDA)', 'Intragastric Route of Administration'),
(46, 'C38247', 'C66729', '', 'Route of Administration Response', 'INTRAGINGIVAL', '', 'Administration within the gingivae. (FDA)', 'Intragingival Route of Administration'),
(47, 'C38248', 'C66729', '', 'Route of Administration Response', 'INTRAHEPATIC', '', 'Administration into the liver.', 'Intrahepatic Route of Administration'),
(48, 'C38249', 'C66729', '', 'Route of Administration Response', 'INTRAILEAL', '', 'Administration within the distal portion of the small intestine, from the jejunum to the cecum. (FDA)', 'Intraileal Route of Administration'),
(49, 'C102399', 'C66729', '', 'Route of Administration Response', 'INTRAJEJUNAL', '', 'Administration into the jejunum.', 'Intrajejunal Route of Administration'),
(50, 'C38250', 'C66729', '', 'Route of Administration Response', 'INTRALESIONAL', '', 'Administration within or introduced directly into a localized lesion. (FDA)', 'Intralesional Route of Administration'),
(51, 'C38251', 'C66729', '', 'Route of Administration Response', 'INTRALUMINAL', '', 'Administration within the lumen of a tube. (FDA)', 'Intraluminal Route of Administration'),
(52, 'C38252', 'C66729', '', 'Route of Administration Response', 'INTRALYMPHATIC', '', 'Administration within the lymph. (FDA)', 'Intralymphatic Route of Administration'),
(53, 'C38253', 'C66729', '', 'Route of Administration Response', 'INTRAMEDULLARY', '', 'Administration within the marrow cavity of a bone. (FDA)', 'Intramedullary Route of Administration'),
(54, 'C38254', 'C66729', '', 'Route of Administration Response', 'INTRAMENINGEAL', '', 'Administration within the meninges (the three membranes that envelope the brain and spinal cord). (FDA)', 'Intrameningeal Route of Administration'),
(55, 'C28161', 'C66729', '', 'Route of Administration Response', 'INTRAMUSCULAR', '', 'Administration within a muscle. (FDA)', 'Intramuscular Route of Administration'),
(56, 'C79141', 'C66729', '', 'Route of Administration Response', 'INTRANODAL', '', 'Administration within a lymph node.', 'Intranodal Route of Administration'),
(57, 'C38255', 'C66729', '', 'Route of Administration Response', 'INTRAOCULAR', '', 'Administration within the eye. (FDA)', 'Intraocular Route of Administration'),
(58, 'C64987', 'C66729', '', 'Route of Administration Response', 'INTRAOSSEOUS', '', 'Administration within the marrow of the bone.', 'Intraosseous Route of Administration'),
(59, 'C38256', 'C66729', '', 'Route of Administration Response', 'INTRAOVARIAN', '', 'Administration within the ovary. (FDA)', 'Intraovarian Route of Administration'),
(60, 'C102400', 'C66729', '', 'Route of Administration Response', 'INTRAPALATAL', '', 'Administration into the palate.', 'Intrapalatal Route of Administration'),
(61, 'C119548', 'C66729', '', 'Route of Administration Response', 'INTRAPARENCHYMAL', '', 'Administration within or into the parenchyma of a targeted organ.', 'Intraparenchymal Route of Administration'),
(62, 'C38257', 'C66729', '', 'Route of Administration Response', 'INTRAPERICARDIAL', '', 'Administration within the pericardium. (FDA)', 'Intrapericardial Route of Administration'),
(63, 'C38258', 'C66729', '', 'Route of Administration Response', 'INTRAPERITONEAL', '', 'Administration within the peritoneal cavity. (FDA)', 'Intraperitoneal Route of Administration'),
(64, 'C38259', 'C66729', '', 'Route of Administration Response', 'INTRAPLEURAL', '', 'Administration within the pleura. (FDA)', 'Intrapleural Route of Administration'),
(65, 'C38260', 'C66729', '', 'Route of Administration Response', 'INTRAPROSTATIC', '', 'Administration within the prostate gland. (FDA)', 'Intraprostatic Route of Administration'),
(66, 'C38261', 'C66729', '', 'Route of Administration Response', 'INTRAPULMONARY', '', 'Administration within the lungs or its bronchi. (FDA)', 'Intrapulmonary Route of Administration'),
(67, 'C38262', 'C66729', '', 'Route of Administration Response', 'INTRASINAL', '', 'Administration within the nasal or periorbital sinuses. (FDA)', 'Intrasinal Route of Administration'),
(68, 'C38263', 'C66729', '', 'Route of Administration Response', 'INTRASPINAL', '', 'Administration within the vertebral column. (FDA)', 'Intraspinal Route of Administration'),
(69, 'C65138', 'C66729', '', 'Route of Administration Response', 'INTRASTOMAL', '', 'Administration into a stoma.', 'Administration via Stoma'),
(70, 'C38264', 'C66729', '', 'Route of Administration Response', 'INTRASYNOVIAL', '', 'Administration within the synovial cavity of a joint. (FDA)', 'Intrasynovial Route of Administration'),
(71, 'C38265', 'C66729', '', 'Route of Administration Response', 'INTRATENDINOUS', '', 'Administration within a tendon. (FDA)', 'Intratendinous Route of Administration'),
(72, 'C38266', 'C66729', '', 'Route of Administration Response', 'INTRATESTICULAR', '', 'Administration within the testicle. (FDA)', 'Intratesticular Route of Administration'),
(73, 'C128995', 'C66729', '', 'Route of Administration Response', 'INTRATHALAMIC', '', 'Administration within the thalamus.', 'Intrathalamic Route of Administration'),
(74, 'C38267', 'C66729', '', 'Route of Administration Response', 'INTRATHECAL', '', 'Administration within the cerebrospinal fluid at any level of the cerebrospinal axis, including injection into the cerebral ventricles. (FDA)', 'Intrathecal Route of Administration'),
(75, 'C38207', 'C66729', '', 'Route of Administration Response', 'INTRATHORACIC', '', 'Administration within the thorax (internal to the ribs); synonymous with the term endothoracic. (FDA)', 'Endothoracic Route of Administration'),
(76, 'C38268', 'C66729', '', 'Route of Administration Response', 'INTRATUBULAR', '', 'Administration within the tubules of an organ. (FDA)', 'Intratubular Route of Administration'),
(77, 'C38269', 'C66729', '', 'Route of Administration Response', 'INTRATUMOR', 'Intratumor Route of Administration', 'Administration within a tumor. (FDA)', 'Intratumoral Route of Administration'),
(78, 'C38270', 'C66729', '', 'Route of Administration Response', 'INTRATYMPANIC', '', 'Administration within the auris media. (FDA)', 'Intratympanic Route of Administration'),
(79, 'C38272', 'C66729', '', 'Route of Administration Response', 'INTRAUTERINE', '', 'Administration within the uterus. (FDA)', 'Intrauterine Route of Administration'),
(80, 'C128996', 'C66729', '', 'Route of Administration Response', 'INTRAVAGINAL', '', 'Administration within the vagina.', 'Intravaginal Route of Administration'),
(81, 'C38273', 'C66729', '', 'Route of Administration Response', 'INTRAVASCULAR', '', 'Administration within a vessel or vessels. (FDA)', 'Intravascular Route of Administration'),
(82, 'C38276', 'C66729', '', 'Route of Administration Response', 'INTRAVENOUS', '', 'Administration within or into a vein or veins. (FDA)', 'Intravenous Route of Administration'),
(83, 'C38274', 'C66729', '', 'Route of Administration Response', 'INTRAVENOUS BOLUS', '', 'Administration within or into a vein or veins all at once. (FDA)', 'Intravenous Bolus'),
(84, 'C38279', 'C66729', '', 'Route of Administration Response', 'INTRAVENOUS DRIP', '', 'Administration within or into a vein or veins over a sustained period of time. (FDA)', 'Intravenous Drip'),
(85, 'C38277', 'C66729', '', 'Route of Administration Response', 'INTRAVENTRICULAR', '', 'Administration within a ventricle. (FDA)', 'Intraventricular Route of Administration'),
(86, 'C38278', 'C66729', '', 'Route of Administration Response', 'INTRAVESICAL', '', 'Administration within the bladder. (FDA)', 'Intravesical Route of Administration'),
(87, 'C38280', 'C66729', '', 'Route of Administration Response', 'INTRAVITREAL', '', 'Administration within the vitreous body of the eye. (FDA)', 'Intravitreal Route of Administration'),
(88, 'C38203', 'C66729', '', 'Route of Administration Response', 'IONTOPHORESIS', '', 'Administration by means of an electric current where ions of soluble salts migrate into the tissues of the body. (FDA)', 'Iontophoresis Route of Administration'),
(89, 'C38281', 'C66729', '', 'Route of Administration Response', 'IRRIGATION', '', 'Administration to bathe or flush open wounds or body cavities. (FDA)', 'Irrigation-Route of Administration'),
(90, 'C38282', 'C66729', '', 'Route of Administration Response', 'LARYNGEAL', '', 'Administration directly upon the larynx. (FDA)', 'Laryngeal Route of Administration'),
(91, 'C38284', 'C66729', '', 'Route of Administration Response', 'NASAL', 'Intranasal Route of Administration', 'Administration to the nose; administered by way of the nose. (FDA)', 'Nasal Route of Administration'),
(92, 'C38285', 'C66729', '', 'Route of Administration Response', 'NASOGASTRIC', '', 'Administration through the nose and into the stomach, usually by means of a tube. (FDA)', 'Nasogastric Route of Administration'),
(93, 'C48623', 'C66729', '', 'Route of Administration Response', 'NOT APPLICABLE', '', 'Routes of administration are not applicable. (FDA)', 'Route of Administration Not Applicable'),
(94, 'C38286', 'C66729', '', 'Route of Administration Response', 'OCCLUSIVE DRESSING TECHNIQUE', '', 'Administration by the topical route which is then covered by a dressing which occludes the area. (FDA)', 'Occlusive Dressing Technique'),
(95, 'C38287', 'C66729', '', 'Route of Administration Response', 'OPHTHALMIC', '', 'Administration to the external eye. (FDA)', 'Ophthalmic Route of Administration'),
(96, 'C38288', 'C66729', '', 'Route of Administration Response', 'ORAL', 'Intraoral Route of Administration; PO', 'Administration to or by way of the mouth. (FDA)', 'Oral Route of Administration'),
(97, 'C78374', 'C66729', '', 'Route of Administration Response', 'ORAL GAVAGE', '', 'Administration through the mouth and into the stomach, usually by means of a tube. (NCI)', 'Oral Gavage Route of Administration'),
(98, 'C64906', 'C66729', '', 'Route of Administration Response', 'OROMUCOSAL', '', 'Administration across the mucosa of the oral cavity.', 'Oromucosal Route of Administration'),
(99, 'C38289', 'C66729', '', 'Route of Administration Response', 'OROPHARYNGEAL', '', 'Administration directly to the mouth and pharynx. (FDA)', 'Oropharyngeal Route of Administration'),
(100, 'C38291', 'C66729', '', 'Route of Administration Response', 'PARENTERAL', '', 'Administration by injection, infusion, or implantation. (FDA)', 'Parenteral Route of Administration'),
(101, 'C38676', 'C66729', '', 'Route of Administration Response', 'PERCUTANEOUS', '', 'Administration through the skin. (FDA)', 'Percutaneous Route of Administration'),
(102, 'C38292', 'C66729', '', 'Route of Administration Response', 'PERIARTICULAR', '', 'Administration around a joint. (FDA)', 'Periarticular Route of Administration'),
(103, 'C38677', 'C66729', '', 'Route of Administration Response', 'PERIDURAL', '', 'Administration to the outside of the dura mater of the spinal cord. (FDA)', 'Peridural Route of Administration'),
(104, 'C38293', 'C66729', '', 'Route of Administration Response', 'PERINEURAL', '', 'Administration surrounding a nerve or nerves. (FDA)', 'Perineural Route of Administration'),
(105, 'C38294', 'C66729', '', 'Route of Administration Response', 'PERIODONTAL', '', 'Administration around a tooth. (FDA)', 'Periodontal Route of Administration'),
(106, 'C112396', 'C66729', '', 'Route of Administration Response', 'PERIVENOUS', '', 'Administration into the area surrounding a vein. (NCI)', 'Perivenous Route of Administration'),
(107, 'C38295', 'C66729', '', 'Route of Administration Response', 'RECTAL', '', 'Administration to the rectum. (FDA)', 'Rectal Route of Administration'),
(108, 'C38216', 'C66729', '', 'Route of Administration Response', 'RESPIRATORY (INHALATION)', '', 'Administration within the respiratory tract by inhaling orally or nasally for local or systemic effect. (FDA)', 'Inhalation Route of Administration'),
(109, 'C38296', 'C66729', '', 'Route of Administration Response', 'RETROBULBAR', '', 'Administration behind the pons or behind the eyeball. (FDA)', 'Retrobulbar Route of Administration'),
(110, 'C38198', 'C66729', '', 'Route of Administration Response', 'SOFT TISSUE', '', 'Administration into any soft tissue. (FDA)', 'Soft Tissue Route of Administration'),
(111, 'C38297', 'C66729', '', 'Route of Administration Response', 'SUBARACHNOID', '', 'Administration beneath the arachnoid. (FDA)', 'Subarachnoid Route of Administration'),
(112, 'C38298', 'C66729', '', 'Route of Administration Response', 'SUBCONJUNCTIVAL', '', 'Administration beneath the conjunctiva. (FDA)', 'Subconjunctival Route of Administration'),
(113, 'C38299', 'C66729', '', 'Route of Administration Response', 'SUBCUTANEOUS', 'SC; Subdermal Route of Administration', 'Administration beneath the skin; hypodermic. Synonymous with the term SUBDERMAL. (FDA)', 'Subcutaneous Route of Administration'),
(114, 'C38300', 'C66729', '', 'Route of Administration Response', 'SUBLINGUAL', '', 'Administration beneath the tongue. (FDA)', 'Sublingual Route of Administration'),
(115, 'C38301', 'C66729', '', 'Route of Administration Response', 'SUBMUCOSAL', '', 'Administration beneath the mucous membrane. (FDA)', 'Submucosal Route of Administration'),
(116, 'C79143', 'C66729', '', 'Route of Administration Response', 'SUBRETINAL', '', 'Administration beneath the retina.', 'Subretinal Route of Administration'),
(117, 'C94636', 'C66729', '', 'Route of Administration Response', 'SUBTENON', '', 'Administration by injection through the membrane covering the muscles and nerves at the back of the eyeball.', 'Subtenon Route of Administration'),
(118, 'C128997', 'C66729', '', 'Route of Administration Response', 'SUPRACHOROIDAL', '', 'Administration above the choroid.', 'Suprachoroidal Route of Administration'),
(119, 'C38304', 'C66729', '', 'Route of Administration Response', 'TOPICAL', 'TOP', 'Administration to a particular spot on the outer surface of the body. The E2B term TRANSMAMMARY is a subset of the term TOPICAL. (FDA)', 'Topical Route of Administration'),
(120, 'C38305', 'C66729', '', 'Route of Administration Response', 'TRANSDERMAL', '', 'Administration through the dermal layer of the skin to the systemic circulation by diffusion. (FDA)', 'Transdermal Route of Administration'),
(121, 'C111326', 'C66729', '', 'Route of Administration Response', 'TRANSMAMMARY', '', 'Administration by ingestion of colostrum or breast milk.', 'Transmammary Route of Administration'),
(122, 'C38283', 'C66729', '', 'Route of Administration Response', 'TRANSMUCOSAL', '', 'Administration across the mucosa. (FDA)', 'Mucosal Route of Administration'),
(123, 'C38307', 'C66729', '', 'Route of Administration Response', 'TRANSPLACENTAL', '', 'Administration through or across the placenta. (FDA)', 'Transplacental Route of Administration'),
(124, 'C38308', 'C66729', '', 'Route of Administration Response', 'TRANSTRACHEAL', '', 'Administration through the wall of the trachea. (FDA)', 'Transtracheal Route of Administration'),
(125, 'C38309', 'C66729', '', 'Route of Administration Response', 'TRANSTYMPANIC', '', 'Administration across or through the tympanic cavity. (FDA)', 'Transtympanic Route of Administration'),
(126, 'C38310', 'C66729', '', 'Route of Administration Response', 'UNASSIGNED', '', 'Route of administration has not yet been assigned. (FDA)', 'Unassigned Route of Administration'),
(127, 'C38311', 'C66729', '', 'Route of Administration Response', 'UNKNOWN', '', 'Route of administration is unknown. (FDA)', 'Unknown Route of Administration'),
(128, 'C38312', 'C66729', '', 'Route of Administration Response', 'URETERAL', '', 'Administration into the ureter. (FDA)', 'Ureteral Route of Administration'),
(129, 'C38271', 'C66729', '', 'Route of Administration Response', 'URETHRAL', '', 'Administration into the urethra. (FDA)', 'Intraurethral Route of Administration'),
(130, 'C38313', 'C66729', '', 'Route of Administration Response', 'VAGINAL', '', 'Administration into the vagina. (FDA)', 'Vaginal Route of Administration')
";

foreach($qry as $q){imw_query($q) or $msg_info[] = imw_error();}



if(count($msg_info)>0){
	$msg_info[] = "<br><br><b>Update 15 Failed!</b>";
	$color = "red";
}else{
	$msg_info[] = "<br><br><b>Update 15 completed successfully.</b>";
	$color = "green";
}
?>
<html>
<head>
<title>Update 15</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>
<body>
	<font face="Arial, Helvetica, sans-serif" size="2" color="<?php print $color; ?>"> <?php echo(implode("<br>",$msg_info));?></font>
</body>
</html>