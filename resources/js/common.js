import { usePage } from "@inertiajs/inertia-vue3"

window.translations = {}
window.locale = localStorage.getItem('locale') || 'id'

export const getAllDefinedTranslation = () => axios.get(route('api.translation.get', window.locale))
                                                    .then(response => response.data)
                                                    .then(response => window.translations = response)

export const __ = (text, replacements = {}) => {
  if (typeof text === 'string' && text.trim() !== '') {
    if (window.translations.hasOwnProperty(text.trim().toLowerCase())) {
      text = window.translations[text.trim().toLocaleLowerCase()]
    } else {
      axios.post(route(
        'api.translation.register', window.locale
      ), { text }).then(getAllDefinedTranslation)
    }

    for (const key in replacements) {
      text = text.replace(new RegExp(`:${key}`, 'g'), replacements[key])
    }
  }

  return text
}

export const can = (abilities) => {
  const { $permissions } = usePage().props.value

  if (Array.isArray(abilities)) {
    for (const ability of abilities) {
      if (can(ability)) {
        return true
      }
    }
  }
  
  if (typeof abilities === 'string') {
    return $permissions.find(permission => permission.name === abilities) !== undefined
  }
  
  if (typeof abilities === 'number') {
    return $permissions.find(permission => permission.id === abilities) !== undefined
  }

  return false
}

export const hasRole = roles => {
  const { $roles } = usePage().props.value

  if ($roles.find(role => role.name === 'superuser')) {
    return true
  }

  if (Array.isArray(roles)) {
    for (const role of roles) {
      if (hasRole(role)) {
        return true
      }
    }
  }

  if (typeof roles === 'string') {
    return $roles.find(role => role.name === roles) !== undefined
  }

  if (typeof roles === 'number') {
    return $roles.find(role => role.id === roles) !== undefined
  }

  return false
}

export const authorization = () => {
  const { $token } = usePage().props.value

  if ($token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${$token}`
  }

  return $token
}