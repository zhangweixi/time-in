var userId = 1;

var user = {
	userId:0,
	code:'',
	token:'',
	info:null,
	openid:null,
	checkLogin:function(){
		
		if(this.userId > 0){
			return true;
		}
		
		//检查缓存中是否有
		const uinfo = uni.getStorageSync('userInfo');
		if(uinfo){
			
			this.userId = uinfo.userId;
			this.info 	= uinfo;
			this.token 	= uinfo.token;
			this.openid = uinfo.mp_openid;
			return true;
		}
		
		return false;
		
		uni.navigateTo({
			url: '/pages/login/login',
			success: res => {},
			fail: () => {},
			complete: () => {}
		});
	},
	login:function(app){
		
		var url = app.APIURL + "user/login";
		var data= {code:this.code};
		
		uni.request({
			url:url,
			data:data,
			success: (res) => {
				
				res = res.data;
				
				if(res.code == 200){
					
					var userInfo 		= res.data.userInfo;
					this.info 		= userInfo;
					this.userId 	= userInfo.user_id;
					this.openid 	= userInfo.mp_openid
					this.token 		= userInfo.token;
					
				}else if(res.code == 2003){
					
					this.openid 	= res.data.userInfo.mp_openid;
				}
			}
		})
	},
	toLogin:function(){
		setTimeout(()=>{
			
			if(this.checkLogin()){
				return true;
			}
			uni.navigateTo({url:"/pages/login/login"});
		},1000);
	}
	
}

var request = function(url,data,callback){
	data.token = this.user.token;
	console.log(this.user);
	
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
	request
}