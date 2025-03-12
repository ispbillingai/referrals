
<!-- Companies Modal Component -->
<div id="companiesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
  <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
    <h3 class="text-lg font-bold mb-4">Referred Companies</h3>
    <div id="companiesList" class="max-h-60 overflow-y-auto"></div>
    <button class="mt-4 w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 transition-colors" onclick="document.getElementById('companiesModal').classList.add('hidden')">
      Close
    </button>
  </div>
</div>
