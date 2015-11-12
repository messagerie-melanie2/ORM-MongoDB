SHOW INDEXES FROM events;
DROP INDEX events_start_end ON events;
DROP INDEX events_recurrence_freq_until ON events;

CREATE INDEX events_start_end
  USING BTREE
  ON events (dtstart DESC, dtend);

CREATE INDEX events_recurrence_freq_until
  USING BTREE
  ON events (recurrence_freq, recurrence_until DESC);

CREATE INDEX events_calendar_start
  USING BTREE
  ON events (calendar, dtstart);

CREATE INDEX events_calendar_end
  USING BTREE
  ON events (calendar, dtend);

CREATE INDEX events_calendar_recurrence_freq_until_end
  USING BTREE
  ON events (calendar, recurrence_freq, recurrence_until, dtend);

