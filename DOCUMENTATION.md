# Documentation Technique et Fonctionnelle — LymphTrack

## 1. Architecture Générale & Flux de Données

LymphTrack est structuré selon le patron de conception **MVC (Modèle - Vue - Contrôleur)** en PHP natif (compatible PHP 8+ et environnements WAMP/Laragon), sans dépendance externe lourde en backend.

```text
Requête HTTP  ──>  index.php (Routeur / Contrôleur Frontal)
                       │
                       ▼
             Contrôleurs (controleur/)
             ├── c_connexion.php
             ├── c_gererPatients.php
             ├── c_gererFollowup.php
             ├── c_gererMesures.php
             ├── c_outcomes.php
             └── c_reglages.php
                 │              ▲
        Données  ▼              │ Données brutes
     Modèle (modele/)           │
     └── class.pdoLymphTrack.inc.php
                 │
                 ▼
       Rendu UI (vue/)
       ├── Gabarits globaux (v_entete, v_pied)
       ├── Module Patients (v_patients, v_profilPatient, v_creerPatient)
       ├── Module Visites & Mesures (v_visite, v_position, v_creerVisite)
       ├── Module Outcomes (v_outcomesFiltres, v_outcomesResultats)
       └── Module Paramètres (v_reglages, v_modifierCompte, v_gestionUtilisateurs, v_creerModifierUtilisateur)
```

---

## 2. Point d'Entrée & Routeur Principal

### `index.php`
* **Rôle** : Contrôleur frontal unique de l'application.
* **Fonctionnalités** :
  * Initialise la session utilisateur (`session_start()`).
  * Instancie le connecteur PDO via le fichier `modele/class.pdoLymphTrack.inc.php`.
  * Lit le paramètre d'URL `$_REQUEST['uc']` (*Use Case*) pour inclure le contrôleur approprié :
    * `connexion` $\rightarrow$ `controleur/c_connexion.php`
    * `gererPatients` $\rightarrow$ `controleur/c_gererPatients.php`
    * `gererFollowup` $\rightarrow$ `controleur/c_gererFollowup.php`
    * `gererMesures` $\rightarrow$ `controleur/c_gererMesures.php`
    * `outcomes` $\rightarrow$ `controleur/c_outcomes.php`
    * `reglages` $\rightarrow$ `controleur/c_reglages.php`
    * Redirection par défaut vers `connexion`.

---

## 3. Couche Modèle (`modele/`)

