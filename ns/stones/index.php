<?php
// Content negotiation for the STONES ontology namespace IRI.
// This file acts as the DirectoryIndex for ns/stones/, handling requests
// that reach this directory before mod_rewrite can intercept them.
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';

// The answer depends on Accept: don't let shared caches reuse it (see ../.htaccess).
header('Vary: Accept');
header('Cache-Control: private, no-cache');

if (
    strpos($accept, 'text/turtle') !== false ||
    strpos($accept, 'application/x-turtle') !== false ||
    strpos($accept, 'application/rdf+xml') !== false ||
    strpos($accept, 'application/owl+xml') !== false
) {
    header('Location: /ns/stones.ttl', true, 303);
} else {
    header('Location: /ns/stones.html', true, 303);
}
exit;
