<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - E-Money</title>
    <!-- Tailwind CSS pour un design clair ultra-personnalisable -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Icons Bootstrap & Google Fonts (Plus Jakarta Sans) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes pulse-slow {

            0%,
            100% {
                transform: scale(1) translate(0px, 0px);
            }

            33% {
                transform: scale(1.08) translate(20px, -30px);
            }

            66% {
                transform: scale(0.96) translate(-15px, 15px);
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 12s infinite ease-in-out;
        }

        .animate-pulse-slow-reverse {
            animation: pulse-slow 16s infinite ease-in-out reverse;
        }
    </style>
</head>

<body class="h-full flex items-center justify-center overflow-hidden font-sans antialiased bg-slate-50/50">

    <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none">
        <div
            class="absolute top-1/4 left-1/4 w-[35rem] h-[35rem] bg-emerald-100/60 rounded-full blur-[100px] animate-pulse-slow">
        </div>
        <div
            class="absolute bottom-1/4 right-1/4 w-[30rem] h-[30rem] bg-indigo-100/50 rounded-full blur-[120px] animate-pulse-slow-reverse">
        </div>
    </div>

    <div class="relative z-10 w-full max-w-md p-4 sm:p-6">
        <div
            class="bg-white/80 backdrop-blur-xl border border-white/80 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden transition-all duration-300">

            <!-- En-tête de l'espace client -->
            <div class="px-6 pt-10 pb-6 text-center">
                <div
                    class="inline-flex items-center justify-center h-14 w-14 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-600 shadow-sm mb-4">
                    <i class="bi bi-wallet2 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">E-MONEY CLIENT</h2>
                <p class="text-sm text-slate-500 mt-1.5">Gérez votre solde et vos transferts instantanément</p>
            </div>

            <!-- Corps du formulaire -->
            <div class="px-8 pb-10">

                <!-- Gestion des Messages d'Erreur (CI4 Standard & Session Fallbacks) -->
                <?php if (isset($session) && $session->getFlashdata('error')): ?>
                    <div
                        class="mb-5 p-4 bg-rose-50 border border-rose-100 text-rose-600 text-sm rounded-2xl flex items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                        <div><?= $session->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <?php if (function_exists('session') && session()->getFlashdata('error')): ?>
                    <div
                        class="mb-5 p-4 bg-rose-50 border border-rose-100 text-rose-600 text-sm rounded-2xl flex items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill text-lg"></i>
                        <div><?= session()->getFlashdata('error') ?></div>
                    </div>
                <?php endif; ?>

                <!-- Formulaire de Connexion -->
                <form action="<?= base_url('login') ?>" method="post" class="space-y-6">
                    <?= csrf_field() ?>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label for="telephone"
                                class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Numéro de
                                téléphone</label>
                            <!-- Badge dynamique d'identification de l'opérateur (Orange, Airtel, Telma) -->
                            <span id="operatorBadge"
                                class="hidden text-[10px] font-extrabold px-2.5 py-0.5 rounded-full uppercase tracking-wider border transition-all duration-300"></span>
                        </div>

                        <div
                            class="relative flex rounded-2xl shadow-sm bg-slate-100/50 border border-slate-200/60 focus-within:bg-white focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10 transition-all duration-200">
                            <span
                                class="inline-flex items-center px-4 rounded-l-2xl border-r border-slate-200 text-slate-500 text-sm font-bold bg-slate-50">
                                +261
                            </span>
                            <input type="text" id="telephone" name="telephone"
                                class="block w-full pl-4 pr-12 py-3.5 bg-transparent border-0 text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-0 text-sm font-semibold tracking-wider"
                                placeholder="033 12 345 67" required autofocus>
                            <!-- Icône d'état de saisie -->
                            <div
                                class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                <i id="statusIcon" class="bi bi-phone"></i>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-500 mt-2.5 leading-relaxed">
                            <i class="bi bi-info-circle me-1 text-slate-400"></i>
                            Saisissez votre numéro de téléphone. Si le compte n'existe pas encore, il sera créé
                            automatiquement avec un solde de 0 Ar.
                        </p>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="w-full py-3.5 px-4 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-extrabold rounded-2xl transition duration-300 shadow-lg shadow-emerald-600/10 focus:outline-none focus:ring-4 focus:ring-emerald-500/20">
                            Se connecter à l'espace
                        </button>

                        <div class="mt-4 text-center">
                            <a href="<?= base_url('login-operateur') ?>"
                                class="text-sm font-semibold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
                                Se connecter en tant qu'opérateur
                            </a>
                        </div>
                    </div>
                </form>

            </div>

        </div>

        <!-- Pied de page discret -->
        <p class="text-center text-xs text-slate-400 mt-6 font-medium">
            &copy; 2026 E-Money Inc. Tous droits réservés.
        </p>
    </div>



</body>

</html>