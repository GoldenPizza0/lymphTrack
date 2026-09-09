# LymphTrack — Technical & Functional Documentation

## 1. Overview

LymphTrack is a clinical web application developed using a procedural MVC architecture in PHP/MySQL, designed for monitoring patients suffering from lymphedema.

The application allows users to:
- Register and manage patient medical records (`sick_patients`) and maintain a healthy control reference baseline (`healthy_patients`).
- Record surgical procedures and follow-up consultations (`operations`) with a dedicated photographic asset manager (`photos`).
- Collect, record, and plot multi-position radio-frequency (RF) sensor measurements (`results`): minimum return loss S11 (`min_return_loss_db`), resonance frequency (`min_frequency_hz`), and bandwidth (`bandwidth_hz`).
- Analyze cohort data through advanced multi-criteria filters (Outcomes module).
- Administer user accounts (`users`) with full audit traceability (`created_by`, timestamps) and privilege tiers (`user_type_enum`).

The interface follows a mobile-first responsive design, optimized for single-handed bedside touch interaction via smartphones, tablets, or laptops.
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

The relational schema is built around 6 main tables ensuring clinical traceability and comparative cohort analysis.

### Table `users`
Stores practitioner accounts and handles authentication.
- `id` (VARCHAR, PK): Unique practitioner identifier or UUID string.
- `email` (VARCHAR, Unique): Practitioner email address used for login.
- `name` (VARCHAR): Practitioner full name.
- `role` (VARCHAR): Clinical or research title/role.
- `institution` (VARCHAR): Affiliated clinical center or university.
- `user_type` (ENUM: 'USER', 'ADMIN', 'SUPER_ADMIN'): System authorization privilege.
- `created_at` (TIMESTAMP): Account creation timestamp.
- `created_by` (VARCHAR, FK -> `users.id`): Identifier of the user who created this account.

### Table `sick_patients`
Contains clinical and demographic records for monitored patients.
- `patient_id` (VARCHAR, PK): Unique pseudonymized clinical identifier (e.g., MV005).
- `age` (INT): Patient age.
- `gender` (ENUM: 'M', 'F', 'Other'): Biological sex.
- `lymphedema_side` (ENUM: 'Left', 'Right', 'Both'): Anatomical side affected by lymphedema.
- `bmi` (FLOAT, Nullable): Body Mass Index (stored as `NULL` if omitted to preserve statistical cohort averages).
- `notes` (TEXT, Nullable): Freeform medical observations and history.
- `created_by` (VARCHAR, FK -> `users.id`): Practitioner who registered the record.
- `created_at` / `updated_at` (TIMESTAMP): Creation and update audit timestamps.

### Table `operations`
Tracks medical interventions, baseline visits, and follow-up consultations.
- `id` (INT, PK, Auto-Increment): Unique procedure or consultation identifier.
- `patient_id` (VARCHAR, FK -> `sick_patients.patient_id`): Reference to the associated patient.
- `name` (VARCHAR): Label of the visit or procedure (e.g., Baseline, Post-op 1 month).
- `operation_date` (DATE): Date of the consultation session.
- `notes` (TEXT, Nullable): Clinical notes tied to the procedure.
- `created_by` / `updated_by` (VARCHAR, FK -> `users.id`): Responsible practitioners.
- `created_at` / `updated_at` (TIMESTAMP): Audit timestamps.

### Table `photos`
Stores clinical photographs captured during follow-up visits.
- `id` (INT, PK, Auto-Increment): Unique photo identifier.
- `operation_id` (INT, FK -> `operations.id`): Associated consultation or operation.
- `filename` (TEXT): File path or filename relative to `uploads/photos/`.
- `created_by` (VARCHAR, FK -> `users.id`): Practitioner who uploaded the file.
- `created_at` / `updated_at` (TIMESTAMP): Upload and modification timestamps.

### Table `results`
Contains raw multi-position RF sensor metrics.
- `id` (INT, PK, Auto-Increment): Unique measurement record identifier.
- `operation_id` (INT, FK -> `operations.id`): Associated consultation.
- `position` (INT): Anatomical sensor site (positions 1 through 6).
- `measurement_number` (INT): Sequential measurement index on the given position.
- `min_return_loss_db` (FLOAT): Measured minimum S11 return loss magnitude in dB.
- `min_frequency_hz` (FLOAT): Corresponding resonance frequency in Hz.
- `bandwidth_hz` (FLOAT): Sensor impedance bandwidth in Hz.
- `uploaded_at` (TIMESTAMP): Time of capture/sensor data ingestion.
- `created_by` / `updated_by` (VARCHAR, FK -> `users.id`): Data entry audit tracking.
- `created_at` / `updated_at` (TIMESTAMP): Audit creation and update timestamps.

