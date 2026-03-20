/**
 * charts.js
 * Initializes and updates Chart.js instances using the mock data.
 * Includes logic to refresh on theme change.
 */

document.addEventListener("DOMContentLoaded", () => {
    // Collect data
    const sales = window.mockData.sales;
    const purchases = window.mockData.purchases;
    const clients = window.mockData.clients;
    const suppliers = window.mockData.suppliers;

    // --- KPI Updates ---
    const totalSales = sales.reduce((sum, item) => sum + item.total, 0);
    const totalPurchases = purchases.reduce((sum, item) => sum + item.total, 0);
    const activeClients = clients.filter(c => c.status === 'Active').length;
    const activeSuppliers = suppliers.filter(s => s.status === 'Active').length;

    const elTotalSales = document.getElementById('kpi-total-sales');
    const elTotalPurchases = document.getElementById('kpi-total-purchases');
    
    if(elTotalSales) elTotalSales.innerText = `$${totalSales.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits:2})}`;
    if(elTotalPurchases) elTotalPurchases.innerText = `$${totalPurchases.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits:2})}`;
    
    const elActiveClients = document.getElementById('kpi-active-clients');
    if(elActiveClients) elActiveClients.innerText = activeClients;

    const elActiveSuppliers = document.getElementById('kpi-active-suppliers');
    if(elActiveSuppliers) elActiveSuppliers.innerText = activeSuppliers;


    // --- Recent Transactions ---
    const recentTbody = document.getElementById('recent-transactions-tbody');
    if (recentTbody) {
        // Combine sales and purchases
        const combined = [
            ...sales.map(s => ({ ...s, name: s.clientName, type: 'Sale' })),
            ...purchases.map(p => ({ ...p, name: p.supplierName, type: 'Purchase' }))
        ];
        
        // Sort newest first
        combined.sort((a, b) => new Date(b.date) - new Date(a.date));
        
        // Take top 5
        const recent = combined.slice(0, 5);
        let html = '';
        recent.forEach(tx => {
            const badgeClass = tx.status === 'Paid' || tx.status === 'Completed' ? 'bg-success bg-opacity-10 text-success' :
                               tx.status === 'Pending' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-danger bg-opacity-10 text-danger';
            
            const typeBadge = tx.type === 'Sale' ? '<span class="badge bg-primary">Sale</span>' : '<span class="badge bg-secondary">Purchase</span>';
            
            html += `
                <tr>
                    <td class="ps-4 fw-medium">${tx.id}</td>
                    <td>${tx.date}</td>
                    <td>${tx.name}</td>
                    <td class="fw-bold">$${tx.total.toFixed(2)}</td>
                    <td>${typeBadge}</td>
                    <td class="pe-4"><span class="badge ${badgeClass}">${tx.status}</span></td>
                </tr>
            `;
        });
        recentTbody.innerHTML = html;
    }


    // --- Charts ---
    // Generate simple monthly grouping for last 6 months
    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
    const today = new Date();
    const months = [];
    const salesData = [];
    const purchaseData = [];

    for (let i = 5; i >= 0; i--) {
        const d = new Date(today.getFullYear(), today.getMonth() - i, 1);
        const mStr = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}`;
        months.push(monthNames[d.getMonth()] + ' ' + d.getFullYear());

        const sSum = sales.filter(s => s.date.startsWith(mStr)).reduce((sum, item) => sum + item.total, 0);
        salesData.push(sSum);

        const pSum = purchases.filter(p => p.date.startsWith(mStr)).reduce((sum, item) => sum + item.total, 0);
        purchaseData.push(pSum);
    }

    const chartConfig = {
        fontColorLight: '#64748b',
        fontColorDark: '#94a3b8',
        gridColorLight: '#e2e8f0',
        gridColorDark: '#334155'
    };

    let salesChartInst = null;
    let ratioChartInst = null;

    function renderCharts(theme) {
        const isDark = theme === 'dark';
        const fontColor = isDark ? chartConfig.fontColorDark : chartConfig.fontColorLight;
        const gridColor = isDark ? chartConfig.gridColorDark : chartConfig.gridColorLight;

        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            color: fontColor,
            scales: {
                x: { grid: { color: gridColor }, ticks: { color: fontColor } },
                y: { grid: { color: gridColor }, ticks: { color: fontColor } }
            },
            plugins: {
                legend: { labels: { color: fontColor } }
            }
        };

        const ctxSales = document.getElementById('salesChart');
        if (ctxSales) {
            if (salesChartInst) salesChartInst.destroy();
            salesChartInst = new Chart(ctxSales.getContext('2d'), {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Sales Revenue',
                        data: salesData,
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: commonOptions
            });
        }

        const ctxRatio = document.getElementById('ratioChart');
        if (ctxRatio) {
            if (ratioChartInst) ratioChartInst.destroy();
            // overriding common options for doughnut
            const doughnutOptions = {
                responsive: true,
                maintainAspectRatio: false,
                color: fontColor,
                plugins: {
                    legend: { position: 'bottom', labels: { color: fontColor } }
                }
            };
            ratioChartInst = new Chart(ctxRatio.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Sales', 'Purchases'],
                    datasets: [{
                        data: [totalSales, totalPurchases],
                        backgroundColor: ['#6366f1', '#f59e0b'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: doughnutOptions
            });
        }
    }

    // Initial render
    const theme = document.documentElement.getAttribute('data-bs-theme') || 'light';
    renderCharts(theme);

    // Expose for theme toggle
    window.updateChartsTheme = renderCharts;
});
