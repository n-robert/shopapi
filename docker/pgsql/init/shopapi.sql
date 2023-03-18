--
-- PostgreSQL database dump
--

-- Dumped from database version 12.14 (Debian 12.14-1.pgdg110+1)
-- Dumped by pg_dump version 12.14 (Debian 12.14-1.pgdg110+1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: carts; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.carts (
    id bigint NOT NULL,
    items json NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    type character varying(255) DEFAULT 'cart'::character varying NOT NULL,
    status character varying(255) DEFAULT 'active'::character varying NOT NULL,
    user_id integer,
    total numeric
);


ALTER TABLE public.carts OWNER TO test;

--
-- Name: carts_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.carts_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.carts_id_seq OWNER TO test;

--
-- Name: carts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.carts_id_seq OWNED BY public.carts.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO test;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.failed_jobs_id_seq OWNER TO test;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO test;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.migrations_id_seq OWNER TO test;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: oauth_access_tokens; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.oauth_access_tokens (
    id character varying(100) NOT NULL,
    user_id bigint,
    client_id bigint NOT NULL,
    name character varying(255),
    scopes text,
    revoked boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_access_tokens OWNER TO test;

--
-- Name: oauth_auth_codes; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.oauth_auth_codes (
    id character varying(100) NOT NULL,
    user_id bigint NOT NULL,
    client_id bigint NOT NULL,
    scopes text,
    revoked boolean NOT NULL,
    expires_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_auth_codes OWNER TO test;

--
-- Name: oauth_clients; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.oauth_clients (
    id bigint NOT NULL,
    user_id bigint,
    name character varying(255) NOT NULL,
    secret character varying(100),
    provider character varying(255),
    redirect text NOT NULL,
    personal_access_client boolean NOT NULL,
    password_client boolean NOT NULL,
    revoked boolean NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_clients OWNER TO test;

--
-- Name: oauth_clients_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.oauth_clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oauth_clients_id_seq OWNER TO test;

--
-- Name: oauth_clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.oauth_clients_id_seq OWNED BY public.oauth_clients.id;


--
-- Name: oauth_personal_access_clients; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.oauth_personal_access_clients (
    id bigint NOT NULL,
    client_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_personal_access_clients OWNER TO test;

--
-- Name: oauth_personal_access_clients_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.oauth_personal_access_clients_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.oauth_personal_access_clients_id_seq OWNER TO test;

--
-- Name: oauth_personal_access_clients_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.oauth_personal_access_clients_id_seq OWNED BY public.oauth_personal_access_clients.id;


--
-- Name: oauth_refresh_tokens; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.oauth_refresh_tokens (
    id character varying(100) NOT NULL,
    access_token_id character varying(100) NOT NULL,
    revoked boolean NOT NULL,
    expires_at timestamp(0) without time zone
);


ALTER TABLE public.oauth_refresh_tokens OWNER TO test;

--
-- Name: order_details; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.order_details (
    id bigint NOT NULL,
    order_id integer NOT NULL,
    product_id integer NOT NULL,
    quantity integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.order_details OWNER TO test;

--
-- Name: order_details_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.order_details_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.order_details_id_seq OWNER TO test;

--
-- Name: order_details_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.order_details_id_seq OWNED BY public.order_details.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.orders (
    id bigint NOT NULL,
    user_id integer,
    subtotal numeric(8,2) NOT NULL,
    discount numeric(8,2) NOT NULL,
    total numeric(8,2) NOT NULL,
    status character varying(255) DEFAULT 'P'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.orders OWNER TO test;

--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.orders_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.orders_id_seq OWNER TO test;

--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.password_resets (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_resets OWNER TO test;

--
-- Name: products; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.products (
    id bigint NOT NULL,
    title character varying(255) NOT NULL,
    slug character varying(255) NOT NULL,
    cost numeric(8,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.products OWNER TO test;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.products_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.products_id_seq OWNER TO test;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO test;

--
-- Name: users; Type: TABLE; Schema: public; Owner: test
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_admin boolean DEFAULT false NOT NULL
);


ALTER TABLE public.users OWNER TO test;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: test
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO test;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: test
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: carts id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.carts ALTER COLUMN id SET DEFAULT nextval('public.carts_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: oauth_clients id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.oauth_clients ALTER COLUMN id SET DEFAULT nextval('public.oauth_clients_id_seq'::regclass);


--
-- Name: oauth_personal_access_clients id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.oauth_personal_access_clients ALTER COLUMN id SET DEFAULT nextval('public.oauth_personal_access_clients_id_seq'::regclass);


--
-- Name: order_details id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.order_details ALTER COLUMN id SET DEFAULT nextval('public.order_details_id_seq'::regclass);


--
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	2014_10_12_100000_create_password_resets_table	1
2	2016_06_01_000001_create_oauth_auth_codes_table	1
3	2016_06_01_000002_create_oauth_access_tokens_table	1
4	2016_06_01_000003_create_oauth_refresh_tokens_table	1
5	2016_06_01_000004_create_oauth_clients_table	1
6	2016_06_01_000005_create_oauth_personal_access_clients_table	1
7	2019_08_19_000000_create_failed_jobs_table	1
8	2021_08_24_070854_add_is_admin_to_users_table	1
9	2014_10_12_000000_create_users_table	1
10	2021_08_23_121024_create_carts_table	1
11	2021_08_24_152855_create_orders_table	1
12	2021_08_23_120937_create_products_table	1
13	2021_08_26_193542_add_columns_to_products_table	1
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.migrations_id_seq', 32, true);


--
-- Name: carts carts_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: oauth_access_tokens oauth_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.oauth_access_tokens
    ADD CONSTRAINT oauth_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: oauth_auth_codes oauth_auth_codes_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.oauth_auth_codes
    ADD CONSTRAINT oauth_auth_codes_pkey PRIMARY KEY (id);


--
-- Name: oauth_clients oauth_clients_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.oauth_clients
    ADD CONSTRAINT oauth_clients_pkey PRIMARY KEY (id);


--
-- Name: oauth_personal_access_clients oauth_personal_access_clients_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.oauth_personal_access_clients
    ADD CONSTRAINT oauth_personal_access_clients_pkey PRIMARY KEY (id);


--
-- Name: oauth_refresh_tokens oauth_refresh_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.oauth_refresh_tokens
    ADD CONSTRAINT oauth_refresh_tokens_pkey PRIMARY KEY (id);


--
-- Name: order_details order_details_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.order_details
    ADD CONSTRAINT order_details_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products products_slug_unique; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_slug_unique UNIQUE (slug);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: test
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: oauth_access_tokens_user_id_index; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX oauth_access_tokens_user_id_index ON public.oauth_access_tokens USING btree (user_id);


--
-- Name: oauth_auth_codes_user_id_index; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX oauth_auth_codes_user_id_index ON public.oauth_auth_codes USING btree (user_id);


--
-- Name: oauth_clients_user_id_index; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX oauth_clients_user_id_index ON public.oauth_clients USING btree (user_id);


--
-- Name: oauth_refresh_tokens_access_token_id_index; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX oauth_refresh_tokens_access_token_id_index ON public.oauth_refresh_tokens USING btree (access_token_id);


--
-- Name: password_resets_email_index; Type: INDEX; Schema: public; Owner: test
--

CREATE INDEX password_resets_email_index ON public.password_resets USING btree (email);


--
-- PostgreSQL database dump complete
--

