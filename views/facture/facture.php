<?php

// use App\Facture;
// use App\Facture_conv;
$title = "Facture | Welcome";



ob_start();
?>
<div x-transition>
    <h2 class="text-2xl font-bold mb-4">🧾 Factures</h2>

    <div class="flex flex-wrap gap-3 mb-4">
        <div>
            <label class="block text-sm font-medium">Abonné</label>
            <select x-model="filtreAbonne" class="mt-1 border rounded px-3 py-2">
            <option value="">Tous les abonnés</option>
            <template x-for="a in abonnes" :key="a.id">
                <option :value="a.id" x-text="a.nom"></option>
            </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium">Mois</label>
            <select x-model="filtreMois" class="mt-1 border rounded px-3 py-2">
            <option value="">Tous les mois</option>
            <template x-for="mois in moisDisponibles()" :key="mois">
                <option :value="mois" x-text="mois"></option>
            </template>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th>Abonné</th>
                    <th>Montant</th>
                    <th>Mois</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="f in paginatedFactures" :key="f.id">
                    <tr class="border-t">
                        <td class="py-2 px-2" x-text="f.nom"></td>
                        <td class="py-2 px-2" x-text="f.montant+' CDF'"></td>
                        <td class="py-2 px-2" x-text="f.mois"></td>
                        <td class="py-2 px-2">
                            <span :class="f.paye ? 'badge-paye' : 'badge-impaye'" class="px-2 py-0.5 rounded-full text-xs" x-text="f.paye ? 'Payé' : 'Impayé'"></span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="flex items-center justify-between mt-4 px-6">
            <button :disabled="pageFactures === 1" @click="pageFactures--" class="bg-blue-500 rounded-lg p-2 text-white m-2">Préc</button>
            <span>Page <span x-text="pageFactures"></span> / <span x-text="totalPages(factures)"></span></span>
            <button :disabled="pageFactures >= totalPages(factures)" @click="pageFactures++" class="bg-blue-500 rounded-lg p-2 text-white m-2">Suiv</button>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require __DIR__ . "/../templete_app/main_templete.php";
