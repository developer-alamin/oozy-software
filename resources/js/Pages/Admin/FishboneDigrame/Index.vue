<template>
  <div class="fishbone_items">
    <div class="fishbone-item" v-for="(item, index) in items" :key="index">
      <div class="cause">
        <div class="rootcause blue">Mensch</div>
        <div class="subcause">
          <div class="leftstart">
            <span class="span">P1</span>
          </div>
          <div class="stat">
            <div class="stat-text">P1</div>
            <div class="sub-stat">S1</div>
          </div>
          <div class="leftstart">
            <span class="span">P1</span>
          </div>
          <div class="stat">P1</div>
          <div class="stat">P1</div>
        </div>
      </div>

      <div class="cause">
        <div class="rootcause green">Maschine</div>
        <div class="subcause">
          <div class="stat">P1</div>
          <div class="leftstart">
            <span class="span">P1</span>
          </div>
          <div class="stat">P1</div>
        </div>
        <div class="subcause">
          <div class="leftstart">
            <span class="span">P1</span>
          </div>
          <div class="stat">P1</div>
        </div>
      </div>
      <div class="cause">
        <div class="rootcause yellow">Milieu</div>
        <div class="subcause">
          <div class="stat">P1</div>
          <div class="stat">P1</div>
          <div class="stat">P1</div>
          <div class="stat">P1</div>
          <div class="stat">P1</div>
        </div>
      </div>

      <div class="line"></div>
      <div class="cause">
        <div class="subcause">
          <div class="stat">P1</div>
          <div class="stat">P1</div>
          <div class="stat">P1</div>
        </div>
        <div class="rootcause blue">Messung</div>
      </div>
      <div class="cause">
        <div class="subcause">
          <div class="leftstart">
            <span class="span">P1</span>
          </div>
          <div class="stat">P1</div>
          <div class="stat">P1</div>
        </div>
        <div class="rootcause green">Material</div>
      </div>
      <div class="cause">
        <div class="subcause">
          <div class="leftstart">
            <span class="span">P1</span>
          </div>
          <div class="stat">P1</div>
          <div class="stat">P1</div>
        </div>
        <div class="rootcause yellow">Methoden</div>
      </div>
      <div class="defect">
        <v-autocomplete
          v-model="category.company_id"
          :items="companies"
          item-value="id"
          item-title="name"
          outlined
          clearable
          density="comfortable"
          @update:search="fetchCompanies"
        ></v-autocomplete>
        <v-btn
          @click="createFishbone"
          color="primary"
          icon
          style="width: 40px; height: 40px"
        >
          <v-tooltip location="top" activator="parent">
            <template v-slot:activator="{ props }">
              <v-icon v-bind="props" style="font-size: 20px">mdi-plus</v-icon>
            </template>
            <span>Add New Problem</span>
          </v-tooltip>
        </v-btn>
        
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      items: [1], // Array to hold fishbone items
      category: {
        company_id: null, // Default to null for better flexibility
        status: null,
        description: "",
      },
      companies: [], // Holds fetched companies
    };
  },
  methods: {
    async fetchData() {
      try {
        const response = await this.$axios.get("/fishbone-digrame");
        this.companies = response.data || [];
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    },
    async createFishbone() {
     this.items.push(1);
      
    },
  },
  created() {
    this.fetchData(); // Fetch initial data when component is created
  },
};
</script>

<style scoped>
.fishbone-item {
      display: inline-grid;
      grid-template-columns: repeat(4, auto);
      grid-template-rows: auto .2em auto;
      padding-left: 2em;
      font-family: Arial;
      --bone-color: #85A0B2;
      --yellow: #FDBE22;
      --green: #69E982;
      --blue: #5CB2FB;

      .cause {
        display: flex;
        flex-direction: column;
        transform: skew(20deg);
        transform-origin: bottom;
        margin-left: 150px;
      }

      .rootcause {
        text-align: center;
        position: relative;
        left: 100%;
        transform: translateX(-50%)  skewX(-20deg);
        font-size: 15px;
        color: #fff;
        border-radius: .2em;
        
        &.yellow {
          background-color: var(--yellow);
        }
        
        &.green {
          background-color: var(--green);
        }
        
        &.blue {
          background-color: var(--blue);
        }
      }

      .subcause {
        flex-grow: 1;
        border-right: .2em solid var(--bone-color);
        padding-bottom: .75em;
        padding-top: .75em
      }

      .stat {
        text-align: right;
        padding-right: 3em;
        position: relative;
        transform: skewX(-20deg);
        line-height: 1.5em;
        font-size: 14px;
      }

      .stat:before {
        content: '';
        display: block;
        background-color: var(--bone-color);
        position: absolute;
        width: 3em;
        height: .2em;
        right: 0;
        top: 50%;
        transform: translate(.2em, -50%);
      }

      .line {
        grid-column-start: 1;
        grid-column-end: 4;
        background-color: var(--bone-color);

        ~ .cause {
          transform: skewX(-20deg);
          transform-origin: top;
        }
        
        ~ .cause .rootcause {
          transform: translateX(-50%)  skewX(20deg);
        }

        ~ .cause .stat {
          transform: skewX(20deg);
        }
      }

      .defect-spacer-top {
        grid-column-start: 4;
        grid-column-end: 4;
        grid-row-start: 1;
        grid-row-end: 2;
      }

      .defect {
        grid-column-start: 4;
        grid-column-end: 4;
        grid-row-start: 2;
        grid-row-end: 3;
        display: flex;
        align-items: center;
        margin-left: 100px;
      }

      .defect-spacer-bottom {
        grid-column-start: 4;
        grid-column-end: 4;
        grid-row-start: 3;
        grid-row-end: 4;
      }

      .defect-text {
        padding: 10px 10px 10px 10px;
        margin-left: 8.5em;
        background-color: var(--bone-color);
        border-radius: .5em;
        color: #fff;
        text-align: center;
      }
      .leftstart {
          text-align: right;
          padding-right: 19px;
          position: relative;
          transform: skewX(356deg);
          line-height: 1em;
      }

      .leftstart::before {
          content: '';
          display: block;
          background-color: var(--bone-color);
          position: absolute;
          width: 1em;
          height: .2em;
          right: 0;
          top: 50%;
          transform: translate(.2em, -50%);
      }

      span.span {
          font-size: 12px;
      }
    }
.v-input--horizontal{
  grid-template-areas:none;
}


</style>