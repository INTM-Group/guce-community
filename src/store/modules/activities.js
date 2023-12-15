import request from "@/plugins/request";
import _reduce from "lodash/reduce";
import _isArray from "lodash/isArray";
import ServiceAbstract from "@/store/service.abstract";
import Activity from "@/models/Activity";
import Ticket from "@/models/Ticket";
import alasql from "@/../node_modules/alasql/dist/alasql.js";

const state = {
  ticketSatisfactions: [],
  ticketSatisfaction: null,
  satisfactionsItems: [],
  satisfactionRate: null
};

const actions = {
  all({ state, commit }, playload) {
    state.loading = true;
    if (!playload) {
      return new Promise(() => {
        state.loading = false;
        throw new Error("Objet non acceptable");
      });
    }
    let { service, path } = state.config;
    let baseObkect = playload.className.toLocaleLowerCase();
    if (!["ticket", "project"].includes(baseObkect))
      return new Promise(() => {
        state.loading = false;
        throw new Error("Objet non acceptable");
      });

    return service
      .get(`/${baseObkect}/${playload.valueOf()}${path}`)
      .then(data => {
        commit("allResponse", data);
        commit("setTicketSatisfactions", data);
      })
      .finally(() => (state.loading = false));
  },
  add: ({ state, commit }, { item, activity }) => {
    state.loading = true;
    if (!item || !["ticket", "project"].includes(item.className.toLocaleLowerCase())) {
      return new Promise(() => {
        state.loading = false;
        throw new Error("Objet non acceptable");
      });
    }
    let baseObkect = item.className.toLocaleLowerCase();
    let { service, path } = state.config;
    let obj = _reduce(
      activity,
      (result, item, key) => {
        if (!["original", "updated_at"].find(i => i == key) && item) {
          if (typeof item != "string") {
            if (_isArray(item) && key === "files") {
              item.forEach(f => result.append(key + "[]", f, f.name));
            } else result.append(key, JSON.stringify(item));
          } else result.append(key, item);
        }
        return result;
      },
      new FormData()
    );

    return service
      .post(`/${baseObkect}/${item.valueOf()}${path}`, obj, {
        headers: {
          "Content-Type": "multipart/form-data; boundary=${form._boundary}"
        }
      })
      .then(data => {
        commit("addResponse", data);
      })
      .finally(() => (state.loading = false));
  },
  takeIt({ dispatch }, { ticket, takeItBy }) {
    let activity = new Activity({
      type: Activity.TYPE_STATUS,
      data: {
        // TODO: verifie le type de ticket pour chenge a STATUS_COURS_CR
        toStatus: [130,70,10].indexOf(ticket.priority) < 0 ? Ticket.STATUS_COURS_CT : Ticket.STATUS_COURS_CR,
        takeItBy
      }
    });

    return dispatch("add", {
      item: ticket,
      activity
    });
  },
  quickComment({ dispatch }, { item, message, ccTo, type, files }) {
    type = (type || 0) | Activity.TYPE_MESSAGE;
    let activity = new Activity({
      type,
      data: {
        ccTo
      },
      message
    });
    activity.files = files;

    return dispatch("add", {
      item,
      activity
    });
  },
  sendMessage({ dispatch }, { item, message, toStatus, ccTo, type, files, satisfaction }) {
    type = (type || 0) | Activity.TYPE_MESSAGE;
    if (!(item.status & toStatus)) {
      type |= Activity.TYPE_STATUS;
    }
    let activity = new Activity({
      type,
      data: {
        toStatus,
        ccTo,
        satisfaction
      },
      message
    });
    activity.files = files;

    return dispatch("add", {
      item,
      activity
    });
  },
  getAllTicketsSatisfaction({ state, commit }) {
    state.loading = true;
    let { service } = state.config;

    return service
      .get(`/satisfactions`)
      .then(data => {
        commit("setSatisfaction", data);
      })
      .finally(() => (state.loading = false));
  }
};

const getters = {
  messages: state => state.items.filter(item => item.type & Activity.TYPE_MESSAGE),
  notifyMails: state =>
    state.items.reduce((result, item) => (_isArray(item.data.ccTo) ? [...result, ...item.data.ccTo] : result), [])
};

const mutations = {
  addResponse(state, data) {
    let items = [...state.items];
    if (!state.current.original || !Object.keys(state.current.original).length) {
      state.current = new Activity(data);
    }
    if (state.items.length) items.unshift(new Activity(data));
    state.items = items;
  },
  clear(state) {
    this.commit("activities/select", null);
    state.items = [];
    state.ticketSatisfactions = [];
    state.ticketSatisfaction = null;
  },
  setTicketSatisfactions(state, data) {
    state.ticketSatisfactions = alasql(
      "SEARCH /WHERE(type & ? AND data->satisfaction ) AS @activity \
    RETURN(@activity->id AS id, @activity->user_id AS userId, @activity->data->satisfaction AS satisfaction) \
		FROM ?",
      [Activity.TYPE_MESSAGE, data]
    );
    state.ticketSatisfaction = Math.round(
      state.ticketSatisfactions.reduce((result, item) => result + item.satisfaction, 0) /
        state.ticketSatisfactions.length
    );
  },
  setSatisfaction(state, data) {
    state.satisfactionsItems = data;
    state.satisfactionRate = Math.round(data.reduce((result, item) => result + item.satisfaction, 0) / data.length);
  }
};

const extention = {
  state,
  actions,
  getters,
  mutations
};

const activitiesStorage = new ServiceAbstract(request, "activities", Activity, extention);

export default activitiesStorage;
