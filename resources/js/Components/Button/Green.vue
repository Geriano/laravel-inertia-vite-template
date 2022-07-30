<script setup>
import Button from '../Button.vue'
</script>

<template>
  <Button class="bg-green-600 hover:bg-green-700 active:bg-green-800">
    <slot />
  </Button>
</template>