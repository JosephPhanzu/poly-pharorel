

<?php

  use App\Permission;

  $permission = new Permission();
//   if (!$session->isConnected() ) {
//     $session->logout();
//     header('Location: /login');
//   }

  $nom = $session->getNom();
  // $prenom = $session->getPrenom();

  $role = $session->getRole();


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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?= $title ?? 'MediFlow - Connexion | Espace hospitalier sécurisé' ?></title>
    <!-- Tailwind CSS CDN + Font Awesome + Google Fonts Inter -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="<?= $script ?? '' ?>"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        /* Transition pour sidebar et overlay mobile */
        .sidebar-transition { transition: transform 0.3s ease-in-out; }
        .overlay { transition: opacity 0.3s ease; }
        /* Custom scroll pour tableau */
        .custom-scroll::-webkit-scrollbar { height: 6px; width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1E3A8A',
                        secondary: '#F97316',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 antialiased">

    <!-- ======================== MOBILE SIDEBAR OVERLAY ======================== -->
    <div id="mobileSidebarOverlay" class="fixed inset-0 bg-black/50 z-30 opacity-0 invisible transition-all duration-300 lg:hidden"></div>

    <!-- ======================== SIDEBAR (FIXE GAUCHE) ======================== -->
    <aside id="sidebar" class="fixed top-0 left-0 h-full w-72 bg-white shadow-xl z-40 transform -translate-x-full lg:translate-x-0 sidebar-transition flex flex-col border-r border-gray-100">
        <!-- logo + nom hopital -->
        <div class="px-6 py-7 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-blue-700 flex items-center justify-center shadow-md">
                <i class="fas fa-hospital-user text-white text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-primary">PolyPharbo</h1>
                <p class="text-xs text-gray-500">Centre Hospitalier</p>
            </div>
        </div>
        
        <!-- navigation menu -->
        <nav class="flex-1 px-4 py-6 space-y-1.5">
            <a href="/dashboard" class="<?php currentPageActive("dashboard", $active, $currentPage) ?> flex items-center gap-3 px-4 py-3 rounded-xl">
                <i class="fas fa-tachometer-alt w-5"></i> <span>Tableau de bord</span>
            </a>
            <a href="/patients" class="<?php currentPageActive("patients", $active, $currentPage) ?> flex items-center gap-3 px-4 py-3 rounded-xl ">
                <i class="fas fa-users w-5"></i> <span>Patients</span>
            </a>
            <a href="/medecins" class="<?php currentPageActive("medecins", $active, $currentPage) ?> flex items-center gap-3 px-4 py-3 rounded-xl ">
                <i class="fas fa-user-md w-5"></i> <span>Médecins</span>
            </a>
            <a href="/rendez-vous" class="<?php currentPageActive("rendez-vous", $active, $currentPage) ?> flex items-center gap-3 px-4 py-3 rounded-xl ">
                <i class="fas fa-calendar-check w-5"></i> <span>Rendez-vous</span>
            </a>
            <a href="/hospitalisations" class="<?php currentPageActive("hospitalisations", $active, $currentPage) ?> flex items-center gap-3 px-4 py-3 rounded-xl ">
                <i class="fas fa-procedures w-5"></i> <span>Hospitalisations</span>
            </a>
            <a href="/facturation" class="<?php currentPageActive("facturation", $active, $currentPage) ?> flex items-center gap-3 px-4 py-3 rounded-xl ">
                <i class="fas fa-file-invoice-dollar w-5"></i> <span>Facturation</span>
            </a>
            <a href="/parametres" class="<?php currentPageActive("parametres", $active, $currentPage) ?> flex items-center gap-3 px-4 py-3 rounded-xl ">
                <i class="fas fa-cog w-5"></i> <span>Paramètres</span>
            </a>
        </nav>
        
        <div class="p-6 border-t border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-secondary to-orange-500 flex items-center justify-center text-white font-bold">DR</div>
                <div>
                    <p class="text-sm font-semibold">Dr. Rivera</p>
                    <p class="text-xs text-gray-500">Directeur médical</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- ======================== MAIN CONTENT (avec header) ======================== -->
    <div class="lg:ml-72 min-h-screen flex flex-col">
        <!-- Top Navbar -->
        <header class="bg-white shadow-sm sticky top-0 z-20 border-b border-gray-100">
            <div class="px-4 sm:px-6 py-3 flex items-center justify-between">
                <!-- Menu burger mobile -->
                <button id="mobileMenuBtn" class="lg:hidden text-gray-600 text-2xl focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
                <!-- Barre recherche -->
                <div class="hidden md:flex items-center bg-gray-100 rounded-full px-4 py-2 w-80">
                    <i class="fas fa-search text-gray-400"></i>
                    <input type="text" id="globalSearchInput" placeholder="Rechercher patient..." class="bg-transparent ml-3 outline-none text-sm w-full">
                </div>
                <div class="flex items-center gap-4">
                    <!-- Notif -->
                    <button class="relative text-gray-500 hover:text-secondary transition">
                        <i class="far fa-bell text-xl"></i>
                        <span class="absolute -top-1 -right-2 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">3</span>
                    </button>
                    <!-- user mobile friendly -->
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-secondary to-orange-500 flex items-center justify-center text-white text-sm font-bold">DR</div>
                        <span class="hidden sm:inline-block text-sm font-medium text-gray-700">Dr. Rivera</span>
                    </div>
                </div>
            </div>
            <!-- barre recherche mobile -->
            <div class="px-4 pb-3 md:hidden">
                <div class="flex items-center bg-gray-100 rounded-full px-4 py-2">
                    <i class="fas fa-search text-gray-400"></i>
                    <input type="text" id="mobileSearchInput" placeholder="Rechercher patient..." class="bg-transparent ml-3 outline-none text-sm w-full">
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="p-4 sm:p-6 space-y-6">
            <script src="https://cdn.jsdelivr.net/npm/axios@1.9.0/dist/axios.min.js"></script>
            <?= $content ?? '' ?>
        </main>
    </div>

    <script>

        $(document).on('click', '#deconnexion', function(e){
            window.location.replace('/deconnexion');
        })


        // const salaryInput = document.getElementById('salary');
        // const cotisationInput = document.getElementById('cotisation');
        // const sidebar = document.getElementById('sidebar');
        // const overlay = document.getElementById('overlay');
        // const menuBtn = document.getElementById('menuBtn');

        // salaryInput.addEventListener('input', () => {
        //     const salary = parseFloat(salaryInput.value) || 0;
        //     cotisationInput.value = (salary * 0.15).toFixed(2);
        // });

        // menuBtn.addEventListener('click', () => {
        //     sidebar.classList.toggle('-translate-x-full');
        //     overlay.classList.toggle('hidden');
        // });

        // overlay.addEventListener('click', () => {
        //     sidebar.classList.add('-translate-x-full');
        //     overlay.classList.add('hidden');
        // });
        </script>

    
</body>
</html>