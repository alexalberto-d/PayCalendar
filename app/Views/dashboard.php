<?php
ob_start();
?>

<!-- Compact Stats Header -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
        <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Mensual</h4>
            <p class="text-lg font-bold text-gray-900" id="totalMonthly">$0.00</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
        <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Anual</h4>
            <p class="text-lg font-bold text-gray-900" id="totalYearly">$0.00</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
        <div class="p-2 bg-green-50 rounded-lg text-green-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </div>
        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Activas</h4>
            <p class="text-lg font-bold text-gray-900" id="activeSubscriptions">0</p>
        </div>
    </div>
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center space-x-3">
        <div class="p-2 bg-purple-50 rounded-lg text-purple-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
        </div>
        <div>
            <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Más Caro</h4>
            <p class="text-lg font-bold text-gray-900" id="mostExpensive">$0.00</p>
        </div>
    </div>
</div>

<!-- Main Tri-Column Layout -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 h-[calc(100vh-140px)] overflow-hidden">

    <!-- Left Column (Charts) -->
    <aside class="lg:col-span-3 flex flex-col gap-4 overflow-y-auto pr-1 custom-scrollbar">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-1/2 flex flex-col min-h-[200px]">
            <h3 class="text-[10px] font-bold text-gray-900 mb-2 uppercase tracking-widest">Categorías</h3>
            <div class="flex-grow relative">
                <canvas id="radarChart"></canvas>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 h-1/2 flex flex-col min-h-[200px]">
            <h3 class="text-[10px] font-bold text-gray-900 mb-2 uppercase tracking-widest">Ciclos de Pago</h3>
            <div class="flex-grow relative">
                <canvas id="doughnutChart"></canvas>
            </div>
        </div>
    </aside>

    <!-- Middle Column (Upcoming) -->
    <section
        class="lg:col-span-3 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden text-ellipsis">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-[10px] font-bold text-gray-900 uppercase tracking-widest">Próximos Cobros</h3>
        </div>
        <div id="upcomingList" class="flex-grow overflow-y-auto p-4 space-y-2 custom-scrollbar">
            <!-- Cards -->
        </div>
    </section>

    <!-- Right Column (Calendar) -->
    <section class="lg:col-span-6 flex flex-col bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div class="flex items-center space-x-3">
                <h2 class="text-base font-bold" id="currentMonthYear">...</h2>
                <div class="flex bg-white shadow-sm border border-gray-200 rounded-lg p-0.5">
                    <button id="prevMonth"
                        class="px-2 py-0.5 hover:bg-gray-100 rounded-md transition-all text-sm">&lt;</button>
                    <button id="todayBtn"
                        class="px-2 py-0.5 hover:bg-gray-100 rounded-md transition-all text-[9px] font-bold uppercase tracking-wider">Hoy</button>
                    <button id="nextMonth"
                        class="px-2 py-0.5 hover:bg-gray-100 rounded-md transition-all text-sm">&gt;</button>
                </div>
            </div>
            <select id="filterCategory"
                class="text-[9px] font-bold uppercase tracking-wider bg-white border border-gray-200 rounded-lg px-2 py-1.5 outline-none">
                <option value="">Categorías</option>
            </select>
        </div>

        <div class="calendar-grid bg-gray-100 gap-[1px] pr-[3px] border-b border-gray-100">
            <div class="bg-white py-1.5 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none flex items-center justify-center">Dom</div>
            <div class="bg-white py-1.5 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none flex items-center justify-center">Lun</div>
            <div class="bg-white py-1.5 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none flex items-center justify-center">Mar</div>
            <div class="bg-white py-1.5 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none flex items-center justify-center">Mié</div>
            <div class="bg-white py-1.5 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none flex items-center justify-center">Jue</div>
            <div class="bg-white py-1.5 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none flex items-center justify-center">Vie</div>
            <div class="bg-white py-1.5 text-center text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none flex items-center justify-center">Sáb</div>
        </div>

        <div id="calendarDays" class="calendar-grid bg-gray-100 gap-[1px] flex-grow overflow-y-auto custom-scrollbar">
            <!-- Days -->
        </div>
    </section>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 3px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #e2e8f0;
        border-radius: 10px;
    }

    .calendar-day {
        min-height: 65px;
        transition: all 0.2s;
    }

    .calendar-day:hover {
        background-color: #f8fafc;
    }

    @media (max-width: 1024px) {
        .h-\[calc\(100vh-140px\)\] {
            height: auto;
            max-height: none;
            overflow: visible;
        }
    }
</style>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>