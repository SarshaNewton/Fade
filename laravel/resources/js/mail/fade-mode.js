const mailApp = document.getElementById('fadeModeToggle');

if (mailApp) {
   let isFadeMode = window.mailConfig?.isFadeMode ?? false;

    const classicActive = window.mailConfig.classicActive;
    const classicInactive = window.mailConfig.classicInactive;
    const fadeActive = window.mailConfig.fadeActive;
    const fadeInactive = window.mailConfig.fadeInactive;

    const classicIcon = document.getElementById('classicModeIcon');
    const fadeIcon = document.getElementById('fadeModeIcon');

    const classicGradientLayer = document.getElementById('classicGradientLayer');
    const fadeGradientLayer = document.getElementById('fadeGradientLayer');

    const fadeModeToggle = document.getElementById('fadeModeToggle');
    const fadeModeSlider = document.getElementById('fadeModeSlider');
    const gradientShell = document.querySelector('.gradient-shell');

    const activeNavItems = document.querySelectorAll(
        'nav a.font-semibold'
    );

    if (fadeModeToggle && fadeModeSlider) {
        fadeModeToggle.addEventListener('click', async () => {
            isFadeMode = !isFadeMode;

            document.body.classList.toggle('is-fade-mode', isFadeMode);
            document.body.classList.toggle('is-classic-mode', !isFadeMode);

            if (classicIcon && fadeIcon) {
                    classicIcon.src = isFadeMode ? classicInactive : classicActive;
                    fadeIcon.src = isFadeMode ? fadeActive : fadeInactive;
            }

            // slider
            fadeModeSlider.classList.remove(
                'left-0',
                'left-1/2',
                'bg-classic-mode',
                'bg-fade-mode'
            );

            fadeModeSlider.classList.add(
                isFadeMode ? 'left-1/2' : 'left-0',
                isFadeMode ? 'bg-fade-mode' : 'bg-classic-mode'
            );

            // page gradient
            if (isFadeMode) {
                classicGradientLayer?.classList.remove('opacity-100');
                classicGradientLayer?.classList.add('opacity-0');

                fadeGradientLayer?.classList.remove('opacity-0');
                fadeGradientLayer?.classList.add('opacity-100');
            } else {
                classicGradientLayer?.classList.remove('opacity-0');
                classicGradientLayer?.classList.add('opacity-100');

                fadeGradientLayer?.classList.remove('opacity-100');
                fadeGradientLayer?.classList.add('opacity-0');
            }

            // Nav background
            activeNavItems.forEach(item => {
                item.classList.remove(
                    'bg-fade-ai-menu',
                    'bg-fade-classic-menu/40',
                    'text-fade-text-highlight',
                    'text-fade-text-primary'
                );

                if (isFadeMode) {
                    item.classList.add(
                        'bg-fade-ai-menu',
                        'text-fade-text-highlight'
                    );
                } else {
                    item.classList.add(
                        'bg-fade-classic-menu/40',
                        'text-fade-text-primary'
                    );
                }
            });

            // save session through AJAX
            const mailboxRegion = document.getElementById('mailboxRegion');
            const mailboxOverlay = document.getElementById('mailboxLoadingOverlay');

            mailboxOverlay?.classList.remove('hidden');
            mailboxOverlay?.classList.add('flex');

            mailboxRegion?.classList.add('is-switching');

            await fetch(window.mailConfig.fadeModeToggleUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': window.mailConfig.csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const response = await fetch(window.location.href, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            const html = await response.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const newRegion = doc.getElementById('mailboxRegion');

            if (newRegion && mailboxRegion) {
                mailboxRegion.innerHTML = newRegion.innerHTML;

                if (window.Alpine) {
                    Alpine.initTree(mailboxRegion);
                }

                mailboxRegion.classList.remove('is-switching');

                requestAnimationFrame(() => {
                    mailboxRegion.classList.remove('opacity-0');
                });
            }
        });
    }
}

