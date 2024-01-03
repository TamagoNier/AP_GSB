use gsb_frais;
select * from etat;

insert into etat(id, libelle)
values('MP', 'Mise en paiement');

Update etat 
set libelle = 'Validée'
where id='VA';