<!-- RAJOUTER UNE CHECK BOX DE VALIDATION POUR LA MISE EN PAIEMENT -->

<hr>
<div class="panel panel-info">
    <div class="panel-heading">Mise en Paiement - *NOM VISITEUR ICI*</div>
    <table class="table table-bordered table-responsive">
        <tr>
            <th>Montant Frais Forfait</th>
            <th>Montant Frais Hors Forfait</th>
        </tr>
        <tr>
            <th><?php echo $montantValide?>€</th>
            <th><?php echo $montantHorsForfait?>€</th>
        </tr>
    </table>
</div>

<style>
    th{
        background-color: white;
    }    
</style>