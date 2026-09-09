<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?uc=connexion&action=demandeConnexion");
    exit();
}

$action = $_REQUEST['action'] ?? 'afficherFiltres';
$pdo = PdoLymphTrack::getPdoLymphTrack();

switch ($action) {
    case 'afficherFiltres':
        $lesPatients = $pdo->getPatients();

        include("vue/v_entete.php");
        include("vue/v_outcomesFiltres.php");
        include("vue/v_pied.php");
        break;

    case 'afficherResultats':
        $selectedPatients = $_POST['patients'] ?? [];
        $position = $_POST['position'] ?? 'All';
        $visit = $_POST['visit'] ?? 'All';

        if (empty($selectedPatients)) {
            header("Location: index.php?uc=outcomes&action=afficherFiltres");
            exit();
        }

        $metriquesComparaison = $pdo->getMetriquesComparaisonMultiPatients($selectedPatients);

        include("vue/v_entete.php");
        include("vue/v_outcomesResultats.php");
        include("vue/v_pied.php");
        break;
}
?>