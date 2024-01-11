create table indemniKm
(
	id integer AUTO_INCREMENT primary key,
	vehicule char(60),
    tarifKm DECIMAL(8, 2)
);

insert into indemniKm(id, vehicule, tarifKm) values
(1,'Véhicule  4CV Diesel', 0.52),
(2,'Véhicule 5/6CV Diesel', 0.58),
(3,'Véhicule  4CV Essence', 0.62),
(4,'Véhicule 5/6CV Essence', 0.67);

/* Maintenant que la table des indemnisation kilométriques est créee il faut 
l'utiliser en tant que clé etrangere dans frais forfait */

drop table indemnKm;

commit;