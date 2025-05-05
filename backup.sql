--
-- PostgreSQL database dump
--

-- Dumped from database version 17.0
-- Dumped by pg_dump version 17.0

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: notify_messenger_messages(); Type: FUNCTION; Schema: public; Owner: backend_app
--

CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
        BEGIN
            PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
            RETURN NEW;
        END;
    $$;


ALTER FUNCTION public.notify_messenger_messages() OWNER TO backend_app;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: backend_app
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO backend_app;

--
-- Name: messenger_messages; Type: TABLE; Schema: public; Owner: backend_app
--

CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(190) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.messenger_messages OWNER TO backend_app;

--
-- Name: COLUMN messenger_messages.created_at; Type: COMMENT; Schema: public; Owner: backend_app
--

COMMENT ON COLUMN public.messenger_messages.created_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.available_at; Type: COMMENT; Schema: public; Owner: backend_app
--

COMMENT ON COLUMN public.messenger_messages.available_at IS '(DC2Type:datetime_immutable)';


--
-- Name: COLUMN messenger_messages.delivered_at; Type: COMMENT; Schema: public; Owner: backend_app
--

COMMENT ON COLUMN public.messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)';


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: backend_app
--

CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.messenger_messages_id_seq OWNER TO backend_app;

--
-- Name: messenger_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: backend_app
--

ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;


--
-- Name: product; Type: TABLE; Schema: public; Owner: backend_app
--

CREATE TABLE public.product (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    description text,
    type character varying(255) NOT NULL,
    base_price numeric(10,2) NOT NULL,
    base_rent_per_day double precision NOT NULL,
    base_rent_per_week double precision NOT NULL,
    stock integer NOT NULL,
    availability boolean NOT NULL,
    image_path character varying(255) DEFAULT NULL::character varying,
    category character varying(255) DEFAULT NULL::character varying
);


ALTER TABLE public.product OWNER TO backend_app;

--
-- Name: product_id_seq; Type: SEQUENCE; Schema: public; Owner: backend_app
--

CREATE SEQUENCE public.product_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_id_seq OWNER TO backend_app;

--
-- Name: product_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: backend_app
--

ALTER SEQUENCE public.product_id_seq OWNED BY public.product.id;


--
-- Name: purchase; Type: TABLE; Schema: public; Owner: backend_app
--

CREATE TABLE public.purchase (
    id integer NOT NULL,
    user_id_id integer NOT NULL,
    product_id_id integer NOT NULL,
    "timestamp" timestamp(0) without time zone NOT NULL,
    amount double precision NOT NULL,
    quantity integer NOT NULL
);


ALTER TABLE public.purchase OWNER TO backend_app;

--
-- Name: purchase_id_seq; Type: SEQUENCE; Schema: public; Owner: backend_app
--

CREATE SEQUENCE public.purchase_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.purchase_id_seq OWNER TO backend_app;

--
-- Name: purchase_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: backend_app
--

ALTER SEQUENCE public.purchase_id_seq OWNED BY public.purchase.id;


--
-- Name: rental; Type: TABLE; Schema: public; Owner: backend_app
--

CREATE TABLE public.rental (
    id integer NOT NULL,
    user_id_id integer NOT NULL,
    product_id_id integer NOT NULL,
    start_timestamp timestamp(0) without time zone NOT NULL,
    end_timestamp timestamp(0) without time zone NOT NULL,
    amount double precision NOT NULL,
    buyout_cost double precision,
    status character varying(255) NOT NULL
);


ALTER TABLE public.rental OWNER TO backend_app;

--
-- Name: rental_id_seq; Type: SEQUENCE; Schema: public; Owner: backend_app
--

CREATE SEQUENCE public.rental_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rental_id_seq OWNER TO backend_app;

--
-- Name: rental_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: backend_app
--

ALTER SEQUENCE public.rental_id_seq OWNED BY public.rental.id;


--
-- Name: user; Type: TABLE; Schema: public; Owner: backend_app
--

CREATE TABLE public."user" (
    id integer NOT NULL,
    email character varying(180) NOT NULL,
    roles json NOT NULL,
    password character varying(255) NOT NULL
);


ALTER TABLE public."user" OWNER TO backend_app;

--
-- Name: user_id_seq; Type: SEQUENCE; Schema: public; Owner: backend_app
--

CREATE SEQUENCE public.user_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.user_id_seq OWNER TO backend_app;

--
-- Name: user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: backend_app
--

ALTER SEQUENCE public.user_id_seq OWNED BY public."user".id;


--
-- Name: messenger_messages id; Type: DEFAULT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);


--
-- Name: product id; Type: DEFAULT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.product ALTER COLUMN id SET DEFAULT nextval('public.product_id_seq'::regclass);


--
-- Name: purchase id; Type: DEFAULT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.purchase ALTER COLUMN id SET DEFAULT nextval('public.purchase_id_seq'::regclass);


--
-- Name: rental id; Type: DEFAULT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.rental ALTER COLUMN id SET DEFAULT nextval('public.rental_id_seq'::regclass);


