window.resizeEmailFrame = function (iframe) {
    try {
        const doc = iframe.contentDocument || iframe.contentWindow.document;

        if (!doc) return;

        const height = Math.max(
            doc.body.scrollHeight,
            doc.documentElement.scrollHeight
        );

        iframe.style.height = `${height}px`;
    } catch (error) {
        console.warn('Could not resize email iframe:', error);
        iframe.style.height = '900px';
    }
};

window.emailActions = function(config) {
    return {
        starred: config.starred,
        starUrl: config.starUrl,

        async toggleStar() {
            try {
                const response = await fetch(this.starUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.starred = data.starred;
                }
            } catch (error) {
                console.error('Failed to toggle star', error);
            }
        }
    };
};