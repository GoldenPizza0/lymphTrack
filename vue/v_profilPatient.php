<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="profile-container">
    <!-- Barre supérieure -->
    <div class="top-nav-bar">
        <a href="index.php?uc=gererPatients&action=afficherPatients" class="back-link" title="Back to patients">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title">Patient: <?= htmlspecialchars($patient['patient_id']) ?></h1>
        <form method="POST" action="index.php?uc=gererPatients&action=exporterPatients" style="margin: 0;">
            <input type="hidden" name="patients_ids[]" value="<?= htmlspecialchars($patient['patient_id']) ?>">
            <button type="submit" class="btn-circle-action" title="Export Patient Folder">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
            </button>
        </form>
    </div>

    <!-- 1. Patient Information Card -->
    <div class="white-card patient-info-card">
        <div class="card-row-between">
            <h2 class="patient-id-green">ID: <?= htmlspecialchars($patient['patient_id']) ?></h2>
            <div class="patient-demographics-text">
                <?= htmlspecialchars($patient['age'] ?? '-') ?>y • <?= ucfirst(strtolower($patient['gender'] ?? '-')) ?><br>
                BMI: <?= htmlspecialchars($patient['bmi'] ?? '-') ?>
            </div>
        </div>
        <div class="info-row">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><circle cx="12" cy="10" r="3"></circle><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"></path></svg>
            <span>Lymphedema side: <?= ucfirst(strtolower($patient['lymphedema_side'] ?? 'Unknown')) ?></span>
        </div>
        <div class="info-row">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>
            <span>Notes: <?= !empty($patient['notes']) ? htmlspecialchars($patient['notes']) : 'No notes available for this patient' ?></span>
        </div>
    </div>

    <!-- 2. Follow-up Timeline -->
    <div class="white-card follow-up-card">
        <h2 class="blue-section-title">Follow-up</h2>
        <div class="timeline-container">
            <div class="timeline-bar"></div>
            <div class="timeline-items">
                <?php if (!empty($visites)) : ?>
                    <?php foreach ($visites as $v) : ?>
                        <div class="timeline-row">
                            <div class="timeline-dot"></div>
                            <a href="index.php?uc=gererFollowup&action=voirVisite&op_id=<?= $v['id'] ?>" class="visit-capsule" style="text-decoration: none; cursor: pointer;">
                                <span class="visit-name"><?= htmlspecialchars($v['name']) ?></span>
                                <span class="visit-date"><?= date('d/m/Y', strtotime($v['operation_date'])) ?></span>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p style="color: #A0AEC0; font-size: 13px; text-align: center;">No follow-up visits recorded yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <a href="index.php?uc=gererFollowup&action=formulaireAjout&patient_id=<?= urlencode($patient['patient_id']) ?>" class="btn-add-followup">Add Follow-up</a>

    <!-- 3. Outcomes Selector -->
    <div class="outcomes-header-block">
        <h2 class="blue-section-title" style="margin-bottom: 4px;">Outcomes</h2>
        <p class="section-instruction">Select a position to visualize its evolution</p>

        <div class="positions-row">
            <button type="button" class="pos-circle active" id="btnPos_0" onclick="updatePositionView(0)">All</button>
            <?php for ($i = 1; $i <= 6; $i++) : ?>
                <button type="button" class="pos-circle" id="btnPos_<?= $i ?>" onclick="updatePositionView(<?= $i ?>)"><?= $i ?></button>
            <?php endfor; ?>
        </div>
    </div>

    <!-- VUE A : Grille pour "All" (6 mini-graphiques) -->
    <div class="white-card all-positions-card" id="allViewContainer">
        <h3 class="graph-subtitle">All positions (1 to 6) across visits</h3>
        
        <!-- Légende globale PreOP / 1 year -->
        <div class="global-visits-legend" id="globalLegend">
            <span class="legend-badge"><span class="badge-square" style="background:#C53030;"></span> PreOP</span>
            <span class="legend-badge"><span class="badge-square" style="background:#1E3A8A;"></span> 1 year</span>
        </div>

        <div class="positions-grid-2col">
            <?php for ($p = 1; $p <= 6; $p++) : ?>
                <div class="mini-chart-card">
                    <div class="mini-chart-header">Position <?= $p ?></div>
                    <div class="mini-canvas-box">
                        <canvas id="miniChart_<?= $p ?>"></canvas>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>

    <!-- VUE B : Graphique unique pour position 1 à 6 + Tableau -->
    <div id="singleViewContainer" style="display: none;">
        <div class="white-card graph-card">
            <h3 class="graph-subtitle" id="singleGraphTitle">Evolution of Position 1 across visits</h3>
            <div class="chart-wrapper">
                <canvas id="singlePositionChart"></canvas>
            </div>
        </div>

        <div class="graph-export-buttons">
            <button type="button" class="btn-pill-export" onclick="downloadCSV()">Export CSV</button>
            <button type="button" class="btn-pill-export" onclick="downloadPNG()">Export PNG</button>
        </div>

        <div class="white-card metrics-card">
            <h3 class="metrics-title">Visit Metrics</h3>
            <table class="metrics-table">
                <thead>
                    <tr>
                        <th>Visit</th>
                        <th>f<sub>0</sub></th>
                        <th>Min S11</th>
                        <th>BW</th>
                    </tr>
                </thead>
                <tbody id="metricsTableBody"></tbody>
            </table>
        </div>
    </div>
