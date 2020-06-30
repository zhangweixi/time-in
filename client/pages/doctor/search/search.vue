<template>
	<view>
		<web-view :src="url"></web-view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				url:this.HOST + "/api/v1/doctor/pets"
			}
		},
		methods: {
			
		}
	}
</script>

<style>

</style>
