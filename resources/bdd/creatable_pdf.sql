CREATE TABLE generPDF
(
    id INT AUTO_INCREMENT PRIMARY KEY,
    mois CHAR(6),
    idvisiteur CHAR(5),
    nomPDF CHAR(20),
    dateGeneration DATETIME,
    FOREIGN KEY (idvisiteur) REFERENCES visiteur(id)
);

Drop table generPDF;