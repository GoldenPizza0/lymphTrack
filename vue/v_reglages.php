<div class="settings-container">
    <h1 class="settings-main-title">Settings</h1>

    <!-- 1. En-tête Profil Utilisateur -->
    <div class="white-card user-header-card">
        <div class="user-avatar-circle">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
        </div>
        <div class="user-header-info">
            <h2 class="user-display-name"><?= htmlspecialchars($user['name'] ?? 'User') ?></h2>
            <div class="user-email-sub"><?= htmlspecialchars($user['email'] ?? '') ?></div>
            <div class="user-role-inst"><?= htmlspecialchars($user['role']) ?> at <?= htmlspecialchars($user['institution']) ?></div>
        </div>
    </div>

    <!-- 2. Account Section -->
    <div class="white-card section-card">
        <h3 class="card-section-label">Account</h3>
        
        <div class="settings-item-row">
            <div class="item-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </div>
            <div class="item-text-box">
                <span class="field-title">Name</span>
                <span class="field-value"><?= htmlspecialchars($user['name'] ?? '-') ?></span>
            </div>
        </div>

        <div class="settings-item-row">
            <div class="item-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
            </div>
            <div class="item-text-box">
                <span class="field-title">Email</span>
                <span class="field-value"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
            </div>
        </div>

        <div class="settings-item-row">
            <div class="item-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
            </div>
            <div class="item-text-box">
                <span class="field-title">Role</span>
                <span class="field-value"><?= htmlspecialchars($user['role']) ?></span>
            </div>
        </div>

        <div class="settings-item-row" style="border-bottom: none;">
            <div class="item-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>
            </div>
            <div class="item-text-box">
                <span class="field-title">Institution</span>
                <span class="field-value"><?= htmlspecialchars($user['institution']) ?></span>
            </div>
        </div>

        <a href="index.php?uc=reglages&action=formulaireModifierMonCompte" class="btn-modify-account" style="text-decoration: none;">Modify Account</a>
    </div>

    <!-- 3. Security & Privacy Section -->
    <div class="white-card section-card">
        <h3 class="card-section-label">Security & Privacy</h3>

        <a href="#" class="settings-link-row" onclick="alert('Change Password feature'); return false;">
            <div class="item-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </div>
            <div class="item-text-box">
                <span class="link-title">Change Password</span>
                <span class="link-desc">Update your login credentials</span>
            </div>
            <svg class="chevron-right" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A0AEC0" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>

        <a href="#" class="settings-link-row" onclick="alert('Privacy Policy view'); return false;">
            <div class="item-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            </div>
            <div class="item-text-box">
                <span class="link-title">Privacy Policy</span>
                <span class="link-desc">How we protect your data</span>
            </div>
            <svg class="chevron-right" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A0AEC0" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>

        <a href="#" class="settings-link-row" style="border-bottom: none;" onclick="alert('Terms of Use view'); return false;">
            <div class="item-icon-box">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            </div>
            <div class="item-text-box">
                <span class="link-title">Terms of Use</span>
                <span class="link-desc">Read our usage guidelines</span>
            </div>
            <svg class="chevron-right" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A0AEC0" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
    </div>

    <!-- 4. Administrator Section -->
    <?php if (in_array(strtoupper($user['user_type'] ?? ''), ['ADMIN', 'SUPER_ADMIN'])) : ?>
        <div class="white-card section-card">
            <h3 class="card-section-label">Administrator</h3>

            <a href="index.php?uc=reglages&action=gererUtilisateurs" class="settings-link-row" style="border-bottom: none;">
                <div class="item-icon-box">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div class="item-text-box">
                    <span class="link-title">User Management</span>
                    <span class="link-desc">Update or create User</span>
                </div>
                <svg class="chevron-right" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A0AEC0" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
            </a>
        </div>
    <?php endif; ?>

    <!-- 5. About Section -->
    <div class="white-card section-card">
        <h3 class="card-section-label">About</h3>
        <div class="about-data-row">
            <span class="about-key">Version</span>
            <span class="about-val">1.0.0</span>
        </div>
        <div class="about-data-row">
            <span class="about-key">Build</span>
            <span class="about-val">2025.08.28</span>
        </div>
    </div>

    <!-- 6. Sign Out Button -->
    <a href="index.php?uc=connexion&action=deconnexion" class="btn-signout">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
        </svg>
        <span>Sign Out</span>
    </a>
</div>

<style>
.settings-container {
    max-width: 580px;
    margin: 20px auto 40px auto;
    padding: 0 16px;
    box-sizing: border-box;
}
.settings-main-title {
    text-align: center;
    font-size: 19px;
    font-weight: 700;
    color: #1A202C;
    margin: 0 0 20px 0;
}

.white-card {
    background: #FFFFFF;
    border-radius: 18px;
    padding: 20px 22px;
    margin-bottom: 16px;
    border: 1px solid #EBF0F7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

/* User Header Card */
.user-header-card {
    display: flex;
    align-items: center;
    gap: 16px;
}
.user-avatar-circle {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background-color: #5B7FDE;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-shrink: 0;
}
.user-display-name {
    font-size: 15px;
    font-weight: 700;
    color: #2D3748;
    margin: 0 0 3px 0;
}
.user-email-sub {
    font-size: 12px;
    color: #718096;
    margin-bottom: 3px;
}
.user-role-inst {
    font-size: 12px;
    font-weight: 500;
    color: #4A6FE3;
}

/* Section Cards */
.card-section-label {
    font-size: 13.5px;
    font-weight: 700;
    color: #2D3748;
    margin: 0 0 14px 0;
}
.settings-item-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 10px 0;
    border-bottom: 1px solid #F7FAFC;
}
.item-icon-box {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.item-text-box {
    display: flex;
    flex-direction: column;
    flex: 1;
}
.field-title {
    font-size: 12px;
    font-weight: 600;
    color: #2D3748;
}
.field-value {
    font-size: 11.5px;
    color: #718096;
    margin-top: 1px;
}

.btn-modify-account {
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
    margin-top: 14px;
    transition: background-color 0.2s;
}
.btn-modify-account:hover {
    background-color: #CBDCFC;
}

/* Liens cliquables (Security & Admin) */
.settings-link-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 11px 0;
    text-decoration: none;
    border-bottom: 1px solid #F7FAFC;
    transition: opacity 0.15s;
}
.settings-link-row:hover {
    opacity: 0.8;
}
.link-title {
    font-size: 13px;
    font-weight: 600;
    color: #2D3748;
}
.link-desc {
    font-size: 11.5px;
    color: #A0AEC0;
    margin-top: 1px;
}
.chevron-right {
    flex-shrink: 0;
}

/* About */
.about-data-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 7px 0;
    font-size: 12.5px;
}
.about-key { color: #718096; }
.about-val { color: #2D3748; font-weight: 600; }

/* Bouton Sign Out */
.btn-signout {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    width: 100%;
    padding: 12px 0;
    border: 1px solid #E2E8F0;
    background-color: #FFFFFF;
    border-radius: 12px;
    color: #4A6FE3;
    font-size: 13.5px;
    font-weight: 600;
    text-decoration: none;
    margin-top: 8px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    transition: background-color 0.2s;
}
.btn-signout:hover {
    background-color: #F8F9FD;
}
</style>