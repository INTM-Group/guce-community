<template>
  <v-app>
    <v-main>
      <router-view :key="$route.path"></router-view>
    </v-main>
    <intm-footer />
    <app-stack-bar />
  </v-app>
</template>

<script>
import IntmFooter from "@/components/IntmFooter";
import AppStackBar from "@/components/AppStackBar";

export default {
  name: "LayoutAuth",
  components: {
    IntmFooter,
    AppStackBar,
  },
};
</script>
