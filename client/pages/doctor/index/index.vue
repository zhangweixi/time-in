<template>
	<view>
		
		<view class="input middle" v-if="progress == 0">
			<input type="number" v-model="petCode" placeholder="请输入PET号码"/>
			<button type="primary" v-on:click="surePetCode()">确定</button>
		</view>
		
		<view class="options middle" v-if="progress == 1">
			<view v-on:click="upStep(1)">上一步</view>
			<view v-on:click="setCameraType(1)">相册选择</view>
			<view v-on:click="setCameraType(0)">相机拍摄</view>
		</view>
		
		<view class="imgs" v-if="progress==3">
			<view class="imgbox">
				<view class="imglist" v-for="(img,key) in imgs" :key="key">
					<image :src="img.src" mode="widthFix"     v-on:click="viewImg(key)" ></image>
					<view class="delbtn" @click="delimg(key);" >删除</view>
				</view>
			</view>
			<view class="handbtns">
				<view class="upstep" v-on:click="upStep(2)">上一步</view>
				<view v-if="needUploadNum == uploadedNum" class=" info" v-on:click="submitPet()">合成PDF</view>
				<view v-if="needUploadNum == uploadedNum" class=" danger" v-on:click="delimg(-1)">全部删除</view>	
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				petCode:"",
				progress:0,//0：输入PET号码，1：选择文件来源,2:显示图片
				imgs1:[{
					src:"https://api.launchever.cn/timein/storage/doctor/1/1563275359.jpg",
					id:1
				},{
					src:"https://api.launchever.cn/timein/storage/doctor/1/1563275320.jpg",
					id:2
				}],
				imgs:[],
				tempimg:'',
				cameraType:[],
				taking:false,
				needUploadNum:0,
				uploadedNum:0
			}
		},
		onLoad() {
			
		},
		methods: {
			upStep:function(step){
				this.progress = step - 1;
				uni.hideLoading();
			},
			surePetCode:function()
			{
				//检查PET号是否存在
				uni.request({
					url:this.APIURL + "doctor/checkHasPet?pid="+this.petCode,
					success:(res)=>{
						if(res.data.code == 200){
							this.progress = 1;
						}else{
							
							uni.showToast({title:res.data.msg,icon:"none"})
						}
					}
				})
			},
			setCameraType:function(type)
			{
				if(type==0){
					this.cameraType = "camera";
				}else{
					this.cameraType = "album";
				}
				this.progress = 2;
				this.takePhoto();
			},
			takePhoto:function(){
				this.taking = true;
				uni.chooseImage({
					sizeType:"original",
					sourceType:[this.cameraType],
					success:(res)=>{
						//uni.showLoading({title:"正在处理"})
						for(var file of res.tempFiles )
						{
							this.needUploadNum = this.needUploadNum+1;
							this.upload(file.path);
						}
						
						if(this.cameraType == 'camera')
						{
							this.takePhoto();	//重新调起摄像头
							console.log('yy');
						}else{
							//文件选择结束，跳转到结果界面
							this.progress = 3;
							this.taking = false;
							
							
							if(this.needUploadNum != this.uploadedNum)
							{
								uni.showLoading({title:"正在处理"});
								
							}
						}
						//uni.hideLoading();
					},
					fail:(res) => {
						this.taking = false;
						console.log(res);
						
						//有文件，显示文件
						console.log("img num"+this.imgs.length);
						if(this.imgs.length > 0 || this.needUploadNum != this.uploadedNum)
						{
							this.progress = 3;
							if(this.needUploadNum != this.uploadedNum){
								uni.showLoading({title:"正在处理"});
							}
						}else{
							
							this.progress = 1; //没有文件，添加按钮
						}
					}
				})
			},
			upload:function(filepath){
					
				uni.uploadFile({
					url:this.APIURL+"doctor/upload?pid="+this.petCode+"&sort="+this.needUploadNum,
					filePath:filepath,
					name:"img",
					success:(res)=>{
						console.log("uploaded");
						this.uploadedNum = this.uploadedNum+1;
						res = res.data;
						res = JSON.parse(res);
						console.log(res);
						var img = {id:res.data.imgId,src:res.data.img};
						this.imgs.push(img);
						
						if(this.taking == false)
						{
							this.progress = 3;
							
							if(this.needUploadNum == this.uploadedNum){
								
								uni.hideLoading();
								
							}else{
								uni.showToast({
									title:"完成一张",
									icon:"success",
									duration:1500
								})
								
								setTimeout(()=>{
									if(this.needUploadNum != this.uploadedNum)
									{
										uni.showLoading({title:'正在处理...'});
									}
								},1500);
							}
						}
					},
					fail:(res)=>{
						console.log('上传失败，重新上传');
						this.upload(filepath);
					}
				})	
			},
			delimg:function(key){
				
				uni.showModal({
					title:"确定删除吗?",
					success:(res)=>{
						
						if(res.confirm == false){
							return;
						}
						var imgs = [];
						for(var k in this.imgs){
							if(key == -1 || key == k){
								uni.request({
									method:'POST',
									url:this.APIURL+"doctor/delete_img?imgId="+this.imgs[k].id
								})
							}else{
								imgs.push(this.imgs[k]);
							}
						}
						this.imgs = imgs;
						if(imgs.length == 0){
							this.progress = 1;
						}
					}
				})
			},
			viewImg:function(key){				
				var imgs = [];
				for(var img of this.imgs){
					
					imgs.push(img.src);
				}
				uni.previewImage({
					urls:imgs,
					current:key
				})
			},
			submitPet:function(){
				uni.showLoading({title:"PDF合成中..."})
				uni.request({
					method:"POST",
					url:this.APIURL + "doctor/submitPet?pid="+this.petCode,
					success:(res)=>{
						uni.hideLoading();
						uni.navigateTo({
							url:"../pdf/pdf?pid="+this.petCode
						})
						this.imgs = [];
						this.progress = 0;
						this.uploadedNum = 0;
						this.needUploadNum = 0;
						this.petCode = "";
					}
				})
			}
		}
	}
