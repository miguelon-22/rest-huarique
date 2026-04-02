-- Pollería Huarique - Official Database Schema for PostgreSQL

-- Drop tables if they exist to start fresh
DROP TABLE IF EXISTS public.detalle_pedidos CASCADE;
DROP TABLE IF EXISTS public.pedidos CASCADE;
DROP TABLE IF EXISTS public.menus CASCADE;
DROP TABLE IF EXISTS public.categorias CASCADE;
DROP TABLE IF EXISTS public.blogs CASCADE;
DROP TABLE IF EXISTS public.clientes CASCADE;
DROP TABLE IF EXISTS public.direcciones_restaurante CASCADE;
DROP TABLE IF EXISTS public.horarios_restaurante CASCADE;
DROP TABLE IF EXISTS public.politicas_privacidad CASCADE;
DROP TABLE IF EXISTS public.redes_sociales CASCADE;
DROP TABLE IF EXISTS public.reservas_mesa CASCADE;
DROP TABLE IF EXISTS public.telefonos_restaurante CASCADE;
DROP TABLE IF EXISTS public.terminos_condiciones CASCADE;
DROP TABLE IF EXISTS public.testimonios CASCADE;
DROP TABLE IF EXISTS public.usuarios CASCADE;

-- Sequences for manual IDs if needed (or use SERIAL)
CREATE SEQUENCE IF NOT EXISTS blogs_id_seq;
CREATE SEQUENCE IF NOT EXISTS categorias_id_seq;
CREATE SEQUENCE IF NOT EXISTS clientes_id_seq;
CREATE SEQUENCE IF NOT EXISTS detalle_pedidos_id_seq;
CREATE SEQUENCE IF NOT EXISTS direcciones_restaurante_id_seq;
CREATE SEQUENCE IF NOT EXISTS horarios_restaurante_id_seq;
CREATE SEQUENCE IF NOT EXISTS menus_id_seq;
CREATE SEQUENCE IF NOT EXISTS pedidos_id_seq;
CREATE SEQUENCE IF NOT EXISTS politicas_privacidad_id_seq;
CREATE SEQUENCE IF NOT EXISTS redes_sociales_id_seq;
CREATE SEQUENCE IF NOT EXISTS reservas_mesa_id_seq;
CREATE SEQUENCE IF NOT EXISTS telefonos_restaurante_id_seq;
CREATE SEQUENCE IF NOT EXISTS terminos_condiciones_id_seq;
CREATE SEQUENCE IF NOT EXISTS testimonios_id_seq;
CREATE SEQUENCE IF NOT EXISTS usuarios_id_seq;

CREATE TABLE public.blogs (
  id bigint NOT NULL DEFAULT nextval('blogs_id_seq'::regclass),
  nombre character varying NOT NULL,
  contenido text NOT NULL,
  imagen character varying NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT blogs_pkey PRIMARY KEY (id)
);

CREATE TABLE public.categorias (
  id bigint NOT NULL DEFAULT nextval('categorias_id_seq'::regclass),
  nombre character varying NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT categorias_pkey PRIMARY KEY (id)
);

CREATE TABLE public.clientes (
  id bigint NOT NULL DEFAULT nextval('clientes_id_seq'::regclass),
  nombre character varying,
  email character varying,
  telefono character varying,
  direccion character varying,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT clientes_pkey PRIMARY KEY (id)
);

CREATE TABLE public.usuarios (
  id bigint NOT NULL DEFAULT nextval('usuarios_id_seq'::regclass),
  primer_nombre character varying NOT NULL,
  segundo_nombre character varying,
  apellido character varying NOT NULL,
  email character varying NOT NULL UNIQUE,
  password character varying NOT NULL,
  rol character varying NOT NULL DEFAULT 'admin'::character varying CHECK (rol::text = ANY (ARRAY['admin'::character varying, 'super_admin'::character varying]::text[])),
  estado smallint NOT NULL DEFAULT 0,
  aviso text,
  telefono character varying,
  direccion text,
  foto_perfil character varying,
  token_activacion character varying,
  remember_token character varying,
  autenticacion_dos_factores smallint NOT NULL DEFAULT 0,
  email_verificado_en timestamp without time zone,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  token_recuperacion character varying,
  expiracion_token timestamp without time zone,
  CONSTRAINT usuarios_pkey PRIMARY KEY (id)
);

