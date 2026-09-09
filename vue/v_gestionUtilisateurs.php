<div class="admin-users-container">
    <!-- Barre de navigation supérieure -->
    <div class="top-nav-bar">
        <a href="index.php?uc=reglages&action=afficherReglages" class="back-link" title="Back to Settings">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title">Admin</h1>
        <!-- Bouton + pour ajouter un nouvel utilisateur -->
        <a href="index.php?uc=reglages&action=formulaireUtilisateur" class="btn-circle-action" title="Add user">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </a>
    </div>

    <!-- Liste des utilisateurs -->
    <div class="users-card-list">
        <?php if (!empty($lesUtilisateurs)) : ?>
            <?php foreach ($lesUtilisateurs as $u) : ?>
                <?php
                    $uType = strtolower($u['user_type'] ?? 'user');
                    $isCurrentUser = ($u['id'] == $_SESSION['user_id']);
                ?>
                <div class="white-card user-card-item">
                    <!-- LE LIEN EST PLACÉ ICI : il englobe tout le bloc d'informations pour ouvrir la modification -->
                    <a href="index.php?uc=reglages&action=formulaireUtilisateur&id=<?= urlencode($u['id']) ?>" class="user-card-link">
                        <div class="user-card-content">
                            <!-- Nom et badge de rôle coloré -->
                            <div class="user-title-row">
                                <span class="user-item-name"><?= htmlspecialchars($u['name']) ?></span>
                                <span class="user-type-badge type-<?= $uType ?>">(<?= htmlspecialchars($uType) ?>)</span>
                            </div>

                            <!-- Email -->
                            <div class="user-prop-line">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"></rect><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path></svg>
                                <span><?= htmlspecialchars($u['email']) ?></span>
                            </div>

                            <!-- Role / Métier -->
                            <div class="user-prop-line">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line></svg>
                                <span><?= htmlspecialchars(!empty($u['role']) ? $u['role'] : 'Doctor') ?></span>
                            </div>

                            <!-- Institution -->
                            <div class="user-prop-line">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"></rect><line x1="8" y1="6" x2="16" y2="6"></line><line x1="8" y1="10" x2="16" y2="10"></line></svg>
                                <span><?= htmlspecialchars(!empty($u['institution']) ? $u['institution'] : 'Uppsala University') ?></span>
                            </div>
                        </div>
                    </a>

                    <!-- Bouton suppression séparé à droite (masqué pour soi-même) -->
                    <div class="user-card-actions">
                        <?php if (!$isCurrentUser) : ?>
                            <a href="index.php?uc=reglages&action=supprimerUtilisateur&id=<?= urlencode($u['id']) ?>" 
                               class="btn-trash-user"
                               onclick="return confirm('Do you really want to delete user <?= htmlspecialchars(addslashes($u['name'])) ?>?');"
                               title="Delete user">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#7A889B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p style="text-align: center; color: #A0AEC0; margin-top: 30px;">No user found.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.admin-users-container {
    max-width: 600px;
    margin: 15px auto 50px auto;
    padding: 0 16px;
    box-sizing: border-box;
}
.top-nav-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 22px;
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
    text-decoration: none;
    box-shadow: 0 3px 8px rgba(91, 127, 222, 0.3);
}

.white-card {
    background: #FFFFFF;
    border-radius: 16px;
    padding: 18px 22px;
    margin-bottom: 14px;
    border: 1px solid #EBF0F7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.user-card-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

/* Style du lien cliquable de la carte */
.user-card-link {
    text-decoration: none;
    color: inherit;
    display: flex;
    flex: 1;
    cursor: pointer;
}
.user-card-link:hover .user-item-name {
    text-decoration: underline;
}

.user-card-content {
    flex: 1;
}
.user-title-row {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
}
.user-item-name {
    font-size: 15px;
    font-weight: 600;
    color: #5B7FDE;
}
.user-type-badge {
    font-size: 12.5px;
    font-weight: 600;
}
.type-super_admin, .type-admin {
    color: #E53E3E;
}
.type-user {
    color: #38A169;
}

.user-prop-line {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 12px;
    color: #718096;
    margin-top: 4px;
}
.user-card-actions {
    margin-left: 12px;
}
.btn-trash-user {
    display: flex;
    align-items: center;
    padding: 6px;
    cursor: pointer;
    text-decoration: none;
}
.btn-trash-user:hover svg {
    stroke: #E53E3E;
}
</style>