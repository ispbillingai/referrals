
<?php
// templates/complaints.php
// Initialize variables to manage form submission
$success = false;
$error = '';

// Process form submission if it exists
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Basic validation
  if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message'])) {
    $error = 'Please fill in all required fields.';
  } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } else {
    // In a real application, you would save this to a database or send an email
    // For this example, we'll just set a success message
    $success = true;
    
    // You could add database code here to save the complaint
    // Example:
    // $stmt = $pdo->prepare("INSERT INTO complaints (name, email, phone, complaint_type, message, date_submitted) VALUES (?, ?, ?, ?, ?, NOW())");
    // $stmt->execute([$_POST['name'], $_POST['email'], $_POST['phone'], $_POST['complaint_type'], $_POST['message']]);
  }
}
?>

<div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8 my-8">
  <h1 class="text-3xl font-bold text-indigo-700 mb-6">Submit a Complaint or Inquiry</h1>
  
  <?php if ($success): ?>
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
      <p class="font-bold">Success!</p>
      <p>Your complaint has been submitted successfully. Our team will review it and get back to you within 48 hours.</p>
    </div>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
      <p class="font-bold">Error!</p>
      <p><?php echo htmlspecialchars($error); ?></p>
    </div>
  <?php endif; ?>
  
  <form method="POST" action="" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
        <input type="text" id="name" name="name" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Enter your full name">
      </div>
      
      <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
        <input type="email" id="email" name="email" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Enter your email address">
      </div>
      
      <div>
        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
        <input type="tel" id="phone" name="phone"
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
          placeholder="Enter your phone number">
      </div>
      
      <div>
        <label for="complaint_type" class="block text-sm font-medium text-gray-700 mb-1">Complaint Type *</label>
        <select id="complaint_type" name="complaint_type" required
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="" disabled selected>Select complaint type</option>
          <option value="payment">Payment Issues</option>
          <option value="referral">Referral Not Credited</option>
          <option value="account">Account Problems</option>
          <option value="system">System Bugs</option>
          <option value="other">Other</option>
        </select>
      </div>
    </div>
    
    <div>
      <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Your Message *</label>
      <textarea id="message" name="message" rows="6" required
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
        placeholder="Please describe your complaint or inquiry in detail..."></textarea>
    </div>
    
    <div class="flex justify-between items-center">
      <button type="submit"
        class="inline-block px-6 py-3 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200 font-semibold transition-all duration-300 hover:bg-indigo-700">
        Submit Complaint
      </button>
      
      <a href="index.php" class="text-indigo-600 hover:underline">
        Return to Leaderboard
      </a>
    </div>
  </form>
</div>
