<template>
  <b-row class="expenses-report-date-filter">
    <b-col md="4">
      <date-range-picker
        ref="picker"
        opens="right"
        :locale-data="{ firstDay: 1, format: 'MM-DD-YYYY' }"
        :singleDatePicker="false"
        :timePicker="false"
        :showWeekNumbers="true"
        :showDropdowns="false"
        :autoApply="false"
        :linkedCalendars="true"
        :ranges="false"
        :dateRange="{ startDate: date.startDate, endDate: date.endDate }"
        @update="updateRange"
      >
        <template v-slot:input="picker" style="min-width: 350px;">
          {{ picker.startDate.valueOf() | moment('MM-DD-YYYY') }} -
          {{ picker.endDate.valueOf() | moment('MM-DD-YYYY') }}
        </template>
      </date-range-picker>
    </b-col>
    <b-col md="12" class="mt-3">
      <b-button-group>
        <b-button
          :variant="
            date.type === 'custom' && date.value === 'Yesterday'
              ? 'outline-primary'
              : 'outline-secondary'
          "
          @click="setDate('Yesterday')"
          >Yesterday</b-button
        >
        <b-button
          :variant="
            date.type === 'custom' && date.value === 'Today'
              ? 'outline-primary'
              : 'outline-secondary'
          "
          @click="setDate('Today')"
          >Today</b-button
        >
        <b-button
          :variant="
            date.type === 'custom' && date.value === 'Last Month'
              ? 'outline-primary'
              : 'outline-secondary'
          "
          @click="setDate('Last Month')"
          >Last Month</b-button
        >
        <b-button
          :variant="
            date.type === 'custom' && date.value === 'This Month'
              ? 'outline-primary'
              : 'outline-secondary'
          "
          @click="setDate('This Month')"
          >This Month</b-button
        >
      </b-button-group>
    </b-col>
  </b-row>
</template>

<script>
import DateRangePicker from 'vue2-daterange-picker';
import { getDateRange } from '@/utils/date';
export default {
  name: 'date-range-selector',
  components: {
    DateRangePicker
  },
  props: {
    date: {
      type: Object,
      default: function() {
        return {
          type: 'custom',
          value: 'Today',
          startDate: '2017-09-05',
          endDate: '2017-09-15'
        };
      }
    }
  },

  methods: {
    setDate(newDate) {
      this.$emit('onChange', {
        type: 'custom',
        value: newDate,
        ...getDateRange(newDate)
      });
    },
    updateRange(newDate) {
      this.$emit('onChange', {
        type: 'range',
        value: newDate,
        startDate: newDate.startDate,
        endDate: newDate.endDate
      });
    }
  }
};
</script>
<style scoped lang="scss"></style>
