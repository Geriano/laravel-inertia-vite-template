<script setup>
import { getCurrentInstance, onMounted, onUpdated, ref } from 'vue'
import Icon from '@/Components/Icon.vue'
import { Inertia } from '@inertiajs/inertia'

const self = getCurrentInstance()
const { menu, edit, destroy, drag, drop } = defineProps({
  menu: Object,
  edit: Function,
  destroy: Function,
  drag: Function,
  drop: Function,
  up: Function,
  down: Function,
  left: Function,
  right: Function,
})

const rounded = () => {
  const { container } = self.refs

  container?.firstElementChild?.classList.add('rounded-l-md')
  container?.lastElementChild?.classList.add('rounded-r-md')
}

onMounted(rounded)
onUpdated(rounded)
</script>

<template>
  <div class="flex items-center space-x-2 dark:bg-gray-800 rounded-md px-4 py-2" :draggable="true" :id="`menu:${menu.id}`">
    <div class="flex items-center space-x-2 w-full" :draggable="false">
      <Icon :name="menu.icon" :draggable="false" />
      <p class="uppercase" :draggable="false">{{ menu.name }}</p>
    </div>

    <div ref="container" class="flex items-center flex-none rounded-md border dark:border-gray-800" :draggable="false">
      <Icon @click.prevent="left(menu)" v-if="menu.parent_id" name="arrow-left" class="px-2 py-1 dark:bg-gray-700 dark:hover:bg-gray-800 text-white transition-all cursor-pointer" :draggable="false" />
      <Icon @click.prevent="right(menu)" v-if="menu.position > 1" name="arrow-right" class="px-2 py-1 dark:bg-gray-700 dark:hover:bg-gray-800 text-white transition-all cursor-pointer" :draggable="false" />
      <Icon @click.prevent="up(menu)" v-if="menu.position > 1" name="arrow-up" class="px-2 py-1 dark:bg-gray-700 dark:hover:bg-gray-800 text-white transition-all cursor-pointer" :draggable="false" />
      <Icon @click.prevent="down(menu)" v-if="menu.position !== menu.parent?.childs_count" name="arrow-down" class="px-2 py-1 dark:bg-gray-700 dark:hover:bg-gray-800 text-white transition-all cursor-pointer" :draggable="false" />
      <Icon @click.prevent="edit(menu)" name="edit" class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white transition-all cursor-pointer" :draggable="false" />
      <Icon @click.prevent="destroy(menu)" name="trash" class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white transition-all cursor-pointer" :draggable="false" />
    </div>
  </div>
</template>