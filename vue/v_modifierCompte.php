<div class="modify-account-container">
    <!-- Barre de navigation supérieure -->
    <div class="top-nav-bar">
        <a href="index.php?uc=reglages&action=afficherReglages" class="back-link" title="Back to Settings">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title">Modify Account</h1>
    </div>

    <?php if (!empty($erreur)) : ?>
        <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="white-card form-card">
        <form action="index.php?uc=reglages&action=validerModifierMonCompte" method="POST">
            
            <!-- 1. Name -->
            <div class="field-block">
                <label class="field-title-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Name
                </label>
                <input type="text" name="name" class="input-rounded-field" placeholder="Enter your full name" value="<?= htmlspecialchars($user['name'] ?? '') ?>">
            </div>

            <!-- 2. Email -->
            <div class="field-block">
                <label class="field-title-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                    Email <span class="required-star">*</span>
                </label>
                <input type="email" name="email" class="input-rounded-field" placeholder="Enter your email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>
            </div>

            <!-- 3. Role -->
            <div class="field-block">
                <label class="field-title-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                    Role
                </label>
                <input type="text" name="role" class="input-rounded-field" placeholder="e.g. Intern, Doctor" value="<?= htmlspecialchars($user['role'] ?? '') ?>">
            </div>

            <!-- 4. Institution -->
            <div class="field-block">
                <label class="field-title-label">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#4A5568" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>
                    Institution
                </label>
                <input type="text" name="institution" class="input-rounded-field" placeholder="e.g. Uppsala University" value="<?= htmlspecialchars($user['institution'] ?? '') ?>">
            </div>

            <div class="submit-btn-wrapper">
                <button type="submit" class="btn-save-account">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<style>
.modify-account-container {
    max-width: 520px;
    margin: 20px auto 40px auto;
    padding: 0 16px;
    box-sizing: border-box;
}
.top-nav-bar {
    display: flex;
    align-items: center;
    position: relative;
    margin-bottom: 24px;
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
    padding: 24px 22px;
    border: 1px solid #EBF0F7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.field-block {
    margin-bottom: 18px;
}
.field-title-label {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 600;
    color: #2D3748;
    margin-bottom: 7px;
}
.required-star {
    color: #E53E3E;
    font-weight: 700;
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

.submit-btn-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 24px;
}
.btn-save-account {
    width: 100%;
    height: 44px;
    background-color: #DDE7FE;
    color: #4A6FE3;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 22px;
    cursor: pointer;
    transition: background-color 0.2s, color 0.2s;
}
.btn-save-account:hover {
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