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

/* on supprime la ligne km de FraisForfait */

ALTER TABLE lignefraisforfait ADD COLUMN vehicule INT Default 3;
ALTER TABLE lignefraisforfait ADD COLUMN kmParcourus INT Default 326;

ALTER TABLE lignefraisforfait DROP COLUMN vehicule ;
ALTER TABLE lignefraisforfait DROP COLUMN kmParcourus ;


delete from fraisforfait where id='KM';

commit;