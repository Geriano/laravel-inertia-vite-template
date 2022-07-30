<script setup>
import Button from '../Button.vue'
</script>

<template>
  <Button class="bg-gray-800 hover:bg-gray-600 active:bg-gray-800">
    <slot />
  </Button>
</template>