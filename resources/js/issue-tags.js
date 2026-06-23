export default function issueTags({ initialTags, availableTags, attachUrlTemplate, detachUrlTemplate }) {
    return {
        availableTags,
        attachUrlTemplate,
        detachUrlTemplate,
        error: '',
        open: false,
        processingIds: [],
        tags: initialTags,

        unattachedTags() {
            return this.availableTags.filter((tag) => !this.isAttached(tag));
        },

        isAttached(tag) {
            return this.tags.some((attachedTag) => attachedTag.id === tag.id);
        },

        isProcessing(tag) {
            return this.processingIds.includes(tag.id);
        },

        tagStyle(tag) {
            const color = tag.color || '#374151';

            return `background-color: ${color}1A; border-color: ${color}; color: ${color};`;
        },

        async attach(tag) {
            await this.syncTag(tag, 'POST', this.attachUrlTemplate);
        },

        async detach(tag) {
            await this.syncTag(tag, 'DELETE', this.detachUrlTemplate);
        },

        async syncTag(tag, method, urlTemplate) {
            this.error = '';
            this.processingIds.push(tag.id);

            try {
                const response = await fetch(urlTemplate.replace('__TAG__', tag.id), {
                    method,
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to update tags.');
                }

                const data = await response.json();

                this.tags = data.tags;
                this.open = false;
            } catch (error) {
                this.error = error.message;
            } finally {
                this.processingIds = this.processingIds.filter((tagId) => tagId !== tag.id);
            }
        },
    };
}
