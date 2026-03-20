/**
 * app.js
 * Main initialization file for UI interactivity.
 * Handles Sidebar, Dark Mode, RTL, and common utilities.
 */

document.addEventListener("DOMContentLoaded", () => {
    // --- 1. Sidebar Toggle ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggleBtn = document.getElementById('sidebar-toggle');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (sidebarToggleBtn && sidebar) {
        sidebarToggleBtn.addEventListener('click', () => {
            // For mobile view (below 992px)
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
                if (sidebarOverlay) sidebarOverlay.classList.toggle('show');
            } else {
                // For desktop view
                sidebar.classList.toggle('collapsed');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
        });
    }

    // Handle resize
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 992 && sidebar) {
            sidebar.classList.remove('show');
            if(sidebarOverlay) sidebarOverlay.classList.remove('show');
        }
    });

    // --- 2. Dark Mode Toggle ---
    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const htmlEl = document.documentElement;

    // Check local storage
    const currentTheme = localStorage.getItem('theme') || 'light';
    htmlEl.setAttribute('data-bs-theme', currentTheme);

    if (darkModeToggle) {
        // Init icon
        const icon = darkModeToggle.querySelector('i');
        if (currentTheme === 'dark' && icon) {
            icon.classList.replace('fa-moon', 'fa-sun');
        }

        darkModeToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const theme = htmlEl.getAttribute('data-bs-theme');
            const newTheme = theme === 'dark' ? 'light' : 'dark';
            htmlEl.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Toggle icon
            if (icon) {
                if (newTheme === 'dark') {
                    icon.classList.replace('fa-moon', 'fa-sun');
                } else {
                    icon.classList.replace('fa-sun', 'fa-moon');
                }
            }

            // If charts exist, we might need to update chart colors
            if (window.updateChartsTheme) {
                window.updateChartsTheme(newTheme);
            }
        });
    }

    // --- 3. RTL Toggle ---
    const rtlToggle = document.getElementById('rtl-toggle');
    const currentDir = localStorage.getItem('dir') || 'ltr';
    htmlEl.setAttribute('dir', currentDir);

    if (rtlToggle) {
        rtlToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const dir = htmlEl.getAttribute('dir');
            const newDir = dir === 'rtl' ? 'ltr' : 'rtl';
            htmlEl.setAttribute('dir', newDir);
            localStorage.setItem('dir', newDir);
            // Reload to re-layout components properly if needed, although CSS handles most
        });
    }
});

// --- Table Utilities (Reusable for pages) ---
window.TableUtils = {
    renderTable: function(data, tbodyId, renderRowFn) {
        const tbody = document.getElementById(tbodyId);
        if(!tbody) return;
        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="10" class="text-center text-muted py-4">No data available</td></tr>`;
            return;
        }
        data.forEach((item, index) => {
            tbody.innerHTML += renderRowFn(item, index);
        });
    },

    paginate: function(data, page, perPage) {
        const start = (page - 1) * perPage;
        return data.slice(start, start + perPage);
    },

    renderPagination: function(totalItems, perPage, currentPage, containerId, onPageChange) {
        const container = document.getElementById(containerId);
        if(!container) return;
        
        const totalPages = Math.ceil(totalItems / perPage);
        let html = `<ul class="pagination pagination-sm justify-content-end mb-0">`;
        
        // Prev button
        html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}"><i class="fa-solid fa-chevron-left"></i></a>
        </li>`;

        for(let i=1; i<=totalPages; i++) {
            // Logic to show limited pages can be added. For now show all.
            if(i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                html += `<li class="page-item ${currentPage === i ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
            } else if (i === currentPage - 2 || i === currentPage + 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Next button
        html += `<li class="page-item ${currentPage === totalPages || totalPages === 0 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}"><i class="fa-solid fa-chevron-right"></i></a>
        </li>`;
        
        html += `</ul>`;
        container.innerHTML = html;

        // Attach events
        container.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.currentTarget.getAttribute('data-page'));
                if(!isNaN(page) && page > 0 && page <= totalPages) {
                    onPageChange(page);
                }
            });
        });
    },

    sortData: function(data, key, dir) {
        return [...data].sort((a, b) => {
            if(a[key] < b[key]) return dir === 'asc' ? -1 : 1;
            if(a[key] > b[key]) return dir === 'asc' ? 1 : -1;
            return 0;
        });
    },

    exportCSV: function(data, filename, columns) {
        // columns is an array of objects: { key: 'id', label: 'ID' }
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += columns.map(c => c.label).join(",") + "\r\n";
        
        data.forEach(item => {
            let row = columns.map(c => {
                let cell = item[c.key] !== undefined ? item[c.key] : '';
                return `"${String(cell).replace(/"/g, '""')}"`;
            });
            csvContent += row.join(",") + "\r\n";
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", filename + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
};
