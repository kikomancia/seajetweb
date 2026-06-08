# Security Report — SeaJet Web Application
**Scan Date:** 2026-05-29  
**Scanned By:** Claude Code (AI-assisted static analysis)  
**Scope:** Full codebase — `c:\xampp\htdocs\seajetweb`  
**Trigger:** Investigation of suspected bot-flooding activity on the contact form

---

## Executive Summary

A full codebase scan was conducted to identify the root cause of suspected bot flooding and to assess overall security posture. The site's contact form (`msg.php`) was found to have **no bot protection** — making it trivially easy for automated scripts to flood the inquiry dashboard with junk submissions.

**No email-sending code was found** anywhere in the codebase. The flooding is limited to database spam (fake entries in `sj_messages`), not outgoing email abuse.

Four security fixes were applied during this session. Three medium-to-low risk items remain open and are documented below.

---

## Scope — Files Scanned

| File | Type | Purpose |
|---|---|---|
| `contact_us.html` | HTML | Public contact form |
| `sj_components/msg.php` | PHP | Contact form POST handler |
| `sj_components/javsript.js` | JavaScript | Form submission (AJAX) |
| `admin/index.php` | PHP | Admin login page |
| `admin/php_files/login.php` | PHP | Admin login handler |
| `admin/php_files/save_user.php` | PHP | Admin user creation |
| `admin/php_files/toggle_user.php` | PHP | Admin user toggle |
| `admin/php_files/update_inquiry_status.php` | PHP | Inquiry status update |
| `admin/php_files/fetch_inquiries.php` | PHP | Inquiry fetch handler |
| `admin/inquiries.php` | PHP | Admin inquiries dashboard |

---

## Findings Before Fixes

### CRITICAL

#### C-01 — No Bot Protection on Contact Form
- **File:** `contact_us.html`, `sj_components/msg.php`
- **Description:** The public contact form accepted unlimited POST submissions with no rate limiting, no CAPTCHA, no honeypot, and no timing validation. Any automated script could flood the `sj_messages` database table with thousands of fake entries per minute.
- **Impact:** Dashboard spam flooding, database bloat, potential DoS on inquiry management
- **Status:** **FIXED**

#### C-02 — No Rate Limiting on `msg.php`
- **File:** `sj_components/msg.php`
- **Description:** No IP-based or session-based throttling. A single source could submit indefinitely without restriction.
- **Impact:** Unlimited junk entries into `sj_messages` table
- **Status:** **FIXED**

---

### HIGH

#### H-01 — Database Credentials Hardcoded in Source File
- **File:** `sj_components/msg.php` — Line 9
- **Description:** Production database username and password are stored in plaintext directly in a PHP file within the web root.
  ```
  $connect = new mysqli('localhost', 'seajetin_admindb', '@DminDATABASE!23', 'seajetin_admin_acct');
  ```
- **Impact:** If the server ever misconfigures PHP (e.g., exposes `.php` as plain text), credentials are fully exposed. Also visible to anyone with file system read access.
- **Recommendation:** Move credentials to a config file outside the web root (e.g., `C:\config\db.php`) and `require` it. Alternatively, use environment variables.
- **Status:** **OPEN**

#### H-02 — No Brute Force Protection on Admin Login
- **File:** `admin/index.php`, `admin/php_files/login.php`
- **Description:** The admin login form accepts unlimited password attempts with no lockout, no rate limiting, and no CAPTCHA. Vulnerable to automated credential stuffing and brute force attacks.
- **Impact:** Admin account compromise
- **Recommendation:** Add login attempt counter in session or database; lock out after 5 failed attempts for 15 minutes. Add a honeypot or time token (same pattern applied to contact form).
- **Status:** **OPEN**

---

### MEDIUM

#### M-01 — No Input Length Limits (Before Fix)
- **File:** `sj_components/msg.php`
- **Description:** Form fields accepted unlimited-length input, allowing oversized payloads to be inserted into the database.
- **Impact:** Database storage abuse, potential memory issues
- **Status:** **FIXED**

