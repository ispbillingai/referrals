
<?php
// templates/footer.php
?>
  </main>

  <script>
    const weeklyMainTab = document.getElementById('weeklyMainTab');
    const monthlyMainTab = document.getElementById('monthlyMainTab');
    const weeklySection  = document.getElementById('weeklySection');
    const monthlySection = document.getElementById('monthlySection');

    weeklyMainTab.addEventListener('click', () => {
      weeklySection.classList.remove('hidden');
      monthlySection.classList.add('hidden');
      weeklyMainTab.classList.add('bg-indigo-600','text-white');
      monthlyMainTab.classList.remove('bg-indigo-600','text-white');
      monthlyMainTab.classList.add('bg-gray-200','text-gray-700');
    });

    monthlyMainTab.addEventListener('click', () => {
      monthlySection.classList.remove('hidden');
      weeklySection.classList.add('hidden');
      monthlyMainTab.classList.add('bg-indigo-600','text-white');
      weeklyMainTab.classList.remove('bg-indigo-600','text-white');
      weeklyMainTab.classList.add('bg-gray-200','text-gray-700');
    });

    const weekTabs = document.querySelectorAll('.weekly-tab');
    weekTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.weekly-content').forEach(div => {
          div.classList.add('hidden');
        });
        const offset = tab.getAttribute('data-week');
        document.getElementById('week' + offset).classList.remove('hidden');

        weekTabs.forEach(t => t.classList.remove('bg-indigo-600','text-white'));
        weekTabs.forEach(t => t.classList.add('bg-gray-200','text-gray-700'));
        tab.classList.remove('bg-gray-200','text-gray-700');
        tab.classList.add('bg-indigo-600','text-white');
      });
    });

    const monthTabs = document.querySelectorAll('.monthly-tab');
    monthTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.monthly-content').forEach(div => {
          div.classList.add('hidden');
        });
        const offset = tab.getAttribute('data-month');
        document.getElementById('month' + offset).classList.remove('hidden');

        monthTabs.forEach(t => t.classList.remove('bg-indigo-600','text-white'));
        monthTabs.forEach(t => t.classList.add('bg-gray-200','text-gray-700'));
        tab.classList.remove('bg-gray-200','text-gray-700');
        tab.classList.add('bg-indigo-600','text-white');
      });
    });
  </script>
</body>
</html>
