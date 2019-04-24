var userId = 1;

var user = {
	userId:0
}
var token = "xxx";

var request = function(url,data,callback){
	data.token = this.token;
	uni.request({
		method:'GET',
		url:url,
		data:data,
		success: (res) => {
			callback(res)
		}
	})
}
export default{
	user,
	userId,
	request,
	token
}