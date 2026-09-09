<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="visit-container">
    <!-- En-tête -->
    <div class="top-nav-bar">
        <a href="index.php?uc=gererPatients&action=voirProfilPatient&id=<?= urlencode($operation['patient_id']) ?>" class="back-link" title="Back to patient">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title">Visit patient : <?= htmlspecialchars($operation['patient_id']) ?></h1>
        <form method="POST" action="index.php?uc=gererPatients&action=exporterPatients" style="margin: 0;">
            <input type="hidden" name="patients_ids[]" value="<?= htmlspecialchars($operation['patient_id']) ?>">
            <button type="submit" class="btn-circle-action" title="Export Visit Folder">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
            </button>
        </form>
    </div>

    <!-- 1. Visit Information Card -->
    <div class="white-card visit-info-card">
        <div class="card-row-between">
            <div class="visit-title-blue">Visit name : <?= htmlspecialchars($operation['name']) ?></div>
            <div class="visit-date-badge">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                <?= date('d/m/Y', strtotime($operation['operation_date'])) ?>
            </div>
        </div>
        <div class="info-row">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>
            <span>Notes: <?= !empty($operation['notes']) ? htmlspecialchars($operation['notes']) : 'No notes available for this visit' ?></span>
        </div>
    </div>

    <!-- 2. Position Section avec silhouette humaine interactive -->
    <div class="white-card position-interactive-card">
        <h2 class="blue-section-title">Positions</h2>
        <div class="body-canvas-wrapper">
            <!-- Silhouette SVG -->
            <svg class="body-silhouette" viewBox="0 0 200 240" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="100" cy="35" r="18" fill="#FEEBC8"/>
                <!-- Ligne droite continue du buste aux pieds sans décalage + pieds arrondis -->
                <path d="M85 58C70 60 55 70 50 85L40 140C38 148 44 155 52 153C58 152 62 146 64 140L72 105V220a11 11 0 0 0 22 0V160H106V220a11 11 0 0 0 22 0V105L136 140C138 146 142 152 148 153C156 155 162 148 160 140L150 85C145 70 130 60 115 58H85Z" fill="#FEEBC8"/>
            </svg>

            <!-- Boutons circulaires des 6 positions -->
            <!-- Positions 1, 2, 3 (Bras Droit) -->
            <a href="index.php?uc=gererMesures&action=voirPosition&op_id=<?= $operation['id'] ?>&pos=3" class="pos-dot dot-p3" title="Position 3">3</a>
            <a href="index.php?uc=gererMesures&action=voirPosition&op_id=<?= $operation['id'] ?>&pos=2" class="pos-dot dot-p2" title="Position 2">2</a>
            <a href="index.php?uc=gererMesures&action=voirPosition&op_id=<?= $operation['id'] ?>&pos=1" class="pos-dot dot-p1" title="Position 1">1</a>

            <!-- Positions 6, 5, 4 (Bras Gauche) -->
            <a href="index.php?uc=gererMesures&action=voirPosition&op_id=<?= $operation['id'] ?>&pos=6" class="pos-dot dot-p6" title="Position 6">6</a>
            <a href="index.php?uc=gererMesures&action=voirPosition&op_id=<?= $operation['id'] ?>&pos=5" class="pos-dot dot-p5" title="Position 5">5</a>
            <a href="index.php?uc=gererMesures&action=voirPosition&op_id=<?= $operation['id'] ?>&pos=4" class="pos-dot dot-p4" title="Position 4">4</a>
        </div>
    </div>

    <!-- Bouton Import all results -->
    <a href="index.php?uc=gererMesures&action=importerToutesMesures&op_id=<?= urlencode($operation['id']) ?>" class="btn-import-all" 
        onclick="return confirm('Import full set of 18 measurements (3 per position) for this visit?');">Import all results
    </a>

    <!-- 3. Photos Section -->
    <div class="white-card photos-card">
        <div class="card-row-between">
            <span class="photos-count-title">Photos (<?= $nbPhotos ?>)</span>
            <a href="index.php?uc=gererFollowup&action=formulaireAjout&patient_id=<?= urlencode($operation['patient_id']) ?>" class="btn-add-photo">+ Add photo</a>
        </div>
    </div>

    <!-- Bouton Export all photos -->
    <div class="center-btn-row">
        <a href="index.php?uc=gererFollowup&action=exporterPhotosVisite&op_id=<?= $operation['id'] ?>" class="btn-export-photos">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            Export all photos
        </a>
    </div>

    <!-- 4. Outcomes Section -->
    <div class="outcomes-header-block">
        <h2 class="blue-section-title" style="margin-bottom: 12px;">Outcomes</h2>
    </div>

    <div class="white-card graph-card">
        <h3 class="graph-subtitle">Comparison graph of measurements</h3>
        <div class="chart-wrapper">
            <canvas id="comparisonChart"></canvas>
        </div>
        <div class="chart-multi-legend">
            <span class="legend-item"><span class="legend-line" style="background:#C53030;"></span> Position 1</span>
            <span class="legend-item"><span class="legend-line" style="background:#2B6CB0;"></span> Position 2</span>
            <span class="legend-item"><span class="legend-line" style="background:#38A169;"></span> Position 3</span>
            <span class="legend-item"><span class="legend-line" style="background:#DD6B20;"></span> Position 4</span>
            <span class="legend-item"><span class="legend-line" style="background:#D53F8C;"></span> Position 5</span>
            <span class="legend-item"><span class="legend-line" style="background:#ECC94B;"></span> Position 6</span>
        </div>
    </div>