</div>

<style>
.profile-container { 
    max-width: 680px;
    margin: 15px auto 50px auto; 
    padding: 0 16px; 
    box-sizing: border-box; 
}
.top-nav-bar { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    position: relative; 
    margin-bottom: 18px; 
}
.back-link { 
    display: flex; 
    align-items: center; 
    text-decoration: none; 
}
.nav-title { 
    position: absolute; 
    left: 50%; 
    transform: translateX(-50%); 
    font-size: 17px; 
    font-weight: 600; 
    color: #1A202C; 
    margin: 0; 
}
.btn-circle-action { 
    width: 36px; 
    height: 36px; 
    border-radius: 50%; 
    background-color: #5B7FDE; 
    color: #FFFFFF; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    border: none; 
    cursor: pointer; 
    box-shadow: 0 3px 8px rgba(91, 127, 222, 0.3); 
}

.white-card { 
    background: #FFFFFF; 
    border-radius: 18px; 
    padding: 20px 24px; 
    margin-bottom: 14px; 
    border: 1px solid #EBF0F7; 
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); 
}
.blue-section-title { 
    text-align: center; 
    font-size: 16px; 
    font-weight: 700; 
    color: #4A6FE3; 
    margin: 0 0 14px 0; 
}

.card-row-between { 
    display: flex; 
    justify-content: space-between; 
    align-items: flex-start; 
    margin-bottom: 10px; 
}
.patient-id-green { 
    font-size: 16px; 
    font-weight: 700; 
    color: #2F855A; 
    margin: 0; 
}
.patient-demographics-text { 
    text-align: right; 
    font-size: 12px; 
    color: #718096; 
    line-height: 1.4; }
.info-row { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    font-size: 12.5px; 
    color: #4A5568; 
    margin-top: 6px; 
}

.timeline-container { 
    position: relative; 
    padding-left: 20px; 
}
.timeline-bar { 
    position: absolute; 
    left: 4px; 
    top: 14px; 
    bottom: 14px; 
    width: 2px; 
    background-color: #5B7FDE; 
}
.timeline-items { 
    display: flex; 
    flex-direction: column; 
    gap: 12px; 
}
.timeline-row { 
    position: relative; 
    display: flex; 
    align-items: center; 
}
.timeline-dot { 
    position: absolute; 
    left: -20px; 
    width: 10px; 
    height: 10px; 
    border-radius: 50%; 
    background-color: #5B7FDE; 
}
.visit-capsule { 
    flex: 1; 
    background-color: #FFFFFF; 
    border: 1px solid #E2E8F0; 
    border-radius: 12px; 
    padding: 10px 18px; 
    display: flex; 
    flex-direction: column; 
}
.visit-name { 
    font-size: 13px; 
    font-weight: 600; 
    color: #2D3748; 
}
.visit-date { 
    font-size: 11px; 
    color: #A0AEC0; 
    margin-top: 2px; 
}

