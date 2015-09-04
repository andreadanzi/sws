-- danzi.tn@20150427 new relation to Rumors

INSERT INTO  vtiger_calendar_default_activitytypes (
id ,
module ,
fieldname ,
defaultcolor
)
VALUES (
'9',  'Rumors',  'Rumors',  '#006400'
);


UPDATE vtiger_calendar_default_activitytypes_seq SET id = 9;

INSERT INTO vtiger_calendar_user_activitytypes (id, defaultid, userid, color, visible) VALUES ('9', '9', '1', '#006400', '1');



UPDATE vtiger_calendar_user_activitytypes_seq SET id = 9;

INSERT INTO  vtiger_ws_referencetype (
fieldtypeid ,
type
)
VALUES (
'34',  'Rumors'
);