### Reference Tables: `healthy_metadata` & `healthy_patients`
Store control baseline measurements from healthy limbs for comparative clinical analysis.
- **`healthy_metadata`**:
  - `patient_id` (INT, PK): Unique healthy subject identifier.
  - `healthy_side` (VARCHAR): Analyzed healthy arm side (e.g., Left / Right).
- **`healthy_patients`**:
  - `id` (INT, PK, Auto-Increment): Control metric identifier.
  - `patient_id` (INT, FK -> `healthy_metadata.patient_id`): Associated control subject.
  - `position` (INT): Anatomical sensor site (1 through 6).
  - `average_min_frequency_hz` (FLOAT): Reference baseline mean resonance frequency.
  - `average_min_return_loss_db` (FLOAT): Reference baseline mean return loss.
  - `average_bandwidth_hz` (FLOAT): Reference baseline mean bandwidth.

---

## 3. Project Files & Architecture

### Entry Point & Routing
- **`index.php`**: Application Front Controller. Initializes the PHP session, boots the PDO Singleton database connection (`PdoLymphTrack`), validates user authentication status, and routes the request to the matching controller via URL query parameters (`$action` / `$uc`).

---

### Controllers (`controleur/`)
- **`c_connexion.php`**: Handles login requests, verifies email addresses against salted password hashes (`password_verify`), provisions session variables (`$_SESSION['user_id']`, permissions), and processes user logout.
- **`c_gererPatients.php`**: Manages the patient registry (`sick_patients`), processes patient intake (generating `patient_id`, calculating age, and handling optional BMI values stored strictly as `NULL`), and directs users to individual patient records.
- **`c_gererFollowup.php`**: Coordinates the registration of follow-up visits and procedures (`operations`), records clinical notes, and processes clinical photo uploads (`photos`).
- **`c_gererMesures.php`**: Controls navigation across the 6 anatomical sites, stores newly acquired RF metrics (`min_frequency_hz`, `min_return_loss_db`, `bandwidth_hz`), and executes measurement deletions with sequential re-indexing of `measurement_number`.
- **`c_outcomes.php`**: Powers the population analytics module. Applies dynamic multi-criteria filters across cohorts (sex, age brackets, affected side `lymphedema_side`, BMI ranges) and cross-references data against healthy control baselines (`healthy_patients`).
- **`c_reglages.php`**: Manages the active user settings interface (institution, dynamic professional role) and provides access to the administration dashboard for user account management (`v_gestionUtilisateurs.php`, `v_creerModifierUtilisateur.php`).

---

### Data Access Layer (`modele/`)
- **`class.pdoLymphTrack.inc.php`**: Central Singleton class encapsulating all prepared SQL statements to prevent SQL injection vulnerabilities.

  **Core Methods:**
  - `getPdoLymphTrack()`: Instantiates or returns the single active PDO connection instance.
  - `getUtilisateur($email, $mdp)`: Validates practitioner login credentials via email and password hash.
  - `getUtilisateurById($id)`: Fetches profile metadata (`name`, `role`, `institution`, `user_type`).
  - `getLesUtilisateurs()`: Retrieves all registered user accounts for the administration panel.
  - `creerUtilisateur(...)` / `modifierUtilisateur(...)`: Inserts or updates practitioner profile records.
  - `supprimerUtilisateur($id)`: Deletes user accounts, with safety guards preventing active administrator self-deletion.
  - `getLesPatients()`: Retrieves all patients from the `sick_patients` table.
  - `getPatientById($patient_id)`: Fetches a complete medical record by patient alphanumeric code.
  - `creerPatient(...)`: Inserts a new patient record, enforcing strict SQL `NULL` for omitted BMI values.
  - `getOperationsByPatientId($patient_id)`: Retrieves the chronological visit and procedure timeline for a patient.
  - `creerOperation(...)`: Registers a consultation in `operations` and records uploaded photo paths in `photos`.
  - `getOperationById($id)`: Fetches details for a specific consultation or surgical event.
  - `getMesuresByOperationEtPosition($operation_id, $position)`: Retrieves RF measurements for a specific site sorted by `measurement_number`.
  - `ajouterMesure(...)`: Records a new RF measurement pass and assigns or increments `measurement_number`.
  - `supprimerMesureEtRenuméroter($result_id, $operation_id, $position)`: Deletes the specified measurement by ID and recalculates `measurement_number` for remaining passes on that site to preserve an unbroken sequence (1, 2, 3...).
  - `getHealthyDataByPosition($position)`: Retrieves reference control averages from `healthy_patients`.
  - `getOutcomesFiltres(...)`: Dynamically builds filtering queries against the cohort according to selected demographic and clinical parameters.

