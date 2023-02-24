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
-- Data for Name: carts; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.carts (id, items, created_at, updated_at, type, status, user_id, total) FROM stdin;
1	{"2":{"id":2,"quantity":2},"3":{"id":3,"quantity":1}}	2021-08-29 15:57:45	2021-08-29 21:24:59	cart	active	1	\N
12	{"1":{"id":1,"quantity":2}}	2021-08-29 22:37:42	2021-08-29 23:22:41	cart	active	3	\N
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.migrations (id, migration, batch) FROM stdin;
2	2014_10_12_100000_create_password_resets_table	1
3	2016_06_01_000001_create_oauth_auth_codes_table	1
4	2016_06_01_000002_create_oauth_access_tokens_table	1
5	2016_06_01_000003_create_oauth_refresh_tokens_table	1
6	2016_06_01_000004_create_oauth_clients_table	1
7	2016_06_01_000005_create_oauth_personal_access_clients_table	1
8	2019_08_19_000000_create_failed_jobs_table	1
10	2021_08_23_121024_create_carts_table	2
12	2021_08_24_062319_create_order_details_table	2
13	2021_08_24_070854_add_is_admin_to_users_table	3
14	2021_08_24_152855_create_orders_table	4
15	2021_08_24_154349_create_orders_table	5
16	2021_08_23_120937_create_products_table	6
23	2014_10_12_000000_create_users_table	7
24	2021_08_24_221711_add_is_admin_to_users_table	7
28	2021_08_26_193542_add_columns_to_carts_table	8
29	2021_08_27_120937_create_products_table	9
\.


--
-- Data for Name: oauth_access_tokens; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.oauth_access_tokens (id, user_id, client_id, name, scopes, revoked, created_at, updated_at, expires_at) FROM stdin;
5740158bfea9d01daaa3c13990efa107c4c711b0a8d97867fd3bc32b85eac8fc48dbf46e75dca756	1	1	LaravelTest	[]	f	2021-08-29 09:21:16	2021-08-29 09:21:16	2021-08-30 09:21:16
192d62a1f5c7ef3959e9b5a55aa5bd1af98c8d454f80a4f92bc0f14d76f8c63fbbaca098febf00ce	1	1	LaravelTest	[]	f	2021-08-26 07:57:46	2021-08-26 07:57:46	2021-08-27 07:57:47
00514273879d7e176cd0c82df465bec0ea4d0535d9fb5632889d697a9b6dfb01efd4cecc2bc78637	1	1	LaravelTest	[]	f	2021-08-26 08:07:01	2021-08-26 08:07:01	2021-08-27 08:07:01
8088a736bc7894a4757b2a332761910a101373c923a8b7e4c0e64eb5dc281956a5e725d142089b3d	1	1	LaravelTest	[]	t	2021-08-26 08:44:36	2021-08-26 08:44:36	2021-08-27 08:44:36
116812180896bf176383821d31fd3e527352719b532998d06e9a9d05012a7c61b513e4a65492cc47	1	1	LaravelTest	[]	t	2021-08-29 21:39:35	2021-08-29 21:39:35	2021-08-30 21:39:35
43463581124da1bbb013ec1e97cdfdbeafbd745cdc87e54a1017c11e1b63df4d90c109b046651c3c	2	1	LaravelTest	[]	t	2021-08-26 09:12:26	2021-08-26 09:12:26	2021-08-27 09:12:26
43f536fda1d00c5b0a6636f87768d82e48b3380555c072edc966a45b93808174199a5d94a74be4f8	3	1	LaravelTest	[]	f	2021-08-29 21:59:24	2021-08-29 21:59:24	2021-08-30 21:59:24
979027959b9301e13d514e2c76b9e1868c33ae92e655d1a5481acb37319838af9c8ed8792ae5fcaa	2	1	LaravelTest	[]	t	2021-08-26 09:24:48	2021-08-26 09:24:48	2021-08-27 09:24:48
f183a4ccc69b5b1ead35cbeb9c2971fbcb7ed0bce8aac9716af7da0a66f76764e1437f9b5b14b59d	2	1	LaravelTest	[]	t	2021-08-26 09:26:35	2021-08-26 09:26:35	2021-08-27 09:26:35
33f2585565332776dcf3088fb407c260c2817fc33be1e192505f16231b25dfc3e53ed8f5175263f9	2	1	LaravelTest	[]	t	2021-08-26 09:47:44	2021-08-26 09:47:44	2021-08-27 09:47:44
2ed78a293664803fabfd3d9710f7685fb36fe880ef7f8a10508f9c86e66a82466f52f9bdd60697e8	1	1	LaravelTest	[]	f	2021-08-28 12:11:52	2021-08-28 12:11:52	2021-08-29 12:11:52
6120de9e0f11a7990380eb59d602fe71bb4caa37b30f9de13ff2ab53d42cb88f49fcb2eebb1eaacd	1	1	LaravelTest	[]	f	2021-08-28 19:45:35	2021-08-28 19:45:35	2021-08-29 19:45:35
728a8b9ec000b999b6bd96688f75fe6febc91704fc6eed61c43b346a98d7b4a4f681fd612736a9cc	1	1	LaravelTest	[]	f	2021-08-28 21:29:42	2021-08-28 21:29:42	2021-08-29 21:29:42
22187e3b456ce2e3a57d8a26b7d384673ed73d3ec63f1565c438e104ff9e4b656086d88ffb0bb9f7	1	1	LaravelTest	[]	f	2021-08-28 21:29:55	2021-08-28 21:29:55	2021-08-29 21:29:55
7ebf8b863a41bb1250e61959910a0b1771cee8b44df18b70494a5a5201db32dbb8a809e7e85d2996	1	1	LaravelTest	[]	f	2021-08-28 21:32:20	2021-08-28 21:32:20	2021-08-29 21:32:20
\.