#### M-02 — No CSRF Protection on Admin POST Handlers
- **Files:** `admin/php_files/save_user.php`, `admin/php_files/toggle_user.php`, `admin/php_files/update_inquiry_status.php`
- **Description:** Admin-side POST endpoints do not validate a CSRF token. A logged-in admin visiting a malicious page could unknowingly trigger actions.
- **Impact:** Unauthorized state changes if admin is socially engineered
- **Recommendation:** Add `session_start()` + CSRF token generation on admin pages, validate token in all POST handlers.
- **Status:** **OPEN**

---

### LOW / INFORMATIONAL

#### L-01 — No Email Sending Found (Informational)
- **Description:** No `mail()`, `PHPMailer`, SMTP, or any email-sending function was found anywhere in the codebase. The contact form only saves to the database — it does not send outgoing emails.
- **Impact:** None (confirms flooding is dashboard-only, not email relay abuse)
- **Status:** Informational — no action required

#### L-02 — Whitelist Validation Present on Admin Filters (Positive Finding)
- **Files:** `admin/php_files/fetch_inquiries.php`, `admin/php_files/update_inquiry_status.php`
- **Description:** Filter and status parameters are validated against a whitelist of allowed values before use.
- **Status:** Good practice — no action required

#### L-03 — Prepared Statements Used Correctly (Positive Finding)
- **File:** `sj_components/msg.php`
- **Description:** All database inserts use `prepare()` + `bind_param()` — SQL injection is not possible through the contact form.
- **Status:** Good practice — no action required

---

## Fixes Applied (This Session)

### Fix 1 — Honeypot Field
- **Files changed:** `contact_us.html`, `sj_components/msg.php`
- **What it does:** A hidden `<input name="website">` field is added to the form. It is invisible to human users but visible to bots that auto-fill all fields. If the field has any value, `msg.php` returns a fake success response and discards the submission silently.

### Fix 2 — Time-Based Token
- **Files changed:** `contact_us.html`, `sj_components/javsript.js`, `sj_components/msg.php`
- **What it does:** JavaScript records a Unix timestamp (`form_token`) when the contact page loads. `msg.php` rejects any submission where less than 3 seconds elapsed since the token was issued — bots submit instantly, humans do not.

### Fix 3 — IP Rate Limiting
- **File changed:** `sj_components/msg.php`
- **What it does:** After a successful submission, the current time is written to a temp file keyed by a hash of the submitter's IP address. Subsequent submissions from the same IP within 10 minutes are rejected with HTTP status `429`. The client receives a friendly "please wait" alert via SweetAlert2.

### Fix 4 — Input Length Limits
- **File changed:** `sj_components/msg.php`
- **What it does:** All POST values are truncated before validation and database insertion:
  - Name: max 100 characters
  - Email: max 150 characters
  - Phone: max 20 characters
  - Message: max 1,000 characters

---

## Bot Attack Flow — Before vs After

### Before Fixes
```
Bot → POST sj_components/msg.php → Inserted into sj_messages → Repeat ∞
```

### After Fixes
```
Bot (auto-fills all fields) → Honeypot triggered → Fake 200, discarded
Bot (submits instantly)     → Time token < 3s   → Fake 200, discarded
Bot (submits repeatedly)    → IP rate limit hit  → 429, discarded
Bot (sends huge payload)    → Truncated at input → Harmless
```

---

## Open Recommendations

| Priority | Item | Effort |
|---|---|---|
| High | Move DB credentials to config file outside web root | Low |
| High | Add brute force protection to admin login | Medium |
| Medium | Add CSRF tokens to admin POST handlers | Medium |
| Low | Consider Cloudflare free plan (DNS-level bot filtering, works with cPanel) | Low |
| Low | Enable ModSecurity in cPanel if available (Security → ModSecurity) | Very Low |

---

## Environment Notes

- **Hosting:** cPanel (shared hosting)
- **Server:** XAMPP / Apache + PHP
- **Database:** MySQL via MySQLi
- **Docker / Root Access:** Not available — server-level WAF (SafeLine, etc.) not applicable
- **Cloudflare:** Recommended as the highest-impact remaining step; requires only a DNS change at the domain registrar

---

*Report generated by Claude Code — AI-assisted static analysis. Manual penetration testing is recommended for production hardening.*
