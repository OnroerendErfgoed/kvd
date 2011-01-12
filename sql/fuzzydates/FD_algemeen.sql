CREATE OR REPLACE FUNCTION FD_oudste() RETURNS float AS $$	
	SELECT -1000000.0::float;
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_jongste() RETURNS float AS $$
	SELECT 100000.0::float;
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakX(datum date) RETURNS float AS $$
DECLARE
	jaar int;
	laatste_dag_jaar date;
	aantal_dagen_in_jaar int;
BEGIN
	jaar := EXTRACT (year FROM $1);
	IF jaar < 0 THEN
		laatste_dag_jaar = (lpad(abs(jaar)::text,4,'0') || '-12-31 BC')::date;
		jaar := jaar + 1;
	ELSE
		laatste_dag_jaar = (lpad(abs(jaar)::text,4,'0') || '-12-31 AD')::date;
	END IF;
	aantal_dagen_in_jaar := EXTRACT(doy FROM (laatste_dag_jaar));
	RETURN jaar + ((EXTRACT(doy FROM $1) -1) / aantal_dagen_in_jaar);
END
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakX(jaar integer) RETURNS float AS $$
DECLARE
	x	float;
BEGIN
	IF jaar = 0 THEN
		RAISE WARNING 'Het jaar 0 bestaat niet en wordt omgezet naar het jaar -1';
		x := -1;
	ELSEIF jaar < 0 THEN
		x := jaar + 1.0;
	ELSE
		x := jaar;
	END IF;
    RETURN jaar;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakX(tekst varchar) RETURNS float AS $$
BEGIN
	    RETURN FD_maakX(tekst::integer);
EXCEPTION WHEN others THEN
	    RETURN FD_maakX(tekst::date);                                                  
END;
$$ LANGUAGE plpgsql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakDatum(x float) RETURNS date AS $$
DECLARE
	jaar int;
	dagen float;
	dag int;
	laatste_dag_jaar date;
	aantal_dagen_in_jaar int;
BEGIN
	IF x >= 1 THEN
		jaar := floor(x);
		laatste_dag_jaar = (lpad(abs(jaar)::text,4,'0') || '-12-31 AD')::date;
		dagen := x - jaar;
	ELSE 
		jaar := floor(x-1);
		laatste_dag_jaar = (lpad(abs(jaar)::text,4,'0') || '-12-31 BC')::date;
		dagen := abs(x-1) - abs(jaar);
	END IF;
	aantal_dagen_in_jaar := EXTRACT(doy FROM (laatste_dag_jaar));
	dag := floor((dagen * aantal_dagen_in_jaar) + 1);
	IF x > 0 THEN
		RETURN lpad(jaar::text,4,'0') || '.' || lpad(dag::text,3,'0');
	ELSE 
		RETURN lpad(abs(jaar)::text,4,'0') || '.' || lpad(dag::text,3,'0') || ' BC';
	END IF;
END;
$$ LANGUAGE plpgsql IMMUTABLE;

COMMENT ON FUNCTION FD_maakDatum(x float) IS 'Met deze functie kan uit een x-waarde van een VTI ongeveer de overeenkomende datum berekend worden. Wegens afrondingsfouten kan er een dag verschil zijn.';

CREATE OR REPLACE FUNCTION FD_maakJaar(x float) RETURNS integer AS $$
DECLARE
	jaar integer;
BEGIN
	IF x <= 0 THEN
		jaar := x - 1;
	ELSE
		jaar := x;
	END IF;
	RETURN jaar;
END;
$$ LANGUAGE plpgsql IMMUTABLE;
COMMENT ON FUNCTION FD_maakJaar(x float) IS 'Met deze functie kan uit een x-waarde van een VTI het overeenkomende jaar berekend worden.';

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(sa float, ka float, kb float, sb float) RETURNS geometry AS $$
	SELECT ST_MakeLine(ARRAY[ST_MakePoint($1,0),
						ST_MakePoint($2,1),
						ST_MakePoint($3,1),
						ST_MakePoint($4,0)]);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(sa varchar, ka varchar, kb varchar, sb varchar) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling(	FD_maakX($1),
								FD_maakX($2),
								FD_maakX($3),
								FD_maakX($4));
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(ka varchar, kb varchar) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling($1,$1,$2,$2);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(d varchar) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling($1,$1,$1,$1);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(sa date, ka date, kb date, sb date) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling(	FD_maakX($1),
								FD_maakX($2),
								FD_maakX($3),
								FD_maakX($4));
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(ka date, kb date) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling($1,$1,$2,$2);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(d date) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling($1,$1,$1,$1);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(sa integer, ka integer, kb integer, sb integer) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling(	FD_maakX($1),
								FD_maakX($2),
								FD_maakX($3),
								FD_maakX($4));
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(ka integer, kb integer) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling($1,$1,$2,$2);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_maakVoorstelling(d integer) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling($1,$1,$1,$1);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_vervaag(ka date, kb date, lv interval, rv interval) RETURNS geometry AS $$
	SELECT FD_maakVoorstelling(($1 - $3)::date, $1, $2, ($2 + $4)::date);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_vervaag(ka date, kb date, v interval) RETURNS geometry AS $$
	SELECT FD_vervaag($1,$2,$3,$3);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_vervaag(d date, v interval) RETURNS geometry AS $$
	SELECT FD_vervaag($1,$1,$2,$2);
$$ LANGUAGE sql IMMUTABLE;

CREATE OR REPLACE FUNCTION FD_vervaag(d date, lv interval, rv interval) RETURNS geometry AS $$
	SELECT FD_vervaag($1,$1,$2,$3);
$$ LANGUAGE sql IMMUTABLE;
