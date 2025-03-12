
<?php
/**
 * Generates monthly tab buttons
 * 
 * @param int $numberOfMonths Number of months to display
 * @return string HTML for the tab buttons
 */
function generateMonthlyTabs($numberOfMonths = 6) {
    ob_start();
    ?>
    <div class="flex space-x-3 mb-6 overflow-x-auto pb-2">
      <?php 
        for ($i = 0; $i < $numberOfMonths; $i++):
          $monthLabel = ($i === 0) ? "Current Month" : "{$i} Month(s) Ago";
          
          // Calculate date range for this month
          $currentDate = new DateTime();
          if ($i > 0) {
            $currentDate->modify("-{$i} month");
          }
          $monthStart = clone $currentDate;
          $monthStart->modify('first day of this month');
          $monthEnd = clone $currentDate;
          $monthEnd->modify('last day of this month');
          
          $dateRangeLabel = $monthStart->format('M d') . ' - ' . $monthEnd->format('M d, Y');
      ?>
      <button 
        class="monthly-tab whitespace-nowrap px-4 py-2 rounded-lg shadow-md transition-all duration-300 <?php echo ($i === 0) ? 'bg-indigo-600 text-white shadow-indigo-200' : 'bg-white text-gray-700 hover:bg-gray-50'; ?>"
        data-month="<?php echo $i; ?>"
      >
        <div class="flex flex-col items-center">
          <span><?php echo $monthLabel; ?></span>
          <span class="text-xs mt-1 font-normal"><?php echo $dateRangeLabel; ?></span>
        </div>
      </button>
      <?php endfor; ?>
    </div>
    <?php
    return ob_get_clean();
}
?>
