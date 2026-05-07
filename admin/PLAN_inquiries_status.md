# Plan: Inquiry Status System

## Overview
Add a `status` field to each inquiry so admins can track whether a message has been read or hidden.

---

## 1. Database Change

**Table:** `sj_messages`

Add a new column:
```sql
ALTER TABLE sj_messages ADD COLUMN status ENUM('unread', 'read', 'hidden') NOT NULL DEFAULT 'unread';
```

| Value    | Meaning                                      |
|----------|----------------------------------------------|
| `unread` | New message, not yet viewed (default)        |
| `read`   | Admin has opened/marked the message as read  |
| `hidden` | Admin has hidden it — removed from main view |

---

## 2. New PHP Handler

**File:** `php_files/update_inquiry_status.php`

- Accepts POST: `id` (message ID) and `status` (`read` / `hidden` / `unread`)
- Validates both fields
- Updates `sj_messages.status` where `id = ?`
- Returns JSON `{ statusCode: 200 }` or error

---

## 3. Updated PHP Files

### `php_files/fetch_inquiries.php`
- Default query: fetch all messages where `status != 'hidden'` (All tab)
- Accept an optional `?filter=hidden` to show hidden messages
- Return `status` column in the result so the frontend can render badges

### `dashboard.php`
- Update the unread stat card to count `WHERE status = 'unread'`
- Rename stat label from "Today's Inquiries" to "Unread Inquiries" (or add a separate card)

---

## 4. Inquiries Page — UI Changes (`inquiries.php`)

### Filter Tabs (above the table)
Three clickable tabs that filter the table:

```
[ All ]  [ Unread ]  [ Read ]  [ Hidden ]
```

- Active tab is highlighted
- Each tab filters the DataTable client-side (or triggers a fetch)
- Tab badge shows count per status (e.g., "Unread (5)")

### Status Column (new column in table)
Add a `Status` column that shows a colored badge:

| Status   | Badge color  |
|----------|-------------|
| `unread` | Blue         |
| `read`   | Green        |
| `hidden` | Gray         |

### Actions Column (new column in table)
Each row gets action buttons:

| Button          | Visible when    | Action                          |
|-----------------|-----------------|----------------------------------|
| Mark as Read    | status = unread | Sets status → `read`            |
| Mark as Unread  | status = read   | Sets status → `unread`          |
| Hide            | not hidden      | Sets status → `hidden`          |
| Unhide          | status = hidden | Sets status → `unread`          |

- All actions use AJAX (no page reload)
- On success: update the row badge and button in-place, or reload the table
- SweetAlert confirmation before hiding

---

## 5. Sidebar — Unread Badge

In `includes/sidebar.php`, next to the Inquiries nav link, show a live count of unread messages:

```
💬 Inquiries   [5]
```

- Badge is fetched once on page load via PHP (not AJAX)
- Only show the badge if count > 0

---

## 6. Implementation Order (when coding)

1. Run the `ALTER TABLE` SQL on the database
2. Create `php_files/update_inquiry_status.php`
3. Update `php_files/fetch_inquiries.php` to return `status`
4. Update `dashboard.php` stat query (unread count)
5. Update `inquiries.php` — add Status column, Actions column, filter tabs, AJAX handlers
6. Update `includes/sidebar.php` — unread badge

---

## Notes
- Hidden messages should NOT appear in the default view but must be accessible via the Hidden tab
- Deleting messages is out of scope for now (hidden = soft hide)
- No email reply feature in this phase
