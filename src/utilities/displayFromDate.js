import dayjs from "@/plugins/moment";

export default function (value, addHeur = false) {
    let asDate = dayjs(value);
    return asDate.format("L" + (addHeur ? ' LT':''));
}
