<!-- Footer -->
<footer class="bg-teal-600 text-white py-12">
    <div class="container mx-auto px-4">
        <!-- Footer Content -->
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Column 1 - About -->
            <div>
                <h3 class="text-xl font-bold mb-4">Volunteer Connect</h3>
                <p class="text-gray-400 mb-4">
                    Connecting passionate volunteers with organizations making a difference in our community.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-white hover:text-white transition duration-300">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-white hover:text-white transition duration-300">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.instagram.com/mr__ashish__2208/?next=%2Fmr__ashish__2208%2F" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://www.linkedin.com/feed/?trk=guest_homepage-basic_google-one-tap-submit" class="text-gray-400 hover:text-white transition duration-300">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            
            <!-- Column 2 - Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="index.php" class="text-gray-400 hover:text-white transition duration-300">Home</a>
                    </li>
                    <li>
                        <a href="opportunities.php" class="text-gray-400 hover:text-white transition duration-300">Find Opportunities</a>
                    </li>
                    <li>
                        <a href="organization-details.php" class="text-gray-400 hover:text-white transition duration-300">Organizations</a>
                    </li>
                    <li>
                        <a href="about.php" class="text-gray-400 hover:text-white transition duration-300">About Us</a>
                    </li>
                    <li>
                        <a href="contact.php" class="text-gray-400 hover:text-white transition duration-300">Contact</a>
                    </li>
                </ul>
            </div>
            
            <!-- Column 3 - Categories -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Volunteer Categories</h3>
                <ul class="space-y-2">
                    <?php
                    $footerCategories = getCategories();
                    $displayCount = min(count($footerCategories), 5);
                    for ($i = 0; $i < $displayCount; $i++) {
                        echo '<li><a href="opportunities.php?category=' . $footerCategories[$i]['id'] . '" class="text-gray-400 hover:text-white transition duration-300">' . htmlspecialchars($footerCategories[$i]['name']) . '</a></li>';
                    }
                    ?>
                </ul>
            </div>
            
            <!-- Column 4 - Contact Info -->
            <div>
                <h3 class="text-lg font-semibold mb-4">Contact Us</h3>
                <ul class="space-y-2 text-gray-400">
                    <li class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                        <span>Jalandhar-Delhi G.T. Road, Phagwara, Punjab (India) - 144411. </span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-3"></i>
                        <span>6266928927,8708755231</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-3"></i>
                        <a href="mailto:info@volunteerconnect.org" class="hover:text-white transition duration-300">lpuvolunteer.org</a>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-clock mt-1 mr-3"></i>
                        <span>Mon-Fri: 9:00 AM - 5:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Bottom Footer -->
        <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
            <p class="text-gray-400 text-sm mb-4 md:mb-0">
                &copy; <?= date('Y') ?> Volunteer Connect. All rights reserved.
            </p>
            <div class="flex space-x-6">
                <a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300">Privacy Policy</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300">Terms of Service</a>
                <a href="#" class="text-gray-400 hover:text-white text-sm transition duration-300">Accessibility</a>
            </div>
        </div>
    </div>
</footer>
