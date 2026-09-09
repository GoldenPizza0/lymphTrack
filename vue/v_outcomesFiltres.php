<div class="outcomes-container">
    <h1 class="outcomes-main-title">Outcomes</h1>

    <form method="POST" action="index.php?uc=outcomes&action=afficherResultats" id="outcomesForm">
        <!-- 1. Barre de recherche par ID -->
        <div class="search-box">
            <svg class="search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2.2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="filterSearch" placeholder="Search by Patient ID..." onkeyup="filtrerListePatients()">
        </div>

        <!-- 2. Bloc des Filtres -->
        <div class="filters-grid">
            <div class="filter-col">
                <select name="position" id="filterPosition" class="filter-select" onchange="filtrerListePatients()">
                    <option value="All">All positions</option>
                    <?php for ($i = 1; $i <= 6; $i++) : ?>
                        <option value="<?= $i ?>">Position <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-col">
                <select name="visit" id="filterVisit" class="filter-select" onchange="filtrerListePatients()">
                    <option value="All">All visits</option>
                    <option value="PreOP">PreOP</option>
                    <option value="1 month">1 month</option>
                    <option value="1 year">1 year</option>
                </select>
            </div>

            <!-- Deuxième ligne de filtres démographiques -->
            <div class="filter-col-4">
                <select id="filterGender" class="filter-select-sm" onchange="filtrerListePatients()">
                    <option value="All">All gender</option>
                    <option value="female">Female</option>
                    <option value="male">Male</option>
                </select>
            </div>
            <div class="filter-col-4">
                <select id="filterSide" class="filter-select-sm" onchange="filtrerListePatients()">
                    <option value="All">All sides</option>
                    <option value="left">Left</option>
                    <option value="right">Right</option>
                    <option value="bilateral">Bilateral</option>
                </select>
            </div>
            <div class="filter-col-4">
                <select id="filterAge" class="filter-select-sm" onchange="filtrerListePatients()">
                    <option value="All">All ages</option>
                    <option value="0-40">< 40</option>
                    <option value="40-60">40 - 60</option>
                    <option value="60-120">> 60</option>
                </select>
            </div>
            <div class="filter-col-4">
                <select id="filterBmi" class="filter-select-sm" onchange="filtrerListePatients()">
                    <option value="All">All BMI</option>
                    <option value="0-20">< 20</option>
                    <option value="20-25">20 - 25</option>
                    <option value="25-30">25 - 30</option>
                    <option value="30-100">> 30</option>
                </select>
            </div>
        </div>

        <!-- Compteur Patients -->
        <div class="counter-row">
            <span id="patientsCount">Patients: <?= count($lesPatients) ?></span>
            <span id="selectedCount" style="margin-left: 10px;">Selected: 0 / 10</span>
        </div>

        <!-- 3. Liste des cartes patients avec pastille de sélection -->
        <div class="patients-scroll-list">
            <?php foreach ($lesPatients as $p) : ?>
                <label class="white-card patient-select-card" 
                       data-id="<?= strtolower($p['patient_id']) ?>"
                       data-gender="<?= strtolower($p['gender'] ?? '') ?>"
                       data-side="<?= strtolower($p['lymphedema_side'] ?? '') ?>"
                       data-age="<?= htmlspecialchars($p['age'] ?? 0) ?>"
                       data-bmi="<?= htmlspecialchars($p['bmi'] ?? 0) ?>">
                    
                    <input type="checkbox" name="patients[]" value="<?= htmlspecialchars($p['patient_id']) ?>" class="patient-checkbox" onchange="updateSelectedCount()">
                    <div class="custom-radio-circle">
                        <svg class="check-icon" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"></polyline>
                        </svg>
                    </div>

                    <div class="patient-select-info">
                        <div class="patient-select-id">ID: <?= htmlspecialchars($p['patient_id']) ?></div>
                        <div class="patient-select-meta">
                            Age: <?= htmlspecialchars($p['age'] ?? '-') ?> • BMI: <?= htmlspecialchars($p['bmi'] ?? '-') ?> • <?= ucfirst(strtolower($p['gender'] ?? '-')) ?>
                        </div>
                    </div>
                </label>
            <?php endforeach; ?>
        </div>

        <!-- Boutons d'action -->
        <div class="actions-row">
            <button type="button" class="btn-action-light" id="btnToggleSelection" onclick="toggleSelectAll()">Select All</button>
            <button type="submit" class="btn-action-primary" id="btnShowResults" disabled>Show results</button>
        </div>
    </form>
