<?php
/*
// Under MIT License
// Distribute, Modify and Contribute under MIT License.
// Use of Software under MIT License.
*/
?>
<?php
/*
File: exam_options.php
Coded in PHP7
Purpose: This file provides chart notes exams summaries . not in use now.
Access Type : Include file
*/
?>
<?php

function getExmSevLoc($finding){

	$t1 = array("Absent","Present","T","1+","2+","3+","4+");
	$t2 = array("1mm","2mm","3mm","4mm","5mm","6mm","7mm","8mm","9mm");
	$t3 = array("T","1+","2+","3+","4+");
	$t4= array("Absent","Trace","1+","2+","3+","4+");
	$t5 = array("Absent","Present");
	$t6= array("Absent","Mild","Moderate","Severe");
	$t7=array("RUL","RLL","LUL","LLL");
	//$t8=array("LUL","LLL");
	$t9=array("Mild","Moderate","Severe");
	$t10=array("Sup","Inf","Tmp");
	$t11=array("V1","V2","V3");
	$t11=array("V1","V2","V3");
	$t12=array("Inferior","Superior","Temporal","Nasal");
	$t13=array("Nas","Temp","Sup","Inf");
	$t14 = array("Absent","T","1+","2+","3+","4+");
	$t15=array("Temporal","Nasal");
	$t16=array("F","PF","EF","Hard");
	$t17=array("Superotemporal","Inferotemporal","Superonasal","Inferonasal");
	$t18=array("F","PF","EF");
	$t19 = array("Absent","1+","2+","3+","4+");
	$t20=array("Low","Medium","High");
	$t21 = array("1mm","2mm","3mm","4mm","5mm");
	$t22 = array("1 mm","2 mm","3 mm","4 mm","5 mm","6 mm","7 mm","8 mm","1.5 mm","2.5 mm","3.5 mm","0.5 mm","0 mm","-1 mm","-2 mm");
	$t23 = array("0","5","10","15","20","25","30","35");
	$t24 =array("+1","+2","+3","+4","+5","0","-3","-2","-1");
	$t25 =array("Small","Medium","Large");
	$t26 =array("Yes","No");


	//Severity
	$arr=array();$arrLoc=array(); //location
	$arr["x"]=array();

	//Pupil
	$arr["Scotopic"]=$arr["Photopic"]=$arr["Dilated"]=$t2;
	$arr["Shape"]=array("Round","Irregular");
	$arr["RL"]=$arr["RA"]=array("Yes","Sluggish","Brisk","None");
	$arr["APD"]=array_merge($t4, array("RAPD"));

	//External
	$arr["Pre-auricular"]=array_merge($t5, array("Lymph Node"));
	$arr["Dacryocystitis"]=$t5;
	$arr["HZV Dermatitis"]=$t11;
	$arr["Proptosis"]=array_merge($t9, array("Absent"));
	$arrLoc["Ecchymosis"]=$t7;
	$arrLoc["Edema"]=$t7;
	$arr["Rosacea"]=$t9;
	$arrLoc["Rosacea"]=$t7;

	$arr["5th Nerve/Pin"]=$arr["5th Nerve/Touch"]=$arr["5th Nerve/Cornea Sensation"]=$arr["5th Nerve/Masseter"]=array_merge($t11, array("Decreased"));
	$arr["7th Nerve Paresis/Hearing"]=$arr["7th Nerve Paresis/Cranial Nerve Abnormalities"]=array("Decreased Upper","Lower","Synkinesis","Spasm");

	$arr["Trauma/Tenderness"]=$t9;
	$arr["Trauma/Palpable Fracture"]=array_merge($t5, $t10);
	$arr["Trauma/Crepitus"]=array_merge($t5, array("Sup","Inf"));
	$arr["Trauma/Exophthalmos"]=$arr["Trauma/Enophthalmos"]=
	$arr["Trauma/Paresthesia/Anesthesia"]=$arr["Trauma/Dermal Abrasion"]=
	$arr["Trauma/Eschar"]=$t5;
	$arr["External/Trauma/Edema"]=array_merge($t5, $t9, $t10);
	$arr["Trauma/Laceration"]=array("Superficial","Deep");

	// L&A
	// Gonioscopy
	$arr["All Quadrant"]=$arr["Superior"]=$arr["Inferior"]=$arr["Nasal"]=$arr["Temporal"]=array("ATM","TM","PTM","SS","CB","Not Visible");
	$arr["All Quadrant/Pigmentation"]=$arr["Superior/Pigmentation"]=$arr["Inferior/Pigmentation"]=$arr["Nasal/Pigmentation"]=$arr["Temporal/Pigmentation"]=$t3;
	$arr["All Quadrant/Iris Convexity"]=$arr["Superior/Iris Convexity"]=$arr["Inferior/Iris Convexity"]=$arr["Nasal/Iris Convexity"]=$arr["Temporal/Iris Convexity"]=array_merge($t3, array("Flat","Concave"));

	//Conjunctiva
	$arr["Conjunctival Chalasis"]=$t4;
	$arr["Conjunctiva SPK"]=$t4;
	$arr["Conjunctiva/Nevus"]=array_merge($t5, $t12);
	$arr["Injection"]=array_merge($t14, $t13, array("Diffuse","Limbal","Interpalpebral"));
	$arr["Mucus Discharge"]=$t14;
	$arr["Follicles"]=$t14;
	$arrLoc["Follicles"]=$t7;

	$arr["Papillae"]=$t14;
	$arrLoc["Papillae"]=$t7;
	$arr["Subconj Hmg"]=array_merge($t14, $t12, array("Diffuse"));
	$arr["Pinguecula"]=array_merge($t14, $t15);
	$arr["Conjunctiva/Foreign Body"]=array_merge($t5, array("Metallic w/rust run","Non Metallic","Suture","Old w/o staining","Penetrating","Non-Penetrating"));
	$arr["Bleb"]=array("Quiet","Diffuse","Localized","Cystic","Thin","Thick","Avascular","Fibrotic","Ring of Steel");
	$arr["Bleb/Vascularity"]=$t9;
	$arr["Bleb/Elevation"]=array("Low","1+","2+","3+");
	$arr["Bleb/Extends for Clock Hours"]=array("1","2","3","4+");
	$arr["Bleb/Seidel Test"]=array("Leak","No Leak");

	//Cornea
	$arr["Dry Eyes/Dec TBUT"]=$t14;
	$arr["Dry Eyes/Dec. Tear Lake"]=$t14;
	$arr["Dry Eyes/SPK"]=$t14;
	$arr["Dry Eyes/Inc. Tear Lake"]=$t14;

	$arr["Dystrophy/Anterior (ABMD/MDF)"]=$t14;
	$arr["Dystrophy/ABMD/MDF"]=$t14;
	$arr["Dystrophy/Stromal"]=$t1;
	$arr["Dystrophy/Posterior"]=array_merge($t14,$t9,array("Guttata","Fuchs","PPD"));
	$arr["Dystrophy/Band Keratopathy"]=array_merge($t15,array("1+","2+","3+","Visual axis"));

	$arr["Trauma/Abrasion"]=array_merge($t5,array("-ve Infiltrate"));
	$arr["Trauma/Irregular Epithelium"]=array("Irregular Epithelium","Pseudo Dendrite");
	$arr["Cornea/Trauma/Foreign Body"]=array_merge($t5,array("Metallic w/rust run","Non Metallic","Suture","Old w/o staining","Penetrating","Non-Penetrating"));
	$arr["Trauma/Part. Thickness Laceration"]=array("Superficial","Deep","-ve Siedel");
	$arr["Trauma/Full Thickness Laceration"]=array("w/ uveal prolapsed","w/o uveal prolapsed","+ve Siedel","-ve Siedel");

	$arr["Infection/Inflammation/Ulcer"]=array("Infectious","Infiltrate","Haze","Neurotrophic","KP's","Endothelial Plaque",
					"Sterile","Peripheral Hypersensitivity Ulcer","Location","Peripheral","Mid Peripheral","Central",
					"Progression","Improving","Worsening","No Change");
	$arr["Infection/Inflammation/Stromal Abscess"]=array("Absent");
	$arr["Infection/Inflammation/HSK"]=array("Dendrite","Geographic Ulcer","Scar");
	$arr["Infection/Inflammation/HZK"]=array("Pseudo Dendrite","Geographic Ulcer","Scar");

	$arr["Pterygium"]=array_merge($t21,$t15,array("Encroaching Pupil"));

	$arr["Edema"]=$t14;
	$arr["Edema/Epithelial (MCE)"]=$t14;
	$arr["Edema/Stromal"]=$t14;
	$arr["Edema/Folds/Striae"]=$t14;

	$arr["Pigmentary deposits"]=$t14;
	$arr["Pigmentary deposits/Vortex"]=$t14;
	$arr["Pigmentary deposits/K-Spindle"]=$t14;

	$arr["Filamentary Keratitis"]=$t9;

	$arr["Contact Lens"]=array("SCL","B-SCL","GPCL");

	$arr["Surgery"]=array("PK","Epithelial Defect","PRK","Scar/Haze","LASIK","Vascularization","RK","Edema",
						"AK","Sutures","LRI","Flap Secure");

	$arr["Cornea/Scar"]=array("Stromal","Anterior","Mid","Posterior","Endothelial","Central","Mid Peripheral","Peripheral");

	$arr["Vascularization"]=array("Sub-epithelial","Stromal","Superficial","Deep",
							"Endothelial","Peripheral","Central","Pannus","Ghost BV","Superior","Inferior","Nasal","Temporal");

	//Ant. Chamber
	$arr["Cell"]=$t14;
	$arr["Flare"]=array_merge($t14, array("Plasmoid Aqueous","Fibrin"));
	$arr["KP"]=array("Pigmented","Non-Pigmented","Fine","Large");
	$arr["Depth"]=array_merge(array("Formed","Deep","Shallow","Flat"),$t9);

	//Iris
	$arr["PI"]=array("Open","Close");
	$arr["Iridectomy"]=array("Peripheral","Sector");
	$arr["Synechiae/Anterior"]=$t5;
	$arr["Synechiae/Posterior"]=$t5;
	$arr["Iris/Nevus"]=array_merge($t5,$t12);
	$arr["Iris/PSX"]=$t14;

	//Lens
	$arr["Nuclear Sclerosis"]=$t14;
	$arr["Cortical"]=$t14;
	$arr["PSC"]=$t14;
	$arr["Lens/PSX"]=$t14;
	$arr["PCO/Secondary Membrane"]=array_merge($t14,array("Open","Present"));
	$arr["IOL"]=array("PC","AC","Toric","IOL axis","Multifocal","Capsule Bag","Sulcus","Aphakia");
	$arr["IOL/De-centered"]=array_merge($t14,array("Nasal","Temp","Sup","Inf","IOL in good position"));
	$arr["PC Haze"]=$t3;

	//Optic Nerve
	$arr["CUP"]=array("Small","Moderate","Large");
	$arr["Superior Rim"]=array("Intact","Thin","Cupped to rim");
	$arr["Inferior Rim"]=array("Intact","Thin","Cupped to rim");

	$arr["Normal Rim"]=array("Pink & Sharp","+SVP");
	$arr["Optic Nerve Hmg"]=$t5;
	$arr["Sloping"]=array_merge($t9,$t12);
	$arr["Notch"]=array_merge($t9,$t12);
	$arr["Pallor"]=array_merge($t9,$t12);
	$arr["Nerve Appearance"]=array("Small","Medium","Large","Tilted Disc");
	$arr["Peri-papillary atrophy"]=array_merge($t5,$t9);
	$arr["Edema"]=array_merge($t5,$t9);
	$arr["Neovascularization"]=array_merge($t5,$t9);

	//Vitreous
	$arr["Vitreous/Hemorrhage"]=array_merge($t5,$t9);
	$arr["PVD"]=array_merge($t5,array("Prominent weiss ring"));
	$arr["Asteroid hyalosis"]=$t1;
	$arr["Vitreous Cells"]=$t1;
	$arr["Pigment"]=$t1;
	$arr["Floaters"]=$t1;

	//Macula
	$arr["Macula/Macular edema"]=array("Absent","Focal","Diffuse","Cystoid");
	$arr["Macula/Drusen"]=array_merge($t14,$t16);
	$arr["Macula/AMD/Drusen"]=array_merge($t14,$t16,array("Soft"));
	$arr["Macula/AMD/RPE Changes"]=array_merge($t14,$t18);
	$arr["Macula/AMD/Geographic Atrophy"]=array_merge($t14,$t18);
	$arr["Macula/AMD/Retinal Pigment Epithelial Detachment"]=$t5;
	$arr["Macula/AMD/CNVM"]=array_merge($t5,array("Subfoveal","Perifoveal","Extrafoveal","Juxtapapillary"));
	$arr["Macula/AMD/SRH"]=array_merge($t5,array("Mild","Moderate","Massive"));
	$arr["Macula/AMD/Subretinal Fluid"]=$t5;
	$arr["Macula/ERM"]=array_merge($t14,$t17);
	$arr["Macula/Cotton Wool Spot"]=array_merge($t17,array("Absent","Macula"));
	$arr["Macula/Retinal Pigment Epithelial Detachment"]=$t5;

	//Periphery
	$arr["Periphery/Peripheral Degeneration/Atrophic changes"]=$t17;
	$arr["Periphery/Peripheral Degeneration/Equatorial Drusen"]=$t17;
	$arr["Periphery/Peripheral Degeneration/Lattice Degeneration"]=$t17;
	$arr["Periphery/Peripheral Degeneration/Reticular Changes"]=$t17;
	$arr["Periphery/Peripheral Degeneration/Retinoschisis"]=$t17;
	$arr["Periphery/Peripheral Degeneration/WWP"]=$t17;
	$arr["Periphery/Peripheral Retinal Hemorrhage"]=array_merge($t17,array("Absent"));
	$arr["Periphery/Peripheral Neovascularization"]=array_merge($t17,array("Absent"));
	$arr["Periphery/Retinal Tear"]=array("Absent","Single","Multiple");
	$arr["Periphery/Retinal Detachment"]=array_merge($t5,$t17,array("Macula On","Macula Off"));

	//Vessels
	$arr["Vessels/Vascular Sheathing"]=$t17;
	$arr["Vessels/Diabetes"]=array("No Retinopathy");
	$arr["Vessels/DR/NPDR"]=array_merge($t9,array("Absent","T"));
	$arr["Vessels/DR/Diabetic macular edema"]=array_merge($t5,array("Center involving","Center Sparing"));
	$arr["Vessels/DR/Hard Exudate"]=$t14;
	$arr["Vessels/DR/Cotton Wool Spots"]=$t14;
	$arr["Vessels/DR/Focal Laser"]=$t5;
	$arr["Vessels/DR/PRP"]=array("Absent","Partial","Complete");
	$arr["Vessels/DR/Neovascularization"]=$t5;
	$arr["Vessels/Nevus"]=$t17;
	$arr["Vessels/Vascular Occlusion/BRVO"]=array_merge($t5,$t17,array("Macular edema"));
	$arr["Vessels/Vascular Occlusion/CRVO"]=array_merge($t5,array("Macular edema"));
	$arr["Vessels/Vascular Occlusion/BRAO"]=$t17;
	$arr["Vessels/Vascular Occlusion/CRAO"]=array_merge($t5,array("Ciliary Artery Sparing"));

	//Retinal Exam
	$arr["Macular edema"]=array("Absent","Focal","Diffuse","Cystoid");
	$arr["Drusen"]=array_merge($t14,$t16);
	$arr["Cotton Wool Spot"]=array_merge($t17,array("Absent","Macula"));
	$arr["Retinal Pigment Epithelial Detachment"]=$t5;
	$arr["Vascular Sheathing"]=$t17;
	$arr["Peripheral Retinal Hemorrhage"]=array_merge($t17,array("Absent"));
	$arr["Peripheral Neovascularization"]=array_merge($t17,array("Absent"));
	$arr["Retinal Tear"]=array("Absent","Single","Multiple");
	$arr["Retinal Detachment"]=array_merge($t5,$t17,array("Macula On","Macula Off"));

	$arr["AMD/Drusen"]=array_merge($t14,$t16,array("Soft"));
	$arr["AMD/RPE Changes"]=array_merge($t14,$t18);
	$arr["AMD/Geographic Atrophy"]=array_merge($t14,$t18);
	$arr["AMD/Retinal Pigment Epithelial Detachment"]=$t5;
	$arr["AMD/CNVM"]=array_merge($t5,array("Subfoveal","Perifoveal","Extrafoveal","Juxtapapillary"));
	$arr["AMD/SRH"]=array_merge($t5,array("Mild","Moderate","Massive"));
	$arr["AMD/Subretinal Fluid"]=$t5;

	$arr["Diabetes"]=array("No Retinopathy");
	$arr["DR/NPDR"]=array_merge($t9,array("Absent","T"));

	$arr["DR/Diabetic macular edema"]=array_merge($t5,array("Center involving","Center Sparing"));
	$arr["DR/Hard Exudate"]=$t14;
	$arr["DR/Cotton Wool Spots"]=$t14;
	$arr["DR/Focal Laser"]=$t5;
	$arr["DR/PRP"]=array("Absent","Partial","Complete");
	$arr["DR/Neovascularization"]=$t5;

	$arr["ERM"]=array_merge($t14,$t17);
	$arr["Nevus"]=$t17;
	$arr["Vascular Occlusion/BRVO"]=array_merge($t5,$t17,array("Macular edema"));
	$arr["Vascular Occlusion/CRVO"]=array_merge($t5,array("Macular edema"));
	$arr["Vascular Occlusion/BRAO"]=$t17;
	$arr["Vascular Occlusion/CRAO"]=array_merge($t5,array("Ciliary Artery Sparing"));

	$arr["Peripheral Degeneration/Atrophic changes"]=$t17;
	$arr["Peripheral Degeneration/Equatorial Drusen"]=$t17;
	$arr["Peripheral Degeneration/Lattice Degeneration"]=$t17;
	$arr["Peripheral Degeneration/Reticular Changes"]=$t17;
	$arr["Peripheral Degeneration/Retinoschisis"]=$t17;
	$arr["Peripheral Degeneration/WWP"]=$t17;

	//Lids
	$arr["Blepharitis"]=$t14;
	$arrLoc["Blepharitis"]=$t7;
	$arr["Angular Blepharitis"]=$t14;
	$arrLoc["Angular Blepharitis"]=$t7;
	$arr["Meibomitis"]=$t14;
	$arrLoc["Meibomitis"]=$t7;
	$arr["Acne Rosacea"]=$t14;
	$arrLoc["Acne Rosacea"]=$t7;
	$arr["Trichiasis"]=$t14;
	$arrLoc["Trichiasis"]=$t7;
	$arr["Trauma/Ecchymosis"]=$t14;
	$arrLoc["Trauma/Ecchymosis"]=$t7;
	$arr["Trauma/Edema"]=$t14;
	$arrLoc["Trauma/Edema"]=$t7;
	$arrLoc["Trauma/Lacerations"]=$t7;

	//upper lid
	$arr["Brow Ptosis/Medial"]=$t19;
	$arr["Brow Ptosis/Lateral"]=$t19;
	$arr["Frontalis Use"]=$t19;
	$arr["Dermatochalasis"]=$t19;

	$arr["Orbital Fat/Medial Fat Prolapse"]=$t19;
	$arr["Orbital Fat/Central Fat Prolapse"]=$t19;
	$arr["Orbital Fat/Sub Brow Fat Prolapse"]=$t19;
	$arr["Lid Crease"]=array_merge($t20,array("Measurements"));

	$arr["Entropion"]=$t19;
	$arr["Ectropion"]=$t19;
	$arr["Lid Laxity"]=$t19;
	$arr["Lid Contour"]=array("Good","Peaked","Nasal Ptosis","Lateral Ptosis");

	$arr["MRD 1"]=$t22;
	$arr["MRD 1 c NS"]=$t22;
	$arr["MRD 1 c brows held up"]=$t22;
	$arr["MRD 2"]=$t22;

	$arr["Sup Scleral Show"]=$t22;
	$arr["VFH"]=$t22;
	$arr["Levator Function"]=$t22;
	//$arr["Lagophthalmos"]=array();

	$arr["Lid Lag"]=$t19;
	$arr["Lacrimal gland prolapse"]=$t19;
	$arr["Ptosis VF/Degree loss without taping"]=$t23;
	$arr["Ptosis VF/Degree Improvement with lid taped"]=$t23;
	$arr["Ptosis VF/% improvement With lids taped"]=$t23;

	$arr["Punctal ectropion"]=$t19;
	$arr["Punctal stenosis"]=$t19;

	//lower lid
	$arr["Lower Lid Position"]=array("Normal","Medial Retraction","Lateral Retraction");
	$arr["Laxity"]=$t19;
	$arr["LCT Laxity"]=$t19;
	$arr["MCT Laxity"]=$t19;
	$arr["Ectropion"]=$t19;

	$arr["Entropion"]=$t19;
	$arr["cicatricial skin changes"]=$t19;
	$arr["Inf Scleral Show"]=$t24;
	//$arr["Lagophthalmus"]=array();

	$arr["Punctal Ectropion"]=$t19;
	$arr["Punctal Stenosis"]=$t19;
	$arr["Medial fat prolapse"]=$t19;
	$arr["Central fat prolapse"]=$t19;

	$arr["Lateral fat prolapse"]=$t19;
	$arr["Tear Trough"]=$t19;
	$arr["Nasojugal fold"]=$t19;

	//Lesion
	$arr["Chalazion"]=$t14;
	$arrLoc["Chalazion"]=$t7;
	$arr["Hordeolum"]=$t14;
	$arrLoc["Hordeolum"]=$t7;
	$arr["Cyst/Inclusion"]=$t14;
	$arrLoc["Cyst/Inclusion"]=$t7;
	$arr["Cyst/Sudoriferous"]=$t14;
	$arrLoc["Cyst/Sudoriferous"]=$t7;
	$arr["Neoplasia"]=array("Basal Cell","Squamous Cell");
	$arr["Hemangioma"]=$t14;
	$arrLoc["Hemangioma"]=$t7;
	$arr["Seborrheic keratosis"]=$t14;
	$arrLoc["Seborrheic keratosis"]=$t7;
	$arr["Intradermal nevus"]=$t14;
	$arrLoc["Intradermal nevus"]=$t7;
	$arr["Hydrocystoma"]=$t14;
	$arrLoc["Hydrocystoma"]=$t7;
	$arr["Hydrocystoma"]=$t14;
	$arrLoc["Hydrocystoma"]=$t7;

	//Lid Position
	$arr["Entropion"]=$t19;
	$arr["Ectropion"]=$t19;
	$arr["Ptosis"]=$t19;
	$arr["Dermatochalasis"]=$t19;
	$arr["Lower Lid Lag"]=$t19;

	//Lacrimal System
	$arr["Puncta"]=array("Absent","Small","Med","Large","Sclerosed","RLL Eversion","Sil Plug","Col Plug","3Mon Plug");
	$arrLoc["Puncta"]=$t7;
	$arr["Lacrimal Duct/Tube Stent"]=array("Bicanalicular");
	$arrLoc["Lacrimal Duct/Tube Stent"]=$t7;
	$arr["Lacrimal Sac"]=array("Dacryocystitis","Mass");
	$arr["Punctal Discharge"]=array_merge($t5,array("Upper","Lower","Left","Right"));

	// Advance
	$arr["Tear Meniscus"]=array("Normal","Decreased","Increased");
	$arr["Upper Puncta"]=$t5;
	$arr["Upper Puncta/Size"]=$t25;
	$arr["Upper Puncta/Stenosis"]=$t19;
	$arr["Upper Puncta/Obstruction"]=$t5;
	$arr["Lower Puncta"]=$t5;
	$arr["Lower Puncta/Size"]=$t25;
	$arr["Lower Puncta/Stenosis"]=$t19;
	$arr["Lower Puncta/Obstruction"]=$t5;
	$arr["Lacrimal Probing/Upper Canalicular Stenosis"]=$t19;
	$arr["Lacrimal Probing/Lower Canalicular Stenosis"]=$t19;
	$arr["Lacrimal Probing/Common Canalicular Stenosis"]=$t19;
	$arr["Lacrimal Probing/Duct stenosis"]=array_merge($t19,array("Obstructed"));
	$arr["Nasal Exam/Nasal Endoscopy"]=$t26;
	$arr["Nasal Exam/Septum"]=array("Normal","Thickened");
	$arr["Nasal Exam/Deviated Septum"]=array_merge($t19,array("Right","Left"));
	$arr["Nasal Exam/Middle Turbinate"]=$t25;
	$arr["Nasal Exam/Inf. Turbinate"]=$t25;
	$arr["Nasal Exam/Polyps"]=$t19;
	$arr["Nasal Exam/Inflammation"]=$t19;

	//$arr["x"]=array();

	//Clinical exam extensions
	if(!empty($finding) && count($arr[$finding])<=0){
		$oExmXml = new ExamXml();
		list($arr[$finding], $arrLoc[$finding]) = $oExmXml->check_ee_findings($finding);
	}


	//remove Absent and -ve from findings list
	$arrsev =array();
	if(count($arr[$finding])>1){
		$a1 = $arr[$finding];
		$a2 = array("Absent","-ve");
		$arrsev = array_values(array_diff($a1,$a2));
	}

	//check severity
	return array("Severity"=>$arrsev, "Location"=>$arrLoc[$finding]);
}
?>
