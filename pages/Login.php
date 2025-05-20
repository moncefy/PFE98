<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Agence de Voyage</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <style>
        body { background-color:rgb(240, 242, 245); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row max-w-4xl w-full">
        <!-- Left Image Section -->
        <div class="hidden md:block md:w-1/2">
            <img src="../images/welc.png" alt="Travel Image" class="h-full w-full object-cover">
        </div>
        <!-- Right Form Section -->
        <div class="w-full md:w-1/2 p-8 md:p-12">
            <div class="flex justify-center mb-6">
                <img src="../images/LOGO.png" alt="Logo" class="h-16">
            </div>
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-4">Bienvenue</h2>
            <p class="text-center text-gray-600 mb-6">Connectez-vous pour continuer</p>
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                    <?= htmlspecialchars($_SESSION['login_error']) ?>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>
            <form action="../class/loginHandler.php" method="POST" class="space-y-6">
                <div>
                    <label for="email" class="block text-gray-700 mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input id="email" name="email" type="email" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent"
                               placeholder="email@exemple.com">
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-gray-700 mb-1">Mot de passe</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input id="password" name="password" type="password" required
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="••••••••">
                    </div>
                </div>
                <button type="submit" class="w-full py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                    Se connecter
                </button>
            </form>
            <div class="mt-6 text-center space-y-3">
                <a href="forgotPassword.php" class="block text-indigo-600 hover:underline text-sm">Mot de passe oublié ?</a>
                <a href="register.php" class="block w-full py-2 border border-blue-600 text-blue-600 font-semibold rounded-lg hover:bg-blue-50 transition">Créer un compte</a>
            </div>
        </div>
    </div>
</body>
</html>