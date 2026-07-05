<template>
  <AdminCrudPage :config="config" />
</template>

<script>
import AdminCrudPage from '../../../components/shared/AdminCrudPage.vue';
import { resourceConfigs } from '../config/resourceConfigs';

export default {
  name: 'StaticPagesListPage',
  components: { AdminCrudPage },
  computed: {
    config() {
      return resourceConfigs['static-pages'];
    },
  },
};
</script>
