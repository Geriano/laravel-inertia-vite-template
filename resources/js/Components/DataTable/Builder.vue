<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import Swal from 'sweetalert2'
import { getCurrentInstance, onMounted, ref } from 'vue'

const { url, sticky } = defineProps({
  url: String,
  sticky: {
    type: Boolean,
    default: true,
  },
})

const paginator = ref({})
const last = ref(null)
const config = useForm({
  page: 1,
  search: '',
  per_page: 10,
  order: {
    key: '',
    dir: 'asc',
  },
  sticky,
})

const fetch = async u => {
  try {
    const a = last.value = u || url
    const response = await axios.post(a || last.value, config.data())
    paginator.value = response.data
  } catch (e) {
    const respose = await Swal.fire({
      title: 'error',
      text: `${e}`,
      icon: 'error',
      showCancelButton: true,
      showCloseButton: true,
    })

    if (respose.isConfirmed) {
      return fetch(last.value)
    }
  }
}

onMounted(fetch)
</script>

<template>
  <div class="flex flex-col w-full">
    <div class="flex flex-col sm:flex-row items-center sm:justify-between space-y-2 sm:space-y-0 sm:space-x-2 p-2">
      <div class="w-full sm:max-w-xs flex items-center space-x-2">
        <label for="per_page" class="w-1/4 sm:w-auto lowercase first-letter:capitalize">per page</label>
        <select name="per_page" v-model="config.per_page" @change.prevent="fetch()" class="w-full sm:w-auto bg-transparent border dark:border-gray-600 rounded-md px-3 py-1">
          <option value="10">10</option>
          <option value="15">15</option>
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>

      <div class="w-full sm:max-w-sm flex items-center space-x-2 sm:justify-end">
        <label for="search" class="w-1/4 sm:w-auto lowercase first-letter:capitalize">search</label>
        <input v-model="config.search" @input.prevent="fetch()" type="search" name="search" class="w-full bg-transparent border dark border-gray-600 rounded-md px-3 py-1 placeholder:capitalize" placeholder="search" autofocus>
      </div>
    </div>

    <div class="p-2">
      <div class="overflow-auto rounded-md border dark:border-gray-900">
        <table class="w-full border-collapse">
          <thead class="border-inherit">
            <slot name="thead" :config="config" :paginator="paginator" :data="paginator.data" :refresh="fetch" />
          </thead>

          <tfoot class="border-inherit">
            <slot name="tfoot" :config="config" :paginator="paginator" :data="paginator.data" :refresh="fetch" />
          </tfoot>

          <tbody class="border-inherit">
            <slot name="tbody" :config="config" :paginator="paginator" :data="paginator.data" :refresh="fetch" />
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>