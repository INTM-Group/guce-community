import service from "@/plugins/request";
import router from "@/router";
import _get from "lodash/get";

const state = {
  rememberMe: false,
  user: null,
  token: null
};

const getters = {
  getAccessToken: state => state.token,
  getIsLoged: state => !!state.token,
  getUser: state => state.user,
  hasPermission: state => (permition, user = { id: 0 }) => {
    if (state.user.has_permissions.root || state.user.id == user.id) return true;
    return _get(state.user.has_permissions, permition, false);
  }
};

const actions = {
  login({ dispatch }, credentials) {
    return service
      .post("/auth", credentials)
      .then(data => dispatch("successLogin", data))
      .catch(() => dispatch("logout", false));
  },
  activation({ dispatch }, credentials) {
    return service.post("/auth/activation", credentials).then(data => dispatch("successLogin", data));
  },
  resetPassword({ commit, dispatch }, credentials) {
    commit("SET_LOGED", false);
    commit("SET_TOKEN", null);
    commit("SET_USER", null);
    commit("SET_RGPD", false);
    return service
      .post("/auth/reset/password", credentials)
      .then(data => dispatch("successReset", data))
      .finally(() => {
        commit("SET_LOGED", false);
        commit("SET_TOKEN", null);
        commit("SET_USER", null);
        commit("SET_RGPD", false);
      });
  },
  successLogin({ commit }, { token, user }) {
    commit("SET_LOGED", token && user);
    commit("SET_TOKEN", token);
    commit("SET_USER", user);
  },
  successReset() {},
  logout({ commit }, redirect) {
    service
      .delete("/auth")
      .then(() => {
        commit("SET_LOGED", false);
        commit("SET_TOKEN", null);
        commit("SET_USER", null);
        commit("SET_RGPD", false);
        if (redirect !== false) {
          router.push({ name: "login" });
        }
      })
      .catch(() => {
        window._VMA.$emit("SHOW_SNACKBAR", {
          text: "auth.reLoginNeed",
          color: "warning"
        });
        router.push({ name: "login" });
      })
      .finally(() => window.localStorage.clear());
  }
};

const mutations = {
  SET_RGPD(state, playload) {
    state.rememberMe = !!playload;
    window.localStorage.setItem("rememberMe", state.rememberMe);
  },
  SET_TOKEN(state, token) {
    state.token = token;
  },
  SET_USER(state, user) {
    if (user) {
      user.full_name = `${user.first_name} ${user.last_name}`;
      user.initials = `${user.first_name.substr(0, 1)}${user.last_name.substr(0, 1)}`;
    }
    state.user = user;
  },
  SET_LOGED(state, { loged }) {
    state.isLoged = loged;
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
