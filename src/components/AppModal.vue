<template>
  <v-dialog
    v-model="dialogActive"
    light
    scrollable
    :fullscreen="$vuetify.breakpoint.xs"
    :hide-overlay="$vuetify.breakpoint.xs"
    :max-width="$vuetify.breakpoint.mdAndUp ? '768px' : '75vw'"
  >
    <v-card v-bind="{ color: $attrs.color }">
      <v-toolbar dark flat dense max-height="3em" color="secondary">
        <v-toolbar-title>
          <slot name="title"> [TITLE] </slot>
        </v-toolbar-title>
        <v-spacer />
        <slot name="head-info"></slot>
        <v-tooltip bottom>
          <template v-slot:activator="{ on, attrs }">
            <v-icon @click="dialogActive = false" v-bind="attrs" v-on="on"
              >mdi-close</v-icon
            >
          </template>
          {{ $vuetify.lang.t("$vuetify.dialog.close") }}
        </v-tooltip>
      </v-toolbar>
      <v-card-text style="height: 100%" class="pr-n3">
        <slot />
        </v-card-text>
      <v-card-actions>
        <slot name="foot-text"></slot>
        <v-spacer></v-spacer>
        <v-btn
          class="font-weight-black"
          color="primary"
          @click="$emit('primary-action')"
          elevation="5"
          :disabled="!valid"
        >
          <slot name="primary-action-text"
            ><v-icon left>mdi-content-save</v-icon>
            {{ $vuetify.lang.t("$vuetify.dialog.save") }}</slot
          >
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  props: {
    valid: {
      type: Boolean,
      default: false,
    },
  },
  components: {},
  data: () => ({}),
  computed: {
    ...mapState("app", {
      dialogActiveStorage: (state) => state.dialogActive,
    }),
    dialogActive: {
      get() {
        return this.dialogActiveStorage;
      },
      set(value) {
        if (!value) this.closeDialog(value);
        this.$emit("close");
      },
    },
  },
  methods: {
    ...mapMutations("app", ["closeDialog"]),
  },
};
</script>
