<script setup>
import { getCurrentInstance, ref } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/inertia-vue3'
import Builder from './Sidebar/Builder.vue'
import Icon from '@/Components/Icon.vue'

const self = getCurrentInstance()
const menus = ref([])
const { user } = usePage().props.value

const fetch = async () => {
  try {
    const response = await axios.get(route('api.v1.user.menu', user.id))
    return menus.value = response.data
  } catch (e) {
    setTimeout(fetch, 1000)
  }
}

fetch()
</script>

<template>
  <div class="flex flex-col w-full h-full bg-inherit">
    <Builder v-if="menus.length" :menus="menus" />
  </div>

  <div class="hidden pl-8"></div>
</template>