CREATE INDEX events_start_end
  ON events
  USING BTREE 
  (dtstart ASC, dtend DESC);

CREATE INDEX events_recurrence_freq_until  
  ON events
  USING BTREE
 (recurrence_freq, recurrence_until);
