create table typeVehicule
(
	id integer primary key,
	libelle char(60),
    tarifKm DECIMAL(8, 2)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

insert into typeVehicule(id, libelle, tarifKm) values
(1,'Véhicule  4CV Diesel', 0.52),
(2,'Véhicule 5/6CV Diesel', 0.58),
(3,'Véhicule  4CV Essence', 0.62),
(4,'Véhicule 5/6CV Essence', 0.67);

select * from typevehicule;
select * from visiteur;

ALTER TABLE visiteur
ADD COLUMN idTypeVehicule INTEGER NULL;

ALTER TABLE visiteur
ADD CONSTRAINT fk_visiteur_idTypeVehicule
FOREIGN KEY (idTypeVehicule) REFERENCES typeVehicule(id);

SET SQL_SAFE_UPDATES = 0;


update visiteur
set idTypeVehicule = 1
where dateembauche BETWEEN '1980-01-01' AND '1995-12-31';

update visiteur
set idTypeVehicule = 2
where dateembauche BETWEEN '1996-01-01' AND '2002-12-31';

update visiteur
set idTypeVehicule = 3
where dateembauche BETWEEN '2003-01-01' AND '2015-12-31';

update visiteur
set idTypeVehicule = 4
where dateembauche BETWEEN '2016-01-01' AND now();

commit;