import request from "@/plugins/request";
import ServiceAbstract from "@/store/service.abstract";
import User from "@/models/User";

const usersStorage = new ServiceAbstract(request, "users", User, {
  getters: {
    forSelect: state =>
      state.items.map(item => ({
        text: item.fullName,
        type: item.type,
        initials: item.initials,
        value: item.id
      }))
  },
  mutations: {
    allResponse(state, data) {
      let departments = [];
      let items = data.map(item => {
        let u = new User(item);
        departments.push(u.department);
        return u;
      });
      state.departments = departments.filter((value, index, self) => value && self.indexOf(value) === index);
      state.items = items;
    }
  }
});

export default usersStorage;
