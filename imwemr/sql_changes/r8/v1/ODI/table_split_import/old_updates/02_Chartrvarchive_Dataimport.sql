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
FROM chart_rv_archive;


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
FROM chart_rv_archive;

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
FROM chart_rv_archive;


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

FROM chart_rv_archive;

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
FROM chart_rv_archive;

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
FROM chart_rv_archive;


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