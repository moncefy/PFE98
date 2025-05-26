<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechargez Votre Compte</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Optional: Add custom styles if needed */
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center"
    style="background: linear-gradient(to bottom, #e0f7fa,rgb(192, 194, 194));">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">RECHARGEZ VOTRE COMPTE</h2>

        <form>
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">VEUILLEZ CHOISIR VOTRE CARTE</label>
                <div class="flex gap-4" id="paymentMethods">
                    <img src="../images/Dahabiya.png" alt="Carte Edahabia" class="h-12 cursor-pointer opacity-50"
                        data-method="dahabiya">
                    <img src="../images/Paypal.png" alt="Paypal" class="h-12 cursor-pointer opacity-50"
                        data-method="paypal">
                    <img src="../images/btc.png" alt="Bitcoin" class="h-12 cursor-pointer opacity-50" data-method="btc">
                </div>
            </div>

            <!-- Dahabiya Card Details (Hidden by default) -->
            <div id="dahabiyaCardDetails" class="mb-6 hidden">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Détails de la carte</h3>
                <div class="space-y-4">
                    <div>
                        <label for="cardNumber" class="block text-gray-700 font-medium mb-1">Numéro de la carte</label>
                        <input type="text" id="cardNumber"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="XXXX XXXX XXXX XXXX">
                    </div>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="expiryDate" class="block text-gray-700 font-medium mb-1">MM/YY</label>
                            <input type="text" id="expiryDate"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="MM/YY">
                        </div>
                        <div>
                            <label for="cvv" class="block text-gray-700 font-medium mb-1">CVV</label>
                            <input type="text" id="cvv"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="XXX">
                        </div>
                        <div class="col-span-3">
                            <label for="nameHolder" class="block text-gray-700 font-medium mb-1">Nom du
                                titulaire</label>
                            <input type="text" id="nameHolder"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Nom complet">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PayPal Details (Hidden by default) -->
            <div id="paypalDetails" class="mb-6 hidden">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Détails PayPal</h3>
                <div class="space-y-4">
                    <div>
                        <label for="paypalEmail" class="block text-gray-700 font-medium mb-1">Email PayPal</label>
                        <input type="email" id="paypalEmail"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="votre.email@exemple.com">
                    </div>
                    <div>
                        <label for="paypalPassword" class="block text-gray-700 font-medium mb-1">Mot de passe
                            PayPal</label>
                        <input type="password" id="paypalPassword"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Mot de passe">
                    </div>
                </div>
            </div>

            <!-- Crypto Details (Hidden by default) -->
            <div id="cryptoDetails" class="mb-6 hidden">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Adresse Crypto</h3>
                <div class="space-y-4">
                    <div>
                        <label for="cryptoAddress" class="block text-gray-700 font-medium mb-1">Adresse de
                            portefeuille</label>
                        <input type="text" id="cryptoAddress"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Entrez l'adresse de votre portefeuille crypto">
                    </div>
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <!-- ReCAPTCHA Placeholder -->
                <div class="g-recaptcha" data-sitekey="6LfnUEcrAAAAAP8dQM1Bd-P4uTF5EIK1SzXN6Pv0"></div>
                <p class="text-gray-500 text-sm ml-2">ReCAPTCHA will go here.</p>
            </div>

            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" class="form-checkbox text-blue-600 rounded">
                    <span class="ml-2 text-gray-700 text-sm">J'accepte les <a href="#"
                            class="text-blue-600 hover:underline">conditions d'utilisation</a></span>
                </label>
            </div>

            <div class="flex justify-end gap-4">
                <button type="button"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-800 font-semibold hover:bg-gray-100">Annuler</button>
                <button type="submit" onclick="handlePayment(event)"
                    class="px-6 py-2 bg-red-600 text-white font-semibold rounded-lg shadow hover:bg-red-700">Continuer</button>
            </div>
        </form>
    </div>

    <!-- Add Google reCAPTCHA API script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        const paymentMethodsDiv = document.getElementById('paymentMethods');
        const paymentImages = paymentMethodsDiv.querySelectorAll('img');
        const dahabiyaCardDetailsDiv = document.getElementById('dahabiyaCardDetails');
        const paypalDetailsDiv = document.getElementById('paypalDetails');
        const cryptoDetailsDiv = document.getElementById('cryptoDetails');

        paymentImages.forEach(img => {
            img.addEventListener('click', () => {
                // Remove selected state from all images
                paymentImages.forEach(i => {
                    i.classList.add('opacity-50');
                    i.classList.remove('border-2', 'border-blue-600', 'rounded-md', 'p-1');
                });

                // Add selected state to the clicked image
                img.classList.remove('opacity-50');
                img.classList.add('border-2', 'border-blue-600', 'rounded-md', 'p-1');

                // Hide all details divs first
                dahabiyaCardDetailsDiv.classList.add('hidden');
                paypalDetailsDiv.classList.add('hidden');
                cryptoDetailsDiv.classList.add('hidden');

                // Show specific details based on selection
                const selectedMethod = img.getAttribute('data-method');
                if (selectedMethod === 'dahabiya') {
                    dahabiyaCardDetailsDiv.classList.remove('hidden');
                } else if (selectedMethod === 'paypal') {
                    paypalDetailsDiv.classList.remove('hidden');
                } else if (selectedMethod === 'btc') {
                    cryptoDetailsDiv.classList.remove('hidden');
                }

                console.log('Selected payment method:', selectedMethod);
            });
        });

        // Optional: Set Dahabiya as selected by default
        const defaultSelectedImage = document.querySelector('[data-method="dahabiya"]');
        if (defaultSelectedImage) {
            defaultSelectedImage.classList.remove('opacity-50');
            defaultSelectedImage.classList.add('border-2', 'border-blue-600', 'rounded-md', 'p-1');
            dahabiyaCardDetailsDiv.classList.remove('hidden'); // Show details for default selection
        }

        function handlePayment(event) {
            event.preventDefault();
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-500 ease-in-out z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>Votre paiement a été effectué avec succès!</span>
                </div>
            `;
            
            // Add notification to body
            document.body.appendChild(notification);
            
            // Animate notification
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);
            
            // Redirect after 2 seconds
            setTimeout(() => {
                window.location.href = 'welcome.php?payment=success';
            }, 1000);
        }
    </script>
</body>

</html>