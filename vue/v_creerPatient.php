<div class="form-container">
    <div class="top-nav-bar">
        <a href="index.php?uc=gererPatients&action=afficherPatients" class="back-link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="page-title">New Patient</h1>
    </div>

    <?php if (!empty($erreur)) : ?>
        <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form action="index.php?uc=gererPatients&action=validerAjoutPatient" method="POST">
        <!-- 1. Patient ID -->
        <div class="section-card">
            <label class="card-label-title">Patient ID</label>
            <div class="input-subtitle">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Custom Patient ID (optional)
            </div>
            <input type="text" name="patient_id" id="custom_patient_id" class="text-field" placeholder="Enter patient ID (e.g. MV123)">
            <div class="toggle-row">
                <span>Do not provide ID</span>
                <label class="switch">
                    <input type="checkbox" name="no_id" id="toggle_no_id" onchange="toggleField('custom_patient_id', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- 2. Patient Demographics -->
        <div class="section-card">
            <label class="card-label-title">Patient Demographics</label>
            <div class="input-subtitle">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Age (optional)
            </div>
            <input type="number" name="age" id="patient_age" class="text-field" placeholder="Enter age">
            <div class="toggle-row">
                <span>Do not provide age</span>
                <label class="switch">
                    <input type="checkbox" name="no_age" id="toggle_no_age" onchange="toggleField('patient_age', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>

            <div class="input-subtitle" style="margin-top: 14px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v12M6 12h12"></path></svg>
                Gender
            </div>
            <div class="segmented-control" id="gender-control">
                <button type="button" class="segment-btn active" onclick="selectSegment('gender-control', 'gender_input', 'MALE', this)">MALE</button>
                <button type="button" class="segment-btn" onclick="selectSegment('gender-control', 'gender_input', 'FEMALE', this)">FEMALE</button>
                <button type="button" class="segment-btn" onclick="selectSegment('gender-control', 'gender_input', 'UNKNOWN', this)">UNKNOWN</button>
                <input type="hidden" name="gender" id="gender_input" value="MALE">
            </div>
        </div>

        <!-- 3. Measurements -->
        <div class="section-card">
            <label class="card-label-title">Measurements</label>
            <div class="input-subtitle">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line></svg>
                BMI (optional)
            </div>
            <input type="number" step="0.1" name="bmi" id="patient_bmi" class="text-field" placeholder="0.0">
            <div class="toggle-row">
                <span>Do not provide BMI</span>
                <label class="switch">
                    <input type="checkbox" name="no_bmi" id="toggle_no_bmi" onchange="toggleField('patient_bmi', this.checked)">
                    <span class="slider"></span>
                </label>
            </div>
        </div>

        <!-- 4. Lymphedema Information -->
        <div class="section-card">
            <label class="card-label-title">Lymphedema Information</label>
            <div class="input-subtitle">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"></path></svg>
                Side
            </div>
            <div class="segmented-control" id="side-control">
                <button type="button" class="segment-btn active" onclick="selectSegment('side-control', 'side_input', 'RIGHT', this)">RIGHT</button>
                <button type="button" class="segment-btn" onclick="selectSegment('side-control', 'side_input', 'LEFT', this)">LEFT</button>
                <button type="button" class="segment-btn" onclick="selectSegment('side-control', 'side_input', 'BOTH', this)">BOTH</button>
                <button type="button" class="segment-btn" onclick="selectSegment('side-control', 'side_input', 'UNKNOWN', this)">UNKNOWN</button>
                <input type="hidden" name="lymphedema_side" id="side_input" value="RIGHT">
            </div>
        </div>

        <!-- 5. Other Notes -->
        <div class="section-card">
            <label class="card-label-title">Other (optional)</label>
            <div class="input-subtitle">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Notes
            </div>
            <textarea name="notes" class="textarea-field" placeholder="Enter notes..."></textarea>
        </div>

        <button type="submit" class="btn-create-patient">Create Patient</button>
    </form>
</div>

