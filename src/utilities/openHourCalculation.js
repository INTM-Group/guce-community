import dayjs from "@/plugins/moment";
import _ from "lodash";

const cacheDateKey = d => d.format("YYYY-MM-DD");
const cacheTwoDateKey = (d1, d2) =>
  (d1 && d1.format ? d1.format("YYYY-MM-DD") : d1) + "|" + (d2 && d2.format ? d2.format("YYYY-MM-DD") : d2);
const isBefore = _.memoize(
  (a, b) => a < b,
  (d1, d2) => d1.valueOf() + "|" + d2.valueOf() /* */
);
const isAfter = _.memoize(
  (a, b) => a > b,
  (d1, d2) => d1.valueOf() + "|" + d2.valueOf() /* */
);
const isSameDate = _.memoize((a, b) => a.isSame(b, "date"), cacheTwoDateKey);
export const strToDayjs = _.memoize(str => dayjs(str, "YYYY-MM-DD hh:mm").add(1, "h"));
const dayjsEndOfDay = _.memoize(d => d.endOf("day"), cacheDateKey);
const isHoliday = _.memoize(
  (h, d) => h.some(hi => d == hi.date),
  (h, d) => d
);
const dayjsDiff = _.memoize(
  (d1, d2) => d1.diff(d2),
  (d1, d2) => d1.toISOString() + d2.toISOString()
);
const durationAsMilliseconds = _.memoize(i => dayjs.duration(i).asMilliseconds());
const startOfNextDay = _.memoize(d => d.add(1, "day").startOf("day"), cacheDateKey);

/**
 * Calculer le temps de ouverture
 *
 * @params {string|dayjs} startDate Heure de depart
 * @params {string|dayjs} endDate Heure de fermeture
 *
 * @returns {object} Object avec les durées d'ouverture, de fermeture et totale
 *                ex:
 *                  { open: dayjs.duration, close: dayjs.duration, total: dayjs.duration }
 *
 *
 * openHourCalculation(
 *    '2020-01-03 09:30:00',
 *    '2020-01-07 14:30:00',
 *    window.businessHours.business_hours,
 *    window.holidays
 * )
 */
function openHourCalculation(startDate, endDate, businessHours, holidays) {
  // The variables we want to calculate and return
  let result = {
    open: 0,
    close: 0,
    total: 0
  };

  //To make sure both dates are given within the openHourCalculation function
  if (!startDate || !endDate) return null;

  //To format startDate using dayjs
  if (typeof startDate === "string") {
    startDate = dayjs(startDate);
  }

  //To format endDate using dayjs
  if (typeof endDate === "string") {
    endDate = dayjs(endDate);
  }

  //if endDate comes before startDate
  if (isBefore(endDate, startDate)) {
    return result;
  }

  let current = startDate;
  let startEndIsSame = isSameDate(startDate, endDate);

  // Loop while currentDate is less than endDate
  while (isBefore(current, endDate)) {
    //Get the current day full name (monday, tuesday...)
    let currentWeekDay = current.day();

    //Get business Hours for current working day
    let workingDay = businessHours[currentWeekDay].reduce(
      (result, session) => [_.min([...result, ...session]), _.max([...result, ...session])],
      []
    );
    // if workingDay is a weekend or a holiday
    if (!workingDay || isHoliday(holidays, current.format("YYYY-MM-DD"))) {
      workingDay = ["00:00", "00:00"];
    }

    //get work start time and format it from string to dayjs
    let workingStartAt = strToDayjs(current.format("YYYY-MM-DD ") + workingDay[0]);

    //get work end time and format it from string to dayjs
    let workingEndAt = strToDayjs(current.format("YYYY-MM-DD ") + workingDay[1]);

    if (startEndIsSame) {
      result.open += durationAsMilliseconds(
        dayjsDiff(dayjs.max(dayjs.min(endDate, workingEndAt), workingStartAt), dayjs.max(current, workingStartAt))
      );
      result.open = Math.max(result.open, 0);
      break;
    }

    let currentIsBeforeWorkingStartAt = isBefore(current, workingStartAt);
    let endDateIsAfterWorkingEndAt = isAfter(endDate, workingEndAt);

    //current is begore work start time
    if (currentIsBeforeWorkingStartAt) {
      //add to close hours the difference between work start time and current
      result.close += durationAsMilliseconds(dayjsDiff(workingStartAt, current));
    }

    //if endDate is after work end time
    if (endDateIsAfterWorkingEndAt) {
      let endCurrentDay = dayjsEndOfDay(current);
      // add to close hours the difference between current day end time and work day end time
      result.close += durationAsMilliseconds(dayjsDiff(endCurrentDay, workingEndAt));
    }

    //current is after work start time
    if (isAfter(current, workingStartAt) && endDateIsAfterWorkingEndAt) {
      //add to open hours the difference between work end time and current
      result.open += durationAsMilliseconds(dayjsDiff(workingEndAt, current));
    }

    // if its a full working day between startDate and endDate of a ticket
    if (
      isAfter(workingEndAt, workingStartAt) &&
      currentIsBeforeWorkingStartAt &&
      isBefore(current, workingEndAt) &&
      endDateIsAfterWorkingEndAt
    ) {
      //add to open hours all the work hours of this day (difference between work end time and work start time)
      result.open += durationAsMilliseconds(dayjsDiff(workingEndAt, workingStartAt));
    }

    //if endDate (the time the ticket is closed) is before work end time and startDate is before work start time
    if (isBefore(endDate, workingEndAt) && !startEndIsSame) {
      // add to open hours the difference between endDate and work start time
      result.open += durationAsMilliseconds(dayjsDiff(endDate, workingStartAt));
    }

    // after calculations, move current to the next day
    current = startOfNextDay(current);
  }

  // Return the number of open, close and total hours in a duration format
  result.total = result.open + result.close;
  return {
    open: dayjs.duration(Math.max(0, result.open)),
    close: dayjs.duration(Math.max(0, result.close)),
    total: dayjs.duration(Math.max(0, result.total))
  };
}

export default openHourCalculation;
