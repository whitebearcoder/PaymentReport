<template>
  <b-container fluid class="root-container">
    <top-navbar></top-navbar>
    <b-container fluid class="main-container ml-auto mr-auto py-4">
      <date-ranger-selector
        :date="dateRange"
        @onChange="changeDateRange"
      ></date-ranger-selector>
      <expenses-report-data-filter
        :filter="filter"
        @onChange="changeFilter"
      ></expenses-report-data-filter>
      <b-row class="mt-4">
        <b-col md="12">
          <table class="report-table">
            <thead>
              <tr>
                <th>Date/Time</th>
                <th>User</th>
                <th>Supplier</th>
                <th>Expenses Type</th>
                <th>Payment method</th>
                <th>Sum(in Original currency)</th>
                <th>In Euro</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="expenses in expensesReportList" :key="expenses.id">
                <td>{{ expenses.date | moment('MM-DD-YYYY') }}</td>
                <td>{{ expenses.user.username }}</td>
                <td>{{ expenses.supplier.name }}</td>
                <td>{{ expenses.expenses_type.name }}</td>
                <td>
                  {{
                    expenses.payment_methods.map(item => item.name).join(', ')
                  }}
                </td>
                <td>{{ `${expenses.currency} ${expenses.sum}` }}</td>
                <td>{{ Number(expenses.sum_euro).toFixed(2) }}</td>
                <td>
                  {{ expenses.received === 'Yes' ? 'Approved' : 'Pending' }}
                </td>
              </tr>
              <tr v-if="expensesReportList.length > 0">
                <td class="border-none"></td>
                <td class="border-none"></td>
                <td class="border-none"></td>
                <td class="border-none"></td>
                <td class="border-none"></td>
                <td align="right">
                  Total:
                </td>
                <td>
                  {{
                    Number(
                      expensesReportList.reduce((sum_euro, current) => {
                        return sum_euro + current.sum_euro;
                      }, 0)
                    ).toFixed(2)
                  }}
                </td>
                <td class="border-none"></td>
              </tr>
            </tbody>
          </table>
        </b-col>
      </b-row>
    </b-container>
  </b-container>
</template>

<script>
import { mapGetters } from 'vuex';
import { USER_TYPE } from '@/constants';
import { calculateCurrency } from '@/utils/currency';
import { getDateRange } from '@/utils/date';
import TopNavbar from '@/sharedComponents/top-navbar.vue';
import DateRangerSelector from '@/sharedComponents/date-range-selector.vue';
import ExpensesReportDataFilter from './data-filter.vue';
export default {
  name: 'Expenses-report',
  components: {
    TopNavbar,
    DateRangerSelector,
    ExpensesReportDataFilter
  },
  computed: {
    ...mapGetters(['currentUser', 'check'])
  },
  data() {
    return {
      currencyData: {},
      dateRange: {
        type: 'custom',
        value: 'Today',
        startDate: getDateRange('today').startDate,
        endDate: getDateRange('today').endDate
      },
      filter: {
        supplier_id: 'all',
        supermaster: 'all',
        master: 'all',
        agent: 'all',
        api: 'all',
        whitelabel: 'all',
        other: 'all',
        expenses_type: 'all',
        payment_method: 'all'
      },
      expensesReportList: []
    };
  },
  async created() {
    if (this.currentUser.user_type === USER_TYPE.INSERT) this.$router.push('/');

    const loader = this.$loading.show();

    // get currency
    try {
      const resCurrency = await axios.get('/api/currency');
      if (resCurrency) {
        this.currencyData = JSON.parse(resCurrency.data.currency);
      }
    } catch (err) {
      this.currencyData = {};
    }

    this.filterReport();

    loader.hide();
  },
  methods: {
    changeDateRange(newValue) {
      this.dateRange = { ...this.dateRange, ...newValue };
      this.filterReport();
    },
    changeFilter(newValue) {
      this.filter[newValue.keyName] = newValue.value;
      this.filterReport();
    },
    async filterReport() {
      const loader = this.$loading.show();
      try {
        let resList = await axios.post(
          '/api/expenses-key-in/filter',
          {
            date_range: {
              startDate: new Date(this.dateRange.startDate),
              endDate: new Date(this.dateRange.endDate)
            },
            filter: this.filter
          },
          {
            headers: {
              'Content-Type': 'application/json'
            }
          }
        );
        if (resList && resList.status === 200) {
          this.expensesReportList = [
            ...resList.data.map(item => {
              return {
                ...item,
                sum_euro: calculateCurrency(
                  { sum: item.sum, currency: item.currency },
                  this.currencyData
                )
              };
            })
          ];
        }
      } catch (err) {
        this.expensesReportList = [];
      }
      loader.hide();
    }
  }
};
</script>

<style scoped lang="scss">
.report-table {
  width: 100%;
  td {
    border: 1px solid grey;
  }
}
</style>
