
/*Chart_Drawings*/
ALTER TABLE chart_drawings ADD COLUMN import_id INT;

INSERT INTO chart_drawings (form_id,
patient_id,
exam_date,
drw_od_txt,
drw_os_txt,
wnlDraw,
posDraw,
ncDraw,
wnlDrawOd,
wnlDrawOs,
uid,
statusElem,
idoc_drawing_id,
ncDraw_od,
ncDraw_os,
ut_elem,
purged,
purgerId,
purgeTime,
last_opr_id,
exam_name,
exm_drawing,
drawing_insert_update_from,
import_id)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(la_od_txt,'')
,IFNULL(la_os_txt,'')
,IFNULL(wnlDraw,0)
,IFNULL(posDraw,0)
,IFNULL(ncDrawLa,0)
,IFNULL(wnlDrawOd,0)
,IFNULL(wnlDrawOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(idoc_drawing_id,'')
,IFNULL(ncDrawLa_od,0)
,IFNULL(ncDrawLa_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(last_opr_id,0)
,IFNULL('LA','')
,IFNULL(la_drawing,'')
,IFNULL(drawing_insert_update_from ,0)
,la_id
FROM chart_la;

/*Chart_lac_sys*/
INSERT INTO chart_lac_sys (form_id,
patient_id,
exam_date,
lacrimal_system_summary,
sumLacOs,
lacrimal_od,
lacrimal_os,
wnlLacSys,
posLacSys,
ncLacSys,
wnlLacSysOd,
wnlLacSysOs,
uid,
statusElem,
ncLacSys_od,
ncLacSys_os,
ut_elem,
purged,
purgerId,
purgeTime,
modi_note_LacSysArr,
last_opr_id,
wnl_value_LacSys)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(lacrimal_system_summary,'')
,IFNULL(sumLacOs,'')
,IFNULL(lacrimal_od,'')
,IFNULL(lacrimal_os,'')
,IFNULL(wnlLacSys,0)
,IFNULL(posLacSys,0)
,IFNULL(ncLacSys,0)
,IFNULL(wnlLacSysOd,0)
,IFNULL(wnlLacSysOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncLacSys_od,0)
,IFNULL(ncLacSys_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(modi_note_LacSysArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_LacSys,'')
FROM chart_la;


/*chart_lesion*/
INSERT INTO chart_lesion (form_id,
patient_id,
exam_date,
lesion_summary,
sumLesionOs,
lesion_od,
lesion_os,
wnlLesion,
posLesion,
ncLesion,
wnlLesionOd,
wnlLesionOs,
uid,
statusElem,
ncLesion_od,
ncLesion_os,
ut_elem,
purged,
purgerId,
purgeTime,
modi_note_LesionArr,
last_opr_id,
wnl_value_Lesion)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(lesion_summary,'')
,IFNULL(sumLesionOs,'')
,IFNULL(lesion_od,'')
,IFNULL(lesion_os,'')
,IFNULL(wnlLesion,0)
,IFNULL(posLesion,0)
,IFNULL(ncLesion,0)
,IFNULL(wnlLesionOd,0)
,IFNULL(wnlLesionOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncLesion_od,0)
,IFNULL(ncLesion_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(modi_note_LesionArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Lesion,'')
FROM chart_la;

/*chart_lid_pos*/
INSERT INTO chart_lid_pos (form_id,
patient_id,
exam_date,
lid_deformity_position_summary,
sumLidPosOs,
lidposition_od,
lidposition_os,
wnlLidPos,
posLidPos,
ncLidPos,
wnlLidPosOd,
wnlLidPosOs,
uid,
statusElem,
ncLidPos_od,
ncLidPos_os,
ut_elem,
purged,
purgerId,
purgeTime,
modi_note_LidPosArr,
last_opr_id,
wnl_value_LidPos)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(lid_deformity_position_summary,'')
,IFNULL(sumLidPosOs,'')
,IFNULL(lidposition_od,'')
,IFNULL(lidposition_os,'')
,IFNULL(wnlLidPos,0)
,IFNULL(posLidPos,0)
,IFNULL(ncLidPos,0)
,IFNULL(wnlLidPosOd,0)
,IFNULL(wnlLidPosOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncLidPos_od,0)
,IFNULL(ncLidPos_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(modi_note_LidPosArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_LidPos,'')
FROM chart_la;

/*chart_lids*/
INSERT INTO chart_lids (form_id,
patient_id,
exam_date,
lid_od,
lid_os,
wnlLids,
posLids,
ncLids,
lid_conjunctiva_summary,
sumLidsOs,
wnlLidsOd,
wnlLidsOs,
uid,
statusElem,
ncLids_od,
ncLids_os,
ut_elem,
purged,
purgerId,
purgeTime,
modi_note_LidsArr,
last_opr_id,
wnl_value_Lids)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(lid_od,'')
,IFNULL(lid_os,'')
,IFNULL(wnlLids,0)
,IFNULL(posLids,0)
,IFNULL(ncLids,0)
,IFNULL(lid_conjunctiva_summary,'')
,IFNULL(sumLidsOs,'')
,IFNULL(wnlLidsOd,0)
,IFNULL(wnlLidsOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncLids_od,0)
,IFNULL(ncLids_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(modi_note_LidsArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Lids,'')
FROM chart_la
WHERE la_id <>0;

/*---div1----*/
UPDATE chart_lids
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_lids
SET statusELem = ''
WHERE statusElem =',';

/*---div2----*/

UPDATE chart_lesion
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_lesion
SET statusELem = ''
WHERE statusElem =',';


/*---div3----*/

UPDATE chart_lid_pos
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_lid_pos
SET statusELem = ''
WHERE statusElem =',';


/*---div4----*/

UPDATE chart_lac_sys
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_lac_sys
SET statusELem = ''
WHERE statusElem =',';


/*---div5----*/

UPDATE chart_drawings
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Os',statusElem),19),''))
WHERE statusElem <> ''
AND LOWER(IFNULL(exam_name,''))='la' ;

UPDATE chart_drawings
SET statusELem = ''
WHERE statusElem =','
AND LOWER(IFNULL(exam_name,''))='la';



INSERT INTO chart_retinal_exam (form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,retinal_od
,retinal_od_summary
,retinal_os
,retinal_os_summary
,wnlRetinal
,ncRetinal
,posRetinal
,wnlRetinalOd
,wnlRetinalOs
,ncRetinal_od
,ncRetinal_os
,periNotExamined
,peri_ne_eye
,ut_elem
,modi_note_retinalArr
,last_opr_id
,emerstt_lvlSeverityRetFind
,emerstt_macEdFind
,emerstt_comm_p2p
,emerstt_lvlSeverity
,wnl_value_RetinalExam)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(retinal_od,'')
,IFNULL(retinal_od_summary,'')
,IFNULL(retinal_os,'')
,IFNULL(retinal_os_summary,'')
,IFNULL(wnlRetinal,0)
,IFNULL(ncRetinal,0)
,IFNULL(posRetinal,0)
,IFNULL(wnlRetinalOd,0)
,IFNULL(wnlRetinalOs,0)
,IFNULL(ncRetinal_od,0)
,IFNULL(ncRetinal_os,0)
,IFNULL(periNotExamined,0)
,IFNULL(peri_ne_eye,'')
,IFNULL(ut_elem,'')
,IFNULL(modi_note_retinalArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(emerstt_lvlSeverityRetFind,'')
,IFNULL(emerstt_macEdFind,'')
,IFNULL(emerstt_comm_p2p,'')
,IFNULL(emerstt_lvlSeverity,'')
,IFNULL(wnl_value_RetinalExam,'')
FROM chart_rv;


INSERT INTO chart_vitreous(form_id
,patient_id
,exam_date
,vitreous_od
,vitreous_od_summary
,vitreous_os
,vitreous_os_summary
,wnlVitreous
,posVitreous
,ncVitreous
,wnlVitreousOd
,wnlVitreousOs
,uid
,statusElem
,ncVitreous_od
,ncVitreous_os
,purged
,purgerId
,purgeTime
,ut_elem
,modi_note_vitreousArr
,last_opr_id
,wnl_value_Vitreous)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(vitreous_od,'')
,IFNULL(vitreous_od_summary,'')
,IFNULL(vitreous_os,'')
,IFNULL(vitreous_os_summary,'')
,IFNULL(wnlVitreous,0)
,IFNULL(posVitreous,0)
,IFNULL(ncVitreous,0)
,IFNULL(wnlVitreousOd,0)
,IFNULL(wnlVitreousOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncVitreous_od,0)
,IFNULL(ncVitreous_os,0)
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(modi_note_vitreousArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Vitreous,'')
FROM chart_rv;

INSERT INTO chart_blood_vessels(form_id
,patient_id
,exam_date
,blood_vessels_od
,blood_vessels_od_summary
,blood_vessels_os
,blood_vessels_os_summary
,wnlBV
,posBV
,ncBV
,wnlBVOd
,wnlBVOs
,uid
,statusElem
,ncBV_od
,ncBV_os
,purged
,purgerId
,purgeTime
,ut_elem
,last_opr_id
,wnl_value_BV)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(blood_vessels_od,'')
,IFNULL(blood_vessels_od_summary,'')
,IFNULL(blood_vessels_os,'')
,IFNULL(blood_vessels_os_summary,'')
,IFNULL(wnlBV,0)
,IFNULL(posBV,0)
,IFNULL(ncBV,0)
,IFNULL(wnlBVOd,0)
,IFNULL(wnlBVOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncBV_od,0)
,IFNULL(ncBV_os,0)
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_BV,'')
FROM chart_rv;


INSERT INTO chart_macula(form_id
,patient_id
,exam_date
,macula_od
,macula_od_summary
,macula_os
,macula_os_summary
,wnlMacula
,posMacula
,ncMacula
,wnlMaculaOd
,wnlMaculaOs
,uid
,statusElem
,ncMacula_od
,ncMacula_os
,purged
,purgerId
,purgeTime
,ut_elem
,last_opr_id
,wnl_value_Macula)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(macula_od,'')
,IFNULL(macula_od_summary,'')
,IFNULL(macula_os,'')
,IFNULL(macula_os_summary,'')
,IFNULL(wnlMacula,0)
,IFNULL(posMacula,0)
,IFNULL(ncMacula,0)
,IFNULL(wnlMaculaOd,0)
,IFNULL(wnlMaculaOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncMacula_od,0)
,IFNULL(ncMacula_os,0)
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Macula,'')

FROM chart_rv;

INSERT INTO chart_periphery(form_id
,patient_id
,exam_date
,periphery_od
,periphery_os
,wnlPeri
,posPeri
,ncPeri
,periphery_od_summary
,periphery_os_summary
,wnlPeriOd
,wnlPeriOs
,uid
,statusElem
,ncPeri_od
,ncPeri_os
,purged
,purgerId
,purgeTime
,ut_elem
,last_opr_id
,wnl_value_Peri)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(periphery_od,'')
,IFNULL(periphery_os,'')
,IFNULL(wnlPeri,0)
,IFNULL(posPeri,0)
,IFNULL(ncPeri,0)
,IFNULL(periphery_od_summary,'')
,IFNULL(periphery_os_summary,'')
,IFNULL(wnlPeriOd,0)
,IFNULL(wnlPeriOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(ncPeri_od,0)
,IFNULL(ncPeri_os,0)
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Peri,'')
FROM chart_rv;

INSERT INTO chart_drawings (form_id,
patient_id,
exam_date,
drw_od_txt,
drw_os_txt,
wnlDraw,
posDraw,
ncDraw,
wnlDrawOd,
wnlDrawOs,
uid,
statusElem,
idoc_drawing_id,
ncDraw_od,
ncDraw_os,
ut_elem,
purged,
purgerId,
purgeTime,
last_opr_id,
exam_name,
exm_drawing,
drawing_insert_update_from,
import_id)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(od_desc,'')
,IFNULL(os_desc,'')
,IFNULL(wnlDraw,0)
,IFNULL(posDraw,0)
,IFNULL(ncDrawRv,0)
,IFNULL(wnlDrawOd,0)
,IFNULL(wnlDrawOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(idoc_drawing_id,'')
,IFNULL(ncDrawRv_od,0)
,IFNULL(ncDrawRv_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(last_opr_id,0)
,IFNULL('FundusExam','')
,IFNULL(od_drawing,'')
,IFNULL(drawing_insert_update_from ,0)
,rv_id
FROM chart_rv;

/*---div1----*/
UPDATE chart_vitreous
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_vitreous
SET statusELem = ''
WHERE statusElem =',';

/*---div2----*/

UPDATE chart_macula
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_macula
SET statusELem = ''
WHERE statusElem =',';


/*---div3----*/

UPDATE chart_periphery
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_periphery
SET statusELem = ''
WHERE statusElem =',';


/*---div4----*/

UPDATE chart_blood_vessels
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_blood_vessels
SET statusELem = ''
WHERE statusElem =',';


/*---div5----*/

UPDATE chart_drawings
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Os',statusElem),19),''))
WHERE statusElem <> ''
AND LOWER(IFNULL(exam_name,''))='fundusexam' ;

UPDATE chart_drawings
SET statusELem = ''
WHERE statusElem =','
AND LOWER(IFNULL(exam_name,''))='fundusexam';


/*---div7----*/
UPDATE chart_retinal_exam
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div7_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div7_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_retinal_exam
SET statusELem = ''
WHERE statusElem =',';


INSERT INTO chart_conjunctiva (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,conjunctiva_od
,conjunctiva_od_summary
,conjunctiva_os
,conjunctiva_os_summary
,wnlConj
,wnlConjOd
,wnlConjOs
,posConj
,ncConj
,ncConj_od
,ncConj_os
,pen_light
,modi_note_ConjArr
,last_opr_id
,wnl_value_Conjunctiva)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(conjunctiva_od,'')
,IFNULL(conjunctiva_od_summary,'')
,IFNULL(conjunctiva_os,'')
,IFNULL(conjunctiva_os_summary,'')
,IFNULL(wnlConj,0)
,IFNULL(wnlConjOd,0)
,IFNULL(wnlConjOs,0)
,IFNULL(posConj,0)
,IFNULL(ncConj,0)
,IFNULL(ncConj_od,0)
,IFNULL(ncConj_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_ConjArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Conjunctiva,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_cornea (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,cornea_od
,cornea_od_summary
,cornea_os
,cornea_os_summary
,wnlCorn
,wnlCornOd
,wnlCornOs
,posCorn
,ncCorn
,ncCorn_od
,ncCorn_os
,pen_light
,modi_note_CornArr
,last_opr_id
,wnl_value_Cornea
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(cornea_od,'')
,IFNULL(cornea_od_summary,'')
,IFNULL(cornea_os,'')
,IFNULL(cornea_os_summary,'')
,IFNULL(wnlCorn,0)
,IFNULL(wnlCornOd,0)
,IFNULL(wnlCornOs,0)
,IFNULL(posCorn,0)
,IFNULL(ncCorn,0)
,IFNULL(ncCorn_od,0)
,IFNULL(ncCorn_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_CornArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Cornea,'')
FROM chart_slit_lamp_exam;

INSERT INTO chart_ant_chamber (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,anf_chamber_od
,anf_chamber_od_summary
,anf_chamber_os
,anf_chamber_os_summary
,wnlAnt
,wnlAntOd
,wnlAntOs
,posAnt
,ncAnt
,ncAnt_od
,ncAnt_os
,pen_light
,modi_note_AntArr
,last_opr_id
,wnl_value_Ant
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(anf_chamber_od,'')
,IFNULL(anf_chamber_od_summary,'')
,IFNULL(anf_chamber_os,'')
,IFNULL(anf_chamber_os_summary,'')
,IFNULL(wnlAnt,0)
,IFNULL(wnlAntOd,0)
,IFNULL(wnlAntOs,0)
,IFNULL(posAnt,0)
,IFNULL(ncAnt,0)
,IFNULL(ncAnt_od,0)
,IFNULL(ncAnt_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_AntArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Ant,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_iris (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,iris_pupil_od
,iris_pupil_od_summary
,iris_pupil_os
,iris_pupil_os_summary
,wnlIris
,wnlIrisOd
,wnlIrisOs
,posIris
,ncIris
,ncIris_od
,ncIris_os
,pen_light
,modi_note_IrisArr
,last_opr_id
,wnl_value_Iris
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(iris_pupil_od,'')
,IFNULL(iris_pupil_od_summary,'')
,IFNULL(iris_pupil_os,'')
,IFNULL(iris_pupil_os_summary,'')
,IFNULL(wnlIris,0)
,IFNULL(wnlIrisOd,0)
,IFNULL(wnlIrisOs,0)
,IFNULL(posIris,0)
,IFNULL(ncIris,0)
,IFNULL(ncIris_od,0)
,IFNULL(ncIris_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_IrisArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Iris,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_lens (
form_id
,patient_id
,exam_date
,uid
,statusElem
,purged
,purgerId
,purgeTime
,ut_elem
,lens_od
,lens_od_summary
,lens_os
,lens_os_summary
,wnlLens
,wnlLensOd
,wnlLensOs
,posLens
,ncLens
,ncLens_od
,ncLens_os
,pen_light
,modi_note_LensArr
,last_opr_id
,wnl_value_Lens
)
SELECT
form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(ut_elem,'')
,IFNULL(lens_od,'')
,IFNULL(lens_od_summary,'')
,IFNULL(lens_os,'')
,IFNULL(lens_os_summary,'')
,IFNULL(wnlLens,0)
,IFNULL(wnlLensOd,0)
,IFNULL(wnlLensOs,0)
,IFNULL(posLens,0)
,IFNULL(ncLens,0)
,IFNULL(ncLens_od,0)
,IFNULL(ncLens_os,0)
,IFNULL(pen_light,0)
,IFNULL(modi_note_LensArr,'')
,IFNULL(last_opr_id,0)
,IFNULL(wnl_value_Lens,'')
FROM chart_slit_lamp_exam;


INSERT INTO chart_drawings (form_id,
patient_id,
exam_date,
drw_od_txt,
drw_os_txt,
wnlDraw,
posDraw,
ncDraw,
wnlDrawOd,
wnlDrawOs,
uid,
statusElem,
idoc_drawing_id,
ncDraw_od,
ncDraw_os,
ut_elem,
purged,
purgerId,
purgeTime,
last_opr_id,
exam_name,
exm_drawing,
drawing_insert_update_from,
import_id)
SELECT form_id
,IFNULL(patient_id,0)
,IFNULL(exam_date,'0000-00-00 00:00:00')
,IFNULL(cornea_od_desc_1,'')
,IFNULL(cornea_os_desc,'')
,IFNULL(wnlDraw,0)
,IFNULL(posDraw,0)
,IFNULL(ncDrawSle,0)
,IFNULL(wnlDrawOd,0)
,IFNULL(wnlDrawOs,0)
,IFNULL(uid,0)
,IFNULL(statusElem,'')
,IFNULL(idoc_drawing_id,'')
,IFNULL(ncDrawSle_od,0)
,IFNULL(ncDrawSle_os,0)
,IFNULL(ut_elem,'')
,IFNULL(purged,0)
,IFNULL(purgerId,0)
,IFNULL(purgeTime,'0000-00-00 00:00:00')
,IFNULL(last_opr_id,0)
,IFNULL('SLE','')
,IFNULL(conjunctiva_od_drawing,'')
,IFNULL(drawing_insert_update_from ,0)
,sle_id
FROM chart_slit_lamp_exam;

/*---div1----*/
UPDATE chart_conjunctiva
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div1_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_conjunctiva
SET statusELem = ''
WHERE statusElem =',';

/*---div2----*/

UPDATE chart_cornea
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div2_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_cornea
SET statusELem = ''
WHERE statusElem =',';


/*---div3----*/

UPDATE chart_ant_chamber
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div3_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_ant_chamber
SET statusELem = ''
WHERE statusElem =',';


/*---div4----*/

UPDATE chart_iris
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div4_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_iris
SET statusELem = ''
WHERE statusElem =',';


/*---div5----*/

UPDATE chart_lens
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div5_Os',statusElem),19),''))
WHERE statusElem <> '';

UPDATE chart_lens
SET statusELem = ''
WHERE statusElem =',';


/*---div6----*/

UPDATE chart_drawings
SET statusELem = CONCAT(IFNULL(CONCAT(SUBSTR(statusElem, LOCATE ('elem_chng_div6_Od',statusElem),19),','),''),
IFNULL(SUBSTR(statusElem, LOCATE ('elem_chng_div6_Os',statusElem),19),''))
WHERE statusElem <> ''
AND LOWER(IFNULL(exam_name,''))='sle' ;

UPDATE chart_drawings
SET statusELem = ''
WHERE statusElem =','
AND LOWER(IFNULL(exam_name,''))='sle' ;


/* chart_vis_master */

INSERT INTO chart_vis_master
(
patient_id
,form_id
,status_elements
,ut_elem
)
SELECT
IFNULL(patient_id,0)
,form_id
,IFNULL(vis_statusElements,'')
,IFNULL(ut_elem,'')
FROM chart_vision
ORDER BY vis_id;



/* chart_acuity -- index1 Distance*/
INSERT INTO chart_acuity
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Distance'
,'1'
,IFNULL(cv.visSnellan,'')
,IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_dis_od_sel_1,'')
,IFNULL(cv.vis_dis_od_txt_1,'')
,IFNULL(cv.vis_dis_os_sel_1,'')
,IFNULL(cv.vis_dis_os_txt_1,'')
,IFNULL(cv.vis_dis_ou_sel_1,'')
,IFNULL(cv.vis_dis_ou_txt_1,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_1,'')<>''
OR IFNULL(cv.vis_dis_od_txt_1,'')<>''
OR IFNULL(cv.vis_dis_os_sel_1,'')<>''
OR IFNULL(cv.vis_dis_os_txt_1,'')<>''
OR IFNULL(cv.vis_dis_ou_sel_1,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_1,'')<>''
OR IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan,'')<>''
ORDER BY cv.vis_id ;



/* chart_acuity -- index2 Distance*/
INSERT INTO chart_acuity
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Distance'
,'2'
,IFNULL(cv.visSnellan,'')
,IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_dis_od_sel_2,'')
,IFNULL(cv.vis_dis_od_txt_2,'')
,IFNULL(cv.vis_dis_os_sel_2,'')
,IFNULL(cv.vis_dis_os_txt_2,'')
,IFNULL(cv.vis_dis_ou_sel_2,'')
,IFNULL(cv.vis_dis_ou_txt_2,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_2,'')<>''
OR IFNULL(cv.vis_dis_od_txt_2,'')<>''
OR IFNULL(cv.vis_dis_os_sel_2,'')<>''
OR IFNULL(cv.vis_dis_os_txt_2,'')<>''
OR IFNULL(cv.vis_dis_ou_sel_2,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_2,'')<>''
OR IFNULL(cv.vis_dis_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan,'')<>''
ORDER BY cv.vis_id ;


/* chart_acuity -- index3 Distance*/
INSERT INTO chart_acuity
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Ad. Acuity'
,'3'
,''
,IFNULL(cv.vis_dis_act_3,'')
,IFNULL(cv.vis_dis_od_sel_3,'')
,IFNULL(cv.vis_dis_od_txt_3,'')
,IFNULL(cv.vis_dis_os_sel_3,'')
,IFNULL(cv.vis_dis_os_txt_3,'')
,IFNULL(cv.vis_dis_ou_sel_3,'')
,IFNULL(cv.vis_dis_ou_txt_3,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_3,'')<>''
OR IFNULL(cv.vis_dis_od_txt_3,'')<>''
OR IFNULL(cv.vis_dis_os_sel_3,'')<>''
OR IFNULL(cv.vis_dis_os_txt_3,'')<>''
OR IFNULL(cv.vis_dis_ou_sel_3,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_3,'')<>''
OR IFNULL(cv.vis_dis_act_3,'')<>''
ORDER BY cv.vis_id ;


/* chart_acuity -- index4 Distance*/
INSERT INTO chart_acuity
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT
cvm.id
,CASE WHEN cv.examDateDistance IS NOT NULL AND cv.examDateDistance <> '0000-00-00' THEN cv.examDateDistance
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.uid,0)
,'Distance'
,'4'
,''
,IFNULL(cv.vis_dis_act_4,'')
,IFNULL(cv.vis_dis_od_sel_4,'')
,IFNULL(cv.vis_dis_od_txt_4,'')
,IFNULL(cv.vis_dis_od_sel_4,'')
,IFNULL(cv.vis_dis_os_txt_4,'')
,IFNULL(cv.vis_dis_od_sel_4,'')
,IFNULL(cv.vis_dis_ou_txt_4,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_dis_od_sel_4,'')<>''
OR IFNULL(cv.vis_dis_od_txt_4,'')<>''
OR IFNULL(cv.vis_dis_os_txt_4,'')<>''
OR IFNULL(cv.vis_dis_ou_txt_4,'')<>''
OR IFNULL(cv.vis_dis_act_4,'')<>''
ORDER BY cv.vis_id ;



/* chart_acuity -- index1 Near*/
INSERT INTO chart_acuity
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,IFNULL(cv.uid,0)
,'Near'
,'1'
,IFNULL(cv.visSnellan_near,'')
,IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_near_od_sel_1,'')
,IFNULL(cv.vis_near_od_txt_1,'')
,IFNULL(cv.vis_near_os_sel_1,'')
,IFNULL(cv.vis_near_os_txt_1,'')
,IFNULL(cv.vis_near_ou_sel_1,'')
,IFNULL(cv.vis_near_ou_txt_1,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_near_od_sel_1,'')<>''
OR IFNULL(cv.vis_near_od_txt_1,'')<>''
OR IFNULL(cv.vis_near_os_sel_1,'')<>''
OR IFNULL(cv.vis_near_os_txt_1,'')<>''
OR IFNULL(cv.vis_near_ou_sel_1,'')<>''
OR IFNULL(cv.vis_near_ou_txt_1,'')<>''
OR IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan_near,'')<>''
ORDER BY cv.vis_id ;



/* chart_acuity -- index2 Near*/
INSERT INTO chart_acuity
(
id_chart_vis_master
,exam_date
,uid
,sec_name
,sec_indx
,snellen
,ex_desc
,sel_od
,txt_od
,sel_os
,txt_os
,sel_ou
,txt_ou
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,IFNULL(cv.uid,0)
,'Near'
,'2'
,IFNULL(cv.visSnellan_near,'')
,IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))
,IFNULL(cv.vis_near_od_sel_2,'')
,IFNULL(cv.vis_near_od_txt_2,'')
,IFNULL(cv.vis_near_os_sel_2,'')
,IFNULL(cv.vis_near_os_txt_2,'')
,IFNULL(cv.vis_near_ou_sel_2,'')
,IFNULL(cv.vis_near_ou_txt_2,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_near_od_sel_2,'')<>''
OR IFNULL(cv.vis_near_od_txt_2,'')<>''
OR IFNULL(cv.vis_near_os_sel_2,'')<>''
OR IFNULL(cv.vis_near_os_txt_2,'')<>''
OR IFNULL(cv.vis_near_ou_sel_2,'')<>''
OR IFNULL(cv.vis_near_ou_txt_2,'')<>''
OR IFNULL(cv.vis_near_desc,IFNULL(cv.vis_dis_near_desc,''))<>''
OR IFNULL(cv.visSnellan_near,'')<>''
ORDER BY cv.vis_id ;


/* chart_ak */
INSERT INTO chart_ak
(
id_chart_vis_master,
exam_date,
uid,
k_od,
slash_od,
x_od,
k_os,
slash_os,
x_os,
k_type,
ex_desc
)
SELECT
cvm.id
,IFNULL(cv.examDateARAK,IFNULL(cv.exam_date,''))
,cv.uid
,IFNULL(cv.vis_ak_od_k,'')
,IFNULL(cv.vis_ak_od_slash,'')
,IFNULL(cv.vis_ak_od_x,'')
,IFNULL(cv.vis_ak_os_k,'')
,IFNULL(cv.vis_ak_os_slash,'')
,IFNULL(cv.vis_ak_os_x,'')
,IFNULL(cv.vis_ktype,'')
,IFNULL(cv.vis_dis_near_desc,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_ak_od_k,'')<>''
OR IFNULL(cv.vis_ak_od_slash,'')<>''
OR IFNULL(cv.vis_ak_od_x,'')<>''
OR IFNULL(cv.vis_ak_os_k,'')<>''
OR IFNULL(cv.vis_ak_os_slash,'')<>''
OR IFNULL(cv.vis_ak_os_x,'')<>''
OR IFNULL(cv.vis_ktype,'')<>''
OR IFNULL(cv.vis_dis_near_desc,'')<>''
ORDER BY cv.vis_id ;



/* chart_sca - AR*/
INSERT INTO chart_sca
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
sel_od,
s_os,
c_os,
a_os,
sel_os,
ex_desc,
ar_ref_place
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'AR'
,IFNULL(cv.vis_ar_od_s,'')
,IFNULL(cv.vis_ar_od_c,'')
,IFNULL(cv.vis_ar_od_a,'')
,IFNULL(cv.vis_ar_od_sel_1,'')
,IFNULL(cv.vis_ar_os_s,'')
,IFNULL(cv.vis_ar_os_c,'')
,IFNULL(cv.vis_ar_os_a,'')
,IFNULL(cv.vis_ar_os_sel_1,IFNULL(cv.vis_ar_od_sel_1,''))
,IFNULL(cv.vis_ar_ak_desc,'')
,IFNULL(cv.vis_ar_ref_place,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_ar_od_s,'')<>''
OR IFNULL(cv.vis_ar_od_c,'')<>''
OR IFNULL(cv.vis_ar_od_a,'')<>''
OR IFNULL(cv.vis_ar_os_s,'')<>''
OR IFNULL(cv.vis_ar_os_c,'')<>''
OR IFNULL(cv.vis_ar_os_a,'')<>''
OR IFNULL(cv.vis_ar_ref_place,'')<>''
OR IFNULL(cv.vis_ar_ak_desc,'')<>''
ORDER BY cv.vis_id ;


/* chart_sca - ARC*/
INSERT INTO chart_sca
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
sel_od,
s_os,
c_os,
a_os,
sel_os,
ex_desc,
ar_ref_place
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'ARC'
,IFNULL(cv.visCycArOdS,'')
,IFNULL(cv.visCycArOdC,'')
,IFNULL(cv.visCycArOdA,'')
,IFNULL(cv.visCycArOdSel1,'')
,IFNULL(cv.visCycArOsS,'')
,IFNULL(cv.visCycArOsC,'')
,IFNULL(cv.visCycArOsA,'')
,IFNULL(cv.visCycArOsSel1,IFNULL(cv.visCycArOdSel1,''))
,IFNULL(cv.visCycArDesc,'')
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.visCycArOdS,'')<>''
OR IFNULL(cv.visCycArOdC,'')<>''
OR IFNULL(cv.visCycArOdA,'')<>''
OR IFNULL(cv.visCycArOdSel1,'')<>''
OR IFNULL(cv.visCycArOsS,'')<>''
OR IFNULL(cv.visCycArOsC,'')<>''
OR IFNULL(cv.visCycArOsA,'')<>''
OR IFNULL(cv.visCycArOsSel1,'')<>''
OR IFNULL(cv.visCycArDesc,'')<>''
ORDER BY cv.vis_id ;



/* chart_sca - RETINOSCOPY*/
INSERT INTO chart_sca
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
s_os,
c_os,
a_os,
sel_od,
sel_os
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'RETINOSCOPY'
,IFNULL(cv.vis_exo_od_s,'')
,IFNULL(cv.vis_exo_od_c,'')
,IFNULL(cv.vis_exo_od_a,'')
,IFNULL(cv.vis_exo_os_s,'')
,IFNULL(cv.vis_exo_os_c,'')
,IFNULL(cv.vis_exo_os_a,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_exo_od_s,'')<>''
OR IFNULL(cv.vis_exo_od_c,'')<>''
OR IFNULL(cv.vis_exo_od_a,'')<>''
OR IFNULL(cv.vis_exo_os_s,'')<>''
OR IFNULL(cv.vis_exo_os_c,'')<>''
OR IFNULL(cv.vis_exo_os_a,'')<>''
ORDER BY cv.vis_id ;


/* chart_sca - CYCLOPLEGIC*/
INSERT INTO chart_sca
(
id_chart_vis_master,
exam_date,
uid,
sec_name,
s_od,
c_od,
a_od,
s_os,
c_os,
a_os,
sel_od,
sel_os
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,'CYCLOPLEGIC RETINO'
,IFNULL(cv.visCycloOdS,'')
,IFNULL(cv.visCycloOdC,'')
,IFNULL(cv.visCycloOdA,'')
,IFNULL(cv.visCycloOsS,'')
,IFNULL(cv.visCycloOsC,'')
,IFNULL(cv.visCycloOsA,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.visCycloOdS,'')<>''
OR IFNULL(cv.visCycloOdC,'')<>''
OR IFNULL(cv.visCycloOdA,'')<>''
OR IFNULL(cv.visCycloOsS,'')<>''
OR IFNULL(cv.visCycloOsC,'')<>''
OR IFNULL(cv.visCycloOsA,'')<>''
ORDER BY cv.vis_id ;

/* chart_exo - EXOPHTHALMOMETER*/
INSERT INTO chart_exo
(
id_chart_vis_master,
exam_date,
uid,
pd,
pd_od,
pd_os
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,IFNULL(cv.vis_ret_pd,'')
,IFNULL(cv.vis_ret_pd_od,'')
,IFNULL(cv.vis_ret_pd_os,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_ret_pd,'')<>''
OR IFNULL(cv.vis_ret_pd_od,'')<>''
OR IFNULL(cv.vis_ret_pd_os,'')<>''
ORDER BY cv.vis_id ;



/* chart_bat - BAT*/
INSERT INTO chart_bat
(
id_chart_vis_master,
exam_date,
uid,
nl_od,
l_od,
m_od,
h_od,
nl_os,
l_os,
m_os,
h_os,
nl_ou,
l_ou,
m_ou,
h_ou,
ex_desc
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,IFNULL(cv.vis_bat_nl_od,'')
,IFNULL(cv.vis_bat_low_od,'')
,IFNULL(cv.vis_bat_med_od,'')
,IFNULL(cv.vis_bat_high_od,'')
,IFNULL(cv.vis_bat_nl_os,'')
,IFNULL(cv.vis_bat_low_os,'')
,IFNULL(cv.vis_bat_med_os,'')
,IFNULL(cv.vis_bat_high_os,'')
,IFNULL(cv.vis_bat_nl_ou,'')
,IFNULL(cv.vis_bat_low_ou,'')
,IFNULL(cv.vis_bat_med_ou,'')
,IFNULL(cv.vis_bat_high_ou,'')
,IFNULL(cv.vis_bat_desc,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_bat_nl_od,'')<>''
OR IFNULL(cv.vis_bat_low_od,'')<>''
OR IFNULL(cv.vis_bat_med_od,'')<>''
OR IFNULL(cv.vis_bat_high_od,'')<>''
OR IFNULL(cv.vis_bat_nl_os,'')<>''
OR IFNULL(cv.vis_bat_low_os,'')<>''
OR IFNULL(cv.vis_bat_med_os,'')<>''
OR IFNULL(cv.vis_bat_high_os,'')<>''
OR IFNULL(cv.vis_bat_nl_ou,'')<>''
OR IFNULL(cv.vis_bat_low_ou,'')<>''
OR IFNULL(cv.vis_bat_med_ou,'')<>''
OR IFNULL(cv.vis_bat_high_ou,'')<>''
OR IFNULL(cv.vis_bat_desc,'')<>''
ORDER BY cv.vis_id ;



/* chart_pam - PAM*/
INSERT INTO chart_pam
(
id_chart_vis_master,
exam_date,
uid,
txt1_od,
txt2_od,
txt1_os,
txt2_os,
txt1_ou,
txt2_ou,
sel1,
sel2,
ex_desc,
pam
)
SELECT
cvm.id
,IFNULL(cv.exam_date,'')
,cv.uid
,IFNULL(cv.vis_pam_od_txt_1,'')
,IFNULL(cv.vis_pam_od_txt_2,'')
,IFNULL(cv.vis_pam_os_txt_1,'')
,IFNULL(cv.vis_pam_os_txt_2,'')
,IFNULL(cv.vis_pam_ou_txt_1,'')
,IFNULL(cv.vis_pam_ou_txt_2,'')
,IFNULL(cv.vis_pam_od_sel_1,'')
,IFNULL(cv.vis_pam_od_sel_2,'')
,IFNULL(cv.vis_pam_desc,'')
,IFNULL(cv.visPam,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pam_od_txt_1,'')<>''
OR IFNULL(cv.vis_pam_od_txt_2,'')<>''
OR IFNULL(cv.vis_pam_os_txt_1,'')<>''
OR IFNULL(cv.vis_pam_os_txt_2,'')<>''
OR IFNULL(cv.vis_pam_ou_txt_1,'')<>''
OR IFNULL(cv.vis_pam_ou_txt_2,'')<>''
OR IFNULL(cv.vis_pam_od_sel_1,'')<>''
OR IFNULL(cv.vis_pam_od_sel_2,'')<>''
OR IFNULL(cv.vis_pam_desc,'')<>''
OR IFNULL(cv.visPam,'')<>''
ORDER BY cv.vis_id ;


/*Update pc_mr */
UPDATE chart_pc_mr pm INNER JOIN chart_vis_master vm
ON pm.form_id = vm.form_id
SET pm.id_chart_vis_master = vm.id;


/* chart_pc_mr -- PC1*/
INSERT INTO chart_pc_mr
(
id_chart_vis_master,
exam_date,
ex_type,
ex_number,
pc_distance,
pc_near,
ex_desc,
prism_desc,
uid
)
SELECT
cvm.id
,CASE WHEN cv.examDatePC IS NOT NULL AND cv.examDatePC <> '0000-00-00' THEN cv.examDatePC
            ELSE IFNULL(cv.exam_date,'') END
,'PC'
,'1'
,CASE WHEN LOCATE('1',cv.pc_distance)>0 THEN 1 ELSE 0 END
,CASE WHEN cv.pc_near = 'Near' THEN 1 ELSE 0 END
,IFNULL(cv.vis_pc_desc,'')
,IFNULL(cv.visPcPrismDesc_1,'')
,IFNULL(cv.uid,0)
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pc_od_s,'')<>''
OR IFNULL(cv.vis_pc_os_s,'')<>''
OR IFNULL(cv.vis_pc_od_c,'')<>''
OR IFNULL(cv.vis_pc_os_c,'')<>''
OR IFNULL(cv.vis_pc_od_a,'')<>''
OR IFNULL(cv.vis_pc_os_a,'')<>''
OR IFNULL(cv.vis_pc_od_add,'')<>''
OR IFNULL(cv.vis_pc_os_add,'')<>''
OR IFNULL(cv.vis_pc_od_p,'')<>''
OR IFNULL(cv.vis_pc_os_p,'')<>''
OR IFNULL(cv.vis_pc_od_prism,'')<>''
OR IFNULL(cv.vis_pc_os_prism,'')<>''
OR IFNULL(cv.vis_pc_od_slash,'')<>''
OR IFNULL(cv.vis_pc_os_slash,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt,'')<>''
OR IFNULL(cv.pc_near,'')<>''
OR IFNULL(cv.vis_pc_desc,'')<>''
OR IFNULL(cv.visPcPrismDesc_1,'')<>''
ORDER BY cv.vis_id ;


/* chart_pc_mr_values -- PC-OD1*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_pc_od_s,'')
,IFNULL(cv.vis_pc_od_c,'')
,IFNULL(cv.vis_pc_od_a,'')
,IFNULL(cv.vis_pc_od_add,'')
,IFNULL(cv.vis_pc_od_p,'')
,IFNULL(cv.vis_pc_od_prism,'')
,IFNULL(cv.vis_pc_od_slash,'')
,IFNULL(cv.vis_pc_od_sel_1,'')
,IFNULL(cv.vis_pc_od_sel_2,'')
,IFNULL(cv.vis_pc_od_overref_s,'')
,IFNULL(cv.vis_pc_od_overref_c,'')
,IFNULL(cv.vis_pc_od_overref_v,'')
,IFNULL(cv.vis_pc_od_overref_a,'')
,IFNULL(cv.vis_pc_od_near_txt,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_od_s,'')<>''
OR IFNULL(cv.vis_pc_od_c,'')<>''
OR IFNULL(cv.vis_pc_od_a,'')<>''
OR IFNULL(cv.vis_pc_od_add,'')<>''
OR IFNULL(cv.vis_pc_od_p,'')<>''
OR IFNULL(cv.vis_pc_od_prism,'')<>''
OR IFNULL(cv.vis_pc_od_slash,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt,'')<>''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;


/* chart_pc_mr_values -- PC-OS1*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_pc_os_s,'')
,IFNULL(cv.vis_pc_os_c,'')
,IFNULL(cv.vis_pc_os_a,'')
,IFNULL(cv.vis_pc_os_add,'')
,IFNULL(cv.vis_pc_os_p,'')
,IFNULL(cv.vis_pc_os_prism,'')
,IFNULL(cv.vis_pc_os_slash,'')
,IFNULL(cv.vis_pc_os_sel_1,'')
,IFNULL(cv.vis_pc_os_sel_2,'')
,IFNULL(cv.vis_pc_os_overref_s,'')
,IFNULL(cv.vis_pc_os_overref_c,'')
,IFNULL(cv.vis_pc_os_overref_v,'')
,IFNULL(cv.vis_pc_os_overref_a,'')
,IFNULL(cv.vis_pc_os_near_txt,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_os_s,'')<>''
OR IFNULL(cv.vis_pc_os_c,'')<>''
OR IFNULL(cv.vis_pc_os_a,'')<>''
OR IFNULL(cv.vis_pc_os_add,'')<>''
OR IFNULL(cv.vis_pc_os_p,'')<>''
OR IFNULL(cv.vis_pc_os_prism,'')<>''
OR IFNULL(cv.vis_pc_os_slash,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt,'')<>''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;


/* chart_pc_mr -- PC2*/
INSERT INTO chart_pc_mr
(
id_chart_vis_master,
exam_date,
ex_type,
ex_number,
pc_distance,
pc_near,
ex_desc,
prism_desc,
uid
)
SELECT
cvm.id
,CASE WHEN cv.examDatePC IS NOT NULL AND cv.examDatePC <> '0000-00-00' THEN cv.examDatePC
            ELSE IFNULL(cv.exam_date,'') END
,'PC'
,'2'
,CASE WHEN LOCATE('2',cv.pc_distance)>0 THEN 1 ELSE 0 END
,CASE WHEN cv.pc_near_2 = 'Near' THEN 1 ELSE 0 END
,IFNULL(cv.vis_pc_desc_2,'')
,IFNULL(cv.visPcPrismDesc_2,'')
,IFNULL(cv.uid,0)
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pc_od_s_2,'')<>''
OR IFNULL(cv.vis_pc_os_s_2,'')<>''
OR IFNULL(cv.vis_pc_od_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_add_2,'')<>''
OR IFNULL(cv.vis_pc_os_add_2,'')<>''
OR IFNULL(cv.vis_pc_od_p_2,'')<>''
OR IFNULL(cv.vis_pc_os_p_2,'')<>''
OR IFNULL(cv.vis_pc_od_prism_2,'')<>''
OR IFNULL(cv.vis_pc_os_prism_2,'')<>''
OR IFNULL(cv.vis_pc_od_slash_2,'')<>''
OR IFNULL(cv.vis_pc_os_slash_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_2,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_2,'')<>''
OR IFNULL(cv.pc_near_2,'')<>''
OR IFNULL(cv.vis_pc_desc_2,'')<>''
OR IFNULL(cv.visPcPrismDesc_2,'')<>''
ORDER BY cv.vis_id ;

/* chart_pc_mr_values -- PC-OD2*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_pc_od_s_2,'')
,IFNULL(cv.vis_pc_od_c_2,'')
,IFNULL(cv.vis_pc_od_a_2,'')
,IFNULL(cv.vis_pc_od_add_2,'')
,IFNULL(cv.vis_pc_od_p_2,'')
,IFNULL(cv.vis_pc_od_prism_2,'')
,IFNULL(cv.vis_pc_od_slash_2,'')
,IFNULL(cv.vis_pc_od_sel_1_2,'')
,IFNULL(cv.vis_pc_od_sel_2_2,'')
,IFNULL(cv.vis_pc_od_overref_s_2,'')
,IFNULL(cv.vis_pc_od_overref_c_2,'')
,IFNULL(cv.vis_pc_od_overref_v_2,'')
,IFNULL(cv.vis_pc_od_overref_a_2,'')
,IFNULL(cv.vis_pc_od_near_txt_2,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_od_s_2,'')<>''
OR IFNULL(cv.vis_pc_od_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_add_2,'')<>''
OR IFNULL(cv.vis_pc_od_p_2,'')<>''
OR IFNULL(cv.vis_pc_od_prism_2,'')<>''
OR IFNULL(cv.vis_pc_od_slash_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_2,'')<>''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;

/* chart_pc_mr_values -- PC-OS2*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_pc_os_s_2,'')
,IFNULL(cv.vis_pc_os_c_2,'')
,IFNULL(cv.vis_pc_os_a_2,'')
,IFNULL(cv.vis_pc_os_add_2,'')
,IFNULL(cv.vis_pc_os_p_2,'')
,IFNULL(cv.vis_pc_os_prism_2,'')
,IFNULL(cv.vis_pc_os_slash_2,'')
,IFNULL(cv.vis_pc_os_sel_1_2,'')
,IFNULL(cv.vis_pc_os_sel_2_2,'')
,IFNULL(cv.vis_pc_os_overref_s_2,'')
,IFNULL(cv.vis_pc_os_overref_c_2,'')
,IFNULL(cv.vis_pc_os_overref_v_2,'')
,IFNULL(cv.vis_pc_os_overref_a_2,'')
,IFNULL(cv.vis_pc_os_near_txt_2,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_os_s_2,'')<>''
OR IFNULL(cv.vis_pc_os_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_add_2,'')<>''
OR IFNULL(cv.vis_pc_os_p_2,'')<>''
OR IFNULL(cv.vis_pc_os_prism_2,'')<>''
OR IFNULL(cv.vis_pc_os_slash_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_2,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_2,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_2,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_2,'')<>''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;




/* chart_pc_mr -- PC3*/
INSERT INTO chart_pc_mr
(
id_chart_vis_master,
exam_date,
ex_type,
ex_number,
pc_distance,
pc_near,
ex_desc,
prism_desc,
uid
)
SELECT
cvm.id
,CASE WHEN cv.examDatePC IS NOT NULL AND cv.examDatePC <> '0000-00-00' THEN cv.examDatePC
            ELSE IFNULL(cv.exam_date,'') END
,'PC'
,'3'
,CASE WHEN LOCATE('3',cv.pc_distance)>0 THEN 1 ELSE 0 END
,CASE WHEN cv.pc_near_3 = 'Near' THEN 1 ELSE 0 END
,IFNULL(cv.vis_pc_desc_3,'')
,IFNULL(cv.visPcPrismDesc_3,'')
,IFNULL(cv.uid,0)
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_pc_od_s_3,'')<>''
OR IFNULL(cv.vis_pc_os_s_3,'')<>''
OR IFNULL(cv.vis_pc_od_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_add_3,'')<>''
OR IFNULL(cv.vis_pc_os_add_3,'')<>''
OR IFNULL(cv.vis_pc_od_p_3,'')<>''
OR IFNULL(cv.vis_pc_os_p_3,'')<>''
OR IFNULL(cv.vis_pc_od_prism_3,'')<>''
OR IFNULL(cv.vis_pc_os_prism_3,'')<>''
OR IFNULL(cv.vis_pc_od_slash_3,'')<>''
OR IFNULL(cv.vis_pc_os_slash_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_3,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_3,'')<>''
OR IFNULL(cv.pc_near_3,'')<>''
OR IFNULL(cv.vis_pc_desc_3,'')<>''
OR IFNULL(cv.visPcPrismDesc_3,'')<>''
ORDER BY cv.vis_id ;


/* chart_pc_mr_values -- PC-OD3*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_pc_od_s_3,'')
,IFNULL(cv.vis_pc_od_c_3,'')
,IFNULL(cv.vis_pc_od_a_3,'')
,IFNULL(cv.vis_pc_od_add_3,'')
,IFNULL(cv.vis_pc_od_p_3,'')
,IFNULL(cv.vis_pc_od_prism_3,'')
,IFNULL(cv.vis_pc_od_slash_3,'')
,IFNULL(cv.vis_pc_od_sel_1_3,'')
,IFNULL(cv.vis_pc_od_sel_2_3,'')
,IFNULL(cv.vis_pc_od_overref_s_3,'')
,IFNULL(cv.vis_pc_od_overref_c_3,'')
,IFNULL(cv.vis_pc_od_overref_v_3,'')
,IFNULL(cv.vis_pc_od_overref_a_3,'')
,IFNULL(cv.vis_pc_od_near_txt_3,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_od_s_3,'')<>''
OR IFNULL(cv.vis_pc_od_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_add_3,'')<>''
OR IFNULL(cv.vis_pc_od_p_3,'')<>''
OR IFNULL(cv.vis_pc_od_prism_3,'')<>''
OR IFNULL(cv.vis_pc_od_slash_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_od_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_od_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_od_near_txt_3,'')<>''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;

/* chart_pc_mr_values -- PC-OS3*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
ovr_s,
ovr_c,
ovr_v,
ovr_a,
txt_1,
txt_2,
sel2v
)
SELECT
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_pc_os_s_3,'')
,IFNULL(cv.vis_pc_os_c_3,'')
,IFNULL(cv.vis_pc_os_a_3,'')
,IFNULL(cv.vis_pc_os_add_3,'')
,IFNULL(cv.vis_pc_os_p_3,'')
,IFNULL(cv.vis_pc_os_prism_3,'')
,IFNULL(cv.vis_pc_os_slash_3,'')
,IFNULL(cv.vis_pc_os_sel_1_3,'')
,IFNULL(cv.vis_pc_os_sel_2_3,'')
,IFNULL(cv.vis_pc_os_overref_s_3,'')
,IFNULL(cv.vis_pc_os_overref_c_3,'')
,IFNULL(cv.vis_pc_os_overref_v_3,'')
,IFNULL(cv.vis_pc_os_overref_a_3,'')
,IFNULL(cv.vis_pc_os_near_txt_3,'')
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_pc_os_s_3,'')<>''
OR IFNULL(cv.vis_pc_os_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_add_3,'')<>''
OR IFNULL(cv.vis_pc_os_p_3,'')<>''
OR IFNULL(cv.vis_pc_os_prism_3,'')<>''
OR IFNULL(cv.vis_pc_os_slash_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_1_3,'')<>''
OR IFNULL(cv.vis_pc_os_sel_2_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_s_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_c_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_v_3,'')<>''
OR IFNULL(cv.vis_pc_os_overref_a_3,'')<>''
OR IFNULL(cv.vis_pc_os_near_txt_3,'')<>''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR1*/
INSERT INTO chart_pc_mr
(
id_chart_vis_master,
exam_date,
provider_id,
ex_type,
ex_number,
mr_none_given,
mr_cyclopegic,
mr_pres_date,
mr_ou_txt_1,
mr_type,
ex_desc,
prism_desc,
uid,
strhash
)
SELECT
cvm.id
,CASE WHEN cv.examDateMR IS NOT NULL AND cv.examDateMR <> '0000-00-00' THEN cv.examDateMR
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.provider_id,0)
,'MR'
,'1'
,CASE WHEN TRIM(cv.vis_mr_none_given) = 'None Given' OR TRIM(cv.vis_mr_none_given) = 'None' THEN ''
      WHEN TRIM(cv.vis_mr_none_given) = 'Given' THEN 'MR 1'
	  WHEN LOCATE('MR1',cv.vis_mr_none_given)>0 THEN 'MR1'
	  ELSE '' END
,CASE WHEN LOCATE('1',cv.vis_mrcyclopegic)>0 THEN 1 ELSE 0 END
,IFNULL(cv.vis_mr_pres_dt_1,'')
,IFNULL(cv.vis_mr_ou_txt_1,'')
,IFNULL(cv.vis_mr_type1,'')
,IFNULL(cv.vis_mr_desc,'')
,IFNULL(cv.visMrPrismDesc_1,'')
,IFNULL(cv.uid,0)
,IFNULL(SUBSTRING_INDEX(SUBSTRING_INDEX(cv.mr_hash,'i:0;s:32:"',-1),'";i:1',1) ,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_mr_od_s,'') <> ''
OR IFNULL(cv.vis_mr_od_c,'') <> ''
OR IFNULL(cv.vis_mr_od_a,'') <> ''
OR IFNULL(cv.vis_mr_od_add,'') <> ''
OR IFNULL(cv.vis_mr_od_p,'') <> ''
OR IFNULL(cv.vis_mr_od_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_2,'') <> ''
OR IFNULL(cv.visMrOdSel2Vision,'') <> ''
OR IFNULL(cv.vis_mr_os_s,'') <> ''
OR IFNULL(cv.vis_mr_os_c,'') <> ''
OR IFNULL(cv.vis_mr_os_a,'') <> ''
OR IFNULL(cv.vis_mr_os_add,'') <> ''
OR IFNULL(cv.vis_mr_os_p,'') <> ''
OR IFNULL(cv.vis_mr_os_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_2,'') <> ''
OR IFNULL(cv.visMrOsSel2Vision,'') <> ''
OR IFNULL(cv.provider_id,0) <> 0
OR IFNULL(cv.vis_mr_ou_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_desc,'') <> ''
OR IFNULL(cv.vis_mr_none_given,'') <> ''
OR IFNULL(cv.vis_mrcyclopegic,'') <> ''
OR IFNULL(cv.vis_mr_pres_dt_1,'') <> ''
OR IFNULL(cv.visMrPrismDesc_1,'') <> ''
OR IFNULL(cv.vis_mr_type1,'') <> ''
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OD1*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_mr_od_s,'')
,IFNULL(cv.vis_mr_od_c,'')
,IFNULL(cv.vis_mr_od_a,'')
,IFNULL(cv.vis_mr_od_add,'')
,IFNULL(cv.vis_mr_od_p,'')
,IFNULL(cv.vis_mr_od_prism,'')
,IFNULL(cv.vis_mr_od_slash,'')
,IFNULL(cv.vis_mr_od_sel_1,'')
,IFNULL(cv.vis_mr_od_sel_2,'')
,IFNULL(cv.vis_mr_od_txt_1,'')
,IFNULL(cv.vis_mr_od_txt_2,'')
,IFNULL(cv.visMrOdSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_od_s,'') <> ''
OR IFNULL(cv.vis_mr_od_c,'') <> ''
OR IFNULL(cv.vis_mr_od_a,'') <> ''
OR IFNULL(cv.vis_mr_od_add,'') <> ''
OR IFNULL(cv.vis_mr_od_p,'') <> ''
OR IFNULL(cv.vis_mr_od_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_txt_2,'') <> ''
OR IFNULL(cv.visMrOdSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR - OS1*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_mr_os_s,'')
,IFNULL(cv.vis_mr_os_c,'')
,IFNULL(cv.vis_mr_os_a,'')
,IFNULL(cv.vis_mr_os_add,'')
,IFNULL(cv.vis_mr_os_p,'')
,IFNULL(cv.vis_mr_os_prism,'')
,IFNULL(cv.vis_mr_os_slash,'')
,IFNULL(cv.vis_mr_os_sel_1,'')
,IFNULL(cv.vis_mr_os_sel_2,'')
,IFNULL(cv.vis_mr_os_txt_1,'')
,IFNULL(cv.vis_mr_os_txt_2,'')
,IFNULL(cv.visMrOsSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_os_s,'') <> ''
OR IFNULL(cv.vis_mr_os_c,'') <> ''
OR IFNULL(cv.vis_mr_os_a,'') <> ''
OR IFNULL(cv.vis_mr_os_add,'') <> ''
OR IFNULL(cv.vis_mr_os_p,'') <> ''
OR IFNULL(cv.vis_mr_os_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_txt_2,'') <> ''
OR IFNULL(cv.visMrOdSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR2*/
INSERT INTO chart_pc_mr
(
id_chart_vis_master,
exam_date,
provider_id,
ex_type,
ex_number,
mr_none_given,
mr_cyclopegic,
mr_pres_date,
mr_ou_txt_1,
mr_type,
ex_desc,
prism_desc,
uid,
strhash
)
SELECT
cvm.id
,CASE WHEN cv.examDateMR IS NOT NULL AND cv.examDateMR <> '0000-00-00' THEN cv.examDateMR
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.providerIdOther,0)
,'MR'
,'2'
,CASE WHEN cv.vis_mr_none_given = 'None Given' OR cv.vis_mr_none_given = 'None' THEN ''
      WHEN cv.vis_mr_none_given = 'Given' THEN 'MR 1'
	  WHEN LOCATE('MR2',cv.vis_mr_none_given)>0 THEN 'MR2'
	  ELSE '' END
,CASE WHEN LOCATE('2',cv.vis_mrcyclopegic)>0 THEN 1 ELSE 0 END
,IFNULL(cv.vis_mr_pres_dt_2,'')
,IFNULL(cv.vis_mr_ou_given_txt_1,'')
,IFNULL(cv.vis_mr_type2,'')
,IFNULL(cv.vis_mr_desc_other,'')
,IFNULL(cv.visMrPrismDesc_2,'')
,IFNULL(cv.uid,0)
,IFNULL(SUBSTRING_INDEX(SUBSTRING_INDEX(cv.mr_hash,'i:1;s:32:"',-1),'";i:2',1) ,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.vis_mr_od_given_s,'') <> ''
OR IFNULL(cv.vis_mr_od_given_c,'') <> ''
OR IFNULL(cv.vis_mr_od_given_a,'') <> ''
OR IFNULL(cv.vis_mr_od_given_add,'') <> ''
OR IFNULL(cv.vis_mr_od_given_p,'') <> ''
OR IFNULL(cv.vis_mr_od_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision,'') <> ''
OR IFNULL(cv.vis_mr_os_given_s,'') <> ''
OR IFNULL(cv.vis_mr_os_given_c,'') <> ''
OR IFNULL(cv.vis_mr_os_given_a,'') <> ''
OR IFNULL(cv.vis_mr_os_given_add,'') <> ''
OR IFNULL(cv.vis_mr_os_given_p,'') <> ''
OR IFNULL(cv.vis_mr_os_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision,'') <> ''
OR IFNULL(cv.providerIdOther,0) <> 0
OR IFNULL(cv.vis_mr_ou_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_desc_other,'') <> ''
OR IFNULL(cv.vis_mr_none_given,'') <> ''
OR IFNULL(cv.vis_mrcyclopegic,'') <> ''
OR IFNULL(cv.vis_mr_pres_dt_2,'') <> ''
OR IFNULL(cv.visMrPrismDesc_2,'') <> ''
OR IFNULL(cv.vis_mr_type2,'') <> ''
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OD2*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT
MAX(cpm.id)
,'OD'
,IFNULL(cv.vis_mr_od_given_s,'')
,IFNULL(cv.vis_mr_od_given_c,'')
,IFNULL(cv.vis_mr_od_given_a,'')
,IFNULL(cv.vis_mr_od_given_add,'')
,IFNULL(cv.vis_mr_od_given_p,'')
,IFNULL(cv.vis_mr_od_given_prism,'')
,IFNULL(cv.vis_mr_od_given_slash,'')
,IFNULL(cv.vis_mr_od_given_sel_1,'')
,IFNULL(cv.vis_mr_od_given_sel_2,'')
,IFNULL(cv.vis_mr_od_given_txt_1,'')
,IFNULL(cv.vis_mr_od_given_txt_2,'')
,IFNULL(cv.visMrOtherOdSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_od_given_s,'') <> ''
OR IFNULL(cv.vis_mr_od_given_c,'') <> ''
OR IFNULL(cv.vis_mr_od_given_a,'') <> ''
OR IFNULL(cv.vis_mr_od_given_add,'') <> ''
OR IFNULL(cv.vis_mr_od_given_p,'') <> ''
OR IFNULL(cv.vis_mr_od_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_od_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_od_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OS2*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT
MAX(cpm.id)
,'OS'
,IFNULL(cv.vis_mr_os_given_s,'')
,IFNULL(cv.vis_mr_os_given_c,'')
,IFNULL(cv.vis_mr_os_given_a,'')
,IFNULL(cv.vis_mr_os_given_add,'')
,IFNULL(cv.vis_mr_os_given_p,'')
,IFNULL(cv.vis_mr_os_given_prism,'')
,IFNULL(cv.vis_mr_os_given_slash,'')
,IFNULL(cv.vis_mr_os_given_sel_1,'')
,IFNULL(cv.vis_mr_os_given_sel_2,'')
,IFNULL(cv.vis_mr_os_given_txt_1,'')
,IFNULL(cv.vis_mr_os_given_txt_2,'')
,IFNULL(cv.visMrOtherOsSel2Vision,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.vis_mr_os_given_s,'') <> ''
OR IFNULL(cv.vis_mr_os_given_c,'') <> ''
OR IFNULL(cv.vis_mr_os_given_a,'') <> ''
OR IFNULL(cv.vis_mr_os_given_add,'') <> ''
OR IFNULL(cv.vis_mr_os_given_p,'') <> ''
OR IFNULL(cv.vis_mr_os_given_prism,'') <> ''
OR IFNULL(cv.vis_mr_os_given_slash,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_sel_2,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_1,'') <> ''
OR IFNULL(cv.vis_mr_os_given_txt_2,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision,'') <> ''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR3*/
INSERT INTO chart_pc_mr
(
id_chart_vis_master,
exam_date,
provider_id,
ex_type,
ex_number,
mr_none_given,
mr_cyclopegic,
mr_pres_date,
mr_ou_txt_1,
mr_type,
ex_desc,
prism_desc,
uid,
strhash
)
SELECT
cvm.id
,CASE WHEN cv.examDateMR IS NOT NULL AND cv.examDateMR <> '0000-00-00' THEN cv.examDateMR
            ELSE IFNULL(cv.exam_date,'') END
,IFNULL(cv.providerIdOther_3,0)
,'MR'
,'3'
,CASE WHEN cv.vis_mr_none_given = 'None Given' OR cv.vis_mr_none_given = 'None' THEN ''
      WHEN cv.vis_mr_none_given = 'Given' THEN 'MR 1'
	  WHEN LOCATE('MR3',cv.vis_mr_none_given)>0 THEN 'MR3'
	  ELSE '' END
,CASE WHEN LOCATE('3',cv.vis_mrcyclopegic)>0 THEN 1 ELSE 0 END
,IFNULL(cv.vis_mr_pres_dt_3,'')
,IFNULL(cv.visMrOtherOuTxt1_3,'')
,IFNULL(cv.vis_mr_type3,'')
,IFNULL(cv.vis_mr_desc_3,'')
,IFNULL(cv.visMrPrismDesc_3,'')
,IFNULL(cv.uid,0)
,IFNULL(SUBSTRING_INDEX(SUBSTRING_INDEX(cv.mr_hash,'i:2;s:32:"',-1),'";',1) ,'')
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
WHERE IFNULL(cv.visMrOtherOdS_3,'') <> ''
OR IFNULL(cv.visMrOtherOdC_3,'') <> ''
OR IFNULL(cv.visMrOtherOdA_3,'') <> ''
OR IFNULL(cv.visMrOtherOdAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOdP_3,'') <> ''
OR IFNULL(cv.visMrOtherOdPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision_3,'') <> ''
OR IFNULL(cv.visMrOtherOsS_3,'') <> ''
OR IFNULL(cv.visMrOtherOsC_3,'') <> ''
OR IFNULL(cv.visMrOtherOsA_3,'') <> ''
OR IFNULL(cv.visMrOtherOsAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOsP_3,'') <> ''
OR IFNULL(cv.visMrOtherOsPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision_3,'') <> ''
OR IFNULL(cv.providerIdOther_3,0) <> 0
OR IFNULL(cv.visMrOtherOuTxt1_3,'') <> ''
OR IFNULL(cv.vis_mr_desc_3,'') <> ''
OR IFNULL(cv.vis_mr_none_given,'') <> ''
OR IFNULL(cv.vis_mrcyclopegic,'') <> ''
OR IFNULL(cv.vis_mr_pres_dt_3,'') <> ''
OR IFNULL(cv.visMrPrismDesc_3,'') <> ''
OR IFNULL(cv.vis_mr_type3,'') <> ''
ORDER BY cv.vis_id ;


/* chart_pc_mr -- MR - OD3*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT
MAX(cpm.id)
,'OD'
,IFNULL(cv.visMrOtherOdS_3,'')
,IFNULL(cv.visMrOtherOdC_3,'')
,IFNULL(cv.visMrOtherOdA_3,'')
,IFNULL(cv.visMrOtherOdAdd_3,'')
,IFNULL(cv.visMrOtherOdP_3,'')
,IFNULL(cv.visMrOtherOdPrism_3,'')
,IFNULL(cv.visMrOtherOdSlash_3,'')
,IFNULL(cv.visMrOtherOdSel1_3,'')
,IFNULL(cv.visMrOtherOdSel2_3,'')
,IFNULL(cv.visMrOtherOdTxt1_3,'')
,IFNULL(cv.visMrOtherOdTxt2_3,'')
,IFNULL(cv.visMrOtherOdSel2Vision_3,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.visMrOtherOdS_3,'') <> ''
OR IFNULL(cv.visMrOtherOdC_3,'') <> ''
OR IFNULL(cv.visMrOtherOdA_3,'') <> ''
OR IFNULL(cv.visMrOtherOdAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOdP_3,'') <> ''
OR IFNULL(cv.visMrOtherOdPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOdTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOdSel2Vision_3,'') <> ''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;



/* chart_pc_mr -- MR - OS2*/
INSERT INTO chart_pc_mr_values
(
chart_pc_mr_id,
site,
sph,
cyl,
axs,
ad,
prsm_p,
prism,
slash,
sel_1,
sel_2,
txt_1,
txt_2,
sel2v,
ovr_s,
ovr_c,
ovr_v,
ovr_a
)
SELECT
MAX(cpm.id)
,'OS'
,IFNULL(cv.visMrOtherOsS_3,'')
,IFNULL(cv.visMrOtherOsC_3,'')
,IFNULL(cv.visMrOtherOsA_3,'')
,IFNULL(cv.visMrOtherOsAdd_3,'')
,IFNULL(cv.visMrOtherOsP_3,'')
,IFNULL(cv.visMrOtherOsPrism_3,'')
,IFNULL(cv.visMrOtherOsSlash_3,'')
,IFNULL(cv.visMrOtherOsSel1_3,'')
,IFNULL(cv.visMrOtherOsSel2_3,'')
,IFNULL(cv.visMrOtherOsTxt1_3,'')
,IFNULL(cv.visMrOtherOsTxt2_3,'')
,IFNULL(cv.visMrOtherOsSel2Vision_3,'')
,''
,''
,''
,''
FROM chart_vision cv
INNER JOIN chart_vis_master cvm ON cv.form_id = cvm.form_id
INNER JOIN chart_pc_mr cpm ON cpm.id_chart_vis_master = cvm.id
WHERE IFNULL(cv.visMrOtherOsS_3,'') <> ''
OR IFNULL(cv.visMrOtherOsC_3,'') <> ''
OR IFNULL(cv.visMrOtherOsA_3,'') <> ''
OR IFNULL(cv.visMrOtherOsAdd_3,'') <> ''
OR IFNULL(cv.visMrOtherOsP_3,'') <> ''
OR IFNULL(cv.visMrOtherOsPrism_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSlash_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt1_3,'') <> ''
OR IFNULL(cv.visMrOtherOsTxt2_3,'') <> ''
OR IFNULL(cv.visMrOtherOsSel2Vision_3,'') <> ''
GROUP BY cpm.id_chart_vis_master
ORDER BY cv.vis_id ;
