<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php?uc=connexion&action=demandeConnexion");
    exit();
}

$action = $_REQUEST['action'] ?? 'formulaireAjout';
$pdo = PdoLymphTrack::getPdoLymphTrack();

switch ($action) {
    case 'formulaireAjout':
        $patient_id = $_GET['patient_id'] ?? '';
        if (empty($patient_id)) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }
        include("vue/v_entete.php");
        include("vue/v_creerFollowup.php");
        include("vue/v_pied.php");
        break;

    case 'validerAjout':
        $patient_id = $_POST['patient_id'] ?? '';
        $name = trim($_POST['name'] ?? '');
        $date = $_POST['date'] ?? date('Y-m-d');
        $notes = trim($_POST['notes'] ?? '');
        $created_by = $_SESSION['user_id'];

        if (!empty($patient_id) && !empty($name)) {
            $operation_id = $pdo->creerOperation($patient_id, $name, $date, $notes, $created_by);

            // Gestion upload photos
            $uploadDir = 'uploads/photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            for ($i = 1; $i <= 3; $i++) {
                if (isset($_FILES["photo_$i"]) && $_FILES["photo_$i"]['error'] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES["photo_$i"]['tmp_name'];
                    $ext = pathinfo($_FILES["photo_$i"]['name'], PATHINFO_EXTENSION);
                    $fileName = $patient_id . '_' . $operation_id . '_photo' . $i . '_' . time() . '.' . $ext;
                    $dest = $uploadDir . $fileName;

                    if (move_uploaded_file($tmpName, $dest)) {
                        $pdo->ajouterPhotoOperation($operation_id, $fileName, $created_by);
                    }
                }
            }

            // Après création, on bascule vers la page de la visite
            header("Location: index.php?uc=gererFollowup&action=voirVisite&op_id=" . urlencode($operation_id));
            exit();
        } else {
            $erreur = "Please provide a valid follow-up name and date.";
            include("vue/v_entete.php");
            include("vue/v_creerFollowup.php");
            include("vue/v_pied.php");
        }
        break;

    case 'voirVisite':
        $operation_id = $_GET['op_id'] ?? '';
        if (empty($operation_id)) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }

        $operation = $pdo->getOperationById($operation_id);
        if (!$operation) {
            header("Location: index.php?uc=gererPatients&action=afficherPatients");
            exit();
        }

        $photos = $pdo->getPhotosParOperation($operation_id);
        $nbPhotos = count($photos);
        $mesures = $pdo->getMesuresVisite($operation_id);

        include("vue/v_entete.php");
        include("vue/v_visite.php");
        include("vue/v_pied.php");
        break;

    case 'exporterPhotosVisite':
        $operation_id = $_GET['op_id'] ?? '';
        $photos = $pdo->getPhotosParOperation($operation_id);

        if (empty($photos)) {
            header("Location: index.php?uc=gererFollowup&action=voirVisite&op_id=" . urlencode($operation_id));
            exit();
        }

        $zip = new ZipArchive();
        $nomZip = 'Photos_Operation_' . $operation_id . '_' . date('Ymd_His') . '.zip';
        $cheminZip = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $nomZip;

        if ($zip->open($cheminZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $dossierPhotos = 'uploads/photos/';
            foreach ($photos as $p) {
                $cheminFichier = $dossierPhotos . $p['filename'];
                if (file_exists($cheminFichier)) {
                    $zip->addFile($cheminFichier, $p['filename']);
                }
            }
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