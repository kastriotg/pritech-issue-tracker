export default function issueMembers({ initialMembers, availableUsers, attachUrlTemplate, detachUrlTemplate }) {
    return {
        attachUrlTemplate,
        availableUsers,
        detachUrlTemplate,
        error: '',
        members: initialMembers,
        open: false,
        processingIds: [],

        unassignedUsers() {
            return this.availableUsers.filter((user) => !this.isAssigned(user));
        },

        isAssigned(user) {
            return this.members.some((member) => member.id === user.id);
        },

        isProcessing(user) {
            return this.processingIds.includes(user.id);
        },

        async attach(user) {
            await this.syncMember(user, 'POST', this.attachUrlTemplate);
        },

        async detach(user) {
            await this.syncMember(user, 'DELETE', this.detachUrlTemplate);
        },

        async syncMember(user, method, urlTemplate) {
            this.error = '';
            this.processingIds.push(user.id);

            try {
                const response = await fetch(urlTemplate.replace('__USER__', user.id), {
                    method,
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('Unable to update members.');
                }

                const data = await response.json();

                this.members = data.members;
                this.open = false;
            } catch (error) {
                this.error = error.message;
            } finally {
                this.processingIds = this.processingIds.filter((userId) => userId !== user.id);
            }
        },
    };
}
