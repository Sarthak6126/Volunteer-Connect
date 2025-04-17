<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
$pageTitle = "About Us - Volunteer Connect";
$currentPage = "about";
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'includes/header.php'; ?>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div class="flex-grow">
        <!-- Hero Section -->
        <section class="bg-teal-600 text-white py-16">
            <div class="container mx-auto px-4">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">About Volunteer Connect</h1>
                <p class="text-xl mb-4">Empowering communities through service and connection.</p>
            </div>
        </section>

        <!-- Our Mission -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800">Our Mission</h2>
                    <p class="text-lg text-gray-700 mb-6">
                        Volunteer Connect exists to bridge the gap between passionate volunteers and organizations making a difference in our communities. We believe that everyone has skills and time that can contribute to positive change.
                    </p>
                    <p class="text-lg text-gray-700 mb-6">
                        Our platform makes it easy for volunteers to find opportunities that match their interests, skills, and availability, while helping organizations connect with the volunteers they need to fulfill their missions.
                    </p>
                    <div class="bg-teal-50 border-l-4 border-teal-500 p-4 mt-8">
                        <p class="italic text-gray-700">
                            "The best way to find yourself is to lose yourself in the service of others." - Mahatma Gandhi
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Our Values -->
        <section class="py-16 bg-gray-50">
            <div class="container mx-auto px-4">
                <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Our Values</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-handshake text-xl text-teal-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Community</h3>
                        <p class="text-gray-600">
                            We believe in the power of community to create meaningful change. By connecting volunteers with organizations, we help build stronger, more resilient communities.
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-users text-xl text-teal-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Inclusivity</h3>
                        <p class="text-gray-600">
                            We're committed to creating an inclusive platform that connects people from all backgrounds with volunteer opportunities where they can make a difference.
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-heart text-xl text-teal-600"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Impact</h3>
                        <p class="text-gray-600">
                            We focus on creating meaningful connections that lead to real impact. Every volunteer hour makes a difference, and we're here to facilitate that positive change.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- How We Started -->
        <section class="py-16 bg-white">
            <div class="container mx-auto px-4">
                <div class="max-w-3xl mx-auto">
                    <h2 class="text-3xl font-bold mb-6 text-gray-800">How We Started</h2>
                    <p class="text-lg text-gray-700 mb-6">
                        Volunteer Connect began as a grassroots initiative to address the challenge many local organizations faced in finding dedicated volunteers. At the same time, community members were expressing interest in volunteering but didn't know where to start.
                    </p>
                    <p class="text-lg text-gray-700 mb-6">
                        Founded in 2023, our platform has grown to connect hundreds of volunteers with dozens of organizations, creating meaningful impact across our community.
                    </p>
                    <p class="text-lg text-gray-700">
                        Today, we continue to expand our reach, bringing more opportunities to volunteers and more help to organizations making a difference.
                    </p>
                </div>
            </div>
        </section>

        <!-- Call to Action -->
        <section class="py-16 bg-teal-600 text-white">
            <div class="container mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold mb-6">Ready to Make a Difference?</h2>
                <p class="text-xl mb-8 max-w-3xl mx-auto">
                    Whether you're looking to volunteer or you're an organization seeking help, Volunteer Connect is here to facilitate meaningful connections.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="opportunities.php" class="bg-white text-teal-600 hover:bg-gray-100 font-semibold px-6 py-3 rounded-lg inline-block transition duration-300">Find Opportunities</a>
                    <a href="contact.php" class="bg-transparent border-2 border-white hover:bg-white hover:text-teal-600 text-white font-semibold px-6 py-3 rounded-lg inline-block transition duration-300">Contact Us</a>
                </div>
            </div>
        </section>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script src="js/main.js"></script>
</body>
</html>
