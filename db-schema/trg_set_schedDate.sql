DROP TRIGGER trg_set_schedDate;
delimiter //
CREATE TRIGGER trg_set_schedDate
BEFORE INSERT ON scheduled_post
FOR EACH ROW
BEGIN
   if (new.EventDate <= curdate()) and (new.EventTime <= curtime()) then
      SET new.schedDate = date_add(new.EventDate, interval 1 day);
   else
      SET new.schedDate = new.EventDate;
   end if;
END;
//
delimiter ;
