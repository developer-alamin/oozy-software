// eventBus.js
import { ref } from "vue";

const bus = {
    trashedSuppliersCount: ref(0),
    updateCount(count) {
        this.trashedSuppliersCount.value = count;
    },
};

export default bus;
