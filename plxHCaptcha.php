<?php

/**
 * Classe plxHCaptcha
 *
 * */
class plxHCaptcha extends plxPlugin {

    /**
     * Constructeur de la classe
     *
     * @return	null
     * @author	Stéphane F. Abdellah B
     * */
    private $json;

    public function __construct($default_lang) {


        # Appel du constructeur de la classe plxPlugin (obligatoire)
        parent::__construct($default_lang);

        # droits pour accéder à la page config.php du plugin
        $this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);

        # Ajouts des hooks
        $this->addHook('plxShowCapchaQ', 'plxShowCapchaQ');
        $this->addHook('plxShowCapchaR', 'plxShowCapchaR');
        $this->addHook('plxMotorNewCommentaire', 'plxMotorNewCommentaire');
        $this->addHook('ThemeEndHead', 'ThemeEndHead');

        $_SESSION['capcha'] = '';
        if (isset($_POST['h-captcha-response'])) { // Ce code est mis ici pour que ça fonctionne dans la page de contact
            $this->json = $this->hCaptchaResponse();
            $_SESSION['capcha'] = ($this->json['success'] ? '' : sha1(uniqid(rand(), true)));
        }
    }

    private function hCaptchaResponse() {
        $ch = curl_init("https://hcaptcha.com/siteverify");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('secret' => $this->getParam('Secretkey'), 'response' => $_POST['h-captcha-response']));
        $json = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $json;
    }

    /**
     * Méthode qui affiche le capcha
     *
     * @return	stdio
     * @author	Stéphane F. Abdellah B
     * */
    public function plxShowCapchaQ() {
        $_SESSION['capcha_token'] = sha1(uniqid(rand(), true));
        echo "<script type=\"text/javascript\">document.addEventListener(\"DOMContentLoaded\", function() {document.getElementById('id_rep').type = 'hidden';});</script>\n"; // on cache le champ réponse du thème
        echo '<div class="h-captcha" data-sitekey="' . $this->getParam('Sitekey') . '"></div>'; // élément hCaptcha
        echo '<input type="hidden" name="capcha_token" value="' . $_SESSION['capcha_token'] . '" />';
        echo '<?php return true; ?>'; # pour interrompre la fonction CapchaQ de plxShow
    }

    /**
     * Méthode qui encode le capcha en sha1 pour comparaison
     *
     * @return	stdio
     * @author	Stéphane F. Abdellah B
     * */
    public function plxMotorNewCommentaire() { // La méthode se lance quand le commentaire est posté
        if (isset($_POST['h-captcha-response'])) {
            if ($this->json['success']) {
                $plxMotor = plxMotor::getInstance();
                $_SESSION['capcha'] = ($plxMotor->mode == 'article' ? sha1('') : sha1(uniqid(rand(), true)));
            } else {
                $_SESSION['capcha'] = sha1(uniqid(rand(), true));
            }
        }
    }

    /**
     * Méthode qui retourne la réponse du capcha // obsolète
     *
     * @return	stdio
     * @author	Stéphane F.
     * */
    public function plxShowCapchaR() {
        echo '<?php return true; ?>';  # pour interrompre la fonction CapchaR de plxShow
    }

    /**
     * Méthode qui integre le script de chez Hcaptcha en <head> et </head>
     *
     * @return	stdio
     * @author	Stéphane F. Abdellah B
     * */
    public function ThemeEndHead() {
        echo "\n\t<script src=\"https://hcaptcha.com/1/api.js\" async defer></script>\n";
    }

}
?>
