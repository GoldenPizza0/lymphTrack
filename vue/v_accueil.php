<div class="container">
    <div class="header">
        <h1>Patients</h1>
        <div class="header-actions">
            <!-- Bouton Ajouter Patient (+) -->
            <a href="index.php?uc=gererPatients&action=formulaireAjoutPatient" class="btn-circle" id="btnAddPatient" title="Add Patient">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
            </a>

            <!-- Bouton Export / Annuler -->
            <button type="button" class="btn-circle" id="btnToggleExport" onclick="toggleExportMode()" title="Export Mode">
                <svg id="iconDownload" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                <svg id="iconClose" style="display:none;" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>

    <!-- Filtres & Barre de Recherche -->
    <form id="filterForm" method="GET" action="index.php">
        <input type="hidden" name="uc" value="gererPatients">
        <input type="hidden" name="action" value="afficherPatients">

        <div class="search-container">
            <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" name="search" class="search-input" placeholder="Search by Patient ID..." value="<?= htmlspecialchars($search ?? '') ?>" onchange="document.getElementById('filterForm').submit();">
        </div>

        <div class="filter-row">
            <div class="select-wrapper">
                <select name="side" class="filter-select" onchange="document.getElementById('filterForm').submit();">
                    <option value="All" <?= (($side ?? 'All') === 'All') ? 'selected' : '' ?>>All sides</option>
                    <option value="Left" <?= (($side ?? '') === 'Left') ? 'selected' : '' ?>>Left</option>
                    <option value="Right" <?= (($side ?? '') === 'Right') ? 'selected' : '' ?>>Right</option>
                    <option value="Both" <?= (($side ?? '') === 'Both') ? 'selected' : '' ?>>Both</option>
                    <option value="Unknown" <?= (($side ?? '') === 'Unknown') ? 'selected' : '' ?>>Unknown</option>
                </select>
                <span class="select-arrow">▼</span>
            </div>

            <div class="select-wrapper">
                <select name="gender" class="filter-select" onchange="document.getElementById('filterForm').submit();">
                    <option value="All" <?= (($gender ?? 'All') === 'All') ? 'selected' : '' ?>>All gender</option>
                    <option value="Female" <?= (($gender ?? '') === 'Female') ? 'selected' : '' ?>>Female</option>
                    <option value="Male" <?= (($gender ?? '') === 'Male') ? 'selected' : '' ?>>Male</option>
                    <option value="Unknown" <?= (($gender ?? '') === 'Unknown') ? 'selected' : '' ?>>Unknown</option>
                </select>
                <span class="select-arrow">▼</span>
            </div>
        </div>
    </form>

    <div class="count-text">Patients: <?= $nbPatients ?? 0 ?></div>

    <!-- Formulaire d'export englobant -->
    <form id="exportForm" method="POST" action="index.php?uc=gererPatients&action=exporterPatients">
        <?php if (!empty($lesPatients)) : ?>
            <?php foreach ($lesPatients as $p) : ?>
                <div class="patient-card" id="card_<?= $p['patient_id'] ?>" onclick="handleCardClick('<?= $p['patient_id'] ?>')">
                    <div class="patient-card-left">
                        <h2>ID: <?= htmlspecialchars($p['patient_id']) ?></h2>
                        <div class="patient-side">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="10" r="3"></circle>
                                <path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path>
                            </svg>
                            <span>Lymphedema side: <?= ucfirst(strtolower($p['lymphedema_side'] ?? 'Unknown')) ?></span>
                        </div>
                    </div>
                    
                    <div class="patient-card-right">
                        <div class="patient-meta">
                            <?= htmlspecialchars($p['age'] ?? '-') ?>y • <?= ucfirst(strtolower($p['gender'] ?? '-')) ?><br>
                            BMI: <?= htmlspecialchars($p['bmi'] ?? '-') ?>
                        </div>

                        <a href="index.php?uc=gererPatients&action=supprimerPatient&id=<?= urlencode($p['patient_id']) ?>" 
                           class="btn-delete" 
                           onclick="event.stopPropagation(); return confirm('Do you really want to delete patient <?= htmlspecialchars($p['patient_id']) ?>?');"
                           title="Delete">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="3 6 5 6 21 6"></polyline>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            </svg>
                        </a>

                        <div class="check-circle" style="display:none;" id="check_<?= $p['patient_id'] ?>">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </div>
                        <input type="checkbox" name="patients_ids[]" value="<?= $p['patient_id'] ?>" id="input_<?= $p['patient_id'] ?>" style="display:none;">
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="export-floating-bar" id="exportActionsBar" style="display: none;">
                <button type="button" class="btn-select-all" onclick="toggleSelectAll()">Select all</button>
                <button type="submit" class="btn-export-confirm" id="btnExportSubmit" disabled>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    <span id="exportText">Export (0)</span>
                </button>
            </div>
        <?php else : ?>
            <p style="text-align: center; color: #718096; margin-top: 40px;">No patient found matching your criteria.</p>
        <?php endif; ?>
    </form>
