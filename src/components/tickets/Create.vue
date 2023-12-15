<template>
  <v-form ref="form" v-model="valid">
    <v-row>
      <v-col cols="12">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-text-short"
          v-model="current.title"
          :readonly="!canEdit"
          :label="
            $vuetify.lang.t('$vuetify.tickets.title') + (canEdit ? ' *' : '')
          "
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.tickets.title'),
              ])
            ),
          ]"
        />
      </v-col>
      <v-expansion-panels accordion v-if="!canEdit && !asPage" class="mb-2">
        <v-expansion-panel>
          <v-expansion-panel-header v-slot="{ open }">
            <span class="ml-n3"
              ><v-icon class="mr-2">mdi-text-subject</v-icon>
              {{
                $vuetify.lang.t("$vuetify.tickets.description") +
                (open ? " : " : "")
              }}</span
            >
          </v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-container
              style="height: 20vh; overflow: auto"
              v-html="current.description"
            />
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>
      <v-col cols="12" v-else-if="!canEdit && asPage">
        <label
          :class="
            !hasDescription
              ? 'error--text'
              : 'v-label theme--light caption grey--text text--darken-1'
          "
        >
          <v-icon
            :class="{
              'error--text': !hasDescription,
              'mr-2': true,
            }"
            >mdi-text-subject</v-icon
          >
          {{
            $vuetify.lang.t("$vuetify.tickets.description") +
            (canEdit ? " *" : "")
          }}
        </label>
        <v-card class="mt-3 ml-7">
          <v-card-text
            style="height: 25vh; overflow: auto;max-width: 40vw;"
            v-html="current.description"
          />
        </v-card>
      </v-col>
      <v-col cols="12" v-else>
        <label
          :class="
            !hasDescription
              ? 'error--text'
              : 'caption theme--light grey--text text--darken-1'
          "
        >
          <v-icon
            :class="{
              'error--text': !hasDescription,
              'mr-2': true,
            }"
            >mdi-text-subject</v-icon
          >
          {{
            $vuetify.lang.t("$vuetify.tickets.description") +
            (canEdit ? " *" : "")
          }}
        </label>
        <tiptap-vuetify
          class="mt-2"
          ref="tvDecription"
          v-model="wyswygDescription"
          :extensions="extensions"
          @blur="$refs.form.validate()"
        />
      </v-col>
      <v-col cols="12" sm="6" v-if="current.id">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-calendar"
          readonly
          :value="displayFromDate(current.created_at, true)"
          :label="$vuetify.lang.t('$vuetify.tickets.created_at')"
        />
      </v-col>
      <v-col cols="12" sm="6" v-if="current.id">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-update"
          readonly
          :value="displayFromDate(current.updated_at, true)"
          :label="$vuetify.lang.t('$vuetify.tickets.updated_at')"
        />
      </v-col>
      <v-col cols="12" sm="6">
        <v-select
          dense
          :items="getPrioritiesForSelect"
          v-model="current.priority"
          :readonly="!canEdit"
          @change="
            () => {
              current.tag_principal = null;
              current.criticality = 0;
            }
          "
          prepend-icon="mdi-format-list-bulleted-type"
          :label="
            $vuetify.lang.t('$vuetify.tickets.type') + (canEdit ? ' *' : '')
          "
          :rules="[
            rules.required(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.tickets.type'),
              ])
            ),
          ]"
        />
      </v-col>
      <v-col cols="12" sm="6" v-if="isSoftwareRequiered">
        <v-autocomplete
          class="tagsCombo"
          dense
          prepend-icon="mdi-laptop"
          v-model="current.tag_principal"
          :readonly="!canEdit"
          :menu-props="{ top: true, offsetY: true }"
          :items="tagOptions"
          item-value="value"
          item-text="value"
          return-object
          :label="
            $vuetify.lang.t('$vuetify.tickets.software') +
            (canEdit && isSoftwareRequiered ? ' *' : '')
          "
          :rules="[
            rules.requiredSoftware(
              $vuetify.lang.t('$vuetify.rules.required', [
                $vuetify.lang.t('$vuetify.tickets.software'),
              ]),
              isSoftwareRequiered
            ),
          ]"
          @change="evaluateCriticality"
        />
      </v-col>
      <v-col cols="12" sm="10" v-if="isSoftwareRequiered">
        <v-combobox
          dense
          class="tagsCombo"
          small-chips
          multiple
          prepend-icon="mdi-laptop-windows"
          v-model="current.tags"
          :clearable="canEdit"
          :readonly="!canEdit"
          :menu-props="{ top: true, offsetY: true }"
          :items="tagOptions"
          :label="$vuetify.lang.t('$vuetify.tickets.softwareComp')"
          :hint="canEdit ? $vuetify.lang.t('$vuetify.tickets.hint') : null"
        >
          <template v-slot:selection="{ attrs, item, select, selected }">
            <v-chip
              small
              v-bind="attrs"
              :close="canEdit"
              :disabled="!canEdit"
              :input-value="selected"
              @click="select"
              @click:close="remove(item)"
            >
              {{ item.text || item }}
            </v-chip>
          </template>
        </v-combobox>
      </v-col>
      <v-col cols="12" sm="2" v-if="current.id && isSoftwareRequiered">
        <v-text-field
          dense
          hide-details="auto"
          prepend-icon="mdi-alert-outline"
          readonly
          :value="
            $vuetify.lang.t(
              '$vuetify.tickets.criticality._' + current.criticalityLevel
            )
          "
          :label="$vuetify.lang.t('$vuetify.tickets.criticality')"
        />
      </v-col>
      <v-col cols="12" v-if="current.id">
        <v-autocomplete
          dense
          small-chips
          multiple
          readonly
          append-icon=""
          prepend-icon="mdi-account-group"
          v-model="current.participants"
          :items="users"
          :label="$vuetify.lang.t('$vuetify.tickets.participants')"
        />
      </v-col>
      <v-col cols="12" v-if="!current.id">
        <v-combobox
          dense
          class="ma-2 font-italic"
          small-chips
          multiple
          prepend-icon="mdi-email-multiple-outline"
          deletable-chips
          hide-details
          clearable
          v-model="current.ccTo"
          :label="$vuetify.lang.t('$vuetify.tickets.notify')"
        />
      </v-col>
      <v-col cols="12" v-if="!current.id">
        <v-file-input
                multiple
                show-size
                dense
                hide-details
                v-model="current.uploadFiles"
                :rules="[rules.sizeLimit]"
                class="font-italic"
                :placeholder="$vuetify.lang.t('$vuetify.tickets.files')"
              />
      </v-col>
    </v-row>
  </v-form>
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
import { mapGetters, mapState } from "vuex";
import { mapCacheActions } from "vuex-cache";
import displayFromDate from "@/utilities/displayFromDate";
import formatBytes, { maxFileUpload } from "@/utilities/formatBytes";
import absoluteHref from "@/utilities/absoluteHref";
import { color } from "@/models/Ticket";
import _get from "lodash/get";
import _sumBy from "lodash/sumBy";


