BEGIN;

\i FD_algemeen.sql
\i FD_vti_metadata.sql
\i FD_NM.sql
\i FD_S.sql

--ROLLBACK;
COMMIT;
