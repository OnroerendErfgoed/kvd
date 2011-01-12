DROP TABLE persoon;

CREATE TABLE persoon (
	id integer NOT NULL,
	naam character varying(255) NOT NULL,
	voornaam character varying(255),
	geboortedatum date,
	sterfdatum date,
	PRIMARY KEY(id)
);		

SELECT AddGeometryColumn('persoon','vti',-1,'LINESTRING',2);
