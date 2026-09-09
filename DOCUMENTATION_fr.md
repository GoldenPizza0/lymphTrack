# LymphTrack — Technical & Functional Documentation

## 1. Overview

LymphTrack est une application web clinique développée selon une architecture MVC procédurale en PHP/MySQL, conçue pour le suivi des patients atteints de lymphœdème.

L'application permet :
- D'enregistrer et de gérer les dossiers médicaux des patients atteints (`sick_patients`) et de maintenir une base de référence saine (`healthy_patients`).
- De consigner les interventions et consultations de suivi (`operations`) avec gestionnaire dédié des clichés photographiques (`photos`).
- De collecter, enregistrer et tracer les mesures radiofréquences (RF) multi-positions (`results`) : perte de retour S11 minimale (`min_return_loss_db`), fréquence de résonance (`min_frequency_hz`) et bande passante (`bandwidth_hz`).
- D'analyser les données de cohortes à travers des filtres multicritères (module Outcomes).
- D'administrer les accès utilisateurs (`users`) avec traçabilité complète des créations/modifications (`created_by`, timestamps) et gestion des privilèges (`user_type_enum`).

L'interface adopte une ergonomie responsive « mobile-first », optimisée pour une manipulation tactile au chevet du patient via smartphone, tablette ou ordinateur.
```text
LymphTrack/
├── controleur/
│   ├── c_connexion.php               # Authentication: login, logout, and session bootstrap
│   ├── c_gererFollowup.php           # Post-operative follow-up consultation workflow
│   ├── c_gererMesures.php            # Measurement capture, signal metrics, and RF plotting
│   ├── c_gererPatients.php           # Patient management: listing, creation, and profile
│   ├── c_outcomes.php                # Population analytics, dynamic multi-attribute filters
│   └── c_reglages.php                # Account settings, profile edit, user administration
├── modele/
│   └── class.pdoLymphTrack.inc.php   # Singleton PDO wrapper handling all MySQL transactions
├── uploads/
│   └── photos/                       # Stored patient clinical photos and arm iconography
│       └── MV005_7_photo1_1788970313.ico
├── vue/
│   ├── v_accueil.php                 # Landing dashboard / entry portal after login
│   ├── v_bandeau.php                 # Top navigation banner and clinical branding
│   ├── v_connexion.php               # Login interface (email/username + password)
│   ├── v_creerFollowup.php           # Form to register a post-op visit / operation event
│   ├── v_creerModifierUtilisateur.php# Unified account creation & profile edition modal/view
│   ├── v_creerPatient.php            # Patient registration form (demographics, clinical data)
│   ├── v_entete.php                  # Global HTML <head>, meta viewport, and CSS inclusions
│   ├── v_gestionUtilisateurs.php     # Admin dashboard: user listing, privilege management
│   ├── v_modifierCompte.php          # Personal account credentials and profile updates
│   ├── v_outcomesFiltres.php         # Multi-criteria filtering form for population studies
│   ├── v_outcomesResultats.php       # Analytical results view with summary metrics & trends
│   ├── v_pied.php                    # Global footer, mobile bottom navigation bar, JS assets
│   ├── v_position.php                # Anatomical sensor positioning (1-6) & live RF plots
│   ├── v_profilPatient.php           # Complete clinical file, chronological history & visits
│   ├── v_reglages.php                # Settings hub (app info, admin switch, profile overview)
│   └── v_visite.php                  # Detailed consultation view: measurements per position
├── .gitignore                        # Git exclusion rules (caches, local credentials)
├── database.sql                      # Complete MySQL schema dump and initial seed dataset
├── DOCUMENTATION.md                  # Comprehensive architectural and user documentation
├── index.php                         # Application front controller & routing entry point
└── README  
```
---

## 2. Database Schema (`lymphtrackdata`)

Le schéma relationnel repose sur 6 tables principales garantissant la traçabilité des actes cliniques et l'analyse comparative.

### Table `users`
Gère les comptes praticiens et l'authentification.
- `id` (VARCHAR, PK) : Identifiant textuel ou UUID du praticien.
- `email` (VARCHAR, Unique) : Adresse électronique de connexion.
- `name` (VARCHAR) : Nom complet du praticien.
- `role` (VARCHAR) : Titre ou fonction clinique/recherche.
- `institution` (VARCHAR) : Centre hospitalier ou université de rattachement.
- `user_type` (ENUM : 'USER', 'ADMIN', 'SUPER_ADMIN') : Habilitation applicative.
- `created_at` (TIMESTAMP) : Date et heure d'enregistrement du compte.
- `created_by` (VARCHAR, FK -> `users.id`) : Utilisateur ayant créé ce compte.

