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

  return route().current(menu.route_or_url) || menu.actives.filter(active => route().current(active)).length > 0
}

const active = childs.find(trace)
const self = getCurrentInstance()
const open = ref(active ? true : false)
</script>

<style scoped>
  .slide-fade-enter-active {
    transition: all 0.3s ease-out;
  }

  .slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(1, 0.5, 0.8, 1);
  }

  .slide-fade-enter-from,
  .slide-fade-leave-to {
    transform: translateX(-20px);
    opacity: 0;
  }
</style>

<template>
  <div class="w-full flex flex-col">
    <button
      @click.prevent="open = ! open"
      :class="`${themes().get('sidebar', 'bg-slate-700 text-gray-200')} ${open && 'dark:bg-gray-800'} pl-${padding !== 0 && padding}`"
      class="w-full p-4"
    >
      <div class="flex items-center space-x-2">
        <Icon :name="menu.icon" />
        <p class="uppercase font-semibold w-full text-left">{{ __(menu.name) }}</p>
        <Icon name="caret-left" class="transition-all ease-in-out duration-150" :class="open && '-rotate-90'" />
      </div>
    </button>

    <Transition name="slide-fade" mode="in-out">
      <div v-if="open" class="flex flex-col" ref="container">
        <slot />
      </div>
    </Transition>
  </div>
</template>