import './bootstrap';

// Clipboard Manager
import { ClipboardManager } from './managers/clipboardManager';
window.ClipboardManager = ClipboardManager;
window.clipboardManager = new ClipboardManager();

// Alert Manager (se você tiver)
import { AlertManager } from './managers/alertManager';
window.alertManager = new AlertManager();

//Chart.JS
import Chart from 'chart.js/auto';
window.Chart = Chart;