<template>
  <app-modal
    :valid="valid"
    v-on:primary-action="primaryAction"
    v-on:close="
      edition = false;
      select();
    "
    :color="selectedTicket.id ? 'grey lighten-4' : 'white'"
  >
    <template v-slot:title>
      {{
        $vuetify.lang.t(
          selectedTicket.id ? "$vuetify.tickets.singular" : "$vuetify.new.m"
        )
      }}
      {{
        selectedTicket.id
          ? "#" + selectedTicket.id
          : $vuetify.lang.t("$vuetify.tickets.singular")
      }}
    </template>
    <template v-slot:head-info v-if="selectedTicket.id">
      <v-chip
        light
        :color="`status_${selectedTicket.status}`"
        class="elevation-3 font-weight-black mr-3 white--text"
      >
        {{
          $vuetify.lang.t("$vuetify.tickets.status._" + selectedTicket.status)
        }}
      </v-chip>
    </template>
    <component
      v-bind:is="modalComponent"
      v-on:validate="validate"
      :edition="edition"
      class="py-5"
    />
    <template v-slot:foot-text>
      <small class="grey--text" v-if="edition || !selectedTicket.id">
        {{ $vuetify.lang.t("$vuetify.fields") }}
      </small>
    </template>
    <template v-slot:primary-action-text>
      {{
        $vuetify.lang.t(
          `$vuetify.dialog.${selectedTicket.id ? "view" : "create"}`,
          [$vuetify.lang.t("$vuetify.tickets.singular")]
        )
      }}
    </template>
  </app-modal>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import AppModal from "@/components/AppModal";
import TicketCreation from "@/components/tickets/Create";

export default {
  components: {
    AppModal,
    TicketCreation,
  },
  data: () => ({
    valid: false,
    edition: false,
  }),
  computed: {
    ...mapState("app", {
      dialogActive: (state) => state.dialogActive,
    }),
    ...mapState("tickets", {
      selectedTicket: (state) => state.current,
    }),
    ...mapGetters("auth", ["hasPermission"]),
    modalComponent() {
      return "ticket-creation";
    },
  },
  methods: {
    ...mapMutations("app", ["closeDialog"]),
    ...mapMutations("tickets", ["select", "reset"]),
    ...mapActions("tickets", ["add", "put"]),
    validate(isValid) {
      let permission = this.selectedTicket.id
        ? "tickets.show"
        : "tickets.store";
      this.valid = isValid && this.hasPermission(permission);
    },
    primaryAction() {
      if (this.selectedTicket.id) {
        this.$router.push({
          name: "ticketView",
          params: { id: this.selectedTicket.id },
        });
        this.closeDialog();
      } else this.save();
    },
    save() {
      let action = this.selectedTicket.id ? "put" : "add";
      this[action](this.selectedTicket, true).then(() => {
        this.edition = false;
        this.select();
      });
      this.closeDialog();
    },
  },
};
</script>
