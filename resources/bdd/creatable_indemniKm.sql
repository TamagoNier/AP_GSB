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

update visiteur
set idTypeVehicule = 1;

SET SQL_SAFE_UPDATES = 0;


commit;