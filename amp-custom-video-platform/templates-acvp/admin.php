<?php

if (array_key_exists('submit_client_id',$_POST)) {
    update_option('client_id_value', $_POST['client_id_value'] );
    ?>
    <div id="setting-error_settings-updated" class="updated settings-error notice is-dismissible">
    <strong>Client ID aggiornato con successo</strong>
    </div>
    <?php
}

$hashNumber = get_option('client_id_value', 'Inserisci il client ID di riferimento per questo sito');
    
?>
   <div class="acvp_admin_area">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h3>TagVideo.Eu - Impostazioni</h3>
                <form action="" method="post">
                <label for="client_id">Client ID</label>
                <input type="text" size="50" name="client_id_value" value="<?php echo $hashNumber;?>" placeholder="Inserisci il client ID di riferimento per questo sito">
                <input type="submit" name="submit_client_id" value="Aggiorna ClientID" class="button button-primary">
                </form>
            </div>
        </div>
    </div>
    </div>