CREATE TABLE public.pedidos (
  id bigint NOT NULL DEFAULT nextval('pedidos_id_seq'::regclass),
  precio_total numeric NOT NULL,
  estado character varying NOT NULL DEFAULT 'pendiente'::character varying CHECK (estado::text = ANY (ARRAY['pendiente'::character varying, 'completado'::character varying, 'cancelado'::character varying]::text[])),
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  cliente_id bigint,
  tipo_pedido character varying NOT NULL CHECK (tipo_pedido::text = ANY (ARRAY['online'::character varying, 'presencial'::character varying]::text[])),
  creado_por_usuario_id bigint,
  actualizado_por_usuario_id bigint,
  numero_pedido character varying NOT NULL,
  metodo_pago character varying NOT NULL,
  informacion_adicional character varying,
  costo_envio numeric,
  distancia_envio character varying,
  precio_por_milla numeric,
  estado_pago_online character varying CHECK (estado_pago_online::text = ANY (ARRAY['pagado'::character varying, 'no_pagado'::character varying]::text[])),
  session_id character varying,
  CONSTRAINT pedidos_pkey PRIMARY KEY (id),
  CONSTRAINT fk_pedido_cliente FOREIGN KEY (cliente_id) REFERENCES public.clientes(id),
  CONSTRAINT fk_pedido_creado_usuario FOREIGN KEY (creado_por_usuario_id) REFERENCES public.usuarios(id),
  CONSTRAINT fk_pedido_actualizado_usuario FOREIGN KEY (actualizado_por_usuario_id) REFERENCES public.usuarios(id)
);

CREATE TABLE public.detalle_pedidos (
  id bigint NOT NULL DEFAULT nextval('detalle_pedidos_id_seq'::regclass),
  pedido_id bigint NOT NULL,
  cantidad integer NOT NULL,
  subtotal numeric NOT NULL,
  nombre_menu character varying NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT detalle_pedidos_pkey PRIMARY KEY (id),
  CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES public.pedidos(id)
);

CREATE TABLE public.menus (
  id bigint NOT NULL DEFAULT nextval('menus_id_seq'::regclass),
  nombre character varying NOT NULL,
  descripcion text NOT NULL,
  precio numeric NOT NULL,
  imagen character varying NOT NULL,
  categoria_id bigint NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT menus_pkey PRIMARY KEY (id),
  CONSTRAINT fk_menu_categoria FOREIGN KEY (categoria_id) REFERENCES public.categorias(id)
);

CREATE TABLE public.reservas_mesa (
  id bigint NOT NULL DEFAULT nextval('reservas_mesa_id_seq'::regclass),
  nombre character varying NOT NULL,
  dni character varying NOT NULL,
  email character varying NOT NULL,
  telefono character varying NOT NULL,
  fecha date NOT NULL,
  hora time without time zone NOT NULL,
  cantidad_personas integer NOT NULL,
  informacion_adicional text,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT reservas_mesa_pkey PRIMARY KEY (id)
);

-- Additional utility tables from user request
CREATE TABLE public.direcciones_restaurante (
  id bigint NOT NULL DEFAULT nextval('direcciones_restaurante_id_seq'::regclass),
  direccion character varying NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT direcciones_restaurante_pkey PRIMARY KEY (id)
);

CREATE TABLE public.horarios_restaurante (
  id bigint NOT NULL DEFAULT nextval('horarios_restaurante_id_seq'::regclass),
  horario character varying NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT horarios_restaurante_pkey PRIMARY KEY (id)
);

CREATE TABLE public.politicas_privacidad (
  id bigint NOT NULL DEFAULT nextval('politicas_privacidad_id_seq'::regclass),
  contenido text NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT politicas_privacidad_pkey PRIMARY KEY (id)
);

