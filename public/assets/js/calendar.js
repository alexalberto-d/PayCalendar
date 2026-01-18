class Calendar {
    constructor(containerId, monthYearLabelId) {
        this.container = document.getElementById(containerId);
        this.label = document.getElementById(monthYearLabelId);
        this.currentDate = new Date();
        this.subscriptions = [];
    }

    setSubscriptions(subs) {
        this.subscriptions = subs;
        this.render();
    }

    prevMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
        this.render();
    }

    nextMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
        this.render();
    }

    today() {
        this.currentDate = new Date();
        this.render();
    }

    render() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();

        this.label.textContent = new Intl.DateTimeFormat('es-MX', { month: 'long', year: 'numeric' }).format(this.currentDate);

        this.container.innerHTML = '';

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        // Previous month days padding
        const prevMonthLastDay = new Date(year, month, 0).getDate();
        for (let i = firstDay - 1; i >= 0; i--) {
            const dayCell = this.createDayCell(prevMonthLastDay - i, true);
            this.container.appendChild(dayCell);
        }

        // Current month days
        for (let i = 1; i <= daysInMonth; i++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
            const dayCell = this.createDayCell(i, false, dateStr);

            // Add subscriptions for this day
            const daySubs = this.subscriptions.filter(s => this.isRenewalDate(s, dateStr));
            daySubs.forEach(sub => {
                const badge = document.createElement('div');
                badge.className = 'mt-1 px-2 py-0.5 text-[10px] rounded text-white truncate cursor-pointer hover:brightness-110';
                badge.style.backgroundColor = sub.color;
                badge.textContent = `${sub.name} (${Utils.formatCurrency(sub.price, sub.currency)})`;
                badge.onclick = (e) => {
                    e.stopPropagation();
                    window.dispatchEvent(new CustomEvent('editSubscription', { detail: sub }));
                };
                dayCell.querySelector('.subs-container').appendChild(badge);
            });

            this.container.appendChild(dayCell);
        }

        // Next month days padding
        const totalCells = 42; // 6 weeks
        const remainingCells = totalCells - this.container.children.length;
        for (let i = 1; i <= remainingCells; i++) {
            const dayCell = this.createDayCell(i, true);
            this.container.appendChild(dayCell);
        }
    }

    createDayCell(day, isPadding, dateStr = null) {
        const div = document.createElement('div');
        div.className = `calendar-day bg-white p-2 border-none relative flex flex-col ${isPadding ? 'text-gray-300 bg-gray-50/50' : 'text-gray-700'}`;

        const dayNum = document.createElement('span');
        dayNum.className = 'text-sm font-semibold mb-1';
        dayNum.textContent = day;

        if (dateStr && dateStr === new Date().toISOString().split('T')[0]) {
            dayNum.className += ' bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center p-0 ml-[-4px] mt-[-4px]';
        }

        const subsContainer = document.createElement('div');
        subsContainer.className = 'subs-container flex flex-col gap-1 overflow-y-auto max-h-[80px]';

        div.appendChild(dayNum);
        div.appendChild(subsContainer);

        return div;
    }

    isRenewalDate(sub, dateStr) {
        const targetDate = new Date(dateStr + 'T00:00:00');
        const startDate = new Date(sub.start_date + 'T00:00:00');

        if (targetDate < startDate) return false;

        const cycle = sub.billing_cycle;

        if (cycle === 'weekly') {
            const diffTime = Math.abs(targetDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays % 7 === 0;
        }

        if (cycle === 'monthly') {
            const startDay = startDate.getDate();
            const targetDay = targetDate.getDate();

            // Handle end of month (e.g., if started on 31st, show on 30th or 28th)
            const lastDayOfTargetMonth = new Date(targetDate.getFullYear(), targetDate.getMonth() + 1, 0).getDate();

            if (startDay >= lastDayOfTargetMonth) {
                return targetDay === lastDayOfTargetMonth;
            }
            return targetDay === startDay;
        }

        if (cycle === 'yearly') {
            return targetDate.getDate() === startDate.getDate() &&
                targetDate.getMonth() === startDate.getMonth();
        }

        return false;
    }
}