.btn-add-followup { 
    display: block; 
    width: 100%; 
    padding: 11px 0; 
    text-align: center; 
    background-color: #DDE7FE; 
    color: #4A6FE3; 
    font-weight: 600; 
    font-size: 13px; 
    border-radius: 12px; 
    text-decoration: none; 
    margin-bottom: 22px; 
    transition: background-color 0.2s; 
}
.btn-add-followup:hover { 
    background-color: #CBDCFC; 
}

.outcomes-header-block { 
    text-align: center; 
    margin-bottom: 16px; 
}
.section-instruction { 
    font-size: 11px; 
    color: #A0AEC0; 
    margin: 0 0 14px 0; 
}
.positions-row { 
    display: flex; 
    justify-content: center; 
    gap: 8px; 
}
.pos-circle { 
    width: 30px; 
    height: 30px; 
    border-radius: 50%; 
    border: none; 
    background-color: #A0AEC0; 
    color: #FFFFFF; 
    font-size: 12px; 
    font-weight: 600; 
    cursor: pointer; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    transition: background-color 0.2s; 
}
.pos-circle.active { 
    background-color: #4A6FE3; 
}

/* Grille 2 colonnes pour "All" */
.all-positions-card {
    padding: 20px 16px;
    box-sizing: border-box;
    overflow: hidden; /* Empêche tout débordement visuel hors de la carte */
}
.graph-subtitle { 
    text-align: center; 
    font-size: 13.5px; 
    font-weight: 600; 
    color: #4A6FE3; 
    margin: 0 0 14px 0; 
}
.global-visits-legend { 
    display: flex; 
    align-items: center; 
    gap: 16px; 
    margin-bottom: 14px; 
    padding-left: 8px; 
}
.legend-badge { 
    display: inline-flex; 
    align-items: center; 
    gap: 6px; 
    font-size: 11.5px; 
    color: #4A5568; 
}
.badge-square { 
    width: 12px; 
    height: 12px; 
    border-radius: 3px; 
    display: inline-block; 
}

.positions-grid-2col {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr)); /* minmax(0, 1fr) est la clé pour empêcher Chart.js de pousser la grille */
    gap: 12px;
    width: 100%;
    box-sizing: border-box;
}
.mini-chart-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 12px;
    padding: 10px 8px 6px 8px;
    min-width: 0; /* Oblige la sous-carte à respecter la colonne */
    box-sizing: border-box;
}
.mini-chart-header {
    font-size: 11.5px;
    font-weight: 600;
    color: #4A6FE3;
    margin-bottom: 4px;
}
.mini-canvas-box {
    position: relative;
    height: 135px;
    width: 100%;
    min-width: 0;
}

/* Vue position unique */
.graph-card { 
    padding: 18px 20px 14px 20px; 
}
.chart-wrapper { 
    position: relative; 
    height: 260px; 
    width: 100%; 
}
.graph-export-buttons { 
    display: flex; 
    justify-content: center; 
    gap: 14px; margin: 16px 0 20px 0; 
}
.btn-pill-export {
    background-color: #DDE7FE; 
    color: #4A6FE3; 
    border: none; 
    border-radius: 8px; 
    padding: 8px 18px; 
    font-size: 12px; 
    font-weight: 600; 
    cursor: pointer; 
    transition: background-color 0.2s; 
}
.btn-pill-export:hover { 
    background-color: #CBDCFC; 
}

