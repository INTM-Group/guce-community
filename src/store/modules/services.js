import request from "@/plugins/request";
import ServiceAbstract from "@/store/service.abstract";
import Service from "@/models/Service";

const servicesStorage = new ServiceAbstract(request, "services", Service, {
  getters: {
    forSelect: state =>
      state.items.map(item => ({
        text: item.name,
        value: item.id
      }))
  }
});

export default servicesStorage;
