import './bootstrap';
//Global Function
import { copyToClipboard } from './globalFunctions'
window.copyToClipboard = copyToClipboard
//Chart.JS
import Chart from 'chart.js/auto';
window.Chart = Chart;