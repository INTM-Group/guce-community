<template>
  <v-container
    min-width="400"
    class="d-flex flex-column justify-space-around align-stretch"
    fluid
  >
    <v-card>
      <v-text-field
        clearable
        hide-details
        outlined
        placeholder="Rechercher"
        prepend-inner-icon="mdi-magnify"
        v-model.lazy="search"
        @click:clear="search = null"
      ></v-text-field>
    </v-card>
    <v-card class="mt-3 px-3" v-if="$vuetify.breakpoint.smAndDown">
      <label>Filter par :</label>
      <v-chip-group
        active-class="primary--text"
        center-active
        v-model="grouppingId"
      >
        <v-chip v-for="gItem in grouppingItems" :key="gItem.value">
          {{ gItem.text }}
        </v-chip>
      </v-chip-group>
    </v-card>
    <small class="grey--text caption mt-1 pa-1 text-center">
      {{ $vuetify.lang.t("$vuetify.softwares.totalCount", [totalSoftware]) }}
    </small>
    <v-data-table
      dense
      fixed-header
      class="elevation-2 mt-3 reverseScrollY"
      ref="table"
      item-key="id"
      sort-by="id"
      :search="search"
      :sort-desc="true"
      :loading-text="$vuetify.lang.t('$vuetify.tickets.loading')"
      :headers="tableHeaders"
      :loading="loading"
      :items="items"
      :group-by="groupping"
      :footer-props="{ 'items-per-page-options': [10, 25, 50, 100, -1] }"
      :items-per-page.sync="itemsPerPage"
      :header-props="{ sortIcon: 'mdi-chevron-up' }"
      :custom-filter="filterOnlyText"
      @update:group-by="setGrouBy"
    >
      <template v-slot:top="{ pagination, options, updateOptions }">
        <div style="position: absolute">
          <v-speed-dial v-model="fab" top>
            <template v-slot:activator>
              <v-tooltip top :disabled="fab">
                <template v-slot:activator="{ on: tooltip }">
                  <v-btn class="mx-2" v-model="fab" icon v-on="{ ...tooltip }">
                    <v-icon v-if="fab" color="grey"> mdi-close </v-icon>
                    <v-icon v-else color="grey"> mdi-download </v-icon>
                  </v-btn>
                </template>
                <span>{{ $vuetify.lang.t("$vuetify.tooltips.download") }}</span>
              </v-tooltip>
            </template>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  fab
                  light
                  small
                  @click="downloadData('CSV')"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon>mdi-file-delimited</v-icon>
                </v-btn>
              </template>
              <span>{{ $vuetify.lang.t("$vuetify.tooltips.cvs") }}</span>
            </v-tooltip>
            <v-tooltip right>
              <template v-slot:activator="{ on, attrs }">
                <v-btn
                  fab
                  light
                  small
                  @click="downloadData('XLSX')"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon>mdi-file-excel</v-icon>
                </v-btn>
              </template>
              <span>{{ $vuetify.lang.t("$vuetify.tooltips.excel") }}</span>
            </v-tooltip>
          </v-speed-dial>
        </div>
        <v-data-footer
          :pagination="pagination"
          :options="options"
          @update:options="updateOptions"
          items-per-page-text="$vuetify.dataTable.itemsPerPageText"
          :items-per-page-options="[10, 25, 50, 100, -1]"
        />
      </template>
      <template
        v-slot:[`group.header`]="{ group, groupBy, headers, toggle, isOpen }"
      >
        <td :colspan="headers.length">
          <v-btn @click="toggle" x-small icon :ref="group">
            <v-icon v-if="isOpen">mdi-chevron-down</v-icon>
            <v-icon v-else>mdi-chevron-right</v-icon>
          </v-btn>
          <span class="mx-5 font-weight-bold">
            {{
              tableHeaders.find((item) => groupBy.indexOf(item.value) >= 0).text
            }}
            :
            {{
              groupBy[0] == "tag_principal"
                ? group
                : $vuetify.lang.t(`$vuetify.tickets.${groupBy[0]}._${group}`)
            }}
            ({{ itemsInGroup[group] }})
          </span>
          <v-btn class="float-right" x-small icon @click="groupping = null">
            <v-icon>mdi-close-thick</v-icon>
          </v-btn>
        </td>
      </template>
      <template v-slot:[`header.criticality`]>
        <button @click="groupping = 'criticality'" style="white-space: nowrap">
          {{ $vuetify.lang.t("$vuetify.tickets.criticality") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template>
      <template v-slot:[`item.criticality`]="{ item }">
        <span
          :class="
            item.criticality != 250
              ? 'grey--text text--darken-3'
              : 'error--text'
          "
          >{{
            $vuetify.lang.t(
              "$vuetify.tickets.criticality._" +
                Math.floor(item.criticality / 120)
            )
          }}</span
        >
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import { mapState, mapMutations } from "vuex";
import { mapCacheActions } from "vuex-cache";
import exportData from "@/utilities/exportData";
import { color } from "@/models/Ticket";
import itemsInGroup from "@/utilities/itemsInGroup";

export default {
  components: {},
  data() {
    return {
      fab: false,
      groupping: null,
      privGrouppingId: null,
      search: null,
      tableHeaders: [
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.software"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "text",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.repository"),
          align: "start",
          value: "repository",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.criticality"),
          align: "center",
          sortable: true,
          cellClass: "text-no-wrap",
          value: "criticality",
          groupping: true,
        },
      ],
    };
  },
  created() {
    this.getTags();
  },
  computed: {
    ...mapState("app", ["userItemsPerPage"]),
    ...mapState("tickets", {
      items: (state) => state.tags,
      totalSoftware: (state) => state.tagsStats,
    }),
    itemsInGroup,
    loading: function () {
      return !(this.items || []).length;
    },
    itemsPerPage: {
      get: function () {
        return this.groupping ? -1 : this.userItemsPerPage;
      },
      set: function (value) {
        if (value > 0) this.setUserItemsPerPage(value);
      },
    },
    grouppingId: {
      get: function () {
        return this.groupping ? this.privGrouppingId : null;
      },
      set: function (value) {
        this.privGrouppingId = value;
        this.groupping = (
          this.grouppingItems[this.privGrouppingId] || { value: null }
        ).value;
      },
    },
    grouppingItems: function () {
      return this.tableHeaders.filter((item) => item.groupping);
    },
  },
  methods: {
    ...mapMutations("app", ["setUserItemsPerPage", "setViewSubtitre"]),
    ...mapCacheActions("tickets", ["getTags"]),
    color,
    downloadData(type) {
      // TODO: Speed optimization _.memoize in traslate
      exportData(
        this.items.map((item) => ({
          ...item,
          toSheet({ lang }) {
            let result = {};
            result[lang("$vuetify.tickets.software")] = this.software.name;
            result[lang("$vuetify.tickets.version")] = this.software.version;
            result[lang("$vuetify.tickets.repository")] = this.repository;
            result[lang("$vuetify.tickets.criticality")] = lang(
              "$vuetify.tickets.criticality._" +
                Math.floor(this.criticality / 120)
            );
            return result;
          },
        })),
        {
          lang: (str) => this.$vuetify.lang.t(str),
          getUserName: (id) => this.getUserName(id),
        },
        type,
        "Softwares"
      );
    },
    setGrouBy(value) {
      if (!value) {
        this.groupping = null;
      } else {
        this.$nextTick(() => {
          let table = this.$refs.table;
          let keys = Object.keys(table.$vnode.componentInstance.openCache);
          keys.forEach((x) => {
            table.$vnode.componentInstance.openCache[x] = false;
          });
        });
      }
    },
    filterOnlyText(value, search) {
      return (
        value != null &&
        search != null &&
        typeof value === "string" &&
        value.toString().indexOf(search) !== -1
      );
    },
  },
};
</script>
