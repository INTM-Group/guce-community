<script>
import { Bar, mixins } from "vue-chartjs";

const { reactiveProp } = mixins;

export default {
  extends: Bar,
  mixins: [reactiveProp],
  props: ["options"],
  data: () => ({
    htmlLegend: null,
  }),
  mounted() {
    this.renderChart(this.chartData, this.options);
    this.$emit("input", this.generateLegend());
  },
};
</script>
