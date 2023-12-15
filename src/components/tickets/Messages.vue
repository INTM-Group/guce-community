<template>
  <section
    class="messagesList my-2"
    :class="{
      'pt-12': !(ticket.status & TICKET_STATUS_CLOSED),
    }"
    id="messages"
  >
    <v-app-bar
      absolute
      color="white"
      dense
      elevate-on-scroll
      scroll-target="#messages"
      class="mt-n2"
    >
      <v-dialog v-model="dialog" max-width="800px">
        <template v-slot:activator="{ on, attrs }">
          <v-btn primary v-bind="attrs" block v-on="on">
            {{
              ticket.status & TICKET_STATUS_RESOLVED
                ? "Nouveau message ou clos "
                : "Nouveau message"
            }}
          </v-btn>
        </template>
        <v-card color="#f5f5f5">
          <v-form
            class="pa-2"
            ref="form"
            id="messageForm"
            @submit="() => false"
          >
            <v-combobox
              dense
              solo
              chips
              multiple
              deletable-chips
              clearable
              hide-details
              class="ma-2 font-italic"
              append-icon="mdi-email-multiple-outline"
              v-model="ccTo"
              :label="$vuetify.lang.t('$vuetify.tickets.copyTo')"
            >
              <template v-slot:prepend>
                <label class="text-no-wrap mt-2">
                  De: <strong>{{ user.full_name }}</strong>
                </label>
                <label class="text-no-wrap mx-3 mt-2">|</label>
                <label class="text-no-wrap mr-1 mt-2">Notifier : </label>
              </template>
            </v-combobox>
            <tiptap-vuetify
              class="mb-2"
              ref="tvDecription"
              v-model="wyswygDescription"
              :toolbar-attributes="{ dense: true, short: true }"
              :placeholder="
                $vuetify.lang.t('$vuetify.activities.actions.writeMessage')
              "
              :disabled="loading"
              :extensions="extensions"
              :card-props="{ flat: true }"
              @blur="$refs.form.validate()"
            />
            <v-file-input
              multiple
              show-size
              dense
              hide-details
              v-model="upladFiles"
              class="font-italic"
              :placeholder="$vuetify.lang.t('$vuetify.tickets.files')"
            />
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSendAndKeep"
              @click="addQuickComment()"
              :disabled="!hasDescription || loading"
            >
              {{ $vuetify.lang.t("$vuetify.activities.actions.sendAndKeep") }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSend"
              @click="addQuickComment()"
              :disabled="!hasDescription || loading"
            >
              {{ $vuetify.lang.t("$vuetify.activities.actions.send") }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSendStartMessage"
              @click="addQuickComment()"
              :disabled="!hasDescription || loading"
            >
              {{
                $vuetify.lang.t("$vuetify.activities.actions.sendOnlyMessage")
              }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSendAndResolved"
              @click="send(TICKET_STATUS_RESOLVED)"
              :disabled="!hasDescription || loading"
            >
              {{
                $vuetify.lang.t("$vuetify.activities.actions.sendAndResolved")
              }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSendAndCorrected"
              @click="send(TICKET_STATUS_COURS_CR)"
              :disabled="!hasDescription || loading"
            >
              {{
                $vuetify.lang.t("$vuetify.activities.actions.sendAndCorrected")
              }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSendAndRefuseCt"
              @click="send(TICKET_STATUS_COURS_CT)"
              :disabled="!hasDescription || loading"
            >
              {{
                $vuetify.lang.t("$vuetify.activities.actions.sendAndRefuseCt")
              }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSendAndRefuseCr"
              @click="send(TICKET_STATUS_COURS_CR)"
              :disabled="!hasDescription || loading"
            >
              {{
                $vuetify.lang.t("$vuetify.activities.actions.sendAndRefuseCr")
              }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="primary"
              elevation="2"
              v-if="isSendAndKeep"
              @click="send(waitStatus)"
              :disabled="!hasDescription || loading"
            >
              {{ $vuetify.lang.t("$vuetify.activities.actions.sendAndWait") }}
            </v-btn>
            <v-btn
              class="ma-2"
              color="error"
              elevation="2"
              v-if="isSendAndRefuseCr"
              @click="dialogClose = true"
              :disabled="!hasDescription || loading"
              >{{
                $vuetify.lang.t("$vuetify.activities.actions.sendAndClose")
              }}</v-btn
            >
            <v-dialog v-model="dialogClose" persistent max-width="290">
              <v-card>
                <v-card-title class="text-h5"> Confirmation </v-card-title>
                <v-card-text
                  >Etes-vous sûr de vouloir clorer ce ticket?</v-card-text
                >
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn color="red" text @click="dialogClose = false">
                    Annuler
                  </v-btn>
                  <v-btn
                    color="primary"
                    text
                    @click="
                      dialogClose = false;
                      send(TICKET_STATUS_CLOSED);
                    "
                  >
                    Oui
                  </v-btn>
                </v-card-actions>
              </v-card>
            </v-dialog>
          </v-form>
        </v-card>
      </v-dialog>
    </v-app-bar>
    <v-dialog
      v-model="dialogSatisfaction"
      persistent
      max-width="550"
      v-if="!(user.type & USER_TYPE_SUPPLIER)"
    >
      <v-card>
        <v-card-title class="text-h5"> Satisfaction </v-card-title>
        <v-card-text
          >Pouvez-vous évaluer votre niveau de satisfaction?</v-card-text
        >
        <v-item-group v-model="satisfaction" @change="sendSatisfaction()">
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
    <article v-for="message in messages" :key="message.id" class="message pa-2">
      <aside>
        <v-avatar
          :color="
            getUser(message.user_id).type & USER_TYPE_CLIENT
              ? 'client'
              : 'support'
          "
          class="mb-2"
          rounded
          size="56"
          >{{ getUserName(message.user_id, true) }}</v-avatar
        >
        <v-icon>{{ message.icon }}-outline</v-icon>
        <v-icon
          :style="{ visibility: !message.data.files ? 'hidden' : 'visible' }"
          >mdi-attachment</v-icon
        >
      </aside>
      <header class="mb-1">
        <h4 class="blue--text text--darken-4">
          {{ getUserName(message.user_id) }}
          <small class="grey--text text--lighten-1">a répondu</small>
        </h4>
        <time class="text-no-wrap">{{
          displayFromDate(message.created_at, true)
        }}</time>
      </header>
      <main v-html="message" />
    </article>
    <article class="message pa-2">
      <aside>
        <v-avatar
          :color="
            getUser(ticket.creator_id).type & USER_TYPE_CLIENT
              ? 'client'
              : 'support'
          "
          class="mb-2"
          rounded
          size="56"
          >{{ getUserName(ticket.creator_id, true) }}</v-avatar
        >
        <v-icon>mdi-message-flash-outline</v-icon>
      </aside>
      <header class="mb-3">
        <h4 class="blue--text text--darken-4">
          {{ getUserName(ticket.creator_id) }}
        </h4>
        <time class="text-no-wrap">{{
          displayFromDate(ticket.created_at, true)
        }}</time>
      </header>
      <main v-html="ticket.description" />
    </article>
  </section>
</template>

<script>
import {
  TiptapVuetify,
  Bold,
  Italic,
  Strike,
  Underline,
  CodeBlock,
  ListItem,
  BulletList,
  OrderedList,
  Link,
  Blockquote,
  HardBreak,
  HorizontalRule,
  History,
} from "tiptap-vuetify";
import { mapActions, mapState, mapGetters } from "vuex";
import { mapCacheActions } from "vuex-cache";
import displayFromDate from "@/utilities/displayFromDate";
import absoluteHref from "@/utilities/absoluteHref";
import Ticket from "@/models/Ticket";
import User from "@/models/User";
import Activity from "@/models/Activity";

export default {
  components: { TiptapVuetify },
  data: () => ({
    TICKET_STATUS_OPEN: Ticket.STATUS_OPEN,
    TICKET_STATUS_COURS_CT: Ticket.STATUS_COURS_CT,
    TICKET_STATUS_COURS_CR: Ticket.STATUS_COURS_CR,
    TICKET_STATUS_ATTENTE_CT: Ticket.STATUS_ATTENTE_CT,
    TICKET_STATUS_RESOLVED: Ticket.STATUS_RESOLVED,
    TICKET_STATUS_ATTENTE_CR: Ticket.STATUS_ATTENTE_CR,
    TICKET_STATUS_CLOSED: Ticket.STATUS_CLOSED,
    USER_TYPE_CLIENT: User.TYPE_CLIENT,
    USER_TYPE_SUPPLIER: User.TYPE_SUPPLIER,
    USER_TYPE_MANAGER: User.TYPE_MANAGER,
    ACTIVITY_TYPE_UPDATE: Activity.TYPE_UPDATE,
    ACTIVITY_TYPE_STATUS: Activity.TYPE_STATUS,
    ACTIVITY_TYPE_MESSAGE: Activity.TYPE_MESSAGE,
    ACTIVITY_TYPE_CLIENT: Activity.TYPE_CLIENT,
    ACTIVITY_TYPE_OTHER: Activity.TYPE_OTHER,
    messageHtml: null,
    dialog: null,
    dialogClose: false,
    dialogSatisfaction: false,
    satisfaction: null,
    extensions: [
      Bold,
      Italic,
      Strike,
      Underline,
      CodeBlock,
      ListItem,
      BulletList,
      OrderedList,
      Link,
      Blockquote,
      HardBreak,
      HorizontalRule,
      History,
    ],
    upladFiles: [],
    ccTo: [],
  }),
  created() {
    this.getUsers();
  },
  methods: {
    ...mapActions("activities", ["sendMessage", "quickComment"]),
    ...mapCacheActions("users", { getUsers: "all" }),
    displayFromDate,
    addQuickComment() {
      this.quickComment({
        item: this.ticket,
        message: this.messageHtml,
        files: this.upladFiles,
        ccTo: this.ccTo,
        type:
          this.user.type & this.USER_TYPE_SUPPLIER
            ? this.ACTIVI_TYPE_MESSAGE
            : this.ACTIVITY_TYPE_CLIENT,
      }).then(() => this.clearForm());
    },
    send(toStatus) {
      if (!toStatus && !(this.ticket.status & this.TICKET_STATUS_OPEN)) {
        toStatus =
          this.user.type & this.USER_TYPE_SUPPLIER
            ? this.TICKET_STATUS_ATTENTE_CT
            : this.TICKET_STATUS_COURS_CT;
      } else if (this.ticket.status < this.TICKET_STATUS_COURS_CT) {
        toStatus = this.ticket.status;
      }
      this.$nextTick(() => {
        this.sendMessage({
          item: this.ticket,
          message: this.messageHtml,
          satisfaction: this.satisfaction,
          toStatus,
          files: this.upladFiles,
          ccTo: this.ccTo,
          type:
            this.user.type & this.USER_TYPE_SUPPLIER
              ? this.ACTIVI_TYPE_MESSAGE
              : this.ACTIVITY_TYPE_CLIENT,
        }).then(() => {
          this.clearForm();
          this.dialog = false;
        });
      });
    },
    sendSatisfaction() {
      let toStatus = this.ticket.status;
      this.$nextTick(() => {
        this.sendMessage({
          item: this.ticket,
          message:
            "Satisfaction soumise: " +
            this.$vuetify.lang.t(
              "$vuetify.tickets.satisfaction._" + this.satisfaction
            ),
          satisfaction: this.satisfaction,
          toStatus,
          files: [],
          ccTo: [],
          type: this.ACTIVITY_TYPE_CLIENT,
        }).then(() => {
          this.clearForm();
        });
        this.dialogSatisfaction = false;
      });
    },
    clearForm() {
      this.messageHtml = "";
      this.upladFiles = [];
      this.ccTo = [];
      this.$emit("message-sended");
    },
    getUser(id) {
      return this.users.find((user) => user.value === id);
    },
    getUserName(id, initials = false) {
      let user = this.getUser(id);
      if (!user) {
        return "-";
      }
      return !initials ? user.text : user.initials;
    },
  },
  watch: {
    dialog() {
      this.satisfaction = null;
    },
  },
  computed: {
    ...mapGetters("auth", ["hasPermission"]),
    ...mapGetters("users", { users: "forSelect" }),
    ...mapGetters("activities", ["messages"]),
    ...mapState("auth", ["user"]),
    ...mapState("tickets", { ticket: "current" }),
    ...mapState("activities", ["loading"]),
    hasDescription() {
      return !!(this.messageHtml || "").replace(/<[^>]*>/g, "");
    },
    wyswygDescription: {
      get() {
        return this.messageHtml;
      },
      set(val) {
        this.messageHtml = absoluteHref(val);
      },
    },
    isSendAndKeep() {
      if (this.user.type & User.TYPE_SUPPLIER) {
        return (
          this.ticket.status & Ticket.STATUS_COURS_CT ||
          this.ticket.status & Ticket.STATUS_COURS_CR
        );
      }
      return (
        this.ticket.status & Ticket.STATUS_ATTENTE_CT ||
        this.ticket.status & Ticket.STATUS_ATTENTE_CR
      );
    },
    isSendAndResolved() {
      if (this.user.type & User.TYPE_SUPPLIER) {
        return this.ticket.status & Ticket.STATUS_COURS_CR;
      }
      return false;
    },
    isSendAndCorrected() {
      if (this.user.type & User.TYPE_SUPPLIER) {
        return this.ticket.status & Ticket.STATUS_COURS_CT;
      }
      return false;
    },
    isSendAndRefuseCt() {
      if (this.ticket.priority == 10) return false;
      if (this.user.type & User.TYPE_SUPPLIER) {
        return false;
      }
      return this.ticket.status & Ticket.STATUS_COURS_CR;
    },
    isSendAndRefuseCr() {
      if (this.user.type & User.TYPE_SUPPLIER) {
        return false;
      }
      return this.ticket.status & Ticket.STATUS_RESOLVED;
    },
    waitStatus() {
      if (this.user.type & User.TYPE_SUPPLIER) {
        if (this.ticket.status & Ticket.STATUS_COURS_CT) {
          return Ticket.STATUS_ATTENTE_CT;
        } else if (this.ticket.status & Ticket.STATUS_COURS_CR) {
          return Ticket.STATUS_ATTENTE_CR;
        }
      } else {
        if (this.ticket.status & Ticket.STATUS_ATTENTE_CT) {
          return Ticket.STATUS_COURS_CT;
        } else if (this.ticket.status & Ticket.STATUS_ATTENTE_CR) {
          return Ticket.STATUS_COURS_CR;
        }
      }
      return false;
    },
    isSend() {
      if (this.user.type & User.TYPE_SUPPLIER) {
        return (
          this.ticket.status & Ticket.STATUS_ATTENTE_CT ||
          this.ticket.status & Ticket.STATUS_ATTENTE_CR
        );
      } else {
        return (
          this.ticket.status & Ticket.STATUS_COURS_CT ||
          this.ticket.status & Ticket.STATUS_COURS_CR
        );
      }
    },
    isSendStartMessage() {
      if (!(this.user.type & User.TYPE_SUPPLIER)) {
        return this.ticket.status & Ticket.STATUS_OPEN;
      }
      return false;
    },
  },
};
</script>

<style lang="scss" scoped>
@import "~vuetify/src/styles/settings/_colors.scss";

#messageForm > footer {
  display: grid;
  grid-gap: 0.5em;
  grid-template-columns: 1fr min-content;
}
.messagesList {
  height: 71vh;
  overflow-x: hidden;
  overflow-y: auto;
  & > article:nth-of-type(even),
  & > form {
    background-color: map-get($grey, "lighten-4");
  }
  & > form {
    position: sticky;
    top: 0;
    z-index: 1;
    border-bottom: dashed 1px map-get($grey, "darken-4");
  }
}
.message {
  display: grid;
  grid-gap: 0.5em;
  grid-template-columns: min-content 1fr;
  grid-template-areas:
    "side head"
    "side main";
  grid-column-gap: 1.5em;
  & > aside {
    grid-area: side;
    width: min-content;
    text-align: center;
  }
  & > header {
    grid-area: head;
    display: grid;
    grid-template-columns: 1fr min-content;
    border-bottom: dashed thin darkblue;
  }
  & > main {
    grid-area: main;
    max-width: 40vw;
  }
}
</style>
