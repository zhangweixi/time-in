<template>
	<view>
		<web-view :src="pdf"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				pdf:''
			}
		},
		onLoad(option) {
			console.log(option);
			this.pdf = this.HOST + "/api/v1/doctor/preview?pid=" + option.pid;
			console.log(this.pdf);
		},
		methods: {
			
		}
	}
</script>

<style>

</style>
