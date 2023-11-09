use gsb_frais;

commit;

alter table visiteur
add iscomptable boolean default false not null;

select iscomptable from visiteur;

update visiteur 
set iscomptable = true
where id = 'a118y';

CREATE USER 'createurGsb'@'localhost' IDENTIFIED BY 'secret';
GRANT create, references, drop, alter ON *.* TO 'createurGsb'@'localhost';

CREATE USER 'userGsb'@'localhost' IDENTIFIED BY 'secret';
GRANT select, insert, update, delete ON gsb_frais.* TO 'userGsb'@'localhost';

CREATE USER IF NOT EXISTS 'userGsb'@'localhost' IDENTIFIED BY 'secret';
GRANT SHOW DATABASES ON *.* TO 'userGsb'@'localhost';
GRANT ALL PRIVILEGES ON `gsb_frais`.* TO userGsb@localhost;