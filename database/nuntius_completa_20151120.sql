--
-- PostgreSQL database dump
--

-- Dumped from database version 9.3.6
-- Dumped by pg_dump version 9.3.6
-- Started on 2015-11-20 18:05:03 COT

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 3045 (class 1262 OID 19562)
-- Dependencies: 3044
-- Name: nuntius; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON DATABASE nuntius IS 'Sistema de integración sectorial:
SNIES
SPADIES
Jovenes en Acción
Registraduría
BPUDC - base de datos unificada del Distrito Capital, directiva 022';


--
-- TOC entry 7 (class 2615 OID 19876)
-- Name: menu; Type: SCHEMA; Schema: -; Owner: -
--

CREATE SCHEMA menu;


--
-- TOC entry 199 (class 3079 OID 12670)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 3047 (class 0 OID 0)
-- Dependencies: 199
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: -
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = menu, pg_catalog;

SET default_with_oids = false;

--
-- TOC entry 191 (class 1259 OID 19877)
-- Name: grupo; Type: TABLE; Schema: menu; Owner: -
--

CREATE TABLE grupo (
    id_grupo integer NOT NULL,
    descripcion text NOT NULL,
    orden_grupo smallint
);


--
-- TOC entry 192 (class 1259 OID 19883)
-- Name: grupo_id_grupo_seq; Type: SEQUENCE; Schema: menu; Owner: -
--

CREATE SEQUENCE grupo_id_grupo_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3048 (class 0 OID 0)
-- Dependencies: 192
-- Name: grupo_id_grupo_seq; Type: SEQUENCE OWNED BY; Schema: menu; Owner: -
--

ALTER SEQUENCE grupo_id_grupo_seq OWNED BY grupo.id_grupo;


--
-- TOC entry 193 (class 1259 OID 19885)
-- Name: item; Type: TABLE; Schema: menu; Owner: -
--

CREATE TABLE item (
    id_item integer NOT NULL,
    id_menu smallint NOT NULL,
    id_grupo smallint NOT NULL,
    id_tipo_item smallint NOT NULL,
    descripcion text NOT NULL,
    columna smallint DEFAULT 1 NOT NULL,
    orden_item smallint DEFAULT 0 NOT NULL,
    link text,
    estado_registro boolean DEFAULT true NOT NULL,
    parametros text
);


--
-- TOC entry 3049 (class 0 OID 0)
-- Dependencies: 193
-- Name: COLUMN item.parametros; Type: COMMENT; Schema: menu; Owner: -
--

COMMENT ON COLUMN item.parametros IS '-- Esta columna permitira enviar cualquier parametro a la pagina, pero en especial se crea para generar los nombres de las pestañas de los tab.';


--
-- TOC entry 194 (class 1259 OID 19894)
-- Name: item_id_item_seq; Type: SEQUENCE; Schema: menu; Owner: -
--

CREATE SEQUENCE item_id_item_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3050 (class 0 OID 0)
-- Dependencies: 194
-- Name: item_id_item_seq; Type: SEQUENCE OWNED BY; Schema: menu; Owner: -
--

ALTER SEQUENCE item_id_item_seq OWNED BY item.id_item;


--
-- TOC entry 195 (class 1259 OID 19896)
-- Name: menu; Type: TABLE; Schema: menu; Owner: -
--

CREATE TABLE menu (
    id_menu integer NOT NULL,
    descripcion text NOT NULL,
    perfil_usuario smallint DEFAULT 0 NOT NULL,
    estado_registro boolean DEFAULT true NOT NULL
);


--
-- TOC entry 196 (class 1259 OID 19904)
-- Name: menu_id_menu_seq; Type: SEQUENCE; Schema: menu; Owner: -
--

CREATE SEQUENCE menu_id_menu_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3051 (class 0 OID 0)
-- Dependencies: 196
-- Name: menu_id_menu_seq; Type: SEQUENCE OWNED BY; Schema: menu; Owner: -
--

ALTER SEQUENCE menu_id_menu_seq OWNED BY menu.id_menu;


--
-- TOC entry 197 (class 1259 OID 19906)
-- Name: tipo_item; Type: TABLE; Schema: menu; Owner: -
--

CREATE TABLE tipo_item (
    id_tipo_item integer NOT NULL,
    descripcion text NOT NULL
);


--
-- TOC entry 198 (class 1259 OID 19912)
-- Name: tipo_item_id_tipo_item_seq; Type: SEQUENCE; Schema: menu; Owner: -
--

CREATE SEQUENCE tipo_item_id_tipo_item_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3052 (class 0 OID 0)
-- Dependencies: 198
-- Name: tipo_item_id_tipo_item_seq; Type: SEQUENCE OWNED BY; Schema: menu; Owner: -
--

