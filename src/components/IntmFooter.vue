<template>
  <v-footer app class="justify-space-between">
    <small
      ><span style="display: inline-block; transform: rotate(-180deg)">©</span>
      {{ thisYear }} {{ $vuetify.lang.t("$vuetify.footer.copy") }}.</small
    >
    <small>v{{ version }} (build : {{ build }})</small>
  </v-footer>
</template>

<script>
import dayjs from "@/plugins/moment";

export default {
  name: "IntmFooter",
  data: () => ({
    thisYear: dayjs().format("YYYY"),
    version: document.body.getAttribute("version"),
    build: dayjs.unix(document.body.getAttribute("build")).format('llll'),
  }),
};
</script>
