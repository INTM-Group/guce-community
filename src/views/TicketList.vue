<template>
  <v-container
    min-width="400"
    class="d-flex flex-column justify-space-around align-stretch"
    fluid
  >
    <v-menu
      v-model="menuDp"
      offset-y
      ref="menuDp"
      min-width="auto"
      transition="scale-transition"
      :close-on-content-click="false"
      :return-value.sync="afterDate"
    >
      <template v-slot:activator="{ on, attrs }">
        <v-card>
          <v-text-field
            v-on="on"
            v-bind="attrs"
            clearable
            hide-details
            readonly
            outlined
            label="Après la date"
            prepend-inner-icon="mdi-calendar"
            :value="afterDateFrmated"
            @click:clear="afterDate = null"
          ></v-text-field>
        </v-card>
      </template>
      <v-date-picker v-model="afterDate" no-title scrollable>
        <v-btn text color="primary" @click="menuDp = false"> Annuler </v-btn>
        <v-spacer></v-spacer>
        <v-btn text color="primary" @click="$refs.menuDp.save(afterDate)">
          OK
        </v-btn>
      </v-date-picker>
    </v-menu>
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
    <v-data-table
      dense
      fixed-header
      class="elevation-2 mt-3 reverseScrollY"
      ref="table"
      item-key="id"
      sort-by="id"
      :sort-desc="true"
      :loading-text="$vuetify.lang.t('$vuetify.tickets.loading')"
      :headers="tableHeaders"
      :loading="loading"
      :items="items"
      :group-by="groupping"
      :footer-props="{ 'items-per-page-options': [10, 25, 50, 100, -1] }"
      :items-per-page.sync="itemsPerPage"
      :item-class="setClassCliclable"
      :header-props="{ sortIcon: 'mdi-chevron-up' }"
      @update:group-by="setGrouBy"
      @click:row="quickViewTicket"
    >
      <template v-slot:top="{ pagination, options, updateOptions }">
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
      <template v-slot:footer>
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
      </template>
      <!--template v-slot:[`header.status`]>
        <button @click="groupping = 'status'" style="white-space: nowrap">
          {{ $vuetify.lang.t("$vuetify.tickets.who") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template>
      <template v-slot:[`header.phase`]>
        <button @click="groupping = 'phase'" style="white-space: nowrap">
          {{ $vuetify.lang.t("$vuetify.tickets.phase") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template-->
      <template v-slot:[`header.criticality`]>
        <button @click="groupping = 'criticality'" style="white-space: nowrap">
          {{ $vuetify.lang.t("$vuetify.tickets.criticality") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template>
      <template v-slot:[`header.satisfaction`]>
        <v-tooltip top>
           <template v-slot:activator="{ on, attrs }">
            <v-icon
              v-bind="attrs"
              v-on="on"
              small
              >mdi-exclamation-thick</v-icon
            >
          </template>
          {{ $vuetify.lang.t("$vuetify.tickets.satisfaction") }}
        </v-tooltip>
      </template>
       <template v-slot:[`header.cronoClient`]>
        <v-tooltip top>
           <template v-slot:activator="{ on, attrs }">
           <span
          v-bind="attrs"
          v-on="on"
        >{{ $vuetify.lang.t("$vuetify.tickets.taClient") }}</span>
          </template>
          {{ $vuetify.lang.t("$vuetify.tickets.taClientMessage") }}
        </v-tooltip>
      </template>
      <template v-slot:[`header.priority`]>
        <button @click="groupping = 'priority'" style="white-space: nowrap">
          {{ $vuetify.lang.t("$vuetify.tickets.type") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template>
      <template v-slot:[`header.tag_principal`]>
        <button
          @click="groupping = 'tag_principal'"
          style="white-space: nowrap"
        >
          {{ $vuetify.lang.t("$vuetify.tickets.software") }}
          <v-icon x-small>mdi-filter-outline</v-icon>
        </button>
      </template>
      <template v-slot:[`item.title`]="{ item }">
        {{ item.title }}
        <v-tooltip bottom offset-overflow color="black">
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              class="mx-1"
              style="margin-top: -0.75em"
              x-small
              color="primary"
              v-bind="attrs"
              v-on="on"
              >mdi-information-outline</v-icon
            >
          </template>
          <div>
            <v-container
              style="max-height: 50vh; overflow: hidden"
              v-html="item.description"
            />
          </div>
        </v-tooltip>
      </template>
      <template v-slot:[`item.open_at`]="{ item }">
        {{ item.open_at.format("YYYY-MM-DD LT") }}
        <span
          v-if="item.stats"
          style="white-space: nowrap"
          :class="(item.stats.in_hours ? 'primary' : 'error') + '--text'"
          >{{ item.stats.in_hours ? "HO" : "HNO" }}</span
        >
      </template>
      <template v-slot:[`item.updated_at`]="{ item }">
        {{ item.updated_at ? item.updated_at.format("YYYY-MM-DD LT") : "-" }}
      </template>
      <template v-slot:[`item.cronoClient`]="{ item }">
        {{ item.cronoClient  }}
      </template>
      <!--template v-slot:[`item.stats.crono.supplier`]="{ item }">
        {{ (item.stats.crono.supplier / (60 * 60 * 1000)).toFixed(2) }}
      </template-->
      <template v-slot:[`item.status`]="{ item }">
        <span>{{
          $vuetify.lang.t("$vuetify.tickets.who._" + item.status)
        }}</span>
      </template>
      <template v-slot:[`item.phase`]="{ item }">
        <span
          :class="`status_${item.status}` + '--text'"
          v-if="[70, 130].indexOf(item.priority) < 0"
          >{{ $vuetify.lang.t("$vuetify.tickets.phase._" + item.status) }}</span
        >
        <span
          :class="`status_${item.status}` + '--text'"
          v-if="[10, 70, 130].indexOf(item.priority) > 0"
          >{{
            $vuetify.lang.t("$vuetify.tickets.phase.__" + item.status)
          }}</span
        >
      </template>
      <template v-slot:[`item.satisfaction`]="{ item }">
        <v-tooltip left>
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              v-bind="attrs"
              v-on="on"
              v-if="item.status == 128"
              :color="satisfaction(item.satisfaction[0]).color"
              icon
            >
              <v-icon size="18px"
                >mdi-{{ satisfaction(item.satisfaction[0]).icon }}</v-icon
              ></v-btn
            >
          </template>
          {{ satisfaction(item.satisfaction[0]).text }}
        </v-tooltip>
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
              "$vuetify.tickets.criticality._" + item.criticalityLevel
            )
          }}</span
        >
      </template>
      <template v-slot:[`item.priority`]="{ item }">
        <span
          :class="
            item.priority != 250 ? 'grey--text text--darken-3' : 'error--text'
          "
          >{{
            $vuetify.lang.t("$vuetify.tickets.priority._" + item.priority)
          }}</span
        >
      </template>
      <template v-slot:[`item.tag_principal`]="{ item }">
        <v-chip class="my-1 mx-1" small v-if="item.tag_principal">{{
          item.tag_principal
        }}</v-chip>
        <v-tooltip right max-width="15vw" v-if="item.tags.length">
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              class="mx-0.5"
              style="margin-top: -0.75em"
              x-small
              color="primary"
              v-bind="attrs"
              v-on="on"
              >mdi-information-outline</v-icon
            >
          </template>
          <span
            >{{ $vuetify.lang.t("$vuetify.tickets.softwareComp") }}:
            {{ item.tags.map((i) => i.text || i).join(", ") }}</span
          >
        </v-tooltip>
      </template>
      <template v-slot:[`item.tpc`]="{ item }">
        {{ item.tpc }}
        <v-tooltip left v-if="[10].indexOf(item.priority) < 0">
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              class="mx-0.5"
              style="margin-top: -0.75em"
              x-small
              color="primary"
              v-bind="attrs"
              v-on="on"
              >mdi-information-outline</v-icon
            >
          </template>
          Engagement {{ item.sla.tpc }}h </v-tooltip
        ><br />
      </template>
      <template v-slot:[`item.tct`]="{ item }">
        {{ [70, 130].indexOf(item.priority) >= 0 ? "-" :  item.tct }}
        <v-tooltip left v-if="[10, 70, 130].indexOf(item.priority) < 0">
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              class="mx-0.5"
              style="margin-top: -0.75em"
              x-small
              color="primary"
              v-bind="attrs"
              v-on="on"
              >mdi-information-outline</v-icon
            >
          </template>
          Engagement {{ item.sla.tctDisplay }} </v-tooltip
        ><br />
      </template>
      <template v-slot:[`item.tct.percentage`]="{ item }">
        <span
          style="white-space: nowrap"
          :class="classPercentage(item.tct.percentage)"
          >{{  [70, 130].indexOf(item.priority) >= 0 ? "-" : item.tct.asPercentage }}</span
        >
      </template>
      <template v-slot:[`item.tcr`]="{ item }">
        {{item.tcr }}
        <v-tooltip left v-if="[10].indexOf(item.priority) < 0">
          <template v-slot:activator="{ on, attrs }">
            <v-icon
              class="mx-0.5"
              style="margin-top: -0.75em"
              x-small
              color="primary"
              v-bind="attrs"
              v-on="on"
              >mdi-information-outline</v-icon
            >
          </template>
          Engagement {{ item.sla.tcrDisplay }}</v-tooltip
        ><br />
      </template>
      <template v-slot:[`item.tcr.percentage`]="{ item }">
        <span
          style="white-space: nowrap"
          :class="classPercentage(item.tcr.percentage)"
          >{{
           item.tcr.asPercentage
          }}</span
        >
      </template>
    </v-data-table>
  </v-container>
</template>

<script>
import { mapState, mapMutations, mapGetters } from "vuex";
import { mapCacheActions } from "vuex-cache";
import dayjs from "@/plugins/moment";
import exportData from "@/utilities/exportData";
import displayFromDate from "@/utilities/displayFromDate";
import setClassCliclable from "@/utilities/setClassCliclable";
import itemsInGroup from "@/utilities/itemsInGroup";
import Ticket, { color } from "@/models/Ticket";

const TICKET_STATUS_OPEN = Ticket.STATUS_OPEN;
const TICKET_STATUS_COURS_CT = Ticket.STATUS_COURS_CT;
const TICKET_STATUS_COURS_CR = Ticket.STATUS_COURS_CR;
const TICKET_STATUS_ATTENTE_CT = Ticket.STATUS_ATTENTE_CT;
const TICKET_STATUS_ATTENTE_CR = Ticket.STATUS_ATTENTE_CR;
const TICKET_STATUS_RESOLVED = Ticket.STATUS_RESOLVED;
const TICKET_STATUS_CLOSED = Ticket.STATUS_CLOSED;

export default {
  components: {},
  data() {
    return {
      fab: false,
      currentTime: dayjs,
      groupping: null,
      menuDp: false,
      afterDate: null,
      selectedItem: { id: null },
      satisfactions: [
        { icon: "emoticon-sad-outline", color: "error", text: "Mauvaise" },
        { icon: "emoticon-neutral-outline", color: "warning", text: "Neutre" },
        {
          icon: "emoticon-happy-outline",
          color: "green darken-3",
          text: "Bonne",
        },
      ],
      tableHeaders: [
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.id"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "id",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.created_at"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          filter: (value) => {
            if (!this.afterDate) return true;
            return value > this.afterDate;
          },
          value: "open_at",
          sort: (a, b) => a.unix() - b.unix(),
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.updated_at"),
          align: "start",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "updated_at",
          sort: (a, b) => a.unix() - b.unix(),
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.title"),
          align: "start",
          sortable: true,
          value: "title",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.software"),
          align: "start",
          sortable: true,
          width: "10em",
          cellClass: "text-no-wrap",
          value: "tag_principal",
          groupping: true,
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.criticality"),
          align: "center",
          sortable: true,
          cellClass: "text-no-wrap",
          value: "criticality",
          groupping: true,
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.type"),
          align: "center",
          sortable: true,
          cellClass: "text-no-wrap",
          value: "priority",
          groupping: true,
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.who"),
          align: "center",
          sortable: true,
          value: "status",
          //groupping: true,
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.phase"),
          align: "center",
          sortable: true,
          value: "phase",
          //groupping: true,
        },
        {
          text: "!",
          align: "center",
          sortable: true,
          value: "satisfaction",
          //groupping: true,
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.taClient"),
          align: "end",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "cronoClient",
          sort: (a, b) => a.valueOf() - b.valueOf(),
        },
        // {
        //   text: "INTM (ho)",
        //   align: "end",
        //   cellClass: "text-no-wrap",
        //   sortable: true,
        //   value: "stats.crono.supplier",
        // },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tpc"),
          align: "end",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "tpc",
          sort: (a, b) => a.valueOf() - b.valueOf(),
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tct"),
          align: "end",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "tct",
          sort: (a, b) => a.valueOf() - b.valueOf(),
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tctPercentage"),
          align: "end",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "tct.percentage",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tcr"),
          align: "end",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "tcr",
          sort: (a, b) => a.valueOf() - b.valueOf(),
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tcrPercentage"),
          align: "end",
          cellClass: "text-no-wrap",
          sortable: true,
          value: "tcr.percentage",
        },
      ],
      privGrouppingId: null,
    };
  },
  mounted() {
    if (this.$route.params.filtered)
      this.setViewSubtitre(
        this.$vuetify.lang.t(
          `$vuetify.dashboard.cards.${this.$route.params.filtered}`
        )
      );
    this.getTickets();
  },
  computed: {
    ...mapState("app", ["userItemsPerPage"]),
    ...mapState("tickets", ["loading", "baseTags"]),
    ...mapState("tickets", {
      tickets: (state) => state.items,
    }),
    ...mapState("users", { users: (state) => state.items }),
    itemsInGroup,
    afterDateFrmated() {
      return this.afterDate ? dayjs(this.afterDate).format("L") : "";
    },
    items() {
      let result = this.tickets;
      if (this.$route.params.filtered) {
        switch (this.$route.params.filtered) {
          case "open":
            result = this.getTicketByStatus()(TICKET_STATUS_OPEN);
            break;
          case "waiting_ct":
            result = this.getTicketByStatus()(TICKET_STATUS_ATTENTE_CT);
            break;
          case "progress_ct":
            result = this.getTicketByStatus()(TICKET_STATUS_COURS_CT);
            break;
          case "waiting_cr":
            result = this.getTicketByStatus()(TICKET_STATUS_ATTENTE_CR);
            break;
          case "progress_cr":
            result = this.getTicketByStatus()(TICKET_STATUS_COURS_CR);
            break;
          case "resolved":
            result = this.getTicketByStatus()(TICKET_STATUS_RESOLVED);
            break;
          case "closed":
            result = this.getTicketByStatus()(TICKET_STATUS_CLOSED);
            break;
          case "created":
          default:
            break;
        }
      }
      return result;
    },
    itemsPerPage: {
      get: function () {
        return this.groupping ? -1 : this.userItemsPerPage;
      },
      set: function (value) {
        if (value > 0 || value === -1) this.setUserItemsPerPage(value);
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
    ...mapCacheActions("tickets", { getTickets: "all" }),
    ...mapMutations("tickets", { selectTicket: "select" }),
    ...mapMutations("app", ["setUserItemsPerPage", "setViewSubtitre"]),
    ...mapGetters("tickets", { getTicketByStatus: "getItemsByStatus" }),
    displayFromDate,
    setClassCliclable,
    color,
    classPercentage(i) {
      if (i <= 90) return ["success--text"];
      if (i <= 100) return ["warning--text"];
      return ["error--text"];
    },
    quickViewTicket(ticket) {
      this.selectTicket(ticket);
      this.$router.push({
        name: "ticketView",
        params: {
          id: ticket.id,
          requestSatisfaction: !ticket.satisfaction.length,
        },
      });
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
    satisfaction(val) {
      return (
        this.satisfactions[val] || {
          color: "gris",
          icon: "alert-outline",
          text: "Pas de satisfaction",
        }
      );
    },
    downloadData(type) {
      // TODO: Speed optimization _.memoize in traslate
      exportData(
        this.items,
        {
          lang: (str) => this.$vuetify.lang.t(str),
          getUserName: (id) => this.getUserName(id),
        },
        type,
        "Tickets"
      );
    },
    take_by(ticket) {
      return this.getUserName(ticket.take_by);
    },
    // TODO: Speed optimization _.memoize in getUserName
    getUserName(id) {
      let user = this.users.find((user) => user.id === id);
      if (!user) {
        return "-";
      }
      return user.fullName;
    },
  },
  beforeRouteEnter(to, from, next) {
    next();
  },
};
</script>
<style>
.clickable {
  cursor: pointer;
  user-select: none;
}
.v-data-table.reverseScrollY > .v-data-table__wrapper,
.v-data-table.reverseScrollY > .v-data-table__wrapper > table {
  transform: rotateX(180deg);
}
.v-data-table.reverseScrollY > .v-data-table__wrapper::-webkit-scrollbar {
  height: 10px;
}
.v-data-table.reverseScrollY > .v-data-table__wrapper::-webkit-scrollbar-thumb {
  background: #ef6c00;
}
.v-data-table.reverseScrollY > .v-data-table__wrapper {
  overflow-y: hidden;
}
</style>