</div>

<style>
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 28px;
}
.header h1 {
    font-size: 26px;
    font-weight: 700;
    margin: 0;
    color: #1A202C;
}
.header-actions {
    display: flex;
    gap: 12px;
}
.btn-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background-color: #5B7FDE;
    color: #FFFFFF;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    border: none;
    cursor: pointer;
    text-decoration: none;
    box-shadow: 0 4px 10px rgba(91, 127, 222, 0.3);
    transition: background-color 0.2s;
}
.btn-circle:hover { 
    background-color: #486AC4; 
}
.btn-circle.mode-close { 
    background-color: #E53E3E; 
    box-shadow: 0 4px 10px rgba(229, 62, 62, 0.3); 
}
.btn-circle.mode-close:hover { 
    background-color: #C53030; 
}

.search-container { 
    position: relative; 
    margin-bottom: 14px; 
}
.search-icon { 
    position: absolute; 
    left: 16px; 
    top: 50%; 
    transform: translateY(-50%); 
    color: #8C98A9; 
}
.search-input {
    width: 100%;
    height: 44px;
    padding-left: 44px;
    padding-right: 16px;
    border-radius: 22px;
    border: 1px solid #E2E8F0;
    background-color: #FFFFFF;
    font-size: 14px;
    outline: none;
    color: #2D3748;
}
.filter-row { 
    display: flex; 
    gap: 14px; 
    margin-bottom: 12px; 
}
.select-wrapper { 
    flex: 1; 
    position: relative; 
}
.filter-select {
    width: 100%;
    height: 44px;
    padding: 0 36px 0 18px;
    border-radius: 22px;
    border: 1px solid #E2E8F0;
    background-color: #FFFFFF;
    font-size: 14px;
    color: #4A5568;
    appearance: none;
    outline: none;
    cursor: pointer;
}
.select-arrow { 
    position: absolute; 
    right: 16px; 
    top: 50%; 
    transform: translateY(-50%); 
    pointer-events: none; 
    color: #4A5568; 
    font-size: 10px; 
}
.count-text { 
    text-align: right; 
    font-size: 12px; 
    font-style: italic; 
    color: #5B7FDE; 
    margin-bottom: 16px; 
    padding-right: 4px; 
}

.patient-card {
    background: #FFFFFF;
    border-radius: 18px;
    padding: 20px 24px;
    margin-bottom: 14px;
    border: 1px solid #EDF2F7;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    transition: background-color 0.2s, border-color 0.2s;
}
.patient-card.selected { 
    background-color: #EDF2FF; 
    border-color: #BAC8FF; 
}
.patient-card-left h2 { 
    font-size: 17px; 
    color: #4361EE; 
    margin: 0 0 10px 0; 
    font-weight: 600; 
}
.patient-side { 
    display: flex; 
    align-items: center; 
    gap: 6px; 
    font-size: 13px; 
    color: #4A5568; 
}
.patient-card-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    justify-content: space-between;
    min-height: 56px;
    gap: 8px;
}
.patient-meta { 
    font-size: 13px; 
    color: #718096; 
    text-align: right; 
}
.btn-delete { 
    background: none; 
    border: none; 
    cursor: pointer; 
    color: #7A889B; 
    padding: 4px; 
    display: flex; 
    align-items: center; 
}
.btn-delete:hover { 
    color: #E53E3E; 
}

