<?php
$pageTitle  = 'Client Inquiries';
$activePage = 'inquiries';
require 'includes/auth.php';
require 'includes/db.php';

$unreadCount = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_messages WHERE status = 'unread'"))['c'];
mysqli_close($connect);

$extraScripts = <<<'HTML'
<script>
// ── State ─────────────────────────────────────────
var allRows      = [];
var filteredRows = [];
var currentPage  = 1;
var perPage      = 15;
var sortCol      = 'date_time';
var sortAsc      = false;
var searchQuery  = '';
var currentFilter = 'all';

// ── Helpers ───────────────────────────────────────
function escHtml(str) {
    var d = document.createElement('div');
    d.textContent = str != null ? String(str) : '';
    return d.innerHTML;
}

function formatDate(dt) {
    var d = new Date(dt.replace(' ', 'T'));
    return d.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
}

function formatTime(dt) {
    var d = new Date(dt.replace(' ', 'T'));
    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}

function statusBadge(status) {
    var map = {
        unread: ['inq-unread', 'Unread'],
        read:   ['inq-read',   'Read'],
        hidden: ['inq-hidden', 'Hidden']
    };
    var info = map[status] || ['inq-unread', status];
    return '<span class="status-badge ' + info[0] + '">' + info[1] + '</span>';
}

function actionButtons(id, status) {
    var btns = '';
    if (status === 'unread') {
        btns += '<button class="btn-action btn-mark-read" onclick="updateStatus(' + id + ',\'read\')">'
              + '<i class="fas fa-check me-1"></i>Read</button> ';
    } else if (status === 'read') {
        btns += '<button class="btn-action btn-mark-unread" onclick="updateStatus(' + id + ',\'unread\')">'
              + '<i class="fas fa-envelope me-1"></i>Unread</button> ';
    }
    if (status !== 'hidden') {
        btns += '<button class="btn-action btn-hide" onclick="confirmHide(' + id + ')">'
              + '<i class="fas fa-eye-slash me-1"></i>Hide</button>';
    } else {
        btns += '<button class="btn-action btn-unhide" onclick="updateStatus(' + id + ',\'unread\')">'
              + '<i class="fas fa-eye me-1"></i>Unhide</button>';
    }
    return btns;
}

// ── Tab counts ────────────────────────────────────
function updateTabCounts(counts) {
    var total = (counts.unread || 0) + (counts.read || 0);
    document.getElementById('badge-all').textContent    = total > 0 ? total : '';
    document.getElementById('badge-unread').textContent = counts.unread > 0 ? counts.unread : '';
    document.getElementById('badge-read').textContent   = counts.read   > 0 ? counts.read   : '';
    document.getElementById('badge-hidden').textContent = counts.hidden > 0 ? counts.hidden : '';
}

// ── Render rows ───────────────────────────────────
function renderRows(rows) {
    var tbody = document.getElementById('tableBody');
    if (!rows || !rows.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-5">No inquiries found.</td></tr>';
        return;
    }
    var html = '';
    for (var i = 0; i < rows.length; i++) {
        var row = rows[i];
        html += '<tr>';
        html += '<td class="ps-4">'
              + '<span class="fw-medium">' + escHtml(formatDate(row.date_time)) + '</span><br>'
              + '<small class="text-muted">' + escHtml(formatTime(row.date_time)) + '</small></td>';
        html += '<td class="fw-medium">' + escHtml(row.cli_name) + '</td>';
        html += '<td class="text-muted">' + escHtml(row.cli_email) + '</td>';
        html += '<td class="text-muted">' + escHtml(row.cli_num) + '</td>';
        html += '<td class="msg-cell text-muted">' + escHtml(row.cli_message) + '</td>';
        html += '<td>' + statusBadge(row.status) + '</td>';
        html += '<td class="text-nowrap">' + actionButtons(row.id, row.status) + '</td>';
        html += '</tr>';
    }
    tbody.innerHTML = html;
}

