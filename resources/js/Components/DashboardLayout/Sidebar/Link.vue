<script setup>
import { getCurrentInstance } from 'vue'
import { Link } from '@inertiajs/inertia-vue3'
import Icon from '@/Components/Icon.vue'

const { menu } = defineProps({
  menu: Object,
})

const active = route().current(menu.route_or_url)

if (route().has(menu.route_or_url))
  menu.route_or_url = route(menu.route_or_url)
</script>

<template>
  <Link :href="menu.route_or_url" class="w-full px-4 py-3" :class="`${themes().get('sidebar', 'bg-slate-700 text-gray-200')} ${active && 'bg-slate-800'}`">
    <div class="flex items-center space-x-2">
      <Icon :name="menu.icon" />
      <p class="uppercase font-semibold">{{ menu.name }}</p>
    </div>
  </Link>
</template>