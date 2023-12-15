import ModelAbstract from "@/models/ModelAbstract";

export default class Project extends ModelAbstract {

  className = "ticket";

  constructor(original = {}) {
    super(original);
    this.status = original["status"];
    this.priority = original["priority"];
    this.criticality = original["criticality"];
    this.service_id = original["service_id"];
    this.creator_id = original["creator_id"];
    this.budget_hours = original["budget_hours"];
    this.budget_amount = original["budget_amount"];
    this.budget_supplementary = original["budget_supplementary"];
    this.logs = original["logs"];
    this.cost = original["cost"];
    this.title = original["title"];
    this.description = original["description"];
    this.risks = original["risks"];
    this.participants = original["participants"];
    this.surveillance = original["surveillance"];
  }
}
