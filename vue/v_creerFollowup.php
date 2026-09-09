<div class="followup-form-container">
    <div class="top-nav-bar">
        <a href="index.php?uc=gererPatients&action=voirProfilPatient&id=<?= urlencode($patient_id) ?>" class="back-link">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2D3748" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </a>
        <h1 class="page-title">Add Follow Up</h1>
    </div>

    <?php if (!empty($erreur)) : ?>
        <div class="alert-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form action="index.php?uc=gererFollowup&action=validerAjout" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="patient_id" value="<?= htmlspecialchars($patient_id) ?>">

        <!-- 1. Follow-up Name -->
        <div class="input-block">
            <label class="field-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Follow-up Name <span class="required-star">*</span>
            </label>
            <input type="text" name="name" class="input-rounded" placeholder="Ex: PreOp, 1 month ..." required>
        </div>

        <!-- 2. Date -->
        <div class="input-block">
            <label class="field-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Date <span class="required-star">*</span>
            </label>
            <div class="date-input-wrapper">
                <input type="date" name="date" class="input-rounded date-input" value="<?= date('Y-m-d') ?>" required>
            </div>
        </div>

        <!-- 3. Notes -->
        <div class="input-block">
            <label class="field-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                Notes (optional)
            </label>
            <textarea name="notes" class="input-rounded textarea-rounded" placeholder="Enter notes..."></textarea>
        </div>

        <!-- 4. Photos -->
        <div class="input-block">
            <label class="field-label">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#5B7FDE" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                Photos (optional)
            </label>
            <div class="photos-grid">
                <?php for ($i = 1; $i <= 3; $i++) : ?>
                    <label class="photo-box" for="photo_input_<?= $i ?>">
                        <input type="file" name="photo_<?= $i ?>" id="photo_input_<?= $i ?>" accept="image/*" style="display:none;" onchange="previewPhoto(this, <?= $i ?>)">
                        <div class="photo-placeholder" id="placeholder_<?= $i ?>">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#718096" stroke-width="1.8"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                            <span>Photo <?= $i ?></span>
                        </div>
                        <img id="img_preview_<?= $i ?>" class="photo-preview-thumb" style="display:none;" alt="Preview <?= $i ?>">
                    </label>
                <?php endfor; ?>
            </div>
        </div>

        <button type="submit" class="btn-next">Next</button>
    </form>
</div>

<style>
.followup-form-container { 
    max-width: 520px; 
    margin: 20px auto 40px auto; 
    padding: 0 16px; 
}
.top-nav-bar { 
    display: flex; 
    align-items: center; 
    position: relative; 
    margin-bottom: 28px; 
}
.back-link { 
    color: #2D3748; 
    text-decoration: none; 
    display: flex; 
    align-items: center; 
}
.page-title { 
    position: absolute; 
    left: 50%; 
    transform: translateX(-50%); 
    font-size: 18px; 
    font-weight: 600; 
    margin: 0; 
    color: #1A202C; 
}
.input-block { 
    margin-bottom: 20px; 
}
.field-label { 
    display: flex; 
    align-items: center; 
    gap: 7px; 
    font-size: 13px; 
    font-weight: 600; 
    color: #4A5568; 
    margin-bottom: 8px; 
}
.required-star { 
    color: #E53E3E; 
    font-size: 14px; 
}
.input-rounded { 
    width: 100%; 
    height: 46px; 
    padding: 0 18px; 
    border-radius: 23px; 
    border: 1px solid #E2E8F0; 
    background-color: #FFFFFF; 
    font-size: 14px; 
    color: #2D3748; 
    outline: none; 
    box-sizing: border-box; 
    transition: border-color 0.2s; 
}
.input-rounded:focus { 
    border-color: #5B7FDE; 
}
.input-rounded::placeholder { 
    color: #A0AEC0; 
}
.textarea-rounded { 
    height: 84px; 
    border-radius: 16px; 
    padding: 12px 18px; 
    resize: none; 
}
.date-input-wrapper {
    position: relative; 
}
.date-input { 
    font-family: inherit; 
    color: #4A5568; 
}
.photos-grid { 
    display: flex; 
    gap: 12px; 
}
.photo-box { 
    flex: 1; 
    height: 90px; 
    background-color: #FFFFFF; 
    border: 1px solid #E2E8F0; 
    border-radius: 14px; 
    display: flex; 
    flex-direction: column; 
    justify-content: center; 
    align-items: center; 
    cursor: pointer; 
    overflow: hidden; 
    position: relative; 
    transition: border-color 0.2s; 
}
.photo-box:hover { 
    border-color: #5B7FDE; 
}
.photo-placeholder { 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 6px; 
}
.photo-placeholder span { 
    font-size: 12px; 
    color: #718096; 
}
.photo-preview-thumb { 
    width: 100%; 
    height: 100%; 
    object-fit: cover; 
}
.btn-next { 
    width: 100%; 
    height: 48px; 
    background-color: #C8D7FC; 
    color: #3B54C8; 
    font-size: 15px; 
    font-weight: 600; 
    border: none; 
    border-radius: 24px; 
    cursor: pointer; 
    margin-top: 14px; 
    transition: background-color 0.2s, color 0.2s; 
}
.btn-next:hover { 
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
function previewPhoto(input, index) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('img_preview_' + index);
            const placeholder = document.getElementById('placeholder_' + index);
            img.src = e.target.result;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>