### Table `sick_patients`
Contient les données cliniques et démographiques des patients suivis.
- `patient_id` (VARCHAR, PK) : Identifiant clinique anonymisé unique (ex. MV005).
- `age` (INT) : Âge du patient.
- `gender` (ENUM : 'M', 'F', 'Other') : Sexe biologique.
- `lymphedema_side` (ENUM : 'Left', 'Right', 'Both') : Côté anatomique atteint.
- `bmi` (FLOAT, Nullable) : Indice de Masse Corporelle (stocké à `NULL` si non renseigné pour préserver les moyennes statistiques).
- `notes` (TEXT, Nullable) : Observations cliniques et antécédents.
- `created_by` (VARCHAR, FK -> `users.id`) : Praticien ayant créé la fiche.
- `created_at` / `updated_at` (TIMESTAMP) : Dates de suivi et de mise à jour.

### Table `operations`
Enregistre les actes chirurgicaux, consultations et visites de suivi.
- `id` (INT, PK, Auto-Increment) : Identifiant unique de l'intervention / visite.
- `patient_id` (VARCHAR, FK -> `sick_patients.patient_id`) : Référence du patient associé.
- `name` (VARCHAR) : Libellé de l'intervention ou de la visite (ex. Baseline, Post-op 1 month).
- `operation_date` (DATE) : Date de la séance.
- `notes` (TEXT, Nullable) : Notes cliniques liées à l'opération.
- `created_by` / `updated_by` (VARCHAR, FK -> `users.id`) : Praticiens responsables.
- `created_at` / `updated_at` (TIMESTAMP) : Horodatage d'audit.

### Table `photos`
Stocke les photographies cliniques associées aux consultations.
- `id` (INT, PK, Auto-Increment) : Identifiant du fichier.
- `operation_id` (INT, FK -> `operations.id`) : Visite ou intervention rattachée.
- `filename` (TEXT) : Nom du fichier ou chemin d'accès dans `uploads/photos/`.
- `created_by` (VARCHAR, FK -> `users.id`) : Utilisateur ayant téléversé le fichier.
- `created_at` / `updated_at` (TIMESTAMP) : Horodatage du téléversement et modifications.

### Table `results`
Contient les séries de mesures radiofréquences brutes par position anatomique.
- `id` (INT, PK, Auto-Increment) : Identifiant unique de la mesure.
- `operation_id` (INT, FK -> `operations.id`) : Consultation associée.
- `position` (INT) : Site anatomique de la mesure (positions 1 à 6).
- `measurement_number` (INT) : Ordre de passage de la mesure sur la position donnée.
- `min_return_loss_db` (FLOAT) : Valeur minimale de perte de retour S11 mesurée (en dB).
- `min_frequency_hz` (FLOAT) : Fréquence de résonance associée (en Hz).
- `bandwidth_hz` (FLOAT) : Largeur de bande du capteur (en Hz).
- `uploaded_at` (TIMESTAMP) : Date de capture/transfert du capteur.
- `created_by` / `updated_by` (VARCHAR, FK -> `users.id`) : Traçabilité des saisies.
- `created_at` / `updated_at` (TIMESTAMP) : Horodatages de création et modification.

### Tables de Référence : `healthy_metadata` & `healthy_patients`
Permettent d'enregistrer des mesures moyennes sur des membres sains servant de témoins pour l'analyse comparative.
- **`healthy_metadata`** :
  - `patient_id` (INT, PK) : Identifiant du sujet sain.
  - `healthy_side` (VARCHAR) : Côté sain analysé (ex. Left / Right).
- **`healthy_patients`** :
  - `id` (INT, PK, Auto-Increment) : Identifiant de la mesure de contrôle.
  - `patient_id` (INT, FK -> `healthy_metadata.patient_id`) : Sujet sain associé.
  - `position` (INT) : Emplacement de mesure (1 à 6).
  - `average_min_frequency_hz` (FLOAT) : Fréquence moyenne de référence.
  - `average_min_return_loss_db` (FLOAT) : Perte de retour moyenne de référence.
  - `average_bandwidth_hz` (FLOAT) : Bande passante moyenne de référence.

---

## 3. Project Files & Architecture

