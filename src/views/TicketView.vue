<template>
  <v-card class="ml-3 mb-3 mr-1" :loading="loading">
    <div
      :style="{ display: $vuetify.breakpoint.smAndUp ? 'flex' : 'block' }"
      v-if="ticket.id"
    >
      <div class="v-card__title">
        <h4 class="blue--text text--darken-4 pr-1">{{ ticket.title }}</h4>
        <h4 class="grey--text text--lighten-2">| Ticket #{{ ticket.id }} |</h4>
        <small class="grey--text ml-2 caption" v-if="ticket.id">
          {{ $vuetify.lang.t("$vuetify.tickets.requested_by") }}
          <strong class="font-italic">{{ requested_by }}</strong>
          <fragment v-if="ticket.take_by">
            |
            {{ $vuetify.lang.t("$vuetify.tickets.take_by") }}
            <strong class="font-italic">{{ take_by }}</strong>
          </fragment>
        </small>
      </div>
      <v-spacer />
      <div class="v-card__title" style="flex-wrap: nowrap">
        <v-chip
          dark
          :color="`status_${ticket.status}`"
          class="elevation-3 font-weight-black mr-3"
          big
        >
          {{ $vuetify.lang.t("$vuetify.tickets.status.__" + ticket.status) }}
        </v-chip>
        <v-tooltip
          bottom
          v-if="
            user.type & USET_TYPE_MANAGER &&
            edition &&
            ticket.status != TICKET_STATUS_CLOSED
          "
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              small
              text
              v-bind="attrs"
              v-on="on"
              color="primary"
              :disabled="!valid"
              :loading="loadingTicket"
              @click="save()"
            >
              <v-icon color="blue darken-4">mdi-content-save</v-icon>
            </v-btn>
          </template>
          <span>{{ $vuetify.lang.t("$vuetify.dialog.save") }}</span>
        </v-tooltip>
        <v-tooltip
          bottom
          v-if="
            user.type & USET_TYPE_MANAGER &&
            !edition &&
            ticket.status != TICKET_STATUS_CLOSED
          "
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              small
              text
              v-bind="attrs"
              v-on="on"
              :loading="loadingTicket"
              @click="edition = !edition"
            >
              <v-icon color="blue darken-4">mdi-pencil</v-icon>
            </v-btn>
          </template>
          <span>{{ $vuetify.lang.t("$vuetify.dialog.modify") }}</span>
        </v-tooltip>
        <v-tooltip
          bottom
          v-else-if="
            user.type & USET_TYPE_MANAGER &&
            ticket.status != TICKET_STATUS_CLOSED
          "
        >
          <template v-slot:activator="{ on, attrs }">
            <v-btn
              small
              text
              v-bind="attrs"
              v-on="on"
              color="error"
              @click="
                edition = !edition;
                refresh();
              "
            >
              <v-icon color="blue darken-4">mdi-close</v-icon>
            </v-btn>
          </template>
          <span>{{ $vuetify.lang.t("$vuetify.dialog.cancel") }}</span>
        </v-tooltip>
      </div>
    </div>
    <v-card-text v-if="!ticket.id">
      {{ $vuetify.lang.t("$vuetify.loading") }}...
    </v-card-text>
    <v-card-text v-if="ticket.id">
      <v-row>
        <v-col>
          <v-tabs :show-arrows="$vuetify.breakpoint.smAndDown">
            <v-tab>{{ $vuetify.lang.t("$vuetify.ticketView.messages") }}</v-tab>
            <v-tab-item>
              <ticket-messages v-on:message-sended="refresh()" />
            </v-tab-item>
            <v-tab>{{ $vuetify.lang.t("$vuetify.ticketView.details") }}</v-tab>
            <v-tab-item class="px-4 pt-6">
              <ticket-editor
                v-on:validate="validate"
                as-page
                :edition="edition"
              />
            </v-tab-item>
            <v-tab>{{ $vuetify.lang.t("$vuetify.ticketView.files") }}</v-tab>
            <v-tab-item>
              <ticket-files />
            </v-tab-item>
            <v-tab>{{
              $vuetify.lang.t("$vuetify.ticketView.notifications")
            }}</v-tab>
            <v-tab-item>
              <p>
                Les courriels suivants recevront des notifications :<br />
                <small>(En plus des participants)</small>
              </p>
              <ul>
                <li v-for="item in notifyMails" :key="item">
                  {{ item }}
                </li>
              </ul>
            </v-tab-item>
          </v-tabs>
        </v-col>
        <v-col v-if="$vuetify.breakpoint.mdAndUp" cols="5">
          <v-card
            elevation="0"
            outlined
            color="grey lighten-4"
            style="border-color: lightgray !important"
          >
            <v-card-title class="grey--text darken-1"
              >{{ $vuetify.lang.t("$vuetify.activities") }}
              <v-dialog v-model="dialog" max-width="50vw">
                <template v-slot:activator="{ on, attrs }">
                  <v-icon
                    color="grey darken1"
                    v-bind="attrs"
                    v-on="on"
                    class="ml-4"
                    >mdi-timeline-help-outline</v-icon
                  >
                </template>
                <v-card>
                  <v-toolbar dark flat dense max-height="3em" color="secondary">
                    <v-toolbar-title>
                      {{ $vuetify.lang.t("$vuetify.activities.help") }}
                    </v-toolbar-title>
                    <v-spacer />
                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-icon @click="dialog = false" v-bind="attrs" v-on="on"
                          >mdi-close</v-icon
                        >
                      </template>
                      {{ $vuetify.lang.t("$vuetify.dialog.close") }}
                    </v-tooltip>
                  </v-toolbar>
                  <v-list style="max-height: 50vh; overflow-y: auto">
                    <v-list-item
                      v-for="(aIcon, aIconId) in {
                        '4': 'mdi-message',
                        '8': 'mdi-message-reply',
                      }"
                      :key="aIconId"
                    >
                      <v-list-item-avatar>
                        <v-icon>{{ aIcon }}</v-icon>
                      </v-list-item-avatar>
                      <v-list-item-content>
                        <v-list-item-title>
                          {{
                            $vuetify.lang.t(
                              `$vuetify.activities.description._${aIconId}`
                            )
                          }}
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                    <v-list-item
                      v-for="(refColor, index) in [1, 4, 8, 64, 128]"
                      :key="`color_${index}`"
                    >
                      <v-list-item-avatar>
                        <v-icon :class="`status_${refColor}`">mdi-none</v-icon>
                      </v-list-item-avatar>
                      <v-list-item-content>
                        <v-list-item-title>
                          {{
                            $vuetify.lang.t(
                              `$vuetify.tickets.status.__${refColor}`
                            )
                          }}
                        </v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list>
                </v-card>
              </v-dialog>
            </v-card-title>
            <hr />
            <section class="activity-timeline">
              <v-timeline dense align-top>
                <v-timeline-item
                  fill-dot
                  class="white--text mb-12 pb-0"
                  color="primary"
                  large
                  v-if="
                    user.type & USER_TYPE_SUPPLIER &&
                    ticket.status & TICKET_STATUS_OPEN
                  "
                >
                  <template v-slot:icon>
                    <span>{{ `${user.initials}` }}</span>
                  </template>
                  <v-row>
                    <v-col>
                      <v-select
                        :items="
                          users.filter((item) => item.type & USER_TYPE_SUPPLIER)
                        "
                        v-model="takeItBy"
                        solo
                        width="200"
                        :placeholder="
                          $vuetify.lang.t('$vuetify.activities.take_it_by')
                        "
                      />
                    </v-col>
                    <v-col>
                      <v-btn
                        class="ma-1 float-right"
                        large
                        @click="
                          takeIt({ ticket, takeItBy }).then(() => refresh())
                        "
                        color="primary"
                      >
                        {{ $vuetify.lang.t("$vuetify.activities.take_it") }}
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-timeline-item>
                <v-timeline-item
                  v-for="activity in activities"
                  :key="activity.id"
                  :color="activity.color"
                  :icon="activity.icon"
                  class="pr-1 grey--text darken-1 pr-2 pb-0"
                >
                  <div
                    class="
                      text-right text-no-wrap
                      caption
                      float-right
                      mt-2
                      pl-1
                    "
                  >
                    {{ displayFromDate(activity.created_at, true) }}
                  </div>
                  <p
                    v-for="description in activity.description"
                    :key="description"
                    class="font-weight-bold subtitle-1 mt-1 mb-0"
                  >
                    {{
                      $vuetify.lang.t(
                        description,
                        $vuetify.lang.t(
                          "$vuetify.tickets.status.__" +
                            (activity.data.fromStatus || 0)
                        ),
                        $vuetify.lang.t(
                          "$vuetify.tickets.status.__" + activity.data.toStatus
                        ),
                        getUserName(activity.user_id)
                      )
                    }}
                  </p>
                  <i class="caption">{{ getUserName(activity.user_id) }}</i>
                </v-timeline-item>
                <v-timeline-item
                  color="status_2"
                  icon="mdi-ticket"
                  class="pr-1 grey--text darken-1 pr-2 pb-0"
                >
                  <div
                    class="
                      text-right text-no-wrap
                      caption
                      float-right
                      mt-2
                      pl-1
                    "
                  >
                    {{ displayFromDate(ticket.created_at, true) }}
                  </div>
                  <p class="font-weight-bold subtitle-1 mt-1 mb-0">
                    {{
                      $vuetify.lang.t(
                        "$vuetify.activities.description._00",
                        $vuetify.lang.t("$vuetify.tickets.status.__0"),
                        $vuetify.lang.t(
                          "$vuetify.tickets.status.__" + ticket.status
                        ),
                        getUserName(ticket.creator_id)
                      )
                    }}
                  </p>
                  <i class="caption">{{ getUserName(ticket.creator_id) }}</i>
                </v-timeline-item>
              </v-timeline>
            </section>
          </v-card>
        </v-col>
      </v-row>
    </v-card-text>
    <v-dialog v-model="dialogSatisfaction" persistent max-width="550"  v-if="
                !(user.type & USER_TYPE_SUPPLIER)">
        <v-card>
          <v-card-title class="text-h5"> Satisfaction </v-card-title>
          <v-card-text
            >Pouvez-vous évaluer votre niveau de satisfaction?</v-card-text
          >
          <v-item-group
              v-model="satisfaction"
              @change="sendSatisfaction()"
            >
          <v-container>
            <v-row>
              <v-col
                v-for="optionSatisfaction in [
                  { icon: 'emoticon-sad-outline', color: 'error' },
                  { icon: 'emoticon-neutral-outline', color: 'warning' },
                  {
                    icon: 'emoticon-happy-outline',
                    color: 'green darken-3',
                  },
                ]"
                :key="optionSatisfaction.icon"
                cols="12"
                md="4"
              >
                <v-item v-slot:default="{ active, toggle }">
                  <v-card
                    :color="active ? 'blue' : ''"
                    class="d-flex align-center"
                    @click="toggle"
                  >
                    <v-card-title class="text-center">
                      <v-icon size="128" :color="optionSatisfaction.color">{{
                        `mdi-${optionSatisfaction.icon}`
                      }}</v-icon>
                    </v-card-title>
                  </v-card>
                </v-item>
              </v-col>
            </v-row>
          </v-container>
          </v-item-group>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="red" text @click="dialogSatisfaction = false">
              Plus tard
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
  </v-card>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import TicketEditor from "@/components/tickets/Create";