.metrics-card { 
    padding: 18px 20px; 
}
.metrics-title { 
    text-align: center; 
    font-size: 13px; 
    font-weight: 700; 
    color: #4A6FE3; 
    margin: 0 0 14px 0; 
}
.metrics-table { 
    width: 100%; 
    border-collapse: collapse; 
    font-size: 12px; 
}
.metrics-table th { 
    text-align: left; 
    color: #718096; 
    font-weight: 600; 
    padding-bottom: 10px; 
    border-bottom: 1px solid #EDF2F7; 
}
.metrics-table td { 
    padding: 8px 0; 
    color: #2D3748; 
}

@media (max-width: 540px) {
    .positions-grid-2col { grid-template-columns: 1fr; }
}
</style>

<script>
// 1. Données issues de la BDD
const allRawMetrics = <?= json_encode($toutesMetriques ?? []) ?>;

// 2. Axe de fréquences
const rawFreqs = [];
for (let f = 0.10; f <= 3.00; f += 0.04) {
    rawFreqs.push(Number(f.toFixed(2)));
}

// 3. Modèle lorentzien
function genererSpectreS11(f0_hz, minS11_db, bw_hz, freqAxisGhz) {
    const f0_ghz = f0_hz / 1e9;
    const bw_ghz = (bw_hz > 0) ? (bw_hz / 1e9) : 0.25;
    const baseline = -0.5;

    return freqAxisGhz.map(f => {
        const denom = 1 + Math.pow((2 * (f - f0_ghz)) / bw_ghz, 2);
        const lorentz = baseline + (minS11_db - baseline) / denom;
        const ripple = 0.18 * Math.sin(f * 16 * Math.PI);
        return Number((lorentz + ripple).toFixed(2));
    });
}

const visitColors = { 'PreOP': '#C53030', '1 year': '#1E3A8A' };
const defaultPalette = ['#C53030', '#1E3A8A', '#38A169', '#DD6B20', '#805AD5'];

// Référence des 6 mini-graphiques et du graphique principal
const miniCharts = {};
let singleChart = null;
let currentPosition = 0;

// Configuration compacte pour les 6 mini-graphiques
function getMiniChartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: {
                grid: { color: '#F7FAFC' },
                ticks: { font: { size: 7.5 }, maxTicksLimit: 6 }
            },
            y: {
                min: -20,
                max: 0,
                ticks: { stepSize: 5, font: { size: 7.5 } },
                grid: { color: '#F7FAFC' }
            }
        }
    };
}

// Initialisation des 6 mini-graphes
for (let p = 1; p <= 6; p++) {
    const canvas = document.getElementById('miniChart_' + p);
    if (canvas) {
        const pMetrics = allRawMetrics.filter(m => parseInt(m.position) === p);
        const datasets = [];

        pMetrics.forEach((m, idx) => {
            datasets.push({
                label: m.visit_name,
                data: genererSpectreS11(parseFloat(m.fo), parseFloat(m.min_s11), parseFloat(m.bw), rawFreqs),
                borderColor: visitColors[m.visit_name] || defaultPalette[idx % defaultPalette.length],
                borderWidth: 1.5,
                pointRadius: 0,
                tension: 0.25
            });
        });

        miniCharts[p] = new Chart(canvas.getContext('2d'), {
            type: 'line',
            data: { labels: rawFreqs.map(f => f.toFixed(2)), datasets: datasets },
            options: getMiniChartOptions()
        });
    }
}

// Initialisation du graphique unique
const singleCtx = document.getElementById('singlePositionChart').getContext('2d');
singleChart = new Chart(singleCtx, {
    type: 'line',
    data: { labels: rawFreqs.map(f => f.toFixed(2)), datasets: [] },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: { boxWidth: 12, font: { size: 11 } }
            }
        },
        scales: {
            x: { title: { display: true, text: 'Frequency (GHz)', font: { size: 9.5 } }, grid: { color: '#F0F4F8' }, ticks: { font: { size: 8.5 }, maxTicksLimit: 10 } },
            y: { title: { display: true, text: 'Return Loss (dB)', font: { size: 9.5 } }, min: -22, max: 0, ticks: { stepSize: 5, font: { size: 8.5 } }, grid: { color: '#F0F4F8' } }
        }
    }
});

