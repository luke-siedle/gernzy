import { shallowMount, mount, createLocalVue } from "@vue/test-utils"
import VueRouter from "vue-router"
import createRouter from '../../src/router';
import App from '../../src/components/App.vue';
import Login from "@/components/Login.vue"

const localVue = createLocalVue()
localVue.use(VueRouter)

describe("Router", () => {
  test("should mount the router and see login", () => {
    const router = createRouter();
    const wrapper = mount(App, { localVue, router })
    router.push('/login');
    expect(wrapper.find(Login).exists()).toBe(true)

  });
});
