<script setup>
import Icon from '../Icon.vue';
</script>

<template>
  <div class="flex-none w-14 h-14 p-3">
    <button @click.prevent="$emit('toggle')" type="button" class="w-full h-full rounded-md border dark:border-gray-600 text-white dark:text-gray-700 transition-all hover:scale-105 p-1">
      <Icon name="bars" />
    </button>
  </div>
</template>