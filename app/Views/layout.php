<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo APP_NAME; ?>
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }

        .calendar-day {
            min-height: 100px;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 min-h-screen">
    <nav class="glass sticky top-0 z-10 border-b border-gray-200 px-6 py-4 flex justify-between items-center w-full">
        <div class="flex items-center space-x-4">
            <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">
                Subscription Calendar
            </h1>
            <span class="text-xs text-gray-400 font-medium bg-gray-100 px-2 py-1 rounded-lg">
                <?php echo $_SESSION['user_email']; ?>
            </span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="/logout" class="text-sm text-gray-500 hover:text-red-600 font-medium transition-colors">Cerrar
                Sesión</a>
            <button id="addSubscriptionBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition-all shadow-md active:scale-95">
                + Nueva Suscripción
            </button>
        </div>
    </nav>

    <main class="w-full px-6 py-6">
        <?php echo $content; ?>
    </main>

    <!-- Modal Placeholder -->
    <div id="modalContainer"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold" id="modalTitle">Suscripción</h3>
                <button id="closeModal" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>
            <div class="p-6">
                <form id="subscriptionForm" class="space-y-4">
                    <input type="hidden" name="id" id="subId">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" id="subName" required
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Precio</label>
                            <input type="number" step="0.01" name="price" id="subPrice" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Moneda</label>
                            <select name="currency" id="subCurrency"
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                                <option value="MXN">MXN</option>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ciclo de Cobro</label>
                            <select name="billing_cycle" id="subCycle" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                                <option value="weekly">Semanal</option>
                                <option value="biweekly">Quincenal</option>
                                <option value="monthly">Mensual</option>
                                <option value="yearly">Anual</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                            <input type="date" name="start_date" id="subStartDate" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1 font-bold">Fecha Fin
                            (Opcional)</label>
                        <input type="date" name="end_date" id="subEndDate"
                            class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                            <input type="text" name="category" id="subCategory" list="categories" required
                                class="w-full px-4 py-2 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                            <datalist id="categories">
                                <option value="Streaming">
                                <option value="Trabajo">
                                <option value="Hogar">
                                <option value="Ocio">
                            </datalist>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input type="color" name="color" id="subColor" value="#3b82f6"
                                class="w-full h-10 p-1 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition-all">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" id="deleteBtn"
                            class="hidden px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-all">Eliminar</button>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-xl hover:bg-blue-700 shadow-lg active:scale-95 transition-all">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="/assets/js/utils.js"></script>
    <script src="/assets/js/calendar.js"></script>
    <script src="/assets/js/app.js"></script>
</body>

</html>