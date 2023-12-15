import _get from "lodash/get";
import _set from "lodash/set";
import _uniq from "lodash/uniq";
import _uniqBy from "lodash/uniqBy";
import _reduce from "lodash/reduce";
import _map from "lodash/map";
import _isArray from "lodash/isArray";
import _each from "lodash/each";
import _sum from "lodash/sum";
import _sumBy from "lodash/sumBy";
import request, { storagePrivate } from "@/plugins/request";
import Ticket, { ticketIcons, priorityLevels } from "@/models/Ticket";
import ServiceAbstract from "@/store/service.abstract";
import dayjs from "@/plugins/moment";

const defaultBar = {
  fill: false,
  borderWidth: 1
};

// Getters
const getters = {
  getPrioritiesForSelect: ({ baseTags }) =>
    baseTags.reduce(
      (result, item, key) =>
        item
          ? [
              {
                text: item,
                value: key
              },
              ...result
            ]
          : result,
      []
    ),
  getByStatus: state => statusFilter => state.items.filter(item => item.status == statusFilter).length,
  getItemsByStatus: state => statusFilter => state.items.filter(item => item.status == statusFilter),
  charByType: state => ({ year, month }) => {
    let startIn = `${year || "2019"}-${month || "01"}-01`,
      endIn = `${year || dayjs().format("YYYY")}-${month || 12}-01`;
    let datasets = _map(state.itemsByType, ({ label, items, id }) => ({
      label,
      ...defaultBar,
      id,
      data: [items.filter(item => !item.open_at || item.open_at.isBetween(startIn, endIn, "month", "[]")).length],
      get total() {
        return _sum(this.data);
      },
      get icon() {
        return _get(ticketIcons, `type.${this.id}`, "mdi-none");
      },
      get borderColor() {
        return getComputedStyle(document.querySelector("#app")).getPropertyValue(
          `--ticket-type-${this.id}-color-solid`
        );
      },
      get backgroundColor() {
        return getComputedStyle(document.querySelector("#app")).getPropertyValue(
          `--ticket-type-${this.id}-color-trasparent`
        );
      },
      get hoverBorderColor() {
        return getComputedStyle(document.querySelector("#app")).getPropertyValue(
          `--ticket-type-${this.id}-color-solid`
        );
      },
      get hoverBackgroundColor() {
        return getComputedStyle(document.querySelector("#app")).getPropertyValue(
          `--ticket-type-${this.id}-color-trasparent`
        );
      }
    }));
    return {
      labels: [""],
      datasets,
      get total() {
        return _sumBy(this.datasets, "total");
      }
    };
  },
  chartByStatus: state => ({ year }) => {
    let datasets = _map(state.itemsByType, ({ label, items, id }) => ({
      label,
      ...defaultBar,
      id,
      data: items.reduce((result, item) => {
        let month = item.open_at.month();
        if (item.open_at.year() == year) _set(result, month, _get(result, month, 0) + 1);
        return result;
      }, []),
      get total() {
        return _sum(this.data);
      },
      get borderColor() {
        return getComputedStyle(document.querySelector("#app")).getPropertyValue(
          `--ticket-type-${this.id}-color-solid`
        );
      },
      get backgroundColor() {
        return getComputedStyle(document.querySelector("#app")).getPropertyValue(
          `--ticket-type-${this.id}-color-trasparent`
        );
      }
    }));
    return {
      labels: ["Janv", "Févr", "Mars", "Avr", "Mai", "Juin", "Juil", "Août", "Sept", "Oct", "Nov", "Déc"],
      datasets,
      get total() {
        return _sumBy(this.datasets, "total");
      }
    };
  }
};

const actions = {
  getTags: ({ state, commit }) => {
    state.loading = true;
    return request
      .get("/intm/client/software_catalog.json", {
        baseURL: storagePrivate.href
      })
      .then(data => {
        commit("getTagsResponse", data);
      })
      .finally(() => (state.loading = false));
  },
  all: ({ state, commit, dispatch }) => {
    state.loading = true;
    let isConnecte = [];
    let services = dispatch("services/all", null, { root: true }).then(() => isConnecte.push(true));
    let users = dispatch("users/all", null, { root: true }).then(() => isConnecte.push(true));
    let tickets = state.config.service
      .get(state.config.path)
      .then(data => {
        commit("allResponse", data);
        isConnecte.push(true);
      })
      .finally(() => (state.loading = false));
    return Promise.all([services, users, tickets]).finally(() => {
      if (isConnecte.length < 3) dispatch("auth/logout", true, { root: true });
    });
  },
  add: ({ state, commit }, item) => {
    state.loading = true;
    let { service, path } = state.config;
    let timeout = 1000 * 60 * 1.5;
    let obj = _reduce(
      item,
      (result, item, key) => {
        if (!["original", "updated_at", "stats"].find(i => i == key) && item) {
          if (typeof item != "string") {
            if (_isArray(item) && key === "uploadFiles") {
              timeout *= 10;
              item.forEach(f => result.append(key + "[]", f, f.name));
            } else result.append(key, JSON.stringify(item));
          } else result.append(key, item);
        }
        return result;
      },
      new FormData()
    );
    return service
      .post(path, obj, {
        timeout,
        headers: {
          "Content-Type": "multipart/form-data; boundary=${form._boundary}"
        }
      })
      .then(data => {
        commit("addResponse", data);
      })
      .finally(() => (state.loading = false));
  },
  getFiles: ({ state, commit }, ticketId) => {
    state.loading = true;
    let filesPath = `/guce/ticket/${ticketId || state.current.id}/`;
    return request
      .get(filesPath, {
        baseURL: storagePrivate.href,
        auth: {
          username: "guce",
          password: "INTM"
        }
      })
      .then(data => {
        commit("ticketFilesResponse", { data, filesPath });
      })
      .catch(() => {
        commit("ticketFilesResponse", { data: [], filesPath });
      })
      .finally(() => (state.loading = false));
  }
};

