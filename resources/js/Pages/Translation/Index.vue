<script setup>
import { ref } from 'vue'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import Card from '@/Components/Card.vue'
import Icon from '@/Components/Icon.vue'
import Input from '@/Components/Input.vue'
import { useForm } from '@inertiajs/inertia-vue3'
import { Inertia } from '@inertiajs/inertia'

const { translations } = defineProps({
  translations: Object,
})

const search = ref('')

const update = (key, value) => {
  return useForm({key, value}).patch(route('superuser.translation.update'), {
    // onFinish: () => Inertia.get(route(route().current()))
  })
}

const sorted = () => Object.keys(translations).sort().map(key => ({
  key: key,
  value: translations[key],
}))
</script>

<template>
  <DashboardLayout
    :title="__('translation')"
  >
    <Card class="bg-white dark:bg-gray-700 dark:text-gray-200">
      <template #header>
        <div class="flex items-center space-x-1 justify-between bg-gray-200 dark:bg-gray-800 p-2">
          <p class="lowercase first-letter:capitalize">
            {{ __('you can change translation on this page') }}
          </p>

          <Input
            v-model="search"
            :placeholder="__('search')"
            type="search"
            class="max-w-xs text-sm"
          />
        </div>
      </template>

      <template #body>
        <div class="flex flex-col space-y-2 min-h-[30rem] p-4">
          <TransitionGroup
            enterActiveClass="transition-all duration-300"
            leaveActiveClass="transition-all duration-300"
            enterFromClass="opacity-0 -translate-y-full bg-white"
            leaveToClass="opacity-0 -translate-y-full bg-white"
          >
            <template
              v-for="(translation, i) in sorted().filter(t => t.key.toLocaleLowerCase().includes(search.trim().toLocaleLowerCase()))"
              :key="i"
            >
              <div class="flex flex-col space-y-1 px-2 rounded-md">
                <div class="flex items-center space-x-1">
                  <label :for="`${translation.key}`" class="flex-none w-1/3">
                    {{ translation.key }}
                  </label>

                  <Input
                    :value="translation.value"
                    @change.prevent="$event.target.value.trim() && update(translation.key, $event.target.value)"
                    type="text"
                    required
                  />
                </div>
              </div>
            </template>
          </TransitionGroup>
        </div>
      </template>
    </Card>
  </DashboardLayout>
</template>