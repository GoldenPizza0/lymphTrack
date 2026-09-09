<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="position-page-container">
    <!-- Barre de navigation supérieure -->
    <div class="top-nav-bar">
        <a href="index.php?uc=gererFollowup&action=voirVisite&op_id=<?= urlencode($operation['id']) ?>" class="back-link" title="Back to visit">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title">Position : <?= htmlspecialchars($position) ?></h1>
        <a href="index.php?uc=gererMesures&action=exporterPositionZip&op_id=<?= urlencode($operation['id']) ?>&pos=<?= urlencode($position) ?>" class="btn-circle-action" title="Export position data">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
        </a>
    </div>

    <!-- Liste des cartes de mesures individuelles -->
    <div class="measurements-list">
        <?php if (!empty($lesMesures)) : ?>
            <?php foreach ($lesMesures as $m) : ?>
                <div class="measurement-card">
                    <div class="measurement-card-left">
                        <h3 class="measurement-title">Measurement <?= htmlspecialchars($m['measurement_number']) ?></h3>
                        <div class="measurement-values">
                            <div>Return loss: <?= htmlspecialchars($m['min_return_loss_db']) ?> dB</div>
                            <div>Frequency: <?= htmlspecialchars($m['min_frequency_hz']) ?> Hz</div>
                            <div>Bandwidth: <?= htmlspecialchars($m['bandwidth_hz']) ?> Hz</div>
                        </div>
                    </div>
                    <a href="index.php?uc=gererMesures&action=supprimerMesure&result_id=<?= urlencode($m['id']) ?>&op_id=<?= urlencode($operation['id']) ?>&pos=<?= urlencode($position) ?>" 
                       class="btn-delete-measurement" 
                       onclick="return confirm('Do you really want to delete Measurement <?= $m['measurement_number'] ?>?');"
                       title="Delete measurement">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        </svg>
                    </a>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p style="text-align: center; color: #A0AEC0; font-size: 13px; margin: 20px 0;">No measurement imported yet for this position.</p>
        <?php endif; ?>
    </div>

    <!-- Bouton Add measurement(s) -->
    <form method="POST" action="index.php?uc=gererMesures&action=ajouterMesureSimulation" style="margin-bottom: 24px;">
        <input type="hidden" name="op_id" value="<?= htmlspecialchars($operation['id']) ?>">
        <input type="hidden" name="pos" value="<?= htmlspecialchars($position) ?>">
        <button type="submit" class="btn-add-measurements">+ Add measurement(s)</button>
    </form>

    <!-- Graphique comparatif des mesures -->
    <div class="white-card graph-card">
        <h3 class="graph-subtitle">Comparison graph of measurements</h3>
        <div class="chart-wrapper">
            <canvas id="positionMeasuresChart"></canvas>
        </div>
        <div class="chart-measurements-legend" id="chartLegend"></div>
    </div>
</div>

<style>
.position-page-container {
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
    margin-bottom: 20px;
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
    text-decoration: none;
    box-shadow: 0 3px 8px rgba(91, 127, 222, 0.3);
}

.measurements-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 18px;
}
.measurement-card {
    background: #FFFFFF;
    border: 1px solid #EBF0F7;
    border-radius: 16px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
}
.measurement-title {
    font-size: 15px;
    font-weight: 600;
    color: #5B7FDE;
    margin: 0 0 6px 0;
}
.measurement-values {
    font-size: 11.5px;
    color: #718096;
    line-height: 1.4;
}
.btn-delete-measurement {
    color: #7A889B;
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
    display: flex;
    align-items: center;
    transition: color 0.15s;
}
.btn-delete-measurement:hover {
    color: #E53E3E;
}

.btn-add-measurements {
    display: block;
    width: 100%;
    padding: 11px 0;
    text-align: center;
    background-color: #DDE7FE;
    color: #4A6FE3;
    font-weight: 600;
    font-size: 13px;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background-color 0.2s;
}
.btn-add-measurements:hover {
    background-color: #CBDCFC;
}

.white-card {
    background: #FFFFFF;
    border-radius: 18px;
    padding: 20px 24px;
    border: 1px solid #EBF0F7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
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
    height: 250px;
    width: 100%;
}
.chart-measurements-legend {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 16px;
    margin-top: 14px;
    font-size: 11px;
    color: #4A5568;
}
.legend-entry {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.legend-square {
    width: 12px;
    height: 3px;
    border-radius: 2px;
}
</style>

<script>
// 1. Récupération directe des mesures MySQL
const mesuresBDD = <?= json_encode($lesMesures) ?>;

// 2. Axe des fréquences : de 0.1 GHz à 3.0 GHz (pas de 0.03 GHz)
const freqAxis = [];
for (let f = 0.1; f <= 3.0; f += 0.03) {
    freqAxis.push(Number(f.toFixed(2)));
}

// 3. Modèle physique de résonance
function genererSpectreS11(f0_hz, minS11_db, bw_hz, freqAxisGhz) {
    const f0_ghz = f0_hz / 1e9;
    const bw_ghz = bw_hz / 1e9;
    const baseline = -0.5;

    return freqAxisGhz.map(f => {
        const denom = 1 + Math.pow((2 * (f - f0_ghz)) / bw_ghz, 2);
        const lorentz = baseline + (minS11_db - baseline) / denom;
        const ripple = 0.2 * Math.sin(f * 16 * Math.PI);
        return Number((lorentz + ripple).toFixed(2));
    });
}

// 4. Construction des datasets à partir des vraies lignes de la BDD
const colors = ['#E53E3E', '#2B6CB0', '#38A169', '#DD6B20', '#805AD5', '#D53F8C'];
const datasets = [];
const legendContainer = document.getElementById('chartLegend');
legendContainer.innerHTML = '';

let minGlobalS11 = -12;

mesuresBDD.forEach((m, index) => {
    const color = colors[index % colors.length];
    const f0 = parseFloat(m.min_frequency_hz);
    const minS11 = parseFloat(m.min_return_loss_db);
    const bw = parseFloat(m.bandwidth_hz);

    if (minS11 < minGlobalS11) {
        minGlobalS11 = minS11 - 2;
    }

    const dataPoints = genererSpectreS11(f0, minS11, bw, freqAxis);

    datasets.push({
        label: 'Measurement ' + m.measurement_number,
        data: dataPoints,
        borderColor: color,
        borderWidth: 1.8,
        pointRadius: 0,
        tension: 0.25
    });

    // Légende sous le graphique
    const item = document.createElement('span');
    item.className = 'legend-entry';
    item.innerHTML = `<span class="legend-square" style="background:${color};"></span> Measurement ${m.measurement_number}`;
    legendContainer.appendChild(item);
});

// 5. Tracé du graphique Chart.js
const ctx = document.getElementById('positionMeasuresChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: freqAxis.map(f => f.toFixed(2)),
        datasets: datasets
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
                min: Math.floor(minGlobalS11), 
                max: 0, 
                ticks: { stepSize: 3, font: { size: 9 } }, 
                grid: { color: '#F0F4F8' } 
            }
        }
    }
});
</script>