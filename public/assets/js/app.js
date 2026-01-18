document.addEventListener('DOMContentLoaded', () => {
    const calendar = new Calendar('calendarDays', 'currentMonthYear');
    let subscriptions = [];
    let radarChart = null;
    let doughnutChart = null;

    // Elements
    const addBtn = document.getElementById('addSubscriptionBtn');
    const modal = document.getElementById('modalContainer');
    const closeModal = document.getElementById('closeModal');
    const subForm = document.getElementById('subscriptionForm');
    const deleteBtn = document.getElementById('deleteBtn');
    const filterCategory = document.getElementById('filterCategory');

    // Stats Elements
    const totalMonthlyEl = document.getElementById('totalMonthly');
    const totalYearlyEl = document.getElementById('totalYearly');
    const activeSubsEl = document.getElementById('activeSubscriptions');
    const mostExpensiveEl = document.getElementById('mostExpensive');
    const upcomingList = document.getElementById('upcomingList');

    // Fetch Subscriptions
    async function fetchSubscriptions() {
        const response = await fetch('/api/subscriptions');
        subscriptions = await response.json();
        updateStats();
        updateCategories();
        calendar.setSubscriptions(subscriptions);
        renderUpcoming();
        updateCharts();
    }

    function updateStats() {
        let monthly = 0;
        let maxPrice = 0;

        subscriptions.forEach(s => {
            const price = parseFloat(s.price);
            let normalizedMonthly = 0;

            if (s.billing_cycle === 'weekly') {
                normalizedMonthly = (price * 52) / 12;
            } else if (s.billing_cycle === 'biweekly') {
                normalizedMonthly = price * 2;
            } else if (s.billing_cycle === 'monthly') {
                normalizedMonthly = price;
            } else if (s.billing_cycle === 'yearly') {
                normalizedMonthly = price / 12;
            }

            monthly += normalizedMonthly;
            if (normalizedMonthly > maxPrice) maxPrice = normalizedMonthly;
        });

        const yearly = monthly * 12;

        totalMonthlyEl.textContent = Utils.formatCurrency(monthly);
        totalYearlyEl.textContent = Utils.formatCurrency(yearly);
        activeSubsEl.textContent = subscriptions.length;
        mostExpensiveEl.textContent = Utils.formatCurrency(maxPrice);
    }

    function updateCategories() {
        const categories = [...new Set(subscriptions.map(s => s.category))];
        const currentFilter = filterCategory.value;
        filterCategory.innerHTML = '<option value="">Categorías</option>';
        categories.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.textContent = cat;
            filterCategory.appendChild(opt);
        });
        filterCategory.value = currentFilter;
    }

    function updateCharts() {
        if (subscriptions.length === 0) return;

        // 1. Radar Chart: Spending by Category
        const catData = {};
        subscriptions.forEach(s => {
            const price = parseFloat(s.price);
            let monthly = 0;
            if (s.billing_cycle === 'weekly') monthly = (price * 52) / 12;
            else if (s.billing_cycle === 'biweekly') monthly = price * 2;
            else if (s.billing_cycle === 'monthly') monthly = price;
            else if (s.billing_cycle === 'yearly') monthly = price / 12;
            catData[s.category] = (catData[s.category] || 0) + monthly;
        });

        const ctxRadar = document.getElementById('radarChart').getContext('2d');
        if (radarChart) radarChart.destroy();
        radarChart = new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: Object.keys(catData),
                datasets: [{
                    label: 'Gasto',
                    data: Object.values(catData),
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { r: { angleLines: { display: true }, suggestedMin: 0, ticks: { display: false } } },
                plugins: { legend: { display: false } }
            }
        });

        // 2. Doughnut Chart: Distribution by Cycle
        const cycleData = { 'weekly': 0, 'biweekly': 0, 'monthly': 0, 'yearly': 0 };
        subscriptions.forEach(s => cycleData[s.billing_cycle]++);

        const ctxDoughnut = document.getElementById('doughnutChart').getContext('2d');
        if (doughnutChart) doughnutChart.destroy();
        doughnutChart = new Chart(ctxDoughnut, {
            type: 'doughnut',
            data: {
                labels: ['Semanal', 'Quincenal', 'Mensual', 'Anual'],
                datasets: [{
                    data: [cycleData.weekly, cycleData.biweekly, cycleData.monthly, cycleData.yearly],
                    backgroundColor: ['#60a5fa', '#34d399', '#4f46e5', '#8b5cf6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                }
            }
        });
    }

    function renderUpcoming() {
        upcomingList.innerHTML = '';
        const now = new Date();
        now.setHours(0, 0, 0, 0);

        const sorted = [...subscriptions]
            .filter(s => new Date(s.next_renewal) >= now)
            .sort((a, b) => new Date(a.next_renewal) - new Date(b.next_renewal));

        if (sorted.length === 0) {
            upcomingList.innerHTML = '<p class="text-xs text-gray-400 text-center py-10">No hay cobros próximos</p>';
            return;
        }

        sorted.forEach(sub => {
            const card = document.createElement('div');
            card.className = 'group bg-white p-3 rounded-xl border border-gray-100 flex items-center justify-between hover:border-blue-200 hover:shadow-sm transition-all cursor-pointer';
            card.onclick = () => openModal(sub);

            const statusColor = Utils.getStatusColor(sub.next_renewal);

            card.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="w-1.5 h-8 rounded-full" style="background-color: ${sub.color}"></div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-[13px] group-hover:text-blue-600 transition-colors leading-tight">${sub.name}</h4>
                        <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">${sub.category}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900 text-[13px] leading-tight">${Utils.formatCurrency(sub.price, sub.currency)}</p>
                    <p class="text-[9px] uppercase font-bold ${statusColor.split(' ')[0]}">${Utils.getRelativeTime(sub.next_renewal)}</p>
                </div>
            `;
            upcomingList.appendChild(card);
        });
    }

    // Modal Operations
    function openModal(sub = null) {
        modal.classList.remove('hidden');
        if (sub) {
            document.getElementById('modalTitle').textContent = 'Editar Suscripción';
            document.getElementById('subId').value = sub.id;
            document.getElementById('subName').value = sub.name;
            document.getElementById('subPrice').value = sub.price;
            document.getElementById('subCurrency').value = sub.currency;
            document.getElementById('subCycle').value = sub.billing_cycle;
            document.getElementById('subStartDate').value = sub.start_date;
            document.getElementById('subEndDate').value = sub.end_date || '';
            document.getElementById('subCategory').value = sub.category;
            document.getElementById('subColor').value = sub.color;
            deleteBtn.classList.remove('hidden');
        } else {
            document.getElementById('modalTitle').textContent = 'Nueva Suscripción';
            subForm.reset();
            document.getElementById('subId').value = '';
            document.getElementById('subStartDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('subEndDate').value = '';
            document.getElementById('subColor').value = '#3b82f6';
            deleteBtn.classList.add('hidden');
        }
    }

    addBtn.onclick = () => openModal();
    closeModal.onclick = () => modal.classList.add('hidden');
    window.onclick = (e) => { if (e.target === modal) modal.classList.add('hidden'); };

    subForm.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(subForm);
        const data = Object.fromEntries(formData.entries());
        const id = data.id;

        const url = id ? `/api/subscriptions/${id}` : '/api/subscriptions';
        const method = id ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        if (response.ok) {
            modal.classList.add('hidden');
            fetchSubscriptions();
        }
    };

    deleteBtn.onclick = async () => {
        const id = document.getElementById('subId').value;
        if (!confirm('¿Seguro que quieres eliminar esta suscripción?')) return;

        const response = await fetch(`/api/subscriptions/${id}`, { method: 'DELETE' });
        if (response.ok) {
            modal.classList.add('hidden');
            fetchSubscriptions();
        }
    };

    // Calendar Controls
    document.getElementById('prevMonth').onclick = () => calendar.prevMonth();
    document.getElementById('nextMonth').onclick = () => calendar.nextMonth();
    document.getElementById('todayBtn').onclick = () => calendar.today();

    filterCategory.onchange = () => {
        const cat = filterCategory.value;
        if (cat) {
            calendar.setSubscriptions(subscriptions.filter(s => s.category === cat));
        } else {
            calendar.setSubscriptions(subscriptions);
        }
    };

    window.addEventListener('editSubscription', (e) => openModal(e.detail));

    // Initial Load
    fetchSubscriptions();
});
