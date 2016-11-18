-- Melanie initial database for calendars, contacts, tasks backend

--
-- Table "users"
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE users (
	uid VARCHAR(255) PRIMARY KEY,
	created timestamp with time zone NOT NULL DEFAULT now(),
	preferences JSONB
);


--
-- Table "calendars"
-- Name: calendars; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE calendars (
	uid VARCHAR(255) PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	owner VARCHAR(255) NOT NULL REFERENCES users(uid),
	created timestamp with time zone NOT NULL DEFAULT now(),
	ctag VARCHAR(255) NOT NULL DEFAULT md5('default'),
	properties JSONB
);


--
-- Table "calendar_shares"
-- Name: calendar_shares; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE calendar_shares (
	calendar_id VARCHAR(255) REFERENCES calendars(uid),
	user_uid VARCHAR(255) REFERENCES users(uid),
	share_value VARCHAR(255) NOT NULL,
	properties JSONB,
	CONSTRAINT calendar_id_user_uid_pkey PRIMARY KEY(calendar_id, user_uid)
);


--
-- Sequence "events_seq"
-- Name: events_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE events_seq
	START WITH 1
    	INCREMENT BY 1
    	NO MAXVALUE
    	NO MINVALUE
    	CACHE 1;


--
-- Table "events"
-- Name: events; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE events (
	id bigint DEFAULT nextval('events_seq'::text) PRIMARY KEY,
	uid VARCHAR(255) NOT NULL,
	calendar_id VARCHAR(255) NOT NULL REFERENCES calendars(uid),
	creator_id VARCHAR(255) NOT NULL REFERENCES users(uid),
	last_modifier_id VARCHAR(255) REFERENCES users(uid),
	created integer NOT NULL DEFAULT extract(epoch from now()),
	modified integer NOT NULL DEFAULT extract(epoch from now()),
	history JSONB,
	summmary VARCHAR(255),
	description TEXT,
	location TEXT,
	geographical TEXT,
	classification VARCHAR(120),
	comment TEXT,
	status VARCHAR(120),
	transparency VARCHAR(120),
	sequence integer,
	priority smallint,
	start timestamp with time zone NOT NULL,
	end timestamp with time zone NOT NULL,
	timezone TEXT,
	duration VARCHAR(255),
	is_deleted smallint NOT NULL DEFAULT 0,
	is_recurrence smallint NOT NULL DEFAULT 0,
	organizer_ics JSONB,
	attendees_ics JSONB,
	alarm_ics JSONB,
	categories JSONB,
	recurrence_ics JSONB,
	recurrence_enddate timestamp with time zone,
	exceptions_ics JSONB,
	attachments_ics JSONB,
	properties_ics JSONB
);


--
-- Sequence "alams_seq"
-- Name: alams_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE alams_seq
	START WITH 1
    	INCREMENT BY 1
    	NO MAXVALUE
    	NO MINVALUE
    	CACHE 1;


--
-- Table "alarms"
-- Name: alarms; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE alarms (
	id bigint DEFAULT nextval('alams_seq'::text) PRIMARY KEY,
	created timestamp with time zone NOT NULL DEFAULT now(),
	date_utc timestamp without time zone NOT NULL,
	event_id bigint NOT NULL REFERENCES events(id),
	calendar_id VARCHAR(255) NOT NULL REFERENCES calendars(uid)
);


--
-- Table "addressbooks"
-- Name: addressbooks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE addressbooks (
	uid VARCHAR(255) PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	owner VARCHAR(255) NOT NULL REFERENCES users(uid),
	ctag VARCHAR(255) NOT NULL DEFAULT md5('default'),
	synctoken VARCHAR(255) NOT NULL,
	properties JSONB
);


--
-- Table "addressbook_shares"
-- Name: addressbook_shares; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE addressbook_shares (
	addressbook_id VARCHAR(255) REFERENCES addressbooks(uid),
	user_uid VARCHAR(255) REFERENCES users(uid),
	share_value VARCHAR(255) NOT NULL,
	properties JSONB,
	CONSTRAINT addressbook_id_user_uid_pkey PRIMARY KEY(addressbook_id, user_uid)
);


--
-- Sequence "contacts_seq"
-- Name: contacts_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE contacts_seq
	START WITH 1
    	INCREMENT BY 1
    	NO MAXVALUE
    	NO MINVALUE
    	CACHE 1;


