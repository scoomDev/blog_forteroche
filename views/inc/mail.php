<?php
$header = "MIME-Version: 1.0\r\n";
$header .= 'From:"Billet simple pour l\'Alaska"<support@bloge-forteroche.com>'."\n";
$header .= 'Content-Type:text/html; charset="utf8"'."\n";
$header .= 'Content-Transfert-Encoding: 8bit';

$message = '
    <html>
        <title>Récupération de mot de passe</title>
        <meta charset="utf-8">
        <body>
            <h3>Récupération de mot de passe</h3>
            <p>Bonjour '. $pseudo .'</p>
            <p>Voici le code de récupération : <strong>' . $recovery_code .'</strong></p>
        </body>
    </html>
';
