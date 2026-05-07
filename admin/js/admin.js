document.addEventListener('DOMContentLoaded', function () {

    // Restore sidebar state
    if (localStorage.getItem('sidebarCollapsed') === 'true') {
        document.body.classList.add('sidebar-collapsed');
    }

    const toggle = document.getElementById('sidebarToggle');
    if (toggle) {
        toggle.addEventListener('click', function () {
            // Mobile: use sidebar-open, Desktop: use sidebar-collapsed
            if (window.innerWidth <= 768) {
                document.body.classList.toggle('sidebar-open');
            } else {
                document.body.classList.toggle('sidebar-collapsed');
                localStorage.setItem(
                    'sidebarCollapsed',
                    document.body.classList.contains('sidebar-collapsed')
                );
            }
        });
    }

    // Init DataTable if present
    const table = document.getElementById('adminTable');
    if (table && typeof simpleDatatables !== 'undefined') {
        new simpleDatatables.DataTable(table, {
            searchable: true,
            sortable: true,
            perPage: 15,
            perPageSelect: [10, 15, 25, 50]
        });
    }

});