--
-- Data for Name: oauth_auth_codes; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.oauth_auth_codes (id, user_id, client_id, scopes, revoked, expires_at) FROM stdin;
\.


--
-- Data for Name: oauth_clients; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.oauth_clients (id, user_id, name, secret, provider, redirect, personal_access_client, password_client, revoked, created_at, updated_at) FROM stdin;
1	\N	Laravel Personal Access Client	vjO1ARuUi0iYJWgYacBU7wv55y2ink7gfIfO4Cvs	\N	http://localhost	t	f	f	2021-08-23 12:00:37	2021-08-23 12:00:37
2	\N	Laravel Password Grant Client	zTg0QVe2J7cQbMQ63aiXkkQ41K87ejWdfRMw4OsY	users	http://localhost	f	t	f	2021-08-23 12:00:37	2021-08-23 12:00:37
3	\N	Shop API Personal Access Client	bgqLEmsVZtd61Lq3JcfR2SCgH6LBXnODt4d1EY9V	\N	http://localhost	t	f	f	2023-02-24 12:15:58	2023-02-24 12:15:58
4	\N	Shop API Password Grant Client	MmFPaFMc1Crx5FeLJbbZfXDgP0sgxwl5Sv2aNK3R	users	http://localhost	f	t	f	2023-02-24 12:15:58	2023-02-24 12:15:58
\.


--
-- Data for Name: oauth_personal_access_clients; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.oauth_personal_access_clients (id, client_id, created_at, updated_at) FROM stdin;
1	1	2021-08-23 12:00:37	2021-08-23 12:00:37
2	3	2023-02-24 12:15:58	2023-02-24 12:15:58
\.


--
-- Data for Name: oauth_refresh_tokens; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.oauth_refresh_tokens (id, access_token_id, revoked, expires_at) FROM stdin;
\.


--
-- Data for Name: order_details; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.order_details (id, order_id, product_id, quantity, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.orders (id, user_id, subtotal, discount, total, status, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.password_resets (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.products (id, title, slug, cost, created_at, updated_at) FROM stdin;
2	Another awesome product	product-2	1155.50	2021-08-27 12:55:56	2021-08-27 12:55:56
3	Superb product	product-3	1098.00	2021-08-27 12:57:43	2021-08-27 12:57:43
4	Superb product No.2	product-4	1535.00	2021-08-27 16:30:05	2021-08-27 16:30:05
1	Awesome product	product-1	1275.00	2021-08-27 12:50:42	2021-08-27 18:36:43
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: test
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, is_admin) FROM stdin;
1	user	user@laraveltest.com	2021-08-26 09:20:36	$2y$10$o1HtD9gqBSqabevu54vZqeaVJv343EzfcJFwzlH91y5phsW5S56/m	XpjWalD1Am	2021-08-26 09:20:36	\N	f
2	admin	admin@laraveltest.com	2021-08-26 09:20:36	$2y$10$/hGKU1er/aimxwIX0lAAWeh5aKJKrNxYpm7kc1x37ZYX1eCE6KjDy	nCHoAzkYOa	2021-08-26 09:20:36	\N	t
3	test	test@laraveltest.com	\N	$2y$10$yDvS86S/KJyyFQjOb/rqdeFrhubrs4HNHVQDAQfXVJUg/4kTScXT6	\N	2021-08-29 21:57:55	2021-08-29 21:57:55	f
\.


--
-- Name: carts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.carts_id_seq', 12, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.migrations_id_seq', 32, true);


--
-- Name: oauth_clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.oauth_clients_id_seq', 4, true);


--
-- Name: oauth_personal_access_clients_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.oauth_personal_access_clients_id_seq', 2, true);


--
-- Name: order_details_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.order_details_id_seq', 1, false);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.orders_id_seq', 1, false);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.products_id_seq', 4, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: test
--

SELECT pg_catalog.setval('public.users_id_seq', 3, true);


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

