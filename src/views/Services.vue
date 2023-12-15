<template>
  <v-container
    min-width="400"
    class="d-flex flex-column justify-space-around align-stretch"
    fluid
  >
    <v-data-table
      dense
      fixed-header
      class="elevation-2"
      ref="table"
      item-key="name"
      sort-by="name"
      :loading-text="$vuetify.lang.t('$vuetify.services.loading')"
      :headers="tableHeaders"
      :loading="loading"
      :items="items"
      :footer-props="{ 'items-per-page-options': [20, 40, 60, 80, 100, -1] }"
      :items-per-page="itemsPerPage"
      :item-class="setClassCliclable"
      :header-props="{ sortIcon: 'mdi-chevron-up' }"
      @click:row="openEditor"
    >
      <template v-slot:footer>
        <div style="position: absolute" class="my-2">
          <v-tooltip top>
            <template v-slot:activator="{ on: tooltip }">
              <v-btn
                class="mx-2"
                icon
                v-on="{ ...tooltip }"
                @click="addService"
              >
                <v-icon color="grey"> mdi-plus-circle</v-icon>
              </v-btn>
            </template>
            <span>{{ $vuetify.lang.t("$vuetify.dialog.create") }}</span>
          </v-tooltip>
        </div>
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import { mapMutations, mapState } from "vuex";
import { mapCacheActions } from "vuex-cache";
import setClassCliclable from "@/utilities/setClassCliclable";

export default {
  components: {},
  data() {
    return {
      itemsPerPage: 20,
      tableHeaders: [
        {
          text: this.$vuetify.lang.t("$vuetify.services.name"),
          align: "start",
          sortable: true,
          value: "name",
        },
      ],
    };
  },
  mounted() {
    this.getServices();
  },
  computed: {
    ...mapState("services", {
      loading: (state) => state.loading,
      items: (state) => state.items,
    }),
  },
  methods: {
    ...mapCacheActions("services", { getServices: "all" }),
    ...mapMutations("app", ["serviceDialog"]),
    ...mapMutations("services", ["select"]),
    setClassCliclable,
    openEditor(service) {
      this.select(service);
      this.serviceDialog();
    },
    addService() {
      this.select();
      this.serviceDialog();
    },
  },
};
</script>
