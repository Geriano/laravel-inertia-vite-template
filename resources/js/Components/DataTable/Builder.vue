<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
import axios from 'axios'
import Swal from 'sweetalert2'
import { getCurrentInstance, onMounted, onUpdated, ref } from 'vue'

const self = getCurrentInstance()
const { url, sticky } = defineProps({
  url: String,
  sticky: {
    type: Boolean,
    default: true,
  },
})

const paginator = ref({})
const processing = ref(true)
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
  paginator.value.current_page !== config.page && fetch()
}

const fetch = async u => {
  try {
    processing.value = true
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

  processing.value = false
}

const createFloatingTh = () => {
  const { thead, tfoot, tbody } = self.refs

  if (thead) {
    let ths = thead.querySelectorAll('th')
    ths.forEach((th, i) => {
      const floating = document.createElement('div')
      floating.className = 'absolute top-0 left-0 w-full h-full border border-1 border-inherit'
      i === 0 && floating.classList.add('rounded-tl-md')
      i + 1 === ths.length && floating.classList.add('rounded-tr-md')

      th.append(floating)
    })
  }

  if (tfoot) {
    let ths = tfoot.querySelectorAll('th')
    ths.forEach((th, i) => {
      const floating = document.createElement('div')
      floating.className = 'absolute bottom-0 left-0 w-full h-full border border-1 border-inherit'
      i === 0 && floating.classList.add('rounded-bl-md')
      i + 1 === ths.length && floating.classList.add('rounded-br-md')

      th.append(floating)
    })
  }
}

const inherit = () => {
  const { thead, tfoot } = self.refs

  if (thead && tfoot) {
    thead.querySelectorAll('th').forEach(th => th.classList.add('bg-inherit', 'border-inherit'))
    tfoot.querySelectorAll('th').forEach(th => th.classList.add('bg-inherit', 'border-inherit'))
  }
}

const rounded = () => {
  const { links } = self.refs

  links && links.firstElementChild?.classList.add('rounded-l-md')
  links && links.lastElementChild?.classList.add('rounded-r-md')
}

onMounted(fetch)
onMounted(() => config.sticky && createFloatingTh())
onMounted(() => inherit())
onMounted(() => rounded())
onUpdated(() => rounded())
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
            <select name="per_page" v-model="config.per_page" @change.prevent="fetch()" class="w-full sm:w-auto bg-transparent border dark:border-gray-600 rounded-md px-3 py-1">
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
        <input v-model="config.search" @input.prevent="fetch()" type="search" name="search" class="w-full bg-transparent border dark border-gray-600 rounded-md px-3 py-1 placeholder:capitalize" placeholder="search" autofocus>
      </div>
    </div>

    <div class="p-2">
      <div class="overflow-auto rounded-md border dark:border-gray-900 max-h-96">
        <table class="w-full border-collapse">
          <thead ref="thead" class="border-inherit z-10" :class="config.sticky && 'sticky top-0 left-0'">
            <slot name="thead" :config="config" :paginator="paginator" :data="paginator.data" :refresh="fetch" />
          </thead>

          <tfoot ref="tfoot" class="border-inherit z-10" :class="config.sticky && 'sticky bottom-0 left-0'">
            <slot name="tfoot" :config="config" :paginator="paginator" :data="paginator.data" :refresh="fetch" />
          </tfoot>

          <tbody ref="tbody" class="border-inherit">
            <slot name="tbody" :config="config" :paginator="paginator" :data="paginator.data" :refresh="fetch" :processing="processing" :empty="!processing && !paginator?.data?.length" />
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

        <div ref="links" class="w-full flex items-center justify-end overflow-auto">
          <button v-for="(link, i) in paginator.links" :key="i" @click.prevent="goTo(link)" class="flex-none hover:bg-gray-100 dark:hover:bg-gray-600 px-2 py-1 transition-all" :class="link.active ? 'bg-gray-300 dark:bg-gray-900' : 'bg-gray-200 dark:bg-gray-800'" v-html="link.label"></button>
        </div>
      </div>
    </Transition>
  </div>
</template>