</script>

<style lang="scss">
	.middle{
		position: fixed;
		top:40vh;
		width:100%;
	}
	
	
	.input{
		display: flex;
		height:40px;
		line-height:40px;
		
		input{
			flex:1;
			text-align: center;
			height: inherit;
			line-height: inherit;
			margin-left:20px;
			background:#eee;
			border-top-right-radius: 0;
			border-bottom-right-radius: 0;
			font-size:18px;
			letter-spacing: 5px;
		}
		button{
			width:80px;
			height: inherit;
			line-height: inherit;
			margin-right:20px;
			border-top-left-radius: 0;
			border-bottom-left-radius: 0;
		}
	}
	
	
	.options{
		display: flex;
		view{
			flex:1;
			text-align: center;
			margin:20px;
			border:1px solid #ccc;
			line-height: 2;
			border-radius: 20px;
			background:#efefef;
		}
	}
	
	.imgs{
		width:100%;
		min-height:100vh;
		z-index: 100;
		background:#444;
		overflow: hidden;
		.imgbox{
			overflow: hidden;
			width:100%;
			padding-top:10px;
			padding-bottom: 50px;
		}
		
		.imglist{
			
			float: left;
			display: block;
			width:50%;
			text-align: center;
			position: relative;
			image{
				display: block;
				width:95%;
				margin:auto;
			}
			
			view{
				position: absolute;
				right:5px;
				bottom:0px;
				color:#fff;
				font-size:10px;
				background:#999;
				padding:3px 5px;
				
			}
		}
		
		.handbtns{
			display: flex;
			margin-bottom:20px;
			view{
				color:#fff;
				flex:1;
				display: block;
				margin:5px;
				margin-top:20px;
				line-height:30px;
				text-align: center;
			}
			.upstep{
				background:#888;
			}
			.danger{
				background:red;
				
			}
			.info{
				background:#34a123;
				color:#fff;
			}
		}
		
		
	}
	.paibtns {
		display: flex;
		float: left;
		button{
			flex:1;
		}	
	}
	
	.flex{
		flex: 1;
	}
</style>