--
-- Name: user id; Type: DEFAULT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public."user" ALTER COLUMN id SET DEFAULT nextval('public.user_id_seq'::regclass);


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20250429100550	2025-04-29 10:06:34	111
DoctrineMigrations\\Version20250503174010	2025-05-03 17:41:09	63
DoctrineMigrations\\Version20250503183011	2025-05-03 18:30:30	45
DoctrineMigrations\\Version20250503183053	2025-05-03 18:30:58	1
\.


--
-- Data for Name: messenger_messages; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public.messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) FROM stdin;
\.


--
-- Data for Name: product; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public.product (id, name, description, type, base_price, base_rent_per_day, base_rent_per_week, stock, availability, image_path, category) FROM stdin;
2	test product 2	Second of His name. Middle brother, nothing to bother.	buy	16.00	0	0	12	t	\N	\N
1	test product 1	Product One, first of His name, ambassador of SQL grace	both	30.00	2	5	5	t	test1-a-681635e020439.jpg	test
\.


--
-- Data for Name: purchase; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public.purchase (id, user_id_id, product_id_id, "timestamp", amount, quantity) FROM stdin;
1	1	1	2025-05-04 11:54:15	90	3
\.


--
-- Data for Name: rental; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public.rental (id, user_id_id, product_id_id, start_timestamp, end_timestamp, amount, buyout_cost, status) FROM stdin;
2	2	1	2025-05-06 00:00:00	2025-05-10 00:00:00	8	24	ongoing
1	1	1	2025-05-05 00:00:00	2025-05-07 00:00:00	4	26	returned
\.


--
-- Data for Name: user; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public."user" (id, email, roles, password) FROM stdin;
1	admin@oms.com	["ROLE_ADMIN"]	$2y$13$fkZ46ylOn6MbIX3gXfHrfuVkG3Nkc4xMMW/tpO4tAYozcCzCXP4BC
2	test2@somemail.com	["ROLE_USER"]	$2y$13$CEPd05wZAMXGQCrWXbqZEOXp5QPj/wIQGA5SfPljRursqN8G2VrR.
\.


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.messenger_messages_id_seq', 1, false);


--
-- Name: product_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.product_id_seq', 2, true);


--
-- Name: purchase_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.purchase_id_seq', 1, true);


--
-- Name: rental_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.rental_id_seq', 2, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.user_id_seq', 2, true);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: messenger_messages messenger_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);


--
-- Name: product product_pkey; Type: CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.product
    ADD CONSTRAINT product_pkey PRIMARY KEY (id);


--
-- Name: purchase purchase_pkey; Type: CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.purchase
    ADD CONSTRAINT purchase_pkey PRIMARY KEY (id);


--
-- Name: rental rental_pkey; Type: CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.rental
    ADD CONSTRAINT rental_pkey PRIMARY KEY (id);


--
-- Name: user user_pkey; Type: CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public."user"
    ADD CONSTRAINT user_pkey PRIMARY KEY (id);


--
-- Name: idx_1619c27d9d86650f; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE INDEX idx_1619c27d9d86650f ON public.rental USING btree (user_id_id);


--
-- Name: idx_1619c27dde18e50b; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE INDEX idx_1619c27dde18e50b ON public.rental USING btree (product_id_id);


--
-- Name: idx_6117d13b9d86650f; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE INDEX idx_6117d13b9d86650f ON public.purchase USING btree (user_id_id);


--
-- Name: idx_6117d13bde18e50b; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE INDEX idx_6117d13bde18e50b ON public.purchase USING btree (product_id_id);


--
-- Name: idx_75ea56e016ba31db; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);


--
-- Name: idx_75ea56e0e3bd61ce; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);


--
-- Name: idx_75ea56e0fb7336f0; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);


--
-- Name: uniq_identifier_email; Type: INDEX; Schema: public; Owner: backend_app
--

CREATE UNIQUE INDEX uniq_identifier_email ON public."user" USING btree (email);


--
-- Name: messenger_messages notify_trigger; Type: TRIGGER; Schema: public; Owner: backend_app
--

CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();


--
-- Name: rental fk_1619c27d9d86650f; Type: FK CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.rental
    ADD CONSTRAINT fk_1619c27d9d86650f FOREIGN KEY (user_id_id) REFERENCES public."user"(id);


--
-- Name: rental fk_1619c27dde18e50b; Type: FK CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.rental
    ADD CONSTRAINT fk_1619c27dde18e50b FOREIGN KEY (product_id_id) REFERENCES public.product(id);


--
-- Name: purchase fk_6117d13b9d86650f; Type: FK CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.purchase
    ADD CONSTRAINT fk_6117d13b9d86650f FOREIGN KEY (user_id_id) REFERENCES public."user"(id);


--
-- Name: purchase fk_6117d13bde18e50b; Type: FK CONSTRAINT; Schema: public; Owner: backend_app
--

ALTER TABLE ONLY public.purchase
    ADD CONSTRAINT fk_6117d13bde18e50b FOREIGN KEY (product_id_id) REFERENCES public.product(id);


--
-- PostgreSQL database dump complete
--

