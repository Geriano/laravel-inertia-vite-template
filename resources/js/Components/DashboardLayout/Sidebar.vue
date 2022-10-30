<script setup>
import { getCurrentInstance, onMounted, onUpdated, ref } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/inertia-vue3'
import Builder from './Sidebar/Builder.vue'
import Icon from '@/Components/Icon.vue'
import Swal from 'sweetalert2'
import { Inertia } from '@inertiajs/inertia'

const { open } = defineProps(['open'])
const menus = ref(usePage().props.value.$menus || [])
const { user } = usePage().props.value

const f = async () => {
  try {
    const response = await axios.get(route('user.menu', user.id))

    menus.value = response.data
  } catch (e) {
    const response = await Swal.fire({
      title: 'Do you want to try again?',
      text: `${e}`,
      icon: 'error',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && fetch()
  }
}
</script>

<style scoped>
.op-enter-active, .op-leave-active {
  transition: all 50ms ease-in-out;
  opacity: 1;
}

.op-enter-from, .op-leave-to {
  opacity: 0;
}
</style>

<template>
  <div class="flex flex-col w-full h-full bg-inherit overflow-auto">
    <transition name="op" mode="in-out">
      <Builder v-if="menus.length" :menus="menus" :open="open" />
    </transition>
  </div>

  <div class="hidden pl-8"></div>
</template>