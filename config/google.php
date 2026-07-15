<?php
// config/google.php
// ─────────────────────────────────────────────────────────────────────────────
// STEP: Paste your Google Client ID below.
// Get it from: https://console.cloud.google.com/
//   → APIs & Services → Credentials → Create OAuth 2.0 Client ID
//   → Application type: Web application
//   → Authorised JavaScript origins: http://localhost:8000 (for local dev)
//   → Authorised redirect URIs: http://localhost:8000/api/auth.php
// ─────────────────────────────────────────────────────────────────────────────

define('GOOGLE_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID_HERE.apps.googleusercontent.com');