const mutations = {
  allResponse(state, data) {
    _each(state.itemsByStatus, el => (el.items = []));
    _each(state.itemsByType, el => (el.items = []));
    let items = data.map(item => {
      let service = this.state.services.items.find(s => s.id == item.service_id);
      let ticket = new Ticket(item, _get(service, "settings"), state.holidays);
      state.itemsByStatus[ticket.status].items.push(ticket);
      state.itemsByType[ticket.priority].items.push(ticket);
      return ticket;
    });
    state.items = items;
  },
  ticketFilesResponse(state, { data, filesPath }) {
    state.files = (data || []).map(file => ({ ...file, filesPath }));
  },
  addResponse(state, data) {
    let items = [...state.items];
    let service = this.state.services.items.find(s => s.id == data.service_id);
    state.current = new Ticket(data, _get(service, "settings"), state.holidays);
    items.push(state.current);
    state.items = items;
  },
  getResponse(state, data) {
    let service = this.state.services.items.find(s => s.id == data.service_id);
    state.current = new Ticket(data, _get(service, "settings"), state.holidays);
    state.items = state.items.map(item => {
      if (item.id === state.current.id) return state.current;
      return item;
    });
  },
  putResponse(state, data) {
    let service = this.state.services.items.find(s => s.id == data.service_id);
    state.current = new Ticket(data, _get(service, "settings"), state.holidays);
    state.items = state.items.map(item => {
      if (item.id === state.current.id) return state.current;
      return item;
    });
  },
  getTagsResponse(state, data) {
    let items = data.map(item => ({
      text: _get(item, "label", item),
      value: _get(item, "label", item),
      software: _get(item, "value", item),
      criticality: _get(item, "criticality", item),
      repository: _get(item, "repository", item)
    }));
    state.tags = _uniqBy([...state.tags, ...items], "text").sort();
    let softwareStats = state.tags.reduce((result, item) => {
      let exist = result.findIndex(el => el.name == (item.software.name || item.text));
      if (exist >= 0) {
        result[exist].versions.push(item.software.version);
      } else {
        result.push({
          name: item.software.name || item.text,
          versions: [item.software.version]
        });
      }
      return result;
    }, []);
    state.tagsStats = softwareStats.reduce((countTags, item) => {
      countTags += Math.ceil(_uniq(item.versions).length / 3);
      return countTags;
    }, 0);
  }
};

export const state = {
  baseTags: priorityLevels,
  tags: [],
  tagsStats: 0,
  files: [],
  holidays: []
};

state.itemsByStatus = _reduce(
  [
    Ticket.STATUS_OPEN,
    Ticket.STATUS_COURS_CT,
    Ticket.STATUS_COURS_CR,
    Ticket.STATUS_ATTENTE_CT,
    Ticket.STATUS_ATTENTE_CR,
    Ticket.STATUS_RESOLVED,
    Ticket.STATUS_CLOSED
  ],
  (result, value) =>
    _set(result, value, {
      label: `status._${value}`,
      items: []
    }),
  {}
);
state.itemsByType = _reduce(
  state.baseTags,
  (result, value, key) =>
    value
      ? _set(result, key, {
          label: value,
          id: key,
          items: []
        })
      : result,
  {}
);

const holidaysDataFiles = require.context("../../../storage/public/holydays", true, /.*\/.*\.json$/);

holidaysDataFiles.keys().forEach(fileName => {
  let hd = holidaysDataFiles(fileName).filter(item => !item.counties);
  state.holidays = [...state.holidays, ...hd];
});

const extention = {
  state,
  getters,
  actions,
  mutations
};

const ticketStorage = new ServiceAbstract(request, "tickets", Ticket, extention);

export default ticketStorage;
