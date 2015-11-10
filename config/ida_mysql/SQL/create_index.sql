CREATE INDEX events_start_end
  USING BTREE
  ON events (dtstart ASC, dtend DESC);

CREATE INDEX events_recurrence_freq_until
  USING BTREE
  ON events (recurrence_freq, recurrence_until);
