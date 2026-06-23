export default function issueSearch({ action, initialSearch = '' }) {
    return {
        action,
        search: initialSearch,
        timeout: null,
        isLoading: false,

        queueSearch() {
            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.fetchIssues(), 350);
        },

        async fetchIssues() {
            const form = this.$refs.form;
            const results = this.$refs.results;
            const url = new URL(this.action, window.location.origin);

            new FormData(form).forEach((value, key) => {
                if (value !== '') {
                    url.searchParams.set(key, value);
                }
            });

            this.isLoading = true;

            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (! response.ok) {
                    form.submit();

                    return;
                }

                results.innerHTML = await response.text();
                window.history.replaceState({}, '', url);
            } finally {
                this.isLoading = false;
            }
        },
    };
}
