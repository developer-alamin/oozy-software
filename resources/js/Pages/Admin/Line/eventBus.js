// eventBus.js
import { ref } from "vue";

const bus = {
    trashedLinesCount: ref(0),
    updateCount(count) {
        this.trashedLinesCount.value = count;
    },
};

export default bus;
