const Utils = {
    formatCurrency(amount, currency = 'USD') {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: currency
        }).format(amount);
    },

    formatDate(dateStr) {
        const options = { day: 'numeric', month: 'short' };
        return new Date(dateStr).toLocaleDateString('es-MX', options);
    },

    getRelativeTime(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        now.setHours(0, 0, 0, 0);
        date.setHours(0, 0, 0, 0);

        const diffTime = date - now;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'hoy';
        if (diffDays === 1) return 'mañana';
        if (diffDays < 7) return `en ${diffDays} días`;
        return `el ${this.formatDate(dateStr)}`;
    },

    getStatusColor(dateStr) {
        const date = new Date(dateStr);
        const now = new Date();
        now.setHours(0, 0, 0, 0);
        date.setHours(0, 0, 0, 0);

        const diffDays = Math.ceil((date - now) / (1000 * 60 * 60 * 24));

        if (diffDays === 0) return 'text-red-600 bg-red-100';
        if (diffDays > 0 && diffDays <= 3) return 'text-yellow-600 bg-yellow-100';
        return 'text-green-600 bg-green-100';
    }
};
