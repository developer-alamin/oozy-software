<template>
  <div class="loadding_warrper" v-if="lodding">
    <span class="loader"></span>
  </div>
  <div class="fishbone_items">
    <div class="fishbone-item  pb-5" v-for="(item, index) in items" :key="index">
    
      <div class="cause" 
     v-for="(category, index) in item?.fishbone_categories.filter((_, i) => i % 2 === 0)" 
     :key="`group1-${index}`">
        <div class="rootcause blue">{{ category.name }}</div>
        <div class="subcause">
          <div class="stat">p1</div>
          <div class="stat">P2</div>
          <div class="stat">P3</div>
        </div>
      </div>

      <div class="line"></div>
      <div class="cause" 
     v-for="(category, index) in item?.fishbone_categories.filter((_, i) => i % 2 !== 0)" 
     :key="`group2-${index}`">
        <div class="subcause">
          <div class="stat">P1</div>
          <div class="stat">P2</div>
          <div class="stat">P3</div>
        </div>
        <div class="rootcause blue">{{ category.name }}</div>
      </div>


      <div class="defect">
        <v-autocomplete
          v-model="item.id"
          :items="problems"
          item-value="id"
          item-title="name"
          outlined
          clearable
          density="comfortable"
          @update:model-value="fetchCategories($event, index)"
          @update:search="(search) => fetchProblems(search, index)"
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
        <v-btn
          v-if="index !== 0 && items.length > 1"
          color="red"
          icon
          style="width: 40px; height: 40px"
          @click="deleteRow(index)"
        >
          <v-tooltip location="top" activator="parent">
            <template v-slot:activator="{ props }">
              <v-icon v-bind="props" style="font-size: 20px">mdi-delete</v-icon>
            </template>
            <span>Delete Problem</span>
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
      items: [], 
      fishbone: {
        problems: [], 
      },
      problems: [], 
      lodding:false,
    };
  },
  methods: {
    async fetchProblems(search, index) {
      try {
        
        const response = await this.$axios.get("/fishbone-digrame", {
          params: { search: this.search },  // Use the provided search term
        });
        // Ensure `items` is not empty initially
        if (this.items.length === 0 && response.data.length > 0) {
          // Push the first item to `items` when it's the first time data is loaded
          this.items.push(response.data[0]);
        }
        // Update problems list for the autocomplete dropdown
        this.problems = response.data || [];
      } catch (error) {
        console.error("Error fetching data:", error);
      } 
    },
    async fetchCategories(problem, index) {

      if (!problem) {
        return;
      }
      try {
        this.lodding = true;
        const response = await this.$axios.get(`/problemby/${problem}/fishbone-category`);
        if (response.data) {
          this.items[index] = response.data;
        }else{
          this.items.push(Object.assign({}, this.items[0])); // Copy the first item
        }
        this.lodding = false;
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    },
    async createFishbone() {
      this.items.push(Object.assign({}, this.items[0])); // Copy the first item
    },  
    async deleteRow(index) {
      this.items.splice(index, 1); // Delete a specific row
    },
  },
  created() {
    this.fetchProblems(); // Fetch initial data when component is created
  },
};
</script>

<style scoped>

.loader {
  width: 67px;
    height: 67px;
    border-radius: 50%;
    display: inline-block;
    position: relative;
    background: linear-gradient(0deg, rgb(255 255 255 / 0%) 33%, #06f036 100%);
    box-sizing: border-box;
    animation: rotation-4ef4078a 1s linear infinite;

}
.loader::after {
  content: '';
    box-sizing: border-box;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: #5d518c;
}
@keyframes rotation {
  0% { transform: rotate(0deg) }
  100% { transform: rotate(360deg)}
} 
      .loadding_warrper {
    position: absolute;
    width: 100%;
    height: 100%;
    background: #ffffff;
    right: 0;
    left: 0;
    z-index: 11111;
    top: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0.6;
}
   

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