</div>

<style>
.outcomes-container { 
    max-width: 580px; 
    margin: 15px auto 80px auto; 
    padding: 0 16px; 
    box-sizing: border-box; 
}
.outcomes-main-title { 
    text-align: center; 
    font-size: 19px; 
    font-weight: 700; 
    color: #1A202C; 
    margin: 0 0 16px 0; 
}

.search-box { 
    position: relative; 
    margin-bottom: 12px; 
}
.search-icon { 
    position: absolute; 
    left: 16px; 
    top: 50%; 
    transform: translateY(-50%); 
}
.search-box input {
    width: 100%; 
    height: 42px; 
    padding: 0 16px 0 42px; 
    border-radius: 22px;
    border: 1px solid #E2E8F0; 
    background: #FFFFFF; 
    font-size: 13px; 
    color: #2D3748; 
    outline: none; 
    box-sizing: border-box;
}

.filters-grid { 
    display: grid; 
    grid-template-columns: 1fr 1fr; 
    gap: 8px; 
    margin-bottom: 8px; 
}
.filter-col-4 { 
    display: contents; 
}
.filter-select {
    width: 100%; 
    height: 38px; 
    border-radius: 19px; 
    border: 1px solid #E2E8F0;
    background: #FFFFFF; 
    font-size: 12px; 
    color: #4A5568; 
    padding: 0 12px; 
    outline: none; 
    cursor: pointer;
}
.filter-select-sm {
    height: 34px; 
    border-radius: 17px; 
    border: 1px solid #E2E8F0;
    background: #FFFFFF; 
    font-size: 11.5px; 
    color: #4A5568; 
    padding: 0 10px; 
    outline: none; 
    cursor: pointer;
}

.counter-row { 
    text-align: right; 
    font-size: 11px; 
    color: #718096; 
    margin: 6px 4px 12px 0; 
}

.patients-scroll-list { 
    display: flex; 
    flex-direction: column; 
    gap: 10px; 
    margin-bottom: 20px; 
}
.patient-select-card {
    background: #FFFFFF; 
    border-radius: 16px; 
    padding: 14px 18px; 
    border: 1px solid #EBF0F7;
    display: flex; 
    align-items: center; 
    gap: 14px; 
    cursor: pointer; 
    user-select: none;
    box-shadow: 0 2px 6px rgba(0,0,0,0.02); 
    transition: border-color 0.2s;
}
.patient-checkbox { 
    display: none; 
}
.custom-radio-circle {
    width: 22px; 
    height: 22px; 
    border-radius: 50%; 
    border: 2px solid #CBD5E0;
    display: flex; 
    align-items: center; 
    justify-content: center; 
    flex-shrink: 0; 
    transition: all 0.2s;
}
.check-icon { 
    display: none; 
}
.patient-checkbox:checked + .custom-radio-circle {
    background-color: #5B7FDE; 
    border-color: #5B7FDE;
}
.patient-checkbox:checked + .custom-radio-circle .check-icon { 
    display: block; 
}

.patient-select-id { 
    font-size: 14.5px; 
    font-weight: 600; 
    color: #5B7FDE; 
}
.patient-select-meta { 
    font-size: 11.5px; 
    color: #718096; 
    margin-top: 2px; 
}

