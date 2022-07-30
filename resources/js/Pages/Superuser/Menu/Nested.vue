<script setup>
import { getCurrentInstance, onMounted, onUpdated, ref } from 'vue'
import Dragable from 'vuedraggable'
import Icon from '@/Components/Icon.vue'

const self = getCurrentInstance()
const { menus, edit, destroy, save } = defineProps({
  menus: Array,
  save: Function,
  edit: Function,
  destroy: Function,
})
</script>

<template>
  <Dragable
    tag="ul"
    :list="menus"
    :group="{ name: 'g1' }"
    item-key="id"
    @change="save">
    <template #item="{ element }">
      <div class="flex flex-col space-y-1">
        <div class="flex items-center space-x-2 bg-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:hover:bg-gray-900 rounded-md px-4 py-2 transition-all cursor-move">
          <div class="flex items-center space-x-2 w-full">
            <Icon :name="element.icon" />
            <p class="uppercase">{{ element.name }}</p>
          </div>

          <div ref="container" class="flex-none flex items-center rounded-md space-x-1">
            <Icon v-if="can('update menu')" @click.prevent="edit(element)" name="edit" class="bg-blue-600 hover:bg-blue-700 px-2 py-1 rounded-md text-sm text-white transition-all cursor-pointer" />
            <Icon v-if="can('delete menu') && element.deleteable" @click.prevent="destroy(element)" name="trash" class="bg-red-600 hover:bg-red-700 px-2 py-1 rounded-md text-sm text-white transition-all cursor-pointer" />
          </div>
        </div>

        <Nested :menus="element.childs" :edit="edit" :destroy="destroy" :save="save" class="ml-8" />
      </div>
    </template>
  </Dragable>
</template>