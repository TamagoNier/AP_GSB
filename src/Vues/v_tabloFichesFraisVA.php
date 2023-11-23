<!-- RAJOUTER UNE CHECK BOX DE VALIDATION POUR LA MISE EN PAIEMENT -->

<hr>
<div class="panel panel-info">
    <div class="panel-heading">Mise en Paiement - <?php echo $nomVisiteur[0]. ' ' . $nomVisiteur[1]?></div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th>Montant Frais Forfait</th>
            <th>Montant Frais Hors Forfait</th>
            <th>Mettre en paiement</th>
        </tr>
        <tr>
            <th><?php echo $montantValide?>€</th>
            <th><?php echo $montantHorsForfait?>€</th>
            <th>
                <form action="index.php?uc=suivipaiement&action=miseEnPaiement" method="post" role="form">  
                    <!-- données envoyées cachées -->
                <input type="hidden" name="idVisiteur" value="<?php echo $leVisiteur ?>">
                <input type="hidden" name="mois" value="<?php echo $leMois; ?>">
                
                <input id="ok" type="submit" value="Mettre en Paiement" class="btn btn-success" 
                   role="button">
                </form>
            </th>
    </table>
</div>