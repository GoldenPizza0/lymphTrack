<?php
session_start();
require_once("modele/class.pdoLymphTrack.inc.php");

$uc = $_REQUEST['uc'] ?? 'connexion';

switch ($uc) {
    case 'connexion':
        include("controleur/c_connexion.php");
        break;

    case 'gererPatients':
        include("controleur/c_gererPatients.php");
        break;

    case 'gererFollowup':
        include("controleur/c_gererFollowup.php");
        break;

	case 'gererMesures':
        include("controleur/c_gererMesures.php");
        break;
	
	case 'reglages':
        include("controleur/c_reglages.php");
        break;

	case 'outcomes':
        include("controleur/c_outcomes.php");
        break;

    default:
        include("controleur/c_connexion.php");
        break;
}
?>