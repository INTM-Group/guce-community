<template>
  <div class="grid-container">
    <bar
      :chartData="charByType({ year, month })"
      :options="options"
      class="chart-graphic"
    />
    <v-col class="chart-legends">
      <div
        class="d-flex flex-row justify-space-between align-center align-center mb-2"
      >
        <v-btn
          v-if="$vuetify.breakpoint.width > 400"
          @click.prevent="dateBefore"
          icon
          ><v-icon large>mdi-chevron-left</v-icon></v-btn
        >
        <v-select
          :items="years"
          v-model="year"
          item-value="value"
          item-text="label"
          dense
          filled
          hide-details="auto"
        />
        <v-select
          v-if="$vuetify.breakpoint.width > 400"
          :items="months"
          item-value="value"
          item-text="label"
          v-model="month"
          hide-details="auto"
          dense
          filled
        />
        <v-btn
          @click.prevent="dateAfter"
          icon
          v-if="$vuetify.breakpoint.width > 400"
          ><v-icon large>mdi-chevron-right</v-icon></v-btn
        >
      </div>
      <v-list color="white" light elevation="3" v-if="$vuetify.breakpoint.width > 400">
        <v-list-item
          v-for="legend in charByType({ year, month }).datasets"
          :key="legend.id"
          dense
          disabled
        >
          <v-list-item-icon>
            <v-icon :style="`color: var(--ticket-type-${legend.id}-color)`">mdi-rectangle</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title v-text="legend.label"></v-list-item-title>
          </v-list-item-content>
          <v-list-item-icon>
            <samp x-small>{{
              asPercentage(
                (legend.total / charByType({ year, month }).total) * 100
              )
            }}</samp>
          </v-list-item-icon>
        </v-list-item>
      </v-list>
    </v-col>
  </div>
</template>

<script>
import Bar from "@/components/chars/Bar";
import { mapGetters } from "vuex";
import dayjs from "@/plugins/moment";
import asPercentage from "@/utilities/asPercentage";

let maxDate = dayjs();
let currentDate = dayjs("2019-01-01 00:00:00");
let years = [];
let months = [];
while (currentDate < maxDate) {
  let year = currentDate.year();
  if (years.findIndex(y => y.value == year) < 0) {
    years.push({
      label: year,
      value: year,
    });
  }
  if (months.length < 12) {
    months.push({
      label: currentDate.format("MMM"),
      value: Number(currentDate.format("M")),
    });
    currentDate = currentDate.add(1, "M");
    continue;
  }
  currentDate = currentDate.add(1, "y");
}
months.unshift({
  label: "*",
  value: null,
});
years.unshift({
  label: "*",
  value: null,
});

export default {
  name: "CharTicketsByType",
  components: {
    Bar,
  },
  data: () => ({
    years,
    months,
    year: null,
    month: null,
    barLegends: null,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false,
      },
      scales: {
        yAxes: [
          {
            gridLines: {
              get color() {
                return getComputedStyle(
                  document.querySelector("#app")
                ).getPropertyValue("--graph-color");
              },
              get zeroLineColor() {
                return getComputedStyle(
                  document.querySelector("#app")
                ).getPropertyValue("--graph-color");
              },
            },
            ticks: {
              get fontColor() {
                return getComputedStyle(
                  document.querySelector("#app")
                ).getPropertyValue("--graph-color");
              },
              min: 0,
              stepSize: 1,
            },
          },
        ],
        xAxes: [
          {
            gridLines: {
              get color() {
                return getComputedStyle(
                  document.querySelector("#app")
                ).getPropertyValue("--graph-color");
              },
              get zeroLineColor() {
                return getComputedStyle(
                  document.querySelector("#app")
                ).getPropertyValue("--graph-color");
              },
            },
            ticks: {
              get fontColor() {
                return getComputedStyle(
                  document.querySelector("#app")
                ).getPropertyValue("--graph-color");
              },
            },
          },
        ],
      },
      tooltips: {
        enabled: false,
      },
      plugins: {
        datalabels: {
          anchor: "center",
          align: "center",
          color: "white",
        },
      },
    },
  }),
  computed: {
    ...mapGetters({
      charByType: "tickets/charByType",
    }),
  },
  watch: {
    year: function (newVal) {
      if (newVal == null)
      this.$nextTick(() => {
        this.month = null;
      })
    },
  },
  methods: {
    dateBefore: function () {
      let date = dayjs(
        `${this.year}-${this.month || "1"}-01`,
        "YYYY-M-DD"
      ).subtract(1, "month");
      if (this.month) {
        this.month = date.month() + 1;
      }
      this.year = this.year = Math.max(
        this.years[this.years.findIndex(y => y.value == date.year())].value,
        date.year()
      );
    },
    dateAfter: function () {
      let date = dayjs(
        `${this.year}-${this.month || "12"}-01`,
        "YYYY-M-DD"
      ).add(1, "month");
      if (this.month) {
        this.month = date.month() + 1;
      }
      this.year = Math.min(date.year(), this.years[this.years.findIndex(y => y.value == date.year())].value);
    },
    asPercentage,
  },
};
</script>
<style lang="sass" scoped>
.grid-container
  display: grid
  grid-template-areas: "chart legens"
  grid-template-columns: 1fr 0.25fr
  grid-template-rows: auto
  gap: 1em
  justify-items: stretch
  align-items: stretch
  justify-content: stretch
  align-content: stretch
  .chart-graphic
    grid-area: "chart"
    position: relative
    min-height: 9em
    max-height: 18em
    background-color: rgba(0, 0, 0, 0.05)
    min-width: 200px
    width: 100%
    padding: 1em 1em 0 0.5em

  .chart-legends
    grid-area: "legends"

@media only screen and (max-width: 600px)
  .grid-container
    grid-template-columns: 1fr
    grid-template-rows: auto auto
    grid-template-areas: "chart" "legens"

.char-legends ul
  list-style: none
  margin: 0
  padding: 0

.char-legends li > span
  padding: 0 0.5em
  margin: 0 0.5em 0 0
  border: solid 1px rgba(127, 127, 127, 0.75)
</style>
