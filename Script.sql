DROP TABLE IF EXISTS public."mainTable" CASCADE;
CREATE TABLE public."mainTable"(
	id serial NOT NULL,
	"fkTable_id" int4 NOT NULL,
	CONSTRAINT "PK_mainTable" PRIMARY KEY (id)

);

ALTER TABLE public."mainTable" OWNER TO postgres;

DROP TABLE IF EXISTS public."fkTable" CASCADE;
CREATE TABLE public."fkTable"(
	id serial NOT NULL,
	code text NOT NULL,
	CONSTRAINT "pk_fkTable" PRIMARY KEY (id)

);

ALTER TABLE public."mainTable" ADD CONSTRAINT "FK_mainTable" FOREIGN KEY ("fkTable_id")
REFERENCES public."fkTable" (id) MATCH FULL
ON DELETE NO ACTION ON UPDATE NO ACTION;

CREATE or replace FUNCTION FN_testForeignKey (in fkText text)
	RETURNS int4
	AS $$
DECLARE
fkID int4;
BEGIN
	SELECT "id" INTO fkID FROM "fkTable" WHERE "code" = fkText;
	IF NOT FOUND THEN
	    insert into "fkTable"(code) 
		values(fkText) 
		returning "id" into fkID;
	END IF;
	insert into "mainTable"("fkTable_id") values (fkID) 
	returning "id" into fkID;
	return fkID;
END;
$$  LANGUAGE plpgsql;
