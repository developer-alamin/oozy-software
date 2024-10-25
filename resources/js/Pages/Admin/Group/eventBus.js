// eventBus.js
import { ref } from "vue";

const bus = {
    trashedGroupsCount: ref(0),
    updateCount(count) {
        this.trashedGroupsCount.value = count;
    },
};

export default bus;
