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
            <th><input type="checkbox" name="formWheelchair" value="Yes" /></th>
    </table>
</div>