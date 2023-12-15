import router from "@/router/index";
import colors from "vuetify/es5/util/colors";
import tz from "@storage/timezones.json";

const cleanSnackbar = {
  show: false,
  text: "",
  color: ""
};

const state = {
  clientName: document.body.getAttribute("client"),
  mode: "light",
  primary: "blue",
  snackbar: cleanSnackbar,
  modalComponent: "add-user-dialog",
  dialogActive: false,
  userItemsPerPage: 10,
  viewSubtitre: [],
  tz
};

// getters
const getters = {
  getThemeColors: state => ({
    light: {
      primary: colors[state.primary].darken1,
      secondary: colors[state.primary].darken4,
      accent: "#82B1FF",
      error: colors.red.accent2,
      info: "#2196F3",
      success: colors.green.base,
      warning: colors.orange.darken1,
      client: colors.orange.base,
      support: colors.blue.darken1,
      status_0: colors.grey.base,
      status_1: colors.green.accent4,
      status_2: colors.green.accent4, //ouvert
      status_4: colors.blue.lighten1, //en cours ct
      status_8: colors.orange.lighten1, //en attente ct
      status_16: colors.blue.darken1, //en cours cr
      status_32: colors.orange.darken1, //en attente cr
      status_64: colors.grey.lighten1, //resolu
      status_128: colors.grey.darken2, //clos
      status_undefined: colors.blue.lighten1,
      ticket_open: colors.green.accent4,
      ticket_cours_ct: colors.blue.lighten1,
      ticket_attente_ct: colors.orange.lighten1,
      ticket_cours_cr: colors.blue.darken1,
      ticket_attente_ct: colors.orange.darken1,
      ticket_resolved: colors.grey.lighten1,
      ticket_closed: colors.grey.darken2
    },
    dark: {
      primary: colors[state.primary].darken4,
      secondary: colors[state.primary].darken1,
      accent: "#82B1FF",
      error: colors.red.accent2,
      info: "#2196F3",
      success: colors.green.base,
      warning: colors.orange.darken1
    }
  }),
  isDark: state => state.mode == "dark",
  tzSelect: state =>
    state.tz
      .reduce((result, item) => {
        return [...result, ...item.utc];
      }, [])
      .sort()
};

// actions
const actions = {
  displaySnackbar({ commit }, snackbar) {
    commit("setSnackbar", snackbar);
    setTimeout(() => {
      commit("setSnackbar", cleanSnackbar);
    }, 3200);
  }
};

// mutations
const mutations = {
  setThemeColor(state, payload) {
    state.themeColor = payload;
  },
  setSnackbar(state, snackbar) {
    state.snackbar = snackbar;
  },
  setViewSubtitre(state, values) {
    state.viewSubtitre = values;
  },
  setUserItemsPerPage(state, values) {
    state.userItemsPerPage = values;
  },
  closeDialog(state) {
    state.dialogActive = false;
  },
  ticketDialog(state) {
    state.modalComponent = "add-ticket-dialog";
    if (router.currentRoute.name === "ticketView") {
      router.push({ name: "tickets" });
    }
    state.dialogActive = true;
  },
  userDialog(state) {
    state.modalComponent = "add-user-dialog";
    state.dialogActive = true;
  },
  serviceDialog(state) {
    state.modalComponent = "service-dialog";
    state.dialogActive = true;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
