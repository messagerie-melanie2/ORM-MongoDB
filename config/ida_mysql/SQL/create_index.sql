SHOW INDEXES FROM events;
DROP INDEX events_start_end ON events;
DROP INDEX events_recurrence_freq_until ON events;

CREATE INDEX events_start_end
  USING BTREE
  ON events (dtstart DESC, dtend);

CREATE INDEX events_recurrence_freq_until
  USING BTREE
  ON events (recurrence_freq, recurrence_until DESC);


