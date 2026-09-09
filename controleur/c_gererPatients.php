<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?uc=connexion&action=demandeConnexion");
    exit();
}

$action = $_REQUEST['action'] ?? 'afficherPatients';
$pdo = PdoLymphTrack::getPdoLymphTrack();

switch ($action) {
    case 'afficherPatients':
        $search = $_GET['search'] ?? '';
        $side = $_GET['side'] ?? 'All';
        $gender = $_GET['gender'] ?? 'All';

        $lesPatients = $pdo->getPatients($search, $side, $gender);
        $nbPatients = count($lesPatients);

        include("vue/v_entete.php");
        include("vue/v_accueil.php");
        include("vue/v_pied.php");
        break;

    case 'voirProfilPatient':
        $patient_id = $_GET['id'] ?? '';
        if (empty($patient_id)) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }

        $patient = $pdo->getPatientById($patient_id);
        if (!$patient) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }

        $visites = $pdo->getVisitesParPatient($patient_id);
        // Récupère toutes les positions d'un coup
        $toutesMetriques = $pdo->getToutesMetriquesPatient($patient_id);

        include("vue/v_entete.php");
        include("vue/v_profilPatient.php");
        include("vue/v_pied.php");
        break;

    case 'formulaireAjoutPatient':
        include("vue/v_entete.php");
        include("vue/v_creerPatient.php");
        include("vue/v_pied.php");
        break;

    case 'validerAjoutPatient':
        $noId = isset($_POST['no_id']);
        $customId = trim($_POST['patient_id'] ?? '');
        $patient_id = ($noId || empty($customId)) ? $pdo->genererProchainPatientId() : strtoupper($customId);

        $noAge = isset($_POST['no_age']);
        $age = ($noAge || empty($_POST['age'])) ? null : intval($_POST['age']);

        $noBmi = isset($_POST['no_bmi']);
        $bmi = ($noBmi || empty($_POST['bmi'])) ? null : floatval($_POST['bmi']);

        $gender = $_POST['gender'] ?? 'UNKNOWN';
        $side = $_POST['lymphedema_side'] ?? 'UNKNOWN';
        $notes = !empty($_POST['notes']) ? trim($_POST['notes']) : null;
        $created_by = $_SESSION['user_id'];

        try {
            $pdo->ajouterPatient($patient_id, $age, $gender, $side, $bmi, $notes, $created_by);
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        } catch (Exception $e) {
            $erreur = "Error creating patient (ID might already exist).";
            include("vue/v_entete.php");
            include("vue/v_creerPatient.php");
            include("vue/v_pied.php");
        }
        break;

    case 'supprimerPatient':
        $id = $_GET['id'] ?? '';
        if (!empty($id)) {
            $pdo->supprimerPatient($id);
        }
        header("Location: index.php?uc=gererPatients&action=afficherPatients");
        exit();

    case 'exporterPatients':
        $idsSelectionnes = $_POST['patients_ids'] ?? [];
        if (empty($idsSelectionnes)) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }

        $donnees = $pdo->getExportDataPourPatients($idsSelectionnes);
        $zip = new ZipArchive();
        $nomZip = 'LymphTrack_Export_' . date('Ymd_His') . '.zip';
        $cheminZip = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nomZip;

        if ($zip->open($cheminZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $patientsGroupes = [];
            foreach ($donnees as $ligne) {
                $patientsGroupes[$ligne['patient_id']][] = $ligne;
            }

            foreach ($patientsGroupes as $pId => $lignes) {
                $csv = "Patient ID;Age;Gender;Side;BMI;Visit Name;Date;Position;Measurement #;Min S11 (dB);Resonance Freq (Hz);Bandwidth (Hz)\n";
                foreach ($lignes as $l) {
                    $csv .= sprintf(
                        "%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s;%s\n",
                        $l['patient_id'],
                        $l['age'] ?? '',
                        $l['gender'] ?? '',
                        $l['lymphedema_side'] ?? '',
                        $l['bmi'] ?? '',
                        $l['visit_name'] ?? '',
                        $l['operation_date'] ?? '',
                        $l['position'] ?? '',
                        $l['measurement_number'] ?? '',
                        $l['min_return_loss_db'] ?? '',
                        $l['min_frequency_hz'] ?? '',
                        $l['bandwidth_hz'] ?? ''
                    );
                }
                $zip->addFromString($pId . "/measurements_summary.csv", $csv);
            }

            $zip->close();

            if (ob_get_level()) { ob_end_clean(); }
            header('Pragma: public');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Cache-Control: private', false);
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($nomZip) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . filesize($cheminZip));

            readfile($cheminZip);
            unlink($cheminZip);
            exit();
        }
        break;
}
?>