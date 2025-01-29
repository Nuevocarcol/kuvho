/**
 * Dashboard
 */

'use strict';

(function () {
  let cardColor, headingColor, labelColor, shadeColor, borderColor, heatMap1, heatMap2, heatMap3, heatMap4;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    borderColor = config.colors_dark.borderColor;
    shadeColor = 'dark';
    heatMap1 = '#4f51c0';
    heatMap2 = '#595cd9';
    heatMap3 = '#8789ff';
    heatMap4 = '#c3c4ff';
  } else {
    cardColor = config.colors.cardColor;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    borderColor = config.colors.borderColor;
    shadeColor = '';
    heatMap1 = '#e1e2ff';
    heatMap2 = '#c3c4ff';
    heatMap3 = '#a5a7ff';
    heatMap4 = '#696cff';
  }

  // Total Users - Area Chart
  // --------------------------------------------------------------------
  const totalUsersEl = document.querySelector('#totalUsersChart');
  const totalUsersConfig = {
    chart: {
      height: 370,
      width: 850,
      type: 'area',
      toolbar: false,
      dropShadow: {
        enabled: true,
        top: 14,
        left: 2,
        blur: 3,
        color: config.colors.primary,
        opacity: 0.15
      }
    },
    series: [
      {
        name: 'Total Users',
        data: monthCount // Use the fetched user count data
      }
    ],
    dataLabels: {
      enabled: false
    },
    stroke: {
      width: 2,
      curve: 'straight' // Use 'smooth' curve for area chart
    },
    colors: [config.colors.primary],
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark', // You can customize the shade color
        shadeIntensity: 0.8,
        opacityFrom: 0.7,
        opacityTo: 0.25,
        stops: [0, 95, 100]
      }
    },
    grid: {
      show: true,
      borderColor: borderColor,
      padding: {
        top: -15,
        bottom: -10,
        left: 0,
        right: 0
      }
    },
    xaxis: {
      categories: months, // Use the fetched months as categories
      labels: {
        offsetX: 0,
        style: {
          colors: labelColor,
          fontSize: '13px'
        }
      },
      axisBorder: {
        show: false
      },
      axisTicks: {
        show: false
      },
      lines: {
        show: false
      }
    },
    yaxis: {
      labels: {
        offsetX: -15,
        formatter: function (val) {
          return parseInt(val);
        },
        style: {
          fontSize: '13px',
          colors: labelColor
        }
      },
      min: 0, // Set the minimum value as needed
      max: 500,
      tickAmount: 5
    }
  };

  // Initialize and render the Total Users chart
  const totalUsersChart = new ApexCharts(totalUsersEl, totalUsersConfig);
  totalUsersChart.render();
})();
