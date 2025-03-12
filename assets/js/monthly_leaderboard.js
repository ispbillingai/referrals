
document.addEventListener('DOMContentLoaded', function() {
  // Set up tab switching for monthly section
  const monthlyTabs = document.querySelectorAll('.monthly-tab');
  monthlyTabs.forEach(tab => {
    tab.addEventListener('click', function() {
      // Hide all content sections
      document.querySelectorAll('.monthly-content').forEach(content => {
        content.classList.add('hidden');
      });
      
      // Remove active class from all tabs
      monthlyTabs.forEach(t => {
        t.classList.remove('bg-indigo-600', 'text-white', 'shadow-indigo-200');
        t.classList.add('bg-white', 'text-gray-700');
      });
      
      // Show the selected content
      const monthNum = this.dataset.month;
      const targetContent = document.getElementById('month' + monthNum);
      if (targetContent) {
        targetContent.classList.remove('hidden');
      }
      
      // Add active class to clicked tab
      this.classList.remove('bg-white', 'text-gray-700');
      this.classList.add('bg-indigo-600', 'text-white', 'shadow-indigo-200');
    });
  });

  document.querySelectorAll('.view-companies').forEach(button => {
    button.addEventListener('click', function() {
      const companies = JSON.parse(this.dataset.companies);
      const companiesList = document.getElementById('companiesList');
      companiesList.innerHTML = companies.map(company => {
        company = company.trim();
        // Create a link with .ispledger.com for all companies
        const companyDomain = company.toLowerCase() + '.ispledger.com';
        return `<div class="py-2 border-b last:border-0">
          <a href="http://${companyDomain}" class="text-blue-600 hover:underline" target="_blank">
            ${company}.ispledger.com
          </a>
        </div>`;
      }).join('');
      
      document.getElementById('companiesModal').classList.remove('hidden');
    });
  });
});
