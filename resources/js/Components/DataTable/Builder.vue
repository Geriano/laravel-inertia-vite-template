<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import Swal from 'sweetalert2'
import { getCurrentInstance, nextTick, onMounted, onUnmounted, onUpdated, ref } from 'vue'
import { cloneDeep } from 'lodash'

const self = getCurrentInstance()
const { url, sticky } = defineProps({
  url: String,
  sticky: {
    type: Boolean,
    default: true,
  },
})

const paginator = ref({
  data: [],
})
const processing = ref(false)
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

const goTo = link => {
  if (!link.url) {
    return
  }
  
  config.page = Number(link.url.match(/page=([\d]+)/)[1])
  paginator.value.current_page !== config.page && refresh()
}

const refresh = async u => {
  try {
    processing.value = true
    const prev = last.value = u || url
    const { data } = await axios.post(prev || last.value, config.data())
    paginator.value = data
    self.proxy.$forceUpdate()
  } catch (e) {
    const { isConfirmed } = await Swal.fire({
      title: 'error',
      text: `${e}`,
      icon: 'error',
      showCancelButton: true,
      showCloseButton: true,
    })

    if (isConfirmed) {
      return await refresh(last.value)
    }
  } finally {
    processing.value = false
  }

}

onMounted(refresh)

const all = {
  url,
  config,
  refresh,
  paginator: paginator.value,
  data: paginator.value.data,
  processing: !processing.value && !paginator.value?.data?.length,
}

defineExpose(all)
</script>

<template>
  <div class="flex flex-col w-full">
    <div class="flex flex-col sm:flex-row items-center sm:justify-between space-y-2 sm:space-y-0 sm:space-x-2 p-2">
      <div class="w-full sm:max-w-xs flex items-center space-x-2">
        <TransitionGroup
          enterActiveClass="transition-all duration-300"
          leaveActiveClass="transition-all duration-300"
          enterFromClass="translate-y-[100%]"
          leaveToClass="translate-y-[100%]">
          <template v-if="paginator?.total > config.per_page">
            <label for="per_page" class="w-1/4 sm:w-auto lowercase first-letter:capitalize">per page</label>
            <select name="per_page" v-model="config.per_page" @change.prevent="refresh()" class="w-full sm:w-auto bg-transparent border dark:border-gray-600 rounded-md px-3 py-1">
              <option class="dark:bg-gray-700" value="10">10</option>
              <option class="dark:bg-gray-700" value="15">15</option>
              <option class="dark:bg-gray-700" value="25">25</option>
              <option class="dark:bg-gray-700" value="50">50</option>
              <option class="dark:bg-gray-700" value="100">100</option>
            </select>
          </template>
        </TransitionGroup>
      </div>

      <div class="w-full sm:max-w-sm flex items-center space-x-2 sm:justify-end">
        <label for="search" class="w-1/4 sm:w-auto lowercase first-letter:capitalize">search</label>
        <input v-model="config.search" @input.prevent="refresh()" type="search" name="search" class="w-full bg-transparent border dark border-gray-600 rounded-md px-3 py-1 placeholder:capitalize" placeholder="search" autofocus>
      </div>
    </div>

    <div class="p-2">
      <div class="overflow-auto rounded-md border dark:border-gray-900 max-h-96">
        <table class="w-full border-collapse">
          <thead ref="thead" class="border-inherit z-10" :class="config.sticky && 'sticky top-0 left-0'">
            <slot name="thead" :config="config" :paginator="paginator" :data="paginator.data" :refresh="refresh" />
          </thead>

          <tfoot ref="tfoot" class="border-inherit z-10" :class="config.sticky && 'sticky bottom-0 left-0'">
            <slot name="tfoot" :config="config" :paginator="paginator" :data="paginator.data" :refresh="refresh" />
          </tfoot>

          <tbody ref="tbody" class="border-inherit">
            <slot name="tbody" :config="config" :paginator="paginator" :data="paginator.data" :processing="processing" :empty="!processing && !paginator?.data?.length" />
          </tbody>
        </table>
      </div>
    </div>

    <Transition
      enterActiveClass="transition-all duration-300"
      leaveActiveClass="transition-all duration-300"
      enterFromClass="-translate-y-[100%]"
      leaveToClass="-translate-y-[100%]">
      <div v-if="paginator.total > paginator.per_page" class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4 p-2">
        <p class="flex-none sm:w-1/4">
          Displaying from {{ paginator.from }} to {{ paginator.to }} in total {{ paginator.total }}
        </p>

        <div ref="links" class="w-full flex items-center flex-wrap justify-end">
          <button
            v-for="(link, i) in paginator.links"
            :key="i"
            @click.prevent="goTo(link)"
            class="flex-none border border-gray-300 dark:border-gray-900 px-2 py-1 transition-all uppercase my-[1px] flex-grow text-sm"
            :class="{
              'bg-gray-300 dark:bg-gray-900': link.active,
              'bg-gray-200 dark:bg-gray-800': !link.active,
              'rounded-l-md': i === 0,
              'rounded-r-md': i + 1 === paginator.links.length,
            }"
            v-html="link.label === 'pagination.next' ? `&gt;` : (link.label === 'pagination.previous' ? '&lt;' : link.label)"
          />
        </div>
      </div>
    </Transition>
  </div>
</template>