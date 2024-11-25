import { useCookies } from "vue3-cookies";

export function useCookie() {
  const { cookies } = useCookies();
  /**
   * Get Cookie by name
   * @param {name}
   */
  function getCookie(name: string) {
    let data = cookies.get(name); //Accept token name and return data if exits
    return data;
  }

  /**
   * Set Cookie by name
   * @param {name, data, expiration}
   * */
  function setCookie({
    name,
    data,
    expire = "",
  }: {
    name: string;
    data: string;
    expire: string;
  }): string {
    cookies.set(name, data, expire); //data, ksfskdf2445, 1MIN
    return "success";
  }

  /**
   * [updateCookie]
   * @param {name}
   * @param {data}
   * @returns void
   */
  function updateCookie(name: string, data: any) {
    let existing_cart_data = getCookie(name);
  }

  /**
   * Delete Cookie by name
   * @param {name}
   * */
  function removeCookie(name: string) {
    let remove = cookies.remove(name);
    return remove;
  }

  return {
    getCookie,
    setCookie,
    removeCookie,
    updateCookie,
  };
}
