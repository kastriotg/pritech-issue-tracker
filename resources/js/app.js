

import Alpine from 'alpinejs';
import issueComments from './issue-comments';
import issueTags from './issue-tags';

window.Alpine = Alpine;
window.issueComments = issueComments;
window.issueTags = issueTags;

Alpine.start();
