

import Alpine from 'alpinejs';
import issueComments from './issue-comments';
import issueMembers from './issue-members';
import issueTags from './issue-tags';

window.Alpine = Alpine;
window.issueComments = issueComments;
window.issueMembers = issueMembers;
window.issueTags = issueTags;

Alpine.start();