---

### Views (`vue/`)
- **`v_entete.php`**: Common HTML head section containing viewport metadata, CSS stylesheet links, and `<meta name="format-detection" content="telephone=no">` to stop iOS Safari from turning multi-digit Hz frequencies into phone links.
- **`v_bandeau.php`**: Top navigation banner displaying the current screen title, branding, and contextual navigation actions (back buttons, shortcuts).
- **`v_pied.php`**: Global footer rendering the fixed mobile bottom navigation bar (Patients, Outcomes, Settings) and bundling JavaScript dependencies (including Chart.js).
- **`v_connexion.php`**: Practitioner authentication form (email and password inputs).
- **`v_accueil.php`**: Primary dashboard displaying the patient directory with quick search and a new patient registration trigger.
- **`v_creerPatient.php`**: Patient registration form (code identifier, age, biological sex, affected side, and "Do not provide BMI" option).
- **`v_profilPatient.php`**: Patient record summary displaying baseline metrics and a chronological log of visits.
- **`v_creerFollowup.php`**: Form to register a follow-up visit or procedure and upload clinical photographs.
- **`v_visite.php`**: Consultation overview providing direct navigation to the 6 anatomical measurement sites.
- **`v_position.php`**: Anatomical position screen (1 to 6). Displays recorded RF passes, supports adding or removing measurements, and renders real-time sensor curves using Chart.js.
- **`v_outcomesFiltres.php`**: Cohort filter interface (bulk select/deselect, age brackets, lymphedema side, BMI thresholds).
- **`v_outcomesResultats.php`**: Graphical visualization of population distributions and comparative resonance shifts against healthy controls.
- **`v_reglages.php`**: Settings view showing account metadata and providing administrative access to practitioner management.
- **`v_modifierCompte.php`**: Form allowing practitioners to update their personal credentials (name, email, password).
- **`v_gestionUtilisateurs.php`**: Administrator console displaying all user accounts alongside permission levels and action shortcuts.
- **`v_creerModifierUtilisateur.php`**: Unified form handling both account creation and profile updates (role, authorization tier, institution).

---

### Utility Files & Directories
- **`uploads/photos/`**: Physical storage directory for uploaded clinical photographs.
- **`database.sql`**: Full database dump containing table schemas and initial seed datasets.
- **`DOCUMENTATION.md`**: Complete functional, technical, and architectural reference manual.
- **`README`**: Concise quickstart guide for project deployment.
- **`.gitignore`**: Git exclusion rules for temporary assets, system files, and local caches.

---

## 4. Multi-Device Remote Access via Ngrok

The application can be deployed and tested in live clinical conditions across any internet-connected device (iOS/Android smartphones, tablets, external laptops) without requiring a paid cloud hosting instance.

The local development machine running Laragon acts as the host server, while **Ngrok** creates an encrypted public HTTPS tunnel back to the local Apache stack.

### Step-by-Step Setup Guide:

1. **Local Environment Check:**
   - Start **Apache** and **MySQL** inside the Laragon control panel.
   - Verify that the application responds locally in your PC browser at `http://localhost/LymphTrack/`.

2. **Starting the Ngrok Tunnel:**
   - Open the Windows Command Prompt (`cmd`).
   - Launch an HTTP tunnel targeting Apache's port 80:
     ```cmd
     ngrok http 80
     ```
   - The command prompt displays active session information:
     ```text
     Session Status                online
     Forwarding                    [https://xxxxxxxx.ngrok-free.dev](https://xxxxxxxx.ngrok-free.dev) -> http://localhost:80
     ```

3. **Connecting from Smartphones or Tablets:**
   - On the remote mobile device (connected via Wi-Fi or cellular 4G/5G data), open a web browser (Safari, Chrome, etc.).
   - Enter the generated HTTPS forwarding URL, appending the exact project folder path:
     ```text
     [https://xxxxxxxx.ngrok-free.dev/LymphTrack/](https://xxxxxxxx.ngrok-free.dev/LymphTrack/)
     ```
   - On first access, Ngrok may display an initial interstitial page: click the blue **"Visit Site"** button.
   - The application will load with its native mobile interface.

> **Operational Requirement:** For the remote link to remain reachable, the host PC must remain powered on (sleep mode disabled) and the terminal running `ngrok http 80` must remain active.