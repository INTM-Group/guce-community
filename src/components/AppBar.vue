<template>
  <v-app-bar absolute app color="transparent" flat>
    <v-toolbar-title>
      <h3 class="blue--text text--darken-4 font-weight-medium">
        {{ $vuetify.lang.t(`$vuetify.${$route.name}`, viewSubtitre) }}
      </h3>
    </v-toolbar-title>
    <v-spacer />
    <v-menu v-if="hasAdd" offset-y left>
      <template v-slot:activator="{ attrs, on: menu }">
        <v-tooltip bottom>
          <template v-slot:activator="{ on: tooltip }">
            <v-btn
              class="ml-2"
              min-width="0"
              icon
              v-bind="attrs"
              v-on="{ ...menu, ...tooltip }"
            >
              <v-icon color="blue darken-4">mdi-plus</v-icon>
            </v-btn>
          </template>
          <span>{{ $vuetify.lang.t("$vuetify.bar.tooltips.create") }}</span>
        </v-tooltip>
      </template>
      <v-list :tile="false" nav light dense>
        <v-list-item @click="addTicket({ ticket: null, dialog: true })">
          <v-list-item-icon class="mr-1">
            <v-icon>mdi-ticket-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-title>
            {{ $vuetify.lang.t("$vuetify.new.m") }}
            {{ $vuetify.lang.t("$vuetify.tickets.singular") }}
          </v-list-item-title>
        </v-list-item>
        <v-list-item @click="addUser()">
          <v-list-item-icon class="mr-1">
            <v-icon>mdi-account-outline</v-icon>
          </v-list-item-icon>
          <v-list-item-title>
            {{ $vuetify.lang.t("$vuetify.new.f") }}
            {{ $vuetify.lang.t("$vuetify.users.singular") }}
          </v-list-item-title>
        </v-list-item>
      </v-list>
    </v-menu>
    <v-tooltip bottom v-if="hasRefresh">
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon @click="refresh" v-bind="attrs" v-on="on">
          <v-icon color="blue darken-4">mdi-cached</v-icon>
        </v-btn>
      </template>
      <span>{{ $vuetify.lang.t("$vuetify.bar.tooltips.refresh") }}</span>
    </v-tooltip>
    <v-tooltip bottom>
      <template v-slot:activator="{ on, attrs }">
        <v-btn icon @click="logout" v-bind="attrs" v-on="on">
          <v-icon color="blue darken-4">mdi-logout</v-icon>
        </v-btn>
      </template>
      <span>{{ $vuetify.lang.t("$vuetify.bar.tooltips.logout") }}</span><br>
      <span v-if="user">{{  user.email  }}</span>
    </v-tooltip>
  </v-app-bar>
</template>

<script>
import { mapActions, mapMutations, mapState } from "vuex";
import dayjs from "@/plugins/moment";

export default {
  name: "DashboardCoreAppBar",
  data: () => ({
    date: dayjs(),
    maxDate: dayjs(),
    minDate: dayjs("2019-01-01 00:00:00"),
    dateModal: false,
  }),
  computed: {
    ...mapState("app", ["viewSubtitre"]),
    ...mapState("auth", ["user"]),
    simpleDate: {
      get: function () {
        return this.date.format("YYYY-MM-DD");
      },
      set: function (value) {
        this.date = dayjs(value, "YYYY-MM-DD");
        this.setDateCalculation(this.date);
      },
    },
    hasAdd() {
      return !!this.$route.meta.add;
    },
    hasRefresh() {
      return !!this.$route.meta.refresh;
    },
  },
  methods: {
    ...mapActions({
      logout: "auth/logout",
      setDateCalculation: "setDateCalculation",
    }),
    ...mapMutations("app", {
      addUser: "userDialog",
      addTicket: "ticketDialog",
    }),
    refresh() {
      this.$store.cache.clear();
      this.$nextTick(() => {
        this.$store.cache.dispatch("roles/all");
        this.$store.cache.dispatch("users/all");
        this.$store.cache.dispatch("activities/getAllTicketsSatisfaction");
        this.$store.cache.dispatch("tickets/all").then(() => {
          this.date = dayjs();
        });
      });
    },
  },
};
</script>
<style>
.input-text-centered input {
  text-align: right !important;
}
</style>
