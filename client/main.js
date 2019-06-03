import Vue from 'vue'
import App from './App'

Vue.config.productionTip = false
App.mpType = 'app'
//const host = "http://192.168.0.103:8000"
const host = "http://192.168.1.100:8000"


//const host = "https://api.launchever.cn/timein"
Vue.prototype.HOST 		= host;
Vue.prototype.APIURL 	= host + "/api/v1/";

const app = new Vue({
    ...App
})
app.$mount()
