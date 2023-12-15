<template>
  <v-container
    min-width="400"
    class="d-flex flex-column justify-space-around align-stretch"
    fluid
  >
    <v-row class="mt-5">
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="deep-purple"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.created')"
          :value="items.length"
          can-click
          @icon-click="iconClick('created')"
        />
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="green accent-4"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.open')"
          :value="getTicketbyStatus(TICKET_STATUS_OPEN)"
          can-click
          @icon-click="iconClick('open')"
        />
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="blue lighten-1"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.progress_ct')"
          :value="getTicketbyStatus(TICKET_STATUS_COURS_CT)"
          can-click
          @icon-click="iconClick('progress_ct')"
        />
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="orange lighten-1"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.waiting_ct')"
          :value="getTicketbyStatus(TICKET_STATUS_ATTENTE_CT)"
          can-click
          @icon-click="iconClick('waiting_ct')"
        />
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="blue darken-1"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.progress_cr')"
          :value="getTicketbyStatus(TICKET_STATUS_COURS_CR)"
          can-click
          @icon-click="iconClick('progress_cr')"
        />
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="orange darken-1"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.waiting_cr')"
          :value="getTicketbyStatus(TICKET_STATUS_ATTENTE_CR)"
          can-click
          @icon-click="iconClick('waiting_cr')"
        />
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="grey lighten-1"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.resolved')"
          :value="getTicketbyStatus(TICKET_STATUS_RESOLVED)"
          can-click
          @icon-click="iconClick('resolved')"
        />
      </v-col>
      <v-col cols="12" sm="6" lg="3">
        <material-stats-card
          color="grey darken-2"
          icon="mdi-ticket-outline"
          :title="$vuetify.lang.t('$vuetify.dashboard.cards.closed')"
          :value="getTicketbyStatus(TICKET_STATUS_CLOSED)"
          can-click
          @icon-click="iconClick('closed')"
        />
      </v-col>
      <!--v-col v-if="satisfactionRate >= 0" cols="12" sm="6" lg="3">
        <material-card
          class="v-card--material-chart"
          :icon-size="92"
          rm-icon-padding
          :color="satisfaction.color"
          :icon="`mdi-${satisfaction.icon}`"
        >
          <template v-slot:after-heading>
            <div class="ml-auto text-right">
              <h2 class="blue--text text--darken-4 font-weight-light">
                {{ $vuetify.lang.t("$vuetify.dashboard.satisfaction", "") }}
              </h2>
              <h3
                class="display-2 font-weight-medium blue--text text--darken-4"
              >
                {{ satisfaction.text }}
              </h3>
            </div>
          </template>
          <v-col cols="12" class="px-0">
            <v-divider class="blue darken-4" />
          </v-col>
        </material-card>
      </v-col-->
    </v-row>
    <v-row>
      <v-col>
        <material-card
          class="v-card--material-chart"
          color="blue lighten-3"
          v-bind="$attrs"
          v-on="$listeners"
        >
          <template v-slot:heading>
            <all-tickets-stacked />
          </template>
          <h2
            class="
              card-title
              font-weight-medium
              mt-2
              ml-2
              blue--text
              text--darken-4
            "
          >
            {{ $vuetify.lang.t("$vuetify.dashboard.cards.allTickets") }}
          </h2>
          <v-divider class="mt-1 mb-3 blue darken-4" />
        </material-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <material-card
          class="v-card--material-chart"
          color="orange lighten-2"
          v-bind="$attrs"
          v-on="$listeners"
        >
          <template v-slot:heading>
            <char-tickets-by-type />
          </template>
          <h2
            class="
              card-title
              font-weight-medium
              mt-2
              ml-2
              blue--text
              text--darken-4
            "
          >
            {{ $vuetify.lang.t("$vuetify.dashboard.cards.distribution") }}
          </h2>
          <v-divider class="mt-1 mb-3 blue darken-4" />
        </material-card>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapGetters } from "vuex";
import { mapCacheActions } from "vuex-cache";
import MaterialStatsCard from "@/components/base/MaterialStatsCard";
import MaterialCard from "@/components/base/MaterialCard";
import CharTicketsByType from "@/components/chars/tickets/byType";
import AllTicketsStacked from "@/components/chars/tickets/allTicketsStacked";
import Ticket from "@/models/Ticket";

export default {
  components: {
    MaterialStatsCard,
    CharTicketsByType,
    MaterialCard,
    AllTicketsStacked,
  },
  data: () => ({
    TICKET_STATUS_OPEN: Ticket.STATUS_OPEN,
    TICKET_STATUS_COURS_CT: Ticket.STATUS_COURS_CT,
    TICKET_STATUS_COURS_CR: Ticket.STATUS_COURS_CR,
    TICKET_STATUS_ATTENTE_CT: Ticket.STATUS_ATTENTE_CT,
    TICKET_STATUS_ATTENTE_CR: Ticket.STATUS_ATTENTE_CR,
    TICKET_STATUS_RESOLVED: Ticket.STATUS_RESOLVED,
    TICKET_STATUS_CLOSED: Ticket.STATUS_CLOSED,
    satisfactions: [
      { icon: "emoticon-sad-outline", color: "error", text: "mauvaise" },
      { icon: "emoticon-neutral-outline", color: "warning", text: "neutre" },
      {
        icon: "emoticon-happy-outline",
        color: "green darken-3",
        text: "bonne",
      },
    ],
  }),
  created() {
    this.getAllTicketsSatisfaction();
    this.getTickets();
  },
  computed: {
    ...mapState("activities", ["satisfactionRate"]),
    ...mapState("tickets", {
      loading: (state) => state.loading,
      items: (state) => state.items,
    }),
    ...mapGetters("tickets", { getTicketbyStatus: "getByStatus" }),
    satisfaction() {
      return (
        this.satisfactions[this.satisfactionRate] || {
          color: "gray",
          icon: "none",
          text: "-",
        }
      );
    },
  },
  methods: {
    ...mapCacheActions("tickets", { getTickets: "all" }),
    ...mapCacheActions("activities", ["getAllTicketsSatisfaction"]),
    iconClick(target) {
      this.$router.push({
        name: "ticketFiltered",
        params: { filtered: target },
      });
    },
  },
};
</script>
