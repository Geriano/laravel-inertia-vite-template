<script setup>
const { error } = defineProps({
  error: String,
})
</script>

<template>
  <Transition
    enterActiveClass="transition-all duration-150"
    leaveActiveClass="transition-all duration-150"
    enterFromClass="opacity-0 -scale-y-100"
    leaveToClass="opacity-0 -scale-y-100">
      <div v-if="error" class="text-right text-sm text-red-500">{{ error }}</div>
  </Transition>
</template>