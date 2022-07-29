<script setup>
import { getCurrentInstance, onMounted, ref } from 'vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'
import Toggler from '@/Components/DashboardLayout/SidebarToggler.vue'
import TopbarDropdown from '@/Components/DashboardLayout/TopbarDropdown.vue'
import Sidebar from '@/Components/DashboardLayout/Sidebar.vue';

const { title } = defineProps({
  title: String,
})

const self = getCurrentInstance()
const open = ref(window.innerWidth > 669)

onMounted(() => window.addEventListener('resize', () => open.value = window.innerWidth > 640))

const { user } = usePage().props.value
Echo.private(`App.Models.User.${user.id}`)
    .notification(notification => {
      console.log(notification)
    })
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: all 300ms ease-in-out;
  opacity: 1;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>

<template>
  <div class="flex bg-gray-300 dark:bg-gray-900 font-sans w-full h-full">
    <Head :title="title" />

    <div ref="sidebar" class="fixed sm:static top-0 left-0 flex-none flex flex-col h-full min-h-screen transition-all ease-in-out duration-300 z-20" :class="`${themes().get('sidebar', 'bg-gray-700 text-gray-200 hover:bg-gray-800 hover:text-gray-100 transition-all ease-in-out duration-100').replace(/hover:(bg|text)-(.*?)-(\d+)/)} ${open ? 'w-full sm:w-60' : 'w-0'}`">
      <div v-if="open" class="sticky top-0 left-0 flex-none flex items-center justify-between w-full h-14 px-2" :class="themes().get('topbar', 'bg-cyan-500 text-gray-700 hover:bg-cyan-600 hover:text-gray-800 transition-all ease-in-out duration-150').replace(/hover:(bg|text)-(.*?)-(\d+)/, '')">
        <Toggler @toggle="open = ! open" class="sm:hidden" />

        <h1 class="text-2xl text-center font-bold w-full">Template</h1>

        <div class="flex-none sm:hidden">
          <TopbarDropdown />
        </div>
      </div>

      <Sidebar v-if="open" />
    </div>

    <div class="relative w-full h-full min-h-screen max-h-screen overflow-auto">
      <div class="sticky top-0 left-0 z-20 flex-none flex justify-between w-full h-14 px-2" :class="themes().get('topbar', 'bg-cyan-500 text-gray-700 hover:bg-cyan-600 hover:text-gray-800 transition-all ease-in-out duration-150').replace(/hover:(bg|text)-(.*?)-(\d+)/, '')">
        <Toggler @toggle="open = ! open" />

        <TopbarDropdown />
      </div>

      <main class="p-6">
        <slot />
      </main>
    </div>
  </div>
</template>