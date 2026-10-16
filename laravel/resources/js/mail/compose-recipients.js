window.composeRecipients = function (initial) {
    return {
        to: initial.to ?? [],
        cc: initial.cc ?? [],
        bcc: initial.bcc ?? [],

        toInput: '',
        ccInput: '',
        bccInput: '',

        showCc: (initial.cc ?? []).length > 0,
        showBcc: (initial.bcc ?? []).length > 0,
        recipientsExpanded: true,

        visibleToCount() {
            return this.recipientsExpanded ? this.to.length : Math.min(this.to.length, 1);
        },

        hiddenNormalCount() {
            if (this.recipientsExpanded) return 0;

            const hiddenTo = Math.max(this.to.length - 1, 0);
            const hiddenCc = this.cc.length;

            return hiddenTo + hiddenCc;
        },

        hiddenBccCount() {
            if (this.recipientsExpanded) return 0;

            return this.bcc.length;
        },

        isValidEmail(value) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        },

        addRecipient(type) {
            const inputKey = `${type}Input`;
            let value = this[inputKey].trim().replace(',', '');

            if (!value) return;

            if (!this.isValidEmail(value)) {
                return;
            }

            if (!this[type].includes(value)) {
                this[type].push(value);
            }

            this[inputKey] = '';
        },

        openCc() {
            this.recipientsExpanded = true;
            this.showCc = true;

            this.$nextTick(() => {
                this.$refs.ccInput?.focus();
            });
        },

        openBcc() {
            this.recipientsExpanded = true;
            this.showBcc = true;

            this.$nextTick(() => {
                this.$refs.bccInput?.focus();
            });
        },

        collapseRecipients() {
            this.addRecipient('to');
            this.addRecipient('cc');
            this.addRecipient('bcc');

            this.recipientsExpanded = false;

            if (this.cc.length === 0) {
                this.showCc = false;
            }

            if (this.bcc.length === 0) {
                this.showBcc = false;
            }
        },
    }
}