</div>

<style>
.visit-container { 
    max-width: 640px; 
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
    font-size: 16px; 
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
.card-row-between { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
}
.visit-title-blue { 
    font-size: 15px; 
    font-weight: 600; 
    color: #4A6FE3; 
}
.visit-date-badge { 
    display: flex; 
    align-items: center; 
    gap: 6px; 
    font-size: 12px; 
    color: #718096; 
}
.info-row { 
    display: flex; 
    align-items: center; 
    gap: 8px; 
    font-size: 12.5px; 
    color: #4A5568; 
    margin-top: 8px; 
}
.blue-section-title { 
    text-align: center; 
    font-size: 16px; 
    font-weight: 700; 
    color: #4A6FE3; 
    margin: 0 0 14px 0; 
}

.position-interactive-card { 
    padding: 20px 20px 30px 20px; 
}
.body-canvas-wrapper { 
    position: relative; 
    max-width: 220px; 
    height: 240px; 
    margin: 0 auto; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
}
.body-silhouette { 
    width: 100%; 
    height: 100%; 
}
.pos-dot { 
    position: absolute; 
    width: 24px; 
    height: 24px; 
    border-radius: 50%; 
    background-color: #5B7FDE; 
    color: #FFFFFF; 
    font-size: 11px; 
    font-weight: 700; 
    display: flex; 
    justify-content: center; 
    align-items: center; 
    text-decoration: none; 
    box-shadow: 0 2px 6px rgba(91, 127, 222, 0.4); 
    transition: transform 0.15s, background-color 0.15s; 
}
.pos-dot:hover { 
    transform: scale(1.15); 
    background-color: #3B54C8; 
}

.dot-p3 { 
    top: 62px; 
    right: 28px; 
}
.dot-p2 { 
    top: 108px; 
    right: 20px; 
}
.dot-p1 { 
    top: 165px; 
    right: 16px; 
}
.dot-p6 { 
    top: 62px; 
    left: 28px; 
}
.dot-p5 { 
    top: 108px; 
    left: 20px; 
}
.dot-p4 { 
    top: 165px; 
    left: 16px; 
}

.btn-import-all { 
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
    margin-bottom: 16px; 
    transition: background-color 0.2s; 
}
.btn-import-all:hover { 
    background-color: #CBDCFC; 
}

.photos-card { 
    padding: 14px 20px; 
    margin-bottom: 12px; 
}
.photos-count-title { 
    font-size: 14px; 
    font-weight: 600; 
    color: #2D3748; 
}
.btn-add-photo { 
    background-color: #DDE7FE; 
    color: #4A6FE3; 
    font-size: 12px; 
    font-weight: 600; 
    padding: 6px 14px; 
    border-radius: 8px; 
    text-decoration: none; 
}
.center-btn-row { 
    display: flex; 
    justify-content: center; 
    margin-bottom: 24px; 
}
.btn-export-photos { 
    display: inline-flex; 
    align-items: center; 
    gap: 8px; 
    background-color: #DDE7FE; 
    color: #4A6FE3; 
    font-size: 12px; 
    font-weight: 600; 
    padding: 8px 18px; 
    border-radius: 8px; 
    text-decoration: none; 
}

.graph-card { 
    padding: 18px 20px 14px 20px; 
}
.graph-subtitle { 
    text-align: center; 
    font-size: 13px; 
    font-weight: 600; 
    color: #4A6FE3; 
    margin: 0 0 12px 0; 
}
.chart-wrapper { 
    position: relative; 
    height: 260px; 
    width: 100%; 
}
.chart-multi-legend { 
    display: flex; 
    flex-wrap: wrap; 
    justify-content: center; 
    gap: 12px; 
    margin-top: 12px; 
    font-size: 11px; 
    color: #4A5568; 
}
.legend-item { 
    display: inline-flex; 
    align-items: center; 
    gap: 5px; 
}
.legend-line { 
    width: 14px; 
    height: 3px; 
    border-radius: 2px; 
}
</style>

<script>
// Récupération des moyennes des 6 positions depuis MySQL
const mesuresVisite = <?= json_encode($mesures) ?>;

const freqAxis = [];
for (let f = 0.1; f <= 3.0; f += 0.03) {
    freqAxis.push(Number(f.toFixed(2)));
}

function genererSpectreS11(f0_hz, minS11_db, bw_hz, freqAxisGhz) {
    const f0_ghz = f0_hz / 1e9;
    const bw_ghz = bw_hz / 1e9;
    const baseline = -0.5;

    return freqAxisGhz.map(f => {
        const denom = 1 + Math.pow((2 * (f - f0_ghz)) / bw_ghz, 2);
        const lorentz = baseline + (minS11_db - baseline) / denom;
        const ripple = 0.25 * Math.sin(f * 18 * Math.PI);
        return Number((lorentz + ripple).toFixed(2));
    });
}

// Couleurs fixes pour les 6 positions anatomiques
const positionColors = {
    1: '#C53030', // Rouge
    2: '#2B6CB0', // Bleu
    3: '#38A169', // Vert
    4: '#DD6B20', // Orange
    5: '#D53F8C', // Rose
    6: '#ECC94B'  // Jaune
};

let minGlobalVisite = -16;
const datasetsVisite = [];

mesuresVisite.forEach(m => {
    const pos = parseInt(m.position);
    const f0 = parseFloat(m.fo);
    const minS11 = parseFloat(m.min_s11);
    const bw = parseFloat(m.bw);

    if (minS11 < minGlobalVisite) {
        minGlobalVisite = minS11 - 2;
    }

    datasetsVisite.push({
        label: 'Position ' + pos,
        data: genererSpectreS11(f0, minS11, bw, freqAxis),
        borderColor: positionColors[pos] || '#4A5568',
        borderWidth: 1.8,
        pointRadius: 0,
        tension: 0.25
    });
});

const ctxComp = document.getElementById('comparisonChart').getContext('2d');
new Chart(ctxComp, {
    type: 'line',
    data: {
        labels: freqAxis.map(f => f.toFixed(2)),
        datasets: datasetsVisite
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { 
                title: { display: true, text: 'Frequency (GHz)', font: { size: 10 } }, 
                grid: { color: '#F0F4F8' }, 
                ticks: { font: { size: 9 }, maxTicksLimit: 12 } 
            },
            y: { 
                title: { display: true, text: 'Return Loss (dB)', font: { size: 10 } }, 
                min: Math.floor(minGlobalVisite), 
                max: 0, 
                ticks: { stepSize: 4, font: { size: 9 } }, 
                grid: { color: '#F0F4F8' } 
            }
        }
    }
});
</script>