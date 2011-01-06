-- Aanmaak van een composiet type om metadata over een vaag tijdsinterval bij te houden.
DROP TYPE IF EXISTS FD_vti_metadata CASCADE;
CREATE TYPE FD_vti_metadata AS (
	sa 				varchar(10),
	ka				varchar(10),
	kb 				varchar(10),
	sb				varchar(10),
	type_van 		varchar(20),
	type_tot		varchar(20),
	omschrijving_van	varchar(50),
	omschrijving_tot	varchar(50),
	omschrijving_van_manueel	boolean,
	omschrijving_tot_manueel	boolean,
	omschrijving		varchar(110)
);

COMMENT ON TYPE FD_vti_metadata IS 'Composiet type om metadata over een vaag tijdsinterval bij te houden.';

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(vm FD_vti_metadata) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling(FD_maakX($1.sa),FD_maakX($1.ka),Fd_maakX($1.kb),FD_maakX($1.sb));
$$ LANGUAGE sql IMMUTABLE;