// ── Pagination ────────────────────────────────────
function renderPagination() {
    var total      = filteredRows.length;
    var totalPages = Math.ceil(total / perPage) || 1;
    var start      = total === 0 ? 0 : (currentPage - 1) * perPage + 1;
    var end        = Math.min(currentPage * perPage, total);

    document.getElementById('tableInfo').textContent =
        total === 0 ? 'No results' : 'Showing ' + start + '–' + end + ' of ' + total;

    var pag = document.getElementById('tablePagination');
    if (totalPages <= 1) { pag.innerHTML = ''; return; }

    var html = '<ul class="pagination pagination-sm mb-0">';
    html += '<li class="page-item' + (currentPage === 1 ? ' disabled' : '') + '">'
          + '<button class="page-link" onclick="goToPage(' + (currentPage - 1) + ')">'
          + '<i class="fas fa-chevron-left fa-xs"></i></button></li>';

    var from = Math.max(1, currentPage - 2);
    var to   = Math.min(totalPages, currentPage + 2);

    if (from > 1) {
        html += '<li class="page-item"><button class="page-link" onclick="goToPage(1)">1</button></li>';
        if (from > 2) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
    }
    for (var p = from; p <= to; p++) {
        html += '<li class="page-item' + (p === currentPage ? ' active' : '') + '">'
              + '<button class="page-link" onclick="goToPage(' + p + ')">' + p + '</button></li>';
    }
    if (to < totalPages) {
        if (to < totalPages - 1) html += '<li class="page-item disabled"><span class="page-link">&hellip;</span></li>';
        html += '<li class="page-item"><button class="page-link" onclick="goToPage(' + totalPages + ')">' + totalPages + '</button></li>';
    }

    html += '<li class="page-item' + (currentPage === totalPages ? ' disabled' : '') + '">'
          + '<button class="page-link" onclick="goToPage(' + (currentPage + 1) + ')">'
          + '<i class="fas fa-chevron-right fa-xs"></i></button></li>';
    html += '</ul>';
    pag.innerHTML = html;
}

function renderCurrentPage() {
    var start    = (currentPage - 1) * perPage;
    var pageRows = filteredRows.slice(start, start + perPage);
    renderRows(pageRows);
    renderPagination();
}

function goToPage(n) {
    var totalPages = Math.ceil(filteredRows.length / perPage) || 1;
    if (n < 1 || n > totalPages) return;
    currentPage = n;
    renderCurrentPage();
}

// ── Sort ──────────────────────────────────────────
function sortBy(col) {
    sortAsc = (sortCol === col) ? !sortAsc : true;
    sortCol = col;
    updateSortIcons();
    currentPage = 1;
    applySearchAndSort();
}

function updateSortIcons() {
    document.querySelectorAll('th.sortable').forEach(function(th) {
        var icon = th.querySelector('i');
        if (!icon) return;
        if (th.dataset.col === sortCol) {
            icon.className = 'fas ' + (sortAsc ? 'fa-sort-up' : 'fa-sort-down') + ' sort-icon sort-active';
        } else {
            icon.className = 'fas fa-sort sort-icon';
        }
    });
}

// ── Filter + sort pipeline ────────────────────────
function applySearchAndSort() {
    var q = searchQuery.toLowerCase();
    filteredRows = allRows.filter(function(row) {
        if (!q) return true;
        return [row.cli_name, row.cli_email, row.cli_num, row.cli_message]
            .join(' ').toLowerCase().indexOf(q) !== -1;
    });

    if (sortCol) {
        filteredRows.sort(function(a, b) {
            var av = String(a[sortCol] || '').toLowerCase();
            var bv = String(b[sortCol] || '').toLowerCase();
            return (av < bv ? -1 : av > bv ? 1 : 0) * (sortAsc ? 1 : -1);
        });
    }

    renderCurrentPage();
}

// ── AJAX load ─────────────────────────────────────
function loadInquiries(filter) {
    currentFilter = filter;
    currentPage   = 1;

    document.querySelectorAll('.filter-tab').forEach(function(t) {
        t.classList.toggle('active', t.dataset.filter === filter);
    });
    document.getElementById('tableInfo').textContent       = '';
    document.getElementById('tablePagination').innerHTML   = '';
    document.getElementById('tableBody').innerHTML =
        '<tr><td colspan="7" class="text-center py-5 text-muted">'
        + '<span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>';

    $.get('php_files/fetch_inquiries.php', { filter: filter }, function(res) {
        if (res.statusCode !== 200) return;
        updateTabCounts(res.counts);
        allRows = res.data;
        applySearchAndSort();
    }, 'json').fail(function() {
        document.getElementById('tableBody').innerHTML =
            '<tr><td colspan="7" class="text-center text-danger py-5">'
            + '<i class="fas fa-exclamation-circle me-2"></i>Failed to load. Please refresh.</td></tr>';
    });
}

