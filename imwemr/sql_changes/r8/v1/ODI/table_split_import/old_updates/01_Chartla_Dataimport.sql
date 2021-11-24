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



