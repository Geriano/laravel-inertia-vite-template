import { ref } from "vue"

localStorage.setItem('themes', localStorage.getItem('themes') || JSON.stringify({
  topbar: 'bg-cyan-500 text-gray-700 hover:bg-cyan-600 hover:text-gray-800 transition-all ease-in-out duration-150',
  sidebar: 'bg-gray-700 text-gray-200 hover:bg-gray-800 hover:text-gray-100 transition-all ease-in-out duration-150',
}))

const themes = ref(JSON.parse(localStorage.getItem('themes')))

const set = (key, val) => {
  themes.value[key] = val
  localStorage.setItem('themes', JSON.stringify(themes.value))
}

const get = (key, def) => themes.value[key] || def

export default {
  ...themes.value, set, get
}