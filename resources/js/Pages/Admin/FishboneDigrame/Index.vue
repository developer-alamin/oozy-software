<template>
  <div class="loadding_warrper" v-if="lodding">
    <span class="loader"></span>
  </div>

  <v-card>
      <v-card-title class="pt-5">
        <v-row class="align-items-center">
          <v-col cols="4" class="text-center">
            <v-autocomplete
                  v-model="problem"
                  :items="problems"
                  item-value="id"
                  item-title="name"
                  class="refferClass"
                  outlined
                  clearable
                  density="comfortable"
                  @update:model-value="handelProblem"
                  @update:search="problemList"
                >
                <template v-slot:label>
                  Select Problem 
                </template>
              </v-autocomplete>
          </v-col>
        </v-row>
      </v-card-title>
    </v-card>



  <div class="fishbone_items pt-5">
    <div class="fishbone-item" v-for="(item, index) in items" :key="index">
      <!-- Causes before the line -->
      <div
        class="cause"
        v-for="(category, catIndex) in item.fishbone_categories.filter((_, i) => i % 2 !== 0)"
        :key="`before-line-${catIndex}`"
      >
        <div class="rootcause green">{{ category.name }}</div>
        <div class="subcause">
          <div
            class="stat"
            v-for="(cause, causeIndex) in category.causes"
            :key="causeIndex"
          >
            {{ cause.name }}
          </div>
        </div>
      </div>

      <!-- Line -->
      <div class="line"></div>

      <!-- Causes after the line -->
      <div
        class="cause"
        v-for="(category, catIndex) in item.fishbone_categories.filter((_, i) => i % 2 == 0)"
        :key="`after-line-${catIndex}`"
      >
        <div class="subcause">
          <div
            class="stat"
            v-for="(cause, causeIndex) in category.causes"
            :key="causeIndex"
          >
            {{ cause.name }}
          </div>
        </div>
        <div class="rootcause blue">{{ category.name }}</div>
      </div>

      <!-- Defect -->
      <div class="defect">
        {{ item.name }}
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      search:'',
      items: [], 
      fishbone: {
        problems: [], 
      },
      problem:null,
      problems: [], 

      limit:5,
      lodding:false,
    };
  },
  mounted() {
    
  },
  methods: {
    handelProblem(value){
      this.fetchProblems('',this.problem)
    },
    async problemList(search){
      try {
        const response = await this.$axios.get("/get_problem_list",{
          params:{
            search:search || ''
          }
        });
       this.problems = response.data || [''];
        
      } catch (error) {
        console.log(error);
        
      }
    },
    async fetchProblems(search,problem) {
      try {
        const response = await this.$axios.get("/fishbone-digrame",{
          params:{
            problem:problem || ''
          }
        });
        if (Array.isArray(response.data)) {
          this.items = response.data; 
          
        } 
        console.log(response.data);
      } catch (error) {
        console.error("Error fetching data:", error);
      } 
    }, 
  },
  created() {
    this.problemList();
    this.fetchProblems(); // Fetch initial data when component is created
  },
};
</script>

<style scoped>

.fishbone-item:not(:first-of-type) {
    padding-top: 100px;
}
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