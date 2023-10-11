DROP TABLE IF EXISTS usuarios cascade;

CREATE TABLE usuarios(
    id  bigserial PRIMARY KEY,
    nombre  varchar NOT NULL,
    password varchar NOT NULL
);
