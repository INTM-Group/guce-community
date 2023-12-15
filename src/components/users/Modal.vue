<template>
  <app-modal
    :valid="valid"
    v-on:primary-action="primaryAction"
    v-on:close="
      edition = false;
      select();
    "
  >
    <template v-slot:title>
      {{
        $vuetify.lang.t(
          selectedUser.id ? "$vuetify.users.singular" : "$vuetify.new.f"
        )
      }}
      {{
        selectedUser.id
          ? "#" + selectedUser.id
          : $vuetify.lang.t("$vuetify.users.singular")
      }}
    </template>
    <component
      v-bind:is="modalComponent"
      v-on:validate="validate"
      :edition="edition"
      class="py-5"
    />
    <template v-slot:foot-text>
      <small class="grey--text" v-if="edition || !selectedUser.id">
        {{ $vuetify.lang.t("$vuetify.fields") }}
      </small>
    </template>
    <template v-slot:primary-action-text>
      {{
        $vuetify.lang.t(
          `$vuetify.dialog.${
            selectedUser.id ? (edition ? "save" : "modify") : "create"
          }`
        )
      }}
    </template>
  </app-modal>
</template>

<script>
import { mapActions, mapMutations, mapState, mapGetters } from "vuex";
import AppModal from "@/components/AppModal";
import UserCreation from "@/components/users/Create";
import UserEdition from "@/components/users/Edition";

export default {
  components: {
    AppModal,
    UserCreation,
    UserEdition,
  },
  data: () => ({
    valid: false,
    edition: false,
  }),
  computed: {
    ...mapState("app", {
      dialogActive: (state) => state.dialogActive,
    }),
    ...mapState("users", {
      selectedUser: (state) => state.current,
    }),
    ...mapState("roles", {
      roles: (state) => state.items.filter(i=>i.type == 2),
    }),
    ...mapGetters("auth", ["hasPermission"]),
    modalComponent() {
      return this.selectedUser.id ? "user-edition" : "user-creation";
    },
  },
  methods: {
    ...mapMutations("app", ["closeDialog"]),
    ...mapMutations("users", ["select", "reset"]),
    ...mapActions("users", ["add", "put"]),
    validate(isValid) {
      let permission = this.selectedUser.id ? "users.updated" : "users.store"
      this.valid = isValid && this.hasPermission(permission);
    },
    primaryAction() {
      if (!this.edition && this.selectedUser.id) {
        this.edition = true;
      } else this.save();
    },
    save() {
      let action = this.selectedUser.id ? "put" : "add";
      if(this.selectedUser.roles == 0){
        this.selectedUser.roles=[this.roles[0].id];
      }
      this[action](this.selectedUser, true).then(() => {
        this.edition = false;
        this.select();
      });
      this.closeDialog();
    },
  },
};
</script>