--
-- Table "contacts"
-- Name: contacts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE contacts (
	id bigint DEFAULT nextval('contacts_seq'::text) PRIMARY KEY,
	uid VARCHAR(255) NOT NULL,
	addressbook_id VARCHAR(255) NOT NULL REFERENCES addressbooks(uid),
	creator_id VARCHAR(255) NOT NULL REFERENCES users(uid),
	last_modifier_id VARCHAR(255) REFERENCES users(uid),
	created integer NOT NULL DEFAULT extract(epoch from now()),
	modified integer NOT NULL DEFAULT extract(epoch from now()),
	history JSONB,
	fullname TEXT,
	lastname VARCHAR(255),
	firstname VARCHAR(255),
	email TEXT,
	emails JSONB,
	addresses JSONB,
	phones JSONB,
	photo JSONB,
	impp JSONB,
	identification JSONB,
	geographical JSONB,
	languages JSONB,
	title VARCHAR(255),
	ROLE VARCHAR(255),
	logo JSONB,
	organizational JSONB,
	categories JSONB,
	note TEXT,
	explanatory JSONB,
	key JSONB,
	calendar JSONB,
	properties_ics JSONB
);


--
-- Sequence "groups_seq"
-- Name: groups_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE groups_seq
	START WITH 1
    	INCREMENT BY 1
    	NO MAXVALUE
    	NO MINVALUE
    	CACHE 1;


--
-- Table "groups"
-- Name: groups; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE groups (
	id bigint DEFAULT nextval('groups_seq'::text) PRIMARY KEY,
	uid VARCHAR(255) NOT NULL,
	addressbook_id VARCHAR(255) NOT NULL REFERENCES addressbooks(uid),
	creator_id VARCHAR(255) NOT NULL REFERENCES users(uid),
	last_modifier_id VARCHAR(255) REFERENCES users(uid),
	created integer NOT NULL DEFAULT extract(epoch from now()),
	modified integer NOT NULL DEFAULT extract(epoch from now()),
	history JSONB,
	name TEXT,
	members JSONB,
	properties_ics JSONB
);


--
-- Table "tasklists"
-- Name: tasklists; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE tasklists (
	uid VARCHAR(255) PRIMARY KEY,
	name VARCHAR(255) NOT NULL,
	owner VARCHAR(255) NOT NULL REFERENCES users(uid),
	ctag VARCHAR(255) NOT NULL DEFAULT md5('default'),
	synctoken VARCHAR(255) NOT NULL,
	properties JSONB
);


--
-- Table "tasklist_shares"
-- Name: tasklist_shares; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE tasklist_shares (
	tasklist_id VARCHAR(255) REFERENCES tasklists(uid),
	user_uid VARCHAR(255) REFERENCES users(uid),
	share_value VARCHAR(255) NOT NULL,
	properties JSONB,
	CONSTRAINT tasklist_id_user_uid_pkey PRIMARY KEY(tasklist_id, user_uid)
);


--
-- Sequence "tasks_seq"
-- Name: tasks_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE tasks_seq
	START WITH 1
    	INCREMENT BY 1
    	NO MAXVALUE
    	NO MINVALUE
    	CACHE 1;


--
-- Table "tasks"
-- Name: tasks; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE tasks (
	id bigint DEFAULT nextval('tasks_seq'::text) PRIMARY KEY,
	uid VARCHAR(255) NOT NULL,
	tasklist_id VARCHAR(255) NOT NULL REFERENCES tasklists(uid),
	creator_id VARCHAR(255) NOT NULL REFERENCES users(uid),
	last_modifier_id VARCHAR(255) REFERENCES users(uid),
	created integer NOT NULL DEFAULT extract(epoch from now()),
	modified integer NOT NULL DEFAULT extract(epoch from now()),
	history JSONB,
	summmary VARCHAR(255),
	description TEXT,
	location TEXT,
	geographical JSONB,
	classification VARCHAR(255),
	comment TEXT,
	status VARCHAR(255),
	sequence integer,
	priority smallint,
	percent_complete smallint,
	start_ics JSONB,
	completed_ics JSONB,
	duration_ics JSONB,
	is_deleted smallint NOT NULL DEFAULT 0,
	organizer_ics JSONB,
	attendees_ics JSONB,
	alarm_ics JSONB,
	categories JSONB,
	attachments JSONB,
	properties_ics JSONB
);

