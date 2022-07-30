<script setup>
import { getCurrentInstance, ref, Teleport } from 'vue';

const { show } = defineProps({
  show: Boolean,
})
</script>

<template>
  <Teleport to="body">
    <transition
      enterActiveClass="transition-all duration-200"
      leaveActiveClass="transition-all duration-50"
      enterFromClass="-translate-y-full opacity-0"
      leaveToClass="-translate-y-full opacity-0">
      <div v-if="show" class="fixed top-0 left-0 w-full h-full min-h-screen flex justify-center backdrop-blur p-10 z-20">
        <slot />
      </div>
    </transition>
  </Teleport>
</template>