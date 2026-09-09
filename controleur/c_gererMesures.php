<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?uc=connexion&action=demandeConnexion");
    exit();
}

$action = $_REQUEST['action'] ?? 'voirPosition';
$pdo = PdoLymphTrack::getPdoLymphTrack();

switch ($action) {
    case 'voirPosition':
        $operation_id = $_GET['op_id'] ?? '';
        $position = isset($_GET['pos']) ? intval($_GET['pos']) : 1;

        if (empty($operation_id)) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }

        $operation = $pdo->getOperationById($operation_id);
        if (!$operation) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }

        $lesMesures = $pdo->getMesuresParPosition($operation_id, $position);

        include("vue/v_entete.php");
        include("vue/v_position.php");
        include("vue/v_pied.php");
        break;

    case 'supprimerMesure':
        $result_id = $_GET['result_id'] ?? '';
        $operation_id = $_GET['op_id'] ?? '';
        $position = $_GET['pos'] ?? 1;

        if (!empty($result_id) && !empty($operation_id)) {
            $pdo->supprimerMesureEtRenuméroter($result_id, $operation_id, $position);
        }

        header("Location: index.php?uc=gererMesures&action=voirPosition&op_id=" . urlencode($operation_id) . "&pos=" . urlencode($position));
        exit();

    case 'ajouterMesureSimulation':
        $operation_id = $_POST['op_id'] ?? '';
        $position = $_POST['pos'] ?? 1;
        $created_by = $_SESSION['user_id'];

        // Valeurs représentatives par défaut d'une prise de mesure micro-onde
        $min_s11 = -9.85 - (mt_rand(0, 80) / 100); // ~ -10 dB
        $freq_hz = 2450000000 + mt_rand(-10000000, 10000000);
        $bw_hz = 85000000 + mt_rand(-2000000, 2000000);

        $pdo->ajouterMesure($operation_id, $position, $min_s11, $freq_hz, $bw_hz, $created_by);

        header("Location: index.php?uc=gererMesures&action=voirPosition&op_id=" . urlencode($operation_id) . "&pos=" . urlencode($position));
        exit();
    
        case 'importerToutesMesures':
        $operation_id = $_GET['op_id'] ?? '';
        $created_by = $_SESSION['user_id'];

        if (!empty($operation_id)) {
            $pdo->importerToutesMesuresAutomatique($operation_id, $created_by);
        }

        // Une fois importé, on revient afficher la visite (gérée par c_gererFollowup)
        header("Location: index.php?uc=gererFollowup&action=voirVisite&op_id=" . urlencode($operation_id));
        exit();
        break;

    case 'exporterPositionZip':
        $operation_id = $_GET['op_id'] ?? '';
        $position = $_GET['pos'] ?? 1;

        $operation = $pdo->getOperationById($operation_id);
        $lesMesures = $pdo->getMesuresParPosition($operation_id, $position);

        $zip = new ZipArchive();
        $nomZip = 'Position_' . $position . '_Op' . $operation_id . '_' . date('Ymd_His') . '.zip';
        $cheminZip = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nomZip;

        if ($zip->open($cheminZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $csv = "Measurement;Return Loss (dB);Frequency (Hz);Bandwidth (Hz)\n";
            foreach ($lesMesures as $m) {
                $csv .= sprintf(
                    "Measurement %d;%s;%s;%s\n",
                    $m['measurement_number'],
                    $m['min_return_loss_db'],
                    $m['min_frequency_hz'],
                    $m['bandwidth_hz']
                );
            }
            $zip->addFromString("Position_" . $position . "_measurements.csv", $csv);
            $zip->close();

            if (ob_get_level()) { ob_end_clean(); }
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($nomZip) . '"');
            header('Content-Length: ' . filesize($cheminZip));

            readfile($cheminZip);
            unlink($cheminZip);
            exit();
        }
        break;
}
?>