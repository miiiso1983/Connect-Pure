import './bootstrap';
import './theme';


// Bundle Chart.js and expose globally for inline Blade scripts
import Chart from 'chart.js/auto';
if (typeof window !== 'undefined') {
  window.Chart = Chart;
}
