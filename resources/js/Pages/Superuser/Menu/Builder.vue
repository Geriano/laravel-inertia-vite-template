<script>
import { defineComponent, getCurrentInstance, h } from "vue"
import Parent from './Parent.vue'
import Child from './Child.vue'

export default defineComponent({
  props: {
    menus: Array,
    edit: Function,
    destroy: Function,
    up: Function,
    down: Function,
    left: Function,
    right: Function,
  },

  data: () => ({
    menuOnDrag: null,
  }),

  setup(props, attrs) {
    return props => {
      const self = getCurrentInstance()
      const { menus, edit, destroy, up, down, left, right } = props
      const drag = menu => self.menuOnDrag = menu
      const drop = drop => {
        const drag = self.menuOnDrag

        if (!drag || !drop) {
          return
        }

        if (drag.parent_id !== drop.parent_id) {
          return
        }

        if (drag.position === drop.position) {
          return
        }

        console.log(drag, drop)
      }

      const generate = menu => {
        if (menu.childs?.length > 0) {
          return h(Parent, {
            ...attrs,
            menu,
            edit,
            destroy,
            drag,
            drop,
            up,
            down,
            left,
            right,
          }, menu.childs.map(child => generate(child)))
        }

        return h(Child, {
          ...attrs,
          menu,
          edit,
          destroy,
          drag,
          drop,
          up,
          down,
          left,
          right,
        })
      }

      return h('div', {
        ...attrs,
        class: `flex flex-col space-y-1 ${attrs.class}`,
      }, menus.map(menu => generate(menu)))
    }
  },
})
</script>