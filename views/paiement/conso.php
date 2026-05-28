<?php
$title = "Paiements | Gestion des abonnés Snel";
ob_start();
?>
<!-- Page Paiements -->
<div x-show="currentPage === 'paiements'" x-transition>
    <h2 class="text-2xl font-bold mb-4">💳 Paiements</h2>
    <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
    <table class="w-full text-sm"><thead class="bg-gray-50"><tr><th>Abonné</th><th>Montant</th><th>Date</th><th>Mode</th></tr></thead>
        <tbody><template x-for="p in paiements" :key="p.id"><tr class="border-t"><td x-text="nomAbonne(p.abonneId)"></td><td x-text="p.montant+' CDF'"></td><td x-text="p.date"></td><td x-text="p.mode"></td></tr></template></tbody>
    </table>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . "/../templete_app/main_templete.php";
?>