CREATE TABLE public.redes_sociales (
  id bigint NOT NULL DEFAULT nextval('redes_sociales_id_seq'::regclass),
  usuario character varying NOT NULL,
  red_social character varying NOT NULL CHECK (red_social::text = ANY (ARRAY['facebook'::character varying, 'instagram'::character varying, 'youtube'::character varying, 'tiktok'::character varying]::text[])),
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT redes_sociales_pkey PRIMARY KEY (id)
);

CREATE TABLE public.telefonos_restaurante (
  id bigint NOT NULL DEFAULT nextval('telefonos_restaurante_id_seq'::regclass),
  telefono character varying NOT NULL,
  usar_whatsapp integer NOT NULL DEFAULT 0,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT telefonos_restaurante_pkey PRIMARY KEY (id)
);

CREATE TABLE public.terminos_condiciones (
  id bigint NOT NULL DEFAULT nextval('terminos_condiciones_id_seq'::regclass),
  contenido text NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT terminos_condiciones_pkey PRIMARY KEY (id)
);

CREATE TABLE public.testimonios (
  id bigint NOT NULL DEFAULT nextval('testimonios_id_seq'::regclass),
  nombre character varying NOT NULL,
  contenido text NOT NULL,
  creado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT testimonios_pkey PRIMARY KEY (id)
);

-- Seed Data for the new schema
INSERT INTO public.categorias (nombre) VALUES 
('Pollo a la Brasa'), 
('Parrillas y Carnes'), 
('Entradas y Piqueos'), 
('Bebidas y Jugos');

INSERT INTO public.menus (nombre, descripcion, precio, imagen, categoria_id) VALUES 
('Pollo Entero Huarique', 'Pollo a la brasa con papas fritas crocantes y ensalada clásica.', 58.00, 'https://images.unsplash.com/photo-1598103442097-8b74394b95c6?auto=format&fit=crop&q=80&w=400', 1),
('1/4 de Pollo Brasa', 'Porción personal acompañada de papas y ensalada.', 18.50, 'https://images.unsplash.com/photo-1594221708779-94832f4320d1?auto=format&fit=crop&q=80&w=400', 1),
('Anticuchos Tradicionales', '2 palos de anticucho de corazón con papa dorada y choclo.', 26.00, 'https://images.unsplash.com/photo-1562601579-599dec554e15?auto=format&fit=crop&q=80&w=400', 3),
('Lomo Saltado de la Casa', 'Carne de res seleccionada, cebolla, tomate y ají amarillo flambeado al wok.', 34.00, 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&q=80&w=400', 2),
('Chicha Morada de 1 Litro', 'Preparada con maíz morado, piña y especias. 100% natural.', 12.00, 'https://images.unsplash.com/photo-1541544741938-0af808871cc0?auto=format&fit=crop&q=80&w=400', 4);

INSERT INTO public.usuarios (primer_nombre, apellido, email, password, rol, estado) VALUES 
('Admin', 'Huarique', 'admin@huarique.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin', 1);

INSERT INTO public.testimonios (nombre, contenido) VALUES 
-- End of original schema --

CREATE TABLE IF NOT EXISTS public.configuraciones (
  id serial PRIMARY KEY,
  clave varchar NOT NULL UNIQUE,
  valor text NOT NULL,
  creado_en timestamp DEFAULT CURRENT_TIMESTAMP,
  actualizado_en timestamp DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE public.reservas_mesa ADD COLUMN IF NOT EXISTS estado_pago varchar DEFAULT 'pendiente' CHECK (estado_pago IN ('pendiente', 'pagado', 'fallido'));
ALTER TABLE public.reservas_mesa ADD COLUMN IF NOT EXISTS payment_id varchar;
ALTER TABLE public.reservas_mesa ADD COLUMN IF NOT EXISTS monto_adelanto numeric DEFAULT 20.00;

-- Seed configurations
INSERT INTO public.configuraciones (clave, valor) VALUES 
('site_name', 'HUARIQUE RESTAURANTE'),
('hero_title', 'EL SABOR QUE TRASCIENDE EL TIEMPO'),
('hero_subtitle', 'Sabor 2.0: Tradición milenaria, algoritmos de sabor modernos.')
ON CONFLICT (clave) DO NOTHING;
