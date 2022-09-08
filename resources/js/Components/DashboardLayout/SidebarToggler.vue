<script setup>
import Icon from '../Icon.vue';
</script>

<template>
  <div class="flex-none w-14 h-14">
    <button @click.prevent="$emit('toggle')" type="button" class="w-full h-full text-gray-700 transition-all p-1">
      <Icon name="bars" />
    </button>
  </div>
</template>