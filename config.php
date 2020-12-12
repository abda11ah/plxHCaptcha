<?php
/**
 * Plugin Hcaptcha
 *
 * @package     Hcaptcha
 * @version     1.0
 * @date        12/12/2020
 * @author      Abdellah B
 * */
if (!defined('PLX_ROOT'))
    exit;
# Control du token du formulaire
plxToken::validateFormToken($_POST);

if (!empty($_POST)) {
    $plxPlugin->setParam('Sitekey', $_POST['Sitekey'], 'cdata');
    $plxPlugin->setParam('Secretkey', $_POST['Secretkey'], 'cdata');
    $plxPlugin->saveParams();
    header('Location: parametres_plugin.php?p=plxHCaptcha');
    exit;
}
?>

<h2><?php $plxPlugin->lang('L_TITLE') ?></h2>
<p><?php $plxPlugin->lang('L_CONFIG_DESCRIPTION') ?></p>

<form action="parametres_plugin.php?p=plxHCaptcha" method="post">
    <fieldset class="withlabel">
        <p><?php echo $plxPlugin->getLang('L_CONFIG_SITE_KEY') ?></p>
        <?php plxUtils::printInput('Sitekey', plxUtils::strCheck($plxPlugin->getParam('Sitekey')), 'text'); ?>
        <p><?php echo $plxPlugin->getLang('L_CONFIG_SECRET_KEY') ?></p>
        <?php plxUtils::printInput('Secretkey', plxUtils::strCheck($plxPlugin->getParam('Secretkey')), 'text'); ?>

    </fieldset>
    <br />
    <?php echo plxToken::getTokenPostMethod() ?>
    <input type="submit" name="submit" value="<?php echo $plxPlugin->getLang('L_CONFIG_SAVE') ?>" />
</form>