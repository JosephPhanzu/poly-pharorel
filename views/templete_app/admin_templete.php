<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.2, user-scalable=yes">
  <title>SNEL Boma • Nzadi — Gestion des abonnés</title>
  <!-- Tailwind via CDN + quelques polices & icônes -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome 6 pour icônes -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
  <!-- Chart.js (pour graphiques dashboard) -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <style>
    /* transitions douces pour le sidebar mobile */
    .sidebar-mobile-enter { transform: translateX(-100%); }
    .sidebar-mobile-enter-active { transform: translateX(0); transition: transform 0.3s ease; }
    .sidebar-mobile-leave { transform: translateX(0); }
    .sidebar-mobile-leave-active { transform: translateX(-100%); transition: transform 0.3s ease; }
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: #f5f7fb; }
    /* scrollbar fine pour tableaux */
    .table-responsive::-webkit-scrollbar { height: 6px; background: #e2e8f0; border-radius: 8px; }
    .table-responsive::-webkit-scrollbar-thumb { background: #1E3A8A; border-radius: 8px; }
    @media (max-width: 768px) {
      .sidebar-overlay { background: rgba(0,0,0,0.5); }
    }
    .card-gradient { background: linear-gradient(135deg, #1E3A8A 0%, #F59E0B 100%); }
    .badge-paye { background: #10b98120; color: #065f46; border:1px solid #10b98150; }
    .badge-impaye { background: #ef444420; color: #991b1b; border:1px solid #ef444450; }
  </style>
</head>
<body class="text-gray-800 antialiased" x-data="snelApp()" x-init="initApp()">
  <!-- Overlay mobile quand sidebar ouvert -->
  <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/40 z-40 md:hidden backdrop-blur-sm" x-transition.opacity></div>

  <!-- Layout global -->
  <div class="flex h-screen overflow-hidden">
    <!-- ========== SIDEBAR ========== -->
    <aside :class="sidebarOpen ? 'max-md:translate-x-0' : 'max-md:-translate-x-full'"
           class="fixed md:static inset-y-0 left-0 z-50 w-64 bg-[#0f1d3a] text-white flex flex-col transition-transform duration-300 ease-in-out shadow-2xl md:shadow-none">
      <div class="p-5 flex items-center gap-3 border-b border-white/10">
        <div class="w-9 h-9 bg-yellow-400 rounded-lg flex items-center justify-center text-[#0f1d3a] font-extrabold text-xl"><i class="fas fa-bolt"></i></div>
        <div>
          <h1 class="font-bold text-sm tracking-wide">SNEL BOMA</h1>
          <p class="text-[10px] text-yellow-300/90">Commune de Nzadi</p>
        </div>
        <button @click="sidebarOpen = false" class="md:hidden ml-auto text-white/70"><i class="fas fa-times text-xl"></i></button>
      </div>
      <nav class="flex-1 px-3 py-6 space-y-1.5 overflow-y-auto">
        <template x-for="item in menuItems" :key="item.id">
          <button @click="setPage(item.id)" 
                  :class="currentPage === item.id ? 'bg-yellow-400 text-[#0f1d3a] font-semibold shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white'"
                  class="w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all text-left">
            <i :class="item.icon" class="text-lg w-5 text-center"></i>
            <span x-text="item.label"></span>
          </button>
        </template>
      </nav>
      <div class="p-4 border-t border-white/10 text-xs text-white/50 flex items-center gap-2">
        <i class="fas fa-cog"></i> Paramètres • v1.0
      </div>
    </aside>

    <!-- ========== MAIN CONTENT ========== -->
    <div class="flex-1 flex flex-col min-w-0 bg-gray-50/80">
      <!-- NAVBAR HEADER -->
      <header class="bg-white shadow-sm border-b border-gray-200 px-4 md:px-6 py-3 flex items-center gap-4 sticky top-0 z-30">
        <button @click="sidebarOpen = !sidebarOpen" class="md:hidden text-gray-700 text-2xl"><i class="fas fa-bars"></i></button>
        <div class="flex-1 flex items-center gap-3">
          <div class="relative w-full max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="search" placeholder="Rechercher abonné, facture..." x-model="globalSearch"
                   class="w-full pl-9 pr-4 py-2.5 bg-gray-100 border-none rounded-xl text-sm focus:ring-2 focus:ring-yellow-400 outline-none">
          </div>
        </div>
        <div class="flex items-center gap-3 sm:gap-4">
          <button class="relative text-gray-500 hover:text-yellow-600"><i class="far fa-bell text-xl"></i><span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-red-500 rounded-full border border-white"></span></button>
          <div class="flex items-center gap-2 pl-2 border-l">
            <div class="w-8 h-8 rounded-full bg-blue-800 text-white flex items-center justify-center text-sm font-bold">SK</div>
            <span class="hidden sm:inline text-sm font-medium">Admin Nzadi</span>
          </div>
        </div>
      </header>

      <!-- CONTENU DYNAMIQUE -->
      <main class="flex-1 overflow-y-auto p-4 md:p-6">
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

        <!-- Page Abonnés -->
        <div x-show="currentPage === 'abonnes'" x-transition>
          <div class="flex flex-wrap justify-between items-center mb-4 gap-3">
            <h2 class="text-2xl font-bold">👥 Abonnés</h2>
            <button @click="openModal('abonne')" class="bg-yellow-400 hover:bg-yellow-500 text-blue-900 px-4 py-2.5 rounded-xl font-semibold shadow-sm flex items-center gap-2"><i class="fas fa-plus"></i> Ajouter</button>
          </div>
          <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
            <div class="p-3 border-b flex gap-2"><input type="text" placeholder="Filtrer..." x-model="filtreAbonne" class="border rounded-lg px-3 py-2 text-sm w-60"></div>
            <div class="table-responsive overflow-x-auto">
              <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600"><tr><th class="p-3 text-left">#Client</th><th>Nom</th><th>Adresse</th><th>Tél</th><th>Statut</th><th class="text-center">Actions</th></tr></thead>
                <tbody>
                  <template x-for="a in filteredAbonnes()" :key="a.id">
                    <tr class="border-t hover:bg-yellow-50/40">
                      <td class="p-3 font-mono text-xs" x-text="a.numero"></td>
                      <td x-text="a.nom"></td>
                      <td x-text="a.adresse"></td>
                      <td x-text="a.tel"></td>
                      <td><span :class="a.actif ? 'text-emerald-700 bg-emerald-100' : 'text-gray-500 bg-gray-200'" class="px-2 py-0.5 rounded-full text-xs font-medium" x-text="a.actif ? 'Actif' : 'Inactif'"></span></td>
                      <td class="text-center">
                        <button @click="voirAbonne(a)" class="text-blue-700 mx-1"><i class="fas fa-eye"></i></button>
                        <button @click="modifierAbonne(a)" class="text-yellow-600 mx-1"><i class="fas fa-edit"></i></button>
                        <button @click="supprimerAbonne(a.id)" class="text-red-500 mx-1"><i class="fas fa-trash"></i></button>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Page Consommation -->
        <div x-show="currentPage === 'consommation'" x-transition>
          <div class="flex justify-between items-center mb-4"><h2 class="text-2xl font-bold">⚡ Consommation</h2><button @click="openModal('conso')" class="bg-yellow-400 px-4 py-2 rounded-xl font-semibold"><i class="fas fa-plus"></i> Ajouter</button></div>
          <div class="bg-white rounded-2xl shadow-sm overflow-x-auto p-2">
            <table class="w-full text-sm"><thead class="bg-gray-50"><tr><th>Abonné</th><th>Ancien index</th><th>Nouvel index</th><th>kWh</th><th>Mois</th></tr></thead>
              <tbody><template x-for="c in consommations" :key="c.id"><tr class="border-t"><td x-text="nomAbonne(c.abonneId)"></td><td x-text="c.ancien"></td><td x-text="c.nouveau"></td><td class="font-bold" x-text="c.nouveau - c.ancien"></td><td x-text="c.mois"></td></tr></template></tbody>
            </table>
          </div>
        </div>

        <!-- Page Factures -->
        <div x-show="currentPage === 'factures'" x-transition>
          <h2 class="text-2xl font-bold mb-4">🧾 Factures</h2>
          <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm"><thead class="bg-gray-50"><tr><th>Abonné</th><th>Montant</th><th>Mois</th><th>Statut</th></tr></thead>
              <tbody><template x-for="f in factures" :key="f.id"><tr class="border-t"><td x-text="nomAbonne(f.abonneId)"></td><td x-text="f.montant+' CDF'"></td><td x-text="f.mois"></td><td><span :class="f.paye ? 'badge-paye' : 'badge-impaye'" class="px-2 py-0.5 rounded-full text-xs" x-text="f.paye ? 'Payé' : 'Impayé'"></span></td></tr></template></tbody>
            </table>
          </div>
        </div>

        <!-- Page Paiements -->
        <div x-show="currentPage === 'paiements'" x-transition>
          <h2 class="text-2xl font-bold mb-4">💳 Paiements</h2>
          <div class="bg-white rounded-2xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm"><thead class="bg-gray-50"><tr><th>Abonné</th><th>Montant</th><th>Date</th><th>Mode</th></tr></thead>
              <tbody><template x-for="p in paiements" :key="p.id"><tr class="border-t"><td x-text="nomAbonne(p.abonneId)"></td><td x-text="p.montant+' CDF'"></td><td x-text="p.date"></td><td x-text="p.mode"></td></tr></template></tbody>
            </table>
          </div>
        </div>

        <!-- Paramètres (placeholder) -->
        <div x-show="currentPage === 'parametres'" x-transition><div class="bg-white p-8 rounded-2xl">Paramètres de l'application (tarifs, profil).</div></div>
      </main>
    </div>
  </div>

  <!-- MODAL Ajout/Modification -->
  <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm" x-transition>
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl" @click.outside="modalOpen=false">
      <h3 class="text-xl font-bold mb-4" x-text="modalTitre"></h3>
      <form @submit.prevent="sauvegarderModal()">
        <template x-if="modalType==='abonne'">
          <div class="space-y-3">
            <input placeholder="Numéro client" x-model="formAbonne.numero" class="w-full border p-2.5 rounded-lg">
            <input placeholder="Nom complet" x-model="formAbonne.nom" class="w-full border p-2.5 rounded-lg">
            <input placeholder="Adresse" x-model="formAbonne.adresse" class="w-full border p-2.5 rounded-lg">
            <input placeholder="Téléphone" x-model="formAbonne.tel" class="w-full border p-2.5 rounded-lg">
            <label class="flex items-center gap-2"><input type="checkbox" x-model="formAbonne.actif"> Actif</label>
          </div>
        </template>
        <template x-if="modalType==='conso'">
          <div class="space-y-3">
            <select x-model="formConso.abonneId" class="w-full border p-2.5 rounded-lg">
              <option value="">-- Abonné --</option>
              <template x-for="a in abonnes" :key="a.id"><option :value="a.id" x-text="a.nom"></option></template>
            </select>
            <input type="number" placeholder="Ancien index" x-model="formConso.ancien" class="w-full border p-2.5 rounded-lg">
            <input type="number" placeholder="Nouvel index" x-model="formConso.nouveau" class="w-full border p-2.5 rounded-lg">
            <input type="text" placeholder="Mois (ex: Mars 2026)" x-model="formConso.mois" class="w-full border p-2.5 rounded-lg">
          </div>
        </template>
        <div class="flex justify-end gap-2 mt-5">
          <button type="button" @click="modalOpen=false" class="px-4 py-2 border rounded-lg">Annuler</button>
          <button type="submit" class="bg-yellow-400 px-5 py-2 rounded-lg font-semibold text-blue-900">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function snelApp() {
      return {
        sidebarOpen: false,
        currentPage: 'dashboard',
        globalSearch: '',
        menuItems: [
          { id: 'dashboard', label: 'Dashboard', icon: 'fas fa-th-large' },
          { id: 'abonnes', label: 'Abonnés', icon: 'fas fa-users' },
          { id: 'consommation', label: 'Consommation', icon: 'fas fa-bolt' },
          { id: 'factures', label: 'Factures', icon: 'fas fa-file-invoice' },
          { id: 'paiements', label: 'Paiements', icon: 'fas fa-credit-card' },
          { id: 'parametres', label: 'Paramètres', icon: 'fas fa-cog' }
        ],
        // données simulées
        abonnes: [
          { id:1, numero:'SNL-001', nom:'Kiese M.', adresse:'Av. Nzadi 12', tel:'0812345678', actif:true },
          { id:2, numero:'SNL-002', nom:'Luzolo P.', adresse:'Q. Marine 5', tel:'0823456789', actif:true },
          { id:3, numero:'SNL-003', nom:'Mavungu J.', adresse:'Cité Nzadi', tel:'0834567890', actif:false }
        ],
        consommations: [
          { id:1, abonneId:1, ancien:1200, nouveau:1350, mois:'Mars 2026' },
          { id:2, abonneId:2, ancien:800, nouveau:940, mois:'Mars 2026' }
        ],
        factures: [
          { id:1, abonneId:1, montant:22500, mois:'Mars 2026', paye:false },
          { id:2, abonneId:2, montant:21000, mois:'Mars 2026', paye:true }
        ],
        paiements: [
          { id:1, abonneId:2, montant:21000, date:'2026-03-25', mode:'Mobile Money' }
        ],
        filtreAbonne: '',
        modalOpen: false, modalType:'', modalTitre:'', editingId: null,
        formAbonne: { numero:'', nom:'', adresse:'', tel:'', actif:true },
        formConso: { abonneId:'', ancien:0, nouveau:0, mois:'' },
        chartInstance: null,

        initApp() {
          this.$watch('currentPage', () => { if (this.currentPage==='dashboard') this.$nextTick(()=> this.initChart()); });
          this.$nextTick(()=> this.initChart());
        },
        setPage(page) { this.currentPage = page; if(window.innerWidth<768) this.sidebarOpen=false; },
        filteredAbonnes() {
          return this.abonnes.filter(a => a.nom.toLowerCase().includes(this.filtreAbonne.toLowerCase()) || a.numero.includes(this.filtreAbonne));
        },
        nomAbonne(id) { const a = this.abonnes.find(x=>x.id==id); return a ? a.nom : '?'; },
        totalConsommationMois() {
          return this.consommations.reduce((acc,c)=> acc + (c.nouveau - c.ancien),0);
        },
        totalFacture() { return this.factures.reduce((s,f)=> s+f.montant,0); },
        payeMontant() { return this.factures.filter(f=>f.paye).reduce((s,f)=>s+f.montant,0); },
        impayeMontant() { return this.factures.filter(f=>!f.paye).reduce((s,f)=>s+f.montant,0); },

        openModal(type, data=null) {
          this.modalType = type;
          if(type==='abonne') {
            this.modalTitre = data ? 'Modifier abonné' : 'Nouvel abonné';
            this.formAbonne = data ? {...data} : { numero:'', nom:'', adresse:'', tel:'', actif:true };
            this.editingId = data?.id || null;
          } else if(type==='conso') {
            this.modalTitre = 'Ajouter consommation';
            this.formConso = { abonneId:'', ancien:0, nouveau:0, mois:'' };
          }
          this.modalOpen = true;
        },
        sauvegarderModal() {
          if(this.modalType==='abonne') {
            if(this.editingId) {
              const idx = this.abonnes.findIndex(a=>a.id===this.editingId);
              if(idx>-1) this.abonnes[idx] = {...this.formAbonne, id:this.editingId};
            } else {
              this.abonnes.push({...this.formAbonne, id: Date.now()});
            }
          } else if(this.modalType==='conso') {
            this.consommations.push({...this.formConso, id: Date.now(), ancien: Number(this.formConso.ancien), nouveau: Number(this.formConso.nouveau)});
          }
          this.modalOpen = false;
        },
        voirAbonne(a) { alert(`Détail : ${a.nom}, ${a.adresse}`); },
        modifierAbonne(a) { this.openModal('abonne', a); },
        supprimerAbonne(id) { if(confirm('Supprimer ?')) this.abonnes = this.abonnes.filter(a=>a.id!==id); },
        initChart() {
          if(this.chartInstance) this.chartInstance.destroy();
          const ctx = document.getElementById('consoChart');
          if(!ctx) return;
          this.chartInstance = new Chart(ctx, {
            type:'line', data:{
              labels:['Jan','Fév','Mar','Avr','Mai'],
              datasets:[{ label:'kWh', data:[450,520,480, this.totalConsommationMois(), 510], borderColor:'#1E3A8A', backgroundColor:'#F59E0B30', tension:0.2 }]
            }
          });
        }
      }
    }
  </script>
</body>
</html>