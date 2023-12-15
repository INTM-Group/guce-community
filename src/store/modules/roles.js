import request from "@/plugins/request";
import ServiceAbstract from "@/store/service.abstract";
import Role from "@/models/Role";

const rolesStorage = new ServiceAbstract(request, "roles", Role, {
  getters: {
    forSelect: state =>
      state.items.map(item => ({
        text: item.description.fr.name,
        value: item.id
      }))
  }
});

export default rolesStorage;
