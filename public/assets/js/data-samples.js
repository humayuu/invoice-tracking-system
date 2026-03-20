/**
 * data-samples.js
 * Contains dummy JSON data for Sales, Purchases, Clients, and Suppliers.
 * Also simulates basic pagination, sorting, filtering client-side for demonstration.
 */

// --- 1. Clients ---
const sampleClients = [
    { id: 'C-001', name: 'Acme Corp', email: 'contact@acme.com', phone: '+1-555-0100', status: 'Active', joined: '2025-01-15' },
    { id: 'C-002', name: 'Globex Inc', email: 'sales@globex.com', phone: '+1-555-0101', status: 'Active', joined: '2025-02-10' },
    { id: 'C-003', name: 'Initech', email: 'info@initech.com', phone: '+1-555-0102', status: 'Inactive', joined: '2024-11-05' },
    { id: 'C-004', name: 'Umbrella Corp', email: 'admin@umbrella.com', phone: '+1-555-0103', status: 'Active', joined: '2024-08-22' },
    { id: 'C-005', name: 'Stark Industries', email: 'tony@stark.com', phone: '+1-555-0104', status: 'Active', joined: '2025-03-01' },
    { id: 'C-006', name: 'Wayne Enterprises', email: 'bruce@wayne.com', phone: '+1-555-0105', status: 'Active', joined: '2024-10-12' },
    { id: 'C-007', name: 'Cyberdyne Systems', email: 'skynet@cyberdyne.com', phone: '+1-555-0106', status: 'Inactive', joined: '2023-05-18' },
    { id: 'C-008', name: 'Massive Dynamic', email: 'william@massive.com', phone: '+1-555-0107', status: 'Active', joined: '2025-01-30' },
];

// --- 2. Suppliers ---
const sampleSuppliers = [
    { id: 'S-001', name: 'TechSource Ltd', email: 'orders@techsource.com', phone: '+44-20-7946-0100', status: 'Active', category: 'Hardware' },
    { id: 'S-002', name: 'Office Depot X', email: 'supply@officedepotx.com', phone: '+1-555-0201', status: 'Active', category: 'Stationery' },
    { id: 'S-003', name: 'CloudNet Servers', email: 'billing@cloudnet.com', phone: '+1-555-0202', status: 'Active', category: 'Software' },
    { id: 'S-004', name: 'Logistics Pro', email: 'shipping@logpro.com', phone: '+1-555-0203', status: 'Inactive', category: 'Shipping' },
    { id: 'S-005', name: 'Global Importers', email: 'import@globalimp.com', phone: '+1-555-0204', status: 'Active', category: 'Hardware' },
];

// --- 3. Sales ---
// Helper to generate random dates within the last 6 months
function generateRandomDate(start, end) {
    return new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime())).toISOString().split('T')[0];
}

const sampleSales = [];
const today = new Date();
const sixMonthsAgo = new Date();
sixMonthsAgo.setMonth(today.getMonth() - 6);

const salesStatuses = ['Paid', 'Pending', 'Overdue', 'Cancelled'];

for (let i = 1; i <= 45; i++) {
    const client = sampleClients[Math.floor(Math.random() * sampleClients.length)];
    const status = salesStatuses[Math.floor(Math.random() * salesStatuses.length)];
    const total = parseFloat((Math.random() * 5000 + 100).toFixed(2));
    const date = generateRandomDate(sixMonthsAgo, today);
    
    sampleSales.push({
        id: `INV-${1000 + i}`,
        date: date,
        clientId: client.id,
        clientName: client.name,
        total: total,
        status: status,
        paymentMethod: Math.random() > 0.5 ? 'Credit Card' : 'Bank Transfer'
    });
}
// Sort by date descending
sampleSales.sort((a, b) => new Date(b.date) - new Date(a.date));

// --- 4. Purchases ---
const samplePurchases = [];
const purchaseStatuses = ['Completed', 'Pending', 'Cancelled'];

for (let i = 1; i <= 35; i++) {
    const supplier = sampleSuppliers[Math.floor(Math.random() * sampleSuppliers.length)];
    const status = purchaseStatuses[Math.floor(Math.random() * purchaseStatuses.length)];
    const total = parseFloat((Math.random() * 8000 + 200).toFixed(2));
    const date = generateRandomDate(sixMonthsAgo, today);
    
    samplePurchases.push({
        id: `PO-${5000 + i}`,
        date: date,
        supplierId: supplier.id,
        supplierName: supplier.name,
        total: total,
        status: status
    });
}
// Sort by date descending
samplePurchases.sort((a, b) => new Date(b.date) - new Date(a.date));

// Expose to window
window.mockData = {
    clients: sampleClients,
    suppliers: sampleSuppliers,
    sales: sampleSales,
    purchases: samplePurchases
};
