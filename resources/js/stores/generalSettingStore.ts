import { defineStore } from "pinia";

export const useGeneralSettingStore = defineStore<"generalSetting", GeneralSettingState>({
  id: "generalSetting",
  state: (): GeneralSettingState => ({
    setting: {},
  }),
  getters: {
    getSettingByKey: (state) => {
      return (key: string) => state.setting[key]
    },
    getSetting: (state) => {
      return state
    }
  },
    actions: {
      setSetting(setting: {}) {
        this.setting = setting
      },

      setSettingItem(key: string, value: string) {
        this.setting[key] = value
      }
    },
});
