<template>
  <div>
    <v-row dense class="d-flex flex-row justify-center mb-2">
      <v-col
        cols="6"
        sm="2"
        class="d-flex flex-row justify-center align-center"
      >
        <v-btn
          v-if="$vuetify.breakpoint.width > 400"
          @click.prevent="dateBefore"
          icon
          ><v-icon large>mdi-chevron-left</v-icon></v-btn
        >
        <h1>{{ year }}</h1>
        <v-btn
          @click.prevent="dateAfter"
          icon
          v-if="$vuetify.breakpoint.width > 400"
          ><v-icon large>mdi-chevron-right</v-icon></v-btn
        >
      </v-col>
    </v-row>
    <bar
      :chartData="chartByStatus({ year })"
      :options="options"
      style="
        position: relative;
        max-height: 25em;
        background-color: rgba(0, 0, 0, 0.05);
        width: 100%;
        min-width: 80vw;
        padding: 1em 1em 0 0.5em;
      "
    />
  </div>
</template>

<script>
import Bar from "@/components/chars/Bar";
import { mapGetters } from "vuex";
import dayjs from "@/plugins/moment";

let maxDate = dayjs();
let currentDate = dayjs("2019-01-01 00:00:00");
let years = [];
let selectedYear = null;
while (currentDate < maxDate) {
  let year = currentDate.year();
  if (years.indexOf(year) < 0) {
    years.unshift(year);
    selectedYear = year;
  }
  currentDate = currentDate.add(1, "y");
}
export default {
  name: "AllTicketsStacked",
  components: {
    Bar,
  },
  data: () => ({
    years,
    year: selectedYear,
    barLegends: null,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        position: "bottom",
      },
      scales: {
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
            stacked: true,
          },
        ],
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
            },
            stacked: true,
          },
        ],
      },
      tooltips: {
        enabled: false,
      },
      hover: { mode: null },
      plugins: {
        datalabels: {
          anchor: "center",
          align: "center",
          color: "white",
        },
      },
    },
  }),
  mounted() {},
  computed: {
    ...mapGetters("tickets", ["chartByStatus"]),
  },
  methods: {
    dateBefore: function () {
      let date = dayjs(`${this.year}`, "YYYY").subtract(1, "year");
      this.year = this.year = Math.max(
        this.years[this.years.length - 1],
        date.year()
      );
    },
    dateAfter: function () {
      let date = dayjs(`${this.year}`, "YYYY").add(1, "year");
      this.year = Math.min(date.year(), this.years[0]);
    },
  },
};
</script>
<style></style>