ALTER SEQUENCE tipo_item_id_tipo_item_seq OWNED BY tipo_item.id_tipo_item;


SET search_path = public, pg_catalog;

--
-- TOC entry 172 (class 1259 OID 19688)
-- Name: nuntius_bloque; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_bloque (
    id_bloque integer NOT NULL,
    nombre character(50) NOT NULL,
    descripcion character(255) DEFAULT NULL::bpchar,
    grupo character(200) NOT NULL
);


--
-- TOC entry 171 (class 1259 OID 19686)
-- Name: nuntius_bloque_id_bloque_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_bloque_id_bloque_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3053 (class 0 OID 0)
-- Dependencies: 171
-- Name: nuntius_bloque_id_bloque_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_bloque_id_bloque_seq OWNED BY nuntius_bloque.id_bloque;


--
-- TOC entry 174 (class 1259 OID 19700)
-- Name: nuntius_bloque_pagina; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_bloque_pagina (
    idrelacion integer NOT NULL,
    id_pagina integer DEFAULT 0 NOT NULL,
    id_bloque integer DEFAULT 0 NOT NULL,
    seccion character(1) NOT NULL,
    posicion integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 173 (class 1259 OID 19698)
-- Name: nuntius_bloque_pagina_idrelacion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_bloque_pagina_idrelacion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3054 (class 0 OID 0)
-- Dependencies: 173
-- Name: nuntius_bloque_pagina_idrelacion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_bloque_pagina_idrelacion_seq OWNED BY nuntius_bloque_pagina.idrelacion;


--
-- TOC entry 176 (class 1259 OID 19711)
-- Name: nuntius_configuracion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_configuracion (
    id_parametro integer NOT NULL,
    parametro character(255) NOT NULL,
    valor character(255) NOT NULL
);


--
-- TOC entry 175 (class 1259 OID 19709)
-- Name: nuntius_configuracion_id_parametro_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_configuracion_id_parametro_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3055 (class 0 OID 0)
-- Dependencies: 175
-- Name: nuntius_configuracion_id_parametro_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_configuracion_id_parametro_seq OWNED BY nuntius_configuracion.id_parametro;


--
-- TOC entry 178 (class 1259 OID 19722)
-- Name: nuntius_dbms; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_dbms (
    idconexion integer NOT NULL,
    nombre character varying(50) NOT NULL,
    dbms character varying(20) NOT NULL,
    servidor character varying(50) NOT NULL,
    puerto integer NOT NULL,
    conexionssh character varying(50) NOT NULL,
    db character varying(100) NOT NULL,
    esquema character varying(100) NOT NULL,
    usuario character varying(100) NOT NULL,
    password character varying(200) NOT NULL
);


--
-- TOC entry 177 (class 1259 OID 19720)
-- Name: nuntius_dbms_idconexion_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_dbms_idconexion_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3056 (class 0 OID 0)
-- Dependencies: 177
-- Name: nuntius_dbms_idconexion_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_dbms_idconexion_seq OWNED BY nuntius_dbms.idconexion;


--
-- TOC entry 179 (class 1259 OID 19731)
-- Name: nuntius_estilo; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_estilo (
    usuario character(50) DEFAULT '0'::bpchar NOT NULL,
    estilo character(50) NOT NULL
);


--
-- TOC entry 181 (class 1259 OID 19739)
-- Name: nuntius_logger; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_logger (
    id integer NOT NULL,
    evento character(255) NOT NULL,
    fecha character(50) NOT NULL
);


--
-- TOC entry 180 (class 1259 OID 19737)
-- Name: nuntius_logger_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_logger_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3057 (class 0 OID 0)
-- Dependencies: 180
-- Name: nuntius_logger_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_logger_id_seq OWNED BY nuntius_logger.id;


--
-- TOC entry 183 (class 1259 OID 19745)
-- Name: nuntius_pagina; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_pagina (
    id_pagina integer NOT NULL,
    nombre character(50) DEFAULT ''::bpchar NOT NULL,
    descripcion character(250) DEFAULT ''::bpchar NOT NULL,
    modulo character(50) DEFAULT ''::bpchar NOT NULL,
    nivel integer DEFAULT 0 NOT NULL,
    parametro character(255) NOT NULL
);


--
-- TOC entry 182 (class 1259 OID 19743)
-- Name: nuntius_pagina_id_pagina_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_pagina_id_pagina_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3058 (class 0 OID 0)
-- Dependencies: 182
-- Name: nuntius_pagina_id_pagina_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_pagina_id_pagina_seq OWNED BY nuntius_pagina.id_pagina;


--
-- TOC entry 188 (class 1259 OID 19786)
-- Name: nuntius_subsistema; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_subsistema (
    id_subsistema integer NOT NULL,
    nombre character varying(250) NOT NULL,
    etiqueta character varying(100) NOT NULL,
    id_pagina integer DEFAULT 0 NOT NULL,
    observacion text
);


--
-- TOC entry 187 (class 1259 OID 19784)
-- Name: nuntius_subsistema_id_subsistema_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_subsistema_id_subsistema_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3059 (class 0 OID 0)
-- Dependencies: 187
-- Name: nuntius_subsistema_id_subsistema_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_subsistema_id_subsistema_seq OWNED BY nuntius_subsistema.id_subsistema;


--
-- TOC entry 189 (class 1259 OID 19796)
-- Name: nuntius_tempformulario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_tempformulario (
    id_sesion character(32) NOT NULL,
    formulario character(100) NOT NULL,
    campo character(100) NOT NULL,
    valor text NOT NULL,
    fecha character(50) NOT NULL
);


--
-- TOC entry 185 (class 1259 OID 19760)
-- Name: nuntius_usuario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_usuario (
    id_usuario integer NOT NULL,
    nombre character varying(50) DEFAULT ''::character varying NOT NULL,
    apellido character varying(50) DEFAULT ''::character varying NOT NULL,
    correo character varying(100) DEFAULT ''::character varying NOT NULL,
    telefono character varying(50) DEFAULT ''::character varying NOT NULL,
    imagen character(255) NOT NULL,
    clave character varying(100) DEFAULT ''::character varying NOT NULL,
    tipo character varying(255) DEFAULT ''::character varying NOT NULL,
    estilo character varying(50) DEFAULT 'basico'::character varying NOT NULL,
    idioma character varying(50) DEFAULT 'es_es'::character varying NOT NULL,
    estado integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 184 (class 1259 OID 19758)
-- Name: nuntius_usuario_id_usuario_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE nuntius_usuario_id_usuario_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 3060 (class 0 OID 0)
-- Dependencies: 184
-- Name: nuntius_usuario_id_usuario_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE nuntius_usuario_id_usuario_seq OWNED BY nuntius_usuario.id_usuario;


--
-- TOC entry 186 (class 1259 OID 19778)
-- Name: nuntius_usuario_subsistema; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_usuario_subsistema (
    id_usuario integer DEFAULT 0 NOT NULL,
    id_subsistema integer DEFAULT 0 NOT NULL,
    estado integer DEFAULT 0 NOT NULL
);


--
-- TOC entry 190 (class 1259 OID 19802)
-- Name: nuntius_valor_sesion; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE nuntius_valor_sesion (
    sesionid character(32) NOT NULL,
    variable character(20) NOT NULL,
    valor character(255) NOT NULL,
    expiracion bigint DEFAULT 0 NOT NULL
);


SET search_path = menu, pg_catalog;

--
-- TOC entry 2867 (class 2604 OID 19914)
-- Name: id_grupo; Type: DEFAULT; Schema: menu; Owner: -
--

ALTER TABLE ONLY grupo ALTER COLUMN id_grupo SET DEFAULT nextval('grupo_id_grupo_seq'::regclass);


--
-- TOC entry 2871 (class 2604 OID 19915)
-- Name: id_item; Type: DEFAULT; Schema: menu; Owner: -
--

ALTER TABLE ONLY item ALTER COLUMN id_item SET DEFAULT nextval('item_id_item_seq'::regclass);


--
-- TOC entry 2874 (class 2604 OID 19916)
-- Name: id_menu; Type: DEFAULT; Schema: menu; Owner: -
--

ALTER TABLE ONLY menu ALTER COLUMN id_menu SET DEFAULT nextval('menu_id_menu_seq'::regclass);


--
-- TOC entry 2875 (class 2604 OID 19917)
-- Name: id_tipo_item; Type: DEFAULT; Schema: menu; Owner: -
--

ALTER TABLE ONLY tipo_item ALTER COLUMN id_tipo_item SET DEFAULT nextval('tipo_item_id_tipo_item_seq'::regclass);


SET search_path = public, pg_catalog;

--
-- TOC entry 2836 (class 2604 OID 19691)
-- Name: id_bloque; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_bloque ALTER COLUMN id_bloque SET DEFAULT nextval('nuntius_bloque_id_bloque_seq'::regclass);


--
-- TOC entry 2838 (class 2604 OID 19703)
-- Name: idrelacion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_bloque_pagina ALTER COLUMN idrelacion SET DEFAULT nextval('nuntius_bloque_pagina_idrelacion_seq'::regclass);


--
-- TOC entry 2842 (class 2604 OID 19714)
-- Name: id_parametro; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_configuracion ALTER COLUMN id_parametro SET DEFAULT nextval('nuntius_configuracion_id_parametro_seq'::regclass);


--
-- TOC entry 2843 (class 2604 OID 19725)
-- Name: idconexion; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_dbms ALTER COLUMN idconexion SET DEFAULT nextval('nuntius_dbms_idconexion_seq'::regclass);


--
-- TOC entry 2845 (class 2604 OID 19742)
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_logger ALTER COLUMN id SET DEFAULT nextval('nuntius_logger_id_seq'::regclass);


--
-- TOC entry 2846 (class 2604 OID 19748)
-- Name: id_pagina; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_pagina ALTER COLUMN id_pagina SET DEFAULT nextval('nuntius_pagina_id_pagina_seq'::regclass);


--
-- TOC entry 2864 (class 2604 OID 19789)
-- Name: id_subsistema; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_subsistema ALTER COLUMN id_subsistema SET DEFAULT nextval('nuntius_subsistema_id_subsistema_seq'::regclass);


--
-- TOC entry 2851 (class 2604 OID 19763)
-- Name: id_usuario; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_usuario ALTER COLUMN id_usuario SET DEFAULT nextval('nuntius_usuario_id_usuario_seq'::regclass);


SET search_path = menu, pg_catalog;

--
-- TOC entry 3032 (class 0 OID 19877)
-- Dependencies: 191
-- Data for Name: grupo; Type: TABLE DATA; Schema: menu; Owner: -
--

INSERT INTO grupo (id_grupo, descripcion, orden_grupo) VALUES (1, 'inicio', 10);
INSERT INTO grupo (id_grupo, descripcion, orden_grupo) VALUES (2, 'snies', 20);
INSERT INTO grupo (id_grupo, descripcion, orden_grupo) VALUES (3, 'spadies', 30);
INSERT INTO grupo (id_grupo, descripcion, orden_grupo) VALUES (5, 'registraduria', 50);
INSERT INTO grupo (id_grupo, descripcion, orden_grupo) VALUES (6, 'enlacesExternos', 60);
INSERT INTO grupo (id_grupo, descripcion, orden_grupo) VALUES (7, 'cerrarSesion', 70);
INSERT INTO grupo (id_grupo, descripcion, orden_grupo) VALUES (4, 'dps', 40);


--
-- TOC entry 3061 (class 0 OID 0)
-- Dependencies: 192
-- Name: grupo_id_grupo_seq; Type: SEQUENCE SET; Schema: menu; Owner: -
--

SELECT pg_catalog.setval('grupo_id_grupo_seq', 7, true);


--
-- TOC entry 3034 (class 0 OID 19885)
-- Dependencies: 193
-- Data for Name: item; Type: TABLE DATA; Schema: menu; Owner: -
--

INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (30, 1, 4, 2, 'jovenesenaccion', 1, 1, NULL, true, NULL);
INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (4, 1, 2, 3, 'corregirnombre', 1, 2, NULL, true, NULL);
INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (31, 1, 4, 3, 'generarreporte', 1, 1, 'reporteJeA', true, NULL);
INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (3, 1, 2, 3, 'actualizarInscrito', 1, 1, NULL, true, NULL);
INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (20, 1, 2, 2, 'tituloMatriculado', 2, 1, NULL, true, NULL);
INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (21, 1, 2, 3, 'reportarMatriculado', 2, 1, 'reportarMatriculado', true, NULL);
INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (1, 1, 1, 1, 'inicio', 1, 1, 'index', true, NULL);
INSERT INTO item (id_item, id_menu, id_grupo, id_tipo_item, descripcion, columna, orden_item, link, estado_registro, parametros) VALUES (2, 1, 2, 2, 'tituloInscrito', 1, 1, NULL, true, NULL);


--
-- TOC entry 3062 (class 0 OID 0)
-- Dependencies: 194
-- Name: item_id_item_seq; Type: SEQUENCE SET; Schema: menu; Owner: -
--

SELECT pg_catalog.setval('item_id_item_seq', 3, true);


--
-- TOC entry 3036 (class 0 OID 19896)
-- Dependencies: 195
-- Data for Name: menu; Type: TABLE DATA; Schema: menu; Owner: -
--

INSERT INTO menu (id_menu, descripcion, perfil_usuario, estado_registro) VALUES (1, 'principal', 0, true);


--
-- TOC entry 3063 (class 0 OID 0)
-- Dependencies: 196
-- Name: menu_id_menu_seq; Type: SEQUENCE SET; Schema: menu; Owner: -
--

SELECT pg_catalog.setval('menu_id_menu_seq', 1, true);


--
-- TOC entry 3038 (class 0 OID 19906)
-- Dependencies: 197
-- Data for Name: tipo_item; Type: TABLE DATA; Schema: menu; Owner: -
--

INSERT INTO tipo_item (id_tipo_item, descripcion) VALUES (1, 'menu');
INSERT INTO tipo_item (id_tipo_item, descripcion) VALUES (2, 'tittle');
INSERT INTO tipo_item (id_tipo_item, descripcion) VALUES (3, 'item');
INSERT INTO tipo_item (id_tipo_item, descripcion) VALUES (4, 'link');


--
-- TOC entry 3064 (class 0 OID 0)
-- Dependencies: 198
-- Name: tipo_item_id_tipo_item_seq; Type: SEQUENCE SET; Schema: menu; Owner: -
--

SELECT pg_catalog.setval('tipo_item_id_tipo_item_seq', 4, true);


SET search_path = public, pg_catalog;

--
-- TOC entry 3013 (class 0 OID 19688)
-- Dependencies: 172
-- Data for Name: nuntius_bloque; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-1, 'menuLateral                                       ', 'Menú lateral módulo de desarrollo.                                                                                                                                                                                                                             ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-2, 'pie                                               ', 'Pie de página módulo de desarrollo.                                                                                                                                                                                                                            ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-3, 'banner                                            ', 'Banner módulo de desarrollo.                                                                                                                                                                                                                                   ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-4, 'cruder                                            ', 'Módulo para crear módulos CRUD.                                                                                                                                                                                                                                ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-5, 'desenlace                                         ', 'Módulo de gestión de desenlace.                                                                                                                                                                                                                                ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-6, 'registro                                          ', 'Módulo para registrar páginas o módulos.                                                                                                                                                                                                                       ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-7, 'constructor                                       ', 'Módulo para diseñar páginas.                                                                                                                                                                                                                                   ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-8, 'contenidoCentral                                  ', 'Contenido página principal de desarrollo.                                                                                                                                                                                                                      ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-9, 'codificador                                       ', 'Módulo para decodificar cadenas.                                                                                                                                                                                                                               ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-10, 'plugin                                            ', 'Módulo para agregar plugin preconfigurados.                                                                                                                                                                                                                    ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (-11, 'saraFormCreator                                   ', 'Módulo para crear formulario con la recomendación de bloques de SARA.                                                                                                                                                                                          ', 'development                                                                                                                                                                                             ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (3, 'menuPrincipal                                     ', 'Menu principal basado en boostrap                                                                                                                                                                                                                              ', 'gui                                                                                                                                                                                                     ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (1, 'portada                                           ', 'Bloque con contenidos de la pagina de inicio                                                                                                                                                                                                                   ', 'autenticacion                                                                                                                                                                                           ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (10, 'listadoVariablesSnies                             ', 'Presenta listado de variables snies con totales en datatable                                                                                                                                                                                                   ', 'snies                                                                                                                                                                                                   ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (2, 'loginSso                                          ', 'Menú para login mediante sso con simpleSAMLphp                                                                                                                                                                                                                 ', 'autenticacion                                                                                                                                                                                           ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (11, 'reporteJeA                                        ', 'Genera reporte de jóvenes en acción para el Departamenro de Prosperidad Social (DPS)                                                                                                                                                                           ', 'jovenesEnAccion                                                                                                                                                                                         ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (4, 'actualizarInscrito                                ', 'Administración de la variable inscrito snies                                                                                                                                                                                                                   ', 'snies/inscrito                                                                                                                                                                                          ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (5, 'actualizarAdmitido                                ', 'Administración de la variable inscrito snies                                                                                                                                                                                                                   ', 'snies/admitido                                                                                                                                                                                          ');
INSERT INTO nuntius_bloque (id_bloque, nombre, descripcion, grupo) VALUES (6, 'reportarMatriculado                               ', 'Actualización de la variable matriculado                                                                                                                                                                                                                       ', 'snies/matriculado                                                                                                                                                                                       ');


--
-- TOC entry 3065 (class 0 OID 0)
-- Dependencies: 171
-- Name: nuntius_bloque_id_bloque_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_bloque_id_bloque_seq', 1, false);


--
-- TOC entry 3015 (class 0 OID 19700)
-- Dependencies: 174
-- Data for Name: nuntius_bloque_pagina; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (1, -1, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (2, -1, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (3, -1, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (4, -1, -8, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (5, -2, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (6, -2, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (7, -2, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (8, -2, -4, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (9, -3, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (10, -3, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (11, -3, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (12, -3, -5, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (13, -4, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (14, -4, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (15, -4, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (16, -4, -9, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (17, -5, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (18, -5, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (19, -5, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (20, -5, -6, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (21, -6, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (22, -6, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (23, -6, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (24, -6, -7, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (25, -7, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (26, -7, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (27, -7, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (28, -7, -10, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (29, -8, -1, 'B', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (30, -8, -2, 'E', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (31, -8, -3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (32, -8, -11, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (33, 1, 1, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (36, 2, 3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (37, 1, 2, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (35, 3, 4, 'C', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (34, 3, 3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (38, 4, 3, 'A', 1);
INSERT INTO nuntius_bloque_pagina (idrelacion, id_pagina, id_bloque, seccion, posicion) VALUES (39, 4, 6, 'C', 1);


--
-- TOC entry 3066 (class 0 OID 0)
-- Dependencies: 173
-- Name: nuntius_bloque_pagina_idrelacion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_bloque_pagina_idrelacion_seq', 39, true);


--
-- TOC entry 3017 (class 0 OID 19711)
-- Dependencies: 176
-- Data for Name: nuntius_configuracion; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (1, 'dbesquema                                                                                                                                                                                                                                                      ', 'public                                                                                                                                                                                                                                                         ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (2, 'prefijo                                                                                                                                                                                                                                                        ', 'nuntius_                                                                                                                                                                                                                                                       ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (4, 'raizDocumento                                                                                                                                                                                                                                                  ', '/usr/local/apache/htdocs/nuntius                                                                                                                                                                                                                               ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (5, 'host                                                                                                                                                                                                                                                           ', 'http://localhost                                                                                                                                                                                                                                               ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (6, 'site                                                                                                                                                                                                                                                           ', '/nuntius                                                                                                                                                                                                                                                       ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (7, 'nombreAdministrador                                                                                                                                                                                                                                            ', 'administrador                                                                                                                                                                                                                                                  ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (8, 'claveAdministrador                                                                                                                                                                                                                                             ', 'QzD2KbYl0-saTYx5kdEBSgTeYtz0u9a_8cTVPXvQjBM                                                                                                                                                                                                                    ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (9, 'correoAdministrador                                                                                                                                                                                                                                            ', 'fernandotower@gmail.com                                                                                                                                                                                                                                        ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (10, 'enlace                                                                                                                                                                                                                                                         ', 'data                                                                                                                                                                                                                                                           ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (11, 'estiloPredeterminado                                                                                                                                                                                                                                           ', 'cupertino                                                                                                                                                                                                                                                      ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (12, 'moduloDesarrollo                                                                                                                                                                                                                                               ', 'moduloDesarrollo                                                                                                                                                                                                                                               ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (13, 'googlemaps                                                                                                                                                                                                                                                     ', '                                                                                                                                                                                                                                                               ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (14, 'recatchapublica                                                                                                                                                                                                                                                ', '                                                                                                                                                                                                                                                               ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (15, 'recatchaprivada                                                                                                                                                                                                                                                ', '                                                                                                                                                                                                                                                               ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (16, 'expiracion                                                                                                                                                                                                                                                     ', '5                                                                                                                                                                                                                                                              ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (17, 'instalado                                                                                                                                                                                                                                                      ', 'true                                                                                                                                                                                                                                                           ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (18, 'debugMode                                                                                                                                                                                                                                                      ', 'false                                                                                                                                                                                                                                                          ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (19, 'dbPrincipal                                                                                                                                                                                                                                                    ', 'nuntius                                                                                                                                                                                                                                                        ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (20, 'hostSeguro                                                                                                                                                                                                                                                     ', 'https://localhost                                                                                                                                                                                                                                              ');
INSERT INTO nuntius_configuracion (id_parametro, parametro, valor) VALUES (3, 'nombreAplicativo                                                                                                                                                                                                                                               ', 'Nuntius Integración Sectorial                                                                                                                                                                                                                                  ');


--
-- TOC entry 3067 (class 0 OID 0)
-- Dependencies: 175
-- Name: nuntius_configuracion_id_parametro_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_configuracion_id_parametro_seq', 20, true);


--
-- TOC entry 3019 (class 0 OID 19722)
-- Dependencies: 178
-- Data for Name: nuntius_dbms; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO nuntius_dbms (idconexion, nombre, dbms, servidor, puerto, conexionssh, db, esquema, usuario, password) VALUES (1, 'estructura', 'pgsql', 'localhost', 5432, '', 'nuntius', 'public', 'snies', 'm-sR5ImpbFUeODq7knOOrMYFJZQ5re0-nx4rXCuuRoo');
INSERT INTO nuntius_dbms (idconexion, nombre, dbms, servidor, puerto, conexionssh, db, esquema, usuario, password) VALUES (2, 'menu', 'pgsql', 'localhost', 5432, '', 'nuntius', 'menu', 'snies', 'm-sR5ImpbFUeODq7knOOrMYFJZQ5re0-nx4rXCuuRoo');
INSERT INTO nuntius_dbms (idconexion, nombre, dbms, servidor, puerto, conexionssh, db, esquema, usuario, password) VALUES (3, 'academica', 'oci8', '10.20.0.4', 1521, '', 'sudd', 'public', 'snies', 'm-sR5ImpbFUeODq7knOOrMYFJZQ5re0-nx4rXCuuRoo');
INSERT INTO nuntius_dbms (idconexion, nombre, dbms, servidor, puerto, conexionssh, db, esquema, usuario, password) VALUES (4, 'sniesLocal', 'pgsql', '10.20.0.15', 5432, '', 'ods', 'public', 'postgres', 'SPz6g5XRhlH03hLasFP0f-zkQOx-RBMnUzt7cWp88uo');


--
-- TOC entry 3068 (class 0 OID 0)
-- Dependencies: 177
-- Name: nuntius_dbms_idconexion_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_dbms_idconexion_seq', 4, true);


--
-- TOC entry 3020 (class 0 OID 19731)
-- Dependencies: 179
-- Data for Name: nuntius_estilo; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3022 (class 0 OID 19739)
-- Dependencies: 181
-- Data for Name: nuntius_logger; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3069 (class 0 OID 0)
-- Dependencies: 180
-- Name: nuntius_logger_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_logger_id_seq', 1, false);


--
-- TOC entry 3024 (class 0 OID 19745)
-- Dependencies: 183
-- Data for Name: nuntius_pagina; Type: TABLE DATA; Schema: public; Owner: -
--

INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-1, 'development                                       ', 'Index módulo de desarrollo.                                                                                                                                                                                                                               ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-2, 'cruder                                            ', 'Generador módulos CRUD.                                                                                                                                                                                                                                   ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-3, 'desenlace                                         ', 'Analizar enlaces.                                                                                                                                                                                                                                         ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-4, 'codificador                                       ', 'Codificar/decodificar cadenas.                                                                                                                                                                                                                            ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-5, 'registro                                          ', 'Registrar páginas o módulos.                                                                                                                                                                                                                              ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-6, 'constructor                                       ', 'Diseñar páginas.                                                                                                                                                                                                                                          ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-7, 'plugin                                            ', 'Agregar plugin preconfigurados.                                                                                                                                                                                                                           ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (-8, 'saraFormCreator                                   ', 'Módulo SARA form creator.                                                                                                                                                                                                                                 ', 'development                                       ', 0, 'jquery=true&jquery-ui=true&jquery-validation=true                                                                                                                                                                                                              ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (1, 'index                                             ', 'Pagina de inicio del sistema de integración sectorial NUNTIUS                                                                                                                                                                                             ', 'general                                           ', 0, 'jquery=true&jquery-ui=true                                                                                                                                                                                                                                     ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (2, 'inicio                                            ', 'Pagina que presenta menú principal y bievenida                                                                                                                                                                                                            ', 'snies                                             ', 0, 'jquery=true&jquery-ui=true                                                                                                                                                                                                                                     ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (3, 'actualizzarInscrito                               ', 'Página de administración de la variable inscrito en el SNIES LOCAL                                                                                                                                                                                        ', 'snies                                             ', 0, 'jquery=true&jquery-ui=true&datatables=true                                                                                                                                                                                                                     ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (5, 'reporteJeA                                        ', 'Pagina para generar y descargar reportes de jóvenes en acción para el departamento de Prosperidad Social (DPS)                                                                                                                                            ', 'jovenesEnAccion                                   ', 0, 'jquery=true&jquery-ui=true&datatables=true                                                                                                                                                                                                                     ');
INSERT INTO nuntius_pagina (id_pagina, nombre, descripcion, modulo, nivel, parametro) VALUES (4, 'reportarMatriculado                               ', 'Página de aactualización de la variable matriculado en el SNIES LOCAL                                                                                                                                                                                     ', 'snies                                             ', 0, 'jquery=true&jquery-ui=true&datatables=true                                                                                                                                                                                                                     ');


--
-- TOC entry 3070 (class 0 OID 0)
-- Dependencies: 182
-- Name: nuntius_pagina_id_pagina_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_pagina_id_pagina_seq', 1, false);


--
-- TOC entry 3029 (class 0 OID 19786)
-- Dependencies: 188
-- Data for Name: nuntius_subsistema; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3071 (class 0 OID 0)
-- Dependencies: 187
-- Name: nuntius_subsistema_id_subsistema_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_subsistema_id_subsistema_seq', 1, false);


--
-- TOC entry 3030 (class 0 OID 19796)
-- Dependencies: 189
-- Data for Name: nuntius_tempformulario; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3026 (class 0 OID 19760)
-- Dependencies: 185
-- Data for Name: nuntius_usuario; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3072 (class 0 OID 0)
-- Dependencies: 184
-- Name: nuntius_usuario_id_usuario_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('nuntius_usuario_id_usuario_seq', 1, false);


--
-- TOC entry 3027 (class 0 OID 19778)
-- Dependencies: 186
-- Data for Name: nuntius_usuario_subsistema; Type: TABLE DATA; Schema: public; Owner: -
--



--
-- TOC entry 3031 (class 0 OID 19802)
-- Dependencies: 190
-- Data for Name: nuntius_valor_sesion; Type: TABLE DATA; Schema: public; Owner: -
--



SET search_path = menu, pg_catalog;

--
-- TOC entry 2895 (class 2606 OID 19919)
-- Name: grupo_pkey; Type: CONSTRAINT; Schema: menu; Owner: -
--

ALTER TABLE ONLY grupo
    ADD CONSTRAINT grupo_pkey PRIMARY KEY (id_grupo);


--
-- TOC entry 2897 (class 2606 OID 19921)
-- Name: item_pkey; Type: CONSTRAINT; Schema: menu; Owner: -
--

ALTER TABLE ONLY item
    ADD CONSTRAINT item_pkey PRIMARY KEY (id_item);


--
-- TOC entry 2899 (class 2606 OID 19923)
-- Name: menu_pkey; Type: CONSTRAINT; Schema: menu; Owner: -
--

ALTER TABLE ONLY menu
    ADD CONSTRAINT menu_pkey PRIMARY KEY (id_menu);


--
-- TOC entry 2901 (class 2606 OID 19925)
-- Name: tipo_item_pkey; Type: CONSTRAINT; Schema: menu; Owner: -
--

ALTER TABLE ONLY tipo_item
    ADD CONSTRAINT tipo_item_pkey PRIMARY KEY (id_tipo_item);


SET search_path = public, pg_catalog;

--
-- TOC entry 2879 (class 2606 OID 19708)
-- Name: nuntius_bloque_pagina_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_bloque_pagina
    ADD CONSTRAINT nuntius_bloque_pagina_pkey PRIMARY KEY (idrelacion);


--
-- TOC entry 2877 (class 2606 OID 19697)
-- Name: nuntius_bloque_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_bloque
    ADD CONSTRAINT nuntius_bloque_pkey PRIMARY KEY (id_bloque);


--
-- TOC entry 2881 (class 2606 OID 19719)
-- Name: nuntius_configuracion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_configuracion
    ADD CONSTRAINT nuntius_configuracion_pkey PRIMARY KEY (id_parametro);


--
-- TOC entry 2883 (class 2606 OID 19730)
-- Name: nuntius_dbms_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_dbms
    ADD CONSTRAINT nuntius_dbms_pkey PRIMARY KEY (idconexion);


--
-- TOC entry 2885 (class 2606 OID 19736)
-- Name: nuntius_estilo_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_estilo
    ADD CONSTRAINT nuntius_estilo_pkey PRIMARY KEY (usuario, estilo);


--
-- TOC entry 2887 (class 2606 OID 19757)
-- Name: nuntius_pagina_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_pagina
    ADD CONSTRAINT nuntius_pagina_pkey PRIMARY KEY (id_pagina);


--
-- TOC entry 2891 (class 2606 OID 19795)
-- Name: nuntius_subsistema_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_subsistema
    ADD CONSTRAINT nuntius_subsistema_pkey PRIMARY KEY (id_subsistema);


--
-- TOC entry 2889 (class 2606 OID 19777)
-- Name: nuntius_usuario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_usuario
    ADD CONSTRAINT nuntius_usuario_pkey PRIMARY KEY (id_usuario);


--
-- TOC entry 2893 (class 2606 OID 19807)
-- Name: nuntius_valor_sesion_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY nuntius_valor_sesion
    ADD CONSTRAINT nuntius_valor_sesion_pkey PRIMARY KEY (sesionid, variable);


SET search_path = menu, pg_catalog;

--
-- TOC entry 2902 (class 2606 OID 19926)
-- Name: item_id_grupo_fkey; Type: FK CONSTRAINT; Schema: menu; Owner: -
--

ALTER TABLE ONLY item
    ADD CONSTRAINT item_id_grupo_fkey FOREIGN KEY (id_grupo) REFERENCES grupo(id_grupo);


--
-- TOC entry 2903 (class 2606 OID 19931)
-- Name: item_id_menu_fkey; Type: FK CONSTRAINT; Schema: menu; Owner: -
--

ALTER TABLE ONLY item
    ADD CONSTRAINT item_id_menu_fkey FOREIGN KEY (id_menu) REFERENCES menu(id_menu);


--
-- TOC entry 2904 (class 2606 OID 19936)
-- Name: item_id_tipo_item_fkey; Type: FK CONSTRAINT; Schema: menu; Owner: -
--

ALTER TABLE ONLY item
    ADD CONSTRAINT item_id_tipo_item_fkey FOREIGN KEY (id_tipo_item) REFERENCES tipo_item(id_tipo_item);


-- Completed on 2015-11-20 18:05:03 COT

--
-- PostgreSQL database dump complete
--

