<?php

  use App\Permission;

  $permission = new Permission();
//   if (!$session->isConnected() ) {
//     $session->logout();
//     header('Location: /login');
//   }

  // $nom = $session->getNom();
  // $prenom = $session->getPrenom();

  // $role = $session->getRole();


  $currentPage = trim($_SERVER['REQUEST_URI'], '/');

    $currentPage = parse_url($currentPage, PHP_URL_PATH);

    function currentPageActive($uri, $class, $currentPage) {
        if (is_array($uri)) :
            echo in_array($currentPage, $uri) ? $class : "text-gray-600 hover:bg-gray-100 transition";
        else :
            echo $currentPage === $uri ? $class : "text-gray-600 hover:bg-gray-100 transition";
        endif;
    }

    $active = 'bg-primary/10 text-primary font-medium';

?>
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
  <!-- Ajouter Axios AVANT votre script Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- CDN jquery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Votre service API -->
    <!-- <script src="api/snelApi.js"></script> -->
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
    .success { background: #10b98120; color: #065f46; border:1px solid #10b98150; }
    .error { background: #ef444420; color: #991b1b; border:1px solid #ef444450; }
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

        <!-- Dashboard Content -->
        <main class="p-4 sm:p-6 space-y-6">
            <script src="https://cdn.jsdelivr.net/npm/axios@1.9.0/dist/axios.min.js"></script>
            <?= $content ?? '' ?>
        </main>

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
        <div id="info" class="d-none my-3 p-3 rounded-lg"></div>
        <template x-if="modalType==='abonne'">
          <div class="space-y-3">
            <input placeholder="Numéro client" x-model="formAbonne.numero_compte" class="w-full border p-2.5 rounded-lg">
            <input placeholder="Nom complet" x-model="formAbonne.nom" class="w-full border p-2.5 rounded-lg">
            <input placeholder="Adresse" x-model="formAbonne.adresse" class="w-full border p-2.5 rounded-lg">
            <input placeholder="Téléphone" x-model="formAbonne.telephone" class="w-full border p-2.5 rounded-lg">
            <input type="email" placeholder="E-mail" x-model="formAbonne.email" class="w-full border p-2.5 rounded-lg">
            <input placeholder="Mot de passe" type="password" x-model="formAbonne.mdp" class="w-full border p-2.5 rounded-lg">
            <select name="statut" id="" x-model="formAbonne.statut">
              <option value="">-- Statut --</option>
              <option value="actif">Actif</option>
              <option value="inactif">Inactif</option>
            </select>
            
          </div>
        </template>
        <template x-if="modalType==='conso'">
          <div class="space-y-3">
            <select x-model="formConso.abonneId" class="w-full border p-2.5 rounded-lg">
              <option value="">-- Abonné --</option>
              <template x-for="a in abonnes" :key="a.id"><option :value="a.id" x-text="a.nom"></option></template>
            </select>
            <div>
              <label for="ancien">Ancien Index</label>
              <input type="number" id="ancien" placeholder="Ancien index" x-model="formConso.ancien" class="w-full border p-2.5 rounded-lg">
            </div>
            
            <div>
                <label for="nouveu">Nouvel Index</label>
                <input type="number" id="nouveau" placeholder="Nouvel index" x-model="formConso.nouveau" class="w-full border p-2.5 rounded-lg">
            </div>
            
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

    $(document).on('click', '#deconnexion', function(e){
        window.location.replace('/deconnexion');
    })



  </script>

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

        //Données simulées pour tests (remplacer par appels API réels)
        getDataFromDB() {
          // Ici vous feriez des appels AJAX pour récupérer les données réelles depuis votre backend
          // Exemple avec Axios :
          
          // this.abonnes = [];
          axios.get('/get_abonnes')
          .then(res => {
            this.abonnes = res.data.data
            console.log(res.data.message);
          })
          .catch(err => console.error('Erreur chargement abonnés', err));
          
          axios.get('/get_conso')
          .then(res => {
            this.consommations = res.data.data;
            console.log(res.data.message);
          })
          .catch(err => console.error('Erreur chargement consommations', err));

          axios.get('/get_facture')
          .then(res => {
            this.factures = res.data.data;
            console.log(res.data.message);
          })
          .catch(err => console.error('Erreur chargement factures', err));
          
          // axios.get('/api/paiements').then(res => this.paiements = res.data);
          
        },

        // données simulées
        abonnes: [
          { id:1, numero_compte:'SNL-001', nom:'Kiese M.', adresse:'Av. Nzadi 12', telephone:'0812345678', actif:true },
          { id:2, numero_compte:'SNL-002', nom:'Luzolo P.', adresse:'Q. Marine 5', telephone:'0823456789', actif:true },
          { id:3, numero_compte:'SNL-003', nom:'Mavungu J.', adresse:'Cité Nzadi', telephone:'0834567890', actif:false }
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
        formAbonne: { numero_compte:'', nom:'', adresse:'', telephone:'', actif:true },
        formConso: { abonneId:'', ancien:0, nouveau:0, mois:'' },
        chartInstance: null,

        initApp() {
          this.getDataFromDB();
          this.$watch('currentPage', () => { if (this.currentPage==='dashboard') this.$nextTick(()=> this.initChart()); });
          this.$nextTick(()=> this.initChart());
        },
        setPage(page) { 
          // this.currentPage = page; 
          // if(window.innerWidth<768) this.sidebarOpen=false; 
          window.location.href=page;
        },

        
        perPage: 10,
        pageAbonnes: 1,
        pageConsommations: 1,
        pageFactures: 1,
        pagePaiements: 1,
        filtreAbonne: '',
        filtreMois: '',
        
        paginate(items, page = 1, perPage = this.perPage) {
          const start = (page - 1) * perPage;
          return items.slice(start, start + perPage);
        },



        totalPages(items) {
          return Math.max(1, Math.ceil(items.length / this.perPage));
        },
        paginatedAbonnes() {
          return this.paginate(this.filteredAbonnes(), this.pageAbonnes);
        },
        setTablePage(tableKey, page) {
          this[tableKey] = page;
        },
        // Filtrage des abonnés par nom ou numéro de compte
        filteredAbonnes() {
          return this.abonnes.filter(a =>
            a.nom.toLowerCase().includes(this.filtreAbonne.toLowerCase()) ||
            a.numero_compte.toLowerCase().includes(this.filtreAbonne.toLowerCase())
          );
        },
        
        filteredAbonnes() {
          return this.abonnes.filter(a => a.nom.toLowerCase().includes(this.filtreAbonne.toLowerCase()) || a.numero_compteur.includes(this.filtreAbonne));
        },

        // filtrage des consommations, factures et paiements par abonné et mois
        filterConsommations() {
          return this.consommations.filter(c => {
            // supporte deux formes possibles des objets consommation :
            // - { abonneId: 1, mois: 'Mars 2026', ... }
            // - { nom: 'X', mois: 'Mars', annee: '2026', ... }
            const selectedAbonne = this.filtreAbonne === '' ? '' : parseInt(this.filtreAbonne);
            let okAbonne = true;
            if (selectedAbonne) {
              if (c.abonneId !== undefined) okAbonne = c.abonneId == selectedAbonne;
              else if (c.abonne_id !== undefined) okAbonne = c.abonne_id == selectedAbonne;
              else if (c.nom) {
                const a = this.abonnes.find(x => x.id == selectedAbonne);
                okAbonne = a ? (a.nom === c.nom) : false;
              } else okAbonne = false;
            }
            const moisStr = (c.mois ? c.mois : '') + (c.annee ? ' ' + c.annee : (c.annee === 0 ? '' : ''));
            const okMois = !this.filtreMois || (c.mois && (c.mois === this.filtreMois || moisStr === this.filtreMois));
            return okAbonne && okMois;
          });
        },
        paginatedConsommations() {
          return this.paginate(this.filterConsommations(), this.pageConsommations);
        },
        filterFactures() {
          const selectedAbonne = this.filtreAbonne === '' ? '' : parseInt(this.filtreAbonne);
          return this.factures.filter(f => {
            let okAbonne = true;
            if (selectedAbonne) {
              if (f.abonneId !== undefined) okAbonne = f.abonneId == selectedAbonne;
              else if (f.abonne_id !== undefined) okAbonne = f.abonne_id == selectedAbonne;
              else if (f.nom) {
                const a = this.abonnes.find(x => x.id == selectedAbonne);
                okAbonne = a ? (a.nom === f.nom) : false;
              } else okAbonne = false;
            }
            const moisStr = (f.mois ? f.mois : '') + (f.annee ? ' ' + f.annee : '');
            const okMois = !this.filtreMois || (f.mois && (f.mois === this.filtreMois || moisStr === this.filtreMois));
            return okAbonne && okMois;
          });
        },

        filterPaiements() {
          const selectedAbonne = this.filtreAbonne === '' ? '' : parseInt(this.filtreAbonne);
          const moisNames = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
          return this.paiements.filter(p => {
            let okAbonne = true;
            if (selectedAbonne) {
              if (p.abonneId !== undefined) okAbonne = p.abonneId == selectedAbonne;
              else if (p.abonne_id !== undefined) okAbonne = p.abonne_id == selectedAbonne;
              else if (p.nom) {
                const a = this.abonnes.find(x => x.id == selectedAbonne);
                okAbonne = a ? (a.nom === p.nom) : false;
              } else okAbonne = false;
            }

            let okMois = true;
            if (this.filtreMois) {
              if (p.mois) {
                const moisStr = p.mois + (p.annee ? ' ' + p.annee : '');
                okMois = p.mois === this.filtreMois || moisStr === this.filtreMois;
              } else if (p.date) {
                const d = new Date(p.date);
                if (isNaN(d)) okMois = false;
                else {
                  const m = moisNames[d.getMonth()] + ' ' + d.getFullYear();
                  okMois = m === this.filtreMois;
                }
              } else okMois = false;
            }

            return okAbonne && okMois;
          });
        },
        paginatedFactures() {
          return this.paginate(this.filterFactures(), this.pageFactures);
        },
        paginatedPaiements() {
          return this.paginate(this.filterPaiements(), this.pagePaiements);
        },
        moisDisponibles() {
          return [...new Set(this.consommations.map(c => c.mois).concat(this.factures.map(f => f.mois)))];
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
            this.formAbonne = data ? {...data} : { numero_compteur:'', nom:'', adresse:'', telephone:'', actif:true };
            this.editingId = data?.id || null;
          } else if(type==='conso') {
            this.modalTitre = 'Ajouter consommation';
            this.formConso = { abonneId:'', ancien:0, nouveau:0, mois:'' };
          }
          this.modalOpen = true;
        },
        sauvegarderModal ()  {
          let formdata = new FormData();
              let endpoint = '';
          if(this.modalType ==='abonne') {
            // alert('hello');
            if(this.editingId) {
              const idx = this.abonnes.findIndex(a=>a.id===this.editingId);
              if(idx>-1) this.abonnes[idx] = {...this.formAbonne, id:this.editingId};
            } else {
                            
              formdata.append('numero_compteur', this.formAbonne.numero_compte);
              formdata.append('nom', this.formAbonne.nom);
              formdata.append('email', this.formAbonne.email);
              formdata.append('mdp', this.formAbonne.mdp);
              formdata.append('adresse', this.formAbonne.adresse);
              formdata.append('telephone', this.formAbonne.telephone);
              formdata.append('statut', this.formAbonne.statut);
              endpoint = '/add_abonne';
                          
            }
          } else if (this.modalType === 'conso') {
            formdata.append('abonneId', this.formConso.abonneId);
            formdata.append('ancien', this.formConso.ancien);
            formdata.append('nouveau', this.formConso.nouveau);
            formdata.append('mois', this.formConso.mois);
            endpoint = '/add_conso';
          }else if (this.modalType === 'facture') {
            formdata.append('abonneId', this.formFacture.abonneId);
            formdata.append('montant', this.formFacture.montant);
            formdata.append('mois', this.formFacture.mois);
            formdata.append('paye', this.formFacture.paye);
            endpoint = '/add_facture';
          }

          let res = axios.post(endpoint, formdata)
          .then(res => {
              console.log(res.data);
              if (res.data.status === 'success') {
                  $('#info').removeClass('error').addClass('success').hide().fadeIn("slow");
                  setTimeout(() => {
                      location.reload();
                  }, 2000);
              }else{
                  $('#info').removeClass('success').addClass('error').hide().fadeIn("slow");
              }
              $('#info').html(res.data.message);
          })
          .catch(err => {
              console.error(err);
              $('#info').removeClass('success').addClass('error').hide().fadeIn("slow");
              $('#info').html("Une erreur est survenue lors de l'enregistrement.");
          });
          // this.modalOpen = false;
        },
        voirAbonne(a) { alert(`Détail : ${a.nom}, ${a.adresse}`); },
        modifierAbonne(a) { this.openModal('abonne', a); },
        supprimerAbonne(id) {
          let formData = new FormData();
          formData.append('id', id);
          if(confirm('Supprimer ?')) {
              axios.post('/delete_abonne', formData)
              .then(res => {
                  console.log(res.data);
                  if (res.data.status === 'success') {
                      alert('Abonné supprimé avec succès');
                      this.abonnes = this.abonnes.filter(a=>a.id!==id);
                  } else {
                      alert('Erreur lors de la suppression : ' + res.data.message);
                  }
              })
              .catch(err => {
                  console.error(err);
                  alert('Une erreur est survenue lors de la suppression.');
              });

          } 
        },
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