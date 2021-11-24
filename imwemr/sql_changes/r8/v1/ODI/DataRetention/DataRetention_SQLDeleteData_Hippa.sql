
SET @LogsRecordsToDelDate =  DATE_ADD(Now(), INTERVAL -6 YEAR);

/* Deleting the data from log tables */
DELETE FROM audit_trail WHERE Date_Time < @LogsRecordsToDelDate ;
DELETE FROM tblathenaposts WHERE fldDateTimeCreated < @LogsRecordsToDelDate ;
DELETE FROM hl7_received WHERE saved_on < @LogsRecordsToDelDate ;
DELETE FROM hl7_sent WHERE sent_on < @LogsRecordsToDelDate ;