import ModelAbstract from "@/models/ModelAbstract";
import dayjs from "@/plugins/moment";

export default class Service extends ModelAbstract {
  constructor(original = {}) {
    super(original);
    this.name = original["name"];
    this.settings = original["settings"] || {
      timezone: dayjs.tz.guess(),
      opening: [
        [],
        [["08:00", "18:00"]],
        [["08:00", "18:00"]],
        [["08:00", "18:00"]],
        [["08:00", "18:00"]],
        [["08:00", "18:00"]],
        []
      ],
      sla: [
        {
          priority: 250, //(AB)
          schedule: true, //true(HO), false(HNO)
          criticality: 250, //250(C),0(NC)
          tpc: 1,
          tct: 9, //1 jo
          tcr: 90, //10 jo
          tctDisplay: "1jo",
          tcrDisplay: "10jo"
        },
        {
          priority: 250,
          schedule: true,
          criticality: 0,
          tpc: 1,
          tct: 45, //5 jo
          tcr: 180, //20 jo
          tctDisplay: "5jo",
          tcrDisplay: "20jo"
        },
        {
          priority: 250,
          schedule: false,
          criticality: 250,
          tpc: 1,
          tct: 12,
          tcr: 12, // pas precis
          tctDisplay: "12h",
          tcrDisplay: "12h"
        },
        {
          priority: 190, //(ANB)
          schedule: true,
          criticality: 250,
          tpc: 1,
          tct: 45, //5 jo
          tcr: 90, //10 jo
          tctDisplay: "5jo",
          tcrDisplay: "10jo"
        },
        {
          priority: 190,
          schedule: true,
          criticality: 0,
          tpc: 1,
          tct: 90, //10 jo
          tcr: 360, //40 jo
          tctDisplay: "10jo",
          tcrDisplay: "40jo"
        },
        {
          priority: 190,
          schedule: false,
          criticality: 250,
          tpc: 1,
          tct: 48,
          tcr: 48, // pas precis
          tctDisplay: "48h",
          tcrDisplay: "48h"
        },
        {
          priority: 130, //(DA)
          schedule: true,
          criticality: 0, // pas de Criticité
          tpc: 1,
          tct: 45, //5 jo
          tcr: 45, //5 jo
          tctDisplay: "5jo",
          tcrDisplay: "5jo"
        },
        {
          priority: 70, //(Info)
          schedule: true,
          criticality: 0, // pas de Criticité
          tpc: 1,
          tct: 45, //5 jo
          tcr: 45, //5 jo
          tctDisplay: "5jo",
          tcrDisplay: "5jo"
        },
        {
          priority: 10, //(P)
          schedule: true,
          criticality: 0, // pas de Criticité
          tpc: 1000,
          tct: 1000,
          tcr: 1000,
          tctDisplay: "-",
          tcrDisplay: "-"
        }
      ],
      slaToDisplay: [
        {
          priority: 250, //(AB)
          schedule: true, //true(HO), false(HNO)
          criticality: 250, //250(C),0(NC)
          tpc: 1,
          tct: 9, //1 jo
          tcr: 90, //10 jo
          tctDisplay: "1jo",
          tcrDisplay: "10jo"
        },
        {
          priority: 250,
          schedule: true,
          criticality: 0,
          tpc: 1,
          tct: 45, //5 jo
          tcr: 180, //20 jo
          tctDisplay: "5jo",
          tcrDisplay: "20jo"
        },
        {
          priority: 250,
          schedule: false,
          criticality: 250,
          tpc: 1,
          tct: 12,
          tcr: 12, // pas precis
          tctDisplay: "12h",
          tcrDisplay: "12h"
        },
        {
          priority: 190, //(ANB)
          schedule: true,
          criticality: 250,
          tpc: 1,
          tct: 45, //5 jo
          tcr: 90, //10 jo
          tctDisplay: "5jo",
          tcrDisplay: "10jo"
        },
        {
          priority: 190,
          schedule: true,
          criticality: 0,
          tpc: 1,
          tct: 90, //10 jo
          tcr: 360, //40 jo
          tctDisplay: "10jo",
          tcrDisplay: "40jo"
        },
        {
          priority: 190,
          schedule: false,
          criticality: 250,
          tpc: 1,
          tct: 48,
          tcr: 48, // pas precis
          tctDisplay: "48h",
          tcrDisplay: "48h"
        },
        {
          priority: 130, //(DA)
          schedule: true,
          criticality: 0, // pas de Criticité
          tpc: 1,
          tct: 45, //5 jo
          tcr: 45, //5 jo
          tctDisplay: "5jo",
          tcrDisplay: "5jo"
        },
        {
          priority: 70, //(Info)
          schedule: true,
          criticality: 0, // pas de Criticité
          tpc: 1,
          tct: 45, //5 jo
          tcr: 45, //5 jo
          tctDisplay: "5jo",
          tcrDisplay: "5jo"
        },
      ]
    };
  }
}
