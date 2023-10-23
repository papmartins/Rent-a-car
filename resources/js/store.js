import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const store = new Vuex.Store({
  state: {
      item: {},
      transaction: { status: '', message: '', data: '' }
  }
});

export default store;