<?php
$action = $_REQUEST['action'] ?? 'demandeConnexion';

switch ($action) {
    case 'demandeConnexion':
        include("vue/v_bandeau.php");
        break;

    case 'validerConnexion':
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $pdo = PdoLymphTrack::getPdoLymphTrack();
        $user = $pdo->verifierUtilisateur($email);

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['user_name'] = $user['name'];

            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        } else {
            $erreur = "Invalid email address or password.";
            include("vue/v_bandeau.php");
        }
        break;

    case 'deconnexion':
        session_destroy();
        header("Location: index.php?uc=connexion&action=demandeConnexion");
        exit();
}
?>