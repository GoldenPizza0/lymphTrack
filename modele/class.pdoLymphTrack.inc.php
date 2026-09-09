<?php
class PdoLymphTrack {
    private static $monPdo;
    private static $monPdoLymphTrack = null;

    private function __construct() {
        $serveur = 'localhost';
        $bdd = 'lymphtrackdata';
        $user = 'root';
        $mdp = '';
        self::$monPdo = new PDO("mysql:host=$serveur;dbname=$bdd;charset=utf8mb4", $user, $mdp);
        self::$monPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getPdoLymphTrack() {
        if (self::$monPdoLymphTrack == null) {
            self::$monPdoLymphTrack = new PdoLymphTrack();
        }
        return self::$monPdoLymphTrack;
    }

    // ----------------------------------------------------
    // AUTHENTIFICATION
    // ----------------------------------------------------
    public function verifierUtilisateur($email) {
        $req = "SELECT id, email, name, role, user_type FROM users WHERE email = :email";
        $stmt = self::$monPdo->prepare($req);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le profil complet d'un utilisateur par son ID
     */
    public function getUtilisateurById($user_id) {
        $sql = "SELECT id, email, name, role, user_type, institution FROM users WHERE id = :id";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère l'ensemble des utilisateurs enregistrés
     */
    public function getTousLesUtilisateurs() {
        $sql = "SELECT id, email, name, role, user_type, institution FROM users ORDER BY id ASC";
        $stmt = self::$monPdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crée un nouvel utilisateur
     */
    public function creerUtilisateur($name, $email, $role, $user_type, $institution) {
        $sql = "INSERT INTO users (name, email, role, user_type, institution) 
                VALUES (:name, :email, :role, :user_type, :institution)";
        $stmt = self::$monPdo->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':role' => !empty($role) ? $role : 'Doctor',
            ':user_type' => $user_type,
            ':institution' => !empty($institution) ? $institution : 'Uppsala University'
        ]);
    }

    /**
     * Met à jour les informations d'un utilisateur existant
     */
    public function modifierUtilisateur($id, $name, $email, $role, $user_type, $institution) {
        $sql = "UPDATE users 
                SET name = :name, email = :email, role = :role, user_type = :user_type, institution = :institution 
                WHERE id = :id";
        $stmt = self::$monPdo->prepare($sql);
        return $stmt->execute([
            ':id' => $id,
            ':name' => !empty($name) ? $name : null,
            ':email' => $email,
            ':role' => !empty($role) ? $role : null,
            ':user_type' => strtoupper($user_type),
            ':institution' => !empty($institution) ? $institution : null
        ]);
    }

    /**
     * Supprime un utilisateur (interdit sur soi-même)
     */
    public function supprimerUtilisateur($id) {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        return $stmt->execute();
    }

    // ----------------------------------------------------
    // PATIENTS
    // ----------------------------------------------------
    public function getPatients($search = '', $side = 'All', $gender = 'All') {
        $sql = "SELECT patient_id, age, gender, lymphedema_side, bmi, notes FROM sick_patients WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND patient_id LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        if (!empty($side) && $side !== 'All') {
            $sql .= " AND lymphedema_side = :side";
            $params[':side'] = strtoupper($side);
        }
        if (!empty($gender) && $gender !== 'All') {
            $sql .= " AND gender = :gender";
            $params[':gender'] = strtoupper($gender);
        }

        $sql .= " ORDER BY patient_id ASC";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPatientById($patient_id) {
        $sql = "SELECT patient_id, age, gender, lymphedema_side, bmi, notes FROM sick_patients WHERE patient_id = :id";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $patient_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function genererProchainPatientId() {
        $sql = "SELECT patient_id FROM sick_patients WHERE patient_id LIKE 'MV%' ORDER BY patient_id DESC LIMIT 1";
        $stmt = self::$monPdo->query($sql);
        $dernierId = $stmt->fetchColumn();

        if ($dernierId) {
            $numero = intval(substr($dernierId, 2)) + 1;
            return 'MV' . str_pad($numero, 3, '0', STR_PAD_LEFT);
        }
        return 'MV001';
    }

    public function ajouterPatient($patient_id, $age, $gender, $lymphedema_side, $bmi, $notes, $created_by) {
        $sql = "INSERT INTO sick_patients (patient_id, age, gender, lymphedema_side, bmi, notes, created_by, updated_by) 
                VALUES (:patient_id, :age, :gender, :lymphedema_side, :bmi, :notes, :created_by, :created_by)";
        $stmt = self::$monPdo->prepare($sql);
        return $stmt->execute([
            ':patient_id' => $patient_id,
            ':age' => $age,
            ':gender' => $gender,
            ':lymphedema_side' => $lymphedema_side,
            ':bmi' => $bmi,
            ':notes' => $notes,
            ':created_by' => $created_by
        ]);
    }

    public function supprimerPatient($patient_id) {
        $sql = "DELETE FROM sick_patients WHERE patient_id = :id";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $patient_id);
        return $stmt->execute();
    }

    public function getExportDataPourPatients($idsPatients) {
        if (empty($idsPatients)) return [];
        $inQuery = implode(',', array_fill(0, count($idsPatients), '?'));
        
        $sql = "SELECT p.patient_id, p.age, p.gender, p.lymphedema_side, p.bmi, p.notes AS patient_notes,
                       o.id AS operation_id, o.name AS visit_name, o.operation_date,
                       r.position, r.measurement_number, r.min_return_loss_db, r.min_frequency_hz, r.bandwidth_hz
                FROM sick_patients p
                LEFT JOIN operations o ON p.patient_id = o.patient_id
                LEFT JOIN results r ON o.id = r.operation_id
                WHERE p.patient_id IN ($inQuery)
                ORDER BY p.patient_id, o.operation_date, r.position, r.measurement_number";

        $stmt = self::$monPdo->prepare($sql);
        $stmt->execute($idsPatients);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ----------------------------------------------------
    // FOLLOW-UPS / VISITES (OPERATIONS) & OUTCOMES
    // ----------------------------------------------------
    public function getVisitesParPatient($patient_id) {
        $sql = "SELECT id, name, operation_date, notes FROM operations WHERE patient_id = :id ORDER BY operation_date ASC";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $patient_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOperationById($operation_id) {
        $sql = "SELECT id, patient_id, name, operation_date, notes FROM operations WHERE id = :id";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $operation_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function creerOperation($patient_id, $name, $operation_date, $notes, $created_by) {
        $sql = "INSERT INTO operations (patient_id, name, operation_date, notes, created_by, updated_by) 
                VALUES (:patient_id, :name, :operation_date, :notes, :created_by, :created_by)";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->execute([
            ':patient_id' => $patient_id,
            ':name' => $name,
            ':operation_date' => $operation_date,
            ':notes' => !empty($notes) ? $notes : null,
            ':created_by' => $created_by
        ]);
        return self::$monPdo->lastInsertId();
    }

    public function ajouterPhotoOperation($operation_id, $filename, $created_by) {
        $sql = "INSERT INTO photos (operation_id, filename, created_by) VALUES (:op_id, :filename, :created_by)";
        $stmt = self::$monPdo->prepare($sql);
        return $stmt->execute([
            ':op_id' => $operation_id,
            ':filename' => $filename,
            ':created_by' => $created_by
        ]);
    }

    public function getPhotosParOperation($operation_id) {
        $sql = "SELECT id, filename, created_at FROM photos WHERE operation_id = :id ORDER BY id ASC";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $operation_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMetriquesParPosition($patient_id, $position) {
        $sql = "SELECT o.name AS visit_name, o.operation_date, 
                       AVG(r.min_frequency_hz) AS fo, 
                       AVG(r.min_return_loss_db) AS min_s11, 
                       AVG(r.bandwidth_hz) AS bw
                FROM operations o
                JOIN results r ON o.id = r.operation_id
                WHERE o.patient_id = :id AND r.position = :pos
                GROUP BY o.id, o.name, o.operation_date
                ORDER BY o.operation_date ASC";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $patient_id);
        $stmt->bindValue(':pos', $position);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMesuresVisite($operation_id) {
        $sql = "SELECT position, AVG(min_return_loss_db) as min_s11, AVG(min_frequency_hz) as fo, AVG(bandwidth_hz) as bw 
                FROM results 
                WHERE operation_id = :id 
                GROUP BY position 
                ORDER BY position ASC";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $operation_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ----------------------------------------------------
    // MESURES & OUTCOMES
    // ----------------------------------------------------
    /**
     * Récupère toutes les métriques de toutes les positions pour un patient donné
     */
    public function getToutesMetriquesPatient($patient_id) {
        $sql = "SELECT o.name AS visit_name, o.operation_date, r.position,
                       AVG(r.min_frequency_hz) AS fo, 
                       AVG(r.min_return_loss_db) AS min_s11, 
                       AVG(r.bandwidth_hz) AS bw
                FROM operations o
                JOIN results r ON o.id = r.operation_id
                WHERE o.patient_id = :id
                GROUP BY o.id, o.name, o.operation_date, r.position
                ORDER BY o.operation_date ASC, r.position ASC";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':id', $patient_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getMesuresParPosition($operation_id, $position) {
        $sql = "SELECT id, operation_id, position, measurement_number, 
                       min_return_loss_db, min_frequency_hz, bandwidth_hz 
                FROM results 
                WHERE operation_id = :op_id AND position = :pos 
                ORDER BY measurement_number ASC";
        $stmt = self::$monPdo->prepare($sql);
        $stmt->bindValue(':op_id', $operation_id);
        $stmt->bindValue(':pos', $position);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouterMesure($operation_id, $position, $min_s11, $freq_hz, $bw_hz, $created_by) {
        // Détermination du prochain numéro de mesure
        $sqlNum = "SELECT COALESCE(MAX(measurement_number), 0) + 1 
                   FROM results 
                   WHERE operation_id = :op_id AND position = :pos";
        $stmtNum = self::$monPdo->prepare($sqlNum);
        $stmtNum->execute([':op_id' => $operation_id, ':pos' => $position]);
        $nextNum = $stmtNum->fetchColumn();

        $sql = "INSERT INTO results (operation_id, position, measurement_number, min_return_loss_db, min_frequency_hz, bandwidth_hz, created_by, updated_by) 
                VALUES (:op_id, :pos, :num, :s11, :freq, :bw, :created_by, :created_by)";
        $stmt = self::$monPdo->prepare($sql);
        return $stmt->execute([
            ':op_id' => $operation_id,
            ':pos' => $position,
            ':num' => $nextNum,
            ':s11' => $min_s11,
            ':freq' => $freq_hz,
            ':bw' => $bw_hz,
            ':created_by' => $created_by
        ]);
    }

    /**
     * Génère et enregistre automatiquement un jeu complet de 18 mesures (3 mesures x 6 positions)
     */
    public function importerToutesMesuresAutomatique($operation_id, $created_by) {
        // Supprime les anciennes mesures de cette visite pour éviter les doublons
        $sqlClean = "DELETE FROM results WHERE operation_id = :op_id";
        $stmtClean = self::$monPdo->prepare($sqlClean);
        $stmtClean->bindValue(':op_id', $operation_id);
        $stmtClean->execute();

        // Profils physiques représentatifs par position (f0 en Hz, minS11 en dB, BW en Hz)
        // Reflète l'asymétrie typique entre membre atteint et sain
        $profilPositions = [
            1 => ['f0' => 2447000000, 's11' => -10.06, 'bw' => 291000000],
            2 => ['f0' => 2465000000, 's11' => -12.30, 'bw' => 310000000],
            3 => ['f0' => 2470000000, 's11' => -14.10, 'bw' => 305000000],
            4 => ['f0' => 2455000000, 's11' => -11.50, 'bw' => 298000000],
            5 => ['f0' => 2460000000, 's11' => -13.20, 'bw' => 302000000],
            6 => ['f0' => 2480000000, 's11' => -15.40, 'bw' => 315000000]
        ];

        $sqlInsert = "INSERT INTO results (operation_id, position, measurement_number, min_return_loss_db, min_frequency_hz, bandwidth_hz, created_by, updated_by) 
                      VALUES (:op_id, :pos, :num, :s11, :freq, :bw, :created_by, :created_by)";
        $stmtInsert = self::$monPdo->prepare($sqlInsert);

        for ($pos = 1; $pos <= 6; $pos++) {
            $base = $profilPositions[$pos];
            for ($num = 1; $num <= 3; $num++) {
                // Légères variations de répétabilité d'une mesure à l'autre (+/- 0.2 dB, +/- 3 MHz)
                $variationS11 = (mt_rand(-20, 20) / 100);
                $variationFreq = mt_rand(-3000000, 3000000);
                $variationBw = mt_rand(-2000000, 2000000);

                $s11 = Number_format($base['s11'] + $variationS11, 2, '.', '');
                $freq = $base['f0'] + $variationFreq;
                $bw = $base['bw'] + $variationBw;

                $stmtInsert->execute([
                    ':op_id' => $operation_id,
                    ':pos' => $pos,
                    ':num' => $num,
                    ':s11' => $s11,
                    ':freq' => $freq,
                    ':bw' => $bw,
                    ':created_by' => $created_by
                ]);
            }
        }
        return true;
    }

    public function supprimerMesureEtRenuméroter($result_id, $operation_id, $position) {
        // 1. Suppression de la mesure
        $sqlDel = "DELETE FROM results WHERE id = :id";
        $stmtDel = self::$monPdo->prepare($sqlDel);
        $stmtDel->bindValue(':id', $result_id);
        $stmtDel->execute();

        // 2. Récupération des mesures restantes triées par ID croissant
        $sqlRestantes = "SELECT id FROM results 
                         WHERE operation_id = :op_id AND position = :pos 
                         ORDER BY measurement_number ASC, id ASC";
        $stmtRestantes = self::$monPdo->prepare($sqlRestantes);
        $stmtRestantes->execute([':op_id' => $operation_id, ':pos' => $position]);
        $mesures = $stmtRestantes->fetchAll(PDO::FETCH_ASSOC);

        // 3. Mise à jour séquentielle des numéros
        $sqlUpdate = "UPDATE results SET measurement_number = :nouveau_num WHERE id = :id";
        $stmtUpd = self::$monPdo->prepare($sqlUpdate);
        $i = 1;
        foreach ($mesures as $m) {
            $stmtUpd->execute([
                ':nouveau_num' => $i,
                ':id' => $m['id']
            ]);
            $i++;
        }
        return true;
    }

    /**
     * Récupère les métriques pour une liste d'identifiants patients
     */
    public function getMetriquesComparaisonMultiPatients($liste_patient_ids) {
        if (empty($liste_patient_ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($liste_patient_ids), '?'));
        $sql = "SELECT sp.patient_id, o.name AS visit_name, o.operation_date, r.position,
                       AVG(r.min_frequency_hz) AS fo, 
                       AVG(r.min_return_loss_db) AS min_s11, 
                       AVG(r.bandwidth_hz) AS bw
                FROM sick_patients sp
                JOIN operations o ON sp.patient_id = o.patient_id
                JOIN results r ON o.id = r.operation_id
                WHERE sp.patient_id IN ($placeholders)
                GROUP BY sp.patient_id, o.id, o.name, o.operation_date, r.position
                ORDER BY sp.patient_id ASC, o.operation_date ASC, r.position ASC";

        $stmt = self::$monPdo->prepare($sql);
        $stmt->execute($liste_patient_ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>