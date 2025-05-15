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
5	Roland TD-27KV V-Drums Zestaw Perkusyjny	Zaawansowany elektroniczny zestaw perkusyjny z siatkowymi padami i realistycznym silnikiem dźwiękowym.	both	3200.00	45	0	12	t	uploads/products/prod3-6819bcc4be00d.jpg	drums
3	Fender Stratocaster Gitara Elektryczna	Klasyczna 6-strunowa gitara elektryczna znana z jasnego brzmienia i wszechstronności.	both	1200.00	20	0	3	t	uploads/products/prod1-6819be6f4bbbd.jpg	electric
4	Yamaha U1 Pianino	Profesjonalne pianino z bogatym brzmieniem i czułymi klawiszami. Idealne do studia lub występów.	rent	4800.00	80	0	3	t	uploads/products/prod2-6819be785b052.jpg	piano
6	Ibanez GIO Gitara Akustyczna	Przystępna cenowo gitara akustyczna dla początkujących lub zwykłych graczy.	buy	180.00	0	0	26	t	uploads/products/prod4-6819beca7ccb2.jpg	acoustic, guitar
7	Shure SM58 Mikrofon Vocal	Standardowy mikrofon dynamiczny do wokalu na żywo lub pracy w studiu.	buy	99.00	0	0	31	t	uploads/products/prod5-6819bffeddc91.jpg	recording
8	Focusrite Scarlett 2i2 Audio Interface	Kompaktowy interfejs USB do nagrywania w domu, z krystalicznie czystymi przedwzmacniaczami.	buy	180.00	0	0	27	t	uploads/products/prod6-6819c03b7aaf4.jpg	recording
9	AKG C414 XLII Mikrofon Pojemnościowy	Wysokiej klasy mikrofon pojemnościowy z wieloma polaryzacjami do profesjonalnego nagrywania.	both	1100.00	60	0	16	t	uploads/products/prod7-6819c0bc8a4bb.jpg	recording
10	Yamaha HS8 Monitory Studyjne (para)	Monitory o płaskiej charakterystyce, idealne do miksowania i masteringu.	buy	750.00	0	0	11	t	uploads/products/prod8-6819c114571ce.jpg	monitor
11	Pioneer DJ Controller DDJ-1000	4-kanałowy kontroler DJ z kołami jog i wyświetlaczami on-jog.	both	1400.00	45	0	17	t	uploads/products/prod9-6819c14a1f294.jpg	DJ
12	Allen & Heath SQ-5 Mikser Cyfrowy	Kompaktowy mikser cyfrowy do pracy na żywo lub w studiu, z ekranem dotykowym.	rent	3999.00	120	0	5	t	uploads/products/prod10-6819c1a219e6f.jpg	mixer
13	Boss GT-1000 Procesor Efektów Gitarowych	Flagowy multi-efekt do gitary elektrycznej z modelowaniem wzmacniacza	both	999.00	25	0	14	t	uploads/products/prod11-6819c1ed001ad.jpg	processor
14	Sennheiser HD 280 Pro Słuchawki	Zamknięte słuchawki do monitorowania i trackowania.	buy	99.00	8	0	22	t	uploads/products/prod12-6819c22229594.jpg	headphones
\.


--
-- Data for Name: purchase; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public.purchase (id, user_id_id, product_id_id, "timestamp", amount, quantity) FROM stdin;
\.


--
-- Data for Name: rental; Type: TABLE DATA; Schema: public; Owner: backend_app
--

COPY public.rental (id, user_id_id, product_id_id, start_timestamp, end_timestamp, amount, buyout_cost, status) FROM stdin;
3	2	9	2025-05-07 00:00:00	2025-05-09 00:00:00	120	980	ongoing
4	2	11	2025-05-04 00:00:00	2025-05-06 00:00:00	90	1310	returned
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

SELECT pg_catalog.setval('public.product_id_seq', 14, true);


--
-- Name: purchase_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.purchase_id_seq', 1, true);


--
-- Name: rental_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.rental_id_seq', 4, true);


--
-- Name: user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: backend_app
--

SELECT pg_catalog.setval('public.user_id_seq', 3, true);


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

