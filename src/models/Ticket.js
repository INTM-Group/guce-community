import ModelAbstract from "@/models/ModelAbstract";
import dayjs from "@/plugins/moment";
import _has from "lodash/has";
import _get from "lodash/get";
import openHourCalculation, { strToDayjs } from "@/utilities/openHourCalculation";
import LapsDisplayer from "@/utilities/LapsDisplayer";

export const priorityLevels = [];
priorityLevels[250] = "Anomalie Bloquante";
priorityLevels[190] = "Anomalie Non Bloquante";
priorityLevels[130] = "Demande Administrative";
priorityLevels[70] = "Demande d'Information";
priorityLevels[10] = "Prestation";

export default class Ticket extends ModelAbstract {
  static STATUS_DISABLED = 0; // same
  static STATUS_VALID = 1;  // same
  static STATUS_OPEN = 2;  // same
  static STATUS_COURS_CT = 4;  // en cours CT
  static STATUS_ATTENTE_CT = 8;  // en attente CT
  static STATUS_COURS_CR = 16; // en cours Cr
  static STATUS_ATTENTE_CR = 32; // en attente Cr
  static STATUS_RESOLVED = 64; // same (resolu)
  static STATUS_CLOSED = 128;  // same (clos)
  static now = dayjs();

  className = "ticket";

  constructor(original = {}, settings, holidays) {
    super(original);
    this.open_at = dayjs(this.created_at);
    this.status = original["status"];
    this.priority = original["priority"];
    this.criticality = original["criticality"];
    this.service_id = original["service_id"];
    this.creator_id = original["creator_id"];
    this.take_by = original["take_by"];
    this.title = original["title"];
    this.description = original["description"];
    this.participants = original["participants"];
    this.tags = original["tags"];
    this.tag_principal = original["tag_principal"];
    this.satisfaction = original["satisfaction"];
    let { sla, opening } = settings || { sla: null, opening: null };
    this.stats = original["stats"] || {
      take_it: 0,
      resolved: 0,
      crono: {
        client: 0,
        supplier: 0
      },
      close: 0
    };
    let tpcValid = true;
    let tctValid = true;
    let tcrValid = true;
    this.sla = null;
    if (this.id) {
      if (!_has(this.stats, "in_hours")) {
        let workingDay = opening[this.open_at.day()].reduce(
          (result, session) => [_.min([...result, ...session]), _.max([...result, ...session])],
          []
        );
        //get work start time and format it from string to dayjs
        let workingStartAt = strToDayjs(this.open_at.format("YYYY-MM-DD ") + workingDay[0]);

        //get work end time and format it from string to dayjs
        let workingEndAt = strToDayjs(this.open_at.format("YYYY-MM-DD ") + workingDay[1]);

        this.stats.in_hours = this.open_at.isBetween(workingStartAt, workingEndAt, "m", "[]");
      }
      this.sla =
        sla.find(
          slaItem =>
            slaItem.priority == this.priority &&
            slaItem.criticality == this.criticality &&
            slaItem.schedule == this.stats.in_hours
        ) ||
        sla.find(
          slaItem =>
            slaItem.priority == this.priority &&
            slaItem.criticality == 0 &&
            slaItem.schedule
        );
      if (!this.stats.take_it) {
        tpcValid = false;
        this.stats.take_it = openHourCalculation(this.open_at, dayjs(), opening, holidays).open.asMilliseconds();
      }
      if (!this.stats.work_around) {
        tctValid = false;
        this.stats.work_around = openHourCalculation(this.open_at, dayjs(), opening, holidays).open.asMilliseconds();
      }
      if (!this.stats.resolved) {
        tcrValid = false;
        this.stats.resolved = openHourCalculation(this.open_at, dayjs(), opening, holidays).open.asMilliseconds();
      }
    }
    let tpcLimit = 1;
    let tctLimit = 4;
    let tcrLimit = 24;
    if (this.sla) {
      tpcLimit = _get(this.sla, `tpc`, 1);
      tctLimit = _get(this.sla, `tct`, 4);
      tcrLimit = _get(this.sla, `tcr`, 24);
    }
    this.tpc = new LapsDisplayer(this.stats.take_it, tpcLimit, tpcValid);
    this.tct = new LapsDisplayer([70, 130].indexOf(this.priority) >= 0 ? 0 : this.stats.work_around, tctLimit, tctValid);
    this.tcr = new LapsDisplayer(this.stats.resolved, tcrLimit, tcrValid);
    this.cronoClient = new LapsDisplayer(this.stats.crono.client, 24, true);
  }

  get criticalityLevel() {
    return Math.floor(this.criticality / 120);
  }

  get priorityLevel() {
    return Math.floor(this.criticality / 60);
  }

