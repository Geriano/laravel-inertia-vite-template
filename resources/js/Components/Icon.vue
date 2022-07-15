<script setup>
const { type, name } = defineProps({
  type: {
    type: String,
    default: 'fa',
  },
  name: {
    type: String,
    default: 'circle',
  },
})
</script>

<template>
  <i :class="`${type} fa-${name}`"></i>
</template>