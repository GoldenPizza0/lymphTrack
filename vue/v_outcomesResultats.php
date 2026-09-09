<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="outcomes-container">
    <div class="top-nav-bar">
        <a href="index.php?uc=outcomes&action=afficherFiltres" class="back-link">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title">Results</h1>
    </div>

    <!-- Carte récapitulative des paramètres sélectionnés -->
    <div class="white-card params-card">
        <h2 class="params-header-title">Selected Parameters</h2>
        <div class="params-meta-lines">
            <div>Patients (<?= count($selectedPatients) ?>): <?= htmlspecialchars(implode(', ', $selectedPatients)) ?></div>
            <div>Visit: <?= htmlspecialchars($visit) ?></div>
            <div>Position: <?= ($position === 'All') ? 'None' : htmlspecialchars($position) ?></div>
        </div>
    </div>

    <!-- Zone d'affichage des graphiques selon le mode -->
    <div id="chartsContainer">
        <?php if ($position === 'All') : ?>
            <!-- Mode 2 : 6 graphiques (un par position) -->
            <?php for ($pos = 1; $pos <= 6; $pos++) : ?>
                <div class="white-card chart-result-card">
                    <h3 class="chart-result-title">Visit : <?= htmlspecialchars($visit) ?> - Position <?= $pos ?></h3>
                    <div class="canvas-container">
                        <canvas id="chartPos_<?= $pos ?>"></canvas>
                    </div>
                </div>
            <?php endfor; ?>
        <?php else : ?>
            <!-- Mode 1 ou 3 : 1 seul graphique grand format -->
            <div class="white-card chart-result-card">
                <h3 class="chart-result-title">
                    <?= ($visit === 'All') ? "Position $position across visits" : "Visit : $visit - Position $position" ?>
                </h3>
                <div class="canvas-container">
                    <canvas id="mainOutcomeChart"></canvas>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.outcomes-container { 
    max-width: 600px; 
    margin: 15px auto 70px auto; 
    padding: 0 16px; 
}
.top-nav-bar { 
    display: flex; 
    align-items: center; 
    position: relative; 
    margin-bottom: 20px; 
}
.back-link { 
    text-decoration: none; 
    display: flex; 
    align-items: center; 
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

.white-card { 
    background: #FFFFFF; 
    border-radius: 18px; 
    padding: 18px 22px; 
    margin-bottom: 16px; 
    border: 1px solid #EBF0F7; 
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02); 
}
.params-header-title { 
    text-align: center; 
    font-size: 14.5px; 
    font-weight: 700; 
    color: #5B7FDE; 
    margin: 0 0 10px 0; 
}
.params-meta-lines { 
    font-size: 12px; 
    color: #718096; 
    line-height: 1.5; 
}

.chart-result-card { 
    padding: 18px 16px 14px 16px; 
}
.chart-result-title { 
    text-align: center; 
    font-size: 13px; 
    font-weight: 600; 
    color: #4A6FE3; 
    margin: 0 0 12px 0; 
}
.canvas-container { 
    position: relative; 
    height: 230px; 
    width: 100%; 
}
</style>

<script>
const rawData = <?= json_encode($metriquesComparaison ?? []) ?>;
const modePosition = "<?= $position ?>";
const modeVisit = "<?= $visit ?>";
const selectedPatients = <?= json_encode($selectedPatients) ?>;

// Axe de fréquences
const rawFreqs = [];
for (let f = 0.095; f <= 3.0; f += 0.035) {
    rawFreqs.push(Number(f.toFixed(3)));
}

function genererSpectreS11(f0_hz, minS11_db, bw_hz, freqAxisGhz) {
    const f0_ghz = f0_hz / 1e9;
    const bw_ghz = (bw_hz > 0) ? (bw_hz / 1e9) : 0.25;
    const baseline = -0.5;

    return freqAxisGhz.map(f => {
        const denom = 1 + Math.pow((2 * (f - f0_ghz)) / bw_ghz, 2);
        const lorentz = baseline + (minS11_db - baseline) / denom;
        const ripple = 0.22 * Math.sin(f * 16 * Math.PI);
        return Number((lorentz + ripple).toFixed(2));
    });
}

// Couleurs attribuées aux patients
const patientColorPalette = ['#C53030', '#1E3A8A', '#38A169', '#DD6B20', '#805AD5', '#319795'];
const patientColors = {};
selectedPatients.forEach((pid, idx) => {
    patientColors[pid] = patientColorPalette[idx % patientColorPalette.length];
});

function getChartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: true, position: 'bottom', labels: { boxWidth: 12, font: { size: 10 } } }
        },
        scales: {
            x: { title: { display: true, text: 'Frequency (GHz)', font: { size: 9 } }, grid: { color: '#F7FAFC' }, ticks: { font: { size: 8.5 }, maxTicksLimit: 10 } },
            y: { title: { display: true, text: 'Return Loss (dB)', font: { size: 9 } }, min: -18, max: 0, ticks: { stepSize: 3, font: { size: 8.5 } }, grid: { color: '#F7FAFC' } }
        }
    };
}

// MODE 2 : 6 graphiques (Position All)
if (modePosition === 'All') {
    for (let p = 1; p <= 6; p++) {
        const ctx = document.getElementById('chartPos_' + p).getContext('2d');
        const datasets = [];

        selectedPatients.forEach(pid => {
            // Filtrer par patient, visite et position
            const m = rawData.find(d => d.patient_id === pid && parseInt(d.position) === p && (modeVisit === 'All' || d.visit_name === modeVisit));
            if (m) {
                datasets.push({
                    label: pid,
                    data: genererSpectreS11(parseFloat(m.fo), parseFloat(m.min_s11), parseFloat(m.bw), rawFreqs),
                    borderColor: patientColors[pid],
                    borderWidth: 1.8,
                    pointRadius: 0,
                    tension: 0.25
                });
            }
        });

        new Chart(ctx, {
            type: 'line',
            data: { labels: rawFreqs.map(f => f.toFixed(3)), datasets: datasets },
            options: getChartOptions()
        });
    }
} else {
    // MODES 1 & 3 : 1 seul graphique
    const ctx = document.getElementById('mainOutcomeChart').getContext('2d');
    const datasets = [];

    selectedPatients.forEach(pid => {
        const pRows = rawData.filter(d => d.patient_id === pid && parseInt(d.position) === parseInt(modePosition) && (modeVisit === 'All' || d.visit_name === modeVisit));
        
        pRows.forEach(m => {
            const label = (modeVisit === 'All') ? `${pid} (${m.visit_name})` : pid;
            datasets.push({
                label: label,
                data: genererSpectreS11(parseFloat(m.fo), parseFloat(m.min_s11), parseFloat(m.bw), rawFreqs),
                borderColor: patientColors[pid],
                borderWidth: 1.8,
                pointRadius: 0,
                tension: 0.25
            });
        });
    });

    new Chart(ctx, {
        type: 'line',
        data: { labels: rawFreqs.map(f => f.toFixed(3)), datasets: datasets },
        options: getChartOptions()
    });
}
</script>