import TicketMessages from "@/components/tickets/Messages";
import TicketFiles from "@/components/tickets/Files";
import { mapCacheActions } from "vuex-cache";
import displayFromDate from "@/utilities/displayFromDate";
import Ticket from "@/models/Ticket";
import User from "@/models/User";

export default {
  components: {
    TicketEditor,
    TicketMessages,
    TicketFiles,
  },
  data: () => ({
    TICKET_STATUS_OPEN: Ticket.STATUS_OPEN,
    TICKET_STATUS_COURS_CT: Ticket.STATUS_COURS_CT,
    TICKET_STATUS_COURS_CR: Ticket.STATUS_COURS_CR,
    TICKET_STATUS_ATTENTE_CT: Ticket.STATUS_ATTENTE_CT,
    TICKET_STATUS_ATTENTE_CR: Ticket.STATUS_ATTENTE_CR,
    TICKET_STATUS_RESOLVED: Ticket.STATUS_RESOLVED,
    TICKET_STATUS_CLOSED: Ticket.STATUS_CLOSED,
    USER_TYPE_CLIENT: User.TYPE_CLIENT,
    USER_TYPE_SUPPLIER: User.TYPE_SUPPLIER,
    USER_TYPE_MANAGER: User.TYPE_MANAGER,
    edition: false,
    dialog: false,
    dialogSatisfaction: false,
    satisfaction: null,
    valid: false,
    USET_TYPE_MANAGER: User.TYPE_MANAGER,
    quickCommentText: null,
    takeItBy: null,
  }),
  created() {
    this.getUsers().then(() => (this.takeItBy = this.user.id));
    this.getTags();
  },
  mounted() {
    if (!this.ticket.id) {
      this.getServices().then(() => this.refresh());
    } else {
      this.getActivities(this.ticket);
      this.getFiles(this.ticket.id);
      //logic pour regarder la satisfaction si ca existe, ouvrir le dialog
      if (this.$route.params.requestSatisfaction && this.ticket.status == this.TICKET_STATUS_CLOSED){
        this.dialogSatisfaction = true;
      }
    }
  },
  methods: {
    ...mapCacheActions("services", { getServices: "all" }),
    ...mapCacheActions("tickets", ["getTags", "put"]),
    ...mapCacheActions("users", { getUsers: "all" }),
    ...mapActions("tickets", ["getFiles"]),
    ...mapActions("tickets", {
      getTicket: "get",
    }),
    ...mapActions("activities", ["takeIt", "quickComment", "sendMessage"]),
    ...mapActions("activities", {
      getActivities: "all",
    }),
    ...mapMutations("tickets", {
      selectTicket: "select",
    }),
    ...mapMutations("activities", {
      clearActivities: "clear",
    }),
    displayFromDate,
    validate(isValid) {
      let permission = this.ticket.id ? "tickets.updated" : "tickets.store";
      this.valid = isValid && this.hasPermission(permission);
    },
    addQuickComment() {
      this.quickComment({
        item: this.ticket,
        message: this.quickCommentText,
      }).then(() => {
        this.quickCommentText = "";
        this.refresh();
      });
    },
    refresh() {
      return this.getTicket(this.$route.params.id).then(() => {
        this.getActivities(this.ticket);
        this.getFiles(this.ticket.id);
       if (!this.ticket.satisfaction.length && this.ticket.status == this.TICKET_STATUS_CLOSED){
        this.dialogSatisfaction = true;
      }
      });
    },
    getUserName(id) {
      let user = this.users.find((user) => user.value === id);
      if (!user) {
        return "-";
      }
      return user.text;
    },
    save() {
      this.put(this.ticket, true).then(() => {
        this.edition = false;
      });
    },
    sendSatisfaction() {
      let toStatus = this.ticket.status;
      this.$nextTick(() => {
        this.sendMessage({
          item: this.ticket,
          message: "Satisfaction soumise: "+ this.$vuetify.lang.t("$vuetify.tickets.satisfaction._" + this.satisfaction),
          satisfaction: this.satisfaction,
          toStatus,
          files: [],
          ccTo: [],
          type:
            this.ACTIVITY_TYPE_CLIENT,
        }).then(() => {
          this.refresh();
        });
      this.dialogSatisfaction = false;
      });
    },
  },
  computed: {
    ...mapState("auth", ["user"]),
    ...mapGetters("auth", ["hasPermission"]),
    ...mapGetters("users", { users: "forSelect" }),
    ...mapGetters("activities", ["messages", "notifyMails",]),
    ...mapState("activities", {
      activities: (state) => state.items,
      loadingActivities: (state) => state.loading,
    }),
    ...mapState("tickets", {
      tagOptions: (state) => state.tags,
      ticket: (state) => state.current,
      loadingTicket: (state) => state.loading,
    }),
    loading() {
      return this.loadingTicket || this.loadingActivities;
    },
    requested_by() {
      return this.getUserName(this.ticket.creator_id);
    },
    take_by() {
      return this.getUserName(this.ticket.take_by);
    },
  },
  beforeRouteLeave(to, from, next) {
    this.selectTicket();
    this.clearActivities([]);
    next();
  },
};
</script>
<style lang="scss">
.activity-timeline {
  height: 70vh;
  overflow-x: hidden;
  overflow-y: scroll;
  & > .v-timeline::before {
    margin-top: 2em;
    height: auto;
  }
}
</style>
