<?php
$title = "Tableau de bord | MediFlow";
ob_start();
?>

    <!-- Dashboard -->
        <div x-show="currentPage === 'dashboard'" x-transition>
          <h2 class="text-2xl font-bold text-gray-800 mb-1">Tableau de bord</h2>
          <p class="text-sm text-gray-500 mb-6">Nzadi, Boma • Aperçu énergétique</p>
          <!-- Stats cards -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-yellow-400 flex justify-between">
              <div><p class="text-sm text-gray-500">Total abonnés</p><p class="text-3xl font-bold" x-text="abonnes.length"></p></div>
              <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600"><i class="fas fa-users"></i></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-blue-800 flex justify-between">
              <div><p class="text-sm text-gray-500">Conso. mois (kWh)</p><p class="text-3xl font-bold" x-text="totalConsommationMois()"></p></div>
              <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-800"><i class="fas fa-bolt"></i></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-emerald-500 flex justify-between">
              <div><p class="text-sm text-gray-500">Facturé (CDF)</p><p class="text-3xl font-bold" x-text="totalFacture()"></p></div>
              <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-700"><i class="fas fa-file-invoice"></i></div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border-l-4 border-rose-400 flex justify-between">
              <div><p class="text-sm text-gray-500">Impayé / Payé</p><p class="text-3xl font-bold"><span x-text="impayeMontant()" class="text-rose-600"></span>/<span x-text="payeMontant()" class="text-emerald-600"></span></p></div>
              <div class="w-10 h-10 bg-rose-100 rounded-xl flex items-center justify-center text-rose-500"><i class="fas fa-hand-holding-usd"></i></div>
            </div>
          </div>
          <!-- Graphique -->
          <div class="bg-white p-5 rounded-2xl shadow-sm">
            <h3 class="font-semibold mb-3 text-gray-700"><i class="fas fa-chart-line mr-2 text-yellow-500"></i>Évolution mensuelle (kWh)</h3>
            <canvas id="consoChart" height="80"></canvas>
          </div>
        </div>

<?php
$content = ob_get_clean();
require __DIR__ . "/../templete_app/main_templete.php";
?>