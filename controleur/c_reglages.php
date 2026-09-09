<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?uc=connexion&action=demandeConnexion");
    exit();
}

$action = $_REQUEST['action'] ?? 'afficherReglages';
$pdo = PdoLymphTrack::getPdoLymphTrack();

// Vérification du statut Admin pour les actions restreintes
$isAdmin = in_array(strtoupper($_SESSION['user_type'] ?? ''), ['ADMIN', 'SUPER_ADMIN']);

switch ($action) {
    case 'afficherReglages':
        $user = $pdo->getUtilisateurById($_SESSION['user_id']);
        if (empty($user['institution'])) $user['institution'] = 'Uppsala University';
        if (empty($user['role'])) $user['role'] = ($user['user_type'] === 'ADMIN') ? 'Administrator' : 'Intern';

        include("vue/v_entete.php");
        include("vue/v_reglages.php");
        include("vue/v_pied.php");
        break;

    case 'gererUtilisateurs':
        if (!$isAdmin) {
            header("Location: index.php?uc=reglages&action=afficherReglages");
            exit();
        }

        $lesUtilisateurs = $pdo->getTousLesUtilisateurs();

        include("vue/v_entete.php");
        include("vue/v_gestionUtilisateurs.php");
        include("vue/v_pied.php");
        break;

    case 'formulaireUtilisateur':
        if (!$isAdmin) {
            header("Location: index.php?uc=reglages&action=afficherReglages");
            exit();
        }

        $id = $_GET['id'] ?? '';
        $userAEditer = null;
        $isEdit = false;

        if (!empty($id)) {
            $userAEditer = $pdo->getUtilisateurById($id);
            if ($userAEditer) {
                $isEdit = true;
            }
        }

        include("vue/v_entete.php");
        include("vue/v_creerModifierUtilisateur.php");
        include("vue/v_pied.php");
        break;

    case 'validerUtilisateur':
        if (!$isAdmin) {
            header("Location: index.php?uc=reglages&action=afficherReglages");
            exit();
        }

        $id = $_POST['id'] ?? '';
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? ''; // Présent dans le formulaire pour respecter la maquette
        $name = trim($_POST['name'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $institution = trim($_POST['institution'] ?? '');
        $user_type = $_POST['user_type'] ?? 'USER';

        if (empty($email)) {
            $erreur = "Email is required.";
            $isEdit = !empty($id);
            $userAEditer = [
                'id' => $id, 'email' => $email, 'name' => $name, 
                'role' => $role, 'institution' => $institution, 'user_type' => $user_type
            ];
            include("vue/v_entete.php");
            include("vue/v_creerModifierUtilisateur.php");
            include("vue/v_pied.php");
            break;
        }

        if (!empty($id)) {
            $pdo->modifierUtilisateur($id, $name, $email, $role, $user_type, $institution);
        } else {
            $pdo->creerUtilisateur($name, $email, $role, $user_type, $institution);
        }

        header("Location: index.php?uc=reglages&action=gererUtilisateurs");
        exit();
        break;

    case 'supprimerUtilisateur':
        if (!$isAdmin) {
            header("Location: index.php?uc=reglages&action=afficherReglages");
            exit();
        }

        $idASupprimer = $_GET['id'] ?? '';
        // Sécurité auto-protection : on ne peut pas se supprimer soi-même
        if (!empty($idASupprimer) && $idASupprimer != $_SESSION['user_id']) {
            $pdo->supprimerUtilisateur($idASupprimer);
        }

        header("Location: index.php?uc=reglages&action=gererUtilisateurs");
        exit();
        break;
    
    case 'formulaireModifierMonCompte':
        $user = $pdo->getUtilisateurById($_SESSION['user_id']);
        if (empty($user['institution'])) $user['institution'] = 'Uppsala University';
        if (empty($user['role'])) $user['role'] = ($user['user_type'] === 'ADMIN') ? 'Administrator' : 'Intern';

        include("vue/v_entete.php");
        include("vue/v_modifierCompte.php");
        include("vue/v_pied.php");
        break;

    case 'validerModifierMonCompte':
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $institution = trim($_POST['institution'] ?? '');

        if (!empty($email)) {
            $currentUser = $pdo->getUtilisateurById($_SESSION['user_id']);
            // Conserve son propre niveau d'accès actuel
            $pdo->modifierUtilisateur($_SESSION['user_id'], $name, $email, $role, $currentUser['user_type'], $institution);
            
            // Mise à jour du nom en session si modifié
            $_SESSION['user_name'] = $name;

            header("Location: index.php?uc=reglages&action=afficherReglages");
            exit();
        } else {
            $erreur = "Email address cannot be empty.";
            $user = [
                'name' => $name,
                'email' => $email,
                'role' => $role,
                'institution' => $institution
            ];
            include("vue/v_entete.php");
            include("vue/v_modifierCompte.php");
            include("vue/v_pied.php");
        }
        break;
}
?>