.actions-row { 
    display: flex; 
    justify-content: center; 
    gap: 14px; 
    margin-top: 14px; 
}
.btn-action-light {
    padding: 10px 22px; 
    border-radius: 12px; 
    background: #DDE7FE; 
    color: #4A6FE3;
    font-weight: 600; 
    font-size: 13px; 
    border: none; 
    cursor: pointer;
}
.btn-action-primary {
    padding: 10px 24px; 
    border-radius: 12px; 
    background: #5B7FDE; 
    color: #FFFFFF;
    font-weight: 600; 
    font-size: 13px; 
    border: none; 
    cursor: pointer;
}
.btn-action-primary:disabled { 
    background: #CBD5E0; 
    cursor: not-allowed; 
}
</style>

<script>
function filtrerListePatients() {
    const q = document.getElementById('filterSearch').value.toLowerCase().trim();
    const gender = document.getElementById('filterGender').value.toLowerCase();
    const side = document.getElementById('filterSide').value.toLowerCase();
    const ageRange = document.getElementById('filterAge').value;
    const bmiRange = document.getElementById('filterBmi').value;

    const cards = document.querySelectorAll('.patient-select-card');
    let visibleCount = 0;

    cards.forEach(card => {
        const id = card.getAttribute('data-id');
        const cGender = card.getAttribute('data-gender');
        const cSide = card.getAttribute('data-side');
        const cAge = parseFloat(card.getAttribute('data-age')) || 0;
        const cBmi = parseFloat(card.getAttribute('data-bmi')) || 0;

        let matches = true;
        if (q && !id.includes(q)) matches = false;
        if (gender !== 'all' && !cGender.includes(gender)) matches = false;
        if (side !== 'all' && !cSide.includes(side)) matches = false;

        if (ageRange !== 'All') {
            const [minA, maxA] = ageRange.split('-').map(Number);
            if (cAge < minA || cAge > maxA) matches = false;
        }
        if (bmiRange !== 'All') {
            const [minB, maxB] = bmiRange.split('-').map(Number);
            if (cBmi < minB || cBmi > maxB) matches = false;
        }

        card.style.display = matches ? 'flex' : 'none';
        if (matches) visibleCount++;
    });

    document.getElementById('patientsCount').innerText = `Patients: ${visibleCount}`;
}

function updateSelectedCount() {
    // On ne compte que les checkboxes cochées et visibles
    const allChecked = document.querySelectorAll('.patient-checkbox:checked');
    const count = allChecked.length;
    
    document.getElementById('selectedCount').innerText = `Selected: ${count} / 10`;
    document.getElementById('btnShowResults').disabled = (count === 0);

    // Bascule dynamique du texte du bouton selon la sélection
    const toggleBtn = document.getElementById('btnToggleSelection');
    if (count > 0) {
        toggleBtn.innerText = "Deselect All";
    } else {
        toggleBtn.innerText = "Select All";
    }

    // Gestion de la limite maximale à 10 patients
    if (count >= 10) {
        document.querySelectorAll('.patient-checkbox:not(:checked)').forEach(cb => cb.disabled = true);
    } else {
        document.querySelectorAll('.patient-checkbox').forEach(cb => cb.disabled = false);
    }
}

function toggleSelectAll() {
    const checked = document.querySelectorAll('.patient-checkbox:checked');
    const toggleBtn = document.getElementById('btnToggleSelection');

    if (checked.length > 0) {
        // Mode Deselect All : on décoche tout
        document.querySelectorAll('.patient-checkbox').forEach(cb => cb.checked = false);
    } else {
        // Mode Select All : on sélectionne les patients visibles (dans la limite de 10)
        const visibleCards = Array.from(document.querySelectorAll('.patient-select-card'))
                                  .filter(card => card.style.display !== 'none');
        
        let selectedCount = 0;
        visibleCards.forEach(card => {
            if (selectedCount < 10) {
                const cb = card.querySelector('.patient-checkbox');
                if (cb) {
                    cb.checked = true;
                    selectedCount++;
                }
            }
        });
    }

    // Rafraîchir les compteurs et l'intitulé du bouton
    updateSelectedCount();
}
</script>