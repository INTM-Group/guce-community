<template>
  <v-text-field
    v-model="vColor"
    hide-details
    prepend-icon="mdi-palette"
    class="ma-0 pa-0"
    readonly
    dense
    outlined
  >
    <template v-slot:prepend>
      <v-icon>mdi-palette</v-icon>
    </template>
    <template v-slot:append>
      <v-menu v-model="menu" top nudge-bottom="105" nudge-left="16">
        <template v-slot:activator="{ on }">
          <div :style="swatchStyle" v-on="on" class="mt-n1" />
        </template>
        <v-card>
          <v-card-text class="pa-0">
            <v-color-picker
              v-model="vColor"
              flat
              hide-canvas
              hide-inputs
              hide-mode-switch
              hide-sliders
              mode="rgba"
              show-swatches
              @update:color="updateColor"
            />
          </v-card-text>
        </v-card>
      </v-menu>
    </template>
  </v-text-field>
</template>

<script>

export default {
  name:"ColorPickerInput",
  props: {
    value: {
      required: false,
      type: String,
      default: "#1976D2",
    },
  },
  watch: {
    value: {
      immediate: true,
      deep: true,
      handler(value) {
        if (value) {
          this.vColor = value;
        }
          this.bgColor = (value || this.vColor) + "88";
      },
    },
  },
  data() {
    return {
      vColor: "#1976D2",
      bgColor: "#1976D288",
      menu: false,
    };
  },
  computed: {
    swatchStyle() {
      const { vColor, bgColor, menu } = this;
      return {
        backgroundColor: bgColor,
        cursor: "pointer",
        height: "30px",
        width: "30px",
        borderRadius: menu ? "50%" : "4px",
        borderColor: vColor,
        borderWidth: "medium",
        borderStyle: "solid",
        transition: "border-radius 200ms ease-in-out",
      };
    },
  },
  methods: {
    updateColor(data) {
      this.bgColor = `${data.hex}88`;
      this.$emit('input', data.hex);
    },
  },
};
</script>

<style>
</style>
