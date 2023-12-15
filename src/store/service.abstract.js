export default class ServiceAbstract {
  constructor(service, path, model, extention = {}) {
    let { state, getters, actions, mutations } = { state: {}, getters: {}, actions: {}, mutations: {}, ...extention };
    this.service = service;
    this.path = `/${path}`;
    this.namespaced = true;
    this.state = {
      loading: false,
      current: new model(),
      items: [],
      ...state,
      config: {
        service,
        path: `/${path}`
      }
    };
    this.getters = {
      ...getters
    };
    this.actions = {
      all: ({ state, commit }) => {
        state.loading = true;
        return this.service
          .get(this.path)
          .then(data => {
            commit("allResponse", data);
          })
          .finally(() => (state.loading = false));
      },
      add: ({ state, commit }, item) => {
        state.loading = true;
        return this.service
          .post(this.path, item)
          .then(data => {
            commit("addResponse", data);
          })
          .finally(() => (state.loading = false));
      },
      get: ({ state, commit }, id) => {
        state.loading = true;
        return this.service
          .get(`${this.path}/${id}`)
          .then(data => {
            commit("getResponse", data);
          })
          .finally(() => (state.loading = false));
      },
      put: ({ state, commit }, item) => {
        state.loading = true;
        return this.service
          .put(`${this.path}/${item.id}`, item)
          .then(data => {
            commit("putResponse", data);
          })
          .finally(() => (state.loading = false));
      },
      remove: ({ state }, item) => {
        state.loading = true;
        return this.service
          .delete(`${this.path}/${item.id}`, item)
          .cath(data => {
            console.debug(data);
            commit("removeResponse", data);
          })
          .finally(() => (state.loading = false));
      },
      ...actions
    };
    this.mutations = {
      select(state, item) {
        state.current = item && item.clone ? item.clone() : new model();
      },
      allResponse(state, data) {
        let items = data.map(item => new model(item));
        state.items = items;
      },
      addResponse(state, data) {
        let items = [...state.items];
        if (!state.current.original || !Object.keys(state.current.original).length) {
          state.current = new model(data);
        }
        if (state.items.length) items.push(new model(data));
        state.items = items;
      },
      getResponse(state, data) {
        state.current = new model(data);
      },
      putResponse(state, data) {
        state.current = new model(data);
        state.items = state.items.map(item => {
          if (item.id === state.current.id) return state.current;
          return item;
        });
      },
      removeResponse(state, data) {
        console.debug(data);
        state.items = _.remove(state.items, item => item.id === state.current.id);
        state.current = null;
      },
      ...mutations
    };
  }
}