### Point d'entrée & Routeur
- **`index.php`** : Contrôleur frontal unique (*Front Controller*). Démarre la session PHP, initialise le singleton PDO `PdoLymphTrack`, contrôle le statut de connexion de l'utilisateur et route la requête vers le bon contrôleur via les paramètres d'URL (`$action` / `$uc`).

---

### Contrôleurs (`controleur/`)
- **`c_connexion.php`** : Gère la mire de connexion, vérifie l'adresse e-mail et le hash du mot de passe (`password_verify`), instancie la session utilisateur (`$_SESSION['user_id']`, permissions) et traite la déconnexion.
- **`c_gererPatients.php`** : Gère la liste des patients atteints (`sick_patients`), le formulaire d'admission (génération du `patient_id`, calcul de l'âge, gestion du BMI facultatif enregistré en `NULL`) et l'accès au profil individuel.
- **`c_gererFollowup.php`** : Orchestre l'enregistrement des nouvelles interventions (`operations`), la saisie des notes cliniques et l'association des photographies (`photos`).
- **`c_gererMesures.php`** : Gère la navigation entre les 6 positions anatomiques, l'enregistrement des métriques RF (`min_frequency_hz`, `min_return_loss_db`, `bandwidth_hz`), ainsi que la suppression ciblée d'une mesure avec réindexation automatique de `measurement_number`.
- **`c_outcomes.php`** : Exécute le module statistique. Applique les filtres multicritères sur la population (sexe, tranche d'âge, côté atteint `lymphedema_side`, seuils de BMI) et compare les résultats avec les données témoins de `healthy_patients`.
- **`c_reglages.php`** : Affiche les informations du compte connecté (institution, rôle dynamique) et héberge la console d'administration pour la gestion des utilisateurs (`v_gestionUtilisateurs.php`, `v_creerModifierUtilisateur.php`).

---

### Modèle d'Accès aux Données (`modele/`)
- **`class.pdoLymphTrack.inc.php`** : Classe Singleton encapsulant l'ensemble des requêtes SQL préparées (protection intégrale contre les injections SQL).
  
  **Méthodes principales :**
  - `getPdoLymphTrack()` : Fournit l'instance unique de connexion PDO.
  - `getUtilisateur($email, $mdp)` : Authentifie le praticien via son email et mot de passe hashé.
  - `getUtilisateurById($id)` : Récupère les données de profil (`name`, `role`, `institution`, `user_type`).
  - `getLesUtilisateurs()` : Récupère l'ensemble des praticiens (console administrateur).
  - `creerUtilisateur(...)` / `modifierUtilisateur(...)` : Insère ou met à jour un profil praticien.
  - `supprimerUtilisateur($id)` : Supprime un compte utilisateur avec garde-fou contre l'auto-suppression.
  - `getLesPatients()` : Liste tous les patients de la table `sick_patients`.
  - `getPatientById($patient_id)` : Retourne le dossier complet d'un patient par son identifiant alphanumérique.
  - `creerPatient(...)` : Enregistre un patient en affectant `NULL` strict au BMI si non renseigné.
  - `getOperationsByPatientId($patient_id)` : Récupère l'historique chronologique des visites/opérations.
  - `creerOperation(...)` : Enregistre une consultation dans `operations` et référence les images dans `photos`.
  - `getOperationById($id)` : Récupère les détails d'une intervention.
  - `getMesuresByOperationEtPosition($operation_id, $position)` : Récupère les mesures RF d'une position donnée ordonnées par `measurement_number`.
  - `ajouterMesure(...)` : Insère une mesure avec calcul ou incrémentation de `measurement_number`.
  - `supprimerMesureEtRenuméroter($result_id, $operation_id, $position)` : Supprime la mesure ciblée par son identifiant et recalcule immédiatement les index `measurement_number` des mesures restantes sur cette position pour maintenir une séquence ininterrompue (1, 2, 3...).
  - `getHealthyDataByPosition($position)` : Récupère les moyennes témoins issues de `healthy_patients`.
  - `getOutcomesFiltres(...)` : Construit dynamiquement les requêtes de filtrage sur la cohorte selon les critères sélectionnés.

---

### Vues (`vue/`)
- **`v_entete.php`** : Balises d'en-tête communes, liens CSS et balise `<meta name="format-detection" content="telephone=no">` neutralisant la détection automatique de numéros de téléphone sur iOS Safari pour les fréquences en Hz.
- **`v_bandeau.php`** : Barre supérieure affichant le titre de l'écran, le logo et les actions contextuelles (retours, raccourcis).
- **`v_pied.php`** : Pied de page intégrant la barre de navigation mobile fixe inférieure (*Bottom Navigation Bar* : Patients, Outcomes, Settings) et l'initialisation des scripts Chart.js.
- **`v_connexion.php`** : Interface de connexion avec saisie de l'e-mail et du mot de passe.
- **`v_accueil.php`** : Tableau de bord listant les fiches patients avec barre de recherche rapide.
- **`v_creerPatient.php`** : Formulaire d'admission d'un patient (code identifiant, âge, sexe, côté atteint, option « Do not provide BMI »).
- **`v_profilPatient.php`** : Vue d'ensemble du dossier patient et journal chronologique des visites.
- **`v_creerFollowup.php`** : Formulaire d'enregistrement d'une visite/opération et téléversement de photographies.
- **`v_visite.php`** : Page de suivi d'une consultation donnant accès aux 6 positions de mesures.
- **`v_position.php`** : Page d'une position anatomique (1 à 6). Affiche les mesures RF, les boutons d'ajout/suppression et le graphique interactif du capteur tracé avec Chart.js.
- **`v_outcomesFiltres.php`** : Interface de filtres multicritères sur la population (sélection par lot, tranches d'âge, côté lymphœdème, IMC).
- **`v_outcomesResultats.php`** : Affichage graphique des distributions et comparaisons de résonance entre patients et sujets sains.
- **`v_reglages.php`** : Espace paramètres présentant les informations du compte utilisateur et le raccourci vers la gestion d'équipe.
- **`v_modifierCompte.php`** : Formulaire de mise à jour des identifiants personnels du praticien connecté.
- **`v_gestionUtilisateurs.php`** : Tableau de bord administrateur répertoriant tous les comptes utilisateurs avec leurs privilèges.
- **`v_creerModifierUtilisateur.php`** : Formulaire unifié permettant de créer ou de mettre à jour un compte praticien (rôle, type d'accès, institution).

---

### Fichiers & Dossiers Utilitaires
- **`uploads/photos/`** : Répertoire de stockage physique des photos cliniques téléversées.
- **`database.sql`** : Script d'export contenant la structure complète des tables et le jeu de données d'initialisation.
- **`DOCUMENTATION.md`** : Guide exhaustif technique, fonctionnel et architectural du projet.
- **`README`** : Guide condensé de prise en main rapide.
- **`.gitignore`** : Règles d'exclusion Git pour les caches locaux et fichiers système.

---

## 4. Multi-Device Remote Access via Ngrok

L'application peut être exécutée et testée en conditions cliniques réelles sur n'importe quel appareil connecté à Internet (smartphone iOS/Android, tablette, ordinateur externe) sans nécessiter de serveur distant payant.

Le PC faisant tourner Laragon agit comme serveur local, et **Ngrok** établit un tunnel sécurisé HTTPS vers l'extérieur.

### Procédure de configuration pas à pas :

1. **Vérification de l'environnement local :**
   - Démarrer les services **Apache** et **MySQL** dans l'interface de Laragon.
   - S'assurer que l'application répond en local sur le navigateur : `http://localhost/LymphTrack/`.

2. **Démarrage du tunnel Ngrok :**
   - Ouvrir une invite de commandes Windows (`cmd`).
   - Lancer le tunnel HTTP sur le port 80 d'Apache :
     ```cmd
     ngrok http 80
     ```
   - L'écran du terminal affiche la session active :
     ```text
     Session Status                online
     Forwarding                    [https://xxxxxxxx.ngrok-free.dev](https://xxxxxxxx.ngrok-free.dev) -> http://localhost:80
     ```

3. **Connexion depuis un smartphone ou une tablette :**
   - Sur l'appareil distant (connecté en Wi-Fi ou réseau mobile 4G/5G), ouvrir un navigateur web (Safari, Chrome, etc.).
   - Saisir l'adresse de redirection générée en ajoutant le dossier de l'application à la fin :
     ```text
     [https://xxxxxxxx.ngrok-free.dev/LymphTrack/](https://xxxxxxxx.ngrok-free.dev/LymphTrack/)
     ```
   - Lors de la première visite, une page de vérification Ngrok apparaît : cliquer sur le bouton bleu **« Visit Site »**.
   - L'application s'affiche avec son ergonomie mobile dédiée.

> **Important :** Pour maintenir l'accès distant actif, le PC hôte doit rester sous tension (sans passer en veille prolongée) et la fenêtre du terminal exécutant `ngrok http 80` doit impérativement rester ouverte.