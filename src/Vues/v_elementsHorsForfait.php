<hr>
<div class="row">
    <div class="panel panel-info">
        <div class="panel-heading">Descriptif des éléments hors forfait</div>
        <table class="table table-bordered table-responsive">
            <thead>
                <tr>
                    <th class="date">Date</th>
                    <th class="libelle">Libellé</th>  
                    <th class="montant">Montant</th>  
                    <th class="action">&nbsp;</th> 
                </tr>
            </thead>  
            <tbody>
            <form method="post" 
              action="index.php?uc=validerfrais&action=majHorsFraisForfait" 
              role="form">
            <?php
            foreach ($lesFraisHorsForfait as $unFraisHorsForfait) {
                $libelle = htmlspecialchars($unFraisHorsForfait['libelle']);
                $date = $unFraisHorsForfait['date'];
                $montant = $unFraisHorsForfait['montant'];
                $id = $unFraisHorsForfait['id']; ?>           
                <tr>
                    <td><input type="text" id="date" 
                               name="date"
                               size="10" 
                               value="<?php echo $date ?>" 
                               class="form-control">
                    </td>
                    <td><input type="text" id="libelle" 
                               name="libelle"
                               size="10"
                               value="<?php echo $libelle ?>" 
                               class="form-control">
                    </td>
                    <td><input type="text" id="montant" 
                               name="montant"
                               size="10"
                               value="<?php echo $montant ?>" 
                               class="form-control">
                    </td>
                    <td>
                        <button class="btn btn-success" type="submit">Corriger</button>
                        <button class="btn btn-danger" type="reset">Réinitialiser</button>
                    </td>
                </tr>
                <?php
            }
            ?>
            </form>
            </tbody>  
        </table>
    </div>
    <form method="post" 
              action="index.php?uc=validerfrais&action=majNbJustificatifs" 
              role="form">
    <div class="form-group">
                        <label for="nbJustificatifs">Nombre de justificatifs : </label>
                        <input type="text" id="nbJustificatifs" 
                               name="nbJustificatif"
                               size ="5cm"
                               value="<?php echo $nbJustificatifs ?>" 
                               class="form-control">
                    </div>
        <button class="btn btn-success" type="submit">Valider</button>
        <button class="btn btn-danger" type="reset">Réinitialiser</button>
    </form>
</div>