// ── Status updates ────────────────────────────────
function updateStatus(id, status) {
    $.post('php_files/update_inquiry_status.php', { id: id, status: status }, function(res) {
        if (res.statusCode === 200) {
            loadInquiries(currentFilter);
        } else {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to update status.' });
        }
    }, 'json').fail(function() {
        Swal.fire({ icon: 'error', title: 'Server error', text: 'Please try again.' });
    });
}

function confirmHide(id) {
    Swal.fire({
        title: 'Hide this inquiry?',
        text: 'It will be moved to the Hidden tab.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, hide it'
    }).then(function(result) {
        if (result.isConfirmed) updateStatus(id, 'hidden');
    });
}

// ── Init ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {

    document.querySelectorAll('.filter-tab').forEach(function(tab) {
        tab.addEventListener('click', function() { loadInquiries(tab.dataset.filter); });
    });

    var searchTimer;
    document.getElementById('tableSearch').addEventListener('input', function() {
        clearTimeout(searchTimer);
        var val = this.value;
        searchTimer = setTimeout(function() {
            searchQuery = val;
            currentPage = 1;
            applySearchAndSort();
        }, 250);
    });

    document.getElementById('perPageSelect').addEventListener('change', function() {
        perPage     = parseInt(this.value, 10);
        currentPage = 1;
        applySearchAndSort();
    });

    document.querySelectorAll('th.sortable').forEach(function(th) {
        th.addEventListener('click', function() { sortBy(th.dataset.col); });
    });

    updateSortIcons();
    loadInquiries('all');
});
</script>
HTML;

include 'includes/header.php';
?>

<div class="page-header mb-3">
    <h1>Client Inquiries</h1>
    <p>All messages submitted via the contact form.</p>
</div>

<!-- Controls: filter tabs + search/per-page -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">

    <div class="filter-tabs">
        <button class="filter-tab active" data-filter="all">
            All <span class="tab-badge" id="badge-all"></span>
        </button>
        <button class="filter-tab" data-filter="unread">
            Unread <span class="tab-badge" id="badge-unread"></span>
        </button>
        <button class="filter-tab" data-filter="read">
            Read <span class="tab-badge" id="badge-read"></span>
        </button>
        <button class="filter-tab" data-filter="hidden">
            Hidden <span class="tab-badge" id="badge-hidden"></span>
        </button>
    </div>

    <div class="d-flex align-items-center gap-2">
        <div class="inq-search-wrap">
            <i class="fas fa-search inq-search-icon"></i>
            <input type="search" id="tableSearch" class="inq-search"
                   placeholder="Search inquiries…">
        </div>
        <select id="perPageSelect" class="form-select form-select-sm inq-perpage">
            <option value="10">10 / page</option>
            <option value="15" selected>15 / page</option>
            <option value="25">25 / page</option>
            <option value="50">50 / page</option>
        </select>
        <button class="btn btn-outline-secondary btn-sm px-2"
                onclick="loadInquiries(currentFilter)" title="Refresh">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>
</div>

<!-- Table card -->
<div class="admin-card overflow-hidden">
    <div class="table-responsive">
        <table id="adminTable" class="table inq-table mb-0">
            <thead>
                <tr>
                    <th class="ps-4 sortable" data-col="date_time">
                        Date <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th class="sortable" data-col="cli_name">
                        Name <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th class="sortable" data-col="cli_email">
                        Email <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th>Contact No.</th>
                    <th>Message</th>
                    <th class="sortable" data-col="status">
                        Status <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <tr>
                    <td colspan="7" class="text-center py-5 text-muted">
                        <span class="spinner-border spinner-border-sm me-2"></span>Loading...
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Footer: info + pagination -->
<div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
    <small class="text-muted" id="tableInfo"></small>
    <nav aria-label="Inquiries pages" id="tablePagination"></nav>
</div>

<?php include 'includes/footer.php'; ?>