<style>
.form-container { 
    max-width: 580px; 
    margin: 20px auto 40px auto; 
    padding: 0 16px; 
}
.top-nav-bar { 
    display: flex; 
    align-items: center; 
    position: relative; 
    margin-bottom: 24px; 
}
.back-link { 
    color: #1A202C; 
    display: flex; 
    align-items: center; 
    text-decoration: none; 
    cursor: pointer; 
}
.page-title { 
    position: absolute; 
    left: 50%; 
    transform: translateX(-50%); 
    font-size: 19px; 
    font-weight: 600; 
    margin: 0; 
    color: #1A202C; 
}
.section-card { 
    background: #FFFFFF; 
    border-radius: 18px; 
    padding: 22px 24px; 
    margin-bottom: 16px; 
    border: 1px solid #EDF2F7; 
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.02); 
}
.card-label-title { 
    display: block; 
    font-size: 16px; 
    font-weight: 700; 
    color: #1A202C; 
    margin-bottom: 12px; 
}
.input-subtitle { 
    display: flex; 
    align-items: center; 
    gap: 6px; 
    font-size: 12px; 
    font-weight: 600; 
    color: #718096; 
    margin-bottom: 8px; 
}
.text-field { 
    width: 100%; 
    height: 44px; 
    padding: 0 16px; 
    border-radius: 22px; 
    border: 1px solid #E2E8F0; 
    background-color: #FFFFFF; 
    font-size: 14px; 
    color: #2D3748; 
    outline: none; 
    box-sizing: border-box; 
}
.text-field:focus { 
    border-color: #638CDE; 
}
.text-field:disabled { 
    background-color: #F7FAFC; 
    color: #A0AEC0; 
    cursor: not-allowed; 
}
.textarea-field { 
    width: 100%; 
    height: 90px; 
    padding: 12px 16px; 
    border-radius: 14px; 
    border: 1px solid #E2E8F0; 
    background-color: #FFFFFF; 
    font-size: 14px; 
    color: #2D3748; 
    outline: none; 
    resize: none; 
    box-sizing: border-box; 
}
.textarea-field:focus { 
    border-color: #638CDE; 
}
.toggle-row { 
    display: flex; 
    align-items: center; 
    gap: 12px; 
    margin-top: 14px; 
    font-size: 13px; 
    color: #718096; 
}
.switch { 
    position: relative; 
    display: inline-block; 
    width: 38px; 
    height: 22px; 
}
.switch input { 
    opacity: 0; 
    width: 0; 
    height: 0; 
}
.slider { 
    position: absolute; 
    cursor: pointer; 
    top: 0; 
    left: 0; 
    right: 0; 
    bottom: 0; 
    background-color: #CBD5E0; 
    transition: .3s; 
    border-radius: 22px; 
}
.slider:before { 
    position: absolute; 
    content: ""; 
    height: 16px; 
    width: 16px; 
    left: 3px; 
    bottom: 3px; 
    background-color: white; 
    transition: .3s; 
    border-radius: 50%; 
}
input:checked + .slider { 
    background-color: #5B7FDE; 
}
input:checked + .slider:before { 
    transform: translateX(16px); 
}
.segmented-control { 
    display: flex; 
    background-color: #EEF2F6; 
    border-radius: 12px; 
    padding: 4px; 
    margin-top: 6px; 
}
.segment-btn { 
    flex: 1; 
    border: none; 
    background: transparent; 
    padding: 9px 0; 
    font-size: 12px; 
    font-weight: 700; 
    color: #718096; 
    border-radius: 9px; 
    cursor: pointer; 
    transition: background-color 0.2s, color 0.2s; 
}
.segment-btn.active { 
    background-color: #5B7FDE; 
    color: #FFFFFF; 
    box-shadow: 0 2px 6px rgba(91, 127, 222, 0.35); 
}
.btn-create-patient { 
    width: 100%; 
    height: 48px; 
    background-color: #5B7FDE; 
    color: #FFFFFF; 
    font-size: 15px; 
    font-weight: 600; 
    border: none; 
    border-radius: 24px; 
    cursor: pointer; 
    box-shadow: 0 4px 12px rgba(91, 127, 222, 0.35); 
    margin-top: 10px; 
    transition: background-color 0.2s; 
}
.btn-create-patient:hover { 
    background-color: #486AC4; 
}
.alert-error { 
    background-color: #FED7D7; 
    color: #C53030; 
    padding: 10px 14px; 
    border-radius: 8px; 
    font-size: 13px; 
    margin-bottom: 16px; 
    text-align: center; 
}
</style>

<script>
function toggleField(fieldId, isChecked) {
    const field = document.getElementById(fieldId);
    field.disabled = isChecked;
    if (isChecked) field.value = '';
}

function selectSegment(containerId, hiddenInputId, value, clickedBtn) {
    const container = document.getElementById(containerId);
    const buttons = container.querySelectorAll('.segment-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    clickedBtn.classList.add('active');
    document.getElementById(hiddenInputId).value = value;
}
</script>