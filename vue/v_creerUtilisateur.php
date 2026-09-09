<div class="admin-form-container">
    <div class="top-nav-bar">
        <a href="index.php?uc=reglages&action=gererUtilisateurs" class="back-link">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="nav-title">Add User</h1>
    </div>

    <?php if (!empty($erreur)) : ?>
        <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form action="index.php?uc=reglages&action=validerAjoutUtilisateur" method="POST">
        <div class="input-block">
            <label class="field-label">Name *</label>
            <input type="text" name="name" class="input-rounded" placeholder="Full Name" required>
        </div>

        <div class="input-block">
            <label class="field-label">Email *</label>
            <input type="email" name="email" class="input-rounded" placeholder="email@example.com" required>
        </div>

        <div class="input-block">
            <label class="field-label">Role (Job Title)</label>
            <input type="text" name="role" class="input-rounded" placeholder="Doctor, Intern, Researcher...">
        </div>

        <div class="input-block">
            <label class="field-label">Access Level (user_type)</label>
            <select name="user_type" class="input-rounded select-role">
                <option value="user">user</option>
                <option value="admin">admin</option>
                <option value="super_admin">super_admin</option>
            </select>
        </div>

        <div class="input-block">
            <label class="field-label">Institution</label>
            <input type="text" name="institution" class="input-rounded" value="Uppsala University">
        </div>

        <button type="submit" class="btn-submit-user">Create User</button>
    </form>
</div>

<style>
.admin-form-container { 
    max-width: 480px; 
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
    text-decoration: none; 
    display: flex; 
    align-items: center; 
}
.nav-title { 
    position: absolute; 
    left: 50%; 
    transform: translateX(-50%); 
    font-size: 18px; 
    font-weight: 600; 
    color: #1A202C; 
    margin: 0; 
}
.input-block { 
    margin-bottom: 16px; 
}
.field-label { 
    display: block; 
    font-size: 13px; 
    font-weight: 600; 
    color: #4A5568; 
    margin-bottom: 6px; 
}
.input-rounded {
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
.input-rounded:focus { 
    border-color: #5B7FDE; 
}
.select-role { 
    appearance: none; 
    cursor: pointer; 
}
.btn-submit-user {
    width: 100%;
    height: 46px;
    background-color: #5B7FDE;
    color: #FFFFFF;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 23px;
    cursor: pointer;
    margin-top: 14px;
    box-shadow: 0 4px 10px rgba(91, 127, 222, 0.3);
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