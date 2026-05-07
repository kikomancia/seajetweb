<?php
$pageTitle  = 'Client Inquiries';
$activePage = 'inquiries';
require 'includes/auth.php';
require 'includes/db.php';

$unreadCount = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS c FROM sj_messages WHERE status = 'unread'"))['c'];
mysqli_close($connect);

$extraScripts = <<<'HTML'
<script>
var currentFilter = 'all';
var inquiryDT     = null;

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
    var map = { unread: ['inq-unread', 'Unread'], read: ['inq-read', 'Read'], hidden: ['inq-hidden', 'Hidden'] };
    var info = map[status] || ['inq-unread', status];
    return '<span class="status-badge ' + info[0] + '">' + info[1] + '</span>';
}

function actionButtons(id, status) {
    var btns = '';
    if (status === 'unread') {
        btns += '<button class="btn-action btn-mark-read" onclick="updateStatus(' + id + ',\'read\')">Mark Read</button> ';
    } else if (status === 'read') {
        btns += '<button class="btn-action btn-mark-unread" onclick="updateStatus(' + id + ',\'unread\')">Mark Unread</button> ';
    }
    if (status !== 'hidden') {
        btns += '<button class="btn-action btn-hide" onclick="confirmHide(' + id + ')">Hide</button>';
    } else {
        btns += '<button class="btn-action btn-unhide" onclick="updateStatus(' + id + ',\'unread\')">Unhide</button>';
    }
    return btns;
}

function updateTabCounts(counts) {
    var total = (counts.unread || 0) + (counts.read || 0);
    document.getElementById('badge-all').textContent    = total > 0 ? total : '';
    document.getElementById('badge-unread').textContent = counts.unread > 0 ? counts.unread : '';
    document.getElementById('badge-read').textContent   = counts.read   > 0 ? counts.read   : '';
    document.getElementById('badge-hidden').textContent = counts.hidden > 0 ? counts.hidden : '';
}

function renderTable(data) {
    var tbody = document.getElementById('tableBody');
    if (!data || !data.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted py-5">No inquiries found.</td></tr>';
        return;
    }
    var html = '';
    for (var i = 0; i < data.length; i++) {
        var row = data[i];
        html += '<tr>';
        html += '<td class="ps-4 text-nowrap">' + escHtml(formatDate(row.date_time)) + '<br><small class="text-muted">' + escHtml(formatTime(row.date_time)) + '</small></td>';
        html += '<td>' + escHtml(row.cli_name) + '</td>';
        html += '<td>' + escHtml(row.cli_email) + '</td>';
        html += '<td>' + escHtml(row.cli_num) + '</td>';
        html += '<td class="msg-cell">' + escHtml(row.cli_message) + '</td>';
        html += '<td>' + statusBadge(row.status) + '</td>';
        html += '<td class="text-nowrap">' + actionButtons(row.id, row.status) + '</td>';
        html += '</tr>';
    }
    tbody.innerHTML = html;
}

function loadInquiries(filter) {
    currentFilter = filter;

    document.querySelectorAll('.filter-tab').forEach(function(t) {
        t.classList.toggle('active', t.dataset.filter === filter);
    });

    if (inquiryDT) { inquiryDT.destroy(); inquiryDT = null; }

    document.getElementById('tableBody').innerHTML =
        '<tr><td colspan="7" class="text-center py-5">' +
        '<span class="spinner-border spinner-border-sm me-2"></span>Loading...</td></tr>';

    $.get('php_files/fetch_inquiries.php', { filter: filter }, function(res) {
        if (res.statusCode !== 200) return;
        updateTabCounts(res.counts);
        renderTable(res.data);
        inquiryDT = new simpleDatatables.DataTable(document.getElementById('adminTable'), {
            searchable: true,
            sortable:   true,
            perPage:    15,
            perPageSelect: [10, 15, 25, 50],
            columns: [{ select: 6, sortable: false }]
        });
    }, 'json').fail(function() {
        document.getElementById('tableBody').innerHTML =
            '<tr><td colspan="7" class="text-center text-danger py-5">Failed to load inquiries.</td></tr>';
    });
}

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

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.filter-tab').forEach(function(tab) {
        tab.addEventListener('click', function() { loadInquiries(tab.dataset.filter); });
    });
    loadInquiries('all');
});
</script>
HTML;

include 'includes/header.php';
?>

<div class="page-header d-flex align-items-center justify-content-between flex-wrap gap-2">
    <div>
        <h1>Client Inquiries</h1>
        <p>All messages submitted via the contact form.</p>
    </div>
    <button class="btn btn-outline-secondary btn-sm" onclick="loadInquiries(currentFilter)">
        <i class="fas fa-sync-alt me-1"></i> Refresh
    </button>
</div>

<!-- Filter tabs -->
<div class="filter-tabs mb-3">
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

<div class="admin-card">
    <div class="admin-card-body p-0">
        <div class="table-responsive">
            <table id="adminTable" class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Contact No.</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <span class="spinner-border spinner-border-sm me-2"></span>Loading...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
