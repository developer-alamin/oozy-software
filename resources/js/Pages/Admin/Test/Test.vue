<template>
  <div class="calendar-container">
    <h3>My Test Page</h3>
    <div class="calendar-header">
      <button @click="changeMonth(-1)">Previous</button>
      <h3>{{ currentMonthName }} {{ currentYear }}</h3>
      <button @click="changeMonth(1)">Next</button>
    </div>
    <table>
      <thead>
        <tr>
          <th>Sun</th>
          <th>Mon</th>
          <th>Tue</th>
          <th>Wed</th>
          <th>Thu</th>
          <th>Fri</th>
          <th>Sat</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(week, weekIndex) in calendarDays" :key="weekIndex">
          <td v-for="(day, dayIndex) in week" :key="dayIndex"
              :class="['date-cell', { 
                'empty': !day, 
                'selected': day && isSelected(day),
                'active': day && isActiveDate(day),
                'disabled': day && !isPreselectedDate(day)
              }]"
              @click="selectDate(day)"
              :disabled="!isPreselectedDate(day)">
            <span v-if="day">{{ day }}</span>
            <div v-if="day && getEventCountForDay(day) > 0" class="value-badge-count">
              {{ getEventCountForDay(day) }}
            </div>
          </td>
        </tr>
      </tbody>
    </table>
    <div id="selectedDate">{{ selectedDateMessage }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      currentDate: new Date(),
      preselectedData: {
        "2025-01-30": 3,
        "2025-02-01": 55,
        "2025-02-14": 1,
        "2025-03-05": 2,
        "2025-03-18": 3
      },
      selectedDate: null
    };
  },
  computed: {
    currentYear() {
      return this.currentDate.getFullYear();
    },
    currentMonth() {
      return this.currentDate.getMonth();
    },
    currentMonthName() {
      return this.currentDate.toLocaleString('default', { month: 'long' });
    },
    calendarDays() {
      const startDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
      const daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
      const weeks = [];
      let week = [];
      let dayCount = 1;

      for (let i = 0; i < 6; i++) {
        week = [];
        for (let j = 0; j < 7; j++) {
          if (i === 0 && j < startDay || dayCount > daysInMonth) {
            week.push(null);
          } else {
            week.push(dayCount);
            dayCount++;
          }
        }
        weeks.push(week);
        if (dayCount > daysInMonth) break;
      }
      return weeks;
    },
    selectedDateMessage() {
      if (!this.selectedDate) {
        return 'Click a preselected date to see details here.';
      }
      const formattedDate = this.formatDate(this.selectedDate);
      const eventsCount = this.getEventCountForDay(this.selectedDate);
      return `Selected Date: ${formattedDate} - Events Count: ${eventsCount}`;
    }
  },
  methods: {
    changeMonth(direction) {
      this.currentDate = new Date(this.currentYear, this.currentMonth + direction, 1);
    },
    formatDate(day) {
      const date = new Date(this.currentYear, this.currentMonth, day);
      return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    },
    selectDate(day) {
      if (!day || !this.isPreselectedDate(day)) return;
      this.selectedDate = day;
    },
    isSelected(day) {
      return this.selectedDate === day;
    },
    isActiveDate(day) {
      const formattedDate = this.formatDate(day);
      return this.preselectedData[formattedDate] !== undefined;
    },
    isPreselectedDate(day) {
      const formattedDate = this.formatDate(day);
      return this.preselectedData[formattedDate] !== undefined;
    },
    getEventCountForDay(day) {
      const formattedDate = this.formatDate(day);
      return this.preselectedData[formattedDate] || 0;
    }
  }
};
</script>

<style scoped>
.calendar-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.calendar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  max-width: 800px;
  margin-bottom: 20px;
}
.calendar-header button {
  padding: 10px 20px;
  font-size: 16px;
  cursor: pointer;
  background-color: #4caf50;
  color: white;
  border: none;
  border-radius: 5px;
}
.calendar-header h3 {
  margin: 0;
  font-size: 20px;
}
table {
  border-collapse: collapse;
  width: 100%;
  max-width: 800px;
}
th, td {
  border: 1px solid #ddd;
  padding: 15px;
  text-align: center;
  cursor: pointer;
  position: relative;
}
th {
  background-color: #f4f4f4;
}
td {
  height: 80px;
}
td.selected {
  background-color: #4caf50;
  color: white;
  font-weight: bold;
}
td.active {
  background-color: #ff9800;
  color: white;
  font-weight: bold;
}
td:hover {
  background-color: #e0f7fa;
}
td.disabled {
  background-color: #f9f9f9;
  cursor: not-allowed;
}
.empty {
  background-color: #f9f9f9;
  cursor: default;
}
.value-badge-count {
  position: absolute;
  bottom: 5px;
  right: 5px;
  background-color: #ff9800;
  color: white;
  padding: 3px 8px;
  border-radius: 5px;
  font-size: 12px;
}
#selectedDate {
  margin-top: 20px;
  font-size: 18px;
  color: #333;
}
</style>
