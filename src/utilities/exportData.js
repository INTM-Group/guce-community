import XLSX from "xlsx";
import _kebabCase from "lodash/kebabCase";
import dayjs from "@/plugins/moment";
import alasql from "@/../node_modules/alasql/dist/alasql.js";

alasql["private"].externalXlsxLib = XLSX;

export default function exportData(items, config, type = "CSV", sheetid = "Data") {
  let param = [
    `${_kebabCase(sheetid)}-${dayjs().format("YYYY-MM-DD-HHmm")}`,
    { sheetid },
    items.map(item => item.toSheet(config, type))
  ];
  alasql(`SELECT * INTO ${type}(?, ?) FROM ?`, param);
}
