<?php
$titrePage = !empty($isEdit) ? "Edit User" : "New User";
$boutonLabel = !empty($isEdit) ? "Update User" : "Create User";
$currentType = strtoupper($userAEditer['user_type'] ?? 'USER');
?>

<div class="user-form-container">
    <!-- Barre supérieure -->
    <div class="top-nav-bar">
        <a href="index.php?uc=reglages&action=gererUtilisateurs" class="back-link" title="Back to User Management">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title"><?= $titrePage ?></h1>
    </div>

    <?php if (!empty($erreur)) : ?>
        <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form action="index.php?uc=reglages&action=validerUtilisateur" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($userAEditer['id'] ?? '') ?>">

        <!-- 1. Email (required) -->
        <div class="field-block">
            <label class="field-title-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Email <span class="required-star">*</span>
            </label>
            <input type="email" name="email" class="input-rounded-field" placeholder="Enter email ..." value="<?= htmlspecialchars($userAEditer['email'] ?? '') ?>" required>
        </div>

        <!-- 2. Password (required) -->
        <div class="field-block">
            <label class="field-title-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                Password <span class="required-star">*</span>
            </label>
            <input type="password" name="password" class="input-rounded-field" placeholder="Enter password ..." <?= empty($isEdit) ? 'required' : '' ?>>
        </div>

        <!-- 3. Name (optional) -->
        <div class="field-block">
            <label class="field-title-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Name <span class="optional-tag">(optional)</span>
            </label>
            <input type="text" name="name" class="input-rounded-field" placeholder="Enter full name ..." value="<?= htmlspecialchars($userAEditer['name'] ?? '') ?>">
        </div>

        <!-- 4. Role (optional) -->
        <div class="field-block">
            <label class="field-title-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                Role <span class="optional-tag">(optional)</span>
            </label>
            <input type="text" name="role" class="input-rounded-field" placeholder="Enter role (e.g. Doctor) ..." value="<?= htmlspecialchars($userAEditer['role'] ?? '') ?>">
        </div>

        <!-- 5. Institution (optional) -->
        <div class="field-block">
            <label class="field-title-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>
                Institution <span class="optional-tag">(optional)</span>
            </label>
            <input type="text" name="institution" class="input-rounded-field" placeholder="Enter institution (e.g. University of Example)..." value="<?= htmlspecialchars($userAEditer['institution'] ?? '') ?>">
        </div>

        <!-- 6. Access Level selector -->
        <div class="segmented-control" id="userTypeControl">
            <button type="button" class="segment-btn <?= ($currentType === 'USER') ? 'active' : '' ?>" onclick="selectAccessLevel('USER', this)">USER</button>
            <button type="button" class="segment-btn <?= ($currentType === 'ADMIN') ? 'active' : '' ?>" onclick="selectAccessLevel('ADMIN', this)">ADMIN</button>
            <button type="button" class="segment-btn <?= ($currentType === 'SUPER_ADMIN') ? 'active' : '' ?>" onclick="selectAccessLevel('SUPER_ADMIN', this)">SUPER_ADMIN</button>
            <input type="hidden" name="user_type" id="userTypeInput" value="<?= $currentType ?>">
        </div>

        <!-- Submit Button -->
        <div class="submit-btn-wrapper">
            <button type="submit" class="btn-create-user"><?= $boutonLabel ?></button>
        </div>
    </form>
</div>

<style>
.user-form-container {
    max-width: 520px;
    margin: 20px auto 40px auto;
    padding: 0 16px;
    box-sizing: border-box;
}
.top-nav-bar {
    display: flex;
    align-items: center;
    position: relative;
    margin-bottom: 28px;
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

.field-block {
    margin-bottom: 16px;
}
.field-title-label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12.5px;
    font-weight: 600;
    color: #2D3748;
    margin-bottom: 6px;
}
.required-star {
    color: #E53E3E;
    font-weight: 700;
}
.optional-tag {
    color: #718096;
    font-weight: 400;
}
.input-rounded-field {
    width: 100%;
    height: 44px;
    padding: 0 18px;
    border-radius: 22px;
    border: 1px solid #E2E8F0;
    background-color: #FFFFFF;
    font-size: 13.5px;
    color: #2D3748;
    outline: none;
    box-sizing: border-box;
    transition: border-color 0.2s;
}
.input-rounded-field:focus {
    border-color: #638CDE;
}
.input-rounded-field::placeholder {
    color: #A0AEC0;
}

/* Sélecteur de niveau d'accès segmenté */
.segmented-control {
    display: flex;
    background-color: #E2E8F0;
    border-radius: 12px;
    padding: 4px;
    margin: 24px 0;
}
.segment-btn {
    flex: 1;
    border: none;
    background: transparent;
    padding: 10px 0;
    font-size: 12px;
    font-weight: 700;
    color: #4A5568;
    border-radius: 9px;
    cursor: pointer;
    transition: all 0.2s;
}
.segment-btn.active {
    background-color: #638CDE;
    color: #FFFFFF;
    box-shadow: 0 2px 6px rgba(99, 140, 222, 0.4);
}

.submit-btn-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 10px;
}
.btn-create-user {
    width: 180px;
    height: 42px;
    background-color: #C8D7FC;
    color: #3B54C8;
    font-size: 13.5px;
    font-weight: 600;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    transition: background-color 0.2s, color 0.2s;
}
.btn-create-user:hover {
    background-color: #5B7FDE;
    color: #FFFFFF;
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
function selectAccessLevel(level, clickedBtn) {
    const buttons = document.querySelectorAll('#userTypeControl .segment-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    clickedBtn.classList.add('active');
    document.getElementById('userTypeInput').value = level;
}
</script>