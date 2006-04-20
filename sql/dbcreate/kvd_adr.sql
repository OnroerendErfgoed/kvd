SET NAMES 'LATIN1';

DROP SCHEMA kvd_adr CASCADE;

BEGIN TRANSACTION;

CREATE SCHEMA kvd_adr;

-- Provincie (Te gebruiken als keuzelijst)
CREATE TABLE kvd_adr.provincie (
	id integer NOT NULL,
	provincie varchar(30),
	CONSTRAINT provincie_pkey PRIMARY KEY (Id),
	CONSTRAINT prvincie_provincie UNIQUE (sector)
);

INSERT INTO kvd_adr.provincie VALUES ( 10000, 'Antwerpen');
INSERT INTO kvd_adr.provincie VALUES ( 20001, 'Vlaams-Brabant');
INSERT INTO kvd_adr.provincie VALUES ( 30000, 'West-Vlaanderen');
INSERT INTO kvd_adr.provincie VALUES ( 40000, 'Oost-Vlaanderen');
INSERT INTO kvd_adr.provincie VALUES ( 70000, 'Limburg');
