<template>
  <v-app>
    <app-drawer ref="drawer" />
    <app-bar ref="drawer" />
    <v-main>
      <router-view />
    </v-main>
    <intm-footer />
    <app-stack-bar />
    <component v-bind:is="displayModal" />
  </v-app>
</template>

<script>
import { mapState } from "vuex";
import AppDrawer from "@/components/AppDrawer";
import AppBar from "@/components/AppBar";
import IntmFooter from "@/components/IntmFooter";
import AppStackBar from "@/components/AppStackBar";

export default {
  name: "LayoutDefault",
  components: {
    AppDrawer,
    AppBar,
    IntmFooter,
    AppStackBar,
    "add-user-dialog": () => import("@/components/users/Modal"),
    "add-ticket-dialog": () => import("@/components/tickets/Modal"),
    "service-dialog": () => import("@/components/services/Modal"),
  },
  computed: {
    ...mapState("app", {
      displayModal: (state) => state.modalComponent,
    }),
  },
};
</script>
