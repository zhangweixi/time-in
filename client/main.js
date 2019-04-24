import Vue from 'vue'
import App from './App'

Vue.config.productionTip = false
App.mpType = 'app'
const host = "http://192.168.1.100:80"
//const host = "http://localhost:8000"
Vue.prototype.HOST 		= host;
Vue.prototype.APIURL 	= host + "/api/v1/";

const app = new Vue({
    ...App
})
app.$mount()
