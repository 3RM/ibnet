<?php
return [
    // User
    'user.rememberMeDuration' => 3600 * 24 * 30,  // 1 month

	// Emails
    'email.admin' => 'admin@ibnet.org',
    'email.noReply' => 'no-reply@ibnet.org',
    'email.team' => 'team@ibnet.org',
    'email.blogDigestEmail' => 'no-reply@ibnet.org',
    'email.systemTitle' => 'Notification from Independent Baptist Network',
    'email.systemSubject' => 'Notification from IBNet.org',

    // Token Expirations
    'tokenExpire.passwordReset' => 3600,        // 60 * 60 (1 hour)
    'tokenExpire.newEmail' => 604800,	        // 7 * 24 * 60 * 60 (1 week)
    'tokenExpire.profileTransfer' => 604800,
    'tokenExpire.groupTransfer' => 604800,
    'tokenExpire.groupInviteJoin' => 2419200,   // 4 * 7 * 24 * 60 * 60 (4 weeks)

    // Missionary update group alert delay before send
    'delay.missionaryUpdate' => 900,            // 15 * 60 (15 mins)

    // API Keys
    'apiKey.google-client' => 'AIzaSyBtE_w8tdgpSwEse8qTsEZoR-Vw776xg6I',
    'apiKey.google-server' => 'AIzaSyDylFZ5rIu9zpubJ5TKV2WsmkEybS4t3HA',
    'apiKey.google-no-restrictions' => 'AIzaSyDvsfbC0RbT8TLJFYFxBiCYzWgUd51xDg8',

    // URLs
    'url.vimeoOembed' => 'https://vimeo.com/api/oembed.json?url=',
    'url.youtubeOembed' => 'http://www.youtube.com/oembed?format=json&url=',
];