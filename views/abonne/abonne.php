<?php
$title = "Abonnés | Gestion des abonnés Snel";
ob_start();
?>
<!-- Page Abonnés -->
 <!-- <H1>HELLO WORLD</H1> -->
<div  x-transition>
    <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
    <h2 class="text-2xl font-bold">👥 Abonnés</h2>
    <button @click="openModal('abonne')" class="bg-yellow-400 hover:bg-yellow-500 text-blue-900 px-4 py-2.5 rounded-xl font-semibold shadow-sm flex items-center gap-2"><i class="fas fa-plus"></i> Ajouter</button>
    </div>
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-3 border-b flex gap-2">
        <input type="text" placeholder="Filtrer..." x-model="filtreAbonne" class="border rounded-lg px-3 py-2 text-sm w-60">
    </div>
    <div class="table-responsive overflow-x-auto">
        <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
            <th class="p-3 text-left">#Client</th>
            <th>Nom</th>
            <th>Adresse</th>
            <th>Tél</th>
            <th>Statut</th>
            <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <template x-for="a in paginatedAbonnes()" :key="a.id">
            <tr class="border-t hover:bg-yellow-50/40">
                <td class="p-3 font-mono text-xs" x-text="a.numero_compteur"></td>
                <td x-text="a.nom"></td>
                <td x-text="a.adresse"></td>
                <td x-text="a.telephone"></td>
                <td><span :class="a.statut === 'Actif' ? 'text-emerald-700 bg-emerald-100' : 'text-gray-500 bg-gray-200'" class="px-2 py-0.5 rounded-full text-xs font-medium" x-text="a.statut"></span></td>
                <td class="text-center">
                <button @click="voirAbonne(a)" class="text-blue-700 mx-1"><i class="fas fa-eye"></i></button>
                <button @click="modifierAbonne(a)" class="text-yellow-600 mx-1"><i class="fas fa-edit"></i></button>
                <button @click="supprimerAbonne(a.id)" class="text-red-500 mx-1"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
            </template>
        </tbody>
        </table>
        <div class="flex items-center justify-between mt-4 px-6">
            <button :disabled="pageAbonnes === 1" @click="pageAbonnes--" class="bg-blue-500 rounded-lg p-2 text-white m-2">Préc</button>
            <span>Page <span x-text="pageAbonnes"></span> / <span x-text="totalPages(filteredAbonnes())"></span></span>
            <button :disabled="pageAbonnes >= totalPages(filteredAbonnes())" @click="pageAbonnes++" class="bg-blue-500 rounded-lg p-2 text-white m-2">Suiv</button>
        </div>
    </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . "/../templete_app/main_templete.php";
?>