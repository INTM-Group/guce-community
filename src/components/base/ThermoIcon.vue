<template>
  <v-avatar v-bind="$attrs" :color="color(level)">
    <v-icon>{{ currentIcon }}</v-icon>
  </v-avatar>
</template>

<script>
import { color } from "@/models/Ticket";

export default {
  props: {
    level: {
      type: Number,
      default: 0,
    },
  },
  components: {},
  data: () => ({
    currentIcon: "mdi-thermometer",
  }),
  watch: {
    level() {
      if ((this.level = 120)) this.currentIcon = "mdi-thermometer";
      if (this.level < 120) this.currentIcon = "mdi-thermometer-low";
      this.currentIcon = "mdi-thermometer-high";
    },
  },
  methods: {
    color,
  },
};
</script>

<style>
</style>