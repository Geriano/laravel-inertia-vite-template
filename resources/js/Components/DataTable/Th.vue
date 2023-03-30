<script setup>
import { ref } from 'vue'
import Icon from '@/Components/Icon.vue'

const { table, sort, name } = defineProps({
  table: Object,
  sort: Boolean,
  name: String,
  class: String,
})

const asc = ref(table.config.order.dir === 'asc')
const desc = ref(table.config.order.dir === 'desc')

const click = () => {
  table.config.order.key = name
  
  if (table.config.order.dir === 'asc') {
    table.config.order.dir = 'desc'
    asc.value = false
    desc.value = true
  } else {
    table.config.order.dir = 'asc'
    asc.value = true
    desc.value = false
  }

  table.refresh()
}
</script>

<template>
  <th ref="th" class="relative uppercase font-semibold bg-inherit text-inherit" :class="`${table?.config?.sticky ? 'sticky top-0 left-0 border-0' : 'border-1'} ${sort && 'cursor-pointer'} ${$props.class}`" @click.prevent="sort && table && click()">
    <div class="flex items-center justify-center space-x-2">
      <div>
        <slot />
      </div>

      <template v-if="table && sort">
        <div class="flex flex-col items-center justify-center text-xs">
          <Icon name="caret-up" class="transition-all duration-300" :class="table.config.order.key === name && table.config.order.dir === 'asc' && asc ? 'text-black dark:text-white' : ' dark:text-gray-400 text-gray-400'" />
          <Icon name="caret-down" class="transition-all duration-300" :class="table.config.order.key === name && table.config.order.dir === 'desc' && desc ? 'text-black dark:text-white' : 'dark:text-gray-400 text-gray-400'" />
        </div>
      </template>
    </div>

    <div class="absolute top-0 left-0 w-full h-full outline outline-1 outline-gray-300 dark:outline-gray-900 bg-inherit text-inherit flex items-center justify-center">
      <div class="flex items-center justify-center space-x-2">
        <div>
          <slot />
        </div>

        <template v-if="table && sort">
          <div class="flex flex-col items-center justify-center text-xs">
            <Icon name="caret-up" class="transition-all duration-300" :class="table.config.order.key === name && table.config.order.dir === 'asc' && asc ? 'text-black dark:text-white' : ' dark:text-gray-400 text-gray-400'" />
            <Icon name="caret-down" class="transition-all duration-300" :class="table.config.order.key === name && table.config.order.dir === 'desc' && desc ? 'text-black dark:text-white' : 'dark:text-gray-400 text-gray-400'" />
          </div>
        </template>
      </div>
    </div>
  </th>
</template>