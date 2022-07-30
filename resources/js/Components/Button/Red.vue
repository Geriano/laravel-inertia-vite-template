<script setup>
import Button from '../Button.vue'
</script>

<template>
  <Button class="bg-red-600 hover:bg-red-700 active:bg-red-800">
    <slot />
  </Button>
</template>