export default {
  props: {
    asPage: {
      type: Boolean,
      default: false,
    },
    edition: {
      type: Boolean,
      default: false,
    },
  },
  components: { TiptapVuetify },
  data: () => ({
    valid: false,
    rules: {
      required: (message) => (v) => (!!v && v != "-") || message,
      requiredSoftware: (message, byValue) => (v) =>
        byValue || (!!v && v != "-") || message,
      minOne: (message) => (v) => !!(v || []).length || message,
      sizeLimit: (value) =>
        !value ||
        _sumBy(value, "size") < maxFileUpload ||
        `${formatBytes(_sumBy(value, "size"))} > ${formatBytes(
          maxFileUpload
        )}"`,
    },
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
  }),
  created() {
    this.getUsers();
    this.getTags();
  },
  computed: {
    ...mapState("app", {
      dialogActive: (state) => state.dialogActive,
    }),
    ...mapState("tickets", {
      current: (state) => state.current,
      tagOptions: (state) => state.tags,
    }),
    ...mapGetters("users", { users: "forSelect" }),
    ...mapGetters("tickets", ["getPrioritiesForSelect"]),
    modalComponent() {
      return this.current.id ? "user-edition" : "user-creation";
    },
    hasDescription() {
      return !!(this.current.description || "").replace(/<[^>]*>/g, "");
    },
    canEdit() {
      return this.edition || !this.current.id;
    },
    isSoftwareRequiered() {
      return this.current.priority > 130;
    },
    wyswygDescription: {
      get() {
        return this.current.description;
      },
      set(val) {
        this.current.description = absoluteHref(val);
      },
    },
  },
  watch: {
    valid() {
      this.$emit("validate", this.valid && this.hasDescription);
    },
    hasDescription() {
      this.$refs.form.validate();
      this.$emit("validate", this.valid && this.hasDescription);
    },
  },
  methods: {
    ...mapCacheActions("tickets", ["getTags"]),
    ...mapCacheActions("users", { getUsers: "all" }),
    color,
    displayFromDate,
    remove(item) {
      this.current.tags.splice(this.current.tags.indexOf(item), 1);
      this.current.tags = [...this.current.tags];
    },
    evaluateCriticality(data) {
      this.current.tag_principal = data.value;
      this.current.criticality = _get(data, "criticality", 0);
    },
  },
};
</script>
<style>
.tagsCombo .v-messages__message {
  white-space: break-spaces;
}
</style>
