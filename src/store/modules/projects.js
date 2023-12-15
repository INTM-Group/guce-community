import request from "@/plugins/request";
import ServiceAbstract from "@/store/service.abstract";
import Project from "@/models/Project";

const projectsStorage = new ServiceAbstract(request, "projects", Project);

export default projectsStorage;
