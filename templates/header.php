
<?php
// templates/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Referral Leaderboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 text-gray-900 min-h-screen">

  <!-- Navigation Bar -->
  <nav class="bg-white shadow-md">
    <div class="max-w-6xl mx-auto px-4">
      <div class="flex justify-between h-16">
        <div class="flex">
          <div class="flex-shrink-0 flex items-center">
            <a href="index.php" class="text-xl font-bold text-indigo-600">ISP Referrals</a>
          </div>
          <div class="hidden md:ml-6 md:flex md:space-x-8">
            <a href="index.php" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> text-sm font-medium">
              Leaderboard
            </a>
            <a href="terms.php" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) === 'terms.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> text-sm font-medium">
              Terms & Conditions
            </a>
            <a href="faq.php" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) === 'faq.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> text-sm font-medium">
              FAQ
            </a>
            <a href="complaints.php" class="inline-flex items-center px-1 pt-1 border-b-2 <?php echo basename($_SERVER['PHP_SELF']) === 'complaints.php' ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'; ?> text-sm font-medium">
              Submit Complaint
            </a>
          </div>
        </div>
        <div class="hidden md:ml-6 md:flex md:items-center">
          <button class="bg-indigo-600 px-4 py-2 rounded text-white font-medium hover:bg-indigo-700 transition-colors">
            Contact Us
          </button>
        </div>
        <div class="-mr-2 flex items-center md:hidden">
          <!-- Mobile menu button -->
          <button id="mobile-menu-button" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false">
            <span class="sr-only">Open main menu</span>
            <!-- Icon when menu is closed -->
            <svg id="menu-closed-icon" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <!-- Icon when menu is open -->
            <svg id="menu-open-icon" class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu, show/hide based on menu state -->
    <div class="hidden md:hidden" id="mobile-menu">
      <div class="pt-2 pb-3 space-y-1">
        <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'index.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          Leaderboard
        </a>
        <a href="terms.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'terms.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          Terms & Conditions
        </a>
        <a href="faq.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'faq.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          FAQ
        </a>
        <a href="complaints.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'complaints.php' ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : 'border-transparent text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800'; ?> block pl-3 pr-4 py-2 border-l-4 text-base font-medium">
          Submit Complaint
        </a>
      </div>
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="flex items-center px-4">
          <button class="w-full bg-indigo-600 px-4 py-2 rounded text-white font-medium hover:bg-indigo-700 transition-colors">
            Contact Us
          </button>
        </div>
      </div>
    </div>
  </nav>

  <!-- Hero Header with Enhanced Gradient -->
  <header class="bg-gradient-to-r from-violet-500 via-fuchsia-500 to-pink-500 text-white px-8 py-20 relative overflow-hidden">
    <div class="max-w-6xl mx-auto relative z-10">
      <h1 class="text-5xl font-bold mb-4 tracking-tight">Referral Leaderboard</h1>
      <p class="text-xl font-medium opacity-90">Track your impact and compete with fellow referrers</p>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-br from-gray-50 to-gray-100 rounded-t-[3rem] transform translate-y-1"></div>
  </header>

  <!-- Main Container with Enhanced Styling -->
  <main class="max-w-6xl mx-auto -mt-12 px-4 relative z-20 mb-10">
    
    <!-- Mobile menu toggle script -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuClosedIcon = document.getElementById('menu-closed-icon');
        const menuOpenIcon = document.getElementById('menu-open-icon');
        
        if (mobileMenuButton && mobileMenu) {
          mobileMenuButton.addEventListener('click', function() {
            const expanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            
            mobileMenuButton.setAttribute('aria-expanded', !expanded);
            mobileMenu.classList.toggle('hidden');
            
            if (menuClosedIcon && menuOpenIcon) {
              menuClosedIcon.classList.toggle('hidden');
              menuOpenIcon.classList.toggle('hidden');
            }
          });
        }
      });
    </script>