  toSheet({ lang, getUserName }, type) {
    let result = {};
    result[lang("$vuetify.tickets.id")] = this.id;
    result[lang("$vuetify.tickets.created_at")] = type === "CSV" ? this.open_at.format("L LTS") : this.open_at.toDate();
    result[lang("$vuetify.tickets.updated_at")] =
      type === "CSV" ? this.updated_at.format("L LTS") : this.updated_at.toDate();
    result[lang("$vuetify.tickets.title")] = this.title;
    result[lang("$vuetify.tickets.requested_by")] = getUserName(this.creator_id);
    result[lang("$vuetify.tickets.software")] = this.tag_principal ? this.tag_principal : "";
    result[lang("$vuetify.tickets.softwareComp")] = this.tags.map(i => i.text || i).join(", ");
    result[lang("$vuetify.tickets.criticality")] = lang("$vuetify.tickets.criticality._" + this.criticalityLevel);
    result[lang("$vuetify.tickets.type")] = lang("$vuetify.tickets.priority._" + this.priority);
    result[lang("$vuetify.tickets.who")] = lang("$vuetify.tickets.who._" + this.status);
    result[lang("$vuetify.tickets.phase")] =
      this.priority <= 130
        ? lang("$vuetify.tickets.phase.__" + this.status)
        : lang("$vuetify.tickets.phase._" + this.status) ;
    result[lang("$vuetify.tickets.status")] = lang("$vuetify.tickets.status._" + this.status);
    result[lang("$vuetify.tickets.satisfaction")] = this.satisfaction >=0 ? lang("$vuetify.tickets.satisfaction._" + this.satisfaction):"";
    result[lang("SNCF")] = (this.cronoClient / (60 * 60 * 1000)).toFixed(2);
    //result[lang("INTM")] = (this.stats.crono.supplier / (60 * 60 * 1000)).toFixed(2);
    result[lang("$vuetify.tickets.tpc") + "%"] = type === "CSV" ? this.tpc.percentageLocal : this.tpc.percentage;
    result[lang("$vuetify.tickets.tct") + "%"] = type === "CSV" ? this.tct.percentageLocal : this.tct.percentage;
    result[lang("$vuetify.tickets.tcr") + "%"] = type === "CSV" ? this.tcr.percentageLocal : this.tcr.percentage;
    return result;
  }
}

const ticketStatus = {};

ticketStatus[`${Ticket.STATUS_DISABLED}`] = Ticket.STATUS_DISABLED;
ticketStatus[`${Ticket.STATUS_VALID}`] = Ticket.STATUS_VALID;
ticketStatus[`${Ticket.STATUS_OPEN}`] = Ticket.STATUS_OPEN;
ticketStatus[`${Ticket.STATUS_COURS_CT}`] = Ticket.STATUS_COURS_CT;
ticketStatus[`${Ticket.STATUS_ATTENTE_CT}`] = Ticket.STATUS_ATTENTE_CT;
ticketStatus[`${Ticket.STATUS_COURS_CR}`] = Ticket.STATUS_COURS_CR;
ticketStatus[`${Ticket.STATUS_ATTENTE_CR}`] = Ticket.STATUS_ATTENTE_CR;
ticketStatus[`${Ticket.STATUS_RESOLVED}`] = Ticket.STATUS_RESOLVED;
ticketStatus[`${Ticket.STATUS_CLOSED}`] = Ticket.STATUS_CLOSED;

export { ticketStatus };

const iconStatus = {};

iconStatus[`${Ticket.STATUS_DISABLED}`] = "mdi-none";
iconStatus[`${Ticket.STATUS_VALID}`] = "mid-none";
iconStatus[`${Ticket.STATUS_OPEN}`] = "mid-none";
iconStatus[`${Ticket.STATUS_COURS_CT}`] = "mid-none";
iconStatus[`${Ticket.STATUS_ATTENTE_CT}`] = "mid-none";
iconStatus[`${Ticket.STATUS_COURS_CR}`] = "mid-none";
iconStatus[`${Ticket.STATUS_ATTENTE_CR}`] = "mid-none";
iconStatus[`${Ticket.STATUS_RESOLVED}`] = "mid-none";
iconStatus[`${Ticket.STATUS_CLOSED}`] = "mid-none";

export const ticketIcons = {
  status: iconStatus,
  type: {
    "250": "mdi-format-list-bulleted-square",
    "190": "mdi-format-list-checkbox",
    "70": "mdi-format-list-bulleted-triangle ",
    "130": "mdi-format-list-bulleted ",
    "10": "mdi-not-equal-variant",
    "": "mdi-format-list-bulleted-type "
  },
  priority: {},
  criticality: {}
};

export const ticketAssets = {
  iconStatus
};

export function color(value) {
  if (value <= 70) return "success";
  if (value <= 130) return "info";
  if (value <= 190) return "warning";
  return "error";
}
