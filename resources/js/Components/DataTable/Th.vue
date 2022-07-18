<script setup>
import { getCurrentInstance, onMounted, ref } from 'vue'
import Icon from '@/Components/Icon.vue'

const self = getCurrentInstance()
const { table, sort, name } = defineProps({
  table: Object,
  sort: Boolean,
  name: String,
  class: String,
})

const current = ref(table && table.config && name === table.config.order.key)
const asc = ref(current.value && table.config.order.dir === 'asc')
const desc = ref(current.value && table.config.order.dir === 'desc')

const click = () => {
  if (current.value) {
    const dir = table.config.order.dir = table.config.order.dir === 'asc' ? 'desc' : 'asc'
    asc.value = dir === 'asc'
    desc.value = dir === 'desc'
  } else {
    table.config.order.key = name
    table.config.order.dir = 'asc'
    current.value = true
    asc.value = true
    desc.value = false
  }

  table.refresh()
}
</script>

<template>
  <th ref="th" class="relative uppercase font-semibold" :class="`${table?.config?.sticky ? 'sticky top-0 left-0 border-0' : 'border-1'} ${sort && 'cursor-pointer'} ${$props.class}`" @click.prevent="sort && table && click()">
    <div class="flex items-center justify-center space-x-2">
      <div>
        <slot />
      </div>

      <template v-if="table && sort">
        <div class="flex flex-col items-center justify-center text-xs">
          <Icon name="caret-up" class="transition-all duration-300" :class="asc ? 'dark:text-white' : 'dark:text-gray-400'" />
          <Icon name="caret-down" class="transition-all duration-300" :class="desc ? 'dark:text-white' : 'dark:text-gray-400'" />
        </div>
      </template>
    </div>
  </th>
</template>