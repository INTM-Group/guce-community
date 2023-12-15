<template>
  <app-modal
    :valid="valid"
    v-on:primary-action="primaryAction"
    v-on:close="select()"
  >
    <template v-slot:title>
      {{
        $vuetify.lang.t(
          service.id ? "$vuetify.services.singular" : "$vuetify.new.m"
        )
      }}
      {{
        service.id
          ? service.name
          : $vuetify.lang.t("$vuetify.services.singular")
      }}
    </template>
    <v-container style="overflow-y: auto">
      <v-form ref="form" v-model="valid">
        <v-row>
          <v-col cols="6">
            <v-text-field
              dense
              hide-details="auto"
              prepend-icon="mdi-text-short"
              v-model="service.name"
              :label="$vuetify.lang.t('$vuetify.services.name') + ' *'"
              :rules="[
                rules.required(
                  $vuetify.lang.t('$vuetify.rules.required', [
                    $vuetify.lang.t('$vuetify.services.name'),
                  ])
                ),
              ]"
            />
          </v-col>
          <v-col cols="6">
            <v-autocomplete
              dense
              hide-details="auto"
              prepend-icon="mdi-map-clock"
              :items="tzSelect"
              v-model="serviceTimezone"
              :label="
                $vuetify.lang.t('$vuetify.services.settings.timezone') + ' *'
              "
              :rules="[
                rules.required(
                  $vuetify.lang.t('$vuetify.rules.required', [
                    $vuetify.lang.t('$vuetify.services.settings.timezone'),
                  ])
                ),
              ]"
              @change="service.settings.timezone = serviceTimezone"
            />
          </v-col>
        </v-row>
        <v-row class="weekDaysList">
          <v-col v-for="(weekDayName, weekDay) in weekDays" :key="weekDay">
            <v-card
              rounded="xl"
              dark
              height="100%"
              :color="weekDay == 0 || weekDay == 6 ? 'warning' : 'primary'"
            >
              <v-card-subtitle class="text-center pb-0 text-no-warp"
                >{{ weekDayName }}
                <session-add
                  v-on:save-times="(times) => adToWeekDay(times, weekDay)"
                />
              </v-card-subtitle>
              <v-container>
                <v-chip
                  light
                  v-for="(session, index) in service.settings.opening[weekDay]"
                  :key="index"
                  close
                  class="ma-1"
                  close-icon="mdi-close"
                  @click:close="removeSession(weekDay, index)"
                >
                  {{ session[0] }}
                  -
                  {{ session[1] }}
                </v-chip>
              </v-container>
            </v-card>
          </v-col>
        </v-row>
        <v-data-table
          dense
          fixed-header
          hide-default-footer
          :headers="tableHeaders"
          :items="service.settings.slaToDisplay"
          class="elevation-2 mt-5"
        >
          <template v-slot:[`item.schedule`]="{ item }">
            {{ item.schedule ? "HO" : "HNO" }}
          </template>
          <template v-slot:[`item.criticality`]="{ item }">
            {{
              $vuetify.lang.t(
                "$vuetify.tickets.criticality._" + item.criticality
              )
            }}
          </template>
          <template v-slot:[`item.priority`]="{ item }">
            {{ $vuetify.lang.t("$vuetify.tickets.priority._" + item.priority) }}
          </template>
          <template v-slot:[`item.tpc`]="props">
            <v-edit-dialog
              :return-value.sync="props.item.tpc"
              @save="saveSla"
              @cancel="cancelSla"
              @open="openSla"
              @close="closeSla"
            >
              {{ props.item.tpc }}
              <template v-slot:input>
                <v-text-field
                  v-model="props.item.tpc"
                  single-line
                  dense
                  type="number"
                ></v-text-field>
              </template>
            </v-edit-dialog>
          </template>
          <template v-slot:[`item.tct`]="props">
            <v-edit-dialog
              :return-value.sync="props.item.tct"
              @save="saveSla"
              @cancel="cancelSla"
              @open="openSla"
              @close="closeSla"
            >
              {{ props.item.tct }}
              <template v-slot:input>
                <v-text-field
                  v-model="props.item.tct"
                  single-line
                  dense
                  type="number"
                ></v-text-field>
              </template>
            </v-edit-dialog>
          </template>
          <template v-slot:[`item.tcr`]="props">
            <v-edit-dialog
              :return-value.sync="props.item.tcr"
              @save="saveSla"
              @cancel="cancelSla"
              @open="openSla"
              @close="closeSla"
            >
              {{ props.item.tcr }}
              <template v-slot:input>
                <v-text-field
                  v-model="props.item.tcr"
                  single-line
                  dense
                  type="number"
                ></v-text-field>
              </template>
            </v-edit-dialog>
          </template>
        </v-data-table>
      </v-form>
    </v-container>
    <template v-slot:foot-text>
      <small class="grey--text">
        {{ $vuetify.lang.t("$vuetify.fields") }}
      </small>
    </template>
    <template v-slot:primary-action-text>
      {{
        $vuetify.lang.t(`$vuetify.dialog.${service.id ? "save" : "create"}`, [
          $vuetify.lang.t("$vuetify.services.singular"),
        ])
      }}
    </template>
  </app-modal>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import AppModal from "@/components/AppModal";