// 4. Bascule instantanée entre la vue All et la vue détaillée
function updatePositionView(pos) {
    currentPosition = pos;

    // Mise à jour de l'état actif des pastilles
    for (let i = 0; i <= 6; i++) {
        const b = document.getElementById('btnPos_' + i);
        if (b) b.classList.toggle('active', i === pos);
    }

    const allContainer = document.getElementById('allViewContainer');
    const singleContainer = document.getElementById('singleViewContainer');

    if (pos === 0) {
        // Mode "All" : affichage de la grille de 6 graphes
        allContainer.style.display = 'block';
        singleContainer.style.display = 'none';
        
        // Redimensionnement pour assurer le rendu net des canvas
        Object.values(miniCharts).forEach(chart => chart.resize());
    } else {
        // Mode Position Unique (1 à 6)
        allContainer.style.display = 'none';
        singleContainer.style.display = 'block';

        document.getElementById('singleGraphTitle').innerText = `Evolution of Position ${pos} across visits`;

        const targetMetrics = allRawMetrics.filter(m => parseInt(m.position) === pos);
        const datasets = [];

        if (targetMetrics.length > 0) {
            targetMetrics.forEach((m, idx) => {
                const color = visitColors[m.visit_name] || defaultPalette[idx % defaultPalette.length];
                datasets.push({
                    label: m.visit_name,
                    data: genererSpectreS11(parseFloat(m.fo), parseFloat(m.min_s11), parseFloat(m.bw), rawFreqs),
                    borderColor: color,
                    borderWidth: 1.8,
                    pointRadius: 0,
                    tension: 0.25
                });
            });
        } else {
            datasets.push({
                label: 'No data recorded',
                data: rawFreqs.map(() => -0.5),
                borderColor: '#CBD5E0',
                borderWidth: 1.5,
                borderDash: [5, 5],
                pointRadius: 0
            });
        }

        singleChart.data.datasets = datasets;
        singleChart.update();
        singleChart.resize();

        // Remplissage du tableau Visit Metrics
        const tbody = document.getElementById('metricsTableBody');
        tbody.innerHTML = '';
        if (targetMetrics.length > 0) {
            targetMetrics.forEach(m => {
                const tr = document.createElement('tr');
                const color = visitColors[m.visit_name] || '#2D3748';
                tr.innerHTML = `
                    <td style="color: ${color}; font-weight: 600;">${m.visit_name}</td>
                    <td>${(parseFloat(m.fo) / 1e9).toFixed(3)} GHz</td>
                    <td>${parseFloat(m.min_s11).toFixed(2)} dB</td>
                    <td>${(parseFloat(m.bw) / 1e6).toFixed(2)} MHz</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="4" style="text-align:center; color:#A0AEC0;">No metrics recorded for position ${pos}</td></tr>`;
        }
    }
}

// Démarrage par défaut sur "All"
updatePositionView(0);

function downloadPNG() {
    const a = document.createElement('a');
    a.download = `Patient_<?= $patient['patient_id'] ?>_Position_${currentPosition}.png`;
    a.href = singleChart.toBase64Image();
    a.click();
}

function downloadCSV() {
    const targetMetrics = allRawMetrics.filter(m => parseInt(m.position) === currentPosition);
    let csv = "Frequency (GHz)";
    singleChart.data.datasets.forEach(d => { csv += ";" + d.label + " (dB)"; });
    csv += "\n";

    for (let i = 0; i < rawFreqs.length; i++) {
        csv += rawFreqs[i];
        singleChart.data.datasets.forEach(d => { csv += ";" + d.data[i]; });
        csv += "\n";
    }
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `Patient_<?= $patient['patient_id'] ?>_Position_${currentPosition}.csv`;
    link.click();
}
</script>