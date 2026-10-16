const minimizeBtn = document.getElementById('minimizeComposeBtn'); 
const header = document.getElementById('composeHeader');

window.openComposeModal = function () {
    const overlay = document.getElementById('composeModalOverlay');

    if (!overlay) return;

    overlay.classList.remove('hidden');
    window.bindComposeForm();
};

window.openDraftComposeModal = function (url) {
    const overlay = document.getElementById('composeModalOverlay');
    const content = document.getElementById('composeModalContent');

    if (!overlay || !content) return;

    overlay.classList.remove('hidden');

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        content.innerHTML = html;
        window.bindComposeForm();

        window.Alpine?.initTree(content);
    })
    .catch(err => console.error('Open draft failed:', err));
};

window.openInlineCompose = function (url) {
    const container = document.getElementById('inlineComposeContainer');
    const content = document.getElementById('inlineComposeContent');

    if (!container || !content) return;

    container.classList.remove('hidden');
    container.classList.add('flex');

    container.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        content.innerHTML = html;

        window.bindComposeForm();

        window.Alpine?.initTree(content);
    })
    .catch(err => console.error('Open inline compose failed:', err));
};

window.closeComposeModal = function () {
    const overlay = document.getElementById('composeModalOverlay');

    if (!overlay) return;

    overlay.classList.add('hidden');
};

window.bindComposeForm = function () {
    const form = document.getElementById('composeForm');
    const editor = document.getElementById('composeEditor');
    const bodyHtmlInput = document.getElementById('bodyHtmlInput');
    const bodyTextInput = document.getElementById('bodyTextInput');
    console.log('bindComposeForm called');
    console.log('Found form:', form);

    if (!form) return;
    if (form.dataset.bound === 'true') {
        console.log('Form already bound');
        return;
    }

    form.dataset.bound = 'true';
    console.log('Binding submit handler');

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (editor && bodyHtmlInput && bodyTextInput) {
            bodyHtmlInput.value = editor.innerHTML;
            bodyTextInput.value = editor.innerText;
        }

        const submitter = e.submitter;
        const explicitFormAction = submitter?.getAttribute('formaction');
        const action = explicitFormAction || form.action;

        console.log('Submitting to action:', action);
        console.log('Submitter:', submitter);

        fetch(action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async res => {
            const contentType = res.headers.get('content-type') || '';

            if (!contentType.includes('application/json')) {
                const text = await res.text();
                console.error('Expected JSON but got:', text);
                throw new Error('Server did not return JSON.');
            }

            const data = await res.json();

            if (!res.ok) {
                throw data;
            }

            return data;
        })
        .then(data => {
            const draftIdInput = form.querySelector('[name="draft_id"]');

            if (data.action === 'saved') {
                if (draftIdInput && data.draft_id) {
                    draftIdInput.value = data.draft_id;
                }

                console.log('Draft saved');
            }

            if (data.action === 'sent') {
                console.log('Email sent');
                window.closeComposeModal();
                window.location.reload();
            }
        })
        .catch(err => {
            console.error('Compose submit failed:', err);
        });
    });
};

window.minimizeComposeModal = function () {
    const modal = document.getElementById('composeModalContent');
    const body = document.getElementById('composeBody');

    if (!modal || !body) return;

    modal.dataset.state = 'minimized';

    body.classList.add('hidden');

        modal.classList.remove(
        'top-6',
        'left-1/2',
        '-translate-x-1/2',
        'w-[90vw]',
        'h-[90vh]',
        'max-w-none',
        'h-[520px]',
        'w-[640px]',
        'rounded-2xl',
    );

    modal.classList.add(
        'bottom-6',
        'right-6',
        'w-1/5',
        'h-auto',
        'rounded-t-lg',
    );

    minimizeBtn?.classList.add('hidden');
    maximizeIcon?.classList.remove('hidden');
    restoreIcon?.classList.add('hidden');
    header?.classList.add('cursor-pointer');
};

window.expandComposeIfMinimized = function () {
    const modal = document.getElementById('composeModalContent');
    const body = document.getElementById('composeBody');

    if (!modal || !body) return;
    if (modal.dataset.state !== 'minimized') return;

    modal.dataset.state = 'normal';

    body.classList.remove('hidden');

    modal.classList.remove(
        'h-auto',
        'w-1/5',
        'rounded-t-lg',
        'top-6',
        'left-1/2',
        '-translate-x-1/2',
        'w-[90vw]',
        'h-[90vh]',
        'max-w-none'
    );

    modal.classList.add(
        'bottom-6',
        'right-6',
        'h-[520px]',
        'w-[640px]',
        'rounded-2xl'
    );

    minimizeBtn?.classList.remove('hidden');
    maximizeIcon?.classList.remove('hidden');
    restoreIcon?.classList.add('hidden');
    header?.classList.remove('cursor-pointer');
};

window.toggleMaximizeComposeModal = function () {
    const modal = document.getElementById('composeModalContent');
    const body = document.getElementById('composeBody');

    if (!modal) return;

    const isMaximized = modal.dataset.state === 'maximized';

    body?.classList.remove('hidden');
    minimizeBtn?.classList.remove('hidden');
    header?.classList.remove('cursor-pointer');

    if (isMaximized) {
        modal.classList.remove(
            'top-6',
            'left-1/2',
            '-translate-x-1/2',
            'w-[90vw]',
            'h-[90vh]',
            'max-w-none'
        );

        modal.classList.add(
            'bottom-6',
            'right-6',
            'w-[640px]',
            'h-[520px]'
        );

        maximizeIcon?.classList.remove('hidden');
        restoreIcon?.classList.add('hidden');

        modal.dataset.state = 'normal';
    } else {
        modal.classList.remove(
            'bottom-6',
            'right-6',
            'w-[640px]',
            'h-[520px]',
            'w-1/5',
            'h-auto',
            'rounded-t-lg'
        );

        modal.classList.add(
            'top-6',
            'left-1/2',
            '-translate-x-1/2',
            'w-[90vw]',
            'h-[90vh]',
            'max-w-none',
            'rounded-2xl'
        );

        maximizeIcon?.classList.add('hidden');
        restoreIcon?.classList.remove('hidden');

        modal.dataset.state = 'maximized';
    }
};
