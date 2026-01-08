// resources/js/Hooks/useChart.js
import { useEffect, useRef } from 'react';
import Chart from 'chart.js/auto';

export const useChart = (config) => {
    const chartRef = useRef(null);
    const chartInstance = useRef(null);

    useEffect(() => {
        if (chartRef.current) {
            // Destruir gráfico anterior se existir
            if (chartInstance.current) {
                chartInstance.current.destroy();
            }

            // Criar novo gráfico
            chartInstance.current = new Chart(chartRef.current, config);
        }

        // Cleanup
        return () => {
            if (chartInstance.current) {
                chartInstance.current.destroy();
            }
        };
    }, [config]);

    return chartRef;
};