use gsb_frais;

-- alter table visiteur
-- add iscomptable boolean default false not null;
-- 
-- update visiteur 
-- set iscomptable = true
-- where id = 'a118y';

-- CREATE USER 'createurGsb'@'localhost' IDENTIFIED BY 'secret';
-- GRANT create, references, drop, alter ON *.* TO 'createurGsb'@'localhost';
-- 
-- CREATE USER 'userGsb'@'localhost' IDENTIFIED BY 'secret';
-- GRANT select, insert, update, delete ON gsb_frais.* TO 'userGsb'@'localhost';

CREATE USER IF NOT EXISTS 'userGsb'@'localhost' IDENTIFIED BY 'secret';
GRANT SHOW DATABASES ON *.* TO 'userGsb'@'localhost';
GRANT ALL PRIVILEGES ON `gsb_frais`.* TO userGsb@localhost;

insert into etat(id, libelle)
values('MP', 'Mise en paiement');

Update etat 
set libelle = 'Validée'
where id='VA';

alter table lignefraishorsforfait
add refuse bool default false;

CREATE TABLE generPDF
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    mois CHAR(6),
    idvisiteur CHAR(5),
    nomPDF CHAR(20),
    FOREIGN KEY (idvisiteur) REFERENCES visiteur(id)
);

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


ALTER TABLE visiteur
ADD COLUMN idTypeVehicule INTEGER NULL;

ALTER TABLE visiteur
ADD CONSTRAINT fk_visiteur_idTypeVehicule
FOREIGN KEY (idTypeVehicule) REFERENCES typeVehicule(id);

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
