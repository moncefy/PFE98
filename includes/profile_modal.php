<!-- Profile Modal -->
<div id="profileModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Mon Profil</h3>
            <button id="editProfileBtn" class="text-gray-500 hover:text-gray-700 transition-colors">
                <i class="fas fa-pencil-alt text-lg"></i>
            </button>
        </div>
        
        <form id="profileForm">
            <input type="hidden" name="id" id="profile_id">
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Prénom:</label>
                <span id="display_prenom" class="text-gray-900 text-lg"></span>
                <input type="text" id="profile_prenom" name="prenom" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 hidden" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Nom:</label>
                <span id="display_nom" class="text-gray-900 text-lg"></span>
                <input type="text" id="profile_nom" name="nom" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 hidden" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Email:</label>
                <span id="display_email" class="text-gray-900 text-lg"></span>
                <input type="email" id="profile_email" name="email" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 hidden" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Téléphone:</label>
                <span id="display_telephone" class="text-gray-900 text-lg"></span>
                <input type="text" id="profile_telephone" name="telephone" class="form-input w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 hidden">
            </div>
            <div class="flex justify-end hidden" id="profileFormButtons">
                <button type="button" id="cancelProfileBtn" class="mr-2 px-4 py-2 bg-gray-300 text-gray-800 font-semibold rounded-lg shadow hover:bg-gray-400">Annuler</button>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow hover:bg-blue-600">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    console.log('Profile modal script loaded.');

    // Profile Modal Handling
    const profileModal = document.getElementById('profileModal');
    const openProfileModalBtn = document.getElementById('openProfileModalBtn');
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelProfileBtn = document.getElementById('cancelProfileBtn');
    const profileForm = document.getElementById('profileForm');
    const profileFormButtons = document.getElementById('profileFormButtons');

    // Display elements
    const displayPrenom = document.getElementById('display_prenom');
    const displayNom = document.getElementById('display_nom');
    const displayEmail = document.getElementById('display_email');
    const displayTelephone = document.getElementById('display_telephone');

    // Input elements
    const inputPrenom = document.getElementById('profile_prenom');
    const inputNom = document.getElementById('profile_nom');
    const inputEmail = document.getElementById('profile_email');
    const inputTelephone = document.getElementById('profile_telephone');
    const profileIdInput = document.getElementById('profile_id');

    let isEditing = false; // State variable

    // Function to toggle between display and edit mode
    function toggleEditMode(editing) {
        isEditing = editing;
        // Hide/Show display spans and input fields
        displayPrenom.classList.toggle('hidden', editing);
        displayNom.classList.toggle('hidden', editing);
        displayEmail.classList.toggle('hidden', editing);
        displayTelephone.classList.toggle('hidden', editing);

        inputPrenom.classList.toggle('hidden', !editing);
        inputNom.classList.toggle('hidden', !editing);
        inputEmail.classList.toggle('hidden', !editing);
        inputTelephone.classList.toggle('hidden', !editing);

        // Hide/Show buttons
        editProfileBtn.classList.toggle('hidden', editing);
        profileFormButtons.classList.toggle('hidden', !editing);

        // If entering edit mode, populate inputs with current display values
        if (editing) {
            inputPrenom.value = displayPrenom.textContent;
            inputNom.value = displayNom.textContent;
            inputEmail.value = displayEmail.textContent;
            inputTelephone.value = displayTelephone.textContent === 'N/A' ? '' : displayTelephone.textContent; // Handle N/A
        }
    }

    // Initially set to display mode
    toggleEditMode(false);


    // Ensure the button exists before adding event listener
    if (openProfileModalBtn) {
        // Open modal
        openProfileModalBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default link behavior
            // Assuming profileDropdown exists and is a variable accessible here or handled elsewhere
            const profileDropdown = document.getElementById('profileDropdown');
            if (profileDropdown) {
                 profileDropdown.classList.add('hidden'); // Hide dropdown
            }
            
            fetchClientDetails(); // Fetch and populate data
            profileModal.classList.remove('hidden');
            toggleEditMode(false); // Ensure display mode when opening
        });
    }

    // Edit button click handler
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', () => {
            toggleEditMode(true);
        });
    }

    // Close modal
    if (cancelProfileBtn) {
        cancelProfileBtn.addEventListener('click', () => {
            profileModal.classList.add('hidden');
            // Optionally reset form or revert changes here
            // For now, just close the modal
        });
    }

    // Close modal when clicking outside
    if (profileModal) {
        profileModal.addEventListener('click', (e) => {
            if (e.target === profileModal) {
                profileModal.classList.add('hidden');
            }
        });
    }

    // Function to fetch client details
    async function fetchClientDetails() {
        console.log('Fetching client details...');
        try {
            const response = await fetch('get_client_details.php'); // Path relative to the including page
            const result = await response.json();
            console.log('Fetch client details response:', result);

            if (result.success && result.data) {
                // Populate display spans
                displayPrenom.textContent = result.data.prenom;
                displayNom.textContent = result.data.nom;
                displayEmail.textContent = result.data.email;
                displayTelephone.textContent = result.data.telephone || 'N/A'; // Show N/A if phone is empty
                
                // Store ID (if you add it to the fetch)
                // profileIdInput.value = result.data.id;

            } else {
                console.error('Error fetching client details:', result.message);
                alert('Erreur lors du chargement des informations client.');
                profileModal.classList.add('hidden'); // Hide modal on error
            }
        } catch (error) {
            console.error('Error fetching client details:', error);
            alert('Une erreur est survenue lors du chargement des informations client.');
            profileModal.classList.add('hidden'); // Hide modal on error
        }
    }

    // Handle Profile Form Submission
    if (profileForm) {
        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            console.log('Submitting profile form...');

            const formData = new FormData(profileForm);
            const clientData = Object.fromEntries(formData.entries());
            console.log('Form data to send:', clientData);

            try {
                const response = await fetch('update_client_profile.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(clientData)
                });

                const result = await response.json();
                console.log('Update profile response:', result);

                if (result.success) {
                    alert('Profil mis à jour avec succès!');
                    profileModal.classList.add('hidden');
                    // Reload the page to update the header name and refresh modal data
                    location.reload(); 
                } else {
                    console.error('Error updating profile:', result.message);
                    alert('Erreur lors de la mise à jour du profil: ' + (result.message || 'Erreur inconnue'));
                }
            } catch (error) {
                console.error('Error submitting profile update:', error);
                alert('Une erreur est survenue lors de la mise à jour du profil. Veuillez réessayer.');
            }
        });
    }
</script> 