.check-circle {
    width: 22px;
    height: 22px;
    min-width: 22px;
    min-height: 22px;
    flex-shrink: 0;
    border-radius: 50%;
    border: 2px solid #CBD5E0;
    background-color: #FFFFFF;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    transition: all 0.2s;
}
.check-circle svg { 
    display: none; 
}
.patient-card.selected .check-circle { 
    background-color: #5B7FDE; 
    border-color: #5B7FDE; 
}
.patient-card.selected .check-circle svg { 
    display: block; 
}

.export-floating-bar { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-top: 24px; 
    padding: 0 4px; 
}
.btn-select-all { 
    background: #FFFFFF; 
    border: 1px solid #E2E8F0; 
    border-radius: 8px; 
    padding: 8px 18px; 
    font-size: 13px; 
    font-weight: 500; 
    color: #4A5568; 
    cursor: pointer; 
}
.btn-export-confirm { 
    background-color: #5B7FDE; 
    color: #FFFFFF; 
    border: none; 
    border-radius: 10px; 
    padding: 10px 22px; 
    font-size: 14px; 
    font-weight: 600; 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    cursor: pointer; 
    box-shadow: 0 4px 10px rgba(91, 127, 222, 0.3); 
}
.btn-export-confirm:disabled { 
    background-color: #CBD5E0; 
    box-shadow: none; 
    cursor: not-allowed; 
}
</style>

<script>
let exportModeActive = false;

function toggleExportMode() {
    exportModeActive = !exportModeActive;
    
    const btnToggle = document.getElementById('btnToggleExport');
    const iconDownload = document.getElementById('iconDownload');
    const iconClose = document.getElementById('iconClose');
    const btnAdd = document.getElementById('btnAddPatient');
    const exportBar = document.getElementById('exportActionsBar');
    const cards = document.querySelectorAll('.patient-card');
    const deleteBtns = document.querySelectorAll('.btn-delete');
    const checkCircles = document.querySelectorAll('.check-circle');

    if (exportModeActive) {
        btnToggle.classList.add('mode-close');
        iconDownload.style.display = 'none';
        iconClose.style.display = 'block';
        btnAdd.style.display = 'none';
        exportBar.style.display = 'flex';

        deleteBtns.forEach(b => b.style.display = 'none');
        checkCircles.forEach(c => c.style.display = 'flex');
    } else {
        btnToggle.classList.remove('mode-close');
        iconDownload.style.display = 'block';
        iconClose.style.display = 'none';
        btnAdd.style.display = 'inline-flex';
        exportBar.style.display = 'none';

        cards.forEach(c => c.classList.remove('selected'));
        document.querySelectorAll("input[name='patients_ids[]']").forEach(i => i.checked = false);
        deleteBtns.forEach(b => b.style.display = 'flex');
        checkCircles.forEach(c => c.style.display = 'none');
        updateExportCount();
    }
}

function handleCardClick(patientId) {
    if (!exportModeActive) {
        window.location.href = 'index.php?uc=gererPatients&action=voirProfilPatient&id=' + encodeURIComponent(patientId);
    } else {
        const card = document.getElementById('card_' + patientId);
        const input = document.getElementById('input_' + patientId);
        input.checked = !input.checked;
        if (input.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
        updateExportCount();
    }
}

function toggleSelectAll() {
    const inputs = document.querySelectorAll("input[name='patients_ids[]']");
    const allChecked = Array.from(inputs).every(i => i.checked);

    inputs.forEach(input => {
        input.checked = !allChecked;
        const card = document.getElementById('card_' + input.value);
        if (!allChecked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }
    });
    updateExportCount();
}

function updateExportCount() {
    const checkedCount = document.querySelectorAll("input[name='patients_ids[]']:checked").length;
    const btnSubmit = document.getElementById('btnExportSubmit');
    const textSpan = document.getElementById('exportText');

    textSpan.innerText = `Export (${checkedCount})`;
    btnSubmit.disabled = (checkedCount === 0);
}
</script>