### `modele/class.pdoLymphTrack.inc.php`
* **Rôle** : Encapsule l'intégralité des requêtes SQL vers la base MySQL `lymphtrackdata` via le design pattern Singleton.
* **Méthodes & Responsabilités** :
  * **Authentification & Gestion des Comptes** :
    * `verifierUtilisateur($email)` : Vérifie l'adresse e-mail et retourne le profil utilisateur.
    * `getUtilisateurById($user_id)` : Récupère le profil complet (nom, rôle, institution, niveau d'accès).
    * `getTousLesUtilisateurs()` : Liste l'ensemble des utilisateurs enregistrés pour l'espace administration.
    * `creerUtilisateur($name, $email, $role, $user_type, $institution)` : Insère un nouvel utilisateur (`USER`, `ADMIN`, `SUPER_ADMIN`).
    * `modifierUtilisateur($id, $name, $email, $role, $user_type, $institution)` : Met à jour les informations d'un compte.
    * `supprimerUtilisateur($id)` : Supprime un compte utilisateur.
  * **Gestion des Patients (`sick_patients`)** :
    * `getPatients($search, $side, $gender)` : Recherche et filtrage multi-critères des patients.
    * `getPatientById($id)` : Récupère les détails d'un patient (âge, BMI, côté atteint, notes).
    * `genererProchainPatientId()` : Calcule automatiquement l'identifiant séquentiel suivant (ex: `MV001`, `MV002`...).
    * `ajouterPatient(...)` : Enregistre un nouveau dossier patient.
    * `supprimerPatient($id)` : Supprime un patient et ses données associées.
    * `getExportDataPourPatients($ids)` : Compile l'ensemble des données patient, visites et mesures pour l'export CSV.
  * **Gestion des Visites / Suivis (`operations`, `photos`)** :
    * `getVisitesParPatient($patient_id)` : Récupère la chronologie des consultations d'un patient.
    * `getOperationById($id)` : Récupère les métadonnées d'une visite.
    * `creerOperation(...)` : Crée une nouvelle visite clinique.
    * `ajouterPhotoOperation(...)` / `getPhotosParOperation(...)` : Téléversement et consultation des photos cliniques.
  * **Gestion des Mesures & Métriques (`results`)** :
    * `getMesuresVisite($operation_id)` : Calcule les moyennes ($f_0$, Min $S_{11}$, BW) pour les 6 positions d'une visite.
    * `getMesuresParPosition($operation_id, $position)` : Liste les mesures unitaires associées à une position anatomique.
    * `ajouterMesure(...)` : Insère une mesure avec calcul automatique du rang séquentiel (`measurement_number`).
    * `supprimerMesureEtRenuméroter(...)` : Supprime une mesure et réaligne la numérotation séquentielle restante (1, 2, 3...).
    * `importerToutesMesuresAutomatique(...)` : Génère et insère en base de données un jeu complet de 18 mesures calibrées (3 répétitions $\times$ 6 positions).
    * `getToutesMetriquesPatient($patient_id)` : Agrège les moyennes de toutes les positions pour tracer les évolutions multi-visites.
    * `getMetriquesComparaisonMultiPatients($liste_ids)` : Récupère les données comparatives pour les cohortes du module *Outcomes*.

---

## 4. Couche Contrôleurs (`controleur/`)

### `controleur/c_connexion.php`
* **Rôle** : Contrôle d'accès et cycle de vie de la session.
* **Actions** :
  * `demandeConnexion` : Présente l'interface de connexion.
  * `valideConnexion` : Authentifie l'utilisateur et initialise les variables de session.
  * `deconnexion` : Détruit la session et redirige vers la mire de connexion.

### `controleur/c_gererPatients.php`
* **Rôle** : Répertoire des patients et profils individuels.
* **Actions** :
  * `afficherPatients` : Liste filtrable des patients avec recherche en temps réel.
  * `voirProfilPatient` : Profil complet du patient, timeline des visites et interface *Outcomes* (grille 2 colonnes pour "All" ou graphique détaillé par position).
  * `formulaireAjoutPatient` & `validerAjoutPatient` : Création assistée d'un nouveau patient.
  * `supprimerPatient` : Suppression d'un dossier.
  * `exporterPatients` : Génération et téléchargement d'un export consolidé.

### `controleur/c_gererFollowup.php`
* **Rôle** : Gestion des consultations cliniques (visites).
* **Actions** :
  * `voirVisite` : Vue détaillée d'une consultation (silhouette anatomique interactive, photos cliniques, graphique comparatif des 6 positions).
  * `formulaireAjout` & `validerAjout` : Planification d'une nouvelle consultation.
  * `televerserPhoto` : Téléversement et association d'une photographie clinique.

### `controleur/c_gererMesures.php`
* **Rôle** : Contrôleur spécialisé dans la gestion des données de signaux micro-ondes.
* **Actions** :
  * `voirPosition` : Affiche la page détaillée d'une position (7.8), liste les mesures individuelles et trace le graphe de répétabilité.
  * `ajouterMesureSimulation` : Ajoute manuellement une répétition unitaire.
  * `supprimerMesure` : Supprime une répétition avec re-numérotation sans trou.
  * `importerToutesMesures` : Exécute l'action *Import all results* en injectant les 18 mesures calibrées de la visite.
  * `exporterPositionZip` : Compile et télécharge une archive ZIP contenant les mesures en CSV pour la position courante.

### `controleur/c_outcomes.php`
* **Rôle** : Moteur de comparaison clinique multi-patients (7.10).
* **Actions** :
  * `afficherFiltres` : Affiche la cohorte avec filtres dynamiques (ID, visite, position, genre, côté, âge, BMI) et sélection plafonnée à 10 patients.
  * `afficherResultats` : Aiguille le rendu graphique selon le mode retenu (Position-based, Visit-based sur 6 graphiques, ou Cross-comparison).

### `controleur/c_reglages.php`
* **Rôle** : Paramètres personnels et console d'administration des utilisateurs (7.9 & 7.9.1).
* **Actions** :
  * `afficherReglages` : Tableau de bord des paramètres et métadonnées système.
  * `formulaireModifierMonCompte` & `validerModifierMonCompte` : Mise à jour des informations personnelles de l'utilisateur connecté.
  * `gererUtilisateurs` : Console d'administration listant les utilisateurs avec badges colorés et protection anti-suppression de son propre compte.
  * `formulaireUtilisateur` & `validerUtilisateur` : Formulaire unique gérant la création et la modification d'un compte (`USER`, `ADMIN`, `SUPER_ADMIN`).
  * `supprimerUtilisateur` : Révocation d'un compte utilisateur.

---

## 5. Couche Vues (`vue/`)

### Layout & Gabarits Communs
* **`vue/v_entete.php`** : Structure HTML5, balises méta viewport (conception mobile-first), polices et styles CSS généraux.
* **`vue/v_pied.php`** : Barre de navigation inférieure fixe (*Bottom Bar*) avec icônes SVG et surbrillance automatique de l'onglet actif (*Patients*, *Outcomes*, *Settings*).

### Module Patients
* **`vue/v_patients.php`** : Liste visuelle des patients, barre de recherche instantanée, filtres par genre/côté, actions de suppression et sélection pour export groupé.
* **`vue/v_creerPatient.php`** : Formulaire d'enregistrement patient avec calcul du BMI et sélection du côté atteint.
* **`vue/v_profilPatient.php`** :
  * Fiche récapitulative et notes médicales.
  * Timeline verticale des visites (*Follow-up*).
  * Sélecteur de position (*All*, *1* à *6*) opérant en JavaScript pur sans rechargement de page.
  * Vue *All* : Grille 2 colonnes avec contrainte stricte de largeur (`minmax(0, 1fr)`) affichant 6 graphiques séparés et leur légende globale.
  * Vue individuelle : Graphique grand format avec exports PNG / CSV et tableau *Visit Metrics* ($f_0$, Min $S_{11}$, BW).

### Module Visites & Mesures
* **`vue/v_visite.php`** :
  * Silhouette vectorielle SVG avec ligne de hanches continue et pieds arrondis.
  * 6 pastilles interactives redirigeant vers chaque position anatomique.
  * Bouton *Import all results* déclenchant l'import complet des 18 mesures.
  * Galerie de photographies médicales et graphique comparatif des 6 positions.
* **`vue/v_creerVisite.php`** : Formulaire d'enregistrement d'une nouvelle consultation (type de visite, date, notes).
* **`vue/v_position.php`** :
  * Liste des mesures individuelles (*Measurement 1, 2, 3...*) avec valeurs détaillées et suppression unitaire.
  * Bouton *Add measurement(s)*.
  * Graphique comparatif de répétabilité (courbes superposées).
  * Bouton d'export ZIP des données de la position.

### Module Outcomes
* **`vue/v_outcomesFiltres.php`** :
  * Filtres croisés démographiques et cliniques.
  * Bouton intelligent basculant entre **Select All** et **Deselect All** selon l'état de la sélection.
  * Compteur dynamique et limitation de sélection à 10 patients maximum.
* **`vue/v_outcomesResultats.php`** :
  * Rendu graphique selon le mode : 6 graphiques séparés (mode *All positions*) ou graphique unique centré (mode *Cross-comparison*).

### Module Paramètres & Administration
* **`vue/v_reglages.php`** : En-tête profil, liens de sécurité, section *Administrator* conditionnelle (comptes admin uniquement) et bouton *Sign Out*.
* **`vue/v_modifierCompte.php`** : Formulaire de mise à jour des coordonnées personnelles de l'utilisateur connecté.
* **`vue/v_gestionUtilisateurs.php`** : Liste d'administration des comptes utilisateurs avec badges colorés et exclusion du bouton de suppression sur son propre compte.
* **`vue/v_creerModifierUtilisateur.php`** : Formulaire avec sélecteur segmenté pour gérer la création ou la modification des accès `USER`, `ADMIN`, `SUPER_ADMIN`.

---

## 6. Modélisation Physique des Signaux Micro-ondes

Afin d'éviter le stockage de fichiers VNA volumineux, les spectres complets de réflexion $S_{11}(f)$ sont reconstitués dynamiquement côté client via **Chart.js** à partir des 3 métriques scalaires de la base MySQL ($f_0$, Min $S_{11}$, BW) selon le modèle de résonateur de Lorentz :

$$S_{11}(f) \approx S_{11,\text{baseline}} + \frac{\text{Min } S_{11} - S_{11,\text{baseline}}}{1 + \left( \frac{2(f - f_0)}{\text{BW}} \right)^2}$$

Une composante sinusoïdale haute fréquence est superposée pour modéliser les ondulations résiduelles des câbles RF, assurant un rendu graphique réaliste et réactif aux modifications de la base de données.