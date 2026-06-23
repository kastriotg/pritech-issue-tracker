export default function issueComments({ indexUrl, storeUrl }) {
    return {
        comments: [],
        errors: {},
        form: {
            body: '',
        },
        indexUrl,
        isLoading: false,
        isSubmitting: false,
        nextPageUrl: null,
        storeUrl,

        init() {
            this.loadComments(this.indexUrl, true);
        },

        async loadComments(url, replace = false) {
            if (!url || this.isLoading) {
                return;
            }

            this.isLoading = true;

            try {
                const response = await fetch(url, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to load comments.');
                }

                const payload = await response.json();

                this.comments = replace ? payload.data : [...this.comments, ...payload.data];
                this.nextPageUrl = payload.links.next;
            } finally {
                this.isLoading = false;
            }
        },

        async submit() {
            this.errors = {};
            this.isSubmitting = true;

            try {
                const response = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(this.form),
                });

                const payload = await response.json();

                if (response.status === 422) {
                    this.errors = payload.errors || {};

                    return;
                }

                if (!response.ok) {
                    throw new Error('Unable to add comment.');
                }

                this.comments = [payload.data, ...this.comments];
                this.form.body = '';
            } finally {
                this.isSubmitting = false;
            }
        },
    };
}
