DROP TRIGGER trg_set_schedDate;
delimiter //
CREATE TRIGGER trg_set_schedDate
BEFORE INSERT ON scheduled_post
FOR EACH ROW
BEGIN
   SET new.schedDate = new.EventDate;
END;
//
delimiter ;
