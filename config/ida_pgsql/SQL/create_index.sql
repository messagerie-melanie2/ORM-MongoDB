DROP INDEX events_recurrence_freq_until;
DROP INDEX events_start_end;


CREATE INDEX events_start_end
  ON events
  USING BTREE 
  (dtstart DESC, dtend);

CREATE INDEX events_recurrence_freq_until  
  ON events
  USING BTREE
 (recurrence_freq, recurrence_until DESC);
