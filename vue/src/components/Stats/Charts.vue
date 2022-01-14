<template>
  <div>
    <canvas v-bind:id="id" ref="key"></canvas>
  </div>
</template>
<script>
import Chart from "chart.js";
import "chartjs-plugin-labels";

export default {
  name: "Chart",
  props: [
    "id",
    "chartType",
    "chartData",
    "chartLabel",
    "chartOptions",
    "chartColors",
  ],
  data() {
    return {
      type: this.chartType,
      data:
        this.chartType != "doughnut"
          ? JSON.parse(this.chartData)
          : this.chartData,
      dataSet: [],
      labels: this.chartLabel,
      options:
        this.chartOptions != undefined
          ? {
              title: this.chartOptions.title ?? "",
              xTitle: this.chartOptions.xTitle ?? "",
              yTitle: this.chartOptions.yTitle ?? "",
            }
          : "",
      colors: this.chartColors ?? "",
    };
  },
  methods: {
    chartConstructor() {
      if (this.type == "doughnut") {
        var arrayHelperLabels = this.labels.filter(
          (_, index) => this.data[index] != 0,
        );
        this.labels = arrayHelperLabels;
        this.dataSet[0].data = this.data.filter(
          (_, index) => this.data[index] != 0,
        );
      }
      const chartElement = this.$refs.key;
      new Chart(chartElement, {
        type: this.type,
        data: {
          labels: this.labels,
          datasets: this.dataSet,
        },
        options: {
          title: {
            display: true,
            text: this.options.title ?? "",
          },
          elements: {
            center:
              this.chartType == "doughnut"
                ? {
                    color: "##000000", // Default is #000000
                    fontStyle: "Arial", // Default is Arial
                    sidePadding: 20, // Default is 20 (as a percentage)
                    minFontSize: 15, // Default is 20 (in px), set to false and text will not wrap.
                    lineHeight: 25, // Default is 25 (in px), used for when text wraps
                  }
                : "",
          },
          scales:
            this.chartType == "doughnut"
              ? ""
              : {
                  xAxes: [
                    {
                      scaleLabel: {
                        display: true,
                        labelString: this.options.xTitle,
                      },
                      stacked: true,
                      gridLines: { display: false },
                    },
                  ],
                  yAxes: [
                    {
                      scaleLabel: {
                        display: true,
                        labelString: this.options.yTitle,
                      },
                      display: true,
                      ticks: {
                        callback: function (value) {
                          return value;
                        },
                        suggestedMin: 0, // minimum will be 0, unless there is a lower value.
                        // OR //
                        beginAtZero: true, // minimum value will be 0.
                      },
                    },
                  ],
                },

          responsive: true,

          maintainAspectRatio: false,
          legend: {
            labels: {
              boxWidth: 10,
            },
            position: "top",
          },
          animation: {
            duration: 2000,
            easing: "easeInOutQuart",
          },
          plugins: {
            labels: {
              render: "percentage",
              fontColor: ["green", "green", "green", "green", "green", "green"],
              precision: 2,
            },
          },
        },
      });
    },
    dataSetPrep() {
      var i = 0;
      switch (this.chartType) {
        case "doughnut": {
          let bgColors = [];
          this.colors.forEach((key) => {
            bgColors.push(this.colors != "" ? this.hexToRgb(key) : "");
          });
          this.dataSet.push({
            data: this.data ? this.data : "",
            backgroundColor: bgColors,
          });
          break;
        }
        default:
          Object.keys(this.data).forEach((key) => {
            this.dataSet.push({
              label: key,
              data: this.data[key] ? this.data[key] : "",
              backgroundColor:
                this.colors != "" ? this.hexToRgb(this.colors[i]) : "",
              borderColor: this.colors[i] ? this.colors[i] : "",
              lineTension: 0,
              pointBackgroundColor: this.colors[i] ? this.colors[i] : "",
            });
            i++;
          });
      }
    },
    hexToRgb(hex) {
      hex = hex[0] == "#" ? hex.substring(1) : hex;
      var bigint = parseInt(hex, 16);
      var r = (bigint >> 16) & 255;
      var g = (bigint >> 8) & 255;
      var b = bigint & 255;
      return "rgba(" + r + "," + g + "," + b + ",0.4)";
    },
    displayTextInCenter() {
      Chart.pluginService.register({
        beforeDraw: function (chart) {
          if (chart.config.options.elements.center) {
            // Get ctx from string
            var ctx = chart.chart.ctx;

            // Get options from the center object in options
            var centerConfig = chart.config.options.elements.center;
            var fontStyle = centerConfig.fontStyle || "Arial";
            var txt = "    Total:     " + chart.getDatasetMeta(0).total;
            var color = centerConfig.color || "#000";
            var maxFontSize = centerConfig.maxFontSize || 75;
            var sidePadding = centerConfig.sidePadding || 20;
            var sidePaddingCalculated =
              (sidePadding / 100) * (chart.innerRadius * 2);
            // Start with a base font of 30px
            ctx.font = "30px " + fontStyle;

            // Get the width of the string and also the width of the element minus 10 to give it 5px side padding
            var stringWidth = ctx.measureText(txt).width;
            var elementWidth = chart.innerRadius * 2 - sidePaddingCalculated;

            // Find out how much the font can grow in width.
            var widthRatio = elementWidth / stringWidth;
            var newFontSize = Math.floor(30 * widthRatio);
            var elementHeight = chart.innerRadius * 2;

            // Pick a new font size so it will not be larger than the height of label.
            var fontSizeToUse = Math.min(
              newFontSize,
              elementHeight,
              maxFontSize,
            );
            var minFontSize = centerConfig.minFontSize;
            var lineHeight = centerConfig.lineHeight || 25;
            var wrapText = false;

            if (minFontSize === undefined) {
              minFontSize = 20;
            }

            if (minFontSize && fontSizeToUse < minFontSize) {
              fontSizeToUse = minFontSize;
              wrapText = true;
            }

            // Set font settings to draw it correctly.
            ctx.textAlign = "center";
            ctx.textBaseline = "middle";
            var centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
            var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
            ctx.font = fontSizeToUse + "px " + fontStyle;
            ctx.fillStyle = color;

            if (!wrapText) {
              ctx.fillText(txt, centerX, centerY);
              return;
            }

            var words = txt.split(" ");
            var line = "";
            var lines = [];

            // Break words up into multiple lines if necessary
            for (let n = 0; n < words.length; n++) {
              var testLine = line + words[n] + " ";
              var metrics = ctx.measureText(testLine);
              var testWidth = metrics.width;
              if (testWidth > elementWidth && n > 0) {
                lines.push(line);
                line = words[n] + " ";
              } else {
                line = testLine;
              }
            }

            // Move the center up depending on line height and number of lines
            centerY -= (lines.length / 2) * lineHeight;

            for (var n = 0; n < lines.length; n++) {
              ctx.fillText(lines[n], centerX, centerY);
              centerY += lineHeight;
            }
            //Draw text in center
            ctx.fillText(line, centerX, centerY);
          }
        },
      });
    },
  },
  computed: {},
  mounted() {
    this.displayTextInCenter();
    this.dataSetPrep();
    this.chartConstructor();
  },
};
</script>
~
<style scoped></style>
