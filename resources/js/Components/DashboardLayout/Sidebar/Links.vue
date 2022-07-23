<script setup>
import { getCurrentInstance, onMounted, onUpdated, ref } from 'vue'
import Icon from '@/Components/Icon.vue'

const { menu, childs } = defineProps({
  menu: Object,
  childs: Array,
  padding: Number,
})

const trace = menu => {
  if (menu.childs?.length) {
    for (const child of menu.childs) {
      if (trace(child)) {
        return true
      }
    }
  }

  return route().current(menu.route_or_url)
}

const active = childs.find(trace)
const self = getCurrentInstance()
const open = ref(active ? true : false)
</script>

<template>
  <div class="w-full flex flex-col">
    <button @click.prevent="open = ! open" class="w-full p-4" :class="`${themes().get('sidebar', 'bg-slate-700 text-gray-200')} ${open && 'dark:bg-gray-800'} pl-${padding}`">
      <div class="flex items-center space-x-2">
        <Icon :name="menu.icon" />
        <p class="uppercase font-semibold w-full text-left">{{ menu.name }}</p>
        <Icon name="caret-left" class="transition-all ease-in-out duration-150" :class="open && '-rotate-90'" />
      </div>
    </button>

    <div v-if="open" class="flex flex-col" ref="container">
      <slot />
    </div>
  </div>
</template>