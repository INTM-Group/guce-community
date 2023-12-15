<template>
  <v-navigation-drawer app permanent expand-on-hover dark color="secondary">
    <v-list dense nav v-if="user">
      <v-list-item class="px-2">
        <v-list-item-title>
          <v-img
            contain
            lazy-src="data:image/png;base64,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"
            :src="logo"
            max-height="40"
            position="center left"
          />
        </v-list-item-title>
      </v-list-item>
      <v-divider></v-divider>
      <template v-for="(item, key) in computeMenu">
        <v-list-item
          v-if="
            !(item.meta.admin && item.meta.admin != user.has_permissions.root)
          "
          color="white"
          link
          :key="key"
          :to="item.path"
        >
          <v-list-item-icon>
            <v-icon color="white">{{ item.meta.icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-content color="white">
            <v-list-item-title class="white--text body-1">
              {{ $vuetify.lang.t(`$vuetify.${item.name}`) }}
            </v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </template>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
import { protectedRoute as routes } from "@/router/config";
import { mapState } from "vuex";
import { storagePublic } from "@/plugins/request";

export default {
  name: "AppDrawer",
  components: {},
  data: ()=>({}),
  computed: {
    ...mapState("app", ["clientName"]),
    ...mapState("auth", ["user"]),
    subdomine() {
      return window.localStorage.subdomine.toUpperCase();
    },
    computeMenu() {
      return routes[0].children.filter((item) => !item.meta.hiddenInMenu);
    },
    logo() {
      return storagePublic + `/intm/${this.clientName.toLowerCase()}/logo.svg`;
    },
  },
  methods: {
    toggleDrawer() {
      this.mini = !this.mini;
    },
  },
};
</script>
