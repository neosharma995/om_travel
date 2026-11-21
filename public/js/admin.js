document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('adminMenuBtn');
    const sidebar = document.getElementById('adminSidebar');

    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('is-open');
        });
    }

    document.querySelectorAll('[data-preview-input]').forEach((input) => {
        input.addEventListener('change', (event) => {
            const targetId = input.getAttribute('data-preview-input');
            const previewImg = targetId ? document.getElementById(targetId) : null;
            const file = event.target.files && event.target.files[0];

            if (!previewImg || !file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                if (e.target && typeof e.target.result === 'string') {
                    previewImg.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        });
    });
});
