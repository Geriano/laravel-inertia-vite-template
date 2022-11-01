<script setup>
import Button from '../Button.vue'

</script>

<template>
  <Button class="bg-blue-600 hover:bg-blue-700 active:bg-blue-800">
    <slot />
  </Button>
</template>