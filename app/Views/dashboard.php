<?php
ob_start();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h4 class="text-sm font-medium text-gray-500 mb-2 uppercase tracking-wider">Gasto Mensual Estimado</h4>
        <p class="text-3xl font-bold text-gray-900" id="totalMonthly">$0.00</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h4 class="text-sm font-medium text-gray-500 mb-2 uppercase tracking-wider">Gasto Anual Estimado</h4>
        <p class="text-3xl font-bold text-gray-900" id="totalYearly">$0.00</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <h4 class="text-sm font-medium text-gray-500 mb-2 uppercase tracking-wider">Suscripciones Activas</h4>
        <p class="text-3xl font-bold text-gray-900" id="activeSubscriptions">0</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <h2 class="text-xl font-bold" id="currentMonthYear">Enero 2026</h2>
            <div class="flex bg-gray-100 rounded-lg p-1">
                <button id="prevMonth" class="px-2 py-1 hover:bg-white rounded-md transition-all">&lt;</button>
                <button id="todayBtn"
                    class="px-3 py-1 hover:bg-white rounded-md transition-all text-sm font-medium">Hoy</button>
                <button id="nextMonth" class="px-2 py-1 hover:bg-white rounded-md transition-all">&gt;</button>
            </div>
        </div>
        <div class="flex items-center space-x-2">
            <select id="filterCategory"
                class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 outline-none">
                <option value="">Todas las categorías</option>
            </select>
        </div>
    </div>

    <div class="calendar-grid bg-gray-200 gap-px border-b border-gray-200">
        <!-- Weekdays -->
        <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">Dom</div>
        <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">Lun</div>
        <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">Mar</div>
        <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">Mié</div>
        <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">Jue</div>
        <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">Vie</div>
        <div class="bg-gray-50 p-2 text-center text-xs font-bold text-gray-500 uppercase">Sáb</div>
    </div>

    <div id="calendarDays" class="calendar-grid bg-gray-200 gap-px">
        <!-- Days will be injected by JS -->
    </div>
</div>

<div class="mt-8">
    <h3 class="text-lg font-bold mb-4">Próximos Cobros</h3>
    <div id="upcomingList" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <!-- Small cards for upcoming renewals -->
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>