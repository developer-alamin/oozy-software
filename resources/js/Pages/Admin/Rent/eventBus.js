// eventBus.js
import { ref } from "vue";

const bus = {
    trashedBrandsCount: ref(0),
    updateCount(count) {
        this.trashedBrandsCount.value = count;
    },
};

export default bus;
