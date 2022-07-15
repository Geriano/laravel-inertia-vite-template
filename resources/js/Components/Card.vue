<script setup>
import { getCurrentInstance, onMounted } from 'vue'
import { normalizeClass } from 'vue';

const self = getCurrentInstance()
defineProps({ 'class': String })

onMounted(() => {
  const { header, body, footer } = self.refs

  header?.firstElementChild?.classList.add('rounded-t-md')
  footer?.firstElementChild?.classList.add('rounded-b-md')

  if (!header) body?.firstElementChild?.classList.add('rounded-t-md')
  if (!footer) body?.firstElementChild?.classList.add('rounded-b-md')
})
</script>

<template>
  <div class="flex flex-col rounded-md" :class="$props.class">
    <div ref="header" v-if="self.slots.header" class="flex flex-col bg-inherit rounded-t-md">
      <slot name="header" />
    </div>

    <div ref="body" v-if="self.slots.body" class="flex flex-col bg-inherit rounded-md">
      <slot name="body" />
    </div>

    <div ref="footer" v-if="self.slots.footer" class="flex flex-col bg-inherit rounded-b-md">
      <slot name="footer" />
    </div>
  </div>
</template>