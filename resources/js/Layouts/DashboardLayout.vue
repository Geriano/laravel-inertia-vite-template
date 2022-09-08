<script setup>
import { getCurrentInstance, onMounted, onUnmounted, ref, watch } from 'vue'
import { Head, usePage } from '@inertiajs/inertia-vue3'
import Toggler from '@/Components/DashboardLayout/SidebarToggler.vue'
import TopbarDropdown from '@/Components/DashboardLayout/TopbarDropdown.vue'
import Sidebar from '@/Components/DashboardLayout/Sidebar.vue';

const { title } = defineProps({
  title: String,
})

const { $config } = usePage().props.value
const open = ref(Boolean(
  Number(localStorage.getItem('sidebar_open'))
))

watch(open, () => {
  localStorage.setItem('sidebar_open', Number(open.value))
})

const q = e => {
  if (e.key === 'q' && ! (e.target instanceof HTMLInputElement)) {
    open.value = ! open.value
  }
}

onMounted(() => document.addEventListener('keyup', q))
onUnmounted(() => document.removeEventListener('keyup', q))
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: all 300ms ease-in-out;
  opacity: 1;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.max-h-sidebar {
  max-height: calc(100vh - 3.5rem);
}
</style>

<template>
  <div class="bg-gray-300 dark:bg-gray-900 font-sans w-full h-full min-h-screen">
    <div class="sticky top-0 left-0 z-20 flex-none flex justify-between w-full h-14" :class="themes().get('topbar', 'bg-cyan-500 text-gray-700 hover:bg-cyan-600 hover:text-gray-800 transition-all ease-in-out duration-150').replace(/hover:(bg|text)-(.*?)-(\d+)/, '')">
      <div class="flex items-center justify-between w-14 md:w-64 pl-0 md:pl-6">
        <h1 class="text-2xl font-bold hidden sm:inline">
          {{ $config.app.name }}
        </h1>

        <Toggler @toggle="open = ! open" />
      </div>

      <div class="md:hidden w-full flex items-center justify-center">
        <h1 class="text-center text-2xl font-semibold">
          {{ $config.app.name }}
        </h1>
      </div>

      <TopbarDropdown />
    </div>

    <div
      class="transition-all duration-300"
      :class="{
        'pl-14 md:pl-64': open,
        'pl-14': !open,
      }"
    >
      <main class="p-6">
        <slot />
      </main>
    </div>

    <div
      class="fixed top-14 left-0 transition-all duration-300 h-screen max-h-sidebar z-20 flex flex-col bg-gray-700"
      :class="{
        'w-64': open,
        'w-14': !open,
      }"
    >
      <Sidebar :open="open" />
    </div>
  </div>

  <div class="hidden bg-gray-300 dark:bg-gray-900 font-sans w-full h-full">
    <Head :title="title" />

    <div ref="sidebar" class="fixed sm:static top-0 left-0 flex-none flex flex-col h-full min-h-screen transition-all ease-in-out duration-300 z-20" :class="`${themes().get('sidebar', 'bg-gray-700 text-gray-200 hover:bg-gray-800 hover:text-gray-100 transition-all ease-in-out duration-100').replace(/hover:(bg|text)-(.*?)-(\d+)/)} ${open ? 'w-full sm:w-60' : 'w-0'}`">
      <div v-if="open" class="flex-none flex items-center justify-between w-full h-14 px-2" :class="themes().get('topbar', 'bg-cyan-500 text-gray-700 hover:bg-cyan-600 hover:text-gray-800 transition-all ease-in-out duration-150').replace(/hover:(bg|text)-(.*?)-(\d+)/, '')">
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