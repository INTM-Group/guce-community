<template>
  <v-snackbar
    :timeout="3000"
    app
    top
    centered
    :color="snackbar.color"
    v-model="snackbar.show"
  >
    {{ snackbar.text }}
    <template v-slot:action="{ attrs }">
      <v-btn icon v-bind="attrs" @click="snackbar.show = false">
        <v-icon>mdi-close</v-icon>
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script>
import { mapState } from "vuex";

export default {
  name: "AppStackBar",
  data: () => ({}),
  computed: {
    ...mapState("app", { snackbar: (state) => state.snackbar }),
  },
};
</script>