import SessionAdd from "@/components/services/SessionAdd";
import dayjs from "@/plugins/moment";
import Service from "@/models/Service";

export default {
  components: {
    AppModal,
    SessionAdd,
  },
  data() {
    return {
      valid: false,
      testColorPicke: null,
      serviceTimezone: null,
      rules: {
        required: (message) => (v) => (!!v && v != "-") || message,
        minOne: (message) => (v) => !!(v || []).length || message,
      },
      weekDays: dayjs.weekdays(),
      tableHeaders: [
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.type"),
          align: "center",
          sortable: false,
          value: "priority",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.services.settings.schedule"),
          align: "center",
          sortable: false,
          value: "schedule",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.criticality"),
          align: "center",
          sortable: false,
          value: "criticality",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tpc"),
          align: "end",
          sortable: false,
          value: "tpc",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tct"),
          align: "end",
          sortable: false,
          value: "tct",
        },
        {
          text: this.$vuetify.lang.t("$vuetify.tickets.tcr"),
          align: "end",
          sortable: false,
          value: "tcr",
        },
      ],
    };
  },
  computed: {
    ...mapState("app", ["dialogActive"]),
    ...mapState("tickets", ["baseTags"]),
    ...mapState("services", {
      // service(state) {
      //   if (!state.current.settings.sla) state.current.settings.sla = [];
      //   return state.current;
      // },
      service: 'current'
    }),
    ...mapGetters("app", ["tzSelect"]),
    ...mapGetters("auth", ["hasPermission"]),
  },
  mounted() {
    this.serviceTimezone = this.service.settings.timezone;
  },
  watch: {
    service: function (newVal) {
      this.serviceTimezone = newVal.settings.timezone;
    },
  },
  methods: {
    ...mapMutations("app", ["closeDialog"]),
    ...mapMutations("services", ["select", "reset"]),
    ...mapActions("services", ["add", "put"]),
    validate(isValid) {
      let permission = this.service.id ? "services.show" : "services.store";
      this.valid = isValid && this.hasPermission(permission);
    },
    primaryAction() {
      this.save();
    },
    save() {
      let action = this.service.id ? "put" : "add";
      this[action](this.service, true).then(() => {
        this.select();
      });
      this.closeDialog();
    },
    adToWeekDay(sessionNew, weekDay) {
      let service = new Service(this.service);
      if (!service.settings.opening[weekDay])
        service.settings.opening[weekDay] = [];
      service.settings.opening[weekDay].push(sessionNew);
      this.select(service);
    },
    removeSession(weekDay, index) {
      this.service.settings.opening[weekDay].splice(index, 1);
    },
    saveSla() {},
    cancelSla() {},
    openSla() {},
    closeSla() {},
  },
};
</script>
<style scoped>
.weekDaysList {
  overflow-y: auto;
  min-height: 10vh;
  max-height: 30vh;
  display: grid;
  grid-template-columns: repeat(7, 11em);
  grid-gap: 0px;
}
</style>
