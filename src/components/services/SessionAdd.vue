<template>
  <v-menu
    v-model="menu"
    :close-on-content-click="false"
    :nudge-width="200"
    bottom
    left
  >
    <template v-slot:activator="{ on, attrs }">
      <v-btn icon v-bind="attrs" v-on="on">
        <v-icon>mdi-plus</v-icon>
      </v-btn>
    </template>

    <v-card>
      <v-card-text>
        <v-text-field
          hide-details="auto"
          prepend-icon="mdi-door-open"
          v-model="sessionNew[0]"
          :label="$vuetify.lang.t('$vuetify.services.session.open') + ' *'"
          type="time"
          :step="60 * 30"
        />
        <v-text-field
          hide-details="auto"
          prepend-icon="mdi-door-closed"
          v-model="sessionNew[1]"
          :label="$vuetify.lang.t('$vuetify.services.session.close') + ' *'"
          type="time"
          :min="sessionNew[1]"
          :step="60 * 30"
        />
      </v-card-text>
      <v-card-actions>
        <v-btn text @click="menu = false">
          {{ $vuetify.lang.t("$vuetify.dialog.cancel") }}
        </v-btn>
        <v-spacer />
        <v-btn color="primary" text @click="save()">
          {{ $vuetify.lang.t("$vuetify.dialog.save") }}</v-btn
        >
      </v-card-actions>
    </v-card>
  </v-menu>
</template>

<script>
export default {
  data: () => ({
    sessionNew: [],
    menu: false,
  }),
  methods: {
    save() {
      this.menu = false;
      this.$emit("save-times", [...this.sessionNew]);
    },
  },
};
</script>

<style>
</style>
