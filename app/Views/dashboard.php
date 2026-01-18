<?php
ob_start();
?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Mensual</h4>
            <p class="text-2xl font-bold text-gray-900" id="totalMonthly">$0.00</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-indigo-50 rounded-xl text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Anual</h4>
            <p class="text-2xl font-bold text-gray-900" id="totalYearly">$0.00</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-green-50 rounded-xl text-green-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Activas</h4>
            <p class="text-2xl font-bold text-gray-900" id="activeSubscriptions">0</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
        <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        </div>
        <div>
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Más Caro</h4>
            <p class="text-2xl font-bold text-gray-900" id="mostExpensive">$0.00</p>
        </div>
    </div>
</div>

<div class="flex flex-col lg:flex-row gap-8">
    <!-- Left Column (30%): Radar Chart + Upcoming -->
    <aside class="w-full lg:w-[30%] space-y-8">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-sm font-bold text-gray-900 mb-6 uppercase tracking-wider">Gastos por Categoría</h3>
            <div class="aspect-square relative flex items-center justify-center">
                <canvas id="radarChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Próximos Cobros</h3>
            </div>
            <div id="upcomingList" class="space-y-4">
                <!-- Upcoming renewal cards -->
            </div>
        </div>
    </aside>

    <!-- Right Column (70%): Calendar -->
    <section class="w-full lg:w-[70%]">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center space-x-4">
                    <h2 class="text-xl font-bold" id="currentMonthYear">...</h2>
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        <button id="prevMonth" class="px-2 py-1 hover:bg-white rounded-md transition-all">&lt;</button>
                        <button id="todayBtn" class="px-3 py-1 hover:bg-white rounded-md transition-all text-sm font-medium">Hoy</button>
                        <button id="nextMonth" class="px-2 py-1 hover:bg-white rounded-md transition-all">&gt;</button>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <select id="filterCategory" class="text-sm bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 outline-none">
                        <option value="">Todas las categorías</option>
                    </select>
                </div>
            </div>
            
            <div class="calendar-grid bg-gray-200 gap-px border-b border-gray-200">
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
    </section>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>