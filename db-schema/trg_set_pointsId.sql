DROP TRIGGER trg_set_pointId;
delimiter //
CREATE TRIGGER trg_set_pointId
BEFORE INSERT ON points_log
FOR EACH ROW
BEGIN
   if (new.PointsId is null) then
      SET new.PointsId = 0;
   end if;
END;
//
delimiter ;
