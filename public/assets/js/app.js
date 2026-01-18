document.addEventListener('DOMContentLoaded', () => {
    const calendar = new Calendar('calendarDays', 'currentMonthYear');
    let subscriptions = [];

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
    const upcomingList = document.getElementById('upcomingList');

    // Fetch Subscriptions
    async function fetchSubscriptions() {
        const response = await fetch('/api/subscriptions');
        subscriptions = await response.json();
        updateStats();
        updateCategories();
        calendar.setSubscriptions(subscriptions);
        renderUpcoming();
    }

    function updateStats() {
        let monthly = 0;
        let yearly = 0;

        subscriptions.forEach(s => {
            const price = parseFloat(s.price);
            if (s.billing_cycle === 'weekly') {
                monthly += (price * 52) / 12;
            } else if (s.billing_cycle === 'monthly') {
                monthly += price;
            } else if (s.billing_cycle === 'yearly') {
                monthly += price / 12;
            }
        });

        yearly = monthly * 12;

        totalMonthlyEl.textContent = Utils.formatCurrency(monthly);
        totalYearlyEl.textContent = Utils.formatCurrency(yearly);
        activeSubsEl.textContent = subscriptions.length;
    }

    function updateCategories() {
        const categories = [...new Set(subscriptions.map(s => s.category))];
        const currentFilter = filterCategory.value;
        filterCategory.innerHTML = '<option value="">Todas las categorías</option>';
        categories.forEach(cat => {
            const opt = document.createElement('option');
            opt.value = cat;
            opt.textContent = cat;
            filterCategory.appendChild(opt);
        });
        filterCategory.value = currentFilter;
    }

    function renderUpcoming() {
        upcomingList.innerHTML = '';
        const now = new Date();
        now.setHours(0, 0, 0, 0);

        const sorted = [...subscriptions]
            .filter(s => new Date(s.next_renewal) >= now)
            .sort((a, b) => new Date(a.next_renewal) - new Date(b.next_renewal))
            .slice(0, 6);

        sorted.forEach(sub => {
            const card = document.createElement('div');
            card.className = 'bg-white p-4 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all cursor-pointer';
            card.onclick = () => openModal(sub);

            const colorClass = Utils.getStatusColor(sub.next_renewal);

            card.innerHTML = `
                <div>
                    <h4 class="font-bold text-gray-900">${sub.name}</h4>
                    <p class="text-xs text-gray-500">${sub.category} • ${sub.billing_cycle}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-blue-600">${Utils.formatCurrency(sub.price, sub.currency)}</p>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-medium ${colorClass}">
                        ${Utils.getRelativeTime(sub.next_renewal)}
                    </span>
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
            document.getElementById('subCategory').value = sub.category;
            document.getElementById('subColor').value = sub.color;
            deleteBtn.classList.remove('hidden');
        } else {
            document.getElementById('modalTitle').textContent = 'Nueva Suscripción';
            subForm.reset();
            document.getElementById('subId').value = '';
            document.getElementById('subStartDate').value = new Date().toISOString().split('T')[0];
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
