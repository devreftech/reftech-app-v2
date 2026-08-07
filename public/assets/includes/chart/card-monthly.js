// Weekly Overview Line Chart
// --------------------------------------------------------------------

(function () {
    let cardColor, labelColor, headingColor, borderColor, grayColor;

    if (isDarkStyle) {
        cardColor = config.colors_dark.cardColor;
        labelColor = config.colors_dark.textMuted;
        headingColor = config.colors_dark.headingColor;
        borderColor = config.colors_dark.borderColor;
        grayColor = "#3b3e59";
    } else {
        cardColor = config.colors.cardColor;
        labelColor = config.colors.textMuted;
        headingColor = config.colors.headingColor;
        borderColor = config.colors.borderColor;
        grayColor = "#f4f4f6";
    }

    const chartColors = {
        donut: {
            series1: config.colors.warning,
            series2: "#fdb528cc",
            series3: "#fdb52899",
            series4: "#fdb52866",
            series5: config.colors_label.warning,
        },
    };
    // Regita
    const weeklyOverviewChartRegitaEl = document.querySelector(
            "#monthlyOverviewChartRegita"
        ),
        weeklyOverviewChartConfigRegita = {
            chart: {
                type: "line",
                height: 178,
                offsetY: -9,
                offsetX: -16,
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
            },
            series: [
                {
                    name: "Daily Call",
                    type: "column",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Visit",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Quotation",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Done PO",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
            ],
            plotOptions: {
                bar: {
                    borderRadius: 9,
                    columnWidth: "50%",
                    endingShape: "rounded",
                    startingShape: "rounded",
                    colors: {
                        ranges: [
                            {
                                to: 80,
                                from: 70,
                                color: config.colors.primary,
                            },
                        ],
                    },
                },
            },
            markers: {
                size: 3.5,
                strokeWidth: 2,
                fillOpacity: 1,
                strokeOpacity: 1,
                colors: [cardColor],
                strokeColors: config.colors.primary,
            },
            stroke: {
                width: [0, 2],
                colors: [config.colors.primary],
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            colors: [grayColor],
            grid: {
                strokeDashArray: 10,
                borderColor,
                padding: {
                    bottom: -10,
                },
            },
            xaxis: {
                categories: ["Week I", "Week II", "Week III", "Week IV", "Week V"],
                tickPlacement: "on",
                labels: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                min: 0,
                max: 80,
                show: true,
                tickAmount: 3,
                labels: {
                    formatter: function (val) {
                        return parseInt(val);
                    },
                    style: {
                        fontSize: "0.75rem",
                        fontFamily: "Inter",
                        colors: labelColor,
                    },
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "none",
                    },
                },
                active: {
                    filter: {
                        type: "none",
                    },
                },
            },
        };
    if (
        typeof weeklyOverviewChartRegitaEl !== undefined &&
        weeklyOverviewChartRegitaEl !== null
    ) {
        const weeklyOverviewChartRegita = new ApexCharts(
            weeklyOverviewChartRegitaEl,
            weeklyOverviewChartConfigRegita
        );
        weeklyOverviewChartRegita.render();
    }
    // Yolan
    const weeklyOverviewChartYolanEl = document.querySelector(
            "#monthlyOverviewChartYolan"
        ),
        weeklyOverviewChartConfigYolan = {
            chart: {
                type: "line",
                height: 178,
                offsetY: -9,
                offsetX: -16,
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
            },
            series: [
                {
                    name: "Daily Call",
                    type: "column",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Visit",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Quotation",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Done PO",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
            ],
            plotOptions: {
                bar: {
                    borderRadius: 9,
                    columnWidth: "50%",
                    endingShape: "rounded",
                    startingShape: "rounded",
                    colors: {
                        ranges: [
                            {
                                to: 80,
                                from: 70,
                                color: config.colors.primary,
                            },
                        ],
                    },
                },
            },
            markers: {
                size: 3.5,
                strokeWidth: 2,
                fillOpacity: 1,
                strokeOpacity: 1,
                colors: [cardColor],
                strokeColors: config.colors.primary,
            },
            stroke: {
                width: [0, 2],
                colors: [config.colors.primary],
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            colors: [grayColor],
            grid: {
                strokeDashArray: 10,
                borderColor,
                padding: {
                    bottom: -10,
                },
            },
            xaxis: {
                categories: ["Week I", "Week II", "Week III", "Week IV", "Week V"],
                tickPlacement: "on",
                labels: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                min: 0,
                max: 80,
                show: true,
                tickAmount: 3,
                labels: {
                    formatter: function (val) {
                        return parseInt(val);
                    },
                    style: {
                        fontSize: "0.75rem",
                        fontFamily: "Inter",
                        colors: labelColor,
                    },
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "none",
                    },
                },
                active: {
                    filter: {
                        type: "none",
                    },
                },
            },
        };
    if (
        typeof weeklyOverviewChartYolanEl !== undefined &&
        weeklyOverviewChartYolanEl !== null
    ) {
        const weeklyOverviewChartYolan = new ApexCharts(
            weeklyOverviewChartYolanEl,
            weeklyOverviewChartConfigYolan
        );
        weeklyOverviewChartYolan.render();
    }
    // Ari
    const weeklyOverviewChartAriEl = document.querySelector(
            "#monthlyOverviewChartAri"
        ),
        weeklyOverviewChartConfigAri = {
            chart: {
                type: "line",
                height: 178,
                offsetY: -9,
                offsetX: -16,
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
            },
            series: [
                {
                    name: "Daily Call",
                    type: "column",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Visit",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Quotation",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Done PO",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
            ],
            plotOptions: {
                bar: {
                    borderRadius: 9,
                    columnWidth: "50%",
                    endingShape: "rounded",
                    startingShape: "rounded",
                    colors: {
                        ranges: [
                            {
                                to: 80,
                                from: 70,
                                color: config.colors.primary,
                            },
                        ],
                    },
                },
            },
            markers: {
                size: 3.5,
                strokeWidth: 2,
                fillOpacity: 1,
                strokeOpacity: 1,
                colors: [cardColor],
                strokeColors: config.colors.primary,
            },
            stroke: {
                width: [0, 2],
                colors: [config.colors.primary],
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            colors: [grayColor],
            grid: {
                strokeDashArray: 10,
                borderColor,
                padding: {
                    bottom: -10,
                },
            },
            xaxis: {
                categories: ["Week I", "Week II", "Week III", "Week IV", "Week V"],
                tickPlacement: "on",
                labels: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                min: 0,
                max: 80,
                show: true,
                tickAmount: 3,
                labels: {
                    formatter: function (val) {
                        return parseInt(val);
                    },
                    style: {
                        fontSize: "0.75rem",
                        fontFamily: "Inter",
                        colors: labelColor,
                    },
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "none",
                    },
                },
                active: {
                    filter: {
                        type: "none",
                    },
                },
            },
        };
    if (
        typeof weeklyOverviewChartAriEl !== undefined &&
        weeklyOverviewChartAriEl !== null
    ) {
        const weeklyOverviewChartAri = new ApexCharts(
            weeklyOverviewChartAriEl,
            weeklyOverviewChartConfigAri
        );
        weeklyOverviewChartAri.render();
    }
    // Yusuf
    const weeklyOverviewChartYusufEl = document.querySelector(
            "#monthlyOverviewChartYusuf"
        ),
        weeklyOverviewChartConfigYusuf = {
            chart: {
                type: "line",
                height: 178,
                offsetY: -9,
                offsetX: -16,
                parentHeightOffset: 0,
                toolbar: {
                    show: false,
                },
            },
            series: [
                {
                    name: "Daily Call",
                    type: "column",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Visit",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Quotation",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
                {
                    name: "Done PO",
                    type: "line",
                    data: [50, 51, 45, 50, 72],
                },
            ],
            plotOptions: {
                bar: {
                    borderRadius: 9,
                    columnWidth: "50%",
                    endingShape: "rounded",
                    startingShape: "rounded",
                    colors: {
                        ranges: [
                            {
                                to: 80,
                                from: 70,
                                color: config.colors.primary,
                            },
                        ],
                    },
                },
            },
            markers: {
                size: 3.5,
                strokeWidth: 2,
                fillOpacity: 1,
                strokeOpacity: 1,
                colors: [cardColor],
                strokeColors: config.colors.primary,
            },
            stroke: {
                width: [0, 2],
                colors: [config.colors.primary],
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            colors: [grayColor],
            grid: {
                strokeDashArray: 10,
                borderColor,
                padding: {
                    bottom: -10,
                },
            },
            xaxis: {
                categories: ["Week I", "Week II", "Week III", "Week IV", "Week V"],
                tickPlacement: "on",
                labels: {
                    show: false,
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                min: 0,
                max: 80,
                show: true,
                tickAmount: 3,
                labels: {
                    formatter: function (val) {
                        return parseInt(val);
                    },
                    style: {
                        fontSize: "0.75rem",
                        fontFamily: "Inter",
                        colors: labelColor,
                    },
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "none",
                    },
                },
                active: {
                    filter: {
                        type: "none",
                    },
                },
            },
        };
    if (
        typeof weeklyOverviewChartYusufEl !== undefined &&
        weeklyOverviewChartYusufEl !== null
    ) {
        const weeklyOverviewChartYusuf = new ApexCharts(
            weeklyOverviewChartYusufEl,
            weeklyOverviewChartConfigYusuf
        );
        weeklyOverviewChartYusuf.render();
    }
})();
